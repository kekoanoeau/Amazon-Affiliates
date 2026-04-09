<?php
/**
 * Shortcodes for Nest & Well
 * All affiliate, review, and content shortcodes.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// ============================================================
// 1. [product_box] — Amazon Product Recommendation Box
// ============================================================

/**
 * Product box shortcode.
 *
 * Usage: [product_box title="Product Name" image="https://..." price="$299"
 *         rating="9.4" prime="yes" badge="editors-choice"
 *         pros="Pros item 1,Pros item 2,Pros item 3"
 *         cons="Con item 1,Con item 2"
 *         link="https://amazon.com/dp/ASIN?tag=xxx"
 *         asin="B0XXXXXX"]
 *
 * @param array $atts Shortcode attributes.
 * @return string Product box HTML.
 */
function nest_well_product_box_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'title'         => '',
            'image'         => '',
            'price'         => '',
            'rating'        => '',
            'prime'         => 'no',
            'badge'         => '',
            'pros'          => '',
            'cons'          => '',
            'link'          => '',
            'asin'          => '',
            'review_count'  => '',
            'button_text'   => 'Check Price on Amazon',
        ),
        $atts,
        'product_box'
    );

    // Build the Amazon link
    $product_url = '';
    if ( $atts['asin'] ) {
        $product_url = nest_well_amazon_url( $atts['asin'] );
    } elseif ( $atts['link'] ) {
        $product_url = $atts['link'];
    }

    // Parse pros/cons
    $pros = array_filter( array_map( 'trim', explode( ',', $atts['pros'] ) ) );
    $cons = array_filter( array_map( 'trim', explode( ',', $atts['cons'] ) ) );

    // Badge class
    $badge_class = $atts['badge'] ? 'product-box--' . sanitize_html_class( $atts['badge'] ) : '';
    $is_editors_choice = ( 'editors-choice' === $atts['badge'] );

    ob_start();
    ?>
    <div class="product-box <?php echo esc_attr( $badge_class ); ?>" itemscope itemtype="https://schema.org/Product">
        <div class="product-box__accent-bar"></div>
        <div class="product-box__inner">

            <?php if ( $atts['image'] ) : ?>
            <div class="product-box__image">
                <img src="<?php echo esc_url( $atts['image'] ); ?>"
                     alt="<?php echo esc_attr( $atts['title'] ); ?>"
                     loading="lazy"
                     itemprop="image">
            </div>
            <?php endif; ?>

            <div class="product-box__details">

                <?php if ( $atts['badge'] ) : ?>
                <div class="product-box__badge badge badge--<?php echo esc_attr( $atts['badge'] ); ?>">
                    <?php echo esc_html( nest_well_get_badge_label( $atts['badge'] ) ); ?>
                </div>
                <?php endif; ?>

                <?php if ( $atts['title'] ) : ?>
                <h3 class="product-box__title" itemprop="name"><?php echo esc_html( $atts['title'] ); ?></h3>
                <?php endif; ?>

                <?php if ( $atts['rating'] ) : ?>
                <div class="product-box__score-row">
                    <span class="product-box__score-badge" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                        <meta itemprop="ratingValue" content="<?php echo esc_attr( $atts['rating'] ); ?>">
                        <meta itemprop="bestRating" content="10">
                        <?php echo esc_html( $atts['rating'] ); ?>/10
                    </span>
                    <?php echo wp_kses_post( nest_well_star_rating_html( $atts['rating'] ) ); ?>
                    <?php if ( $atts['review_count'] ) : ?>
                    <span class="product-box__review-count">(<?php echo esc_html( $atts['review_count'] ); ?>+ reviews)</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $pros ) ) : ?>
                <ul class="product-box__pros">
                    <?php foreach ( array_slice( $pros, 0, 3 ) as $pro ) : ?>
                    <li class="product-box__pros-item">
                        <span class="pros-check" aria-hidden="true">&#10003;</span>
                        <?php echo esc_html( $pro ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <?php if ( ! empty( $cons ) ) : ?>
                <ul class="product-box__cons">
                    <?php foreach ( $cons as $con ) : ?>
                    <li class="product-box__cons-item">
                        <span class="cons-dash" aria-hidden="true">&#8722;</span>
                        <?php echo esc_html( $con ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <?php if ( $atts['price'] ) : ?>
                <div class="product-box__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                    <span itemprop="price" content="<?php echo esc_attr( preg_replace( '/[^0-9.]/', '', $atts['price'] ) ); ?>">
                        From <?php echo esc_html( $atts['price'] ); ?>
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
                   data-product="<?php echo esc_attr( $atts['title'] ); ?>">
                    <?php echo esc_html( $atts['button_text'] ); ?>
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
    <?php
    return ob_get_clean();
}
add_shortcode( 'product_box', 'nest_well_product_box_shortcode' );

// ============================================================
// 2. [rating] — Score Badge Component
// ============================================================

/**
 * Rating shortcode.
 *
 * Usage: [rating score="9.4" label="Editor's Choice"]
 *
 * @param array $atts Shortcode attributes.
 * @return string Rating HTML.
 */
function nest_well_rating_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'score' => '',
            'label' => '',
        ),
        $atts,
        'rating'
    );

    if ( empty( $atts['score'] ) ) {
        return '';
    }

    $score = (float) $atts['score'];

    ob_start();
    ?>
    <span class="rating-badge">
        <span class="rating-badge__score"><?php echo esc_html( number_format( $score, 1 ) ); ?>/10</span>
        <?php if ( $atts['label'] ) : ?>
        <span class="rating-badge__label"><?php echo esc_html( $atts['label'] ); ?></span>
        <?php endif; ?>
        <?php echo wp_kses_post( nest_well_star_rating_html( $score ) ); ?>
    </span>
    <?php
    return ob_get_clean();
}
add_shortcode( 'rating', 'nest_well_rating_shortcode' );

