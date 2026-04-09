<?php
/**
 * Affiliate Disclosure Banner (Template Part)
 * FTC-compliant affiliate disclosure displayed on relevant pages.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$disclosure_text   = get_theme_mod(
    'nest_well_disclosure_text',
    'We independently review everything we recommend. We may earn a commission if you buy through our links — at no extra cost to you.'
);

$disclosure_page   = get_page_by_path( 'affiliate-disclosure' );
$disclosure_url    = $disclosure_page ? get_permalink( $disclosure_page->ID ) : '';
?>
<div class="affiliate-banner" role="note" aria-label="<?php esc_attr_e( 'Affiliate Disclosure', 'nest-and-well' ); ?>">
    <div class="container">
        <p class="affiliate-banner__text">
            <?php echo wp_kses_post( $disclosure_text ); ?>
            <?php if ( $disclosure_url ) : ?>
            <a href="<?php echo esc_url( $disclosure_url ); ?>" class="affiliate-banner__link">
                <?php esc_html_e( 'Learn more', 'nest-and-well' ); ?> &rarr;
            </a>
            <?php endif; ?>
        </p>
    </div>
</div>
