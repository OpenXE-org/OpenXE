<?php

/* ABSCHLAGSRECHNUNG */

$tooltip['abschlagsrechnung']['auftraglist']['#bezeichnung_text_teilrechnung']="Frei wählbar und taucht so auf dem Rechnungsbeleg auf";
$tooltip['abschlagsrechnung']['auftraglist']['#PosNulluebernehmen']="Die Positionen werden aus dem Auftrag direkt übernommen. Zwar mit Preis aber dafür mit Menge 0, damit die UST nicht vorzeitig abgerechnet wird, man aber trotzdem eine Übersicht über die Positionen in der Teilrechnung hat.";
$tooltip['abschlagsrechnung']['auftraglist']['#artikel_teilzahlung']="Der Artikel, den Sie für die Abschlagsrechnung vorher erstellt hatten. Dieser taucht als Position in der Teilrechnung auf";
$tooltip['abschlagsrechnung']['auftraglist']['#artikel_teilzahlung_bezeichnung']="Der Name des Artikels, wie dieser auf der Teilrechnung auftauchen soll";
$tooltip['abschlagsrechnung']['auftraglist']['#artikel_teilzahlung_beschreibung']="Die Artikelbeschreibung des Artikels, wie diese auf der Teilrechnung auftauchen soll";
$tooltip['abschlagsrechnung']['auftraglist']['#artikel_teilzahlung_menge']="Die Menge des Artikels, wie diese auf der Teilrechnung auftauchen soll";
$tooltip['abschlagsrechnung']['auftraglist']['#artikel_teilzahlung_preis']="Der Preis des Artikels, wie er auf der Teilrechnung auftauchen soll";

/* ADRESSEN */

/* Abo - Gruppen */
$tooltip['adresse']['artikel']['#editbeschreibung']="Name der Gruppe (dies erscheint auf der Rechnung)";
$tooltip['adresse']['artikel']['#editbeschreibung2']="Beschreibung der Gruppe (dies erscheint auf der Rechnung)";
$tooltip['adresse']['artikel']['#editansprechpartner']="Wird für das Ansprechpartner-Feld in der Rechnung übernommen";
$tooltip['adresse']['artikel']['#editprojekt']="Die erstellte Rechnung läuft auf dieses Projekt";
$tooltip['adresse']['artikel']['#editsort']="Wenn mehrere Gruppen in einer Rechnung vorkommen, kann die Reihenfolge der Gruppen damit angepasst werden (ab Version 18.3)";
$tooltip['adresse']['artikel']['#editgruppensumme']="Nach jeder Auflistung der Artikel einer Gruppe, wird eine Gruppensumme auf dem Beleg ausgegeben";
$tooltip['adresse']['artikel']['#editrechnung']="Auswahl wie die Gruppe in die Rechnung kommen soll: <ul><li>Gemeinsame Rechnung: Die Gruppen werden untereinander in einer gemeinsamen Rechnung gelistet</li><li>Eigene Rechnung: Für jede Gruppe wird eine eigene Rechnung erstellt</li><li>Sammelrechnung: Mehrere Gruppen können zu einer Sammelrechnung zusammengefasst werden. Nach der Auswahl im Dropdown erhalten Sie ein Suchfeld für eine Sammelrechnung, die unter Adresse ⇒ Abo ⇒ Sammelrechnung angelegt wurde (ab Version 18.3)</li></ul>";
$tooltip['adresse']['artikel']['#editrechnungadresse']="Wird als Rechnungsadresse für die erstellte Abo-Rechnung genommen";
$tooltip['adresse']['artikel']['#editsammelrechnung']="Auswahl der unter Adresse => Abo => Sammelrechnung erstellten Sammelrechnung";

/* Abo - Sammelrechnung */
$tooltip['adresse']['artikel']['#e_bezeichnung']="Name der Sammelrechnung - wird nur gebraucht zur Auswahl in den Abo-Gruppen";
$tooltip['adresse']['artikel']['#e_abwrechnungsadresse']="Optional, wird als Rechnungsadresse in der erstellen Abo Rechnung genommen";
$tooltip['adresse']['artikel']['#e_projekt']="Optional, bestimmt das Rechnungs-Projekt der Sammelrechnung";

/* Details - Adressdaten */
$tooltip['adresse']['create']['#typ']="Wichtig für Netto-/Bruttopreisanzeige in den Dokumenten. Kann aber auch unter Administration => Grundeinstellungen => Steuer/Währung eingestellt werden";
$tooltip['adresse']['edit']['#typ']="Wichtig für Netto-/Bruttopreisanzeige in den Dokumenten. Kann aber auch unter Administration => Grundeinstellungen => Steuer/Währung eingestellt werden";
$tooltip['adresse']['create']['#name']="Entspricht Zeile 1 im Adressblock des Briefpapiers";
$tooltip['adresse']['edit']['#name']="Entspricht Zeile 1 im Adressblock des Briefpapiers";
$tooltip['adresse']['create']['#titel']="Erscheint vor dem Namen im Adressblock des Briefpapiers";
$tooltip['adresse']['edit']['#titel']="Erscheint vor dem Namen im Adressblock des Briefpapiers";
$tooltip['adresse']['create']['#ansprechpartner']="Entspricht Zeile 2 im Adressblock des Briefpapiers";
$tooltip['adresse']['edit']['#ansprechpartner']="Entspricht Zeile 2 im Adressblock des Briefpapiers";
$tooltip['adresse']['create']['#abteilung']="Entspricht Zeile 3 im Adressblock des Briefpapiers";
$tooltip['adresse']['edit']['#abteilung']="Entspricht Zeile 3 im Adressblock des Briefpapiers";
$tooltip['adresse']['create']['#unterabteilung']="Entspricht Zeile 4 im Adressblock des Briefpapiers";
$tooltip['adresse']['edit']['#unterabteilung']="Entspricht Zeile 4 im Adressblock des Briefpapiers";
$tooltip['adresse']['create']['#adresszusatz']="Entspricht Zeile 5 im Adressblock des Briefpapiers";
$tooltip['adresse']['edit']['#adresszusatz']="Entspricht Zeile 5 im Adressblock des Briefpapiers";
$tooltip['adresse']['create']['#strasse']="Entspricht Zeile 6 im Adressblock des Briefpapiers";
$tooltip['adresse']['edit']['#strasse']="Entspricht Zeile 6 im Adressblock des Briefpapiers";
$tooltip['adresse']['create']['#ort']="Entspricht Zeile 7 im Adressblock des Briefpapiers";
$tooltip['adresse']['edit']['#ort']="Entspricht Zeile 7 im Adressblock des Briefpapiers";
$tooltip['adresse']['create']['#land']="Entspricht Zeile 8 im Adressblock des Briefpapiers, wenn es nicht Inland ist";
$tooltip['adresse']['edit']['#land']="Entspricht Zeile 8 im Adressblock des Briefpapiers, wenn es nicht Inland ist";

$tooltip['adresse']['create']['#gln']="GLN steht für Global Location Number und dient zur Identifizierung von Handelspartnern. Diese Nummer wird nur im Zusammenhang mit einer EDI Schnittstelle benötigt und kann sonst leer gelassen werden.";
$tooltip['adresse']['edit']['#gln']="GLN steht für Global Location Number und dient zur Identifizierung von Handelspartnern. Diese Nummer wird nur im Zusammenhang mit einer EDI Schnittstelle benötigt und kann sonst leer gelassen werden.";

$tooltip['adresse']['create']['#kundenfreigabe']="Nur relevant, wenn ein kundenspezifischer Check programmiert wurde. Bsp.: Bei aus einem Shop importierten Aufträge, soll erst überprüft werden, ob der Kunde ein Student ist. => Studentenshop mit Immatrikulation.";
$tooltip['adresse']['edit']['#kundenfreigabe']="Nur relevant, wenn ein kundenspezifischer Check programmiert wurde. Bsp.: Bei aus einem Shop importierten Aufträge, soll erst überprüft werden, ob der Kunde ein Student ist. => Studentenshop mit Immatrikulation.";

$tooltip['adresse']['create']['#marketingsperre'] = "Ist die Marketingsperre aktiv, bekommt der Kunde keine Serienbriefe.";
$tooltip['adresse']['edit']['#marketingsperre'] = $tooltip['adresse']['create']['#marketingsperre'];

/* Details - Zahlungskonditionen / Besteuerung */
$tooltip['adresse']['create']['#art']="Einstelllung, welche Belege im Rahmen des Autoversands erstellt und ggf. versendet bzw. gedruckt werden";
$tooltip['adresse']['edit']['#art']="Einstelllung, welche Belege im Rahmen des Autoversands erstellt und ggf. versendet bzw. gedruckt werden";

$tooltip['adresse']['create']['#hinweistextlieferant']="Hinweistext wird beim Einfügen des Artikels in einen Kundenbeleg als Hinweis angezeigt, wenn im Artikel der Lieferant als Standardlieferant gewählt ist.";
$tooltip['adresse']['edit']['#hinweistextlieferant'] = $tooltip['adresse']['create']['#hinweistextlieferant'];

$tooltip['adresse']['create']['ustid'] = 'Vollständige Ust.-ID inklusive Länderkürzel (z.B. DE999999999, ATU99999999, FRXY999999999)';
$tooltip['adresse']['edit']['ustid'] = $tooltip['adresse']['create']['ustid'];

$tooltip['adresse']['create']['anzeigesteuerbelege']="Anzeige von Belegen mit Brutto / Netto Werten. <br>Weitere Einstellungsmöglichkeiten unter Administration => Einstellungen => Grundeinstellungen => Steuer / Währung und im Projekt => Einstellungen => Steuer / Währung. Einstellungen aus Adresse sticht Einstellungen aus Projekt. Einstellungen aus Projekt sticht Grundeinstellungen.";
$tooltip['adresse']['edit']['anzeigesteuerbelege']="Anzeige von Belegen mit Brutto / Netto Werten. <br>Weitere Einstellungsmöglichkeiten unter Administration => Einstellungen => Grundeinstellungen => Steuer / Währung und im Projekt => Einstellungen => Steuer / Währung. Einstellungen aus Adresse sticht Einstellungen aus Projekt. Einstellungen aus Projekt sticht Grundeinstellungen.";

/* Details - Bankverbindung */
$tooltip['adresse']['edit']['#inhaber']="Wenn dieses Feld nicht gefüllt ist, wird der 'Name/Firmenname' aus den Stammdaten bei Überweisungen/Lastschrifteinzug als Inhaber automatisch gezogen.";
$tooltip['adresse']['edit']['#mandatsreferenzaenderung']="Wenn sich die Mandatsreferenz für diese Adresse ändert kann man den Haken setzen.<br>Dadurch wird im SEPA Format eine andere XML erzeugt.";
$tooltip['adresse']['edit']['#firmensepa']="Es wird ein anderes SEPA Mandat speziell für Firmen erzeugt.";

/* Details - Dokumente Versandoptionen */
$tooltip['adresse']['create']['#rechnung_papier']= 'Diese Option wird u.a. im Abolauf ausgewertet, sodass zusätzlich zur Rechnungs-E-Mail immer auch eine Rechnung auf Papier für den Kunden ausgedruckt wird.<br>Die ausgedruckte Rechnung kann dem Kunden dann separat zugeschickt oder ins Paket gelegt werden.';
$tooltip['adresse']['edit']['#rechnung_papier']= 'Diese Option wird u.a. im Abolauf ausgewertet, sodass zusätzlich zur Rechnungs-E-Mail immer auch eine Rechnung auf Papier für den Kunden ausgedruckt wird.<br>Die ausgedruckte Rechnung kann dem Kunden dann separat zugeschickt oder ins Paket gelegt werden.';
$tooltip['adresse']['create']['#rechnung_anzahlpapier']="Dies wird im Versandzentrum (Logistikprozess LS + auslagern + scannen) und im Multiorderpicking (extra Modul) ausgewertet.";
$tooltip['adresse']['edit']['#rechnung_anzahlpapier']="Dies wird im Versandzentrum (Logistikprozess LS + auslagern + scannen) und im Multiorderpicking (extra Modul) ausgewertet.";
$tooltip['adresse']['create']['#angebot_email']="Es wid die Adress-E-Mail von der hier eingetragenen Mail ersetzt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#angebot_email']=$tooltip['adresse']['create']['#angebot_email'];
$tooltip['adresse']['create']['#auftrag_email']="Es wid die Adress-E-Mail von der hier eingetragenen Mail ersetzt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#auftrag_email']=$tooltip['adresse']['create']['#auftrag_email'];
$tooltip['adresse']['create']['#rechnungs_email']="Es wid die Adress-E-Mail von der hier eingetragenen Mail ersetzt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#rechnungs_email']=$tooltip['adresse']['create']['#rechnungs_email'];
$tooltip['adresse']['create']['#gutschrift_email']="Es wid die Adress-E-Mail von der hier eingetragenen Mail ersetzt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#gutschrift_email']=$tooltip['adresse']['create']['#gutschrift_email'];
$tooltip['adresse']['create']['#lieferschein_email']="Es wid die Adress-E-Mail von der hier eingetragenen Mail ersetzt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#lieferschein_email']=$tooltip['adresse']['create']['#lieferschein_email'];
$tooltip['adresse']['create']['#bestellung_email']="Es wid die Adress-E-Mail von der hier eingetragenen Mail ersetzt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#bestellung_email']=$tooltip['adresse']['create']['#bestellung_email'];

$tooltip['adresse']['create']['#angebot_cc']="Es wird eine zusätzliche E-Mail an die hier eingetragene E-Mail als Kopie geschickt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#angebot_cc']=$tooltip['adresse']['create']['#angebot_cc'];
$tooltip['adresse']['create']['#auftrag_cc']="Es wird eine zusätzliche E-Mail an die hier eingetragene E-Mail als Kopie geschickt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#auftrag_cc']=$tooltip['adresse']['create']['#auftrag_cc'];
$tooltip['adresse']['create']['#rechnung_cc']="Es wird eine zusätzliche E-Mail an die hier eingetragene E-Mail als Kopie geschickt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#rechnung_cc']=$tooltip['adresse']['create']['#rechnung_cc'];
$tooltip['adresse']['create']['#gutschrift_cc']="Es wird eine zusätzliche E-Mail an die hier eingetragene E-Mail als Kopie geschickt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#gutschrift_cc']=$tooltip['adresse']['create']['#gutschrift_cc'];
$tooltip['adresse']['create']['#lieferschein_cc']="Es wird eine zusätzliche E-Mail an die hier eingetragene E-Mail als Kopie geschickt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#lieferschein_cc']=$tooltip['adresse']['create']['#lieferschein_cc'];
$tooltip['adresse']['create']['#bestellung_cc']="Es wird eine zusätzliche E-Mail an die hier eingetragene E-Mail als Kopie geschickt.<br>Nur eine E-Mail-Adresse pro Beleg möglich. Bitte nur eine Adresse angeben.";
$tooltip['adresse']['edit']['#bestellung_cc']=$tooltip['adresse']['create']['#bestellung_cc'];


/* Details - Sonstige Daten */
$tooltip['adresse']['create']['#provision']="Diese Option wird für das alte Modul 'Vertreterabrechnung' ausgewertet.";
$tooltip['adresse']['edit']['#provision']="Diese Option wird für das alte Modul 'Vertreterabrechnung' ausgewertet.";
$tooltip['adresse']['create']['#geburtstagskarte']="Diese Option wird im extra Modul 'Serienbrief' ausgewertet.";
$tooltip['adresse']['edit']['#geburtstagskarte']="Diese Option wird im extra Modul 'Serienbrief' ausgewertet.";
$tooltip['adresse']['create']['#lat']="Dieses Feld wird automatisch über einen Prozessstarter in Verbindung mit Openstreetmap gefüllt: <a target='_blank' href='https://www.wawision.de/helpdesk/kurzanleitung-umkreissuche#nav-prozessstarter-erstellen' >Handbuch Eintrag<a/>";
$tooltip['adresse']['edit']['#lat']="Dieses Feld wird automatisch über einen Prozessstarter in Verbindung mit Openstreetmap gefüllt: <a target='_blank' href='https://www.wawision.de/helpdesk/kurzanleitung-umkreissuche#nav-prozessstarter-erstellen' >Handbuch Eintrag<a/>";
$tooltip['adresse']['create']['#lng']="Dieses Feld wird automatisch über einen Prozessstarter in Verbindung mit Openstreetmap gefüllt: <a target='_blank' href='https://www.wawision.de/helpdesk/kurzanleitung-umkreissuche#nav-prozessstarter-erstellen' >Handbuch Eintrag<a/>";
$tooltip['adresse']['edit']['#lng']="Dieses Feld wird automatisch über einen Prozessstarter in Verbindung mit Openstreetmap gefüllt: <a target='_blank' href='https://www.wawision.de/helpdesk/kurzanleitung-umkreissuche#nav-prozessstarter-erstellen' >Handbuch Eintrag<a/>";
$tooltip['adresse']['create']['#kreditlimit']="Das Kreditlimit dient im Auftrag als zusätzliche Abfrage für das Auftrags-Ampelsystem.<br>Wenn der Gesamtbetrag des Auftrags das hinterlegte Kreditlimit überschreitet, schaltet sich die entsprechende Ampel für das Kreditlimit auf rot und es ist kein Autoversand mehr möglich.";
$tooltip['adresse']['edit']['#kreditlimit']="Das Kreditlimit dient im Auftrag als zusätzliche Abfrage für das Auftrags-Ampelsystem.<br>Wenn der Gesamtbetrag des Auftrags das hinterlegte Kreditlimit überschreitet, schaltet sich die entsprechende Ampel für das Kreditlimit auf rot und es ist kein Autoversand mehr möglich.";
$tooltip['adresse']['create']['#kreditlimiteinmalig']="Das Kreditlimit kann einmalig für einen Auftrag erhöht werden. Der Wert hier gilt <strong>zusätzlich</strong> zum eigentlichen Kreditlimit.<br>Sobald man den Autoversand des Auftrags durchführt, wird der Wert hier wieder auf 0 gesetzt und das eigentliche Kreditlimit des Kunden gilt wieder.";
$tooltip['adresse']['edit']['#kreditlimiteinmalig']="Das Kreditlimit kann einmalig für einen Auftrag erhöht werden. Der Wert hier gilt <strong>zusätzlich</strong> zum eigentlichen Kreditlimit.<br>Sobald man den Autoversand des Auftrags durchführt, wird der Wert hier wieder auf 0 gesetzt und das eigentliche Kreditlimit des Kunden gilt wieder.";
$tooltip['adresse']['create']['#logfile']="Dieses Feld wird nur von extern angebundenen Tools benutzt. <br>Hier können Sie keinen Text speichern, es wird nur ein Text lesend angezeigt, wenn das Feld von außen über die API angesprochen wird.";
$tooltip['adresse']['edit']['#logfile']="Dieses Feld wird nur von extern angebundenen Tools benutzt. <br>Hier können Sie keinen Text speichern, es wird nur ein Text lesend angezeigt, wenn das Feld von außen über die API angesprochen wird.";


/* ANGEBOT */
$tooltip['angebot']['create']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";
$tooltip['angebot']['edit']['#ohne_artikeltext']=$tooltip['angebot']['create']['#ohne_artikeltext'];
$tooltip['angebot']['create']['#kurs']="Der aktuelle Wechselkurs kann in der App 'Währung Umrechnung' von der European Central Bank abgerufen werden.";
$tooltip['angebot']['edit']['#kurs']=$tooltip['angebot']['create']['#kurs'];



/* API-ACCOUNT */

$tooltip['api_account']['create']['#initkey']="Muss selber vergeben werden, mind. 12 Stellen => Wird für Fremd-API Systeme verwendet";
$tooltip['api_account']['edit']['#initkey']="Muss selber vergeben werden, mind. 12 Stellen => Wird für Fremd-API Systeme verwendet";



/* ARTIKEL */

/* Details - Artikel */

$tooltip['artikel']['create']['#name_de']="Textuelle Bezeichnung des Artikels (Beschränkung auf 255 Zeichen), Sprache Inland (z.B. deutsch)";
$tooltip['artikel']['edit']['#name_de']="Textuelle Bezeichnung des Artikels (Beschränkung auf 255 Zeichen), Sprache Inland (z.B. deutsch)";
$tooltip['artikel']['create']['#nummer']="Artikelnummer (Zahlen oder Zeichenfolge. Zahlen werden im Nummernkreis automatisch vergeben. Hierzu Feld beim neu Anlegen eines Artikels leer lassen und speichern. Die nächst höhere Nummer in diesem Nummernkreis wird dann direkt vergeben)";
$tooltip['artikel']['edit']['#nummer']="Artikelnummer (Zahlen oder Zeichenfolge. Zahlen werden im Nummernkreis automatisch vergeben. Hierzu Feld beim neu Anlegen eines Artikels leer lassen und speichern. Die nächst höhere Nummer in diesem Nummernkreis wird dann direkt vergeben)";
$tooltip['artikel']['create']['#projekt']="Artikel können auch auf Projekte beschränkt oder auf öffentliche Projekte gebucht werden";
$tooltip['artikel']['edit']['#projekt']="Artikel können auch auf Projekte beschränkt oder auf öffentliche Projekte gebucht werden";
$tooltip['artikel']['create']['#typ']="Nummernkreis/ Kategorie, auf die der Artikel gebucht wird";
$tooltip['artikel']['edit']['#typ']="Nummernkreis/ Kategorie, auf die der Artikel gebucht wird";
$tooltip['artikel']['create']['#adresse']="Standardlieferant für diesen Artikel.<br><br>Dieser Lieferant wird im Einkaufspreis über den 'Standard laden' Button in die entsprechenden Felder eingefügt.";
$tooltip['artikel']['edit']['#adresse']="Standardlieferant für diesen Artikel.<br><br>Dieser Lieferant wird im Einkaufspreis über den 'Standard laden' Button in die entsprechenden Felder eingefügt.";
$tooltip['artikel']['create']['#anabregs_text']="Beschreibungstext zum Artikel für Angebote, Aufträge, etc.";
$tooltip['artikel']['edit']['#anabregs_text']="Beschreibungstext zum Artikel für Angebote, Aufträge, etc.";
$tooltip['artikel']['create']['#kurztext_de']="Für Volltext-Suche (Stammdaten→ Artikel) oder Online-Shops Gruppentext (z.B. bei Shopware)";
$tooltip['artikel']['edit']['#kurztext_de']="Für Volltext-Suche (Stammdaten→ Artikel) oder Online-Shops Gruppentext (z.B. bei Shopware)";
$tooltip['artikel']['create']['#internerkommentar']="Textfeld für interne Informationen zu Artikeln";
$tooltip['artikel']['edit']['#internerkommentar']="Textfeld für interne Informationen zu Artikeln";

$tooltip['artikel']['create']['#hersteller']="Hier können Sie den Hersteller des Artikels pflegen. Sind Hersteller und Lieferant des Artikels gleich, kann hier auch ein Lieferant aus den Stammdaten hinterlegt werden.<br>Andernfalls kann der Name des Herstellers frei eingetragen werden.";
$tooltip['artikel']['edit']['#hersteller']=$tooltip['artikel']['create']['#hersteller'];
$tooltip['artikel']['create']['#herstellerlink']="Link zur Webseite oder Kontaktadresse des Herstellers für diesen Artikel. ";
$tooltip['artikel']['edit']['#herstellerlink']=$tooltip['artikel']['create']['#herstellerlink'];
$tooltip['artikel']['create']['#herstellernummer']="Nummer des Artikels bei seinem ursprünglichen Hersteller. Diese Nummer kann beim Scan in einigen Oberflächen in Xentral ausgewertet werden.";
$tooltip['artikel']['edit']['#herstellernummer']=$tooltip['artikel']['create']['#herstellernummer'];
$tooltip['artikel']['create']['#ean']="EAN oder sonst. Nummer des Artikels";
$tooltip['artikel']['edit']['#ean']="EAN oder sonst. Nummer des Artikels";
$tooltip['artikel']['create']['#zolltarifnummer']="Die Zolltarifnummer wird beim Artikel auf der Rechnung abgedruckt, sofern der Beleg als „Export“ behandelt wird und die entsprechende Option in den Systemeinstellungen ausgewählt wurde";
$tooltip['artikel']['edit']['#zolltarifnummer']="Die Zolltarifnummer wird beim Artikel auf der Rechnung abgedruckt, sofern der Beleg als „Export“ behandelt wird und die entsprechende Option in den Systemeinstellungen ausgewählt wurde";
$tooltip['artikel']['create']['#herkunftsland']="Herkunftsland des Artikels gepflegt mit 2-stelligen Länderkürzel nach ISO-Alpha 2 (also z.B. DE oder AT)";
$tooltip['artikel']['edit']['#herkunftsland']="Herkunftsland des Artikels gepflegt mit 2-stelligen Länderkürzel nach ISO-Alpha 2 (also z.B. DE oder AT)";
$tooltip['artikel']['create']['#ursprungsregion']="Ursprungsregion des Artikels";
$tooltip['artikel']['edit']['#ursprungsregion']="Ursprungsregion des Artikels";

$tooltip['artikel']['create']['#mindestlager']="Mindestlagermenge für diesen Artikel. Wird diese Menge unterschritten, werden die Artikel farblich markiert in der Liste „Einkauf → Bestellvorschlag“ angezeigt";
$tooltip['artikel']['edit']['#mindestlager']="Mindestlagermenge für diesen Artikel. Wird diese Menge unterschritten, werden die Artikel farblich markiert in der Liste „Einkauf → Bestellvorschlag“ angezeigt";
$tooltip['artikel']['create']['#mindestbestellung']="Die Mindestbestellmenge findet im Bestellvorschlag Anwendung. Die nachzubestellende Menge wird dann immer mindestens in Höhe der Mindestbestellmenge vorgeschlagen.";
$tooltip['artikel']['edit']['#mindestbestellung']="Die Mindestbestellmenge findet im Bestellvorschlag Anwendung. Die nachzubestellende Menge wird dann immer mindestens in Höhe der Mindestbestellmenge vorgeschlagen.";
$tooltip['artikel']['create']['#lager_platz']="Fester Lagerplatz für diesen Artikel. Artikel können auch an mehreren Lagerplätzen eingelagert werden. Besitzt der Artikel ein Standardlager, wird dieser mit vorgeschlagen. Bei einigen automatischen Prozessen wird z.B. auch das Standardlager bebucht. Beim Ausbuchen des Artikels im Lieferschein, wird weiterhin das Lager mit der geringeren Menge bevorzugt.";
$tooltip['artikel']['edit']['#lager_platz']="Fester Lagerplatz für diesen Artikel. Artikel können auch an mehreren Lagerplätzen eingelagert werden. Besitzt der Artikel ein Standardlager, wird dieser mit vorgeschlagen. Bei einigen automatischen Prozessen wird z.B. auch das Standardlager bebucht. Beim Ausbuchen des Artikels im Lieferschein, wird weiterhin das Lager mit der geringeren Menge bevorzugt.";
$tooltip['artikel']['create']['#xvp']="Xentral Volume Point. Über diesen Wert kann der Platzbedarf im Lager (z.B. bzgl. der Lagerkapazitäten) und beim Versand (z.B. bzgl. der Größe von Versandverpackungen) definiert werden.";
$tooltip['artikel']['edit']['#xvp']="Xentral Volume Point. Über diesen Wert kann der Platzbedarf im Lager (z.B. bzgl. der Lagerkapazitäten) und beim Versand (z.B. bzgl. der Größe von Versandverpackungen) definiert werden.";
$tooltip['artikel']['create']['#einheit']="Einheiten können unter Administration → Einstellungen → Artikel Einheiten angelegt werden. Angelegte Einheiten können hier ausgewählt werden. Artikel Einheiten sind z.B. Stück, Stunden oder Kilogramm. Ausgangsmenge ist immer die ganze Einheit, z.B. 1 Stück, 1 Stunde, 1 kg.";
$tooltip['artikel']['edit']['#einheit'] = $tooltip['artikel']['create']['#einheit'];
$tooltip['artikel']['create']['#gewicht']="Gewicht des Artikels in kg (dieses Gewicht kann bei dem Ausdruck von Paketmarken anhand der Artikel pro Auftrag als Vorschlag berechnet werden)";
$tooltip['artikel']['edit']['#gewicht']="Gewicht des Artikels in kg (dieses Gewicht kann bei dem Ausdruck von Paketmarken anhand der Artikel pro Auftrag als Vorschlag berechnet werden)";
$tooltip['artikel']['create']['#laenge']="Länge des Artikels in cm";
$tooltip['artikel']['edit']['#laenge']="Länge des Artikels in cm";
$tooltip['artikel']['create']['#breite']="Breite des Artikels in cm";
$tooltip['artikel']['edit']['#breite']="Breite des Artikels in cm";
$tooltip['artikel']['create']['#hoehe']="Höhe des Artikels in cm";
$tooltip['artikel']['edit']['#hoehe']="Höhe des Artikels in cm";
$tooltip['artikel']['create']['#abckategorie']="Das ist eine Klassifizierung für einen bestimmten Regalplatz. Dies dient bisher nur als Information, wird später aber in diversen Ein/Auslager-Oberflächen als Hinweis angezeigt.";
$tooltip['artikel']['edit']['#abckategorie']=$tooltip['artikel']['create']['#abckategorie'];

