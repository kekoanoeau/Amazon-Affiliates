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
             Newsletter band
             ============================================= -->
        <section class="footer-newsletter" aria-labelledby="footer-newsletter-title">
            <div class="footer-newsletter__inner container">
                <div class="footer-newsletter__copy">
                    <p class="footer-newsletter__eyebrow"><?php esc_html_e( 'Stay in the know', 'nest-and-well' ); ?></p>
                    <h2 id="footer-newsletter-title" class="footer-newsletter__title">
                        <?php esc_html_e( 'Smart picks. Honest reviews. No spam.', 'nest-and-well' ); ?>
                    </h2>
                    <p class="footer-newsletter__subtitle">
                        <?php esc_html_e( 'Join thousands of readers getting our best home, wellness, and gift recommendations every Sunday.', 'nest-and-well' ); ?>
                    </p>
                </div>
                <form class="footer-newsletter__form js-subscribe-form"
                      data-source="footer"
                      method="post"
                      action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
                      novalidate>
                    <input type="hidden" name="action" value="nest_well_subscribe">
                    <label class="screen-reader-text" for="footer-newsletter-email">
                        <?php esc_html_e( 'Email address', 'nest-and-well' ); ?>
                    </label>
                    <input type="email"
                           id="footer-newsletter-email"
                           name="email"
                           class="footer-newsletter__input"
                           placeholder="<?php esc_attr_e( 'you@example.com', 'nest-and-well' ); ?>"
                           autocomplete="email"
                           required>
                    <button type="submit" class="btn btn--sage footer-newsletter__submit">
                        <?php esc_html_e( 'Subscribe', 'nest-and-well' ); ?>
                    </button>
                    <p class="sidebar-email-form__feedback" role="status" aria-live="polite" hidden></p>
                </form>
            </div>
        </section>

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

    <?php get_template_part( 'template-parts/nav/bottom-tabs' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
