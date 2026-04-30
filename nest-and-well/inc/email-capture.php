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

    if ( is_wp_error( $response ) ) {
        return $response;
    }

    $code = wp_remote_retrieve_response_code( $response );
    if ( $code < 200 || $code >= 300 ) {
        $api_label = $is_new_api ? 'new' : 'classic';
        $body_text = wp_remote_retrieve_body( $response );
        return new WP_Error(
            'mailerlite_error',
            sprintf( 'MailerLite %s API returned HTTP %d: %s', $api_label, $code, substr( $body_text, 0, 300 ) )
        );
    }

    return true;
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