$tooltip['artikel']['create']['#lagerartikel']="Bestimmt, ob der Artikel eingelagert werden kann und als Bestand im Lager geführt wird. Nur für Lagerartikel erfolgt in den Logistikprozessen ein automatischer Lagerabzug";
$tooltip['artikel']['edit']['#lagerartikel']="Bestimmt, ob der Artikel eingelagert werden kann und als bestand im Lager geführt wird. Nur für Lagerartikel erfolgt in den Logistikprozessen ein automatischer Lagerabzug";
$tooltip['artikel']['create']['#porto']="Bestimmt ob ein Artikel eine Versandgebühr ist (z.B. Porto Inland, Versand Premium, Nachnahmegebühr, etc.). Diese Artikel werden beim Scan im Versandzentrum nicht abgefragt und in Statistiken z.B. anders/nicht angezeigt";
$tooltip['artikel']['edit']['#porto']="Bestimmt ob ein Artikel eine Versandgebühr ist (z.B. Porto Inland, Versand Premium, Nachnahmegebühr, etc.). Diese Artikel werden beim Scan im Versandzentrum nicht abgefragt und in Statistiken z.B. anders/nicht angezeigt";
$tooltip['artikel']['create']['#rabatt']="Prozent-Rabatt für die gesamte Auftragssumme (z.B. 10%, 20%, etc.). Diese Artikel werden beim Scan im Versandzentrum nicht abgefragt und in Statistiken z.B. anders/nicht angezeigt";
$tooltip['artikel']['edit']['#rabatt']="Prozent-Rabatt für die gesamte Auftragssumme (z.B. 10%, 20%, etc.). Diese Artikel werden beim Scan im Versandzentrum nicht abgefragt und in Statistiken z.B. anders/nicht angezeigt";

$tooltip['artikel']['create']['#variante']="Dieser Artikel ist eine Variante (Artikel im nächsten Feld angeben!)";
$tooltip['artikel']['edit']['#variante']="Dieser Artikel ist eine Variante (Artikel im nächsten Feld angeben!)";
$tooltip['artikel']['create']['#variante_von']="Artikel, zu dem dieser Artikel eine Variante ist";
$tooltip['artikel']['edit']['#variante_von']="Artikel, zu dem dieser Artikel eine Variante ist";
$tooltip['artikel']['create']['#variante_kopie']="Beim Kopieren (Shopimporter) wird der neue Artikel automatisch eine Variante dieses Artikels";
$tooltip['artikel']['edit']['#variante_kopie']="Beim Kopieren (Shopimporter) wird der neue Artikel automatisch eine Variante dieses Artikels";
$tooltip['artikel']['create']['#unikat']="Der Artikel wird nicht für den Bestellvorschlag verwendet.<br><br>Wenn der Artikel in einen Beleg eingefügt wird, öffnet sich automatisch das Artikel bearbeiten Popup, um die Artikelbeschreibung individuell anzugeben.<br><br>(Im extra Modul Prozessmonitor ist die Markierung wichtig, damit die Auftragspositionen auslesbar sind.)";
$tooltip['artikel']['edit']['#unikat']="Der Artikel wird nicht für den Bestellvorschlag verwendet.<br><br>Wenn der Artikel in einen Beleg eingefügt wird, öffnet sich automatisch das Artikel bearbeiten Popup, um die Artikelbeschreibung individuell anzugeben.<br><br>(Im extra Modul Prozessmonitor ist die Markierung wichtig, damit die Auftragspositionen auslesbar sind.)";
$tooltip['artikel']['create']['#matrixprodukt']="Produktoptionen für Modul „Matrixprodukt“";
$tooltip['artikel']['edit']['#matrixprodukt']="Produktoptionen für Modul „Matrixprodukt“";
$tooltip['artikel']['create']['#tagespreise']="Produktoptionen für Modul „Tagespreis“";
$tooltip['artikel']['edit']['#tagespreise']="Produktoptionen für Modul „Tagespreis“";

$tooltip['artikel']['create']['#umsatzsteuer']="normal (Standardeinstellung), ermaessigt, befreit";
$tooltip['artikel']['edit']['#umsatzsteuer']="normal (Standardeinstellung), ermaessigt, befreit";
$tooltip['artikel']['create']['#keinrabatterlaubt']="Dieser Artikel erlaubt keine globalen Rabatte";
$tooltip['artikel']['edit']['#keinrabatterlaubt']="Dieser Artikel erlaubt keine globalen Rabatte";
$tooltip['artikel']['create']['#provisionssperre']="Dieser Artikel wird nicht für hinterlegte Provisionen berücksichtigt";
$tooltip['artikel']['edit']['#provisionssperre']="Dieser Artikel wird nicht für hinterlegte Provisionen berücksichtigt";
$tooltip['artikel']['create']['#chargenverwaltung']='Bei Artikeln mit aktiver Chargenverwaltung werden alle Einheiten mit einer eindeutigen Chargennummer im Bestand geführt. Beim Ein- und Auslagern muss die Charge dann immer gescannt oder angegeben werden.';
$tooltip['artikel']['edit']['#chargenverwaltung']='Bei Artikeln mit aktiver Chargenverwaltung werden alle Einheiten mit einer eindeutigen Chargennummer im Bestand geführt. Beim Ein- und Auslagern muss die Charge dann immer gescannt oder angegeben werden.';
$tooltip['artikel']['create']['#seriennummern']="Keine (Standardeinstellung), eigene erzeugen, originale nutzen, originale einlagern + nutzen";
$tooltip['artikel']['edit']['#seriennummern']="Keine (Standardeinstellung), eigene erzeugen, originale nutzen, originale einlagern + nutzen";
$tooltip['artikel']['create']['#mindesthaltbarkeitsdatum']="Mindesthaltbarkeitsdatum mit einlagern";
$tooltip['artikel']['edit']['#mindesthaltbarkeitsdatum']="Mindesthaltbarkeitsdatum mit einlagern";
$tooltip['artikel']['create']['#allelieferanten']="Artikel kann in Bestellungen jedes Lieferanten eingefügt werden (nicht nur in die, für die ein EK gepflegt ist)";
$tooltip['artikel']['edit']['#allelieferanten']="Artikel kann in Bestellungen jedes Lieferanten eingefügt werden (nicht nur in die, für die ein EK gepflegt ist)";
$tooltip['artikel']['create']['#inventursperre']="Wird nicht in der Inventur berücksichtigt (kann aber dennoch gezählt werden)";
$tooltip['artikel']['edit']['#inventursperre']="Wird nicht in der Inventur berücksichtigt (kann aber dennoch gezählt werden)";
$tooltip['artikel']['create']['#inventurek']="Alternativer Wert, der statt dem Einkaufspreis für die Inventur als Betrag verwendet werden soll";
$tooltip['artikel']['edit']['#inventurek']="Alternativer Wert, der statt dem Einkaufspreis für die Inventur als Betrag verwendet werden soll";
$tooltip['artikel']['create']['#berechneterek']="Für Kalkulationen, Lagerbestände etc. wird dieser Wert als alternativer Einkaufspreis verwendet (wenn möglich). <br>In Beleg-Positionen wird dieser auch für den Deckungsbeitrag herangezogen.";
$tooltip['artikel']['edit']['#berechneterek']="Für Kalkulationen, Lagerbestände etc. wird dieser Wert als alternativer Einkaufspreis verwendet (wenn möglich). <br>In Beleg-Positionen wird dieser auch für den Deckungsbeitrag herangezogen.";
$tooltip['artikel']['create']['#vkmeldungunterdruecken']="Meldung erscheint für diesen Artikel beim Einfügen in Positionen (z.B. Auftrag) nicht mehr. Z.B. für Artikel, die immer kostenlos/ohne Preis sind.";
$tooltip['artikel']['edit']['#vkmeldungunterdruecken']="Meldung erscheint für diesen Artikel beim Einfügen in Positionen (z.B. Auftrag) nicht mehr. Z.B. für Artikel, die immer kostenlos/ohne Preis sind.";

$tooltip['artikel']['create']['#stueckliste']="Ein aus mehreren Einzelteilen bestehender Artikel (Zusammenstellung z.B. über eine Produktion, Einlagerung als ein Artikel, der Inhalt ist im Artikel gebunden)";
$tooltip['artikel']['edit']['#stueckliste']="Ein aus mehreren Einzelteilen bestehender Artikel (Zusammenstellung z.B. über eine Produktion, Einlagerung als ein Artikel, der Inhalt ist im Artikel gebunden)";
$tooltip['artikel']['create']['#juststueckliste']="Ein aus mehreren Einzelteilen bestehender Set-Artikel (Zusammenstellung unmittelbar in der Kommissionierung, die Einzelbestandteile sind auf diese Weise für viele Stücklisten verfügbar und nicht an eine Stückliste gebunden)";
$tooltip['artikel']['edit']['#juststueckliste']="Ein aus mehreren Einzelteilen bestehender Set-Artikel (Zusammenstellung unmittelbar in der Kommissionierung, die Einzelbestandteile sind auf diese Weise für viele Stücklisten verfügbar und nicht an eine Stückliste gebunden)";
$tooltip['artikel']['create']['#keineeinzelartikelanzeigen']="Die Stücklistenbestandteile werden nicht im Dokument angezeigt";
$tooltip['artikel']['edit']['#keineeinzelartikelanzeigen']="Die Stücklistenbestandteile werden nicht im Dokument angezeigt";
$tooltip['artikel']['create']['#has_preproduced_partlist']="Hier können Sie eine alternative Stückliste (normale Stückliste als Lagerartikel - nicht JIT) zu diesem JIT Artikel angeben, die Sie schon parallel einlagern können.<br><br>Wenn beim Einfügen der Positionen im Auftrag die Menge komplett aus der vorproduzierten Stückliste genommen werden kann, werden die Artikel ausgetauscht und die JIT Stückliste durch die vorproduzierte Stückliste in der Auftrags-Position ersetzt.";
$tooltip['artikel']['edit']['#has_preproduced_partlist']=$tooltip['artikel']['edit']['#has_preproduced_partlist'];
$tooltip['artikel']['create']['#produktion']="Markierung des Artikels um ihn innerhalb eines Auftrags in eine Produktion zu überführen";
$tooltip['artikel']['edit']['#produktion']="Markierung des Artikels um ihn innerhalb eines Auftrags in eine Produktion zu überführen";
$tooltip['artikel']['create']['#externeproduktion']="Markierung des Artikels als Beistellung für die Produktion";
$tooltip['artikel']['edit']['#externeproduktion']="Markierung des Artikels als Beistellung für die Produktion";
$tooltip['artikel']['create']['#rohstoffe']="Sonderfunktion => Rohstoffliste";
$tooltip['artikel']['edit']['#rohstoffe']="Sonderfunktion => Rohstoffliste";
$tooltip['artikel']['create']['#geraet']="Markierung in der Kundenadresse zur Unterscheidung der Artikel";
$tooltip['artikel']['edit']['#geraet']="Markierung in der Kundenadresse zur Unterscheidung der Artikel";
$tooltip['artikel']['create']['#serviceartikel']="Markierung in der Kundenadresse zur Unterscheidung der Artikel";
$tooltip['artikel']['edit']['#serviceartikel']="Markierung in der Kundenadresse zur Unterscheidung der Artikel";
$tooltip['artikel']['create']['#gebuehr']="Markierung in der Kundenadresse zur Unterscheidung der Artikel";
$tooltip['artikel']['edit']['#gebuehr']="Markierung in der Kundenadresse zur Unterscheidung der Artikel";
$tooltip['artikel']['create']['#dienstleistung']="Dient als Markierung für Dienstleistungen. Wird aktuell nur von externen Apps ausgewertet";
$tooltip['artikel']['edit']['#dienstleistung']="Dient als Markierung für Dienstleistungen. Wird aktuell nur von externen Apps ausgewertet";

$tooltip['artikel']['create']['#intern_gesperrt']="Dieser Artikel ist gesperrt (für Angebote, Aufträge, etc.; wird durchgestrichen in der Artikelübersicht angezeigt)";
$tooltip['artikel']['edit']['#intern_gesperrt']="Dieser Artikel ist gesperrt (für Angebote, Aufträge, etc.; wird durchgestrichen in der Artikelübersicht angezeigt)";
$tooltip['artikel']['create']['#intern_gesperrtgrund']="Kommentar für Sperre (wird angezeigt, sobald in einen gesperrten Artikel geklickt wird)";
$tooltip['artikel']['edit']['#intern_gesperrtgrund']="Kommentar für Sperre (wird angezeigt, sobald in einen gesperrten Artikel geklickt wird)";
$tooltip['artikel']['create']['#freigabenotwendig']="Löst Prüfung im Auftrag aus (z.B. Artikel der nur an Fachleute/B2B verkauft werden darf)";
$tooltip['artikel']['edit']['#freigabenotwendig']="Löst Prüfung im Auftrag aus (z.B. Artikel der nur an Fachleute/B2B verkauft werden darf)";
$tooltip['artikel']['create']['#freigaberegel']="Textfeld für das programmierte Modul (Regel)";
$tooltip['artikel']['edit']['#freigaberegel']="Textfeld für das programmierte Modul (Regel)";


/* Details - Texte und Beschreibungen */

$tooltip['artikel']['create']['#name_en']="Textuelle Bezeichnung des Artikels (Beschränkung auf 255 Zeichen), Sprache englisch";
$tooltip['artikel']['edit']['#name_en']="Textuelle Bezeichnung des Artikels (Beschränkung auf 255 Zeichen), Sprache englisch";
$tooltip['artikel']['create']['#kurztext_en']="Für Volltext-Suche (Stammdaten→ Artikel) oder Online-Shops Gruppentext (z.B. bei Shopware)";
$tooltip['artikel']['edit']['#kurztext_en']="Für Volltext-Suche (Stammdaten→ Artikel) oder Online-Shops Gruppentext (z.B. bei Shopware)";
$tooltip['artikel']['create']['#anabregs_text_en']="Beschreibungstext zum Artikel für Angebote, Aufträge, etc.";
$tooltip['artikel']['edit']['#anabregs_text_en']="Beschreibungstext zum Artikel für Angebote, Aufträge, etc.";

$tooltip['artikel']['create']['#uebersicht_de']="Textuelle Beschreibung für den Online-Shop (z.B. Shopware)";
$tooltip['artikel']['edit']['#uebersicht_de']="Textuelle Beschreibung für den Online-Shop (z.B. Shopware)";
$tooltip['artikel']['create']['#metadescription_de']="Meta-Description";
$tooltip['artikel']['edit']['#metadescription_de']="Meta-Description";
$tooltip['artikel']['create']['#metakeywords_de']="Meta-Keywords";
$tooltip['artikel']['edit']['#metakeywords_de']="Meta-Keywords";
$tooltip['artikel']['create']['#uebersicht_en']="Textuelle Beschreibung für den Online-Shop (z.B. Shopware)";
$tooltip['artikel']['edit']['#uebersicht_en']="Textuelle Beschreibung für den Online-Shop (z.B. Shopware)";
$tooltip['artikel']['create']['#metadescription_en']="Meta-Description";
$tooltip['artikel']['edit']['#metadescription_en']="Meta-Description";
$tooltip['artikel']['create']['#metakeywords_en']="Meta-Keywords";
$tooltip['artikel']['edit']['#metakeywords_en']="Meta-Keywords";

for($i = 1; $i <= 40; $i++)
{
  $tooltip['artikel']['create']['#freifeld'.$i]="Individuell beschriftbares Freifeld";
  $tooltip['artikel']['edit']['#freifeld'.$i]="Individuell beschriftbares Freifeld";
}

$tooltip['artikel']['create']['#shop']="Verknüpfung auf den Online Shop (im Standard pro Artikel eine Verknüpfung auf einen Shop)";
$tooltip['artikel']['edit']['#shop']="Verknüpfung auf den Online Shop (im Standard pro Artikel eine Verknüpfung auf einen Shop)";
$tooltip['artikel']['create']['#shop2']="Weitere Verknüpfung auf den Online Shop (Sonderfall)";
$tooltip['artikel']['edit']['#shop2']="Weitere Verknüpfung auf den Online Shop (Sonderfall)";
$tooltip['artikel']['create']['#shop3']="Weitere Verknüpfung auf den Online Shop (Sonderfall)";
$tooltip['artikel']['edit']['#shop3']="Weitere Verknüpfung auf den Online Shop (Sonderfall)";
$tooltip['artikel']['create']['#ausverkauft']="Der Artikel wird als ausverkauft markiert.<br><br>Bei einer Änderung dieser Option, muss der Artikel danach manuell nochmal exportiert werden, damit die Änderungen im Shop greifen.";
$tooltip['artikel']['edit']['#ausverkauft']="Der Artikel wird als ausverkauft markiert.<br><br>Bei einer Änderung dieser Option, muss der Artikel danach manuell nochmal exportiert werden, damit die Änderungen im Shop greifen.";
$tooltip['artikel']['create']['#ausverkauft']="Artikel sind im Shop nicht mehr sichtbar (inaktiv).<br><br>Bei einer Änderung dieser Option, muss der Artikel danach manuell nochmal exportiert werden, damit die Änderungen im Shop greifen.";
$tooltip['artikel']['edit']['#ausverkauft']="Artikel sind im Shop nicht mehr sichtbar (inaktiv).<br><br>Bei einer Änderung dieser Option, muss der Artikel danach manuell nochmal exportiert werden, damit die Änderungen im Shop greifen.";
$tooltip['artikel']['create']['#inaktiv']="Artikel sind im Shop nicht mehr sichtbar (inaktiv).<br><br>Bei einer Änderung dieser Option, muss der Artikel danach manuell nochmal exportiert werden, damit die Änderungen im Shop greifen.";
$tooltip['artikel']['edit']['#inaktiv']="Artikel sind im Shop nicht mehr sichtbar (inaktiv).<br><br>Bei einer Änderung dieser Option, muss der Artikel danach manuell nochmal exportiert werden, damit die Änderungen im Shop greifen.";

$tooltip['artikel']['create']['#autolagerlampe']="Automatische Übertragung zu Shop (vorausgesetzt Funktion ist in der Shopschnittstelle (Einstellungen) aktiviert und der entsprechende Prozessstarter zur Lagerzahlenübertragung läuft)";
$tooltip['artikel']['edit']['#autolagerlampe']="Automatische Übertragung zu Shop (vorausgesetzt Funktion ist in der Shopschnittstelle (Einstellungen) aktiviert und der entsprechende Prozessstarter zur Lagerzahlenübertragung läuft)";
$tooltip['artikel']['create']['#restmenge']="Artikel wird nach Abverkauf als nicht mehr verfügbar umgestellt";
$tooltip['artikel']['edit']['#restmenge']="Artikel wird nach Abverkauf als nicht mehr verfügbar umgestellt";
$tooltip['artikel']['create']['#pseudolager']="Pseudo Lagerzahl für Shop (z.B. Shopware).<br>Der Haken 'Lagerzahlen Sync.' muss gesetzt sein. Dieser Wert wird an den Shop übertragen und nicht der 'echte' Lagerbestand.";
$tooltip['artikel']['edit']['#pseudolager']="Pseudo Lagerzahl für Shop (z.B. Shopware).<br>Der Haken 'Lagerzahlen Sync.' muss gesetzt sein. Dieser Wert wird an den Shop übertragen und nicht der 'echte' Lagerbestand.";
$tooltip['artikel']['create']['#lieferzeitmanuell']="Angabe der Lieferzeit";
$tooltip['artikel']['edit']['#lieferzeitmanuell']="Angabe der Lieferzeit";
$tooltip['artikel']['create']['#lagerkorrekturwert']="Der Lagerbestand im Shop wird um den eingetragenen Wert nach oben oder unten angepasst.<br><br>Zum Beispiel wird durch den Eintrag '-5' der Bestand im Shop um 5 vermindert angezeigt;<br> ein Eintrag von '5' heißt, dass auf den Lagerbestand 5 Stück aufaddiert werden.<br><br>Im Standard ist hier '0' eingetragen, was bedeutet, dass nichts addiert oder abgezogen wird.";
$tooltip['artikel']['edit']['#lagerkorrekturwert']=$tooltip['artikel']['create']['#lagerkorrekturwert'];

$tooltip['artikel']['create']['#partnerprogramm_sperre']="Sonderfunktion für Provisionsmodul";
$tooltip['artikel']['edit']['#partnerprogramm_sperre']="Sonderfunktion für Provisionsmodul";
$tooltip['artikel']['create']['#neu']="Fähnchen „NEU“ (Shopware)";
$tooltip['artikel']['edit']['#neu']="Fähnchen „NEU“ (Shopware)";
$tooltip['artikel']['create']['#topseller']="Fähnchen„TopSeller“ (Shopware)";
$tooltip['artikel']['edit']['#topseller']="Fähnchen„TopSeller“ (Shopware)";
$tooltip['artikel']['create']['#startseite']="Sonderoption => Startseite";
$tooltip['artikel']['edit']['#startseite']="Sonderoption => Startseite";
$tooltip['artikel']['create']['#downloadartikel']="Sonderoption => Downloadartikel";
$tooltip['artikel']['edit']['#downloadartikel']="Sonderoption => Downloadartikel";
$tooltip['artikel']['create']['#pseudopreis']="Pseudoverkaufspreis brutto (Shop)";
$tooltip['artikel']['edit']['#pseudopreis']="Pseudoverkaufspreis brutto (Shop)";
$tooltip['artikel']['create']['#generierenummerbeioption']="Shop generiert eigene Artikelnummer für z.B. Individualartikel";
$tooltip['artikel']['edit']['#generierenummerbeioption']="Shop generiert eigene Artikelnummer für z.B. Individualartikel";
$tooltip['artikel']['create']['#altersfreigabe']="Altersfreigabe ab 16, ab 18";
$tooltip['artikel']['edit']['#altersfreigabe']="Altersfreigabe ab 16, ab 18";

$tooltip['artikel']['create']['#autoabgleicherlaubt']="Preis und Artikelname bei Auftragsimport von Fremdshop verwenden und nicht aus Xentral verwenden z.B. bei Gutschein, Porto etc.";
$tooltip['artikel']['edit']['#autoabgleicherlaubt']="Preis und Artikelname bei Auftragsimport von Fremdshop verwenden und nicht aus Xentral verwenden z.B. bei Gutschein, Porto etc.";

$tooltip['artikel']['create']['#steuer_art_produkt']="Physisches Produkt, Digitaler Inhalt, Dienstleistung";
$tooltip['artikel']['edit']['#steuer_art_produkt']="Physisches Produkt, Digitaler Inhalt, Dienstleistung";
$tooltip['artikel']['create']['#steuer_art_produkt_download']="Download, eBook";
$tooltip['artikel']['edit']['#steuer_art_produkt_download']="Download, eBook";

$tooltip['artikel']['create']['#steuer_erloese_inland_normal']="Erlöse Inland standard Steuersatz (19% für DE)";
$tooltip['artikel']['edit']['#steuer_erloese_inland_normal']="Erlöse Inland standard Steuersatz (19% für DE)";
$tooltip['artikel']['create']['#steuer_erloese_inland_ermaessigt']="Erlöse Inland ermaessigter Steuersatz (7% für DE)";
$tooltip['artikel']['edit']['#steuer_erloese_inland_ermaessigt']="Erlöse Inland ermaessigter Steuersatz (7% für DE)";
$tooltip['artikel']['create']['#steuer_erloese_inland_nichtsteuerbar']="Erlöse Inland steuerfrei (0% für DE)";
$tooltip['artikel']['edit']['#steuer_erloese_inland_nichtsteuerbar']="Erlöse Inland steuerfrei (0% für DE)";
$tooltip['artikel']['create']['#steuer_erloese_inland_innergemeinschaftlich']="Erlöse EG Unternehmer (0% für EU) ";
$tooltip['artikel']['edit']['#steuer_erloese_inland_innergemeinschaftlich']="Erlöse EG Unternehmer (0% für EU) ";
$tooltip['artikel']['create']['#steuer_erloese_inland_eunormal']="Erlöse EU Privatperson standard";
$tooltip['artikel']['edit']['#steuer_erloese_inland_eunormal']="Erlöse EU Privatperson standard";
$tooltip['artikel']['create']['#steuer_erloese_inland_euermaessigt']="Erlöse EU Privatperson ermaessigt";
$tooltip['artikel']['edit']['#steuer_erloese_inland_euermaessigt']="Erlöse EU Privatperson ermaessigt";
$tooltip['artikel']['create']['#steuer_erloese_inland_export']="Erlöse Drittland/ Export (0% für Drittland)";
$tooltip['artikel']['edit']['#steuer_erloese_inland_export']="Erlöse Drittland/ Export (0% für Drittland)";

$tooltip['artikel']['create']['#steuer_aufwendung_inland_normal']="Aufwendungen Inland standard Steuersatz (19% für DE)";
$tooltip['artikel']['edit']['#steuer_aufwendung_inland_normal']="Aufwendungen Inland standard Steuersatz (19% für DE)";
$tooltip['artikel']['create']['#steuer_aufwendung_inland_ermaessigt']="Aufwendungen Inland ermaessigter Steuersatz (7% für DE)";
$tooltip['artikel']['edit']['#steuer_aufwendung_inland_ermaessigt']="Aufwendungen Inland ermaessigter Steuersatz (7% für DE)";
$tooltip['artikel']['create']['#steuer_aufwendung_inland_nichtsteuerbar']="Aufwendungen Inland steuerfrei (0% für DE)";
$tooltip['artikel']['edit']['#steuer_aufwendung_inland_nichtsteuerbar']="Aufwendungen Inland steuerfrei (0% für DE)";
$tooltip['artikel']['create']['#steuer_aufwendung_inland_innergemeinschaftlich']="Aufwendungen EG Unternehmer (0% für EU) ";
$tooltip['artikel']['edit']['#steuer_aufwendung_inland_innergemeinschaftlich']="Aufwendungen EG Unternehmer (0% für EU) ";
$tooltip['artikel']['create']['#steuer_aufwendung_inland_eunormal']="Aufwendungen EU Privatperson standard";
$tooltip['artikel']['edit']['#steuer_aufwendung_inland_eunormal']="Aufwendungen EU Privatperson standard";
$tooltip['artikel']['create']['#steuer_aufwendung_inland_euermaessigt']="Aufwendungen EU Privatperson ermaessigt";
$tooltip['artikel']['edit']['#steuer_aufwendung_inland_euermaessigt']="Aufwendungen EU Privatperson ermaessigt";
$tooltip['artikel']['create']['#steuer_aufwendung_inland_import']="Aufwendungen Drittland/ Import (0% für Drittland)";
$tooltip['artikel']['edit']['#steuer_aufwendung_inland_import']="Aufwendungen Drittland/ Import (0% für Drittland)";

/* Einkauf */
$tooltip['artikel']['einkauf']['#vpe']="Wird auch auf Bestelldokumenten angezeigt und die Bestellmenge in die VPE-Menge umgerechnet.";
$tooltip['artikel']['einkaufeditpopup']['#vpe']="Wird auch auf Bestelldokumenten angezeigt und die Bestellmenge in die VPE-Menge umgerechnet.";
$tooltip['artikel']['einkauf']['#nichtberechnet']="Falls man in dem Modul Währungsumrechnung eine automat. Umrechnung laufen hat, wird dieser Preis nicht verwendet.";
$tooltip['artikel']['einkaufeditpopup']['#nichtberechnet']="Falls man in dem Modul Währungsumrechnung eine automat. Umrechnung laufen hat, wird dieser Preis nicht verwendet.";

