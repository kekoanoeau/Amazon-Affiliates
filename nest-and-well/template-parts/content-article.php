<?php
/**
 * Article Card Template Part
 * Used in homepage grid, archives, related posts, and search results.
 *
 * Card structure:
 *   1. Featured Image (full card width, outside body)
 *   2. Title
 *   3. Excerpt
 *   4. Rating + "CHECK IT OUT" / Save actions
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$post_id      = get_the_ID();
$review_score = (float) get_post_meta( $post_id, '_review_score', true );
$review_badge = get_post_meta( $post_id, '_review_badge', true );

// Card variant classes
$card_classes = array( 'article-card' );
if ( 'editors-choice' === $review_badge ) {
    $card_classes[] = 'article-card--editors-choice';
} elseif ( 'best-value' === $review_badge ) {
    $card_classes[] = 'article-card--best-value';
}
?>

<article <?php post_class( $card_classes ); ?> id="post-<?php the_ID(); ?>">

    <!-- 1. Featured Image (full card width, outside body padding) -->
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
            <div class="article-card__image-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </a>

        <?php if ( $review_badge ) : ?>
        <span class="article-card__review-badge badge badge--<?php echo esc_attr( $review_badge ); ?>">
            <?php echo esc_html( nest_well_get_badge_label( $review_badge ) ); ?>
        </span>
        <?php endif; ?>
    </div><!-- .article-card__image-wrap -->

    <div class="article-card__body">

        <!-- 2. Title -->
        <h3 class="article-card__title">
            <a href="<?php the_permalink(); ?>" class="article-card__title-link">
                <?php the_title(); ?>
            </a>
        </h3>

        <!-- 3. Excerpt -->
        <p class="article-card__excerpt">
            <?php echo esc_html( wp_trim_words( get_the_excerpt(), 30, '&hellip;' ) ); ?>
        </p>

        <!-- 4. Rating + Actions -->
        <div class="article-card__bottom">

            <!-- Far left: Social share -->
            <div class="share-buttons article-card__share" aria-label="<?php esc_attr_e( 'Share this article', 'nest-and-well' ); ?>">
                <button type="button"
                        class="share-buttons__item share-buttons__item--native js-native-share"
                        data-share-url="<?php echo esc_attr( get_permalink() ); ?>"
                        data-share-title="<?php echo esc_attr( get_the_title() ); ?>"
                        aria-label="<?php esc_attr_e( 'Share', 'nest-and-well' ); ?>"
                        hidden>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                    </svg>
                </button>

                <a href="https://pinterest.com/pin/create/button/?url=<?php echo rawurlencode( get_permalink() ); ?>&description=<?php echo rawurlencode( get_the_title() ); ?>"
                   class="share-buttons__item share-buttons__item--pinterest"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="<?php esc_attr_e( 'Share on Pinterest', 'nest-and-well' ); ?>">P</a>

                <a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>"
                   class="share-buttons__item share-buttons__item--twitter"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="<?php esc_attr_e( 'Share on X', 'nest-and-well' ); ?>">X</a>

                <button class="share-buttons__item share-buttons__item--copy"
                        data-copy-url="<?php echo esc_attr( get_permalink() ); ?>"
                        aria-label="<?php esc_attr_e( 'Copy link', 'nest-and-well' ); ?>">
                    <?php esc_html_e( 'Copy', 'nest-and-well' ); ?>
                </button>

                <?php nest_well_save_button( $post_id, 'icon' ); ?>
            </div>

            <!-- Middle: Star rating (when present) -->
            <?php if ( $review_score ) : ?>
            <div class="article-card__rating">
                <?php echo wp_kses_post( nest_well_star_rating_html( (float) $review_score ) ); ?>
                <span class="article-card__score"><?php echo esc_html( number_format( (float) $review_score, 1 ) ); ?>/10</span>
            </div>
            <?php endif; ?>

            <!-- Far right: CTA -->
            <a href="<?php the_permalink(); ?>" class="article-card__cta btn btn--primary">
                <?php esc_html_e( 'CHECK IT OUT', 'nest-and-well' ); ?>
            </a>

        </div>

    </div><!-- .article-card__body -->

</article>
