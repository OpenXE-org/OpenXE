<?php
// Nur einfache Fehler melden
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include_once("../conf/main.conf.php");
include_once("../phpwf/plugins/class.mysql.php");
include_once("../www/lib/class.erpapi.php");

class app_t {
  var $DB;
  var $user;
}

$app = new app_t();

$DEBUG = 0;


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
$erp = new erpAPI($app);

$vorlage['adresse'] = array('name','vertrieb','innendienst','sponsor','projekt','kundennummer','lieferantennummer','usereditid','plz','email');
$vorlage['artikel'] = array('projekt','usereditid','nummer','adresse');

$vorlage['auftrag'] = array('projekt','adresse','vertriebid','gruppe','usereditid','status','datum','belegnr','gesamtsumme','transaktionsnummer','internet');
$vorlage['auftrag_position'] = array('auftrag','artikel');

$vorlage['angebot'] = array('projekt','adresse','vertriebid','gruppe','usereditid','status','datum','belegnr');
$vorlage['angebot_position'] = array('angebot','artikel');

$vorlage['rechnung'] = array('projekt','adresse','vertriebid','gruppe','usereditid','auftragid','status','datum','belegnr','soll','zahlungsstatus','provdatum');
$vorlage['rechnung_position'] = array('rechnung','artikel');

$vorlage['gutschrift'] = array('projekt','adresse','vertriebid','gruppe','usereditid','status','datum','belegnr');
$vorlage['gutschrift_position'] = array('gutschrift','artikel');

$vorlage['lieferschein'] = array('projekt','adresse','vertriebid','auftragid','land','usereditid','status','datum','belegnr');
$vorlage['lieferschein_position'] = array('lieferschein','artikel');

$vorlage['objekt_protokoll'] = array('objektid','objekt','action_long');

$vorlage['versand'] = array('lieferschein','projekt','adresse');

$vorlage['bestellung'] = array('projekt','adresse','usereditid','status','datum','belegnr');

$vorlage['bestellung_position'] = array('bestellung','artikel');
$vorlage['lager_platz_inhalt'] = array('lager_platz','artikel');
$vorlage['einkaufspreise'] = array('artikel','adresse','projekt','bestellnummer','bezeichnunglieferant');
$vorlage['verkaufspreise'] = array('artikel','adresse','projekt');
$vorlage['lager_bewegung'] = array('artikel','adresse');
$vorlage['eigenschaften'] = array('artikel');
$vorlage['stueckliste'] = array('artikel');

$vorlage['userrights'] = array('user');

$vorlage['adresse_rolle'] = array('adresse','projekt');

$vorlage['ticket'] = array('warteschlange','schluessel','status','kunde');
$vorlage['ticket_nachricht'] = array('ticket','status','zeit','verfasser');

$vorlage['emailbackup_mails'] = array('webmail','adresse','checksum');
$vorlage['kontoauszuege'] = array('konto','gegenkonto','pruefsumme','fertig','haben','buchung','waehrung');

$vorlage['kontoauszuege_zahlungsausgang'] = array('adresse','parameter','kontoauszuege');
$vorlage['kontoauszuege_zahlungseingang'] = array('adresse','parameter','kontoauszuege');

$vorlage['ansprechpartner'] = array('adresse');
$vorlage['zeiterfassung'] = array('adresse_abrechnung','abgerechnet','abrechnen','adresse');

$vorlage['mlm_baum_cache'] = array('mlm_abrechnung','lizenznehmer','aktiv','qualifiziert');
$vorlage['mlm_downline'] = array('downline');
$vorlage['mlm_positionierung'] = array('adresse');

$vorlage['abrechnungsartikel'] = array('adresse');
$vorlage['dokumente_send'] = array('adresse');

$vorlage['kasse'] = array('adresse');
$vorlage['lieferadressen'] = array('adresse');


