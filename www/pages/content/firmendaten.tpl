
<script type="text/javascript">
    $(document).ready(function() {
        // "E-Mail"-Tab > Testmail senden
        $('#testmail-senden-button').click(function() {
            $('#fieldset-mailversand-einstellungen').loadingOverlay();
            window.setTimeout(function() {
                window.location.href = 'index.php?module=firmendaten&action=testmail';
            }, 250);
        });

        // "Steuer/Währung"-Tab > "Brutto Preise anzeigen" + "Netto Preise anzeigen" > Jeweils anderen Haken entfernen
        $('#immernettorechnungen, #immerbruttorechnungen').change(function() {
            var elementId = $(this).prop('id');
            var isChecked = $(this).prop('checked');
            var otherId = elementId === 'immernettorechnungen' ? 'immerbruttorechnungen' : 'immernettorechnungen';
            if(isChecked) {
                $('#' + otherId).prop('checked', !isChecked);
            }
        });

        // "Briefkopf"-Tab > Schriftart hochladen
        $('#upload-font-button').click(function () {
            $('#upload-font').effect('highlight', 'slow');
        });
    });
	
	function next_number(art,alterwert)
	{
  	var nummer =  prompt('Neue Nummer:',alterwert); 
		if(nummer!=null && nummer!='') { window.location.href='index.php?module=firmendaten&action=nextnumber&cmd='+art+'&nummer='+nummer;}
	}

  function testdaten()
  {
    document.forms[0].absender.value = "Musterfirma GmbH | Musterweg 5 | 12345 Musterstadt";
    document.forms[0].sichtbar.checked = true;

    document.forms[0].barcode.checked= false;
    document.forms[0].schriftgroesse.value = "7";
    document.forms[0].betreffszeile.value="9";
    document.forms[0].dokumententext.value = "9";
    document.forms[0].tabellenbeschriftung.value = "9";
    document.forms[0].tabelleninhalt.value = "9";
    document.forms[0].zeilenuntertext.value="7";
    document.forms[0].freitext.value="9";
    document.forms[0].brieftext.value="11";
    document.forms[0].infobox.value = "8";

    document.forms[0].zahlung_rechnung_sofort_de.value = "Rechnung zahlbar sofort.";
    document.forms[0].zahlung_rechnung_de.value = "Rechnung zahlbar innerhalb {ZAHLUNGSZIELTAGE}.";
    document.forms[0].zahlungszieltage.value = "14";
    document.forms[0].zahlungszieltageskonto.value = "10";
    document.forms[0].zahlungszielskonto.value = "2";

    document.forms[0].next_angebot.value = "100000";
    document.forms[0].next_auftrag.value = "200000";
    document.forms[0].next_rechnung.value = "400000";
    document.forms[0].next_gutschrift.value = "900000";
    document.forms[0].next_lieferschein.value = "300000";
    document.forms[0].next_retoure.value = "500000";
    document.forms[0].next_arbeitsnachweis.value = "300000";
    document.forms[0].next_bestellung.value = "100000";
    document.forms[0].next_kundennummer.value = "10000";
    document.forms[0].next_lieferantennummer.value = "70000";
    document.forms[0].next_mitarbeiternummer.value = "90000";
    document.forms[0].next_artikelnummer.value = "1000000";
//    document.forms[0].next_waren.value = "700000";
//    document.forms[0].next_sonstiges.value = "100000";
    document.forms[0].next_produktion.value = "400000";
    document.forms[0].next_preisanfrage.value = "100000";
    document.forms[0].next_receiptdocument.value = "300000";

    document.forms[0].elements['footer[0][0]'].value = "Sitz der Gesellschaft / Lieferanschrift";
    document.forms[0].elements['footer[0][1]'].value = "Musterfirma GmbH";
    document.forms[0].elements['footer[0][2]'].value = "Musterweg 5";
    document.forms[0].elements['footer[0][3]'].value = "D-12345 Musterstadt";
    document.forms[0].elements['footer[0][4]'].value = "Telefon +49 123 12 34 56 7";
    document.forms[0].elements['footer[0][5]'].value = "Telefax +49 123 12 34 56 78";

    document.forms[0].elements['footer[1][0]'].value = "Bankverbindung";
    document.forms[0].elements['footer[1][1]'].value = "Musterbank";
    document.forms[0].elements['footer[1][2]'].value = "Konto 123456789";
    document.forms[0].elements['footer[1][3]'].value = "BLZ 72012345";
    document.forms[0].elements['footer[1][4]'].value = "";
    document.forms[0].elements['footer[1][5]'].value = "";

    document.forms[0].elements['footer[2][0]'].value = "IBAN DE1234567891234567891";
    document.forms[0].elements['footer[2][1]'].value = "BIC/SWIFT DETSGDBWEMN";
    document.forms[0].elements['footer[2][2]'].value = "Ust-IDNr. DE123456789";
    document.forms[0].elements['footer[2][3]'].value = "E-Mail: info@musterfirma-gmbh.de";
    document.forms[0].elements['footer[2][4]'].value = "Internet: http://www.musterfirma.de";
    document.forms[0].elements['footer[2][5]'].value = "";

    document.forms[0].elements['footer[3][0]'].value = "Gesch&auml;ftsf&uuml;hrer";
    document.forms[0].elements['footer[3][1]'].value = "Max Musterman";
    document.forms[0].elements['footer[3][2]'].value = "Handelsregister: HRB 12345";
    document.forms[0].elements['footer[3][3]'].value = "Amtsgericht: Musterstadt";
    document.forms[0].elements['footer[3][4]'].value = "";
    document.forms[0].elements['footer[3][5]'].value = "";


    document.forms[0].benutzername.value = "musterman";
    document.forms[0].passwort.value = "passwort";
    document.forms[0].host.value = "smtp.server.de";
    document.forms[0].port.value = "25";
    document.forms[0].ssl.checked = true;

    document.forms[0].email.value = "info@server.de";
    document.forms[0].absendername.value = "Meine Firma";
    document.forms[0].signatur.value = "--\n"
				     + "Musterfirma GmbH\n"
				     + "Musterweg 5\n"
				     + "D-12345 Musterstadt\n\n"
				     + "Tel +49 123 12 34 56 7\n"
				     + "Fax +49 123 12 34 56 78\n\n"
				     + "Name der Gesellschaft: Musterfirma GmbH\n"
				     + "Sitz der Gesellschaft: Musterstadt\n\n"
				     + "Handelsregister: Musterstadt, HRB 12345\n"
				     + "Geschäftsführung: Max Musterman\n"
				     + "USt-IdNr.: DE123456789\n\n"
				     + "AGB: http://www.musterfirma.de/\n";

    document.forms[0].name.value = "Musterfirma GmbH";
    document.forms[0].strasse.value = "Musterweg 5";
    document.forms[0].plz.value = "12345";
    document.forms[0].ort.value = "Musterstadt";
    document.forms[0].steuernummer.value = "DE123456789";
    document.forms[0].firmenfarbe.value = "";

	}

	function testdaten_textvorlagen()
  {
  
    document.forms[0].angebot_header.value = "Sehr geehrte Damen und Herren,\n\nhiermit bieten wir Ihnen an:";
    document.forms[0].auftrag_header.value = "Sehr geehrte Damen und Herren,\n\nvielen Dank für Ihren Auftrag.";
    document.forms[0].rechnung_header.value = "Sehr geehrte Damen und Herren,\n\nanbei Ihre Rechnung.";
    document.forms[0].lieferschein_header.value = "Sehr geehrte Damen und Herren,\n\nwir liefern Ihnen:";
    document.forms[0].arbeitsnachweis_header.value = "Sehr geehrte Damen und Herren,\n\nwir liefern Ihnen:";
    document.forms[0].gutschrift_header.value = "Sehr geehrte Damen und Herren,\n\nanbei Ihre {ART}:";
    document.forms[0].bestellung_header.value = "Sehr geehrte Damen und Herren,\n\nwir bestellen hiermit:";

 		document.forms[0].angebot_footer.value = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
    document.forms[0].auftrag_footer.value = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
    document.forms[0].rechnung_footer.value = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
    document.forms[0].lieferschein_footer.value = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
    document.forms[0].arbeitsnachweis_footer.value = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
    document.forms[0].gutschrift_footer.value = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
    document.forms[0].bestellung_footer.value = "Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift gültig.";
		
    document.forms[0].eu_lieferung_vermerk.value = "Steuerfrei nach § 4 Nr. 1b i.V.m. § 6 a UStG. Ihre USt-IdNr. {USTID} Land: {LAND}";

   }


</script>


<div id="tabs">
<ul>
<li><a href="#tabs-1">Firmenanschrift</a></li>
<li><a href="#tabs-2">Briefkopf</a></li>
<li><a href="#tabs-3">Textvorlagen</a></li>
<li><a href="#tabs-4">E-Mail</a></li>
<li><a href="#tabs-6">Nummernkreise</a></li>
<li><a href="#tabs-7">Zahlungsweisen</a></li>
<li><a href="#tabs-8">Steuer / W&auml;hrung</a></li>
<li><a href="#tabs-9">System</a></li>
<li><a href="#tabs-14">Freifelder</a></li>
<li><a href="#tabs-10">Lizenz</a></li>
<li><a href="#tabs-11">API's</a></li>
<li><a href="#tabs-12">Module</a></li>
<li><a href="#tabs-13">Bereinigung</a></li>
</ul>
<form name="firmendatenform" id="firmendatenform" action="" method="POST"  enctype="multipart/form-data">
<div id="tabs-1">
[MESSAGE]
	<!--<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Installation|}</legend>
						<table cellspacing="5" width="100%">
							<tr>
								<td width="300"></td>
								<td><input type="button" onclick="testdaten()" value="Musterdaten einf&uuml;gen"><br><i>Daten werden erst nach Speichern &uuml;bernommen.</i></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>-->

	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Anschrift|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Name:</td><td><input type="text" name="name" value="[NAME]" size="40"></td></tr>
							<tr><td>Stra&szlig;e:</td><td><input type="text" name="strasse" value="[STRASSE]" size="40"></td></tr>
							<tr><td>PLZ:</td><td><input type="text" name="plz" value="[PLZ]" size="40"></td></tr>
							<tr><td>Ort:</td><td><input type="text" name="ort" value="[ORT]" size="40"></td></tr>
							<tr><td>Land:</td><td><input type="text" name="land" value="[LAND]" size="40" maxlength="2"></td></tr>
							<tr><td>USTID:</td><td><input type="text" name="steuernummer" value="[STEUERNUMMER]" size="40"></td></tr>
							<tr><td>SEPA Gl&auml;ubiger ID:</td><td><input type="text" name="sepaglaeubigerid" value="[SEPAGLAEUBIGERID]" size="40"></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<br>
	<input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-1');" value="Speichern" style="float:right">
