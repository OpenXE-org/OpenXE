
<script type="text/javascript">

$(document).ready(function(){

  art = document.getElementById('art');
  rabatt = document.getElementById('rabatte');
  rabatt2 = document.getElementById('rabatte2');
  if (art) {
      // Hide the target field if priority isn't critical
      if (art.options[art.selectedIndex].value =='gruppe') {
				rabatt.style.display='none';
				rabatt2.style.display='none';
      }
      else if (art.options[art.selectedIndex].value =='preisgruppe') {
				rabatt2.style.display='none';
			}
  else if (art.options[art.selectedIndex].value =='verband') {
			}
else {
				rabatt.style.display='none';
				rabatt2.style.display='none';
}



      art.onchange=function() {
          if (art.options[art.selectedIndex].value == 'gruppe') {             
						rabatt.style.display='none';
						rabatt2.style.display='none';
          } else if(art.options[art.selectedIndex].value == 'preisgruppe') {
						rabatt.style.display='';
						rabatt2.style.display='none';
          } else if(art.options[art.selectedIndex].value == 'verband') {
						rabatt.style.display='';
						rabatt2.style.display='';
          } 
          else {
						rabatt.style.display='none';
						rabatt2.style.display='none';
					}
      }
  }
});
 </script>
<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%">
        <tr><td width="200">{|Aktiv|}:</td><td>[AKTIV][MSGAKTIV]&nbsp;<i>Bitte aktivieren! (Ist die Gruppe nicht aktiv wird diese für die Neuanlage von Verknüpfungen ausgeblendet. Die Gruppe besteht in den aktuellen Verknüpfungen weiterhin.)</i></td></tr>
   	<tr><td width="200">{|Bezeichnung|}:</td><td>[NAME][MSGNAME]</td></tr>
        <tr><td width="200">{|Kennziffer|}:</td><td>[KENNZIFFER][MSGKENNZIFFER]&nbsp;<i>z.B. 01, 02, ...</i></td></tr>
        <tr><td width="200">{|Interne Bemerkung|}:</td><td>[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]</td><td></tr>
       	<tr><td width="200">{|Art|}:</td><td>[ART][MSGART]</td></tr>
        <tr><td width="200">{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]&nbsp;<i>optionale Angabe</></td></tr>
        <tr><td width="200">{|Kategorie|}:</td><td>[KATEGORIE][MSGKATEGORIE]&nbsp;<i>optionale Angabe</></td></tr>
    </table>
</fieldset>


<div id="rabatte">
<fieldset><legend>{|Rabatte / Zahlungen|}</legend>
    <table width="100%">
          <tr><td width="200">{|Grundrabatt|}:</td><td>[GRUNDRABATT][MSGGRUNDRABATT]&nbsp;%&nbsp;<i>z.B. 20 fuer 20% (der Rabatt gilt nur f&uuml;r Standardpreise, nicht f&uuml;r Gruppen- oder Kundenspezifische Preise.)</i></td></tr>
          <tr><td width="200">{|Zahlungszieltage|}:</td><td>[ZAHLUNGSZIELTAGE][MSGZAHLUNGSZIELTAGE]&nbsp;Tage&nbsp;<i>z.B. 30</i></td></tr>
          <tr><td width="200">{|Skonto|}:</td><td>[ZAHLUNGSZIELSKONTO][MSGZAHLUNGSZIELSKONTO]&nbsp;%&nbsp;<i>z.B. 2</i></td></tr>
          <tr><td width="200">{|Skonto Tage|}:</td><td>[ZAHLUNGSZIELTAGESKONTO][MSGZAHLUNGSZIELTAGESKONTO]&nbsp;Tage&nbsp;<i>z.B. 10</i></td></tr>
<tr><td>{|Porto frei aktiv|}:</td><td>[PORTOFREI_AKTIV][MSGPORTOFREI_AKTIV]&nbsp;ab&nbsp;[PORTOFREIAB][MSGPORTOFREIAB]&nbsp;&euro;&nbsp;<i>Porto frei ab bestimmtem Umsatz (netto)</i></td></tr>
</table>
</fieldset>
</div>
<div id="rabatte2">
<fieldset><legend>{|Verbandsoptionen|}</legend>
    <table width="100%">
          <tr><td width="200">Rabatte*:</td><td>

<table>
<tr><td>{|Rabatt 1|}:</td><td>[RABATT1][MSGRABATT1] %</td><td width="100">&nbsp;</td>
		<td>{|Bonus 1|}:</td><td>[BONUS1][MSGBONUS1] % ab [BONUS1_AB][MSGBONUS1_AB] &euro;</td><td width="50">&nbsp;</td>
		<td>{|Bonus 6|}:</td><td>[BONUS6][MSGBONUS6] % ab [BONUS6_AB][MSGBONUS6_AB] &euro;</td>
</tr>
<tr><td>{|Rabatt 2|}:</td><td>[RABATT2][MSGRABATT2] %</td><td width="100">&nbsp;</td>
		<td>{|Bonus 2|}:</td><td>[BONUS2][MSGBONUS2] % ab [BONUS2_AB][MSGBONUS2_AB] &euro;</td><td width="50">&nbsp;</td>
		<td>{|Bonus 7|}:</td><td>[BONUS7][MSGBONUS7] % ab [BONUS7_AB][MSGBONUS7_AB] &euro;</td>
</tr>
<tr><td>{|Rabatt 3|}:</td><td>[RABATT3][MSGRABATT3] %</td><td width="100">&nbsp;</td>
		<td>{|Bonus 3|}:</td><td>[BONUS3][MSGBONUS3] % ab [BONUS3_AB][MSGBONUS3_AB] &euro;</td><td width="50">&nbsp;</td>
		<td>{|Bonus 8|}:</td><td>[BONUS8][MSGBONUS8] % ab [BONUS8_AB][MSGBONUS8_AB] &euro;</td>
