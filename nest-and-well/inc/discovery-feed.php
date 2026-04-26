<?php
/**
 * Discovery Feed
 *
 * Image-first masonry browse module rendered below the editorial hero
 * on the homepage. Borrows the density and image-first hierarchy of
 * thisiswhyimbroke.com without changing the Nest & Well voice or palette.
 *
 * Provides:
 *   - nest_well_render_discovery_card( $post_id ) — shared markup helper
 *     used by both the template-part (initial server render) and the
 *     REST endpoint (infinite-scroll appends), so the markup never drifts.
 *   - REST endpoint nestwell/v1/discovery for IntersectionObserver fetches.
 *   - [discovery_feed] shortcode for editors.
 *   - nest-well/discovery-feed block pattern for Gutenberg.
 *
 * Customizer fields for this module live in inc/customizer.php so they
 * sit alongside the other Homepage settings.
 *
 * @package nest-and-well
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whitelist of valid density tokens.
 *
 * @return array
 */
function nest_well_discovery_densities() {
    return array( 'cozy', 'dense', 'tight' );
}

/**
 * Sanitize a density value against the whitelist.
 *
 * @param string $value Raw value.
 * @return string Sanitized value.
 */
function nest_well_discovery_sanitize_density( $value ) {
    return in_array( $value, nest_well_discovery_densities(), true ) ? $value : 'cozy';
}

/**
 * Sanitize the discovery source value.
 *
 * Accepts: 'latest', 'mixed', or 'cat-{id}' where id is a positive integer.
 *
 * @param string $value Raw value.
 * @return string Sanitized value.
 */
function nest_well_discovery_sanitize_source( $value ) {
    $value = (string) $value;
    if ( 'latest' === $value || 'mixed' === $value ) {
        return $value;
    }
    if ( preg_match( '/^cat-(\d+)$/', $value, $m ) ) {
        return 'cat-' . absint( $m[1] );
    }
    return 'latest';
}

/**
 * Sanitize the per-page value.
 *
 * @param int $value Raw value.
 * @return int Sanitized value.
 */
function nest_well_discovery_sanitize_per_page( $value ) {
    $value = absint( $value );
    if ( ! in_array( $value, array( 12, 18, 24, 36 ), true ) ) {
        return 18;
    }
    return $value;
}

/**
 * Map a post's primary category to its aspect-ratio tier.
 *
 * Smart Home + Wellness Tech → square (product feel)
 * Home & Beauty              → tall   (lifestyle feel)
 * Gift Guides + Deals        → wide   (editorial feel)
 *
 * Match is by category slug so editors can rename the display name
 * without breaking the mapping. Falls back to slug *contains* match
 * for flexibility (e.g., "smart-home-deals" → square).
 *
 * @param int $post_id Post ID.
 * @return string One of: 'tall' | 'square' | 'wide'.
 */
function nest_well_discovery_card_tier( $post_id ) {
    $cats = get_the_category( $post_id );
    if ( empty( $cats ) ) {
        return 'square';
    }

    $square_keys = array( 'smart-home', 'wellness', 'wellness-tech', 'tech' );
    $tall_keys   = array( 'home-beauty', 'beauty', 'home-and-beauty', 'home' );
    $wide_keys   = array( 'gift-guide', 'gift-guides', 'gifts', 'deals', 'deal' );

    foreach ( $cats as $cat ) {
        $slug = $cat->slug;
        foreach ( $tall_keys as $key ) {
            if ( false !== strpos( $slug, $key ) && false === strpos( $slug, 'smart' ) ) {
                return 'tall';
            }
        }
        foreach ( $wide_keys as $key ) {
            if ( false !== strpos( $slug, $key ) ) {
                return 'wide';
            }
        }
        foreach ( $square_keys as $key ) {
            if ( false !== strpos( $slug, $key ) ) {
                return 'square';
            }
        }
    }

    return 'square';
}

/**
 * Resolve the best featured-image size to use for a given tier.
 *
 * @param string $tier Tier slug.
 * @return string WordPress image size.
 */
function nest_well_discovery_image_size( $tier ) {
    switch ( $tier ) {
        case 'tall':
            return 'discovery-tall';
        case 'wide':
            return 'card-thumbnail';
        case 'square':
        default:
            return 'discovery-square';
    }
}

/**
 * Render a single discovery card.
 *
 * Decides BUY vs REVIEW variant by presence of _product_asin meta.
 * Shared between server-side template-part and REST endpoint to
 * guarantee identical markup for affiliate compliance and a11y.
 *
 * @param int $post_id Post ID.
 * @return string Card HTML.
 */