/* Verkauf */
$tooltip['artikel']['verkauf']['#preis']="Preisangabe in NETTO";
$tooltip['artikel']['verkauf']['#inbelegausblenden']="Verkaufspreis wird in der Auflistung der Staffelpreise im Angebot nicht mit angezeigt.";



/* ARTIKELKALKULATION */

$tooltip['artikelkalkulation']['list']['#f_abschliessen']="Sobald eine Bestellung im Aktionsmenü auf „abgeschlossen“ gestellt wird, werden die EKs der Bestell-Positionen im jeweiligen Artikel in der Kalkulation vermerkt.";
$tooltip['artikelkalkulation']['list']['#f_fifo']="Mit dieser Option wird geschaut, wie viel Ware dieses Artikels im Lager ist und dabei nur die letzten Bestellmengen berücksichtigt bis die Bestellmengen addiert den Lagerbestand ergeben.<br><br>Z.B. 150 auf Lager<br>Bestellung 1 01.08.2018 100<br>Bestellung 2 02.08.2018 200<br>Bestellung 3 03.08.2018 100<br><br>Mit der Option werden die Preise von Bestellung 3 mit Gewichtung 2/3 + Preise von Bestellung 2 Mit Gewichtung 1/3 gerechnet.<br>Ohne diese Option würde auch Bestellung 1 mit gewertet und die Gewichtungern 1/4, 1/2 und 1/4 lauten. ";
$tooltip['artikelkalkulation']['list']['#e_automatischneuberechnen']="Kostenposition soll bei Berechnung des EK mit ber&uuml;cksichtigt werden";



/* AUFTRAG */

/* Details */
$tooltip['auftrag']['edit']['#lieferdatumkw']="Wunschliefertermin des Kunden. Haken 'KW' zeigt nur die KW an, in der der Wunschliefertermin liegt. Hier auf dem Breifpapier die Variablen einfügen, um die Daten anzeigen zu lassen.";
$tooltip['auftrag']['edit']['reservationdate'] = 'Datum, an dem die Lagerartikel im Auftrag für den Kunden reserviert werden sollen.';
$tooltip['auftrag']['create']['reservationdate'] = $tooltip['auftrag']['edit']['reservationdate'];
$tooltip['auftrag']['edit']['#vorabbezahltmarkieren']="Wenn der Haken gesetzt ist, wird das Ampelsymbol € in der Auftragsampel auf grün gestellt (wichtig für Autoversand).";
$tooltip['auftrag']['edit']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";
$tooltip['auftrag']['edit']['#fastlane']="Dient als Markierung für Aufträge mit Prio.<br>Diese Aufträge erhalten dann in den Übersichten ein '(FL)' hinter der Auftragsnummern und können in der Auftrags-Übersicht, im Auftrags-Versandzentrum und im Lager-Versandzentrum gefiltert werden.";
$tooltip['auftrag']['edit']['#ust_ok']="Automatisch gesetzt bei Besteuerung Inland";
$tooltip['auftrag']['create']['#kurs']="Der aktuelle Wechselkurs kann in der App 'Währung Umrechnung' von der European Central Bank abgerufen werden.";
$tooltip['auftrag']['edit']['#kurs']=$tooltip['auftrag']['create']['#kurs'];

/* Positionen */
/* funktioniert irgendwie nicht im Positionen Popup... */
$tooltip['auftrag']['edit']['#keinrabatterlaubt']="Haken setzen um den Rabatt nach dem erneuten Speichern des Auftrags zu erhalten.<br>Auch bei Preisgruppen muss der Haken gesetzt werden, damit der Rabatt übernommen wird.";


/* AUFTRAGSCOCKPIT */

$tooltip['auftragscockpit']['list']['#legendglobalesuche'] = "Sucht beliebig innerhalb Name, Ansprechpartner, Abteilung, Unterabteilung, Adresszusatz, Strasse, Ort
und PLZ, Kundennummer, Lieferantennummer, Mitarbeiternummer beginnend ";
$tooltip['auftragscockpit']['list']['#legendrufnummernsuche'] = "Suche nach Telefon, Telefax, Mobil Kontakten";
$tooltip['auftragscockpit']['list']['#keinpassendereintrag'] = "Hinweis bitte nur Kunden anlegen falls:";


/* BATCHES */

/* Pickliste */
$tooltip['batches']['edit']['#name'] = 'Wird für interne Zwecke verwendet.';
$tooltip['batches']['edit']['#description'] = 'Beschreibung der Zielsetzung für interne Zwecke.';
/* Benachrichtigung */
$tooltip['batches']['edit']['#notification'] = 'Information zur erstellten Pickliste wird nach der Erstellung per E-Mail mit dem hinterlegten Betreff und Text an den an den angegebenen Empfänger geschickt.';
$tooltip['batches']['edit']['#subject'] = 'Betreffzeile der E-Mail.';
$tooltip['batches']['edit']['#body'] = 'Beschreibung der Zielsetzung für interne Zwecke.';
$tooltip['batches']['edit']['#attachment'] = 'Pickliste wird der E-Mail als PDF-Datei angehängt.';
/* Eigenschaften Pickliste */
$tooltip['batches']['edit']['#order_count_to'] = 'Definiert die minimale und maximale Anzahl von Aufträgen in einer Pickliste, 0 oder Leer bedeutet "unlimitiert". Sind mehr als die maximale Zahl an Aufträgen versandbereit werden entsprechend mehrere Picklisten erstellt.';
$tooltip['batches']['edit']['#max_picklist_count_help'] = 'Maximale Anzahl an gleichzeitg (in einem Durchlauf) erstellten Picklisten.';
$tooltip['batches']['edit']['#article_count_batch_to'] = 'Definiert die minimale und maximale Stückzahl in einer Pickliste, 0 oder Leer bedeutet "unlimitiert". Sind mehr als die maximale Stückzahl in Aufträgen versandbereit, werden entsprechend mehrere Picklisten erstellt.';
/* Ausführung Picklistenerstellung */
$tooltip['batches']['edit']['#autosort'] = 'Bei gleichzeitiger Ausführung mehrerer Regelwerke über einen Prozessstartervorgang werden die Regelwerke nach dieser Reihenfolge ausgeführt.';
/* Auswahl Aufträge */
$tooltip['batches']['edit']['#positions_to'] = 'Verwendet für die Pickliste nur Aufträge, deren Zahl an Positionen zwischen den angegebenen Werten liegt. 0 oder Leer bedeutet "unlimitiert".';
$tooltip['batches']['edit']['#weight_to'] = 'Verwendet für die Pickliste nur Aufträge, deren Gesamtgewicht aller Artikel zwischen den angegebenen Werten liegt. 0 oder Leer bedeutet "unlimitiert". Nicht gepflegte Artikelgewichte werden nicht berücksichtigt.';
$tooltip['batches']['edit']['#volume_to'] = 'Verwendet für die Pickliste nur Aufträge, deren Gesamtvolumen aller Artikel zwischen den angegebenen Werten liegt. 0 oder Leer bedeutet "unlimitiert". Nicht gepflegte Artikelvolumina werden nicht berücksichtigt. Das Volumen wird lediglich als Summe berechnet und kann daher nur ein Anhaltspunkt für den tatsächlichen Platzbedarf sein.';
/* Einschränkung Pro Artikel */
$tooltip['batches']['edit']['#article_weight_to'] = 'Schränkt die Pickliste auf Artikel ein, deren Artikelgewicht (Gewicht pro Artikel) zwischen den angegebenen Werten liegt. "0" oder Leer bedeutet: keine Limitierung.';
$tooltip['batches']['edit']['#article_volume_to'] = 'Schränkt die Pickliste auf Artikel ein, deren Artikelvolumen (Volumen pro Artikel) zwischen den angegebenen Werten liegt. "0" oder Leer bedeutet: keine Limitierung.';
$tooltip['batches']['edit']['#article_count_to'] = 'Schränkt die Pickliste auf Artikel ein, deren Stückzahl (Menge jeder Artikelposition) zwischen den angegebenen Werten liegt. "0" oder Leer bedeutet: keine Limitierung.';
/* Filter Aufträge */
$tooltip['batches']['edit']['#editFilter_project'] = 'Wählt nur Aufträge aus den angegebenen Projekten.';
$tooltip['batches']['edit']['#editFilter_article'] = 'Wählt nur Aufträge in denen einer dieser Artikel enthalten ist.';
$tooltip['batches']['edit']['#editFilter_attribute'] = 'Wählt nur Aufträge mit Artikeln mit einem dieser Eigenschaften.';
$tooltip['batches']['edit']['#editFilter_freefield'] = 'Wählt nur Aufträge mit Artikeln mit den angegebenen Freifeldwerten.';
$tooltip['batches']['edit']['#editFilter_shop'] = 'Wählt nur Aufträge, die aus den angegebenen Shops importiert wurden.';
$tooltip['batches']['edit']['#editFilter_deliverycountry'] = 'Wählt nur Aufträge, die in die angegebenen Lieferländer verschickt werden.';
$tooltip['batches']['edit']['#editFilter_articlecategory'] = 'Wählt nur Aufträge in denen ein Artikel dieser Kategorie enthalten ist.';
$tooltip['batches']['edit']['#editFilter_shipping'] = 'Wählt nur Aufträge, die die angegebenen Versandarten verwenden.';
$tooltip['batches']['edit']['#editFilter_payment'] = 'Wählt nur Aufträge, die über die angegebenen Zahlungsarten beglichen werden / wurden.';
$tooltip['batches']['edit']['#editFilter_group'] = 'Wählt nur Aufträge, die an Kunden aus den angegebenen Gruppen verschickt werden.';
$tooltip['batches']['edit']['#editFilter_storagelocation'] = 'Wählt nur Aufträge, die ausschließlich Artikel an den angegebenen Lagerplätzen beinhalten.';
/* Versandeinstellungen */
$tooltip['batches']['edit']['#project'] = 'Wählt nur Aufträge aus dem angegebenen Projekt. Die Logistik- & Versandeinstellungen werden aus diesem Projekt gezogen. Dazu gehört auch der zu verwendende Drucker.';


/* BENUTZER */

$tooltip['benutzer']['create']['copyusertemplate']="Bitte erst Benutzer speichern, um Benutzerrechte auswählen zu können.";
$tooltip['benutzer']['create']['#projekt']='Beim Anlegen von Adressen und Belegen wird das Projekt-Feld vorausgefüllt mit dem hier angegebenem Projekt.<br>Dies macht z.B. Sinn wenn Sie einen Mitarbeiter als Packer im Logistikprozess haben, der nur ein spezielles Projekt bearbeiten soll.<br>Wird hier kein Projekt eingestellt, so wird beim Anlegen von Adressen, Belegen etc. durch den Benutzer immer das unter <a href="?module=firmendaten&action=edit#tabs-9" target="_blank">Administration => Einstellungen => Grundeinstellungen => System eingestellte Standard-Projekt</a> vorausgefüllt.';
$tooltip['benutzer']['edit']['#projekt'] = $tooltip['benutzer']['create']['#projekt'];
$tooltip['benutzer']['create']['standardversanddrucker']="Wenn der Mitarbeiter z.B. einen eigenen Drucker für den Packtisch im Logistikprozess hat.";
$tooltip['benutzer']['edit']['standardversanddrucker']="Wenn der Mitarbeiter z.B. einen eigenen Drucker für den Packtisch im Logistikprozess hat.";

/* BESTELLUNG */

/* Details */
$tooltip['bestellung']['create']['bestellbestaetigung']="Fügt einen Text unterhalb der Artikeltabelle auf dem Bestellungs-Beleg ein, der den Lieferanten auffordert die Bestellung zu bestätigen. Ansonsten verfällt diese.<br><br>Einen eigenen Text dafür kann man über das Übersetzungsmodul (Administration => Einstellungen => Übersetzungen) mit der Variable <strong>dokument_bestellung_bestaetigung</strong> erreichen.";
$tooltip['bestellung']['edit']['bestellbestaetigung']="Fügt einen Text unterhalb der Artikeltabelle auf dem Bestellungs-Beleg ein, der den Lieferanten auffordert die Bestellung zu bestätigen. Ansonsten verfällt diese.<br><br>Einen eigenen Text dafür kann man über das Übersetzungsmodul (Administration => Einstellungen => Übersetzungen) mit der Variable <strong>dokument_bestellung_bestaetigung</strong> erreichen.";
$tooltip['bestellung']['create']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";
$tooltip['bestellung']['edit']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";

/* BESTELLVORSCHLAGAPP */

/* Einstellungen */
$tooltip['bestellvorschlagapp']['einstellungen']['#link']="Artikel-ID mit %value% angeben z.B. index.php?module=artikel&amp;action=lager&amp;id=%value%";


/* BUCHHALTUNGSEXPORT */

/* Export */
$tooltip['buchhaltungexport']['list']['#datevverbindlichkeitrechnungsdatum']="Ist der Haken gesetzt, werden die Lieferantenrechnungen mit dem Rechnungsdatum als Belegdatum exportiert.<br>Ist der Haken nicht gesetzt, werden die Lieferantenrechnung mit dem Eingangsdatum als Belegdatum exportiert.";
$tooltip['buchhaltungexport']['list']['#datev_append_internet'] = "Nur Element 'consolidatedOrderId' aus Datev XML v5 hinzuf&uuml;gen";

/* Einstellungen */
$tooltip['buchhaltungexport']['einstellungen_list']['#e_zahlungsweise']="Kann leer gelassen werden und wird nur in Spezialfällen gebraucht.<br><br>Hier kann ein bestimmtes Freifeld aus der Adresse in Xentral angegeben werden in dem die Zahlungsweise für DATEV übergeben wird<br> Diese Zahlungsweise wird als Nummer 1 bis 9 hinterlegt - die Nummer finden Sie in DATEV und stellt den aktuellen Index der Zahlungsweise dar.";
$tooltip['buchhaltungexport']['einstellungen_list']['#e_wirtschaftsjahr']="Format: ttmm - Beispiel: 0104 für den 1. April.";

/* CALDAV */

$tooltip['caldav']['config']['baseuri']='Falls sie Mappings oder Symlinks benutzen, bitte hier alles nach der domain eintragen (z.b. "/19.3/xentral/www/caldav/")';

/* DATAPROTECT */
$tooltip['dataprotect']['list']['#dateofdeletion'] = "Persönliche Informationen, sogenannte PII werden 30 Tage nach Versand des Auftrags oder 30 Tage nach Rechnungsfreigabe (was immer später kommt) anonymisiert. Archivierte Belege werden nicht verändert.";
$tooltip['dataprotect']['list']['#deletePersonalInformation'] = "Sofortige Anonymisierung / Löschung aller noch aktiven, persönlichen Informationen von Kundendaten aus ausgewählten Quellen / Projekten. <br />Achtung! Dieser Prozess kann nicht rückgängig gemacht werden!";
$tooltip['dataprotect']['list']['#exportPersonalInformation'] = "Exportiert alle noch aktiven, persönlichen Informationen von Kundendaten aus ausgewählten Quellen / Projekten zur Abgabe nach Aufforderung.";
$tooltip['dataprotect']['settings']['#free_text_field'] = "z.B. f&uuml;r Geschenknachricht";
/* DROPSHIPPING */

/* Gruppen */

$tooltip['dropshipping']['gruppe']['#editbezeichnung']="Frei wählbare Bezeichnung der Gruppe";
$tooltip['dropshipping']['gruppe']['#editadresse']="Der Lieferant auf den eine Bestellung angelegt, bzw Mails verschickt werden sollen";
$tooltip['dropshipping']['gruppe']['#editprojekt']="Projekt auf das der Dropshipping-Teilauftrag gemappt wird";
$tooltip['dropshipping']['gruppe']['#editautoversand']="Führt den Autoversand für den Teilauftrag durch, wenn alle Ampeln des Teilauftrags auf grün stehen.<br>Hinweis: Wenn die Option für die Bestellung 'Als Anhang Lieferschein / Rechnung' gesetzt ist, wird, auch wenn der Haken hier nicht gesetzt ist, der Autoversand ausgeführt.";
$tooltip['dropshipping']['gruppe']['#editzahlungok']="Ein Dropshipping-Teilauftrag wird nur erstellt wenn die Zahlungs-OK Ampel (Zahlung freigegeben) im Orginalauftrag auf grün steht";

$tooltip['dropshipping']['gruppe']['#editbestellunganlegen']="Eine Bestellung für die Artikel aus dem Dropshipping Teilauftrag wird automatisch auf den hinterlegten Lieferanten angelegt, wenn ein Einkaufspreis zu den Positionen hinterlegt ist";
$tooltip['dropshipping']['gruppe']['#editabweichendelieferadresse']="Die Bestellung erhält als abweichende Lieferadresse die Kundenadresse des Hauptauftrags";
$tooltip['dropshipping']['gruppe']['#editlieferscheinanhaengen']="Die Bestellung erhält als Anhangs-Datei den Lieferschein des Dropshipping-Teilauftrags";
$tooltip['dropshipping']['gruppe']['#editrechnunganhaengen']="Die Bestellung erhält als Anhangs-Datei die Rechnung des Auftrags";
$tooltip['dropshipping']['gruppe']['#editauftraganhaengen']="Die Bestellung erhält als Anhangs-Datei die Auftragsbestätigung des Dropshipping-Teilauftrags";

$tooltip['dropshipping']['gruppe']['#editbestellungmail']="Eine E-Mail wird an den Lieferanten geschickt mit allen Anhängen";
$tooltip['dropshipping']['gruppe']['#editbestellungdrucken']="Falls die Bestellung gedruckt werden soll kann hier ein Dokumentendrucker ausgewählt werden";
$tooltip['dropshipping']['gruppe']['#editlieferscheinmail']="Eine E-Mail wird an den Lieferanten geschickt mit dem Lieferschein des Dropshipping-Teilauftrags";
$tooltip['dropshipping']['gruppe']['#editlieferscheindrucken']="Falls der Lieferschein des Dropshipping-Teilauftrags gedruckt werden soll kann hier ein Dokumentendrucker ausgewählt werden";
$tooltip['dropshipping']['gruppe']['#editrechnungmail']="Eine E-Mail wird an den Kunden geschickt mit der Rechnung";
$tooltip['dropshipping']['gruppe']['#editrechnungdrucken']="Falls die Rechnung gedruckt werden soll kann hier ein Dokumentendrucker ausgewählt werden";
$tooltip['dropshipping']['gruppe']['#editrueckmeldungshop']="Wird der Auftrag aus einem Online-Shop nach Xentral übertragen und ein Dropshipping-Auftrag daraus erstellt, wird im Shop der Bestellstatus auf versendet gestellt";


/* DROPSHIPPINGLAGER */

$tooltip['dropshippinglager']['list']['#editprojekt_filter']="Es werden nur Aufträge mit diesem Projekt für das Dropshipping berücksichtigt.";
$tooltip['dropshippinglager']['dropshippinglager']['#editprojekt']="Durch das Dropshipping abgetrennte Teilaufträge werden auf dieses Projekt gebucht.";


/* DRUCKER */

$tooltip['drucker']['create']['#format']="Sollten Sie einen Etikettendrucker oder Bondrucker mit der Adapterbox ansteuern bleibt dieses Feld leer.
Die Dimensionen des Etikettes oder Papiers werden unter Administration - > Einstellungen -> Etiketten definiert.
Link: index.php?module=etiketten&action=list";
$tooltip['drucker']['edit']['#format']="Sollten Sie einen Etikettendrucker oder Bondrucker mit der Adapterbox ansteuern bleibt dieses Feld leer.
Die Dimensionen des Etikettes oder Papiers werden unter Administration - > Einstellungen -> Etiketten definiert.
Link: index.php?module=etiketten&action=list";

/* EMAILBACKUP */

$tooltip['emailbackup']['create']['imap_port']="IMAP = 143, IMAP mit SSL = 993";
$tooltip['emailbackup']['edit']['imap_port']=$tooltip['emailbackup']['create']['imap_port'];

$tooltip['emailbackup']['create']['smtp_port']="Standardport: 25 - Bei Verschlüsselung: 465";
$tooltip['emailbackup']['edit']['smtp_port']=$tooltip['emailbackup']['create']['smtp_port'];

$tooltip['emailbackup']['create']['angezeigtername']="Name wird als Absender des Accounts angezeigt, z.B. \"Hans Meier\".";
$tooltip['emailbackup']['edit']['angezeigtername']=$tooltip['emailbackup']['create']['angezeigtername'];

$tooltip['emailbackup']['create']['email']="Absendeadresse. Muss mit der Postfachadresse übereinstimmenm z.B. hans.meier@example.com.";
$tooltip['emailbackup']['edit']['email']=$tooltip['emailbackup']['create']['email'];

$tooltip['emailbackup']['create']['benutzername']="Anmeldename für das Postfach. Üblicherweise die E-Mail-Adresse oder ein Accountname (z.B. p123456).";
$tooltip['emailbackup']['edit']['benutzername']=$tooltip['emailbackup']['create']['benutzername'];

$tooltip['emailbackup']['create']['internebeschreibung']="Interne Beschreibung des Postfaches (optional). Z.B. \"Persönlicher Account Hans Meier\" oder \"Sammelaccount Sales\".";
$tooltip['emailbackup']['edit']['internebeschreibung']=$tooltip['emailbackup']['create']['internebeschreibung'];


/* ETIKETTEN */

$tooltip['etiketten']['edit']['#xml']="Alle Variablen finden Sie im <a href='#' onClick='window.open(\"http://helpdesk.wawision.de/doku.php?id=wawision:etikettenlayout_erstellen#layout_variablen\", \"_blank\")'>Helpdesk-Artikel</a>";
$tooltip['etiketten']['create']['#xml']="Alle Variablen finden Sie im <a href='#' onClick='window.open(\"http://helpdesk.wawision.de/doku.php?id=wawision:etikettenlayout_erstellen#layout_variablen\", \"_blank\")'>Helpdesk-Artikel</a>";


/* FIRMENDATEN */

// Firmenanschrift
$tooltip['firmendaten']['edit']['name']="Name der eigenen Firma.";
$tooltip['firmendaten']['edit']['strasse']="Stra&szlig;e der eigenen Firma.";
$tooltip['firmendaten']['edit']['plz']="Postleitzahl der eigenen Firma.";
$tooltip['firmendaten']['edit']['ort']="Ort der eigenen Firma.";
$tooltip['firmendaten']['edit']['land']="2-stelliger ISO Code z.B. DE, AT<br />Dieses Feld dient auch als Vorlage für das Land bei der Neukundenerfassung. Bitte achten Sie deshalb auf korrekte Schreibweise.";
$tooltip['firmendaten']['edit']['steuernummer']="Umsatzsteuer-ID der eigenen Firma. Optional kann hier auch die Steuernummer eingetragen werden, falls keien Umsatzsteuer-ID vorhanden ist.";
$tooltip['firmendaten']['edit']['sepaglaeubigerid']="SEPA Gl&auml;biger ID der eigenen Firma. Wird später im Zahlungsverkehr verwendet.";

// Briefkopf
$tooltip['firmendaten']['edit']['absender']="z.B. Xentral ERP Software GmbH | Fuggerstraße 11 | D-86150 Augsburg";
$tooltip['firmendaten']['edit']['briefhtml']="Aktiviert den erweiterten Texteditor in vielen Feldern für das Briefpapier. Dadurch können im Briefpapier auch fett / kursiv und andere Textanpassungen erkannt werden, z.B. HTML Tags wie &lt;i&gt;,&lt;b&gt;,&lt;u&gt;,&lt;font&gt;,&lt;a&gt;";
$tooltip['firmendaten']['edit']['schriftart']="Folgende Schriftarten sind bereits installiert und verwendbar: Arial, Courier, Helvetica, Times.<br>Eigene Schriftarten können ganz unten am Ende der Seite nach dieser Anleitung angebunden werden: <a target='_blank' href='https://www.wawision.de/helpdesk/kurzanleitung-grundeinrichtung-der-firmendaten-briefkopf#nav-eigene-schriftarten-einf-gen'>Wie binde ich eigene Schriftarten in Xentral an?</a>";
$tooltip['firmendaten']['edit']['knickfalz']="Blendet die Markierung der Knickstellen (Striche) auf dem Beleg aus.";
$tooltip['firmendaten']['edit']['barcode']="Ein Barcode mit der Beleg-Nummer wird auf dem Beleg abgedruckt. Dadurch kann man in verschiedenen Oberflächen die Beleg-Nummer rasch scannen und schneller in den richtigen Beleg geleitet werden.";
$tooltip['firmendaten']['edit']['barcode_y_header']="Angabe in Millimeter. Angabe von 0 bedeutet Anzeige direkt unter der Adresse.";
$tooltip['firmendaten']['edit']['barcode_x_header']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['barcode_x']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['barcode_y']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['seite_von_ausrichtung']="Werte: L=links, R=rechts oder C=zentriert";
$tooltip['firmendaten']['edit']['breite_artikelbeschreibung']="Auch unterhalb von Steuer, Einheit, Rabatt und Gesamtsumme.";
$tooltip['firmendaten']['edit']['langeartikelnummern']="Artikelbezeichnungen werden im Briefpapier unterhalb der Artikelnummern angezeigt.";
$tooltip['firmendaten']['edit']['breite_position']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['breite_nummer']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['breite_menge']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['breite_artikel']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['breite_steuer']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['breite_einheit']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['belege_subpositionen']="Positionen einer Gruppe werden als Unterpunkte der Gruppe aufgeführt mit z.B. 1.1, 1.2, 2.1 etc. <br>Mehr Informationen finden Sie hier: <a target='_blank' href='https://www.wawision.de/helpdesk/kurzanleitung-grundeinrichtung-der-firmendaten-briefkopf#nav-subpositionen-in-gruppen'>Subpositionen in Gruppen auf dem Beleg</a>";
$tooltip['firmendaten']['edit']['belege_subpositionenstuecklisten']="Die Stücklistenunterartikel bekommen Teilnummern wie z.B. 1.1, 1.2, 1.3 auf dem Beleg.";
$tooltip['firmendaten']['edit']['belege_stuecklisteneinrueckenmm']="Rückt die Stücklistenunterartikel um x Millimeter nach rechts ein, um die Unterartikel besser in der Artikeltabelle zu erkennen. <br>Ggf. muss dadurch die Breite der Positionsspalte erweitert werden.";
$tooltip['firmendaten']['edit']['briefpapier_ohnedoppelstrich']="Nach der Gesamtsumme der Artikeltabelle wird kein Doppelstrich auf dem Beleg ausgegeben.";
$tooltip['firmendaten']['edit']['abstand_boxrechtsoben']="Verschieben +- in Millimeter, z.B. -10, +25";
$tooltip['firmendaten']['edit']['abstand_boxrechtsoben_lr']="Verschieben +- in Millimeter, z.B. -10, +25";
$tooltip['firmendaten']['edit']['boxausrichtung']="L oder R und optional Spaltenbreite in Millimeter L;30;40 oder R;30;40";
$tooltip['firmendaten']['edit']['abstand_artikeltabelleoben']="Verschieben +- in Millimeter, z.B. -10, +25";
$tooltip['firmendaten']['edit']['abseite2y']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['abstand_umbruchunten']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['abstand_adresszeileoben']="Verschieben +- in Millimeter, z.B. -10, +25";
$tooltip['firmendaten']['edit']['abstand_adresszeilelinks']="Angabe absolut in Millimeter.";
$tooltip['firmendaten']['edit']['abstand_betreffzeileoben']="Verschieben +- in Millimeter, z.B. -10, +25";
$tooltip['firmendaten']['edit']['abstand_name_beschreibung']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['abstand_seitenrandrechts']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['abstand_seiten_unten']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['abstand_gesamtsumme_lr']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['freitext1x']="Angabe absolut in Millimeter.";
$tooltip['firmendaten']['edit']['freitext1y']="Angabe absolut in Millimeter.";
$tooltip['firmendaten']['edit']['freitext1breite']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['freitext2x']="Angabe absolut in Millimeter.";
$tooltip['firmendaten']['edit']['freitext2y']="Angabe absolut in Millimeter.";
$tooltip['firmendaten']['edit']['freitext2breite']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['footer_farbe']="0=Schwarz, 30=Default, 255=Weiß";
$tooltip['firmendaten']['edit']['footer_breite1']="Angabe in Millimeter.";
$tooltip['firmendaten']['edit']['footer_breite2']="Angabe in Millimeter";
$tooltip['firmendaten']['edit']['footer_breite3']="Angabe in Millimeter";
$tooltip['firmendaten']['edit']['footer_breite4']="Angabe in Millimeter";
$tooltip['firmendaten']['edit']['schriftart_upload_bezeichnung']="Es sind nur Buchstaben, Zahlen, Unterstriche und Minus erlaubt";


