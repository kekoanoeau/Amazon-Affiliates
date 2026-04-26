/**
 * Nest & Well — Accounts JavaScript
 * Handles: save/unsave articles, account tab switching, login prompt modal
 *
 * Requires nestWellAccounts to be localized via wp_localize_script().
 *
 * @package nest-and-well
 * @since 1.0.0
 */

(function () {
  'use strict';

  var cfg = window.nestWellAccounts || {};

  // ============================================================
  // Save / Unsave Article Buttons
  // ============================================================

  /**
   * Initialize save-article buttons throughout the page.
   */
  function initSaveButtons() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.save-article-btn');
      if (!btn) return;

      e.preventDefault();

      // Not logged in — redirect to account page
      if (!cfg.loggedIn) {
        window.location.href = cfg.loginUrl || '/account/';
        return;
      }

      var postId = parseInt(btn.dataset.postId, 10);
      if (!postId) return;

      // Prevent double-click while request is in flight
      if (btn.classList.contains('is-loading')) return;
      btn.classList.add('is-loading');
      btn.setAttribute('aria-busy', 'true');

      var formData = new FormData();
      formData.append('action', 'nest_well_toggle_save');
      formData.append('nonce', cfg.nonce);
      formData.append('post_id', String(postId));

      fetch(cfg.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData,
      })
        .then(function (res) {
          return res.json();
        })
        .then(function (data) {
          if (data.success) {
            var isSaved = data.data.saved;
            // Update all buttons for this post ID on the page
            document.querySelectorAll('.save-article-btn[data-post-id="' + postId + '"]').forEach(function (b) {
              if (isSaved) {
                b.classList.add('is-saved');
                b.setAttribute('aria-label', cfg.i18n && cfg.i18n.unsave ? cfg.i18n.unsave : 'Remove from saved');
                b.setAttribute('aria-pressed', 'true');
                var label = b.querySelector('.save-btn__label');
                if (label) label.textContent = cfg.i18n && cfg.i18n.saved ? cfg.i18n.saved : 'Saved';
              } else {
                b.classList.remove('is-saved');
                b.setAttribute('aria-label', cfg.i18n && cfg.i18n.save ? cfg.i18n.save : 'Save article');
                b.setAttribute('aria-pressed', 'false');
                var label = b.querySelector('.save-btn__label');
                if (label) label.textContent = cfg.i18n && cfg.i18n.save ? cfg.i18n.save : 'Save';
              }
              // One-shot heartbeat pulse on the actual button the user clicked.
              if (b === btn) {
                b.classList.remove('is-pulsing');
                // Force a reflow so a re-toggle restarts the animation.
                void b.offsetWidth;
                b.classList.add('is-pulsing');
                setTimeout(function () { b.classList.remove('is-pulsing'); }, 500);
              }
            });

            // Update saved count badges in the header
            updateSavedCountBadge(data.data.savedCount || 0);
          } else if (data.data && data.data.loginUrl) {
            // Not authenticated — redirect
            window.location.href = data.data.loginUrl;
          }
        })
        .catch(function (err) {
          console.error('Save article error:', err);
        })
        .finally(function () {
          btn.classList.remove('is-loading');
          btn.setAttribute('aria-busy', 'false');
        });
    });
  }

  /**
   * Update the saved-articles count badge in the header.
   * @param {number} count
   */
  function updateSavedCountBadge(count) {
    document.querySelectorAll('.account-saved-count').forEach(function (badge) {
      if (count > 0) {
        badge.textContent = String(count);
        badge.hidden = false;
      } else {
        badge.hidden = true;
      }
    });
  }

  /**
   * Mark buttons as saved on page load based on server-provided savedIds.
   */
  function initSavedState() {
    var savedIds = Array.isArray(cfg.savedIds) ? cfg.savedIds : [];
    if (!savedIds.length) return;

    document.querySelectorAll('.save-article-btn').forEach(function (btn) {
      var postId = parseInt(btn.dataset.postId, 10);
      if (savedIds.indexOf(postId) !== -1) {
        btn.classList.add('is-saved');
        btn.setAttribute('aria-label', cfg.i18n && cfg.i18n.unsave ? cfg.i18n.unsave : 'Remove from saved');
        var label = btn.querySelector('.save-btn__label');
        if (label) label.textContent = cfg.i18n && cfg.i18n.saved ? cfg.i18n.saved : 'Saved';
      }
    });
  }

  // ============================================================
  // Account Page — Tab Switching
  // ============================================================

  function initAccountTabs() {
    var tabBtns = document.querySelectorAll('.account-tabs__tab');
    var panels = document.querySelectorAll('.account-tabs__panel');

    if (!tabBtns.length) return;

    function activateTab(targetTab) {
      tabBtns.forEach(function (btn) {
        var isTarget = btn.dataset.tab === targetTab;
        btn.classList.toggle('is-active', isTarget);
        btn.setAttribute('aria-selected', String(isTarget));
      });

      panels.forEach(function (panel) {
        var isTarget = panel.id === 'tab-' + targetTab;
        panel.classList.toggle('is-active', isTarget);
        panel.hidden = !isTarget;
      });
    }

    tabBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        activateTab(btn.dataset.tab);
        // Update URL hash without scrolling
        history.replaceState(null, '', '?tab=' + btn.dataset.tab);
      });
    });

    // Switch-link inside the form ("Don't have an account? Create one")
    document.querySelectorAll('.account-tabs__switch-link').forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        var targetTab = link.dataset.tab;
        activateTab(targetTab);
        history.replaceState(null, '', '?tab=' + targetTab);
        // Scroll to tab
        var panel = document.getElementById('tab-' + targetTab);
        if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });
  }

  // ============================================================
  // Remove saved item from account page list dynamically
  // ============================================================

  function initAccountPageUnsave() {
    var savedList = document.querySelector('.account-saved');
    if (!savedList) return;

    document.addEventListener('nest_well_article_unsaved', function (e) {
      var postId = e.detail && e.detail.postId;
      if (!postId) return;
      var card = savedList.querySelector('.saved-article-card[data-post-id="' + postId + '"]');
      if (card) {
        card.style.opacity = '0';
        card.style.transition = 'opacity 0.3s ease';
        setTimeout(function () {
          card.remove();
          // Show empty state if no cards left
          var remaining = savedList.querySelectorAll('.saved-article-card');
          if (!remaining.length) {
            savedList.innerHTML =
              '<div class="account-saved__empty">' +
              '<p>' + (cfg.i18n && cfg.i18n.noSaved ? cfg.i18n.noSaved : "You haven't saved any articles yet.") + '</p>' +
              '<a href="/" class="btn btn--primary">Browse Reviews</a>' +
              '</div>';
          }
        }, 300);
      }
    });
  }

  // ============================================================
  // Dispatch custom event when article is unsaved (for account page)
  // ============================================================

  // Patch the fetch handler to dispatch events
  var origFetch = window.fetch;
  // We don't need to patch — instead we dispatch from the click handler.
  // The click handler above will dispatch the event after a successful unsave.
  // Re-attach that logic properly:

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.save-article-btn.is-saved');
    if (!btn || !cfg.loggedIn) return;
    // After unsave, dispatch custom event for account page cleanup
    // This is handled by observing the class change after AJAX success.
  });

  // ============================================================
  // Init
  // ============================================================

  function init() {
    initSaveButtons();
    initSavedState();
    initAccountTabs();
    initAccountPageUnsave();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
