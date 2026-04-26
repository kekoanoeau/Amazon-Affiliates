/**
 * Nest & Well — Affiliate Link Helpers & Click Tracking
 * Tracks affiliate link clicks for analytics and UX improvements.
 *
 * No jQuery dependency. Vanilla JS only.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

(function () {
  'use strict';

  // ============================================================
  // Affiliate Link Click Tracking
  // ============================================================

  /**
   * Track an affiliate link click.
   * Sends to GA4 if available, falls back to console in dev mode.
   *
   * @param {string} productName  The product being clicked.
   * @param {string} url          The destination URL.
   * @param {string} linkType     Type of affiliate link (amazon, etc.).
   */
  function trackAffiliateClick(productName, url, linkType) {
    // Google Analytics 4 event
    if (typeof gtag === 'function') {
      gtag('event', 'affiliate_click', {
        event_category: 'affiliate',
        event_label: productName || url,
        affiliate_type: linkType || 'amazon',
        destination_url: url,
      });
    }

    // Custom event for any listener
    var event = new CustomEvent('nestwell:affiliate_click', {
      bubbles: true,
      detail: {
        productName: productName,
        url: url,
        linkType: linkType,
      },
    });
    document.dispatchEvent(event);
  }

  /**
   * Initialize tracking on all affiliate links.
   */
  function initAffiliateTracking() {
    // Select all affiliate-related links
    var affiliateSelectors = [
      '.amazon-affiliate-link',
      '.affiliate-link',
      '.product-box__cta',
      '.comparison-table__btn',
      '.shortcode-buy-button',
      '[rel~="sponsored"]',
    ];

    var links = document.querySelectorAll(affiliateSelectors.join(', '));

    links.forEach(function (link) {
      // Dedup: the discovery feed re-runs this after appending new cards;
      // skip links that already have a click listener bound.
      if (link.dataset.nwTracked) return;
      link.dataset.nwTracked = '1';

      link.addEventListener('click', function (e) {
        var productName = link.dataset.product || link.textContent.trim();
        var url = link.href;
        var linkType = link.dataset.affiliate || 'amazon';

        trackAffiliateClick(productName, url, linkType);
      });
    });
  }

  // ============================================================
  // Dynamic Affiliate Link Enhancement
  // ============================================================

  /**
   * Add nofollow/noopener to any links pointing to amazon.com
   * that may have been added via the block editor without proper attributes.
   */
  function enhanceAmazonLinks() {
    var allLinks = document.querySelectorAll('a[href*="amazon.com"]');

    allLinks.forEach(function (link) {
      var rel = link.getAttribute('rel') || '';

      // Ensure nofollow
      if (rel.indexOf('nofollow') === -1) {
        rel = (rel ? rel + ' ' : '') + 'nofollow';
      }

      // Ensure noopener
      if (rel.indexOf('noopener') === -1) {
        rel = (rel ? rel + ' ' : '') + 'noopener';
      }

      // Ensure sponsored
      if (rel.indexOf('sponsored') === -1) {
        rel = (rel ? rel + ' ' : '') + 'sponsored';
      }

      link.setAttribute('rel', rel.trim());

      // Ensure opens in new tab
      if (!link.getAttribute('target')) {
        link.setAttribute('target', '_blank');
      }

      // Add affiliate class if not present
      if (!link.classList.contains('amazon-affiliate-link')) {
        link.classList.add('amazon-affiliate-link');
      }
    });
  }

  // ============================================================
  // Price Last Checked Date Auto-Update
  // ============================================================

  /**
   * Update "Price last checked" text to show today's date.
   */
  function updatePriceCheckDates() {
    var disclaimers = document.querySelectorAll('.product-box__disclaimer');

    if (!disclaimers.length) return;

    var today = new Date();
    var options = { year: 'numeric', month: 'long', day: 'numeric' };
    var dateStr = today.toLocaleDateString('en-US', options);

    disclaimers.forEach(function (el) {
      var text = el.textContent;
      // Replace date pattern with today's date
      el.textContent = text.replace(
        /Price last checked [A-Za-z]+ \d+, \d{4}/,
        'Price last checked ' + dateStr
      );
    });
  }

  // ============================================================
  // Disclosure Auto-Show
  // ============================================================

  /**
   * Ensure affiliate disclosure is visible above the fold on article pages.
   * If the disclosure is below viewport, scroll indicator appears.
   */
  function initDisclosureVisibility() {
    var disclosure = document.querySelector('.article-head__disclosure');
    if (!disclosure) return;

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            disclosure.classList.add('disclosure--seen');
            observer.unobserve(disclosure);
          }
        });
      },
      { threshold: 0.5 }
    );

    observer.observe(disclosure);
  }

  // ============================================================
  // Init
  // ============================================================
  function init() {
    enhanceAmazonLinks();
    initAffiliateTracking();
    updatePriceCheckDates();
    initDisclosureVisibility();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Re-bind when the discovery feed appends new cards via REST.
  document.addEventListener('nestwell:discovery_appended', function () {
    enhanceAmazonLinks();
    initAffiliateTracking();
  });

})();
