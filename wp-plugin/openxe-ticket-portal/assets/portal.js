/* global OpenXETwitterPortal */
(function () {
  function qs(selector, root) {
    return (root || document).querySelector(selector);
  }

  function qsa(selector, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(selector));
  }

  function formatDate(value) {
    if (!value) {
      return '';
    }
    return value.replace('T', ' ').replace('Z', '');
  }

  function setVisible(el, visible) {
    if (!el) {
      return;
    }
    el.style.display = visible ? '' : 'none';
  }

  function showMessage(el, text) {
    if (!el) {
      return;
    }
    el.textContent = text || '';
  }

  function portalAjax(config, action, payload) {
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
      return response.json().catch(function () {
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
        try {
          config = JSON.parse(rawConfig);
        } catch (e) {
          config = null;
        }
      }
    }
    var baseUrl = (config && config.baseUrl) || '';
    var defaultVerifier = (config && config.defaultVerifier) || 'auto';

    var errorEl = qs('.oxp-error', root);
    if (!config || !config.ajaxUrl || !config.nonce) {
      setVisible(errorEl, true);
      showMessage(errorEl, 'Portal Konfiguration fehlt. Bitte Plugin aktualisieren oder Cache leeren.');
      return;
    }
    if (!baseUrl) {
      setVisible(errorEl, true);
      showMessage(errorEl, 'Portal ist nicht konfiguriert. Bitte OpenXE Base URL setzen.');
      return;
    }

    var loginBox = qs('.oxp-login', root);
    var mainBox = qs('.oxp-main', root);
    var loginMsg = qs('.oxp-login-msg', root);
    var messageMsg = qs('.oxp-message-msg', root);
    var statusValue = qs('.oxp-status-value', root);
    var statusUpdated = qs('.oxp-status-updated', root);
    var messagesBox = qs('.oxp-messages', root);
    var notificationsList = qs('.oxp-notifications-list', root);
    var notificationsSave = qs('.oxp-notifications-save', root);
    var notificationsMsg = qs('.oxp-notifications-msg', root);

    var ticketInput = qs('.oxp-ticket-number', root);
    var ticketRow = qs('.oxp-token-row', root);
    var verifierSelect = qs('.oxp-verifier', root);
    var verifierInput = qs('.oxp-verifier-value', root);
    var loginButton = qs('.oxp-login-btn', root);
    var refreshButton = qs('.oxp-refresh', root);
    var sendButton = qs('.oxp-send', root);
    var messageText = qs('.oxp-message-text', root);

    var printLink = qs('.oxp-print', root);
    var downloadLink = qs('.oxp-download', root);

    var offerBox = qs('.oxp-offer', root);
    var offerToggle = qs('.oxp-offer-toggle', root);
    var offerIdInput = qs('.oxp-offer-id', root);
    var offerActionSelect = qs('.oxp-offer-action', root);
    var offerComment = qs('.oxp-offer-comment', root);
    var offerAgb = qs('.oxp-offer-agb', root);
    var offerAccept = qs('.oxp-offer-accept', root);
    var offerSubmit = qs('.oxp-offer-submit', root);
    var offerMsg = qs('.oxp-offer-msg', root);

    if (offerToggle) {
      offerToggle.addEventListener('click', function () {
        var isVisible = offerBox.style.display !== 'none';
        setVisible(offerBox, !isVisible);
        offerToggle.textContent = isVisible ? 'Angebot bestaetigen' : 'Angebot verbergen';
      });
    }

    var params = new URLSearchParams(window.location.search || '');
    var urlToken = params.get('token') || params.get('ticket_token') || '';
    var urlTicket = params.get('ticket') || params.get('ticket_number') || '';
    var urlOffer = params.get('offer_id') || params.get('angebot_id') || '';
    var urlMagic = params.get('magic_token') || params.get('magicToken') || '';

    var tokenMode = false;
    if (urlTicket) {
      ticketInput.value = urlTicket;
      tokenMode = false;
      setVisible(ticketRow, true);
    } else if (urlToken) {
      ticketInput.value = urlToken;
      tokenMode = true;
      setVisible(ticketRow, true);
    }

    if (ticketInput) {
      ticketInput.addEventListener('input', function () {
        tokenMode = false;
      });
    }
    if (urlOffer) {
      offerIdInput.value = urlOffer;
      setVisible(offerBox, true);
      if (offerToggle) {
        offerToggle.textContent = 'Angebot verbergen';
      }
    }
    if (defaultVerifier && verifierSelect) {
      verifierSelect.value = defaultVerifier;
    }

    var sessionKey = null;
    var sessionToken = '';

    function setSession(token, key, mode) {
      sessionToken = token;
      sessionKey = 'openxe_ticket_portal_session_' + mode + '_' + key;
      sessionStorage.setItem(sessionKey, JSON.stringify({ token: token }));
    }

    function restoreSession(key, mode) {
      sessionKey = 'openxe_ticket_portal_session_' + mode + '_' + key;
      var stored = sessionStorage.getItem(sessionKey);
      if (!stored) {
        return '';
      }
      try {
        var parsed = JSON.parse(stored);
        return parsed && parsed.token ? parsed.token : '';
      } catch (e) {
        return '';
      }
    }

    function updatePrintLinks() {
      if (!sessionToken) {
        return;
      }
      var printUrl = baseUrl + '/index.php?module=ticket&action=portal_print&session_token=' + encodeURIComponent(sessionToken);
      if (printLink) {
        printLink.href = printUrl;
      }
      if (downloadLink) {
        downloadLink.href = printUrl + '&download=1';
      }
    }

    function showError(message) {
      if (!errorEl) {
        return;
      }
      if (message) {
        showMessage(errorEl, message);
        setVisible(errorEl, true);
        return;
      }
      showMessage(errorEl, '');
      setVisible(errorEl, false);
    }

    function formatError(resp, fallback) {
      var raw = resp && resp.data && (resp.data.message || (resp.data.data && resp.data.data.error));
      if (!raw) {
        return fallback || 'Aktion fehlgeschlagen.';
      }
      if (raw === 'access_locked') {
        return 'Zu viele Fehlversuche. Bitte spaeter erneut versuchen.';
      }
      if (raw === 'rate_limited') {
        return 'Zu viele Anfragen. Bitte spaeter erneut versuchen.';
      }
      if (raw === 'verification_failed') {
        return 'Verifikation fehlgeschlagen.';
      }
      if (raw === 'verification_expired') {
        return 'Code abgelaufen. Bitte erneut anfordern.';
      }
      if (raw === 'email_required') {
        return 'E-Mail fehlt im Ticket. Bitte andere Verifikation nutzen.';
      }
      if (raw === 'plz_required') {
        return 'PLZ fehlt im Ticket. Bitte andere Verifikation nutzen.';
      }
      if (raw === 'token_not_found') {
        return 'Token nicht gefunden.';
      }
      if (raw === 'ticket_not_found') {
        return 'Ticket nicht gefunden.';
      }
      if (raw === 'access_failed') {
        return 'Zugriff konnte nicht erstellt werden.';
      }
      if (raw === 'forbidden') {
        return 'Zugriff verweigert. Shared Secret pruefen.';
      }
      if (raw === 'invalid_response') {
        return 'Ungueltige Antwort vom Server.';
      }
      if (raw === 'request_failed') {
        return 'Server nicht erreichbar.';
      }
      return raw;
    }

    function loadStatus() {
      if (!sessionToken) {
        return;
      }
      portalAjax(config, 'openxe_ticket_portal_status', { session_token: sessionToken }).then(function (resp) {
        if (!resp || !resp.success) {
          showError(formatError(resp, 'Status konnte nicht geladen werden.'));
          return;
        }
        showError('');
        var data = resp.data || {};
        statusValue.textContent = data.status_label || data.status_key || '';
        statusUpdated.textContent = data.updated_at ? ('Aktualisiert: ' + formatDate(data.updated_at)) : '';
      });
    }

    function renderMessages(list) {
      messagesBox.innerHTML = '';
      if (!list || !list.length) {
        messagesBox.textContent = 'Keine Nachrichten vorhanden.';
        return;
      }
      list.forEach(function (msg) {
        var item = document.createElement('div');
        item.className = 'oxp-message';
        var meta = document.createElement('div');
        meta.className = 'oxp-message-meta';
        var author = msg.author_type === 'customer' ? 'Kunde' : 'Team';
        meta.innerHTML = '<span>' + author + (msg.created_at ? ' · ' + formatDate(msg.created_at) : '') + '</span>';

        if (window.speechSynthesis) {
          var speakBtn = document.createElement('button');
          speakBtn.textContent = 'Vorlesen';
          speakBtn.className = 'oxp-btn-tts';
          speakBtn.style.padding = '2px 6px';
          speakBtn.style.fontSize = '10px';
          speakBtn.style.marginLeft = '8px';
          speakBtn.addEventListener('click', function () {
            var utterance = new SpeechSynthesisUtterance(msg.text);
            utterance.lang = 'de-DE';
            window.speechSynthesis.speak(utterance);
          });
          meta.appendChild(speakBtn);
        }

        var body = document.createElement('div');
        body.textContent = msg.text || '';
        item.appendChild(meta);
        item.appendChild(body);
        messagesBox.appendChild(item);
      });
    }

    function renderNotifications(list) {
      notificationsList.innerHTML = '';
      if (!list || !list.length) {
        notificationsList.textContent = 'Keine Statusauswahl verfuegbar.';
        return;
      }
      list.forEach(function (item) {
        var row = document.createElement('div');
        row.className = 'oxp-notification-item';
        var input = document.createElement('input');
        input.type = 'checkbox';
        input.value = item.key;
        input.checked = !!item.enabled;
        input.id = 'oxp-notify-' + item.key;
        var label = document.createElement('label');
        label.setAttribute('for', input.id);
        label.textContent = item.label || item.key;
        row.appendChild(input);
        row.appendChild(label);
        notificationsList.appendChild(row);
      });
    }

    function loadMessages() {
      if (!sessionToken) {
        return;
      }
      portalAjax(config, 'openxe_ticket_portal_messages', { session_token: sessionToken }).then(function (resp) {
        if (!resp || !resp.success) {
          showError(formatError(resp, 'Nachrichten konnten nicht geladen werden.'));
          return;
        }
        showError('');
        renderMessages(resp.data && resp.data.messages ? resp.data.messages : []);
      });
    }

    function loadNotifications() {
      if (!sessionToken) {
        return;
      }
      portalAjax(config, 'openxe_ticket_portal_notifications_get', { session_token: sessionToken }).then(function (resp) {
        if (!resp || !resp.success) {
          showError(formatError(resp, 'Benachrichtigungen konnten nicht geladen werden.'));
          return;
        }
        showError('');
        renderNotifications(resp.data && resp.data.statuses ? resp.data.statuses : []);
      });
    }

    function renderOffers(list) {
      if (!offerBox) {
        return;
      }
      var listContainer = qs('.oxp-offer-list', offerBox);
      if (!listContainer) {
        return;
      }
      listContainer.innerHTML = '';
      if (!list || !list.length) {
        setVisible(offerBox, false);
        if (offerToggle) {
          setVisible(offerToggle, false);
        }
        return;
      }
      setVisible(offerToggle, true);
      list.forEach(function (offer) {
        var card = document.createElement('div');
        card.className = 'oxp-offer-card';
        card.innerHTML =
          '<div class=\"oxp-offer-header\">' +
          '<strong>Angebot #' + (offer.belegnr || offer.id) + '</strong>' +
          '<span>' + formatDate(offer.datum) + '</span>' +
          '</div>' +
          '<div class=\"oxp-offer-body\">' +
          'Summe: ' + parseFloat(offer.gesamtsumme).toFixed(2) + ' ' + (offer.waehrung || 'EUR') +
          '</div>' +
          '<div class=\"oxp-offer-actions\">' +
          '<button class=\"oxp-btn oxp-btn-small oxp-offer-select\" data-id=\"' + offer.id + '\">Auswaehlen</button>' +
          '</div>';
        listContainer.appendChild(card);
      });

      qsa('.oxp-offer-select', listContainer).forEach(function (btn) {
        btn.addEventListener('click', function () {
          offerIdInput.value = btn.getAttribute('data-id');
          setVisible(offerBox, true);
          offerBox.scrollIntoView({ behavior: 'smooth' });
        });
      });
    }

    function loadOffers() {
      if (!sessionToken) {
        return;
      }
      portalAjax(config, 'openxe_ticket_portal_offers', { session_token: sessionToken }).then(function (resp) {
        if (!resp || !resp.success) {
          return;
        }
        renderOffers(resp.data && resp.data.offers ? resp.data.offers : []);
      });
    }

    function renderMedia(list) {
      if (!mainBox) {
        return;
      }
      var container = qs('.oxp-media-list', mainBox);
      if (!container) {
        return;
      }
      container.innerHTML = '';
      if (!list || !list.length) {
        setVisible(qs('.oxp-media', mainBox), false);
        return;
      }
      setVisible(qs('.oxp-media', mainBox), true);
      list.forEach(function (m) {
        var size = Math.round(m.file_size / 1024) + ' KB';
        var item = document.createElement('div');
        item.className = 'oxp-media-item';
        item.style.marginBottom = '8px';
        item.innerHTML =
          '<a href=\"#\" class=\"oxp-media-download\" data-id=\"' + m.id + '\">' +
          '<strong>' + (m.filename || 'Datei') + '</strong></a> ' +
          '<small>(' + size + ', ' + formatDate(m.created_at) + ')</small>';
        container.appendChild(item);
      });

      qsa('.oxp-media-download', container).forEach(function (link) {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          var mediaId = link.getAttribute('data-id');
          downloadMedia(mediaId, link.querySelector('strong').textContent);
        });
      });
    }

    function downloadMedia(mediaId, filename) {
      var body = 'session_token=' + encodeURIComponent(sessionToken) +
        '&media_id=' + encodeURIComponent(mediaId) +
        '&nonce=' + encodeURIComponent(config.nonce) +
        '&action=openxe_ticket_portal_media_download';

      fetch(config.ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
      })
        .then(function (resp) { return resp.blob(); })
        .then(function (blob) {
          var url = window.URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.style.display = 'none';
          a.href = url;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
        });
    }

    function loadMedia() {
      if (!sessionToken) {
        return;
      }
      portalAjax(config, 'openxe_ticket_portal_media', { session_token: sessionToken }).then(function (resp) {
        if (!resp || !resp.success) {
          return;
        }
        renderMedia(resp.data && resp.data.media ? resp.data.media : []);
      });
    }

    function showMain() {
      setVisible(loginBox, false);
      setVisible(mainBox, true);
      updatePrintLinks();
      loadStatus();
      loadMessages();
      loadNotifications();
      loadOffers();
      loadMedia();
    }

    function stripMagicToken() {
      try {
        var url = new URL(window.location.href);
        url.searchParams.delete('magic_token');
        url.searchParams.delete('magicToken');
        history.replaceState(null, '', url.toString());
      } catch (e) {
        return;
      }
    }

    function loginWithMagicToken(magicToken) {
      if (!magicToken) {
        return;
      }
      showMessage(loginMsg, 'Magic Link wird geprueft...');
      showError('');
      portalAjax(config, 'openxe_ticket_portal_magic', { magic_token: magicToken }).then(function (resp) {
        if (!resp || !resp.success) {
          showMessage(loginMsg, formatError(resp, 'Magic Link ungueltig.'));
          return;
        }
        if (resp.data && resp.data.session_token) {
          sessionToken = resp.data.session_token;
          stripMagicToken();
          showMain();
          return;
        }
        showMessage(loginMsg, 'Login fehlgeschlagen.');
      });
    }

    function tryAutoLogin() {
      if (urlMagic) {
        return;
      }
      var loginValue = ticketInput.value.trim();
      if (!loginValue) {
        return;
      }
      var mode = tokenMode ? 'token' : 'ticket';
      var storedToken = restoreSession(loginValue, mode);
      if (storedToken) {
        sessionToken = storedToken;
        showMain();
      }
    }

    loginButton.addEventListener('click', function () {
      showMessage(loginMsg, '');
      showError('');
      var loginValue = ticketInput.value.trim();
      var verifierType = verifierSelect.value;
      var verifierValue = verifierInput.value.trim();
      if (!loginValue) {
        showMessage(loginMsg, 'Ticketnummer fehlt.');
        return;
      }
      var payload = {
        verifier_type: verifierType,
        verifier_value: verifierValue
      };
      if (tokenMode) {
        payload.token = loginValue;
      } else {
        payload.ticket_number = loginValue;
      }
      portalAjax(config, 'openxe_ticket_portal_session', payload).then(function (resp) {
        if (!resp || !resp.success) {
          showMessage(loginMsg, formatError(resp, 'Login fehlgeschlagen.'));
          return;
        }
        if (resp.data && resp.data.status === 'verification_sent') {
          showMessage(loginMsg, 'Code gesendet. Bitte Code eingeben.');
          return;
        }
        if (resp.data && resp.data.session_token) {
          setSession(resp.data.session_token, loginValue, tokenMode ? 'token' : 'ticket');
          showMain();
          return;
        }
        showMessage(loginMsg, 'Login fehlgeschlagen.');
      });
    });

    refreshButton.addEventListener('click', function () {
      loadStatus();
      loadMessages();
    });

    sendButton.addEventListener('click', function () {
      showMessage(messageMsg, '');
      var text = messageText.value.trim();
      if (!text) {
        showMessage(messageMsg, 'Bitte Nachricht eingeben.');
        return;
      }
      portalAjax(config, 'openxe_ticket_portal_message', {
        session_token: sessionToken,
        text: text
      }).then(function (resp) {
        if (!resp || !resp.success) {
          showMessage(messageMsg, formatError(resp, 'Nachricht konnte nicht gesendet werden.'));
          return;
        }
        messageText.value = '';
        loadMessages();
      });
    });

    if (notificationsSave) {
      notificationsSave.addEventListener('click', function () {
        showMessage(notificationsMsg, '');
        var selected = [];
        qsa('.oxp-notification-item input[type="checkbox"]', notificationsList).forEach(function (input) {
          if (input.checked) {
            selected.push(input.value);
          }
        });
        portalAjax(config, 'openxe_ticket_portal_notifications_set', {
          session_token: sessionToken,
          selected: JSON.stringify(selected)
        }).then(function (resp) {
          if (!resp || !resp.success) {
            showMessage(notificationsMsg, formatError(resp, 'Speichern fehlgeschlagen.'));
            return;
          }
          showMessage(notificationsMsg, 'Benachrichtigungen gespeichert.');
        });
      });
    }

    offerSubmit.addEventListener('click', function () {
      showMessage(offerMsg, '');
      var offerId = offerIdInput.value.trim();
      var action = offerActionSelect.value;
      var comment = offerComment.value.trim();
      var agbVersion = offerAgb.value.trim();
      if (!offerId) {
        showMessage(offerMsg, 'Bitte Angebot ID angeben.');
        return;
      }
      if (action === 'accept' && !offerAccept.checked) {
        showMessage(offerMsg, 'Bitte AGB akzeptieren.');
        return;
      }
      portalAjax(config, 'openxe_ticket_portal_offer', {
        session_token: sessionToken,
        angebot_id: offerId,
        offer_action: action,
        comment: comment,
        agb_version: agbVersion
      }).then(function (resp) {
        if (!resp || !resp.success) {
          showMessage(offerMsg, formatError(resp, 'Aktion fehlgeschlagen.'));
          return;
        }
        if (resp.data && resp.data.status === 'pending_doi') {
          showMessage(offerMsg, 'Bestaetigung gesendet. Bitte E-Mail pruefen.');
          return;
        }
        showMessage(offerMsg, 'Aktion gespeichert.');
        loadStatus();
      });
    });

    tryAutoLogin();

    if (urlMagic) {
      loginWithMagicToken(urlMagic);
    }
  }

  function initAll() {
    qsa('.openxe-portal').forEach(initPortal);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
