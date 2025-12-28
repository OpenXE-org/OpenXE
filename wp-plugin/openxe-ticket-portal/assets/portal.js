/* global OpenXETwitterPortal */
(function () {
  'use strict';

  function qs(selector, root) {
    return (root || document).querySelector(selector);
  }

  function qsa(selector, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(selector));
  }

  function debounce(func, wait) {
    var timeout;
    return function () {
      var context = this, args = arguments;
      clearTimeout(timeout);
      timeout = setTimeout(function () {
        func.apply(context, args);
      }, wait);
    };
  }

  function formatDate(value) {
    if (!value) return '';
    return value.replace('T', ' ').replace('Z', '');
  }

  function setVisible(el, visible) {
    if (el) el.style.display = visible ? '' : 'none';
  }

  function showMessage(el, text) {
    if (el) el.textContent = text || '';
  }

  var cache = {
    data: {},
    get: function (key) {
      var entry = this.data[key];
      if (entry && (Date.now() - entry.time < 30000)) return entry.val; // 30s cache
      return null;
    },
    set: function (key, val) {
      this.data[key] = { val: val, time: Date.now() };
    },
    clear: function () { this.data = {}; }
  };

  function portalAjax(config, action, payload) {
    var cacheKey = action + JSON.stringify(payload || {});
    var cached = cache.get(cacheKey);
    if (cached) return Promise.resolve(cached);

    var data = new FormData();
    data.append('action', action);
    data.append('nonce', config.nonce || '');
    Object.keys(payload || {}).forEach(function (key) {
      data.append(key, payload[key]);
    });

    return fetch(config.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: data
    }).then(function (response) {
      return response.json().then(function (json) {
        if (json.success) cache.set(cacheKey, json);
        return json;
      }).catch(function () {
        return { success: false, data: { message: 'invalid_response' } };
      });
    }).catch(function () {
      return { success: false, data: { message: 'request_failed' } };
    });
  }

  function initPortal(root) {
    var config = window.OpenXETwitterPortal || null;
    if (!config) {
      var rawConfig = root.getAttribute('data-openxe-config') || '';
      if (rawConfig) {
        try { config = JSON.parse(rawConfig); } catch (e) { config = null; }
      }
    }

    var errorEl = qs('.oxp-error', root);
    if (!config || !config.ajaxUrl || !config.nonce) {
      setVisible(errorEl, true);
      showMessage(errorEl, 'Portal Konfiguration fehlt.');
      return;
    }

    var loginBox = qs('.oxp-login', root);
    var mainBox = qs('.oxp-main', root);
    var loginMsg = qs('.oxp-login-msg', root);
    var statusValue = qs('.oxp-status-value', root);
    var statusUpdated = qs('.oxp-status-updated', root);
    var messagesBox = qs('.oxp-messages', root);
    var ticketInput = qs('.oxp-ticket-number', root);
    var verifierSelect = qs('.oxp-verifier', root);
    var verifierInput = qs('.oxp-verifier-value', root);
    var messageText = qs('.oxp-message-text', root);
    var sessionToken = '';

    function showError(msg) {
      if (!errorEl) return;
      showMessage(errorEl, msg);
      setVisible(errorEl, !!msg);
    }

    function formatError(resp, fallback) {
      var raw = resp && resp.data && (resp.data.message || (resp.data.data && resp.data.data.error));
      var map = {
        'access_locked': 'Zu viele Fehlversuche.',
        'rate_limited': 'Zu viele Anfragen.',
        'verification_failed': 'Verifikation falsch.',
        'token_not_found': 'Ticket nicht gefunden.',
        'message_too_long': 'Nachricht zu lang (max 2000 Zeichen).'
      };
      return map[raw] || raw || fallback || 'Fehler beim Laden.';
    }

    function loadStatus() {
      if (!sessionToken) return;
      portalAjax(config, 'openxe_ticket_portal_status', { session_token: sessionToken }).then(function (resp) {
        if (!resp.success) return showError(formatError(resp));
        showError('');
        var data = resp.data || {};
        statusValue.textContent = data.status_label || data.status_key || '';
        statusUpdated.textContent = data.updated_at ? ('Stand: ' + formatDate(data.updated_at)) : '';
      });
    }

    function loadMessages() {
      if (!sessionToken) return;
      portalAjax(config, 'openxe_ticket_portal_messages', { session_token: sessionToken }).then(function (resp) {
        if (!resp.success) return;
        var list = resp.data && resp.data.messages ? resp.data.messages : [];
        messagesBox.innerHTML = '';
        if (!list.length) {
          messagesBox.textContent = 'Keine Nachrichten.';
          return;
        }
        list.forEach(function (msg) {
          var item = document.createElement('div');
          item.className = 'oxp-message';
          item.innerHTML = '<div class="oxp-message-meta">' + (msg.author_type === 'customer' ? 'Ich' : 'Team') + ' · ' + formatDate(msg.created_at) + '</div><div>' + msg.text + '</div>';
          messagesBox.appendChild(item);
        });
      });
    }

    function showMain() {
      setVisible(loginBox, false);
      setVisible(mainBox, true);
      loadStatus();
      loadMessages();
      loadMedia();
    }

    function loadMedia() {
      portalAjax(config, 'openxe_ticket_portal_media', { session_token: sessionToken }).then(function (resp) {
        if (!resp.success) return;
        var container = qs('.oxp-media-list', root);
        if (!container) return;
        container.innerHTML = '';
        var list = resp.data && resp.data.media ? resp.data.media : [];
        setVisible(qs('.oxp-media', root), !!list.length);
        list.forEach(function (m) {
          var item = document.createElement('div');
          item.innerHTML = '<a href="#" class="oxp-media-dl" data-id="' + m.id + '">' + (m.filename || 'Datei') + '</a> (' + Math.round(m.file_size / 1024) + ' KB)';
          container.appendChild(item);
        });
      });
    }

    qs('.oxp-login-btn', root).addEventListener('click', function () {
      showMessage(loginMsg, 'Anmeldung...');
      portalAjax(config, 'openxe_ticket_portal_session', {
        ticket_number: ticketInput.value.trim(),
        verifier_type: verifierSelect.value,
        verifier_value: verifierInput.value.trim()
      }).then(function (resp) {
        if (!resp.success) return showMessage(loginMsg, formatError(resp));
        if (resp.data.session_token) {
          sessionToken = resp.data.session_token;
          showMain();
        } else if (resp.data.status === 'verification_sent') {
          showMessage(loginMsg, 'Code wurde gesendet.');
        }
      });
    });

    qs('.oxp-refresh', root).addEventListener('click', debounce(function () {
      cache.clear();
      loadStatus();
      loadMessages();
    }, 300));

    qs('.oxp-send', root).addEventListener('click', function () {
      var text = messageText.value.trim();
      if (!text) return;
      portalAjax(config, 'openxe_ticket_portal_message', { session_token: sessionToken, text: text }).then(function (resp) {
        if (!resp.success) return alert(formatError(resp));
        messageText.value = '';
        cache.clear();
        loadMessages();
      });
    });
  }

  qsa('.openxe-portal').forEach(initPortal);
})();
