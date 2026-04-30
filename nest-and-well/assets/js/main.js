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
  // First-line defense: ensure no .js-subscribe-form ever performs a
  // native page submit, regardless of DOM-ready timing or whether any
  // other init function throws before initSubscribeForms binds its
  // per-form listeners. Capture phase + bound at script-parse time so
  // it always wins. Without this, forms on pages where an earlier init
  // errors fall through to a native POST against admin-ajax.php and
  // the browser navigates to the raw "-1" response page.
  // ============================================================
  document.addEventListener('submit', function (e) {
    var t = e.target;
    if (t && t.nodeName === 'FORM' && t.classList && t.classList.contains('js-subscribe-form')) {
      e.preventDefault();
    }
  }, true);

  // ============================================================
  // Theme toggle — shared logic + two independent bindings.
  //
  // applyTheme() is the single source of truth.
  //
  // Binding 1: document-level delegate (script-parse time, bubble
  //   phase). Uses a manual parent-walk instead of .closest() so it
  //   works even when e.target is an SVG <path> or <circle>, where
  //   some older Safari builds don't support .closest().
  //
  // Binding 2: direct btn.addEventListener inside initThemeToggle
  //   (DOMContentLoaded). Sets e._themeHandled = true so the
  //   document delegate skips the same click and cannot double-fire.
  // ============================================================
  function applyTheme(btn) {
    var html = document.documentElement;
    var attr = html.getAttribute('data-theme');
    var current = attr === 'dark' ? 'dark' : attr === 'light' ? 'light'
      : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    var next = current === 'dark' ? 'light' : 'dark';

    html.setAttribute('data-theme', next);
    try { localStorage.setItem('nest-well-theme', next); } catch (err) {}

    if (btn) {
      btn.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false');
      btn.setAttribute('aria-label', next === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
    }
  }

  document.addEventListener('click', function (e) {
    if (e._themeHandled) return; // direct btn listener already handled this
    var node = e.target;
    var btn = null;
    for (var i = 0; i < 5 && node && node !== document.documentElement; i++) {
      if (node.nodeType === 1) {
        var nid = node.id || '';
        var ncls = (node.getAttribute && node.getAttribute('class')) || '';
        if (nid === 'theme-toggle' ||
            ncls.indexOf('site-header__theme-toggle') !== -1 ||
            ncls.indexOf('js-theme-toggle') !== -1) {
          btn = node;
          break;
        }
      }
      node = node.parentNode;
    }
    if (!btn) return;
    applyTheme(btn);
  });

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

    var scrollThreshold = 40;

    function handleScroll() {
      if (window.scrollY > scrollThreshold) {
        header.classList.add('is-sticky');
      } else {
        header.classList.remove('is-sticky');
      }
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
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
    var searchToggle = $('.site-header__search-toggle');
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
    if (sentinel.classList.contains('js-discovery-sentinel')) return; // owned by discovery.js
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
  // 7. Copy Link Button + "Copied ✓" toast
  // ============================================================
  function initCopyLink() {
    var copyButtons = $$('.share-buttons__item--copy');

    function showToast(btn) {
      var row = btn.closest('.share-buttons') || btn.parentNode;
      if (!row) return;

      // Replace any existing toast on the same row.
      var existing = row.querySelector('.nw-copy-toast');
      if (existing) existing.remove();

      var toast = document.createElement('span');
      toast.className = 'nw-copy-toast';
      toast.setAttribute('role', 'status');
      toast.textContent = 'Copied ✓';
      row.appendChild(toast);

      // Animation duration matches nw-toast-in keyframe (1.6s).
      setTimeout(function () { toast.remove(); }, 1700);
    }

    function copyText(text) {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        return navigator.clipboard.writeText(text);
      }
      return new Promise(function (resolve, reject) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
          document.execCommand('copy');
          resolve();
        } catch (e) {
          reject(e);
        }
        document.body.removeChild(ta);
      });
    }

    copyButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var url = btn.dataset.copyUrl || window.location.href;
        copyText(url).then(function () {
          showToast(btn);
        }).catch(function () { /* silent */ });
      });
    });
  }

  // ============================================================
  // 7b. Scroll-reveal — fade-up on cards/sections via [data-reveal]
  // ============================================================
  function initScrollReveal() {
    if (!('IntersectionObserver' in window)) return;

    // Tag the entry points so authors don't have to remember the attribute.
    var targets = [
      '.article-card',
      '.hp-hero__tile',
      '.quick-picks__card',
      '.explore-category__item',
      '.review-summary--auto',
      '.verdict',
      '.newsletter-cta',
      '.author-bio',
      '.how-we-review',
      '.faq-item'
    ];
    var els = $$(targets.join(','));

    var groupCounter = new WeakMap();

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        io.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });

    els.forEach(function (el) {
      if (!el.hasAttribute('data-reveal')) el.setAttribute('data-reveal', '');

      // Stagger siblings inside the same parent so a row of cards fades in
      // left-to-right (~80ms apart, capped at 6 to avoid long delays).
      var parent = el.parentElement;
      if (parent) {
        var n = (groupCounter.get(parent) || 0);
        groupCounter.set(parent, n + 1);
        if (n > 0 && n < 7) {
          el.style.setProperty('--reveal-delay', (n * 80) + 'ms');
        }
      }
      io.observe(el);
    });
  }

  // ============================================================
  // 7c. Animated score counters — count up from 0 when in view
  // ============================================================
  function initScoreCounters() {
    if (!('IntersectionObserver' in window)) return;

    // Six score surfaces all expose data-score-target; some wrap the digits
    // in a child .nw-score-num so we don't trample sibling labels like "/10".
    var els = $$('[data-score-target]');
    if (!els.length) return;

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function easeOut(t) { return 1 - Math.pow(1 - t, 3); }

    function format(n) {
      // 1 decimal unless the target is integer (e.g. "10").
      return Number.isInteger(n) ? String(n) : n.toFixed(1);
    }

    function getDigitNode(el) {
      return el.querySelector('.nw-score-num') || el;
    }

    function runCounter(el) {
      var target = parseFloat(el.getAttribute('data-score-target'));
      if (isNaN(target)) return;

      var node = getDigitNode(el);

      // Animate the optional verdict ring alongside the number.
      var ringHost = el.classList.contains('verdict__score-ring') ? el : null;
      var ringFill = ringHost ? ringHost.querySelector('.verdict__ring-fill') : null;
      var ringPct  = ringHost ? parseFloat(ringHost.getAttribute('data-score-pct') || '0') : 0;

      if (reduceMotion) {
        node.textContent = format(target);
        if (ringFill) ringFill.style.strokeDashoffset = String(100 - ringPct);
        return;
      }

      var duration = 750;
      var start = null;
      function step(ts) {
        if (start === null) start = ts;
        var t = Math.min(1, (ts - start) / duration);
        var eased = easeOut(t);
        var current = target * eased;
        node.textContent = format(current);
        if (ringFill) {
          // pathLength=100 ⇒ stroke-dashoffset 100 = empty, 0 = full.
          ringFill.style.strokeDashoffset = String(100 - (ringPct * eased));
        }
        if (t < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        runCounter(entry.target);
        io.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -20% 0px', threshold: 0.2 });

    els.forEach(function (el) {
      // Reset to 0.0 so the count-up reads as a real animation; if an
      // observer never fires (e.g. element is hidden), the original value
      // is preserved by data-score-target.
      var node = getDigitNode(el);
      if (!reduceMotion && node) {
        var t = parseFloat(el.getAttribute('data-score-target'));
        node.textContent = Number.isInteger(t) ? '0' : '0.0';
      }
      io.observe(el);
    });
  }

  // ============================================================
  // 7e. Comparison table — highlight the row with the highest score
  // ============================================================
  // Authors can also opt out by adding data-no-winner to a single
  // .comparison-table-wrap.
  function initComparisonWinners() {
    $$('.comparison-table').forEach(function (table) {
      if (table.closest('[data-no-winner]')) return;

      var rows = table.querySelectorAll('tbody tr');
      if (rows.length < 2) return;

      var bestScore = -Infinity;
      var bestRow   = null;

      rows.forEach(function (row) {
        var pill = row.querySelector('.comparison-table__score .score-pill');
        if (!pill) return;
        // Strip "/10" / non-numeric chars; the value can be "9.4/10" or "9.4".
        var num = parseFloat(pill.textContent.replace(/[^\d.]/g, ''));
        if (isNaN(num)) return;
        if (num > bestScore) {
          bestScore = num;
          bestRow   = row;
        }
      });

      if (bestRow) {
        bestRow.classList.add('is-winner');
        var nameCell = bestRow.querySelector('.comparison-table__name');
        if (nameCell && !nameCell.querySelector('.comparison-table__win-mark')) {
          var mark = document.createElement('span');
          mark.className = 'comparison-table__win-mark';
          mark.setAttribute('aria-label', 'Highest score');
          mark.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>';
          nameCell.insertBefore(mark, nameCell.firstChild);
        }
      }
    });
  }

  // ============================================================
  // 7d. Star-rating left-to-right fill on viewport entry
  // ============================================================
  function initStarFillReveal() {
    if (!('IntersectionObserver' in window)) return;
    var groups = $$('.star-rating');
    if (!groups.length) return;

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-revealed');
        io.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.4 });

    groups.forEach(function (g) { io.observe(g); });
  }

  // ============================================================
  // 9. Active Navigation State
  // ============================================================
  function initActiveNav() {
    var currentPath = window.location.pathname;
    var navLinks = $$('.site-header__nav a, .category-strip__link');

    navLinks.forEach(function (link) {
      var href = link.getAttribute('href');
      if (href && href !== '/' && currentPath.indexOf(href) === 0) {
        link.classList.add('is-current');
        link.setAttribute('aria-current', 'page');
      }
    });
  }

  // ============================================================
  // 9. Reading Progress Bar
  // ============================================================
  function initReadingProgress() {
    var article = $('.article-body');
    if (!article) return;

    var bar = document.createElement('div');
    bar.className = 'reading-progress-bar';
    bar.setAttribute('role', 'progressbar');
    bar.setAttribute('aria-hidden', 'true');
    document.body.prepend(bar);

    function updateProgress() {
      var rect      = article.getBoundingClientRect();
      var top       = rect.top + window.scrollY;
      var scrolled  = window.scrollY - top;
      var percent   = Math.min(100, Math.max(0, (scrolled / rect.height) * 100));
      bar.style.width = percent + '%';
    }

    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
  }

  // ============================================================
  // 10. Back to Top
  // ============================================================
  function initBackToTop() {
    var btn = $('#back-to-top');
    if (!btn) return;

    function updateVisibility() {
      btn.classList.toggle('is-visible', window.scrollY > 600);
    }

    window.addEventListener('scroll', updateVisibility, { passive: true });
    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    updateVisibility();
  }

  // ============================================================
  // 11. Table of Contents
  // ============================================================
  function initTableOfContents() {
    var content = $('.entry-content');
    if (!content) return;

    var headings = Array.prototype.slice.call(content.querySelectorAll('h2, h3'));
    if (headings.length < 2) return;

    headings.forEach(function (h, i) {
      if (!h.id) {
        h.id = 'toc-' + i + '-' + h.textContent.trim().toLowerCase()
          .replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
      }
    });

    // Long-form review and buying-guide layouts get the TOC in the sticky
    // sidebar; everything else gets the inline TOC.
    var sidebar = $('.sidebar-sticky #secondary, .sidebar-sticky');
    var isReview = document.body.classList.contains('is-review');
    var isGuide  = $('.article-body--guide') !== null;
    var useSidebar = sidebar && (isReview || isGuide || headings.length >= 4);

    var nav = document.createElement('nav');
    nav.className = useSidebar ? 'toc toc--sidebar widget' : 'toc toc--inline';
    nav.setAttribute('aria-label', 'Table of contents');

    var title = document.createElement('p');
    title.className = 'toc__title';
    title.textContent = 'In This Article';
    nav.appendChild(title);

    var list = document.createElement('ul');
    list.className = 'toc__list';

    var verdictId = null;
    headings.forEach(function (h) {
      var li = document.createElement('li');
      li.className = 'toc__item toc__item--' + h.tagName.toLowerCase();
      var a = document.createElement('a');
      a.href = '#' + h.id;
      a.className = 'toc__link';
      a.textContent = h.textContent;
      li.appendChild(a);
      list.appendChild(li);

      if (!verdictId && /verdict|final|takeaway|bottom\s*line/i.test(h.textContent)) {
        verdictId = h.id;
      }
    });

    nav.appendChild(list);

    if (verdictId) {
      var skip = document.createElement('a');
      skip.href = '#' + verdictId;
      skip.className = 'toc__skip';
      skip.textContent = 'Skip to verdict ↓';
      nav.appendChild(skip);
    }

    if (useSidebar) {
      var host = sidebar.querySelector('.sidebar__section');
      if (host) {
        sidebar.insertBefore(nav, host);
      } else {
        sidebar.insertBefore(nav, sidebar.firstChild);
      }
      initTocActiveTracking(nav, headings);
    } else {
      var firstP = content.querySelector('p');
      if (firstP && firstP.nextSibling) {
        content.insertBefore(nav, firstP.nextSibling);
      } else {
        content.insertBefore(nav, content.firstChild);
      }
    }
  }

  // Highlight the TOC link whose section is currently in view.
  function initTocActiveTracking(nav, headings) {
    if (!('IntersectionObserver' in window)) return;

    var links = {};
    nav.querySelectorAll('.toc__link').forEach(function (a) {
      var id = a.getAttribute('href').slice(1);
      links[id] = a;
    });

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        var link = links[entry.target.id];
        if (!link) return;
        if (entry.isIntersecting) {
          Object.keys(links).forEach(function (k) { links[k].classList.remove('is-active'); });
          link.classList.add('is-active');
        }
      });
    }, { rootMargin: '-30% 0px -60% 0px', threshold: 0 });

    headings.forEach(function (h) { observer.observe(h); });
  }

  // ============================================================
  // 12. Sticky Buy Bar
  // ============================================================
  function initStickyBuyBar() {
    var bar     = $('#sticky-buy-bar');
    var trigger = $('.article-head');
    if (!bar || !trigger) return;

    function updateBar() {
      var show = trigger.getBoundingClientRect().bottom < 0;
      bar.classList.toggle('is-visible', show);
      bar.setAttribute('aria-hidden', show ? 'false' : 'true');
    }

    window.addEventListener('scroll', updateBar, { passive: true });
    updateBar();
  }

  // ============================================================
  // 12b. Email Subscribe Forms (sidebar / footer / article-end)
  // ============================================================
  function initSubscribeForms() {
    var forms = $$('.js-subscribe-form');
    if (!forms.length) return;

    // Config may be absent if wp_localize_script was stripped by a caching
    // plugin. We still bind the listener so e.preventDefault() fires and the
    // page never navigates to admin-ajax.php.
    var config = window.nestWellSubscribe || null;
    if (!config && window.console && console.warn) {
      console.warn('nest-well: nestWellSubscribe localize data missing — AJAX subscribe unavailable');
    }

    function showSuccess(form, msg) {
      var card = document.createElement('div');
      card.className = 'subscribe-success';
      card.setAttribute('role', 'status');
      card.setAttribute('aria-live', 'polite');
      card.setAttribute('tabindex', '-1');

      var check = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      check.setAttribute('class', 'subscribe-success__check');
      check.setAttribute('viewBox', '0 0 24 24');
      check.setAttribute('fill', 'none');
      check.setAttribute('stroke', 'currentColor');
      check.setAttribute('stroke-width', '2.5');
      check.setAttribute('stroke-linecap', 'round');
      check.setAttribute('stroke-linejoin', 'round');
      check.setAttribute('aria-hidden', 'true');
      check.innerHTML = '<path d="M20 6 9 17l-5-5"/>';

      var title = document.createElement('p');
      title.className = 'subscribe-success__title';
      title.textContent = msg || 'Thanks — you\'re on the list.';

      var sub = document.createElement('p');
      sub.className = 'subscribe-success__sub';
      sub.textContent = 'Watch your inbox for the confirmation email.';

      card.appendChild(check);
      card.appendChild(title);
      card.appendChild(sub);

      form.parentNode.replaceChild(card, form);

      var rect = card.getBoundingClientRect();
      if (rect.top < 0 || rect.bottom > window.innerHeight) {
        card.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
      }
      try { card.focus({ preventScroll: true }); } catch (e) { card.focus(); }
    }

    forms.forEach(function (form) {
      var feedback    = form.querySelector('.sidebar-email-form__feedback');
      var submit      = form.querySelector('button[type="submit"]');
      var input       = form.querySelector('input[type="email"]');
      var submitLabel = submit ? submit.textContent : '';

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!input || !input.value || form.dataset.submitting === 'true') return;

        // Guard: config missing at runtime — show error, never navigate away.
        if (!config) {
          if (feedback) {
            feedback.textContent = 'Signup is temporarily unavailable. Please try again later.';
            feedback.classList.add('is-error');
            feedback.hidden = false;
          }
          return;
        }

        form.dataset.submitting = 'true';
        if (submit) {
          submit.disabled = true;
          submit.textContent = 'Subscribing…';
          submit.classList.add('is-submitting');
        }
        if (feedback) {
          feedback.hidden = true;
          feedback.classList.remove('is-error', 'is-success');
        }

        var body = new FormData();
        body.append('action', config.action);
        body.append('nonce', config.nonce);
        body.append('email', input.value);
        body.append('source', form.dataset.source || 'sidebar');

        fetch(config.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: body
        })
          .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, json: j }; }); })
          .then(function (res) {
            var msg = (res.json && res.json.data && res.json.data.message) || '';
            if (res.ok && res.json && res.json.success) {
              showSuccess(form, msg);
              return;
            }
            if (feedback) {
              feedback.textContent = msg || 'Something went wrong. Please try again.';
              feedback.classList.add('is-error');
              feedback.hidden = false;
            }
            restoreButton();
            if (input) input.focus();
          })
          .catch(function () {
            if (feedback) {
              feedback.textContent = 'Network error. Please try again.';
              feedback.classList.add('is-error');
              feedback.hidden = false;
            }
            restoreButton();
            if (input) input.focus();
          })
          .finally(function () {
            form.dataset.submitting = 'false';
          });

        function restoreButton() {
          if (!submit) return;
          submit.disabled = false;
          submit.textContent = submitLabel;
          submit.classList.remove('is-submitting');
        }
      });
    });
  }

  // ============================================================
  // 12e. Native Web Share API
  // ============================================================
  // Reveals the native-share button when navigator.share is available;
  // otherwise the button stays hidden and the inline share row is the only
  // affordance. Activates the native sheet on click.
  function initNativeShare() {
    if (typeof navigator === 'undefined' || typeof navigator.share !== 'function') {
      return;
    }

    $$('.js-native-share').forEach(function (btn) {
      btn.hidden = false;
      btn.addEventListener('click', function () {
        var url   = btn.getAttribute('data-share-url') || window.location.href;
        var title = btn.getAttribute('data-share-title') || document.title;

        navigator.share({ title: title, url: url }).catch(function (err) {
          // AbortError is fired when the user dismisses the sheet — ignore.
          if (err && err.name !== 'AbortError') {
            console.warn('Share failed:', err);
          }
        });
      });
    });
  }

  // ============================================================
  // 12d. Pinterest "Save" overlay on article images
  // ============================================================
  // Heavily Pinterest-driven niches (home, wellness, beauty, gifts) get a
  // hover/tap "Save" button that pins the image with the article URL +
  // title so readers don't have to install the Pinterest extension.
  function initPinterestOverlay() {
    if (!document.body.classList.contains('single-article') &&
        !document.body.classList.contains('page-template-page-best-of')) {
      return;
    }

    var pageUrl   = window.location.href;
    var pageTitle = document.title || '';

    function pinUrl(imgSrc) {
      return 'https://pinterest.com/pin/create/button/?' +
             'url=' + encodeURIComponent(pageUrl) +
             '&media=' + encodeURIComponent(imgSrc) +
             '&description=' + encodeURIComponent(pageTitle);
    }

    function imageSrc(img) {
      // Prefer the largest src in srcset if present; otherwise fall back to src.
      var srcset = img.getAttribute('srcset');
      if (srcset) {
        var biggest = srcset.split(',').map(function (s) {
          var parts = s.trim().split(/\s+/);
          return { url: parts[0], w: parseInt(parts[1] || '0', 10) };
        }).sort(function (a, b) { return b.w - a.w; })[0];
        if (biggest && biggest.url) return biggest.url;
      }
      return img.currentSrc || img.src;
    }

    function attach(img) {
      if (img.dataset.nwPinAttached === '1') return;
      if (img.naturalWidth && img.naturalWidth < 200) return;
      if (img.closest('.no-pin, a, .save-article-btn, .share-buttons')) return;

      var src = imageSrc(img);
      if (!src) return;

      var wrap = img.parentElement;
      if (!wrap) return;

      // Wrap the image so the absolutely-positioned button has a positioning
      // context, but only if the parent isn't already a positioned wrapper.
      var positioned = window.getComputedStyle(wrap).position;
      if (positioned === 'static') {
        wrap.style.position = 'relative';
      }
      wrap.classList.add('nw-pin-host');

      var btn = document.createElement('a');
      btn.className = 'nw-pin-btn';
      btn.href = pinUrl(src);
      btn.target = '_blank';
      btn.rel = 'nofollow noopener';
      btn.setAttribute('aria-label', 'Save this image to Pinterest');
      btn.innerHTML =
        '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true">' +
        '<path d="M12 2C6.48 2 2 6.48 2 12c0 4.09 2.46 7.6 6 9.13-.08-.78-.16-1.97.03-2.82.18-.74 1.13-4.74 1.13-4.74s-.29-.58-.29-1.43c0-1.34.78-2.34 1.74-2.34.82 0 1.22.62 1.22 1.36 0 .83-.53 2.07-.8 3.22-.23.96.48 1.74 1.43 1.74 1.71 0 3.03-1.81 3.03-4.41 0-2.31-1.66-3.92-4.03-3.92-2.74 0-4.35 2.06-4.35 4.18 0 .83.32 1.71.72 2.19.08.1.09.18.07.27-.07.3-.24.96-.27 1.1-.04.18-.14.22-.32.13-1.2-.56-1.95-2.31-1.95-3.72 0-3.03 2.2-5.81 6.34-5.81 3.33 0 5.92 2.37 5.92 5.54 0 3.31-2.09 5.97-4.99 5.97-.97 0-1.89-.51-2.2-1.1l-.6 2.28c-.22.84-.81 1.9-1.21 2.54.91.28 1.87.43 2.88.43 5.52 0 10-4.48 10-10S17.52 2 12 2z"/>' +
        '</svg>' +
        '<span>Save</span>';
      wrap.appendChild(btn);
      img.dataset.nwPinAttached = '1';
    }

    // Featured image
    var featured = $('.article-featured-image__img');
    if (featured) attach(featured);

    // In-content images (after the [content] runs through the_content filter,
    // images may be inside <figure> or wrapped in links — attach() skips
    // already-linked images so we don't break gallery-style markup).
    $$('.entry-content img').forEach(function (img) {
      if (img.complete) {
        attach(img);
      } else {
        img.addEventListener('load', function () { attach(img); }, { once: true });
      }
    });
  }

  // ============================================================
  // 12c. Theme Toggle (light / dark)
  // ============================================================
  function initThemeToggle() {
    var btn = document.getElementById('theme-toggle');
    if (!btn) return;

    var STORAGE_KEY = 'nest-well-theme';

    function currentTheme() {
      var attr = document.documentElement.getAttribute('data-theme');
      if (attr === 'dark' || attr === 'light') return attr;
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function syncButton() {
      var isDark = currentTheme() === 'dark';
      btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
      btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    }

    // Direct binding — fires before the document delegate (bubble order).
    // Sets e._themeHandled so the delegate skips the same event.
    btn.addEventListener('click', function (e) {
      e._themeHandled = true;
      applyTheme(btn);
    });

    // Re-sync if the system preference changes and no explicit choice is set.
    if (window.matchMedia) {
      try {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
          if (!localStorage.getItem(STORAGE_KEY)) syncButton();
        });
      } catch (err) { /* older Safari: addListener fallback not critical */ }
    }

    syncButton();
  }

  // ============================================================
  // 13. Table Scroll Wrap
  // ============================================================
  function initTableScroll() {
    $$('.entry-content table').forEach(function (table) {
      if (table.parentNode.classList.contains('table-scroll-wrap')) return;
      var wrap = document.createElement('div');
      wrap.className = 'table-scroll-wrap';
      table.parentNode.insertBefore(wrap, table);
      wrap.appendChild(table);
    });
  }

  // ============================================================
  // Init All
  // ============================================================
  function safeInit(fn, name) {
    try { fn(); } catch (err) {
      if (window.console && console.error) {
        console.error('nest-well: ' + name + ' threw — continuing init', err);
      }
    }
  }
  function init() {
    // Subscribe forms first so any later throw can't prevent submit-listener binding.
    safeInit(initSubscribeForms, 'initSubscribeForms');
    safeInit(initStickyHeader, 'initStickyHeader');
    safeInit(initMobileMenu, 'initMobileMenu');
    safeInit(initSearchToggle, 'initSearchToggle');
    safeInit(initTabFilter, 'initTabFilter');
    safeInit(initInfiniteScroll, 'initInfiniteScroll');
    safeInit(initLoadMore, 'initLoadMore');
    safeInit(initFaqAccordion, 'initFaqAccordion');
    safeInit(initCopyLink, 'initCopyLink');
    safeInit(initActiveNav, 'initActiveNav');
    safeInit(initTableScroll, 'initTableScroll');
    safeInit(initReadingProgress, 'initReadingProgress');
    safeInit(initBackToTop, 'initBackToTop');
    safeInit(initTableOfContents, 'initTableOfContents');
    safeInit(initStickyBuyBar, 'initStickyBuyBar');
    safeInit(initThemeToggle, 'initThemeToggle');
    safeInit(initPinterestOverlay, 'initPinterestOverlay');
    safeInit(initNativeShare, 'initNativeShare');
    safeInit(initScrollReveal, 'initScrollReveal');
    safeInit(initScoreCounters, 'initScoreCounters');
    safeInit(initStarFillReveal, 'initStarFillReveal');
    safeInit(initComparisonWinners, 'initComparisonWinners');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