// Textvorlagen
$tooltip['firmendaten']['edit']['textarea[name="zahlung_lastschrift_de"]']="Wenn Sie in Ihren Textvorlagen auf Felder der Kunden-/Lieferanten-Adresse zugreifen möchten (und nicht auf die Felder im Dokument), stellen Sie Variable 'ADRESSE_' voran. Bsp.: ADRESSE_NAME liefert den Namen aus den Stammdaten.";

// E-Mail
$tooltip['firmendaten']['edit']['mailanstellesmtp']="Nur aktivieren wenn oben nichts eingestellt ist!";
$tooltip['firmendaten']['edit']['noauth']="Nur aktivieren wenn Server keine Authentifizierung ben&ouml;tigt!";
$tooltip['firmendaten']['edit']['port']="Meistens werden die folgenden Ports verwendet:<br />
                                         <ul>
                                         <li>SMTPS oder SSL: 465</li>
                                         <li>TLS: 587</li>
                                         <li>Ohne Verschl&uuml;sselung: 25</li>
                                         </ul>";
$tooltip['firmendaten']['edit']['email']="Bei Empf&auml;nger angezeigte E-Mail Adresse.";
$tooltip['firmendaten']['edit']['absendername']="Bei Empf&auml;nger angezeigter Name.";
$tooltip['firmendaten']['edit']['bcc1']="Jede ausgehende E-Mail wird parallel als Kopie an Adresse gesendet.";
$tooltip['firmendaten']['edit']['bcc2']="Jede ausgehende E-Mail wird parallel als Kopie an Adresse gesendet.";
$tooltip['firmendaten']['edit']['bcc3']="Jede ausgehende E-Mail wird parallel als BCC an Adresse gesendet.<br>Die E-Mails enthalten somit auch den Hauptempfänger, der für externe Verwaltungs-Systeme wie z.B. Hubspot wichtig ist.";
$tooltip['firmendaten']['edit']['email_html_template']="Der Inhalt der E-Mail steht in der Variable {CONTENT}.<br>Externe Bilder werden in der Vorschau aufgrund von Sicherheitseinstellungen <strong>nicht</strong> angezeigt.";


/* Nummernkreise */
$tooltip['firmendaten']['edit']['warnung_doppelte_nummern']="Eine Warnung wird als Infobox angezeigt, wenn es doppelte Kunden-, Artikel-, Rechnungs- oder Gutschriftsnummern gibt.<br>Siehe Anleitung: <a href='https://www.wawision.de/helpdesk/kurzanleitung-doppelte-nummern' target='_blank'>Doppelte Nummern</a>";
$tooltip['firmendaten']['edit']['warnung_doppelte_seriennummern']="Eine Warnung wird als Infobox angezeigt, wenn es beim Einlagern oder im Lieferschein doppelte Seriennummern gibt. <br><br>Siehe Anleitung: <a href='https://www.wawision.de/helpdesk/kurzanleitung-doppelte-nummern#nav-doppelte-seriennummern' target='_blank'>Doppelte Seriennummern</a>";
$tooltip['firmendaten']['edit']['belegnummersortierungint']="Nicht verwenden falls Buchstaben oder Sonderzeichen in Belegnummern sind.<br />In der Übersicht der Belege, wird die Belegnummer als Zahl gewertet und dementsprechend sortiert.<br><br>Beispiel: 10, 11, 100, 1000<br><br>Numerisch: 10, 11, 100, 1000<br>Aphabetisch (ohne Haken): 10, 100, 1000, 11<br>";


// Steuer / Waehrung
$tooltip['firmendaten']['edit']['steuersatz_normal']="z.B. 19.0 (mit Punkt statt Komma)";
$tooltip['firmendaten']['edit']['steuersatz_ermaessigt']="z.B. 7.0 (mit Punkt statt Komma)";
$tooltip['firmendaten']['edit']['kleinunternehmer']="Die Kleinunternehmerregelung gemäß § 19 UStG.";
$tooltip['firmendaten']['edit']['steuerfrei_inland_ausblenden']="Die 0% in Gesamtsumme ausblenden wenn Steuerfrei Inland.";
$tooltip['firmendaten']['edit']['steuerspalteausblenden']="In AN, AB, RE und GS wird Spalte Steuer ausgeblendet, wenn es nur einen Steuersatz im Beleg gibt.";
$tooltip['firmendaten']['edit']['immernettorechnungen']="In AN, AB, RE und GS werden Netto Preise angezeigt.";
$tooltip['firmendaten']['edit']['immerbruttorechnungen']="In AN, AB, RE und GS werden Brutto Preise angezeigt.";
$tooltip['firmendaten']['edit']['viernachkommastellen_belege']="4 anstatt 2 Kommastellen auf Dokumente.";
$tooltip['firmendaten']['edit']['loadcurrencyrate']="Wechselkurs aus W&auml;hrungstabelle ziehen beim Beleg weiterf&uuml;hren.</i>";
$tooltip['firmendaten']['edit']['steuer_standardkonto']="Wird ein FiBu Export unter Buchhaltung => Finanzbuchhaltungsexport => Konten durchgeführt und im Zahlungseingang sind importierte Zahlungen, die noch nicht zugeordnet wurden, wird dieser Wert in die Spalte 'Gegenkonto' gebucht.<br />Als Standard wenn Buchung nicht verbucht wurde bei Export.";
$tooltip['firmendaten']['edit']['steuer_standardkonto_aufwendungen']="Nicht gebuchte Aufwendungen als Standard Konto exportieren.";
$tooltip['firmendaten']['edit']['steuer_anpassung_kundennummer']="Vor dem Export f&uuml;r die Buchhaltung wird der angegebene Wert gesucht und gel&ouml;scht. z.B. das Pr&auml;fix \"2018-\".";
$tooltip['firmendaten']['edit']['gutschriftkursvonrechnung']="Wechselkurs aus Rechnung nehmen, nicht den aktuellen Kurs.";
$tooltip['firmendaten']['edit']['standardmarge']="z.B. 30 f&uuml;r 30% bedeutet EK*1.3";


