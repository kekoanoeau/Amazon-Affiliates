<?php
/**
 * "Explore Category" sidebar block.
 *
 * Lists 5–8 popular posts from the current post's primary category so readers
 * can keep browsing without bouncing. Renders only on single posts that have
 * a category assigned and at least one sibling post.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_singular( 'post' ) ) {
    return;
}

$cats = get_the_category( get_the_ID() );
if ( empty( $cats ) ) {
    return;
}
$primary = $cats[0];

$siblings = get_posts(
    array(
        'post_type'           => 'post',
        'posts_per_page'      => 7,
        'post__not_in'        => array( get_the_ID() ),
        'category'            => $primary->term_id,
        'orderby'             => 'comment_count',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    )
);

if ( empty( $siblings ) ) {
    return;
}

// Stripe accent lookup keyed by category slug — keeps wayfinding consistent
// with the homepage tiles and the category strip.
$accents = array(
    'smart-home'    => 'var(--pine)',
    'wellness-tech' => 'var(--sage)',
    'home-beauty'   => 'var(--moss)',
    'gift-guides'   => 'var(--amber)',
    'deals'         => 'var(--clay)',
);
$accent = isset( $accents[ $primary->slug ] ) ? $accents[ $primary->slug ] : 'var(--sage)';
?>
<aside class="explore-category widget" aria-label="<?php echo esc_attr( sprintf( __( 'More in %s', 'nest-and-well' ), $primary->name ) ); ?>" style="--cat-accent: <?php echo esc_attr( $accent ); ?>;">
    <p class="explore-category__eyebrow"><?php esc_html_e( 'More in', 'nest-and-well' ); ?></p>
    <h4 class="explore-category__title">
        <a href="<?php echo esc_url( get_category_link( $primary->term_id ) ); ?>" class="explore-category__title-link">
            <?php echo esc_html( $primary->name ); ?>
        </a>
    </h4>
    <ul class="explore-category__list">
        <?php foreach ( $siblings as $sibling ) :
            $score = (float) get_post_meta( $sibling->ID, '_review_score', true );
            ?>
        <li class="explore-category__item">
            <a href="<?php echo esc_url( get_permalink( $sibling ) ); ?>" class="explore-category__link">
                <span class="explore-category__post-title"><?php echo esc_html( get_the_title( $sibling ) ); ?></span>
                <?php if ( $score ) : ?>
                <span class="explore-category__score nw-num"><?php echo esc_html( number_format( $score, 1 ) ); ?>/10</span>
                <?php endif; ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <a href="<?php echo esc_url( get_category_link( $primary->term_id ) ); ?>" class="explore-category__all">
        <?php
        printf(
            /* translators: %s: category name */
            esc_html__( 'Browse all %s', 'nest-and-well' ),
            esc_html( $primary->name )
        );
        ?>
        <span aria-hidden="true">&rarr;</span>
    </a>
</aside>
