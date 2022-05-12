<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

[MESSAGE]
<fieldset><legend>{|Informationen von Kontoauszug|}</legend>
<form action="" method="post">
<table width="100%">
<tr><td width="100">Datum:</td><td><input type="text" value="[DATUM]" name="datum">&nbsp;<i>(Bitte gleiches Format eingeben: Jahr-Monat-Tag)</i></td></tr>
<tr><td>Vorgang:</td><td><input type="text" value="[VORGANG]" name="vorgang" size="80"></td></tr>
<tr><td>Soll:</td><td><input type="text" value="[SOLL]" name="soll"></td></tr>
<tr><td>Haben:</td><td><input type="text" value="[HABEN]" name="haben"></td></tr>
<tr><td>Geb&uuml;hr:</td><td><input type="text" value="[GEBUEHR]" name="gebuehr"></td></tr>
<tr><td>W&auml;hrung:</td><td><input type="text" value="[WAEHRUNG]" name="waehrung"></td></tr>
<tr><td>Gegenkonto:</td><td><input type="text" value="[GEGENKONTO]" name="gegenkonto">&nbsp;<i>(Dient nur als Vorschlag und f&uuml;r Pr&uuml;fsummen)</i></td></tr>

<tr><td><i>Interne Bemerkung:</i></td><td><input type="text" value="[INTERNEBEMERKUNG]" name="internebemerkung" size="80"></td></tr>
<tr><td><i>Datev:</i></td><td><input type="checkbox" value="1" [DATEVFERTIG] name="datevfertig">&nbsp;abgeschlossen&nbsp;<i>(wird durch Autoabgleich typisch gesetzt)</i></td></tr>
<tr><td><i>Kontozeile:</i></td><td><input type="checkbox" value="1" [KONTOFERTIG] name="kontofertig">&nbsp;abgeschlossen&nbsp;<i>(wird durch Zahlungseingang typisch gesetzt)</i></td></tr>
<tr><td><i>Importfehler:</i></td><td><input type="checkbox" value="1" [IMPORTFEHLER] name="importfehler">&nbsp;Importfehler</td></tr>
<tr><td colspan="2"><i>Kursiv geschriebene Optionen k&ouml;nnen auch bei schreibgesch&uuml;tzten Konten bearbeitet werden.</i></td></tr>
<tr><td></td><td align="right"><input type="submit" value="Speichern" name="submit"></td></tr>
</table>
</form>
</fieldset>

<fieldset><legend>{|Datev Buchungen|}</legend>
[DATEV]
</fieldset>


<fieldset><legend>Proficheck: Eingangsbuchungen</legend>
[ZAHLUNGEINGANG]
</fieldset>

<fieldset><legend>Proficheck: Ausgangsbuchung</legend>
[ZAHLUNGAUSGANG]
</fieldset>



</div>

<!-- tab view schließen -->
</div>