// System
// System - Einstellungen
$tooltip['firmendaten']['edit']['#projekt']="Dieses Projekt wird immer im Standard geladen.<br>Steuert die Projektvorschläge bei Neuanlage von Adressen und Artikeln sowie Geschäftsbriefvorlagen.";
$tooltip['firmendaten']['edit']['#projektoeffentlich']="Steuert die Sichtbarkeit des Projektes.<br>Die Inhalte von öffentlichen Projekten sind für alle Mitarbeiter sichtbar. Übersteuert die Rollenzuweisung in den Adressstammdaten.";
$tooltip['firmendaten']['edit']['#standardaufloesung']="Keine Scrollbar bei kleinen Bildschirmen anzeigen"; // TODO
$tooltip['firmendaten']['edit']['standardversanddrucker']="Voreinstellung für den Standard Dokumentendrucker, der für Druckvorgänge vorausgewählt ist.<br>Kann pro Benutzer in den Benutzereinstellungen übersteuert werden.<br><br>
Wenn Sie keine physischen Drucker anbinden, bietet es sich an hier zunächst einen PDF Drucker einzustellen siehe <a href='https://xentral.biz/helpdesk/kurzanleitung-drucker-einrichten#nav-download-drucker-anlegen' target='_blank'>Anleitung</a>)."; // TODO
$tooltip['firmendaten']['edit']['standardetikettendrucker']="Voreinstellung für den Standard Etikettendrucker.<br><br>Wenn Sie keinen Etiketten-Drucker anbinden, bietet es sich an hier zunächst einen PDF Drucker einzustellen - so können auch Etiketten als PDF Popup gedruckt werden.";
$tooltip['firmendaten']['edit']['etikettendrucker_wareneingang']="Voreinstellung für den Standard Etikettendrucker im Wareneingang (sofern dieser Wareneingangsprozess verwendet wird).";
$tooltip['firmendaten']['edit']['wareneingang_gross']="Wareneingang in zwei Schritten Annahme + Distribution.<br><br>Beim großen Wareneingang können z.B. auch eine Waage und Kamera verwendet werden, außerdem können die Nummern der Lieferantenrechnungen und Lieferantenlieferscheine erfasst werden.<br><br>Eine Anleitung dazu finden Sie <a href = 'https://xentral.biz/helpdesk/kurzanleitung-zentraler-wareneingang#nav-gro-er-wareneingang' target='_blank'>hier.</a>";
$tooltip['firmendaten']['edit']['wareneingang_lagerartikel']="Im Wareneingang werden nur Artikel angezeigt, die als Lagerartikel definiert sind.";
$tooltip['firmendaten']['edit']['wareneingang_kamera_waage']="Angebundene Kamera und Waage werden im Wareneingang genutzt. Funktioniert nut in Verbindung mit der Option 'Große Paketannahme'.";
$tooltip['firmendaten']['edit']['wareneingangdmsdrucker']="Druck von Etiketten mit Barcode für Belege im Wareneingang an/aus.";
$tooltip['firmendaten']['edit']['aufgaben_bondrucker']="Auswahl eines angebundenen Bondruckers, auf dem die Aufgaben-Zettel ausgedruckt werden sollen.";
$tooltip['firmendaten']['edit']['paketmarke_mit_waage']="Das Gewicht für die Paketmarke wird nicht über die im Artikel hinterlegten Gewichte bestimmt, sondern von einer angebundenen Waage.<br><br>Macht nur Sinn, wenn eine Waage bei der Versandstation angebunden ist.";
$tooltip['firmendaten']['edit']['wareneingang_zwischenlager']="Internes Zwischenlager ist im Wareneingang vorausgewählt.<br>Dadurch wird die Ware ins Zwischenlager eingelagert und kann über Lager => Zwischenlager an die echten Lägerplätze verteilt werden.";
$tooltip['firmendaten']['edit']['versand_gelesen']="Der Freitext und die interne Bemerkung eines Lieferscheins tauchen als rote Meldung im Versandzentrum als Popup auf, das vom Mitarbeiter bestätigt werden muss.<br><br>Funktioniert nur in Verbindung mit einem Logistikprozess (Projekt => Einstellungen => Logistik / Versand), der den Lieferschein in das Versandzentrum übergibt.";
$tooltip['firmendaten']['edit']['mahnwesenmitkontoabgleich']="Der Zahlungsstatus von GS und RE werden automatisch geprüft und neu berechnet, sofern nicht manuell festgeschrieben in der Rechnung. Diese Prüfung wird gestartet wenn man das Mahnwesen aufruft, einen Zahlungseingang auf die RE/GS durchführt oder direkt in den Beleg klickt.";
$tooltip['firmendaten']['edit']['auftragmarkierenegsaldo']="Unbezahlte Aufträge werden in der Auftragsübersicht rot markiert.<br><br>Sobald ein Zahlungseingang auf den Auftrag oder dessen verknüpfte Rechnung einging bzw. die Rechnung auf manuell bezahlt gesetzt wird, ändert sich die Farbe von rot auf schwarz.";
$tooltip['firmendaten']['edit']['wareneingangauftragzubestellung']="Nur für das Modul: Auftrag zu Bestellung. Ist der Haken gesetzt, kommt eine zusätzliche Spalte mit der Auftragsnummer im Wareneingang rein.<br>Dies ist vor allem sinnvoll wenn man über einen Auftrag eine Bestellung anlegt.";
$tooltip['firmendaten']['edit']['internebemerkungminidetails']="Die interne Bemerkung eines Auftrags lässt sich auch im Mini-Detail eines Auftrags in der Auftragsübersicht editieren.<br>Dies funktioniert auch bei schreibgeschützten Aufträgen.";
$tooltip['firmendaten']['edit']['bezeichnungaktionscodes']="Das Feld für die Eingabe der Aktionscodes im Auftrag, kann hiermit umbenannt werden.";
$tooltip['firmendaten']['edit']['kommissionskonsignationslager']="Nur für das extra Modul 'Kommissions-/Konsignationslager'.<br>Hier kann man das Feld im Auftrag für das Konsignationslager umbenennen.";
//System - Einstellungen Artikel, Optionen
$tooltip['firmendaten']['edit']['artikel_suche_kurztext']="Ermöglicht in der Artikel-Übersicht nach Inhalten aus dem Artikel-Feld 'Kurztext DE' zu suchen.";
$tooltip['firmendaten']['edit']['artikel_suche_variante_von']="Ermöglicht die Suche nach Varianten. Ist der Haken gesetzt, werden auch die Varianten zu einem gesuchten Artikel angezeigt."; // TODO
$tooltip['firmendaten']['edit']['artikel_freitext1_suche']="Ermöglicht in der Artikel-Übersicht nach Inhalten von Freifeld 1 und Freifeld 2 aus den Artikeln zu suchen.";
$tooltip['firmendaten']['edit']['artikel_artikelnummer_suche']="Ermöglicht die Suche nach den Artikelnummern von Kunden und Lieferanten in der Artikelübersicht, dem Adressstamm sowie in Belegpositionen.Die Artikelnummern von Lieferanten können beim Anlegen der Einkaufpreise angegeben werden, die Artikelnummern von Kunden beim Anlegen von Verkaufspreisen des Artikels.";
$tooltip['firmendaten']['edit']['artikel_beschleunigte_suche']="In der Suche in der Artikel-Übersicht wird nur nach Artikelnummer und Artikelbeschreibung DE gesucht.<br>Diese Option überschreibt die Auswahl der oberen Haken zur Artikelsuche (z.B. Suche nach Freifeld / Kurztext).";
$tooltip['firmendaten']['edit']['artikel_bilder_uebersicht']="Zeigt das Standard-Artikelbild in der Artikel-Listenansicht.<br>Sollte kein Bild unter Artikel => Dateien hinterlegt sein, wird ein Musterbild eingeblendet.<br><br>Hinweis: Diese Option verlangsamt den Aufbau der Artikel-Übersicht und sollte nur verwendet werden, wenn wenige Ergebnisse pro Seite angezeigt werden.";
$tooltip['firmendaten']['edit']['artikel_baum_uebersicht']="Zeigt den Artikelbaum links neben der Artikel-Liste in der Artikel-Übersicht.<br>Es kann auf eine Kategorie geklickt werden um die Artikel-Liste nach dieser Kategorie zu filtern.";
$tooltip['firmendaten']['edit']['datatables_export_button_flash']="In vielen Live-Tabellen werden durch diese Option unterhalb Buttons für PDF, CSV, Excel Export eingeblendet.<br><br>Hinweis: Es wird immer nur das exportiert, was in der Tabelle im Moment auf der Seite der Tabelle sichtbar ist - z.B. 10 von 1000 Einträge. Wenn mehr exportiert werden soll, können Sie die Anzahl der Elemente auf 1000 pro Seite stellen.";
$tooltip['firmendaten']['edit']['schnellanlegen']="Belege werden ohne Zwischenfrage nach vorhandenen Entwürfen direkt angelegt.<br>Ohne diese Option gelangen Sie in eine Zwischenmaske um vorhandene Entwürfe weiter zu bearbeiten.";
$tooltip['firmendaten']['edit']['schnellanlegen_ohnefreigabe']="Belege werden nach dem Anlegen automatisch freigegeben ohne, dass der Beleg in den Entwurfsmodus kommt und von dort freigegeben werden muss. Es wird direkt eine Belegnummer vergeben und der Status freigegeben gesetzt (aktuell in Angebot, Auftrag, Rechnung, Gutschrift, Lieferschein, Bestellung).<br><br>Achtung: Sobald mehrere Projekte Anwendung finden, greift diese Option nicht mehr!";
$tooltip['firmendaten']['edit']['erweiterte_positionsansicht']="Artikelbeschreibung ist in der Positionsansicht von Belegen sichtbar. Aktuell in Angebot, Auftrag, Rechnung, Gutschrift und Lieferschein.";
$tooltip['firmendaten']['edit']['stuecklistegewichtnurartikel']="Es wird bei JIT-Stücklisten nur das Gewicht aus dem Hauptartikel für das Auftragsprotokoll und den Paketmarkendruck verwendet.<br>Ist die Option nicht aktiviert, so werden die Gewichte, sofern sie gepflegt sind, aus dem Hauptartikel und den Einzelpositionen der JIT-Stückliste aufaddiert. Dann könnte der Hauptartikel z.B. als Verpackungsteil o.Ä. dienen.<br><br>Bei einer normalen Stückliste wird immer nur das Gewicht des Hauptartikels verwendet, wenn es gepflegt ist.<br>Wurde kein Gewicht im Haupartikel gepflegt, so wird auch hier das Gewicht aller Einzelpositionen verwendet.";
$tooltip['firmendaten']['edit']['bestellvorschlaggroessernull']="Wenn ein Artikel rechnerisch im Bestellvorschlag nicht nachbestellt werden muss und in keiner offenen Bestellung vorhanden ist, werden diese Artikel mit der Option im Bestellvorschlag ausgeblendet (Einkauf => Bestellvorschlag).";
$tooltip['firmendaten']['edit']['bestellungohnepreis']="Im Beleg Bestellung wird keine Bestellsumme ausgegeben. Die einzelnen Positionen sind mit dem Betrag 0 in der Positionstabelle dargestellt.";
$tooltip['firmendaten']['edit']['bestellungmitartikeltext']="Die Artikelbeschreibung wird auf dem Beleg der Bestellung unterhalb des Artikelnamens in der Positionstabelle dargestellt.";
$tooltip['firmendaten']['edit']['bestellungeigeneartikelnummer']="In der ersten Spalte der Positionstabelle der Bestellung wird die eigene Artikelnummer statt der Artikelnummer des Lieferanten (aus dem EK) gesetzt.";
$tooltip['firmendaten']['edit']['bestellunglangeartikelnummern']="Im Beleg der Bestellung innerhalb der Positionstabelle werden der Artikelname und die Artikelbeschreibung um eine Zeile nach unten verschoben, sodass lange Artikelnummern nicht in den Namen oder in die Beschreibung hineinschreiben.<br><br>Hinweis: Diese Option funktioniert nur für Artikelnummern des Lieferanten in der ersten Spalte.";
$tooltip['firmendaten']['edit']['bestellungabschliessen']="Schliesst Bestellungen automatisch ab, sobald die gesamte Ware der Bestellung über den Wareneingang als geliefert markiert ist.<br><br>Wenn man in die Bestellungsübersicht klickt, werden die offenen Bestellungen überprüft, ob diese mit der Option auf abgeschlossen gesetzt werden können.<br>Außerdem gibt es einen Prozessstarter (bestellungabschliessen), der das im Hintergrund ebenfalls überprüft.";
$tooltip['firmendaten']['edit']['verkaufspreisevpe']="Wenn der Artikel VPE Einheiten mit Verkaufs-Staffelpreisen hat, wird der Preis und die Menge je nach eingegebener Menge in den Positionen automatisch angepasst (aktuell in Aufträgen, Angeboten, Rechungen, Gutschriften).<br><br>Beispiel:<br>1. VK: ab Menge 1 | 10 Euro | VPE 10<br>2. VK: ab Menge 10 | 9 Euro | VPE 10<br><br>Eingabe von 5 => Menge ändert sich automatisch auf 10 und Einzelpreis 10<br>Eingabe von 13 => Menge ändert sich automatisch auf 20 und Einzelpreis 9<br><br>Hinweis: Es muss eine VPE-Menge im Preis angegeben sein, dass die Funktion greift.";
$tooltip['firmendaten']['edit']['einkaufspreisevpe']="Wenn der Artikel VPE Einheiten mit Einkaufs-Staffelpreisen hat, wird der Preis und die Menge je nach eingegebener Menge in den Positionen automatisch angepasst (Bestellung).<br><br>Beispiel:<br>1. EK: ab Menge 1 | 10 Euro | VPE 10<br>2. EK: ab Menge 10 | 9 Euro | VPE 10<br><br>Eingabe von 5 => Menge ändert sich automatisch auf 10 und Einzelpreis 10<br>Eingabe von 13 => Menge ändert automatisch auf 20 und Einzelpreis 9<br><br>Hinweis: Es muss eine VPE-Menge im Preis angegeben sein, dass die Funktion greift.";
$tooltip['firmendaten']['edit']['porto_berechnen']="Mit dieser Option können die Versandkosten eines Auftrags auf 0 gesetzt werden, sodass der Auftrag versandkostenfrei wird.<br><br>Dazu muss eine Preisgruppe angelegt werden, zu der alle Kunden gehören, die versandkostenfrei einkaufen können sollen.
In der Preisgruppe wird der Mindestbestellwert festgelegt (z.B. 50€), ab dem der Auftrag versandkostenfrei sein soll.<br><br>Eine genau <a href='https://xentral.com/akademie-faq/kurzanleitung-groups-hd-wie-kann-die-funktion-portofrei-ab-in-der-gruppe-aktiviert-werden' target='_blank'>Anleitung</a> befindet sich im Handbuch.";
$tooltip['firmendaten']['edit']['auftrag_eantab']="Im Auftrag erscheint ein neues Tab 'Barcodescanner'.<br>Dort kann man bequem Artikel-Nr. / EANs abscannen und somit Artikel zu den Auftragspositionen hinzufügen.";
$tooltip['firmendaten']['edit']['standard_datensaetze_datatables']="Hier können Sie die Anzahl der angezeigten Listeneinträge in den Live-Tabellen (Artikelübersicht, Adressübersicht, Rechnungsübersicht usw.) festgelegt werden, z.B. 10, 25, 50, 200, oder 1000.<br><br>Bei Wert 0 fällt Xentral auf den Standardwert von 10 zurück.<br><br>Hinweis: Wenn Sie schnell nacheinander die Anzahl ändern, müssen Sie eventuell Ihre Cookies im Browser löschen.";
$tooltip['firmendaten']['edit']['artikeleinheit']="Blendet auf den Belegen eine Spalte für die Artikel-Einheit ein.<br><br>Die Einheit kann in jedem Artikel individuell angegeben werden - wenn diese nicht gesetzt ist, wird die Standard-Artikeleinheit gezogen (siehe nächste Option).";
$tooltip['firmendaten']['edit']['artikeleinheit_standard']="Wird als Einheit für alle Artikel eingeblendet, die keine individuelle Einheit im Artikel hinterlegt haben.";
$tooltip['firmendaten']['edit']['herstellernummerimdokument']="Falls eine Herstellernummer im Artikel hinterlegt ist, wird diese in der Positionstabelle auf dem Beleg mit abgebildet.<br><br>Beispiel: 'Herstellernummer: 123456789'<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Bestellung, Gutschrift.";
$tooltip['firmendaten']['edit']['abmessungimdokument']="Falls im Artikel Höhe/Breite/Länge hinerlegt wurde, wird dies in der Positionstabelle auf dem Beleg mit abgebildet.<br><br>Beispiel: 'Abmessungen: 10,00 x 20,00 x 30,00'<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Bestellung, Gutschrift.";
$tooltip['firmendaten']['edit']['projektnummerimdokument']="Falls die Projekt-Kennung vorhanden ist, wird sie auf dem Beleg in der Infobox dargestellt.<br><br>Beispiel: 'Projekt: STANDARD'<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Bestellung, Gutschrift.";
$tooltip['firmendaten']['edit']['bearbeitertelefonimdokument']="Die Telefonnummer des Bearbeiters wird auf dem Beleg in der Infobox dargestellt. <br>Vorraussetzung ist eine hinterlegte Telefonnummer im Feld 'Telefon' in der Adresse, die mit dem Benutzer verknüpft ist.<br><br>Beispiel: 'Telefon: 01234/123123123'<br><br>Aktuell in in Angebot, Auftrag, Rechnung, Lieferschein, Gutschrift.";
$tooltip['firmendaten']['edit']['bearbeiteremailimdokument']="Die E-Mail des Bearbeiters wird auf dem Beleg in der Infobox dargestellt.<br>Vorraussetzung ist eine hinterlegte E-Mail Adresse im Feld 'E-Mail' in der Adresse, die mit dem Benutzer verknüpft ist.<br><br>Beispiel: 'E-Mail: maxmuster@test.de'<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Gutschrift.";
$tooltip['firmendaten']['edit']['typimdokument']="Zeigt den Adress-Typ (Herr, Frau, Firma, eigenen Typ) in der ersten Zeile des Adressblocks auf dem Beleg an.<br><br>Hinweis: Es wird der Typ der Adresse selbst angezeigt, nicht der Typ des Ansprechpartners.<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Gutschrift.";
$tooltip['firmendaten']['edit']['staffelpreiseanzeigen']="Im Angebot in der Positionstabelle werden Staffelpreise eines Artikels untereinander dargestellt.<br><br>Beispiel:<br>ab 1 St. 10,00 EUR<br>ab 5 St. 9,00 EUR<br>ab 10 Str. 8,00 EUR<br><br>Für 'St.'' wird die Artikeleinheit im Artikel selber hergenommen (z.B. Liter, etc.).<br>Falls diese leer ist, wird die Standard Einheit in den Systemeinstellungen hergenommen.<br>Wenn diese auch leer ist, wird 'St.'' verwendet.";
$tooltip['firmendaten']['edit']['auftragabschliessen']="Wird der Auftrag manuell über das Aktionsmenü zu Lieferschein oder Rechnung weitergeführt, wird der Auftrag nicht auf den Status 'abgeschlossen' gesetzt.<br>Mehr Informationen und Anwendungsbeispiele finden Sie hier: <a href='https://xentral.com/helpdesk/kurzanleitung-logistikprozesse-workflow-oberflaeche#nav-individuelle-logistik-workflows' target='_blank'>https://xentral.com/helpdesk/kurzanleitung-logistikprozesse-workflow-oberflaeche#nav-individuelle-logistik-workflows</a>.";
$tooltip['firmendaten']['edit']['belegeinanhang']="PDFs, die beim Abschicken-Dialog im Beleg mitgesendet werden, werden als Datei im Beleg gespeichert.<br>Diese PDFs tauchen dann auch im Abschicken-Dialog als PDF Icon auf.";
$tooltip['firmendaten']['edit']['dateienweiterfuehren']="Wenn man Belege weiterführt, z.B. Auftrag zu Lieferschein, wird die Datei (unabhängig vom Dateityp) mit in den neuen Beleg übergeben.<br>Es wird kein Duplikat der Datei erstellt. Die Datei wird nur im nächsten Beleg verknüpft.<br><br>Funktioniert für: <ul><li>Anfrage => Angebot</li><li>Angebot => Auftrag</li><li>Angebot => Proformarechnung</li><li>Auftrag => Rechnung</li><li>Auftrag => Lieferschein</li><li>Auftrag => Produktion</li><li>Auftrag => Bestellung</li><li>Bestellung => Produktion</li><li>Lieferschein => Rechnung</li><li>Rechnung => Gutschrift</li></ul>";
$tooltip['firmendaten']['edit']['lieferdatumkw']="Der Haken 'Kalenderwoche (KW)' als Lieferdatum ist standardmäßig in der Belegposition gesetzt für alle nach dem Aktivieren eingefügten Positionen.<br><br>Aktuell in Angebot, Auftrag, Lieferschein, Rechnung, Gutschrift.";
$tooltip['firmendaten']['edit']['freifelderimdokument']="Um sich Freifelder innerhalb von Beleg-Positionen anzeigen zu lassen, müssen Sie auch noch unter Administration => Einstellungen => Grundeinstellungen => Freifelder die Belege auswählen, in denen das Freifeld erscheinen soll. Es wird der Name des Freifelds & der Inhalt in der Positionstabelle im Beleg dargestellt.<br><br>Beispiel: 'Name von Freifeld 1: Inhalt von Freifeld 1'<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Bestellung, Gutschrift.";
$tooltip['firmendaten']['edit']['beleg_pos_ean']="Falls eine EAN Nummer im Artikel hinterlegt ist, wird diese in der Positionstabelle auf dem Beleg mit abgebildet.<br><br>Beispiel: 'EAN: 123456789'<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Gutschrift.</i>";
$tooltip['firmendaten']['edit']['rechnung_gutschrift_ansprechpartner']="Der Ansprechpartner wird auf dem Beleg in der Rechnung und Gutschrift in der 2. Zeile im Adressblock ein / ausgeblendet.";
$tooltip['firmendaten']['edit']['beleg_pos_zolltarifnummer']="Haken gesetzt: Die Zolltarifnummer wird immer abgebildet in den Belegpositionen, wenn eine Zolltarifnummer vorhanden ist.<br><br>Haken nicht gesetzt: Die Zolltarifnummer und Herkunftsland werden nur auf Rechnungen abgebildet, wenn diese auf Besteuerung Export stehen und eine Zolltarifnummer vorhanden ist.<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Gutschrift.";
$tooltip['firmendaten']['edit']['beleg_pos_herkunftsland']="Haken gesetzt: Das Herkunftsland wird nie angezeigt.<br><br>Haken nicht gesetzt: Das Herkunftsland wird nur auf Rechnungen abgebildet, wenn diese auf Besteuerung Export stehen und eine Zolltarifnummer vorhanden ist.<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Gutschrift.";
$tooltip['firmendaten']['edit']['beleg_pos_mhd']="MHD eines Artikels wird falls vorhanden in der Positionstabelle auf Rechnung und Lieferschein dargstellt.<br>Wichtig hierbei sind die Logistik-Einstellungen aus dem Projekt des Belegs zu MHD (Scannen im Versandzentrum / FIFO).<br><br>Beispiel: 'MHD: 13.10.2020'<br><br>Aktuell in Rechnung, Lieferschein.";
$tooltip['firmendaten']['edit']['beleg_pos_charge']="Charge eines Artikels wird falls vorhanden in der Positionstabelle auf Rechnung und Lieferschein dargstellt.<br>Wichtig hierbei sind die Logistik-Einstellungen aus dem Projekt des Belegs zu Chargen (Scannen im Versandzentrum / FIFO).<br><br>Beispiel: 'Charge: 123456789 (2)'<br>(In Klammern ist die Menge zu jeder Charge)<br><br>Aktuell in Rechnung, Lieferschein.";
$tooltip['firmendaten']['edit']['beleg_pos_sn']="Seriennummern eines Artikels werden falls vorhanden in der Positionstabelle auf Rechnung und Lieferschein dargstellt.<br>Wichtig hierbei sind die Logistik-Einstellungen aus dem Projekt des Belegs zu Seriennummern (Scannen im Versandzentrum / FIFO).<br><br>Beispiel: 'S/N: 123456789'<br><br>Aktuell in Rechnung, Lieferschein.";
$tooltip['firmendaten']['edit']['festetrackingnummer']="Nach dem Scannen der Trackingnummer wird diese auf dem Lieferschein unterhalb der Artikel-Tabelle abgebildert.<br><br>Hinweis: Dies ist eine veraltete Option und sollte nicht mehr benutzt werden. Besser ist es unter Administration => Einstellungen => Grundeinstellungen => Textvorlagen die Trackingnummer über die Variable {TRACKINGNUMMER} auszugeben. Dabei können auch mehrere Trackingnummern angezeigt werden."; // TODO
$tooltip['firmendaten']['edit']['adresse_freitext1_suche']="In der Adress-Übersicht werden auch die Inhalte aus Freifeld 1 und Freifeld 2 aus der Adresse durchsucht.";
$tooltip['firmendaten']['edit']['angebot_auftrag_bestellung_ansprechpartner']="Der Ansprechpartner wird auf dem Beleg im Angebot, Auftrag und Bestellung in der 2. Zeile im Adressblock ein / ausgeblendet";
$tooltip['firmendaten']['edit']['beleg_artikelbild']="Artikelbild wird bei allen Aufträgen im PDF mit angezeigt";
$tooltip['firmendaten']['edit']['bezeichnungkundennummer']="Die Bezeichnung für 'Kundennummer' in der Infobox auf Belegen kann hier umbenannt werden.<br>Mit einem Klick auf das Weltkugel Icon können Sie auch Übersetzungen für andere Sprachen dazu anlegen.";
$tooltip['firmendaten']['edit']['auftrag_bezeichnung_vertrieb']="Die Bezeichnung für 'Vertrieb' in der Infobox auf Belegen kann hier umbenannt werden.<br>Mit einem Klick auf das Weltkugel Icon können Sie auch Übersetzungen für andere Sprachen dazu anlegen.";
$tooltip['firmendaten']['edit']['auftrag_bezeichnung_bearbeiter']="Die Bezeichnung für 'Bearbeiter' in der Infobox auf Belegen kann hier umbenannt werden.<br>Mit einem Klick auf das Weltkugel Icon können Sie auch Übersetzungen für andere Sprachen dazu anlegen.";
$tooltip['firmendaten']['edit']['auftrag_bezeichnung_bestellnummer']="Die Bezeichnung für 'Bestellnummer' in der Infobox auf Belegen kann hier umbenannt werden.<br>Mit einem Klick auf das Weltkugel Icon können Sie auch Übersetzungen für andere Sprachen dazu anlegen.";
$tooltip['firmendaten']['edit']['vertriebbearbeiterfuellen']="Bei der Erstellung und Bearbeiten von Belegen, wird nicht automatisch das (leere) Bearbeiter- und Vertriebsfeld gefüllt, sondern muss manuell gefüllt werden.";
$tooltip['firmendaten']['edit']['internetnummerimbeleg']="Gibt die Bestellnummer falls vorhanden aus dem Shop im Infoblock des Belegs aus.<br><br>Aktuell in Angebot, Auftrag, Rechnung, Lieferschein, Bestellung, Gutschrift.";
$tooltip['firmendaten']['edit']['beschriftunginternetnummer']="Die Bezeichnung für 'Internetnummer' in der Infobox auf Belegen kann hier umbenannt werden.<br>Mit einem Klick auf das Weltkugel Icon können Sie auch Übersetzungen für andere Sprachen dazu anlegen.";
$tooltip['firmendaten']['edit']['bezeichnungstornorechnung']="In der  Gutschrift gibt es einen Haken um den Betreff des Gutschrift-Belegs z.B. in 'Stornorechnung' umzubenennen.<br>Mit der Option hier legen Sie den Namen des alternativen Betreffs fest. Beachten Sie bitte 06/2013 §14 UStG bei der Benennung.";
$tooltip['firmendaten']['edit']['stornorechnung_standard']="Mit dieser Option setzen Sie die alternative Bezeichnung in der Gutschrift (Option drüber) als Standard. Dadurch ist der Haken beim Erstellen eines neuen Gutschrifts immer gesetzt.";
$tooltip['firmendaten']['edit']['bezeichnungangebotersatz']="Im Angebot gibt es einen Haken um den Betreff des Angebot-Belegs umzubenennen.<br>Mit der Option hier legen Sie den Namen des alternativen Betreffs fest.";
$tooltip['firmendaten']['edit']['angebotersatz_standard']="Mit dieser Option setzen Sie die alternative Bezeichnung im Angebot (Option drüber) als Standard. Dadurch ist der Haken beim Erstellen eines neuen Angebots immer gesetzt.";
$tooltip['firmendaten']['edit']['bezeichnungauftragersatz']="Im Auftrag gibt es einen Haken um den Betreff des Augtrag-Belegs umzubenennen.<br>Mit der Option hier legen Sie den Namen des alternativen Betreffs fest.";
$tooltip['firmendaten']['edit']['bezeichnungrechnungersatz']="In der Rechnung gibt es einen Haken um den Betreff des Rechnung-Belegs umzubenennen.<br>Mit der Option hier legen Sie den Namen des alternativen Betreffs fest.";
$tooltip['firmendaten']['edit']['bezeichnunglieferscheinersatz']="Im Lieferschein gibt es einen Haken um den Betreff des Lieferschein-Belegs umzubenennen.<br>Mit der Option hier legen Sie den Namen des alternativen Betreffs fest.";
$tooltip['firmendaten']['edit']['bezeichnungbestellungersatz']="In der Bestellung gibt es einen Haken um den Betreff des Bestell-Belegs umzubenennen.<br>Mit der Option hier legen Sie den Namen des alternativen Betreffs fest.";
$tooltip['firmendaten']['edit']['bezeichnungproformarechnungersatz']="In der Proformarechnung gibt es einen Haken um den Betreff der Proformarechnung-Belegs umzubenennen.<br>Mit der Option hier legen Sie den Namen des alternativen Betreffs fest.";
$tooltip['firmendaten']['edit']['briefpapier_bearbeiter_ausblenden']="Blendet den Bearbeiter im Infoblock des Beleges aus";
$tooltip['firmendaten']['edit']['briefpapier_vertrieb_ausblenden']="Blendet den Vertriebsmitarbeiter im Infoblock des Beleges aus";
$tooltip['firmendaten']['edit']['angebot_anzahltage']="Hier wird die Anzahl an Tagen eingetragen, für die ein Angebot gültig sein soll.<br>Das Enddatum wird dadurch automatisch gesetzt. Dieses können Sie über die Variable {GUELTIGBIS} ausgeben.";
$tooltip['firmendaten']['edit']['angebot_anzahlwiedervorlage']="Nach Freigabe eines Angebots, wird automatisch eine Wiedervorlage angelegt, wenn die Tage > 0 sind";
$tooltip['firmendaten']['edit']['wiedervorlage_mitarbeiter']="Mit dieser Einstellung legen Sie fest, dass im Modul Wiedervorlagen nur Adressen mit der Rolle Mitarbeiter als der Bearbeiter einer Wiedervorlage eingetragen werden können.<br>Lassen Sie die Option deaktiviert, können alle Adressen, unabhängig von ihrer Rolle als Bearbeiter einer Wiedervorlage eingetragen werden.";
$tooltip['firmendaten']['edit']['guenstigste_vk']="Es wird in Belegen immer der günstigste Verkaufspreis vorgeschlagen.<br>Ist der Haken nicht gesetzt, so muss der Verkaufspreis dem Einzelkunden explizit im Artikel zugeordnet werden.<br>Ist das nicht der Fall, so nimmt das System weiterhin den niedrigsten Verkaufspreis.";
$tooltip['firmendaten']['edit']['angebot_pipewiedervorlage']="Stage der Wiedervorlage, die in einer automatischen Wiedervorlage aus dem Angebot für die Wiedervorlage übernommen werden soll.";
$tooltip['firmendaten']['edit']['gewichtbezeichnung']="Einheit, in der das Gewicht systemweit dargestellt wird.<br>Wenn nicht anderweitig gepflegt, ist die Standardgewichtseinheit kg.";
$tooltip['firmendaten']['edit']['gewichtzukgfaktor']="Der Umrechnungsfaktor zu Kilogramm.<br><br>Beispiel:<br> Für Tonne wäre es 0,001<br>Für Gramm wäre es 1000";
$tooltip['firmendaten']['edit']['produktionsverhalten']="Hier wird das Verhalten der zu produzierenden Stücklisten definiert, wenn diese aus einem Auftrag produziert werden sollen:<br><br><ul><li>Unterstücklisten auflösen: Die Unterstücklisten einer Stückliste in der Produktion werden aufgelöst und angezeigt</li><li>Unterstücklisten nicht auflösen: Die Unterstücklisten einer Stückliste in der Produktion werden nicht aufgelöst und angezeigt. Die Produktion beschränkt sich damit auf die oberste Stückliste</li><li>Enthält eine zu produzierende Stückliste ebenfalls Stücklisten, die noch produziert werden sollen, so wird mit dieser Einstellung für jede Stückliste und Unter-Stückliste ein eigener Produktionsauftrag angelegt.<br>Voraussetzung:<ul><li>Zu produzierende Stückliste ist als Produktionsartikel markiert (in den Stammdaten des Artikels einstellbar)</li><li>Die Stückliste wurde aus einem Auftrag heraus in eine Produktion überführt.</li></ul></li></ul>";
$tooltip['firmendaten']['edit']['disablecreateproductiononextern']="Für Artikel mit Einstellung \"Externe Produktion\" werden beim Weiterführen von Auftrag zu Produktion keine Produktionen angelegt. Ebenso nicht über die Produktion selbst \"Unterproduktionen auflösen undanlegen\".";
$tooltip['firmendaten']['edit']['auftragexplodieren_unterstuecklisten']="Unterstücklisten die Just-In-Time Stücklisten sind, werden über alle ebenen aufgelöst.<br>Funktionalität nur vorhanden, wenn die erste Ebene explodieren darf.<br>Dazu Haken setzen bei: Projekteinstellungen -> Just-In-Time Stücklisten auflösen/explodieren.";
$tooltip['firmendaten']['edit']['dienstleistungsartikel_nicht_zu_lieferschein']="Dienstleistungsartikel (Option in Artikel - Details) werden aus dem Auftrag nicht übernommen für den LS bei folgenden Aktionen: <ul><li>Manuelles Weiterführen des Auftrags zum Lieferschein</li><li>Autoversand des Auftrags mit automatischer Lieferschein-Erstellung</li></ul>";
$tooltip['firmendaten']['edit']['lagerbestand_in_auftragspositionen_anzeigen']="Der Lagerbestand, sowie die Anzahl der reservierten Artikel werden beim Einfügen von Positionen via Autocomplete im Tab Positionen im Auftrag und Lieferschein dargestellt.";
$tooltip['firmendaten']['edit']['group_sales'] = "Zeigt in der Vertriebsmitarbeiterauswahl in Belegen und dem großen Adressfilter nur die Adressen, die Mitglied dieser Gruppe sind.<br />Falls leer, werden alle Adressen angezeigt.";
$tooltip['firmendaten']['edit']['group_employee'] = "Zeigt in der Bearbeiter- / Innendienstauswahl in Belegen und dem großen Adressfilter nur die Adressen, die Mitglied dieser Gruppe sind.<br />Falls leer, werden alle Adressen angezeigt.";
$tooltip['firmendaten']['edit']['oneclickrelease'] = "Nach dem Klick auf Freigabe wird ohne Zwischenfrage der Beleg freigegeben.";
// System - Zusatzfelder Artikeltabelle
$tooltip['firmendaten']['edit']['#artikeltabellezusatz1']="Möglichkeit eine zusätzliche Spalte in der Artikel-Übersicht darzustellen.<br><br>Hinweis: Der Prozessstarter mit dem Parameter <strong>artikelcache</strong> muss laufen, damit die Werte in den Spalten aktualisiert werden.";
$tooltip['firmendaten']['edit']['#artikeltabellezusatz2']="Möglichkeit eine zusätzliche Spalte in der Artikel-Übersicht darzustellen.<br><br>Hinweis: Der Prozessstarter mit dem Parameter <strong>artikelcache</strong> muss laufen, damit die Werte in den Spalten aktualisiert werden.";
$tooltip['firmendaten']['edit']['#artikeltabellezusatz3']="Möglichkeit eine zusätzliche Spalte in der Artikel-Übersicht darzustellen.<br><br>Hinweis: Der Prozessstarter mit dem Parameter <strong>artikelcache</strong> muss laufen, damit die Werte in den Spalten aktualisiert werden.";
$tooltip['firmendaten']['edit']['#artikeltabellezusatz4']="Möglichkeit eine zusätzliche Spalte in der Artikel-Übersicht darzustellen.<br><br>Hinweis: Der Prozessstarter mit dem Parameter <strong>artikelcache</strong> muss laufen, damit die Werte in den Spalten aktualisiert werden.";
$tooltip['firmendaten']['edit']['#artikeltabellezusatz5']="Möglichkeit eine zusätzliche Spalte in der Artikel-Übersicht darzustellen.<br><br>Hinweis: Der Prozessstarter mit dem Parameter <strong>artikelcache</strong> muss laufen, damit die Werte in den Spalten aktualisiert werden.";
// System - Zusatzfelder Adresstabelle
$tooltip['firmendaten']['edit']['#adressetabellezusatz1']="Möglichkeit eine zusätzliche Spalte in der Adressen-Übersicht darzustellen.";
$tooltip['firmendaten']['edit']['#adressetabellezusatz2']="Möglichkeit eine zusätzliche Spalte in der Adressen-Übersicht darzustellen.";
$tooltip['firmendaten']['edit']['#adressetabellezusatz3']="Möglichkeit eine zusätzliche Spalte in der Adressen-Übersicht darzustellen.";
$tooltip['firmendaten']['edit']['#adressetabellezusatz4']="Möglichkeit eine zusätzliche Spalte in der Adressen-Übersicht darzustellen.";
$tooltip['firmendaten']['edit']['#adressetabellezusatz5']="Möglichkeit eine zusätzliche Spalte in der Adressen-Übersicht darzustellen.";
// System - Zeiterfassung
$tooltip['firmendaten']['edit']['zeiterfassung_anderemitarbeiter']="Zeigt die Option 'für anderen Mitarbeiter Zeit buchen' in der 'Zeiterfassung buchen' Oberfläche an, mit der Sie Zeiten auch für Kollegen erfassen können.";
$tooltip['firmendaten']['edit']['zeiterfassung_kommentar']="Zeigt ein zusätzliches Textfeld für interne Bemerkungen in der 'Zeiterfassung buchen' Oberfläche an.";
$tooltip['firmendaten']['edit']['zeiterfassung_ort']="Zeigt ein zusätzliches Textfeld für eine Ortsangabe in der 'Zeiterfassung buchen' Oberfläche an.";
$tooltip['firmendaten']['edit']['zeiterfassung_erweitert']="Zeigt folgende zusätzliche Optionen in der 'Zeiterfassung buchen' Oberfläche an: <ul><li>Kunde</li><li>Auftrag</li><li>Auftragsposition</li><li>Produktion (falls Modul vorhanden)</li><li>Serviceauftrag (falls Modul vorhanden)</li><li>Abrechnen Haken => Setzt Zeit zum Abrechnen</li></ul>";
$tooltip['firmendaten']['edit']['zeiterfassung_abrechnenvorausgewaehlt']="Der Haken für 'Abrechnen' wird in der 'Zeiterfassung buchen' Oberfläche standardmäßig gesetzt.";
$tooltip['firmendaten']['edit']['zeiterfassung_schliessen']="Wenn der Haken gesetzt ist, können Sie die Anzahl der Tage eingeben, wie viele Tage in der Vergangenheit eine gebuchte Zeit liegen darf.<br>Wenn ein Mitarbeiter versucht eine ältere Buchung zu tätigen, bekommt er daraufhin eine Fehlermeldung.<br>Um bestimmte Benutzer von dieser Regel auszunehmen können Sie dem Benutzer das Rechte 'bearbeitenerlauben' im Rechteblock 'Zeiterfassung' geben.";
$tooltip['firmendaten']['edit']['zeiterfassung_pflicht']="Wenn der Haken gesetzt ist und keine Zeiten am letzten Werkstag gebucht wurden, wird der Mitarbeiter beim Login auf die Zeiterfassungsoberfläche geleitet.";
// System - Adresse
$tooltip['firmendaten']['edit']['reihenfolge_zwischenspeicher']="Bestimmt die Reihenfolge und die Daten, die Sie mit dem 'Adresse in Zwischenspeicher' Button direkt in der Adresse in den Zwischenspeicher nehmen können. Mit Ctrl/CMD + V können Sie den Inhalt dann wieder ausgeben.";
$tooltip['firmendaten']['edit']['adresse_vorlage']="Hier können Sie eine Adresse angeben, die als Vorlage für neu angelegte Adressen gilt (nicht über die Importzentrale).<br>Es werden beim Neu-Anlegen einer Adresse die meisten Felder der Musteradresse unter Adresse => Details übernommen.";
// System - LDAP
$tooltip['firmendaten']['edit']['ldap_host']="Die URI um Ihren LDAP-Client zu erreichen";
$tooltip['firmendaten']['edit']['ldap_bindname']="Hauptgruppe der Nutzer, die sich einloggen können";
$tooltip['firmendaten']['edit']['ldap_searchbase']="Definiert, wo im Verzeichnisbaum abwärts die Suche nach bestimmten Objekten gestartet werden soll.";
$tooltip['firmendaten']['edit']['ldap_filter']="Innerhalb der Verzeichnisse kann nach hinterlegten Daten gefiltert werden. Z.B. kann man prüfen ob der Benutzer der passenden Gruppe angehört und ob es sich überhaupt um eine Person handelt. (Die Angabe des LDAP Filters ist Pflicht sonst klappt die Authentifzierung nicht)";
// System - DSGVO Einstellungen
$tooltip['firmendaten']['edit']['dsgvoversandunternehmen']="Die Felder E-Mail und Telefon werden nicht dem Versanddienstleister mitgegeben.<br>Beim Erstellen einer Paketmarke werden die Felder mit der E-Mail Adresse ausgefüllt aber nicht übermittelt.";
// System - Sicherheit
$tooltip['firmendaten']['edit']['externeurlsblockieren']="Im Ticketsystem werden keine externen URLs geladen um Angriffen vorzubeugen.";
$tooltip['firmendaten']['edit']['additionalcspheader']="Zusätzliche CSP Header um die Einbindung externer Inhalte zu erlauben.";
// System - System E-Mails
$tooltip['firmendaten']['edit']['systemmailsabschalten']="Systeminterne E-Mails zu Lagersync Statusmeldung, etc. werden nicht mehr ausgeführt.";
$tooltip['firmendaten']['edit']['systemmailsempfaenger']="E-Mail Adresse, an die die System E-Mails gehen sollen.<br>Die Option drüber zum Abschalten der System-E-Mails darf natürlich nicht aktiviert sein.";
// System - Beschleunigung / Limitierungen
$tooltip['firmendaten']['edit']['begrenzen_belege']="=>Schaltet sich automatisch ein, wenn es mehr als 5000 Belege + 1 Sekunde Abfragedauer ODER mehr als 10000 Belege in einer Belegübersicht sind.<br>=>Begrenzt auf doppelte Anzahl von Anzeige (Seite 10) - ist nur bei der Anzahl der gefunden Daten sowie Anzahl der Seiten unterhalb der Live-Tabelle wichtig<br>=>Für die Performance bei großen Datenmengen ist es von Vorteil die Option zu aktivieren<br>=>Es werden bei der Suche trotzdem alle Belege berücksichtigt - die Suche läuft nur gestaffelt ab.";
$tooltip['firmendaten']['edit']['begrenzen_artikeltabelle']="=> Schaltet sich automatisch ein, wenn es mehr als 5000 Artikel + 1 Sekunde Abfragedauer ODER mehr als 10000 Artikel sind<br>=> Wenn man die Option einmal rausnimmt, dann wird die Option nicht mehr automatisch gesetzt.";
$tooltip['firmendaten']['edit']['autoversand_maxauftraege']="=> Schaltet sich automatisch ein, wenn es mehr als 100 offene Aufträge sind, die für den Autoversand gecheckt werden müssen <br>=> Nimmt die ältesten Aufträge, prüft die ersten ältesten 100 Aufträge (für Autoversand Plus)
⇒ Wenn es deutlich mehr sind, sollte der Cronjob öfters laufen (autoversand_plus)<br>=> Standard sollte 100 eingetragen sein - 0 ist keine Begrenzung";
$tooltip['firmendaten']['edit']['versandmails_max']="Sendet die Trackingnummer über einen Prozessstarter an den Shop.<br>Dies ist sinnvoll wenn über einen Fulfiller mehrere Trackingnummern auf einmal geliefert werden und der Shop nicht so viele Requests verarbeiten kann. Bzw. wenn die Shop-API von sich aus langsam ist, muss beim Scannen der Paketmarke nicht lange gewartet werden, bis der Shop die Trackingnummer verarbeitet hat.<br><br>=> Schaltet sich automatisch ein, wenn es mehr als 100 Aufträge sind, die zurückgemeldet werden müssen.<br>=> Statusrückmeldung & Tracking an Shop<br>=> Wenn deutlich mehr Trackingnummern zurückgemeldet werden müssen, dann sollte der Cronjob öfters laufen (shop_rueckmeldungen)";
$tooltip['firmendaten']['edit']['schnellsuche']="Aktiviert sicht automatisch bei mehr als 5000 Artikel + 1 Sekunde Abfragedauer ODER mehr als 10000<br>=> Blendet im Artikel ein neues Suchfeld ein. In diesem Suchfeld muss mit Enter bestätigt werden für einen Suchbegriff, womit keine überflüssigen Such-Requests an die Datenbank geschickt werden und sich so keine Such-Requests anstauen, die die Suche verlangsamen.";
$tooltip['firmendaten']['edit']['schnellsuchecount']="Deaktiviert die Berechnung unterhalb der Übersichtstabellen, wie viele Ergebnisse von allen Artikel angezeigt werden.<br>Diese Berechnung kann bei vielen Daten in der Tabelle zu erheblichen Ladezeiten führen und sollte deswegen bei mehreren Hundertausend Daten in einer Tabelle (z.B. Artikel) mit dem Haken deaktiviert werden.";

//API
$tooltip['firmendaten']['edit']['']="Wenn die API aktiv ist m&uuml;ssen alle Etikettendrucker &uuml;ber die API angesprochen werden.";

/* GEFAHRGUT */

$tooltip['gefahrgut']['list']['#e_befoerderungskategorie']="Die Beförderungskategorie bestimmt den Faktor mit dem die Gefahrgutpunkte für diesen Artikel berechnet werden.";


/* GESCHAEFTSBRIEF_VORLAGEN */

$tooltip['geschaeftsbrief_vorlagen']['list']['#subjekt']="Typ des Belegs, für welchen die Geschäftsbriefvorlage verwendet werden soll.<br><br>Der Typ muss immer genau eindeutig sein und darf nicht anders heißen als in den Standard-Vorlagen voreingestellt.
Zu verwenden ist also z.B. Angebot, Auftrag, Bestellung, Lieferschein, Rechnung etc.";


/* GRUPPEN */

$tooltip['gruppen']['create']['#art']="Bei Auswahl der Preisgruppe bekommen Sie weitere Einstellungsmöglichkeiten bezüglich Rabatt usw.";
$tooltip['gruppen']['edit']['#art']="Bei Auswahl der Preisgruppe bekommen Sie weitere Einstellungsmöglichkeiten bezüglich Rabatt usw.";


/* GUTSCHRIFT */

$tooltip['gutschrift']['create']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";
$tooltip['gutschrift']['edit']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";
$tooltip['gutschrift']['create']['#kurs']="Der aktuelle Wechselkurs kann in der App 'Währung Umrechnung' von der European Central Bank abgerufen werden.";
$tooltip['gutschrift']['edit']['#kurs']=$tooltip['gutschrift']['create']['#kurs'];


/* KONTEN */

$tooltip['konten']['create']['#bezeichnung']="Interne Bezeichnung für das Konto. Diese Bezeichnung kann beliebig gewählt werden und wird an verschiedenen Stellen in der Oberfläche angezeigt";
$tooltip['konten']['edit']['#bezeichnung']="Interne Bezeichnung für das Konto. Diese Bezeichnung kann beliebig gewählt werden und wird an verschiedenen Stellen in der Oberfläche angezeigt";
$tooltip['konten']['create']['#type']="Typ des Kontos (Bankkonto: Konto (CSV-Import), PayPal-Konto, Kreditkartenanbieter, Kasse etc.)";
$tooltip['konten']['edit']['#type']="Typ des Kontos (Bankkonto: Konto (CSV-Import), PayPal-Konto, Kreditkartenanbieter, Kasse etc.)";
$tooltip['konten']['create']['#projekt']="Optional - Zugriff auf Konto kann auf ein Projekt beschränkt werden (Rolle bei Mitarbeiter)";
$tooltip['konten']['edit']['#projekt']="Optional - Zugriff auf Konto kann auf ein Projekt beschränkt werden (Rolle bei Mitarbeiter)";
$tooltip['konten']['create']['#aktiv']="Dieses Konto ist aktiv (in Verwendung) oder inaktiv (stillgelegt)";
$tooltip['konten']['edit']['#aktiv']="Dieses Konto ist aktiv (in Verwendung) oder inaktiv (stillgelegt)";
$tooltip['konten']['create']['#keineemail']="Der Kunde erhält keine automatische E-Mail-Benachrichtigung über den Eingang seiner Zahlung";
$tooltip['konten']['edit']['#keineemail']="Der Kunde erhält keine automatische E-Mail-Benachrichtigung über den Eingang seiner Zahlung";
$tooltip['konten']['create']['#schreibbar']="Es dürfen Änderungen in den Kontoauszügen vorgenommen werden (nicht empfohlen, wird nur benötigt, wenn die Kontoauszüge von Hand eingegeben werden)";
$tooltip['konten']['edit']['#schreibbar']="Es dürfen Änderungen in den Kontoauszügen vorgenommen werden (nicht empfohlen, wird nur benötigt, wenn die Kontoauszüge von Hand eingegeben werden)";

