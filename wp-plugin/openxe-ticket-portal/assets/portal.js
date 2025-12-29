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
        if (json.success) {
          cache.set(cacheKey, json);
        } else {
          console.error('[OpenXE Portal] AJAX Error:', action, json);
        }
        return json;
      }).catch(function (err) {
        console.error('[OpenXE Portal] JSON Parse Error:', action, err);
        return { success: false, data: { message: 'invalid_response' } };
      });
    }).catch(function (err) {
      console.error('[OpenXE Portal] Fetch Error:', action, err);
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
    var printLink = qs('.oxp-print', root);
    var downloadLink = qs('.oxp-download', root);
    var sessionToken = '';

    function showError(msg) {
      if (!errorEl) return;
      showMessage(errorEl, msg);
      setVisible(errorEl, !!msg);
    }

    function getSourceIcon(source) {
      var icons = {
        'email': '<svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>',
        'portal': '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
        'phone': '<svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>'
      };
      return icons[source] || icons['portal'];
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

        // Update status
        if (statusValue) statusValue.textContent = data.status_label || data.status_key || '';
        if (statusUpdated) statusUpdated.textContent = data.updated_at ? ('Stand: ' + formatDate(data.updated_at)) : '';

        // Update ticket details
        var ticketNumberEl = qs('.oxp-ticket-number-display', root);
        var customerNameEl = qs('.oxp-customer-name', root);
        var customerAddressEl = qs('.oxp-customer-address', root);

        if (ticketNumberEl) ticketNumberEl.textContent = data.ticket_number || data.ticket_key || '';
        if (customerNameEl) customerNameEl.textContent = data.customer_name || '';
        if (customerAddressEl) {
          var addressParts = [];
          if (data.customer_street) addressParts.push(data.customer_street);
          if (data.customer_zip || data.customer_city) {
            addressParts.push((data.customer_zip || '') + ' ' + (data.customer_city || ''));
          }
          customerAddressEl.textContent = addressParts.join(', ') || '-';
        }
      });
    }

    function renderMessages(list) {
      messagesBox.innerHTML = '';
      if (!list || !list.length) {
        messagesBox.textContent = 'Keine Nachrichten.';
        return;
      }

      var sorted = list.slice().reverse();
      var limit = 4;
      var displayed = sorted.slice(0, limit);
      var hasMore = sorted.length > limit;

      displayed.forEach(function (msg) {
        var isCustomer = msg.author_type === 'customer';
        var wrapper = document.createElement('div');
        wrapper.className = 'oxp-chat-message ' + (isCustomer ? 'customer' : 'staff');

        var preview = document.createElement('div');
        preview.className = 'oxp-message-preview';
        preview.innerHTML = '<div class="oxp-message-header"><span class="oxp-message-author">' + (isCustomer ? 'Ich' : 'Team') + '</span><div class="oxp-message-meta"><span class="oxp-message-source" title="' + (msg.source || 'portal') + '">' + getSourceIcon(msg.source) + '</span><span class="oxp-message-date">' + formatDate(msg.created_at) + '</span></div></div><div class="oxp-message-snippet">' + msg.text + '</div>';

        var full = document.createElement('div');
        full.className = 'oxp-message-full';
        full.style.display = 'none';
        full.textContent = msg.text;

        preview.addEventListener('click', function () {
          var isExpanded = full.style.display !== 'none';
          full.style.display = isExpanded ? 'none' : '';
          preview.classList.toggle('expanded', !isExpanded);
        });

        wrapper.appendChild(preview);
        wrapper.appendChild(full);
        messagesBox.appendChild(wrapper);
      });

      if (hasMore) {
        var showMore = document.createElement('button');
        showMore.className = 'oxp-show-more';
        showMore.textContent = 'Alle Nachrichten anzeigen (' + sorted.length + ')';
        showMore.onclick = function () {
          renderMessagesFull(sorted);
        };
        messagesBox.insertBefore(showMore, messagesBox.firstChild);
      }
    }

    function renderMessagesFull(list) {
      messagesBox.innerHTML = '';
      list.forEach(function (msg) {
        // ... same bubble logic but simplified or as above ...
        var isCustomer = msg.author_type === 'customer';
        var wrapper = document.createElement('div');
        wrapper.className = 'oxp-chat-message ' + (isCustomer ? 'customer' : 'staff');
        var full = document.createElement('div');
        full.className = 'oxp-message-full';
        full.style.display = 'block';
        full.innerHTML = '<div class="oxp-message-header"><span class="oxp-message-author">' + (isCustomer ? 'Ich' : 'Team') + '</span><div class="oxp-message-meta"><span class="oxp-message-source">' + getSourceIcon(msg.source) + '</span><span class="oxp-message-date">' + formatDate(msg.created_at) + '</span></div></div>' + msg.text;
        wrapper.appendChild(full);
        messagesBox.appendChild(wrapper);
      });
    }

    function loadMessages() {
      if (!sessionToken) return;
      portalAjax(config, 'openxe_ticket_portal_messages', { session_token: sessionToken }).then(function (resp) {
        if (!resp.success) return;
        renderMessages(resp.data && resp.data.messages ? resp.data.messages : []);
      });
    }

    function updatePrintLinks() {
      if (!sessionToken || !config.baseUrl) return;
      var printUrl = config.baseUrl + '/index.php?module=ticket&action=portal_print&session_token=' + encodeURIComponent(sessionToken);
      if (printLink) {
        printLink.href = printUrl;
        setVisible(printLink, true);
      }
      if (downloadLink) {
        downloadLink.href = printUrl + '&download=1';
        setVisible(downloadLink, true);
      }
    }

    function renderOfferCards(offers) {
      var container = qs('.oxp-offer-list', root);
      if (!container) return;
      container.innerHTML = '';
      if (!offers || !offers.length) {
        setVisible(qs('.oxp-offer', root), false);
        return;
      }
      setVisible(qs('.oxp-offer', root), true);
      offers.forEach(function (offer) {
        var card = document.createElement('div');
        card.className = 'oxp-offer-card';
        card.innerHTML = '<div class="oxp-offer-header"><strong>Angebot #' + offer.belegnr + '</strong><span>' + formatDate(offer.datum) + '</span></div><div class="oxp-offer-body">Summe: ' + parseFloat(offer.gesamtsumme).toFixed(2) + ' ' + (offer.waehrung || 'EUR') + '</div><div class="oxp-offer-actions"><button class="oxp-btn-accept" data-id="' + offer.id + '">Bestätigen</button><button class="oxp-btn-decline" data-id="' + offer.id + '">Ablehnen</button></div>';
        container.appendChild(card);
      });

      qsa('.oxp-btn-accept', container).forEach(function (btn) {
        btn.onclick = function () { confirmOffer(btn.dataset.id, 'accept'); };
      });
      qsa('.oxp-btn-decline', container).forEach(function (btn) {
        btn.onclick = function () { confirmOffer(btn.dataset.id, 'decline'); };
      });
    }

    function confirmOffer(id, action) {
      var comment = prompt(action === 'accept' ? 'Optionaler Kommentar:' : 'Grund für Ablehnung:');
      if (comment === null) return;
      portalAjax(config, 'openxe_ticket_portal_offer', { session_token: sessionToken, angebot_id: id, offer_action: action, comment: comment }).then(function (resp) {
        if (!resp.success) return alert(formatError(resp));
        alert(action === 'accept' ? 'Angebot bestätigt.' : 'Angebot abgelehnt.');
        cache.clear();
        loadStatus();
        loadOffers();
      });
    }

    function loadOffers() {
      if (!sessionToken) return;
      portalAjax(config, 'openxe_ticket_portal_offers', { session_token: sessionToken }).then(function (resp) {
        if (resp.success) renderOfferCards(resp.data && resp.data.offers ? resp.data.offers : []);
      });
    }

    function showMain() {
      setVisible(loginBox, false);
      setVisible(mainBox, true);
      updatePrintLinks();
      loadStatus();
      loadMessages();
      loadMedia();
      loadOffers();
      loadNotifications();
    }

    function loadNotifications() {
      if (!sessionToken) return;
      portalAjax(config, 'openxe_ticket_portal_notifications', { session_token: sessionToken }).then(function (resp) {
        if (!resp.success) return;
        var container = qs('.oxp-notifications-list', root);
        if (!container) return;

        var data = resp.data || {};
        var statuses = data.statuses || [];
        var defaultAll = data.default_all || false;

        container.innerHTML = '';
        if (!statuses.length) {
          container.innerHTML = '<p>Keine Benachrichtigungsoptionen verfügbar.</p>';
          return;
        }

        statuses.forEach(function (status) {
          var label = document.createElement('label');
          label.className = 'oxp-notification-item';
          var checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.value = status.key;
          checkbox.checked = status.enabled || defaultAll;
          checkbox.className = 'oxp-notification-checkbox';
          label.appendChild(checkbox);
          label.appendChild(document.createTextNode(' ' + status.label));
          container.appendChild(label);
        });
      });
    }

    function saveNotifications() {
      if (!sessionToken) return;
      var checkboxes = qsa('.oxp-notification-checkbox', root);
      var selected = checkboxes.filter(function (cb) { return cb.checked; }).map(function (cb) { return cb.value; });

      var notifyMsg = qs('.oxp-notifications-msg', root);
      if (notifyMsg) notifyMsg.textContent = 'Speichere...';

      portalAjax(config, 'openxe_ticket_portal_notification_save', {
        session_token: sessionToken,
        selected: JSON.stringify(selected)
      }).then(function (resp) {
        if (!resp.success) {
          if (notifyMsg) notifyMsg.textContent = 'Fehler beim Speichern.';
          return;
        }
        if (notifyMsg) {
          notifyMsg.textContent = 'Gespeichert.';
          setTimeout(function () { notifyMsg.textContent = ''; }, 2000);
        }
      });
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

    qs('.oxp-refresh', root).addEventListener('click', function () {
      cache.clear();
      loadStatus();
      loadMessages();
      loadOffers();
      loadNotifications();
    });

    var notificationsSaveBtn = qs('.oxp-notifications-save', root);
    if (notificationsSaveBtn) {
      notificationsSaveBtn.addEventListener('click', saveNotifications);
    }

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
