<script type="text/javascript">
[JSPLACETEL]

$(document).ready(function(){
    var vorname = $('#vorname').val();
    var typ = $('select[name=typ]').val();

    document.getElementById('abweichenderechnungsadressestyle').style.display="none";
    if(document.getElementById('abweichende_rechnungsadresse').checked)
      document.getElementById('abweichenderechnungsadressestyle').style.display="";

  });


  function abweichend(cmd)
        {
          document.getElementById('abweichenderechnungsadressestyle').style.display="none";
    if(document.getElementById('abweichende_rechnungsadresse').checked)
      document.getElementById('abweichenderechnungsadressestyle').style.display="";
        }
      //-->
     </script>

[SAVEPAGEREALLY]
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Adressdaten|}</a></li>
        <li><a href="#tabs-2">{|Zahlungskonditionen / Besteuerung|}</a></li>
        <li><a href="#tabs-3">{|Bankverbindung|}</a></li>
        <li><a href="#tabs-4">{|Dokumente Versandoptionen|}</a></li>
        <li><a href="#tabs-5">{|Sonstige Daten|}</a></li>
    </ul>

<div id="tabs-1">

[MESSAGEROLLE]
[MESSAGE]


<form action="" method="post" name="eprooform">

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">


<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">{|Adresse|} <font color="blue">[ANZEIGENUMMER]</font></b>[ANZEIGENAMEDE]</td>
<td>[STATUSICONS]</td>
<td align="right">[ICONMENU]&nbsp;[BUTTONS2]&nbsp;<input type="submit" name="speichern" class="button-sticky"
    value="Speichern" onclick="this.form.action += '#tabs-1';"/> [ABBRECHEN]</td>
</tr>
</table>
</div>
</div>
</div>
</div>

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">

[BUTTONS]
[FORMHANDLEREVENT]
</div>
</div>
</div>
</div>



<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Stammdaten|}</legend>
		
    <table width="100%" class="mkTableFormular">
	  <tr><td >{|Typ|}:</td><td>[TYP][MSGTYP]</td></tr>
          <tr><td colspan="2"></td></tr>	

          <tr><td><span id="name_label"><label for="name">{|Name|}:</label>*</span></td><td>[NAME][MSGNAME]</td></tr>
          <tr><td colspan="2"><br></td></tr>	
          <tr><td><label for="titel">{|Titel|}:</label></td><td>[TITEL][MSGTITEL]</td></tr>
          <tr><td><span id="ansprechpartner_label"><label for="ansprechpartner">{|Ansprechpartner|}:</label></span></td><td>[ANSPRECHPARTNER][MSGANSPRECHPARTNER]</td></tr>
          <tr><td><label for="abteilung">{|Abteilung|}:</label></td><td>[ABTEILUNG][MSGABTEILUNG]</td></tr>
          <tr><td><label for="unterabteilung">{|Unterabteilung|}:</label></td><td>[UNTERABTEILUNG][MSGUNTERABTEILUNG]</td></tr>
	  <tr><td><label for="adresszusatz">{|Adresszusatz|}:</label></td><td>[ADRESSZUSATZ][MSGADRESSZUSATZ]</td></tr>

          <tr><td colspan="2"><br></td></tr>
          <tr><td><label for="strasse">{|Stra&szlig;e|}:</label></td><td>[STRASSE][MSGSTRASSE]</td></tr>
          <tr><td><label for="plz">PLZ</label>/<label for="ort">{|Ort|}:</label></td><td nowrap>[PLZ][MSGPLZ]&nbsp;[ORT][MSGORT]</td></tr>
          [VORBUNDESSTAAT]<tr valign="top"><td><label for="bundesstaat">{|Bundesstaat|}:</label></td><td colspan="2">[EPROO_SELECT_BUNDESSTAAT]</td></tr>[NACHBUNDESSTAAT]
          <tr valign="top"><td><label for="land">{|Land|}:</label></td><td colspan="2">[EPROO_SELECT_LAND]</td></tr>
          <tr><td colspan="2"><br></td></tr>	
          <tr><td><label for="lieferbedingung">{|Lieferbedingung|}:</label></td><td>[LIEFERBEDINGUNG][MSGLIEFERBEDINGUNG]</td></tr>
          <tr><td><label for="gln">{|GLN|}:</label></td><td>[GLN][MSGGLN]</td></tr>
          <tr><td colspan="2"><br></td></tr>	

            <tr><td><label for="abweichende_rechnungsadresse">{|Abw. Rechnungsadresse|}:</label></td><td>[ABWEICHENDE_RECHNUNGSADRESSE][MSGABWEICHENDE_RECHNUNGSADRESSE]</td></tr>