$tooltip['konten']['create']['#inhaber']="Kontoinhaber für dieses Konto (keine Sonderzeichen, wenn der Sepa Zahlungsverkehr via XML verwendet wird)";
$tooltip['konten']['edit']['#inhaber']="Kontoinhaber für dieses Konto (keine Sonderzeichen, wenn der Sepa Zahlungsverkehr via XML verwendet wird)";
$tooltip['konten']['create']['#swift']="BIC für dieses Konto (bei Bankkonten Pflichtangabe für Lastschrifteinzug und Sepa-Sammelüberweisung)";
$tooltip['konten']['edit']['#swift']="BIC für dieses Konto (bei Bankkonten Pflichtangabe für Lastschrifteinzug und Sepa-Sammelüberweisung)";
$tooltip['konten']['create']['#iban']="IBAN für dieses Konto (bei Bankkonten Pflichtangabe für Lastschrifteinzug und Sepa-Sammelüberweisung)";
$tooltip['konten']['edit']['#iban']="IBAN für dieses Konto (bei Bankkonten Pflichtangabe für Lastschrifteinzug und Sepa-Sammelüberweisung)";
$tooltip['konten']['create']['#blz']="Bankleitzahl für dieses Konto";
$tooltip['konten']['edit']['#blz']="Bankleitzahl für dieses Konto";
$tooltip['konten']['create']['#konto']="Kontonummer für dieses Konto";
$tooltip['konten']['edit']['#konto']="Kontonummer für dieses Konto";
$tooltip['konten']['create']['#glaeubiger']="Gläubiger ID für Bankkonten (bei Bankkonten Pflichtangabe für Lastschrifteinzug)";
$tooltip['konten']['edit']['#glaeubiger']="Gläubiger ID für Bankkonten (bei Bankkonten Pflichtangabe für Lastschrifteinzug)";
$tooltip['konten']['create']['#lastschrift']="Haken setzen um den Lastschrifteinzug zu aktivieren";
$tooltip['konten']['edit']['#lastschrift']="Haken setzen um den Lastschrifteinzug zu aktivieren";
$tooltip['konten']['create']['#hbci']="Nur in alten Versionen noch vorhanden";
$tooltip['konten']['edit']['#hbci']="Nur in alten Versionen noch vorhanden";
$tooltip['konten']['create']['#hbcikennung']="Nur in alten Versionen noch vorhanden";
$tooltip['konten']['edit']['#hbcikennung']="Nur in alten Versionen noch vorhanden";

$tooltip['konten']['create']['#datevkonto']="Nummer des Kontenrahmens für das Bankkonto (für den Export der Auszüge); je nach Kontenrahmen z.B. 1800, 1700 etc";
$tooltip['konten']['edit']['#datevkonto']="Nummer des Kontenrahmens für das Bankkonto (für den Export der Auszüge); je nach Kontenrahmen z.B. 1800, 1700 etc";

$tooltip['konten']['create']['#importerstezeilenummer']="Erste Zeilennummer in der echte Daten stehen (Bsp. für Daten ab Zeile 1: „1“). Sofern es eine Beschriftungszeile gibt, ist die erste richtige Datenzeile meist Zeile 2 (anzugeben dann „2“)";
$tooltip['konten']['edit']['#importerstezeilenummer']="Erste Zeilennummer in der echte Daten stehen (Bsp. für Daten ab Zeile 1: „1“). Sofern es eine Beschriftungszeile gibt, ist die erste richtige Datenzeile meist Zeile 2 (anzugeben dann „2“)";
$tooltip['konten']['create']['#codierung']="Im Standard auf UTF8 Encode stellen, um die Umlaute korrekt zu übertragen";
$tooltip['konten']['edit']['#codierung']="Im Standard auf UTF8 Encode stellen, um die Umlaute korrekt zu übertragen";
$tooltip['konten']['create']['#importtrennzeichen']="Strichpunkt oder Komma. Das Trennzeichen ist in der CSV Datei über einen Texteditor ersichtlich";
$tooltip['konten']['edit']['#importtrennzeichen']="Strichpunkt oder Komma. Das Trennzeichen ist in der CSV Datei über einen Texteditor ersichtlich";
$tooltip['konten']['create']['#importdatenmaskierung']="Anführungszeichen oder keine Maskierung. Die Maskierung ist in der CSV Datei über einen Texteditor ersichtlich";
$tooltip['konten']['edit']['#importdatenmaskierung']="Anführungszeichen oder keine Maskierung. Die Maskierung ist in der CSV Datei über einen Texteditor ersichtlich";
$tooltip['konten']['create']['#importletztenzeilenignorieren']="Im Standard eine „0“ eintragen, es sei denn der Kontoauszug enthält am Ende weitere Informationszeilen, die nicht mehr zu den Einzelbuchungen gehören. Hierzu als Zahl angeben, wieviele Zeilen am Ende ignoriert werden sollen";
$tooltip['konten']['edit']['#importletztenzeilenignorieren']="Im Standard eine „0“ eintragen, es sei denn der Kontoauszug enthält am Ende weitere Informationszeilen, die nicht mehr zu den Einzelbuchungen gehören. Hierzu als Zahl angeben, wieviele Zeilen am Ende ignoriert werden sollen";
$tooltip['konten']['create']['#importfelddatum']="Spalte, in der das Datum steht";
$tooltip['konten']['edit']['#importfelddatum']="Spalte, in der das Datum steht";
$tooltip['konten']['create']['#importfelddatumformat']="Format, wie es für Xentral ausgegeben wird (im Standard: %3-%2-%1)
–»» Bsp. für Eingabe und Ausgabe: 24.12.2016 in CSV entspricht Eingabeformat %1.%2.%3 und Ausgabeformat %3-%2-%1 (Ausgabe muss immer auf diese Format gebracht werden YYYY-MM-DD). Weiteres Beispiel: Ist das Eingabeformat folgendermaßen: 08/11/2016 11:12:45 wird dieses mit Variablen so dargestellt: %1/%2/%3 %4:%5:%6. Das Ausgabeformat bleibt %3-%2-%1 (die Uhrzeit wird in der Ausgabe abgeschnitten)
";
$tooltip['konten']['edit']['#importfelddatumformat']="Format, wie es für Xentral ausgegeben wird (im Standard: %3-%2-%1)
–»» Bsp. für Eingabe und Ausgabe: 24.12.2016 in CSV entspricht Eingabeformat %1.%2.%3 und Ausgabeformat %3-%2-%1 (Ausgabe muss immer auf diese Format gebracht werden YYYY-MM-DD). Weiteres Beispiel: Ist das Eingabeformat folgendermaßen: 08/11/2016 11:12:45 wird dieses mit Variablen so dargestellt: %1/%2/%3 %4:%5:%6. Das Ausgabeformat bleibt %3-%2-%1 (die Uhrzeit wird in der Ausgabe abgeschnitten)
";
$tooltip['konten']['create']['#importfelddatumformatausgabe']=" Format, wie das Datum in Ihrer CSV-Datei angegeben ist (Meist Datum YYYY-MM-DD dann: %3-%2-%1)";
$tooltip['konten']['edit']['#importfelddatumformatausgabe']=" Format, wie das Datum in Ihrer CSV-Datei angegeben ist (Meist Datum YYYY-MM-DD dann: %3-%2-%1)";

$tooltip['konten']['create']['#importfeldbetrag']="Spalte, in der der Betrag steht (nur dieses Feld, wenn der Betrag in einer Spalte steht)";
$tooltip['konten']['edit']['#importfeldbetrag']="Spalte, in der der Betrag steht (nur dieses Feld, wenn der Betrag in einer Spalte steht)";
$tooltip['konten']['create']['#importextrahabensoll']="Wird benötigt (Haken setzen und Spalte für Haben und Soll angeben), sofern der Betrag auf 2 Spalten aufgeteilt ist nach Haben und Soll";
$tooltip['konten']['edit']['#importextrahabensoll']="Wird benötigt (Haken setzen und Spalte für Haben und Soll angeben), sofern der Betrag auf 2 Spalten aufgeteilt ist nach Haben und Soll";
$tooltip['konten']['create']['#importfeldhaben']="Spalte, in der der Betrag für HABEN steht (Voraussetzung: Haken bei „Extra Haben u. Soll“ gesetzt)";
$tooltip['konten']['edit']['#importfeldhaben']="Spalte, in der der Betrag für HABEN steht (Voraussetzung: Haken bei „Extra Haben u. Soll“ gesetzt)";
$tooltip['konten']['create']['#importfeldsoll']="Spalte, in der der Betrag für SOLL steht (Voraussetzung: Haken bei „Extra Haben u. Soll“ gesetzt)";
$tooltip['konten']['edit']['#importfeldsoll']="Spalte, in der der Betrag für SOLL steht (Voraussetzung: Haken bei „Extra Haben u. Soll“ gesetzt)";
$tooltip['konten']['create']['#importfeldbuchungstext']="Spalte(n), in der der Buchungstext steht. Besonderheit: für den Buchungstext können mehrere Spalten zusammengesetzt werden. Beispiel: 5+11+3+8+12+18+14. Bitte beachten: relevanteste Informationen für den Buchhaltungsexport nach vorne stellen, denn hier werden oft zu lange Texte abgeschnitten. Um möglichst viele relevante Informationen in eine separate Buchhaltungssoftware (z.B. Datev) zu importieren empfiehlt sich z.B. folgende Reihenfolge: Absender Name + Buchungstext + Referenznummer + Lastschriftinformationen + Sonstige Felder + … + …";
$tooltip['konten']['edit']['#importfeldbuchungstext']="Spalte(n), in der der Buchungstext steht. Besonderheit: für den Buchungstext können mehrere Spalten zusammengesetzt werden. Beispiel: 5+11+3+8+12+18+14. Bitte beachten: relevanteste Informationen für den Buchhaltungsexport nach vorne stellen, denn hier werden oft zu lange Texte abgeschnitten. Um möglichst viele relevante Informationen in eine separate Buchhaltungssoftware (z.B. Datev) zu importieren empfiehlt sich z.B. folgende Reihenfolge: Absender Name + Buchungstext + Referenznummer + Lastschriftinformationen + Sonstige Felder + … + …";
$tooltip['konten']['create']['#importfeldwaehrung']="Spalte, in der die Währung steht";
$tooltip['konten']['edit']['#importfeldwaehrung']="Spalte, in der die Währung steht";
$tooltip['konten']['create']['#importfeldhabensollkennung']="Separate Spalte, die festlegt, ob der Betrag Haben oder Soll ist (z.B. eine extra Spalte, in der die Information 'H' oder 'S' zu finden ist)";
$tooltip['konten']['edit']['#importfeldhabensollkennung']="Separate Spalte, die festlegt, ob der Betrag Haben oder Soll ist (z.B. eine extra Spalte, in der die Information 'H' oder 'S' zu finden ist)";
$tooltip['konten']['create']['#importfeldkennunghaben']="Zeichen für 'Haben' z.B. 'H' oder '+'' (Voraussetzung: Spaltenangabe für 'Haben/Soll Kennung')";
$tooltip['konten']['edit']['#importfeldkennunghaben']="Zeichen für 'Haben' z.B. 'H' oder '+' (Voraussetzung: Spaltenangabe für 'Haben/Soll Kennung')";
$tooltip['konten']['create']['#importfeldkennungsoll']="Zeichen für 'Soll' z.B. 'S' oder '-' (Voraussetzung: Spaltenangabe für 'Haben/Soll Kennung')";
$tooltip['konten']['edit']['#importfeldkennungsoll']="Zeichen für 'Soll' z.B. 'S' oder '-' (Voraussetzung: Spaltenangabe für 'Haben/Soll Kennung')";

$tooltip['konten']['create']['#liveimport_online']="Haken aktiviert den Live-Import (Voraussetzung: korrekte Eingabe der API Daten bei 'Zugangsdaten Live-Import'";
$tooltip['konten']['edit']['#liveimport_online']="Haken aktiviert den Live-Import (Voraussetzung: korrekte Eingabe der API Daten bei 'Zugangsdaten Live-Import'";
$tooltip['konten']['create']['#liveimport']="API Code für den Live-Import";
$tooltip['konten']['edit']['#liveimport']="API Code für den Live-Import";


/* LAGER */

/* Details */
$tooltip['lager']['platzeditpopup']['abckategorie']="Das ist eine Klassifizierung für einen bestimmten Regalplatz.<br>Dies dient bisher nur als Information, wird später aber in diversen Ein/Auslager-Oberflächen als Hinweis angezeigt.";
$tooltip['lager']['platzeditpopup']['allowproduction'] = 'Sobald ein Lagerplatz diese Checkbox gesetzt hat, wird nur noch dieses Lager in der Produktion beim bevorzugten Lager angezeigt.<br />Wenn diese Checkbox bei keinem Lagerplatz gesetzt ist, werden alle Lager in der Produktion beim bevorzugten Lager angezeigt.';
$tooltip['lager']['platz']['allowproduction'] = 'Sobald ein Lagerplatz diese Checkbox gesetzt hat, wird nur noch dieses Lager in der Produktion beim bevorzugten Lager angezeigt.<br />Wenn diese Checkbox bei keinem Lagerplatz gesetzt ist, werden alle Lager in der Produktion beim bevorzugten Lager angezeigt.';

/* LIEFERSCHEIN */

/* Details - Lieferschein */
$tooltip['lieferschein']['create']['keinerechnung']="Die Option ist gedacht für Musterlieferungen.<br><br>Lieferscheine, die mit diesen Haken versehen sind, werden nicht aufgeführt, wenn die Filteroption 'ohne Rechnung' an folgenden Stellen gewählt wird: <ul><li>Lieferscheine Übersicht</li><li>Auftrags Übersicht</li><li>Sammelrechnung (extra Modul): Lieferschein erscheint nicht in Liste</li></ul>";
$tooltip['lieferschein']['edit']['keinerechnung']="Die Option ist gedacht für Musterlieferungen.<br><br>Lieferscheine, die mit diesen Haken versehen sind, werden nicht aufgeführt, wenn die Filteroption 'ohne Rechnung' an folgenden Stellen gewählt wird: <ul><li>Lieferscheine Übersicht</li><li>Auftrags Übersicht</li><li>Sammelrechnung (extra Modul): Lieferschein erscheint nicht in Liste</li></ul>";
$tooltip['lieferschein']['create']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";
$tooltip['lieferschein']['edit']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";

/* Paketmarke */
$tooltip['lieferschein']['paketmarke']['sofortabschliessen']="<ul><li>Paketmarken werden entsprechen der angegebenen Anzahl Pakete erstellt</li><li>Die dazugehörigen Trackingnummern werden automatisch abgespeichert</li><li>Es wird eine Versandmail verschickt, in der sich die Links und Trackingnummern für DPD befinden.</li></ul>";
$tooltip['lieferschein']['paketmarke']['wunschtermin']="Gibt auf der Paketmarke den Wunschtermin und Wunschzeit aus. <br><br>Ob der Wunschtermin angegeben werden kann, hängt vom Datum und vor allem von der Adresse des Empfängers ab. Hier wird überprüft, ob die Empfänger-Adresse am angegebenen Wunschtermin beliefert werden kann. Falls nicht, wird eine Fehlermeldung angezeigt - bitte hier dann DHL oder den Empfänger direkt kontaktieren oder ohne Wunschtermin absenden.";


/* MAHNWESEN */

$tooltip['mahnwesen']['list']['#neuberechnen']="Berechnet den Zahlungsstatus der Rechnungen und die Fälligkeiten der Mahnstufen neu.<br><br>Diese Berechnung kann man auch über einen Prozessstarter z.B. einmal am Tag im Hintergrund ausführen lassen.";
$tooltip['mahnwesen']['list']['#starten']="Führt für alle markierten Einträge das Mahnwesen mit der nächsten Stufe aus (= Status nach Mahnlauf).<br><br>Bei den Einstellungen kann hinterlegt werden, ob die Mahnung per E-Mail versendet wird. Hat die betreffende Rechnung in den Stammdaten oder in der Rechnung selbst keine E-Mail Adresse hinterlegt so wird die Mahnung am ausgewählten Drucker ausgegeben.<br><br>Ist die E-Mail Funktion nicht ausgewählt werden die Mahnungen ausgedruckt.";

/* Einstellungen */
$tooltip['mahnwesen']['mahnweseneinstellungen']['#mahnwesen_einst_zahlungerinnerung']="Fälligkeit ist fest => ist immer das berechnete Zahlungsziel (z.B. bei Rechnung 14 Tage ist die Fälligkeit für die Zahlungserinnerung am Tag 15)";
$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_ze_wenn_null_versand']="Fälligkeit wird in Tagen angegeben bei Rechnung zahlbar 'sofort' (entspricht '0' Tage) oder Vorkasse-Zahlungsweisen, die nicht automatisch auf bezahlt gesetzt werden";
$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_ze_lastschrift']="Fälligkeit der Zahlungsweise 'Lastschrift (oder 'verhält sich wie Lastschrift'); aufgerechnet wird das Einzugsdatum in der Rechnung. Bei Einzugsdauer von X Bankarbeitstagen kann z.B. dieser Wert eingestellt werden, dann tauchen die Lastschriften in der Regel nicht im Mahnwesen auf (nur wenn sie vergessen wurden einzuziehen oder wenn der Einzug nicht funktioniert hat oder es eine Rückbuchung gab)";
$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_m1_tage']="Fälligkeit wird in Tagen angegeben";
$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_m2_tage']="Fälligkeit wird in Tagen angegeben";
$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_m3_tage']="Fälligkeit wird in Tagen angegeben";
$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_ik_tage']="Fälligkeit wird in Tagen angegeben";

$tooltip['mahnwesen']['mahnweseneinstellungen']['#mahnwesen_einst_buchhaltung']="E-Mail Erinnerung an die Buchhaltung (Standard E-Mail aus den Grundeinstellungen)";
$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_zahlungserinnerungtage']="Tage, nach dem die Vorkasse E-Mail als Erinnerung an den Kunden gesendet werden soll (Auftrag)";
$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_klaerungsmailtage']="E-Mail, die an die Buchhaltung versendet wird (Standard E-Mail aus den Grundeinstellungen), so dass zu lange offene Vorkasse Aufträge storniert werden können";
$tooltip['mahnwesen']['mahnweseneinstellungen']['#mahnwesen_einst_schwelle']="EUR Betrag, ab dem die Zahlungsmail noch versendet werden soll an den Kunden (Auftrag)";

$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_skontoueberziehenerlauben']="Anzahl der Tage, um die die automatische Erkennung im Zahlungseingang noch greifen soll (Skontozeitraum im Zahlungseingang zusätzlich um x Tage verlängern)";
$tooltip['mahnwesen']['mahnweseneinstellungen']['mahnwesen_bearbeiter_anzeigen']="Bearbeiter und Vertreib in der Infobox auf dem Mahndokument anzeigen";

/* MITARBEITERZEITERFASSUNG */

/* Setting */
$tooltip['mitarbeiterzeiterfassung']['settings']['logouttime']="Dadurch werden alle Mitarbeiter in der Stechuhr nach x Sekunden automatisch ausgeloggt, wenn Sie in der Zeit nichts in der Stechuhr-Oberfläche klicken";

/* Mitarbeiter - Einstellungen */
$tooltip['mitarbeiterzeiterfassung']['einstellungen']['pauseaddieren']="Ist der Haken aktiviert, werden manuell gebuchte Pausen auf die Pausenzeit der automatisch abgezogenen Pause addiert. <br><br>Bsp: 6 Std Arbeitszeit mit 30 Min automatisch abgezogener Pause + 10 Min manueller Pause => 40 Min Pause und 5:20 IST-Zeit";



/* ONLINESHOPS */

/* Details - Schnittstelle */

/* Amazon */
$tooltip['onlineshops']['edit']['#keinerechnung']="Falls externer Dienstleister wie amainvoice verwendet wird.";
$tooltip['onlineshops']['edit']['#FBAprojekt']="Falls leer, wird obiges Projekt bzw. Projekte aus Mapping verwendet.";
$tooltip['onlineshops']['edit']['#FBMprojekt']="Falls leer, wird obiges Projekt bzw. Projekte aus Mapping verwendet.";

/* Magento 1.9 */
$tooltip['onlineshops']['edit']['statusoffenueberschreiben']="Status der offenen Aufträge im Shop, die Xentral beim Auftragsimport abholen soll.<br>Mit Semikolon (;) kann man mehrere Status angeben.";
$tooltip['onlineshops']['edit']['statusinbearbeitungueberschreiben']="Setzt diesen Status für die Aufträge im Shop, die Xentral abgeholt hat.";

/* Magento 2 */
$tooltip['onlineshops']['edit']['statusoffenueberschreiben']="Status der offenen Aufträge im Shop, die Xentral beim Auftragsimport abholen soll.<br>Mit Semikolon (;) kann man mehrere Status angeben.";
$tooltip['onlineshops']['edit']['statusinbearbeitungueberschreiben']="Setzt diesen Status für die Aufträge im Shop, die Xentral abgeholt hat.";

/* Oxid */
$tooltip['onlineshops']['edit']['#oxidusername']="Administrator Benutzername";
$tooltip['onlineshops']['edit']['#oxidpassword']="Administrator Passwort";
$tooltip['onlineshops']['edit']['#oxidurl']="Shop URL + /oxerpservice.php?wsdl&version=2.15.0 also z.B. www.shop.de/oxerpservice.php?wsdl&version=2.15.0";

/* Shopware */
$tooltip['onlineshops']['edit']['#ImportShopwareApiUrl']="Die API-URL setzt sich aus Ihrer Shopdomain/api zusammen. Bsp: http://www.ihredomain.de/api. Beachten Sie, ob die URL des Shops http:// oder https:// besitzt.";
$tooltip['onlineshops']['edit']['#archiv_von']="Älteste Belegnummer im Shop von der die Aufträge abgeholt werden sollen (inkl). Wenn leer gelassen, dann wird ab dem ältesten abgeholt.";
$tooltip['onlineshops']['edit']['#archiv_bis']="Letzte Belegnummer im Shop bis zu der die Aufträge abgeholt werden sollen (inkl). Wenn leer gelassen, dann wird bis zum neuesten abgeholt.";
$tooltip['onlineshops']['edit']['#archiv_aufrag_abschliessen']="Der importierte Auftrag wird auf Status „abgeschlossen“ gesetzt.";
$tooltip['onlineshops']['edit']['#archiv_rechnung_erzeugen']="Es wird zu dem importierten Auftrag eine Rechnung erzeugt.";
$tooltip['onlineshops']['edit']['#archiv_rechnung_bezahlt']="Die erzeugte Rechnung wird auf bezahlt gesetzt. („Rechnung erzeugen“ muss angehakt sein)";
$tooltip['onlineshops']['edit']['#archiv_storniert_abholen']="Holt auch die Aufträge ab, die im Shop storniert sind.";

/* Gambio */
$tooltip['onlineshops']['edit']['#ImportUser']="Hier muss der Nutzer eingetragen werden, der Zugriff auf die API hat. Meistens ein Administrator";
$tooltip['onlineshops']['edit']['#ImportPass']="Hier muss das entsprechende Passwort für den API Nutzer eingetragen werden";
$tooltip['onlineshops']['edit']['#ImportURL']="ihreshopurl/api.php/v2";
$tooltip['onlineshops']['edit']['#ImportKategorien']="Relevant für Artikelexport Xentral -> Shop. Wenn nicht gesetzt wird eine Sammelkategorie in Gambio verwendet.";
$tooltip['onlineshops']['edit']['#Steuerklassenormal']="Steuerklassen gemäß Gambio Einstellung";
$tooltip['onlineshops']['edit']['#Steuerklasseermaessigt']="Steuerklassen gemäß Gambio Einstellung";
$tooltip['onlineshops']['edit']['#Steuerklassebefreit']="Steuerklassen gemäß Gambio Einstellung";
$tooltip['onlineshops']['edit']['#NameOffen']="Der Name des Status im Shop, um offene Bestellungen darzustellen.";
$tooltip['onlineshops']['edit']['#IDBearbeitung']="Hier ist die ID einzutragen, auf welche Gambio die Bestellung in den Status „in Bearbeitung“ setzt";
$tooltip['onlineshops']['edit']['#IDAbgeschlossen']="Hier ist die ID einzutragen, auf welche Gambio die Bestellung in den Status „abgeschlossen“ setzt";

/* Shopify */
$tooltip['onlineshops']['edit']['#shopifytracking']="E-Mail Funktion für Trackingmails kann über die Shopify API deaktiviert werden. Dieser Haken aktiviert die Mail Funktion in Shopify (ist im Standard deaktivert, hierzu muss die Tracking E-Mail in Xentral aktiviert werden)";

/* ePages */
$tooltip['onlineshops']['edit']['#lastschriftdatenueberschreiben'] = "Achtung: Nur für ePages verfügbar.<br />Wenn im Warenkorb Daten zur Bankverbindung übergeben werden, dann werden die Daten bei der zugehörigen Adresse überschrieben.";

/* Details - Einstellungen */
/* ab hier fuer kundenspezifsche Importer */
$tooltip['onlineshops']['edit']['#stornoabgleich']="Funktioniert für diese aktuellen Shopschnittstellen: <ul><li>Woocommerce</li><li>Shopware</li><li>Shopify</li></ul> <br>Wird ein Auftrag in Xentral storniert, wird automatisch der Auftrag im Shop ebenfalls storniert.";
$tooltip['onlineshops']['edit']['#gesamtbetragfestsetzen']="Diese Einstellung legt fest, dass beim Auftragsimport immer der Gesamtbetrag, den der Shop übermittelt festgeschrieben und verwendet werden soll.<br><br>Xentral rechnet den Gesamtbetrag dann nicht mehr neu aus den Preisen der einzelnen Auftragspositionen aus, sondern verwendet den Gesamtbetrag des Shops.<br><br>Das kann z.B. hilfreich sein, wenn der Shop mit weniger Nachkommastellen als Xentral rechnet und so durch die Mehrwertsteuer Rundungsfehler (meistens genau 1 Cent) entstehen.";
$tooltip['onlineshops']['edit']['#nurneueartikel']="Betrifft die Option in der Shopschnittstelle 'Artikelliste abholen'
=> Dadurch werden nur neue Artikel angelegt (Nummer, Fremdnummer, etc. werden wieder gecheckt), aber keine Artikel geupdatet.";
$tooltip['onlineshops']['edit']['#useorderid']="Diese Option kann nötig sein, wenn man weder nach Status noch nach Bestellnummer die Aufträge importieren kann.<br><br>Ein Grund dafür könnte sein, dass die Bestellnummer von numerisch auf alphanumerisch wechselt - dann können Sie diese Option hier anhaken.";
$tooltip['onlineshops']['edit']['#sendonlywithtracking'] = 'Automatisches Zurückmelden von abgeschlossenen Aufträgen deaktivieren falls keine Trackingnummern vorhanden sind';
/* Artikel Import / Export */
$tooltip['onlineshops']['edit']['#ueberschreibe_lagerkorrekturwert']='Lagerkorrekturwert wird global für alle Artikel im Shop festgelegt und muss nicht mehr pro Artikel im Artikelstamm gepflegt werden.<br>Der eingestellte Lagerkorrekturwert im Artikelstamm einzelner Artikel wird für diesen Shop von diesem Wert überschrieben.';

$tooltip['onlineshops']['edit']['#artikelrabatt']="Artikel Nr., auf die der Rabatt gebucht wird.";
$tooltip['onlineshops']['edit']['#artikelrabattsteuer']="Leerlassen für Steuersatz aus Artikel.";

$tooltip['onlineshops']['edit']['#modulename']="Diese Funktion wird verwendet wenn Sie Ihren Shopanbieter wechseln oder den Shop aktualisieren.<br>Z.B. ein alter Gambio Shop wurde aktualisiert und kann nur noch per API angesprochen werden.";

