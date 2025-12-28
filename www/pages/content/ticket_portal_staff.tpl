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
    <br>
    <input type="submit" name="save" value="Status/Kommentar Speichern" class="btnBlue">
  </fieldset>
</form>

<hr>

<form method="post" action="index.php?module=ticket&action=portal_staff_upload&id=[ID]" enctype="multipart/form-data">
  <fieldset>
    <legend>Medien Upload (Bilder/PDF)</legend>
    <p>Dateiformate: JPG, PNG, WebP, PDF (max. 10MB)</p>
    <input type="file" name="file" required>
    <br><br>
    <label>
      <input type="checkbox" name="is_public" value="1"> F&uuml;r Kunden im Portal sichtbar
    </label>
    <br><br>
    <input type="submit" value="Datei Hochladen" class="btnBlue">
  </fieldset>
</form>

<fieldset>
  <legend>Hochgeladene Medien</legend>
  [MEDIA_LIST]
</fieldset>
