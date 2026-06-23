<div id="tabs">
	<ul>
		<li><a href="#tabs-1">{|Artikel/Einkauf|}</a></li>
		<li><a href="#tabs-2">{|Adressen|}</a></li>
		<li><a href="#tabs-3">{|Zeiterfassung|}</a></li>
		<li><a href="#tabs-4">{|Wiedervorlagen|}</a></li>
		<li><a href="#tabs-5">{|Notizen|}</a></li>
		<li><a href="#tabs-6">{|Kontenrahmen|}</a></li>
		<li><a href="#tabs-7">{|Kontoauszug|}</a></li>
		<li><a href="#tabs-8">{|St&uuml;ckliste|}</a></li>
	</ul>

	<div id="tabs-1">
		<div class="row">
			<div class="col-xs-12 col-sm-1 col-sm-height">
				<div class="inside inside-full-height">
					<fieldset><legend>{|Artikel/Einkauf|}</legend>
						<table class="mkTable">
							<tr><td colspan = 3>
                                <h3>Dateianh&auml;nge mit ZIP-Datei importieren:</h3><br>
                                Die ZIP-Datei muss folgende Struktur aufweisen (Beispiel, Dateinamen sind beliebig):
                                <ul>
                                    <li>import.csv</li>
                                     <ul>
                                        <li>Artikelkennung1</li>
                                        <ul>
                                            <li>shopbild</li>
                                            <ul>
                                                <li>Bild1.jpg</li>
                                                <li>Bild2.jpg</li>
                                            </ul>
                                            <li>datenblatt</li>
                                            <ul>
                                                <li>Datenblatt.pdf</li>
                                            </ul>
                                        </ul>
                                        <li>Artikelkennung2</li>
                                        <ul>
                                            <li>...</li>
                                        </ul>
                                        <li>Artikelkennung3</li>
                                        <ul>
                                            <li>...</li>
                                        </ul>
                                    </ul>
                                </ul>
                                Artikelkennung kann sein: Artikelnummer, Herstellernummer oder EAN
                            </td><td></td><td></td></tr>
							<tr>
								<th>Variable</th>
								<th>Beschreibung</th>
								<th>Kommentar</th>
							</tr>
							<tr><td>nummer</td><td>Artikel Nr.</td><td>Pflichtfeld</td></tr>
							<tr><td>name_de</td><td>Artikelname (DE)</td><td>Pflichtfeld falls Artikel neu erstellt wird</td></tr>
							<tr><td>name_en</td><td>Artikelname (EN)</td><td></td></tr>
							<tr><td>artikelbeschreibung_de</td><td>Artikelbeschreibung (DE)</td><td></td></tr>
							<tr><td>artikelbeschreibung_en</td><td>Artikelbeschreibung (EN)</td><td></td></tr>
							<tr><td>kurztext_de</td><td>Kurztext (DE)</td><td></td></tr>
							<tr><td>kurztext_en</td><td>Kurztext (EN)</td><td></td></tr>
							<tr><td>uebersicht_de</td><td>Onlineshop / Artikelbeschreibung (DE)</td><td></td></tr>
							<tr><td>uebersicht_en</td><td>Onlineshop / Artikelbeschreibung (EN)</td><td></td></tr>
							<tr><td>metatitle_de</td><td></td><td></td></tr>
							<tr><td>metatitle_en</td><td></td><td></td></tr>
							<tr><td>metadescription_de</td><td></td><td></td></tr>
							<tr><td>metadescription_en</td><td></td><td></td></tr>
							<tr><td>metakeywords_de</td><td></td><td></td></tr>
							<tr><td>metakeywords_en</td><td></td><td></td></tr>
							<tr><td>artikelkategorie</td><td></td><td>(id)</td></tr>
							<tr><td>artikelkategorie_name</td><td>Artikelkategorie</td><td>Falls die Artikelkategorie noch nicht vorhanden ist, wird diese neu angelegt</td></tr>
							<tr><td>artikelbaumX</td><td></td><td>(X=1 bis 20; CLEAR um alle Zuordnungen zu löschen)</td></tr>
							<tr><td>dateiX</td><td>Dateianhang</td><td>(X=1 bis 20), Pfad der Datei in der ZIP-Datei</td></tr>
							<tr><td>dateistichwortX</td><td>Dateistichwort-Zuordnung für Dateianhang</td><td>(X=1 bis 20), [STICHWOERTER]</td></tr>
							<tr><td>bildtitelX</td><td></td><td>(X=1 bis 20; X entspricht der Sortierung des Bildes in den Dateien des Artikels)</td></tr>
							<tr><td>bildbeschreibung</td><td></td><td(X=1 bis 20; X entspricht der Sortierung des Bildes in den Dateien des Artikels)></td></tr>
							<tr><td>internerkommentar</td><td>Interner Kommentar</td><td></td></tr>
							<tr><td>hersteller</td><td></td><td></td></tr>
							<tr><td>herstellerlink</td><td></td><td></td></tr>
							<tr><td>herstellernummer</td><td></td><td></td></tr>
							<tr><td>ean</td><td>EAN Nr.</td><td></td></tr>
							<tr><td>herstellerland</td><td></td><td></td></tr>
							<tr><td>herkunftsland</td><td></td><td></td></tr>
							<tr><td>zolltarifnummer</td><td></td><td></td></tr>
							<tr><td>ursprungsregion</td><td></td><td></td></tr>
							<tr><td>allelieferanten</td><td></td><td></td></tr>
							<tr><td>standardlieferant</td><td></td><td></td></tr>
							<tr><td>geraet</td><td></td><td></td></tr>
							<tr><td>serviceartikel</td><td></td><td></td></tr>
							<tr><td>inventurek</td><td></td><td></td></tr>
							<tr><td>inventurekaktiv</td><td></td><td></td></tr>
							<tr><td>berechneterek</td><td></td><td></td></tr>
							<tr><td>berechneterekwaehrung</td><td></td><td></td></tr>
							<tr><td>verwendeberechneterek</td><td></td><td></td></tr>
							<tr><td>steuer_erloese_inland_normal</td><td></td><td></td></tr>
							<tr><td>steuer_erloese_inland_ermaessigt</td><td></td><td></td></tr>
							<tr><td>steuer_aufwendung_inland_nichtsteuerbar</td><td></td><td></td></tr>
							<tr><td>steuer_erloese_inland_innergemeinschaftlich</td><td></td><td></td></tr>
							<tr><td>steuer_erloese_inland_eunormal</td><td></td><td></td></tr>
							<tr><td>steuer_erloese_inland_euermaessigt</td><td></td><td></td></tr>
							<tr><td>steuer_erloese_inland_export</td><td></td><td></td></tr>
							<tr><td>steuer_aufwendung_inland_normal</td><td></td><td></td></tr>
							<tr><td>steuer_aufwendung_inland_ermaessigt</td><td></td><td></td></tr>
							<tr><td>steuer_aufwendung_inland_nichtsteuerbar</td><td></td><td></td></tr>
							<tr><td>steuer_aufwendung_inland_innergemeinschaftlich</td><td></td><td></td></tr>
							<tr><td>steuer_aufwendung_inland_eunormal</td><td></td><td></td></tr>
							<tr><td>steuer_aufwendung_inland_euermaessigt</td><td></td><td></td></tr>
							<tr><td>steuer_aufwendung_inland_import</td><td></td><td></td></tr>
							<tr><td>mindesthaltbarkeitsdatum</td><td></td><td>(1=aktiv,0=nicht aktiv)</td></tr>
							<tr><td>seriennummern</td><td></td><td>(keine,eigene,vomprodukt,vomprodukteinlagern)</td></tr>
							<tr><td>chargenverwaltung</td><td></td><td>(0=keine,2=originale nutzen)</td></tr>
							<tr><td>mindestlager</td><td>Minimaler Lagerbestand</td><td></td></tr>
							<tr><td>mindestbestellung</td><td></td><td></td></tr>
							<tr><td>umsatzsteuer</td><td>Ermäßigte Umsatzsteuer</td><td>Möglich in CSV: ermaessigt, normal</td></tr>
							<tr><td>artikelabschliessenkalkulation</td><td>Checkbox 'Einkaufspreis beim Abschließen der Bestellung in Kalkulation übernehmen' zu finden unter Artikel / Kalkulation</td><td>Mit dem Wert 1 aktiviert man die Checkbox, mit 0 deaktiviert man sie</td></tr>
							<tr><td>artikelfifokalkulation</td><td>Checkbox 'Kalkulierter EK Preis automatisch nach FIFO Prinzip der letzten Eingänge den Mittelwert neu berechnen' zu finden unter Artikel / Kalkulation</td><td>Mit dem Wert 1 aktiviert man die Checkbox, mit 0 deaktiviert man sie</td></tr>
							<tr><td>lieferantname</td><td></td><td></td></tr>
							<tr><td>lieferantbestellnummerX</td><td></td><td>(X = leer, 1-3)</td></tr>
							<tr><td>lieferantartikelbezeichnungX</td><td></td><td>(X = leer, 1-3)</td></tr>
							<tr><td>lieferanteinkaufnettoX</td><td>Einkaufspreis (netto) pro Stück</td><td>Pflichtfeld beim Import von Einkaufspreisen, (X = leer, 1-3)</td></tr>
							<tr><td>lieferanteinkaufwaehrungX</td><td>Währung des EInkaufspreises, z.B. (EUR, CHF, USD)</td><td>Pflichtfeld beim Import von Einkaufspreisen, (X = leer, 1-3)</td></tr>
							<tr><td>lieferanteinkaufmengeX</td><td>Staffelmenge des Einkaufspreise z.B. ab 10 Stück = 10, für Standardpreis Menge = 1 angeben</td><td>Pflichtfeld beim Import von Einkaufspreisen, (X = leer, 1-3)</td></tr>
							<tr><td>lieferanteinkaufvpemengeX</td><td></td><td>(X = leer, 1-3)</td></tr>
							<tr><td>lieferanteinkaufvpepreisX</td><td></td><td>(X = leer, 1-3)</td></tr>
							<tr><td>lieferantrahmenvertrag_vonX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantrahmenvertrag_bisX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantpreis_anfrage_vomX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantgueltig_bisX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantdatum_lagerlieferantX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantsicherheitslagerX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantrahmenvertrag_mengeX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantlieferzeit_aktuellX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantlieferzeit_standardX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantlager_lieferantX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantrahmenvertragX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantbemerkungX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantnichtberechnetX</td><td></td><td>(X = leer, bzw 1-3)</td></tr>
							<tr><td>lieferantennummer</td><td>Lieferantennummer, muss im System vorhanden sein</td><td>Pflichtfeld für Einkaufpreis-Import</td></tr>
							<tr><td>kundennummer</td><td></td><td></td></tr>
							<tr><td>gewicht</td><td>Gewicht (in kg)</td><td></td></tr>
							<tr><td>breite</td><td>Breite (in cm)</td><td></td></tr>
							<tr><td>hoehe</td><td>Höhe (in cm)</td><td></td></tr>
							<tr><td>laenge</td><td>Länge (in cm)</td><td></td></tr>
							<tr><td>einheit</td><td>Einheit des Artikels </td><td></td></tr>
							<tr><td>xvp</td><td>Anzahl der Xentral Volume Points (XVPs)</td><td></td></tr>
							<tr><td>produktion</td><td></td><td></td></tr>
							<tr><td>lagerartikel</td><td>Lagerartikel = 1, Kein Lagerartikel (z.B. Dienstleistung, Porto) = 0</td><td></td></tr>
							<tr><td>lager_platz</td><td>Standardlager (Regalname angeben, nicht Lagername)</td><td></td></tr>
							<tr><td>lager_menge_addieren</td><td>Lagerbestand der zum bestehenden Bestand addiert werden soll</td><td></td></tr>
							<tr><td>lager_menge_total</td><td>Lagerbestand der auf den Lagerplatz gebucht werden soll</td><td></td></tr>
							<tr><td>lager_mhd</td><td>MHD zur Einlagerung auf dem Lagerplatz</td><td></td></tr>
							<tr><td>lager_charge</td><td>Charge zur Einlagerung auf dem Lagerplatz</td><td></td></tr>
							<tr><td>lager_platzX</td><td>siehe lager_platz</td><td>(X = 1-5)</td></tr>
							<tr><td>lager_menge_addierenX</td><td>siehe lager_menge_addieren</td><td>(X = 1-5)</td></tr>
							<tr><td>lager_menge_totalX</td><td>siehe lager_menge_total</td><td>(X = 1-5)</td></tr>
							<tr><td>lager_mhdX</td><td>siehe lager_mhd</td><td>(X = 1-5)</td></tr>
							<tr><td>lager_chargeX</td><td>siehe lager_charge</td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_mengeX</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_gewichtX</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_laengeX</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_breiteX</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_hoeheX</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_mengeX2</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_gewichtX2</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_laengeX2</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_breiteX2</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>lager_vpe_hoeheX2</td><td></td><td>(X = 1-5)</td></tr>
							<tr><td>verkaufspreisXnetto</td><td>Verkaufspreis</td><td>(X = 1-10), Pflichfeld für Verkaufspreis</td></tr>
							<tr><td>verkaufspreisXpreisfuermenge</td><td>Angegebener verkaufspreisXnetto gilt für Menge</td><td>(X = 1-10)</td></tr>
							<tr><td>verkaufspreisXmenge</td><td>Ab Menge</td><td>(X = 1-10), Pflichtfeld für Verkaufspreis - Falls Standardpreis (kein Staffelpreis) → als Menge 1 angeben</td></tr>
							<tr><td>verkaufspreisXwaehrung</td><td>Währung</td><td>(EUR,CHF,USD,...) (X = 1-10)</td></tr>
							<tr><td>verkaufspreisXgruppe</td><td>Gruppenspezifischer Preis für Gruppe</td><td>(Kennziffer) (X = 1-10)</td></tr>
							<tr><td>verkaufspreisXkundennummer</td><td>Kundenspezifischer Preis für Kunde mit Kundennummer</td><td>(X = 1-10)</td></tr>
							<tr><td>verkaufspreisXartikelnummerbeikunde</td><td>Artikelnummer, die der Kunde verwendet (zur Information)</td><td>(X = 1-10)</td></tr>
							<tr><td>verkaufspreisXgueltigab</td><td>Startdatum des Verkaufspreises</td><td>(X = 1-10)</td></tr>
							<tr><td>verkaufspreisXgueltigbis</td><td>Enddatum des Verkaufspreises</td><td>(X = 1-10)</td></tr>
							<tr><td>verkaufspreisXinternerkommentar</td><td>Interner Kommentar z.B. bzgl. besonderer Konditionen oder Gründe für Zeitraum</td><td>(X = 1-10)</td></tr>
							<tr><td>variante_von</td><td>Artikel ist eine Variante von (Artikelnummer)</td><td></td></tr>
							<tr><td>projekt</td><td>Projekt</td><td></td></tr>
							<tr><td>geloescht</td><td>Gelöschter Artikel = 1, nicht gelöscht = 0</td></tr>
							<tr><td>inaktiv</td><td>Inaktiver Artikel = 1, aktiver Artikel = 0</td><td></td></tr>
							<tr><td>aktiv</td><td></td><td></td></tr>
							<tr><td>juststueckliste</td><td></td><td></td></tr>
							<tr><td>stueckliste</td><td></td><td></td></tr>
							<tr><td>stuecklistevonartikel</td><td>In Stückliste (Nummer): Gibt Hauptartikel von diesem Stücklistenartikel an</td><td></td></tr>
							<tr><td>stuecklistemenge</td><td>Anzahl des Artikels in Stücklisten</td><td></td></tr>
							<tr><td>stuecklisteart</td><td>Art der Stückliste:  et = Einkauf, it = Information, bt = Beistellung</td><td></td></tr>
							<tr><td>vkmeldungunterdruecken</td><td></td><td></td></tr>
							<tr><td>shop_shopid</td><td></td><td>(shopid ersetzen durch die ID des Shops)</td></tr>
							<tr><td>aktiv_shopid</td><td></td><td>(shopid ersetzen durch die ID des Shops)</td></tr>
							<tr><td>fremdnummerX_shopid</td><td></td><td>(shopid ersetzen durch die ID des Shops, X = 1-40)</td></tr>
							<tr><td>fremdnummerbezeichnungX_shopid</td><td></td><td>(shopid ersetzen durch die ID des Shops, X = 1-40)</td></tr>
							<tr><td>pseudopreis</td><td>Pseudopreis, auch Streichpreis z.B. zur Anzeige im Onlineshop</td><td></td></tr>
							<tr><td>freifeldX</td><td>Freifelder des Artikels nach eigener Definition im System, eine Typprüfung findet nicht statt</td><td>(X = 1-40)</td></tr>
							<tr><td>intern_gesperrt</td><td></td><td></td></tr>
							<tr><td>intern_gesperrtgrund</td><td></td><td></td></tr>
							<tr><td>autolagerlampe</td><td></td><td>(Lagersync)</td></tr>
							<tr><td>pseudolager</td><td></td><td></td></tr>
							<tr><td>lagerkorrekturwert</td><td></td><td></td></tr>
							<tr><td>restmenge</td><td></td><td></td></tr>
							<tr><td>provision1</td><td></td><td>(in %)</td></tr>
							<tr><td>provisiontyp1</td><td></td><td>(ek, vk, erloes, leer)</td></tr>
							<tr><td>provision2</td><td></td><td>(in %)</td></tr>
							<tr><td>provisiontyp2</td><td></td><td>(ek, vk, erloes, leer)</td></tr>
							<tr><td>eigenschaftnameX</td><td></td><td>X = 1 bis 50 (mehrfach)</td></tr>
							<tr><td>eigenschaftnameeindeutigX</td><td></td><td>X = 1 bis 50 (einzeln)</td></tr>
							<tr><td>eigenschaftwertX</td><td></td><td>(X =1 bis 50)</td></tr>
							<tr><td>eigenschaftnameX_yy</td><td></td><td>(X =1 bis 50, yy Ländercode en, fr...)</td></tr>
							<tr><td>eigenschaftwertX_yy</td><td></td><td>(X =1 bis 50, yy Ländercode en, fr...)</td></tr>
							<tr><td>matrixprodukt</td><td></td><td></td></tr>
							<tr><td>matrixproduktvon</td><td></td><td></td></tr>
							<tr><td>matrixproduktgruppe1</td><td></td><td></td></tr>
							<tr><td>matrixproduktgruppe2</td><td></td><td></td></tr>
							<tr><td>matrixproduktwert1</td><td></td><td></td></tr>
							<tr><td>matrixproduktwert2</td><td></td><td></td></tr>
							<tr><td>matrixgruppe1</td><td></td><td>(alle Optionen aus Gruppe1)</td></tr>
							<tr><td>matrixgruppe2</td><td></td><td>(alle Optionen aus Gruppe2)</td></tr>
							<tr><td>matrixartikelnummer</td><td></td><td>(1 = Nummernkreis, 2 = Optionnamen als Postfix, 3|trennzeichen|Stellen|Startnummer = Postfix)</td></tr>
							<tr><td>matrixnamefuerunterartikel</td><td></td><td>(1 = Optionen werden an Artikelbezeichnung der Unterartikel gehängt, 0 = Artikelbezeichnung wird vom Hauptartikel genommen)</td></tr>
							<tr><td>nameX_YY</td><td></td><td>Weitere Sprachen (X = 1-100, YY ersetzt durch den ISO Code der Sprache aus dem Sprachen Modul)</td></tr>
							<tr><td>kurztextX_YY</td><td></td><td></td></tr>
							<tr><td>beschreibungX_YY</td><td></td><td></td></tr>
							<tr><td>beschreibung_onlineX_YY</td><td></td><td></td></tr>
							<tr><td>meta_titleX_YY</td><td></td><td></td></tr>
							<tr><td>meta_descriptionX_YY</td><td></td><td></td></tr>
							<tr><td>meta_keywordsX_YY</td><td></td><td></td></tr>
							<tr><td>katalogartikelX_YY</td><td></td><td></td></tr>
							<tr><td>katalog_bezeichnungX_YY</td><td></td><td></td></tr>
							<tr><td>katalog_textX_YY</td><td></td><td></td></tr>
							<tr><td>shopX_YY</td><td></td><td></td></tr>
							<tr><td>aktivX_YY</td><td></td><td></td></tr>
							<tr><td></td><td></td><td></td></tr>
						</table>                        
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<div id="tabs-2">
		<div class="row">
			<div class="col-xs-12 col-sm-1 col-sm-height">
				<div class="inside inside-full-height">
					<fieldset><legend>{|Adressen|}</legend>
						<table class="mkTable">
							<tr>
								<th>Variable</th>
								<th>Beschreibung</th>
								<th>Kommentar</th>
							</tr>
							<tr><td>typ (herr,frau,firma)</td><td>Anrede</td><td>Erwartet: Herr, Frau, Firma, Mr, Mrs, Company</td></tr>
							<tr><td>marketingsperre</td><td></td><td></td></tr>
							<tr><td>trackingsperre</td><td></td><td></td></tr>
							<tr><td>sprache</td><td>Sprache für Belege</td><td>Möglich in CSV: deutsch, englisch</td></tr>
							<tr><td>name</td><td>Name</td><td>Pflichtfeld - Bei Firmen: Firmenname, bei Personen: Vor- und Nachname</td></tr>
							<tr><td>vorname (falls nicht bei name)</td><td></td><td></td></tr>
							<tr><td>abteilung</td><td>Abteilung</td><td></td></tr>
							<tr><td>unterabteilung</td><td>Unterabteilung</td><td></td></tr>
							<tr><td>ansprechpartner</td><td>Ansprechpartner</td><td></td></tr>
							<tr><td>land (DE,AT,...)</td><td>Land</td><td>Wichtig: Nur ISO Code (DE, AT, NL, etc.)</td></tr>
							<tr><td>strasse</td><td>Straße</td><td></td></tr>
							<tr><td>strasse_hausnummer</td><td></td><td></td></tr>
							<tr><td>hausnummer</td><td></td><td></td></tr>
							<tr><td>ort</td><td>Ort</td><td></td></tr>
							<tr><td>plz</td><td>PLZ</td><td></td></tr>
							<tr><td>plz_ort</td><td></td><td></td></tr>
							<tr><td>telefon</td><td>Telefonnummer</td><td></td></tr>
							<tr><td>telefax</td><td>Telefax-Nummer</td><td></td></tr>
							<tr><td>mobil</td><td>Mobiltelefonnummer</td><td></td></tr>
							<tr><td>email</td><td>E-Mail-Adresse</td><td></td></tr>
							<tr><td>ustid</td><td>USt.-ID-Nr.</td><td></td></tr>
							<tr><td>ust_befreit</td><td></td><td></td></tr>
							<tr><td>sonstiges</td><td>Sonstiges</td><td></td></tr>
							<tr><td>adresszusatz</td><td>Adresszusatz</td><td></td></tr>
							<tr><td>kundenfreigabe</td><td></td><td></td></tr>
							<tr><td>kundennummer</td><td>Kundennummer</td><td>Pflichtfeld (bei Kunden) - Nicht doppelt vergeben</td></tr>
							<tr><td>lieferantennummer</td><td>Lieferantennummer</td><td>Pflichtfeld (bei Lieferanten) - Nicht doppelt vergeben</td></tr>
							<tr><td>mitarbeiternummer</td><td>Mitarbeiternummer</td><td>Pflichtfeld (bei Mitarbeiter) - Nicht doppelt vergeben</td></tr>
							<tr><td>bank</td><td>Name der Bank</td><td></td></tr>
							<tr><td>inhaber</td><td>Kontoinhaber</td><td></td></tr>
							<tr><td>swift</td><td>SWIFT-Code / BIC</td><td></td></tr>
							<tr><td>iban</td><td>IBAN</td><td></td></tr>
							<tr><td>waehrung</td><td>Währung des Kontos</td><td></td></tr>
							<tr><td>paypal</td><td></td><td></td></tr>
							<tr><td>paypalinhaber</td><td></td><td></td></tr>
							<tr><td>paypalwaehrung</td><td></td><td></td></tr>
							<tr><td>projekt</td><td></td><td></td></tr>
							<tr><td>zahlungsweise</td><td></td><td></td></tr>
							<tr><td>zahlungszieltage</td><td></td><td></td></tr>
							<tr><td>zahlungszieltageskonto</td><td></td><td></td></tr>
							<tr><td>zahlungszielskonto</td><td></td><td></td></tr>
							<tr><td>versandart</td><td></td><td></td></tr>
							<tr><td>kundennummerlieferant</td><td></td><td></td></tr>
							<tr><td>zahlungsweiselieferant</td><td></td><td></td></tr>
							<tr><td>zahlungszieltagelieferant</td><td></td><td></td></tr>
							<tr><td>zahlungszieltageskontolieferant</td><td></td><td></td></tr>
							<tr><td>zahlungszielskontolieferant</td><td></td><td></td></tr>
							<tr><td>versandartlieferant</td><td></td><td></td></tr>
							<tr><td>firma</td><td></td><td></td></tr>
							<tr><td>webid</td><td></td><td></td></tr>
							<tr><td>internetseite</td><td>Internetseite</td><td></td></tr>
							<tr><td>titel</td><td>Akademischer Titel</td></tr>
							<tr><td>anschreiben</td><td>Anschreiben</td><td></td></tr>
							<tr><td>geburtstag</td><td>Geburtsdatum</td><td></td></tr>
							<tr><td>liefersperre</td><td></td><td></td></tr>
							<tr><td>steuernummer</td><td></td><td></td></tr>
							<tr><td>steuerbefreit</td><td></td><td></td></tr>
							<tr><td>liefersperregrund</td><td></td><td></td></tr>
							<tr><td>verrechnungskontoreisekosten</td><td></td><td></td></tr>
							<tr><td>abweichende_rechnungsadresse</td><td></td><td></td></tr>
							<tr><td>rechnung_vorname</td><td>Abweichende Rechnungsadresse: Vorname</td><td></td></tr>
							<tr><td>rechnung_name</td><td>Abweichende Rechnungsadresse: Name</td><td></td></tr>
							<tr><td>rechnung_titel</td><td>Abweichende Rechnungsadresse: Akademischer Titel</td><td></td></tr>
							<tr><td>rechnung_typ</td><td>Abweichende Rechnungsadresse: Typ (firma, herr, frau, mr., mrs.)</td><td></td></tr>
							<tr><td>rechnung_strasse</td><td>Abweichende Rechnungsadresse: Strasse</td><td></td></tr>
							<tr><td>rechnung_ort</td><td>Abweichende Rechnungsadresse: Ort</td><td></td></tr>
							<tr><td>rechnung_land</td><td>Abweichende Rechnungsadresse: Land</td><td></td></tr>
							<tr><td>rechnung_abteilung</td><td>Abweichende Rechnungsadresse: Abteilung (Zeile 2)</td><td></td></tr>
							<tr><td>rechnung_unterabteilung</td><td>Abweichende Rechnungsadresse: Unterabteilung (Zeile 3)</td><td></td></tr>
							<tr><td>rechnung_adresszusatz</td><td>Abweichende Rechnungsadresse: Adresszusatz</td><td></td></tr>
							<tr><td>rechnung_telefon</td><td>Abweichende Rechnungsadresse: Telefonnummer</td><td></td></tr>
							<tr><td>rechnung_telefax</td><td>Abweichende Rechnungsadresse: Telefax-Nummer</td><td></td></tr>
							<tr><td>rechnung_anschreiben</td><td>Abweichende Rechnungsadresse: Anschreiben</td><td></td></tr>
							<tr><td>rechnung_email</td><td>Abweichende Rechnungsadresse: E-Mail-Adresse</td><td></td></tr>
							<tr><td>rechnung_plz</td><td>Abweichende Rechnungsadresse: Postleitzahl (PLZ)</td><td></td></tr>
							<tr><td>rechnung_ansprechpartner</td><td>Abweichende Rechnungsadresse: Ansprechpartner</td><td></td></tr>
							<tr><td>kennung</td><td></td><td></td></tr>
							<tr><td>vertrieb</td><td>Mitarbeiter Vertrieb</td><td></td></tr>
							<tr><td>innendienst</td><td>Mitarbeiter Innendienst</td><td></td></tr>
							<tr><td>rabatt</td><td></td><td></td></tr>
							<tr><td>rabattX</td><td></td><td>(X = 1 - 5)</td></tr>
							<tr><td>bonusX</td><td></td><td>(X = 1-10)</td></tr>
							<tr><td>bonusX_ab</td><td>(X = 1-10)</td><td></td></tr>
							<tr><td>verbandsnummer</td><td></td><td></td></tr>
							<tr><td>portofreiab</td><td></td><td></td></tr>
							<tr><td>zahlungskonditionen_festschreiben</td><td></td><td></td></tr>
							<tr><td>rabatte_festschreiben</td><td></td><td></td></tr>
							<tr><td>provision</td><td></td><td></td></tr>
							<tr><td>portofrei_aktiv</td><td></td><td></td></tr>
							<tr><td>rabattinformation</td><td></td><td></td></tr>
							<tr><td>freifeld1 - freifeld20</td><td>Freifelder des Artikels nach eigener Definition im System, eine Typprüfung findet nicht statt</td><td></td></tr>
							<tr><td>rechnung_periode</td><td></td><td></td></tr>
							<tr><td>rechnung_anzahlpapier</td><td></td><td></td></tr>
							<tr><td>rechnung_permail</td><td></td><td></td></tr>
							<tr><td>usereditid</td><td></td><td></td></tr>
							<tr><td>useredittimestamp</td><td></td><td></td></tr>
							<tr><td>infoauftragserfassung</td><td></td><td></td></tr>
							<tr><td>mandatsreferenz</td><td></td><td></td></tr>
							<tr><td>kreditlimit</td><td></td><td></td></tr>
							<tr><td>abweichendeemailab</td><td></td><td></td></tr>
							<tr><td>filiale</td><td></td><td></td></tr>
							<tr><td>mandatsreferenzdatum</td><td></td><td></td></tr>
							<tr><td>mandatsreferenzaenderung</td><td></td><td></td></tr>
							<tr><td>sachkonto</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1name (1-3 möglich)</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1typ</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1strasse</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1sprache</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1bereich</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1abteilung</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1unterabteilung</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1land</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1ort</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1plz</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1telefon</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1telefax</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1mobil</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1email</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1sonstiges</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1adresszusatz</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1ansprechpartner_land</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1anschreiben</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1titel</td><td></td><td></td></tr>
							<tr><td>ansprechpartner1marketingsperre</td><td></td><td></td></tr>
							<tr><td>lieferadresse1name (1-3 möglich)</td><td></td><td></td></tr>
							<tr><td>lieferadresse1typ</td><td></td><td></td></tr>
							<tr><td>lieferadresse1strasse</td><td></td><td></td></tr>
							<tr><td>lieferadresse1abteilung</td><td></td><td></td></tr>
							<tr><td>lieferadresse1unterabteilung</td><td></td><td></td></tr>
							<tr><td>lieferadresse1land</td><td></td><td></td></tr>
							<tr><td>lieferadresse1ort</td><td></td><td></td></tr>
							<tr><td>lieferadresse1plz</td><td></td><td></td></tr>
							<tr><td>lieferadresse1telefon</td><td></td><td></td></tr>
							<tr><td>lieferadresse1gln</td><td></td><td></td></tr>
							<tr><td>lieferadresse1email</td><td></td><td></td></tr>
							<tr><td>lieferadresse1adresszusatz</td><td></td><td></td></tr>
							<tr><td>lieferadresse1standardlieferadresse</td><td></td><td></td></tr>
							<tr><td>lieferadresse1ustid</td><td></td><td></td></tr>
							<tr><td>lieferadresse1ust_befreit</td><td></td><td></td></tr>
							<tr><td>gruppe1-5</td><td>Gruppe</td><td>Die Gruppe muss zuvor angelegt worden sein</td></tr>
							<tr><td>kundennummer_buchhaltung</td><td>Von der Kunden-Nr. abweichendes Buchungskonto (Debitoren-Nr.)</td><td></td></tr>
							<tr><td>lieferantennummer_buchhaltung</td><td>Von der Lieferanten-Nr. abweichendes Buchungskonto (Kreditoren-Nr.)</td><td></td></tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<div id="tabs-3">
		<div class="row">
			<div class="col-xs-12 col-sm-1 col-sm-height">
				<div class="inside inside-full-height">
					<fieldset><legend>{|Zeiterfassung|}</legend>
						<table class="mkTable">
							<tr>
								<th>Variable</th>
								<th>Beschreibung</th>
								<th>Kommentar</th>
							</tr>
							<tr><td>datum_von</td><td></td><td>Pflichtfeld</td></tr>
							<tr><td>zeit_von</td><td></td><td>Pflichtfeld</td></tr>
							<tr><td>datum_bis</td><td></td><td>Pflichtfeld</td></tr>
							<tr><td>zeit_bis</td><td></td><td>Pflichtfeld</td></tr>
							<tr><td>kennung</td><td>Auswahl aus Arbeit, Pause, Urlaub, Krankheit, Freizeitausgleich, Feiertag</td><td>Pflichtfeld</td></tr>
							<tr><td>taetigkeit</td><td>Kurzbeschreibung der Tätigkeit</td><td>Pflichtfeld</td></tr>
							<tr><td>details</td><td>Erweiterte Beschreibung der Tätigkeit</td><td></td></tr>
							<tr><td>mitarbeiternummer</td><td>Mitarbeiter</td><td>Pflichtfeld</td></tr>
							<tr><td>kundennummer</td><td>Kundennummer für Zeiten, die auf Kunden gebucht werden</td></tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<div id="tabs-4">
		<div class="row">
			<div class="col-xs-12 col-sm-1 col-sm-height">
				<div class="inside inside-full-height">
					<fieldset><legend>{|Wiedervorlagen|}</legend>
						<table class="mkTable">
							<tr>
								<th>Variable</th>
								<th>Beschreibung</th>
								<th>Kommentar</th>
							</tr>
							<tr>
								<td>datum_faellig</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>uhrzeit_faellig</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>kundennummer</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>mitarbeiternummer</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>betreff</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>text</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>abgeschlossen</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>prio</td>
								<td></td>
								<td></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<div id="tabs-5">
		<div class="row">
			<div class="col-xs-12 col-sm-1 col-sm-height">
				<div class="inside inside-full-height">
					<fieldset><legend>{|Notizen|}</legend>
						<table class="mkTable">
							<tr>
								<th>Variable</th>
								<th>Beschreibung</th>
								<th>Kommentar</th>
							</tr>
							<tr>
								<td>datum</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>uhrzeit</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>kundennummer</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>mitarbeiternummer</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>betreff</td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td>text</td>
								<td></td>
								<td></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
    <div id="tabs-6">
	    <div class="row">
		    <div class="col-xs-12 col-sm-1 col-sm-height">
			    <div class="inside inside-full-height">
				    <fieldset><legend>{|Kontenrahmen|}</legend>
					    <table class="mkTable">
						    <tr>
							    <th>Variable</th>
							    <th>Beschreibung</th>
							    <th>Kommentar</th>
						    </tr>
						    <tr>
							    <td>sachkonto</td>
							    <td>Sachkontonummer</td>
							    <td></td>
						    </tr>
						    <tr>
							    <td>beschriftung</td>
							    <td>Sachkontobeschriftung</td>
							    <td></td>
						    </tr>
						    <tr>
							    <td>art</td>
							    <td>Art des Kontos</td>
							    <td>'Aufwendungen', 'Erl&ouml;se', 'Geldtransit' oder 'Saldo'</td>
						    </tr>
						    <tr>
							    <td>bemerkung</td>
							    <td>Bemerkung zum Konto</td>
							    <td>optional</td>
						    </tr>
						    <tr>
							    <td>projekt</td>
							    <td>Projekt-Kennung</td>
							    <td>optional</td>
						    </tr>
						    <tr>
							    <td>ausblenden</td>
							    <td>Soll das Konto ausgeblendet werden?</td>
							    <td>0 oder 1</td>
						    </tr>
					    </table>
				    </fieldset>
			    </div>
		    </div>
	    </div>
	</div>
    <div id="tabs-7">
	    <div class="row">
		    <div class="col-xs-12 col-sm-1 col-sm-height">
			    <div class="inside inside-full-height">
				    <fieldset><legend>{|Kontoauszug|}</legend>
					    <table class="mkTable">
						    <tr>
							    <th>Variable</th>
							    <th>Beschreibung</th>
							    <th>Kommentar</th>
						    </tr>
						    <tr>
							    <td>konto</td>
							    <td>Konto-Kurzbezeichnung</td>
							    <td></td>
						    </tr>
						    <tr>
							    <td>buchung</td>
							    <td>Buchungsdatum</td>
							    <td>Im Format DD.MM.YYYY</td>
						    </tr>
						    <tr>
							    <td>betrag</td>
							    <td>Betrag</td>
							    <td></td>
						    </tr>
						    <tr>
							    <td>betrag2</td>
							    <td>Betrag (wenn z.B. Soll / Haben getrennt)</td>
							    <td></td>
						    </tr>
						    <tr>
							    <td>waehrung</td>
							    <td>W&auml;hrung</td>
							    <td>Muss in den W&auml;hrungen vorhanden sein</td>
						    </tr>
						    <tr>
							    <td>buchungstext</td>
							    <td>Buchungstext</td>
							    <td>Wird für die Zuordnung verwendet</td>
						    </tr>
						    <tr>
							    <td>buchungstext2</td>
							    <td>Buchungstext</td>
							    <td>Wird für die Zuordnung verwendet</td>
						    </tr>
						    <tr>
							    <td>buchungstext3</td>
							    <td>Buchungstext</td>
							    <td>Wird für die Zuordnung verwendet</td>
						    </tr>
						    <tr>
							    <td>buchungstext4</td>
							    <td>Buchungstext</td>
							    <td>Wird für die Zuordnung verwendet</td>
						    </tr>
					    </table>
				    </fieldset>
			    </div>
		    </div>
	    </div>    
	</div>
    <div id="tabs-8">
	    <div class="row">
		    <div class="col-xs-12 col-sm-1 col-sm-height">
			    <div class="inside inside-full-height">
				    <fieldset><legend>{|St&uuml;ckliste|}</legend>
					    <table class="mkTable">
						    <tr>
							    <th>Variable</th>
							    <th>Beschreibung</th>
							    <th>Kommentar</th>
						    </tr>
						    <tr>
							    <td>stuecklistevonartikel</td>
							    <td>Hauptartikel (muss eine St&uuml;ckliste sein)</td>
							    <td>Artikelnummer</td>
						    </tr>
						    <tr>
							    <td>artikel</td>
							    <td></td>
							    <td>Artikelnummer</td>
						    </tr>						    
						    <tr>
							    <td>menge</td>
							    <td>Zahl</td>
							    <td></td>
						    </tr>
                            <tr>
							    <td>referenz</td>
							    <td>Text</td>
							    <td>F&uuml;r Best&uuml;ckungen</td>
						    </tr>
                            <tr>
							    <td>place</td>
							    <td>Leer oder 'DNP' = Nicht platzieren</td>
							    <td>F&uuml;r Best&uuml;ckungen</td>
						    </tr>
						    <tr>
							    <td>layer</td>
							    <td>"top" oder "bottom"</td>
							    <td>F&uuml;r Best&uuml;ckungen</td>
						    </tr>
						    <tr>
							    <td>wert</td>
							    <td>Text</td>
							    <td>F&uuml;r Best&uuml;ckungen</td>
						    </tr>
						    <tr>
							    <td>bauform</td>
							    <td>Text</td>
							    <td>F&uuml;r Best&uuml;ckungen</td>
						    </tr>
						    <tr>
							    <td>zachse</td>
							    <td>Zahl</td>
							    <td>F&uuml;r Best&uuml;ckungen</td>
						    </tr>                           						   
						    <tr>
							    <td>xpos</td>
							    <td>Zahl</td>
							    <td>F&uuml;r Best&uuml;ckungen</td>
						    </tr>
						    <tr>
							    <td>ypos</td>
							    <td>Zahl</td>
							    <td>F&uuml;r Best&uuml;ckungen</td>
						    </tr>                           
						    <tr>
							    <td>rotation</td>
							    <td>Zahl</td>
							    <td>F&uuml;r Best&uuml;ckungen</td>
						    </tr>
						    <tr>
							    <td>art</td>
							    <td>Einkaufsteil "et",Informationsteil "it" oder Beistellung "bt"</td>
							    <td>Alternativposition?</td>
						    </tr>
					    </table>
				    </fieldset>
			    </div>
		    </div>
	    </div>
    </div>
</div>