function nest_well_render_discovery_card( $post_id ) {
    $post = get_post( $post_id );
    if ( ! $post || 'publish' !== $post->post_status ) {
        return '';
    }

    $title         = get_the_title( $post );
    $permalink     = get_permalink( $post );
    $tier          = nest_well_discovery_card_tier( $post_id );
    $image_size    = nest_well_discovery_image_size( $tier );

    $asin          = get_post_meta( $post_id, '_product_asin', true );
    $price         = get_post_meta( $post_id, '_product_price', true );
    $score         = (float) get_post_meta( $post_id, '_review_score', true );
    $badge         = get_post_meta( $post_id, '_review_badge', true );
    $product_name  = get_post_meta( $post_id, '_product_name', true );

    $has_asin      = (bool) $asin;
    $variant       = $has_asin ? 'buy' : 'review';

    if ( $has_asin && function_exists( 'nest_well_amazon_url' ) ) {
        $cta_url      = nest_well_amazon_url( $asin );
        $cta_text     = esc_html__( 'Buy on Amazon', 'nest-and-well' );
        $cta_rel      = 'nofollow noopener sponsored';
        $cta_target   = '_blank';
        $affiliate_attr = ' data-affiliate="amazon"';
    } else {
        $cta_url      = $permalink;
        $cta_text     = esc_html__( 'Read Review', 'nest-and-well' );
        $cta_rel      = '';
        $cta_target   = '';
        $affiliate_attr = '';
    }

    $card_classes = array( 'discovery-card', 'discovery-card--' . $variant );
    if ( 'editors-choice' === $badge ) {
        $card_classes[] = 'discovery-card--editors-choice';
    } elseif ( 'best-value' === $badge ) {
        $card_classes[] = 'discovery-card--best-value';
    }

    ob_start();
    ?>
    <article class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>" itemscope itemtype="https://schema.org/Product" data-post-id="<?php echo esc_attr( $post_id ); ?>">

        <div class="discovery-card__media discovery-card__media--<?php echo esc_attr( $tier ); ?>">

            <?php if ( has_post_thumbnail( $post ) ) : ?>
                <?php
                echo get_the_post_thumbnail(
                    $post,
                    $image_size,
                    array(
                        'class'    => 'discovery-card__image',
                        'loading'  => 'lazy',
                        'alt'      => esc_attr( $title ),
                        'itemprop' => 'image',
                    )
                );
                ?>
            <?php else : ?>
                <div class="discovery-card__image-placeholder" aria-hidden="true"></div>
            <?php endif; ?>

            <?php if ( $badge && function_exists( 'nest_well_get_badge_label' ) ) : ?>
                <span class="discovery-card__badge badge badge--<?php echo esc_attr( $badge ); ?>">
                    <?php echo esc_html( nest_well_get_badge_label( $badge ) ); ?>
                </span>
            <?php endif; ?>

            <?php if ( $price ) : ?>
                <span class="discovery-card__price-pill" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                    <span itemprop="price" content="<?php echo esc_attr( preg_replace( '/[^0-9.]/', '', $price ) ); ?>">
                        <?php echo esc_html( $price ); ?>
                    </span>
                    <meta itemprop="priceCurrency" content="USD">
                </span>
            <?php endif; ?>

            <?php if ( $score ) : ?>
                <span class="discovery-card__rating-pill nw-num" data-score-target="<?php echo esc_attr( number_format( $score, 1, '.', '' ) ); ?>" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                    <span class="nw-score-num"><?php echo esc_html( number_format( $score, 1 ) ); ?></span>/10
                    <meta itemprop="ratingValue" content="<?php echo esc_attr( $score ); ?>">
                    <meta itemprop="bestRating" content="10">
                    <meta itemprop="ratingCount" content="1">
                </span>
            <?php endif; ?>

            <div class="discovery-card__overlay">
                <h3 class="discovery-card__title" itemprop="name">
                    <a href="<?php echo esc_url( $permalink ); ?>" class="discovery-card__title-link">
                        <?php echo esc_html( $product_name ? $product_name : $title ); ?>
                    </a>
                </h3>
                <a href="<?php echo esc_url( $cta_url ); ?>"
                   class="discovery-card__cta discovery-card__cta--<?php echo esc_attr( $variant ); ?>"
                   <?php if ( $cta_target ) : ?>target="<?php echo esc_attr( $cta_target ); ?>"<?php endif; ?>
                   <?php if ( $cta_rel ) : ?>rel="<?php echo esc_attr( $cta_rel ); ?>"<?php endif; ?>
                   <?php echo $affiliate_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static literal ?>
                   data-product="<?php echo esc_attr( $product_name ? $product_name : $title ); ?>">
                    <?php echo esc_html( $cta_text ); ?>
                    <?php if ( $has_asin ) : ?><span aria-hidden="true">&rarr;</span><?php endif; ?>
                </a>
            </div>

        </div><!-- .discovery-card__media -->

    </article>
    <?php
    return ob_get_clean();
}

