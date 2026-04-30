<?php
/**
 * Nest & Well Theme Functions
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Theme version constant
define( 'NEST_WELL_VERSION', '1.0.4' );
define( 'NEST_WELL_DIR', get_template_directory() );
define( 'NEST_WELL_URI', get_template_directory_uri() );

// Load required files
require_once NEST_WELL_DIR . '/inc/theme-setup.php';
require_once NEST_WELL_DIR . '/inc/customizer.php';
require_once NEST_WELL_DIR . '/inc/widgets.php';
require_once NEST_WELL_DIR . '/inc/affiliate-helpers.php';
require_once NEST_WELL_DIR . '/inc/seo-helpers.php';
require_once NEST_WELL_DIR . '/inc/shortcodes.php';
require_once NEST_WELL_DIR . '/inc/category-feed-meta.php';
require_once NEST_WELL_DIR . '/inc/email-capture.php';
require_once NEST_WELL_DIR . '/inc/user-accounts.php';
require_once NEST_WELL_DIR . '/inc/faq-meta.php';
require_once NEST_WELL_DIR . '/inc/review-meta.php';
require_once NEST_WELL_DIR . '/inc/discovery-feed.php';

/**
 * Enqueue theme scripts and styles.
 */
function nest_well_enqueue_assets() {
    // Google Fonts — Inter for body + JetBrains Mono for numeric data
    wp_enqueue_style(
        'nest-well-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=JetBrains+Mono:wght@400;700&display=swap',
        array(),
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'nest-well-style',
        NEST_WELL_URI . '/assets/css/main.css',
        array( 'nest-well-google-fonts' ),
        NEST_WELL_VERSION
    );

    // Theme style.css (required by WordPress)
    wp_enqueue_style(
        'nest-well-base',
        get_stylesheet_uri(),
        array( 'nest-well-style' ),
        NEST_WELL_VERSION
    );

    // Main JavaScript
    wp_enqueue_script(
        'nest-well-main',
        NEST_WELL_URI . '/assets/js/main.js',
        array(),
        NEST_WELL_VERSION,
        true
    );

    // Affiliate JavaScript
    wp_enqueue_script(
        'nest-well-affiliate',
        NEST_WELL_URI . '/assets/js/affiliate.js',
        array(),
        NEST_WELL_VERSION,
        true
    );

    // Discovery Feed JavaScript — IntersectionObserver for masonry infinite scroll
    wp_enqueue_script(
        'nest-well-discovery',
        NEST_WELL_URI . '/assets/js/discovery.js',
        array( 'nest-well-main' ),
        NEST_WELL_VERSION,
        true
    );

    // Pass data to JS
    wp_localize_script(
        'nest-well-main',
        'nestWellData',
        array(
            'restUrl'    => esc_url_raw( rest_url() ),
            'restNonce'  => wp_create_nonce( 'wp_rest' ),
            'ajaxUrl'    => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
            'themeUrl'   => NEST_WELL_URI,
            'homeUrl'    => esc_url( home_url( '/' ) ),
        )
    );

    // Comments script (only on singular with comments open)
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'nest_well_enqueue_assets' );

/**
 * Enqueue block editor styles.
 */
function nest_well_enqueue_editor_assets() {
    wp_enqueue_style(
        'nest-well-editor',
        NEST_WELL_URI . '/assets/css/editor.css',
        array(),
        NEST_WELL_VERSION
    );
}
add_action( 'enqueue_block_editor_assets', 'nest_well_enqueue_editor_assets' );

/**
 * Add defer attribute to non-critical scripts.
 */
function nest_well_defer_scripts( $tag, $handle, $src ) {
    $defer_scripts = array( 'nest-well-main', 'nest-well-affiliate', 'nest-well-discovery' );

    if ( in_array( $handle, $defer_scripts, true ) ) {
        return str_replace( ' src=', ' defer src=', $tag );
    }

    return $tag;
}
add_filter( 'script_loader_tag', 'nest_well_defer_scripts', 10, 3 );

/**
 * Preconnect and preload critical fonts via wp_head.
 */
function nest_well_preload_fonts() {
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php
}
add_action( 'wp_head', 'nest_well_preload_fonts', 1 );

/**
 * Apply the user's persisted theme preference before CSS loads.
 * Runs synchronously in <head> to avoid a flash of light theme on
 * dark-mode users.
 */
function nest_well_theme_preference_bootstrap() {
    ?>
    <script>(function(){try{var p=localStorage.getItem('nest-well-theme');if(p==='dark'||p==='light'){document.documentElement.setAttribute('data-theme',p);}}catch(e){}})();</script>
    <?php
}
add_action( 'wp_head', 'nest_well_theme_preference_bootstrap', 0 );

/**
 * Remove WordPress bloat from wp_head.
 */
function nest_well_remove_wp_bloat() {
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'nest_well_remove_wp_bloat' );

