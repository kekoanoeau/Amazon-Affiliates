<?php
/**
 * 5-Stripe Category Navigation Bar
 * Renders the horizontal 5-stripe colored category navigation.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$stripes = array(
    1 => array(
        'label' => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
        'url'   => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
        'color' => 'var(--forest)',
    ),
    2 => array(
        'label' => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
        'url'   => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
        'color' => 'var(--sage)',
    ),
    3 => array(
        'label' => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
        'url'   => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
        'color' => 'var(--moss)',
    ),
    4 => array(
        'label' => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
        'url'   => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
        'color' => 'var(--amber)',
    ),
    5 => array(
        'label' => get_theme_mod( 'nest_well_stripe_5_label', 'Deals' ),
        'url'   => get_theme_mod( 'nest_well_stripe_5_url', '/deals/' ),
        'color' => 'var(--clay)',
    ),
);
?>
<nav class="stripe-nav" aria-label="<?php esc_attr_e( 'Category Navigation', 'nest-and-well' ); ?>">
    <div class="stripe-nav__inner">
        <?php foreach ( $stripes as $num => $stripe ) : ?>
        <a href="<?php echo esc_url( $stripe['url'] ); ?>"
           class="stripe-nav__item stripe-nav__item--<?php echo esc_attr( $num ); ?>"
           style="background-color: <?php echo esc_attr( $stripe['color'] ); ?>;">
            <?php echo esc_html( $stripe['label'] ); ?>
        </a>
        <?php endforeach; ?>
    </div>
</nav>
