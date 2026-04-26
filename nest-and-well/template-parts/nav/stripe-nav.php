<?php
/**
 * Category Strip — quiet horizontal text nav.
 *
 * Replaces the legacy 5-colour stripe bar with a single-row text nav.
 * Each category retains its accent colour as a 3 px hover/active underline,
 * keeping wayfinding without dominating the chrome.
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
    $path  = trim( wp_parse_url( $url, PHP_URL_PATH ), '/' );
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

    $children = get_categories(
        array(
            'parent'     => $parent->term_id,
            'hide_empty' => false,
            'number'     => 8,
            'orderby'    => 'count',
            'order'      => 'DESC',
        )
    );

    $items = array();
    foreach ( $children as $child ) {
        $items[] = array(
            'name' => $child->name,
            'url'  => get_term_link( $child ),
        );
    }
    return $items;
}

$stripes = array(
    1 => array(
        'label'  => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
        'url'    => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
        'accent' => 'var(--pine)',
    ),
    2 => array(
        'label'  => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
        'url'    => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
        'accent' => 'var(--sage)',
    ),
    3 => array(
        'label'  => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
        'url'    => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
        'accent' => 'var(--moss)',
    ),
    4 => array(
        'label'  => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
        'url'    => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
        'accent' => 'var(--amber)',
    ),
    5 => array(
        'label'  => get_theme_mod( 'nest_well_stripe_5_label', 'Deals' ),
        'url'    => get_theme_mod( 'nest_well_stripe_5_url', '/deals/' ),
        'accent' => 'var(--clay)',
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
<nav class="category-strip" aria-label="<?php esc_attr_e( 'Categories', 'nest-and-well' ); ?>">
    <div class="category-strip__inner container">
        <ul class="category-strip__list">
            <?php foreach ( $stripes as $num => $stripe ) :
                $slug      = nest_well_slug_from_url( $stripe['url'] );
                $is_active = ( $slug && $slug === $current_slug );
                ?>
            <li class="category-strip__item<?php echo $is_active ? ' is-active' : ''; ?>"
                style="--cat-accent: <?php echo esc_attr( $stripe['accent'] ); ?>;">
                <a href="<?php echo esc_url( $stripe['url'] ); ?>" class="category-strip__link">
                    <?php echo esc_html( $stripe['label'] ); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