</div>
<div id="tabs-2">
	[MESSAGE]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Absender|}</legend>
						<table cellspacing="5">
							<tr><td width="300">Absender einblenden:</td><td><input type="checkbox" name="sichtbar" [SICHTBAR]></td><td><input type="text" name="absender" size="50" value="[ABSENDER]"></td></tr>
							<tr><td width="300">Absender unterstrichen darstellen:</td><td><input type="checkbox" name="absenderunterstrichen" [ABSENDERUNTERSTRICHEN]></td></tr>
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
						<legend>{|PDF Grundeinstellungen|}</legend>
						<table cellspacing="5">
							<tr><td>HTML in Briefkopf und Text erlauben:</td><td><input type="checkbox" name="briefhtml" [BRIEFHTML]></td></tr>
							<tr><td width="300">Schriftart:</td><td><input type="text" name="schriftart" size="20" value="[SCHRIFTART]"></td></tr>
							<tr><td>Knickfalz ausblenden:</td><td><input type="checkbox" name="knickfalz" [KNICKFALZ]></td></tr>
							<tr><td>Barcode einblenden:</td><td><input type="checkbox" name="barcode" [BARCODE]></td></tr>
							<tr><td width="300">Barcode Header Abstand Oben:</td><td><input type="text" name="barcode_y_header" size="3" value="[BARCODE_Y_HEADER]"></td></tr>
							<tr><td width="300">Barcode Header Abstand Links:</td><td><input type="text" name="barcode_x_header" size="3" value="[BARCODE_X_HEADER]"></td></tr>
							<tr><td width="300">Barcode Footer Abstand Links:</td><td><input type="text" name="barcode_x" size="3" value="[BARCODE_X]"></td></tr>
							<tr><td width="300">Barcode Footer Abstand Oben:</td><td><input type="text" name="barcode_y" size="3" value="[BARCODE_Y]"></td></tr>
							<tr><td width="300">Seitennummerierung und Belegnr Ausrichtung:</td><td><input type="checkbox" name="seite_von_sichtbar" [SEITEVONSICHTBAR]><input type="text" name="seite_von_ausrichtung" value="[SEITEVONAUSRICHTUNG]" size="2"></td></tr>
							<tr><td width="300">Ausrichtung an Tabelle:</td><td><input type="checkbox" name="seite_von_ausrichtung_relativ" [SEITE_VON_AUSRICHTUNG_RELATIV]></td></tr>
							<tr><td width="300">Belegnummer bei Seitenanzahl anzeigen:</td><td><input type="checkbox" name="seite_belegnr" [SEITE_BELEGNR]></td></tr>
							<tr><td width="300">Artikelbeschreibung &uuml;ber komplette Breite:</td><td><input type="checkbox" name="breite_artikelbeschreibung" [BREITE_ARTIKELBESCHREIBUNG]></td></tr>
							<tr><td width="300">Lange Artikelnummern im Briefpapier:</td><td><input type="checkbox" name="langeartikelnummern" [LANGEARTIKELNUMMERN]></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend></legend>
						<table>
							<tr><td width="300">Breite Position:</td><td><input type="text" name="breite_position" size="3" value="[BREITE_POSITION]"></td></tr>
							<tr><td width="300">Breite Nummer:</td><td><input type="text" name="breite_nummer" size="3" value="[BREITE_NUMMER]"></td></tr>
							<tr><td width="300">Breite Menge:</td><td><input type="text" name="breite_menge" size="3" value="[BREITE_MENGE]"></td></tr>
							<tr><td width="300">Breite Artikel:</td><td><input type="text" name="breite_artikel" size="3" value="[BREITE_ARTIKEL]"></td></tr>
							<tr><td width="300">Breite Steuer:</td><td><input type="text" name="breite_steuer" size="3" value="[BREITE_STEUER]"></td></tr>
							<tr><td width="300">Breite Einheit:</td><td><input type="text" name="breite_einheit" size="3" value="[BREITE_EINHEIT]"></td></tr>
							<tr><td width="300">Subpositionen in Gruppen:</td><td><input type="checkbox" name="belege_subpositionen" [BELEGE_SUBPOSITIONEN]></td></tr>
							<tr><td width="300">Subpositionen in St&uuml;cklisten:</td><td><input type="checkbox" name="belege_subpositionenstuecklisten" [BELEGE_SUBPOSITIONENSTUECKLISTEN]></td></tr>
							<tr><td width="300">St&uuml;cklisten einr&uuml;cken:</td><td><input type="text" name="belege_stuecklisteneinrueckenmm" size="3" value="[BELEGE_STUECKLISTENEINRUECKENMM]" /></td></tr>
							<tr><td width="300">Gesamtsumme ohne Doppelstrich:</td><td><input type="checkbox" name="briefpapier_ohnedoppelstrich" [BRIEFPAPIER_OHNEDOPPELSTRICH]></td></tr>
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
						<legend>{|Formatierung|}</legend>
						<table>
							<tr>
								<td width="300">Schriftgr&ouml;&szlig;e Betreffzeile:</td>
								<td><input type="text" size="3" name="betreffszeile" value="[BETREFFSZEILE]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Tabellenbeschriftung:</td>
								<td><input type="text" size="3" name="tabellenbeschriftung" value="[TABELLENBESCHRIFTUNG]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Artikel Beschreibung:</td>
								<td><input type="text" size="3" name="zeilenuntertext" value="[ZEILENUNTERTEXT]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Infobox:</td>
								<td><input type="text" size="3" name="infobox" value="[INFOBOX]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Empf&auml;nger:</td>
								<td><input type="text" size="3" name="schriftgroesse" value="[SCHRIFTGROESSE]"></td>
							</tr>
							<tr>
								<td></td>
								<td></td>
							</tr>
						</table>
						<br />
						<table>
							<tr>
								<td width="300">Schriftgr&ouml;&szlig;e Dokumententext:</td>
								<td><input type="text" size="3" name="dokumententext" value="[DOKUMENTENTEXT]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Tabelleninhalt:</td>
								<td><input type="text" size="3" name="tabelleninhalt" value="[TABELLENINHALT]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Freitext:</td>
								<td><input type="text" size="3" name="freitext" value="[FREITEXT]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Brieftext:</td>
								<td><input type="text" size="3" name="brieftext" value="[BRIEFTEXT]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Absender:</td>
								<td><input type="text" size="3" name="schriftgroesseabsender" value="[SCHRIFTGROESSEABSENDER]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Gesamt:</td>
								<td><input type="text" size="3" name="schriftgroesse_gesamt" value="[SCHRIFTGROESSE_GESAMT]"></td>
							</tr>
							<tr>
								<td>Schriftgr&ouml;&szlig;e Steuer:</td>
								<td><input type="text" size="3" name="schriftgroesse_gesamt_steuer" value="[SCHRIFTGROESSE_GESAMT_STEUER]"></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend></legend>
							<table>
								<tr>
									<td width="300">Abstand Infobox oben/unten:</td>
									<td><input type="text" size="3" name="abstand_boxrechtsoben" value="[ABSTANDBOXRECHTSOBEN]"></td>
								</tr>
								<tr>
									<td>Abstand Infobox rechts/links:</td>
									<td><input type="text" size="3" name="abstand_boxrechtsoben_lr" value="[ABSTANDBOXRECHTSOBENLR]"></td>
								</tr>
								<tr>
									<td>Ausrichtung Infobox Text:</td>
									<td><input type="text" size="3" name="boxausrichtung" value="[BOXAUSRICHTUNG]"></td>
								</tr>
								<tr>
									<td>Abstand Artikeltabelle oben/unten:</td>
									<td><input type="text" size="3" name="abstand_artikeltabelleoben" value="[ABSTANDARTIKELTABELLEOBEN]"></td>
								</tr>
								<tr>
									<td>Abstand Inhalt ab Seite 2 Oben:</td>
									<td><input type="text" size="3" name="abseite2y" value="[ABSEITE2Y]"></td>
								</tr>
								<tr>
									<td>Abstand Umbruch unten:</td>
									<td><input type="text" size="3" name="abstand_umbruchunten" value="[ABSTAND_UMBRUCHUNTEN]"></td>
								</tr>
							</table>
							<br />
							<table>
								<tr>
									<td width="300">Abstand Empf&auml;nger oben/unten:</td>
									<td><input type="text" size="3" name="abstand_adresszeileoben" value="[ABSTANDADRESSZEILEOBEN]"></td>
								</tr>
								<tr>
									<td>Abstand Empf&auml;nger links:</td>
									<td><input type="text" size="3" name="abstand_adresszeilelinks" value="[ABSTAND_ADRESSZEILELINKS]"></td>
								</tr>
								<tr>
									<td>Abstand Betreffzeile oben/unten:</td>
									<td><input type="text" size="3" name="abstand_betreffzeileoben" value="[ABSTANDBETREFFZEILEOBEN]"></td>
								</tr>
								<tr>
									<td>Abstand Artikelname zu Beschreibung:</td>
									<td><input type="text" size="3" name="abstand_name_beschreibung" value="[ABSTANDNAMEBESCHREIBUNG]"></td>
								</tr>
								<tr>
									<td>Abstand Seitenrand Links / Rechts:</td>
									<td><input type="text" size="3" name="abstand_seitenrandlinks" value="[ABSTAND_SEITENRANDLINKS]">/ <input type="text" size="3" name="abstand_seitenrandrechts" value="[ABSTAND_SEITENRANDRECHTS]"></td>
								</tr>
								<tr>
									<td>Abstand Seitennummer Unten:</td>
									<td><input type="text" size="3" name="abstand_seiten_unten" value="[ABSTAND_SEITEN_UNTEN]"></td>
								</tr>
								<tr>
									<td>Abstand Gesamtsumme Links:</td>
									<td><input type="text" size="3" name="abstand_gesamtsumme_lr" value="[ABSTAND_GESAMTSUMME_LR]"></td>
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
						<legend>{|Freitext 1|}</legend>
						<table>
							<tr><td width="300">Einblenden:</td><td><input type="checkbox" value="1" [FREITEXT1AKTIV] name="freitext1aktiv" ></td></tr>
							<tr><td>Abstand Links:</td><td><input type="text" size="3" value="[FREITEXT1X]" name="freitext1x"></td></tr>
							<tr><td>Abstand Oben:</td><td><input type="text" size="3" value="[FREITEXT1Y]" name="freitext1y" ></td></tr>
							<tr><td>Schriftgröße:</td><td><input type="text" size="3" value="[FREITEXT1SCHRIFTGROESSE]" name="freitext1schriftgroesse"></td></tr>
							<tr><td>Breite:</td><td><input type="text" size="3" value="[FREITEXT1BREITE]" name="freitext1breite"></td></tr>
							<tr><td>Inhalt:</td><td><textarea rows="5" cols="40" name="freitext1inhalt" id="freitext1inhalt" data-lang="freitext1inhalt">[FREITEXT1INHALT]</textarea></td></tr>
						</table>
					</fieldset>
			  </div>
  		</div>
  		<div class="col-xs-12 col-md-6 col-md-height">
  			<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Freitext 2|}</legend>
							<table>
								<tr><td width="300">Einblenden:</td><td><input type="checkbox" value="1" [FREITEXT2AKTIV] name="freitext2aktiv"></td></tr>
								<tr><td>Abstand Links:</td><td><input type="text" size="3" value="[FREITEXT2X]" name="freitext2x"></td></tr>
								<tr><td>Abstand Oben:</td><td><input type="text" size="3" value="[FREITEXT2Y]" name="freitext2y" ></td></tr>
								<tr><td>Schriftgröße:</td><td><input type="text" size="3" value="[FREITEXT2SCHRIFTGROESSE]" name="freitext2schriftgroesse"></td></tr>
								<tr><td>Breite:</td><td><input type="text" size="3" value="[FREITEXT2BREITE]" name="freitext2breite"></td></tr>
								<tr><td>Inhalt:</td><td><textarea rows="5" cols="40" name="freitext2inhalt" id="freitext2inhalt" data-lang="freitext2inhalt">[FREITEXT2INHALT]</textarea></td></tr>
							</table>
					</fieldset>
		    </div>
  		</div>
  	</div>
  </div>

	<div class="row" id="wizard-footer">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>Fu&szlig;zeile</legend>
						<table cellspacing="5" align="left">
							<tr>
								<td colspan="2">Footer einblenden:<input type="checkbox" name="footersichtbar" [FOOTERSICHTBAR]></td>
								<td colspan="2">Footer zentriert (Spalte 1):<input type="checkbox" name="footer_zentriert" [FOOTER_ZENTRIERT]></td>
								<td colspan="2">Farbe: <input type="text" name="footer_farbe" value="[FOOTER_FARBE]" size="3"></td>
							</tr>

							<tr>
								<td></td>
								<td>Spalte 1</td>
								<td>Spalte 2</td>
								<td></td>
								<td>Spalte 3</td>
								<td>Spalte 4</td>
							</tr>

							<tr>
								<td>1</td>
								<td><input type="text" name="footer[0][0]" size="30" value="[FOOTER00]"></td><td><input type="text" name="footer[1][0]" size="30" value="[FOOTER10]"></td>
								<td></td><td><input type="text" name="footer[2][0]" size="30" value="[FOOTER20]"></td><td><input type="text" name="footer[3][0]" size="30" value="[FOOTER30]"></td>
							</tr>
							<tr>
								<td>2</td>
								<td><input type="text" name="footer[0][1]" size="30" value="[FOOTER01]"></td><td><input type="text" name="footer[1][1]" size="30" value="[FOOTER11]"></td>
								<td></td><td><input type="text" name="footer[2][1]" size="30" value="[FOOTER21]"></td><td><input type="text" name="footer[3][1]" size="30" value="[FOOTER31]"></td>
							</tr>
							<tr>
								<td>3</td>
								<td><input type="text" name="footer[0][2]" size="30" value="[FOOTER02]"></td><td><input type="text" name="footer[1][2]" size="30" value="[FOOTER12]"></td>
								<td></td><td><input type="text" name="footer[2][2]" size="30" value="[FOOTER22]"></td><td><input type="text" name="footer[3][2]" size="30" value="[FOOTER32]"></td>
							</tr>
							<tr>
								<td>4</td>
								<td><input type="text" name="footer[0][3]" size="30" value="[FOOTER03]"></td><td><input type="text" name="footer[1][3]" size="30" value="[FOOTER13]"></td>
								<td></td><td><input type="text" name="footer[2][3]" size="30" value="[FOOTER23]"></td><td><input type="text" name="footer[3][3]" size="30" value="[FOOTER33]"></td>
							</tr>
							<tr>
								<td>5</td>
								<td><input type="text" name="footer[0][4]" size="30" value="[FOOTER04]"></td><td><input type="text" name="footer[1][4]" size="30" value="[FOOTER14]"></td>
								<td></td><td><input type="text" name="footer[2][4]" size="30" value="[FOOTER24]"></td><td><input type="text" name="footer[3][4]" size="30" value="[FOOTER34]"></td>
							</tr>
							<tr>
								<td>6</td>
								<td><input type="text" name="footer[0][5]" size="30" value="[FOOTER05]"></td><td><input type="text" name="footer[1][5]" size="30" value="[FOOTER15]"></td>
								<td></td><td><input type="text" name="footer[2][5]" size="30" value="[FOOTER25]"></td><td><input type="text" name="footer[3][5]" size="30" value="[FOOTER35]"></td>
							</tr>
							<tr>
								<td></td>
								<td>Breite: <input type="text" name="footer_breite1" size="3" value="[FOOTERBREITE1]"></td>
								<td>Breite: <input type="text" name="footer_breite2" size="3" value="[FOOTERBREITE2]"></td>
								<td></td>
								<td>Breite: <input type="text" name="footer_breite3" size="3" value="[FOOTERBREITE3]"></td>
								<td>Breite: <input type="text" name="footer_breite4" size="3" value="[FOOTERBREITE4]"></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-6 col-md-height" id="wizard-letter-paper">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Hintergrund|}</legend>
						<table width="100%">
							<tr><td colspan="6">Aktuell wird verwendet: [HINTERGRUNDTEXT]<br><br></td></tr>
						</table>
						<table width="100%">
							<tr valign="top">
								<td width="300"><input type="radio" name="hintergrund" value="logo" [HINTERGRUNDLOGO]>&nbsp;Logo</td><td><input type="file" name="logo">[LOGOVORHANDEN] [HINTERGRUNDLOGOTEXT] (<b style="color:red">Achtung aktuell wird nur JPG unterst&uuml;tzt!</b>)</td>
							</tr>
							<tr>
								<td><br></td>
							</tr>
							<tr valign="top">
								<td><input type="radio" name="hintergrund" value="briefpapier" [HINTERGRUNDBRIEFPAPIER]>&nbsp;PDF als Hintergrund</td><td><input type="file" name="briefpapier">[BRIEFPAPIERVORHANDEN] [HINTERGRUNDBRIEFPAPIERTEXT] (Seite 1)<br><br>
								Anderes Briefpapier ab Seite 2:<input type="checkbox" name="briefpapier2vorhanden" [BRIEFPAPIER2VORHANDEN]>
								<br>
								<input type="file" name="briefpapier2">[BRIEFPAPIERVORHANDEN2]&nbsp;[HINTERGRUNDBRIEFPAPIER2TEXT](Seite 2 und Folgende)<br></td>
							</tr>
							<tr>
								<td><br></td>
							</tr>
							<tr valign="top">
								<td><input type="radio" name="hintergrund" value="kein" [HINTERGRUNDKEIN]>&nbsp;Kein Hintergrund</td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset id="upload-font">
						<legend>{|Schriftart hochladen|}</legend>
						<p><i>Es werden nur TTF-Schriftarten unterstützt. Für jeden Schriftschnitt muss eine eigene Datei hochgeladen werden.</i></p>
						<table width="100%">
							<tr>
								<td width="300">Bezeichnung: </td>
								<td><input type="text" name="schriftart_upload_bezeichnung"></td>
							</tr>
							<tr>
								<td>Schriftschnitt Normal: </td>
								<td><input type="file" name="schriftart_upload[normal]"></td>
							</tr>
							<tr>
								<td>Schriftschnitt Kursiv: </td>
								<td><input type="file" name="schriftart_upload[kursiv]"></td>
							</tr>
							<tr>
								<td>Schriftschnitt Fett: </td>
								<td><input type="file" name="schriftart_upload[fett]"></td>
							</tr>
							<tr>
								<td>Schriftschnitt Fettkursiv: </td>
								<td><input type="file" name="schriftart_upload[fettkursiv]"></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<br><input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-2');" value="Speichern" style="float:right">


</div>


<div id="tabs-3">
[MESSAGE]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Textvorlagen|}</legend>
						<table width="100%">
							<tr><td width="300"></td><td>&nbsp;<input type="button" onclick="if(confirm('Textvorlagen wirklich überschreiben?'))window.location.href='index.php?module=firmendaten&action=briefpapiervorlage'" value="Standardtexte laden"></td></tr>
							<tr><td>Angebot Text vor Artikeltabelle:</td><td><textarea rows="5" cols="100" name="angebot_header" id="angebot_header" data-lang="angebot_header">[ANGEBOT_HEADER]</textarea></td></tr>
							<tr><td>Angebot Text am Ende (nach Freitext):</td><td><textarea rows="5" cols="100" name="angebot_footer" id="angebot_footer" data-lang="angebot_footer">[ANGEBOT_FOOTER]</textarea></td></tr>
							<tr><td>Angebot ohne Briefpapier und Logo:</td><td><input type="checkbox" name="angebot_ohnebriefpapier" [ANGEBOT_OHNEBRIEFPAPIER]></td></tr>

							<tr><td>Auftrag Text vor Artikeltabelle:</td><td><textarea rows="5" cols="100" name="auftrag_header" id="auftrag_header" data-lang="auftrag_header">[AUFTRAG_HEADER]</textarea></td></tr>
							<tr><td>Auftrag Text am Ende (nach Freitext):</td><td><textarea rows="5" cols="100" name="auftrag_footer" id="auftrag_footer" data-lang="auftrag_footer">[AUFTRAG_FOOTER]</textarea></td></tr>
							<tr><td>Auftrag ohne Briefpapier und Logo:</td><td><input type="checkbox" name="auftrag_ohnebriefpapier" [AUFTRAG_OHNEBRIEFPAPIER]></td></tr>

							<tr><td>Rechnung Text vor Artikeltabelle:</td><td><textarea rows="5" cols="100" name="rechnung_header" id="rechnung_header" data-lang="rechnung_header">[RECHNUNG_HEADER]</textarea></td></tr>
							<tr><td>Rechnung Text am Ende (nach Freitext):</td><td><textarea rows="5" cols="100" name="rechnung_footer" id="rechnung_footer" data-lang="rechnung_footer">[RECHNUNG_FOOTER]</textarea></td></tr>
							<tr><td>Rechnung ohne Briefpapier und Logo:</td><td><input type="checkbox" name="rechnung_ohnebriefpapier" [RECHNUNG_OHNEBRIEFPAPIER]></td></tr>

							<tr><td>Lieferschein Text vor Artikeltabelle:</td><td><textarea rows="5" cols="100" name="lieferschein_header" id="lieferschein_header" data-lang="lieferschein_header">[LIEFERSCHEIN_HEADER]</textarea></td></tr>
							<tr><td>Lieferschein Text am Ende (nach Freitext):</td><td><textarea rows="5" cols="100" name="lieferschein_footer" id="lieferschein_footer" data-lang="lieferschein_footer">[LIEFERSCHEIN_FOOTER]</textarea></td></tr>
							<tr><td>Lieferschein ohne Briefpapier und Logo:</td><td><input type="checkbox" name="lieferschein_ohnebriefpapier" [LIEFERSCHEIN_OHNEBRIEFPAPIER]></td></tr>

							<tr><td>Gutschrift Text vor Artikeltabelle:</td><td><textarea rows="5" cols="100" name="gutschrift_header" id="gutschrift_header" data-lang="gutschrift_header">[GUTSCHRIFT_HEADER]</textarea><br><i>Variablen: {ART} (Gutschrift oder Stornorechnung):</i></td></tr>
							<tr><td>Gutschrift Text am Ende (nach Freitext):</td><td><textarea rows="5" cols="100" name="gutschrift_footer" id="gutschrift_footer" data-lang="gutschrift_footer">[GUTSCHRIFT_FOOTER]</textarea></td></tr>
							<tr><td>Gutschrift ohne Briefpapier und Logo:</td><td><input type="checkbox" name="gutschrift_ohnebriefpapier" [GUTSCHRIFT_OHNEBRIEFPAPIER]></td></tr>

							<tr><td>Bestellung Text vor Artikeltabelle:</td><td><textarea rows="5" cols="100" name="bestellung_header" id="bestellung_header" data-lang="bestellung_header">[BESTELLUNG_HEADER]</textarea></td></tr>
							<tr><td>Bestellung Text am Ende (nach Freitext):</td><td><textarea rows="5" cols="100" name="bestellung_footer" id="bestellung_footer" data-lang="bestellung_footer">[BESTELLUNG_FOOTER]</textarea></td></tr>

							<tr><td>Arbeitsnachweis Text vor Artikeltabelle:</td><td><textarea rows="5" cols="100" name="arbeitsnachweis_header" id="arbeitsnachweis_header" data-lang="arbeitsnachweis_header">[ARBEITSNACHWEIS_HEADER]</textarea></td></tr>
							<tr><td>Arbeitsnachweis Text am Ende (nach Freitext):</td><td><textarea rows="5" cols="100" name="arbeitsnachweis_footer" id="arbeitsnachweis_footer" data-lang="arbeitsnachweis_footer">[ARBEITSNACHWEIS_FOOTER]</textarea></td></tr>
							<tr><td>Arbeitsnachweis ohne Briefpapier und Logo:</td><td><input type="checkbox" name="arbeitsnachweis_ohnebriefpapier" [ARBEITSNACHWEIS_OHNEBRIEFPAPIER]></td></tr>

							<tr><td>Provisionsgutschrift Text vor Artikeltabelle:</td><td><textarea rows="5" cols="100" name="provisionsgutschrift_header" id="provisionsgutschrift_header" data-lang="provisionsgutschrift_header">[PROVISIONSGUTSCHRIFT_HEADER]</textarea></td></tr>
							<tr><td>Provisionsgutschrift Text am Ende (nach Freitext):</td><td><textarea rows="5" cols="100" name="provisionsgutschrift_footer" id="provisionsgutschrift_footer" data-lang="provisionsgutschrift_footer">[PROVISIONSGUTSCHRIFT_FOOTER]</textarea></td></tr>


							<tr><td>Proformarechnung Text vor Artikeltabelle:</td><td><textarea rows="5" cols="100" name="proformarechnung_header" id="proformarechnung_header" data-lang="proformarechnung_header">[PROFORMARECHNUNG_HEADER]</textarea></td></tr>
							<tr><td>Proformarechnung Text am Ende (nach Freitext):</td><td><textarea rows="5" cols="100" name="proformarechnung_footer" id="proformarechnung_footer" data-lang="proformarechnung_footer">[PROFORMARECHNUNG_FOOTER]</textarea></td></tr>
							<tr><td>Proformarechnung ohne Briefpapier und Logo:</td><td><input type="checkbox" name="proformarechnung_ohnebriefpapier" [PROFORMARECHNUNG_OHNEBRIEFPAPIER]></td></tr>

							<tr><td>Reisekosten ohne Briefpapier und Logo:</td><td><input type="checkbox" name="reisekosten_ohnebriefpapier" [REISEKOSTEN_OHNEBRIEFPAPIER]></td></tr>

							<tr><td>EU-Lieferung Vermerk:</td><td><textarea rows="5" cols="100" name="eu_lieferung_vermerk" id="eu_lieferung_vermerk" data-lang="eu_lieferung_vermerk">[EU_LIEFERUNG_VERMERK]</textarea><br><i>Variablen: {USTID} {LAND}</i></td></tr>
							<tr><td>Export-Lieferung Vermerk:</td><td><textarea rows="5" cols="100" name="export_lieferung_vermerk" id="export_lieferung_vermerk" data-lang="export_lieferung_vermerk">[EXPORT_LIEFERUNG_VERMERK]</textarea><br><i>Variablen: {LAND}</i></td></tr>

							<tr><td>Variablen:</td><td><i>{LAND}, {FREIFELD1},{FREIFELD2},{FREIFELD3},{ANSCHREIBEN},{BELEGNR},{KUNDENNUMMER},{VERBANDSNUMMER},{VERBAND},{LIEFERTERMIN},{LIEFERWOCHE},{GUELTIGBIS},{GUELTIGBISWOCHE},{LIEFERADRESSE},{LIEFERADRESSELANG},{NETTOGEWICHT},{PROJEKT},{TRACKINGNUMMER},{ANZAHLTEILE},{ANZAHLTEILEALLE},{NVE},{ABWEICHENDE_RECHNUNGSADRESSE},{ABWEICHENDE_RECHNUNGSADRESSELANG}, {VERSANDARTBEZEICHNUNG}</i><br><br></td></tr>

							<tr><td>Angebot Reihenfolge nach Tabelle:</td><td><textarea rows="5" cols="100" name="footer_reihenfolge_angebot" id="footer_reihenfolge_angebot">[FOOTER_REIHENFOLGE_ANGEBOT]</textarea><br>
							eigene Reihenfolge  f&uuml;r Angebot aktivieren <input type="checkbox" name="footer_reihenfolge_angebot_aktivieren" [FOOTER_REIHENFOLGE_ANGEBOT_AKTIVIEREN]><br><i>Variablen: {FOOTERFREITEXT},{FOOTERTEXTVORLAGEANGEBOT},{FOOTERSTEUER},{FOOTERZAHLUNGSWEISETEXT}</i><br><br><br></td></tr>
							<tr><td>Auftrag Reihenfolge nach Tabelle:</td><td><textarea rows="5" cols="100" name="footer_reihenfolge_auftrag" id="footer_reihenfolge_auftrag">[FOOTER_REIHENFOLGE_AUFTRAG]</textarea><br>eigene Reihenfolge  f&uuml;r Auftrag aktivieren <input type="checkbox" name="footer_reihenfolge_auftrag_aktivieren" [FOOTER_REIHENFOLGE_AUFTRAG_AKTIVIEREN]><br><i>Variablen: {FOOTERFREITEXT},{FOOTERTEXTVORLAGEAUFTRAG},{FOOTERSTEUER},{FOOTERZAHLUNGSWEISETEXT},{FOOTERSYSTEMFREITEXT}</i><br><br><br></td></tr>
							<tr><td>Rechnung Reihenfolge nach Tabelle:</td><td><textarea rows="5" cols="100" name="footer_reihenfolge_rechnung" id="footer_reihenfolge_rechnung">[FOOTER_REIHENFOLGE_RECHNUNG]</textarea><br>eigene Reihenfolge  f&uuml;r Rechnung aktivieren <input type="checkbox" name="footer_reihenfolge_rechnung_aktivieren" [FOOTER_REIHENFOLGE_RECHNUNG_AKTIVIEREN]><br><i>Variablen: {FOOTERVERSANDINFO},{FOOTERFREITEXT},{FOOTERTEXTVORLAGERECHNUNG},{FOOTERSTEUER},{FOOTERZAHLUNGSWEISETEXT}{FOOTERSYSTEMFREITEXT}</i><br><br><br></td></tr>
							<tr><td>Gutschrift Reihenfolge nach Tabelle:</td><td><textarea rows="5" cols="100" name="footer_reihenfolge_gutschrift" id="footer_reihenfolge_gutschrift">[FOOTER_REIHENFOLGE_GUTSCHRIFT]</textarea><br>eigene Reihenfolge  f&uuml;r Gutschrift aktivieren <input type="checkbox" name="footer_reihenfolge_gutschrift_aktivieren" [FOOTER_REIHENFOLGE_GUTSCHRIFT_AKTIVIEREN]><br><i>Variablen: {FOOTERFREITEXT},{FOOTERTEXTVORLAGEGUTSCHRIFT},{FOOTERSTEUER},{FOOTERZAHLUNGSWEISETEXT}</i><br><br><br></td></tr>
							<tr><td>Lieferschein Reihenfolge nach Tabelle:</td><td><textarea rows="5" cols="100" name="footer_reihenfolge_lieferschein" id="footer_reihenfolge_lieferschein">[FOOTER_REIHENFOLGE_LIEFERSCHEIN]</textarea><br>eigene Reihenfolge  f&uuml;r Lieferschein aktivieren <input type="checkbox" name="footer_reihenfolge_lieferschein_aktivieren" [FOOTER_REIHENFOLGE_LIEFERSCHEIN_AKTIVIEREN]><br><i>Variablen: {FOOTERVERSANDINFO},{FOOTERFREITEXT},{FOOTERTEXTVORLAGELIEFERSCHEIN}</i><br><br><br></td></tr>
							<tr><td>Bestellung Reihenfolge nach Tabelle:</td><td><textarea rows="5" cols="100" name="footer_reihenfolge_bestellung" id="footer_reihenfolge_bestellung">[FOOTER_REIHENFOLGE_BESTELLUNG]</textarea><br>eigene Reihenfolge  f&uuml;r Bestellung aktivieren <input type="checkbox" name="footer_reihenfolge_bestellung_aktivieren" [FOOTER_REIHENFOLGE_BESTELLUNG_AKTIVIEREN]><br><i>Variablen: {FOOTERFREITEXT},{FOOTERTEXTVORLAGEBESTELLUNG}</i><br><br><br></td></tr>
							<tr><td>Proformarechnung Reihenfolge nach Tabelle:</td><td><textarea rows="5" cols="100" name="footer_reihenfolge_proformarechnung" id="footer_reihenfolge_proformarechnung">[FOOTER_REIHENFOLGE_PROFORMARECHNUNG]</textarea><br>eigene Reihenfolge  f&uuml;r Proformarechnung aktivieren <input type="checkbox" name="footer_reihenfolge_proformarechnung_aktivieren" [FOOTER_REIHENFOLGE_PROFORMARECHNUNG_AKTIVIEREN]><br><i>Variablen: {FOOTERVERSANDINFO},{FOOTERFREITEXT},{FOOTERTEXTVORLAGERECHNUNG},{FOOTERSTEUER},{FOOTERZAHLUNGSWEISETEXT}{FOOTERSYSTEMFREITEXT}</i></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<br>
	<input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-3');" value="Speichern" style="float:right">
