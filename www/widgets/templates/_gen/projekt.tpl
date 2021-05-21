<form action="" method="post" name="eprooform">
      [FORMHANDLEREVENT]

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Grundeinstellungen|}</a></li>
        <li><a href="#tabs-3">{|Logistik / Versand|}</a></li>
        <li><a href="#tabs-4">{|Eigene Nummernkreise|}</a></li>
        <li><a href="#tabs-5">{|Steuer / W&auml;hrung|}</a></li>
        <li><a href="#tabs-6">{|POS Einstellungen|}</a></li>
        <li><a href="#tabs-7">{|Filiale|}</a></li>
    </ul>

<div id="tabs-1">
  [MESSAGE]
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-12 col-md-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|Allgemein|}</legend>
            <table border="0" width="100%">
              <tr><td width="300">{|Farbe|}:</td><td>[FARBE][MSGFARBE]</td></tr>
              <tr><td>{|Verkaufszahlen|}:</td><td>[VERKAUFSZAHLENDIAGRAM][MSGVERKAUFSZAHLENDIAGRAM]</td></tr>
              <tr><td width="300">&Ouml;ffentliches Projekt: </td><td>[OEFFENTLICH][MSGOEFFENTLICH]</td></tr>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-12 col-md-height">
        <div class="inside inside-full-height">
          <fieldset><legend>{|Buchhaltung|}</legend>
            <table border="0" width="100%">
              <tr><td width="300">{|Zahlungsmail|}:</td><td>[ZAHLUNGSERINNERUNG][MSGZAHLUNGSERINNERUNG]&nbsp;{|Optional Bedingungen|}:&nbsp;[ZAHLUNGSMAILBEDINUNGEN][MSGZAHLUNGSMAILBEDINUNGEN]</td></tr>
              <tr><td>{|Stornomail|}:</td><td>[STORNOMAIL][MSGSTORNOMAIL]</td></tr>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-12 col-md-height">
        <div class="inside inside-full-height">
        </div>
      </div>
    </div>
  </div>
 <br />
 <input type="submit" name="speichern" value="Speichern" style="float:right" />
</div>