/**
 * Build the WP_Query args for a discovery feed page.
 *
 * @param string $source   Source token.
 * @param int    $page     Page number.
 * @param int    $per_page Posts per page.
 * @return array WP_Query args.
 */
function nest_well_discovery_query_args( $source, $page, $per_page ) {
    $args = array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => $per_page,
        'paged'               => max( 1, (int) $page ),
        'ignore_sticky_posts' => true,
        'no_found_rows'       => false,
    );

    if ( 'latest' === $source ) {
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
    } elseif ( 'mixed' === $source ) {
        // Round-robin across the 5 site categories by querying recent posts
        // and letting the server render in date order. (True round-robin is
        // expensive; date-DESC across all categories gives a representative
        // mix without per-request taxonomy gymnastics.)
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
    } elseif ( preg_match( '/^cat-(\d+)$/', $source, $m ) ) {
        $args['cat']     = absint( $m[1] );
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
    }

    return $args;
}

/**
 * Render the discovery feed wrapped HTML.
 *
 * Used by template-part and shortcode. Pulls headline/subtitle/source/density
 * from Customizer with sensible defaults; per-instance overrides take precedence.
 *
 * @param array $overrides Per-instance overrides.
 * @return string HTML.
 */
function nest_well_render_discovery_feed( $overrides = array() ) {
    $enabled = isset( $overrides['enabled'] )
        ? (bool) $overrides['enabled']
        : (bool) get_theme_mod( 'nest_well_discovery_enabled', true );

    if ( ! $enabled ) {
        return '';
    }

    $headline = isset( $overrides['headline'] )
        ? (string) $overrides['headline']
        : get_theme_mod( 'nest_well_discovery_headline', __( 'More to Explore', 'nest-and-well' ) );

    $subtitle = isset( $overrides['subtitle'] )
        ? (string) $overrides['subtitle']
        : get_theme_mod( 'nest_well_discovery_subtitle', __( "Browse what we're testing this week.", 'nest-and-well' ) );

    $source = isset( $overrides['source'] )
        ? nest_well_discovery_sanitize_source( $overrides['source'] )
        : nest_well_discovery_sanitize_source( get_theme_mod( 'nest_well_discovery_source', 'latest' ) );

    $density = isset( $overrides['density'] )
        ? nest_well_discovery_sanitize_density( $overrides['density'] )
        : nest_well_discovery_sanitize_density( get_theme_mod( 'nest_well_discovery_density', 'cozy' ) );

    $per_page = isset( $overrides['per_page'] )
        ? nest_well_discovery_sanitize_per_page( $overrides['per_page'] )
        : nest_well_discovery_sanitize_per_page( get_theme_mod( 'nest_well_discovery_per_page', 18 ) );

    $query = new WP_Query( nest_well_discovery_query_args( $source, 1, $per_page ) );

    if ( ! $query->have_posts() ) {
        return '';
    }

    $total_pages = (int) $query->max_num_pages;

    ob_start();
    ?>
    <section class="discovery-feed container" data-density="<?php echo esc_attr( $density ); ?>">

        <header class="discovery-feed__header">
            <h2 class="discovery-feed__title"><?php echo esc_html( $headline ); ?></h2>
            <?php if ( $subtitle ) : ?>
                <p class="discovery-feed__subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
        </header>

        <div class="discovery-feed__masonry" id="discovery-feed-grid">
            <?php
            while ( $query->have_posts() ) {
                $query->the_post();
                echo nest_well_render_discovery_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — escaped in helper
            }
            wp_reset_postdata();
            ?>
        </div>

        <?php if ( $total_pages > 1 ) : ?>
            <div class="discovery-feed__sentinel js-discovery-sentinel"
                 aria-hidden="true"
                 data-page="2"
                 data-per-page="<?php echo esc_attr( $per_page ); ?>"
                 data-source="<?php echo esc_attr( $source ); ?>"
                 data-density="<?php echo esc_attr( $density ); ?>"
                 data-total-pages="<?php echo esc_attr( $total_pages ); ?>"></div>
        <?php endif; ?>

        <div class="discovery-feed__loading js-discovery-loading" hidden aria-live="polite" aria-busy="false">
            <span class="screen-reader-text"><?php esc_html_e( 'Loading more picks...', 'nest-and-well' ); ?></span>
            <?php for ( $i = 0; $i < 3; $i++ ) : ?>
                <div class="skeleton-card" aria-hidden="true"></div>
            <?php endfor; ?>
        </div>

        <div class="discovery-feed__end js-discovery-end" hidden aria-live="polite">
            <p><?php esc_html_e( "That's everything we're showing here.", 'nest-and-well' ); ?></p>
        </div>

    </section>
    <?php
    return ob_get_clean();
}

