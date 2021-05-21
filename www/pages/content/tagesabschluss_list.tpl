<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post">
<fieldset><legend>{|Auswahl|}</legend>

<table>
<tr><td width="200">Auswahl Belegarten:</td><td>
<table>
<tr><td><input type="checkbox" name="auswahl[]" value="rechnung"></td><td>Rechnung</td></tr>
<tr><td><input type="checkbox" name="auswahl[]" value="lieferschein"></td><td>Lieferscheine</td></tr>
<tr><td><input type="checkbox" name="auswahl[]" value="angebot"></td><td>Angebot</td></tr>
<tr><td><input type="checkbox" name="auswahl[]" value="auftrag"></td><td>Auftrag</td></tr>
<tr><td><input type="checkbox" name="auswahl[]" value="gutschrift"></td><td>Gutschrift</td></tr>
<tr><td><input type="checkbox" name="auswahl[]" value="bestellung"></td><td>Bestellung</td></tr>
</table>
</td></tr>

<tr><td>Datum von:</td><td><input type="text" size="10" value="[VON]" name="von" id="von"></td></tr>
<tr><td>Datum bis:</td><td><input type="text" size="10" value="[BIS]" name="bis" id="bis"></td></tr>
<tr><td>Exemplare pro Beleg:</td><td><input type="text" size="10" value="1" name="exemplare"></td></tr>
<tr><td>Projekt:</td><td><input type="text" size="10" value="" name="projekt" id="projekt">&nbsp;<i>(Optional)</i></td></tr>
<tr><td>Drucker:</td><td><select name="drucker">[DRUCKER]</select></td></tr>
</table>
</fieldset>
<table width="100%"><tr><td align="center"><input type="submit" value="Stapeldruck starten" name="drucken"></td></tr></table>
</form>
</div>

<!-- tab view schließen -->
</div>

