<?php
/**
 * Mobile bottom-tab navigation.
 *
 * Sticky 5-category bar fixed to the bottom of the viewport on mobile.
 * Mirrors the Customizer-configured stripe categories (same accent palette
 * as the desktop category strip) so wayfinding is consistent across
 * breakpoints. Hidden on desktop via CSS.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$tabs = array(
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
        'url'    => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
        'accent' => 'var(--pine)',
        'icon'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
    ),
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
        'url'    => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
        'accent' => 'var(--sage)',
        'icon'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
    ),
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
        'url'    => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
        'accent' => 'var(--moss)',
        'icon'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="9"/></svg>',
    ),
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
        'url'    => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
        'accent' => 'var(--amber)',
        'icon'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7zM12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>',
    ),
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_5_label', 'Deals' ),
        'url'    => get_theme_mod( 'nest_well_stripe_5_url', '/deals/' ),
        'accent' => 'var(--clay)',
        'icon'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
    ),
);

$current_slug = '';
if ( is_category() ) {
    $current_slug = get_queried_object()->slug;
} elseif ( is_singular( 'post' ) ) {
    $cats = get_the_category();
    if ( $cats ) {
        $current_slug = $cats[0]->slug;
    }
}
?>
<nav class="bottom-tabs" aria-label="<?php esc_attr_e( 'Categories', 'nest-and-well' ); ?>">
    <?php foreach ( $tabs as $tab ) :
        $slug      = nest_well_slug_from_url( $tab['url'] );
        $is_active = ( $slug && $slug === $current_slug );
        ?>
    <a href="<?php echo esc_url( $tab['url'] ); ?>"
       class="bottom-tabs__item<?php echo $is_active ? ' is-active' : ''; ?>"
       style="--cat-accent: <?php echo esc_attr( $tab['accent'] ); ?>;"
       <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
        <span class="bottom-tabs__icon" aria-hidden="true"><?php echo $tab['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        <span class="bottom-tabs__label"><?php echo esc_html( $tab['label'] ); ?></span>
    </a>
    <?php endforeach; ?>
</nav>
