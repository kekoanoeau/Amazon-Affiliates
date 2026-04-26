<?php
/**
 * Auto-rendered FAQ Accordion (from post meta).
 *
 * Reads the structured FAQ items saved via the FAQ meta box
 * (inc/faq-meta.php) and renders an accessible accordion. Schema is emitted
 * separately by inc/seo-helpers.php through the shared FAQ collector.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$faqs = nest_well_get_post_faqs( get_the_ID() );
if ( empty( $faqs ) ) {
    return;
}
?>
<section class="article-faqs" aria-labelledby="article-faqs-title">
    <h2 id="article-faqs-title" class="article-faqs__title">
        <?php esc_html_e( 'Frequently asked questions', 'nest-and-well' ); ?>
    </h2>

    <?php foreach ( $faqs as $i => $item ) :
        $faq_id = 'meta-faq-' . $i;
        ?>
    <div class="faq-item">
        <button class="faq-item__question"
                aria-expanded="false"
                aria-controls="<?php echo esc_attr( $faq_id ); ?>"
                type="button">
            <span><?php echo esc_html( $item['q'] ); ?></span>
            <span class="faq-item__icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-item__answer" id="<?php echo esc_attr( $faq_id ); ?>" hidden>
            <div><?php echo wp_kses_post( wpautop( $item['a'] ) ); ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</section>
