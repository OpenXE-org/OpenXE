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
        <td>
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%" border="0">
   <tr><td width="130">{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td><td></td></tr>

   <tr><td width="130">{|Quelle|}:</td><td colspan="2">
		[ZIEL][MSGZIEL]&nbsp;<i>(Auswahl Quelltabelle f&uuml;r Daten)</i></td></tr>
   <tr><td width="130">{|CSV Beschriftung|}:</td><td>[EXPORTERSTEZEILENUMMER][MSGEXPORTERSTEZEILENUMMER]&nbsp;<i>Erste Zeile = Beschriftung</i></td><td></td></tr>
    <tr><td width="130">{|CSV Trennzeichen|}:</td><td>[EXPORTTRENNZEICHEN][MSGEXPORTTRENNZEICHEN]</td><td></td></tr>
    <tr><td width="130">{|CSV Maskierung|}:</td><td>[EXPORTDATENMASKIERUNG][MSGEXPORTDATENMASKIERUNG]</td><td></td></tr>
   <tr><td width="130">{|Filter Datum|}:</td><td>[FILTERDATUM][MSGFILTERDATUM]&nbsp;<i>Bei der Ausgabe kann man ein Datumsbereich angeben</i></td><td></td></tr>
   <tr><td width="130">{|Filter Projekt|}:</td><td>[FILTERPROJEKT][MSGFILTERPROJEKT]&nbsp;<i>Bei der Ausgabe kann man ein Projekt angeben</i></td><td></td></tr>

   <tr><td width="130">{|API Freigabe|}:</td><td>[APIFREIGABE][MSGAPIFREIGABE]&nbsp;<i>Abfrage &uuml;ber API freigeben</i></td><td></td></tr>
<tr valign="top"><td width="130">{|CSV Felder|}:</td><td><table><tr valign="top"><td>[FIELDS][MSGFIELDS]</td><td><i>Feldname;<br>Feldname;<br></td></tr>
</table>

</td><td align="center">
</td></tr>
<tr valign="top"><td width="130">{|Filter|}:</td><td><table><tr valign="top"><td>[FIELDS_WHERE][MSGFIELDS_WHERE]</td><td><i>Feldname > 1;<br>Feldname LIKE '8%';<br></td></tr>
</table>

</td><td align="center">
</td></tr>



<tr valign="top"><td>Verfügbaren Felder:</td><td colspan="2">
<table width="100%"><tr valign="top"><td><b>Artikel/Einkauf<br><br></b>
<ul>
<li><u>nummer</u>;</li>
<!--<li><u>bestellnummer</u>(vom Hersteller);</li>-->
<!--<li>ekpreis;</li>
<li>vkpreis;</li>
<li>ab_menge;</li>-->
<li>name_de;</li>
<li>name_en;</li>
<li>kurztext_de;</li>
<li>kurztext_en;</li>
<li>artikelbeschreibung_de;</li>
<li>artikelbeschreibung_en;</li>
<li>anabregs_text;</li>
<li>internerkommentar;</li>
<li>hersteller;</li>
<li>typ;</li>
<li>herstellerlink;</li>
<li><u>herstellernummer</u>;</li>
<li>ean;</li>
<li>verkaufspreisnetto;</li>
<li>einkaufspreisnetto;</li>
<li>lieferantname;</li>
<li>lieferantnummer;</li>
<li>lager_menge;</li>
<li>inventurek;</li>
<li>standardlagerplatz;</li>
<li>inventurekaktiv;</li>
<li>freifeldX; <i>(X = 1-40)</i></li>
<li>freifeldnameX; <i>(X = 1-40)</i></li>
<li>eigenschaftnameX; <i>(X = 1-50)</i></li>
<li>eigenschaftwertX; <i>(X = 1-50)</i></li>
<li>artikelkategorie;</li>
<li>artikelkategorie_name;</li>
<li>artikelbaumX; <i>(X = 1-20)</i></li>
<li>zolltarifnummer;</li>
<li>ursprungsregion;</li>
<li>berechneterek;</li>
<li>berechneterekwaehrung;</li>
<li>verwendeberechneterek;</li>
<li>steuer_aufwendung_inland_import;</li>
<li>steuer_aufwendung_inland_normal;</li>
<li>steuer_aufwendung_inland_eunormal;</li>
<li>steuer_aufwendung_inland_ermaessigt;</li>
<li>steuer_aufwendung_inland_euermaessigt;</li>
<li>steuer_aufwendung_inland_nichtsteuerbar;</li>
<li>steuer_aufwendung_inland_innergemeinschaftlich;</li>
<li>steuer_erloese_inland_export;</li>
<li>steuer_erloese_inland_eunormal;</li>
<li>steuer_erloese_inland_ermaessigt;</li>
<li>steuer_erloese_inland_euermaessigt;</li>
<li>steuer_erloese_inland_innergemeinschaftlich;</li>
<li>mindestlager;</li>
<li>mindestbestellung;</li>
<li>gewicht;</li>
<li>breite;</li>
<li>hoehe;</li>
<li>laenge;</li>
<li>einheit;</li>
<li>lagerartikel;</li>
<li>standardlagerplatz;</li>
<li>geloescht;</li>
<li>intern_gesperrt;</li>
<li>intern_gesperrtgrund;</li>
<li>lagerkorrekturwert;</li>
<li>aktiv / inaktiv;</li>
<li>juststueckliste;</li>
<li>autolagerlampe;</li>
<li>variante_von;</li>
<li>projekt;</li>
<li>verkaufspreisnettoX; <i>(X = 1-50)</i></li>
<li>verkaufspreisabmengeX; <i>(X = 1-50)</i></li>
<li>verkaufspreisvpemengeX; <i>(X = 1-50)</i></li>
<li>verkaufspreiswaehrungX; <i>(X = 1-50)</i></li>
<li>verkaufspreisgruppeX; <i>(X = 1-50)</i></li>
<li>verkaufspreiskundennummerX; <i>(X = 1-50)</i></li>
<li>verkaufspreisartikelnummerbeikundeX; <i>(X = 1-50)</i></li>
<li>verkaufspreisgueltigabX; <i>(X = 1-50)</i></li>
<li>verkaufspreisgueltigbisX; <i>(X = 1-50)</i></li>
<li>verkaufspreisinternerkommentarX; <i>(X = 1-50)</i></li>
</ul>

</td><td><b>Adressen<br><br></b>
<ul>
<li>typ; <i>(herr,frau,firma)</i></li>
<li>marketingsperre;</li>
<li>trackingsperre;</li>
<li>rechnungsadresse;</li>
<li>sprache;</li>
<li>name;</li>
<li>abteilung;</li>
<li>unterabteilung;</li>
<li>ansprechpartner;</li>
<li>land; <i>(DE,AT,...)</i></li>
<li>strasse;</li>
<li>ort;</li>
<li>plz;</li>
<li>telefon;</li>
<li>telefax;</li>
<li>mobil;</li>
<li>email;</li>
<li>ustid;</li>
<li>ust_befreit;</li>
<li>sonstiges;</li>
<li>adresszusatz;</li>
<li>kundenfreigabe;</li>
<li>steuer;</li>
<li>logdatei;</li>
<li>kundennummer;</li>
<li>lieferantennummer;</li>
<li>mitarbeiternummer;</li>
<li>konto;</li>
<li>blz;</li>
<li>bank;</li>
<li>inhaber;</li>
<li>swift;</li>
<li>iban;</li>
<li>waehrung;</li>
<li>paypal;</li>
<li>paypalinhaber;</li>
<li>paypalwaehrung;</li>
<li>projekt;</li>
<li>zahlungsweise;</li>
<li>zahlungszieltage;</li>
<li>zahlungszieltageskonto;</li>
<li>zahlungszielskonto;</li>
<li>versandart;</li>
<li>kundennummerlieferant;</li>
<li>zahlungsweiselieferant;</li>
<li>zahlungszieltagelieferant;</li>
<li>zahlungszieltageskontolieferant;</li>
<li>zahlungszielskontolieferant;</li>
<li>versandartlieferant;</li>
<li>geloescht;</li>
<li>firma;</li>
<li>webid;</li>
<li>internetseite;</li>
<li>vorname;</li>
<li>titel;</li>
<li>anschreiben;</li>
<li>geburtstag;</li>
<li>liefersperre;</li>
<li>steuernummer;</li>
<li>steuerbefreit;</li>
<li>liefersperregrund;</li>
<li>verrechnungskontoreisekosten;</li>
<li>abweichende_rechnungsadresse;</li>
<li>rechnung_vorname;</li>
<li>rechnung_name;</li>
<li>rechnung_titel;</li>
<li>rechnung_typ;</li>
<li>rechnung_strasse;</li>
<li>rechnung_ort;</li>
<li>rechnung_land;</li>
<li>rechnung_abteilung;</li>
<li>rechnung_unterabteilung;</li>
<li>rechnung_adresszusatz;</li>
<li>rechnung_telefon;</li>
<li>rechnung_telefax;</li>
<li>rechnung_anschreiben;</li>
<li>rechnung_email;</li>
<li>rechnung_plz;</li>
<li>rechnung_ansprechpartner;</li>
<li>kennung;</li>
<li>vertrieb;</li>
<li>innendienst;</li>
<li>rabatt;</li>
<li>rabatt1;</li>
<li>rabatt2;</li>
<li>rabatt3;</li>
<li>rabatt4;</li>
<li>rabatt5;</li>
<li>bonus1;</li>
<li>bonus1_ab;</li>
<li>bonus2;</li>
<li>bonus2_ab;</li>
<li>bonus3;</li>
<li>bonus3_ab;</li>
<li>bonus4;</li>
<li>bonus4_ab;</li>
<li>bonus5;</li>
<li>bonus5_ab;</li>
<li>bonus6;</li>
<li>bonus6_ab;</li>
<li>bonus7;</li>
<li>bonus7_ab;</li>
<li>bonus8;</li>
<li>bonus8_ab;</li>
<li>bonus9;</li>
<li>bonus9_ab;</li>
<li>bonus10;</li>
<li>bonus10_ab;</li>
<li>verbandsnummer;</li>
<li>portofreiab;</li>
<li>zahlungskonditionen_festschreiben;</li>
<li>rabatte_festschreiben;</li>
<li>provision;</li>
<li>portofrei_aktiv;</li>
<li>rabattinformation;</li>
<li>freifeldX; <i>(X = 1-20)</i></li>
<li>rechnung_periode;</li>
<li>rechnung_anzahlpapier;</li>
<li>rechnung_permail;</li>
<li>usereditid;</li>
<li>useredittimestamp;</li>
<li>infoauftragserfassung;</li>
<li>mandatsreferenz;</li>
<li>glaeubigeridentnr;</li>
<li>kreditlimit;</li>
<li>tour;</li>
<li>abweichendeemailab;</li>
<li>filiale;</li>
<li>mandatsreferenzdatum;</li>
<li>mandatsreferenzaenderung;</li>
<li>sachkonto;</li>
</ul>

</td>

<td><b>Rechnung<br><br></b>
<ul>
<li>datum;</li>
<li>name;</li>
<li>ort;</li>
<li>plz;</li>
<li>strasse;</li>
<li>land;</li>
<li>soll;</li>
<li>kundennummer;</li>
<li>belegnr;</li>
<li>ustid;</li>
<li>gegenkonto;</li>
<li>waehrung;</li>
<li>auftrag_transaktionsnummer;</li>
<li>auftrag_internet;</li>
<li>steuersatz_normal;</li>
<li>steuersatz_normal_betrag;</li>
<li>steuersatz_ermaessigt;</li>
<li>steuersatz_ermaessigt_betrag;</li>
</ul>

</td>
<td><b>Zeiterfassung<br><br></b>
<ul>
<li>datum_von;</li>
<li>zeit_von;</li>
<li>datum_bis;</li>
<li>zeit_bis;</li>
<li>kennung;</li>
<li>taetigkeit;</li>
<li>details;</li>
</ul>

</td>
<td><b>Beleg Positionen <br>(AN,AB,RE,GS,LS)</b>
<ul>
<li>beleg_kundennummer;</li>
<li>beleg_name;</li>
<li>beleg_land;</li>
<li>beleg_belegnr;</li>
<li>beleg_datum;</li>
<li>beleg_status;</li>
<li>bp.nummer;</li>
<li>bp.bezeichnung;</li>
<li>bp.sort;</li>
<li>bp.preis; <i>Einzelpreis</i></li>
<li>bp.rabatt;</li>
<li>bp.menge;</li>
<li>bp.steuersatz;</li>
<li>bp.erloese;</li>
<li>bp.einkaufspreis;</li>
<li>bp.ekwaehrung;</li>
<li>preis_negativ; <i>(bei GS)</i></li>
<li>einheit;</li>
<li>projekt;</li>
</ul>

</td>




</tr></table><br><u>Pflichtfelder</u>&nbsp;(um bestehende Datens&auml;tze zu &auml;ndern muss mindestens dieser Wert angebenen werden)
</td></tr>
          <tr><td width="130">{|Interne Bemerkung|}:</td><td>[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]</td><td></td></tr>
<!--   <tr><td width="130">{|Letzter Export|}:</td><td><input type="text" name="letzterexport" size="40"</td></tr>
   <tr><td width="130">{|Von Mitarbeiter|}:</td><td><input type="text" name="mitarbeiterletzterexport" size="40"</td></tr>-->

</table></fieldset>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" name="submit"/>
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>