/**
 * Add lazy loading to content images.
 */
function nest_well_lazy_load_content_images( $content ) {
    if ( ! is_singular() ) {
        return $content;
    }

    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function( $matches ) {
            if ( strpos( $matches[1], 'loading=' ) === false ) {
                return '<img' . $matches[1] . ' loading="lazy">';
            }
            return $matches[0];
        },
        $content
    );

    return $content;
}
add_filter( 'the_content', 'nest_well_lazy_load_content_images' );

/**
 * Custom excerpt length.
 */
function nest_well_excerpt_length( $length ) {
    return 25;
}
add_filter( 'excerpt_length', 'nest_well_excerpt_length' );

/**
 * Custom excerpt more string.
 */
function nest_well_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'nest_well_excerpt_more' );

/**
 * Add body classes for template detection.
 */
function nest_well_body_classes( $classes ) {
    if ( is_singular( 'post' ) ) {
        $classes[] = 'single-article';
    }

    if ( has_post_thumbnail() ) {
        $classes[] = 'has-featured-image';
    }

    // Add review class if post has a review score
    if ( is_singular() && get_post_meta( get_the_ID(), '_review_score', true ) ) {
        $classes[] = 'is-review';
    }

    return $classes;
}
add_action( 'body_class', 'nest_well_body_classes' );

/**
 * Estimated read time for post content.
 */
function nest_well_get_read_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $stored = get_post_meta( $post_id, '_read_time', true );
    if ( $stored ) {
        return (int) $stored;
    }

    $content    = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    $read_time  = max( 1, (int) ceil( $word_count / 200 ) );

    return $read_time;
}

/**
 * Get star rating HTML.
 *
 * @param float $score   Score out of 10.
 * @param int   $max     Maximum stars (default 5).
 * @return string HTML star rating.
 */
function nest_well_star_rating_html( $score, $max = 5 ) {
    if ( ! $score ) {
        return '';
    }

    $stars_filled = round( ( $score / 10 ) * $max );
    $stars_empty  = $max - $stars_filled;
    $html         = '<span class="star-rating" aria-label="' . esc_attr( $score ) . ' out of 10">';

    for ( $i = 0; $i < $stars_filled; $i++ ) {
        $html .= '<span class="star star--filled" aria-hidden="true">&#9733;</span>';
    }

    for ( $i = 0; $i < $stars_empty; $i++ ) {
        $html .= '<span class="star star--empty" aria-hidden="true">&#9733;</span>';
    }

    $html .= '</span>';

    return $html;
}

/**
 * Get badge label from badge slug.
 *
 * @param string $badge Badge slug.
 * @return string Badge label.
 */
function nest_well_get_badge_label( $badge ) {
    $badges = array(
        'editors-choice'  => "Editor&#39;s Choice",
        'best-value'      => 'Best Value',
        'budget-pick'     => 'Budget Pick',
        'premium-pick'    => 'Premium Pick',
        'staff-favorite'  => 'Staff Favorite',
    );

    return isset( $badges[ $badge ] ) ? $badges[ $badge ] : '';
}

/**
 * Get breadcrumb HTML.
 *
 * @return string Breadcrumb HTML.
 */
function nest_well_breadcrumbs() {
    if ( is_front_page() ) {
        return '';
    }

    $breadcrumbs   = array();
    $breadcrumbs[] = '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'nest-and-well' ) . '</a>';

    if ( is_category() ) {
        $breadcrumbs[] = esc_html( single_cat_title( '', false ) );
    } elseif ( is_tag() ) {
        $breadcrumbs[] = esc_html__( 'Tag', 'nest-and-well' ) . ': ' . esc_html( single_tag_title( '', false ) );
    } elseif ( is_single() ) {
        $categories = get_the_category();
        if ( $categories ) {
            $cat           = $categories[0];
            $breadcrumbs[] = '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
        }
        $breadcrumbs[] = esc_html( get_the_title() );
    } elseif ( is_page() ) {
        $breadcrumbs[] = esc_html( get_the_title() );
    } elseif ( is_search() ) {
        $breadcrumbs[] = esc_html__( 'Search Results', 'nest-and-well' );
    } elseif ( is_404() ) {
        $breadcrumbs[] = esc_html__( 'Page Not Found', 'nest-and-well' );
    }

    $separator = '<span class="breadcrumb-sep" aria-hidden="true">&rsaquo;</span>';

    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'nest-and-well' ) . '">';
    echo implode( ' ' . $separator . ' ', $breadcrumbs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '</nav>';
}

/**
 * Remove Jetpack-injected Related Posts from the post content.
 *
 * Jetpack appends its Related Posts block via the_content filter.
 * We have a custom Related Reviews section at the bottom of single.php
 * so the Jetpack one above the tags is redundant.
 */
add_filter( 'jetpack_relatedposts_filter_enabled_for_request', '__return_false' );
