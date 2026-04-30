<?php
/**
 * Email Capture for Nest & Well
 *
 * Handles AJAX submissions from the sidebar / footer email signup forms and
 * forwards subscribers to MailerLite. If no MailerLite API key is configured,
 * subscribers are stored in WordPress options as a fallback so no signups are
 * lost while the integration is being set up.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * AJAX handler for email signup submissions.
 *
 * Action: nest_well_subscribe (logged-in + logged-out).
 */
function nest_well_handle_subscribe() {
    check_ajax_referer( 'nest_well_subscribe', 'nonce' );

    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    if ( ! is_email( $email ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Please enter a valid email address.', 'nest-and-well' ) ),
            400
        );
    }

    $source = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : 'sidebar';

    $api_key  = get_theme_mod( 'nest_well_mailerlite_api_key', '' );
    $group_id = get_theme_mod( 'nest_well_mailerlite_group_id', '' );

    // Always persist locally first so a failed/slow API call never loses a signup.
    nest_well_store_subscriber_fallback( $email, $source );

    if ( $api_key ) {
        $result = nest_well_mailerlite_subscribe( $email, $api_key, $group_id, $source );
        if ( is_wp_error( $result ) ) {
            error_log( sprintf(
                '[nest-well] MailerLite signup failed for %s: %s',
                $email,
                $result->get_error_message()
            ) );
            // Don't fail the user-facing request — we already stored locally.
            wp_send_json_success(
                array( 'message' => __( 'Thanks — you\'re on the list.', 'nest-and-well' ) )
            );
        }
        wp_send_json_success(
            array( 'message' => __( 'Thanks — check your inbox to confirm.', 'nest-and-well' ) )
        );
    }

    wp_send_json_success(
        array( 'message' => __( 'Thanks — you\'re on the list.', 'nest-and-well' ) )
    );
}
add_action( 'wp_ajax_nest_well_subscribe', 'nest_well_handle_subscribe' );
add_action( 'wp_ajax_nopriv_nest_well_subscribe', 'nest_well_handle_subscribe' );

/**
 * POST a subscriber to MailerLite.
 *
 * Auto-detects which API to call based on token format:
 *   - New MailerLite (post-2022 accounts) — JWT-style token containing dots →
 *     POST https://connect.mailerlite.com/api/subscribers with Bearer auth.
 *   - Classic MailerLite (legacy accounts) — 32-char hex key →
 *     POST https://api.mailerlite.com/api/v2/subscribers with X-MailerLite-ApiKey.
 *
 * @param string $email    Subscriber email.
 * @param string $api_key  MailerLite API token / key.
 * @param string $group_id Optional MailerLite group / list ID.
 * @param string $source   Source identifier (sidebar / footer / etc).
 * @return true|WP_Error True on success, WP_Error on failure.
 */
function nest_well_mailerlite_subscribe( $email, $api_key, $group_id = '', $source = '' ) {
    $is_new_api = ( strpos( $api_key, '.' ) !== false );

    if ( $is_new_api ) {
        $endpoint = 'https://connect.mailerlite.com/api/subscribers';
        $body     = array(
            'email'  => $email,
            'fields' => array(
                'signup_source' => $source,
            ),
        );
        if ( $group_id ) {
            $body['groups'] = array( $group_id );
        }
        $headers = array(
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        );
    } else {
        $endpoint = $group_id
            ? sprintf( 'https://api.mailerlite.com/api/v2/groups/%s/subscribers', rawurlencode( $group_id ) )
            : 'https://api.mailerlite.com/api/v2/subscribers';
        $body    = array(
            'email'  => $email,
            'fields' => array(
                'signup_source' => $source,
            ),
        );
        $headers = array(
            'Content-Type'        => 'application/json',
            'X-MailerLite-ApiKey' => $api_key,
        );
    }

    $response = wp_remote_post(
        $endpoint,
        array(
            'timeout' => 8,
            'headers' => $headers,
            'body'    => wp_json_encode( $body ),
        )
    );

    $api_label = $is_new_api ? 'new' : 'classic';

    if ( is_wp_error( $response ) ) {
        nest_well_record_mailerlite_attempt( false, array(
            'api'      => $api_label,
            'endpoint' => $endpoint,
            'email'    => $email,
            'error'    => $response->get_error_message(),
        ) );
        return $response;
    }

    $code      = wp_remote_retrieve_response_code( $response );
    $body_text = wp_remote_retrieve_body( $response );

    if ( $code < 200 || $code >= 300 ) {
        nest_well_record_mailerlite_attempt( false, array(
            'api'      => $api_label,
            'endpoint' => $endpoint,
            'email'    => $email,
            'status'   => $code,
            'body'     => substr( $body_text, 0, 800 ),
        ) );
        return new WP_Error(
            'mailerlite_error',
            sprintf( 'MailerLite %s API returned HTTP %d: %s', $api_label, $code, substr( $body_text, 0, 300 ) )
        );
    }

    nest_well_record_mailerlite_attempt( true, array(
        'api'      => $api_label,
        'endpoint' => $endpoint,
        'email'    => $email,
        'status'   => $code,
    ) );

    return true;
}

