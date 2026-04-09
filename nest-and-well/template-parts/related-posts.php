<?php
/**
 * Related Posts Template Part
 * Displays 3 related posts from the same category below single post content.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$current_post_id = get_the_ID();
$categories      = get_the_category( $current_post_id );

if ( empty( $categories ) ) {
    return;
}

$cat_ids = wp_list_pluck( $categories, 'term_id' );

$args = array(
    'post_type'           => 'post',
    'posts_per_page'      => 3,
    'post__not_in'        => array( $current_post_id ),
    'category__in'        => $cat_ids,
    'ignore_sticky_posts' => true,
    'orderby'             => 'relevance',
    'order'               => 'DESC',
);

$related_posts = new WP_Query( $args );

if ( ! $related_posts->have_posts() ) {
    return;
}
?>

<section class="related-posts" aria-labelledby="related-posts-heading">
    <h2 class="related-posts__heading" id="related-posts-heading">
        <?php esc_html_e( 'Related Reviews', 'nest-and-well' ); ?>
    </h2>

    <div class="article-grid article-grid--3col article-grid--compact">
        <?php
        while ( $related_posts->have_posts() ) :
            $related_posts->the_post();
            get_template_part( 'template-parts/content-article' );
        endwhile;

        wp_reset_postdata();
        ?>
    </div>
</section><!-- .related-posts -->
