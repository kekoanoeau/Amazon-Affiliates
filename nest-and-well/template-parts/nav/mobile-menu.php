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
                <span class="mobile-menu__logo-text">Nest <span class="wordmark__amp">&amp;</span> Well</span>
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

            <ul class="mobile-menu__categories">
                <?php
                $stripes = array(
                    array(
                        'label'  => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
                        'url'    => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
                        'accent' => 'var(--pine)',
                    ),
                    array(
                        'label'  => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
                        'url'    => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
                        'accent' => 'var(--sage)',
                    ),
                    array(
                        'label'  => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
                        'url'    => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
                        'accent' => 'var(--moss)',
                    ),
                    array(
                        'label'  => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
                        'url'    => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
                        'accent' => 'var(--amber)',
                    ),
                    array(
                        'label'  => get_theme_mod( 'nest_well_stripe_5_label', 'Deals' ),
                        'url'    => get_theme_mod( 'nest_well_stripe_5_url', '/deals/' ),
                        'accent' => 'var(--clay)',
                    ),
                );

                foreach ( $stripes as $stripe ) :
                ?>
                <li class="mobile-menu__category-item" style="--cat-accent: <?php echo esc_attr( $stripe['accent'] ); ?>;">
                    <a href="<?php echo esc_url( $stripe['url'] ); ?>" class="mobile-menu__category-link">
                        <?php echo esc_html( $stripe['label'] ); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="mobile-menu__search">
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</div>