/* Adressen Import */
$tooltip['onlineshops']['edit']['#kundenurvonprojekt']='Wird beim Import aus dem Shop ein Kunde anhand seiner E-Mail-Adresse erkannt, wird der Auftrag wieder auf ihn verknüpft.<br>Soll das nur innerhalb eines Projektes geschehen, muss diese Option aktiviert werden.<br>Dann wird ein Kunde, dessen Auftrag anhand der E-Mail-Adresse zugeordnet werden kann, der aber zu einem fremden Projekt gehört, neu im Adressstamm angelegt.';
$tooltip['onlineshops']['edit']['#adressennichtueberschreiben']='Im Adressstamm werden bei bestehende Adressen keine Daten durch einen Auftragsimport aus dem Shop überschrieben, wenn diese Option aktiviert ist. Andernfalls erfolgt durch den Auftragsimport eine automatische Aktualisierung der Kundendaten im Adressstamm.';

/* Details - Zahlweise Mapping */
$tooltip['onlineshops']['edit']['#b_vorabbezahltmarkieren']="Das Ampelicon für 'Zahlung Ok (€)' im Auftrag wird automatisch auf grün gestellt.";
$tooltip['onlineshops']['edit']['#fastlane']="Dient als Markierung für Aufträge mit Prio.<br><br>Diese Aufträge erhalten dann in den Übersichten ein '(FL)' hinter der Auftragsnummern und können in der Auftrags-Übersicht, im Auftrags-Versandzentrum und im Lager-Versandzentrum gefiltert werden.";

/* Details - Smarty */
$tooltip['onlineshops']['edit']['#transferactive']="Um Smarty für eingehende Aufträge zu nutzen, muss diese Option aktiviert werden.";
$tooltip['onlineshops']['edit']['#smartyinputtype']="Format für das zu erstellende Smarty Template.";
$tooltip['onlineshops']['edit']['#replacecart']="Wenn diese Option aktiviert ist, wird die von xentral im Hintergrund vorbereitete Warenkorbstruktur durch das Ergebnis von Smarty ersetzt. Ist dies nicht der Fall, werden nur die gefundenen Knoten ersetzt und alles andere vom originalen Importer verwendet.";


/* PREISANFRAGE */

$tooltip['preisanfrage']['edit']['#zusammenfassen']="Dadurch werden Positionen auf dem Beleg zusammengefasst, die der selbe Artikel sind und gleichzeitig direkt von der Reihenfolge aufeinander folgen.<br><ul><li>Die Positionsnummer erhöht sich nicht</li><li>Die Artikelnummer wird nur bei der ersten Position angezeigt</li><li>Die Artikelbeschreibung wird nur bei der ersten Position angezeigt</li></ul>";


/* Produktion */
$tooltip['produktion']['abschluss']['#auftragmengenanpassen']="Die Menge im zugeh&ouml;rigen Auftrag wird an die Menge erfolgreich angepasst.";
$tooltip['produktionszentrum']['abschluss']['#auftragmengenanpassen']="Die Menge im zugeh&ouml;rigen Auftrag wird an die Menge erfolgreich angepasst.";

$tooltip['produktion']['abschluss']['#mengeerfolgreich'] = 'Höhere Mengen als die geplante Menge können nur mit der deaktivierten (kein Haken setzen) Systemeinstellung "Produktionskorrektur nicht verwenden" verbucht werden. ';
$tooltip['produktion']['edit']['#mengeerfolgreich'] = $tooltip['produktion']['abschluss']['#mengeerfolgreich'];
$tooltip['produktion']['create']['#standardlager'] = "Lager, aus dem die Artikel für die Produktion ausgelagert werden sollen.Hier können alle Lager ausgewählt werden, in denen sich mindestens ein Lagerplatz befindet, aus dem Produktionen ausgelagert werden dürfen (Einstellung auf Regalebene unter Lager => Lagerverwaltung).";
$tooltip['produktion']['edit']['#standardlager'] = $tooltip['produktion']['create']['#standardlager'];


/* PROJEKT */

$tooltip['projekt']['uebersicht']['abkuerzung']="In GROSSBUCHSTABEN oder Nummern.";

$tooltip['projekt']['create']['#projektanlegenbestehend']="Hinweis: Bitte denken Sie daran, die Nummernkreise Ihres Projekts anzupassen, falls Sie ein Projekt mit eigenen Nummernkreisen kopieren möchten!";

/* Einstellungen - Grundeinstellungen */
$tooltip['projekt']['edit']['verkaufszahlendiagram']="Anzeige in Verkaufszahlendiagramm.";
$tooltip['projekt']['edit']['oeffentlich']="&Ouml;ffentlich f&uuml;r alle Mitarbeiter.";
$tooltip['projekt']['create']['#zahlungsmailbedinungen']="Optionale Bedingungen durch Programmierung erweiterbar.";
$tooltip['projekt']['edit']['#zahlungsmailbedinungen']="Optionale Bedingungen durch Programmierung erweiterbar.";
$tooltip['projekt']['edit']['stornomail']="Bei Stornierung E-Mail Stornierung an Kunden.";
$tooltip['projekt']['create']['speziallieferschein']="Das Briefpapier als PDF für dieses Projekt bei dem Tab \"Dateien\" als \"Briefpapier Seite 1\" und \"Briefpapier Seite 2\" hochladen.";
$tooltip['projekt']['edit']['speziallieferschein']="Das Briefpapier als PDF für dieses Projekt bei dem Tab \"Dateien\" als \"Briefpapier Seite 1\" und \"Briefpapier Seite 2\" hochladen.";
$tooltip['projekt']['edit']['speziallieferscheinbeschriftung']="Mit Beschriftung Header und Footer wie bei Firmendaten.";
/* Einstellungen - Logistik/Versand */
/* Versandprozess und Kommissionierung */
$tooltip['projekt']['edit']['#lagerplatzlieferscheinausblenden']='Bei Kommissionierverfahren mit dem Schlagwort "Lieferschein mit Lagerplatz" werden die Lagerplätze der Artikel standardmäßig in der Positionstabelle des Lieferscheins mit angezeigt.<br>Bei allen anderen Kommissionierverfahren wird der Lagerplatz nie auf dem Lieferschein angezeigt.<br>Wird diese Einstellung aktiviert, so werden Lagerplätze in diesem Projekt auch bei Kommissionierverfahren mit "Lieferschein mit Lagerplatz" nicht mehr auf dem Lieferschein angezeigt.';
$tooltip['projekt']['edit']['#kommissionierlauflieferschein']="Diese Option verändert das Verhalten unter Lager => Artikel für Lieferungen bei einem Kommissionierlauf mit mehreren Lieferscheinen.<br><br>Ist der Haken aktiviert, wird die Tabelle im ausgewählten Kommissionierlauf aufgeteilt auf die unterschiedlichen Lieferscheine. Gibt es also Lieferscheine mit dem selben Artikel, wird dieser Artikel aufgesplittet in einzelne Zeilen und nicht kumuliert in 1 Zeile dargestellt.";
$tooltip['projekt']['edit']['#multiorderpicking']='Der Kommissionierlauf mit Multi-Order-Picking ist aktuell noch nicht für Artikel mit Chargen, Mindesthaltbarkeitsdaten oder Seriennummern geeignet, da er nicht auf den Scan im Versandzentrum zurückgreift bei dem diese aus dem Lager ausgebucht werden.';
$tooltip['projekt']['edit']['#druckennachtracking']="In Verbindung mit dem Haken „Rechnung erst im Versandprozess erzeugen“ aus der Stufe Kommissionierung werden die Trackingnummern auf der Rechnung abgebildet, sobald die Paketmarke gescannt wird.";
$tooltip['projekt']['edit']['#orderpicking_sort']='Art der Sortierung der jeweilgen Lagerplätze. Der erste gezogene Lagerplatz jeder Position ist für die Sortierung ausschlaggebend. Es findet kein Aufsplitten der einzelnen Positionen statt.';

/* Stufe (Kommissionierung) */
$tooltip['projekt']['edit']['#exportdruckrechnungstufe1']="Ausdruck von zusätzlichen Belegen, wenn der Beleg auf dem Steuerstatus 'Export' steht.";
$tooltip['projekt']['edit']['#kommissionierlistestufe1']="Ausdruck der Pickliste (Kommissionierlauf).<br>Diese Liste wird erzeugt, wenn man den Autoversand ausführt und kann auch unter Lager => Artikel für Lieferungen aufgerufen werden.";
$tooltip['projekt']['edit']['#rechnungerzeugen']='Die Rechnung zu einem Auftrag wird erst erstellt, wenn man diesen unter Lager => Versandzentrum verarbeitet. Das ist z.B. sinnvoll, wenn man die Rechnung beim Füllen das Pakets hineinlegen will und nicht schon vorher irgendwo ausdrucken oder verschicken.<br><br>Achtung:<br>Ist diese Einstellung gesetzt, wird nur eine Rechnung erzeugt, wenn der dazugehörige Auftrag auch im Versandprozess verarbeitet wird. Das Kommissionierverfahren muss daher das Schlagwort "scannen im Versandzentrum" enthalten.<br>Aufträge, die keine Lagerartikel (sondern z.B. nur Dienstleistungsartikel) enthalten, landen nicht im Versandzentrum.';
$tooltip['projekt']['edit']['#lieferscheinedrucken']='Bei Versandart "Selbstabholer" wird beim ersten Druck des Lieferscheins ein Doppeldruck angestoßen.';
$tooltip['projekt']['edit']['#autodrucklieferschein'] = $tooltip['projekt']['edit']['#lieferscheinedrucken'];
$tooltip['projekt']['edit']['#paketmarkedrucken']="Diese Option funktioniert nur wenn: <ul><li>Der Prozessstarter 'autoversand_manuell' / 'autoversand_plus' verwendet wird (legt die Aufträge im Autoversand erstmal in eine Warteschleife um Timeouts zu vermeiden</li><li>Ein physicher Drucker verwendet wird - also KEIN PDF/Download-Drucker</li><li>Ein Kommissionierverfahren mit Versandzentrum verwendet wird</li></ul><br>Hinweis: Im Versandzentrum werden alle Sendungen gesammelt, die nicht automatisch von DHL bei der Adresse erkannt wurden.";
/* Stufe (Versand) an Versandstation */
$tooltip['projekt']['edit']['#exportdruckrechnung']="Ausdruck von zusätzlichen Belegen, wenn der Beleg auf dem Steuerstatus 'Export' steht.";
$tooltip['projekt']['edit']['#paketmarkeautodrucken']="In Kombination mit einem Versandprozess mit Scannen im Versandzentrum wird hier die Paketmarke sowie deren Trackingnummer automatisch nach dem Scannen unter Lager => Versandzentrum bestätigt.<br><br>Damit muss der Mitarbeiter im Versand die Eingabe der Paketmarke nicht mehr mit Enter bestätigen.";
$tooltip['projekt']['edit']['#autodruckrechnungdoppel']='Wenn ein Auftrag die Zahlungsweise Bar sowie auch die Versandart Selbstabholer hat, so wird mit dieser Einstellung im Versandzentrum ein Rechnungs-Doppel erstellt und mitausgedruckt.<br>Voraussetzung ist, dass die Versandart Selbstabholer unter Administration → Einstellungen → Versandarten angelegt und mit dem Typ selbstabholer sowie dem Modul Selbstabholer eingestellt ist.';
/* Optionen */
$tooltip['projekt']['edit']['reservierung']="Für Aufträge in diesem Projekt werden nach Freigabe alle Artikel direkt im Lager reserviert.";
$tooltip['projekt']['edit']['#projektlager']="Für Aufträge und Lieferscheine in diesem Projekt dürfen Artikel nur aus dem Lager ausgelagert werden, welches dem Projekt eindeutig zugeordnet wurde. Die Auftragsampel bleibt somit rot, wenn auf dem Projektlager keine ausreichende Menge der Artikel im Auftrag liegt. Ebenso dürfen Aufträge anderer Projekte nicht auf das Lager zugreifen (bidirektionale Beziehung).
<br><br>Hinweis: Das Feld ‘Bevorzugtes Lager’ darf nicht gefüllt sein, wenn diese Einstellung aktiviert ist.";
$tooltip['projekt']['edit']['#standardlager']="Aufträge und Lieferscheine in diesem Projekt lagern aus diesem Lager aus. Ist auf dem Bevorzugten Lager keine ausreichender Bestand vorhanden, wird die Lagerampel rot.
Andere Projekte dürfen auch aus diesem Lager auslagern (unidirektionale Beziehung).<br><br>Hinweis: Diese Option sticht den Projektlager Haken drüber.";
$tooltip['projekt']['edit']['standardlagerproduktion']="Produktionen in diesem Projekt lagern aus diesem Lager aus. Ist auf dem Bevorzugten Lager keine ausreichender Bestand vorhanden, wird die Lagerampel rot.
Andere Projekte dürfen auch aus diesem Lager auslagern (unidirektionale Beziehung).<br><br>Hinweis: Diese Option sticht den Projektlager Haken drüber.";
$tooltip['projekt']['edit']['nurlagerartikel']="Beim Weiterführen eines Auftrags in einen Lieferschein werden nur die Lagerartikel in den Lieferschein übernommen.";
$tooltip['projekt']['edit']['seriennummernerfassen']="Beim Artikelscan im Versandzentrum müssen auch die Seriennummern abgescannt werden.";
$tooltip['projekt']['edit']['chargenerfassen']="Beim Artikelscan im Versandzentrum müssen auch die Chargen abgescannt werden.";
$tooltip['projekt']['edit']['mhderfassen']="Beim Artikelscan im Versandzentrum müssen auch die MHDs abgescannt werden.";
$tooltip['projekt']['edit']['autobestbeforebatch']="Wenn Charge/MHD eines Artikels beim Scan im Versandzentrum eindeutig sind, weil es auf dem ausgewählten Lagerplatz nur eine einzige Charge/MHD des Artikels gibt, dann wird diese Charge/MHD direkt eingefügt und der nächste Scan wird geladen.";
$tooltip['projekt']['edit']['allwaysautobestbeforebatch']="Die Vorauswahl erfolgt nach dem FIFO-Verfahren.";
$tooltip['projekt']['edit']['#allechargenmhd']="Ist der Haken gesetzt werden im Versandzentrum beim Artikelscan alle Chargen & MHDs aus dem Artikel angezeigt. <br>Ist der Haken nicht gesetzt wird im Versandzentrum nur die Charge/MHD angezeigt, die für den Versandprozess ausgelagert wurde.";
$tooltip['projekt']['edit']['eanherstellerscan']="Wenn Artikelnummer gescannt wird andere erlauben. Innerhalb dieses Projekts.";
$tooltip['projekt']['edit']['eanherstellerscanerlauben']="Beim Scannen eines Artikels im Versandzentrum darf anstelle der Artikelnummer auch die EAN gescannt werden.";
$tooltip['projekt']['edit']['fremdnummerscanerlauben']="Beim Scannen eines Artikels im Versandzentrum darf anstelle der Artikelnummer auch die Fremdnummer des Artikels aus dem Shop gescannt werden.";
$tooltip['projekt']['edit']['manualtracking']="Nach dem erfolgreichen Scan aller Artikel im Versandzentrum erfolgt keine automatische Weiterleitung in den Paketmarken-Dialog. Der Benutzer muss stattdessen auf eine Schaltfläche klicken, um dort hinzugelangen.";
$tooltip['projekt']['edit']['selbstabholermail']="Wird ein Auftrag mit der Versandart ‘Selbstabholer’ im Versandzentrum gescannt, erhält der Kunde automatisch eine Abholerinnerung per Mail.";
$tooltip['projekt']['edit']['folgebestaetigung']="Regelmäßige Folgebestätigung per Mail an den Kunden versenden, wenn Ware noch nicht versendet werden konnte.";
$tooltip['projekt']['edit']['portocheck']="Portocheck-Ampel im Auftrag wird erst grün, sobald ein Portoartikel im Auftrag enthalten ist.";
$tooltip['projekt']['edit']['nachnahmecheck']="Nachnahmecheck-Ampel im Auftrag wird erst grün, sobald zwei Portoartikel im Auftrag enthalten sind. Der zweite Portoartikel bildet die Nachnahmegebühr ab.";
$tooltip['projekt']['edit']['#shopzwangsprojekt']="Wird ein Auftrag aus einem Online-Shop importiert, der einen Artikel dieses Projektes enthält, wird auch der Auftrag auf dieses Projekt gebucht.<br><br>
In den Online-Shop-Einstellungen muss hierfür die Option ‘Multiprojekt Shop’ gesetzt sein.";
$tooltip['projekt']['edit']['kundenfreigabe_loeschen']="Nach dem Auftragsabschluss wird die Kundenfreigabe im Adressstamm des Kunden gelöscht.";
$tooltip['projekt']['edit']['#autostuecklistenanpassung']="Just-in-Time Stücklisten explodieren im Auftrag. Gültig für die 1. Ebene. Im Artikelstamm muss die Just-in-Time-Stückliste dafür mit dieser Eigenschaft markiert sein: “Auflösen / explodieren im Auftrag”.
<br><br>Sollen Unterstücklisten ebenfalls aufgelöst werden, muss dies in den Systemeinstellungen definiert werden.";
$tooltip['projekt']['edit']['versandzweigeteilt']="Schritt 1: Artikel packen, Schritt 2: Paketmarke drucken<br><br>
Wurden im Versandzentrum alle Artikel gescannt, wird der Benutzer nicht zum Paketmarkendialog weitergeleitet, sondern zurück in die Übersicht im Versandzentrum.
<br><br>
Der nächste Benutzer, der den Auftrag aufruft, landet direkt im Paketmarkendruck.";
$tooltip['projekt']['edit']['versandlagerplatzanzeigen']="Im Versandzentrum wird der Lagerplatz angezeigt, aus dem der Artikel entnommen wird.";
$tooltip['projekt']['edit']['versandartikelnameausstammdaten']="Im Versandzentrum wird der Artikelname aus den Stammdaten von xentral angezeigt, auch wenn der Artikelname auf den Dokumenten (Auftragsbestätigung, Rechnung) oder im Online-Shop abweichend ist.";
$tooltip['projekt']['edit']['produktionauftragautomatischfreigeben']="Beim Abschlie&szlig;en einer Produktion wird der zugeh&ouml;rige Auftrag abgeschlossen.";
$tooltip['projekt']['edit']['production_show_only_needed_storages']="In der Produktion werden die vefügbaren Lagerplätze nach dem FIFO-Prinzip geordnet angezeigt.";
$tooltip['projekt']['edit']['produktion_extra_seiten']="Im Arbeitsanweisungen-PDF einer Produktion wird jede einzelne Arbeitsanweisung auf eine eigene Seite gedruckt.";
$tooltip['projekt']['edit']['differenz_auslieferung_tage']="Anzahl der Tage zwischen dem Datum ‘Wunsch Liefertermin’ und dem Datum ‘Auslieferung Lager’, welches im Auftrag standardmäßig gesetzt werden soll.";
$tooltip['projekt']['edit']['create_proformainvoice']="Für Aufträge mit Besteuerung ‘Export’ wird automatisch eine Proformarechnung im System angelegt.";

/*E-Mail Versand */
$tooltip['projekt']['edit']['email_html_template']="Der Inhalt der E-Mail steht in der Variable {CONTENT}.<br>Externe Bilder werden in der Vorschau aufgrund von Sicherheitseinstellungen <strong>nicht</strong> angezeigt.";

/* Eigene Nummernkreise */
$tooltip['projekt']['edit']['eigenernummernkreis']="Zur Aktivierung den Haken \"Eigene Nummernkreise\" setzen + \"speichern\" (Button rechts unten klicken). Anschliessend die Nummernkreise je Feld über \"bearbeiten\" eintragen";

/* Steuer / Waehrung */
$tooltip['projekt']['edit']['anzeigesteuerbelege']="Anzeige von Belegen mit Brutto / Netto Werten. <br>Weitere Einstellungsmöglichkeiten unter Administration => Einstellungen => Grundeinstellungen => Steuer / Währung und in der Adresse => Zahlungskonditionen. Einstellungen aus Adresse sticht Einstellungen aus Projekt. Einstellungen aus Projekt sticht Grundeinstellungen.";

/* POS Einstellungen */
$tooltip['projekt']['edit']['kasse_lagerprozess']="Automatisches Abbuchen aus Lager.";
$tooltip['projekt']['edit']['kasse_lager']='Sofern bei "Lagerprozess" die Option "Aus eingestelltem POS Lager entnehmen" ausgew&auml;hlt ist.';
$tooltip['projekt']['edit']['kasse_autologout']="Angabe in Sekunden. 0 bedeutet kein automatisches Ausloggen.";
$tooltip['projekt']['edit']['kasse_autologout_abschluss']="Angabe in Sekunden. 0 bedeutet kein automatisches Ausloggen.";

/* Filiale */
$tooltip['projekt']['edit']['filialadresse']="Die Filialadresse muss die Rolle Kunde haben.";
$tooltip['projekt']['edit']['versandprojektfiliale']="Lieferungen werden &uuml;ber dieses Projekt versendet.";

/* PROVISIONENARTIKELVERTRETER */

$tooltip['provisionenartikelvertreter']['einstellungen']['absolutrabatt']="Gibt ein Vertreter Rabatt, so wird der prozentuale Rabatt von der prozentualen Provison abgezogen.<br>Beispiel: VK: 100; Provision 5%; Rabatt 2% => gewährte Provision 3% auf 100 VK";
$tooltip['provisionenartikelvertreter']['einstellungen']['brutto']="Gilt nur f&uuml;r neu erstellte Abrechnungen";
$tooltip['provisionenartikelvertreter']['einstellungen']['rebezahltam']="Rechnungen ohne bezahlt am Eintrag werden ignoriert";
$tooltip['provisionenartikelvertreter']['provisionen']['#b_typvertreter']="Bei den Berechnungsbasen \"Einkaufspreis\" und \"Erlös\" (Verkaufspreis – Einkaufspreis) wird der günstigste Einkaufspreis des Artikels aus dem Artikelstamm herangezogen.";
$tooltip['provisionenartikelvertreter']['provisionen']['#b_typvertriebsleitung']="Bei den Berechnungsbasen \"Einkaufspreis\" und \"Erlös\" (Verkaufspreis – Einkaufspreis) wird der günstigste Einkaufspreis des Artikels aus dem Artikelstamm herangezogen.";



/* RECHNUNG */

$tooltip['rechnung']['create']['lieferdatum']="Wenn Lieferschein vorhanden, bitte das Lieferdatum im Lieferschein ändern";
$tooltip['rechnung']['edit']['lieferdatum']="Wenn Lieferschein vorhanden, bitte das Lieferdatum im Lieferschein ändern";
$tooltip['rechnung']['create']['mahnwesenfestsetzen']="Expertenmodus: Die Einstellungen zur Zahlung können mit diesem Haken manuell festgesetzt werden. Hinweis: Dieser Haken setzt die automatische Berechnung über den Zahlungseingang außer Kraft. Damit kommt die Rechnung auch NICHT in das Mahnwesen. Sofern Sie den Zahlungseingang über die Kontoauszüge nutzen, sollte dieser Haken nur für manuelle Anpassungen verwendet werden, denn diese Einstellungen werden über den Zahlungseingang in der Regel automatisch gesetzt.";
$tooltip['rechnung']['edit']['mahnwesenfestsetzen']="Expertenmodus: Die Einstellungen zur Zahlung können mit diesem Haken manuell festgesetzt werden. Hinweis: Dieser Haken setzt die automatische Berechnung über den Zahlungseingang außer Kraft. Damit kommt die Rechnung auch NICHT in das Mahnwesen. Sofern Sie den Zahlungseingang über die Kontoauszüge nutzen, sollte dieser Haken nur für manuelle Anpassungen verwendet werden, denn diese Einstellungen werden über den Zahlungseingang in der Regel automatisch gesetzt.";
$tooltip['rechnung']['create']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";
$tooltip['rechnung']['edit']['#ohne_artikeltext']="Die Artikelbeschreibung in allen Beleg-Positionen wird auf dem Beleg ausgeblendet.";
$tooltip['rechnung']['create']['#kurs']="Der aktuelle Wechselkurs kann in der App 'Währung Umrechnung' von der European Central Bank abgerufen werden.";
$tooltip['rechnung']['edit']['#kurs']=$tooltip['rechnung']['create']['#kurs'];


/* RECHNUNGSLAUF */

$tooltip['rechnungslauf']['einstellungen']['#autodatum']="Berechnet einen Monat später.<br>Rechnung bzw. Auftrag wird in dem Intervall bzw. Monat erstellt in dem das Abo gebucht ist (z.B. Abrechnung im Juni für Leistungszeitraum Mai).<br><br>Somit muss das Datum für die Abrechnung nicht mehr jeden Monat manuell gesetzt werden. Der Abolauf wird somit im Standard um einen Monat/Intervall verschoben";
$tooltip['rechnungslauf']['einstellungen']['#gruppen']="Hinweis: Dieses Feature ist noch Beta! Bitte manuell Abos prüfen!";
$tooltip['rechnungslauf']['einstellungen']['#datum']="Nur verwenden wenn Abrechnung vergessen wurde! Datum darf nicht in der Zukunft sein!";
$tooltip['rechnungslauf']['einstellungen']['#cronjoborders'] = "Es können die Abos automatisch abgerechnet werden. Immer wenn ein Abo gefunden wird wird dieses abgerechnet. Es muss dem Prozessstarter „rechnungslauf“ aktiviert sein.";


/* REPORT */

$tooltip['report']['edit']['#dowload-structure-file']='Diese Datei kann in ein xentral System importiert werden um dort ebenfalls den Bericht nutzen zu können.';
$tooltip['report']['edit']['#edit-col-format-statement']='Verwendet "{VALUE}" als Platzhalter für den Spaltenwert z.B.: FORMAT({VALUE}, 3, \'de_DE\')';


/* SAMMELRECHNUNG */

$tooltip['sammelrechnung']['edit']['#sammelrechnung_portoartikel']="Die offenen Portoartikel aus dem Auftrag werden mit in die Sammelrechnung übernommen.<br>Ist der Haken nicht gesetzt, werden nur die ausgewählten Positionen aus der Sammelrechnungsübersicht genommen.";


/* SHIPPINGTAXSPLIT */

$tooltip['shippingtaxsplit']['list']['active']="Die Portoartikel werden automatisch nach Steuersätzen anteilig aufgeteilt";
$tooltip['shippingtaxsplit']['list']['usemaxtaxamount']="Die Portoartikel werden mit dem Steuersatz veranschlagt, der summenmäßig den größten Anteil der Bestellung ausmacht.<br>Die Option \"Immer automatish beim Bearbeiten des Belegs nach Steuersätzen aufsplitten bzw. anpassen\" muss hierfür aktiviert sein.";


/* SNAPADDY */
$tooltip['snapaddy']['edit']['#api-key']="Sie finden Ihren API-Token auf der Webseite von snapADDY unter Profil verwalten / Profil bearbeiten / API-Token";
$tooltip['snapaddy']['edit']['#browser-key']="Bitte kopieren Sie diesen Wert in Ihr snapADDY Browser-Plugin unter Einstellungen / Export / snapADDY API in das Feld \"Authorization-Header\"";
$tooltip['snapaddy']['edit']['#endpoint']="Bitte kopieren Sie diesen Wert in Ihr snapADDY Browser-Plugin unter Einstellungen / Export / snapADDY API in das Feld \"URL\"";


/* Spedition */

$tooltip['spedition']['einstellungen']['art']="z.B. Frankatur, VP-Codes";
$tooltip['spedition']['einstellungen']['code']="z.B. Code für Art von Spedition";
$tooltip['spedition']['einstellungen']['name']="Name oder Beschreibung z.B. bei Franktatur: unfrei Rechnung an Empfänger oder frei Haus";
$tooltip['spedition']['einstellungen']['kurzname']="Kurzbeschreibung z.B. bei Franktatur: unfrei oder frei Haus";