// ============================================================
// 3. [badge] — Badge Pill Component
// ============================================================

/**
 * Badge shortcode.
 *
 * Usage: [badge type="editors-choice"]
 * Types: editors-choice | best-value | budget-pick | premium-pick | staff-favorite
 *
 * @param array $atts Shortcode attributes.
 * @return string Badge HTML.
 */
function nest_well_badge_shortcode( $atts ) {
    $atts = shortcode_atts(
        array( 'type' => 'editors-choice' ),
        $atts,
        'badge'
    );

    $label = nest_well_get_badge_label( $atts['type'] );
    if ( ! $label ) {
        return '';
    }

    return sprintf(
        '<span class="badge badge--%s">%s</span>',
        esc_attr( $atts['type'] ),
        esc_html( $label )
    );
}
add_shortcode( 'badge', 'nest_well_badge_shortcode' );

// ============================================================
// 4. [pros_cons] — Two-Column Pros/Cons Box
// ============================================================

/**
 * Pros/Cons shortcode.
 *
 * Usage: [pros_cons pros="item1,item2,item3" cons="item1,item2"]
 *
 * @param array $atts Shortcode attributes.
 * @return string Pros/Cons HTML.
 */
function nest_well_pros_cons_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'pros' => '',
            'cons' => '',
        ),
        $atts,
        'pros_cons'
    );

    $pros = array_filter( array_map( 'trim', explode( ',', $atts['pros'] ) ) );
    $cons = array_filter( array_map( 'trim', explode( ',', $atts['cons'] ) ) );

    if ( empty( $pros ) && empty( $cons ) ) {
        return '';
    }

    ob_start();
    ?>
    <div class="pros-cons-box">
        <?php if ( ! empty( $pros ) ) : ?>
        <div class="pros-cons-box__pros">
            <h4 class="pros-cons-box__heading pros-cons-box__heading--pros">
                <?php esc_html_e( 'Pros', 'nest-and-well' ); ?>
            </h4>
            <ul class="pros-cons-box__list">
                <?php foreach ( $pros as $pro ) : ?>
                <li class="pros-cons-box__item pros-cons-box__item--pro">
                    <span class="pros-check" aria-hidden="true">&#10003;</span>
                    <?php echo esc_html( $pro ); ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if ( ! empty( $cons ) ) : ?>
        <div class="pros-cons-box__cons">
            <h4 class="pros-cons-box__heading pros-cons-box__heading--cons">
                <?php esc_html_e( 'Cons', 'nest-and-well' ); ?>
            </h4>
            <ul class="pros-cons-box__list">
                <?php foreach ( $cons as $con ) : ?>
                <li class="pros-cons-box__item pros-cons-box__item--con">
                    <span class="cons-dash" aria-hidden="true">&#8722;</span>
                    <?php echo esc_html( $con ); ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'pros_cons', 'nest_well_pros_cons_shortcode' );

