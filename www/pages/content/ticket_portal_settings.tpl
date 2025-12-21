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
    <p>Platzhalter: {ticket_number}, {ticket_id}, {status_key}, {status_label}, {customer_name}, {public_note}, {company_name}</p>
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
      <input type="text" name="ticketportal_shared_secret" value="[PORTAL_SHARED_SECRET]" size="60" autocomplete="off">
    </label>
    <p>Wenn gesetzt, muss das WordPress Plugin den gleichen Wert senden.</p>
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