/*
$vorlage['angebot'] = array('belegnr','adresse');
$vorlage['angebot_position'] = array('angebot','artikel','projekt','nummer');
$vorlage['bestellung_position'] = array('bestellung','bestellung','artikel','abgeschlossen','status','geliefert','menge');
$vorlage['datei_stichwoerter'] = array('parameter','subjekt','subjekt','subjekt');
$vorlage['datei_version'] = array('datei');
$vorlage['datev_buchungen'] = array('kontoauszug');
$vorlage['emailbackup'] = array('adresse');
$vorlage['emailbackup_mails'] = array('subject','checksum','spam');
$vorlage['gutschrift'] = array('status','datum');
$vorlage['kontoauszuege_zahlungsausgang'] = array('kontoauszuege');
$vorlage['kontoauszuege_zahlungseingang'] = array('kontoauszuege');
$vorlage['lager_platz'] = array('kurzbezeichnung');
$vorlage['lager_platz_inhalt'] = array('artikel');
$vorlage['lager_reserviert'] = array('adresse','adresse','artikel');
$vorlage['lieferadressen'] = array('adresse');

$vorlage['lieferschein_position'] = array('lieferschein','artikel','projekt');
$vorlage['produktion_position'] = array('artikel','produktion','geliefert','geliefert');
$vorlage['projekt'] = array('abkuerzung');
$vorlage['rechnung'] = array('auftragid','belegnr','adresse','kundennummer','status','datum');
$vorlage['rechnung_position'] = array('artikel','projekt','rechnung');
$vorlage['service'] = array('nummer');
$vorlage['ticket'] = array('schluessel');
$vorlage['ticket_nachricht'] = array('ticket');
*/

foreach($vorlage as $tabelle=>$fields)
{
  $tmp = $app->DB->SelectArr("SHOW INDEX FROM `".$tabelle."`");
  unset($geloeschte_indizies);
  unset($vorhanden_indizies);
  $geloeschte_indizies = array();
  $vorhanden_indizies = array();

  for($i=0;$i<count($tmp);$i++)
  {
    //[Key_name] => auftrag_63
    if($tmp[$i]['Key_name'] != "PRIMARY" && !in_array($tmp[$i]['Key_name'], $geloeschte_indizies) && !in_array($tmp[$i]['Key_name'], $vorlage[$tabelle]))
    {
      $sql = "ALTER TABLE  `".$tmp[$i]['Table']."` DROP INDEX  `".$tmp[$i]['Key_name']."`";
      echo $sql."\r\n";
      $app->DB->Update($sql);
      $geloeschte_indizies[]=$tmp[$i]['Key_name'];
    } else {
			$vorhanden_indizies[] = $tmp[$i]['Key_name'];
		}
  }

  for($j=0;$j<count($vorlage[$tabelle]);$j++)
  {
    $sql = "ALTER TABLE `".$tabelle."` ADD INDEX (`".$vorlage[$tabelle][$j]."`)";
		if(!in_array($vorlage[$tabelle][$j], $vorhanden_indizies))
		{
    	echo $sql."\r\n";
    	$app->DB->Update($sql);
		} else {
    	echo "VORHANDEN ".$sql."\r\n";
		}
  }
}

