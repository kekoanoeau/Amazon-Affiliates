<?php
/**
 * User Accounts — Registration, Login, Save Articles
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// ============================================================
// ENQUEUE ACCOUNT ASSETS
// ============================================================

function nest_well_enqueue_account_assets() {
    wp_enqueue_script(
        'nest-well-accounts',
        NEST_WELL_URI . '/assets/js/accounts.js',
        array(),
        NEST_WELL_VERSION,
        true
    );

    wp_localize_script(
        'nest-well-accounts',
        'nestWellAccounts',
        array(
            'ajaxUrl'   => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
            'nonce'     => wp_create_nonce( 'nest_well_accounts' ),
            'loggedIn'  => is_user_logged_in(),
            'loginUrl'  => esc_url( home_url( '/account/' ) ),
            'savedIds'  => is_user_logged_in() ? nest_well_get_saved_post_ids() : array(),
            'i18n'      => array(
                'save'      => esc_html__( 'Save article', 'nest-and-well' ),
                'saved'     => esc_html__( 'Saved', 'nest-and-well' ),
                'unsave'    => esc_html__( 'Remove from saved', 'nest-and-well' ),
                'loginPrompt' => esc_html__( 'Log in to save articles', 'nest-and-well' ),
            ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'nest_well_enqueue_account_assets' );

// ============================================================
// SAVE / UNSAVE ARTICLES
// ============================================================

/**
 * Get saved post IDs for current user.
 *
 * @param int|null $user_id User ID or null for current user.
 * @return int[] Array of saved post IDs.
 */
function nest_well_get_saved_post_ids( $user_id = null ) {
    $user_id = $user_id ?: get_current_user_id();
    if ( ! $user_id ) {
        return array();
    }
    $saved = get_user_meta( $user_id, '_nest_well_saved_posts', true );
    return is_array( $saved ) ? array_map( 'absint', $saved ) : array();
}

/**
 * Check whether a post is saved by the current user.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function nest_well_is_post_saved( $post_id ) {
    return in_array( absint( $post_id ), nest_well_get_saved_post_ids(), true );
}

/**
 * AJAX: toggle save/unsave for a post.
 * Requires user to be logged in.
 */
function nest_well_ajax_toggle_save() {
    check_ajax_referer( 'nest_well_accounts', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Please log in to save articles.', 'nest-and-well' ) ), 401 );
    }

    $post_id = absint( $_POST['post_id'] ?? 0 );
    if ( ! $post_id || ! get_post( $post_id ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Invalid article.', 'nest-and-well' ) ), 400 );
    }

    $user_id = get_current_user_id();
    $saved   = nest_well_get_saved_post_ids( $user_id );

    if ( in_array( $post_id, $saved, true ) ) {
        $saved  = array_values( array_diff( $saved, array( $post_id ) ) );
        $is_saved = false;
    } else {
        $saved[]  = $post_id;
        $is_saved = true;
    }

    update_user_meta( $user_id, '_nest_well_saved_posts', $saved );

    wp_send_json_success( array(
        'saved'      => $is_saved,
        'post_id'    => $post_id,
        'savedCount' => count( $saved ),
    ) );
}
add_action( 'wp_ajax_nest_well_toggle_save', 'nest_well_ajax_toggle_save' );

// Non-logged-in users get a login prompt (handled client-side via loginUrl).
add_action( 'wp_ajax_nopriv_nest_well_toggle_save', function () {
    check_ajax_referer( 'nest_well_accounts', 'nonce' );
    wp_send_json_error( array(
        'message'  => esc_html__( 'Please log in to save articles.', 'nest-and-well' ),
        'loginUrl' => esc_url( home_url( '/account/' ) ),
    ), 401 );
} );

// ============================================================
// CUSTOM REGISTRATION HANDLER
// ============================================================

/**
 * Handle registration form submission.
 * Called from page-account.php.
 *
 * @return WP_Error|true WP_Error on failure, true on success.
 */
function nest_well_handle_registration() {
    if (
        ! isset( $_POST['nest_well_register_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nest_well_register_nonce'] ) ), 'nest_well_register' )
    ) {
        return new WP_Error( 'invalid_nonce', esc_html__( 'Security check failed.', 'nest-and-well' ) );
    }

    if ( ! get_option( 'users_can_register' ) ) {
        return new WP_Error( 'registration_disabled', esc_html__( 'User registration is currently disabled.', 'nest-and-well' ) );
    }

    $username  = sanitize_user( wp_unslash( $_POST['reg_username'] ?? '' ) );
    $email     = sanitize_email( wp_unslash( $_POST['reg_email'] ?? '' ) );
    $password  = wp_unslash( $_POST['reg_password'] ?? '' );
    $password2 = wp_unslash( $_POST['reg_password2'] ?? '' );

    if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
        return new WP_Error( 'empty_fields', esc_html__( 'Please fill in all required fields.', 'nest-and-well' ) );
    }

    if ( ! is_email( $email ) ) {
        return new WP_Error( 'invalid_email', esc_html__( 'Please enter a valid email address.', 'nest-and-well' ) );
    }

    if ( $password !== $password2 ) {
        return new WP_Error( 'password_mismatch', esc_html__( 'Passwords do not match.', 'nest-and-well' ) );
    }

    if ( strlen( $password ) < 8 ) {
        return new WP_Error( 'password_short', esc_html__( 'Password must be at least 8 characters.', 'nest-and-well' ) );
    }

    if ( username_exists( $username ) ) {
        return new WP_Error( 'username_exists', esc_html__( 'That username is already taken.', 'nest-and-well' ) );
    }

    if ( email_exists( $email ) ) {
        return new WP_Error( 'email_exists', esc_html__( 'An account with that email already exists.', 'nest-and-well' ) );
    }

    $user_id = wp_create_user( $username, $password, $email );

    if ( is_wp_error( $user_id ) ) {
        return $user_id;
    }

    // Auto-login after registration.
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id );

    return true;
}

