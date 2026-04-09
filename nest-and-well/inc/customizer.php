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
