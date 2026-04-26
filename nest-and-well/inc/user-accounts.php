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
