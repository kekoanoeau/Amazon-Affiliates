<?php
/**
 * "How We Review" trust block.
 *
 * Renders on review posts (posts with a `_review_score` meta) directly under
 * the article header to communicate editorial credibility.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$how_page = get_page_by_path( 'how-we-review' );
$how_url  = $how_page ? get_permalink( $how_page->ID ) : '';
?>
<aside class="how-we-review" aria-label="<?php esc_attr_e( 'How we test and review', 'nest-and-well' ); ?>">
    <div class="how-we-review__inner">
        <span class="how-we-review__badge" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
            </svg>
        </span>
        <p class="how-we-review__text">
            <strong><?php esc_html_e( 'Independently reviewed.', 'nest-and-well' ); ?></strong>
            <?php esc_html_e( 'We bought this with our own money, tested it in a real home, and rated it against the best alternatives we\'ve used.', 'nest-and-well' ); ?>
            <?php if ( $how_url ) : ?>
            <a href="<?php echo esc_url( $how_url ); ?>" class="how-we-review__link">
                <?php esc_html_e( 'How we review', 'nest-and-well' ); ?> &rarr;
            </a>
            <?php endif; ?>
        </p>
    </div>
</aside>
