<?php
/**
 * Affiliate Helpers for Nest & Well
 * Amazon affiliate link functions and FTC disclosure management.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the Amazon Associates tracking ID from Customizer.
 *
 * @return string Tracking ID or empty string.
 */
function nest_well_get_tracking_id() {
    return get_theme_mod( 'nest_well_amazon_tracking_id', '' );
}

/**
 * Build an Amazon affiliate product URL.
 *
 * @param string $asin        Amazon ASIN (product ID).
 * @param string $tracking_id Optional tracking ID override.
 * @return string Full Amazon affiliate URL.
 */
function nest_well_amazon_url( $asin, $tracking_id = '' ) {
    if ( empty( $asin ) ) {
        return '';
    }

    $tracking_id = $tracking_id ?: nest_well_get_tracking_id();
    $url         = 'https://www.amazon.com/dp/' . rawurlencode( $asin );

    if ( $tracking_id ) {
        $url .= '?tag=' . rawurlencode( $tracking_id );
    }

    return $url;
}

/**
 * Build a complete Amazon affiliate link HTML element.
 *
 * @param string $asin        Amazon ASIN.
 * @param string $text        Link text.
 * @param array  $args        Optional arguments.
 * @return string HTML anchor tag.
 */
function nest_well_amazon_link( $asin, $text, $args = array() ) {
    $url = nest_well_amazon_url( $asin );

    if ( empty( $url ) ) {
        return esc_html( $text );
    }

    $defaults = array(
        'class'  => 'amazon-affiliate-link',
        'target' => '_blank',
        'rel'    => 'nofollow noopener sponsored',
    );

    $args = wp_parse_args( $args, $defaults );

    return sprintf(
        '<a href="%s" class="%s" target="%s" rel="%s">%s</a>',
        esc_url( $url ),
        esc_attr( $args['class'] ),
        esc_attr( $args['target'] ),
        esc_attr( $args['rel'] ),
        esc_html( $text )
    );
}

/**
 * Wrap any URL with proper affiliate attributes.
 *
 * @param string $url         The URL to wrap.
 * @param string $text        Link text.
 * @param string $tracking_id Optional tracking ID.
 * @param array  $args        Optional link attributes.
 * @return string HTML anchor tag.
 */
function nest_well_affiliate_link( $url, $text, $tracking_id = '', $args = array() ) {
    if ( empty( $url ) ) {
        return esc_html( $text );
    }

    // Append tracking ID to Amazon URLs if provided
    if ( $tracking_id && strpos( $url, 'amazon.com' ) !== false ) {
        $separator = ( strpos( $url, '?' ) !== false ) ? '&' : '?';
        $url      .= $separator . 'tag=' . rawurlencode( $tracking_id );
    } elseif ( ! $tracking_id ) {
        $stored_id = nest_well_get_tracking_id();
        if ( $stored_id && strpos( $url, 'amazon.com' ) !== false ) {
            $separator = ( strpos( $url, '?' ) !== false ) ? '&' : '?';
            $url      .= $separator . 'tag=' . rawurlencode( $stored_id );
        }
    }

    $defaults = array(
        'class'  => 'affiliate-link',
        'target' => '_blank',
        'rel'    => 'nofollow noopener sponsored',
    );

    $args = wp_parse_args( $args, $defaults );

    return sprintf(
        '<a href="%s" class="%s" target="%s" rel="%s">%s</a>',
        esc_url( $url ),
        esc_attr( $args['class'] ),
        esc_attr( $args['target'] ),
        esc_attr( $args['rel'] ),
        esc_html( $text )
    );
}

/**
 * Get the FTC disclosure HTML from Customizer settings.
 *
 * @param bool $inline Whether this is an inline disclosure (vs. banner).
 * @return string Disclosure HTML.
 */
function nest_well_get_disclosure_html( $inline = false ) {
    $text = get_theme_mod(
        'nest_well_disclosure_text',
        'Disclosure: We independently review everything we recommend. We may earn a commission if you buy through our links — at no extra cost to you. Our editorial opinions are never influenced by commissions.'
    );

    $class = $inline ? 'affiliate-disclosure affiliate-disclosure--inline' : 'affiliate-disclosure';

    $disclosure_page_url = get_permalink( get_page_by_path( 'affiliate-disclosure' ) );

    $learn_more = '';
    if ( $disclosure_page_url ) {
        $learn_more = ' <a href="' . esc_url( $disclosure_page_url ) . '" class="disclosure-link">' . esc_html__( 'Learn more', 'nest-and-well' ) . ' &rarr;</a>';
    }

    return sprintf(
        '<div class="%s" role="note"><p>%s%s</p></div>',
        esc_attr( $class ),
        wp_kses_post( $text ),
        $learn_more
    );
}

/**
 * Auto-prepend affiliate disclosure to single post content.
 * Skips pages, homepage, and content that already has the disclosure shortcode.
 *
 * @param string $content Post content.
 * @return string Modified content.
 */
function nest_well_prepend_disclosure( $content ) {
    // Only on single posts (not pages, not archives).
    if ( ! is_single() || is_page() ) {
        return $content;
    }

    // Skip if disclosure shortcode already present.
    if ( has_shortcode( $content, 'affiliate_disclosure' ) ) {
        return $content;
    }

    // Skip if disclosure HTML class already present (from template).
    if ( strpos( $content, 'affiliate-disclosure' ) !== false ) {
        return $content;
    }

    $disclosure = nest_well_get_disclosure_html( true );

    return $disclosure . $content;
}
add_filter( 'the_content', 'nest_well_prepend_disclosure', 5 );

/**
 * Auto-disclosure hook for single posts via wp_head.
 * FTC compliance: disclosure must be visible before content.
 */
function nest_well_auto_affiliate_disclosure() {
    if ( ! is_single() ) {
        return;
    }
    // Output handled by prepend_disclosure filter above.
}
add_action( 'wp_head', 'nest_well_auto_affiliate_disclosure' );

/**
 * Get social links array from Customizer.
 *
 * @return array Social network => URL pairs.
 */
function nest_well_get_social_links() {
    $networks = array(
        'pinterest' => array(
            'label' => 'Pinterest',
            'icon'  => 'P',
        ),
        'instagram' => array(
            'label' => 'Instagram',
            'icon'  => 'IG',
        ),
        'youtube'   => array(
            'label' => 'YouTube',
            'icon'  => 'YT',
        ),
        'twitter'   => array(
            'label' => 'Twitter / X',
            'icon'  => 'X',
        ),
    );

    $social_links = array();

    foreach ( $networks as $network => $data ) {
        $url = get_theme_mod( "nest_well_social_{$network}", '' );
        if ( $url ) {
            $social_links[ $network ] = array_merge( $data, array( 'url' => $url ) );
        }
    }

    return $social_links;
}

/**
 * Output social links HTML.
 *
 * @param string $class CSS class for the container.
 */
function nest_well_social_links_html( $class = 'social-links' ) {
    $social_links = nest_well_get_social_links();

    if ( empty( $social_links ) ) {
        return;
    }

    echo '<ul class="' . esc_attr( $class ) . '">';

    foreach ( $social_links as $network => $data ) {
        printf(
            '<li class="social-links__item social-links__item--%s"><a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s"><span class="social-icon" aria-hidden="true">%s</span></a></li>',
            esc_attr( $network ),
            esc_url( $data['url'] ),
            esc_attr( $data['label'] ),
            esc_html( $data['icon'] )
        );
    }

    echo '</ul>';
}
