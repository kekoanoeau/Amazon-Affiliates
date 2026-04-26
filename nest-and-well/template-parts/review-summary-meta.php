<?php
/**
 * Auto-rendered Review Summary card.
 *
 * Builds the review summary card from post meta when `_review_score` is set,
 * so authors don't have to paste the [review_summary] shortcode manually.
 * Pulls: _review_score, _review_badge, _product_name, _product_asin,
 *        _product_price, plus the post excerpt as verdict fallback.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$post_id = get_the_ID();
$score   = get_post_meta( $post_id, '_review_score', true );
if ( ! $score ) {
    return;
}

// Skip if author has already manually placed the shortcode in content.
$content = get_post_field( 'post_content', $post_id );
if ( has_shortcode( $content, 'review_summary' ) ) {
    return;
}

$badge        = get_post_meta( $post_id, '_review_badge', true );
$product_name = get_post_meta( $post_id, '_product_name', true );
$product_asin = get_post_meta( $post_id, '_product_asin', true );
$price        = get_post_meta( $post_id, '_product_price', true );
$pros_meta    = get_post_meta( $post_id, '_review_pros', true );
$cons_meta    = get_post_meta( $post_id, '_review_cons', true );
$verdict      = get_post_meta( $post_id, '_review_verdict', true );
if ( ! $verdict ) {
    $verdict = get_the_excerpt( $post_id );
}

$pros = $pros_meta ? array_filter( array_map( 'trim', explode( "\n", $pros_meta ) ) ) : array();
$cons = $cons_meta ? array_filter( array_map( 'trim', explode( "\n", $cons_meta ) ) ) : array();

$buy_url = $product_asin ? nest_well_amazon_url( $product_asin ) : '';
?>
<aside class="review-summary review-summary--auto" aria-label="<?php esc_attr_e( 'Review summary', 'nest-and-well' ); ?>">

    <div class="review-summary__header">
        <?php if ( $badge ) : ?>
        <span class="badge badge--<?php echo esc_attr( $badge ); ?>">
            <?php echo esc_html( nest_well_get_badge_label( $badge ) ); ?>
        </span>
        <?php endif; ?>

        <?php if ( $product_name ) : ?>
        <h2 class="review-summary__title"><?php echo esc_html( $product_name ); ?></h2>
        <?php endif; ?>

        <div class="review-summary__score-wrap">
            <div class="review-summary__score">
                <?php echo esc_html( number_format( (float) $score, 1 ) ); ?><span>/10</span>
            </div>
            <?php echo wp_kses_post( nest_well_star_rating_html( (float) $score ) ); ?>
        </div>
    </div>

    <?php if ( ! empty( $pros ) || ! empty( $cons ) ) : ?>
    <div class="review-summary__body">
        <?php if ( ! empty( $pros ) ) : ?>
        <div class="review-summary__pros">
            <h4><?php esc_html_e( 'Pros', 'nest-and-well' ); ?></h4>
            <ul>
                <?php foreach ( $pros as $pro ) : ?>
                <li><span class="pros-check" aria-hidden="true">&#10003;</span> <?php echo esc_html( $pro ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if ( ! empty( $cons ) ) : ?>
        <div class="review-summary__cons">
            <h4><?php esc_html_e( 'Cons', 'nest-and-well' ); ?></h4>
            <ul>
                <?php foreach ( $cons as $con ) : ?>
                <li><span class="cons-dash" aria-hidden="true">&#8722;</span> <?php echo esc_html( $con ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ( $verdict ) : ?>
    <div class="review-summary__verdict">
        <strong><?php esc_html_e( 'Verdict:', 'nest-and-well' ); ?></strong>
        <?php echo wp_kses_post( $verdict ); ?>
    </div>
    <?php endif; ?>

    <?php if ( $price || $buy_url ) : ?>
    <div class="review-summary__cta-row">
        <?php if ( $price ) : ?>
        <div class="review-summary__price">
            <span class="price-label"><?php esc_html_e( 'From', 'nest-and-well' ); ?></span>
            <span class="price-value"><?php echo esc_html( $price ); ?></span>
        </div>
        <?php endif; ?>

        <?php if ( $buy_url ) : ?>
        <a href="<?php echo esc_url( $buy_url ); ?>"
           class="btn btn--sage review-summary__cta"
           target="_blank"
           rel="nofollow noopener sponsored"
           data-affiliate="amazon"
           data-product="<?php echo esc_attr( $product_name ); ?>">
            <?php esc_html_e( 'Check Price on Amazon', 'nest-and-well' ); ?>
            <span aria-hidden="true">&rarr;</span>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</aside>
