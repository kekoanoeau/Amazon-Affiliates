<?php
/**
 * WordPress Customizer Options for Nest & Well
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Customizer settings, sections, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function nest_well_customizer_register( $wp_customize ) {

    // =========================================================
    // PANEL: Nest & Well Settings
    // =========================================================
    $wp_customize->add_panel(
        'nest_well_settings',
        array(
            'title'       => esc_html__( 'Nest & Well Settings', 'nest-and-well' ),
            'description' => esc_html__( 'Customize the Nest & Well theme settings.', 'nest-and-well' ),
            'priority'    => 130,
        )
    );

    // =========================================================
    // SECTION: Brand Colors
    // =========================================================
    $wp_customize->add_section(
        'nest_well_colors',
        array(
            'title'    => esc_html__( 'Brand Colors', 'nest-and-well' ),
            'panel'    => 'nest_well_settings',
            'priority' => 10,
        )
    );

    // Primary color (--forest)
    $wp_customize->add_setting(
        'nest_well_color_forest',
        array(
            'default'           => '#1A3C34',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'nest_well_color_forest',
            array(
                'label'   => esc_html__( 'Primary Color (Forest)', 'nest-and-well' ),
                'section' => 'nest_well_colors',
            )
        )
    );

    // Accent color (--amber)
    $wp_customize->add_setting(
        'nest_well_color_amber',
        array(
            'default'           => '#E8A23A',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'nest_well_color_amber',
            array(
                'label'   => esc_html__( 'Accent Color (Amber)', 'nest-and-well' ),
                'section' => 'nest_well_colors',
            )
        )
    );

    // CTA color (--sage)
    $wp_customize->add_setting(
        'nest_well_color_sage',
        array(
            'default'           => '#4A7C59',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'nest_well_color_sage',
            array(
                'label'   => esc_html__( 'CTA Color (Sage)', 'nest-and-well' ),
                'section' => 'nest_well_colors',
            )
        )
    );

    // =========================================================
    // SECTION: Header & Navigation
    // =========================================================
    $wp_customize->add_section(
        'nest_well_header',
        array(
            'title'    => esc_html__( 'Header & Navigation', 'nest-and-well' ),
            'panel'    => 'nest_well_settings',
            'priority' => 20,
        )
    );

    // Show/hide top utility bar
    $wp_customize->add_setting(
        'nest_well_show_utility_bar',
        array(
            'default'           => true,
            'sanitize_callback' => 'nest_well_sanitize_checkbox',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'nest_well_show_utility_bar',
        array(
            'label'   => esc_html__( 'Show Top Utility Bar', 'nest-and-well' ),
            'section' => 'nest_well_header',
            'type'    => 'checkbox',
        )
    );

    // 5 Category Stripes
    $stripes = array(
        1 => array( 'label' => 'Smart Home',    'url' => '/smart-home/' ),
        2 => array( 'label' => 'Wellness Tech', 'url' => '/wellness-tech/' ),
        3 => array( 'label' => 'Home Beauty',   'url' => '/home-beauty/' ),
        4 => array( 'label' => 'Gift Guides',   'url' => '/gift-guides/' ),
        5 => array( 'label' => 'Deals',         'url' => '/deals/' ),
    );

    foreach ( $stripes as $num => $stripe ) {
        // Stripe label
        $wp_customize->add_setting(
            "nest_well_stripe_{$num}_label",
            array(
                'default'           => $stripe['label'],
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
            )
        );
        $wp_customize->add_control(
            "nest_well_stripe_{$num}_label",
            array(
                /* translators: %d: stripe number */
                'label'   => sprintf( esc_html__( 'Stripe %d Label', 'nest-and-well' ), $num ),
                'section' => 'nest_well_header',
                'type'    => 'text',
            )
        );

        // Stripe URL
        $wp_customize->add_setting(
            "nest_well_stripe_{$num}_url",
            array(
                'default'           => $stripe['url'],
                'sanitize_callback' => 'sanitize_url',
                'transport'         => 'postMessage',
            )
        );
        $wp_customize->add_control(
            "nest_well_stripe_{$num}_url",
            array(
                /* translators: %d: stripe number */
                'label'   => sprintf( esc_html__( 'Stripe %d URL', 'nest-and-well' ), $num ),
                'section' => 'nest_well_header',
                'type'    => 'url',
            )
        );
    }

    // =========================================================
    // SECTION: Affiliate Settings
    // =========================================================
    $wp_customize->add_section(
        'nest_well_affiliate',
        array(
            'title'    => esc_html__( 'Affiliate Settings', 'nest-and-well' ),
            'panel'    => 'nest_well_settings',
            'priority' => 30,
        )
    );

    // Amazon Associates Tracking ID
    $wp_customize->add_setting(
        'nest_well_amazon_tracking_id',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'nest_well_amazon_tracking_id',
        array(
            'label'       => esc_html__( 'Amazon Associates Tracking ID', 'nest-and-well' ),
            'description' => esc_html__( 'Your Amazon Associates tag (e.g., nestandwell-20)', 'nest-and-well' ),
            'section'     => 'nest_well_affiliate',
            'type'        => 'text',
        )
    );

    // Affiliate disclosure text
    $wp_customize->add_setting(
        'nest_well_disclosure_text',
        array(
            'default'           => 'Disclosure: We independently review everything we recommend. We may earn a commission if you buy through our links — at no extra cost to you. Our editorial opinions are never influenced by commissions.',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    $wp_customize->add_control(
        'nest_well_disclosure_text',
        array(
            'label'   => esc_html__( 'Affiliate Disclosure Text', 'nest-and-well' ),
            'section' => 'nest_well_affiliate',
            'type'    => 'textarea',
        )
    );

    // Show/hide disclosure on homepage
    $wp_customize->add_setting(
        'nest_well_show_disclosure_homepage',
        array(
            'default'           => true,
            'sanitize_callback' => 'nest_well_sanitize_checkbox',
        )
    );
    $wp_customize->add_control(
        'nest_well_show_disclosure_homepage',
        array(
            'label'   => esc_html__( 'Show Disclosure Banner on Homepage', 'nest-and-well' ),
            'section' => 'nest_well_affiliate',
            'type'    => 'checkbox',
        )
    );

    // =========================================================
    // SECTION: Homepage
    // =========================================================
    $wp_customize->add_section(
        'nest_well_homepage',
        array(
            'title'    => esc_html__( 'Homepage', 'nest-and-well' ),
            'panel'    => 'nest_well_settings',
            'priority' => 40,
        )
    );

    // Brand tagline (under wordmark on homepage + hero eyebrow)
    $wp_customize->add_setting(
        'nest_well_brand_tagline',
        array(
            'default'           => 'Shop Smarter. Live Better.',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'nest_well_brand_tagline',
        array(
            'label'       => esc_html__( 'Brand Tagline', 'nest-and-well' ),
            'description' => esc_html__( 'Shown under the wordmark on the homepage and as the hero eyebrow.', 'nest-and-well' ),
            'section'     => 'nest_well_homepage',
            'type'        => 'text',
        )
    );

    // Hero headline
    $wp_customize->add_setting(
        'nest_well_hero_headline',
        array(
            'default'           => 'Smart Home & Wellness, Thoughtfully Reviewed',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'nest_well_hero_headline',
        array(
            'label'   => esc_html__( 'Hero Headline', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'text',
        )
    );

    // Hero subtext
    $wp_customize->add_setting(
        'nest_well_hero_subtext',
        array(
            'default'           => 'We test products in real homes so you can shop with confidence.',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'nest_well_hero_subtext',
        array(
            'label'   => esc_html__( 'Hero Subtext', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'textarea',
        )
    );

    // Primary CTA label
    $wp_customize->add_setting(
        'nest_well_cta_primary_label',
        array(
            'default'           => 'Browse Reviews',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'nest_well_cta_primary_label',
        array(
            'label'   => esc_html__( 'Primary CTA Label', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'text',
        )
    );

    // Primary CTA URL
    $wp_customize->add_setting(
        'nest_well_cta_primary_url',
        array(
            'default'           => '/smart-home/',
            'sanitize_callback' => 'sanitize_url',
        )
    );
    $wp_customize->add_control(
        'nest_well_cta_primary_url',
        array(
            'label'   => esc_html__( 'Primary CTA URL', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'url',
        )
    );

    // Secondary CTA label
    $wp_customize->add_setting(
        'nest_well_cta_secondary_label',
        array(
            'default'           => 'Start with Wellness',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'nest_well_cta_secondary_label',
        array(
            'label'   => esc_html__( 'Secondary CTA Label', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'text',
        )
    );

    // Secondary CTA URL
    $wp_customize->add_setting(
        'nest_well_cta_secondary_url',
        array(
            'default'           => '/wellness-tech/',
            'sanitize_callback' => 'sanitize_url',
        )
    );
    $wp_customize->add_control(
        'nest_well_cta_secondary_url',
        array(
            'label'   => esc_html__( 'Secondary CTA URL', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'url',
        )
    );

    // Show/hide featured category grid
    $wp_customize->add_setting(
        'nest_well_show_category_grid',
        array(
            'default'           => true,
            'sanitize_callback' => 'nest_well_sanitize_checkbox',
        )
    );
    $wp_customize->add_control(
        'nest_well_show_category_grid',
        array(
            'label'   => esc_html__( 'Show Featured Category Grid', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'checkbox',
        )
    );

    // ----- Discovery Feed (image-first masonry below hero) -----

    $wp_customize->add_setting(
        'nest_well_discovery_enabled',
        array(
            'default'           => true,
            'sanitize_callback' => 'nest_well_sanitize_checkbox',
        )
    );
    $wp_customize->add_control(
        'nest_well_discovery_enabled',
        array(
            'label'       => esc_html__( 'Show Discovery Feed below hero', 'nest-and-well' ),
            'description' => esc_html__( 'Image-first masonry browse module for product discovery.', 'nest-and-well' ),
            'section'     => 'nest_well_homepage',
            'type'        => 'checkbox',
        )
    );

    $wp_customize->add_setting(
        'nest_well_discovery_headline',
        array(
            'default'           => __( 'More to Explore', 'nest-and-well' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'nest_well_discovery_headline',
        array(
            'label'   => esc_html__( 'Discovery feed headline', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'nest_well_discovery_subtitle',
        array(
            'default'           => __( "Browse what we're testing this week.", 'nest-and-well' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'nest_well_discovery_subtitle',
        array(
            'label'   => esc_html__( 'Discovery feed subtitle', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'text',
        )
    );

    // Source: build choices from the existing top-level categories
    $source_choices = array( 'latest' => esc_html__( 'Latest posts', 'nest-and-well' ) );
    $source_cats    = get_categories( array( 'hide_empty' => false ) );
    if ( ! is_wp_error( $source_cats ) && ! empty( $source_cats ) ) {
        foreach ( $source_cats as $cat ) {
            $source_choices[ 'cat-' . $cat->term_id ] = $cat->name;
        }
    }
    $source_choices['mixed'] = esc_html__( 'Mixed', 'nest-and-well' );

    $wp_customize->add_setting(
        'nest_well_discovery_source',
        array(
            'default'           => 'latest',
            'sanitize_callback' => 'nest_well_discovery_sanitize_source',
        )
    );
    $wp_customize->add_control(
        'nest_well_discovery_source',
        array(
            'label'   => esc_html__( 'Discovery source', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'select',
            'choices' => $source_choices,
        )
    );

    $wp_customize->add_setting(
        'nest_well_discovery_density',
        array(
            'default'           => 'cozy',
            'sanitize_callback' => 'nest_well_discovery_sanitize_density',
        )
    );
    $wp_customize->add_control(
        'nest_well_discovery_density',
        array(
            'label'   => esc_html__( 'Card density', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'radio',
            'choices' => array(
                'cozy'  => esc_html__( 'Cozy (3 columns)', 'nest-and-well' ),
                'dense' => esc_html__( 'Dense (4 columns)', 'nest-and-well' ),
                'tight' => esc_html__( 'Tight (5 columns)', 'nest-and-well' ),
            ),
        )
    );

    $wp_customize->add_setting(
        'nest_well_discovery_per_page',
        array(
            'default'           => 18,
            'sanitize_callback' => 'nest_well_discovery_sanitize_per_page',
        )
    );
    $wp_customize->add_control(
        'nest_well_discovery_per_page',
        array(
            'label'   => esc_html__( 'Posts per scroll fetch', 'nest-and-well' ),
            'section' => 'nest_well_homepage',
            'type'    => 'select',
            'choices' => array(
                12 => '12',
                18 => '18',
                24 => '24',
                36 => '36',
            ),
        )
    );

    // =========================================================
    // SECTION: Email Capture (MailerLite)
    // =========================================================
    $wp_customize->add_section(
        'nest_well_email',
        array(
            'title'       => esc_html__( 'Email Capture', 'nest-and-well' ),
            'description' => esc_html__( 'Connect MailerLite to capture sidebar / footer signups. Without an API key, signups are stored locally as a fallback.', 'nest-and-well' ),
            'panel'       => 'nest_well_settings',
            'priority'    => 45,
        )
    );

    $wp_customize->add_setting(
        'nest_well_mailerlite_api_key',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'nest_well_mailerlite_api_key',
        array(
            'label'       => esc_html__( 'MailerLite API Key', 'nest-and-well' ),
            'description' => esc_html__( 'Found under MailerLite → Integrations → Developer API.', 'nest-and-well' ),
            'section'     => 'nest_well_email',
            'type'        => 'password',
        )
    );

    $wp_customize->add_setting(
        'nest_well_mailerlite_group_id',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'nest_well_mailerlite_group_id',
        array(
            'label'       => esc_html__( 'MailerLite Group / List ID', 'nest-and-well' ),
            'description' => esc_html__( 'Optional. Leave blank to add subscribers to your default list.', 'nest-and-well' ),
            'section'     => 'nest_well_email',
            'type'        => 'text',
        )
    );

    // =========================================================
    // SECTION: Footer
    // =========================================================
    $wp_customize->add_section(
        'nest_well_footer',
        array(
            'title'    => esc_html__( 'Footer', 'nest-and-well' ),
            'panel'    => 'nest_well_settings',
            'priority' => 50,
        )
    );

    // Footer tagline
    $wp_customize->add_setting(
        'nest_well_footer_tagline',
        array(
            'default'           => 'Smart Home & Wellness, Thoughtfully Reviewed',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'nest_well_footer_tagline',
        array(
            'label'   => esc_html__( 'Footer Tagline', 'nest-and-well' ),
            'section' => 'nest_well_footer',
            'type'    => 'text',
        )
    );

    // Copyright text
    $wp_customize->add_setting(
        'nest_well_copyright_text',
        array(
            'default'           => '© 2026 Nest & Well. All rights reserved.',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'nest_well_copyright_text',
        array(
            'label'   => esc_html__( 'Copyright Text', 'nest-and-well' ),
            'section' => 'nest_well_footer',
            'type'    => 'text',
        )
    );

    // Footer about blurb
    $wp_customize->add_setting(
        'nest_well_footer_about',
        array(
            'default'           => 'We independently review smart home tech and wellness products so you can shop with confidence. Our team tests everything in real homes.',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    $wp_customize->add_control(
        'nest_well_footer_about',
        array(
            'label'   => esc_html__( 'Footer About Blurb', 'nest-and-well' ),
            'section' => 'nest_well_footer',
            'type'    => 'textarea',
        )
    );

    // Social URLs
    $social_networks = array(
        'pinterest'  => 'Pinterest',
        'instagram'  => 'Instagram',
        'youtube'    => 'YouTube',
        'twitter'    => 'Twitter / X',
    );

    foreach ( $social_networks as $network => $label ) {
        $wp_customize->add_setting(
            "nest_well_social_{$network}",
            array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_url',
            )
        );
        $wp_customize->add_control(
            "nest_well_social_{$network}",
            array(
                /* translators: %s: social network name */
                'label'   => sprintf( esc_html__( '%s URL', 'nest-and-well' ), $label ),
                'section' => 'nest_well_footer',
                'type'    => 'url',
            )
        );
    }
}
add_action( 'customize_register', 'nest_well_customizer_register' );

/**
 * Sanitize checkbox values.
 *
 * @param bool $checked Whether checkbox is checked.
 * @return bool Sanitized value.
 */
function nest_well_sanitize_checkbox( $checked ) {
    return ( isset( $checked ) && (bool) $checked ) ? true : false;
}

/**
 * Output dynamic CSS based on Customizer settings.
 */
function nest_well_customizer_css() {
    $forest = get_theme_mod( 'nest_well_color_forest', '#1A3C34' );
    $amber  = get_theme_mod( 'nest_well_color_amber', '#E8A23A' );
    $sage   = get_theme_mod( 'nest_well_color_sage', '#4A7C59' );

    // Only output if values differ from defaults
    if ( '#1A3C34' !== $forest || '#E8A23A' !== $amber || '#4A7C59' !== $sage ) {
        ?>
        <style id="nest-well-customizer-css">
        :root {
            --forest: <?php echo esc_attr( $forest ); ?>;
            --amber:  <?php echo esc_attr( $amber ); ?>;
            --sage:   <?php echo esc_attr( $sage ); ?>;
        }
        </style>
        <?php
    }
}
add_action( 'wp_head', 'nest_well_customizer_css' );

/**
 * Binds JS handlers for selective refresh / live preview in Customizer.
 */
function nest_well_customizer_preview_js() {
    wp_enqueue_script(
        'nest-well-customizer-preview',
        NEST_WELL_URI . '/assets/js/customizer-preview.js',
        array( 'customize-preview' ),
        NEST_WELL_VERSION,
        true
    );
}
add_action( 'customize_preview_init', 'nest_well_customizer_preview_js' );