// ============================================================
// 5. [affiliate_disclosure] — Inline FTC Disclosure
// ============================================================

/**
 * Affiliate disclosure shortcode.
 *
 * Usage: [affiliate_disclosure]
 *
 * @param array $atts Shortcode attributes (unused).
 * @return string Disclosure HTML.
 */
function nest_well_affiliate_disclosure_shortcode( $atts ) {
    return nest_well_get_disclosure_html( true );
}
add_shortcode( 'affiliate_disclosure', 'nest_well_affiliate_disclosure_shortcode' );

// ============================================================
// 6. [comparison_table] — Product Comparison Table
// ============================================================

/**
 * Comparison table shortcode (container — accepts [comparison_row] as content).
 *
 * Usage:
 * [comparison_table]
 *   [comparison_row name="Product A" score="9.4" price="$299" feature="Best range" link="https://..."]
 *   [comparison_row name="Product B" score="8.8" price="$199" feature="Budget choice" link="https://..."]
 * [/comparison_table]
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Inner content with comparison_row shortcodes.
 * @return string Comparison table HTML.
 */
function nest_well_comparison_table_shortcode( $atts, $content = '' ) {
    $atts = shortcode_atts(
        array(
            'title' => '',
        ),
        $atts,
        'comparison_table'
    );

    // Parse inner [comparison_row] shortcodes
    $rows = array();
    preg_match_all( '/\[comparison_row([^\]]*)\]/i', $content, $matches );

    if ( ! empty( $matches[1] ) ) {
        foreach ( $matches[1] as $row_attrs_str ) {
            $row_atts = shortcode_parse_atts( $row_attrs_str );
            $row_atts = wp_parse_args(
                $row_atts,
                array(
                    'name'    => '',
                    'score'   => '',
                    'price'   => '',
                    'feature' => '',
                    'link'    => '',
                    'badge'   => '',
                )
            );
            $rows[] = $row_atts;
        }
    }

    if ( empty( $rows ) ) {
        return '';
    }

    ob_start();
    ?>
    <div class="comparison-table-wrap">
        <?php if ( $atts['title'] ) : ?>
        <h3 class="comparison-table__title"><?php echo esc_html( $atts['title'] ); ?></h3>
        <?php endif; ?>
        <div class="comparison-table-scroll">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Product', 'nest-and-well' ); ?></th>
                        <th><?php esc_html_e( 'Score', 'nest-and-well' ); ?></th>
                        <th><?php esc_html_e( 'Price', 'nest-and-well' ); ?></th>
                        <th><?php esc_html_e( 'Best For', 'nest-and-well' ); ?></th>
                        <th><?php esc_html_e( 'Action', 'nest-and-well' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $rows as $row ) : ?>
                    <tr>
                        <td class="comparison-table__name">
                            <?php if ( $row['badge'] ) : ?>
                            <span class="badge badge--<?php echo esc_attr( $row['badge'] ); ?>">
                                <?php echo esc_html( nest_well_get_badge_label( $row['badge'] ) ); ?>
                            </span>
                            <?php endif; ?>
                            <?php echo esc_html( $row['name'] ); ?>
                        </td>
                        <td class="comparison-table__score">
                            <?php if ( $row['score'] ) : ?>
                            <span class="score-pill"><?php echo esc_html( $row['score'] ); ?>/10</span>
                            <?php endif; ?>
                        </td>
                        <td class="comparison-table__price"><?php echo esc_html( $row['price'] ); ?></td>
                        <td class="comparison-table__feature"><?php echo esc_html( $row['feature'] ); ?></td>
                        <td class="comparison-table__action">
                            <?php if ( $row['link'] ) : ?>
                            <a href="<?php echo esc_url( $row['link'] ); ?>"
                               class="btn btn--sage comparison-table__btn"
                               target="_blank"
                               rel="nofollow noopener sponsored">
                                <?php esc_html_e( 'Check Price', 'nest-and-well' ); ?>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'comparison_table', 'nest_well_comparison_table_shortcode' );