<div id="tabs-3">
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-sm-height">
        <div class="inside_white inside-full-height">
          <fieldset class="favoriten-home aufgaben-home">
            <legend>Schnellauswahl Kommissonierverfahren</legend>
            <div class="content content-fav">
              <br />
              <div class="tabsbutton" style="float:left; width:100%">
                <a href="#" class="btnGreen" onclick="DialogKommissionierverfahren('1')">Einstufig: Einzelkommissionierung mit Paketmarke</a>&nbsp;
                <a href="#" class="btnGreen" onclick="DialogKommissionierverfahren('2')">Zweistufig: Pickliste mit Versandzentrum</a>&nbsp;
                <a href="#" class="btnGreen" onclick="DialogKommissionierverfahren('3')">Zweistufig: Multi-Order-Picking mit Versandzentrum</a>&nbsp;
                <a href="#" class="btnGreen" onclick="DialogKommissionierverfahren('4')">Fulfiller oder externer Produzent</a>&nbsp;
              </div>
              <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <br />
          </fieldset>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-sm-height">
        <div class="inside inside-full-height">
          [MESSAGE]
          <fieldset>
            <legend>{|Versandprozess und Kommissionierung|}</legend>
            <table border="0" width="100%">
  					  <tr>
                <td width="300">{|Kommissionierverfahren|}:</td><td>[KOMMISSIONIERVERFAHREN][MSGKOMMISSIONIERVERFAHREN]
                </td>
              </tr>
              <tr>
                <td>{|Lagerplatz auf Lieferschein ausblenden|}:</td><td>[LAGERPLATZLIEFERSCHEINAUSBLENDEN][MSGLAGERPLATZLIEFERSCHEINAUSBLENDEN]</td></tr>
                [VORKOMMISSIONIERUNGSLAUF]
              <tr><td>{|Kommissionierlauf gruppieren nach Lieferscheine|}:</td><td>[KOMMISSIONIERLAUFLIEFERSCHEIN][MSGKOMMISSIONIERLAUFLIEFERSCHEIN]</td></tr>
              [NACHKOMMISSIONIERUNGSLAUF]
              [VORMULTIORDERPICKING]
              <tr><td>{|Kommissionierlauf mit Multi-Order-Picking|}:</td><td>[MULTIORDERPICKING][MSGMULTIORDERPICKING]</td></tr>
              [NACHMULTIORDERPICKING]
              <tr>
                <td>{|Sortierung Kommissionierschein|}:</td>
                <td>
                  [ORDERPICKING_SORT][MSGORDERPICKING_SORT]
                </td>
              </tr>
              <tr>
                <td>{|Auto-Versand als Standard deaktivieren|}:</td>
                <td>[DEACTIVATEAUTOSHIPPING][MSGDEACTIVATEAUTOSHIPPING]</td>
              </tr>
            
              <!--<tr><td width="300">{|Automatisch Versand anlegen|}:</td><td>[AUTOVERSAND][MSGAUTOVERSAND]&nbsp;<i>Bei Auftr&auml;gen ist die Option "per Versandzentrum versenden" automatisch gesetzt.</i></td></tr>-->
						  <tr><td>{|Drucker Stufe (Kommissionierung)|}</td><td>[DRUCKERLOGISTIKSTUFE1][MSGDRUCKERLOGISTIKSTUFE1]&nbsp;<i>{|z.B. Lieferschein drucken|}</i></td></tr>
		          <tr><td>{|Drucker Stufe (Versand)|}</td><td>[DRUCKERLOGISTIKSTUFE2][MSGDRUCKERLOGISTIKSTUFE2]&nbsp;<i>{|Belege bei Versandstation|}</i></td></tr>
						  <tr><td>{|Lieferscheinposition: Etiketten|}</td><td>[ETIKETTEN_POSITIONEN][MSGETIKETTEN_POSITIONEN]&nbsp;<i></i></td></tr>
	            <tr><td>{|Lieferscheinposition: Etiketten-Drucker|}</td><td>[ETIKETTEN_DRUCKER][MSGETIKETTEN_DRUCKER]&nbsp;<i></i></td></tr>
	            <tr><td>{|Lieferscheinposition: Etiketten-Art|}</td><td>[ETIKETTEN_ART][MSGETIKETTEN_ART]&nbsp;<i></i></td></tr>
	            <tr><td>{|Lieferscheinposition: Etiketten-Sortierung|}</td><td>[ETIKETTEN_SORT][MSGETIKETTEN_SORT]&nbsp;<i></i></td></tr>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-sm-6 col-sm-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|Stufe 1 (Pick/Kommissionierung) |}</legend>
            <table border="0" width="100%" class="mkTable">
              <tr><td width="300"></td><td>Drucker</td><td width="30%">Anzahl Exemplare</td><td width="30%">E-Mail</td></tr>
              <tr><td width="300">{|Rechnung|}:</td><td>[AUTODRUCKRECHNUNGSTUFE1][MSGAUTODRUCKRECHNUNGSTUFE1]</td><td>[AUTODRUCKRECHNUNGSTUFE1MENGE][MSGAUTODRUCKRECHNUNGSTUFE1MENGE]</td><td>[AUTODRUCKRECHNUNGSTUFE1MAIL][MSGAUTODRUCKRECHNUNGSTUFE1MAIL]</td></tr>
              <tr><td>{|zus&auml;tzl. Ausdruck Rechnung bei Export|}:</td><td>[EXPORTDRUCKRECHNUNGSTUFE1][MSGEXPORTDRUCKRECHNUNGSTUFE1]</td><td>[EXPORTDRUCKRECHNUNGSTUFE1MENGE][MSGEXPORTDRUCKRECHNUNGSTUFE1MENGE]</td><td></td></tr>
              <tr><td width="300">{|Kommissionierschein|}:</td><td>[AUTODRUCKKOMMISSIONIERSCHEINSTUFE1][MSGAUTODRUCKKOMMISSIONIERSCHEINSTUFE1]</td><td>[AUTODRUCKKOMMISSIONIERSCHEINSTUFE1MENGE][MSGAUTODRUCKKOMMISSIONIERSCHEINSTUFE1MENGE]</td><td></td></tr>
              <tr><td>{|Kommissionierliste|}:</td><td>[KOMMISSIONIERLISTESTUFE1][MSGKOMMISSIONIERLISTESTUFE1]</td><td>[KOMMISSIONIERLISTESTUFE1MENGE][MSGKOMMISSIONIERLISTESTUFE1MENGE]</td><td></td></tr>
              <tr><td width="300">{|Rechnung erst im Versandprozess erzeugen|}:</td><td>[RECHNUNGERZEUGEN][MSGRECHNUNGERZEUGEN]&nbsp;</td><td></td><td></td></tr>
              <tr><td>{|Lieferschein drucken|}:</td><td>[LIEFERSCHEINEDRUCKEN][MSGLIEFERSCHEINEDRUCKEN]</td><td>[LIEFERSCHEINEDRUCKENMENGE][MSGLIEFERSCHEINEDRUCKENMENGE]</td><td></td></tr>
              <tr><td width="300">{|PDF Anhang|}:</td><td>[DRUCKANHANG][MSGDRUCKANHANG]</td><td></td><td>[MAILANHANG][MSGMAILANHANG]</td></tr>
              <tr><td>{|Auftrag drucken|}:</td><td>[AUFTRAGDRUCKEN][MSGAUFTRAGDRUCKEN]</td><td>[AUFTRAGDRUCKENMENGE][MSGAUFTRAGDRUCKENMENGE]</td><td></td></tr>
              <tr><td>{|Paketmarke drucken|}:</td><td>[PAKETMARKEDRUCKEN][MSGPAKETMARKEDRUCKEN]</td><td></td><td></td></tr>
            </table>
          </fieldset>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-sm-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|Stufe 2 (Pack) an Versandstation|}</legend>
            <table border="0" width="100%" class="mkTable">
              <tr><td width="300"></td><td>{|Drucker|}</td><td width="30%">{|Anzahl Exemplare|}</td><td width="30%">{|E-Mail|}</td></tr>
              <tr><td width="300">{|Versandbest&auml;tigung/Tracking|}:</td><td></td><td></td><td>[AUTOMAILVERSANDBESTAETIGUNG][MSGAUTOMAILVERSANDBESTAETIGUNG]</td></tr>
              <tr><td width="300">{|Rechnung|}:</td><td>[AUTODRUCKRECHNUNG][MSGAUTODRUCKRECHNUNG]</td><td>[AUTODRUCKRECHNUNGMENGE][MSGAUTODRUCKRECHNUNGMENGE]</td><td>[AUTOMAILRECHNUNG][MSGAUTOMAILRECHNUNG]</td></tr>
              <tr><td>{|zus&auml;tzl. Ausdruck Rechnung bei Export|}:</td><td>[EXPORTDRUCKRECHNUNG][MSGEXPORTDRUCKRECHNUNG]</td><td>[EXPORTDRUCKRECHNUNGMENGE][MSGEXPORTDRUCKRECHNUNGMENGE]</td><td></td></tr>
              <tr><td>{|Auto. Proformarechnung bei Export |}:</td><td>[PRINT_PROFORMAINVOICE][MSGPRINT_PROFORMAINVOICE]</td><td>[PROFORMAINVOICE_AMOUNT][MSGPROFORMAINVOICE_AMOUNT]</td><td></td></tr>
              <tr><td width="300">{|Lieferschein|}:</td><td>[AUTODRUCKLIEFERSCHEIN][MSGAUTODRUCKLIEFERSCHEIN]</td><td>[AUTODRUCKLIEFERSCHEINMENGE][MSGAUTODRUCKLIEFERSCHEINMENGE]</td><td>[AUTOMAILLIEFERSCHEIN][MSGAUTOMAILLIEFERSCHEIN]</td></tr>
              <tr><td width="300">{|PDF Anhang|}:</td><td>[AUTODRUCKANHANG][MSGAUTODRUCKANHANG]</td><td></td><td>[AUTOMAILANHANG][MSGAUTOMAILANHANG]</td></tr>
              <tr><td>{|Paketmarke automatisch drucken|}:</td><td>[PAKETMARKEAUTODRUCKEN][MSGPAKETMARKEAUTODRUCKEN]</td><td></td></tr>
              <tr><td width="300">{|Rechnung Doppel bei Selbstabholer u. Bar|}:</td><td>[AUTODRUCKRECHNUNGDOPPEL][MSGAUTODRUCKRECHNUNGDOPPEL]</td><td></td><td></td></tr>
              <tr><td width="300">{|Drucken nach Trackingnummer erfassen|}:</td><td>[DRUCKENNACHTRACKING][MSGDRUCKENNACHTRACKING]</td></tr>
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
          <fieldset>
            <legend>{|Optionen|}</legend>
            <table border="0" width="100%">
              <!--<tr><td width="300">{|Wechsel auf einstufige Kommissionierung|}:</td><td>[WECHSELAUFEINSTUFIG][MSGWECHSELAUFEINSTUFIG]&nbsp; <i>Wenn mehr als x Artikel in einem Auftrag sind.</i></td></tr>-->
              <tr><td>{|Auto-Reservierung im Lager|}:</td><td>[RESERVIERUNG][MSGRESERVIERUNG]</td></tr>
              <tr><td></td><td></td></tr>
              <tr><td>{|Projektlager|}:</td><td>[PROJEKTLAGER][MSGPROJEKTLAGER]</td></tr>
              <tr><td>{|Bevorzugtes Lager|}:</td><td>[STANDARDLAGER][MSGSTANDARDLAGER]</td></tr>
              [VORPRODUKTIONVORHANDEN]<tr><td>{|Bevorzugtes Lager f&uuml;r Produktionen|}:</td><td>[STANDARDLAGERPRODUKTION][MSGSTANDARDLAGERPRODUKTION]</td></tr>[NACHPRODUKTIONVORHANDEN]
              <tr><td></td><td></td></tr>
              <tr><td>{|Versandzentrum 2-Schritte|}:</td><td>[VERSANDZWEIGETEILT][MSGVERSANDZWEIGETEILT]</td></tr>
              <tr><td>{|Versandzentrum Artikelname aus Stammdaten|}:</td><td>[VERSANDARTIKELNAMEAUSSTAMMDATEN][MSGVERSANDARTIKELNAMEAUSSTAMMDATEN]</td></tr>
              <tr><td>{|Versandzentrum Lagerplatz anzeigen|}:</td><td>[VERSANDLAGERPLATZANZEIGEN][MSGVERSANDLAGERPLATZANZEIGEN]</td></tr>
              <tr><td>{|Versandzentrum Keine automatische Weiterleitung zum Frankieren|}:</td><td>[MANUALTRACKING][MSGMANUALTRACKING]</td></tr>
              <tr><td></td><td></td></tr>
              <tr><td>{|Chargen im Versandzentrum erfassen|}:</td><td>[CHARGENERFASSEN][MSGCHARGENERFASSEN]</td></tr>
              <tr><td>{|MHD im Versandzentrum erfassen|}:</td><td>[MHDERFASSEN][MSGMHDERFASSEN]</td></tr>
              <tr><td>{|Seriennummern im Versandzentrum erfassen|}:</td><td>[SERIENNUMMERNERFASSEN][MSGSERIENNUMMERNERFASSEN]</td></tr>
              <tr><td></td><td></td></tr>
              <tr><td>{|Alle Chargen/MHD durchsuchbar machen|}:</td><td>[ALLECHARGENMHD][MSGALLECHARGENMHD]</td></tr>
              <tr><td><label for="autobestbeforebatch">{|MHD/Charge automatisch &uuml;bernehmen falls eindeutig|}:</label></td><td>[AUTOBESTBEFOREBATCH][MSGAUTOBESTBEFOREBATCH]</td></tr>
              <tr><td><label for="allwaysautobestbeforebatch">{|MHD/Charge immer automatisch vorausw&auml;hlen|}:</label></td><td>[ALLWAYSAUTOBESTBEFOREBATCH][MSGALLWAYSAUTOBESTBEFOREBATCH]</td></tr>
              <tr><td></td><td></td></tr>
              <tr><td>{|EAN scannen erlauben|}:</td><td>[EANHERSTELLERSCANERLAUBEN][MSGEANHERSTELLERSCANERLAUBEN]</td></tr>
              <tr><td>{|EAN, Hersteller- oder Artikel-Nr. auf Projekt beschr&auml;nken|}:</td><td>[EANHERSTELLERSCAN][MSGEANHERSTELLERSCAN]</td></tr>
              <tr><td>{|Shop-Fremdnummern scannen erlauben|}:</td><td>[FREMDNUMMERSCANERLAUBEN][MSGFREMDNUMMERSCANERLAUBEN]</td></tr>
            </table>
          </fieldset>
        </div>
      </div>
      <div class="col-xs-12 col-md-6 col-md-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend></legend>
            <table border="0" width="100%">
              <tr><td width="400">{|Porto-Check|}:</td><td>[PORTOCHECK][MSGPORTOCHECK]</td></tr>
              <tr><td>{|Nachnahme-Check|}:</td><td>[NACHNAHMECHECK][MSGNACHNAHMECHECK]</td></tr>
              <tr><td>{|Folgebest&auml;tigung|}:</td><td>[FOLGEBESTAETIGUNG][MSGFOLGEBESTAETIGUNG]</td></tr>
              <tr><td>{|Selbstabholer Mail|}:</td><td>[SELBSTABHOLERMAIL][MSGSELBSTABHOLERMAIL]</td></tr>
              <tr><td>{|Anzahl Differenz Tage Auslieferung|}:</td><td>[DIFFERENZ_AUSLIEFERUNG_TAGE][MSGDIFFERENZ_AUSLIEFERUNG_TAGE]</td></tr>
              <tr><td>{|Automatisch Proformarechnung bei Export anlegen|}:</td><td>[CREATE_PROFORMAINVOICE][MSGCREATE_PROFORMAINVOICE]</td></tr>
              <tr><td></td><td></td></tr>
              <tr><td>{|Auftrags-Check installiert|}:</td><td>[CHECKOK][MSGCHECKOK]&nbsp;Funktion:&nbsp;[CHECKNAME][MSGCHECKNAME]</td></tr>
              <tr><td>{|Automatisch St&uuml;cklisten explodieren|}:</td><td>[AUTOSTUECKLISTENANPASSUNG][MSGAUTOSTUECKLISTENANPASSUNG]</td></tr>
              <tr><td>{|Kundenfreigabe l&ouml;schen|}:</td><td>[KUNDENFREIGABE_LOESCHEN][MSGKUNDENFREIGABE_LOESCHEN]</td></tr>
              <tr><td>{|Nur Lagerartikel &uuml;bernehmen|}:</td><td>[NURLAGERARTIKEL][MSGNURLAGERARTIKEL]</td></tr>
              <tr><td>{|Online-Shop Projekt|}: </td><td>[SHOPZWANGSPROJEKT][MSGSHOPZWANGSPROJEKT]</td></tr>
              <tr><td></td><td></td></tr>
              <tr><td>{|Produktion: Lagerplätze nach FIFO anzeigen|}:</td><td>[PRODUCTION_SHOW_ONLY_NEEDED_STORAGES][MSGPRODUCTION_SHOW_ONLY_NEEDED_STORAGES]</td></tr>
              <tr><td>{|Produktion: Jede Arbeitsanweisung auf eine extra Seite|}:</td><td>[PRODUKTION_EXTRA_SEITEN][MSGPRODUKTION_EXTRA_SEITEN]</td></tr>
              <tr><td>{|Schnellproduktion: Automatische Freigabe von Auftr&auml;gen aktivieren|}:</td><td>[PRODUKTIONAUFTRAGAUTOMATISCHFREIGEBEN][MSGPRODUKTIONAUFTRAGAUTOMATISCHFREIGEBEN]</td></tr>
              <!--<tr><td>{|Projekt&uuml;bergreifende Kommissionierung|}:</td><td>[PROJEKTUEBERGREIFENDKOMMISIONIEREN][MSGPROJEKTUEBERGREIFENDKOMMISIONIEREN]</td></tr>-->
              <!--<tr><td>{|Veraltete Versand- und Zahlungs-Optionen anzeigen|}:</td><td>[VERALTET][MSGVERALTET]&nbsp;<i>(Nur für Einstellungen bis Version 15.3. Bitte nicht mehr verwenden.)</i></td></tr>-->
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-sm-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>E-Mail Versand Einstellungen (falls abweichend von Daten aus Firmeneinstellungen)</legend>
          </fieldset>
        </div>
      </div>
    </div>
  </div>

  <!-- speichern -->
  <table width="100%">
    <tr>
      <td width="" valign="" height="" bgcolor="" align="right">
        <input type="submit" name="speichern" value="Speichern" onclick="this.form.action += '#tabs-3';" />
      </td>
    </tr>
  </table>

