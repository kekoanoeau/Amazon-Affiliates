/**
 * Nest & Well — Main JavaScript
 * Handles: sticky nav, mobile menu, search toggle, tab filter, load more, FAQ accordion, copy link
 *
 * No jQuery dependency. Vanilla JS only.
 *
 * @package nest-and-well
 * @since 1.0.0
 */

(function () {
  'use strict';

  // ============================================================
  // Utilities
  // ============================================================

  /**
   * Get element by selector, returns null if not found.
   * @param {string} selector
   * @param {Element} [context=document]
   * @returns {Element|null}
   */
  function $(selector, context) {
    return (context || document).querySelector(selector);
  }

  /**
   * Get all elements by selector.
   * @param {string} selector
   * @param {Element} [context=document]
   * @returns {NodeList}
   */
  function $$(selector, context) {
    return (context || document).querySelectorAll(selector);
  }

  // ============================================================
  // 1. Sticky Header
  // ============================================================
  function initStickyHeader() {
    var header = $('#masthead');
    if (!header) return;

    var utilityBar = $('.site-header__utility-bar');
    var stickyBrand = $('.primary-nav__sticky-brand');
    var scrollThreshold = 80;

    function handleScroll() {
      if (window.scrollY > scrollThreshold) {
        header.classList.add('is-sticky');
        if (utilityBar) utilityBar.setAttribute('aria-hidden', 'true');
        if (stickyBrand) stickyBrand.removeAttribute('aria-hidden');
      } else {
        header.classList.remove('is-sticky');
        if (utilityBar) utilityBar.removeAttribute('aria-hidden');
        if (stickyBrand) stickyBrand.setAttribute('aria-hidden', 'true');
      }
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // Run on load
  }

  // ============================================================
  // 2. Mobile Menu
  // ============================================================
  function initMobileMenu() {
    var toggleBtn = $('#mobile-menu-toggle');
    var mobileMenu = $('#mobile-menu');
    var closeBtn = $('#mobile-menu-close');
    var overlay = $('#mobile-menu-overlay');

    if (!toggleBtn || !mobileMenu) return;

    function openMenu() {
      mobileMenu.classList.add('is-open');
      mobileMenu.setAttribute('aria-hidden', 'false');
      toggleBtn.setAttribute('aria-expanded', 'true');
      document.body.classList.add('menu-is-open');
      if (closeBtn) closeBtn.focus();
    }

    function closeMenu() {
      mobileMenu.classList.remove('is-open');
      mobileMenu.setAttribute('aria-hidden', 'true');
      toggleBtn.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('menu-is-open');
      toggleBtn.focus();
    }

    toggleBtn.addEventListener('click', openMenu);

    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && mobileMenu.classList.contains('is-open')) {
        closeMenu();
      }
    });
  }

  // ============================================================
  // 3. Search Toggle (utility bar)
  // ============================================================
  function initSearchToggle() {
    var searchToggle = $('.utility-bar__search-toggle');
    var searchWrap   = $('#site-search-form');

    if (!searchToggle || !searchWrap) return;

    function openSearch() {
      searchWrap.classList.add('is-open');
      searchWrap.setAttribute('aria-hidden', 'false');
      searchToggle.setAttribute('aria-expanded', 'true');
      searchToggle.setAttribute('aria-label', 'Close search');
      // Brief delay so the CSS width transition plays before focus moves
      setTimeout(function () {
        var input = searchWrap.querySelector('input[type="search"]');
        if (input) input.focus();
      }, 50);
    }

    function closeSearch() {
      searchWrap.classList.remove('is-open');
      searchWrap.setAttribute('aria-hidden', 'true');
      searchToggle.setAttribute('aria-expanded', 'false');
      searchToggle.setAttribute('aria-label', 'Search');
    }

    searchToggle.addEventListener('click', function () {
      if (searchToggle.getAttribute('aria-expanded') === 'true') {
        closeSearch();
      } else {
        openSearch();
      }
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && searchToggle.getAttribute('aria-expanded') === 'true') {
        closeSearch();
        searchToggle.focus();
      }
    });

    // Close on click outside
    document.addEventListener('click', function (e) {
      if (
        searchToggle.getAttribute('aria-expanded') === 'true' &&
        !searchWrap.contains(e.target) &&
        !searchToggle.contains(e.target)
      ) {
        closeSearch();
      }
    });
  }

  // ============================================================
  // 4. Tab Filter (homepage article grid)
  // ============================================================
  function initTabFilter() {
    var tabs = $$('.article-tabs__tab');
    var items = $$('.article-grid__item');

    if (!tabs.length || !items.length) return;

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var filter = tab.dataset.filter;

        // Update tab states
        tabs.forEach(function (t) {
          t.classList.remove('is-active');
          t.setAttribute('aria-selected', 'false');
        });
        tab.classList.add('is-active');
        tab.setAttribute('aria-selected', 'true');

        // Filter items
        var hasVisible = false;
        items.forEach(function (item) {
          var dataKey = 'filter' + filter.replace(/-([a-z])/g, function (g) {
            return g[1].toUpperCase();
          });

          var dataAttr = 'filter' + filter.split('-').map(function (s, i) {
            return i === 0 ? s : s.charAt(0).toUpperCase() + s.slice(1);
          }).join('');

          var show = filter === 'newest' || item.dataset[dataAttr] === 'true';

          if (show) {
            item.classList.remove('is-hidden');
            item.removeAttribute('hidden');
            hasVisible = true;
          } else {
            item.classList.add('is-hidden');
          }
        });
      });
    });
  }

  // ============================================================
  // 5. Homepage Infinite Scroll (IntersectionObserver)
  // ============================================================
  function initInfiniteScroll() {
    var sentinel = $('.js-infinite-sentinel');
    var grid     = $('#hp-feed-grid');
    var loading  = $('.js-infinite-loading');
    var endMsg   = $('.js-infinite-end');

    if (!sentinel || !grid) return;
    if (!('IntersectionObserver' in window)) return; // graceful no-op for old browsers

    var categoryId = sentinel.dataset.categoryId || '';
    var isFetching = false;

    function fetchNextPage() {
      var page       = parseInt(sentinel.dataset.page, 10);
      var perPage    = parseInt(sentinel.dataset.perPage || '12', 10);
      var totalPages = parseInt(sentinel.dataset.totalPages || '1', 10);

      if (isFetching || page > totalPages) return;
      isFetching = true;

      if (loading) { loading.hidden = false; loading.setAttribute('aria-busy', 'true'); }

      var base = (window.nestWellData && window.nestWellData.restUrl)
        ? window.nestWellData.restUrl + 'wp/v2/posts'
        : '/wp-json/wp/v2/posts';
      var apiUrl = base + '?per_page=' + perPage + '&page=' + page + '&_embed=1';
      if (categoryId) { apiUrl += '&categories=' + categoryId; }

      fetch(apiUrl, {
        headers: { 'X-WP-Nonce': (window.nestWellData && window.nestWellData.restNonce) || '' },
      })
        .then(function (res) {
          if (!res.ok) throw new Error('Network error');
          var wpTotal = parseInt(res.headers.get('X-WP-TotalPages') || '1', 10);
          sentinel.dataset.totalPages = String(wpTotal);
          return res.json();
        })
        .then(function (posts) {
          if (!posts || !posts.length) {
            sentinel.remove();
            if (endMsg) endMsg.hidden = false;
            return;
          }

          var frag = document.createDocumentFragment();
          posts.forEach(function (post) {
            var wrapper = document.createElement('div');
            wrapper.className = 'flex-item homepage-style-item';
            wrapper.innerHTML = buildPostCard(post);
            frag.appendChild(wrapper);
          });
          grid.appendChild(frag);

          var nextPage = page + 1;
          sentinel.dataset.page = String(nextPage);

          if (nextPage > parseInt(sentinel.dataset.totalPages, 10)) {
            sentinel.remove();
            if (endMsg) endMsg.hidden = false;
          }
        })
        .catch(function (err) {
          console.error('Infinite scroll error:', err);
        })
        .finally(function () {
          isFetching = false;
          if (loading) { loading.hidden = true; loading.setAttribute('aria-busy', 'false'); }
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
      { rootMargin: '200px' }  // start loading 200px before sentinel hits viewport
    );

    observer.observe(sentinel);
  }

  // ============================================================
  // 6. Load More Button (non-homepage pages: archive, search)
  // ============================================================
  function initLoadMore() {
    var loadMoreBtn = $('.js-load-more');
    var grid = $('#tab-panel-articles');
    var spinner = $('.js-load-more-spinner');

    if (!loadMoreBtn || !grid) return;

    var totalPages = parseInt(loadMoreBtn.dataset.totalPages || '10', 10);

    loadMoreBtn.addEventListener('click', function () {
      var page = parseInt(loadMoreBtn.dataset.page, 10);
      var perPage = parseInt(loadMoreBtn.dataset.perPage || '9', 10);

      if (page > totalPages) {
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'No more reviews';
        return;
      }

      // Show loading state
      loadMoreBtn.disabled = true;
      if (spinner) {
        spinner.hidden = false;
        spinner.setAttribute('aria-busy', 'true');
      }

      var apiUrl = (window.nestWellData && window.nestWellData.restUrl)
        ? window.nestWellData.restUrl + 'wp/v2/posts?per_page=' + perPage + '&page=' + page + '&_embed=1'
        : '/wp-json/wp/v2/posts?per_page=' + perPage + '&page=' + page + '&_embed=1';

      fetch(apiUrl, {
        headers: {
          'X-WP-Nonce': (window.nestWellData && window.nestWellData.restNonce) || ''
        }
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          // Get total pages from headers
          var wpTotalPages = parseInt(response.headers.get('X-WP-TotalPages') || '1', 10);
          totalPages = wpTotalPages;
          loadMoreBtn.dataset.totalPages = String(wpTotalPages);
          return response.json();
        })
        .then(function (posts) {
          if (!posts || !posts.length) {
            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'No more reviews';
            return;
          }

          posts.forEach(function (post) {
            var card = buildPostCard(post);
            var wrapper = document.createElement('div');
            wrapper.className = 'article-grid__item';
            wrapper.setAttribute('data-filter-newest', 'true');
            wrapper.innerHTML = card;
            grid.appendChild(wrapper);
          });

          loadMoreBtn.dataset.page = String(page + 1);

          if (page + 1 > totalPages) {
            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'All reviews loaded';
          } else {
            loadMoreBtn.disabled = false;
          }
        })
        .catch(function (error) {
          console.error('Load more error:', error);
          loadMoreBtn.disabled = false;
          loadMoreBtn.textContent = 'Try again';
        })
        .finally(function () {
          if (spinner) {
            spinner.hidden = true;
            spinner.setAttribute('aria-busy', 'false');
          }
        });
    });
  }

  /**
   * Build a minimal post card HTML from REST API post data.
   * @param {Object} post
   * @returns {string} HTML string
   */
  function buildPostCard(post) {
    var title = post.title && post.title.rendered ? post.title.rendered : '';
    var link = post.link || '#';
    var excerpt = post.excerpt && post.excerpt.rendered
      ? post.excerpt.rendered.replace(/<[^>]+>/g, '').substring(0, 120) + '&hellip;'
      : '';

    var imgHtml = '';
    if (post._embedded && post._embedded['wp:featuredmedia'] && post._embedded['wp:featuredmedia'][0]) {
      var media = post._embedded['wp:featuredmedia'][0];
      var imgUrl = (media.media_details && media.media_details.sizes && media.media_details.sizes['card-thumbnail'])
        ? media.media_details.sizes['card-thumbnail'].source_url
        : media.source_url;
      if (imgUrl) {
        imgHtml = '<img src="' + escAttr(imgUrl) + '" alt="' + escAttr(title.replace(/<[^>]+>/g, '')) + '" loading="lazy" class="article-card__image">';
      }
    }

    var authorName = '';
    if (post._embedded && post._embedded.author && post._embedded.author[0]) {
      authorName = post._embedded.author[0].name || '';
    }

    var date = post.date ? new Date(post.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '';

    return '<article class="article-card">' +
      '<div class="article-card__image-wrap">' +
      '<a href="' + escAttr(link) + '" class="article-card__image-link" tabindex="-1" aria-hidden="true">' +
      (imgHtml || '<div class="article-card__image-placeholder"></div>') +
      '</a>' +
      '</div>' +
      '<div class="article-card__body">' +
      '<h3 class="article-card__title"><a href="' + escAttr(link) + '" class="article-card__title-link">' + title + '</a></h3>' +
      '<p class="article-card__excerpt">' + excerpt + '</p>' +
      '<div class="article-card__meta">' +
      (authorName ? '<span class="article-card__author">' + escHtml(authorName) + '</span><span class="article-card__sep" aria-hidden="true">&middot;</span>' : '') +
      '<time class="article-card__date">' + escHtml(date) + '</time>' +
      '</div>' +
      '<div class="article-card__bottom">' +
      '<div class="article-card__rating"></div>' +
      '<a href="' + escAttr(link) + '" class="article-card__read-link">Read Review &rarr;</a>' +
      '</div>' +
      '</div>' +
      '</article>';
  }

  function escHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str)));
    return div.innerHTML;
  }

  function escAttr(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }

  // ============================================================
  // 6. FAQ Accordion
  // ============================================================
  function initFaqAccordion() {
    var faqButtons = $$('.faq-item__question');

    faqButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var isExpanded = btn.getAttribute('aria-expanded') === 'true';
        var answer = $('#' + btn.getAttribute('aria-controls'));
        var icon = btn.querySelector('.faq-item__icon');

        btn.setAttribute('aria-expanded', String(!isExpanded));

        if (answer) {
          if (isExpanded) {
            answer.hidden = true;
          } else {
            answer.hidden = false;
          }
        }

        if (icon) {
          icon.textContent = isExpanded ? '+' : '−';
        }
      });
    });
  }

  // ============================================================
  // 7. Copy Link Button
  // ============================================================
  function initCopyLink() {
    var copyButtons = $$('.share-buttons__item--copy');

    copyButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var url = btn.dataset.copyUrl || window.location.href;

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(url).then(function () {
            var original = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function () {
              btn.textContent = original;
            }, 2000);
          });
        } else {
          // Fallback for older browsers
          var textarea = document.createElement('textarea');
          textarea.value = url;
          textarea.style.position = 'fixed';
          textarea.style.opacity = '0';
          document.body.appendChild(textarea);
          textarea.select();
          try {
            document.execCommand('copy');
            var original = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function () {
              btn.textContent = original;
            }, 2000);
          } catch (e) {
            // Silent fail
          }
          document.body.removeChild(textarea);
        }
      });
    });
  }

  // ============================================================
  // 8. Stripe Nav Dropdowns
  // ============================================================
  function initStripeNavDropdowns() {
    var stripeItems = $$('.stripe-nav__item.has-dropdown');

    stripeItems.forEach(function (item) {
      var toggle = item.querySelector('.stripe-nav__toggle');
      var dropdown = item.querySelector('.stripe-nav__dropdown');

      if (!toggle || !dropdown) return;

      var navInner = item.closest('.stripe-nav__inner');

      function openDropdown() {
        dropdown.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        item.classList.add('is-expanded');
        if (navInner) navInner.classList.add('has-open-dropdown');
      }

      function closeDropdown() {
        dropdown.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        item.classList.remove('is-expanded');
        if (navInner) navInner.classList.remove('has-open-dropdown');
      }

      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = toggle.getAttribute('aria-expanded') === 'true';
        // Close all other open dropdowns first
        $$('.stripe-nav__item.has-dropdown.is-expanded').forEach(function (other) {
          if (other !== item) {
            other.querySelector('.stripe-nav__dropdown').hidden = true;
            other.querySelector('.stripe-nav__toggle').setAttribute('aria-expanded', 'false');
            other.classList.remove('is-expanded');
          }
        });
        if (isOpen) {
          closeDropdown();
        } else {
          openDropdown();
        }
      });

      // Close on Escape key
      item.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && item.classList.contains('is-expanded')) {
          closeDropdown();
          toggle.focus();
        }
      });
    });

    // Click outside closes all dropdowns
    document.addEventListener('click', function () {
      $$('.stripe-nav__item.has-dropdown.is-expanded').forEach(function (item) {
        item.querySelector('.stripe-nav__dropdown').hidden = true;
        item.querySelector('.stripe-nav__toggle').setAttribute('aria-expanded', 'false');
        item.classList.remove('is-expanded');
      });
      $$('.stripe-nav__inner.has-open-dropdown').forEach(function (inner) {
        inner.classList.remove('has-open-dropdown');
      });
    });

    // Prevent clicks inside dropdown from closing it
    $$('.stripe-nav__dropdown').forEach(function (dropdown) {
      dropdown.addEventListener('click', function (e) {
        e.stopPropagation();
      });
    });
  }

  // ============================================================
  // 9. Active Navigation State
  // ============================================================
  function initActiveNav() {
    var currentPath = window.location.pathname;
    var navLinks = $$('.primary-nav__menu a, .stripe-nav__item');

    navLinks.forEach(function (link) {
      var href = link.getAttribute('href');
      if (href && href !== '/' && currentPath.indexOf(href) === 0) {
        link.classList.add('is-current');
        link.setAttribute('aria-current', 'page');
      }
    });
  }

  // ============================================================
  // Init All
  // ============================================================
  function init() {
    initStickyHeader();
    initMobileMenu();
    initSearchToggle();
    initTabFilter();
    initInfiniteScroll();
    initLoadMore();
    initFaqAccordion();
    initCopyLink();
    initStripeNavDropdowns();
    initActiveNav();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
