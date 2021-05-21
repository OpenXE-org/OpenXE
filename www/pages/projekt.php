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

use Xentral\Components\Exporter\Collection\DataCollection;
use Xentral\Components\Exporter\Collection\FormatterCollection;
use Xentral\Components\Exporter\Csv\CsvConfig;
use Xentral\Components\Exporter\Csv\CsvExporter;
use Xentral\Components\Http\JsonResponse;

include '_gen/projekt.php';

class Projekt extends GenProjekt {
  var $app;
  var $ebene=0;
  var $voraengerbaum;
  var $projektwaehrung="";

  const MODULE_NAME = 'Project';

  /** @var array $javascript */
  public $javascript = [
    './classes/Modules/Task/www/js/task.js',
    './resources/js/lazyLoadComponent.js',
  ];

  static function TableSearch(&$app, $name, $erlaubtevars)
  {
    switch($name)
    {
      case "projekt_kopie":
        $allowed['projekt'] = array('create');

        $heading = array('Abkürzung', 'Beschreibung', 'Kd-Nr.', 'Kunde', 'Men&uuml;');
        $width = array('30%', '30%', '20%', '10%', '5%');
        $findcols = array('p.abkuerzung', 'p.name', 'a.kundennummer', 'a.name', 'p.id');
        $searchsql = array('p.abkuerzung', 'p.name', 'a.kundennummer', 'a.name');

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=projekt&action=create&schritt3=weiter&id=%value%\">" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>" . "&nbsp;</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, p.abkuerzung, p.name, a.kundennummer,a.name, p.id FROM projekt p LEFT JOIN adresse a ON a.id=p.kunde ";

        $where = " p.id > 0 ".$app->erp->ProjektRechte();

        $count = "SELECT COUNT(p.id) FROM projekt p LEFT JOIN adresse a ON a.id=p.kunde WHERE ".$where;
        break;
      case "projekt_mitarbeiterstundengeplant":
        $allowed['projekt'] = array('dashboard');
        $heading = array('Mitarbeiternummer','Mitarbeiter','Bezeichnung','Stunden','Stundenlohn','');
        $width = array('20%','25%','20%','15%','14%','1%');
        $findcols = array( 'a.mitarbeiternummer','a.name','tgz.bezeichnung','tgz.stunden','tgz.stundensatz', 'a.id');
        $searchsql = array('a.mitarbeiternummer','a.name','tgz.bezeichnung','tgz.stunden','tgz.stundensatz', 'a.id');
        
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        $defaultorderdesc = 1;

        $teilprojekt = (int)$app->User->GetParameter("teilprojekt_geplante_zeiten_teilprojekt");
        $id = (int)$app->Secure->GetGET('id');
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,a.mitarbeiternummer, a.name as name,
            concat('<input onchange=\"changestundensatzname(',a.id,',$teilprojekt)\" type=\"text\" value=\"',ifnull(tgz.bezeichnung,ifnull((SELECT zk.beschreibung FROM zeiterfassung_kosten zk WHERE zk.adresse = a.id AND (zk.gueltig_ab = '0000-00-00' OR zk.gueltig_ab <= curdate()) ORDER by zk.gueltig_ab DESC LIMIT 1 ),'')),'\" name=\"stundensatzname_',a.id,'\" id=\"stundensatzname_',a.id,'\" />') , 
            concat('<input onchange=\"changestundensatzname(',a.id,',$teilprojekt)\" type=\"text\" value=\"',ifnull(tgz.stunden,0),'\" name=\"stunden_',a.id,'\" id=\"stunden_',a.id,'\" />') , 
            concat('<input onchange=\"changestundensatzname(',a.id,',$teilprojekt)\" type=\"text\" value=\"',ifnull(tgz.stundensatz,ifnull((SELECT zk.stundensatz FROM zeiterfassung_kosten zk WHERE zk.adresse = a.id AND (zk.gueltig_ab = '0000-00-00' OR zk.gueltig_ab <= curdate()) ORDER by zk.gueltig_ab DESC LIMIT 1 ),0)),'\" name=\"stundensatz_',a.id,'\" id=\"stundensatz_',a.id,'\" />')
            , a.id as menu
            FROM adresse a 
            INNER JOIN adresse_rolle a2 ON a2.adresse=a.id 
            INNER JOIN projekt p ON p.id=a2.projekt OR p.id = a.projekt
            LEFT JOIN teilprojekt_geplante_zeiten tgz ON a.id = tgz.adresse AND tgz.teilprojekt = '$teilprojekt'";
        $groupby = " group by a.id";
        $where = " (a2.projekt='$id' AND (a2.bis='0000-00-00' OR a2.bis >= date(NOW())) AND a2.objekt like 'Projekt' AND a.geloescht != 1 AND mitarbeiternummer != '') OR not isnull(tgz.id) ";
        
        $count = "SELECT count(distinct a.id) FROM adresse a 
                                           
                                           INNER JOIN adresse_rolle a2 ON a2.adresse=a.id 
                                           INNER JOIN projekt p ON p.id=a2.projekt OR p.id = a.projekt
                                           LEFT JOIN teilprojekt_geplante_zeiten tgz ON a.id = tgz.adresse AND tgz.teilprojekt = '$teilprojekt'
                                           WHERE $where";
            
      break;
      case "projekt_verbindlichkeiten":
        $allowed['projekt'] = array('dashboard');
        $id = (int)$app->Secure->GetGET('id');
        
        
        $heading = array('','Nr', 'Teilprojekt', 'Lf-Nr.','Lieferant','RE-Datum','RE-Nr','Verwendungszweck','IBAN/BIC','Betrag (netto)', 'Betrag (brutto)', 'Skonto Bis', 'Zahlbar Bis', 'Skonto','Zahlweise','Bezahlt','Monitor', 'Men&uuml;');
        $width = array('1%','5%', '5%', '5%', '20%', '5%','5%', '20%','10%', '10%','20%','5%', '5%', '1%', '1%', '1%', '8%');
        $findcols = array('','v.belegnr', 'ap.aufgabe', 'a.lieferantennummer','a.name','v.rechnungsdatum','v.rechnung','v.verwendungszweck', "CONCAT('a.iban','/','a.swift')","if((SELECT SUM(bestellung_betrag_netto) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id'), ".$app->erp->FormatPreis("(SELECT SUM(bestellung_betrag_netto) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id')",2).",".$app->erp->FormatPreis("v.betrag-v.summenormal-v.summeermaessigt-v.summesatz3-v.summesatz4",2).")",

         "if((SELECT SUM(bestellung_betrag) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id'), ".$app->erp->FormatPreis("(SELECT SUM(bestellung_betrag) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id')",2).",".$app->erp->FormatPreis("v.betrag",2).")",

          'v.skontobis', 'v.zahlbarbis', 'v.skonto','v.zahlungsweise', "if(v.status='bezahlt','ja','nein')",'v.status', 'v.id');
        $searchsql = array('a.lieferantennummer', 'ap.aufgabe', 'a.name', "DATE_FORMAT(v.rechnungsdatum, '%d.%m.%Y')","if(v.status='bezahlt','ja','nein')", 'v.rechnung','v.rechnung',"CONCAT('a.iban','/','a.swift')",'v.verwendungszweck',"if((SELECT SUM(bestellung_betrag_netto) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id'), ".$app->erp->FormatPreis("(SELECT SUM(bestellung_betrag_netto) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id')",2).",".$app->erp->FormatPreis("v.betrag-v.summenormal-v.summeermaessigt-v.summesatz3-v.summesatz4",2).")", "if((SELECT SUM(bestellung_betrag) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id'), ".$app->erp->FormatPreis("(SELECT SUM(bestellung_betrag) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id')",2).",".$app->erp->FormatPreis("v.betrag",2).")", 'v.kostenstelle','v.zahlungsweise');
        $sumcol=array(10,11);

        $datecols = array(5,12);

        $defaultorder = 17;
        $defaultorderdesc = 1;
        $alignright = array(5,9,10,11,13);
        $moreinfo=true;
        $moreinfoaction = 'verbindlichkeit';
        $menucol = 17;
        $menu = "<table><tr><td nowrap><a target=\"_blank\" href=\"index.php?module=verbindlichkeit&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\"></a></td></tr></table>";

          // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, '<img src=./themes/{$app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
              v.belegnr as 'nr.', ap.aufgabe, a.lieferantennummer,a.name as lieferant,DATE_FORMAT(v.rechnungsdatum, '%d.%m.%Y'), if(v.betrag <0, CONCAT('<font color=red>',v.verwendungszweck,' ',if(v.rechnung!='','RE ',''),v.rechnung,'</font>'),v.rechnung),v.verwendungszweck,
                CONCAT(if(a.iban='',CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.id,'#tabs-2\" target=\"_blank\"><font color=red>fehlt - bitte nachtragen</font></a>'),a.iban),' / ',
                if(a.swift='',CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.id,'#tabs-2\" target=\"_blank\"><font color=red>fehlt - bitte nachtragen</font></a>'),a.swift)) as BIC, 
                  if((SELECT SUM(bestellung_betrag_netto) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id'), ".$app->erp->FormatPreis("(SELECT SUM(bestellung_betrag_netto) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id')",2).",".$app->erp->FormatPreis("v.betrag-v.summenormal-v.summeermaessigt-v.summesatz3-v.summesatz4",2)."), if((SELECT SUM(bestellung_betrag) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id'), ".$app->erp->FormatPreis("(SELECT SUM(bestellung_betrag) FROM verbindlichkeit_bestellungen WHERE verbindlichkeit = v.id AND bestellung_projekt = '$id')",2).",".$app->erp->FormatPreis("v.betrag",2)."),
                    if(v.skontobis='0000-00-00','-',if(v.skontobis >=NOW(),
                          CONCAT('<font color=red>',DATE_FORMAT(v.skontobis,'%d.%m.%Y'),'</font>'),DATE_FORMAT(v.skontobis,'%d.%m.%Y'))) as skonto_bis,
                      if(v.zahlbarbis='0000-00-00','-',DATE_FORMAT(v.zahlbarbis,'%d.%m.%Y')) as zahlbar_bis,
                        if(v.skonto > 0,CONCAT(v.skonto,' %'),'-') as skonto, if(v.zahlungsweise='','rechnung',v.zahlungsweise), if(v.status='bezahlt','ja','nein'),
                          (" . $app->YUI->IconsSQLVerbindlichkeit() . ")  as icons,
                            v.id FROM `verbindlichkeit` AS `v` LEFT JOIN adresse a ON v.adresse = a.id LEFT JOIN arbeitspaket ap ON v.teilprojekt = ap.id
                            
                            
                            
                            ";


        // Fester filter
        $where = " (v.projekt = '$id' OR v.id IN(SELECT DISTINCT v.id FROM verbindlichkeit v JOIN verbindlichkeit_bestellungen vb ON v.id = vb.verbindlichkeit WHERE vb.bestellung_projekt = '$id') ";
        for($i = 1; $i <= 15; $i++)$where .= " OR v.bestellung".$i."projekt = '$id' ";
        $where .= ") AND v.status_beleg != 'storniert' ";
        
        // gesamt anzahl
        $count = "SELECT COUNT(v.id) FROM `verbindlichkeit` AS `v` WHERE $where ";
        

            
      break;
      case 'projekttabelle':
        $allowed['projekt'] = array('list');

        $abgeschlossene = $app->YUI->TableSearchFilter($name, 12, 'abgeschlossene', 0,0,'checkbox');
        $alle = $app->YUI->TableSearchFilter($name, 13, 'alle', 0,0,'checkbox');
        $meine = $app->YUI->TableSearchFilter($name, 14, 'meine', 0,0,'checkbox');
        $geplant = $app->YUI->TableSearchFilter($name, 15, 'geplant', 0,0,'checkbox');
        // headings

        $heading = array('Kennung','Beschreibung','Kunden-Nr.','Kunde','Verantwortlicher');
        $width = array('8%', '20%','5%','15%', '15%');
        $moreinfo = true;
        $moreinfoaction='projektuebersicht';
        $findcols = array('p.abkuerzung','p.name','a.kundennummer','a.name','p.verantwortlicher');

        $defaultorder = 6;
        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $width[] = '8%';
        $heading[] = '&Ouml;ffentlich';
        $findcols[] = "if(p.oeffentlich,'ja','-')";
        $searchsql = array('p.name', 'p.abkuerzung', 'a2.name', 'a.kundennummer', 'a.name',"if(p.oeffentlich,'ja','-')");


        $heading[] = 'Men&uuml;';

        $menucol = count($heading)-1;

        $width[] = '1%';
        $findcols[] = 'p.id';
        $defaultorderdesc = 1;
        $menu .= "<td>";
        $menu .= "<a href=\"index.php?module=projekt&action=uebersicht&id=%value%\">";
        $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=projekt&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a href=\"#\" class=\"label-manager\" data-label-column-number=\"3\" data-label-reference-id=\"%value%\" data-label-reference-table=\"projekt\">";
        $menu .= "<span class=\"label-manager-icon\"></span>";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, '<img src=./themes/".$app->Conf->WFconf['defaulttheme']."/images/details_open.png class=details>' as open,";
        $sql .= "  p.abkuerzung, p.name, a.kundennummer,a.name, a2.name,";
        $sql .= "if(p.oeffentlich,'ja','-') as oeffentlich, $freifeldsql
                p.id as menu FROM projekt p LEFT JOIN adresse a ON a.id=p.kunde LEFT JOIN adresse a2 ON p.verantwortlicher = a2.id ";
        //if(p.status like 'abgeschlossen','abgeschlossen','offen') as status,
        // fester filter
        $where = " p.geloescht=0 " . $app->erp->ProjektRechte();

        if($alle)
        {

        }elseif($geplant)
        {
          $where .= ' AND p.status like \'geplant\' ';
        }elseif($abgeschlossene)
        {
          $where .= ' AND p.status like \'abgeschlossen\' ';
        }else{
          $where .= ' AND p.status not like \'geplant\' AND p.status not like \'abgeschlossen\' ';
        }

        if($meine){
          $where .= " AND (p.verantwortlicher = '".$app->User->GetAdresse()."' OR (SELECT count(ar.id) FROM adresse_rolle ar WHERE ar.projekt = p.id AND ar.adresse = '".$app->User->GetAdresse()."' )) ";
        }

        $count = "SELECT COUNT(p.id) FROM projekt p WHERE $where";
        break;

      case "projekt_logbuch":
        //$allowed['adresse'] = array('brief');

        $heading = array('','Datum', 'Titel','Ansprechpartner','Projekt','Bearbeiter', 'Art', 'Gesendet','', 'Men&uuml;');
        $width = array('1%','15%', '30%','10%','15%', '15%', '10%', '10%','1%', '1%');
        $findcols = array('a.open','a.datum', 'a.title','a.ansprechpartner','a.abkuerzung', 'a.bearbeiter', 'a.art','a.gesendet','a.did','a.did');
        $searchsql = array('a.datum', 'a.title','a.ansprechpartner','a.abkuerzung', 'a.bearbeiter','a.art','a.suchtext','a.internebezeichnung');

        $defaultorder = 2; // sortiert nach dem oeffnen nach spalte 2
        $defaultorderdesc = 1; // 0 = auftsteigend , 1 = absteigen (eventuell notfalls pruefen)
        //index.php?module=adresse&action=korreseditpopup&id=%value% popup
        $menu = "<center><span style=\"display:none;\">%value%</span><img src=\"themes/".$app->Conf->WFconf['defaulttheme']."/images/forward.svg\" border=\"0\" onclick=\"gotovorgang(this)\" style=\"cursor: pointer;\"></center>";

        $moreinfo = true;
        $doppelteids = true;
        $moreinfoaction = 'brief';
        $menucol = 9;

        $cmd = $app->Secure->GetGET("cmd");
        $id = (int)$app->Secure->GetGET("id");

        $sql = '
          SELECT
            SQL_CALC_FOUND_ROWS a.id,
            a.open,
            a.datum,
            if(ifnull(a.internebezeichnung,\'\') = \'\', a.title, concat(a.title,\'<br /><i style="color:grey">\',a.internebezeichnung,\'</i>\')),
            a.ansprechpartner,
            a.abkuerzung,
            a.bearbeiter,
            a.art,
            a.gesendet,
            a.pdf,
            a.did
          FROM 
          (
            (
              SELECT
                d.id,"<img src=./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                CONCAT(DATE_FORMAT(d.datum, "%Y-%m-%d"), " ", IF(d.uhrzeit IS NULL OR DATE_FORMAT(d.uhrzeit, "%H:%i")="00:00", "", DATE_FORMAT(d.uhrzeit, "%H:%i")) ) as datum,
                d.betreff as title,if(d.typ = \'email\',if(d.ansprechpartner <> \'\',d.ansprechpartner,d.email_an),d.ansprechpartner) as ansprechpartner,
                p.abkuerzung as abkuerzung,
                if(bearbeiter!="",bearbeiter,a2.name) as bearbeiter,
                CONCAT(UCASE(LEFT(d.typ, 1)), SUBSTRING(d.typ, 2)) as art,
                CONCAT(IF(d.sent = 1, "JA", "NEIN"),"<a data-type=dokumente data-id=", d.id, "></a>") as gesendet,
                "" as pdf,
                concat("1","-",d.id) as did,d.content as suchtext,d.internebezeichnung
              FROM
                dokumente d
              LEFT JOIN projekt p ON p.id=d.projekt
              LEFT JOIN adresse a2 ON a2.id=adresse_from
              WHERE 
                p.id = '.$id.'
            )

            UNION ALL

            (
              SELECT
                ds.id,"<img src=./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                CONCAT(DATE_FORMAT(ds.zeit, "%Y-%m-%d")," ", IF(DATE_FORMAT(ds.zeit, "%H:%i")="00:00", "", DATE_FORMAT(ds.zeit, "%H:%i"))) as datum,
                ds.betreff COLLATE utf8_general_ci as title,ds.ansprechpartner COLLATE utf8_general_ci,
                p.abkuerzung as abkuerzung,
                ds.bearbeiter COLLATE utf8_general_ci,
                CONCAT(UCASE(LEFT(ds.dokument, 1)), SUBSTRING(ds.dokument, 2),"<span style=\"display:none;\" class=\"editlink\">index.php?module=",ds.dokument,"&action=edit&id=",ds.parameter,"</span>") as art,
                CONCAT(IF(ds.versendet = 1, "JA", "NEIN"),"<a data-type=dokumente_send data-id=", ds.id, "></a>") as gesendet,
                concat("<a href=\"index.php?module=",ds.dokument,"&action=pdf&id=",ds.parameter,"\"><img src=./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/pdf.svg></a>") as pdf,
                concat("2","-",ds.id) as did,ds.text COLLATE utf8_general_ci as suchtext,
                ifnull(d1.internebezeichnung,
                ifnull(d2.internebezeichnung,
                ifnull(d3.internebezeichnung,
                ifnull(d4.internebezeichnung,
                ifnull(d5.internebezeichnung,
                ifnull(d6.internebezeichnung,
                 \'\')))))) as internebezeichnung
              FROM
                dokumente_send ds
                LEFT JOIN lieferschein d1 ON ds.parameter = d1.id AND ds.dokument = \'lieferschein\'
                LEFT JOIN auftrag d2 ON ds.parameter = d2.id AND ds.dokument = \'auftrag\'
                LEFT JOIN rechnung d3 ON ds.parameter = d3.id AND ds.dokument = \'rechnung\'
                LEFT JOIN gutschrift d4 ON ds.parameter = d4.id AND ds.dokument = \'gutschrift\'
                LEFT JOIN angebot d5 ON ds.parameter = d5.id AND ds.dokument = \'angebot\'
                LEFT JOIN bestellung d6 ON ds.parameter = d6.id AND ds.dokument = \'bestellung\'
                LEFT JOIN projekt p ON p.id=ds.projekt
              WHERE 
                p.id = '.$id.'
            )

            UNION ALL

            (
              SELECT
                k.id,"<img src=./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                CONCAT(DATE_FORMAT(k.von, "%Y-%m-%d")," ", IF(DATE_FORMAT(k.von, "%H:%i")="00:00", "", DATE_FORMAT(k.von, "%H:%i"))) as datum,
                k.bezeichnung COLLATE utf8_general_ci as title,\'\' as ansprechpartner,
                p.abkuerzung as abkuerzung,
                a2.name,
                "Kalender" as art,
                CONCAT("<a data-type=kalender data-id=", k.id, "></a>") as gesendet,
                "" as pdf,
                concat("6","-",k.id) as did,k.beschreibung COLLATE utf8_general_ci as suchtext,\'\' as internebezeichnung
              FROM
                kalender_event k 
                LEFT JOIN adresse a2 ON k.adresseintern = a2.id
                LEFT JOIN projekt p ON p.id=k.projekt
              WHERE 
                p.id = '.$id.'
            )

            ';
        if(1){//$this->app->erp->RechteVorhanden('ticket','offene')){
          $sql.= 'UNION ALL
            (
              SELECT
                ticket.id,"<img src=./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                DATE_FORMAT(ticket.zeit, "%Y-%m-%d %H:%i") as datum,
                concat(ticket.betreff COLLATE utf8_general_ci,if(ticket.notiz!=\'\',concat(\'<br /><i style="color:grey">\',ticket.notiz,\'</i>\'),\'\')) as title,ticket_nachricht.verfasser as ansprechpartner,
                projekt.abkuerzung as abkuerzung,
                ticket.bearbeiter,
                CONCAT("Ticket") as art,
                CONCAT("JA<a data-type=ticket_nachricht data-id=", ticket_nachricht.id, "></a>") as gesendet,
                "" as pdf,
                concat("3","-",ticket.id) as did,ticket_nachricht.text COLLATE utf8_general_ci as suchtext,\'\' as internebezeichnung
              FROM
                ticket
                LEFT JOIN ticket_nachricht ON ticket.schluessel = ticket_nachricht.ticket
                LEFT JOIN projekt ON projekt.id=ticket.projekt
              WHERE 
                projekt.id = '.$id.'
            )';

          //if(ifnull(a.internebezeichnung,\'\') = \'\', a.title, concat(a.title,\'<br /><i style="color:grey">\',a.internebezeichnung,\'</i>\')),
          $sql.= 'UNION ALL
            (
              SELECT
                ticket.id,"<img src=./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                DATE_FORMAT(ticket_nachricht.zeitausgang, "%Y-%m-%d %H:%i") as datum,
                concat(\'RE: \',ticket.betreff COLLATE utf8_general_ci,if(ticket.notiz!=\'\',concat(\'<br /><i style="color:grey">\',ticket.notiz,\'</i>\'),\'\')) as title,ticket_nachricht.verfasser as ansprechpartner,

                projekt.abkuerzung as abkuerzung,
                ticket.bearbeiter,
                CONCAT("Ticket") as art,
                CONCAT("JA<a data-type=ticket_nachricht data-id=", ticket_nachricht.id, "></a>") as gesendet,
                "" as pdf,
                concat("33","-",ticket.id) as did,ticket_nachricht.textausgang COLLATE utf8_general_ci as suchtext,\'\' as internebezeichnung
              FROM
                ticket
                LEFT JOIN ticket_nachricht ON ticket.schluessel = ticket_nachricht.ticket
                LEFT JOIN projekt ON projekt.id=ticket.projekt
              WHERE
                projekt = '.$id.'
            )';
        }

        //if($this->app->erp->RechteVorhanden('webmail','list')){
        $sql.= 'UNION ALL
            (
              SELECT
                e.id,"<img src=./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                DATE_FORMAT(e.empfang, "%Y-%m-%d %H:%i") as datum,
                e.subject COLLATE utf8_general_ci as title,\'\' as ansprechpartner,
                p.abkuerzung COLLATE utf8_general_ci as abkuerzung,
                "" as bearbeiter,
                CONCAT("E-Mail Archiv") as art,
                CONCAT("JA<a data-type=emailbackup_mails data-id=", e.id, "></a>") as gesendet,
                "" as pdf,
                concat("4","-",e.id) as did,ifnull(e.action,e.action_html) as suchtext,\'\' as internebezeichnung
              FROM
                emailbackup_mails e
                LEFT JOIN projekt p ON p.id=e.projekt
              WHERE 
                p.id = '.$id.'

            )';
        //}
        if($app->erp->RechteVorhanden('wiedervorlage','list')){
          $sql .='
            UNION ALL

            (
              SELECT
                w.id,"<img src=./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                CONCAT(DATE_FORMAT(datum_erinnerung, "%Y-%m-%d"), " ", IF(zeit_erinnerung IS NULL OR DATE_FORMAT(zeit_erinnerung, "%H:%i")="00:00", "", DATE_FORMAT(zeit_erinnerung, "%H:%i")) ) as datum,
                w.bezeichnung  COLLATE utf8_general_ci as title,\'\' as ansprechpartner,
                p.abkuerzung  COLLATE utf8_general_ci as abkuerzung,
                adr.name COLLATE utf8_general_ci as bearbeiter,
                CONCAT("Wiedervorlage") as art,
                CONCAT("<a data-type=wiedervorlage data-id=", w.id, "></a>") as gesendet,
                "" as pdf,
                concat("5","-",w.id) as did,w.beschreibung COLLATE utf8_general_ci as suchtext,\'\' as internebezeichnung
              FROM
                wiedervorlage w left join adresse adr on w.bearbeiter = adr.id
                LEFT JOIN projekt p ON p.id=w.projekt
              WHERE 
                p.id = '.$id.'
            )';
        }
        $sql .='
          ) a
        ';

        $moreinfo = true;
        $doppelteids = true;
        $moreinfoaction = 'brief';
        $menucol = 9;

        if($cmd=="adresse_brief" && $id > 0) $adresseId = $id;
        else
          $adresseId = $app->User->GetParameter('adresse_brief_adresseId');

        $count = '
          SELECT
            SUM(anzahl)
          FROM 
          (
            (
              SELECT
                COUNT(id) as anzahl
              FROM
                dokumente
              WHERE
                projekt = '.$id.'
            )
            UNION ALL
            (
              SELECT
                COUNT(id) as anzahl
              FROM
                dokumente_send
              WHERE
                projekt = '.$id.'
            )';
        if(1){
          $count .= '
            UNION ALL
            ( 
              SELECT
                COUNT(ticket.id) as anzahl
              FROM
                ticket
                LEFT JOIN ticket_nachricht ON ticket.schluessel = ticket_nachricht.ticket
              WHERE
                projekt = '.$id.'
            )';
        }

        $count .= '
            UNION ALL
            ( 
              SELECT
                COUNT(k.id) as anzahl
              FROM
                kalender_event k 
                LEFT JOIN adresse a2 ON k.adresseintern = a2.id
                LEFT JOIN projekt p ON p.id=k.projekt
              WHERE
                p.id = '.$id.'
            )';

        if($app->erp->RechteVorhanden('wiedervorlage','list')){
          $count .= '
              UNION ALL
              (
                SELECT
                  COUNT(id) as anzahl
                FROM
                  wiedervorlage
                WHERE
                  projekt = '.$id.'

              )';
        }
        $count .= '
          ) a
        ';

        break;

      case 'projekt_wiedervorlagen':
        // START EXTRA checkboxen
        //$columnfilter=true;
        $allowed['wiedervorlage'] = array('list');
        $id = (int)$app->Secure->GetGET("id");

        $moreinfo = true;
        $moreinfoaction = 'dashboardwiedervorlagen';

        $where = '1 ';

        $mitarbeiter = $app->User->GetParameter('table_wiedervorlage_mitarbeiter');
        $wheremitarbeiter ='';
        if($app->erp->RechteVorhanden('wiedervorlage','alle') ){
          if($mitarbeiter<>0){
            $wheremitarbeiter = " AND (w.adresse_mitarbeiter='" . $mitarbeiter . "' OR (w.adresse_mitarbeiter=0 AND w.bearbeiter='" . $mitarbeiter . "'))";
          }else{
            $wheremitarbeiter = ' ';
          }
        } else {
          $wheremitarbeiter = " AND (w.adresse_mitarbeiter='".(int)$app->User->GetAdresse()."' OR (w.adresse_mitarbeiter=0 AND w.bearbeiter='".(int)$app->User->GetAdresse()."'))";
        }
        $where .= $wheremitarbeiter;

        $where .= "AND w.projekt = '$id' ".$app->erp->ProjektRechte("w.projekt");

        $viewId = (int)$app->User->GetParameter('table_wiedervorlage_view');
        $where .= ($viewId > 0 ? " AND ws.view = '{$viewId}' " : " AND (ws.view = 0 OR w.stages = 0 OR (w.stages > 0 AND ws.id is NULL)) ");

        $heading = array('','Fällig am', 'Titel','Kunde','Projekt',  'Melden bei','Volumen','Chance','Stage', 'Abschlussdatum');
        $width = array('1%','10%', '30%','15%','5%','8%', '4%', '2%','8%','8%');
        $findcols = array('open',
          "if(
            w.datum_erinnerung = date(now()),

          concat('A',' ',w.datum_erinnerung,' ',w.zeit_erinnerung, ' ', DATE_FORMAT(w.datum_erinnerung, '%d.%m.%Y')),

          if(w.datum_erinnerung < date(now()) AND w.datum_erinnerung <> '0000-00-00',
            concat('B',' ', TIMESTAMPDIFF(SECOND, concat(w.datum_erinnerung,' ',w.zeit_erinnerung),'2999-01-01'), ' ', DATE_FORMAT(w.datum_erinnerung, '%d.%m.%Y'))

            ,concat('C',' ',w.datum_erinnerung,' ',w.zeit_erinnerung, ' ', DATE_FORMAT(w.datum_erinnerung, '%d.%m.%Y'))
          )
        )",
          'w.bezeichnung','a3.name','if(p2.abkuerzung !="",p2.abkuerzung,IFNULL(p.abkuerzung,""))',  'IF(a4.name != "",a4.name,a2.name)','w.betrag','w.chance',"if(ws.kurzbezeichnung!='',ws.kurzbezeichnung,ws.name)",'w.datum_abschluss');
        $searchsql = array('DATE_FORMAT(w.datum_erinnerung, "%d.%m.%Y")', 'w.bezeichnung','a3.name','if(p2.abkuerzung !="",p2.abkuerzung,IFNULL(p.abkuerzung,""))',  'IF(a4.name != "",a4.name,a2.name)',$app->erp->FormatPreis("w.betrag"),'w.chance',"if(ws.kurzbezeichnung!='',ws.kurzbezeichnung,ws.name)",'DATE_FORMAT(w.datum_abschluss, "%d.%m.%Y")');

        $trcol = 10;
        $defaultorder = 2;
        $defaultorderdesc = 'asc';

        $additionalFieldsSql = '';
        $additionalAddressFields = $app->erp->getZusatzfelderAdresse();
        $result = [];
        for($i = 1; $i <= 10; $i++){
          $property = (string)$app->erp->GetKonfiguration('wiedervorlagen_zusatzfeld'.$i.'_schluessel');
          if (!empty($property)){
            $result[$property] = $additionalAddressFields[$property];
          }
        }
        $additionalFields = $result;

        foreach ($additionalFields as $additionalProperty => $additionalLabel) {
          $additionalFieldsSql .= 'a3.' . $additionalProperty . ' AS additional_' . $additionalProperty . ', ';
          $searchsql[] = 'a3.' . $additionalProperty;
          $findcols[] = 'a3.' . $additionalProperty;
          $heading[] = str_replace(' ', '&nbsp;', $additionalLabel);
          $width[] = '5%';
          $trcol++;
        }

        $heading[] = 'tr';
        $width[] = '0%';

        $heading[] = '';
        $width[] = '8%';

        $findcols[] = 'w.id';
        $searchsql[] = 'w.id';

        $findcols[] = 'w.id';

        $menucol = count($findcols)-1;

        $menu = '<input type="hidden" value="%value%">';

        $alignright = array(7,8);
        $aligncenter = array(9);

        $sql = 'SELECT  SQL_CALC_FOUND_ROWS w.id,"<img src=./themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/details_open.png class=details>" as open,
                '.$app->YUI->Stroke("w.abgeschlossen",'DATE_FORMAT(datum_erinnerung, "%d.%m.%Y")').' as datum_ang, 
                concat('.$app->YUI->Redify('w.prio', $app->YUI->Stroke('w.abgeschlossen','w.bezeichnung')).',if(w.module=\'angebot\' AND w.parameter > 0,CONCAT(\' <a href="index.php?module=angebot&action=edit&id=\',w.parameter,\'"" target="_blank">(zum Angebot)</a>\'),if(w.module=\'ticket\' AND w.parameter > 0,CONCAT(\' <a href="index.php?module=ticket&action=assistent&id=\',w.parameter,\'"" target="_blank">(zum Ticket)</a>\'),\'\'))), 
                '.$app->YUI->Stroke('w.abgeschlossen','a3.name').', 
                '.$app->YUI->Stroke('w.abgeschlossen','if(p2.abkuerzung !="",p2.abkuerzung,IFNULL(p.abkuerzung,""))').' AS abkuerzung, 
                
                '.$app->YUI->Stroke('w.abgeschlossen','IF(a4.name != "",a4.name,a2.name)').', 
                '.$app->YUI->Stroke('w.abgeschlossen',$app->erp->FormatPreis('w.betrag')).', 
                '.$app->YUI->Stroke('w.abgeschlossen',"concat(w.chance,' %')").' AS chance,
                '.$app->YUI->Stroke('w.abgeschlossen',"if(ws.kurzbezeichnung!='',ws.kurzbezeichnung,ws.name)").',
                '.$app->YUI->Stroke("w.abgeschlossen",'DATE_FORMAT(datum_abschluss, "%d.%m.%Y")').' as datum_abschluss,
                '.$additionalFieldsSql.'
                if(w.datum_erinnerung = date(now()),\'#D2EC9D\',if(w.datum_erinnerung < date(now()) AND w.datum_erinnerung <> \'0000-00-00\',\'#F1B19F\',\'\')) as tr,
                w.id
                
                from wiedervorlage w
                
                LEFT JOIN adresse a2 on w.bearbeiter = a2.id
                LEFT JOIN adresse a3 on w.adresse = a3.id
                LEFT JOIN adresse a4 ON w.adresse_mitarbeiter = a4.id
                LEFT JOIN projekt p on a3.projekt = p.id
                LEFT JOIN wiedervorlage_stages ws on ws.id = w.stages
                LEFT JOIN projekt p2 on w.projekt = p2.id
                ';

        break;

    }
    
    $erg = [];

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }
    return $erg; 
  }

  /** @var Application $app */
  function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ProjektCreate");
    $this->app->ActionHandler("edit","ProjektEdit");
    $this->app->ActionHandler("list","ProjektList");
    $this->app->ActionHandler("pdf","ProjektPDF");
    $this->app->ActionHandler("delete","ProjektDelete");
    $this->app->ActionHandler("dateien","ProjektDateien");
    $this->app->ActionHandler("uebersicht","ProjektUebersicht");
    $this->app->ActionHandler("zeit","ProjektZeit");
    $this->app->ActionHandler("material","ProjektMaterial");
    $this->app->ActionHandler("arbeitsnachweise","ProjektArbeitsnachweise");
    $this->app->ActionHandler("arbeitsnachweispdf","ProjektArbeitsnachweisPDF");
    $this->app->ActionHandler("arbeitspaket","ProjektArbeitspaket");
    $this->app->ActionHandler("arbeitspaketeditpopup","ProjektArbeitspaketEditPopup");
    $this->app->ActionHandler("arbeitspaketdelete","ProjektArbeitspaketDelete");
    $this->app->ActionHandler("arbeitspaketdisable","ProjektArbeitspaketDisable");
    $this->app->ActionHandler("arbeitspaketcopy","ProjektArbeitspaketCopy");
    $this->app->ActionHandler("nextnumber","ProjektNextnumber");
    $this->app->ActionHandler("delnumber","ProjektDelnumber");
    $this->app->ActionHandler("getnextnumber","ProjektGetNextNumber");
    $this->app->ActionHandler("neueaufgabe","ProjektNeueAufgabe");
    $this->app->ActionHandler("artikeledit","ProjektArtikelEdit");
    
    $this->app->ActionHandler("minidetailarbeitspaket","ProjektMiniDetailArbeitspaket");
    
    $this->app->ActionHandler("plan","ProjektPlan");

    $this->app->ActionHandler("kostenstellen","ProjektKostenstellen");
    $this->app->ActionHandler("schaltung","ProjektSchaltung");
    
    $this->app->ActionHandler("mitgliederdelete","ProjektMitgliederDelete");
    $this->app->ActionHandler("copy","ProjektCopy");
    $this->app->ActionHandler("minidetailprojektuebersicht", "ProjektuebersichtMinidetail");

    $this->app->ActionHandler("projektlogbuch","ProjektLogbuch");
    $this->app->ActionHandler("minidetailbrief","ProjektMiniDetailBrief");
    $this->app->ActionHandler("briefpreview","ProjektBriefPreview");


    $this->app->ActionHandlerListen($app);
  }
  
  function getStueckliste(&$ret, $artikel, $menge, $lvl = 0)
  {
    if($lvl > 6) {
      return;
    }
    $parent = -1;
    if(!empty($ret))$parent = count($ret)-1;
    $stueckliste = $this->app->DB->SelectArr("SELECT id, menge, artikel,stuecklistevonartikel FROM  stueckliste WHERE stuecklistevonartikel = '$artikel'");
    if($stueckliste)
    {
      foreach($stueckliste as $art)
      {
        $art['menge'] = $art['menge'] * $menge;
        $art['parent'] = -1;
        if(is_array($ret)) {
          $art['parent'] = $parent;
        }
        
        $ret[] = $art;
        $res = $this->getStueckliste($ret, $art['artikel'], $art['menge'] , $lvl+1);
        if($res)
        {
          foreach($res as $a)
          {
            $a['parent'] = $art['parent']+1;
            $ret[] = $a;
          }
          unset($res);
        }
      }
    }
  }

  function UpdateProjektartikelCache($id, $nurwe = false)
  {
    $artikel = $this->app->DB->Select("SELECT artikel FROM projekt_artikel WHERE id = '$id' LIMIT 1");
    if(!$artikel)
    {
      return false;
    }
    if(!$nurwe)
    {
      $belege = array('PR'=>'produktion','AN'=>'angebot','AB'=>'auftrag','LS'=>'lieferschein','GS'=>'gutschrift','RE'=>'rechnung','PF'=>'preisanfrage','PRO','proformarechnung'); //WE Wareneingang
      foreach($belege as $cache => $beleg)
      {
        $count = $this->app->DB->Select("SELECT sum(bp.menge) as co FROM $beleg"."_position bp INNER JOIN $beleg b ON bp.$beleg = b.id WHERE bp.teilprojekt > 0 AND bp.artikel = '$artikel' AND b.status!='storniert' AND b.status!='abgelehnt' AND b.status != 'angelegt' AND b.status != '' ".($cache != 'PRO'?" AND b.belegnr != '' ":''));
        $this->app->DB->Update("UPDATE projekt_artikel SET cache_".$cache." = '$count' WHERE id = '$id' LIMIT 1");
      }
      $beleg = 'lieferschein';
      $cache = 'WA';
      $count = $this->app->DB->Select("SELECT sum(bp.geliefert) as co FROM $beleg"."_position bp INNER JOIN $beleg b ON bp.$beleg = b.id WHERE bp.teilprojekt > 0 AND bp.artikel = '$artikel' AND b.status!='storniert' AND b.status!='abgelehnt' AND b.status != 'angelegt' AND b.belegnr != ''");
      $this->app->DB->Update("UPDATE projekt_artikel SET cache_".$cache." = '$count',lastcheck = now() WHERE id = '$id' LIMIT 1");
    }
    
    $count = $this->app->DB->SelectArr("SELECT sum(geliefert) as cogeliefert, sum(menge) as comenge FROM bestellung_position WHERE teilprojekt > 0 AND artikel = '$artikel' ");
    if($count)
    {
      $count = reset($count);
      $menge = $count['comenge'];
      $geliefert = $count['cogeliefert'];
      $this->app->DB->Update("UPDATE projekt_artikel SET cache_WE = '$geliefert', cache_BE = '$menge' WHERE id = '$id' LIMIT 1");
    }
  }


  function ProjektNeueAufgabe()
  {
    $id = (int)$this->app->Secure->GetGET('id');

    $aufgabe = $this->app->erp->CreateAufgabe(0,"");
    $this->app->DB->Update("UPDATE aufgabe SET projekt='$id' WHERE id='$aufgabe'");
    $this->app->Location->execute('index.php?module=aufgaben&action=edit&id='.$aufgabe);
  }

  function ProjektArtikelEdit()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $artikelid = $this->app->DB->Select("SELECT artikel FROM projekt_artikel WHERE id='$id' LIMIT 1");
    $this->app->Location->execute('index.php?module=artikel&action=edit&id='.$artikelid);
  }
  
  function ProjektCopy()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($id && $this->app->erp->UserProjektRecht($id))
    {
      $newid = $this->app->erp->CopyProjekt($id);
      if($newid)
      {
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Das Projekt wurde erfolgreich kopiert.</div>");
        $this->app->Location->execute('index.php?module=projekt&action=list&msg='.$msg);
      }
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Fehler beim Kopieren des Projekts.</div>"); 
    }else{
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Fehlende Projektrechte: Das Projekt wurde nicht kopiert.</div>"); 
    }
    $this->app->Location->execute('index.php?module=projekt&action=list&msg='.$msg);
  }
  
  function ProjektMitgliederDelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $projekt = (int)$this->app->DB->Select("SELECT projekt FROM adresse_rolle WHERE id = '$id' LIMIT 1");
    $adresse = (int)$this->app->DB->Select("SELECT adresse FROM adresse_rolle WHERE id = '$id' LIMIT 1");
    if($projekt && $adresse && $this->app->erp->UserProjektRecht($projekt))
    {
      $this->app->DB->Update("UPDATE adresse_rolle SET bis = DATE_SUB(curdate(),INTERVAL 1 DAY) WHERE objekt like 'Projekt' AND id = '$id' LIMIT 1");
    }
    $this->app->Location->execute('index.php?module=projekt&action=mitglieder&id='.$projekt);
  }

  function ProjektMitglieder()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $this->app->Tpl->Set('ID',$id);
    
    if($this->app->Secure->GetGET('cmd') === 'adresse')
    {
      $adresse = $this->app->DB->Select("SELECT adresse FROM adresse_rolle WHERE id = '$id' LIMIT 1");
      $this->app->Location->execute('index.php?module=adresse&action=edit&id='.$adresse);
    }
    
    if($this->app->Secure->GetPOST('hinzufuegen'))
    {
      $adresse = explode(' ',$this->app->Secure->GetPOST('adresse'));
      $adresse = (int)$adresse[0];
      $rolle = $this->app->Secure->GetPOST('rolle');
      $rollen = array('Kunde','Mitarbeiter','Mitglied','Lieferant');//'Projektleiter','Lieferant','Externer Mitarbeiter');
      if($adresse && in_array($rolle, $rollen))
      {
        $check = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE adresse = '$adresse' AND projekt = '$id' AND objekt like 'Projekt' AND subjekt LIKE '$rolle' LIMIT 1");
        if($check)
        {
          $this->app->DB->Update("UPDATE adresse_rolle SET von = curdate() WHERE id = '$check' AND von > curdate() LIMIT 1");
          $this->app->DB->Update("UPDATE adresse_rolle SET bis = '0000-00-00' WHERE id = '$check' LIMIT 1");
        }else{
          $this->app->DB->Insert("INSERT INTO adresse_rolle (adresse, objekt, subjekt, projekt, praedikat, von, bis, parameter) VALUES ('$adresse','Projekt','$rolle','$id','von',now(),'0000-00-00','$id')");
        }
        $this->app->Tpl->Add('MESSAGE','<div class="info">Rolle '.$rolle.' angelegt</div>');
      }
    }
    
    $this->ProjektMenu();
    $projektabgeschlossen = false;
    if($this->app->DB->Select("SELECT id FROM projekt WHERE id = '$id' AND status = 'abgeschlossen' LIMIT 1"))$projektabgeschlossen = true;
    if($projektabgeschlossen)$this->app->Tpl->Set('BUTTONDISABLED',' disabled ');
    $this->app->YUI->AutoComplete('adresse','adresse');
    $this->app->YUI->TableSearch('TAB1',"projekt_mitglieder");

    $this->app->Tpl->Parse('PAGE',"projekt_mitglieder.tpl");
  }


  /**
   * @param int $projectId
   *                           
   * @return array
   */
  protected function BuildProjectScheduleData($projectId)
  {
    $projectId = (int)$projectId;

    $milestonesData = $this->GetProjectScheduleMilestonesData($projectId);
    $packagesData = $this->GetProjectSchedulePackagesData($projectId);
    $data = array_merge([$milestonesData], $packagesData);

    return $data;
  }

  /**
   * Arbeitspakete für Gantt-Diagramm laden
   *
   * @param int $projectId
   *
   * @return array
   */
  protected function GetProjectSchedulePackagesData($projectId)
  {
    $projectId = (int)$projectId;
    $colorClasses = [
      'offen' => 'ganttGreen',
      'aktiv' => 'ganttGreen',
      'abgeschlossen' => 'ganttBlue',
      'abgerechnet' => 'ganttBlue',
    ];
    $data = [];
    $packages = $this->app->DB->SelectArr(
      sprintf(
        "SELECT 
         ap.id, ap.aufgabe AS title, ap.beschreibung AS description, ap.status, 
       ap.farbe AS color, 
         ap.vorgaenger AS predecessor, ap.sort AS sorting, 
       ap.startdatum AS date_from, ap.abgabedatum AS date_to, 
         ad.name AS responsible_name, 
       IFNULL(
           (SELECT COUNT(`id`) FROM `wiedervorlage` WHERE `subproject_id` = ap.id AND `abgeschlossen` = 0)
           ,0
           )
           AS `resubmissions_open`,
       IFNULL(
           (SELECT COUNT(`id`) FROM `wiedervorlage` WHERE `subproject_id` = ap.id AND `abgeschlossen` = 1)
           ,0
           )
           AS `resubmissions_closed` ,
       IFNULL(
           (SELECT COUNT(`id`) FROM `aufgabe` WHERE `teilprojekt` = ap.id AND `status` = 'offen')
           ,0
           )
           AS `tasks_open`,
       IFNULL(
           (SELECT COUNT(`id`) FROM `aufgabe` WHERE `teilprojekt` = ap.id AND `status` = 'abgeschlossen')
           ,0
           )
           AS `tasks_closed`
       FROM `arbeitspaket` AS `ap`
       LEFT JOIN `adresse` AS `ad` ON ap.adresse = ad.id AND ad.geloescht = 0 
             AND ad.mitarbeiternummer != '' AND ad.mitarbeiternummer != '0'
       WHERE ap.geloescht != 1 AND ap.projektplanausblenden != 1 AND ap.projekt = %d 
       ORDER BY ap.vorgaenger, ap.sort, ap.aufgabe",
      $projectId
      )
    );
    $packages = $this->SortProjectSchedulePackagesData($packages);

    foreach ($packages as $package) {
      $label = '';
      $customClass = array_key_exists($package['status'], $colorClasses) ? $colorClasses[$package['status']] : 'ganttGray';
      $dateFrom = !empty($package['date_from']) && $package['date_from'] !== '0000-00-00' ? strtotime($package['date_from']) : null;
      $dateTo = !empty($package['date_to']) && $package['date_to'] !== '0000-00-00' ? strtotime($package['date_to']) : null;
      $package['responsible_name'] .= sprintf(
        ' %d/%d',
        (int)$package['resubmissions_closed'] + (int)$package['tasks_closed'],
        (int)$package['resubmissions_open'] + (int)$package['tasks_open']
      );
      if ($dateFrom === null && $dateTo === null) {
        $customClass = 'ganttOrange';
        $label = 'Kein Start und Abgabedatum gewählt';
        $dateFrom = time();
        $dateTo = time() + (60 * 60 * 24 * 7); // Sieben Tage
      }
      if ($dateFrom === null && $dateTo > 0) {
        $customClass = 'ganttOrange';
        $label = 'Startdatum fehlt';
        $dateFrom = $dateTo - (60 * 60 * 24 * 7); // Sieben Tage
      }
      if ($dateFrom > 0 && $dateTo === null) {
        $customClass = 'ganttOrange';
        $label = 'Abgabedatum fehlt';
        $dateTo = $dateFrom + (60 * 60 * 24 * 7); // Sieben Tage
      }
      if ($package['status'] === 'aktiv' || $package['status'] === 'offen') {
        if(time() > $dateTo){
          $customClass = 'ganttRed'; // Überfällig
        }
      }

      $customColor = strtoupper($package['color']) !== '#FFFFFF' ? $package['color'] : null;
      $isRootPackage = (int)$package['predecessor'] === 0;
      $data[] = [
        'name' => $isRootPackage ? $package['title'] : '',
        'desc' => !$isRootPackage ? $package['title'] : '',
        'values' => [[
          'from' => '/Date(' . $dateFrom . '000)/',
          'to' => '/Date(' . $dateTo . '000)/',
          'label' => !empty($label) ? $label : $package['responsible_name'],
          'customClass' => empty($customColor) ? $customClass : null,
          'dataObj' => [
            'type' => 'package',
            'projectId' => $projectId,
            'packageId' => (int)$package['id'],
            'color' => $customColor,
            'level' => count($package['parents']),
          ],
        ]],
      ];
    }

    return $data;
  }

  /**
   * Meilensteine für Gantt-Diagramm laden
   *
   * @param int $projectId
   *
   * @return array
   */
  protected function GetProjectScheduleMilestonesData($projectId)
  {
    $projectId = (int)$projectId;

    $milestones = $this->app->DB->SelectArr(
      "SELECT 
         e.id, e.bezeichnung AS title, e.beschreibung AS description, e.color,
         e.von AS datetime_from, e.bis AS datetime_to
       FROM kalender_event AS e 
       WHERE e.typ = 'meilenstein' AND e.projekt = '{$projectId}' AND e.von != '0000-00-00 00:00:00'
       ORDER BY e.von ASC, e.bis ASC"
    );

    $values = [];
    foreach ($milestones as $milestone) {
      $datetimeFrom = strtotime($milestone['datetime_from']);
      $datetimeTo = !empty($milestone['datetime_to']) && $milestone['datetime_to'] !== '0000-00-00 00:00:00' ? strtotime($milestone['datetime_to']) : $datetimeFrom + (60 * 60 * 8);

      $values[] = [
        'from' => '/Date(' . $datetimeFrom . '000)/',
        'to' => '/Date(' . $datetimeTo . '000)/',
        'label' => $milestone['title'],
        'dataObj' => [
          'type' => 'milestone',
          'projectId' => $projectId,
          'milestoneId' => (int)$milestone['id'],
          'color' => !empty($milestone['color']) && strtoupper($milestone['color']) !== '#FFFFFF' ? $milestone['color'] : '#0B8092',
        ],
      ];
    }

    return [
      'name' => 'Meilensteine',
      'values' => $values,
    ];
  }

  /**
   * Arbeitspakete für Gantt-Diagramm sortieren; anhand der Projekt-Hirarchie
   *
   * @param array $packages
   *
   * @return array
   */
  protected function SortProjectSchedulePackagesData($packages)
  {
    foreach ($packages as &$package) {
      $package['parents'] = $this->GetProjectSchedulePackageParents($package['id'], $packages, []);
      $package['parents'] = array_reverse($package['parents']);
      $parentsWithPadding = array_map(function ($parentId) {
        return str_pad($parentId, 3, '0', STR_PAD_LEFT);
      }, $package['parents']);
      $package['path'] = implode('-', $parentsWithPadding);
    }
    unset($package);

    // Rebuild array: Sort by path
    $paths = array_column($packages, 'path');
    $result = array_combine($paths, $packages);
    ksort($result);

    // Drop index; only needed for sorting
    return array_values($result);
  }

  /**
   * Alle Eltern eines Arbeitspaketes finden; bzw. deren Sortierungswert
   *
   * @param int   $childId
   * @param array $packages
   * @param array $foundParents
   *
   * @return array
   */
  protected function GetProjectSchedulePackageParents($childId, array $packages, array $foundParents = [])
  {
    $childId = (int)$childId;

    foreach ($packages as $package) {
      if ((int)$package['id'] === $childId) {
        $parentId = (int)$package['predecessor'];
        $foundParents[] = (int)$package['sorting'];
        $foundParents = $this->GetProjectSchedulePackageParents($parentId, $packages, $foundParents);
      }
    }

    return $foundParents;
  }

  /**
   * Projektplan für alle Projekte als CSV-Download senden
   *
   * @return void
   */
  protected function SendOverviewProjectScheduleCsvDownload()
  {
    $projects = $this->app->DB->SelectArr("SELECT p.id FROM projekt AS p WHERE p.status != 'abgeschlossen'");
    $overview = [];
    foreach ($projects as $project) {
      $packages = $this->FetchProjectScheduleForCsvDownload($project['id']);
      $overview = array_merge($overview, $packages);
    }

    $this->GenerateCsvDownloadFromPackages($overview);
  }

  /**
   * Projektplan für einzelnes Projekt als CSV-Download senden
   *
   * @param int $projectId
   *
   * @return void
   */
  protected function SendSingleProjectScheduleCsvDownload($projectId)
  {
    $projectId = (int)$projectId;
    $packages = $this->FetchProjectScheduleForCsvDownload($projectId);
    $this->GenerateCsvDownloadFromPackages($packages);
  }

  /**
   * @param int $projectId
   *
   * @return array
   */
  protected function FetchProjectScheduleForCsvDownload($projectId)
  {
    $projectId = (int)$projectId;

    $packages = $this->app->DB->SelectArr(
      "SELECT 
         ap.id, ap.aufgabe AS title, ap.beschreibung AS description, ap.status, ap.farbe AS color, 
         ap.vorgaenger AS predecessor, ap.sort AS sorting, ap.startdatum AS date_from, ap.abgabedatum AS date_to,
         ad.name AS person_name, ad.mitarbeiternummer AS person_number,
         p.id AS project_id, p.name AS project_name, p.abkuerzung AS project_abbr
       FROM arbeitspaket AS ap 
       INNER JOIN projekt AS p ON ap.projekt = p.id     
       LEFT JOIN adresse AS ad ON ap.adresse = ad.id
       WHERE ap.geloescht != 1 AND ap.projektplanausblenden != 1 AND ap.projekt = '{$projectId}' 
       ORDER BY ap.vorgaenger, ap.sort, ap.aufgabe"
    );
    $packages = $this->SortProjectSchedulePackagesData($packages);

    return $packages;
  }

  /**
   * @param array $packages
   *
   * @return void
   */
  protected function GenerateCsvDownloadFromPackages($packages)
  {
    $colNames = [
      'Projekt-Kürzel',
      'Projekt-Name',
      'Arbeitspaket',
      'Beschreibung',
      'Status',
      'Startdatum',
      'Abgabedatum',
      'Mitarbeiter-Nr',
      'Mitarbeiter-Name',
    ];

    $packagesCollection = new FormatterCollection($packages, function ($package) {
        $dateFrom = !empty($package['date_from']) && $package['date_from'] !== '0000-00-00' ? strtotime($package['date_from']) : null;
        $dateTo = !empty($package['date_to']) && $package['date_to'] !== '0000-00-00' ? strtotime($package['date_to']) : null;

        return [
            $package['project_abbr'],
            $package['project_name'],
            $package['title'],
            $package['description'],
            $package['status'],
            $dateFrom !== null ? date('d.m.Y', $dateFrom) : '',
            $dateTo !== null ? date('d.m.Y', $dateTo) : '',
            (string)$package['person_number'],
            (string)$package['person_name'],
        ];
    });

    $config = new CsvConfig(';', '"');
    $exporter = new CsvExporter($config);
    $data = new DataCollection([$colNames], $packagesCollection);
    $handle = $exporter->exportToResource('php://memory', $data);

    rewind($handle);
    $stat = fstat($handle);
    header('Cache-Control: must-revalidate');
    header('Pragma: must-revalidate');
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="projektplan.csv"');
    header('Content-Length: ' . $stat['size']);
    fpassthru($handle);
    fclose($handle);
    $this->app->ExitXentral();
  }


  function ProjektDelnumber()
  {
    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetGET("nummer");
    $cmd = $this->app->Secure->GetGET("cmd");
    $projekt = $this->app->DB->SelectArr("SELECT * from projekt where id = '$id' LIMIT 1");
    if($projekt)
    {
      $projekt = reset($projekt);
      if(isset($projekt['next_'.$cmd]))$this->app->DB->Update("UPDATE projekt set ".'next_'.$cmd."='' where id = '$id'");

    }
    $this->app->Location->execute('index.php?module=projekt&action=edit&id='.$id.'#tabs-4');
  }


  function ProjektNextnumber()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $nummer = $this->app->Secure->GetGET('nummer');
    $cmd = $this->app->Secure->GetGET('cmd');
    $projekt = $this->app->DB->SelectRow(sprintf('SELECT * from projekt where id = %d LIMIT 1',$id));
    if($cmd != '' && !empty($projekt))
    {
      if(isset($projekt['next_'.$cmd])){
        $this->app->DB->Update(
          sprintf(
          "UPDATE projekt set `%s` = '%s' where id = %d"
            ,'next_'.$cmd, $nummer, $id
          )
        );
      }
    }
    $this->app->Location->execute('index.php?module=projekt&action=edit&id='.$id.'#tabs-4');
  }

  function ProjektGetNextNumber()
  {
    $id = $this->app->Secure->GetGET('id');
    $projekt = $this->app->DB->SelectArr(sprintf('SELECT * from projekt where id = %d LIMIT 1', $id));
    if($projekt)
    {
      echo json_encode($projekt[0]);
    }
    $this->app->ExitXentral();
  }

  function ProjektArbeitsnachweisPDF()
  {
    $date = $this->app->Secure->GetGET('date');

    $Brief = new ArbeitsnachweisPDF($this->app);
    $Brief->GetArbeitsnachweis($date);
    $Brief->displayDocument();
    $this->app->ExitXentral();
  }

  function ProjektPlan()
  {
    $id = $this->app->Secure->GetGET('id');


    $this->ProjektMenu();

    $startarbeitspaket = 0;

    $baumtiefe = $this->ProjektPlanRekrusiv($id,$startarbeitspaket);

    $arbeitspakete = $this->ProjektPlanArbeitspaketeinReihenfolge($id,$baumtiefe);

    asort($arbeitspakete);

    $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM projekt WHERE id='".$id."'");
    $this->app->Tpl->Add('TAB1',"<h1>&Uuml;bersicht</h1><br><style>.border {border: 1px solid black;}</style>");

    $this->app->Tpl->Add('TAB1',"<table width=700><tr><td>".nl2br($beschreibung)."</td></tr></table><br>");
    $this->app->Tpl->Add('TAB1',"<h1>Umfang</h1><br>");
    $this->app->Tpl->Add('TAB1',"<table border=0 class=border cellspacing=0 cellpadding=3 width=\"65%\">");

    $pos = 1;

    foreach($arbeitspakete as $key=>$value)
    {
      $arbeitspaketeArr = $this->app->DB->SelectRow(sprintf('SELECT * FROM arbeitspaket WHERE id= %d ', (int)$key));
      $aufgabe = $arbeitspaketeArr['aufgabe'];
      $vorgaenger = $arbeitspaketeArr['vorgaenger'];
      $art = $arbeitspaketeArr['art'];

      if($art==='meilenstein'){
        $this->app->Tpl->Add('TAB1', "<tr><td width=20 class=border>$pos</td><td width=300 class=border><b>" . $aufgabe . "</b></td>");
      }
      else{
        $this->app->Tpl->Add('TAB1', "<tr><td width=20 class=border>$pos</td><td width=300 class=border>" . $aufgabe . "</td>");
      }

      $painted=false;
      for($j=0;$j<$baumtiefe;$j++)
      {
        if($vorgaenger==$this->voraengerbaum[$j] && $painted==false){
          $this->app->Tpl->Add('TAB1',"<td width=20 class=border><b>X</b></td>");
          $painted=true;
        }
        else{
          $this->app->Tpl->Add('TAB1', "<td width=20 class=border>&nbsp;</td>");
        }
      }
      $this->app->Tpl->Add('TAB1',"</tr>");
      $pos++;
    }
    $this->app->Tpl->Add('TAB1',"</table><br><br>");


    $this->app->Tpl->Add('TAB1',"<h1>Details</h1><br>");

    $pos = 1;
    foreach($arbeitspakete as $key=>$value)
    {
      $aufgabe = $this->app->DB->Select("SELECT aufgabe FROM arbeitspaket WHERE id='".$key."'");	
      $vorgaenger = $this->app->DB->Select("SELECT vorgaenger FROM arbeitspaket WHERE id='".$key."'");	
      $art = $this->app->DB->Select("SELECT UPPER(art) FROM arbeitspaket WHERE id='".$key."'");	
      $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM arbeitspaket WHERE id='".$key."'");	
      $vorgaenger_aufgabe = $this->app->DB->Select("SELECT aufgabe FROM arbeitspaket WHERE id='".$vorgaenger."'");	
      $abgabe_bis = $this->app->DB->Select("SELECT DATE_FORMAT(abgabedatum,'%d.%m.%Y') FROM arbeitspaket WHERE id='".$key."'");	
      $startdatum = $this->app->DB->Select("SELECT DATE_FORMAT(startdatum,'%d.%m.%Y') FROM arbeitspaket WHERE id='".$key."'");	

      if($abgabe_bis!=="00.00.0000" && $abgabe_bis!="")
        $abgabe_bis = "<br>Abgabe bis $abgabe_bis";



      $zeit_geplant = $this->app->DB->Select("SELECT zeit_geplant FROM arbeitspaket WHERE id='".$key."'");	
      $kosten_geplant = $this->app->DB->Select("SELECT kosten_geplant FROM arbeitspaket WHERE id='".$key."'");	

      $gesamt_zeit = $gesamt_zeit + $zeit_geplant;
      $gesamt_kosten = $gesamt_kosten + $kosten_geplant;

      if($zeit_geplant==="0.00") $zeit_geplant="-"; else $zeit_geplant=$zeit_geplant." h";
      if($kosten_geplant==="0.00") $kosten_geplant="-"; else $kosten_geplant = $kosten_geplant." ".$this->projektwaehrung;


      if($art==='MEILENSTEIN')
      {
        $kostentabelle = 'Meilenstein (keine Kosten)';
      } else {

        $kostentabelle = "<table border=0 width=200>
          <tr><td>Stunden:</td><td>$zeit_geplant</td></tr>
          <tr><td>oder Fixkosten:</td><td>$kosten_geplant</td></tr>
          </table>";

      }
      $this->app->Tpl->Add('TAB1',"<br><h2>".$pos.". $aufgabe ($art)</h2><br>
          <table border=0 cellpadding=5 width=700 class=border>
          <tr><td><b>Beschreibung:</b></td><td><b>Aufwand:</b></td></tr>
          <tr valign=top><td width=420>".nl2br($beschreibung)."</td><td>
          $kostentabelle

          $abgabe_bis
          </td></tr></table>");
      $pos++;
    }	


    //Material
    $this->app->Tpl->Add('TAB1',"<h1>Material</h1>");

    $material = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket 
        WHERE projekt='$id' AND art='material'");
    if(!empty($material)){
      foreach($material as $materialRow) {
        $aufgabe = $materialRow['aufgabe'];
        $beschreibung = $materialRow['beschreibung'];
        $kosten_geplant = $materialRow['kosten_geplant'];
        $this->app->Tpl->Add('TAB1', "
          <table border=0 cellpadding=5 width=700 class=border>
          <tr><td><b>Material:</b>$aufgabe</td><td><b>Kosten:</b></td></tr>
          <tr valign=top><td width=420>" . nl2br($beschreibung) . "</td><td>
          <table border=0 width=200>
          <tr><td>Komplett:</td><td>$kosten_geplant " . $this->projektwaehrung . "</td></tr>
          </table>
          </td></tr></table><br>");

        $gesamt_kosten = $gesamt_kosten + $kosten_geplant;
      }
    }

    $stundensatz = "65";	

    $gesamt = $gesamt_kosten + $gesamt_zeit*$stundensatz;

    $this->app->Tpl->Add('TAB1',"<h1>Kosten</h1>

        <table border=0 cellpadding=5 width=700 class=border>
        <tr><td>Gesamt:</td></tr>
        <tr valign=top><td>
        <table border=0 width=100%>
        <tr><td width=430>Stunden:</td><td>$gesamt_zeit h (&#225; $stundensatz ".$this->projektwaehrung.")</td></tr>
        <tr><td>externe Kosten:</td><td>$gesamt_kosten ".$this->projektwaehrung."</td></tr>
        <tr><td>Gesamt:</td><td><b>$gesamt ".$this->projektwaehrung."</b>&nbsp;(zzgl. gesetzl. MwSt.)</td></tr>
        </table>
        </td></tr></table>
        ");

    $this->app->Tpl->Set('TABTEXT',"Projektplan");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");

  }



  function ProjektPlanArbeitspaketeinReihenfolge($id,$baumtiefe)
  {
    $arbeitspakete = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket WHERE projekt='$id' AND art!='material'");
    if(empty($arbeitspakete)) {
      return [];
    }
    $reihenfolge = array();

    foreach($arbeitspakete as $arbeitspaket)
    {
      for($j=0;$j<$baumtiefe;$j++)
      {
        if($arbeitspaket['vorgaenger']==$this->voraengerbaum[$j]){
          $reihenfolge[$arbeitspaket['id']]=$j;
          $j=$baumtiefe;
        }
      }
    }
    return $reihenfolge;
  }


  function ProjektPlanRekrusiv($projekt,$id)
  {
    $arbeitspakete = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket 
        WHERE projekt='$projekt' AND vorgaenger='".$id."' AND art!='material'");

    if(empty($arbeitspakete)) {
      return 0;
    }
    $anzahl = 0;

    foreach($arbeitspakete as $arbeitspaket)
    {
      $this->voraengerbaum[]=$arbeitspaket['vorgaenger'];
      $anzahl++;
      $anzahl = $anzahl + $this->ProjektPlanRekrusiv($projekt,$arbeitspaket['id']);
    }
    return $anzahl;
  }



  function ProjektPDF()
  {
    $id = $this->app->Secure->GetGET('id');

    //    $belegnr = $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$id' LIMIT 1");

    //    if(is_numeric($belegnr) && $belegnr!=0)
    if($id > 0){
      $Brief = new ProjektPDF($this->app);
      $Brief->GetProjekt($id);
      $Brief->displayDocument();
    } //else
    //     $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Noch nicht freigegebene Angeboten k&ouml;nnen nicht als PDF betrachtet werden.!</div>");

    $this->ProjektList();
  }



  function ProjektDelete()
  {
    $ref = $_SERVER['HTTP_REFERER'];
    $id = $this->app->Secure->GetGET('id');
    if(is_numeric($id) && $id > 0) {
      $this->app->DB->Delete("DELETE FROM projekt WHERE id='$id' LIMIT 1");
      $this->app->DB->Delete("DELETE FROM geschaeftsbrief_vorlagen WHERE projekt='$id'");
    }
    $this->app->Location->execute($ref);
  }

  function getProjektArtikel($id)
  {
    $arbeitspakete = null;
    $this->getProjektBaum($arbeitspakete, 0, $id, '', null, true);
    $artikel = null;
    $ind = $this->getProjektArtikelBaum($artikel, 0, $id, 0);
    $i = 0;
    foreach($arbeitspakete as $paket)
    {
      $i++;
      if($i < count($arbeitspakete))
      {
        $tmp = null;
        $ind = $this->getProjektArtikelBaum($tmp, 0, $id, $paket['id'],'',$ind);
        if($tmp)
        {
          foreach($tmp as $v)$artikel[] = $v;
          unset($tmp);
        }
      }
    }
    return $artikel;
  }
  
  function getProjektArtikelBaum(&$artikel, $parent, $projekt, $teilprojekt, $nr = '', $i = 0)
  {
    $res = $this->app->DB->SelectArr("SELECT pa.*, a.nummer, a.name_de FROM projekt_artikel pa LEFT JOIN artikel a ON pa.artikel = a.id WHERE pa.parent = '$parent' AND pa.projekt = '$projekt' AND pa.teilprojekt = '$teilprojekt' ORDER by pa.sort, a.name_de");
    if($res)
    {
      foreach($res as $k => $v)
      {
        $i++;
        $v['nr_alt'] = $v['nr'];
        if($nr != '')
        {
          $v['nr'] = $nr.'.'.$i;
        }else{
          $v['nr'] = $i;
        }
        $artikel[] = $v;
        $this->getProjektArtikelBaum($artikel, $v['id'], $projekt, $teilprojekt, $v['nr']);
      }
    }
    return $i;
  }
  
  function getProjektBaum(&$projekte, $vorgaenger, $projekt, $nr = '', $liste = null, $onlynummertitel = false)
  {
    if($liste  === null)
    {
      for($i = 0; $i <= 22; $i++) {
        $liste[] = 0;
      }
    }
    $zeit_summiert = $liste[0];
    $gebucht_summiert = $liste[1];
    $kosten_summiert = $liste[2];
    $abgerechnet_summiert = $liste[3];
    $offen_summiert = $liste[4];
    $gesamt_zeit_ek_geplant = $liste[5];
    $gesamt_zeit_ek_gebucht = $liste[6];
    $gesamt_zeit_ek_offen = $liste[7];
    $gesamt_zeit_vk_geplant = $liste[8];
    $gesamt_zeit_vk_gebucht = $liste[9];
    $gesamt_zeit_vk_offen = $liste[10];
    $gesamt_artikel_ek_geplant = $liste[11];
    $gesamt_artikel_ek_gebucht = $liste[12];
    $gesamt_artikel_ek_offen = $liste[13];
    $gesamt_artikel_vk_geplant = $liste[14];
    $gesamt_artikel_vk_gebucht = $liste[15];
    $gesamt_artikel_vk_offen = $liste[16];
    $gesamt_rohertrag_zeit = $liste[17];
    $gesamt_rohertrag_artikel = $liste[18];
    $gesamt_rohertrag_gesamt = $liste[19];
    $gesamt_rohertrag_zeit_geplant = $liste[20];
    $gesamt_rohertrag_artikel_geplant = $liste[21];
    $gesamt_rohertrag_gesamt_geplant = $liste[22];
    
    $res = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket WHERE geloescht != 1 AND vorgaenger = '$vorgaenger' AND projekt = '$projekt' ORDER by sort, aufgabe");
    if($res)
    {
      $i = 0;
      foreach($res as $k => $v)
      {
        $i++;
        if($nr != '')
        {
          $v['nr'] = $nr.'.'.$i;
        }else{
          $v['nr'] = $i;
        }

        if(!$onlynummertitel)
        {
          if($v['adresse'])
          {
            $v['verantwortlicher'] = $this->app->DB->Select("SELECT name FROM adresse WHERE id = '".$v['adresse']."' LIMIT 1");
          }else{
            $v['verantwortlicher'] = '';
          }
          $zeit_summiert += $v['zeit_geplant'];
          if($v['artikel_geplant']){
            
            $v['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '".$v['artikel_geplant']."' LIMIT 1");
            /*$v['BE'] = $this->app->DB->Select("SELECT sum(bp.menge) FROM bestellung b INNER JOIN bestellung_position bp ON b.id = bp.bestellung WHERE bp.artikel = '".$v['artikel_geplant']."' ");
            $v['PR'] = $this->app->DB->Select("SELECT sum(bp.menge) FROM produktion b INNER JOIN produktion_position bp ON b.id = bp.produktion WHERE bp.artikel = '".$v['artikel_geplant']."' ");
            $v['AN'] = $this->app->DB->Select("SELECT sum(bp.menge) FROM angebot b INNER JOIN angebot_position bp ON b.id = bp.angebot WHERE bp.artikel = '".$v['artikel_geplant']."' ");
            $v['AB'] = $this->app->DB->Select("SELECT sum(bp.menge) FROM auftrag b INNER JOIN auftrag_position bp ON b.id = bp.auftrag WHERE bp.artikel = '".$v['artikel_geplant']."' ");
            $v['LS'] = $this->app->DB->Select("SELECT sum(bp.menge) FROM lieferschein b INNER JOIN lieferschein_position bp ON b.id = bp.lieferschein WHERE bp.artikel = '".$v['artikel_geplant']."' ");
            $v['RE'] = $this->app->DB->Select("SELECT sum(bp.menge) FROM rechnung b INNER JOIN rechnung_position bp ON b.id = bp.rechnung WHERE bp.artikel = '".$v['artikel_geplant']."' ");
            $v['GS'] = $this->app->DB->Select("SELECT sum(bp.menge) FROM gutschrift b INNER JOIN gutschrift_position bp ON b.id = bp.gutschrift WHERE bp.artikel = '".$v['artikel_geplant']."' ");*/
            if(!$v['BE'])$v['BE'] = '-';
            if(!$v['PR'])$v['PR'] = '-';
            if(!$v['AN'])$v['AN'] = '-';
            if(!$v['AB'])$v['AB'] = '-';
            if(!$v['LS'])$v['LS'] = '-';
            if(!$v['RE'])$v['RE'] = '-';
            if(!$v['GS'])$v['GS'] = '-';
          }else{
            $v['nummer'] = '-';
            $v['BE'] = '-';
            $v['PR'] = '-';
            $v['AN'] = '-';
            $v['AB'] = '-';
            $v['LS'] = '-';
            $v['RE'] = '-';
            $v['GS'] = '-';
          }
          $v['status'] = strtoupper($v['status']);
          $v['zeit_gebucht'] = ((int)$this->app->DB->Select("SELECT sum( TIME_TO_SEC( TIMEDIFF( bis, von ) )) FROM `zeiterfassung` WHERE arbeitspaket = '".$v['id']."'  "))/3600;
          $v['zeit_abgerechnet'] = ((int)$this->app->DB->Select("SELECT sum( TIME_TO_SEC( TIMEDIFF( bis, von ) )) FROM `zeiterfassung` WHERE arbeitspaket = '".$v['id']."' AND abgerechnet = 1"))/3600;
          //$v['zeit_abgerechnet'] = ((int)$this->app->DB->Select("SELECT sum( TIME_TO_SEC( TIMEDIFF( bis, von ) )) FROM `zeiterfassung` WHERE arbeitspaket = '".$v['id']."' AND abgerechnet = 1"))/3600;
          //$v['zeit_offen'] = ((int)$this->app->DB->Select("SELECT sum( TIME_TO_SEC( TIMEDIFF( bis, von ) )) FROM `zeiterfassung` WHERE arbeitspaket = '".$v['id']."' AND status <> 'abgeschlossen' "))/3600;
          $v['zeit_offen'] = $v['zeit_geplant'] - ((int)$this->app->DB->Select("SELECT sum( TIME_TO_SEC( TIMEDIFF( bis, von ) )) FROM `zeiterfassung` WHERE arbeitspaket = '".$v['id']."'"))/3600;;
          $gebucht_summiert += $v['zeit_gebucht'];
          $abgerechnet_summiert += $v['zeit_abgerechnet'];
          $offen_summiert += $v['zeit_offen'];
          
          $v['zeit_ek_geplant'] = $v['ek_geplant']*($v['kalkulationbasis'] === 'pauschale'?1:$v['zeit_geplant']);
          $v['zeit_ek_gebucht'] = 0;//$v['ek_geplant']*($v['kalkulationbasis'] == 'pauschale'?1:$v['zeit_gebucht']);
          
          if($v['zeit_gebucht'])
          {
            $stundenmitstundensatz = (float)$this->app->DB->Select("SELECT sum( TIME_TO_SEC( TIMEDIFF( bis, von ) ) / 3600) FROM `zeiterfassung` WHERE arbeitspaket = '".$v['id']."' AND stundensatz > 0");
            $zeitmitstundensatz = (float)$this->app->DB->Select("SELECT sum( TIME_TO_SEC( TIMEDIFF( bis, von ) ) / 3600 * stundensatz ) FROM `zeiterfassung` WHERE arbeitspaket = '".$v['id']."' AND stundensatz > 0");
            if($v['kalkulationbasis'] === 'pauschale')
            {
              $v['zeit_ek_gebucht'] = $zeitmitstundensatz+(($v['zeit_gebucht']-$stundenmitstundensatz) / ($v['zeit_geplant']?$v['zeit_geplant']:1))*$v['ek_geplant'];
            }else{
              $v['zeit_ek_gebucht'] = $zeitmitstundensatz+($v['zeit_gebucht']-$stundenmitstundensatz)*$v['ek_geplant']; 
            }
            
          }
          $v['zeit_ek_offen'] = $v['ek_geplant']*($v['kalkulationbasis'] === 'pauschale'?($v['zeit_ek_gebucht'] > 0?0:1):$v['zeit_offen']);
          $v['zeit_vk_geplant'] = $v['vk_geplant']*($v['vkkalkulationbasis'] === 'pauschale'?1:$v['zeit_geplant']);
          $v['zeit_vk_gebucht'] = 0;//$v['vk_geplant']*($v['vkkalkulationbasis'] == 'pauschale'?1:$v['zeit_gebucht']);
          $v['zeit_vk_offen'] = $v['vk_geplant']*($v['vkkalkulationbasis'] === 'pauschale'?($v['zeit_vk_gebucht'] > 0?0:1):$v['zeit_offen']);

          
          $v['rohertrag_zeit'] = $v['zeit_vk_geplant'] - $v['zeit_ek_gebucht'];
          $v['rohertrag_zeit_geplant'] = $v['zeit_vk_geplant'] - $v['zeit_ek_geplant'];
          
          
          $artikelliste = $this->app->DB->SelectArr("SELECT pa.* FROM projekt_artikel pa INNER JOIN artikel a ON pa.artikel = a.id WHERE pa.projekt = '$projekt' AND pa.teilprojekt = '".$v['id']."' AND a.geloescht <> 1 AND a.nummer <> 'DEL'");
          $v['artikel_vk_geplant'] = 0;
          $v['artikel_vk_gebucht'] = 0;
          
          $v['artikel_ek_geplant'] = 0;
          $v['artikel_ek_gebucht'] = 0;
          
          if($artikelliste)
          {
            foreach($artikelliste as $art)
            {
              if($art['kalkulationbasis'] === 'prostueck')
              {
                if($art['showinmonitoring'])$v['artikel_vk_geplant'] += $art['vk_geplant'] * $art['geplant'];
                if($art['showinmonitoring'])$v['artikel_ek_geplant'] += $art['ek_geplant'] * $art['geplant'];
                $vk_geplant = $v['vk_geplant'];
                $ek_geplant = $v['ek_geplant'];
              }else{
                $vk_geplant = 0;
                $ek_geplant = 0;
                if($art['showinmonitoring'])$v['artikel_vk_geplant'] += $art['vk_geplant'];
                if($art['showinmonitoring'])$v['artikel_ek_geplant'] += $art['ek_geplant'];
                if($art['geplant'] > 0)
                {
                  $vk_geplant = $art['vk_geplant'] / $art['geplant'];
                  $ek_geplant = $art['ek_geplant'] / $art['geplant'];
                }
              }
              if($vk_geplant)
              {
                $v['artikel_vk_gebucht'] += (float)$this->app->DB->Select("SELECT sum(rp.menge) FROM rechnung r INNER JOIN rechnung_position rp ON r.id = rp.rechnung AND rp.artikel = '".$art['artikel']."' AND r.status != 'storniert' AND r.status != 'angelegt' AND rp.teilprojekt = '".$v['id']."'");
                $v['artikel_vk_gebucht'] -= (float)$this->app->DB->Select("SELECT sum(rp.menge) FROM gutschrift r INNER JOIN gutschrift_position rp ON r.id = rp.gutschrift AND rp.artikel = '".$art['artikel']."' AND r.status != 'storniert' AND r.status != 'angelegt' AND rp.teilprojekt  = '".$v['id']."'");
              }
              if($ek_geplant)
              {
                $v['artikel_ek_gebucht'] += (float)$this->app->DB->Select("SELECT sum(rp.menge) FROM bestellung r INNER JOIN bestellung_position rp ON r.id = rp.bestellung AND rp.artikel = '".$art['artikel']."' AND r.status != 'storniert' AND r.status != 'angelegt' AND rp.teilprojekt = '".$v['id']."'");
              }
            }            
          }
          
          $v['artikel_ek_offen'] = $v['artikel_ek_geplant']-$v['artikel_ek_gebucht'];
          $v['artikel_vk_offen'] = $v['artikel_vk_geplant']-$v['artikel_vk_gebucht'];
          $v['rohertrag_artikel'] = $v['artikel_vk_gebucht'] - $v['artikel_ek_gebucht'];
          $v['rohertrag_gesamt'] = $v['rohertrag_zeit']+$v['rohertrag_artikel'];
          $v['rohertrag_artikel_geplant'] = $v['artikel_vk_geplant'] - $v['artikel_ek_geplant'];
          $v['rohertrag_gesamt_geplant'] = $v['rohertrag_zeit_geplant']+$v['rohertrag_artikel_geplant'];
          
          $v['prognose_artikel'] = $v['artikel_vk_geplant']-$v['artikel_ek_geplant'];
          $v['prognose_zeit'] = $v['zeit_vk_geplant'] - $v['zeit_ek_gebucht'];
          $v['prognose_zeit_geplant'] = $v['zeit_vk_geplant'] - $v['zeit_ek_geplant'];
          $v['prognose_gesamt'] = $v['prognose_artikel'] + $v['prognose_zeit_geplant'];
          $gesamt_artikel_ek_gebucht += $v['artikel_ek_gebucht'];
          $gesamt_artikel_ek_offen += $v['artikel_ek_offen'];
          $gesamt_artikel_ek_geplant += $v['artikel_ek_geplant'];
          $gesamt_artikel_vk_gebucht += $v['artikel_vk_gebucht'];
          $gesamt_artikel_vk_offen += $v['artikel_vk_offen'];
          $gesamt_artikel_vk_geplant += $v['artikel_vk_geplant'];          
          
          $gesamt_zeit_ek_gebucht += $v['zeit_ek_gebucht'];
          $gesamt_zeit_ek_offen += $v['zeit_ek_offen'];
          $gesamt_zeit_ek_geplant += $v['zeit_ek_geplant'];
          $gesamt_zeit_vk_gebucht += $v['zeit_vk_gebucht'];
          $gesamt_zeit_vk_offen += $v['zeit_vk_offen'];
          $gesamt_zeit_vk_geplant += $v['zeit_vk_geplant'];
          
          $gesamt_rohertrag_artikel += $v['rohertrag_artikel'];
          $gesamt_rohertrag_zeit += $v['rohertrag_zeit'];
          $gesamt_rohertrag_gesamt += $v['rohertrag_gesamt'];
          
          $gesamt_rohertrag_artikel_geplant += $v['rohertrag_artikel_geplant'];
          $gesamt_rohertrag_zeit_geplant += $v['rohertrag_zeit_geplant'];
          $gesamt_rohertrag_gesamt_geplant += $v['rohertrag_gesamt_geplant'];
          
          $v['prognose_artikel'] = number_format($v['prognose_artikel'],2,'.','');
          $v['prognose_zeit'] = number_format($v['prognose_zeit'],2,'.','');
          $v['prognose_zeit_geplant'] = number_format($v['prognose_zeit_geplant'],2,'.','');
          $v['prognose_gesamt'] = number_format($v['prognose_gesamt'],2,'.','');
          
          $v['rohertrag_zeit'] = number_format($v['rohertrag_zeit'],2,'.','');
          $v['rohertrag_artikel'] = number_format($v['rohertrag_artikel'],2,'.','');
          $v['rohertrag_gesamt'] = number_format($v['rohertrag_gesamt'],2,'.','');

          $v['rohertrag_zeit_geplant'] = number_format($v['rohertrag_zeit_geplant'],2,'.','');
          $v['rohertrag_artikel_geplant'] = number_format($v['rohertrag_artikel_geplant'],2,'.','');
          $v['rohertrag_gesamt_geplant'] = number_format($v['rohertrag_gesamt_geplant'],2,'.','');
          
          $v['zeit_ek_geplant'] = number_format($v['zeit_ek_geplant'],2,'.','');
          $v['zeit_ek_gebucht'] = number_format($v['zeit_ek_gebucht'],2,'.','');
          $v['zeit_ek_offen'] = number_format($v['zeit_ek_offen'],2,'.','');
          $v['zeit_vk_geplant'] = number_format($v['zeit_vk_geplant'],2,'.','');
          $v['zeit_vk_gebucht'] = number_format($v['zeit_vk_gebucht'],2,'.','');
          $v['zeit_vk_offen'] = number_format($v['zeit_vk_offen'],2,'.','');
          
          $v['artikel_vk_geplant'] = number_format($v['artikel_vk_geplant'],2,'.','');
          $v['artikel_vk_gebucht'] = number_format($v['artikel_vk_gebucht'],2,'.','');
          $v['artikel_vk_offen'] = number_format($v['artikel_vk_offen'],2,'.','');
          $v['artikel_ek_geplant'] = number_format($v['artikel_ek_geplant'],2,'.','');
          $v['artikel_ek_gebucht'] = number_format($v['artikel_ek_gebucht'],2,'.','');
          $v['artikel_ek_offen'] = number_format($v['artikel_ek_offen'],2,'.','');
          
          $v['zeit_gebucht'] = number_format($v['zeit_gebucht'],2,'.','');
          $v['zeit_abgerechnet'] = number_format($v['zeit_abgerechnet'],2,'.','');
          $v['gebucht_summiert'] = $v['zeit_gebucht'];//number_format($gebucht_summiert,2,'.','');
          $v['abgerechnet_summiert'] = number_format($abgerechnet_summiert,2,'.','');
          $v['zeit_offen'] = number_format($v['zeit_offen'],2,'.','');
          $v['zeit_summiert'] = number_format($v['zeit_geplant'],2,'.','') ;  //number_format($zeit_summiert,2,'.','');
          $kosten_summiert += (float)$v['kosten_geplant'];
          $v['kosten_geplant']  = number_format($v['kosten_geplant'],2,',','.');
          
        }
        $projekteindex = empty($projekte)?0:count($projekte);

        // checken ob es so besser ist
        //foreach($v as $v_key=>$v_value)
        //  if(trim(strip_tags($v[$v_key]))=="0.00") $v[$v_key]="";
        $v['editierbar'] = true;
        
        if($v['zeit_geplant'] > 0 && $this->app->DB->Select("SELECT id FROM `teilprojekt_geplante_zeiten` WHERE teilprojekt = '".$v['id']."' AND stundensatz > 0 AND stunden > 0 LIMIT 1"))
        {
          $v['editierbar'] = false;  
        }
        
        $projekte[] = $v;
        $oldgebucht_summiert = $gebucht_summiert;
        $oldzeit_summiert = $zeit_summiert;
        
        $anzahlteilprojekte = count($projekte);
        $liste = $this->getProjektBaum($projekte, $v['id'], $projekt, $v['nr'], 
        array($zeit_summiert,$gebucht_summiert, $kosten_summiert, $abgerechnet_summiert,$offen_summiert
        ,$gesamt_zeit_ek_geplant,$gesamt_zeit_ek_gebucht,$gesamt_zeit_ek_offen
        ,$gesamt_zeit_vk_geplant,$gesamt_zeit_vk_gebucht,$gesamt_zeit_vk_offen
        ,$gesamt_artikel_ek_geplant,$gesamt_artikel_ek_gebucht,$gesamt_artikel_ek_offen
        ,$gesamt_artikel_vk_geplant,$gesamt_artikel_vk_gebucht,$gesamt_artikel_vk_offen
        ,$gesamt_rohertrag_zeit,$gesamt_rohertrag_artikel,$gesamt_rohertrag_gesamt
        ,$gesamt_rohertrag_zeit_geplant,$gesamt_rohertrag_artikel_geplant,$gesamt_rohertrag_gesamt_geplant
        ), $onlynummertitel);
        
        if(count($projekte) == $anzahlteilprojekte)
        {
          //$projekte[count($projekte)-1]['editierbar'] = true;
        }
        
        

        $zeit_summiert = $liste[0];
        $gebucht_summiert = $liste[1];
        $kosten_summiert = $liste[2];
        $abgerechnet_summiert = $liste[3];
        $offen_summiert = $liste[4];
        $gesamt_zeit_ek_geplant = $liste[5];
        $gesamt_zeit_ek_gebucht = $liste[6];
        $gesamt_zeit_ek_offen = $liste[7];
        $gesamt_zeit_vk_geplant = $liste[8];
        $gesamt_zeit_vk_gebucht = $liste[9];
        $gesamt_zeit_vk_offen = $liste[10];
        $gesamt_artikel_ek_geplant = $liste[11];
        $gesamt_artikel_ek_gebucht = $liste[12];
        $gesamt_artikel_ek_offen = $liste[13];
        $gesamt_artikel_vk_geplant = $liste[14];
        $gesamt_artikel_vk_gebucht = $liste[15];
        $gesamt_artikel_vk_offen = $liste[16];
        $gesamt_rohertrag_zeit = $liste[17];
        $gesamt_rohertrag_artikel = $liste[18];
        $gesamt_rohertrag_gesamt = $liste[19];
        $gesamt_rohertrag_zeit_geplant = $liste[20];
        $gesamt_rohertrag_artikel_geplant = $liste[21];
        $gesamt_rohertrag_gesamt_geplant = $liste[22];
        $projekte[$projekteindex]['gebucht_summiert'] += $gebucht_summiert - $oldgebucht_summiert;
        $projekte[$projekteindex]['gebucht_summiert'] = number_format($projekte[$projekteindex]['gebucht_summiert'],2,',','.');
        $projekte[$projekteindex]['zeit_summiert'] += $zeit_summiert - $oldzeit_summiert;
        $projekte[$projekteindex]['zeit_summiert'] = number_format($projekte[$projekteindex]['zeit_summiert'],2,',','.');
      }
    }
    if($vorgaenger == 0)
    {
      if(isset($v))unset($v);
      $v['nr'] = '';
      $v['aufgabe'] = '<b>Summe Gesamt</b>';
      $v['nummer'] = '-';
      $v['BE'] = '-';
      $v['PR'] = '-';
      $v['AN'] = '-';
      $v['AB'] = '-';
      $v['LS'] = '-';
      $v['RE'] = '-';
      $v['GS'] = '-';
      $v['zeit_summiert'] = '<b>'.number_format($zeit_summiert,2).'</b>';
      $v['zeit_geplant'] = $v['zeit_summiert'];
      $v['gebucht_summiert'] = '<b>'.number_format($gebucht_summiert,2).'</b>';
      $v['zeit_gebucht'] = '<b>'.number_format($gebucht_summiert,2).'</b>';
      $v['zeit_offen'] = '<b>'.number_format($offen_summiert,2).'</b>';
      $v['zeit_abgerechnet'] = '<b>'.number_format($abgerechnet_summiert,2).'</b>';
      $v['kosten_geplant'] = '<b>'.number_format($kosten_summiert,2,',','.').'</b>';
      $v['rohertrag_zeit'] = '<b'.($gesamt_rohertrag_zeit < 0?' style="color:red;"':'').'>'.number_format($gesamt_rohertrag_zeit,2,'.','').'</b>';
      $v['rohertrag_artikel'] = '<b'.($gesamt_rohertrag_artikel < 0?' style="color:red;"':'').'>'.number_format($gesamt_rohertrag_artikel,2,'.','').'</b>';
      $v['rohertrag_gesamt'] = '<b'.($gesamt_rohertrag_gesamt < 0?' style="color:red;"':'').'>'.number_format($gesamt_rohertrag_gesamt,2,'.','').'</b>';

      $v['rohertrag_zeit_geplant'] = '<b'.($gesamt_rohertrag_zeit_geplant < 0?' style="color:red;"':'').'>'.number_format($gesamt_rohertrag_zeit_geplant,2,'.','').'</b>';
      $v['rohertrag_artikel_geplant'] = '<b'.($gesamt_rohertrag_artikel_geplant < 0?' style="color:red;"':'').'>'.number_format($gesamt_rohertrag_artikel_geplant,2,'.','').'</b>';
      $v['rohertrag_gesamt_geplant'] = '<b'.($gesamt_rohertrag_gesamt_geplant < 0?' style="color:red;"':'').'>'.number_format($gesamt_rohertrag_gesamt_geplant,2,'.','').'</b>';
      $prognose_artikel = $gesamt_artikel_vk_geplant - $gesamt_artikel_ek_geplant;
      $prognose_zeit = $gesamt_zeit_vk_geplant - $gesamt_zeit_ek_gebucht;
      $prognose_zeit_geplant = $gesamt_zeit_vk_geplant - $gesamt_zeit_ek_geplant;
      $prognose_gesamt = $prognose_artikel + $prognose_zeit_geplant;
      $v['zeit_ek_geplant'] = '<b>'.number_format($gesamt_zeit_ek_geplant,2,'.','').'</b>';
      $v['zeit_ek_gebucht'] = '<b>'.number_format($gesamt_zeit_ek_gebucht,2,'.','').'</b>';
      $v['zeit_ek_offen'] = '<b'.($gesamt_zeit_ek_offen < 0?' style="color:red;"':'').'>'.number_format($gesamt_zeit_ek_offen,2,'.','').'</b>';
      $v['zeit_vk_geplant'] = '<b>'.number_format($gesamt_zeit_vk_geplant,2,'.','').'</b>';
      $v['zeit_vk_gebucht'] = '<b>'.number_format($gesamt_zeit_vk_gebucht,2,'.','').'</b>';
      $v['zeit_vk_offen'] = '<b'.($gesamt_zeit_vk_offen < 0?' style="color:red;"':'').'>'.number_format($gesamt_zeit_vk_offen,2,'.','').'</b>';
      
      $v['artikel_ek_geplant'] = '<b>'.number_format($gesamt_artikel_ek_geplant,2,'.','').'</b>';
      $v['artikel_ek_gebucht'] = '<b>'.number_format($gesamt_artikel_ek_gebucht,2,'.','').'</b>';
      $v['artikel_ek_offen'] = '<b'.($gesamt_artikel_ek_offen < 0?' style="color:red;"':'').'>'.number_format($gesamt_artikel_ek_offen,2,'.','').'</b>';
      $v['artikel_vk_geplant'] = '<b>'.number_format($gesamt_artikel_vk_geplant,2,'.','').'</b>';
      $v['artikel_vk_gebucht'] = '<b>'.number_format($gesamt_artikel_vk_gebucht,2,'.','').'</b>';
      $v['artikel_vk_offen'] = '<b'.($gesamt_artikel_vk_offen < 0?' style="color:red;"':'').'>'.number_format($gesamt_artikel_vk_offen,2,'.','').'</b>';
      $v['prognose_artikel'] = '<b'.($prognose_artikel < 0?' style="color:red;"':'').'>'.number_format($prognose_artikel,2,'.','').'</b>';
      $v['prognose_zeit'] = '<b'.($prognose_zeit < 0?' style="color:red;"':'').'>'.number_format($prognose_zeit,2,'.','').'</b>';
      $v['prognose_zeit_geplant'] = '<b'.($prognose_zeit_geplant < 0?' style="color:red;"':'').'>'.number_format($prognose_zeit_geplant,2,'.','').'</b>';
      $v['prognose_gesamt'] = '<b'.($prognose_gesamt < 0?' style="color:red;"':'').'>'.number_format($prognose_gesamt,2,'.','').'</b>';

      $projekte[] = $v;
    }

    $result =  array($zeit_summiert,$gebucht_summiert,$kosten_summiert,$abgerechnet_summiert,$offen_summiert,$gesamt_zeit_ek_geplant,$gesamt_zeit_ek_gebucht,$gesamt_zeit_ek_offen
        ,$gesamt_zeit_vk_geplant,$gesamt_zeit_vk_gebucht,$gesamt_zeit_vk_offen
        ,$gesamt_artikel_ek_geplant,$gesamt_artikel_ek_gebucht,$gesamt_artikel_ek_offen
        ,$gesamt_artikel_vk_geplant,$gesamt_artikel_vk_gebucht,$gesamt_artikel_vk_offen
        ,$gesamt_rohertrag_zeit,$gesamt_rohertrag_artikel,$gesamt_rohertrag_gesamt
        ,$gesamt_rohertrag_zeit_geplant,$gesamt_rohertrag_artikel_geplant,$gesamt_rohertrag_gesamt_geplant
        );

    return $result;
  }

  function ProjektArbeitspaketMonitor($id)
  {
    $arbeitspaket = $this->app->DB->SelectRow("SELECT * FROM arbeitspaket WHERE id = '$id' LIMIT 1");

    if(empty($arbeitspaket)) {
      return '';
    }

    $check = 0;
    $budget = 0;
    $termin = 0;

    if($arbeitspaket['status'] !== 'abgeschlossen' && $arbeitspaket['status'] !== 'abgerechnet')
    {
      if(empty($arbeitspaket['abgabedatum']) || $arbeitspaket['abgabedatum'] == '0000-00-00' || $this->app->DB->Select("select '".$arbeitspaket['abgabedatum']."' >= date(now())"))
      {
        $termin = 1;
      }else{
        $termin = 2;
      }
      if(round(((int)$this->app->DB->Select("SELECT sum( TIME_TO_SEC( TIMEDIFF( bis, von ) )) FROM `zeiterfassung` WHERE arbeitspaket = '".$id."' "))/3600,2) > $arbeitspaket['zeit_geplant'])
      {
        $check = 2;
      }else{
        $check = 1;
      }
      $bestellungsumme = $this->app->DB->Select("SELECT sum(bp.preis*bp.menge) FROM bestellung_position bp INNER JOIN projekt_artikel pa ON pa.artikel = bp.artikel AND pa.teilprojekt = '$id'");
      if(($bestellungsumme > $arbeitspaket['kosten_geplant']) && $arbeitspaket['kosten_geplant'] > 0)
      {
        $budget = 2;
      }else{
        $budget = 1;
      }
    }
    return '<img src="./themes/new/images/'.($check==0?'grey':($check==1?'checkgo':'checkstop')).'.png" title="'.($check==0?'Teilprojekt abgeschlossen / abgerechnet':($check==1?'Gebuchte Zeit noch in geplanter Zeit':'Gebuchte Zeit nicht in geplanter Zeit')).'"><img src="./themes/new/images/'.($termin==0?'grey':($termin==1?'termingo':'terminstop')).'.png" title="'.($termin==0?'Teilprojekt abgeschlossen / abgerechnet':($termin==1?'Keine Termine &uuml;berschritten':'Termine wurden &uuml;berschritten')).'"><img src="./themes/new/images/'.($budget==0?'grey':($budget==1?'kreditlimitgo':'kreditlimitstop')).'.png" title="'.($budget==0?'Teilprojekt abgeschlossen / abgerechnet':($budget==1?'Material im Budget':'Budget wurde &uuml;berschritten')).'">';
  }
 
  function ProjektUebersicht()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $this->ProjektMenu();


    $speichern = $this->app->Secure->GetPOST('speichern');
    if($speichern!='')
    {
      $kunde = $this->app->Secure->GetPOST("kunde");
      $verantwortlicher = $this->app->Secure->GetPOST("verantwortlicher");
      $kundennummer = strstr($kunde,' ',true);
      $mitarbeiternummer = strstr($verantwortlicher,' ',true);
      $kundeid = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer!='' AND kundennummer='$kundennummer' AND geloescht!=1 LIMIT 1");
      $verantwortlicherid = $mitarbeiternummer;//$this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer!='' AND mitarbeiternummer='$mitarbeiternummer' AND geloescht!=1 LIMIT 1");

      $allowed = "/[^a-zA-Z0-9._-]/";
      $this->app->Secure->POST["abkuerzung"] = preg_replace($allowed,"",$this->app->Secure->POST["abkuerzung"]);
      $this->app->Secure->POST["abkuerzung"]=substr(strtoupper($this->app->Secure->POST["abkuerzung"]),0,20);

      $error = false;

      // pruefe ob es die Abzuerung schon gibt
      $checkprojekt = $this->app->DB->Select("SELECT COUNT(id) FROM projekt WHERE abkuerzung='".$this->app->Secure->POST["abkuerzung"]."' AND abkuerzung!='' AND id!='$id' "); 
      if($checkprojekt > 0)
      {
        $checkprojekt++;
        $this->app->Secure->POST["abkuerzung"]=$this->app->Secure->POST["abkuerzung"]."-".$checkprojekt; 

        $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Die Kennung gibt es bereits. Es wurde automatisch eine Folgenummer angelegt.</div>"); 
        $error = true;
      }    

      if($this->app->Secure->POST["abkuerzung"]=="")
      {
        $kennung = $this->app->DB->Select("SELECT COUNT(id) FROM projekt WHERE abkuerzung LIKE 'PROJEKT%' ") + 1;
        $this->app->Secure->POST["abkuerzung"]="PROJEKT-".$kennung;
        $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Die Kennung ist ein Pflichtfeld. Es wurde eine automatische Kennung vergeben.</div>"); 
        $error = true;
      }

      $this->app->FormHandler->FormUpdateDatabase("projekt",$id);
      $this->app->DB->Update("UPDATE projekt SET kunde='$kundeid', verantwortlicher='$verantwortlicherid' WHERE id='$id' LIMIT 1");
      if($msg!="")
      {
        header("Location: index.php?module=projekt&action=uebersicht&id=$id&msg=$msg");
        exit;
      }

    }

    $this->app->FormHandler->FormGetVars("projekt",$id);
    $data = $this->app->DB->SelectArr("SELECT CONCAT(a.kundennummer,' ',a.name) as kunde, CONCAT(a2.id,' ',a2.name) as mitarbeiter, status FROM projekt p
      LEFT JOIN adresse a ON a.id=p.kunde LEFT JOIN adresse a2 ON a2.id=p.verantwortlicher WHERE p.id='$id' LIMIT 1");
    if(isset($data[0]))
    {  
      $this->app->Tpl->Set('KUNDE',$data[0]['kunde']);
      $this->app->Tpl->Set('VERANTWORTLICHER',$data[0]['mitarbeiter']);    
      switch($data[0]['status']){
        case 'gestartet':
        case 'geplant':
        case 'abgeschlossen':
          $this->app->Tpl->Set("STATUS".strtoupper($data[0]['status']),' selected="selected" ');
        break;
      }
    }



    for($i = 0; $i <= 10; $i++)
    {
      $n1 = 'projektfreifeld'.$i.'typ';
      $n2 = 'projektfreifeld'.$i.'spalte';
      $n3 = 'projektfreifeld'.$i.'sort';
      $freifeldtyp[$i] = $this->app->erp->Firmendaten($n1);
      $freifeldspalte[$i] = $this->app->erp->Firmendaten($n2);
      $freifeldsort[$i] = $this->app->erp->Firmendaten($n3);
      if($freifeldspalte[$i] > 0)
      {
        $spalte[$freifeldspalte[$i]][$i]['index'] = $i;
        $spalte[$freifeldspalte[$i]][$i]['sort'] = $freifeldsort[$i];
        $sort[$freifeldspalte[$i]][$i] = $freifeldsort[$i];
      }
    }
    $s = 1;
    array_multisort($sort[$s], SORT_ASC, $spalte[$s]);
    $tmpi = 0;
    $output = '';
    foreach($spalte[$s] as $k => $v)
    {
      $tmpi++;
      $bez = $this->app->erp->Firmendaten('projektfreifeld'.$v['index']);

      if($freifeldtyp[$v['index']] === 'select')
          {
            $optionen = null;
            $beza = explode('|', $bez);
            $bez = trim($beza[0]);
            if(count($beza) > 1)
            {
              for($inds = 1; $inds < count($beza); $inds++)$optionen[] = trim($beza[$inds]);
            }
          }
      if(empty($bez))$bez = 'Freifeld '.$v['index'];
      $output .= "<tr><td>".$bez.":</td><td>";
      switch($freifeldtyp[$v['index']])
      {
        case 'checkbox':
        if($speichern!="" && $this->app->Secure->GetPOST('freifeld'.$v['index'])==""){
          $this->app->DB->Update("UPDATE projekt SET freifeld".$v['index']."='0' WHERE id = '$id' LIMIT 1");
        } 
          $output .= '<input  type="checkbox" name="freifeld'.$v['index'].'" id="freifeld'.$v['index'].'" value="1" '.($this->app->DB->Select("SELECT freifeld".$v['index']." FROM projekt WHERE id = '$id' LIMIT 1")?' checked="checked" ':'').' />';
        break;
        case 'mehrzeilig':
          $output .= '<textarea  cols="40" name="freifeld'.$v['index'].'" id="freifeld'.$v['index'].'">'.$this->app->DB->Select("SELECT freifeld".$v['index']." FROM projekt WHERE id = '$id' LIMIT 1").'</textarea>';
        break;
        case 'datum':
          $output .='<input type="text" size="10" id="freifeld'.$v['index'].'" name="freifeld'.$v['index'].'" value="'.$this->app->DB->Select("SELECT freifeld".$v['index']." FROM projekt WHERE id = '$id' LIMIT 1").'" />';
          $this->app->YUI->DatePicker('freifeld'.$v['index']);
        break;

        case 'select':
              $output .= '<select name="freifeld'.$v['index'].'" id="freifeld'.$v['index'].'">';
              $tmpv = $this->app->DB->Select("SELECT freifeld".$v['index']." FROM projekt WHERE id = '$id' LIMIT 1");
              if(isset($optionen) && $optionen)
              {
                $found = false;
                foreach($optionen as $ov)
                {
                  $ovvalue=$ov;
                  if(strpos($ov,'=>') !== false) {
                    list($ov, $ovvalue) = explode('=>', $ov);
                  }
                  if($ovvalue == $tmpv)
                  {
                    $found = true;
                    break;
                  }
                }
                if(!$found)$output .= '<option>'.$tmpv.'</option>';
                foreach($optionen as $ov)
                {
                  $ovvalue=$ov;
                  if(strpos($ov,'=>') !== false) {
                    list($ov, $ovvalue) = explode('=>', $ov);
                  }
                  $output .= '<option'.($tmpv == $ovvalue?' selected':'').' value="'.$ovvalue.'">'.$ov.'</option>';
                }
              }else{
                $output .= '<option>'.$tmpv.'</option>';
              }
              $output .= '</select>';
            break;
        default:
          $output .= '<input type="text" size="30" id="freifeld'.$v['index'].'" name="freifeld'.$v['index'].'" value="'.$this->app->DB->Select("SELECT freifeld".$v['index']." FROM projekt WHERE id = '$id' LIMIT 1").'" />';
        break;
      }
      $output .= "</td></tr>";
    }

    $this->app->Tpl->Set("FREIFELDER",$output);

    $this->app->YUI->AutoComplete("abkuerzung","projektname",1);
    $this->app->YUI->AutoComplete("kunde","kunde");
    $this->app->YUI->AutoComplete("verantwortlicher","adresse");
    $this->app->YUI->CkEditor("beschreibung","belege");
    $this->app->YUI->CkEditor("sonstiges","internal");
    $this->app->Tpl->Parse('PAGE','projekt_uebersicht.tpl');
  }
 
  
  function ProjektMiniDetailArbeitspaket()
  {
    $this->ProjektMiniDetailTeilprojekt();
  }

  function ProjektMiniDetailTeilprojekt()
  {
    $this->app->Tpl->Output("projekt_minidetail_teilprojekt.tpl");
    $this->app->ExitXentral();
    /*

    $id = $this->app->Secure->GetGET("id");
    $projekt = $this->app->DB->Select("SELECT projekt FROM arbeitspaket WHERE id='$id'");
    $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM arbeitspaket WHERE id='$id'");
    $vorgaenger = $this->app->DB->Select("SELECT vorgaenger FROM arbeitspaket WHERE id='$id'");
    $vorgaenger_aufgabe = $this->app->DB->Select("SELECT aufgabe FROM arbeitspaket WHERE id='$vorgaenger'");

    $this->app->Tpl->Set('VORGAENGER',$vorgaenger_aufgabe);
    $this->app->Tpl->Set('BESCHREIBUNG',nl2br($beschreibung));
    $this->app->Tpl->Set('PROJEKT',$projekt);
    $this->app->Tpl->Set('ID',$id);

    $table = new EasyTable($this->app);
    $table->Query("
        SELECT 
        CONCAT('<input type=\"checkbox\" checked name=\"z_id[]\" value=\"',z.id,'\">') as '',
        DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR')) AS Datum, 
        DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis,
        FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2) AS Dauer,
        a.name as Mitarbeiter,
        IF(LENGTH(z.aufgabe) > 40, CONCAT('<a title=\"',z.aufgabe,'\" style=\"font-weight:normal\">',LEFT(z.aufgabe, 37), '...</a>'), 
          CONCAT('<a title=\"',z.aufgabe,'\" style=\"font-weight:normal\">',z.aufgabe,'</a>')) as Taetigkeit, 
        CONCAT(v.nummer,' ',v.beschreibung) as verrechnungsart,

        if(z.arbeitsnachweis > 0,CONCAT('<a href=\"index.php?module=arbeitsnachweis&action=edit&id=',z.arbeitsnachweis,'\" target=\"_blank\">gebucht</a>'),'-')  as arbeitsnachweis,

        CONCAT('<a href=\"#\" onclick=\"if(!confirm(\'Wirklich stornieren?\')) return false; else window.location.href=\'index.php?module=zeiterfassung&action=list&do=stornieren&lid=', z.id, '&back=projekt&back_id=$projekt\'\"><img src=\"./themes/new/images/delete.svg\"></a>&nbsp;<a href=\"index.php?module=zeiterfassung&action=create&id=', z.id, '&back=projekt&back_id=$projekt\" ><img src=\"./themes/new/images/edit.svg\"></a>')
        FROM zeiterfassung z 
        LEFT JOIN adresse a ON a.id=z.adresse 
        LEFT JOIN adresse b ON b.id=z.adresse_abrechnung
        LEFT JOIN projekt p ON p.id=z.projekt 
        LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id
        LEFT JOIN verrechnungsart v ON v.nummer=z.verrechnungsart
        WHERE z.arbeitspaket='$id' AND (z.arbeitsnachweis IS NULL OR z.arbeitsnachweis=0) AND ist_abgerechnet!=1 AND abgerechnet!=1 ORDER by z.von
        ");
    $table->DisplayNew('OFFENEZEIT', "Menü","Action");

    $table->Query("
        SELECT 
        DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR')) AS Datum, 
        DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis,
        FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2) AS Dauer,
        a.name as Mitarbeiter,
        IF(LENGTH(z.aufgabe) > 40, CONCAT('<a title=\"',z.aufgabe,'\" style=\"font-weight:normal\">',LEFT(z.aufgabe, 37), '...</a>'), 
          CONCAT('<a title=\"',z.aufgabe,'\" style=\"font-weight:normal\">',z.aufgabe,'</a>')) as Taetigkeit, 
        CONCAT(v.nummer,' ',v.beschreibung) as verrechnungsart,

        if(z.arbeitsnachweis > 0,CONCAT('<a href=\"index.php?module=arbeitsnachweis&action=edit&id=',z.arbeitsnachweis,'\" target=\"_blank\">gebucht</a>'),'-')  as arbeitsnachweis,

        CONCAT('<a href=\"#\" onclick=\"if(!confirm(\'Wirklich stornieren?\')) return false; else window.location.href=\'index.php?module=zeiterfassung&action=list&do=stornieren&lid=', z.id, '&back=projekt&back_id=$projekt\'\"><img src=\"./themes/new/images/delete.svg\"></a>&nbsp;<a href=\"index.php?module=zeiterfassung&action=create&id=', z.id, '&back=projekt&back_id=$projekt\" ><img src=\"./themes/new/images/edit.svg\"></a>')
        FROM zeiterfassung z 
        LEFT JOIN adresse a ON a.id=z.adresse 
        LEFT JOIN adresse b ON b.id=z.adresse_abrechnung
        LEFT JOIN projekt p ON p.id=z.projekt 
        LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id
        LEFT JOIN verrechnungsart v ON v.nummer=z.verrechnungsart
        WHERE z.arbeitspaket='$id' AND (z.arbeitsnachweis >0 OR z.ist_abgerechnet=1 OR z.abgerechnet=1) ORDER by z.von
        ");
    $table->DisplayNew('GEBUCHTEZEIT', "Menü","Action");



    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Output("projekt_minidetail_teilprojekt.tpl");
    exit;*/
  }

  function ProjektDateien()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->ProjektMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT'," (Dateien)");
    $this->app->YUI->DateiUpload('PAGE',"Projekt",$id);
  }


  function ProjektZeit()
  {
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->Secure->GetGET("sid");
    $this->ProjektMenu();
    $this->app->YUI->TableSearch('TAB1',"projektzeiterfassung");

    $this->app->Tpl->Add('TAB1',"<br><table width=\"100%\"><tr><td align=\"center\">
        <input type=\"submit\" value=\"Auftrag erzeugen\" name=\"auftragsubmit\">
        <input type=\"submit\" value=\"Status: Archiv / Freigabe\" name=\"auftragsubmit\">
        <!--			<input type=\"submit\" value=\"Rechnung erzeugen\" name=\"rechnungsubmit\">-->
        </td></tr></table><br></form>");

    $this->app->Tpl->Parse('PAGE',"zeiterfassunguebersicht.tpl");


    /*

    //    $auftragsubmit = $this->app->Secure->GetPOST("auftragsubmit");
    $lieferscheinsubmit = $this->app->Secure->GetPOST("lieferscheinsubmit");
    $z_id = $this->app->Secure->GetPOST("z_id");
    if($lieferscheinsubmit !="") {
    //				print_r($z_id);
    $adresse = $this->app->DB->Select("SELECT kunde FROM projekt WHERE id='$id' LIMIT 1");

    //			$lieferschein = $this-app->erp->CreateLieferschein($adresse);
    for($i=0;$i<count($z_id);$i++)
    {
    $single_z_id = $z_id[$i];
    //$dauer = $this->app->DB->Select("SELECT TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600 AS Dauer FROM zeiterfassung z WHERE z.id='".$single_z_id."'");
    //echo $dauer."<br>";
    $this->app->DB->Select("UPDATE zeiterfassung SET ist_abgerechnet='1', abgerechnet='1' WHERE id='".$single_z_id."'");
    }

    }
    //   $rechnungsubmit = $this->app->Secure->GetPOST("rechnungsubmit");

    $this->app->Tpl->Set(TABTEXT,"Zeiterfassung");

    $tmp = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket WHERE projekt='$id'");

    $options="<option>Alle</option>";

    if($sid=="ohne")	
    $options.="<option value=\"ohne\">Ohne</option>";
    else
    $options.="<option value=\"ohne\">Ohne</option>";

    for($i=0;$i<count($tmp);$i++)
    {

    if($sid==$tmp[$i][id])$checked="selected"; else $checked="";
    $options .="<option value=\"".$tmp[$i][id]."\" $checked>".$tmp[$i][aufgabe]."</option>";

    }

    $this->app->Tpl->Add(INHALT,"
    <script>
    $(function(){
    // bind change event to select
    $('#dynamic_select').bind('change', function () {
    var url = $(this).val(); // get selected value
    if (url) { // require a URL
    window.location = 'index.php?module=projekt&action=zeit&id=$id&sid='+url; // redirect
    }
    return false;
    });
    });
    </script>

    <table width=\"100%\"><tr><td align=\"center\">Auswahl Unterprojekt/Arbeitspaket: <select id=\"dynamic_select\">$options</select></td></tr></table><br>
    <form action=\"\" method=\"post\">[EASYTABLE]");


    if($sid >0)
    $subwhere = " AND ap.id='$sid'";

    $table = new EasyTable($this->app);

    $table->Query("SELECT 
    if(z.abgerechnet,'&nbsp;&nbsp;-',CONCAT('<input type=\"checkbox\" value=\"',z.id,'\" name=\"z_id[]\" checked>')) as 'übernehmen', 
    DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR')) AS Datum, 
    a.name as mitarbeiter,
    DATE_FORMAT(z.von,'%H:%i') as start, DATE_FORMAT(z.bis,'%H:%i') as ende,
    TIMEDIFF(z.bis, z.von) AS Dauer,
    ap.aufgabe as 'unterprojekt/Aufgabe', 
    z.aufgabe as Taetigkeit, 
    if(abgerechnet,'ja','nein') as abgrechnet,
      CONCAT('<a href=\"index.php?module=zeiterfassung&action=create&id=',z.id,'&back=projekt&back_id=$id&back_sid=$sid\"><img src=\"themes/new/images/edit.svg\"></a>&nbsp;
          <a href=\"#\" onclick=\"if(!confirm(\'Wirklich stornieren?\')) return false; else window.location.href=\'index.php?module=zeiterfassung&action=list&do=stornieren&back=projekt&back_id=$id&back_sid=$sid&lid=',z.id,'\'\"><img src=\"themes/new/images/delete.svg\"></a>&nbsp;') as Menü
        FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id
        WHERE z.projekt=$id $subwhere
        ORDER BY 7,Datum, bis DESC
        ");
    $table->DisplayNew(EASYTABLE, "Menü","noAction");

    $summe = $this->app->DB->Select("SELECT
        SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))))
        FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id
        WHERE z.projekt=$id $subwhere");

    //$this->app->Tpl->Add(INHALT,"<br>Summe offen: $summe Summe gesamt: $summegesamt<br>");
    //$this->app->Tpl->Add(INHALT,"Summe offen: $summeeur ".$this->projektwaehrung);
    //$this->app->Tpl->Add(INHALT,"Summe offen: $summeeur ".$this->projektwaehrung);
    $this->app->Tpl->Add(EXTEND,"Summe: $summe");

    $this->app->Tpl->Add(INHALT,"<br><table width=\"100%\"><tr><td align=\"center\">
        <!--<input type=\"submit\" value=\"Auftrag erzeugen\" name=\"auftragsubmit\">-->
        <input type=\"submit\" value=\"Arbeitszeiten freigeben\" name=\"lieferscheinsubmit\">
        <!--			<input type=\"submit\" value=\"Rechnung erzeugen\" name=\"rechnungsubmit\">-->
        </td></tr></table><br></form>");

    $this->app->Tpl->Parse(TAB1,"projekt_zeiterfassung.tpl");

    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
    */
  }


  function ProjektMaterial()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set('TABTEXT',"Materialeinsatz");
    $this->app->Tpl->Set('SUBSUBHEADING',"Positionen aus Bestellungen");
    $table = new EasyTable($this->app);
    $table->Query("SELECT  bp.bezeichnunglieferant as artikel, a.name as lieferant,b.belegnr as bestellung, bp.menge, bp.preis,menge*preis as gesamt, if(bp.abgerechnet,'ja','nein') as rechnung FROM bestellung_position bp 
        LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN adresse a ON b.adresse=a.id WHERE bp.projekt='$id' ORDER By bp.bestellung");

    $table->DisplayNew('MATERIAL', "abgerechnet","noAction");

    $summe = $this->app->DB->Select("SELECT  SUM(menge*preis) FROM bestellung_position WHERE projekt='$id' AND abgerechnet!='1' ORDER By bestellung");
    $summevk= $this->ProjektOffenesMaterial($id);
    //$summegesamt = $summe;

    $this->app->Tpl->Add('MATERIAL',"<br>Summe offen: $summe ");
    $this->app->Tpl->Add('MATERIAL',"<br>Summe offen: $summevk ");
    $this->app->Tpl->Parse('TAB1',"rahmen70.tpl");


    $this->app->Tpl->Set('SUBSUBHEADING',"Positionen aus Lieferungen");
    $table = new EasyTable($this->app);
    $table->Query("SELECT  bp.menge, bp.bezeichnung as artikel, bp.seriennummer, b.belegnr as lieferschein, if(bp.abgerechnet,'ja','nein') as rechnung FROM lieferschein_position bp 
        LEFT JOIN lieferschein b ON bp.lieferschein=b.id LEFT JOIN adresse a ON b.adresse=a.id WHERE b.projekt='$id' ORDER By bp.lieferschein");

    $table->DisplayNew('MATERIAL', "abgerechnet","noAction");

    //$summe = $this->app->DB->Select("SELECT  SUM(menge*preis) FROM bestellung_position WHERE projekt='$id' AND abgerechnet!='1' ORDER By bestellung");
    //$summegesamt = $summe;

    //$this->app->Tpl->Add(INHALT,"<br>Summe offen: $summe Summe gesamt: $summegesamt<br>");



    $this->app->Tpl->Set('SUBSUBHEADING',"Material im Lager");
    $table = new EasyTable($this->app);
    $table->Query("SELECT  DISTINCT a.name_de as artikel, a.nummer, lp.kurzbezeichnung as regal, lpi.menge, a.hersteller FROM lager_platz_inhalt lpi
        LEFT JOIN artikel a ON a.id=lpi.artikel LEFT JOIN lager_platz lp ON lpi.lager_platz=lp.id WHERE a.projekt='$id' GROUP BY a.id");

    $table->DisplayNew('MATERIAL', "abgerechnet","noAction");

    //$summe = $this->app->DB->Select("SELECT  SUM(menge*preis) FROM bestellung_position WHERE projekt='$id' AND abgerechnet!='1' ORDER By bestellung");
    //$summegesamt = $summe;

    //$this->app->Tpl->Add(INHALT,"<br>Summe offen: $summe Summe gesamt: $summegesamt<br>");
  }

  function ProjektOffenesMaterial($id)
  {
    $summe = $this->app->DB->Select("SELECT  SUM(menge*preis) FROM bestellung_position WHERE projekt='$id' AND abgerechnet!='1' ORDER By bestellung");
    $summevk= $summe*((100 + $this->app->erp->GetStandardMarge())/100);
    //$summegesamt = $summe;
    return $summevk;
  }

  function ProjektOffeneZeit($id)
  {
    $summe = $this->app->DB->Select("SELECT FORMAT(SUM(TIMEDIFF(z.bis, z.von))/10000,2) FROM zeiterfassung z LEFT JOIN arbeitspaket ap ON z.arbeitspaket = ap.id AND ap.projekt = '$id' WHERE (z.art='' OR z.art='Arbeit') AND (z.projekt='$id' OR not isnull(ap.id)) AND z.abgerechnet!='1'");
    $summeeur = $summe*$this->app->erp->GetStandardStundensatz();

    return $summeeur;
  }


  function ProjektSchaltung()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->ProjektMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT'," (Schaltung)");
    $this->app->Tpl->Set('PAGE',"
        <br>Neues Teilprojekt anlegen: <br>
        Name:<input type=text value=\"KUBUS 3B\"><br>Budget: <input type=text><br>Liefertermin: <input type=text value=>
        <br><br>
        <br><br>
        <table border=1><tr><td>Teilprojekt</td><td>Aktion</td></tr>
        <tr><td>Prototyp 1 KUBUS 3B</td><td><a>BOM</a>&nbsp;<a>Lagerbestand</a>&nbsp;<a>Bestellung</a>&nbsp;<a>Datenblaetter</a>&nbsp;<a>Schaltplan u. Layout</a>&nbsp;<a>Projekt Charter</a></td>
        <tr><td>SNOM Stick</td><td><a>BOM</a>&nbsp;<a>Lagerbestand</a>&nbsp;<a>Bestellung</a>&nbsp;<a>Datenblaetter</a>&nbsp;<a>Schaltplan u. Layout</a>&nbsp;<a>Projekt Charter</a></td>
        </table>
        <br><br>
        Prototyp 1 (inkl. eagle, stuecklisten, fertigungsauftrag fuer prototype, prueflisten, lagerbestand, bestellungsauftrag usw..)<br>Budget<br>Kostenstellen (ende mit 1)");
  }


  function ProjektKostenstellen()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->ProjektMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT'," (Kostenstellen)");


    $summezeit = $this->ProjektOffeneZeit($id);
    $summevk = $this->ProjektOffenesMaterial($id);

    $kosten = $summezeit + $summevk;
/*
    $this->app->Tpl->Set('KOSTEN',money_format('€ %!n',$kosten));
    $this->app->Tpl->Set('SUMMEZEIT',money_format('€ %!n',$summezeit));
    $this->app->Tpl->Set('SUMMEVK',money_format('€ %!n',$summevk));
*/
    $this->ProjektMaterial();


    $this->app->Tpl->Parse('PAGE',"projekt_kostenstellen.tpl");

  }

  /**
   * @return array
   */
  public function getDataToCopyFromFormular()
  {
    $data = [];
    $postKeys = array_keys($this->app->Secure->POST);
    foreach($postKeys as $postKey) {
      $postKeySplitted = explode('_', $postKey);
      if(isset($postKeySplitted[1])) {
        $itemId = (int)$postKeySplitted[1];
        switch($postKeySplitted[0]) {
          case 'arbeitspaket':
          case 'artikel':
          case 'aufgaben':
          case 'adressrollen':
          case 'wiedervorlagen':
            $data[$postKeySplitted[0]][] = $itemId;
            break;
        }
      }
    }

    return $data;
  }

  /**
   * @param int $fromId
   * @param int $toId
   */
  public function copyProjectData($fromId, $toId)
  {
    $data = $this->getDataToCopyFromFormular();

    $this->app->erp->CopyProjektDaten($fromId, $toId, $data);

    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Das Projekt wurde erfolgreich kopiert.</div>");
    $this->app->Location->execute('index.php?module=projekt&action=uebersicht&id='.$toId.'&msg='.$msg);
    exit;
  }

  /**
   * @param string $shortName
   *
   * @return mixed|string|null
   */
  public function getProjectIdByProjectShortName($shortName)
  {
    return $this->app->DB->Select(sprintf("SELECT `id` FROM `projekt` WHERE `abkuerzung` = '%s' LIMIT 1", $shortName));
  }

  public function handleCopyProjectFromFormSubmit()
  {
    if($this->app->Secure->GetPOST('speichern') && $this->app->Secure->GetPOST('prkopieren')) {
      $fromId = $this->app->Secure->GetGET('id');
      $toId = $this->app->Secure->GetPOST('toid');
      $data = $this->getDataToCopyFromFormular();
      $this->app->erp->CopyProjektDaten($fromId, $toId, $data);
      $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Das Projekt wurde erfolgreich kopiert.</div>");
      $this->app->Location->execute('index.php?module=projekt&action=uebersicht&id='.$toId.'&msg='.$msg);
    }
  }

  /**
   * @param int $customerId
   *
   * @return array|string[]
   */
  public function getNextProjectShortCutByCustomer($customerId)
  {
    $shortCode = null;
    if(empty($customerId)) {
      return ['shortcode' => $shortCode, 'error' => 'Kein Kunde ausgew&auml;lt.'];
    }
    $customerNumber = $this->app->DB->Select(
      sprintf(
        'SELECT a.kundennummer FROM `adresse` AS `a` WHERE a.id = %d LIMIT 1',
        $customerId
      )
    );
    if(empty($customerNumber)) {
      return ['shortcode' => $shortCode, 'error' => 'Keine Kundennummer gefunden.'];
    }

    $shortCodes = $this->app->DB->SelectFirstCols(
        sprintf(
          "SELECT `abkuerzung` FROM `projekt` WHERE `abkuerzung` LIKE '%s.%%'",
          $this->app->DB->real_escape_string($customerNumber)
        )
      );
    if(empty($shortCodes)) {
      return ['shortcode' => $customerNumber.'.1'];
    }
    $newShortCodeIndex = 1;
    $length = 1;
    foreach($shortCodes as $dbShortCode) {
      $oldShortCode = substr($dbShortCode, strlen($customerNumber)+1);
      if(strlen($oldShortCode) > $length) {
        $length = strlen($oldShortCode);
      }
      $oldShortCode = 1 + (int)$oldShortCode;
      if($newShortCodeIndex < $oldShortCode) {
        $newShortCodeIndex = $oldShortCode;
      }
    }

    return [
      'shortcode' =>
        $customerNumber.'.'
        .(strlen($newShortCodeIndex) < $length ?str_repeat('0', $length-strlen($newShortCodeIndex)):'')
        .$newShortCodeIndex
    ];
  }

  /**
   * @return array
   */
  public function getProjectCreateErrorMessagesByForm()
  {
    $error = [];
    $kunde =  (int)$this->app->Secure->GetPOST('kunde');
    $abkuerzung =  $this->app->Secure->GetPOST('abkuerzung');
    $typ = $this->app->Secure->GetPOST('typ');
    if($typ === 'manuell' || $typ === 'kundennummer' || $typ === 'fortlaufend') {
      switch($typ) {
        case 'kundennummer':
          $customerInfo = $this->getNextProjectShortCutByCustomer($kunde);
          if(!empty($customerInfo['error'])) {
            $error[] = $customerInfo['error'];
          }
          break;
      }
    }
    $checkabkuerzung = 0;
    if($typ !== 'fortlaufend') {
      $checkabkuerzung = $this->app->DB->Select("SELECT `id` FROM `projekt` WHERE `abkuerzung` = '$abkuerzung' LIMIT 1");
    }
    if($checkabkuerzung > 0 && $this->app->Secure->GetPOST('typ')==='manuell') {
      $error[] = 'Abkürzung schon vorhanden. Bitte eine andere wählen.';
    }
    if($this->app->Secure->GetPOST('name')==''){
      $error[] = 'Bitte geben Sie eine Bezeichung an.';
    }
    if($this->app->Secure->GetPOST('abkuerzung')=='' && $this->app->Secure->GetPOST('typ')==='manuell') {
      $error[] = 'Bitte geben Sie eine Kennung an.';
    }

    return $error;
  }

  public function ProjektCreate()
  {
    if($this->app->Secure->GetPOST('speichern') && $this->app->Secure->GetPOST('prkopieren')) {
      $this->handleCopyProjectFromFormSubmit();
    }
    $isJsonResponse = $this->app->Secure->GetGET('fromjson');
    $isSaveForm = $this->app->Secure->GetPOST('save') == 1;
    if($isJsonResponse && $isSaveForm) {
      $error = $this->getProjectCreateErrorMessagesByForm();
      if($error) {
        $this->app->User->SetParameter('task_project_id', 0);
        return new JsonResponse(['error' => implode('<br />', $error)]);
      }
      $abkuerzung =  $this->app->Secure->GetPOST('abkuerzung');
      $typ = $this->app->Secure->GetPOST('typ');
      $customerId = (int)$this->app->Secure->GetPOST('kunde');
      if($typ === 'kundennummer') {
        $customerInfo = $this->getNextProjectShortCutByCustomer($customerId);
        $abkuerzung = $customerInfo['shortcode'];
      }
      elseif($typ === 'fortlaufend') {
        $abkuerzung = $this->app->erp->GetNextNummer('projektnummer');
      }
      $check = $this->finishCreateProject($abkuerzung, 0);
      if($check <= 0) {
        return new JsonResponse(
          ['error' => 'Es gab einen Fehler. Das Projekt konnte nicht angelegt werden.']
        );
      }
      $this->app->User->SetParameter('task_project_id', $check);
      return new JsonResponse(['id' => $check]);
    }
    $this->app->erp->MenuEintrag('index.php?module=projekt&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    $schritt2 = $this->app->Secure->GetPOST('schritt2');
    $schritt3 = $this->app->Secure->GetGET('schritt3');
    $auswahl = $this->app->Secure->GetPOST('auswahl');
    if($schritt3=='') {
      $schritt3 = $this->app->Secure->GetPOST('schritt3');
    }
    if($schritt2 !== '' && $auswahl==='neu') {
      $schritt2 = '';
      $schritt3 = 'weiter';
    }
    if($schritt2 == '' && $schritt3 == '') {
      $this->app->Tpl->Parse('PAGE', 'projekt_create_step1.tpl');
      return;
    }
    if($schritt2!='') {
      $this->app->YUI->TableSearch('TAB1', 'projekt_kopie', 'show', '', '', basename(__FILE__), __CLASS__);
      $this->app->Tpl->Parse('PAGE', 'projekt_create_step2.tpl');
      return;
    }
      // auswahl projekt

    $id = $this->app->Secure->GetGET('id');
    if($id > 0) {
      $dbValues = $this->app->DB->SelectRow(sprintf('SELECT * FROM `projekt` WHERE `id` = %d', $id));

      if(strpos($dbValues['abkuerzung'], '.') !== false) {
        $numericind = strlen($dbValues['abkuerzung']);
        $fortlaufennach = '';
        for($i = $numericind -1; $i > 0 ; $i--) {
          if(!is_numeric($dbValues['abkuerzung'][$i])){
            break;
          }
          $fortlaufennach = $dbValues['abkuerzung'][$i].$fortlaufennach;
        }

        $fortlaufendvor = '';
        if($i > 0) {
          $fortlaufendvor = substr($dbValues['abkuerzung'], 0, $i + 1);
        }
        if($fortlaufennach == '') {
          $fortlaufennach = 1;
        }
        $fortlaufennachlen = strlen($fortlaufennach);

        while(!empty($this->getProjectIdByProjectShortName($fortlaufendvor.$fortlaufennach))) {
          $fortlaufennach++;
          if(strlen($fortlaufennach) < $fortlaufennachlen) {
            $fortlaufennach = str_repeat('0',$fortlaufennachlen-strlen($fortlaufennach)).$fortlaufennach;
          }
        }
        $dbValues['abkuerzung'] = $fortlaufendvor.$fortlaufennach;
      }
      else{
        $dbValues['abkuerzung'] = $dbValues['abkuerzung'] . '-1';
      }

      $this->app->Tpl->Set('NAME', $dbValues['name']);
      $this->app->Tpl->Set(
        'KUNDE',
        $this->app->DB->Select(
          sprintf(
            "SELECT CONCAT(`id`,' ',`name`) FROM `adresse` WHERE `id` = %d",
            $dbValues['kunde']
          )
        )
      );
      $this->app->Tpl->Set(
        'VERANTWORTLICHER',
        $this->app->DB->Select(
          sprintf("SELECT CONCAT(`id`,' ',`name`) FROM `adresse` WHERE `id` = %d",
            $dbValues['verantwortlicher'])
        )
      );
      $this->app->Tpl->Set('ABKUERZUNG', $dbValues['abkuerzung']);
      $this->app->Tpl->Set('BESCHREIBUNG', $dbValues['beschreibung']);
      $this->app->Tpl->Set('PROJEKT', $dbValues['projekt']);
      $this->app->Tpl->Set('STATUS', $dbValues['status']);

      $typ = $this->app->Secure->GetPOST('typ');
      if($typ === 'manuell' || $typ === 'kundennummer' || $typ === 'fortlaufend') {
        $this->app->Tpl->Set(strtoupper($typ),' checked="checked" ');
      }
      else{
        $this->app->Tpl->Set('MANUELL',' checked="checked" ');
      }
    }
    else {
      $name =  $this->app->Secure->GetPOST('name');
      $kunde =  $this->app->Secure->GetPOST('kunde');
      $verantwortlicher =  $this->app->Secure->GetPOST('verantwortlicher');
      $abkuerzung =  $this->app->Secure->GetPOST('abkuerzung');
      $beschreibung =  $this->app->Secure->GetPOST('beschreibung');
      $projekt =  $this->app->Secure->GetPOST('projekt');
      $status =  $this->app->Secure->GetPOST('status');
      $this->app->Tpl->Set('NAME', $name);
      $this->app->Tpl->Set('KUNDE', $kunde);
      $this->app->Tpl->Set('VERANTWORTLICHER', $verantwortlicher);
      $this->app->Tpl->Set('ABKUERZUNG', $abkuerzung);
      $this->app->Tpl->Set('BESCHREIBUNG', $beschreibung);
      $this->app->Tpl->Set('PROJEKT', $projekt);
      $this->app->Tpl->Set('STATUS', $status);
      $typ = $this->app->Secure->GetPOST('typ');
      if($typ === 'manuell' || $typ === 'kundennummer' || $typ === 'fortlaufend') {
        $this->app->Tpl->Set(strtoupper($typ),' checked="checked" ');
        switch($typ) {
          case 'manuell':
            //nichts weiter zu tun
          break;
          case 'kundennummer':
            if($kunde) {
              $customerNumber = $this->app->DB->Select(
                sprintf('SELECT `kundennummer` FROM `adresse` WHERE `id` = %d LIMIT 1', $kunde)
              );
              if($customerNumber == '') {
                $error[]="Keine Kundennummer gefunden.";
              }
              else{
                $shortCodes = $this->app->DB->SelectFirstCols(
                  sprintf(
                    "SELECT `abkuerzung` FROM `projekt` WHERE `abkuerzung` LIKE '%s'",
                    $this->app->DB->real_escape_string($customerNumber).'.%'
                  )
                );
                if(!empty($shortCodes)) {
                  $postFixNumber = 1;
                  $postFixStringLength = 1;
                  foreach($shortCodes as $shortCode) {
                    $dbPostFixNumber = substr($shortCode, strlen($customerNumber) + 1);
                    if(strlen($dbPostFixNumber) > $postFixStringLength) {
                      $postFixStringLength = strlen($dbPostFixNumber);
                    }
                    $newPostFixNumber = 1 + (int)$dbPostFixNumber;
                    if($postFixNumber < $newPostFixNumber) {
                      $postFixNumber = $newPostFixNumber;
                    }
                  }
                  $postFixNumberString = (string)$postFixNumber;
                  if(strlen($postFixNumberString) < $postFixStringLength) {
                    $prefixZeroCounts = $postFixStringLength - strlen($postFixNumberString);
                    $postFixNumberString = str_repeat('0', $prefixZeroCounts) .$postFixNumberString;
                  }
                  $abkuerzung = $customerNumber . '.' . $postFixNumberString;
                }
                else{
                  $abkuerzung = $customerNumber . '.1';
                }
              }
            }else{
              $error[]="Kein Kunde ausgew&auml;lt.";
            }
          break;
          case 'fortlaufend':
            $abkuerzung = $this->app->erp->GetNextNummer('projektnummer');
          break;
        }
      }
      else{
        $this->app->Tpl->Set('MANUELL',' checked="checked" ');
      }
    }
    // pruefe ob es abkuerzung schon gibt

    if($isSaveForm) {
      $checkabkuerzung = $this->app->DB->Select("SELECT p.id FROM `projekt` AS `p` WHERE p.abkuerzung = '$abkuerzung' LIMIT 1");

      if($checkabkuerzung > 0 && $this->app->Secure->GetPOST('typ')==='manuell'){
        $error[] = 'Abkürzung schon vorhanden. Bitte eine andere wählen.';
      }
      if($this->app->Secure->GetPOST('name')==''){
        $error[] = 'Bitte geben Sie eine Bezeichung an.';
      }
      if($this->app->Secure->GetPOST("abkuerzung")=='' && $this->app->Secure->GetPOST('typ')==='manuell'){
        $error[] = 'Bitte geben Sie eine Kennung an.';
      }

      if(empty($error)) {
        $check = $this->finishCreateProject($abkuerzung);

        if($check > 0) {
          $oldProjectId  = (int)$this->app->Secure->GetGET('id');

          if($oldProjectId > 0){
            $this->createAdditionalInputForCopyProjects($oldProjectId,$check);
            return;
          }
          $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Das Projekt wurde erfolgreich angelegt.</div>");
          $this->app->Location->execute("index.php?module=projekt&action=uebersicht&id=$check&msg=$msg");

        }
        else{
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Es gab einen Fehler. Das Projekt konnte nicht angelegt werden.</div>  ");
          $this->app->Location->execute("index.php?module=projekt&action=list&msg=$msg");
        }
      }
    }

    if(!empty($error)) {
      foreach($error as $errorMessage) {
        $this->app->Tpl->Add('MESSAGE', '<div class="error">'.$errorMessage.'</div>');
      }
    }
    $this->app->Tpl->Add(
      'STATUSSEL',
      $this->getProjectStatusSelect()
    );
    $this->app->YUI->AutoComplete('kunde', 'adresse');
    $this->app->YUI->AutoComplete('verantwortlicher', 'adresse');

    $this->app->YUI->CkEditor('beschreibung', 'belege');
    $this->app->Tpl->Parse('TAB1', 'projekt_create_step3.tpl');
    $this->app->Tpl->Parse('PAGE', 'projekt_create_step_form.tpl');
  }

  /**
   * @param null|string $statusSelected
   *
   * @return string
   */
  public function getProjectStatusSelect($statusSelected = null)
  {
    $html = '';
    if($statusSelected === null) {
      $statusSelected = (string)$this->app->Secure->GetPOST('status');
    }
    if(empty($statusSelected)) {
      $statusSelected = 'gestartet';
    }
    foreach(['geplant','gestartet','abgeschlossen'] as $status) {
      $html .= '<option'.($statusSelected === $status?' selected="selected" ':'').'>'.$status.'</option>';
    }

    return $html;
  }

  /**
   * @var string   $abkuerzung
   * @var int|null $id
   *
   * @return int
   */
  public function finishCreateProject($abkuerzung, $id = null): int
  {
    $data = [];
    $data['name'] = $this->app->Secure->GetPOST('name');
    $data['kunde'] = $this->app->Secure->GetPOST('kunde');
    $data['verantwortlicher'] = explode(' ', $this->app->Secure->GetPOST('verantwortlicher'));
    $data['verantwortlicher'] = (int)reset($data['verantwortlicher']);
    if($data['verantwortlicher'] <= 0) {
      $data['verantwortlicher']  = $this->app->User->GetAdresse();
    }
    $data['abkuerzung'] = $this->app->Secure->GetPOST('abkuerzung');
    $data['beschreibung'] = $this->app->Secure->GetPOST('beschreibung');
    $data['projekt'] = $this->app->Secure->GetPOST('projekt');
    $data['status'] = $this->app->Secure->GetPOST('status');
    $data['farbe'] =  $this->app->Secure->GetPOST('farbe');
    if($data['farbe'] === 'false') {
      $data['farbe'] = '';
    }
    if($abkuerzung==''){
      $abkuerzung = $data['abkuerzung'];
    }

    $allowed = "/[^a-zA-Z0-9._-]/";
    $abkuerzung = preg_replace($allowed, '', $abkuerzung);
    $abkuerzung =  substr(strtoupper($abkuerzung),0,20);

    if($abkuerzung) {
      $data['abkuerzung'] = $abkuerzung;
    }

    // kopieren oder anlegen
    if($id === null) {
      $id = $this->app->Secure->GetGET('id');
    }
    if($id > 0) {
      $check = $this->app->erp->CopyProjekt($id);
      if($check > 0){
        $this->app->DB->Update(
          "UPDATE `projekt` 
          SET `name` = '" . $data['name'] . "', `abkuerzung` = '" . $data['abkuerzung'] . "',
          `kunde` = '" . $data['kunde'] . "', `verantwortlicher` = '" . $data['verantwortlicher'] . "',
          `beschreibung` = '" . $data['beschreibung'] . "', `status` = '" . $data['status'] . "' 
          WHERE `id` = '$check' 
          LIMIT 1"
        );
        $this->addUserToProjectToUserAddressRole($check);
        if($this->app->erp->Firmendaten('projektoeffentlich')) {
          $this->app->DB->Update("UPDATE projekt SET oeffentlich = 1 WHERE id = '$check' LIMIT 1");
        }
      }
    }
    else {
      $check = $this->createProjectFromData($data);
      $this->addUserToProjectToUserAddressRole($check);
    }

    return (int)$check;
  }

  /**
   * @param int $oldProjectId
   * @param int $copyProjectId
   * @return bool
   */
  public function createAdditionalInputForCopyProjects(int $oldProjectId, int $copyProjectId): bool
  {
    $hasAdditionalInput = false;
    $this->app->Tpl->Set('TOID',$copyProjectId);
  }

  /**
   * @param int $projectId
   */
  public function addUserToProjectToUserAddressRole($projectId)
  {
    if(empty($projectId) || $this->app->erp->UserProjektRecht($projectId)) {
      return;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `adresse_rolle` 
          (`adresse`, `subjekt`, `praedikat`, `objekt`, `parameter`, `von`, `projekt`)
        VALUES (%d, 'Mitglied', 'von', 'Projekt', '%d', NOW(), '%d')",
        $this->app->User->GetAdresse(), $projectId, $projectId
      )
    );
  }

  /**
   * @param array $data
   *
   * @return int
   */
  public function createProjectFromData($data) {
    if(empty($data['waehrung'])) {
      $data['waehrung'] = $this->app->erp->Firmendaten('waehrung');
    }
    if(empty($data['steuersatz_normal'])) {
      $data['steuersatz_normal'] = $this->app->erp->Firmendaten('steuersatz_normal');
    }
    if(empty($data['steuersatz_ermaessigt'])) {
      $data['steuersatz_ermaessigt'] = $this->app->erp->Firmendaten('steuersatz_ermaessigt');
    }
    if($this->app->erp->Firmendaten('projektoeffentlich')) {
      $data['oeffentlich'] = 1;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `projekt` 
        (`name`, `abkuerzung`, `kunde`, `verantwortlicher`, `beschreibung`, `status`, `waehrung`,
         `steuersatz_normal`, `steuersatz_ermaessigt`, `oeffentlich`, `farbe`) 
         VALUES 
            ('%s', '%s', %d, '%s', '%s', '%s','%s', %f, %f, %d, '%s')",
        $data['name'], $data['abkuerzung'], $data['kunde'], $data['verantwortlicher'], $data['beschreibung'],
        (empty($data['status'])?'gestartet':$data['status']), $data['waehrung'],
        $data['steuersatz_normal'], $data['steuersatz_ermaessigt'], (int)!empty($data['oeffentlich']),
        $data['farbe']
      )
    );

    return (int)$this->app->DB->GetInsertID();
  }

  function ProjektList()
  {
    $this->app->erp->MenuEintrag("index.php?module=projekt&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=projekt&action=schedule","Projektplan");
    $this->app->erp->MenuEintrag("index.php?module=projekt&action=create","Neues Projekt anlegen");
    $this->app->YUI->TableSearch('TAB1','projekttabelle', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE',"projektuebersicht.tpl");
  }


  function ProjektMenu($id="")
  {
    if($id=="")
      $id = $this->app->Secure->GetGET("id");

    //$nummer = $this->app->Secure->GetPOST("nummer");

    $data = $this->app->DB->SelectArr("SELECT p.abkuerzung as abk,p.name as projekt_name, a.kundennummer, a.name as kunde 
      FROM projekt p LEFT JOIN adresse a ON a.id=p.kunde WHERE p.id='$id' LIMIT 1");

    $data = reset($data);
    $this->app->Tpl->Add('KURZUEBERSCHRIFT2',$data['abk']." ".$data['projekt_name']." ".$data['kundennummer']." ".$data['kunde']);

    $this->app->erp->MenuEintrag("index.php?module=projekt&action=uebersicht&id=$id","&Uuml;bersicht");

    $anzahldateien = $this->app->erp->AnzahlDateien("Projekt",$id);
    if($anzahldateien > 0) $anzahldateien = " (".$anzahldateien.")"; else $anzahldateien="";

    $this->app->erp->MenuEintrag("index.php?module=projekt&action=dateien&id=$id","Dateien".$anzahldateien);
//    if($this->app->erp->Version()!="stock")
//      $this->app->erp->MenuEintrag("index.php?module=projekt&action=arbeitspaket&id=$id","Teilprojekte / Arbeitspakete");
    //    $this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=projekt&action=edit&id=$id\">Adressen</a></li>");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=projekt&action=schaltung&id=$id\">Schaltungen</a></li>");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=projekt&action=edit&id=$id\">Kosten</a></li>");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=projekt&action=edit&id=$id\">Wareneingang</a></li>");
    //    $this->app->erp->MenuEintrag("index.php?module=projekt&action=zeit&id=$id","Zeiterfassung");
    //    $this->app->erp->MenuEintrag("index.php?module=projekt&action=arbeitsnachweise&id=$id","Arbeitsnachweise");
    //$this->app->erp->MenuEintrag("index.php?module=projekt&action=kostenstellen&id=$id","Kostenstellen");
//    if($this->app->erp->Version()!="stock")
//      $this->app->erp->MenuEintrag("index.php?module=projekt&action=kostenstellen&id=$id","Kostenstellen");
    //$this->app->Tpl->Add(TABS,"<a href=\"index.php?module=projekt&action=kosten&id=$id\">idGesamtkalkulation</a>&nbsp;");
    // $this->app->erp->MenuEintrag("index.php?module=projekt&action=pdf&id=$id","PDF");
//    if($this->app->erp->Version()!="stock")
//      $this->app->erp->MenuEintrag("index.php?module=projekt&action=plan&id=$id","Projektplan");



    $this->app->erp->MenuEintrag("index.php?module=projekt&action=edit&id=$id","Einstellungen");

    $this->app->erp->MenuEintrag("index.php?module=projekt&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    $this->app->erp->RunMenuHook('projekt');
    //$this->app->erp->MenuEintrag("index.php?module=projekt&action=statistik&id=$id","Statistik");

  }

  function ProjektArbeitsnachweise()
  {

    $this->ProjektMenu();

    $this->app->YUI->TableSearch('TAB1',"arbeitsnachweiseprojekt");
    $this->app->Tpl->Set('TABTEXT',"Arbeitsnachweise");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }

  function ArbeitspaketReadDetails($index,&$ref)
  {

  }

  function ProjektArbeitspaket()
  {
    $this->ProjektMenu();

    $id = (int)$this->app->Secure->GetGET("id");
    $pakete = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket WHERE projekt = '$id' ORDER by vorgaenger, sort, id");
    if($pakete)
    {
      $oldvorgaenger = -1;
      foreach($pakete as $paket)
      {
        if($paket['sort'] == 0)
        {
          $newsort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM arbeitspaket WHERE projekt = '$id' AND vorgaenger = '".$paket['vorgaenger']."'");
          $this->app->DB->Update("UPDATE arbeitspaket SET sort = '$newsort' WHERE id = '".$paket['id']."' LIMIT 1");
        }
      }      
    }
   
    $abgerechnet = $this->app->Secure->GetPOST("abgerechnet");

    $this->app->Tpl->Parse('MANUELLCHECKBOX',"checkbox.tpl");

    $this->app->Tpl->Add('OFFENE',
        "<center>
        <input type=\"submit\" value=\"markierte Teilprojekte mit Zeitangabe in Rechnung oder Auftrag &uuml;berf&uuml;hren\" name=\"inklzeit\">
        <input type=\"submit\" value=\"markierte Teilprojekte ohne Zeitangabe in Rechnung oder Auftrag &uuml;berf&uuml;hren\">
        <br>
        <br>
        <input type=\"submit\" value=\"als abgerechnet markieren\" name=\"abgerechnetmarkiert\">
        <input type=\"submit\" value=\"als offen markieren\" name=\"offenmarkiert\">
        </center>");

    if($abgerechnet!="")
    {
      $zid = $this->app->Secure->GetPOST("z_id");
      for($i=0;$i<count($zid);$i++)
      {
        $zid_i = (int)$zid[$i];
        $this->app->DB->Update("UPDATE zeiterfassung SET ist_abgerechnet=1, abgerechnet=1 WHERE id='$zid_i' LIMIT 1");
      }
    }

    // neues arbeitspaket
    $widget = new WidgetArbeitspaket($this->app,'TAB2');
    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=projekt&action=arbeitspaket&id=$id#tabs-1");
    $this->app->Tpl->Set('TMPSCRIPT',"<script type=\"text/javascript\">$(document).ready(function(){ $('#tabs').tabs('select', 1); });</script>");

    $widget->Create();


    // easy table mit arbeitspaketen YUI als template 
    $this->app->YUI->TableSearch('TAB1',"projektzeiterfassung");

    $tmp = $this->app->DB->Select("SELECT SUM(zeit_geplant) FROM arbeitspaket WHERE projekt='$id'");

    //$this->app->Tpl->Add('TAB1',"<div class=\"info\">Kontigent Projekt (mit abgeschlossenen) geplant: $tmp</div>");

    $this->app->Tpl->Parse('PAGE',"arbeitspaketeuebersicht.tpl");
  }

  function ProjektArbeitspaketEditPopup()
  {
    //$frame = $this->app->Secure->GetGET("frame");
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->Secure->GetGET("sid");
    $this->app->Tpl->Set('OPENDISABLE',"<!--");
    $this->app->Tpl->Set('CLOSEDISABLE',"-->");


    $sid = $this->app->DB->Select("SELECT projekt FROM arbeitspaket WHERE id='$id' LIMIT 1");
    //    $this->ProjektMenu($sid);

    $abk= $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$sid' LIMIT 1");
    $name= $this->app->DB->Select("SELECT name FROM projekt WHERE id='$sid' LIMIT 1");



    $this->app->Tpl->Add('KURZUEBERSCHRIFT',"Projekt $abk");
    $this->app->Tpl->Add('KURZUEBERSCHRIFT2',$name);



    $this->app->erp->MenuEintrag("index.php?module=projekt&action=arbeitspaket&id=$sid","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=projekt&action=arbeitspaket&id=$sid';\">");

    $widget = new WidgetArbeitspaket($this->app,'TAB1');
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=projekt&action=arbeitspaket&id=$sid#tabs-1");
    $widget->Edit();


    $this->app->Tpl->Add('TAB2',"Sie bearbeiten gerade ein Arbeitspaket. Erst nach dem Speichern k&ouml;nnen neue Arbeitspakete angelegt werden.");

    $this->app->Tpl->Parse('PAGE',"arbeitspaketeuebersicht.tpl");
  }


  function ProjektArbeitspaketDisable()
  {
    //   $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET("id");

    $sid = $this->app->DB->Select("SELECT projekt FROM arbeitspaket WHERE id='$id' LIMIT 1");
    $this->app->DB->Update("UPDATE arbeitspaket SET abgenommen=1,abgenommen_von='".$this->app->User->GetID()."' WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=projekt&action=arbeitspaket&id=".$sid);
    exit;

  }
  
  function DeleteArbeitspaket($id)
  {
    $id = (int)$id;
    if(!$this->app->erp->RechteVorhanden('projekt','arbeitspaketdelete'))return false;
    $id = $this->app->DB->Select("SELECT id FROM arbeitspaket WHERE id = '$id' LIMIT 1");
    $sid = $this->app->DB->Select("SELECT projekt FROM arbeitspaket WHERE id='$id' LIMIT 1");
    if(!$id || !$sid)return false;
    $vorgaenger = (int)$this->app->DB->Select("SELECT vorgaenger FROM arbeitspaket WHERE id='$id' LIMIT 1");
    if($vorgaenger)$this->app->DB->Update("UPDATE arbeitspaket SET vorgaenger = '$vorgaenger' WHERE projekt = '$sid' AND vorgaenger = '$id'");
    $this->app->DB->Delete("DELETE FROM arbeitspaket WHERE id='$id' LIMIT 1");
    return true;
  }

  function ProjektArbeitspaketDelete()
  {
    //    $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->DB->Select("SELECT projekt FROM arbeitspaket WHERE id='$id' LIMIT 1");
    $this->DeleteArbeitspaket($id);
    header("Location: index.php?module=projekt&action=arbeitspaket&id=".$sid);
    exit;
  }


  function ProjektArbeitspaketCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $id = $this->app->DB->MysqlCopyRow("arbeitspaket","id",$id);
    $this->app->DB->Update("UPDATE arbeitspaket SET geloescht='0', abgenommen='0', abgenommen_von='0', abgenommen_bemerkung='' WHERE id='$id' LIMIT 1");

    //$this->app->DB->Update("UPDATE einkaufspreise SET geloescht='1' WHERE id='$id' LIMIT 1");
    $sid = $this->app->DB->Select("SELECT projekt FROM arbeitspaket WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=projekt&action=arbeitspaket&id=".$sid);
    exit;
  }


  function ProjektKalender()
  {
    $this->ProjektMenu();
    
    if($this->app->Secure->GetPOST('submitForm') == 1)
    {
      $id = (int)$this->app->Secure->GetGET("id");
      $eventid = $this->app->Secure->GetPOST("eventid");
      $titel = $this->app->Secure->GetPOST("titel");
      $datum = $this->app->Secure->GetPOST("datum");
      $datum_bis = $this->app->Secure->GetPOST("datum_bis");
      $allday = $this->app->Secure->GetPOST("allday");
      $public = $this->app->Secure->GetPOST("public");
      $erinnerung = $this->app->Secure->GetPOST("erinnerung");
      $von = $this->app->Secure->GetPOST("von");
      $bis = $this->app->Secure->GetPOST("bis");
      $beschreibung = $this->app->Secure->GetPOST("beschreibung");
      $ort = $this->app->Secure->GetPOST("ort");
      $adresse = $this->app->Secure->GetPOST("adresse");
      $adresseintern = $this->app->Secure->GetPOST("adresseintern");
      $projekt = $this->app->Secure->GetPOST("projekt");

      $personen = $this->app->Secure->GetPOST("personen");
      if($personen == '')$personen = null;
      $color = $this->app->Secure->GetPOST("color");
      $mode = $this->app->Secure->GetPOST('mode');
      $von_datum =  $this->app->String->Convert("$datum $von", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");
      $bis_datum =  $this->app->String->Convert("$datum_bis $bis", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");

      if($allday=='1') {
        $von_datum = $this->app->String->Convert("$datum 00:00", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");
        $bis_datum = $this->app->String->Convert("$datum_bis 00:00", "%1.%2.%3 %4:%5", "%3-%2-%1 %4:%5");
        //$bis_datum = $datum_bis;
      }

      $adresse = trim($adresse);
      $rest = explode(" ",$adresse);
      $rest = $rest[0];
      $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE id='$rest' AND geloescht=0 LIMIT 1");

      $adresseintern = trim($adresseintern);
      $rest = explode(" ",$adresseintern);
      $rest = $rest[0];
      $adresseintern =  $this->app->DB->Select("SELECT id FROM adresse WHERE id='$rest' AND geloescht=0 LIMIT 1");

      $projekt = trim($projekt);
      $projekt =  $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND abkuerzung!='' LIMIT 1");
      if($mode=="new") {
        $this->app->DB->Insert("INSERT INTO kalender_event (ort,bezeichnung,beschreibung, von, bis, allDay, color, public,adresse,adresseintern,angelegtvon,erinnerung,projekt,typ)
            VALUES ('$ort','$titel', '$beschreibung','$von_datum', '$bis_datum', '$allday', '$color', '$public','$adresse','$adresseintern','".$this->app->User->GetAdresse()."','$erinnerung','$projekt','meilenstein')");
        $event = $this->app->DB->GetInsertID();

        $calendarActionType = 'added';
        $this->app->erp->RunHook('kalender_event_hook', 2, $event, $calendarActionType);
      }elseif($mode == "edit")
      {
        
        $check = $this->app->DB->Select("SELECT id FROM kalender_event WHERE  id='$eventid' AND projekt = '$id' AND typ = 'meilenstein' LIMIT 1");
        if($check)
        {
          $this->app->DB->Update("UPDATE kalender_event SET ort='$ort',bezeichnung='$titel', beschreibung='$beschreibung',von='$von_datum', bis='$bis_datum',
              allDay='$allday', color='$color', public='$public',adresse='$adresse',adresseintern='$adresseintern',erinnerung='$erinnerung' WHERE id='$eventid' LIMIT 1");

          $calendarActionType = 'modified';
          $this->app->erp->RunHook('kalender_event_hook', 2, $eventid, $calendarActionType);

          $this->app->DB->Delete("DELETE FROM kalender_user WHERE event='$eventid'");
          $event = $eventid;
        }else{
          $mode = "";
        }
      }
      if(!in_array($this->app->User->GetID(),$personen))
      {
        $personen[]=$this->app->User->GetID();
      }

      // Schreibe Personen
      if(is_numeric($event) && is_array($personen) && count($personen) && $mode!="delete" && $mode != "") {
        for($p=0;$p<count($personen);$p++)
          $this->app->DB->Insert("INSERT INTO kalender_user (event, userid) VALUES ('$event', '{$personen[$p]}')");
      }
    }
    
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd == 'getkalender')
    {
      $id = $this->app->Secure->GetGET('id');
      $data['write'] = 0;
      if(strpos($id, 'task'))
      {
        $id = (int)str_replace('task','',$id);
        $kalenderevent = $this->app->DB->SelectArr("SELECT id, ort, bezeichnung AS titel, beschreibung, von, bis, allDay, color, public,erinnerung,adresse,adresseintern,projekt, typ FROM kalender_event WHERE id='$id' LIMIT 1");
        if($kalenderevent)
        {
          $data = reset($kalenderevent);
          $data['write'] = 0;
          $data['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '".$data['projekt']."' LIMIT 1");
          if($data['typ'] == 'meilenstein')$data['write'] = 1;
          $personen = $this->app->DB->SelectArr("SELECT DISTINCT ku.userid, a.name FROM kalender_user AS ku
              LEFT JOIN user AS u ON u.id=ku.userid 
              LEFT JOIN adresse a ON a.id=u.adresse
              WHERE ku.event='$id' ORDER BY u.username ");
          $data['personen'] = $personen;
        }
      }
      echo json_encode($data);
      exit;
      
    }
    $user = $this->app->User->GetID();
    $users = $this->app->DB->SelectArr("SELECT u.id, a.name as description FROM user u LEFT JOIN adresse a ON a.id=u.adresse WHERE u.activ='1' AND u.kalender_ausblenden!=1 ORDER BY u.username");
    for($i=0; $i<count($users);$i++){
      $user_out .= "<option value=\"{$users[$i]['id']}\" $select>{$users[$i]['description']}</option>";
    }
    $this->app->Tpl->Set('PERSONEN', $user_out);


    $this->app->Tpl->Set('LINKADRESSE',"<a href=\"#\" onclick=\"splitstring = document.getElementById('adresse').value; felder = splitstring.split(' ', 3); if( felder[0] > 0) window.location.href='index.php?module=adresse&action=brief&id=' + felder[0];\" style=\"font-weight:normal;text-decoration:underline; position:absolute;margin-top:5px;margin-left:5px;\"><img src=\"themes/new/images/forward.svg\"></a>");
    $id = (int)$this->app->Secure->GetGET("id");
    $this->app->Tpl->Set('PROJEKTNAME', $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$id' LIMIT 1"));
    //$this->app->Tpl->Set('COLORS', $this->ColorPicker());
    $this->app->YUI->ColorPicker("color");
    $this->app->YUI->AutoComplete("adresse","adresse");
    $this->app->YUI->AutoComplete("adresseintern","adresse");
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("datum_bis");
    
    $projektabgeschlossen = false;
    if($this->app->DB->Select("SELECT id FROM projekt WHERE id = '$id' AND status = 'abgeschlossen' LIMIT 1"))$projektabgeschlossen = true;
    if($projektabgeschlossen)
    {
      $this->app->Tpl->Set('VORSPEICHERN','/*');
      $this->app->Tpl->Set('NACHSPEICHERN','*/');
      $this->app->Tpl->Set('EDITABLE', 'false');
    }else{
      $this->app->Tpl->Set('EDITABLE', 'true');
    }
    
    $this->app->Tpl->Parse('TAB1',"projekt_dashboard_zeitplanung.tpl");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  }

  function ProjektTagebuch()
  {
    $this->ProjektMenu();

    $this->app->Tpl->Parse('PAGE',"projekt_tagebuch.tpl");
  }

  function ProjektuebersichtMinidetail(){
    $id = $this->app->Secure->GetGET('id');

    $arbeitsschritttabelle = new EasyTable($this->app);
    $arbeitsschritttabelle->Query("SELECT p.belegnr AS produktion, pa.sort AS nr, pa.name AS arbeitsanweisung, ag.bezeichnung, 
                    a.name AS 'geplanter Mitarbeiter', 
                    CONCAT(
                    IF(pa.einzelzeit >= 3600,
                    CONCAT(FLOOR(pa.einzelzeit / 3600),':',
                    IF( MOD( pa.einzelzeit,3600)<360,'0','')),''
                    ), 
                    FLOOR(MOD(pa.einzelzeit,3600)/60 ),
                    ':',
                    IF(MOD(pa.einzelzeit,60) < 10,'0',''),
                    MOD(pa.einzelzeit,60), ' min'
                    ) as einzelzeit, pa.status 
                   FROM produktion_arbeitsanweisung pa
                   LEFT JOIN adresse a ON pa.geplanter_mitarbeiter = a.id
                   LEFT JOIN arbeitsplatzgruppen ag ON pa.arbeitsplatzgruppe = ag.id  
                   JOIN produktion p ON pa.produktion = p.id 
                   WHERE pa.status = 'gestartet' AND p.projekt = '$id' AND p.status = 'gestartet'
                   ORDER BY p.id, pa.sort");
    $arbeitsschritttabelle->DisplayNew("ARBEITSSCHRITTE","Status","noAction");


    $aufgabentabelle = new EasyTable($this->app);
    $aufgabentabelle->Query("SELECT a.aufgabe, ap.aufgabe AS teilprojekt, adr.name as mitarbeiter, 
                      if(a.abgabe_bis,DATE_FORMAT(abgabe_bis,'%d.%m.%Y'),'') as 'Abgabe-Termin', a.status
                    FROM  aufgabe a 
                    LEFT JOIN projekt p ON p.id=a.projekt 
                    LEFT JOIN adresse adr ON a.adresse=adr.id 
                    LEFT JOIN arbeitspaket ap ON ap.id=a.teilprojekt
                    WHERE a.projekt = '$id' AND a.status = 'offen'");
    $aufgabentabelle->DisplayNew("AUFGABEN", "Status", "noAction");


    $where = '';
    for($i=1;$i<=10;$i++){
      $where .= " (name = 'projektfreifeld".$i."' AND IFNULL((SELECT 1 FROM firmendaten_werte WHERE name = 'projektfreifeld".$i."spalte' AND wert > 0 LIMIT 1),0)) OR";
    }
    $where = rtrim($where, " OR");

    $freifeldbez = $this->app->DB->SelectArr("SELECT wert, name FROM firmendaten_werte WHERE".$where);
    $freifelder = array();

    foreach ($freifeldbez as $value){
      $freifelder[$value['name']] = $value['wert'];
    }

    for ($i=1;$i<=count($freifelder);$i++) {
      if($freifelder['projektfreifeld'.$i] === ''){
        $freifelder['projektfreifeld'.$i] = 'Freifeld '.$i;
      }
    }

    $freifeldtable = "<table>";
    for($i=1;$i<=10;$i++){
      if(array_key_exists('projektfreifeld'.$i, $freifelder)){
        $freifeldbla = $this->app->DB->Select("SELECT freifeld$i FROM projekt WHERE id = '$id' LIMIT 1");
        $freifeldtable .= "<tr>";
        $freifeldtable .= "<td><b>".$freifelder['projektfreifeld'.$i]."</b>:</td><td>".$freifeldbla."</td>";
        $freifeldtable .= "</tr>";
      }
    }
    $freifeldtable .= "</table>";

    $this->app->Tpl->Set("PROJEKTFREIFELDER", $freifeldtable);

    $this->app->Tpl->Output("projektuebersicht_minidetail.tpl");
    exit;
  }


  function ProjektEdit()
  {
    $this->ProjektMenu();
    $id = $this->app->Secure->GetGET("id");

    $cmd = $this->app->Secure->GetPOST("cmd");
    $mode = $this->app->Secure->GetPOST("mode");

    if($cmd=="setup" && $mode > 0 && $id > 0)
    {
      $this->ProjektSetup($id,$mode);
      echo json_encode('success');
      $this->app->ExitXentral();
    }


    $this->app->Tpl->Set('TMPSCRIPT',"");


    $allowed = "/[^a-zA-Z0-9._-]/";      
    $this->app->Secure->POST["abkuerzung"] = preg_replace($allowed,"",$this->app->Secure->POST["abkuerzung"]); 
    $this->app->Secure->POST["abkuerzung"]=substr(strtoupper($this->app->Secure->POST["abkuerzung"]),0,20);


    $abkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$id' LIMIT 1");
    if($abkuerzung=="")
    {
      $tmp_abkuerzung = $this->app->DB->Select("SELECT MAX(abkuerzung) FROM projekt");
      $tmp_abkuerzung = $this->app->erp->CalcNextNummer($tmp_abkuerzung);
      $this->app->DB->Update("UPDATE projekt SET abkuerzung='$tmp_abkuerzung' WHERE id='$id' LIMIT 1");
    }

    parent::ProjektEdit();

    if($this->app->Secure->GetPOST("speichern")!="")
    {
      if($this->app->Secure->GetGET("msg")=="")
      {
        $msg = $msg.$this->app->Tpl->Get('MESSAGE');
        $msg = base64_encode($msg);
      } else {
        $msg = base64_encode($msg);
      }

      $this->app->Location->execute("index.php?module=projekt&action=edit&id=$id&msg=$msg");
      exit;
    }

  }


  function ProjektSetup($project,$mode)
  {
    $data['kommissionierverfahren']="lieferscheinlager"; //OK
    $data['multiorderpicking']=0; //OK
    $data['autodruckrechnungstufe1']=0; //OK
    $data['kommissionierlauflieferschein']=0; //OK
    $data['autodruckrechnungstufe1mail']=0; //OK
    $data['exportdruckrechnungstufe1']=0; //OK
    $data['autodruckkommissionierscheinstufe1']=0; //OK
    $data['kommissionierlistestufe1']=0; //OK
    $data['rechnungerzeugen']=0; //OK
    $data['lieferscheinedrucken']=0; //OK
    $data['auftragdrucken']=0; //OK
    $data['paketmarkedrucken']=0; //OK
    $data['autodruckrechnung']=0; //OK
    $data['exportdruckrechnung']=0; //OK
    $data['print_proformainvoice']=0; //OK
    $data['automailversandbestaetigung']=0; //OK
    $data['automailrechnung']=0; //OK
    $data['autodrucklieferschein']=0; //OK
    $data['autodruckanhang']=0; //OK
    $data['paketmarkeautodrucken']=0; //OK
    $data['autodruckrechnungdoppel']=0; //OK
    $data['druckennachtracking']=0; //OK

    $data['autodruckrechnungstufe1menge']=1; //OK
    $data['exportdruckrechnungstufe1menge']=3; //OK
    $data['autodruckkommissionierscheinstufe1menge']=1; //OK
    $data['kommissionierlistestufe1menge']=1; //OK
    $data['lieferscheinedruckenmenge']=1; //OK
    $data['auftragdruckenmenge']=1; //OK
    $data['autodruckrechnungmenge']=1; //OK
    $data['exportdruckrechnungmenge']=3; //OK
    $data['proformainvoice_amount']=3; //OK
    $data['autodrucklieferscheinmenge']=1; //OK

    switch($mode)
    {
      case 1: 
        $data['kommissionierverfahren']="lieferscheinlager"; //OK
        $data['autodruckrechnungstufe1mail']=1; //OK
        $data['lieferscheinedrucken']=1; //OK
        $data['paketmarkedrucken']=1; //OK
      break;
      
      case 2: 
        $data['kommissionierverfahren']="lieferscheinscan"; //OK
        $data['kommissionierlistestufe1']=1; //OK
        $data['rechnungerzeugen']=1; //OK
        $data['automailversandbestaetigung']=1; //OK
        $data['automailrechnung']=1; //OK
        $data['autodrucklieferschein']=1; //OK
        $data['paketmarkeautodrucken']=1; //OK
        $data['druckennachtracking']=1; //OK
      break;
 
      case 3: 
        $data['kommissionierverfahren']="lieferscheinscan";
        $data['multiorderpicking']=1; //OK
        $data['rechnungerzeugen']=1; //OK
        $data['automailversandbestaetigung']=1; //OK
        $data['automailrechnung']=1; //OK
        $data['autodrucklieferschein']=1; //OK
        $data['paketmarkeautodrucken']=1; //OK
        $data['druckennachtracking']=1; //OK
      break;
 
      case 4: 
        //$data['kommissionierverfahren']="";
      break;
    }

    foreach($data as $name => $value)
    {
      $this->app->DB->Update("UPDATE projekt SET $name='$value' WHERE id='$project' LIMIT 1");
    }
    return;
  }

  function ProjektLogbuch()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $id = (int)$this->app->Secure->GetGET('id');

    if($cmd == 'goto'){
      $antwort = array('success' => 0, 'data' => 'Rechte für Vorgang nicht ausreichend');
      $art = $this->app->Secure->GetGET('art');

      switch ($art) {
        case '1':
        case '2':
        case '4':
          if($this->app->erp->RechteVorhanden('adresse', 'brief')){
            if($art == 2){
              $adressid = $this->app->DB->Select("SELECT adresse FROM dokumente_send WHERE id = '$id' LIMIT 1");
            }else{
              $adressid = $this->app->DB->Select("SELECT adresse_to FROM dokumente WHERE id = '$id' LIMIT 1"); // evtl. nur bei 1 bei notiz
            }

            $antwort['data'] = "?module=adresse&action=brief&id=$adressid";
            $antwort['success'] = 1;
          }
          break;
        case '5':
          $adressid = $this->app->DB->Select("SELECT adresse FROM wiedervorlage WHERE id = '$id' LIMIT 1");
          if($adressid > 0){
            if($this->app->erp->RechteVorhanden('adresse', 'brief')){
              $antwort['data'] = "?module=adresse&action=brief&id=$adressid";
              $antwort['success'] = 1;
            }
          }else{
            if($this->app->erp->RechteVorhanden('wiedervorlage', 'list')){
              $antwort['data'] = "?module=wiedervorlage&action=list";
              $antwort['success'] = 1;
            }
          }
          break;
        case '3':
        case '33':
          if($this->app->erp->RechteVorhanden('ticket', 'assistent')){
            $antwort['data'] = "?module=ticket&action=assistent&id=$id";
            $antwort['success'] = 1;
          }
          break;
        case '6':
          if($this->app->erp->RechteVorhanden('kalender', 'list')){
            $antwort['data'] = "?module=kalender&action=list";
            $antwort['success'] = 1;
          }
          break;
        default:
          $antwort['data'] = "Unbekannte Vorgangsart";
          break;
      }

      echo json_encode($antwort);
      exit;
    }


    //$this->app->Tpl->Set('ID',$id);


  }


  function ProjektMiniDetailBrief($parsetarget = "", $menu = true)
  {
    $doppelteids = $this->app->Secure->GetGET("id");
    $ids = preg_split('/\-/',$doppelteids);
    if(count($ids) > 1)
    {
      $typ = (int)$ids[0];
      $id = (int)$ids[1];
      switch($typ)
      {
        case '1':
          $this->ProjektBriefPreview('dokumente',$id,false);
          break;
        case '2':
          $this->ProjektBriefPreview('dokumente_send',$id,false);
          break;
        case '5':
          $this->ProjektBriefPreview('wiedervorlage',$id,false);
          break;
        case '6':
          $this->ProjektBriefPreview('kalender',$id,false);
          break;
      }
    }
    exit;
  }
  function ProjektBriefPreview($type = '', $id = '', $json = true) {
    if(!$type)$type = $this->app->Secure->GetGET('type');
    if(!$id)$id = $this->app->Secure->GetGET('id');

    $query = '';
    if ($type) {
      switch ($type) {
        case 'dokumente':
          $query .= '
            SELECT
            id,
            DATE_FORMAT(datum, "%d.%m.%Y") as datum,
            betreff,
            content
              FROM
              dokumente
              WHERE
              id = ' . $id . '
              ';
          break;
        case 'dokumente_send':
          $query .= '
            SELECT
            id,
            DATE_FORMAT(zeit, "%d.%m.%Y") as datum,
            betreff,
            text as content
              FROM
              dokumente_send
              WHERE
              id = ' . $id . '
              ';
          break;
        case 'wiedervorlage':
          $query .= '
            SELECT
            id,
            DATE_FORMAT(datum_angelegt, "%d.%m.%Y") as datum,
            DATE_FORMAT(datum_erinnerung, "%d.%m.%Y") as datum_erinnerung,
            DATE_FORMAT(zeit_angelegt,"%H:%i") as zeit_angelegt,
            zeit_erinnerung,
            bezeichnung as betreff,
            beschreibung as content
              FROM
              wiedervorlage
              WHERE
              id = ' . $id . '
              ';
          break;
        case 'kalender':
          $query .= '
            SELECT
            id,
            DATE_FORMAT(von, "%d.%m.%Y") as datum,
            DATE_FORMAT(bis, "%d.%m.%Y") as datumbis,
            DATE_FORMAT(von, "%H:%i:%s") as uhrzeit,
            DATE_FORMAT(bis, "%H:%i:%s") as uhrzeitbis,
            allDay,
            public,
            adresseintern,
            bezeichnung as betreff,
            beschreibung as content
              FROM
              kalender_event
              WHERE
              id = ' . $id . '
              ';
          break;
      }

    }

    $res = $this->app->DB->SelectArr($query);
    if ($res) {
      $res = reset($res);
      /*
          if (isset($res['content'])) {
            $res['content'] = '<input type="button" onclick="briefDrucken(' . $res['id'] . ');" value="Drucken"><br><br>'.nl2br($res['content']);
          }
      */
    }
    if(isset($res['datum']) && !$tickets)
    {
      $res['content'] = '<b>Angelegt am: '.$res['datum'].(isset($res['zeit_angelegt'])?' '.$res['zeit_angelegt']:'')."</b><br />".$res['content'];
    }

    if($json)
    {
      echo json_encode($res);
    } else {
      //$ausg = "<h2>".$res['datum'].' '.$res['betreff']."</h2>".nl2br($res['content']);
      if($res['content']=="") $res['content']="Kein Inhalt vorhanden";

      if($this->app->DB->Select("SELECT COUNT(id) FROM datei_stichwoerter ds WHERE ds.subjekt='anhang' AND ds.objekt='dokument' AND ds.parameter='$id'") > 0 && $this->app->erp->RechteVorhanden("dateien","send"))
      {
        $table = new EasyTable($this->app);
        $table->Query("SELECT d.titel,ds.id FROM datei_stichwoerter ds LEFT JOIN datei d ON d.id=ds.datei WHERE ds.subjekt='anhang' AND ds.objekt='dokument' AND ds.parameter='$id' ORDER by ds.sort");
        $res['content'] .= $table->DisplayNew('return',"<a href=\"index.php?module=dateien&action=send&id=%value%\"><img src=\"./themes/new/images/download.svg\"></a>");
      }

      if($this->app->erp->isHTML($res['content']))
        $ausg = $res['content'];
      else
        $ausg = nl2br($res['content']);
      echo $ausg;
    }
    exit;
  }

  function ProjektDashboardMinidetailWiedervorlagen(){
    /** @var wiedervorlage $obj */
    $obj=$this->app->erp->LoadModul('wiedervorlage');
    if(!empty($obj))
    {
      $obj->WiedervorlageMinidetail();
    }else {
      $this->app->ExitXentral();
    }
  }


}