</div>


<div id="tabs-4">
[MESSAGE]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset id="fieldset-mailversand-einstellungen">
						<legend>{|Versand Einstellungen|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Benutzername (E-Mail):&nbsp;</td><td><input type="text" name="benutzername" size="40" value="[BENUTZERNAME]"></td></tr>
							<tr><td>Passwort:&nbsp;</td><td><input type="password" name="passwort" size="40" value="[PASSWORT]" AUTOCOMPLETE="off"></td></tr>
							<tr><td>Postausgangsserver:&nbsp;</td><td><input type="text" name="host" size="40" value="[HOST]"></td></tr>
							<tr><td>Port:&nbsp;</td><td><input type="text" name="port" size="4" value="[PORT]"></td></tr>
							<tr><td width="50">Verschl&uuml;sselung:</td><td><select name="mailssl"><option value="0">keine</option><option value="1" [TLS]>TLS</option><option value="2" [SSL]>SSL</option></select></td></tr>
							<tr><td>Testmail Empf&auml;nger:&nbsp;</td><td><input type="text" name="testmailempfaenger" size="40" value="[TESTMAILEMPFAENGER]"></td></tr>
							<tr><td width="50">Testmail:</td><td><input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-4');" value="Speichern">&nbsp;<input type="button" value="Testmail senden" id="testmail-senden-button">&nbsp;<i>Bitte erst speichern und dann senden!</i></td></tr>
							<tr><td width="50">PHP mail() verwenden (nur in Ausnahme!):</td><td><input type="checkbox" name="mailanstellesmtp" [MAILANSTELLESMTP]></td></tr>
							<tr><td width="50">Keine SMTP Authentifizierung:</td><td><input type="checkbox" name="noauth" [NOAUTH]></td></tr>
							<!--<tr><td align="center"></td><td><input type="button" name="testmail" value="Testmail schicken (zum Account Testen)"></td></tr>-->
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
						<legend>Standard Einstellung E-Mail (bei Versand von E-Mails)</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">E-Mailadresse:</td><td><input type="text" name="email" value="[EMAIL]" size="40"></td>
							<tr><td>Name des Absenders:</td><td><input type="text" name="absendername" value="[ABSENDERNAME]" size="40"></td>
							<tr><td>Standardsignatur:</td><td><textarea name="signatur" id="signatur" rows="15" cols="80">[SIGNATUR]</textarea></td>
							<tr><td>Standard Gru&szlig;formel:</td><td><textarea name="mailgrussformel" id="mailgrussformel" rows="5" cols="80" data-lang="mailgrussformel">[MAILGRUSSFORMEL]</textarea>&nbsp;<i>Variable Absendername {MITARBEITER}</i></td>
							<tr><td>Kopie-Empfänger 1:</td><td><input type="text" name="bcc1" value="[BCC1]" size="40"></td>
							<tr><td>Kopie-Empfänger 2:</td><td><input type="text" name="bcc2" value="[BCC2]" size="40"></td>
							<tr><td>BCC:</td><td><input type="text" name="bcc3" value="[BCC3]" size="40"></td></tr>
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
						<legend>E-Mail-HTML-Template (bei Versand von E-Mails)</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">HTML Template:</td><td><textarea name="email_html_template" id="email_html_template" rows="15" cols="80">[EMAIL_HTML_TEMPLATE]</textarea></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<br>
	<input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-4');" value="Speichern" style="float:right">
</div>

<div id="tabs-6">
[MESSAGE]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Nummernkreise|}</legend>
						<table cellspacing="5" width="100%">
							<tr>
								<td width="300">N&auml;chste Angebotsnummer:</td><td><input type="text" name="next_angebot" readonly value="[NEXT_ANGEBOT]" size="40">
								<input type="button" onclick="next_number('angebot','[NEXT_ANGEBOT]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Auftragsnummer:</td><td><input type="text" name="next_auftrag" readonly value="[NEXT_AUFTRAG]" size="40">
								<input type="button" onclick="next_number('auftrag','[NEXT_AUFTRAG]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Lieferscheinnummer:</td><td><input type="text" name="next_lieferschein" readonly value="[NEXT_LIEFERSCHEIN]" size="40">
								<input type="button" onclick="next_number('lieferschein','[NEXT_LIEFERSCHEIN]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Retourennummer:</td><td><input type="text" name="next_retoure" readonly value="[NEXT_RETOURE]" size="40">
								<input type="button" onclick="next_number('retoure','[NEXT_RETOURE]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Rechnungsnummer:</td><td><input type="text" name="next_rechnung" readonly value="[NEXT_RECHNUNG]" size="40">
								<input type="button" onclick="next_number('rechnung','[NEXT_RECHNUNG]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Gutschriftnummer:</td><td><input type="text" name="next_gutschrift" readonly value="[NEXT_GUTSCHRIFT]" size="40">
								<input type="button" onclick="next_number('gutschrift','[NEXT_GUTSCHRIFT]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Bestellungsnummer:</td><td><input type="text" name="next_bestellung" readonly value="[NEXT_BESTELLUNG]" size="40">
								<input type="button" onclick="next_number('bestellung','[NEXT_BESTELLUNG]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Arbeitsnachweisnummer:</td><td><input type="text" name="next_arbeitsnachweis" readonly value="[NEXT_ARBEITSNACHWEIS]" size="40">
								<input type="button" onclick="next_number('arbeitsnachweis','[NEXT_ARBEITSNACHWEIS]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Reisekostennummer:</td><td><input type="text" name="next_reisekosten" readonly value="[NEXT_REISEKOSTEN]" size="40">
								<input type="button" onclick="next_number('reisekosten','[NEXT_REISEKOSTEN]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Produktionnummer:</td><td><input type="text" name="next_produktion" readonly value="[NEXT_PRODUKTION]" size="40">
								<input type="button" onclick="next_number('produktion','[NEXT_PRODUKTION]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Anfragenummer:</td><td><input type="text" name="next_anfrage" readonly value="[NEXT_ANFRAGE]" size="40">
								<input type="button" onclick="next_number('anfrage','[NEXT_ANFRAGE]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Preisanfragenummer:</td><td><input type="text" name="next_preisanfrage" readonly value="[NEXT_PREISANFRAGE]" size="40">
								<input type="button" onclick="next_number('preisanfrage','[NEXT_PREISANFRAGE]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Proformarechnungsnummer:</td><td><input type="text" name="next_proformarechnung" readonly value="[NEXT_PROFORMARECHNUNG]" size="40">
								<input type="button" onclick="next_number('proformarechnung','[NEXT_PROFORMARECHNUNG]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>Nächste Verbindlichkeitsnummer:</td><td><input type="text" name="next_verbindlichkeit" readonly value="[NEXT_VERBINDLICHKEIT]" size="40">
								<input type="button" onclick="next_number('verbindlichkeit','[NEXT_VERBINDLICHKEIT]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Kundennummer:</td><td><input type="text" name="next_kundennummer"  readonly value="[NEXT_KUNDENNUMMER]" size="40">
								<input type="button" onclick="next_number('kundennummer','[NEXT_KUNDENNUMMER]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Lieferantennummer:</td><td><input type="text" name="next_lieferantennummer" readonly value="[NEXT_LIEFERANTENNUMMER]" size="40">
								<input type="button" onclick="next_number('lieferantennummer','[NEXT_LIEFERANTENNUMMER]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>N&auml;chste Mitarbeiternummer:</td><td><input type="text" name="next_mitarbeiternummer" readonly value="[NEXT_MITARBEITERNUMMER]" size="40">
								<input type="button" onclick="next_number('mitarbeiternummer','[NEXT_MITARBEITERNUMMER]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>Zuletzt vergebene Artikelnummer:</td><td><input type="text" name="next_artikelnummer" readonly value="[NEXT_ARTIKELNUMMER]" size="40">
								<input type="button" onclick="next_number('artikelnummer','[NEXT_ARTIKELNUMMER]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>Zuletzt vergebene Projektnummer:</td><td><input type="text" name="next_projektnummer" readonly value="[NEXT_PROJEKTNUMMER]" size="40">
								<input type="button" onclick="next_number('projektnummer','[NEXT_PROJEKTNUMMER]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td>Zuletzt vergebene Wareneingangsnummer:</td><td><input type="text" name="next_receiptdocument" readonly value="[NEXT_RECEIPTDOCUMENT]" size="40">
								<input type="button" onclick="next_number('receiptdocument','[NEXT_RECEIPTDOCUMENT]');" value="bearbeiten"></td>
							</tr>
							<tr>
								<td width="300">Warnung doppelte Nr.:</td><td><input type="checkbox" name="warnung_doppelte_nummern" [WARNUNG_DOPPELTE_NUMMERN]></td>
							</tr>
							<tr>
								<td width="300">Warnung doppelte Seriennummern:</td><td><input type="checkbox" value="1" id="warnung_doppelte_seriennummern" name="warnung_doppelte_seriennummern" [WARNUNG_DOPPELTE_SERIENNUMMERN]></td>
							</tr>

							<!--<tr><td>Artikel: Ware</td><td><input type="text" name="next_waren" value="[NEXT_WAREN]" size="40">&nbsp;(Aktuell MAX: [NEXT_WAREN_MAX])</td></tr>
							<tr><td>Artikel: Produktion</td><td><input type="text" name="next_produktion" value="[NEXT_PRODUKTION]" size="40">&nbsp;(Aktuell MAX: [NEXT_PRODUKTION_MAX])</td></tr>
							<tr><td>Artikel: Dienstleistung/Sonstiges</td><td><input type="text" name="next_sonstiges" value="[NEXT_SONSTIGES]" size="40">&nbsp;(Aktuell MAX: [NEXT_SONSTIGES_MAX])</td></tr>-->
							<tr>
								<td>Belegnummer numerisch sortieren:</td><td><input type="checkbox" name="belegnummersortierungint" [BELEGNUMMERSORTIERUNGINT] value="1" /></td>
							</tr>
							<tr>
								<td width="300">Verfügbare Variablen:</td><td>{JAHR}, {MONAT}, {TAG}, {KW}, {ADRESSE_&lt;spaltenname&gt;}</td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<br>
	<input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-6');" value="Speichern" style="float:right">
</div>

