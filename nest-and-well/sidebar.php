<?php
/**
 * Sidebar Template
 * Widget area for single posts and pages.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<aside id="secondary" class="widget-area sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Article sidebar', 'nest-and-well' ); ?>">

    <!-- Email Signup Widget -->
    <?php if ( is_active_sidebar( 'sidebar-email-signup' ) ) : ?>
    <div class="sidebar__section sidebar__section--email">
        <?php dynamic_sidebar( 'sidebar-email-signup' ); ?>
    </div>
    <?php else : ?>
    <div class="sidebar__section sidebar__section--email">
        <div class="sidebar-email-default">
            <h4 class="sidebar-email-default__title">
                <?php esc_html_e( 'Get Our Best Picks', 'nest-and-well' ); ?>
            </h4>
            <p class="sidebar-email-default__desc">
                <?php esc_html_e( 'Weekly reviews and deals, no spam.', 'nest-and-well' ); ?>
            </p>
            <form class="sidebar-email-form js-subscribe-form" data-source="sidebar" novalidate>
                <label for="sidebar-email" class="screen-reader-text">
                    <?php esc_html_e( 'Email address', 'nest-and-well' ); ?>
                </label>
                <input type="email"
                       id="sidebar-email"
                       name="email"
                       class="sidebar-email-form__input"
                       placeholder="<?php esc_attr_e( 'Your email address', 'nest-and-well' ); ?>"
                       autocomplete="email"
                       required>
                <button type="submit" class="sidebar-email-form__submit btn btn--sage">
                    <?php esc_html_e( 'Subscribe', 'nest-and-well' ); ?>
                </button>
                <p class="sidebar-email-form__feedback" role="status" aria-live="polite" hidden></p>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Top Picks Widget -->
    <div class="sidebar__section sidebar__section--top-picks">
        <?php if ( is_active_sidebar( 'sidebar-top-picks' ) ) : ?>
            <?php dynamic_sidebar( 'sidebar-top-picks' ); ?>
        <?php elseif ( is_singular( 'post' ) ) : ?>
        <div class="widget widget--top-picks">
            <h4 class="widget-title"><?php esc_html_e( 'Top Picks', 'nest-and-well' ); ?></h4>
            <?php nest_well_top_picks_default( get_the_ID() ); ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sponsored Partner — Fetch Referral -->
    <div class="sidebar__section sidebar__section--sponsored">
        <?php get_template_part( 'template-parts/sidebar-fetch-ad' ); ?>
    </div>

    <!-- Explore Category -->
    <div class="sidebar__section sidebar__section--explore">
        <?php get_template_part( 'template-parts/explore-category' ); ?>
    </div>

    <!-- Deal Alert Widget -->
    <?php if ( is_active_sidebar( 'sidebar-deal-alert' ) ) : ?>
    <div class="sidebar__section sidebar__section--deal-alert">
        <?php dynamic_sidebar( 'sidebar-deal-alert' ); ?>
    </div>
    <?php endif; ?>

    <!-- Main Sidebar (catch-all) -->
    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
    <div class="sidebar__section">
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
    </div>
    <?php endif; ?>

</aside><!-- #secondary -->
