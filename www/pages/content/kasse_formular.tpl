<script type="text/javascript">
$( document ).ready(function() {

$( "#betrag" ).keyup(function( event ) {
  var str = $("#steuergruppe option:selected").text();
  var steuer = parseFloat(str.replace("%", "")); 
  steuer = (steuer + 100)/100; 

  var str2 = $("#betrag").val();
  betrag = parseFloat(str2.replace(",", ".")); 
  $("#nettobetrag").text((betrag/steuer).toFixed(2));
});

$( "#steuergruppe" ).change(function( event ) {
  var str = $("#steuergruppe option:selected").text();
  var steuer = parseFloat(str.replace("%", "")); 
  steuer = (steuer + 100)/100; 

  var str2 = $("#betrag").val();
  betrag = parseFloat(str2.replace(",", ".")); 
  $("#nettobetrag").text((betrag/steuer).toFixed(2));
});



});
</script>
<form action="" method="post" name="eprooform">

<fieldset>
  <legend>{|Kasse|}</legend>
  <table width="100%">
    <tr>
      <td width="300">{|Datum|}*:</td>
      <td><input type="text" name="datum" size="10" value="[DATUM]" id="datum" [READONLYDATUM]></td>
    </tr>
	  <tr>
      <td>{|Bruttobetrag|}*:</td>
      <td><input type="text" name="betrag" id="betrag" value="[BETRAG]" size="15" [READONLY]>&nbsp;
        <select name="auswahl" [DISABLED]>
          <option value="ausgabe" [AUSGABE]>Ausgabe</option>
          <option value="einnahme" [EINNAHME]>Einnahme</option>
        </select>&nbsp;
        <select name="steuergruppe" id="steuergruppe" [DISABLED]>
          <option value="0" [STANDARD]>[STANDARDSTEUERSATZ]%</option>
          <option value="1" [ERMAESSIGT]>[ERMAESSIGTSTEUERSATZ]%</option> 
          <option value="2" [OHNEUST]>[OHNESTEUERSATZ]%</option>[SELSTEUER]
        </select>
      </td>
      <td>
    </tr>
    <tr>
      <td></td>
      <td>{|entspricht|}:&nbsp;<span id="nettobetrag">[NETTOBETRAG]</span> ({|netto|})</td>
    </tr>
    <tr>
      <td>{|Belegfeld|}*:</td>
      <td><input type="text" name="grund" size="46" value="[GRUND]" [READONLY]></td>
    </tr>
	  <tr>
      <td width="300">{|Adresse (optional)|}:</td>
      <td><input type="text" name="adresse" id="adresse" size="46" value="[ADRESSE]" [READONLY]></td>
      <td>
    </tr>
  </table>
</fieldset>

<fieldset>
  <legend>{|Erweitert|}</legend>
  <table width="100%">
    <tr>
      <td width="300">{|Konto|}:</td>
      <td><input type="text" name="sachkonto" value="[SACHKONTO]" size="18" id="sachkonto" [READONLY]></td>
      <td>
    </tr>

    <tr>
      <td>{|Projekt|}:</td>
      <td><input type="text" name="projekt" id="projekt" size="46" value="[PROJEKT]" [READONLY]></td>
    </tr>
  </table>
</fieldset>

<fieldset>
  <legend>{|Sonstiges|}</legend>
  <table width="100%">
    <tr>
      <td width="300">{|Bemerkung zur Buchung|}:</td>
      <td><input type="text" name="storniert_grund" id="storniert_grund" size="46" value="[STORNIERT_GRUND]">&nbsp;<br><i>({|Dieses Feld wird in den &Uuml;bersichten mit angezeigt.|})</i></td>
    </tr>
    <tr>
      <td width="300">{|Interne Bemerkung|}:</td>
      <td><textarea name="bemerkung" id="bemerkung" rows="5" cols="46">[BEMERKUNG]</textarea></td>
    </tr>
  </table>
</fieldset>


<table width="100%">
  <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td>
      <table width="100%">
        <tr>
          <td><input type="button" onclick="window.location.href='index.php?module=kasse&action=edit&id=[ID]'" value="Abbrechen"></td>
          <td align="right"><input type="submit" name="anlegen" value="Speichern" /></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</form>