/**
 * Register the REST endpoint that powers infinite-scroll appends.
 */
function nest_well_register_discovery_rest() {
    register_rest_route(
        'nestwell/v1',
        '/discovery',
        array(
            'methods'             => 'GET',
            'permission_callback' => '__return_true',
            'callback'            => 'nest_well_discovery_rest_callback',
            'args'                => array(
                'page'     => array( 'default' => 2,    'sanitize_callback' => 'absint' ),
                'per_page' => array( 'default' => 18,   'sanitize_callback' => 'nest_well_discovery_sanitize_per_page' ),
                'source'   => array( 'default' => 'latest', 'sanitize_callback' => 'nest_well_discovery_sanitize_source' ),
                'density'  => array( 'default' => 'cozy',   'sanitize_callback' => 'nest_well_discovery_sanitize_density' ),
            ),
        )
    );
}
add_action( 'rest_api_init', 'nest_well_register_discovery_rest' );

/**
 * REST callback — returns rendered HTML for one page of cards.
 *
 * @param WP_REST_Request $req REST request.
 * @return WP_REST_Response
 */
function nest_well_discovery_rest_callback( $req ) {
    $page     = max( 1, (int) $req->get_param( 'page' ) );
    $per_page = nest_well_discovery_sanitize_per_page( $req->get_param( 'per_page' ) );
    $source   = nest_well_discovery_sanitize_source( $req->get_param( 'source' ) );

    $query = new WP_Query( nest_well_discovery_query_args( $source, $page, $per_page ) );

    $html = '';
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $html .= nest_well_render_discovery_card( get_the_ID() );
        }
        wp_reset_postdata();
    }

    $total_pages = (int) $query->max_num_pages;

    return rest_ensure_response(
        array(
            'html'       => $html,
            'page'       => $page,
            'totalPages' => $total_pages,
            'hasMore'    => $page < $total_pages,
        )
    );
}

/**
 * [discovery_feed] shortcode — wraps the render helper.
 *
 * Attributes:
 *   source   = latest | mixed | cat-{id}
 *   density  = cozy | dense | tight
 *   per_page = 12 | 18 | 24 | 36
 *   headline = string
 *   subtitle = string
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML.
 */
function nest_well_discovery_feed_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'source'   => '',
            'density'  => '',
            'per_page' => '',
            'headline' => '',
            'subtitle' => '',
        ),
        $atts,
        'discovery_feed'
    );

    $overrides = array();
    foreach ( $atts as $k => $v ) {
        if ( '' !== $v ) {
            $overrides[ $k ] = $v;
        }
    }

    return nest_well_render_discovery_feed( $overrides );
}
add_shortcode( 'discovery_feed', 'nest_well_discovery_feed_shortcode' );

/**
 * Register the block pattern and its category.
 */
function nest_well_register_discovery_pattern() {
    if ( function_exists( 'register_block_pattern_category' ) ) {
        register_block_pattern_category(
            'nest-well',
            array( 'label' => __( 'Nest & Well', 'nest-and-well' ) )
        );
    }

    if ( function_exists( 'register_block_pattern' ) ) {
        register_block_pattern(
            'nest-well/discovery-feed',
            array(
                'title'       => __( 'Discovery Feed (image-first masonry)', 'nest-and-well' ),
                'description' => __( 'Image-led product cards in a 3-5 column masonry. Honors Discovery settings in the Customizer.', 'nest-and-well' ),
                'categories'  => array( 'nest-well' ),
                'keywords'    => array( 'masonry', 'feed', 'products', 'discovery' ),
                'content'     => '<!-- wp:shortcode -->[discovery_feed]<!-- /wp:shortcode -->',
            )
        );
    }
}
add_action( 'init', 'nest_well_register_discovery_pattern' );
