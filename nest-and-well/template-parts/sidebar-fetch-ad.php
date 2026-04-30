<?php
/**
 * Sidebar — Sponsored Fetch Referral Card
 *
 * Sponsored partner card linking to the site owner's Fetch referral.
 * rel="sponsored" satisfies FTC + Google's affiliate-link policy and
 * is auto-tracked by assets/js/affiliate.js (the [rel~="sponsored"]
 * selector picks it up alongside Amazon links).
 *
 * @package nest-and-well
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$fetch_url  = 'https://referral.fetch.com/vvv3/referralqr?code=GQP2JW';
$fetch_code = 'GQP2JW';
?>

<aside class="sponsored-ad sponsored-ad--fetch" aria-label="<?php esc_attr_e( 'Sponsored partner', 'nest-and-well' ); ?>">

    <span class="sponsored-ad__eyebrow"><?php esc_html_e( 'Sponsored', 'nest-and-well' ); ?></span>

    <a href="<?php echo esc_url( $fetch_url ); ?>"
       class="sponsored-ad__media-link affiliate-link"
       target="_blank"
       rel="nofollow noopener sponsored"
       data-affiliate="fetch"
       data-product="Fetch Rewards Referral"
       aria-hidden="true"
       tabindex="-1">
        <div class="sponsored-ad__media">
            <!-- Replace this inline mark with the official Fetch SVG/PNG if you have it.
                 The wordmark below stands in for now and stays brand-recognisable. -->
            <span class="sponsored-ad__wordmark" aria-hidden="true">
                <svg class="sponsored-ad__paw" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <ellipse cx="6" cy="9" rx="2" ry="2.6"/>
                    <ellipse cx="11" cy="6.5" rx="2" ry="2.8"/>
                    <ellipse cx="17" cy="6.5" rx="2" ry="2.8"/>
                    <ellipse cx="22" cy="9" rx="2" ry="2.6" transform="translate(-3.6 0)"/>
                    <path d="M14 11c-3.3 0-6 3-6 6 0 1.7 1.3 3 3 3 1 0 1.6-.4 2.4-.9.5-.3.9-.6 1.6-.6.7 0 1.1.3 1.6.6.8.5 1.4.9 2.4.9 1.7 0 3-1.3 3-3 0-3-2.7-6-6-6z" transform="translate(-2 -1)"/>
                </svg>
                <span class="sponsored-ad__wordmark-text">fetch</span>
            </span>
        </div>
    </a>

    <div class="sponsored-ad__body">
        <h4 class="sponsored-ad__headline">
            <?php esc_html_e( 'Get paid to scan receipts', 'nest-and-well' ); ?>
        </h4>
        <p class="sponsored-ad__copy">
            <?php esc_html_e( 'Snap any grocery, takeout, or retail receipt with the Fetch app — earn points and redeem for gift cards.', 'nest-and-well' ); ?>
        </p>

        <ul class="sponsored-ad__bullets">
            <li><?php esc_html_e( 'Free to use, no purchase required', 'nest-and-well' ); ?></li>
            <li><?php esc_html_e( 'Works with any retailer', 'nest-and-well' ); ?></li>
            <li><?php esc_html_e( 'Cash out for Amazon, Target, Starbucks &amp; more', 'nest-and-well' ); ?></li>
        </ul>

        <div class="sponsored-ad__bonus">
            <span class="sponsored-ad__bonus-label">
                <?php esc_html_e( 'Use code', 'nest-and-well' ); ?>
            </span>
            <code class="sponsored-ad__bonus-code"><?php echo esc_html( $fetch_code ); ?></code>
            <span class="sponsored-ad__bonus-note">
                <?php esc_html_e( 'for bonus points', 'nest-and-well' ); ?>
            </span>
        </div>

        <a href="<?php echo esc_url( $fetch_url ); ?>"
           class="sponsored-ad__cta affiliate-link"
           target="_blank"
           rel="nofollow noopener sponsored"
           data-affiliate="fetch"
           data-product="Fetch Rewards Referral">
            <?php esc_html_e( 'Get the App', 'nest-and-well' ); ?> &#x2197;
        </a>

        <p class="sponsored-ad__disclaimer">
            <?php esc_html_e( 'We may earn rewards when you sign up.', 'nest-and-well' ); ?>
        </p>
    </div>

</aside><!-- .sponsored-ad--fetch -->
