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

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . 
                        "<a href=\"index.php?module=produktion&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;".
                        "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion&action=delete&id=%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . 
                        "</td></tr></table>";

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
			p.id,
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
                    (SELECT projekt.abkuerzung FROM projekt INNER JOIN artikel a WHERE a.projekt = projekt.id AND a.id = p.artikel LIMIT 1) as projekt,
                    (SELECT a.nummer FROM artikel a WHERE a.id = p.artikel LIMIT 1) as name,
                    FORMAT(p.menge,0,'de_DE'),
                    'tbd' as Lager,
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
                $heading = array('','','Nummer', 'Artikel', 'Projekt',  'Planmenge', 'Lager', 'Reserviert', 'Verbraucht', 'Men&uuml;');
                $width = array('1%','1%',  '5%','30%',        '5%',      '1%',        '1%',    '1%',         '1%' ,            '1%'); // Fill out manually later

                $findcols = array('(SELECT a.name FROM artikel a WHERE a.id = p.artikel LIMIT 1)', 'p.projekt', 'p.bezeichnung', 'p.beschreibung', 'p.internerkommentar', 'p.nummer', 'p.menge', 'p.preis', 'p.waehrung', 'p.lieferdatum', 'p.vpe', 'p.sort', 'p.status', 'p.umsatzsteuer', 'p.bemerkung', 'p.geliefert', 'p.geliefert_menge', 'p.explodiert', 'p.explodiert_parent', 'p.logdatei', 'p.nachbestelltexternereinkauf', 'p.beistellung', 'p.externeproduktion', 'p.einheit', 'p.steuersatz', 'p.steuertext', 'p.erloese', 'p.erloesefestschreiben', 'p.freifeld1', 'p.freifeld2', 'p.freifeld3', 'p.freifeld4', 'p.freifeld5', 'p.freifeld6', 'p.freifeld7', 'p.freifeld8', 'p.freifeld9', 'p.freifeld10', 'p.freifeld11', 'p.freifeld12', 'p.freifeld13', 'p.freifeld14', 'p.freifeld15', 'p.freifeld16', 'p.freifeld17', 'p.freifeld18', 'p.freifeld19', 'p.freifeld20', 'p.freifeld21', 'p.freifeld22', 'p.freifeld23', 'p.freifeld24', 'p.freifeld25', 'p.freifeld26', 'p.freifeld27', 'p.freifeld28', 'p.freifeld29', 'p.freifeld30', 'p.freifeld31', 'p.freifeld32', 'p.freifeld33', 'p.freifeld34', 'p.freifeld35', 'p.freifeld36', 'p.freifeld37', 'p.freifeld38', 'p.freifeld39', 'p.freifeld40', 'p.stuecklistestufe', 'p.teilprojekt');
                $searchsql = array('p.produktion', 'p.artikel', 'p.projekt', 'p.bezeichnung', 'p.beschreibung', 'p.internerkommentar', 'p.nummer', 'p.menge', 'p.preis', 'p.waehrung', 'p.lieferdatum', 'p.vpe', 'p.sort', 'p.status', 'p.umsatzsteuer', 'p.bemerkung', 'p.geliefert', 'p.geliefert_menge', 'p.explodiert', 'p.explodiert_parent', 'p.logdatei', 'p.nachbestelltexternereinkauf', 'p.beistellung', 'p.externeproduktion', 'p.einheit', 'p.steuersatz', 'p.steuertext', 'p.erloese', 'p.erloesefestschreiben', 'p.freifeld1', 'p.freifeld2', 'p.freifeld3', 'p.freifeld4', 'p.freifeld5', 'p.freifeld6', 'p.freifeld7', 'p.freifeld8', 'p.freifeld9', 'p.freifeld10', 'p.freifeld11', 'p.freifeld12', 'p.freifeld13', 'p.freifeld14', 'p.freifeld15', 'p.freifeld16', 'p.freifeld17', 'p.freifeld18', 'p.freifeld19', 'p.freifeld20', 'p.freifeld21', 'p.freifeld22', 'p.freifeld23', 'p.freifeld24', 'p.freifeld25', 'p.freifeld26', 'p.freifeld27', 'p.freifeld28', 'p.freifeld29', 'p.freifeld30', 'p.freifeld31', 'p.freifeld32', 'p.freifeld33', 'p.freifeld34', 'p.freifeld35', 'p.freifeld36', 'p.freifeld37', 'p.freifeld38', 'p.freifeld39', 'p.freifeld40', 'p.stuecklistestufe', 'p.teilprojekt');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',p.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=produktion_position&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion_position&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

                $sql = "SELECT SQL_CALC_FOUND_ROWS
                    p.id,
                    $dropnbox,
                    (SELECT a.nummer FROM artikel a WHERE a.id = p.artikel LIMIT 1) as name,
                    (SELECT a.name_de FROM artikel a WHERE a.id = p.artikel LIMIT 1) as name,
                    (SELECT projekt.abkuerzung FROM projekt INNER JOIN artikel a WHERE a.projekt = projekt.id AND a.id = p.artikel LIMIT 1) as projekt,
                    FORMAT(p.menge,0,'de_DE'),
                    'tbd' as Lager,
                    'tbd' as Reserviert,
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
        
        // Check if storno possible -> No partial production yet

	    $geliefert_menge = $this->app->DB->SelectArr("SELECT geliefert_menge FROM produktion_position pp WHERE pp.produktion = $id");
        
        if (empty($geliefert_menge)) {
            $this->app->Tpl->Set('MESSAGE', "<div class=\"info\">Der Eintrag wurde storniert.</div>");           
        } else {
            $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag kann nicht storniert werden, da bereits Buchungen vorhanden sind.</div>");           
        } 

        $this->produktion_list();
    } 

    /*
     * Edit produktion item
     * If id is empty, create a new one
     */
        
    function produktion_edit() {
        $id = $this->app->Secure->GetGET('id');
              
        $submit = $this->app->Secure->GetPOST('submit');

        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=produktion&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=produktion&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();

               
//        foreach ($input as $key => $value) {
//            echo($key." -> ".$value."<br>\n");
//        }


        if (empty($id)) {
            // New item
            $id = 'NULL';        
        } else {
        }

        if ($submit != '')
        {
            switch ($submit) {
                case 'speichern':
                    // Write to database
            
                    // Add checks here
                    $input['standardlager'] = $this->app->erp->ReplaceLagerPlatz(true,$input['standardlager'],true); // Parameters: Target db?, value, from form?

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

                    $sql = "INSERT INTO produktion (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;
                    $this->app->DB->Update($sql);

                    if ($id == 'NULL') {
                        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                        header("Location: index.php?module=produktion&action=list&msg=$msg");
                    } else {
                        $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
                    }
                break;
                case 'planen':
                    
                    // Check 
                    // Parse positions            
                	$sql = "SELECT artikel FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=1";
            	    $produktionsartikel = $this->app->DB->SelectArr($sql);

                    if (!empty($produktionsartikel)) {
                        $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Bereits geplant.</div>");
                        break;                        
                    }

                    $artikel_planen = $this->app->Secure->GetPOST('artikel_planen');
                    $artikel_planen_menge = $this->app->Secure->GetPOST('artikel_planen_menge');
                        
                    $sql = "SELECT id FROM artikel WHERE stueckliste = '1' AND nummer = '".$artikel_planen."'";
              	    $artikel_id = $this->app->DB->SelectArr($sql)[0]['id'];

                    if (!$artikel_id) {
                        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Artikel ist keine St&uuml;ckliste.</div>");
                        break;
                    }

                    // Insert positions

                    $position_array = array();

                    $sql = "SELECT '".$id."' as id, artikel, menge, '0' as stuecklistestufe FROM stueckliste WHERE stuecklistevonartikel = ".$artikel_id;
                    $stueckliste =  $this->app->DB->SelectArr($sql);

                    if (empty($stueckliste)) {
                        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">St&uuml;ckliste ist leer.</div>");
                        break;
                    }

                    foreach ($stueckliste as $key => $value) {                        
                        $value['menge'] = $value['menge'] * $artikel_planen_menge;
                        $position_values[] = '('.implode(",",$value).',\'\')';
                    }

                    $sql = "INSERT INTO produktion_position (produktion, artikel, menge, stuecklistestufe, projekt) VALUES ( $id, $artikel_id, $artikel_planen_menge, 1, ''), ".implode(',',$position_values);

//                    echo($sql);

                    $this->app->DB->Update($sql);                   

                    $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Planung angelegt.</div>");
                break;            
                case 'freigeben':
                    $sql = "UPDATE produktion SET status = 'freigegeben' WHERE id=$id";
                    $this->app->DB->Update($sql);                   
                break;
                case 'produzieren':
                    $sql = "UPDATE produktion SET status = 'gestartet' WHERE id=$id";
                    $this->app->DB->Update($sql);                   
                break;
                case 'abschliessen':
                    $sql = "UPDATE produktion SET status = 'abgeschlossen' WHERE id=$id";
                    $this->app->DB->Update($sql);                   
                break;

            }           
        }

    
        // Load values again from database

        $sql = "SELECT SQL_CALC_FOUND_ROWS 
                p.id,
    			(SELECT pp.bezeichnung FROM produktion_position pp WHERE pp.produktion = p.id AND pp.stuecklistestufe = 1 LIMIT 1) as artikelname,
                p.datum,
                p.art,
                p.projekt,
                p.belegnr,
                p.internet,
                p.bearbeiter,
                p.angebot,
                p.freitext,
                p.internebemerkung,
                p.status,
                p.adresse,
                p.name,
                p.abteilung,
                p.unterabteilung,
                p.strasse,
                p.adresszusatz,
                p.ansprechpartner,
                p.plz,
                p.ort,
                p.land,
                p.ustid,
                p.ust_befreit,
                p.ust_inner,
                p.email,
                p.telefon,
                p.telefax,
                p.betreff,
                p.kundennummer,
                p.versandart,
                p.vertrieb,
                p.zahlungsweise,
                p.zahlungszieltage,
                p.zahlungszieltageskonto,
                p.zahlungszielskonto,
                p.bank_inhaber,
                p.bank_institut,
                p.bank_blz,
                p.bank_konto,
                p.kreditkarte_typ,
                p.kreditkarte_inhaber,
                p.kreditkarte_nummer,
                p.kreditkarte_pruefnummer,
                p.kreditkarte_monat,
                p.kreditkarte_jahr,
                p.firma,
                p.versendet,
                p.versendet_am,
                p.versendet_per,
                p.versendet_durch,
                p.autoversand,
                p.keinporto,
                p.keinestornomail,
                p.abweichendelieferadresse,
                p.liefername,
                p.lieferabteilung,
                p.lieferunterabteilung,
                p.lieferland,
                p.lieferstrasse,
                p.lieferort,
                p.lieferplz,
                p.lieferadresszusatz,
                p.lieferansprechpartner,
                p.packstation_inhaber,
                p.packstation_station,
                p.packstation_ident,
                p.packstation_plz,
                p.packstation_ort,
                p.autofreigabe,
                p.freigabe,
                p.nachbesserung,
                p.gesamtsumme,
                p.inbearbeitung,
                p.abgeschlossen,
                p.nachlieferung,
                p.lager_ok,
                p.porto_ok,
                p.ust_ok,
                p.check_ok,
                p.vorkasse_ok,
                p.nachnahme_ok,
                p.reserviert_ok,
                p.bestellt_ok,
                p.zeit_ok,
                p.versand_ok,
                p.partnerid,
                p.folgebestaetigung,
                p.zahlungsmail,
                p.stornogrund,
                p.stornosonstiges,
                p.stornorueckzahlung,
                p.stornobetrag,
                p.stornobankinhaber,
                p.stornobankkonto,
                p.stornobankblz,
                p.stornobankbank,
                p.stornogutschrift,
                p.stornogutschriftbeleg,
                p.stornowareerhalten,
                p.stornomanuellebearbeitung,
                p.stornokommentar,
                p.stornobezahlt,
                p.stornobezahltam,
                p.stornobezahltvon,
                p.stornoabgeschlossen,
                p.stornorueckzahlungper,
                p.stornowareerhaltenretour,
                p.partnerausgezahlt,
                p.partnerausgezahltam,
                p.kennen,
                p.logdatei,
                p.bezeichnung,
                p.datumproduktion,
                p.anschreiben,
                p.usereditid,
                p.useredittimestamp,
                p.steuersatz_normal,
                p.steuersatz_zwischen,
                p.steuersatz_ermaessigt,
                p.steuersatz_starkermaessigt,
                p.steuersatz_dienstleistung,
                p.waehrung,
                p.schreibschutz,
                p.pdfarchiviert,
                p.pdfarchiviertversion,
                p.typ,
                p.reservierart,
                p.auslagerart,
                p.projektfiliale,
                p.datumauslieferung,
                p.datumbereitstellung,
                p.unterlistenexplodieren,
                p.charge,
                p.arbeitsschrittetextanzeigen,
                p.einlagern_ok,
                p.auslagern_ok,
                p.mhd,
                p.auftragmengenanpassen,
                p.internebezeichnung,
                p.mengeoriginal,
                p.teilproduktionvon,
                p.teilproduktionnummer,
                p.parent,
                p.parentnummer,
                p.bearbeiterid,
                FORMAT(p.mengeausschuss,0) as mengeausschuss,
                FORMAT(p.mengeerfolgreich,0) as mengeerfolgreich,
                p.abschlussbemerkung,
                p.auftragid,
                p.funktionstest,
                p.seriennummer_erstellen,
                p.unterseriennummern_erfassen,
                p.datumproduktionende,
                p.standardlager,
                p.id FROM produktion p"." WHERE id=$id";	

        $produktion_from_db = $this->app->DB->SelectArr($sql)[0];

        foreach ($produktion_from_db as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);             
        }
             
        /*
         * Add displayed items later
         */ 

    	$sql = "SELECT " . $this->app->YUI->IconsSQL_produktion('p') . " AS `icons` FROM produktion p WHERE id=$id";
	    $icons = $this->app->DB->SelectArr($sql);
        $this->app->Tpl->Add('STATUSICONS',  $icons[0]['icons']);

        $this->app->YUI->AutoComplete("projekt", "projektname", 1);
        $this->app->YUI->AutoComplete("kundennummer", "kunde", 1);
        $this->app->YUI->AutoComplete("auftragid", "auftrag", 1);

        $this->app->YUI->AutoComplete("artikel_planen", "artikelnummer", 1);

        $this->app->YUI->AutoComplete("standardlager", "lagerplatz");
        $this->app->Tpl->Set('STANDARDLAGER', $this->app->erp->ReplaceLagerPlatz(false,$produktion_from_db['standardlager'],false)); // Convert ID to form display

        $this->app->YUI->DatePicker("datum");
        $this->app->YUI->DatePicker("datumauslieferung");
        $this->app->YUI->DatePicker("datumbereitstellung");
        $this->app->YUI->DatePicker("datumproduktionende");
        $this->app->YUI->DatePicker("datumproduktion");


        $this->app->YUI->CkEditor("freitext","internal", null, 'JQUERY');
        $this->app->YUI->CkEditor("internebemerkung","internal", null, 'JQUERY');

        // Reparse positions            
    	$sql = "SELECT id,artikel, FORMAT(menge,0) as menge FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=1";
        $produktionsartikel = $this->app->DB->SelectArr($sql)[0];

        $this->app->Tpl->Set('MENGE_GEPLANT',$produktionsartikel['menge']);

        if (empty($produktionsartikel)) {
            $this->app->Tpl->Set('AKTION_FREIGEBEN_VISIBLE','hidden');      
            $this->app->Tpl->Set('ARTIKEL_MENGE_VISIBLE','hidden');            
        } else {                                     
            $this->app->Tpl->Set('AKTION_ARTIKEL_PLANEN_VISIBLE','hidden');
            $this->app->YUI->TableSearch('PRODUKTION_POSITION_TARGET_TABELLE', 'produktion_position_target_list', "show", "", "", basename(__FILE__), __CLASS__);      
            $this->app->YUI->TableSearch('PRODUKTION_POSITION_SOURCE_TABELLE', 'produktion_position_source_list', "show", "", "", basename(__FILE__), __CLASS__);
            $produktionsartikel_id = $produktionsartikel['artikel'];
            $sql = "SELECT name_de,nummer FROM artikel WHERE id=".$produktionsartikel_id;
            $produktionsartikel_name = $this->app->DB->SelectArr($sql)[0]['name_de'];
        }

        $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', $produktionsartikel_name." (Produktion ".$produktion_from_db['belegnr'].")");

        // Action menu
        switch ($produktion_from_db['status']) {
            case 'angelegt':                
                $this->app->Tpl->Set('AKTION_RESERVIEREN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_PRODUZIEREN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_ABSCHLIESSEN_VISIBLE','hidden');
            break;
            case 'freigegeben':
                $this->app->Tpl->Set('AKTION_FREIGEBEN_VISIBLE','hidden');
            break;
            case 'gestartet':
                $this->app->Tpl->Set('AKTION_FREIGEBEN_VISIBLE','hidden');
            break;
            case 'abgeschlossen':
                $this->app->Tpl->Set('AKTION_SPEICHERN_DISABLED','disabled');
                $this->app->Tpl->Set('AKTION_FREIGEBEN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_RESERVIEREN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_PRODUZIEREN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_ABSCHLIESSEN_VISIBLE','hidden');
            break;
            case 'storniert':
                $this->app->Tpl->Set('AKTION_SPEICHERN_DISABLED','disabled');
                $this->app->Tpl->Set('AKTION_FREIGEBEN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_RESERVIEREN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_PRODUZIEREN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_ABSCHLIESSEN_VISIBLE','hidden');
            break;
            default: // new item
                $this->app->Tpl->Set('POSITIONEN_TAB_VISIBLE','hidden="hidden"');
            break;
        }

        $this->app->Tpl->Parse('PAGE', "produktion_edit.tpl");
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();

    	$input['kundennummer'] = $this->app->Secure->GetPOST('kundennummer');
    	$input['projekt'] = $this->app->Secure->GetPOST('projekt');
    	$input['auftragid'] = $this->app->Secure->GetPOST('auftragid');
	    $input['internebezeichnung'] = $this->app->Secure->GetPOST('internebezeichnung');   

        $input['datum'] = $this->app->Secure->GetPOST('datum');
    	$input['standardlager'] = $this->app->Secure->GetPOST('standardlager');

	    $input['reservierart'] = $this->app->Secure->GetPOST('reservierart');
	    $input['auslagerart'] = $this->app->Secure->GetPOST('auslagerart');      
    	$input['unterlistenexplodieren'] = $this->app->Secure->GetPOST('unterlistenexplodieren');
    	$input['funktionstest'] = $this->app->Secure->GetPOST('funktionstest');        
    	$input['arbeitsschrittetextanzeigen'] = $this->app->Secure->GetPOST('arbeitsschrittetextanzeigen');
    	$input['seriennummer_erstellen'] = $this->app->Secure->GetPOST('seriennummer_erstellen');

    	$input['datumauslieferung'] = $this->app->Secure->GetPOST('datumauslieferung');
    	$input['datumbereitstellung'] = $this->app->Secure->GetPOST('datumbereitstellung');
    	$input['datumproduktion'] = $this->app->Secure->GetPOST('datumproduktion');
    	$input['datumproduktionende'] = $this->app->Secure->GetPOST('datumproduktionende');

    	$input['freitext'] = $this->app->Secure->GetPOST('freitext');
    	$input['internebemerkung'] = $this->app->Secure->GetPOST('internebemerkung');

        return $input;
    }

}
