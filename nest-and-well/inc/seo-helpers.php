<?php
/**
 * SEO Helpers for Nest & Well
 * Open Graph, Twitter Card, Schema.org JSON-LD, breadcrumbs, canonical URL.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output all SEO meta tags in wp_head.
 */
function nest_well_seo_meta_tags() {
    // Canonical URL
    nest_well_canonical_url();

    // Open Graph
    nest_well_open_graph_tags();

    // Twitter Card
    nest_well_twitter_card_tags();

    // Schema.org JSON-LD
    nest_well_schema_markup();
}
add_action( 'wp_head', 'nest_well_seo_meta_tags', 5 );

/**
 * Output canonical URL tag.
 */
function nest_well_canonical_url() {
    $canonical = '';

    if ( is_singular() ) {
        $canonical = get_permalink();
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $canonical = get_term_link( get_queried_object() );
    } elseif ( is_front_page() ) {
        $canonical = home_url( '/' );
    } elseif ( is_home() ) {
        $canonical = get_permalink( get_option( 'page_for_posts' ) );
    } elseif ( is_search() ) {
        $canonical = get_search_link();
    } elseif ( is_archive() ) {
        $canonical = get_post_type_archive_link( get_post_type() );
    }

    if ( $canonical && ! is_wp_error( $canonical ) ) {
        echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
    }
}

/**
 * Get SEO description for current page.
 *
 * @return string Description text.
 */
function nest_well_get_seo_description() {
    if ( is_singular() ) {
        $excerpt = get_the_excerpt();
        if ( $excerpt ) {
            return wp_strip_all_tags( $excerpt );
        }
        $content = get_the_content();
        return wp_trim_words( wp_strip_all_tags( $content ), 30, '' );
    }

    if ( is_category() || is_tag() || is_tax() ) {
        $description = term_description();
        if ( $description ) {
            return wp_strip_all_tags( $description );
        }
    }

    return get_bloginfo( 'description' );
}

/**
 * Get OG image URL for current page.
 *
 * @return string Image URL.
 */
function nest_well_get_og_image() {
    if ( is_singular() && has_post_thumbnail() ) {
        $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
        if ( $image ) {
            return $image[0];
        }
    }

    // Fallback to custom logo
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
        if ( $logo ) {
            return $logo[0];
        }
    }

    // Fallback to placeholder
    return NEST_WELL_URI . '/assets/images/logo-placeholder.svg';
}

/**
 * Output Open Graph meta tags.
 */
function nest_well_open_graph_tags() {
    $title       = wp_get_document_title();
    $description = nest_well_get_seo_description();
    $image       = nest_well_get_og_image();
    $url         = '';
    $type        = 'website';

    if ( is_singular() ) {
        $url  = get_permalink();
        $type = 'article';
    } elseif ( is_front_page() ) {
        $url = home_url( '/' );
    } else {
        $url = get_pagenum_link();
    }

    echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( $type ) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";

    if ( is_single() ) {
        echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '" />' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '" />' . "\n";

        $categories = get_the_category();
        if ( $categories ) {
            echo '<meta property="article:section" content="' . esc_attr( $categories[0]->name ) . '" />' . "\n";
        }
    }
}

/**
 * Output Twitter Card meta tags.
 */
function nest_well_twitter_card_tags() {
    $title       = wp_get_document_title();
    $description = nest_well_get_seo_description();
    $image       = nest_well_get_og_image();

    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
}

/**
 * Output Schema.org JSON-LD markup.
 */
function nest_well_schema_markup() {
    $schemas = array();

    // Organization / WebSite schema (always)
    $schemas[] = nest_well_website_schema();

    if ( is_front_page() ) {
        // Nothing extra on homepage beyond WebSite schema.
    } elseif ( is_singular( 'post' ) ) {
        $review_score = get_post_meta( get_the_ID(), '_review_score', true );
        $product_name = get_post_meta( get_the_ID(), '_product_name', true );

        if ( $review_score && $product_name ) {
            $schemas[] = nest_well_review_schema();
        } else {
            $schemas[] = nest_well_article_schema();
        }

        $schemas[] = nest_well_breadcrumb_schema();
    } elseif ( is_page() || is_category() || is_archive() ) {
        $schemas[] = nest_well_breadcrumb_schema();
    }

    foreach ( $schemas as $schema ) {
        if ( $schema ) {
            echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
        }
    }
}

/**
 * Build WebSite schema.
 *
 * @return array Schema array.
 */
