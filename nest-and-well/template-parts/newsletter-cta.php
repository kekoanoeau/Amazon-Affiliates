<?php
/**
 * Inline end-of-article newsletter CTA.
 *
 * Posts to the same AJAX handler the sidebar form uses
 * (see inc/email-capture.php), with `source=article-end` so signups can
 * be attributed to in-article placement.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$cats         = get_the_category();
$primary_name = ( ! empty( $cats ) && isset( $cats[0]->name ) ) ? $cats[0]->name : '';
?>
<aside class="newsletter-cta newsletter-cta--inline" aria-labelledby="newsletter-cta-title">
    <div class="newsletter-cta__inner">
        <div class="newsletter-cta__copy">
            <p class="newsletter-cta__eyebrow"><?php esc_html_e( 'The shortlist', 'nest-and-well' ); ?></p>
            <h3 id="newsletter-cta-title" class="newsletter-cta__title">
                <?php
                if ( $primary_name ) {
                    /* translators: %s: category name */
                    printf( esc_html__( 'Get the best new %s reviews in your inbox.', 'nest-and-well' ), esc_html( strtolower( $primary_name ) ) );
                } else {
                    esc_html_e( 'Get the best new reviews in your inbox.', 'nest-and-well' );
                }
                ?>
            </h3>
            <p class="newsletter-cta__subtitle">
                <?php esc_html_e( 'One thoughtful email a week. Real testing, no fluff, easy unsubscribe.', 'nest-and-well' ); ?>
            </p>
        </div>
        <form class="newsletter-cta__form js-subscribe-form"
              data-source="article-end"
              method="post"
              action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
              novalidate>
            <input type="hidden" name="action" value="nest_well_subscribe">
            <label class="screen-reader-text" for="newsletter-cta-email">
                <?php esc_html_e( 'Email address', 'nest-and-well' ); ?>
            </label>
            <input type="email"
                   id="newsletter-cta-email"
                   name="email"
                   class="newsletter-cta__input"
                   placeholder="<?php esc_attr_e( 'you@example.com', 'nest-and-well' ); ?>"
                   autocomplete="email"
                   required>
            <button type="submit" class="btn btn--sage newsletter-cta__submit">
                <?php esc_html_e( 'Subscribe', 'nest-and-well' ); ?>
            </button>
            <p class="sidebar-email-form__feedback" role="status" aria-live="polite" hidden></p>
        </form>
    </div>
</aside>
