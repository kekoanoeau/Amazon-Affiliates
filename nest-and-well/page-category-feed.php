<?php
/**
 * Template Name: Category Feed
 *
 * A page template that renders an infinite-scroll article grid filtered
 * to a specific category. The category is chosen per-page via the
 * "Category Feed Settings" meta box in the page editor.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

get_header();

$page_id     = get_the_ID();
$category_id = (int) get_post_meta( $page_id, '_feed_category', true );

$query_args = array(
    'post_type'           => 'post',
    'posts_per_page'      => 12,
    'ignore_sticky_posts' => true,
    'post_status'         => 'publish',
);

if ( $category_id ) {
    $query_args['cat'] = $category_id;
}

$feed_query  = new WP_Query( $query_args );
$total_pages = (int) $feed_query->max_num_pages;
?>

<main id="main" class="site-main site-main--category-feed">

    <!-- Page header -->
    <div class="category-feed-hero">
        <div class="container">
            <?php nest_well_breadcrumbs(); ?>
            <h1 class="category-feed-hero__title"><?php the_title(); ?></h1>
            <?php
            $description = get_the_content();
            if ( $description ) :
            ?>
            <div class="category-feed-hero__desc">
                <?php echo wp_kses_post( wpautop( $description ) ); ?>
            </div>
            <?php endif; ?>
        </div>
    </div><!-- .category-feed-hero -->

    <div class="hp-feed container">

        <!-- Article grid — initial server-side load -->
        <div class="hp-feed__grid" id="hp-feed-grid">
            <?php if ( $feed_query->have_posts() ) : ?>
                <?php while ( $feed_query->have_posts() ) : $feed_query->the_post(); ?>
                <div class="flex-item homepage-style-item">
                    <?php get_template_part( 'template-parts/content-article' ); ?>
                </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
            <p class="hp-feed__no-posts">
                <?php esc_html_e( 'No articles found in this category. Check back soon!', 'nest-and-well' ); ?>
            </p>
            <?php endif; ?>
        </div><!-- #hp-feed-grid -->

        <!-- Infinite scroll sentinel -->
        <?php if ( $total_pages > 1 ) : ?>
        <div class="hp-feed__sentinel js-infinite-sentinel"
             aria-hidden="true"
             data-page="2"
             data-per-page="12"
             data-total-pages="<?php echo esc_attr( $total_pages ); ?>"
             <?php if ( $category_id ) : ?>data-category-id="<?php echo esc_attr( $category_id ); ?>"<?php endif; ?>>
        </div>
        <?php endif; ?>

        <!-- Loading indicator -->
        <div class="hp-feed__loading js-infinite-loading" hidden aria-live="polite" aria-busy="false">
            <span class="hp-feed__spinner" aria-hidden="true"></span>
            <?php esc_html_e( 'Good things take a moment\u2026', 'nest-and-well' ); ?>
        </div>

        <!-- End-of-feed message -->
        <div class="hp-feed__end js-infinite-end" hidden aria-live="polite">
            <p><?php esc_html_e( "You've read everything. Nice work.", 'nest-and-well' ); ?></p>
        </div>

    </div><!-- .hp-feed -->

</main><!-- #main -->

<?php get_footer(); ?>
