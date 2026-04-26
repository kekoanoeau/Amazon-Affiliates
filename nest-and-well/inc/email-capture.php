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

    if ( $api_key ) {
        $result = nest_well_mailerlite_subscribe( $email, $api_key, $group_id, $source );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error(
                array( 'message' => $result->get_error_message() ),
                502
            );
        }
        wp_send_json_success(
            array( 'message' => __( 'Thanks — check your inbox to confirm.', 'nest-and-well' ) )
        );
    }

    // Fallback: store in options so signups aren't lost before MailerLite is wired.
    nest_well_store_subscriber_fallback( $email, $source );
    wp_send_json_success(
        array( 'message' => __( 'Thanks — you\'re on the list.', 'nest-and-well' ) )
    );
}
add_action( 'wp_ajax_nest_well_subscribe', 'nest_well_handle_subscribe' );
add_action( 'wp_ajax_nopriv_nest_well_subscribe', 'nest_well_handle_subscribe' );

/**
 * POST a subscriber to MailerLite Classic API.
 *
 * @param string $email    Subscriber email.
 * @param string $api_key  MailerLite API key.
 * @param string $group_id Optional MailerLite group/list ID.
 * @param string $source   Source identifier (sidebar / footer / etc).
 * @return true|WP_Error True on success, WP_Error on failure.
 */
function nest_well_mailerlite_subscribe( $email, $api_key, $group_id = '', $source = '' ) {
    $endpoint = $group_id
        ? sprintf( 'https://api.mailerlite.com/api/v2/groups/%s/subscribers', rawurlencode( $group_id ) )
        : 'https://api.mailerlite.com/api/v2/subscribers';

    $body = array(
        'email'  => $email,
        'fields' => array(
            'signup_source' => $source,
        ),
    );

    $response = wp_remote_post(
        $endpoint,
        array(
            'timeout' => 8,
            'headers' => array(
                'Content-Type'      => 'application/json',
                'X-MailerLite-ApiKey' => $api_key,
            ),
            'body'    => wp_json_encode( $body ),
        )
    );

    if ( is_wp_error( $response ) ) {
        return $response;
    }

    $code = wp_remote_retrieve_response_code( $response );
    if ( $code < 200 || $code >= 300 ) {
        return new WP_Error(
            'mailerlite_error',
            __( 'We couldn\'t reach the mailing list right now. Please try again in a minute.', 'nest-and-well' )
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