// Register companion shortcode (processed within comparison_table content)
add_shortcode( 'comparison_row', '__return_empty_string' );

// ============================================================
// 7. [quick_picks] — At a Glance Summary Box
// ============================================================

/**
 * Quick picks shortcode.
 *
 * Usage: [quick_picks]
 *   [quick_pick type="Best Overall" name="Product A" desc="Short description" price="$299" link="https://..."]
 *   [quick_pick type="Best Budget" name="Product B" desc="Short description" price="$99" link="https://..."]
 *   [quick_pick type="Best Premium" name="Product C" desc="Short description" price="$499" link="https://..."]
 * [/quick_picks]
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Inner shortcode content.
 * @return string Quick picks HTML.
 */
function nest_well_quick_picks_shortcode( $atts, $content = '' ) {
    $atts = shortcode_atts(
        array( 'title' => 'At a Glance' ),
        $atts,
        'quick_picks'
    );

    $picks = array();
    preg_match_all( '/\[quick_pick([^\]]*)\]/i', $content, $matches );

    if ( ! empty( $matches[1] ) ) {
        foreach ( $matches[1] as $pick_attrs_str ) {
            $pick_atts = shortcode_parse_atts( $pick_attrs_str );
            $pick_atts = wp_parse_args(
                $pick_atts,
                array(
                    'type'  => '',
                    'name'  => '',
                    'desc'  => '',
                    'price' => '',
                    'link'  => '',
                )
            );
            $picks[] = $pick_atts;
        }
    }

    if ( empty( $picks ) ) {
        return '';
    }

    ob_start();
    ?>
    <div class="quick-picks">
        <div class="quick-picks__header">
            <span class="quick-picks__icon" aria-hidden="true">&#9776;</span>
            <h3 class="quick-picks__title"><?php echo esc_html( $atts['title'] ); ?></h3>
        </div>
        <ul class="quick-picks__list">
            <?php foreach ( $picks as $pick ) : ?>
            <li class="quick-picks__item">
                <?php if ( $pick['type'] ) : ?>
                <span class="quick-picks__type"><?php echo esc_html( $pick['type'] ); ?>:</span>
                <?php endif; ?>
                <span class="quick-picks__name">
                    <?php if ( $pick['link'] ) : ?>
                    <a href="<?php echo esc_url( $pick['link'] ); ?>"
                       target="_blank"
                       rel="nofollow noopener sponsored">
                        <?php echo esc_html( $pick['name'] ); ?>
                    </a>
                    <?php else : ?>
                    <?php echo esc_html( $pick['name'] ); ?>
                    <?php endif; ?>
                </span>
                <?php if ( $pick['desc'] ) : ?>
                <span class="quick-picks__desc">&mdash; <?php echo esc_html( $pick['desc'] ); ?></span>
                <?php endif; ?>
                <?php if ( $pick['price'] ) : ?>
                <span class="quick-picks__price"><?php echo esc_html( $pick['price'] ); ?></span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'quick_picks', 'nest_well_quick_picks_shortcode' );
add_shortcode( 'quick_pick', '__return_empty_string' );

// ============================================================
// 8. [faq] — FAQ Accordion Item with Schema
// ============================================================

/**
 * FAQ shortcode — individual FAQ item.
 * Automatically collects items for FAQPage JSON-LD in wp_footer.
 *
 * Usage: [faq question="What is..." answer="It is..."]
 * Use multiple [faq] shortcodes in one article.
 *
 * @param array $atts Shortcode attributes.
 * @return string FAQ accordion HTML.
 */
