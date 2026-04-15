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
