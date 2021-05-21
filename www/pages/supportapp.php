<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*
* WaWision (c) WaWision GmbH, Holzbachstrasse 4, D-86154 Augsburg, * Germany 2015
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php
include ("_gen/supportapp.php");
class Supportapp Extends GenSupportapp {
  var $app;

  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case 'supportapp_artikel':
        $heading = array('Artikel Nummer','Artikel Bezeichnung','Typ','Men&uuml;');
        $width = array('9%', '80%','10%', '1%');

        $findcols = array('a.nummer', 'a.name_de', "IF(wa.typ=1,'Telefonsupport',IF(wa.typ=2,'Technikersupport','Enterprise'))",'a.id');
        $searchsql = array('a.nummer', 'a.name_de', "IF(wa.typ=1,'Telefonsupport',IF(wa.typ=2,'Technikersupport','Enterprise'))",'a.id');

        $defaultorder = 0;
        $defaultorderdesc = 0;

        $menu = '<table cellpadding="0" cellspacing="0">';
          $menu .= '<tr>';
            $menu .= '<td nowrap>';
              $menu .= "<img style=\"cursor: pointer;\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" onclick=\"deleteartikel(%value%);\">";
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        $where = "1 ";

        $sql = "SELECT SQL_CALC_FOUND_ROWS wa.id, a.nummer, a.name_de, IF(wa.typ=1,'Telefonsupport',IF(wa.typ=2,'Technikersupport','Enterprise')), wa.id FROM artikel a JOIN supportapp_artikel wa ON a.id = wa.artikel ";

        $count = "SELECT count(wa.id) FROM supportapp_artikel wa WHERE $where";
        break;

      case 'supportapp_abgelaufen':
        $heading = array('Kunden-Nr.', 'Kunde','Hat(te) Supportvertrag','Kaufvertrag Ablauf','Men&uuml;');
        $width = array('25%', '25%','5%','10%', '1%');

        $findcols = array('adr.kundennummer','adr.name','isnull(re.adresse)','re2.datum','adr.id');
        $searchsql = array('adr.kundennummer','adr.name',"date_format(re2.datum,'%d.%m.%Y')");

        $defaultorder = 0;
        $defaultorderdesc = 0;

        $menu = '<table cellpadding="0" cellspacing="0">';
        $menu .= '<tr>';
        $menu .= '<td nowrap>';
        $menu .= "<a  href=\"index.php?module=adresse&action=edit&id=%value%\" target=\"_blank\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\" /></a>&nbsp;";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";
        $artikektelefonarr = array(0);
        for($i = 1; $i <= 10; $i++)
        {
          $tmp = $app->erp->GetKonfiguration('supportapp_enterprise'.($i > 1?$i:''));
          $tmp = explode(' ', $tmp);
          $tmp = reset($tmp);
          $tmp = $app->DB->Select("SELECT id FROM artikel WHERE nummer <> '' AND ifnull(geloescht,0) = 0 AND nummer = '".$app->DB->real_escape_string($tmp)."' LIMIT 1");
          if($tmp)$enterprisearr[] = $tmp;
        }
        
        $abgelaufene = $app->YUI->TableSearchFilter($name, 1, 'abgelaufene',0,0,'checkbox');