</div>

<div id="tabs-4">
 [MESSAGE]
 <div class="row">
   <div class="row-height">
     <div class="col-xs-12 col-md-12 col-md-height">
       <div class="inside inside-full-height">
         <fieldset>
           <legend>{|Nummernkreis|}</legend>
           <table border="0" width="100%">
             <tr><td width="300">{|Eigene Nummernkreise|}:</td><td>[EIGENERNUMMERNKREIS][MSGEIGENERNUMMERNKREIS]</td></tr>
             [STARTNUMMER]<tr><td width="200">N&auml;chste Angebotsnummer:</td><td>[NEXT_ANGEBOT][MSGNEXT_ANGEBOT]&nbsp;</td></tr>
             <tr><td>N&auml;chste Auftragsnummer:</td><td>[NEXT_AUFTRAG][MSGNEXT_AUFTRAG]&nbsp;</td></tr>
             <tr><td>N&auml;chste Lieferscheinnummer:</td><td>[NEXT_LIEFERSCHEIN][MSGNEXT_LIEFERSCHEIN]&nbsp;</td></tr>
             <tr><td>N&auml;chste Retourennummer:</td><td>[NEXT_RETOURE][MSGNEXT_RETOURE]&nbsp;</td></tr>
             <tr><td>N&auml;chste Rechnungsnummer:</td><td>[NEXT_RECHNUNG][MSGNEXT_RECHNUNG]&nbsp;</td></tr>
             <tr><td>N&auml;chste Gutschriftnummer:</td><td>[NEXT_GUTSCHRIFT][MSGNEXT_GUTSCHRIFT]&nbsp;</td></tr>
             <tr><td>N&auml;chste Bestellungsnummer:</td><td>[NEXT_BESTELLUNG][MSGNEXT_BESTELLUNG]&nbsp;</td></tr>
             <tr><td>N&auml;chste Arbeitsnachweisnummer:</td><td>[NEXT_ARBEITSNACHWEIS][MSGNEXT_ARBEITSNACHWEIS]&nbsp;</td></tr>
             <tr><td>N&auml;chste Reisekostennummer:</td><td>[NEXT_REISEKOSTEN][MSGNEXT_REISEKOSTEN]&nbsp;</td></tr>
             <tr><td>N&auml;chste Produktionnummer:</td><td>[NEXT_PRODUKTION][MSGNEXT_PRODUKTION]&nbsp;</td></tr>
             <tr><td>N&auml;chste Anfragenummer:</td><td>[NEXT_ANFRAGE][MSGNEXT_ANFRAGE]&nbsp;</td></tr>
             <tr><td>N&auml;chste Proformarechnungsnummer:</td><td>[NEXT_PROFORMARECHNUNG][MSGNEXT_PROFORMARECHNUNG]&nbsp;</td></tr>
             <tr><td>Nächste Verbindlichkeitsnummer:</td><td>[NEXT_VERBINDLICHKEIT][MSGNEXT_VERBINDLICHKEIT] </td></tr>
             <tr><td>Nächste Warenbuchungsbelegnummer:</td><td>[NEXT_GOODSPOSTINGDOCUMENT][MSGNEXT_GOODSPOSTINGDOCUMENT] </td></tr>
             <tr><td>N&auml;chste Kundennummer:</td><td>[NEXT_KUNDENNUMMER][MSGNEXT_KUNDENNUMMER]&nbsp;</td></tr>
             <tr><td>N&auml;chste Lieferantenummer:</td><td>[NEXT_LIEFERANTENNUMMER][MSGNEXT_LIEFERANTENNUMMER]&nbsp;</td></tr>
             <tr><td>N&auml;chste Mitarbeiternummer:</td><td>[NEXT_MITARBEITERNUMMER][MSGNEXT_MITARBEITERNUMMER]&nbsp;</td></tr>
             <tr><td>N&auml;chste Artikelnummer:</td><td>[NEXT_ARTIKELNUMMER][MSGNEXT_ARTIKELNUMMER]&nbsp;</td></tr>
             [ENDENUMMER]
           </table>
         </fieldset>
       </div>
     </div>
   </div>
 </div>
 <br />
 <input type="submit" name="speichern" value="Speichern" onclick="this.form.action += '#tabs-4';" style="float:right"/>
