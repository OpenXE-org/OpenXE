<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Produktion {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "produktion_list");        
        $this->app->ActionHandler("create", "produktion_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "produktion_edit");
        $this->app->ActionHandler("delete", "produktion_delete");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "produktion_list":
                $allowed['produktion_list'] = array('list');
/*
Produktion
Kd-Nr.
Kunde
Vom
Bezeichnung
Soll
Ist
Zeit geplant
Zeit gebucht
Projekt
Status
Monitor
Menü
*/

//                $heading = array('','','datum', 'art', 'projekt', 'belegnr', 'internet', 'bearbeiter', 'angebot', 'freitext', 'internebemerkung', 'status', 'adresse', 'name', 'abteilung', 'unterabteilung', 'strasse', 'adresszusatz', 'ansprechpartner', 'plz', 'ort', 'land', 'ustid', 'ust_befreit', 'ust_inner', 'email', 'telefon', 'telefax', 'betreff', 'kundennummer', 'versandart', 'vertrieb', 'zahlungsweise', 'zahlungszieltage', 'zahlungszieltageskonto', 'zahlungszielskonto', 'bank_inhaber', 'bank_institut', 'bank_blz', 'bank_konto', 'kreditkarte_typ', 'kreditkarte_inhaber', 'kreditkarte_nummer', 'kreditkarte_pruefnummer', 'kreditkarte_monat', 'kreditkarte_jahr', 'firma', 'versendet', 'versendet_am', 'versendet_per', 'versendet_durch', 'autoversand', 'keinporto', 'keinestornomail', 'abweichendelieferadresse', 'liefername', 'lieferabteilung', 'lieferunterabteilung', 'lieferland', 'lieferstrasse', 'lieferort', 'lieferplz', 'lieferadresszusatz', 'lieferansprechpartner', 'packstation_inhaber', 'packstation_station', 'packstation_ident', 'packstation_plz', 'packstation_ort', 'autofreigabe', 'freigabe', 'nachbesserung', 'gesamtsumme', 'inbearbeitung', 'abgeschlossen', 'nachlieferung', 'lager_ok', 'porto_ok', 'ust_ok', 'check_ok', 'vorkasse_ok', 'nachnahme_ok', 'reserviert_ok', 'bestellt_ok', 'zeit_ok', 'versand_ok', 'partnerid', 'folgebestaetigung', 'zahlungsmail', 'stornogrund', 'stornosonstiges', 'stornorueckzahlung', 'stornobetrag', 'stornobankinhaber', 'stornobankkonto', 'stornobankblz', 'stornobankbank', 'stornogutschrift', 'stornogutschriftbeleg', 'stornowareerhalten', 'stornomanuellebearbeitung', 'stornokommentar', 'stornobezahlt', 'stornobezahltam', 'stornobezahltvon', 'stornoabgeschlossen', 'stornorueckzahlungper', 'stornowareerhaltenretour', 'partnerausgezahlt', 'partnerausgezahltam', 'kennen', 'logdatei', 'bezeichnung', 'datumproduktion', 'anschreiben', 'usereditid', 'useredittimestamp', 'steuersatz_normal', 'steuersatz_zwischen', 'steuersatz_ermaessigt', 'steuersatz_starkermaessigt', 'steuersatz_dienstleistung', 'waehrung', 'schreibschutz', 'pdfarchiviert', 'pdfarchiviertversion', 'typ', 'reservierart', 'auslagerart', 'projektfiliale', 'datumauslieferung', 'datumbereitstellung', 'unterlistenexplodieren', 'charge', 'arbeitsschrittetextanzeigen', 'einlagern_ok', 'auslagern_ok', 'mhd', 'auftragmengenanpassen', 'internebezeichnung', 'mengeoriginal', 'teilproduktionvon', 'teilproduktionnummer', 'parent', 'parentnummer', 'bearbeiterid', 'mengeausschuss', 'mengeerfolgreich', 'abschlussbemerkung', 'auftragid', 'funktionstest', 'seriennummer_erstellen', 'unterseriennummern_erfassen', 'datumproduktionende', 'standardlager', 'Men&uuml;');
                $heading = array('','','Produktion','Kd-Nr.','Kunde','Vom','Bezeichnung','Soll','Ist','Zeit geplant','Zeit gebucht','Projekt','Status','Monitor','Men&uuml;');

                $width = array('1%','1%','10%'); // Fill out manually later

//                $findcols = array('p.datum', 'p.art', 'p.projekt', 'p.belegnr', 'p.internet', 'p.bearbeiter', 'p.angebot', 'p.freitext', 'p.internebemerkung', 'p.status', 'p.adresse', 'p.name', 'p.abteilung', 'p.unterabteilung', 'p.strasse', 'p.adresszusatz', 'p.ansprechpartner', 'p.plz', 'p.ort', 'p.land', 'p.ustid', 'p.ust_befreit', 'p.ust_inner', 'p.email', 'p.telefon', 'p.telefax', 'p.betreff', 'p.kundennummer', 'p.versandart', 'p.vertrieb', 'p.zahlungsweise', 'p.zahlungszieltage', 'p.zahlungszieltageskonto', 'p.zahlungszielskonto', 'p.bank_inhaber', 'p.bank_institut', 'p.bank_blz', 'p.bank_konto', 'p.kreditkarte_typ', 'p.kreditkarte_inhaber', 'p.kreditkarte_nummer', 'p.kreditkarte_pruefnummer', 'p.kreditkarte_monat', 'p.kreditkarte_jahr', 'p.firma', 'p.versendet', 'p.versendet_am', 'p.versendet_per', 'p.versendet_durch', 'p.autoversand', 'p.keinporto', 'p.keinestornomail', 'p.abweichendelieferadresse', 'p.liefername', 'p.lieferabteilung', 'p.lieferunterabteilung', 'p.lieferland', 'p.lieferstrasse', 'p.lieferort', 'p.lieferplz', 'p.lieferadresszusatz', 'p.lieferansprechpartner', 'p.packstation_inhaber', 'p.packstation_station', 'p.packstation_ident', 'p.packstation_plz', 'p.packstation_ort', 'p.autofreigabe', 'p.freigabe', 'p.nachbesserung', 'p.gesamtsumme', 'p.inbearbeitung', 'p.abgeschlossen', 'p.nachlieferung', 'p.lager_ok', 'p.porto_ok', 'p.ust_ok', 'p.check_ok', 'p.vorkasse_ok', 'p.nachnahme_ok', 'p.reserviert_ok', 'p.bestellt_ok', 'p.zeit_ok', 'p.versand_ok', 'p.partnerid', 'p.folgebestaetigung', 'p.zahlungsmail', 'p.stornogrund', 'p.stornosonstiges', 'p.stornorueckzahlung', 'p.stornobetrag', 'p.stornobankinhaber', 'p.stornobankkonto', 'p.stornobankblz', 'p.stornobankbank', 'p.stornogutschrift', 'p.stornogutschriftbeleg', 'p.stornowareerhalten', 'p.stornomanuellebearbeitung', 'p.stornokommentar', 'p.stornobezahlt', 'p.stornobezahltam', 'p.stornobezahltvon', 'p.stornoabgeschlossen', 'p.stornorueckzahlungper', 'p.stornowareerhaltenretour', 'p.partnerausgezahlt', 'p.partnerausgezahltam', 'p.kennen', 'p.logdatei', 'p.bezeichnung', 'p.datumproduktion', 'p.anschreiben', 'p.usereditid', 'p.useredittimestamp', 'p.steuersatz_normal', 'p.steuersatz_zwischen', 'p.steuersatz_ermaessigt', 'p.steuersatz_starkermaessigt', 'p.steuersatz_dienstleistung', 'p.waehrung', 'p.schreibschutz', 'p.pdfarchiviert', 'p.pdfarchiviertversion', 'p.typ', 'p.reservierart', 'p.auslagerart', 'p.projektfiliale', 'p.datumauslieferung', 'p.datumbereitstellung', 'p.unterlistenexplodieren', 'p.charge', 'p.arbeitsschrittetextanzeigen', 'p.einlagern_ok', 'p.auslagern_ok', 'p.mhd', 'p.auftragmengenanpassen', 'p.internebezeichnung', 'p.mengeoriginal', 'p.teilproduktionvon', 'p.teilproduktionnummer', 'p.parent', 'p.parentnummer', 'p.bearbeiterid', 'p.mengeausschuss', 'p.mengeerfolgreich', 'p.abschlussbemerkung', 'p.auftragid', 'p.funktionstest', 'p.seriennummer_erstellen', 'p.unterseriennummern_erfassen', 'p.datumproduktionende', 'p.standardlager');
                $findcols = array('p.id','p.id','p.belegnr','p.kundennummer','p.name','p.datum');

//                $searchsql = array('p.datum', 'p.art', 'p.projekt', 'p.belegnr', 'p.internet', 'p.bearbeiter', 'p.angebot', 'p.freitext', 'p.internebemerkung', 'p.status', 'p.adresse', 'p.name', 'p.abteilung', 'p.unterabteilung', 'p.strasse', 'p.adresszusatz', 'p.ansprechpartner', 'p.plz', 'p.ort', 'p.land', 'p.ustid', 'p.ust_befreit', 'p.ust_inner', 'p.email', 'p.telefon', 'p.telefax', 'p.betreff', 'p.kundennummer', 'p.versandart', 'p.vertrieb', 'p.zahlungsweise', 'p.zahlungszieltage', 'p.zahlungszieltageskonto', 'p.zahlungszielskonto', 'p.bank_inhaber', 'p.bank_institut', 'p.bank_blz', 'p.bank_konto', 'p.kreditkarte_typ', 'p.kreditkarte_inhaber', 'p.kreditkarte_nummer', 'p.kreditkarte_pruefnummer', 'p.kreditkarte_monat', 'p.kreditkarte_jahr', 'p.firma', 'p.versendet', 'p.versendet_am', 'p.versendet_per', 'p.versendet_durch', 'p.autoversand', 'p.keinporto', 'p.keinestornomail', 'p.abweichendelieferadresse', 'p.liefername', 'p.lieferabteilung', 'p.lieferunterabteilung', 'p.lieferland', 'p.lieferstrasse', 'p.lieferort', 'p.lieferplz', 'p.lieferadresszusatz', 'p.lieferansprechpartner', 'p.packstation_inhaber', 'p.packstation_station', 'p.packstation_ident', 'p.packstation_plz', 'p.packstation_ort', 'p.autofreigabe', 'p.freigabe', 'p.nachbesserung', 'p.gesamtsumme', 'p.inbearbeitung', 'p.abgeschlossen', 'p.nachlieferung', 'p.lager_ok', 'p.porto_ok', 'p.ust_ok', 'p.check_ok', 'p.vorkasse_ok', 'p.nachnahme_ok', 'p.reserviert_ok', 'p.bestellt_ok', 'p.zeit_ok', 'p.versand_ok', 'p.partnerid', 'p.folgebestaetigung', 'p.zahlungsmail', 'p.stornogrund', 'p.stornosonstiges', 'p.stornorueckzahlung', 'p.stornobetrag', 'p.stornobankinhaber', 'p.stornobankkonto', 'p.stornobankblz', 'p.stornobankbank', 'p.stornogutschrift', 'p.stornogutschriftbeleg', 'p.stornowareerhalten', 'p.stornomanuellebearbeitung', 'p.stornokommentar', 'p.stornobezahlt', 'p.stornobezahltam', 'p.stornobezahltvon', 'p.stornoabgeschlossen', 'p.stornorueckzahlungper', 'p.stornowareerhaltenretour', 'p.partnerausgezahlt', 'p.partnerausgezahltam', 'p.kennen', 'p.logdatei', 'p.bezeichnung', 'p.datumproduktion', 'p.anschreiben', 'p.usereditid', 'p.useredittimestamp', 'p.steuersatz_normal', 'p.steuersatz_zwischen', 'p.steuersatz_ermaessigt', 'p.steuersatz_starkermaessigt', 'p.steuersatz_dienstleistung', 'p.waehrung', 'p.schreibschutz', 'p.pdfarchiviert', 'p.pdfarchiviertversion', 'p.typ', 'p.reservierart', 'p.auslagerart', 'p.projektfiliale', 'p.datumauslieferung', 'p.datumbereitstellung', 'p.unterlistenexplodieren', 'p.charge', 'p.arbeitsschrittetextanzeigen', 'p.einlagern_ok', 'p.auslagern_ok', 'p.mhd', 'p.auftragmengenanpassen', 'p.internebezeichnung', 'p.mengeoriginal', 'p.teilproduktionvon', 'p.teilproduktionnummer', 'p.parent', 'p.parentnummer', 'p.bearbeiterid', 'p.mengeausschuss', 'p.mengeerfolgreich', 'p.abschlussbemerkung', 'p.auftragid', 'p.funktionstest', 'p.seriennummer_erstellen', 'p.unterseriennummern_erfassen', 'p.datumproduktionende', 'p.standardlager');
                $searchsql = array('p.datum', 'p.art', 'p.projekt', 'p.belegnr', 'p.internet', 'p.bearbeiter', 'p.angebot', 'p.freitext', 'p.internebemerkung', 'p.status', 'p.adresse', 'p.name', 'p.abteilung', 'p.unterabteilung', 'p.strasse', 'p.adresszusatz', 'p.ansprechpartner', 'p.plz', 'p.ort', 'p.land', 'p.ustid', 'p.ust_befreit', 'p.ust_inner', 'p.email', 'p.telefon', 'p.telefax', 'p.betreff', 'p.kundennummer', 'p.versandart', 'p.vertrieb', 'p.zahlungsweise', 'p.zahlungszieltage', 'p.zahlungszieltageskonto', 'p.zahlungszielskonto', 'p.bank_inhaber', 'p.bank_institut', 'p.bank_blz', 'p.bank_konto', 'p.kreditkarte_typ', 'p.kreditkarte_inhaber', 'p.kreditkarte_nummer', 'p.kreditkarte_pruefnummer', 'p.kreditkarte_monat', 'p.kreditkarte_jahr', 'p.firma', 'p.versendet', 'p.versendet_am', 'p.versendet_per', 'p.versendet_durch', 'p.autoversand', 'p.keinporto', 'p.keinestornomail', 'p.abweichendelieferadresse', 'p.liefername', 'p.lieferabteilung', 'p.lieferunterabteilung', 'p.lieferland', 'p.lieferstrasse', 'p.lieferort', 'p.lieferplz', 'p.lieferadresszusatz', 'p.lieferansprechpartner', 'p.packstation_inhaber', 'p.packstation_station', 'p.packstation_ident', 'p.packstation_plz', 'p.packstation_ort', 'p.autofreigabe', 'p.freigabe', 'p.nachbesserung', 'p.gesamtsumme', 'p.inbearbeitung', 'p.abgeschlossen', 'p.nachlieferung', 'p.lager_ok', 'p.porto_ok', 'p.ust_ok', 'p.check_ok', 'p.vorkasse_ok', 'p.nachnahme_ok', 'p.reserviert_ok', 'p.bestellt_ok', 'p.zeit_ok', 'p.versand_ok', 'p.partnerid', 'p.folgebestaetigung', 'p.zahlungsmail', 'p.stornogrund', 'p.stornosonstiges', 'p.stornorueckzahlung', 'p.stornobetrag', 'p.stornobankinhaber', 'p.stornobankkonto', 'p.stornobankblz', 'p.stornobankbank', 'p.stornogutschrift', 'p.stornogutschriftbeleg', 'p.stornowareerhalten', 'p.stornomanuellebearbeitung', 'p.stornokommentar', 'p.stornobezahlt', 'p.stornobezahltam', 'p.stornobezahltvon', 'p.stornoabgeschlossen', 'p.stornorueckzahlungper', 'p.stornowareerhaltenretour', 'p.partnerausgezahlt', 'p.partnerausgezahltam', 'p.kennen', 'p.logdatei', 'p.bezeichnung', 'p.datumproduktion', 'p.anschreiben', 'p.usereditid', 'p.useredittimestamp', 'p.steuersatz_normal', 'p.steuersatz_zwischen', 'p.steuersatz_ermaessigt', 'p.steuersatz_starkermaessigt', 'p.steuersatz_dienstleistung', 'p.waehrung', 'p.schreibschutz', 'p.pdfarchiviert', 'p.pdfarchiviertversion', 'p.typ', 'p.reservierart', 'p.auslagerart', 'p.projektfiliale', 'p.datumauslieferung', 'p.datumbereitstellung', 'p.unterlistenexplodieren', 'p.charge', 'p.arbeitsschrittetextanzeigen', 'p.einlagern_ok', 'p.auslagern_ok', 'p.mhd', 'p.auftragmengenanpassen', 'p.internebezeichnung', 'p.mengeoriginal', 'p.teilproduktionvon', 'p.teilproduktionnummer', 'p.parent', 'p.parentnummer', 'p.bearbeiterid', 'p.mengeausschuss', 'p.mengeerfolgreich', 'p.abschlussbemerkung', 'p.auftragid', 'p.funktionstest', 'p.seriennummer_erstellen', 'p.unterseriennummern_erfassen', 'p.datumproduktionende', 'p.standardlager');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',p.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=produktion&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

//                $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, $dropnbox, p.datum, p.art, p.projekt, p.belegnr, p.internet, p.bearbeiter, p.angebot, p.freitext, p.internebemerkung, p.status, p.adresse, p.name, p.abteilung, p.unterabteilung, p.strasse, p.adresszusatz, p.ansprechpartner, p.plz, p.ort, p.land, p.ustid, p.ust_befreit, p.ust_inner, p.email, p.telefon, p.telefax, p.betreff, p.kundennummer, p.versandart, p.vertrieb, p.zahlungsweise, p.zahlungszieltage, p.zahlungszieltageskonto, p.zahlungszielskonto, p.bank_inhaber, p.bank_institut, p.bank_blz, p.bank_konto, p.kreditkarte_typ, p.kreditkarte_inhaber, p.kreditkarte_nummer, p.kreditkarte_pruefnummer, p.kreditkarte_monat, p.kreditkarte_jahr, p.firma, p.versendet, p.versendet_am, p.versendet_per, p.versendet_durch, p.autoversand, p.keinporto, p.keinestornomail, p.abweichendelieferadresse, p.liefername, p.lieferabteilung, p.lieferunterabteilung, p.lieferland, p.lieferstrasse, p.lieferort, p.lieferplz, p.lieferadresszusatz, p.lieferansprechpartner, p.packstation_inhaber, p.packstation_station, p.packstation_ident, p.packstation_plz, p.packstation_ort, p.autofreigabe, p.freigabe, p.nachbesserung, p.gesamtsumme, p.inbearbeitung, p.abgeschlossen, p.nachlieferung, p.lager_ok, p.porto_ok, p.ust_ok, p.check_ok, p.vorkasse_ok, p.nachnahme_ok, p.reserviert_ok, p.bestellt_ok, p.zeit_ok, p.versand_ok, p.partnerid, p.folgebestaetigung, p.zahlungsmail, p.stornogrund, p.stornosonstiges, p.stornorueckzahlung, p.stornobetrag, p.stornobankinhaber, p.stornobankkonto, p.stornobankblz, p.stornobankbank, p.stornogutschrift, p.stornogutschriftbeleg, p.stornowareerhalten, p.stornomanuellebearbeitung, p.stornokommentar, p.stornobezahlt, p.stornobezahltam, p.stornobezahltvon, p.stornoabgeschlossen, p.stornorueckzahlungper, p.stornowareerhaltenretour, p.partnerausgezahlt, p.partnerausgezahltam, p.kennen, p.logdatei, p.bezeichnung, p.datumproduktion, p.anschreiben, p.usereditid, p.useredittimestamp, p.steuersatz_normal, p.steuersatz_zwischen, p.steuersatz_ermaessigt, p.steuersatz_starkermaessigt, p.steuersatz_dienstleistung, p.waehrung, p.schreibschutz, p.pdfarchiviert, p.pdfarchiviertversion, p.typ, p.reservierart, p.auslagerart, p.projektfiliale, p.datumauslieferung, p.datumbereitstellung, p.unterlistenexplodieren, p.charge, p.arbeitsschrittetextanzeigen, p.einlagern_ok, p.auslagern_ok, p.mhd, p.auftragmengenanpassen, p.internebezeichnung, p.mengeoriginal, p.teilproduktionvon, p.teilproduktionnummer, p.parent, p.parentnummer, p.bearbeiterid, p.mengeausschuss, p.mengeerfolgreich, p.abschlussbemerkung, p.auftragid, p.funktionstest, p.seriennummer_erstellen, p.unterseriennummern_erfassen, p.datumproduktionende, p.standardlager, p.id FROM produktion p";
//                $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, $dropnbox, p.belegnr, p.kundennummer, p.name, p.datum, \"SUBSELECT\", \"SUBSELECT\", p.mengeerfolgreich, \"-\", \"-\", p.projekt, p.status, p.status, p.id FROM produktion p";
                $sql = "SELECT SQL_CALC_FOUND_ROWS 
			p.id,
			$dropnbox,
			p.belegnr,
			p.kundennummer,
			p.name,
			p.datum,
			(SELECT pp.bezeichnung FROM produktion_position pp WHERE pp.produktion = p.id AND pp.stuecklistestufe = 1 LIMIT 1),
			FORMAT((SELECT SUM(menge) FROM produktion_position pp WHERE pp.produktion = p.id AND pp.stuecklistestufe = 1),0,'de_DE'),
			FORMAT(p.mengeerfolgreich,0),
			\"-\",
			\"-\",
			(SELECT projekt.abkuerzung FROM projekt WHERE p.projekt = projekt.id LIMIT 1),
			p.status,
		        (" . $app->YUI->IconsSQL_produktion('p') . ")  AS `icons`,
			p.id
			FROM produktion p";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM produktion WHERE $where";
//                $groupby = "";

                break;
                case "produktion_position_target_list":
                $id = $app->Secure->GetGET('id');
                $allowed['produktion_position_list'] = array('list');
                $heading = array('','', 'Artikel', 'Projekt', 'Nummer', 'Planmenge', 'Lager', 'Produziert', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                $findcols = array('(SELECT a.name FROM artikel a WHERE a.id = p.artikel LIMIT 1)', 'p.projekt', 'p.bezeichnung', 'p.beschreibung', 'p.internerkommentar', 'p.nummer', 'p.menge', 'p.preis', 'p.waehrung', 'p.lieferdatum', 'p.vpe', 'p.sort', 'p.status', 'p.umsatzsteuer', 'p.bemerkung', 'p.geliefert', 'p.geliefert_menge', 'p.explodiert', 'p.explodiert_parent', 'p.logdatei', 'p.nachbestelltexternereinkauf', 'p.beistellung', 'p.externeproduktion', 'p.einheit', 'p.steuersatz', 'p.steuertext', 'p.erloese', 'p.erloesefestschreiben', 'p.freifeld1', 'p.freifeld2', 'p.freifeld3', 'p.freifeld4', 'p.freifeld5', 'p.freifeld6', 'p.freifeld7', 'p.freifeld8', 'p.freifeld9', 'p.freifeld10', 'p.freifeld11', 'p.freifeld12', 'p.freifeld13', 'p.freifeld14', 'p.freifeld15', 'p.freifeld16', 'p.freifeld17', 'p.freifeld18', 'p.freifeld19', 'p.freifeld20', 'p.freifeld21', 'p.freifeld22', 'p.freifeld23', 'p.freifeld24', 'p.freifeld25', 'p.freifeld26', 'p.freifeld27', 'p.freifeld28', 'p.freifeld29', 'p.freifeld30', 'p.freifeld31', 'p.freifeld32', 'p.freifeld33', 'p.freifeld34', 'p.freifeld35', 'p.freifeld36', 'p.freifeld37', 'p.freifeld38', 'p.freifeld39', 'p.freifeld40', 'p.stuecklistestufe', 'p.teilprojekt');
                $searchsql = array('p.produktion', 'p.artikel', 'p.projekt', 'p.bezeichnung', 'p.beschreibung', 'p.internerkommentar', 'p.nummer', 'p.menge', 'p.preis', 'p.waehrung', 'p.lieferdatum', 'p.vpe', 'p.sort', 'p.status', 'p.umsatzsteuer', 'p.bemerkung', 'p.geliefert', 'p.geliefert_menge', 'p.explodiert', 'p.explodiert_parent', 'p.logdatei', 'p.nachbestelltexternereinkauf', 'p.beistellung', 'p.externeproduktion', 'p.einheit', 'p.steuersatz', 'p.steuertext', 'p.erloese', 'p.erloesefestschreiben', 'p.freifeld1', 'p.freifeld2', 'p.freifeld3', 'p.freifeld4', 'p.freifeld5', 'p.freifeld6', 'p.freifeld7', 'p.freifeld8', 'p.freifeld9', 'p.freifeld10', 'p.freifeld11', 'p.freifeld12', 'p.freifeld13', 'p.freifeld14', 'p.freifeld15', 'p.freifeld16', 'p.freifeld17', 'p.freifeld18', 'p.freifeld19', 'p.freifeld20', 'p.freifeld21', 'p.freifeld22', 'p.freifeld23', 'p.freifeld24', 'p.freifeld25', 'p.freifeld26', 'p.freifeld27', 'p.freifeld28', 'p.freifeld29', 'p.freifeld30', 'p.freifeld31', 'p.freifeld32', 'p.freifeld33', 'p.freifeld34', 'p.freifeld35', 'p.freifeld36', 'p.freifeld37', 'p.freifeld38', 'p.freifeld39', 'p.freifeld40', 'p.stuecklistestufe', 'p.teilprojekt');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',p.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=produktion_position&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion_position&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS
                    p.id,
                    $dropnbox,
                    (SELECT a.name_de FROM artikel a WHERE a.id = p.artikel LIMIT 1) as name,
                    (SELECT projekt.abkuerzung FROM projekt WHERE p.projekt = projekt.id LIMIT 1) as projekt,
                    (SELECT a.nummer FROM artikel a WHERE a.id = p.artikel LIMIT 1) as name,
                    FORMAT(p.menge,0,'de_DE'),
                    FORMAT(p.menge,0,'de_DE') as Lager,
                    FORMAT(p.geliefert_menge,0,'de_DE'),
                    p.id 
                    FROM produktion_position p";

                $where = " stuecklistestufe = 1 AND produktion = $id";

                $count = "SELECT count(DISTINCT id) FROM produktion_position WHERE $where";
//                $groupby = "";

                break;
                case "produktion_position_source_list":
                $id = $app->Secure->GetGET('id');
                $allowed['produktion_position_list'] = array('list');
                $heading = array('','', 'Artikel', 'Projekt', 'Nummer', 'Planmenge', 'Lager', 'Reserviert', 'Verbraucht', 'Men&uuml;');
                $width = array('1%','1%','10%'); // Fill out manually later

                $findcols = array('(SELECT a.name FROM artikel a WHERE a.id = p.artikel LIMIT 1)', 'p.projekt', 'p.bezeichnung', 'p.beschreibung', 'p.internerkommentar', 'p.nummer', 'p.menge', 'p.preis', 'p.waehrung', 'p.lieferdatum', 'p.vpe', 'p.sort', 'p.status', 'p.umsatzsteuer', 'p.bemerkung', 'p.geliefert', 'p.geliefert_menge', 'p.explodiert', 'p.explodiert_parent', 'p.logdatei', 'p.nachbestelltexternereinkauf', 'p.beistellung', 'p.externeproduktion', 'p.einheit', 'p.steuersatz', 'p.steuertext', 'p.erloese', 'p.erloesefestschreiben', 'p.freifeld1', 'p.freifeld2', 'p.freifeld3', 'p.freifeld4', 'p.freifeld5', 'p.freifeld6', 'p.freifeld7', 'p.freifeld8', 'p.freifeld9', 'p.freifeld10', 'p.freifeld11', 'p.freifeld12', 'p.freifeld13', 'p.freifeld14', 'p.freifeld15', 'p.freifeld16', 'p.freifeld17', 'p.freifeld18', 'p.freifeld19', 'p.freifeld20', 'p.freifeld21', 'p.freifeld22', 'p.freifeld23', 'p.freifeld24', 'p.freifeld25', 'p.freifeld26', 'p.freifeld27', 'p.freifeld28', 'p.freifeld29', 'p.freifeld30', 'p.freifeld31', 'p.freifeld32', 'p.freifeld33', 'p.freifeld34', 'p.freifeld35', 'p.freifeld36', 'p.freifeld37', 'p.freifeld38', 'p.freifeld39', 'p.freifeld40', 'p.stuecklistestufe', 'p.teilprojekt');
                $searchsql = array('p.produktion', 'p.artikel', 'p.projekt', 'p.bezeichnung', 'p.beschreibung', 'p.internerkommentar', 'p.nummer', 'p.menge', 'p.preis', 'p.waehrung', 'p.lieferdatum', 'p.vpe', 'p.sort', 'p.status', 'p.umsatzsteuer', 'p.bemerkung', 'p.geliefert', 'p.geliefert_menge', 'p.explodiert', 'p.explodiert_parent', 'p.logdatei', 'p.nachbestelltexternereinkauf', 'p.beistellung', 'p.externeproduktion', 'p.einheit', 'p.steuersatz', 'p.steuertext', 'p.erloese', 'p.erloesefestschreiben', 'p.freifeld1', 'p.freifeld2', 'p.freifeld3', 'p.freifeld4', 'p.freifeld5', 'p.freifeld6', 'p.freifeld7', 'p.freifeld8', 'p.freifeld9', 'p.freifeld10', 'p.freifeld11', 'p.freifeld12', 'p.freifeld13', 'p.freifeld14', 'p.freifeld15', 'p.freifeld16', 'p.freifeld17', 'p.freifeld18', 'p.freifeld19', 'p.freifeld20', 'p.freifeld21', 'p.freifeld22', 'p.freifeld23', 'p.freifeld24', 'p.freifeld25', 'p.freifeld26', 'p.freifeld27', 'p.freifeld28', 'p.freifeld29', 'p.freifeld30', 'p.freifeld31', 'p.freifeld32', 'p.freifeld33', 'p.freifeld34', 'p.freifeld35', 'p.freifeld36', 'p.freifeld37', 'p.freifeld38', 'p.freifeld39', 'p.freifeld40', 'p.stuecklistestufe', 'p.teilprojekt');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		$dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',p.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=produktion_position&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion_position&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS
                    p.id,
                    $dropnbox,
                    (SELECT a.name_de FROM artikel a WHERE a.id = p.artikel LIMIT 1) as name,
                    (SELECT projekt.abkuerzung FROM projekt WHERE p.projekt = projekt.id LIMIT 1) as projekt,
                    (SELECT a.nummer FROM artikel a WHERE a.id = p.artikel LIMIT 1) as name,
                    FORMAT(p.menge,0,'de_DE'),
                    FORMAT(p.menge,0,'de_DE') as Lager,
                    FORMAT(p.menge,0,'de_DE') as Reserviert,
                    FORMAT(p.geliefert_menge,0,'de_DE'),
                    p.id 
                    FROM produktion_position p";

                $where = " stuecklistestufe = 0 AND produktion = $id";

                $count = "SELECT count(DISTINCT id) FROM produktion_position WHERE $where";
//                $groupby = "";

                break;
        }

        $erg = false;

        foreach ($erlaubtevars as $k => $v) {
            if (isset($$v)) {
                $erg[$v] = $$v;
            }
        }
        return $erg;
    }
    
    function produktion_list() {
        $this->app->erp->MenuEintrag("index.php?module=produktion&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=produktion&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'produktion_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "produktion_list.tpl");
    }    

    public function produktion_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `produktion` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->produktion_list();
    } 

    /*
     * Edit produktion item
     * If id is empty, create a new one
     */
        
    function produktion_edit() {
        $id = $this->app->Secure->GetGET('id');
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=produktion&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=produktion&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
                
        if (empty($id)) {
            // New item
            $id = 'NULL';
        } 

        if ($submit != '')
        {

            // Write to database
            
            // Add checks here

            $columns = "id, ";
            $values = "$id, ";
            $update = "";
    
            $fix = "";

            foreach ($input as $key => $value) {
                $columns = $columns.$fix.$key;
                $values = $values.$fix."'".$value."'";
                $update = $update.$fix.$key." = '$value'";

                $fix = ", ";
            }

//            echo($columns."<br>");
//            echo($values."<br>");
//            echo($update."<br>");

            $sql = "INSERT INTO produktion (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;

//            echo($sql);

            $this->app->DB->Update($sql);

            if ($id == 'NULL') {
                $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                header("Location: index.php?module=produktion&action=list&msg=$msg");
            } else {
                $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
            }
        }

    
        // Load values again from database

	$sql = "SELECT SQL_CALC_FOUND_ROWS p.id, p.datum, p.art, p.projekt, p.belegnr, p.internet, p.bearbeiter, p.angebot, p.freitext, p.internebemerkung, p.status, p.adresse, p.name, p.abteilung, p.unterabteilung, p.strasse, p.adresszusatz, p.ansprechpartner, p.plz, p.ort, p.land, p.ustid, p.ust_befreit, p.ust_inner, p.email, p.telefon, p.telefax, p.betreff, p.kundennummer, p.versandart, p.vertrieb, p.zahlungsweise, p.zahlungszieltage, p.zahlungszieltageskonto, p.zahlungszielskonto, p.bank_inhaber, p.bank_institut, p.bank_blz, p.bank_konto, p.kreditkarte_typ, p.kreditkarte_inhaber, p.kreditkarte_nummer, p.kreditkarte_pruefnummer, p.kreditkarte_monat, p.kreditkarte_jahr, p.firma, p.versendet, p.versendet_am, p.versendet_per, p.versendet_durch, p.autoversand, p.keinporto, p.keinestornomail, p.abweichendelieferadresse, p.liefername, p.lieferabteilung, p.lieferunterabteilung, p.lieferland, p.lieferstrasse, p.lieferort, p.lieferplz, p.lieferadresszusatz, p.lieferansprechpartner, p.packstation_inhaber, p.packstation_station, p.packstation_ident, p.packstation_plz, p.packstation_ort, p.autofreigabe, p.freigabe, p.nachbesserung, p.gesamtsumme, p.inbearbeitung, p.abgeschlossen, p.nachlieferung, p.lager_ok, p.porto_ok, p.ust_ok, p.check_ok, p.vorkasse_ok, p.nachnahme_ok, p.reserviert_ok, p.bestellt_ok, p.zeit_ok, p.versand_ok, p.partnerid, p.folgebestaetigung, p.zahlungsmail, p.stornogrund, p.stornosonstiges, p.stornorueckzahlung, p.stornobetrag, p.stornobankinhaber, p.stornobankkonto, p.stornobankblz, p.stornobankbank, p.stornogutschrift, p.stornogutschriftbeleg, p.stornowareerhalten, p.stornomanuellebearbeitung, p.stornokommentar, p.stornobezahlt, p.stornobezahltam, p.stornobezahltvon, p.stornoabgeschlossen, p.stornorueckzahlungper, p.stornowareerhaltenretour, p.partnerausgezahlt, p.partnerausgezahltam, p.kennen, p.logdatei, p.bezeichnung, p.datumproduktion, p.anschreiben, p.usereditid, p.useredittimestamp, p.steuersatz_normal, p.steuersatz_zwischen, p.steuersatz_ermaessigt, p.steuersatz_starkermaessigt, p.steuersatz_dienstleistung, p.waehrung, p.schreibschutz, p.pdfarchiviert, p.pdfarchiviertversion, p.typ, p.reservierart, p.auslagerart, p.projektfiliale, p.datumauslieferung, p.datumbereitstellung, p.unterlistenexplodieren, p.charge, p.arbeitsschrittetextanzeigen, p.einlagern_ok, p.auslagern_ok, p.mhd, p.auftragmengenanpassen, p.internebezeichnung, p.mengeoriginal, p.teilproduktionvon, p.teilproduktionnummer, p.parent, p.parentnummer, p.bearbeiterid, p.mengeausschuss, p.mengeerfolgreich, p.abschlussbemerkung, p.auftragid, p.funktionstest, p.seriennummer_erstellen, p.unterseriennummern_erfassen, p.datumproduktionende, p.standardlager, p.id FROM produktion p"." WHERE id=$id";	
        $result = $this->app->DB->SelectArr($sql);

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }
             
        /*
         * Add displayed items later
         */ 

	$sql = "SELECT " . $this->app->YUI->IconsSQL_produktion('p') . " AS `icons` FROM produktion p WHERE id=$id";
	$icons = $this->app->DB->SelectArr($sql);
        $this->app->Tpl->Add('STATUSICONS',  $icons[0]['icons']);



