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
        <?php foreach ( $stripes as $num => $stripe ) :
            $slug     = nest_well_slug_from_url( $stripe['url'] );
            $children = nest_well_get_stripe_children( $slug );
            $has_kids = ! empty( $children );
            $menu_id  = 'stripe-dropdown-' . $num;
        ?>
        <div class="stripe-nav__item stripe-nav__item--<?php echo esc_attr( $num ); ?> <?php echo $has_kids ? 'has-dropdown' : ''; ?>"
             style="--stripe-color: <?php echo esc_attr( $stripe['color'] ); ?>; background-color: <?php echo esc_attr( $stripe['color'] ); ?>;">

            <a href="<?php echo esc_url( $stripe['url'] ); ?>"
               class="stripe-nav__label">
                <?php echo esc_html( $stripe['label'] ); ?>
            </a>

            <?php if ( $has_kids ) : ?>
            <button class="stripe-nav__toggle"
                    aria-expanded="false"
                    aria-controls="<?php echo esc_attr( $menu_id ); ?>"
                    aria-label="<?php echo esc_attr( sprintf( __( 'Expand %s subcategories', 'nest-and-well' ), $stripe['label'] ) ); ?>">
                <svg class="stripe-nav__chevron" width="10" height="10" viewBox="0 0 10 10" fill="currentColor" aria-hidden="true">
                    <path d="M1 3 L5 7 L9 3" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <div class="stripe-nav__dropdown" id="<?php echo esc_attr( $menu_id ); ?>" hidden>
                <ul class="stripe-nav__dropdown-list">
                    <li class="stripe-nav__dropdown-item stripe-nav__dropdown-item--all">
                        <a href="<?php echo esc_url( $stripe['url'] ); ?>">
                            <?php echo esc_html( sprintf( __( 'All %s', 'nest-and-well' ), $stripe['label'] ) ); ?>
                        </a>
                    </li>
                    <?php foreach ( $children as $child ) : ?>
                    <li class="stripe-nav__dropdown-item">
                        <a href="<?php echo esc_url( $child['url'] ); ?>">
                            <?php echo esc_html( $child['name'] ); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

        </div><!-- .stripe-nav__item -->
        <?php endforeach; ?>
    </div><!-- .stripe-nav__inner -->
</nav>
