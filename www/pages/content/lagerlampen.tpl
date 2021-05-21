<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Manuelle Lagerampel</a></li>
        <li><a href="#tabs-2">Automatische Lagerampel</a></li>
        <li><a href="#tabs-3">Online-Shop Optionen</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

<table height="80" width="100%"><tr><td>
<fieldset><legend>&nbsp;Filter</legend>
<center>
<table width="100%" cellspacing="5">
<tr>
  <td nowrap><input type="checkbox" id="green" value="A"><br>gr&uuml;n</td>
  <td><input type="checkbox" id="yellow"><br>gelb</td>
  <td><input type="checkbox" id="red"><br>rot</td>
  <td><input type="checkbox" id="imlager"><br>im Lager</td>
  <td><input type="checkbox" id="nichtimlager"><br>nicht im Lager</td>
  <td><input type="checkbox" id="reserviert"><br>reserviert</td>
  <td><input type="checkbox" id="nichtreservier"><br>nicht reserviert</td>
</tr></table>
</center>
</fieldset>
</td></tr></table>

<form action="#tabs-1" method="post">
[MESSAGE]
[TAB1]
[TAB1NEXT]
[MANUELLCHECKBOX] Alle markieren<br><br><br><br>
<input type="submit" value="Ampel auf gr&uuml;n stellen" name="jetztgruen">
<input type="submit" value="Ampel auf gelb stellen" name="jetztgelb">
<input type="submit" value="Ampel auf rot stellen" name="jetztrot">
</form>

</div>

<div id="tabs-2">
<form action="#tabs-2" method="post">
[TAB2]
[AUTOCHECKBOX] Alle markieren<br><br><br><br>
<input type="submit" value="Speichern" name="aktivieren">
</form>
</div>

<div id="tabs-3">
<form action="#tabs-3" method="post">
[TAB3]
<input type="submit" value="NEU Markierung entfernen" name="neuweg">
</form>
</div>



<!-- tab view schließen -->
</div>

