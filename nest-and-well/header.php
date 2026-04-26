<?php
/**
 * Site Header Template
 *
 * Single sticky editorial bar — wordmark left, primary nav center,
 * search + account right. A quiet text-only category strip sits
 * directly beneath it.
 *
 * @package nest-and-well
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content">
    <?php esc_html_e( 'Skip to content', 'nest-and-well' ); ?>
</a>

<div id="page" class="site">

    <header id="masthead" class="site-header" role="banner">

        <!-- Single editorial bar -->
        <div class="site-header__bar">
            <div class="site-header__inner container">

                <!-- Hamburger (mobile only) -->
                <button class="site-header__hamburger"
                        id="mobile-menu-toggle"
                        aria-label="<?php esc_attr_e( 'Open menu', 'nest-and-well' ); ?>"
                        aria-controls="mobile-menu"
                        aria-expanded="false">
                    <span class="hamburger__line" aria-hidden="true"></span>
                    <span class="hamburger__line" aria-hidden="true"></span>
                    <span class="hamburger__line" aria-hidden="true"></span>
                </button>

                <!-- Wordmark -->
                <div class="site-header__brand">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wordmark" rel="home">
                        <?php
                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                        if ( $custom_logo_id ) :
                            echo wp_get_attachment_image(
                                $custom_logo_id,
                                'full',
                                false,
                                array(
                                    'class'   => 'wordmark__logo-img',
                                    'loading' => 'eager',
                                    'alt'     => get_bloginfo( 'name' ),
                                )
                            ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        else :
                        ?>
                        <span class="wordmark__text">
                            Nest <span class="wordmark__amp">&amp;</span> Well
                        </span>
                        <?php endif; ?>
                    </a>
                    <?php if ( is_front_page() ) : ?>
                    <span class="wordmark__tagline">
                        <?php
                        echo esc_html(
                            get_theme_mod(
                                'nest_well_brand_tagline',
                                __( 'Shop Smarter. Live Better.', 'nest-and-well' )
                            )
                        );
                        ?>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Primary nav (desktop) -->
                <nav class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary Navigation', 'nest-and-well' ); ?>">
                    <?php
                    if ( has_nav_menu( 'primary' ) ) {
                        wp_nav_menu(
                            array(
                                'theme_location' => 'primary',
                                'menu_class'     => 'primary-nav__menu',
                                'container'      => false,
                                'depth'          => 2,
                                'fallback_cb'    => false,
                            )
                        );
                    }
                    ?>
                </nav>

                <!-- Right-aligned utilities -->
                <div class="site-header__actions">

                    <!-- Search -->
                    <div class="site-header__search-wrap" id="site-search-form">
                        <form role="search" method="get" class="site-header__search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <label class="screen-reader-text" for="utility-search-input">
                                <?php esc_html_e( 'Search', 'nest-and-well' ); ?>
                            </label>
                            <input type="search"
                                   id="utility-search-input"
                                   class="site-header__search-input"
                                   name="s"
                                   placeholder="<?php esc_attr_e( 'Search reviews…', 'nest-and-well' ); ?>"
                                   value="<?php echo esc_attr( get_search_query() ); ?>"
                                   autocomplete="off">
                            <button type="submit" class="site-header__search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'nest-and-well' ); ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <button class="site-header__search-toggle"
                            aria-label="<?php esc_attr_e( 'Search', 'nest-and-well' ); ?>"
                            aria-expanded="false"
                            aria-controls="site-search-form">
                        <svg class="site-header__search-icon site-header__search-icon--search" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <svg class="site-header__search-icon site-header__search-icon--close" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Account -->
                    <a href="<?php echo esc_url( home_url( '/account/' ) ); ?>" class="site-header__account-link" aria-label="<?php esc_attr_e( 'Account', 'nest-and-well' ); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>

                </div><!-- .site-header__actions -->

            </div><!-- .site-header__inner -->
        </div><!-- .site-header__bar -->

        <!-- Quiet category strip -->
        <?php get_template_part( 'template-parts/nav/stripe-nav' ); ?>

    </header><!-- #masthead -->

    <!-- Mobile menu drawer -->
    <?php get_template_part( 'template-parts/nav/mobile-menu' ); ?>

    <div id="content" class="site-content">
