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

  function portalAjax(action, payload) {
    var data = new FormData();
    data.append('action', action);
    data.append('nonce', OpenXETwitterPortal.nonce);
    Object.keys(payload || {}).forEach(function (key) {
      data.append(key, payload[key]);
    });
    return fetch(OpenXETwitterPortal.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: data
    }).then(function (response) {
      return response.json();
    });
  }

  function initPortal(root) {
    var baseUrl = (OpenXETwitterPortal && OpenXETwitterPortal.baseUrl) || '';
    var defaultVerifier = (OpenXETwitterPortal && OpenXETwitterPortal.defaultVerifier) || 'plz';

    var errorEl = qs('.oxp-error', root);
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

    var tokenInput = qs('.oxp-token', root);
    var tokenRow = qs('.oxp-token-row', root);
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

    verifierSelect.value = defaultVerifier;

    var params = new URLSearchParams(window.location.search || '');
    var urlToken = params.get('token') || params.get('ticket_token') || '';
    var urlOffer = params.get('offer_id') || params.get('angebot_id') || '';
    var urlVerifierType = params.get('verifier_type') || params.get('verifierType') || '';
    var urlVerifierValue = params.get('verifier_value') || params.get('verifierValue') || '';

    if (urlToken) {
      tokenInput.value = urlToken;
      setVisible(tokenRow, false);
    }
    if (urlOffer) {
      offerIdInput.value = urlOffer;
      setVisible(offerBox, true);
      if (offerToggle) {
        offerToggle.textContent = 'Angebot verbergen';
      }
    }
    if (urlVerifierType) {
      verifierSelect.value = urlVerifierType;
    }
    if (urlVerifierValue) {
      verifierInput.value = urlVerifierValue;
    }

    var sessionKey = null;
    var sessionToken = '';

    function setSession(token, ticketToken) {
      sessionToken = token;
      sessionKey = 'openxe_ticket_portal_session_' + ticketToken;
      sessionStorage.setItem(sessionKey, JSON.stringify({ token: token }));
    }

    function restoreSession(ticketToken) {
      sessionKey = 'openxe_ticket_portal_session_' + ticketToken;
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

    function loadStatus() {
      if (!sessionToken) {
        return;
      }
      portalAjax('openxe_ticket_portal_status', { session_token: sessionToken }).then(function (resp) {
        if (!resp || !resp.success) {
          return;
        }
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
        meta.textContent = author + (msg.created_at ? ' · ' + formatDate(msg.created_at) : '');
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
      portalAjax('openxe_ticket_portal_messages', { session_token: sessionToken }).then(function (resp) {
        if (!resp || !resp.success) {
          return;
        }
        renderMessages(resp.data && resp.data.messages ? resp.data.messages : []);
      });
    }

    function loadNotifications() {
      if (!sessionToken) {
        return;
      }
      portalAjax('openxe_ticket_portal_notifications_get', { session_token: sessionToken }).then(function (resp) {
        if (!resp || !resp.success) {
          return;
        }
        renderNotifications(resp.data && resp.data.statuses ? resp.data.statuses : []);
      });
    }

    function showMain() {
      setVisible(loginBox, false);
      setVisible(mainBox, true);
      updatePrintLinks();
      loadStatus();
      loadMessages();
      loadNotifications();
    }

    function tryAutoLogin() {
      var token = tokenInput.value.trim();
      if (!token) {
        return;
      }
      var storedToken = restoreSession(token);
      if (storedToken) {
        sessionToken = storedToken;
        showMain();
      }
    }

    loginButton.addEventListener('click', function () {
      showMessage(loginMsg, '');
      var token = tokenInput.value.trim();
      var verifierType = verifierSelect.value;
      var verifierValue = verifierInput.value.trim();
      if (!token) {
        showMessage(loginMsg, 'Token fehlt.');
        return;
      }
      portalAjax('openxe_ticket_portal_session', {
        token: token,
        verifier_type: verifierType,
        verifier_value: verifierValue
      }).then(function (resp) {
        if (!resp || !resp.success) {
          var message = (resp && resp.data && (resp.data.message || (resp.data.data && resp.data.data.error))) || 'Login fehlgeschlagen.';
          showMessage(loginMsg, message);
          return;
        }
        if (resp.data && resp.data.status === 'verification_sent') {
          showMessage(loginMsg, 'Code gesendet. Bitte Code eingeben.');
          return;
        }
        if (resp.data && resp.data.session_token) {
          setSession(resp.data.session_token, token);
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
      portalAjax('openxe_ticket_portal_message', {
        session_token: sessionToken,
        text: text
      }).then(function (resp) {
        if (!resp || !resp.success) {
          showMessage(messageMsg, 'Nachricht konnte nicht gesendet werden.');
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
        portalAjax('openxe_ticket_portal_notifications_set', {
          session_token: sessionToken,
          selected: JSON.stringify(selected)
        }).then(function (resp) {
          if (!resp || !resp.success) {
            showMessage(notificationsMsg, 'Speichern fehlgeschlagen.');
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
      portalAjax('openxe_ticket_portal_offer', {
        session_token: sessionToken,
        angebot_id: offerId,
        offer_action: action,
        comment: comment,
        agb_version: agbVersion
      }).then(function (resp) {
        if (!resp || !resp.success) {
          var message = (resp && resp.data && resp.data.message) || 'Aktion fehlgeschlagen.';
          showMessage(offerMsg, message);
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

    if (urlToken && urlVerifierType && urlVerifierValue && loginButton) {
      loginButton.click();
    }
  }

  function initAll() {
    if (!window.OpenXETwitterPortal) {
      return;
    }
    qsa('.openxe-portal').forEach(initPortal);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