</table></fieldset>
</div>
</div>
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Kontaktdaten|}</legend>
    [VORNAME][MSGVORNAME]
    <table width="100%" class="mkTableFormular">
            <tr><td><label for="telefon">{|Telefon|}:</label></td><td>[TELEFON][MSGTELEFON]&nbsp;[TELEFONBUTTON]</td></tr>
            <tr><td><label for="telefax">{|Telefax|}:</label></td><td>[TELEFAX][MSGTELEFAX]</td></tr>
            <tr><td><label for="mobil">{|Mobil|}:</label></td><td>[MOBIL][MSGMOBIL]&nbsp;[MOBILBUTTON]</td></tr>
            <tr><td colspan="2"><br></td></tr>	
            <tr><td><label for="anschreiben">{|Anschreiben|}:</label></td><td>[ANSCHREIBEN][MSGANSCHREIBEN]</td></tr>
            <tr><td colspan="2"><br></td></tr>	
            <tr><td><label for="email">{|E-Mail|}:</label></td><td>[EMAIL][MSGEMAIL]&nbsp;[EMAILBUTTON]</td></tr>
            <tr><td><label for="internetseite">{|Internetseite|}:</label></td><td>[INTERNETSEITE][MSGINTERNETSEITE]&nbsp;[INTERNETBUTTON]</td></tr>
            <tr><td colspan="2"><br></td></tr>	
            [BUTTON_KONTAKTE]

</table></fieldset>

<fieldset><legend>{|Zuordnung|}</legend>
    [VORNAME][MSGVORNAME]
    <table width="100%" class="mkTableFormular">
              <tr><td>{|Vertrieb|}:</td><td>[VERTRIEB][MSGVERTRIEB]</td></tr>
        <tr><td>{|Innendienst|}:</td><td>[INNENDIENST][MSGINNENDIENST]</td></tr>
	<tr><td>{|Hauptprojekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
  <tr><td>{|Herkunftskanal (Shop)|}:</td><td>[FROMSHOP][MSGFROMSHOP]</td></tr>

	</table></fieldset>
</div>
</div>


<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">

[ADRESSE_EDIT_HOOK1]

<fieldset><legend>{|Einstellungen|}</legend>
    <table width="100%" class="mkTableFormular">

        <tr><td>{|Liefersperre|}:</td><td>[LIEFERSPERRE][MSGLIEFERSPERRE]&nbsp;{|Datum|}:&nbsp;[LIEFERSPERREDATUM][MSGLIEFERSPERREDATUM]</td></tr>
        <tr valign="top"><td>{|Liefersperre Grund|}:</td><td>[LIEFERSPERREGRUND][MSGLIEFERSPERREGRUND]</td></tr>
        <tr><td colspan="2"><br></td></tr>	
        <tr><td>{|Sprache f&uuml;r Belege|}:</td><td>[SPRACHE][MSGSPRACHE]</td></tr>

        <tr><td>{|Kundenfreigabe|}:</td><td>[KUNDENFREIGABE][MSGKUNDENFREIGABE]            </td></tr>
        <tr><td colspan="2"><br></td></tr>	
       	<tr><td>{|Folgebest&auml;tigungsperre|}:</td><td>[FOLGEBESTAETIGUNGSPERRE][MSGFOLGEBESTAETIGUNGSPERRE]</td></tr>
	<tr><td>{|Trackingmailsperre|}:</td><td>[TRACKINGSPERRE][MSGTRACKINGSPERRE]</td></tr>
        <tr><td>{|Marketingsperre|}:</td><td>[MARKETINGSPERRE][MSGMARKETINGSPERRE]</td></tr>
        <tr><td>{|Lead|}:</td><td>[LEAD][MSGLEAD]</td></tr>
    
</table>
</fieldset>

</div>
</div>
</div>
</div>





<div style="display:[ABWEICHENDERECHNUNGSADRESSESTYLE]" id="abweichenderechnungsadressestyle">
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Abweichende Rechnungsadresse|}</legend>
		[RECHNUNG_VORNAME][MSGRECHNUNG_VORNAME]
    <table width="100%" class="mkTableFormular">
	  <tr><td >{|Typ|}:</td><td>[RECHNUNG_TYP][MSGRECHNUNG_TYP]</td>
          <td>&nbsp;</td>
            <td></td><td></td></tr>

          <tr><td>{|Name|}:*</td><td>[RECHNUNG_NAME][MSGRECHNUNG_NAME]</td>
          <td>&nbsp;</td>
            <td>{|Telefon|}:</td><td>[RECHNUNG_TELEFON][MSGRECHNUNG_TELEFON]</td></tr>

    
      <tr><td>{|Titel|}:</td><td>[RECHNUNG_TITEL][MSGRECHNUNG_TITEL]</td>
          <td>&nbsp;</td>
            <td>{|Telefax|}:</td><td>[RECHNUNG_TELEFAX][MSGRECHNUNG_TELEFAX]</td></tr>

          <tr><td><span id="ansprechpartner_label">{|Ansprechpartner|}:</span></td><td>[RECHNUNG_ANSPRECHPARTNER][MSGRECHNUNG_ANSPRECHPARTNER]</td>
          <td>&nbsp;</td>
            <td>{|Anschreiben (Sehr geehrter ...)|}:</td><td>[RECHNUNG_ANSCHREIBEN][MSGRECHNUNG_ANSCHREIBEN]</td></tr>


          <tr><td>{|Abteilung|}:</td><td>[RECHNUNG_ABTEILUNG][MSGRECHNUNG_ABTEILUNG]</td><td>&nbsp;</td>
          <td>{|E-Mail|}:</td><td>[RECHNUNG_EMAIL][MSGRECHNUNG_EMAIL]</td></tr>

          <tr><td>{|Unterabteilung|}:</td><td>[RECHNUNG_UNTERABTEILUNG][MSGRECHNUNG_UNTERABTEILUNG]</td><td>&nbsp;</td>
           <td></td><td></td></tr>

	  <tr><td>{|Adresszusatz|}:</td><td>[RECHNUNG_ADRESSZUSATZ][MSGRECHNUNG_ADRESSZUSATZ]</td><td>&nbsp;</td>
          <td></td><td></td></tr>

          <tr><td>{|Stra&szlig;e|}:</td><td>[RECHNUNG_STRASSE][MSGRECHNUNG_STRASSE]</td><td>&nbsp;</td>
            <td></td><td>
	    </td></tr>
          <tr><td>{|PLZ/Ort|}:</td><td nowrap>[RECHNUNG_PLZ][MSGRECHNUNG_PLZ]&nbsp;[RECHNUNG_ORT][MSGRECHNUNG_ORT]</td><td>&nbsp;</td>
            <td colspan="2" rowspan="2" align="right">[BUTTON_KONTAKTE_RECHNUNG]</td></tr>
          [VORBUNDESSTAAT]<tr valign="top"><td><label for="rechung_bundesstaat">{|Bundesstaat|}:</label></td><td colspan="2">[EPROO_SELECT_BUNDESSTAAT_RECHNUNG]</td></tr>[NACHBUNDESSTAAT]
          <tr><td>{|Land|}:</td><td colspan="2">[EPROO_SELECT_LAND_RECHNUNG]</td>
          </tr>
          <tr><td>{|GLN|}:</td><td>[RECHNUNG_GLN][MSGRECHNUNG_GLN]</td></tr>

