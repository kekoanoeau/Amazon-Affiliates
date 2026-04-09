<?php
/**
 * Single Article Content Template Part
 * Renders the main body content area for single posts.
 * Used within single.php's content-area column.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="single-content">

    <!-- Review Summary (if post has a review score) -->
    <?php
    $review_score = get_post_meta( get_the_ID(), '_review_score', true );
    $review_badge = get_post_meta( get_the_ID(), '_review_badge', true );
    $product_name = get_post_meta( get_the_ID(), '_product_name', true );
    $product_asin = get_post_meta( get_the_ID(), '_product_asin', true );
    $product_price = get_post_meta( get_the_ID(), '_product_price', true );

    if ( $review_score && $product_name ) : ?>
    <div class="single-review-summary">
        <div class="single-review-summary__header">
            <?php if ( $review_badge ) : ?>
            <span class="badge badge--<?php echo esc_attr( $review_badge ); ?>">
                <?php echo esc_html( nest_well_get_badge_label( $review_badge ) ); ?>
            </span>
            <?php endif; ?>
            <h2 class="single-review-summary__product"><?php echo esc_html( $product_name ); ?></h2>
            <div class="single-review-summary__score-row">
                <div class="single-review-summary__score"><?php echo esc_html( $review_score ); ?><span>/10</span></div>
                <?php echo wp_kses_post( nest_well_star_rating_html( $review_score ) ); ?>
            </div>
        </div>
        <?php if ( $product_price || $product_asin ) : ?>
        <div class="single-review-summary__buy">
            <?php if ( $product_price ) : ?>
            <span class="single-review-summary__price">From <?php echo esc_html( $product_price ); ?></span>
            <?php endif; ?>
            <?php if ( $product_asin ) : ?>
            <a href="<?php echo esc_url( nest_well_amazon_url( $product_asin ) ); ?>"
               class="btn btn--sage"
               target="_blank"
               rel="nofollow noopener sponsored">
                <?php esc_html_e( 'Check Price on Amazon', 'nest-and-well' ); ?> &rarr;
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Main Article Content -->
    <div class="entry-content single-content__body">
        <?php the_content(); ?>
    </div>

</div><!-- .single-content -->
