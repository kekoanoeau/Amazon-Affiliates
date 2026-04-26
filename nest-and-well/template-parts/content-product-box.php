<?php
/**
 * Amazon Product Box Template Part
 * Renders the product recommendation box component.
 * Used directly in templates when PHP variables are preferred over shortcode attributes.
 *
 * Expected variables (passed via set_query_var or direct $args):
 *   $args['title']        — Product name (string)
 *   $args['image']        — Product image URL (string)
 *   $args['price']        — Price string, e.g. "$299" (string)
 *   $args['rating']       — Score out of 10 (float)
 *   $args['badge']        — Badge slug (string): editors-choice|best-value|etc.
 *   $args['pros']         — Array of pros (array)
 *   $args['cons']         — Array of cons (array)
 *   $args['asin']         — Amazon ASIN (string)
 *   $args['link']         — Direct affiliate URL override (string)
 *   $args['review_count'] — Review count string, e.g. "2,400+" (string)
 *   $args['button_text']  — CTA button text (string)
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Support both get_template_part() $args and direct $args variable
if ( ! isset( $args ) ) {
    $args = array();
}

$defaults = array(
    'title'        => '',
    'image'        => '',
    'price'        => '',
    'rating'       => '',
    'badge'        => '',
    'pros'         => array(),
    'cons'         => array(),
    'asin'         => '',
    'link'         => '',
    'review_count' => '',
    'button_text'  => __( 'Check Price on Amazon', 'nest-and-well' ),
);

$args = wp_parse_args( $args, $defaults );

// Build product URL
$product_url = '';
if ( $args['asin'] ) {
    $product_url = nest_well_amazon_url( $args['asin'] );
} elseif ( $args['link'] ) {
    $product_url = $args['link'];
}

$badge_class       = $args['badge'] ? 'product-box--' . sanitize_html_class( $args['badge'] ) : '';
$is_editors_choice = ( 'editors-choice' === $args['badge'] );

// Parse pros/cons from string if needed
if ( is_string( $args['pros'] ) ) {
    $args['pros'] = array_filter( array_map( 'trim', explode( ',', $args['pros'] ) ) );
}
if ( is_string( $args['cons'] ) ) {
    $args['cons'] = array_filter( array_map( 'trim', explode( ',', $args['cons'] ) ) );
}
?>

<div class="product-box <?php echo esc_attr( $badge_class ); ?>" itemscope itemtype="https://schema.org/Product">
    <!-- Top accent bar (amber for Editor's Choice, sage otherwise) -->
    <div class="product-box__accent-bar <?php echo $is_editors_choice ? 'product-box__accent-bar--amber' : ''; ?>"></div>

    <div class="product-box__inner">

        <?php if ( $args['image'] ) : ?>
        <div class="product-box__image">
            <img src="<?php echo esc_url( $args['image'] ); ?>"
                 alt="<?php echo esc_attr( $args['title'] ); ?>"
                 loading="lazy"
                 itemprop="image">
        </div>
        <?php endif; ?>

        <div class="product-box__details">

            <?php if ( $args['badge'] ) : ?>
            <div class="product-box__badge badge badge--<?php echo esc_attr( $args['badge'] ); ?>">
                <?php echo esc_html( nest_well_get_badge_label( $args['badge'] ) ); ?>
            </div>
            <?php endif; ?>

            <?php if ( $args['title'] ) : ?>
            <h3 class="product-box__title" itemprop="name"><?php echo esc_html( $args['title'] ); ?></h3>
            <?php endif; ?>

            <?php if ( $args['rating'] ) : ?>
            <div class="product-box__score-row" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                <meta itemprop="ratingValue" content="<?php echo esc_attr( $args['rating'] ); ?>">
                <meta itemprop="bestRating" content="10">
                <span class="product-box__score-badge nw-num" data-score-target="<?php echo esc_attr( number_format( (float) $args['rating'], 1, '.', '' ) ); ?>">
                    <span class="nw-score-num"><?php echo esc_html( $args['rating'] ); ?></span>/10
                </span>
                <?php echo wp_kses_post( nest_well_star_rating_html( $args['rating'] ) ); ?>
                <?php if ( $args['review_count'] ) : ?>
                <span class="product-box__review-count">(<?php echo esc_html( $args['review_count'] ); ?>+ reviews)</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $args['pros'] ) ) : ?>
            <ul class="product-box__pros" aria-label="<?php esc_attr_e( 'Pros', 'nest-and-well' ); ?>">
                <?php foreach ( array_slice( $args['pros'], 0, 3 ) as $pro ) : ?>
                <li class="product-box__pros-item">
                    <span class="pros-check" aria-hidden="true">&#10003;</span>
                    <?php echo esc_html( $pro ); ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php if ( ! empty( $args['cons'] ) ) : ?>
            <ul class="product-box__cons" aria-label="<?php esc_attr_e( 'Cons', 'nest-and-well' ); ?>">
                <?php foreach ( $args['cons'] as $con ) : ?>
                <li class="product-box__cons-item">
                    <span class="cons-dash" aria-hidden="true">&#8722;</span>
                    <?php echo esc_html( $con ); ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php if ( $args['price'] ) : ?>
            <div class="product-box__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                <span class="product-box__price-label"><?php esc_html_e( 'From', 'nest-and-well' ); ?></span>
                <span class="product-box__price-value" itemprop="price" content="<?php echo esc_attr( preg_replace( '/[^0-9.]/', '', $args['price'] ) ); ?>">
                    <?php echo esc_html( $args['price'] ); ?>
                </span>
                <meta itemprop="priceCurrency" content="USD">
            </div>
            <?php endif; ?>

            <?php if ( $product_url ) : ?>
            <a href="<?php echo esc_url( $product_url ); ?>"
               class="product-box__cta btn btn--amazon"
               target="_blank"
               rel="nofollow noopener sponsored"
               data-affiliate="amazon"
               data-product="<?php echo esc_attr( $args['title'] ); ?>">
                <?php echo esc_html( $args['button_text'] ); ?>
                <span class="btn-arrow" aria-hidden="true">&rarr;</span>
            </a>
            <p class="product-box__disclaimer">
                <?php
                printf(
                    /* translators: %s: date */
                    esc_html__( 'Price last checked %s. We earn a commission from Amazon purchases.', 'nest-and-well' ),
                    esc_html( wp_date( 'F j, Y' ) )
                );
                ?>
            </p>
            <?php endif; ?>

        </div><!-- .product-box__details -->
    </div><!-- .product-box__inner -->
</div><!-- .product-box -->
