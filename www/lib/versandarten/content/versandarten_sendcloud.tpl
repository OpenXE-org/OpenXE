<br><br><table id="paketmarketab" align="center">
<tr>
<td align="center">
<br>
<form action="" method="post">
[ERROR]
<h1>{|Paketmarken Drucker f&uuml;r|} [ZUSATZ]</h1>
<br>
<b>{|Empf&auml;nger|}</b>
<br>
<br>
<table>
<tr><td>


<table style="float:left;">
<tr><td>{|Name|}:</td><td><input type="text" size="36" value="[NAME]" name="name" id="name"><script type="text/javascript">document.getElementById("name").focus(); </script></td></tr>
<tr><td>{|Name 2|}:</td><td><input type="text" size="36" value="[NAME2]" name="name2"></td></tr>
	<tr><td>{|Name 3|}:</td><td><input type="text" size="36" value="[NAME3]" name="name3"></td></tr>

<tr><td>{|Land|}:</td><td>[EPROO_SELECT_LAND]</td></tr>
<tr><td>{|PLZ/Ort|}:</td><td><input type="text" name="plz" size="5" value="[PLZ]">&nbsp;<input type="text" size="30" name="ort" value="[ORT]"></td></tr>
<tr><td>{|Strasse/Hausnummer|}:</td><td><input type="text" size="30" value="[STRASSE]" name="strasse">&nbsp;<input type="text" size="5" name="hausnummer" value="[HAUSNUMMER]"></td></tr>

<tr><td>{|E-Mail|}:</td><td><input type="text" size="36" value="[EMAIL]" name="email"></td></tr>
<tr><td>{|Telefon|}:</td><td><input type="text" size="36" value="[TELEFON]" name="telefon"></td></tr>

[PRODUCT_LIST]


<tr style="[VERSTECKEN]"><td colspan="2">&nbsp;</td></tr>
<tr id="us-states" style="[VERSTECKEN]"><td>{|Bundesland|}:</td><td>[EPROO_SELECT_BUNDESSTAAT]</td></tr>
<tr id="states" style="[VERSTECKEN]"><td>{|Bundesland|}:</td><td><input type="text" size="36" value="[BUNDESLAND]" name="state"></td></tr>
<tr style="[VERSTECKEN]"><td>{|Rechnungsnummer|}:</td><td><input type="text" size="36" value="[RECHNUNGSNUMMER]" name="rechnungsnummer"></td></tr>
<tr style="[VERSTECKEN]"><td>{|Sendungsart|}:</td><td><select name="sendungsart">
	<option value="0">{|Geschenk|}</option>
	<option value="1">{|Dokumente|}</option>
	<option value="2" selected>{|Kommerzielle Waren|}</option>
	<option value="3">{|Erprobungswaren|}</option>
	<option value="4">{|Rücksendung|}</option>
	</select></td></tr>
    <tr><td nowrap>{|Extra Versicherung|}:</td><td><input type="checkbox" name="versichert" value="1" [VERSICHERT] /></td></tr>
  <tr class="versicherung"><td>{|Versicherungssumme|}:</td><td><input type="text" size="10" id="versicherungssumme" name="versicherungssumme" value="[VERSICHERUNGSSUMME]" /></td></tr>
</table>

<table style="float:right;">
[GEWICHT]
<tr>
<td>{|H&ouml;he (in cm)|}:</td>
<td>
<input type="text" size="10" value="[HEIGHT]" name="height">
</td>
</tr>
<tr>
<td>{|Breite (in cm)|}:</td>
<td>
<input type="text" size="10" value="[WIDTH]" name="width">
</td>
</tr>
<tr>
<td>{|L&auml;nge (in cm)|}:</td>
<td>
<input type="text" size="10" value="[LENGTH]" name="length">
</td>
</tr>
</table>

<div style="clear:both"></div>

<br><br>
<center><input class="btnGreen" type="submit" value="{|Paketmarke drucken|}" name="drucken">&nbsp;
[TRACKINGMANUELL]
&nbsp;<input type="button" value="{|Andere Versandart auswählen|}" onclick="window.location.href='index.php?module=versanderzeugen&action=wechsel&id=[ID]'" name="anders">&nbsp;
<!--<input type="button" value="Abbrechen">--></center>
</td></tr></table>
  </form>

<br><br>
</td></tr></table>
