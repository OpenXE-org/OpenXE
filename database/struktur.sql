-- MariaDB dump 10.19  Distrib 10.6.16-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: openxe
-- ------------------------------------------------------
-- Server version	10.6.16-MariaDB-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET innodb_strict_mode = OFF;

--
-- Table structure for table `abrechnungsartikel`
--

DROP TABLE IF EXISTS `abrechnungsartikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `abrechnungsartikel` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sort` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `menge` float NOT NULL,
  `preis` decimal(10,4) NOT NULL,
  `steuerklasse` varchar(255) NOT NULL,
  `rabatt` varchar(255) NOT NULL,
  `abgerechnet` int(1) NOT NULL,
  `startdatum` date NOT NULL,
  `lieferdatum` date NOT NULL,
  `abgerechnetbis` date NOT NULL,
  `wiederholend` int(1) NOT NULL,
  `zahlzyklus` int(10) NOT NULL,
  `abgrechnetam` date NOT NULL,
  `rechnung` int(10) NOT NULL,
  `projekt` int(10) NOT NULL,
  `adresse` int(10) NOT NULL,
  `status` varchar(64) NOT NULL,
  `bemerkung` text NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `beschreibung` text NOT NULL,
  `dokument` varchar(64) NOT NULL,
  `preisart` varchar(64) NOT NULL,
  `enddatum` date NOT NULL,
  `angelegtvon` int(11) NOT NULL DEFAULT 0,
  `angelegtam` date NOT NULL,
  `experte` tinyint(1) NOT NULL DEFAULT 0,
  `waehrung` varchar(10) NOT NULL,
  `beschreibungersetzten` tinyint(1) NOT NULL DEFAULT 0,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`,`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abrechnungsartikel_gruppe`
--

DROP TABLE IF EXISTS `abrechnungsartikel_gruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `abrechnungsartikel_gruppe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `beschreibung` varchar(255) NOT NULL,
  `beschreibung2` text NOT NULL,
  `rabatt` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ansprechpartner` varchar(255) NOT NULL,
  `extrarechnung` int(11) DEFAULT NULL,
  `gruppensumme` tinyint(1) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `rechnungadresse` int(11) NOT NULL DEFAULT 0,
  `sammelrechnung` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abschlagsrechnung_rechnung`
--

DROP TABLE IF EXISTS `abschlagsrechnung_rechnung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `abschlagsrechnung_rechnung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rechnung` int(11) NOT NULL DEFAULT 0,
  `auftrag` int(11) NOT NULL DEFAULT 0,
  `bezeichnung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rechnung` (`rechnung`),
  KEY `auftrag` (`auftrag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accordion`
--

DROP TABLE IF EXISTS `accordion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accordion` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `position` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adapterbox`
--

DROP TABLE IF EXISTS `adapterbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adapterbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(128) NOT NULL,
  `verwendenals` varchar(128) NOT NULL,
  `baudrate` varchar(128) NOT NULL,
  `model` varchar(128) NOT NULL,
  `seriennummer` varchar(128) NOT NULL,
  `ipadresse` varchar(128) NOT NULL,
  `netmask` varchar(128) NOT NULL,
  `gateway` varchar(128) NOT NULL,
  `dns` varchar(128) NOT NULL,
  `dhcp` tinyint(1) NOT NULL DEFAULT 1,
  `wlan` tinyint(1) NOT NULL DEFAULT 0,
  `ssid` varchar(128) NOT NULL,
  `passphrase` varchar(256) NOT NULL,
  `letzteverbindung` datetime DEFAULT NULL,
  `tmpip` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adapterbox_log`
--

DROP TABLE IF EXISTS `adapterbox_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adapterbox_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(64) NOT NULL,
  `meldung` varchar(64) NOT NULL,
  `seriennummer` varchar(64) NOT NULL,
  `device` varchar(64) NOT NULL,
  `datum` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adapterbox_request_log`
--

DROP TABLE IF EXISTS `adapterbox_request_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adapterbox_request_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth` varchar(255) NOT NULL,
  `validpass` varchar(255) NOT NULL,
  `device` varchar(255) NOT NULL,
  `digets` varchar(255) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse`
--

DROP TABLE IF EXISTS `adresse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `typ` varchar(255) NOT NULL,
  `marketingsperre` varchar(64) NOT NULL,
  `trackingsperre` int(1) NOT NULL,
  `rechnungsadresse` int(1) NOT NULL,
  `sprache` varchar(32) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `land` varchar(64) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `ort` varchar(64) NOT NULL,
  `plz` varchar(64) NOT NULL,
  `telefon` varchar(64) DEFAULT NULL,
  `telefax` varchar(64) DEFAULT NULL,
  `mobil` varchar(64) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `ustid` varchar(64) DEFAULT NULL,
  `ust_befreit` int(1) NOT NULL,
  `passwort_gesendet` int(1) NOT NULL,
  `sonstiges` text NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `kundenfreigabe` int(1) NOT NULL,
  `steuer` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `kundennummer` varchar(255) NOT NULL,
  `lieferantennummer` varchar(255) NOT NULL,
  `mitarbeiternummer` varchar(255) NOT NULL,
  `konto` varchar(64) DEFAULT NULL,
  `blz` varchar(64) DEFAULT NULL,
  `bank` varchar(255) NOT NULL,
  `inhaber` varchar(255) NOT NULL,
  `swift` varchar(64) DEFAULT NULL,
  `iban` varchar(64) DEFAULT NULL,
  `waehrung` varchar(255) NOT NULL,
  `paypal` varchar(255) NOT NULL,
  `paypalinhaber` varchar(255) NOT NULL,
  `paypalwaehrung` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `partner` int(11) NOT NULL,
  `zahlungsweise` varchar(64) NOT NULL,
  `zahlungszieltage` varchar(64) NOT NULL,
  `zahlungszieltageskonto` varchar(64) NOT NULL,
  `zahlungszielskonto` varchar(64) NOT NULL,
  `versandart` varchar(64) NOT NULL,
  `kundennummerlieferant` varchar(64) NOT NULL,
  `zahlungsweiselieferant` varchar(64) NOT NULL,
  `zahlungszieltagelieferant` varchar(64) NOT NULL,
  `zahlungszieltageskontolieferant` varchar(64) NOT NULL,
  `zahlungszielskontolieferant` varchar(64) NOT NULL,
  `versandartlieferant` varchar(64) NOT NULL,
  `geloescht` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `webid` varchar(1024) DEFAULT NULL,
  `vorname` varchar(255) DEFAULT NULL,
  `kennung` varchar(255) DEFAULT NULL,
  `sachkonto` varchar(20) NOT NULL,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `filiale` text DEFAULT NULL,
  `vertrieb` int(11) DEFAULT NULL,
  `innendienst` int(11) DEFAULT NULL,
  `verbandsnummer` varchar(255) DEFAULT NULL,
  `abweichendeemailab` varchar(64) DEFAULT NULL,
  `portofrei_aktiv` decimal(10,2) DEFAULT NULL,
  `portofreiab` decimal(10,2) NOT NULL DEFAULT 0.00,
  `infoauftragserfassung` text NOT NULL,
  `mandatsreferenz` varchar(255) NOT NULL,
  `mandatsreferenzdatum` date DEFAULT NULL,
  `mandatsreferenzaenderung` tinyint(1) NOT NULL DEFAULT 0,
  `glaeubigeridentnr` varchar(255) NOT NULL,
  `kreditlimit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tour` int(11) NOT NULL DEFAULT 0,
  `zahlungskonditionen_festschreiben` int(1) DEFAULT NULL,
  `rabatte_festschreiben` int(1) DEFAULT NULL,
  `mlmaktiv` int(1) DEFAULT NULL,
  `mlmvertragsbeginn` date DEFAULT NULL,
  `mlmlizenzgebuehrbis` date DEFAULT NULL,
  `mlmfestsetzenbis` date DEFAULT NULL,
  `mlmfestsetzen` int(1) NOT NULL DEFAULT 0,
  `mlmmindestpunkte` int(1) NOT NULL DEFAULT 0,
  `mlmwartekonto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `abweichende_rechnungsadresse` int(1) NOT NULL DEFAULT 0,
  `rechnung_vorname` varchar(64) DEFAULT NULL,
  `rechnung_name` varchar(64) DEFAULT NULL,
  `rechnung_titel` varchar(64) DEFAULT NULL,
  `rechnung_typ` varchar(64) DEFAULT NULL,
  `rechnung_strasse` varchar(64) DEFAULT NULL,
  `rechnung_ort` varchar(64) DEFAULT NULL,
  `rechnung_plz` varchar(64) DEFAULT NULL,
  `rechnung_ansprechpartner` varchar(64) DEFAULT NULL,
  `rechnung_land` varchar(64) DEFAULT NULL,
  `rechnung_abteilung` varchar(64) DEFAULT NULL,
  `rechnung_unterabteilung` varchar(64) DEFAULT NULL,
  `rechnung_adresszusatz` varchar(64) DEFAULT NULL,
  `rechnung_telefon` varchar(64) DEFAULT NULL,
  `rechnung_telefax` varchar(64) DEFAULT NULL,
  `rechnung_anschreiben` varchar(64) DEFAULT NULL,
  `rechnung_email` varchar(64) DEFAULT NULL,
  `geburtstag` date DEFAULT NULL,
  `rolledatum` date DEFAULT NULL,
  `liefersperre` int(1) DEFAULT NULL,
  `liefersperregrund` text DEFAULT NULL,
  `mlmpositionierung` varchar(255) DEFAULT NULL,
  `steuernummer` varchar(255) DEFAULT NULL,
  `steuerbefreit` int(1) DEFAULT NULL,
  `mlmmitmwst` int(1) DEFAULT NULL,
  `mlmabrechnung` varchar(64) DEFAULT NULL,
  `mlmwaehrungauszahlung` varchar(64) DEFAULT NULL,
  `mlmauszahlungprojekt` int(11) NOT NULL DEFAULT 0,
  `sponsor` int(11) DEFAULT NULL,
  `geworbenvon` int(11) DEFAULT NULL,
  `logfile` text DEFAULT NULL,
  `kalender_aufgaben` int(1) DEFAULT NULL,
  `verrechnungskontoreisekosten` int(11) NOT NULL DEFAULT 0,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rabatt` decimal(10,2) DEFAULT NULL,
  `provision` decimal(10,2) DEFAULT NULL,
  `rabattinformation` text DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `internetseite` text DEFAULT NULL,
  `bonus1` decimal(10,2) DEFAULT NULL,
  `bonus1_ab` decimal(10,2) DEFAULT NULL,
  `bonus2` decimal(10,2) DEFAULT NULL,
  `bonus2_ab` decimal(10,2) DEFAULT NULL,
  `bonus3` decimal(10,2) DEFAULT NULL,
  `bonus3_ab` decimal(10,2) DEFAULT NULL,
  `bonus4` decimal(10,2) DEFAULT NULL,
  `bonus4_ab` decimal(10,2) DEFAULT NULL,
  `bonus5` decimal(10,2) DEFAULT NULL,
  `bonus5_ab` decimal(10,2) DEFAULT NULL,
  `bonus6` decimal(10,2) DEFAULT NULL,
  `bonus6_ab` decimal(10,2) DEFAULT NULL,
  `bonus7` decimal(10,2) DEFAULT NULL,
  `bonus7_ab` decimal(10,2) DEFAULT NULL,
  `bonus8` decimal(10,2) DEFAULT NULL,
  `bonus8_ab` decimal(10,2) DEFAULT NULL,
  `bonus9` decimal(10,2) DEFAULT NULL,
  `bonus9_ab` decimal(10,2) DEFAULT NULL,
  `bonus10` decimal(10,2) DEFAULT NULL,
  `bonus10_ab` decimal(10,2) DEFAULT NULL,
  `rechnung_periode` int(11) DEFAULT NULL,
  `rechnung_anzahlpapier` int(11) DEFAULT NULL,
  `rechnung_permail` int(1) DEFAULT NULL,
  `titel` varchar(64) DEFAULT NULL,
  `anschreiben` varchar(64) DEFAULT NULL,
  `nachname` varchar(128) NOT NULL,
  `arbeitszeitprowoche` decimal(10,2) NOT NULL DEFAULT 0.00,
  `folgebestaetigungsperre` tinyint(1) NOT NULL DEFAULT 0,
  `lieferantennummerbeikunde` varchar(128) DEFAULT NULL,
  `verein_mitglied_seit` date DEFAULT NULL,
  `verein_mitglied_bis` date DEFAULT NULL,
  `verein_mitglied_aktiv` tinyint(1) DEFAULT NULL,
  `verein_spendenbescheinigung` tinyint(1) NOT NULL DEFAULT 0,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `rechnung_papier` tinyint(1) NOT NULL DEFAULT 0,
  `angebot_cc` varchar(128) NOT NULL,
  `auftrag_cc` varchar(128) NOT NULL,
  `rechnung_cc` varchar(128) NOT NULL,
  `gutschrift_cc` varchar(128) NOT NULL,
  `lieferschein_cc` varchar(128) NOT NULL,
  `bestellung_cc` varchar(128) NOT NULL,
  `angebot_fax_cc` varchar(128) NOT NULL,
  `auftrag_fax_cc` varchar(128) NOT NULL,
  `rechnung_fax_cc` varchar(128) NOT NULL,
  `gutschrift_fax_cc` varchar(128) NOT NULL,
  `lieferschein_fax_cc` varchar(128) NOT NULL,
  `bestellung_fax_cc` varchar(128) NOT NULL,
  `abperfax` tinyint(1) NOT NULL DEFAULT 0,
  `abpermail` varchar(128) NOT NULL,
  `kassiereraktiv` int(1) NOT NULL DEFAULT 0,
  `kassierernummer` varchar(10) NOT NULL,
  `kassiererprojekt` int(11) NOT NULL DEFAULT 0,
  `portofreilieferant_aktiv` tinyint(1) NOT NULL DEFAULT 0,
  `portofreiablieferant` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mandatsreferenzart` varchar(64) NOT NULL,
  `mandatsreferenzwdhart` varchar(64) NOT NULL,
  `serienbrief` tinyint(1) NOT NULL DEFAULT 0,
  `kundennummer_buchhaltung` varchar(20) NOT NULL,
  `lieferantennummer_buchhaltung` varchar(20) NOT NULL,
  `lead` tinyint(1) NOT NULL DEFAULT 0,
  `zahlungsweiseabo` varchar(64) NOT NULL,
  `bundesland` varchar(64) NOT NULL,
  `mandatsreferenzhinweis` text DEFAULT NULL,
  `geburtstagkalender` tinyint(1) NOT NULL DEFAULT 0,
  `geburtstagskarte` tinyint(1) NOT NULL DEFAULT 0,
  `liefersperredatum` date DEFAULT NULL,
  `umsatzsteuer_lieferant` varchar(64) NOT NULL,
  `lat` decimal(18,12) DEFAULT NULL,
  `lng` decimal(18,12) DEFAULT NULL,
  `art` varchar(32) NOT NULL,
  `fromshop` int(11) NOT NULL DEFAULT 0,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `angebot_email` varchar(128) NOT NULL,
  `auftrag_email` varchar(128) NOT NULL,
  `rechnungs_email` varchar(128) NOT NULL,
  `gutschrift_email` varchar(128) NOT NULL,
  `lieferschein_email` varchar(128) NOT NULL,
  `bestellung_email` varchar(128) NOT NULL,
  `lieferschwellenichtanwenden` tinyint(1) NOT NULL DEFAULT 0,
  `hinweistextlieferant` text NOT NULL,
  `firmensepa` tinyint(1) NOT NULL DEFAULT 0,
  `hinweis_einfuegen` text NOT NULL,
  `anzeigesteuerbelege` int(11) NOT NULL DEFAULT 0,
  `gln` varchar(32) NOT NULL,
  `rechnung_gln` varchar(32) NOT NULL,
  `keinealtersabfrage` tinyint(1) NOT NULL DEFAULT 0,
  `lieferbedingung` text NOT NULL,
  `mlmintranetgesamtestruktur` tinyint(1) NOT NULL DEFAULT 0,
  `kommissionskonsignationslager` int(11) NOT NULL DEFAULT 0,
  `zollinformationen` text NOT NULL,
  `bundesstaat` varchar(32) NOT NULL,
  `rechnung_bundesstaat` varchar(32) NOT NULL,
  `rechnung_anzahlpapier_abweichend` int(1) DEFAULT NULL,
  `kontorahmen` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `projekt` (`projekt`),
  KEY `kundennummer` (`kundennummer`),
  KEY `lieferantennummer` (`lieferantennummer`),
  KEY `usereditid` (`usereditid`),
  KEY `plz` (`plz`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_abosammelrechnungen`
--

DROP TABLE IF EXISTS `adresse_abosammelrechnungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_abosammelrechnungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `rabatt` decimal(10,2) NOT NULL DEFAULT 0.00,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `abweichende_rechnungsadresse` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_accounts`
--

DROP TABLE IF EXISTS `adresse_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `adresse` int(11) DEFAULT NULL,
  `bezeichnung` varchar(128) DEFAULT NULL,
  `art` varchar(128) DEFAULT NULL,
  `url` text NOT NULL,
  `benutzername` text NOT NULL,
  `passwort` text NOT NULL,
  `webid` int(11) NOT NULL DEFAULT 0,
  `gueltig_ab` date DEFAULT NULL,
  `gueltig_bis` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_filter`
--

DROP TABLE IF EXISTS `adresse_filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `ansprechpartner` tinyint(1) NOT NULL DEFAULT 0,
  `user` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_filter_gruppen`
--

DROP TABLE IF EXISTS `adresse_filter_gruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_filter_gruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filter` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `isand` tinyint(1) NOT NULL DEFAULT 1,
  `isnot` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `filter` (`filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_filter_positionen`
--

DROP TABLE IF EXISTS `adresse_filter_positionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_filter_positionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filter` int(11) NOT NULL DEFAULT 0,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(32) NOT NULL,
  `typ2` varchar(32) NOT NULL,
  `isand` tinyint(1) NOT NULL DEFAULT 0,
  `isnot` tinyint(1) NOT NULL DEFAULT 0,
  `parameter1` varchar(64) NOT NULL,
  `parameter2` varchar(64) NOT NULL,
  `parameter3` varchar(64) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `filter` (`filter`),
  KEY `gruppe` (`gruppe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_import`
--

DROP TABLE IF EXISTS `adresse_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typ` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `plz` varchar(64) DEFAULT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(64) DEFAULT NULL,
  `telefon` varchar(128) DEFAULT NULL,
  `telefax` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `mobil` varchar(64) DEFAULT NULL,
  `internetseite` varchar(255) NOT NULL,
  `ustid` varchar(64) DEFAULT NULL,
  `user` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `angelegt_am` datetime DEFAULT NULL,
  `abgeschlossen` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_kontakhistorie`
--

DROP TABLE IF EXISTS `adresse_kontakhistorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_kontakhistorie` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `adresse` int(10) NOT NULL,
  `grund` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `datum` datetime NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_kontakte`
--

DROP TABLE IF EXISTS `adresse_kontakte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_kontakte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) DEFAULT NULL,
  `bezeichnung` varchar(1024) DEFAULT NULL,
  `kontakt` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_rolle`
--

DROP TABLE IF EXISTS `adresse_rolle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_rolle` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `adresse` int(10) NOT NULL,
  `projekt` int(11) NOT NULL,
  `subjekt` varchar(255) NOT NULL,
  `praedikat` varchar(255) NOT NULL,
  `objekt` varchar(255) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `von` date NOT NULL,
  `bis` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`),
  KEY `projekt` (`projekt`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adresse_typ`
--

DROP TABLE IF EXISTS `adresse_typ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adresse_typ` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `netto` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adressetiketten`
--

DROP TABLE IF EXISTS `adressetiketten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adressetiketten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `etikett` int(11) NOT NULL DEFAULT 0,
  `verwenden_als` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aktionscode_liste`
--

DROP TABLE IF EXISTS `aktionscode_liste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aktionscode_liste` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) NOT NULL,
  `beschriftung` varchar(64) NOT NULL,
  `ausblenden` tinyint(1) NOT NULL DEFAULT 0,
  `bemerkung` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amainvoice_config`
--

DROP TABLE IF EXISTS `amainvoice_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amainvoice_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amainvoice_files`
--

DROP TABLE IF EXISTS `amainvoice_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amainvoice_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `status` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amazon_article`
--

DROP TABLE IF EXISTS `amazon_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amazon_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `seller_sku` varchar(255) NOT NULL,
  `asin` varchar(16) NOT NULL,
  `is_fba` tinyint(4) NOT NULL DEFAULT -1,
  `last_check` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `seller_sku` (`seller_sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amazon_rechnung_anlegen`
--

DROP TABLE IF EXISTS `amazon_rechnung_anlegen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amazon_rechnung_anlegen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopid` int(11) NOT NULL DEFAULT 0,
  `auftrag` int(11) NOT NULL DEFAULT 0,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `fba` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `shopid` (`shopid`,`auftrag`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amazon_report`
--

DROP TABLE IF EXISTS `amazon_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amazon_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT 0,
  `reportid` varchar(32) DEFAULT NULL,
  `requestreportid` varchar(32) DEFAULT NULL,
  `reporttype` varchar(255) DEFAULT NULL,
  `marketplaces` varchar(255) DEFAULT NULL,
  `requesttype` varchar(64) DEFAULT NULL,
  `report_processing_status` varchar(255) DEFAULT '_done_',
  `imported` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  `liveimported` tinyint(1) DEFAULT 0,
  `createreturnorders` tinyint(1) DEFAULT 0,
  `lastchecked` datetime DEFAULT NULL,
  `report_options` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`,`reportid`),
  KEY `reporttype` (`reporttype`),
  KEY `requestreportid` (`requestreportid`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amazon_report_schedule`
--

DROP TABLE IF EXISTS `amazon_report_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amazon_report_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) unsigned NOT NULL DEFAULT 0,
  `report_type` varchar(64) NOT NULL,
  `schedule` varchar(12) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `scheduled_date` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_id` (`shop_id`,`report_type`,`schedule`,`scheduled_date`,`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amazon_shipment_info`
--

DROP TABLE IF EXISTS `amazon_shipment_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amazon_shipment_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `orderid` varchar(19) NOT NULL,
  `orderitemid` varchar(32) NOT NULL,
  `merchantorderid` varchar(32) NOT NULL,
  `merchantorderitemid` varchar(32) NOT NULL,
  `shipmentitemid` varchar(32) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `carrier` varchar(32) NOT NULL,
  `currency` varchar(8) NOT NULL,
  `tracking_number` varchar(32) NOT NULL,
  `sales_channel` varchar(32) NOT NULL,
  `fulfillment_channel` varchar(32) NOT NULL,
  `fulfillment_center_id` varchar(32) NOT NULL,
  `quantity_shipped` int(11) NOT NULL DEFAULT 0,
  `shipment_date` datetime DEFAULT NULL,
  `estimated_arrival_date` datetime DEFAULT NULL,
  `ship_address_1` varchar(255) NOT NULL,
  `ship_address_2` varchar(255) NOT NULL,
  `ship_address_3` varchar(255) NOT NULL,
  `ship_city` varchar(255) NOT NULL,
  `ship_state` varchar(255) NOT NULL,
  `ship_postal_code` varchar(255) NOT NULL,
  `ship_country` varchar(255) NOT NULL,
  `ship_phone_number` varchar(255) NOT NULL,
  `bill_address_1` varchar(255) NOT NULL,
  `bill_address_2` varchar(255) NOT NULL,
  `bill_address_3` varchar(255) NOT NULL,
  `bill_city` varchar(255) NOT NULL,
  `bill_state` varchar(255) NOT NULL,
  `bill_postal_code` varchar(255) NOT NULL,
  `bill_country` varchar(255) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `buyer_name` varchar(255) NOT NULL,
  `item_price` decimal(10,2) DEFAULT NULL,
  `item_tax` decimal(10,2) DEFAULT NULL,
  `shipping_price` decimal(10,2) DEFAULT NULL,
  `shipping_tax` decimal(10,2) DEFAULT NULL,
  `gift_wrap_price` decimal(10,2) DEFAULT NULL,
  `gift_wrap_tax` decimal(10,2) DEFAULT NULL,
  `item_promotion_discount` decimal(10,2) DEFAULT NULL,
  `ship_promotion_discount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amazon_vat_report`
--

DROP TABLE IF EXISTS `amazon_vat_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amazon_vat_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) unsigned NOT NULL DEFAULT 0,
  `orderid` varchar(19) NOT NULL,
  `sku` varchar(64) NOT NULL,
  `transaction_type` varchar(8) NOT NULL,
  `fullrow` text DEFAULT NULL,
  `hash_sha1` varchar(40) NOT NULL,
  `hash_nr` int(11) NOT NULL DEFAULT 0,
  `on_orderimport` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `order_changed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `invoice_changed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `credit_note_changed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `invoice_created` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `credit_note_created` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `doctype_id` int(11) NOT NULL DEFAULT 0,
  `position_id` int(11) NOT NULL DEFAULT 0,
  `invoicenumber` varchar(64) NOT NULL,
  `shipment_date` date DEFAULT NULL,
  `tax_calculation_date` date DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orderid` (`orderid`),
  KEY `hash_sha1` (`hash_sha1`),
  KEY `transaction_type` (`transaction_type`),
  KEY `position_id` (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amazon_vatinvoice`
--

DROP TABLE IF EXISTS `amazon_vatinvoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amazon_vatinvoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopid` int(11) NOT NULL DEFAULT 0,
  `orderid` varchar(255) NOT NULL,
  `vat_invoice_number` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `from_city` varchar(255) NOT NULL,
  `from_state` varchar(255) NOT NULL,
  `from_country` varchar(255) NOT NULL,
  `from_postal_code` varchar(255) NOT NULL,
  `from_location_code` varchar(255) NOT NULL,
  `seller_tax_registration` varchar(255) NOT NULL,
  `buyer_tax_registration` varchar(255) NOT NULL,
  `shipment_date` date DEFAULT NULL,
  `isreturn` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `shopid` (`shopid`,`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amazoninvoice_position`
--

DROP TABLE IF EXISTS `amazoninvoice_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amazoninvoice_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctype` varchar(32) DEFAULT NULL,
  `doctype_id` int(11) DEFAULT 0,
  `position_id` int(11) DEFAULT 0,
  `inv_rech_nr` varchar(255) DEFAULT NULL,
  `inv_date` varchar(255) DEFAULT NULL,
  `amazonorderid` varchar(255) DEFAULT NULL,
  `shipmentdate` varchar(255) DEFAULT NULL,
  `buyeremail` varchar(255) DEFAULT NULL,
  `buyerphonenumber` varchar(255) DEFAULT NULL,
  `buyername` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `productname` varchar(255) DEFAULT NULL,
  `quantitypurchased` varchar(255) DEFAULT NULL,
  `quantityshipped` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `mwst` varchar(255) DEFAULT NULL,
  `taxrate` varchar(255) DEFAULT NULL,
  `brutto_total` varchar(255) DEFAULT NULL,
  `netto_total` varchar(255) DEFAULT NULL,
  `tax_total` varchar(255) DEFAULT NULL,
  `itemprice` varchar(255) DEFAULT NULL,
  `itemprice_netto` varchar(255) DEFAULT NULL,
  `itemprice_tax` varchar(255) DEFAULT NULL,
  `shippingprice` varchar(255) DEFAULT NULL,
  `shippingprice_netto` varchar(255) DEFAULT NULL,
  `shippingprice_tax` varchar(255) DEFAULT NULL,
  `giftwrapprice` varchar(255) DEFAULT NULL,
  `giftwrapprice_netto` varchar(255) DEFAULT NULL,
  `giftwrapprice_tax` varchar(255) DEFAULT NULL,
  `itempromotiondiscount` varchar(255) DEFAULT NULL,
  `itempromotiondiscount_netto` varchar(255) DEFAULT NULL,
  `itempromotiondiscount_tax` varchar(255) DEFAULT NULL,
  `shippromotiondiscount` varchar(255) DEFAULT NULL,
  `shippromotiondiscount_netto` varchar(255) DEFAULT NULL,
  `shippromotiondiscount_tax` varchar(255) DEFAULT NULL,
  `giftwrappromotiondiscount` varchar(255) DEFAULT NULL,
  `giftwrappromotiondiscount_netto` varchar(255) DEFAULT NULL,
  `giftwrappromotiondiscount_tax` varchar(255) DEFAULT NULL,
  `shipservicelevel` varchar(255) DEFAULT NULL,
  `recipientname` varchar(255) DEFAULT NULL,
  `shipaddress1` varchar(255) DEFAULT NULL,
  `shipaddress2` varchar(255) DEFAULT NULL,
  `shipaddress3` varchar(255) DEFAULT NULL,
  `shipcity` varchar(255) DEFAULT NULL,
  `shipstate` varchar(255) DEFAULT NULL,
  `shippostalcode` varchar(255) DEFAULT NULL,
  `shipcountry` varchar(255) DEFAULT NULL,
  `shipphonenumber` varchar(255) DEFAULT NULL,
  `billaddress1` varchar(255) DEFAULT NULL,
  `billaddress2` varchar(255) DEFAULT NULL,
  `billaddress3` varchar(255) DEFAULT NULL,
  `billcity` varchar(255) DEFAULT NULL,
  `billstate` varchar(255) DEFAULT NULL,
  `billpostalcode` varchar(255) DEFAULT NULL,
  `billcountry` varchar(255) DEFAULT NULL,
  `carrier` varchar(255) DEFAULT NULL,
  `trackingnumber` varchar(255) DEFAULT NULL,
  `fulfillmentcenterid` varchar(255) DEFAULT NULL,
  `fulfillmentchannel` varchar(255) DEFAULT NULL,
  `saleschannel` varchar(255) DEFAULT NULL,
  `asin` varchar(255) DEFAULT NULL,
  `conditiontype` varchar(255) DEFAULT NULL,
  `quantityavailable` varchar(255) DEFAULT NULL,
  `isbusinessorder` varchar(255) DEFAULT NULL,
  `uid` varchar(255) DEFAULT NULL,
  `vatcheck` varchar(255) DEFAULT NULL,
  `documentlink` varchar(255) DEFAULT NULL,
  `order_id` int(11) DEFAULT 0,
  `rem_gs_nr` varchar(255) DEFAULT NULL,
  `orderid` varchar(255) DEFAULT NULL,
  `rem_date` varchar(255) DEFAULT NULL,
  `returndate` varchar(255) DEFAULT NULL,
  `buyercompanyname` varchar(255) DEFAULT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `remreturnshipcost` varchar(255) DEFAULT NULL,
  `remsondererstattung` varchar(255) DEFAULT NULL,
  `itempromotionid` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `rem_gs_nr_real` varchar(255) DEFAULT NULL,
  `create` tinyint(1) DEFAULT 0,
  `create_order` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `anfrage`
--

DROP TABLE IF EXISTS `anfrage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anfrage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `projekt` varchar(255) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `auftrag` varchar(255) NOT NULL,
  `auftragid` int(11) NOT NULL,
  `freitext` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `adresse` int(11) NOT NULL,
  `mitarbeiter` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(255) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `versand` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `inbearbeitung_user` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `zuarchivieren` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `vertriebid` int(11) DEFAULT NULL,
  `bearbeiterid` int(11) DEFAULT NULL,
  `aktion` varchar(64) NOT NULL,
  `vertrieb` varchar(255) NOT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `realrabatt` decimal(10,2) DEFAULT NULL,
  `rabatt` decimal(10,2) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(255) NOT NULL DEFAULT 'EUR',
  `typ` varchar(16) DEFAULT NULL,
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `internebemerkung` text DEFAULT NULL,
  `sprache` varchar(32) NOT NULL,
  `bodyzusatz` text NOT NULL,
  `lieferbedingung` text NOT NULL,
  `titel` varchar(64) NOT NULL,
  `bundesstaat` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `anfrage_position`
--

DROP TABLE IF EXISTS `anfrage_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anfrage_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `anfrage` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `menge` float NOT NULL,
  `sort` int(10) NOT NULL,
  `bemerkung` text NOT NULL,
  `preis` decimal(10,4) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(255) DEFAULT NULL,
  `grundrabatt` decimal(10,2) DEFAULT NULL,
  `erloese` varchar(8) DEFAULT NULL,
  `erloesefestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `rabattsync` int(1) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `geliefert` decimal(14,4) NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `einheit` varchar(255) NOT NULL,
  `lieferdatum` date DEFAULT NULL,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `anfrage` (`anfrage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `anfrage_protokoll`
--

DROP TABLE IF EXISTS `anfrage_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anfrage_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anfrage` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `anfrage` (`anfrage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `angebot`
--

DROP TABLE IF EXISTS `angebot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `angebot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `gueltigbis` date NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `anfrage` varchar(255) NOT NULL,
  `auftrag` varchar(255) NOT NULL,
  `freitext` text NOT NULL,
  `internebemerkung` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `adresse` int(11) NOT NULL,
  `retyp` varchar(255) NOT NULL,
  `rechnungname` varchar(255) NOT NULL,
  `retelefon` varchar(255) NOT NULL,
  `reansprechpartner` varchar(255) NOT NULL,
  `retelefax` varchar(255) NOT NULL,
  `reabteilung` varchar(255) NOT NULL,
  `reemail` varchar(255) NOT NULL,
  `reunterabteilung` varchar(255) NOT NULL,
  `readresszusatz` varchar(255) NOT NULL,
  `restrasse` varchar(255) NOT NULL,
  `replz` varchar(255) NOT NULL,
  `reort` varchar(255) NOT NULL,
  `reland` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(64) DEFAULT NULL,
  `versandart` varchar(255) NOT NULL,
  `vertrieb` varchar(255) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungszieltage` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `zahlungszielskonto` decimal(10,2) NOT NULL,
  `gesamtsumme` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `bank_inhaber` varchar(255) NOT NULL,
  `bank_institut` varchar(255) NOT NULL,
  `bank_blz` int(11) NOT NULL,
  `bank_konto` int(11) NOT NULL,
  `kreditkarte_typ` varchar(255) NOT NULL,
  `kreditkarte_inhaber` varchar(255) NOT NULL,
  `kreditkarte_nummer` varchar(255) NOT NULL,
  `kreditkarte_pruefnummer` varchar(255) NOT NULL,
  `kreditkarte_monat` int(11) NOT NULL,
  `kreditkarte_jahr` int(11) NOT NULL,
  `abweichendelieferadresse` int(1) NOT NULL,
  `abweichenderechnungsadresse` int(1) NOT NULL,
  `liefername` varchar(255) NOT NULL,
  `lieferabteilung` varchar(255) NOT NULL,
  `lieferunterabteilung` varchar(255) NOT NULL,
  `lieferland` varchar(255) NOT NULL,
  `lieferstrasse` varchar(255) NOT NULL,
  `lieferort` varchar(255) NOT NULL,
  `lieferplz` varchar(255) NOT NULL,
  `lieferadresszusatz` varchar(255) NOT NULL,
  `lieferansprechpartner` varchar(255) NOT NULL,
  `liefertelefon` varchar(255) NOT NULL,
  `liefertelefax` varchar(255) NOT NULL,
  `liefermail` varchar(255) NOT NULL,
  `autoversand` int(1) NOT NULL,
  `keinporto` int(1) NOT NULL,
  `gesamtsummeausblenden` tinyint(1) NOT NULL,
  `ust_befreit` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `inbearbeitung` int(1) NOT NULL,
  `vermerk` text NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ansprechpartner` varchar(255) DEFAULT NULL,
  `deckungsbeitragcalc` tinyint(1) NOT NULL DEFAULT 0,
  `deckungsbeitrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `erloes_netto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `umsatz_netto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `lieferdatum` date DEFAULT NULL,
  `vertriebid` int(11) DEFAULT NULL,
  `aktion` varchar(64) NOT NULL,
  `provision` decimal(10,2) DEFAULT NULL,
  `provision_summe` decimal(18,2) NOT NULL DEFAULT 0.00,
  `keinsteuersatz` int(1) DEFAULT NULL,
  `anfrageid` int(11) NOT NULL DEFAULT 0,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `realrabatt` decimal(10,2) DEFAULT NULL,
  `rabatt` decimal(10,2) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(255) NOT NULL DEFAULT 'EUR',
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviert` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviertversion` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(255) NOT NULL DEFAULT 'firma',
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `auftragid` int(11) NOT NULL DEFAULT 0,
  `lieferid` int(11) NOT NULL DEFAULT 0,
  `ansprechpartnerid` int(11) NOT NULL DEFAULT 0,
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `abweichendebezeichnung` tinyint(1) NOT NULL DEFAULT 0,
  `zuarchivieren` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `angelegtam` datetime DEFAULT NULL,
  `kopievon` int(11) NOT NULL DEFAULT 0,
  `kopienummer` int(11) NOT NULL DEFAULT 0,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  `sprache` varchar(32) NOT NULL,
  `liefergln` varchar(64) NOT NULL,
  `lieferemail` varchar(200) NOT NULL,
  `gln` varchar(64) NOT NULL,
  `planedorderdate` date DEFAULT NULL,
  `bearbeiterid` int(11) DEFAULT NULL,
  `kurs` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `ohne_artikeltext` int(1) DEFAULT NULL,
  `anzeigesteuer` tinyint(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `bodyzusatz` text NOT NULL,
  `lieferbedingung` text NOT NULL,
  `titel` varchar(64) NOT NULL,
  `liefertitel` varchar(64) NOT NULL,
  `standardlager` int(11) NOT NULL DEFAULT 0,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontoberechnet` tinyint(1) NOT NULL DEFAULT 0,
  `shop` int(1) NOT NULL DEFAULT 0,
  `internet` varchar(255) NOT NULL,
  `transaktionsnummer` varchar(255) NOT NULL,
  `packstation_inhaber` varchar(255) NOT NULL,
  `packstation_station` varchar(255) NOT NULL,
  `packstation_ident` varchar(255) NOT NULL,
  `packstation_plz` varchar(64) NOT NULL,
  `packstation_ort` varchar(255) NOT NULL,
  `shopextid` varchar(1024) NOT NULL,
  `bundesstaat` varchar(32) NOT NULL,
  `lieferbundesstaat` varchar(32) NOT NULL,
  `rabatteportofestschreiben` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`),
  KEY `adresse` (`adresse`),
  KEY `vertriebid` (`vertriebid`),
  KEY `status` (`status`),
  KEY `datum` (`datum`),
  KEY `belegnr` (`belegnr`),
  KEY `versandart` (`versandart`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `angebot_position`
--

DROP TABLE IF EXISTS `angebot_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `angebot_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `angebot` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `waehrung` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `status` varchar(64) NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `geliefert` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `punkte` decimal(10,2) NOT NULL,
  `bonuspunkte` decimal(10,2) NOT NULL,
  `mlmdirektpraemie` decimal(10,2) DEFAULT NULL,
  `keinrabatterlaubt` int(1) DEFAULT NULL,
  `grundrabatt` decimal(10,2) DEFAULT NULL,
  `rabattsync` int(1) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `einheit` varchar(255) NOT NULL,
  `optional` int(1) NOT NULL DEFAULT 0,
  `rabatt` decimal(10,2) NOT NULL,
  `zolltarifnummer` varchar(128) NOT NULL DEFAULT '0',
  `herkunftsland` varchar(128) NOT NULL DEFAULT '0',
  `artikelnummerkunde` varchar(128) NOT NULL,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(1024) DEFAULT NULL,
  `erloese` varchar(8) DEFAULT NULL,
  `erloesefestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `einkaufspreiswaehrung` varchar(8) NOT NULL,
  `einkaufspreis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `einkaufspreisurspruenglich` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `einkaufspreisid` int(11) NOT NULL DEFAULT 0,
  `ekwaehrung` varchar(8) NOT NULL,
  `deckungsbeitrag` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `formelmenge` varchar(255) NOT NULL,
  `formelpreis` varchar(255) NOT NULL,
  `ohnepreis` int(1) NOT NULL DEFAULT 0,
  `textalternativpreis` varchar(50) NOT NULL,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_gesamt` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_gesamt` decimal(14,4) DEFAULT NULL,
  `steuerbetrag` decimal(14,4) DEFAULT NULL,
  `skontosperre` tinyint(1) NOT NULL DEFAULT 0,
  `berechnen_aus_teile` tinyint(1) DEFAULT 0,
  `ausblenden_im_pdf` tinyint(1) DEFAULT 0,
  `explodiert_parent` int(11) DEFAULT 0,
  `umsatz_netto_einzeln` decimal(14,4) DEFAULT NULL,
  `umsatz_netto_gesamt` decimal(14,4) DEFAULT NULL,
  `umsatz_brutto_einzeln` decimal(14,4) DEFAULT NULL,
  `umsatz_brutto_gesamt` decimal(14,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `angebot` (`angebot`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `angebot_protokoll`
--

DROP TABLE IF EXISTS `angebot_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `angebot_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `angebot` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `angebot` (`angebot`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ansprechpartner`
--

DROP TABLE IF EXISTS `ansprechpartner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ansprechpartner` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `typ` varchar(255) NOT NULL,
  `sprache` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `bereich` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sonstiges` text NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `steuer` varchar(255) NOT NULL,
  `adresse` int(10) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mobil` varchar(64) DEFAULT NULL,
  `titel` varchar(1024) DEFAULT NULL,
  `anschreiben` varchar(1024) DEFAULT NULL,
  `ansprechpartner_land` varchar(255) DEFAULT NULL,
  `vorname` varchar(1024) DEFAULT NULL,
  `geburtstag` date DEFAULT NULL,
  `geburtstagkalender` tinyint(1) NOT NULL DEFAULT 0,
  `geburtstagskarte` tinyint(1) NOT NULL DEFAULT 0,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `interne_bemerkung` varchar(1024) NOT NULL,
  `marketingsperre` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ansprechpartner_gruppen`
--

DROP TABLE IF EXISTS `ansprechpartner_gruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ansprechpartner_gruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ansprechpartner` int(11) NOT NULL DEFAULT 0,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `aktiv` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_account`
--

DROP TABLE IF EXISTS `api_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(64) NOT NULL,
  `initkey` varchar(128) NOT NULL,
  `importwarteschlange_name` varchar(64) NOT NULL,
  `event_url` varchar(128) NOT NULL,
  `remotedomain` varchar(128) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `importwarteschlange` tinyint(1) NOT NULL DEFAULT 1,
  `cleanutf8` tinyint(1) NOT NULL DEFAULT 1,
  `uebertragung_account` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `permissions` text DEFAULT NULL,
  `is_legacy` tinyint(1) NOT NULL DEFAULT 0,
  `ishtmltransformation` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nonce` varchar(64) NOT NULL,
  `opaque` varchar(64) NOT NULL,
  `nonce_count` int(11) NOT NULL DEFAULT 1,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_mapping`
--

DROP TABLE IF EXISTS `api_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api` int(11) NOT NULL DEFAULT 0,
  `uebertragung_account` int(11) NOT NULL DEFAULT 0,
  `tabelle` varchar(64) NOT NULL,
  `id_int` int(11) NOT NULL DEFAULT 0,
  `id_ext` varchar(64) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uebertragung_account` (`uebertragung_account`),
  KEY `id_ext` (`id_ext`),
  KEY `api` (`api`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_permission`
--

DROP TABLE IF EXISTS `api_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_regel`
--

DROP TABLE IF EXISTS `api_regel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_regel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `action` varchar(64) NOT NULL,
  `bedingung` varchar(1024) NOT NULL,
  `parameter` varchar(1024) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `sofortuebertragen` tinyint(1) NOT NULL DEFAULT 0,
  `prio` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(64) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_request`
--

DROP TABLE IF EXISTS `api_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api` int(11) NOT NULL DEFAULT 0,
  `uebertragung_account` int(11) NOT NULL DEFAULT 0,
  `status` varchar(32) NOT NULL DEFAULT 'angelegt',
  `prio` int(11) NOT NULL DEFAULT 0,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `typ` varchar(255) NOT NULL,
  `parameter1` varchar(255) NOT NULL,
  `parameter1int` int(11) NOT NULL DEFAULT 0,
  `parameter2` varchar(255) NOT NULL,
  `anzeige` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `uebertragen` tinyint(1) NOT NULL DEFAULT 0,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `datei` varchar(255) NOT NULL,
  `uebertragen_am` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anzahl_uebertragen` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `uebertragung_account` (`uebertragung_account`),
  KEY `parameter1int` (`parameter1int`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_request_response_log`
--

DROP TABLE IF EXISTS `api_request_response_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_request_response_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_id` int(11) NOT NULL DEFAULT 0,
  `raw_request` mediumtext DEFAULT NULL,
  `raw_response` mediumtext DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `status` varchar(64) NOT NULL,
  `doctype` varchar(64) NOT NULL,
  `doctype_id` int(11) NOT NULL DEFAULT 0,
  `is_incomming` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `api_id` (`api_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `arbeitsfreietage`
--

DROP TABLE IF EXISTS `arbeitsfreietage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arbeitsfreietage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(128) NOT NULL,
  `typ` varchar(128) NOT NULL,
  `datum` date DEFAULT NULL,
  `projekt` int(11) NOT NULL DEFAULT 1,
  `land` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `datum` (`datum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `arbeitspaket`
--

DROP TABLE IF EXISTS `arbeitspaket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arbeitspaket` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `adresse` int(10) NOT NULL,
  `aufgabe` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `projekt` int(10) NOT NULL,
  `zeit_geplant` decimal(10,2) NOT NULL,
  `kostenstelle` int(11) NOT NULL,
  `status` varchar(64) NOT NULL,
  `abgabe` varchar(255) NOT NULL,
  `abgenommen` varchar(255) NOT NULL,
  `abgenommen_von` int(10) NOT NULL,
  `abgenommen_bemerkung` text NOT NULL,
  `initiator` int(10) NOT NULL,
  `art` varchar(255) NOT NULL,
  `abgabedatum` date NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `geloescht` int(1) DEFAULT NULL,
  `vorgaenger` int(11) DEFAULT NULL,
  `kosten_geplant` decimal(10,4) DEFAULT NULL,
  `artikel_geplant` int(11) DEFAULT NULL,
  `auftragid` int(11) DEFAULT NULL,
  `abgerechnet` int(1) NOT NULL DEFAULT 0,
  `cache_BE` int(11) NOT NULL DEFAULT 0,
  `cache_PR` int(11) NOT NULL DEFAULT 0,
  `cache_AN` int(11) NOT NULL DEFAULT 0,
  `cache_AB` int(11) NOT NULL DEFAULT 0,
  `cache_LS` int(11) NOT NULL DEFAULT 0,
  `cache_RE` int(11) NOT NULL DEFAULT 0,
  `cache_GS` int(11) NOT NULL DEFAULT 0,
  `last_cache` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aktiv` tinyint(1) NOT NULL DEFAULT 0,
  `startdatum` date NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `ek_geplant` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `vk_geplant` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `kalkulationbasis` varchar(64) NOT NULL DEFAULT 'stundenbasis',
  `cache_PF` int(11) NOT NULL DEFAULT 0,
  `farbe` varchar(16) NOT NULL,
  `vkkalkulationbasis` varchar(64) DEFAULT NULL,
  `projektplanausblenden` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `article_label`
--

DROP TABLE IF EXISTS `article_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `label_id` int(11) NOT NULL DEFAULT 0,
  `printer_id` int(11) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL DEFAULT 0,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `article_property_translation`
--

DROP TABLE IF EXISTS `article_property_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_property_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `language_from` varchar(32) NOT NULL,
  `language_to` varchar(32) NOT NULL,
  `property_from` varchar(255) NOT NULL,
  `property_to` varchar(255) NOT NULL,
  `property_value_from` varchar(255) NOT NULL,
  `property_value_to` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `category_id` (`category_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel`
--

DROP TABLE IF EXISTS `artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `typ` varchar(255) NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `checksum` text NOT NULL,
  `projekt` int(11) NOT NULL,
  `inaktiv` varchar(255) NOT NULL,
  `ausverkauft` int(1) NOT NULL,
  `warengruppe` varchar(255) NOT NULL,
  `name_de` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `kurztext_de` text NOT NULL,
  `kurztext_en` text NOT NULL,
  `beschreibung_de` text NOT NULL,
  `beschreibung_en` text NOT NULL,
  `uebersicht_de` text NOT NULL,
  `uebersicht_en` text NOT NULL,
  `links_de` text NOT NULL,
  `links_en` text NOT NULL,
  `startseite_de` text NOT NULL,
  `startseite_en` text NOT NULL,
  `standardbild` varchar(255) NOT NULL,
  `herstellerlink` varchar(255) NOT NULL,
  `hersteller` varchar(255) NOT NULL,
  `teilbar` varchar(255) NOT NULL,
  `nteile` varchar(255) NOT NULL,
  `seriennummern` varchar(255) NOT NULL,
  `lager_platz` varchar(255) NOT NULL,
  `lieferzeit` varchar(255) NOT NULL,
  `lieferzeitmanuell` varchar(255) NOT NULL,
  `sonstiges` text NOT NULL,
  `gewicht` varchar(255) NOT NULL,
  `endmontage` varchar(255) NOT NULL,
  `funktionstest` varchar(255) NOT NULL,
  `artikelcheckliste` varchar(255) NOT NULL,
  `stueckliste` int(1) NOT NULL,
  `juststueckliste` int(1) NOT NULL,
  `barcode` varchar(7) NOT NULL,
  `hinzugefuegt` varchar(255) NOT NULL,
  `pcbdecal` varchar(255) NOT NULL,
  `lagerartikel` int(1) NOT NULL,
  `porto` int(1) NOT NULL,
  `chargenverwaltung` int(1) NOT NULL,
  `provisionsartikel` int(1) NOT NULL,
  `gesperrt` int(1) NOT NULL,
  `sperrgrund` varchar(255) NOT NULL,
  `geloescht` int(1) NOT NULL,
  `gueltigbis` date NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `klasse` varchar(255) NOT NULL,
  `adresse` int(11) NOT NULL,
  `shopartikel` int(1) NOT NULL,
  `unishopartikel` int(1) NOT NULL,
  `journalshopartikel` int(11) NOT NULL,
  `shop` int(11) NOT NULL,
  `katalog` int(1) NOT NULL,
  `katalogtext_de` text NOT NULL,
  `katalogtext_en` text NOT NULL,
  `katalogbezeichnung_de` varchar(255) NOT NULL,
  `katalogbezeichnung_en` varchar(255) NOT NULL,
  `neu` int(1) NOT NULL,
  `topseller` int(1) NOT NULL,
  `startseite` int(1) NOT NULL,
  `wichtig` int(1) NOT NULL,
  `mindestlager` int(11) NOT NULL,
  `mindestbestellung` int(11) NOT NULL,
  `partnerprogramm_sperre` int(1) NOT NULL,
  `internerkommentar` text NOT NULL,
  `intern_gesperrt` int(11) NOT NULL,
  `intern_gesperrtuser` int(11) NOT NULL,
  `intern_gesperrtgrund` text NOT NULL,
  `inbearbeitung` int(11) NOT NULL,
  `inbearbeitunguser` int(11) NOT NULL,
  `cache_lagerplatzinhaltmenge` int(11) NOT NULL,
  `internkommentar` text NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `anabregs_text` text DEFAULT NULL,
  `autobestellung` int(1) NOT NULL DEFAULT 0,
  `produktion` int(1) DEFAULT NULL,
  `herstellernummer` varchar(255) DEFAULT NULL,
  `restmenge` int(1) DEFAULT NULL,
  `mlmdirektpraemie` decimal(10,2) DEFAULT NULL,
  `keineeinzelartikelanzeigen` tinyint(1) NOT NULL DEFAULT 0,
  `mindesthaltbarkeitsdatum` int(1) NOT NULL DEFAULT 0,
  `letzteseriennummer` varchar(255) NOT NULL,
  `individualartikel` int(1) NOT NULL DEFAULT 0,
  `keinrabatterlaubt` int(1) DEFAULT NULL,
  `rabatt` int(1) NOT NULL DEFAULT 0,
  `rabatt_prozent` decimal(10,2) DEFAULT NULL,
  `geraet` tinyint(1) NOT NULL DEFAULT 0,
  `serviceartikel` tinyint(1) NOT NULL DEFAULT 0,
  `autoabgleicherlaubt` int(1) NOT NULL DEFAULT 0,
  `pseudopreis` decimal(10,2) DEFAULT NULL,
  `freigabenotwendig` int(1) NOT NULL DEFAULT 0,
  `freigaberegel` varchar(255) NOT NULL,
  `nachbestellt` int(1) DEFAULT NULL,
  `ean` varchar(1024) NOT NULL,
  `mlmpunkte` decimal(10,2) NOT NULL,
  `mlmbonuspunkte` decimal(10,2) NOT NULL,
  `mlmkeinepunkteeigenkauf` int(1) DEFAULT NULL,
  `shop2` int(11) DEFAULT NULL,
  `shop3` int(11) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `freifeld1` text NOT NULL,
  `freifeld2` text NOT NULL,
  `freifeld3` text NOT NULL,
  `freifeld4` text NOT NULL,
  `freifeld5` text NOT NULL,
  `freifeld6` text NOT NULL,
  `einheit` varchar(255) NOT NULL,
  `webid` varchar(1024) NOT NULL,
  `lieferzeitmanuell_en` varchar(255) DEFAULT NULL,
  `variante` int(1) DEFAULT NULL,
  `variante_von` int(11) DEFAULT NULL,
  `produktioninfo` text DEFAULT NULL,
  `sonderaktion` text DEFAULT NULL,
  `sonderaktion_en` text DEFAULT NULL,
  `autolagerlampe` int(1) NOT NULL DEFAULT 0,
  `leerfeld` varchar(64) DEFAULT NULL,
  `zolltarifnummer` varchar(64) NOT NULL,
  `herkunftsland` varchar(64) NOT NULL,
  `laenge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `breite` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hoehe` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gebuehr` tinyint(1) NOT NULL DEFAULT 0,
  `pseudolager` varchar(255) NOT NULL,
  `downloadartikel` tinyint(1) NOT NULL DEFAULT 0,
  `matrixprodukt` tinyint(1) NOT NULL DEFAULT 0,
  `steuer_erloese_inland_normal` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_normal` varchar(10) NOT NULL,
  `steuer_erloese_inland_ermaessigt` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_ermaessigt` varchar(10) NOT NULL,
  `steuer_erloese_inland_steuerfrei` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_steuerfrei` varchar(10) NOT NULL,
  `steuer_erloese_inland_innergemeinschaftlich` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_innergemeinschaftlich` varchar(10) NOT NULL,
  `steuer_erloese_inland_eunormal` varchar(10) NOT NULL,
  `steuer_erloese_inland_nichtsteuerbar` varchar(10) NOT NULL,
  `steuer_erloese_inland_euermaessigt` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_nichtsteuerbar` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_eunormal` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_euermaessigt` varchar(10) NOT NULL,
  `steuer_erloese_inland_export` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_import` varchar(10) NOT NULL,
  `steuer_art_produkt` int(1) NOT NULL DEFAULT 1,
  `steuer_art_produkt_download` int(1) NOT NULL DEFAULT 1,
  `metadescription_de` text NOT NULL,
  `metadescription_en` text NOT NULL,
  `metakeywords_de` text NOT NULL,
  `metakeywords_en` text NOT NULL,
  `anabregs_text_en` text NOT NULL,
  `externeproduktion` tinyint(1) NOT NULL DEFAULT 0,
  `bildvorschau` varchar(64) NOT NULL,
  `inventursperre` int(1) NOT NULL DEFAULT 0,
  `variante_kopie` tinyint(1) NOT NULL DEFAULT 0,
  `unikat` tinyint(1) NOT NULL DEFAULT 0,
  `generierenummerbeioption` tinyint(1) NOT NULL DEFAULT 0,
  `allelieferanten` tinyint(1) NOT NULL DEFAULT 0,
  `tagespreise` tinyint(1) NOT NULL DEFAULT 0,
  `rohstoffe` tinyint(1) NOT NULL DEFAULT 0,
  `nettogewicht` varchar(64) NOT NULL,
  `xvp` decimal(14,2) NOT NULL DEFAULT 0.00,
  `ohnepreisimpdf` tinyint(1) NOT NULL DEFAULT 0,
  `provisionssperre` int(1) DEFAULT NULL,
  `dienstleistung` tinyint(1) NOT NULL DEFAULT 0,
  `inventurekaktiv` int(1) NOT NULL DEFAULT 0,
  `inventurek` decimal(18,8) DEFAULT NULL,
  `hinweis_einfuegen` text NOT NULL,
  `etikettautodruck` int(1) NOT NULL DEFAULT 0,
  `lagerkorrekturwert` int(11) NOT NULL DEFAULT 0,
  `autodrucketikett` int(11) NOT NULL DEFAULT 0,
  `abckategorie` varchar(1) NOT NULL,
  `laststorage_changed` timestamp NOT NULL DEFAULT '1970-01-01 23:00:00',
  `laststorage_sync` timestamp NOT NULL DEFAULT '1970-01-01 23:00:00',
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext_innergemeinschaftlich` varchar(1024) DEFAULT NULL,
  `steuertext_export` varchar(1024) DEFAULT NULL,
  `formelmenge` varchar(255) NOT NULL,
  `formelpreis` varchar(255) NOT NULL,
  `freifeld7` text NOT NULL,
  `freifeld8` text NOT NULL,
  `freifeld9` text NOT NULL,
  `freifeld10` text NOT NULL,
  `freifeld11` text NOT NULL,
  `freifeld12` text NOT NULL,
  `freifeld13` text NOT NULL,
  `freifeld14` text NOT NULL,
  `freifeld15` text NOT NULL,
  `freifeld16` text NOT NULL,
  `freifeld17` text NOT NULL,
  `freifeld18` text NOT NULL,
  `freifeld19` text NOT NULL,
  `freifeld20` text NOT NULL,
  `freifeld21` text NOT NULL,
  `freifeld22` text NOT NULL,
  `freifeld23` text NOT NULL,
  `freifeld24` text NOT NULL,
  `freifeld25` text NOT NULL,
  `freifeld26` text NOT NULL,
  `freifeld27` text NOT NULL,
  `freifeld28` text NOT NULL,
  `freifeld29` text NOT NULL,
  `freifeld30` text NOT NULL,
  `freifeld31` text NOT NULL,
  `freifeld32` text NOT NULL,
  `freifeld33` text NOT NULL,
  `freifeld34` text NOT NULL,
  `freifeld35` text NOT NULL,
  `freifeld36` text NOT NULL,
  `freifeld37` text NOT NULL,
  `freifeld38` text NOT NULL,
  `freifeld39` text NOT NULL,
  `freifeld40` text NOT NULL,
  `ursprungsregion` varchar(255) NOT NULL,
  `bestandalternativartikel` int(11) NOT NULL DEFAULT 0,
  `metatitle_de` text NOT NULL,
  `metatitle_en` text NOT NULL,
  `vkmeldungunterdruecken` tinyint(1) NOT NULL DEFAULT 0,
  `altersfreigabe` varchar(3) NOT NULL,
  `unikatbeikopie` tinyint(1) NOT NULL DEFAULT 0,
  `steuergruppe` int(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `artikelautokalkulation` int(11) NOT NULL DEFAULT 0,
  `artikelabschliessenkalkulation` int(11) NOT NULL DEFAULT 0,
  `artikelfifokalkulation` int(11) NOT NULL DEFAULT 0,
  `keinskonto` tinyint(1) NOT NULL DEFAULT 0,
  `berechneterek` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `verwendeberechneterek` tinyint(1) NOT NULL DEFAULT 0,
  `berechneterekwaehrung` varchar(16) NOT NULL,
  `has_preproduced_partlist` tinyint(1) DEFAULT 0,
  `preproduced_partlist` int(11) DEFAULT 0,
  `kontorahmen` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`),
  KEY `nummer` (`nummer`),
  KEY `adresse` (`adresse`),
  KEY `laststorage_changed` (`laststorage_changed`),
  KEY `laststorage_sync` (`laststorage_sync`),
  KEY `variante_von` (`variante_von`),
  KEY `herstellernummer` (`herstellernummer`),
  KEY `geloescht` (`geloescht`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_arbeitsanweisung`
--

DROP TABLE IF EXISTS `artikel_arbeitsanweisung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_arbeitsanweisung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `bild` longblob NOT NULL,
  `einzelzeit` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `arbeitsplatzgruppe` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_artikelgruppe`
--

DROP TABLE IF EXISTS `artikel_artikelgruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_artikelgruppe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL,
  `artikelgruppe` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `geloescht` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_cached_fields`
--

DROP TABLE IF EXISTS `artikel_cached_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_cached_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `project_name` varchar(64) NOT NULL,
  `number` varchar(64) NOT NULL,
  `ean` varchar(64) NOT NULL,
  `factory_number` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `manufactor` varchar(255) NOT NULL,
  `customfield1` varchar(255) NOT NULL,
  `customfield2` varchar(255) NOT NULL,
  `ek_customnumber` varchar(1024) NOT NULL,
  `vk_customnumber` varchar(1024) NOT NULL,
  `eigenschaften` varchar(1024) NOT NULL,
  `is_storage_article` tinyint(1) NOT NULL DEFAULT 0,
  `is_variant` tinyint(1) NOT NULL DEFAULT 0,
  `variant_from_id` int(11) NOT NULL DEFAULT 0,
  `variant_from_name` varchar(64) NOT NULL,
  `is_partlist` tinyint(1) NOT NULL DEFAULT 0,
  `is_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `lager_verfuegbar` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `ek_netto` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `ek_brutto` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `vk_netto` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `vk_brutto` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `inzulauf` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `imsperrlager` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `inproduktion` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `lager_gesamt` decimal(14,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_freifelder`
--

DROP TABLE IF EXISTS `artikel_freifelder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_freifelder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `sprache` varchar(255) NOT NULL,
  `nummer` int(11) NOT NULL,
  `wert` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_onlineshops`
--

DROP TABLE IF EXISTS `artikel_onlineshops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_onlineshops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `shop` int(11) NOT NULL DEFAULT 0,
  `aktiv` int(11) NOT NULL DEFAULT 0,
  `ausartikel` int(11) NOT NULL DEFAULT 1,
  `lagerkorrekturwert` int(11) NOT NULL DEFAULT 0,
  `pseudolager` varchar(255) NOT NULL,
  `autolagerlampe` tinyint(1) NOT NULL DEFAULT 0,
  `restmenge` tinyint(1) NOT NULL DEFAULT 0,
  `lieferzeitmanuell` varchar(255) NOT NULL,
  `pseudopreis` decimal(10,2) NOT NULL DEFAULT 0.00,
  `generierenummerbeioption` tinyint(1) NOT NULL DEFAULT 0,
  `variante_kopie` tinyint(1) NOT NULL DEFAULT 0,
  `unikat` tinyint(1) NOT NULL DEFAULT 0,
  `unikatbeikopie` tinyint(1) NOT NULL DEFAULT 0,
  `autoabgeleicherlaubt` tinyint(1) NOT NULL DEFAULT 0,
  `last_article_hash` varchar(64) NOT NULL,
  `last_article_transfer` timestamp NULL DEFAULT NULL,
  `last_storage_transfer` timestamp NULL DEFAULT NULL,
  `storage_cache` int(11) DEFAULT NULL,
  `pseudostorage_cache` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`,`shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_permanenteinventur`
--

DROP TABLE IF EXISTS `artikel_permanenteinventur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_permanenteinventur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `zeitstempel` datetime DEFAULT NULL,
  `bearbeiter` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lager_platz` (`lager_platz`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_shop`
--

DROP TABLE IF EXISTS `artikel_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL,
  `shop` int(11) NOT NULL,
  `checksum` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_texte`
--

DROP TABLE IF EXISTS `artikel_texte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_texte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `sprache` varchar(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `kurztext` text NOT NULL,
  `beschreibung` text NOT NULL,
  `beschreibung_online` text NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `katalogartikel` tinyint(1) NOT NULL DEFAULT 0,
  `katalog_bezeichnung` text NOT NULL,
  `katalog_text` text NOT NULL,
  `shop` int(11) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `shop` (`shop`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_zu_optionen`
--

DROP TABLE IF EXISTS `artikel_zu_optionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_zu_optionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikeloption` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikel_zu_optionengruppe`
--

DROP TABLE IF EXISTS `artikel_zu_optionengruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikel_zu_optionengruppe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikeloptionengruppe` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_de` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `preisadd` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `preisart` varchar(20) NOT NULL DEFAULT 'absolut',
  `bearbeiter` varchar(255) NOT NULL,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikelbaum_artikel`
--

DROP TABLE IF EXISTS `artikelbaum_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikelbaum_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `kategorie` int(11) NOT NULL DEFAULT 0,
  `haupt` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`,`kategorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikeleigenschaften`
--

DROP TABLE IF EXISTS `artikeleigenschaften`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikeleigenschaften` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `name` varchar(64) DEFAULT NULL,
  `typ` varchar(64) DEFAULT 'einzeilig',
  `projekt` int(11) NOT NULL DEFAULT 0,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikeleigenschaftenwerte`
--

DROP TABLE IF EXISTS `artikeleigenschaftenwerte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikeleigenschaftenwerte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikeleigenschaften` int(11) NOT NULL DEFAULT 0,
  `vorlage` int(11) NOT NULL DEFAULT 0,
  `name` varchar(64) DEFAULT NULL,
  `einheit` varchar(64) DEFAULT NULL,
  `wert` text DEFAULT NULL,
  `artikel` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `artikeleigenschaften` (`artikeleigenschaften`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikeleinheit`
--

DROP TABLE IF EXISTS `artikeleinheit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikeleinheit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `einheit_de` varchar(255) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikelgruppen`
--

DROP TABLE IF EXISTS `artikelgruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikelgruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `bezeichnung_en` varchar(255) NOT NULL,
  `shop` int(11) NOT NULL,
  `aktiv` int(1) NOT NULL,
  `beschreibung_de` text DEFAULT NULL,
  `beschreibung_en` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikelkalkulation`
--

DROP TABLE IF EXISTS `artikelkalkulation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikelkalkulation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `bestellung` int(11) NOT NULL DEFAULT 0,
  `datum` date NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `kostenart` varchar(255) NOT NULL,
  `kosten` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `menge` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(255) NOT NULL,
  `gesamtkosten` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `kommentar` varchar(255) NOT NULL,
  `waehrung` varchar(16) NOT NULL,
  `gesperrt` tinyint(1) NOT NULL,
  `automatischneuberechnen` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikelkalkulation_menge`
--

DROP TABLE IF EXISTS `artikelkalkulation_menge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikelkalkulation_menge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikelkalkulation_tag`
--

DROP TABLE IF EXISTS `artikelkalkulation_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikelkalkulation_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `datum` date NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `preis` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `waehrung` varchar(16) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `json` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikelkategorien`
--

DROP TABLE IF EXISTS `artikelkategorien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikelkategorien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(64) NOT NULL,
  `next_nummer` varchar(128) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `externenummer` tinyint(1) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `steuer_erloese_inland_normal` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_normal` varchar(10) NOT NULL,
  `steuer_erloese_inland_ermaessigt` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_ermaessigt` varchar(10) NOT NULL,
  `steuer_erloese_inland_steuerfrei` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_steuerfrei` varchar(10) NOT NULL,
  `steuer_erloese_inland_innergemeinschaftlich` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_innergemeinschaftlich` varchar(10) NOT NULL,
  `steuer_erloese_inland_eunormal` varchar(10) NOT NULL,
  `steuer_erloese_inland_nichtsteuerbar` varchar(10) NOT NULL,
  `steuer_erloese_inland_euermaessigt` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_nichtsteuerbar` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_eunormal` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_euermaessigt` varchar(10) NOT NULL,
  `steuer_erloese_inland_export` varchar(10) NOT NULL,
  `steuer_aufwendung_inland_import` varchar(10) NOT NULL,
  `steuertext_innergemeinschaftlich` varchar(1024) DEFAULT NULL,
  `steuertext_export` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikelkontingente`
--

DROP TABLE IF EXISTS `artikelkontingente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikelkontingente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `datum` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikelnummer_fremdnummern`
--

DROP TABLE IF EXISTS `artikelnummer_fremdnummern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikelnummer_fremdnummern` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `bezeichnung` varchar(255) DEFAULT NULL,
  `nummer` varchar(255) DEFAULT NULL,
  `shopid` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(255) DEFAULT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `scannable` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`),
  KEY `shopid` (`shopid`),
  KEY `nummer` (`nummer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikeloptionen`
--

DROP TABLE IF EXISTS `artikeloptionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikeloptionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gruppe` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_de` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `preisadd` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `preisart` varchar(20) NOT NULL DEFAULT 'absolut',
  `bearbeiter` varchar(255) NOT NULL,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artikeloptionengruppe`
--

DROP TABLE IF EXISTS `artikeloptionengruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artikeloptionengruppe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_de` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `standardoption` tinyint(1) DEFAULT NULL,
  `artikel` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aufgabe`
--

DROP TABLE IF EXISTS `aufgabe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aufgabe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `aufgabe` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `prio` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `kostenstelle` int(11) NOT NULL,
  `initiator` int(11) NOT NULL,
  `angelegt_am` date NOT NULL,
  `startdatum` date NOT NULL,
  `startzeit` time NOT NULL,
  `intervall_tage` int(11) NOT NULL,
  `stunden` decimal(10,2) DEFAULT NULL,
  `abgabe_bis` date NOT NULL,
  `abgeschlossen` int(1) NOT NULL,
  `abgeschlossen_am` date NOT NULL,
  `sonstiges` text NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `startseite` int(1) DEFAULT NULL,
  `oeffentlich` int(1) DEFAULT NULL,
  `emailerinnerung` int(1) DEFAULT NULL,
  `emailerinnerung_tage` int(11) DEFAULT NULL,
  `note_x` int(11) DEFAULT NULL,
  `note_y` int(11) DEFAULT NULL,
  `note_z` int(11) DEFAULT NULL,
  `note_color` varchar(255) DEFAULT NULL,
  `pinwand` int(1) DEFAULT NULL,
  `vorankuendigung` int(11) DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `ganztags` int(1) NOT NULL DEFAULT 1,
  `zeiterfassung_pflicht` tinyint(1) NOT NULL DEFAULT 0,
  `zeiterfassung_abrechnung` tinyint(1) NOT NULL DEFAULT 0,
  `kunde` int(11) DEFAULT NULL,
  `pinwand_id` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) DEFAULT NULL,
  `abgabe_bis_zeit` time DEFAULT NULL,
  `email_gesendet_vorankuendigung` tinyint(1) NOT NULL DEFAULT 0,
  `email_gesendet` tinyint(1) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `note_w` int(11) DEFAULT NULL,
  `note_h` int(11) DEFAULT NULL,
  `ansprechpartner_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aufgabe_erledigt`
--

DROP TABLE IF EXISTS `aufgabe_erledigt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aufgabe_erledigt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `aufgabe` int(11) NOT NULL,
  `abgeschlossen_am` date NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auftrag`
--

DROP TABLE IF EXISTS `auftrag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auftrag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `art` varchar(255) NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `internet` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `angebot` varchar(255) NOT NULL,
  `freitext` text NOT NULL,
  `internebemerkung` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `adresse` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `ust_befreit` int(1) NOT NULL,
  `ust_inner` int(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(64) DEFAULT NULL,
  `versandart` varchar(255) NOT NULL,
  `vertrieb` varchar(255) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungszieltage` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `zahlungszielskonto` decimal(10,2) NOT NULL,
  `bank_inhaber` varchar(255) NOT NULL,
  `bank_institut` varchar(255) NOT NULL,
  `bank_blz` varchar(255) NOT NULL,
  `bank_konto` varchar(255) NOT NULL,
  `kreditkarte_typ` varchar(255) NOT NULL,
  `kreditkarte_inhaber` varchar(255) NOT NULL,
  `kreditkarte_nummer` varchar(255) NOT NULL,
  `kreditkarte_pruefnummer` varchar(255) NOT NULL,
  `kreditkarte_monat` varchar(255) NOT NULL,
  `kreditkarte_jahr` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `autoversand` int(1) NOT NULL,
  `keinporto` int(1) NOT NULL,
  `keinestornomail` int(1) NOT NULL,
  `abweichendelieferadresse` int(1) NOT NULL,
  `liefername` varchar(255) NOT NULL,
  `lieferabteilung` varchar(255) NOT NULL,
  `lieferunterabteilung` varchar(255) NOT NULL,
  `lieferland` varchar(255) NOT NULL,
  `lieferstrasse` varchar(255) NOT NULL,
  `lieferort` varchar(255) NOT NULL,
  `lieferplz` varchar(255) NOT NULL,
  `lieferadresszusatz` varchar(255) NOT NULL,
  `lieferansprechpartner` varchar(255) NOT NULL,
  `packstation_inhaber` varchar(255) NOT NULL,
  `packstation_station` varchar(255) NOT NULL,
  `packstation_ident` varchar(255) NOT NULL,
  `packstation_plz` varchar(255) NOT NULL,
  `packstation_ort` varchar(255) NOT NULL,
  `autofreigabe` int(1) NOT NULL,
  `freigabe` int(1) NOT NULL,
  `nachbesserung` int(1) NOT NULL,
  `gesamtsumme` decimal(18,2) NOT NULL DEFAULT 0.00,
  `inbearbeitung` int(1) NOT NULL,
  `abgeschlossen` int(1) NOT NULL,
  `nachlieferung` int(1) NOT NULL,
  `lager_ok` int(1) NOT NULL,
  `porto_ok` int(1) NOT NULL,
  `ust_ok` int(1) NOT NULL,
  `check_ok` int(1) NOT NULL,
  `vorkasse_ok` int(1) NOT NULL,
  `nachnahme_ok` int(1) NOT NULL,
  `reserviert_ok` int(1) NOT NULL,
  `partnerid` int(11) NOT NULL,
  `folgebestaetigung` date NOT NULL,
  `zahlungsmail` date NOT NULL,
  `stornogrund` varchar(255) NOT NULL,
  `stornosonstiges` varchar(255) NOT NULL,
  `stornorueckzahlung` varchar(255) NOT NULL,
  `stornobetrag` decimal(18,2) NOT NULL DEFAULT 0.00,
  `stornobankinhaber` varchar(255) NOT NULL,
  `stornobankkonto` varchar(255) NOT NULL,
  `stornobankblz` varchar(255) NOT NULL,
  `stornobankbank` varchar(255) NOT NULL,
  `stornogutschrift` int(1) NOT NULL,
  `stornogutschriftbeleg` varchar(255) NOT NULL,
  `stornowareerhalten` int(1) NOT NULL,
  `stornomanuellebearbeitung` varchar(255) NOT NULL,
  `stornokommentar` text NOT NULL,
  `stornobezahlt` varchar(255) NOT NULL,
  `stornobezahltam` date NOT NULL,
  `stornobezahltvon` varchar(255) NOT NULL,
  `stornoabgeschlossen` int(1) NOT NULL,
  `stornorueckzahlungper` varchar(255) NOT NULL,
  `stornowareerhaltenretour` int(1) NOT NULL,
  `partnerausgezahlt` int(1) NOT NULL,
  `partnerausgezahltam` date NOT NULL,
  `kennen` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `keinetrackingmail` int(1) DEFAULT NULL,
  `zahlungsmailcounter` int(1) DEFAULT NULL,
  `rma` int(1) NOT NULL DEFAULT 0,
  `transaktionsnummer` varchar(255) NOT NULL,
  `vorabbezahltmarkieren` int(1) NOT NULL DEFAULT 0,
  `deckungsbeitragcalc` tinyint(1) NOT NULL DEFAULT 0,
  `deckungsbeitrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `erloes_netto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `umsatz_netto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `lieferdatum` date DEFAULT NULL,
  `tatsaechlicheslieferdatum` date DEFAULT NULL,
  `liefertermin_ok` int(1) NOT NULL DEFAULT 1,
  `teillieferung_moeglich` int(1) NOT NULL DEFAULT 0,
  `kreditlimit_ok` int(1) NOT NULL DEFAULT 1,
  `kreditlimit_freigabe` int(1) NOT NULL DEFAULT 0,
  `liefersperre_ok` int(1) NOT NULL DEFAULT 1,
  `teillieferungvon` int(11) NOT NULL DEFAULT 0,
  `teillieferungnummer` int(11) NOT NULL DEFAULT 0,
  `vertriebid` int(11) DEFAULT NULL,
  `aktion` varchar(64) NOT NULL,
  `provision` decimal(10,2) DEFAULT NULL,
  `provision_summe` decimal(10,2) DEFAULT NULL,
  `anfrageid` int(11) NOT NULL DEFAULT 0,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `shopextid` varchar(1024) NOT NULL,
  `shopextstatus` varchar(1024) NOT NULL,
  `ihrebestellnummer` varchar(255) DEFAULT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `realrabatt` decimal(10,2) DEFAULT NULL,
  `rabatt` decimal(10,2) DEFAULT NULL,
  `einzugsdatum` date DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `shop` int(11) NOT NULL DEFAULT 0,
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(255) NOT NULL DEFAULT 'EUR',
  `keinsteuersatz` int(1) DEFAULT NULL,
  `angebotid` int(11) DEFAULT NULL,
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviert` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviertversion` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(255) NOT NULL DEFAULT 'firma',
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `auftragseingangper` varchar(64) NOT NULL,
  `lieferid` int(11) NOT NULL DEFAULT 0,
  `ansprechpartnerid` int(11) NOT NULL DEFAULT 0,
  `systemfreitext` text NOT NULL,
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `lieferungtrotzsperre` int(1) NOT NULL DEFAULT 0,
  `zuarchivieren` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `angelegtam` datetime DEFAULT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldogeprueft` datetime DEFAULT NULL,
  `lieferantenauftrag` tinyint(1) NOT NULL DEFAULT 0,
  `lieferant` int(11) NOT NULL DEFAULT 0,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  `abweichendebezeichnung` tinyint(1) NOT NULL DEFAULT 0,
  `rabatteportofestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `sprache` varchar(32) NOT NULL,
  `bundesland` varchar(64) NOT NULL,
  `gln` varchar(64) NOT NULL,
  `liefergln` varchar(64) NOT NULL,
  `lieferemail` varchar(200) NOT NULL,
  `reservationdate` date DEFAULT NULL,
  `rechnungid` int(11) NOT NULL DEFAULT 0,
  `deliverythresholdvatid` varchar(64) NOT NULL,
  `fastlane` tinyint(1) DEFAULT 0,
  `bearbeiterid` int(11) DEFAULT NULL,
  `kurs` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `lieferantennummer` varchar(255) NOT NULL,
  `lieferantkdrnummer` varchar(255) NOT NULL,
  `ohne_artikeltext` int(1) DEFAULT NULL,
  `webid` int(11) DEFAULT NULL,
  `anzeigesteuer` tinyint(11) NOT NULL DEFAULT 0,
  `cronjobkommissionierung` int(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `bodyzusatz` text NOT NULL,
  `lieferbedingung` text NOT NULL,
  `titel` varchar(64) NOT NULL,
  `liefertitel` varchar(64) NOT NULL,
  `standardlager` int(11) NOT NULL DEFAULT 0,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontoberechnet` tinyint(1) NOT NULL DEFAULT 0,
  `kommissionskonsignationslager` int(11) NOT NULL DEFAULT 0,
  `extsoll` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bundesstaat` varchar(32) NOT NULL,
  `lieferbundesstaat` varchar(32) NOT NULL,
  `kundennummer_buchhaltung` varchar(32) NOT NULL,
  `storage_country` varchar(3) NOT NULL,
  `shop_status_update_attempt` int(3) NOT NULL DEFAULT 0,
  `shop_status_update_last_attempt_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`),
  KEY `adresse` (`adresse`),
  KEY `vertriebid` (`vertriebid`),
  KEY `status` (`status`),
  KEY `datum` (`datum`),
  KEY `belegnr` (`belegnr`),
  KEY `gesamtsumme` (`gesamtsumme`),
  KEY `transaktionsnummer` (`transaktionsnummer`),
  KEY `internet` (`internet`),
  KEY `lieferantkdrnummer` (`lieferantkdrnummer`),
  KEY `teillieferungvon` (`teillieferungvon`),
  KEY `versandart` (`versandart`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auftrag_position`
--

DROP TABLE IF EXISTS `auftrag_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auftrag_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `auftrag` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `waehrung` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `status` varchar(64) NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `geliefert` int(11) NOT NULL,
  `geliefert_menge` decimal(14,4) NOT NULL,
  `explodiert` int(1) NOT NULL,
  `explodiert_parent` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `punkte` decimal(10,2) NOT NULL,
  `bonuspunkte` decimal(10,2) NOT NULL,
  `mlmdirektpraemie` decimal(10,2) DEFAULT NULL,
  `keinrabatterlaubt` int(1) DEFAULT NULL,
  `grundrabatt` decimal(10,2) DEFAULT NULL,
  `rabattsync` int(1) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `einheit` varchar(255) NOT NULL,
  `webid` varchar(1024) DEFAULT NULL,
  `rabatt` decimal(10,2) NOT NULL,
  `nachbestelltexternereinkauf` int(1) DEFAULT NULL,
  `zolltarifnummer` varchar(128) NOT NULL DEFAULT '0',
  `herkunftsland` varchar(128) NOT NULL DEFAULT '0',
  `artikelnummerkunde` varchar(128) NOT NULL,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(1024) DEFAULT NULL,
  `erloese` varchar(8) DEFAULT NULL,
  `erloesefestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `einkaufspreiswaehrung` varchar(8) NOT NULL,
  `einkaufspreis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `einkaufspreisurspruenglich` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `einkaufspreisid` int(11) NOT NULL DEFAULT 0,
  `ekwaehrung` varchar(8) NOT NULL,
  `deckungsbeitrag` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `formelmenge` varchar(255) NOT NULL,
  `formelpreis` varchar(255) NOT NULL,
  `ohnepreis` int(1) NOT NULL DEFAULT 0,
  `zolleinzelwert` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `zollgesamtwert` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `zollwaehrung` varchar(3) NOT NULL,
  `zolleinzelgewicht` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `zollgesamtgewicht` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `potentiellerliefertermin` date DEFAULT NULL,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_gesamt` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_gesamt` decimal(14,4) DEFAULT NULL,
  `steuerbetrag` decimal(14,4) DEFAULT NULL,
  `skontosperre` tinyint(1) NOT NULL DEFAULT 0,
  `ausblenden_im_pdf` tinyint(1) DEFAULT 0,
  `umsatz_netto_einzeln` decimal(14,4) DEFAULT NULL,
  `umsatz_netto_gesamt` decimal(14,4) DEFAULT NULL,
  `umsatz_brutto_einzeln` decimal(14,4) DEFAULT NULL,
  `umsatz_brutto_gesamt` decimal(14,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auftrag` (`auftrag`,`artikel`),
  KEY `artikel` (`artikel`),
  KEY `auftrag_2` (`auftrag`),
  KEY `explodiert_parent` (`explodiert_parent`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auftrag_protokoll`
--

DROP TABLE IF EXISTS `auftrag_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auftrag_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auftrag` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auftrag` (`auftrag`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autoresponder_blacklist`
--

DROP TABLE IF EXISTS `autoresponder_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `autoresponder_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cachetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mailaddress` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `backup`
--

DROP TABLE IF EXISTS `backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `dateiname` varchar(255) DEFAULT NULL,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `beleg_chargesnmhd`
--

DROP TABLE IF EXISTS `beleg_chargesnmhd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beleg_chargesnmhd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctype` varchar(255) NOT NULL,
  `doctypeid` int(11) NOT NULL DEFAULT 0,
  `pos` int(11) NOT NULL DEFAULT 0,
  `type` varchar(10) NOT NULL,
  `type2` varchar(10) NOT NULL,
  `type3` varchar(10) NOT NULL,
  `wert` varchar(255) NOT NULL,
  `wert2` varchar(255) NOT NULL,
  `wert3` varchar(255) NOT NULL,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `lagerplatz` int(11) NOT NULL DEFAULT 0,
  `internebemerkung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `doctypeid` (`doctypeid`),
  KEY `pos` (`pos`),
  KEY `type` (`type`),
  KEY `type2` (`type2`),
  KEY `wert` (`wert`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `beleg_zwischenpositionen`
--

DROP TABLE IF EXISTS `beleg_zwischenpositionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beleg_zwischenpositionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctype` varchar(255) NOT NULL,
  `doctypeid` int(11) NOT NULL,
  `pos` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `postype` varchar(64) NOT NULL,
  `wert` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `doctypeid` (`doctypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `belege`
--

DROP TABLE IF EXISTS `belege`;
/*!50001 DROP VIEW IF EXISTS `belege`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `belege` AS SELECT
 1 AS `id`,
  1 AS `adresse`,
  1 AS `datum`,
  1 AS `belegnr`,
  1 AS `status`,
  1 AS `land`,
  1 AS `typ`,
  1 AS `umsatz_netto`,
  1 AS `erloes_netto`,
  1 AS `deckungsbeitrag`,
  1 AS `provision_summe`,
  1 AS `vertriebid`,
  1 AS `gruppe` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `belegegesamt`
--

DROP TABLE IF EXISTS `belegegesamt`;
/*!50001 DROP VIEW IF EXISTS `belegegesamt`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `belegegesamt` AS SELECT
 1 AS `id`,
  1 AS `adresse`,
  1 AS `datum`,
  1 AS `belegnr`,
  1 AS `status`,
  1 AS `land`,
  1 AS `typ`,
  1 AS `umsatz_netto`,
  1 AS `umsatz_brutto`,
  1 AS `erloes_netto`,
  1 AS `deckungsbeitrag`,
  1 AS `provision_summe`,
  1 AS `vertriebid`,
  1 AS `gruppe`,
  1 AS `projekt` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `belegeimport`
--

DROP TABLE IF EXISTS `belegeimport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `belegeimport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `art` varchar(20) NOT NULL,
  `status` varchar(24) NOT NULL,
  `beleg_status` varchar(24) NOT NULL,
  `beleg_datum` varchar(24) NOT NULL,
  `beleg_lieferdatum` varchar(24) NOT NULL,
  `beleg_tatsaechlicheslieferdatum` varchar(24) NOT NULL,
  `beleg_versandart` varchar(24) NOT NULL,
  `beleg_zahlungsweise` varchar(32) NOT NULL,
  `beleg_belegnr` varchar(20) NOT NULL,
  `beleg_hauptbelegnr` varchar(20) NOT NULL,
  `beleg_kundennummer` varchar(64) NOT NULL,
  `beleg_lieferantennummer` varchar(64) NOT NULL,
  `beleg_name` varchar(64) NOT NULL,
  `beleg_abteilung` varchar(255) NOT NULL,
  `beleg_unterabteilung` varchar(255) NOT NULL,
  `beleg_adresszusatz` varchar(255) NOT NULL,
  `beleg_ansprechpartner` varchar(255) NOT NULL,
  `beleg_telefon` varchar(255) NOT NULL,
  `beleg_email` varchar(255) NOT NULL,
  `beleg_land` varchar(2) NOT NULL,
  `beleg_strasse` varchar(255) NOT NULL,
  `beleg_plz` varchar(64) NOT NULL,
  `beleg_ort` varchar(255) NOT NULL,
  `beleg_projekt` int(11) NOT NULL DEFAULT 0,
  `beleg_aktion` varchar(255) NOT NULL,
  `beleg_internebemerkung` text NOT NULL,
  `beleg_internebezeichnung` text NOT NULL,
  `beleg_freitext` text NOT NULL,
  `beleg_ihrebestellnummer` varchar(255) NOT NULL,
  `beleg_lieferbedingung` varchar(255) NOT NULL,
  `beleg_art` varchar(32) NOT NULL,
  `beleg_auftragid` int(11) NOT NULL DEFAULT 0,
  `artikel_nummer` varchar(255) NOT NULL,
  `artikel_ean` varchar(255) NOT NULL,
  `artikel_bezeichnung` varchar(255) NOT NULL,
  `artikel_beschreibung` text NOT NULL,
  `artikel_menge` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `artikel_preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `artikel_preisfuermenge` decimal(18,8) NOT NULL DEFAULT 1.00000000,
  `artikel_rabatt` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `artikel_waehrung` varchar(3) NOT NULL,
  `artikel_lieferdatum` date DEFAULT NULL,
  `artikel_sort` int(11) NOT NULL DEFAULT 1,
  `artikel_umsatzsteuer` varchar(255) NOT NULL,
  `artikel_einheit` varchar(255) NOT NULL,
  `artikel_zolltarifnummer` varchar(255) NOT NULL,
  `artikel_herkunftsland` varchar(255) NOT NULL,
  `artikel_artikelnummerkunde` varchar(255) NOT NULL,
  `artikel_freifeld1` varchar(255) NOT NULL,
  `artikel_freifeld2` varchar(255) NOT NULL,
  `artikel_freifeld3` varchar(255) NOT NULL,
  `artikel_freifeld4` varchar(255) NOT NULL,
  `artikel_freifeld5` varchar(255) NOT NULL,
  `artikel_freifeld6` varchar(255) NOT NULL,
  `artikel_freifeld7` varchar(255) NOT NULL,
  `artikel_freifeld8` varchar(255) NOT NULL,
  `artikel_freifeld9` varchar(255) NOT NULL,
  `artikel_freifeld10` varchar(255) NOT NULL,
  `artikel_freifeld11` varchar(255) NOT NULL,
  `artikel_freifeld12` varchar(255) NOT NULL,
  `artikel_freifeld13` varchar(255) NOT NULL,
  `artikel_freifeld14` varchar(255) NOT NULL,
  `artikel_freifeld15` varchar(255) NOT NULL,
  `artikel_freifeld16` varchar(255) NOT NULL,
  `artikel_freifeld17` varchar(255) NOT NULL,
  `artikel_freifeld18` varchar(255) NOT NULL,
  `artikel_freifeld19` varchar(255) NOT NULL,
  `artikel_freifeld20` varchar(255) NOT NULL,
  `beleg_unterlistenexplodieren` int(11) NOT NULL DEFAULT 0,
  `artikel_steuersatz` int(11) DEFAULT -1,
  `adresse_typ` varchar(255) NOT NULL,
  `adresse_ustid` varchar(255) NOT NULL,
  `adresse_anschreiben` varchar(255) NOT NULL,
  `adresscounter` int(11) DEFAULT 0,
  `adresse_freifeld1` varchar(255) NOT NULL,
  `adresse_freifeld2` varchar(255) NOT NULL,
  `adresse_freifeld3` varchar(255) NOT NULL,
  `adresse_freifeld4` varchar(255) NOT NULL,
  `adresse_freifeld5` varchar(255) NOT NULL,
  `adresse_freifeld6` varchar(255) NOT NULL,
  `adresse_freifeld7` varchar(255) NOT NULL,
  `adresse_freifeld8` varchar(255) NOT NULL,
  `adresse_freifeld9` varchar(255) NOT NULL,
  `adresse_freifeld10` varchar(255) NOT NULL,
  `adresse_freifeld11` varchar(255) NOT NULL,
  `adresse_freifeld12` varchar(255) NOT NULL,
  `adresse_freifeld13` varchar(255) NOT NULL,
  `adresse_freifeld14` varchar(255) NOT NULL,
  `adresse_freifeld15` varchar(255) NOT NULL,
  `adresse_freifeld16` varchar(255) NOT NULL,
  `adresse_freifeld17` varchar(255) NOT NULL,
  `adresse_freifeld18` varchar(255) NOT NULL,
  `adresse_freifeld19` varchar(255) NOT NULL,
  `adresse_freifeld20` varchar(255) NOT NULL,
  `beleg_sprache` varchar(20) NOT NULL,
  `beleg_auftragsnummer` varchar(20) NOT NULL,
  `beleg_rechnungsnumer` varchar(20) NOT NULL,
  `beleg_liefername` varchar(64) NOT NULL,
  `beleg_lieferabteilung` varchar(255) NOT NULL,
  `beleg_lieferunterabteilung` varchar(255) NOT NULL,
  `beleg_lieferland` varchar(2) NOT NULL,
  `beleg_lieferstrasse` varchar(255) NOT NULL,
  `beleg_lieferort` varchar(255) NOT NULL,
  `beleg_lieferplz` varchar(64) NOT NULL,
  `beleg_lieferadresszusatz` varchar(255) NOT NULL,
  `beleg_lieferansprechpartner` varchar(255) NOT NULL,
  `beleg_abschlagauftrag` varchar(20) NOT NULL,
  `beleg_abschlagauftragbezeichnung` varchar(255) NOT NULL,
  `beleg_zahlungszieltage` int(11) NOT NULL DEFAULT 0,
  `beleg_zahlungszieltageskonto` int(11) NOT NULL DEFAULT 0,
  `beleg_zahlungszielskonto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `beleg_bodyzusatz` text NOT NULL,
  `beleg_bearbeiter` varchar(255) NOT NULL,
  `beleg_waehrung` varchar(20) NOT NULL,
  `beleg_bundesstaat` varchar(20) NOT NULL,
  `beleg_internet` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `belegeimport_running`
--

DROP TABLE IF EXISTS `belegeimport_running`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `belegeimport_running` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT 0,
  `art` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `filename` varchar(256) NOT NULL,
  `command` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `belegeregs`
--

DROP TABLE IF EXISTS `belegeregs`;
/*!50001 DROP VIEW IF EXISTS `belegeregs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `belegeregs` AS SELECT
 1 AS `id`,
  1 AS `adresse`,
  1 AS `datum`,
  1 AS `belegnr`,
  1 AS `status`,
  1 AS `land`,
  1 AS `typ`,
  1 AS `umsatz_netto`,
  1 AS `erloes_netto`,
  1 AS `deckungsbeitrag`,
  1 AS `provision_summe`,
  1 AS `vertriebid`,
  1 AS `gruppe`,
  1 AS `projekt` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `belegevorlagen`
--

DROP TABLE IF EXISTS `belegevorlagen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `belegevorlagen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `belegtyp` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `json` mediumtext NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `berichte`
--

DROP TABLE IF EXISTS `berichte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `berichte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  `struktur` text DEFAULT NULL,
  `spaltennamen` varchar(1024) DEFAULT NULL,
  `spaltenbreite` varchar(1024) DEFAULT NULL,
  `spaltenausrichtung` varchar(1024) DEFAULT NULL,
  `variablen` text DEFAULT NULL,
  `sumcols` varchar(1024) DEFAULT NULL,
  `doctype` varchar(64) DEFAULT NULL,
  `doctype_actionmenu` int(1) NOT NULL DEFAULT 0,
  `doctype_actionmenuname` varchar(256) NOT NULL,
  `doctype_actionmenufiletype` varchar(256) NOT NULL DEFAULT 'csv',
  `project` int(11) NOT NULL DEFAULT 0,
  `ftpuebertragung` int(1) NOT NULL DEFAULT 0,
  `ftppassivemode` int(1) NOT NULL DEFAULT 0,
  `ftphost` varchar(512) DEFAULT NULL,
  `ftpport` int(11) DEFAULT NULL,
  `ftpuser` varchar(512) DEFAULT NULL,
  `ftppassword` varchar(512) DEFAULT NULL,
  `ftpuhrzeit` time DEFAULT NULL,
  `ftpletzteuebertragung` datetime DEFAULT NULL,
  `ftpnamealternativ` varchar(512) DEFAULT NULL,
  `emailuebertragung` int(1) NOT NULL DEFAULT 0,
  `emailempfaenger` varchar(512) DEFAULT NULL,
  `emailbetreff` varchar(512) DEFAULT NULL,
  `emailuhrzeit` time DEFAULT NULL,
  `emailletzteuebertragung` datetime DEFAULT NULL,
  `emailnamealternativ` varchar(512) DEFAULT NULL,
  `typ` varchar(16) NOT NULL DEFAULT 'ftp',
  PRIMARY KEY (`id`),
  KEY `doctype` (`doctype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bestbeforebatchtoposition`
--

DROP TABLE IF EXISTS `bestbeforebatchtoposition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bestbeforebatchtoposition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctype` varchar(64) NOT NULL,
  `doctype_id` int(11) NOT NULL DEFAULT 0,
  `position_id` int(11) NOT NULL DEFAULT 0,
  `bestbeforedatebatch` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `doctype` (`doctype`,`doctype_id`,`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bestellung`
--

DROP TABLE IF EXISTS `bestellung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bestellung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `bestellungsart` varchar(255) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `angebot` varchar(255) NOT NULL,
  `freitext` text NOT NULL,
  `internebemerkung` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `adresse` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `abweichendelieferadresse` int(1) NOT NULL,
  `liefername` varchar(255) NOT NULL,
  `lieferabteilung` varchar(255) NOT NULL,
  `lieferunterabteilung` varchar(255) NOT NULL,
  `lieferland` varchar(255) NOT NULL,
  `lieferstrasse` varchar(255) NOT NULL,
  `lieferort` varchar(255) NOT NULL,
  `lieferplz` varchar(255) NOT NULL,
  `lieferadresszusatz` varchar(255) NOT NULL,
  `lieferansprechpartner` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `ust_befreit` int(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(255) NOT NULL,
  `lieferantennummer` varchar(255) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `einkaeufer` varchar(255) NOT NULL,
  `keineartikelnummern` int(1) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungsstatus` varchar(255) NOT NULL,
  `zahlungszieltage` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `zahlungszielskonto` decimal(10,2) NOT NULL,
  `gesamtsumme` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `bank_inhaber` varchar(255) NOT NULL,
  `bank_institut` varchar(255) NOT NULL,
  `bank_blz` int(11) NOT NULL,
  `bank_konto` int(11) NOT NULL,
  `paypalaccount` varchar(255) NOT NULL,
  `bestellbestaetigung` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `artikelnummerninfotext` int(1) DEFAULT NULL,
  `ansprechpartner` varchar(255) DEFAULT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(255) NOT NULL DEFAULT 'EUR',
  `bestellungohnepreis` tinyint(1) NOT NULL DEFAULT 0,
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviert` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviertversion` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(255) NOT NULL DEFAULT 'firma',
  `verbindlichkeiteninfo` varchar(255) NOT NULL,
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `bestellung_bestaetigt` tinyint(1) NOT NULL DEFAULT 0,
  `bestaetigteslieferdatum` date DEFAULT NULL,
  `bestellungbestaetigtper` varchar(64) NOT NULL,
  `bestellungbestaetigtabnummer` varchar(64) NOT NULL,
  `gewuenschteslieferdatum` date DEFAULT NULL,
  `zuarchivieren` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `angelegtam` datetime DEFAULT NULL,
  `preisanfrageid` int(11) NOT NULL DEFAULT 0,
  `sprache` varchar(32) NOT NULL,
  `kundennummerlieferant` varchar(64) NOT NULL,
  `ohne_artikeltext` int(1) DEFAULT NULL,
  `langeartikelnummern` tinyint(1) NOT NULL DEFAULT 0,
  `abweichendebezeichnung` tinyint(1) NOT NULL DEFAULT 0,
  `anzeigesteuer` tinyint(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `bodyzusatz` text NOT NULL,
  `lieferbedingung` text NOT NULL,
  `titel` varchar(64) NOT NULL,
  `liefertitel` varchar(64) NOT NULL,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontoberechnet` tinyint(1) NOT NULL DEFAULT 0,
  `bundesstaat` varchar(32) NOT NULL,
  `lieferbundesstaat` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`),
  KEY `adresse` (`adresse`),
  KEY `status` (`status`),
  KEY `datum` (`datum`),
  KEY `belegnr` (`belegnr`),
  KEY `versandart` (`versandart`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bestellung_position`
--

DROP TABLE IF EXISTS `bestellung_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bestellung_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `bestellung` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bezeichnunglieferant` varchar(255) NOT NULL,
  `bestellnummer` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `waehrung` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `status` varchar(64) NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `geliefert` decimal(14,4) NOT NULL,
  `mengemanuellgeliefertaktiviert` int(11) NOT NULL,
  `manuellgeliefertbearbeiter` varchar(255) NOT NULL,
  `abgerechnet` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `abgeschlossen` int(1) DEFAULT NULL,
  `einheit` varchar(255) NOT NULL,
  `zolltarifnummer` varchar(128) NOT NULL DEFAULT '0',
  `herkunftsland` varchar(128) NOT NULL DEFAULT '0',
  `artikelnummerkunde` varchar(128) NOT NULL,
  `auftrag_position_id` int(11) NOT NULL DEFAULT 0,
  `preisanfrage_position_id` int(11) NOT NULL DEFAULT 0,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `auswahlmenge` decimal(14,4) DEFAULT NULL,
  `auswahletiketten` int(11) DEFAULT NULL,
  `auswahllagerplatz` int(11) DEFAULT NULL,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(1024) DEFAULT NULL,
  `erloese` varchar(8) DEFAULT NULL,
  `erloesefestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_gesamt` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_gesamt` decimal(14,4) DEFAULT NULL,
  `explodiert_parent` int(11) NOT NULL DEFAULT 0,
  `keinrabatterlaubt` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bestellung` (`bestellung`,`artikel`),
  KEY `artikel` (`artikel`),
  KEY `bestellung_2` (`bestellung`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bestellung_protokoll`
--

DROP TABLE IF EXISTS `bestellung_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bestellung_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bestellung` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bestellung` (`bestellung`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bestellvorschlag`
--

DROP TABLE IF EXISTS `bestellvorschlag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bestellvorschlag` (
  `user` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `menge` int(11) NOT NULL,
  PRIMARY KEY (`artikel`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bestellvorschlag_app`
--

DROP TABLE IF EXISTS `bestellvorschlag_app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bestellvorschlag_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(14,4) NOT NULL DEFAULT -1.0000,
  `bedarf` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `imauftrag` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `inproduktion` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `inbestellung` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `inbestellung_nichtversendet` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `promonat` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `verkauf` decimal(14,2) NOT NULL DEFAULT 0.00,
  `einkauf` decimal(14,2) NOT NULL DEFAULT 0.00,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `kommentar` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(32) NOT NULL,
  `lieferant` int(11) NOT NULL DEFAULT 0,
  `auswahl` tinyint(1) NOT NULL DEFAULT 0,
  `von` date NOT NULL DEFAULT '0000-00-00',
  `bis` date NOT NULL DEFAULT '0000-00-00',
  `nr` int(11) NOT NULL DEFAULT 0,
  `einkaufsid` int(11) NOT NULL DEFAULT 0,
  `bedarfgesamt` decimal(14,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bestellvorschlag_app_staffeln`
--

DROP TABLE IF EXISTS `bestellvorschlag_app_staffeln`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bestellvorschlag_app_staffeln` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `tage` int(11) NOT NULL DEFAULT 0,
  `bedarfstaffel` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `boxnachrichten`
--

DROP TABLE IF EXISTS `boxnachrichten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boxnachrichten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `bezeichnung` varchar(255) NOT NULL,
  `nachricht` text NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `prio` int(11) NOT NULL DEFAULT 0,
  `ablaufzeit` int(11) NOT NULL DEFAULT 0,
  `objekt` varchar(255) NOT NULL,
  `parameter` int(11) NOT NULL DEFAULT 0,
  `beep` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bundesstaaten`
--

DROP TABLE IF EXISTS `bundesstaaten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bundesstaaten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `land` varchar(255) NOT NULL,
  `iso` varchar(255) NOT NULL,
  `bundesstaat` varchar(255) NOT NULL,
  `aktiv` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caldav_changes`
--

DROP TABLE IF EXISTS `caldav_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caldav_changes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` text DEFAULT NULL,
  `change_type` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `change_log`
--

DROP TABLE IF EXISTS `change_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `change_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bearbeiter` varchar(32) NOT NULL,
  `module` varchar(32) NOT NULL,
  `action` varchar(32) NOT NULL,
  `tabelle` varchar(64) NOT NULL,
  `tableid` int(11) NOT NULL DEFAULT 0,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tableid` (`tableid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `change_log_field`
--

DROP TABLE IF EXISTS `change_log_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `change_log_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `change_log` int(11) NOT NULL DEFAULT 0,
  `fieldname` varchar(64) NOT NULL,
  `oldvalue` text NOT NULL,
  `newvalue` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `change_log` (`change_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chargen`
--

DROP TABLE IF EXISTS `chargen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chargen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `charge` text NOT NULL,
  `adresse` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `beschreibung` varchar(255) NOT NULL,
  `lieferung` date NOT NULL,
  `lieferschein` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `logdatei` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chargen_log`
--

DROP TABLE IF EXISTS `chargen_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chargen_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `eingang` int(1) NOT NULL DEFAULT 0,
  `bezeichnung` text NOT NULL,
  `internebemerkung` text NOT NULL,
  `zeit` datetime DEFAULT NULL,
  `adresse_mitarbeiter` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `doctype` varchar(32) NOT NULL,
  `doctypeid` int(11) NOT NULL DEFAULT 0,
  `bestand` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `is_interim` tinyint(1) NOT NULL DEFAULT 0,
  `storage_movement_id` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `doctypeid` (`doctypeid`),
  KEY `doctype` (`doctype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chargenverwaltung`
--

DROP TABLE IF EXISTS `chargenverwaltung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chargenverwaltung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL,
  `bestellung` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat`
--

DROP TABLE IF EXISTS `chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_from` int(11) NOT NULL DEFAULT 0,
  `user_to` int(11) NOT NULL DEFAULT 0,
  `message` text NOT NULL,
  `zeitstempel` datetime DEFAULT NULL,
  `prio` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_from` (`user_from`),
  KEY `user_to` (`user_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_gelesen`
--

DROP TABLE IF EXISTS `chat_gelesen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_gelesen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `message` int(11) NOT NULL DEFAULT 0,
  `zeitstempel` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`,`message`),
  KEY `message` (`message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checkaltertable`
--

DROP TABLE IF EXISTS `checkaltertable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkaltertable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `checksum` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=586 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collectivedebitor`
--

DROP TABLE IF EXISTS `collectivedebitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collectivedebitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paymentmethod_id` int(11) DEFAULT NULL,
  `channel_id` int(11) NOT NULL DEFAULT 0,
  `country` varchar(255) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `account` varchar(255) DEFAULT NULL,
  `store_in_address` tinyint(1) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjob_kommissionierung`
--

DROP TABLE IF EXISTS `cronjob_kommissionierung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cronjob_kommissionierung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `bezeichnung` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjob_log`
--

DROP TABLE IF EXISTS `cronjob_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cronjob_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `cronjob_id` int(11) NOT NULL DEFAULT 0,
  `memory_usage` int(11) NOT NULL DEFAULT 0,
  `memory_peak` int(11) NOT NULL DEFAULT 0,
  `cronjob_name` varchar(255) NOT NULL,
  `change_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cronjob_id` (`cronjob_id`,`change_time`)
) ENGINE=InnoDB AUTO_INCREMENT=70109 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjob_starter_running`
--

DROP TABLE IF EXISTS `cronjob_starter_running`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cronjob_starter_running` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(23) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(10) NOT NULL,
  `task_id` int(11) NOT NULL DEFAULT 0,
  `last_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=9530 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crossselling_artikel`
--

DROP TABLE IF EXISTS `crossselling_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crossselling_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aktiv` int(1) NOT NULL,
  `art` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `crosssellingartikel` int(11) NOT NULL,
  `shop` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `bemerkung` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `gegenseitigzuweisen` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datei`
--

DROP TABLE IF EXISTS `datei`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datei` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `titel` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `geloescht` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `firma` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datei_stichwoerter`
--

DROP TABLE IF EXISTS `datei_stichwoerter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datei_stichwoerter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datei` int(10) NOT NULL,
  `subjekt` varchar(255) NOT NULL,
  `objekt` varchar(255) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sort` int(11) NOT NULL DEFAULT 0,
  `parameter2` int(11) NOT NULL DEFAULT 0,
  `objekt2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `datei` (`datei`),
  KEY `parameter` (`parameter`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datei_stichwortvorlagen`
--

DROP TABLE IF EXISTS `datei_stichwortvorlagen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datei_stichwortvorlagen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `beschriftung` varchar(128) NOT NULL,
  `ausblenden` tinyint(1) NOT NULL DEFAULT 0,
  `modul` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datei_version`
--

DROP TABLE IF EXISTS `datei_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datei_version` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `datei` int(10) NOT NULL,
  `ersteller` varchar(255) NOT NULL,
  `datum` date NOT NULL,
  `version` int(5) NOT NULL,
  `dateiname` varchar(255) NOT NULL,
  `bemerkung` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `size` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `datei` (`datei`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dateibaum`
--

DROP TABLE IF EXISTS `dateibaum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dateibaum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datei_stichwoerter` int(11) NOT NULL DEFAULT 0,
  `pfad` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datev_buchungen`
--

DROP TABLE IF EXISTS `datev_buchungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datev_buchungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wkz` varchar(255) NOT NULL,
  `umsatz` decimal(10,2) NOT NULL,
  `gegenkonto` int(255) NOT NULL,
  `belegfeld1` varchar(255) NOT NULL,
  `belegfeld2` varchar(255) NOT NULL,
  `datum` date NOT NULL,
  `konto` varchar(255) NOT NULL,
  `haben` int(1) NOT NULL,
  `kost1` varchar(255) NOT NULL,
  `kost2` varchar(255) NOT NULL,
  `kostmenge` varchar(255) NOT NULL,
  `skonto` decimal(10,2) NOT NULL,
  `buchungstext` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `exportiert` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `kontoauszug` int(11) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datevconnect_online_export`
--

DROP TABLE IF EXISTS `datevconnect_online_export`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datevconnect_online_export` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `delivery_problemcase`
--

DROP TABLE IF EXISTS `delivery_problemcase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_problemcase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `problemcase` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `device_jobs`
--

DROP TABLE IF EXISTS `device_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deviceidsource` varchar(64) DEFAULT NULL,
  `deviceiddest` varchar(64) DEFAULT NULL,
  `job` longtext NOT NULL,
  `zeitstempel` datetime DEFAULT NULL,
  `abgeschlossen` tinyint(1) NOT NULL DEFAULT 0,
  `art` varchar(64) DEFAULT NULL,
  `request_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docscan`
--

DROP TABLE IF EXISTS `docscan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docscan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datei` int(11) DEFAULT NULL,
  `kategorie` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docscan_metadata`
--

DROP TABLE IF EXISTS `docscan_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docscan_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `docscan_id` int(10) unsigned NOT NULL,
  `meta_key` varchar(32) NOT NULL,
  `meta_value` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_customization_infoblock`
--

DROP TABLE IF EXISTS `document_customization_infoblock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_customization_infoblock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) NOT NULL,
  `doctype` varchar(32) NOT NULL,
  `fontstyle` varchar(2) NOT NULL,
  `alignment` varchar(2) NOT NULL,
  `content` text DEFAULT NULL,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `document_customization_infoblock_translation`
--

DROP TABLE IF EXISTS `document_customization_infoblock_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_customization_infoblock_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_customization_infoblock_id` int(11) NOT NULL DEFAULT 0,
  `language_code` varchar(2) NOT NULL,
  `content` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `fontstyle` varchar(2) NOT NULL,
  `alignment` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `document_customization_infoblock_id` (`document_customization_infoblock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dokumente`
--

DROP TABLE IF EXISTS `dokumente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dokumente` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `adresse_from` int(11) NOT NULL,
  `adresse_to` int(11) NOT NULL,
  `typ` varchar(24) NOT NULL,
  `von` varchar(512) NOT NULL,
  `firma` varchar(512) NOT NULL,
  `an` varchar(512) NOT NULL,
  `email_an` varchar(255) NOT NULL,
  `firma_an` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `plz` varchar(16) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(32) NOT NULL,
  `datum` date NOT NULL,
  `betreff` varchar(1023) NOT NULL,
  `content` text NOT NULL,
  `signatur` tinyint(1) NOT NULL,
  `send_as` varchar(24) NOT NULL,
  `email` varchar(255) NOT NULL,
  `printer` int(2) NOT NULL,
  `fax` tinyint(2) NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `ansprechpartner` varchar(512) DEFAULT NULL,
  `email_cc` varchar(255) DEFAULT NULL,
  `email_bcc` varchar(255) DEFAULT NULL,
  `bearbeiter` varchar(128) DEFAULT NULL,
  `uhrzeit` time DEFAULT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse_to` (`adresse_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dokumente_send`
--

DROP TABLE IF EXISTS `dokumente_send`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dokumente_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dokument` varchar(255) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `adresse` int(11) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `parameter` int(11) NOT NULL,
  `art` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `geloescht` int(1) NOT NULL,
  `versendet` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dateiid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dropshipping`
--

DROP TABLE IF EXISTS `dropshipping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dropshipping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `gruppe` (`gruppe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dropshipping_gruppe`
--

DROP TABLE IF EXISTS `dropshipping_gruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dropshipping_gruppe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(200) NOT NULL,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `autoversand` int(1) NOT NULL DEFAULT 0,
  `zahlungok` tinyint(1) NOT NULL DEFAULT 0,
  `lieferdatumberechnen` int(1) NOT NULL DEFAULT 0,
  `bestellunganlegen` int(1) NOT NULL DEFAULT 0,
  `abweichendelieferadresse` int(1) NOT NULL DEFAULT 0,
  `lieferscheinanhaengen` int(1) NOT NULL DEFAULT 0,
  `rechnunganhaengen` int(1) NOT NULL DEFAULT 0,
  `auftraganhaengen` int(1) NOT NULL DEFAULT 0,
  `bestellungmail` int(1) NOT NULL DEFAULT 0,
  `lieferscheinmail` int(1) NOT NULL DEFAULT 0,
  `rechnungmail` int(1) NOT NULL DEFAULT 0,
  `rueckmeldungshop` int(1) NOT NULL DEFAULT 0,
  `bestellungdrucken` int(11) NOT NULL DEFAULT 0,
  `lieferscheindrucken` int(11) NOT NULL DEFAULT 0,
  `rechnungdrucken` int(11) NOT NULL DEFAULT 0,
  `lieferscheincsv` tinyint(1) NOT NULL DEFAULT 0,
  `auftragcsv` tinyint(1) NOT NULL DEFAULT 0,
  `bestellungabschliessen` tinyint(1) NOT NULL DEFAULT 0,
  `belegeautoversandkunde` tinyint(1) NOT NULL DEFAULT 0,
  `belegeautoversand` varchar(16) NOT NULL DEFAULT 'standardauftrag',
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `drucker`
--

DROP TABLE IF EXISTS `drucker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drucker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `befehl` varchar(255) NOT NULL,
  `aktiv` int(1) NOT NULL,
  `firma` int(1) NOT NULL,
  `tomail` varchar(255) NOT NULL,
  `tomailtext` text NOT NULL,
  `tomailsubject` text NOT NULL,
  `adapterboxip` varchar(255) NOT NULL,
  `adapterboxseriennummer` varchar(255) NOT NULL,
  `adapterboxpasswort` varchar(255) NOT NULL,
  `anbindung` varchar(255) NOT NULL,
  `art` int(1) NOT NULL DEFAULT 0,
  `faxserver` int(1) NOT NULL DEFAULT 0,
  `format` varchar(64) NOT NULL,
  `keinhintergrund` tinyint(1) NOT NULL DEFAULT 0,
  `json` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `drucker_spooler`
--

DROP TABLE IF EXISTS `drucker_spooler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drucker_spooler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drucker` int(11) NOT NULL DEFAULT 0,
  `filename` varchar(128) NOT NULL,
  `content` longblob NOT NULL,
  `description` varchar(128) NOT NULL,
  `anzahl` varchar(128) NOT NULL,
  `befehl` varchar(128) NOT NULL,
  `anbindung` varchar(128) NOT NULL,
  `zeitstempel` datetime DEFAULT NULL,
  `user` int(11) NOT NULL DEFAULT 0,
  `gedruckt` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `drucker` (`drucker`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dsgvo_loeschauftrag`
--

DROP TABLE IF EXISTS `dsgvo_loeschauftrag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dsgvo_loeschauftrag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `loeschauftrag_vom` date NOT NULL,
  `kommentar` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dta`
--

DROP TABLE IF EXISTS `dta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `datum` date NOT NULL,
  `name` varchar(255) NOT NULL,
  `konto` varchar(64) NOT NULL,
  `blz` varchar(64) NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `vz1` varchar(255) NOT NULL,
  `vz2` varchar(255) NOT NULL,
  `vz3` varchar(255) NOT NULL,
  `lastschrift` int(1) NOT NULL,
  `gutschrift` int(1) NOT NULL,
  `kontointern` int(10) NOT NULL,
  `datei` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `waehrung` varchar(3) NOT NULL DEFAULT 'EUR',
  `verbindlichkeit` int(11) NOT NULL DEFAULT 0,
  `rechnung` int(11) NOT NULL DEFAULT 0,
  `mandatsreferenzaenderung` tinyint(1) NOT NULL DEFAULT 0,
  `mandatsreferenzart` varchar(64) NOT NULL,
  `mandatsreferenzwdhart` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dta_datei`
--

DROP TABLE IF EXISTS `dta_datei`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dta_datei` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `inhalt` text NOT NULL,
  `datum` datetime NOT NULL,
  `status` varchar(64) NOT NULL,
  `art` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `konto` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dta_datei_verband`
--

DROP TABLE IF EXISTS `dta_datei_verband`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dta_datei_verband` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `bemerkung` text NOT NULL,
  `dateiname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `nachricht` text NOT NULL,
  `datum_versendet` date DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `verband` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `variante` int(11) NOT NULL DEFAULT 0,
  `partnerid` varchar(255) NOT NULL,
  `kundennummer` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eangenerator`
--

DROP TABLE IF EXISTS `eangenerator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eangenerator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ean` varchar(255) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_articles_to_sync`
--

DROP TABLE IF EXISTS `ebay_articles_to_sync`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_articles_to_sync` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `request` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_artikelzuordnungen`
--

DROP TABLE IF EXISTS `ebay_artikelzuordnungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_artikelzuordnungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` varchar(255) NOT NULL,
  `shop` int(10) NOT NULL,
  `bezeichnung` varchar(255) DEFAULT NULL,
  `variation` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `artikel` int(11) DEFAULT NULL,
  `erledigt` int(1) DEFAULT 0,
  `verkauft` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_auktionen`
--

DROP TABLE IF EXISTS `ebay_auktionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_auktionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bild` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `itemid` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `artikel` int(11) NOT NULL,
  `typ` varchar(255) NOT NULL,
  `startdatum` datetime NOT NULL,
  `dauer` varchar(255) NOT NULL,
  `letzteaktualisierung` date NOT NULL,
  `eingestellt` int(11) NOT NULL DEFAULT 0,
  `verfuegbar` int(11) NOT NULL DEFAULT 0,
  `verkauf` float NOT NULL DEFAULT 0,
  `sofortkauf` float NOT NULL DEFAULT 0,
  `beobachtet` int(11) NOT NULL DEFAULT 0,
  `shop` int(11) NOT NULL,
  `aktiv` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_bulk_call`
--

DROP TABLE IF EXISTS `ebay_bulk_call`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_bulk_call` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `request` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_bulk_jobs`
--

DROP TABLE IF EXISTS `ebay_bulk_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_bulk_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` varchar(255) DEFAULT NULL,
  `file_id` varchar(255) DEFAULT NULL,
  `response_file_id` varchar(255) DEFAULT NULL,
  `shop_id` int(11) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `next_action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_fee_overview`
--

DROP TABLE IF EXISTS `ebay_fee_overview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_fee_overview` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `itemid` varchar(255) NOT NULL,
  `fee_date` datetime NOT NULL,
  `fee_type` varchar(255) NOT NULL,
  `fee_description` varchar(255) NOT NULL,
  `fee_amount` varchar(255) NOT NULL,
  `fee_vat` float NOT NULL DEFAULT 0,
  `fee_memo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_kategoriespezifisch`
--

DROP TABLE IF EXISTS `ebay_kategoriespezifisch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_kategoriespezifisch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `artikel` int(11) NOT NULL,
  `shop` int(11) NOT NULL,
  `primsec` int(11) NOT NULL,
  `spec` int(11) NOT NULL,
  `specname` varchar(255) DEFAULT NULL,
  `typ` varchar(255) DEFAULT NULL,
  `cardinality` varchar(255) DEFAULT NULL,
  `maxvalues` int(11) DEFAULT 1,
  `options` text DEFAULT NULL,
  `val` varchar(255) DEFAULT NULL,
  `mandatory` int(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_kategorievorschlag`
--

DROP TABLE IF EXISTS `ebay_kategorievorschlag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_kategorievorschlag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `kategorie` varchar(255) NOT NULL,
  `vorschlagcategoryid` int(11) DEFAULT NULL,
  `vorschlagbezeichnung` varchar(255) DEFAULT NULL,
  `vorschlagparentsid` varchar(255) DEFAULT NULL,
  `vorschlagparentsbezeichnung` varchar(255) DEFAULT NULL,
  `wahrscheinlichkeit` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_kategoriezustand`
--

DROP TABLE IF EXISTS `ebay_kategoriezustand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_kategoriezustand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kategorie` varchar(255) NOT NULL,
  `wert` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `datum` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_picture_hosting_service`
--

DROP TABLE IF EXISTS `ebay_picture_hosting_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_picture_hosting_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebay_staging_listing_id` int(11) DEFAULT NULL,
  `ebay_staging_listing_variation_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ebay_staging_listing_id` (`ebay_staging_listing_id`),
  KEY `ebay_staging_listing_variation_id` (`ebay_staging_listing_variation_id`),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_rahmenbedingungen`
--

DROP TABLE IF EXISTS `ebay_rahmenbedingungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_rahmenbedingungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(10) NOT NULL,
  `aktiv` int(1) DEFAULT 0,
  `profilid` varchar(255) NOT NULL,
  `profiltype` varchar(255) NOT NULL,
  `profilname` varchar(255) NOT NULL,
  `profilsummary` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `defaultwert` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_rest_token`
--

DROP TABLE IF EXISTS `ebay_rest_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_rest_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopexport_id` int(11) NOT NULL,
  `token` text NOT NULL,
  `scope` varchar(255) NOT NULL,
  `valid_until` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_staging_listing`
--

DROP TABLE IF EXISTS `ebay_staging_listing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_staging_listing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `shop_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT 0,
  `item_id_external` varchar(255) DEFAULT NULL,
  `ebay_primary_category_id_external` varchar(255) DEFAULT NULL,
  `ebay_primary_store_category_id_external` varchar(255) DEFAULT NULL,
  `ebay_secondary_store_category_id_external` varchar(255) DEFAULT NULL,
  `ebay_secondary_category_id_external` varchar(255) DEFAULT NULL,
  `ebay_shipping_profile_id_external` varchar(255) DEFAULT NULL,
  `ebay_return_profile_id_external` varchar(255) DEFAULT NULL,
  `ebay_payment_profile_id_external` varchar(255) DEFAULT NULL,
  `ebay_private_listing` int(1) DEFAULT 0,
  `ebay_price_suggestion` int(1) DEFAULT 0,
  `ebay_plus` int(1) DEFAULT 0,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `ean` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `listing_duration` varchar(255) DEFAULT NULL,
  `inventory_tracking_method` varchar(255) DEFAULT NULL,
  `condition_display_name` varchar(255) DEFAULT NULL,
  `condition_id_external` varchar(255) DEFAULT NULL,
  `condition_description` varchar(255) DEFAULT NULL,
  `delivery_time` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_staging_listing_specific`
--

DROP TABLE IF EXISTS `ebay_staging_listing_specific`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_staging_listing_specific` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebay_staging_listing_id` int(11) DEFAULT NULL,
  `property` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ebay_staging_listing_id` (`ebay_staging_listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_staging_listing_variant`
--

DROP TABLE IF EXISTS `ebay_staging_listing_variant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_staging_listing_variant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `ebay_staging_listing_id` int(11) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `ebay_staging_listing_id` (`ebay_staging_listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_staging_listing_variant_specific`
--

DROP TABLE IF EXISTS `ebay_staging_listing_variant_specific`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_staging_listing_variant_specific` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebay_staging_listing_variant_id` int(11) DEFAULT NULL,
  `property` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_storekategorien`
--

DROP TABLE IF EXISTS `ebay_storekategorien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_storekategorien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `shop` int(11) NOT NULL,
  `kategorie` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_template`
--

DROP TABLE IF EXISTS `ebay_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aktiv` int(1) DEFAULT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `template` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_variantenbilder`
--

DROP TABLE IF EXISTS `ebay_variantenbilder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_variantenbilder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datei` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `datum` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_versand`
--

DROP TABLE IF EXISTS `ebay_versand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_versand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `beschreibung` varchar(255) NOT NULL,
  `carrier` varchar(255) NOT NULL,
  `customcarrier` varchar(255) NOT NULL,
  `zeitmin` int(11) DEFAULT 0,
  `zeitmax` int(11) DEFAULT 0,
  `service` varchar(255) DEFAULT NULL,
  `kategorie` varchar(255) DEFAULT NULL,
  `aktiv` int(1) DEFAULT 0,
  `shop` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ebay_versand_zuordnung`
--

DROP TABLE IF EXISTS `ebay_versand_zuordnung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebay_versand_zuordnung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ebayversand` int(11) DEFAULT 0,
  `versandart` int(11) DEFAULT 0,
  `shop` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eigenschaften`
--

DROP TABLE IF EXISTS `eigenschaften`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eigenschaften` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `art` int(11) NOT NULL DEFAULT 0,
  `hauptkategorie` varchar(128) DEFAULT NULL,
  `unterkategorie` varchar(128) DEFAULT NULL,
  `einheit` varchar(64) DEFAULT NULL,
  `wert` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `einkaufspreise`
--

DROP TABLE IF EXISTS `einkaufspreise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `einkaufspreise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL,
  `adresse` int(11) NOT NULL,
  `objekt` varchar(255) NOT NULL,
  `projekt` varchar(255) NOT NULL,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `waehrung` varchar(255) NOT NULL,
  `ab_menge` decimal(14,4) NOT NULL DEFAULT 1.0000,
  `vpe` varchar(64) NOT NULL DEFAULT '1',
  `preis_anfrage_vom` date NOT NULL,
  `gueltig_bis` date NOT NULL,
  `lieferzeit_standard` int(11) NOT NULL,
  `lieferzeit_aktuell` int(11) NOT NULL,
  `lager_lieferant` int(11) NOT NULL,
  `datum_lagerlieferant` date NOT NULL,
  `bestellnummer` varchar(255) NOT NULL,
  `bezeichnunglieferant` varchar(255) NOT NULL,
  `sicherheitslager` int(11) NOT NULL,
  `bemerkung` text NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `standard` int(1) NOT NULL,
  `geloescht` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `apichange` tinyint(1) NOT NULL DEFAULT 0,
  `rahmenvertrag` tinyint(1) NOT NULL DEFAULT 0,
  `rahmenvertrag_von` date DEFAULT NULL,
  `rahmenvertrag_bis` date DEFAULT NULL,
  `rahmenvertrag_menge` int(11) NOT NULL DEFAULT 0,
  `beschreibung` text NOT NULL,
  `nichtberechnet` tinyint(1) NOT NULL DEFAULT 1,
  `lieferzeit_standard_einheit` varchar(64) NOT NULL,
  `lieferzeit_aktuell_einheit` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`),
  KEY `adresse` (`adresse`),
  KEY `projekt` (`projekt`),
  KEY `bestellnummer` (`bestellnummer`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emailbackup`
--

DROP TABLE IF EXISTS `emailbackup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailbackup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `angezeigtername` varchar(255) NOT NULL,
  `internebeschreibung` varchar(255) NOT NULL,
  `benutzername` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `server` varchar(255) NOT NULL,
  `smtp` varchar(255) NOT NULL,
  `ticket` int(1) NOT NULL,
  `imap_sentfolder_aktiv` int(1) NOT NULL,
  `imap_sentfolder` varchar(255) NOT NULL DEFAULT 'inbox.sent',
  `imap_port` int(11) NOT NULL DEFAULT 993,
  `imap_type` int(11) NOT NULL DEFAULT 3,
  `autoresponder` int(1) NOT NULL,
  `geschaeftsbriefvorlage` int(11) NOT NULL,
  `autoresponderbetreff` varchar(255) NOT NULL,
  `autorespondertext` text NOT NULL,
  `projekt` int(11) NOT NULL,
  `emailbackup` int(1) NOT NULL,
  `adresse` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `loeschtage` varchar(255) NOT NULL,
  `geloescht` int(1) NOT NULL,
  `ticketloeschen` tinyint(1) NOT NULL DEFAULT 0,
  `ticketabgeschlossen` tinyint(1) NOT NULL DEFAULT 0,
  `ticketqueue` varchar(255) DEFAULT NULL,
  `ticketprojekt` varchar(255) DEFAULT NULL,
  `ticketemaileingehend` int(1) NOT NULL DEFAULT 0,
  `smtp_extra` int(1) NOT NULL DEFAULT 0,
  `smtp_ssl` int(1) NOT NULL DEFAULT 0,
  `smtp_port` int(11) NOT NULL DEFAULT 25,
  `smtp_frommail` varchar(128) NOT NULL,
  `smtp_fromname` varchar(128) NOT NULL,
  `client_alias` varchar(255) NOT NULL,
  `smtp_authtype` varchar(128) NOT NULL,
  `smtp_authparam` text NOT NULL,
  `smtp_loglevel` int(1) NOT NULL DEFAULT 0,
  `autosresponder_blacklist` tinyint(1) NOT NULL DEFAULT 1,
  `eigenesignatur` tinyint(1) NOT NULL DEFAULT 0,
  `signatur` text NOT NULL,
  `mutex` tinyint(1) NOT NULL DEFAULT 0,
  `abdatum` date DEFAULT NULL,
  `email` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emailbackup_mails`
--

DROP TABLE IF EXISTS `emailbackup_mails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailbackup_mails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webmail` int(10) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `action` longtext NOT NULL,
  `action_html` longtext NOT NULL,
  `empfang` datetime DEFAULT NULL,
  `anhang` varchar(255) NOT NULL,
  `gelesen` int(1) NOT NULL DEFAULT 0,
  `dsgvo` int(1) NOT NULL DEFAULT 0,
  `checksum` varchar(255) NOT NULL,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `spam` int(1) NOT NULL DEFAULT 0,
  `antworten` int(1) NOT NULL DEFAULT 0,
  `phpobj` text DEFAULT NULL,
  `flattenedparts` longblob DEFAULT NULL,
  `attachment` longblob DEFAULT NULL,
  `geloescht` int(1) NOT NULL DEFAULT 0,
  `warteschlange` int(1) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `ticketnachricht` int(11) NOT NULL DEFAULT 0,
  `mail_replyto` varchar(255) NOT NULL,
  `verfasser_replyto` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `webmail` (`webmail`),
  KEY `gelesen` (`gelesen`),
  KEY `spam` (`spam`),
  KEY `geloescht` (`geloescht`),
  KEY `antworten` (`antworten`),
  KEY `warteschlange` (`warteschlange`),
  KEY `adresse` (`adresse`),
  KEY `checksum` (`checksum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `epost_files`
--

DROP TABLE IF EXISTS `epost_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `epost_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rechnung` int(10) unsigned DEFAULT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` text DEFAULT NULL,
  `datei` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etiketten`
--

DROP TABLE IF EXISTS `etiketten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etiketten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `xml` text NOT NULL,
  `bemerkung` text NOT NULL,
  `ausblenden` tinyint(1) NOT NULL DEFAULT 0,
  `verwendenals` varchar(64) NOT NULL,
  `labelbreite` int(11) NOT NULL DEFAULT 50,
  `labelhoehe` int(11) NOT NULL DEFAULT 18,
  `labelabstand` int(11) NOT NULL DEFAULT 3,
  `labeloffsetx` int(11) NOT NULL DEFAULT 0,
  `labeloffsety` int(11) NOT NULL DEFAULT 6,
  `format` varchar(64) NOT NULL,
  `manuell` tinyint(1) NOT NULL DEFAULT 0,
  `anzahlprozeile` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etsy_taxonomy`
--

DROP TABLE IF EXISTS `etsy_taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etsy_taxonomy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `id_external` varchar(255) NOT NULL,
  `parent_id_external` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etsy_transaction`
--

DROP TABLE IF EXISTS `etsy_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etsy_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `etsy_transaction_id` varchar(255) NOT NULL,
  `etsy_listing_id` int(11) NOT NULL,
  `etsy_title` varchar(255) NOT NULL,
  `etsy_buyer_email` varchar(255) NOT NULL,
  `etsy_creation_time` datetime NOT NULL,
  `fetched_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `beschreibung` varchar(255) NOT NULL,
  `kategorie` varchar(255) NOT NULL,
  `zeit` datetime NOT NULL,
  `objekt` varchar(255) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_api`
--

DROP TABLE IF EXISTS `event_api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cachetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `eventname` varchar(255) DEFAULT NULL,
  `parameter` varchar(255) DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `retries` int(11) DEFAULT NULL,
  `kommentar` varchar(255) DEFAULT NULL,
  `api` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exportlink_sent`
--

DROP TABLE IF EXISTS `exportlink_sent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exportlink_sent` (
  `id` int(11) NOT NULL,
  `reg` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  `objekt` int(11) NOT NULL,
  `mail` int(11) NOT NULL,
  `ident` int(11) NOT NULL,
  `adresse` int(11) NOT NULL,
  `datum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exportvorlage`
--

DROP TABLE IF EXISTS `exportvorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exportvorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) DEFAULT NULL,
  `ziel` varchar(255) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  `fields` text DEFAULT NULL,
  `fields_where` text DEFAULT NULL,
  `letzterexport` datetime DEFAULT NULL,
  `mitarbeiterletzterexport` varchar(255) DEFAULT NULL,
  `exporttrennzeichen` varchar(255) DEFAULT NULL,
  `exporterstezeilenummer` int(11) DEFAULT NULL,
  `exportdatenmaskierung` varchar(255) DEFAULT NULL,
  `exportzeichensatz` varchar(255) DEFAULT NULL,
  `filterdatum` tinyint(1) NOT NULL DEFAULT 0,
  `filterprojekt` tinyint(1) NOT NULL DEFAULT 0,
  `apifreigabe` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extended_approval_protocol`
--

DROP TABLE IF EXISTS `extended_approval_protocol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extended_approval_protocol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctype_id` int(11) NOT NULL DEFAULT 0,
  `doctype` varchar(255) NOT NULL,
  `requestertype` varchar(255) NOT NULL,
  `requester_id` int(11) NOT NULL DEFAULT 0,
  `moneylimit` decimal(14,2) NOT NULL DEFAULT 0.00,
  `releasetype` varchar(255) NOT NULL,
  `release_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extended_approval_responsibility`
--

DROP TABLE IF EXISTS `extended_approval_responsibility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extended_approval_responsibility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctype` varchar(255) NOT NULL,
  `requestertype` varchar(255) NOT NULL,
  `requester_id` int(11) NOT NULL DEFAULT 0,
  `moneylimit` decimal(14,2) NOT NULL DEFAULT 0.00,
  `releasetype` varchar(255) NOT NULL,
  `release_id` int(11) NOT NULL DEFAULT 0,
  `email` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fee_reduction`
--

DROP TABLE IF EXISTS `fee_reduction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_reduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctype` varchar(64) NOT NULL,
  `doctype_id` int(11) NOT NULL DEFAULT 0,
  `position_id` int(11) NOT NULL DEFAULT 0,
  `amount` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `price` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `price_type` varchar(64) NOT NULL,
  `currency` varchar(8) NOT NULL,
  `comment` varchar(1024) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `doctype` (`doctype`),
  KEY `doctype_id` (`doctype_id`),
  KEY `price_type` (`price_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fibu_buchungen`
--

DROP TABLE IF EXISTS `fibu_buchungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fibu_buchungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von_typ` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `von_id` int(11) NOT NULL,
  `nach_typ` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `nach_id` int(11) NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `waehrung` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'EUR',
  `benutzer` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `internebemerkung` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `datum` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fibu_buchungen_alle`
--

DROP TABLE IF EXISTS `fibu_buchungen_alle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fibu_buchungen_alle` (
  `buchungsart` varchar(9) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `typ` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `id` int(11) NOT NULL,
  `datum` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `doc_typ` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `doc_id` int(11) NOT NULL,
  `doc_info` varchar(513) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `betrag` decimal(18,2) NOT NULL,
  `waehrung` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `edit_module` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `edit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `fibu_buchungen_alle_view`
--

DROP TABLE IF EXISTS `fibu_buchungen_alle_view`;
/*!50001 DROP VIEW IF EXISTS `fibu_buchungen_alle_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `fibu_buchungen_alle_view` AS SELECT
 1 AS `buchungsart`,
  1 AS `typ`,
  1 AS `id`,
  1 AS `datum`,
  1 AS `doc_typ`,
  1 AS `doc_id`,
  1 AS `doc_info`,
  1 AS `betrag`,
  1 AS `waehrung`,
  1 AS `edit_module`,
  1 AS `edit_id` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `fibu_objekte`
--

DROP TABLE IF EXISTS `fibu_objekte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fibu_objekte` (
  `datum` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `typ` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `id` int(11) NOT NULL,
  `info` varchar(513) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `parent_typ` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `parent_id` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `parent_info` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `is_beleg` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `fibu_objekte_view`
--

DROP TABLE IF EXISTS `fibu_objekte_view`;
/*!50001 DROP VIEW IF EXISTS `fibu_objekte_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `fibu_objekte_view` AS SELECT
 1 AS `datum`,
  1 AS `typ`,
  1 AS `id`,
  1 AS `info`,
  1 AS `parent_typ`,
  1 AS `parent_id`,
  1 AS `parent_info`,
  1 AS `is_beleg` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `file_link`
--

DROP TABLE IF EXISTS `file_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `label` varchar(255) NOT NULL,
  `file_link` varchar(255) NOT NULL,
  `internal_note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `firma`
--

DROP TABLE IF EXISTS `firma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `firma` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `standardprojekt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `firmendaten`
--

DROP TABLE IF EXISTS `firmendaten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `firmendaten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firma` int(11) NOT NULL,
  `logo` longblob NOT NULL,
  `briefpapier` longblob NOT NULL,
  `benutzername` varchar(64) NOT NULL,
  `passwort` varchar(64) NOT NULL,
  `host` varchar(64) NOT NULL,
  `port` varchar(64) NOT NULL,
  `mailssl` int(1) NOT NULL DEFAULT 0,
  `signatur` text NOT NULL,
  `datum` datetime NOT NULL,
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `deviceserials` text NOT NULL,
  `lizenz` text NOT NULL,
  `schluessel` text NOT NULL,
  `mlm_mindestbetrag` decimal(10,2) NOT NULL DEFAULT 50.00,
  `mlm_letzter_tag` date DEFAULT NULL,
  `mlm_erster_tag` date DEFAULT NULL,
  `mlm_letzte_berechnung` datetime DEFAULT NULL,
  `mlm_01` decimal(10,2) NOT NULL DEFAULT 15.00,
  `mlm_02` decimal(10,2) NOT NULL DEFAULT 20.00,
  `mlm_03` decimal(10,2) NOT NULL DEFAULT 28.00,
  `mlm_04` decimal(10,2) NOT NULL DEFAULT 32.00,
  `mlm_05` decimal(10,2) NOT NULL DEFAULT 36.00,
  `mlm_06` decimal(10,2) NOT NULL DEFAULT 40.00,
  `mlm_07` decimal(10,2) NOT NULL DEFAULT 44.00,
  `mlm_08` decimal(10,2) NOT NULL DEFAULT 44.00,
  `mlm_09` decimal(10,2) NOT NULL DEFAULT 44.00,
  `mlm_10` decimal(10,2) NOT NULL DEFAULT 44.00,
  `mlm_11` decimal(10,2) NOT NULL DEFAULT 50.00,
  `mlm_12` decimal(10,2) NOT NULL DEFAULT 54.00,
  `mlm_13` decimal(10,2) NOT NULL DEFAULT 45.00,
  `mlm_14` decimal(10,2) NOT NULL DEFAULT 48.00,
  `mlm_15` decimal(10,2) NOT NULL DEFAULT 60.00,
  `zahlung_rechnung_sofort_de` text NOT NULL,
  `zahlung_rechnung_de` text NOT NULL,
  `zahlung_vorkasse_de` text NOT NULL,
  `zahlung_lastschrift_de` text NOT NULL,
  `zahlung_nachnahme_de` text NOT NULL,
  `zahlung_bar_de` text NOT NULL,
  `zahlung_paypal_de` text NOT NULL,
  `zahlung_amazon_de` text NOT NULL,
  `zahlung_kreditkarte_de` text NOT NULL,
  `zahlung_ratenzahlung_de` text NOT NULL,
  `briefpapier2` longblob DEFAULT NULL,
  `freifeld1` text NOT NULL,
  `freifeld2` text NOT NULL,
  `freifeld3` text NOT NULL,
  `freifeld4` text NOT NULL,
  `freifeld5` text NOT NULL,
  `freifeld6` text NOT NULL,
  `firmenfarbehell` text NOT NULL,
  `firmenfarbedunkel` text NOT NULL,
  `firmenfarbeganzdunkel` text NOT NULL,
  `navigationfarbe` text NOT NULL,
  `navigationfarbeschrift` text NOT NULL,
  `unternavigationfarbe` text NOT NULL,
  `unternavigationfarbeschrift` text NOT NULL,
  `firmenlogo` longblob DEFAULT NULL,
  `rechnung_header` text DEFAULT NULL,
  `lieferschein_header` text DEFAULT NULL,
  `angebot_header` text DEFAULT NULL,
  `auftrag_header` text DEFAULT NULL,
  `gutschrift_header` text DEFAULT NULL,
  `bestellung_header` text DEFAULT NULL,
  `arbeitsnachweis_header` text DEFAULT NULL,
  `provisionsgutschrift_header` text DEFAULT NULL,
  `rechnung_footer` text DEFAULT NULL,
  `lieferschein_footer` text DEFAULT NULL,
  `angebot_footer` text DEFAULT NULL,
  `auftrag_footer` text DEFAULT NULL,
  `gutschrift_footer` text DEFAULT NULL,
  `bestellung_footer` text DEFAULT NULL,
  `arbeitsnachweis_footer` text DEFAULT NULL,
  `provisionsgutschrift_footer` text DEFAULT NULL,
  `eu_lieferung_vermerk` text NOT NULL,
  `export_lieferung_vermerk` text NOT NULL,
  `zahlung_amazon_bestellung_de` text NOT NULL,
  `zahlung_billsafe_de` text NOT NULL,
  `zahlung_sofortueberweisung_de` text NOT NULL,
  `zahlung_secupay_de` text NOT NULL,
  `adressefreifeld1` text NOT NULL,
  `adressefreifeld2` text NOT NULL,
  `adressefreifeld3` text NOT NULL,
  `adressefreifeld4` text NOT NULL,
  `adressefreifeld5` text NOT NULL,
  `adressefreifeld6` text NOT NULL,
  `adressefreifeld7` text NOT NULL,
  `adressefreifeld8` text NOT NULL,
  `adressefreifeld9` text NOT NULL,
  `adressefreifeld10` text NOT NULL,
  `zahlung_eckarte_de` text NOT NULL,
  `devicekey` varchar(64) NOT NULL,
  `mailanstellesmtp` int(1) DEFAULT NULL,
  `layout_iconbar` int(1) DEFAULT NULL,
  `bcc1` varchar(64) NOT NULL,
  `bcc2` varchar(64) NOT NULL,
  `firmenfarbe` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `betreffszeile` int(1) NOT NULL DEFAULT 0,
  `dokumententext` int(1) NOT NULL DEFAULT 0,
  `barcode_y` int(11) NOT NULL DEFAULT 265,
  `email_html_template` text NOT NULL,
  `freifeld7` text NOT NULL,
  `freifeld8` text NOT NULL,
  `freifeld9` text NOT NULL,
  `freifeld10` text NOT NULL,
  `freifeld11` text NOT NULL,
  `freifeld12` text NOT NULL,
  `freifeld13` text NOT NULL,
  `freifeld14` text NOT NULL,
  `freifeld15` text NOT NULL,
  `freifeld16` text NOT NULL,
  `freifeld17` text NOT NULL,
  `freifeld18` text NOT NULL,
  `freifeld19` text NOT NULL,
  `freifeld20` text NOT NULL,
  `freifeld21` text NOT NULL,
  `freifeld22` text NOT NULL,
  `freifeld23` text NOT NULL,
  `freifeld24` text NOT NULL,
  `freifeld25` text NOT NULL,
  `freifeld26` text NOT NULL,
  `freifeld27` text NOT NULL,
  `freifeld28` text NOT NULL,
  `freifeld29` text NOT NULL,
  `freifeld30` text NOT NULL,
  `freifeld31` text NOT NULL,
  `freifeld32` text NOT NULL,
  `freifeld33` text NOT NULL,
  `freifeld34` text NOT NULL,
  `freifeld35` text NOT NULL,
  `freifeld36` text NOT NULL,
  `freifeld37` text NOT NULL,
  `freifeld38` text NOT NULL,
  `freifeld39` text NOT NULL,
  `freifeld40` text NOT NULL,
  `adressefreifeld11` text NOT NULL,
  `adressefreifeld12` text NOT NULL,
  `adressefreifeld13` text NOT NULL,
  `adressefreifeld14` text NOT NULL,
  `adressefreifeld15` text NOT NULL,
  `adressefreifeld16` text NOT NULL,
  `adressefreifeld17` text NOT NULL,
  `adressefreifeld18` text NOT NULL,
  `adressefreifeld19` text NOT NULL,
  `adressefreifeld20` text NOT NULL,
  `proformarechnung_header` text DEFAULT NULL,
  `proformarechnung_footer` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `firmendaten_werte`
--

DROP TABLE IF EXISTS `firmendaten_werte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `firmendaten_werte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `typ` varchar(64) NOT NULL,
  `typ1` varchar(64) NOT NULL,
  `typ2` varchar(64) NOT NULL,
  `wert` text NOT NULL,
  `default_value` text NOT NULL,
  `default_null` tinyint(1) NOT NULL DEFAULT 0,
  `darf_null` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1258 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formeln`
--

DROP TABLE IF EXISTS `formeln`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formeln` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `kennung` varchar(255) NOT NULL,
  `aktiv` tinyint(1) NOT NULL,
  `formel` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kennung` (`kennung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formula_position`
--

DROP TABLE IF EXISTS `formula_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formula_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `formula` varchar(500) NOT NULL,
  `doctype` varchar(32) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `free_article`
--

DROP TABLE IF EXISTS `free_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `free_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `everyone` tinyint(1) NOT NULL DEFAULT 0,
  `while_stocks_last` tinyint(1) NOT NULL DEFAULT 0,
  `only_new_customer` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `free_article_included`
--

DROP TABLE IF EXISTS `free_article_included`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `free_article_included` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `free_article_id` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `order_position_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `free_article_id` (`free_article_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geschaeftsbrief_vorlagen`
--

DROP TABLE IF EXISTS `geschaeftsbrief_vorlagen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geschaeftsbrief_vorlagen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sprache` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `subjekt` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gls`
--

DROP TABLE IF EXISTS `gls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vorlage` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name2` varchar(255) NOT NULL,
  `name3` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `hausnr` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `notiz` varchar(255) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goodspostingdocument`
--

DROP TABLE IF EXISTS `goodspostingdocument`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goodspostingdocument` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `belegnr` varchar(32) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'angelegt',
  `name` varchar(64) NOT NULL,
  `document_date` date DEFAULT NULL,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `document_type` varchar(32) NOT NULL,
  `storage_location_from_id` int(11) NOT NULL DEFAULT 0,
  `storage_location_to_id` int(11) NOT NULL DEFAULT 0,
  `schreibschutz` tinyint(1) NOT NULL DEFAULT 0,
  `storagesort` varchar(16) NOT NULL,
  `document_info` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goodspostingdocument_movement`
--

DROP TABLE IF EXISTS `goodspostingdocument_movement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goodspostingdocument_movement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goodspostingdocument_position_id` int(11) NOT NULL DEFAULT 0,
  `quantity_stored` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `serial` varchar(64) NOT NULL,
  `batch` varchar(64) NOT NULL,
  `bestbefore` date DEFAULT NULL,
  `storage_location_from_id` int(11) NOT NULL DEFAULT 0,
  `storage_location_to_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `goodspostingdocument_position_id` (`goodspostingdocument_position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goodspostingdocument_position`
--

DROP TABLE IF EXISTS `goodspostingdocument_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goodspostingdocument_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goodspostingdocument_id` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `reason` varchar(255) NOT NULL,
  `relation_document` varchar(32) NOT NULL,
  `relation_document_id` int(11) NOT NULL DEFAULT 0,
  `relation_document_position_id` int(11) NOT NULL DEFAULT 0,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `quantity` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `quantity_stored` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `storage_location_from_id` int(11) NOT NULL DEFAULT 0,
  `storage_location_to_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `goodspostingdocument_id` (`goodspostingdocument_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goodspostingdocument_protocol`
--

DROP TABLE IF EXISTS `goodspostingdocument_protocol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goodspostingdocument_protocol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goodspostingdocument_id` int(11) NOT NULL DEFAULT 0,
  `message` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `goodspostingdocument_id` (`goodspostingdocument_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_access_token`
--

DROP TABLE IF EXISTS `google_access_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `google_account_id` int(11) unsigned NOT NULL DEFAULT 0,
  `token` varchar(255) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `google_account_id` (`google_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_account`
--

DROP TABLE IF EXISTS `google_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `refresh_token` varchar(255) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_account_property`
--

DROP TABLE IF EXISTS `google_account_property`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_account_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `google_account_id` int(11) unsigned NOT NULL DEFAULT 0,
  `varname` varchar(64) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `google_account_scope`
--

DROP TABLE IF EXISTS `google_account_scope`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_account_scope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `google_account_id` int(11) unsigned NOT NULL DEFAULT 0,
  `scope` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `google_account_id` (`google_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `googleapi`
--

DROP TABLE IF EXISTS `googleapi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `googleapi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `user` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `refresh_token` varchar(255) DEFAULT NULL,
  `last_auth` datetime DEFAULT NULL,
  `id_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `googleapi_calendar_sync`
--

DROP TABLE IF EXISTS `googleapi_calendar_sync`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `googleapi_calendar_sync` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL DEFAULT 0,
  `foreign_id` varchar(255) NOT NULL,
  `owner` int(11) NOT NULL DEFAULT 0,
  `from_google` tinyint(4) NOT NULL DEFAULT 0,
  `event_date` datetime DEFAULT NULL,
  `html_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `googleapi_user`
--

DROP TABLE IF EXISTS `googleapi_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `googleapi_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `googleapi_id_name` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `auto_sync` tinyint(4) NOT NULL DEFAULT 0,
  `identifier` varchar(255) DEFAULT NULL,
  `refresh_token` varchar(255) DEFAULT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gpsstechuhr`
--

DROP TABLE IF EXISTS `gpsstechuhr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gpsstechuhr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `koordinaten` varchar(512) DEFAULT NULL,
  `zeit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gruppen`
--

DROP TABLE IF EXISTS `gruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `art` varchar(512) DEFAULT NULL,
  `kennziffer` varchar(255) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  `grundrabatt` decimal(10,2) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `sonderrabatt_skonto` decimal(10,2) DEFAULT NULL,
  `provision` decimal(10,2) DEFAULT NULL,
  `kundennummer` varchar(255) DEFAULT NULL,
  `partnerid` varchar(255) DEFAULT NULL,
  `dta_aktiv` tinyint(1) NOT NULL DEFAULT 0,
  `dta_periode` tinyint(2) NOT NULL DEFAULT 0,
  `dta_dateiname` varchar(255) NOT NULL,
  `dta_mail` varchar(255) NOT NULL,
  `dta_mail_betreff` varchar(255) NOT NULL,
  `dta_mail_text` text DEFAULT NULL,
  `dtavariablen` text DEFAULT NULL,
  `dta_variante` int(11) NOT NULL DEFAULT 0,
  `bonus1` decimal(10,2) DEFAULT NULL,
  `bonus1_ab` decimal(10,2) DEFAULT NULL,
  `bonus2` decimal(10,2) DEFAULT NULL,
  `bonus2_ab` decimal(10,2) DEFAULT NULL,
  `bonus3` decimal(10,2) DEFAULT NULL,
  `bonus3_ab` decimal(10,2) DEFAULT NULL,
  `bonus4` decimal(10,2) DEFAULT NULL,
  `bonus4_ab` decimal(10,2) DEFAULT NULL,
  `bonus5` decimal(10,2) DEFAULT NULL,
  `bonus5_ab` decimal(10,2) DEFAULT NULL,
  `bonus6` decimal(10,2) DEFAULT NULL,
  `bonus6_ab` decimal(10,2) DEFAULT NULL,
  `bonus7` decimal(10,2) DEFAULT NULL,
  `bonus7_ab` decimal(10,2) DEFAULT NULL,
  `bonus8` decimal(10,2) DEFAULT NULL,
  `bonus8_ab` decimal(10,2) DEFAULT NULL,
  `bonus9` decimal(10,2) DEFAULT NULL,
  `bonus9_ab` decimal(10,2) DEFAULT NULL,
  `bonus10` decimal(10,2) DEFAULT NULL,
  `bonus10_ab` decimal(10,2) DEFAULT NULL,
  `zahlungszieltage` int(11) NOT NULL DEFAULT 14,
  `zahlungszielskonto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `zahlungszieltageskonto` int(11) NOT NULL DEFAULT 0,
  `portoartikel` int(11) DEFAULT NULL,
  `portofreiab` decimal(10,2) NOT NULL DEFAULT 0.00,
  `erweiterteoptionen` int(1) DEFAULT NULL,
  `zentralerechnung` int(1) DEFAULT NULL,
  `zentralregulierung` int(1) DEFAULT NULL,
  `gruppe` int(1) DEFAULT NULL,
  `preisgruppe` int(1) DEFAULT NULL,
  `verbandsgruppe` int(1) DEFAULT NULL,
  `rechnung_name` varchar(255) DEFAULT NULL,
  `rechnung_strasse` varchar(255) DEFAULT NULL,
  `rechnung_ort` varchar(255) DEFAULT NULL,
  `rechnung_plz` varchar(64) DEFAULT NULL,
  `rechnung_abteilung` varchar(255) DEFAULT NULL,
  `rechnung_land` varchar(255) DEFAULT NULL,
  `rechnung_email` varchar(255) DEFAULT NULL,
  `rechnung_periode` int(11) DEFAULT NULL,
  `rechnung_anzahlpapier` int(11) DEFAULT NULL,
  `rechnung_permail` int(1) DEFAULT NULL,
  `webid` varchar(1024) NOT NULL,
  `portofrei_aktiv` decimal(10,2) DEFAULT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `objektname` varchar(64) NOT NULL,
  `objekttyp` varchar(64) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `objektname2` varchar(64) NOT NULL,
  `objekttyp2` varchar(64) NOT NULL,
  `parameter2` varchar(255) NOT NULL,
  `objektname3` varchar(64) NOT NULL,
  `objekttyp3` varchar(64) NOT NULL,
  `parameter3` varchar(255) NOT NULL,
  `kategorie` int(1) NOT NULL DEFAULT 0,
  `aktiv` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gruppen_kategorien`
--

DROP TABLE IF EXISTS `gruppen_kategorien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gruppen_kategorien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gruppenmapping`
--

DROP TABLE IF EXISTS `gruppenmapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gruppenmapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `parameter1` varchar(255) NOT NULL DEFAULT '0',
  `parameter2` varchar(255) NOT NULL DEFAULT '0',
  `parameter3` varchar(255) NOT NULL DEFAULT '0',
  `von` date NOT NULL,
  `bis` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gruppe` (`gruppe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gruppenrechnung_auswahl`
--

DROP TABLE IF EXISTS `gruppenrechnung_auswahl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gruppenrechnung_auswahl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lieferschein` int(11) NOT NULL DEFAULT 0,
  `auftrag` int(11) NOT NULL DEFAULT 0,
  `user` int(11) NOT NULL DEFAULT 0,
  `auswahl` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `lieferschein` (`lieferschein`),
  KEY `auftrag` (`auftrag`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gutschrift`
--

DROP TABLE IF EXISTS `gutschrift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gutschrift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `anlegeart` varchar(255) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `rechnung` int(11) NOT NULL,
  `rechnungid` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `freitext` text NOT NULL,
  `internebemerkung` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `adresse` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `ustbrief` int(11) NOT NULL,
  `ustbrief_eingang` int(11) NOT NULL,
  `ustbrief_eingang_am` date NOT NULL,
  `ust_befreit` int(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(64) DEFAULT NULL,
  `lieferschein` int(11) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `buchhaltung` varchar(255) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungsstatus` varchar(255) NOT NULL,
  `ist` decimal(18,2) NOT NULL DEFAULT 0.00,
  `soll` decimal(18,2) NOT NULL DEFAULT 0.00,
  `zahlungszieltage` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `zahlungszielskonto` decimal(10,2) NOT NULL,
  `gesamtsumme` decimal(10,4) NOT NULL,
  `bank_inhaber` varchar(255) NOT NULL,
  `bank_institut` varchar(255) NOT NULL,
  `bank_blz` int(11) NOT NULL,
  `bank_konto` int(11) NOT NULL,
  `kreditkarte_typ` varchar(255) NOT NULL,
  `kreditkarte_inhaber` varchar(255) NOT NULL,
  `kreditkarte_nummer` varchar(255) NOT NULL,
  `kreditkarte_pruefnummer` varchar(255) NOT NULL,
  `kreditkarte_monat` int(11) NOT NULL,
  `kreditkarte_jahr` int(11) NOT NULL,
  `paypalaccount` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `inbearbeitung` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dta_datei_verband` int(11) NOT NULL DEFAULT 0,
  `manuell_vorabbezahlt` date DEFAULT NULL,
  `manuell_vorabbezahlt_hinweis` varchar(128) NOT NULL,
  `nicht_umsatzmindernd` tinyint(1) NOT NULL DEFAULT 0,
  `dta_datei` int(11) NOT NULL DEFAULT 0,
  `deckungsbeitragcalc` tinyint(1) NOT NULL DEFAULT 0,
  `deckungsbeitrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `erloes_netto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `umsatz_netto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `vertriebid` int(11) DEFAULT NULL,
  `aktion` varchar(64) NOT NULL,
  `vertrieb` varchar(255) NOT NULL,
  `provision` decimal(10,2) DEFAULT NULL,
  `provision_summe` decimal(10,2) DEFAULT NULL,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `ihrebestellnummer` varchar(255) DEFAULT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `realrabatt` decimal(10,2) DEFAULT NULL,
  `rabatt` decimal(10,2) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(255) NOT NULL DEFAULT 'EUR',
  `keinsteuersatz` int(1) DEFAULT NULL,
  `stornorechnung` int(1) DEFAULT NULL,
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviert` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviertversion` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(255) NOT NULL DEFAULT 'firma',
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `lieferid` int(11) NOT NULL DEFAULT 0,
  `ansprechpartnerid` int(11) NOT NULL DEFAULT 0,
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `zuarchivieren` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `angelegtam` datetime DEFAULT NULL,
  `ansprechpartner` varchar(255) DEFAULT NULL,
  `sprache` varchar(32) NOT NULL,
  `gln` varchar(64) NOT NULL,
  `deliverythresholdvatid` varchar(64) NOT NULL,
  `bearbeiterid` int(11) DEFAULT NULL,
  `kurs` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `ohne_artikeltext` int(1) DEFAULT NULL,
  `anzeigesteuer` tinyint(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `bodyzusatz` text NOT NULL,
  `lieferbedingung` text NOT NULL,
  `titel` varchar(64) NOT NULL,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontoberechnet` tinyint(1) NOT NULL DEFAULT 0,
  `extsoll` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bundesstaat` varchar(32) NOT NULL,
  `kundennummer_buchhaltung` varchar(32) NOT NULL,
  `storage_country` varchar(3) NOT NULL,
  `abweichendebezeichnung` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`),
  KEY `adresse` (`adresse`),
  KEY `vertriebid` (`vertriebid`),
  KEY `status` (`status`),
  KEY `datum` (`datum`),
  KEY `belegnr` (`belegnr`),
  KEY `versandart` (`versandart`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gutschrift_position`
--

DROP TABLE IF EXISTS `gutschrift_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gutschrift_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `gutschrift` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `waehrung` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `status` varchar(64) NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `explodiert_parent` int(11) NOT NULL DEFAULT 0,
  `explodiert_parent_artikel` int(11) NOT NULL DEFAULT 0,
  `keinrabatterlaubt` int(1) DEFAULT NULL,
  `grundrabatt` decimal(10,2) DEFAULT NULL,
  `rabattsync` int(1) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `einheit` varchar(255) NOT NULL,
  `rabatt` decimal(10,2) NOT NULL,
  `zolltarifnummer` varchar(128) NOT NULL DEFAULT '0',
  `herkunftsland` varchar(128) NOT NULL DEFAULT '0',
  `artikelnummerkunde` varchar(128) NOT NULL,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  `auftrag_position_id` int(11) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(1024) DEFAULT NULL,
  `erloese` varchar(8) DEFAULT NULL,
  `erloesefestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `einkaufspreiswaehrung` varchar(8) NOT NULL,
  `einkaufspreis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `einkaufspreisurspruenglich` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `einkaufspreisid` int(11) NOT NULL DEFAULT 0,
  `ekwaehrung` varchar(8) NOT NULL,
  `deckungsbeitrag` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `formelmenge` varchar(255) NOT NULL,
  `formelpreis` varchar(255) NOT NULL,
  `ohnepreis` int(1) NOT NULL DEFAULT 0,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_gesamt` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_gesamt` decimal(14,4) DEFAULT NULL,
  `steuerbetrag` decimal(14,4) DEFAULT NULL,
  `skontosperre` tinyint(1) NOT NULL DEFAULT 0,
  `ausblenden_im_pdf` tinyint(1) DEFAULT 0,
  `umsatz_netto_einzeln` decimal(14,4) DEFAULT NULL,
  `umsatz_netto_gesamt` decimal(14,4) DEFAULT NULL,
  `umsatz_brutto_einzeln` decimal(14,4) DEFAULT NULL,
  `umsatz_brutto_gesamt` decimal(14,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gutschrift` (`gutschrift`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gutschrift_protokoll`
--

DROP TABLE IF EXISTS `gutschrift_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gutschrift_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gutschrift` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gutschrift` (`gutschrift`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hook`
--

DROP TABLE IF EXISTS `hook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `alias` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `parametercount` int(11) NOT NULL DEFAULT 1,
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hook_action`
--

DROP TABLE IF EXISTS `hook_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hook_module` int(11) NOT NULL DEFAULT 0,
  `action` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hook_layout`
--

DROP TABLE IF EXISTS `hook_layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `dokumenttyp` varchar(64) NOT NULL,
  `module` varchar(64) NOT NULL,
  `funktion` varchar(64) NOT NULL,
  `typ` varchar(64) NOT NULL,
  `block` varchar(64) NOT NULL,
  `blocktyp` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hook_menu`
--

DROP TABLE IF EXISTS `hook_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hook_menu_register`
--

DROP TABLE IF EXISTS `hook_menu_register`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook_menu_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hook_menu` int(11) NOT NULL DEFAULT 0,
  `module` varchar(64) NOT NULL,
  `funktion` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hook_module`
--

DROP TABLE IF EXISTS `hook_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hook_navigation`
--

DROP TABLE IF EXISTS `hook_navigation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook_navigation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `first` varchar(64) NOT NULL,
  `sec` varchar(64) NOT NULL,
  `aftersec` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hook_register`
--

DROP TABLE IF EXISTS `hook_register`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hook_action` int(11) NOT NULL DEFAULT 0,
  `function` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `position` int(11) NOT NULL DEFAULT 0,
  `hook` int(11) NOT NULL DEFAULT 0,
  `module` varchar(64) NOT NULL,
  `module_parameter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `hook` (`hook`)
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `importmasterdata`
--

DROP TABLE IF EXISTS `importmasterdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `importmasterdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `template_id` int(11) NOT NULL DEFAULT 0,
  `count_rows` int(11) NOT NULL DEFAULT 0,
  `imported_rows` int(11) NOT NULL DEFAULT 0,
  `filename` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'created',
  `message` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `user_id` (`user_id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `importvorlage`
--

DROP TABLE IF EXISTS `importvorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `importvorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) DEFAULT NULL,
  `ziel` varchar(255) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  `fields` text DEFAULT NULL,
  `letzterimport` datetime DEFAULT NULL,
  `mitarbeiterletzterimport` varchar(255) DEFAULT NULL,
  `importtrennzeichen` varchar(255) DEFAULT NULL,
  `importerstezeilenummer` int(11) DEFAULT NULL,
  `importdatenmaskierung` varchar(255) DEFAULT NULL,
  `importzeichensatz` varchar(255) DEFAULT NULL,
  `utf8decode` tinyint(1) NOT NULL DEFAULT 1,
  `charset` varchar(32) NOT NULL DEFAULT 'utf8',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `importvorlage_log`
--

DROP TABLE IF EXISTS `importvorlage_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `importvorlage_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `importvorlage` int(11) DEFAULT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) DEFAULT NULL,
  `tabelle` varchar(255) DEFAULT NULL,
  `datensatz` int(11) DEFAULT NULL,
  `ersterdatensatz` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inhalt`
--

DROP TABLE IF EXISTS `inhalt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inhalt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sprache` varchar(255) NOT NULL,
  `inhalt` varchar(255) NOT NULL,
  `kurztext` text NOT NULL,
  `html` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(512) NOT NULL,
  `keywords` varchar(512) NOT NULL,
  `inhaltstyp` varchar(255) NOT NULL,
  `sichtbarbis` datetime NOT NULL,
  `datum` date NOT NULL,
  `aktiv` int(1) NOT NULL,
  `shop` int(11) NOT NULL,
  `template` varchar(255) DEFAULT NULL,
  `finalparse` varchar(255) NOT NULL,
  `navigation` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventur`
--

DROP TABLE IF EXISTS `inventur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `auftrag` varchar(255) NOT NULL,
  `auftragid` int(11) NOT NULL,
  `freitext` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `adresse` int(11) NOT NULL,
  `mitarbeiter` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(255) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `versand` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `inbearbeitung_user` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `noprice` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventur_position`
--

DROP TABLE IF EXISTS `inventur_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventur_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventur` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `sort` int(10) NOT NULL,
  `bemerkung` text NOT NULL,
  `preis` decimal(10,4) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `inventur` (`inventur`,`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventur_protokoll`
--

DROP TABLE IF EXISTS `inventur_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventur_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventur` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inventur` (`inventur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_template`
--

DROP TABLE IF EXISTS `item_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jqcalendar`
--

DROP TABLE IF EXISTS `jqcalendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jqcalendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `ort` varchar(255) NOT NULL,
  `von` datetime NOT NULL,
  `bis` datetime NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kalender`
--

DROP TABLE IF EXISTS `kalender`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kalender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT 'default',
  `farbe` varchar(15) NOT NULL DEFAULT '3300ff',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kalender_event`
--

DROP TABLE IF EXISTS `kalender_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kalender_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kalender` int(11) NOT NULL DEFAULT 0,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` longtext DEFAULT NULL,
  `von` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bis` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `allDay` tinyint(1) NOT NULL DEFAULT 0,
  `color` varchar(7) NOT NULL DEFAULT '#6f93db',
  `public` int(1) NOT NULL DEFAULT 0,
  `ort` text DEFAULT NULL,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `adresseintern` int(11) NOT NULL DEFAULT 0,
  `angelegtvon` int(11) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `erinnerung` int(1) DEFAULT NULL,
  `ansprechpartner_id` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(32) NOT NULL,
  `uri` text DEFAULT NULL,
  `uid` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kalender_gruppen`
--

DROP TABLE IF EXISTS `kalender_gruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kalender_gruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `farbe` varchar(8) NOT NULL,
  `ausblenden` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kalender_gruppen_mitglieder`
--

DROP TABLE IF EXISTS `kalender_gruppen_mitglieder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kalender_gruppen_mitglieder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kalendergruppe` int(11) NOT NULL,
  `benutzergruppe` int(11) NOT NULL,
  `adresse` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kalendergruppe` (`kalendergruppe`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kalender_temp`
--

DROP TABLE IF EXISTS `kalender_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kalender_temp` (
  `tId` int(11) NOT NULL,
  `eId` int(11) NOT NULL,
  `szelle` varchar(15) NOT NULL,
  `nanzbelegt` int(11) NOT NULL,
  `ndatum` varchar(8) NOT NULL,
  `nbelegt` float NOT NULL,
  `nanzspalten` int(11) NOT NULL DEFAULT 0,
  `nposbelegt` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kalender_user`
--

DROP TABLE IF EXISTS `kalender_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kalender_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `event` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kasse`
--

DROP TABLE IF EXISTS `kasse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kasse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `auswahl` varchar(255) NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `adresse` int(11) NOT NULL,
  `grund` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `steuergruppe` int(11) NOT NULL,
  `exportiert` int(1) NOT NULL,
  `exportiert_datum` date NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `konto` int(11) NOT NULL DEFAULT 1,
  `nummer` int(11) NOT NULL DEFAULT 0,
  `wert` decimal(10,2) NOT NULL DEFAULT 0.00,
  `steuersatz` decimal(10,2) NOT NULL DEFAULT 0.00,
  `betrag_brutto_normal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `betrag_steuer_normal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `betrag_brutto_ermaessigt` decimal(10,2) NOT NULL DEFAULT 0.00,
  `betrag_steuer_ermaessigt` decimal(10,2) NOT NULL DEFAULT 0.00,
  `betrag_brutto_befreit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `betrag_steuer_befreit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tagesabschluss` tinyint(1) NOT NULL DEFAULT 0,
  `storniert` tinyint(1) NOT NULL DEFAULT 0,
  `storniert_grund` varchar(255) NOT NULL,
  `storniert_bearbeiter` varchar(64) NOT NULL,
  `sachkonto` varchar(64) NOT NULL,
  `bemerkung` text NOT NULL,
  `belegdatum` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datum` (`datum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kasse_log`
--

DROP TABLE IF EXISTS `kasse_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kasse_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kasseid` int(11) NOT NULL DEFAULT 0,
  `user` int(11) NOT NULL DEFAULT 0,
  `beschreibung` varchar(255) NOT NULL,
  `betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wert` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kommissionierung`
--

DROP TABLE IF EXISTS `kommissionierung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kommissionierung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `bearbeiter` varchar(255) NOT NULL,
  `user` int(11) NOT NULL DEFAULT 0,
  `kommentar` varchar(255) NOT NULL,
  `abgeschlossen` tinyint(1) NOT NULL DEFAULT 0,
  `improzess` tinyint(1) NOT NULL DEFAULT 0,
  `bezeichnung` varchar(40) NOT NULL,
  `skipconfirmboxscan` tinyint(1) NOT NULL DEFAULT -1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kommissionierung_position`
--

DROP TABLE IF EXISTS `kommissionierung_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kommissionierung_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `kommissionierung` int(11) NOT NULL DEFAULT 0,
  `ausgeblendet` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kommissionierung_position_ls`
--

DROP TABLE IF EXISTS `kommissionierung_position_ls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kommissionierung_position_ls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kommissionierung` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `lieferschein` int(11) NOT NULL DEFAULT 0,
  `ausgeblendet` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kommissionskonsignationslager_positionen`
--

DROP TABLE IF EXISTS `kommissionskonsignationslager_positionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kommissionskonsignationslager_positionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `user` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `lieferschein` int(11) NOT NULL DEFAULT 0,
  `rechnung` int(11) NOT NULL DEFAULT 0,
  `lieferschein_position` int(11) NOT NULL DEFAULT 0,
  `rechnung_position` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `auswahl` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `ausgelagert` decimal(14,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `konfiguration`
--

DROP TABLE IF EXISTS `konfiguration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `konfiguration` (
  `name` varchar(255) NOT NULL,
  `wert` text NOT NULL,
  `adresse` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `konten`
--

DROP TABLE IF EXISTS `konten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `konten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `kurzbezeichnung` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `erstezeile` text NOT NULL,
  `datevkonto` int(10) NOT NULL,
  `blz` varchar(255) NOT NULL,
  `konto` varchar(255) NOT NULL,
  `swift` varchar(255) NOT NULL,
  `iban` varchar(255) NOT NULL,
  `lastschrift` int(1) NOT NULL,
  `hbci` int(1) NOT NULL,
  `hbcikennung` text NOT NULL,
  `inhaber` varchar(255) NOT NULL,
  `aktiv` int(1) NOT NULL,
  `keineemail` int(1) NOT NULL,
  `firma` int(1) NOT NULL,
  `schreibbar` int(1) NOT NULL DEFAULT 1,
  `importletztenzeilenignorieren` int(11) NOT NULL DEFAULT 0,
  `liveimport` text DEFAULT NULL,
  `liveimport_passwort` text DEFAULT NULL,
  `liveimport_online` int(1) DEFAULT NULL,
  `importtrennzeichen` varchar(255) DEFAULT NULL,
  `codierung` varchar(255) DEFAULT NULL,
  `importerstezeilenummer` int(11) DEFAULT NULL,
  `importdatenmaskierung` varchar(255) DEFAULT NULL,
  `importnullbytes` int(1) NOT NULL,
  `glaeubiger` varchar(64) DEFAULT NULL,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `saldo_summieren` int(1) NOT NULL DEFAULT 0,
  `saldo_betrag` decimal(18,2) NOT NULL DEFAULT 0.00,
  `saldo_datum` date DEFAULT NULL,
  `importfelddatum` varchar(255) DEFAULT NULL,
  `importfelddatumformat` varchar(255) DEFAULT NULL,
  `importfelddatumformatausgabe` varchar(255) DEFAULT NULL,
  `importfeldbetrag` varchar(255) DEFAULT NULL,
  `importfeldbetragformat` varchar(255) DEFAULT NULL,
  `importfeldbuchungstext` varchar(255) DEFAULT NULL,
  `importfeldbuchungstextformat` varchar(255) DEFAULT NULL,
  `importfeldwaehrung` varchar(255) DEFAULT NULL,
  `importfeldwaehrungformat` varchar(255) DEFAULT NULL,
  `importfeldhabensollkennung` varchar(10) NOT NULL,
  `importfeldkennunghaben` varchar(10) NOT NULL,
  `importfeldkennungsoll` varchar(10) NOT NULL,
  `importextrahabensoll` tinyint(1) NOT NULL DEFAULT 0,
  `importfeldhaben` varchar(10) NOT NULL,
  `importfeldsoll` varchar(10) NOT NULL,
  `cronjobaktiv` tinyint(1) NOT NULL DEFAULT 0,
  `cronjobverbuchen` tinyint(1) NOT NULL DEFAULT 0,
  `last_import` timestamp NOT NULL DEFAULT '1979-01-01 23:00:00',
  `importperiode_in_hours` int(11) NOT NULL DEFAULT 8,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kontoauszuege`
--

DROP TABLE IF EXISTS `kontoauszuege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontoauszuege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `konto` int(11) NOT NULL,
  `buchung` date NOT NULL,
  `originalbuchung` date NOT NULL,
  `vorgang` text NOT NULL,
  `originalvorgang` text NOT NULL,
  `soll` decimal(10,2) NOT NULL,
  `originalsoll` decimal(10,2) NOT NULL,
  `haben` decimal(10,2) NOT NULL,
  `originalhaben` decimal(10,2) NOT NULL,
  `gebuehr` decimal(10,2) NOT NULL,
  `originalgebuehr` decimal(10,2) NOT NULL,
  `waehrung` varchar(255) NOT NULL,
  `originalwaehrung` varchar(255) NOT NULL,
  `fertig` int(1) NOT NULL,
  `datev_abgeschlossen` int(1) NOT NULL,
  `buchungstext` varchar(255) NOT NULL,
  `gegenkonto` varchar(255) NOT NULL,
  `belegfeld1` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `mailbenachrichtigung` int(11) NOT NULL,
  `pruefsumme` varchar(255) NOT NULL,
  `kostenstelle` varchar(10) NOT NULL,
  `importgroup` bigint(20) DEFAULT NULL,
  `diff` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `diffangelegt` timestamp NULL DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  `importfehler` int(1) DEFAULT NULL,
  `parent` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `doctype` varchar(64) NOT NULL,
  `doctypeid` int(11) NOT NULL,
  `vorauswahltyp` varchar(64) NOT NULL,
  `vorauswahlparameter` varchar(255) NOT NULL,
  `klaerfall` tinyint(1) NOT NULL DEFAULT 0,
  `klaergrund` varchar(255) NOT NULL,
  `bezugtyp` varchar(64) NOT NULL,
  `bezugparameter` varchar(255) NOT NULL,
  `vorauswahlvorschlag` int(11) NOT NULL DEFAULT 0,
  `importdatum` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `konto` (`konto`),
  KEY `parent` (`parent`),
  KEY `gegenkonto` (`gegenkonto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kontoauszuege_zahlungsausgang`
--

DROP TABLE IF EXISTS `kontoauszuege_zahlungsausgang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontoauszuege_zahlungsausgang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `datum` date NOT NULL,
  `objekt` varchar(255) NOT NULL,
  `parameter` int(11) NOT NULL,
  `kontoauszuege` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `abgeschlossen` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kontoauszuege` (`kontoauszuege`),
  KEY `parameter` (`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kontoauszuege_zahlungseingang`
--

DROP TABLE IF EXISTS `kontoauszuege_zahlungseingang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontoauszuege_zahlungseingang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `datum` date NOT NULL,
  `objekt` varchar(255) NOT NULL,
  `parameter` int(11) NOT NULL,
  `kontoauszuege` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `abgeschlossen` int(11) NOT NULL,
  `parameter2` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kontoauszuege` (`kontoauszuege`),
  KEY `parameter` (`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kontorahmen`
--

DROP TABLE IF EXISTS `kontorahmen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontorahmen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sachkonto` varchar(16) NOT NULL,
  `beschriftung` varchar(128) DEFAULT NULL,
  `bemerkung` text NOT NULL,
  `ausblenden` tinyint(1) NOT NULL DEFAULT 0,
  `art` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kontorahmen_checked`
--

DROP TABLE IF EXISTS `kontorahmen_checked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontorahmen_checked` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kontorahmen` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kopiebelegempfaenger`
--

DROP TABLE IF EXISTS `kopiebelegempfaenger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kopiebelegempfaenger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `belegtyp` varchar(255) NOT NULL,
  `art` varchar(255) NOT NULL,
  `projekt` varchar(255) NOT NULL,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `empfaenger_email` varchar(255) NOT NULL,
  `empfaenger_name` varchar(255) NOT NULL,
  `drucker` int(11) NOT NULL,
  `anzahl_ausdrucke` int(11) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 0,
  `autoversand` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kostenstelle`
--

DROP TABLE IF EXISTS `kostenstelle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kostenstelle` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `projekt` varchar(255) NOT NULL,
  `verantwortlicher` varchar(255) NOT NULL,
  `logdatei` varchar(255) NOT NULL,
  `nummer` int(11) NOT NULL,
  `beschreibung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kostenstelle_buchung`
--

DROP TABLE IF EXISTS `kostenstelle_buchung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kostenstelle_buchung` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kostenstelle` int(10) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `datum` varchar(255) NOT NULL,
  `buchungstext` varchar(255) NOT NULL,
  `sonstiges` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kostenstellen`
--

DROP TABLE IF EXISTS `kostenstellen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kostenstellen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nummer` varchar(20) DEFAULT NULL,
  `beschreibung` varchar(512) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kundevorlage`
--

DROP TABLE IF EXISTS `kundevorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kundevorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungszieltage` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `zahlungszielskonto` int(11) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_automatic`
--

DROP TABLE IF EXISTS `label_automatic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `label_automatic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label_type_id` int(11) NOT NULL DEFAULT 0,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `action` varchar(64) NOT NULL,
  `selection` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_group`
--

DROP TABLE IF EXISTS `label_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `label_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_table` varchar(64) NOT NULL,
  `title` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_table` (`group_table`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_reference`
--

DROP TABLE IF EXISTS `label_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `label_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label_type_id` int(11) unsigned NOT NULL,
  `reference_table` varchar(64) NOT NULL,
  `reference_id` int(11) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `label_type_id` (`label_type_id`,`reference_table`,`reference_id`),
  UNIQUE KEY `label_type_id_2` (`label_type_id`,`reference_table`,`reference_id`),
  UNIQUE KEY `label_type_id_3` (`label_type_id`,`reference_table`,`reference_id`),
  UNIQUE KEY `label_type_id_4` (`label_type_id`,`reference_table`,`reference_id`),
  UNIQUE KEY `label_type_id_5` (`label_type_id`,`reference_table`,`reference_id`),
  UNIQUE KEY `label_type_id_6` (`label_type_id`,`reference_table`,`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_type`
--

DROP TABLE IF EXISTS `label_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `label_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label_group_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(24) NOT NULL,
  `title` varchar(64) NOT NULL,
  `hexcolor` varchar(7) NOT NULL DEFAULT '#ffffff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `laender`
--

DROP TABLE IF EXISTS `laender`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `laender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iso` varchar(3) NOT NULL,
  `iso3` varchar(3) NOT NULL,
  `num_code` varchar(3) NOT NULL,
  `bezeichnung_de` varchar(255) NOT NULL,
  `bezeichnung_en` varchar(255) NOT NULL,
  `eu` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager`
--

DROP TABLE IF EXISTS `lager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `manuell` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `geloescht` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `projekt` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager_bewegung`
--

DROP TABLE IF EXISTS `lager_bewegung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager_bewegung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lager_platz` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `vpe` varchar(255) NOT NULL,
  `eingang` int(1) NOT NULL,
  `zeit` datetime NOT NULL,
  `referenz` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `adresse` int(11) DEFAULT NULL,
  `bestand` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `permanenteinventur` tinyint(1) NOT NULL DEFAULT 0,
  `paketannahme` int(11) DEFAULT NULL,
  `doctype` varchar(32) NOT NULL,
  `doctypeid` int(11) NOT NULL DEFAULT 0,
  `vpeid` int(11) NOT NULL DEFAULT 0,
  `is_interim` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager_charge`
--

DROP TABLE IF EXISTS `lager_charge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager_charge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `charge` varchar(1024) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `artikel` int(11) DEFAULT NULL,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `lager_platz` int(11) DEFAULT NULL,
  `zwischenlagerid` int(11) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lager_platz` (`lager_platz`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager_differenzen`
--

DROP TABLE IF EXISTS `lager_differenzen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager_differenzen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) DEFAULT NULL,
  `eingang` decimal(10,4) DEFAULT NULL,
  `ausgang` decimal(10,4) DEFAULT NULL,
  `berechnet` decimal(10,4) DEFAULT NULL,
  `bestand` decimal(10,4) DEFAULT NULL,
  `differenz` decimal(10,4) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `lager_platz` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager_mindesthaltbarkeitsdatum`
--

DROP TABLE IF EXISTS `lager_mindesthaltbarkeitsdatum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager_mindesthaltbarkeitsdatum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `mhddatum` date DEFAULT NULL,
  `artikel` int(11) DEFAULT NULL,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `lager_platz` int(11) DEFAULT NULL,
  `zwischenlagerid` int(11) DEFAULT NULL,
  `charge` varchar(1024) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lager_platz` (`lager_platz`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager_platz`
--

DROP TABLE IF EXISTS `lager_platz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager_platz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lager` int(11) NOT NULL,
  `kurzbezeichnung` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `projekt` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `geloescht` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `autolagersperre` int(1) NOT NULL DEFAULT 0,
  `verbrauchslager` int(1) NOT NULL DEFAULT 0,
  `sperrlager` int(1) NOT NULL DEFAULT 0,
  `laenge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `breite` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hoehe` decimal(10,2) NOT NULL DEFAULT 0.00,
  `poslager` int(1) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `abckategorie` varchar(1) NOT NULL,
  `regalart` varchar(100) NOT NULL,
  `rownumber` int(11) NOT NULL DEFAULT 0,
  `allowproduction` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `lager` (`lager`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager_platz_inhalt`
--

DROP TABLE IF EXISTS `lager_platz_inhalt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager_platz_inhalt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lager_platz` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `vpe` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `bestellung` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `inventur` decimal(14,4) DEFAULT NULL,
  `lager_platz_vpe` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`),
  KEY `lager_platz` (`lager_platz`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager_platz_vpe`
--

DROP TABLE IF EXISTS `lager_platz_vpe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager_platz_vpe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lager_platz` int(11) DEFAULT NULL,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `inventur` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `breite` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hoehe` decimal(10,2) NOT NULL DEFAULT 0.00,
  `laenge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gewicht` decimal(10,2) NOT NULL DEFAULT 0.00,
  `menge2` int(11) NOT NULL DEFAULT 0,
  `breite2` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hoehe2` decimal(10,2) NOT NULL DEFAULT 0.00,
  `laenge2` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gewicht2` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager_reserviert`
--

DROP TABLE IF EXISTS `lager_reserviert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager_reserviert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `grund` varchar(255) NOT NULL,
  `objekt` varchar(255) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `datum` date NOT NULL,
  `reserviertdatum` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `posid` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `lager` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`,`artikel`),
  KEY `objekt` (`objekt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lager_seriennummern`
--

DROP TABLE IF EXISTS `lager_seriennummern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lager_seriennummern` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) DEFAULT NULL,
  `lager_platz` int(11) DEFAULT NULL,
  `zwischenlagerid` int(11) DEFAULT NULL,
  `seriennummer` text DEFAULT NULL,
  `charge` varchar(1024) DEFAULT NULL,
  `mhddatum` date DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lagermindestmengen`
--

DROP TABLE IF EXISTS `lagermindestmengen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lagermindestmengen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `datumvon` date DEFAULT NULL,
  `datumbis` date DEFAULT NULL,
  `max_menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`,`lager_platz`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lagerstueckliste`
--

DROP TABLE IF EXISTS `lagerstueckliste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lagerstueckliste` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager` int(11) NOT NULL DEFAULT 0,
  `sofortexplodieren` tinyint(1) NOT NULL DEFAULT 0,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lagerwert`
--

DROP TABLE IF EXISTS `lagerwert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lagerwert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `lager` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `gewicht` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `volumen` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `inventurwert` decimal(18,8) DEFAULT NULL,
  `preis_letzterek` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `preis_kalkulierterek` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `letzte_bewegung` datetime DEFAULT NULL,
  `waehrungkalk` varchar(16) NOT NULL,
  `waehrungletzt` varchar(16) NOT NULL,
  `kurskalk` decimal(19,8) NOT NULL DEFAULT 0.00000000,
  `kursletzt` decimal(19,8) NOT NULL DEFAULT 0.00000000,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`),
  KEY `datum` (`datum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `layouttemplate_attachment`
--

DROP TABLE IF EXISTS `layouttemplate_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `layouttemplate_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL,
  `articlecategory_id` int(11) NOT NULL DEFAULT 0,
  `group_id` int(11) NOT NULL DEFAULT 0,
  `layouttemplate_id` int(11) NOT NULL DEFAULT 0,
  `language` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `parameter` int(11) NOT NULL DEFAULT 0,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `layouttemplate_attachment_items`
--

DROP TABLE IF EXISTS `layouttemplate_attachment_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `layouttemplate_attachment_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object` varchar(255) NOT NULL,
  `parameter_id` int(11) NOT NULL DEFAULT 0,
  `layouttemplate_id` int(11) NOT NULL DEFAULT 0,
  `file_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `layoutvorlagen`
--

DROP TABLE IF EXISTS `layoutvorlagen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `layoutvorlagen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `typ` varchar(128) NOT NULL,
  `pdf_hintergrund` longblob NOT NULL,
  `format` varchar(128) NOT NULL,
  `kategorie` varchar(128) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `layoutvorlagen_positionen`
--

DROP TABLE IF EXISTS `layoutvorlagen_positionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `layoutvorlagen_positionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layoutvorlage` int(11) NOT NULL DEFAULT 0,
  `name` varchar(128) NOT NULL,
  `beschreibung` varchar(128) NOT NULL,
  `typ` varchar(128) NOT NULL,
  `position_typ` varchar(128) NOT NULL,
  `position_x` double NOT NULL DEFAULT 1,
  `position_y` double NOT NULL DEFAULT 1,
  `position_parent` int(11) NOT NULL DEFAULT 0,
  `breite` double NOT NULL DEFAULT 1,
  `hoehe` double NOT NULL DEFAULT 1,
  `schrift_art` varchar(128) NOT NULL,
  `zeilen_hoehe` double NOT NULL DEFAULT 5,
  `schrift_groesse` double NOT NULL DEFAULT 1,
  `schrift_farbe` varchar(128) NOT NULL,
  `schrift_align` varchar(128) NOT NULL,
  `hintergrund_farbe` varchar(128) NOT NULL,
  `rahmen` varchar(128) NOT NULL,
  `rahmen_farbe` varchar(128) NOT NULL,
  `sichtbar` tinyint(1) NOT NULL DEFAULT 1,
  `inhalt_deutsch` text NOT NULL,
  `inhalt_englisch` text NOT NULL,
  `bild_deutsch` longblob NOT NULL,
  `bild_englisch` longblob NOT NULL,
  `schrift_fett` tinyint(1) NOT NULL DEFAULT 0,
  `schrift_kursiv` tinyint(1) NOT NULL DEFAULT 0,
  `schrift_underline` tinyint(1) NOT NULL DEFAULT 0,
  `bild_deutsch_typ` varchar(5) NOT NULL,
  `bild_englisch_typ` varchar(5) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `zeichenbegrenzung` tinyint(1) NOT NULL DEFAULT 0,
  `zeichenbegrenzung_anzahl` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `layoutvorlage` (`layoutvorlage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferadressen`
--

DROP TABLE IF EXISTS `lieferadressen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferadressen` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `typ` varchar(255) NOT NULL,
  `sprache` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sonstiges` text NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `steuer` varchar(255) NOT NULL,
  `adresse` varchar(10) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ansprechpartner` varchar(255) DEFAULT NULL,
  `standardlieferadresse` tinyint(1) NOT NULL DEFAULT 0,
  `interne_bemerkung` text DEFAULT NULL,
  `hinweis` text DEFAULT NULL,
  `gln` varchar(32) NOT NULL,
  `ustid` varchar(32) NOT NULL,
  `lieferbedingung` text NOT NULL,
  `ust_befreit` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferantengutschrift`
--

DROP TABLE IF EXISTS `lieferantengutschrift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferantengutschrift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usereditid` int(11) NOT NULL,
  `belegnr` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `datum` date DEFAULT NULL,
  `status_beleg` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `schreibschutz` tinyint(1) NOT NULL DEFAULT 0,
  `rechnung` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `zahlbarbis` date NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `skonto` decimal(10,2) NOT NULL,
  `skontobis` date NOT NULL,
  `freigabe` int(1) NOT NULL,
  `freigabemitarbeiter` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `adresse` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `status` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `bezahlt` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `waehrung` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'EUR',
  `zahlungsweise` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `eingangsdatum` date NOT NULL,
  `rechnungsdatum` date DEFAULT NULL,
  `rechnungsfreigabe` tinyint(1) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `beschreibung` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `sachkonto` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `internebemerkung` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `ust_befreit` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferantengutschrift_position`
--

DROP TABLE IF EXISTS `lieferantengutschrift_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferantengutschrift_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lieferantengutschrift` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung` int(11) NOT NULL DEFAULT 0,
  `nummer` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `waehrung` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `einheit` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `vpe` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `bezeichnung` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `umsatzsteuer` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `beschreibung` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `lieferdatum` date DEFAULT NULL,
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(1024) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `kostenstelle` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `preis` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `verbindlichkeit_position` int(11) NOT NULL DEFAULT 0,
  `kontorahmen` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `lieferantengutschrift` (`lieferantengutschrift`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferantengutschrift_protokoll`
--

DROP TABLE IF EXISTS `lieferantengutschrift_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferantengutschrift_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lieferantengutschrift` int(11) NOT NULL DEFAULT 0,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `grund` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lieferantengutschrift` (`lieferantengutschrift`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferantvorlage`
--

DROP TABLE IF EXISTS `lieferantvorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferantvorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `kundennummer` varchar(255) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungszieltage` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `zahlungszielskonto` int(11) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferbedingungen`
--

DROP TABLE IF EXISTS `lieferbedingungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferbedingungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lieferbedingungen` text NOT NULL,
  `kennzeichen` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferschein`
--

DROP TABLE IF EXISTS `lieferschein`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferschein` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `lieferscheinart` varchar(255) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `auftrag` varchar(255) NOT NULL,
  `auftragid` int(11) NOT NULL,
  `freitext` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `adresse` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(64) DEFAULT NULL,
  `versandart` varchar(255) NOT NULL,
  `versand` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `inbearbeitung_user` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `vertriebid` int(11) DEFAULT NULL,
  `vertrieb` varchar(255) NOT NULL,
  `ust_befreit` int(1) NOT NULL,
  `ihrebestellnummer` varchar(255) DEFAULT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lieferantenretoure` tinyint(1) NOT NULL DEFAULT 0,
  `lieferantenretoureinfo` text NOT NULL,
  `lieferant` int(11) NOT NULL DEFAULT 0,
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviert` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviertversion` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(255) NOT NULL DEFAULT 'firma',
  `internebemerkung` text DEFAULT NULL,
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `lieferid` int(11) NOT NULL DEFAULT 0,
  `ansprechpartnerid` int(11) NOT NULL DEFAULT 0,
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `projektfiliale_eingelagert` tinyint(1) NOT NULL DEFAULT 0,
  `zuarchivieren` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `angelegtam` datetime DEFAULT NULL,
  `kommissionierung` int(11) NOT NULL DEFAULT 0,
  `sprache` varchar(32) NOT NULL,
  `bundesland` varchar(64) NOT NULL,
  `gln` varchar(64) NOT NULL,
  `rechnungid` int(11) NOT NULL DEFAULT 0,
  `bearbeiterid` int(11) DEFAULT NULL,
  `keinerechnung` tinyint(1) NOT NULL DEFAULT 0,
  `ohne_artikeltext` int(1) DEFAULT NULL,
  `abweichendebezeichnung` tinyint(1) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `bodyzusatz` text NOT NULL,
  `lieferbedingung` text NOT NULL,
  `titel` varchar(64) NOT NULL,
  `standardlager` int(11) NOT NULL DEFAULT 0,
  `kommissionskonsignationslager` int(11) NOT NULL DEFAULT 0,
  `bundesstaat` varchar(32) NOT NULL,
  `teillieferungvon` int(11) NOT NULL DEFAULT 0,
  `teillieferungnummer` int(11) NOT NULL DEFAULT 0,
  `kiste` int(11) NOT NULL DEFAULT -1,
  `versand_status` int(11) NOT NULL DEFAULT 0,
  `umgelagert` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`),
  KEY `adresse` (`adresse`),
  KEY `auftragid` (`auftragid`),
  KEY `land` (`land`),
  KEY `status` (`status`),
  KEY `datum` (`datum`),
  KEY `belegnr` (`belegnr`),
  KEY `keinerechnung` (`keinerechnung`),
  KEY `versandart` (`versandart`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferschein_position`
--

DROP TABLE IF EXISTS `lieferschein_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferschein_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `lieferschein` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `seriennummer` varchar(255) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `lieferdatum` date NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `status` varchar(64) NOT NULL,
  `bemerkung` text NOT NULL,
  `geliefert` decimal(14,4) NOT NULL,
  `abgerechnet` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `explodiert_parent_artikel` int(11) NOT NULL DEFAULT 0,
  `einheit` varchar(255) NOT NULL,
  `zolltarifnummer` varchar(128) NOT NULL DEFAULT '0',
  `herkunftsland` varchar(128) NOT NULL DEFAULT '0',
  `artikelnummerkunde` varchar(128) NOT NULL,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  `auftrag_position_id` int(11) NOT NULL DEFAULT 0,
  `kostenlos` tinyint(1) NOT NULL DEFAULT 0,
  `lagertext` varchar(255) NOT NULL,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `explodiert_parent` int(11) NOT NULL DEFAULT 0,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `zolleinzelwert` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `zollgesamtwert` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `zollwaehrung` varchar(3) NOT NULL,
  `zolleinzelgewicht` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `zollgesamtgewicht` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `nve` varchar(255) NOT NULL,
  `packstueck` varchar(255) NOT NULL,
  `vpemenge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `einzelstueckmenge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `ausblenden_im_pdf` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `lieferschein` (`lieferschein`),
  KEY `artikel` (`artikel`),
  KEY `auftrag_position_id` (`auftrag_position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferschein_protokoll`
--

DROP TABLE IF EXISTS `lieferschein_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferschein_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lieferschein` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lieferschein` (`lieferschein`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferschwelle`
--

DROP TABLE IF EXISTS `lieferschwelle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferschwelle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ursprungsland` varchar(255) NOT NULL,
  `empfaengerland` varchar(255) NOT NULL,
  `lieferschwelleeur` decimal(16,2) NOT NULL DEFAULT 0.00,
  `ustid` varchar(255) NOT NULL,
  `steuersatznormal` decimal(10,2) NOT NULL,
  `steuersatzermaessigt` decimal(10,2) NOT NULL,
  `steuersatzspezial` decimal(10,2) NOT NULL,
  `steuersatzspezialursprungsland` decimal(10,2) NOT NULL,
  `erloeskontonormal` int(11) NOT NULL,
  `erloeskontoermaessigt` int(11) NOT NULL,
  `erloeskontobefreit` int(11) NOT NULL,
  `ueberschreitungsdatum` date NOT NULL,
  `aktuellerumsatz` decimal(16,2) NOT NULL DEFAULT 0.00,
  `preiseanpassen` tinyint(1) NOT NULL DEFAULT 0,
  `verwenden` tinyint(1) NOT NULL DEFAULT 0,
  `jahr` varchar(4) DEFAULT NULL,
  `use_storage` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `empfaengerland` (`empfaengerland`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lieferschwelle_artikel`
--

DROP TABLE IF EXISTS `lieferschwelle_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieferschwelle_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `empfaengerland` varchar(255) NOT NULL,
  `steuersatz` decimal(10,2) NOT NULL,
  `bemerkung` varchar(255) NOT NULL,
  `aktiv` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `empfaengerland` (`empfaengerland`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liefertermine_positionen`
--

DROP TABLE IF EXISTS `liefertermine_positionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liefertermine_positionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bestellung` int(11) NOT NULL DEFAULT 0,
  `bestellung_position` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `lieferdatum` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `linkeditor`
--

DROP TABLE IF EXISTS `linkeditor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `linkeditor` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `rule` varchar(1024) NOT NULL,
  `replacewith` varchar(1024) NOT NULL,
  `active` varchar(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_time` datetime(3) DEFAULT NULL,
  `level` varchar(16) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `method` varchar(64) DEFAULT NULL,
  `line` int(11) unsigned DEFAULT NULL,
  `origin_type` varchar(64) DEFAULT NULL,
  `origin_detail` varchar(255) DEFAULT NULL,
  `dump` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logdatei`
--

DROP TABLE IF EXISTS `logdatei`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logdatei` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `befehl` varchar(255) NOT NULL,
  `statement` varchar(255) NOT NULL,
  `app` blob NOT NULL,
  `zeit` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logfile`
--

DROP TABLE IF EXISTS `logfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logfile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meldung` text NOT NULL,
  `dump` text NOT NULL,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `bearbeiter` varchar(64) NOT NULL,
  `funktionsname` varchar(64) NOT NULL,
  `datum` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `magento2_extended_mapping`
--

DROP TABLE IF EXISTS `magento2_extended_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `magento2_extended_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopexport_id` int(11) NOT NULL,
  `magento2_extended_mapping_name` varchar(255) NOT NULL,
  `magento2_extended_mapping_type` varchar(255) DEFAULT NULL,
  `magento2_extended_mapping_parameter` varchar(255) DEFAULT NULL,
  `magento2_extended_mapping_visible` tinyint(1) NOT NULL DEFAULT 0,
  `magento2_extended_mapping_filterable` tinyint(1) NOT NULL DEFAULT 0,
  `magento2_extended_mapping_searchable` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `shopexport_id` (`shopexport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mahnwesen`
--

DROP TABLE IF EXISTS `mahnwesen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mahnwesen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `tage` int(11) NOT NULL,
  `gebuehr` decimal(10,2) NOT NULL,
  `mail` int(11) NOT NULL,
  `druck` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailausgang`
--

DROP TABLE IF EXISTS `mailausgang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mailausgang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `body` longblob NOT NULL,
  `from` varchar(255) NOT NULL,
  `to` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '0',
  `art` int(10) NOT NULL DEFAULT 0,
  `zeit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `managementboard_liquiditaet`
--

DROP TABLE IF EXISTS `managementboard_liquiditaet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `managementboard_liquiditaet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(200) NOT NULL,
  `datum` date DEFAULT NULL,
  `enddatum` date DEFAULT NULL,
  `art` int(10) NOT NULL DEFAULT 0,
  `betrag` decimal(18,2) NOT NULL DEFAULT 0.00,
  `parent` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `managementboard_liquiditaet_datum`
--

DROP TABLE IF EXISTS `managementboard_liquiditaet_datum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `managementboard_liquiditaet_datum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctype` varchar(200) NOT NULL,
  `parameter` int(10) NOT NULL DEFAULT 0,
  `datum` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mandatory_field`
--

DROP TABLE IF EXISTS `mandatory_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mandatory_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `field_id` varchar(255) NOT NULL,
  `error_message` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `min_length` int(11) NOT NULL DEFAULT 0,
  `max_length` int(11) NOT NULL DEFAULT 0,
  `mandatory` tinyint(1) NOT NULL DEFAULT 0,
  `comparator` varchar(15) NOT NULL,
  `compareto` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`),
  KEY `action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `massenbearbeitung`
--

DROP TABLE IF EXISTS `massenbearbeitung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `massenbearbeitung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `feld` varchar(255) NOT NULL,
  `wert` text NOT NULL,
  `subjekt` varchar(255) NOT NULL,
  `objekt` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrix_article_options_translation`
--

DROP TABLE IF EXISTS `matrix_article_options_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrix_article_options_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matrix_article_id` int(11) NOT NULL,
  `language_from` varchar(255) NOT NULL,
  `name_from` varchar(255) NOT NULL,
  `name_external_from` varchar(255) NOT NULL,
  `language_to` varchar(255) NOT NULL,
  `name_to` varchar(255) NOT NULL,
  `name_external_to` varchar(255) NOT NULL,
  `articlenumber_suffix_from` varchar(16) NOT NULL,
  `articlenumber_suffix_to` varchar(16) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrix_article_translation`
--

DROP TABLE IF EXISTS `matrix_article_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrix_article_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_from` varchar(255) NOT NULL,
  `name_from` varchar(255) NOT NULL,
  `name_external_from` varchar(255) NOT NULL,
  `language_to` varchar(255) NOT NULL,
  `name_to` varchar(255) NOT NULL,
  `name_external_to` varchar(255) NOT NULL,
  `project` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrix_list_view`
--

DROP TABLE IF EXISTS `matrix_list_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrix_list_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matrix_article_id` int(11) NOT NULL DEFAULT 0,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `article_number` varchar(64) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `dimension1` varchar(64) NOT NULL,
  `option_id1` int(11) NOT NULL DEFAULT 0,
  `dimension2` varchar(64) NOT NULL,
  `option_id2` int(11) NOT NULL DEFAULT 0,
  `dimension3` varchar(64) NOT NULL,
  `option_id3` int(11) NOT NULL DEFAULT 0,
  `dimension4` varchar(64) NOT NULL,
  `option_id4` int(11) NOT NULL DEFAULT 0,
  `dimension5` varchar(64) NOT NULL,
  `option_id5` int(11) NOT NULL DEFAULT 0,
  `dimension6` varchar(64) NOT NULL,
  `option_id6` int(11) NOT NULL DEFAULT 0,
  `dimension7` varchar(64) NOT NULL,
  `option_id7` int(11) NOT NULL DEFAULT 0,
  `dimension8` varchar(64) NOT NULL,
  `option_id8` int(11) NOT NULL DEFAULT 0,
  `dimension9` varchar(64) NOT NULL,
  `option_id9` int(11) NOT NULL DEFAULT 0,
  `dimension10` varchar(64) NOT NULL,
  `option_id10` int(11) NOT NULL DEFAULT 0,
  `dimension11` varchar(64) NOT NULL,
  `option_id11` int(11) NOT NULL DEFAULT 0,
  `dimension12` varchar(64) NOT NULL,
  `option_id12` int(11) NOT NULL DEFAULT 0,
  `dimension13` varchar(64) NOT NULL,
  `option_id13` int(11) NOT NULL DEFAULT 0,
  `dimension14` varchar(64) NOT NULL,
  `option_id14` int(11) NOT NULL DEFAULT 0,
  `dimension15` varchar(64) NOT NULL,
  `option_id15` int(11) NOT NULL DEFAULT 0,
  `dimension16` varchar(64) NOT NULL,
  `option_id16` int(11) NOT NULL DEFAULT 0,
  `dimension17` varchar(64) NOT NULL,
  `option_id17` int(11) NOT NULL DEFAULT 0,
  `dimension18` varchar(64) NOT NULL,
  `option_id18` int(11) NOT NULL DEFAULT 0,
  `dimension19` varchar(64) NOT NULL,
  `option_id19` int(11) NOT NULL DEFAULT 0,
  `dimension20` varchar(64) NOT NULL,
  `option_id20` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `matrix_article_id` (`matrix_article_id`,`hash`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrix_list_view_status`
--

DROP TABLE IF EXISTS `matrix_list_view_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrix_list_view_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matrix_article_id` int(11) NOT NULL DEFAULT 0,
  `toupdate` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `matrix_article_id` (`matrix_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrixprodukt_eigenschaftengruppen`
--

DROP TABLE IF EXISTS `matrixprodukt_eigenschaftengruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixprodukt_eigenschaftengruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_ext` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(255) NOT NULL,
  `erstellt` timestamp NOT NULL DEFAULT current_timestamp(),
  `pflicht` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrixprodukt_eigenschaftengruppen_artikel`
--

DROP TABLE IF EXISTS `matrixprodukt_eigenschaftengruppen_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixprodukt_eigenschaftengruppen_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_ext` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(255) NOT NULL,
  `erstellt` timestamp NOT NULL DEFAULT current_timestamp(),
  `sort` int(11) NOT NULL DEFAULT 0,
  `pflicht` int(11) NOT NULL DEFAULT 0,
  `oeffentlich` int(11) NOT NULL DEFAULT 0,
  `typ` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrixprodukt_eigenschaftenoptionen`
--

DROP TABLE IF EXISTS `matrixprodukt_eigenschaftenoptionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixprodukt_eigenschaftenoptionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_ext` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `erstellt` timestamp NOT NULL DEFAULT current_timestamp(),
  `bearbeiter` varchar(255) NOT NULL,
  `artikelnummer` varchar(32) NOT NULL,
  `articlenumber_suffix` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrixprodukt_eigenschaftenoptionen_artikel`
--

DROP TABLE IF EXISTS `matrixprodukt_eigenschaftenoptionen_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixprodukt_eigenschaftenoptionen_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `matrixprodukt_eigenschaftenoptionen` int(11) NOT NULL DEFAULT 0,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `name_ext` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `erstellt` timestamp NOT NULL DEFAULT current_timestamp(),
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(255) NOT NULL,
  `artikelnummer` varchar(32) NOT NULL,
  `articlenumber_suffix` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gruppe` (`gruppe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `matrixprodukt_optionen_zu_artikel`
--

DROP TABLE IF EXISTS `matrixprodukt_optionen_zu_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixprodukt_optionen_zu_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `option_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `option_id` (`option_id`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maximum_discount`
--

DROP TABLE IF EXISTS `maximum_discount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maximum_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` int(11) NOT NULL DEFAULT 0,
  `discount` decimal(14,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mhd_log`
--

DROP TABLE IF EXISTS `mhd_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mhd_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `eingang` int(1) NOT NULL DEFAULT 0,
  `mhddatum` date DEFAULT NULL,
  `internebemerkung` text NOT NULL,
  `zeit` datetime DEFAULT NULL,
  `adresse_mitarbeiter` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `doctype` varchar(32) NOT NULL,
  `doctypeid` int(11) NOT NULL DEFAULT 0,
  `bestand` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `charge` varchar(255) NOT NULL,
  `is_interim` tinyint(1) NOT NULL DEFAULT 0,
  `storage_movement_id` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `doctypeid` (`doctypeid`),
  KEY `doctype` (`doctype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mitarbeiterzeiterfassung`
--

DROP TABLE IF EXISTS `mitarbeiterzeiterfassung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mitarbeiterzeiterfassung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `kuerzel` varchar(50) DEFAULT NULL,
  `von` datetime DEFAULT NULL,
  `bis` datetime DEFAULT NULL,
  `dauer` int(11) DEFAULT 0,
  `stechuhrvon` datetime DEFAULT NULL,
  `stechuhrbis` datetime DEFAULT NULL,
  `stechuhrvonid` int(11) DEFAULT 0,
  `stechuhrbisid` int(11) DEFAULT 0,
  `stechuhrdauer` int(11) DEFAULT 0,
  `buchungsart` varchar(100) DEFAULT NULL,
  `aktiv` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mitarbeiterzeiterfassung_einstellungen`
--

DROP TABLE IF EXISTS `mitarbeiterzeiterfassung_einstellungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mitarbeiterzeiterfassung_einstellungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(255) NOT NULL,
  `erstellt` timestamp NOT NULL DEFAULT current_timestamp(),
  `vorlagemo` int(11) NOT NULL DEFAULT 0,
  `vorlagedi` int(11) NOT NULL DEFAULT 0,
  `vorlagemi` int(11) NOT NULL DEFAULT 0,
  `vorlagedo` int(11) NOT NULL DEFAULT 0,
  `vorlagefr` int(11) NOT NULL DEFAULT 0,
  `vorlagesa` int(11) NOT NULL DEFAULT 0,
  `vorlageso` int(11) NOT NULL DEFAULT 0,
  `rundenkommen` varchar(48) NOT NULL DEFAULT 'nicht_runden',
  `rundengehen` varchar(48) NOT NULL DEFAULT 'nicht_runden',
  `pauseabziehen` tinyint(1) NOT NULL DEFAULT 0,
  `pausedauer` int(11) NOT NULL DEFAULT 0,
  `pauseab1` int(11) NOT NULL DEFAULT 0,
  `pausedauer1` int(11) NOT NULL DEFAULT 0,
  `pauseab2` int(11) NOT NULL DEFAULT 0,
  `pausedauer2` int(11) NOT NULL DEFAULT 0,
  `pauseab3` int(11) NOT NULL DEFAULT 0,
  `pausedauer3` int(11) NOT NULL DEFAULT 0,
  `urlaubimjahr` decimal(6,2) NOT NULL DEFAULT 0.00,
  `minutenprotag` int(11) NOT NULL DEFAULT 0,
  `resturlaub2015` decimal(6,2) NOT NULL DEFAULT 0.00,
  `resturlaub2016` decimal(6,2) NOT NULL DEFAULT 0.00,
  `resturlaub2017` decimal(6,2) NOT NULL DEFAULT 0.00,
  `urlaubimjahr2017` decimal(6,2) NOT NULL DEFAULT 0.00,
  `urlaubimjahr2018` decimal(6,2) NOT NULL DEFAULT 0.00,
  `standardstartzeit` time NOT NULL DEFAULT '08:00:00',
  `pauserunden` int(11) NOT NULL DEFAULT 5,
  `minstartzeit` tinyint(1) NOT NULL DEFAULT 0,
  `pauseaddieren` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mitarbeiterzeiterfassung_sollstunden`
--

DROP TABLE IF EXISTS `mitarbeiterzeiterfassung_sollstunden`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mitarbeiterzeiterfassung_sollstunden` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `datum` date DEFAULT NULL,
  `minuten` int(11) NOT NULL DEFAULT 0,
  `istminuten` int(11) NOT NULL DEFAULT 0,
  `berechnetminuten` int(11) NOT NULL DEFAULT 0,
  `urlaubminuten` int(11) NOT NULL DEFAULT 0,
  `unbezahltminuten` int(11) NOT NULL DEFAULT 0,
  `krankminuten` int(11) NOT NULL DEFAULT 0,
  `kuerzel` varchar(32) NOT NULL,
  `kommentar` varchar(255) NOT NULL,
  `standardstartzeit` time DEFAULT NULL,
  `minstartzeit` tinyint(1) DEFAULT NULL,
  `rundenkommen` varchar(48) DEFAULT NULL,
  `rundengehen` varchar(48) DEFAULT NULL,
  `pauseabziehen` tinyint(1) DEFAULT NULL,
  `pausedauer` int(11) DEFAULT NULL,
  `pauseab1` int(11) DEFAULT NULL,
  `pausedauer1` int(11) DEFAULT NULL,
  `pauseab2` int(11) DEFAULT NULL,
  `pausedauer2` int(11) DEFAULT NULL,
  `pauseab3` int(11) DEFAULT NULL,
  `pausedauer3` int(11) DEFAULT NULL,
  `minutenprotag` int(11) DEFAULT NULL,
  `pauserunden` int(11) DEFAULT NULL,
  `stundenberechnet` tinyint(1) NOT NULL DEFAULT 0,
  `stunden` decimal(6,2) NOT NULL DEFAULT 0.00,
  `pauseaddieren` tinyint(1) DEFAULT NULL,
  `vacation_request_token` varchar(32) NOT NULL,
  `internal_comment` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`),
  KEY `datum` (`datum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlm_abrechnung`
--

DROP TABLE IF EXISTS `mlm_abrechnung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlm_abrechnung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von` date DEFAULT NULL,
  `bis` date DEFAULT NULL,
  `betrag_netto` decimal(20,10) NOT NULL,
  `punkte` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bonuspunkte` decimal(10,2) NOT NULL DEFAULT 0.00,
  `anzahl` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlm_abrechnung_adresse`
--

DROP TABLE IF EXISTS `mlm_abrechnung_adresse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlm_abrechnung_adresse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `belegnr` int(11) NOT NULL DEFAULT 0,
  `betrag_netto` decimal(20,10) NOT NULL,
  `betrag_ist` decimal(20,10) NOT NULL,
  `mitsteuer` int(1) DEFAULT NULL,
  `mlmabrechnung` varchar(64) DEFAULT NULL,
  `alteposition` varchar(64) DEFAULT NULL,
  `neueposition` varchar(64) DEFAULT NULL,
  `erreichteposition` varchar(64) DEFAULT NULL,
  `abrechnung` int(11) NOT NULL DEFAULT 0,
  `waehrung` varchar(3) NOT NULL DEFAULT 'EUR',
  `punkte` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bonuspunkte` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rechnung_name` varchar(64) DEFAULT NULL,
  `rechnung_strasse` varchar(64) DEFAULT NULL,
  `rechnung_ort` varchar(64) DEFAULT NULL,
  `rechnung_plz` varchar(64) DEFAULT NULL,
  `rechnung_land` varchar(64) DEFAULT NULL,
  `steuernummer` varchar(64) DEFAULT NULL,
  `steuersatz` decimal(10,2) NOT NULL DEFAULT 0.00,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `bezahlt` int(1) NOT NULL DEFAULT 0,
  `bezahlt_bearbeiter` varchar(64) NOT NULL,
  `bezahlt_datum` date DEFAULT NULL,
  `bezahlt_status` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlm_abrechnung_log`
--

DROP TABLE IF EXISTS `mlm_abrechnung_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlm_abrechnung_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `abrechnung` int(11) NOT NULL DEFAULT 0,
  `meldung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlm_downline`
--

DROP TABLE IF EXISTS `mlm_downline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlm_downline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `downline` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlm_positionierung`
--

DROP TABLE IF EXISTS `mlm_positionierung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlm_positionierung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `positionierung` varchar(255) NOT NULL,
  `datum` date DEFAULT NULL,
  `erneuert` date DEFAULT NULL,
  `temporaer` tinyint(1) NOT NULL DEFAULT 0,
  `rueckgaengig` tinyint(1) NOT NULL DEFAULT 0,
  `mlm_abrechnung` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlm_wartekonto`
--

DROP TABLE IF EXISTS `mlm_wartekonto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlm_wartekonto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `betrag` decimal(10,2) DEFAULT NULL,
  `abrechnung` int(11) NOT NULL DEFAULT 0,
  `autoabrechnung` tinyint(1) NOT NULL DEFAULT 0,
  `abgerechnet` tinyint(1) NOT NULL DEFAULT 0,
  `rechnung_position_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module_action`
--

DROP TABLE IF EXISTS `module_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`action`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=275 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module_lock`
--

DROP TABLE IF EXISTS `module_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_lock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `userid` int(15) DEFAULT 0,
  `salt` varchar(255) DEFAULT NULL,
  `zeit` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module_stat`
--

DROP TABLE IF EXISTS `module_stat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `created_date` date DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `created_date` (`created_date`,`module`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module_stat_detail`
--

DROP TABLE IF EXISTS `module_stat_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_stat_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `document_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `uid` varchar(40) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`uid`,`module`,`action`,`document_id`,`visible`,`start_date`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module_status`
--

DROP TABLE IF EXISTS `module_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `navigation_alternative`
--

DROP TABLE IF EXISTS `navigation_alternative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `navigation_alternative` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `first` varchar(64) NOT NULL,
  `sec` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `prio` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletter_blacklist`
--

DROP TABLE IF EXISTS `newsletter_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletter_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newslettercache`
--

DROP TABLE IF EXISTS `newslettercache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newslettercache` (
  `checksum` text NOT NULL,
  `comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification_message`
--

DROP TABLE IF EXISTS `notification_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `type` varchar(16) NOT NULL DEFAULT 'default',
  `title` varchar(64) NOT NULL,
  `message` varchar(1024) DEFAULT NULL,
  `tags` varchar(512) DEFAULT NULL,
  `options_json` text DEFAULT NULL,
  `priority` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `object_stat`
--

DROP TABLE IF EXISTS `object_stat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_type` varchar(64) NOT NULL,
  `object_parameter` varchar(64) NOT NULL,
  `event_type` varchar(64) NOT NULL,
  `created_at` date NOT NULL,
  `event_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `created_at` (`created_at`,`object_type`,`object_parameter`,`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `objekt_lager_platz`
--

DROP TABLE IF EXISTS `objekt_lager_platz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `objekt_lager_platz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parameter` int(11) NOT NULL DEFAULT 0,
  `objekt` varchar(255) NOT NULL,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `kommentar` varchar(255) DEFAULT NULL,
  `bearbeiter` varchar(255) DEFAULT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `lager_platz` (`lager_platz`),
  KEY `parameter` (`parameter`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `objekt_protokoll`
--

DROP TABLE IF EXISTS `objekt_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `objekt_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objekt` varchar(64) NOT NULL,
  `objektid` int(11) NOT NULL DEFAULT 0,
  `action_long` varchar(128) NOT NULL,
  `meldung` varchar(255) NOT NULL DEFAULT '0',
  `bearbeiter` varchar(128) NOT NULL,
  `zeitstempel` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offenevorgaenge`
--

DROP TABLE IF EXISTS `offenevorgaenge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offenevorgaenge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `href` varchar(255) NOT NULL,
  `beschriftung` text NOT NULL,
  `linkremove` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `onlineshop_transfer_cart`
--

DROP TABLE IF EXISTS `onlineshop_transfer_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `onlineshop_transfer_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `cart_original` mediumtext DEFAULT NULL,
  `cart_transfer` mediumtext DEFAULT NULL,
  `template` mediumtext DEFAULT NULL,
  `extid` varchar(255) DEFAULT NULL,
  `internet` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`),
  KEY `extid` (`extid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `onlineshops_tasks`
--

DROP TABLE IF EXISTS `onlineshops_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `onlineshops_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(15) DEFAULT 0,
  `command` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'inactive',
  `counter` int(15) DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `lastupdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `openstreetmap_status`
--

DROP TABLE IF EXISTS `openstreetmap_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `openstreetmap_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paketannahme`
--

DROP TABLE IF EXISTS `paketannahme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paketannahme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `datum` datetime NOT NULL,
  `verpackungszustand` int(11) NOT NULL,
  `bemerkung` text NOT NULL,
  `foto` int(11) NOT NULL,
  `gewicht` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `vorlage` varchar(255) NOT NULL,
  `vorlageid` varchar(255) NOT NULL,
  `zahlung` varchar(255) NOT NULL,
  `betrag` decimal(10,4) NOT NULL,
  `status` varchar(64) NOT NULL,
  `beipack_rechnung` int(1) NOT NULL,
  `beipack_lieferschein` int(1) NOT NULL,
  `beipack_anschreiben` int(1) NOT NULL,
  `beipack_gesamt` int(10) NOT NULL,
  `bearbeiter_distribution` varchar(255) NOT NULL,
  `postgrund` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `renr` varchar(255) DEFAULT NULL,
  `lsnr` varchar(255) DEFAULT NULL,
  `datum_abgeschlossen` datetime NOT NULL,
  `bearbeiter_abgeschlossen` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paketdistribution`
--

DROP TABLE IF EXISTS `paketdistribution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paketdistribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bearbeiter` varchar(255) NOT NULL,
  `zeit` datetime NOT NULL,
  `paketannahme` int(11) NOT NULL,
  `adresse` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `etiketten` int(11) NOT NULL,
  `bemerkung` text NOT NULL,
  `bestellung_position` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `retoure_position` int(11) NOT NULL DEFAULT 0,
  `vorlaeufig` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `partner`
--

DROP TABLE IF EXISTS `partner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `partner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `netto` decimal(10,2) NOT NULL,
  `tage` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `geloescht` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `shop` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `partner_verkauf`
--

DROP TABLE IF EXISTS `partner_verkauf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `partner_verkauf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auftrag` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `menge` int(11) NOT NULL,
  `partner` int(11) NOT NULL,
  `freigabe` int(1) NOT NULL,
  `abgerechnet` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parts_list_alternative`
--

DROP TABLE IF EXISTS `parts_list_alternative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parts_list_alternative` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parts_list_id` int(11) NOT NULL DEFAULT 0,
  `alternative_article_id` int(11) NOT NULL DEFAULT 0,
  `reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_transaction`
--

DROP TABLE IF EXISTS `payment_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `returnorder_id` int(11) NOT NULL DEFAULT 0,
  `payment_status` varchar(32) NOT NULL,
  `payment_account_id` int(11) NOT NULL DEFAULT 0,
  `address_id` int(11) NOT NULL DEFAULT 0,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(8) NOT NULL,
  `payment_reason` varchar(255) NOT NULL,
  `payment_json` text DEFAULT NULL,
  `liability_id` int(11) NOT NULL DEFAULT 0,
  `payment_transaction_group_id` int(11) NOT NULL DEFAULT 0,
  `payment_info` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `returnorder_id` (`returnorder_id`),
  KEY `liabilitiy_id` (`liability_id`),
  KEY `payment_transaction_group_id` (`payment_transaction_group_id`),
  KEY `payment_account_id` (`payment_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_transaction_group`
--

DROP TABLE IF EXISTS `payment_transaction_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transaction_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_account_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_account_id` (`payment_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_transaction_preview`
--

DROP TABLE IF EXISTS `payment_transaction_preview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transaction_preview` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `returnorder_id` int(11) NOT NULL DEFAULT 0,
  `liability_id` int(11) NOT NULL DEFAULT 0,
  `payment_account_id` int(11) NOT NULL DEFAULT 0,
  `address_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `selected` tinyint(1) NOT NULL DEFAULT 0,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(8) NOT NULL,
  `payment_reason` varchar(255) NOT NULL,
  `payment_info` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `returnorder_id` (`returnorder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paymentaccount_import_job`
--

DROP TABLE IF EXISTS `paymentaccount_import_job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentaccount_import_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paymentaccount_id` int(11) NOT NULL DEFAULT 0,
  `from` datetime DEFAULT NULL,
  `to` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(32) NOT NULL DEFAULT 'created',
  PRIMARY KEY (`id`),
  KEY `paymentaccount_id` (`paymentaccount_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paymentaccount_import_scheduler`
--

DROP TABLE IF EXISTS `paymentaccount_import_scheduler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentaccount_import_scheduler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paymentaccount_id` int(11) NOT NULL DEFAULT 0,
  `hour` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paymentaccount_id` (`paymentaccount_id`,`hour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paymentimport_lock`
--

DROP TABLE IF EXISTS `paymentimport_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentimport_lock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paymentaccount_id` int(11) NOT NULL DEFAULT 0,
  `locked_by_type` varchar(16) NOT NULL,
  `locked_by_id` int(11) NOT NULL DEFAULT 0,
  `script_process_id` int(11) NOT NULL DEFAULT 0,
  `last_update` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paymentaccount_id` (`paymentaccount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pdfarchiv`
--

DROP TABLE IF EXISTS `pdfarchiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pdfarchiv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zeitstempel` datetime DEFAULT NULL,
  `checksum` varchar(128) NOT NULL,
  `table_id` int(11) NOT NULL DEFAULT 0,
  `table_name` varchar(128) NOT NULL,
  `doctype` varchar(128) NOT NULL,
  `doctypeorig` varchar(128) NOT NULL,
  `dateiname` varchar(128) NOT NULL,
  `bearbeiter` varchar(128) NOT NULL,
  `belegnummer` varchar(128) NOT NULL,
  `erstesoriginal` int(11) NOT NULL DEFAULT 0,
  `schreibschutz` tinyint(1) NOT NULL DEFAULT 0,
  `keinhintergrund` tinyint(1) NOT NULL DEFAULT 0,
  `parameter` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `table_id` (`table_id`),
  KEY `schreibschutz` (`schreibschutz`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pdfmirror_md5pool`
--

DROP TABLE IF EXISTS `pdfmirror_md5pool`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pdfmirror_md5pool` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zeitstempel` datetime DEFAULT NULL,
  `checksum` varchar(128) NOT NULL,
  `table_id` int(11) NOT NULL DEFAULT 0,
  `table_name` varchar(128) NOT NULL,
  `bearbeiter` varchar(128) NOT NULL,
  `erstesoriginal` int(11) NOT NULL DEFAULT 0,
  `pdfarchiv_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissionhistory`
--

DROP TABLE IF EXISTS `permissionhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissionhistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `granting_user_id` int(11) DEFAULT NULL,
  `granting_user_name` varchar(255) DEFAULT NULL,
  `receiving_user_id` int(11) DEFAULT NULL,
  `receiving_user_name` varchar(255) DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `permission` int(1) DEFAULT NULL,
  `timeofpermission` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pinwand`
--

DROP TABLE IF EXISTS `pinwand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pinwand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `user` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pinwand_user`
--

DROP TABLE IF EXISTS `pinwand_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pinwand_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pinwand` int(11) NOT NULL DEFAULT 0,
  `user` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `pinwand` (`pinwand`,`user`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_abschluss`
--

DROP TABLE IF EXISTS `pos_abschluss`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_abschluss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bargeld` longblob NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `datum` date DEFAULT NULL,
  `zeitstempel` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_kassierer`
--

DROP TABLE IF EXISTS `pos_kassierer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_kassierer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `kassenkennung` varchar(16) NOT NULL,
  `inaktiv` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_order`
--

DROP TABLE IF EXISTS `pos_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `auftrag` int(11) NOT NULL DEFAULT 0,
  `rechnung` int(11) NOT NULL DEFAULT 0,
  `lieferschein` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `verkaeufer` int(11) NOT NULL DEFAULT 0,
  `zeitstempel` datetime DEFAULT NULL,
  `zahlungsweise` varchar(64) NOT NULL,
  `betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `lager` varchar(128) NOT NULL,
  `gutschrift` int(11) NOT NULL DEFAULT 0,
  `wechselgeld` decimal(10,2) DEFAULT NULL,
  `gegeben` decimal(10,2) DEFAULT NULL,
  `betrag_diff` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tip` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tip_konto` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_rksv`
--

DROP TABLE IF EXISTS `pos_rksv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_rksv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projekt` int(11) DEFAULT NULL,
  `rechnung` int(11) DEFAULT NULL,
  `belegnummer` int(11) NOT NULL DEFAULT 0,
  `betragnormal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `betragermaessigt1` decimal(18,2) NOT NULL DEFAULT 0.00,
  `betragermaessigt2` decimal(18,2) NOT NULL DEFAULT 0.00,
  `betragbesonders` decimal(18,2) NOT NULL DEFAULT 0.00,
  `betragnull` decimal(18,2) NOT NULL DEFAULT 0.00,
  `umsatzzaehler` decimal(18,2) NOT NULL DEFAULT 0.00,
  `umsatzzaehler_aes` text NOT NULL,
  `signatur` text NOT NULL,
  `jwscompact` text NOT NULL,
  `belegart` varchar(10) NOT NULL,
  `zeitstempel` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_sessions`
--

DROP TABLE IF EXISTS `pos_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `kassierer` varchar(64) NOT NULL DEFAULT '0',
  `sesssionbezeichnung` varchar(64) NOT NULL,
  `data` longtext DEFAULT NULL,
  `zeitstempel` datetime DEFAULT NULL,
  `importiert` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_tagesabschluss`
--

DROP TABLE IF EXISTS `pos_tagesabschluss`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_tagesabschluss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `netto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `brutto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `datum` date DEFAULT NULL,
  `nummer` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `datum` (`datum`),
  KEY `projekt` (`projekt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos_zaehlungen`
--

DROP TABLE IF EXISTS `pos_zaehlungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_zaehlungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `konto` int(11) NOT NULL DEFAULT 0,
  `eur500` int(11) NOT NULL DEFAULT 0,
  `eur200` int(11) NOT NULL DEFAULT 0,
  `eur100` int(11) NOT NULL DEFAULT 0,
  `eur50` int(11) NOT NULL DEFAULT 0,
  `eur20` int(11) NOT NULL DEFAULT 0,
  `eur10` int(11) NOT NULL DEFAULT 0,
  `eur5` int(11) NOT NULL DEFAULT 0,
  `eur1` int(11) NOT NULL DEFAULT 0,
  `eur2` int(11) NOT NULL DEFAULT 0,
  `eur05` int(11) NOT NULL DEFAULT 0,
  `eur02` int(11) NOT NULL DEFAULT 0,
  `eur01` int(11) NOT NULL DEFAULT 0,
  `eur005` int(11) NOT NULL DEFAULT 0,
  `eur002` int(11) NOT NULL DEFAULT 0,
  `eur001` int(11) NOT NULL DEFAULT 0,
  `gesamt` decimal(18,2) NOT NULL DEFAULT 0.00,
  `diff` decimal(18,2) NOT NULL DEFAULT 0.00,
  `kommentar` text NOT NULL,
  `bearbeiter` varchar(64) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `preisanfrage`
--

DROP TABLE IF EXISTS `preisanfrage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preisanfrage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `projekt` varchar(255) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `auftrag` varchar(255) NOT NULL,
  `auftragid` int(11) NOT NULL,
  `freitext` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `adresse` int(11) NOT NULL,
  `mitarbeiter` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `lieferantennummer` varchar(255) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `versand` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `inbearbeitung_user` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `reservierart` varchar(255) DEFAULT NULL,
  `auslagerart` varchar(255) DEFAULT 'sammel',
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `datumauslieferung` date DEFAULT NULL,
  `datumbereitstellung` date DEFAULT NULL,
  `zuarchivieren` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(255) NOT NULL DEFAULT 'EUR',
  `typ` varchar(255) NOT NULL DEFAULT 'firma',
  `bearbeiterid` int(11) DEFAULT NULL,
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `internebemerkung` text NOT NULL,
  `sprache` varchar(32) NOT NULL,
  `bodyzusatz` text NOT NULL,
  `lieferbedingung` text NOT NULL,
  `titel` varchar(64) NOT NULL,
  `bundesstaat` varchar(32) NOT NULL,
  `zusammenfassen` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `preisanfrage_position`
--

DROP TABLE IF EXISTS `preisanfrage_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preisanfrage_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `preisanfrage` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `sort` int(10) NOT NULL,
  `bemerkung` text NOT NULL,
  `preis` decimal(10,4) NOT NULL,
  `geliefert` decimal(14,4) NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `einheit` varchar(255) NOT NULL,
  `lieferdatum` date DEFAULT NULL,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `preisanfrage` (`preisanfrage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `preisanfrage_protokoll`
--

DROP TABLE IF EXISTS `preisanfrage_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preisanfrage_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `preisanfrage` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presta_image_association`
--

DROP TABLE IF EXISTS `presta_image_association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presta_image_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `xentral_id` int(11) NOT NULL,
  `presta_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presta_matrix_association`
--

DROP TABLE IF EXISTS `presta_matrix_association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presta_matrix_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `combination_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion`
--

DROP TABLE IF EXISTS `produktion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `art` varchar(255) NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `internet` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `angebot` varchar(255) NOT NULL,
  `freitext` text NOT NULL,
  `internebemerkung` text NOT NULL,
  `status` varchar(64) NOT NULL DEFAULT 'angelegt',
  `adresse` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `plz` varchar(64) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(64) NOT NULL,
  `ustid` varchar(64) NOT NULL,
  `ust_befreit` int(1) NOT NULL,
  `ust_inner` int(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(255) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `vertrieb` varchar(255) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungszieltage` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `zahlungszielskonto` int(11) NOT NULL,
  `bank_inhaber` varchar(255) NOT NULL,
  `bank_institut` varchar(255) NOT NULL,
  `bank_blz` varchar(255) NOT NULL,
  `bank_konto` varchar(255) NOT NULL,
  `kreditkarte_typ` varchar(255) NOT NULL,
  `kreditkarte_inhaber` varchar(255) NOT NULL,
  `kreditkarte_nummer` varchar(255) NOT NULL,
  `kreditkarte_pruefnummer` varchar(255) NOT NULL,
  `kreditkarte_monat` varchar(255) NOT NULL,
  `kreditkarte_jahr` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `autoversand` int(1) NOT NULL,
  `keinporto` int(1) NOT NULL,
  `keinestornomail` int(1) NOT NULL,
  `abweichendelieferadresse` int(1) NOT NULL,
  `liefername` varchar(255) NOT NULL,
  `lieferabteilung` varchar(255) NOT NULL,
  `lieferunterabteilung` varchar(255) NOT NULL,
  `lieferland` varchar(64) NOT NULL,
  `lieferstrasse` varchar(255) NOT NULL,
  `lieferort` varchar(255) NOT NULL,
  `lieferplz` varchar(64) NOT NULL,
  `lieferadresszusatz` varchar(255) NOT NULL,
  `lieferansprechpartner` varchar(255) NOT NULL,
  `packstation_inhaber` varchar(255) NOT NULL,
  `packstation_station` varchar(255) NOT NULL,
  `packstation_ident` varchar(255) NOT NULL,
  `packstation_plz` varchar(64) NOT NULL,
  `packstation_ort` varchar(255) NOT NULL,
  `autofreigabe` int(1) NOT NULL,
  `freigabe` int(1) NOT NULL,
  `nachbesserung` int(1) NOT NULL,
  `gesamtsumme` decimal(18,2) NOT NULL DEFAULT 0.00,
  `inbearbeitung` int(1) NOT NULL,
  `abgeschlossen` int(1) NOT NULL,
  `nachlieferung` int(1) NOT NULL,
  `lager_ok` int(1) NOT NULL,
  `porto_ok` int(1) NOT NULL,
  `ust_ok` int(1) NOT NULL,
  `check_ok` int(1) NOT NULL,
  `vorkasse_ok` int(1) NOT NULL,
  `nachnahme_ok` int(1) NOT NULL,
  `reserviert_ok` int(1) NOT NULL,
  `bestellt_ok` int(1) NOT NULL,
  `zeit_ok` int(1) NOT NULL,
  `versand_ok` int(1) NOT NULL,
  `partnerid` int(11) NOT NULL,
  `folgebestaetigung` date NOT NULL,
  `zahlungsmail` date NOT NULL,
  `stornogrund` varchar(255) NOT NULL,
  `stornosonstiges` varchar(255) NOT NULL,
  `stornorueckzahlung` varchar(255) NOT NULL,
  `stornobetrag` decimal(18,2) NOT NULL DEFAULT 0.00,
  `stornobankinhaber` varchar(255) NOT NULL,
  `stornobankkonto` varchar(255) NOT NULL,
  `stornobankblz` varchar(255) NOT NULL,
  `stornobankbank` varchar(255) NOT NULL,
  `stornogutschrift` int(1) NOT NULL,
  `stornogutschriftbeleg` varchar(255) NOT NULL,
  `stornowareerhalten` int(1) NOT NULL,
  `stornomanuellebearbeitung` varchar(255) NOT NULL,
  `stornokommentar` text NOT NULL,
  `stornobezahlt` varchar(255) NOT NULL,
  `stornobezahltam` date NOT NULL,
  `stornobezahltvon` varchar(255) NOT NULL,
  `stornoabgeschlossen` int(1) NOT NULL,
  `stornorueckzahlungper` varchar(255) NOT NULL,
  `stornowareerhaltenretour` int(1) NOT NULL,
  `partnerausgezahlt` int(1) NOT NULL,
  `partnerausgezahltam` date NOT NULL,
  `kennen` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bezeichnung` varchar(255) DEFAULT NULL,
  `datumproduktion` date DEFAULT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(255) NOT NULL DEFAULT 'EUR',
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviert` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviertversion` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(255) NOT NULL DEFAULT 'firma',
  `reservierart` varchar(255) DEFAULT NULL,
  `auslagerart` varchar(255) DEFAULT 'sammel',
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `datumauslieferung` date DEFAULT NULL,
  `datumbereitstellung` date DEFAULT NULL,
  `unterlistenexplodieren` tinyint(1) NOT NULL DEFAULT 0,
  `charge` varchar(255) NOT NULL,
  `arbeitsschrittetextanzeigen` tinyint(1) NOT NULL DEFAULT 1,
  `einlagern_ok` int(1) NOT NULL,
  `auslagern_ok` int(1) NOT NULL,
  `mhd` date DEFAULT NULL,
  `auftragmengenanpassen` int(1) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `mengeoriginal` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `teilproduktionvon` int(11) NOT NULL DEFAULT 0,
  `teilproduktionnummer` int(11) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `parentnummer` int(11) NOT NULL DEFAULT 0,
  `bearbeiterid` int(11) DEFAULT NULL,
  `mengeausschuss` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `mengeerfolgreich` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `abschlussbemerkung` text NOT NULL,
  `auftragid` int(11) NOT NULL DEFAULT 0,
  `funktionstest` tinyint(1) NOT NULL DEFAULT 0,
  `seriennummer_erstellen` int(11) NOT NULL DEFAULT 1,
  `unterseriennummern_erfassen` tinyint(1) NOT NULL DEFAULT 0,
  `datumproduktionende` date DEFAULT NULL,
  `standardlager` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`),
  KEY `auftragid` (`auftragid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_arbeitsanweisung`
--

DROP TABLE IF EXISTS `produktion_arbeitsanweisung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_arbeitsanweisung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produktion` int(11) NOT NULL DEFAULT 0,
  `position` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `bild` longblob NOT NULL,
  `einzelzeit` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `geplanter_mitarbeiter` int(11) NOT NULL DEFAULT 0,
  `arbeitsplatzgruppe` int(11) NOT NULL DEFAULT 0,
  `status` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `produktion` (`produktion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_arbeitsanweisung_batch`
--

DROP TABLE IF EXISTS `produktion_arbeitsanweisung_batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_arbeitsanweisung_batch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produktion` int(11) NOT NULL DEFAULT 0,
  `produktion_arbeitsanweisung` int(11) NOT NULL DEFAULT 0,
  `erfolgreich` decimal(14,4) NOT NULL,
  `ausschuss` decimal(14,4) NOT NULL,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `batch` text NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `produktion_arbeitsanweisung` (`produktion_arbeitsanweisung`),
  KEY `produktion` (`produktion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_baugruppen`
--

DROP TABLE IF EXISTS `produktion_baugruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_baugruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produktion` int(11) NOT NULL DEFAULT 0,
  `position` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `baugruppennr` varchar(255) NOT NULL,
  `seriennummer` varchar(255) NOT NULL,
  `pruefer` varchar(255) NOT NULL,
  `kommentar` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `statusgeprueft` tinyint(4) NOT NULL DEFAULT 0,
  `hauptseriennummerok` tinyint(4) NOT NULL DEFAULT 0,
  `unterseriennummerok` tinyint(4) NOT NULL DEFAULT 0,
  `istausschuss` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `produktion` (`produktion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_baugruppen_charge`
--

DROP TABLE IF EXISTS `produktion_baugruppen_charge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_baugruppen_charge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produktion` int(11) NOT NULL DEFAULT 0,
  `baugruppe` int(11) NOT NULL DEFAULT 0,
  `charge` int(11) NOT NULL DEFAULT 0,
  `chargennummer` varchar(32) NOT NULL,
  `mhd` date DEFAULT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `menge` decimal(14,4) NOT NULL DEFAULT 1.0000,
  PRIMARY KEY (`id`),
  KEY `produktion` (`produktion`),
  KEY `baugruppe` (`baugruppe`),
  KEY `charge` (`charge`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_charge`
--

DROP TABLE IF EXISTS `produktion_charge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_charge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produktion` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `bezeichnung` varchar(255) NOT NULL,
  `kommentar` text NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `chargennummer` varchar(32) NOT NULL,
  `mhd` date DEFAULT NULL,
  `typ` varchar(32) NOT NULL,
  `anzahl` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `ausgelagert` decimal(14,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`id`),
  KEY `produktion` (`produktion`),
  KEY `artikel` (`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_etiketten`
--

DROP TABLE IF EXISTS `produktion_etiketten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_etiketten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produktion` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `etikett` int(11) NOT NULL DEFAULT 0,
  `drucker` int(11) NOT NULL DEFAULT 0,
  `menge` int(11) NOT NULL DEFAULT 0,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `produktion` (`produktion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_funktionsprotokoll`
--

DROP TABLE IF EXISTS `produktion_funktionsprotokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_funktionsprotokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produktion` int(11) NOT NULL DEFAULT 0,
  `position` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `bild` longblob NOT NULL,
  `typ` varchar(255) NOT NULL DEFAULT 'frage',
  `widget` varchar(255) NOT NULL,
  `klassen` varchar(255) NOT NULL,
  `beschreibung_textfeld1` varchar(255) NOT NULL,
  `beschreibung_textfeld2` varchar(255) NOT NULL,
  `textfeld1` tinyint(1) NOT NULL DEFAULT 0,
  `textfeld2` tinyint(1) NOT NULL DEFAULT 0,
  `config` text NOT NULL,
  `weiter_bei_fehler` tinyint(1) NOT NULL DEFAULT 0,
  `menge` int(11) NOT NULL DEFAULT 0,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `bearbeiter` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `produktion` (`produktion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_funktionsprotokoll_position`
--

DROP TABLE IF EXISTS `produktion_funktionsprotokoll_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_funktionsprotokoll_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `baugruppe` int(11) NOT NULL DEFAULT 0,
  `funktionsprotokoll` int(11) NOT NULL DEFAULT 0,
  `textfeld1` text NOT NULL,
  `textfeld2` text NOT NULL,
  `eingabejson` text NOT NULL,
  `eingabehtml` text NOT NULL,
  `ausgabejson` text NOT NULL,
  `ausgabehtml` text NOT NULL,
  `ok` int(11) NOT NULL DEFAULT 0,
  `fehler` int(11) NOT NULL DEFAULT 0,
  `klasse` varchar(255) NOT NULL,
  `kommentar` text NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `inaktiv` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `funktionsprotokoll` (`funktionsprotokoll`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_position`
--

DROP TABLE IF EXISTS `produktion_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produktion` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `preis` decimal(18,8) NOT NULL,
  `waehrung` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `status` varchar(64) NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `geliefert` int(11) NOT NULL,
  `geliefert_menge` decimal(14,4) NOT NULL,
  `explodiert` int(1) NOT NULL,
  `explodiert_parent` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nachbestelltexternereinkauf` int(1) DEFAULT NULL,
  `beistellung` int(11) NOT NULL DEFAULT 0,
  `externeproduktion` int(11) NOT NULL DEFAULT 0,
  `einheit` varchar(255) NOT NULL,
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(1024) DEFAULT NULL,
  `erloese` varchar(8) DEFAULT NULL,
  `erloesefestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `stuecklistestufe` int(15) DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `produktion` (`produktion`),
  KEY `artikel` (`artikel`),
  KEY `explodiert_parent` (`explodiert_parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_protokoll`
--

DROP TABLE IF EXISTS `produktion_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produktion` int(11) NOT NULL DEFAULT 0,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `produktion` (`produktion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktion_unterseriennummern`
--

DROP TABLE IF EXISTS `produktion_unterseriennummern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktion_unterseriennummern` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `baugruppe` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `seriennummer` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `inaktiv` tinyint(1) NOT NULL DEFAULT 0,
  `kommentar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `baugruppe` (`baugruppe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `produktionslager`
--

DROP TABLE IF EXISTS `produktionslager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produktionslager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `bemerkung` varchar(255) NOT NULL,
  `status` varchar(64) NOT NULL,
  `bestellung_pos` int(11) NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `produzent` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proformarechnung`
--

DROP TABLE IF EXISTS `proformarechnung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proformarechnung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `aborechnung` int(1) NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `anlegeart` varchar(255) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `auftrag` varchar(255) NOT NULL,
  `auftragid` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `freitext` text NOT NULL,
  `internebemerkung` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `adresse` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `ust_befreit` int(1) NOT NULL,
  `ustbrief` int(11) NOT NULL,
  `ustbrief_eingang` int(11) NOT NULL,
  `ustbrief_eingang_am` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(64) DEFAULT NULL,
  `lieferschein` int(11) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `buchhaltung` varchar(255) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungsstatus` varchar(255) NOT NULL,
  `ist` decimal(18,2) NOT NULL DEFAULT 0.00,
  `soll` decimal(18,2) NOT NULL DEFAULT 0.00,
  `skonto_gegeben` decimal(10,2) NOT NULL,
  `zahlungszieltage` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `zahlungszielskonto` decimal(10,2) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `versendet_mahnwesen` int(1) NOT NULL,
  `mahnwesen` varchar(255) NOT NULL,
  `mahnwesen_datum` date NOT NULL,
  `mahnwesen_gesperrt` int(1) NOT NULL,
  `mahnwesen_internebemerkung` text NOT NULL,
  `inbearbeitung` int(1) NOT NULL,
  `datev_abgeschlossen` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `doppel` int(1) DEFAULT NULL,
  `autodruck_rz` int(1) NOT NULL DEFAULT 0,
  `autodruck_periode` int(1) NOT NULL DEFAULT 1,
  `autodruck_done` int(1) NOT NULL DEFAULT 0,
  `autodruck_anzahlverband` int(11) NOT NULL DEFAULT 0,
  `autodruck_anzahlkunde` int(11) NOT NULL DEFAULT 0,
  `autodruck_mailverband` int(1) NOT NULL DEFAULT 0,
  `autodruck_mailkunde` int(1) NOT NULL DEFAULT 0,
  `dta_datei_verband` int(11) NOT NULL DEFAULT 0,
  `dta_datei` int(11) NOT NULL DEFAULT 0,
  `deckungsbeitragcalc` tinyint(1) NOT NULL DEFAULT 0,
  `deckungsbeitrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `umsatz_netto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `erloes_netto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `mahnwesenfestsetzen` tinyint(1) NOT NULL DEFAULT 0,
  `vertriebid` int(11) DEFAULT NULL,
  `aktion` varchar(64) NOT NULL,
  `vertrieb` varchar(255) NOT NULL,
  `provision` decimal(10,2) DEFAULT NULL,
  `provision_summe` decimal(10,2) DEFAULT NULL,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `punkte` int(11) DEFAULT NULL,
  `bonuspunkte` int(11) DEFAULT NULL,
  `provdatum` date DEFAULT NULL,
  `ihrebestellnummer` varchar(255) DEFAULT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `realrabatt` decimal(10,2) DEFAULT NULL,
  `rabatt` decimal(10,2) DEFAULT NULL,
  `einzugsdatum` date DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `forderungsverlust_datum` date DEFAULT NULL,
  `forderungsverlust_betrag` decimal(10,2) DEFAULT NULL,
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(255) NOT NULL DEFAULT 'EUR',
  `keinsteuersatz` int(1) DEFAULT NULL,
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviert` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviertversion` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(255) NOT NULL DEFAULT 'firma',
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `lieferid` int(11) NOT NULL DEFAULT 0,
  `ansprechpartnerid` int(11) NOT NULL DEFAULT 0,
  `systemfreitext` text NOT NULL,
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `zuarchivieren` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `angelegtam` datetime DEFAULT NULL,
  `abweichendebezeichnung` tinyint(1) NOT NULL DEFAULT 0,
  `bezahlt_am` date DEFAULT NULL,
  `sprache` varchar(32) NOT NULL,
  `abweichendelieferadresse` int(1) DEFAULT NULL,
  `titel` varchar(255) DEFAULT NULL,
  `bearbeiterid` int(1) NOT NULL,
  `bodyzusatz` text NOT NULL,
  `lieferbedingung` text DEFAULT NULL,
  `liefername` varchar(255) DEFAULT NULL,
  `lieferabteilung` varchar(255) DEFAULT NULL,
  `lieferunterabteilung` varchar(255) DEFAULT NULL,
  `lieferland` varchar(2) DEFAULT NULL,
  `lieferstrasse` varchar(255) DEFAULT NULL,
  `lieferort` varchar(255) DEFAULT NULL,
  `lieferplz` varchar(20) DEFAULT NULL,
  `lieferadresszusatz` varchar(255) DEFAULT NULL,
  `lieferansprechpartner` varchar(255) DEFAULT NULL,
  `liefertitel` varchar(255) DEFAULT NULL,
  `liefergln` varchar(64) DEFAULT NULL,
  `zollinformation` int(1) DEFAULT NULL,
  `verzollungadresse` int(1) NOT NULL DEFAULT 0,
  `verzollinformationen` text DEFAULT NULL,
  `verzollungname` varchar(255) NOT NULL,
  `verzollungabteilung` varchar(255) NOT NULL,
  `verzollungunterabteilung` varchar(255) NOT NULL,
  `verzollungland` varchar(2) NOT NULL,
  `verzollungstrasse` varchar(255) NOT NULL,
  `verzollungort` varchar(255) NOT NULL,
  `verzollungplz` varchar(20) NOT NULL,
  `verzollungadresszusatz` varchar(255) NOT NULL,
  `verzollungansprechpartner` varchar(255) NOT NULL,
  `verzollungtitel` varchar(255) NOT NULL,
  `formelmenge` varchar(255) NOT NULL,
  `anzeigesteuer` tinyint(11) NOT NULL DEFAULT 0,
  `bundesstaat` varchar(32) NOT NULL,
  `lieferbundesstaat` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proformarechnung_lieferschein`
--

DROP TABLE IF EXISTS `proformarechnung_lieferschein`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proformarechnung_lieferschein` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proformarechnung` int(11) NOT NULL DEFAULT 0,
  `lieferschein` int(11) NOT NULL DEFAULT 0,
  `lieferschein_position` int(11) NOT NULL DEFAULT 0,
  `proformarechnung_position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proformarechnung_position`
--

DROP TABLE IF EXISTS `proformarechnung_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proformarechnung_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `proformarechnung` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `waehrung` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `status` varchar(64) NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `explodiert_parent_artikel` int(11) NOT NULL DEFAULT 0,
  `keinrabatterlaubt` int(1) DEFAULT NULL,
  `punkte` decimal(10,2) NOT NULL,
  `bonuspunkte` decimal(10,2) NOT NULL,
  `mlmdirektpraemie` decimal(10,2) DEFAULT NULL,
  `mlm_abgerechnet` int(1) DEFAULT NULL,
  `grundrabatt` decimal(10,2) DEFAULT NULL,
  `rabattsync` int(1) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `einheit` varchar(255) NOT NULL,
  `rabatt` decimal(10,2) NOT NULL,
  `zolltarifnummer` varchar(128) NOT NULL DEFAULT '0',
  `herkunftsland` varchar(128) NOT NULL DEFAULT '0',
  `artikelnummerkunde` varchar(128) NOT NULL,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  `auftrag_position_id` int(11) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(255) DEFAULT NULL,
  `erloese` varchar(8) DEFAULT NULL,
  `kostenstelle` varchar(10) NOT NULL,
  `erloesefestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `formelmenge` varchar(255) NOT NULL,
  `formelpreis` varchar(255) NOT NULL,
  `geliefert` decimal(14,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `proformarechnung` (`proformarechnung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proformarechnung_protokoll`
--

DROP TABLE IF EXISTS `proformarechnung_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proformarechnung_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proformarechnung` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projekt`
--

DROP TABLE IF EXISTS `projekt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projekt` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `abkuerzung` varchar(128) NOT NULL,
  `verantwortlicher` text NOT NULL,
  `beschreibung` text NOT NULL,
  `sonstiges` text NOT NULL,
  `aktiv` varchar(10) NOT NULL,
  `farbe` varchar(16) NOT NULL,
  `autoversand` int(1) NOT NULL,
  `checkok` int(1) NOT NULL,
  `portocheck` int(1) NOT NULL,
  `automailrechnung` int(1) NOT NULL,
  `checkname` text NOT NULL,
  `zahlungserinnerung` int(1) NOT NULL,
  `zahlungsmailbedinungen` text NOT NULL,
  `folgebestaetigung` int(1) NOT NULL,
  `stornomail` int(1) NOT NULL,
  `kundenfreigabe_loeschen` int(1) NOT NULL,
  `autobestellung` int(1) NOT NULL,
  `speziallieferschein` int(1) NOT NULL,
  `lieferscheinbriefpapier` int(11) NOT NULL,
  `speziallieferscheinbeschriftung` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `geloescht` int(1) NOT NULL,
  `logdatei` text NOT NULL,
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(3) NOT NULL DEFAULT 'EUR',
  `eigenesteuer` int(1) NOT NULL DEFAULT 0,
  `druckerlogistikstufe1` int(11) NOT NULL DEFAULT 0,
  `druckerlogistikstufe2` int(11) NOT NULL DEFAULT 0,
  `selbstabholermail` tinyint(1) NOT NULL DEFAULT 0,
  `eanherstellerscan` tinyint(1) NOT NULL DEFAULT 0,
  `reservierung` int(1) DEFAULT NULL,
  `verkaufszahlendiagram` int(1) DEFAULT NULL,
  `oeffentlich` int(1) NOT NULL DEFAULT 0,
  `shopzwangsprojekt` int(1) NOT NULL DEFAULT 0,
  `kunde` int(11) DEFAULT NULL,
  `dpdkundennr` text NOT NULL,
  `dhlkundennr` text NOT NULL,
  `dhlformat` text DEFAULT NULL,
  `dpdformat` text DEFAULT NULL,
  `paketmarke_einzeldatei` int(1) DEFAULT NULL,
  `dpdpfad` text DEFAULT NULL,
  `dhlpfad` text DEFAULT NULL,
  `upspfad` varchar(64) NOT NULL DEFAULT '0',
  `dhlintodb` tinyint(1) NOT NULL DEFAULT 0,
  `intraship_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `intraship_drucker` int(11) NOT NULL DEFAULT 0,
  `intraship_testmode` tinyint(1) NOT NULL DEFAULT 0,
  `intraship_user` text NOT NULL,
  `intraship_signature` text NOT NULL,
  `intraship_ekp` text NOT NULL,
  `intraship_api_user` text NOT NULL,
  `intraship_api_password` text NOT NULL,
  `intraship_company_name` text NOT NULL,
  `intraship_street_name` text NOT NULL,
  `intraship_street_number` text NOT NULL,
  `intraship_zip` varchar(12) NOT NULL,
  `intraship_country` varchar(128) NOT NULL DEFAULT 'germany',
  `intraship_city` text NOT NULL,
  `intraship_email` text NOT NULL,
  `intraship_phone` text NOT NULL,
  `intraship_internet` text NOT NULL,
  `intraship_contact_person` text NOT NULL,
  `intraship_account_owner` text NOT NULL,
  `intraship_account_number` text NOT NULL,
  `intraship_bank_code` text NOT NULL,
  `intraship_bank_name` text NOT NULL,
  `intraship_iban` text NOT NULL,
  `intraship_bic` text NOT NULL,
  `intraship_WeightInKG` int(11) NOT NULL DEFAULT 5,
  `intraship_LengthInCM` int(11) NOT NULL DEFAULT 50,
  `intraship_WidthInCM` int(11) NOT NULL DEFAULT 50,
  `intraship_HeightInCM` int(11) NOT NULL DEFAULT 50,
  `intraship_PackageType` varchar(8) NOT NULL DEFAULT 'pl',
  `abrechnungsart` text NOT NULL,
  `kommissionierverfahren` text NOT NULL,
  `wechselaufeinstufig` int(11) DEFAULT NULL,
  `projektuebergreifendkommisionieren` int(1) DEFAULT NULL,
  `absendeadresse` text DEFAULT NULL,
  `absendename` text DEFAULT NULL,
  `absendesignatur` text NOT NULL,
  `autodruckrechnung` int(1) DEFAULT NULL,
  `autodruckversandbestaetigung` int(1) DEFAULT NULL,
  `automailversandbestaetigung` int(1) DEFAULT NULL,
  `autodrucklieferschein` int(1) DEFAULT NULL,
  `automaillieferschein` int(1) DEFAULT NULL,
  `autodruckstorno` int(1) DEFAULT NULL,
  `autodruckanhang` int(1) DEFAULT NULL,
  `automailanhang` int(1) DEFAULT NULL,
  `autodruckerrechnung` int(11) NOT NULL DEFAULT 1,
  `autodruckerlieferschein` int(11) NOT NULL DEFAULT 1,
  `autodruckeranhang` int(11) NOT NULL DEFAULT 1,
  `autodruckrechnungmenge` int(11) NOT NULL DEFAULT 1,
  `autodrucklieferscheinmenge` int(11) NOT NULL DEFAULT 1,
  `eigenernummernkreis` int(11) DEFAULT NULL,
  `next_angebot` text DEFAULT NULL,
  `next_auftrag` text DEFAULT NULL,
  `next_rechnung` text DEFAULT NULL,
  `next_lieferschein` text DEFAULT NULL,
  `next_arbeitsnachweis` text DEFAULT NULL,
  `next_reisekosten` text DEFAULT NULL,
  `next_bestellung` text DEFAULT NULL,
  `next_gutschrift` text DEFAULT NULL,
  `next_kundennummer` text DEFAULT NULL,
  `next_lieferantennummer` text DEFAULT NULL,
  `next_mitarbeiternummer` text DEFAULT NULL,
  `next_waren` text DEFAULT NULL,
  `next_produktion` text DEFAULT NULL,
  `next_sonstiges` text DEFAULT NULL,
  `next_anfrage` text DEFAULT NULL,
  `next_artikelnummer` text DEFAULT NULL,
  `gesamtstunden_max` decimal(10,2) NOT NULL,
  `auftragid` int(11) DEFAULT NULL,
  `dhlzahlungmandant` varchar(3) NOT NULL,
  `dhlretourenschein` int(1) NOT NULL,
  `land` varchar(2) NOT NULL DEFAULT 'de',
  `etiketten_positionen` tinyint(1) NOT NULL DEFAULT 0,
  `etiketten_drucker` int(11) NOT NULL DEFAULT 0,
  `etiketten_art` int(11) NOT NULL DEFAULT 0,
  `seriennummernerfassen` tinyint(1) NOT NULL DEFAULT 1,
  `versandzweigeteilt` tinyint(1) NOT NULL DEFAULT 0,
  `nachnahmecheck` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_lieferschein_anlegen` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_lagerprozess` text NOT NULL,
  `kasse_belegausgabe` text NOT NULL,
  `kasse_preisgruppe` int(11) NOT NULL DEFAULT 0,
  `kasse_text_bemerkung` varchar(255) NOT NULL DEFAULT 'interne bemerkung',
  `kasse_text_freitext` varchar(255) NOT NULL DEFAULT 'text auf beleg',
  `kasse_drucker` int(11) NOT NULL DEFAULT 0,
  `kasse_lieferschein` int(11) NOT NULL DEFAULT 1,
  `kasse_rechnung` int(11) NOT NULL DEFAULT 1,
  `kasse_lieferschein_doppel` int(11) NOT NULL DEFAULT 1,
  `kasse_lager` int(11) NOT NULL DEFAULT 0,
  `kasse_konto` int(11) NOT NULL DEFAULT 0,
  `kasse_laufkundschaft` int(11) NOT NULL DEFAULT 0,
  `kasse_rabatt_artikel` int(11) NOT NULL DEFAULT 0,
  `kasse_zahlung_bar` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_zahlung_ec` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_zahlung_kreditkarte` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_zahlung_ueberweisung` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_zahlung_paypal` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_extra_keinbeleg` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_extra_rechnung` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_extra_quittung` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_extra_gutschein` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_extra_rabatt_prozent` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_extra_rabatt_euro` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_adresse_erweitert` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_zahlungsauswahl_zwang` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_button_entnahme` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_button_trinkgeld` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_vorauswahl_anrede` varchar(64) NOT NULL DEFAULT 'herr',
  `kasse_erweiterte_lagerabfrage` tinyint(1) NOT NULL DEFAULT 0,
  `filialadresse` int(11) NOT NULL DEFAULT 0,
  `versandprojektfiliale` int(11) NOT NULL DEFAULT 0,
  `differenz_auslieferung_tage` int(11) NOT NULL DEFAULT 2,
  `autostuecklistenanpassung` int(11) NOT NULL DEFAULT 1,
  `dpdendung` varchar(32) NOT NULL DEFAULT '.csv',
  `dhlendung` varchar(32) NOT NULL DEFAULT '.csv',
  `tracking_substr_start` int(11) NOT NULL DEFAULT 8,
  `tracking_remove_kundennummer` tinyint(11) NOT NULL DEFAULT 1,
  `tracking_substr_length` tinyint(11) NOT NULL DEFAULT 0,
  `go_drucker` int(11) NOT NULL DEFAULT 0,
  `go_apiurl_prefix` text NOT NULL,
  `go_apiurl_postfix` text NOT NULL,
  `go_apiurl_user` text NOT NULL,
  `go_username` text NOT NULL,
  `go_password` text NOT NULL,
  `go_ax4nr` text NOT NULL,
  `go_name1` text NOT NULL,
  `go_name2` text NOT NULL,
  `go_abteilung` text NOT NULL,
  `go_strasse1` text NOT NULL,
  `go_strasse2` text NOT NULL,
  `go_hausnummer` varchar(10) NOT NULL,
  `go_plz` text DEFAULT NULL,
  `go_ort` text NOT NULL,
  `go_land` text NOT NULL,
  `go_standardgewicht` decimal(10,2) DEFAULT NULL,
  `go_format` text DEFAULT NULL,
  `go_ausgabe` text DEFAULT NULL,
  `intraship_exportgrund` text NOT NULL,
  `billsafe_merchantId` text NOT NULL,
  `billsafe_merchantLicenseSandbox` text NOT NULL,
  `billsafe_merchantLicenseLive` text NOT NULL,
  `billsafe_applicationSignature` text NOT NULL,
  `billsafe_applicationVersion` text NOT NULL,
  `secupay_apikey` text NOT NULL,
  `secupay_url` text NOT NULL,
  `secupay_demo` tinyint(1) NOT NULL DEFAULT 0,
  `mahnwesen` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(64) NOT NULL DEFAULT 'gestartet',
  `kasse_bondrucker` int(11) NOT NULL DEFAULT 0,
  `kasse_bondrucker_aktiv` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_bondrucker_qrcode` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_bon_zeile1` varchar(255) NOT NULL DEFAULT 'xentral store',
  `kasse_bon_zeile2` text NOT NULL,
  `kasse_bon_zeile3` text NOT NULL,
  `kasse_zahlung_bar_bezahlt` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_zahlung_ec_bezahlt` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_zahlung_kreditkarte_bezahlt` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_zahlung_ueberweisung_bezahlt` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_zahlung_paypal_bezahlt` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_quittung_rechnung` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_button_einlage` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_button_schublade` tinyint(1) NOT NULL DEFAULT 0,
  `produktionauftragautomatischfreigeben` tinyint(1) NOT NULL DEFAULT 0,
  `versandlagerplatzanzeigen` tinyint(1) NOT NULL DEFAULT 0,
  `versandartikelnameausstammdaten` tinyint(1) NOT NULL DEFAULT 0,
  `projektlager` int(1) NOT NULL DEFAULT 0,
  `tracing_substr_length` tinyint(11) NOT NULL DEFAULT 0,
  `intraship_partnerid` varchar(32) NOT NULL DEFAULT '01',
  `intraship_retourenlabel` tinyint(1) NOT NULL DEFAULT 0,
  `intraship_retourenaccount` varchar(16) NOT NULL,
  `absendegrussformel` text NOT NULL,
  `autodruckrechnungdoppel` int(1) NOT NULL DEFAULT 0,
  `intraship_partnerid_welt` text NOT NULL,
  `next_kalkulation` text NOT NULL,
  `next_preisanfrage` text NOT NULL,
  `next_proformarechnung` text NOT NULL,
  `next_verbindlichkeit` text DEFAULT NULL,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `mahnwesen_abweichender_versender` varchar(40) NOT NULL,
  `lagerplatzlieferscheinausblenden` int(11) NOT NULL DEFAULT 0,
  `etiketten_sort` tinyint(2) NOT NULL DEFAULT 0,
  `eanherstellerscanerlauben` tinyint(1) NOT NULL DEFAULT 0,
  `chargenerfassen` tinyint(1) NOT NULL DEFAULT 0,
  `mhderfassen` tinyint(1) NOT NULL DEFAULT 0,
  `autodruckrechnungstufe1` tinyint(1) NOT NULL DEFAULT 0,
  `autodruckrechnungstufe1menge` tinyint(1) NOT NULL DEFAULT 1,
  `autodruckrechnungstufe1mail` tinyint(1) NOT NULL DEFAULT 0,
  `autodruckkommissionierscheinstufe1` tinyint(1) NOT NULL DEFAULT 0,
  `autodruckkommissionierscheinstufe1menge` tinyint(1) NOT NULL DEFAULT 1,
  `kasse_bondrucker_freifeld` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_bondrucker_anzahl` int(11) NOT NULL DEFAULT 1,
  `kasse_rksv_aktiv` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_rksv_tool` text NOT NULL,
  `kasse_rksv_kartenleser` text NOT NULL,
  `kasse_rksv_karteseriennummer` text NOT NULL,
  `kasse_rksv_kartepin` text NOT NULL,
  `kasse_rksv_aeskey` text NOT NULL,
  `kasse_rksv_publiczertifikat` text NOT NULL,
  `kasse_rksv_publiczertifikatkette` text NOT NULL,
  `kasse_rksv_kassenid` text NOT NULL,
  `kasse_gutschrift` int(11) NOT NULL DEFAULT 1,
  `rechnungerzeugen` tinyint(1) NOT NULL DEFAULT 0,
  `pos_artikeltexteuebernehmen` tinyint(1) NOT NULL DEFAULT 0,
  `pos_anzeigenetto` tinyint(1) NOT NULL DEFAULT 0,
  `pos_zwischenspeichern` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_button_belegladen` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_button_storno` tinyint(1) NOT NULL DEFAULT 0,
  `pos_kundenalleprojekte` tinyint(1) NOT NULL DEFAULT 0,
  `pos_artikelnurausprojekt` tinyint(1) NOT NULL DEFAULT 0,
  `allechargenmhd` tinyint(1) NOT NULL DEFAULT 0,
  `anzeigesteuerbelege` int(11) NOT NULL DEFAULT 0,
  `pos_grosseansicht` tinyint(1) NOT NULL DEFAULT 0,
  `preisberechnung` int(11) NOT NULL DEFAULT 0,
  `steuernummer` varchar(32) NOT NULL,
  `paketmarkeautodrucken` tinyint(1) NOT NULL DEFAULT 0,
  `orderpicking_sort` varchar(26) NOT NULL,
  `deactivateautoshipping` tinyint(1) NOT NULL DEFAULT 0,
  `pos_sumarticles` tinyint(1) NOT NULL DEFAULT 0,
  `manualtracking` tinyint(1) NOT NULL DEFAULT 0,
  `zahlungsweise` text NOT NULL,
  `zahlungsweiselieferant` text NOT NULL,
  `versandart` text NOT NULL,
  `ups_api_user` text NOT NULL,
  `ups_api_password` text NOT NULL,
  `ups_api_key` text NOT NULL,
  `ups_accountnumber` text NOT NULL,
  `ups_company_name` text NOT NULL,
  `ups_street_name` text NOT NULL,
  `ups_street_number` varchar(10) NOT NULL,
  `ups_zip` text NOT NULL,
  `ups_country` varchar(2) NOT NULL,
  `ups_city` text NOT NULL,
  `ups_email` text NOT NULL,
  `ups_phone` text NOT NULL,
  `ups_internet` text NOT NULL,
  `ups_contact_person` text NOT NULL,
  `ups_WeightInKG` decimal(10,2) DEFAULT NULL,
  `ups_LengthInCM` decimal(10,2) DEFAULT NULL,
  `ups_WidthInCM` decimal(10,2) DEFAULT NULL,
  `ups_HeightInCM` decimal(10,2) DEFAULT NULL,
  `ups_drucker` int(11) NOT NULL DEFAULT 0,
  `ups_ausgabe` varchar(16) NOT NULL DEFAULT 'gif',
  `ups_package_code` varchar(16) NOT NULL DEFAULT '02',
  `ups_package_description` varchar(255) NOT NULL DEFAULT 'customer supplied',
  `ups_service_code` varchar(16) NOT NULL DEFAULT '11',
  `ups_service_description` varchar(255) NOT NULL DEFAULT 'ups standard',
  `email_html_template` text DEFAULT NULL,
  `druckanhang` int(1) DEFAULT NULL,
  `mailanhang` int(1) DEFAULT NULL,
  `next_retoure` text DEFAULT NULL,
  `next_goodspostingdocument` text DEFAULT NULL,
  `pos_disable_single_entries` tinyint(1) DEFAULT 0,
  `pos_disable_single_day` tinyint(1) DEFAULT 0,
  `pos_disable_counting_protocol` tinyint(1) DEFAULT 0,
  `pos_disable_signature` tinyint(1) DEFAULT 0,
  `steuer_erloese_inland_normal` varchar(10) DEFAULT NULL,
  `steuer_aufwendung_inland_normal` varchar(10) DEFAULT NULL,
  `steuer_erloese_inland_ermaessigt` varchar(10) DEFAULT NULL,
  `steuer_aufwendung_inland_ermaessigt` varchar(10) DEFAULT NULL,
  `steuer_erloese_inland_nichtsteuerbar` varchar(10) DEFAULT NULL,
  `steuer_aufwendung_inland_nichtsteuerbar` varchar(10) DEFAULT NULL,
  `steuer_erloese_inland_innergemeinschaftlich` varchar(10) DEFAULT NULL,
  `steuer_aufwendung_inland_innergemeinschaftlich` varchar(10) DEFAULT NULL,
  `steuer_erloese_inland_eunormal` varchar(10) DEFAULT NULL,
  `steuer_aufwendung_inland_eunormal` varchar(10) DEFAULT NULL,
  `steuer_erloese_inland_euermaessigt` varchar(10) DEFAULT NULL,
  `steuer_aufwendung_inland_euermaessigt` varchar(10) DEFAULT NULL,
  `steuer_erloese_inland_export` varchar(10) DEFAULT NULL,
  `steuer_aufwendung_inland_import` varchar(10) DEFAULT NULL,
  `create_proformainvoice` tinyint(1) DEFAULT 0,
  `print_proformainvoice` tinyint(1) DEFAULT 0,
  `proformainvoice_amount` int(11) DEFAULT 0,
  `anzeigesteuerbelegebestellung` tinyint(1) DEFAULT 0,
  `autobestbeforebatch` tinyint(1) DEFAULT 0,
  `allwaysautobestbeforebatch` tinyint(1) DEFAULT 0,
  `kommissionierlauflieferschein` tinyint(1) NOT NULL DEFAULT 0,
  `intraship_exportdrucker` int(11) NOT NULL DEFAULT 0,
  `multiorderpicking` tinyint(1) NOT NULL DEFAULT 0,
  `standardlager` int(11) NOT NULL DEFAULT 0,
  `standardlagerproduktion` int(11) NOT NULL DEFAULT 0,
  `klarna_merchantid` text NOT NULL,
  `klarna_sharedsecret` text NOT NULL,
  `nurlagerartikel` tinyint(1) NOT NULL DEFAULT 1,
  `paketmarkedrucken` tinyint(1) NOT NULL DEFAULT 0,
  `lieferscheinedrucken` tinyint(1) NOT NULL DEFAULT 0,
  `lieferscheinedruckenmenge` int(11) NOT NULL DEFAULT 0,
  `auftragdrucken` tinyint(1) NOT NULL DEFAULT 0,
  `auftragdruckenmenge` int(11) NOT NULL DEFAULT 0,
  `druckennachtracking` tinyint(1) NOT NULL DEFAULT 0,
  `exportdruckrechnungstufe1` tinyint(1) NOT NULL DEFAULT 0,
  `exportdruckrechnungstufe1menge` int(11) NOT NULL DEFAULT 0,
  `exportdruckrechnung` tinyint(1) NOT NULL DEFAULT 0,
  `exportdruckrechnungmenge` int(11) NOT NULL DEFAULT 0,
  `kommissionierlistestufe1` tinyint(1) NOT NULL DEFAULT 0,
  `kommissionierlistestufe1menge` int(11) NOT NULL DEFAULT 0,
  `fremdnummerscanerlauben` tinyint(1) NOT NULL DEFAULT 0,
  `zvt100url` text NOT NULL,
  `zvt100port` varchar(5) NOT NULL,
  `production_show_only_needed_storages` tinyint(1) NOT NULL DEFAULT 0,
  `produktion_extra_seiten` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_button_trinkgeldeckredit` tinyint(1) NOT NULL DEFAULT 0,
  `kasse_autologout` int(11) NOT NULL DEFAULT 0,
  `kasse_autologout_abschluss` int(11) NOT NULL DEFAULT 0,
  `next_receiptdocument` text DEFAULT NULL,
  `taxfromdoctypesettings` tinyint(1) NOT NULL DEFAULT 0,
  `next_lieferantengutschrift` text DEFAULT NULL,
  `kasse_print_qr` tinyint(1) NOT NULL DEFAULT 0,
  `buchhaltung_berater` varchar(64) NOT NULL,
  `buchhaltung_mandant` varchar(64) NOT NULL,
  `buchhaltung_wj_beginn` varchar(4) NOT NULL DEFAULT '0101',
  `buchhaltung_sachkontenlaenge` int(1) NOT NULL DEFAULT 4,
  PRIMARY KEY (`id`),
  KEY `abkuerzung` (`abkuerzung`),
  KEY `kunde` (`kunde`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projekt_artikel`
--

DROP TABLE IF EXISTS `projekt_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projekt_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `parent` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `geplant` decimal(14,4) NOT NULL,
  `cache_BE` decimal(14,4) NOT NULL,
  `cache_PR` decimal(14,4) NOT NULL,
  `cache_AN` decimal(14,4) NOT NULL,
  `cache_AB` decimal(14,4) NOT NULL,
  `cache_LS` decimal(14,4) NOT NULL,
  `cache_RE` decimal(14,4) NOT NULL,
  `cache_GS` decimal(14,4) NOT NULL,
  `cache_WE` decimal(14,4) NOT NULL,
  `ek_geplant` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `vk_geplant` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `kalkulationbasis` varchar(64) NOT NULL DEFAULT 'prostueck',
  `nr` varchar(64) NOT NULL,
  `cache_WA` decimal(14,4) NOT NULL,
  `cache_PF` decimal(14,4) NOT NULL,
  `cache_PRO` decimal(14,4) NOT NULL,
  `lastcheck` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_cache` timestamp NULL DEFAULT NULL,
  `kommentar` varchar(1024) NOT NULL,
  `showinmonitoring` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projekt_inventar`
--

DROP TABLE IF EXISTS `projekt_inventar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projekt_inventar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `bestellung` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `adresse` int(11) NOT NULL,
  `mitarbeiter` varchar(255) NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `zeit` datetime NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `protokoll`
--

DROP TABLE IF EXISTS `protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meldung` text NOT NULL,
  `dump` text NOT NULL,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `bearbeiter` varchar(64) NOT NULL,
  `funktionsname` varchar(64) NOT NULL,
  `datum` datetime DEFAULT NULL,
  `parameter` int(11) NOT NULL DEFAULT 0,
  `argumente` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1132 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provision_regeln`
--

DROP TABLE IF EXISTS `provision_regeln`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provision_regeln` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `beschreibung` text NOT NULL,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `von` date NOT NULL,
  `bis` date NOT NULL,
  `typ` varchar(32) NOT NULL,
  `prio` int(11) NOT NULL DEFAULT 0,
  `absolut` tinyint(1) NOT NULL DEFAULT 0,
  `provision` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `belegtyp` varchar(32) NOT NULL,
  `belegnr` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `gruppe` (`gruppe`),
  KEY `artikel` (`artikel`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provisionenartikel_abrechnungen`
--

DROP TABLE IF EXISTS `provisionenartikel_abrechnungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provisionenartikel_abrechnungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datumvon` date DEFAULT NULL,
  `datumbis` date DEFAULT NULL,
  `angelegt_von` varchar(128) NOT NULL,
  `umsatz_netto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `provision` decimal(10,2) NOT NULL DEFAULT 0.00,
  `dynamisch` int(11) NOT NULL DEFAULT 0,
  `userid` int(11) NOT NULL DEFAULT 0,
  `berechnungstyp` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provisionenartikel_abrechnungen_provisionen`
--

DROP TABLE IF EXISTS `provisionenartikel_abrechnungen_provisionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provisionenartikel_abrechnungen_provisionen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `artikelkategorie` varchar(255) NOT NULL,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `waehrung` varchar(128) NOT NULL,
  `menge` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `datum` date DEFAULT NULL,
  `provision` decimal(10,2) NOT NULL DEFAULT 0.00,
  `abrechnung` int(11) NOT NULL DEFAULT 0,
  `rabatt` decimal(10,2) NOT NULL DEFAULT 0.00,
  `umsatznetto` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `provisionbetrag` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `typ` tinyint(1) NOT NULL DEFAULT 1,
  `typid` int(11) NOT NULL DEFAULT 0,
  `vertriebsleiteradresse` int(11) NOT NULL DEFAULT 0,
  `vertriebsleiterprovision` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vertriebsleiterprovisionbetrag` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `nummer` varchar(255) NOT NULL,
  `name_de` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provisionenartikel_provision`
--

DROP TABLE IF EXISTS `provisionenartikel_provision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provisionenartikel_provision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `kategorie` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `provision` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gueltigvon` date DEFAULT NULL,
  `gueltigbis` date DEFAULT NULL,
  `provisiontyp` varchar(64) NOT NULL,
  `kunde` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prozessstarter`
--

DROP TABLE IF EXISTS `prozessstarter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prozessstarter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `bedingung` varchar(255) NOT NULL,
  `art` varchar(255) NOT NULL,
  `startzeit` datetime NOT NULL,
  `letzteausfuerhung` datetime NOT NULL,
  `periode` varchar(255) NOT NULL DEFAULT '1440',
  `typ` varchar(255) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `aktiv` int(1) NOT NULL,
  `mutex` int(1) NOT NULL,
  `mutexcounter` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `art_filter` varchar(20) NOT NULL,
  `status` varchar(255) NOT NULL,
  `status_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `recommended_period` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parameter` (`parameter`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pseudostorage_shop`
--

DROP TABLE IF EXISTS `pseudostorage_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pseudostorage_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `formula` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `real_article_mapping`
--

DROP TABLE IF EXISTS `real_article_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `real_article_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `ignore` int(2) NOT NULL DEFAULT 0,
  `ext_sku` varchar(255) NOT NULL,
  `ext_ean` varchar(255) NOT NULL,
  `ext_name` varchar(255) NOT NULL,
  `ext_item_id` varchar(255) NOT NULL,
  `ext_unit_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `real_kategoriespezifisch`
--

DROP TABLE IF EXISTS `real_kategoriespezifisch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `real_kategoriespezifisch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `artikel` int(11) NOT NULL,
  `shop` int(11) NOT NULL,
  `mandatory` int(1) DEFAULT 0,
  `specwert` varchar(255) NOT NULL,
  `specbezeichnung` varchar(255) NOT NULL,
  `specname` varchar(255) DEFAULT NULL,
  `multipleallowed` int(1) DEFAULT 0,
  `type` varchar(255) DEFAULT NULL,
  `typevalue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `real_kategorievorschlag`
--

DROP TABLE IF EXISTS `real_kategorievorschlag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `real_kategorievorschlag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `artikel` int(11) NOT NULL,
  `shop` int(11) NOT NULL,
  `vorschlagid` int(11) DEFAULT NULL,
  `vorschlagbezeichnung` varchar(255) DEFAULT NULL,
  `vorschlagparentid` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL,
  `lieferkategorie` varchar(255) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `real_versandgruppen`
--

DROP TABLE IF EXISTS `real_versandgruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `real_versandgruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `receiptdocument`
--

DROP TABLE IF EXISTS `receiptdocument`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receiptdocument` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `creditnote_id` int(11) NOT NULL DEFAULT 0,
  `parcel_receipt_id` int(11) NOT NULL DEFAULT 0,
  `useredit_id` int(11) NOT NULL DEFAULT 0,
  `status` varchar(32) NOT NULL,
  `status_qs` varchar(32) NOT NULL,
  `updated_by` varchar(255) NOT NULL,
  `document_number` varchar(32) NOT NULL,
  `supplier_order_id` int(11) NOT NULL DEFAULT 0,
  `return_order_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `useredit_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `receiptdocument_log`
--

DROP TABLE IF EXISTS `receiptdocument_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receiptdocument_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receiptdocument_id` int(11) NOT NULL DEFAULT 0,
  `log` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `receiptdocument_id` (`receiptdocument_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `receiptdocument_position`
--

DROP TABLE IF EXISTS `receiptdocument_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receiptdocument_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receiptdocument_id` int(11) NOT NULL DEFAULT 0,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `amount` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `amount_good` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `amount_bad` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `position` int(11) NOT NULL DEFAULT 0,
  `type` varchar(32) NOT NULL,
  `doctype` varchar(255) NOT NULL,
  `doctypeid` int(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `receiptdocument_id` (`receiptdocument_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rechnung`
--

DROP TABLE IF EXISTS `rechnung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rechnung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `aborechnung` int(1) NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `anlegeart` varchar(255) NOT NULL,
  `belegnr` varchar(255) NOT NULL,
  `auftrag` varchar(255) NOT NULL,
  `auftragid` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `freitext` text NOT NULL,
  `internebemerkung` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `adresse` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `ansprechpartner` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `ust_befreit` int(1) NOT NULL,
  `ustbrief` int(11) NOT NULL,
  `ustbrief_eingang` int(11) NOT NULL,
  `ustbrief_eingang_am` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(64) DEFAULT NULL,
  `lieferschein` int(11) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `buchhaltung` varchar(255) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungsstatus` varchar(255) NOT NULL,
  `ist` decimal(18,2) NOT NULL DEFAULT 0.00,
  `soll` decimal(18,2) NOT NULL DEFAULT 0.00,
  `skonto_gegeben` decimal(10,2) NOT NULL,
  `zahlungszieltage` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `zahlungszielskonto` decimal(10,2) NOT NULL,
  `firma` int(11) NOT NULL,
  `versendet` int(1) NOT NULL,
  `versendet_am` datetime NOT NULL,
  `versendet_per` varchar(255) NOT NULL,
  `versendet_durch` varchar(255) NOT NULL,
  `versendet_mahnwesen` int(1) NOT NULL,
  `mahnwesen` varchar(255) NOT NULL,
  `mahnwesen_datum` date NOT NULL,
  `mahnwesen_gesperrt` int(1) NOT NULL,
  `mahnwesen_internebemerkung` text NOT NULL,
  `inbearbeitung` int(1) NOT NULL,
  `datev_abgeschlossen` int(1) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `doppel` int(1) DEFAULT NULL,
  `autodruck_rz` int(1) NOT NULL DEFAULT 0,
  `autodruck_periode` int(1) NOT NULL DEFAULT 1,
  `autodruck_done` int(1) NOT NULL DEFAULT 0,
  `autodruck_anzahlverband` int(11) NOT NULL DEFAULT 0,
  `autodruck_anzahlkunde` int(11) NOT NULL DEFAULT 0,
  `autodruck_mailverband` int(1) NOT NULL DEFAULT 0,
  `autodruck_mailkunde` int(1) NOT NULL DEFAULT 0,
  `dta_datei_verband` int(11) NOT NULL DEFAULT 0,
  `dta_datei` int(11) NOT NULL DEFAULT 0,
  `deckungsbeitragcalc` tinyint(1) NOT NULL DEFAULT 0,
  `deckungsbeitrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `umsatz_netto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `erloes_netto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `mahnwesenfestsetzen` tinyint(1) NOT NULL DEFAULT 0,
  `vertriebid` int(11) DEFAULT NULL,
  `aktion` varchar(64) NOT NULL,
  `vertrieb` varchar(255) NOT NULL,
  `provision` decimal(10,2) DEFAULT NULL,
  `provision_summe` decimal(10,2) DEFAULT NULL,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `punkte` int(11) DEFAULT NULL,
  `bonuspunkte` int(11) DEFAULT NULL,
  `provdatum` date DEFAULT NULL,
  `ihrebestellnummer` varchar(255) DEFAULT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `realrabatt` decimal(10,2) DEFAULT NULL,
  `rabatt` decimal(10,2) DEFAULT NULL,
  `einzugsdatum` date DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `forderungsverlust_datum` date DEFAULT NULL,
  `forderungsverlust_betrag` decimal(10,2) DEFAULT NULL,
  `steuersatz_normal` decimal(10,2) NOT NULL DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) NOT NULL DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) NOT NULL DEFAULT 7.00,
  `waehrung` varchar(255) NOT NULL DEFAULT 'EUR',
  `keinsteuersatz` int(1) DEFAULT NULL,
  `schreibschutz` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviert` int(1) NOT NULL DEFAULT 0,
  `pdfarchiviertversion` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(255) NOT NULL DEFAULT 'firma',
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `lieferid` int(11) NOT NULL DEFAULT 0,
  `ansprechpartnerid` int(11) NOT NULL DEFAULT 0,
  `systemfreitext` text NOT NULL,
  `projektfiliale` int(11) NOT NULL DEFAULT 0,
  `zuarchivieren` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) NOT NULL,
  `angelegtam` datetime DEFAULT NULL,
  `abweichendebezeichnung` tinyint(1) NOT NULL DEFAULT 0,
  `bezahlt_am` date DEFAULT NULL,
  `sprache` varchar(32) NOT NULL,
  `bundesland` varchar(64) NOT NULL,
  `gln` varchar(64) NOT NULL,
  `deliverythresholdvatid` varchar(64) NOT NULL,
  `bearbeiterid` int(11) DEFAULT NULL,
  `kurs` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `ohne_artikeltext` int(1) DEFAULT NULL,
  `anzeigesteuer` tinyint(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `bodyzusatz` text NOT NULL,
  `lieferbedingung` text NOT NULL,
  `titel` varchar(64) NOT NULL,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontoberechnet` tinyint(1) NOT NULL DEFAULT 0,
  `extsoll` decimal(10,2) NOT NULL DEFAULT 0.00,
  `teilstorno` tinyint(1) NOT NULL DEFAULT 0,
  `bundesstaat` varchar(32) NOT NULL,
  `kundennummer_buchhaltung` varchar(32) NOT NULL,
  `storage_country` varchar(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projekt` (`projekt`),
  KEY `adresse` (`adresse`),
  KEY `auftragid` (`auftragid`),
  KEY `status` (`status`),
  KEY `datum` (`datum`),
  KEY `belegnr` (`belegnr`),
  KEY `soll` (`soll`),
  KEY `zahlungsstatus` (`zahlungsstatus`),
  KEY `provdatum` (`provdatum`),
  KEY `lieferschein` (`lieferschein`),
  KEY `versandart` (`versandart`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rechnung_position`
--

DROP TABLE IF EXISTS `rechnung_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rechnung_position` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rechnung` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `internerkommentar` text NOT NULL,
  `nummer` varchar(255) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `waehrung` varchar(255) NOT NULL,
  `lieferdatum` date NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `status` varchar(64) NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `explodiert_parent_artikel` int(11) NOT NULL DEFAULT 0,
  `punkte` decimal(10,2) NOT NULL,
  `bonuspunkte` decimal(10,2) NOT NULL,
  `mlmdirektpraemie` decimal(10,2) DEFAULT NULL,
  `mlm_abgerechnet` int(1) DEFAULT NULL,
  `keinrabatterlaubt` int(1) DEFAULT NULL,
  `grundrabatt` decimal(10,2) DEFAULT NULL,
  `rabattsync` int(1) DEFAULT NULL,
  `rabatt1` decimal(10,2) DEFAULT NULL,
  `rabatt2` decimal(10,2) DEFAULT NULL,
  `rabatt3` decimal(10,2) DEFAULT NULL,
  `rabatt4` decimal(10,2) DEFAULT NULL,
  `rabatt5` decimal(10,2) DEFAULT NULL,
  `einheit` varchar(255) NOT NULL,
  `rabatt` decimal(10,2) NOT NULL,
  `zolltarifnummer` varchar(128) NOT NULL DEFAULT '0',
  `herkunftsland` varchar(128) NOT NULL DEFAULT '0',
  `artikelnummerkunde` varchar(128) NOT NULL,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `lieferdatumkw` tinyint(1) NOT NULL DEFAULT 0,
  `auftrag_position_id` int(11) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(10) NOT NULL,
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(1024) DEFAULT NULL,
  `erloese` varchar(8) DEFAULT NULL,
  `erloesefestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `einkaufspreiswaehrung` varchar(8) NOT NULL,
  `einkaufspreis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `einkaufspreisurspruenglich` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `einkaufspreisid` int(11) NOT NULL DEFAULT 0,
  `ekwaehrung` varchar(8) NOT NULL,
  `deckungsbeitrag` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `formelmenge` varchar(255) NOT NULL,
  `formelpreis` varchar(255) NOT NULL,
  `ohnepreis` int(1) NOT NULL DEFAULT 0,
  `skontobetrag` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_netto_gesamt` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_einzeln` decimal(14,4) DEFAULT NULL,
  `skontobetrag_brutto_gesamt` decimal(14,4) DEFAULT NULL,
  `steuerbetrag` decimal(14,4) DEFAULT NULL,
  `skontosperre` tinyint(1) NOT NULL DEFAULT 0,
  `ausblenden_im_pdf` tinyint(1) DEFAULT 0,
  `umsatz_netto_einzeln` decimal(14,4) DEFAULT NULL,
  `umsatz_netto_gesamt` decimal(14,4) DEFAULT NULL,
  `umsatz_brutto_einzeln` decimal(14,4) DEFAULT NULL,
  `umsatz_brutto_gesamt` decimal(14,4) DEFAULT NULL,
  `explodiert_parent` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `rechnung` (`rechnung`),
  KEY `artikel` (`artikel`),
  KEY `auftrag_position_id` (`auftrag_position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rechnung_protokoll`
--

DROP TABLE IF EXISTS `rechnung_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rechnung_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rechnung` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rechnung` (`rechnung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reisekosten`
--

DROP TABLE IF EXISTS `reisekosten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reisekosten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `projekt` varchar(222) DEFAULT NULL,
  `teilprojekt` int(11) DEFAULT NULL,
  `prefix` varchar(222) DEFAULT NULL,
  `reisekostenart` varchar(255) DEFAULT NULL,
  `belegnr` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) DEFAULT NULL,
  `auftrag` varchar(255) DEFAULT NULL,
  `auftragid` int(11) DEFAULT NULL,
  `freitext` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `adresse` int(11) DEFAULT NULL,
  `mitarbeiter` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `abteilung` varchar(255) DEFAULT NULL,
  `unterabteilung` varchar(255) DEFAULT NULL,
  `strasse` varchar(255) DEFAULT NULL,
  `adresszusatz` varchar(255) DEFAULT NULL,
  `ansprechpartner` varchar(255) DEFAULT NULL,
  `plz` varchar(255) DEFAULT NULL,
  `ort` varchar(255) DEFAULT NULL,
  `land` varchar(255) DEFAULT NULL,
  `ustid` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefon` varchar(255) DEFAULT NULL,
  `telefax` varchar(255) DEFAULT NULL,
  `betreff` varchar(255) DEFAULT NULL,
  `kundennummer` varchar(255) DEFAULT NULL,
  `versandart` varchar(255) DEFAULT NULL,
  `versand` varchar(255) DEFAULT NULL,
  `firma` int(11) DEFAULT NULL,
  `versendet` int(1) DEFAULT NULL,
  `versendet_am` datetime DEFAULT NULL,
  `versendet_per` varchar(255) DEFAULT NULL,
  `versendet_durch` varchar(255) DEFAULT NULL,
  `inbearbeitung_user` int(1) DEFAULT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `ust_befreit` int(1) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `steuersatz_normal` decimal(10,2) DEFAULT 19.00,
  `steuersatz_zwischen` decimal(10,2) DEFAULT 7.00,
  `steuersatz_ermaessigt` decimal(10,2) DEFAULT 7.00,
  `steuersatz_starkermaessigt` decimal(10,2) DEFAULT 7.00,
  `steuersatz_dienstleistung` decimal(10,2) DEFAULT 7.00,
  `waehrung` varchar(255) DEFAULT 'EUR',
  `anlass` text DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  `von` date DEFAULT NULL,
  `bis` date DEFAULT NULL,
  `von_zeit` varchar(255) DEFAULT NULL,
  `bis_zeit` varchar(255) DEFAULT NULL,
  `schreibschutz` int(1) DEFAULT 0,
  `pdfarchiviert` int(1) DEFAULT 0,
  `pdfarchiviertversion` int(11) DEFAULT 0,
  `typ` varchar(255) DEFAULT 'firma',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reisekosten_position`
--

DROP TABLE IF EXISTS `reisekosten_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reisekosten_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reisekosten` int(11) NOT NULL,
  `reisekostenart` varchar(255) DEFAULT NULL,
  `artikel` varchar(255) DEFAULT NULL,
  `projekt` int(11) DEFAULT NULL,
  `bezeichnung` varchar(255) DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  `ort` text DEFAULT NULL,
  `internerkommentar` text DEFAULT NULL,
  `nummer` varchar(255) DEFAULT NULL,
  `verrechnungsart` varchar(255) DEFAULT NULL,
  `menge` float DEFAULT NULL,
  `arbeitspaket` int(11) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `von` varchar(255) DEFAULT NULL,
  `bis` varchar(255) DEFAULT NULL,
  `sort` int(10) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `bemerkung` text DEFAULT NULL,
  `bezahlt_wie` varchar(255) DEFAULT NULL,
  `uststeuersatz` varchar(255) DEFAULT NULL,
  `keineust` int(1) DEFAULT NULL,
  `betrag` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `abrechnen` int(1) DEFAULT NULL,
  `abgerechnet` int(1) DEFAULT NULL,
  `abgerechnet_objekt` varchar(255) DEFAULT NULL,
  `abgerechnet_parameter` int(11) DEFAULT NULL,
  `exportiert` int(1) DEFAULT NULL,
  `exportiert_am` date DEFAULT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mitarbeiter` int(11) DEFAULT 0,
  `teilprojekt` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `reisekosten` (`reisekosten`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reisekosten_protokoll`
--

DROP TABLE IF EXISTS `reisekosten_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reisekosten_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reisekosten` int(11) DEFAULT NULL,
  `zeit` datetime DEFAULT NULL,
  `bearbeiter` varchar(255) DEFAULT NULL,
  `grund` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reisekosten` (`reisekosten`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reisekostenart`
--

DROP TABLE IF EXISTS `reisekostenart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reisekostenart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nummer` varchar(20) DEFAULT NULL,
  `beschreibung` varchar(512) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `project` int(11) NOT NULL DEFAULT 0,
  `sql_query` text NOT NULL,
  `remark` text DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `readonly` tinyint(4) NOT NULL DEFAULT 0,
  `csv_delimiter` varchar(32) DEFAULT NULL,
  `csv_enclosure` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_column`
--

DROP TABLE IF EXISTS `report_column`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_column` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL DEFAULT 0,
  `key_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `width` varchar(255) NOT NULL,
  `alignment` varchar(255) NOT NULL,
  `sum` tinyint(4) NOT NULL DEFAULT 0,
  `sequence` int(11) NOT NULL DEFAULT 0,
  `sorting` varchar(255) NOT NULL,
  `format_type` varchar(64) DEFAULT NULL,
  `format_statement` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3523 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_favorite`
--

DROP TABLE IF EXISTS `report_favorite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_favorite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_parameter`
--

DROP TABLE IF EXISTS `report_parameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL DEFAULT 0,
  `varname` varchar(255) NOT NULL,
  `displayname` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `default_value` varchar(255) NOT NULL,
  `options` varchar(255) NOT NULL,
  `control_type` varchar(255) NOT NULL,
  `editable` tinyint(4) NOT NULL DEFAULT 0,
  `variable_extern` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=931 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_share`
--

DROP TABLE IF EXISTS `report_share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL DEFAULT 0,
  `chart_public` tinyint(4) NOT NULL DEFAULT 0,
  `chart_axislabel` varchar(255) NOT NULL,
  `chart_type` varchar(255) NOT NULL,
  `chart_x_column` varchar(255) NOT NULL,
  `data_columns` varchar(255) NOT NULL,
  `chart_group_column` varchar(255) NOT NULL,
  `chart_dateformat` varchar(255) NOT NULL,
  `chart_interval_value` int(11) NOT NULL DEFAULT 0,
  `chart_interval_mode` varchar(255) NOT NULL,
  `file_public` tinyint(4) NOT NULL DEFAULT 0,
  `file_pdf_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `file_csv_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `file_xls_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `menu_public` tinyint(4) NOT NULL DEFAULT 0,
  `menu_doctype` varchar(255) NOT NULL,
  `menu_label` varchar(255) NOT NULL,
  `menu_format` varchar(255) NOT NULL,
  `tab_public` tinyint(4) NOT NULL DEFAULT 0,
  `tab_module` varchar(255) NOT NULL,
  `tab_action` varchar(255) NOT NULL,
  `tab_label` varchar(255) NOT NULL,
  `tab_position` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_transfer`
--

DROP TABLE IF EXISTS `report_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL DEFAULT 0,
  `ftp_active` tinyint(4) NOT NULL DEFAULT 0,
  `ftp_type` varchar(255) NOT NULL,
  `ftp_host` varchar(255) NOT NULL,
  `ftp_port` varchar(255) NOT NULL,
  `ftp_user` varchar(255) NOT NULL,
  `ftp_password` varchar(255) NOT NULL,
  `ftp_interval_mode` varchar(255) NOT NULL,
  `ftp_interval_value` varchar(255) NOT NULL,
  `ftp_daytime` time DEFAULT NULL,
  `ftp_format` varchar(255) NOT NULL,
  `ftp_filename` varchar(255) NOT NULL,
  `ftp_last_transfer` datetime DEFAULT NULL,
  `email_active` tinyint(4) NOT NULL DEFAULT 0,
  `email_recipient` text NOT NULL,
  `email_subject` varchar(255) NOT NULL,
  `email_interval_mode` varchar(255) NOT NULL,
  `email_interval_value` varchar(255) NOT NULL,
  `email_daytime` time DEFAULT NULL,
  `email_format` varchar(255) NOT NULL,
  `email_filename` varchar(255) NOT NULL,
  `email_last_transfer` datetime DEFAULT NULL,
  `url_format` varchar(255) NOT NULL,
  `url_begin` date DEFAULT NULL,
  `url_end` date DEFAULT NULL,
  `url_address` text NOT NULL,
  `url_token` text NOT NULL,
  `api_active` tinyint(4) NOT NULL DEFAULT 0,
  `api_account_id` int(11) NOT NULL DEFAULT 0,
  `api_format` varchar(255) NOT NULL,
  `ftp_passive` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_user`
--

DROP TABLE IF EXISTS `report_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `chart_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `file_enabled` int(11) NOT NULL DEFAULT 0,
  `menu_enabled` int(11) NOT NULL DEFAULT 0,
  `tab_enabled` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `retoure`
--

DROP TABLE IF EXISTS `retoure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retoure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `projekt` varchar(222) DEFAULT NULL,
  `belegnr` varchar(255) DEFAULT NULL,
  `bearbeiter` varchar(255) DEFAULT NULL,
  `lieferschein` varchar(255) DEFAULT NULL,
  `lieferscheinid` int(11) DEFAULT NULL,
  `auftrag` varchar(255) DEFAULT NULL,
  `auftragid` int(11) DEFAULT NULL,
  `freitext` text DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `adresse` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `abteilung` varchar(255) DEFAULT NULL,
  `unterabteilung` varchar(255) DEFAULT NULL,
  `strasse` varchar(255) DEFAULT NULL,
  `adresszusatz` varchar(255) DEFAULT NULL,
  `ansprechpartner` varchar(255) DEFAULT NULL,
  `plz` varchar(255) DEFAULT NULL,
  `ort` varchar(255) DEFAULT NULL,
  `land` varchar(255) DEFAULT NULL,
  `abweichendelieferadresse` int(11) DEFAULT 0,
  `liefername` varchar(255) DEFAULT NULL,
  `lieferabteilung` varchar(255) DEFAULT NULL,
  `lieferunterabteilung` varchar(255) DEFAULT NULL,
  `lieferstrasse` varchar(255) DEFAULT NULL,
  `lieferadresszusatz` varchar(255) DEFAULT NULL,
  `lieferansprechpartner` varchar(255) DEFAULT NULL,
  `lieferplz` varchar(255) DEFAULT NULL,
  `lieferort` varchar(255) DEFAULT NULL,
  `lieferland` varchar(255) DEFAULT NULL,
  `ustid` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefon` varchar(255) DEFAULT NULL,
  `telefax` varchar(255) DEFAULT NULL,
  `betreff` varchar(255) DEFAULT NULL,
  `kundennummer` varchar(64) DEFAULT NULL,
  `versandart` varchar(255) DEFAULT NULL,
  `versand` varchar(255) DEFAULT NULL,
  `firma` int(11) DEFAULT NULL,
  `versendet` int(1) DEFAULT NULL,
  `versendet_am` datetime DEFAULT NULL,
  `versendet_per` varchar(255) DEFAULT NULL,
  `versendet_durch` varchar(255) DEFAULT NULL,
  `inbearbeitung_user` int(1) DEFAULT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `vertriebid` int(11) DEFAULT NULL,
  `vertrieb` varchar(255) DEFAULT NULL,
  `ust_befreit` int(1) DEFAULT NULL,
  `ihrebestellnummer` varchar(255) DEFAULT NULL,
  `anschreiben` varchar(255) DEFAULT NULL,
  `usereditid` int(11) DEFAULT NULL,
  `useredittimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lieferantenretoure` tinyint(1) DEFAULT 0,
  `lieferantenretoureinfo` text DEFAULT NULL,
  `lieferant` int(11) DEFAULT 0,
  `schreibschutz` int(1) DEFAULT 0,
  `pdfarchiviert` int(1) DEFAULT 0,
  `pdfarchiviertversion` int(11) DEFAULT 0,
  `typ` varchar(255) DEFAULT 'firma',
  `internebemerkung` text DEFAULT NULL,
  `ohne_briefpapier` int(1) DEFAULT NULL,
  `lieferid` int(11) DEFAULT 0,
  `ansprechpartnerid` int(11) DEFAULT 0,
  `projektfiliale` int(11) DEFAULT 0,
  `projektfiliale_eingelagert` tinyint(1) DEFAULT 0,
  `zuarchivieren` int(11) DEFAULT 0,
  `internebezeichnung` varchar(255) DEFAULT NULL,
  `angelegtam` datetime DEFAULT NULL,
  `kommissionierung` int(11) DEFAULT 0,
  `sprache` varchar(32) DEFAULT NULL,
  `bundesland` varchar(64) DEFAULT NULL,
  `gln` varchar(64) DEFAULT NULL,
  `rechnungid` int(11) DEFAULT 0,
  `bearbeiterid` int(11) DEFAULT NULL,
  `keinerechnung` tinyint(1) DEFAULT 0,
  `ohne_artikeltext` int(1) DEFAULT NULL,
  `abweichendebezeichnung` tinyint(1) DEFAULT 0,
  `bodyzusatz` text DEFAULT NULL,
  `lieferbedingung` text DEFAULT NULL,
  `titel` varchar(64) DEFAULT NULL,
  `standardlager` int(11) DEFAULT 0,
  `kommissionskonsignationslager` int(11) DEFAULT 0,
  `bundesstaat` varchar(32) DEFAULT NULL,
  `teillieferungvon` int(11) DEFAULT 0,
  `teillieferungnummer` int(11) DEFAULT 0,
  `gutschrift_id` int(11) NOT NULL DEFAULT 0,
  `fortschritt` varchar(16) NOT NULL,
  `storage_ok` tinyint(11) NOT NULL DEFAULT 0,
  `replacementorder_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`),
  KEY `status` (`status`),
  KEY `versandart` (`versandart`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `retoure_position`
--

DROP TABLE IF EXISTS `retoure_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retoure_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `retoure` int(11) DEFAULT NULL,
  `artikel` int(11) DEFAULT NULL,
  `projekt` int(11) DEFAULT NULL,
  `bezeichnung` varchar(255) DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  `internerkommentar` text DEFAULT NULL,
  `nummer` varchar(255) DEFAULT NULL,
  `seriennummer` varchar(255) DEFAULT NULL,
  `menge` decimal(14,4) DEFAULT NULL,
  `lieferdatum` date DEFAULT NULL,
  `vpe` varchar(255) DEFAULT NULL,
  `sort` int(10) DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `bemerkung` text DEFAULT NULL,
  `geliefert` decimal(14,4) DEFAULT NULL,
  `abgerechnet` int(1) DEFAULT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `explodiert_parent_artikel` int(11) DEFAULT 0,
  `einheit` varchar(255) DEFAULT NULL,
  `zolltarifnummer` varchar(128) DEFAULT '0',
  `herkunftsland` varchar(128) DEFAULT '0',
  `artikelnummerkunde` varchar(128) DEFAULT NULL,
  `freifeld1` text DEFAULT NULL,
  `freifeld2` text DEFAULT NULL,
  `freifeld3` text DEFAULT NULL,
  `freifeld4` text DEFAULT NULL,
  `freifeld5` text DEFAULT NULL,
  `freifeld6` text DEFAULT NULL,
  `freifeld7` text DEFAULT NULL,
  `freifeld8` text DEFAULT NULL,
  `freifeld9` text DEFAULT NULL,
  `freifeld10` text DEFAULT NULL,
  `freifeld11` text DEFAULT NULL,
  `freifeld12` text DEFAULT NULL,
  `freifeld13` text DEFAULT NULL,
  `freifeld14` text DEFAULT NULL,
  `freifeld15` text DEFAULT NULL,
  `freifeld16` text DEFAULT NULL,
  `freifeld17` text DEFAULT NULL,
  `freifeld18` text DEFAULT NULL,
  `freifeld19` text DEFAULT NULL,
  `freifeld20` text DEFAULT NULL,
  `freifeld21` text DEFAULT NULL,
  `freifeld22` text DEFAULT NULL,
  `freifeld23` text DEFAULT NULL,
  `freifeld24` text DEFAULT NULL,
  `freifeld25` text DEFAULT NULL,
  `freifeld26` text DEFAULT NULL,
  `freifeld27` text DEFAULT NULL,
  `freifeld28` text DEFAULT NULL,
  `freifeld29` text DEFAULT NULL,
  `freifeld30` text DEFAULT NULL,
  `freifeld31` text DEFAULT NULL,
  `freifeld32` text DEFAULT NULL,
  `freifeld33` text DEFAULT NULL,
  `freifeld34` text DEFAULT NULL,
  `freifeld35` text DEFAULT NULL,
  `freifeld36` text DEFAULT NULL,
  `freifeld37` text DEFAULT NULL,
  `freifeld38` text DEFAULT NULL,
  `freifeld39` text DEFAULT NULL,
  `freifeld40` text DEFAULT NULL,
  `lieferdatumkw` tinyint(1) DEFAULT 0,
  `auftrag_position_id` int(11) DEFAULT 0,
  `lieferschein_position_id` int(11) DEFAULT 0,
  `kostenlos` tinyint(1) DEFAULT 0,
  `lagertext` varchar(255) DEFAULT NULL,
  `teilprojekt` int(11) DEFAULT 0,
  `explodiert_parent` int(11) DEFAULT 0,
  `ausblenden_im_pdf` tinyint(1) DEFAULT 0,
  `grund` varchar(255) DEFAULT NULL,
  `grundbeschreibung` text DEFAULT NULL,
  `aktion` varchar(255) DEFAULT NULL,
  `aktionbeschreibung` text DEFAULT NULL,
  `menge_eingang` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `menge_gutschrift` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `default_storagelocation` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `retoure` (`retoure`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `retoure_protokoll`
--

DROP TABLE IF EXISTS `retoure_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retoure_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `retoure` int(11) DEFAULT NULL,
  `zeit` datetime DEFAULT NULL,
  `bearbeiter` varchar(255) DEFAULT NULL,
  `grund` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `retoure` (`retoure`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `returnorder_quantity`
--

DROP TABLE IF EXISTS `returnorder_quantity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `returnorder_quantity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `delivery_note_id` int(11) NOT NULL DEFAULT 0,
  `quantity` decimal(14,4) DEFAULT NULL,
  `serialnumber` varchar(255) NOT NULL,
  `batch` varchar(255) NOT NULL,
  `bestbefore` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_note_id` (`delivery_note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rma`
--

DROP TABLE IF EXISTS `rma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `projekt` varchar(222) NOT NULL,
  `belegnr` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `freigabe` int(1) NOT NULL,
  `status` varchar(64) NOT NULL,
  `adresse` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `abteilung` varchar(255) NOT NULL,
  `unterabteilung` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `adresszusatz` varchar(255) NOT NULL,
  `plz` varchar(64) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `ustid` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `telefax` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `kundennummer` varchar(255) NOT NULL,
  `lieferantennummer` varchar(255) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `zahlungszieltage` int(11) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `bestellbestaetigung` int(1) NOT NULL,
  `freitext` varchar(255) NOT NULL,
  `zahlungszielskonto` int(11) NOT NULL,
  `zahlungszieltageskonto` int(11) NOT NULL,
  `bestellbestaetigungsdatum` date NOT NULL,
  `lieferdatum` date NOT NULL,
  `einkaeufer` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rma_artikel`
--

DROP TABLE IF EXISTS `rma_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rma_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `wareneingang` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `lieferschein` int(11) NOT NULL,
  `pos` int(11) NOT NULL,
  `wunsch` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `artikel` int(11) NOT NULL,
  `status` varchar(64) NOT NULL,
  `angelegtam` date NOT NULL,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `techniker` text NOT NULL,
  `buchhaltung` text NOT NULL,
  `abgeschlossen` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `seriennummer` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rma_position`
--

DROP TABLE IF EXISTS `rma_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rma_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rma_artikel` int(15) DEFAULT 0,
  `status` varchar(255) DEFAULT 'offen',
  `sort` int(15) DEFAULT 0,
  `letzter_kommentar` int(11) DEFAULT 0,
  `bearbeiter` varchar(255) DEFAULT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rma_protokoll`
--

DROP TABLE IF EXISTS `rma_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rma_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rma_artikel` int(15) DEFAULT 0,
  `rma_position` int(15) DEFAULT 0,
  `kommentar` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `interngrund` text DEFAULT NULL,
  `externgrund` text DEFAULT NULL,
  `bearbeiter` varchar(255) DEFAULT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rma_vorlagen_grund`
--

DROP TABLE IF EXISTS `rma_vorlagen_grund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rma_vorlagen_grund` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `sprache` varchar(10) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `ausblenden` tinyint(1) NOT NULL DEFAULT 0,
  `rmakategorie` int(11) NOT NULL DEFAULT 0,
  `default_storagelocation` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `rmakategorie` (`rmakategorie`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rma_vorlagen_kategorien`
--

DROP TABLE IF EXISTS `rma_vorlagen_kategorien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rma_vorlagen_kategorien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `aktion` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rohstoffe`
--

DROP TABLE IF EXISTS `rohstoffe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rohstoffe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menge` decimal(18,8) NOT NULL,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `rohstoffvonartikel` int(11) NOT NULL DEFAULT 0,
  `lagerwert` tinyint(1) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `referenz` text DEFAULT NULL,
  `art` varchar(64) NOT NULL DEFAULT 'material',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sammelrechnung_position`
--

DROP TABLE IF EXISTS `sammelrechnung_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sammelrechnung_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `rechnung` int(11) NOT NULL DEFAULT 0,
  `menge` float NOT NULL DEFAULT 0,
  `rechnung_position_id` int(11) NOT NULL DEFAULT 0,
  `auftrag_position_id` int(11) NOT NULL DEFAULT 0,
  `lieferschein_position_id` int(11) NOT NULL DEFAULT 0,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `auswahl` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `lieferschein_position_id` (`lieferschein_position_id`),
  KEY `auftrag_position_id` (`auftrag_position_id`),
  KEY `rechnung` (`rechnung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheck_checked`
--

DROP TABLE IF EXISTS `scheck_checked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheck_checked` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gutschrift` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheck_druck`
--

DROP TABLE IF EXISTS `scheck_druck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheck_druck` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bearbeiter` int(11) NOT NULL DEFAULT 0,
  `kommentar` varchar(255) NOT NULL,
  `layout` int(11) NOT NULL DEFAULT 0,
  `konto` int(11) NOT NULL DEFAULT 0,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scheck_gutschrift`
--

DROP TABLE IF EXISTS `scheck_gutschrift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheck_gutschrift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gutschrift` int(11) NOT NULL DEFAULT 0,
  `druck` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seriennummern`
--

DROP TABLE IF EXISTS `seriennummern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seriennummern` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seriennummer` varchar(255) NOT NULL,
  `artikel` int(11) NOT NULL,
  `beschreibung` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `logdatei` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seriennummern_log`
--

DROP TABLE IF EXISTS `seriennummern_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seriennummern_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `lager_platz` int(11) NOT NULL DEFAULT 0,
  `eingang` int(1) NOT NULL DEFAULT 0,
  `bezeichnung` text NOT NULL,
  `internebemerkung` text NOT NULL,
  `zeit` datetime DEFAULT NULL,
  `adresse_mitarbeiter` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `doctype` varchar(32) NOT NULL,
  `doctypeid` int(11) NOT NULL DEFAULT 0,
  `bestbeforedate` date DEFAULT NULL,
  `batch` varchar(255) DEFAULT NULL,
  `storage_movement_id` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `zuweisen` int(11) NOT NULL DEFAULT 0,
  `ansprechpartner` varchar(255) NOT NULL,
  `nummer` varchar(64) DEFAULT NULL,
  `prio` varchar(10) NOT NULL DEFAULT 'niedrig',
  `eingangart` varchar(10) NOT NULL,
  `datum` datetime DEFAULT NULL,
  `erledigenbis` date DEFAULT NULL,
  `betreff` varchar(255) NOT NULL,
  `beschreibung_html` longtext NOT NULL,
  `internebemerkung` longtext NOT NULL,
  `antwortankunden` longtext NOT NULL,
  `angelegtvonuser` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'angelegt',
  `artikel` int(11) NOT NULL DEFAULT 0,
  `seriennummer` varchar(255) NOT NULL,
  `antwortpermail` tinyint(1) NOT NULL DEFAULT 0,
  `bezahlte_zusatzleistung` tinyint(1) NOT NULL DEFAULT 0,
  `freigabe` tinyint(1) NOT NULL DEFAULT 0,
  `freigabe_datum` datetime DEFAULT NULL,
  `freigabe_bearbeiter` int(11) NOT NULL DEFAULT 0,
  `dauer_geplant` decimal(10,2) NOT NULL DEFAULT 0.00,
  `art` varchar(64) NOT NULL,
  `bereich` varchar(64) NOT NULL,
  `freifeld1` text NOT NULL,
  `freifeld2` text NOT NULL,
  `freifeld3` text NOT NULL,
  `freifeld4` text NOT NULL,
  `freifeld5` text NOT NULL,
  `version` text NOT NULL,
  `antwortankundenempfaenger` varchar(64) NOT NULL,
  `antwortankundenkopie` varchar(64) NOT NULL,
  `antwortankundenblindkopie` varchar(64) NOT NULL,
  `antwortankundenbetreff` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nummer` (`nummer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sevensenders_shipment`
--

DROP TABLE IF EXISTS `sevensenders_shipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sevensenders_shipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT current_timestamp(),
  `lieferschein` int(11) DEFAULT NULL,
  `carrier` text DEFAULT NULL,
  `shipment_id` text DEFAULT NULL,
  `shipment_reference` text DEFAULT NULL,
  `tracking` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport`
--

DROP TABLE IF EXISTS `shopexport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `typ` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `challenge` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `cms` int(1) NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `geloescht` int(1) NOT NULL DEFAULT 0,
  `artikelporto` int(11) NOT NULL DEFAULT 0,
  `artikelnachnahme` int(11) NOT NULL DEFAULT 0,
  `artikelimport` int(1) NOT NULL DEFAULT 0,
  `artikelimporteinzeln` int(1) NOT NULL DEFAULT 0,
  `demomodus` tinyint(1) NOT NULL DEFAULT 0,
  `aktiv` int(1) NOT NULL DEFAULT 1,
  `lagerexport` int(1) NOT NULL DEFAULT 1,
  `artikelexport` int(1) NOT NULL DEFAULT 1,
  `multiprojekt` int(1) NOT NULL DEFAULT 0,
  `artikelnachnahme_extraartikel` tinyint(1) NOT NULL DEFAULT 1,
  `vorabbezahltmarkieren_ohnevorkasse_bar` int(11) NOT NULL DEFAULT 0,
  `einzelsync` tinyint(1) NOT NULL DEFAULT 0,
  `utf8codierung` tinyint(1) NOT NULL DEFAULT 1,
  `auftragabgleich` int(1) NOT NULL DEFAULT 0,
  `rabatteportofestschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `artikelnummernummerkreis` tinyint(1) NOT NULL DEFAULT 0,
  `holealle` tinyint(1) NOT NULL DEFAULT 0,
  `ab_nummer` varchar(255) NOT NULL,
  `direktimport` tinyint(1) NOT NULL DEFAULT 0,
  `ust_ok` tinyint(1) NOT NULL DEFAULT 0,
  `anzgleichzeitig` int(15) NOT NULL DEFAULT 1,
  `datumvon` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datumbis` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tmpdatumvon` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tmpdatumbis` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `holeallestati` tinyint(1) NOT NULL DEFAULT 1,
  `cronjobaktiv` tinyint(1) NOT NULL DEFAULT 0,
  `nummersyncstatusaendern` tinyint(1) NOT NULL DEFAULT 0,
  `zahlungsweisenmapping` tinyint(1) NOT NULL DEFAULT 0,
  `versandartenmapping` tinyint(1) NOT NULL DEFAULT 0,
  `artikelnummeruebernehmen` tinyint(1) NOT NULL DEFAULT 0,
  `artikelbeschreibungauswawision` tinyint(1) NOT NULL DEFAULT 0,
  `artikelbeschreibungenuebernehmen` tinyint(1) NOT NULL DEFAULT 0,
  `stuecklisteergaenzen` tinyint(1) NOT NULL DEFAULT 0,
  `adressupdate` tinyint(1) NOT NULL DEFAULT 0,
  `kundenurvonprojekt` tinyint(1) NOT NULL DEFAULT 0,
  `add_debitorennummer` tinyint(1) NOT NULL DEFAULT 0,
  `debitorennummer` varchar(16) NOT NULL,
  `sendonlywithtracking` tinyint(1) NOT NULL DEFAULT 0,
  `api_account_id` int(10) NOT NULL DEFAULT 0,
  `api_account_token` varchar(1024) NOT NULL,
  `autosendarticle` tinyint(1) NOT NULL DEFAULT 0,
  `autosendarticle_last` timestamp NULL DEFAULT NULL,
  `shopbilderuebertragen` tinyint(1) NOT NULL DEFAULT 0,
  `adressennichtueberschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `auftraegeaufspaeter` tinyint(1) NOT NULL DEFAULT 0,
  `autoversandbeikommentardeaktivieren` tinyint(1) NOT NULL DEFAULT 0,
  `artikeltexteuebernehmen` tinyint(1) NOT NULL DEFAULT 1,
  `artikelportoermaessigt` int(11) NOT NULL DEFAULT 0,
  `artikelrabatt` int(11) NOT NULL DEFAULT 0,
  `artikelrabattsteuer` decimal(4,2) NOT NULL DEFAULT -1.00,
  `positionsteuersaetzeerlauben` tinyint(1) NOT NULL DEFAULT 0,
  `json` text NOT NULL,
  `freitext` varchar(64) NOT NULL,
  `artikelbezeichnungauswawision` tinyint(1) NOT NULL DEFAULT 0,
  `angeboteanlegen` tinyint(1) NOT NULL DEFAULT 0,
  `autoversandoption` varchar(255) NOT NULL DEFAULT 'standard',
  `artikelnummerbeimanlegenausshop` tinyint(1) NOT NULL DEFAULT 0,
  `shoptyp` varchar(32) NOT NULL,
  `modulename` varchar(64) NOT NULL,
  `einstellungen_json` mediumtext NOT NULL,
  `maxmanuell` int(11) NOT NULL DEFAULT 0,
  `preisgruppe` int(11) NOT NULL DEFAULT 0,
  `variantenuebertragen` tinyint(1) NOT NULL DEFAULT 1,
  `crosssellingartikeluebertragen` tinyint(1) NOT NULL DEFAULT 1,
  `staffelpreiseuebertragen` tinyint(1) NOT NULL DEFAULT 1,
  `lagergrundlage` tinyint(1) NOT NULL DEFAULT 0,
  `portoartikelanlegen` tinyint(1) NOT NULL DEFAULT 0,
  `nurneueartikel` tinyint(1) NOT NULL DEFAULT 1,
  `startdate` date DEFAULT NULL,
  `ueberschreibe_lagerkorrekturwert` tinyint(1) NOT NULL DEFAULT 0,
  `lagerkorrekturwert` int(11) NOT NULL DEFAULT 0,
  `vertrieb` int(11) NOT NULL DEFAULT 0,
  `eigenschaftenuebertragen` tinyint(1) NOT NULL DEFAULT 0,
  `kategorienuebertragen` tinyint(1) NOT NULL DEFAULT 0,
  `stornoabgleich` tinyint(1) NOT NULL DEFAULT 0,
  `nurpreise` tinyint(1) NOT NULL DEFAULT 0,
  `steuerfreilieferlandexport` tinyint(1) NOT NULL DEFAULT 1,
  `gutscheineuebertragen` tinyint(1) NOT NULL DEFAULT 0,
  `gesamtbetragfestsetzen` tinyint(1) NOT NULL DEFAULT 0,
  `lastschriftdatenueberschreiben` tinyint(1) NOT NULL DEFAULT 0,
  `gesamtbetragfestsetzendifferenz` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_adressenuebertragen`
--

DROP TABLE IF EXISTS `shopexport_adressenuebertragen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_adressenuebertragen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL,
  `adresse` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_archiv`
--

DROP TABLE IF EXISTS `shopexport_archiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_archiv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL DEFAULT 0,
  `anzahl` int(11) NOT NULL DEFAULT 0,
  `erfolgreich` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL,
  `letzteabgeholtenummer` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `datumvon` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `datumbis` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `nummervon` varchar(255) NOT NULL,
  `nummerbis` varchar(255) NOT NULL,
  `abschliessen` tinyint(1) NOT NULL DEFAULT 1,
  `stornierteabholen` tinyint(1) NOT NULL DEFAULT 1,
  `rechnung_erzeugen` tinyint(1) NOT NULL DEFAULT 1,
  `rechnung_bezahlt` tinyint(1) NOT NULL DEFAULT 1,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `donotimport` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `shop` (`shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_artikel`
--

DROP TABLE IF EXISTS `shopexport_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopid` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `wert` text NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shopid` (`shopid`,`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_artikeluebertragen`
--

DROP TABLE IF EXISTS `shopexport_artikeluebertragen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_artikeluebertragen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `check_nr` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_artikeluebertragen_check`
--

DROP TABLE IF EXISTS `shopexport_artikeluebertragen_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_artikeluebertragen_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_change_log`
--

DROP TABLE IF EXISTS `shopexport_change_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_change_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `diff` text NOT NULL,
  `creation_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `message` varchar(255) NOT NULL,
  `plaindiff` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_freifelder`
--

DROP TABLE IF EXISTS `shopexport_freifelder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_freifelder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL,
  `freifeld_wawi` varchar(255) NOT NULL,
  `freifeld_shop` varchar(255) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NULL DEFAULT NULL,
  `updatedby` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_getarticles`
--

DROP TABLE IF EXISTS `shopexport_getarticles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_getarticles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL,
  `nummer` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shop` (`shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_kampange`
--

DROP TABLE IF EXISTS `shopexport_kampange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_kampange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `banner` int(11) NOT NULL,
  `unterbanner` int(11) NOT NULL,
  `von` date NOT NULL,
  `bis` date NOT NULL,
  `link` text NOT NULL,
  `firma` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  `aktiv` int(1) NOT NULL,
  `shop` int(11) NOT NULL,
  `artikel` varchar(255) NOT NULL,
  `aktion` varchar(255) NOT NULL,
  `geloescht` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_kategorien`
--

DROP TABLE IF EXISTS `shopexport_kategorien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_kategorien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL DEFAULT 0,
  `kategorie` int(11) NOT NULL DEFAULT 0,
  `extsort` int(11) NOT NULL DEFAULT 0,
  `extid` varchar(255) NOT NULL,
  `extparent` varchar(255) NOT NULL,
  `extname` varchar(255) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NULL DEFAULT NULL,
  `updatedby` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shop` (`shop`),
  KEY `kategorie` (`kategorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_kundengruppen`
--

DROP TABLE IF EXISTS `shopexport_kundengruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_kundengruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopid` int(11) NOT NULL DEFAULT 0,
  `gruppeid` int(11) NOT NULL DEFAULT 0,
  `apply_to_new_customers` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL DEFAULT 'mitglied',
  `extgruppename` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NULL DEFAULT NULL,
  `updatedby` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_log`
--

DROP TABLE IF EXISTS `shopexport_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopid` int(11) NOT NULL DEFAULT 0,
  `typ` varchar(64) NOT NULL,
  `parameter1` text NOT NULL,
  `parameter2` text NOT NULL,
  `bearbeiter` varchar(64) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `parameter3` varchar(255) NOT NULL,
  `parameter4` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shopid` (`shopid`,`typ`,`parameter3`,`parameter4`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_mapping`
--

DROP TABLE IF EXISTS `shopexport_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL DEFAULT 0,
  `tabelle` varchar(255) NOT NULL,
  `intid` int(11) NOT NULL DEFAULT 0,
  `intid2` int(11) NOT NULL DEFAULT 0,
  `extid` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop` (`shop`),
  KEY `tabelle` (`tabelle`),
  KEY `intid` (`intid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_sprachen`
--

DROP TABLE IF EXISTS `shopexport_sprachen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_sprachen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL DEFAULT 0,
  `land` varchar(32) NOT NULL,
  `sprache` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NULL DEFAULT NULL,
  `updatedby` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_status`
--

DROP TABLE IF EXISTS `shopexport_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikelexport` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `zeit` datetime NOT NULL,
  `bemerkung` text NOT NULL,
  `befehl` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_subshop`
--

DROP TABLE IF EXISTS `shopexport_subshop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_subshop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `subshopkennung` varchar(255) NOT NULL,
  `sprache` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NULL DEFAULT NULL,
  `updatedby` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_versandarten`
--

DROP TABLE IF EXISTS `shopexport_versandarten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_versandarten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL,
  `versandart_shop` varchar(255) NOT NULL,
  `versandart_wawision` varchar(255) NOT NULL,
  `autoversand` int(11) NOT NULL DEFAULT 0,
  `land` text NOT NULL,
  `aktiv` int(11) NOT NULL DEFAULT 0,
  `versandart_ausgehend` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NULL DEFAULT NULL,
  `updatedby` varchar(255) NOT NULL,
  `fastlane` tinyint(1) NOT NULL DEFAULT 0,
  `produkt_ausgehend` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_voucher_cache`
--

DROP TABLE IF EXISTS `shopexport_voucher_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_voucher_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_id` int(11) NOT NULL,
  `value` float NOT NULL DEFAULT 0,
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `voucher_id` (`voucher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_zahlungsstatus`
--

DROP TABLE IF EXISTS `shopexport_zahlungsstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_zahlungsstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL DEFAULT 0,
  `auftrag` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop` (`shop`),
  KEY `auftrag` (`auftrag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopexport_zahlweisen`
--

DROP TABLE IF EXISTS `shopexport_zahlweisen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopexport_zahlweisen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL,
  `zahlweise_shop` varchar(255) NOT NULL,
  `zahlweise_wawision` varchar(255) NOT NULL,
  `vorabbezahltmarkieren` int(11) NOT NULL DEFAULT 0,
  `autoversand` int(11) NOT NULL DEFAULT 0,
  `aktiv` int(11) NOT NULL DEFAULT 0,
  `keinerechnung` tinyint(1) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedby` varchar(255) NOT NULL,
  `fastlane` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimport_amazon_aufrufe`
--

DROP TABLE IF EXISTS `shopimport_amazon_aufrufe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimport_amazon_aufrufe` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `shop` varchar(100) NOT NULL,
  `funktion` varchar(100) NOT NULL,
  `daten` mediumtext NOT NULL,
  `abgeschlossen` int(1) NOT NULL DEFAULT 0,
  `maxpuffer` int(3) NOT NULL,
  `timeout` int(4) NOT NULL,
  `zeitstempel` datetime DEFAULT NULL,
  `ausgefuehrt` int(1) DEFAULT 0,
  `preismenge` int(1) DEFAULT NULL,
  `apifunktion` varchar(100) NOT NULL,
  `feedid` varchar(50) DEFAULT NULL,
  `relatedtoid` int(4) DEFAULT NULL,
  `inbearbeitung` int(1) DEFAULT 0,
  `fehlertext` text DEFAULT NULL,
  `shopid` int(11) NOT NULL DEFAULT 0,
  `json_encoded` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimport_amazon_gotorders`
--

DROP TABLE IF EXISTS `shopimport_amazon_gotorders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimport_amazon_gotorders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` varchar(30) DEFAULT NULL,
  `orderitemid` varchar(30) DEFAULT NULL,
  `nextordertoken` varchar(30) DEFAULT NULL,
  `nextitemtoken` varchar(30) DEFAULT NULL,
  `zeitstempel` datetime DEFAULT NULL,
  `tracking` varchar(64) DEFAULT NULL,
  `sent` tinyint(1) DEFAULT 0,
  `shopid` int(11) NOT NULL DEFAULT 0,
  `isprime` tinyint(1) DEFAULT -1,
  `isprimenextday` tinyint(1) DEFAULT -1,
  `imported` tinyint(1) DEFAULT 1,
  `isfba` tinyint(1) DEFAULT -1,
  `marketplace` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimport_amazon_throttling`
--

DROP TABLE IF EXISTS `shopimport_amazon_throttling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimport_amazon_throttling` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apifunktion` varchar(100) DEFAULT NULL,
  `shop` varchar(100) DEFAULT NULL,
  `typ` varchar(50) DEFAULT NULL,
  `max` int(3) DEFAULT NULL,
  `stunde` int(3) DEFAULT NULL,
  `restore` float DEFAULT NULL,
  `zaehlermax` int(3) DEFAULT NULL,
  `zaehlerstunde` int(3) DEFAULT NULL,
  `ersteraufruf` datetime DEFAULT NULL,
  `letzteraufruf` datetime DEFAULT NULL,
  `maxpuffer` int(3) DEFAULT NULL,
  `minpuffer` int(3) DEFAULT NULL,
  `timeout` int(4) DEFAULT NULL,
  `zaehleraufrufe` int(3) DEFAULT 0,
  `zeitstempel` datetime DEFAULT NULL,
  `shopid` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `shopid` (`shopid`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimport_auftraege`
--

DROP TABLE IF EXISTS `shopimport_auftraege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimport_auftraege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopid` int(11) NOT NULL DEFAULT 0,
  `extid` varchar(255) NOT NULL,
  `sessionid` varchar(255) NOT NULL,
  `warenkorb` mediumtext NOT NULL,
  `imported` int(1) NOT NULL,
  `trash` int(1) NOT NULL,
  `projekt` int(11) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `logdatei` datetime NOT NULL,
  `bestellnummer` varchar(255) DEFAULT NULL,
  `jsonencoded` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimport_checkorder`
--

DROP TABLE IF EXISTS `shopimport_checkorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimport_checkorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `ext_order` varchar(255) NOT NULL DEFAULT '0',
  `fetch_counter` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'unpaid',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_last_modified` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_attachedoffers`
--

DROP TABLE IF EXISTS `shopimporter_amazon_attachedoffers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_attachedoffers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `is_fba` tinyint(1) NOT NULL DEFAULT 0,
  `marketplace` varchar(16) NOT NULL,
  `title` varchar(255) NOT NULL,
  `merchantgroup` varchar(255) NOT NULL,
  `condition` varchar(32) NOT NULL DEFAULT 'new',
  `sku` varchar(64) NOT NULL,
  `asin` varchar(16) NOT NULL,
  `status` varchar(32) NOT NULL,
  `currency` varchar(4) NOT NULL,
  `status_article` int(1) NOT NULL DEFAULT 0,
  `status_storage` int(1) NOT NULL DEFAULT 0,
  `status_price` int(1) NOT NULL DEFAULT 0,
  `status_flat` int(1) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `feed_submission_id` varchar(32) NOT NULL,
  `feed_submission_id_price` varchar(32) NOT NULL,
  `feed_submission_id_storage` varchar(32) NOT NULL,
  `feed_submission_id_flat` varchar(32) NOT NULL,
  `error_code` varchar(8) NOT NULL,
  `error_message` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`),
  KEY `article_id` (`article_id`),
  KEY `sku` (`sku`),
  KEY `asin` (`asin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_browsetree`
--

DROP TABLE IF EXISTS `shopimporter_amazon_browsetree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_browsetree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `browsenodeid` varchar(32) DEFAULT NULL,
  `marketplace` varchar(32) DEFAULT NULL,
  `parent_id` int(11) DEFAULT 0,
  `browseNodename` varchar(255) DEFAULT NULL,
  `browseNodestorecontextname` varchar(255) DEFAULT NULL,
  `browsepathbyid` varchar(1024) DEFAULT NULL,
  `browsepathbyname` varchar(1024) DEFAULT NULL,
  `haschildren` tinyint(1) DEFAULT 0,
  `producttypedefinitions` varchar(255) DEFAULT NULL,
  `refinementsinformationcount` int(11) DEFAULT 0,
  `deprecated` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `browsenodeid` (`browsenodeid`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68633 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_categorie`
--

DROP TABLE IF EXISTS `shopimporter_amazon_categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `root_node` varchar(32) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `node_de` varchar(32) DEFAULT NULL,
  `node_uk` varchar(32) DEFAULT NULL,
  `node_fr` varchar(32) DEFAULT NULL,
  `node_it` varchar(32) DEFAULT NULL,
  `node_es` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `node_de` (`node_de`)
) ENGINE=InnoDB AUTO_INCREMENT=16081 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_creditnotes_adjustmentid`
--

DROP TABLE IF EXISTS `shopimporter_amazon_creditnotes_adjustmentid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_creditnotes_adjustmentid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `creditnote_id` int(11) NOT NULL DEFAULT 0,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `invoice_id` int(11) NOT NULL DEFAULT 0,
  `adjustmentid` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`),
  KEY `creditnote_id` (`creditnote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_feedsubmission`
--

DROP TABLE IF EXISTS `shopimporter_amazon_feedsubmission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_feedsubmission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT 0,
  `feed_submission_id` varchar(32) DEFAULT NULL,
  `feed_type` varchar(64) DEFAULT NULL,
  `feed_processing_status` varchar(32) DEFAULT NULL,
  `parameter` varchar(32) DEFAULT NULL,
  `started_processing_date` datetime DEFAULT NULL,
  `submitted_date` datetime DEFAULT NULL,
  `completed_processing_date` datetime DEFAULT NULL,
  `lastcheck` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`,`feed_submission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_flatfile_article`
--

DROP TABLE IF EXISTS `shopimporter_amazon_flatfile_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_flatfile_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `flatfile_id` int(11) NOT NULL DEFAULT 0,
  `sku` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `marketplace` varchar(32) NOT NULL,
  `feedsubmissionid` varchar(255) NOT NULL,
  `error_message` text DEFAULT NULL,
  `all_required_ok` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_flatfile_article_image`
--

DROP TABLE IF EXISTS `shopimporter_amazon_flatfile_article_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_flatfile_article_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopimporter_amazon_flatfile_article_id` int(11) DEFAULT 0,
  `file_id` int(11) DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shopimporter_amazon_flatfile_article_id` (`shopimporter_amazon_flatfile_article_id`),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_flatfile_article_value`
--

DROP TABLE IF EXISTS `shopimporter_amazon_flatfile_article_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_flatfile_article_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopimporter_amazon_flatfile_article_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shopimporter_amazon_flatfile_article_id` (`shopimporter_amazon_flatfile_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_flatfiledefinition`
--

DROP TABLE IF EXISTS `shopimporter_amazon_flatfiledefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_flatfiledefinition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `country` varchar(2) NOT NULL,
  `csv` text DEFAULT NULL,
  `definitions_json` mediumtext DEFAULT NULL,
  `requirements_json` mediumtext DEFAULT NULL,
  `allowed_values_json` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_flatfilefields`
--

DROP TABLE IF EXISTS `shopimporter_amazon_flatfilefields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_flatfilefields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fieldname` (`fieldname`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4499 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_invoice_address`
--

DROP TABLE IF EXISTS `shopimporter_amazon_invoice_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_invoice_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `orderid` varchar(19) NOT NULL,
  `name` varchar(255) NOT NULL,
  `addressfieldone` varchar(255) NOT NULL,
  `addressfieldtwo` varchar(255) NOT NULL,
  `addressfieldthree` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `postalcode` varchar(32) NOT NULL,
  `countrycode` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phonenumber` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_invoice_upload`
--

DROP TABLE IF EXISTS `shopimporter_amazon_invoice_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_invoice_upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `int_order_id` int(11) NOT NULL DEFAULT 0,
  `invoice_id` int(11) NOT NULL DEFAULT 0,
  `credit_note_id` int(11) NOT NULL DEFAULT 0,
  `file_id` int(11) NOT NULL DEFAULT 0,
  `orderid` varchar(19) NOT NULL,
  `shippingid` varchar(19) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `report` varchar(32) NOT NULL,
  `marketplace` varchar(32) NOT NULL,
  `status` varchar(32) NOT NULL,
  `error_code` varchar(5) NOT NULL,
  `error_message` text NOT NULL,
  `invoice_number` varchar(64) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_vat_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `transaction_id` varchar(255) NOT NULL,
  `count_sent` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_listing`
--

DROP TABLE IF EXISTS `shopimporter_amazon_listing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_listing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `marketplace_request` varchar(32) NOT NULL,
  `request_date_listing` datetime DEFAULT NULL,
  `request_date_listing_inactive` datetime DEFAULT NULL,
  `seller_sku` varchar(255) NOT NULL,
  `item_name` varchar(1024) NOT NULL,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `listing_id` varchar(255) NOT NULL,
  `item_description` text DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `open_date` datetime DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `item_is_marketplace` varchar(1) NOT NULL,
  `product_id_type` int(11) DEFAULT NULL,
  `zshop_shipping_fee` decimal(12,2) DEFAULT NULL,
  `item_note` varchar(255) NOT NULL,
  `item_condition` int(11) DEFAULT NULL,
  `zshop_category1` varchar(255) NOT NULL,
  `zshop_browse_path` varchar(255) NOT NULL,
  `zshop_storefron_feature` varchar(255) NOT NULL,
  `asin` varchar(10) NOT NULL,
  `asin2` varchar(10) NOT NULL,
  `asin3` varchar(10) NOT NULL,
  `will_ship_internationally` varchar(32) NOT NULL,
  `expedited_shipping` varchar(32) NOT NULL,
  `zshop_boldface` varchar(1) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `bid_for_fetatured_placement` varchar(255) NOT NULL,
  `add_delete` varchar(255) NOT NULL,
  `pending_quantity` int(11) DEFAULT NULL,
  `fulfillment_channel` varchar(255) NOT NULL,
  `business_price` decimal(12,2) DEFAULT NULL,
  `quantity_price_type` varchar(255) NOT NULL,
  `quantity_lower_bound1` int(11) DEFAULT NULL,
  `quantity_price1` decimal(12,2) DEFAULT NULL,
  `quantity_lower_bound2` int(11) DEFAULT NULL,
  `quantity_price2` decimal(12,2) DEFAULT NULL,
  `quantity_lower_bound3` int(11) DEFAULT NULL,
  `quantity_price3` decimal(12,2) DEFAULT NULL,
  `quantity_lower_bound4` int(11) DEFAULT NULL,
  `quantity_price4` decimal(12,2) DEFAULT NULL,
  `quantity_lower_bound5` int(11) DEFAULT NULL,
  `quantity_price5` decimal(12,2) DEFAULT NULL,
  `merchant_shipping_group` varchar(255) NOT NULL,
  `status` varchar(32) NOT NULL,
  `is_fba` tinyint(1) DEFAULT -1,
  `active` tinyint(1) DEFAULT NULL,
  `recommended_article_id` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `seller_sku` (`seller_sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_merchantgroup`
--

DROP TABLE IF EXISTS `shopimporter_amazon_merchantgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_merchantgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT 0,
  `groupname` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_order_status`
--

DROP TABLE IF EXISTS `shopimporter_amazon_order_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_order_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `orderid` varchar(19) NOT NULL,
  `status` varchar(32) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_id` (`shop_id`,`orderid`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_orderadjustment`
--

DROP TABLE IF EXISTS `shopimporter_amazon_orderadjustment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_orderadjustment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `payment_transaction_id` int(11) NOT NULL DEFAULT 0,
  `submitfeedid` varchar(32) NOT NULL,
  `status` varchar(32) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_orderinfo`
--

DROP TABLE IF EXISTS `shopimporter_amazon_orderinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_orderinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT 0,
  `orderid` varchar(32) DEFAULT NULL,
  `isprime` tinyint(1) DEFAULT -1,
  `isfba` tinyint(1) DEFAULT -1,
  `trackingsent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_recommendation`
--

DROP TABLE IF EXISTS `shopimporter_amazon_recommendation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_recommendation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT 0,
  `recommendationtype` varchar(255) DEFAULT NULL,
  `itemname` varchar(255) DEFAULT NULL,
  `defectgroup` varchar(255) DEFAULT NULL,
  `recommendationid` varchar(255) DEFAULT NULL,
  `recommendationreason` varchar(255) DEFAULT NULL,
  `defectattribute` varchar(255) DEFAULT NULL,
  `asin` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`),
  KEY `recommendationid` (`recommendationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_report_scheduler`
--

DROP TABLE IF EXISTS `shopimporter_amazon_report_scheduler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_report_scheduler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `request_date` datetime DEFAULT NULL,
  `request_period` int(11) NOT NULL DEFAULT 0,
  `marketplace_request` varchar(32) NOT NULL,
  `report_type` varchar(255) NOT NULL,
  `last_reportrequestid` varchar(32) NOT NULL,
  `last_generatedreportid` varchar(32) NOT NULL,
  `last_report_status` varchar(32) NOT NULL,
  `imported` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_id` (`shop_id`,`report_type`,`marketplace_request`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_requestinfo`
--

DROP TABLE IF EXISTS `shopimporter_amazon_requestinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_requestinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) DEFAULT 0,
  `type` varchar(64) DEFAULT NULL,
  `doctype` varchar(64) DEFAULT NULL,
  `parameter` varchar(32) DEFAULT NULL,
  `parameter2` varchar(32) DEFAULT NULL,
  `status` varchar(32) DEFAULT NULL,
  `shopimporter_amazon_aufrufe_id` int(11) DEFAULT 0,
  `error` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`,`type`),
  KEY `shopimporter_amazon_aufrufe_id` (`shopimporter_amazon_aufrufe_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_service_status`
--

DROP TABLE IF EXISTS `shopimporter_amazon_service_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_service_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `status` varchar(32) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_small_and_light`
--

DROP TABLE IF EXISTS `shopimporter_amazon_small_and_light`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_small_and_light` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `marketplace_request` varchar(32) NOT NULL,
  `request_date` datetime DEFAULT NULL,
  `sku` varchar(255) NOT NULL,
  `fnsku` varchar(10) NOT NULL,
  `asin` varchar(10) NOT NULL,
  `protuct_name` varchar(1024) NOT NULL,
  `enrolled_in_snl` varchar(3) NOT NULL,
  `marketplace` varchar(16) NOT NULL,
  `your_snl_price` decimal(12,2) DEFAULT NULL,
  `inventory_in_snl_fc` int(11) DEFAULT NULL,
  `inventory_in_non_snl_fc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_token`
--

DROP TABLE IF EXISTS `shopimporter_amazon_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL,
  `token` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`,`type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_amazon_xsd_enumerations`
--

DROP TABLE IF EXISTS `shopimporter_amazon_xsd_enumerations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_amazon_xsd_enumerations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_name` varchar(255) NOT NULL,
  `direct_parent` varchar(255) NOT NULL,
  `element_name` varchar(255) NOT NULL,
  `element_value` varchar(255) NOT NULL,
  `enumeration_type` varchar(255) NOT NULL,
  `restriction` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `enumeration_type` (`enumeration_type`),
  KEY `element_name` (`element_name`)
) ENGINE=InnoDB AUTO_INCREMENT=26745 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopimporter_shopify_auftraege`
--

DROP TABLE IF EXISTS `shopimporter_shopify_auftraege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopimporter_shopify_auftraege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` int(11) NOT NULL DEFAULT 0,
  `extid` varchar(32) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(32) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_id` varchar(64) NOT NULL,
  `zahlungsweise` varchar(64) NOT NULL,
  `getestet` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `shop` (`shop`),
  KEY `extid` (`extid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopnavigation`
--

DROP TABLE IF EXISTS `shopnavigation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopnavigation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `position` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `bezeichnung_en` varchar(255) NOT NULL,
  `plugin` varchar(255) NOT NULL,
  `pluginparameter` varchar(255) NOT NULL,
  `shop` int(11) NOT NULL,
  `target` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `singleshipment_order`
--

DROP TABLE IF EXISTS `singleshipment_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `singleshipment_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `deliverynote_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `status` varchar(255) NOT NULL,
  `trackingnumber` varchar(255) NOT NULL,
  `quality` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `snapaddy_address`
--

DROP TABLE IF EXISTS `snapaddy_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snapaddy_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` varchar(64) DEFAULT NULL,
  `contact_list` varchar(64) DEFAULT NULL,
  `firstName` varchar(64) DEFAULT NULL,
  `lastName` varchar(64) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `website` varchar(64) DEFAULT NULL,
  `zip` varchar(64) DEFAULT NULL,
  `xentral_id` int(11) DEFAULT 0,
  `snap_created` varchar(64) DEFAULT NULL,
  `snap_hash` varchar(64) NOT NULL,
  `address` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `snap_hash` (`snap_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `snapaddy_log`
--

DROP TABLE IF EXISTS `snapaddy_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snapaddy_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lvl` varchar(16) NOT NULL,
  `msg` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sprachen`
--

DROP TABLE IF EXISTS `sprachen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sprachen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iso` varchar(2) NOT NULL,
  `bezeichnung_de` varchar(255) NOT NULL,
  `bezeichnung_en` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spryker_data`
--

DROP TABLE IF EXISTS `spryker_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spryker_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `internal_id` int(11) NOT NULL DEFAULT 0,
  `reference` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `time_of_validity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spryker_online_number`
--

DROP TABLE IF EXISTS `spryker_online_number`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spryker_online_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_reference` varchar(255) NOT NULL,
  `order_shipment` varchar(255) NOT NULL,
  `order_number` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_reference` (`order_reference`),
  KEY `order_shipment` (`order_shipment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spryker_order_reference`
--

DROP TABLE IF EXISTS `spryker_order_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spryker_order_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `order_reference` varchar(255) NOT NULL,
  `shipment_id` varchar(255) NOT NULL,
  `order_item_reference` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sqlcache`
--

DROP TABLE IF EXISTS `sqlcache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sqlcache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abfrage` text NOT NULL,
  `ergebnis` text NOT NULL,
  `shortcode` varchar(255) NOT NULL,
  `sekunden` int(11) NOT NULL DEFAULT 120,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=496 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `standardpackage`
--

DROP TABLE IF EXISTS `standardpackage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `standardpackage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `width` decimal(14,2) NOT NULL DEFAULT 0.00,
  `height` decimal(14,2) NOT NULL DEFAULT 0.00,
  `length` decimal(14,2) NOT NULL DEFAULT 0.00,
  `xvp` decimal(14,2) NOT NULL DEFAULT 0.00,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `color` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stechuhr`
--

DROP TABLE IF EXISTS `stechuhr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stechuhr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` datetime DEFAULT NULL,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `user` int(11) NOT NULL DEFAULT 0,
  `kommen` tinyint(1) NOT NULL DEFAULT 0,
  `uebernommen` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL,
  `mitarbeiterzeiterfassungid` int(15) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `datum` (`datum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stechuhrdevice`
--

DROP TABLE IF EXISTS `stechuhrdevice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stechuhrdevice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `reduziert` int(11) NOT NULL DEFAULT 0,
  `code` int(11) NOT NULL DEFAULT 0,
  `aktiv` int(11) NOT NULL DEFAULT 0,
  `IP` int(4) NOT NULL DEFAULT 0,
  `submask` int(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `steuersaetze`
--

DROP TABLE IF EXISTS `steuersaetze`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `steuersaetze` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(64) NOT NULL,
  `satz` decimal(5,2) NOT NULL DEFAULT 0.00,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `bearbeiter` varchar(64) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `project_id` int(11) NOT NULL DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `type` varchar(32) NOT NULL,
  `country_code` varchar(8) NOT NULL,
  `set_data` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_replenishment_list`
--

DROP TABLE IF EXISTS `stock_replenishment_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_replenishment_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `storage_area_id` int(11) NOT NULL DEFAULT 0,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `amount_to_relocate` decimal(14,2) NOT NULL DEFAULT 0.00,
  `storage_min_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `storage_max_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `is_replenishment` tinyint(1) NOT NULL DEFAULT 0,
  `needed` decimal(14,2) NOT NULL DEFAULT 0.00,
  `inorder` decimal(14,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`,`is_replenishment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stueckliste`
--

DROP TABLE IF EXISTS `stueckliste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stueckliste` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `referenz` text NOT NULL,
  `place` varchar(255) NOT NULL,
  `layer` varchar(255) NOT NULL,
  `stuecklistevonartikel` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `firma` int(11) NOT NULL,
  `wert` text NOT NULL,
  `bauform` text NOT NULL,
  `alternative` int(11) NOT NULL DEFAULT 0,
  `zachse` varchar(64) NOT NULL,
  `xpos` varchar(64) NOT NULL,
  `ypos` varchar(64) NOT NULL,
  `art` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stuecklistevonartikel` (`stuecklistevonartikel`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stundensatz`
--

DROP TABLE IF EXISTS `stundensatz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stundensatz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `satz` float NOT NULL,
  `typ` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscription_cycle_job`
--

DROP TABLE IF EXISTS `subscription_cycle_job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscription_cycle_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` int(11) NOT NULL,
  `document_type` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `job_type` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `printer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `address` (`address_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supersearch_index_group`
--

DROP TABLE IF EXISTS `supersearch_index_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supersearch_index_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  `title` varchar(32) NOT NULL,
  `module` varchar(38) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `last_full_update` timestamp NULL DEFAULT NULL,
  `last_diff_update` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supersearch_index_item`
--

DROP TABLE IF EXISTS `supersearch_index_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supersearch_index_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `index_name` varchar(16) NOT NULL,
  `index_id` varchar(38) NOT NULL,
  `project_id` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(128) NOT NULL,
  `subtitle` varchar(128) DEFAULT NULL,
  `additional_infos` varchar(255) DEFAULT NULL,
  `link` varchar(128) NOT NULL,
  `search_words` text NOT NULL,
  `outdated` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_identifier` (`index_name`,`index_id`),
  KEY `project_id` (`project_id`),
  FULLTEXT KEY `FullText` (`search_words`)
) ENGINE=InnoDB AUTO_INCREMENT=382 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supportapp`
--

DROP TABLE IF EXISTS `supportapp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supportapp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `mitarbeiter` int(11) NOT NULL DEFAULT 0,
  `startdatum` date NOT NULL,
  `zeitgeplant` int(11) NOT NULL DEFAULT 0,
  `version` text NOT NULL,
  `bemerkung` text NOT NULL,
  `status` varchar(10) NOT NULL,
  `phase` varchar(10) NOT NULL,
  `intervall` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supportapp_artikel`
--

DROP TABLE IF EXISTS `supportapp_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supportapp_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `typ` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supportapp_auftrag_check`
--

DROP TABLE IF EXISTS `supportapp_auftrag_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supportapp_auftrag_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `gruppe` int(11) NOT NULL,
  `schritt` int(11) NOT NULL,
  `auftragposition` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supportapp_gruppen`
--

DROP TABLE IF EXISTS `supportapp_gruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supportapp_gruppen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `aktiv` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supportapp_log`
--

DROP TABLE IF EXISTS `supportapp_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supportapp_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` int(11) NOT NULL DEFAULT 0,
  `logdatei` datetime NOT NULL,
  `details` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supportapp_schritte`
--

DROP TABLE IF EXISTS `supportapp_schritte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supportapp_schritte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL DEFAULT '0',
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `beschreibung` text NOT NULL,
  `aktiv` int(1) NOT NULL DEFAULT 0,
  `sort` int(11) DEFAULT 0,
  `vorgaenger` int(11) DEFAULT 0,
  `filter` int(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supportapp_vorlagen`
--

DROP TABLE IF EXISTS `supportapp_vorlagen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supportapp_vorlagen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) NOT NULL,
  `taetigkeit` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `survey`
--

DROP TABLE IF EXISTS `survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `once_per_user` tinyint(1) NOT NULL DEFAULT 0,
  `send_to_xentral` tinyint(1) NOT NULL DEFAULT 0,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `survey_user`
--

DROP TABLE IF EXISTS `survey_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_disk_free`
--

DROP TABLE IF EXISTS `system_disk_free`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_disk_free` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `disk_free_kb_start` int(11) DEFAULT NULL,
  `disk_free_kb_end` int(11) DEFAULT NULL,
  `db_size` int(11) DEFAULT NULL,
  `userdata_mb_size` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systemhealth`
--

DROP TABLE IF EXISTS `systemhealth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemhealth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `systemhealth_category_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(64) NOT NULL,
  `description` varchar(64) NOT NULL,
  `status` varchar(64) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lastupdate` timestamp NULL DEFAULT NULL,
  `resetable` tinyint(1) NOT NULL DEFAULT 0,
  `last_reset` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `systemhealth_category_id` (`systemhealth_category_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systemhealth_category`
--

DROP TABLE IF EXISTS `systemhealth_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemhealth_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systemhealth_custom_error_lvl`
--

DROP TABLE IF EXISTS `systemhealth_custom_error_lvl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemhealth_custom_error_lvl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `systemhealth_id` int(11) NOT NULL DEFAULT 0,
  `status` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `systemhealth_id` (`systemhealth_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systemhealth_event`
--

DROP TABLE IF EXISTS `systemhealth_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemhealth_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `systemhealth_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `doctype` varchar(64) NOT NULL,
  `doctype_id` int(11) NOT NULL DEFAULT 0,
  `status` varchar(64) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `systemhealth_id` (`systemhealth_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systemhealth_notification`
--

DROP TABLE IF EXISTS `systemhealth_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemhealth_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systemhealth_notification_item`
--

DROP TABLE IF EXISTS `systemhealth_notification_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemhealth_notification_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `systemhealth_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `status` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systemlog`
--

DROP TABLE IF EXISTS `systemlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meldung` text NOT NULL,
  `dump` text NOT NULL,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `bearbeiter` varchar(64) NOT NULL,
  `funktionsname` varchar(64) NOT NULL,
  `datum` datetime DEFAULT NULL,
  `parameter` int(11) NOT NULL DEFAULT 0,
  `argumente` text NOT NULL,
  `level` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systemtemplates`
--

DROP TABLE IF EXISTS `systemtemplates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemtemplates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(200) NOT NULL,
  `footer_icons` varchar(200) NOT NULL,
  `category` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `hidden` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `system_pkey` (`id`,`hidden`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_subscription`
--

DROP TABLE IF EXISTS `task_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_timeline`
--

DROP TABLE IF EXISTS `task_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teilprojekt_geplante_zeiten`
--

DROP TABLE IF EXISTS `teilprojekt_geplante_zeiten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teilprojekt_geplante_zeiten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `bezeichnung` varchar(255) NOT NULL,
  `stundensatz` decimal(5,2) NOT NULL DEFAULT 0.00,
  `stunden` decimal(8,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `telefonrueckruf`
--

DROP TABLE IF EXISTS `telefonrueckruf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telefonrueckruf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `zeit` time NOT NULL,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `adressetext` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  `ticket` int(11) NOT NULL DEFAULT 0,
  `angenommenvon` int(11) NOT NULL DEFAULT 0,
  `rueckrufvon` int(11) NOT NULL DEFAULT 0,
  `telefonnummer` varchar(255) NOT NULL,
  `kommentar` text NOT NULL,
  `abgeschlossen` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `rueckrufvon` (`rueckrufvon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `telefonrueckruf_versuche`
--

DROP TABLE IF EXISTS `telefonrueckruf_versuche`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telefonrueckruf_versuche` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefonrueckruf` int(11) NOT NULL DEFAULT 0,
  `datum` date NOT NULL,
  `zeit` time NOT NULL,
  `bearbeiter` int(11) NOT NULL DEFAULT 0,
  `beschreibung` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `telefonrueckruf` (`telefonrueckruf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `templatemessage`
--

DROP TABLE IF EXISTS `templatemessage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templatemessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `message` text NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `textvorlagen`
--

DROP TABLE IF EXISTS `textvorlagen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `textvorlagen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `stichwoerter` varchar(255) DEFAULT NULL,
  `projekt` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schluessel` varchar(255) NOT NULL,
  `zeit` timestamp NOT NULL DEFAULT current_timestamp(),
  `projekt` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `quelle` varchar(255) NOT NULL,
  `status` varchar(64) NOT NULL,
  `adresse` int(11) NOT NULL,
  `kunde` varchar(255) NOT NULL,
  `warteschlange` varchar(255) NOT NULL,
  `mailadresse` varchar(255) NOT NULL,
  `prio` int(1) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `zugewiesen` int(1) NOT NULL,
  `inbearbeitung` int(1) NOT NULL,
  `inbearbeitung_user` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `notiz` text NOT NULL,
  `bitteantworten` tinyint(1) NOT NULL DEFAULT 0,
  `service` int(11) NOT NULL DEFAULT 0,
  `kommentar` text NOT NULL,
  `privat` tinyint(1) NOT NULL DEFAULT 0,
  `dsgvo` tinyint(1) NOT NULL DEFAULT 0,
  `tags` text NOT NULL,
  `nachrichten_anz` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schluessel` (`schluessel`),
  KEY `service` (`service`),
  KEY `adresse` (`adresse`),
  KEY `warteschlange` (`warteschlange`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_category`
--

DROP TABLE IF EXISTS `ticket_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `project_id` int(10) NOT NULL DEFAULT 0,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_header`
--

DROP TABLE IF EXISTS `ticket_header`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_nachricht` int(15) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_nachricht`
--

DROP TABLE IF EXISTS `ticket_nachricht`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_nachricht` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` varchar(255) NOT NULL,
  `verfasser` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `zeit` datetime NOT NULL,
  `zeitausgang` datetime DEFAULT NULL,
  `text` longtext NOT NULL,
  `textausgang` longtext NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `bemerkung` text NOT NULL,
  `medium` varchar(255) NOT NULL,
  `versendet` varchar(255) NOT NULL,
  `status` varchar(64) NOT NULL,
  `mail_cc` varchar(128) NOT NULL,
  `verfasser_replyto` varchar(255) NOT NULL,
  `mail_replyto` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket` (`ticket`),
  FULLTEXT KEY `FullText` (`betreff`,`verfasser`,`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_regeln`
--

DROP TABLE IF EXISTS `ticket_regeln`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_regeln` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empfaenger_email` varchar(255) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `betreff` varchar(255) NOT NULL,
  `spam` tinyint(1) NOT NULL DEFAULT 0,
  `persoenlich` tinyint(1) NOT NULL DEFAULT 0,
  `prio` tinyint(1) NOT NULL DEFAULT 0,
  `dsgvo` tinyint(1) NOT NULL DEFAULT 0,
  `warteschlange` text NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `adresse` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_vorlage`
--

DROP TABLE IF EXISTS `ticket_vorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_vorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projekt` int(10) NOT NULL,
  `vorlagenname` varchar(255) NOT NULL,
  `vorlage` text NOT NULL,
  `firma` int(11) NOT NULL,
  `sichtbar` int(1) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `ticket_category_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ticket_category_id` (`ticket_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transfer_account_label`
--

DROP TABLE IF EXISTS `transfer_account_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfer_account_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL DEFAULT 0,
  `filter_type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transfer_sellingreport_job`
--

DROP TABLE IF EXISTS `transfer_sellingreport_job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfer_sellingreport_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL DEFAULT 0,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'created',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebersetzung`
--

DROP TABLE IF EXISTS `uebersetzung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebersetzung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `beschriftung` text NOT NULL,
  `sprache` varchar(255) NOT NULL,
  `original` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sprache` (`sprache`),
  KEY `label` (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_account`
--

DROP TABLE IF EXISTS `uebertragungen_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(64) NOT NULL,
  `typ` varchar(32) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `server` varchar(255) NOT NULL,
  `port` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `parameter1` varchar(255) NOT NULL,
  `parameter2` varchar(255) NOT NULL,
  `parameter3` varchar(255) NOT NULL,
  `parameter4` varchar(255) NOT NULL,
  `authmethod` varchar(255) NOT NULL,
  `publickeyfile` varchar(255) NOT NULL,
  `privatekeyfile` varchar(255) NOT NULL,
  `publickey` text DEFAULT NULL,
  `privatekey` text DEFAULT NULL,
  `ssl_aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `sammeln` tinyint(1) NOT NULL DEFAULT 1,
  `minwartezeit` int(11) NOT NULL DEFAULT 0,
  `bearbeiter` varchar(64) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `letzte_uebertragung` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `letzter_status` varchar(255) NOT NULL,
  `api` int(11) NOT NULL DEFAULT 0,
  `loeschen_nach_download` tinyint(1) NOT NULL DEFAULT 0,
  `xml_pdf` varchar(32) NOT NULL DEFAULT 'xml',
  `belegtyp` varchar(32) NOT NULL,
  `documenttype_incoming` varchar(32) NOT NULL,
  `belegstatus` varchar(32) NOT NULL,
  `belegab_id` int(11) NOT NULL DEFAULT 0,
  `belegab_datum` date NOT NULL DEFAULT '0000-00-00',
  `briefpapierimxml` tinyint(1) NOT NULL DEFAULT 0,
  `maxbelege` int(11) NOT NULL DEFAULT 10,
  `emailbody` text NOT NULL,
  `importwarteschlange` tinyint(1) NOT NULL DEFAULT 1,
  `xml_zusatz` text NOT NULL,
  `gln_freifeld` int(4) NOT NULL DEFAULT 0,
  `trackingmail` tinyint(1) NOT NULL DEFAULT 0,
  `rechnungmail` tinyint(1) NOT NULL DEFAULT 0,
  `lager` int(11) NOT NULL DEFAULT 0,
  `einzelnexml` tinyint(1) NOT NULL DEFAULT 0,
  `lagerzahlen` tinyint(1) NOT NULL DEFAULT 0,
  `tracking` tinyint(1) NOT NULL DEFAULT 0,
  `csv_codierung` varchar(32) NOT NULL,
  `csv_trennzeichen` varchar(32) NOT NULL,
  `csv_tracking` text NOT NULL,
  `csv_lagerzahl` text NOT NULL,
  `csv_lieferschein` text NOT NULL,
  `csv_auftrag` text NOT NULL,
  `csv_bestellung` text NOT NULL,
  `lagerzahlen_lager` int(11) NOT NULL DEFAULT 0,
  `lagerzahlen_lagerplatz` int(11) NOT NULL DEFAULT 0,
  `lagerzahlen_zeit1` time DEFAULT NULL,
  `lagerzahlen_zeit2` time DEFAULT NULL,
  `lagerzahlen_zeit3` time DEFAULT NULL,
  `lagerzahlen_zeit4` time DEFAULT NULL,
  `lagerzahlen_zeit5` time DEFAULT NULL,
  `lagerzahlen_letzteuebertragung` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `auftrageingang` tinyint(1) NOT NULL DEFAULT 0,
  `kundennummernuebernehmen` tinyint(1) NOT NULL DEFAULT 0,
  `createarticleifnotexists` tinyint(1) NOT NULL DEFAULT 0,
  `createarticleasstoragearticle` tinyint(1) NOT NULL DEFAULT 0,
  `bestellungeingang` tinyint(1) NOT NULL DEFAULT 0,
  `artikeleingang` tinyint(1) NOT NULL DEFAULT 0,
  `trackingeingang` tinyint(1) NOT NULL DEFAULT 1,
  `lagerzahleneingang` tinyint(1) NOT NULL DEFAULT 1,
  `adresselieferant` int(11) NOT NULL DEFAULT 0,
  `lagerplatzignorieren` int(11) NOT NULL DEFAULT 0,
  `neueartikeluebertragen` tinyint(1) NOT NULL DEFAULT 0,
  `dateianhanguebertragen` tinyint(1) NOT NULL DEFAULT 0,
  `dateianhangtyp` varchar(255) NOT NULL DEFAULT 'datei',
  `csvheader_lagerzahlen` text DEFAULT NULL,
  `csvheader_tracking` text DEFAULT NULL,
  `csvnowrap` tinyint(1) NOT NULL DEFAULT 0,
  `lagerzahlenverfuegbaremenge` tinyint(1) NOT NULL DEFAULT 0,
  `autoshopexport` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `einstellungen_json` mediumtext DEFAULT NULL,
  `rechnunganlegen` tinyint(1) NOT NULL DEFAULT 0,
  `lieferantenbestellnummer` tinyint(1) NOT NULL DEFAULT 0,
  `send_sales_report` tinyint(1) NOT NULL DEFAULT 0,
  `sales_report_type` varchar(32) NOT NULL,
  `createproduction` tinyint(1) NOT NULL DEFAULT 0,
  `ownaddress` int(11) NOT NULL DEFAULT 0,
  `updatearticles` int(11) NOT NULL DEFAULT 0,
  `logarticlenotfound` int(11) NOT NULL DEFAULT 0,
  `alldoctypes` tinyint(1) NOT NULL DEFAULT 0,
  `csvseparator` varchar(4) NOT NULL DEFAULT ';',
  `coding` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_account_oauth`
--

DROP TABLE IF EXISTS `uebertragungen_account_oauth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_account_oauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uebertragungen_account_id` int(10) unsigned NOT NULL,
  `client_id` text DEFAULT NULL,
  `client_secret` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `access_token` text DEFAULT NULL,
  `expiration_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uebertragungen_account_id` (`uebertragungen_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_artikel`
--

DROP TABLE IF EXISTS `uebertragungen_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uebertragungen_account` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uebertragungen_account` (`uebertragungen_account`,`artikel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_dateien`
--

DROP TABLE IF EXISTS `uebertragungen_dateien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_dateien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uebertragung_account` int(11) NOT NULL DEFAULT 0,
  `datei` varchar(255) NOT NULL,
  `datei_wawi` varchar(255) NOT NULL,
  `status` varchar(64) NOT NULL,
  `download` tinyint(1) NOT NULL DEFAULT 1,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `geloescht_von_server` tinyint(1) NOT NULL DEFAULT 0,
  `zeitstempelupdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `filesize` int(11) NOT NULL DEFAULT -1,
  PRIMARY KEY (`id`),
  KEY `uebertragung_account` (`uebertragung_account`,`datei`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_event`
--

DROP TABLE IF EXISTS `uebertragungen_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uebertragung_account` int(11) NOT NULL DEFAULT 0,
  `eventname` varchar(64) NOT NULL,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `cachetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `retries` int(11) NOT NULL DEFAULT 0,
  `kommentar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uebertragung_account` (`uebertragung_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_event_einstellungen`
--

DROP TABLE IF EXISTS `uebertragungen_event_einstellungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_event_einstellungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uebertragung_account` int(11) NOT NULL DEFAULT 0,
  `eventname` varchar(64) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `eingang` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `uebertragung_account` (`uebertragung_account`,`eventname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_fileconvert_log`
--

DROP TABLE IF EXISTS `uebertragungen_fileconvert_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_fileconvert_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uebertragungen_account` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL,
  `datei` varchar(255) NOT NULL,
  `typ` varchar(255) NOT NULL,
  `parameter1` varchar(255) NOT NULL,
  `parameter2` varchar(255) NOT NULL,
  `wert` text NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_lagercache`
--

DROP TABLE IF EXISTS `uebertragungen_lagercache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_lagercache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL,
  `lager_platz` int(11) NOT NULL,
  `lagerzahl` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `uebertragungen` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_log`
--

DROP TABLE IF EXISTS `uebertragungen_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uebertragungen_account` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL,
  `datei` varchar(255) NOT NULL,
  `typ` varchar(255) NOT NULL,
  `parameter1` varchar(255) NOT NULL,
  `parameter2` varchar(255) NOT NULL,
  `wert` text NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uebertragungen_account` (`uebertragungen_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_monitor`
--

DROP TABLE IF EXISTS `uebertragungen_monitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_monitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uebertragungen_account` int(11) NOT NULL DEFAULT 0,
  `api_request` int(11) NOT NULL DEFAULT 0,
  `datei` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL,
  `nachricht` varchar(255) NOT NULL,
  `element1` varchar(255) NOT NULL,
  `element2` varchar(255) NOT NULL,
  `element3` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `doctype` varchar(255) NOT NULL,
  `doctypeid` int(11) NOT NULL DEFAULT 0,
  `ausgeblendet` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `uebertragungen_account` (`uebertragungen_account`,`datei`,`api_request`,`doctypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uebertragungen_trackingnummern`
--

DROP TABLE IF EXISTS `uebertragungen_trackingnummern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uebertragungen_trackingnummern` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uebertragungen` int(11) NOT NULL,
  `lieferschein` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `umsatzstatistik`
--

DROP TABLE IF EXISTS `umsatzstatistik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `umsatzstatistik` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `objekt` varchar(64) NOT NULL,
  `belegnr` varchar(64) NOT NULL,
  `kundennummer` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `parameter` int(11) NOT NULL DEFAULT 0,
  `betrag_netto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `betrag_brutto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `erloes_netto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deckungsbeitrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `datum` date DEFAULT NULL,
  `waehrung` varchar(3) NOT NULL DEFAULT 'EUR',
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unterprojekt`
--

DROP TABLE IF EXISTS `unterprojekt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unterprojekt` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `projekt` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `verantwortlicher` varchar(255) NOT NULL,
  `aktiv` varchar(255) NOT NULL,
  `position` int(10) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ups`
--

DROP TABLE IF EXISTS `ups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `account_nummer` varchar(255) NOT NULL,
  `bemerkung` varchar(255) NOT NULL,
  `auswahl` int(11) NOT NULL DEFAULT 0,
  `aktiv` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `repassword` int(1) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `settings` text NOT NULL,
  `parentuser` int(11) DEFAULT NULL,
  `activ` int(11) DEFAULT 0,
  `type` varchar(100) DEFAULT NULL,
  `adresse` int(10) NOT NULL,
  `fehllogins` int(11) NOT NULL,
  `standarddrucker` int(1) NOT NULL,
  `firma` int(10) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `startseite` varchar(1024) DEFAULT NULL,
  `hwtoken` int(1) DEFAULT NULL,
  `hwkey` varchar(255) DEFAULT NULL,
  `hwcounter` int(11) DEFAULT NULL,
  `motppin` varchar(255) DEFAULT NULL,
  `motpsecret` varchar(255) DEFAULT NULL,
  `passwordmd5` varchar(255) DEFAULT NULL,
  `externlogin` int(1) DEFAULT NULL,
  `projekt_bevorzugen` tinyint(1) NOT NULL DEFAULT 0,
  `email_bevorzugen` tinyint(1) NOT NULL DEFAULT 1,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `rfidtag` varchar(64) NOT NULL,
  `vorlage` varchar(255) DEFAULT NULL,
  `kalender_passwort` varchar(255) DEFAULT NULL,
  `kalender_ausblenden` int(1) NOT NULL DEFAULT 0,
  `kalender_aktiv` int(1) DEFAULT NULL,
  `gpsstechuhr` int(1) DEFAULT NULL,
  `standardetikett` int(11) NOT NULL DEFAULT 0,
  `standardfax` int(11) NOT NULL DEFAULT 0,
  `internebezeichnung` varchar(255) DEFAULT NULL,
  `hwdatablock` varchar(255) DEFAULT NULL,
  `standardversanddrucker` int(11) NOT NULL DEFAULT 0,
  `passwordsha512` varchar(128) NOT NULL,
  `salt` varchar(128) NOT NULL,
  `paketmarkendrucker` int(11) DEFAULT 0,
  `sprachebevorzugen` varchar(255) DEFAULT NULL,
  `vergessencode` varchar(255) DEFAULT NULL,
  `vergessenzeit` datetime DEFAULT NULL,
  `chat_popup` tinyint(1) DEFAULT 1,
  `defaultcolor` varchar(10) DEFAULT NULL,
  `passwordhash` char(60) DEFAULT NULL,
  `docscan_aktiv` tinyint(1) DEFAULT 0,
  `docscan_passwort` varchar(64) DEFAULT NULL,
  `callcenter_notification` tinyint(1) DEFAULT 1,
  `stechuhrdevice` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_totp`
--

DROP TABLE IF EXISTS `user_totp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_totp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) unsigned DEFAULT 0,
  `secret` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userkonfiguration`
--

DROP TABLE IF EXISTS `userkonfiguration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userkonfiguration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `useronline`
--

DROP TABLE IF EXISTS `useronline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useronline` (
  `user_id` int(5) NOT NULL DEFAULT 0,
  `login` int(1) NOT NULL DEFAULT 0,
  `sessionid` varchar(255) NOT NULL,
  `ip` varchar(200) NOT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY `sessionid` (`sessionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userrights`
--

DROP TABLE IF EXISTS `userrights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userrights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `module` varchar(64) NOT NULL,
  `action` varchar(64) NOT NULL,
  `permission` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uservorlage`
--

DROP TABLE IF EXISTS `uservorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uservorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bezeichnung` varchar(255) DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uservorlagerights`
--

DROP TABLE IF EXISTS `uservorlagerights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uservorlagerights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vorlage` int(11) DEFAULT NULL,
  `module` varchar(64) DEFAULT NULL,
  `action` varchar(64) DEFAULT NULL,
  `permission` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5601 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ustprf`
--

DROP TABLE IF EXISTS `ustprf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ustprf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ustid` varchar(255) NOT NULL,
  `land` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `plz` varchar(255) NOT NULL,
  `rechtsform` varchar(255) NOT NULL,
  `strasse` varchar(255) NOT NULL,
  `status` varchar(64) NOT NULL,
  `datum_online` datetime NOT NULL,
  `datum_brief` date NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `briefbestellt` date NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `datum` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ustprf_protokoll`
--

DROP TABLE IF EXISTS `ustprf_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ustprf_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ustprf_id` int(11) NOT NULL,
  `zeit` datetime NOT NULL,
  `bemerkung` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `logdatei` datetime NOT NULL,
  `daten` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verbindlichkeit`
--

DROP TABLE IF EXISTS `verbindlichkeit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verbindlichkeit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `belegnr` varchar(255) NOT NULL,
  `status_beleg` varchar(64) NOT NULL,
  `schreibschutz` tinyint(1) NOT NULL DEFAULT 0,
  `rechnung` varchar(255) NOT NULL,
  `zahlbarbis` date NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `ustid` varchar(64) NOT NULL,
  `summenormal` decimal(10,4) NOT NULL,
  `summeermaessigt` decimal(10,4) NOT NULL,
  `summesatz3` decimal(10,4) NOT NULL,
  `summesatz4` decimal(10,4) NOT NULL,
  `steuersatzname3` varchar(64) NOT NULL,
  `steuersatzname4` varchar(64) NOT NULL,
  `skonto` decimal(10,2) NOT NULL,
  `skontobis` date NOT NULL,
  `skontofestsetzen` int(1) NOT NULL DEFAULT 0,
  `freigabe` int(1) NOT NULL,
  `freigabemitarbeiter` varchar(255) NOT NULL,
  `bestellung` int(11) NOT NULL,
  `adresse` int(11) NOT NULL,
  `projekt` int(11) NOT NULL,
  `teilprojekt` int(11) NOT NULL,
  `auftrag` int(11) NOT NULL,
  `status` varchar(64) NOT NULL,
  `bezahlt` int(1) NOT NULL,
  `kontoauszuege` int(11) NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `bestellung1` int(1) NOT NULL DEFAULT 0,
  `bestellung1betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung1bemerkung` varchar(255) NOT NULL,
  `bestellung1projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung1kostenstelle` varchar(64) NOT NULL,
  `bestellung1auftrag` varchar(64) NOT NULL,
  `bestellung2` int(1) NOT NULL DEFAULT 0,
  `bestellung2betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung2bemerkung` varchar(255) NOT NULL,
  `bestellung2kostenstelle` varchar(64) NOT NULL,
  `bestellung2auftrag` varchar(64) NOT NULL,
  `bestellung2projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung3` int(1) NOT NULL DEFAULT 0,
  `bestellung3betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung3bemerkung` varchar(255) NOT NULL,
  `bestellung3kostenstelle` varchar(64) NOT NULL,
  `bestellung3auftrag` varchar(64) NOT NULL,
  `bestellung3projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung4` int(1) NOT NULL DEFAULT 0,
  `bestellung4betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung4bemerkung` varchar(255) NOT NULL,
  `bestellung4kostenstelle` varchar(64) NOT NULL,
  `bestellung4auftrag` varchar(64) NOT NULL,
  `bestellung4projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung5` int(1) NOT NULL DEFAULT 0,
  `bestellung5betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung5bemerkung` varchar(255) NOT NULL,
  `bestellung5kostenstelle` varchar(64) NOT NULL,
  `bestellung5auftrag` varchar(64) NOT NULL,
  `bestellung5projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung6` int(1) NOT NULL DEFAULT 0,
  `bestellung6betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung6bemerkung` varchar(255) NOT NULL,
  `bestellung6kostenstelle` varchar(64) NOT NULL,
  `bestellung6auftrag` varchar(64) NOT NULL,
  `bestellung6projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung7` int(1) NOT NULL DEFAULT 0,
  `bestellung7betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung7bemerkung` varchar(255) NOT NULL,
  `bestellung7kostenstelle` varchar(64) NOT NULL,
  `bestellung7auftrag` varchar(64) NOT NULL,
  `bestellung7projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung8` int(1) NOT NULL DEFAULT 0,
  `bestellung8betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung8bemerkung` varchar(255) NOT NULL,
  `bestellung8kostenstelle` varchar(64) NOT NULL,
  `bestellung8auftrag` varchar(64) NOT NULL,
  `bestellung8projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung9` int(1) NOT NULL DEFAULT 0,
  `bestellung9betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung9bemerkung` varchar(255) NOT NULL,
  `bestellung9kostenstelle` varchar(64) NOT NULL,
  `bestellung9auftrag` varchar(64) NOT NULL,
  `bestellung9projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung10` int(1) NOT NULL DEFAULT 0,
  `bestellung10betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung10bemerkung` varchar(255) NOT NULL,
  `bestellung10kostenstelle` varchar(64) NOT NULL,
  `bestellung10auftrag` varchar(64) NOT NULL,
  `bestellung10projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung11` int(1) NOT NULL DEFAULT 0,
  `bestellung11betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung11bemerkung` varchar(255) NOT NULL,
  `bestellung11kostenstelle` varchar(64) NOT NULL,
  `bestellung11auftrag` varchar(64) NOT NULL,
  `bestellung11projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung12` int(1) NOT NULL DEFAULT 0,
  `bestellung12betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung12bemerkung` varchar(255) NOT NULL,
  `bestellung12projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung12kostenstelle` varchar(64) NOT NULL,
  `bestellung12auftrag` varchar(64) NOT NULL,
  `bestellung13` int(1) NOT NULL DEFAULT 0,
  `bestellung13betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung13bemerkung` varchar(255) NOT NULL,
  `bestellung13kostenstelle` varchar(64) NOT NULL,
  `bestellung13auftrag` varchar(64) NOT NULL,
  `bestellung13projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung14` int(1) NOT NULL DEFAULT 0,
  `bestellung14betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung14bemerkung` varchar(255) NOT NULL,
  `bestellung14kostenstelle` varchar(64) NOT NULL,
  `bestellung14auftrag` varchar(64) NOT NULL,
  `bestellung14projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung15` int(1) NOT NULL DEFAULT 0,
  `bestellung15betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bestellung15bemerkung` varchar(255) NOT NULL,
  `bestellung15kostenstelle` varchar(64) NOT NULL,
  `bestellung15auftrag` varchar(64) NOT NULL,
  `bestellung15projekt` int(11) NOT NULL DEFAULT 0,
  `waehrung` varchar(3) NOT NULL DEFAULT 'EUR',
  `zahlungsweise` varchar(255) NOT NULL,
  `eingangsdatum` date NOT NULL,
  `buha_konto1` varchar(20) NOT NULL,
  `buha_belegfeld1` varchar(200) NOT NULL,
  `buha_betrag1` decimal(10,2) NOT NULL DEFAULT 0.00,
  `buha_konto2` varchar(20) NOT NULL,
  `buha_belegfeld2` varchar(200) NOT NULL,
  `buha_betrag2` decimal(10,2) NOT NULL DEFAULT 0.00,
  `buha_konto3` varchar(20) NOT NULL,
  `buha_belegfeld3` varchar(200) NOT NULL,
  `buha_betrag3` decimal(10,2) NOT NULL DEFAULT 0.00,
  `buha_konto4` varchar(20) NOT NULL,
  `buha_belegfeld4` varchar(200) NOT NULL,
  `buha_betrag4` decimal(10,2) NOT NULL DEFAULT 0.00,
  `buha_konto5` varchar(20) NOT NULL,
  `buha_belegfeld5` varchar(200) NOT NULL,
  `buha_betrag5` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rechnungsdatum` date DEFAULT NULL,
  `rechnungsfreigabe` tinyint(1) NOT NULL DEFAULT 0,
  `kostenstelle` varchar(255) DEFAULT NULL,
  `beschreibung` varchar(255) DEFAULT NULL,
  `sachkonto` varchar(64) DEFAULT NULL,
  `art` varchar(64) NOT NULL,
  `verwendungszweck` varchar(255) DEFAULT NULL,
  `dta_datei` int(11) NOT NULL DEFAULT 0,
  `frachtkosten` decimal(10,2) NOT NULL DEFAULT 0.00,
  `internebemerkung` text DEFAULT NULL,
  `ustnormal` decimal(10,2) DEFAULT NULL,
  `ustermaessigt` decimal(10,2) DEFAULT NULL,
  `uststuer3` decimal(10,2) DEFAULT NULL,
  `uststuer4` decimal(10,2) DEFAULT NULL,
  `betragbezahlt` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bezahltam` date NOT NULL,
  `klaerfall` tinyint(1) NOT NULL DEFAULT 0,
  `klaergrund` varchar(255) NOT NULL,
  `skonto_erhalten` decimal(10,2) NOT NULL DEFAULT 0.00,
  `kurs` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `sprache` varchar(25) NOT NULL,
  `usereditid` int(11) NOT NULL,
  `datum` date DEFAULT NULL,
  `steuersatz_normal` decimal(5,2) NOT NULL DEFAULT 0.00,
  `steuersatz_ermaessigt` decimal(5,2) NOT NULL DEFAULT 0.00,
  `ust_befreit` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`),
  KEY `bestellung` (`bestellung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verbindlichkeit_bestellungen`
--

DROP TABLE IF EXISTS `verbindlichkeit_bestellungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verbindlichkeit_bestellungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verbindlichkeit` int(11) NOT NULL DEFAULT 0,
  `nummer` int(11) NOT NULL DEFAULT 0,
  `bestellung` int(11) NOT NULL DEFAULT 0,
  `bestellung_betrag` decimal(14,2) DEFAULT 0.00,
  `bestellung_betrag_netto` decimal(14,2) DEFAULT 0.00,
  `bestellung_projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung_auftrag` int(11) NOT NULL DEFAULT 0,
  `bestellung_kostenstelle` int(11) NOT NULL DEFAULT 0,
  `bestellung_bemerkung` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `verbindlichkeit` (`verbindlichkeit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verbindlichkeit_kontierung`
--

DROP TABLE IF EXISTS `verbindlichkeit_kontierung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verbindlichkeit_kontierung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verbindlichkeit` int(11) NOT NULL DEFAULT 0,
  `betrag` decimal(18,2) NOT NULL DEFAULT 0.00,
  `belegfeld` varchar(255) NOT NULL,
  `buchungstext` varchar(255) NOT NULL,
  `gegenkonto` varchar(255) NOT NULL,
  `waehrung` varchar(3) NOT NULL,
  `steuersatz` decimal(10,2) NOT NULL,
  `kostenstelle` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `verbindlichkeit` (`verbindlichkeit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verbindlichkeit_ocr`
--

DROP TABLE IF EXISTS `verbindlichkeit_ocr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verbindlichkeit_ocr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` int(11) NOT NULL DEFAULT 0,
  `property` varchar(32) NOT NULL,
  `search_term` varchar(32) NOT NULL,
  `search_direction` varchar(5) NOT NULL DEFAULT 'right',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verbindlichkeit_position`
--

DROP TABLE IF EXISTS `verbindlichkeit_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verbindlichkeit_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verbindlichkeit` int(11) NOT NULL DEFAULT 0,
  `sort` int(11) NOT NULL DEFAULT 0,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `bestellung` int(11) NOT NULL DEFAULT 0,
  `nummer` varchar(255) NOT NULL,
  `bestellnummer` varchar(255) NOT NULL,
  `waehrung` varchar(255) NOT NULL,
  `einheit` varchar(255) NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `umsatzsteuer` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `lieferdatum` date DEFAULT NULL,
  `steuersatz` decimal(5,2) DEFAULT NULL,
  `steuertext` varchar(1024) DEFAULT NULL,
  `kostenstelle` varchar(10) NOT NULL,
  `preis` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `paketdistribution` int(11) NOT NULL DEFAULT 0,
  `kontorahmen` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `verbindlichkeit` (`verbindlichkeit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verbindlichkeit_protokoll`
--

DROP TABLE IF EXISTS `verbindlichkeit_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verbindlichkeit_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verbindlichkeit` int(11) NOT NULL DEFAULT 0,
  `zeit` datetime NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `verbindlichkeit` (`verbindlichkeit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verbindlichkeit_regelmaessig`
--

DROP TABLE IF EXISTS `verbindlichkeit_regelmaessig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verbindlichkeit_regelmaessig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `aktiv` int(1) DEFAULT 0,
  `typ` varchar(255) DEFAULT '0',
  `filter` varchar(255) NOT NULL,
  `soll` varchar(255) NOT NULL,
  `haben` varchar(255) NOT NULL,
  `gebuehr` varchar(255) NOT NULL,
  `waehrung` varchar(255) NOT NULL,
  `art` varchar(255) NOT NULL,
  `wert` varchar(255) NOT NULL,
  `rechnungnr` varchar(255) NOT NULL,
  `verwendungszweck` varchar(255) NOT NULL,
  `kostenstelle` varchar(255) NOT NULL,
  `zahlungsweise` varchar(255) NOT NULL,
  `gegenkonto` varchar(32) NOT NULL,
  `wepruefung` int(1) DEFAULT NULL,
  `repruefung` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verbindlichkeit_regelmaessig_beleg`
--

DROP TABLE IF EXISTS `verbindlichkeit_regelmaessig_beleg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verbindlichkeit_regelmaessig_beleg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verbindlichkeit_regelmaessig` int(11) DEFAULT 0,
  `verbindlichkeit` int(11) DEFAULT 0,
  `datum` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `verbindlichkeit_regelmaessig` (`verbindlichkeit_regelmaessig`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verkaufspreise`
--

DROP TABLE IF EXISTS `verkaufspreise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verkaufspreise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL,
  `objekt` varchar(255) NOT NULL,
  `projekt` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `preis` decimal(18,8) NOT NULL DEFAULT 0.00000000,
  `waehrung` varchar(255) NOT NULL,
  `ab_menge` decimal(14,4) NOT NULL DEFAULT 1.0000,
  `vpe` varchar(64) NOT NULL DEFAULT '1',
  `vpe_menge` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `angelegt_am` date NOT NULL,
  `gueltig_bis` date NOT NULL,
  `bemerkung` text NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `logdatei` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `firma` int(11) NOT NULL,
  `geloescht` int(1) NOT NULL,
  `kundenartikelnummer` varchar(255) DEFAULT NULL,
  `art` varchar(255) NOT NULL DEFAULT 'kunde',
  `gruppe` int(11) DEFAULT NULL,
  `apichange` tinyint(1) NOT NULL DEFAULT 0,
  `nichtberechnet` tinyint(1) NOT NULL DEFAULT 1,
  `inbelegausblenden` tinyint(1) NOT NULL DEFAULT 0,
  `gueltig_ab` date NOT NULL DEFAULT '0000-00-00',
  `kurs` decimal(14,4) DEFAULT -1.0000,
  `kursdatum` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `artikel` (`artikel`),
  KEY `adresse` (`adresse`),
  KEY `projekt` (`projekt`),
  KEY `kundenartikelnummer` (`kundenartikelnummer`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verkaufszahlen_chart`
--

DROP TABLE IF EXISTS `verkaufszahlen_chart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verkaufszahlen_chart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `regs` tinyint(1) NOT NULL DEFAULT 0,
  `monat` tinyint(1) NOT NULL DEFAULT 1,
  `bezeichnung` varchar(255) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verkaufszahlen_chart_projekt`
--

DROP TABLE IF EXISTS `verkaufszahlen_chart_projekt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verkaufszahlen_chart_projekt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chart` int(11) DEFAULT 0,
  `projekt` int(11) DEFAULT 0,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `chart` (`chart`,`projekt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `verrechnungsart`
--

DROP TABLE IF EXISTS `verrechnungsart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `verrechnungsart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nummer` varchar(20) DEFAULT NULL,
  `beschreibung` varchar(512) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `versand`
--

DROP TABLE IF EXISTS `versand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `versand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `rechnung` int(11) NOT NULL,
  `lieferschein` int(11) NOT NULL,
  `versandart` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `gewicht` varchar(255) NOT NULL,
  `freigegeben` int(1) NOT NULL,
  `bearbeiter` varchar(255) NOT NULL,
  `versender` varchar(255) NOT NULL,
  `abgeschlossen` int(1) NOT NULL,
  `versendet_am` date NOT NULL,
  `versandunternehmen` varchar(255) NOT NULL,
  `tracking` varchar(255) NOT NULL,
  `download` int(11) NOT NULL,
  `firma` int(1) NOT NULL,
  `logdatei` datetime NOT NULL,
  `keinetrackingmail` int(1) DEFAULT NULL,
  `versendet_am_zeitstempel` datetime DEFAULT NULL,
  `weitererlieferschein` int(1) NOT NULL DEFAULT 0,
  `anzahlpakete` int(11) NOT NULL DEFAULT 0,
  `gelesen` int(1) NOT NULL DEFAULT 0,
  `paketmarkegedruckt` int(1) NOT NULL DEFAULT 0,
  `papieregedruckt` int(1) NOT NULL DEFAULT 0,
  `versandzweigeteilt` tinyint(1) NOT NULL DEFAULT 0,
  `improzess` tinyint(1) NOT NULL DEFAULT 0,
  `improzessuser` int(1) NOT NULL DEFAULT 0,
  `lastspooler_id` int(11) NOT NULL DEFAULT 0,
  `lastprinter` int(11) NOT NULL DEFAULT 0,
  `lastexportspooler_id` int(11) NOT NULL DEFAULT 0,
  `lastexportprinter` int(11) NOT NULL DEFAULT 0,
  `tracking_link` text DEFAULT NULL,
  `cronjob` int(1) NOT NULL DEFAULT 0,
  `adressvalidation` int(1) NOT NULL DEFAULT 0,
  `retoure` int(11) NOT NULL DEFAULT 0,
  `klaergrund` varchar(255) NOT NULL,
  `bundesstaat` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lieferschein` (`lieferschein`),
  KEY `projekt` (`projekt`),
  KEY `cronjob` (`cronjob`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `versandarten`
--

DROP TABLE IF EXISTS `versandarten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `versandarten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `modul` varchar(64) NOT NULL,
  `keinportocheck` tinyint(1) NOT NULL DEFAULT 0,
  `paketmarke_drucker` int(11) NOT NULL DEFAULT 0,
  `export_drucker` int(11) NOT NULL DEFAULT 0,
  `einstellungen_json` text NOT NULL,
  `ausprojekt` tinyint(1) NOT NULL DEFAULT 0,
  `versandmail` int(11) NOT NULL DEFAULT 0,
  `geschaeftsbrief_vorlage` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `versandpaket_lieferschein_position`
--

DROP TABLE IF EXISTS `versandpaket_lieferschein_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `versandpaket_lieferschein_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `versandpaket` int(11) NOT NULL,
  `lieferschein_position` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `versandpaket_lieferschein_position` (`versandpaket`,`lieferschein_position`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `versandpakete`
--

DROP TABLE IF EXISTS `versandpakete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `versandpakete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `versand` int(11) NOT NULL DEFAULT 0,
  `nr` int(11) NOT NULL DEFAULT 0,
  `tracking` varchar(255) NOT NULL,
  `versender` varchar(255) NOT NULL,
  `gewicht` varchar(10) NOT NULL,
  `bemerkung` text NOT NULL,
  `datum` datetime NOT NULL DEFAULT current_timestamp(),
  `versandart` varchar(64) NOT NULL,
  `lieferschein_ohne_pos` int(11) DEFAULT NULL,
  `tracking_link` text NOT NULL,
  `status` varchar(64) NOT NULL,
  `usereditid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `versandzentrum_log`
--

DROP TABLE IF EXISTS `versandzentrum_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `versandzentrum_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `aktion` varchar(255) NOT NULL,
  `wert` varchar(255) NOT NULL,
  `versandid` int(11) NOT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vertreterumsatz`
--

DROP TABLE IF EXISTS `vertreterumsatz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vertreterumsatz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vertriebid` int(11) NOT NULL DEFAULT 0,
  `userid` int(11) NOT NULL DEFAULT 0,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `objekt` varchar(64) NOT NULL,
  `belegnr` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `parameter` int(11) NOT NULL DEFAULT 0,
  `betrag_netto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `betrag_brutto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `erloes_netto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deckungsbeitrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  `datum` date DEFAULT NULL,
  `waehrung` varchar(3) NOT NULL DEFAULT 'EUR',
  `gruppe` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `provision` decimal(10,2) NOT NULL DEFAULT 0.00,
  `provision_summe` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vorlage`
--

DROP TABLE IF EXISTS `vorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `datum` date NOT NULL,
  `bemerkung` text NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `waage_artikel`
--

DROP TABLE IF EXISTS `waage_artikel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `waage_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `reihenfolge` int(11) NOT NULL DEFAULT 0,
  `beschriftung` varchar(255) NOT NULL,
  `mhddatum` int(11) NOT NULL DEFAULT 0,
  `etikettendrucker` int(11) NOT NULL DEFAULT 0,
  `etikett` int(11) NOT NULL DEFAULT 0,
  `waage` int(11) NOT NULL DEFAULT 0,
  `etikettxml` longblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `waehrung_umrechnung`
--

DROP TABLE IF EXISTS `waehrung_umrechnung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `waehrung_umrechnung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `waehrung_von` varchar(255) NOT NULL,
  `waehrung_nach` varchar(255) NOT NULL,
  `kurs` decimal(16,8) NOT NULL DEFAULT 1.00000000,
  `gueltig_bis` datetime DEFAULT NULL,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  `bearbeiter` varchar(255) DEFAULT NULL,
  `kommentar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warteschlangen`
--

DROP TABLE IF EXISTS `warteschlangen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warteschlangen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warteschlange` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `wiedervorlage` int(11) NOT NULL,
  `adresse` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wawision_uebersetzung`
--

DROP TABLE IF EXISTS `wawision_uebersetzung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wawision_uebersetzung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `sprache` varchar(32) NOT NULL,
  `typ` varchar(32) NOT NULL,
  `original` text NOT NULL,
  `uebersetzung` text NOT NULL,
  `typ1` varchar(255) NOT NULL,
  `typ2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`,`sprache`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail`
--

DROP TABLE IF EXISTS `webmail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL,
  `benutzername` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `server` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail_mails`
--

DROP TABLE IF EXISTS `webmail_mails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail_mails` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `webmail` int(10) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `bcc` varchar(255) NOT NULL,
  `replyto` varchar(255) NOT NULL,
  `plaintext` text NOT NULL,
  `htmltext` text NOT NULL,
  `empfang` datetime NOT NULL,
  `anhang` int(1) NOT NULL,
  `gelesen` int(1) NOT NULL,
  `checksum` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webmail_zuordnungen`
--

DROP TABLE IF EXISTS `webmail_zuordnungen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webmail_zuordnungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` int(11) NOT NULL,
  `zuordnung` varchar(255) NOT NULL,
  `parameter` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage`
--

DROP TABLE IF EXISTS `wiedervorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `adresse_mitarbeier` int(11) NOT NULL DEFAULT 0,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `ergebnis` text NOT NULL,
  `betrag` decimal(10,2) DEFAULT NULL,
  `erinnerung` datetime DEFAULT NULL,
  `erinnerung_per_mail` int(1) DEFAULT NULL,
  `erinnerung_empfaenger` text DEFAULT NULL,
  `link` text DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `bearbeiter` int(11) NOT NULL DEFAULT 0,
  `adresse_mitarbeiter` int(11) NOT NULL DEFAULT 0,
  `datum_angelegt` date DEFAULT NULL,
  `zeit_angelegt` time DEFAULT NULL,
  `datum_erinnerung` date DEFAULT NULL,
  `zeit_erinnerung` time DEFAULT NULL,
  `parameter` int(11) NOT NULL DEFAULT 0,
  `oeffentlich` tinyint(1) NOT NULL DEFAULT 0,
  `abgeschlossen` tinyint(1) NOT NULL DEFAULT 0,
  `ansprechpartner_id` int(11) NOT NULL DEFAULT 0,
  `subproject_id` int(11) NOT NULL DEFAULT 0,
  `chance` int(3) DEFAULT NULL,
  `datum_abschluss` date DEFAULT NULL,
  `datum_status` date DEFAULT NULL,
  `prio` tinyint(1) NOT NULL DEFAULT 0,
  `stages` int(11) NOT NULL DEFAULT 0,
  `color` varchar(64) NOT NULL DEFAULT '#a2d624',
  PRIMARY KEY (`id`),
  KEY `adresse` (`adresse`),
  KEY `adresse_mitarbeiter` (`adresse_mitarbeiter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_aufgabe`
--

DROP TABLE IF EXISTS `wiedervorlage_aufgabe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_aufgabe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resubmission_id` int(10) unsigned NOT NULL,
  `task_id` int(10) unsigned NOT NULL,
  `required_completion_stage_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resubmission_task` (`resubmission_id`,`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_aufgabe_vorlage`
--

DROP TABLE IF EXISTS `wiedervorlage_aufgabe_vorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_aufgabe_vorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `required_from_stage_id` int(10) unsigned NOT NULL DEFAULT 0,
  `add_task_at_stage_id` int(10) unsigned NOT NULL DEFAULT 0,
  `employee_address_id` int(10) unsigned NOT NULL DEFAULT 0,
  `project_id` int(10) unsigned NOT NULL DEFAULT 0,
  `subproject_id` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL,
  `submission_date_days` int(10) NOT NULL DEFAULT 0,
  `submission_time` time NOT NULL,
  `state` varchar(64) NOT NULL,
  `priority` varchar(64) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_board_member`
--

DROP TABLE IF EXISTS `wiedervorlage_board_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_board_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_freifeld_inhalt`
--

DROP TABLE IF EXISTS `wiedervorlage_freifeld_inhalt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_freifeld_inhalt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resubmission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resubmission_id` (`resubmission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_freifeld_konfiguration`
--

DROP TABLE IF EXISTS `wiedervorlage_freifeld_konfiguration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_freifeld_konfiguration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `available_from_stage_id` int(10) unsigned NOT NULL DEFAULT 0,
  `required_from_stage_id` int(10) unsigned NOT NULL DEFAULT 0,
  `show_in_pipeline` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `show_in_tables` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_protokoll`
--

DROP TABLE IF EXISTS `wiedervorlage_protokoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_protokoll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vorgaengerid` int(11) DEFAULT NULL,
  `wiedervorlageid` int(11) DEFAULT NULL,
  `adresse_mitarbeier` int(11) NOT NULL DEFAULT 0,
  `erinnerung_alt` datetime DEFAULT NULL,
  `erinnerung_neu` datetime DEFAULT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `beschreibung` text NOT NULL,
  `ergebnis` text NOT NULL,
  `adresse_mitarbeiter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `wiedervorlageid` (`wiedervorlageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_stages`
--

DROP TABLE IF EXISTS `wiedervorlage_stages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kurzbezeichnung` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `hexcolor` varchar(7) NOT NULL DEFAULT '#a2d624',
  `ausblenden` int(1) NOT NULL,
  `stageausblenden` int(1) NOT NULL,
  `sort` int(11) NOT NULL,
  `view` int(11) NOT NULL DEFAULT 0,
  `chance` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_timeline`
--

DROP TABLE IF EXISTS `wiedervorlage_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wiedervorlage` int(11) NOT NULL,
  `adresse_mitarbeiter` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text NOT NULL,
  `css` varchar(64) NOT NULL,
  `color` varchar(64) NOT NULL,
  `fix` int(11) NOT NULL,
  `user` int(11) NOT NULL DEFAULT 0,
  `stage` int(11) NOT NULL DEFAULT 0,
  `leadtype` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_view`
--

DROP TABLE IF EXISTS `wiedervorlage_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `shortname` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `project` int(11) NOT NULL DEFAULT 0,
  `hide_collection_stage` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiedervorlage_zu_aufgabe_vorlage`
--

DROP TABLE IF EXISTS `wiedervorlage_zu_aufgabe_vorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiedervorlage_zu_aufgabe_vorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wiedervorlage_id` int(10) unsigned NOT NULL DEFAULT 0,
  `aufgabe_vorlage_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki`
--

DROP TABLE IF EXISTS `wiki`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `lastcontent` text DEFAULT NULL,
  `wiki_workspace_id` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `language` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=302 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_changelog`
--

DROP TABLE IF EXISTS `wiki_changelog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_changelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wiki_id` int(11) NOT NULL DEFAULT 0,
  `comment` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text DEFAULT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `wiki_id` (`wiki_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_faq`
--

DROP TABLE IF EXISTS `wiki_faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wiki_id` int(11) NOT NULL DEFAULT 0,
  `question` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `wiki_id` (`wiki_id`)
) ENGINE=InnoDB AUTO_INCREMENT=343 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_subscription`
--

DROP TABLE IF EXISTS `wiki_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wiki_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `wiki_id` (`wiki_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_workspace`
--

DROP TABLE IF EXISTS `wiki_workspace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_workspace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `foldername` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `savein` varchar(32) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wizard`
--

DROP TABLE IF EXISTS `wizard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wizard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `key` varchar(32) NOT NULL,
  `title` varchar(64) NOT NULL,
  `skip_link_text` varchar(64) DEFAULT NULL,
  `params` varchar(512) DEFAULT NULL,
  `options` varchar(512) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wizard_step`
--

DROP TABLE IF EXISTS `wizard_step`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wizard_step` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wizard_id` int(11) NOT NULL DEFAULT 0,
  `key` varchar(32) NOT NULL,
  `link` varchar(255) NOT NULL,
  `title` varchar(64) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `options` varchar(512) DEFAULT NULL,
  `position` tinyint(3) NOT NULL DEFAULT 0,
  `checked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `wizard_id` (`wizard_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zahlungsavis`
--

DROP TABLE IF EXISTS `zahlungsavis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zahlungsavis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date DEFAULT NULL,
  `adresse` int(11) NOT NULL DEFAULT 0,
  `versendet` tinyint(1) NOT NULL DEFAULT 0,
  `versendet_am` date DEFAULT NULL,
  `versendet_per` varchar(64) NOT NULL,
  `ersteller` varchar(64) NOT NULL,
  `bic` varchar(64) NOT NULL,
  `iban` varchar(64) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `bemerkung` varchar(255) NOT NULL,
  `dta_datei` int(11) NOT NULL DEFAULT 0,
  `betrag` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zahlungsavis_gutschrift`
--

DROP TABLE IF EXISTS `zahlungsavis_gutschrift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zahlungsavis_gutschrift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zahlungsavis` int(11) NOT NULL DEFAULT 0,
  `gutschrift` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zahlungsavis_mailausgang`
--

DROP TABLE IF EXISTS `zahlungsavis_mailausgang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zahlungsavis_mailausgang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `avis_id` int(11) NOT NULL,
  `versendet` int(2) NOT NULL DEFAULT 0,
  `versucht` int(11) NOT NULL DEFAULT 0,
  `zeitstempel` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zahlungsavis_rechnung`
--

DROP TABLE IF EXISTS `zahlungsavis_rechnung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zahlungsavis_rechnung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zahlungsavis` int(11) NOT NULL DEFAULT 0,
  `rechnung` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zahlungsweisen`
--

DROP TABLE IF EXISTS `zahlungsweisen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zahlungsweisen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `bezeichnung` varchar(255) NOT NULL,
  `freitext` text NOT NULL,
  `aktiv` tinyint(1) NOT NULL DEFAULT 1,
  `geloescht` tinyint(1) NOT NULL DEFAULT 0,
  `automatischbezahlt` tinyint(1) NOT NULL DEFAULT 0,
  `automatischbezahltverbindlichkeit` tinyint(1) NOT NULL DEFAULT 0,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `vorkasse` tinyint(1) NOT NULL DEFAULT 0,
  `verhalten` varchar(64) NOT NULL DEFAULT 'vorkasse',
  `modul` varchar(64) NOT NULL,
  `einstellungen_json` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zeiterfassung`
--

DROP TABLE IF EXISTS `zeiterfassung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zeiterfassung` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `art` varchar(64) NOT NULL,
  `adresse` int(10) NOT NULL,
  `von` datetime NOT NULL,
  `bis` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aufgabe` varchar(255) NOT NULL,
  `beschreibung` text DEFAULT NULL,
  `arbeitspaket` int(10) NOT NULL,
  `buchungsart` varchar(255) NOT NULL,
  `kostenstelle` varchar(255) NOT NULL,
  `projekt` int(10) DEFAULT 0,
  `abgerechnet` int(10) NOT NULL,
  `logdatei` datetime NOT NULL,
  `status` varchar(64) DEFAULT NULL,
  `gps` varchar(1024) DEFAULT NULL,
  `arbeitsnachweispositionid` int(11) NOT NULL DEFAULT 0,
  `adresse_abrechnung` int(11) DEFAULT NULL,
  `abrechnen` int(1) DEFAULT NULL,
  `ist_abgerechnet` int(1) DEFAULT NULL,
  `gebucht_von_user` int(11) DEFAULT NULL,
  `ort` varchar(1024) DEFAULT NULL,
  `abrechnung_dokument` varchar(1024) DEFAULT NULL,
  `dokumentid` int(11) DEFAULT NULL,
  `verrechnungsart` varchar(255) DEFAULT NULL,
  `arbeitsnachweis` int(11) DEFAULT NULL,
  `internerkommentar` text DEFAULT NULL,
  `aufgabe_id` int(11) NOT NULL DEFAULT 0,
  `auftrag` int(11) NOT NULL DEFAULT 0,
  `auftragpositionid` int(11) NOT NULL DEFAULT 0,
  `produktion` int(11) NOT NULL DEFAULT 0,
  `stundensatz` decimal(5,2) NOT NULL DEFAULT 0.00,
  `arbeitsanweisung` int(11) NOT NULL DEFAULT 0,
  `serviceauftrag` int(11) NOT NULL DEFAULT 0,
  `anz_mitarbeiter` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `adresse_abrechnung` (`adresse_abrechnung`),
  KEY `abgerechnet` (`abgerechnet`),
  KEY `abrechnen` (`abrechnen`),
  KEY `adresse` (`adresse`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zeiterfassungvorlage`
--

DROP TABLE IF EXISTS `zeiterfassungvorlage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zeiterfassungvorlage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vorlage` varchar(255) NOT NULL,
  `ausblenden` tinyint(1) NOT NULL DEFAULT 0,
  `vorlagedetail` text NOT NULL,
  `art` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL DEFAULT 0,
  `teilprojekt` int(11) NOT NULL DEFAULT 0,
  `kunde` int(11) NOT NULL DEFAULT 0,
  `abrechnen` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zertifikatgenerator`
--

DROP TABLE IF EXISTS `zertifikatgenerator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zertifikatgenerator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artikel` int(11) NOT NULL DEFAULT 0,
  `beschreibung_deutsch` text NOT NULL,
  `beschreibung_englisch` text NOT NULL,
  `bestell_anmerkung_deutsch` text NOT NULL,
  `bestell_anmerkung_englisch` text NOT NULL,
  `interne_anmerkung` text NOT NULL,
  `unterschrift` tinyint(1) NOT NULL DEFAULT 0,
  `dateofsale_stamp` tinyint(1) NOT NULL DEFAULT 0,
  `preisfaktor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `kurs_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `typ` int(11) NOT NULL DEFAULT 1,
  `typ_text` text NOT NULL,
  `adresse_kunde` int(11) NOT NULL DEFAULT 0,
  `adresse_absender` text NOT NULL,
  `layout` int(11) NOT NULL DEFAULT 0,
  `zertifikate` tinyint(1) NOT NULL DEFAULT 1,
  `preis_eur` varchar(128) NOT NULL,
  `preis_usd` varchar(128) NOT NULL,
  `erstellt_datum` date DEFAULT NULL,
  `bearbeiter` varchar(128) NOT NULL,
  `preis_eur_retail` varchar(128) NOT NULL,
  `datei` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zolltarifnummer`
--

DROP TABLE IF EXISTS `zolltarifnummer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zolltarifnummer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nummer` varchar(255) DEFAULT NULL,
  `beschreibung` varchar(512) DEFAULT NULL,
  `internebemerkung` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zwischenlager`
--

DROP TABLE IF EXISTS `zwischenlager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zwischenlager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bearbeiter` varchar(255) NOT NULL,
  `projekt` int(11) NOT NULL,
  `artikel` int(11) NOT NULL,
  `menge` decimal(14,4) NOT NULL,
  `vpe` varchar(255) NOT NULL,
  `grund` varchar(255) NOT NULL,
  `lager_von` varchar(255) NOT NULL,
  `lager_nach` varchar(255) NOT NULL,
  `richtung` varchar(255) NOT NULL,
  `erledigt` int(1) NOT NULL,
  `objekt` varchar(255) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `firma` int(11) NOT NULL,
  `logdatei` datetime NOT NULL,
  `paketannahme` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `belege`
--

/*!50001 DROP VIEW IF EXISTS `belege`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`openxe`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `belege` AS select `rechnung`.`id` AS `id`,`rechnung`.`adresse` AS `adresse`,`rechnung`.`datum` AS `datum`,`rechnung`.`belegnr` AS `belegnr`,`rechnung`.`status` AS `status`,`rechnung`.`land` AS `land`,'rechnung' AS `typ`,`rechnung`.`umsatz_netto` AS `umsatz_netto`,`rechnung`.`erloes_netto` AS `erloes_netto`,`rechnung`.`deckungsbeitrag` AS `deckungsbeitrag`,`rechnung`.`provision_summe` AS `provision_summe`,`rechnung`.`vertriebid` AS `vertriebid`,`rechnung`.`gruppe` AS `gruppe` from `rechnung` where `rechnung`.`status` <> 'angelegt' union all select `gutschrift`.`id` AS `id`,`gutschrift`.`adresse` AS `adresse`,`gutschrift`.`datum` AS `datum`,`gutschrift`.`belegnr` AS `belegnr`,`gutschrift`.`status` AS `status`,`gutschrift`.`land` AS `land`,'gutschrift' AS `typ`,`gutschrift`.`umsatz_netto` * -1 AS `umsatz_netto*-1`,`gutschrift`.`erloes_netto` * -1 AS `erloes_netto*-1`,`gutschrift`.`deckungsbeitrag` * -1 AS `deckungsbeitrag*-1`,`gutschrift`.`provision_summe` * -1 AS `provision_summe*-1`,`gutschrift`.`vertriebid` AS `vertriebid`,`gutschrift`.`gruppe` AS `gruppe` from `gutschrift` where `gutschrift`.`status` <> 'angelegt' */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `belegegesamt`
--

/*!50001 DROP VIEW IF EXISTS `belegegesamt`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`openxe`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `belegegesamt` AS select `rechnung`.`id` AS `id`,`rechnung`.`adresse` AS `adresse`,`rechnung`.`datum` AS `datum`,`rechnung`.`belegnr` AS `belegnr`,`rechnung`.`status` AS `status`,`rechnung`.`land` AS `land`,'rechnung' AS `typ`,`rechnung`.`umsatz_netto` AS `umsatz_netto`,`rechnung`.`soll` AS `umsatz_brutto`,`rechnung`.`erloes_netto` AS `erloes_netto`,`rechnung`.`deckungsbeitrag` AS `deckungsbeitrag`,`rechnung`.`provision_summe` AS `provision_summe`,`rechnung`.`vertriebid` AS `vertriebid`,`rechnung`.`gruppe` AS `gruppe`,`rechnung`.`projekt` AS `projekt` from `rechnung` union all select `gutschrift`.`id` AS `id`,`gutschrift`.`adresse` AS `adresse`,`gutschrift`.`datum` AS `datum`,`gutschrift`.`belegnr` AS `belegnr`,`gutschrift`.`status` AS `status`,`gutschrift`.`land` AS `land`,'gutschrift' AS `typ`,`gutschrift`.`umsatz_netto` * -1 AS `umsatz_netto*-1`,`gutschrift`.`soll` * -1 AS `umsatz_brutto*-1`,`gutschrift`.`erloes_netto` * -1 AS `erloes_netto*-1`,`gutschrift`.`deckungsbeitrag` * -1 AS `deckungsbeitrag*-1`,`gutschrift`.`provision_summe` * -1 AS `provision_summe*-1`,`gutschrift`.`vertriebid` AS `vertriebid`,`gutschrift`.`gruppe` AS `gruppe`,`gutschrift`.`projekt` AS `projekt` from `gutschrift` union all select `auftrag`.`id` AS `id`,`auftrag`.`adresse` AS `adresse`,`auftrag`.`datum` AS `datum`,`auftrag`.`belegnr` AS `belegnr`,`auftrag`.`status` AS `status`,`auftrag`.`land` AS `land`,'auftrag' AS `typ`,`auftrag`.`umsatz_netto` AS `umsatz_netto`,`auftrag`.`gesamtsumme` AS `umsatz_brutto`,`auftrag`.`erloes_netto` AS `erloes_netto`,`auftrag`.`deckungsbeitrag` AS `deckungsbeitrag`,`auftrag`.`provision_summe` AS `provision_summe`,`auftrag`.`vertriebid` AS `vertriebid`,`auftrag`.`gruppe` AS `gruppe`,`auftrag`.`projekt` AS `projekt` from `auftrag` union all select `bestellung`.`id` AS `id`,`bestellung`.`adresse` AS `adresse`,`bestellung`.`datum` AS `datum`,`bestellung`.`belegnr` AS `belegnr`,`bestellung`.`status` AS `status`,`bestellung`.`land` AS `land`,'bestellung' AS `typ`,`bestellung`.`gesamtsumme` AS `umsatz_netto`,`bestellung`.`gesamtsumme` AS `umsatz_brutto`,'0' AS `erloes_netto`,'0' AS `deckungsbeitrag`,'0' AS `provision_summe`,'0' AS `vertriebid`,'0' AS `gruppe`,`bestellung`.`projekt` AS `projekt` from `bestellung` union all select `lieferschein`.`id` AS `id`,`lieferschein`.`adresse` AS `adresse`,`lieferschein`.`datum` AS `datum`,`lieferschein`.`belegnr` AS `belegnr`,`lieferschein`.`status` AS `status`,`lieferschein`.`land` AS `land`,'lieferschein' AS `typ`,'0' AS `umsatz_netto`,'0' AS `umsatz_brutto`,'0' AS `erloes_netto`,'0' AS `deckungsbeitrag`,'0' AS `provision_summe`,'0' AS `vertriebid`,'0' AS `gruppe`,`lieferschein`.`projekt` AS `projekt` from `lieferschein` union all select `angebot`.`id` AS `id`,`angebot`.`adresse` AS `adresse`,`angebot`.`datum` AS `datum`,`angebot`.`belegnr` AS `belegnr`,`angebot`.`status` AS `status`,`angebot`.`land` AS `land`,'angebot' AS `typ`,`angebot`.`umsatz_netto` AS `umsatz_netto`,`angebot`.`gesamtsumme` AS `umsatz_brutto`,'0' AS `erloes_netto`,`angebot`.`deckungsbeitrag` AS `deckungsbeitrag`,'0' AS `provision_summe`,`angebot`.`vertriebid` AS `vertriebid`,'0' AS `gruppe`,`angebot`.`projekt` AS `projekt` from `angebot` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `belegeregs`
--

/*!50001 DROP VIEW IF EXISTS `belegeregs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`openxe`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `belegeregs` AS select `rechnung`.`id` AS `id`,`rechnung`.`adresse` AS `adresse`,`rechnung`.`datum` AS `datum`,`rechnung`.`belegnr` AS `belegnr`,`rechnung`.`status` AS `status`,`rechnung`.`land` AS `land`,'rechnung' AS `typ`,`rechnung`.`umsatz_netto` AS `umsatz_netto`,`rechnung`.`erloes_netto` AS `erloes_netto`,`rechnung`.`deckungsbeitrag` AS `deckungsbeitrag`,`rechnung`.`provision_summe` AS `provision_summe`,`rechnung`.`vertriebid` AS `vertriebid`,`rechnung`.`gruppe` AS `gruppe`,`rechnung`.`projekt` AS `projekt` from `rechnung` union all select `gutschrift`.`id` AS `id`,`gutschrift`.`adresse` AS `adresse`,`gutschrift`.`datum` AS `datum`,`gutschrift`.`belegnr` AS `belegnr`,`gutschrift`.`status` AS `status`,`gutschrift`.`land` AS `land`,'gutschrift' AS `typ`,`gutschrift`.`umsatz_netto` * -1 AS `umsatz_netto*-1`,`gutschrift`.`erloes_netto` * -1 AS `erloes_netto*-1`,`gutschrift`.`deckungsbeitrag` * -1 AS `deckungsbeitrag*-1`,`gutschrift`.`provision_summe` * -1 AS `provision_summe*-1`,`gutschrift`.`vertriebid` AS `vertriebid`,`gutschrift`.`gruppe` AS `gruppe`,`gutschrift`.`projekt` AS `projekt` from `gutschrift` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `fibu_buchungen_alle_view`
--

/*!50001 DROP VIEW IF EXISTS `fibu_buchungen_alle_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`openxe`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `fibu_buchungen_alle_view` AS select `fb`.`buchungsart` AS `buchungsart`,`fb`.`typ` AS `typ`,`fb`.`id` AS `id`,if(`fibu_objekte_view`.`datum` <> '',`fibu_objekte_view`.`datum`,`fb`.`datum`) AS `datum`,`fb`.`gegen_typ` AS `doc_typ`,`fb`.`gegen_id` AS `doc_id`,`fibu_objekte_view`.`info` AS `doc_info`,cast(`fb`.`soll` as decimal(10,2)) AS `betrag`,`fb`.`waehrung` AS `waehrung`,`fb`.`edit_module` AS `edit_module`,`fb`.`edit_id` AS `edit_id` from ((select 'umsatz' AS `buchungsart`,'rechnung' AS `typ`,`rechnung`.`id` AS `id`,-`rechnung`.`soll` AS `soll`,`rechnung`.`waehrung` AS `waehrung`,'rechnung' AS `gegen_typ`,`rechnung`.`id` AS `gegen_id`,`rechnung`.`datum` AS `datum`,'rechnung' AS `edit_module`,`rechnung`.`id` AS `edit_id` from `rechnung` where `rechnung`.`belegnr` <> '' union select 'umsatz' AS `umsatz`,'gutschrift' AS `gutschrift`,`gutschrift`.`id` AS `id`,`gutschrift`.`soll` AS `soll`,`gutschrift`.`waehrung` AS `waehrung`,'gutschrift' AS `gutschrift`,`gutschrift`.`id` AS `id`,`gutschrift`.`datum` AS `datum`,'gutschrift' AS `edit_module`,`gutschrift`.`id` AS `id` from `gutschrift` where `gutschrift`.`belegnr` <> '' union select 'aufwand' AS `aufwand`,'verbindlichkeit' AS `verbindlichkeit`,`verbindlichkeit`.`id` AS `id`,`verbindlichkeit`.`betrag` AS `betrag`,`verbindlichkeit`.`waehrung` AS `waehrung`,'verbindlichkeit' AS `verbindlichkeit`,`verbindlichkeit`.`id` AS `id`,`verbindlichkeit`.`rechnungsdatum` AS `rechnungsdatum`,'verbindlichkeit' AS `verbindlichkeit`,`verbindlichkeit`.`id` AS `id` from `verbindlichkeit` where `verbindlichkeit`.`belegnr` <> '' union select 'zahlung' AS `zahlung`,'kontoauszuege' AS `kontoauszuege`,`kontoauszuege`.`id` AS `id`,`kontoauszuege`.`soll` AS `soll`,`kontoauszuege`.`waehrung` AS `waehrung`,'kontoauszuege' AS `kontoauszuege`,`kontoauszuege`.`id` AS `id`,`kontoauszuege`.`buchung` AS `buchung`,'kontoauszuege' AS `kontoauszuege`,`kontoauszuege`.`id` AS `id` from `kontoauszuege` where `kontoauszuege`.`importfehler` is null union select 'abbuchung' AS `abbuchung`,`fibu_buchungen`.`von_typ` AS `von_typ`,`fibu_buchungen`.`von_id` AS `von_id`,`fibu_buchungen`.`betrag` AS `betrag`,`fibu_buchungen`.`waehrung` AS `waehrung`,`fibu_buchungen`.`nach_typ` AS `nach_typ`,`fibu_buchungen`.`nach_id` AS `nach_id`,`fibu_buchungen`.`datum` AS `datum`,'fibu_buchungen' AS `fibu_buchungen`,`fibu_buchungen`.`id` AS `id` from `fibu_buchungen` union select 'zubuchung' AS `zubuchung`,`fibu_buchungen`.`nach_typ` AS `nach_typ`,`fibu_buchungen`.`nach_id` AS `nach_id`,-`fibu_buchungen`.`betrag` AS `-``openxe``.``fibu_buchungen``.``betrag```,`fibu_buchungen`.`waehrung` AS `waehrung`,`fibu_buchungen`.`von_typ` AS `von_typ`,`fibu_buchungen`.`von_id` AS `von_id`,`fibu_buchungen`.`datum` AS `datum`,'fibu_buchungen' AS `fibu_buchungen`,`fibu_buchungen`.`id` AS `id` from `fibu_buchungen`) `fb` left join `fibu_objekte_view` on(`fb`.`gegen_typ` = `fibu_objekte_view`.`typ` and `fb`.`gegen_id` = `fibu_objekte_view`.`id`)) where `fb`.`datum` >= (select `firmendaten_werte`.`wert` from `firmendaten_werte` where `firmendaten_werte`.`name` = 'fibu_buchungen_startdatum') and `fibu_objekte_view`.`datum` >= (select `firmendaten_werte`.`wert` from `firmendaten_werte` where `firmendaten_werte`.`name` = 'fibu_buchungen_startdatum') or `fibu_objekte_view`.`datum` = '' */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `fibu_objekte_view`
--

/*!50001 DROP VIEW IF EXISTS `fibu_objekte_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`openxe`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `fibu_objekte_view` AS select `fo`.`datum` AS `datum`,`fo`.`typ` AS `typ`,`fo`.`id` AS `id`,`fo`.`info` AS `info`,`fo`.`parent_typ` AS `parent_typ`,`fo`.`parent_id` AS `parent_id`,`fo`.`parent_info` AS `parent_info`,`fo`.`typ` in ('rechnung','gutschrift','verbindlichkeit','auftrag') AS `is_beleg` from (select `auftrag`.`datum` AS `datum`,'auftrag' AS `typ`,`auftrag`.`id` AS `id`,`auftrag`.`belegnr` AS `info`,'adresse' AS `parent_typ`,`auftrag`.`adresse` AS `parent_id`,`auftrag`.`name` AS `parent_info` from `auftrag` where `auftrag`.`belegnr` <> '' union select `rechnung`.`datum` AS `datum`,'rechnung' AS `typ`,`rechnung`.`id` AS `id`,`rechnung`.`belegnr` AS `info`,'adresse' AS `parent_type`,`rechnung`.`adresse` AS `parent_id`,`rechnung`.`name` AS `parent_info` from `rechnung` where `rechnung`.`belegnr` <> '' union select `gutschrift`.`datum` AS `datum`,'gutschrift' AS `gutschrift`,`gutschrift`.`id` AS `id`,`gutschrift`.`belegnr` AS `belegnr`,'adresse' AS `parent_type`,`gutschrift`.`adresse` AS `parent_id`,`gutschrift`.`name` AS `parent_info` from `gutschrift` where `gutschrift`.`belegnr` <> '' union select `verbindlichkeit`.`rechnungsdatum` AS `rechnungsdatum`,'verbindlichkeit' AS `verbindlichkeit`,`verbindlichkeit`.`id` AS `id`,`verbindlichkeit`.`rechnung` AS `belegnr`,'adresse' AS `parent_type`,`verbindlichkeit`.`adresse` AS `parent_id`,`adresse`.`name` AS `name` from (`verbindlichkeit` join `adresse` on(`verbindlichkeit`.`adresse` = `adresse`.`id`)) where `verbindlichkeit`.`belegnr` <> '' union select `kontoauszuege`.`buchung` AS `buchung`,'kontoauszuege' AS `kontoauszuege`,`kontoauszuege`.`id` AS `id`,concat(`konten`.`kurzbezeichnung`,' - ',`kontoauszuege`.`buchungstext`) AS `buchungstext`,'konten' AS `parent_type`,`kontoauszuege`.`konto` AS `parent_id`,`konten`.`bezeichnung` AS `bezeichnung` from (`kontoauszuege` left join `konten` on(`konten`.`id` = `kontoauszuege`.`konto`)) union select '' AS `datum`,'kontorahmen' AS `'kontorahmen'`,`kontorahmen`.`id` AS `id`,concat(`kontorahmen`.`sachkonto`,' - ',`kontorahmen`.`beschriftung`) AS `beschriftung`,'','','' from `kontorahmen`) `fo` where `fo`.`datum` >= (select `firmendaten_werte`.`wert` from `firmendaten_werte` where `firmendaten_werte`.`name` = 'fibu_buchungen_startdatum') or `fo`.`datum` = '' */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-05 12:22:40


INSERT INTO `adresse` (`id`, `typ`, `marketingsperre`, `trackingsperre`, `rechnungsadresse`, `sprache`, `name`, `abteilung`, `unterabteilung`, `ansprechpartner`, `land`, `strasse`, `ort`, `plz`, `telefon`, `telefax`, `mobil`, `email`, `ustid`, `ust_befreit`, `passwort_gesendet`, `sonstiges`, `adresszusatz`, `kundenfreigabe`, `steuer`, `logdatei`, `kundennummer`, `lieferantennummer`, `mitarbeiternummer`, `konto`, `blz`, `bank`, `inhaber`, `swift`, `iban`, `waehrung`, `paypal`, `paypalinhaber`, `paypalwaehrung`, `projekt`, `partner`, `zahlungsweise`, `zahlungszieltage`, `zahlungszieltageskonto`, `zahlungszielskonto`, `versandart`, `kundennummerlieferant`, `zahlungsweiselieferant`, `zahlungszieltagelieferant`, `zahlungszieltageskontolieferant`, `zahlungszielskontolieferant`, `versandartlieferant`, `geloescht`, `firma`, `webid`, `vorname`, `kennung`, `sachkonto`, `freifeld1`, `freifeld2`, `freifeld3`, `filiale`, `vertrieb`, `innendienst`, `verbandsnummer`, `abweichendeemailab`, `portofrei_aktiv`, `portofreiab`, `infoauftragserfassung`, `mandatsreferenz`, `mandatsreferenzdatum`, `mandatsreferenzaenderung`, `glaeubigeridentnr`, `kreditlimit`, `tour`, `zahlungskonditionen_festschreiben`, `rabatte_festschreiben`, `mlmaktiv`, `mlmvertragsbeginn`, `mlmlizenzgebuehrbis`, `mlmfestsetzenbis`, `mlmfestsetzen`, `mlmmindestpunkte`, `mlmwartekonto`, `abweichende_rechnungsadresse`, `rechnung_vorname`, `rechnung_name`, `rechnung_titel`, `rechnung_typ`, `rechnung_strasse`, `rechnung_ort`, `rechnung_plz`, `rechnung_ansprechpartner`, `rechnung_land`, `rechnung_abteilung`, `rechnung_unterabteilung`, `rechnung_adresszusatz`, `rechnung_telefon`, `rechnung_telefax`, `rechnung_anschreiben`, `rechnung_email`, `geburtstag`, `rolledatum`, `liefersperre`, `liefersperregrund`, `mlmpositionierung`, `steuernummer`, `steuerbefreit`, `mlmmitmwst`, `mlmabrechnung`, `mlmwaehrungauszahlung`, `mlmauszahlungprojekt`, `sponsor`, `geworbenvon`, `logfile`, `kalender_aufgaben`, `verrechnungskontoreisekosten`, `usereditid`, `useredittimestamp`, `rabatt`, `provision`, `rabattinformation`, `rabatt1`, `rabatt2`, `rabatt3`, `rabatt4`, `rabatt5`, `internetseite`, `bonus1`, `bonus1_ab`, `bonus2`, `bonus2_ab`, `bonus3`, `bonus3_ab`, `bonus4`, `bonus4_ab`, `bonus5`, `bonus5_ab`, `bonus6`, `bonus6_ab`, `bonus7`, `bonus7_ab`, `bonus8`, `bonus8_ab`, `bonus9`, `bonus9_ab`, `bonus10`, `bonus10_ab`, `rechnung_periode`, `rechnung_anzahlpapier`, `rechnung_permail`, `titel`, `anschreiben`, `nachname`, `arbeitszeitprowoche`, `folgebestaetigungsperre`, `lieferantennummerbeikunde`, `verein_mitglied_seit`, `verein_mitglied_bis`, `verein_mitglied_aktiv`, `verein_spendenbescheinigung`, `freifeld4`, `freifeld5`, `freifeld6`, `freifeld7`, `freifeld8`, `freifeld9`, `freifeld10`, `rechnung_papier`, `angebot_cc`, `auftrag_cc`, `rechnung_cc`, `gutschrift_cc`, `lieferschein_cc`, `bestellung_cc`, `angebot_fax_cc`, `auftrag_fax_cc`, `rechnung_fax_cc`, `gutschrift_fax_cc`, `lieferschein_fax_cc`, `bestellung_fax_cc`, `abperfax`, `abpermail`, `kassiereraktiv`, `kassierernummer`, `kassiererprojekt`, `portofreilieferant_aktiv`, `portofreiablieferant`, `mandatsreferenzart`, `mandatsreferenzwdhart`, `serienbrief`, `kundennummer_buchhaltung`, `lieferantennummer_buchhaltung`, `lead`, `zahlungsweiseabo`, `bundesland`, `mandatsreferenzhinweis`, `geburtstagkalender`, `geburtstagskarte`, `liefersperredatum`) VALUES
(1, '', '', 0, 0, '', 'Administrator', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0, '', '2015-10-26 14:19:35', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', 0, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, '', '', NULL, 0, '', 0.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, '0000-00-00 00:00:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 0.00, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, '', 0, 0, 0.00, '', '', 0, '', '', 0, '', '', NULL, 0, 0, NULL);
INSERT INTO `artikeleinheit` (`id`, `einheit_de`, `internebemerkung`) VALUES
(1, 'Stk', 'Stück (Menge)'),
(2, 'g', 'Gramm'),
(3, 'kg', 'Kilogramm'),
(4, 'ml', 'Milliliter'),
(5, 'l', 'Liter'),
(6, 'cm', 'Zentimeter'),
(7, 'm', 'Meter');
INSERT INTO `drucker` (`id`, `name`, `bezeichnung`, `befehl`, `aktiv`, `firma`, `tomail`, `tomailtext`, `tomailsubject`, `adapterboxip`, `adapterboxseriennummer`, `adapterboxpasswort`, `anbindung`, `art`, `faxserver`, `format`, `keinhintergrund`, `json`) VALUES
(1, 'Dokumentendrucker', 'BelegPDF', '', 1, 1, '', '', '', '', '', '', 'download', 0, 0, 'DINA4', 0, ''),
(2, 'Etikettendrucker', 'EtikettenXMLPDF', '', 1, 1, '', '', '', '', '', '', 'download', 2, 0, '', 0, '');
INSERT INTO `exportvorlage` (`id`, `bezeichnung`, `ziel`, `internebemerkung`, `fields`, `fields_where`, `letzterexport`, `mitarbeiterletzterexport`, `exporttrennzeichen`, `exporterstezeilenummer`, `exportdatenmaskierung`, `exportzeichensatz`, `filterdatum`, `filterprojekt`, `apifreigabe`) VALUES
(1, 'Standard Artikel Export (Format siehe Wiki)', 'artikel', '', 'nummer;\r\nname_de;\r\nname_en;\r\nbeschreibung_de;\r\nbeschreibung_en;\r\nkurztext_de;\r\nkurztext_en;\r\ninternerkommentar;\r\nhersteller;\r\nherstellernummer;\r\nherstellerlink;\r\nean;', NULL, '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, 0, 0);
INSERT INTO `firma` (`id`, `name`, `standardprojekt`) VALUES
(1, 'Musterfirma GmbH', 1);
INSERT INTO `firmendaten` (`id`, `firma`, `logo`, `briefpapier`, `benutzername`, `passwort`, `host`, `port`, `mailssl`, `signatur`, `datum`, `steuersatz_normal`, `steuersatz_zwischen`, `steuersatz_ermaessigt`, `steuersatz_starkermaessigt`, `steuersatz_dienstleistung`, `deviceserials`, `lizenz`, `schluessel`, `mlm_mindestbetrag`, `mlm_letzter_tag`, `mlm_erster_tag`, `mlm_letzte_berechnung`, `mlm_01`, `mlm_02`, `mlm_03`, `mlm_04`, `mlm_05`, `mlm_06`, `mlm_07`, `mlm_08`, `mlm_09`, `mlm_10`, `mlm_11`, `mlm_12`, `mlm_13`, `mlm_14`, `mlm_15`, `zahlung_rechnung_sofort_de`, `zahlung_rechnung_de`, `zahlung_vorkasse_de`, `zahlung_lastschrift_de`, `zahlung_nachnahme_de`, `zahlung_bar_de`, `zahlung_paypal_de`, `zahlung_amazon_de`, `zahlung_kreditkarte_de`, `zahlung_ratenzahlung_de`, `briefpapier2`, `freifeld1`, `freifeld2`, `freifeld3`, `freifeld4`, `freifeld5`, `freifeld6`, `firmenfarbehell`, `firmenfarbedunkel`, `firmenfarbeganzdunkel`, `navigationfarbe`, `navigationfarbeschrift`, `unternavigationfarbe`, `unternavigationfarbeschrift`, `firmenlogo`, `rechnung_header`, `lieferschein_header`, `angebot_header`, `auftrag_header`, `gutschrift_header`, `bestellung_header`, `arbeitsnachweis_header`, `provisionsgutschrift_header`, `rechnung_footer`, `lieferschein_footer`, `angebot_footer`, `auftrag_footer`, `gutschrift_footer`, `bestellung_footer`, `arbeitsnachweis_footer`, `provisionsgutschrift_footer`, `eu_lieferung_vermerk`, `export_lieferung_vermerk`, `zahlung_amazon_bestellung_de`, `zahlung_billsafe_de`, `zahlung_sofortueberweisung_de`, `zahlung_secupay_de`, `adressefreifeld1`, `adressefreifeld2`, `adressefreifeld3`, `adressefreifeld4`, `adressefreifeld5`, `adressefreifeld6`, `adressefreifeld7`, `adressefreifeld8`, `adressefreifeld9`, `adressefreifeld10`, `zahlung_eckarte_de`, `devicekey`, `mailanstellesmtp`, `layout_iconbar`, `bcc1`, `bcc2`, `firmenfarbe`, `name`, `betreffszeile`, `dokumententext`) VALUES
(1, 1, '', '', 'mustermann1', 'passwort', 'smtp.ihr_mail_server.de', '25', 1, 'LS0NCk11c3RlcmZpcm1hIEdtYkgNCk11c3RlcndlZyA1DQpELTEyMzQ1IE11c3RlcnN0YWR0DQoNClRlbCArNDkgMTIzIDEyIDM0IDU2IDcNCkZheCArNDkgMTIzIDEyIDM0IDU2IDc4DQoNCk5hbWUgZGVyIEdlc2VsbHNjaGFmdDogTXVzdGVyZmlybWEgR21iSA0KU2l0eiBkZXIgR2VzZWxsc2NoYWZ0OiBNdXN0ZXJzdGFkdA0KDQpIYW5kZWxzcmVnaXN0ZXI6IE11c3RlcnN0YWR0LCBIUkIgMTIzNDUNCkdlc2Now6RmdHNmw7xocnVuZzogTWF4IE11c3Rlcm1hbg0KVVN0LUlkTnIuOiBERTEyMzQ1Njc4OQ0KDQpBR0I6IGh0dHA6Ly93d3cubXVzdGVyZmlybWEuZGUvDQo=', '0000-00-00 00:00:00', 19.00, 7.00, 7.00, 7.00, 7.00, '', '', '', 50.00, NULL, NULL, NULL, 15.00, 20.00, 28.00, 32.00, 36.00, 40.00, 44.00, 44.00, 44.00, 44.00, 50.00, 54.00, 45.00, 48.00, 60.00, 'Rechnung zahlbar sofort.', 'Rechnung zahlbar innerhalb {ZAHLUNGSZIELTAGE} Tage bis zum {ZAHLUNGBISDATUM}.', '', '', '', '', '', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', '', 'Musterfirma GmbH', 9, 9);

UPDATE `firmendaten` SET `rechnung_footer` = '{IF}{LIEFERADRESSE}{THEN}<strong>Lieferadresse:</strong><br />{LIEFERADRESSE}{ELSE}{ENDIF}<br />Dieses Formular wurde maschinell erstellt und ist ohne Unterschrift g&uuml;ltig.' WHERE `id` = 1;
UPDATE `firmendaten` SET `rechnung_header` = '{ANSCHREIBEN},<br /><br />anbei Ihre Rechnung.<br /><br />{IF}{INTERNET}{THEN}Bestellnummer: {INTERNET}<br />{ELSE}{ENDIF}{IF}{TRANSAKTIONSNUMMER}{THEN}Transaktionsnummer: {TRANSAKTIONSNUMMER}<br />{ELSE}{ENDIF}{IF}{USTID}{THEN}Ihre USt-ID: {USTID}<br />{ELSE}{ENDIF}{IF}{LIEFERBEDINGUNG}{THEN}Lieferbedingung: {LIEFERBEDINGUNG}<br />{ELSE}{ENDIF}' WHERE `id` = 1;

INSERT INTO `firmendaten_werte` (`id`, `name`, `typ`, `typ1`, `typ2`, `wert`, `default_value`, `default_null`, `darf_null`) VALUES
(1, 'absender', 'varchar', '1024', '', 'Musterfirma GmbH | Musterweg 5 | 12345 Musterstadt', '', 1, 0),
(2, 'sichtbar', 'int', '1', '', '1', '1', 0, 0),
(3, 'barcode', 'int', '1', '', '1', '0', 0, 0),
(4, 'schriftgroesse', 'int', '1', '', '9', '0', 0, 0),
(7, 'zeilenuntertext', 'int', '1', '', '7', '0', 0, 0),
(9, 'infobox', 'int', '1', '', '8', '0', 0, 0),
(10, 'spaltenbreite', 'int', '1', '', '0', '0', 0, 0),
(11, 'footer_0_0', 'varchar', '512', '', 'Sitz der Gesellschaft / Lieferanschrift', '', 1, 0),
(12, 'footer_0_1', 'varchar', '512', '', 'Musterfirma GmbH', '', 1, 0),
(13, 'footer_0_2', 'varchar', '512', '', 'Musterweg 5', '', 1, 0),
(14, 'footer_0_3', 'varchar', '512', '', 'D-12345 Musterstadt', '', 1, 0),
(15, 'footer_0_4', 'varchar', '512', '', 'Telefon +49 123 12 34 56 7', '', 1, 0),
(16, 'footer_0_5', 'varchar', '512', '', 'Telefax +49 123 12 34 56 78', '', 1, 0),
(17, 'footer_1_0', 'varchar', '64', '', 'Bankverbindung', '', 1, 0),
(18, 'footer_1_1', 'varchar', '64', '', 'Musterbank', '', 1, 0),
(19, 'footer_1_2', 'varchar', '64', '', 'Konto 123456789', '', 1, 0),
(20, 'footer_1_3', 'varchar', '64', '', 'BLZ 72012345', '', 1, 0),
(21, 'footer_1_4', 'varchar', '64', '', '', '', 1, 0),
(22, 'footer_1_5', 'varchar', '64', '', '', '', 1, 0),
(23, 'footer_2_0', 'varchar', '64', '', 'IBAN DE1234567891234567891', '', 1, 0),
(24, 'footer_2_1', 'varchar', '64', '', 'BIC/SWIFT DETSGDBWEMN', '', 1, 0),
(25, 'footer_2_2', 'varchar', '64', '', 'Ust-IDNr. DE123456789', '', 1, 0),
(26, 'footer_2_3', 'varchar', '64', '', 'E-Mail: info@musterfirma-gmbh.de', '', 1, 0),
(27, 'footer_2_4', 'varchar', '64', '', 'Internet: http://www.musterfirma.de', '', 1, 0),
(28, 'footer_2_5', 'varchar', '64', '', '', '', 1, 0),
(29, 'footer_3_0', 'varchar', '64', '', 'Geschäftsführer', '', 1, 0),
(30, 'footer_3_1', 'varchar', '64', '', 'Max Musterman', '', 1, 0),
(31, 'footer_3_2', 'varchar', '64', '', 'Handelsregister: HRB 12345', '', 1, 0),
(32, 'footer_3_3', 'varchar', '64', '', 'Amtsgericht: Musterstadt', '', 1, 0),
(33, 'footer_3_4', 'varchar', '64', '', '', '', 1, 0),
(34, 'footer_3_5', 'varchar', '64', '', '', '', 1, 0),
(36, 'hintergrund', 'varchar', '64', '', 'kein', '', 1, 0),
(37, 'logo_type', 'varchar', '64', '', '', '', 1, 0),
(38, 'briefpapier_type', 'varchar', '64', '', '', '', 1, 0),
(39, 'email', 'varchar', '64', '', 'mail@ihr_mail_server.de', '', 1, 0),
(40, 'absendername', 'varchar', '64', '', 'Meine Firma', '', 1, 0),
(41, 'strasse', 'varchar', '64', '', 'Musterweg 5', '', 1, 0),
(42, 'plz', 'varchar', '64', '', '12345', '', 1, 0),
(43, 'ort', 'varchar', '64', '', 'Musterstadt', '', 1, 0),
(44, 'steuernummer', 'varchar', '64', '', 'DE123456789', '', 1, 0),
(45, 'startseite_wiki', 'varchar', '64', '', '', '', 1, 0),
(46, 'projekt', 'int', '11', '', '1', '', 1, 1),
(48, 'next_angebot', 'varchar', '64', '', '100000', '', 1, 0),
(49, 'next_auftrag', 'varchar', '64', '', '200000', '', 1, 0),
(50, 'next_gutschrift', 'varchar', '64', '', '900000', '', 1, 0),
(51, 'next_lieferschein', 'varchar', '64', '', '300000', '', 1, 0),
(52, 'next_bestellung', 'varchar', '64', '', '100000', '', 1, 0),
(53, 'next_rechnung', 'varchar', '64', '', '400000', '', 1, 0),
(54, 'next_kundennummer', 'varchar', '64', '', '10000', '', 1, 0),
(55, 'next_lieferantennummer', 'varchar', '64', '', '70000', '', 1, 0),
(56, 'next_mitarbeiternummer', 'varchar', '64', '', '90000', '', 1, 0),
(57, 'next_waren', 'varchar', '64', '', '', '', 1, 0),
(58, 'next_sonstiges', 'varchar', '64', '', '', '', 1, 0),
(59, 'next_produktion', 'varchar', '64', '', '400000', '', 1, 0),
(60, 'breite_position', 'int', '11', '', '10', '10', 0, 0),
(61, 'breite_menge', 'int', '11', '', '10', '10', 0, 0),
(62, 'breite_nummer', 'int', '11', '', '20', '20', 0, 0),
(63, 'breite_einheit', 'int', '11', '', '15', '15', 0, 0),
(64, 'skonto_ueberweisung_ueberziehen', 'int', '11', '', '0', '0', 0, 0),
(65, 'kleinunternehmer', 'int', '1', '', '0', '0', 0, 0),
(66, 'mahnwesenmitkontoabgleich', 'int', '1', '', '1', '1', 0, 0),
(67, 'porto_berechnen', 'int', '1', '', '0', '0', 0, 0),
(68, 'immernettorechnungen', 'int', '1', '', '0', '0', 0, 0),
(69, 'schnellanlegen', 'int', '1', '', '1', '0', 0, 0),
(70, 'bestellvorschlaggroessernull', 'int', '1', '', '0', '0', 0, 0),
(71, 'versand_gelesen', 'int', '1', '', '0', '0', 0, 0),
(72, 'versandart', 'varchar', '64', '', 'versandunternehmen', '', 0, 0),
(73, 'zahlungsweise', 'varchar', '64', '', 'rechnung', '', 0, 0),
(74, 'zahlung_lastschrift_konditionen', 'int', '1', '', '0', '0', 0, 0),
(75, 'breite_artikelbeschreibung', 'tinyint', '1', '', '1', '0', 0, 0),
(76, 'deviceenable', 'tinyint', '1', '', '0', '0', 0, 0),
(77, 'etikettendrucker_wareneingang', 'int', '11', '', '0', '0', 0, 0),
(78, 'waehrung', 'varchar', '64', '', 'EUR', 'EUR', 0, 0),
(79, 'footer_breite1', 'int', '11', '', '50', '50', 0, 0),
(82, 'footer_breite4', 'int', '11', '', '40', '40', 0, 0),
(83, 'boxausrichtung', 'varchar', '64', '', 'L', 'R', 0, 0),
(84, 'branch', 'varchar', '64', '', '', '', 0, 0),
(85, 'version', 'varchar', '64', '', '15.4.f7412d4', '', 0, 0),
(86, 'standard_datensaetze_datatables', 'int', '11', '', '0', '10', 0, 0),
(87, 'auftrag_bezeichnung_vertrieb', 'varchar', '64', '', 'Vertrieb', 'Vertrieb', 0, 0),
(88, 'auftrag_bezeichnung_bearbeiter', 'varchar', '64', '', 'Bearbeiter', 'Bearbeiter', 0, 0),
(89, 'auftrag_bezeichnung_bestellnummer', 'varchar', '64', '', 'Ihre Bestellnummer', 'Ihre Bestellnummer', 0, 0),
(90, 'bezeichnungkundennummer', 'varchar', '64', '', 'Kundennummer', 'Kundennummer', 0, 0),
(91, 'bezeichnungstornorechnung', 'varchar', '64', '', 'Stornorechnung', 'Stornorechnung', 0, 0),
(92, 'bestellungohnepreis', 'tinyint', '1', '', '0', '0', 0, 0),
(94, 'rechnung_gutschrift_ansprechpartner', 'int', '1', '', '0', '1', 0, 0),
(95, 'api_initkey', 'varchar', '1024', '', '', '', 0, 0),
(96, 'api_remotedomain', 'varchar', '1024', '', '', '', 0, 0),
(97, 'api_eventurl', 'varchar', '1024', '', '', '', 0, 0),
(98, 'api_enable', 'int', '1', '', '0', '0', 0, 0),
(99, 'api_cleanutf8', 'tinyint', '1', '', '1', '1', 0, 0),
(100, 'api_importwarteschlange', 'int', '1', '', '0', '0', 0, 0),
(101, 'api_importwarteschlange_name', 'varchar', '64', '', '', '', 0, 0),
(102, 'wareneingang_zwischenlager', 'int', '1', '', '0', '1', 0, 0),
(103, 'modul_mlm', 'int', '1', '', '0', '0', 0, 0),
(104, 'modul_verband', 'int', '1', '', '0', '0', 0, 0),
(105, 'modul_mhd', 'int', '1', '', '0', '0', 0, 0),
(106, 'mhd_warnung_tage', 'int', '11', '', '3', '3', 0, 0),
(107, 'mlm_anzahlmonate', 'int', '11', '', '11', '11', 0, 0),
(108, 'mlm_01_punkte', 'int', '11', '', '2999', '2999', 0, 0),
(109, 'mlm_02_punkte', 'int', '11', '', '3000', '3000', 0, 0),
(110, 'mlm_03_punkte', 'int', '11', '', '5000', '5000', 0, 0),
(111, 'mlm_04_punkte', 'int', '11', '', '10000', '10000', 0, 0),
(112, 'mlm_05_punkte', 'int', '11', '', '15000', '15000', 0, 0),
(113, 'mlm_06_punkte', 'int', '11', '', '25000', '25000', 0, 0),
(114, 'mlm_07_punkte', 'int', '11', '', '50000', '50000', 0, 0),
(115, 'mlm_08_punkte', 'int', '11', '', '100000', '100000', 0, 0),
(116, 'mlm_09_punkte', 'int', '11', '', '150000', '150000', 0, 0),
(117, 'mlm_10_punkte', 'int', '11', '', '200000', '200000', 0, 0),
(118, 'mlm_11_punkte', 'int', '11', '', '250000', '250000', 0, 0),
(119, 'mlm_12_punkte', 'int', '11', '', '300000', '300000', 0, 0),
(120, 'mlm_13_punkte', 'int', '11', '', '350000', '350000', 0, 0),
(121, 'mlm_14_punkte', 'int', '11', '', '400000', '400000', 0, 0),
(122, 'mlm_15_punkte', 'int', '11', '', '450000', '450000', 0, 0),
(123, 'mlm_01_mindestumsatz', 'int', '11', '', '50', '50', 0, 0),
(124, 'mlm_02_mindestumsatz', 'int', '11', '', '50', '50', 0, 0),
(125, 'mlm_03_mindestumsatz', 'int', '11', '', '50', '50', 0, 0),
(126, 'mlm_04_mindestumsatz', 'int', '11', '', '50', '50', 0, 0),
(127, 'mlm_05_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(128, 'mlm_06_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(129, 'mlm_07_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(130, 'mlm_08_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(131, 'mlm_09_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(132, 'mlm_10_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(133, 'mlm_11_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(134, 'mlm_12_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(135, 'mlm_13_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(136, 'mlm_14_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(137, 'mlm_15_mindestumsatz', 'int', '11', '', '100', '100', 0, 0),
(138, 'standardaufloesung', 'int', '11', '', '0', '0', 0, 0),
(139, 'standardversanddrucker', 'int', '11', '', '0', '0', 0, 0),
(140, 'standardetikettendrucker', 'int', '11', '', '0', '0', 0, 0),
(141, 'externereinkauf', 'int', '1', '', '0', '', 1, 1),
(142, 'schriftart', 'varchar', '64', '', '', '', 1, 1),
(143, 'knickfalz', 'int', '1', '', '0', '', 1, 1),
(144, 'artikeleinheit', 'int', '1', '', '0', '', 1, 1),
(145, 'artikeleinheit_standard', 'varchar', '64', '', '', '', 1, 1),
(147, 'abstand_boxrechtsoben_lr', 'int', '11', '', '0', '0', 0, 0),
(148, 'zahlungszieltage', 'int', '11', '', '14', '14', 0, 0),
(149, 'zahlungszielskonto', 'int', '11', '', '2', '', 1, 0),
(150, 'zahlungszieltageskonto', 'int', '11', '', '10', '', 1, 0),
(151, 'zahlung_rechnung', 'int', '1', '', '1', '1', 0, 0),
(152, 'zahlung_vorkasse', 'int', '1', '', '1', '1', 0, 0),
(153, 'zahlung_nachnahme', 'int', '1', '', '1', '1', 0, 0),
(154, 'zahlung_kreditkarte', 'int', '1', '', '1', '1', 0, 0),
(155, 'zahlung_paypal', 'int', '1', '', '1', '1', 0, 0),
(156, 'zahlung_bar', 'int', '1', '', '1', '1', 0, 0),
(157, 'zahlung_lastschrift', 'int', '1', '', '1', '0', 0, 0),
(158, 'zahlung_amazon', 'int', '1', '', '1', '0', 0, 0),
(159, 'zahlung_ratenzahlung', 'int', '1', '', '1', '1', 0, 0),
(160, 'briefpapier2vorhanden', 'int', '1', '', '0', '', 1, 1),
(161, 'artikel_suche_kurztext', 'int', '1', '', '1', '', 1, 1),
(162, 'adresse_freitext1_suche', 'int', '1', '', '0', '0', 0, 0),
(163, 'iconset_dunkel', 'tinyint', '1', '', '0', '0', 0, 0),
(164, 'warnung_doppelte_nummern', 'int', '1', '', '1', '1', 0, 0),
(165, 'next_arbeitsnachweis', 'varchar', '64', '', '300000', '', 1, 1),
(166, 'next_reisekosten', 'varchar', '64', '', '31000', '', 1, 1),
(167, 'next_anfrage', 'varchar', '64', '', '50000', '', 1, 1),
(168, 'next_artikelnummer', 'varchar', '64', '', '1000000', '', 0, 0),
(169, 'seite_von_ausrichtung', 'varchar', '64', '', 'R', '', 1, 1),
(170, 'seite_von_sichtbar', 'int', '1', '', '1', '', 1, 1),
(171, 'parameterundfreifelder', 'int', '1', '', '0', '', 1, 1),
(172, 'firmenlogotype', 'varchar', '64', '', '', '', 1, 1),
(173, 'firmenlogoaktiv', 'int', '1', '', '0', '', 1, 1),
(174, 'projektnummerimdokument', 'int', '1', '', '0', '', 1, 1),
(175, 'herstellernummerimdokument', 'int', '1', '', '0', '', 1, 1),
(176, 'standardmarge', 'int', '11', '', '30', '', 1, 1),
(177, 'steuer_erloese_inland_normal', 'varchar', '10', '', '4400', '', 0, 0),
(178, 'steuer_aufwendung_inland_normal', 'varchar', '10', '', '5400', '', 0, 0),
(179, 'steuer_erloese_inland_ermaessigt', 'varchar', '10', '', '4300', '', 0, 0),
(180, 'steuer_aufwendung_inland_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(181, 'steuer_erloese_inland_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(182, 'steuer_aufwendung_inland_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(183, 'steuer_erloese_inland_innergemeinschaftlich', 'varchar', '10', '', '4125', '', 0, 0),
(184, 'steuer_aufwendung_inland_innergemeinschaftlich', 'varchar', '10', '', '5425', '', 0, 0),
(185, 'steuer_erloese_inland_eunormal', 'varchar', '10', '', '4315', '', 0, 0),
(186, 'steuer_aufwendung_inland_eunormal', 'varchar', '10', '', '', '', 0, 0),
(187, 'steuer_erloese_inland_export', 'varchar', '10', '', '4120', '', 0, 0),
(188, 'steuer_aufwendung_inland_import', 'varchar', '10', '', '', '', 0, 0),
(189, 'steuer_anpassung_kundennummer', 'varchar', '10', '', '', '', 0, 0),
(190, 'steuer_art_1', 'varchar', '30', '', '', '', 0, 0),
(191, 'steuer_art_1_normal', 'varchar', '10', '', '', '', 0, 0),
(192, 'steuer_art_1_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(193, 'steuer_art_1_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(194, 'steuer_art_2', 'varchar', '30', '', '', '', 0, 0),
(195, 'steuer_art_2_normal', 'varchar', '10', '', '', '', 0, 0),
(196, 'steuer_art_2_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(197, 'steuer_art_2_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(198, 'steuer_art_3', 'varchar', '30', '', '', '', 0, 0),
(199, 'steuer_art_3_normal', 'varchar', '10', '', '', '', 0, 0),
(200, 'steuer_art_3_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(201, 'steuer_art_3_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(202, 'steuer_art_4', 'varchar', '30', '', '', '', 0, 0),
(203, 'steuer_art_4_normal', 'varchar', '10', '', '', '', 0, 0),
(204, 'steuer_art_4_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(205, 'steuer_art_4_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(206, 'steuer_art_5', 'varchar', '30', '', '', '', 0, 0),
(207, 'steuer_art_5_normal', 'varchar', '10', '', '', '', 0, 0),
(208, 'steuer_art_5_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(209, 'steuer_art_5_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(210, 'steuer_art_6', 'varchar', '30', '', '', '', 0, 0),
(211, 'steuer_art_6_normal', 'varchar', '10', '', '', '', 0, 0),
(212, 'steuer_art_6_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(213, 'steuer_art_6_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(214, 'steuer_art_7', 'varchar', '30', '', '', '', 0, 0),
(215, 'steuer_art_7_normal', 'varchar', '10', '', '', '', 0, 0),
(216, 'steuer_art_7_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(217, 'steuer_art_7_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(218, 'steuer_art_8', 'varchar', '30', '', '', '', 0, 0),
(219, 'steuer_art_8_normal', 'varchar', '10', '', '', '', 0, 0),
(220, 'steuer_art_8_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(221, 'steuer_art_8_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(222, 'steuer_art_9', 'varchar', '30', '', '', '', 0, 0),
(223, 'steuer_art_9_normal', 'varchar', '10', '', '', '', 0, 0),
(224, 'steuer_art_9_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(225, 'steuer_art_9_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(226, 'steuer_art_10', 'varchar', '30', '', '', '', 0, 0),
(227, 'steuer_art_10_normal', 'varchar', '10', '', '', '', 0, 0),
(228, 'steuer_art_10_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(229, 'steuer_art_10_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(230, 'steuer_art_11', 'varchar', '30', '', '', '', 0, 0),
(231, 'steuer_art_11_normal', 'varchar', '10', '', '', '', 0, 0),
(232, 'steuer_art_11_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(233, 'steuer_art_11_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(234, 'steuer_art_12', 'varchar', '30', '', '', '', 0, 0),
(235, 'steuer_art_12_normal', 'varchar', '10', '', '', '', 0, 0),
(236, 'steuer_art_12_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(237, 'steuer_art_12_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(238, 'steuer_art_13', 'varchar', '30', '', '', '', 0, 0),
(239, 'steuer_art_13_normal', 'varchar', '10', '', '', '', 0, 0),
(240, 'steuer_art_13_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(241, 'steuer_art_13_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(242, 'steuer_art_14', 'varchar', '30', '', '', '', 0, 0),
(243, 'steuer_art_14_normal', 'varchar', '10', '', '', '', 0, 0),
(244, 'steuer_art_14_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(245, 'steuer_art_14_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(246, 'steuer_art_15', 'varchar', '30', '', '', '', 0, 0),
(247, 'steuer_art_15_normal', 'varchar', '10', '', '', '', 0, 0),
(248, 'steuer_art_15_ermaessigt', 'varchar', '10', '', '', '', 0, 0),
(249, 'steuer_art_15_steuerfrei', 'varchar', '10', '', '', '', 0, 0),
(250, 'rechnung_ohnebriefpapier', 'int', '1', '', '0', '', 1, 1),
(251, 'lieferschein_ohnebriefpapier', 'int', '1', '', '0', '', 1, 1),
(252, 'angebot_ohnebriefpapier', 'int', '1', '', '0', '', 1, 1),
(253, 'auftrag_ohnebriefpapier', 'int', '1', '', '0', '', 1, 1),
(254, 'gutschrift_ohnebriefpapier', 'int', '1', '', '0', '', 1, 1),
(255, 'bestellung_ohnebriefpapier', 'int', '1', '', '0', '', 1, 1),
(256, 'arbeitsnachweis_ohnebriefpapier', 'int', '1', '', '0', '', 1, 1),
(257, 'abstand_adresszeileoben', 'int', '11', '', '0', '', 1, 1),
(258, 'abstand_boxrechtsoben', 'int', '11', '', '0', '', 1, 1),
(261, 'wareneingang_kamera_waage', 'int', '1', '', '0', '', 1, 1),
(262, 'briefhtml', 'tinyint', '1', '', '1', '0', 0, 0),
(263, 'seite_von_ausrichtung_relativ', 'tinyint', '1', '', '0', '0', 0, 0),
(264, 'absenderunterstrichen', 'tinyint', '1', '', '1', '1', 0, 0),
(265, 'schriftgroesseabsender', 'int', '11', '', '7', '7', 0, 0),
(266, 'datatables_export_button_flash', 'tinyint', '1', '', '1', '1', 0, 0),
(267, 'land', 'varchar', '2', '', 'DE', 'DE', 0, 0),
(268, 'modul_finanzbuchhaltung', 'tinyint', '1', '', '0', '0', 0, 0),
(269, 'testmailempfaenger', 'varchar', '128', '', '', '', 0, 0),
(270, 'immerbruttorechnungen', 'int', '1', '', '0', '0', 0, 0),
(271, 'sepaglaeubigerid', 'varchar', '64', '', '', '', 0, 0),
(272, 'viernachkommastellen_belege', 'tinyint', '1', '', '0', '0', 0, 0),
(273, 'bezeichnungangebotersatz', 'varchar', '64', '', '', '', 0, 0),
(274, 'stornorechnung_standard', 'int', '1', '', '0', '0', 0, 0),
(275, 'angebotersatz_standard', 'int', '1', '', '0', '0', 0, 0),
(276, 'modul_verein', 'int', '1', '', '0', '0', 0, 0),
(277, 'abstand_gesamtsumme_lr', 'int', '11', '', '100', '100', 0, 0),
(278, 'zahlung_amazon_bestellung', 'int', '1', '', '0', '0', 0, 0),
(279, 'zahlung_billsafe', 'int', '1', '', '0', '0', 0, 0),
(280, 'zahlung_sofortueberweisung', 'int', '1', '', '0', '0', 0, 0),
(281, 'zahlung_secupay', 'int', '1', '', '0', '0', 0, 0),
(282, 'artikel_bilder_uebersicht', 'tinyint', '1', '', '0', '0', 0, 0),
(283, 'steuer_erloese_inland_nichtsteuerbar', 'varchar', '10', '', '', '', 0, 0),
(284, 'steuer_erloese_inland_euermaessigt', 'varchar', '10', '', '', '', 0, 0),
(285, 'steuer_aufwendung_inland_nichtsteuerbar', 'varchar', '10', '', '', '', 0, 0),
(286, 'steuer_aufwendung_inland_euermaessigt', 'varchar', '10', '', '', '', 0, 0),
(287, 'abstand_seitenrandlinks', 'int', '11', '', '15', '15', 0, 0),
(288, 'abstand_adresszeilelinks', 'int', '11', '', '15', '15', 0, 0),
(289, 'wareneingang_gross', 'int', '1', '', '0', '0', 0, 0),
(290, 'barcode_x_header', 'int', '11', '', '12', '12', 0, 0),
(291, 'barcode_x', 'int', '11', '', '12', '12', 0, 0),
(292, 'barcode_y_header', 'int', '11', '', '0', '0', 0, 0),
(294, 'abstand_seiten_unten', 'int', '11', '', '34', '34', 0, 0),
(295, 'mailgrussformel', 'varchar', '1024', '', '\r\n\r\n\r\nFür Rückfragen stehe ich Ihnen gerne zur Verfügung.\r\n\r\nMit freundlichen Grüßen\r\n{MITARBEITER}', '\r\n\r\n\r\nFür Rückfragen stehe ich Ihnen gerne zur Verfügung.\r\n\r\nMit freundlichen Grüßen\r\n{MITARBEITER}', 0, 0),
(296, 'geburtstagekalender', 'tinyint', '1', '', '1', '1', 0, 0),
(297, 'bezeichnungauftragersatz', 'varchar', '64', '', 'Proformarechnung', 'Proformarechnung', 0, 0),
(298, 'bezeichnungrechnungersatz', 'varchar', '64', '', 'Quittung', 'Quittung', 0, 0),
(299, 'footer_zentriert', 'int', '1', '', '0', '0', 0, 0),
(300, 'footer_farbe', 'int', '11', '', '30', '30', 0, 0),
(301, 'zahlung_einzugsermaechtigung', 'int', '1', '', '0', '', 1, 0),
(302, 'zahlung_eckarte', 'int', '1', '', '0', '0', 0, 0),
(303, 'abseite2y', 'int', '11', '', '50', '50', 0, 0),
(304, 'artikel_freitext1_suche', 'int', '1', '', '0', '0', 0, 0),
(305, 'next_kalkulation', 'varchar', '255', '', '', '', 1, 1),
(306, 'next_preisanfrage', 'varchar', '255', '', '', '', 1, 1),
(307, 'next_proformarechnung', 'varchar', '255', '', '', '', 1, 1),
(308, 'adressefreifeld1typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(309, 'adressefreifeld2typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(310, 'adressefreifeld3typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(311, 'adressefreifeld4typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(312, 'adressefreifeld5typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(313, 'adressefreifeld6typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(314, 'adressefreifeld7typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(315, 'adressefreifeld8typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(316, 'adressefreifeld9typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(317, 'adressefreifeld10typ', 'varchar', '16', '', 'einzeilig', 'einzeilig', 0, 0),
(318, 'adressefreifeld1spalte', 'int', '11', '', '0', '0', 0, 0),
(319, 'adressefreifeld2spalte', 'int', '11', '', '0', '0', 0, 0),
(320, 'adressefreifeld3spalte', 'int', '11', '', '0', '0', 0, 0),
(321, 'adressefreifeld4spalte', 'int', '11', '', '0', '0', 0, 0),
(322, 'adressefreifeld5spalte', 'int', '11', '', '0', '0', 0, 0),
(323, 'adressefreifeld6spalte', 'int', '11', '', '0', '0', 0, 0),
(324, 'adressefreifeld7spalte', 'int', '11', '', '0', '0', 0, 0),
(325, 'adressefreifeld8spalte', 'int', '11', '', '0', '0', 0, 0),
(326, 'adressefreifeld9spalte', 'int', '11', '', '0', '0', 0, 0),
(327, 'adressefreifeld10spalte', 'int', '11', '', '0', '0', 0, 0),
(328, 'adressefreifeld1sort', 'int', '11', '', '0', '0', 0, 0),
(329, 'adressefreifeld2sort', 'int', '11', '', '0', '0', 0, 0),
(330, 'adressefreifeld3sort', 'int', '11', '', '0', '0', 0, 0),
(331, 'adressefreifeld4sort', 'int', '11', '', '0', '0', 0, 0),
(332, 'adressefreifeld5sort', 'int', '11', '', '0', '0', 0, 0),
(333, 'adressefreifeld6sort', 'int', '11', '', '0', '0', 0, 0),
(334, 'adressefreifeld7sort', 'int', '11', '', '0', '0', 0, 0),
(335, 'adressefreifeld8sort', 'int', '11', '', '0', '0', 0, 0),
(336, 'adressefreifeld9sort', 'int', '11', '', '0', '0', 0, 0),
(337, 'adressefreifeld10sort', 'int', '11', '', '0', '0', 0, 0),
(338, 'wareneingangauftragzubestellung', 'tinyint', '1', '', '0', '0', 0, 0),
(339, 'freifelderimdokument', 'int', '1', '', '0', '0', 0, 0),
(340, 'internetnummerimbeleg', 'int', '1', '', '', '', 1, 1),
(341, 'beschriftunginternetnummer', 'varchar', '64', '', 'Internetnummer', 'Internetnummer', 0, 0),
(342, 'briefpapier_bearbeiter_ausblenden', 'tinyint', '1', '', '0', '0', 0, 0),
(343, 'briefpapier_vertrieb_ausblenden', 'tinyint', '1', '', '0', '0', 0, 0),
(344, 'auftragmarkierenegsaldo', 'tinyint', '1', '', '0', '0', 0, 0),
(345, 'breite_artikel', 'int', '11', '', '76', '76', 1, 1),
(346, 'breite_steuer', 'int', '11', '', '15', '', 1, 1),
(347, 'next_verbindlichkeit', 'varchar', '255', '', '', '', 1, 1),
(375, 'cleaner_logfile', 'tinyint', '1', '', '1', '1', 0, 0),
(376, 'cleaner_logfile_tage', 'int', '11', '', '90', '90', 0, 0),
(377, 'cleaner_shopexportlog', 'tinyint', '1', '', '1', '1', 0, 0),
(378, 'cleaner_shopexportlog_tage', 'int', '11', '', '90', '90', 0, 0),
(379, 'cleaner_versandzentrum', 'tinyint', '1', '', '1', '1', 0, 0),
(380, 'cleaner_versandzentrum_tage', 'int', '11', '', '90', '90', 0, 0),
(381, 'cleaner_uebertragungen', 'tinyint', '1', '', '1', '1', 0, 0),
(382, 'cleaner_uebertragungen_tage', 'int', '11', '', '90', '90', 0, 0),
(383, 'cleaner_protokoll', 'tinyint', '1', '', '1', '1', 0, 0),
(384, 'cleaner_protokoll_tage', 'int', '11', '', '90', '90', 0, 0),
(385, 'cleaner_shopimport', 'tinyint', '1', '', '1', '1', 0, 0),
(386, 'cleaner_shopimport_tage', 'int', '11', '', '90', '90', 0, 0),
(387, 'cleaner_adapterbox', 'tinyint', '1', '', '1', '1', 0, 0),
(388, 'cleaner_adapterbox_tage', 'int', '11', '', '90', '90', 0, 0),
(389, 'bcc3', 'varchar', '128', '', '', '', 0, 0),
(390, 'rechnungersatz_standard', 'int', '1', '', '0', '0', 0, 0)

;

INSERT INTO `geschaeftsbrief_vorlagen` (`id`, `sprache`, `betreff`, `text`, `subjekt`, `projekt`, `firma`) VALUES
(1, 'deutsch', 'Bestellung {BELEGNR} von {FIRMA}', '{ANSCHREIBEN},<br><br>anbei übersenden wir Ihnen unsere Bestellung zu. Bitte senden Sie uns als Bestätigung für den Empfang eine Auftragsbestätigung zu.', 'Bestellung', 1, 1),
(2, 'englisch', 'Order {BELEGNR} from {FIRMA}', 'Dear Sir or Madam,<br><br>enclosed we send our order. <br>Please send as an acknowledgment.', 'Bestellung', 1, 1),
(3, 'deutsch', 'Betreff: {BETREFF}', '{ANSCHREIBEN}', 'Korrespondenz', 1, 1),
(5, 'deutsch', 'Lieferschein {BELEGNR} von {FIRMA}', '{ANSCHREIBEN}, <br><br>anbei übersenden wir Ihnen unseren Lieferschein zu.', 'Lieferschein', 1, 1),
(6, 'deutsch', 'Ihr Angebot {BELEGNR} von {FIRMA}', '{ANSCHREIBEN}, <br><br>anbei das gewünschte Angebot. Wir hoffen Ihnen die passenden Artikel anbieten zu können.', 'Angebot', 1, 1),
(7, 'deutsch', 'Auftragsbestätigung {BELEGNR} von {FIRMA}', '{ANSCHREIBEN},<br><br>anbei übersenden wir Ihnen Ihre Auftragsbestätigung. ', 'Auftrag', 1, 1),
(8, 'deutsch', 'Ihre Rechnung {BELEGNR} von {FIRMA}', '{ANSCHREIBEN},<br><br><br>anbei finden Sie Ihre Rechnung. Gerne stehen wir Ihnen weiterhin zur Verfügung.<br><br>Ihre Rechnung ist im PDF-Format erstellt worden. Um sich die Rechnung ansehen zu können, klicken Sie auf den Anhang und es öffnet sich automatisch der Acrobat Reader. Sollten Sie keinen Acrobat Reader besitzen, haben wir für Sie den Link zum kostenlosen Download von Adobe Acrobat Reader mit angegeben. Er führt Sie automatisch auf die Downloadseite von Adobe. So können Sie sich Ihre Rechnung auch für Ihre Unterlagen ausdrucken.<br><br>http://www.adobe.com/products/acrobat/readstep2.html', 'Rechnung', 1, 1),
(9, 'deutsch', 'Versand Ihrer Bestellung von {FIRMA}', '{ANSCHREIBEN},<br><br>soeben wurde Ihr Bestellung zusammengestellt und wird in Kürze unserem Versandunternehmen übergeben.<br><br>{VERSAND}<br><br>Ihr {FIRMA} Team<br>', 'Versand', 1, 1),
(10, 'deutsch', 'Eingang Ihrer Zahlung', '{ANSCHREIBEN},<br><br><br>Ihre Zahlung zum Auftrag Nr. {AUFTRAG} vom {DATUM} in Höhe von {GESAMT} EUR konnte zugeordnet werden.<br><br><br>Vielen Dank.<br><br>Ihr {FIRMA} Team<br>', 'ZahlungOK', 1, 1),
(11, 'deutsch', 'Fehlbetrag bei Eingang Ihrer Zahlung', '{ANSCHREIBEN},<br><br>bezüglich Ihrer Zahlung zum Auftrag Nr. {AUFTRAG} vom {DATUM} in Höhe von {GESAMT} EUR gab es bei der Zuordnung eine Zahlungsdifferenz von {REST} EUR.<br><br><br>Bitte überweisen Sie noch den Fehlbetrag in Höhe von {REST} EUR mit dem angegebenen Verwendungszweck auf unser Konto:<br><br>Verwendungszweck: {AUFTRAG}<br><br>{FIRMA}<br>IBAN: XXXXXX<br>BIC: YYYYYYYYY<br><br>Bitte beachten Sie bei der Zahlung: eventuelle Gebühren dürfen nicht zu unseren Lasten gehen.<br><br><br>Ihr {FIRMA} Team<br>', 'ZahlungDiff', 1, 1),
(12, 'deutsch', 'Stornierung Ihres Auftrags', '{ANSCHREIBEN},<br><br><br>Ihr Auftrag Nr. {AUFTRAG} vom {DATUM} wurde soeben aus unserem System storniert.<br><br>Bereits bezahltes Auftragsguthaben erstatten wir Ihnen in den nächsten Tagen auf dem gleichen Weg (Bank, Paypal, Kreditkarte, etc.) Ihrer Zahlung zurück. <br><br>Sollten Daten für die Zahlung fehlen, wird ein Sachbearbeiter mit Ihnen Kontakt aufnehmen.<br><br>Vielen Dank.<br><br>Ihr {FIRMA} Team<br>', 'Stornierung', 1, 1),
(14, 'deutsch', 'Vorkasse Ihrer Bestellung', '{ANSCHREIBEN},<br><br>vielen Dank nochmals für Ihre Bestellung.<br><br>Bezüglich Ihres Auftrags Nr. {AUFTRAG} vom {DATUM} in Höhe von {GESAMT} EUR senden wir Ihnen die Zahlungsinformationen zu. Sollten Sie <br>zwischenzeitlich den Betrag bereits überwiesen haben, sehen Sie diese E-Mail bitte als gegenstandslos an.<br><br>Bitte überweisen Sie den Betrag in Höhe von {REST} EUR mit dem angegebenen Verwendungszweck auf unser Konto:<br><br>Verwendungszweck: {AUFTRAG}<br>Betrag: {REST}<br><br>{FIRMA}<br>Bank:Deutsche Bank<br>IBAN: DE1234567<br>BIC: DEUTDEMM720<br><br>Bitte beachten Sie bei der Zahlung: eventuelle Gebühren dürfen nicht zu unseren Lasten gehen.<br><br><br>Ihr {FIRMA} Team<br>', 'ZahlungMiss', 1, 1),
(15, 'deutsch', 'Betriebsurlaub vom 09.08 bis 24.08.2010', 'Liebe Kunden,<br><br>wir sind vom 09.08.2010 bis zum 24.08.2010 im Betriebsurlaub.<br>Ihre Anfragen werden deshalb erst wieder nach diesem Zeitraum bearbeitet.<br><br>Ihre Bestellungen werden in dieser Zeit statt täglich wöchentlich versendet.*<br><br>Wir wünschen Ihnen eine schöne Ferienzeit und bedanken uns für Ihr Verständnis.<br><br>Das {FIRMA} Team<br><br><br><br>*sofern sich die Ware bei uns im Lager befindet.', 'Betriebsurlaub', 0, 1),
(16, 'deutsch', 'Zusammenstellung Ihrer Bestellung', '{ANSCHREIBEN},<br><br>soeben wurde Ihr Bestellung zusammengestellt. Sie können Ihre Ware jetzt abholen. Sind Sie bereits bei uns gewesen, so sehen Sie diese E-Mail bitte als gegenstandslos an.<br><br>{VERSAND}<br><br>Ihr {FIRMA} Team<br>', 'Selbstabholer', 0, 1),
(17, 'deutsch', 'Ihre Gutschrift {BELEGNR} von {FIRMA}', '{ANSCHREIBEN},<br><br>anbei finden Sie Ihre Gutschrift. Gerne stehen wir Ihnen weiterhin zur Verfügung.<br><br>Ihre Gutschrift ist im PDF-Format erstellt worden. Um sich die Gutschrift ansehen zu können, klicken Sie auf den Anhang und es öffnet sich automatisch der Acrobat Reader. Sollten Sie keinen Acrobat Reader besitzen, haben wir für Sie den Link zum kostenlosen Download von Adobe Acrobat Reader mit angegeben. Er führt Sie automatisch auf die Downloadseite von Adobe. So können Sie sich Ihre Gutschrift auch für Ihre Unterlagen ausdrucken.<br><br>http://www.adobe.com/products/acrobat/readstep2.html<br><br>{IF}{INTERNET}{THEN}Internet-Bestellnr.: {INTERNET}{ELSE}{ENDIF}', 'Gutschrift', 1, 1);


/* OpenXE 2024-01-24 für datatablelabel */
INSERT INTO `hook` (`name`, `aktiv`, `parametercount`, `alias`, `description`) VALUES
('eproosystem_ende', 1, 0, '', ''),
('parseuservars', 1, 0, '', ''),
('dokumentsend_ende', 1, 0, '', ''),
('auftrag_versand_ende', 1, 0, '', ''),
('transfer_document_incoming', 1, 0, '', '')
;

INSERT INTO `hook_register` (`hook_action`, `function`, `aktiv`, `position`, `hook`, `module`, `module_parameter`) VALUES
(0, 'DataTableLabelsInclude', 1, 3, (SELECT id FROM hook WHERE name = 'eproosystem_ende'), 'Datatablelabels', 0),
(0, 'DatatablelabelsParseUserVars', 1, 2, (SELECT id FROM hook WHERE name = 'parseuservars'), 'Datatablelabels', 0),
(0, 'DataTableLabelsDokumentSendHook', 1, 1, (SELECT id FROM hook WHERE name = 'dokumentsend_ende'), 'Datatablelabels', 0),
(0, 'DatatablelabelsOrderSent', 1, 1, (SELECT id FROM hook WHERE name = 'auftrag_versand_ende'), 'Datatablelabels', 0),
(0, 'DatatablelabelsTransferDocumentIncomming', 1, 1, (SELECT id FROM hook WHERE name = 'transfer_document_incoming'), 'Datatablelabels', 0);
/* OpenXE 2024-01-24 für datatablelabel */

/* OpenXE 2024-02-03 für belegvorlagen */
INSERT INTO `hook` (`name`, `aktiv`, `parametercount`, `alias`, `description`) VALUES
('BelegPositionenButtons', 1, 3, '', ''),
('AARLGPositionen_cmds_end', 1, 1, '', ''),
('ajax_filter_hook1', 1, 1, '', '');

INSERT INTO `hook_register` (`hook_action`, `function`, `aktiv`, `position`, `hook`, `module`, `module_parameter`) VALUES
(0, 'BelegevorlagenAARLGPositionen_cmds_end', 1, 2, (SELECT id FROM hook WHERE name = 'AARLGPositionen_cmds_end' LIMIT 1), 'belegevorlagen', 0),
(0, 'Belegevorlagenajax_filter_hook1', 1, 2, (SELECT id FROM hook WHERE name = 'ajax_filter_hook1' LIMIT 1), 'belegevorlagen', 0),
(0, 'BelegevorlagenBelegPositionenButtons', 1, 2, (SELECT id FROM hook WHERE name = 'BelegPositionenButtons' LIMIT 1), 'belegevorlagen', 0)
;
/* OpenXE 2024-02-03 für belegvorlagen */

/* OpenXE 2024-08-11 für TOTP */
INSERT INTO `hook`(`name`, `aktiv`, `parametercount`, `alias`, `description`) VALUES
('login_password_check_otp', 1, 3, '', '');

INSERT INTO `hook_register`(
 `hook_action`, `function`, `aktiv`, `position`, `hook`, `module`, `module_parameter`) VALUES
 ( 0, 'TOTPCheckLogin', 1, 1, (SELECT id FROM hook WHERE NAME = 'login_password_check_otp' LIMIT 1), 'totp', 0);
/* OpenXE 2024-08-11 für TOTP */

/* OpenXE 2024-08-11 für Smarty shopimport */
INSERT INTO `hook` (`name`, `aktiv`, `parametercount`, `alias`, `description`) VALUES
('ImportAuftragBefore', 1, 4, '', '');

INSERT INTO `hook_register` (`hook_action`, `function`, `aktiv`, `position`, `hook`, `module`, `module_parameter`) VALUES
(0, 'ImportAuftragBeforeHook', 1, 1, (SELECT id FROM hook WHERE name = 'ImportAuftragBefore' LIMIT 1), 'onlineshops', 0);
/* OpenXE 2024-08-11 für Smarty shopimport */

/* OpenXE 2025-06-03 Reportfreigabe */
INSERT INTO `hook` (`name`, `aktiv`, `parametercount`, `alias`, `description`) VALUES
('ajax_filter_hook1', 1, 5, '', '');

INSERT INTO `hook_register` (`hook_action`, `function`, `aktiv`, `position`, `hook`, `module`, `module_parameter`) VALUES
(0, 'AjaxAutocompleteFilterUser', 1, 5, (SELECT id FROM hook WHERE name = 'ajax_filter_hook1' LIMIT 1), 'report', 0);
/* OpenXE 2025-06-03 Reportfreigabe */

/*
BelegPositionenButtons

Id,Hook_action,Function,Aktiv,Position,Hook,Module,Module_parameter
20,0,BelegevorlagenBelegPositionenButtons,1,2,16,belegevorlagen,0
*/

INSERT INTO `hook_menu` (`id`, `module`, `aktiv`) VALUES
(1, 'artikel', 1),
(2, 'provisionenartikel', 1),
(3, 'startseite', 1);
INSERT INTO `importvorlage` (`id`, `bezeichnung`, `ziel`, `internebemerkung`, `fields`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`, `utf8decode`, `charset`) VALUES
(1, '01 A - Initialer Artikelimport', 'artikel', '', '1:nummer;\r\n2:name_de;\r\n3:beschreibung_de;\r\n4:kurztext_de;\r\n5:internerkommentar;\r\n6:hersteller;\r\n7:ean;\r\n8:gewicht;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '', 0, ''),
(2, '01 B - Artikel mit Staffelpreisen (nach Artikelimport 01 A)', 'artikel', '', '1:nummer;\r\n2:verkaufspreis1netto;\r\n3:verkaufspreis1menge;\r\n4:verkaufspreis2netto;\r\n5:verkaufspreis2menge;\r\n6:verkaufspreis3netto;\r\n7:verkaufspreis3menge;\r\n8:verkaufspreis4netto;\r\n9:verkaufspreis4menge;\r\n10:verkaufspreis5netto;\r\n11:verkaufspreis5menge;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '', 0, ''),
(3, '01 C - Artikel mit Gruppenpreisen (nach Artikelimport 01 A)', 'artikel', '', '1:nummer;\r\n2:verkaufspreis1netto;\r\n3:verkaufspreis1menge;\r\n4:verkaufspreis1gruppe;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '', 0, ''),
(4, '01 D - Stücklistenartikel (nach Artikelimport 01 A)', 'artikel', '', '1:nummer;\r\n2:stuecklistevonartikel;\r\n3:stuecklistemenge;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '', 0, ''),
(5, '01 E - Variantenartikel verknüpfen (nach Artikelimport 01 A)', 'artikel', '', '1:nummer;\r\n2:variante_von;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '', 0, ''),
(6, '01 F - Matrix-Artikel (nach Artikelimport 01 A)', 'artikel', '', '1:nummer;\r\n2:matrixproduktvon;\r\n3:matrixproduktgruppe1;\r\n4:matrixproduktgruppe2;\r\n5:matrixproduktwert1;\r\n6:matrixproduktwert2;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '', 0, ''),
(7, '01 G - Chargen-Artikel (nach Artikelimport 01 A)', 'artikel', '', '1:nummer;\r\n2:chargenverwaltung;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '', 0, ''),
(8, '01 I - Einkaufspreise (nach Artikelimport 01 A)', 'artikel', '', '1:nummer;\r\n2:lieferantennummer;\r\n3:lieferanteinkaufnetto;\r\n4:lieferanteinkaufmenge;\r\n5:lieferantbestellnummer;\r\n6:lieferanteinkaufwaehrung;\r\n7:lieferantname;\r\n8:lieferantartikelbezeichnung;\r\n9:lieferanteinkaufvpemenge;\r\n10:lieferanteinkaufvpepreis;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(9, '01 H - Verkaufspreise (nach Artikelimport 01 A)', 'artikel', '', '1:nummer;\r\n2:verkaufspreis1netto;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(10, '01 J - Shopzuordnung mit Fremdnummern (nach Artikelimport 01 A)', 'artikel', 'Hinweis: \r\nDie Ziffer (im Beispiel 1 oder 2) bitte durch Shop-ID ersetzen. \r\nWie Du die ID ermitteln kannst findest Du im Handbuch.', '1:nummer;\r\n2:fremdnummer_1; (Ziffer 1 bitte durch Shop-ID ersetzen, siehe Handbuch)\r\n3:fremdnummerbezeichnung_1; \r\n4:shop_1;\r\n5:aktiv_1;\r\n6:fremdnummer_2; (Ziffer 2 bitte durch Shop-ID ersetzen, siehe Handbuch)\r\n7:fremdnummerbezeichnung_2;\r\n8:shop_2;\r\n9:aktiv_2;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(11, '01 K - Dienstleistungsartikel (nach Artikelimport 01 A)', 'artikel', '', '1:nummer;\r\n2:name_de;\r\n3:artikelbeschreibung_de;\r\n4:kurztext_de;\r\n5:internerkommentar;\r\n6:hersteller;\r\n7:ean;\r\n8:gewicht;\r\n9:lieferantennummer;\r\n10:lieferantbestellnummer;\r\n11:lieferanteinkaufnetto;\r\n12:lieferanteinkaufmenge;\r\n13:verkaufspreis1netto;\r\n14:verkaufspreis1menge;\r\n\"0\":lagerartikel;\r\n16:lager_platz;\r\n17:lager_menge_total;\r\n18:mindestlager;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(12, '01 Z - Vollständiger Artikelimport (Alle verfügbaren Importfelder)', 'artikel', 'Prinzipiell ist es möglich bis zu 50 Eigenschaften anzugeben, die jeweils drei Spalten beinhalten. In dieser Vorlage sind die Eigenschaften beispielhhaft enthalten und wurden auf 10 beschränkt.', '1:nummer;\r\n2:name_de;\r\n3:name_en;\r\n4:artikelbeschreibung_de;\r\n5:artikelbeschreibung_en;\r\n6:kurztext_de;\r\n7:kurztext_en;\r\n8:uebersicht_de;\r\n9:uebersicht_en;\r\n10:metatitle_de;\r\n11:metatitle_en;\r\n12:metadescription_de;\r\n13:metadescription_en;\r\n14:metakeywords_de;\r\n15:metakeywords_en;\r\n16:artikelkategorie;\r\n17:artikelkategorie_name;\r\n18:artikelbaum1;\r\n19:artikelbaum2;\r\n20:artikelbaum3;\r\n21:artikelbaum4;\r\n22:artikelbaum5;\r\n23:artikelbaum6;\r\n24:artikelbaum7;\r\n25:artikelbaum8;\r\n26:artikelbaum9;\r\n27:artikelbaum10;\r\n28:artikelbaum11;\r\n29:artikelbaum12;\r\n30:artikelbaum13;\r\n31:artikelbaum14;\r\n32:artikelbaum15;\r\n33:artikelbaum16;\r\n34:artikelbaum17;\r\n35:artikelbaum18;\r\n36:artikelbaum19;\r\n37:artikelbaum20;\r\n38:bildtitel1;\r\n39:bildbeschreibung1;\r\n40:bildtitel2;\r\n41:bildbeschreibung2;\r\n42:bildtitel3;\r\n43:bildbeschreibung3;\r\n44:bildtitel4;\r\n45:bildbeschreibung4;\r\n46:bildtitel5;\r\n47:bildbeschreibung5;\r\n48:bildtitel6;\r\n49:bildbeschreibung6;\r\n50:bildtitel7;\r\n51:bildbeschreibung7;\r\n52:bildtitel8;\r\n53:bildbeschreibung8;\r\n54:bildtitel9;\r\n55:bildbeschreibung9;\r\n56:bildtitel10;\r\n57:bildbeschreibung10;\r\n58:bildtitel11;\r\n59:bildbeschreibung11;\r\n60:bildtitel12;\r\n61:bildbeschreibung12;\r\n62:bildtitel13;\r\n63:bildbeschreibung13;\r\n64:bildtitel14;\r\n65:bildbeschreibung14;\r\n66:bildtitel15;\r\n67:bildbeschreibung15;\r\n68:bildtitel16;\r\n69:bildbeschreibung16;\r\n70:bildtitel17;\r\n71:bildbeschreibung17;\r\n72:bildtitel18;\r\n73:bildbeschreibung18;\r\n74:bildtitel19;\r\n75:bildbeschreibung19;\r\n76:bildtitel20;\r\n77:bildbeschreibung20;\r\n78:internerkommentar;\r\n79:hersteller;\r\n80:herstellerlink;\r\n81:herstellernummer;\r\n82:ean;\r\n83:herstellerland;\r\n84:herkunftsland;\r\n85:zolltarifnummer;\r\n86:ursprungsregion;\r\n87:allelieferanten;\r\n88:standardlieferant;\r\n89:geraet;\r\n90:serviceartikel;\r\n91:inventurek;\r\n92:inventurekaktiv;\r\n93:berechneterek;\r\n94:berechneterekwaehrung;\r\n95:verwendeberechneterek;\r\n96:steuer_erloese_inland_normal;\r\n97:steuer_erloese_inland_ermaessigt;\r\n98:steuer_aufwendung_inland_nichtsteuerbar;\r\n99:steuer_erloese_inland_innergemeinschaftlich;\r\n100:steuer_erloese_inland_eunormal;\r\n101:steuer_erloese_inland_euermaessigt;\r\n102:steuer_erloese_inland_export;\r\n103:steuer_aufwendung_inland_normal;\r\n104:steuer_aufwendung_inland_ermaessigt;\r\n105:steuer_aufwendung_inland_nichtsteuerbar;\r\n106:steuer_aufwendung_inland_innergemeinschaftlich;\r\n107:steuer_aufwendung_inland_eunormal;\r\n108:steuer_aufwendung_inland_euermaessigt;\r\n109:steuer_aufwendung_inland_import;\r\n110:mindesthaltbarkeitsdatum;\r\n111:seriennummern;\r\n112:chargenverwaltung;\r\n113:mindestlager;\r\n114:mindestbestellung;\r\n115:umsatzsteuer;\r\n116:artikelautokalkulation;\r\n117:artikelabschliessenkalkulation;\r\n118:artikelfifokalkulation;\r\n119:lieferantname;\r\n120:lieferantbestellnummer;\r\n121:lieferantartikelbezeichnung;\r\n122:lieferanteinkaufnetto;\r\n123:lieferanteinkaufwaehrung;\r\n124:lieferanteinkaufmenge;\r\n125:lieferanteinkaufvpemenge;\r\n126:lieferanteinkaufvpepreis;\r\n127:lieferantbestellnummer2;\r\n128:lieferantartikelbezeichnung2;\r\n129:lieferanteinkaufnetto2;\r\n130:lieferanteinkaufwaehrung2;\r\n131:lieferanteinkaufmenge2;\r\n132:lieferanteinkaufvpemenge2;\r\n133:lieferanteinkaufvpepreis2;\r\n134:lieferantbestellnummer3;\r\n135:lieferantartikelbezeichnung3;\r\n136:lieferanteinkaufnetto3;\r\n137:lieferanteinkaufwaehrung3;\r\n138:lieferanteinkaufmenge3;\r\n139:lieferanteinkaufvpemenge3;\r\n140:lieferanteinkaufvpepreis3;\r\n141:lieferantrahmenvertrag_von;\r\n142:lieferantrahmenvertrag_bis;\r\n143:lieferantpreis_anfrage_vom;\r\n144:lieferantgueltig_bis;\r\n145:lieferantdatum_lagerlieferant;\r\n146:lieferantsicherheitslager;\r\n147:lieferantrahmenvertrag_menge;\r\n148:lieferantlieferzeit_aktuell;\r\n149:lieferantlieferzeit_standard;\r\n150:lieferantlager_lieferant;\r\n151:lieferantrahmenvertrag;\r\n152:lieferantbemerkung;\r\n153:lieferantnichtberechnet;\r\n154:lieferantrahmenvertrag_von1;\r\n155:lieferantrahmenvertrag_bis1;\r\n156:lieferantpreis_anfrage_vom1;\r\n157:lieferantgueltig_bis1;\r\n158:lieferantdatum_lagerlieferant1;\r\n159:lieferantsicherheitslager1;\r\n160:lieferantrahmenvertrag_menge1;\r\n161:lieferantlieferzeit_aktuell1;\r\n162:lieferantlieferzeit_standard1;\r\n163:lieferantlager_lieferant1;\r\n164:lieferantrahmenvertrag1;\r\n165:lieferantbemerkung1;\r\n166:lieferantnichtberechnet1;\r\n167:lieferantrahmenvertrag_von2;\r\n168:lieferantrahmenvertrag_bis2;\r\n169:lieferantpreis_anfrage_vom2;\r\n170:lieferantgueltig_bis2;\r\n171:lieferantdatum_lagerlieferant2;\r\n172:lieferantsicherheitslager2;\r\n173:lieferantrahmenvertrag_menge2;\r\n174:lieferantlieferzeit_aktuell2;\r\n175:lieferantlieferzeit_standard2;\r\n176:lieferantlager_lieferant2;\r\n177:lieferantrahmenvertrag2;\r\n178:lieferantbemerkung2;\r\n179:lieferantnichtberechnet2;\r\n180:lieferantrahmenvertrag_von3;\r\n181:lieferantrahmenvertrag_bis3;\r\n182:lieferantpreis_anfrage_vom3;\r\n183:lieferantgueltig_bis3;\r\n184:lieferantdatum_lagerlieferant3;\r\n185:lieferantsicherheitslager3;\r\n186:lieferantrahmenvertrag_menge3;\r\n187:lieferantlieferzeit_aktuell3;\r\n188:lieferantlieferzeit_standard3;\r\n189:lieferantlager_lieferant3;\r\n190:lieferantrahmenvertrag3;\r\n191:lieferantbemerkung3;\r\n192:lieferantnichtberechnet3;\r\n193:lieferantennummer;\r\n194:standardlieferant;\r\n195:kundennummer;\r\n196:gewicht;\r\n197:breite;\r\n198:hoehe;\r\n199:laenge;\r\n200:einheit;\r\n201:xvp;\r\n202:produktion;\r\n203:lagerartikel;\r\n204:lager_platz;\r\n205:lager_menge_addieren;\r\n206:lager_menge_total;\r\n207:lager_mhd;\r\n208:lager_charge;\r\n209:lager_platz2;\r\n210:lager_menge_addieren2;\r\n211:lager_menge_total2;\r\n212:lager_mhd2;\r\n213:lager_charge2;\r\n214:lager_platz3;\r\n215:lager_menge_addieren3;\r\n216:lager_menge_total3;\r\n217:lager_mhd3;\r\n218:lager_charge3;\r\n219:lager_platz4;\r\n220:lager_menge_addieren4;\r\n221:lager_menge_total4;\r\n222:lager_mhd4;\r\n223:lager_charge4;\r\n224:lager_platz5;\r\n225:lager_menge_addieren5;\r\n226:lager_menge_total5;\r\n227:lager_mhd5;\r\n228:lager_charge5;\r\n229:lager_vpe_menge1;\r\n230:lager_vpe_gewicht1;\r\n231:lager_vpe_laenge1;\r\n232:lager_vpe_breite1;\r\n233:lager_vpe_hoehe;\r\n234:lager_vpe_menge2;\r\n235:lager_vpe_gewicht2;\r\n236:lager_vpe_laenge2;\r\n237:lager_vpe_breite2;\r\n238:lager_vpe_hoehe2;\r\n239:lager_vpe_menge3;\r\n240:lager_vpe_gewicht3;\r\n241:lager_vpe_laenge3;\r\n242:lager_vpe_breite3;\r\n243:lager_vpe_hoehe3;\r\n244:lager_vpe_menge4;\r\n245:lager_vpe_gewicht4;\r\n246:lager_vpe_laenge4;\r\n247:lager_vpe_breite4;\r\n248:lager_vpe_hoehe4;\r\n249:lager_vpe_menge5;\r\n250:lager_vpe_gewicht5;\r\n251:lager_vpe_laenge5;\r\n252:lager_vpe_breite5;\r\n253:lager_vpe_hoehe5;\r\n254:verkaufspreis1netto;\r\n255:verkaufspreis1preisfuermenge;\r\n256:verkaufspreis1menge;\r\n257:verkaufspreis1waehrung;\r\n258:verkaufspreis1gruppe;\r\n259:verkaufspreis1kundennummer;\r\n260:verkaufspreis1artikelnummerbeikunde;\r\n261:verkaufspreis1gueltigab;\r\n262:verkaufspreis1gueltigbis;\r\n263:verkaufspreis1internerkommentar;\r\n264:verkaufspreis2netto;\r\n265:verkaufspreis2preisfuermenge;\r\n266:verkaufspreis2menge;\r\n267:verkaufspreis2waehrung;\r\n268:verkaufspreis2gruppe;\r\n269:verkaufspreis2kundennummer;\r\n270:verkaufspreis2artikelnummerbeikunde;\r\n271:verkaufspreis2gueltigab;\r\n272:verkaufspreis2gueltigbis;\r\n273:verkaufspreis2internerkommentar;\r\n274:verkaufspreis3netto;\r\n275:verkaufspreis3preisfuermenge;\r\n276:verkaufspreis3menge;\r\n277:verkaufspreis3waehrung;\r\n278:verkaufspreis3gruppe;\r\n279:verkaufspreis3kundennummer;\r\n280:verkaufspreis3artikelnummerbeikunde;\r\n281:verkaufspreis3gueltigab;\r\n282:verkaufspreis3gueltigbis;\r\n283:verkaufspreis3internerkommentar;\r\n284:verkaufspreis4netto;\r\n285:verkaufspreis4preisfuermenge;\r\n286:verkaufspreis4menge;\r\n287:verkaufspreis4waehrung;\r\n288:verkaufspreis4gruppe;\r\n289:verkaufspreis4kundennummer;\r\n290:verkaufspreis4artikelnummerbeikunde;\r\n291:verkaufspreis4gueltigab;\r\n292:verkaufspreis4gueltigbis;\r\n293:verkaufspreis4internerkommentar;\r\n294:verkaufspreis5netto;\r\n295:verkaufspreis5preisfuermenge;\r\n296:verkaufspreis5menge;\r\n297:verkaufspreis5waehrung;\r\n298:verkaufspreis5gruppe;\r\n299:verkaufspreis5kundennummer;\r\n300:verkaufspreis5artikelnummerbeikunde;\r\n301:verkaufspreis5gueltigab;\r\n302:verkaufspreis5gueltigbis;\r\n303:verkaufspreis5internerkommentar;\r\n304:verkaufspreis6netto;\r\n305:verkaufspreis6preisfuermenge;\r\n306:verkaufspreis6menge;\r\n307:verkaufspreis6waehrung;\r\n308:verkaufspreis6gruppe;\r\n309:verkaufspreis6kundennummer;\r\n310:verkaufspreis6artikelnummerbeikunde;\r\n311:verkaufspreis6gueltigab;\r\n312:verkaufspreis6gueltigbis;\r\n313:verkaufspreis6internerkommentar;\r\n314:verkaufspreis7netto;\r\n315:verkaufspreis7preisfuermenge;\r\n316:verkaufspreis7menge;\r\n317:verkaufspreis7waehrung;\r\n318:verkaufspreis7gruppe;\r\n319:verkaufspreis7kundennummer;\r\n320:verkaufspreis7artikelnummerbeikunde;\r\n321:verkaufspreis7gueltigab;\r\n322:verkaufspreis7gueltigbis;\r\n323:verkaufspreis7internerkommentar;\r\n324:verkaufspreis8netto;\r\n325:verkaufspreis8preisfuermenge;\r\n326:verkaufspreis8menge;\r\n327:verkaufspreis8waehrung;\r\n328:verkaufspreis8gruppe;\r\n329:verkaufspreis8kundennummer;\r\n330:verkaufspreis8artikelnummerbeikunde;\r\n331:verkaufspreis8gueltigab;\r\n332:verkaufspreis8gueltigbis;\r\n333:verkaufspreis8internerkommentar;\r\n334:verkaufspreis9netto;\r\n335:verkaufspreis9preisfuermenge;\r\n336:verkaufspreis9menge;\r\n337:verkaufspreis9waehrung;\r\n338:verkaufspreis9gruppe;\r\n339:verkaufspreis9kundennummer;\r\n340:verkaufspreis9artikelnummerbeikunde;\r\n341:verkaufspreis9gueltigab;\r\n342:verkaufspreis9gueltigbis;\r\n343:verkaufspreis9internerkommentar;\r\n344:verkaufspreis10netto;\r\n345:verkaufspreis10preisfuermenge;\r\n346:verkaufspreis10menge;\r\n347:verkaufspreis10waehrung;\r\n348:verkaufspreis10gruppe;\r\n349:verkaufspreis10kundennummer;\r\n350:verkaufspreis10artikelnummerbeikunde;\r\n351:verkaufspreis10gueltigab;\r\n352:verkaufspreis10gueltigbis;\r\n353:verkaufspreis10internerkommentar;\r\n354:variante_von;\r\n355:projekt;\r\n356:geloescht;\r\n357:inaktiv;\r\n358:aktiv;\r\n359:juststueckliste;\r\n360:stuecklistevonartikel;\r\n361:stuecklistemenge;\r\n362:stuecklisteart;\r\n363:vkmeldungunterdruecken;\r\n364:shop_1;\r\n365:aktiv_1;\r\n366:fremdnummer_1;\r\n367:fremdnummerbezeichnung_1;\r\n368:pseudopreis;\r\n369:freifeld1;\r\n370:freifeld2;\r\n371:freifeld3;\r\n372:freifeld4;\r\n373:freifeld5;\r\n374:freifeld6;\r\n375:freifeld7;\r\n376:freifeld8;\r\n377:freifeld9;\r\n378:freifeld10;\r\n379:freifeld11;\r\n380:freifeld12;\r\n381:freifeld13;\r\n382:freifeld14;\r\n383:freifeld15;\r\n384:freifeld16;\r\n385:freifeld17;\r\n386:freifeld18;\r\n387:freifeld19;\r\n388:freifeld20;\r\n389:freifeld21;\r\n390:freifeld22;\r\n391:freifeld23;\r\n392:freifeld24;\r\n393:freifeld25;\r\n394:freifeld26;\r\n395:freifeld27;\r\n396:freifeld28;\r\n397:freifeld29;\r\n398:freifeld30;\r\n399:freifeld31;\r\n400:freifeld32;\r\n401:freifeld33;\r\n402:freifeld34;\r\n403:freifeld35;\r\n404:freifeld36;\r\n405:freifeld37;\r\n406:freifeld38;\r\n407:freifeld39;\r\n408:freifeld40;\r\n409:intern_gesperrt;\r\n410:intern_gesperrtgrund;\r\n411:autolagerlampe;\r\n412:pseudolager;\r\n413:lagerkorrekturwert;\r\n414:restmenge;\r\n415:provision1;\r\n416:provisiontyp1;\r\n417:provision2;\r\n418:provisiontyp2;\r\n419:eigenschaftname1;\r\n420:eigenschaftnameeindeutig1;\r\n421:eigenschaftwert1;\r\n422:eigenschaftname2;\r\n423:eigenschaftnameeindeutig2;\r\n424:eigenschaftwert2;\r\n425:eigenschaftname3;\r\n426:eigenschaftnameeindeutig3;\r\n427:eigenschaftwert3;\r\n428:eigenschaftname4;\r\n429:eigenschaftnameeindeutig4;\r\n430:eigenschaftwert4;\r\n431:eigenschaftname5;\r\n432:eigenschaftnameeindeutig5;\r\n433:eigenschaftwert5;\r\n434:eigenschaftname6;\r\n435:eigenschaftnameeindeutig6;\r\n436:eigenschaftwert6;\r\n437:eigenschaftname7;\r\n438:eigenschaftnameeindeutig7;\r\n439:eigenschaftwert7;\r\n440:eigenschaftname8;\r\n441:eigenschaftnameeindeutig8;\r\n442:eigenschaftwert8;\r\n443:eigenschaftname9;\r\n444:eigenschaftnameeindeutig9;\r\n445:eigenschaftwert9;\r\n446:eigenschaftname10;\r\n447:eigenschaftnameeindeutig10;\r\n448:eigenschaftwert10;\r\n449:matrixprodukt;\r\n450:matrixproduktvon;\r\n451:matrixproduktgruppe1;\r\n452:matrixproduktgruppe2;\r\n453:matrixproduktwert1;\r\n454:matrixproduktwert2;\r\n455:matrixgruppe1;\r\n456:matrixgruppe2;\r\n457:matrixartikelnummer;\r\n458:matrixnamefuerunterartikel;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(13, '02 A - Initialer Adressimport', 'adresse', '', '1:kundennummer;\r\n2:lieferantennummer;\r\n3:firma;\r\n4:typ;\r\n5:strasse;\r\n6:plz;\r\n7:ort;\r\n8:telefon;\r\n9:mobil;\r\n10:telefax;\r\n11:internetseite;\r\n12:ansprechpartner;\r\n13:anschreiben;\r\n14:email;\r\n15:land;\r\n16:sprache;\r\n17:ustid;\r\n18:steuernummer;\r\n19:zahlungsweise;\r\n20:zahlungszieltage;\r\n21:zahlungszieltageskonto;\r\n22:zahlungszielskonto;\r\n23:sonstiges;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(14, '02 B - Kunden (optional separater Import)', 'adresse', '', '1:kundennummer;\r\n2:firma;\r\n3:typ;\r\n4:strasse;\r\n5:plz;\r\n6:ort;\r\n7:telefon;\r\n8:mobil;\r\n9:telefax;\r\n10:internetseite;\r\n11:ansprechpartner;\r\n12:anschreiben;\r\n13:email;\r\n14:land;\r\n15:sprache;\r\n16:ustid;\r\n17:steuernummer;\r\n18:zahlungsweise;\r\n19:zahlungszieltage;\r\n20:zahlungszieltageskonto;\r\n21:zahlungszielskonto;\r\n22:sonstiges;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(15, '02 D - Lieferanten (optional separater Import)', 'adresse', '', '1:lieferantennummer;\r\n2:firma;\r\n3:typ;\r\n4:strasse;\r\n5:plz;\r\n6:ort;\r\n7:telefon;\r\n8:mobil;\r\n9:telefax;\r\n10:internetseite;\r\n11:ansprechpartner;\r\n12:anschreiben;\r\n13:email;\r\n14:land;\r\n15:sprache;\r\n16:ustid;\r\n17:steuernummer;\r\n18:zahlungsweise;\r\n19:zahlungszieltage;\r\n20:zahlungszieltageskonto;\r\n21:zahlungszielskonto;\r\n22:sonstiges;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(16, '02 E - Mitarbeiter (optional separater Import)', 'adresse', '', '1:kundennummer;\r\n2:mitarbeiternummer;\r\n3:firma;\r\n4:typ;\r\n5:strasse;\r\n6:plz;\r\n7:ort;\r\n8:telefon;\r\n9:mobil;\r\n10:telefax;\r\n11:internetseite;\r\n12:ansprechpartner;\r\n13:anschreiben;\r\n14:email;\r\n15:land;\r\n16:sprache;\r\n17:ustid;\r\n18:steuernummer;\r\n19:zahlungsweise;\r\n20:zahlungszieltage;\r\n21:zahlungszieltageskonto;\r\n22:zahlungszielskonto;\r\n23:sonstiges;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(17, '02 F - Gruppenzugehörigkeit (nach Adressimport)', 'adresse', '', '1:kundennummer;\r\n2:lieferantennummer;\r\n3:gruppe1;\r\n4:gruppe2;\r\n5:gruppe3;\r\n6:gruppe4;\r\n7:gruppe5;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(18, '02 G - Lieferadressen (für Kunden und Lieferanten)', 'adresse', '', '1:kundennummer;\r\n2:lieferantennummer;\r\n3:lieferadresse1name;\r\n4:lieferadresse1typ;\r\n5:lieferadresse1strasse;\r\n6:lieferadresse1abteilung;\r\n7:lieferadresse1unterabteilung;\r\n8:lieferadresse1land;\r\n9:lieferadresse1ort;\r\n10:lieferadresse1plz;\r\n11:lieferadresse1telefon;\r\n12:lieferadresse1gln;\r\n13:lieferadresse1email;\r\n14:lieferadresse1adresszusatz;\r\n15:lieferadresse1standardlieferadresse;\r\n16:lieferadresse1ustid;\r\n17:lieferadresse1ust_befreit;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(19, '02 H - Ansprechpartner (für Kunden und Lieferanten)', 'adresse', '', '1:kundennummer;\r\n2:lieferantennummer;\r\n2:ansprechpartner1name;\r\n3:ansprechpartner1typ;\r\n4:ansprechpartner1strasse;\r\n5:ansprechpartner1sprache;\r\n6:ansprechpartner1bereich;\r\n7:ansprechpartner1abteilung;\r\n8:ansprechpartner1unterabteilung;\r\n9:ansprechpartner1land;\r\n10:ansprechpartner1ort;\r\n11:ansprechpartner1plz;\r\n12:ansprechpartner1telefon;\r\n13:ansprechpartner1telefax;\r\n14:ansprechpartner1mobil;\r\n15:ansprechpartner1email;\r\n16:ansprechpartner1sonstiges;\r\n17:ansprechpartner1adresszusatz;\r\n18:ansprechpartner1ansprechpartner_land;\r\n19:ansprechpartner1anschreiben;\r\n20:ansprechpartner1titel;\r\n20:ansprechpartner1marketingsperre;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(20, '02 Z - Vollständiger Adressimport (Alle verfügbaren Importfelder)', 'adresse', '', '1:typ;\r\n2:marketingsperre;\r\n3:trackingsperre;\r\n4:sprache;\r\n5:name;\r\n6:vorname;\r\n7:abteilung;\r\n8:unterabteilung;\r\n9:ansprechpartner;\r\n10:land;\r\n11:strasse;\r\n12:strasse_hausnummer;\r\n13:hausnummer;\r\n14:ort;\r\n15:plz;\r\n16:plz_ort;\r\n17:telefon;\r\n18:telefax;\r\n19:mobil;\r\n20:email;\r\n21:ustid;\r\n22:ust_befreit;\r\n23:sonstiges;\r\n24:adresszusatz;\r\n25:kundenfreigabe;\r\n26:kundennummer;\r\n27:lieferantennummer;\r\n28:mitarbeiternummer;\r\n29:bank;\r\n30:inhaber;\r\n31:swift;\r\n32:iban;\r\n33:waehrung;\r\n34:paypal;\r\n35:paypalinhaber;\r\n36:paypalwaehrung;\r\n37:projekt;\r\n38:zahlungsweise;\r\n39:zahlungszieltage;\r\n40:zahlungszieltageskonto;\r\n41:zahlungszielskonto;\r\n42:versandart;\r\n43:kundennummerlieferant;\r\n44:zahlungsweiselieferant;\r\n45:zahlungszieltagelieferant;\r\n46:zahlungszieltageskontolieferant;\r\n47:zahlungszielskontolieferant;\r\n48:versandartlieferant;\r\n49:firma;\r\n50:webid;\r\n51:internetseite;\r\n52:titel;\r\n53:anschreiben;\r\n54:geburtstag;\r\n55:liefersperre;\r\n56:steuernummer;\r\n57:steuerbefreit;\r\n58:liefersperregrund;\r\n59:verrechnungskontoreisekosten;\r\n60:abweichende_rechnungsadresse;\r\n61:rechnung_vorname;\r\n62:rechnung_name;\r\n63:rechnung_titel;\r\n64:rechnung_typ;\r\n65:rechnung_strasse;\r\n66:rechnung_ort;\r\n67:rechnung_land;\r\n68:rechnung_abteilung;\r\n69:rechnung_unterabteilung;\r\n70:rechnung_adresszusatz;\r\n71:rechnung_telefon;\r\n72:rechnung_telefax;\r\n73:rechnung_anschreiben;\r\n74:rechnung_email;\r\n75:rechnung_plz;\r\n76:rechnung_ansprechpartner;\r\n77:kennung;\r\n78:vertrieb;\r\n79:innendienst;\r\n80:rabatt;\r\n81:rabatt1;\r\n82:rabatt2;\r\n83:rabatt3;\r\n84:rabatt4;\r\n85:rabatt5;\r\n86:bonus1;\r\n87:bonus1_ab;\r\n88:bonus2;\r\n89:bonus2_ab;\r\n90:bonus3;\r\n91:bonus3_ab;\r\n92:bonus4;\r\n93:bonus4_ab;\r\n94:bonus5;\r\n95:bonus5_ab;\r\n96:bonus6;\r\n97:bonus6_ab;\r\n98:bonus7;\r\n99:bonus7_ab;\r\n100:bonus8;\r\n101:bonus8_ab;\r\n102:bonus9;\r\n103:bonus9_ab;\r\n104:bonus10;\r\n105:bonus10_ab;\r\n106:verbandsnummer;\r\n107:portofreiab;\r\n108:zahlungskonditionen_festschreiben;\r\n109:rabatte_festschreiben;\r\n110:provision;\r\n111:portofrei_aktiv;\r\n112:rabattinformation;\r\n113:freifeld1;\r\n114:freifeld2;\r\n115:freifeld3;\r\n116:freifeld4;\r\n117:freifeld5;\r\n118:freifeld6;\r\n119:freifeld7;\r\n120:freifeld8;\r\n121:freifeld9;\r\n122:freifeld10;\r\n123:freifeld11;\r\n124:freifeld12;\r\n125:freifeld13;\r\n126:freifeld14;\r\n127:freifeld15;\r\n128:freifeld16;\r\n129:freifeld17;\r\n130:freifeld18;\r\n131:freifeld19;\r\n132:freifeld20;\r\n133:rechnung_periode;\r\n134:rechnung_anzahlpapier;\r\n135:rechnung_permail;\r\n136:usereditid;\r\n137:useredittimestamp;\r\n138:infoauftragserfassung;\r\n139:mandatsreferenz;\r\n140:kreditlimit;\r\n141:freifeld2;\r\n142:freifeld3;\r\n143:abweichendeemailab;\r\n144:filiale;\r\n145:mandatsreferenzdatum;\r\n146:mandatsreferenzaenderung;\r\n147:sachkonto;\r\n148:ansprechpartner1name;\r\n149:ansprechpartner1typ;\r\n150:ansprechpartner1strasse;\r\n151:ansprechpartner1sprache;\r\n152:ansprechpartner1bereich;\r\n153:ansprechpartner1abteilung;\r\n154:ansprechpartner1unterabteilung;\r\n155:ansprechpartner1land;\r\n156:ansprechpartner1ort;\r\n157:ansprechpartner1plz;\r\n158:ansprechpartner1telefon;\r\n159:ansprechpartner1telefax;\r\n160:ansprechpartner1mobil;\r\n161:ansprechpartner1email;\r\n162:ansprechpartner1sonstiges;\r\n163:ansprechpartner1adresszusatz;\r\n164:ansprechpartner1ansprechpartner_land;\r\n165:ansprechpartner1anschreiben;\r\n166:ansprechpartner1titel;\r\n167:ansprechpartner1marketingsperre;\r\n168:ansprechpartner2name;\r\n169:ansprechpartner2typ;\r\n170:ansprechpartner2strasse;\r\n171:ansprechpartner2sprache;\r\n172:ansprechpartner2bereich;\r\n173:ansprechpartner2abteilung;\r\n174:ansprechpartner2unterabteilung;\r\n175:ansprechpartner2land;\r\n176:ansprechpartner2ort;\r\n177:ansprechpartner2plz;\r\n178:ansprechpartner2telefon;\r\n179:ansprechpartner2telefax;\r\n180:ansprechpartner2mobil;\r\n181:ansprechpartner2email;\r\n182:ansprechpartner2sonstiges;\r\n183:ansprechpartner2adresszusatz;\r\n184:ansprechpartner2ansprechpartner_land;\r\n185:ansprechpartner2anschreiben;\r\n186:ansprechpartner2titel;\r\n187:ansprechpartner2marketingsperre;\r\n188:ansprechpartner3name;\r\n189:ansprechpartner3typ;\r\n190:ansprechpartner3strasse;\r\n191:ansprechpartner3sprache;\r\n192:ansprechpartner3bereich;\r\n193:ansprechpartner3abteilung;\r\n194:ansprechpartner3unterabteilung;\r\n195:ansprechpartner3land;\r\n196:ansprechpartner3ort;\r\n197:ansprechpartner3plz;\r\n198:ansprechpartner3telefon;\r\n199:ansprechpartner3telefax;\r\n200:ansprechpartner3mobil;\r\n201:ansprechpartner3email;\r\n202:ansprechpartner3sonstiges;\r\n203:ansprechpartner3adresszusatz;\r\n204:ansprechpartner3ansprechpartner_land;\r\n205:ansprechpartner3anschreiben;\r\n206:ansprechpartner3titel;\r\n207:ansprechpartner3marketingsperre;\r\n208:lieferadresse1name ;\r\n209:lieferadresse1typ;\r\n210:lieferadresse1strasse;\r\n211:lieferadresse1abteilung;\r\n212:lieferadresse1unterabteilung;\r\n213:lieferadresse1land;\r\n214:lieferadresse1ort;\r\n215:lieferadresse1plz;\r\n216:lieferadresse1telefon;\r\n217:lieferadresse1gln;\r\n218:lieferadresse1email;\r\n219:lieferadresse1adresszusatz;\r\n220:lieferadresse1standardlieferadresse;\r\n221:lieferadresse1ustid;\r\n222:lieferadresse1ust_befreit;\r\n223:lieferadresse2name ;\r\n224:lieferadresse2typ;\r\n225:lieferadresse2strasse;\r\n226:lieferadresse2abteilung;\r\n227:lieferadresse2unterabteilung;\r\n228:lieferadresse2land;\r\n229:lieferadresse2ort;\r\n230:lieferadresse2plz;\r\n231:lieferadresse2telefon;\r\n232:lieferadresse2gln;\r\n233:lieferadresse2email;\r\n234:lieferadresse2adresszusatz;\r\n235:lieferadresse2standardlieferadresse;\r\n236:lieferadresse2ustid;\r\n237:lieferadresse2ust_befreit;\r\n238:lieferadresse3name ;\r\n239:lieferadresse3typ;\r\n240:lieferadresse3strasse;\r\n241:lieferadresse3abteilung;\r\n242:lieferadresse3unterabteilung;\r\n243:lieferadresse3land;\r\n244:lieferadresse3ort;\r\n245:lieferadresse3plz;\r\n246:lieferadresse3telefon;\r\n247:lieferadresse3gln;\r\n248:lieferadresse3email;\r\n249:lieferadresse3adresszusatz;\r\n250:lieferadresse3standardlieferadresse;\r\n251:lieferadresse3ustid;\r\n252:lieferadresse3ust_befreit;\r\n253:gruppe1;\r\n254:gruppe2;\r\n255:gruppe3;\r\n256:gruppe4;\r\n257:gruppe5;\r\n258:kundennummer_buchhaltung;\r\n259:lieferantennummer_buchhaltung;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(21, '03 A - Lagerbestand (absolut)', 'artikel', 'Lagerbestände werden hier als absolute Zahlen auf den Lagerplatz gebucht, es erfolgt keine Verrechnung mit einem vorhandenen Lagerbestand.', '1:nummer; (Artikelnummer)\r\n2:lager_platz;\r\n3:lager_menge_total; (Gesamte Lagermenge auf dem Lagerplatz)\r\n\"1\":lagerartikel; (Setzt den Haken \"Lager\" im Artikel)', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(22, '03 B - Lagerbestand mit Seriennummern', 'artikel', '', '1:nummer;\r\n2:seriennummern; \r\n3:lager_platz;\r\n4:lager_menge_addieren;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(23, '03 C - Lagerbestand mit Charge / MHD', 'artikel', '', '1:nummer;\r\n2:lager_platz;\r\n3:lager_menge_addieren;\r\n4:mindesthaltbarkeitsdatum;\r\n5:lager_charge;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(24, '05 A - Notizen', 'notizen', '', '1:datum;\r\n2:uhrzeit;\r\n3:kundennummer;\r\n4:mitarbeiternummer;\r\n5:betreff;\r\n6:text;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(25, '06 A - Wiedervorlagen', 'wiedervorlagen', '', '1:datum_faellig;\r\n2:uhrzeit_faellig;\r\n3:kundennummer;\r\n4:mitarbeiternummer;\r\n5:betreff;\r\n6:text;\r\n7:abgeschlossen;\r\n8:prio;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(26, '07 A - Zeiterfassung', 'zeiterfassung', '', '1:datum_von;\r\n2:zeit_von;\r\n3:datum_bis;\r\n4:zeit_bis;\r\n5:kennung;\r\n6:taetigkeit;\r\n7:details;\r\n8:mitarbeiternummer;\r\n9:kundennummer;', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '', 0, ''),
(27, '03 A - Lagerbestand (addieren)', 'adresse', 'Addiert die importierten Lagerbestände auf dem Lagerplatz, d.h. der importierte Bestand wird dem bestehenden Bestand hinzugerechnet.', '1:nummer; (Artikelnummer)\r\n2:lager_platz;\r\n3:lager_menge_addieren; (Zusätzlich einzulagernde Lagermenge auf Lagerplatz)\r\n\"1\":lagerartikel; (Setzt den Haken \"Lager\" im Artikel)', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '', 0, '');
INSERT INTO `lager` (`id`, `bezeichnung`, `beschreibung`, `manuell`, `firma`, `geloescht`, `logdatei`, `projekt`, `adresse`) VALUES
(1, 'Hauptlager', '', 0, 1, 0, '2020-02-16 23:00:00', 0, 0);

INSERT INTO `lager_platz` (`id`, `lager`, `kurzbezeichnung`, `bemerkung`, `projekt`, `firma`, `geloescht`, `logdatei`, `autolagersperre`, `verbrauchslager`, `sperrlager`, `laenge`, `breite`, `hoehe`, `poslager`, `adresse`, `abckategorie`, `regalart`, `rownumber`, `allowproduction`) VALUES
(1, 1, 'Lagerplatz1', '', 0, 0, 0, '2020-02-16 23:00:00', 0, 0, 0, '0.00', '0.00', '0.00', 0, 0, '', '', 0, 0);


INSERT INTO `projekt` (`id`, `name`, `abkuerzung`, `verantwortlicher`, `beschreibung`, `sonstiges`, `aktiv`, `farbe`, `autoversand`, `checkok`, `portocheck`, `automailrechnung`, `checkname`, `zahlungserinnerung`, `zahlungsmailbedinungen`, `folgebestaetigung`, `stornomail`, `kundenfreigabe_loeschen`, `autobestellung`, `speziallieferschein`, `lieferscheinbriefpapier`, `speziallieferscheinbeschriftung`, `firma`, `geloescht`, `logdatei`, `steuersatz_normal`, `steuersatz_zwischen`, `steuersatz_ermaessigt`, `steuersatz_starkermaessigt`, `steuersatz_dienstleistung`, `waehrung`, `eigenesteuer`, `druckerlogistikstufe1`, `druckerlogistikstufe2`, `selbstabholermail`, `eanherstellerscan`, `reservierung`, `verkaufszahlendiagram`, `oeffentlich`, `shopzwangsprojekt`, `kunde`, `dpdkundennr`, `dhlkundennr`, `dhlformat`, `dpdformat`, `paketmarke_einzeldatei`, `dpdpfad`, `dhlpfad`, `upspfad`, `dhlintodb`, `intraship_enabled`, `intraship_drucker`, `intraship_testmode`, `intraship_user`, `intraship_signature`, `intraship_ekp`, `intraship_api_user`, `intraship_api_password`, `intraship_company_name`, `intraship_street_name`, `intraship_street_number`, `intraship_zip`, `intraship_country`, `intraship_city`, `intraship_email`, `intraship_phone`, `intraship_internet`, `intraship_contact_person`, `intraship_account_owner`, `intraship_account_number`, `intraship_bank_code`, `intraship_bank_name`, `intraship_iban`, `intraship_bic`, `intraship_WeightInKG`, `intraship_LengthInCM`, `intraship_WidthInCM`, `intraship_HeightInCM`, `intraship_PackageType`, `abrechnungsart`, `kommissionierverfahren`, `wechselaufeinstufig`, `projektuebergreifendkommisionieren`, `absendeadresse`, `absendename`, `absendesignatur`, `autodruckrechnung`, `autodruckversandbestaetigung`, `automailversandbestaetigung`, `autodrucklieferschein`, `automaillieferschein`, `autodruckstorno`, `autodruckanhang`, `automailanhang`, `autodruckerrechnung`, `autodruckerlieferschein`, `autodruckeranhang`, `autodruckrechnungmenge`, `autodrucklieferscheinmenge`, `eigenernummernkreis`, `next_angebot`, `next_auftrag`, `next_rechnung`, `next_lieferschein`, `next_arbeitsnachweis`, `next_reisekosten`, `next_bestellung`, `next_gutschrift`, `next_kundennummer`, `next_lieferantennummer`, `next_mitarbeiternummer`, `next_waren`, `next_produktion`, `next_sonstiges`, `next_anfrage`, `next_artikelnummer`, `gesamtstunden_max`, `auftragid`, `dhlzahlungmandant`, `dhlretourenschein`, `land`, `etiketten_positionen`, `etiketten_drucker`, `etiketten_art`, `seriennummernerfassen`, `versandzweigeteilt`, `nachnahmecheck`, `kasse_lieferschein_anlegen`, `kasse_lagerprozess`, `kasse_belegausgabe`, `kasse_preisgruppe`, `kasse_text_bemerkung`, `kasse_text_freitext`, `kasse_drucker`, `kasse_lieferschein`, `kasse_rechnung`, `kasse_lieferschein_doppel`, `kasse_lager`, `kasse_konto`, `kasse_laufkundschaft`, `kasse_rabatt_artikel`, `kasse_zahlung_bar`, `kasse_zahlung_ec`, `kasse_zahlung_kreditkarte`, `kasse_zahlung_ueberweisung`, `kasse_zahlung_paypal`, `kasse_extra_keinbeleg`, `kasse_extra_rechnung`, `kasse_extra_quittung`, `kasse_extra_gutschein`, `kasse_extra_rabatt_prozent`, `kasse_extra_rabatt_euro`, `kasse_adresse_erweitert`, `kasse_zahlungsauswahl_zwang`, `kasse_button_entnahme`, `kasse_button_trinkgeld`, `kasse_vorauswahl_anrede`, `kasse_erweiterte_lagerabfrage`, `filialadresse`, `versandprojektfiliale`, `differenz_auslieferung_tage`, `autostuecklistenanpassung`, `dpdendung`, `dhlendung`, `tracking_substr_start`, `tracking_remove_kundennummer`, `tracking_substr_length`, `go_drucker`, `go_apiurl_prefix`, `go_apiurl_postfix`, `go_apiurl_user`, `go_username`, `go_password`, `go_ax4nr`, `go_name1`, `go_name2`, `go_abteilung`, `go_strasse1`, `go_strasse2`, `go_hausnummer`, `go_plz`, `go_ort`, `go_land`, `go_standardgewicht`, `go_format`, `go_ausgabe`, `intraship_exportgrund`, `billsafe_merchantId`, `billsafe_merchantLicenseSandbox`, `billsafe_merchantLicenseLive`, `billsafe_applicationSignature`, `billsafe_applicationVersion`, `secupay_apikey`, `secupay_url`, `secupay_demo`, `mahnwesen`, `status`, `kasse_bondrucker`, `kasse_bondrucker_aktiv`, `kasse_bondrucker_qrcode`, `kasse_bon_zeile1`, `kasse_bon_zeile2`, `kasse_bon_zeile3`, `kasse_zahlung_bar_bezahlt`, `kasse_zahlung_ec_bezahlt`, `kasse_zahlung_kreditkarte_bezahlt`, `kasse_zahlung_ueberweisung_bezahlt`, `kasse_zahlung_paypal_bezahlt`, `kasse_quittung_rechnung`, `kasse_button_einlage`, `kasse_button_schublade`, `produktionauftragautomatischfreigeben`, `versandlagerplatzanzeigen`, `versandartikelnameausstammdaten`, `projektlager`, `tracing_substr_length`, `intraship_partnerid`, `intraship_retourenlabel`, `intraship_retourenaccount`, `absendegrussformel`, `autodruckrechnungdoppel`, `intraship_partnerid_welt`, `next_kalkulation`, `next_preisanfrage`, `next_proformarechnung`, `next_verbindlichkeit`, `freifeld1`, `freifeld2`, `freifeld3`, `freifeld4`, `freifeld5`, `freifeld6`, `freifeld7`, `freifeld8`, `freifeld9`, `freifeld10`, `mahnwesen_abweichender_versender`, `lagerplatzlieferscheinausblenden`, `etiketten_sort`, `eanherstellerscanerlauben`, `chargenerfassen`, `mhderfassen`, `autodruckrechnungstufe1`, `autodruckrechnungstufe1menge`, `autodruckrechnungstufe1mail`, `autodruckkommissionierscheinstufe1`, `autodruckkommissionierscheinstufe1menge`, `kasse_bondrucker_freifeld`, `kasse_bondrucker_anzahl`, `kasse_rksv_aktiv`, `kasse_rksv_tool`, `kasse_rksv_kartenleser`, `kasse_rksv_karteseriennummer`, `kasse_rksv_kartepin`, `kasse_rksv_aeskey`, `kasse_rksv_publiczertifikat`, `kasse_rksv_publiczertifikatkette`, `kasse_rksv_kassenid`, `kasse_gutschrift`, `rechnungerzeugen`, `pos_artikeltexteuebernehmen`, `pos_anzeigenetto`, `pos_zwischenspeichern`, `kasse_button_belegladen`, `kasse_button_storno`, `pos_kundenalleprojekte`, `pos_artikelnurausprojekt`, `allechargenmhd`, `anzeigesteuerbelege`, `pos_grosseansicht`, `preisberechnung`, `steuernummer`, `paketmarkeautodrucken`, `orderpicking_sort`, `deactivateautoshipping`, `pos_sumarticles`, `manualtracking`, `zahlungsweise`, `zahlungsweiselieferant`, `versandart`, `ups_api_user`, `ups_api_password`, `ups_api_key`, `ups_accountnumber`, `ups_company_name`, `ups_street_name`, `ups_street_number`, `ups_zip`, `ups_country`, `ups_city`, `ups_email`, `ups_phone`, `ups_internet`, `ups_contact_person`, `ups_WeightInKG`, `ups_LengthInCM`, `ups_WidthInCM`, `ups_HeightInCM`, `ups_drucker`, `ups_ausgabe`, `ups_package_code`, `ups_package_description`, `ups_service_code`, `ups_service_description`, `email_html_template`, `druckanhang`, `mailanhang`, `next_retoure`, `next_goodspostingdocument`, `pos_disable_single_entries`, `pos_disable_single_day`, `pos_disable_counting_protocol`, `pos_disable_signature`, `steuer_erloese_inland_normal`, `steuer_aufwendung_inland_normal`, `steuer_erloese_inland_ermaessigt`, `steuer_aufwendung_inland_ermaessigt`, `steuer_erloese_inland_nichtsteuerbar`, `steuer_aufwendung_inland_nichtsteuerbar`, `steuer_erloese_inland_innergemeinschaftlich`, `steuer_aufwendung_inland_innergemeinschaftlich`, `steuer_erloese_inland_eunormal`, `steuer_aufwendung_inland_eunormal`, `steuer_erloese_inland_euermaessigt`, `steuer_aufwendung_inland_euermaessigt`, `steuer_erloese_inland_export`, `steuer_aufwendung_inland_import`, `create_proformainvoice`, `print_proformainvoice`, `proformainvoice_amount`, `anzeigesteuerbelegebestellung`, `autobestbeforebatch`, `allwaysautobestbeforebatch`, `kommissionierlauflieferschein`, `intraship_exportdrucker`, `multiorderpicking`, `standardlager`, `standardlagerproduktion`, `klarna_merchantid`, `klarna_sharedsecret`, `nurlagerartikel`, `paketmarkedrucken`, `lieferscheinedrucken`, `lieferscheinedruckenmenge`, `auftragdrucken`, `auftragdruckenmenge`, `druckennachtracking`, `exportdruckrechnungstufe1`, `exportdruckrechnungstufe1menge`, `exportdruckrechnung`, `exportdruckrechnungmenge`, `kommissionierlistestufe1`, `kommissionierlistestufe1menge`, `fremdnummerscanerlauben`, `zvt100url`, `zvt100port`, `production_show_only_needed_storages`, `produktion_extra_seiten`, `kasse_button_trinkgeldeckredit`, `kasse_autologout`, `kasse_autologout_abschluss`, `next_receiptdocument`) VALUES
(1, 'Standard Projekt', 'STANDARD', '', 'Standard Projekt', '', '', '#92b73c', 0, 0, 0, 0, '', 0, '', 0, 0, 0, 0, 0, 0, 0, 1, 0, '', '19.00', '7.00', '7.00', '7.00', '7.00', 'EUR', 0, 1, 1, 0, 0, 0, 1, 0, 0, 0, '', '', '', '', 0, '', '', '0', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, '', 'keine', 'lieferschein', 0, 0, '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0.00', 0, '', 0, 'DE', 0, 2, 1, 1, 0, 1, 0, 'kein', 'kein', 0, 'Interne Bemerkung', 'Text auf Beleg', 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 0, 0, 'herr', 0, 0, 0, 2, 1, '.csv', '.csv', 8, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0.00', '', '', '', '', '', '', '', '', '', '', 0, 1, 'gestartet', 0, 0, 0, 'Xentral Store', 'Xentral GmbH\r\nHolzbachstrasse 4\r\n86152 Augsburg\r\nTel: 0821/26841041\r\nwww.wawision.de', 'Vielen Dank fuer Ihren Einkauf!\r\nUmtausch innerhalb 8 Tagen\r\ngegen Vorlage des Kassenbons\r\nUST.-IDNr: 123456789', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '01', 0, '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', 0, 0, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0, '', '', '', '', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 'deliverynotesort', 0, 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0.00', '0.00', '0.00', '0.00', 0, 'GIF', '02', 'Customer Supplied', '11', 'UPS Standard', '', 0, 0, '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', 0, 0, 0, 0, 0, '');

INSERT INTO `prozessstarter` (`id`, `bezeichnung`, `bedingung`, `art`, `startzeit`, `letzteausfuerhung`, `periode`, `typ`, `parameter`, `aktiv`, `mutex`, `mutexcounter`, `firma`, `art_filter`) VALUES
(1, 'Tickets', '', 'periodisch', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', 'cronjob', 'tickets', 0, 0, 0, 1, ''),
(2, 'E-Mails ', '', 'periodisch', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '5', 'cronjob', 'emailbackup', 0, 0, 0, 1, ''),
(3, 'Aufgaben Erinnerung', '', 'periodisch', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', 'cronjob', 'aufgabenmails', 0, 0, 0, 1, ''),
(4, 'Lagerzahlen (Shops)', '', 'periodisch', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '5', 'cronjob', 'lagerzahlen', 0, 0, 0, 1, ''),
(5, 'Zahlungsmail', '', 'uhrzeit', '2015-10-25 13:00:00', '0000-00-00 00:00:00', '', 'cronjob', 'zahlungsmail', 0, 0, 0, 1, ''),
(6, 'Überzahlte Rechnungen', '', 'uhrzeit', '2015-10-25 23:00:00', '0000-00-00 00:00:00', '', 'cronjob', 'ueberzahlterechnungen', 0, 0, 0, 1, ''),
(7, 'Umsatzstatistik', '', 'uhrzeit', '2015-10-25 23:30:00', '0000-00-00 00:00:00', '', 'cronjob', 'umsatzstatistik', 0, 0, 0, 1, ''),
(8, 'Paketmarken Tracking Download', '', 'uhrzeit', '2015-10-25 14:00:00', '0000-00-00 00:00:00', '', 'cronjob', 'wgettracking', 0, 0, 0, 1, ''),
(9, 'Lagerhistorie', '', 'uhrzeit', '2015-10-25 00:00:00', '0000-00-00 00:00:00', '', 'cronjob', 'lagerwert', 0, 0, 0, 1, ''),
(10, 'Chat-Benachrichtigung', '', 'periodisch', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '60', 'cronjob', 'chat', 0, 0, 0, 1, '');

INSERT INTO `user` (`id`, `username`, `password`, `repassword`, `description`, `settings`, `parentuser`, `activ`, `type`, `adresse`, `fehllogins`, `standarddrucker`, `firma`, `logdatei`, `startseite`, `hwtoken`, `hwkey`, `hwcounter`, `motppin`, `motpsecret`, `passwordmd5`, `externlogin`, `projekt_bevorzugen`, `email_bevorzugen`, `projekt`, `rfidtag`, `vorlage`, `kalender_passwort`, `kalender_ausblenden`, `kalender_aktiv`, `gpsstechuhr`, `standardetikett`, `standardfax`, `internebezeichnung`, `hwdatablock`, `standardversanddrucker`, `passwordsha512`, `salt`) VALUES
(1, 'admin', 'qnvEQ1sFWNdIg', 0, 'Administrator', 'firstinstall', 0, 1, 'admin', 1, 0, 0, 1, '2016-08-05 08:34:59', NULL, NULL, NULL, NULL, NULL, NULL, '21232f297a57a5a743894a0e4a801fc3', 1, 0, 1, 0, '', NULL, NULL, 0, NULL, NULL, 0, 0, NULL, NULL, 0, '', '');

INSERT INTO `uservorlage` (`id`, `bezeichnung`, `beschreibung`) VALUES
(1, 'Allgemeine Sachbearbeitung', 'Neben dem Stammdaten-Management auch Angebotserstellung und Auftragserfassung sowie allgemeine Rechte für die Teamfunktionen:\r\n- Zugriff auf Adressen\r\n- Zugriff auf Artikel\r\n- Zugriff auf Angebot\r\n- Zugriff auf Aufträge\r\n- Zugriff auf Bestellungen\r\n- Zugriff auf Kalender'),
(2, 'Lager', '- Sieht Adressen\r\n- Sieht Bestellungen\r\n- Zugriff auf Artikel\r\n- Zugriff auf Inventur\r\n- Zugriff auf Kommissionierläufe\r\n- Zugriff auf Lagerverwaltung\r\n- Zugriff auf mobile Lageroberfläche\r\n- Zugriff auf Lieferschein\r\n- Zugriff auf Statistiken\r\n- Zugriff auf Wareneingang'),
(3, 'Sachbearbeitung mit Buchhaltung', 'Sachbearbeitung erweitert um Menüpunkte der Buchhaltung:\r\n- Zugriff auf Adressen\r\n- Zugriff auf Angebote\r\n- Zugriff auf Aufträge\r\n- Zugriff auf Bestellungen\r\n- Zugriff auf Gutschriften\r\n- Zugriff auf Kassenbuch\r\n- Zugriff auf Lohnabrechnung\r\n- Zugriff auf Mahnwesen\r\n- Zugriff auf Rechnungen\r\n- Zugriff auf Verbindlichkeiten\r\n- Zugriff auf Artikel'),
(4, 'Versand', '- Sieht Auftragsliste\r\n- Sieht die Druckerliste\r\n- Zugriff auf Kommissionierläufe\r\n- Zugriff auf Lieferscheine\r\n- Zugriff auf Versandzentrum');


INSERT INTO `uservorlagerights` (`id`, `vorlage`, `module`, `action`, `permission`) VALUES
(1, 1, 'welcome', 'login', 1),
(2, 1, 'welcome', 'logout', 1),
(3, 1, 'welcome', 'start', 1),
(4, 1, 'welcome', 'startseite', 1),
(5, 1, 'welcome', 'settings', 1),
(6, 1, 'adresse', 'abrechnungzeit', 1),
(7, 1, 'adresse', 'abrechnungzeitdelete', 1),
(8, 1, 'adresse', 'accountspopup', 1),
(9, 1, 'adresse', 'abrechnungzeitabgeschlossen', 1),
(10, 1, 'adresse', 'accountseditpopup', 1),
(11, 1, 'adresse', 'addposition', 1),
(12, 1, 'adresse', 'adressestammdatenpopup', 1),
(13, 1, 'adresse', 'ansprechpartner', 1),
(14, 1, 'adresse', 'ansprechpartnereditpopup', 1),
(15, 1, 'adresse', 'ansprechpartnerlieferadressepopup', 1),
(16, 1, 'adresse', 'accounts', 1),
(17, 1, 'adresse', 'adressebestellungmarkieren', 1),
(18, 1, 'adresse', 'ansprechpartnerpopup', 1),
(19, 1, 'adresse', 'artikeleditpopup', 1),
(20, 1, 'adresse', 'autocomplete', 1),
(21, 1, 'adresse', 'artikel', 1),
(22, 1, 'adresse', 'adressesrndelete', 1),
(23, 1, 'adresse', 'belege', 1),
(24, 1, 'adresse', 'brief', 1),
(25, 1, 'adresse', 'briefbearbeiten', 1),
(26, 1, 'adresse', 'brieferstellen', 1),
(27, 1, 'adresse', 'briefdelete', 1),
(28, 1, 'adresse', 'briefeditpopup', 1),
(29, 1, 'adresse', 'briefdrucken', 1),
(30, 1, 'adresse', 'briefkorrdelete', 1),
(31, 1, 'adresse', 'briefpdf', 1),
(32, 1, 'adresse', 'briefkorrpdf', 1),
(33, 1, 'adresse', 'briefpreview', 1),
(34, 1, 'adresse', 'createdokument', 1),
(35, 1, 'adresse', 'create', 1),
(36, 1, 'adresse', 'dateien', 1),
(37, 1, 'adresse', 'delartikel', 1),
(38, 1, 'adresse', 'delkontakt', 1),
(39, 1, 'adresse', 'edit', 1),
(40, 1, 'adresse', 'downartikel', 1),
(41, 1, 'adresse', 'delete', 1),
(42, 1, 'adresse', 'getid', 1),
(43, 1, 'adresse', 'downloaddatei', 1),
(44, 1, 'adresse', 'email', 1),
(45, 1, 'adresse', 'emaileditpopup', 1),
(46, 1, 'adresse', 'kontakthistorie', 1),
(47, 1, 'adresse', 'gruppen', 1),
(48, 1, 'adresse', 'korreseditpopup', 1),
(49, 1, 'adresse', 'kontakthistorieeditpopup', 1),
(50, 1, 'adresse', 'lieferadresse', 1),
(51, 1, 'adresse', 'kundevorlage', 1),
(52, 1, 'adresse', 'lieferantartikel', 1),
(53, 1, 'adresse', 'lieferantvorlage', 1),
(54, 1, 'adresse', 'list', 1),
(55, 1, 'adresse', 'lohn', 1),
(56, 1, 'adresse', 'kundeartikel', 1),
(57, 1, 'adresse', 'lieferadressepopup', 1),
(58, 1, 'adresse', 'lieferadresseneditpopup', 1),
(59, 1, 'adresse', 'minidetail', 1),
(60, 1, 'adresse', 'minidetailadr', 1),
(61, 1, 'adresse', 'minidetailbrief', 1),
(62, 1, 'adresse', 'open', 1),
(63, 1, 'adresse', 'multilevel', 1),
(64, 1, 'adresse', 'offenebestellungen', 1),
(65, 1, 'adresse', 'newkontakt', 1),
(66, 1, 'adresse', 'pdf', 1),
(67, 1, 'adresse', 'positioneneditpopup', 1),
(68, 1, 'adresse', 'rolledatum', 1),
(69, 1, 'adresse', 'removeemailanhang', 1),
(70, 1, 'adresse', 'roledel', 1),
(71, 1, 'adresse', 'rolecreate', 1),
(72, 1, 'adresse', 'rolledelete', 1),
(73, 1, 'adresse', 'saverolle', 1),
(74, 1, 'adresse', 'rollen', 1),
(75, 1, 'adresse', 'sepamandat', 1),
(76, 1, 'adresse', 'serienbrief', 1),
(77, 1, 'adresse', 'service', 1),
(78, 1, 'adresse', 'stundensatzdelete', 1),
(79, 1, 'adresse', 'stundensatz', 1),
(80, 1, 'adresse', 'stundensatzedit', 1),
(81, 1, 'adresse', 'suche', 1),
(82, 1, 'adresse', 'suchmaske', 1),
(83, 1, 'adresse', 'ustpopup', 1),
(84, 1, 'adresse', 'upartikel', 1),
(85, 1, 'adresse', 'ustprfedit', 1),
(86, 1, 'adresse', 'ustprf', 1),
(87, 1, 'adresse', 'ustprfneu', 1),
(88, 1, 'adresse', 'verbindlichkeiten', 1),
(89, 1, 'adresse', 'zeiterfassung', 1),
(90, 1, 'adresse', 'verein', 1),
(91, 1, 'anfrage', 'abgerechnet', 1),
(92, 1, 'anfrage', 'abschicken', 1),
(93, 1, 'anfrage', 'archivierepdf', 1),
(94, 1, 'anfrage', 'angebot', 1),
(95, 1, 'anfrage', 'abschliessen', 1),
(96, 1, 'anfrage', 'beauftragtfreigabe', 1),
(97, 1, 'anfrage', 'addposition', 1),
(98, 1, 'anfrage', 'buchen', 1),
(99, 1, 'anfrage', 'createfromproject', 1),
(100, 1, 'anfrage', 'copy', 1),
(101, 1, 'anfrage', 'dateien', 1),
(102, 1, 'anfrage', 'create', 1),
(103, 1, 'anfrage', 'downanfrageposition', 1),
(104, 1, 'anfrage', 'delanfrageposition', 1),
(105, 1, 'anfrage', 'edit', 1),
(106, 1, 'anfrage', 'inlinepdf', 1),
(107, 1, 'anfrage', 'list', 1),
(108, 1, 'anfrage', 'delete', 1),
(109, 1, 'anfrage', 'livetabelle', 1),
(110, 1, 'anfrage', 'editable', 1),
(111, 1, 'anfrage', 'freigabe', 1),
(112, 1, 'anfrage', 'pdf', 1),
(113, 1, 'anfrage', 'minidetail', 1),
(114, 1, 'anfrage', 'positionen', 1),
(115, 1, 'anfrage', 'positioneneditpopup', 1),
(116, 1, 'anfrage', 'upanfrageposition', 1),
(117, 1, 'anfrage', 'schreibschutz', 1),
(118, 1, 'anfrage', 'vorabfreigabe', 1),
(119, 1, 'anfrage', 'protokoll', 1),
(120, 1, 'angebot', 'abgelehnt', 1),
(121, 1, 'angebot', 'abschicken', 1),
(122, 1, 'angebot', 'copy', 1),
(123, 1, 'angebot', 'auftrag', 1),
(124, 1, 'angebot', 'archivierepdf', 1),
(125, 1, 'angebot', 'addposition', 1),
(126, 1, 'angebot', 'create', 1),
(127, 1, 'angebot', 'delangebotposition', 1),
(128, 1, 'angebot', 'deleterabatte', 1),
(129, 1, 'angebot', 'dateien', 1),
(130, 1, 'angebot', 'delete', 1),
(131, 1, 'angebot', 'downangebotposition', 1),
(132, 1, 'angebot', 'edit', 1),
(133, 1, 'angebot', 'editable', 1),
(134, 1, 'angebot', 'einkaufspreise', 1),
(135, 1, 'angebot', 'freigabe', 1),
(136, 1, 'angebot', 'inlinepdf', 1),
(137, 1, 'angebot', 'formeln', 1),
(138, 1, 'angebot', 'kopievon', 1),
(139, 1, 'angebot', 'livetabelle', 1),
(140, 1, 'angebot', 'pdffromarchive', 1),
(141, 1, 'angebot', 'minidetail', 1),
(142, 1, 'angebot', 'list', 1),
(143, 1, 'angebot', 'positionen', 1),
(144, 1, 'angebot', 'pdf', 1),
(145, 1, 'angebot', 'schreibschutz', 1),
(146, 1, 'angebot', 'positioneneditpopup', 1),
(147, 1, 'angebot', 'steuer', 1),
(148, 1, 'angebot', 'summe', 1),
(149, 1, 'angebot', 'protokoll', 1),
(150, 1, 'angebot', 'undelete', 1),
(151, 1, 'angebot', 'upangebotposition', 1),
(152, 1, 'angebot', 'wiedervorlage', 1),
(153, 1, 'angebot', 'zertifikate', 1),
(154, 1, 'artikel', 'ajaxwerte', 1),
(155, 1, 'artikel', 'auslagern', 1),
(156, 1, 'artikel', 'ausreservieren', 1),
(157, 1, 'artikel', 'baumdetail', 1),
(158, 1, 'artikel', 'baumajax', 1),
(159, 1, 'artikel', 'baumedit', 1),
(160, 1, 'artikel', 'belege', 1),
(161, 1, 'artikel', 'baum', 1),
(162, 1, 'artikel', 'chargen', 1),
(163, 1, 'artikel', 'copy', 1),
(164, 1, 'artikel', 'create', 1),
(165, 1, 'artikel', 'dateien', 1),
(166, 1, 'artikel', 'chargedelete', 1),
(167, 1, 'artikel', 'copysave', 1),
(168, 1, 'artikel', 'delete', 1),
(169, 1, 'artikel', 'copyedit', 1),
(170, 1, 'artikel', 'delrohstoffe', 1),
(171, 1, 'artikel', 'delstueckliste', 1),
(172, 1, 'artikel', 'demo', 1),
(173, 1, 'artikel', 'edit', 1),
(174, 1, 'artikel', 'downstueckliste', 1),
(175, 1, 'artikel', 'eigenschaftencopy', 1),
(176, 1, 'artikel', 'eigenschaften', 1),
(177, 1, 'artikel', 'editstueckliste', 1),
(178, 1, 'artikel', 'editrohstoffe', 1),
(179, 1, 'artikel', 'eigenschaftendelete', 1),
(180, 1, 'artikel', 'einkauf', 1),
(181, 1, 'artikel', 'eigenschaftensuche', 1),
(182, 1, 'artikel', 'eigenschafteneditpopup', 1),
(183, 1, 'artikel', 'einkaufcopy', 1),
(184, 1, 'artikel', 'einkaufdelete', 1),
(185, 1, 'artikel', 'einkaufdisable', 1),
(186, 1, 'artikel', 'einkaufeditpopup', 1),
(187, 1, 'artikel', 'einlagern', 1),
(188, 1, 'artikel', 'etiketten', 1),
(189, 1, 'artikel', 'fremdnummerndelete', 1),
(190, 1, 'artikel', 'fremdnummern', 1),
(191, 1, 'artikel', 'instueckliste', 1),
(192, 1, 'artikel', 'fremdnummernsave', 1),
(193, 1, 'artikel', 'lager', 1),
(194, 1, 'artikel', 'lagerlampe', 1),
(195, 1, 'artikel', 'lagersync', 1),
(196, 1, 'artikel', 'matrixprodukt', 1),
(197, 1, 'artikel', 'fremdnummernedit', 1),
(198, 1, 'artikel', 'list', 1),
(199, 1, 'artikel', 'matrixproduktdyn', 1),
(200, 1, 'artikel', 'mindesthaltbarkeitsdatum', 1),
(201, 1, 'artikel', 'mhddelete', 1),
(202, 1, 'artikel', 'minidetail', 1),
(203, 1, 'artikel', 'multilevel', 1),
(204, 1, 'artikel', 'verkauf', 1),
(205, 1, 'artikel', 'stuecklisteimport', 1),
(206, 1, 'artikel', 'shopexport', 1),
(207, 1, 'artikel', 'stuecklisteetiketten', 1),
(208, 1, 'artikel', 'stuecklisteempty', 1),
(209, 1, 'artikel', 'verkaufcopy', 1),
(210, 1, 'artikel', 'upstueckliste', 1),
(211, 1, 'artikel', 'srnlageredit', 1),
(212, 1, 'artikel', 'reservierung', 1),
(213, 1, 'artikel', 'srnadresseedit', 1),
(214, 1, 'artikel', 'thumbnail', 1),
(215, 1, 'artikel', 'shopimport', 1),
(216, 1, 'artikel', 'stuecklisteupload', 1),
(217, 1, 'artikel', 'umlagern', 1),
(218, 1, 'artikel', 'zertifikate', 1),
(219, 1, 'artikel', 'srnlagerdelete', 1),
(220, 1, 'artikel', 'newlist', 1),
(221, 1, 'artikel', 'seriennummern', 1),
(222, 1, 'artikel', 'offeneauftraege', 1),
(223, 1, 'artikel', 'verkaufdelete', 1),
(224, 1, 'artikel', 'verkaufeditpopup', 1),
(225, 1, 'artikel', 'rohstoffe', 1),
(226, 1, 'artikel', 'profisuche', 1),
(227, 1, 'artikel', 'produktion', 1),
(228, 1, 'artikel', 'stueckliste', 1),
(229, 1, 'artikel', 'wareneingang', 1),
(230, 1, 'artikel', 'verkaufdisable', 1),
(231, 1, 'artikel', 'shopexportfiles', 1),
(232, 1, 'artikel', 'onlineshop', 1),
(233, 1, 'artikel', 'provision', 1),
(234, 1, 'artikel', 'stuecklisteexport', 1),
(235, 1, 'artikel', 'offenebestellungen', 1),
(236, 1, 'artikel', 'projekte', 1),
(237, 1, 'artikel', 'statistik', 1),
(238, 1, 'artikel', 'rohstoffeempty', 1),
(239, 1, 'artikel', 'schnellanlegen', 1),
(240, 1, 'artikel', 'rabatt', 1),
(241, 1, 'artikel', 'schliessen', 1),
(242, 1, 'auftrag', 'abschicken', 1),
(243, 1, 'auftrag', 'archivierepdf', 1),
(244, 1, 'auftrag', 'alsfreigegeben', 1),
(245, 1, 'auftrag', 'ausversand', 1),
(246, 1, 'auftrag', 'anfrage', 1),
(247, 1, 'auftrag', 'berechnen', 1),
(248, 1, 'auftrag', 'checkdisplay', 1),
(249, 1, 'auftrag', 'abschluss', 1),
(250, 1, 'auftrag', 'dateien', 1),
(251, 1, 'auftrag', 'delauftragposition', 1),
(252, 1, 'auftrag', 'delete', 1),
(253, 1, 'auftrag', 'copy', 1),
(254, 1, 'auftrag', 'create', 1),
(255, 1, 'auftrag', 'deleterabatte', 1),
(256, 1, 'auftrag', 'addposition', 1),
(257, 1, 'auftrag', 'downauftragposition', 1),
(258, 1, 'auftrag', 'ean', 1),
(259, 1, 'auftrag', 'edit', 1),
(260, 1, 'auftrag', 'editable', 1),
(261, 1, 'auftrag', 'einkaufspreise', 1),
(262, 1, 'auftrag', 'inlinepdf', 1),
(263, 1, 'auftrag', 'kreditlimit', 1),
(264, 1, 'auftrag', 'list', 1),
(265, 1, 'auftrag', 'livetabelle', 1),
(266, 1, 'auftrag', 'freigabe', 1),
(267, 1, 'auftrag', 'lieferschein', 1),
(268, 1, 'auftrag', 'minidetail', 1),
(269, 1, 'auftrag', 'lieferscheinrechnung', 1),
(270, 1, 'auftrag', 'nachlieferung', 1),
(271, 1, 'auftrag', 'paypal', 1),
(272, 1, 'auftrag', 'positioneneditpopup', 1),
(273, 1, 'auftrag', 'pdf', 1),
(274, 1, 'auftrag', 'positionen', 1),
(275, 1, 'auftrag', 'proforma', 1),
(276, 1, 'auftrag', 'pdffromarchive', 1),
(277, 1, 'auftrag', 'produktion', 1),
(278, 1, 'auftrag', 'reservieren', 1),
(279, 1, 'auftrag', 'schreibschutz', 1),
(280, 1, 'auftrag', 'protokoll', 1),
(281, 1, 'auftrag', 'shopexport', 1),
(282, 1, 'auftrag', 'steuer', 1),
(283, 1, 'auftrag', 'teillieferung', 1),
(284, 1, 'auftrag', 'rechnung', 1),
(285, 1, 'auftrag', 'summe', 1),
(286, 1, 'auftrag', 'search', 1),
(287, 1, 'auftrag', 'tracking', 1),
(288, 1, 'auftrag', 'undelete', 1),
(289, 1, 'auftrag', 'upauftragposition', 1),
(290, 1, 'auftrag', 'uststart', 1),
(291, 1, 'auftrag', 'verfuegbar', 1),
(292, 1, 'auftrag', 'versand', 1),
(293, 1, 'auftrag', 'updateverband', 1),
(294, 1, 'auftrag', 'zahlungsmail', 1),
(295, 1, 'auftrag', 'zahlungsmahnungswesen', 1),
(296, 1, 'auftrag', 'versandzentrum', 1),
(297, 1, 'auftrag', 'zertifikate', 1),
(298, 1, 'bestellung', 'abschicken', 1),
(299, 1, 'bestellung', 'copy', 1),
(300, 1, 'bestellung', 'addposition', 1),
(301, 1, 'bestellung', 'archivierepdf', 1),
(302, 1, 'bestellung', 'auftrag', 1),
(303, 1, 'bestellung', 'dateien', 1),
(304, 1, 'bestellung', 'abschliessen', 1),
(305, 1, 'bestellung', 'delbestellungposition', 1),
(306, 1, 'bestellung', 'create', 1),
(307, 1, 'bestellung', 'downbestellungposition', 1),
(308, 1, 'bestellung', 'delete', 1),
(309, 1, 'bestellung', 'edit', 1),
(310, 1, 'bestellung', 'editable', 1),
(311, 1, 'bestellung', 'inlinepdf', 1),
(312, 1, 'bestellung', 'einlagern', 1),
(313, 1, 'bestellung', 'list', 1),
(314, 1, 'bestellung', 'freigabe', 1),
(315, 1, 'bestellung', 'livetabelle', 1),
(316, 1, 'bestellung', 'minidetail', 1),
(317, 1, 'bestellung', 'offenepositionen', 1),
(318, 1, 'bestellung', 'pdf', 1),
(319, 1, 'bestellung', 'positioneneditpopup', 1),
(320, 1, 'bestellung', 'pdffromarchive', 1),
(321, 1, 'bestellung', 'schreibschutz', 1),
(322, 1, 'bestellung', 'positionen', 1),
(323, 1, 'bestellung', 'protokoll', 1),
(324, 1, 'bestellung', 'steuer', 1),
(325, 1, 'bestellung', 'undelete', 1),
(326, 1, 'bestellung', 'upbestellungposition', 1),
(327, 1, 'kalender', 'data', 1),
(328, 1, 'kalender', 'delete', 1),
(329, 1, 'kalender', 'gruppenedit', 1),
(330, 1, 'kalender', 'gruppendelete', 1),
(331, 1, 'kalender', 'gruppenlist', 1),
(332, 1, 'kalender', 'gruppensave', 1),
(333, 1, 'kalender', 'eventdata', 1),
(334, 1, 'kalender', 'ics', 1),
(335, 1, 'kalender', 'taskstatus', 1),
(336, 1, 'kalender', 'list', 1),
(337, 1, 'kalender', 'update', 1),
(338, 1, 'korrespondenz', 'create', 1),
(339, 1, 'korrespondenz', 'delete', 1),
(340, 1, 'korrespondenz', 'pdf', 1),
(341, 1, 'korrespondenz', 'send', 1),
(342, 1, 'korrespondenz', 'list', 1),
(343, 1, 'korrespondenz', 'edit', 1),
(344, 1, 'preisanfrage', 'abschicken', 1),
(345, 1, 'preisanfrage', 'abschliessen', 1),
(346, 1, 'preisanfrage', 'bestellung', 1),
(347, 1, 'preisanfrage', 'buchen', 1),
(348, 1, 'preisanfrage', 'addposition', 1),
(349, 1, 'preisanfrage', 'copy', 1),
(350, 1, 'preisanfrage', 'archivierepdf', 1),
(351, 1, 'preisanfrage', 'dateien', 1),
(352, 1, 'preisanfrage', 'delpreisanfrageposition', 1),
(353, 1, 'preisanfrage', 'createfromproject', 1),
(354, 1, 'preisanfrage', 'create', 1),
(355, 1, 'preisanfrage', 'downpreisanfrageposition', 1),
(356, 1, 'preisanfrage', 'edit', 1),
(357, 1, 'preisanfrage', 'delete', 1),
(358, 1, 'preisanfrage', 'inlinepdf', 1),
(359, 1, 'preisanfrage', 'freigabe', 1),
(360, 1, 'preisanfrage', 'livetabelle', 1),
(361, 1, 'preisanfrage', 'editable', 1),
(362, 1, 'preisanfrage', 'minidetail', 1),
(363, 1, 'preisanfrage', 'list', 1),
(364, 1, 'preisanfrage', 'pdf', 1),
(365, 1, 'preisanfrage', 'positionen', 1),
(366, 1, 'preisanfrage', 'positioneneditpopup', 1),
(367, 1, 'preisanfrage', 'protokoll', 1),
(368, 1, 'preisanfrage', 'schreibschutz', 1),
(369, 1, 'preisanfrage', 'uppreisanfrageposition', 1),
(370, 1, 'webmail', 'aktualisieren', 1),
(371, 1, 'webmail', 'list', 1),
(372, 1, 'webmail', 'suchen', 1),
(373, 1, 'webmail', 'antworten', 1),
(374, 1, 'webmail', 'schreiben', 1),
(375, 1, 'webmail', 'view', 1),
(376, 1, 'zolltarifnummer', 'create', 1),
(377, 1, 'zolltarifnummer', 'delete', 1),
(378, 1, 'zolltarifnummer', 'edit', 1),
(379, 1, 'zolltarifnummer', 'list', 1),
(380, 2, 'welcome', 'login', 1),
(381, 2, 'welcome', 'logout', 1),
(382, 2, 'welcome', 'start', 1),
(383, 2, 'welcome', 'startseite', 1),
(384, 2, 'welcome', 'settings', 1),
(385, 2, 'appstore', 'list', 1),
(386, 2, 'auftragassistent', 'abschicken', 1),
(387, 2, 'auftragassistent', 'delete', 1),
(388, 2, 'auftragassistent', 'edit', 1),
(389, 2, 'auftragassistent', 'freigabe', 1),
(390, 2, 'auftragassistent', 'create', 1),
(391, 2, 'auftragassistent', 'lieferadresseneu', 1),
(392, 2, 'auftragassistent', 'list', 1),
(393, 2, 'auftragassistent', 'archiv', 1),
(394, 2, 'auftragassistent', 'listfreigegebene', 1),
(395, 2, 'auftragassistent', 'versand', 1),
(396, 2, 'auftragassistent', 'protokoll', 1),
(397, 2, 'auftragassistent', 'zahlung', 1),
(398, 2, 'auftragassistent', 'artikel', 1),
(399, 2, 'auftragassistent', 'kundeuebernehmen', 1),
(400, 2, 'auftragassistent', 'lieferadresseauswahl', 1),
(401, 2, 'dateien', 'abschicken', 1),
(402, 2, 'dateien', 'delete', 1),
(403, 2, 'dateien', 'create', 1),
(404, 2, 'dateien', 'edit', 1),
(405, 2, 'dateien', 'download', 1),
(406, 2, 'dateien', 'lieferadresseauswahl', 1),
(407, 2, 'dateien', 'archiv', 1),
(408, 2, 'dateien', 'lieferadresseneu', 1),
(409, 2, 'dateien', 'listfreigegebene', 1),
(410, 2, 'dateien', 'list', 1),
(411, 2, 'dateien', 'minidetail', 1),
(412, 2, 'dateien', 'kundeuebernehmen', 1),
(413, 2, 'dateien', 'freigabe', 1),
(414, 2, 'dateien', 'zahlung', 1),
(415, 2, 'dateien', 'protokoll', 1),
(416, 2, 'dateien', 'artikel', 1),
(417, 2, 'dateien', 'versand', 1),
(418, 2, 'dateien', 'send', 1),
(419, 2, 'inventur', 'abschicken', 1),
(420, 2, 'inventur', 'addposition', 1),
(421, 2, 'inventur', 'create', 1),
(422, 2, 'inventur', 'copy', 1),
(423, 2, 'inventur', 'buchen', 1),
(424, 2, 'inventur', 'createfromproject', 1),
(425, 2, 'inventur', 'automatisch', 1),
(426, 2, 'inventur', 'downinventurposition', 1),
(427, 2, 'inventur', 'delete', 1),
(428, 2, 'inventur', 'editable', 1),
(429, 2, 'inventur', 'delinventurposition', 1),
(430, 2, 'inventur', 'freigabe', 1),
(431, 2, 'inventur', 'livetabelle', 1),
(432, 2, 'inventur', 'list', 1),
(433, 2, 'inventur', 'pdf', 1),
(434, 2, 'inventur', 'bereinigen', 1),
(435, 2, 'inventur', 'csv', 1),
(436, 2, 'inventur', 'positioneneditpopup', 1),
(437, 2, 'inventur', 'edit', 1),
(438, 2, 'inventur', 'minidetail', 1),
(439, 2, 'inventur', 'upinventurposition', 1),
(440, 2, 'inventur', 'protokoll', 1),
(441, 2, 'inventur', 'positionen', 1),
(442, 2, 'inventur', 'schreibschutz', 1),
(443, 2, 'kommissionierlauf', 'edit', 1),
(444, 2, 'kommissionierlauf', 'list', 1),
(445, 2, 'kommissionierlauf', 'minidetail', 1),
(446, 2, 'kommissionierlauf', 'versandzentrum', 1),
(447, 2, 'kommissionierlauf', 'label', 1),
(448, 2, 'lager', 'artikelentfernen', 1),
(449, 2, 'lager', 'artikelfuerlieferungen', 1),
(450, 2, 'lager', 'bewegung', 1),
(451, 2, 'lager', 'bewegungpopup', 1),
(452, 2, 'lager', 'auslagernproduktion', 1),
(453, 2, 'lager', 'bestand', 1),
(454, 2, 'lager', 'buchenauslagern', 1),
(455, 2, 'lager', 'buchen', 1),
(456, 2, 'lager', 'bucheneinlagern', 1),
(457, 2, 'lager', 'artikelentfernenreserviert', 1),
(458, 2, 'lager', 'ausgehend', 1),
(459, 2, 'lager', 'delete', 1),
(460, 2, 'lager', 'differenzen', 1),
(461, 2, 'lager', 'deleteplatz', 1),
(462, 2, 'lager', 'buchenzwischenlager', 1),
(463, 2, 'lager', 'differenzenlagerplatzedit', 1),
(464, 2, 'lager', 'differenzenlagerplatz', 1),
(465, 2, 'lager', 'create', 1),
(466, 2, 'lager', 'buchenzwischenlagerdelete', 1),
(467, 2, 'lager', 'differenzenlagerplatzsave', 1),
(468, 2, 'lager', 'inhalt', 1),
(469, 2, 'lager', 'letztebewegungen', 1),
(470, 2, 'lager', 'etikettenlist', 1),
(471, 2, 'lager', 'list', 1),
(472, 2, 'lager', 'nachschublager', 1),
(473, 2, 'lager', 'edit', 1),
(474, 2, 'lager', 'platz', 1),
(475, 2, 'lager', 'regaletiketten', 1),
(476, 2, 'lager', 'etiketten', 1),
(477, 2, 'lager', 'schnellauslagern', 1),
(478, 2, 'lager', 'schnelleinlagern', 1),
(479, 2, 'lager', 'lagerpdfsammelentnahme', 1),
(480, 2, 'lager', 'schnellumlagern', 1),
(481, 2, 'lager', 'platzeditpopup', 1),
(482, 2, 'lagerinventur', 'bestand', 1),
(483, 2, 'lager', 'zwischenlager', 1),
(484, 2, 'lagerinventur', 'changeinv', 1),
(485, 2, 'lager', 'produktionslager', 1),
(486, 2, 'lagerinventur', 'inventur', 1),
(487, 2, 'lagermobil', 'artikelentfernen', 1),
(488, 2, 'lager', 'wert', 1),
(489, 2, 'lagermobil', 'artikelfuerlieferungen', 1),
(490, 2, 'lager', 'reservierungen', 1),
(491, 2, 'lagermobil', 'auslagern', 1),
(492, 2, 'lagermobil', 'artikelentfernenreserviert', 1),
(493, 2, 'lagermobil', 'auslagernproduktion', 1),
(494, 2, 'lagerinventur', 'inventurladen', 1),
(495, 2, 'lagermobil', 'ausgehend', 1),
(496, 2, 'lagermobil', 'bucheneinlagern', 1),
(497, 2, 'lagermobil', 'bewegung', 1),
(498, 2, 'lagermobil', 'buchen', 1),
(499, 2, 'lagermobil', 'bewegungpopup', 1),
(500, 2, 'lagermobil', 'buchenzwischenlagerdelete', 1),
(501, 2, 'lagermobil', 'create', 1),
(502, 2, 'lagermobil', 'buchenzwischenlager', 1),
(503, 2, 'lagermobil', 'differenzen', 1),
(504, 2, 'lagermobil', 'differenzenlagerplatzedit', 1),
(505, 2, 'lagermobil', 'differenzenlagerplatzsave', 1),
(506, 2, 'lagermobil', 'differenzenlagerplatz', 1),
(507, 2, 'lagermobil', 'buchenauslagern', 1),
(508, 2, 'lagermobil', 'einlagern', 1),
(509, 2, 'lagermobil', 'deleteplatz', 1),
(510, 2, 'lagermobil', 'inhalt', 1),
(511, 2, 'lagermobil', 'delete', 1),
(512, 2, 'lagermobil', 'letztebewegungen', 1),
(513, 2, 'lagermobil', 'list', 1),
(514, 2, 'lagermobil', 'lagerpdfsammelentnahme', 1),
(515, 2, 'lagermobil', 'platz', 1),
(516, 2, 'lagermobil', 'edit', 1),
(517, 2, 'lagermobil', 'etiketten', 1),
(518, 2, 'lagermobil', 'regaletiketten', 1),
(519, 2, 'lagermobil', 'platzeditpopup', 1),
(520, 2, 'lagermobil', 'reservierungen', 1),
(521, 2, 'lagermobil', 'schnell_auslagern', 1),
(522, 2, 'lagermobil', 'schnell_einlagern', 1),
(523, 2, 'lagermobil', 'produktionslager', 1),
(524, 2, 'lagermobil', 'schnelleinlagern', 1),
(525, 2, 'lagermobil', 'zwischenlager', 1),
(526, 2, 'laufzettel', 'download', 1),
(527, 2, 'lieferschein', 'abschicken', 1),
(528, 2, 'lagermobil', 'wert', 1),
(529, 2, 'lagermobil', 'nachschublager', 1),
(530, 2, 'lagermobil', 'lpumlagern', 1),
(531, 2, 'lieferschein', 'auslagern', 1),
(532, 2, 'lieferschein', 'archivierepdf', 1),
(533, 2, 'lieferschein', 'addposition', 1),
(534, 2, 'lieferschein', 'create', 1),
(535, 2, 'lieferschein', 'dellieferscheinposition', 1),
(536, 2, 'lieferschein', 'downlieferscheinposition', 1),
(537, 2, 'lieferschein', 'delete', 1),
(538, 2, 'lieferschein', 'editable', 1),
(539, 2, 'lieferschein', 'abschliessen', 1),
(540, 2, 'lieferschein', 'edit', 1),
(541, 2, 'lagermobil', 'umlagern', 1),
(542, 2, 'lieferschein', 'copy', 1),
(543, 2, 'lieferschein', 'livetabelle', 1),
(544, 2, 'lieferschein', 'list', 1),
(545, 2, 'lieferschein', 'eingabeseriennummern', 1),
(546, 2, 'lieferschein', 'pdf', 1),
(547, 2, 'lieferschein', 'pdffromarchive', 1),
(548, 2, 'lieferschein', 'inlinepdf', 1),
(549, 2, 'lieferschein', 'positionen', 1),
(550, 2, 'lieferschein', 'freigabe', 1),
(551, 2, 'lieferschein', 'positioneneditpopup', 1),
(552, 2, 'lieferschein', 'protokoll', 1),
(553, 2, 'lieferschein', 'schreibschutz', 1),
(554, 2, 'lieferschein', 'uplieferscheinposition', 1),
(555, 2, 'lieferschein', 'rechnung', 1),
(556, 2, 'lieferschein', 'positionenetiketten', 1),
(557, 2, 'lieferschein', 'paketmarke', 1),
(558, 2, 'lieferschein', 'minidetail', 1),
(559, 2, 'paketmarke', 'create', 1),
(560, 2, 'paketmarke', 'tracking', 1),
(561, 2, 'protokoll', 'list', 1),
(562, 2, 'prozess_auftrag_einlagern', 'list', 1),
(563, 2, 'prozess_monitor', 'minidetail', 1),
(564, 2, 'prozess_monitor', 'list', 1),
(565, 2, 'prozessstarter', 'list', 1),
(566, 2, 'protokoll', 'minidetail', 1),
(567, 2, 'wareneingang', 'create', 1),
(568, 2, 'wareneingang', 'distribution', 1),
(569, 2, 'wareneingang', 'help', 1),
(570, 2, 'wareneingang', 'distrietiketten', 1),
(571, 2, 'wareneingang', 'distriabschluss', 1),
(572, 2, 'wareneingang', 'manuellerfassen', 1),
(573, 2, 'wareneingang', 'list', 1),
(574, 2, 'wareneingang', 'paketabsender', 1),
(575, 2, 'wareneingang', 'distriinhalt', 1),
(576, 2, 'wareneingang', 'paketetikett', 1),
(577, 2, 'wareneingang', 'paketzustand', 1),
(578, 2, 'wareneingang', 'removevorgang', 1),
(579, 2, 'wareneingang', 'stornieren', 1),
(580, 2, 'wareneingang', 'paketannahme', 1),
(581, 2, 'wareneingang', 'vorgang', 1),
(582, 2, 'wareneingang', 'minidetail', 1),
(583, 2, 'wareneingang', 'main', 1),
(584, 2, 'wareneingang', 'paketabschliessen', 1),
(585, 2, 'webmail', 'list', 1),
(586, 2, 'artikel', 'shopexportfiles', 1),
(587, 2, 'artikel', 'shopexport', 1),
(588, 2, 'artikel', 'list', 1),
(589, 2, 'adresse', 'list', 1),
(590, 2, 'adresse', 'kontakthistorie', 1),
(591, 2, 'prozess_monitor', 'create', 1),
(592, 2, 'prozess_monitor', 'edit', 1),
(593, 2, 'prozess_monitor', 'delete', 1),
(594, 2, 'prozessstarter', 'create', 1),
(595, 2, 'prozessstarter', 'delete', 1),
(596, 2, 'prozessstarter', 'edit', 1),
(597, 2, 'artikel', 'edit', 1),
(598, 2, 'artikel', 'lager', 1),
(599, 2, 'artikel', 'lagerlampe', 1),
(600, 2, 'artikel', 'ajaxwerte', 1),
(601, 2, 'artikel', 'baumdetail', 1),
(602, 2, 'artikel', 'baumajax', 1),
(603, 2, 'artikel', 'baum', 1),
(604, 2, 'artikel', 'baumedit', 1),
(605, 2, 'artikel', 'ausreservieren', 1),
(606, 2, 'artikel', 'chargedelete', 1),
(607, 2, 'artikel', 'copy', 1),
(608, 2, 'artikel', 'copyedit', 1),
(609, 2, 'artikel', 'copysave', 1),
(610, 2, 'artikel', 'delete', 1),
(611, 2, 'artikel', 'dateien', 1),
(612, 2, 'artikel', 'chargen', 1),
(613, 2, 'artikel', 'auslagern', 1),
(614, 2, 'artikel', 'delstueckliste', 1),
(615, 2, 'artikel', 'demo', 1),
(616, 2, 'artikel', 'editstueckliste', 1),
(617, 2, 'artikel', 'editrohstoffe', 1),
(618, 2, 'artikel', 'delrohstoffe', 1),
(619, 2, 'artikel', 'downstueckliste', 1),
(620, 2, 'artikel', 'eigenschaftendelete', 1),
(621, 2, 'artikel', 'einkauf', 1),
(622, 2, 'artikel', 'eigenschaftensuche', 1),
(623, 2, 'artikel', 'eigenschaften', 1),
(624, 2, 'artikel', 'einkaufcopy', 1),
(625, 2, 'artikel', 'eigenschafteneditpopup', 1),
(626, 2, 'artikel', 'einkaufdisable', 1),
(627, 2, 'artikel', 'etiketten', 1),
(628, 2, 'artikel', 'eigenschaftencopy', 1),
(629, 2, 'artikel', 'einkaufdelete', 1),
(630, 2, 'artikel', 'fremdnummerndelete', 1),
(631, 2, 'artikel', 'einkaufeditpopup', 1),
(632, 2, 'artikel', 'fremdnummernsave', 1),
(633, 2, 'artikel', 'fremdnummernedit', 1),
(634, 2, 'artikel', 'einlagern', 1),
(635, 2, 'artikel', 'instueckliste', 1),
(636, 2, 'artikel', 'lagersync', 1),
(637, 2, 'artikel', 'fremdnummern', 1),
(638, 2, 'artikel', 'matrixproduktdyn', 1),
(639, 2, 'artikel', 'mindesthaltbarkeitsdatum', 1),
(640, 2, 'artikel', 'mhddelete', 1),
(641, 2, 'artikel', 'minidetail', 1),
(642, 2, 'artikel', 'offenebestellungen', 1),
(643, 2, 'artikel', 'multilevel', 1),
(644, 2, 'artikel', 'newlist', 1),
(645, 2, 'artikel', 'onlineshop', 1),
(646, 2, 'artikel', 'projekte', 1),
(647, 2, 'artikel', 'produktion', 1),
(648, 2, 'artikel', 'provision', 1),
(649, 2, 'artikel', 'offeneauftraege', 1),
(650, 2, 'artikel', 'reservierung', 1),
(651, 2, 'artikel', 'rohstoffe', 1),
(652, 2, 'artikel', 'rabatt', 1),
(653, 2, 'artikel', 'rohstoffeempty', 1),
(654, 2, 'artikel', 'seriennummern', 1),
(655, 2, 'artikel', 'profisuche', 1),
(656, 2, 'artikel', 'schliessen', 1),
(657, 2, 'artikel', 'shopimport', 1),
(658, 2, 'artikel', 'matrixprodukt', 1),
(659, 2, 'artikel', 'srnadresseedit', 1),
(660, 2, 'artikel', 'srnlageredit', 1),
(661, 2, 'artikel', 'stuecklisteempty', 1),
(662, 2, 'artikel', 'stueckliste', 1),
(663, 2, 'artikel', 'srnlagerdelete', 1),
(664, 2, 'artikel', 'statistik', 1),
(665, 2, 'artikel', 'stuecklisteexport', 1),
(666, 2, 'artikel', 'stuecklisteupload', 1),
(667, 2, 'artikel', 'schnellanlegen', 1),
(668, 2, 'artikel', 'upstueckliste', 1),
(669, 2, 'artikel', 'verkauf', 1),
(670, 2, 'artikel', 'umlagern', 1),
(671, 2, 'artikel', 'verkaufcopy', 1),
(672, 2, 'artikel', 'verkaufdisable', 1),
(673, 2, 'artikel', 'stuecklisteetiketten', 1),
(674, 2, 'artikel', 'thumbnail', 1),
(675, 2, 'artikel', 'verkaufeditpopup', 1),
(676, 2, 'artikel', 'verkaufdelete', 1),
(677, 2, 'artikel', 'stuecklisteimport', 1),
(678, 2, 'artikel', 'wareneingang', 1),
(679, 2, 'artikel', 'zertifikate', 1),
(680, 2, 'statistiken', 'dashboard', 1),
(681, 2, 'statistiken', 'artikel', 1),
(682, 2, 'statistiken', 'list', 1),
(683, 2, 'statistiken', 'topartikel', 1),
(684, 2, 'statistiken', 'einstellungen', 1),
(685, 2, 'adresse', 'open', 1),
(686, 2, 'bestellung', 'pdf', 1),
(687, 2, 'bestellung', 'list', 1),
(688, 2, 'bestellung', 'minidetail', 1),
(689, 2, 'bestellung', 'einlagern', 1),
(690, 2, 'bestellung_einlagern', 'list', 1),
(691, 2, 'auftrag', 'versand', 1),
(692, 2, 'auftrag', 'summe', 1),
(693, 2, 'auftrag', 'tracking', 1),
(694, 2, 'auftrag', 'upauftragposition', 1),
(695, 2, 'auftrag', 'undelete', 1),
(696, 2, 'auftrag', 'verfuegbar', 1),
(697, 2, 'auftrag', 'versandzentrum', 1),
(698, 2, 'auftrag', 'uststart', 1),
(699, 2, 'auftrag', 'teillieferung', 1),
(700, 2, 'auftrag', 'zertifikate', 1),
(701, 2, 'auftrag', 'updateverband', 1),
(702, 2, 'auftrag', 'zahlungsmail', 1),
(703, 2, 'auftrag', 'nachlieferung', 1),
(704, 2, 'auftrag', 'zahlungsmahnungswesen', 1),
(705, 3, 'welcome', 'login', 1),
(706, 3, 'welcome', 'logout', 1),
(707, 3, 'welcome', 'start', 1),
(708, 3, 'welcome', 'startseite', 1),
(709, 3, 'welcome', 'settings', 1),
(710, 3, 'adresse', 'abrechnungzeit', 1),
(711, 3, 'adresse', 'abrechnungzeitdelete', 1),
(712, 3, 'adresse', 'accountspopup', 1),
(713, 3, 'adresse', 'abrechnungzeitabgeschlossen', 1),
(714, 3, 'adresse', 'accountseditpopup', 1),
(715, 3, 'adresse', 'addposition', 1),
(716, 3, 'adresse', 'adressestammdatenpopup', 1),
(717, 3, 'adresse', 'ansprechpartner', 1),
(718, 3, 'adresse', 'ansprechpartnereditpopup', 1),
(719, 3, 'adresse', 'ansprechpartnerlieferadressepopup', 1),
(720, 3, 'adresse', 'accounts', 1),
(721, 3, 'adresse', 'adressebestellungmarkieren', 1),
(722, 3, 'adresse', 'ansprechpartnerpopup', 1),
(723, 3, 'adresse', 'artikeleditpopup', 1),
(724, 3, 'adresse', 'autocomplete', 1),
(725, 3, 'adresse', 'artikel', 1),
(726, 3, 'adresse', 'adressesrndelete', 1),
(727, 3, 'adresse', 'belege', 1),
(728, 3, 'adresse', 'brief', 1),
(729, 3, 'adresse', 'briefbearbeiten', 1),
(730, 3, 'adresse', 'brieferstellen', 1),
(731, 3, 'adresse', 'briefdelete', 1),
(732, 3, 'adresse', 'briefeditpopup', 1),
(733, 3, 'adresse', 'briefdrucken', 1),
(734, 3, 'adresse', 'briefkorrdelete', 1),
(735, 3, 'adresse', 'briefpdf', 1),
(736, 3, 'adresse', 'briefkorrpdf', 1),
(737, 3, 'adresse', 'briefpreview', 1),
(738, 3, 'adresse', 'createdokument', 1),
(739, 3, 'adresse', 'create', 1),
(740, 3, 'adresse', 'dateien', 1),
(741, 3, 'adresse', 'delartikel', 1),
(742, 3, 'adresse', 'delkontakt', 1),
(743, 3, 'adresse', 'edit', 1),
(744, 3, 'adresse', 'downartikel', 1),
(745, 3, 'adresse', 'delete', 1),
(746, 3, 'adresse', 'getid', 1),
(747, 3, 'adresse', 'downloaddatei', 1),
(748, 3, 'adresse', 'email', 1),
(749, 3, 'adresse', 'emaileditpopup', 1),
(750, 3, 'adresse', 'kontakthistorie', 1),
(751, 3, 'adresse', 'gruppen', 1),
(752, 3, 'adresse', 'korreseditpopup', 1),
(753, 3, 'adresse', 'kontakthistorieeditpopup', 1),
(754, 3, 'adresse', 'lieferadresse', 1),
(755, 3, 'adresse', 'kundevorlage', 1),
(756, 3, 'adresse', 'lieferantartikel', 1),
(757, 3, 'adresse', 'lieferantvorlage', 1),
(758, 3, 'adresse', 'list', 1),
(759, 3, 'adresse', 'lohn', 1),
(760, 3, 'adresse', 'kundeartikel', 1),
(761, 3, 'adresse', 'lieferadressepopup', 1),
(762, 3, 'adresse', 'lieferadresseneditpopup', 1),
(763, 3, 'adresse', 'minidetail', 1),
(764, 3, 'adresse', 'minidetailadr', 1),
(765, 3, 'adresse', 'minidetailbrief', 1),
(766, 3, 'adresse', 'open', 1),
(767, 3, 'adresse', 'multilevel', 1),
(768, 3, 'adresse', 'offenebestellungen', 1),
(769, 3, 'adresse', 'newkontakt', 1),
(770, 3, 'adresse', 'pdf', 1),
(771, 3, 'adresse', 'positioneneditpopup', 1),
(772, 3, 'adresse', 'rolledatum', 1),
(773, 3, 'adresse', 'removeemailanhang', 1),
(774, 3, 'adresse', 'roledel', 1),
(775, 3, 'adresse', 'rolecreate', 1),
(776, 3, 'adresse', 'rolledelete', 1),
(777, 3, 'adresse', 'saverolle', 1),
(778, 3, 'adresse', 'rollen', 1),
(779, 3, 'adresse', 'sepamandat', 1),
(780, 3, 'adresse', 'serienbrief', 1),
(781, 3, 'adresse', 'service', 1),
(782, 3, 'adresse', 'stundensatzdelete', 1),
(783, 3, 'adresse', 'stundensatz', 1),
(784, 3, 'adresse', 'stundensatzedit', 1),
(785, 3, 'adresse', 'suche', 1),
(786, 3, 'adresse', 'suchmaske', 1),
(787, 3, 'adresse', 'ustpopup', 1),
(788, 3, 'adresse', 'upartikel', 1),
(789, 3, 'adresse', 'ustprfedit', 1),
(790, 3, 'adresse', 'ustprf', 1),
(791, 3, 'adresse', 'ustprfneu', 1),
(792, 3, 'adresse', 'verbindlichkeiten', 1),
(793, 3, 'adresse', 'zeiterfassung', 1),
(794, 3, 'adresse', 'verein', 1),
(795, 3, 'anfrage', 'abgerechnet', 1),
(796, 3, 'anfrage', 'abschicken', 1),
(797, 3, 'anfrage', 'archivierepdf', 1),
(798, 3, 'anfrage', 'angebot', 1),
(799, 3, 'anfrage', 'abschliessen', 1),
(800, 3, 'anfrage', 'beauftragtfreigabe', 1),
(801, 3, 'anfrage', 'addposition', 1),
(802, 3, 'anfrage', 'buchen', 1),
(803, 3, 'anfrage', 'createfromproject', 1),
(804, 3, 'anfrage', 'copy', 1),
(805, 3, 'anfrage', 'dateien', 1),
(806, 3, 'anfrage', 'create', 1),
(807, 3, 'anfrage', 'downanfrageposition', 1),
(808, 3, 'anfrage', 'delanfrageposition', 1),
(809, 3, 'anfrage', 'edit', 1),
(810, 3, 'anfrage', 'inlinepdf', 1),
(811, 3, 'anfrage', 'list', 1),
(812, 3, 'anfrage', 'delete', 1),
(813, 3, 'anfrage', 'livetabelle', 1),
(814, 3, 'anfrage', 'editable', 1),
(815, 3, 'anfrage', 'freigabe', 1),
(816, 3, 'anfrage', 'pdf', 1),
(817, 3, 'anfrage', 'minidetail', 1),
(818, 3, 'anfrage', 'positionen', 1),
(819, 3, 'anfrage', 'positioneneditpopup', 1),
(820, 3, 'anfrage', 'upanfrageposition', 1),
(821, 3, 'anfrage', 'schreibschutz', 1),
(822, 3, 'anfrage', 'vorabfreigabe', 1),
(823, 3, 'anfrage', 'protokoll', 1),
(824, 3, 'angebot', 'abgelehnt', 1),
(825, 3, 'angebot', 'abschicken', 1),
(826, 3, 'angebot', 'copy', 1),
(827, 3, 'angebot', 'auftrag', 1),
(828, 3, 'angebot', 'archivierepdf', 1),
(829, 3, 'angebot', 'addposition', 1),
(830, 3, 'angebot', 'create', 1),
(831, 3, 'angebot', 'delangebotposition', 1),
(832, 3, 'angebot', 'deleterabatte', 1),
(833, 3, 'angebot', 'dateien', 1),
(834, 3, 'angebot', 'delete', 1),
(835, 3, 'angebot', 'downangebotposition', 1),
(836, 3, 'angebot', 'edit', 1),
(837, 3, 'angebot', 'editable', 1),
(838, 3, 'angebot', 'einkaufspreise', 1),
(839, 3, 'angebot', 'freigabe', 1),
(840, 3, 'angebot', 'inlinepdf', 1),
(841, 3, 'angebot', 'formeln', 1),
(842, 3, 'angebot', 'kopievon', 1),
(843, 3, 'angebot', 'livetabelle', 1),
(844, 3, 'angebot', 'pdffromarchive', 1),
(845, 3, 'angebot', 'minidetail', 1),
(846, 3, 'angebot', 'list', 1),
(847, 3, 'angebot', 'positionen', 1),
(848, 3, 'angebot', 'pdf', 1),
(849, 3, 'angebot', 'schreibschutz', 1),
(850, 3, 'angebot', 'positioneneditpopup', 1),
(851, 3, 'angebot', 'steuer', 1),
(852, 3, 'angebot', 'summe', 1),
(853, 3, 'angebot', 'protokoll', 1),
(854, 3, 'angebot', 'undelete', 1),
(855, 3, 'angebot', 'upangebotposition', 1),
(856, 3, 'angebot', 'wiedervorlage', 1),
(857, 3, 'angebot', 'zertifikate', 1),
(858, 3, 'artikel', 'ajaxwerte', 1),
(859, 3, 'artikel', 'auslagern', 1),
(860, 3, 'artikel', 'ausreservieren', 1),
(861, 3, 'artikel', 'baumdetail', 1),
(862, 3, 'artikel', 'baumajax', 1),
(863, 3, 'artikel', 'baumedit', 1),
(864, 3, 'artikel', 'belege', 1),
(865, 3, 'artikel', 'baum', 1),
(866, 3, 'artikel', 'chargen', 1),
(867, 3, 'artikel', 'copy', 1),
(868, 3, 'artikel', 'create', 1),
(869, 3, 'artikel', 'dateien', 1),
(870, 3, 'artikel', 'chargedelete', 1),
(871, 3, 'artikel', 'copysave', 1),
(872, 3, 'artikel', 'delete', 1),
(873, 3, 'artikel', 'copyedit', 1),
(874, 3, 'artikel', 'delrohstoffe', 1),
(875, 3, 'artikel', 'delstueckliste', 1),
(876, 3, 'artikel', 'demo', 1),
(877, 3, 'artikel', 'edit', 1),
(878, 3, 'artikel', 'downstueckliste', 1),
(879, 3, 'artikel', 'eigenschaftencopy', 1),
(880, 3, 'artikel', 'eigenschaften', 1),
(881, 3, 'artikel', 'editstueckliste', 1),
(882, 3, 'artikel', 'editrohstoffe', 1),
(883, 3, 'artikel', 'eigenschaftendelete', 1),
(884, 3, 'artikel', 'einkauf', 1),
(885, 3, 'artikel', 'eigenschaftensuche', 1),
(886, 3, 'artikel', 'eigenschafteneditpopup', 1),
(887, 3, 'artikel', 'einkaufcopy', 1),
(888, 3, 'artikel', 'einkaufdelete', 1),
(889, 3, 'artikel', 'einkaufdisable', 1),
(890, 3, 'artikel', 'einkaufeditpopup', 1),
(891, 3, 'artikel', 'einlagern', 1),
(892, 3, 'artikel', 'etiketten', 1),
(893, 3, 'artikel', 'fremdnummerndelete', 1),
(894, 3, 'artikel', 'fremdnummern', 1),
(895, 3, 'artikel', 'instueckliste', 1),
(896, 3, 'artikel', 'fremdnummernsave', 1),
(897, 3, 'artikel', 'lager', 1),
(898, 3, 'artikel', 'lagerlampe', 1),
(899, 3, 'artikel', 'lagersync', 1),
(900, 3, 'artikel', 'matrixprodukt', 1),
(901, 3, 'artikel', 'fremdnummernedit', 1),
(902, 3, 'artikel', 'list', 1),
(903, 3, 'artikel', 'matrixproduktdyn', 1),
(904, 3, 'artikel', 'mindesthaltbarkeitsdatum', 1),
(905, 3, 'artikel', 'mhddelete', 1),
(906, 3, 'artikel', 'minidetail', 1),
(907, 3, 'artikel', 'multilevel', 1),
(908, 3, 'artikel', 'verkauf', 1),
(909, 3, 'artikel', 'stuecklisteimport', 1),
(910, 3, 'artikel', 'shopexport', 1),
(911, 3, 'artikel', 'stuecklisteetiketten', 1),
(912, 3, 'artikel', 'stuecklisteempty', 1),
(913, 3, 'artikel', 'verkaufcopy', 1),
(914, 3, 'artikel', 'upstueckliste', 1),
(915, 3, 'artikel', 'srnlageredit', 1),
(916, 3, 'artikel', 'reservierung', 1),
(917, 3, 'artikel', 'srnadresseedit', 1),
(918, 3, 'artikel', 'thumbnail', 1),
(919, 3, 'artikel', 'shopimport', 1),
(920, 3, 'artikel', 'stuecklisteupload', 1),
(921, 3, 'artikel', 'umlagern', 1),
(922, 3, 'artikel', 'zertifikate', 1),
(923, 3, 'artikel', 'srnlagerdelete', 1),
(924, 3, 'artikel', 'newlist', 1),
(925, 3, 'artikel', 'seriennummern', 1),
(926, 3, 'artikel', 'offeneauftraege', 1),
(927, 3, 'artikel', 'verkaufdelete', 1),
(928, 3, 'artikel', 'verkaufeditpopup', 1),
(929, 3, 'artikel', 'rohstoffe', 1),
(930, 3, 'artikel', 'profisuche', 1),
(931, 3, 'artikel', 'produktion', 1),
(932, 3, 'artikel', 'stueckliste', 1),
(933, 3, 'artikel', 'wareneingang', 1),
(934, 3, 'artikel', 'verkaufdisable', 1),
(935, 3, 'artikel', 'shopexportfiles', 1),
(936, 3, 'artikel', 'onlineshop', 1),
(937, 3, 'artikel', 'provision', 1),
(938, 3, 'artikel', 'stuecklisteexport', 1),
(939, 3, 'artikel', 'offenebestellungen', 1),
(940, 3, 'artikel', 'projekte', 1),
(941, 3, 'artikel', 'statistik', 1),
(942, 3, 'artikel', 'rohstoffeempty', 1),
(943, 3, 'artikel', 'schnellanlegen', 1),
(944, 3, 'artikel', 'rabatt', 1),
(945, 3, 'artikel', 'schliessen', 1),
(946, 3, 'auftrag', 'abschicken', 1),
(947, 3, 'auftrag', 'archivierepdf', 1),
(948, 3, 'auftrag', 'alsfreigegeben', 1),
(949, 3, 'auftrag', 'ausversand', 1),
(950, 3, 'auftrag', 'anfrage', 1),
(951, 3, 'auftrag', 'berechnen', 1),
(952, 3, 'auftrag', 'checkdisplay', 1),
(953, 3, 'auftrag', 'abschluss', 1),
(954, 3, 'auftrag', 'dateien', 1),
(955, 3, 'auftrag', 'delauftragposition', 1),
(956, 3, 'auftrag', 'delete', 1),
(957, 3, 'auftrag', 'copy', 1),
(958, 3, 'auftrag', 'create', 1),
(959, 3, 'auftrag', 'deleterabatte', 1),
(960, 3, 'auftrag', 'addposition', 1),
(961, 3, 'auftrag', 'downauftragposition', 1),
(962, 3, 'auftrag', 'ean', 1),
(963, 3, 'auftrag', 'edit', 1),
(964, 3, 'auftrag', 'editable', 1),
(965, 3, 'auftrag', 'einkaufspreise', 1),
(966, 3, 'auftrag', 'inlinepdf', 1),
(967, 3, 'auftrag', 'kreditlimit', 1),
(968, 3, 'auftrag', 'list', 1),
(969, 3, 'auftrag', 'livetabelle', 1),
(970, 3, 'auftrag', 'freigabe', 1),
(971, 3, 'auftrag', 'lieferschein', 1),
(972, 3, 'auftrag', 'minidetail', 1),
(973, 3, 'auftrag', 'lieferscheinrechnung', 1),
(974, 3, 'auftrag', 'nachlieferung', 1),
(975, 3, 'auftrag', 'paypal', 1),
(976, 3, 'auftrag', 'positioneneditpopup', 1),
(977, 3, 'auftrag', 'pdf', 1),
(978, 3, 'auftrag', 'positionen', 1),
(979, 3, 'auftrag', 'proforma', 1),
(980, 3, 'auftrag', 'pdffromarchive', 1),
(981, 3, 'auftrag', 'produktion', 1),
(982, 3, 'auftrag', 'reservieren', 1),
(983, 3, 'auftrag', 'schreibschutz', 1),
(984, 3, 'auftrag', 'protokoll', 1),
(985, 3, 'auftrag', 'shopexport', 1),
(986, 3, 'auftrag', 'steuer', 1),
(987, 3, 'auftrag', 'teillieferung', 1),
(988, 3, 'auftrag', 'rechnung', 1),
(989, 3, 'auftrag', 'summe', 1),
(990, 3, 'auftrag', 'search', 1),
(991, 3, 'auftrag', 'tracking', 1),
(992, 3, 'auftrag', 'undelete', 1),
(993, 3, 'auftrag', 'upauftragposition', 1),
(994, 3, 'auftrag', 'uststart', 1),
(995, 3, 'auftrag', 'verfuegbar', 1),
(996, 3, 'auftrag', 'versand', 1),
(997, 3, 'auftrag', 'updateverband', 1),
(998, 3, 'auftrag', 'zahlungsmail', 1),
(999, 3, 'auftrag', 'zahlungsmahnungswesen', 1),
(1000, 3, 'auftrag', 'versandzentrum', 1),
(1001, 3, 'auftrag', 'zertifikate', 1),
(1002, 3, 'bestellung', 'abschicken', 1),
(1003, 3, 'bestellung', 'copy', 1),
(1004, 3, 'bestellung', 'addposition', 1),
(1005, 3, 'bestellung', 'archivierepdf', 1),
(1006, 3, 'bestellung', 'auftrag', 1),
(1007, 3, 'bestellung', 'dateien', 1),
(1008, 3, 'bestellung', 'abschliessen', 1),
(1009, 3, 'bestellung', 'delbestellungposition', 1),
(1010, 3, 'bestellung', 'create', 1),
(1011, 3, 'bestellung', 'downbestellungposition', 1),
(1012, 3, 'bestellung', 'delete', 1),
(1013, 3, 'bestellung', 'edit', 1),
(1014, 3, 'bestellung', 'editable', 1),
(1015, 3, 'bestellung', 'inlinepdf', 1),
(1016, 3, 'bestellung', 'einlagern', 1),
(1017, 3, 'bestellung', 'list', 1),
(1018, 3, 'bestellung', 'freigabe', 1),
(1019, 3, 'bestellung', 'livetabelle', 1),
(1020, 3, 'bestellung', 'minidetail', 1),
(1021, 3, 'bestellung', 'offenepositionen', 1),
(1022, 3, 'bestellung', 'pdf', 1),
(1023, 3, 'bestellung', 'positioneneditpopup', 1),
(1024, 3, 'bestellung', 'pdffromarchive', 1),
(1025, 3, 'bestellung', 'schreibschutz', 1),
(1026, 3, 'bestellung', 'positionen', 1),
(1027, 3, 'bestellung', 'protokoll', 1),
(1028, 3, 'bestellung', 'steuer', 1),
(1029, 3, 'bestellung', 'undelete', 1),
(1030, 3, 'bestellung', 'upbestellungposition', 1),
(1031, 3, 'kalender', 'data', 1),
(1032, 3, 'kalender', 'delete', 1),
(1033, 3, 'kalender', 'gruppenedit', 1),
(1034, 3, 'kalender', 'gruppendelete', 1),
(1035, 3, 'kalender', 'gruppenlist', 1),
(1036, 3, 'kalender', 'gruppensave', 1),
(1037, 3, 'kalender', 'eventdata', 1),
(1038, 3, 'kalender', 'ics', 1),
(1039, 3, 'kalender', 'taskstatus', 1),
(1040, 3, 'kalender', 'list', 1),
(1041, 3, 'kalender', 'update', 1),
(1042, 3, 'korrespondenz', 'create', 1),
(1043, 3, 'korrespondenz', 'delete', 1),
(1044, 3, 'korrespondenz', 'pdf', 1),
(1045, 3, 'korrespondenz', 'send', 1),
(1046, 3, 'korrespondenz', 'list', 1),
(1047, 3, 'korrespondenz', 'edit', 1),
(1048, 3, 'preisanfrage', 'abschicken', 1),
(1049, 3, 'preisanfrage', 'abschliessen', 1),
(1050, 3, 'preisanfrage', 'bestellung', 1),
(1051, 3, 'preisanfrage', 'buchen', 1),
(1052, 3, 'preisanfrage', 'addposition', 1),
(1053, 3, 'preisanfrage', 'copy', 1),
(1054, 3, 'preisanfrage', 'archivierepdf', 1),
(1055, 3, 'preisanfrage', 'dateien', 1),
(1056, 3, 'preisanfrage', 'delpreisanfrageposition', 1),
(1057, 3, 'preisanfrage', 'createfromproject', 1),
(1058, 3, 'preisanfrage', 'create', 1),
(1059, 3, 'preisanfrage', 'downpreisanfrageposition', 1),
(1060, 3, 'preisanfrage', 'edit', 1),
(1061, 3, 'preisanfrage', 'delete', 1),
(1062, 3, 'preisanfrage', 'inlinepdf', 1),
(1063, 3, 'preisanfrage', 'freigabe', 1),
(1064, 3, 'preisanfrage', 'livetabelle', 1),
(1065, 3, 'preisanfrage', 'editable', 1),
(1066, 3, 'preisanfrage', 'minidetail', 1),
(1067, 3, 'preisanfrage', 'list', 1),
(1068, 3, 'preisanfrage', 'pdf', 1),
(1069, 3, 'preisanfrage', 'positionen', 1),
(1070, 3, 'preisanfrage', 'positioneneditpopup', 1),
(1071, 3, 'preisanfrage', 'protokoll', 1),
(1072, 3, 'preisanfrage', 'schreibschutz', 1),
(1073, 3, 'preisanfrage', 'uppreisanfrageposition', 1),
(1074, 3, 'webmail', 'aktualisieren', 1),
(1075, 3, 'webmail', 'list', 1),
(1076, 3, 'webmail', 'suchen', 1),
(1077, 3, 'webmail', 'antworten', 1),
(1078, 3, 'webmail', 'schreiben', 1),
(1079, 3, 'webmail', 'view', 1),
(1080, 3, 'zolltarifnummer', 'create', 1),
(1081, 3, 'zolltarifnummer', 'delete', 1),
(1082, 3, 'zolltarifnummer', 'edit', 1),
(1083, 3, 'zolltarifnummer', 'list', 1),
(1084, 3, 'gutschrift', 'abschicken', 1),
(1085, 3, 'gutschrift', 'addposition', 1),
(1086, 3, 'gutschrift', 'create', 1),
(1087, 3, 'gutschrift', 'copy', 1),
(1088, 3, 'gutschrift', 'dateien', 1),
(1089, 3, 'gutschrift', 'delete', 1),
(1090, 3, 'gutschrift', 'archivierepdf', 1),
(1091, 3, 'gutschrift', 'deleterabatte', 1),
(1092, 3, 'gutschrift', 'downgutschriftposition', 1),
(1093, 3, 'gutschrift', 'edit', 1),
(1094, 3, 'gutschrift', 'delgutschriftposition', 1),
(1095, 3, 'gutschrift', 'einkaufspreise', 1),
(1096, 3, 'gutschrift', 'inlinepdf', 1),
(1097, 3, 'gutschrift', 'editable', 1),
(1098, 3, 'gutschrift', 'list', 1),
(1099, 3, 'gutschrift', 'freigabe', 1),
(1100, 3, 'gutschrift', 'formeln', 1),
(1101, 3, 'gutschrift', 'livetabelle', 1),
(1102, 3, 'gutschrift', 'pdffromarchive', 1),
(1103, 3, 'gutschrift', 'minidetail', 1),
(1104, 3, 'gutschrift', 'pdf', 1),
(1105, 3, 'gutschrift', 'positioneneditpopup', 1),
(1106, 3, 'gutschrift', 'schreibschutz', 1),
(1107, 3, 'gutschrift', 'positionen', 1),
(1108, 3, 'gutschrift', 'storno', 1),
(1109, 3, 'gutschrift', 'protokoll', 1),
(1110, 3, 'gutschrift', 'summe', 1),
(1111, 3, 'gutschrift', 'zahlungseingang', 1),
(1112, 3, 'gutschrift', 'steuer', 1),
(1113, 3, 'gutschrift', 'upgutschriftposition', 1),
(1114, 3, 'gutschrift', 'zahlungsmahnungswesen', 1),
(1115, 3, 'kasse', 'abschluss', 1),
(1116, 3, 'kasse', 'anfangsbestand', 1),
(1117, 3, 'kasse', 'berechnen', 1),
(1118, 3, 'kasse', 'delete', 1),
(1119, 3, 'kasse', 'detail', 1),
(1120, 3, 'kasse', 'down', 1),
(1121, 3, 'kasse', 'archiv', 1),
(1122, 3, 'kasse', 'create', 1),
(1123, 3, 'kasse', 'dateien', 1),
(1124, 3, 'kasse', 'export', 1),
(1125, 3, 'kasse', 'kassepdf', 1),
(1126, 3, 'kasse', 'list', 1),
(1127, 3, 'kasse', 'edit', 1),
(1128, 3, 'kasse', 'storno', 1),
(1129, 3, 'kasse', 'pdf', 1),
(1130, 3, 'kasse', 'up', 1),
(1131, 3, 'kontoblatt', 'create', 1),
(1132, 3, 'kontoblatt', 'datev', 1),
(1133, 3, 'kontoblatt', 'edit', 1),
(1134, 3, 'kontoblatt', 'differenzen', 1),
(1135, 3, 'kontoblatt', 'delete', 1),
(1136, 3, 'kontoblatt', 'editable', 1),
(1137, 3, 'kontoblatt', 'differenzenkonto', 1),
(1138, 3, 'kontoblatt', 'exportieren', 1),
(1139, 3, 'kontoblatt', 'fehlende', 1),
(1140, 3, 'kontoblatt', 'experte', 1),
(1141, 3, 'kontoblatt', 'exportadressen', 1),
(1142, 3, 'kontoblatt', 'list', 1),
(1143, 3, 'kontoblatt', 'kassesaldo', 1),
(1144, 3, 'kontoblatt', 'mahnwesenlive', 1),
(1145, 3, 'kontoblatt', 'salden', 1),
(1146, 3, 'kontoblatt', 'opos', 1),
(1147, 3, 'kontoblatt', 'saldendatev', 1),
(1148, 3, 'kontoblatt', 'saldo', 1),
(1149, 3, 'lohnabrechnung', 'details', 1),
(1150, 3, 'lohnabrechnung', 'list', 1),
(1151, 3, 'lohnabrechnung', 'monatsuebersicht', 1),
(1152, 3, 'lohnabrechnung', 'minidetail', 1),
(1153, 3, 'mahnwesen', 'forderungsverlust', 1),
(1154, 3, 'mahnwesen', 'mahnpdf', 1),
(1155, 3, 'mahnwesen', 'list', 1),
(1156, 3, 'mahnwesen', 'destop', 1),
(1157, 3, 'mahnwesen', 'mahnweseneinstellungen', 1),
(1158, 3, 'mahnwesen', 'manuellbezahltentfernen', 1),
(1159, 3, 'mahnwesen', 'skonto', 1),
(1160, 3, 'mahnwesen', 'manuellbezahltmarkiert', 1),
(1161, 3, 'mahnwesen', 'stop', 1),
(1162, 3, 'proformarechnung', 'abschicken', 1),
(1163, 3, 'proformarechnung', 'addposition', 1),
(1164, 3, 'proformarechnung', 'archivierepdf', 1),
(1165, 3, 'proformarechnung', 'copy', 1),
(1166, 3, 'proformarechnung', 'buchen', 1),
(1167, 3, 'proformarechnung', 'dateien', 1),
(1168, 3, 'proformarechnung', 'delproformarechnungposition', 1),
(1169, 3, 'proformarechnung', 'create', 1),
(1170, 3, 'proformarechnung', 'downproformarechnungposition', 1),
(1171, 3, 'proformarechnung', 'abschliessen', 1),
(1172, 3, 'proformarechnung', 'editable', 1),
(1173, 3, 'proformarechnung', 'delete', 1),
(1174, 3, 'proformarechnung', 'freigabe', 1),
(1175, 3, 'proformarechnung', 'formeln', 1),
(1176, 3, 'proformarechnung', 'inlinepdf', 1),
(1177, 3, 'proformarechnung', 'lieferscheine', 1),
(1178, 3, 'proformarechnung', 'list', 1),
(1179, 3, 'proformarechnung', 'edit', 1),
(1180, 3, 'proformarechnung', 'positionen', 1),
(1181, 3, 'proformarechnung', 'minidetail', 1),
(1182, 3, 'proformarechnung', 'livetabelle', 1),
(1183, 3, 'proformarechnung', 'pdf', 1),
(1184, 3, 'proformarechnung', 'protokoll', 1),
(1185, 3, 'proformarechnung', 'positioneneditpopup', 1),
(1186, 3, 'proformarechnung', 'schreibschutz', 1),
(1187, 3, 'proformarechnung', 'upproformarechnungposition', 1),
(1188, 3, 'ratenzahlung', 'create', 1),
(1189, 3, 'ratenzahlung', 'details', 1),
(1190, 3, 'ratenzahlung', 'list', 1),
(1191, 3, 'ratenzahlung', 'freitext', 1),
(1192, 3, 'ratenzahlung', 'delete', 1),
(1193, 3, 'ratenzahlung', 'save', 1),
(1194, 3, 'ratenzahlung', 'minidetail', 1),
(1195, 3, 'ratenzahlung', 'edit', 1),
(1196, 3, 'ratenzahlung', 'pdf', 1),
(1197, 3, 'rechnung', 'abschicken', 1),
(1198, 3, 'rechnung', 'alternativpdf', 1),
(1199, 3, 'rechnung', 'addposition', 1),
(1200, 3, 'rechnung', 'copy', 1),
(1201, 3, 'rechnung', 'archivierepdf', 1),
(1202, 3, 'rechnung', 'create', 1),
(1203, 3, 'rechnung', 'dateien', 1),
(1204, 3, 'rechnung', 'deleterabatte', 1),
(1205, 3, 'rechnung', 'dta', 1),
(1206, 3, 'rechnung', 'delrechnungposition', 1),
(1207, 3, 'rechnung', 'downrechnungposition', 1),
(1208, 3, 'rechnung', 'delete', 1),
(1209, 3, 'rechnung', 'editable', 1),
(1210, 3, 'rechnung', 'formeln', 1),
(1211, 3, 'rechnung', 'freigabe', 1),
(1212, 3, 'rechnung', 'edit', 1),
(1213, 3, 'rechnung', 'lastschrift', 1),
(1214, 3, 'rechnung', 'gutschrift', 1),
(1215, 3, 'rechnung', 'einkaufspreise', 1),
(1216, 3, 'rechnung', 'inlinepdf', 1),
(1217, 3, 'rechnung', 'lastschriftwdh', 1),
(1218, 3, 'rechnung', 'list', 1),
(1219, 3, 'rechnung', 'manuellbezahltmarkiert', 1),
(1220, 3, 'rechnung', 'minidetail', 1),
(1221, 3, 'rechnung', 'mahnwesen', 1),
(1222, 3, 'rechnung', 'livetabelle', 1),
(1223, 3, 'rechnung', 'manuellbezahltentfernen', 1),
(1224, 3, 'rechnung', 'pdf', 1),
(1225, 3, 'rechnung', 'pdffromarchive', 1),
(1226, 3, 'rechnung', 'protokoll', 1),
(1227, 3, 'rechnung', 'multilevel', 1),
(1228, 3, 'rechnung', 'positionen', 1),
(1229, 3, 'rechnung', 'search', 1),
(1230, 3, 'rechnung', 'positioneneditpopup', 1),
(1231, 3, 'rechnung', 'schreibschutz', 1),
(1232, 3, 'rechnung', 'steuer', 1),
(1233, 3, 'rechnung', 'updateverband', 1),
(1234, 3, 'rechnung', 'uprechnungposition', 1),
(1235, 3, 'rechnung', 'summe', 1),
(1236, 3, 'rechnung', 'zahlungsmahnungswesen', 1),
(1237, 3, 'rechnung', 'zahlungseingang', 1),
(1238, 3, 'rechnung', 'zertifikate', 1),
(1239, 3, 'sammelrechnung', 'chcb', 1),
(1240, 3, 'sammelrechnung', 'chmenge', 1),
(1241, 3, 'sammelrechnung', 'list', 1),
(1242, 3, 'sammelrechnung', 'minidetail', 1),
(1243, 3, 'sammelrechnung', 'edit', 1),
(1244, 3, 'verbindlichkeit', 'bezahlt', 1),
(1245, 3, 'verbindlichkeit', 'create', 1),
(1246, 3, 'verbindlichkeit', 'delete', 1),
(1247, 3, 'verbindlichkeit', 'createbestellung', 1),
(1248, 3, 'verbindlichkeit', 'editreadonly', 1),
(1249, 3, 'verbindlichkeit', 'edit', 1),
(1250, 3, 'verbindlichkeit', 'kontierungen', 1),
(1251, 3, 'verbindlichkeit', 'dateien', 1),
(1252, 3, 'verbindlichkeit', 'kostenstelle', 1),
(1253, 3, 'verbindlichkeit', 'einstellungen', 1),
(1254, 3, 'verbindlichkeit', 'list', 1),
(1255, 3, 'verbindlichkeit', 'offen', 1),
(1256, 3, 'verbindlichkeit', 'minidetail', 1),
(1257, 4, 'welcome', 'login', 1),
(1258, 4, 'welcome', 'logout', 1),
(1259, 4, 'welcome', 'start', 1),
(1260, 4, 'welcome', 'startseite', 1),
(1261, 4, 'welcome', 'settings', 1),
(1262, 4, 'versanderzeugen', 'artikel', 1),
(1263, 4, 'versanderzeugen', 'frankieren', 1),
(1264, 4, 'versanderzeugen', 'gelesen', 1),
(1265, 4, 'versanderzeugen', 'korrektur', 1),
(1266, 4, 'versanderzeugen', 'delete', 1),
(1267, 4, 'versanderzeugen', 'list', 1),
(1268, 4, 'versanderzeugen', 'freigabe', 1),
(1269, 4, 'versanderzeugen', 'einzel', 1),
(1270, 4, 'versanderzeugen', 'main', 1),
(1271, 4, 'versanderzeugen', 'artikeloffen', 1),
(1272, 4, 'versanderzeugen', 'wechsel', 1),
(1273, 4, 'versanderzeugen', 'schnelleingabe', 1),
(1274, 4, 'lieferschein', 'addposition', 1),
(1275, 4, 'lieferschein', 'copy', 1),
(1276, 4, 'lieferschein', 'abschicken', 1),
(1277, 4, 'lieferschein', 'auslagern', 1),
(1278, 4, 'lieferschein', 'dellieferscheinposition', 1),
(1279, 4, 'lieferschein', 'archivierepdf', 1),
(1280, 4, 'lieferschein', 'create', 1),
(1281, 4, 'lieferschein', 'downlieferscheinposition', 1),
(1282, 4, 'lieferschein', 'abschliessen', 1),
(1283, 4, 'lieferschein', 'delete', 1),
(1284, 4, 'lieferschein', 'eingabeseriennummern', 1),
(1285, 4, 'lieferschein', 'freigabe', 1),
(1286, 4, 'lieferschein', 'list', 1),
(1287, 4, 'lieferschein', 'minidetail', 1),
(1288, 4, 'lieferschein', 'inlinepdf', 1),
(1289, 4, 'lieferschein', 'paketmarke', 1),
(1290, 4, 'lieferschein', 'pdffromarchive', 1),
(1291, 4, 'lieferschein', 'positionen', 1),
(1292, 4, 'lieferschein', 'livetabelle', 1),
(1293, 4, 'lieferschein', 'editable', 1),
(1294, 4, 'lieferschein', 'edit', 1),
(1295, 4, 'lieferschein', 'rechnung', 1),
(1296, 4, 'lieferschein', 'schreibschutz', 1),
(1297, 4, 'lieferschein', 'uplieferscheinposition', 1),
(1298, 4, 'lieferschein', 'positionenetiketten', 1),
(1299, 4, 'lieferschein', 'protokoll', 1),
(1300, 4, 'lieferschein', 'pdf', 1);
INSERT INTO `uservorlagerights` (`id`, `vorlage`, `module`, `action`, `permission`) VALUES
(1301, 4, 'lieferschein', 'positioneneditpopup', 1),
(1302, 4, 'laufzettel', 'download', 1),
(1303, 4, 'lieferbedingungen', 'list', 1),
(1304, 4, 'kommissionierlauf', 'edit', 1),
(1305, 4, 'kommissionierlauf', 'minidetail', 1),
(1306, 4, 'kommissionierlauf', 'versandzentrum', 1),
(1307, 4, 'kommissionierlauf', 'label', 1),
(1308, 4, 'kommissionierlauf', 'list', 1),
(1309, 4, 'drucker', 'list', 1),
(1310, 4, 'auftrag', 'list', 1),
(1311, 4, 'versanderzeugen', 'offene', 1),
(1312, 4, 'auftrag', 'minidetail', 1),
(1313, 4, 'prozess_monitor', 'delete', 1),
(1314, 4, 'prozess_monitor', 'list', 1),
(1315, 4, 'prozess_monitor', 'minidetail', 1),
(1316, 4, 'prozess_monitor', 'edit', 1),
(1317, 4, 'prozess_monitor', 'create', 1),
(1318, 4, 'drucker', 'spooler', 1),
(1319, 4, 'drucker', 'spoolerdownload', 1),
(1320, 4, 'drucker', 'spoolerdownloadall', 1);

INSERT INTO `versandarten` (`id`, `type`, `bezeichnung`, `aktiv`, `geloescht`, `projekt`, `modul`, `keinportocheck`, `paketmarke_drucker`, `export_drucker`, `einstellungen_json`, `ausprojekt`, `versandmail`, `geschaeftsbrief_vorlage`) VALUES
(1, 'DHL', 'DHL', 1, 0, 0, 'dhlversenden', 0, 0, 0, '', 0, 0, 0),
(2, 'DPD', 'DPD', 1, 0, 0, 'dpdapi', 0, 0, 0, '', 0, 0, 0),
(3, 'express_dpd', 'Express DPD', 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0),
(4, 'export_dpd', 'Export DPD', 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0),
(5, 'gls', 'GLS', 1, 0, 0, 'glsapi', 0, 0, 0, '', 0, 0, 0),
(6, 'keinversand', 'Kein Versand', 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0),
(7, 'versandunternehmen', 'Sonstige', 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0),
(8, 'spedition', 'Spedition', 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0),
(9, 'Go', 'GO!', 1, 0, 0, '', 0, 0, 0, '', 0, 0, 0);

INSERT INTO `wiedervorlage_view` (`id`, `name`, `shortname`, `active`, `project`) VALUES
(1, 'To Do Liste', 'Kanban', 1, 0),
(2, 'Sprint Board / Roadmap', 'Sprint', 1, 0),
(3, 'Sales Funnel', 'Sales', 1, 0),
(4, 'Marketing Funnel', 'Marketing', 1, 0),
(5, 'Projekte', 'Projekte', 1, 0),
(6, 'Website-Relaunch (Beispiel)', 'Webseite', 1, 0);

INSERT INTO `wiedervorlage_stages` (`id`, `kurzbezeichnung`, `name`, `hexcolor`, `ausblenden`, `stageausblenden`, `sort`, `view`, `chance`) VALUES
(1, 'Anfrage', 'Anfrage', '#A2D624', 0, 1, 1, 3, 0),
(2, 'Angebot', 'Angebot abgegeben', '#A2D624', 0, 1, 3, 3, 20),
(3, 'Beratung', 'Termin zur Beratung durchgeführt', '#A2D624', 0, 1, 2, 3, 10),
(4, 'Besprechung Angebot', 'Besprechung Angebot', '#A2D624', 0, 1, 4, 3, 50),
(5, 'Baldiger Abschluß', 'Kurz vor Kauf', '#A2D624', 0, 1, 5, 3, 80),
(6, 'ToDo', 'Zu erledigen', '#A2D624', 0, 1, 1, 1, NULL),
(7, 'InProgress', 'In Arbeit', '#A2D624', 0, 1, 2, 1, NULL),
(8, 'Done', 'Erledigt', '#A2D624', 0, 1, 3, 1, NULL),
(9, 'Awareness', 'Awareness (Bewusstsein)', '#A2D624', 0, 1, 1, 4, NULL),
(10, 'Consideration', 'Consideration (Überlegung)', '#A2D624', 0, 1, 2, 4, NULL),
(11, 'Conversion', 'Conversion (Konvertierung)', '#A2D624', 0, 1, 3, 4, NULL),
(12, 'Stay', 'Stay (Erhalt)', '#A2D624', 0, 1, 4, 4, NULL),
(13, 'Okay', 'Okay (Befürwortung)', '#A2D624', 0, 1, 5, 4, NULL);
INSERT INTO `wiki` (`id`, `name`, `content`, `lastcontent`) VALUES
(1, 'StartseiteWiki', '\n<p>Herzlich Willkommen in Ihrem OpenXE, dem freien ERP.<br><br>Wir freuen uns Sie als Benutzer begrüßen zu dürfen. Mit OpenXE organisieren Sie Ihre Firma schnell und einfach. Sie haben alle wichtigen Zahlen und Vorgänge im Überblick.<br><br>Für Einsteiger sind die folgenden Themen wichtig:<br><br></p>\n<ul>\n<li> <a href="index.php?module=firmendaten&amp;action=edit" target="_blank"> Firmendaten</a> (dort richten Sie Ihr Briefpapier ein)</li>\n<li> <a href="index.php?module=adresse&amp;action=list" target="_blank"> Stammdaten / Adressen</a> (Kunden und Lieferanten anlegen)</li>\n<li> <a href="index.php?module=artikel&amp;action=list" target="_blank"> Artikel anlegen</a> (Ihr Artikelstamm)</li>\n<li> <a href="index.php?module=angebot&amp;action=list" target="_blank"> Angebot</a> / <a href="index.php?module=auftrag&amp;action=list" target="_blank"> Auftrag</a> (Alle Dokumente für Ihr Geschäft)</li>\n<li> <a href="index.php?module=rechnung&amp;action=list" target="_blank"> Rechnung</a> / <a href="index.php?module=gutschrift&amp;action=list" target="_blank"> Gutschrift</a></li>\n<li> <a href="index.php?module=lieferschein&amp;action=list" target="_blank"> Lieferschein</a></li>\n</ul>\n<p><br><br>Kennen Sie unsere Zusatzmodule die Struktur und Organisation in das tägliche Geschäft bringen?<br><br></p>\n<ul>\n<li> <a href="index.php?module=kalender&amp;action=list" target="_blank"> Kalender</a></li>\n<li> <a href="index.php?module=wiki&amp;action=list" target="_blank"> Wiki</a></li>\n</ul>', NULL);


INSERT INTO `konten` (`id`, `bezeichnung`, `kurzbezeichnung`, `type`, `erstezeile`, `datevkonto`, `blz`, `konto`, `swift`, `iban`, `lastschrift`, `hbci`, `hbcikennung`, `inhaber`, `aktiv`, `keineemail`, `firma`, `schreibbar`, `importletztenzeilenignorieren`, `liveimport`, `liveimport_passwort`, `liveimport_online`, `importtrennzeichen`, `codierung`, `importerstezeilenummer`, `importdatenmaskierung`, `importnullbytes`, `glaeubiger`, `geloescht`, `projekt`, `saldo_summieren`, `saldo_betrag`, `saldo_datum`, `importfelddatum`, `importfelddatumformat`, `importfelddatumformatausgabe`, `importfeldbetrag`, `importfeldbetragformat`, `importfeldbuchungstext`, `importfeldbuchungstextformat`, `importfeldwaehrung`, `importfeldwaehrungformat`, `importfeldhabensollkennung`, `importfeldkennunghaben`, `importfeldkennungsoll`, `importextrahabensoll`, `importfeldhaben`, `importfeldsoll`, `cronjobaktiv`, `cronjobverbuchen`) VALUES
(1, 'Bankkonto CSV (Beispiel: Deutsche Bank)', '', 'konto', '', 1800, '', '', '', '', 0, 0, '', '', 1, 0, 1, 1, 1, '', '', 0, 'semikolon', '', 6, 'keine', 0, '', 0, 0, 0, '0.00', '0000-00-00', '1', '%1.%2.%3', '%3-%2-%1', '', '', '4+5+8+9+10+6+7+3', '', '18', '', '', '', '', 1, '17', '16', 0, 0);

INSERT INTO `artikelkategorien` (`id`, `bezeichnung`, `next_nummer`, `projekt`, `geloescht`, `externenummer`, `parent`, `steuer_erloese_inland_normal`, `steuer_aufwendung_inland_normal`, `steuer_erloese_inland_ermaessigt`, `steuer_aufwendung_inland_ermaessigt`, `steuer_erloese_inland_steuerfrei`, `steuer_aufwendung_inland_steuerfrei`, `steuer_erloese_inland_innergemeinschaftlich`, `steuer_aufwendung_inland_innergemeinschaftlich`, `steuer_erloese_inland_eunormal`, `steuer_erloese_inland_nichtsteuerbar`, `steuer_erloese_inland_euermaessigt`, `steuer_aufwendung_inland_nichtsteuerbar`, `steuer_aufwendung_inland_eunormal`, `steuer_aufwendung_inland_euermaessigt`, `steuer_erloese_inland_export`, `steuer_aufwendung_inland_import`, `steuertext_innergemeinschaftlich`, `steuertext_export`) VALUES
(1, 'Handelsware (100000)', '100000', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(2, 'Dienstleistungen', '', 0, 0, 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(3, 'Versandartikel', '', 0, 0, 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(4, 'Produktionsmaterial', 'PM-', 0, 0, 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(5, 'Fremdleistung', '', 0, 0, 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(6, 'Sonstiges', '', 0, 0, 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL);


INSERT INTO `label_group` (`id`, `group_table`, `title`, `created_at`) VALUES
(1, 'wiki', 'Wiki', '2019-12-03 15:04:30'),
(2, 'projekt', 'Projekt', '2019-12-13 16:07:25'),
(3, 'adresse', 'Adresse', '2019-12-16 13:08:24'),
(4, 'artikel', 'Artikel', '2020-01-01 07:00:00'),
(5, 'auftrag', 'Auftrag', '2020-01-01 07:00:00'),
(6, 'bestellung', 'Bestellung', '2020-01-01 07:00:00'),
(7, 'retoure', 'Retoure', '2020-01-01 07:00:00'),
(8, 'produktion', 'Produktion', '2020-01-01 07:00:00'),
(9, 'angebot', 'Angebot', '2020-01-01 07:00:00'),
(10, 'anfrage', 'Anfrage', '2020-01-01 07:00:00'),
(11, 'lieferschein', 'Lieferschein', '2020-01-01 07:00:00'),
(12, 'lieferscheineinbearbeitung', 'Lieferscheineinbearbeitung', '2020-01-01 07:00:00'),
(13, 'wiedervorlage', 'Wiedervorlage', '2020-01-01 07:00:00'),
(14, 'gutschrift', 'Gutschrift', '2020-01-01 07:00:00'),
(15, 'gutschrifteninbearbeitung', 'Gutschrifteninbearbeitung', '2020-01-01 07:00:00'),
(16, 'gutschriftenoffene', 'Gutschriftenoffene', '2020-01-01 07:00:00');


INSERT INTO `label_type` (`id`, `label_group_id`, `type`, `title`, `hexcolor`, `created_at`, `updated_at`) VALUES
(1, 0, 'erledigt', 'Erledigt', '#339966', '2020-01-13 10:32:50', '2020-01-13 11:40:45'),
(2, 0, 'zuklaeren', 'Zu klären', '#ff0000', '2020-01-13 10:33:23', '2020-01-13 11:40:45'),
(3, 3, 'adressefalsch', 'Adresse falsch', '#ff6600', '2020-01-13 10:34:01', '2020-01-13 11:40:45'),
(4, 3, 'umgezogen', 'Umgezogen', '#ff6600', '2020-01-13 10:34:22', '2020-01-13 11:40:45'),
(5, 0, 'prio', 'Prio', '#ff00ff', '2020-01-13 11:29:09', '2020-01-13 11:30:28'),
(6, 3, 'kontaktdatenfalsch', 'Kontaktdaten falsch', '#ff6600', '2020-01-13 11:31:17', '2020-01-13 11:40:45'),
(7, 5, 'reklamation', 'Reklamation', '#00ccff', '2020-01-13 11:35:27', '2020-01-13 11:40:45'),
(8, 5, 'externeproduktion', 'Externe Produktion', '#000000', '2020-01-13 11:37:04', '2020-01-13 11:40:45'),
(9, 7, 'weabwartengutschrift', 'Wareneingang abwarten > Gutschrift', '#85cddb', '2020-01-13 11:37:53', '2020-01-13 12:38:58'),
(10, 7, 'weabwartenaustausch', 'Wareneingang abwarten > Austausch', '#99cc00', '2020-01-13 11:38:24', '2020-01-13 12:39:10'),
(11, 7, 'weabwartenersatzgeliefer', 'Wareneingang abwarten > Ersatzware schon geliefert', '#00ccff', '2020-01-13 11:39:33', '2020-01-13 12:39:04'),
(12, 0, 'wartenaufkunde', 'Warten auf Kunde', '#a9ca45', '2020-01-13 11:42:01', '2020-01-13 11:43:08'),
(13, 0, 'wartenauflieferant', 'Warten auf Lieferant', '#ff99cc', '2020-01-13 11:42:13', '2020-01-13 11:43:32'),
(14, 4, 'keinlagerbestand', 'Kein Lagerbestand', '#ff0000', '2020-01-13 12:34:51', '2020-01-13 11:40:45'),
(15, 4, 'anfrageanlieferant', 'Anfrage an Lieferant', '#ff9900', '2020-01-13 12:35:43', '2020-01-13 11:40:45'),
(16, 4, 'konfigurierbar', 'Konfigurierbar', '#ff99cc', '2020-01-13 12:41:23', '2020-01-13 11:40:45'),
(17, 5, 'mitkonfiguration', 'Enthält Konfiguration', '#ff99cc', '2020-01-13 12:41:55', '2020-01-13 11:40:45'),
(19, 0, 'pruefen', 'Prüfen', '#99ccff', '2020-01-13 12:43:32', '2020-01-13 11:40:45');


INSERT INTO `ticket_vorlage` (`id`, `projekt`, `vorlagenname`, `vorlage`, `firma`, `sichtbar`) VALUES
(1, 1, 'Bewerbung Zusage Gespräch', '{ANREDE} {NAME},\r\n\r\nwir danken Ihnen für Ihre Bewerbung und das Interesse an unserem Unternehmen, dass Sie uns damit entgegengebracht haben.\r\n\r\nGerne würden wir Sie persönlich kennenlernen und laden Sie hiermit zu einem Bewerbungsgespräch in unserem Haus ein.\r\n\r\nTermin: \r\n\r\nBitte teilen Sie uns mit, ob Sie diesen Termin wahrnehmen können.\r\n\r\n{GRUSSWORT}\r\n{ANSPRECHPARTNER}', 1, 0),
(2, 1, 'Bewerbung Eingangsbestätigung', '{ANREDE} {NAME},\r\n\r\nzunächst möchten wir uns für Ihre Bewerbung bedanken und freuen uns über Ihr Interesse an einer Mitarbeit in unserem Unternehmen.\r\n\r\nWir prüfen alle eingesandten Unterlagen gründlich, deshalb kann die Bearbeitung einige Zeit in Anspruch nehmen. Wir bitten Sie daher um etwas Geduld.\r\n\r\nSobald wir eine engere Auswahl getroffen haben, werden wir uns wieder mit Ihnen in Verbindung setzen.\r\n\r\n{GRUSSWORT}\r\n{ANSPRECHPARTNER}', 1, 0),
(3, 1, 'Bewerbung Absage', '{ANREDE} {NAME},\r\n\r\nwir danken Ihnen für Ihre Bewerbung und das Interesse an unserem Unternehmen, dass Sie uns damit entgegengebracht haben.\r\n\r\nIm Verlauf des Auswahlprozesses haben wir uns für einen anderen Bewerber entschieden.\r\n\r\nWir wünschen Ihnen weiterhin viel Erfolg bei der Suche nach einer neuen Aufgabe und wünschen Ihnen für die Zukunft alles Gute!\r\n\r\n{GRUSSWORT}\r\n{ANSPRECHPARTNER}', 1, 0);
INSERT INTO `konfiguration` (`name`, `wert`, `adresse`, `firma`) VALUES
('mahnwesen_ik_tage', '10', 1, 1),
('mahnwesen_m1_tage', '10', 1, 1),
('mahnwesen_m2_tage', '10', 1, 1),
('mahnwesen_m3_tage', '10', 1, 1);
-- MySQL dump 10.13  Distrib 5.7.32, for Linux (x86_64)
--
-- Host: localhost    Database: xentral
-- ------------------------------------------------------
-- Server version	5.7.32-0ubuntu0.18.04.1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `checkaltertable`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `checkaltertable` (
                                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                                 `checksum` varchar(128) NOT NULL DEFAULT '',
                                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=585 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checkaltertable`
--

LOCK TABLES `checkaltertable` WRITE;
/*!40000 ALTER TABLE `checkaltertable` DISABLE KEYS */;
INSERT INTO `checkaltertable` VALUES (0,'469943e44abf04fd2bfb79137823d259'),(1,'56200a272ea6d48be3c0234c68da1a7b'),(2,'5ad92b9b53bb563bae950a24305e6319'),(3,'8582a4debf2393146f1dfd9c92b5abf2'),(4,'56561c78a24519d65235d322a6efe556'),(5,'5a5ff925d985d8f4ca7904b03fb6ee3a'),(6,'2daedf89061e7cb5cc834e981b171069'),(7,'ee7545aebcbf7807dc641f31495e4e8c'),(8,'7a175231df9a397886d17dedabe97761'),(9,'5ad056173d3358dbbca1f7c728458147'),(10,'92dd8439e8ede44c3e640e2e34330520'),(11,'7fcb415d3a6ac3b6eb478ba06f01ab1e'),(12,'fd641e02ed1fef7daab0044e1023d0c8'),(13,'0378347566dfcb9f0ea33c5ea8da53f1'),(14,'9974336ffc3c558ff906ce8ce8884e14'),(15,'e3462e8c785dc70fa5b506e4af992103'),(16,'200c43ecc0a88c39664b84b1da1aa9c1'),(17,'8d8869bcdcef7f46acb0b8de1ce501ba'),(18,'b9656cd5708d08d7877b74ca6a824023'),(19,'3242a354bf8f91151f041d44d2a7f435'),(20,'faf5476685de3eb8f0737a8c9e9734dd'),(21,'d2d99148f8e4cfc80bb278226e54b4ff'),(22,'d6aabcf86db1e8465a535e72127cde4f'),(23,'606decccadba40aa1d94912420c89742'),(24,'e0916eef7c31202e3b2686a8357a592c'),(25,'c3d6901f72201b55154bea4a130357cc'),(26,'df80612c1b1c63e286c7116e7ee4a2a1'),(27,'1c59c78a0aeab59a1119d47ba40b222c'),(28,'458f636b1ffb971f223459d6e8127344'),(29,'0d9980e8bf188cda8e8a8b30c6500530'),(30,'6100e00daff2d364c60b241db45ef916'),(31,'77a00132c31207f68b2e9da2164461f6'),(32,'9575c3ab0fedc92de1729f1fd0b2fe79'),(33,'5b93ad0edea715b060761b29800daca2'),(34,'d0a1ecb5142a7736850f055fec3cad4a'),(35,'a1b1add40ab46b019052a1bf480befbd'),(36,'b3ee2620046ec23e95345593165bc62b'),(37,'1cb73e98bc09da227c405d59304a71ee'),(38,'d91e4e396897c8078b94c8cd326d1ae7'),(39,'05054e32bfcdeb4452d5ddfd5cfcda53'),(40,'a995ca868ff247c7c82fdc5bb2c14da1'),(41,'9e548944ad186ee2ed9b98aa489cf1ed'),(42,'c3f2d2a1992941067475353ee78a6f90'),(43,'e46c56074787c6d8d21032fed84cc181'),(44,'d7e3e6830970a6e370933e59acd44194'),(45,'f758d7e774cbe937f66f2e904c3881e2'),(46,'74c0c8661747d1031c0fd5431a6c07fd'),(47,'ce57bf2dff4fd4017e07e7b8e363e4f3'),(48,'f78133595216906adc5b3c938e7c629d'),(49,'6943b11feaee4003002159d060ee7bd3'),(50,'796287565102f6e19e525530a27dd0bf'),(51,'9e3ee42124dff86a040c43bc53e1a8e3'),(52,'7a8a10bf5203ab9f8b555a06e64954a6'),(53,'b3dd30df044100e35ff5ea69b82d2efd'),(54,'09ba71c755929ac6f5421ae3b42fe73a'),(55,'2e610cd434eb823e0b6775f78b0f6d66'),(56,'63268e22b05578ed6c92dc37bfe8878e'),(57,'c5422323f58b9d1a1066ab1d04cac2cf'),(58,'1ba87eda1b4da2a00b24aa7636a397f2'),(59,'f628f29f4d092bb7cbfa84adf4549438'),(60,'81d54157d3a545c85b545d57075bdd20'),(61,'a3ab812805e64909ffb21888b9c75044'),(62,'15a80b890c56534fc7146c4d8c0cdf22'),(63,'e4d8ce17b4ae97edfd4e162a6f1c97e9'),(64,'1522e91ab89069c081a78a69b8216429'),(65,'ae3c557237355eb7d7f8e3be74c14427'),(66,'c3e2e0efa40cdf2b76a6ab8f731059f5'),(67,'9ee44acad5a8d1cf221b683c0ed3b169'),(68,'bf6c2d03d6b63efa143d965cf8adc6e8'),(69,'93ec7163ac77a1517540a79c6bf8ff36'),(70,'9edcf3c8db9d0f53b001a5c5f16bc8e6'),(71,'e962c7b853a0295208a905d44c9a9c39'),(72,'692a17cc210c02c8bb1a637d3f75bd06'),(73,'6d63fed96fcb7d570eeb403e5f09aad6'),(74,'c5208861ce3ecfb629661ad3db1434bb'),(75,'b0b9a3192076b537111d0ea01f032e87'),(76,'8575cfa891a8208eee9fbc291f01027f'),(77,'d5e77330f15e446734ed7f71a66d1154'),(78,'f2acfebae8e8d90c45b9077a689f1339'),(79,'aaeb4cc6fe0a7a33dc9a0560e67f65ef'),(80,'ebf292a410ed41171b5bb55ae29a419d'),(81,'0ddb5cd661a2c237b7fef9bcd88b68b9'),(82,'486f86bd5a4a856a29baf8b33d9f8cd1'),(83,'11e751bb472319ac2d387086f0e470f7'),(84,'b0fc49f56e4562eb85558afa8c84e3b9'),(85,'9157fc8f94f3a4e2fb8be15299817a27'),(86,'e00bfcfaa3b80550e97d2b918623dfe4'),(87,'fe3cac6c88e5ca7271dd015f80266e1b'),(88,'fa9d9b45363055994084fe0d6d418b30'),(89,'0a363ad7e037c30fba85af82cbba8d77'),(90,'990985df9775845fa440c0b66fff591e'),(91,'43470c43ea8c09d424cf9bbc355b0a15'),(92,'264448fbb84b3c05b750db7866854bb7'),(93,'c548fb43b40bd30fb0c218345ebcdd2b'),(94,'a9747002c2d485db7490267c964a16ef'),(95,'8085ad955b6679a67a4285d32e3e8d41'),(96,'4f657e4cca5260a8cf1f9393c8b621d4'),(97,'a9ed013b7b5d8a6e5baec97c3335603a'),(98,'639d2506f1017c446f13ff1c2dd9c342'),(99,'8e41a64ed11277034494023a484aad31'),(100,'95b9f2a2483abc22083ebab70ce9f0a7'),(101,'22bc5b763c421b8d8ff6d8aa3add37ab'),(102,'d9f2cb0c225df40f892260c6284131e0'),(103,'7327bcf36a36b4851ae9528cd7fbe64b'),(104,'34052f6c051ae8469fde3e7b7ccaa69f'),(105,'36c1c0a8a6186bce50c76129f2c22546'),(106,'c60cf1fc2dacf9d7424863e9a5ac8b76'),(107,'90f302296a3938f92ee2fc6e2687f94a'),(108,'670ae71fc41e6eb34f5643bc0bb889ad'),(109,'3bf1eeccbcde4023d82c9565b3538fd6'),(110,'46124bfca7084a5dac5072e1b1553436'),(111,'5691e885b495c1448314d4ee233306f3'),(112,'80fab5a3786d2274f53d7356f2bc2378'),(113,'7e9ccb22233a527c13a06610160d8f2b'),(114,'8a260e4d35478d7221787623142203d8'),(115,'437d5870f46d944cf8fa1b840c4818d1'),(116,'ad58bd705b050d514ac79b4a6299cc8e'),(117,'86c76ea142d2add91374b0737b1c0725'),(118,'161916783743dd6a7f895c4d5e7d5b96'),(119,'49d715e06de3409d73dacc8a4457d38e'),(120,'349f777b03afa796aedb96865759f0ef'),(121,'a5cda00ab1f4a2d78fe898d7ed8048a0'),(122,'332b0b63723697f7a329f68ec46bee61'),(123,'a47af6dcf5a48523edbe9be61483b95e'),(124,'da3edcd4349184056fd2297b59e6cd82'),(125,'457d19b1927a4926ae98c95ebbb539ec'),(126,'bbf692f3bca02b36653aa4895b2bfafc'),(127,'4073fb5c4de0a6c56e59eeb0041e8afe'),(128,'003e0ff88c123d8905fb56980927a498'),(129,'65e5bfbd814553b753c9b09e2c0ad512'),(130,'d5d5c3cb3e156ae194c5736c327043b9'),(131,'fe78fc15171028a763e9cae4b9901044'),(132,'2517aa790a7e3d37df6bda943de9a7b0'),(133,'42f8858cf64c7d8a831dd05354aacfcf'),(134,'599a04b670844bdef0a133558f1cec24'),(135,'6a4e9466fe47eeea9e713cdbe9ca54b9'),(136,'ef7396ad4c26890ceccd3d9c7f895035'),(137,'51f09ede452533d119807a7a3fb26ee4'),(138,'c8680bf31b409cf9f7bdcaefa54818d2'),(139,'f25ba1399963895945689e0d8badfe49'),(140,'bc0a93aa7982649b790bfb63165c4179'),(141,'afcb5471044d3357b46095c60be221f3'),(142,'1866b77d53fa36422f301ad53cca73dc'),(143,'5e86d8d8f0b82cdaa09bc64a6d4eaed8'),(144,'0e6262dd4d7496322476cb0d31bd71c9'),(145,'f3b56c7eff747128136be10d38903561'),(146,'852b33fa64a9cfeda2fabcf4025dd15c'),(147,'e78095cfad814ac0cea4a5ad7cdf7ab8'),(148,'13b1654de0f54274fe629816aff74164'),(149,'b44b0cbfd0db4ae77ae0250903152049'),(150,'fc2581965efd97a03dc33663731ecbb6'),(151,'5396cbe3eb306f17b03b5cf83ce340d3'),(152,'d263ed50c54ef26f0ef815ee353e8b8d'),(153,'197e6fb9dacfeb1a9d6b2dd6d68a838a'),(154,'9da7eedf0f217baa7880d397a2f12366'),(155,'bd0b595d68dd35006569a3b9bcbee3fa'),(156,'caeeb9a10ebcb1b7570fded2674a3b92'),(157,'fc5e9fcf2fd9a73d44f18967f73a9f3d'),(158,'1c412daacc313c49c553f1155b981277'),(159,'cc3adb08ecbcdbeb4188df653e0cd428'),(160,'2c972ae54e5cf05f98bea025ede71831'),(161,'a8df5275c5c04d049eda916d55149e0d'),(162,'769dd60b6b15aca0ce4a2ab86a7e069c'),(163,'fd7178d06a3fb6ae01686ac16c44ced6'),(164,'8cf3bea4ab65569a45bc41f3a04bdc4c'),(165,'fa609fbf402852aaa2a2c8c98245e7b2'),(166,'e770143dd283b6fc20ae2f5db5e956c2'),(167,'361e75e4c946d4167aae78e4c14a970b'),(168,'93b3681f0cdcdd650099585fafe8c2a7'),(169,'930a62e8540b6f9a8415990949e86c4b'),(170,'effd92b9d11fcfc0cb75abcf6a947c96'),(171,'cbb6b7e85534d62ff4be5e6a08a14dfa'),(172,'fd8f1615dbc4ea0eecd3d9a16833a732'),(173,'6e536f2e9aaa9dc2fc1bed1ce92ac678'),(174,'f7f285747d862a6be57a48fb8be85b59'),(175,'9730b60e5bd248341117b333ab8b1dca'),(176,'f20f71c7b872ef30d90ae181ce9e752e'),(177,'82f37beb97a6c93fc04401e308959443'),(178,'dce9912db0978bdd8e45c78f6f403333'),(179,'c6c9c86ed1d64d3b8f71bee36b915e4c'),(180,'9bf5529400c6448f7b4c93bdd8742383'),(181,'48bddfbbf799e9427f07ae2c0fec2b7a'),(182,'8ddc8057e2c0325fbe42c91da2098b8b'),(183,'2c1884058b7dbe9fc9beaddfff57bfdf'),(184,'179022cf1f3cfc5bdea211816150bbab'),(185,'3ac244afc5662d12eeb49e6a1cb9ea60'),(186,'3ed9ce1d1339d1f67207427617b77e33'),(187,'47f25294ab6457f3cc75ff5bfea4aba9'),(188,'d412b17d9ad33a8920ad0f217d9f3fad'),(189,'f40bf0b8c1617ed40c15d8c2584aba6d'),(190,'87ff8b9b47a10389b7f03c1a52de88f8'),(191,'88e24f3ce5aa9499e30e42250408b1c4'),(192,'8f66fb519d0c6707caa6da1ff057e374'),(193,'6e6b8270464ea6ddb98d8fd85e1eac8e'),(194,'0efa5d88e0b45f77b6efd6b19dadbaaf'),(195,'e21bb4ba870b9011a369d716557970a6'),(196,'3a0f1c38bdfb8fbdf4eb6083a4577b84'),(197,'fac6d48e9defe910c010a5cfd33d0385'),(198,'ea60e1928eacd713474d85c4f93ff1df'),(199,'6f3bfad185ebb45c3831d765214cb28e'),(200,'a178b22da35b936dbc5b21faf2fdf103'),(201,'5a96393c9caa19a9b37072bcd4c9ed73'),(202,'7fd41f14b255235949ff7c487c9896dc'),(203,'1a91cb788e8a37d0a5d791dc6a2053c8'),(204,'9e3ed630fdb8655312b9d517ae96ca7c'),(205,'43307afdb21a71326c5301b74ce08e64'),(206,'2b50f3da15a5e0bda8942832235a205a'),(207,'8046f05c67381cafe2657b0f557e338d'),(208,'9deabf98e647910a88c2799a50271bc2'),(209,'12cbd35afc84dd28a75764f4ccc39229'),(210,'d80a072debfd9fa3fb7c8da3b0fb7e78'),(211,'d02195e180e0c6329f0586a3b8fcd1f2'),(212,'b56390739bec538ac472be73f9f18597'),(213,'cdf431b1126a8a00e36c18ae2873af70'),(214,'31124be36f23e8713dcf46161bdd0a79'),(215,'c87a470f3748203fcb161a7a3e5dc1c4'),(216,'b48ec82fbc6f8a3739e6471c1f72a7c9'),(217,'66370edeef236d93342141d241998cae'),(218,'0932fc4596ba8ce5309d28edb4085bf5'),(219,'3b3bb12f6e0e0d3269410c75db4a31c5'),(220,'a1e685a24b64a749496036534ec19a4a'),(221,'97c625f68c1d95db81a05ca99649552e'),(222,'be1e03e0229359c40ef483cb21cf3e86'),(223,'4943de0c04a3d0c08ae54a4ce8709c18'),(224,'c4e53c7bc84c323bdb211f0061a82b04'),(225,'e1bd94a318b4a037848ac0856f712400'),(226,'cc03b252f8db93c111b0336815bfa82a'),(227,'fd469d7ab5459bd54be33e3126aca581'),(228,'3cf9e9ead722d13aa503355c8fb15258'),(229,'2656d8d0359cf97ef1d489a925a7e5d3'),(230,'e4588e93cfaa53d18a145eaf0c745729'),(231,'1efbc05c2949e88839867629193064ec'),(232,'60d82a71c7faf7b6d141b26c52307f96'),(233,'9ef996ff7a895a31a69913aca5fe9d92'),(234,'64a83b04fcf18af4e49ed9657b235ae3'),(235,'7903f778b7211f6c4fed0a35f73947ce'),(236,'537cdd3f799f207333f25cf4f97fdab7'),(237,'5b5798e069454466facc15f326b740a6'),(238,'79e791931abb5a2471a5ca7000e52eda'),(239,'1bea7f7e2adc80f1c26e70c31043fd42'),(240,'5c5085f8ee120ebfa211846bc462f88d'),(241,'98e617783b714dbe776f676cbdd4e62d'),(242,'4f72917517c3c50dd2b5b817e0ead1a7'),(243,'69508e9583b4f2df363027fdcb01bf65'),(244,'1e34e24c8bae574720d4f52f2af09da7'),(245,'4937312ead66204d292d1dd3b5c640a5'),(246,'453304ba40eeaa655d45cacc37f4316a'),(247,'beaf92624c4e05ac1401e40d35054f6f'),(248,'e136294f9eb5a5ec17068c25bb15598f'),(249,'0b54a794ab37c92f30779db600b53bcf'),(250,'671d6b7f4f48035cdeafc9fdd8a04e28'),(251,'95b1ea14a9abb719c0358e1d5e399ba3'),(252,'033cdc0a5a1d67055dc3800da5a90274'),(253,'f88c4bb69c0b763165f10de2842c734c'),(254,'0d7087cfc992b8f4aa01f34e31c1f0bc'),(255,'b425928c345fc7c9a9fb0054315dc41f'),(256,'cbbbaf37c126ee900ec1d19777b1f0d7'),(257,'e30c0949f1b07dddb945cfdc7484c2ad'),(258,'afe48d4d1232fcc78b0525f852e8ed37'),(259,'68e5ea9e75c9c79d3ae7190fdcad8340'),(260,'f8a8f03c6da6d76f796d8183c682ac56'),(261,'b95a049f2531d9ed607dda4791d5fa5d'),(262,'d845c5a43afa805f96ad4a11ae893d6b'),(263,'7d4aa6b31d1d766e328912c5793fa5fa'),(264,'8fa311f6eb6496a457a8139216ff4341'),(265,'945dabd84856c68a8f6f1d55727789e8'),(266,'0a15256b9e9712f4dedae197aac64f5c'),(267,'ef57ce7c472271a6bf0d1969c7091e45'),(268,'8ef43a10d7f48f621100d496b3f0d83b'),(269,'0b8036e33431dbcd94082c168a71d451'),(270,'df486389913aba5d4fb0cb0777020a02'),(271,'963ea1e0267c1970e765539a6a4c8f3b'),(272,'316c10adae70d1e3073bd5cad9499a7c'),(273,'8d0e84127d2d6f398cb97839998f1741'),(274,'78c7ce917ebe45c37753db854db3cb23'),(275,'d6250678c5786c0ed6f8745131bad97e'),(276,'fa260d76235be3061528765b3a3c1c46'),(277,'860ba8f511587d4fbae20cd30055bc0e'),(278,'f7af55772af062e93fab52aeb524baba'),(279,'6e632f7898ac04002321726c8afceb6d'),(280,'e3f34173ff50f806bb6b90dbeb8a7ae9'),(281,'b793885b85baeb21f9c1657b51808116'),(282,'41179000552146940062c6a577a2b3b0'),(283,'fb074ead388372178cbaf461c4b38c06'),(284,'3690f1234ca969b2fe416a8ec2337fc5'),(285,'09cf3030f537c09ad3ccf18f6b72304f'),(286,'8790cd2fcc6079a22f3c875ae87cbeef'),(287,'e74a0dbb5c0a016ef8158dc71d93931a'),(288,'7c620021c6d9e9370e1ea35e6b8f3cca'),(289,'a3d9f4051ea51313296630dcfced3dc7'),(290,'a31c6d6d7d5d928f11e4e52f3eaba860'),(291,'d67adf7c0fb5550f8772dace892ec7d1'),(292,'098507a2726825121daec2989719b603'),(293,'9346cec898d2237bd69c740ffd783541'),(294,'768bd6181a416d0269c84d1bb47a9238'),(295,'230b6970bfddb13e0297cbeebb9c7ff4'),(296,'a01876df12cee8bd9037ca32cf0ef74e'),(297,'85ccf3dc48d8afad1ed07479a164001a'),(298,'cd89e67753d85d3b524e9a25c521dcee'),(299,'40bfe9634afc6f2c99c0f285548b8b6c'),(300,'fd779dd65adac87ec37fdac2e9310cb1'),(301,'fa415fad549b61a7cd2ae42efa2c7fa7'),(302,'f7a7407ceb0cc27af89c681a583e473c'),(303,'b88b4d89cb3ed804a2fa8bc1f42f9b36'),(304,'607e12086d15779d5539d6ce2f6babe0'),(305,'927ea154b25426119bc01ad5bf6270e4'),(306,'eda55eda116be07f0947f00f812afb91'),(307,'db7534ee606e220cfb2931d2252258ce'),(308,'fe167526e11ed2678639045171c19ac4'),(309,'a2ffa37996fb5370a2de7655e6e3bb24'),(310,'5483e38dfd07db620788ae456ecf856b'),(311,'dbb6b504fdc9318bb4e982cdffc08f0f'),(312,'e7a1c0d0ea3402a7d41b341dc38d2329'),(313,'2a7b11239d8749b3d6929b329d5fff37'),(314,'01c5993b84564441fe6295c6f46c8047'),(315,'a186f0923f8a7e9e675b4ac19934f9e5'),(316,'b7175430d426c3b1a52437fda0f11d38'),(317,'f5bcd2295ae30f4354d52e6325925277'),(318,'dbdd23ca1efb866a71736c23c4efb89b'),(319,'a514017e1009178843c131134995e317'),(320,'af698b567d742cae25ca820b248bd9e5'),(321,'3a1aad0699233d726b6ea9849f9c2eb5'),(322,'5cda64ad40014307e46526022a4e50f4'),(323,'6b4a8d39095c54dcb9466e9c85f3aa48'),(324,'4717b85d356575098b4983cfed35576c'),(325,'c61416c60bda91b7008908d21f42f265'),(326,'6202e45c98e47fbc0ac25b5226fb77de'),(327,'fa9257e3b96cf55d323aaa8042a33b01'),(328,'20fdf9085c13b7dfb63ab54ba4e1ed02'),(329,'d26cb7c069c6f4f8e6d2eb178c1e8da6'),(330,'045f869d6dc1e09dd434b0a42b57905d'),(331,'8872a256a48101de3572bc816b9c32e5'),(332,'8d376a8679a04855104d8275148c1588'),(333,'c9c7fac08707f744ec49bbfb5445c553'),(334,'649753f31d468eddda9c94f019e67614'),(335,'389157b7b28c5f6c0f63d7cae8917c44'),(336,'15dc13ecba1cc98d94f5aeaced00c2af'),(337,'3f24c581745373c4ecadf1e54515c006'),(338,'4af75b568be89ca19572c1923bfe24b8'),(339,'4a8c28a47e679ef7576397a6917b9224'),(340,'e2cfa6756c793f3f08b24cb42ea26a13'),(341,'4911e831573c967cfdb3b607c979bdf3'),(342,'d1fbf33d85532744253eeb512305fc81'),(343,'142d22ebec60cb697e3fb5e66d7097ad'),(344,'2c610588ea5de5b1dc00c3129e81174d'),(345,'7e367d1eff18adf342ea353834cd83f2'),(346,'ddceea95c129f869f321def833d63281'),(347,'accc5a7742fab1e2fb949d0c13f3c938'),(348,'e0ac84c076c58cd4dc99acc7caf8bb3a'),(349,'aea95e715b191da82817b36e79a162ad'),(350,'6e77808f453a47cd721c2cbdbe06235f'),(351,'a5d795ba841951768fc4bb015c81cc46'),(352,'23e75470e1c9899421b3a7a1e446346d'),(353,'cddc4d37badda04043b581d50b5c2b0e'),(354,'3e6908de72adebfd1d16abafd873234e'),(355,'e24714dd9563a5ed4a84dcc8d1c6904a'),(356,'08fc37e01e583f4222bcaa1138d7f98c'),(357,'9d7f80904fac62098daa48203f619a4e'),(358,'d15a507835ee68cef12b6f54062c9605'),(359,'dabd8fe1d03233db3ec47c77874c8482'),(360,'f13b51e8f3b438e8d5ac8e202ca80973'),(361,'d8f3ec118df3703cfc0057d97a46abe8'),(362,'899e46d29adbc84b558d512ce777be55'),(363,'fe867cf0c65a91bb23e131e6e52d5b43'),(364,'e4bdef9dc5f13a2e5ddcad246858fb3e'),(365,'750c3b0b83e11b0f206b96dd1026d20a'),(366,'6838b8d0da4dc5ce467c088facd9f076'),(367,'4511aa3fec6e48d212197ec32040f5e5'),(368,'0e4013fae08cb7a10a3510fe047ba17e'),(369,'7d6fc762d8d14c294e174a0eb03e47c7'),(370,'da45befdb6c6be2dddaf3799119a1058'),(371,'50a2512446b6e02d8d4cd5203e2f0b24'),(372,'f46ec8071e51382e63eca7e2ad7bcd5a'),(373,'201b609b7af2b052cd74fd408efdf7d3'),(374,'074749d95dfa198e8396347292ec7dec'),(375,'93965532199ff976a13fb15db631005c'),(376,'fd5fd617bebddf8eacb4fbace8fa405b'),(377,'16b9dde45fcfeab0512a6b5e7ed9539e'),(378,'cba74fa1d4b82e715bb61e214104993a'),(379,'afcdb3091a3de2de2158d51d7a10ee12'),(380,'fc35ee363f55bc07f83a7d44d13a307d'),(381,'a2cbc6a11af19e3f854a3e0d63232408'),(382,'673a5d0b99ecac598a5608e37a0c1c2b'),(383,'c51bf7db6a7fb735634c11b0c7bee925'),(384,'0ea22bbf1249b3aa9d240e70ff7fc65a'),(385,'e862f153d0a58710b1e1fa65b5e0a28f'),(386,'5bc9b22891601a4ce3985aee5bdcbc2f'),(387,'d47ca1e35b149ed0cd09b6def1bae487'),(388,'85f5a21be8d0c5a1b8cfa804e7aa24da'),(389,'c77f7b2c3e51b65a34ae9a59844178a5'),(390,'414b33017c09fedee363b5e0ecc95d0f'),(391,'70dd844e4a8cffee9015340b7653dcf1'),(392,'833185a10770334d715483c7bcfc6f31'),(393,'0a2ac4706bbed6adc72100d4cd261786'),(394,'3f97b0e3722eaee1fcca8fac77dd9c69'),(395,'33c532d7d5c038823344d28d0a5e7fcc'),(396,'2bfed99659b77e2d0d69ff4d198b2dd8'),(397,'20d2598bfb8b8dcdecc1a570bb4290d2'),(398,'a94c4e497899641234dbe9d0257b7cd3'),(399,'2e6f0825b4042cb02407086e45200a25'),(400,'131e9bc95c963d745a28062bab416367'),(401,'fff64d81d5c96699197582f4c2c6b364'),(402,'e4e65de4d8f88cf8f5e616148c459d93'),(403,'c496bf125f896f92c2a9969763cbfe0f'),(404,'cdcd3db54d617ba10017093a17cc6e72'),(405,'00dd1a8ac41b3070fadf6240b30550b4'),(406,'915e048d4448b2dd16a367db52f056d1'),(407,'dc55806bab9afa3f13826be8646391f0'),(408,'b3596475ff1e6437a11f452c845454b2'),(409,'4217c07ab7a2f20e7f9037b699424147'),(410,'8cc56f01d18cc843ee426fa82015e998'),(411,'a9898b7c86c50ee539bf465d9d14c0c0'),(412,'95524a09ad1290e64d4c13b7ea4d6f36'),(413,'ef5a4702660a6acd12301306533e014e'),(414,'c2b7e3ee7da018e1b774c422baa6a319'),(415,'7fcde9ff4c1bb61c3d0897f5cd78348d'),(416,'2c9482e92e057a945b2b285352f8011b'),(417,'39ca8485b5567e1752e1911e92898cab'),(418,'19e0ae650cdb9b65ab99338e3ca9ea52'),(419,'3a09fbe971317cf852de7e2658131741'),(420,'ffc9c3b49ce89c71374b32b36ee46a45'),(421,'8921b37ec5db607508273b0aa6c567cc'),(422,'c442e9f63a9cf8eefded431a7e99597b'),(423,'05a0634f1ee48ae53d9d8ecb0fa24e46'),(424,'bff496f24dd88b003dcda56d13748339'),(425,'125c3d69887ac392d2a31d74aea44127'),(426,'6656709154c9f31971e94ca988bb04b7'),(427,'3addc5439943582056ffb98788349588'),(428,'d05682001c2e209dc377142dcf38b256'),(429,'7c030e962826b4ed2ca54aedc34e35e7'),(430,'02e248a5ffdf10847d1a302a86432397'),(431,'50646189b61341c4babf95b3dd1ee7b3'),(432,'fcee4d7b721fad80eeee7311da4834d5'),(433,'89cc2d39ddcf613c6cae9692656eb8f3'),(434,'ab40203aa7ef3f1ec9ac16f6e0a3881b'),(435,'a24a56ebb55c697b6c5a0a04186a9ea1'),(436,'f7d0314c11b1ea7fc2e26b7c21ca4f54'),(437,'366e4a12afd3c487e7dfef463ec0da65'),(438,'0cc175f8f7b12cdccd9aabe92c9952ee'),(439,'2160ae14d0f21b9afb8e29923cb43644'),(440,'ba51f54b4aac7b9f6edba6ce74fce1f4'),(441,'f337ac95afaf2f92c9cd6533ddf1fa4b'),(442,'4216d9dda72dfd03179f989cac06ac49'),(443,'12536b739762227b796e47f211d9fcbb'),(444,'24c9e593bf2e06c6475767190ab6b381'),(445,'6acbc6ae0c3566d3e9f1717481e2b861'),(446,'bc04ee147b9382523bd03d7a1f983aa0'),(447,'7e0e79d714ee28e0ccd710942e54f12c'),(448,'7a8765b1114bdfb4f0952e547a69c172'),(449,'692816d23bd28593d7f0ca4e17332be7'),(450,'15abfa2477e019ef530dabdaf6c4bceb'),(451,'5b849d0b2e106b0f56a7fd79d81e5516'),(452,'d000f0c45a692419499075c297106b2b'),(453,'2cfb4c1da2e9d10c11a0d4fb00cc7d96'),(454,'750f5c37c575e0a1d52b67267af07733'),(455,'abd998abfc070ef1fb3554ad4641d77f'),(456,'dfd964be41aee9672649600b803e1e71'),(457,'40772323467abb464672cc02fb9d8a59'),(458,'1890e88f37f3cf4d5ddc60a4631ba17a'),(459,'444fbd3e182175195e7c8af72d0c1eaf'),(460,'d33ba0602b6b5960a5f437c5c315be67'),(461,'3dc224129eeb70be6a6ce4759a305f7e'),(462,'dc470715fe9d2a5b457a0a9a6f39e513'),(463,'91d528165fb2b43a6553b9100cd8083a'),(464,'f6765cc9300d791eac45eb7e562c837e'),(465,'166b398d07663740cd468969f897226a'),(466,'0c9b9d9b438e40afc200faac99dfeb5d'),(467,'369b702442c13adb5d6fb922f0baed6f'),(468,'4fe2c1dcf57a9e7ed3bd798e9bbc5cf2'),(469,'19d414ae5c44fe68f0e8200b0e0561cd'),(470,'34982558aef77ef351955f69d04bb576'),(471,'3be40d35bd8da952523db53a67f5f8da'),(472,'4fb4aec468faa917fad519feba4cacd9'),(473,'9d263dafe73fc0786de7d784df300aa0'),(474,'c1f6fcc893489b40d18ee267eb2b896a'),(475,'da82e3265bc31a23fd0622a40d9057b0'),(476,'01fb0729b203990b6c49ff5090888920'),(477,'ec0462aa14eec502a8e2d8bb4f6091b3'),(478,'c04b870a54ef29294ba0384f8b011d98'),(479,'0e5f3f670dab82eab04eec812d35f9e3'),(480,'0a68ec44d3de0460afc85688542c9a06'),(481,'ee814bab3fcef449085cdc21dd1dfcc5'),(482,'efd7fd5fcd8a854d25e89c0e00f576fa'),(483,'13e3d84dd8cb95b537e55b552355630d'),(484,'164b490f5e88054c54f597c8304ea9fd'),(485,'e25db7f0294a3ca55aff37dfcfea8f5a'),(486,'0fe076d5cc57bcbd36540497ca218df4'),(487,'88ae947e06731c54966b1d5e80bbff54'),(488,'dc579217a7cca4f9337c423b4ad2a02a'),(489,'b3e18370aa01b371c1013a36f3dc7720'),(490,'78d2bf0b8ebcd2e652ad085b7b26d791'),(491,'56c9fb0eaee3a555cae2bd757c7a8fb7'),(492,'229b5139f4bc0710dd48b50cbab23f6e'),(493,'8dacf8cdbb51258491a73d32e450f964'),(494,'a0438b064af9a8a65127cc507fce040c'),(495,'cf6f531a8a06fff25d8d68666c81600f'),(496,'7e05e7ec22549d57ae4cc84e0c91983d'),(497,'3b916243e0a7197e39b13761bf66ab26'),(498,'78b53c5352c03a50937c9025b3cf591a'),(499,'da93fbda4616c528234ec7ed51fb7fbb'),(500,'0a86546e399db99806aa64f9a78edaf8'),(501,'32fabfbe4ae6a7e4fa5bbb6b6962efc9'),(502,'013fcb691fe86405e7403457add37960'),(503,'89e68033a8c79f2f791e617a2fd488a5'),(504,'ea3d9e9718cd2c8b4b03715459f1705b'),(505,'efcdee06f87e5391bc7560251de34195'),(506,'5b7dc2cf5baa38bc290e50dbe2fbfe94'),(507,'b3cdb6d4725d8078f6b3685117f4f4a3'),(508,'094e99b2ce2d4cd4fe1450d6756ce73c'),(509,'beb73e584515c78b6508119da4dbe84b'),(510,'46278c2845a8e2dd2366624b6c34e675'),(511,'9a42721d6e625318fab0429766d34479'),(512,'d469819c30441f996e186f5ca531f07b'),(513,'361a3d0ca0d1394e036a2ce327a2ec81'),(514,'49d9113cef826fe9403ae316a9a838f3'),(515,'956953e7d0e4f8bd218140f545eaf538'),(516,'264dcfe9e1d8e5bbfe9bba843582d8a5'),(517,'fc469dfcf5a4b5926862ea31675827df'),(518,'64abfb699e5bf12414ac8e2339e6cd0d'),(519,'88bd510536d66192629899e24d020638'),(520,'d9665a104ca3b3b453864995db101f3b'),(521,'a489c0a357c0be14c4a18467da2a38ac'),(522,'3ebeaf276891b4eaea9e5e6b39dfeaca'),(523,'ca73774a0b475880d9f8abe61ae651a0'),(524,'6bd43162bc06f2134ec2947336840356'),(525,'eef34725945ec4b70c08c26a22ba91b9'),(526,'156ae87153a387e4ef7aa901915c7db3'),(527,'2b0351badf710fbe4227bba2d54865fa'),(528,'30e884c682c044243c602337f6faa4f1'),(529,'10ed817214bf890de32bf5656119d448'),(530,'1e5797f7ab7c500c0f286e9ccc1d6fb3'),(531,'caad4e35fce9b0314708e97a8913f054'),(532,'5be6a86b1894fede0095403c5fbf57c2'),(533,'e13a0f2c11fee90d25339ecc9b687e37'),(534,'21939d62390f9d625ce3ff2448d04d8b'),(535,'26a530e6bd4e218ace8996649cb6774a'),(536,'b336262185d5159d02f32ac803c28dfd'),(537,'62c60b1d0fdbd155ed57555266c4ea86'),(538,'db06ea50d7815ee92e55f3f570868022'),(539,'2109a289f74c512c924017ec8b0ab742'),(540,'e24953e31c2f5bfb3043514079cbadb3'),(541,'b237b098eb9e81f7ce7cc5faca02a5da'),(542,'790a56a5e4e85ae58ca4d25c6000dbbe'),(543,'66b2380e941494033ee36ca032e485bc'),(544,'7e2e65e16daeea3878edbc1b30f73040'),(545,'c3999a35b35b048de2efd82c76a69433'),(546,'4cefff0a04bdb9536dce4c03919c93bd'),(547,'b3eb063ffe0e545a53723c796876fd47'),(548,'13521940f0af6c14e314fc6d2fae2058'),(549,'813d8ead9afbe69a4eafd901b9075ec5'),(550,'90f9abf13900154cc9894350a52cbe51'),(551,'910e807d0319ae63d06e8ee202f8ffc3'),(552,'c411c13c83d8ad88b8008931d47455fa'),(553,'09680df24c4d322ab699471119e53cd2'),(554,'29dcc77fd41e81213f606b00b79ca43a'),(555,'d66d65bb311616ccbb49e9ce93c45182'),(556,'48fd18fbd7cf461b613dd441c36c9fb1'),(557,'1e0280b5b989724cebcbc67f0ec0ea46'),(558,'e5a1f8899025a61632e772e7b7786b15'),(559,'321075408630adf6defbe912875fb13f'),(560,'cbd59d8df5ed9e1c95ce4285872c8891'),(561,'ea3cebba8fe8dd09a89556270c060b7f'),(562,'6b563ff7843bef53a05e687f776701dc'),(563,'419c838d4400c9a05ac4f3eb96cd49e5'),(564,'cfd2dedd2145b89c6e7acc37c8e23869'),(565,'75ca8c5cd4166c5cf3ae52133b3d3e78'),(566,'c47fd8fc6d64ea2910b7130bdd6639ed'),(567,'163eb37d5ce43edf0b0e9ad009ae757f'),(568,'3fa8b3199b24f0bb56e49a3a19d5c9d9'),(569,'cdd73d9922deffe21deb4f76e65a21b7'),(570,'3a13c2a657eec84c831ea5259326da36'),(571,'81fd738d687d7b48ef30fc197efd6d3c'),(572,'afde496c930f489d16503f40d5f31a7a'),(573,'c8eeb1d5fa83f8d3b3a8537916c06f57'),(574,'ddb338217698e683841266b4c9b8cf1e'),(575,'060a7400e9bacaeb51a7ceb6f5f7ccf2'),(576,'4377431b089dfdba4a15f27191fc4e83'),(577,'6d689cf92122428b92ac5e87d79b1e9e'),(578,'ee595849a043d3b54654756e94a6693b'),(579,'ef70dd89493a9aa6aa2717a72007da8c'),(580,'dd092d4ee28f47a5ad2a12e6eddd4b77'),(581,'5dd7080f6c8a7bcac39711f27dc6e2b7'),(582,'d3dcc07c0da1cd5ba3ff90f3c8478653'),(583,'65fbe031a96dca77dcb9857d06ad534f'),(584,'c0dd450af905935c2ea11357ea02c6d8');
/*!40000 ALTER TABLE `checkaltertable` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-12-07  8:56:21
