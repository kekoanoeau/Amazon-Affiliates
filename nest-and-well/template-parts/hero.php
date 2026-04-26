<?php
/**
 * Homepage Hero — editorial brand statement, tagline, CTAs, category tiles.
 *
 * Reads Customizer settings registered in inc/customizer.php
 * (nest_well_hero_headline, nest_well_hero_subtext, nest_well_cta_*).
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$headline      = get_theme_mod( 'nest_well_hero_headline', __( 'Smart Home & Wellness, Thoughtfully Reviewed', 'nest-and-well' ) );
$subtext       = get_theme_mod( 'nest_well_hero_subtext', __( 'We test products in real homes so you can shop with confidence.', 'nest-and-well' ) );
$cta_label     = get_theme_mod( 'nest_well_cta_primary_label', __( 'Browse Reviews', 'nest-and-well' ) );
$cta_url       = get_theme_mod( 'nest_well_cta_primary_url', '/smart-home/' );
$cta2_label    = get_theme_mod( 'nest_well_cta_secondary_label', __( 'Start with Wellness', 'nest-and-well' ) );
$cta2_url      = get_theme_mod( 'nest_well_cta_secondary_url', '/wellness-tech/' );
$show_tiles    = get_theme_mod( 'nest_well_show_category_grid', true );
$brand_tagline = get_theme_mod( 'nest_well_brand_tagline', __( 'Shop Smarter. Live Better.', 'nest-and-well' ) );

$tiles = array(
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
        'url'    => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
        'accent' => 'var(--pine)',
        'desc'   => __( 'Speakers, plugs, cameras & hubs', 'nest-and-well' ),
    ),
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
        'url'    => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
        'accent' => 'var(--sage)',
        'desc'   => __( 'Sleep, recovery & mindfulness', 'nest-and-well' ),
    ),
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
        'url'    => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
        'accent' => 'var(--moss)',
        'desc'   => __( 'At-home skincare & spa', 'nest-and-well' ),
    ),
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
        'url'    => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
        'accent' => 'var(--amber)',
        'desc'   => __( 'Curated picks for everyone', 'nest-and-well' ),
    ),
    array(
        'label'  => get_theme_mod( 'nest_well_stripe_5_label', 'Deals' ),
        'url'    => get_theme_mod( 'nest_well_stripe_5_url', '/deals/' ),
        'accent' => 'var(--clay)',
        'desc'   => __( 'Worthwhile sales, vetted weekly', 'nest-and-well' ),
    ),
);
?>
<section class="hp-hero" aria-labelledby="hp-hero-headline">
    <div class="hp-hero__inner container">

        <div class="hp-hero__lede">
            <p class="hp-hero__eyebrow"><?php echo esc_html( $brand_tagline ); ?></p>
            <h1 id="hp-hero-headline" class="hp-hero__headline"><?php echo esc_html( $headline ); ?></h1>
            <p class="hp-hero__subtext"><?php echo esc_html( $subtext ); ?></p>

            <div class="hp-hero__ctas">
                <?php if ( $cta_label && $cta_url ) : ?>
                <a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn--sage hp-hero__cta">
                    <?php echo esc_html( $cta_label ); ?>
                </a>
                <?php endif; ?>
                <?php if ( $cta2_label && $cta2_url ) : ?>
                <a href="<?php echo esc_url( $cta2_url ); ?>" class="btn btn--sage-outline hp-hero__cta">
                    <?php echo esc_html( $cta2_label ); ?>
                </a>
                <?php endif; ?>
            </div>

            <p class="hp-hero__trust">
                <span class="hp-hero__trust-dot" aria-hidden="true">&bull;</span>
                <?php esc_html_e( 'Independent reviews. No paid placements. Tested in real homes.', 'nest-and-well' ); ?>
            </p>
        </div>

        <?php if ( $show_tiles ) : ?>
        <ul class="hp-hero__tiles" aria-label="<?php esc_attr_e( 'Browse categories', 'nest-and-well' ); ?>">
            <?php foreach ( $tiles as $tile ) : ?>
            <li class="hp-hero__tile" style="--cat-accent: <?php echo esc_attr( $tile['accent'] ); ?>;">
                <a href="<?php echo esc_url( $tile['url'] ); ?>" class="hp-hero__tile-link">
                    <span class="hp-hero__tile-label"><?php echo esc_html( $tile['label'] ); ?></span>
                    <span class="hp-hero__tile-desc"><?php echo esc_html( $tile['desc'] ); ?></span>
                    <span class="hp-hero__tile-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

    </div>
</section>