/**
 * Record the most recent MailerLite API attempt for the diagnostics page.
 *
 * @param bool  $success Whether the call succeeded.
 * @param array $data    Details (status, body, error, etc).
 */
function nest_well_record_mailerlite_attempt( $success, $data ) {
    update_option( 'nest_well_mailerlite_last', array(
        'success' => (bool) $success,
        'time'    => current_time( 'mysql' ),
        'data'    => $data,
    ), false );
}

/**
 * Persist a subscriber to a WP option as a fallback.
 * Capped at 5,000 entries to avoid unbounded option growth.
 *
 * @param string $email  Subscriber email.
 * @param string $source Source identifier.
 */
function nest_well_store_subscriber_fallback( $email, $source ) {
    $list = get_option( 'nest_well_pending_subscribers', array() );
    if ( ! is_array( $list ) ) {
        $list = array();
    }
    $list[] = array(
        'email'  => $email,
        'source' => $source,
        'time'   => current_time( 'mysql' ),
    );
    if ( count( $list ) > 5000 ) {
        $list = array_slice( $list, -5000 );
    }
    update_option( 'nest_well_pending_subscribers', $list, false );
}

/**
 * Localise email-capture data for the front-end JS handler.
 */
function nest_well_localize_email_capture() {
    wp_localize_script(
        'nest-well-main',
        'nestWellSubscribe',
        array(
            'ajaxUrl' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
            'nonce'   => wp_create_nonce( 'nest_well_subscribe' ),
            'action'  => 'nest_well_subscribe',
        )
    );
}
add_action( 'wp_enqueue_scripts', 'nest_well_localize_email_capture', 30 );

/* =============================================================
 * Admin diagnostics page (Tools → Email Capture)
 * Lets the site owner see:
 *   - Whether an API key is configured + which API it'll target
 *   - The most recent MailerLite call (success or failure, status, body)
 *   - Pending subscribers in the local fallback option
 *   - A "Send test subscriber" button that exercises the live endpoint
 * ============================================================= */
function nest_well_register_diagnostics_page() {
    add_management_page(
        __( 'Email Capture', 'nest-and-well' ),
        __( 'Email Capture', 'nest-and-well' ),
        'manage_options',
        'nest-well-email-capture',
        'nest_well_render_diagnostics_page'
    );
}
add_action( 'admin_menu', 'nest_well_register_diagnostics_page' );