</table></fieldset>

</div>
</div>
</div>
</div>

</div>

[BENUTZERDEFINIERTSTART]
    

<div class="row">
<div class="row-height">

<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>[BENUTZERDEFINIERT]</legend>
[FREIFELDSPALTE1]
</fieldset>
</div>
</div>

<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>&nbsp;</legend>
[FREIFELDSPALTE2]
</fieldset>
</div>
</div>

</div>
</div>

[BENUTZERDEFINIERTENDE]

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Sonstiges|}</legend>
<table width="98%"  class="mkTableFormular">
	  <tr><td>{|Info f&uuml;r Auftragserfassung|}:</td><td colspan="4">[INFOAUFTRAGSERFASSUNG][MSGINFOAUFTRAGSERFASSUNG]</td></tr>
	  <tr><td>{|Sonstiges|}:</td><td colspan="4">[SONSTIGES][MSGSONSTIGES]</td></tr>
          </table>
</fieldset>
</div>
</div>
</div>
</div>




</div>



<div id="tabs-2">
[MESSAGE]

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">

<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">{|Adresse|} <font color="blue">[ANZEIGENUMMER]</font></b>[ANZEIGENAMEDE]</td>
<td>[STATUSICONS]</td>
<td align="right">[ICONMENU]&nbsp;[BUTTONS2]&nbsp;<input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-2';"/> [ABBRECHEN]</td>
</tr>
</table>
 </div> 
 </div> 
 </div> 
 </div> 

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">

<fieldset style="min-height:290px"><legend>{|Zahlungskonditionen des Kunden f&uuml;r Rechnungen|}</legend>

 <table width="100%" class="mkTableFormular">
            <tbody>