<div id="tabs-7">
[MESSAGE]
[MESSAGEZAHLUNGSWEISEN]
<fieldset><legend>{|Zahlungsweisen|}</legend>
<table width="100%">
  <tr [TR_ZAHLUNG_RECHNUNG]><td width="50"><input type="checkbox" name="zahlung_rechnung" [ZAHLUNG_RECHNUNG]></td><td>Rechnung</td><td></td></tr>

  <tr valign="top" [TR_ZAHLUNG_RECHNUNG]><td></td><td>Satz in Rechnung:<br />(sofort) (DE)</td><td><textarea name="zahlung_rechnung_sofort_de" data-lang="zahlung_rechnung_sofort_de" rows="4" cols="80" style="width:80%">[ZAHLUNG_RECHNUNG_SOFORT_DE]</textarea><br><i>z.B. Rechnung zahlbar sofort.</i></td></tr>
	<tr valign="top" [TR_ZAHLUNG_RECHNUNG]><td></td><td>Satz in Rechnung:<br />(>= 1 Tag) (DE)</td><td><textarea name="zahlung_rechnung_de" data-lang="zahlung_rechnung_de" rows="4" cols="80" style="width:80%">[ZAHLUNG_RECHNUNG_DE]</textarea><br>

  <tr valign="top" [TR_ZAHLUNG_RECHNUNG]><td></td><td>Satz in Angebot/Auftrag:<br />(sofort) (DE)</td><td><textarea name="zahlung_auftrag_sofort_de" data-lang="zahlung_auftrag_sofort_de" rows="4" cols="80" style="width:80%">[ZAHLUNG_AUFTRAG_SOFORT_DE]</textarea><br><i>z.B. Rechnung zahlbar sofort.</i></td></tr>
	<tr valign="top" [TR_ZAHLUNG_RECHNUNG]><td></td><td>Satz in Angebot/Auftrag:<br />(>= 1 Tag) (DE)</td><td><textarea name="zahlung_auftrag_de" data-lang="zahlung_auftrag_de" rows="4" cols="80" style="width:80%">[ZAHLUNG_AUFTRAG_DE]</textarea><br>


	<i>z.B. Rechnung zahlbar innerhalb {ZAHLUNGSZIELTAGE}.</i></td></tr>
  <tr [TR_ZAHLUNG_RECHNUNG]><td><br /></td></tr>
  <tr [TR_ZAHLUNG_RECHNUNG]><td width="50"><input type="checkbox" name="eigener_skontotext" [EIGENER_SKONTOTEXT]></td><td colspan="2">Eigener Skontotext (wenn Skonto > 0%)</td></tr>
  <tr valign="top" [TR_ZAHLUNG_RECHNUNG]><td></td><td>Satz in Angebot/Auftrag (DE):</td><td><textarea name="eigener_skontotext_anab" data-lang="eigener_skontotext_anab" rows="4" cols="80" style="width:80%">[EIGENER_SKONTOTEXT_ANAB]</textarea><br><i>z.B. Skonto {ZAHLUNGSZIELSKONTO}% innerhalb {ZAHLUNGSZIELTAGESKONTO} Tage.</i></td></tr>
	<tr valign="top" [TR_ZAHLUNG_RECHNUNG]><td></td><td>Satz in Rechnung (DE):</td><td><textarea name="eigener_skontotext_re" data-lang="eigener_skontotext_re" rows="4" cols="80" style="width:80%">[EIGENER_SKONTOTEXT_RE]</textarea><br>
	<i>z.B. Skonto {ZAHLUNGSZIELSKONTO}% innerhalb {ZAHLUNGSZIELTAGESKONTO} Tage bis zum {ZAHLUNGSZIELSKONTODATUM}.</i></td></tr>
  <tr valign="top" [TR_ZAHLUNG_RECHNUNG]><td></td><td></td><td><br><i>Variabeln: {ZAHLUNGBISDATUM}, {ZAHLUNGSZIELTAGE}, {ZAHLUNGSZIELSKONTO}, {ZAHLUNGSZIELSKONTOTOTAL}, {ZAHLUNGSZIELTAGESKONTO}, {ZAHLUNGSZIELSKONTODATUM}, {SOLL}, {SOLLMITSKONTO}, {SKONTOBETRAG}, {SKONTOFAEHIG},{SKONTOFAEHIGNETTO}, {BELEGNR}, {NAME}, {STEUERNORMAL}, {GESAMTNETTO}, {GESAMTNETTONORMAL}, {STEUERERMAESSIGT}, {GESAMTNETTOERMAESSIGT}, {WAEHRUNG}</i></td></tr>
  <tr><td></td><td>Rechnung Zahlungsziel in Tage:</td><td><input type="text" name="zahlungszieltage" size="10" value="[ZAHLUNGSZIELTAGE]"></td></tr>
  <tr><td></td><td>Skonto in Tage:</td><td><input type="text" name="zahlungszieltageskonto" size="10" value="[ZAHLUNGSZIELTAGESKONTO]"></td></tr>
  <tr><td></td><td>Skonto in Prozent:</td><td><input type="text" name="zahlungszielskonto" size="10" value="[ZAHLUNGSZIELSKONTO]"></td></tr>

  <tr [TR_ZAHLUNG_VORKASSE]><td><input type="checkbox" name="zahlung_vorkasse" [ZAHLUNG_VORKASSE]></td><td>Vorkasse</td><td></td></tr>
  <tr  valign="top" [TR_ZAHLUNG_VORKASSE]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_vorkasse_de" data-lang="zahlung_vorkasse_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_VORKASSE_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>

  <tr [TR_ZAHLUNG_NACHNAHME]><td><input type="checkbox" name="zahlung_nachnahme" [ZAHLUNG_NACHNAHME]></td><td>Nachnahme</td><td></td></tr>
 <tr valign="top" [TR_ZAHLUNG_NACHNAHME]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_nachnahme_de" data-lang="zahlung_nachnahme_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_NACHNAHME_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>


  <tr [TR_ZAHLUNG_LASTSCHRIFT]><td><input type="checkbox" name="zahlung_lastschrift" [ZAHLUNG_LASTSCHRIFT]></td><td>Lastschrift</td><td>
</td></tr>
 <tr valign="top" [TR_ZAHLUNG_LASTSCHRIFT]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_lastschrift_de" data-lang="zahlung_lastschrift_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_LASTSCHRIFT_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i><!--<br><input type="checkbox" value="1" name="zahlung_lastschrift_konditionen" [ZAHLUNG_LASTSCHRIFT_KONDITIONEN]>&nbsp;Einzugsdatum berechnen anhand Rechnungskonditionen bei Kunden--></td></tr>

  <tr [TR_ZAHLUNG_BAR]><td><input type="checkbox" name="zahlung_bar" [ZAHLUNG_BAR]></td><td>Barzahlung</td><td></td></tr>
<tr valign="top" [TR_ZAHLUNG_BAR]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_bar_de" data-lang="zahlung_bar_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_BAR_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>


  <tr [TR_ZAHLUNG_PAYPAL]><td><input type="checkbox" name="zahlung_paypal" [ZAHLUNG_PAYPAL]></td><td>Paypal</td><td></td></tr>
<tr  valign="top" [TR_ZAHLUNG_PAYPAL]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_paypal_de" data-lang="zahlung_paypal_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_PAYPAL_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>

  <tr [TR_ZAHLUNG_KREDITKARTE]><td><input type="checkbox" name="zahlung_kreditkarte" [ZAHLUNG_KREDITKARTE]></td><td>Kreditkarte</td><td></td></tr>
<tr  valign="top" [TR_ZAHLUNG_KREDITKARTE]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_kreditkarte_de" data-lang="zahlung_kreditkarte_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_KREDITKARTE_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>


  <tr [TR_ZAHLUNG_AMAZON]><td><input type="checkbox" name="zahlung_amazon" [ZAHLUNG_AMAZON]></td><td>Amazon Payments</td><td></td></tr>
<tr  valign="top" [TR_ZAHLUNG_AMAZON]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_amazon_de" data-lang="zahlung_amazon_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_AMAZON_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>


  <tr [TRZAHLUNG_AMAZON_BESTELLUNG]><td><input type="checkbox" name="zahlung_amazon_bestellung" [ZAHLUNG_AMAZON_BESTELLUNG]></td><td>Amazon Bestellung</td><td></td></tr>
<tr  valign="top" [TRZAHLUNG_AMAZON_BESTELLUNG]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_amazon_bestellung_de" data-lang="zahlung_amazon_bestellung_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_AMAZON_BESTELLUNG_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>



  <tr [TRZAHLUNG_SECUPAY]><td><input type="checkbox" name="zahlung_secupay" [ZAHLUNG_SECUPAY]></td><td>Secupay</td><td></td></tr>
<tr  valign="top" [TRZAHLUNG_SECUPAY]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_secupay_de" data-lang="zahlung_secupay_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_SECUPAY_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>



  <tr [TRZAHLUNG_SOFORTUEBERWEISUNG]><td><input type="checkbox" name="zahlung_sofortueberweisung" [ZAHLUNG_SOFORTUEBERWEISUNG]></td><td>Sofort&uuml;berweisung</td><td></td></tr>
<tr  valign="top" [TRZAHLUNG_SOFORTUEBERWEISUNG]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_sofortueberweisung_de" data-lang="zahlung_sofortueberweisung_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_SOFORTUEBERWEISUNG_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>



  <tr [TRZAHLUNG_RATENZAHLUNG]><td><input type="checkbox" name="zahlung_ratenzahlung" [ZAHLUNG_RATENZAHLUNG]></td><td>Ratenzahlung</td><td></td></tr>
<tr  valign="top" [TRZAHLUNG_RATENZAHLUNG]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_ratenzahlung_de" data-lang="zahlung_ratenzahlung_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_RATENZAHLUNG_DE]</textarea><br>
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>


  <tr [TRZAHLUNG_ECKARTE]><td><input type="checkbox" name="zahlung_eckarte" [ZAHLUNG_ECKARTE]></td><td>EC-Karte</td><td></td></tr>
<tr  valign="top" [TRZAHLUNG_ECKARTE]><td></td><td>Zahlungsbedingungen:</td><td><textarea name="zahlung_eckarte_de" data-lang="zahlung_eckarte_de" rows="8" cols="80" style="width:80%">[ZAHLUNG_ECKARTE_DE]</textarea><br />
<i>Dieser Text erscheint auf dem Angebot, Auftrag und der Rechnung.</i></td></tr>



</table>

</fieldset>

<br><center><input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-7');" value="Speichern"></center>
</div>


<div id="tabs-8">
[MESSAGE]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>Steuersätze</legend>
						<table width="100%">
							<tr><td width="300">Steuersatz (normal):</td><td><input type="text" name="steuersatz_normal" id="steuersatz_normal" size="10" value="[STEUERSATZNORMAL]"></td></tr>
							<tr><td width="300">Steuersatz (erm&auml;&szlig;igt):</td><td><input type="text" name="steuersatz_ermaessigt" id="steuersatz_ermaessigt" size="10" value="[STEUERSATZERMAESSIGT]"></td></tr>
							<tr><td>Weiterf&uuml;hren von Belegen:</td><td>
										<select name="taxfromdoctypesettings" id="taxfromdoctypesettings">
											<option value="0">Steuern aus Beleg zuvor (Empfehlung)</option>
											<option value="1" [OPTIONTAXFROMDOCTYPESETTINGS]>Steuern immer aus Einstellungen</option>
										</select>
									</td>
							</tr>
							<tr><td>Kleinunternehmer:</td><td><input type="checkbox" name="kleinunternehmer" [KLEINUNTERNEHMER]></td></tr>
							<tr><td>Steuerfrei Inland:</td><td><input type="checkbox" name="steuerfrei_inland_ausblenden" [STEUERFREI_INLAND_AUSBLENDEN]></td></tr>
							<tr><td>Steuerspalte ausblenden:</td><td><input type="checkbox" name="steuerspalteausblenden" [STEUERSPALTEAUSBLENDEN]></td></tr>
							<tr><td>Immer Netto Rechnungen:</td><td><input type="checkbox" id="immernettorechnungen" name="immernettorechnungen" [IMMERNETTORECHNUNGEN]></td></tr>
							<tr><td>Immer Brutto Rechnungen:</td><td><input type="checkbox" id="immerbruttorechnungen" name="immerbruttorechnungen" [IMMERBRUTTORECHNUNGEN]></td></tr>
							<tr><td>Belege mit 4 Nachkommastellen:</td><td><input type="checkbox" name="viernachkommastellen_belege" [VIERNACHKOMMASTELLEN_BELEGE]></td></tr>
							<tr><td>Standard Zahlungsweise Kunde:</td><td><select name="zahlungsweise">[ZAHLUNGSWEISE]</select></td></tr>
							<tr><td>Standard Zahlungsweise Lieferant:</td><td><select name="zahlungsweiselieferant">[ZAHLUNGSWEISELIEFERANT]</select></td></tr>
							<tr><td>Standard Versandart:</td><td><select name="versandart">[VERSANDART]</select></td></tr>
							<!--<tr><td>Positionen kaufmännisch Runden:</td><td><input type="checkbox" name="positionenkaufmaenischrunden" [POSITIONENKAUFMAENISCHRUNDEN]>&nbsp;<i>In AN,AB,RE und GS werden die Positionen gerundet bevor die Gesamtsumme gebildet wird.</i></td></tr>-->
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>W&auml;hrung</legend>
						<table width="100%">
							<tr><td width="300">Standard W&auml;hrung:</td><td><select name="waehrung">[WAEHRUNG]</select></td></tr>
							<tr><td>W&auml;hrungskurs laden:</td><td><input type="checkbox" name="loadcurrencyrate" [LOADCURRENCYRATE]></td></tr>
							<tr><td>Wechselkurs aus Rechnung für Gutschrift/[BEZEICHNUNGSTORNORECHNUNG]:</td><td><input type="checkbox" name="gutschriftkursvonrechnung" [GUTSCHRIFTKURSVONRECHNUNG]></td></tr>
							<tr><td>Standard Marge in %:</td><td><input type="text" name="standardmarge" size="5" value="[STANDARDMARGE]"></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	<div class="row" id="wizard-account-system">
		<div class="row-height">
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Finanzbuchhaltung Export Kontenrahmen|}</legend>
						<table>
							<tr>
								<td width="300"></td><td>Erl&ouml;se</td>
							</tr>
							<tr>
								<td width="300">Inland (normal):</td><td><input type="text" name="steuer_erloese_inland_normal" size="10" value="[STEUER_ERLOESE_INLAND_NORMAL]"></td>
							</tr>
						 	<tr>
								<td width="300">Inland (erm&auml;&szlig;igt):</td><td><input type="text" name="steuer_erloese_inland_ermaessigt" size="10" value="[STEUER_ERLOESE_INLAND_ERMAESSIGT]"></td>
							</tr>
							<tr>
								<td width="300">Inland (steuerfrei):</td><td><input type="text" name="steuer_erloese_inland_nichtsteuerbar" size="10" value="[STEUER_ERLOESE_INLAND_NICHTSTEUERBAR]"></td>
							</tr>
							<!--<tr>
								<td width="300">Inland (steuerfrei)</td><td><input type="text" name="steuer_erloese_inland_steuerfrei" size="10" value="[STEUER_ERLOESE_INLAND_STEUERFREI]"></td>
								<td width="300">Inland (steuerfrei)</td><td><input type="text" name="steuer_aufwendung_inland_steuerfrei" size="10" value="[STEUER_AUFWENDUNG_INLAND_STEUERFREI]"></td>
							</tr>-->
							<tr>
								<td width="300">Innergemeinschaftlich EU:</td><td><input type="text" name="steuer_erloese_inland_innergemeinschaftlich" size="10" value="[STEUER_ERLOESE_INLAND_INNERGEMEINSCHAFTLICH]"></td>
							</tr>
							<tr>
								<td width="300">EU (normal):</td><td><input type="text" name="steuer_erloese_inland_eunormal" size="10" value="[STEUER_ERLOESE_INLAND_EUNORMAL]"></td>
							</tr>
							<tr>
							  <td width="300">EU (erm&auml;&szlig;igt):</td><td><input type="text" name="steuer_erloese_inland_euermaessigt" size="10" value="[STEUER_ERLOESE_INLAND_EUERMAESSIGT]"></td>
							</tr>
						  <tr>
								<td width="300">Export:</td><td><input type="text" name="steuer_erloese_inland_export" size="10" value="[STEUER_ERLOESE_INLAND_EXPORT]"></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend></legend>
						<table>
							<tr>
								<td width="300"></td><td>Aufwendungen</td>
							</tr>
							<tr>
								<td width="300">Inland (normal):</td><td><input type="text" name="steuer_aufwendung_inland_normal" size="10" value="[STEUER_AUFWENDUNG_INLAND_NORMAL]"></td>
							</tr>
							<tr>
								<td width="300">Inland (erm&auml;&szlig;igt):</td><td><input type="text" name="steuer_aufwendung_inland_ermaessigt" size="10" value="[STEUER_AUFWENDUNG_INLAND_ERMAESSIGT]"></td>
							</tr>
							<tr>
								<td width="300">Inland (steuerfrei):</td><td><input type="text" name="steuer_aufwendung_inland_nichtsteuerbar" size="10" value="[STEUER_AUFWENDUNG_INLAND_NICHTSTEUERBAR]"></td>
							</tr>
							<tr>
								<td width="300">Innergemeinschaftlich EU:</td><td><input type="text" name="steuer_aufwendung_inland_innergemeinschaftlich" size="10" value="[STEUER_AUFWENDUNG_INLAND_INNERGEMEINSCHAFTLICH]"></td>
							</tr>
							<tr>
								<td width="300">EU (normal):</td><td><input type="text" name="steuer_aufwendung_inland_eunormal" size="10" value="[STEUER_AUFWENDUNG_INLAND_EUNORMAL]"></td>
							</tr>
							<tr>
								<td width="300">EU (erm&auml;&szlig;igt):</td><td><input type="text" name="steuer_aufwendung_inland_euermaessigt" size="10" value="[STEUER_AUFWENDUNG_INLAND_EUERMAESSIGT]"></td>
							</tr>
							<tr>
								<td width="300">Import:</td><td><input type="text" name="steuer_aufwendung_inland_import" size="10" value="[STEUER_AUFWENDUNG_INLAND_IMPORT]"></td>
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
						<legend>{|Finanzbuchhaltung Export Einstellungen|}</legend>
						<table width="100%">
							<tr>
								<td width="300">Standard Konto:</td><td colspan="3"><input type="text" name="steuer_standardkonto" size="10" value="[STEUER_STANDARDKONTO]"></td>
							</tr>
							<tr>
								<td width="300">Aufwendungen Standard Konto:</td><td colspan="3"><input type="checkbox" name="steuer_standardkonto_aufwendungen" value="1" [STEUER_STANDARDKONTO_AUFWENDUNGEN]></td>
							</tr>
							<!--<tr>
								<td width="300">Export nach Positionen</td><td colspan="3"><input type="checkbox" name="steuer_positionen_export" value="1" [STEUER_POSITIONEN_EXPORT]"></td>
							</tr>-->
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset><legend>{|Finanzbuchhaltung Export Kundennummer|}</legend>
						<table width="100%">
							<tr>
								<td width="300">Anpassung Kundennummer:</td><td colspan="3"><input type="text" name="steuer_anpassung_kundennummer" size="20" value="[STEUER_ANPASSUNG_KUNDENNUMMER]"></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	<!--
	<fieldset><legend>Finanzbuchhaltung Export Kontenrahmen - Weitere Kostenarten</legend>
	<table>
	<tr><td width="300">Nr.</td><td>Bezeichnung</td><td>Steuer<br>(normal)</td><td>Steuer<br>(erm&auml;&szlig;igt)</td><td>Steuer<br>(steuerfrei)</td></tr>
	<tr><td>Kostenart 1</td><td><input type="text" size="30" name="steuer_art_1" value="[STEUER_ART_1]"></td>
				<td><input type="text" size="10" name="steuer_art_1_normal" value="[STEUER_ART_1_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_1_ermaessigt" value="[STEUER_ART_1_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_1_steuerfrei" value="[STEUER_ART_1_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 2</td><td><input type="text" size="30" name="steuer_art_2" value="[STEUER_ART_2]"></td>
				<td><input type="text" size="10" name="steuer_art_2_normal" value="[STEUER_ART_2_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_2_ermaessigt" value="[STEUER_ART_2_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_2_steuerfrei" value="[STEUER_ART_2_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 3</td><td><input type="text" size="30" name="steuer_art_3" value="[STEUER_ART_3]"></td>
				<td><input type="text" size="10" name="steuer_art_3_normal" value="[STEUER_ART_3_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_3_ermaessigt" value="[STEUER_ART_3_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_3_steuerfrei" value="[STEUER_ART_3_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 4</td><td><input type="text" size="30" name="steuer_art_4" value="[STEUER_ART_4]"></td>
				<td><input type="text" size="10" name="steuer_art_4_normal" value="[STEUER_ART_4_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_4_ermaessigt" value="[STEUER_ART_4_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_4_steuerfrei" value="[STEUER_ART_4_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 5</td><td><input type="text" size="30" name="steuer_art_5" value="[STEUER_ART_5]"></td>
				<td><input type="text" size="10" name="steuer_art_5_normal" value="[STEUER_ART_5_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_5_ermaessigt" value="[STEUER_ART_5_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_5_steuerfrei" value="[STEUER_ART_5_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 6</td><td><input type="text" size="30" name="steuer_art_6" value="[STEUER_ART_6]"></td>
				<td><input type="text" size="10" name="steuer_art_6_normal" value="[STEUER_ART_6_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_6_ermaessigt" value="[STEUER_ART_6_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_6_steuerfrei" value="[STEUER_ART_6_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 7</td><td><input type="text" size="30" name="steuer_art_7" value="[STEUER_ART_7]"></td>
				<td><input type="text" size="10" name="steuer_art_7_normal" value="[STEUER_ART_7_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_7_ermaessigt" value="[STEUER_ART_7_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_7_steuerfrei" value="[STEUER_ART_7_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 8</td><td><input type="text" size="30" name="steuer_art_8" value="[STEUER_ART_8]"></td>
				<td><input type="text" size="10" name="steuer_art_8_normal" value="[STEUER_ART_8_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_8_ermaessigt" value="[STEUER_ART_8_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_8_steuerfrei" value="[STEUER_ART_8_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 9</td><td><input type="text" size="30" name="steuer_art_9" value="[STEUER_ART_9]"></td>
				<td><input type="text" size="10" name="steuer_art_9_normal" value="[STEUER_ART_9_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_9_ermaessigt" value="[STEUER_ART_9_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_9_steuerfrei" value="[STEUER_ART_9_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 10</td><td><input type="text" size="30" name="steuer_art_10" value="[STEUER_ART_10]"></td>
				<td><input type="text" size="10" name="steuer_art_10_normal" value="[STEUER_ART_10_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_10_ermaessigt" value="[STEUER_ART_10_STEUERFREI]"></td>
				<td><input type="text" size="10" name="steuer_art_10_steuerfrei" value="[STEUER_ART_10_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 11</td><td><input type="text" size="30" name="steuer_art_11" value="[STEUER_ART_11]"></td>
				<td><input type="text" size="10" name="steuer_art_11_normal" value="[STEUER_ART_11_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_11_ermaessigt" value="[STEUER_ART_11_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_11_steuerfrei" value="[STEUER_ART_11_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 12</td><td><input type="text" size="30" name="steuer_art_12" value="[STEUER_ART_12]"></td>
				<td><input type="text" size="10" name="steuer_art_12_normal" value="[STEUER_ART_12_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_12_ermaessigt" value="[STEUER_ART_12_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_12_steuerfrei" value="[STEUER_ART_12_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 13</td><td><input type="text" size="30" name="steuer_art_13" value="[STEUER_ART_13]"></td>
				<td><input type="text" size="10" name="steuer_art_13_normal" value="[STEUER_ART_13_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_13_ermaessigt" value="[STEUER_ART_13_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_13_steuerfrei" value="[STEUER_ART_13_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 14</td><td><input type="text" size="30" name="steuer_art_14" value="[STEUER_ART_14]"></td>
				<td><input type="text" size="10" name="steuer_art_14_normal" value="[STEUER_ART_14_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_14_ermaessigt" value="[STEUER_ART_14_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_14_steuerfrei" value="[STEUER_ART_14_STEUERFREI]"></td>
				</tr>
	<tr><td>Kostenart 15</td><td><input type="text" size="30" name="steuer_art_15" value="[STEUER_ART_15]"></td>
				<td><input type="text" size="10" name="steuer_art_15_normal" value="[STEUER_ART_15_NORMAL]"></td>
				<td><input type="text" size="10" name="steuer_art_15_ermaessigt" value="[STEUER_ART_15_ERMAESSIGT]"></td>
				<td><input type="text" size="10" name="steuer_art_15_steuerfrei" value="[STEUER_ART_15_STEUERFREI]"></td>
				</tr>
	</table>
	</fieldset>
	-->

	<br>
	<input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-8');" value="Speichern" style="float:right">
