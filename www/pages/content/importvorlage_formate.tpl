<div id="tabs">

	<ul>
		<li><a href="#tabs-1">{|Artikel/Einkauf|}</a></li>
		<li><a href="#tabs-2">{|Adressen|}</a></li>
		<li><a href="#tabs-3">{|Zeiterfassung|}</a></li>
		<li><a href="#tabs-4">{|Wiedervorlagen|}</a></li>
		<li><a href="#tabs-5">{|Notizen|}</a></li>
	</ul>

	<div id="tabs-1">
		<div class="row">
			<div class="col-xs-12 col-sm-1 col-sm-height">
				<div class="inside inside-full-height">
					<fieldset><legend>{|Artikel/Einkauf|}</legend>
						<table class="mkTable">
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
							<tr><td>artikelkategorie (id)</td><td></td><td></td></tr>
							<tr><td>artikelkategorie_name</td><td>Artikelkategorie</td><td>Falls die Artikelkategorie noch nicht vorhanden ist, wird diese neu angelegt</td></tr>
							<tr><td>artikelbaumX (X=1 bis 20; CLEAR um alle Zuordnungen zu löschen)</td><td></td><td></td></tr>
							<tr><td>bildtitelX (X=1 bis 20; X entspricht der Sortierung des Bildes in den Dateien des Artikels)</td><td></td><td></td></tr>
							<tr><td>bildbeschreibung(X=1 bis 20; X entspricht der Sortierung des Bildes in den Dateien des Artikels)</td><td></td><td></td></tr>
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
							<tr><td>mindesthaltbarkeitsdatum (1=aktiv,0=nicht aktiv)</td><td></td><td></td></tr>
							<tr><td>seriennummern (keine,eigene,vomprodukt,vomprodukteinlagern)</td><td></td><td></td></tr>
							<tr><td>chargenverwaltung (0=keine,2=originale nutzen)</td><td></td><td></td></tr>
							<tr><td>mindestlager</td><td>Minimaler Lagerbestand</td><td></td></tr>
							<tr><td>mindestbestellung</td><td></td><td></td></tr>
							<tr><td>umsatzsteuer</td><td>Ermäßigte Umsatzsteuer</td><td>Möglich in CSV: ermaessigt, normal</td></tr>
							<tr><td>artikelabschliessenkalkulation</td><td>Checkbox 'Einkaufspreis beim Abschließen der Bestellung in Kalkulation übernehmen' zu finden unter Artikel / Kalkulation</td><td>Mit dem Wert 1 aktiviert man die Checkbox, mit 0 deaktiviert man sie</td></tr>
							<tr><td>artikelfifokalkulation</td><td>Checkbox 'Kalkulierter EK Preis automatisch nach FIFO Prinzip der letzten Eingänge den Mittelwert neu berechnen' zu finden unter Artikel / Kalkulation</td><td>Mit dem Wert 1 aktiviert man die Checkbox, mit 0 deaktiviert man sie</td></tr>
							<tr><td>lieferantname</td><td></td><td></td></tr>
							<tr><td>lieferantbestellnummer</td><td></td><td></td></tr>
							<tr><td>lieferantartikelbezeichnung</td><td></td><td></td></tr>
							<tr><td>lieferanteinkaufnetto</td><td>Einkaufspreis (netto) pro Stück</td><td>Pflichtfeld beim Import von Einkaufspreisen</td></tr>
							<tr><td>lieferanteinkaufwaehrung</td><td>Währung des EInkaufspreises, z.B. (EUR, CHF, USD)</td><td>Pflichtfeld beim Import von Einkaufspreisen</td></tr>
							<tr><td>lieferanteinkaufmenge</td><td>Staffelmenge des Einkaufspreise z.B. ab 10 Stück = 10, für Standardpreis Menge = 1 angeben</td><td>Pflichtfeld beim Import von Einkaufspreisen</td></tr>
							<tr><td>lieferanteinkaufvpemenge</td><td></td><td></td></tr>
							<tr><td>lieferanteinkaufvpepreis</td><td></td><td></td></tr>
							<tr><td>lieferantbestellnummer2 - 3</td><td>siehe lieferantbestellnummer</td><td></td></tr>
							<tr><td>lieferantartikelbezeichnung2 - 3</td><td>siehe lieferantartikelbezeichnung</td><td></td></tr>
							<tr><td>lieferanteinkaufnetto2 - 3</td><td>siehe lieferanteinkaufnetto</td><td></td></tr>
							<tr><td>lieferanteinkaufwaehrung2 - 3</td><td>siehe lieferanteinkaufwaehrung</td><td></td></tr>
							<tr><td>lieferanteinkaufmenge2 - 3</td><td>siehe lieferanteinkaufmenge</td><td></td></tr>
							<tr><td>lieferanteinkaufvpemenge2 - 3</td><td>siehe lieferanteinkaufvpemenge</td><td></td></tr>
							<tr><td>lieferanteinkaufvpepreis2 - 3</td><td>siehe lieferanteinkaufvpepreis</td><td></td></tr>
							<tr><td>lieferantrahmenvertrag_vonX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantrahmenvertrag_bisX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantpreis_anfrage_vomX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantgueltig_bisX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantdatum_lagerlieferantX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantsicherheitslagerX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantrahmenvertrag_mengeX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantlieferzeit_aktuellX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantlieferzeit_standardX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantlager_lieferantX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantrahmenvertragX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantbemerkungX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantnichtberechnetX (X = leer, bzw 1-3)</td><td></td><td></td></tr>
							<tr><td>lieferantennummer</td><td>Lieferantennummer, muss im System vorhanden sein</td><td>Pflichtfeld für Einkaufpreis-Import</td></tr>
							<tr><td>standardlieferant (Lieferantennummer aus Xentral)</td><td></td><td></td></tr>
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
							<tr><td>lager_platzX (X = 1-5)</td><td>siehe lager_platz</td><td></td></tr>
							<tr><td>lager_menge_addierenX (X = 1-5)</td><td>siehe lager_menge_addieren</td><td></td></tr>
							<tr><td>lager_menge_totalX (X = 1-5)</td><td>siehe lager_menge_total</td><td></td></tr>
							<tr><td>lager_mhdX (X = 1-5)</td><td>siehe lager_mhd</td><td></td></tr>
							<tr><td>lager_chargeX (X = 1-5)</td><td>siehe lager_charge</td><td></td></tr>
							<tr><td>lager_vpe_mengeX (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>lager_vpe_gewichtX (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>lager_vpe_laengeX (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>lager_vpe_breiteX (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>lager_vpe_hoeheX (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>lager_vpe_mengeX2 (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>lager_vpe_gewichtX2 (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>lager_vpe_laengeX2 (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>lager_vpe_breiteX2 (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>lager_vpe_hoeheX2 (X = 1-5)</td><td></td><td></td></tr>
							<tr><td>verkaufspreisXnetto (X = 1-10)</td><td>Verkaufspreis</td><td>Pflichfeld für Verkaufspreis</td></tr>
							<tr><td>verkaufspreisXpreisfuermenge (X = 1-10)</td><td>Angegebener verkaufspreisXnetto gilt für Menge</td><td></td></tr>
							<tr><td>verkaufspreisXmenge (X = 1-10)</td><td>Ab Menge</td><td>Pflichtfeld für Verkaufspreis - Falls Standardpreis (kein Staffelpreis) → als Menge 1 angeben</td></tr>
							<tr><td>verkaufspreisXwaehrung (EUR,CHF,USD,...) (X = 1-10)</td><td>Währung</td><td></td></tr>
							<tr><td>verkaufspreisXgruppe (Kennziffer) (X = 1-10)</td><td>"Gruppenspezifischer Preis für Gruppe</td></tr>
							<tr><td>"</td><td></td></tr>
							<tr><td>verkaufspreisXkundennummer (X = 1-10)</td><td>"Kundenspezifischer Preis für Kunde mit Kundennummer</td></tr>
							<tr><td>"</td><td></td></tr>
							<tr><td>verkaufspreisXartikelnummerbeikunde (X = 1-10)</td><td>Artikelnummer, die der Kunde verwendet (zur Information)</td><td></td></tr>
							<tr><td>verkaufspreisXgueltigab (X = 1-10)</td><td>Startdatum des Verkaufspreises</td><td></td></tr>
							<tr><td>verkaufspreisXgueltigbis (X = 1-10)</td><td>Enddatum des Verkaufspreises</td><td></td></tr>
							<tr><td>verkaufspreisXinternerkommentar (X = 1-10)</td><td>Interner Kommentar z.B. bzgl. besonderer Konditionen oder Gründe für Zeitraum</td><td></td></tr>
							<tr><td>variante_von</td><td>Artikel ist eine Variante von (Artikelnummer)</td><td></td></tr>
							<tr><td>projekt</td><td>Projekt</td><td></td></tr>
							<tr><td>geloescht (auf 1 setzen)</td><td>"Gelöschter Artikel = 1, nicht gelöscht = 0</td></tr>
							<tr><td>"</td><td></td></tr>
							<tr><td>inaktiv</td><td>Inaktiver Artikel = 1, aktiver Artikel = 0</td><td></td></tr>
							<tr><td>aktiv</td><td></td><td></td></tr>
							<tr><td>juststueckliste</td><td></td><td></td></tr>
							<tr><td>stueckliste</td><td></td><td></td></tr>
							<tr><td>stuecklistevonartikel</td><td>In Stückliste (Nummer): Gibt Hauptartikel von diesem Stücklistenartikel an</td><td></td></tr>
							<tr><td>stuecklistemenge</td><td>Anzahl des Artikels in Stücklisten</td><td></td></tr>
							<tr><td>stuecklisteart</td><td>Art der Stückliste:  et = Einkauf, it = Information, bt = Beistellung</td><td></td></tr>
							<tr><td>vkmeldungunterdruecken</td><td></td><td></td></tr>
							<tr><td>shop_shopid (shopid ersetzen durch die ID des Shops)</td><td></td><td></td></tr>
							<tr><td>aktiv_shopid (shopid ersetzen durch die ID des Shops)</td><td></td><td></td></tr>
							<tr><td>fremdnummerX_shopid (shopid ersetzen durch die ID des Shops, X = 1-40)</td><td></td><td></td></tr>
							<tr><td>fremdnummerbezeichnungX_shopid (shopid ersetzen durch die ID des Shops, X = 1-40)</td><td></td><td></td></tr>
							<tr><td>pseudopreis</td><td>Pseudopreis, auch Streichpreis z.B. zur Anzeige im Onlineshop</td><td></td></tr>
							<tr><td>freifeld1 - freifeld40</td><td>Freifelder des Artikels nach eigener Definition im System, eine Typprüfung findet nicht statt</td><td></td></tr>
							<tr><td>intern_gesperrt</td><td></td><td></td></tr>
							<tr><td>intern_gesperrtgrund</td><td></td><td></td></tr>
							<tr><td>autolagerlampe (Lagersync)</td><td></td><td></td></tr>
							<tr><td>pseudolager</td><td></td><td></td></tr>
							<tr><td>lagerkorrekturwert</td><td></td><td></td></tr>
							<tr><td>restmenge</td><td></td><td></td></tr>
							<tr><td>provision1 (in %)</td><td></td><td></td></tr>
							<tr><td>provisiontyp1 (ek, vk, erloes, leer)</td><td></td><td></td></tr>
							<tr><td>provision2 (in %)</td><td></td><td></td></tr>
							<tr><td>provisiontyp2 (ek, vk, erloes, leer)</td><td></td><td></td></tr>
							<tr><td>eigenschaftname1 bis 50 (mehrfach)</td><td></td><td></td></tr>
							<tr><td>eigenschaftnameeindeutig1 bis 50 (einzeln)</td><td></td><td></td></tr>
							<tr><td>eigenschaftwert1 bis 50</td><td></td><td></td></tr>
							<tr><td>eigenschaftname1_xx bis 50 (xx Ländercode en, fr...)</td><td></td><td></td></tr>
							<tr><td>eigenschaftwert1_xx bis 50 (xx Ländercode en, fr...)</td><td></td><td></td></tr>
							<tr><td>matrixprodukt</td><td></td><td></td></tr>
							<tr><td>matrixproduktvon</td><td></td><td></td></tr>
							<tr><td>matrixproduktgruppe1</td><td></td><td></td></tr>
							<tr><td>matrixproduktgruppe2</td><td></td><td></td></tr>
							<tr><td>matrixproduktwert1</td><td></td><td></td></tr>
							<tr><td>matrixproduktwert2</td><td></td><td></td></tr>
							<tr><td>matrixgruppe1 (alle Optionen aus Gruppe1)</td><td></td><td></td></tr>
							<tr><td>matrixgruppe2 (alle Optionen aus Gruppe2)</td><td></td><td></td></tr>
							<tr><td>matrixartikelnummer (1 = Nummernkreis, 2 = Optionnamen als Postfix, 3|trennzeichen|Stellen|Startnummer = Postfix)</td><td></td><td></td></tr>
							<tr><td>matrixnamefuerunterartikel (1 = Optionen werden an Artikelbezeichnung der Unterartikel gehängt, 0 = Artikelbezeichnung wird vom Hauptartikel genommen)</td><td></td><td></td></tr>
							<tr><td>Weitere Sprachen (X = 1-100, YY ersetzt durch den ISO Code der Sprache aus dem Sprachen Modul)</td><td></td><td></td></tr>
							<tr><td>nameX_YY</td><td></td><td></td></tr>
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
							<tr><td>freifeld1_YY - freifeld40_YY</td><td></td><td></td></tr>
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
							<tr><td>titel</td><td>"Akademischer Titel</td></tr>
							<tr><td>"</td><td></td></tr>
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
							<tr><td>rabattX (X = 1 - 5)</td><td></td><td></td></tr>
							<tr><td>bonusX (X = 1-10)</td><td></td><td></td></tr>
							<tr><td>bonusX_ab (X = 1-10)</td><td></td><td></td></tr>
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
</div>