<tr><td>{|Zahlungskonditionen festschreiben|}:</td><td>[ZAHLUNGSKONDITIONEN_FESTSCHREIBEN][MSGZAHLUNGSKONDITIONEN_FESTSCHREIBEN]&nbsp;<i>{|Immer diese verwenden (nie von Gruppe)|}</i></td></tr>
<tr><td width="200">{|Zahlungsweise|}:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]<td></tr>
<tr><td>{|Zahlungsziel (bei Rechnung)|}:</td><td>[ZAHLUNGSZIELTAGE][MSGZAHLUNGSZIELTAGE]&nbsp;{|in Tagen|}</td></tr>
<tr><td nowrap>{|Zahlungsziel Skonto (bei Rechnung)|}:</td><td>[ZAHLUNGSZIELTAGESKONTO][MSGZAHLUNGSZIELTAGESKONTO]&nbsp;{|in Tagen|}</td></tr>
<tr><td>{|Skonto (bei Rechnung)|}:</td><td>[ZAHLUNGSZIELSKONTO][MSGZAHLUNGSZIELSKONTO]&nbsp;{|in %|}</td></tr>
<tr><td width="210">{|Lieferantennummer bei Kunde|}:</td><td>[LIEFERANTENNUMMERBEIKUNDE][MSGLIEFERANTENNUMMERBEIKUNDE]</td></tr>
<tr><td>{|Zahlungsweise Abo|}:</td><td>[ZAHLUNGSWEISEABO][MSGZAHLUNGSWEISEABO]</td></tr>
<tr><td>{|Belege im Auto-Versand erstellen|}:</td><td>[ART][MSGART]</td></tr>
[VORKOMMISSIONIERLAGER]
<tr><td>{|Kommissions-/Konsignationslager|}:</td><td>[KOMMISSIONSKONSIGNATIONSLAGER][MSGKOMMISSIONSKONSIGNATIONSLAGER]</td></tr>
[NACHKOMMISSIONIERLAGER]
</tbody></table>
</fieldset>



</div>
</div>

<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">


<fieldset style="min-height:290px"><legend>{|Zahlungskonditionen beim Lieferant bei Bestellungen|}</legend>
<table border="0" width="100%"  class="mkTableFormular" align="left">
            <tbody>
<tr><td>{|Zahlungsweise|}:</td><td>[ZAHLUNGSWEISELIEFERANT][MSGZAHLUNGSWEISELIEFERANT]</td></tr>
<tr><td>{|Zahlungsziel (in Tagen)|}:</td><td>[ZAHLUNGSZIELTAGELIEFERANT][MSGZAHLUNGSZIELTAGELIEFERANT]&nbsp;{|in Tagen|}</td></tr>
<tr><td nowrap>{|Zahlungsziel Skonto (in Tagen)|}:</td><td>[ZAHLUNGSZIELTAGESKONTOLIEFERANT][MSGZAHLUNGSZIELTAGESKONTOLIEFERANT]&nbsp;{|in Tagen|}</td></tr>
<tr><td>{|Skonto|}:</td><td>[ZAHLUNGSZIELSKONTOLIEFERANT][MSGZAHLUNGSZIELSKONTOLIEFERANT]&nbsp;{|in %|}</td></tr>
<tr><td>{|Lieferart|}:</td><td>[VERSANDARTLIEFERANT][MSGVERSANDARTLIEFERANT]</td></tr>
<tr><td width="210">{|Kundennummer bei Lieferant|}:</td><td>[KUNDENNUMMERLIEFERANT][MSGKUNDENNUMMERLIEFERANT]</td></tr>
<tr><td>{|Besteuerung Verbindlichkeiten|}:</td><td>[UMSATZSTEUER_LIEFERANT][MSGUMSATZSTEUER_LIEFERANT]</td></tr>
<tr><td>{|Lieferant Hinweis-Text|}:</td><td>[HINWEISTEXTLIEFERANT][MSGHINWEISTEXTLIEFERANT]<td></tr>
</tbody></table>

</fieldset>



</div>
</div>





</div>
</div>


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Steuer / W&auml;hrung / Zoll|}</legend>
  <table width="98%" class="mkTableFormular">
    <tr><td>{|USt-ID|}:</td><td>[USTID][MSGUSTID]</td></tr>
    <tr><td>{|Steuernummer|}:</td><td>[STEUERNUMMER][MSGSTEUERNUMMER]</td></tr>
    <tr><td>{|Besteuerung|}:</td><td>[UST_BEFREIT][MSGUST_BEFREIT]</td></tr>
    <tr><td>{|Standard W&auml;hrung|}:</td><td>[WAEHRUNG][MSGWAEHRUNG]</td></tr>
    <tr><td>{|Lieferschwelle nicht anwenden|}:</td><td>[LIEFERSCHWELLENICHTANWENDEN][MSGLIEFERSCHWELLENICHTANWENDEN]</td></tr>
    <tr><td>{|Anzeige Steuer auf Belege|}:</td><td>
    [ANZEIGESTEUERBELEGE][MSGANZEIGESTEUERBELEGE]</td></tr>
      [VORPROFORMARECHNUNG]
      <tr><td >{|Zollinformationen|}:</td><td>[ZOLLINFORMATIONEN][MSGZOLLINFORMATIONEN]</td></tr>
      [NACHPROFORMATRECHNUNG]
  </table>