$tables = array('abrechnungsartikel', 'accordion', 'adapterbox', 'adapterbox_log', 'adresse', 'adresse_accounts', 'adresse_import', 'adresse_kontakhistorie', 'adresse_kontakte', 'adresse_rolle', 'aktionscode_liste', 'anfrage', 'anfrage_position', 'anfrage_protokoll', 'angebot', 'angebot_position', 'angebot_protokoll', 'ansprechpartner', 'arbeitsfreietage', 'arbeitsnachweis', 'arbeitsnachweis_position', 'arbeitsnachweis_protokoll', 'arbeitspaket', 'artikel', 'artikel_artikelgruppe', 'artikel_permanenteinventur', 'artikel_shop', 'artikeleigenschaften', 'artikeleigenschaftenwerte', 'artikeleinheit', 'artikelgruppen', 'artikelkategorien', 'artikelkontingente', 'aufgabe', 'aufgabe_erledigt', 'auftrag', 'auftrag_position', 'auftrag_protokoll', 'autorechnungsdruck', 'autorechnungsdruck_rechnung', 'autoresponder_blacklist', 'backup', 'belege', 'bene', 'berichte', 'bestellung', 'bestellung_position', 'bestellung_protokoll', 'cache', 'cache_felder', 'calendar', 'chargenverwaltung', 'chat', 'datei', 'datei_stichwoerter', 'datei_version', 'datev_buchungen', 'dentalunion_io_dateien', 'dentalunion_io_dateien_status', 'dentalunion_jobs', 'dentalunion_logs', 'dentalunion_logs_status', 'dentalunion_zahnfarben', 'dentalunion_zahnformen', 'dentalunion_zahnhersteller', 'dentalunion_zahnsorten', 'device_jobs', 'dokumente', 'dokumente_send', 'drucker', 'drucker_spooler', 'dta', 'dta_datei', 'dta_datei_verband', 'eigenschaften', 'einkaufspreise', 'emailbackup', 'emailbackup_mails', 'etiketten', 'event', 'event_api', 'exportlink_sent', 'exportvorlage', 'firma', 'firmendaten', 'geschaeftsbrief_vorlagen', 'gpsstechuhr', 'gruppen', 'gutschrift', 'gutschrift_position', 'gutschrift_protokoll', 'importvorlage', 'importvorlage_log', 'inhalt', 'interne_events', 'inventur', 'inventur_position', 'inventur_protokoll', 'jqcalendar', 'kalender', 'kalender_event', 'kalender_temp', 'kalender_user', 'kasse', 'kasse_log', 'konfiguration', 'konten', 'kontoauszuege', 'kontoauszuege_zahlungsausgang', 'kontoauszuege_zahlungseingang', 'kontorahmen', 'kostenstelle', 'kostenstelle_buchung', 'kostenstellen', 'kundevorlage', 'lager', 'lager_bewegung', 'lager_charge', 'lager_differenzen', 'lager_mindesthaltbarkeitsdatum', 'lager_platz', 'lager_platz_inhalt', 'lager_reserviert', 'lager_seriennummern', 'lagermindestmengen', 'layoutvorlagen', 'layoutvorlagen_positionen', 'lieferadressen', 'lieferantvorlage', 'lieferschein', 'lieferschein_position', 'lieferschein_protokoll', 'linkeditor', 'logdatei', 'logfile', 'mitarbeiterzeiterfassung', 'mitarbeiterzeiterfassung_sollstunden', 'mlm_abrechnung', 'mlm_abrechnung_adresse', 'mlm_abrechnung_log', 'mlm_baum_cache', 'mlm_downline', 'mlm_positionen', 'mlm_positionierung', 'mlm_wartekonto', 'mlmpositionierung', 'module_lock', 'module_lock_', 'newsletter_blacklist', 'newslettercache', 'objekt_protokoll', 'offenevorgaenge', 'paketannahme', 'paketdistribution', 'partner', 'partner_verkauf', 'pdfmirror_md5pool', 'pinwand', 'pinwand_user', 'pos_kassierer', 'pos_order', 'pos_sessions', 'produktion', 'produktion_position', 'produktion_protokoll', 'produktionslager', 'projekt', 'projekt_inventar', 'protokoll', 'provisionenartikel_abrechnungen', 'provisionenartikel_abrechnungen_provisionen', 'provisionenartikel_provision', 'prozesslock', 'prozessstarter', 'rechnung', 'rechnung_position', 'rechnung_protokoll', 'reisekosten', 'reisekosten_position', 'reisekosten_protokoll', 'reisekostenart', 'rma', 'rma_artikel', 'seriennummern', 'service', 'shopexport', 'shopexport_kampange', 'shopexport_status', 'shopimport_auftraege', 'shopnavigation', 'stechuhr', 'stueckliste', 'stundensatz', 'systemlog', 'textvorlagen', 'ticket', 'ticket_nachricht', 'ticket_vorlage', 'uebersetzung', 'umsatzstatistik', 'unterprojekt', 'user', 'useronline', 'userrights', 'uservorlage', 'uservorlagerights', 'ustprf', 'ustprf_protokoll', 'verbindlichkeit', 'verkaufspreise', 'verrechnungsart', 'versand', 'versandpakete', 'vertreterumsatz', 'waage_artikel', 'warteschlangen', 'webmail', 'webmail_mails', 'webmail_zuordnungen', 'wiedervorlage', 'wiedervorlage_protokoll', 'wiki', 'zahlungsavis', 'zahlungsavis_gutschrift', 'zahlungsavis_rechnung', 'zeiterfassung', 'zertifikatgenerator', 'zwischenlager');

foreach($tables as $key => $table)
{
  if($query = $app->DB->SelectArr("SHOW CREATE TABLE ".$table))
  {
    if($query[0]['Create Table'])
    {
      if(strpos($query[0]['Create Table'],'ENGINE=MyISAM'))
      {
        $sql = "ALTER TABLE ".$table." ENGINE = InnoDB";
        $app->DB->Update($sql);
        echo $sql."\r\n";
      }
    }
  }
}


?>