</div>

<div id="tabs-5">
 <div class="row">
   <div class="row-height">
     <div class="col-xs-12 col-md-12 col-md-height">
       <div class="inside inside-full-height">
         <fieldset>
           <legend>{|Steuer / Standardw&auml;hrung|}</legend>
           <table border="0" width="100%">
             <tr><td>{|Steuersatz (normal)|}:</td><td>[STEUERSATZ_NORMAL][MSGSTEUERSATZ_NORMAL]</td></tr>
             <tr><td>{|Steuersatz (erm&auml;&szlig;igt)|}:</td><td>[STEUERSATZ_ERMAESSIGT][MSGSTEUERSATZ_ERMAESSIGT]</td></tr>
             <tr><td>{|Weiterf&uuml;hren von Belegen|}:</td><td>
                     [TAXFROMDOCTYPESETTINGS][MSGTAXFROMDOCTYPESETTINGS]
                  </td>
             </tr>
             <tr><td>Standard Zahlungsweise Kunde:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]</td></tr>
             <tr><td>Standard Zahlungsweise Lieferant:</td><td>[ZAHLUNGSWEISELIEFERANT][MSGZAHLUNGSWEISELIEFERANT]</td></tr>
             <tr><td>Standard Versandart:</td><td>[VERSANDART][MSGVERSANDART]</td></tr>
             <tr><td>{|W&auml;hrung|}:</td><td>[WAEHRUNG][MSGWAEHRUNG]&nbsp;</td></tr>
             <tr><td width="300">{|USt.-ID|}:</td><td>[STEUERNUMMER][MSGSTEUERNUMMER]&nbsp;</td></tr>
             <tr><td>{|Mahnwesen aktiv|}:</td><td>[MAHNWESEN][MSGMAHNWESEN]</td></tr>
             <tr><td>{|Versand Mahnungen über E-Mail-Account|}:</td><td>[MAHNWESEN_ABWEICHENDER_VERSENDER][MSGMAHNWESEN_ABWEICHENDER_VERSENDER]</td></tr>
             <tr><td>{|Eigene Steuers&auml;tze verwenden|}:</td><td>[EIGENESTEUER][MSGEIGENESTEUER]</td></tr>
             <tr><td>{|Anzeige Steuer auf Belege|}:</td><td>
                     [ANZEIGESTEUERBELEGE][MSGANZEIGESTEUERBELEGE]
                 </td>
             </tr>
             <tr><td>{|Einstellung auch f&uuml;r Bestellungen verwenden|}:</td><td>[ANZEIGESTEUERBELEGEBESTELLUNG][MSGANZEIGESTEUERBELEGEBESTELLUNG]</td></tr>
             <tr><td>{|Bruttobetrag Berechnung|}:</td><td>
                     [PREISBERECHNUNG][MSGPREISBERECHNUNG]
                 </td>
             </tr>
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
         <fieldset>
           <legend>{|Finanzbuchhaltung Export Kontenrahmen|}</legend>
           <table border="0" width="100%">
             <tr>
               <td width="300"></td><td>Erl&ouml;se</td>
             </tr>
             <tr>
               <td width="300">Inland (normal):</td><td>[STEUER_ERLOESE_INLAND_NORMAL][MSGSTEUER_ERLOESE_INLAND_NORMAL]</td>
             </tr>
             <tr>
               <td width="300">Inland (erm&auml;&szlig;igt):</td><td>[STEUER_ERLOESE_INLAND_ERMAESSIGT][MSGSTEUER_ERLOESE_INLAND_ERMAESSIGT]</td>
             <tr>
               <td width="300">Inland (steuerfrei):</td><td>[STEUER_ERLOESE_INLAND_NICHTSTEUERBAR][MSGSTEUER_ERLOESE_INLAND_NICHTSTEUERBAR]</td>
             </tr>
             <tr>
               <td width="300">Innergemeinschaftlich EU:</td><td>[STEUER_ERLOESE_INLAND_INNERGEMEINSCHAFTLICH][MSGSTEUER_ERLOESE_INLAND_INNERGEMEINSCHAFTLICH]</td>
             </tr>
             <tr>
               <td width="300">EU (normal):</td><td>[STEUER_ERLOESE_INLAND_EUNORMAL][MSGSTEUER_ERLOESE_INLAND_EUNORMAL]</td>
             </tr>
             <tr>
               <td width="300">EU (erm&auml;&szlig;igt):</td><td>[STEUER_ERLOESE_INLAND_EUERMAESSIGT][MSGSTEUER_ERLOESE_INLAND_EUERMAESSIGT]</td>
             </tr>
             <tr>
               <td width="300">Export:</td><td>[STEUER_ERLOESE_INLAND_EXPORT][MSGSTEUER_ERLOESE_INLAND_EXPORT]</td>
             </tr>
           </table>
         </fieldset>
       </div>
     </div>
     <div class="col-xs-12 col-md-6 col-md-height">
       <div class="inside inside-full-height">
         <fieldset>
           <table>
             <tr>
               <td width="300"></td><td>Aufwendungen</td>
             </tr>
             <tr>
               <td width="300">Inland (normal):</td><td>[STEUER_AUFWENDUNG_INLAND_NORMAL][MSGSTEUER_AUFWENDUNG_INLAND_NORMAL]</td>
             </tr>
             <tr>
               <td width="300">Inland (erm&auml;&szlig;igt):</td><td>[STEUER_AUFWENDUNG_INLAND_ERMAESSIGT][MSGSTEUER_AUFWENDUNG_INLAND_ERMAESSIGT]</td>
             </tr>
             <tr>
               <td width="300">Inland (steuefrei):</td><td>[STEUER_AUFWENDUNG_INLAND_NICHTSTEUERBAR][MSGSTEUER_AUFWENDUNG_INLAND_NICHTSTEUERBAR]</td>
             </tr>
             <tr>
               <td width="300">Innergemeinschaftlich EU:</td><td>[STEUER_AUFWENDUNG_INLAND_INNERGEMEINSCHAFTLICH][MSGSTEUER_AUFWENDUNG_INLAND_INNERGEMEINSCHAFTLICH]</td>
             </tr>
             <tr>
               <td width="300">EU (normal):</td><td>[STEUER_AUFWENDUNG_INLAND_EUNORMAL][MSGSTEUER_AUFWENDUNG_INLAND_EUNORMAL]</td>
             </tr>
             <tr>
              <td width="300">EU (erm&auml;&szlig;igt):</td><td>[STEUER_AUFWENDUNG_INLAND_EUERMAESSIGT][MSGSTEUER_AUFWENDUNG_INLAND_EUERMAESSIGT]</td>
             </tr>
             <tr>
               <td width="300">Import:</td><td>[STEUER_AUFWENDUNG_INLAND_IMPORT][MSGSTEUER_AUFWENDUNG_INLAND_IMPORT]</td>
             </tr>
           </table>
         </fieldset>
       </div>
     </div>
   </div>
 </div>
 <br />
 <input type="submit" name="speichern" value="Speichern" onclick="this.form.action += '#tabs-5';" style="float:right"/>