// ============================================================
// CUSTOM LOGIN HANDLER
// ============================================================

/**
 * Handle login form submission.
 * Called from page-account.php.
 *
 * @return WP_Error|true WP_Error on failure, true on success.
 */
function nest_well_handle_login() {
    if (
        ! isset( $_POST['nest_well_login_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nest_well_login_nonce'] ) ), 'nest_well_login' )
    ) {
        return new WP_Error( 'invalid_nonce', esc_html__( 'Security check failed.', 'nest-and-well' ) );
    }

    $credentials = array(
        'user_login'    => sanitize_text_field( wp_unslash( $_POST['log'] ?? '' ) ),
        'user_password' => wp_unslash( $_POST['pwd'] ?? '' ),
        'remember'      => ! empty( $_POST['rememberme'] ),
    );

    $user = wp_signon( $credentials, is_ssl() );

    if ( is_wp_error( $user ) ) {
        return new WP_Error( 'login_failed', esc_html__( 'Incorrect username or password.', 'nest-and-well' ) );
    }

    return true;
}

// ============================================================
// LOGOUT REDIRECT
// ============================================================

/**
 * Redirect to homepage (or account page) after logout.
 */
function nest_well_logout_redirect() {
    return home_url( '/account/?logged_out=1' );
}
add_filter( 'logout_redirect', 'nest_well_logout_redirect' );

// ============================================================
// PASSWORD RESET — REQUEST EMAIL
// ============================================================

/**
 * Handle "forgot password" form submission.
 * Sends a reset email via WP's built-in retrieve_password() flow,
 * which respects any SMTP plugin already wired into wp_mail().
 *
 * Privacy: returns true even when the email is unknown so the
 * response cannot be used to enumerate accounts. Real errors are
 * written to the PHP error log for ops debugging.
 *
 * @return WP_Error|true
 */
function nest_well_handle_password_reset_request() {
    if (
        ! isset( $_POST['nest_well_reset_request_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nest_well_reset_request_nonce'] ) ), 'nest_well_reset_request' )
    ) {
        return new WP_Error( 'invalid_nonce', esc_html__( 'Security check failed.', 'nest-and-well' ) );
    }

    $login = sanitize_text_field( wp_unslash( $_POST['user_login'] ?? '' ) );
    if ( empty( $login ) ) {
        return new WP_Error( 'empty_login', esc_html__( 'Please enter your username or email.', 'nest-and-well' ) );
    }

    $user = is_email( $login ) ? get_user_by( 'email', $login ) : get_user_by( 'login', $login );

    if ( $user instanceof WP_User ) {
        $result = retrieve_password( $user->user_login );
        if ( is_wp_error( $result ) ) {
            error_log( 'nest_well_handle_password_reset_request: ' . $result->get_error_message() );
        }
    }

    // Always succeed from the user's POV — never leak account existence.
    return true;
}

