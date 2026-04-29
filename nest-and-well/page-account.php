<?php
/**
 * Template Name: Account Page
 *
 * Handles login, registration, and logged-in profile/saved-articles overview.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// ----------------------------------------------------------------
// Handle form submissions BEFORE any output.
// ----------------------------------------------------------------
$action_result = null;

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
    if ( isset( $_POST['nest_well_register_nonce'] ) ) {
        $action_result = nest_well_handle_registration();
        if ( true === $action_result ) {
            wp_safe_redirect( home_url( '/account/?registered=1' ) );
            exit;
        }
    } elseif ( isset( $_POST['nest_well_login_nonce'] ) ) {
        $action_result = nest_well_handle_login();
        if ( true === $action_result ) {
            $redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/account/' );
            wp_safe_redirect( $redirect );
            exit;
        }
    } elseif ( isset( $_POST['nest_well_reset_request_nonce'] ) ) {
        $action_result = nest_well_handle_password_reset_request();
        if ( true === $action_result ) {
            wp_safe_redirect( home_url( '/account/?reset_sent=1' ) );
            exit;
        }
    } elseif ( isset( $_POST['nest_well_reset_nonce'] ) ) {
        $action_result = nest_well_handle_password_reset();
        if ( true === $action_result ) {
            wp_safe_redirect( home_url( '/account/?reset=1' ) );
            exit;
        }
    } elseif ( isset( $_POST['nest_well_profile_nonce'] ) ) {
        $action_result = nest_well_handle_profile_update();
        if ( 'profile_updated' === $action_result ) {
            wp_safe_redirect( home_url( '/account/?profile_updated=1' ) );
            exit;
        }
        if ( 'password_updated' === $action_result ) {
            wp_safe_redirect( home_url( '/account/?password_updated=1' ) );
            exit;
        }
    }
}

get_header();

$tab           = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'login';
$action        = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : '';
$reset_key     = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
$reset_login   = isset( $_GET['login'] ) ? sanitize_text_field( wp_unslash( $_GET['login'] ) ) : '';
$is_reset_view = ( 'reset' === $action && $reset_key && $reset_login );
?>

<main id="content" class="site-content account-page">
<div class="container account-page__container">

<?php if ( is_user_logged_in() ) :
    $current_user = wp_get_current_user();
    $saved_ids    = nest_well_get_saved_post_ids();
    $saved_count  = count( $saved_ids );
?>

    <!-- ============================================================
         LOGGED-IN VIEW
         ============================================================ -->

    <?php if ( isset( $_GET['profile_updated'] ) ) : ?>
    <div class="account-notice account-notice--success" role="status">
        <?php esc_html_e( 'Profile updated.', 'nest-and-well' ); ?>
    </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['password_updated'] ) ) : ?>
    <div class="account-notice account-notice--success" role="status">
        <?php esc_html_e( 'Password updated.', 'nest-and-well' ); ?>
    </div>
    <?php endif; ?>

    <?php if ( is_wp_error( $action_result ) ) : ?>
    <div class="account-notice account-notice--error" role="status">
        <?php echo esc_html( $action_result->get_error_message() ); ?>
    </div>
    <?php endif; ?>

    <div class="account-profile">

        <div class="account-profile__header">
            <div class="account-profile__avatar">
                <?php echo get_avatar( $current_user->ID, 80, '', esc_attr( $current_user->display_name ), array( 'class' => 'account-profile__avatar-img' ) ); ?>
            </div>
            <div class="account-profile__info">
                <h1 class="account-profile__name"><?php echo esc_html( $current_user->display_name ); ?></h1>
                <p class="account-profile__email"><?php echo esc_html( $current_user->user_email ); ?></p>
            </div>
            <div class="account-profile__actions">
                <a href="<?php echo esc_url( wp_logout_url( home_url( '/account/?logged_out=1' ) ) ); ?>"
                   class="btn btn--sage-outline account-profile__logout">
                    <?php esc_html_e( 'Log Out', 'nest-and-well' ); ?>
                </a>
            </div>
        </div>

        <!-- Saved articles count -->
        <div class="account-profile__stats">
            <div class="account-stat">
                <span class="account-stat__number"><?php echo esc_html( $saved_count ); ?></span>
                <span class="account-stat__label"><?php echo esc_html( _n( 'Saved Article', 'Saved Articles', $saved_count, 'nest-and-well' ) ); ?></span>
            </div>
        </div>

        <!-- Profile Settings -->
        <details class="account-edit" id="profile-settings">
            <summary class="account-edit__summary">
                <?php esc_html_e( 'Profile Settings', 'nest-and-well' ); ?>
            </summary>

            <div class="account-edit__panels">

                <!-- Identity sub-form -->
                <form class="account-form account-edit__form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate>
                    <?php wp_nonce_field( 'nest_well_profile', 'nest_well_profile_nonce' ); ?>

                    <h3 class="account-edit__heading"><?php esc_html_e( 'Identity', 'nest-and-well' ); ?></h3>

                    <div class="account-edit__row account-edit__row--two-col">
                        <div class="account-form__field">
                            <label class="account-form__label" for="display_name">
                                <?php esc_html_e( 'Display Name', 'nest-and-well' ); ?>
                            </label>
                            <input class="account-form__input"
                                   type="text"
                                   id="display_name"
                                   name="display_name"
                                   value="<?php echo esc_attr( $current_user->display_name ); ?>"
                                   autocomplete="name"
                                   required>
                        </div>

                        <div class="account-form__field">
                            <label class="account-form__label" for="user_email">
                                <?php esc_html_e( 'Email', 'nest-and-well' ); ?>
                            </label>
                            <input class="account-form__input"
                                   type="email"
                                   id="user_email"
                                   name="user_email"
                                   value="<?php echo esc_attr( $current_user->user_email ); ?>"
                                   autocomplete="email"
                                   required>
                        </div>
                    </div>

                    <div class="account-form__field">
                        <label class="account-form__label" for="current_password_identity">
                            <?php esc_html_e( 'Current Password', 'nest-and-well' ); ?>
                            <span class="account-form__required" aria-hidden="true">*</span>
                        </label>
                        <input class="account-form__input"
                               type="password"
                               id="current_password_identity"
                               name="current_password"
                               autocomplete="current-password"
                               required>
                        <span class="account-form__hint"><?php esc_html_e( 'Required to confirm changes.', 'nest-and-well' ); ?></span>
                    </div>

                    <button type="submit" class="btn btn--sage account-edit__submit">
                        <?php esc_html_e( 'Save changes', 'nest-and-well' ); ?>
                    </button>
                </form>

                <!-- Change-password sub-form -->
                <form class="account-form account-edit__form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate>
                    <?php wp_nonce_field( 'nest_well_profile', 'nest_well_profile_nonce' ); ?>
                    <input type="hidden" name="change_password" value="1">

                    <h3 class="account-edit__heading"><?php esc_html_e( 'Change Password', 'nest-and-well' ); ?></h3>

                    <div class="account-form__field">
                        <label class="account-form__label" for="current_password_pw">
                            <?php esc_html_e( 'Current Password', 'nest-and-well' ); ?>
                        </label>
                        <input class="account-form__input"
                               type="password"
                               id="current_password_pw"
                               name="current_password"
                               autocomplete="current-password"
                               required>
                    </div>

                    <div class="account-edit__row account-edit__row--two-col">
                        <div class="account-form__field">
                            <label class="account-form__label" for="new_password">
                                <?php esc_html_e( 'New Password', 'nest-and-well' ); ?>
                            </label>
                            <input class="account-form__input"
                                   type="password"
                                   id="new_password"
                                   name="new_password"
                                   autocomplete="new-password"
                                   minlength="8"
                                   required>
                            <span class="account-form__hint"><?php esc_html_e( 'Minimum 8 characters', 'nest-and-well' ); ?></span>
                        </div>

                        <div class="account-form__field">
                            <label class="account-form__label" for="new_password_confirm">
                                <?php esc_html_e( 'Confirm New Password', 'nest-and-well' ); ?>
                            </label>
                            <input class="account-form__input"
                                   type="password"
                                   id="new_password_confirm"
                                   name="new_password_confirm"
                                   autocomplete="new-password"
                                   minlength="8"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn--sage-outline account-edit__submit">
                        <?php esc_html_e( 'Update password', 'nest-and-well' ); ?>
                    </button>
                </form>

            </div><!-- .account-edit__panels -->
        </details><!-- #profile-settings -->

        <!-- Saved Articles List -->
        <div class="account-saved">
            <h2 class="account-saved__heading"><?php esc_html_e( 'Your Saved Articles', 'nest-and-well' ); ?></h2>

            <?php if ( empty( $saved_ids ) ) : ?>
            <div class="account-saved__empty">
                <p><?php esc_html_e( 'You haven\'t saved any articles yet.', 'nest-and-well' ); ?></p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
                    <?php esc_html_e( 'Browse Reviews', 'nest-and-well' ); ?>
                </a>
            </div>
            <?php else :
                $saved_posts = get_posts( array(
                    'post__in'       => $saved_ids,
                    'orderby'        => 'post__in',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                ) );
                foreach ( $saved_posts as $saved_post ) :
                    setup_postdata( $saved_post );
                    $score = get_post_meta( $saved_post->ID, '_review_score', true );
                    $badge = get_post_meta( $saved_post->ID, '_review_badge', true );
            ?>
            <article class="account-saved__item saved-article-card">
                <?php if ( has_post_thumbnail( $saved_post->ID ) ) : ?>
                <a href="<?php echo esc_url( get_permalink( $saved_post->ID ) ); ?>" class="saved-article-card__image-link" tabindex="-1" aria-hidden="true">
                    <?php echo get_the_post_thumbnail( $saved_post->ID, 'card-thumbnail', array( 'class' => 'saved-article-card__image', 'loading' => 'lazy' ) ); ?>
                </a>
                <?php endif; ?>
                <div class="saved-article-card__body">
                    <?php if ( $badge ) : ?>
                    <span class="article-card__badge article-card__badge--<?php echo esc_attr( $badge ); ?>">
                        <?php echo esc_html( nest_well_get_badge_label( $badge ) ); ?>
                    </span>
                    <?php endif; ?>
                    <h3 class="saved-article-card__title">
                        <a href="<?php echo esc_url( get_permalink( $saved_post->ID ) ); ?>">
                            <?php echo esc_html( $saved_post->post_title ); ?>
                        </a>
                    </h3>
                    <?php if ( $score ) : ?>
                    <div class="saved-article-card__rating">
                        <?php echo nest_well_star_rating_html( floatval( $score ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <span class="article-card__score"><?php echo esc_html( number_format( floatval( $score ), 1 ) ); ?>/10</span>
                    </div>
                    <?php endif; ?>
                    <div class="saved-article-card__meta">
                        <time datetime="<?php echo esc_attr( get_the_date( 'c', $saved_post->ID ) ); ?>">
                            <?php echo esc_html( get_the_date( '', $saved_post->ID ) ); ?>
                        </time>
                    </div>
                </div>
                <div class="saved-article-card__actions">
                    <a href="<?php echo esc_url( get_permalink( $saved_post->ID ) ); ?>" class="btn btn--sage">
                        <?php esc_html_e( 'Read Review', 'nest-and-well' ); ?>
                    </a>
                    <button class="save-article-btn is-saved"
                            data-post-id="<?php echo esc_attr( $saved_post->ID ); ?>"
                            aria-label="<?php esc_attr_e( 'Remove from saved', 'nest-and-well' ); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                        <?php esc_html_e( 'Saved', 'nest-and-well' ); ?>
                    </button>
                </div>
            </article>
            <?php endforeach; wp_reset_postdata(); ?>
            <?php endif; ?>
        </div><!-- .account-saved -->

    </div><!-- .account-profile -->

<?php else : // Not logged in — show login/register tabs ?>

    <!-- ============================================================
         GUEST VIEW — Login / Register Tabs
         ============================================================ -->

    <?php if ( isset( $_GET['logged_out'] ) ) : ?>
    <div class="account-notice account-notice--info" role="status">
        <?php esc_html_e( 'You have been logged out.', 'nest-and-well' ); ?>
    </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['registered'] ) ) : ?>
    <div class="account-notice account-notice--success" role="status">
        <?php esc_html_e( 'Account created! You are now logged in.', 'nest-and-well' ); ?>
    </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['reset_sent'] ) ) : ?>
    <div class="account-notice account-notice--success" role="status">
        <?php esc_html_e( 'If an account matches that email, a password reset link is on its way. Check your inbox.', 'nest-and-well' ); ?>
    </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['reset'] ) ) : ?>
    <div class="account-notice account-notice--success" role="status">
        <?php esc_html_e( 'Password updated. You can now sign in with your new password.', 'nest-and-well' ); ?>
    </div>
    <?php endif; ?>

    <?php if ( is_wp_error( $action_result ) ) : ?>
    <div class="account-notice account-notice--error" role="status">
        <?php echo esc_html( $action_result->get_error_message() ); ?>
    </div>
    <?php endif; ?>

    <?php if ( $is_reset_view ) : ?>

    <!-- ============================================================
         RESET PASSWORD VIEW (no tabs — focused single form)
         ============================================================ -->
    <div class="account-form-wrap">
        <h1 class="account-form__heading"><?php esc_html_e( 'Choose a new password', 'nest-and-well' ); ?></h1>
        <p class="account-form__subheading"><?php esc_html_e( 'Enter and confirm your new password below.', 'nest-and-well' ); ?></p>

        <form class="account-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate>
            <?php wp_nonce_field( 'nest_well_reset', 'nest_well_reset_nonce' ); ?>
            <input type="hidden" name="key" value="<?php echo esc_attr( $reset_key ); ?>">
            <input type="hidden" name="login" value="<?php echo esc_attr( $reset_login ); ?>">

            <div class="account-form__field">
                <label class="account-form__label" for="reset_new_password">
                    <?php esc_html_e( 'New Password', 'nest-and-well' ); ?>
                    <span class="account-form__required" aria-hidden="true">*</span>
                </label>
                <input class="account-form__input"
                       type="password"
                       id="reset_new_password"
                       name="new_password"
                       autocomplete="new-password"
                       minlength="8"
                       required>
                <span class="account-form__hint"><?php esc_html_e( 'Minimum 8 characters', 'nest-and-well' ); ?></span>
            </div>

            <div class="account-form__field">
                <label class="account-form__label" for="reset_new_password_confirm">
                    <?php esc_html_e( 'Confirm New Password', 'nest-and-well' ); ?>
                    <span class="account-form__required" aria-hidden="true">*</span>
                </label>
                <input class="account-form__input"
                       type="password"
                       id="reset_new_password_confirm"
                       name="new_password_confirm"
                       autocomplete="new-password"
                       minlength="8"
                       required>
            </div>

            <button type="submit" class="btn btn--primary account-form__submit">
                <?php esc_html_e( 'Reset password', 'nest-and-well' ); ?>
            </button>

            <p class="account-form__switch">
                <a href="<?php echo esc_url( home_url( '/account/' ) ); ?>" class="account-tabs__switch-link">
                    <?php esc_html_e( 'Back to login', 'nest-and-well' ); ?>
                </a>
            </p>
        </form>
    </div>

    <?php else : ?>

    <div class="account-tabs">
        <div class="account-tabs__nav" role="tablist" aria-label="<?php esc_attr_e( 'Account options', 'nest-and-well' ); ?>">
            <button class="account-tabs__tab <?php echo ( 'register' !== $tab && 'forgot' !== $tab ) ? 'is-active' : ''; ?>"
                    role="tab"
                    aria-selected="<?php echo ( 'register' !== $tab && 'forgot' !== $tab ) ? 'true' : 'false'; ?>"
                    aria-controls="tab-login"
                    id="tab-btn-login"
                    data-tab="login">
                <?php esc_html_e( 'Log In', 'nest-and-well' ); ?>
            </button>
            <button class="account-tabs__tab <?php echo 'register' === $tab ? 'is-active' : ''; ?>"
                    role="tab"
                    aria-selected="<?php echo 'register' === $tab ? 'true' : 'false'; ?>"
                    aria-controls="tab-register"
                    id="tab-btn-register"
                    data-tab="register">
                <?php esc_html_e( 'Create Account', 'nest-and-well' ); ?>
            </button>
            <button class="account-tabs__tab <?php echo 'forgot' === $tab ? 'is-active' : ''; ?>"
                    role="tab"
                    aria-selected="<?php echo 'forgot' === $tab ? 'true' : 'false'; ?>"
                    aria-controls="tab-forgot"
                    id="tab-btn-forgot"
                    data-tab="forgot">
                <?php esc_html_e( 'Forgot Password', 'nest-and-well' ); ?>
            </button>
        </div>

        <!-- LOGIN PANEL -->
        <div class="account-tabs__panel <?php echo ( 'register' !== $tab && 'forgot' !== $tab ) ? 'is-active' : ''; ?>"
             id="tab-login"
             role="tabpanel"
             aria-labelledby="tab-btn-login"
             <?php echo ( 'register' === $tab || 'forgot' === $tab ) ? 'hidden' : ''; ?>>

            <h1 class="account-form__heading"><?php esc_html_e( 'Welcome Back', 'nest-and-well' ); ?></h1>
            <p class="account-form__subheading"><?php esc_html_e( 'Log in to access your saved articles.', 'nest-and-well' ); ?></p>

            <form class="account-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate>
                <?php wp_nonce_field( 'nest_well_login', 'nest_well_login_nonce' ); ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr( get_permalink() ); ?>">

                <div class="account-form__field">
                    <label class="account-form__label" for="user_login">
                        <?php esc_html_e( 'Username or Email', 'nest-and-well' ); ?>
                    </label>
                    <input class="account-form__input"
                           type="text"
                           id="user_login"
                           name="log"
                           autocomplete="username"
                           required>
                </div>

                <div class="account-form__field">
                    <label class="account-form__label" for="user_pass">
                        <?php esc_html_e( 'Password', 'nest-and-well' ); ?>
                    </label>
                    <input class="account-form__input"
                           type="password"
                           id="user_pass"
                           name="pwd"
                           autocomplete="current-password"
                           required>
                </div>

                <div class="account-form__field account-form__field--checkbox">
                    <label class="account-form__checkbox-label">
                        <input type="checkbox" name="rememberme" value="forever">
                        <?php esc_html_e( 'Remember me', 'nest-and-well' ); ?>
                    </label>
                </div>

                <button type="submit" class="btn btn--primary account-form__submit">
                    <?php esc_html_e( 'Log In', 'nest-and-well' ); ?>
                </button>

                <p class="account-form__switch">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'forgot', get_permalink() ) ); ?>" class="account-tabs__switch-link" data-tab="forgot">
                        <?php esc_html_e( 'Forgot your password?', 'nest-and-well' ); ?>
                    </a>
                </p>

                <p class="account-form__switch">
                    <?php esc_html_e( "Don't have an account?", 'nest-and-well' ); ?>
                    <a href="#tab-register" class="account-tabs__switch-link" data-tab="register">
                        <?php esc_html_e( 'Create one', 'nest-and-well' ); ?>
                    </a>
                </p>
            </form>
        </div><!-- #tab-login -->

        <!-- REGISTER PANEL -->
        <div class="account-tabs__panel <?php echo 'register' === $tab ? 'is-active' : ''; ?>"
             id="tab-register"
             role="tabpanel"
             aria-labelledby="tab-btn-register"
             <?php echo 'register' !== $tab ? 'hidden' : ''; ?>>

            <h1 class="account-form__heading"><?php esc_html_e( 'Create Your Account', 'nest-and-well' ); ?></h1>
            <p class="account-form__subheading"><?php esc_html_e( 'Save your favorite reviews and get personalized picks.', 'nest-and-well' ); ?></p>

            <?php if ( ! get_option( 'users_can_register' ) ) : ?>
            <div class="account-notice account-notice--info">
                <?php esc_html_e( 'User registration is currently disabled.', 'nest-and-well' ); ?>
            </div>
            <?php else : ?>
            <form class="account-form" method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'register', get_permalink() ) ); ?>" novalidate>
                <?php wp_nonce_field( 'nest_well_register', 'nest_well_register_nonce' ); ?>

                <div class="account-form__field">
                    <label class="account-form__label" for="reg_username">
                        <?php esc_html_e( 'Username', 'nest-and-well' ); ?>
                        <span class="account-form__required" aria-hidden="true">*</span>
                    </label>
                    <input class="account-form__input"
                           type="text"
                           id="reg_username"
                           name="reg_username"
                           autocomplete="username"
                           value="<?php echo isset( $_POST['reg_username'] ) ? esc_attr( sanitize_user( wp_unslash( $_POST['reg_username'] ) ) ) : ''; ?>"
                           required>
                </div>

                <div class="account-form__field">
                    <label class="account-form__label" for="reg_email">
                        <?php esc_html_e( 'Email Address', 'nest-and-well' ); ?>
                        <span class="account-form__required" aria-hidden="true">*</span>
                    </label>
                    <input class="account-form__input"
                           type="email"
                           id="reg_email"
                           name="reg_email"
                           autocomplete="email"
                           value="<?php echo isset( $_POST['reg_email'] ) ? esc_attr( sanitize_email( wp_unslash( $_POST['reg_email'] ) ) ) : ''; ?>"
                           required>
                </div>

                <div class="account-form__field">
                    <label class="account-form__label" for="reg_password">
                        <?php esc_html_e( 'Password', 'nest-and-well' ); ?>
                        <span class="account-form__required" aria-hidden="true">*</span>
                    </label>
                    <input class="account-form__input"
                           type="password"
                           id="reg_password"
                           name="reg_password"
                           autocomplete="new-password"
                           minlength="8"
                           required>
                    <span class="account-form__hint"><?php esc_html_e( 'Minimum 8 characters', 'nest-and-well' ); ?></span>
                </div>

                <div class="account-form__field">
                    <label class="account-form__label" for="reg_password2">
                        <?php esc_html_e( 'Confirm Password', 'nest-and-well' ); ?>
                        <span class="account-form__required" aria-hidden="true">*</span>
                    </label>
                    <input class="account-form__input"
                           type="password"
                           id="reg_password2"
                           name="reg_password2"
                           autocomplete="new-password"
                           minlength="8"
                           required>
                </div>

                <button type="submit" class="btn btn--primary account-form__submit">
                    <?php esc_html_e( 'Create Account', 'nest-and-well' ); ?>
                </button>

                <p class="account-form__switch">
                    <?php esc_html_e( 'Already have an account?', 'nest-and-well' ); ?>
                    <a href="#tab-login" class="account-tabs__switch-link" data-tab="login">
                        <?php esc_html_e( 'Log in', 'nest-and-well' ); ?>
                    </a>
                </p>
            </form>
            <?php endif; ?>
        </div><!-- #tab-register -->

        <!-- FORGOT PANEL -->
        <div class="account-tabs__panel <?php echo 'forgot' === $tab ? 'is-active' : ''; ?>"
             id="tab-forgot"
             role="tabpanel"
             aria-labelledby="tab-btn-forgot"
             <?php echo 'forgot' !== $tab ? 'hidden' : ''; ?>>

            <h1 class="account-form__heading"><?php esc_html_e( 'Reset your password', 'nest-and-well' ); ?></h1>
            <p class="account-form__subheading"><?php esc_html_e( "Enter your username or email and we'll send you a reset link.", 'nest-and-well' ); ?></p>

            <form class="account-form" method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'forgot', get_permalink() ) ); ?>" novalidate>
                <?php wp_nonce_field( 'nest_well_reset_request', 'nest_well_reset_request_nonce' ); ?>

                <div class="account-form__field">
                    <label class="account-form__label" for="forgot_user_login">
                        <?php esc_html_e( 'Username or Email', 'nest-and-well' ); ?>
                    </label>
                    <input class="account-form__input"
                           type="text"
                           id="forgot_user_login"
                           name="user_login"
                           autocomplete="username"
                           required>
                </div>

                <button type="submit" class="btn btn--primary account-form__submit">
                    <?php esc_html_e( 'Send reset link', 'nest-and-well' ); ?>
                </button>

                <p class="account-form__switch">
                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="account-tabs__switch-link" data-tab="login">
                        <?php esc_html_e( 'Back to login', 'nest-and-well' ); ?>
                    </a>
                </p>
            </form>
        </div><!-- #tab-forgot -->

    </div><!-- .account-tabs -->

    <?php endif; // $is_reset_view ?>

<?php endif; // is_user_logged_in() ?>

</div><!-- .account-page__container -->
</main>

<?php get_footer(); ?>
