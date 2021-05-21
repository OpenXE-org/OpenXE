<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<form action="" method="post">
<fieldset><legend>{|Verein|}</legend>
<table>
<tr><td width="200">Mitglied seit:</td><td><input type="text" name="verein_mitglied_seit" id="verein_mitglied_seit" size="10" value="[VEREIN_MITGLIED_SEIT]"></td></tr>
<tr><td width="200">Mitglied bis:</td><td><input type="text" name="verein_mitglied_bis" id="verein_mitglied_bis" size="10" value="[VEREIN_MITGLIED_BIS]"></td></tr>
<tr><td width="200">Mitglied aktiv:</td><td><input type="checkbox" name="verein_mitglied_aktiv" value="1" [VEREIN_MITGLIED_AKTIV]></td></tr>
<tr><td width="200">Spendenbescheinigung:</td><td><input type="checkbox" name="verein_spendenbescheinigung" value="1" [VEREIN_SPENDENBESCHEINIGUNG]></td></tr>
</table>
</fieldset>
<center><input type="submit" name="submit" value="Speichern"></center>
</form>
</div>

<!-- tab view schließen -->
</div>

