

<form action="" method="post" name="eprooform">
<table width="100%"><tr valign="top" style="background-color:#ececec"><td>

[FORMHANDLEREVENT]
[MESSAGE]

<fieldset><legend>Ansprechpartner</legend>
<table>
 <tr><td>{|Typ|}:</td><td>[TYP][MSGTYP]</td></tr>
  <tr><td>{|Name|}:*</td><td>[NAME][MSGNAME]</td></tr>
  <tr><td colspan="2"><br></td></tr>
  <tr><td>{|Titel|}:</td><td>[TITEL][MSGTITEL]</td></tr>
  <tr><td>{|Zust&auml;ndig bzw. Position|}:</td><td>[BEREICH][MSGBEREICH]</td></tr>
  <tr><td>{|Abteilung|}:</td><td>[ABTEILUNG][MSGABTEILUNG]</td></tr>
  <tr><td>{|Unterabteilung|}:</td><td>[UNTERABTEILUNG][MSGUNTERABTEILUNG]</td></tr>
  <tr><td>{|Adresszusatz|}:</td><td>[ADRESSZUSATZ][MSGADRESSZUSATZ]</td></tr>
  <tr><td colspan="2"><br></td></tr>
  <tr><td>{|Stra&szlig;e|}:</td><td>[STRASSE][MSGSTRASSE]</td></tr>
  <tr><td>{|PLZ/Ort|}:</td><td nowrap>[PLZ][MSGPLZ]&nbsp;[ORT][MSGORT]</td></tr>
  <tr><td>{|Land|}:</td><td>[EPROO_SELECT_LAND_ANSPRECHPARTNER]</td></tr>
  <tr><td colspan="2"><br></td></tr>
  <tr><td>{|Vorname (f&uuml;r Altdaten)|}:</td><td>[VORNAME][MSGVORNAME]</td></tr>
  <tr><td colspan="2"><br></td></tr>
  <tr><td>{|Geburtstag|}:</td><td>[GEBURTSTAG][MSGGEBURTSTAG]&nbsp;[GEBURTSTAGKALENDER][MSGGEBURTSTAGKALENDER]&nbsp;im Kalender anzeigen</td></tr>
  <tr><td>{|Geburtstagskarte|}:</td><td>[GEBURTSTAGSKARTE][MSGGEBURTSTAGSKARTE]</td></tr>
  <tr><td>{|Marketingsperre|}:</td><td>[MARKETINGSPERRE][MSGMARKETINGSPERRE]</td></tr>
</table>
</fieldset>

</td><td>

<fieldset><legend>Kontaktdaten Ansprechpartner</legend>
<table>
  <tr><td>Telefon:</td><td>[TELEFON][MSGTELEFON]</td></tr>
  <tr><td>Telefax:</td><td>[TELEFAX][MSGTELEFAX]</td></tr>
  <tr><td>Mobil:</td><td>[MOBIL][MSGMOBIL]</td></tr>
  <tr><td colspan="2"><br></td></tr>
  <tr><td>Anschreiben:</td><td>[ANSCHREIBEN][MSGANSCHREIBEN]</td></tr>
  <tr><td colspan="2"><br></td></tr>
  <tr><td>E-Mail:</td><td>[EMAIL][MSGEMAIL]</td></tr>
  <tr><td colspan="2"><br></td></tr>
  <tr><td colspan="2">Sonstiges:<br><br>[SONSTIGES][MSGSONSTIGES]</td></tr>
</table>

<center>[BUTTON]
    <input type="submit"
    value="Ansprechpartner speichern" class="btnGreen" /> 
</center>

</fieldset>

</td>

[GRUPPEAUSBLENDENSTART]
<td>
<fieldset>
<legend>{|Gruppen|}</legend>
[GRUPPEN]
<script>
function grchange(grid, el)
{
  $.ajax({
    url: "index.php?module=adresse&action=ansprechpartner&cmd=changegr&id=[ID]&lid=[LID]",
    type: 'POST',
        type: 'POST',
        dataType: 'json',
        data: { gruppe: grid, wert : $(el).prop('checked')?1:0},}
    ).done( function(data) {
      
    }).fail( function( jqXHR, textStatus ) {
      
    
   });
}
</script>
</fieldset>
</td>
[GRUPPEAUSBLENDENENDE]
</tr></table>

</form>