function nest_well_render_diagnostics_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Handle "Send test subscriber" form submission.
    $test_result = null;
    if ( isset( $_POST['nest_well_test'] ) && check_admin_referer( 'nest_well_test_subscriber' ) ) {
        $test_email = isset( $_POST['test_email'] )
            ? sanitize_email( wp_unslash( $_POST['test_email'] ) )
            : '';
        if ( ! is_email( $test_email ) ) {
            $test_result = array( 'ok' => false, 'msg' => 'Invalid email.' );
        } else {
            $api_key  = get_theme_mod( 'nest_well_mailerlite_api_key', '' );
            $group_id = get_theme_mod( 'nest_well_mailerlite_group_id', '' );
            if ( ! $api_key ) {
                $test_result = array( 'ok' => false, 'msg' => 'No MailerLite API key configured in Customizer.' );
            } else {
                $r = nest_well_mailerlite_subscribe( $test_email, $api_key, $group_id, 'admin-test' );
                if ( is_wp_error( $r ) ) {
                    $test_result = array( 'ok' => false, 'msg' => $r->get_error_message() );
                } else {
                    $test_result = array( 'ok' => true, 'msg' => 'MailerLite accepted the subscriber. Check your list now.' );
                }
            }
        }
    }

    // Handle "Clear pending subscribers" form submission.
    if ( isset( $_POST['nest_well_clear_pending'] ) && check_admin_referer( 'nest_well_clear_pending' ) ) {
        delete_option( 'nest_well_pending_subscribers' );
        echo '<div class="notice notice-success"><p>Pending subscribers cleared.</p></div>';
    }

    $api_key   = get_theme_mod( 'nest_well_mailerlite_api_key', '' );
    $group_id  = get_theme_mod( 'nest_well_mailerlite_group_id', '' );
    $api_label = $api_key
        ? ( strpos( $api_key, '.' ) !== false ? 'New MailerLite (connect.mailerlite.com)' : 'Classic MailerLite (api.mailerlite.com/v2)' )
        : 'Not configured';
    $last      = get_option( 'nest_well_mailerlite_last', null );
    $pending   = get_option( 'nest_well_pending_subscribers', array() );
    if ( ! is_array( $pending ) ) {
        $pending = array();
    }
    ?>
    <div class="wrap">
        <h1>Email Capture Diagnostics</h1>

        <?php if ( $test_result ) : ?>
            <div class="notice notice-<?php echo $test_result['ok'] ? 'success' : 'error'; ?>">
                <p><strong><?php echo $test_result['ok'] ? 'Success' : 'Failed'; ?>:</strong> <?php echo esc_html( $test_result['msg'] ); ?></p>
            </div>
        <?php endif; ?>

        <h2>Configuration</h2>
        <table class="widefat striped" style="max-width:720px">
            <tbody>
                <tr><th style="width:200px">API token</th><td><?php echo $api_key ? '<code>' . esc_html( substr( $api_key, 0, 6 ) . '…' . substr( $api_key, -4 ) ) . '</code> (' . esc_html( strlen( $api_key ) ) . ' chars)' : '<em>Not set</em>'; ?></td></tr>
                <tr><th>Detected API</th><td><?php echo esc_html( $api_label ); ?></td></tr>
                <tr><th>Group / List ID</th><td><?php echo $group_id ? '<code>' . esc_html( $group_id ) . '</code>' : '<em>Default list</em>'; ?></td></tr>
            </tbody>
        </table>
        <p><a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=nest_well_email' ) ); ?>" class="button">Edit in Customizer</a></p>

        <h2>Last MailerLite call</h2>
        <?php if ( $last && is_array( $last ) ) : ?>
            <table class="widefat striped" style="max-width:720px">
                <tbody>
                    <tr><th style="width:200px">Result</th><td><?php echo $last['success'] ? '<span style="color:#1a8917">Success</span>' : '<span style="color:#c0392b">Failed</span>'; ?></td></tr>
                    <tr><th>When</th><td><?php echo esc_html( $last['time'] ); ?></td></tr>
                    <?php if ( ! empty( $last['data']['endpoint'] ) ) : ?>
                        <tr><th>Endpoint</th><td><code><?php echo esc_html( $last['data']['endpoint'] ); ?></code></td></tr>
                    <?php endif; ?>
                    <?php if ( ! empty( $last['data']['email'] ) ) : ?>
                        <tr><th>Email</th><td><code><?php echo esc_html( $last['data']['email'] ); ?></code></td></tr>
                    <?php endif; ?>
                    <?php if ( isset( $last['data']['status'] ) ) : ?>
                        <tr><th>HTTP status</th><td><code><?php echo esc_html( $last['data']['status'] ); ?></code></td></tr>
                    <?php endif; ?>
                    <?php if ( ! empty( $last['data']['error'] ) ) : ?>
                        <tr><th>Network error</th><td><code><?php echo esc_html( $last['data']['error'] ); ?></code></td></tr>
                    <?php endif; ?>
                    <?php if ( ! empty( $last['data']['body'] ) ) : ?>
                        <tr><th>Response body</th><td><pre style="white-space:pre-wrap;margin:0;background:#f6f7f7;padding:8px;border:1px solid #dcdcde"><?php echo esc_html( $last['data']['body'] ); ?></pre></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><em>No MailerLite calls have been made yet from this site.</em></p>
        <?php endif; ?>

        <h2>Send a test subscriber</h2>
        <p>Posts a real subscriber to MailerLite using your saved token. The result above will update.</p>
        <form method="post" style="margin-bottom:24px">
            <?php wp_nonce_field( 'nest_well_test_subscriber' ); ?>
            <input type="email" name="test_email" placeholder="you@example.com" required class="regular-text">
            <button type="submit" name="nest_well_test" value="1" class="button button-primary">Send test</button>
        </form>

        <h2>Pending subscribers (local fallback)</h2>
        <p>Every signup is saved here first, even when MailerLite succeeds, so nothing is lost. <strong><?php echo count( $pending ); ?></strong> entries.</p>
        <?php if ( $pending ) : ?>
            <table class="widefat striped" style="max-width:720px">
                <thead><tr><th>Email</th><th>Source</th><th>Time</th></tr></thead>
                <tbody>
                <?php foreach ( array_reverse( array_slice( $pending, -50 ) ) as $row ) : ?>
                    <tr>
                        <td><code><?php echo esc_html( $row['email'] ); ?></code></td>
                        <td><?php echo esc_html( $row['source'] ); ?></td>
                        <td><?php echo esc_html( $row['time'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ( count( $pending ) > 50 ) : ?>
                <p><em>Showing the most recent 50 of <?php echo count( $pending ); ?>.</em></p>
            <?php endif; ?>
            <form method="post" style="margin-top:12px" onsubmit="return confirm('Delete all locally-stored signups? This does not affect MailerLite.');">
                <?php wp_nonce_field( 'nest_well_clear_pending' ); ?>
                <button type="submit" name="nest_well_clear_pending" value="1" class="button">Clear pending list</button>
            </form>
        <?php endif; ?>
    </div>
    <?php
}