</fieldset>
</div>
</div>
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>{|Kunde/Lieferant|}</legend>
<table  class="mkTableFormular">
          <tr><td></td><td></td><td>Abw. Debitoren- bzw. Kreditoren Nummer</td></tr>
          <tr><td >Kunden Nr.:</td><td>[KUNDENNUMMER][MSGKUNDENNUMMER]</td><td>[KUNDENNUMMER_BUCHHALTUNG][MSGKUNDENNUMMER_BUCHHALTUNG]</td></tr>
          <tr><td>Lieferanten Nr.:</td><td>[LIEFERANTENNUMMER][MSGLIEFERANTENNUMMER]</td><td>[LIEFERANTENNUMMER_BUCHHALTUNG][MSGLIEFERANTENNUMMER_BUCHHALTUNG]</td></tr>
          <tr><td>Mitarbeiter Nr.:</td><td>[MITARBEITERNUMMER][MSGMITARBEITERNUMMER]</td><td></td></tr>
          [STARTDISABLEVERBAND]<tr><td>{|Mitgliedsnummer im Verband|}:</td><td>[VERBANDSNUMMER][MSGVERBANDSNUMMER]</td><td></td></tr>[ENDEDISABLEVERBAND]
</table>
</fieldset>
</div>
</div>



</div>
</div>


</div>




<div id="tabs-3">
[MESSAGE]
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">
<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">{|Adresse|} <font color="blue">[ANZEIGENUMMER]</font></b>[ANZEIGENAMEDE]</td>
<td>[STATUSICONS]</td>
<td align="right">[ICONMENU]&nbsp;[BUTTONS2]&nbsp;<input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-3';"/> [ABBRECHEN]</td>
</tr>
</table>

 </div> 
 </div> 
 </div> 
 </div> 

<fieldset><legend>{|Bankverbindung|}</legend>
<table width="100%" class="mkTableFormular">
<tr><td width="210">{|Inhaber|}:</td><td>[INHABER][MSGINHABER]</td>
<td>&nbsp;</td>
<td>{|Bank|}:</td><td>[BANK][MSGBANK]</td></tr>

<!--<tr><td>{|Konto|}:</td><td>[KONTO][MSGKONTO]</td><td>&nbsp;</td>
<td>{|BLZ|}:</td><td>[BLZ][MSGBLZ]</td></tr>-->

<tr><td>{|BIC|}:</td><td>[SWIFT][MSGSWIFT]</td>
<td>&nbsp;</td>
<td>{|IBAN|}:</td><td>[IBAN][MSGIBAN]</td></tr>

<tr><td>{|Mandatsreferenz|}:</td><td>[MANDATSREFERENZ][MSGMANDATSREFERENZ]</td>
<td>&nbsp;</td>
<td>{|Lastschrift Art|}:</td><td>[MANDATSREFERENZART][MSGMANDATSREFERENZART]
&nbsp;[MANDATSREFERENZWDHART][MSGMANDATSREFERENZWDHART]
</td></tr>

<tr><td>{|Mandatsreferenz Datum|}:</td><td>[MANDATSREFERENZDATUM][MSGMANDATSREFERENZDATUM]</td>
<td>&nbsp;</td>
<td>{|Mandatsreferenz &Auml;nderung|}:</td><td>[MANDATSREFERENZAENDERUNG][MSGMANDATSREFERENZAENDERUNG]&nbsp;<i>&Auml;nderung seit letzter Lastschrift</i></td></tr>


<tr><td></td><td></td><td>&nbsp;</td>
<td>Firmen-SEPA</td><td>[FIRMENSEPA][MSGFIRMENSEPA]</td></tr>


<tr><td></td><td></td><td>&nbsp;</td>
<td></td><td><a class="button button-secondary" href="index.php?module=adresse&action=sepamandat&id=[ID]">Download SEPA Mandatsreferenz</a></td></tr>


<tr><td>{|Bemerkung|}:</td><td colspan="4">[MANDATSREFERENZHINWEIS][MSGMANDATSREFERENZHINWEIS]</td></tr>

</table></fieldset>
<fieldset><legend>{|Paypal (bei Zahlungen)|}</legend>
<table width="100%" class="mkTableFormular">
<tr><td width="210">{|Inhaber|}:</td><td>[PAYPALINHABER][MSGPAYPALINHABER]</td>
<td>&nbsp;</td>
<td>{|Paypal-Account|}:</td><td>[PAYPAL][MSGPAYPAL]</td></tr>
<tr><td>{|W&auml;hrung|}:</td><td>[PAYPALWAEHRUNG][MSGPAYPALWAEHRUNG]</td><td>&nbsp;</td>
<td></td><td></td></tr>
</table></fieldset>
</div>


<div id="tabs-4">
[MESSAGE]

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">
<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">{|Adresse|} <font color="blue">[ANZEIGENUMMER]</font></b>[ANZEIGENAMEDE]</td>
<td>[STATUSICONS]</td>
<td align="right">[ICONMENU]&nbsp;[BUTTONS2]&nbsp;<input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-4';"/> [ABBRECHEN]</td>
</tr>
</table>

 </div> 
 </div> 
 </div> 
 </div> 

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Allgemeine Versandoptionen|}</legend> 
 <table width="100%" class="mkTableFormular">
