<?php
/**
 * Theme Setup
 * Registers theme support, menus, image sizes, and other core configuration.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function nest_well_setup() {
    // Make the theme available for translation.
    load_theme_textdomain( 'nest-and-well', NEST_WELL_DIR . '/languages' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable post thumbnails / featured images.
    add_theme_support( 'post-thumbnails' );

    // Enable HTML5 markup.
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Enable custom logo.
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 60,
            'width'       => 200,
            'flex-height' => true,
            'flex-width'  => true,
            'header-text' => array( 'site-title', 'site-description' ),
        )
    );

    // Enable Selective Refresh for Widgets in the Customizer.
    add_theme_support( 'customize-selective-refresh-widgets' );

    // Editor styles.
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor.css' );

    // Block editor styles.
    add_theme_support( 'wp-block-styles' );

    // Responsive embeds.
    add_theme_support( 'responsive-embeds' );

    // Wide and full alignment blocks.
    add_theme_support( 'align-wide' );

    // Post formats (optional).
    add_theme_support(
        'post-formats',
        array( 'aside', 'image', 'video', 'quote', 'link', 'gallery' )
    );

    // Feed links.
    add_theme_support( 'automatic-feed-links' );

    // Starter content.
    // (No starter content defined — theme is clean slate.)

    // Register navigation menus.
    register_nav_menus(
        array(
            'primary'    => esc_html__( 'Primary Navigation', 'nest-and-well' ),
            'footer'     => esc_html__( 'Footer Navigation', 'nest-and-well' ),
            'footer-legal' => esc_html__( 'Footer Legal Links', 'nest-and-well' ),
        )
    );
}
add_action( 'after_setup_theme', 'nest_well_setup' );

/**
 * Register custom image sizes.
 */
function nest_well_image_sizes() {
    // Article card thumbnail — 16:9 ratio
    add_image_size( 'card-thumbnail', 600, 400, true );

    // Hero image — 16:9 ratio
    add_image_size( 'hero-image', 1200, 675, true );

    // Product image — square
    add_image_size( 'product-image', 400, 400, true );
}
add_action( 'after_setup_theme', 'nest_well_image_sizes' );

/**
 * Add custom image sizes to the Media Library selector.
 *
 * @param array $sizes Registered image sizes.
 * @return array Modified image sizes.
 */
function nest_well_add_image_sizes_to_chooser( $sizes ) {
    return array_merge(
        $sizes,
        array(
            'card-thumbnail' => esc_html__( 'Card Thumbnail (600x400)', 'nest-and-well' ),
            'hero-image'     => esc_html__( 'Hero Image (1200x675)', 'nest-and-well' ),
            'product-image'  => esc_html__( 'Product Image (400x400)', 'nest-and-well' ),
        )
    );
}
add_filter( 'image_size_names_choose', 'nest_well_add_image_sizes_to_chooser' );

/**
 * Enable lazy loading globally.
 */
add_filter( 'wp_lazy_loading_enabled', '__return_true' );

/**
 * Register custom post meta fields.
 */
function nest_well_register_post_meta() {
    $post_meta_fields = array(
        '_review_score'   => array(
            'type'        => 'number',
            'description' => 'Review score (0-10)',
            'sanitize_callback' => function( $value ) {
                $value = (float) $value;
                return max( 0, min( 10, $value ) );
            },
        ),
        '_review_badge'   => array(
            'type'        => 'string',
            'description' => 'Review badge type',
            'sanitize_callback' => 'sanitize_text_field',
        ),
        '_product_price'  => array(
            'type'        => 'string',
            'description' => 'Product price string',
            'sanitize_callback' => 'sanitize_text_field',
        ),
        '_product_asin'   => array(
            'type'        => 'string',
            'description' => 'Amazon ASIN',
            'sanitize_callback' => 'sanitize_text_field',
        ),
        '_product_name'   => array(
            'type'        => 'string',
            'description' => 'Product being reviewed',
            'sanitize_callback' => 'sanitize_text_field',
        ),
        '_last_updated'   => array(
            'type'        => 'string',
            'description' => 'Last updated date',
            'sanitize_callback' => 'sanitize_text_field',
        ),
        '_read_time'      => array(
            'type'        => 'integer',
            'description' => 'Estimated read time in minutes',
            'sanitize_callback' => 'absint',
        ),
    );

    foreach ( $post_meta_fields as $key => $args ) {
        register_post_meta(
            'post',
            $key,
            array(
                'show_in_rest'      => true,
                'single'            => true,
                'type'              => $args['type'],
                'description'       => $args['description'],
                'sanitize_callback' => $args['sanitize_callback'],
                'auth_callback'     => function() {
                    return current_user_can( 'edit_posts' );
                },
            )
        );
    }
}
add_action( 'init', 'nest_well_register_post_meta' );

/**
 * Auto-calculate read time on post save.
 *
 * @param int $post_id Post ID.
 */
function nest_well_auto_calculate_read_time( $post_id ) {
    // Skip auto-saves and revisions.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Only calculate if not already set manually.
    $manual_time = get_post_meta( $post_id, '_read_time', true );
    if ( $manual_time ) {
        return;
    }

    $content    = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    $read_time  = max( 1, (int) ceil( $word_count / 200 ) );

    update_post_meta( $post_id, '_read_time', $read_time );
}
add_action( 'save_post', 'nest_well_auto_calculate_read_time' );

/**
 * Content width — maximum allowed width for content in this theme.
 */
function nest_well_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'nest_well_content_width', 720 );
}
add_action( 'after_setup_theme', 'nest_well_content_width', 0 );