</div>

<div id="tabs-9">
[MESSAGE]
[MESSAGEMAILS]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Projekt|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Hauptprojekt:</td><td>[PROJEKTAUTOSTART]<input type="text" name="projekt" id="projekt" value="[PROJEKT]" size="40">[PROJEKTAUTOENDE]&nbsp;</td></tr>
							<tr><td>Projekt &ouml;ffentlich setzen bei Anlage:</td><td><input type="checkbox" name="projektoeffentlich" id="projektoeffentlich" [PROJEKTOEFFENTLICH]></td></tr>
							<tr>
								<td>Projektrechte nur Mitarbeiterrolle auswerten:</td>
								<td><input type="checkbox" name="onlyemployeeprojects" id="onlyemployeeprojects" [ONLYEMPLOYEEPROJECTS] /></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Artikel|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Erweiterte Artikelsuche:</td><td><input type="checkbox" name="artikel_suche_kurztext" [ARTIKELSUCHEKURZTEXT]><i>Suche auch im Kurztext DE</i></td></tr>
							<tr><td width="300">Erweiterte Artikelsuche:</td><td><input type="checkbox" name="artikel_suche_variante_von" [ARTIKELSUCHEVARIANTEVON]><i>Suche auch nach Varianten</i></td></tr>
							<tr><td width="300">Erweiterte Artikelsuche:</td><td><input type="checkbox" name="artikel_freitext1_suche" [ARTIKEL_FREITEXT1_SUCHE]><i>Suche in Freifeld 1 und Freifeld 2</i></td></tr>
							<tr><td width="300">Erweiterte Artikelsuche:</td><td><input type="checkbox" name="artikel_artikelnummer_suche" [ARTIKEL_ARTIKELNUMMER_SUCHE]><i>Suche auch in Artikelnummern von Kunden und Lieferanten </i></td></tr>
							<tr><td width="300">Beschleunigte Artikelsuche:</td><td><input type="checkbox" name="artikel_beschleunigte_suche" [ARTIKEL_BESCHLEUNIGTE_SUCHE]></td></tr>
							<tr><td width="300">Artikelbilder in &Uuml;bersicht:</td><td><input type="checkbox" name="artikel_bilder_uebersicht" [ARTIKEL_BILDER_UEBERSICHT]></td></tr>
							<tr><td width="300">Artikelbaum in &Uuml;bersicht:</td><td><input type="checkbox" name="artikel_baum_uebersicht" [ARTIKEL_BAUM_UEBERSICHT]></td></tr>
							<tr><td width="300">Gewicht aus JIT-Stückliste:</td><td><input type="checkbox" name="stuecklistegewichtnurartikel" [STUECKLISTEGEWICHTNURARTIKEL]><i>Nur das Gewicht aus dem Hauptartikel der JIT-Stückliste verwenden (ansonsten werden die Gewichte von Haupt- und Unterartikel aufaddiert).</i></td></tr>
							<tr><td width="300">Standard Artikel Einheit:</td><td><input type="text" name="artikeleinheit_standard" value="[ARTIKELEINHEITSTANDARD]">&nbsp;</td></tr>
							<tr><td width="300">Gewichtseinheit:</td><td><input type="text" name="gewichtbezeichnung" value="[GEWICHTBEZEICHNUNG]">&nbsp;</td></tr>
							<tr><td width="300">Umrechnungsfaktor Gewicht:</td><td><input type="text" name="gewichtzukgfaktor" value="[GEWICHTZUKGFAKTOR]">&nbsp;</td></tr>
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
						<legend>{|Produktion|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Verhalten Weiterf&uuml;hren Auftrag zur Produktion:</td><td><select name="produktionsverhalten">[PRODUKTIONSVERHALTEN]</select></td></tr>
							<tr><td>Produktion nicht automatisch starten beim Weiterf&uuml;hren:</td><td><input type="checkbox" name="disableproductionautostart" [DISABLEPRODUCTIONAUTOSTART]></td></tr>
							<tr><td>{|Unterproduktionen mit Hauptproduktion abschlie&szlig;en|}:</td><td><input type="checkbox" name="closesubproductions" [CLOSESUBPRODUCTIONS]></td></tr>
							<tr><td width="300">Produktionskorrektur nicht verwenden:</td><td><input type="checkbox" name="produktionskorrektur_nichtverwenden" [PRODUKTIONSKORREKTUR_NICHTVERWENDEN]></td></tr>
							<tr><td>Für externe Produktionsartikel keine Produktionen anlegen:</td><td><input type="checkbox" name="disablecreateproductiononextern" [DISABLECREATEPRODUCTIONONEXTERN]></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Wareneingang|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td>Annahme mit Kamera/Waage:</td><td><input type="checkbox" name="wareneingang_kamera_waage" [WARENEINGANG_KAMERA_WAAGE]> </td></tr>
							<tr><td>Wareneingang mit Zwischenlager:</td><td><input type="checkbox" name="wareneingang_zwischenlager" [WARENEINGANG_ZWISCHENLAGER]> </td></tr>
							<tr><td>Auftr&auml;ge zu Bestellungen im Wareneingang anzeigen:</td><td><input type="checkbox" name="wareneingangauftragzubestellung" [WARENEINGANGAUFTRAGZUBESTELLUNG]></td></tr>
							<tr><td>{|Bildtyp beim Upload im Wareneingang|}:</td><td><select name="wareneingangbildtypvorauswahl" id="wareneingangbildtypvorauswahl">[WARENEINGANBILDTYPVORAUSWAHL]</select></td></tr>
							<tr><td>{|Verhalten beim Scannen im Wareneingang|}:</td><td><select name="wareneingangscanverhalten" id="wareneingangscanverhalten">[WARENEINGANGSCANVERHALTENAUSWAHL]</select></td></tr>
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
						<legend>{|Drucker|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Standard Drucker:</td><td><select name="standardversanddrucker">[STANDARDVERSANDDRUCKER]</select>&nbsp;</td></tr>
							<tr><td width="300">Standard Etikettendrucker:</td><td><select name="standardetikettendrucker">[STANDARDETIKETTENDRUCKER]</select>&nbsp;</td></tr>
							<tr><td width="300">Etikettendrucker Wareneingang:</td><td><select name="etikettendrucker_wareneingang">[ETIKETTENDRUCKERWARENEINGANG]</select>&nbsp;</td></tr>
							<tr><td width="300">DMS Etiketten Wareneingang:</td><td><input type="checkbox" name="wareneingangdmsdrucker" id="wareneingangdmsdrucker" [WARENEINGANGDMSDRUCKER]>&nbsp;</td></tr>
							<tr><td width="300">Aufgaben Bondrucker:</td><td><select name="aufgaben_bondrucker">[AUFGABEN_BONDRUCKER]</select>&nbsp;</td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Sonstiges|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Kleine Aufl&ouml;sung:</td><td><input type="checkbox" name="standardaufloesung" id="standardaufloesung" [STANDARDAUFLOESUNG]>&nbsp;</td></tr>
							<tr><td>Paketmarke mit Waage:</td><td><input type="checkbox" name="paketmarke_mit_waage" [PAKETMARKE_MIT_WAAGE]></td></tr>
							<tr><td>{|Sprache bevorzugen|}:</td><td><select name="sprachebevorzugen" id="sprachebevorzugen">[SPRACHEBEVORZUGEN]</select></td></tr>
							<tr><td width="300">Export Button (CSV, PDF, Clipboard) unter Live-Tabelle:</td><td><input type="checkbox" name="datatables_export_button_flash" [DATATABLES_EXPORT_BUTTON_FLASH]></td></tr>
							<tr><td width="300">Bestellvorschlag Menge:</td><td><input type="checkbox" name="bestellvorschlaggroessernull" [BESTELLVORSCHLAGSGROESSERNULL]></td></tr>
							<tr><td>Mahnwesen mit Kontoabgleich:</td><td><input type="checkbox" name="mahnwesenmitkontoabgleich" [MAHNWESENMITKONTOABGLEICH]> </td></tr>
							<tr><td width="300">Anzahl Einträge Live-Tabelle:</td><td><input type="text" name="standard_datensaetze_datatables" size="10" value="[STANDARD_DATENSAETZE_DATATABLES]">&nbsp;</td></tr>
							<tr><td width="300">Erweiterte Adresssuche:</td><td><input type="checkbox" name="adresse_freitext1_suche" [ADRESSE_FREITEXT1_SUCHE]></td></tr>
							<tr><td width="300">Wiedervorlage - nur Mitarbeiter wählbar:</td><td><input type="checkbox"  name="wiedervorlage_mitarbeiter" [WIEDERVORLAGE_MITARBEITER]>&nbsp;</td></tr>
							<tr><td width="300">{|Artikelbild im Versandzentrum anzeigen|}:</td><td><input type="checkbox" name="versandzentrum_bild" [VERSANDZENTRUM_BILD] />&nbsp;</td></tr>
							<tr><td width="300">{|Gruppe für Bearbeiter|}:</td><td><input type="text" id="group_employee" name="group_employee" value="[GROUP_EMPLOYEE]" /></td></tr>
							<tr><td width="300">{|Gruppe für Vertrieb|}:</td><td><input type="text" id="group_sales" name="group_sales" value="[GROUP_SALES]" /></td></tr>
							<tr><td>{|Auto. Proformarechnung bei Export &quot;nur Lagerartikel&quot;|}:</td><td><input type="checkbox" name="proformainvoice_juststorgearticles" [PROFORMAINVOICE_JUSTSTORGEARTICLES] /></td></tr>
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
						<legend>{|Belege|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Belege schnell anlegen ohne Zwischentabelle:</td><td><input type="checkbox" name="schnellanlegen" [SCHNELLANLEGEN]></td></tr>
							<tr><td width="300">Belege schnell anlegen ohne manuelle Freigabe:</td><td><input type="checkbox" name="schnellanlegen_ohnefreigabe" [SCHNELLANLEGEN_OHNEFREIGABE]></td></tr>
							<tr><td width="300">Freigabe mit einem Klick:</td><td><input type="checkbox" name="oneclickrelease" [ONECLICKRELEASE]></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Auswahl g&uuml;nstigster Verkaufspreise:</td><td><input type="checkbox" name="guenstigste_vk" [GUENSTIGSTE_VK]>&nbsp;</td></tr>
							<tr><td width="300">Erweiterte Positionsansicht:</td><td><input type="checkbox" name="erweiterte_positionsansicht" [ERWEITERTE_POSITIONSANSICHT]></td></tr>
							<tr><td width="300">Lagerbestand in Belegpositionen anzeigen:</td><td><input type="checkbox" name="lagerbestand_in_auftragspositionen_anzeigen" [LAGERBESTAND_IN_AUFTRAGSPOSITIONEN_ANZEIGEN]>&nbsp;</td></tr>
							<tr><td>Staffelpreise neu laden falls Menge ge&auml;ndert wird:</td><td><input type="checkbox" value="1" id="position_quantity_change_price_update" name="position_quantity_change_price_update" [POSITION_QUANTITY_CHANGE_PRICE_UPDATE] /></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Verkaufspreise auf VPE anpassen:</td><td><input type="checkbox" name="verkaufspreisevpe" [VERKAUFSPREISEVPE]></td></tr>
							<tr><td width="300">Einkaufspreise auf VPE anpassen:</td><td><input type="checkbox" name="einkaufspreisevpe" [EINKAUFSPREISEVPE]></td></tr>
							<tr><td width="300">Porto berechnen:</td><td><input type="checkbox" name="porto_berechnen" [PORTO_BERECHNEN]></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Dienstleistungsartikel nicht zu Lieferschein &uuml;bernehmen:</td><td><input type="checkbox" name="dienstleistungsartikel_nicht_zu_lieferschein" [DIENSTLEISTUNGSARTIKEL_NICHT_ZU_LIEFERSCHEIN]>&nbsp;</td></tr>
							<tr><td>Lieferschein Freitext / Interne Bemerkung:</td><td><input type="checkbox" name="versand_gelesen" [VERSAND_GELESEN]> </td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Abmessung im Dokument:</td><td><input type="checkbox" name="abmessungimdokument" [ABMESSUNGIMDOKUMENT]></td></tr>
							<tr><td width="300">Adresse Typ im Dokument:</td><td><input type="checkbox" name="typimdokument" [TYPIMDOKUMENT]></td></tr>
							<tr><td width="300">Artikel Einheit im Dokument:</td><td><input type="checkbox" name="artikeleinheit" [ARTIKELEINHEIT]></td></tr>
							<tr><td width="300">Bearbeiter Telefon im Dokument:</td><td><input type="checkbox" name="bearbeitertelefonimdokument" [BEARBEITERTELEFONIMDOKUMENT]></td></tr>
							<tr><td width="300">Bearbeiter E-Mail im Dokument:</td><td><input type="checkbox" name="bearbeiteremailimdokument" [BEARBEITEREMAILIMDOKUMENT]></td></tr>
							<tr><td width="300">Herstellernummer im Dokument:</td><td><input type="checkbox" name="herstellernummerimdokument" [HERSTELLERNUMMERIMDOKUMENT]></td></tr>
							<tr><td width="300">Projekt im Dokument:</td><td><input type="checkbox" name="projektnummerimdokument" [PROJEKTNUMMERIMDOKUMENT]></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Dateien beim Weiterführen übernehmen:</td><td><input type="checkbox" name="dateienweiterfuehren" [DATEIENWEITERFUEHREN]></td></tr>
							<tr><td width="300">Original Beleg-PDF beim Versenden als Anhang einfügen:</td><td><input type="checkbox" name="belegeinanhang" [BELEGEINANHANG]></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Positionen mit Freifelder:</td><td><input type="checkbox" name="freifelderimdokument" [FREIFELDERIMDOKUMENT]></td></tr>
							<tr><td width="300">Positionen mit EAN:</td><td><input type="checkbox" name="beleg_pos_ean" [BELEG_POS_EAN]></td></tr>
							<tr><td width="300">Positionen mit Zolltarifnummer:</td><td><input type="checkbox" name="beleg_pos_zolltarifnummer" [BELEG_POS_ZOLLTARIFNUMMER]></td></tr>
							<tr><td width="300">Positionen mit MHD:</td><td><input type="checkbox" name="beleg_pos_mhd" [BELEG_POS_MHD]></td></tr>
							<tr><td width="300">Positionen mit Charge:</td><td><input type="checkbox" name="beleg_pos_charge" [BELEG_POS_CHARGE]></td></tr>
							<tr><td width="300">Positionen mit Seriennummern:</td><td><input type="checkbox" name="beleg_pos_sn" [BELEG_POS_SN]></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Herkunftsland immer ausblenden:</td><td><input type="checkbox" name="beleg_pos_herkunftsland" [BELEG_POS_HERKUNFTSLAND]></td></tr>
							<tr><td width="300">Lieferdatum in KW:</td><td><input type="checkbox" name="lieferdatumkw" [LIEFERDATUMKW]></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Artikelbild in Auftrag PDF anzeigen:</td><td><input type="checkbox" name="beleg_artikelbild" [BELEG_ARTIKELBILD]></td></tr>
							<tr><td width="300">Artikelbild in Lieferschein PDF anzeigen:</td><td><input type="checkbox" name="lieferschein_artikelbild" [LIEFERSCHEIN_ARTIKELBILD]></td></tr>
							<tr><td width="300">Artikelbild in Rechnung PDF anzeigen:</td><td><input type="checkbox" name="rechnung_artikelbild" [RECHNUNG_ARTIKELBILD]></td></tr>
							<tr><td width="300">Artikelbild in Bestellung PDF anzeigen:</td><td><input type="checkbox" name="bestellung_artikelbild" [BESTELLUNG_ARTIKELBILD]></td></tr>
							<tr><td width="300">Artikelbild in Gutschrift PDF anzeigen:</td><td><input type="checkbox" name="gutschrift_artikelbild" [GUTSCHRIFT_ARTIKELBILD]></td></tr>
							<tr><td width="300">Artikelbild in Angebot PDF anzeigen:</td><td><input type="checkbox" name="angebot_artikelbild" [ANGEBOT_ARTIKELBILD]></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Dokumente Beschriftung Kundennummer:</td><td><input type="text" name="bezeichnungkundennummer" data-lang="bezeichnungkundennummer" value="[BEZEICHNUNGKUNDENNUMMER]">&nbsp;<i>Beschriftung im PDF.</i></td></tr>
							<tr><td width="300">Dokumente Beschriftung Bestellnummer:</td><td><input type="text" name="auftrag_bezeichnung_bestellnummer" data-lang="auftrag_bezeichnung_bestellnummer" value="[AUFTRAG_BEZEICHNUNG_BESTELLNUMMER]">&nbsp;<i>Beschriftung in allen Dokumenten.</i></td></tr>
							<tr><td width="300">Dokumente Beschriftung Bearbeiter:</td><td><input type="text" name="auftrag_bezeichnung_bearbeiter" data-lang="auftrag_bezeichnung_bearbeiter" value="[AUFTRAG_BEZEICHNUNG_BEARBEITER]">&nbsp;<i>Beschriftung im Auftrag.</i></td></tr>
							<tr><td width="300">Dokumente Beschriftung Vertrieb:</td><td><input type="text" name="auftrag_bezeichnung_vertrieb" data-lang="auftrag_bezeichnung_vertrieb" value="[AUFTRAG_BEZEICHNUNG_VERTRIEB]">&nbsp;<i>Beschriftung im Auftrag.</i></td></tr>
							<tr><td width="300">Bearbeiter und Vertrieb nicht füllen:</td><td><input type="checkbox"  name="vertriebbearbeiterfuellen" [VERTRIEBBEARBEITERFUELLEN]></td></tr>
							<tr><td width="300">Dokumente Bearbeiter ausblenden:</td><td><input type="checkbox"  name="briefpapier_bearbeiter_ausblenden" [BRIEFPAPIER_BEARBEITER_AUSBLENDEN]></td></tr>
							<tr><td width="300">Dokumente Vertrieb ausblenden:</td><td><input type="checkbox"  name="briefpapier_vertrieb_ausblenden" [BRIEFPAPIER_VERTRIEB_AUSBLENDEN]></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Beschriftung Internetnummer:</td><td><input type="checkbox" name="internetnummerimbeleg" [INTERNETNUMMERIMBELEG]></td></tr>
							<tr><td width="300">Dokumente Beschriftung Internetnummer:</td><td><input type="text" name="beschriftunginternetnummer" data-lang="beschriftunginternetnummer" value="[BESCHRIFTUNGINTERNETNUMMER]">&nbsp;<i>Beschriftung in allen Dokumenten.</i></td></tr>
							<tr><td width="300">Ansprechpartner in Rechnung, Gutschrift anzeigen:</td><td><input type="checkbox"  name="rechnung_gutschrift_ansprechpartner" [RECHNUNG_GUTSCHRIFT_ANSPRECHPARTNER]></td></tr>
							<tr><td width="300">Ansprechpartner in Angebot, Auftrag, Bestellung anzeigen:</td><td><input type="checkbox"  name="angebot_auftrag_bestellung_ansprechpartner" [ANGEBOT_AUFTRAG_BESTELLUNG_ANSPRECHPARTNER]></td></tr>
							<tr><td>Bezeichnung Aktionscodes:</td><td><input type="text" size="40" name="bezeichnungaktionscodes" value="[BEZEICHNUNGAKTIONSCODES]" ></td></tr>
							<tr><td>Bezeichnung Kommissions-/Konsignationslager:</td><td><input type="text" size="40" name="kommissionskonsignationslager" value="[KOMMISSIONSKONSIGNATIONSLAGER]" ></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Beschriftung Abweichend Angebot:</td><td><input type="text" name="bezeichnungangebotersatz" data-lang="bezeichnungangebotersatz" value="[BEZEICHNUNGANGEBOTERSATZ]">&nbsp;<i>Beschriftung im Angebot</i></td></tr>
							<tr><td width="300">[BEZEICHNUNGANGEBOTERSATZ] als Standard:</td><td><input type="checkbox"  name="angebotersatz_standard" [ANGEBOTERSATZ_STANDARD]></td></tr>
							<tr><td width="300">Beschriftung Abweichend Auftrag:</td><td><input type="text" name="bezeichnungauftragersatz" data-lang="bezeichnungauftragersatz" value="[BEZEICHNUNGAUFTRAGERSATZ]">&nbsp;<i>Beschriftung im Auftrag</i></td></tr>
							<tr><td width="300">Beschriftung Abweichend Rechnung:</td><td><input type="text" name="bezeichnungrechnungersatz" data-lang="bezeichnungrechnungersatz" value="[BEZEICHNUNGRECHNUNGERSATZ]">&nbsp;<i>Beschriftung in Rechnung</i></td></tr>
							<tr><td width="300">Beschriftung Abweichend Gutschrift:</td><td><input type="text" name="bezeichnungstornorechnung" data-lang="bezeichnungstornorechnung" value="[BEZEICHNUNGSTORNORECHNUNG]">&nbsp;<i>laut 06/2013 §14  UStG</i></td></tr>
							<tr><td width="300">[BEZEICHNUNGSTORNORECHNUNG] als Standard:</td><td><input type="checkbox"  name="stornorechnung_standard" [STORNORECHNUNG_STANDARD]></td></tr>
							<tr><td width="300">Beschriftung Abweichend Lieferschein:</td><td><input type="text" name="bezeichnunglieferscheinersatz" data-lang="bezeichnunglieferscheinersatz" value="[BEZEICHNUNGLIEFERSCHEINERSATZ]">&nbsp;<i>Beschriftung in Lieferschein</i></td></tr>
							<tr><td width="300">Beschriftung Abweichend Bestellung:</td><td><input type="text" name="bezeichnungbestellungersatz" data-lang="bezeichnungbestellungersatz" value="[BEZEICHNUNGBESTELLUNGERSATZ]">&nbsp;<i>Beschriftung in Bestellung</i></td></tr>
							<tr><td width="300">Beschriftung Abweichend Proformarechnung:</td><td><input type="text" name="bezeichnungproformarechnungersatz" data-lang="bezeichnungproformarechnungersatz" value="[BEZEICHNUNGPROFORMARECHNUNGERSATZ]">&nbsp;<i>Beschriftung in Proformarechnung</i></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Angebot Tage gültig bis:</td><td><input type="text" name="angebot_anzahltage" value="[ANGEBOT_ANZAHLTAGE]">&nbsp;</td></tr>
							<tr><td width="300">Angebot Tage Wiedervorlage:</td><td><input type="text" name="angebot_anzahlwiedervorlage" value="[ANGEBOT_ANZAHLWIEDERVORLAGE]">&nbsp;<i>Automatisch Wiedervorlage anlegen wenn Tage > 0</i></td></tr>
							<tr><td width="300">Angebot Stage Wiedervorlage:</td><td><input type="text" name="angebot_pipewiedervorlage" id="angebot_pipewiedervorlage" value="[ANGEBOT_PIPEWIEDERVORLAGE]">&nbsp;</td></tr>
							<tr><td width="300">Staffelpreise im Angebot bei Positionen anzeigen:</td><td><input type="checkbox" name="staffelpreiseanzeigen" [STAFFELPREISEANZEIGEN]></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Auftrag Barcodescanner Reiter einblenden:</td><td><input type="checkbox" name="auftrag_eantab" [AUFTRAG_EANTAB]></td></tr>
							<tr><td width="300">Auftrag nicht abschlie&szlig;en beim Weiterführen zu Lieferschein und Rechnung:</td><td><input type="checkbox" name="auftragabschliessen" [AUFTRAGABSCHLIESSEN]></td></tr>
							<tr><td>Interne Bemerkungen im Auftrag-Minidetails editierbar:</td><td><input type="checkbox" name="internebemerkungminidetails" [INTERNEBEMERKUNGMINIDETAILS]></td></tr>
							<tr><td>Markierung unbezahlte Auftr&auml;ge:</td><td><input type="checkbox" name="auftragmarkierenegsaldo" [AUFTRAGMARKIERENEGSALDO]> </td></tr>
							<tr><td width="300">Unterst&uuml;cklisten im Auftrag aufl&ouml;sen:</td><td><input type="checkbox" name="auftragexplodieren_unterstuecklisten" [AUFTRAGEXPLODIEREN_UNTERSTUECKLISTEN]>&nbsp;</td></tr>
							<tr><td></td><td></td></tr>
							<tr><td></td><td></td></tr>
							<tr><td width="300">Bestellung ohne Preise:</td><td><input type="checkbox" name="bestellungohnepreis" [BESTELLUNGOHNEPREIS]></td></tr>
							<tr><td width="300">Bestellung mit Artikeltext:</td><td><input type="checkbox" name="bestellungmitartikeltext" [BESTELLUNGMITARTIKELTEXT]></td></tr>
							<tr><td width="300">Bestellung Eigene Artikelnummer erste Spalte:</td><td><input type="checkbox" name="bestellungeigeneartikelnummer" [BESTELLUNGEIGENEARTIKELNUMMER]></td></tr>
							<tr><td width="300">Bestellung Lange Artikelnummern:</td><td><input type="checkbox" name="bestellunglangeartikelnummern" [BESTELLUNGLANGEARTIKELNUMMERN]></td></tr>
							<tr><td width="300">Bestellung automatisch abschlie&szlig;en:</td><td><input type="checkbox" name="bestellungabschliessen" [BESTELLUNGABSCHLIESSEN]></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Zusatzfelder Artikeltabelle|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Spalte 1:</td><td><select id="artikeltabellezusatz1" name="artikeltabellezusatz1">[SELARTIKELTABELLEZUSATZ1]</select></td></tr>
							<tr><td width="300">Spalte 2:</td><td><select id="artikeltabellezusatz2" name="artikeltabellezusatz2">[SELARTIKELTABELLEZUSATZ2]</select></td></tr>
							<tr><td width="300">Spalte 3:</td><td><select id="artikeltabellezusatz3" name="artikeltabellezusatz3">[SELARTIKELTABELLEZUSATZ3]</select></td></tr>
							<tr><td width="300">Spalte 4:</td><td><select id="artikeltabellezusatz4" name="artikeltabellezusatz4">[SELARTIKELTABELLEZUSATZ4]</select></td></tr>
							<tr><td width="300">Spalte 5:</td><td><select id="artikeltabellezusatz5" name="artikeltabellezusatz5">[SELARTIKELTABELLEZUSATZ5]</select></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Zusatzfelder Adresstabelle|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Spalte 1:</td><td><select id="adressetabellezusatz1" name="adressetabellezusatz1">[SELADRESSETABELLEZUSATZ1]</select></td></tr>
							<tr><td width="300">Spalte 2:</td><td><select id="adressetabellezusatz2" name="adressetabellezusatz2">[SELADRESSETABELLEZUSATZ2]</select></td></tr>
							<tr><td width="300">Spalte 3:</td><td><select id="adressetabellezusatz3" name="adressetabellezusatz3">[SELADRESSETABELLEZUSATZ3]</select></td></tr>
							<tr><td width="300">Spalte 4:</td><td><select id="adressetabellezusatz4" name="adressetabellezusatz4">[SELADRESSETABELLEZUSATZ4]</select></td></tr>
							<tr><td width="300">Spalte 5:</td><td><select id="adressetabellezusatz5" name="adressetabellezusatz5">[SELADRESSETABELLEZUSATZ5]</select></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Zusatzfelder Auftragstabelle|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Spalte 1:</td><td><select id="auftragtabellezusatz1" name="auftragtabellezusatz1">[SELAUFTRAGTABELLEZUSATZ1]</select></td></tr>
							<tr><td width="300">Spalte 2:</td><td><select id="auftragtabellezusatz2" name="auftragtabellezusatz2">[SELAUFTRAGTABELLEZUSATZ2]</select></td></tr>
							<tr><td width="300">Spalte 3:</td><td><select id="auftragtabellezusatz3" name="auftragtabellezusatz3">[SELAUFTRAGTABELLEZUSATZ3]</select></td></tr>
							<tr><td width="300">Spalte 4:</td><td><select id="auftragtabellezusatz4" name="auftragtabellezusatz4">[SELAUFTRAGTABELLEZUSATZ4]</select></td></tr>
							<tr><td width="300">Spalte 5:</td><td><select id="auftragtabellezusatz5" name="auftragtabellezusatz5">[SELAUFTRAGTABELLEZUSATZ5]</select></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
			 		<fieldset>
						<legend>{|Zusatzfelder Rechnung|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Spalte 1:</td><td><select id="rechnungtabellezusatz1" name="rechnungtabellezusatz1">[SELRECHNUNGTABELLEZUSATZ1]</select></td></tr>
							<tr><td width="300">Spalte 2:</td><td><select id="rechnungtabellezusatz2" name="rechnungtabellezusatz2">[SELRECHNUNGTABELLEZUSATZ2]</select></td></tr>
							<tr><td width="300">Spalte 3:</td><td><select id="rechnungtabellezusatz3" name="rechnungtabellezusatz3">[SELRECHNUNGTABELLEZUSATZ3]</select></td></tr>
							<tr><td width="300">Spalte 4:</td><td><select id="rechnungtabellezusatz4" name="rechnungtabellezusatz4">[SELRECHNUNGTABELLEZUSATZ4]</select></td></tr>
							<tr><td width="300">Spalte 5:</td><td><select id="rechnungtabellezusatz5" name="rechnungtabellezusatz5">[SELRECHNUNGTABELLEZUSATZ5]</select></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
				  <fieldset>
					  <legend>{|Zusatzfelder Lieferschein|}</legend>
						<table cellspacing="5" width="100%">
				 		  <tr><td width="300">Spalte 1:</td><td><select id="lieferscheintabellezusatz1" name="lieferscheintabellezusatz1">[SELLIEFERSCHEINTABELLEZUSATZ1]</select></td></tr>
						  <tr><td width="300">Spalte 2:</td><td><select id="lieferscheintabellezusatz2" name="lieferscheintabellezusatz2">[SELLIEFERSCHEINTABELLEZUSATZ2]</select></td></tr>
						  <tr><td width="300">Spalte 3:</td><td><select id="lieferscheintabellezusatz3" name="lieferscheintabellezusatz3">[SELLIEFERSCHEINTABELLEZUSATZ3]</select></td></tr>
						  <tr><td width="300">Spalte 4:</td><td><select id="lieferscheintabellezusatz4" name="lieferscheintabellezusatz4">[SELLIEFERSCHEINTABELLEZUSATZ4]</select></td></tr>
						  <tr><td width="300">Spalte 5:</td><td><select id="lieferscheintabellezusatz5" name="lieferscheintabellezusatz5">[SELLIEFERSCHEINTABELLEZUSATZ5]</select></td></tr>
					  </table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Zusatzfelder Produktion|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Spalte 1:</td><td><select id="produktiontabellezusatz1" name="produktiontabellezusatz1">[SELPRODUKTIONTABELLEZUSATZ1]</select></td></tr>
						  <tr><td width="300">Spalte 2:</td><td><select id="produktiontabellezusatz2" name="produktiontabellezusatz2">[SELPRODUKTIONTABELLEZUSATZ2]</select></td></tr>
							<tr><td width="300">Spalte 3:</td><td><select id="produktiontabellezusatz3" name="produktiontabellezusatz3">[SELPRODUKTIONTABELLEZUSATZ3]</select></td></tr>
							<tr><td width="300">Spalte 4:</td><td><select id="produktiontabellezusatz4" name="produktiontabellezusatz4">[SELPRODUKTIONTABELLEZUSATZ4]</select></td></tr>
							<tr><td width="300">Spalte 5:</td><td><select id="produktiontabellezusatz5" name="produktiontabellezusatz5">[SELPRODUKTIONTABELLEZUSATZ5]</select></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Zusatzfelder Bestellung|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Spalte 1:</td><td><select id="bestellungtabellezusatz1" name="bestellungtabellezusatz1">[SELBESTELLUNGTABELLEZUSATZ1]</select></td></tr>
							<tr><td width="300">Spalte 2:</td><td><select id="bestellungtabellezusatz2" name="bestellungtabellezusatz2">[SELBESTELLUNGTABELLEZUSATZ2]</select></td></tr>
							<tr><td width="300">Spalte 3:</td><td><select id="bestellungtabellezusatz3" name="bestellungtabellezusatz3">[SELBESTELLUNGTABELLEZUSATZ3]</select></td></tr>
							<tr><td width="300">Spalte 4:</td><td><select id="bestellungtabellezusatz4" name="bestellungtabellezusatz4">[SELBESTELLUNGTABELLEZUSATZ4]</select></td></tr>
							<tr><td width="300">Spalte 5:</td><td><select id="bestellungtabellezusatz5" name="bestellungtabellezusatz5">[SELBESTELLUNGTABELLEZUSATZ5]</select></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
					<fieldset></fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
					<fieldset></fieldset>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Zeiterfassung|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Buchen auf anderen Mitarbeiter erlauben:</td><td><input type="checkbox" name="zeiterfassung_anderemitarbeiter" [ZEITERFASSUNG_ANDEREMITARBEITER]> </td></tr>
							<!--<tr><td width="300">Beschreibungsfeld sperren</td><td><input type="checkbox" name="zeiterfassung_beschreibungsspeere" [ZEITERFASSUNG_BESCHREIBUNGSSPERRE]> <i>(Allgemein aktivieren)</i></td></tr>-->
							<tr><td width="300">Feld Interner Kommentar sichtbar:</td><td><input type="checkbox" name="zeiterfassung_kommentar" [ZEITERFASSUNG_KOMMENTAR]> <i></i></td></tr>
							<tr><td width="300">Feld Ort sichtbar:</td><td><input type="checkbox" name="zeiterfassung_ort" [ZEITERFASSUNG_ORT]> <i></i></td></tr>
							<tr><td width="300">Erweiterte Zeitangabe:</td><td><input type="checkbox" name="zeiterfassung_erweitert" [ZEITERFASSUNG_ERWEITERT]></td></tr>
							<tr><td width="300">abrechnen vorausgewählt:</td><td><input type="checkbox" name="zeiterfassung_abrechnenvorausgewaehlt" [ZEITERFASSUNG_ABRECHNENVORAUSGEWAEHLT]> </td></tr>
							<tr><td width="300">Zeiterfassung schlie&szlig;en:</td><td><input type="checkbox" name="zeiterfassung_schliessen" [ZEITERFASSUNG_SCHLIESSEN]></td></tr>
							<tr class="zeiterfassung_schliessen"><td width="300">Differenz:</td><td><input type="text" name="zeiterfassung_schliessentage" value="[ZEITERFASSUNG_SCHLIESSENTAGE]"> <i>(Heute - (0-14) Tage)</i></td></tr>
							<tr><td width="300">Zeiterfassungspflicht:</td><td><input type="checkbox" name="zeiterfassung_pflicht" [ZEITERFASSUNG_PFLICHT]></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Auftragsampeln ausblenden|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Lager:</td><td><input type="checkbox" value="1" name="ampellager" [AMPELLAGER]></td><td>Kundencheck: </td><td><input type="checkbox" value="1" name="ampelkunde" [AMPELKUNDE]></td></tr>
							<tr><td width="300">Porto: </td><td><input type="checkbox" value="1" name="ampelporto" [AMPELPORTO]></td><td>Liefertermin: </td><td><input type="checkbox" value="1" name="ampelliefertermin" [AMPELLIEFERTERMIN]></td></tr>
							<tr><td width="300">UST: </td><td><input type="checkbox" value="1" name="ampelust" [AMPELUST]></td><td>Kreditlimit: </td><td><input type="checkbox" value="1" name="ampelkreditlimit" [AMPELKREDITLIMIT]></td></tr>
							<tr><td width="300">Zahlung: </td><td><input type="checkbox" value="1" name="ampelzahlung" [AMPELZAHLUNG]></td><td>Liefersperre: </td><td><input type="checkbox" value="1" name="ampelliefersperre" [AMPELLIEFERSPERRE]></td></tr>
							<tr><td width="300">Nachnahme: </td><td><input type="checkbox" value="1" name="ampelnachnahme" [AMPELNACHNAHME]></td><td>Produktion: </td><td><input type="checkbox" value="1" name="ampelproduktion" [AMPELPRODUKTION]></td></tr>
							<tr><td width="300">Autoversand: </td><td><input type="checkbox" value="1" name="ampelautoversand" [AMPELAUTOVERSAND]></td><td></td><td></td></tr>
						</table>


						<!--<table cellspacing="5">
							<tr><td width="300">Ausblenden:</td><td><table><tr><td><input type="checkbox" value="1" name="ampellager" [AMPELLAGER]>&nbsp;Lager</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelporto" [AMPELPORTO]>&nbsp;Porto</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelust" [AMPELUST]>&nbsp;UST</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelzahlung" [AMPELZAHLUNG]>&nbsp;Zahlung</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelnachnahme" [AMPELNACHNAHME]>&nbsp;Nachnahme</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelautoversand" [AMPELAUTOVERSAND]>&nbsp;Autoversand</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelkunde" [AMPELKUNDE]>&nbsp;Kundencheck</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelliefertermin" [AMPELLIEFERTERMIN]>&nbsp;Liefertermin</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelkreditlimit" [AMPELKREDITLIMIT]>&nbsp;Kreditlimit</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelliefersperre" [AMPELLIEFERSPERRE]>&nbsp;Liefersperre</td></tr>
							<tr><td><input type="checkbox" value="1" name="ampelproduktion" [AMPELPRODUKTION]>&nbsp;Produktion</td></tr></table></td></tr>
						</table>-->
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
						<legend>{|Adresse|}</legend>
						<table cellspacing="5">
							<tr><td width="300">Reihenfolge Zwischenspeicher:</td><td><textarea rows="5" cols="70" name="reihenfolge_zwischenspeicher" id="reihenfolge_zwischenspeicher">[REIHENFOLGE_ZWISCHENSPEICHER]</textarea></td></tr>
							<tr><td width="">Adresse Vorlage:</td><td><input type="text" name="adresse_vorlage" id="adresse_vorlage" value="[ADRESSE_VORLAGE]" size="40"></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Servereinstellungen|}</legend>
						<table cellspacing="5" width="100%">
							<tr>
								<td width="300">{|Server-URL|}:</td>
								<td width="300"><input type="text" size="40" name="server_url" id="server_url" value="[SERVER_URL]"></td>
								<td width="50">{|Port|}:</td>
								<td><input type="text" size="10" name="server_port" id="server_port" value="[SERVER_PORT]"></td>
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
						<legend>{|LDAP Verzeichnis|}</legend>
						<table cellspacing="5" width="100%">
  						<tr><td width="300">LDAP URI:</td><td><input type="text" name="ldap_host" value="[LDAP_HOST]" size="40"></td></tr>
  						<tr><td>LDAP RDN:</td><td><input type="text" name="ldap_bindname" value="[LDAP_BINDNAME]" size="40"></td></tr>
  						<tr><td>LDAP Basis DN:</td><td><input type="text" name="ldap_searchbase" value="[LDAP_SEARCHBASE]" size="40"></td></tr>
  						<tr><td>LDAP Filter:</td><td><input type="text" name="ldap_filter" value="[LDAP_FILTER]" size="40"></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|DSGVO Einstellungen|}</legend>
						<table cellspacing="5" width="100%">
  						<tr><td width="300">E-Mail und Telefon nicht dem Versandunternehmen übergeben:</td><td><input type="checkbox" name="dsgvoversandunternehmen" [DSGVOVERSANDUNTERNEHMEN]> </td></tr>
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
						<legend>{|Sicherheit|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Externe URL im Ticketsystem nicht laden:</td><td><input type="checkbox" name="externeurlsblockieren" [EXTERNEURLSBLOCKIEREN]></td></tr>
							<tr><td width="300">Zusätzliche CSP Header:</td><td><input type="text" size="40" id="additionalcspheader" name="additionalcspheader" value="[ADDITIONALCSPHEADER]"> </td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|System E-Mails|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">System E-Mails abschalten:</td><td><input type="checkbox" name="systemmailsabschalten" id="systemmailsabschalten" [SYSTEMMAILSABSCHALTEN]></td></tr>
							<tr><td width="300">System E-Mails Empf&auml;nger:</td><td><input type="text" size="40" name="systemmailsempfaenger" id="systemmailsempfaenger" value="[SYSTEMMAILSEMPFAENGER]"></td></tr>
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
						<legend>{|Beschleunigung / Limitierungen|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Begrenzen Belegetabellen: </td><td><input type="checkbox" name="begrenzen_belege" [BEGRENZEN_BELEGE]></td>
								<td>[VORBEGRENZENANZAHL_BELEGE]auf[NACHBEGRENZENANZAHL_BELEGE] </td>
								<td>[VORBEGRENZENANZAHL_BELEGE]<input type="text" name="begrenzenanzahl_belege" id="begrenzenanzahl_belege" value="[BEGRENZENANZAHL_BELEGE]" size="6">&nbsp;Datens&auml;tze&nbsp;<i>Suche wird nach dieser Anzahl abgebrochen</i>[NACHBEGRENZENANZAHL_BELEGE]</td></tr>

							<tr><td width="300">Begrenzen Artikeltabelle: </td><td><input type="checkbox" name="begrenzen_artikeltabelle" [BEGRENZEN_ARTIKELTABELLE]></td>
								<td>[VORBEGRENZENANZAHL_ARTIKELTABELLE]auf[NACHBEGRENZENANZAHL_ARTIKELTABELLE] </td>
								<td>[VORBEGRENZENANZAHL_ARTIKELTABELLE]<input type="text" name="begrenzenanzahl_artikeltabelle" id="begrenzenanzahl_artikeltabelle" value="[BEGRENZENANZAHL_ARTIKELTABELLE]" size="6">&nbsp;Datens&auml;tze&nbsp;<i>Suche wird nach dieser Anzahl abgebrochen</i>[NACHBEGRENZENANZAHL_ARTIKELTABELLE]</td></tr>

							<tr><td width="300">Begrenzen Adressetabelle: </td><td><input type="checkbox" name="begrenzen_adressetabelle" [BEGRENZEN_ADRESSETABELLE]></td>
								<td>[VORBEGRENZENANZAHL_ADRESSETABELLE]auf[NACHBEGRENZENANZAHL_ADRESSETABELLE] </td>
								<td>[VORBEGRENZENANZAHL_ADRESSETABELLE]<input type="text" name="begrenzenanzahl_adressetabelle" id="begrenzenanzahl_adressetabelle" value="[BEGRENZENANZAHL_ADRESSETABELLE]" size="6">&nbsp;Datens&auml;tze&nbsp;<i>Suche wird nach dieser Anzahl abgebrochen</i>[NACHBEGRENZENANZAHL_ADRESSETABELLE]</td></tr>

							<tr><td width="300">Begrenzen Autoversand: </td><td></td>
								<td>auf </td>
								<td><input type="text" name="autoversand_maxauftraege" id="autoversand_maxauftraege" value="[AUTOVERSAND_MAXAUFTRAEGE]" size="6">&nbsp;Datens&auml;tze&nbsp;<i>Anzahl der maximal zu pr&uuml;fenden Auftr&auml;ge beim Autoversand (0 bedeutet keine Begrenzung)</i></td></tr>

							<tr><td>Versandmails und R&uuml;ckmeldung an Shop per Prozessstarter:</td><td><input type="checkbox" name="versandmail_zwischenspeichern" [VERSANDMAIL_ZWISCHENSPEICHERN] /></td><td>Anzahl pro Durchlauf:</td><td><input type="text" name="versandmails_max" id="versandmails_max" value="[VERSANDMAILS_MAX]" size="6">&nbsp;Mails&nbsp;<i>(0 bedeutet keine Begrenzung)</i></td></tr>
							<tr><td>Schnellsuche aktivieren: </td><td><input type="checkbox" name="schnellsuche" [SCHNELLSUCHE]></td><td> </td><td></td></tr>
							<tr><td>F&uuml;r Autoversand gesperrte Auftr&auml;ge mitberechnen: </td><td><input type="checkbox" name="autoversand_locked_orders" [AUTOVERSAND_LOCKED_ORDERS]></td><td> </td><td></td></tr>
							<tr><td>Anzeige "gefiltert von" deaktivieren: </td><td><input type="checkbox" name="schnellsuchecount" [SCHNELLSUCHECOUNT]></td><td> </td><td></td></tr>
							<tr><td><label>{|Prozessstarter limitieren|}</label></td><td></td><td><label for="cronjob_limit">{|Maximal Anzahl|}:</label></td><td><input type="text" id="cronjob_limit" name="cronjob_limit" size="6" VALUE="[CRONJOB_LIMIT]" /> <i>{|Standard 3|}</i></td></tr>
							<tr><td><label for="poll_repeattime">Polling-Zeit erh&ouml;hen</label></td><td></td><td>auf</td><td><input type="text" size="6" name="poll_repeattime" id="poll_repeattime" value="[POLL_REPEATTIME]" /> Sekunden <i>(Standard 5)</i></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend></legend>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
<br><center><input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-9');" value="Speichern"></center>
</div>

<div id="tabs-10">
[MESSAGE]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Zugang Updateserver|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Lizenz:</td><td><input type="text" name="lizenz" value="[LIZENZ]" size="80"></td></tr>
							<tr><td>Schl&uuml;ssel:</td><td><textarea rows="5" cols="80" name="schluessel">[SCHLUESSEL]</textarea></td></tr>
							<!--<tr><td>Branch</td><td><input type="text" name="branch" value="[BRANCH]" size="40"></td></tr>-->
							<!--<tr><td>Version</td><td><input type="text" name="version" readonly value="[VERSION]" size="40"></td></tr>-->
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<br>
	<input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-10');" value="Speichern" style="float:right">[UPDATESTARTENBUTTON]
</div>


<div id="tabs-11">
[MESSAGE]

	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-10 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Xentral Device API|}</legend>
						<table cellspacing="5" width="100%">
							<tr><td width="300">Xentral Device API aktiviert:</td><td><input type="checkbox" name="deviceenable" [DEVICEENABLE]></td></tr>
							<tr><td>Security Key:</td><td><input type="text" name="devicekey" id="devicekey" value="[DEVICEKEY]" size="40">&nbsp;
								<input type="button" value="Key generieren" onclick="document.getElementById('devicekey').value = generatePass(48);"></td>
							</tr>
							<!--<tr><td>Device Seriennummern</td><td><textarea name="deviceserials" cols="40" rows="5">[DEVICESERIALS]</textarea></td></tr>-->
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<br>
	<input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-11');" value="Speichern" style="float:right">
</div>

<div id="tabs-12">
[MESSAGE]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-10 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Modul|}</legend>
						<table cellspacing="5">
							<tr><td width="300">Externer Einkauf:</td><td><input type="checkbox" name="externereinkauf" [EXTERNEREINKAUF]>&nbsp;<i>(wenn Einkauf nicht &uuml;ber Xentral genutzt wird)</i></td></tr>
							<!--<tr><td width="300">Modul Vertriebsstruktur (MLM)</td><td><input type="checkbox" name="modul_mlm" [MODUL_MLM]>&nbsp;<i>(aktiviert wenn Modul vorhanden)</i></td></tr>-->
							<!--<tr><td width="300">Modul Verband</td><td><input type="checkbox" name="modul_verband" [MODUL_VERBAND]>&nbsp;<i>(aktiviert wenn Modul vorhanden)</i></td></tr>-->
							<!--<tr><td width="300">Modul Mindesthaltbarkeit</td><td><input type="checkbox" name="modul_mhd" [MODUL_MHD]>&nbsp;<i>(aktiviert wenn Modul vorhanden)</i></td></tr>-->
							<!--<tr><td width="300">Modul Verein</td><td><input type="checkbox" name="modul_verein" [MODUL_VEREIN]>&nbsp;<i>(aktiviert wenn Modul vorhanden)</i></td></tr>-->
							<!--<tr><td width="300">Modul Finanzbuchhaltung</td><td><input type="checkbox" name="modul_finanzbuchhaltung" [MODUL_FINANZBUCHHALTUNG]>&nbsp;<i>(aktiviert wenn Modul vorhanden)</i></td></tr>-->
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<br>
	<input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-12');" value="Speichern" style="float:right">
</div>

<div id="tabs-13">
[MESSAGE]
[MESSAGECLEANER]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Bereinigung|}</legend>
						<table cellspacing="5">
							<tr>
								<td width="300">Logfile-Tabelle bereinigen:</td>
								<td><input type="checkbox" name="cleaner_logfile" [CLEANER_LOGFILE]></td>
								<td>die &auml;lter sind als Tage:</td>
								<td><input type="text" name="cleaner_logfile_tage" value="[CLEANER_LOGFILE_TAGE]" size="4" /></td>
							</tr>
							<tr>
								<td>Protokoll-Tabelle bereinigen:</td>
								<td><input type="checkbox" name="cleaner_protokoll" [CLEANER_PROTOKOLL]></td>
								<td>die &auml;lter sind als Tage:</td>
								<td><input type="text" name="cleaner_protokoll_tage" value="[CLEANER_PROTOKOLL_TAGE]" size="4" /></td>
							</tr>
							<tr>
								<td>Shopimportauftr&auml;ge-Tabelle bereinigen:</td>
								<td><input type="checkbox" name="cleaner_shopimport" [CLEANER_SHOPIMPORT]></td>
								<td>die &auml;lter sind als Tage:</td>
								<td><input type="text" name="cleaner_shopimport_tage" value="[CLEANER_SHOPIMPORT_TAGE]" size="4" /></td>
							</tr>
							<tr>
								<td>Shopexport-Log-Tabelle bereinigen:</td>
								<td><input type="checkbox" name="cleaner_shopexportlog" [CLEANER_SHOPEXPORTLOG]></td>
								<td>die &auml;lter sind als Tage:</td>
								<td><input type="text" name="cleaner_shopexportlog_tage" value="[CLEANER_SHOPEXPORTLOG_TAGE]" size="4" /></td>
							</tr>
							<tr>
								<td>Versandzentrum-Logs bereinigen:</td>
								<td><input type="checkbox" name="cleaner_versandzentrum" [CLEANER_VERSANDZENTRUM]></td>
								<td>die &auml;lter sind als Tage:</td><td><input type="text" name="cleaner_versandzentrum_tage" value="[CLEANER_VERSANDZENTRUM_TAGE]" size="4"></td>
							</tr>
							<tr>
								<td>&Uuml;bertragungen-Dateien bereinigen:</td>
								<td><input type="checkbox" name="cleaner_uebertragungen" [CLEANER_UEBERTRAGUNGEN]></td>
								<td>die &auml;lter sind als Tage:</td><td><input type="text" name="cleaner_uebertragungen_tage" value="[CLEANER_UEBERTRAGUNGEN_TAGE]" size="4"></td>
							</tr>
							<tr>
								<td>Adapterbox Logs bereinigen:</td>
								<td><input type="checkbox" name="cleaner_adapterbox" [CLEANER_ADAPTERBOX]></td>
								<td>die &auml;lter sind als Tage:</td><td><input type="text" name="cleaner_adapterbox_tage" value="[CLEANER_ADAPTERBOX_TAGE]" size="4"></td>
							</tr>
					</table>
				</fieldset>
				</div>
			</div>
		</div>
	</div>
	<br>
	<input type="submit" name="submitFirmendaten" onclick="$('#firmendatenform').attr('action','#tabs-13');" value="Speichern" style="float:right">
</div>
</form>
<form name="firmendatenformfreifelder" id="firmendatenformfreifelder" action="" method="POST"  enctype="multipart/form-data">
<div id="tabs-14">
[MESSAGE]
<div class="row">  
<div class="row-height">    
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Freifelder|}</legend>
  <table width="100%" class="mkTable" height="750px">
          <tr><td colspan="5"><b>{|Bezeichnung Artikel Freifelder|}</b></td><td colspan="9"><b>{|Anzeige im PDF|}</b></td></tr>
          <tr>
            <td>aktiviert</td><td><input type="checkbox" name="parameterundfreifelder" value="1" [PARAMETERUNDFREIFELDER]>&nbsp;<i>Freifelder im Artikel einblenden</i></td><td>Typ</td><td>Spalte</td><td>Reihenfolge</td>
            <td width="7%">Angebot</td><td width="7%">Auftrag</td><td width="7%">Rechnung</td><td width="7%">Gutschrift</td><td width="7%">Lieferschein</td><td width="7%">Bestellung</td><td width="7%">Proforma</td><td width="7%">Preisanfrage</td><td>Produktion</td>
          </tr>
          [ARTIKELFREIFELDER]