// ============================================================
// PASSWORD RESET — APPLY NEW PASSWORD
// ============================================================

/**
 * Handle the new-password form on /account/?action=reset&key=...&login=...
 * Validates the WP reset key and writes the new password.
 *
 * @return WP_Error|true
 */
function nest_well_handle_password_reset() {
    if (
        ! isset( $_POST['nest_well_reset_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nest_well_reset_nonce'] ) ), 'nest_well_reset' )
    ) {
        return new WP_Error( 'invalid_nonce', esc_html__( 'Security check failed.', 'nest-and-well' ) );
    }

    $key   = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );
    $login = sanitize_text_field( wp_unslash( $_POST['login'] ?? '' ) );
    $pass  = wp_unslash( $_POST['new_password'] ?? '' );
    $pass2 = wp_unslash( $_POST['new_password_confirm'] ?? '' );

    if ( empty( $key ) || empty( $login ) ) {
        return new WP_Error( 'invalid_link', esc_html__( 'This password reset link is invalid or has expired. Please request a new one.', 'nest-and-well' ) );
    }

    if ( empty( $pass ) ) {
        return new WP_Error( 'empty_password', esc_html__( 'Please enter a new password.', 'nest-and-well' ) );
    }

    if ( $pass !== $pass2 ) {
        return new WP_Error( 'password_mismatch', esc_html__( 'Passwords do not match.', 'nest-and-well' ) );
    }

    if ( strlen( $pass ) < 8 ) {
        return new WP_Error( 'password_short', esc_html__( 'Password must be at least 8 characters.', 'nest-and-well' ) );
    }

    $user = check_password_reset_key( $key, $login );
    if ( is_wp_error( $user ) ) {
        return new WP_Error( 'invalid_link', esc_html__( 'This password reset link is invalid or has expired. Please request a new one.', 'nest-and-well' ) );
    }

    reset_password( $user, $pass );

    return true;
}

// ============================================================
// PROFILE UPDATE — DISPLAY NAME / EMAIL / PASSWORD
// ============================================================

/**
 * Handle the logged-in user's profile-edit form.
 * Splits into two sub-forms via a `change_password` flag so a
 * single nonce/handler can serve both identity edits and password
 * change. Either sub-form requires the user's current password.
 *
 * @return WP_Error|true
 */
function nest_well_handle_profile_update() {
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'not_logged_in', esc_html__( 'You must be logged in.', 'nest-and-well' ) );
    }

    if (
        ! isset( $_POST['nest_well_profile_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nest_well_profile_nonce'] ) ), 'nest_well_profile' )
    ) {
        return new WP_Error( 'invalid_nonce', esc_html__( 'Security check failed.', 'nest-and-well' ) );
    }

    $current_user    = wp_get_current_user();
    $is_password_form = ! empty( $_POST['change_password'] );

    $current_password = wp_unslash( $_POST['current_password'] ?? '' );
    if ( empty( $current_password ) || ! wp_check_password( $current_password, $current_user->user_pass, $current_user->ID ) ) {
        return new WP_Error( 'wrong_password', esc_html__( 'Current password is incorrect.', 'nest-and-well' ) );
    }

    if ( $is_password_form ) {
        $new_pass  = wp_unslash( $_POST['new_password'] ?? '' );
        $new_pass2 = wp_unslash( $_POST['new_password_confirm'] ?? '' );

        if ( empty( $new_pass ) ) {
            return new WP_Error( 'empty_password', esc_html__( 'Please enter a new password.', 'nest-and-well' ) );
        }
        if ( $new_pass !== $new_pass2 ) {
            return new WP_Error( 'password_mismatch', esc_html__( 'New passwords do not match.', 'nest-and-well' ) );
        }
        if ( strlen( $new_pass ) < 8 ) {
            return new WP_Error( 'password_short', esc_html__( 'Password must be at least 8 characters.', 'nest-and-well' ) );
        }

        wp_set_password( $new_pass, $current_user->ID );
        // wp_set_password destroys the session — restore it.
        wp_set_auth_cookie( $current_user->ID, true );
        return 'password_updated';
    }

    // Identity form
    $display_name = sanitize_text_field( wp_unslash( $_POST['display_name'] ?? '' ) );
    $new_email    = sanitize_email( wp_unslash( $_POST['user_email'] ?? '' ) );

    if ( empty( $display_name ) ) {
        return new WP_Error( 'empty_name', esc_html__( 'Display name cannot be empty.', 'nest-and-well' ) );
    }
    if ( empty( $new_email ) || ! is_email( $new_email ) ) {
        return new WP_Error( 'invalid_email', esc_html__( 'Please enter a valid email address.', 'nest-and-well' ) );
    }

    if ( $new_email !== $current_user->user_email ) {
        $existing = email_exists( $new_email );
        if ( $existing && (int) $existing !== (int) $current_user->ID ) {
            return new WP_Error( 'email_taken', esc_html__( 'That email is already in use by another account.', 'nest-and-well' ) );
        }
    }

    $update = wp_update_user( array(
        'ID'           => $current_user->ID,
        'display_name' => $display_name,
        'user_email'   => $new_email,
    ) );

    if ( is_wp_error( $update ) ) {
        return $update;
    }

    return 'profile_updated';
}

