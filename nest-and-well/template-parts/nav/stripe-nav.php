<?php
/**
 * 5-Stripe Category Navigation Bar
 * Expandable stripes — clicking the chevron reveals child category links.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Helper: derive a category slug from a stripe URL.
 * e.g. "/smart-home/" → "smart-home"
 */
function nest_well_slug_from_url( $url ) {
    $path = trim( parse_url( $url, PHP_URL_PATH ), '/' );
    // Take the last non-empty path segment
    $parts = array_filter( explode( '/', $path ) );
    return end( $parts ) ?: '';
}

/**
 * Get child category links for a given parent slug.
 *
 * @param string $slug Parent category slug.
 * @return array[] Array of ['name', 'url'] for each child.
 */
function nest_well_get_stripe_children( $slug ) {
    if ( ! $slug ) {
        return array();
    }

    $parent = get_term_by( 'slug', $slug, 'category' );
    if ( ! $parent || is_wp_error( $parent ) ) {
        return array();
    }

    $children = get_categories( array(
        'parent'     => $parent->term_id,
        'hide_empty' => false,
        'number'     => 8,
        'orderby'    => 'count',
        'order'      => 'DESC',
    ) );

    $items = array();
    foreach ( $children as $child ) {
        $items[] = array(
            'name' => $child->name,
            'url'  => get_term_link( $child ),
        );
    }
    return $items;
}

// Stripe 1 uses --pine (distinct dark teal) instead of --forest
// to differentiate it from the header and hero backgrounds.
$stripes = array(
    1 => array(
        'label' => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
        'url'   => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
        'color' => 'var(--pine)',   // distinct from --forest header/hero
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
        <div class="stripe-nav__item stripe-nav__item--<?php echo esc_attr( $num ); ?>"
             style="background-color: <?php echo esc_attr( $stripe['color'] ); ?>;">

            <a href="<?php echo esc_url( $stripe['url'] ); ?>"
               class="stripe-nav__label">
                <?php echo esc_html( $stripe['label'] ); ?>
            </a>

        </div><!-- .stripe-nav__item -->
        <?php endforeach; ?>
    </div><!-- .stripe-nav__inner -->
</nav>