</table></fieldset>
</div>
</div>

</div>
</div>
<div class="row">  
<div class="row-height">    
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">



<fieldset><legend>{|Bezeichnung Adresse Freifelder|}</legend>
    <table cellspacing="5" class="mkTable">
          <tr><td></td><td>Bezeichnung</td><td>Typ</td><td>Spalte</td><td>Reihenfolge</td></tr>
          <tr><td>Freifeld 1:</td><td><input type="text" name="adressefreifeld1" size="25" value="[ADRESSEFREIFELD1]"></td><td><select name="adressefreifeld1typ">[ADRESSEFREIFELD1TYP]</select></td><td><select name="adressefreifeld1spalte">[ADRESSEFREIFELD1SPALTE]</select></td><td><input type="text" name="adressefreifeld1sort" size="3" value="[ADRESSEFREIFELD1SORT]"></td></tr>
          <tr><td>Freifeld 2:</td><td><input type="text" name="adressefreifeld2" size="25" value="[ADRESSEFREIFELD2]"></td><td><select name="adressefreifeld2typ">[ADRESSEFREIFELD2TYP]</select></td><td><select name="adressefreifeld2spalte">[ADRESSEFREIFELD2SPALTE]</select></td><td><input type="text" name="adressefreifeld2sort" size="3" value="[ADRESSEFREIFELD2SORT]"></td></tr>
          <tr><td>Freifeld 3:</td><td><input type="text" name="adressefreifeld3" size="25" value="[ADRESSEFREIFELD3]"></td><td><select name="adressefreifeld3typ">[ADRESSEFREIFELD3TYP]</select></td><td><select name="adressefreifeld3spalte">[ADRESSEFREIFELD3SPALTE]</select></td><td><input type="text" name="adressefreifeld3sort" size="3" value="[ADRESSEFREIFELD3SORT]"></td></tr>
          <tr><td>Freifeld 4:</td><td><input type="text" name="adressefreifeld4" size="25" value="[ADRESSEFREIFELD4]"></td><td><select name="adressefreifeld4typ">[ADRESSEFREIFELD4TYP]</select></td><td><select name="adressefreifeld4spalte">[ADRESSEFREIFELD4SPALTE]</select></td><td><input type="text" name="adressefreifeld4sort" size="3" value="[ADRESSEFREIFELD4SORT]"></td></tr>
          <tr><td>Freifeld 5:</td><td><input type="text" name="adressefreifeld5" size="25" value="[ADRESSEFREIFELD5]"></td><td><select name="adressefreifeld5typ">[ADRESSEFREIFELD5TYP]</select></td><td><select name="adressefreifeld5spalte">[ADRESSEFREIFELD5SPALTE]</select></td><td><input type="text" name="adressefreifeld5sort" size="3" value="[ADRESSEFREIFELD5SORT]"></td></tr>
          <tr><td>Freifeld 6:</td><td><input type="text" name="adressefreifeld6" size="25" value="[ADRESSEFREIFELD6]"></td><td><select name="adressefreifeld6typ">[ADRESSEFREIFELD6TYP]</select></td><td><select name="adressefreifeld6spalte">[ADRESSEFREIFELD6SPALTE]</select></td><td><input type="text" name="adressefreifeld6sort" size="3" value="[ADRESSEFREIFELD6SORT]"></td></tr>
          <tr><td>Freifeld 7:</td><td><input type="text" name="adressefreifeld7" size="25" value="[ADRESSEFREIFELD7]"></td><td><select name="adressefreifeld7typ">[ADRESSEFREIFELD7TYP]</select></td><td><select name="adressefreifeld7spalte">[ADRESSEFREIFELD7SPALTE]</select></td><td><input type="text" name="adressefreifeld7sort" size="3" value="[ADRESSEFREIFELD7SORT]"></td></tr>
          <tr><td>Freifeld 8:</td><td><input type="text" name="adressefreifeld8" size="25" value="[ADRESSEFREIFELD8]"></td><td><select name="adressefreifeld8typ">[ADRESSEFREIFELD8TYP]</select></td><td><select name="adressefreifeld8spalte">[ADRESSEFREIFELD8SPALTE]</select></td><td><input type="text" name="adressefreifeld8sort" size="3" value="[ADRESSEFREIFELD8SORT]"></td></tr>
          <tr><td>Freifeld 9:</td><td><input type="text" name="adressefreifeld9" size="25" value="[ADRESSEFREIFELD9]"></td><td><select name="adressefreifeld9typ">[ADRESSEFREIFELD9TYP]</select></td><td><select name="adressefreifeld9spalte">[ADRESSEFREIFELD9SPALTE]</select></td><td><input type="text" name="adressefreifeld9sort" size="3" value="[ADRESSEFREIFELD9SORT]"></td></tr>
          <tr><td>Freifeld 10:</td><td><input type="text" name="adressefreifeld10" size="25" value="[ADRESSEFREIFELD10]"></td><td><select name="adressefreifeld10typ">[ADRESSEFREIFELD10TYP]</select></td><td><select name="adressefreifeld10spalte">[ADRESSEFREIFELD10SPALTE]</select></td><td><input type="text" name="adressefreifeld10sort" size="3" value="[ADRESSEFREIFELD10SORT]"></td></tr>
          <tr><td>Freifeld 11:</td><td><input type="text" name="adressefreifeld11" size="25" value="[ADRESSEFREIFELD11]"></td><td><select name="adressefreifeld11typ">[ADRESSEFREIFELD11TYP]</select></td><td><select name="adressefreifeld11spalte">[ADRESSEFREIFELD11SPALTE]</select></td><td><input type="text" name="adressefreifeld11sort" size="3" value="[ADRESSEFREIFELD11SORT]"></td></tr>
          <tr><td>Freifeld 12:</td><td><input type="text" name="adressefreifeld12" size="25" value="[ADRESSEFREIFELD12]"></td><td><select name="adressefreifeld12typ">[ADRESSEFREIFELD12TYP]</select></td><td><select name="adressefreifeld12spalte">[ADRESSEFREIFELD12SPALTE]</select></td><td><input type="text" name="adressefreifeld12sort" size="3" value="[ADRESSEFREIFELD12SORT]"></td></tr>
          <tr><td>Freifeld 13:</td><td><input type="text" name="adressefreifeld13" size="25" value="[ADRESSEFREIFELD13]"></td><td><select name="adressefreifeld13typ">[ADRESSEFREIFELD13TYP]</select></td><td><select name="adressefreifeld13spalte">[ADRESSEFREIFELD13SPALTE]</select></td><td><input type="text" name="adressefreifeld13sort" size="3" value="[ADRESSEFREIFELD13SORT]"></td></tr>
          <tr><td>Freifeld 14:</td><td><input type="text" name="adressefreifeld14" size="25" value="[ADRESSEFREIFELD14]"></td><td><select name="adressefreifeld14typ">[ADRESSEFREIFELD14TYP]</select></td><td><select name="adressefreifeld14spalte">[ADRESSEFREIFELD14SPALTE]</select></td><td><input type="text" name="adressefreifeld14sort" size="3" value="[ADRESSEFREIFELD14SORT]"></td></tr>
          <tr><td>Freifeld 15:</td><td><input type="text" name="adressefreifeld15" size="25" value="[ADRESSEFREIFELD15]"></td><td><select name="adressefreifeld15typ">[ADRESSEFREIFELD15TYP]</select></td><td><select name="adressefreifeld15spalte">[ADRESSEFREIFELD15SPALTE]</select></td><td><input type="text" name="adressefreifeld15sort" size="3" value="[ADRESSEFREIFELD15SORT]"></td></tr>
          <tr><td>Freifeld 16:</td><td><input type="text" name="adressefreifeld16" size="25" value="[ADRESSEFREIFELD16]"></td><td><select name="adressefreifeld16typ">[ADRESSEFREIFELD16TYP]</select></td><td><select name="adressefreifeld16spalte">[ADRESSEFREIFELD16SPALTE]</select></td><td><input type="text" name="adressefreifeld16sort" size="3" value="[ADRESSEFREIFELD16SORT]"></td></tr>
          <tr><td>Freifeld 17:</td><td><input type="text" name="adressefreifeld17" size="25" value="[ADRESSEFREIFELD17]"></td><td><select name="adressefreifeld17typ">[ADRESSEFREIFELD17TYP]</select></td><td><select name="adressefreifeld17spalte">[ADRESSEFREIFELD17SPALTE]</select></td><td><input type="text" name="adressefreifeld17sort" size="3" value="[ADRESSEFREIFELD17SORT]"></td></tr>
          <tr><td>Freifeld 18:</td><td><input type="text" name="adressefreifeld18" size="25" value="[ADRESSEFREIFELD18]"></td><td><select name="adressefreifeld18typ">[ADRESSEFREIFELD18TYP]</select></td><td><select name="adressefreifeld18spalte">[ADRESSEFREIFELD18SPALTE]</select></td><td><input type="text" name="adressefreifeld18sort" size="3" value="[ADRESSEFREIFELD18SORT]"></td></tr>
          <tr><td>Freifeld 19:</td><td><input type="text" name="adressefreifeld19" size="25" value="[ADRESSEFREIFELD19]"></td><td><select name="adressefreifeld19typ">[ADRESSEFREIFELD19TYP]</select></td><td><select name="adressefreifeld19spalte">[ADRESSEFREIFELD19SPALTE]</select></td><td><input type="text" name="adressefreifeld19sort" size="3" value="[ADRESSEFREIFELD19SORT]"></td></tr>
          <tr><td>Freifeld 20:</td><td><input type="text" name="adressefreifeld20" size="25" value="[ADRESSEFREIFELD20]"></td><td><select name="adressefreifeld20typ">[ADRESSEFREIFELD20TYP]</select></td><td><select name="adressefreifeld20spalte">[ADRESSEFREIFELD20SPALTE]</select></td><td><input type="text" name="adressefreifeld20sort" size="3" value="[ADRESSEFREIFELD20SORT]"></td></tr>