function nest_well_website_schema() {
    return array(
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        'name'            => get_bloginfo( 'name' ),
        'url'             => home_url( '/' ),
        'description'     => get_bloginfo( 'description' ),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => array(
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url( '/?s={search_term_string}' ),
            ),
            'query-input' => 'required name=search_term_string',
        ),
        'publisher'       => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'url'   => home_url( '/' ),
            'logo'  => array(
                '@type' => 'ImageObject',
                'url'   => NEST_WELL_URI . '/assets/images/logo-placeholder.svg',
            ),
        ),
    );
}

/**
 * Build Article schema for single posts.
 *
 * @return array Schema array.
 */
function nest_well_article_schema() {
    $post_id      = get_the_ID();
    $image        = nest_well_get_og_image();
    $last_updated = get_post_meta( $post_id, '_last_updated', true );

    $author_id   = get_post_field( 'post_author', $post_id );
    $author_name = get_the_author_meta( 'display_name', $author_id );
    $author_url  = get_author_posts_url( $author_id );

    return array(
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => get_the_title(),
        'description'      => nest_well_get_seo_description(),
        'image'            => $image,
        'url'              => get_permalink(),
        'datePublished'    => get_the_date( 'c' ),
        'dateModified'     => $last_updated ? date( 'c', strtotime( $last_updated ) ) : get_the_modified_date( 'c' ),
        'author'           => array(
            '@type' => 'Person',
            'name'  => $author_name,
            'url'   => $author_url,
        ),
        'publisher'        => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'logo'  => array(
                '@type' => 'ImageObject',
                'url'   => NEST_WELL_URI . '/assets/images/logo-placeholder.svg',
            ),
        ),
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id'   => get_permalink(),
        ),
    );
}

/**
 * Build Review schema for product review posts.
 *
 * @return array Schema array.
 */
function nest_well_review_schema() {
    $post_id      = get_the_ID();
    $score        = (float) get_post_meta( $post_id, '_review_score', true );
    $product_name = get_post_meta( $post_id, '_product_name', true );
    $last_updated = get_post_meta( $post_id, '_last_updated', true );

    $author_id   = get_post_field( 'post_author', $post_id );
    $author_name = get_the_author_meta( 'display_name', $author_id );

    return array(
        '@context'     => 'https://schema.org',
        '@type'        => 'Review',
        'name'         => get_the_title(),
        'description'  => nest_well_get_seo_description(),
        'url'          => get_permalink(),
        'datePublished' => get_the_date( 'c' ),
        'dateModified'  => $last_updated ? date( 'c', strtotime( $last_updated ) ) : get_the_modified_date( 'c' ),
        'author'       => array(
            '@type' => 'Person',
            'name'  => $author_name,
        ),
        'reviewRating' => array(
            '@type'       => 'Rating',
            'ratingValue' => $score,
            'bestRating'  => 10,
            'worstRating' => 0,
        ),
        'itemReviewed' => array(
            '@type' => 'Product',
            'name'  => $product_name,
        ),
        'publisher'    => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
        ),
    );
}

/**
 * Build BreadcrumbList schema.
 *
 * @return array|null Schema array or null on homepage.
 */
function nest_well_breadcrumb_schema() {
    if ( is_front_page() ) {
        return null;
    }

    $items   = array();
    $items[] = array(
        '@type'    => 'ListItem',
        'position' => 1,
        'name'     => get_bloginfo( 'name' ),
        'item'     => home_url( '/' ),
    );

    $position = 2;

    if ( is_single() ) {
        $categories = get_the_category();
        if ( $categories ) {
            $cat     = $categories[0];
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => $cat->name,
                'item'     => get_category_link( $cat->term_id ),
            );
            $position++;
        }
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif ( is_category() ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => single_cat_title( '', false ),
            'item'     => get_term_link( get_queried_object() ),
        );
    } elseif ( is_page() ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    }

    return array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    );
}

/**
 * Output FAQPage JSON-LD schema.
 * Called from wp_footer when [faq] shortcodes are used on the page.
 * FAQ items are collected by the shortcode via static variable.
 */
function nest_well_faq_schema() {
    $faq_items = nest_well_get_faq_items();

    if ( empty( $faq_items ) ) {
        return;
    }

    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array(),
    );

    foreach ( $faq_items as $item ) {
        $schema['mainEntity'][] = array(
            '@type'          => 'Question',
            'name'           => $item['question'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => $item['answer'],
            ),
        );
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_footer', 'nest_well_faq_schema', 20 );

/**
 * Global FAQ items storage.
 *
 * @param array|null $item FAQ item to add, or null to retrieve all.
 * @return array All collected FAQ items.
 */
function nest_well_get_faq_items( $item = null ) {
    static $faq_items = array();

    if ( null !== $item ) {
        $faq_items[] = $item;
    }

    return $faq_items;
}