// ============================================================
// REDIRECT LOGGED-IN USERS AWAY FROM WP-LOGIN
// ============================================================

/**
 * Redirect wp-login.php visitors to the custom account page.
 */
function nest_well_redirect_login_page() {
    $page_viewed = basename( $_SERVER['REQUEST_URI'] ?? '' );
    if ( 'wp-login.php' === $page_viewed && ! is_admin() && 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
        wp_safe_redirect( home_url( '/account/' ) );
        exit;
    }
}
add_action( 'init', 'nest_well_redirect_login_page' );

// ============================================================
// SAVED ARTICLES COUNT FOR ACCOUNT MENU
// ============================================================

/**
 * Output a compact saved-articles count badge for the header.
 */
function nest_well_saved_count_badge() {
    if ( ! is_user_logged_in() ) {
        return;
    }
    $count = count( nest_well_get_saved_post_ids() );
    if ( $count > 0 ) {
        echo '<span class="account-saved-count" aria-label="' . esc_attr( sprintf( _n( '%d saved article', '%d saved articles', $count, 'nest-and-well' ), $count ) ) . '">' . esc_html( $count ) . '</span>';
    }
}

// ============================================================
// SAVE BUTTON RENDERER
// ============================================================

/**
 * Render a save / unsave toggle button.
 * Wired by assets/js/accounts.js — buttons share state across the page via
 * data-post-id, so duplicates (card + single) stay in sync after toggling.
 *
 * @param int    $post_id Post ID to save.
 * @param string $variant Visual variant: 'icon' (default) or 'inline' (with label).
 */
function nest_well_save_button( $post_id = 0, $variant = 'icon' ) {
    $post_id = $post_id ? (int) $post_id : (int) get_the_ID();
    if ( ! $post_id ) {
        return;
    }

    $is_saved   = is_user_logged_in() && nest_well_is_post_saved( $post_id );
    $logged_in  = is_user_logged_in();
    $aria_label = $is_saved
        ? __( 'Remove from saved', 'nest-and-well' )
        : ( $logged_in ? __( 'Save article', 'nest-and-well' ) : __( 'Log in to save', 'nest-and-well' ) );

    $classes = array( 'save-article-btn', 'save-btn--' . sanitize_html_class( $variant ) );
    if ( $is_saved ) {
        $classes[] = 'is-saved';
    }
    ?>
    <button type="button"
            class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
            data-post-id="<?php echo esc_attr( $post_id ); ?>"
            aria-label="<?php echo esc_attr( $aria_label ); ?>"
            aria-pressed="<?php echo $is_saved ? 'true' : 'false'; ?>">
        <svg class="save-btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
        </svg>
        <?php if ( 'inline' === $variant ) : ?>
        <span class="save-btn__label">
            <?php echo esc_html( $is_saved ? __( 'Saved', 'nest-and-well' ) : __( 'Save', 'nest-and-well' ) ); ?>
        </span>
        <?php endif; ?>
    </button>
    <?php
}