<tr><td width="210">{|Immer Papier Rechnung|}:</td><td>[RECHNUNG_PAPIER][MSGRECHNUNG_PAPIER]&nbsp;<i>Bei automatischen Versand wird immer eine Rechnung per Papier versendet (auch wenn eine E-Mail vorhanden ist)</i></td></tr>
<tr><td width="210">{|Anzahl Ausdrucke Rechnung|}:</td><td>[RECHNUNG_ANZAHLPAPIER][MSGRECHNUNG_ANZAHLPAPIER]&nbsp;<i>Anzahl der Ausdrucke beim Versand</i></td></tr>
<!--<tr><td width="210">{|Immer E-Mail Rechnung|}:</td><td>[RECHNUNG_PERMAIL][MSGRECHNUNG_PERMAIL]&nbsp;</i></td></tr>-->
</table>
</fieldset>

 </div> 
 </div> 
 </div> 
 </div> 

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend id='adresseemailempfaenger'>E-Mail Empf&auml;nger (Im Dokument abschicken Dialog wenn E-Mail leer war oder Beleg neu angelegt wurde)</legend>
<table width="100%" class="mkTableFormular">
<tr><td width="210">{|Angebot|}:</td><td>[ANGEBOT_EMAIL][MSGANGEBOT_EMAIL]&nbsp;<i>Vorauswahl E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Auftrag|}:</td><td>[AUFTRAG_EMAIL][MSGAUFTRAG_EMAIL]&nbsp;<i>Vorauswahl E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Rechnung|}:</td><td>[RECHNUNGS_EMAIL][MSGRECHNUNGS_EMAIL]&nbsp;<i>Vorauswahl E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Gutschrift|}:</td><td>[GUTSCHRIFT_EMAIL][MSGGUTSCHRIFT_EMAIL]&nbsp;<i>Vorauswahl E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Lieferschein|}:</td><td>[LIEFERSCHEIN_EMAIL][MSGLIEFERSCHEIN_EMAIL]&nbsp;<i>Vorauswahl E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Bestellung|}:</td><td>[BESTELLUNG_EMAIL][MSGBESTELLUNG_EMAIL]&nbsp;<i>Vorauswahl E-Mail Empf&auml;nger</i></td></tr>
</table></fieldset>

</div>
</div>
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>E-Mail Kopie Empf&auml;nger (Dokument abschicken)</legend>
 <table width="100%" class="mkTableFormular">
<tr><td width="210">{|Angebot|}:</td><td>[ANGEBOT_CC][MSGANGEBOT_CC]&nbsp;<i>Zus&auml;tzlich eingetragener E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Auftrag|}:</td><td>[AUFTRAG_CC][MSGAUFTRAG_CC]&nbsp;<i>Zus&auml;tzlich eingetragener E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Rechnung|}:</td><td>[RECHNUNG_CC][MSGRECHNUNG_CC]&nbsp;<i>Zus&auml;tzlich eingetragener E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Gutschrift|}:</td><td>[GUTSCHRIFT_CC][MSGGUTSCHRIFT_CC]&nbsp;<i>Zus&auml;tzlich eingetragener E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Lieferschein|}:</td><td>[LIEFERSCHEIN_CC][MSGLIEFERSCHEIN_CC]&nbsp;<i>Zus&auml;tzlich eingetragener E-Mail Empf&auml;nger</i></td></tr>
<tr><td width="210">{|Bestellung|}:</td><td>[BESTELLUNG_CC][MSGBESTELLUNG_CC]&nbsp;<i>Zus&auml;tzlich eingetragener E-Mail Empf&auml;nger</i></td></tr>
</table></fieldset>



</div>
</div>
</div>
</div>


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>Fax Kopie Empf&auml;nger</legend>
    <table width="100%" class="mkTableFormular">
<tr><td width="210">{|AB per Fax bevorzugt|}:</td><td>[ABPERFAX][MSGABPERFAX]&nbsp;<i>Fax vorausw&auml;hlen bei Versand</i></td></tr>
</table></fieldset>
 </div> 
 </div> 
 </div> 
 </div> 



</div>







<div id="tabs-5">
[MESSAGE]
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">
<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">{|Adresse|} <font color="blue">[ANZEIGENUMMER]</font></b>[ANZEIGENAMEDE]</td>
<td>[STATUSICONS]</td>
<td align="right">[ICONMENU]&nbsp;[BUTTONS2]&nbsp;<input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-5';"/> [ABBRECHEN]</td>
</tr>
</table>

 </div> 
 </div> 
 </div> 
 </div> 

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Vertrieb / Innendienst|}</legend>
<table border="0" width="100%" class="mkTableFormular">
            <tbody>
