<form method="post">
  [MESSAGE]
  <fieldset>
    <legend>Ticket Portal (Mitarbeiter)</legend>
    <div><strong>Ticketnummer:</strong> [TICKETNUMMER]</div>
    <div><strong>Betreff:</strong> [BETREFF]</div>
    <p>Kundenstatus und Kommentare werden im Portal angezeigt, sobald sie als kundenrelevant markiert sind.</p>
    <label>Status<br>
      <select name="status_key">
        [STATUS_OPTIONS]
      </select>
    </label>
    <br>
    <label>Kommentar fuer Kunden<br>
      <textarea name="public_note" rows="4" style="width:100%;"></textarea>
    </label>
    <br>
    <label>Interne Notiz<br>
      <textarea name="internal_note" rows="4" style="width:100%;"></textarea>
    </label>
  </fieldset>

  <input type="submit" name="save" value="Speichern" class="btnBlue">
</form>
