<?php
/**
 * 404 Not Found Template
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();

$tiles = array(
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
?>

<main id="main" class="site-main site-main--404">
    <div class="error-404 container">

        <div class="error-404__head">
            <p class="error-404__eyebrow"><?php esc_html_e( 'Lost the trail', 'nest-and-well' ); ?></p>
            <h1 class="error-404__title">
                <span class="error-404__title-num nw-num" aria-hidden="true">404</span>
                <span class="error-404__title-text"><?php esc_html_e( 'We couldn\'t find that page', 'nest-and-well' ); ?></span>
            </h1>
            <p class="error-404__message">
                <?php esc_html_e( 'The link may have moved, or the product page might be retired. Try a search, or pick up where most readers start.', 'nest-and-well' ); ?>
            </p>

            <div class="error-404__search">
                <?php get_search_form(); ?>
            </div>

            <div class="error-404__actions">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--sage">
                    <?php esc_html_e( 'Back to home', 'nest-and-well' ); ?>
                </a>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/' ) ); ?>" class="btn btn--sage-outline">
                    <?php esc_html_e( 'Latest reviews', 'nest-and-well' ); ?>
                </a>
            </div>
        </div>

        <section class="error-404__section">
            <h2 class="error-404__section-title"><?php esc_html_e( 'Browse a category', 'nest-and-well' ); ?></h2>
            <ul class="error-404__tiles">
                <?php foreach ( $tiles as $tile ) : ?>
                <li class="error-404__tile" style="--cat-accent: <?php echo esc_attr( $tile['accent'] ); ?>;">
                    <a href="<?php echo esc_url( $tile['url'] ); ?>" class="error-404__tile-link">
                        <span class="error-404__tile-label"><?php echo esc_html( $tile['label'] ); ?></span>
                        <span class="error-404__tile-arrow" aria-hidden="true">&rarr;</span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <?php
        $recent = new WP_Query(
            array(
                'posts_per_page'      => 4,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            )
        );

        if ( $recent->have_posts() ) :
        ?>
        <section class="error-404__section">
            <h2 class="error-404__section-title"><?php esc_html_e( 'Recent reviews', 'nest-and-well' ); ?></h2>
            <ul class="error-404__recent">
                <?php while ( $recent->have_posts() ) : $recent->the_post();
                    $score = (float) get_post_meta( get_the_ID(), '_review_score', true );
                    ?>
                <li class="error-404__recent-item">
                    <a href="<?php the_permalink(); ?>" class="error-404__recent-link">
                        <span class="error-404__recent-title"><?php the_title(); ?></span>
                        <?php if ( $score ) : ?>
                        <span class="error-404__recent-score nw-num"><?php echo esc_html( number_format( $score, 1 ) ); ?>/10</span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
        </section>
        <?php
        wp_reset_postdata();
        endif;
        ?>

    </div>
</main>

<?php
get_footer();
