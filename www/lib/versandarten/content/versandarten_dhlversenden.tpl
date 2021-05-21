<br><br><table id="paketmarketab" align="center">
<tr>
<td align="center">
<br>
<form action="" method="post">
[ERROR]
<h1>Paketmarken Drucker f&uuml;r [ZUSATZ]</h1>
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
<tr><td>E-Mail:</td><td><input type="text" size="36" value="[EMAIL]" name="email"></td></tr>
<tr><td>Telefon:</td><td><input type="text" size="36" value="[TELEFON]" name="telefon"></td></tr>
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
  <tr><td>Nachnahme:</td><td style="min-width:200px;"><input type="checkbox" name="nachnahme" value="1" [NACHNAHME]> (Betrag: [BETRAG] EUR)<input type="hidden" name="betrag" value="[BETRAG]"></td></tr>
  <!--<tr><td>Versichert 2500 EUR:</td><td><input type="checkbox" name="versichert" value="1" [VERSICHERT]></td></tr>
  <tr><td>Versichert 25000 EUR:</td><td><input type="checkbox" name="extraversichert" value="1" [EXTRAVERSICHERT]></td></tr>-->
  <tr><td nowrap>Extra Versicherung:</td><td><input type="checkbox" name="versichert" value="1" [VERSICHERT] /></td></tr>
  <tr class="versicherung"><td>Versicherungssumme:</td><td><input type="text" size="10" id="versicherungssumme" name="versicherungssumme" value="[VERSICHERUNGSSUMME]" /></td></tr>
  <tr><td>Leitcodierung:</td><td style="min-width:200px;"><input type="checkbox" name="leitcodierung" value="1" [LEITCODIERUNG]>&nbsp;<i>ohne Leitcodierung können extra Kosten entstehen</i></td></tr>
  <tr><td>Abholdatum:</td><td><input type="text" size="10" id="abholdatum" name="abholdatum" value="[ABHOLDATUM]" /></td></tr>
  <tr><td>Wunschtermin:</td><td><input type="checkbox" name="wunschtermin" value="1" [WUNSCHTERMIN] /></td></tr>
  <tr class="wunschzeitraum"><td>Wunschlieferdatum:</td><td><input type="text" size="10" id="wunschlieferdatum" name="wunschlieferdatum" value="[WUNSCHLIEFERDATUM]" /></td></tr>
  <tr class="wunschzeitraum"><td>Wunschlieferzeitraum:</td>
  <td><!--<input type="radio" name="wunschzeitraum" id="wunsch10001200" value="10001200" [WUNSCH10001200] /> 10:00 - 12:00
   <input type="radio" name="wunschzeitraum" id="wunsch12001400" value="12001400" [WUNSCH12001400] /> 12:00 - 14:00
   <input type="radio" name="wunschzeitraum" id="wunsch14001600" value="14001600" [WUNSCH14001600] /> 14:00 - 16:00
   <input type="radio" name="wunschzeitraum" id="wunsch16001800" value="16001800" [WUNSCH16001800] /> 16:00 - 18:00 -->
   <input type="radio" name="wunschzeitraum" id="wunsch18002000" value="18002000" [WUNSCH18002000] /> 18:00 - 20:00
   <input type="radio" name="wunschzeitraum" id="wunsch19002100" value="19002100" [WUNSCH19002100] /> 19:00 - 21:00
  </td></tr>
  [VORRETOURENLABEL]<tr><td nowrap>Retourenlabel drucken:</td><td><input type="checkbox" value="1" id="retourenlabel" name="retourenlabel" [RETOURENLABEL] /></td></tr>[NACHRETOURENLABEL]
  [VORALTERSFREIGABE]<tr><td nowrap>Altersfreigabe notwendig:</td><td><input type="checkbox" name="altersfreigabe" value="1" [ALTERSFREIGABE]></td></tr>[NACHALTERSFREIGABE]
</table>
<br><br>
<center><input class="btnGreen" type="submit" value="Paketmarke drucken" name="drucken">&nbsp;

[TRACKINGMANUELL]
&nbsp;<input type="button" value="{|Andere Versandart auswählen|}" onclick="window.location.href='index.php?module=versanderzeugen&action=wechsel&id=[ID]'" name="anders">&nbsp;
<!--<input type="button" value="Abbrechen">--></center>
</form>
</td></tr></table>
<br><br>

<script type="text/JavaScript" language="javascript">
$(document).ready(function() {
 $( "#abholdatum" ).datepicker({ dateFormat: 'dd.mm.yy',dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'], firstDay:1,
          showWeek: true, monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai',
          'Juni', 'Juli', 'August', 'September', 'Oktober',  'November', 'Dezember'], });
});
</script>