<tr><td>{|Provision|}:</td><td>[PROVISION][MSGPROVISION] % <i>(In Prozent f&uuml;r Vertrieb)</td></tr>
</tbody></table>
</fieldset>

</div>
</div>
</div>
</div>

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Porto / Versandart|}</legend>
    <table width="100%" class="mkTableFormular">
            <tbody>
<tr><td width="210">{|Porto frei aktiv|}:</td><td>[PORTOFREI_AKTIV][MSGPORTOFREI_AKTIV]&nbsp;ab&nbsp;[PORTOFREIAB][MSGPORTOFREIAB]&nbsp;&euro;&nbsp;<i>Porto frei ab bestimmtem Umsatz (netto)</i></td></tr>

<tr><td>{|Versandart|}:</td><td>[VERSANDART][MSGVERSANDART]</td></tr>
<tr><td>{|Keine Alterspr&uuml;fung notwendig|}:</td><td>[KEINEALTERSABFRAGE][MSGKEINEALTERSABFRAGE]</td></tr>
<!--<tr><td>{|Standard Tour|}:</td><td>[TOUR][MSGTOUR]</td></tr>-->

</tbody></table>
</fieldset>

</div>
</div>
</div>
</div>
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Porto bei Lieferant|}</legend>
    <table width="100%" class="mkTableFormular">
            <tbody>
<tr><td width="210">{|Porto frei aktiv|}:</td><td>[PORTOFREILIEFERANT_AKTIV][MSGPORTOFREILIEFERANT_AKTIV]&nbsp;ab&nbsp;[PORTOFREIABLIEFERANT][MSGPORTOFREIABLIEFERANT]&nbsp;&euro;&nbsp;<i>Porto frei ab bestimmtem Umsatz (netto)</i></td></tr>
</tbody></table>
</fieldset>

</div>
</div>
</div>
</div>


[STARTDISABLEVERBAND]
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
    <table width="100%" class="mkTableFormular">
          <tr><td width="200">{|Rabatte|}:</td><td>
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

</table>

</td><td></tr>
</table>

<table width="100%" class="mkTableFormular">
<tr><td width="210">{|Rabattinformationen|}:</td><td>[RABATTINFORMATION][MSGRABATTINFORMATION]</td></tr>
<tr><td width="210">{|Filiale|}:</td><td>[FILIALE][MSGFILIALE]</td></tr>
</table>


</div>
</div>
</div>
</div>
[ENDEDISABLEVERBAND]

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Sonstiges|}</legend>
<table width="100%" class="mkTableFormular">
<tr><td width="210">{|Geburtstag|}:</td><td>[GEBURTSTAG][MSGGEBURTSTAG]&nbsp;[GEBURTSTAGKALENDER][MSGGEBURTSTAGKALENDER]&nbsp;im Kalender anzeigen</td>
<td>&nbsp;</td>
<td>{|Geburtstagskarte|}:</td><td>[GEBURTSTAGSKARTE][MSGGEBURTSTAGSKARTE]</td></tr>

</table></fieldset>


</div>
</div>
</div>
</div>
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Finanzbuchhaltung|}</legend>
<table width="100%" class="mkTableFormular">
<tr><td width="210">{|Reisekosten|}:</td><td>[VERRECHNUNGSKONTOREISEKOSTEN][MSGVERRECHNUNGSKONTOREISEKOSTEN]&nbsp;<i>Verrechnungskonto</i></td>
<td>&nbsp;</td>
<td></td><td></td></tr>
<!--<tr><td width="210">{|Sachkonto Erl&ouml;se|}:</td><td>[SACHKONTO][MSGSACHKONTO]&nbsp;<i>Optional falls es manuell f&uuml;r Kunden angegeben werden soll.</i></td>
<td>&nbsp;</td>
<td></td><td></td></tr>-->


<tr><td width="210">{|Kredit Limit|}:</td><td>[KREDITLIMIT][MSGKREDITLIMIT]&nbsp;<i>in &euro;</i></td>
<td>&nbsp;</td>
<td></td><td></td></tr>



</table></fieldset>

</div>
</div>
</div>
</div>
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Personalwesen Einstellungen|}</legend>
<table width="100%" class="mkTableFormular">
<tr><td width="210">{|Arbeitszeit pro Woche|}:</td><td>[ARBEITSZEITPROWOCHE][MSGARBEITSZEITPROWOCHE]&nbsp;<i>in Stunden</i></td>
<td>&nbsp;</td>
<td></td><td></td></tr>
</table>
</fieldset>

