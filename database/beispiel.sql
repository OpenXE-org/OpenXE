-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 26. Okt 2015 um 17:47
-- Server Version: 5.5.44-0+deb8u1
-- PHP-Version: 5.6.13-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `osstest`
--


--
-- Daten für Tabelle `adresse`
--

INSERT INTO `adresse` (`id`, `typ`, `marketingsperre`, `trackingsperre`, `rechnungsadresse`, `sprache`, `name`, `abteilung`, `unterabteilung`, `ansprechpartner`, `land`, `strasse`, `ort`, `plz`, `telefon`, `telefax`, `mobil`, `email`, `ustid`, `ust_befreit`, `passwort_gesendet`, `sonstiges`, `adresszusatz`, `kundenfreigabe`, `steuer`, `logdatei`, `kundennummer`, `lieferantennummer`, `mitarbeiternummer`, `konto`, `blz`, `bank`, `inhaber`, `swift`, `iban`, `waehrung`, `paypal`, `paypalinhaber`, `paypalwaehrung`, `projekt`, `partner`, `zahlungsweise`, `zahlungszieltage`, `zahlungszieltageskonto`, `zahlungszielskonto`, `versandart`, `kundennummerlieferant`, `zahlungsweiselieferant`, `zahlungszieltagelieferant`, `zahlungszieltageskontolieferant`, `zahlungszielskontolieferant`, `versandartlieferant`, `geloescht`, `firma`, `webid`, `vorname`, `kennung`, `sachkonto`, `freifeld1`, `freifeld2`, `freifeld3`, `filiale`, `vertrieb`, `innendienst`, `verbandsnummer`, `abweichendeemailab`, `portofrei_aktiv`, `portofreiab`, `infoauftragserfassung`, `mandatsreferenz`, `mandatsreferenzdatum`, `mandatsreferenzaenderung`, `glaeubigeridentnr`, `kreditlimit`, `tour`, `zahlungskonditionen_festschreiben`, `rabatte_festschreiben`, `mlmaktiv`, `mlmvertragsbeginn`, `mlmlizenzgebuehrbis`, `mlmfestsetzenbis`, `mlmfestsetzen`, `mlmmindestpunkte`, `mlmwartekonto`, `abweichende_rechnungsadresse`, `rechnung_vorname`, `rechnung_name`, `rechnung_titel`, `rechnung_typ`, `rechnung_strasse`, `rechnung_ort`, `rechnung_plz`, `rechnung_ansprechpartner`, `rechnung_land`, `rechnung_abteilung`, `rechnung_unterabteilung`, `rechnung_adresszusatz`, `rechnung_telefon`, `rechnung_telefax`, `rechnung_anschreiben`, `rechnung_email`, `geburtstag`, `rolledatum`, `liefersperre`, `liefersperregrund`, `mlmpositionierung`, `steuernummer`, `steuerbefreit`, `mlmmitmwst`, `mlmabrechnung`, `mlmwaehrungauszahlung`, `mlmauszahlungprojekt`, `sponsor`, `geworbenvon`, `logfile`, `kalender_aufgaben`, `verrechnungskontoreisekosten`, `usereditid`, `useredittimestamp`, `rabatt`, `provision`, `rabattinformation`, `rabatt1`, `rabatt2`, `rabatt3`, `rabatt4`, `rabatt5`, `internetseite`, `bonus1`, `bonus1_ab`, `bonus2`, `bonus2_ab`, `bonus3`, `bonus3_ab`, `bonus4`, `bonus4_ab`, `bonus5`, `bonus5_ab`, `bonus6`, `bonus6_ab`, `bonus7`, `bonus7_ab`, `bonus8`, `bonus8_ab`, `bonus9`, `bonus9_ab`, `bonus10`, `bonus10_ab`, `rechnung_periode`, `rechnung_anzahlpapier`, `rechnung_permail`, `titel`, `anschreiben`, `nachname`, `arbeitszeitprowoche`, `folgebestaetigungsperre`, `lieferantennummerbeikunde`, `verein_mitglied_seit`, `verein_mitglied_bis`, `verein_mitglied_aktiv`, `verein_spendenbescheinigung`, `freifeld4`, `freifeld5`, `freifeld6`, `freifeld7`, `freifeld8`, `freifeld9`, `freifeld10`, `rechnung_papier`, `angebot_cc`, `auftrag_cc`, `rechnung_cc`, `gutschrift_cc`, `lieferschein_cc`, `bestellung_cc`, `angebot_fax_cc`, `auftrag_fax_cc`, `rechnung_fax_cc`, `gutschrift_fax_cc`, `lieferschein_fax_cc`, `bestellung_fax_cc`, `abperfax`, `abpermail`, `kassiereraktiv`, `kassierernummer`, `kassiererprojekt`, `portofreilieferant_aktiv`, `portofreiablieferant`, `mandatsreferenzart`, `mandatsreferenzwdhart`, `serienbrief`) VALUES
(3, 'firma', '', 0, 0, 'deutsch', 'Max Muster', '', '', '', 'DE', 'Musterstrasse 6', 'Musterdorf', '12345', '0821123456789', '0821123456790', '', 'info@maxmuellermuster.de', '', 0, 0, '', '', 0, '', '2015-10-26 15:58:21', '10000', '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 'rechnung', '', '', '', 'versandunternehmen', '', 'rechnung', '', '', '', '', 0, 1, '', '', '', '', '', '', '', '', 0, 0, '', '', 0.00, 0.00, '', '', '0000-00-00', 0, '', 0.00, 0, 0, 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', 0, 0, 0.00, 0, '', '', '', 'firma', '', '', '', '', 'DE', '', '', '', '', '', '', '', '0000-00-00', '0000-00-00', 0, '', '', '', 0, 0, '', '', 0, 0, 0, '', 0, 0, 2, '2015-10-26 15:58:21', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', '', '', 0.00, 0, '', '0000-00-00', '0000-00-00', 0, 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0, 0, 0.00, 'einmalig', 'erste', 0),
(4, 'frau', '', 0, 0, 'deutsch', 'Eva Müller', '', '', '', 'DE', 'Musterweg 12a', 'Musterdorf', '12345', '089123456789', '089123456790', '', '', '', 0, 0, '', '', 0, '', '2015-10-26 15:58:01', '10001', '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 'rechnung', '', '', '', 'versandunternehmen', '', 'rechnung', '', '', '', '', 0, 1, '', '', '', '', '', '', '', '', 0, 0, '', '', 0.00, 0.00, '', '', '0000-00-00', 0, '', 0.00, 0, 0, 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', 0, 0, 0.00, 0, '', '', '', 'firma', '', '', '', '', 'DE', '', '', '', '', '', '', '', '0000-00-00', '0000-00-00', 0, '', '', '', 0, 0, '', '', 0, 0, 0, '', 0, 0, 2, '2015-10-26 15:58:01', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', '', '', 0.00, 0, '', '0000-00-00', '0000-00-00', 0, 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0, 0, 0.00, 'einmalig', 'erste', 0),
(5, 'firma', '', 0, 0, 'deutsch', 'Hans Huber', '', '', '', 'DE', 'Musterstrasse 6', 'Musterstadt', '12345', '017123456745', '', '', 'hans@huberhausen.de', '', 0, 0, '', '', 0, '', '2015-10-26 15:59:11', '10002', '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 'rechnung', '', '', '', 'versandunternehmen', '', 'rechnung', '', '', '', '', 0, 1, '', '', '', '', '', '', '', '', 0, 0, '', '', 0.00, 0.00, '', '', '0000-00-00', 0, '', 0.00, 0, 0, 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', 0, 0, 0.00, 0, '', '', '', 'firma', '', '', '', '', 'DE', '', '', '', '', '', '', '', '0000-00-00', '0000-00-00', 0, '', '', '', 0, 0, '', '', 0, 0, 0, '', 0, 0, 2, '2015-10-26 15:59:11', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', '', '', 0.00, 0, '', '0000-00-00', '0000-00-00', 0, 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0, 0, 0.00, 'einmalig', 'erste', 0),
(6, 'herr', '', 0, 0, 'deutsch', 'Anton Lechner', '', '', '', 'DE', 'Musterstrasse 6', '', '12345', '', '', '', '', '', 0, 0, '', '', 0, '', '2015-10-26 16:00:29', '', '', '90000', '', '', '', '', '', '', '', '', '', '', 1, 0, 'rechnung', '', '', '', 'versandunternehmen', '', 'rechnung', '', '', '', '', 0, 1, '', '', '', '', '', '', '', '', 0, 0, '', '', 0.00, 0.00, '', '', '0000-00-00', 0, '', 0.00, 0, 0, 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', 0, 0, 0.00, 0, '', '', '', 'firma', '', '', '', '', 'DE', '', '', '', '', '', '', '', '0000-00-00', '0000-00-00', 0, '', '', '', 0, 0, '', '', 0, 0, 0, '', 0, 0, 2, '2015-10-26 16:00:29', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', '', '', 0.00, 0, '', '0000-00-00', '0000-00-00', 0, 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0, 0, 0.00, 'einmalig', 'erste', 0),
(7, 'firma', '', 0, 0, 'deutsch', 'Schrauben Meier', '', '', '', 'DE', 'Musterstrasse 6', 'Musterdorf', '12345', '12345678', '', '', 'schrauben@meiermusterdorf.de', '', 0, 0, '', '', 0, '', '2015-10-26 16:04:50', '', '70000', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 'rechnung', '', '', '', 'versandunternehmen', '', 'rechnung', '', '', '', '', 0, 1, '', '', '', '', '', '', '', '', 0, 0, '', '', 0.00, 0.00, '', '', '0000-00-00', 0, '', 0.00, 0, 0, 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', 0, 0, 0.00, 0, '', '', '', 'firma', '', '', '', '', 'DE', '', '', '', '', '', '', '', '0000-00-00', '0000-00-00', 0, '', '', '', 0, 0, '', '', 0, 0, 0, '', 0, 0, 2, '2015-10-26 16:04:50', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', '', '', 0.00, 0, '', '0000-00-00', '0000-00-00', 0, 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0, 0, 0.00, 'einmalig', 'erste', 0),
(8, 'firma', '', 0, 0, 'deutsch', 'Elektronik Großhandel', '', '', '', 'DE', 'Musterweg 12a', 'Musterdorf', '12345', '12345678', '', '', 'elektronik@grosshandel.de', '', 0, 0, '', '', 0, '', '2015-10-26 16:05:42', '', '70001', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 'rechnung', '', '', '', 'versandunternehmen', '', 'rechnung', '', '', '', '', 0, 1, '', '', '', '', '', '', '', '', 0, 0, '', '', 0.00, 0.00, '', '', '0000-00-00', 0, '', 0.00, 0, 0, 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', 0, 0, 0.00, 0, '', '', '', 'firma', '', '', '', '', 'DE', '', '', '', '', '', '', '', '0000-00-00', '0000-00-00', 0, '', '', '', 0, 0, '', '', 0, 0, 0, '', 0, 0, 2, '2015-10-26 16:05:42', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', '', '', 0.00, 0, '', '0000-00-00', '0000-00-00', 0, 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0, 0, 0.00, 'einmalig', 'erste', 0);

--
-- Daten für Tabelle `adresse_rolle`
--

INSERT INTO `adresse_rolle` (`id`, `adresse`, `projekt`, `subjekt`, `praedikat`, `objekt`, `parameter`, `von`, `bis`) VALUES
(NULL, 3, 1, 'Kunde', 'von', 'Projekt', '1', NOW(), '0000-00-00'),
(NULL, 4, 1, 'Kunde', 'von', 'Projekt', '1', NOW(), '0000-00-00'),
(NULL, 5, 1, 'Kunde', 'von', 'Projekt', '1', NOW(), '0000-00-00'),
(NULL, 6, 1, 'Mitarbeiter', 'von', 'Projekt', '1', NOW(), '0000-00-00'),
(NULL, 7, 1, 'Lieferant', 'von', 'Projekt', '1', NOW(), '0000-00-00'),
(NULL, 8, 1, 'Lieferant', 'von', 'Projekt', '1', NOW(), '0000-00-00');

--
-- Daten für Tabelle `angebot`
--

INSERT INTO `angebot` (`id`, `datum`, `gueltigbis`, `projekt`, `belegnr`, `bearbeiter`, `anfrage`, `auftrag`, `freitext`, `internebemerkung`, `status`, `adresse`, `retyp`, `rechnungname`, `retelefon`, `reansprechpartner`, `retelefax`, `reabteilung`, `reemail`, `reunterabteilung`, `readresszusatz`, `restrasse`, `replz`, `reort`, `reland`, `name`, `abteilung`, `unterabteilung`, `strasse`, `adresszusatz`, `plz`, `ort`, `land`, `ustid`, `email`, `telefon`, `telefax`, `betreff`, `kundennummer`, `versandart`, `vertrieb`, `zahlungsweise`, `zahlungszieltage`, `zahlungszieltageskonto`, `zahlungszielskonto`, `gesamtsumme`, `bank_inhaber`, `bank_institut`, `bank_blz`, `bank_konto`, `kreditkarte_typ`, `kreditkarte_inhaber`, `kreditkarte_nummer`, `kreditkarte_pruefnummer`, `kreditkarte_monat`, `kreditkarte_jahr`, `abweichendelieferadresse`, `abweichenderechnungsadresse`, `liefername`, `lieferabteilung`, `lieferunterabteilung`, `lieferland`, `lieferstrasse`, `lieferort`, `lieferplz`, `lieferadresszusatz`, `lieferansprechpartner`, `liefertelefon`, `liefertelefax`, `liefermail`, `autoversand`, `keinporto`, `ust_befreit`, `firma`, `versendet`, `versendet_am`, `versendet_per`, `versendet_durch`, `inbearbeitung`, `vermerk`, `logdatei`, `ansprechpartner`, `deckungsbeitragcalc`, `deckungsbeitrag`, `erloes_netto`, `umsatz_netto`, `lieferdatum`, `vertriebid`, `aktion`, `provision`, `provision_summe`, `keinsteuersatz`, `anfrageid`, `gruppe`, `anschreiben`, `usereditid`, `useredittimestamp`, `realrabatt`, `rabatt`, `rabatt1`, `rabatt2`, `rabatt3`, `rabatt4`, `rabatt5`, `steuersatz_normal`, `steuersatz_zwischen`, `steuersatz_ermaessigt`, `steuersatz_starkermaessigt`, `steuersatz_dienstleistung`, `waehrung`, `schreibschutz`, `pdfarchiviert`, `pdfarchiviertversion`, `typ`, `ohne_briefpapier`, `auftragid`, `lieferid`, `ansprechpartnerid`, `projektfiliale`, `abweichendebezeichnung`) VALUES
(1, NOW(), '2015-11-23', '1', '100000', 'Administrator2', '', '', '', '', 'beauftragt', 3, '', '', '', '', '', '', '', '', '', '', '', '', '', 'Max Muster', '', '', 'Musterstrasse 6', '', '12345', 'Musterdorf', 'DE', '', 'info@maxmuellermuster.de', '0821123456789', '0821123456790', '', '10000', 'versandunternehmen', 'Administrator2', 'rechnung', 14, 10, 2.00, 737.8000, '', '', 0, 0, '', '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 0, 1, 1, '2015-10-26 17:17:26', 'sonstiges', 'Administrator2', 0, '', '2015-10-26 16:42:04', '', 1, 68.98, 427.70, 620.00, NULL, 0, '', 0.00, NULL, NULL, 0, 0, '', 2, '2015-10-26 16:17:28', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', 1, 0, 0, 'firma', 0, 1, 0, 0, 0, 0),
(2, NOW(), '2015-11-23', '1', '100001', 'Administrator2', '', '', '', '', 'freigegeben', 4, '', '', '', '', '', '', '', '', '', '', '', '', '', 'Eva Müller', '', '', 'Musterweg 12a', '', '12345', 'Musterdorf', 'DE', '', '', '089123456789', '089123456790', '', '10001', 'versandunternehmen', 'Administrator2', 'rechnung', 14, 10, 2.00, 73.7800, '', '', 0, 0, '', '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 0, 1, 0, '0000-00-00 00:00:00', '', '', 0, '', '2015-10-26 16:17:58', '', 1, 68.98, 42.77, 62.00, NULL, 0, '', 0.00, NULL, NULL, 0, 0, '', 2, '2015-10-26 16:17:58', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', 0, 0, 0, 'frau', 0, 0, 0, 0, 0, 0),
(3, NOW(), '2015-11-23', '1', '100002', 'Administrator2', '', '', '', '', 'freigegeben', 5, '', '', '', '', '', '', '', '', '', '', '', '', '', 'Hans Huber', '', '', 'Musterstrasse 6', '', '12345', 'Musterstadt', 'DE', '', 'hans@huberhausen.de', '017123456745', '', '', '10002', 'versandunternehmen', 'Administrator', 'rechnung', 14, 10, 2.00, 1106.7000, '', '', 0, 0, '', '', '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 0, 1, 0, '0000-00-00 00:00:00', '', '', 0, '', '2015-10-26 16:39:52', '', 1, 100.00, 930.00, 930.00, NULL, 0, '', 0.00, NULL, NULL, 0, 0, '', 1, '2015-10-26 16:39:52', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', 0, 0, 0, 'firma', 0, 0, 0, 0, 0, 0);

--
-- Daten für Tabelle `angebot_position`
--

INSERT INTO `angebot_position` (`id`, `angebot`, `artikel`, `projekt`, `bezeichnung`, `beschreibung`, `internerkommentar`, `nummer`, `menge`, `preis`, `waehrung`, `lieferdatum`, `vpe`, `sort`, `status`, `umsatzsteuer`, `bemerkung`, `geliefert`, `logdatei`, `punkte`, `bonuspunkte`, `mlmdirektpraemie`, `keinrabatterlaubt`, `grundrabatt`, `rabattsync`, `rabatt1`, `rabatt2`, `rabatt3`, `rabatt4`, `rabatt5`, `einheit`, `optional`, `rabatt`, `zolltarifnummer`, `herkunftsland`) VALUES
(NULL, 1, 12, 1, 'Handsteuergerät', 'Kabelgebundenes Steuergerät mit Ein-/Ausschalter und 2 Tastern (Vorwärts/Rückwärts)', '', '700012', 10, 62.0000, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 0, '2015-10-26 16:17:16', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, 0.00, '0', '0'),
(NULL, 2, 12, 1, 'Handsteuergerät', 'Kabelgebundenes Steuergerät mit Ein-/Ausschalter und 2 Tastern (Vorwärts/Rückwärts)', '', '700012', 1, 62.0000, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 0, '2015-10-26 16:17:42', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, 0.00, '0', '0'),
(NULL, 3, 12, 1, 'Handsteuergerät', 'Kabelgebundenes Steuergerät mit Ein-/Ausschalter und 2 Tastern (Vorwärts/Rückwärts)', '', '700012', 15, 62.0000, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 0, '2015-10-26 16:18:14', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, 0.00, '0', '0');

--
-- Daten für Tabelle `angebot_protokoll`
--

INSERT INTO `angebot_protokoll` (`id`, `angebot`, `zeit`, `bearbeiter`, `grund`) VALUES
(NULL, 1, '2015-10-26 17:16:05', 'Administrator2', 'Angebot angelegt'),
(NULL, 1, '2015-10-26 17:17:19', 'Administrator2', 'Angebot freigegeben'),
(NULL, 1, '2015-10-26 17:17:26', 'Administrator2', 'Angebot versendet'),
(NULL, 2, '2015-10-26 17:17:32', 'Administrator2', 'Angebot angelegt'),
(NULL, 2, '2015-10-26 17:17:46', 'Administrator2', 'Angebot freigegeben'),
(NULL, 3, '2015-10-26 17:18:02', 'Administrator2', 'Angebot angelegt'),
(NULL, 3, '2015-10-26 17:18:17', 'Administrator2', 'Angebot freigegeben');

--
-- Daten für Tabelle `artikel`
--

INSERT INTO `artikel` (`id`, `typ`, `nummer`, `checksum`, `projekt`, `inaktiv`, `ausverkauft`, `warengruppe`, `name_de`, `name_en`, `kurztext_de`, `kurztext_en`, `beschreibung_de`, `beschreibung_en`, `uebersicht_de`, `uebersicht_en`, `links_de`, `links_en`, `startseite_de`, `startseite_en`, `standardbild`, `herstellerlink`, `hersteller`, `teilbar`, `nteile`, `seriennummern`, `lager_platz`, `lieferzeit`, `lieferzeitmanuell`, `sonstiges`, `gewicht`, `endmontage`, `funktionstest`, `artikelcheckliste`, `stueckliste`, `juststueckliste`, `barcode`, `hinzugefuegt`, `pcbdecal`, `lagerartikel`, `porto`, `chargenverwaltung`, `provisionsartikel`, `gesperrt`, `sperrgrund`, `geloescht`, `gueltigbis`, `umsatzsteuer`, `klasse`, `adresse`, `shopartikel`, `unishopartikel`, `journalshopartikel`, `shop`, `katalog`, `katalogtext_de`, `katalogtext_en`, `katalogbezeichnung_de`, `katalogbezeichnung_en`, `neu`, `topseller`, `startseite`, `wichtig`, `mindestlager`, `mindestbestellung`, `partnerprogramm_sperre`, `internerkommentar`, `intern_gesperrt`, `intern_gesperrtuser`, `intern_gesperrtgrund`, `inbearbeitung`, `inbearbeitunguser`, `cache_lagerplatzinhaltmenge`, `internkommentar`, `firma`, `logdatei`, `anabregs_text`, `autobestellung`, `produktion`, `herstellernummer`, `restmenge`, `mlmdirektpraemie`, `keineeinzelartikelanzeigen`, `mindesthaltbarkeitsdatum`, `letzteseriennummer`, `individualartikel`, `keinrabatterlaubt`, `rabatt`, `rabatt_prozent`, `geraet`, `serviceartikel`, `autoabgleicherlaubt`, `pseudopreis`, `freigabenotwendig`, `freigaberegel`, `nachbestellt`, `ean`, `mlmpunkte`, `mlmbonuspunkte`, `mlmkeinepunkteeigenkauf`, `shop2`, `shop3`, `usereditid`, `useredittimestamp`, `freifeld1`, `freifeld2`, `freifeld3`, `freifeld4`, `freifeld5`, `freifeld6`, `einheit`, `webid`, `lieferzeitmanuell_en`, `variante`, `variante_von`, `produktioninfo`, `sonderaktion`, `sonderaktion_en`, `autolagerlampe`, `leerfeld`, `zolltarifnummer`, `herkunftsland`, `laenge`, `breite`, `hoehe`, `gebuehr`, `pseudolager`, `downloadartikel`, `matrixprodukt`, `steuer_erloese_inland_normal`, `steuer_aufwendung_inland_normal`, `steuer_erloese_inland_ermaessigt`, `steuer_aufwendung_inland_ermaessigt`, `steuer_erloese_inland_steuerfrei`, `steuer_aufwendung_inland_steuerfrei`, `steuer_erloese_inland_innergemeinschaftlich`, `steuer_aufwendung_inland_innergemeinschaftlich`, `steuer_erloese_inland_eunormal`, `steuer_erloese_inland_nichtsteuerbar`, `steuer_erloese_inland_euermaessigt`, `steuer_aufwendung_inland_nichtsteuerbar`, `steuer_aufwendung_inland_eunormal`, `steuer_aufwendung_inland_euermaessigt`, `steuer_erloese_inland_export`, `steuer_aufwendung_inland_import`, `steuer_art_produkt`, `steuer_art_produkt_download`, `metadescription_de`, `metadescription_en`, `metakeywords_de`, `metakeywords_en`, `anabregs_text_en`) VALUES
(1, 'produkt', '700001', '', 1, '', 0, '', 'Schraube M10x20', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 7, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:36:43', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:36:43', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(2, 'produkt', '700002', '', 1, '', 0, '', 'Sechskant-Mutter M10', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 7, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:25:35', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:25:35', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(3, 'produkt', '700003', '', 1, '', 0, '', 'Schalthebel 20x10', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 8, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:25:49', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:25:49', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(4, 'produkt', '700004', '', 1, '', 0, '', 'Halter L55', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 7, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:26:02', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:26:02', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(5, 'produkt', '700005', '', 1, '', 0, '', 'Rahmen R12 komplett', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 7, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:26:13', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:26:13', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(6, 'produkt', '700006', '', 1, '', 0, '', 'LED Anzeige RLED 24-8', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 8, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:26:27', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:26:27', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(7, 'produkt', '700007', '', 1, '', 0, '', 'Schalter S3 24V 5A', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 8, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:26:59', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:26:59', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(8, 'produkt', '700008', '', 1, '', 0, '', 'Gehäuse GHK5 20x30x10', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 8, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:27:10', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:27:10', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(9, 'produkt', '700009', '', 1, '', 0, '', 'Gehäusedeckel GHK5 20x30 fertig bearbeitet', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:27:46', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:27:46', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(10, 'produkt', '700010', '', 1, '', 0, '', 'Taster TS1 24V 5A', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 8, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:28:33', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:28:33', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(11, 'produkt', '700011', '', 1, '', 0, '', 'Verschlußklammer VSK 10', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 8, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:28:43', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:28:43', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(12, 'produkt', '700012', '', 1, '', 0, '', 'Handsteuergerät Bausatz', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 1, 1, '', '', '', 0, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:47:03', 'Kabelgebundenes Steuergerät mit Ein-/Ausschalter und 2 Tastern (Vorwärts/Rückwärts)', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:47:03', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(13, 'produkt', '700013', '', 1, '', 0, '', 'Kabel 3 Adern x 0,2qmm 5m konfektioniert', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 1, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:29:21', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:29:21', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', ''),
(14, 'gebuehr', '100001', '', 1, '', 0, '', 'Versandkosten', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'keine', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, 0, 0, 0, 0, '', 0, '0000-00-00', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', 0, 0, 0, '', 1, '2015-10-26 16:47:05', '', 0, 0, '', 0, 0.00, 0, 0, '', 0, 0, 0, 0.00, 0, 0, 0, 0.00, 0, '', 0, '', 0.00, 0.00, 0, 0, 0, 2, '2015-10-26 16:47:05', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 0, '', '', 'DE', 0.00, 0.00, 0.00, 0, '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', '', '');

--
-- Daten für Tabelle `aufgabe`
--

INSERT INTO `aufgabe` (`id`, `adresse`, `aufgabe`, `beschreibung`, `prio`, `projekt`, `kostenstelle`, `initiator`, `angelegt_am`, `startdatum`, `startzeit`, `intervall_tage`, `stunden`, `abgabe_bis`, `abgeschlossen`, `abgeschlossen_am`, `sonstiges`, `bearbeiter`, `logdatei`, `startseite`, `oeffentlich`, `emailerinnerung`, `emailerinnerung_tage`, `note_x`, `note_y`, `note_z`, `note_color`, `pinwand`, `vorankuendigung`, `status`, `ganztags`, `zeiterfassung_pflicht`, `zeiterfassung_abrechnung`, `kunde`, `pinwand_id`, `sort`, `abgabe_bis_zeit`, `email_gesendet_vorankuendigung`, `email_gesendet`) VALUES
(1, 1, 'Vorbereitung der Umsatzsteuervoranmeldung', 'Vorbereitung der Umsatzsteuervoranmeldung', '', 0, 0, 1, '0000-00-00', '0000-00-00', '00:00:00', 0, NULL, '0000-00-00', 0, '0000-00-00', '', '', '2015-10-26 16:26:22', 1, NULL, NULL, NULL, 21, 162, 2, 'yellow', 1, NULL, 'offen', 1, 0, 0, NULL, 0, NULL, NULL, 0, 0),
(2, 1, 'Neuen Mitarbeiter in Versand einweisen', 'Neuen Mitarbeiter in Versand einweisen', '', 0, 0, 1, '0000-00-00', '0000-00-00', '00:00:00', 0, NULL, '0000-00-00', 0, NOW(), '', '', '2015-10-26 16:28:09', 1, NULL, NULL, NULL, 248, 61, 4, 'blue', 1, NULL, 'abgeschlossen', 1, 0, 0, NULL, 0, NULL, NULL, 0, 0),
(3, 1, 'Blumen im Büro gießen', 'Blumen im Büro gießen', '', 0, 0, 1, '0000-00-00', '0000-00-00', '00:00:00', 0, NULL, '0000-00-00', 0, '0000-00-00', '', '', '2015-10-26 16:28:27', 1, 1, NULL, NULL, 448, 242, 4, 'green', 1, NULL, 'offen', 1, 0, 0, NULL, 0, NULL, NULL, 0, 0),
(4, 1, 'Vorbereiten auf Bewerbungsgespräch', 'Vorbereiten auf Bewerbungsgespräch', '', 0, 0, 1, '0000-00-00', '0000-00-00', '00:00:00', 0, NULL, '0000-00-00', 0, '0000-00-00', '', '', '2015-10-26 16:29:18', NULL, NULL, NULL, NULL, 243, 72, 6, 'blue', 1, NULL, 'offen', 1, 0, 0, NULL, 0, NULL, NULL, 0, 0);

--
-- Daten für Tabelle `auftrag`
--

INSERT INTO `auftrag` (`id`, `datum`, `art`, `projekt`, `belegnr`, `internet`, `bearbeiter`, `angebot`, `freitext`, `internebemerkung`, `status`, `adresse`, `name`, `abteilung`, `unterabteilung`, `strasse`, `adresszusatz`, `ansprechpartner`, `plz`, `ort`, `land`, `ustid`, `ust_befreit`, `ust_inner`, `email`, `telefon`, `telefax`, `betreff`, `kundennummer`, `versandart`, `vertrieb`, `zahlungsweise`, `zahlungszieltage`, `zahlungszieltageskonto`, `zahlungszielskonto`, `bank_inhaber`, `bank_institut`, `bank_blz`, `bank_konto`, `kreditkarte_typ`, `kreditkarte_inhaber`, `kreditkarte_nummer`, `kreditkarte_pruefnummer`, `kreditkarte_monat`, `kreditkarte_jahr`, `firma`, `versendet`, `versendet_am`, `versendet_per`, `versendet_durch`, `autoversand`, `keinporto`, `keinestornomail`, `abweichendelieferadresse`, `liefername`, `lieferabteilung`, `lieferunterabteilung`, `lieferland`, `lieferstrasse`, `lieferort`, `lieferplz`, `lieferadresszusatz`, `lieferansprechpartner`, `packstation_inhaber`, `packstation_station`, `packstation_ident`, `packstation_plz`, `packstation_ort`, `autofreigabe`, `freigabe`, `nachbesserung`, `gesamtsumme`, `inbearbeitung`, `abgeschlossen`, `nachlieferung`, `lager_ok`, `porto_ok`, `ust_ok`, `check_ok`, `vorkasse_ok`, `nachnahme_ok`, `reserviert_ok`, `partnerid`, `folgebestaetigung`, `zahlungsmail`, `stornogrund`, `stornosonstiges`, `stornorueckzahlung`, `stornobetrag`, `stornobankinhaber`, `stornobankkonto`, `stornobankblz`, `stornobankbank`, `stornogutschrift`, `stornogutschriftbeleg`, `stornowareerhalten`, `stornomanuellebearbeitung`, `stornokommentar`, `stornobezahlt`, `stornobezahltam`, `stornobezahltvon`, `stornoabgeschlossen`, `stornorueckzahlungper`, `stornowareerhaltenretour`, `partnerausgezahlt`, `partnerausgezahltam`, `kennen`, `logdatei`, `keinetrackingmail`, `zahlungsmailcounter`, `rma`, `transaktionsnummer`, `vorabbezahltmarkieren`, `deckungsbeitragcalc`, `deckungsbeitrag`, `erloes_netto`, `umsatz_netto`, `lieferdatum`, `tatsaechlicheslieferdatum`, `liefertermin_ok`, `teillieferung_moeglich`, `kreditlimit_ok`, `kreditlimit_freigabe`, `liefersperre_ok`, `teillieferungvon`, `teillieferungnummer`, `vertriebid`, `aktion`, `provision`, `provision_summe`, `anfrageid`, `gruppe`, `shopextid`, `shopextstatus`, `ihrebestellnummer`, `anschreiben`, `usereditid`, `useredittimestamp`, `realrabatt`, `rabatt`, `einzugsdatum`, `rabatt1`, `rabatt2`, `rabatt3`, `rabatt4`, `rabatt5`, `shop`, `steuersatz_normal`, `steuersatz_zwischen`, `steuersatz_ermaessigt`, `steuersatz_starkermaessigt`, `steuersatz_dienstleistung`, `waehrung`, `keinsteuersatz`, `angebotid`, `schreibschutz`, `pdfarchiviert`, `pdfarchiviertversion`, `typ`, `ohne_briefpapier`, `auftragseingangper`, `lieferid`, `ansprechpartnerid`, `systemfreitext`, `projektfiliale`) VALUES
(1, NOW(), '', '1', '200000', '', 'Administrator2', '100000', '', '', 'freigegeben', 3, 'Max Muster', '', '', 'Musterstrasse 6', '', '', '12345', 'Musterdorf', 'DE', '', 0, 0, 'info@maxmuellermuster.de', '0821123456789', '0821123456790', '', '10000', 'versandunternehmen', 'Administrator2', 'rechnung', 14, 10, 2.00, '', '', '', '', '', '', '', '', '', '', 1, 0, '0000-00-00 00:00:00', '', '', 1, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 737.80, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, '0000-00-00', '0000-00-00', '', '', '', 0.00, '', '', '', '', 0, '', 0, '', '', '', '0000-00-00', '', 0, '', 0, 0, '0000-00-00', '', '2015-10-26 16:23:45', NULL, NULL, 0, '', 0, 1, 63.50, 393.70, 620.00, '0000-00-00', NULL, 1, 0, 1, 0, 1, 0, 0, 0, '', 0.00, 0.00, 0, 0, '', '', '', '', 2, '2015-10-26 16:23:45', NULL, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', 0, 1, 0, 0, 0, 'firma', NULL, '', 0, 0, '', 0),
(2, NOW(), 'standardauftrag', '1', '200001', '', 'Administrator2', '', '', '', 'freigegeben', 4, 'Eva Müller', '', '', 'Musterweg 12a', '', '', '12345', 'Musterdorf', 'DE', '', 0, 0, '', '089123456789', '089123456790', '', '10001', 'versandunternehmen', 'Administrator2', 'vorkasse', 14, 10, 2.00, '', '', '', '', 'MasterCard', '', '', '', '1', '2009', 1, 0, '0000-00-00 00:00:00', '', '', 1, 0, 0, 0, '', '', '', 'DE', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 73.78, 0, 0, 0, 0, 1, 1, 1, 0, 1, 0, 0, '0000-00-00', '0000-00-00', '', '', '', 0.00, '', '', '', '', 0, '', 0, '', '', '', '0000-00-00', '', 0, '', 0, 0, '0000-00-00', '', '2015-10-26 16:40:29', 0, 0, 0, '', 0, 1, 63.50, 39.37, 62.00, '0000-00-00', '0000-00-00', 1, 1, 1, 0, 1, 0, 0, 0, '', 0.00, 0.00, 0, 0, '', '', '', '', 2, '2015-10-26 16:23:37', 0.00, 0.00, '0000-00-00', 0.00, 0.00, 0.00, 0.00, 0.00, 0, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', 0, 0, 0, 0, 0, 'frau', 0, 'internet', 0, 0, '', 0),
(3, NOW(), '', '1', '200002', '', 'Administrator2', '', '', '', 'freigegeben', 5, 'Hans Huber', '', '', 'Musterstrasse 6', '', '', '12345', 'Musterstadt', 'DE', '', 0, 0, 'hans@huberhausen.de', '017123456745', '', '', '10002', 'versandunternehmen', 'Administrator2', 'rechnung', 14, 10, 2.00, '', '', '', '', '', '', '', '', '', '', 1, 0, '0000-00-00 00:00:00', '', '', 1, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 368.90, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, '0000-00-00', '0000-00-00', '', '', '', 0.00, '', '', '', '', 0, '', 0, '', '', '', '0000-00-00', '', 0, '', 0, 0, '0000-00-00', '', '2015-10-26 16:40:29', NULL, NULL, 0, '', 0, 1, 32.77, 101.60, 310.00, NULL, NULL, 1, 1, 1, 0, 1, 0, 0, 0, '', 0.00, NULL, 0, 0, '', '', NULL, '', 2, '2015-10-26 16:23:28', NULL, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', NULL, NULL, 0, 0, 0, 'firma', 0, '', 0, 0, '', 0),
(4, NOW(), '', '1', '200003', '', 'Administrator2', '', '', '', 'freigegeben', 5, 'Hans Huber', '', '', 'Musterstrasse 6', '', '', '12345', 'Musterstadt', 'DE', '', 0, 0, 'hans@huberhausen.de', '017123456745', '', '', '10002', 'versandunternehmen', 'Administrator2', 'rechnung', 14, 10, 2.00, '', '', '', '', '', '', '', '', '', '', 1, 0, '0000-00-00 00:00:00', '', '', 1, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 10.17, 0, 0, 0, 1, 1, 1, 1, 1, 1, 0, 0, '0000-00-00', '0000-00-00', '', '', '', 0.00, '', '', '', '', 0, '', 0, '', '', '', '0000-00-00', '', 0, '', 0, 0, '0000-00-00', '', '2015-10-26 16:30:20', NULL, NULL, 0, '', 0, 1, 85.96, 7.35, 8.55, NULL, NULL, 1, 0, 1, 0, 1, 0, 0, 0, '', 0.00, NULL, 0, 0, '', '', NULL, '', 2, '2015-10-26 16:30:20', NULL, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', NULL, NULL, 0, 0, 0, 'firma', 0, '', 0, 0, '', 0),
(5, NOW(), '', '1', '200004', '', 'Administrator2', '', '', '', 'abgeschlossen', 5, 'Hans Huber', '', '', 'Musterstrasse 6', '', '', '12345', 'Musterstadt', 'DE', '', 0, 0, 'hans@huberhausen.de', '017123456745', '', '', '10002', 'versandunternehmen', 'Administrator', 'rechnung', 14, 10, 2.00, '', '', '', '', '', '', '', '', '', '', 1, 0, '0000-00-00 00:00:00', '', '', 1, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 10.17, 0, 0, 0, 1, 1, 1, 1, 1, 1, 0, 0, '0000-00-00', '0000-00-00', '', '', '', 0.00, '', '', '', '', 0, '', 0, '', '', '', '0000-00-00', '', 0, '', 0, 0, '0000-00-00', '', '2015-10-26 16:40:09', NULL, NULL, 0, '', 0, 1, 85.96, 7.35, 8.55, '0000-00-00', NULL, 1, 0, 1, 0, 1, 0, 0, 0, '', 0.00, 0.00, 0, 0, '', '', '', NULL, 1, '2015-10-26 16:40:09', NULL, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', NULL, 0, 1, 0, 0, 'firma', NULL, '', 0, 0, '', 0);

--
-- Daten für Tabelle `auftrag_position`
--

INSERT INTO `auftrag_position` (`id`, `auftrag`, `artikel`, `projekt`, `bezeichnung`, `beschreibung`, `internerkommentar`, `nummer`, `menge`, `preis`, `waehrung`, `lieferdatum`, `vpe`, `sort`, `status`, `umsatzsteuer`, `bemerkung`, `geliefert`, `geliefert_menge`, `explodiert`, `explodiert_parent`, `logdatei`, `punkte`, `bonuspunkte`, `mlmdirektpraemie`, `keinrabatterlaubt`, `grundrabatt`, `rabattsync`, `rabatt1`, `rabatt2`, `rabatt3`, `rabatt4`, `rabatt5`, `einheit`, `webid`, `rabatt`, `nachbestelltexternereinkauf`, `zolltarifnummer`, `herkunftsland`) VALUES
(1, 1, 12, 1, 'Handsteuergerät', 'Kabelgebundenes Steuergerät mit Ein-/Ausschalter und 2 Tastern (Vorwärts/Rückwärts)', '', '700012', 10, 62.0000, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 0, 0, 1, 0, '2015-10-26 16:23:43', 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, '', NULL, 0.00, NULL, '0', '0'),
(2, 2, 12, 1, 'Handsteuergerät', 'Kabelgebundenes Steuergerät mit Ein-/Ausschalter und 2 Tastern (Vorwärts/Rückwärts)', '', '700012', 1, 62.0000, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 0, 0, 1, 0, '2015-10-26 16:23:35', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(3, 3, 12, 1, 'Handsteuergerät', 'Kabelgebundenes Steuergerät mit Ein-/Ausschalter und 2 Tastern (Vorwärts/Rückwärts)', '', '700012', 5, 62.0000, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 0, 0, 1, 0, '2015-10-26 16:23:07', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(4, 3, 1, 1, 'Schraube M10x20', '', '', '700001', 20, 0.0000, '', '0000-00-00', '', 2, 'angelegt', '', '', 0, 0, 0, 3, '2015-10-26 16:23:06', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(5, 3, 8, 1, 'Gehäuse GHK5 20x30x10', '', '', '700008', 5, 0.0000, '', '0000-00-00', '', 3, 'angelegt', '', '', 0, 0, 0, 3, '2015-10-26 16:23:06', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(6, 3, 9, 1, 'Gehäusedeckel GHK5 20x30 fertig bearbeitet', '', '', '700009', 5, 0.0000, '', '0000-00-00', '', 4, 'angelegt', '', '', 0, 0, 0, 3, '2015-10-26 16:23:06', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(7, 3, 13, 1, 'Kabel 3 Adern x 0,2qmm 5m konfektioniert', '', '', '700013', 5, 0.0000, '', '0000-00-00', '', 5, 'angelegt', '', '', 0, 0, 0, 3, '2015-10-26 16:23:06', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(8, 3, 10, 1, 'Taster TS1 24V 5A', '', '', '700010', 10, 0.0000, '', '0000-00-00', '', 6, 'angelegt', '', '', 0, 0, 0, 3, '2015-10-26 16:23:06', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(9, 3, 11, 1, 'Verschlußklammer VSK 10', '', '', '700011', 10, 0.0000, '', '0000-00-00', '', 7, 'angelegt', '', '', 0, 0, 0, 3, '2015-10-26 16:23:07', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(10, 2, 1, 1, 'Schraube M10x20', '', '', '700001', 4, 0.0000, '', '0000-00-00', '', 2, 'angelegt', '', '', 0, 0, 0, 2, '2015-10-26 16:23:35', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(11, 2, 8, 1, 'Gehäuse GHK5 20x30x10', '', '', '700008', 1, 0.0000, '', '0000-00-00', '', 3, 'angelegt', '', '', 0, 0, 0, 2, '2015-10-26 16:23:35', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(12, 2, 9, 1, 'Gehäusedeckel GHK5 20x30 fertig bearbeitet', '', '', '700009', 1, 0.0000, '', '0000-00-00', '', 4, 'angelegt', '', '', 0, 0, 0, 2, '2015-10-26 16:23:35', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(13, 2, 13, 1, 'Kabel 3 Adern x 0,2qmm 5m konfektioniert', '', '', '700013', 1, 0.0000, '', '0000-00-00', '', 5, 'angelegt', '', '', 0, 0, 0, 2, '2015-10-26 16:23:35', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(14, 2, 10, 1, 'Taster TS1 24V 5A', '', '', '700010', 2, 0.0000, '', '0000-00-00', '', 6, 'angelegt', '', '', 0, 0, 0, 2, '2015-10-26 16:23:35', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(15, 2, 11, 1, 'Verschlußklammer VSK 10', '', '', '700011', 2, 0.0000, '', '0000-00-00', '', 7, 'angelegt', '', '', 0, 0, 0, 2, '2015-10-26 16:23:35', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(16, 1, 1, 1, 'Schraube M10x20', '', '', '700001', 40, 0.0000, '', '0000-00-00', '', 2, 'angelegt', '', '', 0, 0, 0, 1, '2015-10-26 16:23:43', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(17, 1, 8, 1, 'Gehäuse GHK5 20x30x10', '', '', '700008', 10, 0.0000, '', '0000-00-00', '', 3, 'angelegt', '', '', 0, 0, 0, 1, '2015-10-26 16:23:43', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(18, 1, 9, 1, 'Gehäusedeckel GHK5 20x30 fertig bearbeitet', '', '', '700009', 10, 0.0000, '', '0000-00-00', '', 4, 'angelegt', '', '', 0, 0, 0, 1, '2015-10-26 16:23:43', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(19, 1, 13, 1, 'Kabel 3 Adern x 0,2qmm 5m konfektioniert', '', '', '700013', 10, 0.0000, '', '0000-00-00', '', 5, 'angelegt', '', '', 0, 0, 0, 1, '2015-10-26 16:23:43', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(20, 1, 10, 1, 'Taster TS1 24V 5A', '', '', '700010', 20, 0.0000, '', '0000-00-00', '', 6, 'angelegt', '', '', 0, 0, 0, 1, '2015-10-26 16:23:43', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(21, 1, 11, 1, 'Verschlußklammer VSK 10', '', '', '700011', 20, 0.0000, '', '0000-00-00', '', 7, 'angelegt', '', '', 0, 0, 0, 1, '2015-10-26 16:23:43', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(22, 4, 1, 1, 'Schraube M10x20', '', '', '700001', 10, 0.1600, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 0, 0, 0, 0, '2015-10-26 16:29:33', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(23, 4, 14, 1, 'Versandkosten', '', '', '100001', 1, 6.9500, 'EUR', '0000-00-00', '1', 2, 'angelegt', '', '', 0, 0, 0, 0, '2015-10-26 16:30:19', 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0.00, NULL, '0', '0'),
(24, 5, 1, 1, 'Schraube M10x20', '', '', '700001', 10, 0.1600, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 1, 10, 0, 0, '2015-10-26 16:36:23', 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', 0.00, 0, '0', '0'),
(25, 5, 14, 1, 'Versandkosten', '', '', '100001', 1, 6.9500, 'EUR', '0000-00-00', '1', 2, 'angelegt', '', '', 0, 0, 0, 0, '2015-10-26 16:36:01', 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, '', '', 0.00, 0, '0', '0');

--
-- Daten für Tabelle `auftrag_protokoll`
--

INSERT INTO `auftrag_protokoll` (`id`, `auftrag`, `zeit`, `bearbeiter`, `grund`) VALUES
(NULL, 1, '2015-10-26 17:18:32', 'Administrator2', 'Auftrag freigegeben'),
(NULL, 2, '2015-10-26 17:18:36', 'Administrator2', 'Auftrag angelegt'),
(NULL, 2, '2015-10-26 17:19:08', 'Administrator2', 'Auftrag freigegeben'),
(NULL, 3, '2015-10-26 17:19:23', 'Administrator2', 'Auftrag angelegt'),
(NULL, 3, '2015-10-26 17:19:56', 'Administrator2', 'Auftrag freigegeben'),
(NULL, 4, '2015-10-26 17:24:02', 'Administrator2', 'Auftrag angelegt'),
(NULL, 4, '2015-10-26 17:29:36', 'Administrator2', 'Auftrag freigegeben'),
(NULL, 5, '2015-10-26 17:36:07', 'Administrator2', 'Auftrag freigegeben');

--
-- Daten für Tabelle `bestellung`
--

INSERT INTO `bestellung` (`id`, `datum`, `projekt`, `bestellungsart`, `belegnr`, `bearbeiter`, `angebot`, `freitext`, `internebemerkung`, `status`, `adresse`, `name`, `vorname`, `abteilung`, `unterabteilung`, `strasse`, `adresszusatz`, `plz`, `ort`, `land`, `abweichendelieferadresse`, `liefername`, `lieferabteilung`, `lieferunterabteilung`, `lieferland`, `lieferstrasse`, `lieferort`, `lieferplz`, `lieferadresszusatz`, `lieferansprechpartner`, `ustid`, `ust_befreit`, `email`, `telefon`, `telefax`, `betreff`, `kundennummer`, `lieferantennummer`, `versandart`, `lieferdatum`, `einkaeufer`, `keineartikelnummern`, `zahlungsweise`, `zahlungsstatus`, `zahlungszieltage`, `zahlungszieltageskonto`, `zahlungszielskonto`, `gesamtsumme`, `bank_inhaber`, `bank_institut`, `bank_blz`, `bank_konto`, `paypalaccount`, `bestellbestaetigung`, `firma`, `versendet`, `versendet_am`, `versendet_per`, `versendet_durch`, `logdatei`, `artikelnummerninfotext`, `ansprechpartner`, `anschreiben`, `usereditid`, `useredittimestamp`, `steuersatz_normal`, `steuersatz_zwischen`, `steuersatz_ermaessigt`, `steuersatz_starkermaessigt`, `steuersatz_dienstleistung`, `waehrung`, `bestellungohnepreis`, `schreibschutz`, `pdfarchiviert`, `pdfarchiviertversion`, `typ`, `verbindlichkeiteninfo`, `ohne_briefpapier`, `projektfiliale`, `bestellung_bestaetigt`, `bestaetigteslieferdatum`, `bestellungbestaetigtper`, `bestellungbestaetigtabnummer`, `gewuenschteslieferdatum`) VALUES
(1, NOW(), '1', '', '100000', 'Administrator2', '', '', '', 'freigegeben', 8, 'Elektronik Großhandel', '', '', '', 'Musterweg 12a', '', '12345', 'Musterdorf', 'DE', 0, '', '', '', '', '', '', '', '', '', '', 0, 'elektronik@grosshandel.de', '12345678', '', '', '', '70001', '', '0000-00-00', 'Administrator2', 0, '', '', 0, 0, 0.00, 22.9551, '', '', 0, 0, '', 0, 1, 0, '0000-00-00 00:00:00', '', '', '2015-10-26 16:15:22', 1, NULL, '', 2, '2015-10-26 16:15:22', 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', 0, 0, 0, 0, 'firma', '', 0, 0, 0, NULL, '', '', NULL),
(2, NOW(), '1', '', '100001', 'Administrator2', '', '', '', 'freigegeben', 7, 'Schrauben Meier', '', '', '', 'Musterstrasse 6', '', '12345', 'Musterdorf', 'DE', 0, '', '', '', '', '', '', '', '', '', '', 0, 'schrauben@meiermusterdorf.de', '12345678', '', '', '', '70000', '', '0000-00-00', 'Administrator2', 0, '', '', 0, 0, 0.00, 29.7500, '', '', 0, 0, '', 0, 1, 0, '0000-00-00 00:00:00', '', '', '2015-10-26 16:15:50', 1, NULL, '', 2, '2015-10-26 16:15:50', 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', 0, 0, 0, 0, 'firma', '', 0, 0, 0, NULL, '', '', NULL);

--
-- Daten für Tabelle `bestellung_position`
--

INSERT INTO `bestellung_position` (`id`, `bestellung`, `artikel`, `projekt`, `bezeichnunglieferant`, `bestellnummer`, `beschreibung`, `menge`, `preis`, `waehrung`, `lieferdatum`, `vpe`, `sort`, `status`, `umsatzsteuer`, `bemerkung`, `geliefert`, `mengemanuellgeliefertaktiviert`, `manuellgeliefertbearbeiter`, `abgerechnet`, `logdatei`, `abgeschlossen`, `einheit`, `zolltarifnummer`, `herkunftsland`) VALUES
(1, 1, 3, 1, 'SCHALT001', '', '', 1, 2.3900, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 0, 0, '', 0, '2015-10-26 16:14:46', NULL, '', '0', '0'),
(3, 1, 11, 1, 'KLAMMER001', '', '', 20, 0.2300, 'EUR', '0000-00-00', '1', 2, 'angelegt', '', '', 0, 0, '', 0, '2015-10-26 16:15:12', NULL, '', '0', '0'),
(4, 1, 10, 1, 'TASTER001', '', '', 10, 1.2300, 'EUR', '0000-00-00', '1', 3, 'angelegt', '', '', 0, 0, '', 0, '2015-10-26 16:15:10', NULL, '', '0', '0'),
(5, 2, 1, 1, 'Schraube M10x20', '', '', 100, 0.1200, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', 0, 0, '', 0, '2015-10-26 16:15:43', NULL, '', '0', '0'),
(6, 2, 2, 1, 'Mutter M10', '124345', '', 100, 0.1300, 'EUR', '0000-00-00', '1', 2, 'angelegt', '', '', 0, 0, '', 0, '2015-10-26 16:15:46', NULL, '', '0', '0');

--
-- Daten für Tabelle `bestellung_protokoll`
--

INSERT INTO `bestellung_protokoll` (`id`, `bestellung`, `zeit`, `bearbeiter`, `grund`) VALUES
(NULL, 1, '2015-10-26 17:14:34', 'Administrator2', 'Bestellung angelegt'),
(NULL, 1, '2015-10-26 17:15:15', 'Administrator2', 'Bestellung freigegeben'),
(NULL, 2, '2015-10-26 17:15:29', 'Administrator2', 'Bestellung angelegt'),
(NULL, 2, '2015-10-26 17:15:49', 'Administrator2', 'Bestellung freigegeben');

--
-- Daten für Tabelle `datei`
--

INSERT INTO `datei` (`id`, `titel`, `beschreibung`, `nummer`, `geloescht`, `logdatei`, `firma`) VALUES
(1, '', '', '', 0, '2015-10-26 16:17:26', 1),
(2, 'lieferschein', '', '', 0, '2015-10-26 16:36:23', 1);

--
-- Daten für Tabelle `datei_stichwoerter`
--

INSERT INTO `datei_stichwoerter` (`id`, `datei`, `subjekt`, `objekt`, `parameter`, `logdatei`) VALUES
(NULL, 1, 'angebot', 'angebot', '1', '2015-10-26 16:17:26'),
(NULL, 2, 'lieferschein', 'lieferschein', '1', '2015-10-26 16:36:24');

--
-- Daten für Tabelle `datei_version`
--

INSERT INTO `datei_version` (`id`, `datei`, `ersteller`, `datum`, `version`, `dateiname`, `bemerkung`, `logdatei`) VALUES
(1, 1, 'Administrator2', NOW(), 1, '20151026_AN100000.pdf', 'Initiale Version', '2015-10-26 16:17:26'),
(2, 2, 'Administrator2', NOW(), 1, '20151026_LS300000.pdf', 'Initiale Version', '2015-10-26 16:36:23');

--
-- Daten für Tabelle `dokumente_send`
--

INSERT INTO `dokumente_send` (`id`, `dokument`, `zeit`, `bearbeiter`, `adresse`, `ansprechpartner`, `projekt`, `parameter`, `art`, `betreff`, `text`, `geloescht`, `versendet`, `logdatei`, `dateiid`) VALUES
(NULL, 'angebot', '2015-10-26 17:17:26', 'Administrator2', 3, 'Max Muster <info@maxmuellermuster.de>', 1, 1, 'sonstiges', 'Ihr Angebot von Musterfirma GmbH', 'Sehr geehrter Herr \r\n\r\n\r\nanbei das gewünschte Angebot. Wir hoffen Ihnen die passenden Artikel anbieten zu können.\r\n\r\n\r\nMit freundlichen Grüßen\r\n\r\nAdministrator2', 0, 1, '2015-10-26 16:17:26', 1),
(NULL, 'lieferschein', '2015-10-26 17:36:24', 'Administrator2', 5, '', 1, 1, 'versand', 'Mitgesendet bei Lieferung', '', 0, 0, '2015-10-26 16:36:24', 2);

--
-- Daten für Tabelle `einkaufspreise`
--

INSERT INTO `einkaufspreise` (`id`, `artikel`, `adresse`, `objekt`, `projekt`, `preis`, `waehrung`, `ab_menge`, `vpe`, `preis_anfrage_vom`, `gueltig_bis`, `lieferzeit_standard`, `lieferzeit_aktuell`, `lager_lieferant`, `datum_lagerlieferant`, `bestellnummer`, `bezeichnunglieferant`, `sicherheitslager`, `bemerkung`, `bearbeiter`, `logdatei`, `standard`, `geloescht`, `firma`, `apichange`) VALUES
(NULL, 5, 7, '', '', 12.9200, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', '123456', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 5, 7, '', '', 10.4500, 'EUR', 10, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', '123456', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 4, 7, '', '', 47.2000, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', '838232', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 3, 8, '', '', 2.3900, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'SCHALT001', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 3, 8, '', '', 1.9200, 'EUR', 100, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'SCHALT001', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 2, 7, '', '', 0.1300, 'EUR', 100, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '124345', 'Mutter M10', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 1, 7, '', '', 0.1200, 'EUR', 100, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'Schraube M10x20', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 11, 8, '', '', 0.2300, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'KLAMMER001', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 10, 8, '', '', 1.2300, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'TASTER001', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 8, 8, '', '', 19.2300, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'GEHK100', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 7, 8, '', '', 3.5400, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'SCHALT002', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 6, 8, '', '', 7.3200, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'LED001', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 9, 8, '', '', 5.8200, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'DECKEL001', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0),
(NULL, 13, 8, '', '', 13.2300, 'EUR', 1, '1', '0000-00-00', '0000-00-00', 0, 0, 0, '0000-00-00', '', 'SPEZIALKABEL001', 0, '', '', '0000-00-00 00:00:00', 0, 0, 1, 0);




--
-- Daten für Tabelle `kalender_event`
--

INSERT INTO `kalender_event` (`id`, `kalender`, `bezeichnung`, `beschreibung`, `von`, `bis`, `allDay`, `color`, `public`, `ort`) VALUES
(1, 0, 'Telefontermin Fa. Klinger', 'Erstgespräch zu möglicher Kooperation', concat(YEAR(now()),'-',MONTH(now()),'-27 13:00:00'), concat(YEAR(now()),'-',MONTH(now()),'-27 13:30:00'), 0, '#7592A0', 0, 'Büro'),
(2, 0, 'Besuch durch Herrn Bäumlinger', '', concat(YEAR(now()),'-',MONTH(now()),'-22 09:30:00'), concat(YEAR(now()),'-',MONTH(now()),'-22 10:00:00'), 0, '', 1, 'Büro'),
(3, 0, 'Gemeinsames Mittagessen', '', concat(YEAR(now()),'-',MONTH(now()),'-07 10:30:00'), concat(YEAR(now()),'-',MONTH(now()),'-07 11:30:00'), 0, '#004704', 1, 'Zum goldenen Ochsen, Hauptstraße'),
(4, 0, 'Besprechung Vertrieb', '', concat(YEAR(now()),'-',MONTH(now()),'-17 14:00:00'), concat(YEAR(now()),'-',MONTH(now()),'-17 16:00:00'), 0, '#FF8128', 1, 'Besprechungsraum Erdgeschoß'),
(5, 0, 'ISO 9001 Audit', 'Reguläres Audit durch Herrn Richard vom TÜV Süd', concat(YEAR(now()),'-',MONTH(now()),'-28 23:00:00'), concat(YEAR(now()),'-',MONTH(now()),'-30 23:00:00'), 1, '#C40046', 1, 'Gesamte Firma');

--
-- Daten für Tabelle `kalender_user`
--

INSERT INTO `kalender_user` (`id`, `event`, `userid`) VALUES
(NULL, 2, 1),
(NULL, 3, 1),
(NULL, 4, 1),
(NULL, 5, 1),
(NULL, 1, 1);

--
-- Daten für Tabelle `lager_bewegung`
--

INSERT INTO `lager_bewegung` (`id`, `lager_platz`, `artikel`, `menge`, `vpe`, `eingang`, `zeit`, `referenz`, `bearbeiter`, `projekt`, `firma`, `logdatei`, `adresse`, `bestand`, `permanenteinventur`) VALUES
(NULL, 1, 1, 32, 'einzeln', 1, '2015-10-26 17:12:17', 'Differenz: ', 'Administrator2', 0, 0, '2015-10-26 16:12:17', NULL, 32, 0),
(NULL, 1, 2, 93, 'einzeln', 1, '2015-10-26 17:12:31', 'Differenz: ', 'Administrator2', 0, 0, '2015-10-26 16:12:31', NULL, 93, 0),
(NULL, 2, 3, 12, 'einzeln', 1, '2015-10-26 17:12:49', 'Differenz: ', 'Administrator2', 0, 0, '2015-10-26 16:12:49', NULL, 12, 0),
(NULL, 2, 4, 12, 'einzeln', 1, '2015-10-26 17:13:01', 'Differenz: ', 'Administrator2', 0, 0, '2015-10-26 16:13:01', NULL, 12, 0),
(NULL, 3, 5, 2, 'einzeln', 1, '2015-10-26 17:13:13', 'Differenz: ', 'Administrator2', 0, 0, '2015-10-26 16:13:13', NULL, 2, 0),
(NULL, 3, 7, 12, 'einzeln', 1, '2015-10-26 17:13:26', 'Differenz: ', 'Administrator2', 0, 0, '2015-10-26 16:13:26', NULL, 12, 0),
(NULL, 3, 8, 2, 'einzeln', 1, '2015-10-26 17:13:37', 'Differenz: ', 'Administrator2', 0, 0, '2015-10-26 16:13:37', NULL, 2, 0),
(NULL, 1, 9, 1, 'einzeln', 1, '2015-10-26 17:13:46', 'Differenz: ', 'Administrator2', 0, 0, '2015-10-26 16:13:46', NULL, 1, 0),
(NULL, 1, 10, 14, 'einzeln', 1, '2015-10-26 17:13:56', 'Differenz: ', 'Administrator2', 0, 0, '2015-10-26 16:13:56', NULL, 14, 0),
(NULL, 1, 1, 10, '', 0, '2015-10-26 17:36:23', 'Lieferschein 300000', 'Administrator2', 1, 0, '0000-00-00 00:00:00', NULL, 22, 0);

--
-- Daten für Tabelle `lager_platz`
--

INSERT INTO `lager_platz` (`id`, `lager`, `kurzbezeichnung`, `bemerkung`, `projekt`, `firma`, `geloescht`, `logdatei`, `autolagersperre`, `verbrauchslager`, `sperrlager`, `laenge`, `breite`, `hoehe`, `poslager`) VALUES
(2, 1, 'Lagerplatz2', '', 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 10.00, 30.00, 20.00, 0),
(3, 1, 'Lagerplatz3', '', 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 10.00, 30.00, 20.00, 0),
(4, 1, 'HL002', '', 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 10.00, 30.00, 20.00, 0),
(5, 1, 'HL003', '', 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 100.00, 100.00, 100.00, 0);

--
-- Daten für Tabelle `lager_platz_inhalt`
--

INSERT INTO `lager_platz_inhalt` (`id`, `lager_platz`, `artikel`, `menge`, `vpe`, `bearbeiter`, `bestellung`, `projekt`, `firma`, `logdatei`, `inventur`) VALUES
(NULL, 1, 2, 93, '', '', 0, 0, 0, '0000-00-00 00:00:00', NULL),
(NULL, 2, 3, 12, '', '', 0, 0, 0, '0000-00-00 00:00:00', NULL),
(NULL, 2, 4, 12, '', '', 0, 0, 0, '0000-00-00 00:00:00', NULL),
(NULL, 3, 5, 2, '', '', 0, 0, 0, '0000-00-00 00:00:00', NULL),
(NULL, 3, 7, 12, '', '', 0, 0, 0, '0000-00-00 00:00:00', NULL),
(NULL, 3, 8, 2, '', '', 0, 0, 0, '0000-00-00 00:00:00', NULL),
(NULL, 1, 9, 1, '', '', 0, 0, 0, '0000-00-00 00:00:00', NULL),
(NULL, 1, 10, 14, '', '', 0, 0, 0, '0000-00-00 00:00:00', NULL),
(NULL, 1, 1, 22, '', '', 0, 0, 0, '0000-00-00 00:00:00', NULL);

--
-- Daten für Tabelle `lieferschein`
--

INSERT INTO `lieferschein` (`id`, `datum`, `projekt`, `lieferscheinart`, `belegnr`, `bearbeiter`, `auftrag`, `auftragid`, `freitext`, `status`, `adresse`, `name`, `abteilung`, `unterabteilung`, `strasse`, `adresszusatz`, `ansprechpartner`, `plz`, `ort`, `land`, `ustid`, `email`, `telefon`, `telefax`, `betreff`, `kundennummer`, `versandart`, `versand`, `firma`, `versendet`, `versendet_am`, `versendet_per`, `versendet_durch`, `inbearbeitung_user`, `logdatei`, `vertriebid`, `vertrieb`, `ust_befreit`, `ihrebestellnummer`, `anschreiben`, `usereditid`, `useredittimestamp`, `lieferantenretoure`, `lieferantenretoureinfo`, `lieferant`, `schreibschutz`, `pdfarchiviert`, `pdfarchiviertversion`, `typ`, `internebemerkung`, `ohne_briefpapier`, `lieferid`, `ansprechpartnerid`, `projektfiliale`, `projektfiliale_eingelagert`) VALUES
(1, NOW(), '1', '', '300000', 'Administrator2', '200004', 5, '', 'versendet', 5, 'Hans Huber', '', '', 'Musterstrasse 6', '', '', '12345', 'Musterstadt', 'DE', '', 'hans@huberhausen.de', '017123456745', '', '', '10002', 'versandunternehmen', 'Administrator2', 1, 1, '0000-00-00 00:00:00', '', '', 0, '2015-10-26 16:39:31', 0, 'Administrator', 0, '', '', 1, '2015-10-26 16:39:31', 0, '', 0, 1, 0, 0, 'firma', '', NULL, 0, 0, 0, 0);

--
-- Daten für Tabelle `lieferschein_position`
--

INSERT INTO `lieferschein_position` (`id`, `lieferschein`, `artikel`, `projekt`, `bezeichnung`, `beschreibung`, `internerkommentar`, `nummer`, `seriennummer`, `menge`, `lieferdatum`, `vpe`, `sort`, `status`, `bemerkung`, `geliefert`, `abgerechnet`, `logdatei`, `explodiert_parent_artikel`, `einheit`, `zolltarifnummer`, `herkunftsland`) VALUES
(NULL, 1, 1, 1, 'Schraube M10x20', '', '', '700001', '', 10, '0000-00-00', '1', 1, 'angelegt', '', 10, 0, '2015-10-26 16:36:23', 0, '', '0', '0'),
(NULL, 1, 14, 1, 'Versandkosten', '', '', '100001', '', 1, '0000-00-00', '1', 2, 'angelegt', '', 1, 0, '2015-10-26 16:36:23', 0, '', '0', '0');

--
-- Daten für Tabelle `lieferschein_protokoll`
--

INSERT INTO `lieferschein_protokoll` (`id`, `lieferschein`, `zeit`, `bearbeiter`, `grund`) VALUES
(NULL, 1, '2015-10-26 17:36:24', 'Administrator2', 'Lieferschein versendet (Auto-Versand)');

--
-- Daten für Tabelle `objekt_protokoll`
--

INSERT INTO `objekt_protokoll` (`id`, `objekt`, `objektid`, `action_long`, `meldung`, `bearbeiter`, `zeitstempel`) VALUES
(NULL, 'lager', 1, 'lager_create', 'Lager angelegt', 'Administrator', '2015-10-26 16:34:09'),
(NULL, 'artikel', 1, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:37:47'),
(NULL, 'artikel', 2, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:38:17'),
(NULL, 'artikel', 3, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:38:53'),
(NULL, 'artikel', 4, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:51:03'),
(NULL, 'artikel', 5, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:51:37'),
(NULL, 'artikel', 6, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:53:19'),
(NULL, 'artikel', 7, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:53:55'),
(NULL, 'artikel', 8, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:55:01'),
(NULL, 'artikel', 9, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:55:36'),
(NULL, 'artikel', 10, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:56:29'),
(NULL, 'adresse', 3, 'adresse_create', 'Adresse angelegt', 'Administrator2', '2015-10-26 16:56:48'),
(NULL, 'artikel', 11, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 16:56:50'),
(NULL, 'adresse', 3, 'adresse_next_kundennummer', 'Kundennummer erhalten: 10000', 'Administrator2', '2015-10-26 16:56:52'),
(NULL, 'adresse', 4, 'adresse_create', 'Adresse angelegt', 'Administrator2', '2015-10-26 16:57:45'),
(NULL, 'adresse', 4, 'adresse_next_kundennummer', 'Kundennummer erhalten: 10001', 'Administrator2', '2015-10-26 16:57:49'),
(NULL, 'adresse', 5, 'adresse_create', 'Adresse angelegt', 'Administrator2', '2015-10-26 16:59:07'),
(NULL, 'adresse', 5, 'adresse_next_kundennummer', 'Kundennummer erhalten: 10002', 'Administrator2', '2015-10-26 16:59:10'),
(NULL, 'adresse', 6, 'adresse_create', 'Adresse angelegt', 'Administrator2', '2015-10-26 17:00:23'),
(NULL, 'adresse', 6, 'adresse_next_mitarbeiternummer', 'Mitarbeiternummer erhalten: 90000', 'Administrator2', '2015-10-26 17:00:28'),
(NULL, 'artikel', 12, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 17:04:40'),
(NULL, 'adresse', 7, 'adresse_create', 'Adresse angelegt', 'Administrator2', '2015-10-26 17:04:44'),
(NULL, 'adresse', 7, 'adresse_next_lieferantennummer', 'Lieferantennummer erhalten: 70000', 'Administrator2', '2015-10-26 17:04:49'),
(NULL, 'adresse', 8, 'adresse_create', 'Adresse angelegt', 'Administrator2', '2015-10-26 17:05:26'),
(NULL, 'adresse', 8, 'adresse_next_lieferantennummer', 'Lieferantennummer erhalten: 70001', 'Administrator2', '2015-10-26 17:05:42'),
(NULL, 'artikel', 13, 'artikel_create', 'Artikel angelegt', 'Administrator', '2015-10-26 17:06:32'),
(NULL, 'stueckliste', 1, 'stueckliste_create', 'Stueckliste angelegt', 'Administrator', '2015-10-26 17:07:23'),
(NULL, 'stueckliste', 2, 'stueckliste_create', 'Stueckliste angelegt', 'Administrator', '2015-10-26 17:07:34'),
(NULL, 'einkaufspreise', 1, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:07:43'),
(NULL, 'stueckliste', 3, 'stueckliste_create', 'Stueckliste angelegt', 'Administrator', '2015-10-26 17:07:48'),
(NULL, 'stueckliste', 4, 'stueckliste_create', 'Stueckliste angelegt', 'Administrator', '2015-10-26 17:08:04'),
(NULL, 'stueckliste', 5, 'stueckliste_create', 'Stueckliste angelegt', 'Administrator', '2015-10-26 17:08:17'),
(NULL, 'einkaufspreise', 3, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:08:25'),
(NULL, 'einkaufspreise', 4, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:08:48'),
(NULL, 'stueckliste', 6, 'stueckliste_create', 'Stueckliste angelegt', 'Administrator', '2015-10-26 17:09:14'),
(NULL, 'einkaufspreise', 6, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:09:34'),
(NULL, 'stueckliste', 7, 'stueckliste_create', 'Stueckliste angelegt', 'Administrator', '2015-10-26 17:09:53'),
(NULL, 'einkaufspreise', 7, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:09:58'),
(NULL, 'einkaufspreise', 8, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:10:17'),
(NULL, 'einkaufspreise', 9, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:10:40'),
(NULL, 'einkaufspreise', 10, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:11:04'),
(NULL, 'einkaufspreise', 11, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:11:23'),
(NULL, 'einkaufspreise', 12, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:11:46'),
(NULL, 'verkaufspreise', 1, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:17:06'),
(NULL, 'verkaufspreise', 2, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:25:20'),
(NULL, 'verkaufspreise', 3, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:25:35'),
(NULL, 'verkaufspreise', 4, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:25:48'),
(NULL, 'verkaufspreise', 5, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:26:02'),
(NULL, 'verkaufspreise', 6, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:26:12'),
(NULL, 'verkaufspreise', 7, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:26:26'),
(NULL, 'verkaufspreise', 8, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:26:36'),
(NULL, 'verkaufspreise', 9, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:26:47'),
(NULL, 'verkaufspreise', 10, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:27:09'),
(NULL, 'einkaufspreise', 13, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:27:37'),
(NULL, 'verkaufspreise', 11, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:27:46'),
(NULL, 'verkaufspreise', 12, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:28:33'),
(NULL, 'verkaufspreise', 13, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:28:42'),
(NULL, 'einkaufspreise', 14, 'einkaufspreise_create', 'Einkaufspreise angelegt', 'Administrator2', '2015-10-26 17:29:13'),
(NULL, 'verkaufspreise', 14, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:29:21'),
(NULL, 'artikel', 14, 'artikel_create', 'Artikel angelegt', 'Administrator2', '2015-10-26 17:29:47'),
(NULL, 'verkaufspreise', 15, 'verkaufspreise_create', 'Verkaufspreise angelegt', 'Administrator2', '2015-10-26 17:30:04');

--
-- Daten für Tabelle `paketannahme`
--

INSERT INTO `paketannahme` (`id`, `adresse`, `datum`, `verpackungszustand`, `bemerkung`, `foto`, `gewicht`, `bearbeiter`, `projekt`, `vorlage`, `vorlageid`, `zahlung`, `betrag`, `status`, `beipack_rechnung`, `beipack_lieferschein`, `beipack_anschreiben`, `beipack_gesamt`, `bearbeiter_distribution`, `postgrund`, `logdatei`) VALUES
(1, 8, '2015-10-26 17:37:51', 0, '', 0, '', 'Administrator', 1, 'adresse', '8', '', 0.0000, 'angenommen', 0, 0, 0, 0, '', '', '2015-10-26 16:37:51');

--
-- Daten für Tabelle `pdfmirror_md5pool`
--

INSERT INTO `pdfmirror_md5pool` (`id`, `zeitstempel`, `checksum`, `table_id`, `table_name`, `bearbeiter`, `erstesoriginal`) VALUES
(NULL, '2015-10-26 17:17:23', '', 1, 'angebot', 'Administrator2', 1),
(NULL, '2015-10-26 17:17:23', 'a4dc135088a29be6491585eaf49af797', 1, 'angebot', 'Administrator2', 0),
(NULL, '2015-10-26 17:36:23', '', 1, 'lieferschein', 'Administrator2', 1),
(NULL, '2015-10-26 17:36:23', 'aa57f0d5ca21d5d11a6d5755593b4fa5', 1, 'lieferschein', 'Administrator2', 0),
(NULL, '2015-10-26 17:39:58', '', 3, 'angebot', 'Administrator', 1),
(NULL, '2015-10-26 17:39:58', '98113d469e4c218cf22173626a1df7d6', 3, 'angebot', 'Administrator', 0),
(NULL, '2015-10-26 17:40:13', '', 3, 'auftrag', 'Administrator', 1),
(NULL, '2015-10-26 17:40:13', '714589efc1ad89880f8caf08df8dee96', 3, 'auftrag', 'Administrator', 0);

--
-- Daten für Tabelle `pinwand`
--

INSERT INTO `pinwand` (`id`, `name`, `user`) VALUES
(1, 'Erforschen von xentral', 1);

--
-- Daten für Tabelle `pinwand_user`
--

INSERT INTO `pinwand_user` (`id`, `pinwand`, `user`) VALUES
(NULL, 1, 1),
(NULL, 1, 2),
(NULL, 1, 3);


--
-- Daten für Tabelle `protokoll`
--

INSERT INTO `protokoll` (`id`, `meldung`, `dump`, `module`, `action`, `bearbeiter`, `funktionsname`, `datum`, `parameter`, `argumente`) VALUES
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 15:19:42', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 15:19:49', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 15:21:04', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 15:21:07', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 15:28:57', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 15:33:02', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 15:33:10', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 15:33:10', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 15:33:11', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:33:49', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:36:36', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:36:51', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:37:29', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:37:46', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:38:51', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:40:51', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:41:08', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:41:50', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:02', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:02', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:06', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:06', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:10', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:10', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:13', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:13', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:17', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:17', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:23', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:23', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:34', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:34', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:42', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:42', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:50', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:50', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:42:56', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:42:56', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:43:01', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:43:01', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:43:05', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:43:05', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:43:11', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:43:11', 0, ''),
(NULL, '', '', 'firmendaten', 'nextnumber', 'Administrator', 'Run', '2015-10-26 15:43:20', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:43:20', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:43:23', 0, ''),
(NULL, '', '', 'firmendaten', 'edit', 'Administrator', 'Run', '2015-10-26 15:46:58', 0, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'edit', 'Administrator', 'Run', '2015-10-26 15:47:34', 2, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'edit', 'Administrator', 'Run', '2015-10-26 15:47:59', 2, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'edit', 'Administrator', 'Run', '2015-10-26 15:47:59', 2, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'edit', 'Administrator', 'Run', '2015-10-26 15:48:13', 2, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'edit', 'Administrator', 'Run', '2015-10-26 15:48:13', 2, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'edit', 'Administrator', 'Run', '2015-10-26 15:48:34', 3, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'edit', 'Administrator', 'Run', '2015-10-26 15:48:42', 14, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'edit', 'Administrator', 'Run', '2015-10-26 15:49:01', 14, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'edit', 'Administrator', 'Run', '2015-10-26 15:49:09', 13, ''),
(NULL, '', '', 'geschaeftsbrief_vorlagen', 'delete', 'Administrator', 'Run', '2015-10-26 15:49:22', 13, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 16:28:10', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 16:28:16', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 16:28:16', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 16:28:17', 0, ''),
(NULL, '', '', 'prozessstarter', 'edit', 'Administrator', 'Run', '2015-10-26 16:28:33', 6, ''),
(NULL, '', '', 'prozessstarter', 'edit', 'Administrator', 'Run', '2015-10-26 16:28:40', 3, ''),
(NULL, '', '', 'prozessstarter', 'edit', 'Administrator', 'Run', '2015-10-26 16:30:04', 8, ''),
(NULL, '', '', 'prozessstarter', 'edit', 'Administrator', 'Run', '2015-10-26 16:30:11', 8, ''),
(NULL, '', '', 'prozessstarter', 'edit', 'Administrator', 'Run', '2015-10-26 16:30:12', 8, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:33:09', 0, ''),
(NULL, '', '', 'lager', 'create', 'Administrator', 'Run', '2015-10-26 16:33:56', 0, ''),
(NULL, '', '', 'lager', 'create', 'Administrator', 'Run', '2015-10-26 16:34:09', 0, ''),
(NULL, '', '', 'lager', 'edit', 'Administrator', 'Run', '2015-10-26 16:34:09', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:34:12', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:35:16', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:35:16', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:35:36', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:35:36', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:35:56', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:35:56', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:36:08', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:36:08', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:36:29', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator', 'Run', '2015-10-26 16:36:29', 1, ''),
(NULL, '', '', 'lager', 'wert', 'Administrator', 'Run', '2015-10-26 16:36:54', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 16:36:55', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 16:37:02', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator2', 'Run', '2015-10-26 16:37:02', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator2', 'Run', '2015-10-26 16:37:05', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:37:15', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:37:47', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:37:47', 1, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:37:50', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:38:17', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:38:17', 2, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:38:20', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:38:53', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:39:02', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:39:05', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:40:18', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:40:41', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:41:52', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:42:03', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:42:11', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:44:59', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:45:02', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:45:05', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:45:45', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:45:47', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:50:11', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:50:35', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:50:38', 2, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:50:41', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:51:03', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:51:03', 4, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:51:07', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:51:37', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:51:37', 5, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:51:56', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:53:19', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:53:20', 6, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:53:23', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:53:55', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:53:55', 7, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:53:58', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:55:01', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:55:01', 8, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:55:05', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:55:35', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:55:36', 9, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:55:40', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 16:55:42', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 16:55:45', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator2', 'Run', '2015-10-26 16:55:45', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator2', 'Run', '2015-10-26 16:55:46', 0, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 16:55:54', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:56:29', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:56:29', 10, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:56:35', 0, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 16:56:48', 0, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:56:48', 3, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 16:56:49', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:56:50', 11, ''),
(NULL, '', '', 'adresse', 'rollen', 'Administrator2', 'Run', '2015-10-26 16:56:52', 3, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:56:52', 3, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 16:56:56', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:57:14', 11, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 16:57:17', 11, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 16:57:45', 0, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:57:45', 4, ''),
(NULL, '', '', 'adresse', 'rollen', 'Administrator2', 'Run', '2015-10-26 16:57:49', 4, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:57:49', 4, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:58:00', 4, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:58:11', 3, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:58:17', 3, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:58:21', 3, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 16:58:29', 0, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 16:59:07', 0, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:59:07', 5, ''),
(NULL, '', '', 'adresse', 'rollen', 'Administrator2', 'Run', '2015-10-26 16:59:10', 5, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 16:59:10', 5, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 16:59:19', 0, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 17:00:23', 0, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 17:00:23', 6, ''),
(NULL, '', '', 'adresse', 'rollen', 'Administrator2', 'Run', '2015-10-26 17:00:28', 6, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 17:00:28', 6, ''),
(NULL, '', '', 'benutzer', 'create', 'Administrator2', 'Run', '2015-10-26 17:00:34', 0, ''),
(NULL, '', '', 'benutzer', 'create', 'Administrator2', 'Run', '2015-10-26 17:00:53', 0, ''),
(NULL, '', '', 'benutzer', 'edit', 'Administrator2', 'Run', '2015-10-26 17:00:53', 3, ''),
(NULL, '', '', 'benutzer', 'edit', 'Administrator2', 'Run', '2015-10-26 17:01:01', 3, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'benutzer', 'chrights', 'Administrator2', 'Run', '2015-10-26 17:01:14', 0, ''),
(NULL, '', '', 'welcome', 'logout', 'Administrator2', 'Run', '2015-10-26 17:01:17', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 17:01:17', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:01:19', 11, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:01:22', 11, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 17:01:23', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Anton Lechner', 'Run', '2015-10-26 17:01:24', 0, ''),
(NULL, '', '', 'welcome', 'logout', 'Anton Lechner', 'Run', '2015-10-26 17:01:28', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 17:01:28', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 17:01:31', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 17:01:36', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 17:02:32', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:02:32', 3, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator2', 'Run', '2015-10-26 17:02:33', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator2', 'Run', '2015-10-26 17:02:33', 0, ''),
(NULL, '', '', 'artikel', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:02:38', 11, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:02:41', 3, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:02:43', 3, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:02:46', 3, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:02:49', 3, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:02:50', 3, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:02:55', 3, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 17:03:04', 0, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 17:04:10', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 17:04:40', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:04:40', 12, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 17:04:44', 0, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 17:04:44', 7, ''),
(NULL, '', '', 'adresse', 'rollen', 'Administrator2', 'Run', '2015-10-26 17:04:49', 7, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 17:04:49', 7, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 17:04:51', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 17:04:52', 0, ''),
(NULL, '', '', 'adresse', 'create', 'Administrator2', 'Run', '2015-10-26 17:05:26', 0, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 17:05:26', 8, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 17:05:30', 8, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 17:05:35', 8, ''),
(NULL, '', '', 'adresse', 'rollen', 'Administrator2', 'Run', '2015-10-26 17:05:42', 8, ''),
(NULL, '', '', 'adresse', 'edit', 'Administrator2', 'Run', '2015-10-26 17:05:42', 8, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 17:06:32', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:06:32', 13, ''),
(NULL, '', '', 'artikel', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:06:35', 12, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator', 'Run', '2015-10-26 17:06:36', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:06:39', 13, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:06:42', 11, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:06:44', 10, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:06:46', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:06:47', 8, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:06:50', 7, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:06:54', 6, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:06:56', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:06:59', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:07:05', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:07:05', 5, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:07:08', 4, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:07:12', 3, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:07:15', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:07:18', 1, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:07:22', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:07:23', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:07:23', 5, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:07:34', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:07:35', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:07:43', 5, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:07:43', 5, ''),
(NULL, '', '', 'artikel', 'einkaufcopy', 'Administrator2', 'Run', '2015-10-26 17:07:47', 1, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:07:47', 5, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:07:48', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:07:48', 12, ''),
(NULL, '', '', 'artikel', 'einkaufeditpopup', 'Administrator2', 'Run', '2015-10-26 17:07:49', 2, ''),
(NULL, '', '', 'artikel', 'einkaufeditpopup', 'Administrator2', 'Run', '2015-10-26 17:07:58', 2, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:07:59', 5, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:08:04', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:08:04', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:08:06', 4, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:08:17', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:08:18', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:08:25', 4, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:08:25', 4, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:08:31', 7, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:08:32', 3, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:08:48', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:08:48', 3, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:08:48', 3, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:08:52', 12, ''),
(NULL, '', '', 'artikel', 'einkaufcopy', 'Administrator2', 'Run', '2015-10-26 17:08:53', 4, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:08:53', 3, ''),
(NULL, '', '', 'artikel', 'einkaufeditpopup', 'Administrator2', 'Run', '2015-10-26 17:08:56', 5, ''),
(NULL, '', '', 'artikel', 'delstueckliste', 'Administrator', 'Run', '2015-10-26 17:08:58', 5, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:08:58', 12, ''),
(NULL, '', '', 'artikel', 'einkaufeditpopup', 'Administrator2', 'Run', '2015-10-26 17:09:05', 5, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:09:06', 3, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:09:09', 2, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:09:14', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:09:14', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:09:34', 2, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:09:34', 2, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:09:38', 1, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:09:53', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator', 'Run', '2015-10-26 17:09:53', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:09:58', 1, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:09:59', 1, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:10:03', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:10:04', 11, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:10:17', 11, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:10:17', 11, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:10:20', 10, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:10:30', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:10:31', 10, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator', 'Run', '2015-10-26 17:10:33', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:10:40', 10, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:10:40', 10, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:10:43', 8, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:11:00', 0, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:11:04', 8, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:11:04', 8, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:11:05', 0, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:11:07', 7, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:11:23', 7, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:11:24', 7, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:11:28', 6, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:11:46', 6, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:11:46', 6, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:01', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:11', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:17', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:17', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:25', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:31', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:31', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:43', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:49', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:50', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:12:57', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:01', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:01', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:13:04', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:09', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:13', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:14', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:20', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:26', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:26', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:33', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:37', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:37', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:42', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:46', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:46', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:53', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:13:54', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:56', 0, ''),
(NULL, '', '', 'lager', 'bucheneinlagern', 'Administrator2', 'Run', '2015-10-26 17:13:57', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:14:14', 12, ''),
(NULL, '', '', 'welcome', 'unlock', 'Administrator2', 'Run', '2015-10-26 17:14:18', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:14:18', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:14:25', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:14:28', 12, ''),
(NULL, '', '', 'bestellung', 'create', 'Administrator2', 'Run', '2015-10-26 17:14:34', 0, ''),
(NULL, '', '', 'bestellung', 'create', 'Administrator2', 'Run', '2015-10-26 17:14:34', 0, ''),
(NULL, '', '', 'bestellung', 'edit', 'Administrator2', 'Run', '2015-10-26 17:14:35', 1, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:14:35', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:14:35', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'bestellung', 'edit', 'Administrator2', 'Run', '2015-10-26 17:14:39', 1, ''),
(NULL, '', '', 'bestellung', 'edit', 'Administrator2', 'Run', '2015-10-26 17:14:39', 1, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:14:39', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:14:40', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:14:45', 700003, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:14:45', 700003, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:14:46', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:14:46', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:14:49', 700003, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:14:49', 700003, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:14:50', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:14:50', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:14:56', 700011, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:14:56', 700011, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:14:57', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:14:57', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:02', 0, ''),
(NULL, '', '', 'bestellung', 'delbestellungposition', 'Administrator2', 'Run', '2015-10-26 17:15:02', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'delbestellungposition', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:15:02', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:05', 700010, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:05', 700010, ''),
(NULL, '', '', 'bestellung', 'delbestellungposition', 'Administrator2', 'Run', '2015-10-26 17:15:06', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'delbestellungposition', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:15:06', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:09', 0, ''),
(NULL, '', '', 'bestellung', 'editable', 'Administrator2', 'Run', '2015-10-26 17:15:10', 0, ''),
(NULL, '', '', 'bestellung', 'editable', 'Administrator2', 'Run', '2015-10-26 17:15:12', 0, ''),
(NULL, '', '', 'bestellung', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:15:14', 1, ''),
(NULL, '', '', 'bestellung', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:15:15', 1, ''),
(NULL, '', '', 'bestellung', 'edit', 'Administrator2', 'Run', '2015-10-26 17:15:15', 1, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:15:16', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:15:16', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'bestellung', 'edit', 'Administrator2', 'Run', '2015-10-26 17:15:21', 1, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:15:22', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:15:22', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'bestellung', 'inlinepdf', 'Administrator2', 'Run', '2015-10-26 17:15:23', 1, ''),
(NULL, '', '', 'bestellung', 'inlinepdf', 'Administrator2', 'Run', '2015-10-26 17:15:24', 1, ''),
(NULL, '', '', 'bestellung', 'create', 'Administrator2', 'Run', '2015-10-26 17:15:29', 0, ''),
(NULL, '', '', 'bestellung', 'create', 'Administrator2', 'Run', '2015-10-26 17:15:29', 0, ''),
(NULL, '', '', 'bestellung', 'edit', 'Administrator2', 'Run', '2015-10-26 17:15:29', 2, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:15:29', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:15:29', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'bestellung', 'edit', 'Administrator2', 'Run', '2015-10-26 17:15:33', 2, ''),
(NULL, '', '', 'bestellung', 'edit', 'Administrator2', 'Run', '2015-10-26 17:15:33', 2, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:15:34', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:15:34', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:38', 0, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:38', 0, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:39', 0, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:41', 700001, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:41', 700001, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:15:43', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:15:43', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:45', 700002, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:45', 700002, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:15:46', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:15:46', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'bestellung', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:15:47', 2, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:15:47', 0, ''),
(NULL, '', '', 'bestellung', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:15:49', 2, ''),
(NULL, '', '', 'bestellung', 'edit', 'Administrator2', 'Run', '2015-10-26 17:15:49', 2, ''),
(NULL, '', '', 'bestellung', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:15:49', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'bestellung', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:15:49', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'bestellung', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:15:54', 2, ''),
(NULL, '', '', 'bestellung', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:15:57', 1, ''),
(NULL, '', '', 'angebot', 'create', 'Administrator2', 'Run', '2015-10-26 17:16:05', 0, ''),
(NULL, '', '', 'angebot', 'create', 'Administrator2', 'Run', '2015-10-26 17:16:05', 0, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:16:05', 1, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:16:05', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:16:06', 1, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:16:06', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:16:08', 1, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:16:09', 1, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:16:09', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:16:10', 1, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:16:10', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:16:16', 700008, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:16:16', 700008, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:16:19', 0, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:16:22', 1, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:16:22', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:16:23', 1, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:16:23', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:16:28', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:16:28', 700012, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:16:35', 12, ''),
(NULL, '', '', 'welcome', 'unlock', 'Administrator2', 'Run', '2015-10-26 17:16:39', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:16:39', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:16:47', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:16:49', 12, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:16:52', 12, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:16:57', 12, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:17:00', 12, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:17:06', 12, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:17:06', 12, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:17:12', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:17:12', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:17:16', 700012, ''),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:17:16', 1, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:16', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:16', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:17:17', 1, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'freigabe', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:17', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:17:17', 0, ''),
(NULL, '', '', 'angebot', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:17:19', 1, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:17:19', 1, ''),
(NULL, 'ANGEBOT BELEG 100000', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:19', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:17:19', 1, ''),
(NULL, 'ANGEBOT BELEG 100000', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:19', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'abschicken', 'Administrator2', 'Run', '2015-10-26 17:17:23', 1, ''),
(NULL, 'ANGEBOT BELEG 100000', '', 'angebot', 'abschicken', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:23', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'abschicken', 'Administrator2', 'Run', '2015-10-26 17:17:26', 1, ''),
(NULL, 'ANGEBOT BELEG 100000', '', 'angebot', 'abschicken', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:26', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:17:27', 0, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:17:27', 1, ''),
(NULL, 'ANGEBOT BELEG 100000', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:27', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:17:28', 1, ''),
(NULL, 'ANGEBOT BELEG 100000', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:28', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'kalender', 'eventdata', 'Administrator', 'Run', '2015-10-26 17:17:30', 4, ''),
(NULL, '', '', 'angebot', 'create', 'Administrator2', 'Run', '2015-10-26 17:17:32', 0, ''),
(NULL, '', '', 'angebot', 'create', 'Administrator2', 'Run', '2015-10-26 17:17:32', 0, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:17:32', 2, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:32', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:17:33', 2, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:33', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:17:35', 2, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:17:36', 0, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:17:36', 2, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:36', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:17:37', 2, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:37', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'kalender', 'eventdata', 'Administrator', 'Run', '2015-10-26 17:17:39', 1, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:17:41', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:17:41', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:17:42', 700012, ''),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:17:42', 2, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:42', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:42', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:17:44', 2, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'freigabe', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:44', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:17:44', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:17:44', 0, ''),
(NULL, '', '', 'angebot', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:17:46', 2, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:17:46', 2, ''),
(NULL, 'ANGEBOT BELEG 100001', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:46', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:17:46', 2, ''),
(NULL, 'ANGEBOT BELEG 100001', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:46', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:17:53', 2, ''),
(NULL, 'ANGEBOT BELEG 100001', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:53', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:17:54', 2, ''),
(NULL, 'ANGEBOT BELEG 100001', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:54', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:17:57', 2, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:17:57', 2, ''),
(NULL, 'ANGEBOT BELEG 100001', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:57', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:17:58', 2, ''),
(NULL, 'ANGEBOT BELEG 100001', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:17:58', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'create', 'Administrator2', 'Run', '2015-10-26 17:18:01', 0, ''),
(NULL, '', '', 'angebot', 'create', 'Administrator2', 'Run', '2015-10-26 17:18:01', 0, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:02', 3, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:02', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:02', 3, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:02', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:06', 3, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:07', 3, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:07', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:07', 3, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:07', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK');
INSERT INTO `protokoll` (`id`, `meldung`, `dump`, `module`, `action`, `bearbeiter`, `funktionsname`, `datum`, `parameter`, `argumente`) VALUES
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:18:11', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:18:11', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:18:13', 700012, ''),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:13', 3, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:13', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:14', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:18:15', 3, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:18:15', 0, ''),
(NULL, 'ANGEBOT BELEG ', '', 'angebot', 'freigabe', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:15', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:18:16', 3, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:17', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:17', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:17', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:17', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:18:21', 1, ''),
(NULL, 'ANGEBOT BELEG 100000', '', 'angebot', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:21', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'auftrag', 'Administrator2', 'Run', '2015-10-26 17:18:26', 1, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:27', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:27', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:28', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:28', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:28', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:28', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:18:30', 1, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'freigabe', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:30', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:18:32', 1, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:32', 1, ''),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:32', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:32', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:32', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:33', 1, ''),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:33', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'create', 'Administrator2', 'Run', '2015-10-26 17:18:36', 0, ''),
(NULL, '', '', 'auftrag', 'create', 'Administrator2', 'Run', '2015-10-26 17:18:36', 0, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:36', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:36', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:36', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:36', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:37', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:37', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:40', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:40', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:40', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:41', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:41', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:41', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:41', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:41', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:41', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:45', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:45', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:45', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:45', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:46', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:46', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:49', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:49', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:49', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:18:50', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:50', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:50', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:50', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:51', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:51', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:18:56', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:18:56', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:18:57', 700012, ''),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:18:57', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:57', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:18:57', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:19:00', 0, ''),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:19:06', 2, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'freigabe', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:06', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:19:06', 0, ''),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:19:08', 2, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:19:08', 0, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:19:08', 2, ''),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:08', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:08', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:08', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:19:09', 2, ''),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:09', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:19:15', 2, ''),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:15', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:15', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:19:18', 2, ''),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:18', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:18', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:18', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'kalender', 'eventdata', 'Administrator', 'Run', '2015-10-26 17:19:19', 1, ''),
(NULL, '', '', 'auftrag', 'create', 'Administrator2', 'Run', '2015-10-26 17:19:23', 0, ''),
(NULL, '', '', 'auftrag', 'create', 'Administrator2', 'Run', '2015-10-26 17:19:23', 0, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:19:24', 3, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:24', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:24', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:24', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:19:25', 3, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:25', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:19:31', 3, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:31', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:31', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:19:32', 3, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:32', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:32', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:32', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:19:32', 3, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:32', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:19:35', 0, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:19:41', 0, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:19:42', 0, ''),
(NULL, '', '', 'welcome', 'addpinwand', 'Administrator', 'Run', '2015-10-26 17:19:44', 0, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:19:45', 3, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:45', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:45', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:45', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:19:46', 3, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:46', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:19:50', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:19:50', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:19:53', 700012, ''),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:19:53', 3, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:53', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:53', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:19:54', 3, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'freigabe', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:54', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:19:54', 0, ''),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:19:56', 3, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:19:56', 3, ''),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:56', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:56', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:56', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:19:57', 3, ''),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:19:57', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:20:02', 3, ''),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:20:02', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'welcome', 'addpinwand', 'Administrator', 'Run', '2015-10-26 17:20:06', 0, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:20:06', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:20:14', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator2', 'Run', '2015-10-26 17:20:17', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:20:20', 12, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:20:21', 0, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:20:31', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:20:38', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:20:41', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:20:47', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:20:50', 12, ''),
(NULL, '', '', 'angebot', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:21:15', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:21:15', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'edit', 'Administrator2', 'Run', '2015-10-26 17:21:18', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:21:18', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:21:18', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:21:18', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'inlinepdf', 'Administrator2', 'Run', '2015-10-26 17:21:20', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'inlinepdf', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:21:20', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'inlinepdf', 'Administrator2', 'Run', '2015-10-26 17:21:21', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'inlinepdf', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:21:21', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:21:25', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:21:25', 700012, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:21:27', 700012, ''),
(NULL, '', '', 'angebot', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:21:27', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:21:27', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:21:28', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:21:30', 0, ''),
(NULL, '', '', 'angebot', 'delangebotposition', 'Administrator2', 'Run', '2015-10-26 17:21:30', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'delangebotposition', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:21:31', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:21:32', 0, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:21:39', 0, ''),
(NULL, '', '', 'welcome', 'addpinwand', 'Administrator', 'Run', '2015-10-26 17:22:10', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 17:22:48', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:22:48', 0, ''),
(NULL, '', '', 'projekt', 'edit', 'Administrator2', 'Run', '2015-10-26 17:22:51', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:22:57', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:23:02', 1, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:23:06', 3, ''),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:07', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:07', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:07', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:23:08', 3, ''),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:08', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:23:12', 12, ''),
(NULL, '', '', 'welcome', 'unlock', 'Administrator2', 'Run', '2015-10-26 17:23:16', 12, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:23:16', 12, ''),
(NULL, '', '', 'artikel', 'stueckliste', 'Administrator2', 'Run', '2015-10-26 17:23:22', 12, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:23:29', 0, ''),
(NULL, '', '', 'auftrag', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:23:31', 3, ''),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:31', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:23:33', 2, ''),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:33', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:23:35', 2, ''),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:35', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:35', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:35', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:23:36', 2, ''),
(NULL, 'AUFTRAG BELEG 200001', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:36', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', '', '', 'Administrator', 'Run', '2015-10-26 17:23:39', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 17:23:39', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:23:39', 0, ''),
(NULL, '', '', 'auftrag', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:23:41', 1, ''),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:41', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:23:43', 1, ''),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:43', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:43', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:43', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:23:44', 1, ''),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:44', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:23:46', 1, ''),
(NULL, 'AUFTRAG BELEG 200000', '', 'auftrag', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:23:46', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 17:23:52', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:23:53', 0, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:23:58', 0, ''),
(NULL, '', '', 'welcome', 'addnote', 'Administrator', 'Run', '2015-10-26 17:24:01', 0, ''),
(NULL, '', '', 'auftrag', 'create', 'Administrator2', 'Run', '2015-10-26 17:24:02', 0, ''),
(NULL, '', '', 'auftrag', 'create', 'Administrator2', 'Run', '2015-10-26 17:24:02', 0, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:03', 4, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:03', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:03', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:03', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:24:04', 4, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:04', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:07', 4, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:07', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:07', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:08', 4, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:08', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:08', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:08', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:24:09', 4, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:24:09', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:24:13', 700001, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:24:13', 700001, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:24', 1, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:29', 2, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:32', 3, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:35', 4, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:39', 5, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:42', 6, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:46', 7, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:49', 7, ''),
(NULL, '', '', 'welcome', 'addnote', 'Administrator', 'Run', '2015-10-26 17:24:52', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:53', 7, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:24:53', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:24:57', 8, ''),
(NULL, '', '', 'welcome', 'addnote', 'Administrator', 'Run', '2015-10-26 17:24:58', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:25:00', 9, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:25:02', 10, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:25:04', 11, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:25:07', 13, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:09', 1, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:20', 1, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:20', 1, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:26', 2, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:35', 2, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:35', 2, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:41', 3, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:48', 3, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:48', 3, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:25:52', 4, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:02', 4, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:02', 4, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:05', 5, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:12', 5, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:12', 5, ''),
(NULL, '', '', 'welcome', 'addnote', 'Administrator', 'Run', '2015-10-26 17:26:15', 0, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:26:15', 0, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:16', 6, ''),
(NULL, '', '', 'welcome', 'movenote', 'Administrator', 'Run', '2015-10-26 17:26:20', 2, ''),
(NULL, '', '', 'welcome', 'movenote', 'Administrator', 'Run', '2015-10-26 17:26:22', 1, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:26', 6, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:26', 6, ''),
(NULL, '', '', 'welcome', 'addnote', 'Administrator', 'Run', '2015-10-26 17:26:27', 0, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:30', 7, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:36', 7, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:36', 7, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:40', 7, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:47', 7, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:47', 7, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:54', 7, ''),
(NULL, '', '', 'artikel', 'verkaufdelete', 'Administrator2', 'Run', '2015-10-26 17:26:58', 9, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:26:59', 7, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:27:03', 8, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:27:09', 8, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:27:10', 8, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:27:16', 9, ''),
(NULL, '', '', 'welcome', 'addnote', 'Administrator', 'Run', '2015-10-26 17:27:17', 0, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:27:17', 0, ''),
(NULL, '', '', 'welcome', 'movenote', 'Administrator', 'Run', '2015-10-26 17:27:22', 3, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:27:23', 9, ''),
(NULL, '', '', 'welcome', 'movenote', 'Administrator', 'Run', '2015-10-26 17:27:25', 2, ''),
(NULL, '', '', 'aufgaben', 'edit', 'Administrator', 'Run', '2015-10-26 17:27:36', 2, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:27:37', 9, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:27:37', 9, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:27:39', 9, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:27:45', 9, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:27:46', 9, ''),
(NULL, '', '', 'aufgaben', 'edit', 'Administrator', 'Run', '2015-10-26 17:27:52', 2, ''),
(NULL, '', '', 'welcome', 'oknote', 'Administrator', 'Run', '2015-10-26 17:28:09', 2, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:28:09', 0, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:28:25', 10, ''),
(NULL, '', '', 'welcome', 'movenote', 'Administrator', 'Run', '2015-10-26 17:28:27', 3, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:28:27', 10, ''),
(NULL, '', '', 'welcome', 'addnote', 'Administrator', 'Run', '2015-10-26 17:28:29', 0, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:28:33', 10, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:28:33', 10, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:28:36', 11, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:28:38', 11, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:28:42', 11, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:28:43', 11, ''),
(NULL, '', '', 'welcome', 'addnote', 'Administrator', 'Run', '2015-10-26 17:28:44', 0, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:28:45', 0, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:28:45', 13, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:28:49', 13, ''),
(NULL, '', '', 'welcome', 'movenote', 'Administrator', 'Run', '2015-10-26 17:28:49', 4, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:28:53', 0, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:29:05', 13, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:29:13', 13, ''),
(NULL, '', '', 'artikel', 'einkauf', 'Administrator2', 'Run', '2015-10-26 17:29:13', 13, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:29:14', 0, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:29:15', 13, ''),
(NULL, '', '', 'welcome', 'movenote', 'Administrator', 'Run', '2015-10-26 17:29:18', 4, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:29:20', 0, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:29:21', 13, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:29:21', 13, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:29:22', 0, ''),
(NULL, '', '', 'zeiterfassung', 'listuser', 'Administrator', 'Run', '2015-10-26 17:29:25', 0, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:29:26', 0, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:29:30', 700001, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:29:30', 700001, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:29:33', 700001, ''),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:29:33', 4, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:29:33', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:29:33', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:29:35', 4, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'freigabe', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:29:35', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:29:35', 0, ''),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:29:36', 4, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:29:36', 4, ''),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:29:36', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:29:36', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:29:37', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:29:37', 4, ''),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:29:37', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'create', 'Administrator2', 'Run', '2015-10-26 17:29:41', 0, ''),
(NULL, '', '', 'artikel', 'create', 'Administrator2', 'Run', '2015-10-26 17:29:47', 0, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:29:47', 14, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:29:52', 14, ''),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:29:56', 14, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:29:59', 14, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:30:04', 14, ''),
(NULL, '', '', 'artikel', 'verkauf', 'Administrator2', 'Run', '2015-10-26 17:30:05', 14, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:30:13', 4, ''),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:30:13', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:30:13', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:30:13', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:30:13', 4, ''),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:30:13', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:30:18', 100001, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:30:18', 100001, ''),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:30:19', 100001, ''),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:30:19', 4, ''),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:30:19', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:30:19', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'artikel', 'ajaxwerte', 'Administrator2', 'Run', '2015-10-26 17:30:21', 0, ''),
(NULL, '', '', 'auftrag', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:30:25', 4, ''),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:30:25', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'projekt', 'edit', 'Administrator2', 'Run', '2015-10-26 17:30:34', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:30:40', 1, ''),
(NULL, '', '', 'projekt', 'edit', 'Administrator2', 'Run', '2015-10-26 17:30:42', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:30:45', 1, ''),
(NULL, '', '', 'projekt', 'edit', 'Administrator2', 'Run', '2015-10-26 17:30:47', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:30:53', 1, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:30:55', 0, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:30:55', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:30:58', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:03', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:08', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:13', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:18', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:23', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:28', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:33', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:38', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:43', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:48', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:53', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:31:58', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:03', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:08', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:13', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:18', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:23', 1, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:32:28', 0, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:32:28', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:28', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:33', 1, ''),
(NULL, '', '', 'zeiterfassung', 'listuser', 'Administrator', 'Run', '2015-10-26 17:32:35', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:38', 1, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:32:39', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:43', 1, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:32:46', 1, ''),
(NULL, '', '', 'zeiterfassung', 'listuser', 'Administrator', 'Run', '2015-10-26 17:32:46', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:48', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:53', 1, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:32:53', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:32:58', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:03', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:08', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:13', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:18', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:23', 1, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:33:27', 0, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:33:28', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:28', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:33', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:38', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:43', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:48', 1, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:33:51', 0, ''),
(NULL, '', '', 'zeiterfassung', 'create', 'Administrator', 'Run', '2015-10-26 17:33:52', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:53', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:33:58', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:03', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:08', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:13', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:18', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:23', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:28', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:33', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:38', 1, '');
INSERT INTO `protokoll` (`id`, `meldung`, `dump`, `module`, `action`, `bearbeiter`, `funktionsname`, `datum`, `parameter`, `argumente`) VALUES
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:43', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:48', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:53', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:34:58', 1, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 17:35:00', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:35:00', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:03', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:08', 1, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:35:12', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:13', 1, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 17:35:13', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:35:14', 0, ''),
(NULL, '', '', 'kalender', 'eventdata', 'Administrator', 'Run', '2015-10-26 17:35:15', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:18', 1, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 17:35:22', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:35:23', 0, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:23', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:28', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:33', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:38', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:43', 1, ''),
(NULL, '', '', 'projekt', 'getnextnumber', 'Administrator2', 'Run', '2015-10-26 17:35:48', 1, ''),
(NULL, '', '', 'auftrag', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:35:54', 4, ''),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:35:54', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'copy', 'Administrator2', 'Run', '2015-10-26 17:35:59', 4, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:36:02', 5, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:02', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:02', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:02', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:36:03', 5, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:03', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:36:05', 5, ''),
(NULL, 'AUFTRAG BELEG ', '', 'auftrag', 'freigabe', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:05', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'freigabe', 'Administrator2', 'Run', '2015-10-26 17:36:07', 5, ''),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:36:07', 5, ''),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:07', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:07', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:07', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:36:08', 5, ''),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:08', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'versand', 'Administrator2', 'Run', '2015-10-26 17:36:17', 5, ''),
(NULL, 'MESSAGE VOR LOCK', '', 'auftrag', 'versand', 'Administrator2', 'AuftragVersand', '2015-10-26 17:36:17', 5, 'QXJyYXkKKAopCg=='),
(NULL, 'MESSAGE NACH LOCK', '', 'auftrag', 'versand', 'Administrator2', 'AuftragVersand', '2015-10-26 17:36:17', 5, 'QXJyYXkKKAopCg=='),
(NULL, 'MESSAGE START', '', 'auftrag', 'versand', 'Administrator2', 'AuftragVersand', '2015-10-26 17:36:17', 5, 'QXJyYXkKKAopCg=='),
(NULL, 'WeiterfuehrenAuftrag AB 200004 Art: ', '', 'auftrag', 'versand', 'Administrator2', 'AuftragVersand', '2015-10-26 17:36:18', 5, 'QXJyYXkKKAopCg=='),
(NULL, 'WeiterfuehrenAuftragZuLieferschein AB 200004', '', 'auftrag', 'versand', 'Administrator2', 'AuftragVersand', '2015-10-26 17:36:20', 5, 'QXJyYXkKKAopCg=='),
(NULL, 'WeiterfuehrenAuftragZuRechnung AB 200004 Preis 8.55', '', 'auftrag', 'versand', 'Administrator2', 'AuftragVersand', '2015-10-26 17:36:20', 5, 'QXJyYXkKKAopCg=='),
(NULL, 'RECHNUNG BELEG ', '', 'auftrag', 'versand', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:23', 5, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiByZWNobnVuZwopCg=='),
(NULL, 'WeiterfuehrenAuftragZuRechnung AB 200004 (id 5) RE 400000 (id 1)', '', 'auftrag', 'versand', 'Administrator2', 'AuftragVersand', '2015-10-26 17:36:23', 5, 'QXJyYXkKKAopCg=='),
(NULL, 'WeiterfuehrenAuftragZuRechnung AB 200004 Kommissionierverfahren: lieferschein Projekt 1', '', 'auftrag', 'versand', 'Administrator2', 'AuftragVersand', '2015-10-26 17:36:23', 5, 'QXJyYXkKKAopCg=='),
(NULL, 'MESSAGE BEENDET UNLOCK', '', 'auftrag', 'versand', 'Administrator2', 'AuftragVersand', '2015-10-26 17:36:24', 5, 'QXJyYXkKKAopCg=='),
(NULL, '', '', 'auftrag', 'edit', 'Administrator2', 'Run', '2015-10-26 17:36:24', 5, ''),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:24', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:24', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'edit', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:24', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator2', 'Run', '2015-10-26 17:36:24', 5, ''),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'positionen', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:24', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'rechnung', 'minidetail', 'Administrator2', 'Run', '2015-10-26 17:36:31', 1, ''),
(NULL, 'RECHNUNG BELEG 400000', '', 'rechnung', 'minidetail', 'Administrator2', 'BerechneDeckungsbeitrag', '2015-10-26 17:36:31', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiByZWNobnVuZwopCg=='),
(NULL, '', '', 'artikel', 'edit', 'Administrator2', 'Run', '2015-10-26 17:36:40', 1, ''),
(NULL, '', '', 'artikel', 'lager', 'Administrator2', 'Run', '2015-10-26 17:36:43', 1, ''),
(NULL, '', '', 'wareneingang', 'paketannahme', 'Administrator', 'Run', '2015-10-26 17:37:38', 0, ''),
(NULL, '', '', 'wareneingang', 'paketannahme', 'Administrator', 'Run', '2015-10-26 17:37:51', 8, ''),
(NULL, '', '', 'wareneingang', 'distriinhalt', 'Administrator', 'Run', '2015-10-26 17:37:51', 1, ''),
(NULL, '', '', 'wareneingang', 'paketannahme', 'Administrator', 'Run', '2015-10-26 17:38:01', 0, ''),
(NULL, '', '', '', '', 'Administrator', 'Run', '2015-10-26 17:38:10', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 17:38:10', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:38:10', 0, ''),
(NULL, '', '', 'rechnung', 'edit', 'Administrator', 'Run', '2015-10-26 17:38:28', 1, ''),
(NULL, '', '', 'rechnung', 'pdf', 'Administrator', 'Run', '2015-10-26 17:38:47', 1, ''),
(NULL, '', '', 'rechnung', 'copy', 'Administrator', 'Run', '2015-10-26 17:38:59', 1, ''),
(NULL, '', '', 'rechnung', 'edit', 'Administrator', 'Run', '2015-10-26 17:39:02', 2, ''),
(NULL, 'RECHNUNG BELEG ', '', 'rechnung', 'edit', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:39:02', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiByZWNobnVuZwopCg=='),
(NULL, 'RECHNUNG BELEG ', '', 'rechnung', 'edit', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:39:02', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiByZWNobnVuZwopCg=='),
(NULL, 'RECHNUNG BELEG ', '', 'rechnung', 'edit', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:39:02', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiByZWNobnVuZwopCg=='),
(NULL, '', '', 'rechnung', 'positionen', 'Administrator', 'Run', '2015-10-26 17:39:02', 2, ''),
(NULL, 'RECHNUNG BELEG ', '', 'rechnung', 'positionen', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:39:02', 2, 'QXJyYXkKKAogICAgWzBdID0+IDIKICAgIFsxXSA9PiByZWNobnVuZwopCg=='),
(NULL, '', '', 'rechnung', 'minidetail', 'Administrator', 'Run', '2015-10-26 17:39:18', 1, ''),
(NULL, 'RECHNUNG BELEG 400000', '', 'rechnung', 'minidetail', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:39:18', 1, 'QXJyYXkKKAogICAgWzBdID0+IDEKICAgIFsxXSA9PiByZWNobnVuZwopCg=='),
(NULL, '', '', 'lieferschein', 'edit', 'Administrator', 'Run', '2015-10-26 17:39:30', 1, ''),
(NULL, '', '', 'lieferschein', 'positionen', 'Administrator', 'Run', '2015-10-26 17:39:30', 1, ''),
(NULL, '', '', 'lieferschein', 'pdf', 'Administrator', 'Run', '2015-10-26 17:39:36', 1, ''),
(NULL, '', '', 'angebot', 'edit', 'Administrator', 'Run', '2015-10-26 17:39:51', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'edit', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:39:51', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'positionen', 'Administrator', 'Run', '2015-10-26 17:39:52', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'positionen', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:39:52', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'angebot', 'pdf', 'Administrator', 'Run', '2015-10-26 17:39:58', 3, ''),
(NULL, 'ANGEBOT BELEG 100002', '', 'angebot', 'pdf', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:39:58', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhbmdlYm90CikK'),
(NULL, '', '', 'auftrag', 'edit', 'Administrator', 'Run', '2015-10-26 17:40:08', 5, ''),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'edit', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:40:08', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'edit', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:40:08', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'edit', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:40:08', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'positionen', 'Administrator', 'Run', '2015-10-26 17:40:09', 5, ''),
(NULL, 'AUFTRAG BELEG 200004', '', 'auftrag', 'positionen', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:40:09', 5, 'QXJyYXkKKAogICAgWzBdID0+IDUKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'pdf', 'Administrator', 'Run', '2015-10-26 17:40:12', 3, ''),
(NULL, 'AUFTRAG BELEG 200002', '', 'auftrag', 'pdf', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:40:12', 3, 'QXJyYXkKKAogICAgWzBdID0+IDMKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'minidetail', 'Administrator', 'Run', '2015-10-26 17:40:18', 4, ''),
(NULL, 'AUFTRAG BELEG 200003', '', 'auftrag', 'minidetail', 'Administrator', 'BerechneDeckungsbeitrag', '2015-10-26 17:40:18', 4, 'QXJyYXkKKAogICAgWzBdID0+IDQKICAgIFsxXSA9PiBhdWZ0cmFnCikK'),
(NULL, '', '', 'auftrag', 'berechnen', 'Administrator', 'Run', '2015-10-26 17:40:26', 0, ''),
(NULL, '', '', 'auftrag', 'berechnen', 'Administrator', 'Run', '2015-10-26 17:40:29', 0, ''),
(NULL, '', '', 'lager', 'buchenzwischenlager', 'Administrator2', 'Run', '2015-10-26 17:40:43', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator2', 'Run', '2015-10-26 17:40:48', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator2', 'Run', '2015-10-26 17:40:48', 0, ''),
(NULL, '', '', 'importvorlage', 'uebersicht', 'Administrator2', 'Run', '2015-10-26 17:40:50', 0, ''),
(NULL, '', '', 'lager', 'edit', 'Administrator2', 'Run', '2015-10-26 17:40:56', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator2', 'Run', '2015-10-26 17:40:59', 1, ''),
(NULL, '', '', 'lager', 'platzeditpopup', 'Administrator2', 'Run', '2015-10-26 17:41:02', 1, ''),
(NULL, '', '', 'lager', 'platz', 'Administrator2', 'Run', '2015-10-26 17:41:06', 1, ''),
(NULL, '', '', 'welcome', 'logout', 'Administrator', 'Run', '2015-10-26 17:41:30', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 17:41:30', 0, ''),
(NULL, '', '', 'welcome', 'logout', '', 'Run', '2015-10-26 17:41:59', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 17:42:00', 0, ''),
(NULL, '', '', 'welcome', 'login', '', 'Run', '2015-10-26 17:42:04', 0, ''),
(NULL, '', '', 'welcome', 'start', 'Administrator', 'Run', '2015-10-26 17:42:04', 0, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:42:06', 0, ''),
(NULL, '', '', 'welcome', 'pinwand', 'Administrator', 'Run', '2015-10-26 17:42:10', 0, ''),
(NULL, '', '', 'aufgaben', 'edit', 'Administrator', 'Run', '2015-10-26 17:42:17', 1, ''),
(NULL, '', '', 'rechnung', 'edit', 'Administrator', 'Run', '2015-10-26 17:43:18', 1, ''),
(NULL, '', '', 'kalender', 'data', 'Administrator', 'Run', '2015-10-26 17:44:14', 0, '');



--
-- Daten für Tabelle `rechnung`
--

INSERT INTO `rechnung` (`id`, `datum`, `aborechnung`, `projekt`, `anlegeart`, `belegnr`, `auftrag`, `auftragid`, `bearbeiter`, `freitext`, `internebemerkung`, `status`, `adresse`, `name`, `abteilung`, `unterabteilung`, `strasse`, `adresszusatz`, `ansprechpartner`, `plz`, `ort`, `land`, `ustid`, `ust_befreit`, `ustbrief`, `ustbrief_eingang`, `ustbrief_eingang_am`, `email`, `telefon`, `telefax`, `betreff`, `kundennummer`, `lieferschein`, `versandart`, `lieferdatum`, `buchhaltung`, `zahlungsweise`, `zahlungsstatus`, `ist`, `soll`, `skonto_gegeben`, `zahlungszieltage`, `zahlungszieltageskonto`, `zahlungszielskonto`, `firma`, `versendet`, `versendet_am`, `versendet_per`, `versendet_durch`, `versendet_mahnwesen`, `mahnwesen`, `mahnwesen_datum`, `mahnwesen_gesperrt`, `mahnwesen_internebemerkung`, `inbearbeitung`, `datev_abgeschlossen`, `logdatei`, `doppel`, `autodruck_rz`, `autodruck_periode`, `autodruck_done`, `autodruck_anzahlverband`, `autodruck_anzahlkunde`, `autodruck_mailverband`, `autodruck_mailkunde`, `dta_datei_verband`, `dta_datei`, `deckungsbeitragcalc`, `deckungsbeitrag`, `umsatz_netto`, `erloes_netto`, `mahnwesenfestsetzen`, `vertriebid`, `aktion`, `vertrieb`, `provision`, `provision_summe`, `gruppe`, `punkte`, `bonuspunkte`, `provdatum`, `ihrebestellnummer`, `anschreiben`, `usereditid`, `useredittimestamp`, `realrabatt`, `rabatt`, `einzugsdatum`, `rabatt1`, `rabatt2`, `rabatt3`, `rabatt4`, `rabatt5`, `forderungsverlust_datum`, `forderungsverlust_betrag`, `steuersatz_normal`, `steuersatz_zwischen`, `steuersatz_ermaessigt`, `steuersatz_starkermaessigt`, `steuersatz_dienstleistung`, `waehrung`, `keinsteuersatz`, `schreibschutz`, `pdfarchiviert`, `pdfarchiviertversion`, `typ`, `ohne_briefpapier`, `lieferid`, `ansprechpartnerid`, `systemfreitext`, `projektfiliale`) VALUES
(1, NOW(), 0, '1', '', '400000', '200004', 5, 'Administrator2', '', '', 'freigegeben', 5, 'Hans Huber', '', '', 'Musterstrasse 6', '', '', '12345', 'Musterstadt', 'DE', '', 0, 0, 0, '0000-00-00', 'hans@huberhausen.de', '017123456745', '', '', '10002', 1, 'versandunternehmen', '0000-00-00', 'Administrator2', 'rechnung', 'offen', 0.00, 10.17, 0.00, 14, 10, 2.00, 1, 0, '0000-00-00 00:00:00', '', '', 0, '', '0000-00-00', 0, '', 0, 0, '2015-10-26 16:43:18', NULL, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 85.96, 8.55, 7.35, 0, 0, '', 'Administrator2', 0.00, 0.00, 0, NULL, NULL, NULL, '', '', 1, '2015-10-26 16:43:18', NULL, 0.00, '0000-00-00', 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', 0, 0, 0, 0, 'firma', NULL, 0, 0, '', 0),
(2, NOW(), 0, '1', '', '', '200004', 5, 'Administrator2', '', '', 'angelegt', 5, 'Hans Huber', '', '', 'Musterstrasse 6', '', '', '12345', 'Musterstadt', 'DE', '', 0, 0, 0, '0000-00-00', 'hans@huberhausen.de', '017123456745', '', '', '10002', 0, 'versandunternehmen', '0000-00-00', 'Administrator', 'rechnung', 'offen', 0.00, 10.17, 0.00, 14, 10, 2.00, 1, 0, '0000-00-00 00:00:00', '', '', 0, '', '0000-00-00', 0, '', 0, 0, '2015-10-26 16:39:06', NULL, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 85.96, 8.55, 7.35, 0, 0, '', '', 0.00, 0.00, 0, NULL, NULL, NULL, NULL, NULL, 1, '2015-10-26 16:39:03', NULL, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 19.00, 7.00, 7.00, 7.00, 7.00, 'EUR', NULL, 0, 0, 0, 'firma', 0, 0, 0, '', 0);

--
-- Daten für Tabelle `rechnung_position`
--

INSERT INTO `rechnung_position` (`id`, `rechnung`, `artikel`, `projekt`, `bezeichnung`, `beschreibung`, `internerkommentar`, `nummer`, `menge`, `preis`, `waehrung`, `lieferdatum`, `vpe`, `sort`, `status`, `umsatzsteuer`, `bemerkung`, `logdatei`, `explodiert_parent_artikel`, `punkte`, `bonuspunkte`, `mlmdirektpraemie`, `mlm_abgerechnet`, `keinrabatterlaubt`, `grundrabatt`, `rabattsync`, `rabatt1`, `rabatt2`, `rabatt3`, `rabatt4`, `rabatt5`, `einheit`, `rabatt`, `zolltarifnummer`, `herkunftsland`) VALUES
(NULL, 1, 1, 1, 'Schraube M10x20', '', '', '700001', 10, 0.1600, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', '2015-10-26 16:36:22', 0, 0.00, 0.00, 0.00, NULL, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, '0', '0'),
(NULL, 1, 14, 1, 'Versandkosten', '', '', '100001', 1, 6.9500, 'EUR', '0000-00-00', '1', 2, 'angelegt', '', '', '2015-10-26 16:36:23', 0, 0.00, 0.00, 0.00, NULL, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, '0', '0'),
(NULL, 2, 1, 1, 'Schraube M10x20', '', '', '700001', 10, 0.1600, 'EUR', '0000-00-00', '1', 1, 'angelegt', '', '', '2015-10-26 16:39:01', 0, 0.00, 0.00, 0.00, 0, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, '0', '0'),
(NULL, 2, 14, 1, 'Versandkosten', '', '', '100001', 1, 6.9500, 'EUR', '0000-00-00', '1', 2, 'angelegt', '', '', '2015-10-26 16:39:02', 0, 0.00, 0.00, 0.00, 0, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, '', 0.00, '0', '0');

--
-- Daten für Tabelle `stueckliste`
--

INSERT INTO `stueckliste` (`id`, `sort`, `artikel`, `referenz`, `place`, `layer`, `stuecklistevonartikel`, `menge`, `firma`, `wert`, `bauform`) VALUES
(NULL, 1, 1, '', 'DP', 'Top', 12, 4, 1, '', ''),
(NULL, 2, 8, '', 'DP', 'Top', 12, 1, 1, '', ''),
(NULL, 3, 9, '', 'DP', 'Top', 12, 1, 1, '', ''),
(NULL, 4, 13, '', 'DP', 'Top', 12, 1, 1, '', ''),
(NULL, 5, 10, '', 'DP', 'Top', 12, 2, 1, '', ''),
(NULL, 6, 11, '', 'DP', 'Top', 12, 2, 1, '', '');

--
-- Daten für Tabelle `systemlog`
--

INSERT INTO `systemlog` (`id`, `meldung`, `dump`, `module`, `action`, `bearbeiter`, `funktionsname`, `datum`, `parameter`, `argumente`, `level`) VALUES
(NULL, 'Fehlendes Recht', '', 'kalender', 'data', 'Anton Lechner', 'Check', '2015-10-26 17:01:24', 0, 'QXJyYXkKKAogICAgWzBdID0+IHN0YW5kYXJkCiAgICBbMV0gPT4ga2FsZW5kZXIKICAgIFsyXSA9PiBkYXRhCiAgICBbM10gPT4gMwopCg==', 1),
(NULL, 'Keine gueltige Benutzer ID erhalten', '', 'welcome', 'start', '', 'Check', '2015-10-26 17:41:57', 0, 'QXJyYXkKKAogICAgWzBdID0+IHdlYgogICAgWzFdID0+IHdlbGNvbWUKICAgIFsyXSA9PiBzdGFydAogICAgWzNdID0+IAopCg==', 1);

--
-- Daten für Tabelle `user`
--


--
-- Daten für Tabelle `userrights`
--

INSERT INTO `userrights` (`id`, `user`, `module`, `action`, `permission`) VALUES
(NULL, 3, 'welcome', 'login', 1),
(NULL, 3, 'welcome', 'logout', 1),
(NULL, 3, 'welcome', 'start', 1),
(NULL, 3, 'welcome', 'startseite', 1),
(NULL, 3, 'welcome', 'settings', 1),
(NULL, 3, 'zeiterfassung', 'abrechnenpdf', 1),
(NULL, 3, 'zeiterfassung', 'details', 1),
(NULL, 3, 'zeiterfassung', 'delete', 1),
(NULL, 3, 'zeiterfassung', 'create', 1),
(NULL, 3, 'zeiterfassung', 'arbeitspaket', 1),
(NULL, 3, 'zeiterfassung', 'dokuarbeitszeitpdf', 1),
(NULL, 3, 'zeiterfassung', 'edit', 1),
(NULL, 3, 'zeiterfassung', 'list', 1),
(NULL, 3, 'zeiterfassung', 'listuser', 1),
(NULL, 3, 'zeiterfassung', 'minidetail', 1);

--
-- Daten für Tabelle `verkaufspreise`
--

INSERT INTO `verkaufspreise` (`id`, `artikel`, `objekt`, `projekt`, `adresse`, `preis`, `waehrung`, `ab_menge`, `vpe`, `vpe_menge`, `angelegt_am`, `gueltig_bis`, `bemerkung`, `bearbeiter`, `logdatei`, `firma`, `geloescht`, `kundenartikelnummer`, `art`, `gruppe`, `apichange`) VALUES
(NULL, 12, '', '', '0', 62.0000, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 1, '', '', '0', 0.1600, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 2, '', '', '0', 0.1700, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 3, '', '', '0', 3.1100, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 4, '', '', '0', 61.3600, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 5, '', '', '0', 16.8000, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 6, '', '', '0', 9.5200, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 7, '', '', '0', 4.6000, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 7, '', '', '0', 4.6000, 'EUR', 1, '', 0, '0000-00-00', '2015-10-25', '', '', '2015-10-26 16:26:58', 1, 1, '', 'Kunde', 0, 0),
(NULL, 8, '', '', '0', 24.0000, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 9, '', '', '0', 23.0000, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 10, '', '', '0', 1.6000, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 11, '', '', '0', 0.3000, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 13, '', '', '0', 17.2000, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0),
(NULL, 14, '', '', '0', 6.9500, 'EUR', 1, '', 0, '0000-00-00', '0000-00-00', '', '', '0000-00-00 00:00:00', 1, 0, '', 'Kunde', 0, 0);

--
-- Daten für Tabelle `zeiterfassung`
--

INSERT INTO `zeiterfassung` (`id`, `art`, `adresse`, `von`, `bis`, `aufgabe`, `beschreibung`, `arbeitspaket`, `buchungsart`, `kostenstelle`, `projekt`, `abgerechnet`, `logdatei`, `status`, `gps`, `arbeitsnachweispositionid`, `adresse_abrechnung`, `abrechnen`, `ist_abgerechnet`, `gebucht_von_user`, `ort`, `abrechnung_dokument`, `dokumentid`, `verrechnungsart`, `arbeitsnachweis`, `internerkommentar`, `aufgabe_id`) VALUES
(NULL, 'Arbeit', 1, '2015-10-26 09:00:00', '2015-10-26 17:00:00', 'Einrichtung WaWision', '', 0, 'manuell', '', 0, 0, '0000-00-00 00:00:00', NULL, '', 0, 0, 0, 0, 1, '', NULL, NULL, '', NULL, '', 0),
(NULL, 'Pause', 1, '2015-10-26 12:00:00', '2015-10-26 11:30:00', 'Mittagspause', '', 0, 'manuell', '', 0, 0, '0000-00-00 00:00:00', NULL, '', 0, 0, 0, 0, 1, '', NULL, NULL, '', NULL, '', 0),
(NULL, 'Arbeit', 1, '2015-10-27 09:00:00', '2015-10-27 16:00:00', 'Vertrieb', '', 0, 'manuell', '', 0, 0, '0000-00-00 00:00:00', NULL, '', 0, 0, 0, 0, 1, '', NULL, NULL, '', NULL, '', 0),
(NULL, 'Pause', 1, '2015-10-26 12:00:00', '2015-10-26 11:45:00', 'Mittagspause', '', 0, 'manuell', '', 0, 0, '0000-00-00 00:00:00', NULL, '', 0, 0, 0, 0, 1, '', NULL, NULL, '', NULL, '', 0);

UPDATE `firmendaten_werte` SET `wert` = 10003 WHERE `name` = 'next_kundennummer';
UPDATE `firmendaten_werte` SET `wert` = 90001 WHERE `name` = 'next_mitarbeiternummer';
UPDATE `firmendaten_werte` SET `wert` = 70002 WHERE `name` = 'next_lieferantennummer';

UPDATE `firmendaten_werte` SET `wert` = 100003 WHERE `name` = 'next_angebot';
UPDATE `firmendaten_werte` SET `wert` = 200005 WHERE `name` = 'next_auftrag';
UPDATE `firmendaten_werte` SET `wert` = 100002 WHERE `name` = 'next_bestellung';
UPDATE `firmendaten_werte` SET `wert` = 300001 WHERE `name` = 'next_lieferschein';
UPDATE `firmendaten_werte` SET `wert` = 400001 WHERE `name` = 'next_rechnung';



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
