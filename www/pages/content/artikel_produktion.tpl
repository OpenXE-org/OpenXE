<table width="100%"><tr valign="top">
<td>

<form action="" method="post">
<fieldset style="height:200px"><legend>Produktionen anlegen:</legend>
<table width="100%">
<tr><td width="200">Menge</td><td><input type="text"  size="5" name="menge">&nbsp;</td></tr>
<tr><td>Kunde</td><td>[KUNDESTART]<input type="text" size="50" id="kunde" name="kunde">[KUNDEEND]&nbsp;</td></tr>

<tr><td></td><td align="right"><br><br><input type="submit" name="produktion_anlegen_einzeln" value="Produktionen anlegen"></td></tr>
</table>
</fieldset>
</form>

<!--
<form action="" method="post">
<fieldset style="height:200px"><legend>Alle St&uuml;cklisten f&uuml;r Produktion anlegen:</legend>
<table width="100%">
<tr><td width="200">St&uuml;ckliste 1</td><td><input type="text"  size="5" name="menge_smt">&nbsp;<i>(Menge)</i></td></tr>
<tr><td>St&uuml;ckliste 2</td><td><input type="text" size="5" name="menge_filling">&nbsp;<i>(Menge)</i></td></tr>
<tr><td>St&uuml;ckliste 3</td><td><input type="text" size="5" name="menge_tht">&nbsp;<i>(Menge)</i></td></tr>
<tr><td>Kunde</td><td>[KUNDESTART]<input type="text" id="kunde" name="kunde">[KUNDEEND]&nbsp;<i>(Kunde f&uuml;r Produktion)</i></td></tr>

<tr><td></td><td align="right"><br><br><input type="submit" name="produktion_anlegen" value="Produktionen anlegen"></td></tr>
</table>
</fieldset>
</form>
-->

</td><td width="50%">

<form action="" method="post">
<fieldset style="height:200px"><legend>Weitere St&uuml;cklisten vorhanden:</legend>
<table>
<tr><td>St&uuml;ckliste 1</td><td>[SMTSTART]<input type="text" size="50" value="[SMT]" name="smt" id="smt">[SMTEND]&nbsp;</td></tr>
<tr><td>St&uuml;ckliste 2</td><td>[FILLINGSTART]<input type="text" size="50" value="[FILLING]" name="filling" id="filling">[FILLINGEND]</td></tr>
<tr><td>St&uuml;ckliste 3</td><td>[THTSTART]<input type="text" value="[THT]" size="50" id="tht" name="tht">[THTEND]&nbsp;</td></tr>

<tr><td></td><td align="right"><br><br><input type="submit" name="speichern" value="Speichern"></td></tr>
</table>
</form>
</fieldset>

</td></tr></table>
