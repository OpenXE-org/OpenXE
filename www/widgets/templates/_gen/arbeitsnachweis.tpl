[SAVEPAGEREALLY]

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-2">Arbeitsnachweis</a></li>
        <li><a href="#tabs-4" onclick="callCursorArbeitsnachweis();">Positionen</a></li>
       <li><a href="index.php?module=arbeitsnachweis&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>
      [FURTHERTABS]

    </ul>


<div id="tabs-2">
[MESSAGE]
<form action="" method="post" name="eprooform" id="eprooform">
[FORMHANDLEREVENT]

<center>
<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center">
<tr><td>


<!-- // ende anfang -->
<table width="100%" align="center" style="background-color:#cfcfd1;">
<tr>
<td width="33%"></td>
<td align="center"><b style="font-size: 14pt">Arbeitsnachweis <font color="blue">[NUMMER]</font></b>[KUNDE]</td>
<td width="33%" align="right" nowrap>[ICONMENU]&nbsp;[SAVEBUTTON]</td>
</tr>
</table>


<table width="100%"><tr valign="top"><td width="50%">

<fieldset><legend>{|Kunde|}</legend>
<table class="mkTableFormular">

  <tr><td>Kunden-Nr.:</td><td nowrap>[ADRESSE][MSGADRESSE][KUNDEAUTOEND]&nbsp;
[BUTTON_UEBERNEHMEN]
</td></tr>
</table>
</fieldset>


<fieldset><legend>{|Allgemein|}</legend>
<table class="mkTableFormular">
  <tr><td>{|Projekt|}:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
  <tr><td>{|Status|}:</td><td>[STATUS]</td></tr>
  <tr><td>{|Auftrag|}:</td><td>[AUFTRAGID][MSGAUFTRAGID]</td></tr>
  <tr><td>{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
  <tr><td>{|Suffix|}:</td><td>[PREFIX][MSGPREFIX]</td></tr>
  <tr><td>{|Schreibschutz|}:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>


</table>
</fieldset>



</td><td>



</td></tr></table>

<!--
<table width="100%"><tr><td>
<fieldset><legend>{|Positionen|}</legend>
[POSITIONEN]
</fieldset>
</td></tr></table>
-->
<table width="100%"><tr><td>
<fieldset><legend>{|Stammdaten|}</legend>
<table class="mkTableFormular">
          <tr><td width="150">{|Anrede|}:</td><td width="200">[TYP][MSGTYP]</td>
          <td width="20">&nbsp;</td>
            <td width="120"></td><td></td></tr>
          <tr><td>{|Name|}:</td><td>[NAME][MSGNAME]</td>
          <td>&nbsp;</td>
            <td>{|Telefon|}:</td><td>[TELEFON][MSGTELEFON]</td></tr>
          <tr><td>{|Ansprechpartner|}:</td><td>[ANSPRECHPARTNER][MSGANSPRECHPARTNER]</td>
          <td>&nbsp;</td>
            <td>{|Telefax|}:</td><td>[TELEFAX][MSGTELEFAX]</td></tr>
          <tr><td>{|Abteilung|}:</td><td>[ABTEILUNG][MSGABTEILUNG]</td><td>&nbsp;</td>
          <td>{|E-Mail|}:</td><td>[EMAIL][MSGEMAIL]</td></tr>
          <tr><td>{|Unterabteilung|}:</td><td>[UNTERABTEILUNG][MSGUNTERABTEILUNG]</td><td>&nbsp;</td>
           <td>Anschreiben</td><td>[ANSCHREIBEN][MSGANSCHREIBEN]</td></tr>
          <tr><td>{|Adresszusatz|}:</td><td>[ADRESSZUSATZ][MSGADRESSZUSATZ]</td><td>&nbsp;</td>
           <td></td><td></td></tr>
          <tr><td>Stra&szlig;e</td><td>[STRASSE][MSGSTRASSE]</td><td>&nbsp;</td>
            <td></td><td>[LIEFERADRESSEPOPUP][ANSPRECHPARTNERPOPUP]
            </td></tr>
          <tr><td>PLZ/Ort</td><td>[PLZ][MSGPLZ]&nbsp;[ORT][MSGORT]</td><td>&nbsp;</td>
            <td></td><td></td></tr>

          <tr><td>{|Land|}:</td><td colspan="3">[EPROO_SELECT_LAND]</td>
          </tr>
</table>
</fieldset>
</td></tr></table>

<table width="100%"><tr><td>
<fieldset><legend>{|Freitext|}</legend>
[FREITEXT][MSGFREITEXT]
</fieldset>
</td></tr></table>


<table width="100%"><tr valign="top"><td width="50%">


<fieldset><legend>{|Arbeitsnachweis|}</legend>
<table class="mkTableFormular">
<tr><td>{|Bearbeiter|}:</td><td>[BEARBEITER][MSGBEARBEITER]</td></tr>
<tr><td>{|Kein Briefpapier und Logo|}:</td><td>[OHNE_BRIEFPAPIER][MSGOHNE_BRIEFPAPIER]</td></tr>
<tr><td>{|Anzeige Verrechnungsart|}:</td><td>[ANZEIGE_VERRECHNUNGSART][MSGANZEIGE_VERRECHNUNGSART]</td></tr>

</table>
</fieldset>


</td><td>




</td></tr></table>
<table width="100%"><tr><td>
<fieldset><legend>{|Interne Bemerkung|}</legend>
[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]
</fieldset>
</td></tr></table>

</center>


</td>
</table>

<br><br>
<table width="100%">
<tr><td align="right">
    <input type="submit" name="speichern"
    value="Speichern" />
</td></tr></table>
</div>


</form>

<div id="tabs-4">
<div class="overflow-scroll">
<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center">
<tr><td>


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr>
<td width="33%"></td>
<td align="center"><b style="font-size: 14pt">Arbeitsnachweis <font color="blue">[NUMMER]</font></b>[KUNDE]</td>
<td width="33%" align="right">[ICONMENU2]</td>
</tr>
</table>


[POS]

</td></tr></table>




</div>
</div>

<div id="tabs-3"></div>
      [FURTHERTABSDIV]



 <!-- tab view schließen -->
</div>

