<?php
/**
 * Site Header Template
 *
 * Three-zone header:
 *   Zone 1: Top utility bar (forest bg) — logo + search
 *   Zone 2: Primary navigation (white bg, sticky)
 *   Zone 3: 5-stripe category bar
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

        <?php if ( get_theme_mod( 'nest_well_show_utility_bar', true ) ) : ?>
        <!-- =============================================
             ZONE 1: Top Utility Bar
             ============================================= -->
        <div class="site-header__utility-bar" role="complementary" aria-label="<?php esc_attr_e( 'Site utilities', 'nest-and-well' ); ?>">
            <div class="utility-bar__inner container">

                <!-- Logo / Brand Name -->
                <div class="utility-bar__brand">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="utility-bar__logo-link" rel="home">
                        <?php
                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                        if ( $custom_logo_id ) :
                            $logo_image = wp_get_attachment_image(
                                $custom_logo_id,
                                'full',
                                false,
                                array(
                                    'class'   => 'utility-bar__logo-img',
                                    'loading' => 'eager',
                                    'alt'     => get_bloginfo( 'name' ),
                                )
                            );
                            echo $logo_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        else :
                        ?>
                        <span class="utility-bar__site-name"><?php bloginfo( 'name' ); ?></span>
                        <?php endif; ?>
                    </a>
                    <span class="utility-bar__tagline"><?php bloginfo( 'description' ); ?></span>
                </div>

                <!-- Utility Actions -->
                <div class="utility-bar__actions">
                    <button class="utility-bar__search-toggle"
                            aria-label="<?php esc_attr_e( 'Toggle search', 'nest-and-well' ); ?>"
                            aria-expanded="false"
                            aria-controls="site-search-form">
                        <svg class="utility-bar__search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                </div>

            </div><!-- .utility-bar__inner -->

            <!-- Search Form (initially hidden) -->
            <div class="utility-bar__search-panel" id="site-search-form" hidden>
                <div class="container">
                    <?php get_search_form(); ?>
                </div>
            </div>
        </div><!-- .site-header__utility-bar -->
        <?php endif; ?>

        <!-- =============================================
             ZONE 3: 5-Stripe Category Bar
             ============================================= -->
        <?php get_template_part( 'template-parts/nav/stripe-nav' ); ?>

    </header><!-- #masthead -->

    <!-- Mobile Menu Overlay -->
    <?php get_template_part( 'template-parts/nav/mobile-menu' ); ?>

    <div id="content" class="site-content">