</div>
</div>
</div>
</div>
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Freifelder |}</legend>
<table width="100%" class="mkTableFormular">
[VORFREIFELD1]<tr><td width="210">[FREIFELD1BEZEICHNUNG]:</td><td>[FREIFELD1][MSGFREIFELD1]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD1]
[VORFREIFELD2]<tr><td width="210">[FREIFELD2BEZEICHNUNG]:</td><td>[FREIFELD2][MSGFREIFELD2]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD2]
[VORFREIFELD3]<tr><td width="210">[FREIFELD3BEZEICHNUNG]:</td><td>[FREIFELD3][MSGFREIFELD3]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD3]
[VORFREIFELD4]<tr><td width="210">[FREIFELD4BEZEICHNUNG]:</td><td>[FREIFELD4][MSGFREIFELD4]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD4]
[VORFREIFELD5]<tr><td width="210">[FREIFELD5BEZEICHNUNG]:</td><td>[FREIFELD5][MSGFREIFELD5]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD5]
[VORFREIFELD6]<tr><td width="210">[FREIFELD6BEZEICHNUNG]:</td><td>[FREIFELD6][MSGFREIFELD6]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD6]
[VORFREIFELD7]<tr><td width="210">[FREIFELD7BEZEICHNUNG]:</td><td>[FREIFELD7][MSGFREIFELD7]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD7]
[VORFREIFELD8]<tr><td width="210">[FREIFELD8BEZEICHNUNG]:</td><td>[FREIFELD8][MSGFREIFELD8]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD8]
[VORFREIFELD9]<tr><td width="210">[FREIFELD9BEZEICHNUNG]:</td><td>[FREIFELD9][MSGFREIFELD9]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD9]
[VORFREIFELD10]<tr><td width="210">[FREIFELD10BEZEICHNUNG]:</td><td>[FREIFELD10][MSGFREIFELD10]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD10]
[VORFREIFELD11]<tr><td width="210">[FREIFELD11BEZEICHNUNG]:</td><td>[FREIFELD11][MSGFREIFELD11]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD11]
[VORFREIFELD12]<tr><td width="210">[FREIFELD12BEZEICHNUNG]:</td><td>[FREIFELD12][MSGFREIFELD12]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD12]
[VORFREIFELD13]<tr><td width="210">[FREIFELD13BEZEICHNUNG]:</td><td>[FREIFELD13][MSGFREIFELD13]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD13]
[VORFREIFELD14]<tr><td width="210">[FREIFELD14BEZEICHNUNG]:</td><td>[FREIFELD14][MSGFREIFELD14]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD14]
[VORFREIFELD15]<tr><td width="210">[FREIFELD15BEZEICHNUNG]:</td><td>[FREIFELD15][MSGFREIFELD15]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD15]
[VORFREIFELD16]<tr><td width="210">[FREIFELD16BEZEICHNUNG]:</td><td>[FREIFELD16][MSGFREIFELD16]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD16]
[VORFREIFELD17]<tr><td width="210">[FREIFELD17BEZEICHNUNG]:</td><td>[FREIFELD17][MSGFREIFELD17]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD17]
[VORFREIFELD18]<tr><td width="210">[FREIFELD18BEZEICHNUNG]:</td><td>[FREIFELD18][MSGFREIFELD18]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD18]
[VORFREIFELD19]<tr><td width="210">[FREIFELD19BEZEICHNUNG]:</td><td>[FREIFELD19][MSGFREIFELD19]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD19]
[VORFREIFELD20]<tr><td width="210">[FREIFELD20BEZEICHNUNG]:</td><td>[FREIFELD20][MSGFREIFELD20]&nbsp;</td><td>&nbsp;</td><td></td><td></td></tr>[NACHFREIFELD20]

</table></fieldset>

</div>
</div>
</div>
</div>
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Geodaten|}</legend>
<table width="100%" class="mkTableFormular">
<tr><td width="210">{|Breitengrad|}:</td><td>[LAT][MSGLAT]</td><td>&nbsp;</td><td></td><td></td></tr>
<tr><td width="210">Längengrad:</td><td>[LNG][MSGLNG]</td><td>&nbsp;</td><td></td><td></td></tr>
</table></fieldset>


</div>
</div>
</div>
</div>
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>Kennung (f&uuml;r vereinfachte Imports f&uuml;r Zeitferfassungen, Artikel-Imports, etc.)</legend>
<table width="100%" class="mkTableFormular">
<tr><td width="210">{|Eindeutige Kennung|}:</td><td>[KENNUNG][MSGKENNUNG]&nbsp;<i>(Grossbuchstaben, keine Sonderzeichen)</i></td>
<td>&nbsp;</td>
<td></td><td></td></tr>
</table></fieldset>

</div>
</div>
</div>
</div>
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Logbuch|}</legend>
<table width="100%" class="mkTableFormular">
<tr valign="top"><td width="210">{|Eintr&auml;ge|}:</td><td>[LOGFILE]</td>
</tr>
</table></fieldset>

</div>
</div>
</div>
</div>

  
</form>
</div>


</div>

