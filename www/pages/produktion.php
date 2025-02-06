<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;

class Produktion {

    // Botched helper function -> Should be replaced with a proper locale solution someday TODO
    function FormatMenge ($value) {
        return(number_format($value,0,',','.')); // DE
//        return(number_format($value,0,'.',',')); // EN
    }

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "produktion_list");
        $this->app->ActionHandler("create", "produktion_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "produktion_edit");
        $this->app->ActionHandler("copy", "produktion_copy");
        $this->app->ActionHandler("minidetail", "produktion_minidetail");
        $this->app->ActionHandler("delete", "produktion_delete");
        $this->app->ActionHandler("pdf", "produktion_pdf");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);

        $this->Install();
    }

    public function Install() {


    }

    public function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "produktion_list":
                $allowed['produktion_list'] = array('list');

//                $heading = array('','','datum', 'art', 'projekt', 'belegnr', 'internet', 'bearbeiter', 'angebot', 'freitext', 'internebemerkung', 'status', 'adresse', 'name', 'abteilung', 'unterabteilung', 'strasse', 'adresszusatz', 'ansprechpartner', 'plz', 'ort', 'land', 'ustid', 'ust_befreit', 'ust_inner', 'email', 'telefon', 'telefax', 'betreff', 'kundennummer', 'versandart', 'vertrieb', 'zahlungsweise', 'zahlungszieltage', 'zahlungszieltageskonto', 'zahlungszielskonto', 'bank_inhaber', 'bank_institut', 'bank_blz', 'bank_konto', 'kreditkarte_typ', 'kreditkarte_inhaber', 'kreditkarte_nummer', 'kreditkarte_pruefnummer', 'kreditkarte_monat', 'kreditkarte_jahr', 'firma', 'versendet', 'versendet_am', 'versendet_per', 'versendet_durch', 'autoversand', 'keinporto', 'keinestornomail', 'abweichendelieferadresse', 'liefername', 'lieferabteilung', 'lieferunterabteilung', 'lieferland', 'lieferstrasse', 'lieferort', 'lieferplz', 'lieferadresszusatz', 'lieferansprechpartner', 'packstation_inhaber', 'packstation_station', 'packstation_ident', 'packstation_plz', 'packstation_ort', 'autofreigabe', 'freigabe', 'nachbesserung', 'gesamtsumme', 'inbearbeitung', 'abgeschlossen', 'nachlieferung', 'lager_ok', 'porto_ok', 'ust_ok', 'check_ok', 'vorkasse_ok', 'nachnahme_ok', 'reserviert_ok', 'bestellt_ok', 'zeit_ok', 'versand_ok', 'partnerid', 'folgebestaetigung', 'zahlungsmail', 'stornogrund', 'stornosonstiges', 'stornorueckzahlung', 'stornobetrag', 'stornobankinhaber', 'stornobankkonto', 'stornobankblz', 'stornobankbank', 'stornogutschrift', 'stornogutschriftbeleg', 'stornowareerhalten', 'stornomanuellebearbeitung', 'stornokommentar', 'stornobezahlt', 'stornobezahltam', 'stornobezahltvon', 'stornoabgeschlossen', 'stornorueckzahlungper', 'stornowareerhaltenretour', 'partnerausgezahlt', 'partnerausgezahltam', 'kennen', 'logdatei', 'bezeichnung', 'datumproduktion', 'anschreiben', 'usereditid', 'useredittimestamp', 'steuersatz_normal', 'steuersatz_zwischen', 'steuersatz_ermaessigt', 'steuersatz_starkermaessigt', 'steuersatz_dienstleistung', 'waehrung', 'schreibschutz', 'pdfarchiviert', 'pdfarchiviertversion', 'typ', 'reservierart', 'auslagerart', 'projektfiliale', 'datumauslieferung', 'datumbereitstellung', 'unterlistenexplodieren', 'charge', 'arbeitsschrittetextanzeigen', 'einlagern_ok', 'auslagern_ok', 'mhd', 'auftragmengenanpassen', 'internebezeichnung', 'mengeoriginal', 'teilproduktionvon', 'teilproduktionnummer', 'parent', 'parentnummer', 'bearbeiterid', 'mengeausschuss', 'mengeerfolgreich', 'abschlussbemerkung', 'auftragid', 'funktionstest', 'seriennummer_erstellen', 'unterseriennummern_erfassen', 'datumproduktionende', 'standardlager', 'Men&uuml;');
                $heading = array('','','Produktion','Kd-Nr.','Kunde','Vom','Bezeichnung','Soll','Ist','Zeit geplant','Zeit gebucht','Projekt','Status','Monitor','Men&uuml;');

                $alignright = array(8,9,10,11);

                $width = array('1%','1%','10%'); // Fill out manually later

                $bezeichnung = "CONCAT (
                            IFNULL((SELECT CONCAT(a.name_de,' (',a.nummer,')','<br>') FROM artikel a INNER JOIN produktion_position pp ON pp.artikel = a.id WHERE pp.stuecklistestufe = 1 AND pp.produktion = p.id LIMIT 1),''),
                            CONCAT('<i>',internebezeichnung,'</i>')
                        )";

                $findcols = array('p.id','p.id','p.belegnr','adresse.kundennummer','adresse.name','p.datum',$bezeichnung,'soll','ist', 'zeit_geplant','zeit_geplant', 'projekt','p.status','icons','id');

                $searchsql = array('p.belegnr','p.kundennummer','p.name',$bezeichnung);

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',p.id,'\" />') AS `auswahl`";

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" .
                        "<a href=\"index.php?module=produktion&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;".
                        "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion&action=delete&id=%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" .
                        "<a href=\"#\" onclick=CopyDialog(\"index.php?module=produktion&action=copy&id=%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>" .
                        "<a href=\"index.php?module=produktion&action=pdf&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/pdf.svg\" border=\"0\"></a>".
                        "</td></tr></table>";


