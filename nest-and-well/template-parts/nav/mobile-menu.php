<?php
/**
 * Mobile Menu Overlay
 * Full-screen overlay navigation for mobile devices.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'nest-and-well' ); ?>" aria-hidden="true">
    <div class="mobile-menu__overlay" id="mobile-menu-overlay"></div>
    <div class="mobile-menu__panel">
        <div class="mobile-menu__header">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-menu__logo">
                <span class="mobile-menu__logo-text">Nest &amp; Well</span>
            </a>
            <button class="mobile-menu__close" id="mobile-menu-close" aria-label="<?php esc_attr_e( 'Close menu', 'nest-and-well' ); ?>">
                <span class="mobile-menu__close-icon" aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="mobile-menu__body">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'mobile-menu__nav',
                    'fallback_cb'    => false,
                    'depth'          => 2,
                )
            );
            ?>

            <div class="mobile-menu__stripes">
                <?php
                $stripes = array(
                    1 => array(
                        'label' => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
                        'class' => 'stripe--forest',
                    ),
                    2 => array(
                        'label' => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
                        'class' => 'stripe--sage',
                    ),
                    3 => array(
                        'label' => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
                        'class' => 'stripe--moss',
                    ),
                    4 => array(
                        'label' => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
                        'class' => 'stripe--amber',
                    ),
                    5 => array(
                        'label' => get_theme_mod( 'nest_well_stripe_5_label', 'Deals' ),
                        'url'   => get_theme_mod( 'nest_well_stripe_5_url', '/deals/' ),
                        'class' => 'stripe--clay',
                    ),
                );

                foreach ( $stripes as $stripe ) :
                ?>
                <a href="<?php echo esc_url( $stripe['url'] ); ?>"
                   class="mobile-menu__stripe <?php echo esc_attr( $stripe['class'] ); ?>">
                    <?php echo esc_html( $stripe['label'] ); ?>
                </a>
                <?php endforeach; ?>
            </div>

            <div class="mobile-menu__search">
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</div>