function nest_well_faq_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'question' => '',
            'answer'   => '',
        ),
        $atts,
        'faq'
    );

    if ( empty( $atts['question'] ) || empty( $atts['answer'] ) ) {
        return '';
    }

    // Register for FAQPage schema (output in wp_footer via seo-helpers.php)
    nest_well_get_faq_items(
        array(
            'question' => wp_strip_all_tags( $atts['question'] ),
            'answer'   => wp_strip_all_tags( $atts['answer'] ),
        )
    );

    static $faq_index = 0;
    $faq_index++;
    $faq_id = 'faq-' . $faq_index;

    ob_start();
    ?>
    <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
        <button class="faq-item__question" aria-expanded="false" aria-controls="<?php echo esc_attr( $faq_id ); ?>">
            <span itemprop="name"><?php echo esc_html( $atts['question'] ); ?></span>
            <span class="faq-item__icon" aria-hidden="true">+</span>
        </button>
        <div class="faq-item__answer" id="<?php echo esc_attr( $faq_id ); ?>" hidden
             itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text"><?php echo wp_kses_post( $atts['answer'] ); ?></div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'faq', 'nest_well_faq_shortcode' );

// ============================================================
// 9. [buy_button] — Standalone CTA Button
// ============================================================

/**
 * Buy button shortcode.
 *
 * Usage: [buy_button url="https://..." text="Check Price on Amazon"]
 *
 * @param array $atts Shortcode attributes.
 * @return string Button HTML.
 */
function nest_well_buy_button_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'url'   => '',
            'text'  => 'Check Price on Amazon',
            'style' => 'primary',
        ),
        $atts,
        'buy_button'
    );

    if ( empty( $atts['url'] ) ) {
        return '';
    }

    $btn_class = 'sage' === $atts['style'] ? 'btn--sage' : 'btn--primary';

    return sprintf(
        '<a href="%s" class="btn %s shortcode-buy-button" target="_blank" rel="nofollow noopener sponsored" data-affiliate="amazon">%s <span aria-hidden="true">&rarr;</span></a>',
        esc_url( $atts['url'] ),
        esc_attr( $btn_class ),
        esc_html( $atts['text'] )
    );
}
add_shortcode( 'buy_button', 'nest_well_buy_button_shortcode' );

// ============================================================
// 10. [review_summary] — Review Summary Card
// ============================================================

/**
 * Review summary shortcode — shown at top of review articles.
 *
 * Usage: [review_summary score="9.4" title="Product Name"
 *         pros="Pro 1,Pro 2,Pro 3" cons="Con 1,Con 2"
 *         verdict="Overall verdict text here."]
 *
 * @param array $atts Shortcode attributes.
 * @return string Review summary HTML.
 */
function nest_well_review_summary_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'score'   => '',
            'title'   => '',
            'pros'    => '',
            'cons'    => '',
            'verdict' => '',
            'price'   => '',
            'badge'   => '',
        ),
        $atts,
        'review_summary'
    );

    $pros = array_filter( array_map( 'trim', explode( ',', $atts['pros'] ) ) );
    $cons = array_filter( array_map( 'trim', explode( ',', $atts['cons'] ) ) );

    ob_start();
    ?>
    <div class="review-summary">
        <div class="review-summary__header">
            <?php if ( $atts['badge'] ) : ?>
            <span class="badge badge--<?php echo esc_attr( $atts['badge'] ); ?>">
                <?php echo esc_html( nest_well_get_badge_label( $atts['badge'] ) ); ?>
            </span>
            <?php endif; ?>
            <?php if ( $atts['title'] ) : ?>
            <h3 class="review-summary__title"><?php echo esc_html( $atts['title'] ); ?></h3>
            <?php endif; ?>
            <?php if ( $atts['score'] ) : ?>
            <div class="review-summary__score-wrap">
                <div class="review-summary__score"><?php echo esc_html( $atts['score'] ); ?><span>/10</span></div>
                <?php echo wp_kses_post( nest_well_star_rating_html( $atts['score'] ) ); ?>
            </div>
            <?php endif; ?>
        </div>

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

        <?php if ( $atts['verdict'] ) : ?>
        <div class="review-summary__verdict">
            <strong><?php esc_html_e( 'Verdict:', 'nest-and-well' ); ?></strong>
            <?php echo wp_kses_post( $atts['verdict'] ); ?>
        </div>
        <?php endif; ?>

        <?php if ( $atts['price'] ) : ?>
        <div class="review-summary__price">
            <span class="price-label"><?php esc_html_e( 'From', 'nest-and-well' ); ?></span>
            <span class="price-value"><?php echo esc_html( $atts['price'] ); ?></span>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'review_summary', 'nest_well_review_summary_shortcode' );