//                $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, $dropnbox, p.datum, p.art, p.projekt, p.belegnr, p.internet, p.bearbeiter, p.angebot, p.freitext, p.internebemerkung, p.status, p.adresse, p.name, p.abteilung, p.unterabteilung, p.strasse, p.adresszusatz, p.ansprechpartner, p.plz, p.ort, p.land, p.ustid, p.ust_befreit, p.ust_inner, p.email, p.telefon, p.telefax, p.betreff, p.kundennummer, p.versandart, p.vertrieb, p.zahlungsweise, p.zahlungszieltage, p.zahlungszieltageskonto, p.zahlungszielskonto, p.bank_inhaber, p.bank_institut, p.bank_blz, p.bank_konto, p.kreditkarte_typ, p.kreditkarte_inhaber, p.kreditkarte_nummer, p.kreditkarte_pruefnummer, p.kreditkarte_monat, p.kreditkarte_jahr, p.firma, p.versendet, p.versendet_am, p.versendet_per, p.versendet_durch, p.autoversand, p.keinporto, p.keinestornomail, p.abweichendelieferadresse, p.liefername, p.lieferabteilung, p.lieferunterabteilung, p.lieferland, p.lieferstrasse, p.lieferort, p.lieferplz, p.lieferadresszusatz, p.lieferansprechpartner, p.packstation_inhaber, p.packstation_station, p.packstation_ident, p.packstation_plz, p.packstation_ort, p.autofreigabe, p.freigabe, p.nachbesserung, p.gesamtsumme, p.inbearbeitung, p.abgeschlossen, p.nachlieferung, p.lager_ok, p.porto_ok, p.ust_ok, p.check_ok, p.vorkasse_ok, p.nachnahme_ok, p.reserviert_ok, p.bestellt_ok, p.zeit_ok, p.versand_ok, p.partnerid, p.folgebestaetigung, p.zahlungsmail, p.stornogrund, p.stornosonstiges, p.stornorueckzahlung, p.stornobetrag, p.stornobankinhaber, p.stornobankkonto, p.stornobankblz, p.stornobankbank, p.stornogutschrift, p.stornogutschriftbeleg, p.stornowareerhalten, p.stornomanuellebearbeitung, p.stornokommentar, p.stornobezahlt, p.stornobezahltam, p.stornobezahltvon, p.stornoabgeschlossen, p.stornorueckzahlungper, p.stornowareerhaltenretour, p.partnerausgezahlt, p.partnerausgezahltam, p.kennen, p.logdatei, p.bezeichnung, p.datumproduktion, p.anschreiben, p.usereditid, p.useredittimestamp, p.steuersatz_normal, p.steuersatz_zwischen, p.steuersatz_ermaessigt, p.steuersatz_starkermaessigt, p.steuersatz_dienstleistung, p.waehrung, p.schreibschutz, p.pdfarchiviert, p.pdfarchiviertversion, p.typ, p.reservierart, p.auslagerart, p.projektfiliale, p.datumauslieferung, p.datumbereitstellung, p.unterlistenexplodieren, p.charge, p.arbeitsschrittetextanzeigen, p.einlagern_ok, p.auslagern_ok, p.mhd, p.auftragmengenanpassen, p.internebezeichnung, p.mengeoriginal, p.teilproduktionvon, p.teilproduktionnummer, p.parent, p.parentnummer, p.bearbeiterid, p.mengeausschuss, p.mengeerfolgreich, p.abschlussbemerkung, p.auftragid, p.funktionstest, p.seriennummer_erstellen, p.unterseriennummern_erfassen, p.datumproduktionende, p.standardlager, p.id FROM produktion p";
//                $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, $dropnbox, p.belegnr, p.kundennummer, p.name, p.datum, \"SUBSELECT\", \"SUBSELECT\", p.mengeerfolgreich, \"-\", \"-\", p.projekt, p.status, p.status, p.id FROM produktion p";
                $sql = "SELECT SQL_CALC_FOUND_ROWS
			            p.id,
			            $dropnbox,
			            p.belegnr,
			            adresse.kundennummer,
			            adresse.name as name,
                        DATE_FORMAT(datum,'%d.%m.%Y') as datum,

                        ".$bezeichnung." as bezeichnung,

			            FORMAT((SELECT SUM(menge) FROM produktion_position pp WHERE pp.produktion = p.id AND pp.stuecklistestufe = 1),0,'de_DE') as soll,
			            FORMAT(p.mengeerfolgreich,0,'de_DE') as ist,
			            \"-\" as zeit_geplant,
			            \"-\" as zeit_erfasst,
			            (SELECT projekt.abkuerzung FROM projekt WHERE p.projekt = projekt.id LIMIT 1) as projekt,
			            p.status,
	                    (" . $app->YUI->IconsSQL_produktion('p') . ")  AS `icons`,
			            p.id
			            FROM produktion p
			            LEFT JOIN adresse ON adresse.id = p.adresse
                        ";

                $where = "0";

                  // Toggle filters
                $this->app->Tpl->Add('JQUERYREADY', "$('#angelegte').click( function() { fnFilterColumn1( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#offene').click( function() { fnFilterColumn2( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#geschlossene').click( function() { fnFilterColumn3( 0 ); } );");
                $this->app->Tpl->Add('JQUERYREADY', "$('#stornierte').click( function() { fnFilterColumn4( 0 ); } );");

                for ($r = 1;$r <= 4;$r++) {
                  $this->app->Tpl->Add('JAVASCRIPT', '
                                         function fnFilterColumn' . $r . ' ( i )
                                         {
                                         if(oMoreData' . $r . $name . '==1)
                                         oMoreData' . $r . $name . ' = 0;
                                         else
                                         oMoreData' . $r . $name . ' = 1;

                                         $(\'#' . $name . '\').dataTable().fnFilter(
                                           \'\',
                                           i,
                                           0,0
                                           );
                                         }
                                         ');
                }


                $more_data1 = $this->app->Secure->GetGET("more_data1");
                if ($more_data1 == 1) {
                   $where .= "  OR p.status IN ('angelegt')";
                } else {
                }

                $more_data2 = $this->app->Secure->GetGET("more_data2");
                if ($more_data2 == 1) {
                  $where .= " OR p.status IN ('freigegeben','gestartet')";
                }
                else {
                }

                $more_data3 = $this->app->Secure->GetGET("more_data3");
                if ($more_data3 == 1) {
                  $where .= " OR p.status IN ('abgeschlossen')";
                }
                else {
                }

                $more_data4 = $this->app->Secure->GetGET("more_data4");
                if ($more_data4 == 1) {
                  $where .= " OR p.status IN ('storniert')";
                }
                else {
                }
                // END Toggle filters

                $moreinfo = true; // Allow drop down details
                $menucol = 14; // For moredata

                if ($where=='0') {
                 $where = " p.status IN ('freigegeben','gestartet')";
                }

                $count = "SELECT count(DISTINCT p.id) FROM produktion p INNER JOIN produktion_position pp ON pp.produktion = pp.id WHERE $where";
//                $groupby = "";

                break;
            case "produktion_position_source_list":
                $id = $app->Secure->GetGET('id');

                $sql = "SELECT standardlager FROM produktion WHERE id=$id";
           	    $standardlager = $app->DB->SelectArr($sql)[0]['standardlager'];

                $allowed['produktion_position_list'] = array('list');

            	$sql = "SELECT menge FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=1";
        	    $produktionsmenge = $app->DB->SelectArr($sql)[0]['menge'];

                // Get status to control UI element menu availability
                $sql = "SELECT p.status from produktion p WHERE p.id = $id";
                $result = $app->DB->SelectArr($sql)[0];
                $status = $result['status'];

                if (in_array($status,array('angelegt','freigegeben'))) {
                    $heading = array('','','Nummer', 'Artikel', 'Projekt', 'Planmenge pro St&uuml;ck', 'Lager alle (verf&uuml;gbar)', 'Lager (verf&uuml;gbar)', 'Reserviert', 'Planmenge', 'Verbraucht', 'Men&uuml;');
                    $width = array(  '1%','1%','5%', '30%',     '5%',      '1%',                       '1%',                          '1%' ,                    '1%',         '1%',        '1%'         ,'1%');
                    $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=produktion_position&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion_position&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";
                } else {
                    $heading = array('','','Nummer', 'Artikel', 'Projekt','Planmenge pro St&uuml;ck', 'Lager alle (verf&uuml;gbar)',  'Lager (verf&uuml;gbar)', 'Reserviert', 'Planmenge', 'Verbraucht', '');
                    $width = array(  '1%','1%','5%', '30%',     '5%',      '1%',                       '1%',                          '1%' ,                    '1%',         '1%',        '1%'         ,'1%');
                    $menu = "";
                }

                $alignright = array(6,7,8,9,10);

                $findcols = array('','p.artikel','(SELECT a.nummer FROM artikel a WHERE a.id = p.artikel LIMIT 1)','(SELECT a.name_de FROM artikel a WHERE a.id = p.artikel LIMIT 1)','projekt','stueckmenge','lageralle','lager','reserviert','menge','geliefert_menge');
                $searchsql = array('p.artikel','nummer','name','projekt','lager','menge','reserviert','geliefert_menge');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $dropnbox = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`, CONCAT('<input type=\"checkbox\" name=\"auswahl[]\" value=\"',p.id,'\" />') AS `auswahl`";

                $sql = "SELECT SQL_CALC_FOUND_ROWS
                    p.id,
                    $dropnbox,
                    (SELECT a.nummer FROM artikel a WHERE a.id = p.artikel LIMIT 1) as nummer,
                    (SELECT a.name_de FROM artikel a WHERE a.id = p.artikel LIMIT 1) as name,
                    (SELECT projekt.abkuerzung FROM projekt INNER JOIN artikel a WHERE a.projekt = projekt.id AND a.id = p.artikel LIMIT 1) as projekt,
                    FORMAT(p.menge/$produktionsmenge,0,'de_DE') as stueckmenge,
                    IF ((SELECT lagerartikel FROM artikel a WHERE a.id = p.artikel LIMIT 1) != 0,
                    CONCAT (
                        FORMAT (IFNULL((SELECT SUM(menge) FROM lager_platz_inhalt lpi WHERE lpi.artikel = p.artikel),0),0,'de_DE'),
                        ' (',
                        FORMAT (
                                IFNULL((SELECT SUM(menge) FROM lager_platz_inhalt lpi WHERE lpi.artikel = p.artikel),0)-
                                IFNULL((SELECT SUM(menge) FROM lager_reserviert r WHERE r.artikel = p.artikel),0),
                                0,
                                'de_DE'
                        ),
                        ')'
                    ),'') as lageralle,
                    if (('$standardlager' != '0') && ((SELECT lagerartikel FROM artikel a WHERE a.id = p.artikel LIMIT 1) != 0),
                        CONCAT (
                            FORMAT (IFNULL((SELECT SUM(menge) FROM lager_platz_inhalt lpi WHERE lpi.lager_platz = $standardlager AND lpi.artikel = p.artikel),0),0,'de_DE'),
                            ' (',
                            FORMAT (
                                    IFNULL((SELECT SUM(menge) FROM lager_platz_inhalt lpi WHERE lpi.lager_platz = $standardlager AND lpi.artikel = p.artikel),0)-
                                    IFNULL((SELECT SUM(menge) FROM lager_reserviert r WHERE r.lager_platz = $standardlager AND r.artikel = p.artikel),0),
                                    0,
                                    'de_DE'
                            ),
                            ')'
                        )
                        ,''
                    ) as lager,
                    FORMAT ((SELECT SUM(menge) FROM lager_reserviert r WHERE r.lager_platz = $standardlager AND r.artikel = p.artikel AND r.objekt = 'produktion' AND r.parameter = $id AND r.posid = p.id),0,'de_DE') as Reserviert,
                    FORMAT(p.menge,0,'de_DE'),
                    FORMAT(p.geliefert_menge,0,'de_DE') as geliefert_menge,
                    p.id
                    FROM produktion_position p";

                $where = " stuecklistestufe = 0 AND produktion = $id";

                $count = "SELECT count(DISTINCT id) FROM produktion_position WHERE $where";
//                $groupby = "";

                break;
            case "produktion_source_list": // Aggregated per artikel
                $id = $app->Secure->GetGET('id');

            	$sql = "SELECT menge FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=1";
        	    $produktionsmenge = $app->DB->SelectArr($sql)[0]['menge'];

                $sql = "SELECT standardlager FROM produktion WHERE id=$id";
           	    $standardlager = $app->DB->SelectArr($sql)[0]['standardlager'];

                $allowed['produktion_position_list'] = array('list');
                $heading = array('','Nummer', 'Artikel', 'Projekt','Planmenge pro St&uuml;ck', 'Lager alle (verf&uuml;gbar)' ,'Lager (verf&uuml;gbar)', 'Reserviert','Planmenge', 'Verbraucht','');
                $width = array('1%','5%',     '30%',        '5%',      '1%',        '1%',      '1%',      '1%' ,         '1%',               '1%'            ,'1%');

                $alignright = array(5,6,7,8,9);

                $findcols = array('p.artikel','(SELECT a.nummer FROM artikel a WHERE a.id = p.artikel LIMIT 1)','(SELECT a.name_de FROM artikel a WHERE a.id = p.artikel LIMIT 1)','projekt','stueckmenge','lageralle','lager','reserviert','menge','geliefert_menge');

                $searchsql = array('p.artikel','nummer','name','projekt','lager','menge','reserviert','geliefert_menge');

                $defaultorder = 1;
                $defaultorderdesc = 0;

		        $drop = "'<img src=./themes/new/images/details_open.png class=details>' AS `open`";

                $sql = "SELECT SQL_CALC_FOUND_ROWS
                    p.artikel,
    		        $drop,
                    (SELECT a.nummer FROM artikel a WHERE a.id = p.artikel LIMIT 1) as nummer,
                    (SELECT a.name_de FROM artikel a WHERE a.id = p.artikel LIMIT 1) as name,
                    (SELECT projekt.abkuerzung FROM projekt INNER JOIN artikel a WHERE a.projekt = projekt.id AND a.id = p.artikel LIMIT 1) as projekt,
                    FORMAT(SUM(p.menge)/$produktionsmenge,0,'de_DE') as stueckmenge,
                    IF ((SELECT lagerartikel FROM artikel a WHERE a.id = p.artikel LIMIT 1) != 0,
                    CONCAT (
                        FORMAT (IFNULL((SELECT SUM(menge) FROM lager_platz_inhalt lpi WHERE lpi.artikel = p.artikel),0),0,'de_DE'),
                        ' (',
                        FORMAT (
                                IFNULL((SELECT SUM(menge) FROM lager_platz_inhalt lpi WHERE lpi.artikel = p.artikel),0)-
                                IFNULL((SELECT SUM(menge) FROM lager_reserviert r WHERE r.artikel = p.artikel),0),
                                0,
                                'de_DE'
                        ),
                        ')'
                    ),'') as lageralle,
                    if (('$standardlager' != '0') && ((SELECT lagerartikel FROM artikel a WHERE a.id = p.artikel LIMIT 1) != 0),
                        CONCAT (
                            FORMAT (IFNULL((SELECT SUM(menge) FROM lager_platz_inhalt lpi WHERE lpi.lager_platz = $standardlager AND lpi.artikel = p.artikel),0),0,'de_DE'),
                            ' (',
                            FORMAT (
                                    IFNULL((SELECT SUM(menge) FROM lager_platz_inhalt lpi WHERE lpi.lager_platz = $standardlager AND lpi.artikel = p.artikel),0)-
                                    IFNULL((SELECT SUM(menge) FROM lager_reserviert r WHERE r.lager_platz = $standardlager AND r.artikel = p.artikel),0),
                                    0,
                                    'de_DE'
                            ),
                            ')'
                        )
                        ,''
                    ) as lager,
                    FORMAT ((SELECT SUM(menge) FROM lager_reserviert r WHERE r.lager_platz = $standardlager AND r.artikel = p.artikel AND r.objekt = 'produktion' AND r.parameter = $id),0,'de_DE') as reserviert,
                    FORMAT(SUM(p.menge),0,'de_DE') as menge,
                    FORMAT(SUM(p.geliefert_menge),0,'de_DE') as geliefert_menge,
                    p.id
                    FROM produktion_position p";

                $where = " stuecklistestufe = 0 AND produktion = $id";

                $count = "SELECT count(DISTINCT id) FROM produktion_position WHERE $where";
                $groupby = " GROUP BY p.artikel ";

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

        $this->StatusBerechnen(0); // all open ones

        $this->app->YUI->TableSearch('TAB1', 'produktion_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "produktion_list.tpl");
    }

    public function produktion_delete() {
        $id = (int) $this->app->Secure->GetGET('id');

        // Check if storno possible -> No partial production yet

	    $geliefert_menge = $this->app->DB->SelectArr("SELECT SUM(geliefert_menge) as menge FROM produktion_position pp WHERE pp.produktion = $id")[0]['menge'];

        if ($geliefert_menge == 0) {

            $sql = "UPDATE produktion SET status='storniert' WHERE id = '$id'";
            $this->app->DB->Update($sql);
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

    function produktion_edit($id = NULL) {

        if (empty($id)) {
            $id = $this->app->Secure->GetGET('id');
        }
        if($this->app->erp->DisableModul('produktion',$id))
        {
          return;
        }

        $submit = $this->app->Secure->GetPOST('submit');

        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=produktion&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=produktion&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $input = $this->GetInput();
        $msg = $this->app->erp->base64_url_decode($this->app->Secure->GetGET('msg'));

        $sql = "SELECT status, belegnr, projekt, standardlager FROM produktion WHERE id = '$id'";
        $from_db = $this->app->DB->SelectArr($sql)[0];
        $global_status = $from_db['status'];
        $global_produktionsnummer = $from_db['belegnr'];
        $global_projekt = $from_db['projekt'];
        $global_standardlager = $from_db['standardlager'];

//        foreach ($input as $key => $value) {
//            echo($key." -> ".$value."<br>\n");
//        }

        $this->app->Tpl->Set('MESSAGE', "");

        if (empty($id)) {
            // New item
            $id = 'NULL';

        } else {
        }

        if ($submit != '')
        {

            $msg = "";

            switch ($submit) {
                case 'speichern':
                    // Write to database

                    // Add checks here

                    if (empty($input['datum'])) {
                        $input['datum'] = date("Y-m-d");
                    } else {
                        $input['datum'] = $this->app->erp->ReplaceDatum(true,$input['datum'],true);
                    }

                    if ($id == 'NULL') {
                        $input['status'] = 'angelegt';
                    }

                    $input['datumauslieferung'] = $this->app->erp->ReplaceDatum(true,$input['datumauslieferung'],true);
                    $input['datumbereitstellung'] = $this->app->erp->ReplaceDatum(true,$input['datumbereitstellung'],true);
                    $input['datumproduktion'] = $this->app->erp->ReplaceDatum(true,$input['datumproduktion'],true);
                    $input['datumproduktionende'] = $this->app->erp->ReplaceDatum(true,$input['datumproduktionende'],true);

                    $input['adresse'] = $this->app->erp->ReplaceKunde(true,$input['adresse'],true);
                    $input['auftragid'] = $this->app->erp->ReplaceAuftrag(true,$input['auftrag'],true);
                    unset($input['auftrag']);
                    $input['projekt'] = $this->app->erp->ReplaceProjekt(true,$input['projekt'],true);

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

                        $id = $this->app->DB->GetInsertID();

                        if (!empty($id)) {
                            $this->ProtokollSchreiben($id,'Produktion angelegt');
                            $msg = "<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>"; // Overwrite old MSG
                            $msg = $this->app->erp->base64_url_encode($msg);
                            header("Location: index.php?module=produktion&action=edit&id=$id&msg=$msg");
                        }

                    } else {
                        $msg .= "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>";
                    }
                break;
                case 'planen':

                    // Check
                    // Parse positions
                	$sql = "SELECT artikel FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=1";
            	    $produktionsartikel = $this->app->DB->SelectArr($sql);

                    if (!empty($produktionsartikel)) {
                        $msg .= "<div class=\"success\">Bereits geplant.</div>";
                        break;
                    }

                    $artikel_planen_id = $this->app->erp->ReplaceArtikel(true, $this->app->Secure->GetPOST('artikel_planen'),true); // Convert from form to artikel number
                    $artikel_planen_menge = $this->app->Secure->GetPOST('artikel_planen_menge');

                    if (!$artikel_planen_id) {
                        $msg .= "<div class=\"error\">Artikel ist keine St&uuml;ckliste.</div>";
                        break;
                    }

                    if ($artikel_planen_menge < 1) {
                        $msg .= "<div class=\"error\">Ung&uuml;ltige Planmenge.</div>";
                        break;
                    }

                    // Insert positions

                    $position_array = array();

                    $sql = "SELECT '".$id."' as id, artikel, menge, '0' as stuecklistestufe FROM stueckliste WHERE stuecklistevonartikel = ".$artikel_planen_id;
                    $stueckliste =  $this->app->DB->SelectArr($sql);

                    if (empty($stueckliste)) {
                        $msg .= "<div class=\"error\">St&uuml;ckliste ist leer.</div>";
                        break;
                    }

                    foreach ($stueckliste as $key => $value) {
                        $value['menge'] = $value['menge'] * $artikel_planen_menge;
                        $position_values[] = '('.implode(",",$value).',\'\')';
                    }

                    $sql = "INSERT INTO produktion_position (produktion, artikel, menge, stuecklistestufe, projekt) VALUES ( $id, $artikel_planen_id, $artikel_planen_menge, 1, '$global_projekt'), ".implode(',',$position_values);
                    $this->app->DB->Update($sql);

                    $msg .= "<div class=\"success\">Planung angelegt.</div>";
                    $this->ProtokollSchreiben($id,"Produktion geplant ($artikel_planen_menge)");

                break;
                case 'freigeben':
                    $this->app->erp->BelegFreigabe("produktion",$id);
                    $this->ProtokollSchreiben($id,'Produktion freigegeben');
                break;
                case 'reservieren':

                    // Check quantities and reserve for every position

                    if($global_standardlager == 0) {
                        break;
                    }

                    $fortschritt = $this->MengeFortschritt($id,$global_standardlager);

                    if (empty($fortschritt)) {
                        break;
                    }

              	    $sql = "SELECT pp.id, pp.artikel, a.name_de, a.nummer, pp.menge as menge, pp.geliefert_menge as geliefert_menge FROM produktion_position pp INNER JOIN artikel a ON a.id = pp.artikel WHERE pp.produktion=$id AND pp.stuecklistestufe=0";
            	    $materialbedarf = $this->app->DB->SelectArr($sql);

                    // Try to reserve material
                    $reservierung_durchgefuehrt = false;
                    foreach ($materialbedarf as $materialbedarf_position) {

                        // Calculate new needed quantity if there is scrap
                        $materialbedarf_position['menge'] = $materialbedarf_position['menge']*($fortschritt['ausschuss']+$fortschritt['geplant'])/$fortschritt['geplant'];

                        $result = $this->ArtikelReservieren($materialbedarf_position['artikel'], $global_standardlager, $materialbedarf_position['menge']-$materialbedarf_position['geliefert_menge'], 0, 'produktion', $id, $materialbedarf_position['id'],"Produktion $global_produktionsnummer");
                        if ($result > 0) {
                            $reservierung_durchgefuehrt = true;
                        }
                    }

                    // Message output
                    if ($reservierung_durchgefuehrt) {
                         $msg .= "<div class=\"info\">Reservierung durchgeführt.</div>";
                    } else {
                         $msg .= "<div class=\"error\">Keine Reservierung durchgeführt!</div>";
                    }
                    break;
                case 'produzieren':

                    // Check quanitites
                    // Parse positions
                	$sql = "SELECT artikel, menge, geliefert_menge FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=1";
            	    $produktionsartikel_position = $this->app->DB->SelectArr($sql)[0];

                    if (empty($produktionsartikel_position)) {
                        $msg .= "<div class=\"error\">Keine Planung vorhanden.</div>";
                        break;
                    }

                    $menge_produzieren = $this->app->Secure->GetPOST('menge_produzieren');
                    if (empty($menge_produzieren)) {
                        $menge_produzieren = 0;
                    }
                    $menge_ausschuss = $this->app->Secure->GetPOST('menge_ausschuss_produzieren');
                    if (empty($menge_ausschuss)) {
                        $menge_ausschuss = 0;
                    }

                    $menge_plan = $produktionsartikel_position['menge'];
                    $menge_geliefert = $produktionsartikel_position['geliefert_menge'];

                    $menge_auslagern = $menge_produzieren+$menge_ausschuss;

                    if ($menge_auslagern < 1) {
                         $msg .= "<div class=\"error\">Ung&uuml;ltige Menge.</div>";
                        break;
                    }

                    $menge_moeglich = $this->LagerCheckProduktion($id, $global_standardlager, false);

                    if ($menge_auslagern > $menge_moeglich) {
                        $msg .= "<div class=\"error\">Lagermenge nicht ausreichend. ($menge_auslagern > $menge_moeglich)</div>";
                        break;
                    }

                    $sql = "UPDATE produktion SET status = 'gestartet' WHERE id=$id";
                    $this->app->DB->Update($sql);

                	  $sql = "SELECT pp.id, pp.artikel, pp.menge, pp.geliefert_menge, pp.stuecklistestufe, a.lagerartikel FROM produktion_position pp INNER JOIN artikel a ON a.id = pp.artikel WHERE pp.produktion=$id";
            	     $material = $this->app->DB->SelectArr($sql);

                    foreach ($material as $material_position) {

                        // Calculate quantity to be removed
                        $menge_artikel_auslagern =  $material_position['menge']/$produktionsartikel_position['menge']*$menge_auslagern;

                        // Remove material from stock
                        if ($material_position['stuecklistestufe'] == 0 && $material_position['lagerartikel']) {
                            $result = $this->app->erp->LagerAuslagernRegal($material_position['artikel'],$global_standardlager,$menge_artikel_auslagern,$global_projekt,'Produktion '.$global_produktionsnummer);
                            if ($result != 1) {
                                $msg .= "<div class=\"error\">Kritischer Fehler beim Ausbuchen! (Position ".$material_position['id'].", Menge ".$menge_artikel_auslagern.").</div>".
                                $error = true;
                                break;
                            }
                            // Adjust reservation
                            $result = $this->ArtikelReservieren($material_position['artikel'],$global_standardlager,-$menge_artikel_auslagern,0,'produktion',$id,$material_position['id'],"Produktion $global_produktionsnummer");
                        }

                        // Update position
                        $sql = "UPDATE produktion_position SET geliefert_menge = geliefert_menge + $menge_artikel_auslagern WHERE id = ".$material_position['id'];

                        $this->app->DB->Update($sql);
                    }

                    if ($error) {
                       break;
                    }

                    // Insert produced parts into stock
                    // Check target stock, if not existing, use default stock of article, if not given use production stock


                	$ziellager_from_form = $this->app->erp->ReplaceLagerPlatz(true,$this->app->Secure->GetPOST('ziellager'),true); // Parameters: Target db?, value, from form?

                    $use_artikel_lager = false;
                    $ziellager =  $global_standardlager;

                    if (!empty($ziellager_from_form)) {
                        $sql = "SELECT id FROM lager_platz WHERE id = ".$ziellager_from_form;
                        $result = $this->app->DB->SelectArr($sql);
                        if (!empty($result)) {
                            $ziellager = $ziellager_from_form;
                        } else {
                            $use_artikel_lager = true;
                        }
                    } else {
                        $use_artikel_lager = true;
                    }

                    if ($use_artikel_lager) {
                        $sql = "SELECT lager_platz FROM artikel WHERE id = ".$produktionsartikel_position['artikel'];
                        $result = $this->app->DB->SelectArr($sql);
                        if (!empty($result) && !empty($result[0]['lager_platz'])) {
                            $ziellager = $result[0]['lager_platz'];
                        } else {

                        }
                    } else {

                    }
                    $sql = "SELECT kurzbezeichnung FROM lager_platz WHERE id = $ziellager";
                    $lagername = $this->app->DB->SelectArr($sql)[0]['kurzbezeichnung'];

                    // ERPAPI
                    //   function LagerEinlagern($artikel,$menge,$regal,$projekt,$grund="",$importer="",$paketannahme="",$doctype = "", $doctypeid = 0, $vpeid = 0, $permanenteinventur = 0, $adresse = 0)
                    $this->app->erp->LagerEinlagern($produktionsartikel_position['artikel'],$menge_produzieren,$ziellager,$global_projekt,"Produktion $global_produktionsnummer");
                    // No error handling in LagerEinlagern...

                    $sql = "UPDATE produktion SET mengeerfolgreich = mengeerfolgreich + $menge_produzieren, mengeausschuss = mengeausschuss + $menge_ausschuss WHERE id = $id";
                    $this->app->DB->Update($sql);

                    if ($menge_produzieren > 0) {
                        $lagertext = ", eingelagert in $lagername";
                    }
                    $text = "Produktion durchgef&uuml;hrt ($menge_produzieren erfolgreich, $menge_ausschuss Ausschuss)$lagertext";
                    $msg .= "<div class=\"info\">$text.</div>";
                    $this->ProtokollSchreiben($id,$text);

                break;
                case 'teilen':

                    // Create partial production

                    $menge_abteilen = $this->app->Secure->GetPOST('menge_produzieren');

                    $fortschritt = $this->MengeFortschritt($id,$global_standardlager);

                    if (empty($fortschritt)) {
                        break;
                    }

                    if (($menge_abteilen < 1) || ($menge_abteilen > $fortschritt['offen'])) {
                        $msg .= "<div class=\"error\">Ung&uuml;ltige Teilmenge.</div>";
                        break;
                    }

                    $sql = "SELECT * from produktion WHERE id = $id";
            	    $produktion_alt = $this->app->DB->SelectArr($sql)[0];

                    // Part production of part production -> select parent
                    $hauptproduktion_id = $produktion_alt['teilproduktionvon'];
                    if ($hauptproduktion_id != 0) {
                        $sql = "SELECT belegnr FROM produktion WHERE id = $hauptproduktion_id";
                        $hauptproduktion_belegnr = $this->app->DB->SelectArr($sql)[0]['belegnr'];
                    } else {
                        $hauptproduktion_id = $produktion_alt['id'];
                        $hauptproduktion_belegnr = $produktion_alt['belegnr'];
                    }

                    $sql = "SELECT MAX(teilproduktionnummer) as tpn FROM produktion WHERE teilproduktionvon = $hauptproduktion_id";
                    $teilproduktionnummer = $this->app->DB->SelectArr($sql)[0]['tpn'];
                    if (empty($teilproduktionnummer) || $teilproduktionnummer == 0) {
                        $teilproduktionnummer = '1';
                    } else {
                        $teilproduktionnummer++;
                    }

                    $produktion_neu = array();
                    $produktion_neu['status'] = 'angelegt';
                    $produktion_neu['datum'] = date("Y-m-d");
                    $produktion_neu['art'] = $produktion_alt['art'];
                    $produktion_neu['projekt'] = $produktion_alt['projekt'];
                    $produktion_neu['angebot'] = $produktion_alt['angebot'];
                    $produktion_neu['kundennummer'] = $produktion_alt['kundennummer'];
                    $produktion_neu['auftragid'] = $produktion_alt['auftragid'];
                    $produktion_neu['freitext'] = $produktion_alt['freitext'];
                    $produktion_neu['internebemerkung'] = $produktion_alt['internebemerkung'];
                    $produktion_neu['adresse'] = $produktion_alt['adresse'];
                    $produktion_neu['internebemerkung'] = $produktion_alt['internebemerkung'];
                    $produktion_neu['internebezeichnung '] = $produktion_alt['internebezeichnung'];
                    $produktion_neu['standardlager'] = $produktion_alt['standardlager'];
                    $produktion_neu['belegnr'] = $hauptproduktion_belegnr."-".$teilproduktionnummer;
                    $produktion_neu['teilproduktionvon'] = $hauptproduktion_id;
                    $produktion_neu['teilproduktionnummer'] = $teilproduktionnummer;

                    $columns = "";
                    $values = "";
                    $update = "";

                    $fix = "";

                    foreach ($produktion_neu as $key => $value) {
                        $columns = $columns.$fix.$key;
                        $values = $values.$fix."'".$value."'";
                        $update = $update.$fix.$key." = '$value'";
                        $fix = ", ";
                    }

                    $sql = "INSERT INTO produktion (".$columns.") VALUES (".$values.")";
                    $this->app->DB->Update($sql);
                    $produktion_neu_id = $this->app->DB->GetInsertID();

                    // Now add the positions
                    $sql = "SELECT * FROM produktion_position WHERE produktion = $id";
                    $positionen = $this->app->DB->SelectArr($sql);

                    foreach ($positionen as $position) {

                        $columns = "";

                        // Preserve these values
                        $pos_id = $position['id'];
                        $geliefert_menge = $position['geliefert_menge'];
                        $menge = $position['menge'];
                        $produktion_alt_id = $position['produktion'];
                        $menge_pro_stueck = $menge/$fortschritt['geplant'];

                        // For the new positions
                        $position['id'] = 'NULL';
                        $position['geliefert_menge'] = 0;

                        $position['menge'] = $menge_abteilen*$menge_pro_stueck;
                        $position['produktion'] = $produktion_neu_id;

                        $values = "";
                        $fix = "";
                        foreach ($position as $key => $value) {
                            $columns = $columns.$fix.$key;
                            $values = $values.$fix."'".$value."'";
                            $fix = ", ";
                        }
                        $sql = "INSERT INTO produktion_position (".$columns.") VALUES (".$values.")";
                        $this->app->DB->Update($sql);

                        // For the old positions
                        // Reduce positions in old production
                        $position['id'] = $pos_id;
                        $position['geliefert_menge'] = $geliefert_menge;
                        $position['menge'] = $menge - $position['menge']; // old quantity - partial quantity
                        $position['produktion'] = $produktion_alt_id;

                        $fix = "";
                        $update = "";
                        foreach ($position as $key => $value) {
                            $update = $update.$fix.$key." = '".($value)."'";
                            $fix = ", ";
                        }

                        $sql = "UPDATE produktion_position SET $update WHERE id = $pos_id";
                        $this->app->DB->Update($sql);

                        // Free surplus reservations
                        $restreservierung = $menge_pro_stueck * $fortschritt['offen']-$menge_abteilen;
                        $result = $this->ArtikelReservieren($position['artikel'],$global_standardlager,0,$restreservierung,'produktion',$id,$position['id'],"Produktion $global_produktionsnummer");

                    }

                    $this->ProtokollSchreiben($id,"Teilproduktion erstellt: ".$produktion_neu['belegnr']." (Menge $menge_abteilen)");
                    $msg = "<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>"; // Overwrite old MSG
                    $msg = $this->app->erp->base64_url_encode($msg);
                    header("Location: index.php?module=produktion&action=edit&id=$produktion_neu_id&msg=$msg");

                break;
                case 'leeren':

                    if ($global_status == 'angelegt' || $global_status == 'freigegeben') {
                        $sql = "SELECT id, artikel, menge, geliefert_menge FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=0";
                	    $material = $this->app->DB->SelectArr($sql);
                        foreach ($material as $material_position) {
                            // Remove reservation
                            $result = $this->ArtikelReservieren($material_position['artikel'],$global_standardlager,0,0,'produktion',$id,$material_position['id'],"Produktion $global_produktionsnummer");
                        }
                        $sql = "DELETE FROM produktion_position WHERE produktion = $id";
                        $this->app->DB->Update($sql);
                        $msg .= "<div class=\"warning\">Planung geleert.</div>";
                    } else {
                        $msg .= "<div class=\"error\">Planung kann nicht geleert werden.</div>";
                    }

                break;
                case 'anpassen':

                    if ($global_status == 'angelegt' || $global_status == 'freigegeben' || $global_status == 'gestartet') {

                        $menge_anpassen = $this->app->Secure->GetPOST('menge_produzieren');

                        if (empty($menge_anpassen)) {
                            $msg .= "<div class=\"error\">Ung&uuml;ltige Planmenge.</div>";
                            break;
                        }

                        $fortschritt = $this->MengeFortschritt($id,$global_standardlager);

                        $result = $this->MengeAnpassen($id,$menge_anpassen,$global_standardlager);

                        if ($result == -1) {
                              $msg .= "<div class=\"error\">Ung&uuml;ltige Planmenge.</div>";
                        } else {
                             $msg .= "<div class=\"info\">Planmenge angepasst.</div>";
                        }
                    }

                    $this->ProtokollSchreiben($id,"Menge angepasst auf ".$this->FormatMenge($menge_anpassen));

                break;
                case 'abschliessen':
                    $sql = "UPDATE produktion SET status = 'abgeschlossen' WHERE id=$id";
                    $this->app->DB->Update($sql);

                	$sql = "SELECT id, artikel, menge, geliefert_menge FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=0";
            	    $material = $this->app->DB->SelectArr($sql);

                    foreach ($material as $material_position) {
                        // Remove reservation
                        $result = $this->ArtikelReservieren($material_position['artikel'],$global_standardlager,0,0,'produktion',$id,$material_position['id'],"Produktion $global_produktionsnummer");
                    }

                    $this->ProtokollSchreiben($id,'Produktion abgeschlossen');

                break;
                case 'etikettendrucken':

                    $menge_drucken = $this->app->Secure->GetPOST('menge_produzieren');

                    if ($menge_drucken) {
                        $sql = "SELECT artikel FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=1";
                	    $produktionsartikel_position = $this->app->DB->SelectArr($sql)[0];
                        $produktionsartikel_id = $produktionsartikel_position['artikel'];

                        $sql = "SELECT al.* FROM article_label al INNER JOIN artikel a ON a.id = al.article_id WHERE type = 'produktion' AND al.article_id = ".$produktionsartikel_id;
                        $produktionsetiketten = $this->app->DB->SelectArr($sql);

                        if (!empty($produktionsetiketten)) {
                            foreach ($produktionsetiketten as $produktionsetikett) {
                                $this->app->erp->EtikettenDrucker(
                                    kennung: $produktionsetikett['label_id'],
                                    anzahl: $menge_drucken*$produktionsetikett['amount'],
                                    tabelle: 'artikel',
                                    id: $produktionsartikel_id,
                                    variables: null,
                                    druckercode: $produktionsetikett['printer_id']
                                );
                            }
                        }
                    } else {
                        $msg .= "<div class=\"error\">Ung&uuml;ltige Menge.</div>";
                    }
                break;
            }
        }

        // Load values again from database
        // toDo: cleanup

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
                p.mengeausschuss,
                p.mengeerfolgreich,
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

        $this->StatusBerechnen((int)$id);

    	$sql = "SELECT " . $this->app->YUI->IconsSQL_produktion('p') . " AS `icons` FROM produktion p WHERE id=$id";
	    $icons = $this->app->DB->SelectArr($sql);
        $this->app->Tpl->Add('STATUSICONS',  $icons[0]['icons']);

        if ($produktion_from_db['teilproduktionvon'] != 0) {
            $sql = "SELECT belegnr FROM produktion WHERE id = ".$produktion_from_db['teilproduktionvon'];
    	    $hauptproduktion_belegnr = $this->app->DB->SelectArr($sql)[0]['belegnr'];
            $this->app->Tpl->Set('TEILPRODUKTIONINFO',"Teilproduktion von ".$hauptproduktion_belegnr);
        }

        $sql = "SELECT belegnr FROM produktion WHERE teilproduktionvon = $id";
	    $teilproduktionen = $this->app->DB->SelectArr($sql);

        if (!empty($teilproduktionen)) {
            $this->app->Tpl->Set('TEILPRODUKTIONINFO',"Zu dieser Produktion geh&ouml;ren die Teilproduktionen: ".implode(', ',array_column($teilproduktionen,'belegnr')));
        }

        if($produktion_from_db['standardlager'] == 0) {
            $msg .= "<div class=\"error\">Kein Materiallager ausgew&auml;hlt.</div>";
        }

        $this->app->Tpl->Set('PROJEKT',$this->app->erp->ReplaceProjekt(false,$produktion_from_db['projekt'],false));
        $this->app->Tpl->Set('ADRESSE',$this->app->erp->ReplaceKunde(false,$produktion_from_db['adresse'],false));
        $this->app->Tpl->Set('AUFTRAG',$this->app->erp->ReplaceAuftrag(false,$produktion_from_db['auftragid'],false));

        $this->app->YUI->AutoComplete("projekt", "projektname", 1);
        $this->app->YUI->AutoComplete("adresse", "kunde", 1);
        $this->app->YUI->AutoComplete("auftrag", "auftrag", 1);

        $this->app->YUI->AutoComplete("artikel_planen", "stuecklistenartikel");
        $this->app->YUI->AutoComplete("artikel_hinzu", "artikelnummer");

        $this->app->YUI->AutoComplete("standardlager", "lagerplatz");
        $this->app->YUI->AutoComplete("ziellager", "lagerplatz");

        $this->app->YUI->AutoComplete("artikel", "artikelnummer");

        $this->app->Tpl->Set('STANDARDLAGER', $this->app->erp->ReplaceLagerPlatz(false,$produktion_from_db['standardlager'],false)); // Convert ID to form display

        $this->app->YUI->DatePicker("datum");
        $this->app->Tpl->Set('DATUM',$this->app->erp->ReplaceDatum(false,$produktion_from_db['datum'],true));

        $this->app->YUI->DatePicker("datumauslieferung");
        $this->app->Tpl->Set('DATUMAUSLIEFERUNG',$this->app->erp->ReplaceDatum(false,$produktion_from_db['datumauslieferung'],false));

        $this->app->YUI->DatePicker("datumbereitstellung");
        $this->app->Tpl->Set('DATUMBEREITSTELLUNG',$this->app->erp->ReplaceDatum(false,$produktion_from_db['datumbereitstellung'],false));

        $this->app->YUI->DatePicker("datumproduktion");
        $this->app->Tpl->Set('DATUMPRODUKTION',$this->app->erp->ReplaceDatum(false,$produktion_from_db['datumproduktion'],false));

        $this->app->YUI->DatePicker("datumproduktionende");
        $this->app->Tpl->Set('DATUMPRODUKTIONENDE',$this->app->erp->ReplaceDatum(false,$produktion_from_db['datumproduktionende'],false));

        $this->app->YUI->CkEditor("freitext","internal", null, 'JQUERY');
        $this->app->YUI->CkEditor("internebemerkung","internal", null, 'JQUERY');

        /*
            UI Elements

            AKTION_SPEICHERN_DISABLED
            AKTION_PLANEN_VISIBLE
            AKTION_FREIGEBEN_VISIBLE
            AKTION_RESERVIEREN_VISIBLE
            AKTION_PRODUZIEREN_VISIBLE
            AKTION_ETIKETTEN_DRUCKEN_DISABLED
            AKTION_ABSCHLIESSEN_VISIBLE
            POSITIONEN_TAB_VISIBLE
        */

        // Reparse positions
    	$sql = "SELECT id,artikel, menge, geliefert_menge FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=1";
        $produktionsartikel_position = $this->app->DB->SelectArr($sql)[0];

        // Not planned
        if (empty($produktionsartikel_position)) {

            $this->app->Tpl->Set('AKTION_FREIGEBEN_VISIBLE','hidden');
            $this->app->Tpl->Set('ARTIKEL_MENGE_VISIBLE','hidden');
            $this->app->Tpl->Set('AKTION_PRODUZIEREN_VISIBLE','hidden');
            $this->app->Tpl->Set('AKTION_LEEREN_VISIBLE','hidden');
            $this->app->Tpl->Set('AKTION_RESERVIEREN_VISIBLE','hidden');
            $this->app->Tpl->Set('AKTION_TEILEN_VISIBLE','hidden');
        } else {
        // Planned

            $fortschritt = $this->MengeFortschritt((int) $id, 0);

            if (!empty($fortschritt)) {
                $this->app->Tpl->Set('MENGE_GEPLANT',$this->FormatMenge($fortschritt['geplant']));
                $this->app->Tpl->Set('MENGE_PRODUZIERT',$this->FormatMenge($fortschritt['produziert']));
                $this->app->Tpl->Set('MENGE_OFFEN',$this->FormatMenge($fortschritt['offen']));
                $this->app->Tpl->Set('MENGE_RESERVIERT',$this->FormatMenge($fortschritt['reserviert']));
                $this->app->Tpl->Set('MENGE_PRODUZIERBAR',$this->FormatMenge($fortschritt['produzierbar']));
                $this->app->Tpl->Set('MENGE_ERFOLGREICH',$this->FormatMenge($fortschritt['erfolgreich']));
                $this->app->Tpl->Set('MENGE_AUSSCHUSS',$this->FormatMenge($fortschritt['ausschuss']));
            }

            if ($fortschritt['produziert'] > $fortschritt['geplant']) {
                $msg .= "<div class=\"info\">Planmenge überschritten.</div>";
            }

            $this->app->Tpl->Set('AKTION_PLANEN_VISIBLE','hidden');
            $this->app->YUI->TableSearch('PRODUKTION_POSITION_SOURCE_POSITION_TABELLE', 'produktion_position_source_list', "show", "", "", basename(__FILE__), __CLASS__);
            $this->app->YUI->TableSearch('PRODUKTION_POSITION_SOURCE_TABELLE', 'produktion_source_list', "show", "", "", basename(__FILE__), __CLASS__);
            $produktionsartikel_id = $produktionsartikel_position['artikel'];

            $sql = "SELECT name_de,nummer FROM artikel WHERE id=".$produktionsartikel_id;
            $produktionsartikel = $this->app->DB->SelectArr($sql)[0];
            $produktionsartikel_name = $produktionsartikel['name_de'];
            $produktionsartikel_nummer = $produktionsartikel['nummer'];

            $sql = "SELECT al.* FROM article_label al INNER JOIN artikel a ON a.id = al.article_id WHERE type = 'produktion'";


            $sql = "SELECT artikel FROM produktion_position pp WHERE produktion=$id AND stuecklistestufe=1";

            $sql = "SELECT al.* FROM article_label al INNER JOIN artikel a ON a.id = al.article_id WHERE type = 'produktion' AND al.article_id = ".$produktionsartikel_id;
            $produktionsetiketten = $this->app->DB->SelectArr($sql);
        }

        if (empty($produktionsetiketten)) {
            $this->app->Tpl->Set('AKTION_ETIKETTEN_DRUCKEN_DISABLED','disabled');
        }

        if (empty($produktion_from_db['belegnr'])) {
            $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', 'ENTWURF - '.$produktionsartikel_name." (".$produktionsartikel_nummer.")");
        } else {
            $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', $produktion_from_db['belegnr']." ".$produktionsartikel_name." (<a href=\"index.php?module=artikel&action=edit&id=".$produktionsartikel_id."\">".$produktionsartikel_nummer."</a>)", html: true);
        }

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
                $this->app->Tpl->Set('AKTION_PLANEN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_LEEREN_VISIBLE','hidden');
            break;
            case 'abgeschlossen':
            case 'storniert':
                $this->app->Tpl->Set('AKTION_SPEICHERN_DISABLED','disabled');
                $this->app->Tpl->Set('AKTION_PLANEN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_FREIGEBEN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_RESERVIEREN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_PRODUZIEREN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_TEILEN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_ABSCHLIESSEN_VISIBLE','hidden');
                $this->app->Tpl->Set('AKTION_LEEREN_VISIBLE','hidden');
            break;
            default: // new item
                $this->app->Tpl->Set('POSITIONEN_TAB_VISIBLE','hidden="hidden"');
            break;
        }

        $this->app->Tpl->Set('PRODUKTION_ID',$id);

        $this->app->Tpl->Set('MESSAGE', $msg);
        $this->produktion_minidetail('MINIDETAILINEDIT');
        $this->app->Tpl->Parse('PAGE', "produktion_edit.tpl");

    }

    /*
        Create a copy as draft
    */

    function produktion_copy() {
        $id = (int) $this->app->Secure->GetGET('id');
        if (empty($id)) {
            return;
        }
        $result = $this->Copy($id,0);
        if ($result <= 0) {
            $msg .= "<div class=\"error\">Fehler beim Anlegen der Kopie.</div>";
            $this->app->Tpl->Set('MESSAGE', $msg);
            $this->produktion_list();
        }
        else {
            $msg .= "<div class=\"success\">Kopie angelegt. $result new $id old</div>";
            $this->app->Tpl->Set('MESSAGE', $msg);
            $this->produktion_edit((int) $result);
        }
    }

    public function produktion_minidetail($parsetarget='',$menu=true) {

        $id = $this->app->Secure->GetGET('id');

        $fortschritt = $this->MengeFortschritt((int) $id, 0);

        if (!empty($fortschritt)) {
            $this->app->Tpl->Set('MINI_MENGE_GEPLANT',$this->FormatMenge($fortschritt['geplant']));
            $this->app->Tpl->Set('MINI_MENGE_PRODUZIERT',$this->FormatMenge($fortschritt['produziert']));
            $this->app->Tpl->Set('MINI_MENGE_OFFEN',$this->FormatMenge($fortschritt['offen']));
            $this->app->Tpl->Set('MINI_MENGE_RESERVIERT',$this->FormatMenge($fortschritt['reserviert']));
            $this->app->Tpl->Set('MINI_MENGE_PRODUZIERBAR',$this->FormatMenge($fortschritt['produzierbar']));
            $this->app->Tpl->Set('MINI_MENGEERFOLGREICH',$this->FormatMenge($fortschritt['erfolgreich']));
            $this->app->Tpl->Set('MINI_MENGEAUSSCHUSS',$this->FormatMenge($fortschritt['ausschuss']));
        }

        $this->ProtokollTabelleErzeugen($id, 'PROTOKOLL');

        if($parsetarget=='')
        {
            $this->app->Tpl->Output('produktion_minidetail.tpl');
            $this->app->ExitXentral();
        }

        $this->app->Tpl->Parse($parsetarget,'produktion_minidetail.tpl');
    }


    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();

        $input['adresse'] = $this->app->Secure->GetPOST('adresse');
        $input['projekt'] = $this->app->Secure->GetPOST('projekt');
        $input['auftrag'] = $this->app->Secure->GetPOST('auftrag');
    	    	
	    $input['internebezeichnung'] = $this->app->Secure->GetPOST('internebezeichnung');

        $input['datum'] = $this->app->Secure->GetPOST('datum');
    	$input['standardlager'] = $this->app->Secure->GetPOST('standardlager');
        $input['standardlager'] = $this->app->erp->ReplaceLagerPlatz(true,$input['standardlager'],true); // Parameters: Target db?, value, from form?

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

    // Check stock situation and reservation
    // Return possible production quantity for all stock or just the reserved
    function LagerCheckProduktion(int $produktion_id, int $lager, bool $only_reservations) : int {

        $menge_moeglich = PHP_INT_MAX;

  	    $sql = "SELECT pp.id, artikel, SUM(menge) as menge, geliefert_menge FROM produktion_position pp INNER JOIN artikel a ON pp.artikel = a.id WHERE pp.produktion=$produktion_id AND pp.stuecklistestufe=0 AND a.lagerartikel != 0 GROUP BY artikel";
	    $materialbedarf_gesamt = $this->app->DB->SelectArr($sql);

  	    $sql = "SELECT id, artikel, SUM(menge) as menge, geliefert_menge as geliefert_menge FROM produktion_position pp WHERE produktion=$produktion_id AND stuecklistestufe=1 GROUP BY artikel";
        $result =  $this->app->DB->SelectArr($sql)[0];
	    $menge_plan_gesamt = $result['menge'];

        if ($menge_plan_gesamt == 0) {
            return(0);
        }

  	    $sql = "SELECT SUM(mengeerfolgreich) as menge FROM produktion WHERE id=$produktion_id";
        $result =  $this->app->DB->SelectArr($sql)[0];
	    $menge_geliefert_gesamt = $result['menge'];

        foreach ($materialbedarf_gesamt as $materialbedarf_artikel) {

            $artikel = $materialbedarf_artikel['artikel'];
            $position = $materialbedarf_artikel['id'];
            $menge_plan_artikel = $materialbedarf_artikel['menge'];
            $menge_geliefert = $materialbedarf_artikel['menge_geliefert'];

            $sql = "SELECT SUM(menge) as menge FROM lager_reserviert r WHERE lager_platz=$lager AND artikel = $artikel AND r.objekt = 'produktion' AND r.parameter = $produktion_id";
    	    $menge_reserviert_diese = $this->app->DB->SelectArr($sql)[0]['menge'];

            if ($only_reservations) {
                $menge_verfuegbar = $menge_reserviert_diese;
            } else {
                $sql = "SELECT SUM(menge) as menge FROM lager_platz_inhalt WHERE lager_platz=$lager AND artikel = $artikel";
        	    $menge_lager = $this->app->DB->SelectArr($sql)[0]['menge'];

                $sql = "SELECT SUM(menge) as menge FROM lager_reserviert r WHERE lager_platz=$lager AND artikel = $artikel";
    	        $menge_reserviert_lager = $this->app->DB->SelectArr($sql)[0]['menge'];

                $sql = "SELECT SUM(menge) as menge FROM lager_reserviert r WHERE artikel = $artikel";
        	    $menge_reserviert_gesamt = $this->app->DB->SelectArr($sql)[0]['menge'];

                $menge_verfuegbar = $menge_lager-$menge_reserviert_lager+$menge_reserviert_diese;
            }

            $menge_moeglich_artikel = round($menge_verfuegbar / ($menge_plan_artikel/$menge_plan_gesamt), 0, PHP_ROUND_HALF_DOWN);

            if ($menge_moeglich_artikel < $menge_moeglich) {
                $menge_moeglich = $menge_moeglich_artikel;
            }

//          echo("------------------------Lager $lager a $artikel menge_plan_artikel $menge_plan_artikel menge_geliefert $menge_geliefert menge_lager $menge_lager menge_reserviert_diese $menge_reserviert_diese menge_reserviert_gesamt $menge_reserviert_gesamt menge_verfuegbar $menge_verfuegbar menge_moeglich_artikel $menge_moeglich_artikel menge_moeglich $menge_moeglich<br>");

        }

        if ($menge_moeglich < 0) {
            $menge_moeglich = 0;
        }


        return($menge_moeglich);
    }

    // Modify or add reservation
    // If quantity is negative, the existing reservation will be reduced
    // If current quantity is higher as menge_reservieren_limit, current quantity will be reduced
    // Returns amount that is reserved
    function ArtikelReservieren(int $artikel, $lager, int $menge_reservieren, int $menge_reservieren_limit, string $objekt, int $objekt_id, int $position_id, string $text) : int {

        if($lager <= 0 || $artikel <= 0 || $position_id <= 0) {
            return 0;
        }

    	$sql = "SELECT menge FROM lager_reserviert WHERE objekt='$objekt' AND parameter = $objekt_id AND artikel = $artikel AND lager_platz = $lager AND posid = $position_id";
        $menge_reserviert_diese = $this->app->DB->SelectArr($sql)[0]['menge'];
        if ($menge_reserviert_diese == null) {
            $menge_reserviert_diese = 0;
        }

        $sql = "SELECT menge FROM lager_reserviert WHERE artikel = $artikel AND lager_platz = $lager";
        $menge_reserviert_lager_platz = $this->app->DB->SelectArr($sql)[0]['menge'];
        if ($menge_reserviert_lager_platz == null) {
            $menge_reserviert_lager_platz = 0;
        }
    	
    	$sql = "SELECT menge FROM lager_platz_inhalt WHERE artikel = $artikel AND lager_platz = $lager";
        $menge_lager = $this->app->DB->SelectArr($sql)[0]['menge'];
        if ($menge_lager == null) {
            $menge_lager = 0;
        }

        if ($menge_reservieren < 0) { // Relative reduction
            $menge_reservieren = $menge_reserviert_diese+$menge_reservieren;

            if ($menge_reservieren < 0) {
                $menge_reservieren = 0;
            }
        }

        if (($menge_reservieren == 0) && ($menge_reservieren_limit <= 0)) {
            $sql = "DELETE FROM lager_reserviert WHERE objekt = '$objekt' AND parameter = $objekt_id AND artikel = $artikel AND posid = $position_id";
            $this->app->DB->Update($sql);
            return(0);
        }

        $menge_lager_reservierbar = $menge_lager - $menge_reserviert_lager_platz + $menge_reserviert_diese;

        if ($menge_reservieren_limit > 0) {
            if ($menge_reserviert_diese > $menge_reservieren_limit) {
                $menge_reservieren = $menge_reservieren_limit;
            } else {
                // Nothing to do
                return($menge_reserviert_diese);
            }
        }

        if ($menge_lager_reservierbar > 0) {
            if ($menge_reserviert_diese > 0) {
                // Modify given entry
                if ($menge_reservieren > $menge_lager_reservierbar) {
                    $menge_reservieren = $menge_lager_reservierbar; // Take all that is there
                }
                $sql = "UPDATE lager_reserviert SET menge = $menge_reservieren WHERE objekt = '$objekt' AND parameter = $objekt_id AND artikel = $artikel AND posid = $position_id";
                $this->app->DB->Update($sql);
            } else {
                // Create new entry
                if ($menge_reservieren > $menge_lager_reservierbar) {
                    $menge_reservieren = $menge_lager_reservierbar; // Take all that is there
                }
                $sql = "INSERT INTO lager_reserviert (menge,objekt,parameter,artikel,posid,lager_platz,grund) VALUES (".
                        $menge_reservieren.",".
                        "'$objekt',".
                        $objekt_id.",".
                        $artikel.",".
                        $position_id.",".
                        $lager.",".
                        "'$text'".
                        ")";
                $this->app->DB->Update($sql);
            }
        } else {
            $menge_reservieren = 0;
        }

        return ($menge_reservieren);

    }


    /*
        Adjust the planned quantity of a produktion
        Lower limit is the already produced quantity
        Return -1 if not possible, else 1
    */
    function MengeAnpassen(int $produktion_id, int $menge_neu, int $lager) : int {

        $fortschritt = $this->MengeFortschritt($produktion_id,$lager);

        $sql = "SELECT menge,geliefert_menge FROM produktion_position WHERE produktion = $produktion_id AND stuecklistestufe = 1";
        $produktionsmengen_alt = $this->app->DB->SelectArr($sql)[0];

        if (empty($produktionsmengen_alt)) {
            return(-1);
        }
        if ($menge_neu < $produktionsmengen_alt['geliefert_menge']) {
            return(-1);
        }

        $sql = "SELECT * from produktion WHERE id = $produktion_id";
        $produktion_alt = $this->app->DB->SelectArr($sql)[0];

        // Process positions
        $sql = "SELECT * FROM produktion_position WHERE produktion = $produktion_id";
        $positionen = $this->app->DB->SelectArr($sql);

        foreach ($positionen as $position) {
            $menge_pro_stueck = $position['menge']/$produktionsmengen_alt['menge'];
            $position_menge_neu = $menge_neu*$menge_pro_stueck;
            $sql = "UPDATE produktion_position SET menge=".$position_menge_neu." WHERE id =".$position['id'];
            $this->app->DB->Update($sql);

            // Free surplus reservations
            $restreservierung = $menge_pro_stueck * ($menge_neu+$fortschritt['ausschuss']-$fortschritt['produziert']);

            $result = $this->ArtikelReservieren($position['artikel'],$lager,0,$restreservierung,'produktion',$produktion_id,$position['id'],"Produktion ".$produktion_alt['belegnr']);
        }
        return(1);
    }

    /*
    Output progress information
    ['geplant']
    ['produziert']
    ['erfolgreich']
    ['ausschuss']
    ['offen']
    ['reserviert']
    ['produzierbar']

    If lager <= 0 -> use lager from database

    */
    function MengeFortschritt(int $produktion_id, int $lager) : array {
        $result = array();

        if ($lager <= 0) {
            $sql = "SELECT standardlager FROM produktion WHERE id = $produktion_id";
            $lager = $this->app->DB->SelectArr($sql)[0]['standardlager'];
        }

        $sql = "SELECT menge as geplant, geliefert_menge as produziert FROM produktion_position WHERE produktion = $produktion_id AND stuecklistestufe = 1";
        $position_values = $this->app->DB->SelectArr($sql)[0];

        if (empty($position_values)) {
            return($result);
        }

        $sql = "SELECT mengeerfolgreich as erfolgreich, mengeausschuss as ausschuss FROM produktion WHERE id = $produktion_id";
        $produktion_values = $this->app->DB->SelectArr($sql)[0];

        if (empty($produktion_values)) {
            return($result);
        }

        $result['geplant'] = $position_values['geplant'];
        $result['produziert'] = $position_values['produziert'];

        $result['erfolgreich'] = $produktion_values['erfolgreich'];
        $result['ausschuss'] = $produktion_values['ausschuss'];

        $result['offen'] = $result['geplant']-$result['erfolgreich'];

        if (empty($lager)) {
            $result['reserviert'] = 0;
            $result['produzierbar'] = 0;
        } else {
            $result['reserviert'] = $this->LagerCheckProduktion($produktion_id, $lager, true);
            $result['produzierbar'] = $this->LagerCheckProduktion($produktion_id, $lager, false);
        }

        return($result);
    }

    // Do calculations for the status icon display
    // id = 0 for all open ones
    function StatusBerechnen(int $produktion_id) {

        $where = "WHERE status IN ('freigegeben','gestartet') ";

        if ($produktion_id > 0) {
            $where .= "AND id = $produktion_id";
        }

        $sql = "SELECT id, lager_ok, reserviert_ok, auslagern_ok, einlagern_ok, zeit_ok, versand_ok FROM produktion ".$where;
        $produktionen = $this->app->DB->SelectArr($sql);

        foreach ($produktionen as $produktion) {

            $produktion_id = $produktion['id'];

            $fortschritt = $this->MengeFortschritt($produktion_id,-1);

            if (empty($fortschritt)) {
                continue;
            }

            // lager_ok
            if ($fortschritt['produzierbar'] >= $fortschritt['offen']) {
                $values['lager_ok'] = 1;
    //        } else if ($fortschritt['produzierbar'] > 0) {
    //            $values['lager_ok'] = 2;
            } else {
                $values['lager_ok'] = 0;
            }

            // reserviert_ok
            if ($fortschritt['reserviert'] >= $fortschritt['offen']) {
                $values['reserviert_ok'] = 1;
    //        } else if ($fortschritt['reserviert'] > 0) {
    //            $values['reserviert_ok'] = 2;
            } else {
                $values['reserviert_ok'] = 0;
            }

            // auslagern_ok
            if ($fortschritt['produziert'] >= $fortschritt['geplant']) {
                $values['auslagern_ok'] = 1;
    //        } else if ($fortschritt['produziert'] > 0) {
    //            $values['auslagern_ok'] = 2;
            } else {
                $values['auslagern_ok'] = 0;
            }

            // einlagern_ok
            if ($fortschritt['erfolgreich'] >= $fortschritt['geplant']) {
                $values['einlagern_ok'] = 1;
    //        } else if ($fortschritt['erfolgreich'] > 0) {
    //            $values['einlagern_ok'] = 2;
            } else {
                $values['einlagern_ok'] = 0;
            }

            // reserviert_ok
            if ($fortschritt['produziert'] >= $fortschritt['geplant']) {
                $values['auslagern_ok'] = 1;
    //        } else if ($fortschritt['produziert'] > 0) {
    //            $values['auslagern_ok'] = 2;
            } else {
                $values['auslagern_ok'] = 0;
            }

            $fix = "";
            $update = "";
            foreach ($values as $key => $value) {
                $update = $update.$fix.$key." = '".($value)."'";
                $fix = ", ";
            }

            $sql = "UPDATE produktion SET $update WHERE id = $produktion_id";
            $this->app->DB->Update($sql);
        }
    }

    // Copy an existing produktion as draft, with option to adjust the quantity
    // return id on sucess, else negative number

    function Copy($produktion_id, $menge_abteilen) : int {

        if (empty($produktion_id)) {
            return(-1);
        }

        $fortschritt = $this->MengeFortschritt($produktion_id,0);
        if (empty($fortschritt)) {
            return(-2);
        }

        if ($menge_abteilen < 1) {
            $menge_abteilen = $fortschritt['geplant'];
        }

        $sql = "SELECT * from produktion WHERE id = $produktion_id";
	    $produktion_alt = $this->app->DB->SelectArr($sql)[0];

        if (empty($produktion_alt)) {
            return (-3);
        }

        $menge_pro_stueck = $menge_abteilen/$fortschritt['geplant'];

        $produktion_neu = array();
        $produktion_neu['status'] = 'angelegt';
        $produktion_neu['datum'] = date("Y-m-d");
        $produktion_neu['art'] = $produktion_alt['art'];
        $produktion_neu['projekt'] = $produktion_alt['projekt'];
        $produktion_neu['angebot'] = $produktion_alt['angebot'];
        $produktion_neu['adresse'] = $produktion_alt['adresse'];
        $produktion_neu['auftragid'] = $produktion_alt['auftragid'];
        $produktion_neu['freitext'] = $produktion_alt['freitext'];
        $produktion_neu['internebemerkung'] = $produktion_alt['internebemerkung'];
        $produktion_neu['adresse'] = $produktion_alt['adresse'];

        if ($produktion_alt['belegnr'] != '') {
            $produktion_neu['internebezeichnung '] = "Kopie von ".$produktion_alt['belegnr']." ".$produktion_alt['internebezeichnung'];
        } else {
            $produktion_neu['internebezeichnung '] = $produktion_alt['internebezeichnung'];
        }

        $produktion_neu['standardlager'] = $produktion_alt['standardlager'];


        $columns = "";
        $values = "";
        $update = "";

        $fix = "";

        foreach ($produktion_neu as $key => $value) {
            $columns = $columns.$fix.$key;
            $values = $values.$fix."'".$value."'";
            $update = $update.$fix.$key." = '$value'";
            $fix = ", ";
        }

        $sql = "INSERT INTO produktion (".$columns.") VALUES (".$values.")";
        $this->app->DB->Update($sql);
        $produktion_neu_id = $this->app->DB->GetInsertID();

        // Now add the positions
        $sql = "SELECT * FROM produktion_position WHERE produktion = $produktion_id";
        $positionen = $this->app->DB->SelectArr($sql);

        foreach ($positionen as $position) {

            $columns = "";

            // For the new positions
            $position['id'] = 'NULL';
            $position['geliefert_menge'] = 0;

            $position['menge'] = $menge_abteilen*$menge_pro_stueck;
            $position['produktion'] = $produktion_neu_id;

            $values = "";
            $fix = "";
            foreach ($position as $key => $value) {
                $columns = $columns.$fix.$key;
                $values = $values.$fix."'".$value."'";
                $fix = ", ";
            }
            $sql = "INSERT INTO produktion_position (".$columns.") VALUES (".$values.")";
            $this->app->DB->Update($sql);

        }

        return($produktion_neu_id);
    }

    /*
        Write something into the log
    */
    function ProtokollSchreiben(int $produktion_id, string $text) {
        $sql = "INSERT INTO produktion_protokoll (produktion, zeit, bearbeiter, grund) VALUES ($produktion_id, NOW(), '".$this->app->DB->real_escape_string($this->app->User->GetName())."','".$this->app->DB->real_escape_string($text)."')";
        $this->app->DB->Insert($sql);
    }

    function ProtokollTabelleErzeugen($produktion_id, $parsetarget)
    {
        $tmp = new EasyTable($this->app);
        $tmp->Query("SELECT zeit,bearbeiter,grund FROM produktion_protokoll WHERE produktion='$produktion_id' ORDER by zeit DESC");
        $tmp->DisplayNew($parsetarget,'Protokoll','noAction');
    }

    function produktion_pdf() {
        $id = $this->app->Secure->GetGET('id');
        $Brief = new ProduktionPDF($this->app, styleData: Array('herstellernummerimdokument' => 1, 'ohne_steuer' => true, 'artikeleinheit' => false, 'abstand_boxrechtsoben' => -70, 'abstand_artikeltabelleoben' => -80, 'abstand_betreffzeileoben' => -70, 'preise_ausblenden' => true, 'hintergrund' => 'none'));
        $Brief->GetProduktion($id);
        $Brief->displayDocument(false);
        exit();
    }

}

