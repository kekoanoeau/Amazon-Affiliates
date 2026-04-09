<?php
/**
 * Article Card Template Part
 * Used in homepage grid, archives, related posts, and search results.
 *
 * Supports variants:
 *   - Default card
 *   - Editor's Choice (3px top border --amber)
 *   - Best Value (3px top border --moss)
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$post_id      = get_the_ID();
$review_score = get_post_meta( $post_id, '_review_score', true );
$review_badge = get_post_meta( $post_id, '_review_badge', true );
$read_time    = nest_well_get_read_time( $post_id );

$categories   = get_the_category();
$primary_cat  = $categories ? $categories[0] : null;

// Category stripe color map
$stripe_colors = array(
    'smart-home'    => 'stripe--forest',
    'wellness-tech' => 'stripe--sage',
    'home-beauty'   => 'stripe--moss',
    'gift-guides'   => 'stripe--amber',
    'deals'         => 'stripe--clay',
);

$cat_color_class = '';
if ( $primary_cat ) {
    $cat_color_class = isset( $stripe_colors[ $primary_cat->slug ] ) ? $stripe_colors[ $primary_cat->slug ] : 'stripe--forest';
}

// Card variant classes
$card_classes = array( 'article-card' );
if ( 'editors-choice' === $review_badge ) {
    $card_classes[] = 'article-card--editors-choice';
} elseif ( 'best-value' === $review_badge ) {
    $card_classes[] = 'article-card--best-value';
}
?>

<article <?php post_class( $card_classes ); ?> id="post-<?php the_ID(); ?>">

    <!-- Featured Image -->
    <div class="article-card__image-wrap">
        <a href="<?php the_permalink(); ?>" class="article-card__image-link" tabindex="-1" aria-hidden="true">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php
                the_post_thumbnail(
                    'card-thumbnail',
                    array(
                        'class'   => 'article-card__image',
                        'loading' => 'lazy',
                        'alt'     => esc_attr( get_the_title() ),
                    )
                );
                ?>
            <?php else : ?>
            <div class="article-card__image-placeholder" aria-hidden="true">
                <span class="placeholder-icon">&#9632;</span>
            </div>
            <?php endif; ?>
        </a>

        <!-- Category Badge (overlaid on image) -->
        <?php if ( $primary_cat ) : ?>
        <a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>"
           class="article-card__cat-badge <?php echo esc_attr( $cat_color_class ); ?>"
           aria-label="<?php /* translators: %s: category name */ printf( esc_attr__( 'Category: %s', 'nest-and-well' ), esc_attr( $primary_cat->name ) ); ?>">
            <?php echo esc_html( $primary_cat->name ); ?>
        </a>
        <?php endif; ?>

        <!-- Review Badge if set -->
        <?php if ( $review_badge ) : ?>
        <span class="article-card__review-badge badge badge--<?php echo esc_attr( $review_badge ); ?>">
            <?php echo esc_html( nest_well_get_badge_label( $review_badge ) ); ?>
        </span>
        <?php endif; ?>
    </div><!-- .article-card__image-wrap -->

    <!-- Card Body -->
    <div class="article-card__body">

        <!-- Title -->
        <h3 class="article-card__title">
            <a href="<?php the_permalink(); ?>" class="article-card__title-link">
                <?php the_title(); ?>
            </a>
        </h3>

        <!-- Excerpt -->
        <p class="article-card__excerpt">
            <?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '&hellip;' ) ); ?>
        </p>

        <!-- Meta Row -->
        <div class="article-card__meta">
            <span class="article-card__author"><?php the_author(); ?></span>
            <span class="article-card__sep" aria-hidden="true">&middot;</span>
            <time class="article-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_date() ); ?>
            </time>
            <?php if ( $read_time ) : ?>
            <span class="article-card__sep" aria-hidden="true">&middot;</span>
            <span class="article-card__read-time">
                <?php
                printf(
                    /* translators: %d: minutes */
                    esc_html( _n( '%d min', '%d min', $read_time, 'nest-and-well' ) ),
                    (int) $read_time
                );
                ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- Bottom Row: Rating + Read Link -->
        <div class="article-card__bottom">
            <div class="article-card__rating">
                <?php if ( $review_score ) : ?>
                <?php echo wp_kses_post( nest_well_star_rating_html( $review_score ) ); ?>
                <span class="article-card__score"><?php echo esc_html( $review_score ); ?>/10</span>
                <?php endif; ?>
            </div>
            <a href="<?php the_permalink(); ?>" class="article-card__read-link">
                <?php esc_html_e( 'Read Review', 'nest-and-well' ); ?> &rarr;
            </a>
        </div>

    </div><!-- .article-card__body -->

</article>