</table></fieldset>

</div></div>
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>{|Bezeichnung Projekt Freifelder|}</legend>
    <table cellspacing="5" class="mkTable">
          <tr><td></td><td>Bezeichnung</td><td>Typ</td><td>Spalte</td><td>Reihenfolge</td><td>Tabelle</td><td>Spaltenbreite in %</td></tr>
          <tr><td>Freifeld 1:</td><td><input type="text" name="projektfreifeld1" size="25" value="[PROJEKTFREIFELD1]"></td><td><select name="projektfreifeld1typ">[PROJEKTFREIFELD1TYP]</select></td><td><select name="projektfreifeld1spalte">[PROJEKTFREIFELD1SPALTE]</select></td><td><input type="text" name="projektfreifeld1sort" size="3" value="[PROJEKTFREIFELD1SORT]"></td><td><input type="text" name="projektfreifeld1tabelle" size="3" value="[PROJEKTFREIFELD1TABELLE]"></td><td><input type="text" name="projektfreifeld1breite" size="3" value="[PROJEKTFREIFELD1BREITE]"></td></tr>
          <tr><td>Freifeld 2:</td><td><input type="text" name="projektfreifeld2" size="25" value="[PROJEKTFREIFELD2]"></td><td><select name="projektfreifeld2typ">[PROJEKTFREIFELD2TYP]</select></td><td><select name="projektfreifeld2spalte">[PROJEKTFREIFELD2SPALTE]</select></td><td><input type="text" name="projektfreifeld2sort" size="3" value="[PROJEKTFREIFELD2SORT]"></td><td><input type="text" name="projektfreifeld2tabelle" size="3" value="[PROJEKTFREIFELD2TABELLE]"></td><td><input type="text" name="projektfreifeld2breite" size="3" value="[PROJEKTFREIFELD2BREITE]"></td></tr>
          <tr><td>Freifeld 3:</td><td><input type="text" name="projektfreifeld3" size="25" value="[PROJEKTFREIFELD3]"></td><td><select name="projektfreifeld3typ">[PROJEKTFREIFELD3TYP]</select></td><td><select name="projektfreifeld3spalte">[PROJEKTFREIFELD3SPALTE]</select></td><td><input type="text" name="projektfreifeld3sort" size="3" value="[PROJEKTFREIFELD3SORT]"></td><td><input type="text" name="projektfreifeld3tabelle" size="3" value="[PROJEKTFREIFELD3TABELLE]"></td><td><input type="text" name="projektfreifeld3breite" size="3" value="[PROJEKTFREIFELD3BREITE]"></td></tr>
          <tr><td>Freifeld 4:</td><td><input type="text" name="projektfreifeld4" size="25" value="[PROJEKTFREIFELD4]"></td><td><select name="projektfreifeld4typ">[PROJEKTFREIFELD4TYP]</select></td><td><select name="projektfreifeld4spalte">[PROJEKTFREIFELD4SPALTE]</select></td><td><input type="text" name="projektfreifeld4sort" size="3" value="[PROJEKTFREIFELD4SORT]"></td><td><input type="text" name="projektfreifeld4tabelle" size="3" value="[PROJEKTFREIFELD4TABELLE]"></td><td><input type="text" name="projektfreifeld4breite" size="3" value="[PROJEKTFREIFELD4BREITE]"></td></tr>
          <tr><td>Freifeld 5:</td><td><input type="text" name="projektfreifeld5" size="25" value="[PROJEKTFREIFELD5]"></td><td><select name="projektfreifeld5typ">[PROJEKTFREIFELD5TYP]</select></td><td><select name="projektfreifeld5spalte">[PROJEKTFREIFELD5SPALTE]</select></td><td><input type="text" name="projektfreifeld5sort" size="3" value="[PROJEKTFREIFELD5SORT]"></td><td><input type="text" name="projektfreifeld5tabelle" size="3" value="[PROJEKTFREIFELD5TABELLE]"></td><td><input type="text" name="projektfreifeld5breite" size="3" value="[PROJEKTFREIFELD5BREITE]"></td></tr>
          <tr><td>Freifeld 6:</td><td><input type="text" name="projektfreifeld6" size="25" value="[PROJEKTFREIFELD6]"></td><td><select name="projektfreifeld6typ">[PROJEKTFREIFELD6TYP]</select></td><td><select name="projektfreifeld6spalte">[PROJEKTFREIFELD6SPALTE]</select></td><td><input type="text" name="projektfreifeld6sort" size="3" value="[PROJEKTFREIFELD6SORT]"></td><td><input type="text" name="projektfreifeld6tabelle" size="3" value="[PROJEKTFREIFELD6TABELLE]"></td><td><input type="text" name="projektfreifeld6breite" size="3" value="[PROJEKTFREIFELD6BREITE]"></td></tr>
          <tr><td>Freifeld 7:</td><td><input type="text" name="projektfreifeld7" size="25" value="[PROJEKTFREIFELD7]"></td><td><select name="projektfreifeld7typ">[PROJEKTFREIFELD7TYP]</select></td><td><select name="projektfreifeld7spalte">[PROJEKTFREIFELD7SPALTE]</select></td><td><input type="text" name="projektfreifeld7sort" size="3" value="[PROJEKTFREIFELD7SORT]"></td><td><input type="text" name="projektfreifeld7tabelle" size="3" value="[PROJEKTFREIFELD7TABELLE]"></td><td><input type="text" name="projektfreifeld7breite" size="3" value="[PROJEKTFREIFELD7BREITE]"></td></tr>
          <tr><td>Freifeld 8:</td><td><input type="text" name="projektfreifeld8" size="25" value="[PROJEKTFREIFELD8]"></td><td><select name="projektfreifeld8typ">[PROJEKTFREIFELD8TYP]</select></td><td><select name="projektfreifeld8spalte">[PROJEKTFREIFELD8SPALTE]</select></td><td><input type="text" name="projektfreifeld8sort" size="3" value="[PROJEKTFREIFELD8SORT]"></td><td><input type="text" name="projektfreifeld8tabelle" size="3" value="[PROJEKTFREIFELD8TABELLE]"></td><td><input type="text" name="projektfreifeld8breite" size="3" value="[PROJEKTFREIFELD8BREITE]"></td></tr>
          <tr><td>Freifeld 9:</td><td><input type="text" name="projektfreifeld9" size="25" value="[PROJEKTFREIFELD9]"></td><td><select name="projektfreifeld9typ">[PROJEKTFREIFELD9TYP]</select></td><td><select name="projektfreifeld9spalte">[PROJEKTFREIFELD9SPALTE]</select></td><td><input type="text" name="projektfreifeld9sort" size="3" value="[PROJEKTFREIFELD9SORT]"></td><td><input type="text" name="projektfreifeld9tabelle" size="3" value="[PROJEKTFREIFELD9TABELLE]"></td><td><input type="text" name="projektfreifeld9breite" size="3" value="[PROJEKTFREIFELD9BREITE]"></td></tr>
          <tr><td>Freifeld 10:</td><td><input type="text" name="projektfreifeld10" size="25" value="[PROJEKTFREIFELD10]"></td><td><select name="projektfreifeld10typ">[PROJEKTFREIFELD10TYP]</select></td><td><select name="projektfreifeld10spalte">[PROJEKTFREIFELD10SPALTE]</select></td><td><input type="text" name="projektfreifeld10sort" size="3" value="[PROJEKTFREIFELD10SORT]"></td><td><input type="text" name="projektfreifeld10tabelle" size="3" value="[PROJEKTFREIFELD10TABELLE]"></td><td><input type="text" name="projektfreifeld10breite" size="3" value="[PROJEKTFREIFELD10BREITE]"></td></tr>

</table></fieldset>
</div>
</div>
</div>
</div>

<br><center><input type="submit" name="submitFirmendatenFreifelder" onclick="$('#firmendatenformfreifelder').attr('action','#tabs-14');" value="Speichern"></center>
</div>
  </form>
  
  
</div>