        $sql = "SELECT SQL_CALC_FOUND_ROWS adr.id, adr.kundennummer, adr.name, if(isnull(re.adresse),'-','ja'), if(isnull(re2.datum),'',
                if( re2.datum < DATE_SUB(CURDATE(),INTERVAL 1 YEAR),concat('<span style=\"color:red\">',date_format(re2.datum,'%d.%m.%Y'),'</span>'),date_format(re2.datum,'%d.%m.%Y') )) , adr.id 
                FROM adresse adr
                INNER JOIN adresse_rolle ar ON adr.id = ar.adresse AND ar.subjekt LIKE 'Kunde' 
                LEFT JOIN (
                    SELECT r.adresse 
                    FROM rechnung r 
                    INNER JOIN rechnung_position rp ON r.status <> 'storniert' AND r.belegnr <> '' AND r.id = rp.rechnung AND rp.artikel in (".implode(',', $enterprisearr).") AND rp.artikel <> 0
                    LEFT JOIN abrechnungsartikel abo ON r.adresse = abo.adresse AND abo.artikel = rp.artikel
                      AND abo.artikel <> 0 
                    WHERE isnull(abo.adresse) OR 
                      (".
                        //"abo.enddatum < CURDATE() 
                        //AND ifnull(abo.enddatum,'0000-00-00') <> '0000-00-00' ".
                        " ifnull(abo.enddatum,'0000-00-00') = '0000-00-00' ".
                        "AND r.datum < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                      )
                    GROUP BY r.adresse
                ) re ON 
                adr.id = re.adresse
                LEFT JOIN (
                SELECT r2.adresse, max(r2.datum) as datum
                FROM rechnung r2 INNER JOIN rechnung_position rp2 ON r2.status <> 'storniert' AND r2.belegnr <> '' AND r2.id = rp2.rechnung 
                INNER JOIN wawisionstore_artikelmapping wa ON rp2.artikel = wa.artikel AND wa.typ <> 'modul' AND wa.testversion = 0 AND wa.cloud = ''
                GROUP BY r2.adresse
                ) re2 ON adr.id = re2.adresse
                LEFT JOIN (
                           SELECT r.adresse 
                    FROM rechnung r 
                    INNER JOIN rechnung_position rp ON r.status <> 'storniert' AND r.belegnr <> '' AND r.id = rp.rechnung AND rp.artikel in (".implode(',', $enterprisearr).") AND rp.artikel <> 0
                    LEFT JOIN abrechnungsartikel abo ON r.adresse = abo.adresse AND abo.artikel = rp.artikel
                      AND abo.artikel <> 0 
                    WHERE  
                      ( ifnull(abo.enddatum,'0000-00-00') = '0000-00-00')
                       OR abo.enddatum >= CURDATE()
                    GROUP BY r.adresse
                
                ) ab ON adr.id = ab.adresse
        ";
        $where = " ifnull(adr.geloescht,0) = 0 AND (ifnull(re.adresse,0) > 0 OR  (ifnull(re2.adresse,0) > 0 ".($abgelaufene?" AND re2.datum < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)  ":"").") ) AND isnull(ab.adresse) ";

        break;

      case 'supportapp_schritte':
        $heading = array('Bezeichnung', 'Gruppe', 'Beschreibung','Vorgänger', 'Reihenfolge', 'Aktiv', 'Filter','Men&uuml;');
        $width = array('20%', '20%','30%','20%', '4%','5%', '1%');

        $findcols = array('ws.bezeichnung', 'wg.bezeichnung', 'ws.beschreibung', 'ws.sort', 'ws.aktiv', 'ws.filter', 'ws.id');
        $searchsql = array('ws.bezeichnung', 'wg.bezeichnung', 'ws.beschreibung', 'ws.sort', 'ws.id');

        $defaultorder = 0;
        $defaultorderdesc = 0;

        $menu = '<table cellpadding="0" cellspacing="0">';
          $menu .= '<tr>';
            $menu .= '<td nowrap>';
              $menu .= "<img style=\"cursor: pointer;\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\" onclick=\"neuedit(%value%);\">&nbsp;";
              $menu .= "<img style=\"cursor: pointer;\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" onclick=\"deleteeintrag(%value%);\">";
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        $where = "1";

        $sql = "SELECT SQL_CALC_FOUND_ROWS ws.id, ws.bezeichnung, wg.bezeichnung, ws.beschreibung, IFNULL(wsv.bezeichnung,''), ws.sort, IF(ws.aktiv=1,'Ja','Nein'),IF(ws.filter=1,'Ja','Nein'), ws.id FROM supportapp_schritte ws JOIN supportapp_gruppen wg ON ws.gruppe = wg.id LEFT JOIN supportapp_schritte wsv ON ws.vorgaenger = wsv.id";

        $count = "SELECT count(id) FROM supportapp_schritte WHERE $where";
        break;
      case 'supportapp_gruppen':
        $heading = array('Bezeichnung','Artikel','Aktiv','Men&uuml;');
        $width = array('70%','20%', '9%', '1%');

        $findcols  = array('bezeichnung', 'artikel', 'aktiv');
        $searchsql = array('bezeichnung', 'artikel', 'aktiv');

        $defaultorder = 0;
        $defaultorderdesc = 0;

        $menu = '<table cellpadding="0" cellspacing="0">';
          $menu .= '<tr>';
            $menu .= '<td nowrap>';
              $menu .= "<img style=\"cursor: pointer;\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\" onclick=\"gruppenneuedit(%value%);\">&nbsp;";
              $menu .= "<img style=\"cursor: pointer;\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" onclick=\"gruppendeleteeintrag(%value%);\">";
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        $where = "1";

        $sql = "SELECT SQL_CALC_FOUND_ROWS wg.id, wg.bezeichnung, CONCAT(a.nummer,\" \",a.name_de),IF(wg.aktiv=1,'Ja','Nein') AS aktiv, wg.id FROM supportapp_gruppen wg LEFT JOIN artikel a on wg.artikel = a.id ";

        $count = "SELECT count(id) FROM supportapp_gruppen WHERE $where";
        break;
      case 'supportapp_zeiterfassung':
        $kundennr = $app->DB->real_escape_string($app->Secure->GetGET('kdnr'));
        $kundenid = $app->DB->real_escape_string($app->Secure->GetGET('id'));

        if($kundennr != ''){
          $kundenname = $app->DB->Select("SELECT name FROM adresse WHERE kundennummer = '".$kundennr."'");
          $kundenid = $app->DB->Select("SELECT id FROM adresse WHERE kundennummer = '".$kundennr."'");
        }else{
          if($kundenid != ''){
            $kundenname = $app->DB->Select("SELECT name FROM adresse WHERE id = '".$kundenid."'");
            $kundennr = $app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '".$kundenid."'");
          }
        }

        $heading = array('Datum', 'Von', 'Bis', 'Dauer','Mitarbeiter','T&auml;tigkeit','Beschreibung','');
        $width = array('5%', '5%','5%','5%','20','19%','40%', '1%');

        $findcols = array("z.von",'z.von', 'z.bis', 'format((HOUR(TIMEDIFF(z.bis, z.von))) + MINUTE(TIMEDIFF(z.bis, z.von))/60,2)','name', 'aufgabe', 'beschreibung','z.id');
        $searchsql = array('DATE_FORMAT(z.von, GET_FORMAT(DATE,\'EUR\'))','a.name', 'z.aufgabe', 'z.beschreibung','z.id');

        $datecols = array(0);
        $numbercols = array(3);

        $defaultorder = 1;
        $defaultorderdesc = 1;

        $menu = '';

        $where = " adresse_abrechnung = '$kundenid' ";

        $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT z.id, DATE_FORMAT(z.von, GET_FORMAT(DATE,'EUR')) AS datum,  DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis, format((HOUR(TIMEDIFF(z.bis, z.von))) + MINUTE(TIMEDIFF(z.bis, z.von))/60,2) AS Dauer, a.name, z.aufgabe, REPLACE(z.beschreibung, '\n', '<br>'),  z.id FROM zeiterfassung z LEFT JOIN adresse a ON z.adresse = a.id ";


        $count = "SELECT count(DISTINCT id) FROM zeiterfassung WHERE $where";
        break;
      case 'supportapp_kunden':
        $heading = array('kundennummer','Name','Ansprechpartner','Sperrvermerk','Pakete','','Men&uuml;');
        $width = array('9%', '25%','25%','35%', '5%','0%','1%');

        $findcols = array('a.kundennummer', 'a.name','a.ansprechpartner','a.freifeld6','a.freifeld7','a.id','a.id');
        $searchsql = array('a.kundennummer', 'a.name','a.ansprechpartner','a.freifeld6','a.freifeld7','a.id','a.id');

        $defaultorder = 0;
        $defaultorderdesc = 0;


        //$menu = '<table cellpadding="0" cellspacing="0">';
          //$menu .= '<tr>';
            //$menu .= '<td nowrap>';
              //$menu .= "<a href=\"index.php?module=adresse&action=edit&id=%value%\" target=\"_blank\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;";
              $menu = "<center><a href=\"index.php?module=supportapp&action=auftrag&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a></center>";
            //$menu .= "</td>";
          //$menu .= "</tr>";
        //$menu .= "</table>";

        $trcol = 5;

        $artikektelefonarr = array(0);
        $artikektechnikerarr = array(0);
        $artikekenterprisearr = array(0);
        $artikeltmp = $app->DB->SelectArr("SELECT * FROM supportapp_artikel");
        for ($i=0; $i < count($artikeltmp); $i++) { 
          switch ($artikeltmp[$i]['typ']) {
            case '1':
              $artikektelefonarr[] = $artikeltmp[$i]['artikel'];
              break;
            case '2':
              $artikektechnikerarr[] = $artikeltmp[$i]['artikel'];
              break;
            case '3':
              $artikekenterprisearr[] = $artikeltmp[$i]['artikel'];
              break;
          }
        }

//$supportvertrag2 = $->app->DB->Select("SELECT rp.id, r.datum FROM rechnung_position rp LEFT JOIN rechnung r ON rp.rechnung = r.id WHERE artikel in (".implode(', ', $artikekenterprisearr).") AND artikel <> 0 AND adresse = '$kundenid' AND r.datum >= (now() - INTERVAL 1 YEAR)");


        $where = "ar.subjekt='Kunde' AND a.geloescht = '0' AND (a.projekt = 0 OR (1 ".$app->erp->ProjektRechte('a.projekt')."))";

        $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT a.id, a.kundennummer, a.name, a.ansprechpartner, a.freifeld6,
        CONCAT('<table cellpadding=\"0\" cellspacing=\"0\"><tr><td nowrap>',
        '<img title=\"',IF(IFNULL(telefon.adresse,0) > 0 OR ifnull(plus.adresse,0) > 0,'Telefonsupport vorhanden', 'Kein Telefonsupport'),'\" src=\"themes/new/images/', IF(IFNULL(telefon.adresse,0) > 0 OR ifnull(plus.adresse,0) > 0, 'telefonsupport_gruen_kl.png', 'telefonsupport_grey_kl.png'),'\">&nbsp;',
        '<img title=\"',IF(IFNULL(techniker.adresse,0) > 0,'Technikersupport vorhanden', 'Kein Technikersupport'),'\" src=\"themes/new/images/', IF(IFNULL(techniker.adresse,0) > 0, 'technikersupport_gruen_kl.png', 'technikersupport_grey_kl.png'),'\">&nbsp;',
        '<img title=\"',IF(a.freifeld9 <> '0' AND a.freifeld9 <> '','Bezahlsupport vorhanden', 'Kein Bezahlsupport'),'\" src=\"themes/new/images/',IF(a.freifeld9 <> '0' AND a.freifeld9 <> '', 'bezahlsupport_gruen_kl.png', 'bezahlsupport_grey_kl.png'),'\">&nbsp;',
        '<img title=\"',IF(IFNULL(plus.adresse,0) > 0,'Pluspaket vorhanden', 'Kein Pluspaket'),'\" src=\"themes/new/images/',IF(IFNULL(plus.adresse,0) > 0, 'pluspaket_gruen_kl.png', 'pluspaket_grey_kl.png'),'\">&nbsp;',
        '<img title=\"',IF(IFNULL(enterprise.adresse,0) > 0 OR NOT erstesjahr.id IS NULL,'Supportvertrag vorhanden', 'Kein Supportvertrag'),'\" src=\"themes/new/images/',IF(IFNULL(enterprise.adresse,0) > 0 OR NOT erstesjahr.id IS NULL, 'vertrag_gruen_kl.png', 'vertrag_grey_kl.png'),'\">&nbsp;',
        '</td></tr></table>'),
        IF(a.freifeld6 = '' ,'',IF(ISNULL(a.freifeld6),'','#ffadad')) AS tr, a.id
        FROM adresse a
        JOIN adresse_rolle ar ON a.id = ar.adresse
        LEFT JOIN (SELECT rp.id, r.adresse FROM rechnung_position rp LEFT JOIN rechnung r ON rp.rechnung = r.id WHERE artikel in (".implode(', ', $artikekenterprisearr).") AND artikel <> 0 AND r.datum >= (now() - INTERVAL 1 YEAR)) AS erstesjahr ON a.id = erstesjahr.adresse

        LEFT JOIN (
        SELECT adresse
        FROM abrechnungsartikel WHERE artikel in (".implode(', ', $artikektelefonarr).") AND artikel <> 0 AND (enddatum IS NULL OR enddatum >= CURDATE())
        GROUP BY adresse
        ) telefon ON a.id = telefon.adresse
        LEFT JOIN (
        SELECT adresse
        FROM abrechnungsartikel WHERE artikel in (".implode(', ', $artikektechnikerarr).") AND artikel <> 0  AND (enddatum IS NULL OR enddatum >= CURDATE())
        GROUP BY adresse
        ) techniker ON a.id = techniker.adresse
        LEFT JOIN (
        SELECT adresse
        FROM abrechnungsartikel WHERE artikel in (".implode(', ', $artikekenterprisearr).") AND artikel <> 0  AND (enddatum IS NULL OR enddatum >= CURDATE())
        GROUP BY adresse
        ) enterprise ON a.id = enterprise.adresse
        LEFT JOIN (
        SELECT adresse FROM supportapp WHERE status='gestartet' GROUP BY adresse
        ) plus on a.id = plus.adresse
        ";

        //$count = "SELECT count(DISTINCT  a.id) FROM adresse a JOIN adresse_rolle ar ON a.id = ar.adresse WHERE $where";

        break;
      case "supportapp_list":
        // angeben welches recht der Benutzer braucht damit er auf diese tabelle zugreifen darf
        $allowed['supportapp'] = array('list');

        $app->Tpl->Add('JQUERYREADY', "$('#eigene').click( function() { fnFilterColumn1( 0 ); } );");
        $app->Tpl->Add('JQUERYREADY', "$('#geplant').click( function() { fnFilterColumn2( 0 ); } );");
        $app->Tpl->Add('JQUERYREADY', "$('#abgeschlossen').click( function() { fnFilterColumn3( 0 ); } );");

        for ($r = 1;$r < 4;$r++) {
          $app->Tpl->Add('JAVASCRIPT', '
             function fnFilterColumn'.$r.'( i ){
             if(oMoreData'.$r.$name.'==1)oMoreData'.$r.$name.'=0;
             else
             oMoreData'.$r.$name.'=1;
             $(\'#' . $name . '\').dataTable().fnFilter(\'A\',i,0,0);
             }
             ');
        }


        $heading = array('','Mitarbeiter','Kunde','Version','Startdatum','Letzte Buchung','Offen','Geplant','Eigene','Gesamt','Men&uuml;','Zustand','');
        $width = array('1%','10%','20%','25%','8%','8%','8%','8%','8%','8%','5%','1%');

        $findcols = array('open','ma.name', "ku.name",'w.version','w.startdatum','w.startdatum','w.zeitgeplant','w.zeitgeplant','w.id','w.status','w.id');
        $searchsql = array('ma.name', "ku.name",'w.version','w.startdatum','w.zeitgeplant','w.status');

        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc = 1;

        $menu = "<a href=\"index.php?module=supportapp&action=edit&id=%value%\"></a>";

        $trcol = 11;

        $menucol = 12;
        $moreinfo = true;
        //$sumcol = 9;
        $alignright = array(5,6,7,8,9,10,11);

        $more_data1 = $app->Secure->GetGET("more_data1");
        $more_data2 = $app->Secure->GetGET("more_data2");
        $more_data3 = $app->Secure->GetGET("more_data3");

        $moredataarray = array();
        $schrittearray = array();
        $filterschritte = $app->DB->SelectArr("SELECT ws.id AS id, wg.bezeichnung AS wgb, ws.bezeichnung AS wsb FROM supportapp_schritte ws LEFT JOIN supportapp_gruppen wg ON ws.gruppe = wg.id WHERE ws.aktiv = '1' and filter = '1' ORDER BY wg.id, ws.sort");
        if(count($filterschritte) > 0){
          for ($i=4; $i < count($filterschritte)+4; $i++) {
            $moredataarray[$i] = $app->Secure->GetGET("more_data".$i);
            $schrittearray[$i] = $filterschritte[$i-4]['id'];
          }
        }

        if ($more_data2 == 1)
          $where = " w.status='geplant' ";
        else if ($more_data3 == 1)
          $where = " w.status='abgeschlos' ";
        else
          $where = " w.status='gestartet' ";

        if ($more_data1 == 1)
          $where .= " AND w.mitarbeiter='".$app->User->GetAdresse()."' ";


        $filterjoin = "";
        $countfilter = "";
        $aktivefilterschritte = array();
        foreach ($moredataarray as $key => $value) {
          if($value == 1){
            $aktivefilterschritte[] = "wac.schritt = ".$schrittearray[$key];
          }
        }
        if(count($aktivefilterschritte) > 0 ){
          $filterjoin = "LEFT JOIN (SELECT wac.adresse, SUM(wac.status) AS sumx, COUNT(IF(ws.vorgaenger,IF(wac2.status = 1,1,NULL),wac.status)) AS countx FROM supportapp_auftrag_check wac LEFT JOIN supportapp_schritte ws ON wac.schritt = ws.id LEFT JOIN supportapp_auftrag_check wac2 ON wac2.auftragposition = wac.auftragposition AND wac2.schritt = ws.vorgaenger WHERE (".implode(" OR ", $aktivefilterschritte).") GROUP BY adresse) AS wac ON wac.adresse = ku.id";
          $countfilter = "LEFT JOIN (SELECT wac.adresse, SUM(wac.status) AS sumx, COUNT(IF(ws.vorgaenger,IF(wac2.status = 1,1,NULL),wac.status)) AS countx FROM supportapp_auftrag_check wac LEFT JOIN supportapp_schritte ws ON wac.schritt = ws.id LEFT JOIN supportapp_auftrag_check wac2 ON wac2.auftragposition = wac.auftragposition AND wac2.schritt = ws.vorgaenger WHERE (".implode(" OR ", $aktivefilterschritte).") GROUP BY adresse) AS wac ON wac.adresse = w.adresse";
          $where .= "AND wac.sumx < wac.countx AND wac.countx>=".count($aktivefilterschritte);
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS w.id, '<img src=./themes/{$app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open,
           ma.name, ku.name, w.version,DATE_FORMAT(w.startdatum,'%d.%m.%Y'),
           (SELECT DATE_FORMAT(MAX(z2.bis),'%d.%m.%Y') FROM zeiterfassung z2 WHERE z2.adresse_abrechnung=w.adresse AND DATE_FORMAT(z2.von,'%Y-%m-%d') >=w.startdatum ),
              (SELECT SUM(auf.umsatz_netto) FROM auftrag auf WHERE auf.adresse=w.adresse AND (auf.status='freigegeben' OR auf.status='versendet')),
            FORMAT(w.zeitgeplant,2),
            (SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z3.bis, z3.von)))/3600,2) FROM zeiterfassung z3 WHERE z3.adresse=w.mitarbeiter AND z3.adresse_abrechnung=w.adresse AND DATE_FORMAT(z3.von,'%Y-%m-%d') >= w.startdatum),
            (SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z3.bis, z3.von)))/3600,2) FROM zeiterfassung z3 WHERE z3.adresse_abrechnung=w.adresse AND DATE_FORMAT(z3.von,'%Y-%m-%d') >= w.startdatum),
            CONCAT('<table cellpadding=\"0\" cellspacing=\"0\"><tr><td nowrap><img style=\"cursor: pointer;\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\" onclick=\"neuedit(',w.id,')\">&nbsp;<a href=\"index.php?module=supportapp&action=auftrag&id=',ku.id,'\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a></td></tr></table>') as test,
            IF(DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'),(SELECT DATE_FORMAT(MAX(z2.bis),'%Y-%m-%d') FROM zeiterfassung z2 WHERE z2.adresse_abrechnung=w.adresse AND z2.adresse=w.mitarbeiter AND DATE_FORMAT(z2.von,'%Y-%m-%d') >=w.startdatum )) > 5*w.intervall,'#ffadad', IF(DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d'),(SELECT DATE_FORMAT(MAX(z2.bis),'%Y-%m-%d') FROM zeiterfassung z2 WHERE z2.adresse_abrechnung=w.adresse AND DATE_FORMAT(z2.von,'%Y-%m-%d') >=w.startdatum )) > 2*w.intervall,'#E5F5D2',   '')) as tr,
            w.id
          FROM supportapp w LEFT JOIN adresse ma ON ma.id=w.mitarbeiter LEFT JOIN adresse ku ON ku.id=w.adresse $filterjoin";

        $count = "SELECT count(w.id) FROM supportapp w $countfilter WHERE $where";
      break;
      case "supportapp_vorlagenuebersicht":
        $heading = array('Bezeichnung','Taetigkeit', 'Beschreibung','Men&uuml;');
        $width = array('10%', '20%','79%', '1%');

        $findcols = array('bezeichnung', 'taetigkeit', 'beschreibung', 'id');
        $searchsql = array('bezeichnung', 'taetigkeit', 'beschreibung', 'id');

        $defaultorder = 0;
        $defaultorderdesc = 0;

        $menu = '<table cellpadding="0" cellspacing="0">';
          $menu .= '<tr>';
            $menu .= '<td nowrap>';
              $menu .= "<img style=\"cursor: pointer;\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\" onclick=\"neuedit(%value%);\">&nbsp;";
              $menu .= "<img style=\"cursor: pointer;\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\" onclick=\"deleteeintrag(%value%);\">";
            $menu .= "</td>";
          $menu .= "</tr>";
        $menu .= "</table>";

        $where = "1";

        $sql = "SELECT SQL_CALC_FOUND_ROWS id, taetigkeit, bezeichnung, beschreibung, id FROM supportapp_vorlagen";
        $count = "SELECT count(id) FROM supportapp_vorlagen WHERE $where";

        break;
    }

    $erg = false;

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))$erg[$v] = $$v;
    }
    return $erg;
  }

  function __construct(&$app, $intern = false) {
    $this->app=&$app;
    if($intern)return;
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("list", "supportappList");
    $this->app->ActionHandler("kunden", "supportappKunden");
    $this->app->ActionHandler("schritte", "supportappSchritte");
    //$this->app->ActionHandler("create", "supportappCreate");
    $this->app->ActionHandler("auftrag", "supportappAuftrag");
    $this->app->ActionHandler("edit", "supportappEdit");
    $this->app->ActionHandler("minidetail", "supportappMiniDetail");
    $this->app->ActionHandler("abgelaufeneabos", "supportappAbgelaufeneAbos");
    $this->app->ActionHandler("einstellungen", "supportappEinstellungen");
    $this->app->erp->Headlines('Suppport');
    $this->app->DefaultActionHandler("kunden");
    $this->app->ActionHandlerListen($app);
  }

  function supportappEinstellungen()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    switch ($cmd){
      case 'artikelhinzufuegen':
        $typ = $this->app->Secure->GetGET('typ');
        $artikeltmp = explode(" ", $this->app->Secure->GetGET('artikel'));
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer ='".$artikeltmp[0]."' LIMIT 1");
        $antwort = 0;
        if($artikelid > 0){
          if(!$this->app->DB->Select("SELECT id FROM supportapp_artikel WHERE artikel='$artikelid' AND typ='$typ'")){
            $this->app->DB->Insert("INSERT INTO supportapp_artikel (artikel, typ) VALUES ('$artikelid','$typ')");
            $antwort = 1;
          }
        }

        echo json_encode($antwort);
        exit;
        break;
      case 'deleteartikel':
        $id = $this->app->Secure->GetGET('id');
        $this->app->DB->Delete("DELETE FROM supportapp_artikel WHERE id='$id'");
        echo json_encode(1);
        exit;
        break;
      case 'vorlagespeichern':
        $id = $this->app->Secure->GetPOST('vid');
        $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
        $taetigkeit = $this->app->Secure->GetPOST('taetigkeit');
        $beschreibung = $this->app->Secure->GetPOST('beschreibung');
        if($this->app->DB->Select("SELECT id FROM supportapp_vorlagen WHERE bezeichnung = '$bezeichnung' LIMIT 1")){
          $antwort = 'Eine Vorlage mit dieser Bezeichnung existiert bereits.';
        }

        if($antwort == ''){
          $antwort = "success";
          if($id == '0'){
            $this->app->DB->Insert("INSERT INTO supportapp_vorlagen (bezeichnung, taetigkeit, beschreibung) VALUES ('$bezeichnung','$taetigkeit','$beschreibung')");
          }else{
            $this->app->DB->Update("UPDATE supportapp_vorlagen SET taetigkeit = '$taetigkeit', bezeichnung = '$bezeichnung', beschreibung = '$beschreibung' WHERE id = $id");
          }
        }
        echo json_encode($antwort);
        exit;
        break;
      case 'delete':
        $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $this->app->DB->Delete("DELETE FROM supportapp_vorlagen WHERE id = '$id'");
        echo json_encode('success');
        exit;
        break;
      case 'editvorlage':
        $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $result = $this->app->DB->SelectArr("SELECT * FROM supportapp_vorlagen WHERE id = '$id'");
        $result = reset($result);
        echo json_encode($result);
        exit;
        break;
      default:
        break;
    }


    if($this->app->DB->real_escape_string($this->app->Secure->GetPOST('schritteerrechnen'))){
      $kunden = $this->app->DB->SelectArr("SELECT adresse FROM supportapp WHERE status='gestartet'");
      $schrittzahl = 0;
      $kundenzahl = count($kunden);
      for ($h=0; $h < count($kunden); $h++) {
        $kundenid = $kunden[$h]['adresse'];
        $gruppenzumhinzufuegen= $this->app->DB->SelectArr("SELECT ap.id, was.gruppe FROM auftrag_position ap JOIN auftrag a ON ap.auftrag = a.id LEFT JOIN supportapp_gruppen wag ON ap.artikel = wag.artikel LEFT JOIN supportapp_schritte was ON wag.id = was.gruppe LEFT JOIN supportapp_auftrag_check wac ON wac.schritt = was.id WHERE a.adresse = '$kundenid' AND a.status <> 'storniert' AND a.belegnr <> '' AND wag.aktiv = 1 AND was.aktiv = 1 GROUP BY ap.id, was.id");
        for ($i=0; $i < count($gruppenzumhinzufuegen); $i++) {
          $einzelschritte = $this->app->DB->SelectArr("SELECT * FROM supportapp_schritte WHERE aktiv = 1 AND gruppe = ".$gruppenzumhinzufuegen[$i]['gruppe']);
          for ($j=0; $j < count($einzelschritte); $j++) {
            $vorhanden = $this->app->DB->Select("SELECT id FROM supportapp_auftrag_check WHERE auftragposition = '".$gruppenzumhinzufuegen[$i]['id']."' AND gruppe = '".$gruppenzumhinzufuegen[$i]['gruppe']."' AND adresse = '$kundenid' AND schritt = '".$einzelschritte[$j]['id']."' LIMIT 1");
            if($vorhanden == ''){
              $schrittzahl += 1;
              $this->app->DB->Insert("INSERT INTO supportapp_auftrag_check (adresse, gruppe, schritt, auftragposition, status) VALUES ('$kundenid','".$gruppenzumhinzufuegen[$i]['gruppe']."','".$einzelschritte[$j]['id']."','".$gruppenzumhinzufuegen[$i]['id']."','0')");
            }
          }
        }
      }
      $this->app->Tpl->Set("MESSAGE", "<div class=\"info\">Es wurden $schrittzahl Schritte bei $kundenzahl Kunden hinzugefügt</div>");
    }

    $this->supportappMenuNormal();
    $this->app->erp->Headlines('','Einstellungen');

    $this->app->YUI->AutoComplete("artikelfuerauswahl", "artikelnummer");
    $this->app->YUI->CkEditor("beschreibung","all",array('height'=>'11em', 'width'=>'100%'));
    $this->app->YUI->TableSearch('TAB2','supportapp_vorlagenuebersicht', "show","","",basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('ARTIKELTABELLE','supportapp_artikel', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE', 'supportapp_einstellungen.tpl');
  }

  function Install()
  {
    $artikeluebernehmen = false; //Nur für das erste mal notwending, kann später gelöscht werden 
    $this->app->DB->Select("SELECT id FROM supportapp_artikel LIMIT 1");
    if($this->app->DB->error())$artikeluebernehmen = true; 

    $this->app->erp->CheckTable("supportapp");
    $this->app->erp->CheckColumn("id", "int(11)", "supportapp", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("adresse", "int(11)", "supportapp", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("mitarbeiter", "int(11)", "supportapp", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("startdatum", "DATE", "supportapp", "NOT NULL");
    $this->app->erp->CheckColumn("zeitgeplant", "int(11)", "supportapp", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("version", "TEXT", "supportapp", "NOT NULL");
    $this->app->erp->CheckColumn("bemerkung", "TEXT", "supportapp", "NOT NULL");
    $this->app->erp->CheckColumn("status", "varchar(10)", "supportapp", "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn("phase", "varchar(10)", "supportapp", "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn("intervall", "int(11)", "supportapp", "NOT NULL DEFAULT '1'");

    $this->app->erp->CheckTable("supportapp_schritte");
    $this->app->erp->CheckColumn("id", "int(11)", "supportapp_schritte", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("bezeichnung", "varchar(255)", "supportapp_schritte", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("gruppe", "int(11)", "supportapp_schritte", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("beschreibung", "TEXT", "supportapp_schritte", "NOT NULL");
    $this->app->erp->CheckColumn("aktiv", "int(1)", "supportapp_schritte", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("sort", "int(11)", "supportapp_schritte", "DEFAULT 0");
    $this->app->erp->CheckColumn("vorgaenger", "int(11)", "supportapp_schritte", "DEFAULT 0");
    $this->app->erp->CheckColumn("filter", "int(1)", "supportapp_schritte", "DEFAULT 0");

    $this->app->erp->CheckTable("supportapp_gruppen");
    $this->app->erp->CheckColumn("id", "int(11)", "supportapp_gruppen", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("artikel", "int(11)", "supportapp_gruppen", "NOT NULL");
    $this->app->erp->CheckColumn("bezeichnung", "varchar(255)", "supportapp_gruppen", "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn("aktiv", "int(1)", "supportapp_gruppen", "NOT NULL DEFAULT '1'");

    $this->app->erp->CheckTable("supportapp_auftrag_check");
    $this->app->erp->CheckColumn("id", "int(11)", "supportapp_auftrag_check", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("adresse", "int(11)", "supportapp_auftrag_check", "NOT NULL");
    $this->app->erp->CheckColumn("gruppe", "int(11)", "supportapp_auftrag_check", "NOT NULL");
    $this->app->erp->CheckColumn("schritt", "int(11)", "supportapp_auftrag_check", "NOT NULL");
    $this->app->erp->CheckColumn("auftragposition", "int(11)", "supportapp_auftrag_check", "NOT NULL");
    $this->app->erp->CheckColumn("status", "int(1)", "supportapp_auftrag_check", "NOT NULL DEFAULT 0");

    $this->app->erp->CheckTable("supportapp_vorlagen");
    $this->app->erp->CheckColumn("id", "int(11)", "supportapp_vorlagen", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("bezeichnung", "varchar(255)", "supportapp_vorlagen", "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn("taetigkeit", "varchar(255)", "supportapp_vorlagen", "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn("beschreibung", "TEXT", "supportapp_vorlagen", "NOT NULL DEFAULT ''");

    $this->app->erp->CheckTable("supportapp_log");
    $this->app->erp->CheckColumn("id", "int(11)", "supportapp_log", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("adresse", "int(11)", "supportapp_log", "NOT NULL DEFAULT '0'"); //Kunde
    $this->app->erp->CheckColumn("bearbeiter", "int(11)", "supportapp_log", "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn("logdatei", "DATETIME", "supportapp_log", "NOT NULL");
    $this->app->erp->CheckColumn("details", "varchar(255)", "supportapp_log", "NOT NULL DEFAULT ''");

    $this->app->erp->CheckTable("supportapp_artikel");
    $this->app->erp->CheckColumn("id", "int(11)", "supportapp_artikel", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("artikel", "int(11)", "supportapp_artikel", "NOT NULL DEFAULT '0'"); //artikel
    $this->app->erp->CheckColumn("typ", "int(11)", "supportapp_artikel", "NOT NULL DEFAULT '0'"); //) 1: Telefonsupport, 2: Technikersupport, 3: Enterprise, etc 
    $this->app->erp->RegisterMenuHook('startseite','supportappMenuHook', $this);

    if($artikeluebernehmen){ //Nur für das erste mal notwending, kann später gelöscht werden 
      //Damit bei der Änderung der Artikelzuweisung nicht alle Artikel nochmal angelegt werden müssen
      for($i = 1; $i <= 10; $i++)
      {
        $tmp = $this->app->erp->GetKonfiguration('supportapp_telefonsupport'.($i > 1?$i:''));
        $tmp = explode(' ', $tmp);
        $tmp = reset($tmp);
        $tmp = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer <> '' AND ifnull(geloescht,0) = 0 AND nummer = '".$this->app->DB->real_escape_string($tmp)."'  LIMIT 1");
        if($tmp)$this->app->DB->Insert("INSERT INTO supportapp_artikel (artikel, typ) VALUES ('$tmp','1')");
        $tmp = $this->app->erp->GetKonfiguration('supportapp_technikersupport'.($i > 1?$i:''));
        $tmp = explode(' ', $tmp);
        $tmp = reset($tmp);
        $tmp = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer <> '' AND ifnull(geloescht,0) = 0 AND nummer = '".$this->app->DB->real_escape_string($tmp)."'  LIMIT 1");
        if($tmp)$this->app->DB->Insert("INSERT INTO supportapp_artikel (artikel, typ) VALUES ('$tmp','2')");
        $tmp = $this->app->erp->GetKonfiguration('supportapp_enterprise'.($i > 1?$i:''));
        $tmp = explode(' ', $tmp);
        $tmp = reset($tmp);
        $tmp = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer <> '' AND ifnull(geloescht,0) = 0 AND nummer = '".$this->app->DB->real_escape_string($tmp)."' LIMIT 1");
        if($tmp)$this->app->DB->Insert("INSERT INTO supportapp_artikel (artikel, typ) VALUES ('$tmp','3')");
      }
    }

     }

  function supportappMenuHook()
  {
    //$this->app->erp->InsertMenuAfter("index.php?module=matrixprodukt&action=artikel&id=$id","Matrixprodukt","artikel","eigenschaften");
    $this->app->erp->MenuEintrag("index.php?module=supportapp&action=kunden","Support");
  }

  function supportappMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=supportapp&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }
  function supportappMenuNormal($id = '')
  {
    //$this->app->erp->MenuEintrag("index.php?module=supportapp&action=create","Neue supportapp");
    $this->app->erp->MenuEintrag("index.php?module=supportapp&action=kunden","Kunden");
    $this->app->erp->MenuEintrag("index.php?module=supportapp&action=list","Einrichtung");
    $this->app->erp->MenuEintrag("index.php?module=supportapp&action=auftrag".($id != ''?'&id='.$id:''),"Auftrag");
    $this->app->erp->MenuEintrag("index.php?module=supportapp&action=schritte","Schritte");
    $this->app->erp->MenuEintrag("index.php?module=supportapp&action=abgelaufeneabos","Abgelaufene Supportvertr&auml;ge");
    $this->app->erp->MenuEintrag("index.php?module=supportapp&action=einstellungen","Einstellungen");
 
    if($this->app->User->GetParameter("supportapp_startstop")==''){
      $this->app->User->SetParameter("supportapp_startstop", serialize(array()));
    }

  }

  function supportappAbgelaufeneAbos()
  {
    $this->supportappMenuNormal();
    $this->app->erp->Headlines('','Abgelaufene Supportvertr&auml;ge');
    $this->app->YUI->TableSearch('TAB1','supportapp_abgelaufen', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE', 'supportapp_abgelaufeneabos.tpl');
  }

  function supportappList()
  {
    $cmd = $this->app->Secure->GetGET('cmd');

    switch ($cmd) {
      case 'einrichtungspeichern':
        $einrichtungid = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('einrichtungid'));
        $kundetmp = explode(" ", $this->app->DB->real_escape_string($this->app->Secure->GetPOST('kunde')));
        $mitarbeitertmp = explode(" ",$this->app->DB->real_escape_string($this->app->Secure->GetPOST('mitarbeiter')));
        $version = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('version'));
        $status = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('status'));
        $intervall = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('intervall'));
        $startdatum = date_create_from_format('d.m.Y',$this->app->DB->real_escape_string($this->app->Secure->GetPOST('startdatum')));

        $zeitgeplant = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('zeitgeplant'));
        $phase = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('phase'));
        $bemerkung = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('bemerkung'));

        $kundennummer = $kundetmp[0];
        $mitarbeiternummer = $mitarbeitertmp[0];

        $kundenid = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer ='$kundennummer' LIMIT 1");
        $mitarbeiterid = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer = '$mitarbeiternummer' LIMIT 1");

        if($einrichtungid > 0){
          $this->app->DB->Update("UPDATE supportapp SET adresse='$kundenid',mitarbeiter='$mitarbeiterid',startdatum='".date_format($startdatum, 'Y-m-d')."',zeitgeplant='$zeitgeplant', intervall='$intervall', version='$version',bemerkung='$bemerkung',status='$status',phase='$phase' WHERE id='$einrichtungid'");
        }else{
          $this->app->DB->Insert("INSERT INTO supportapp (adresse, mitarbeiter, startdatum, zeitgeplant, intervall, version, bemerkung, status, phase) VALUES ('$kundenid','$mitarbeiterid','".date_format($startdatum, 'Y-m-d')."','$zeitgeplant','$intervall','$version','$bemerkung','$status','$phase')");
        }
        echo json_encode("success");
        exit;
        break;
      case 'geteinrichtung':
        $einrichtungid = $this->app->Secure->GetGET('id');

        $einrichtung = reset($this->app->DB->SelectArr("SELECT s.id AS id, CONCAT(a1.kundennummer,' ',a1.name) AS kunde, CONCAT(a2.mitarbeiternummer,' ',a2.name) AS mitarbeiter, DATE_FORMAT(s.startdatum,'%d.%m.%Y') AS startdatum, s.zeitgeplant, s.version, s.intervall, s.status, s.phase, s.bemerkung FROM supportapp s LEFT JOIN adresse a1 ON s.adresse = a1.id LEFT JOIN adresse a2 ON s.mitarbeiter = a2.id WHERE s.id = '$einrichtungid'"));
        echo json_encode($einrichtung);
        exit;
        break;
      default:
        # code...
        break;
    }


    $this->supportappMenuNormal();
    $this->app->erp->Headlines('','Einrichtung');
    $filterschritte = $this->app->DB->SelectArr("SELECT ws.id AS id, wg.bezeichnung AS wgb, ws.bezeichnung AS wsb FROM supportapp_schritte ws LEFT JOIN supportapp_gruppen wg ON ws.gruppe = wg.id WHERE ws.aktiv = '1' and filter = '1' ORDER BY wg.id, ws.sort");

    if(count($filterschritte) > 0){
      $filterinhalt = "<fieldset style=\"width:33em;\"><legend>Einzelfilter</legend><table>";
      $filtergruppe_tmp = "";
      for ($i=0; $i < count($filterschritte); $i++) {
        if($filtergruppe_tmp != $filterschritte[$i]['wgb']){
          $filterinhalt .= '<tr><td></td></tr><tr><td colspan="2"><b>'.$filterschritte[$i]['wgb'].'</b></td></tr>';
          $filtergruppe_tmp = $filterschritte[$i]['wgb'];
        }
        $filterinhalt .= '<tr><td><input type="checkbox" id="filter_'.$filterschritte[$i]['id'].'"></td><td rowspan="2"><label for="filter_'.$filterschritte[$i]['id'].'">'.$filterschritte[$i]['wsb'].' fehlt</label></td></tr><tr></tr>';

        $this->app->Tpl->Add('JQUERYREADY', "$('#filter_".$filterschritte[$i]['id']."').click(function() { fnFilterColumn".($i+4)."( 0 ); } );");
        $this->app->Tpl->Add('JAVASCRIPT', '
         function fnFilterColumn'.($i+4).'( i ){
         if(oMoreData'.($i+4).'supportapp_list==1)oMoreData'.($i+4).'supportapp_list=0;
         else
         oMoreData'.($i+4).'supportapp_list=1;
         $(\'#supportapp_list\').dataTable().fnFilter(\'A\',i,0,0);
         }
         ');
      }
      $filterinhalt .= "</table></fieldset>";
    }

    $this->app->YUI->DatePicker("startdatum");
    $this->app->YUI->CkEditor("bemerkung","basic",array('height'=>'200px', 'width'=>'500px'));
    $this->app->YUI->AutoComplete("kunde","kunde");
    $this->app->YUI->AutoComplete("mitarbeiter","mitarbeiter");


    $this->app->Tpl->Set("FILTERBOX", $filterinhalt);
    $this->app->YUI->TableSearch('TAB1','supportapp_list', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","supportapp_list.tpl");
  }

  function supportappAuftrag()
  {
    $cmd = $this->app->Secure->GetGET('cmd');

    switch ($cmd) {
      case 'changeschritt':
        $adressid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $gs = explode("_",$this->app->DB->real_escape_string($this->app->Secure->GetPOST('gs')));
        $checked = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('checked'));

        $schrittname = $this->app->DB->Select("SELECT bezeichnung FROM supportapp_schritte WHERE id = '".$gs[2]."'");
        $this->supportapplogbucheintrag($adressid, $this->app->User->GetAdresse(), $schrittname." geändert zu ".($checked=='1'?'erledigt':'nicht erledigt'));

        $vorhanden = $this->app->DB->Select("SELECT id FROM supportapp_auftrag_check WHERE adresse='".$adressid."' AND schritt='".$gs[2]."' AND auftragposition = '".$gs[3]."'");
        if($vorhanden){
          $this->app->DB->Update("UPDATE supportapp_auftrag_check set status = '$checked' WHERE adresse='".$adressid."' AND schritt='".$gs[2]."' AND auftragposition = '".$gs[3]."'");
        }else{
          $this->app->DB->Insert("INSERT INTO supportapp_auftrag_check (adresse, gruppe, schritt, auftragposition, status) VALUES ('$adressid','".$gs[1]."','".$gs[2]."','".$gs[3]."', '$checked')");
        }

        echo json_encode("success");
        exit;
        break;
      case 'goto':
        $kundennr = $this->app->DB->real_escape_string($this->app->Secure->GetGET('kdnr'));
        $kundendaten = explode(" ", $kundennr);
        $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer = '".$kundendaten[0]."'");
        echo json_encode($id);
        exit;
        break;
      case 'start':
        $kundenid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $startstop = unserialize($this->app->User->GetParameter("supportapp_startstop"));


        if(($startstop[$kundenid]['status'] != 1)){
          $startstop[$kundenid]['status'] = 1;
          $startstop[$kundenid]['zeit'] = date("d.m.Y H:i");
          $this->app->User->SetParameter("supportapp_startstop", serialize($startstop));
        }

        echo json_encode("success");
        exit;
        break;
      case 'stop':
        $kundenid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $startstop = unserialize($this->app->User->GetParameter("supportapp_startstop"));
        $zeit = $startstop[$kundenid]['zeit'];
        $projektid = $this->app->DB->Select("SELECT CONCAT(p.abkuerzung,' ',p.name) FROM projekt p LEFT JOIN adresse a ON p.id = a.projekt WHERE a.id = '$kundenid'");

        $antwort = array('zeit' => $zeit,
                         'adresse' => $kundenid,
                         'projekt' => $projekt);
        echo json_encode($antwort);
        exit;
        break;
      case 'save': 
        $kundenid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $bearbeiternummer = $this->app->DB->real_escape_string($this->app->Secure->GetGET('bearbeiter'));
        $bearbeiteradresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer = '".$bearbeiternummer."'");

        if($bearbeiteradresse == '' || $bearbeiteradresse == 0 || !is_numeric($bearbeiteradresse)){
          $bearbeiteradresse = $this->app->User->GetAdresse();
        }

        $von = $this->app->Secure->GetPOST('von');
        $bis = $this->app->Secure->GetPOST('bis');
        $taetigkeit = $this->app->Secure->GetPOST('taetigkeit');
        $details = $this->app->Secure->GetPOST('details');
        $projekt = $this->app->Secure->GetPOST('projekt');
        $projekt = explode(" ", $projekt);
        $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$projekt[0]."'");
        if($projektid == '') $projektid = 0;

        $von = DateTime::createFromFormat('d.m.Y H:i', $von);
        $bis = DateTime::createFromFormat('d.m.Y H:i', $bis);
        $von = $von->format("Y-m-d H:i:s");
        $bis = $bis->format("Y-m-d H:i:s");

        $erfolg = $this->app->DB->Insert("INSERT INTO zeiterfassung (art, adresse, von, bis, aufgabe, beschreibung, arbeitspaket, buchungsart, kostenstelle, projekt, abgerechnet, logdatei, status, gps, arbeitsnachweispositionid, adresse_abrechnung, abrechnen, ist_abgerechnet, gebucht_von_user, ort, abrechnung_dokument, dokumentid, verrechnungsart, arbeitsnachweis, internerkommentar, aufgabe_id, auftrag, auftragpositionid, produktion,stundensatz, arbeitsanweisung, serviceauftrag) 
          VALUES ('Arbeit', '$bearbeiteradresse', '$von', '$bis', '$taetigkeit', '$details', '0', 'manuell', '', '$projektid', '0', NOW(), 'offen', '', '0', '$kundenid', '0', '0', '$bearbeiteradresse', '', '', '0', '', '0', '', '0', '0', '0', '0', '0', '0', '1')");

        $startstop = unserialize($this->app->User->GetParameter("supportapp_startstop"));
        $startstop[$kundenid]['status'] = '0';
        $startstop[$kundenid]['zeit'] = '';
        $this->app->User->SetParameter("supportapp_startstop", serialize($startstop));

        if($erfolg == '1'){
          echo json_encode("success");
        }else{
          echo json_encode("Zeiterfassung wurde nicht gebucht: Unbekannter Fehler.");
        }

        exit;
        break;
      case 'discard':
        $kundenid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $startstop = unserialize($this->app->User->GetParameter("supportapp_startstop"));
        $startstop[$kundenid]['status'] = '0';
        $startstop[$kundenid]['zeit'] = '';

        $this->app->User->SetParameter("supportapp_startstop", serialize($startstop));
        echo json_encode("success");
        exit;
        break;
      case 'notiz':
        $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $sonstiges = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('notiz'));
        $this->app->DB->Update("UPDATE adresse SET sonstiges = '$sonstiges' WHERE id = '$id'");
        echo json_encode("success");
        exit;
        break;
      case 'getmail':
        $empfanengerid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $betreff = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '".$empfanengerid."'")." - ".$this->app->DB->Select("SELECT name FROM adresse WHERE id = '".$empfanengerid."'");


        $antwort = array(
          'von' => $this->app->User->GetName()." <".$this->app->erp->GetFirmaMail().">",
          'an' => "kontakt@wawision.de",
          'betreff' => $betreff
          );

        echo json_encode($antwort);
        exit;
        break;
      case 'sendmail':
        $kundenid = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('id'));
        $von = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('von'));
        $an = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('an'));
        $betreff = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('betreff'));
        $nachricht = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('nachricht'));

        $this->supportapplogbucheintrag($kundenid, $this->app->User->GetAdresse(), 'An Vertrieb: '.substr($betreff, 0,25).((strlen($betreff) > 25?'...':'')));

        $von = explode('<', trim($von, '> '));
        $an = explode('<', trim($an, '> '));

        $mailSend = $this->app->erp->MailSend(
            $von[1], //Mail
            $von[0], //Von
            $an[1],
            $an[0],
            $betreff,
            $nachricht
            );

        echo json_encode("success");
        exit;
        break;
      case 'holevorlage':
        $vorlageid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $daten = $this->app->DB->SelectArr("SELECT taetigkeit, beschreibung FROM supportapp_vorlagen WHERE id = '$vorlageid'");
        $daten = reset($daten);
        echo json_encode($daten);
        exit;
        break;
      default:
        # code...
        break;
    }

    $kundennr = $this->app->DB->real_escape_string($this->app->Secure->GetGET('kdnr'));
    $kundenid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
    $this->app->erp->Headlines('','Auftrag');
    if($kundennr != ''){
      $kundenname = $this->app->DB->Select("SELECT name FROM adresse WHERE kundennummer = '".$kundennr."'");
      $kundenid = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer = '".$kundennr."'");
    }else{
      if($kundenid != ''){
        $kundenname = $this->app->DB->Select("SELECT name FROM adresse WHERE id = '".$kundenid."'");
        $kundennr = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '".$kundenid."'");
        $this->app->erp->Headlines('','','<a href="index.php?module=adresse&action=edit&id='.$kundenid.'" target="_blank">'.$kundennr.' '.$kundenname.'</a>');
      }
    }

    $nameduser = $this->app->DB->Select("SELECT freifeld5 FROM adresse WHERE id = '".$kundenid."'");
    $sperrvermerk = trim($this->app->DB->Select("SELECT freifeld6 FROM adresse WHERE id = '".$kundenid."'"));
    //$telefonsupport = trim($this->app->DB->Select("SELECT freifeld7 FROM adresse WHERE id = '".$kundenid."'"));
    //$techniksupport = trim($this->app->DB->Select("SELECT freifeld8 FROM adresse WHERE id = '".$kundenid."'"));

    $artikektelefonarr = array(0);
    $artikektechnikerarr = array(0);
    $artikekenterprisearr = array(0);

    $artikeltmp = $this->app->DB->SelectArr("SELECT wa.* FROM supportapp_artikel wa JOIN artikel a ON wa.artikel = a.id WHERE a.geloescht = 0");
    for ($i=0; $i < count($artikeltmp); $i++) { 
      switch ($artikeltmp[$i]['typ']) {
        case '1':
          $artikektelefonarr[] = $artikeltmp[$i]['artikel'];
          break;
        case '2':
          $artikektechnikerarr[] = $artikeltmp[$i]['artikel'];
          break;
        case '3':
          $artikekenterprisearr[] = $artikeltmp[$i]['artikel'];
          break;
      }
    }
    $telefonsupport = $this->app->DB->Select("SELECT id FROM abrechnungsartikel  WHERE artikel in (".implode(', ', $artikektelefonarr).")  AND artikel <> 0 AND (enddatum IS NULL OR enddatum >= CURDATE()) AND adresse = '$kundenid' LIMIT 1");

    $techniksupport = $this->app->DB->Select("SELECT id FROM abrechnungsartikel  WHERE artikel in (".implode(', ', $artikektechnikerarr).") AND artikel <> 0 AND (enddatum IS NULL OR enddatum >= CURDATE()) AND adresse = '$kundenid' LIMIT 1");

    $supportvertrag = $this->app->DB->Select("SELECT id FROM abrechnungsartikel  WHERE artikel in (".implode(', ', $artikekenterprisearr).") AND artikel <> 0 AND (enddatum IS NULL OR enddatum >= CURDATE()) AND adresse = '$kundenid' LIMIT 1");

    $supportvertrag2 = $this->app->DB->Select("SELECT rp.id FROM rechnung_position rp LEFT JOIN rechnung r ON rp.rechnung = r.id WHERE artikel in (".implode(', ', $artikekenterprisearr).") AND artikel <> 0 AND adresse = '$kundenid' AND r.datum >= (now() - INTERVAL 1 YEAR)");


    $rechnungenbezahlt = trim($this->app->DB->Select("SELECT freifeld9 FROM adresse WHERE id = '".$kundenid."'"));
    $pluspaketamlaufen = $this->app->DB->Select("SELECT COUNT(w.id) FROM supportapp w LEFT JOIN adresse ma ON ma.id=w.mitarbeiter LEFT JOIN adresse ku ON ku.id=w.adresse  WHERE  w.status='gestartet' AND adresse = '".$kundenid."'");

    if($pluspaketamlaufen) $telefonsupport = $pluspaketamlaufen; // Wenn Kunde Pluspaket hat ist Telefonsupport auch automatisch dabei

    $sonstiges = $this->app->DB->Select("SELECT sonstiges FROM adresse WHERE id = '".$kundenid."'");


    if($kundenid != ''){
      $gruppenzumhinzufuegen= $this->app->DB->SelectArr("SELECT ap.id, was.gruppe FROM auftrag_position ap JOIN auftrag a ON ap.auftrag = a.id LEFT JOIN supportapp_gruppen wag ON ap.artikel = wag.artikel LEFT JOIN supportapp_schritte was ON wag.id = was.gruppe LEFT JOIN supportapp_auftrag_check wac ON wac.auftragposition = ap.id WHERE a.adresse = '$kundenid' AND a.status <> 'storniert' AND a.belegnr <> '' AND wag.aktiv = 1 AND was.aktiv = 1 AND ISNULL(wac.id) GROUP BY ap.id");
      for ($i=0; $i < count($gruppenzumhinzufuegen); $i++) {
        $einzelschritte = $this->app->DB->Select("SELECT * FROM supportapp_schritte WHERE aktiv = 1 AND gruppe = ".$gruppenzumhinzufuegen[$i]['gruppe']);
        for ($j=0; $j < count($einzelschritte); $j++) {
          $vorhanden = $this->app->DB->Select("SELECT id FROM supportapp_auftrag_check WHERE auftragposition = '".$gruppenzumhinzufuegen[$i]['id']."' AND gruppe = '".$gruppenzumhinzufuegen[$i]['gruppe']."' AND adresse = '$kundenid' AND schritt = '".$einzelschritte[$j]['id']."' LIMIT 1");
          if($vorhanden == ''){
            $this->app->DB->Insert("INSERT INTO supportapp_auftrag_check (adresse, gruppe, schritt, auftragposition, status) VALUES ('$kundenid','".$gruppenzumhinzufuegen[$i]['gruppe']."','".$einzelschritte[$j]['id']."','".$gruppenzumhinzufuegen[$i]['id']."','0')");
          }
        }
      }
    }

    /*
    $updatedaten = $this->app->DB->SelectArr("SELECT w.*,
      date_format(versionupdate,'%d.%m.%Y %H:%i:%s') as versionupdatede ,
      date_format(ioncube_expdate,'%d.%m.%Y') as ioncube_expdatede,
      datediff(ioncube_expdate,CURDATE()) as diff  FROM wawisionsupport w WHERE adresse = '$kundenid' LIMIT 1");
    if($updatedaten)
    {
      $updatedaten = reset($updatedaten);
      $this->app->Tpl->Add('UPDATE_MODULLIST',str_replace(',',', ',$updatedaten['module']));

      $revision = $updatedaten['revision'];
      if($revision)$revision = '<a target="_blank" href="http://192.168.0.81/versionen/versionen.php?version='.$revision.'">'.$revision.'</a>';
      $this->app->Tpl->Add('UPDATE_REVISION',$revision);
      $this->app->Tpl->Add('UPDATE_MAXUSER', $updatedaten['ioncube_maxuser']);
      $this->app->Tpl->Add('UPDATE_MAXLIGHTUSER', $updatedaten['ioncube_maxlightuser']);
      $this->app->Tpl->Add('UPDATE_VERSIONUPATE', $updatedaten['versionupdatede']);
      $this->app->Tpl->Add('UPDATE_ABLAUFAM',$updatedaten['ioncube_expdatede']);
      $this->app->Tpl->Add('UPDATE_ABLAUFIN',$updatedaten['diff']);
      $this->app->Tpl->Add('UPDATE_VERSIONSHINWEIS',$updatedaten['versionshinweis']);

      $this->app->Tpl->Add('UPDATE_DEAKTIVIEREN', $updatedaten['ioncube_deaktivateonexp']?'ja':'nein');
      $this->app->Tpl->Add('UPDATE_GESPERRT', $updatedaten['gesperrt']?'ja':'nein');
      $this->app->Tpl->Add('UPDATE_TESTLIZENZ', $updatedaten['testlizenz']?'ja':'nein');
      $this->app->Tpl->Add('UPDATE_CLOUD', $updatedaten['cloud']!=''?ucfirst($updatedaten['cloud']):'Kauf Erstvertrag');

      if($updatedaten['module_custom']!='')
      {
        $this->app->Tpl->Add('UPDATEWARNUNG','<div class="error">Kundenspezifische Modifikationen vorhanden!</div>');

        $module_custom = json_decode($updatedaten['module_custom']);
        foreach($module_custom as $datei => $arr)
        {
          if(strpos($datei,'/download/') !== 0)
          {
            $this->app->Tpl->Add('UEBERLADENLISTE','<div >'.$datei.'');
            
            //foreach($arr as $fkey => $arr2)
           // {
           //   $this->app->Tpl->Add('UEBERLADENLISTE','<pre>'."\r\n...\r\n");
           //   foreach($arr2 as $v)$this->app->Tpl->Add('UEBERLADENLISTE',$v);
           //   $this->app->Tpl->Add('UEBERLADENLISTE',"\r\n...\r\n".'</pre><br /><br />');
            //}
            $this->app->Tpl->Add('UEBERLADENLISTE','</div>');
          }
        }

      }
    }*/
    //$this->app->Tpl->Add('MODULEUPDATE');


    $updates ='<table class="mkTable" cellpadding="0" cellspacing="0">
      <tr>
        <td>Datum</td>
        <td>Update</td>
        <td>Info 1</td>
        <td>Info 2</td>
        <td>Info 3</td>
      </tr>';
    $updates .= '
    <tr>
      <td>dummy</td>
      <td>dummy</td>
      <td>Info 1</td>
      <td>Info 2</td>
      <td>Info 3</td>
    </tr>';
    $updates .= '</table>';

    $module ='<table class="mkTable" cellpadding="0" cellspacing="0">
      <tr>
        <td>Datum</td>
        <td>Nummer</td>
        <td>Name</td>
        <td>Menge</td>
        <td>Preis</td>
        <td>Rabatt</td>
      </tr>';
    $modules = $this->app->DB->SelectArr("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') AS datum, ar.nummer, ap.bezeichnung, ".$this->app->erp->FormatMenge("ap.menge")." AS menge, ".$this->app->erp->FormatPreis("ap.preis",2)." AS preis, ".$this->app->erp->FormatPreis("ap.rabatt",2)." AS rabatt FROM auftrag a LEFT JOIN auftrag_position ap ON a.id = ap.auftrag LEFT JOIN artikel ar ON ar.id = ap.artikel WHERE a.status <> 'angelegt' AND a.status <> 'storniert' AND a.adresse = '$kundenid'");
    for ($i=0; $i < count($modules); $i++) {
      $module .= '
      <tr>
        <td>'.$modules[$i]['datum'].'</td>
        <td>'.$modules[$i]['nummer'].'</td>
        <td>'.$modules[$i]['bezeichnung'].'</td>
        <td align="right">'.$modules[$i]['menge'].'</td>
        <td align="right">'.$modules[$i]['preis'].'</td>
        <td  align="right">'.$modules[$i]['rabatt'].'</td>
      </tr>';
    }
    $module .= '</table>';

    $belege ='<table class="mkTable" cellpadding="0" cellspacing="0">
      <tr>
        <td>Art</td>
        <td>Beleg</td>
        <td>Datum</td>
        <td>Summe</td>
        <td>Status</td>
        <td></td>
      </tr>';
    $steuersatznormal = 1+$this->app->erp->GetStandardSteuersatzNormal()/100;
    $steuersatzermaessigt = 1+$this->app->erp->GetStandardSteuersatzErmaessigt()/100;
    $beleges = $this->app->DB->SelectArr("SELECT 'Angebot' AS art,a.belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') AS datum, ".$this->app->erp->FormatPreis("IF(ISNULL(SUM(ap.preis)),0,SUM(ap.preis*ap.menge*IF(ap.umsatzsteuer = 'normal',$steuersatznormal,$steuersatzermaessigt)))",2)." AS summe, a.status, CONCAT('<a href=\"index.php?module=angebot&action=pdf&id=',a.id,'\"><img src=\"themes/new/images/pdf.svg\" border=\"0\"></a>') AS pdf FROM angebot a LEFT JOIN angebot_position ap ON a.id = ap.angebot WHERE adresse = '$kundenid' GROUP BY a.id UNION SELECT 'Auftrag' AS art,a.belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') AS datum, ".$this->app->erp->FormatPreis("IF(ISNULL(SUM(ap.preis)),0,SUM(ap.preis*ap.menge*IF(ap.umsatzsteuer = 'normal',$steuersatznormal,$steuersatzermaessigt)))",2)." AS summe, a.status, CONCAT('<a href=\"index.php?module=auftrag&action=pdf&id=',a.id,'\"><img src=\"themes/new/images/pdf.svg\" border=\"0\"></a>') AS pdf FROM auftrag a LEFT JOIN auftrag_position ap ON a.id = ap.auftrag WHERE adresse = '$kundenid' GROUP BY a.id");
    for ($i=0; $i < count($beleges); $i++) {
      $belege .= '
      <tr>
        <td>'.$beleges[$i]['art'].'</td>
        <td>'.$beleges[$i]['belegnr'].'</td>
        <td>'.$beleges[$i]['datum'].'</td>
        <td align="right">'.$beleges[$i]['summe'].'</td>
        <td>'.$beleges[$i]['status'].'</td>
        <td><center>'.$beleges[$i]['pdf'].'</center></td>
      </tr>';
    }
    $belege .= '</table>';

    $logbuch ='<table class="mkTable" cellpadding="0" cellspacing="0">
      <tr>
        <td>Datum</td>
        <td>Bearbeiter</td>
        <td>Details</td>
      </tr>';
    $logbuchs = $this->app->DB->SelectArr("SELECT wl.logdatei, a.name, wl.details FROM supportapp_log wl LEFT JOIN adresse a ON wl.bearbeiter = a.id WHERE adresse = '$kundenid' ORDER BY wl.logdatei DESC");  

    for ($i=0; $i < count($logbuchs); $i++) {
      $logbuch .= '
      <tr>
        <td>'.date_format(date_create($logbuchs[$i]['logdatei']), 'H:i d.m.y').'</td>
        <td>'.$logbuchs[$i]['name'].'</td>
        <td>'.$logbuchs[$i]['details'].'</td>
      </tr>';
    }
    $logbuch .= '</table>';

    $einrichtung ='<table class="mkTable" cellpadding="0" cellspacing="0">
      <tr>
        <td>Status</td>
        <td>Startdatum</td>
        <td>Bemerkung</td>
      </tr>';
    $einrichtungs = $this->app->DB->SelectArr("SELECT status, DATE_FORMAT(startdatum,'%d.%m.%Y') AS startdatum ,bemerkung FROM supportapp WHERE adresse='$kundenid' ORDER BY startdatum");  
    for ($i=0; $i < count($einrichtungs); $i++) {
      $einrichtung .= '
      <tr>
        <td>'.$einrichtungs[$i]['status'].'</td>
        <td>'.$einrichtungs[$i]['startdatum'].'</td>
        <td>'.$einrichtungs[$i]['bemerkung'].'</td>
      </tr>';
    }
    $einrichtung .= '</table>';


    $gruppen = $this->app->DB->SelectArr("SELECT wag.id AS id, ap.id AS auftragsposition, a.belegnr AS auftrag, ap.bezeichnung AS bezeichnung, COUNT(was.id) AS gesamt, x.erledigt FROM auftrag_position ap JOIN auftrag a ON ap.auftrag = a.id LEFT JOIN supportapp_gruppen wag ON ap.artikel = wag.artikel LEFT JOIN supportapp_schritte was ON wag.id = was.gruppe LEFT JOIN (SELECT auftragposition, SUM(status) AS erledigt FROM supportapp_auftrag_check WHERE adresse = '$kundenid' GROUP BY auftragposition) x ON x.auftragposition = ap.id WHERE a.adresse = '$kundenid' AND a.status <> 'storniert' AND a.belegnr <> '' AND wag.aktiv = 1 AND was.aktiv = 1 GROUP BY ap.id");
    $checkboxen = "<table>";
    $zeilen = 0;
    $kopfzeilen = 0;
    for ($i=0; $i < count($gruppen); $i++) {
      if($gruppen[$i]['gesamt'] > $gruppen[$i]['erledigt']){
        $schritte = $this->app->DB->SelectArr("SELECT ws.*, IF(wac.status=1,1,0) AS status FROM supportapp_schritte ws LEFT JOIN (SELECT * FROM supportapp_auftrag_check WHERE adresse = '".$kundenid."' AND auftragposition = '".$gruppen[$i]['auftragsposition']."') wac ON ws.id = wac.schritt WHERE ws.gruppe = '".$gruppen[$i]['id']."' AND ws.aktiv = '1' ORDER BY sort ASC");
        $checkboxen .= '<tr><td colspan="4"><b><u>'.$gruppen[$i]['auftrag']." - ".$gruppen[$i]['bezeichnung'].'<u><b></td><tr>';
        $kopfzeilen++;
        $checkboxen .= '<tr>';
        for ($j=0; $j < count($schritte); $j++) {
          if(($j % 4) == 0 && $j != 0){
            $checkboxen .= '</tr><tr>';
          }
          if(($j % 4) == 0 || $j == 0){
            $zeilen++;
          }
          $cbname = 'gs_'.$gruppen[$i]['id'].'_'.$schritte[$j]['id'].'_'.$gruppen[$i]['auftragsposition'];
          $checkboxen .= '<td><input type="checkbox" name="'.$cbname.'" id="'.$cbname.'" onchange="changeschritt(\''.$kundenid.'\',\''.$cbname.'\');" '.($schritte[$j]['status']=='1'?'checked':'').'></td><td colspan=2><label for="'.$cbname.'">'.$schritte[$j]['bezeichnung'].'</label></td>';
        }
        $checkboxen .="</tr><tr>";
        $checkboxen .= '</tr>';
      }
    }
    $checkboxen .= "</table>";

    $stundensumme = $this->app->DB->Select("SELECT IFNULL(SUM(TIME_TO_SEC(TIMEDIFF(bis, von)))/3600,0) AS Dauer FROM zeiterfassung WHERE adresse_abrechnung = '$kundenid' AND MONTH(von) = MONTH(CURRENT_DATE) AND YEAR(von) = YEAR(CURRENT_DATE)");
    if($stundensumme == "")
      $stundensumme = 0;
    $stundensumme = (int)$stundensumme.":".round(fmod($stundensumme,1)*60);
    $stundensummejahr = $this->app->DB->Select("SELECT IFNULL(SUM(TIME_TO_SEC(TIMEDIFF(bis, von)))/3600,0) AS Dauer FROM zeiterfassung WHERE adresse_abrechnung = '$kundenid' AND YEAR(von) = YEAR(CURRENT_DATE)");
    if($stundensummejahr == "")
      $stundensummejahr = 0;
    $stundensummejahr = (int)$stundensummejahr.":".round(fmod($stundensummejahr,1)*60);

    $timestamp = strtotime(date("Y-m-d"));
    $resttagemonat = (int)date('t', $timestamp) - (int)date('j', $timestamp);
    $this->supportappMenuNormal($kundenid);
    $vorlagenarray = $this->app->DB->SelectArr("SELECT * FROM supportapp_vorlagen");
    $vorlagen = "";
    for ($i=0; $i < count($vorlagenarray); $i++) {
      $vorlagen .= '<option value="'.$vorlagenarray[$i]['id'].'">'.$vorlagenarray[$i]['bezeichnung'].'</option>';
    }


    if($telefonsupport == ''){
      $this->app->Tpl->Set("TELEFON", "telefonsupport_grey.png");
      $this->app->Tpl->Set("TELEFONTITLE", "Kein Telefonsupport");
    }else{
      $this->app->Tpl->Set("TELEFON", "telefonsupport_go.png");
      $this->app->Tpl->Set("TELEFONTITLE", "Telefonsupport vorhanden");
    }

    if($techniksupport == ''){
      $this->app->Tpl->Set("TECHNIK", "technikersupport_grey.png");
      $this->app->Tpl->Set("TECHNIKTITLE", "Kein Technikersupport");
    }else{
      $this->app->Tpl->Set("TECHNIK", "technikersupport_go.png");
      $this->app->Tpl->Set("TECHNIKTITLE", "Technikersupport vorhanden");
    }

    if($rechnungenbezahlt == ''){
      $this->app->Tpl->Set("GELD", "bezahlsupport_grey.png");
      $this->app->Tpl->Set("GELDTITLE", "Kein Bezahlsupport");
    }else{
      $this->app->Tpl->Set("GELD", "bezahlsupport_go.png");
      $this->app->Tpl->Set("GELDTITLE", "Bezahlsupport vorhanden");
    }

    if($pluspaketamlaufen > 0){
      $this->app->Tpl->Set("PLUS", "pluspaket_go.png");
      $this->app->Tpl->Set("PLUSTITLE", "Pluspaket vorhanden");
    }else{
      $this->app->Tpl->Set("PLUS", "pluspaket_grey.png");
      $this->app->Tpl->Set("PLUSTITLE", "Kein Pluspaket");
    }

    if($supportvertrag > 0 || $supportvertrag2 >0){
      $this->app->Tpl->Set("VERTRAG", "vertrag_go.png");
      $this->app->Tpl->Set("VERTRAGTITLE", "Supportvertrag vorhanden");
    }else{
      $this->app->Tpl->Set("VERTRAG", "vertrag_grey.png");
      $this->app->Tpl->Set("VERTRAGTITLE", "Kein Supportvertrag");
    }

    $this->app->YUI->AutoComplete("kunde","kunde");
    if($kundenid != ''){
      $this->app->Tpl->Set("ADRESSID", $kundenid);
    }else{
      $this->app->Tpl->Set('ELEMENTESICHTBAR', "display:none;");
    }
    $this->app->YUI->AutoComplete("bearbeiter","mitarbeiter");


    $bearbeiteradresse = $this->app->User->GetAdresse();
    $bearbeitertext = $this->app->DB->Select("SELECT CONCAT (mitarbeiternummer, ' ', name) FROM adresse WHERE id = $bearbeiteradresse LIMIT 1");
    $this->app->Tpl->Set("BEARBEITER",$bearbeitertext);
    $this->app->Tpl->Set("KUNDE",$kundennr." ".$kundenname);
    $this->app->Tpl->Set("NAMEDUSER", $nameduser);
    $this->app->Tpl->Set("VERSION", "TODO");

    if($sperrvermerk != ''){
      $this->app->Tpl->Set("SPERRWARNUNG", "background-color: red;border: 0px solid red;border-top: 25px solid red;");
      $this->app->Tpl->Set("SPERRVERMERK", "<td>Sperrvermerk: </td><td colspan=\"3\">".$sperrvermerk."</td>");
    }

    //$this->app->Tpl->Set("UPDATES", $updates);
    $this->app->Tpl->Set("MODULELISTE", $module);
    $this->app->Tpl->Set("BELEGE", $belege);
    $this->app->Tpl->Set("PAKETE", $pakete);
    $this->app->Tpl->Set("EINRICHTUNG", $einrichtung);
    $this->app->Tpl->Set("LOGBUCH", $logbuch);
    $this->app->Tpl->Set("STARTSTOPVORLAGE", $vorlagen);

    $this->app->Tpl->Set("NOTIZEN", $sonstiges);

    $hoehe = $zeilen*2.5;
    $hoehe += $kopfzeilen*1.8;
    $hoehe += 1;
    if($hoehe == 0){
      $this->app->Tpl->Set("PLUSPAKETATTRIBUT", 'display:none;');
    }else{
      $this->app->Tpl->Set("PLUSPAKETATTRIBUT", 'height:'.($hoehe>1?$hoehe:'1').'em');
    }

    $this->app->Tpl->Set("RESTTAGEMONAT", $resttagemonat);
    $this->app->Tpl->Set("STUNDENSUMME", $stundensumme);
    $this->app->Tpl->Set("STUNDENSUMMEJAHR", $stundensummejahr);
    $this->app->Tpl->Set("CHECKBOXEN", $checkboxen);

    $startstop = unserialize($this->app->User->GetParameter("supportapp_startstop"));

    if($startstop[$kundenid]['status'] == 1){
      $startstopzeit = $startstop[$kundenid]['zeit'];
      $sekunden = strtotime(date("Y-m-d H:i:s")) - date_create_from_format('d.m.Y H:i', $startstopzeit)->getTimestamp();
      $this->app->Tpl->Set("STARTSTOPTEXT", "Zeiterfassung l&auml;uft");
      $this->app->Tpl->Set("STARTSTOPSTATUS", '1');
      $this->app->Tpl->Set('STARTSTOPFARBE', '#E73028');
      $this->app->Tpl->Set("STARTSTOPSEKUNDEN", $sekunden);
    }else{
      $this->app->Tpl->Set("STARTSTOPTEXT", "Zeiterfassung starten");
      $this->app->Tpl->Set("STARTSTOPSTATUS", '0');
      $this->app->Tpl->Set('STARTSTOPFARBE', '#A2D624');
      $this->app->Tpl->Set("STARTSTOPSEKUNDEN", '0');
    }

    $this->app->YUI->AutoComplete("startstopmitarbeiter","mitarbeiter");
    $this->app->YUI->AutoComplete("startstopprojekt","projektname");
    //$this->app->YUI->CkEditor("nachricht","all",array('height'=>'11em', 'width'=>'100%'));
    $this->app->YUI->CkEditor("nachricht","all",array('height'=>'10em', 'width'=>'100%'));
    $this->app->YUI->CkEditor("notizen","minimal",array('height'=>'11em', 'width'=>'100%'));
    $this->app->YUI->TableSearch('TAB1','supportapp_zeiterfassung', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","supportapp_auftrag.tpl");
  }  

  function supportappSchritte()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    switch ($cmd) {
      case 'schrittegetfuergruppe':
        $gruppe = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('gruppe'));
        $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));

        $gruppenid = $this->app->DB->Select("SELECT id FROM supportapp_gruppen WHERE bezeichnung = '$gruppe' LIMIT 1");
        $vorgaenger = $this->app->DB->SelectArr("SELECT id, bezeichnung FROM supportapp_schritte WHERE gruppe='$gruppenid' AND id <>'$id' AND aktiv = 1");
        echo json_encode($vorgaenger);
        exit;
        break;
      case 'schritteget':
        $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));

        $result = $this->app->DB->SelectArr("SELECT ws.id AS id, ws.bezeichnung AS bezeichnung, ws.beschreibung AS beschreibung, wg.bezeichnung AS gruppe, ws.aktiv AS aktiv, ws.sort AS sort, ws.filter AS filter, wg.id AS gruppenid, ws.vorgaenger AS vorgaenger FROM supportapp_schritte ws JOIN supportapp_gruppen wg ON ws.gruppe = wg.id WHERE ws.id = '$id'");
        $result = reset($result);
        $vorgaenger = $this->app->DB->SelectArr("SELECT id, bezeichnung FROM supportapp_schritte WHERE gruppe=".$result['gruppenid']." AND id <>'$id' AND aktiv = 1");
        $result['vorgaengerauflistung'] = $vorgaenger;
        echo json_encode($result);
        exit;
        break;
      case 'schritteneuedit':
        $id = $this->app->Secure->GetPOST('id');
        $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
        $beschreibung = $this->app->Secure->GetPOST('beschreibung');
        $gruppe = $this->app->Secure->GetPOST('gruppe');
        $reihenfolge = $this->app->Secure->GetPOST('reihenfolge');
        $aktiv = $this->app->Secure->GetPOST('aktiv');
        $filter = $this->app->Secure->GetPOST('filter');
        $vorgaenger = $this->app->Secure->GetPOST('vorgaenger');

        if(!is_numeric($reihenfolge)){
          $reihenfolge = 0;
        }
        $gruppenid = $this->app->DB->Select("SELECT id FROM supportapp_gruppen WHERE bezeichnung = '$gruppe'");
        if($gruppenid != 0 ){
          if($id == '0'){
            $this->app->DB->Insert("INSERT INTO supportapp_schritte (bezeichnung, beschreibung, gruppe, sort, aktiv, filter, vorgaenger) VALUES ('$bezeichnung','$beschreibung', '$gruppenid', '$reihenfolge', '$aktiv', '$filter', '$vorgaenger')");
          }else{
            $this->app->DB->Update("UPDATE supportapp_schritte SET bezeichnung = '$bezeichnung', beschreibung = '$beschreibung', gruppe = '$gruppenid', sort = '$reihenfolge', aktiv = '$aktiv', filter = '$filter', vorgaenger = '$vorgaenger' WHERE id = $id");
          }
        }else{
          echo json_encode("Gruppe konnte nicht gefunden werden.");
          exit;
        }
        echo json_encode("success");
        exit;
      case 'schrittedelete':
        $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $this->app->DB->Delete("DELETE FROM supportapp_schritte WHERE id = '$id'");
        echo json_encode('success');
        exit;
        break;
      case 'gruppeget':
        $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $result = $this->app->DB->SelectArr("SELECT * FROM supportapp_gruppen WHERE id = '$id'");
        $result = reset($result);
        $artikel = $this->app->DB->SelectArr("SELECT nummer, name_de FROM artikel WHERE id = '".$result['artikel']."'");
        $artikel = reset($artikel);
        $result['artikel'] = $artikel['nummer']." ".$artikel['name_de'];
        echo json_encode($result);
        exit;
        break;
      case 'gruppenneuedit':
        $id = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('id'));
        $aktiv = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('aktiv'));
        $bezeichnung = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('bezeichnung'));
        $artikel = explode(" ", $this->app->DB->real_escape_string($this->app->Secure->GetPOST('artikel')));
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '".$artikel[0]."'");
        if($id == '0'){
          $this->app->DB->Insert("INSERT INTO supportapp_gruppen (bezeichnung, aktiv,artikel) VALUES ('$bezeichnung', '$aktiv', '$artikelid')");
        }else{
          $this->app->DB->Update("UPDATE supportapp_gruppen SET bezeichnung = '$bezeichnung', aktiv = '$aktiv', artikel = '$artikelid' WHERE id = '$id'");
        }
        echo json_encode("success");
        exit;
      case 'gruppedelete':
        $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
        $this->app->DB->Delete("DELETE FROM supportapp_schritte WHERE gruppe = '$id'");
        $this->app->DB->Delete("DELETE FROM supportapp_gruppen WHERE id = '$id'");
        echo json_encode('success');
        exit;
        break;

        break;
      default:
        break;
    }


    $this->supportappMenuNormal();
    $this->app->erp->Headlines('','Schritte');
    $this->app->YUI->TableSearch('TAB1','supportapp_schritte', "show","","",basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('TAB2','supportapp_gruppen', "show","","",basename(__FILE__), __CLASS__);

    $this->app->YUI->CkEditor("beschreibung","all",array('height'=>'100px', 'width'=>'545px'));

    $this->app->YUI->AutoComplete("gruppe","supportapp_gruppen");
    $this->app->YUI->AutoComplete("gruppeartikel","artikelnummer");

    $this->app->Tpl->Parse("PAGE","supportapp_schritte.tpl");
  }
  function supportappKunden()
  {
    $this->supportappMenuNormal();
    $this->app->erp->Headlines('','Kunden');
    $this->app->YUI->TableSearch('TAB1','supportapp_kunden', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse("PAGE","supportapp_kunden.tpl");
  }




  function supportappMiniDetail()
  {
    $id = (int)$this->app->Secure->GetGET("id");

    $adresse = $this->app->DB->Select("SELECT adresse FROM supportapp WHERE id='$id' LIMIT 1");

    $table = new EasyTable($this->app);

    $table->Query("SELECT DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR')) AS Datum,
          z.aufgabe as Taetigkeit,

          TIME_FORMAT(TIMEDIFF(z.bis, z.von),'%H:%i') AS Dauer,
          a.name as mitarbeiter

          FROM zeiterfassung z LEFT JOIN projekt p ON p.id=z.projekt LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id LEFT JOIN adresse a ON z.adresse=a.id
          WHERE z.adresse_abrechnung=".$adresse."
          ORDER BY z.id DESC LIMIT 100
          ");
    $table->DisplayNew('LETZTEBUCHUNGEN',"Mitarbeiter","noAction");

    $adr = $this->app->DB->SelectArr("select *, DATE_FORMAT(mandatsreferenzdatum, '%e.%m.%Y') AS mandatsreferenzdatumd from adresse where id = ".$adresse." limit 1");
    if($adr)
    {

      $adr = reset($adr);

      $this->app->Tpl->Set('EMAIL',$adr['email']);
      $this->app->Tpl->Set('MOBIL',$adr['mobil']);
      $this->app->Tpl->Set('TELEFAX',$adr['telefax']);
      $this->app->Tpl->Set('TELEFON',$adr['telefon']);
      $this->app->Tpl->Set('ORT',$adr['ort']);
      $this->app->Tpl->Set('LAND',$adr['land']);
      $this->app->Tpl->Set('PLZ',$adr['plz']);
      $this->app->Tpl->Set('STRASSE',$adr['strasse']);
      $this->app->Tpl->Set('ANSPRECHPARTNERNAME',$adr['ansprechpartner']);

    }

    $table = new EasyTable($this->app);
    $table->Query("SELECT a.name, a.bereich, a.email, a.telefon, a.mobil FROM ansprechpartner a WHERE adresse='$adresse'  AND a.name!='Neuer Datensatz' ORDER by id DESC");
    $table->DisplayNew('ANSPRECHPARTNER',"Mobil","noAction");

    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(a.datum,'%d.%m.%Y') as datum,a.belegnr as auftrag, ap.nummer as 'Artikel-Nr.',CONCAT('<b>',ap.bezeichnung,'</b><br>',REPLACE(ap.beschreibung,'\r\n','<br>')) as bezeichnung, ap.menge FROM auftrag a LEFT JOIN auftrag_position ap ON ap.auftrag=a.id WHERE a.adresse='$adresse'  ORDER by ap.id DESC");
    $table->DisplayNew('ARTIKEL',"Menge","noAction",false,0,0,false);



    $this->app->Tpl->Output("supportapp_minidetail.tpl");
    exit;
  }

  function supportappCreate()
  {
    //Veraltet, kann raus
    $this->supportappMenu();
    $this->app->erp->Headlines('','Anlegen');
    parent::supportappCreate();
  }


  function supportappEdit()
  {
    $id = (int)$this->app->Secure->GetGET("id");
    $adresse = $this->app->DB->Select("SELECT adresse FROM supportapp WHERE id = '$id' LIMIT 1");
    $this->app->erp->Headlines('','Bearbeiten',$this->app->DB->Select("SELECT concat('<a href=\"index.php?module=adresse&action=edit&id=',id,'\">',kundennummer,' ',name,'</a>') FROM adresse WHERE id = '$adresse' LIMIT 1"));
    $this->app->erp->MenuEintrag("index.php?module=supportapp&action=edit&id=$id","Details");
    $this->supportappMenu();
    parent::supportappEdit();
  }

  function supportapplogbucheintrag($adresse, $bearbeiter, $details){
    $this->app->DB->Insert("INSERT INTO supportapp_log (logdatei, adresse, bearbeiter, details) VALUES (NOW(), '$adresse', '$bearbeiter', '$details')");
  }

}
