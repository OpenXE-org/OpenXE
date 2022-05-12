<br><br><table  style="min-width:60%;max-width:700px;background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br>
<form action="" method="post">
<h1>Paketmarken Drucker f&uuml;r DHL <font color=red>[ZUSATZ]</font></h1>
<br>
<b>Empf&auml;nger</b>
<br>
<br>
<table>
<tr><td>


<table style="float:left;">
<tr><td>Name:</td><td><input type="text" size="36" value="[NAME]" name="name" id="name"><script type="text/javascript">document.getElementById("name").focus(); </script></td></tr>
<tr><td>Name 2:</td><td><input type="text" size="36" value="[NAME2]" name="name2"></td></tr>
<tr><td>Name 3:</td><td><input type="text" size="36" value="[NAME3]" name="name3"></td></tr>
<tr><td>Land:</td><td>[EPROO_SELECT_LAND]</td></tr>
<tr><td>PLZ/ort:</td><td><input type="text" name="plz" size="5" value="[PLZ]">&nbsp;<input type="text" size="30" name="ort" value="[ORT]"></td></tr>
<tr><td>Strasse/Hausnummer:</td><td><input type="text" size="30" value="[STRASSE]" name="strasse">&nbsp;<input type="text" size="5" name="hausnummer" value="[HAUSNUMMER]"></td></tr>
</table>
<table style="float:left;">
<!--<tr><td width="180">Anzahl Pakete:</td><td nowrap><input type="text" name="anzahl" size="5" value="[ANZAHL]" id="anzahl">&nbsp;<input type="button" onclick=window.location.href="index.php?module=versanderzeugen&action=frankieren&id=[ID]&land=[LAND]&anzahl="+document.getElementById('anzahl').value value="erstellen"></td></tr>-->
[GEWICHT]
<!--<tr><td>Foto:</td><td><img src="http://t3.gstatic.com/images?q=tbn:QTV_X4YJEI2p7M:http://notebook.pege.org/2005-inode/paket.jpg"></td></tr>
<tr><td></td><td><input type="button" value="Nochmal Wiegen+Foto"></td></tr>-->
</table>
<!--
<table>
<tr><td>Gewicht:</td><td><input type="text" size="5"></td></tr>
<tr><td>Foto:</td><td><img src="http://t3.gstatic.com/images?q=tbn:QTV_X4YJEI2p7M:http://notebook.pege.org/2005-inode/paket.jpg"></td></tr>
<tr><td></td><td><input type="button" value="Nochmal Wiegen+Foto"></td></tr>
</table>
-->
</tr>
</table>
<br><br>

<table align="center">
  <tr><td colspan="2"><b>Service</b></td></tr>
  <tr><td>Nachnahme:</td><td><input type="checkbox" name="nachnahme" value="1" [NACHNAHME]> (Betrag: [BETRAG] EUR)<input type="hidden" name="betrag" value="[BETRAG]"></td></tr>
  <tr><td>Versichert 2500 EUR:</td><td><input type="checkbox" name="versichert" value="1" [VERSICHERT]></td></tr>
  <tr><td>Versichert 25000 EUR:</td><td><input type="checkbox" name="extraversichert" value="1" [EXTRAVERSICHERT]></td></tr>
</table>
<br><br>
<center><input style="background-color: #FF8080" type="submit" value="Paketmarke drucken" name="drucken">&nbsp;

&nbsp;<input type="button" value="{|Andere Versandart auswählen|}" onclick="window.location.href='index.php?module=versanderzeugen&action=wechsel&id=[ID]'" name="anders">&nbsp;
<!--<input type="button" value="Abbrechen">--></center>
</form>
</td></tr></table>
<br><br>
