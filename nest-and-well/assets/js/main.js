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
  // 12b. Email Subscribe Forms (sidebar / footer)
  // ============================================================
  function initSubscribeForms() {
    var forms = $$('.js-subscribe-form');
    if (!forms.length || typeof window.nestWellSubscribe === 'undefined') return;

    forms.forEach(function (form) {
      var feedback = form.querySelector('.sidebar-email-form__feedback');
      var submit   = form.querySelector('button[type="submit"]');
      var input    = form.querySelector('input[type="email"]');

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!input || !input.value || form.dataset.submitting === 'true') return;

        form.dataset.submitting = 'true';
        if (submit) submit.disabled = true;
        if (feedback) {
          feedback.hidden = true;
          feedback.classList.remove('is-error', 'is-success');
        }

        var body = new FormData();
        body.append('action', window.nestWellSubscribe.action);
        body.append('nonce', window.nestWellSubscribe.nonce);
        body.append('email', input.value);
        body.append('source', form.dataset.source || 'sidebar');

        fetch(window.nestWellSubscribe.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          body: body
        })
          .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, json: j }; }); })
          .then(function (res) {
            var msg = (res.json && res.json.data && res.json.data.message) || '';
            if (res.ok && res.json && res.json.success) {
              if (feedback) {
                feedback.textContent = msg || 'Thanks!';
                feedback.classList.add('is-success');
                feedback.hidden = false;
              }
              form.reset();
            } else {
              if (feedback) {
                feedback.textContent = msg || 'Something went wrong. Please try again.';
                feedback.classList.add('is-error');
                feedback.hidden = false;
              }
            }
          })
          .catch(function () {
            if (feedback) {
              feedback.textContent = 'Network error. Please try again.';
              feedback.classList.add('is-error');
              feedback.hidden = false;
            }
          })
          .finally(function () {
            form.dataset.submitting = 'false';
            if (submit) submit.disabled = false;
          });
      });
    });
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
  function init() {
    initStickyHeader();
    initMobileMenu();
    initSearchToggle();
    initTabFilter();
    initInfiniteScroll();
    initLoadMore();
    initFaqAccordion();
    initCopyLink();
    initActiveNav();
    initTableScroll();
    initReadingProgress();
    initBackToTop();
    initTableOfContents();
    initStickyBuyBar();
    initSubscribeForms();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