/*
        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         
         */

//        $this->SetInput($input);              


    // Parse positions                                      
        $this->app->YUI->TableSearch('PRODUKTION_POSITION_TARGET_TABELLE', 'produktion_position_target_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->YUI->TableSearch('PRODUKTION_POSITION_SOURCE_TABELLE', 'produktion_position_source_list', "show", "", "", basename(__FILE__), __CLASS__);

        $this->app->Tpl->Parse('PAGE', "produktion_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['datum'] = $this->app->Secure->GetPOST('datum');
	$input['art'] = $this->app->Secure->GetPOST('art');
	$input['projekt'] = $this->app->Secure->GetPOST('projekt');
	$input['belegnr'] = $this->app->Secure->GetPOST('belegnr');
	$input['internet'] = $this->app->Secure->GetPOST('internet');
	$input['bearbeiter'] = $this->app->Secure->GetPOST('bearbeiter');
	$input['angebot'] = $this->app->Secure->GetPOST('angebot');
	$input['freitext'] = $this->app->Secure->GetPOST('freitext');
	$input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung');
//	$input['status'] = $this->app->Secure->GetPOST('status');
	$input['adresse'] = $this->app->Secure->GetPOST('adresse');
	$input['name'] = $this->app->Secure->GetPOST('name');
	$input['abteilung'] = $this->app->Secure->GetPOST('abteilung');
	$input['unterabteilung'] = $this->app->Secure->GetPOST('unterabteilung');
	$input['strasse'] = $this->app->Secure->GetPOST('strasse');
	$input['adresszusatz'] = $this->app->Secure->GetPOST('adresszusatz');
	$input['ansprechpartner'] = $this->app->Secure->GetPOST('ansprechpartner');
	$input['plz'] = $this->app->Secure->GetPOST('plz');
	$input['ort'] = $this->app->Secure->GetPOST('ort');
	$input['land'] = $this->app->Secure->GetPOST('land');
	$input['ustid'] = $this->app->Secure->GetPOST('ustid');
	$input['ust_befreit'] = $this->app->Secure->GetPOST('ust_befreit');
	$input['ust_inner'] = $this->app->Secure->GetPOST('ust_inner');
	$input['email'] = $this->app->Secure->GetPOST('email');
	$input['telefon'] = $this->app->Secure->GetPOST('telefon');
	$input['telefax'] = $this->app->Secure->GetPOST('telefax');
	$input['betreff'] = $this->app->Secure->GetPOST('betreff');
	$input['kundennummer'] = $this->app->Secure->GetPOST('kundennummer');
	$input['versandart'] = $this->app->Secure->GetPOST('versandart');
	$input['vertrieb'] = $this->app->Secure->GetPOST('vertrieb');
	$input['zahlungsweise'] = $this->app->Secure->GetPOST('zahlungsweise');
	$input['zahlungszieltage'] = $this->app->Secure->GetPOST('zahlungszieltage');
	$input['zahlungszieltageskonto'] = $this->app->Secure->GetPOST('zahlungszieltageskonto');
	$input['zahlungszielskonto'] = $this->app->Secure->GetPOST('zahlungszielskonto');
	$input['bank_inhaber'] = $this->app->Secure->GetPOST('bank_inhaber');
	$input['bank_institut'] = $this->app->Secure->GetPOST('bank_institut');
	$input['bank_blz'] = $this->app->Secure->GetPOST('bank_blz');
	$input['bank_konto'] = $this->app->Secure->GetPOST('bank_konto');
	$input['kreditkarte_typ'] = $this->app->Secure->GetPOST('kreditkarte_typ');
	$input['kreditkarte_inhaber'] = $this->app->Secure->GetPOST('kreditkarte_inhaber');
	$input['kreditkarte_nummer'] = $this->app->Secure->GetPOST('kreditkarte_nummer');
	$input['kreditkarte_pruefnummer'] = $this->app->Secure->GetPOST('kreditkarte_pruefnummer');
	$input['kreditkarte_monat'] = $this->app->Secure->GetPOST('kreditkarte_monat');
	$input['kreditkarte_jahr'] = $this->app->Secure->GetPOST('kreditkarte_jahr');
	$input['firma'] = $this->app->Secure->GetPOST('firma');
	$input['versendet'] = $this->app->Secure->GetPOST('versendet');
	$input['versendet_am'] = $this->app->Secure->GetPOST('versendet_am');
	$input['versendet_per'] = $this->app->Secure->GetPOST('versendet_per');
	$input['versendet_durch'] = $this->app->Secure->GetPOST('versendet_durch');
	$input['autoversand'] = $this->app->Secure->GetPOST('autoversand');
	$input['keinporto'] = $this->app->Secure->GetPOST('keinporto');
	$input['keinestornomail'] = $this->app->Secure->GetPOST('keinestornomail');
	$input['abweichendelieferadresse'] = $this->app->Secure->GetPOST('abweichendelieferadresse');
	$input['liefername'] = $this->app->Secure->GetPOST('liefername');
	$input['lieferabteilung'] = $this->app->Secure->GetPOST('lieferabteilung');
	$input['lieferunterabteilung'] = $this->app->Secure->GetPOST('lieferunterabteilung');
	$input['lieferland'] = $this->app->Secure->GetPOST('lieferland');
	$input['lieferstrasse'] = $this->app->Secure->GetPOST('lieferstrasse');
	$input['lieferort'] = $this->app->Secure->GetPOST('lieferort');
	$input['lieferplz'] = $this->app->Secure->GetPOST('lieferplz');
	$input['lieferadresszusatz'] = $this->app->Secure->GetPOST('lieferadresszusatz');
	$input['lieferansprechpartner'] = $this->app->Secure->GetPOST('lieferansprechpartner');
	$input['packstation_inhaber'] = $this->app->Secure->GetPOST('packstation_inhaber');
	$input['packstation_station'] = $this->app->Secure->GetPOST('packstation_station');
	$input['packstation_ident'] = $this->app->Secure->GetPOST('packstation_ident');
	$input['packstation_plz'] = $this->app->Secure->GetPOST('packstation_plz');
	$input['packstation_ort'] = $this->app->Secure->GetPOST('packstation_ort');
	$input['autofreigabe'] = $this->app->Secure->GetPOST('autofreigabe');
	$input['freigabe'] = $this->app->Secure->GetPOST('freigabe');
	$input['nachbesserung'] = $this->app->Secure->GetPOST('nachbesserung');
	$input['gesamtsumme'] = $this->app->Secure->GetPOST('gesamtsumme');
	$input['inbearbeitung'] = $this->app->Secure->GetPOST('inbearbeitung');
	$input['abgeschlossen'] = $this->app->Secure->GetPOST('abgeschlossen');
	$input['nachlieferung'] = $this->app->Secure->GetPOST('nachlieferung');
	$input['lager_ok'] = $this->app->Secure->GetPOST('lager_ok');
	$input['porto_ok'] = $this->app->Secure->GetPOST('porto_ok');
	$input['ust_ok'] = $this->app->Secure->GetPOST('ust_ok');
	$input['check_ok'] = $this->app->Secure->GetPOST('check_ok');
	$input['vorkasse_ok'] = $this->app->Secure->GetPOST('vorkasse_ok');
	$input['nachnahme_ok'] = $this->app->Secure->GetPOST('nachnahme_ok');
	$input['reserviert_ok'] = $this->app->Secure->GetPOST('reserviert_ok');
	$input['bestellt_ok'] = $this->app->Secure->GetPOST('bestellt_ok');
	$input['zeit_ok'] = $this->app->Secure->GetPOST('zeit_ok');
	$input['versand_ok'] = $this->app->Secure->GetPOST('versand_ok');
	$input['partnerid'] = $this->app->Secure->GetPOST('partnerid');
	$input['folgebestaetigung'] = $this->app->Secure->GetPOST('folgebestaetigung');
	$input['zahlungsmail'] = $this->app->Secure->GetPOST('zahlungsmail');
	$input['stornogrund'] = $this->app->Secure->GetPOST('stornogrund');
	$input['stornosonstiges'] = $this->app->Secure->GetPOST('stornosonstiges');
	$input['stornorueckzahlung'] = $this->app->Secure->GetPOST('stornorueckzahlung');
	$input['stornobetrag'] = $this->app->Secure->GetPOST('stornobetrag');
	$input['stornobankinhaber'] = $this->app->Secure->GetPOST('stornobankinhaber');
	$input['stornobankkonto'] = $this->app->Secure->GetPOST('stornobankkonto');
	$input['stornobankblz'] = $this->app->Secure->GetPOST('stornobankblz');
	$input['stornobankbank'] = $this->app->Secure->GetPOST('stornobankbank');
	$input['stornogutschrift'] = $this->app->Secure->GetPOST('stornogutschrift');
	$input['stornogutschriftbeleg'] = $this->app->Secure->GetPOST('stornogutschriftbeleg');
	$input['stornowareerhalten'] = $this->app->Secure->GetPOST('stornowareerhalten');
	$input['stornomanuellebearbeitung'] = $this->app->Secure->GetPOST('stornomanuellebearbeitung');
	$input['stornokommentar'] = $this->app->Secure->GetPOST('stornokommentar');
	$input['stornobezahlt'] = $this->app->Secure->GetPOST('stornobezahlt');
	$input['stornobezahltam'] = $this->app->Secure->GetPOST('stornobezahltam');
	$input['stornobezahltvon'] = $this->app->Secure->GetPOST('stornobezahltvon');
	$input['stornoabgeschlossen'] = $this->app->Secure->GetPOST('stornoabgeschlossen');
	$input['stornorueckzahlungper'] = $this->app->Secure->GetPOST('stornorueckzahlungper');
	$input['stornowareerhaltenretour'] = $this->app->Secure->GetPOST('stornowareerhaltenretour');
	$input['partnerausgezahlt'] = $this->app->Secure->GetPOST('partnerausgezahlt');
	$input['partnerausgezahltam'] = $this->app->Secure->GetPOST('partnerausgezahltam');
	$input['kennen'] = $this->app->Secure->GetPOST('kennen');
	$input['logdatei'] = $this->app->Secure->GetPOST('logdatei');
	$input['bezeichnung'] = $this->app->Secure->GetPOST('bezeichnung');
	$input['datumproduktion'] = $this->app->Secure->GetPOST('datumproduktion');
	$input['anschreiben'] = $this->app->Secure->GetPOST('anschreiben');
	$input['usereditid'] = $this->app->Secure->GetPOST('usereditid');
	$input['useredittimestamp'] = $this->app->Secure->GetPOST('useredittimestamp');
	$input['steuersatz_normal'] = $this->app->Secure->GetPOST('steuersatz_normal');
	$input['steuersatz_zwischen'] = $this->app->Secure->GetPOST('steuersatz_zwischen');
	$input['steuersatz_ermaessigt'] = $this->app->Secure->GetPOST('steuersatz_ermaessigt');
	$input['steuersatz_starkermaessigt'] = $this->app->Secure->GetPOST('steuersatz_starkermaessigt');
	$input['steuersatz_dienstleistung'] = $this->app->Secure->GetPOST('steuersatz_dienstleistung');
	$input['waehrung'] = $this->app->Secure->GetPOST('waehrung');
	$input['schreibschutz'] = $this->app->Secure->GetPOST('schreibschutz');
	$input['pdfarchiviert'] = $this->app->Secure->GetPOST('pdfarchiviert');
	$input['pdfarchiviertversion'] = $this->app->Secure->GetPOST('pdfarchiviertversion');
	$input['typ'] = $this->app->Secure->GetPOST('typ');
	$input['reservierart'] = $this->app->Secure->GetPOST('reservierart');
	$input['auslagerart'] = $this->app->Secure->GetPOST('auslagerart');
	$input['projektfiliale'] = $this->app->Secure->GetPOST('projektfiliale');
	$input['datumauslieferung'] = $this->app->Secure->GetPOST('datumauslieferung');
	$input['datumbereitstellung'] = $this->app->Secure->GetPOST('datumbereitstellung');
	$input['unterlistenexplodieren'] = $this->app->Secure->GetPOST('unterlistenexplodieren');
	$input['charge'] = $this->app->Secure->GetPOST('charge');
	$input['arbeitsschrittetextanzeigen'] = $this->app->Secure->GetPOST('arbeitsschrittetextanzeigen');
	$input['einlagern_ok'] = $this->app->Secure->GetPOST('einlagern_ok');
	$input['auslagern_ok'] = $this->app->Secure->GetPOST('auslagern_ok');
	$input['mhd'] = $this->app->Secure->GetPOST('mhd');
	$input['auftragmengenanpassen'] = $this->app->Secure->GetPOST('auftragmengenanpassen');
	$input['internebezeichnung'] = $this->app->Secure->GetPOST('internebezeichnung');
	$input['mengeoriginal'] = $this->app->Secure->GetPOST('mengeoriginal');
	$input['teilproduktionvon'] = $this->app->Secure->GetPOST('teilproduktionvon');
	$input['teilproduktionnummer'] = $this->app->Secure->GetPOST('teilproduktionnummer');
	$input['parent'] = $this->app->Secure->GetPOST('parent');
	$input['parentnummer'] = $this->app->Secure->GetPOST('parentnummer');
	$input['bearbeiterid'] = $this->app->Secure->GetPOST('bearbeiterid');
	$input['mengeausschuss'] = $this->app->Secure->GetPOST('mengeausschuss');
	$input['mengeerfolgreich'] = $this->app->Secure->GetPOST('mengeerfolgreich');
	$input['abschlussbemerkung'] = $this->app->Secure->GetPOST('abschlussbemerkung');
	$input['auftragid'] = $this->app->Secure->GetPOST('auftragid');
	$input['funktionstest'] = $this->app->Secure->GetPOST('funktionstest');
	$input['seriennummer_erstellen'] = $this->app->Secure->GetPOST('seriennummer_erstellen');
	$input['unterseriennummern_erfassen'] = $this->app->Secure->GetPOST('unterseriennummern_erfassen');
	$input['datumproduktionende'] = $this->app->Secure->GetPOST('datumproduktionende');
	$input['standardlager'] = $this->app->Secure->GetPOST('standardlager');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('DATUM', $input['datum']);
	$this->app->Tpl->Set('ART', $input['art']);
	$this->app->Tpl->Set('PROJEKT', $input['projekt']);
	$this->app->Tpl->Set('BELEGNR', $input['belegnr']);
	$this->app->Tpl->Set('INTERNET', $input['internet']);
	$this->app->Tpl->Set('BEARBEITER', $input['bearbeiter']);
	$this->app->Tpl->Set('ANGEBOT', $input['angebot']);
	$this->app->Tpl->Set('FREITEXT', $input['freitext']);
	$this->app->Tpl->Set('INTERNEBEMERKUNG', $input['internebemerkung']);
	$this->app->Tpl->Set('STATUS', $input['status']);
	$this->app->Tpl->Set('ADRESSE', $input['adresse']);
	$this->app->Tpl->Set('NAME', $input['name']);
	$this->app->Tpl->Set('ABTEILUNG', $input['abteilung']);
	$this->app->Tpl->Set('UNTERABTEILUNG', $input['unterabteilung']);
	$this->app->Tpl->Set('STRASSE', $input['strasse']);
	$this->app->Tpl->Set('ADRESSZUSATZ', $input['adresszusatz']);
	$this->app->Tpl->Set('ANSPRECHPARTNER', $input['ansprechpartner']);
	$this->app->Tpl->Set('PLZ', $input['plz']);
	$this->app->Tpl->Set('ORT', $input['ort']);
	$this->app->Tpl->Set('LAND', $input['land']);
	$this->app->Tpl->Set('USTID', $input['ustid']);
	$this->app->Tpl->Set('UST_BEFREIT', $input['ust_befreit']);
	$this->app->Tpl->Set('UST_INNER', $input['ust_inner']);
	$this->app->Tpl->Set('EMAIL', $input['email']);
	$this->app->Tpl->Set('TELEFON', $input['telefon']);
	$this->app->Tpl->Set('TELEFAX', $input['telefax']);
	$this->app->Tpl->Set('BETREFF', $input['betreff']);
	$this->app->Tpl->Set('KUNDENNUMMER', $input['kundennummer']);
	$this->app->Tpl->Set('VERSANDART', $input['versandart']);
	$this->app->Tpl->Set('VERTRIEB', $input['vertrieb']);
	$this->app->Tpl->Set('ZAHLUNGSWEISE', $input['zahlungsweise']);
	$this->app->Tpl->Set('ZAHLUNGSZIELTAGE', $input['zahlungszieltage']);
	$this->app->Tpl->Set('ZAHLUNGSZIELTAGESKONTO', $input['zahlungszieltageskonto']);
	$this->app->Tpl->Set('ZAHLUNGSZIELSKONTO', $input['zahlungszielskonto']);
	$this->app->Tpl->Set('BANK_INHABER', $input['bank_inhaber']);
	$this->app->Tpl->Set('BANK_INSTITUT', $input['bank_institut']);
	$this->app->Tpl->Set('BANK_BLZ', $input['bank_blz']);
	$this->app->Tpl->Set('BANK_KONTO', $input['bank_konto']);
	$this->app->Tpl->Set('KREDITKARTE_TYP', $input['kreditkarte_typ']);
	$this->app->Tpl->Set('KREDITKARTE_INHABER', $input['kreditkarte_inhaber']);
	$this->app->Tpl->Set('KREDITKARTE_NUMMER', $input['kreditkarte_nummer']);
	$this->app->Tpl->Set('KREDITKARTE_PRUEFNUMMER', $input['kreditkarte_pruefnummer']);
	$this->app->Tpl->Set('KREDITKARTE_MONAT', $input['kreditkarte_monat']);
	$this->app->Tpl->Set('KREDITKARTE_JAHR', $input['kreditkarte_jahr']);
	$this->app->Tpl->Set('FIRMA', $input['firma']);
	$this->app->Tpl->Set('VERSENDET', $input['versendet']);
	$this->app->Tpl->Set('VERSENDET_AM', $input['versendet_am']);
	$this->app->Tpl->Set('VERSENDET_PER', $input['versendet_per']);
	$this->app->Tpl->Set('VERSENDET_DURCH', $input['versendet_durch']);
	$this->app->Tpl->Set('AUTOVERSAND', $input['autoversand']);
	$this->app->Tpl->Set('KEINPORTO', $input['keinporto']);
	$this->app->Tpl->Set('KEINESTORNOMAIL', $input['keinestornomail']);
	$this->app->Tpl->Set('ABWEICHENDELIEFERADRESSE', $input['abweichendelieferadresse']);
	$this->app->Tpl->Set('LIEFERNAME', $input['liefername']);
	$this->app->Tpl->Set('LIEFERABTEILUNG', $input['lieferabteilung']);
	$this->app->Tpl->Set('LIEFERUNTERABTEILUNG', $input['lieferunterabteilung']);
	$this->app->Tpl->Set('LIEFERLAND', $input['lieferland']);
	$this->app->Tpl->Set('LIEFERSTRASSE', $input['lieferstrasse']);
	$this->app->Tpl->Set('LIEFERORT', $input['lieferort']);
	$this->app->Tpl->Set('LIEFERPLZ', $input['lieferplz']);
	$this->app->Tpl->Set('LIEFERADRESSZUSATZ', $input['lieferadresszusatz']);
	$this->app->Tpl->Set('LIEFERANSPRECHPARTNER', $input['lieferansprechpartner']);
	$this->app->Tpl->Set('PACKSTATION_INHABER', $input['packstation_inhaber']);
	$this->app->Tpl->Set('PACKSTATION_STATION', $input['packstation_station']);
	$this->app->Tpl->Set('PACKSTATION_IDENT', $input['packstation_ident']);
	$this->app->Tpl->Set('PACKSTATION_PLZ', $input['packstation_plz']);
	$this->app->Tpl->Set('PACKSTATION_ORT', $input['packstation_ort']);
	$this->app->Tpl->Set('AUTOFREIGABE', $input['autofreigabe']);
	$this->app->Tpl->Set('FREIGABE', $input['freigabe']);
	$this->app->Tpl->Set('NACHBESSERUNG', $input['nachbesserung']);
	$this->app->Tpl->Set('GESAMTSUMME', $input['gesamtsumme']);
	$this->app->Tpl->Set('INBEARBEITUNG', $input['inbearbeitung']);
	$this->app->Tpl->Set('ABGESCHLOSSEN', $input['abgeschlossen']);
	$this->app->Tpl->Set('NACHLIEFERUNG', $input['nachlieferung']);
	$this->app->Tpl->Set('LAGER_OK', $input['lager_ok']);
	$this->app->Tpl->Set('PORTO_OK', $input['porto_ok']);
	$this->app->Tpl->Set('UST_OK', $input['ust_ok']);
	$this->app->Tpl->Set('CHECK_OK', $input['check_ok']);
	$this->app->Tpl->Set('VORKASSE_OK', $input['vorkasse_ok']);
	$this->app->Tpl->Set('NACHNAHME_OK', $input['nachnahme_ok']);
	$this->app->Tpl->Set('RESERVIERT_OK', $input['reserviert_ok']);
	$this->app->Tpl->Set('BESTELLT_OK', $input['bestellt_ok']);
	$this->app->Tpl->Set('ZEIT_OK', $input['zeit_ok']);
	$this->app->Tpl->Set('VERSAND_OK', $input['versand_ok']);
	$this->app->Tpl->Set('PARTNERID', $input['partnerid']);
	$this->app->Tpl->Set('FOLGEBESTAETIGUNG', $input['folgebestaetigung']);
	$this->app->Tpl->Set('ZAHLUNGSMAIL', $input['zahlungsmail']);
	$this->app->Tpl->Set('STORNOGRUND', $input['stornogrund']);
	$this->app->Tpl->Set('STORNOSONSTIGES', $input['stornosonstiges']);
	$this->app->Tpl->Set('STORNORUECKZAHLUNG', $input['stornorueckzahlung']);
	$this->app->Tpl->Set('STORNOBETRAG', $input['stornobetrag']);
	$this->app->Tpl->Set('STORNOBANKINHABER', $input['stornobankinhaber']);
	$this->app->Tpl->Set('STORNOBANKKONTO', $input['stornobankkonto']);
	$this->app->Tpl->Set('STORNOBANKBLZ', $input['stornobankblz']);
	$this->app->Tpl->Set('STORNOBANKBANK', $input['stornobankbank']);
	$this->app->Tpl->Set('STORNOGUTSCHRIFT', $input['stornogutschrift']);
	$this->app->Tpl->Set('STORNOGUTSCHRIFTBELEG', $input['stornogutschriftbeleg']);
	$this->app->Tpl->Set('STORNOWAREERHALTEN', $input['stornowareerhalten']);
	$this->app->Tpl->Set('STORNOMANUELLEBEARBEITUNG', $input['stornomanuellebearbeitung']);
	$this->app->Tpl->Set('STORNOKOMMENTAR', $input['stornokommentar']);
	$this->app->Tpl->Set('STORNOBEZAHLT', $input['stornobezahlt']);
	$this->app->Tpl->Set('STORNOBEZAHLTAM', $input['stornobezahltam']);
	$this->app->Tpl->Set('STORNOBEZAHLTVON', $input['stornobezahltvon']);
	$this->app->Tpl->Set('STORNOABGESCHLOSSEN', $input['stornoabgeschlossen']);
	$this->app->Tpl->Set('STORNORUECKZAHLUNGPER', $input['stornorueckzahlungper']);
	$this->app->Tpl->Set('STORNOWAREERHALTENRETOUR', $input['stornowareerhaltenretour']);
	$this->app->Tpl->Set('PARTNERAUSGEZAHLT', $input['partnerausgezahlt']);
	$this->app->Tpl->Set('PARTNERAUSGEZAHLTAM', $input['partnerausgezahltam']);
	$this->app->Tpl->Set('KENNEN', $input['kennen']);
	$this->app->Tpl->Set('LOGDATEI', $input['logdatei']);
	$this->app->Tpl->Set('BEZEICHNUNG', $input['bezeichnung']);
	$this->app->Tpl->Set('DATUMPRODUKTION', $input['datumproduktion']);
	$this->app->Tpl->Set('ANSCHREIBEN', $input['anschreiben']);
	$this->app->Tpl->Set('USEREDITID', $input['usereditid']);
	$this->app->Tpl->Set('USEREDITTIMESTAMP', $input['useredittimestamp']);
	$this->app->Tpl->Set('STEUERSATZ_NORMAL', $input['steuersatz_normal']);
	$this->app->Tpl->Set('STEUERSATZ_ZWISCHEN', $input['steuersatz_zwischen']);
	$this->app->Tpl->Set('STEUERSATZ_ERMAESSIGT', $input['steuersatz_ermaessigt']);
	$this->app->Tpl->Set('STEUERSATZ_STARKERMAESSIGT', $input['steuersatz_starkermaessigt']);
	$this->app->Tpl->Set('STEUERSATZ_DIENSTLEISTUNG', $input['steuersatz_dienstleistung']);
	$this->app->Tpl->Set('WAEHRUNG', $input['waehrung']);
	$this->app->Tpl->Set('SCHREIBSCHUTZ', $input['schreibschutz']);
	$this->app->Tpl->Set('PDFARCHIVIERT', $input['pdfarchiviert']);
	$this->app->Tpl->Set('PDFARCHIVIERTVERSION', $input['pdfarchiviertversion']);
	$this->app->Tpl->Set('TYP', $input['typ']);
	$this->app->Tpl->Set('RESERVIERART', $input['reservierart']);
	$this->app->Tpl->Set('AUSLAGERART', $input['auslagerart']);
	$this->app->Tpl->Set('PROJEKTFILIALE', $input['projektfiliale']);
	$this->app->Tpl->Set('DATUMAUSLIEFERUNG', $input['datumauslieferung']);
	$this->app->Tpl->Set('DATUMBEREITSTELLUNG', $input['datumbereitstellung']);
	$this->app->Tpl->Set('UNTERLISTENEXPLODIEREN', $input['unterlistenexplodieren']);
	$this->app->Tpl->Set('CHARGE', $input['charge']);
	$this->app->Tpl->Set('ARBEITSSCHRITTETEXTANZEIGEN', $input['arbeitsschrittetextanzeigen']);
	$this->app->Tpl->Set('EINLAGERN_OK', $input['einlagern_ok']);
	$this->app->Tpl->Set('AUSLAGERN_OK', $input['auslagern_ok']);
	$this->app->Tpl->Set('MHD', $input['mhd']);
	$this->app->Tpl->Set('AUFTRAGMENGENANPASSEN', $input['auftragmengenanpassen']);
	$this->app->Tpl->Set('INTERNEBEZEICHNUNG', $input['internebezeichnung']);
	$this->app->Tpl->Set('MENGEORIGINAL', $input['mengeoriginal']);
	$this->app->Tpl->Set('TEILPRODUKTIONVON', $input['teilproduktionvon']);
	$this->app->Tpl->Set('TEILPRODUKTIONNUMMER', $input['teilproduktionnummer']);
	$this->app->Tpl->Set('PARENT', $input['parent']);
	$this->app->Tpl->Set('PARENTNUMMER', $input['parentnummer']);
	$this->app->Tpl->Set('BEARBEITERID', $input['bearbeiterid']);
	$this->app->Tpl->Set('MENGEAUSSCHUSS', $input['mengeausschuss']);
	$this->app->Tpl->Set('MENGEERFOLGREICH', $input['mengeerfolgreich']);
	$this->app->Tpl->Set('ABSCHLUSSBEMERKUNG', $input['abschlussbemerkung']);
	$this->app->Tpl->Set('AUFTRAGID', $input['auftragid']);
	$this->app->Tpl->Set('FUNKTIONSTEST', $input['funktionstest']);
	$this->app->Tpl->Set('SERIENNUMMER_ERSTELLEN', $input['seriennummer_erstellen']);
	$this->app->Tpl->Set('UNTERSERIENNUMMERN_ERFASSEN', $input['unterseriennummern_erfassen']);
	$this->app->Tpl->Set('DATUMPRODUKTIONENDE', $input['datumproduktionende']);
	$this->app->Tpl->Set('STANDARDLAGER', $input['standardlager']);
	
    }

}