</div>


<div id="tabs-6">
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-12 col-md-height">
        <div class="inside inside-full-height">
          [POSINFOBOX]
          <fieldset>
            <legend>{|POS Einstellungen|}</legend>
            <table border="0" width="100%">
              <tr>
                <td>{|Lagerprozess|}:</td><td>[KASSE_LAGERPROZESS][MSGKASSE_LAGERPROZESS]
                </td>
              </tr>
              <tr><td>{|Adapterbox für POS|}:</td><td>[KASSE_BONDRUCKER][MSGKASSE_BONDRUCKER]&nbsp;</td></tr>
              <tr><td>{|POS Lager f&uuml;r den Verkauf|}:</td><td>[KASSE_LAGER][MSGKASSE_LAGER]</td></tr>
              <tr><td>{|Preisgruppe bevorzugt|}:</td><td>[KASSE_PREISGRUPPE][MSGKASSE_PREISGRUPPE]</td></tr>
              <tr><td>{|Kasse f&uuml;r Bar|}:</td><td>[KASSE_KONTO][MSGKASSE_KONTO]</td></tr>
              <tr><td>{|Laufkundschaft|}:</td><td>[KASSE_LAUFKUNDSCHAFT][MSGKASSE_LAUFKUNDSCHAFT]</td></tr>
              <tr><td width="300">{|Kunden aus fremden Nummernkreisen abwickeln|}:</td><td>[POS_KUNDENALLEPROJEKTE][MSGPOS_KUNDENALLEPROJEKTE]</td></tr>
              <tr><td width="300">{|Nur Artikel aus Projekt erlauben|}:</td><td>[POS_ARTIKELNURAUSPROJEKT][MSGPOS_ARTIKELNURAUSPROJEKT]</td></tr>
              <tr><td>{|Gleiche Artikel summieren bei nacheinander Eingabe|}:</td><td>[POS_SUMARTICLES][MSGPOS_SUMARTICLES]</td></tr>
              <tr><td>{|Artikel f&uuml;r EUR-Rabatt|}:</td><td>[KASSE_RABATT_ARTIKEL][MSGKASSE_RABATT_ARTIKEL]</td></tr>
              <tr><td width="300">{|Lieferschein erstellen|}:</td><td>[KASSE_LIEFERSCHEIN_ANLEGEN][MSGKASSE_LIEFERSCHEIN_ANLEGEN]</td></tr>
              <tr><td>{|Kasse Beschriftung 1|}:</td><td>[KASSE_TEXT_BEMERKUNG][MSGKASSE_TEXT_BEMERKUNG]&nbsp;<i>z.B. Interne Bemerkung (Feld "Interne Bemerkung" im Auftrag und Rechnung)</i></td></tr>
              <tr><td>{|Kasse Beschriftung 2|}:</td><td>[KASSE_TEXT_FREITEXT][MSGKASSE_TEXT_FREITEXT]&nbsp;<i>z.B. Text auf Beleg (Feld "Freitext" im Auftrag und Rechnung)</i></td></tr>
              <tr><td width="300">{|Artikelbeschreibung in Belege &uuml;bernehmen|}:</td><td>[POS_ARTIKELTEXTEUEBERNEHMEN][MSGPOS_ARTIKELTEXTEUEBERNEHMEN]</td></tr>
              <tr><td width="300">{|POS Anzeige in netto|}:</td><td>[POS_ANZEIGENETTO][MSGPOS_ANZEIGENETTO]</td></tr>
              <tr><td width="300">{|Mehrere Auftr&auml;ge pro Kassierer|}:</td><td>[POS_ZWISCHENSPEICHERN][MSGPOS_ZWISCHENSPEICHERN]</td></tr>
              <tr><td width="300">{|Detaillierte Ansicht im Abschluss-PDF|}:</td><td>[POS_GROSSEANSICHT][MSGPOS_GROSSEANSICHT]</td></tr>
              <tr><td width="300">{|Einzelbuchungen ausblenden|}:</td><td>[POS_DISABLE_SINGLE_ENTRIES][MSGPOS_DISABLE_SINGLE_ENTRIES]</td></tr>
              <tr><td width="300">{|Monatsberichte ohne Einzeltage|}:</td><td>[POS_DISABLE_SINGLE_DAY][MSGPOS_DISABLE_SINGLE_DAY]</td></tr>
              <tr><td width="300">{|Z&auml;hlprotokoll ausblenden|}:</td><td>[POS_DISABLE_COUNTING_PROTOCOL][MSGPOS_DISABLE_COUNTING_PROTOCOL]</td></tr>
              <tr><td width="300">{|Unterschriftblock ausblenden|}:</td><td>[POS_DISABLE_SIGNATURE][MSGPOS_DISABLE_SIGNATURE]</td></tr>
            </table>
	        </fieldset>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-12 col-md-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|Buttons|}</legend>
            <table>
              <tr><td width="300">{|Zahlungsweise Bar|}:</td><td>[KASSE_ZAHLUNG_BAR][MSGKASSE_ZAHLUNG_BAR]</td><td>[KASSE_ZAHLUNG_BAR_BEZAHLT][MSGKASSE_ZAHLUNG_BAR_BEZAHLT]&nbsp;Rechnung als bezahlt markieren</td></tr>
              <tr><td>{|Zahlungsweise EC|}:</td><td>[KASSE_ZAHLUNG_EC][MSGKASSE_ZAHLUNG_EC]</td><td>[KASSE_ZAHLUNG_EC_BEZAHLT][MSGKASSE_ZAHLUNG_EC_BEZAHLT]&nbsp;Rechnung als bezahlt markieren</td></tr>
              <tr><td>{|Zahlungsweise Kreditkarte|}:</td><td>[KASSE_ZAHLUNG_KREDITKARTE][MSGKASSE_ZAHLUNG_KREDITKARTE]</td><td>[KASSE_ZAHLUNG_KREDITKARTE_BEZAHLT][MSGKASSE_ZAHLUNG_KREDITKARTE_BEZAHLT]&nbsp;Rechnung als bezahlt markieren</td></tr>
              <tr><td>{|Zahlungsweise &Uuml;berweisung|}:</td><td>[KASSE_ZAHLUNG_UEBERWEISUNG][MSGKASSE_ZAHLUNG_UEBERWEISUNG]</td><td>[KASSE_ZAHLUNG_UEBERWEISUNG_BEZAHLT][MSGKASSE_ZAHLUNG_UEBERWEISUNG_BEZAHLT]&nbsp;Rechnung als bezahlt markieren</td></tr>
              <tr><td>{|Beleg Rechnung|}:</td><td>[KASSE_EXTRA_RECHNUNG][MSGKASSE_EXTRA_RECHNUNG]</td></tr>
              <tr><td>{|Beleg Quittung|}:</td><td>[KASSE_EXTRA_QUITTUNG][MSGKASSE_EXTRA_QUITTUNG]</td></tr>
              <!--  <tr><td>{|Kein Beleg|}:</td><td>[KASSE_EXTRA_KEINBELEG][MSGKASSE_EXTRA_KEINBELEG]</td></tr>-->
              <tr><td>Rabatt in %:</td><td>[KASSE_EXTRA_RABATT_PROZENT][MSGKASSE_EXTRA_RABATT_PROZENT]</td></tr>
              <tr><td>{|Rabatt in EUR|}:</td><td>[KASSE_EXTRA_RABATT_EURO][MSGKASSE_EXTRA_RABATT_EURO]</td></tr>
              <tr><td>{|Entnahme|}:</td><td>[KASSE_BUTTON_ENTNAHME][MSGKASSE_BUTTON_ENTNAHME]</td></tr>
              <tr><td>{|Einlage|}:</td><td>[KASSE_BUTTON_EINLAGE][MSGKASSE_BUTTON_EINLAGE]</td></tr>
              <tr><td>{|Trinkgeld|}:</td><td>[KASSE_BUTTON_TRINKGELD][MSGKASSE_BUTTON_TRINKGELD]</td></tr>
              <tr><td>{|Trinkgeld bei EC und Kreditkarte|}:</td><td>[KASSE_BUTTON_TRINKGELDECKREDIT][MSGKASSE_BUTTON_TRINKGELDECKREDIT]</td></tr>
              <tr><td>{|Lade &ouml;ffnen|}:</td><td>[KASSE_BUTTON_SCHUBLADE][MSGKASSE_BUTTON_SCHUBLADE]</td></tr>
              <tr><td>{|Belege laden|}:</td><td>[KASSE_BUTTON_BELEGLADEN][MSGKASSE_BUTTON_BELEGLADEN]</td></tr>
              <tr><td>{|Storno|}:</td><td>[KASSE_BUTTON_STORNO][MSGKASSE_BUTTON_STORNO]</td></tr>
              <tr><td>{|Automatisches Ausloggen|}:</td><td>[KASSE_AUTOLOGOUT][MSGKASSE_AUTOLOGOUT]</td></tr>
              <tr><td>{|Automatisches Ausloggen nach Abschluss Zahlung|}:</td><td>[KASSE_AUTOLOGOUT_ABSCHLUSS][MSGKASSE_AUTOLOGOUT_ABSCHLUSS]</td></tr>
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
          <fieldset>
            <legend>{|Weitere Einstellungen|}</legend>
            <table border="0" width="100%">
              <tr><td width="300">{|Erweiterte Adressfelder|}:</td><td>[KASSE_ADRESSE_ERWEITERT][MSGKASSE_ADRESSE_ERWEITERT]</td></tr>
              <tr><td>{|Zwangsauswahl Zahlweise|}:</td><td>[KASSE_ZAHLUNGSAUSWAHL_ZWANG][MSGKASSE_ZAHLUNGSAUSWAHL_ZWANG]</td></tr>
              <tr><td>{|Vorauswahl Anrede|}:</td><td>[KASSE_VORAUSWAHL_ANREDE][MSGKASSE_VORAUSWAHL_ANREDE]</td></tr>
              <!--<tr><td>{|Erweiterte Lagerabfrage|}:</td><td>[KASSE_ERWEITERTE_LAGERABFRAGE][MSGKASSE_ERWEITERTE_LAGERABFRAGE]</td></tr>-->
            </table>
	        </fieldset>
        </div>
      </div>
      <div class="col-xs-12 col-md-6 col-md-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|Drucker Einstellungen|}</legend>
            <table border="0" width="100%">
              <tr><td width="300">{|Belegausgabe nach Abschluss|}:</td><td>[KASSE_BELEGAUSGABE][MSGKASSE_BELEGAUSGABE]</td>
              </tr>
              <tr><td>Drucker</td><td>[KASSE_DRUCKER][MSGKASSE_DRUCKER]&nbsp;</td></tr>
              <tr><td>{|Anzahl Lieferschein|}:</td><td>[KASSE_LIEFERSCHEIN][MSGKASSE_LIEFERSCHEIN]</td></tr>
              <tr><td>{|Anzahl Rechnung|}:</td><td>[KASSE_RECHNUNG][MSGKASSE_RECHNUNG]</td></tr>
              <tr><td>{|Anzahl Gutschrift|}:</td><td>[KASSE_GUTSCHRIFT][MSGKASSE_GUTSCHRIFT]</td></tr>
              <tr><td>{|Anzahl Lieferschein-Doppel|}:</td><td>[KASSE_LIEFERSCHEIN_DOPPEL][MSGKASSE_LIEFERSCHEIN_DOPPEL]</td></tr>
              <tr><td>{|Bei Rechnungsausdruck immer auch Quittung (Kassenbon) ausdrucken|}:</td><td>[KASSE_QUITTUNG_RECHNUNG][MSGKASSE_QUITTUNG_RECHNUNG]</td></tr>
              <tr><td>{|QR-Code auf Quittung (Kassenbon) drucken|}:</td><td>[KASSE_PRINT_QR][MSGKASSE_PRINT_QR]</td></tr>
              <!--<tr><td>{|Rechnung per Mail|}:</td><td>[KASSE_RECHNUNGPERMAIL][MSGKASSE_RECHNUNGPERMAIL]</td></tr>-->
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-12 col-md-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|Bondrucker|}</legend>
            <table border="0" width="100%">
              <tr><td width="300">{|aktiv|}:</td><td>[KASSE_BONDRUCKER_AKTIV][MSGKASSE_BONDRUCKER_AKTIV]</td></tr>
              <tr><td>{|Anzahl Ausdrucke|}:</td><td>[KASSE_BONDRUCKER_ANZAHL][MSGKASSE_BONDRUCKER_ANZAHL]</td></tr>
              <tr><td>{|Zeile 1|}:</td><td>[KASSE_BON_ZEILE1][MSGKASSE_BON_ZEILE1]</td></tr>
              <tr><td>{|Zeile 2|}:</td><td>[KASSE_BON_ZEILE2][MSGKASSE_BON_ZEILE2]</td></tr>
              <tr><td>{|Zeile 3|}:</td><td>[KASSE_BON_ZEILE3][MSGKASSE_BON_ZEILE3]</td></tr>
              <tr><td>{|Freifeld aus Artikel auf Bon|}:</td><td>[KASSE_BONDRUCKER_FREIFELD][MSGKASSE_BONDRUCKER_FREIFELD]&nbsp;</td>
              </tr>
              <tr><td>{|Belegnummer als QR-Code|}:</td><td>[KASSE_BONDRUCKER_QRCODE][MSGKASSE_BONDRUCKER_QRCODE]</td></tr>
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
          <fieldset>
            <legend>{|ZVT (über TCP/IP)|}</legend>
            <table border="0" width="100%">
              <tr><td width="300">{|IP-Adresse|}:</td><td>[ZVT100URL][MSGZVT100URL]</td></tr>
              <tr><td>{|Port|}:</td><td>[ZVT100PORT][MSGZVT100PORT]</td></tr>
            </table>
          </fieldset>
        </div>
      </div>
      <div class="col-xs-12 col-md-6 col-md-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|RKSV Einstellungen|}</legend>
            <table border="0" width="100%">
              <tr><td width="300">{|aktiv|}:</td><td>[KASSE_RKSV_AKTIV][MSGKASSE_RKSV_AKTIV]</td></tr>
              <!--<tr><td>{|RKSV-Tool|}:</td><td>[KASSE_RKSV_TOOL][MSGKASSE_RKSV_TOOL]&nbsp;</td></tr>-->
              <tr><td>{|Kartenleser|}:</td><td>[KASSE_RKSV_KARTENLESER][MSGKASSE_RKSV_KARTENLESER]</td></tr>
              <tr><td>{|Seriennummer der Karte|}:</td><td>[KASSE_RKSV_KARTESERIENNUMMER][MSGKASSE_RKSV_KARTESERIENNUMMER]</td></tr>
              <tr><td>{|PIN der Karte|}:</td><td>[KASSE_RKSV_KARTEPIN][MSGKASSE_RKSV_KARTEPIN]</td></tr>
              <tr><td>{|Kassen-ID|}:</td><td>[KASSE_RKSV_KASSENID][MSGKASSE_RKSV_KASSENID]</td></tr>
              <tr><td>{|AES Key|}:</td><td>[KASSE_RKSV_AESKEY][MSGKASSE_RKSV_AESKEY]</td></tr>
             </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
  <input type="submit" name="speichern" value="Speichern" onclick="this.form.action += '#tabs-6';" style="float:right"/>

