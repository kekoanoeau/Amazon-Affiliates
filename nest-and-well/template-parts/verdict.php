<?php
/**
 * "Bottom Line" Verdict Closer
 *
 * Renders at the end of review posts using the same `_review_verdict` and
 * `_review_score` meta the auto review-summary card uses. Provides readers
 * with a clear takeaway + a final affiliate CTA — the conversion-critical
 * close most successful review sites have at the end of the body.
 *
 * Skipped if no verdict was supplied (blank meta).
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$post_id = get_the_ID();
$verdict = get_post_meta( $post_id, '_review_verdict', true );
if ( ! $verdict ) {
    return;
}

$score        = get_post_meta( $post_id, '_review_score', true );
$badge        = get_post_meta( $post_id, '_review_badge', true );
$product_name = get_post_meta( $post_id, '_product_name', true );
$product_asin = get_post_meta( $post_id, '_product_asin', true );
$price        = get_post_meta( $post_id, '_product_price', true );

$buy_url = $product_asin ? nest_well_amazon_url( $product_asin ) : '';
?>
<section class="verdict" aria-labelledby="verdict-title">
    <div class="verdict__inner">
        <div class="verdict__header">
            <p class="verdict__eyebrow"><?php esc_html_e( 'The Bottom Line', 'nest-and-well' ); ?></p>
            <h2 id="verdict-title" class="verdict__title">
                <?php
                if ( $product_name ) {
                    /* translators: %s: product name */
                    printf( esc_html__( 'Should you buy the %s?', 'nest-and-well' ), esc_html( $product_name ) );
                } else {
                    esc_html_e( 'Our verdict', 'nest-and-well' );
                }
                ?>
            </h2>
        </div>

        <div class="verdict__body">
            <?php if ( $score || $badge ) : ?>
            <div class="verdict__score-col">
                <?php if ( $score ) :
                    $score_pct = max( 0, min( 100, (float) $score * 10 ) );
                    ?>
                <div class="verdict__score-ring" data-score-target="<?php echo esc_attr( number_format( (float) $score, 1, '.', '' ) ); ?>" data-score-pct="<?php echo esc_attr( $score_pct ); ?>" role="img" aria-label="<?php echo esc_attr( sprintf( __( '%s out of 10', 'nest-and-well' ), number_format( (float) $score, 1 ) ) ); ?>">
                    <svg class="verdict__ring-svg" viewBox="0 0 100 100" aria-hidden="true">
                        <circle class="verdict__ring-track" cx="50" cy="50" r="44" fill="none" stroke-width="6"></circle>
                        <circle class="verdict__ring-fill" cx="50" cy="50" r="44" fill="none" stroke-width="6"
                                stroke-dasharray="276.46" stroke-dashoffset="276.46"
                                pathLength="100" transform="rotate(-90 50 50)"></circle>
                    </svg>
                    <div class="verdict__score">
                        <span class="verdict__score-value nw-num"><?php echo esc_html( number_format( (float) $score, 1 ) ); ?></span>
                        <span class="verdict__score-out nw-num">/10</span>
                    </div>
                </div>
                <?php echo wp_kses_post( nest_well_star_rating_html( (float) $score ) ); ?>
                <?php endif; ?>
                <?php if ( $badge ) : ?>
                <span class="badge badge--<?php echo esc_attr( $badge ); ?>">
                    <?php echo esc_html( nest_well_get_badge_label( $badge ) ); ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="verdict__copy">
                <div class="verdict__text"><?php echo wp_kses_post( wpautop( $verdict ) ); ?></div>

                <?php if ( $buy_url ) : ?>
                <div class="verdict__cta-row">
                    <?php if ( $price ) : ?>
                    <span class="verdict__price">
                        <span class="price-label"><?php esc_html_e( 'Today', 'nest-and-well' ); ?></span>
                        <span class="price-value"><?php echo esc_html( $price ); ?></span>
                    </span>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( $buy_url ); ?>"
                       class="btn btn--sage verdict__cta"
                       target="_blank"
                       rel="nofollow noopener sponsored"
                       data-affiliate="amazon"
                       data-product="<?php echo esc_attr( $product_name ); ?>">
                        <?php
                        if ( $product_name ) {
                            /* translators: %s: product name */
                            printf( esc_html__( 'Check the %s on Amazon', 'nest-and-well' ), esc_html( $product_name ) );
                        } else {
                            esc_html_e( 'Check price on Amazon', 'nest-and-well' );
                        }
                        ?>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
