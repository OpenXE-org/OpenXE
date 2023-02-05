<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form method="POST">
<fieldset><legend>{|Umrechnung|}</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="3"><tr><td>
  <table align="center" border="0" cellspacing="0" cellpadding="3">
    <tr><td>W&auml;hrung von</td><td><select name="waehrung_von">[WAEHRUNG_VON]</select></td></tr>
    <tr><td>W&auml;hrung nach</td><td><select name="waehrung_nach">[WAEHRUNG_NACH]</select></td></tr>
    <tr><td>Kurs:</td><td><input type="number" lang="de_DE" step="0.0001" name="kurs" value="[KURS]" /></td></tr>
    <tr><td>g&uuml;ltig bis</td><td><input type="text" name="gueltig_bis" id="gueltig_bis" value="[GUELTIG_BIS]" /></td></tr>
    <tr><td></td><td><input type="submit" value="Speichern" name="submit" /></td></tr>
  </table>
</tr></td></table>
</fieldset>
</form>
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>