</div>


<div id="tabs-7">
 <div class="row">
   <div class="row-height">
     <div class="col-xs-12 col-md-12 col-md-height">
       <div class="inside inside-full-height">
         <fieldset>
           <legend>{|Adresse der Filiale|}</legend>
         </fieldset>
       </div>
     </div>
   </div>
 </div>
 <br />
</div>





</div>

  </form>


<script type="text/javascript">

$(document).ready(function() {
$('.veraltet').hide();

      $("#veraltet").click(function () {
            if ($(this).is(":checked")) {
                $(".veraltet").show();
            } else {
                $(".veraltet").hide();
            }
        });

});

</script>


<script type="text/javascript">
  $( function() {
    $( "#dialog-kommissionierverfahren1").dialog({
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 600,
      modal: true,
      buttons: {
        "Kommissioniervefahren jetzt aktivieren": function() {
          $.ajax({
                url: 'index.php?module=projekt&action=edit&id=[ID]',
                dataType: 'json',
                data: { cmd: "setup",mode:1 },
                method: 'post',
                success: function( data, textStatus, jQxhr ){
                    $(location).attr('href','index.php?module=projekt&action=edit&id=[ID]&rand='+Math.random()+'#tabs-3')
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });
          $( this ).dialog( "close" );
        },
        "Abbrechen": function() {
          $( this ).dialog( "close" );
        }
      }
    });
    $( "#dialog-kommissionierverfahren2").dialog({
      resizable: false,
      autoOpen: false,
      height: "auto",
      width: 600,
      modal: true,
      buttons: {
        "Kommissioniervefahren jetzt aktivieren": function() {
          $.ajax({
                url: 'index.php?module=projekt&action=edit&id=[ID]',
                dataType: 'json',
                data: { cmd: "setup",mode:2 },
                method: 'post',
                success: function( data, textStatus, jQxhr ){
                    $(location).attr('href','index.php?module=projekt&action=edit&id=[ID]&rand='+Math.random()+'#tabs-3')
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });

          $( this ).dialog( "close" );
        },
        "Abbrechen": function() {
          $( this ).dialog( "close" );
        }
      }
    });
    $( "#dialog-kommissionierverfahren3").dialog({
      resizable: false,
      autoOpen: false,
      height: "auto",
      width: 600,
      modal: true,
      buttons: {
        "Kommissioniervefahren jetzt aktivieren": function() {
          $.ajax({
                url: 'index.php?module=projekt&action=edit&id=[ID]',
                dataType: 'json',
                data: { cmd: "setup",mode:3 },
                method: 'post',
                success: function( data, textStatus, jQxhr ){
                    $(location).attr('href','index.php?module=projekt&action=edit&id=[ID]&rand='+Math.random()+'#tabs-3')
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });


          $( this ).dialog( "close" );
        },
        "Abbrechen": function() {
          $( this ).dialog( "close" );
        }
      }
    });
    $( "#dialog-kommissionierverfahren4").dialog({
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 600,
      modal: true,
      buttons: {
        "Kommissioniervefahren jetzt aktivieren": function() {
          $.ajax({
                url: 'index.php?module=projekt&action=edit&id=[ID]',
                dataType: 'json',
                data: { cmd: "setup",mode:4 },
                method: 'post',
                success: function( data, textStatus, jQxhr ){
                      location.reload();
                },
                error: function( jqXhr, textStatus, errorThrown ){
                    console.log( errorThrown );
                }
            });


          $( this ).dialog( "close" );
        },
        "Abbrechen": function() {
          $( this ).dialog( "close" );
        }
      }
    });



 
  });

  function DialogKommissionierverfahren(kom)
  {
    $( "#dialog-kommissionierverfahren" + kom).dialog( "open" );
  }

</script>

<div id="dialog-kommissionierverfahren1" title="Einstufig: Einzel-kommissionierung mit Paketmarke">

<p>Hier handelt es sich um einen Prozess bei dem Sie jeden Auftrag einzeln an die Kommissionierung &uuml;bergeben. Bei der &Uuml;bergabe erfolgt zum einen die Lagerbuchung der Auftragspositionen und zum anderen der Ausdruck von Lieferschein und Paketmarke. Hier wird keine weitere Stufe f&uuml;r Verpackung der Sendung im System angeboten.</p>

<p>Voraussetzungen:</p>
<ul>
<li>Prozess mit Versandzentrum</li>
<li>Echter physisch angebundener Drucker</li>
<li>autoversand_manuell Cronjob muss aktiv sein</li>
</ul>

</div>

<div id="dialog-kommissionierverfahren2" title="Zweistufig: Pickliste mit Versandzentrum">
<!--<iframe width="560" height="315" src="https://www.youtube.com/embed/-hhsfWQUoUo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>-->
<p>Hier handelt es sich um einen zweistufigen Prozess. Im ersten Schritt wird eine PICKLISTE erzeugt mit der man die Sammelkommissionierung durchf&uuml;hren kann. Die kommissionierte Ware kann dann im zweiten Schritt im Versandzentrum auf die einzelnen Lieferschein vereinzelt werden. Dort erfolgt auch die Erstellung von Paketmarke und Rechnung.</p>

<p>Voraussetzungen:</p>
<ul>
<li>Autoversand_manuell darf dabei nicht aktiviert sein</li>
<li>Kommissionierlauf gruppieren nach Lieferscheine</li>
</ul>

</div>

<div id="dialog-kommissionierverfahren3" title="Zweistufig: Multi-Order-Picking mit Versandzentrum">
<p>Hier handelt es sich ebenso um einen zweistufigen Prozess. Im ersten Schritt erfolgt die Kommissionierung papierlos mit einem mobilen Endger&auml;t (z.B. Handheld). Hierbei k&ouml;nnen wie der Name schon sagt  eine Sammelkommissionierung durchgef&uuml;hrt werden. Ebenso kann jedoch bei der Versand&uuml;bergabe auch ein gr&ouml;&szlig;er B2B-Auftrag einzeln an die Kommissionierung &uuml;bergeben werden. Im darauffolgenden Schritt werden die gepickten Waren wieder auf die Lieferscheine vereinzelt, verpackt und gelabelt.</p>

<p>Voraussetzungen:</p>
<ul>
<li>Kommissioinierlauf mit Multi-Order-Picking</li>
<li>passende Einstellungen im Modul Multi-Order-Picking (z.B. Arbeit mit Kiste oder Lieferschein-Etikett usw.)</li>
</ul>


</div>

<div id="dialog-kommissionierverfahren4" title="Fulfiller oder externer Produzent">
<p>Hier handelt es sich um einen einfachen Prozess bei dem in Xentral im Rahmen der Versandübe eine Lagerbuchung erfolgt. Alle weiteren Einstellungen m&uuml;ssen pro &Uuml;bertragen-Account vorgenommen werden. Dort kann definiert werden, ob</p>

<ol>
  <li>direkt Auftr&auml;ge oder</li>
  <li>Lieferscheine als Ergebnis der Versand&uuml;bergabe &uuml;bergeben werden.</li>
</ol>

<p>Voraussetzungen:</p>
<ul>
<li>Erfolgreich eingerichtete &Uuml;bertragen-Accounts</li>
</ul>


</div>