</tr>
<tr><td>{|Rabatt 4|}:</td><td>[RABATT4][MSGRABATT4] %</td><td width="100">&nbsp;</td>
		<td>{|Bonus 4|}:</td><td>[BONUS4][MSGBONUS4] % ab [BONUS4_AB][MSGBONUS4_AB] &euro;</td><td width="50">&nbsp;</td>
		<td>{|Bonus 9|}:</td><td>[BONUS9][MSGBONUS9] % ab [BONUS9_AB][MSGBONUS9_AB] &euro;</td>
</tr>
<tr><td>{|Rabatt 5|}:</td><td>[RABATT5][MSGRABATT5] %</td><td width="100">&nbsp;</td>
		<td>{|Bonus 5|}:</td><td>[BONUS5][MSGBONUS5] % ab [BONUS5_AB][MSGBONUS5_AB] &euro;</td><td width="50">&nbsp;</td>
		<td>{|Bonus 10|}:</td><td>[BONUS10][MSGBONUS10] % ab [BONUS10_AB][MSGBONUS10_AB] &euro;</td>
</tr>
<tr><td>{|Provision|}:</td><td>[PROVISION][MSGPROVISION] %</td><td width="100">&nbsp;</td>
    <td></td><td></td><td width="50">&nbsp;</td>
    <td></td><td></td>
</tr>
<tr><td>{|Sonderrabatt|}:</td><td colspan="2">[SONDERRABATT_SKONTO][MSGSONDERRABATT_SKONTO] % (bei Skonto)</td>
    <td></td><td></td><td width="50">&nbsp;</td>
    <td></td><td></td>
</tr>
<tr><td colspan="8">* der Rabatt gilt nur f&uuml;r Standardpreise, nicht f&uuml;r Gruppen- oder Kundenspezifische Preise</td>
</tr>
</table>
</td><td></tr>
</table></fieldset>

<fieldset><legend>{|Buchhaltung Einstellungen|}</legend>
    <table width="100%">
   				<tr><td width="200">{|Zentralregulierung|}:</td><td>[ZENTRALREGULIERUNG][MSGZENTRALREGULIERUNG]</td></tr>
   				<!--<tr><td width="200">{|Zentrale Rechnungsadresse|}:</td><td>[ZENTRALERECHNUNG][MSGZENTRALERECHNUNG]</td></tr>-->
					<tr><td>{|Periode der Rechnung|}:</td><td>[RECHNUNG_PERIODE][MSGRECHNUNG_PERIODE]</td></tr>
          <tr><td width="200">{|Anzahl Papierrechnungen|}:</td><td>[RECHNUNG_ANZAHLPAPIER][MSGRECHNUNG_ANZAHLPAPIER]</td></tr>
          <tr><td width="200">{|Rechnung per Mail|}:</td><td>[RECHNUNG_PERMAIL][MSGRECHNUNG_PERMAIL]</td></tr>
          <tr><td width="200">{|Name / Firma|}:</td><td>[RECHNUNG_NAME][MSGRECHNUNG_NAME]</td></tr>
          <tr><td width="200">{|Abteilung|}:</td><td>[RECHNUNG_ABTEILUNG][MSGRECHNUNG_ABTEILUNG]</td></tr>
          <tr><td width="200">Strasse + Hausnummer:</td><td>[RECHNUNG_STRASSE][MSGRECHNUNG_STRASSE]</td></tr>
          <tr><td width="200">{|PLZ / Ort|}:</td><td>[RECHNUNG_PLZ][MSGRECHNUNG_PLZ]&nbsp;[RECHNUNG_ORT][MSGRECHNUNG_ORT]</td></tr>
          <tr><td width="200">{|Land|}:</td><td></td></tr>
          <tr><td width="200">{|E-Mail|}:</td><td>[RECHNUNG_EMAIL][MSGRECHNUNG_EMAIL]</td></tr>
          <tr><td width="200">{|Kundennummer im Verband|}:</td><td>[KUNDENNUMMER][MSGKUNDENNUMMER]</td></tr>
</table>
</fieldset>


<fieldset><legend>DTA - Datentr&auml;ger Austausch Einstellungen </legend>
    <table width="100%">
   				<tr><td width="200">{|Aktiv|}:</td><td>[DTA_AKTIV][MSGDTA_AKTIV]</td></tr>
					<tr><td>{|Variante|}:</td><td>[DTA_VARIANTE][MSGDTA_VARIANTE]</td></tr>
          <tr><td width="200">{|DTA Variablen|}:</td><td>[DTAVARIABLEN][MSGDTAVARIABLEN]</td></tr>
	
					<tr><td>{|Periode|}:</td><td>[DTA_PERIODE][MSGDTA_PERIODE]</td></tr>
          <tr><td width="200">{|Partner ID f&uuml;r DTA|}:</td><td>[PARTNERID][MSGPARTNERID]</td></tr>
          <tr><td width="200">{|Dateiname|}:</td><td>[DTA_DATEINAME][MSGDTA_DATEINAME]</td></tr>
          <tr><td width="200">{|E-Mail Empf&auml;nger|}:</td><td>[DTA_MAIL][MSGDTA_MAIL]</td></tr>
          <tr><td width="200">{|E-Mail Betreff|}:</td><td>[DTA_MAIL_BETREFF][MSGDTA_MAIL_BETREFF]</td></tr>
          <tr><td width="200">{|E-Mail Textvorlage|}:</td><td>[DTA_MAIL_TEXT][MSGDTA_MAIL_TEXT]</td></tr>
</table>
</fieldset>


</div>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" />
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>


