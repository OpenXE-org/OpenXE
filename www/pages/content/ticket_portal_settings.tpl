<form method="post">
  [MESSAGE]
  <fieldset>
    <legend>Ticket Portal</legend>
    <label>
      <input type="checkbox" name="ticketportal_enabled" value="1" [PORTAL_ENABLED]>
      Portal aktivieren
    </label>
    <br>
    <label>Portal URL (WordPress)<br>
      <input type="text" name="ticketportal_portal_url" value="[PORTAL_URL]" size="80">
    </label>
    <br>
    <label>
      <input type="checkbox" name="ticketportal_allow_offer_confirm" value="1" [PORTAL_ALLOW_OFFER]>
      Angebotsbestaetigung im Portal erlauben
    </label>
    <br>
    <label>
      <input type="checkbox" name="ticketportal_allow_customer_comments" value="1" [PORTAL_ALLOW_COMMENTS]>
      Kundenkommentare erlauben
    </label>
    <br>
    <label>
      <input type="checkbox" name="ticketportal_notify_all_status" value="1" [PORTAL_NOTIFY_ALL]>
      Standard: Benachrichtigungen fuer alle Statusaenderungen
    </label>
    <br>
    <label>Benachrichtigung Betreff<br>
      <input type="text" name="ticketportal_notify_subject" value="[PORTAL_NOTIFY_SUBJECT]" size="80">
    </label>
    <br>
    <label>Benachrichtigung Text<br>
      <textarea name="ticketportal_notify_body" rows="6" style="width:100%;">[PORTAL_NOTIFY_BODY]</textarea>
    </label>
    <p>Platzhalter (Beispiele):</p>
    <ul>
      <li>{ticket_number} -> 4711</li>
      <li>{ticket_id} -> 123</li>
      <li>{status_key} -> in_bearbeitung</li>
      <li>{status_label} -> In Bearbeitung</li>
      <li>{customer_name} -> Max Mustermann</li>
      <li>{public_note} -> Druckkopf wird ersetzt</li>
      <li>{company_name} -> OpenXE Service</li>
    </ul>
  </fieldset>

  <fieldset>
    <legend>AGB / Double Opt-In</legend>
    <label>AGB URL<br>
      <input type="text" name="ticketportal_agb_url" value="[PORTAL_AGB_URL]" size="80">
    </label>
    <br>
    <label>AGB Version<br>
      <input type="text" name="ticketportal_agb_version" value="[PORTAL_AGB_VERSION]" size="20">
    </label>
  </fieldset>

  <fieldset>
    <legend>Token Laufzeiten (Minuten)</legend>
    <label>Session TTL<br>
      <input type="number" name="ticketportal_session_ttl_min" value="[PORTAL_SESSION_TTL]" min="1">
    </label>
    <br>
    <label>Code TTL<br>
      <input type="number" name="ticketportal_code_ttl_min" value="[PORTAL_CODE_TTL]" min="1">
    </label>
    <br>
    <label>Magic Link TTL<br>
      <input type="number" name="ticketportal_magic_ttl_min" value="[PORTAL_MAGIC_TTL]" min="1">
    </label>
    <br>
    <label>Double Opt-In TTL<br>
      <input type="number" name="ticketportal_doi_ttl_min" value="[PORTAL_DOI_TTL]" min="1">
    </label>
  </fieldset>

  <fieldset>
    <legend>Sicherheit</legend>
    <label>Shared Secret (optional)<br>
      <input type="text" id="ticketportal_shared_secret" name="ticketportal_shared_secret" value="[PORTAL_SHARED_SECRET]" size="60" autocomplete="off">
    </label>
    <button type="button" id="portal-secret-generate">Generieren</button>
    <button type="button" id="portal-secret-copy">Kopieren</button>
    <p>Wenn gesetzt, muss das WordPress Plugin den gleichen Wert senden.</p>
    <label>
      <input type="checkbox" name="ticketportal_log_enabled" value="1" [PORTAL_LOG_ENABLED]>
      Portal-Log aktivieren
    </label>
    <p>Logdatei: <code>[PORTAL_LOG_PATH]</code></p>
    <label>Letzte Eintraege</label>
    <textarea readonly rows="8" style="width:100%;">[PORTAL_LOG_CONTENT]</textarea>
    <button type="submit" name="clear_log" value="1" class="btn">Log leeren</button>
    <br>
    <label>Max. Fehlversuche<br>
      <input type="number" name="ticketportal_max_attempts" value="[PORTAL_MAX_ATTEMPTS]" min="1">
    </label>
    <br>
    <label>Sperrzeit nach Fehlversuchen (Minuten)<br>
      <input type="number" name="ticketportal_lockout_min" value="[PORTAL_LOCKOUT_MIN]" min="1">
    </label>
  </fieldset>

  <fieldset>
    <legend>Statusmodell</legend>
    <p><strong>Kundenstatus Texte</strong></p>
    <table>
      <thead>
        <tr>
          <th>Status Key</th>
          <th>Bezeichnung</th>
        </tr>
      </thead>
      <tbody>
        [STATUS_LABEL_ROWS]
      </tbody>
    </table>
    <p><strong>Mapping: Interner Status -&gt; Kundenstatus</strong></p>
    <table>
      <thead>
        <tr>
          <th>Interner Status</th>
          <th>Kundenstatus</th>
        </tr>
      </thead>
      <tbody>
        [STATUS_MAP_ROWS]
      </tbody>
    </table>
  </fieldset>

  <fieldset>
    <legend>WordPress Plugin</legend>
    <p><a href="[PORTAL_PLUGIN_URL]">Plugin herunterladen</a></p>
    <p>Kurzanleitung:</p>
    <ol>
      <li>Plugin in WordPress hochladen/aktivieren.</li>
      <li>OpenXE Base URL in den Plugin-Einstellungen setzen.</li>
      <li>Shortcode <code>[openxe_ticket_portal]</code> in eine Seite einfuegen.</li>
    </ol>
  </fieldset>

  <input type="submit" name="save" value="Speichern" class="btnBlue">
</form>

<script>
(function () {
  var input = document.getElementById('ticketportal_shared_secret');
  var btnGenerate = document.getElementById('portal-secret-generate');
  var btnCopy = document.getElementById('portal-secret-copy');
  if (!input || !btnGenerate || !btnCopy) {
    return;
  }

  function toHex(buffer) {
    return Array.prototype.map.call(buffer, function (b) {
      return ('00' + b.toString(16)).slice(-2);
    }).join('');
  }

  function generateSecret() {
    if (window.crypto && window.crypto.getRandomValues) {
      var bytes = new Uint8Array(32);
      window.crypto.getRandomValues(bytes);
      return toHex(bytes);
    }
    var fallback = '';
    for (var i = 0; i < 64; i++) {
      fallback += Math.floor(Math.random() * 16).toString(16);
    }
    return fallback;
  }

  btnGenerate.addEventListener('click', function () {
    input.value = generateSecret();
    input.focus();
    input.select();
  });

  btnCopy.addEventListener('click', function () {
    input.focus();
    input.select();
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(input.value).catch(function () {});
      return;
    }
    document.execCommand('copy');
  });
})();
</script>