/* STUECKLISTENDETAILS */
$tooltip['stuecklistendetails']['list']['e_stueckliste']="Auswahl der Stückliste. Normale oder JIT Stücklisten sind beide möglich.";
$tooltip['stuecklistendetails']['list']['e_artikel']="Nach Auswahl der Stückliste, kann hier die Position innerhalb der bestehenden Stückliste festgelegt werden, für die die nachfolgenden Optionen gelten.";
$tooltip['stuecklistendetails']['list']['e_projekt']="Möglichkeit der Einschränkung auf Projektebene. ";
$tooltip['stuecklistendetails']['list']['e_re_ausblenden']="Nimmt die Position zwar mit in die Rechnung, setzt dort aber den „Im PDF ausblenden“ Haken, damit der Artikel nicht auf dem Beleg erscheint.";
$tooltip['stuecklistendetails']['list']['e_lf_ausblenden']="Nimmt die Position zwar mit in den Lieferschein, setzt dort aber den „Im PDF ausblenden“ Haken, damit der Artikel nicht auf dem Beleg erscheint.";
$tooltip['stuecklistendetails']['list']['e_ab_ausblenden']="Nimmt die Position zwar mit in den Auftrag, setzt dort aber den „Im PDF ausblenden“ Haken, damit der Artikel nicht auf dem Beleg erscheint.";
$tooltip['stuecklistendetails']['list']['e_ko_ausblenden']="Optionen Nein (Position taucht gar nicht auf) | Ja (Position taucht auf) | Verkleinert (Position taucht auf, aber mit kleinerer Schriftgröße)";
$tooltip['stuecklistendetails']['list']['e_scan_skip']="Die Position muss nicht im Versandzentrum gescannt werden";
$tooltip['stuecklistendetails']['list']['e_aktiv']="Wenn Haken nicht gesetzt ist, werden diesee Einstellungen nicht berücksichtigt";


/* UEBERSETZUNG */

$tooltip['uebersetzung']['list']['#edituebersetzung']="HTML (fett, kursiv, Zeilenumbruch, etc.) funktioniert im Moment NUR für die Variablen 'freifeld1inhalt' und 'freifeld2inhalt'.";


/* UBERTRAGUNGEN */

/* EDI Einstellungen */
$tooltip['uebertragungen']['create']['rechnungmail']="Die Rechnungs E-Mail wird automatisch versendet, wenn die Trackingnummer vom Fulfiller zurückgemeldet wird. <br>Bei mehreren Trackingnummern erfolgt der Versand der Rechnungs E-Mail nur einmal. <br><br><u>Vorraussetzungen:</u> <ul><li>Haken hier im Übertragungen Modul für 'Rechnung Mail' gesetzt</li><li>Rechnung bereits vorhanden mit Kunden E-Mail Adresse und Kundenname</li><li>Der Rechnungsbetrag muss grösser als 0 sein</li><ul>";
$tooltip['uebertragungen']['edit']['rechnungmail']=$tooltip['uebertragungen']['create']['rechnungmail'];

$tooltip['uebertragungen']['create']['lager']="Lagerplatz für Rückmeldungen ohne Lagerplatzangabe";
$tooltip['uebertragungen']['edit']['lager']=$tooltip['uebertragungen']['create']['lager'];

$tooltip['uebertragungen']['create']['importwarteschlange']="Belege müssen erst freigegeben werden";
$tooltip['uebertragungen']['edit']['importwarteschlange']=$tooltip['uebertragungen']['create']['importwarteschlange'];

$tooltip['uebertragungen']['create']['briefpapierimxml']="wird Base64encodiert in XML gespeichert";
$tooltip['uebertragungen']['edit']['briefpapierimxml']=$tooltip['uebertragungen']['create']['briefpapierimxml'];

$tooltip['uebertragungen']['create']['api']="Bitte ein API Account in Xentral anlegen und hier auswählen. Dieser wird für die Kommunikation benötigt.";
$tooltip['uebertragungen']['edit']['api']=$tooltip['uebertragungen']['create']['api'];

$tooltip['uebertragungen']['create']['parameter1']="Ausgabe der Belege von Xentral (Optional wenn Xentral von Fulfillment-Dienstleistern genutzt wird Ausgabe der \"Sonstigen Aktionen\" Lagerzahlen und Trackingnummern für Slave-Xentral bzw. ERP von Kunden.";
$tooltip['uebertragungen']['edit']['parameter1']=$tooltip['uebertragungen']['create']['parameter1'];

$tooltip['uebertragungen']['create']['parameter3']="Eingang für Aufträge, Lager, Tracking, Artikel etc.";
$tooltip['uebertragungen']['edit']['parameter3']=$tooltip['uebertragungen']['create']['parameter3'];

$tooltip['uebertragungen']['create']['autoshopexport'] = 'Unabhängig von Lieferscheinen und Trackingnummern wird nach X Tagen spätestens ein Auftrag final abgeschlossen und eventuell an Shops und Marktplätze zurückgemeldet.<br /> Es werden nur Aufträge beachtet die max. vor 14 Tagen per Übertragenmodul versendet wurden.';
$tooltip['uebertragungen']['edit']['autoshopexport'] = $tooltip['uebertragungen']['create']['autoshopexport'];

$tooltip['uebertragungen']['edit']['rechnunganlegen'] = 'Wenn der Lieferschein bzw. Trackingnummer zurückgemeldet wird und die passende Rechnung noch fehlt wird automatisch eine Rechnung angelegt.';
$tooltip['uebertragungen']['create']['rechnunganlegen'] = $tooltip['uebertragungen']['edit']['rechnunganlegen'];

$tooltip['uebertragungen']['edit']['createarticleifnotexists'] = 'Fehlende Artikel aus Auftragspositionen automatisch anlegen';
$tooltip['uebertragungen']['create']['createarticleifnotexists'] = $tooltip['uebertragungen']['edit']['createarticleifnotexists'];

$tooltip['uebertragungen']['edit']['update_shipping_method'] = 'Die Versandart des Lieferscheins wird angepasst falls diese in xentral angelegt ist.';
$tooltip['uebertragungen']['create']['update_shipping_method'] = $tooltip['uebertragungen']['edit']['update_shipping_method'];
  /* VERPACKUNGSMATERIAL */

$tooltip['verpackungsmaterial']['list']['e_aktiv']="Nur aktive Regeln werden für den Lagerauszug ausgewertet";
$tooltip['verpackungsmaterial']['list']['e_projektauslieferschein']="Die Lagerbuchung des Verpackungsmaterials wird auf das Projekt gebucht, welches dem Lieferschein zugewiesen ist.";
$tooltip['verpackungsmaterial']['list']['e_anzahl_teile_von']="Ab welcher Menge gilt diese Regel - Mindestanzahl";
$tooltip['verpackungsmaterial']['list']['e_anzahl_teile_bis']="Bis welcher Menge gilt diese Regel - Maximalanzahl";
$tooltip['verpackungsmaterial']['list']['e_artikel']="Der Verpackungsartikel, der ausgelagert werden soll";
$tooltip['verpackungsmaterial']['list']['e_menge']="Die Anzahl des Verpackungsartikels, die ausgelagert werden soll";
$tooltip['verpackungsmaterial']['list']['e_lager']="Wenn Verpackungsmaterial auf mehreren Lägern liegt, wird zuerst aus diesem Lager ausgebucht";
$tooltip['verpackungsmaterial']['list']['e_projekt']="Beschränkung auf Projekt möglich - hier wird das Projekt aus dem Beleg ausgewertet";


/* VERBINDLICHKEIT */
$tooltip['verbindlichkeit']['edit']['projekt']="Es wird das bevorzugte Projekt des Benutzers oder das Hauptprojekt der Firma oder das Projekt aus der Rolle des Benutzers ermittelt.";
/* Regelmaessige Verbindlichkeiten */
$tooltip['verbindlichkeit']['automatisch']['auswahl']="Per Zahlungseingang oder per Datum auswählen";
$tooltip['verbindlichkeit']['automatisch']['edittyp']="Wenn per Zahlungseingang oben ausgewählt wurde, hat man die Möglichkeit zwischen Verbindlichkeit, Kontenrahmen und Importfehler zu wählen. Kontenrahmen bucht die Zeile auf einen bestimmten Kontenrahmen und Importfehler markiert die Zeile als Importfehler";
$tooltip['verbindlichkeit']['automatisch']['#aktiv']="Nur aktive Einträge werden für regelemäßige Verbindlichkeiten ausgewertet";

$tooltip['verbindlichkeit']['automatisch']['#buchungstext']="dieser Buchungstext auf dem Bankkonto ist immer gleich und soll erkannt werden (z.B: 'Dauermietvertrag 12345X'). Sie können auch mit %Text% nach einem Textbaustein mitten im Buchungstext filtern. Z.B. %Miete% bei 'Das ist die Miete, die fällig wird'";
$tooltip['verbindlichkeit']['automatisch']['#soll']="dieser Betrag in der Spalte SOLL soll erkannt werden";
$tooltip['verbindlichkeit']['automatisch']['#haben']="dieser Betrag in der Spalte HABEN soll erkannt werden";
$tooltip['verbindlichkeit']['automatisch']['#gebuehr']="dieser Betrag in der Spalte GEBÜHR soll erkannt werden";
$tooltip['verbindlichkeit']['automatisch']['#waehrung']="die Buchung hat folgende Währung";
$tooltip['verbindlichkeit']['automatisch']['#tag']="Tag im Monat, an dem die Verbindlichkeit erstellt werden soll";

$tooltip['verbindlichkeit']['automatisch']['#lieferant']="die Verbindlichkeit soll auf diesen Lieferanten angelegt werden";
$tooltip['verbindlichkeit']['automatisch']['#gegenkonto']="die Verbindlichkeit soll auf dieses Gegenkonto angelegt werden (z.B. Bürobedarf: 906815)";
$tooltip['verbindlichkeit']['automatisch']['#rechnungnr']="die Verbindlichkeit soll diese Rechnungsnummer erhalten (z.B. NR. 12345X)";
$tooltip['verbindlichkeit']['automatisch']['#verwendungszweck']="die Verbindlichkeit soll diesen Buchungstext erhalten";
$tooltip['verbindlichkeit']['automatisch']['#kostenstelle']="die Verbindlichkeit soll diese Kostenstelle erhalten";
$tooltip['verbindlichkeit']['automatisch']['#zahlungsweise']="bitte wählen Sie die Zahlunsweise für die Verbindlichkeit aus";
$tooltip['verbindlichkeit']['automatisch']['#wareneingangspruefung']="Haken setzen, wenn in der Verbindlichkeit die Wareneingangsprüfung angehakt sein soll";
$tooltip['verbindlichkeit']['automatisch']['#rechnungseingangspruefung']="Haken setzen, wenn in der Verbindlichkeit die Rechnungseingangsprüfung angehakt sein soll";
$tooltip['verbindlichkeit']['automatisch']['#verbindlichkeitbetrag']="Dieser Betrag wird für die Verbindlichkeit übernommen";
$tooltip['verbindlichkeit']['automatisch']['#verbindlichkeitwaehrung']="Diese Währung wird für die Verbindlichkeit übernommen";

$tooltip['verbindlichkeit']['automatisch']['#grund']="Der Grund für den Importfehler, der in der Buchung vermerkt wird";


/* VERSANDARTEN */

$tooltip['versandarten']['create']['selmodul']="Durch Auswahl des Moduls und anschließendem Speicherns werden weitere versandartenspezifische Auswahlfelder angezeigt.";
$tooltip['versandarten']['edit']['selmodul']="Durch Auswahl des Moduls und anschließendem Speicherns werden weitere versandartenspezifische Auswahlfelder angezeigt.";
$tooltip['versandarten']['create']['ausprojekt']="Bitte nur anhaken, wenn Versand-Daten im Projekt vorhanden - übernimmt die Einstellungen im Projekt.";
$tooltip['versandarten']['edit']['ausprojekt']="Bitte nur anhaken, wenn Versand-Daten im Projekt vorhanden - übernimmt die Einstellungen im Projekt.";

$tooltip['versandarten']['create']['#autotracking']="Die Trackingnummer wird nach dem Erstellen der Paketmarke automatisch in das Feld eingefügt und muss nicht mehr von der Paketmarke gescannt werden.";
$tooltip['versandarten']['edit']['#autotracking']="Die Trackingnummer wird nach dem Erstellen der Paketmarke automatisch in das Feld eingefügt und muss nicht mehr von der Paketmarke gescannt werden.";

$tooltip['versandarten']['create']['trackingstart']="Beim Abscannen der Paketmarke wird ab dieser Stelle (inklusive) die Trackingnummer übernommen.";
$tooltip['versandarten']['edit']['trackingstart']="Beim Abscannen der Paketmarke wird ab dieser Stelle (inklusive) die Trackingnummer übernommen.";
$tooltip['versandarten']['create']['trackinglaenge']="Beim Abscannen der Paketmarke wird diese Länge der Trackingnummer ab der Startposition übernommen.";
$tooltip['versandarten']['edit']['trackinglaenge']="Beim Abscannen der Paketmarke wird diese Länge der Trackingnummer ab der Startposition übernommen.";

/* DPD */
$tooltip['versandarten']['create']['sofortabschliessen']="Diese Option wird in den Paketmarkendialog übernommen. <ul><li>Paketmarken werden entsprechen der angegebenen Anzahl Pakete erstellt</li><li>Die dazugehörigen Trackingnummern werden automatisch abgespeichert</li><li>Es wird eine Versandmail verschickt, in der sich die Links und Trackingnummern für DPD befinden.</li></ul>";
$tooltip['versandarten']['edit']['sofortabschliessen']="Diese Option wird in den Paketmarkendialog übernommen. <ul><li>Paketmarken werden entsprechen der angegebenen Anzahl Pakete erstellt</li><li>Die dazugehörigen Trackingnummern werden automatisch abgespeichert</li><li>Es wird eine Versandmail verschickt, in der sich die Links und Trackingnummern für DPD befinden.</li></ul>";

/* Intraship */
$tooltip['versandarten']['create']['intraship_exportgrund']="Pflichtfeld bei Exportsendungen. Dieses Feld erscheint auch auf den Export-Papieren von DHL zu dieser Sendung. Hier geben Sie eine Sammelbeschreibung für Ihre Ware ein.";
$tooltip['versandarten']['edit']['intraship_exportgrund']="Pflichtfeld bei Exportsendungen. Dieses Feld erscheint auch auf den Export-Papieren von DHL zu dieser Sendung. Hier geben Sie eine Sammelbeschreibung für Ihre Ware ein.";
$tooltip['versandarten']['create']['runden']="Das Gewicht aus der Paketmarke wir gerundet auf eine Ganzzahl an DHL gesendet. <br>Hinweis: In der Paketmaske kann noch ein Kommawert angezeigt werden - die Rundung erfolgt erst beim Übertragen der Daten via API.";
$tooltip['versandarten']['edit']['runden']="Das Gewicht aus der Paketmarke wir gerundet auf eine Ganzzahl an DHL gesendet. <br>Hinweis: In der Paketmaske kann noch ein Kommawert angezeigt werden - die Rundung erfolgt erst beim Übertragen der Daten via API.";
$tooltip['versandarten']['create']['keineversicherung']="Wenn der Haken <u>nicht</u> gesetzt ist, wird der Haken für die Extra-Versicherung für DHL automatisch in der Paketmarke gesetzt. Stand Mai 2017 sind das extra: 6€ für 500€-2500€ und 18€ für 2500€ - 25000€.";
$tooltip['versandarten']['edit']['keineversicherung']="Wenn der Haken <u>nicht</u> gesetzt ist, wird der Haken für die Extra-Versicherung für DHL automatisch in der Paketmarke gesetzt. Stand Mai 2017 sind das extra: 6€ für 500€-2500€ und 18€ für 2500€ - 25000€.";
$tooltip['versandarten']['create']['leitcodierung']="Wenn eine Adresse nicht in der Leitcode-Datenbank von DHL gefunden werden konnte (z. B. Adresse falsch geschrieben, Straße zu neu oder umbenannt, Hausnummer zu neu) erscheint ein Fehler in der Paketmarke ('Hard failure' o.ä.) und die Adresse muss abgeändert werden.";
$tooltip['versandarten']['edit']['leitcodierung']="Wenn eine Adresse nicht in der Leitcode-Datenbank von DHL gefunden werden konnte (z. B. Adresse falsch geschrieben, Straße zu neu oder umbenannt, Hausnummer zu neu) erscheint ein Fehler in der Paketmarke ('Hard failure' o.ä.) und die Adresse muss abgeändert werden.";
$tooltip['versandarten']['create']['intraship_vorausverfuegung']="Legt via API fest, wie mit der Paketmarke bei Nichtzustellbarkeit verfahren wird.<br>Bei manchen Niedrigpreis-Artikeln kann es sich lohnen das Paket preiszugeben (wird dann vom Versanddienstleister versteigert).";
$tooltip['versandarten']['edit']['intraship_vorausverfuegung']=$tooltip['versandarten']['create']['intraship_vorausverfuegung'];
$tooltip['versandarten']['create']['#sperrgut']="Option funktioniert nur mit Leitcodierung. Setzt die Sperrgut Option im Geschäftskundenportal für alle Sendungen. Am Besten legt man sich für Sperrgut Sendungen eine extra Versandart in Xentral an.";
$tooltip['versandarten']['edit']['#sperrgut']="Option funktioniert nur mit Leitcodierung. Setzt die Sperrgut Option im Geschäftskundenportal für alle Sendungen. Am Besten legt man sich für Sperrgut Sendungen eine extra Versandart in Xentral an.";
$tooltip['versandarten']['create']['log']="XML Dateien zu jedem Paketmarkenvorgang werden auf dem Server gespeichert. Damit könnten bei Problemen mit der API Fehlersuche betrieben werden.";
$tooltip['versandarten']['edit']['log']="XML Dateien zu jedem Paketmarkenvorgang werden auf dem Server gespeichert. Damit könnten bei Problemen mit der API Fehlersuche betrieben werden.";

/* Ups */
$tooltip['versandarten']['create']['service_code']="Prüfen Sie bitte den Standard Service Code (z.B. für UPS Standard oder Saver) mit Ihren Einstellungen im UPS Kundenportal";
$tooltip['versandarten']['edit']['service_code']="Prüfen Sie bitte den Standard Service Code (z.B. für UPS Standard oder Saver) mit Ihren Einstellungen im UPS Kundenportal";
$tooltip['versandarten']['create']['#zpl']="Ein anderes Format für die Paketmarke. Es werden Gif Dateien mit einem etwas anderem Format erstellt.";
$tooltip['versandarten']['edit']['#zpl']="Ein anderes Format für die Paketmarke. Es werden Gif Dateien mit einem etwas anderem Format erstellt.";
$tooltip['versandarten']['create']['#sandbox']="Zum Testen von Paketmarken gedacht. Auf dem Label erscheint groß 'SAMPLE' auf dem Barcode um es als Testlabel zu kennzeichnen.";
$tooltip['versandarten']['edit']['#sandbox']="Zum Testen von Paketmarken gedacht. Auf dem Label erscheint groß 'SAMPLE' auf dem Barcode um es als Testlabel zu kennzeichnen.";

/* Sevensenders */
$tooltip['versandarten']['edit']['skip_scan']='Der Trackingscandialog wird übersprungen und erst ein Versandeintrag angelegt, wenn die Daten von 7S zur Verfügung stehen.';


/* VERSANDERZEUGEN */

$tooltip['versanderzeugen']['frankieren']['wunschtermin']="Gibt auf der Paketmarke den Wunschtermin und Wunschzeit aus. <br><br>Ob der Wunschtermin angegeben werden kann, hängt vom Datum und vor allem von der Adresse des Empfängers ab. Hier wird überprüft, ob die Empfänger-Adresse am angegebenen Wunschtermin beliefert werden kann. Falls nicht, wird eine Fehlermeldung angezeigt - bitte hier dann DHL oder den Empfänger direkt kontaktieren oder ohne Wunschtermin absenden.";

/* VOUCHER */

$tooltip['voucher']['list']['#voucher_article_id']="Soll der Gutschein nur für einen Artikel gelten, wird hier der entsprechende Artikel hinterlegt.";
$tooltip['voucher']['list']['#voucher_valid_to']="Zeitraum, in dem der Gutschein gültig ist";
$tooltip['voucher']['list']['#voucher_address_id']="Soll der Gutschein nur für einen Kunden gelten, wird hier der entsprechende Kunde hinterlegt.";
$tooltip['voucher']['list']['#voucher_agent_address_id']="Falls auf den Gutschein eine Provision für den verkaufenden Mitarbeiter hinterlegt wird, kann hier der Mitarbeiter eingetragen werden.";
$tooltip['voucher']['list']['#voucher_comission_rate']="Falls auf den Gutschein eine Provision für den verkaufenden Mitarbeiter hinterlegt wird, kann hier der Provissionsatz (in Prozent) eingetragen werden.";
$tooltip['voucher']['list']['#voucher_voucher_original_value']="Wert des Gutscheins, z.B. 50";
$tooltip['voucher']['list']['#voucher_currency']="Währung des Werts des Gutscheins";
$tooltip['voucher']['list']['#voucher_tax_name']="Steuersatz des Gutscheins";


/* WARENEINGANG */
$tooltip['wareneingang']['paketannahme']['#articlescan'] = "Scannen von Artikeln oder Retouren.";

/* WELCOME */

$tooltip['welcome']['settings']['#txtGoogleIdentifier'] = 'Normalerweise ist das die G-Mail Adresse.';
$tooltip['welcome']['settings']['#chkGoogleActive'] = 'Ist der Haken nicht gesetzt wird keine Synchronisierung durchgeführt. Die Verbindung zu Google bleibt erhalten.';


/* ZAHLUNGSEINGANG */

$tooltip['zahlungseingang']['onlinebanking']['#editimportfehler']="Markiert diese Buchung als Importfehler und verhindert somit, dass diese Buchung im FiBu Export erscheint";
$tooltip['zahlungseingang']['onlinebanking']['#editabgeschlossen']="Markiert diese Buchung als abgeschlossen und verhindert, dass diese Buchung wieder im Zahlungseingang aufgeführt wird";

/* Kontenblatt */
$tooltip['zahlungseingang']['kontenblatt']['#buchunghaben']="Bezieht sich auf das Feld 'Konto'";
$tooltip['zahlungseingang']['kontenblatt']['#buchungsoll']="Bezieht sich auf das Feld 'Konto'";

/* Einstellungen */
$tooltip['zahlungseingang']['einstellungen']['#zahlungseingang_toleranz']="Zahlungseingang darf von angegebenem Wert abweichen.";

/* ZEITERFASSUNG */

$tooltip['zeiterfassung']['create']['#bisZeit']="Beim Buchen von tagesübergreifenden Zeiten, können die Zeiten folgendermaßen gebucht werden:<br><br>erster Eintrag von: 22:00 - 23:59<br>zweiter Eintrag (für den Folgetag) von: 00:00 - 06:00";
$tooltip['zeiterfassung']['edit']['#editabgeschlossen']=$tooltip['zeiterfassung']['create']['#bisZeit'];


/* ZEITERFASSUNGVORLAGE */

$tooltip['zeiterfassungvorlage']['list']['#vorlage']="Die Art/Tätigkeit, die man in der Zeiterfassung eingibt um die Vorlage auszuwählen.";
$tooltip['zeiterfassungvorlage']['list']['#vorlagedetail']="Inhalt für Details in der Zeiterfassung. Wird automatisch eingefügt wenn die Art/Tätigkeit in der Zeiterfassung gewählt wurde.";
$tooltip['zeiterfassungvorlage']['list']['#ausblenden']="Inaktive Vorlage. Taucht nicht für die Schnellsuche in der Zeiterfassung auf.";

/* ZAHLUNGSWEISEN */

$tooltip['zahlungsweisen']['create']['automatischbezahlt']="Setzt Zahlungsstatus einer Rechnung auf 'bezahlt'.<br>
Diese Einstellung verhält sich so, als würde eine Rechnung manuell auf bezahlt gesetzt werden.";
$tooltip['zahlungsweisen']['edit']['automatischbezahlt']="Setzt Zahlungsstatus einer Rechnung auf 'bezahlt'.<br>
Diese Einstellung verhält sich so, als würde eine Rechnung manuell auf bezahlt gesetzt werden.";
$tooltip['zahlungsweisen']['create']['automatischbezahltverbindlichkeit']="Setzt Zahlungsstatus einer Verbindlichkeit auf 'bezahlt' und verschiebt diese in das Verbindlichkeiten-Archiv.<br>Diese Einstellung verhält sich so, als würde eine Verbindlichkeit manuell auf bezahlt gesetzt werden.";
$tooltip['zahlungsweisen']['edit']['automatischbezahltverbindlichkeit']="Setzt Zahlungsstatus einer Verbindlichkeit auf 'bezahlt' und verschiebt diese in das Verbindlichkeiten-Archiv.<br>Diese Einstellung verhält sich so, als würde eine Verbindlichkeit manuell auf bezahlt gesetzt werden.";

/* LIEFERSCHWELLE */

$tooltip['lieferschwelle']['list']['e_verwenden']='Wird automatisch gesetzt, sobald Lieferschwelle in EUR überschritten wurde. Bei manuellen Aktivieren wird ab sofort der hier eingestellte Steuersatz verwendet';
$tooltip['lieferschwelle']['list']['e_erloeskontonormal'] = 'Wenn das Erlöskonto erst nachträglich ergänzt wird, werden diese nicht in bereits existierende Belege übernommen.';
$tooltip['lieferschwelle']['list']['e_erloeskontoermaessigt'] = 'Wenn das Erlöskonto erst nachträglich ergänzt wird, werden diese nicht in bereits existierende Belege übernommen.';
$tooltip['lieferschwelle']['list']['e_erloeskontobefreit'] = 'Wenn das Erlöskonto erst nachträglich ergänzt wird, werden diese nicht in bereits existierende Belege übernommen.';

/* AMAINVOICE */

$tooltip['amainvoice']['list']['firmkeyid'] = 'Zugangsdaten direkt vom Amainvoice Portal';
$tooltip['amainvoice']['list']['clientidentifier'] = 'Zugangsdaten direkt vom Amainvoice Portal';
$tooltip['amainvoice']['list']['startdate'] = 'Das Datum ab dem die Rechnungen und Gutschriften abgeholt werden sollen';
$tooltip['amainvoice']['list']['createorder'] = 'Diese Option nur aktivieren wenn die Aufträge nicht durch die Xentral-Schnittelle bereits importiert werden';

/* COPPERSURCHARGE */
$tooltip['coppersurcharge']['list']['surcharge-article'] = '(Pflichtfeld) Dieser Artikel wird als Grundlage für die Positionen benötigt. Von diesem wird der Verkaufspreis als DEL verwendet. Dieser ist z.b. über das Tagespreise-Modul schnell pflegbar';
$tooltip['coppersurcharge']['list']['surcharge-position-type'] = '(Pflichtfeld) Auswahl darüber, wie die Zuschlagspositionen eingefügt werden sollen.';
$tooltip['coppersurcharge']['list']['surcharge-document-conversion'] = '(Pflichtfeld) Welches Datum soll als Grundlage der Berechnung dienen, wenn ein Auftrag aus einem Angebot generiert wird? Falls kein Angebot da sein sollte dient das aktuelle Datum als Fallback.';
$tooltip['coppersurcharge']['list']['surcharge-invoice'] = '(Pflichtfeld) Welches Datum soll als Grundlage der Berechnung dienen, wenn eine Rechnung generiert wird? Fallback bei nicht vorhandenen Vorgängerbeleg ist immer das aktuelle Datum.';
$tooltip['coppersurcharge']['list']['surcharge-delivery-costs'] = '(Pflichtfeld) Bezugskosten sind eine Grundlage der Berechnungsformel (in %): Kupferzuschlag EUR/km = (Kupfergewicht (kg/km) * (DEL + Bezugskosten)) - Kupferbasis / 100. Der Standard sind derzeit 1%';
$tooltip['coppersurcharge']['list']['surcharge-copper-base-standard'] = '(Pflichtfeld) Die Kupferbasis ist eine Grundlage der Berechnungsformel: Kupferzuschlag EUR/km = (Kupfergewicht (kg/km) * (DEL + Bezugskosten)) - Kupferbasis / 100. Der Standard sind derzeit 150 EUR pro 100kg';
$tooltip['coppersurcharge']['list']['surcharge-copper-base'] = 'Falls ein Artikel eine abweichende Kupferbasis haben soll kann diese in einem Freifeld gepflegt werden. Dieses kann hier ausgewählt werden.';
$tooltip['coppersurcharge']['list']['surcharge-copper-number'] = '(Pflichtfeld) In diesem Freifeld kann die artikelspezifische Kupferzahl (km/kg) gepflegt werden. Sie ist Grundlage der Berechnung: Kupferzuschlag EUR/km = (Kupfergewicht (kg/km) * (DEL + Bezugskosten)) - Kupferbasis / 100.';