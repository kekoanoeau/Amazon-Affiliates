/**
 * Nest & Well — Discovery Feed Infinite Scroll
 *
 * IntersectionObserver scoped to .js-discovery-sentinel. Fetches
 * server-rendered HTML from /wp-json/nestwell/v1/discovery and appends
 * it directly to the masonry grid — preserving column-count flow with
 * zero layout race.
 *
 * Dispatches `nestwell:discovery_appended` after each successful append
 * so affiliate.js (and any external listeners) can re-bind tracking and
 * link enhancements on the new cards.
 *
 * Vanilla ES5 — no jQuery, no external libs.
 *
 * @package nest-and-well
 * @since   1.0.0
 */
(function () {
  'use strict';

  function initDiscoveryScroll() {
    var sentinel = document.querySelector('.js-discovery-sentinel');
    var grid     = document.getElementById('discovery-feed-grid');
    var loading  = document.querySelector('.js-discovery-loading');
    var endMsg   = document.querySelector('.js-discovery-end');

    if (!sentinel || !grid) return;
    if (!('IntersectionObserver' in window)) return; // graceful no-op for old browsers

    var isFetching = false;

    function fetchNextPage() {
      var page       = parseInt(sentinel.dataset.page, 10) || 2;
      var perPage    = parseInt(sentinel.dataset.perPage || '18', 10);
      var totalPages = parseInt(sentinel.dataset.totalPages || '1', 10);
      var source     = sentinel.dataset.source || 'latest';
      var density    = sentinel.dataset.density || 'cozy';

      if (isFetching || page > totalPages) return;
      isFetching = true;

      if (loading) {
        loading.hidden = false;
        loading.setAttribute('aria-busy', 'true');
      }

      var base = (window.nestWellData && window.nestWellData.restUrl)
        ? window.nestWellData.restUrl + 'nestwell/v1/discovery'
        : '/wp-json/nestwell/v1/discovery';

      var apiUrl = base
        + '?page=' + encodeURIComponent(page)
        + '&per_page=' + encodeURIComponent(perPage)
        + '&source=' + encodeURIComponent(source)
        + '&density=' + encodeURIComponent(density);

      fetch(apiUrl, {
        headers: { 'X-WP-Nonce': (window.nestWellData && window.nestWellData.restNonce) || '' },
      })
        .then(function (res) {
          if (!res.ok) throw new Error('Network error');
          return res.json();
        })
        .then(function (payload) {
          if (!payload || !payload.html) {
            sentinel.remove();
            if (endMsg) endMsg.hidden = false;
            return;
          }

          // Append server-rendered HTML directly to preserve column-count
          // flow and keep markup byte-identical to the initial render.
          var temp = document.createElement('div');
          temp.innerHTML = payload.html;
          while (temp.firstChild) {
            grid.appendChild(temp.firstChild);
          }

          // Notify other modules so they can re-bind on appended nodes.
          document.dispatchEvent(new CustomEvent('nestwell:discovery_appended', {
            bubbles: true,
            detail: { page: payload.page, totalPages: payload.totalPages },
          }));

          if (typeof payload.totalPages === 'number') {
            sentinel.dataset.totalPages = String(payload.totalPages);
          }

          var nextPage = page + 1;
          sentinel.dataset.page = String(nextPage);

          if (!payload.hasMore || nextPage > parseInt(sentinel.dataset.totalPages, 10)) {
            sentinel.remove();
            if (endMsg) endMsg.hidden = false;
          }
        })
        .catch(function (err) {
          console.error('Discovery feed scroll error:', err);
        })
        .then(function () {
          isFetching = false;
          if (loading) {
            loading.hidden = true;
            loading.setAttribute('aria-busy', 'false');
          }
        });
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            fetchNextPage();
          }
        });
      },
      { rootMargin: '400px' } // start loading earlier — masonry cards are denser
    );

    observer.observe(sentinel);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDiscoveryScroll);
  } else {
    initDiscoveryScroll();
  }

})();
