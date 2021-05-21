<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<form action="" method="post">

<fieldset><legend>{|Artikel Beschreibung|}</legend>
<table>
<tr><td width="200">Name:</td><td><input type="text" name="name" size="50" id="name" value="[NAME]"></td></tr>
<tr><td width="200">Kategorie:</td><td><select name="kategorie">[KATEGORIE]</select></td></tr>
<tr><td width="200">Chargen aktiv:</td><td><input type="checkbox" name="chargen" value="1" [CHARGEN]></td></tr>
<tr><td width="200">MHD aktiv:</td><td><input type="checkbox" name="mhd" value="1" [MHD]></td></tr>
</table>
</fieldset>

<fieldset><legend>{|Einkauf|}</legend>
<table>
<tr><td width="200">Lieferant:</td><td><input type="text" name="lieferant" value="[LIEFERANT]" size="50" id="lieferant"></td></tr>
<tr><td width="200">VPE Menge:</td><td><input type="text" name="menge" size="50" id="menge" value="[MENGE]"></td></tr>
<tr><td width="200">Preis f&uuml;r VPE:</td><td><input type="text" name="preis" size="50" id="preis" value="[PREIS]"></td></tr>
</table>
</fieldset>
<!--
<fieldset><legend>{|Lager|}</legend>
<table>
<tr><td width="200">Standardlager:</td><td><input type="text" name="name" size="50" id="name"></td></tr>
<tr><td width="200">Menge:</td><td><input type="text" name="name" size="50" id="name"></td></tr>
</table>
</fieldset>
-->
<br>
<center><input type="button" value="Zur&uuml;ck" onclick="window.location.href='index.php?module=artikel&action=schnellanlegen';">&nbsp;<input type="submit" name="submit_anlegen" value="Artikel anlegen"></center>



</form>
</div>

<!-- tab view schließen -->
</div>

<script type="text/javascript">document.getElementById("name").focus(); </script>
