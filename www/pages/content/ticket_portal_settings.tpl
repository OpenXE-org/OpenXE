<form method="post">
  [MESSAGE]
  <fieldset>
    <legend>Ticket Portal</legend>
    <label>
      <input type="checkbox" name="ticketportal_enabled" value="1" [PORTAL_ENABLED]>
      Portal aktivieren
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
    <legend>AGB / DOI</legend>
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
    <label>DOI TTL<br>
      <input type="number" name="ticketportal_doi_ttl_min" value="[PORTAL_DOI_TTL]" min="1">
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
