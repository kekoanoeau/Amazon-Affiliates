<?php
/**
 * Site Footer Template
 *
 * Three-zone footer:
 *   Zone 1: Footer category stripe (repeats 5-stripe nav, smaller)
 *   Zone 2: Main footer (4-column grid with logo, links, about & legal)
 *   Zone 3: Legal footer bar (copyright + affiliate notice)
 *
 * @package nest-and-well
 * @since 1.0.0
 */
?>

    </div><!-- #content -->

    <footer id="colophon" class="site-footer" role="contentinfo">

        <!-- =============================================
             ZONE 1: Footer Category Stripe
             ============================================= -->
        <div class="footer-stripe-nav" aria-label="<?php esc_attr_e( 'Footer category navigation', 'nest-and-well' ); ?>">
            <?php
            $stripes = array(
                1 => array(
                    'label' => get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ),
                    'url'   => get_theme_mod( 'nest_well_stripe_1_url', '/smart-home/' ),
                    'color' => 'var(--forest)',
                ),
                2 => array(
                    'label' => get_theme_mod( 'nest_well_stripe_2_label', 'Wellness Tech' ),
                    'url'   => get_theme_mod( 'nest_well_stripe_2_url', '/wellness-tech/' ),
                    'color' => 'var(--sage)',
                ),
                3 => array(
                    'label' => get_theme_mod( 'nest_well_stripe_3_label', 'Home Beauty' ),
                    'url'   => get_theme_mod( 'nest_well_stripe_3_url', '/home-beauty/' ),
                    'color' => 'var(--moss)',
                ),
                4 => array(
                    'label' => get_theme_mod( 'nest_well_stripe_4_label', 'Gift Guides' ),
                    'url'   => get_theme_mod( 'nest_well_stripe_4_url', '/gift-guides/' ),
                    'color' => 'var(--amber)',
                ),
                5 => array(
                    'label' => get_theme_mod( 'nest_well_stripe_5_label', 'Deals' ),
                    'url'   => get_theme_mod( 'nest_well_stripe_5_url', '/deals/' ),
                    'color' => 'var(--clay)',
                ),
            );

            foreach ( $stripes as $stripe ) :
            ?>
            <a href="<?php echo esc_url( $stripe['url'] ); ?>"
               class="footer-stripe-nav__item"
               style="background-color: <?php echo esc_attr( $stripe['color'] ); ?>;">
                <?php echo esc_html( $stripe['label'] ); ?>
            </a>
            <?php endforeach; ?>
        </div><!-- .footer-stripe-nav -->

        <!-- =============================================
             ZONE 2: Main Footer Grid
             ============================================= -->
        <div class="footer-main">
            <div class="container">
                <div class="footer-main__grid">

                    <!-- Column 1: Brand & About -->
                    <div class="footer-main__col footer-main__col--brand">
                        <div class="footer-brand">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-brand__logo-link" rel="home">
                                <span class="footer-brand__name">Nest &amp; Well</span>
                            </a>
                            <p class="footer-brand__tagline">
                                <?php echo esc_html( get_theme_mod( 'nest_well_footer_tagline', 'Smart Home & Wellness, Thoughtfully Reviewed' ) ); ?>
                            </p>
                            <p class="footer-brand__about">
                                <?php echo wp_kses_post( get_theme_mod( 'nest_well_footer_about', 'We independently review smart home tech and wellness products so you can shop with confidence. Our team tests everything in real homes.' ) ); ?>
                            </p>
                            <?php nest_well_social_links_html( 'footer-social' ); ?>
                        </div>
                    </div><!-- .footer-main__col--brand -->

                    <!-- Column 2: Smart Home Links -->
                    <div class="footer-main__col footer-main__col--smart-home">
                        <h4 class="footer-main__col-heading">
                            <?php echo esc_html( get_theme_mod( 'nest_well_stripe_1_label', 'Smart Home' ) ); ?>
                        </h4>
                        <?php if ( is_active_sidebar( 'footer-smart-home' ) ) : ?>
                            <?php dynamic_sidebar( 'footer-smart-home' ); ?>
                        <?php else : ?>
                        <ul class="footer-main__link-list">
                            <li><a href="<?php echo esc_url( home_url( '/smart-speakers/' ) ); ?>"><?php esc_html_e( 'Smart Speakers', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/smart-displays/' ) ); ?>"><?php esc_html_e( 'Smart Displays', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/smart-lighting/' ) ); ?>"><?php esc_html_e( 'Smart Lighting', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/smart-security/' ) ); ?>"><?php esc_html_e( 'Smart Security', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/smart-thermostats/' ) ); ?>"><?php esc_html_e( 'Smart Thermostats', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/robot-vacuums/' ) ); ?>"><?php esc_html_e( 'Robot Vacuums', 'nest-and-well' ); ?></a></li>
                        </ul>
                        <?php endif; ?>
                    </div><!-- .footer-main__col--smart-home -->

                    <!-- Column 3: Wellness Links -->
                    <div class="footer-main__col footer-main__col--wellness">
                        <h4 class="footer-main__col-heading">
                            <?php echo esc_html( get_theme_mod( 'nest_well_stripe_2_label', 'Wellness' ) ); ?>
                        </h4>
                        <?php if ( is_active_sidebar( 'footer-wellness' ) ) : ?>
                            <?php dynamic_sidebar( 'footer-wellness' ); ?>
                        <?php else : ?>
                        <ul class="footer-main__link-list">
                            <li><a href="<?php echo esc_url( home_url( '/fitness-trackers/' ) ); ?>"><?php esc_html_e( 'Fitness Trackers', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/sleep-tech/' ) ); ?>"><?php esc_html_e( 'Sleep Tech', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/air-purifiers/' ) ); ?>"><?php esc_html_e( 'Air Purifiers', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/massage-guns/' ) ); ?>"><?php esc_html_e( 'Massage Guns', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/smart-scales/' ) ); ?>"><?php esc_html_e( 'Smart Scales', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/meditation-devices/' ) ); ?>"><?php esc_html_e( 'Meditation Devices', 'nest-and-well' ); ?></a></li>
                        </ul>
                        <?php endif; ?>
                    </div><!-- .footer-main__col--wellness -->

                    <!-- Column 4: About & Legal -->
                    <div class="footer-main__col footer-main__col--legal">
                        <h4 class="footer-main__col-heading"><?php esc_html_e( 'About & Legal', 'nest-and-well' ); ?></h4>
                        <?php if ( is_active_sidebar( 'footer-about-legal' ) ) : ?>
                            <?php dynamic_sidebar( 'footer-about-legal' ); ?>
                        <?php else : ?>
                        <ul class="footer-main__link-list">
                            <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/affiliate-disclosure/' ) ); ?>"><?php esc_html_e( 'Affiliate Disclosure', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/terms-of-use/' ) ); ?>"><?php esc_html_e( 'Terms of Use', 'nest-and-well' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/how-we-review/' ) ); ?>"><?php esc_html_e( 'How We Review', 'nest-and-well' ); ?></a></li>
                        </ul>
                        <?php endif; ?>
                    </div><!-- .footer-main__col--legal -->

                </div><!-- .footer-main__grid -->
            </div><!-- .container -->
        </div><!-- .footer-main -->

        <!-- =============================================
             ZONE 3: Legal Footer Bar
             ============================================= -->
        <div class="footer-legal">
            <div class="footer-legal__inner container">
                <span class="footer-legal__copyright">
                    <?php echo wp_kses_post( get_theme_mod( 'nest_well_copyright_text', '&copy; 2026 Nest &amp; Well. All rights reserved.' ) ); ?>
                </span>
                <span class="footer-legal__affiliate-notice">
                    <?php esc_html_e( 'Amazon affiliate links — we earn commissions at no cost to you.', 'nest-and-well' ); ?>
                </span>
                <nav class="footer-legal__links" aria-label="<?php esc_attr_e( 'Legal links', 'nest-and-well' ); ?>">
                    <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'nest-and-well' ); ?></a>
                    <span aria-hidden="true">|</span>
                    <a href="<?php echo esc_url( home_url( '/terms-of-use/' ) ); ?>"><?php esc_html_e( 'Terms', 'nest-and-well' ); ?></a>
                    <span aria-hidden="true">|</span>
                    <a href="<?php echo esc_url( home_url( '/affiliate-disclosure/' ) ); ?>"><?php esc_html_e( 'Affiliate Disclosure', 'nest-and-well' ); ?></a>
                </nav>
            </div><!-- .footer-legal__inner -->
        </div><!-- .footer-legal -->

    </footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
