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
include '_gen/adresse.php';

class Adresse extends GenAdresse {
  /** @var Application $app */
  var $app;
  const MODULE_NAME = 'Address';

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public static function TableSearch($app, $name, $erlaubtevars)
  {
    $id = $app->Secure->GetGET('id');
    switch($name)
    {
      case 'lieferantartikel':
        $allowed['adresse'] = array('lieferantartikel');

        // START EXTRA checkboxen
        $app->Tpl->Add('JQUERYREADY', "$('#offen').on('click', function() { fnFilterColumn1( 0 ); } );");
        for ($r = 1;$r < 2;$r++) {
          $app->Tpl->Add('JAVASCRIPT', '
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

        // headings
        $heading = array('Nummer', 'Artikel', 'Verkauf', 'LA', 'AB', 'BE', 'Reserv.', 'Fehlende', 'Gesamt', 'aktueller Monate', 'letzter Monat', 'Status', 'Men&uuml;');
        $width = array('5%', '40%', '5%', '5%', '5%', '5%', '5%', '5%', '5%', '5%', '5%', '5%', '15%');

        $findcols = array('nummer', 'name_de', 'verkauf', 'CAST(`lager` as SIGNED)', 'CAST(`offen` as SIGNED)', 'CAST(`bestellung` as SIGNED)', 'CAST(`res` as SIGNED)', 'CAST(`fehlende` as SIGNED)', 'CAST(`gesamt` as SIGNED)', 'CAST(`monat` as SIGNED)', 'CAST(`monat_last` as SIGNED)', 'status', 'id');

        $searchsql = array('a.name_de', "IFNULL((SELECT e.bestellnummer FROM einkaufspreise e WHERE e.artikel=a.id AND e.adresse='$id' AND e.geloescht!=1 AND e.bestellnummer!='' LIMIT 1),'')", 'a.nummer');
        $menu = "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/copy.svg\" border=\"0\"></a>";
        $aktuellermonat = $app->DB->Select("SELECT CONCAT(YEAR(NOW()),'-',MONTH(NOW()))");
        $letztermonat = $app->DB->Select("SELECT CONCAT(YEAR( DATE_SUB( NOW() , INTERVAL 1 MONTH )),'-',DATE_FORMAT( DATE_SUB( NOW() , INTERVAL 1 MONTH ) ,'%m'))");
        $alignright = array(4,5,6,7,8,9,10,11);
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, 

              CONCAT(if( (SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') > IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) + IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0)

                    , CONCAT('<font color=red><b>',a.name_de,'</b></font>'),a.name_de),'<br>Best-Nr.:',IFNULL((SELECT e.bestellnummer FROM einkaufspreise e WHERE e.artikel=a.id AND e.adresse='$id' AND e.geloescht!=1 AND e.bestellnummer!='' ORDER by e.id DESC LIMIT 1),'-'),'<br>Letzter EK-Preis: ',IFNULL((SELECT ".$app->erp->FormatPreis("e.preis",2)." FROM einkaufspreise e WHERE e.artikel=a.id AND (e.gueltig_bis > NOW() OR e.gueltig_bis='0000-00-00') AND e.geloescht!=1 AND e.preis >= 0 ORDER by e.id DESC LIMIT 1),''),' ab Menge ',IFNULL((SELECT ".$app->erp->FormatMenge("e.ab_menge")." FROM einkaufspreise e WHERE e.artikel=a.id AND (e.gueltig_bis > NOW() OR e.gueltig_bis='0000-00-00') AND e.geloescht!=1 AND e.preis >= 0 ORDER by e.id DESC LIMIT 1),'')) as name, 


              ifnull((SELECT DATE_FORMAT(MAX(auftrag.datum), '%d.%m.%Y') FROM auftrag LEFT JOIN
                    auftrag_position ON auftrag.id=auftrag_position.auftrag WHERE auftrag_position.artikel=a.id
                    ),0) as verkauf,



              ifnull(if( (SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') > IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) + IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0)
                    ,
                    CONCAT('<font color=red>',if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0,(SELECT ".$app->erp->FormatMenge("SUM(l.menge)")." FROM lager_platz_inhalt l WHERE l.artikel=a.id),'-'),'</font>'),

                    if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0,(SELECT ".$app->erp->FormatMenge("SUM(l.menge)")." FROM lager_platz_inhalt l WHERE l.artikel=a.id),'-')),0)
                as lager,

              ifnull((SELECT ".$app->erp->FormatMenge("SUM(ap.menge)")." FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben'),0) as offen,

              ifnull((SELECT ".$app->erp->FormatMenge("SUM(bp.menge-bp.geliefert)")." FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0) as bestellung,

              ifnull((if((SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id) > 0,(SELECT ".$app->erp->FormatMenge("SUM(l.menge)")." FROM lager_reserviert l WHERE l.artikel=a.id),'-')),0) as res,

              ifnull(IF((SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') - ( IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) + IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0)) > 0,".$app->erp->FormatMenge("(SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') - ( IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) + IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0))").",'-'),0) as fehlende,



              ifnull(if((SELECT SUM(ap.menge) FROM auftrag_position ap WHERE ap.artikel=a.id) > 0, (SELECT ".$app->erp->FormatMenge("SUM(ap.menge)")." FROM auftrag_position ap WHERE ap.artikel=a.id),'-'),0) as gesamt,
              ifnull((SELECT ".$app->erp->FormatMenge("SUM(ap.menge)")." FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND DATE_FORMAT(auf.datum,'%Y-%m')='$aktuellermonat'),0) as monat,
              ifnull((SELECT ".$app->erp->FormatMenge("SUM(ap.menge)")." FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND DATE_FORMAT(auf.datum,'%Y-%m')='$letztermonat'),0) as monat_last,

              ifnull(if( (SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') > IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0)+IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0), 'fehlt','ok'),'') as status, 
              a.id as menu
                FROM artikel a ";

        // START EXTRA more
        $more_data1 = $app->Secure->GetGET("more_data1");

        if ($more_data1 == 1) $subwhere[] = " (((SELECT MAX(auftrag.datum) FROM auftrag LEFT JOIN
              auftrag_position ON auftrag.id=auftrag_position.auftrag WHERE auftrag_position.artikel=a.id
                ) < DATE_SUB(NOW(),INTERVAL 6 MONTH)) AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0 )";
        $csubwhere = isset($subwhere)?count($subwhere):0;
        $tmp = '';
        for ($j = 0;$j < $csubwhere;$j++) {
          $tmp.= " AND " . $subwhere[$j];
        }

        // fester filter
        $where = " (a.adresse='$id' OR a.id IN(SELECT artikel FROM einkaufspreise WHERE adresse='$id' AND (gueltig_bis>CURRENT_DATE() OR gueltig_bis='0000-00-00'))) AND a.geloescht!=1 $tmp";
        $count = "SELECT COUNT(a.id) FROM artikel a WHERE $where";

        break;
      case 'adressealleabos':
        $allowed['adresse'] = array('artikel');
        $heading = array('Bezeichnung', 'Nummer', 'Start am', 'Abgerechnet bis', 'Enddatum', 'Preis','Rabatt', 'Währung', 'Menge', 'Art', 'Dokument','Gruppe / Reihenfolge', 'Men&uuml;');
        $width = array('10%', '6%', '10%', '10%', '10%', '6%','3%', '4%', '2%', '5%', '5%','15%', '8%');

        $findcols = array('aa.bezeichnung', 'ar.nummer', "if(aa.startdatum!='0000-00-00',DATE_FORMAT(aa.startdatum,'%d.%m.%Y') ,if(aa.angelegtam='0000-00-00','-',DATE_FORMAT(aa.angelegtam,'%d.%m.%Y')))", "if(aa.abgerechnetbis='0000-00-00','-',aa.abgerechnetbis)", "if(aa.enddatum='0000-00-00','-',aa.enddatum)", 'aa.preis','aa.rabatt', 'aa.waehrung', 'aa.menge',
          "if(aa.wiederholend=1 OR aa.preisart='monat' OR aa.preisart='jahr' OR aa.preisart='monatx' OR aa.preisart='wochen',aa.preisart,
          if(aa.preisart = '30tage', 
          '30 Tage',
          if(aa.preisart = '360tage', '360 Tage', 'einmalig')
        )
          )",
          'aa.dokument',"CONCAT(IFNULL(ag.beschreibung,''),aa.sort)", 'id');
        $searchsql = array('aa.bezeichnung', 'ar.nummer', "if(aa.startdatum!='0000-00-00',DATE_FORMAT(aa.startdatum,'%d.%m.%Y') ,if(aa.angelegtam='0000-00-00','-',DATE_FORMAT(aa.angelegtam,'%d.%m.%Y')))", "if(aa.abgerechnetbis='0000-00-00','-',DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y'))", "if(aa.enddatum='0000-00-00','-',DATE_FORMAT(aa.enddatum,'%d.%m.%Y'))", $app->erp->FormatPreis("aa.preis"),'aa.rabatt', 'aa.waehrung', $app->erp->FormatMenge("aa.menge"),
          "if(aa.wiederholend=1 OR aa.preisart='monat' OR aa.preisart='jahr' OR aa.preisart='monatx' OR aa.preisart='wochen',aa.preisart,
          if(aa.preisart = '30tage', 
          '30 Tage',
          if(aa.preisart = '360tage', '360 Tage', 'einmalig')
        )
          )",
          'ag.beschreibung', 'aa.dokument');

        $numbercols = array(5,8);
        $datecols = array(3,4);
        $alignright = array(6,7,8,9);
        $defaultorder = 12;
        $defaultorderdesc = 0;

        $nurAktive = $app->YUI->TableSearchFilter($name, 5, 'nur_aktive_abos', '0', 0, 'checkbox');
        $subwhere = '';
        if($nurAktive){
          $subwhere = " AND (aa.enddatum = '0000-00-00' OR aa.enddatum >= CURDATE())";
        }

        $id = $app->Secure->GetGET('id');

        $menu = "<table>";
        $menu .= "<tr>";
        $menu .= "<td nowrap>";
        $menu .= "<a onclick=\"AboartikelEdit(%value%,$id)\" href=\"#\" title=\"Artikel &auml;ndern\"><img border=\"0\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\"></a>&nbsp;";
        $menu .= "<a onclick=DeleteDialog(\"index.php?module=adresse&action=delartikel&sid=%value%&id=$id\"); href=\"#\" ><img border=\"0\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\"></a>&nbsp;";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        $where = " aa.adresse='$id'".$subwhere;

        $sql = "SELECT SQL_CALC_FOUND_ROWS aa.id, aa.bezeichnung, ar.nummer, if(aa.startdatum!='0000-00-00',DATE_FORMAT(aa.startdatum,'%d.%m.%Y') ,if(aa.angelegtam='0000-00-00','-',DATE_FORMAT(aa.angelegtam,'%d.%m.%Y'))) as 'angelegtam', if(aa.abgerechnetbis='0000-00-00','-',DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y')) as 'abgerechnetbis', 
      if(aa.enddatum='0000-00-00','-',DATE_FORMAT(aa.enddatum,'%d.%m.%Y')) as enddatum,
      ".$app->erp->FormatPreis("aa.preis")." as preis, aa.rabatt, aa.waehrung, ".$app->erp->FormatMenge("aa.menge")." as menge, 
      if(
        aa.wiederholend=1 OR aa.preisart='monat' OR aa.preisart='jahr' OR aa.preisart='monatx' OR aa.preisart='wochen',
        aa.preisart,
        if(aa.preisart = '30tage', 
          '30 Tage',
          if(aa.preisart = '360tage', '360 Tage', 'einmalig')
        )
      ),
      aa.dokument,CONCAT(IFNULL(ag.beschreibung,'-'), ' / ', if(ag.id > 0,' ',''),aa.sort, ''), aa.id as id
      FROM abrechnungsartikel aa LEFT JOIN artikel ar ON ar.id=aa.artikel LEFT JOIN user u ON u.id=aa.angelegtvon LEFT JOIN adresse au ON au.id=u.adresse
      LEFT JOIN abrechnungsartikel_gruppe ag ON aa.gruppe=ag.id";
        $count = "SELECT COUNT(aa.id) FROM abrechnungsartikel aa LEFT JOIN artikel ar ON ar.id = aa.artikel LEFT JOIN user u ON u.id = aa.angelegtvon LEFT JOIN adresse au ON au.id = u.adresse WHERE $where";

        break;
      case 'adressealleabossort':
        $allowed['adresse'] = array('artikel');
        $heading = array('Bezeichnung', 'Nummer', 'Preis','Gruppe', 'Reihenfolge', 'Men&uuml;');
        $width = array('10%', '10%', '10%', '10%', '10%', '8%');

        $findcols = array('aa.bezeichnung', 'ar.nummer', 'preis','ag.beschreibung','aa.sort', 'id');
        $searchsql = array('aa.bezeichnung', 'ar.nummer', 'preis','ag.beschreibung');

        $alignright = array(6);
        $defaultorder = 4;
        $defaultorderdesc = 0;

        $id = $app->Secure->GetGET('id');

        $menu = "<table>";
        $menu .= "<tr>";
        $menu .= "<td nowrap>";
        $menu .= "<a onclick=\"AboartikelMove(%value%,1)\" href=\"#\"><img border=\"0\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/up.png\"></a>";
        $menu .= "<a onclick=\"AboartikelMove(%value%,0)\" href=\"#\"><img border=\"0\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/down.png\"></a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        $where = " aa.adresse='$id'";

        $orderby = "ORDER BY aa.gruppe ASC, aa.sort ASC";

        $sql = "SELECT SQL_CALC_FOUND_ROWS aa.id, 
        aa.bezeichnung, 
        ar.nummer,
      ".$app->erp->FormatPreis("aa.preis")." as preis, 
      IFNULL(ag.beschreibung,'-') as gruppe, 
      aa.sort, 
      aa.id as id
      FROM abrechnungsartikel aa LEFT JOIN artikel ar ON ar.id=aa.artikel LEFT JOIN user u ON u.id=aa.angelegtvon LEFT JOIN adresse au ON au.id=u.adresse
      LEFT JOIN abrechnungsartikel_gruppe ag ON aa.gruppe=ag.id";
        $count = "SELECT COUNT(aa.id) FROM abrechnungsartikel aa LEFT JOIN artikel ar ON ar.id = aa.artikel LEFT JOIN user u ON u.id = aa.angelegtvon LEFT JOIN adresse au ON au.id = u.adresse WHERE $where";

        break;
      case 'abrechnungsartikel_gruppe':
        $allowed['adresse'] = array('artikel');
        $heading = array('Bezeichnung','Reihenfolge','Ansprechpartner','Rechnung','Ziel-Projekt', 'Men&uuml;');
        $width = array('30%','5%', '30%','10%','15%', '8%');

        $findcols = array('ag.beschreibung','ag.sort', 'ag.ansprechpartner', "if(ag.extrarechnung=0,'Gemeinsame Rechnung', if(ag.extrarechnung=1,'Eigene Rechnung', if(ag.extrarechnung=2, 'Sammelrechnung', '-')))",'p.abkuerzung', 'ag.id');
        $searchsql = array('ag.beschreibung','ag.sort', 'ag.ansprechpartner', "if(ag.extrarechnung=0,'Gemeinsame Rechnung', if(ag.extrarechnung=1,'Eigene Rechnung', if(ag.extrarechnung=2, 'Sammelrechnung', '-')))",'p.abkuerzung');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $id = $app->Secure->GetGET('id');

        $menu = "<table class=\"nopadding\" cellpadding=\"0\" cellspacing=\"0\">";
        $menu .= "<tr>";
        $menu .= "<td>";
        $menu .= "<a onclick=\"AbogruppeEdit(%value%)\" href=\"#\" title=\"Artikel &auml;ndern\">";
        $menu .= "<img border=\"0\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "<td>";
        $menu .= "<a onclick=AbogruppeDelete(%value%); href=\"#\" >";
        $menu .= "<img border=\"0\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        $where = " ag.adresse='$id' ";

        $sql = "SELECT SQL_CALC_FOUND_ROWS ag.id, ag.beschreibung,if(ag.sort<=0,'-',ag.sort),ag.ansprechpartner,if(ag.extrarechnung=0,'Gemeinsame Rechnung', if(ag.extrarechnung=1,'Eigene Rechnung', if(ag.extrarechnung=2, 'Sammelrechnung', '-'))), p.abkuerzung, ag.id FROM abrechnungsartikel_gruppe ag
          LEFT JOIN projekt p ON p.id=ag.projekt";

        $count = "SELECT COUNT(ag.id) FROM abrechnungsartikel_gruppe ag WHERE $where";


        break;
      case 'abosammelrechnungen':
        $allowed['adresse'] = array('artikel');
        $heading = array('Bezeichnung', 'abweichende Rechnungsadresse', 'Ziel-Projekt', 'Men&uuml;');
        $width = array('30%', '30%', '30%', '1%');

        $findcols = array('abs.bezeichnung', 'a.name', 'p.abkuerzung', 'abs.id');
        $searchsql = array('abs.bezeichnung', 'a.name', 'p.abkuerzung');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $id = $app->Secure->GetGET('id');

        $menu = "<table cellpadding=0 cellspacing=0>";
        $menu .= "<tr>";
        $menu .= "<td nowrap>";
        $menu .= '<a href="javascript:;" onclick="AboSammelrechnungEdit(%value%);">';
        $menu .= "<img border=\"0\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">";
        $menu .= "</a>&nbsp;";
        $menu .= '<a href="javascript:;" onclick="AboSammelrechnungDelete(%value%);">';
        $menu .= "<img border=\"0\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">";
        $menu .= "</a>";
        $menu .= "</td>";
        $menu .= "</tr>";
        $menu .= "</table>";

        $where = " abs.adresse='$id' ";

        $sql = "SELECT SQL_CALC_FOUND_ROWS abs.id, abs.bezeichnung, a.name, p.abkuerzung, abs.id FROM adresse_abosammelrechnungen abs LEFT JOIN adresse a ON abs.abweichende_rechnungsadresse = a.id LEFT JOIN projekt p ON abs.projekt = p.id";

        $count = "SELECT COUNT(abs.id) FROM adresse_abosammelrechnungen abs WHERE $where";
        break;

      case 'abrechnungsartikel':
        $allowed['adresse'] = array('artikel');
        $heading = array('Artikel', 'Nummer', 'Ab', 'Preis','Waehrung', 'Projekt', 'Startdatum', 'Menge', 'Aktion');
        $width = array('20%', '10%', '2%', '8%', '5%','1%', '10%', '10%', '10%');
        $findcols = array('a.name_de', 'a.nummer', 'ab', 'v.preis','v.waehrung','projekt', 'lieferdatum', 'menge', 'v.id');
        $searchsql = array('a.name_de', 'a.nummer');
        $id = $app->Secure->GetGET('id');
        $alignright=array(4,5);
        $menu = '<center><input type="button" value="anlegen" onclick="anlegen(' . $id . ',%value%)"></center>';
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,CONCAT(a.name_de,' ',if(v.adresse='$id','(Kundensp. Preis)',''), if(v.gruppe > 0,CONCAT('(Gruppenpreis ',g.name,' ',g.kennziffer,')'),'')), CONCAT('<a href=\"index.php?module=artikel&action=verkauf&id=',a.id,'\" target=\"_blank\">',a.nummer,'</a>'), ".$app->erp->FormatMenge("v.ab_menge")." AS ab, ".$app->erp->FormatPreis("v.preis").",v.waehrung, p.abkuerzung as projekt,
              CONCAT('<center><input type=\"text\" size=\"10\" value=\"',DATE_FORMAT(NOW(),'%d.%m.%Y'),'\" id=\"datum',v.id,'\"></center>') AS lieferdatum,
              CONCAT('<center><input type=\"text\" size=\"3\" value=\"\" id=\"menge',v.id,'\"><select name=\"art',v.id,'\" id=\"art',v.id,'\"><option value=\"abo\">Abo</option><option value=\"einmalig\">Einmalig</option></select></center>') AS menge,
              v.id 
                FROM artikel AS a 
                LEFT JOIN verkaufspreise AS v ON v.artikel=a.id
                LEFT JOIN projekt AS p ON p.id=v.projekt LEFT JOIN gruppen g ON g.id=v.gruppe";
        $where = " v.ab_menge>0 AND a.geloescht!=1 AND (v.gueltig_bis ='0000-00-00' OR v.gueltig_bis >= DATE_FORMAT(NOW(),'%Y-%m-%d'))  AND (v.adresse='$id' or v.adresse<=0) ";
        $count = "SELECT COUNT(a.id) FROM artikel AS a 
              LEFT JOIN verkaufspreise AS v ON v.artikel=a.id
              LEFT JOIN projekt AS p ON p.id=v.projekt
              WHERE $where";
        break;
      case 'adresse_ansprechpartnergruppen':
      //$doctype = str_replace('belegeinauslagern_bewegungen_','',$name);
      $id = $app->Secure->GetGET('id');
      $lid = $app->YUI->TableSearchFilter($name, 1, 'filterlid');
      $allowed['adresse'] = array('ansprechpartner');
      $heading = array('','Gruppe','Kategorie','');
      $width = array('1%','49%','49','1%');
      $findcols = array('ag.aktiv','gr.name','gk.bezeichnung','gr.id');
      $searchsql = array('gr.name','gk.bezeichnung');
      $defaultorder = 2;
      $defaultorderdesc = 0;
      $disabled = "";
      //if(!$lid)$disabled = "disabled ";
      $sql= "SELECT SQL_CALC_FOUND_ROWS gr.id ,
      concat('<input type=\"checkbox\" $disabled onchange=\"grchange(',gr.id,',this,$lid);\" id=\"gr_',gr.id,'\"  name=\"gr_',gr.id,'\" ',(if(ifnull(ag.aktiv,0) <> 0,' checked ','')),' />') as auswahl,
      gr.name, gk.bezeichnung, gr.id
      FROM gruppen gr 
      LEFT JOIN ansprechpartner_gruppen ag ON gr.id = ag.gruppe and ag.ansprechpartner = '$lid'  
      LEFT JOIN gruppen_kategorien gk ON gr.kategorie = gk.id
      ";
      $where = " gr.art != 'preisgruppe' ";
      $maxrows = 50;
      $disablebuttons = true;
      break;

      case 'adresse_ustprf':
        $allowed['adresse'] = array('ustprf');

        $heading = array('Datum', 'Firma', 'Stra&szlig;e', 'PLZ', 'Ort', 'Land', 'Brief', 'Status', 'Men&uuml;');
        $width = array('8%', '25%', '25%', '5%', '10%', '5%', '3%', '5%', '1%');

        $findcols = array('u.datum', 'u.name', 'u.strasse', 'u.plz', 'u.ort', 'u.land', "if(u.briefbestellt != '0000-00-00', 'ja', '-')", "IF(u.status = 'gueltigmarkieren', 'gültig markiert', status)", 'u.id');
        $searchsql = array("DATE_FORMAT(u.datum, '%d.%m.%Y')", 'u.name', 'u.strasse', 'u.plz', 'u.ort', 'u.land', 'u.briefbestellt', "IF(u.status = 'gueltigmarkieren', 'gültig markiert', status)");

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $menu = '<table cellpadding=0 cellspacing=0>';
          $menu .= '<tr>';
            $menu .= '<td nowrap>';
              $menu .= '<a href="javascript:;" onclick="UstprfEdit(%value%);">';
                $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
              $menu .= '</a>&nbsp;';
              $menu .= '<a href="javascript:;" onclick="UstprfDelete(%value%);">';
                $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
              $menu .= '</a>';
            $menu .= '</td>';
          $menu .= '</tr>';
        $menu .= '</table>';

        $adressid = $app->Secure->GetGET('id');

        $where = " u.id > 0 AND u.adresse = '$adressid'";

        $sql = "SELECT SQL_CALC_FOUND_ROWS u.id, DATE_FORMAT(u.datum, '%d.%m.%Y'), u.name, u.strasse, u.plz, u.ort, u.land, if(u.briefbestellt != '0000-00-00', 'ja', '-'), IF(u.status = 'gueltigmarkieren', 'gültig markiert', IF(u.status = 'online', 'erfolgreich geprüft', u.status)), u.id FROM ustprf u";

        $count = "SELECT COUNT(u.id) FROM ustprf u WHERE $where";
        break;

      case 'adresse_ustprf_protokoll':
        $allowed['adresse'] = array('ustprf');

        $heading = array('Datum / Zeit', 'Bemerkung', 'Bearbeiter', '');
        $width = array('18%', '56%', '25%', '1%');

        $findcols = array('u.zeit', 'u.bemerkung', 'u.bearbeiter', 'u.id');
        $searchsql = array("DATE_FORMAT(u.zeit, '%d.%m.%Y %H:%i:%s')", 'u.bemerkung', 'u.bearbeiter');

        $defaultorder = 1;
        $defaultorderdesc = 1;

        //$ustprfid = 0;
        $ustprfid = $app->Secure->GetGET('more_data1');

        $where = " u.ustprf_id = '$ustprfid'";

        $sql = "SELECT SQL_CALC_FOUND_ROWS u.id, DATE_FORMAT(u.zeit, '%d.%m.%Y %H:%i:%s'), IF(u.daten != '', CONCAT(u.bemerkung, '<br /><i style=color:#999>', u.daten,'</i>'), u.bemerkung), u.bearbeiter, u.id FROM ustprf_protokoll u";

        $count = "SELECT COUNT(u.id) FROM ustprf_protokoll u WHERE $where";
    }

    $erg = [];
    foreach($erlaubtevars as $k => $v) {
      if(isset($$v)){
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  /**
   * Adresse constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern){
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AdresseCreate");
    $this->app->ActionHandler("edit","AdresseEdit");
    $this->app->ActionHandler("getid","AdresseGetid");
    $this->app->ActionHandler("open","AdresseOpen");
    $this->app->ActionHandler("list","AdresseList");
    $this->app->ActionHandler("suche","AdresseSuche");
    $this->app->ActionHandler("delete","AdresseDelete");
    $this->app->ActionHandler("ustprf","AdresseUstprf");
    $this->app->ActionHandler("lieferantvorlage","AdresseLieferantvorlage");
    $this->app->ActionHandler("kundevorlage","AdresseKundevorlage");
    $this->app->ActionHandler("zeiterfassung","AdresseZeiterfassung");
    $this->app->ActionHandler("abrechnungzeit","AdresseAbrechnungzeit");
    $this->app->ActionHandler("abrechnungzeitabgeschlossen","AdresseAbrechnungzeitabgeschlossen");
    $this->app->ActionHandler("abrechnungzeitdelete","AdresseAbrechnungzeitdelete");
    $this->app->ActionHandler("pdf","AdresseStammblatt");

    $this->app->ActionHandler("lieferadresse","AdresseLieferadresse");
    $this->app->ActionHandler("lieferadresseneditpopup","AdresseLieferadressenEditPopup");
    $this->app->ActionHandler("ansprechpartner","AdresseAnsprechpartner");
    $this->app->ActionHandler("ansprechpartnereditpopup","AdresseAnsprechpartnerEditPopup");
    $this->app->ActionHandler("ansprechpartnerpopup","AdresseAnsprechpartnerPopup");

    $this->app->ActionHandler("accounts","AdresseAccounts");


    $this->app->ActionHandler("adressestammdatenpopup","AdresseStammdatenLieferadressePopup");
    $this->app->ActionHandler("ansprechpartnerlieferadressepopup","AdresseAnsprechpartnerLieferadressePopup");
    $this->app->ActionHandler("lieferadressepopup","AdresseLieferadressePopup");
    $this->app->ActionHandler("ustpopup","AdresseUSTPopup");
    $this->app->ActionHandler("rollen","AdresseRollen");
    $this->app->ActionHandler("gruppen","AdresseGruppen");
    $this->app->ActionHandler("kontakthistorie","AdresseKontakthistorie");
    $this->app->ActionHandler("kontakthistorieeditpopup","AdresseKontakthistorieEditPopup");
    $this->app->ActionHandler("rolecreate","AdresseRolleAnlegen");
    $this->app->ActionHandler("rolledatum","AdresseRolleDatum");
    $this->app->ActionHandler("roledel","AdresseRolleLoeschen");
    $this->app->ActionHandler("addposition","AdresseAddPosition");
    $this->app->ActionHandler("suchmaske","AdresseSuchmaske");
    $this->app->ActionHandler("dateien","AdresseDateien");
    $this->app->ActionHandler("brief","AdresseBrief");
    $this->app->ActionHandler("briefdelete","AdresseBriefDelete");
    $this->app->ActionHandler("briefpdf","AdresseBriefPDF");
    $this->app->ActionHandler("briefeditpopup","AdresseBriefEditPopup");

    $this->app->ActionHandler("saverolle", "AdresserolleSave");

    $this->app->ActionHandler("brieferstellen","AdresseBriefErstellen");
    $this->app->ActionHandler("briefpreview","AdresseBriefPreview");
    $this->app->ActionHandler("briefbearbeiten","AdresseBriefBearbeiten");
    $this->app->ActionHandler("briefkorrpdf", "AdresseKorrBriefPdf");
    $this->app->ActionHandler("briefkorrdelete", "AdresseKorrBriefDelete");
    $this->app->ActionHandler("briefdrucken", "AdresseBriefDrucken");


    $this->app->ActionHandler("email","AdresseEmail");
    $this->app->ActionHandler("belege","AdresseBelege");
    $this->app->ActionHandler("positioneneditpopup","AdresseArtikelEditPopup");
    $this->app->ActionHandler("emaileditpopup","AdresseEmailEditPopup");
    $this->app->ActionHandler("artikel","AdresseArtikelPosition");
    $this->app->ActionHandler("lieferantartikel","AdresseLieferantArtikel");
    $this->app->ActionHandler("kundeartikel","AdresseKundeArtikel");
    $this->app->ActionHandler("delartikel","DelArtikel");
    $this->app->ActionHandler("upartikel","UpArtikel");
    $this->app->ActionHandler("downartikel","DownArtikel");

    $this->app->ActionHandler("rolledelete","AdresseRolleDelete");
    $this->app->ActionHandler("artikeleditpopup","AdresseArtikelEditPopup");
    $this->app->ActionHandler("kontakthistorie","AdresseKontaktHistorie");
    $this->app->ActionHandler("offenebestellungen","AdresseOffeneBestellungen");
    $this->app->ActionHandler("verbindlichkeiten","AdresseVerbindlichkeiten");
    $this->app->ActionHandler("adressebestellungmarkieren","AdresseBestellungMarkiert");
    $this->app->ActionHandler("autocomplete","AdresseAutoComplete");

    $this->app->ActionHandler("lohn","AdresseLohnStundensatzUebersicht");
    $this->app->ActionHandler("stundensatz","AdresseStundensatz");
    $this->app->ActionHandler("stundensatzedit","AdresseStundensatzEdit");
    $this->app->ActionHandler("stundensatzdelete","AdresseStundensatzDelete");
    $this->app->ActionHandler("createdokument","AdresseCreateDokument");
    $this->app->ActionHandler("newkontakt","AdresseNewKontakt");
    $this->app->ActionHandler("delkontakt","AdresseDelKontakt");
    $this->app->ActionHandler("minidetail","AdresseMiniDetailZeit");
    $this->app->ActionHandler("minidetailadr","AdresseMiniDetailAdr");
    $this->app->ActionHandler("minidetailbrief","AdresseMiniDetailBrief");
    $this->app->ActionHandler("korreseditpopup","AdresseKorressEditPopup");
    $this->app->ActionHandler("sepamandat","AdresseSEPAMandat");

    $this->app->ActionHandler("verein","AdresseVerein");
    
    $this->app->ActionHandler("removeemailanhang","Adresseremoveemailanhang");
    $this->app->ActionHandler("downloaddatei", "AdresseDownloadDatei");

    
    $this->app->ActionHandler("minidetaillieferadressen","AdresseMinidetailLieferadressen");
    $this->app->ActionHandler("minidetailansprechpartner","AdresseMinidetailAnsprechpartner");   
    
    $id = $this->app->Secure->GetGET('id');
    //$nummer = $this->app->Secure->GetPOST("nummer");

    //if($nummer=="")
    //$name = $this->app->DB->Select("SELECT CONCAT(name,'&nbsp;&nbsp;',
    if($id > 0){
      $nummer = $this->app->DB->Select("SELECT CONCAT(
        if(kundennummer!='',CONCAT('Kunde: ',kundennummer),''),
          if(lieferantennummer!='',CONCAT(' Lieferant: ',lieferantennummer),'')) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    }else{
      $nummer = '';
    }


    if(is_numeric($id)){
      $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    }

    // else
    //   $name = $nummer;

    if(!empty($name)){
      $this->app->Tpl->SetText('UEBERSCHRIFT', 'Adresse von: ' . $name);
    }
    else{
      $this->app->Tpl->SetText('UEBERSCHRIFT', 'Adressen');
    }

    $this->app->Tpl->SetText('KURZUEBERSCHRIFT','Adressen');
    if(isset($name) && isset($nummer) && $name!='' && $nummer!=''){
      $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', "$name ($nummer)");
    }
    else{
      $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', !empty($name) ? $name : '');
    }

    if(isset($name)){
      $this->app->Tpl->SetText('ANZEIGENUMMER',$name);
    }
    if(isset($nummer)){
      $this->app->Tpl->SetText('ANZEIGENAMEDE',' '.$nummer);
    }

    $this->app->Tpl->Set('FARBE','[FARBE1]');

    $this->app->erp->CheckColumn('email', 'varchar(255)', 'adresse_accounts', 'NOT NULL');
    $this->app->erp->CheckColumn('notiz', 'text', 'adresse_accounts', 'NOT NULL');

    $this->app->erp->Headlines('Adressen');

    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
    $this->app->erp->RegisterHook('supersearch_detail', 'adresse', 'AdresseSupersearchDetail');
  }

  /**
   * @param \Xentral\Widgets\SuperSearch\Query\DetailQuery   $detailQuery
   * @param \Xentral\Widgets\SuperSearch\Result\ResultDetail $detailResult
   *
   * @return void
   */
  public function AdresseSupersearchDetail($detailQuery, $detailResult)
  {
    if ($detailQuery->getGroupKey() !== 'addresses') {
      return;
    }

    $adresseId = $detailQuery->getItemIdentifier();
    $sql = sprintf(
      "SELECT a.id, a.name, a.ansprechpartner, a.kundennummer, a.lieferantennummer FROM `adresse` AS `a` WHERE a.id = '%s' LIMIT 1",
      $this->app->DB->real_escape_string($adresseId)
    );
    $adresse = $this->app->DB->SelectRow($sql);
    if (empty($adresse)) {
      return;
    }

    $title = $adresse['name'];
    if (!empty($adresse['ansprechpartner'])) {
      $title .= ' ' . $adresse['ansprechpartner'];
    }
    if (!empty($adresse['kundennummer'])) {
      $title .= ' <small>Kunde ' . $adresse['kundennummer'] . '</small>';
    }
    if (!empty($adresse['lieferantennummer'])) {
      $title .= ' <small>Lieferant ' . $adresse['lieferantennummer'] . '</small>';
    }
    $detailResult->setTitle($title);
    $detailResult->addButton('Adresse Details', sprintf('index.php?module=adresse&action=edit&id=%s', $adresse['id']));
    $detailResult->setMiniDetailUrl(sprintf('index.php?module=adresse&action=minidetailadr&id=%s', $adresse['id']));
  }

  
  
  public function AdresseDownloadDatei()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($this->app->DB->Select("SELECT id FROM datei_stichwoerter where datei = '$id' AND subjekt like 'anhang' and objekt like 'dokument' LIMIT 1"))
    {
      $this->app->erp->SendDatei($id);
    } else {
      header("Content-Disposition: attachment; filename=\"Fehler.txt\"");
    }
    $this->app->ExitXentral();
  }
  
  public function Adresseremoveemailanhang()
  {
    $res['status'] = false;
    $datei = (int)$this->app->Secure->GetGET('id');
    if($datei)
    {
      $checkstichwort = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE datei = '$datei' AND subjekt like 'anhang' and objekt like 'dokument' LIMIT 1");
      if($checkstichwort)
      {
        $this->app->DB->Delete("DELETE FROM datei_version WHERE datei='$datei'");
        $this->app->DB->Delete("DELETE FROM datei_stichwoerter WHERE datei='$datei'");
        $this->app->DB->Update("UPDATE datei SET geloescht=1 WHERE id='$datei'");
        $res['status'] = true;      
      }
    }
    echo json_encode($res);
    $this->app->ExitXentral();
  }

  public function AdresseMiniDetailBrief($parsetarget = '', $menu = true)
  {
    $doppelteids = $this->app->Secure->GetGET('id');
    $ids = preg_split('/\-/',$doppelteids);
    if(count($ids) > 1)
    {
      $typ = (int)$ids[0];
      $id = (int)$ids[1];
      switch($typ)
      {
        case '1':
        
        $this->AdresseBriefPreview('dokumente',$id,false);
        break;
        case '2':
        
        $this->AdresseBriefPreview('dokumente_send',$id,false);
        break;
        
        case '5':
        
        $this->AdresseBriefPreview('wiedervorlage',$id,false);
        break;
        case '6':
        $this->AdresseBriefPreview('kalender',$id,false);
        break;

      }
      
    }
    $this->app->ExitXentral();
  }
  
  public function AdresseKorressEditPopup()
  {
    // nach page inhalt des dialogs ausgeben
    if (class_exists('WidgetDokumente')){
      $widget = new WidgetDokumente($this->app,'PAGE');
      //$sid = $this->app->DB->Select("SELECT adresse FROM abrechnungsartikel WHERE id='$id' LIMIT 1");
      //$widget->form->SpecialActionAfterExecute("close_refresh",
      //    "index.php?module=adresse&action=artikel&id=$sid");
      $widget->Edit();
      $this->app->BuildNavigation=false;
    }
  }
  
  public function AdresseMiniDetailAdr($parsetarget = '', $menu = true)
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $adr = $this->app->DB->SelectRow("SELECT adr.*, DATE_FORMAT(adr.mandatsreferenzdatum, '%e.%m.%Y') AS mandatsreferenzdatumd FROM adresse AS adr WHERE adr.id = ".$id." limit 1");
    if(!empty($adr))
    {
      $this->app->Tpl->Set('EMAIL',$adr['email']);
      $this->app->Tpl->Set('MOBIL',$adr['mobil']);
      $this->app->Tpl->Set('MARKETINGSPERRE',($adr['marketingsperre']==1?"JA":"NEIN"));
      $this->app->Tpl->Set('TELEFAX',$adr['telefax']);
      $this->app->Tpl->Set('TELEFON',$adr['telefon']);
      $this->app->Tpl->Set('ORT',$adr['ort']);
      $this->app->Tpl->Set('LAND',$adr['land']);
      $this->app->Tpl->Set('PLZ',$adr['plz']);
      $this->app->Tpl->Set('STRASSE',$adr['strasse']);
      $this->app->Tpl->Set('ANSPRECHPARTNERNAME',$adr['ansprechpartner']);

      $this->app->Tpl->Set('ZAHLUNGSWEISE',$adr['zahlungsweise']);
      $this->app->Tpl->Set('ZAHLUNGSZIELTAGE',$adr['zahlungszieltage']);
      $this->app->Tpl->Set('ZAHLUNGSZIELTAGESKONTO',$adr['zahlungszieltage']);
      $this->app->Tpl->Set('ZAHLUNGSZIELTAGESKONTO',$adr['zahlungszieltageskonto']);
      $this->app->Tpl->Set('ZAHLUNGSZIELSKONTO',$adr['zahlungszielskonto']);
      $this->app->Tpl->Set('KUNDENNUMMERLIEFERANT',$adr['kundennummerlieferant']);
      $this->app->Tpl->Set('ZAHLUNGSWEISELIEFERANT',$adr['zahlungsweiselieferant']);
      $this->app->Tpl->Set('ZAHLUNGSZIELTAGELIEFERANT',$adr['zahlungszieltagelieferant']);
      $this->app->Tpl->Set('ZAHLUNGSZIELTAGESKONTOLIEFERANT',$adr['zahlungszieltagelieferant']);
      $this->app->Tpl->Set('ZAHLUNGSZIELTAGESKONTOLIEFERANT',$adr['zahlungszieltageskontolieferant']);
      $this->app->Tpl->Set('ZAHLUNGSZIELSKONTOLIEFERANT',$adr['zahlungszielskontolieferant']);
      $this->app->Tpl->Set('VERSANDARTLIEFERANT', $adr['versandartlieferant']);
      
      
      $this->app->Tpl->Set('INHABER',$adr['inhaber']);
      $this->app->Tpl->Set('BANK',$adr['bank']);
      $this->app->Tpl->Set('SWIFT',$adr['swift']);
      $this->app->Tpl->Set('IBAN',$adr['iban']);
      $this->app->Tpl->Set('MANDATREFERENZ',$adr['mandatsreferenz']);
      $this->app->Tpl->Set('ZAHLUNGSZIELSKONTOLIEFERANT',$adr['zahlungszielskontolieferant']);
      $this->app->Tpl->Set('MANDATREFERENZART',$adr['mandatsreferenzart']);
      $this->app->Tpl->Set('MANDATSREFERENZDHART',$adr['mandatsreferenzdhart']);
      $this->app->Tpl->Set('MANDATSREFERENZDATUM',$adr['mandatsreferenzdatumd']);
      $this->app->Tpl->Set('MANDATSREFERENZAENDERUNG',($adr['mandatsreferenzaenderung']?'ja':'nein'));
      $this->app->Tpl->Set('WAEHRUNG',$adr['waehrung']);
      
      $table = new EasyTable($this->app);
      $table->Query("SELECT a.name, a.bereich, a.email, a.telefon, a.telefax, a.mobil FROM ansprechpartner a WHERE adresse='$id'  AND a.name!='Neuer Datensatz' ORDER by id DESC");
      $table->DisplayNew('ANSPRECHPARTNER','Mobil','noAction');

      $table = new EasyTable($this->app);
      $table->Query("SELECT a.subjekt as Rolle,
        if(a.objekt='','ALLE',a.objekt) as Zuordnung,
        if(a.objekt='Projekt',if(a.parameter='','ALLE',p.abkuerzung),CONCAT(g.name,' ',g.kennziffer)) as auswahl,
        DATE_FORMAT(a.von,'%d.%m.%Y') as seit, if(a.bis='0000-00-00','aktuell',DATE_FORMAT(a.bis,'%d.%m.%Y')) as bis
        FROM adresse_rolle a  LEFT JOIN projekt p ON a.parameter=p.id
        LEFT JOIN gruppen g ON g.id=a.parameter
        WHERE a.adresse='$id'");
      $table->DisplayNew('ROLLEN','Bis','noAction');


      $table2 = new EasyTable($this->app);
      $table2->Query("SELECT  if(l.standardlieferadresse,CONCAT('<strong>',l.name,' (Standardlieferadresse)</strong>'),l.name) as name2, l.strasse, 
                                             l.land, l.plz, l.ort, l.telefon,l.email FROM lieferadressen l where  l.adresse='". $id . "' AND l.name!='Neuer Datensatz'" );
      $table2->DisplayNew('LIEFERANTEN','Email','noAction');

      $table3 = new EasyTable($this->app);
      $table3->Query($this->GetAddressCrmSql($id, 15));
      $table3->DisplayNew('CRMDATEIEN','PDF','noAction');
    }
    if($parsetarget=='')
    {
      $this->app->Tpl->Output('adresse_minidetail.tpl');
      $this->app->ExitXentral();
    }
    $this->app->Tpl->Parse($parsetarget,'adresse_minidetail.tpl');
  }
  

  public function AdresseStammblatt()
  {
    $id = $this->app->Secure->GetGET('id');
    $projekt = '';
    if(class_exists('AdressstammblattPDFCustom'))
    {
      $Brief = new AdressstammblattPDFCustom($this->app,$projekt);
    }else{
      $Brief = new AdressstammblattPDF($this->app,$projekt);
    }

    $Brief->GetAdressstammblatt($id);
    $Brief->displayDocument();
    $this->app->ExitXentral();
  }



  function AdresseMiniDetailZeit() {

    $id = $this->app->Secure->GetGET('id');


    if(strpos($_SERVER['HTTP_REFERER'],'action=brief')!==false) {

      $data = explode('||', $id);
      /*
         echo '<pre>';
         print_r($data);
         echo '</pre>';
       */

      if (isset($data[1])) {

        $query = '';

        switch ($data[1]) {
          case 'dokumente':
            $query .= '
              SELECT
              von as personVon,
                  content as content,
                  DATE_FORMAT(datum, "%d.%m.%Y") as datum
                    FROM
                    dokumente
                    WHERE
                    id = ' . $data[0] . '
                    ';
            break;
          case 'dokumente_send':
            $query .= '
              SELECT
              bearbeiter as personVon,
                         text as content,
                         DATE_FORMAT(zeit, "%d.%m.%Y") as datum
                           FROM
                           dokumente_send
                           WHERE
                           id = ' . $data[0] . '
                           ';
            break;
          case 'ticket_nachricht':
            $query .= '
              SELECT
              verfasser as personVon,
                        text as content,
                        DATE_FORMAT(zeit, "%d.%m.%Y") as datum
                          FROM
                          ticket_nachricht
                          WHERE
                          id = ' . $data[0] . '
                          ';
            break;
          case 'emailbackup_mails':
            $query .= '
              SELECT
              sender as personVon,
                     if(action_html!="",action_html,action) as content,
                     DATE_FORMAT(empfang, "%d.%m.%Y") as datum
                       FROM
                       emailbackup_mails
                       WHERE
                       id = ' . $data[0] . '
                       ';
            break;
          default:
            $this->app->ExitXentral();
            break; 
        }

        $res = $this->app->DB->SelectRow($query);

        if ($res) {

          echo '<table cellpadding="0" cellspacing="0" width="100%" border=1>';

          echo '<tr>';
          echo '<td width="150">Datum:</td>';
          echo '<td>' . $res['datum'] . '</td>';
          echo '</tr>';

          echo '<tr>';
          echo '<td width="150">Von:</td>';
          echo '<td>' . $res['personVon'] . '</td>';
          echo '</tr>';

          echo '<tr>';
          echo '<td>Text:</td>';
          echo '<td>' . nl2br($res['content']) . '</td>';
          echo '</tr>';

          echo '</table>';

        }

        /*
           echo '<pre>';
           print_r($query);
           print_r($res);
           echo '</pre>';
         */


      }


      //echo $id;

    } else {


      $tmp = $this->app->DB->SelectArr("SELECT * FROM zeiterfassung WHERE id='$id'");
      $tmp = $tmp[0];
      $teilprojekt = $this->app->DB->Select("SELECT aufgabe FROM arbeitspaket WHERE id='".$tmp[arbeitspaket]."'");

      echo '<table width="710">';
      echo '<tr><td width="200"><b>Ort:</b></td><td>'.$tmp['ort'].'</td></tr>';
      echo '<tr><td><b>Tätigkeit:</b></td><td>'.$tmp['aufgabe'].'</td></tr>';
      echo '<tr valign="top"><td><b>Beschreibung:</b></td><td>'.nl2br($tmp['beschreibung']).'</td></tr>';
      echo '<tr><td><b>Teilprojekt:</b></td><td>'.$teilprojekt.'</td></tr>';
      echo '<tr><td><b>Kostenstelle:</b></td><td>'.$tmp['kostenstelle'].'</td></tr>';
      echo '<tr><td><b>Verrechnungsart:</b></td><td>'.$tmp['verrechnungsart'].'</td></tr>';
      echo '</table>';

    }

    $this->app->ExitXentral();
  }

  function AdresseAbrechnungzeitabgeschlossen()
  {
    $sid = $this->app->Secure->GetPOST('sid');
    $id = $this->app->Secure->GetGET('id');
    if($sid > 0){
      $this->app->DB->Update("UPDATE zeiterfassung SET ist_abgerechnet='1', abgerechnet='1' WHERE id='$sid' AND adresse_abrechnung='$id' LIMIT 1");
    }
    $this->AdresseAbrechnungzeit();
  }


  function AdresseAbrechnungzeitdelete()
  {
    $sid = $this->app->Secure->GetGET('sid');
    $id = $this->app->Secure->GetGET('id');
    if($sid > 0){
      $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id='$sid' AND adresse_abrechnung='$id' LIMIT 1");
    }
    $this->AdresseAbrechnungzeit();
  }



  function AdresseZeiterfassung()
  {
    $this->AdresseMenu();
    $this->app->Tpl->Add('OFFENE','<form action="" method="post">');
    $this->app->YUI->TableSearch('OFFENE','zeiterfassungmitarbeiter');

    $id = $this->app->Secure->GetGET('id');

    $back=$this->app->erp->base64_url_encode("index.php?module=adresse&action=abrechnungzeit&id=$id");

    $this->app->Tpl->Set('BACK',$back);
    $this->app->Tpl->Set('ID',$id);
  
    $this->app->Tpl->Parse('PAGE','adresse_zeiterfassung.tpl');
  }



  function AdresseAbrechnungzeit()
  {
    $this->AdresseMenu();
    $this->app->Tpl->Set('VERS','Professional');
    $this->app->Tpl->Set('MODUL','Professional');
    $this->app->Tpl->Parse('PAGE', 'only_version.tpl');
  }


  function AdresseCreateDokument()
  {
    $id = $this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd');

    $relocation = true;

    switch($cmd)
    {	
      case 'auftrag': $newid = $this->app->erp->CreateAuftrag($id); $this->app->erp->LoadAuftragStandardwerte($newid,$id); break;
      case 'angebot': $newid = $this->app->erp->CreateAngebot($id); $this->app->erp->LoadAngebotStandardwerte($newid,$id); break;
      case 'rechnung': $newid = $this->app->erp->CreateRechnung($id); $this->app->erp->LoadRechnungStandardwerte($newid,$id); break;
      case 'lieferschein': $newid = $this->app->erp->CreateLieferschein($id); $this->app->erp->LoadLieferscheinStandardwerte($newid,$id); break;
      case 'gutschrift': $newid = $this->app->erp->CreateGutschrift($id); $this->app->erp->LoadGutschriftStandardwerte($newid,$id); break;
      case 'bestellung': $newid = $this->app->erp->CreateBestellung($id); $this->app->erp->LoadBestellungStandardwerte($newid,$id);break;
      default: $relocation = false;
    }

    if($relocation)
    {
      $this->app->Location->execute("index.php?module=$cmd&action=edit&id=$newid");
    }

  }

  function AdresseLohnStundensatzUebersicht()
  {
    $this->AdresseMenu();	

    if(method_exists($this->app->erp,'GetTmpMessageOut'))
    {
      $this->app->Tpl->Set('MESSAGE',$this->app->erp->GetTmpMessageOut());
    }else{
      $msg = $this->app->erp->base64_url_decode($this->app->Secure->GetGET("msg"));
      if($msg!='') {
        $this->app->Tpl->Set('MESSAGE', $msg);
      }
    }
    $this->AdresseLohn();
    $this->AdresseStundensatz();

    $this->app->Tpl->Parse('PAGE','adresse_lohn.tpl');
  }

  function AdresseLohn()
  {
    $id = $this->app->Secure->GetGET('id');
    if(is_numeric($id))
    {
      $this->app->YUI->TableSearch('TAB1','adresselohn');
    }else{
      $this->app->Tpl->Set('MESSAGE', '<div class="error">Mitarbeiter-ID konnte nicht gefunden werden.</div>');
    }

  }

  function AdresseStundensatz($id)
  {
    $id = $this->app->Secure->GetGET('id');

    if(is_numeric($id))
    {
      $stundensatz = $this->app->Secure->GetPOST('Stundensatz_StandardStundensatz');
      $submit = $this->app->Secure->GetPOST('Stundensatz_Submit');

      // Speichere neuen Stundensatz
      if($submit!='')
      {
        $this->app->DB->Insert("INSERT INTO stundensatz (adresse, satz, typ, projekt, datum) VALUES ('$id', '$stundensatz', 'Standard', '0', NOW())");
        $this->app->Tpl->Set('MESSAGE', '<div class="success">Der neue Standard-Stundensatz wurde &uuml;bernommen.</div>');
      }

      // Hole neuesten Stundensatz
      $standard = $this->app->DB->Select("SELECT satz 
          FROM stundensatz 
          WHERE typ='standard'
          AND adresse='$id'
          ORDER BY datum DESC LIMIT 1");
      $this->app->Tpl->Set('STANDARDSTUNDENSATZ', $standard);

      // Fülle Projekt-Tabelle
      $this->app->YUI->TableSearch('TAB2',"adressestundensatz");
    }else
      $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Mitarbeiter-ID konnte nicht gefunden werden.</div>");
  }

  function AdresseStundensatzEdit()
  {
    $this->AdresseMenu();

    $user = $this->app->Secure->GetGET('user');
    $id = $this->app->Secure->GetGET('id');
    $projekt = $this->app->Secure->GetGET('projekt');

    $satz = $this->app->Secure->GetPOST('Stundensatz_Angepasst');
    $adapt = $this->app->Secure->GetPOST('Stundensatz_Adapt');
    $cancel = $this->app->Secure->GetPOST('Stundensatz_Angepasst_Cancel');
    $submit = $this->app->Secure->GetPOST('Stundensatz_Angepasst_Submit');


    if($cancel!='')
    {
      $this->app->Location->execute("index.php?module=adresse&action=lohn&id=$user");
    }


    // Hole neuesten Standard-Stundensatz
    $standard = $this->app->DB->Select("SELECT satz 
        FROM stundensatz 
        WHERE typ='standard'
        AND adresse='$user'
        ORDER BY datum DESC LIMIT 1");

    if(is_numeric($id))
    {
      // Stundensatz existiert bereits, hole Daten
      $stundensatz = $this->app->DB->SelectArr("SELECT * FROM stundensatz WHERE id='$id' LIMIT 1");
      $this->app->Tpl->Set('STUNDENSATZANGEPASST', $stundensatz[0][satz]);

      if($submit!='')
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM stundensatz WHERE id='$id' LIMIT 1");

        if($adapt!=''){
          $this->app->DB->Update("UPDATE stundensatz SET satz='$satz' WHERE adresse='$user' AND projekt='$projekt'");
        }

        $this->app->DB->Insert("INSERT INTO stundensatz (adresse, satz, typ, projekt, datum)
            VALUES ('$user', '$satz', 'Angepasst', '$projekt', NOW())");
        $this->app->Location->execute("index.php?module=adresse&action=lohn&id=$user&msg=$msg");
      }


      $this->app->Tpl->Set('MODE', 'Stundensatz editieren');
    }else
    {
      // Stundensatz existiert noch nicht
      $this->app->Tpl->Set('STUNDENSATZANGEPASST', $standard);
      $this->app->Tpl->Set('ADAPTDISABLED', 'DISABLED');

      if($submit!='')
      {
        // Schreibe neuen Satz
        $this->app->DB->Insert("INSERT INTO stundensatz (adresse, satz, typ, projekt, datum)
            VALUES ('$user', '$satz', 'Angepasst', '$projekt', NOW())");

        $msg = $this->app->erp->base64_url_encode('<div class="success">Der Stundensatz wurde erfolgreich gespeichert.</div>');
        $this->app->Location->execute("index.php?module=adresse&action=lohn&id=$user&msg=$msg");
      }	

      $this->app->Tpl->Set('MODE', 'Stundensatz erstellen');
    }

    $this->app->Tpl->Parse('PAGE', 'adresse_stundensatz_edit.tpl');
  }

  function AdresseStundensatzDelete()
  {
    $user = $this->app->Secure->GetGET('user');
    $id = $this->app->Secure->GetGET('id');

    if(is_numeric($id)){
      $this->app->DB->Delete("DELETE FROM stundensatz WHERE id='$id' LIMIT 1");
    }
    else{
      $msg = $this->app->erp->base64_url_encode('<div class="error">Stundensatz-ID konnte nicht gefunden werden. Standard-Stundens&auml;tze k&ouml;nnen nicht gel&ouml;scht werden.</div>');
    }

    $this->app->Location->execute("index.php?module=adresse&action=lohn&id=$user&msg=$msg");
  }



  function AdresseAutoComplete()
  {

    $table = $this->app->Secure->GetGET('table');
    $filter = $this->app->Secure->GetGET('filter');
    $name = $this->app->Secure->GetGET('name');
    $query = $this->app->Secure->GetGET('query');
    $colsstring = $this->app->erp->base64_url_decode($this->app->Secure->GetGET('colsstring'));
    $returncol= $this->app->erp->base64_url_decode($this->app->Secure->GetGET('returncol'));
    if($table==''){
      $table = $name;
    }

    if($filter==='kunde'){
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id WHERE adresse_rolle.subjekt='Kunde' AND adresse.kundennummer!=0 AND adresse.geloescht=0 AND adresse.name LIKE '%$query%'";
    }
    elseif($filter==='mitarbeiter'){
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id WHERE (adresse_rolle.subjekt='Mitarbeiter' OR adresse_rolle.subjekt='Externer Mitarbeiter') AND adresse.mitarbeiternummer!=0 AND adresse.geloescht=0 
        AND adresse.name LIKE '%$query%'";
    }
    elseif($filter==='lieferant'){
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id WHERE adresse_rolle.subjekt='Lieferant' AND adresse.geloescht=0 AND adresse.name LIKE '%$query%'";
    }elseif($filter==='kunde_auftrag'){
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id LEFT JOIN auftrag ON auftrag.adresse=adresse.id WHERE adresse_rolle.subjekt='Kunde' AND ((auftrag.status='freigegeben' OR auftrag.status='storniert') OR (auftrag.vorkasse_ok=0 AND (auftrag.zahlungsweise='paypal' OR auftrag.zahlungsweise='vorkasse' OR auftrag.zahlungsweise='kreditkarte'))) AND adresse.geloescht=0
        AND adresse.name LIKE '%$query%'";
    }elseif($filter==='kunde_rechnung'){
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id LEFT JOIN rechnung ON rechnung.adresse=adresse.id WHERE adresse_rolle.subjekt='Kunde' AND rechnung.ist < rechnung.soll AND adresse.geloescht=0 AND adresse.name LIKE '%$query%'";
    }elseif($filter==='kunde_gutschrift'){
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id LEFT JOIN gutschrift ON gutschrift.adresse=adresse.id WHERE adresse_rolle.subjekt='Kunde' AND adresse.geloescht=0 AND adresse.name LIKE '%$query%'";
    }elseif($table==='artikel'){
      $filter = "WHERE name_de LIKE '%$query%'";
    }elseif(($filter=='' || $filter==='adresse') && $name==='adresse'){
      $filter = "WHERE adresse.geloescht=0 AND adresse.name LIKE '%$query%'";
    }
    $arr = $this->app->DB->SelectArr("SELECT DISTINCT $colsstring, $returncol FROM $table $filter ORDER by 1 LIMIT 10");
    //      echo "SELECT DISTINCT $colsstring, $returncol FROM $table $filter ORDER by 1";

    $cols = explode(',',$colsstring);
    foreach($arr as $key=>$value){
      //$tpl_end .= '{id:"'.$value[$returncol].'", cola:"'.$value[$cols[0]].'", colb:"'.$value[$cols[1]].'", colc:"'.$value[$cols[2]].'"},';
      echo $value[$returncol].'!*!'.$value[$cols[0]].' '.$value[$cols[1]].' '.$value[$cols[2]]."\n";
      //echo $value[$cols[0]].' '.$value[$cols[1]].' '.$value[$cols[2]]."\n";
      //echo $value[$cols[0]]."\n";
    }

    $this->app->ExitXentral();
  }


  function AdresseDateien()
  {
    $id = $this->app->Secure->GetGET('id');
    $this->AdresseMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Dateien)');
    $this->app->YUI->DateiUpload('PAGE','Adressen',$id);
  }


  function AdresseDelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    
    $checkuser = $this->app->DB->Select("SELECT count(id) FROM user WHERE adresse='$id'");
    $checkkassierer = $this->app->DB->Select("SELECT count(id) FROM pos_kassierer WHERE adresse='$id'");
    $checkabo = $this->app->DB->Select("SELECT COUNT(id) FROM abrechnungsartikel WHERE adresse = '$id'");

    if($id > 0){
      $msg = '';
      if($checkuser > 0){
        $msg .= $this->app->erp->base64_url_encode("<div class=\"error\">Die Adresse ist noch mit einem Benutzer verlinkt und kann daher nicht gelöscht werden.</div>");
      }else if($checkkassierer){
        $msg .= $this->app->erp->base64_url_encode("<div class=\"error\">Die Adresse ist noch mit einem Kassierer verlinkt und kann daher nicht gelöscht werden.</div>");
      }else if($checkabo > 0){
        $msg .= $this->app->erp->base64_url_encode("<div class=\"error\">Die Adresse ist noch mit Abos verlinkt und kann daher nicht gelöscht werden.</div>");
      }else {
        $this->app->DB->Update("UPDATE adresse SET geloescht='1',kundennummer=CONCAT('DEL-',kundennummer), lieferantennummer=CONCAT('DEL-',lieferantennummer), 
          mitarbeiternummer=CONCAT('DEL-',mitarbeiternummer) WHERE id='$id' LIMIT 1");

        $this->app->DB->Update("DELETE FROM ansprechpartner WHERE adresse='$id'");
        $this->app->DB->Update("DELETE FROM lieferadressen WHERE adresse='$id'");
        $this->app->DB->Update("DELETE FROM adresse_rolle WHERE adresse='$id'");
        $this->app->DB->Update("DELETE FROM dokumente WHERE adresse='$id'");
        $this->app->DB->Update("DELETE FROM dokumente_send WHERE adresse='$id'");

        $this->app->erp->ObjektProtokoll("adresse",$id,"adresse_delete","Adresse geloescht: $id");
        $this->app->erp->RunHook('address_delete', 1, $id);

        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Adresse wurde gelöscht!</div>");
      }
    }
    $this->app->Location->execute('index.php?module=adresse&action=list&msg='.$msg);
  }


  function AdresseRolleDatum()
  {
    $id = $this->app->Secure->GetGET('id');
    $sid = $this->app->Secure->GetPOST('sid');
    $von = $this->app->Secure->GetGET('von');
    $bis = $this->app->Secure->GetGET('bis');
    $von = $this->app->String->Convert($von,'%1.%2.%3','%3-%2-%1');
    $bis = $this->app->String->Convert($bis,'%1.%2.%3','%3-%2-%1');

    $this->app->DB->Delete("UPDATE adresse_rolle SET von='$von', bis='$bis' WHERE id='$sid' AND adresse='$id' LIMIT 1");

    $gruppe = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE id='$sid' AND adresse='$id' LIMIT 1");

    if($gruppe > 0)
    {
      if($von!='--' && $bis!='--')
      {
        $this->app->DB->Update("UPDATE auftrag SET gruppe='$gruppe' WHERE datum<='$bis' AND datum >='$von'	AND adresse='$id'");	
        $this->app->DB->Update("UPDATE rechnung SET gruppe='$gruppe' WHERE datum<='$bis' AND datum >='$von'	AND adresse='$id'");	
        $this->app->DB->Update("UPDATE gutschrift SET gruppe='$gruppe' WHERE datum<='$bis' AND datum >='$von'	AND adresse='$id'");	
      } else if($von!='--' && $bis=='--')
      {
        $this->app->DB->Update("UPDATE auftrag SET gruppe='$gruppe' WHERE datum>='$von' AND adresse='$id'");	
        $this->app->DB->Update("UPDATE rechnung SET gruppe='$gruppe' WHERE datum>='$von' AND adresse='$id'");	
        $this->app->DB->Update("UPDATE gutschrift SET gruppe='$gruppe' WHERE datum>='$von' AND adresse='$id'");	
      }
    }

    $this->AdresseRollen();
  }


  function AdresseGruppen()
  {  
    $this->AdresseMenu();

    $id = (int)$this->app->Secure->GetGET('id');
    $gid=(int)$this->app->Secure->GetPOST('gid');
    $value=(int)$this->app->Secure->GetPOST('value');

    if($gid>0)
    {
      if($value=='1'){
        $this->app->erp->AddRolleZuAdresse($id, 'Mitglied', 'von','Gruppe', $gid);
      }else{
        $this->app->DB->Delete("UPDATE adresse_rolle SET bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE parameter='$gid' AND adresse='$id'
        AND (subjekt='Mitglied' OR subjekt='Kunde') AND objekt='Gruppe' AND bis = '0000-00-00'");
      }
      $this->app->erp->RunHook('adresse_gruppen_change', 3, $id, $gid, $value);
      $this->app->ExitXentral();
    }

    $this->app->YUI->TableSearch('TAB1','adresse_gruppen');

    $this->app->Tpl->Parse('PAGE','adresse_gruppen.tpl');
  }


  function AdresseRolleDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    $sid = $this->app->Secure->GetGET('sid');
    if($sid > 0){
      $this->app->DB->Delete("DELETE FROM adresse_rolle WHERE id='$sid' AND adresse='$id' LIMIT 1");
    }
    //    $this->app->Secure->POST['rolleanlegen'] = "true";
    //$this->AdresseEdit();
    $this->AdresseRollen();
    //$this->app->Tpl->Set(AKTIV_TAB3,"selected");
  }

  function AdresseCreate()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'duplicate'){
      $error = false;

      $name = trim($this->app->Secure->GetPOST('name'));
      $street = trim($this->app->Secure->GetPOST('street'));
      $zipcode = trim($this->app->Secure->GetPOST('zipcode'));
      $place = trim($this->app->Secure->GetPOST('place'));

      $where = '';

      if($name !== ''){
        $where .= " name = '$name' AND";
      }
      if($street !== ''){
        $where .= " strasse = '$street' AND";
      }
      if($zipcode !== ''){
        $where .= " plz = '$zipcode' AND";
      }
      if($place !== ''){
        $where .= " ort = '$place' AND";
      }

      if($where !== ''){
        $where = rtrim($where, 'AND');

        $addressId = $this->app->DB->Select("SELECT id FROM adresse WHERE ".$where." LIMIT 1");
        if($addressId != '' && $addressId > 0){
          $error = true;
        }
      }

      header('Content-Type: application/json');
      echo json_encode($error);
      $this->app->ExitXentral();
    }

    $this->app->ModuleScriptCache->IncludeJavascriptFiles('address', ['./classes/Modules/Address/www/js/address_create.js']);

    $this->app->erp->Headlines('Adresse anlegen');
    $this->app->Tpl->Set('TOPHEADING','Adresse anlegen');

    $this->app->erp->MenuEintrag('index.php?module=adresse&action=list','Zur&uuml;ck zur &Uuml;bersicht');


    parent::AdresseCreate();
  }


  function AdresseSuche()
  {
    $this->app->erp->MenuEintrag('index.php?module=adresse&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=adresse&action=create','Neue Adresse anlegen');

    $this->app->YUI->TableSearch('TAB1','adresse_suche');
    $this->app->Tpl->Parse('PAGE','adresse_suche.tpl');
  }

  function AdresseMenuList()
  {
    $this->app->erp->MenuEintrag('index.php?module=adresse&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=adresse&action=create','Neue Adresse anlegen');
    $this->app->erp->RunMenuHook('adresselist');    
  }


  function AdresseList()
  {
    $parameter = $this->app->User->GetParameter('table_filter_adresse');
    $parameter = json_decode($parameter, true);

    $this->AdresseMenuList();
    $zahlungsweisetext = null;
    $zahlungsweisentabelle = $this->app->DB->SelectArr("SELECT type,bezeichnung FROM `zahlungsweisen` WHERE aktiv = 1 AND type <> '' AND bezeichnung <> ''");
    if($zahlungsweisentabelle)
    {
      foreach($zahlungsweisentabelle as $v)
      {
        $zahlungsweisetext[$v['type']] = $v['bezeichunng'];
      }
    }

    $zahlungsweisen_ = $this->app->DB->SelectArr('
        SELECT
        zahlungsweise, zahlungsweiselieferant
        FROM
        adresse WHERE ifnull(geloescht, 0) = 0 AND (zahlungsweise <> \'\' OR zahlungsweiselieferant <> \'\')
        GROUP BY
        zahlungsweise, zahlungsweiselieferant
        ');
    $zahlungsweisen = null;
    if($zahlungsweisen_)
    {
      foreach($zahlungsweisen_ as $v)
      {
        if($v['zahlungsweise'] != '' && (!is_array($zahlungsweisen) || !in_array($v['zahlungsweise'], $zahlungsweisen))){
          $zahlungsweisen[] = $v['zahlungsweise'];
        }
        if($v['zahlungsweiselieferant'] != '' && (!is_array($zahlungsweisen) || !in_array($v['zahlungsweiselieferant'],$zahlungsweisen))){
          $zahlungsweisen[] = $v['zahlungsweiselieferant'];
        }
      }
      if($zahlungsweisen){
        sort($zahlungsweisen);
      }
    }


    $zahlungsweiseStr = '';
    if ($zahlungsweisen) {
      foreach ($zahlungsweisen as $zahlungsweise) {
        $zahlungsweiseStr .= '<option value="' . $zahlungsweise . '">' . (isset($zahlungsweisetext[$zahlungsweise])?$zahlungsweisetext[$zahlungsweise]:ucfirst($zahlungsweise)) . '</option>';
      }
    }

    $rollen = $this->app->DB->SelectArr('
        SELECT
        *
        FROM
        adresse_rolle
        GROUP BY
        subjekt
        ');

    $rollenStr = '';
    if ($rollen) {
      foreach ($rollen as $rolle) {
        $rollenStr .= '<option value="' . $rolle['subjekt'] . '">' . $rolle['subjekt'] . '</option>';
      }
    }
    
    $gruppen = $this->app->DB->SelectArr("SELECT * FROM gruppen gr WHERE (art = 'gruppe' or art = 'preisgruppe') AND (projekt = 0 OR (1 ".$this->app->erp->ProjektRechte('gr.projekt')."))");
    
    $gruppenStr = '';
    if ($gruppen) {
      foreach ($gruppen as $gruppe) {
        $gruppenStr .= '<option value="' . $gruppe['id'] . '">' . $gruppe['name'] . '</option>';
      }
    }
    
    $laender = $this->app->erp->GetSelectLaenderliste();
    $laenderStr = '';
    foreach ($laender as $landKey => $land) {
      $laenderStr .= '<option value="' . $landKey . '">' . $land . '</option>';
    }

    if( $this->app->erp->ModulVorhanden('verband') ) {
      $verbandsnummer = '';
      $verbandsnummer .= '<tr>';
      $verbandsnummer .= '<td>Verbandsnummer:</td>';
      $verbandsnummer .= '<td><input type="text" name="verbandsnummer" value=""></td>';
      $verbandsnummer .= '</tr>';
      $this->app->Tpl->Add('VERBANDSNUMMER',$verbandsnummer);
    }

    $this->app->YUI->AutoComplete('projekt', 'projektname', 1);
    $this->app->YUI->AutoComplete('name', 'adressename', 1);
    $this->app->YUI->AutoComplete('kundennummer', 'kunde', 1);
    $this->app->YUI->AutoComplete('vertrieb','adressegruppevertriebbearbeiter', 0, '&typ=vertrieb');
    $this->app->YUI->AutoComplete('innendienst','adressegruppevertriebbearbeiter', 0, '&typ=bearbeiter');
    $this->app->Tpl->Add('ZAHLUNGSWEISEN',$zahlungsweiseStr);
    $this->app->Tpl->Add('ROLLEN',$rollenStr);
    $this->app->Tpl->Add('GRUPPEN',$gruppenStr);
    $this->app->Tpl->Add('LAENDER',$laenderStr);
    $this->app->Tpl->Parse('TAB1','adresse_table_filter.tpl');

    $this->app->YUI->TableSearch('TAB1','adressetabelle');
    if($this->app->erp->Firmendaten('schnellsuche')){
      $this->app->YUI->EnterSearch('SCHNELLSUCHE', 'adressetabelle');
    }
    $this->app->Tpl->Parse('PAGE','adresseuebersicht.tpl');
  }


  function AdresseMenu()
  {
    $id = $this->app->Secure->GetGET('id');

    $nummer = $this->app->DB->Select("SELECT CONCAT(
        if(kundennummer!='',CONCAT('Kunde: ',kundennummer),''),
          if(lieferantennummer!='',CONCAT(' Lieferant: ',lieferantennummer),'')) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");

    if(is_numeric($id)){
      $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    }

    if(isset($name) && $name!=''){
      $this->app->Tpl->SetText('UEBERSCHRIFT', 'Adresse von: ' . $name);
    }
    else{
      $this->app->Tpl->SetText('UEBERSCHRIFT', 'Adressen');
    }

    $this->app->Tpl->SetText('KURZUEBERSCHRIFT','Adressen');
    if(isset($name) && isset($nummer) && $name!='' && $nummer!=''){
      $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', "$name ($nummer)");
    }
    else{
      $this->app->Tpl->SetText('KURZUEBERSCHRIFT2', !empty($name) ? $name : '');
    }

    if(isset($name)){
      $this->app->Tpl->SetText('ANZEIGENUMMER',$name);
    }
    if(isset($nummer)){
      $this->app->Tpl->SetText('ANZEIGENAMEDE',' '.$nummer);
    }

    $cmd = $this->app->Secure->GetPOST('cmd');
     
    if($cmd==='crm')
    {
      $this->app->erp->MenuEintrag('index.php?module=crm&action=list','Zur&uuml;ck zur &Uuml;bersicht');
    } 

    $this->app->erp->MenuEintrag('index.php?module=adresse&action=create','Neue Adresse anlegen');

    $this->app->erp->MenuEintrag("index.php?module=adresse&action=edit&id=$id",'Details');
  
    $anzahldateien = $this->app->erp->AnzahlDateien('Adressen',$id);
    if($anzahldateien > 0) {
      $anzahldateien = ' ('.$anzahldateien.')';
    } else {
      $anzahldateien='';
    }

    $this->app->erp->MenuEintrag("index.php?module=adresse&action=dateien&id=$id",'Dateien'.$anzahldateien);

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
                adresse_to = ' . (int)$id . '
            )

            UNION ALL

            (
              SELECT
                COUNT(id) as anzahl
              FROM
                dokumente_send
              WHERE
                adresse = ' . (int)$id . '
            )';
            if(1){//$this->app->erp->RechteVorhanden('ticket','list')){
            $count .= '
            UNION ALL

            ( 
              SELECT
                COUNT(ticket.id) as anzahl
              FROM
                ticket
                LEFT JOIN ticket_nachricht ON ticket.schluessel = ticket_nachricht.ticket
              WHERE
                ticket.adresse = ' . (int)$id . '
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
                k.adresse = ' . (int)$id . '

            )';


            if($this->app->erp->RechteVorhanden('wiedervorlage','list')){
              $count .= '
              UNION ALL

              (
                SELECT
                  COUNT(id) as anzahl
                FROM
                  wiedervorlage
                WHERE
                 wiedervorlage.adresse = ' . (int)$id . '

              )';
            }
            $count .= '
          ) a
        ';

    $anzahlcrm = 0;
    if($id > 0){
      $anzahlcrm = $this->app->DB->Select($count);
    }

    if($anzahlcrm > 0){
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=brief&id=$id", "CRM ($anzahlcrm)");
    }
    else{
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=brief&id=$id", 'CRM');
    }

    if($this->app->erp->Version()!=='stock'){
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=rollen&id=$id", 'Rollen');
    }

    $this->app->erp->MenuEintrag("index.php?module=adresse&action=gruppen&id=$id",'Gruppen');



    if($this->app->erp->RechteVorhanden('verein','list') && $this->app->erp->Firmendaten('modul_verein')=='1'){
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=verein&id=$id", 'Verein');
    }


    // Ist Benutzer ein Mitarbeiter?
    if(is_numeric($id)){
      $mitarbeiter = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE adresse='$id' AND subjekt='Mitarbeiter' LIMIT 1");
    }
    if(is_numeric($mitarbeiter))
    {
      //$this->app->erp->MenuEintrag("index.php?module=adresse&action=lohn&id=$id","Lohn");
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=zeiterfassung&id=$id",'Zeit');
    }


    $anzahlansp = $this->app->DB->Select("SELECT COUNT(id) FROM ansprechpartner WHERE adresse='$id'");
    if($anzahlansp>0) {
      $anzahlansp = ' ('.$anzahlansp.')';
    } else {
      $anzahlansp='';
    }

    $anzahllief = $this->app->DB->Select("SELECT COUNT(id) FROM lieferadressen WHERE adresse='$id'");
    if($anzahllief>0) {
      $anzahllief = ' ('.$anzahllief.')';
    } else {
      $anzahllief='';
    }

    $anzahlacc = $this->app->DB->Select("SELECT COUNT(id) FROM adresse_accounts WHERE adresse='$id'");
    if($anzahlacc>0) {
      $anzahlacc = ' ('.$anzahlacc.')';
    } else {
      $anzahlacc='';
    }

    $this->app->erp->MenuEintrag("index.php?module=adresse&action=ansprechpartner&id=$id",'Ansprechpart.'.$anzahlansp);
    $this->app->erp->MenuEintrag("index.php?module=adresse&action=lieferadresse&id=$id",'Lieferadressen'.$anzahllief);
    $this->app->erp->MenuEintrag("index.php?module=adresse&action=accounts&id=$id",'Accounts'.$anzahlacc);


    if($this->app->erp->IsAdresseSubjekt($id,'Kunde')){
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=belege&id=$id", 'Belege');
    }

    if($this->app->erp->Version()!=='stock'){
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=kundeartikel&id=$id", 'Artikel');
    }

    if($this->app->erp->IsAdresseSubjekt($id,'Kunde'))
    {
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=abrechnungzeit&id=$id",'Zeitkonto');
      if($this->app->erp->ModulVorhanden('rechnungslauf'))
      {
        $anzahlabos = $this->app->DB->Select("SELECT COUNT(id) FROM abrechnungsartikel WHERE adresse='$id' AND (enddatum = '0000-00-00' OR enddatum >= NOW())"); 
        if($anzahlabos>0){
          $this->app->erp->MenuEintrag("index.php?module=adresse&action=artikel&id=$id", "Abos ($anzahlabos)");
        }
        else{
          $this->app->erp->MenuEintrag("index.php?module=adresse&action=artikel&id=$id", 'Abos ');
        }
      }
      //$this->app->erp->MenuEintrag("index.php?module=adresse&action=ustprf&id=$id","USt");
    }

    if($this->app->erp->IsAdresseSubjekt($id,'Lieferant'))
    {
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=lieferantartikel&id=$id",'Lieferprogramm');
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=offenebestellungen&id=$id",'Bestellungen');
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=verbindlichkeiten&id=$id",'Verbindlichkeiten');
    }


    $this->app->erp->MenuEintrag("index.php?module=adresse&action=ustprf&id=$id",'Ust-ID Prf.');

    $this->app->erp->RunMenuHook('adresse');
  }

  function AdresseKontaktHistorie()
  {
    $this->AdresseMenu();
    $this->app->Tpl->Add('UEBERSCHRIFT',' (Kontakthistorie)');
    $this->app->Tpl->Set('SUBSUBHEADING','Adressen');
    $id = $this->app->Secure->GetGET('id');

    // neues arbeitspaket
    $widget = new WidgetAnsprechpartner($this->app,'TAB2');
    $widget->form->SpecialActionAfterExecute('none',
        "index.php?module=adresse&action=ansprechpartner&id=$id");
    $widget->Create();


    //Formula ansprechpartner
    $table = new EasyTable($this->app);
    $table->Query("SELECT name, bereich, telefon, email,id FROM ansprechpartner WHERE adresse='$id'");
    $table->DisplayNew('INHALT', "<a href=\"index.php?module=adresse&action=ansprechpartnereditpopup&frame=false&id=%value%\" 
        onclick=\"makeRequest(this);return false\">Bearbeiten</a>");

    // easy table mit arbeitspaketen YUI als template 
    $this->app->Tpl->Parse('TAB1','rahmen70.tpl');
    $this->app->Tpl->Set('AKTIV_TAB1','selected');
    $this->app->Tpl->Parse('PAGE','ansprechpartneruebersicht.tpl');
  }

  function AdresseKontaktHistorieEditPopup()
  {
    $frame = $this->app->Secure->GetGET('frame');
    $id = $this->app->Secure->GetGET('id');

    if($frame=='false')
    {
      // hier nur fenster größe anpassen
      $this->app->YUI->IframeDialog(600,320);
    } else {
      // nach page inhalt des dialogs ausgeben
      $widget = new WidgetAnsprechpartner($this->app,'PAGE');
      $adresse = $this->app->DB->Select("SELECT adresse FROM ansprechpartner WHERE id='$id' LIMIT 1");
      $widget->form->SpecialActionAfterExecute('close_refresh',
          "index.php?module=adresse&action=ansprechpartner&id=$adresse");

      $widget->Edit();
      $this->app->BuildNavigation=false;
    }
  }




  function AdresseRolle()
  {


  } 

  function AdresseNummern($id)
  {
    $tmp_data_adresse= $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    if(is_numeric($id)) {
      $kundennummer = $tmp_data_adresse['kundennummer'];
      $lieferantennummer = $tmp_data_adresse['lieferantennummer'];
      $mitarbeiternummer= $tmp_data_adresse['mitarbeiternummer'];
      $projekt= $tmp_data_adresse['projekt'];
    }


    if($kundennummer=='0' || $kundennummer==''){
      // pruefe ob rolle kunden vorhanden
      if(is_numeric($id)){
        $check = $this->app->DB->Select("SELECT adresse FROM adresse_rolle WHERE adresse='$id' AND subjekt='Kunde' LIMIT 1");
      }
      if(!empty($check))
      {
        $kundennummer = $this->app->erp->GetNextKundennummer($projekt,$tmp_data_adresse);
        $this->app->erp->ObjektProtokoll('adresse',$id,'adresse_next_kundennummer',"Kundennummer erhalten: $kundennummer");
        
        $this->app->DB->Update("UPDATE adresse SET kundennummer='$kundennummer' WHERE id='$id' AND (kundennummer='0' OR kundennummer='') LIMIT 1");
      } else{
        $kundennummer = 'noch keine';
      }
    }

    if($lieferantennummer=='0'){
      if(is_numeric($id)){
        $check = $this->app->DB->Select("SELECT adresse FROM adresse_rolle WHERE adresse='$id' AND subjekt='Lieferant' LIMIT 1");
      }else {
        $check = '';
      }
      if($check!='')
      {
        $lieferantennummer= $this->app->erp->GetNextLieferantennummer($projekt,$tmp_data_adresse);
        $this->app->erp->ObjektProtokoll('adresse',$id,'adresse_next_lieferantennummer',"Lieferantennummer erhalten: $lieferantennummer");
        if(is_numeric($id)){
          $this->app->DB->Update("UPDATE adresse SET lieferantennummer='$lieferantennummer' WHERE id='$id' AND (lieferantennummer='0' OR lieferantennummer='') LIMIT 1");
        }
      } else{
        $lieferantennummer = 'noch keine';
      }
    }

    if($mitarbeiternummer=='0'){
      if(is_numeric($id)){
        $check = $this->app->DB->Select("SELECT adresse FROM adresse_rolle WHERE adresse='$id' AND (subjekt='Mitarbeiter' OR subjekt='Externer Mitarbeiter') LIMIT 1");
      }else{
        $check = '';
      }
      if($check!='')
      {
        $mitarbeiternummer= $this->app->erp->GetNextMitarbeiternummer($projekt,$tmp_data_adresse);
        $this->app->erp->ObjektProtokoll('adresse',$id,'adresse_next_mitarbeiternummer',"Mitarbeiternummer erhalten: $mitarbeiternummer");
        if(is_numeric($id)){
          $this->app->DB->Update("UPDATE adresse SET mitarbeiternummer='$mitarbeiternummer' WHERE id='$id' AND (mitarbeiternummer='0' OR mitarbeiternummer='') LIMIT 1");
        }

      } else{
        $mitarbeiternummer = 'noch keine';
      }
    }
  }


  function AdresseDelKontakt()
  {
    $id = $this->app->Secure->GetGET('id');
    $lid = $this->app->Secure->GetGET('lid');
    if($lid > 0){
      $this->app->DB->Delete("DELETE FROM adresse_kontakte WHERE id='$lid' LIMIT 1");
    }

    $this->app->Location->execute("index.php?module=adresse&action=edit&id=$id");
  }



  function AdresseNewKontakt()
  {
    $bezeichnung = $this->app->Secure->GetGET('bezeichnung');
    $kontakt = $this->app->Secure->GetGET('kontakt');
    $id = $this->app->Secure->GetGET('id');

    //INSERT
    $this->app->DB->Insert("INSERT INTO adresse_kontakte (id,adresse,bezeichnung,kontakt) VALUES ('','$id','$bezeichnung','$kontakt')");

    $this->app->Location->execute("index.php?module=adresse&action=edit&id=$id");
  }

  function AdresseOpen()
  {

    $kundennummer=$this->app->Secure->GetGET('kundennummer');
    $projekt=$this->app->Secure->GetGET('projekt');

    if($projekt!='')
    {
      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $id = $this->app->DB->Select("SELECT id FROM adresse WHERE projekt='$projektid' AND kundennummer='$kundennummer' LIMIT 1");
    } else {
      $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' LIMIT 1");
    }

    $cmd=$this->app->Secure->GetPOST('cmd');
    if($cmd=="") $cmd = $this->app->Secure->GetGET('cmd');
    $this->app->Location->execute('index.php?module=adresse&action='.$cmd.'&id='.$id);
  }	


  function AdresseGetid()
  {

    $kundennummer=$this->app->Secure->GetGET('kundennummer');
    $projekt=$this->app->Secure->GetGET('projekt');

    if($projekt!='')
    {
      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $id = $this->app->DB->Select("SELECT id FROM adresse WHERE projekt='$projektid' AND kundennummer='$kundennummer' LIMIT 1");
    } else {
      $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' LIMIT 1");
    }

    echo $id;
    $this->app->ExitXentral();
  }	


  function AdresseEdit()
  {
    $id = $this->app->Secure->GetGET('id');
    $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id = '$id' LIMIT 1");

    $shopid = (int)$this->app->Secure->GetGET('shop');
    $export = (int)$this->app->Secure->GetGET('export');
    $shops = $this->app->DB->SelectArr("SELECT id, bezeichnung, modulename, shoptyp FROM shopexport WHERE projekt = '$projekt' AND demomodus <> 1 AND aktiv = 1 AND adressupdate = 1");

    if($shopid > 0){
      foreach ($shops as $shop){
        if((int)$shop['id'] === $shopid){
          if($export){
            $erg = $this->app->remote->RemoteCommand($shopid, 'sendadresse', $id);
            if(isset($erg['meldung']) && (!isset($erg['fehler']) || !$erg['fehler']))
            {
              $msg = $this->app->erp->base64_url_encode('<div class="info">'.$erg['meldung'].'</div>');
            }elseif(isset($erg['fehler']) && $erg['fehler'])
            {
              $msg = $this->app->erp->base64_url_encode('<div class="error">Es ist ein Fehler aufgetreten: '.$erg['fehler'].'!</div>');
            }else{
              $msg = $this->app->erp->base64_url_encode('<div class="error">Fehlerhafte Online-Shopkonfiguration</div>');
            }
          }else{
            $erg = $this->app->remote->RemoteCommand($shopid, 'getadresse', $id);
            if(isset($erg['meldung']) && (!isset($erg['fehler']) || !$erg['fehler']))
            {
              $msg = $this->app->erp->base64_url_encode('<div class="info">'.$erg['meldung'].'</div>');
            }elseif(isset($erg['fehler']) && $erg['fehler'])
            {
              $msg = $this->app->erp->base64_url_encode('<div class="error">Es ist ein Fehler aufgetreten: '.$erg['fehler'].'!</div>');
            }else{
              $msg = $this->app->erp->base64_url_encode('<div class="error">Fehlerhafte Online-Shopkonfiguration</div>');
            }
          }
          $this->app->Location->execute('index.php?module=adresse&action=edit&id='.$id.'&msg='.$msg);
          break;
        }
      }
    }


    if($this->app->erp->DisableModul('adresse',$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->AdresseMenu();
      return;
    } 

    $this->app->YUI->SaveReally();

    $adresse_kontakte = $this->app->Secure->GetPOST('adresse_kontakte');
    if(!empty($adresse_kontakte) && count($adresse_kontakte) > 0) {
      foreach($adresse_kontakte as $key=>$value)
        $this->app->DB->Update("UPDATE adresse_kontakte SET kontakt='$value' WHERE id='$key' LIMIT 1");
    }
    $telefon = '';
    $mobil = '';
    $email = '';
    $internetseite = '';
    if(is_numeric($id)) {
      $adressarr = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      if(!empty($adressarr)){
        $kundennummer = $adressarr['kundennummer'];//$this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
        $lieferantennummer = $adressarr['lieferantennummer'];//$this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
        $mitarbeiternummer = $adressarr['mitarbeiternummer'];//$this->app->DB->Select("SELECT mitarbeiternummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
        $logfile = $adressarr['adresse'];//$this->app->DB->Select("SELECT logfile FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
        $telefon = $adressarr['telefon'];
        $mobil = $adressarr['mobil'];
        $email = $adressarr['email'];
        $internetseite = $adressarr['internetseite'];
      }else{
        $kundennummer = '';
        $lieferantennummer = '';
        $mitarbeiternummer = '';
        $logfile = '';
      }
      $logfile  = str_replace(';',"\r\n",$logfile);
      $this->app->Tpl->Set('LOGFILE',"<textarea cols=\"60\" rows=\"5\" id=\"logfile\" name=\"logfile\">$logfile</textarea>");
    }

    $sipuid = false;
    $isPlaceTelAvailable = $this->app->erp->RechteVorhanden('placetel','call')
      && !empty($sipuid = $this->app->erp->GetPlacetelSipuid());
    $isSipgateAvailable = $this->app->erp->RechteVorhanden('sipgate','call')
      && !empty($this->app->erp->GetKonfiguration('sipgate_api_user'));
    if($isSipgateAvailable || $isPlaceTelAvailable) {
      $phoneModule = $isPlaceTelAvailable ? 'placetel' : 'sipgate';
      $this->app->Tpl->Set('JSPLACETEL','
          function call(id, dummy)
          {
            $.ajax({
                url: \'index.php?module=' . $phoneModule . '&action=call&id=\'+id,
                type: \'POST\',
                dataType: \'json\',
                data: {},
                success: function(data) {
                  if(data)
                  {
                  
                  }
                }
            });

          }
      ');
    }
    //$telefon= $this->app->DB->Select("SELECT telefon FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    if($telefon!='')
    {
      $telefon = str_replace('/','',$telefon);
      $telefon = str_replace(' ','',$telefon);
      if($isPlaceTelAvailable || $isSipgateAvailable)
      {
        $this->app->Tpl->Set('TELEFONBUTTON',"<a href=\"#\" onclick=call(\"1-".$id."\") ><img border=\"0\" width=\"20\" src=\"./themes/".$this->app->Conf->WFconf['defaulttheme']."/images/phone.png\" class=\"iconininput\" alt=\"anrufen\" /></a>");
      }else{
        $this->app->Tpl->Set('TELEFONBUTTON',"<a href=\"tel://$telefon\"><img border=\"0\" width=\"20\" src=\"./themes/".$this->app->Conf->WFconf['defaulttheme']."/images/phone.png\" class=\"iconininput\" alt=\"anrufen\" /></a>");
      }
    }
    //$mobil= $this->app->DB->Select("SELECT mobil FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    if($mobil!='')
    {
      $mobil = str_replace('/','',$mobil);
      $mobil = str_replace(' ','',$mobil);
      if($isPlaceTelAvailable || $isSipgateAvailable)
      {
        $this->app->Tpl->Set('MOBILBUTTON',"<a href=\"#\" onclick=call(\"2-".$id."\") ><img border=\"0\" width=\"20\" src=\"./themes/".$this->app->Conf->WFconf['defaulttheme']."/images/phone.png\" class=\"iconininput\" alt=\"anrufen\" /></a>");
      }else{
        $this->app->Tpl->Set('MOBILBUTTON',"<a href=\"tel://$mobil\"><img border=\"0\" width=\"20\" src=\"./themes/".$this->app->Conf->WFconf['defaulttheme']."/images/phone.png\" class=\"iconininput\" alt=\"anrufen\" /></a>");
      }
    }    
    
    //$email = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    if($email != '')
    {
      $email = str_ireplace("mailto:",'',$email);
      $this->app->Tpl->Set('EMAILBUTTON',"<a href=\"mailto:$email\"><img border=\"0\" width=\"20\" src=\"./themes/".$this->app->Conf->WFconf['defaulttheme']."/images/mail2.png\" alt=\"Email schreiben\" class=\"iconininput\" /></a>");
    }

    //$internetseite = $this->app->DB->Select("SELECT internetseite FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    if($internetseite != '')
    {
      if(stripos($internetseite,'http') === false)$internetseite = 'http://'.$internetseite;
      $this->app->Tpl->Set('INTERNETBUTTON',"<a href=\"$internetseite\" target=\"_blank\"><img border=\"0\" width=\"20\" src=\"./themes/".$this->app->Conf->WFconf['defaulttheme']."/images/web.png\" class=\"iconininput\" alt=\"Internetseite besuchen\" /></a>");
    }

    //Weitere Kontakte
    $buttons_kontakte = "

<input type=\"button\" class=\"button button-secondary\" onclick=\"var bezeichnung =  prompt('Etikett bzw. Bezeichnung (z.B. E-Mail, Skype, ICQ, ...):','Telefon Privat'); 
if((bezeichnung !=null && bezeichnung!='')) {var kontakt =  prompt('Kontakt:',''); if((bezeichnung !=null && bezeichnung!='') && (kontakt!=null && kontakt!='')) { window.location.href='index.php?module=adresse&action=newkontakt&id=".$id."&bezeichnung='+encodeURIComponent(bezeichnung)+'&kontakt='+encodeURIComponent(kontakt);}}\"
 value=\"Weitere Kontaktinfos\">";

$kontakte = $this->app->DB->SelectArr("SELECT * FROM adresse_kontakte WHERE adresse='$id'");
$tabindex=60;
$table_kontakte = '';
$ckontakte = !empty($kontakte)?count($kontakte):0;
for($i=0;$i<$ckontakte;$i++)
{
  $tabindex = $tabindex+i;
  $table_kontakte .= "<tr><td>".$kontakte[$i]['bezeichnung'].":&nbsp;
    </td><td><input type=text name=\"adresse_kontakte[".$kontakte[$i]['id']."]\" value=\"".$kontakte[$i]['kontakt']."\" size=\"30\" tabindex=\"$tabindex\">&nbsp;<a href=\"#\" onclick=\"if(!confirm('".$kontakte[$i]['bezeichnung']." wirklich entfernen?'))    return false; else window.location.href='index.php?module=adresse&action=delkontakt&id=".$id."&lid=".$kontakte[$i]['id']."';\">x</a></td></tr>";
}


$this->app->Tpl->Set('BUTTON_KONTAKTE',"$table_kontakte<tr><td></td><td>$buttons_kontakte</td></tr>");




$things = array('angebot','auftrag','rechnung','lieferschein','gutschrift');
$buttons_kunde = '';
foreach($things as $key=>$value) {
  $buttons_kunde .= '
  <input type="button" class="btnGreenBig" onclick="window.location.href=\'index.php?module=adresse&action=createdokument&id=' . $id . '&cmd=' . $value . '\';" value="' . ucfirst($value) . '">';
}

$things = array('bestellung');
$buttons_lieferant = '';
foreach($things as $key=>$value) {
  $buttons_lieferant .= '
  <input type="button" class="btnGreenBig" onclick="window.location.href=\'index.php?module=adresse&action=createdokument&id=' . $id . '&cmd=' . $value . '\';" value="' . ucfirst($value) . '">';
}



  if($kundennummer !='') {
    $buttons = $buttons_kunde;
  }
  if($lieferantennummer !='') {
    $buttons .= $buttons_lieferant;
  }

  if(1){//$buttons !=""){
    $this->app->Tpl->Set('BUTTONS','<fieldset><legend>Neu Anlegen</legend>
        <div class="tabsbutton" style="float:left;">'.$buttons.'</div>
        </fieldset>');

    $this->app->Tpl->Set('BUTTONS2','<input type="button" class="button button-secondary" onclick="copyTextToClipboard(\''.str_replace("'","\'",$this->AdresseAnschriftString($id)).'\')" value="Adresse in Zwischenspeicher">
        <input type="button" class="button button-secondary" onclick="window.location.href=\'index.php?module=adresse&action=pdf&id='.$id.'\'" value="Stammdaten PDF">');
  }

if(is_numeric($id)){
  $anzahl_rollen = $this->app->DB->Select("SELECT SUM(id) FROM adresse_rolle WHERE adresse='$id'");
}

$anzahl_lead = !empty($adressarr['lead'])?$adressarr['lead']:0;// $this->app->DB->Select("SELECT lead FROM adresse WHERE id='$id'");
$anzahl_rollen += $anzahl_lead;


if($anzahl_rollen<1)
{

  if($this->app->erp->Version()==='stock')
  {
    $lieferant_checked = 'checked';
    $kunde_checked = '';
  } else {
    $lieferant_checked = '';
    $kunde_checked = 'checked';
  }

  if($this->app->erp->RechteVorhanden("crm","list"))
    $leadbox = "<input type=\"checkbox\" value=\"1\" name=\"lead\" id=\"lead\">&nbsp;<label for=\"lead\">als Lead markieren</label>";

  if($this->app->erp->RechteVorhanden("adresse","rollen"))
  {
    $this->app->Tpl->Set('MESSAGEROLLE',"
      <div class=\"info\">Die Adresse hat noch keine Rolle. Soll eine <a href=\"index.php?module=adresse&action=rollen&id=$id\">Rolle</a> anlegt werden: <form action=\"index.php?module=adresse&action=rollen&id=$id\" method=\"post\">
      <input type=\"checkbox\" value=\"1\" name=\"kunde\" id=\"kunde\" $kunde_checked>&nbsp;<label for=\"kunde\">als Kunde markieren</label>
      <input type=\"checkbox\" value=\"1\" name=\"lieferant\" id=\"lieferant\" $lieferant_checked>&nbsp;<label for=\"lieferant\">als Lieferant markieren</label>
      <input type=\"checkbox\" value=\"1\" name=\"mitarbeiter\" id=\"mitarbeiter\">&nbsp;<label for=\"mitarbeiter\">als Mitarbeiter markieren</label>
      $leadbox
      <input type=\"submit\" value=\"Jetzt markieren\" name=\"submitrolle\">
      </form></div>");
  }
}

if($anzahl_rollen==1 && $anzahl_lead==1)
{
$this->app->Tpl->Set('MESSAGEROLLE','<div class="info">Diese Adresse ist ein potentieller Neukunde (Lead).</div>');

}

// aktiviere tab 1
$this->app->Tpl->Set('AKTIV_ADRESSE','selected');
$this->AdresseNummern($id);
if($kundennummer==0) {
  $kundennummer = 'keine Kundennummer vorhanden';
}
if($lieferantennummer==0){
  $lieferantennummer = 'keine Lieferantennummer vorhanden';
}
if($mitarbeiternummer==0){
  $mitarbeiternummer = 'keine Mitarbeiternummer vorhanden';
}


$this->app->Tpl->Set('KUNDENNUMMERANZEIGE',$kundennummer);
$this->app->Tpl->Set('LIEFERANTENNUMMERANZEIGE',$lieferantennummer);
$this->app->Tpl->Set('MITARBEITERNUMMERANZEIGE',$mitarbeiternummer);

$this->AdresseMenu();
$this->app->Tpl->Set('TABLE_ADRESSE_KONTAKTHISTORIE','TDB');
$this->app->Tpl->Set('TABLE_ADRESSE_ROLLEN','TDB');

$this->app->Tpl->Set('TABLE_ADRESSE_USTID','TDB');


$this->app->Tpl->Set('SUBSUBHEADING','Rolle anlegen');
if($this->app->Secure->GetPOST('rolleanlegen')!=''){
  $this->app->Tpl->Set('AKTIV_TAB3', 'selected');
}
else{
  $this->app->Tpl->Set('AKTIV_TAB1', 'selected');
}

$abweichende_rechnungsadresse= $this->app->DB->Select("SELECT abweichende_rechnungsadresse FROM adresse WHERE id='$id' LIMIT 1");
$this->app->Tpl->Set('ABWEICHENDERECHNUNGSADRESSESTYLE','none');
if($abweichende_rechnungsadresse=='1') {
  $this->app->Tpl->Set('ABWEICHENDERECHNUNGSADRESSESTYLE','');
}

$liefersperre= $this->app->DB->Select("SELECT liefersperre FROM adresse WHERE id='$id' LIMIT 1");
if($liefersperre=='1')
{
  $this->app->Tpl->Add('MESSAGE',"<div class=\"error\">Achtung! Bei dieser Adresse ist die Liefersperre gesetzt!</div>");
}
parent::AdresseEdit();
$this->app->erp->MessageHandlerStandardForm();

}

function AdresseRollen()
{ 
  $this->AdresseMenu();

  $id = $this->app->Secure->GetGET('id');
  $reload = $this->app->Secure->GetGET('reload');
  $submitrolle = $this->app->Secure->GetPOST('submitrolle');

  $this->AdresseRolleEdit();

  $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$id' LIMIT 1");

  if($submitrolle!='')
  {

    if($this->app->Secure->GetPOST('kunde')=='1'){
      $this->app->erp->AddRolleZuAdresse($id, 'Kunde', 'von', 'Projekt', $projekt);
    }
    if($this->app->Secure->GetPOST('mitarbeiter')=='1'){
      $this->app->erp->AddRolleZuAdresse($id, 'Mitarbeiter', 'von', 'Projekt', $projekt);
    }
    if($this->app->Secure->GetPOST('lieferant')=='1'){
      $this->app->erp->AddRolleZuAdresse($id, 'Lieferant', 'von', 'Projekt', $projekt);
    }
    if($this->app->Secure->GetPOST('lead')=='1'){
      $this->app->DB->Update("UPDATE adresse SET `lead`=1 WHERE id='$id' LIMIT 1");
    }
  }

  $this->AdresseNummern($id);

  if($submitrolle!='')
  {
    $this->app->Location->execute("index.php?module=adresse&action=edit&id=$id");
  }

  if($this->app->Secure->GetPOST('rolleanlegen')!='')
  {
    $subjekt = $this->app->Secure->GetPOST('subjekt');
    $objekt = $this->app->Secure->GetPOST('objekt');
    if($objekt==='Projekt')
    {
      $projekt =  $this->app->Secure->GetPOST('parameter');
      $parameter = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND abkuerzung <> '' LIMIT 1");
    } else {
      $gruppe=  $this->app->Secure->GetPOST('gruppe');
      $parameter = $this->app->DB->Select("SELECT id FROM gruppen WHERE CONCAT(name,' ',kennziffer)='$gruppe' LIMIT 1");
    }

    if(!($objekt==='Gruppe' && $parameter <=0))
    {
      $this->app->erp->AddRolleZuAdresse($id, $subjekt, "von", $objekt, $parameter); 
    }
    else {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Rolle nicht gespeichert! Bitte geben Sie eine Gruppe an!</div>");
      $this->app->Location->execute("index.php?module=adresse&action=rollen&id=$id&msg=$msg");
    }
  }	

  $this->app->YUI->AutoComplete('parameter','projektname',1);
  $this->app->YUI->AutoComplete('gruppe','gruppe');

  $subjekt= $this->app->erp->GetAdressSubject();
  $this->app->Tpl->Set('ROLLE_SELECT',$this->app->erp->GetSelect($subjekt,''));

  $this->app->Tpl->Parse('TAB1','adresse_rolle.tpl');
  if($this->app->Secure->GetPOST('rolleanlegen')!='' || $reload=='true')
  {
    $this->app->Location->execute("index.php?module=adresse&action=rollen&id=$id");
  } 


  $this->app->Tpl->Set('SUBSUBHEADING','Rollen der Adresse');
  $this->app->Tpl->Set('TABTEXT','Rollen');

  $table = new EasyTable($this->app);
  $table->Query("SELECT a.subjekt as Rolle, 
        if(a.objekt='','ALLE',a.objekt) as Zuordnung, 
        if(a.objekt='Projekt',if(a.parameter='','ALLE',p.abkuerzung),CONCAT(g.name,' ',g.kennziffer)) as auswahl, 
        DATE_FORMAT(a.von,'%d.%m.%Y') as seit, if(a.bis='0000-00-00','aktuell',DATE_FORMAT(a.bis,'%d.%m.%Y')) as bis,  a.id
        FROM adresse_rolle a  LEFT JOIN projekt p ON a.parameter=p.id 
        LEFT JOIN gruppen g ON g.id=a.parameter
        WHERE a.adresse='$id'");

  $table->DisplayNew('TAB1NEXT', "<!--<a href=\"index.php?module=adresse&action=rolleeditpopup&frame=false&id=%value%\" 
      onclick=\"makeRequest(this);return false\"><img src=\"./themes/[THEME]/images/edit.svg\" border=\"0\"></a>&nbsp;-->
      <a href=\"javascript:;\" onclick=\"AdresseRolleEdit(%value%);\"><img src=\"./themes/[THEME]/images/edit.svg\" border=\"0\"></a>&nbsp;<a onclick=\"if(!confirm('Rolle wirklich l&ouml;schen?')) return false; else window.location.href='index.php?module=adresse&action=rolledelete&id=$id&sid=%value%';\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>");

  //<a onclick=\"var von =  prompt('Von Datum:','');   if((von !=null && von!='')) {var bis =  prompt('Bis Datum:',''); if((von !=null && von!='') ) { window.location.href='index.php?module=adresse&action=rolledatum&sid=%value%&id=".$id."&von='+von+'&bis='+bis;}}\">


  $this->app->Tpl->Parse('PAGE','tabview.tpl');

  $this->app->YUI->DatePicker('von_datum');
  $this->app->YUI->DatePicker('bis_datum');
} 


function AdresseRolleEdit()
{
  if($this->app->Secure->GetGET('cmd')==='get'){
    
    $id = (int)$this->app->Secure->GetPOST('id');
                
    $data = $this->app->DB->SelectRow("SELECT id as id, DATE_FORMAT(von,'%d.%m.%Y') as von_datum, DATE_FORMAT(bis,'%d.%m.%Y') as bis_datum FROM adresse_rolle WHERE id = \"$id\" LIMIT 1");

    echo json_encode($data);
    $this->app->ExitXentral();
  }
}

function AdresserolleSave()
{
    $id = (int)$this->app->Secure->GetPOST('id');
    $von_datum = $this->app->Secure->GetPOST('von_datum');
    $bis_datum = $this->app->Secure->GetPOST('bis_datum');

    $id = $this->app->DB->Select("SELECT id FROM adresse_rolle ar WHERE id = \"$id\"");

    $von_datuml = strlen(trim($von_datum));
    $bis_datuml = strlen(trim($bis_datum));

    $von_datum = $this->app->String->Convert($von_datum,'%3.%2.%1','%1-%2-%3');
    $bis_datum = $this->app->String->Convert($bis_datum,'%3.%2.%1','%1-%2-%3');
    $fehler = '';
    if($id != '' && trim($von_datum) != '' && trim($bis_datum) != '' && $von_datuml >= 8  && $von_datuml <= 10 && $bis_datuml >= 8 && $bis_datuml <= 10 && $von_datum != '0000-00-00'){
      $this->app->DB->Update("UPDATE adresse_rolle SET von = \"$von_datum\", bis = \"$bis_datum\" WHERE id = \"$id\"");
    }else{
      if($von_datum == '0000-00-00'){
        $fehler .= 'Bitte ein gültiges von Datum angeben';
      }
      if($von_datuml < 8 || $von_datuml > 10 || $bis_datuml < 8 || $bis_datuml > 10){
        $fehler .= 'Bitte ein gültiges Datum angeben';
      }
      echo json_encode(array('status'=>0,'statusText'=>$fehler));
      $this->app->ExitXentral();
    }
    
    echo json_encode(array('status'=>1));
    $this->app->ExitXentral();
}


function AdresseUSTPopup()
{
  $frame = $this->app->Secure->GetGET('frame');
  if($frame=='false')
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(650,530);
  } else {
    $this->AdresseUstprf();
    $this->app->BuildNavigation=false;
  }
}



function AdresseUstprf()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");

  $this->app->Tpl->Set("MESSAGE","<div class=\"warning\">Hinweis: Dies ist ein Beta-Feature. Bitte pr&uuml;fen Sie die Daten und melden uns per Ticket-System, wenn etwas bei Ihnen nicht stimmt.</div>");

  $this->app->YUI->DateiPopup('DATEIENPOPUP','adresseustprf','#e_id',array(
      'openbuttontarget'=>'DATEIBUTTON'
    ,'frompopup'=>'editUstprf','afteropen'=>'AFTERPOPUPOPEN'
  ));

  $laender = $this->app->erp->GetSelectLaenderliste();
  $laenderstr = '';

  foreach($laender as $key => $value){
    if($key != "0" && $land == $key){
      $laenderstr .= '<option value="' . $key . '" selected>' . $value . '</option>';
    }else{
      $laenderstr .= '<option value="' . $key . '">' . $value . '</option>';
    }
  }

  $this->app->Tpl->Add("LAENDER", $laenderstr);

  $this->app->Tpl->Set("ADRESSID", $id);

  $this->app->YUI->CkEditor("e_mailtext", "belege");

  $this->app->YUI->TableSearch('TAB1','adresse_ustprf', 'show','','',basename(__FILE__), __CLASS__);
  $this->app->YUI->TableSearch('PROTOKOLLTABELLE','adresse_ustprf_protokoll','show','','',basename(__FILE__), __CLASS__);


  $cmd = $this->app->Secure->GetGET('cmd');
  $ustprfid = (int)$this->app->Secure->GetPOST('ustprfid');
  $adressid = (int)$this->app->Secure->GetPOST('adressid');
  $name = trim($this->app->Secure->GetPOST('name'));
  $ustid = trim($this->app->Secure->GetPOST('ustid'));
  $plz = trim($this->app->Secure->GetPOST('plz'));
  $ort = trim($this->app->Secure->GetPOST('ort'));
  $strasse = trim($this->app->Secure->GetPOST('strasse'));
  $land = trim($this->app->Secure->GetPOST('land'));

  $ustid = str_replace(' ', '', $ustid);

  $url = html_entity_decode($name." ".$ort." ".$land, ENT_QUOTES | ENT_XML1);

  $ownvatok = false;

  include_once(dirname(__DIR__).'/lib/class.ustid.php');

  switch($cmd)
  {
    case "startwerte":
      $abwreadresse = $this->app->DB->Select("SELECT abweichende_rechnungsadresse FROM adresse WHERE id = '$adressid' LIMIT 1");

      if($abwreadresse == 1){
        $data = $this->app->DB->SelectRow("SELECT id, kundennummer AS nummer, rechnung_name AS name, rechnung_land AS land, ustid, rechnung_ort AS ort, rechnung_plz AS plz, rechnung_strasse AS strasse FROM adresse WHERE id = '$adressid' LIMIT 1");
      } else{
        $data = $this->app->DB->SelectRow("SELECT id, kundennummer AS nummer, land, name, ustid, ort, plz, strasse FROM adresse WHERE id = '$adressid' LIMIT 1");
      }

      if($data){
        $data['name'] = html_entity_decode($data['name'], ENT_QUOTES | ENT_XML1);
        $url = html_entity_decode($data['name'])." ".$data['ort']." ".$data['land'];

        $data['ustid'] = str_replace(' ', '', $data['ustid']);

        $data['uststatus']="angelegt";
        $data['status']=1;
      }
      $this->app->DB->Insert("INSERT INTO ustprf (adresse, ustid, name, ort, plz, strasse, land,status,datum) VALUES ('$adressid', '".$data['ustid']."', 
        '".$this->app->DB->real_escape_string($data['name'])."', '".$this->app->DB->real_escape_string($data['ort'])."', '".$data['plz']."', 
        '".$this->app->DB->real_escape_string($data['strasse'])."', '".$data['land']."','angelegt',NOW())");


      $ustprfid = $this->app->DB->GetInsertID();
      $data['ustprfid'] = $ustprfid;

      $protokoll = "Neue Prüfung angelegt";

    break;
    case "stammdaten":
      $this->app->DB->Update("UPDATE adresse SET name = '$name', ustid = '$ustid', plz = '$plz', ort = '$ort', strasse = '$strasse', land = '$land' WHERE id = '$adressid'");
      $this->app->DB->Update("UPDATE ustprf SET ustid = '$ustid', name = '$name', ort = '$ort', plz = '$plz', strasse = '$strasse', land = '$land' WHERE id = '$ustprfid'");
      $data['status']=1;
      $data['statusText']="Stammdaten wurden aktualisiert";
      $protokoll = $data['statusText'];
      $adressdaten = $name." | ".$ort." | ".$plz." | ".$strasse." | ".$land." | ".$ustid;
    break;
    case "online":
    case "brief":
      $ustland = substr($ustid, 0, 2);
      if($ustland!=""){
        $ustlaender = $this->app->erp->GetUSTEU(true);
        if(!in_array($ustland, $ustlaender)){
          $data['status']=0;
          $data['statusText']="Dieses Land ist für die Prüfung nicht verfügbar.\nEs können nur Abfragen über Firmen in der EU durchgeführt werden.";
          break;
        }
      }

      $this->app->DB->Update("UPDATE ustprf SET ustid='".$ustid."', name='".$name."',
        ort='".$ort."', plz='".$plz."', strasse='".$strasse."', land='".$land."' WHERE id='".$ustprfid."'");

      $adressdaten = $name." | ".$ort." | ".$plz." | ".$strasse." | ".$land." | ".$ustid;

      $UstStatus = $this->app->erp->CheckUst($this->app->erp->Firmendaten("steuernummer"), $ustid ,$name,$ort,$strasse,$plz,($cmd=="brief"?"ja":"nein"));

      if(is_array($UstStatus) && !is_numeric($UstStatus)){
        //$tmp = new USTID();
        $msg = $UstStatus['ERROR_MSG'];

        if(($UstStatus['ERROR_CODE']==200 || $UstStatus['ERROR_CODE']==222) && (($UstStatus['ERG_NAME']=="A" && $UstStatus['ERG_PLZ']=="A" && $UstStatus['ERG_ORT']=="A" && $UstStatus['ERG_STR']=="A") || $cmd=="brief")){
          $data['status']=1;
          if($cmd=="brief") {
            $this->app->DB->Update("UPDATE ustprf SET status='$cmd',datum_brief=NOW(),briefbestellt=NOW() WHERE id='$ustprfid'");
            $data['statusText']="Schriftliche Mitteilung des BZSt wurde angefordert!";
          }else{
            $this->app->DB->Update("UPDATE ustprf SET status='$cmd',datum_online=NOW() WHERE id='$ustprfid'");
            $data['statusText']=$msg;
          }
        }else if($UstStatus['ERROR_CODE']==200 || $UstStatus['ERROR_CODE']==222){
          $data['status']=0;

          $this->app->DB->Update("UPDATE ustprf SET status='teilweise',datum_online=NOW() WHERE id='$ustprfid'");
          if($UstStatus['ERG_NAME']=="B") $tmpmsg .= "\nName stimmt nicht mit BZSt Eintrag überein! ";
          if($UstStatus['ERG_PLZ']=="B") $tmpmsg .= "\nPLZ stimmt nicht mit BZSt Eintrag überein! ";
          if($UstStatus['ERG_ORT']=="B") $tmpmsg .= "\nOrt stimmt nicht mit BZSt Eintrag überein! ";
          if($UstStatus['ERG_STR']=="B") $tmpmsg .= "\nStrasse stimmt nicht mit BZSt Eintrag überein! ";

          if($UstStatus['ERG_NAME']=="C") $tmpmsg .= "\nName wurde vom BZSt nicht angefragt! ";
          if($UstStatus['ERG_PLZ']=="C") $tmpmsg .= "\nPLZ wurde vom BZSt nicht angefragt! ";
          if($UstStatus['ERG_ORT']=="C") $tmpmsg .= "\nOrt wurde vom BZSt nicht angefragt! ";
          if($UstStatus['ERG_STR']=="C") $tmpmsg .= "\nStrasse wurde vom BZSt nicht angefragt! ";

          if($UstStatus['ERG_NAME']=="D") $tmpmsg .= "\nName wird vom EU-Mitgliedsstaat nicht mitgeteilt! ";
          if($UstStatus['ERG_PLZ']=="D") $tmpmsg .= "\nPLZ wird vom EU-Mitgliedsstaat nicht mitgeteilt! ";
          if($UstStatus['ERG_ORT']=="D") $tmpmsg .= "\nOrt wird vom EU-Mitgliedsstaat nicht mitgeteilt! ";
          if($UstStatus['ERG_STR']=="D") $tmpmsg .= "\nStrasse wird vom EU-Mitgliedsstaat nicht mitgeteilt! ";

          $data['ustprfid'] = $ustprfid;
          $data['adressdaten'] = $adressdaten;
          $data['error_code'] = $UstStatus['ERROR_CODE'];
          $data['statusText']=trim($tmpmsg);
        }else{
          $data['status']=0;
          $this->app->DB->Update("UPDATE ustprf SET status='fehlgeschlagen',datum_online=NOW() WHERE id='$ustprfid'");
          if($UstStatus['ERROR_CODE'] != ''){
            $data['statusText']=$msg.' (Code: '.$UstStatus['ERROR_CODE'].')';
          }else{
            $data['statusText'] = $msg.' (Code: Der Server des Bundeszentralamtes für Steuern ist derzeit nicht erreichbar.\nBitte probieren Sie es zu einem späteren Zeitpunkt noch einmal.)';
          }
        }
        $protokoll = $data['statusText'];
      }
      

      $data['uststatus'] = $this->app->DB->Select("SELECT status FROM ustprf WHERE id='$ustprfid'");
      $data['datum_online'] = $this->app->DB->Select("SELECT DATE_FORMAT(datum_online,'%d.%m.%Y') as datum_online FROM ustprf WHERE id='$ustprfid'");
      $data['datum_brief'] = $this->app->DB->Select("SELECT DATE_FORMAT(datum_brief,'%d.%m.%Y') as datum_brief FROM ustprf WHERE id='$ustprfid'");
      
      if($data['datum_online'] == '00.00.0000'){
        $data['datum_online'] = '-';
      }

      if($data['datum_brief'] == '00.00.0000'){
        $data['datum_brief'] = '-';
      }
    break;
    case "delete":
      if($ustprfid > 0){
        $this->app->DB->Delete("DELETE FROM ustprf WHERE id='" . $ustprfid . "'");
        $this->app->DB->Delete("DELETE FROM ustprf_protokoll WHERE ustprf_id='" . $ustprfid . "'");
        $data['status'] = 1;
      }
        break;


    case "get":
      $data = $this->app->DB->SelectRow("SELECT *,id as ustprfid, status as uststatus,DATE_FORMAT(datum_online,'%d.%m.%Y') as datum_online,DATE_FORMAT(datum_brief,'%d.%m.%Y') as datum_brief FROM ustprf WHERE id='".$ustprfid."'");

      $url = html_entity_decode($data['name'])." ".$data['ort']." ".$data['land'];

      if($data['datum_online'] == '00.00.0000'){
        $data['datum_online'] = '-';
      }

      if($data['datum_brief'] == '00.00.0000'){
        $data['datum_brief'] = '-';
      }
      
      $data['status']=1;
    break;

    case "gueltigmarkieren":
      if($ustprfid > 0){
        $this->app->DB->Update("UPDATE ustprf SET status='$cmd',datum_online=NOW() WHERE id='$ustprfid'");
        $protokoll = "Manuell als g&uuml;ltig markiert.";
        $adressdaten = $name." | ".$ort." | ".$plz." | ".$strasse." | ".$land." | ".$ustid;
        $data['status'] = 1;
        $data['uststatus'] = $this->app->DB->Select("SELECT status FROM ustprf WHERE id='$ustprfid'");
        $data['datum_online'] = $this->app->DB->Select("SELECT DATE_FORMAT(datum_online,'%d.%m.%Y') as datum_online FROM ustprf WHERE id='$ustprfid'");
      }else{
        $data['status'] = 0;
        $data['statusText'] = 'Keine gültige Ustprf-ID';
      }
    break;
  }
 
  if($ustprfid > 0 && $protokoll!='' && $cmd!=='delete')
  {
    $this->app->DB->Insert("INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter,logdatei, daten)  VALUES ('".$ustprfid."',NOW(),
        '$protokoll', '".$this->app->User->GetName()."',NOW(), '".$adressdaten."')");
  }

  if($cmd!='')
  { 
    $data['url'] = "http://www.google.de/search?q=$url&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:de:official&client=firefox-a";
    echo json_encode($data);
    $this->app->ExitXentral();
  }

  $this->app->Tpl->Parse('PAGE',"adresse_ustprf.tpl");
}

function AdresseProtokoll($lid)
{
  if($lid!=""){
    $table = new EasyTable($this->app);

    $table->Query("SELECT DATE_FORMAT(zeit, '%d.%m.%Y %H:%i') AS Datum, bemerkung,bearbeiter FROM ustprf_protokoll WHERE ustprf_id='$lid' ORDER BY zeit DESC", 0, "noAction");

    $table->DisplayNew('PROTOKOLL',"", "noAction");

  }
}


function AdresseVerbindlichkeiten()
{
  $this->AdresseMenu();

  $this->app->Tpl->Parse('PAGE',"adresse_verbindlichkeiten.tpl");
}

function AdresseKundeArtikel()
{
  $parameter = $this->app->User->GetParameter('table_filter_adressekundeartikel');
  $parameter = json_decode($parameter, true);

  $cmd = $this->app->Secure->GetGET('cmd');
  if($cmd==='edit') {
    $id = (int)$this->app->Secure->GetPOST('id');
    $art = (int)$this->app->Secure->GetPOST('art');
    $tabelle = "";

    if($art == 1){
      $tabelle = "auftrag";
    }elseif($art == 2){
      $tabelle = "rechnung";
    }elseif($art == 3){
      $tabelle = "gutschrift";
    }elseif($art == 4){
      $tabelle = "angebot";
    }elseif($art == 5){
      $tabelle = "lieferschein";
    }elseif($art == 6){
      $tabelle = "produktion";
    }
      
    $data = $this->app->DB->SelectRow("SELECT id, internerkommentar FROM ".$tabelle."_position WHERE id = '$id' LIMIT 1");
          
    if($data){
      $data['art'] = $tabelle;
             
    }else{
      $data['id'] = 0;
      $data['internerkommentar'] = 0;
      $data['art'] = '';
    }
    echo json_encode($data);
    $this->app->ExitXentral();

  }
  if($cmd==='save'){
    $id = $this->app->Secure->GetPOST('id');
    $internerkommentar = $this->app->Secure->GetPOST('internerkommentar');
    $art = $this->app->Secure->GetPOST('art');

    $error = '';

    if($error == ''){
      if($id > 0){
        $this->app->DB->Update("UPDATE ".$art."_position SET internerkommentar = '$internerkommentar' WHERE id = '$id'");
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
    }else{
      echo json_encode(array('status'=>0,'statusText'=>$error));
      $this->app->ExitXentral();
    }
  }


  if($this->app->Secure->GetPOST('speichern'))
  {
    $internerkommentar = $this->app->Secure->GetPOST('internerkommentar');
    $rechnung_position = (int)$this->app->Secure->GetPOST('rechnung_position');
    if($rechnung_position)
    {
      $rechnung = $this->app->DB->Select("SELECT rechnung FROM rechnung_position WHERE id = '$rechnung_position' LIMIT 1");
      if($rechnung)
      {
        $id = (int)$this->app->Secure->GetGET('id');
        if($id && $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id = '$rechnung' AND adresse = '$id' LIMIT 1"))
        {
          $this->app->DB->Update("UPDATE rechnung_position SET internerkommentar = '$internerkommentar' WHERE id = '$rechnung_position' LIMIT 1");
        }
      }
    }
  }
  $this->AdresseMenu();
  $this->app->Tpl->Parse('TAB1','adresse_kundeartikel_table_filter.tpl');
  $this->app->YUI->TableSearch('TAB1','adresseartikel');

  $this->app->YUI->TableSearch('TAB7','adresse_artikel_gekaufte', 'show','','',basename(__FILE__), __CLASS__);

  $this->app->Tpl->Parse('PAGE','adresse_artikel.tpl');
}

public function AdresseLieferantArtikel()
{
  $this->AdresseMenu();

  $this->app->YUI->TableSearch('TAB1','lieferantartikel', 'show','','',basename(__FILE__), __CLASS__);
  $this->app->Tpl->Parse('PAGE','adresse_lieferprogramm.tpl');
}

function AdresseBestellungMarkiert()
{
  $id = $this->app->Secure->GetGET('id');
  $sid = $this->app->Secure->GetPOST('sid');
  if($sid > 0){
    $geliefert = $this->app->DB->Select("SELECT geliefert FROM bestellung_position WHERE id='$sid' LIMIT 1");
    $menge = $this->app->DB->Select("SELECT menge FROM bestellung_position WHERE id='$sid' LIMIT 1");
    $tmp = $menge - $geliefert;
    if($tmp < 0){
      $tmp = 0;
    }
    $this->app->DB->Update("UPDATE bestellung_position SET abgeschlossen='1', mengemanuellgeliefertaktiviert='$tmp', geliefert='$menge',manuellgeliefertbearbeiter='" . $this->app->User->GetName() . "' WHERE id='$sid' LIMIT 1");
  }
  echo json_encode(array('status'=>1));
  $this->app->ExitXentral();
}


function AdresseOffeneBestellungen()
{
  $id = $this->app->Secure->GetGET('id');
  $this->app->Tpl->Set('ID',$id);

  $this->AdresseMenu();

  $this->app->Tpl->Set('UEBERSCHRIFT1','Bestellungen');

  $this->app->YUI->TableSearch('TAB1', 'adressebestellungen');

  $this->app->Tpl->Set('TAB1SELECT','selected');
  $this->app->Tpl->Set('EXTEND','');
  $this->app->Tpl->Set('INHALT','');

  $this->app->YUI->TableSearch('TAB2', 'adressebestellungen_artikel');
  $this->app->Tpl->Parse('PAGE', 'adressebestellung.tpl');
}

function AdresseAccounts()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET('id');
  $lid = $this->app->Secure->GetGET("lid");
  $cmd = $this->app->Secure->GetGET('cmd');

  $iframe = $this->app->Secure->GetGET("iframe");


  if($cmd === "save"){
    $bearbeitungsid = (int)$this->app->Secure->GetPOST('id');
    $bezeichnung = trim($this->app->Secure->GetPOST('bezeichnung'));
    $benutzername = trim($this->app->Secure->GetPOST('benutzername'));
    $passwort = trim($this->app->Secure->GetPOST('passwort'));
    $art = trim($this->app->Secure->GetPOST('art'));
    $url = trim($this->app->Secure->GetPOST('url'));
    $email = trim($this->app->Secure->GetPOST('email'));
    $notiz = trim($this->app->Secure->GetPOST('notiz'));
    $gueltigab = trim($this->app->Secure->GetPOST('gueltigab'));
    $gueltigbis = trim($this->app->Secure->GetPOST('gueltigbis'));
    $aktiv = $this->app->Secure->GetPOST('aktiv');
    $adressid = (int)$this->app->Secure->GetPOST('adressid');
    
    $error = "";

    if($bezeichnung == ""){
      $error .= "Bitte Bezeichnung ausfüllen\n";
    }

    if($gueltigab == "" || $gueltigab == "0000-00-00"){
      $gueltigab = "0000-00-00"; 
    }else{
      $gueltigab = date('Y-m-d',strtotime($gueltigab));
    }

    if($gueltigbis == "" || $gueltigbis == "0000-00-00"){
      $gueltigbis = "0000-00-00";
    }else{
      $gueltigbis = date('Y-m-d',strtotime($gueltigbis));
    }
    
    if($error == ''){
      if($bearbeitungsid){
        $this->app->DB->Update("UPDATE adresse_accounts SET bezeichnung = '$bezeichnung', benutzername = '$benutzername', passwort = '$passwort', art = '$art', url = '$url', email = '$email', notiz = '$notiz', gueltig_ab = '$gueltigab', gueltig_bis = '$gueltigbis', aktiv = '$aktiv' WHERE id = '$bearbeitungsid'");
                 
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
      $this->app->DB->Insert("INSERT INTO adresse_accounts (bezeichnung, benutzername, passwort, art, url, email, notiz, gueltig_ab, gueltig_bis, aktiv, adresse) VALUES ('$bezeichnung', '$benutzername', '$passwort', '$art', '$url', '$email', '$notiz', '$gueltigab', '$gueltigbis', '$aktiv', '$adressid')");
      echo json_encode(array('status'=>1));
      $this->app->ExitXentral();
    }
    echo json_encode(array('status'=>0,'statusText'=>$error));
    $this->app->ExitXentral();



  }
  if($cmd === 'edit'){

    $bearbeitungsid = (int)$this->app->Secure->GetPOST('id');

    $data = $this->app->DB->SelectRow("SELECT id, bezeichnung, benutzername, passwort, art, url, email, notiz, gueltig_ab, gueltig_bis, aktiv FROM adresse_accounts WHERE id = '$bearbeitungsid' LIMIT 1");
      
    if($data){
      if($data['gueltig_ab'] == "0000-00-00"){
        $data['gueltig_ab'] = "";
      }else{
        $data['gueltig_ab'] = date('d.m.Y',strtotime($data['gueltig_ab']));
      }
      if($data['gueltig_bis'] == "0000-00-00"){
        $data['gueltig_bis'] = "";
      }else{
        $data['gueltig_bis'] = date('d.m.Y',strtotime($data['gueltig_bis']));
      }      
    }else{
      $data['id'] = 0;
      $data['bezeichnung'] = '';
      $data['benutzername'] = '';
      $data['passwort'] = '';
      $data['art'] = '';
      $data['url'] = '';
      $data['email'] = '';
      $data['notiz'] = '';
      $data['gueltig_ab'] = '';
      $data['gueltig_bis'] = '';
      $data['aktiv'] = 0;
    }
    echo json_encode($data);
    $this->app->ExitXentral();

  }
  if($cmd === 'delete'){
    $loeschid = (int) $this->app->Secure->GetPOST('eid');
    $this->app->DB->Update("DELETE FROM adresse_accounts WHERE id = '$loeschid' LIMIT 1");
        
    echo json_encode(array('status'=>1));
    $this->app->ExitXentral();
  }




  if($iframe=="true")
    $this->app->BuildNavigation=false;

  if($delete==1)
  {
    $this->app->DB->Delete("DELETE FROM adresse_accounts WHERE id='$lid' AND adresse='$id' LIMIT 1");
    $this->app->Location->execute("index.php?module=adresse&action=accounts$add_cmd&id=$id&iframe=$iframe");
  }
  
  $this->app->YUI->AutoComplete("e_art", "accountart");
  $this->app->YUI->CkEditor("e_notiz","basic",array('height'=>'11em', 'width'=>'35em'));
  $this->app->YUI->DatePicker('e_gueltigab');
  $this->app->YUI->DatePicker('e_gueltigbis');


  //Formula lieferadresse
  // easy table mit arbeitspaketen YUI als template 
  $this->app->YUI->TableSearch('TAB1',"adresse_accounts");
  //$this->app->Tpl->Parse('TAB1',"rahmen70.tpl");
  $this->app->Tpl->Set('AKTIV_TAB1',"selected");

  if($iframe=="true")
    $this->app->Tpl->Parse('PAGE',"adresse_accounts_uebersicht_popup.tpl");
  else
    $this->app->Tpl->Parse('PAGE',"adresse_accounts_uebersicht.tpl");

}


function AdresseAnsprechpartnerPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET('id');
  $cmd= $this->app->Secure->GetPOST('cmd');

  if($cmd=="alslieferadresse")
    $als_lieferadresse=true;
  else
    $als_lieferadresse=false;


  $this->AdresseAnsprechpartner($als_lieferadresse);
  $this->app->BuildNavigation=false;
}

function AdresseAnsprechpartnerLieferadressePopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET('id');

  $this->AdresseAnsprechpartner(true);
  $this->app->BuildNavigation=false;
}

function AdresseStammdatenLieferadressePopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET('id');

  $this->AdresseStammdaten(true);
  $this->app->BuildNavigation=false;
}



function AdresseStammdaten($als_lieferadresse=false)
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET('id');
  $lid = $this->app->Secure->GetGET("lid");
  $delete = $this->app->Secure->GetGET("delete");
  $create= $this->app->Secure->GetGET("create");
  $cmd= $this->app->Secure->GetPOST('cmd');

  $als_verzollungsadresse = false;
  $als_ansprechpartner = false;
  if($cmd=="alslieferadresse")
  {
    $this->app->Tpl->Set('STARTDISABLEANREDE',"<!--");
    $this->app->Tpl->Set('ENDEDISABLEANREDE',"-->");
    $als_lieferadresse=true;
    $add_cmd = "&cmd=alslieferadresse";
  }elseif($cmd == "alsverzollungadresse")
  {
    $this->app->Tpl->Set('STARTDISABLEANREDE',"<!--");
    $this->app->Tpl->Set('ENDEDISABLEANREDE',"-->");
    $als_verzollungsadresse=true;
    $add_cmd = "&cmd=alsverzollungadresse";
  } elseif($cmd == "alsansprechpartner")
  {
    $this->app->Tpl->Set('STARTDISABLEANREDE',"<!--");
    $this->app->Tpl->Set('ENDEDISABLEANREDE',"-->");
    $als_ansprechpartner=true;
    $add_cmd = "&cmd=alsansprechpartner";
  }
  

  $iframe = $this->app->Secure->GetGET("iframe");

  if($iframe=="true")
    $this->app->BuildNavigation=false;

  // neues arbeitspaket

  if($iframe=="true") {
    if($als_lieferadresse)
    {
      $einfuegen = "<a onclick=\"AnsprechpartnerLieferadresse('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
    }elseif($als_verzollungsadresse)
    {
      $einfuegen = "<a onclick=\"Verzolladresse('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
    }elseif($als_ansprechpartner)
    {
      $einfuegen = "<a onclick=\"AdresseAnsprechpartner('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
    }
    else
      $einfuegen = "<a onclick=\"Ansprechpartner('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
  }
  //Formula lieferadresse
  // easy table mit arbeitspaketen YUI als template 
  if($als_lieferadresse)
  {
    $this->app->YUI->TableSearch('TAB1',"adresse_stammdatenlieferadresselist");
  }elseif($als_verzollungsadresse)
  {
    $this->app->YUI->TableSearch('TAB1',"adresse_stammdatenverzollungadresselist");
  }
  else
    $this->app->YUI->TableSearch('TAB1',"adresse_ansprechpartnerlist"); //TODO fehlt
  //$this->app->Tpl->Parse('TAB1',"rahmen70.tpl");
  $this->app->Tpl->Set('AKTIV_TAB1',"selected");

  if($iframe=="true")
    $this->app->Tpl->Parse('PAGE',"ansprechpartneruebersicht_popup.tpl");
  else
    $this->app->Tpl->Parse('PAGE',"ansprechpartneruebersicht.tpl");
}


function AdresseAnsprechpartner($als_lieferadresse=false)
{

  $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
  $lid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('lid'));
  
  if($this->app->Secure->GetGET('cmd')==='changegr') {
    $gruppe = (int)$this->app->Secure->GetPOST('gruppe');
    $wert = $this->app->Secure->GetPOST('wert')?1:0;
    if($id && $lid && $gruppe)
    {
      $check = $this->app->DB->Select("SELECT id FROM ansprechpartner_gruppen WHERE gruppe = '$gruppe' AND ansprechpartner = '$lid' LIMIT 1");
      if($check)
      {
        $this->app->DB->Update("UPDATE ansprechpartner_gruppen SET aktiv = '$wert' WHERE id = '$check' LIMIT 1");
      }elseif($wert){
        $this->app->DB->Insert("INSERT INTO ansprechpartner_gruppen (gruppe, ansprechpartner, aktiv) VALUES ('$gruppe', '$lid', 1)");
      }
    }
    echo json_decode(array('status'=>1));
    $this->app->ExitXentral();
  }

  $delete = $this->app->Secure->GetGET("delete");
  $create= $this->app->Secure->GetGET("create");
  $cmd = $this->app->Secure->GetGET('cmd');

  //$ansprechpartnerspeichern = "";
  $ansprechpartnerspeichern = $this->app->Secure->GetPOST('ansprechpartnerspeichern');
  if($ansprechpartnerspeichern != ""){
    $cmd = "save"; 
  }

  switch ($cmd) {
    case 'get':
      
      $data = $this->app->DB->SelectRow("SELECT * FROM ansprechpartner WHERE id = '$lid'");
      $data['geburtstag'] = $this->app->String->Convert($data['geburtstag'],"%1-%2-%3","%3.%2.%1");
      echo json_encode($data);
      $this->app->ExitXentral();
      break;
    case 'save':
      $lid = $this->app->Secure->GetPOST('lid');
      $typ = $this->app->Secure->GetPOST('typ');
      $land = $this->app->Secure->GetPOST('land');
      $name = $this->app->Secure->GetPOST('name');
      $titel = $this->app->Secure->GetPOST('titel');
      $bereich = $this->app->Secure->GetPOST('bereich');
      $abteilung = $this->app->Secure->GetPOST('abteilung');
      $unterabteilung = $this->app->Secure->GetPOST('unterabteilung');
      $adresszusatz = $this->app->Secure->GetPOST('adresszusatz');
      $anschreiben = $this->app->Secure->GetPOST('anschreiben');
      $vorname = $this->app->Secure->GetPOST('vorname');
      $geburtstag = $this->app->String->Convert($this->app->Secure->GetPOST('geburtstag'),"%3.%2.%1","%1-%2-%3");
      $geburtstagkalender = $this->app->Secure->GetPOST('geburtstagkalender');
      $geburtstagskarte = $this->app->Secure->GetPOST('geburtstagskarte');
      $marketingsperre = $this->app->Secure->GetPOST('marketingsperre');
      $strasse = $this->app->Secure->GetPOST('strasse');
      $plz = $this->app->Secure->GetPOST('plz');
      $ort = $this->app->Secure->GetPOST('ort');
      $email = $this->app->Secure->GetPOST('email');
      $telefon = $this->app->Secure->GetPOST('telefon');
      $telefax = $this->app->Secure->GetPOST('telefax');
      $mobil = $this->app->Secure->GetPOST('mobil');
      $sonstiges = $this->app->Secure->GetPOST('sonstiges');
      $internebemerkung = $this->app->Secure->GetPOST('interne_bemerkung');

      $error = "";

      if(trim($name) == ""){
        $error .= "Bitte Name ausfüllen"."\n";
      }

      if($error == ""){
        if($lid == '0'){
          //Neuen Eintrag anlegen
          $this->app->DB->Insert("INSERT INTO ansprechpartner (typ, name, bereich, abteilung, unterabteilung, land, strasse, ort, plz, telefon, telefax, email, sonstiges, adresszusatz, adresse, logdatei, mobil, titel, anschreiben, ansprechpartner_land, vorname, geburtstag, geburtstagkalender, geburtstagskarte, marketingsperre, interne_bemerkung) VALUES ('$typ', '$name', '$bereich','$abteilung','$unterabteilung','$land','$strasse','$ort','$plz','$telefon','$telefax', '$email', '$sonstiges', '$adresszusatz','$id',NOW(),'$mobil','$titel','$anschreiben','$land','$vorname','$geburtstag', '$geburtstagkalender', '$geburtstagskarte', '$marketingsperre', '$internebemerkung')");
          $lid = $this->app->DB->GetInsertID();
          if (!empty($lid)) {
            $this->app->erp->RunHook('contact_person_created', 1, $lid);
          }
          $gruppen = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('gruppen'));
          $gruppenarray = explode("|", $gruppen);
 
          for ($i=0; $i < count($gruppenarray) ; $i++) { 
            if($gruppenarray[$i] != ''){
              $gruppenid = substr($gruppenarray[$i], 3);
              $this->app->DB->Insert("INSERT INTO ansprechpartner_gruppen (ansprechpartner, gruppe, aktiv) VALUES ('$lid', '".$gruppenid."', '1')");
            }
          }
          if($ansprechpartnerspeichern == ""){
            echo json_encode(array('status'=>1));
            $this->app->ExitXentral();
          }
        }else{
          //Alten Eintrag updaten
          $this->app->DB->Update("UPDATE ansprechpartner SET typ ='$typ', name = '$name', bereich = '$bereich', abteilung = '$abteilung', unterabteilung = '$unterabteilung', land = '$land', strasse = '$strasse', ort = '$ort', plz = '$plz', telefon = '$telefon', telefax = '$telefax', email = '$email', sonstiges = '$sonstiges', adresszusatz = '$adresszusatz', mobil = '$mobil', titel = '$titel', anschreiben = '$anschreiben', land = '$land', vorname = '$vorname', geburtstag = '$geburtstag', geburtstagkalender =  '$geburtstagkalender', geburtstagskarte = '$geburtstagskarte', marketingsperre = '$marketingsperre', interne_bemerkung = '$internebemerkung' WHERE id = '$lid'");
          $this->app->erp->RunHook('contact_person_updated', 1, $lid);
          if($ansprechpartnerspeichern == ''){
            echo json_encode(array('status'=>1));
            $this->app->ExitXentral();
          }
        }
      }else{
        if($ansprechpartnerspeichern == ''){
          echo json_encode(array('status'=>0,'statusText'=>$error));
          $this->app->ExitXentral();
        }
      }
      
      break;
     case 'change':
      $lid = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('lid'));
      $wert = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('wert'));
      $gruppe = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('gruppe'));
      if($lid != 0){
          $vorhanden = $this->app->DB->Select("SELECT id FROM ansprechpartner_gruppen WHERE ansprechpartner = '$lid' AND gruppe = '$gruppe'");
        if($vorhanden){
          $this->app->DB->Update("UPDATE ansprechpartner_gruppen SET aktiv = $wert WHERE ansprechpartner = '$lid' AND gruppe = '$gruppe'");
        }else{
          $this->app->DB->Insert("INSERT INTO ansprechpartner_gruppen (ansprechpartner, gruppe, aktiv) VALUES ('$lid', '$gruppe', '$wert')");
        }
      }
      echo json_encode('success');
      $this->app->ExitXentral();
      break;          
    case 'delete':
      $lid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('lid'));
      $this->app->erp->RunHook('contact_person_deleted', 1, $lid);
      $this->app->DB->Delete("DELETE FROM ansprechpartner WHERE id = '$lid'");
      echo json_encode('success');
      $this->app->ExitXentral();
      break;
    default:
      break;
  }


  if($cmd=="alslieferadresse")
  {
    $this->app->Tpl->Set('STARTDISABLEANREDE',"<!--");
    $this->app->Tpl->Set('ENDEDISABLEANREDE',"-->");
    $als_lieferadresse=true;
    $add_cmd = "&cmd=alslieferadresse";
  }

  $iframe = $this->app->Secure->GetGET("iframe");

  if($iframe=="true")
    $this->app->BuildNavigation=false;


  if($delete==1)
  {
    $this->app->erp->RunHook('contact_person_deleted', 1, $lid);
    $this->app->DB->Delete("DELETE FROM ansprechpartner WHERE id='$lid' AND adresse='$id' LIMIT 1");
    $this->app->Location->execute("index.php?module=adresse&action=ansprechpartner$add_cmd&id=$id&iframe=$iframe");
  }
  $this->AdresseMenu();
  if($iframe != "true")
  {
    /*
    $gruppen = $this->app->DB->SelectArr("SELECT ag.aktiv as agaktiv, gr.* FROM gruppen gr LEFT JOIN ansprechpartner_gruppen ag ON gr.id = ag.gruppe and ag.ansprechpartner = '$lid' WHERE art != 'preisgruppe'");
    if($gruppen)
    {
      $this->app->Tpl->Add('GRUPPEN','<table>');
      
      foreach($gruppen as $gruppe)
      {
        $this->app->Tpl->Add('GRUPPEN','<tr><td><input type="checkbox" onchange="grchange('.$gruppe['id'].', this);" id="gr_'.$gruppe['id'].'" name="gr_'.$gruppe['id'].'" '.($gruppe['agaktiv']?' checked="checked" ':'').' '.($lid?'':' disabled ').' /></td><td>'.$gruppe['name'].'</td></tr>');
      }
      $this->app->Tpl->Add('GRUPPEN','</table>');
    }*/

    $this->app->YUI->TableSearch('GRUPPEN','adresse_ansprechpartnergruppen', "show","","",basename(__FILE__), __CLASS__);
    $this->app->Tpl->Add('GRUPPEN','<div style="overflow:hidden;width:0;height:0;padding:0;margin:0;"><input type="text" value="'.$lid.'" id="filterlid" name="filterlid" /></div>
      <script>$(document).ready(function() { $(\'#filterlid\').trigger(\'change\'); });
      </script>
      ');
    
    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Set('LID',$lid);
  } else {
    $this->app->Tpl->Set('GRUPPEAUSBLENDENSTART',"<!--");
    $this->app->Tpl->Set('GRUPPEAUSBLENDENENDE',"-->");
  }

  $this->app->YUI->DatePicker("geburtstag");
  

  // neues arbeitspaket
  /*
  $widget = new WidgetAnsprechpartner($this->app,'TAB1');
  $widget->form->SpecialActionAfterExecute("none",
      "index.php?module=adresse&action=ansprechpartner&id=$id&iframe=".$iframe.$add_cmd);
  if($lid > 0)
  {


    $this->app->Tpl->Set('BUTTON','
        <input type="button" onclick="copyTextToClipboard(\''.$this->app->erp->AdresseAnschriftString($lid,true).'\')" value="Adresse in Zwischenspeicher">
      ');

    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=adresse&action=ansprechpartner&id=$id&iframe=".$iframe.$add_cmd);
    $widget->Edit();
  }
  else
  {
    $widget->Create();
  }*/

  if($iframe=="true") {
    if($als_lieferadresse)
      $einfuegen = "<a onclick=\"AnsprechpartnerLieferadresse('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
    else
      $einfuegen = "<a onclick=\"Ansprechpartner('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
  }
  //Formula lieferadresse
  // easy table mit arbeitspaketen YUI als template 
  /*if($als_lieferadresse)
  {
    $this->app->Tpl->Add("TAB1","<fieldset><legend>Filter</legend><input type=\"checkbox\" name=\"alle\" id=\"alle\">&nbsp;auf alle Ansprechpartner zugreifen</fieldset>");
    $this->app->YUI->TableSearch('TAB1',"adresse_ansprechpartnerlieferadresselist");
  }
  else*/
    $this->app->YUI->TableSearch('TAB1',"adresse_ansprechpartnerlist");
  //$this->app->Tpl->Parse('TAB1',"rahmen70.tpl");
  $this->app->Tpl->Set('AKTIV_TAB1',"selected");
  
  $this->app->Tpl->Set('ADRESSID',$id); 
  
  $adresstypen = $this->app->DB->SelectArr("SELECT type, bezeichnung FROM adresse_typ WHERE aktiv = 1 AND geloescht = 0".$this->app->erp->ProjektRechte());

  $laender = $this->app->erp->GetSelectLaenderliste();

  if($lid > 0){
    $adresseland = $this->app->DB->Select("SELECT land FROM ansprechpartner WHERE id = '$lid' LIMIT 1");
  }else{
    $adresseland = $this->app->DB->Select("SELECT land FROM adresse WHERE id = '$id' LIMIT 1");
  }
  
  if($adresseland == ""){
    $this->app->erp->Firmendaten('land');
  }


  if($adresstypen != ""){
    $lieferadressetyp = "";
    foreach($adresstypen as $key=>$value){
      $lieferadressetyp .= '<option value="'.$value['type'].'">'.$value['bezeichnung'].'</option>';
    }    
  }

  if($laender != ""){
    $lieferadresselaender = "";
    foreach ($laender as $landKey => $land) {
      $lieferadresselaender .= '<option '.($adresseland == $landKey?' selected="selected" ':'').' value="' . $landKey . '">' . $land . '</option>';
    }
  }
    
  if($iframe=="true"){
    $this->app->Tpl->Set("ANSPRECHPARTNERTYP", $lieferadressetyp);
  }
    
  $this->app->Tpl->Add("LANDSELECTED", $adresseland);
  $this->app->Tpl->Add("ANSPRECHPARTNERLAENDER", $lieferadresselaender);


  $this->app->YUI->CkEditor("interne_bemerkung", "basic");


  if($iframe=="true")
    $this->app->Tpl->Parse('PAGE',"ansprechpartneruebersicht_popup.tpl");
  else
    $this->app->Tpl->Parse('PAGE',"ansprechpartneruebersicht.tpl");
}

function AdresseAnsprechpartnerEditPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET('id');

  if($frame=="false")
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(600,320);
  } else {
    // nach page inhalt des dialogs ausgeben
    $widget = new WidgetAnsprechpartner($this->app,'PAGE');
    $adresse = $this->app->DB->Select("SELECT adresse FROM ansprechpartner WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=adresse&action=ansprechpartner&id=$adresse");

    $widget->Edit();
    $this->app->BuildNavigation=false;
  }
}




function AdresseLieferadressePopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET('id');
  $this->AdresseLieferadresse();
  $this->app->BuildNavigation=false;
}


function AdresseLieferadresse()
{
  $this->AdresseMenu();
  $id = $this->app->DB->real_escape_string($this->app->Secure->GetGET('id'));
  $lid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('lid'));

  $delete = $this->app->Secure->GetGET("delete");
  $create = $this->app->Secure->GetGET("create");
  $module = $this->app->Secure->GetGET("module");
  $action = $this->app->Secure->GetGET("action");
  $iframe = $this->app->Secure->GetGET("iframe");

  $cmd = $this->app->Secure->GetGET('cmd');

  $lieferadressespeichern = $this->app->Secure->GetPOST('lieferadressespeichern');

  if($lieferadressespeichern != ""){
    $cmd = "save"; 
  }

  switch ($cmd) {
    case 'get':
      $data = $this->app->DB->SelectRow("SELECT * FROM lieferadressen WHERE id = '$lid'");
      echo json_encode($data);
      $this->app->ExitXentral();
      break;
    case 'save':
      $lid = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('lid'));
      $typ = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('typ'));
      $name = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('name'));
      $abteilung = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('abteilung'));
      $unterabteilung = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('unterabteilung'));
      $land = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('land'));
      $strasse = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('strasse'));
      $ort = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('ort'));
      $plz = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('plz'));
      $telefon = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('telefon'));
      $email = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('email'));
      $adresszusatz = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('adresszusatz'));
      $standardlieferadresse = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('standardlieferadresse'));
      $gln = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('gln'));
      $ustid = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('ustid'));
      $lieferbedingung = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('lieferbedingung'));
      $ust_befreit = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('ust_befreit'));
      $internebemerkung = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('interne_bemerkung'));
      $hinweis = $this->app->DB->real_escape_string($this->app->Secure->GetPOST('hinweis'));

      $error = "";

      if(trim($name) == ""){
        $error .= "Bitte Name ausfüllen"."\n";
      }

      if($standardlieferadresse == 1){
        $this->app->DB->Update("UPDATE lieferadressen SET standardlieferadresse = 0 WHERE adresse='$id'");
      }

      if($lieferadressespeichern != ''){
        if($error != ''){
          $this->app->Tpl->Add("MESSAGE", "<div class=\"error\">$error</div>");
        }else{
          if($lid == '0'){
            //Neuen Eintrag anlegen
            $this->app->DB->Insert("INSERT INTO lieferadressen (typ,name,abteilung,unterabteilung,land,strasse,ort,plz,adresse,telefon,email,adresszusatz,logdatei,standardlieferadresse,gln,ustid,lieferbedingung,ust_befreit,interne_bemerkung,hinweis) VALUES ('$typ','$name','$abteilung','$unterabteilung','$land','$strasse','$ort','$plz','$id','$telefon','$email','$adresszusatz',NOW(),'$standardlieferadresse','$gln','$ustid','$lieferbedingung','$ust_befreit','$internebemerkung','$hinweis')");
          }else{
            //Alten Eintrag updaten
            $this->app->DB->Update("UPDATE lieferadressen SET typ='$typ',name='$name',abteilung='$abteilung',unterabteilung='$unterabteilung',land='$land', strasse='$strasse',ort='$ort',plz='$plz',telefon='$telefon',email='$email',adresszusatz='$adresszusatz',standardlieferadresse='$standardlieferadresse',gln='$gln',ustid='$ustid',lieferbedingung='$lieferbedingung',ust_befreit='$ust_befreit',interne_bemerkung = '$internebemerkung',hinweis='$hinweis' WHERE id = '$lid'");     
          }
        }
      }else{
        if($error == ''){
          if($lid == '0'){
            //Neuen Eintrag anlegen
            $this->app->DB->Insert("INSERT INTO lieferadressen (typ,name,abteilung,unterabteilung,land,strasse,ort,plz,adresse,telefon,email,adresszusatz,logdatei,standardlieferadresse,gln,ustid,lieferbedingung,ust_befreit,interne_bemerkung,hinweis) VALUES ('$typ','$name','$abteilung','$unterabteilung','$land','$strasse','$ort','$plz','$id','$telefon','$email','$adresszusatz',NOW(),'$standardlieferadresse','$gln','$ustid','$lieferbedingung','$ust_befreit','$internebemerkung','$hinweis')");
            echo json_encode(array('status'=>1));
            $this->app->ExitXentral();
          }
          //Alten Eintrag updaten
          $this->app->DB->Update("UPDATE lieferadressen SET typ='$typ',name='$name',abteilung='$abteilung',unterabteilung='$unterabteilung',land='$land', strasse='$strasse',ort='$ort',plz='$plz',telefon='$telefon',email='$email',adresszusatz='$adresszusatz',standardlieferadresse='$standardlieferadresse',gln='$gln',ustid='$ustid',lieferbedingung='$lieferbedingung',ust_befreit='$ust_befreit',interne_bemerkung = '$internebemerkung',hinweis='$hinweis' WHERE id = '$lid'");
          echo json_encode(array('status'=>1));
          $this->app->ExitXentral();

        }
        echo json_encode(array('status'=>0,'statusText'=>$error));
        $this->app->ExitXentral();

      }

      
      break;
    case 'delete':
      $lid = $this->app->DB->real_escape_string($this->app->Secure->GetGET('lid'));
      $this->app->DB->Delete("DELETE FROM lieferadressen WHERE id = '$lid'");     
      echo json_encode("success");
      $this->app->ExitXentral();
      break;
    default:
      break;
  }


  if($iframe=="true"){
    $this->app->BuildNavigation = false;
  }

  if($iframe=="true") {
    $einfuegen = "<a onclick=\"Lieferadresse('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
  }

  $this->app->Tpl->Set('ADRESSID',$id); 

  $adresstypen = $this->app->DB->SelectArr("SELECT type, bezeichnung FROM adresse_typ WHERE aktiv = 1 AND geloescht = 0".$this->app->erp->ProjektRechte());

  $laender = $this->app->erp->GetSelectLaenderliste();

  if($lid > 0){
    $adresseland = $this->app->DB->Select("SELECT land FROM lieferadressen WHERE id = '$lid' LIMIT 1");
  }else{
    $adresseland = $this->app->DB->Select("SELECT land FROM adresse WHERE id = '$id' LIMIT 1");
  }
  
  if($adresseland == ""){
    $this->app->erp->Firmendaten('land');
  }

  if($adresstypen != ""){
    $lieferadressetyp = "";
    foreach($adresstypen as $key=>$value){
      $lieferadressetyp .= '<option value="'.$value['type'].'">'.$value['bezeichnung'].'</option>';
    }    
  }

  if($laender != ""){
    $lieferadresselaender = "";
    foreach ($laender as $landKey => $land) {
      $lieferadresselaender .= '<option '.($adresseland == $landKey?' selected="selected" ':'').' value="' . $landKey . '">' . $land . '</option>';
    }
  }
  
  if($iframe=="true"){
    $this->app->Tpl->Set("LIEFERADRESSETYP", $lieferadressetyp);
  }
  $this->app->Tpl->Add("LANDSELECTED", $adresseland);
  $this->app->Tpl->Add("LIEFERADRESSELAENDER", $lieferadresselaender);
  

  $this->app->YUI->AutoComplete("lieferbedingung", "lieferbedingungen");
  $this->app->YUI->CkEditor("interne_bemerkung", "basic");
  $this->app->YUI->CkEditor("hinweis", "basic");


  $this->app->Tpl->Add("TAB1",

    '<div class="filter-box filter-usersave">
			<div class="filter-block filter-inline">
				<div class="filter-title">{|Filter|}</div>
				<ul class="filter-list">
					<li class="filter-item">
					  <label for="alle" class="switch">
					    <input type="checkbox" name="alle" id="alle">
					    <span class="slider round"></span>
					  </label>
					  <label for="alle">{|auf alle Lieferadressen zugreifen|}</label>
					</li>
				</ul>
			</div>
		</div>');


  $this->app->YUI->TableSearch('TAB1',"adresse_lieferadressenlist");
  //  $this->app->Tpl->Parse('TAB1',"rahmen70.tpl");
  $this->app->Tpl->Set('AKTIV_TAB1',"selected");

  if($iframe=="true"){
    $this->app->Tpl->Parse('PAGE', "lieferadressenuebersicht_popup.tpl");
  }
  else{
    $this->app->Tpl->Parse('PAGE', "lieferadressenuebersicht.tpl");
  }
}

function AdresseLieferadressenEditPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET('id');

  if($frame=="false")
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(600,320);
  } else {
    // nach page inhalt des dialogs ausgeben
    $widget = new WidgetLieferadressen($this->app,'PAGE');
    $adresse = $this->app->DB->Select("SELECT adresse FROM lieferadressen WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute("close_refresh", "index.php?module=adresse&action=lieferadresse&id=$adresse");

    $widget->Edit();
    $this->app->BuildNavigation=false;
  }
}

function DruckerSelect($selected='') {
  if($selected=='') {
    $selected = $this->app->DB->Select("SELECT standarddrucker FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
  }

  $drucker = $this->app->DB->SelectArr("SELECT id, name FROM  drucker WHERE firma='".$this->app->User->GetFirma()."' AND aktiv='1'");
  if(empty($drucker)) {
    return '';
  }
  $out = '';
  foreach($drucker as $dr) {
    $mark = '';
    if($dr['id']==$selected) {
      $mark='selected';
    }
    $out .="<option value=\"{$dr['id']}\" $mark>{$dr['name']}</option>";
  }
  return $out;
}

function AdresseBriefDrucken() {

  $id = $this->app->Secure->GetGET('id');
  $type = $this->app->Secure->GetGET('type');

  if($type === 'kalender') {
    $dokument = $this->app->DB->SelectRow(
      sprintf(
        "SELECT
          *, DATE_FORMAT(von, '%%d.%%m.%%Y') as datumvon, DATE_FORMAT(bis, '%%d.%%m.%%Y') as datumbis,
          DATE_FORMAT(von, '%%H:%%i') as zeitvon, DATE_FORMAT(bis, '%%H:%%i') as zeitbis
          FROM  kalender_event
          WHERE id = %d",
        $id
      )
    );
  }
  elseif($type === 'wiedervorlage') {
    $dokument = $this->app->DB->SelectRow(
      sprintf(
        "SELECT *,
            DATE_FORMAT(datum_angelegt, '%%d.%%m.%%Y') as datum_angelegt, 
            DATE_FORMAT(datum_erinnerung, '%%d.%%m.%%Y') as datum_erinnerung,
            DATE_FORMAT(zeit_angelegt, '%%H:%%i') as zeit_angelegt, 
            DATE_FORMAT(zeit_erinnerung, '%%H:%%i') as zeit_erinnerung
          FROM wiedervorlage
          WHERE  id = %d",
        $id
      )
    );
  }
  else{
    $dokument = $this->app->DB->SelectRow(
      sprintf(
        "SELECT *, DATE_FORMAT(datum, '%%d.%%m.%%Y') as datum
        FROM dokumente
        WHERE id = %d",
        $id
      )
    );
  }

  if (empty($dokument)) {
    echo 'Korrespondenz noch nicht gespeichert.';
    $this->app->ExitXentral();
  }

  $adresse = $this->app->DB->SelectRow(
    sprintf(
      'SELECT *
       FROM adresse
       WHERE
       id = %d',
      ($type !== 'kalender' && $type !== 'wiedervorlage')?$dokument['adresse_to']:$dokument['adresse']
    )
  );

  if ($this->app->erp->Firmendaten('modul_mlm')) {
    $sponsor = $this->app->DB->SelectRow(
      sprintf(
        'SELECT *
        FROM adresse
        WHERE id = %d' , $adresse['sponsor']
      )
    );
    if ($sponsor) {
      $sponsor = $sponsor['kundennummer'] . ' ' . $sponsor['name'];
    }
    else {
      $sponsor = 'Kein Sponsor';
    }

    $table = '';
    $table .= '<tr>';
      $table .= '<th align="left" style="border-bottom: 2px solid #d7d7d7;">Vertragsbeginn:</th>';
      $table .= '<td style="border-bottom: 2px solid #d7d7d7;"> ' . $adresse['mlmvertragsbeginn'] . '</td>';
      $table .= '<th align="left" style="border-bottom: 2px solid #d7d7d7;">Sponsor:</th>';
      $table .= '<td style="border-bottom: 2px solid #d7d7d7;"> ' . $sponsor . '</td>';
    $table .= '</tr>';

    $this->app->Tpl->Add('SPONSOR',$table);
  }


  $this->app->Tpl->Add('KUNDENNUMMER',$adresse['kundennummer']);
  $this->app->Tpl->Add('ANREDE', ucfirst($adresse['typ']));
  $this->app->Tpl->Add('NAME',$adresse['name']);
  $this->app->Tpl->Add('ANSPRECHPARTNER',$dokument['ansprechpartner']);
  $this->app->Tpl->Add('TELEFON',$adresse['telefon']);
  $this->app->Tpl->Add('TELEFAX',$adresse['telefax']);
  $this->app->Tpl->Add('TITEL',$adresse['titel']);
  $this->app->Tpl->Add('ANSCHREIBEN',$adresse['anschreiben']);
  $this->app->Tpl->Add('ABTEILUNG',$adresse['abteilung']);
  $this->app->Tpl->Add('EMAIL',$adresse['email']);
  $this->app->Tpl->Add('UNTERABTEILUNG',$adresse['unterabteilung']);
  $this->app->Tpl->Add('MOBIL',$adresse['mobil']);
  $this->app->Tpl->Add('ADRESSZUSATZ',$adresse['adresszusatz']);
  $this->app->Tpl->Add('INTERNETSEITE',$adresse['internetseite']);
  $this->app->Tpl->Add('STRASSE',$adresse['strasse']);

  if ($adresse['kundenfreigabe'] == 1) {
    $this->app->Tpl->Add('KUNDENFREIGABE','Ja');
  } else {
    $this->app->Tpl->Add('KUNDENFREIGABE','Nein');
  }

  $this->app->Tpl->Add('PLZORT',$adresse['plz'] . ' ' . $adresse['ort']);

  if ($adresse['abweichende_rechnungsadresse'] == 1) {
    $this->app->Tpl->Add('ABWEICHENDE_RECHNUNGSADRESSE','Ja');
  } else {
    $this->app->Tpl->Add('ABWEICHENDE_RECHNUNGSADRESSE','Nein');
  }

  $this->app->Tpl->Add('LAND',$adresse['land']);
  if($type !== 'kalender' && $type !== 'wiedervorlage'){
    $this->app->Tpl->Add('DATUMTEXT','Datum:');
    $this->app->Tpl->Add('UHRZEITTEXT','Uhrzeit:');
    $this->app->Tpl->Add('DATUM',$dokument['datum']);
    $this->app->Tpl->Add('UHRZEIT',$dokument['uhrzeit']);
    $this->app->Tpl->Add('BETREFF',$dokument['betreff']);
    $this->app->Tpl->Add('BEARBEITER',$dokument['bearbeiter']);
    $this->app->Tpl->Add('TEXT',nl2br($dokument['content']));
  }elseif($type === 'kalender'){
    $this->app->Tpl->Add('DATUMTEXT','von:');
    $this->app->Tpl->Add('UHRZEITTEXT','bis:');
    $this->app->Tpl->Add('DATUM',$dokument['datumvon'].' '.$dokument['zeitvon']);
    $this->app->Tpl->Add('UHRZEIT',$dokument['datumbis'].' '.$dokument['zeitbis']);
    $this->app->Tpl->Add('BETREFF',$dokument['bezeichnung']);
    $this->app->Tpl->Add('BEARBEITER',$this->app->DB->Select("SELECT name FROM adresse WHERE id = '".$dokument['angelegtvon']."' LIMIT 1"));
    $this->app->Tpl->Add('TEXT',nl2br($dokument['beschreibung']));
  }elseif($type === 'wiedervorlage')
  {
    $this->app->Tpl->Add('DATUMTEXT','angelegt:');
    $this->app->Tpl->Add('UHRZEITTEXT','Erinnerung:');
    $this->app->Tpl->Add('DATUM',$dokument['datum_angelegt'].' '.$dokument['zeit_angelegt']);
    $this->app->Tpl->Add('UHRZEIT',$dokument['datum_erinnerung'].' '.$dokument['zeit_erinnerung']);
    $this->app->Tpl->Add('BETREFF',$dokument['bezeichnung']);
    $this->app->Tpl->Add('BEARBEITER',$this->app->DB->Select("SELECT name FROM adresse WHERE id = '".$dokument['bearbeiter']."' LIMIT 1"));
    $this->app->Tpl->Add('TEXT',nl2br($dokument['beschreibung']));
  }

  $this->app->Tpl->Output('adresse_brief_druck.tpl');

  $this->app->ExitXentral();
}

function AdresseBriefErstellen() {

  //$this->AdresseMenu();

  $id = $this->app->Secure->GetGET('id');
  $type = $this->app->Secure->GetGET('type');

  $adresse = $this->app->DB->SelectRow(
    sprintf(
      'SELECT *
        FROM adresse
        WHERE id = %d' , $id
    )
  );
  $this->app->Tpl->Add('EMPFAENGER',$adresse['name']);
  //$kundennummer = $this->app->DB->Select("SELECT kundennummer from adresse where id = ".(int)$id)."";
  //$this->app->Tpl->Add(BEARBEITER,$kundennummer." ".$this->app->DB->Select("SELECT name from adresse where id = ".(int)$id));
  $this->app->Tpl->Add('ADRESSE',$adresse['kundennummer']." ".$adresse['name']);
  $this->app->Tpl->Add('MITARBEITER',$this->app->DB->Select("SELECT mitarbeiternummer from adresse where id = ".$this->app->User->GetAdresse()." limit 1")." ".$this->app->User->GetName());
  $this->app->Tpl->Add('BEARBEITER',$this->app->DB->Select("SELECT mitarbeiternummer from adresse where id = ".$this->app->User->GetAdresse()." limit 1")." ".$this->app->User->GetName());
  $this->app->Tpl->Add('BEARBEITEROHNENUMMER',$this->app->User->GetName());
  //$this->app->BuildNavigation=false;

  $this->app->YUI->DatePicker("datum");
  $this->app->Tpl->Add('DATUM', date('d.m.Y'));
  $this->app->Tpl->Add('UHRZEIT', date('H:i'));
  $this->app->Tpl->Add('DATUMBIS', date('d.m.Y'));
  $this->app->Tpl->Add('UHRZEITBIS', date('H:i'));
  $this->app->Tpl->Add('DATUM_ERINNERUNG',date("d.m.Y",strtotime ("+1 day")));
  $this->app->Tpl->Add('UHRZEIT_ERINNERUNG', date('H:i'));

  
  if ($type) {
    switch ($type) {
      case 'wiedervorlage':
        $this->app->YUI->DatePicker("datum_erinnerung");
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", array('resize_dir'=>'both'), 'JQUERY2');
        }
        for($i=0;$i<=100;$i += 10)
        {
          $this->app->Tpl->Add("CHANCE","<option value=\"$i\">$i %</option>");
        }

        $template = 'adresse_brief_wiedervorlage.tpl';
        break;
      break;
      case 'kalender':
        $this->app->YUI->DatePicker("datum");
        $this->app->YUI->DatePicker("datumbis");
        $this->app->YUI->TimePicker("uhrzeit");
        $this->app->YUI->TimePicker("uhrzeitbis");
        $template = 'adresse_brief_kalender.tpl';
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", array('resize_dir'=>'both'), 'JQUERY2');
        }
        break;
      break;
      case 'brief':

        $this->app->Tpl->Add('SENDER', $this->app->User->GetName());
        $this->app->Tpl->Add('STRASSE',$adresse['strasse']);
        $this->app->Tpl->Add('PLZ',$adresse['plz']);
        $this->app->Tpl->Add('ORT',$adresse['ort']);
        $this->app->Tpl->Add('INTERNEBEZEICHNUNG',$adresse['internebezeichnung']);

        $laender = $this->app->erp->GetSelectLaenderliste();
        $laenderStr = '';
        foreach ($laender as $landKey => $land) {
          $laenderStr .= '<option '.($adresse['land'] == $landKey?' selected="selected" ':'').' value="' . $landKey . '">' . $land . '</option>';
        }

        $this->app->Tpl->Add('LAND',$laenderStr);

        $this->app->Tpl->Set('DRUCKERSELECT', $this->DruckerSelect());
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", array('resize_dir'=>'both'), 'JQUERY2');
        }

        $template = 'adresse_brief_brief.tpl';
        break;
      case 'email':

        $this->app->YUI->AutoComplete("email_an","emailname");
        $this->app->YUI->AutoCompleteAdd("email_cc","emailname");
        $this->app->YUI->AutoCompleteAdd("email_bcc","emailname");

        $this->app->Tpl->Set('EMAIL_SENDER', $this->app->erp->GetSelectEmailMitName());
        if (!empty($adresse['email'])) {
          $this->app->Tpl->Add('EMAIL_AN',str_replace('"','&#34;',$adresse['name']) . ' &lt;' . $adresse['email'] . '&gt;');
        }
        $anhaenge = '';
        $anhaenge .= '<tr><td nowrap>Datei:</td><td><input type="file" name="upload[]" id="file" /></td></tr>';
//        $anhaenge .= '<tr><td nowrap>Datei 2:</td><td><input type="file" name="upload[]" /></td></tr>';
//        $anhaenge .= '<tr><td nowrap>Datei 3:</td><td><input type="file" name="upload[]" /></td></tr>';
         

        $this->app->Tpl->Add('ANHAENGEHERAUFLADEN', $anhaenge);
        $anhaenge = '<tr><td colspan=3 align=center><i>Keine Anh&auml;nge vorhanden</i></td></tr>';
        $this->app->Tpl->Add('ANHAENGE', $anhaenge);
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", array('resize_dir'=>'both'), 'JQUERY2');
        }
        // $this->app->Tpl->Parse('PAGE',"adresse_brief_email.tpl");
        $template = 'adresse_brief_email.tpl';
        break;
      case 'telefon':
        // $this->app->Tpl->Parse('PAGE',"adresse_brief_telefon.tpl");
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", array('resize_dir'=>'both'), 'JQUERY2');
        }
        $template = 'adresse_brief_telefon.tpl';
        break;
      case 'notiz':
        // $this->app->Tpl->Parse('PAGE',"adresse_brief_notiz.tpl");
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", array('resize_dir'=>'both'), 'JQUERY2');
        }
        $template = 'adresse_brief_notiz.tpl';
        break;
      default:
        // $this->app->Tpl->Parse('PAGE',"adresse_brief_email.tpl");
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", array('resize_dir'=>'both'), 'JQUERY2');
        }
        $template = 'adresse_brief_email.tpl';
        break;
    }
  } else {
    $this->app->Tpl->Add('EMAIL',$adresse['email']);
    // $this->app->Tpl->Parse('PAGE',"adresse_brief_email.tpl");
    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("content","internal", array('resize_dir'=>'both'), 'JQUERY2');
    }
    $template = 'adresse_brief_email.tpl';
  }

  $this->app->Tpl->Output($template);
  $this->app->ExitXentral();

  /*
     $this->app->BuildNavigation=false;
     $this->app->Tpl->Set('INHALT',"");
     $this->app->Tpl->Parse('PAGE',"adresse_brief_email.tpl");
   */


}


function AdresseBriefCreatePDF($dokumentId, $display=true) {

  $data = $this->app->DB->SelectRow(
    sprintf(
      'SELECT *
      FROM dokumente
      WHERE id = %d',
      $dokumentId
    )
  );

  $data['firma'] = $this->app->DB->Select('
      SELECT
      MAX(id)
      FROM
      firma
      LIMIT 1
      ');

  $data['firma'] = $this->app->DB->Select('
      SELECT
      absender
      FROM
      firmendaten
      WHERE
      id = ' . $data['firma'] . '
      ');

  $korrespondenz = new KorrespondenzPDF($this->app,$data['projekt']);
  $korrespondenz->SetBetreff($this->app->erp->ReadyForPDF($data['betreff']));

  $korrespondenz->SetDetail('Datum', $this->app->String->Convert($data['datum'],"%1-%2-%3","%3.%2.%1"));
  $korrespondenz->SetDetail('Bearbeiter', $this->app->erp->ReadyForPDF($data['von']));

  $korrespondenz->setRecipient(
      array(
        $this->app->erp->ReadyForPDF($data['an']), 
        $this->app->erp->ReadyForPDF($data['ansprechpartner']),
        '',
        $this->app->erp->ReadyForPDF($data['adresse']),
        $data['plz'],
        $this->app->erp->ReadyForPDF($data['ort']),
        $data['land']
        )
      );

  if(strpos($data['betreff'],'{') !== false) {
    $data['betreff'] = $this->app->erp->ParseUserVars('adresse', $data['adresse_to'], $data['betreff']);
  }
  if(strpos($data['content'],'{') !== false) {
    $data['content'] = $this->app->erp->ParseUserVars('adresse', $data['adresse_to'], $data['content']);
  }

  $korrespondenz->setLetterDetails(
      array(
        $this->app->erp->ReadyForPDF($data['betreff']),
        str_replace('\r\n',"\n\n",$this->app->erp->ReadyForPDF($data['content']))
        )
      );
  $korrespondenz->setAbsender($data['firma']);

  $korrespondenz->Create();

  if($display) {
    $korrespondenz->displayDocument();
  } else {
    return $korrespondenz->displayTMP();
  }
}

function AdresseBriefSaveDocument() {

  $data = array();
  $data['user'] = '';
  $data['datum'] = $this->app->Secure->GetPOST('datum');
  $data['von'] = $this->app->Secure->GetPOST('von');
  $data['firma'] = $this->app->Secure->GetPOST('firma');
  $data['ansprechpartner'] = $this->app->Secure->GetPOST('ansprechpartner');
  $data['an'] = $this->app->Secure->GetPOST('an');
  $data['email_an'] = $this->app->Secure->GetPOST('email_an');
  $data['email_cc'] = $this->app->Secure->GetPOST('email_cc');
  $data['email_bcc'] = $this->app->Secure->GetPOST('email_bcc');
  $data['firma_an'] = $this->app->Secure->GetPOST('firma_an');
  $data['adresse'] = $this->app->Secure->GetPOST('adresse');
  $data['plz'] = $this->app->Secure->GetPOST('plz');
  $data['ort'] = $this->app->Secure->GetPOST('ort');
  $data['internebezeichnung'] = $this->app->Secure->GetPOST('internebezeichnung');
  $data['land'] = $this->app->Secure->GetPOST('land');
  $data['betreff'] = $this->app->Secure->GetPOST('betreff');
  $data['content'] = $this->app->Secure->GetPOST('content');
  $data['signatur'] = $this->app->Secure->GetPOST('signatur');
  $data['send_as'] = $this->app->Secure->GetPOST('send_as');
  $data['email'] = $this->app->Secure->GetPOST('email');
  $data['printer'] = $this->app->Secure->GetPOST('printer');
  $data['fax'] = $this->app->Secure->GetPOST('fax');
  $data['user'] = $this->app->Secure->GetGET('id');
  $data['signatur'] = $this->app->Secure->GetPOST('signatur');
  $data['eintragId'] = $this->app->Secure->GetPOST('eintragId');
  $data['projekt'] = $this->app->Secure->GetPOST('projekt');

  $data['projekt'] = explode(' ',$data['projekt']);
  if($data['projekt'][0] !="")
    $data['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$data['projekt'][0]."' AND abkuerzung!='' LIMIT 1");

  $data['uhrzeit'] = $this->app->Secure->GetPOST('uhrzeit');
  if (!$data['uhrzeit']) {
    $data['uhrzeit'] = date('H:i:s');
  }

  $data['bearbeiter'] = $this->app->Secure->GetPOST('bearbeiter');

  if (!empty($data['signatur'])) {
    $data['signatur'] = 1;
  } else {
    $data['signatur'] = 0;
  }

  if (!$data['datum']) {
    $data['datum'] = date('d.m.Y');
  }

  $data['typ'] = $this->app->Secure->GetPOST('type');
  if (!$data['typ']) {
    $data['typ'] = 'brieffax';
  }

  $adresse = $this->app->User->GetAdresse();
  $datum = $this->app->String->Convert($data['datum'],"%1.%2.%3","%3-%2-%1");

  if (isset($data['eintragId']) && !empty($data['eintragId'])) {

    $this->app->DB->Insert('
        UPDATE
        dokumente
        SET
        adresse_from = "' . $adresse . '",
        adresse_to = "' . $data['user'] . '",
        typ = "' . $data['typ'] . '",
        von = "' . $data['von'] . '",
        firma = "' . $data['firma'] . '",
        ansprechpartner = "' . $data['ansprechpartner'] . '",
        internebezeichnung = "' . $data['internebezeichnung'] . '",
        an = "' . $data['an'] . '",
        email_an = "' . $data['email_an'] . '",
        email_cc = "' . $data['email_cc'] . '",
        email_bcc = "' . $data['email_bcc'] . '",
        firma_an = "' . $data['firma_an'] . '",
        adresse = "' . $data['adresse'] . '",
        plz = "' . $data['plz'] . '",
        ort = "' . $data['ort'] . '",
        land = "' . $data['land'] . '",
        datum = "' . $datum . '",
        betreff = "' . $data['betreff'] . '",
        content = "' . $data['content'] . '",
        signatur = "' . $data['signatur'] . '",
        send_as = "' . $data['send_as'] . '",
        email = "' . $data['email'] . '",
        printer = "' . $data['printer'] . '",
        fax = "' . $data['fax'] . '",
        created = NOW(),
        uhrzeit = "' . $data['uhrzeit'] . '",
        projekt = "' . $data['projekt'] . '",
        bearbeiter = "' . $data['bearbeiter'] . '"
          WHERE
          id = ' . $data['eintragId'] . '
          ');

    $returnId = $data['eintragId'];

  } else {

    $returnId = $this->app->erp->DokumentCreate($data,$adresse);
  }

  if($returnId && $data['typ'] == 'email')
  {
    if(isset($_FILES['upload']) && is_array($_FILES['upload']))
    {
      foreach($_FILES['upload']['tmp_name'] as $key => $file)
      {
        if($file != "")
        {
          $fileid = $this->app->erp->CreateDatei($_FILES['upload']['name'][$key], $_FILES['upload']['name'][$key], "", "", $_FILES['upload']['tmp_name'][$key], $this->app->User->GetName());
          // stichwoerter hinzufuegen
          $this->app->erp->AddDateiStichwort($fileid, "anhang", "dokument", $returnId);
        }
      }
    }
    $this->app->Tpl->Add('AJAXBRIEF', "
    		$.ajax({
			url: 'index.php',
			data: {
				module: 'adresse',
				action: 'briefbearbeiten',
        typ : 'email',
				id: ".$returnId."
			},
			beforeSend: function() {
				App.loading.open();
			},
			success: function(data) {

				$('.alleAnzeigen').show();

				$('.adresse_brief_anlegen').css({
					width: $('.adresse_brief_tabelle').width()
				});


				$('.adresse_brief_tabelle').find('fieldset').hide();
				$('.adresse_brief_tabelle').find('.adresse_brief_tabelle_view').hide();

				$('.adresse_brief_anlegen')
					.html(data)
					.show();

				fnFilterColumn8(0);
				App.loading.close();
			}
		});
    ");
  }
  
  return $returnId;

}


function AdresseBriefPreview($type = '', $id = '', $json = true) {
  
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

  $res = $this->app->DB->SelectRow($query);
  if ($res) {
/*
    if (isset($res['content'])) {
      $res['content'] = '<input type="button" onclick="briefDrucken(' . $res['id'] . ');" value="Drucken"><br><br>'.nl2br($res['content']);
    }
*/
  }
  if(isset($res['datum']) && !$tickets)
  {
    if($type=="kalender" || $type=="dokumente_send")
      $res['content'] = $res['content'];
    else
      $res['content'] = '<b>Angelegt am: '.$res['datum'].(isset($res['zeit_angelegt'])?' '.$res['zeit_angelegt']:'')."</b><br />".$res['content'];
  }

  if($json)
  {
  echo json_encode($res);
  } else {
    //$ausg = "<h2>".$res['datum'].' '.$res['betreff']."</h2>".nl2br($res['content']);
    //if($res['content']=="") $res['content']="Kein Inhalt vorhanden";

    if($this->app->erp->isHTML($res['content']))
      $ausg = $res['content'];
    else
      $ausg = nl2br($res['content']);
    echo $ausg;
  }
  $this->app->ExitXentral();

}

function AdresseBriefBearbeiten() {
  $id = $this->app->Secure->GetGET('id');
  $type = $this->app->Secure->GetGET('type');
  $typ = $this->app->Secure->GetGET('typ');

  if($typ === 'kalender')
  {
    $query = '
          SELECT
          id,
          DATE_FORMAT(von, "%d.%m.%Y") as datum,
          DATE_FORMAT(bis, "%d.%m.%Y") as datumbis,
          DATE_FORMAT(von, "%H:%i") as uhrzeit,
          DATE_FORMAT(bis, "%H:%i") as uhrzeitbis,
          allDay,
          public,
          adresse,
          ansprechpartner_id,
          angelegtvon,
          adresseintern,
          bezeichnung,
          beschreibung 
            FROM
            kalender_event
            WHERE
            id = ' . $id . '
            ';
  }elseif($typ === 'wiedervorlage')
  {
    $query = 'SELECT * from wiedervorlage where id = '.(int)$id;
  }else{
  
  $query = '
    SELECT
    d.id,
    d.typ,
    d.von,
    d.firma,
    d.an,
    d.email_an,
    d.email_cc,
    d.email_bcc,
    d.ansprechpartner,
    d.internebezeichnung,
    d.adresse,
    d.plz,
    d.ort,
    d.land,
    DATE_FORMAT(d.datum, "%d.%m.%Y") as datum,
    d.betreff,
    d.content,
    d.bearbeiter,
    DATE_FORMAT(d.uhrzeit,"%H:%i") as uhrzeit,
    CONCAT(p.abkuerzung," ",p.name) as projekt
      FROM
      dokumente d
      LEFT JOIN projekt p ON p.id=d.projekt
      WHERE
      d.id = ' . $id . '
      ';
  }
  $dokument = $this->app->DB->SelectRow($query);

  if($typ === 'kalender')
  {
    $this->app->YUI->DateiPopup('DATEIENBUTTON', 'kalender_event', $dokument['id']);
    $template = 'adresse_brief_kalender.tpl';
    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("content","internal", null, 'JQUERY2');
    }
    $bearbeiternummer = $this->app->DB->Select("SELECT mitarbeiternummer from adresse where id = ".$dokument['angelegtvon']." limit 1");
    $adressdaten = $this->app->DB->SelectRow("SELECT name, kundennummer, lieferantennummer from adresse where id = ".$dokument['adresse']." limit 1");

    if($dokument['ansprechpartner_id'] != "" && $dokument['ansprechpartner_id'] > 0){
      $ansprechpartnerId = $this->app->DB->Select("SELECT id FROM ansprechpartner WHERE id = '" . $dokument['ansprechpartner_id'] . "' LIMIT 1");
      if($ansprechpartnerId != "" && $ansprechpartnerId > 0){
        $ansprechpartnerName = $this->app->DB->Select("SELECT name FROM ansprechpartner WHERE id = '$ansprechpartnerId' LIMIT 1");
        $ansprechpartner = $ansprechpartnerId . ' ' . $ansprechpartnerName;
        $ansprechpartner .= ' (' . $adressdaten['name'] . ', Kdr: ' . $adressdaten['kundennummer'] . ' ';
        if($adressdaten['lieferantennummer'] != ''){
          $ansprechpartner .= 'Liefr: ' . $adressdaten['lieferantennummer'];
        }
        $ansprechpartner .= ')';
        $dokument['ansprechpartner'] = $ansprechpartner;
      }
    }

    $datum = $dokument['datum'];
    $datumbis = $dokument['datumbis'];
    $this->app->Tpl->Add('UHRZEIT',$dokument['uhrzeit']);
    $this->app->Tpl->Add('UHRZEITBIS',$dokument['uhrzeitbis']);
    $this->app->Tpl->Add('DATUM',$datum);
    $this->app->Tpl->Add('DATUMBIS',$datumbis);
    if($dokument['allDay'])$this->app->Tpl->Set('ALLDAY',' checked="checked" ');
    if($dokument['public'])$this->app->Tpl->Set('PUBLIC',' checked="checked" ');
    $this->app->Tpl->Add('ADRESSEINTERN',$this->app->DB->Select("SELECT CONCAT(id,' ',name) from adresse where id = ".$dokument['adresseintern']." limit 1"));
    $this->app->Tpl->Add('CONTENT',$dokument['beschreibung']);
    $this->app->Tpl->Add('BETREFF',$dokument['bezeichnung']);
    $this->app->Tpl->Add('BEARBEITER',$bearbeiternummer?$bearbeiternummer.' '.$this->app->DB->Select("SELECT name from adresse where id = ".$dokument['angelegtvon']." limit 1"):'');
    $this->app->Tpl->Add('ANSPRECHPARTNER',$dokument['ansprechpartner']);
    $check = $this->app->DB->SelectArr("SELECT * FROM layoutvorlagen WHERE kategorie like 'Besuchsbericht' order by name");
    if($check)
    {
      $this->app->Tpl->Set('LAYOUTBUTTON','<a href="index.php?module=adresse&action=brief&cmd=layoutoeffnen&id='.$id.'&layout='.$check[0]['id'].'" target="_blank"><input type="button" name="close" value="Besuchsbericht" /></a>');
    }
    
  }
  elseif($typ == 'wiedervorlage')
  {
    if($this->app->erp->Firmendaten("briefhtml")=="1")
    {
      $this->app->YUI->CkEditor("content","internal",null, 'JQUERY2');
    }
    $this->app->YUI->DateiPopup('DATEIENBUTTON', 'wiedervorlage', $dokument['id']);
    $template = 'adresse_brief_wiedervorlage.tpl';
    $mitarbeiternummer = $this->app->DB->Select("SELECT mitarbeiternummer from adresse where id = ".$dokument['adresse_mitarbeiter']." limit 1");
    $bearbeiternummer = $this->app->DB->Select("SELECT mitarbeiternummer from adresse where id = ".$dokument['bearbeiter']." limit 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer from adresse where id = ".$dokument['adresse']." limit 1");
    $adressdaten = $this->app->DB->SelectRow("SELECT name, kundennummer, lieferantennummer from adresse where id = ".$dokument['adresse']." limit 1");

    if($dokument['ansprechpartner_id'] != "" && $dokument['ansprechpartner_id'] > 0){
      $ansprechpartnerId = $this->app->DB->Select("SELECT id FROM ansprechpartner WHERE id = '" . $dokument['ansprechpartner_id'] . "' LIMIT 1");
      if($ansprechpartnerId != "" && $ansprechpartnerId > 0){
        $ansprechpartnerName = $this->app->DB->Select("SELECT name FROM ansprechpartner WHERE id = '$ansprechpartnerId' LIMIT 1");
        $ansprechpartner = $ansprechpartnerId . ' ' . $ansprechpartnerName;
        $ansprechpartner .= ' (' . $adressdaten['name'] . ', Kdr: ' . $adressdaten['kundennummer'] . ' ';
        if($adressdaten['lieferantennummer'] != ''){
          $ansprechpartner .= 'Liefr: ' . $adressdaten['lieferantennummer'];
        }
        $ansprechpartner .= ')';
        $dokument['ansprechpartner'] = $ansprechpartner;
      }
    }

    $dokument['datum_angelegt'] = $this->app->String->Convert($dokument['datum_angelegt'],"%1-%2-%3","%3.%2.%1");
    $dokument['datum_erinnerung'] = $this->app->String->Convert($dokument['datum_erinnerung'],"%1-%2-%3","%3.%2.%1");
    $this->app->Tpl->Add('DATUM',$dokument['datum_angelegt']);
    $this->app->Tpl->Add('UHRZEIT',$dokument['zeit_angelegt']);
    $this->app->Tpl->Add('BEARBEITER',$bearbeiternummer?$bearbeiternummer.' '.$this->app->DB->Select("SELECT name from adresse where id = ".$dokument['bearbeiter']." limit 1"):'');
    $this->app->Tpl->Add('BETREFF',$dokument['bezeichnung']);
    $this->app->Tpl->Add('MITARBEITER',$mitarbeiternummer?$mitarbeiternummer.' '.$this->app->DB->Select("SELECT name from adresse where id = ".$dokument['adresse_mitarbeiter']." limit 1"):'');
    $this->app->Tpl->Add('ADRESSE',$kundennummer?$kundennummer.' '.$this->app->DB->Select("SELECT name from adresse where id = ".$dokument['adresse']." limit 1"):'');
    $this->app->Tpl->Add('CONTENT',$dokument['beschreibung']);
    $this->app->Tpl->Add('DATUM_ERINNERUNG',$dokument['datum_erinnerung']);
    $this->app->Tpl->Add('UHRZEIT_ERINNERUNG',$dokument['zeit_erinnerung']);
    if($dokument['abgeschlossen'])$this->app->Tpl->Add('ABGESCHLOSSEN',' checked="checked" ');
    if($dokument['prio'])$this->app->Tpl->Add('PRIO',' checked="checked" ');

    $this->app->Tpl->Set('STAGES',$this->app->DB->Select("SELECT CONCAT(id,' ',kurzbezeichnung,' (',name,')') FROM wiedervorlage_stages WHERE id='".$dokument['stages']."' LIMIT 1"));
    $this->app->Tpl->Add('BETRAG',number_format($dokument['betrag'],2,",","."));
    for($i=0;$i<=100;$i += 10)
    {
      $this->app->Tpl->Add("CHANCE","<option value=\"$i\"  ".($dokument['chance']==$i?"selected":"").">$i %</option>");
    }

    $this->app->Tpl->Add('PROJEKT',$this->app->DB->Select("SELECT CONCAT(abkuerzung,' ',name) FROM projekt WHERE id='".$dokument['projekt']."' LIMIT 1"));
    $this->app->Tpl->Add('ANSPRECHPARTNER',$dokument['ansprechpartner']);
        
  }else {
    switch($dokument['typ']) {
      case 'brief':
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","belege", null, 'JQUERY2');
        }
        $this->app->YUI->DateiPopup('DATEIENBUTTON', 'dokumente', $dokument['id']);
        $template = 'adresse_brief_brief.tpl';
        break;
      case 'email':
      
      
        $anhaenge = '';
        $anhaenge .= '<tr><td nowrap>Datei:</td><td colspan="2"><input type="file" name="upload[]" id="file"/></td></tr>';
//        $anhaenge .= '<tr><td nowrap>Datei 2:</td><td colspan="2"><input type="file" name="upload[]" /></td></tr>';
//        $anhaenge .= '<tr><td nowrap>Datei 3:</td><td colspan="2"><input type="file" name="upload[]" /></td></tr>';

        $this->app->Tpl->Add('ANHAENGEHERAUFLADEN', $anhaenge);
        $anhaenge = "";
        
        $anhaengedb = $this->app->DB->SelectArr("SELECT ds.id, ds.datei, d.titel FROM datei_stichwoerter ds INNER JOIN datei d on ds.datei = d.id WHERE ds.parameter = '$id' AND ds.objekt = 'dokument' AND ds.subjekt = 'anhang' and d.geloescht <> 1");
        if($anhaengedb)
        {
          foreach($anhaengedb as $anhang)
          {
            $anhaenge .= '<tr id="trdatei_'.$anhang['datei'].'"><td><input type="checkbox" name="datei_'.$anhang['datei'].'" value="1" checked/></td><td><a style="font-weight:normal" href="index.php?module=adresse&action=downloaddatei&id='.$anhang['datei'].'">'.$anhang['titel'].'</a></td><td width=20><img src="./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/delete.svg" onclick="remdatei('.$anhang['datei'].');" style="border:0;" /></td></tr>';
          }
        } else {
          $anhaenge .= '<tr><td colspan=3 align=center><i>Keine Anh&auml;nge vorhanden</i></td></tr>';
        }
         
        $this->app->Tpl->Add('ANHAENGE', $anhaenge);
      
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", null, 'JQUERY2');
        }
        $this->app->YUI->DateiPopup('DATEIENBUTTON', 'dokumente', $dokument['id']);
        $template = 'adresse_brief_email.tpl';
        break;
      case 'telefon':
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", null, 'JQUERY2');
        }
        $this->app->YUI->DateiPopup('DATEIENBUTTON', 'dokumente', $dokument['id']);
        $template = 'adresse_brief_telefon.tpl';
        break;
      case 'notiz':
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", null, 'JQUERY2');
        }
        $this->app->YUI->DateiPopup('DATEIENBUTTON', 'dokumente', $dokument['id']);
        $template = 'adresse_brief_notiz.tpl';
        break;
      default:
        if($this->app->erp->Firmendaten("briefhtml")=="1")
        {
          $this->app->YUI->CkEditor("content","internal", null, 'JQUERY2');
        }
        $template = 'adresse_brief_brief.tpl';
      break;
    }
    $this->app->Tpl->Add('EMAIL_SENDER', $this->app->erp->GetSelectEmailMitName($dokument['von']));
    $this->app->Tpl->Add('EMPFAENGER',$dokument['an']);
    $this->app->Tpl->Add('SENDER',$dokument['von']);
    $this->app->Tpl->Add('ANSPRECHPARTNER',$dokument['ansprechpartner']);
    $this->app->Tpl->Add('DATUM',$dokument['datum']);
    $this->app->Tpl->Add('STRASSE',$dokument['adresse']);
    $this->app->Tpl->Add('PLZ',$dokument['plz']);
    $this->app->Tpl->Add('ORT',$dokument['ort']);

    $this->app->Tpl->Add('BEARBEITER',$dokument['bearbeiter']);
    $this->app->Tpl->Add('UHRZEIT',$dokument['uhrzeit']);

    // $this->app->Tpl->Add(LAND,$dokument['land']);

    $laender = $this->app->erp->GetSelectLaenderliste();
    $laenderStr = '';
    foreach ($laender as $landKey => $land) {
      $laenderStr .= '<option '.($dokument['land'] == $landKey?' selected="selected" ':'').' value="' . $landKey . '">' . $land . '</option>';
    }

    $this->app->Tpl->Add('LAND',$laenderStr);

    $this->app->Tpl->Add('BETREFF',$dokument['betreff']);
    $this->app->Tpl->Add('INTERNEBEZEICHNUNG',$dokument['internebezeichnung']);
    $this->app->Tpl->Add('CONTENT',$dokument['content']);

    $this->app->Tpl->Add('PROJEKT',$dokument['projekt']);

    $this->app->Tpl->Add('EMAIL_AN',$dokument['email_an']);
    $this->app->Tpl->Add('EMAIL_CC',$dokument['email_cc']);
    $this->app->Tpl->Add('EMAIL_BCC',$dokument['email_bcc']);
  }



  $this->app->Tpl->Add('EINTRAGID',$dokument['id']);

  $this->app->Tpl->Add('DRUCKERSELECT', $this->DruckerSelect());

  $this->app->Tpl->Output($template);

  $this->app->ExitXentral();

}

function AdresseKorrBriefPdf() {

  $id = $this->app->Secure->GetGET('id');
  $this->AdresseBriefCreatePDF($id, true);
  $this->app->ExitXentral();
}

function AdresseKorrBriefDelete() {

  $typ = $this->app->Secure->GetGET('typ');
  $id = $this->app->Secure->GetGET('id');
  if(strtolower($typ) === 'kalender')
  {
    $this->app->DB->Delete("DELETE FROM kalender_user where event = '$id'");
    $this->app->DB->Delete('
        DELETE FROM
        kalender_event
        WHERE
        id = "' . $id . '"
        ');
  }elseif(strtolower($typ) === 'wiedervorlage')
  {
    $this->app->DB->Delete('
        DELETE FROM
        wiedervorlage
        WHERE
        id = ' . $id . '
        ');
    
    
  }else{
    $this->app->DB->Delete('
        DELETE FROM
        dokumente
        WHERE
        id = ' . $id . '
        ');
    
    
  }

  $json['status'] = 0;
  $json['statusText'] = 'Email konnte nicht gesendet werden.';
  echo json_encode($json);
  $this->app->ExitXentral();
}


function AdresseBriefSaveKalender()
{
  $data = array();
  $data['id'] = $this->app->Secure->GetPOST('eintragId');
  $data['adresse'] = $this->app->Secure->GetGET('id');
  $data['adresseintern'] = $this->app->Secure->GetPOST('adresseintern');
  $data['bezeichnung'] = $this->app->Secure->GetPOST('betreff');
  $data['beschreibung'] = $this->app->Secure->GetPOST('content');

  $data['projekt'] = $this->app->Secure->GetPOST("projekt");
  $data['projekt'] = explode(' ',$data['projekt']);
  $data['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$data['projekt'][0]."' AND abkuerzung!='' LIMIT 1");

  $data['angelegtvon'] = (int)$this->app->DB->Select("SELECT id from adresse where mitarbeiternummer = '" .(int)$this->app->Secure->GetPOST('angelegtvon')."'");

  $tmp = trim($data['adresseintern']);
  $rest = explode(" ",$tmp);
  $data['adresseintern'] = $rest[0];

  
  $data['von'] = $this->app->String->Convert($_POST['datum'],"%1.%2.%3","%3-%2-%1");
  $uhrzeit = $_POST['uhrzeit']?$_POST['uhrzeit']:'0:00';
  if(!$uhrzeit)$uhrzeit = '0:00';
  $data['von'] = $data['von'].' '.$uhrzeit;
  $data['bis'] = $this->app->String->Convert($_POST['datumbis'],"%1.%2.%3","%3-%2-%1");
  $uhrzeitbis = $_POST['uhrzeitbis']?$_POST['uhrzeitbis']:'0:00';
  if(!$uhrzeitbis)$uhrzeitbis = '0:00';
  $data['bis'] = $data['bis'].' '.$uhrzeitbis;
  
  $data['allDay'] = $this->app->Secure->GetPOST("allDay")?1:0;
  $data['public'] = $this->app->Secure->GetPOST("public")?1:0;

  $data['ansprechpartner_id'] = $this->app->Secure->GetPOST("ansprechpartner");
  if($data['ansprechpartner_id'] != ""){
    $ansprechpartner = explode(" ", $data['ansprechpartner_id']);
    $ansprechpartnerId = $ansprechpartner[0];
    $ansprechpartnerId = $this->app->DB->Select("SELECT id FROM ansprechpartner WHERE id = '$ansprechpartnerId' LIMIT 1");
    if($ansprechpartnerId === '' || $ansprechpartnerId <= 0){
      $ansprechpartnerId = 0;
    }
  }else{
    $ansprechpartnerId = 0;
  }
  $data['ansprechpartner_id'] = $ansprechpartnerId;
  
 
  if($data['von'] && $data['bezeichnung'])
  {
    
    $id = 0;
    if($data['id'])
    {
      $id = $this->app->DB->Select("SELECT id from kalender_event where id = ".(int)$data['id']." Limit 1");
    }
    if($id)
    {
      $sql = 'UPDATE kalender_event set ';
      $first = true;
      foreach($data as $k => $v)
      {
        if($k != 'id')
        {
          if(!$first)$sql .= ', ';
          $first = false;
          $sql .= $k." = '".$v."'";
          
        }
        
      }
      $sql .= " where id = ".$id;
      
     if($this->app->DB->Update($sql)){
       $calendarActionType = 'modified';
       $this->app->erp->RunHook('kalender_event_hook', 2, $id, $calendarActionType);
       return $id;
     };
     return false;
    } else {
      
      $sql = "INSERT INTO kalender_event (";
      $first = true;
      foreach($data as $k => $v)
      {
        if($k != 'id')
        {
          if(!$first)$sql .= ', ';
          $first = false;
          $sql .= $k;
        }
      }
      $sql .= ") values (";
      $first = true;
      foreach($data as $k => $v)
      {
        if($k != 'id')
        {
          if(!$first)$sql .= ', ';
          $first = false;
          $sql .= "'".$v."'";
        }
      }
      $sql .= ")";
      
      if($this->app->DB->Insert($sql)){
        $result = $this->app->DB->GetInsertID();

        $calendarActionType = 'added';
        $this->app->erp->RunHook('kalender_event_hook', 2, $result, $calendarActionType);

        $this->app->DB->Insert("INSERT INTO kalender_user (id,event,userid) VALUES ('','$result','".$this->app->User->GetID()."')");
        return $result;
      }
      return false;
    }
      
    
    
  } else {
    
  }
  return false;
}


function AdresseBriefSaveWiedervorlage()
{
  $data = array();
  $data['id'] = $this->app->Secure->GetPOST('eintragId');
  $data['adresse'] = $this->app->Secure->GetGET('id');
  $bearbeiterdaten = explode(" ",$this->app->Secure->GetPOST('bearbeiter'));
  $data['bearbeiter'] = (int)$this->app->DB->Select("SELECT id from adresse where mitarbeiternummer = '".$bearbeiterdaten[0]."'");
  $mitarbeiterdaten = explode(" ",$this->app->Secure->GetPOST('adresse_mitarbeiter'));
  $data['adresse_mitarbeiter'] = (int)$this->app->DB->Select("SELECT id from adresse where mitarbeiternummer = '".$mitarbeiterdaten[0]."'");
  $data['bezeichnung'] = $this->app->Secure->GetPOST('betreff');
  $data['beschreibung'] = $this->app->Secure->GetPOST('content');
  $data['stages'] = $this->app->Secure->GetPOST('stages');
  $data['betrag'] = $this->app->Secure->GetPOST('betrag');
  $data['chance'] = $this->app->Secure->GetPOST('chance');

  
  $data['datum_erinnerung'] = $_POST['datumerinnerung'];
  $data['zeit_erinnerung'] = $_POST['uhrzeiterinnerung']?$_POST['uhrzeiterinnerung']:'0:00';
  if(!$data['datum_erinnerung'])$data['datum_erinnerung'] = '0:00';
  $data['module'] = $this->app->Secure->GetGET("module");
  $data['action'] = $this->app->Secure->GetGET("action");
  $data['abgeschlossen'] = (int)$this->app->Secure->GetPOST("abgeschlossen");
  $data['prio'] = (int)$this->app->Secure->GetPOST("prio");
  $data['projekt'] = $this->app->Secure->GetPOST("projekt");
  if (!empty($data['projekt'])) {
    $data['projekt'] = explode(' ',$data['projekt']);
    $data['projekt'] = (int)$this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$data['projekt'][0]."' AND abkuerzung!='' LIMIT 1");
  } else {
    // Kein Projekt gewählt
    $projekt = (int)$this->app->DB->Select("SELECT f.standardprojekt FROM `firma` AS f WHERE f.id='".$this->app->User->GetFirma()."' LIMIT 1");
    $projekt_bevorzugt = (int)$this->app->DB->Select("SELECT u.projekt_bevorzugen FROM `user` AS u WHERE u.id='".$this->app->User->GetID()."' LIMIT 1");
    if($projekt_bevorzugt === 1){
      $projekt = (int)$this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    }
    $data['projekt'] = $projekt;
  }

  $data['stages'] = explode(' ',$data['stages']);
  $data['stages'] = $data['stages'][0];
  $data['betrag'] = str_replace(',','.',$data['betrag']);

  $data['ansprechpartner_id'] = $this->app->Secure->GetPOST("ansprechpartner");
  if($data['ansprechpartner_id'] != ""){
    $ansprechpartner = explode(" ", $data['ansprechpartner_id']);
    $ansprechpartnerId = $ansprechpartner[0];
    $ansprechpartnerId = $this->app->DB->Select("SELECT id FROM ansprechpartner WHERE id = '$ansprechpartnerId' LIMIT 1");
    if($ansprechpartnerId === '' || $ansprechpartnerId <= 0){
      $ansprechpartnerId = 0;
    }
  }else{
    $ansprechpartnerId = 0;
  }
  $data['ansprechpartner_id'] = $ansprechpartnerId;

  $data['datum_erinnerung'] = $this->app->String->Convert($data['datum_erinnerung'],"%1.%2.%3","%3-%2-%1");
  if($data['datum_erinnerung'] && $data['zeit_erinnerung'] && $data['bezeichnung'])
  {
    
    $id = 0;
    if($data['id'])
    {
      $id = $this->app->DB->Select("SELECT id from wiedervorlage where id = ".(int)$data['id']." Limit 1");
    }
    if($id)
    {
      $sql = 'UPDATE wiedervorlage set ';
      $first = true;
      foreach($data as $k => $v)
      {
        if($k != 'id')
        {
          if(!$first)$sql .= ', ';
          $first = false;
          $sql .= $k." = '".$v."'";
          
        }
        
      }
      $sql .= " where id = ".$id;
      
     if($this->app->DB->Update($sql))return $id;
     return false;
    } else {
      
      $data['datum_angelegt'] = $this->app->Secure->GetPOST('datum');
      $data['zeit_angelegt'] = $this->app->Secure->GetPOST('uhrzeit');
      $data['datum_angelegt'] = $this->app->String->Convert($data['datum_angelegt'],"%1.%2.%3","%3-%2-%1");
      
      $sql = "INSERT INTO wiedervorlage (";
      $first = true;
      foreach($data as $k => $v)
      {
        if($k != 'id')
        {
          if(!$first)$sql .= ', ';
          $first = false;
          $sql .= $k;
        }
      }
      $sql .= ") values (";
      $first = true;
      foreach($data as $k => $v)
      {
        if($k != 'id')
        {
          if(!$first)$sql .= ', ';
          $first = false;
          $sql .= "'".$v."'";
        }
      }
      $sql .= ")";
      
      if($this->app->DB->Insert($sql))return $this->app->DB->GetInsertID();
      return false;
    }
      
    
    
  } else {
    
  }
  return false;
  
}

function AdresseBrief() {
  if($this->app->Secure->GetGET('cmd') == 'layoutoeffnen')
  {
    $layout = $this->app->Secure->GetGET('layout');
    $id = $this->app->Secure->GetGET('id');
    $query = '
          SELECT
          ke.id,
          DATE_FORMAT(ke.von, "%d.%m.%Y") as datum,
          DATE_FORMAT(ke.bis, "%d.%m.%Y") as datumbis,
          DATE_FORMAT(ke.von, "%H:%i") as uhrzeit,
          DATE_FORMAT(ke.bis, "%H:%i") as uhrzeitbis,
          ke.allDay,
          ke.public,
          ke.adresse,
          ke.angelegtvon,
          ke.adresseintern,
          ke.bezeichnung,
          ke.beschreibung,
          a.telefon as telefonmitarbeiter,
          a.name as vertriebmitarbeiter,
          a.email as emailmitarbeiter,
          a2.name as bearbeiter
            FROM
            kalender_event ke
            LEFT JOIN adresse a ON a.id=ke.adresseintern
            LEFT JOIN adresse a2 ON a2.id=ke.angelegtvon
            WHERE
            ke.id = ' . $id . '
            ';
    $kalenderevent = $this->app->DB->SelectRow($query);

    $data = array();

    if($kalenderevent)
    {
      foreach($kalenderevent as $key => $value)
      {
        $data[strtoupper($key)] = $value;
      }
    }
    $adressen = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id = '".$kalenderevent['adresse']."'");
    if($adressen)
    {
      foreach($adressen as $key => $value)
      {
        $data['ADRESSE_'.strtoupper($key)] = $value;
      }
    }
    if(class_exists('LayoutvorlagenPDF'))
    {
      $pdf = new LayoutvorlagenPDF($this->app);
      $pdf->SetLanguage('deutsch');
      $layout = $this->app->DB->SelectRow('SELECT * FROM layoutvorlagen WHERE id = ' . $layout);

      $layoutpositionen = $this->app->DB->SelectArr('SELECT * FROM layoutvorlagen_positionen WHERE layoutvorlage = ' . $layout['id']);
      if($pdf->GetLayoutvorlage($layout['id'],$data)) {
        $pdf->filename = 'Besuchsbericht'.($adressen['kundennummer']?'_'.$adressen['kundennummer']:'').'.pdf';
        $pdf->displayDocument($name);
      }
    }
    $this->app->ExitXentral();
  }
  $this->app->YUI->AutoComplete("ansprechpartner","ansprechpartner","","&adresse=$id");
  $this->app->YUI->AutoComplete("projekt","projektname",1);
  $this->app->YUI->TagEditor('internebezeichnung', array('width'=>370));

  $pType = $this->app->Secure->GetPOST('type');
  $do = $this->app->Secure->GetPOST('do');

  $json = array();

  $json['status'] = 0;
  $json['statusText'] = '';

  if (isset($pType) && !empty($pType)) {

    $newId = 0;
    if($pType !== 'wiedervorlage' && $pType !== 'kalender')
    {
      $newId = $this->AdresseBriefSaveDocument();
    } else {
      if($pType === 'kalender')
      {
        $newId = $this->AdresseBriefSaveKalender();
      }else{
        $newId = $this->AdresseBriefSaveWiedervorlage();
      }
    }

    if ($newId > 0) {
      $json['status'] = 1;
      $json['statusText'] = 'Das Dokument wurde gespeichert.';
      $json['statusId'] = $newId;
      if ($pType === 'notiz') {
        $this->app->erp->RunHook('address_note_created', 1, $newId);
      }
    }

    if($pType !== 'email' || !$this->app->Secure->GetPOST('save'))
    {    
      if (isset($do) && !empty($do)) {

        switch ($do) {
          case 'PDF':

            $json['id'] = $newId;
            $json['responseType'] = 'PDF';
            $json['statusText'] = 'PDF wurde erstellt.';

            $this->app->DB->Update('
                UPDATE
                dokumente
                SET
                sent = 1
                WHERE
                id ='.$newId.'
                ');
 

            break;
          case 'DRUCKEN':
            $drucker = $this->app->Secure->GetPOST('drucker');
    
            $this->app->erp->BriefpapierHintergrundDisable($drucker);

            $tmpBrief = $this->AdresseBriefCreatePDF($newId, false);
            $this->app->printer->Drucken($drucker, $tmpBrief);

            $this->app->DB->Update('
                UPDATE
                dokumente
                SET
                sent = 1
                WHERE
                id ='.$newId.'
                ');
 

            unlink($tmpBrief);
            $json['statusText'] = 'Dokument wird gedruckt.';
            $json['responseType'] = 'TEXT';

            break;
          case 'EMAIL':

            $data = $this->app->DB->SelectRow('
                SELECT
                *
                FROM
                dokumente
                WHERE
                id = ' . $newId . '
                ');

            // TODO MK korrekte cc und bcc holen
            $cc = $this->app->erp->get_emails($data['email_cc']);
            $bcc = $this->app->erp->get_emails($data['email_bcc']);

            //TODO MK email_an und to richtig extrahieren
            //$email_string = 'Benedikt Sauter <sauter@embedded-projects.net>';
            $tmp_an = trim($data['email_an']);
            list($data['to'], $data['email_an']) = explode(' <', trim($data['email_an'], '> '));
            list($data['von'], $data['email']) = explode(' <', trim($data['von'], '> '));

            if($data['email_an']=="") {
              $data['email_an'] = $tmp_an;
              $data['to'] = $tmp_an;
            }

            //$this->app->erp->LogFile("BCC $bcc CC $cc");

            $dateien = array();
            foreach($_POST as $pk => $pv)
            {
              $pka = explode('_',$pk);
              if($pka[0] === 'datei' && isset($pka[1]) && $pka[1] && is_numeric($pka[1]))
              {
                $dateiname = $this->app->erp->GetDateiName($pka[1]);
                if($dateiname)
                {
                  $dateiinhalt = $this->app->erp->GetDatei($pka[1]);
                  
                  if($handle = fopen ($this->app->erp->GetTMP().$dateiname, "wb"))
                  {
                    fwrite($handle, $dateiinhalt);
                    fclose($handle);
                    $dateien[] = $this->app->erp->GetTMP().$dateiname;
                  }                   
                }
              }
            }
            
            if(strpos($data['betreff'],'{') !== false) {
              $data['betreff'] = $this->app->erp->ParseUserVars('adresse', $data['adresse_to'], $data['betreff']);
            }
            if(strpos($data['content'],'{') !== false) {
              $data['content'] = $this->app->erp->ParseUserVars('adresse', $data['adresse_to'], $data['content']);
            }
      
            $mailSend = $this->app->erp->MailSend(
                $data['email'],
                $data['von'],
                $data['email_an'],
                $data['to'],
                $data['betreff'],
                $data['content'],
                $dateien,
                $projekt,true,$cc,$bcc
                );
                
            if($dateien && is_array($dateien))
            {
              foreach($dateien as $datei)unlink($datei);
            }

            if($mailSend =='1') {
              $json['status'] = 1;
              $json['type'] = 'email';
              $json['statusText'] = 'Email wurde gesendet.';
              $this->app->DB->Update('
                  UPDATE
                  dokumente
                  SET
                  sent = 1,
                  send_as = "email"
                  WHERE
                  id = ' . $data['id'] . '
                  ');
            } else {
              $json['status'] = 0;
              $json['statusText'] = 'Email konnte nicht gesendet werden. ('.$this->app->erp->mail_error.')';
            }

          break;

          default:
          break;
        }

      }

      echo json_encode($json);
      $this->app->ExitXentral();
    }
  }

  $id = $this->app->Secure->GetGET('id');

  $this->AdresseMenu();
 

  $this->app->User->SetParameter('adresse_brief_adresseId', $id);

  $count = 0;
  $count += $this->app->DB->Select('SELECT count(id) FROM dokumente WHERE adresse_to = ' . $id);
  $count += $this->app->DB->Select('SELECT count(id) FROM dokumente_send WHERE adresse = ' . $id);
  $count += $this->app->DB->Select('SELECT count(id) FROM wiedervorlage WHERE adresse = ' . $id);
  $count += $this->app->DB->Select('SELECT count(id) FROM kalender_event WHERE adresse = '.$id);

  if ($count > 0) {
    $this->app->YUI->TableSearch('TABELLE', 'adresse_brief');
    $this->app->Tpl->Set('TABELLEFLAG', 0);
  } else {
    $this->app->Tpl->Set('TABELLE', '<br><div class="info">Es sind noch keine Einträge vorhanden.</div>');
    $this->app->Tpl->Set('TABELLEFLAG', 1);
  }

  if(!$this->app->erp->RechteVorhanden('wiedervorlage','list'))
  {
    $this->app->Tpl->Set('VORWIEDERVORLAGE','<!-- ');
    $this->app->Tpl->Set('NACHWIEDERVORLAGE',' -->');
  }
/*
  if(!$this->app->erp->RechteVorhanden('ticket','offene'))
  {
    $this->app->Tpl->Set('VORTICKETS','<!-- ');
    $this->app->Tpl->Set('NACHTICKETS',' -->');
  }
*/


  $cmd = $this->app->Secure->GetPOST('cmd');
  if($cmd=="crm")
    $this->app->Tpl->Parse('CRMTABELLE',"adresse_crm_tabelle.tpl");


  $this->app->Tpl->Parse('TAB1',"adresse_brief.tpl");
  $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  $befehl = $this->app->User->GetParameter('adresse_brief_befehl');
  if($befehl)
  {
    $this->app->User->SetParameter('adresse_brief_befehl','');
    $this->app->Tpl->Add('PAGE','
    <script type="text/JavaScript" language="javascript">
    $(document).ready(function() {
      '.$befehl.'
    });
    </script>
    ');
  }

  // Wenn GET-Parameter "telefon_notiz_erstellen" gesetzt ist, direkt Anlegen-Dialog öffnen
  $telefonNotizErstellen = (int)$this->app->Secure->GetGET('telefon_notiz_erstellen');
  if ($telefonNotizErstellen === 1) {
    $this->app->Tpl->Add('PAGE','
      <script type="text/javascript">
      $(document).ready(function() { briefAnlegen(\'telefon\'); });
      </script>
    ');
  }
}



function AdresseBriefEditPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id= $this->app->Secure->GetGET('id');
  $sid= $this->app->Secure->GetPOST('sid');

  if($frame=="false")
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(800,650,"index.php?module=adresse&action=briefeditpopup&id=$id&sid=$sid");
  } else {

    $adresse = $id;

    $typ = $this->app->DB->Select("SELECT dokument FROM dokumente_send WHERE id='$sid' LIMIT 1");
    //$parameter = $this->app->DB->Select("SELECT parameter FROM dokumente_send WHERE id='$sid' LIMIT 1");
    $parameter = $sid;

    //echo "typ = $typ ".$parameter;

    $this->app->erp->DokumentMask('PAGE',$typ,$parameter,$adresse,'',true);

    $this->app->BuildNavigation=false;
  }
}


function AdresseBriefDelete()
{
  $sid = $this->app->Secure->GetPOST('sid');
  $id = $this->app->Secure->GetGET('id');

  $this->app->DB->Update("UPDATE dokumente_send SET geloescht=1 WHERE id='$sid' LIMIT 1");

  $this->AdresseBrief();
}

function AdresseBriefPDF()
{
  $sid = $this->app->Secure->GetGET('sid'); // 30.03.19 von POST zu GET BS
  $id = $this->app->Secure->GetGET('id');

  //$Brief = new Geschaeftsbrief(&$this->app,$sid);
  $projekt = $this->app->DB->Select("SELECT projekt FROM dokumente WHERE id='$sid'");
  $Brief = new BriefPDF($this->app,$projekt);
  $Brief->GetBrief($sid);
  $Brief->displayDocument();

  $this->AdresseBrief();
}


function AdresseBelege()
{
  $cmd = $this->app->Secure->GetPOST('cmd');
  $id = $this->app->Secure->GetGET('id');
  $von = $this->app->User->GetParameter("adresse_belege_von");
  $bis = $this->app->User->GetParameter("adresse_belege_bis");

  if($cmd == 'kundensaldodatumsrelevant'){
    $von = $this->app->Secure->GetGET("von");
    $bis = $this->app->Secure->GetGET("bis");
  }
  $von = date_format(date_create_from_format('d.m.Y', $von), 'Y-m-d');
  $bis = date_format(date_create_from_format('d.m.Y', $bis), 'Y-m-d');

  $rechnungt = $this->app->DB->Select("SELECT SUM(soll-ist) FROM rechnung WHERE status != 'angelegt' AND zahlungsstatus != 'bezahlt' AND adresse = '$id' AND datum >= '$von' AND datum <= '$bis'");
  //$gutschriftt = $this->app->DB->Select("SELECT SUM(soll-ist) FROM gutschrift WHERE status != 'angelegt' AND (manuell_vorabbezahlt != '0000-00-00' OR manuell_vorabbezahlt IS NOT NULL) AND adresse = '$id' AND datum >= '$von' AND datum <= '$bis'");
  $gutschriftt = $this->app->DB->Select("SELECT SUM(soll-ist) FROM gutschrift WHERE status != 'angelegt' AND (manuell_vorabbezahlt = '0000-00-00' OR manuell_vorabbezahlt IS NULL) AND zahlungsstatus != 'bezahlt' AND rechnungid = 0 AND adresse = '$adresse' AND datum >= '$von' AND datum <= '$bis'");
  $verbindlichkeitt = $this->app->DB->Select("SELECT SUM(betrag-betragbezahlt) FROM verbindlichkeit WHERE status = 'offen' AND adresse = '$id' AND rechnungsdatum >= '$von' AND rechnungsdatum <= '$bis'");
  $kundensaldot = $rechnungt - $gutschriftt - $verbindlichkeitt;
  $kundensaldot = round($kundensaldot, 2);

  if($kundensaldot > 0){
    $kundensaldot = "<font color=red>-".number_format($kundensaldot,2,',','.')."</font>";
  }else if($kundensaldot == 0){
    $kundensaldot = "0,00";
  }else{
    $kundensaldot = number_format($kundensaldot,2,',','.');
  }
  if($cmd === 'kundensaldodatumsrelevant'){
    echo json_encode($kundensaldot);
    $this->app->ExitXentral();
  }
  $this->AdresseMenu();

  $kreditlimit = $this->app->DB->Select("SELECT kreditlimit FROM adresse WHERE id='$id' LIMIT 1");
 // $saldo = $this->app->erp->SaldoAdresse($id);

  $kreditlimit_frei = $kreditlimit - $saldo;

  if($kreditlimit <=0) { 
    $kreditlimit="kein Limit";
    $kreditlimit_frei = "kein Limit";
  } else $kreditlimit = number_format($kreditlimit,2,',','.');

  $this->app->Tpl->Set('KREDITLIMIT',$kreditlimit);
  $this->app->Tpl->Set('KREDITLIMITFREI',$kreditlimit_frei);


  //$this->app->Tpl->Set('UMSATZ',number_format($this->app->erp->UmsatzAdresseAuftragJahr($id),2,',','.'));
  $this->app->Tpl->Set('UMSATZ',number_format($this->app->erp->UmsatzAdresseRechnungJahr($id),2,',','.'));

 /* $saldo = round($saldo,2);

  if($saldo > 0)
    $saldo = "<font color=red>-".number_format($saldo,2,',','.')."</font>";
  else if ($saldo==0) $saldo="0,00";
  else $saldo = number_format($saldo,2,',','.');
*/
//  $this->app->Tpl->Set('SALDO',$saldo);


  $kundensaldo = $this->app->erp->KundenSaldo($id);

  if($kundensaldo > 0){
    $kundensaldo = "<font color=red>-".number_format($kundensaldo,2,',','.')."</font>";
  }else if($kundensaldo == 0){
    $kundensaldo = "0,00";
  }else{
    $kundensaldo = number_format($kundensaldo,2,',','.');
  }

  $this->app->Tpl->Set('KUNDENSALDO', $kundensaldo);
  $this->app->Tpl->Set('KUNDENSALDODATUMSRELEVANT', $kundensaldot);

  if($this->app->erp->IsAdresseSubjekt($id,"Lieferant")){
    $this->app->Tpl->Set('MITTEBESTELLUNG',

      '<li class="filter-item">
        <label for="bestellung" class="switch">
          <input type="checkbox" value="1" id="bestellung" />
          <span class="slider round"></span>
        </label>
        <label for="bestellung">{|Bestellung|}</label>
       </li>
       <li class="filter-item">
        <label for="verbindlichkeit" class="switch">
          <input type="checkbox" value="1" id="verbindlichkeit" />
          <span class="slider round"></span>
        </label>
        <label for="verbindlichkeit">{|Verbindlichkeit|}</label>
       </li>');



  }

  $this->app->Tpl->Set('ID',$id);

  $this->app->YUI->DatePicker("von");
  $this->app->YUI->DatePicker("bis");

  $this->app->YUI->AutoSaveUserParameter("von","adresse_belege_von");
  $this->app->YUI->AutoSaveUserParameter("bis","adresse_belege_bis");

  $this->app->YUI->TableSearch('TAB1',"adressebelege");
  //$this->app->YUI->TableSearch('TAB2',"adresse_auftrag");
  //$this->app->YUI->TableSearch('TAB3',"adresse_rechnung");
  //$this->app->YUI->TableSearch('TAB4',"adresse_gutschrift");
  //$this->app->YUI->TableSearch('TAB5',"adresse_lieferschein");

  $this->app->Tpl->Parse('PAGE',"adresse_belege.tpl");
}



function AdresseEmail()
{
  $this->AdresseMenu();


  // NEU füllen
  $widget = new WidgetEmail($this->app,'TAB2');
  $widget->Create();

  // UEBERSICHT füllen
  $this->app->Tpl->Set('HEADING',"Adresse");
  $this->app->Tpl->Set('SUBHEADING',"Email schreiben");
  $adresse = $this->app->User->GetAdresse();

  //Offene Aufgaben
  $table = new EasyTable($this->app);
  $table->Query("SELECT betreff, id FROM email");
  $table->DisplayNew('INHALT', "<a href=\"index.php?module=adresse&action=emaileditpopup&frame=false&id=%value%\" 
      onclick=\"makeRequest(this);return false\">Bearbeiten</a>");
  $this->app->Tpl->Parse('TAB1',"rahmen.tpl");

  // PARSE
  $this->app->Tpl->Set('AKTIV_TAB1',"selected");

  $this->app->Tpl->Parse('PAGE',"emailuebersicht.tpl");

}



function AdresseEmailEditPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  if($frame=="false")
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(510,610);
  } else {
    // nach page inhalt des dialogs ausgeben
    $widget = new WidgetEmail($this->app,'PAGE');
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }
}




function AdresseSuchmaske()
{
  $typ=$this->app->Secure->GetGET("typ");

  $this->app->Tpl->Set('HEADING',"Suchmaske f&uuml;r Adressen");
  $table = new EasyTable($this->app);
  switch($typ) {
    case "auftragrechnung":
      $table->Query("SELECT typ,name, ort, plz, strasse, abteilung, unterabteilung, ustid, email, adresszusatz, id as kundeadressid, id FROM adresse WHERE geloescht=0
          order by name");
      break;
    case "auftraglieferschein":
      $table->Query("SELECT typ as liefertyp, name as liefername, ort as lieferort, plz as lieferplz, strasse as lieferstrasse, abteilung as lieferabteilung, unterabteilung
          as lieferunterabteilung, adresszusatz as lieferadresszusatz, id as lieferadressid  FROM adresse WHERE geloescht=0 order by name");
      break;
    default:
      $table->Query("SELECT typ,name, ort, plz, strasse, abteilung, unterabteilung, ustid, email, adresszusatz, id as kundeadressid, id FROM adresse WHERE geloescht=0 order by name");
  }

  $table->DisplayWithDelivery('PAGE');

  $this->app->BuildNavigation=false;
}



function AdresseKundevorlage()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET('id');
  // prufe ob es schon einen eintrag gibt
  $check = $this->app->DB->Select("SELECT id FROM kundevorlage WHERE adresse='$id' LIMIT 1");
  if( !($check > 0 && is_numeric($check)))
  {
    $this->app->DB->Insert("INSERT INTO kundevorlage (id,adresse) VALUES ('','$id')");
  }

  $check = $this->app->DB->Select("SELECT id FROM kundevorlage WHERE adresse='$id' LIMIT 1");
  $this->app->Secure->GET['id']=$check;
  $this->app->Tpl->Set('AKTIV_TAB1',"selected");
  $widget = new WidgetKundevorlage($this->app,'PAGE');
  $widget->Edit();
  $this->app->Secure->GET['id']=$id;
}

function AdresseAddPosition()
{
  $sid = $this->app->Secure->GetPOST('sid');
  $id = $this->app->Secure->GetGET('id');
  $menge = $this->app->Secure->GetGET("menge");
  $datum  = $this->app->Secure->GetGET("datum");
  $datum  = $this->app->String->Convert($datum,"%1.%2.%3","%3-%2-%1");
  $tmpid = $this->app->erp->AddAdressePosition($id, $sid,$menge,$datum);

  $art  = $this->app->Secure->GetGET("art");

  if(!$this->app->erp->CheckDateValidate($datum)) $datum=date('Y-m-d');

  if($art==='abo'){
    $this->app->DB->Update("UPDATE abrechnungsartikel SET wiederholend=1,startdatum='$datum',zahlzyklus=1 WHERE id='$tmpid' LIMIT 1");
  }
  else if ($art==='einmalig'){
    $this->app->DB->Update("UPDATE abrechnungsartikel SET wiederholend=0,preisart='einmalig' WHERE id='$tmpid' LIMIT 1");
  }
  
  $this->app->DB->Update("UPDATE abrechnungsartikel SET angelegtvon='".$this->app->User->GetID()."',angelegtam=NOW() WHERE id='$tmpid' LIMIT 1");

  $this->app->Location->execute("index.php?module=adresse&action=artikel&id=$id");
}

function AdresseLieferantvorlage()
{

  //zahlungsweise   zahlungszieltage  zahlungszieltageskonto  zahlungszielskonto  versandart
  //zahlungsweiselieferant  zahlungszieltagelieferant   zahlungszieltageskontolieferant   zahlungszielskontolieferant   versandartlieferant
  $arr = $this->app->DB->SelectArr("SELECT id,kundennummerlieferant FROM adresse WHERE lieferantennummer >0");

  foreach($arr as $key=>$value)
  {
    if($value['kundennummerlieferant']=="")
    {
      $id = $value['id'];
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
      $zahlungsweiselieferant = $this->app->DB->Select("SELECT zahlungsweise FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
      $zahlungszieltagelieferant = $this->app->DB->Select("SELECT zahlungszieltage FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
      $zahlungszieltageskontolieferant = $this->app->DB->Select("SELECT zahlungszielskonto FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
      $versandartlieferant = $this->app->DB->Select("SELECT versandart FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");

      if($kundennummer !="")	
      {
        echo "UPDATE adresse SET kundennummerlieferant='$kundennummer',zahlungsweiselieferant='$zahlungsweiselieferant',
             zahlungszieltagelieferant='$zahlungszieltagelieferant',zahlungszieltageskontolieferant='$zahlungszieltageskontolieferant',
             versandartlieferant='$versandartlieferant' WHERE id='$id';";
      } 
    }

  }


  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET('id');
  // prufe ob es schon einen eintrag gibt
  $check = $this->app->DB->Select("SELECT id FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
  if( !($check > 0 && is_numeric($check)))
  {
    $this->app->DB->Insert("INSERT INTO lieferantvorlage (id,adresse) VALUES ('','$id')");
  }

  $check = $this->app->DB->Select("SELECT id FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
  $this->app->Secure->GET['id']=$check;
  $this->app->Tpl->Set('AKTIV_TAB1',"selected");
  $widget = new WidgetLieferantvorlage($this->app,'PAGE');
  $widget->Edit();
  $this->app->Secure->GET['id']=$id;
}

  /**
   * @param int $addressId
   *
   * @return float
   */
public function getAboMonthSum($addressId) {
  if(empty($addressId)) {
    return 0.00;
  }
  $sum = (float)$this->app->DB->Select(
    sprintf(
      "SELECT SUM(
          abo.menge * abo.preis * (1 - rabatt / 100)
          * 
          IF(
              abo.preisart = 'jahr' OR abo.preisart = '360tage', 
              IF(abo.zahlzyklus <= 1, 1/12,1 / (abo.zahlzyklus * 12)),
              IF(
                  abo.preisart = 'wochen', 
                  IF(abo.zahlzyklus <= 1,  7 / 30, 1 / (abo.zahlzyklus * 30 / 7)) ,
                  IF(abo.preisart = 'einmalig', 
                     0,
                        IF(abo.preisart = 'monat' OR abo.preisart = '30tage',
                          1,
                          IF(abo.zahlzyklus <= 1, 1, 1/ (abo.zahlzyklus))
                    )
                  )
              )
          )
      )
      FROM abrechnungsartikel AS abo
      WHERE abo.adresse = %d 
        AND (abo.startdatum <= CURDATE() OR IFNULL(abo.startdatum,'0000-00-00') = '0000-00-00')
        AND (abo.enddatum >= CURDATE() OR IFNULL(abo.enddatum,'0000-00-00') = '0000-00-00')",
      $addressId
    )
  );

  return $sum;
}


function AdresseArtikelPosition($withmenu = true)
{
  if($withmenu) {
    $this->AdresseMenu();
  }
  $id = $this->app->Secure->GetGET('id');
  /* neu anlegen formular */
  $artikelart = $this->app->Secure->GetPOST("artikelart");
  $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
  $beschreibung = $this->app->Secure->GetPOST("beschreibung");
  $vpe = $this->app->Secure->GetPOST("vpe");
  $umsatzsteuerklasse = $this->app->Secure->GetPOST("umsatzsteuerklasse");
  $waehrung = $this->app->Secure->GetPOST("waehrung");
  $projekt= $this->app->Secure->GetPOST("projekt");
  $preis = $this->app->Secure->GetPOST("preis");
  $preis = str_replace(',','.',$preis);
  $menge = $this->app->Secure->GetPOST("menge");
  $menge = str_replace(',','.',$menge);
  $art = $this->app->Secure->GetPOST("art");
  $lieferdatum = $this->app->Secure->GetPOST("lieferdatum");
  $zahlzyklus = $this->app->Secure->GetPOST("zahlzyklus");
  $wiederholend= $this->app->Secure->GetPOST("wiederholend");
  $startdatum= $this->app->Secure->GetPOST("startdatum");

  if($lieferdatum=="") $lieferdatum=date("d.m.Y");

  $cmd= $this->app->Secure->GetGET('cmd');

  switch($cmd)
  {

    case "getArtikelData":
      $id= $this->app->Secure->GetPOST("artikel");
      $adresse = $this->app->Secure->GetPOST("adresse");
      $artikel = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE nummer='$id' AND nummer!='' LIMIT 1");
      $artikel[0]['nettopreis'] = $this->app->erp->GetVerkaufspreis($artikel[0]['id'],1,$adresse);
      echo json_encode($artikel[0]);
      $this->app->ExitXentral();
    break;

    case "getAboartikel":
      $id= $this->app->Secure->GetPOST('id');
      $adresse = $this->app->Secure->GetPOST("adresse");
      $artikel = $this->app->DB->SelectArr("SELECT aba.*, a.nummer FROM abrechnungsartikel aba inner JOIN artikel a ON (a.id = aba.artikel) WHERE aba.id='$id' AND aba.adresse='$adresse' LIMIT 1");
      $AboGruppen_tmp = $this->app->DB->SelectArr("SELECT id,beschreibung FROM abrechnungsartikel_gruppe where (adresse = '$adresse' OR adresse = 0) ORDER BY sort");
      if(is_array($AboGruppen_tmp)) {
        $AboGruppen = '<option>Bitte auswählen</option>';
      }else{
        $AboGruppen = '';
      }
      foreach($AboGruppen_tmp as $value){
        if($value['id'] == $artikel[0]['gruppe']){
          $selected = " selected=\"selected\"";
        }else{
          $selected='';
        }
        $AboGruppen .= '<option value="' . $value['id'] . '"  '.$selected.'>' . $value['beschreibung'] . '</option>';
      }

      if(is_array($artikel[0])) {
        $artikel[0]['startdatum'] = $this->app->String->Convert($artikel[0]['startdatum'], "%3-%2-%1", "%1.%2.%3");
        $artikel[0]['lieferdatum'] = $this->app->String->Convert($artikel[0]['lieferdatum'], "%3-%2-%1", "%1.%2.%3");
        $artikel[0]['abgerechnetbis'] = $this->app->String->Convert($artikel[0]['abgerechnetbis'], "%3-%2-%1", "%1.%2.%3");
        $artikel[0]['abgrechnetam'] = $this->app->String->Convert($artikel[0]['abgrechnetam'], "%3-%2-%1", "%1.%2.%3");
        $artikel[0]['enddatum'] = $this->app->String->Convert($artikel[0]['enddatum'], "%3-%2-%1", "%1.%2.%3");
        $artikel[0]['gruppen'] = $AboGruppen;
        echo json_encode($artikel[0]);
      }else{
        $basis['startdatum'] = date("d.m.Y");
        $basis['lieferdatum'] = "00.00.0000";
        $basis['abgerechnetbis'] = "00.00.0000";
        $basis['abgrechnetam'] = "00.00.0000";
        $basis['enddatum'] = "00.00.0000";
        $basis['preisart'] = "monat";
        $basis['preis'] = '0.00';
        $basis['menge'] = '0.00';
        $basis['status'] = "1";
        $basis['dokument'] = "rechnung";
        $basis['gruppen'] = $AboGruppen;
        echo json_encode($basis);
      }
      $this->app->ExitXentral();
    break;


    case "moveAboArtikel":
      $id= $this->app->Secure->GetPOST('id');
      $direction = $this->app->Secure->GetPOST("direction");
      // wir schauen erstmal ob der Eintrag in einer Gruppe ist, und holen dann alle Artikel dieser gruppe
      $artikel_list = $this->app->DB->SelectArr("SELECT id FROM abrechnungsartikel where gruppe=(Select gruppe from abrechnungsartikel where id='$id') order by sort asc");
      $counter=0;
      foreach ($artikel_list as $value){
        if($value['id'] == $id){
          $moveItem = $counter;
        }else{
          $counter++;
        }
        $artikel[] = $value['id'];
      }

      if($direction === 'down'){
        $neuArtikelSortierung = $this->down($artikel,$moveItem);
      } else{
        $neuArtikelSortierung = $this->up($artikel,$moveItem);
      }

      foreach ($neuArtikelSortierung as $key => $value){
        //$neuePos=0;
        $neuePos = $key+1;
        $update = $this->app->DB->Update("UPDATE abrechnungsartikel set sort='$neuePos' where id='$value'");
      }
      echo json_encode(array(
          'status' => 1
      ));
      $this->app->ExitXentral();
    break;
    case "saveAboartikel":
      $id= $this->app->Secure->GetPOST('id');
      $adresse = $this->app->Secure->GetPOST("adresse");
      $artikel = $this->app->Secure->GetPOST("artikel");
      $artikelid = $this->app->Secure->GetPOST("artikelid");
      $artikelueberschreiben = $this->app->Secure->GetPOST("artikelueberschreiben");
      $artikelneuanlage = $this->app->Secure->GetPOST("artikelneuanlage");
      $preisart= $this->app->Secure->GetPOST("preisart");
      $rabatt= $this->app->Secure->GetPOST("rabatt");
      $dokument= $this->app->Secure->GetPOST("dokument");
      $gruppe= $this->app->Secure->GetPOST("gruppe");
      $sort= $this->app->Secure->GetPOST("sort");
      $startdatum= $this->app->String->Convert($this->app->Secure->GetPOST("startdatum"),"%1.%2.%3","%3-%2-%1");
      $enddatum= $this->app->String->Convert($this->app->Secure->GetPOST("enddatum"),"%1.%2.%3","%3-%2-%1");
      $abgerechnetbis= $this->app->String->Convert($this->app->Secure->GetPOST("abgerechnetbis"),"%1.%2.%3","%3-%2-%1");
      $abgrechnetam= $this->app->String->Convert($this->app->Secure->GetPOST("abgrechnetam"),"%1.%2.%3","%3-%2-%1");
      $bemerkung= $this->app->Secure->GetPOST("bemerkung");
      $experte= $this->app->Secure->GetPOST("experte");
      $bezeichnung= $this->app->Secure->GetPOST("bezeichnung");
      $beschreibungersetzten= $this->app->Secure->GetPOST("beschreibungersetzten");

      $beschreibung= $this->app->Secure->GetPOST("beschreibung");

      if(!$this->app->erp->CheckDateValidate($startdatum)) $startdatum=date('Y-m-d');

      // prüfen ob ein neuer Artikel gesetzt ist
      if($artikelneuanlage == '1'){
        $tmp_id = explode(" ",$artikel);
        $tmp_id = $tmp_id[0];

        $artikeltmp = $this->app->DB->SelectArr("SELECT id,name_de,anabregs_text FROM artikel WHERE nummer='$tmp_id' LIMIT 1");
        $artikeltmp[0]['waehrung'] = $this->app->DB->Select("SELECT waehrung FROM adresse WHERE id = '".$adresse."'");

        if($artikeltmp[0]['waehrung'] == ''){
          $waehrung = "EUR";
        }
        else {
          $waehrung=$artikeltmp[0]['waehrung'];
        }

        $anzahlGruppe = $this->app->DB->Select("SELECT count(gruppe) FROM abrechnungsartikel WHERE gruppe='$gruppe' AND adresse='$adresse'");

        if($sort == ''){$sort=$anzahlGruppe +1;}

        $this->app->DB->Insert("INSERT INTO abrechnungsartikel (
              id,artikel,bezeichnung,beschreibung,menge,preis, preisart,rabatt, dokument, sort,lieferdatum, steuerklasse, status,projekt,wiederholend,zahlzyklus,adresse,startdatum,enddatum,gruppe,experte,waehrung,abgerechnetbis, abgrechnetam,bemerkung, beschreibungersetzten)
          VALUES (
          '',
          '{$artikeltmp[0]['id']}',
          '$bezeichnung',
          '$beschreibung',
          '$menge',
          '$preis',
          '$preisart',
          '$rabatt',
          '$dokument',
          '$sort',
          '$lieferdatum',
          '$umsatzsteuerklasse',
          'angelegt',
          '$projekt',
          '$wiederholend',
          '$zahlzyklus',
          '$adresse',
          '$startdatum',
          '$enddatum',
          '$gruppe',
          '$experte',
          '$waehrung',
          '$abgerechnetbis',
          '$abgrechnetam',
          '$bemerkung',
          '$beschreibungersetzten'
          )");
      }else{
        $this->app->DB->Update("UPDATE abrechnungsartikel SET
          artikel='$artikelid',
          bezeichnung='$bezeichnung',
          beschreibung = '$beschreibung',
          menge = '$menge',
          preis = '$preis',
          preisart = '$preisart',
          rabatt = '$rabatt',
          dokument = '$dokument',
          gruppe = '$gruppe',
          sort = '$sort',
          startdatum = '$startdatum',
          zahlzyklus = '$zahlzyklus',
          enddatum = '$enddatum',
          abgerechnetbis = '$abgerechnetbis',
          abgrechnetam = '$abgrechnetam',
          bemerkung = '$bemerkung',
          experte = '$experte',
          beschreibungersetzten = '$beschreibungersetzten'

          WHERE id='$id' LIMIT 1");
      }
      echo json_encode(array(
          'status' => 1
      ));
      $this->app->ExitXentral();
      break;


    case 'savegruppe':
      $sid= $this->app->Secure->GetPOST('id');
      $ansprechpartner= $this->app->Secure->GetPOST('ansprechpartner');
      $beschreibung= $this->app->Secure->GetPOST('beschreibung');
      $beschreibung2= $this->app->Secure->GetPOST('beschreibung2');
      $rabatt = $this->app->Secure->GetPOST('rabatt');

      if($rabatt != ""){
        $rabatt = str_replace(',','.',$rabatt);
      }

      $rechnung= $this->app->Secure->GetPOST('rechnung');
      //if($rechnung!="1") $rechnung=0;

      $gruppensumme= $this->app->Secure->GetPOST('gruppensumme');
      if($gruppensumme!='1') {
        $gruppensumme=0;
      }

      $rechnungadresse= $this->app->Secure->GetPOST('rechnungadresse');
      $rechnungadresse = explode(' ',$rechnungadresse);
      $rechnungadresse = $rechnungadresse[0];
      $rechnungadresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$rechnungadresse' AND kundennummer!='' LIMIT 1");

      if($rechnungadresse <=0) $rechnungadresse = 0;

      $sort= $this->app->Secure->GetPOST('sort');

      $projekt= $this->app->Secure->GetPOST('projekt');
      $projekt = explode(' ',$projekt);
      $projekt = $projekt[0];
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND abkuerzung!='' LIMIT 1");

      $sammelrechnung = trim($this->app->Secure->GetPOST('sammelrechnung'));
      if($sammelrechnung != ''){
        $sammelrechnung = explode(' ', $sammelrechnung);
        $sammelrechnung = $sammelrechnung[0];
        $sammelrechnung = $this->app->DB->Select("SELECT id FROM adresse_abosammelrechnungen WHERE id = '$sammelrechnung' LIMIT 1");
      }else{
        $sammelrechnung = 0;
      }


      if($sid > 0)
      {
        $this->app->DB->Update("UPDATE abrechnungsartikel_gruppe SET beschreibung='$beschreibung',beschreibung2='$beschreibung2',rabatt = '$rabatt',ansprechpartner='$ansprechpartner',extrarechnung='$rechnung',projekt='$projekt',sort='$sort',gruppensumme='$gruppensumme',rechnungadresse='$rechnungadresse',sammelrechnung='$sammelrechnung' WHERE id='$sid' LIMIT 1");
      } else {
        $this->app->DB->Insert("INSERT INTO abrechnungsartikel_gruppe (id,beschreibung,beschreibung2,rabatt,ansprechpartner,extrarechnung,projekt,adresse,sort,gruppensumme,rechnungadresse,sammelrechnung) 
          VALUES ('','$beschreibung','$beschreibung2','$rabatt','$ansprechpartner','$rechnung','$projekt','$id','$sort','$gruppensumme','$rechnungadresse','$sammelrechnung') ");
      }
      echo json_encode(array(
          'status' => 1
      ));
      $this->app->ExitXentral();
    break;

    case 'getgruppe':
      $sid= $this->app->Secure->GetPOST('id');
      $tmp = $this->app->DB->SelectRow("SELECT * FROM abrechnungsartikel_gruppe WHERE id='$sid' LIMIT 1");
      $tmp['status']=1;
      if($tmp['projekt'] > 0){
        $tmp['projekt'] = $this->app->DB->Select("SELECT CONCAT(abkuerzung,' ',name) FROM projekt WHERE id='" . $tmp['projekt'] . "' LIMIT 1");
      }
      else{
        $tmp['projekt'] = '';
      }

      if($tmp['rechnungadresse'] > 0){
        $tmp['rechnungadresse'] = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name,if(ort!='',CONCAT(' (',ort,')'),'')) FROM adresse WHERE id='" . $tmp['rechnungadresse'] . "' LIMIT 1");
      }
      else{
        $tmp['rechnungadresse'] = '';
      }

      if($tmp['sammelrechnung'] > 0){
        $tmp['sammelrechnung'] = $this->app->DB->Select("SELECT CONCAT(id, ' ', bezeichnung) FROM adresse_abosammelrechnungen WHERE id = '" . $tmp['sammelrechnung'] . "' LIMIT 1");
      }
      else{
        $tmp['sammelrechnung'] = '';
      }

      $tmp['rabatt'] = str_replace('.',',',$tmp['rabatt']);


      echo json_encode($tmp);
      $this->app->ExitXentral();
    break;

    case 'deletegruppe':
      $sid= $this->app->Secure->GetPOST('id');
      $this->app->DB->Update("UPDATE `abrechnungsartikel` SET `gruppe`=0 WHERE `gruppe`='$sid'");
      $this->app->DB->Update("UPDATE `subscription_cycle_autosubscription` SET `subscription_group_id`=0 WHERE `subscription_group_id`='$sid'");
      $this->app->DB->Delete("DELETE FROM `abrechnungsartikel_gruppe` WHERE `id`='$sid' LIMIT 1");
      $tmp['status']=1;
      $tmp['statusText']='Gruppe gelöscht';
      echo json_encode($tmp);
      $this->app->ExitXentral();
    break;

  }


  $anlegen_artikelneu = $this->app->Secure->GetPOST('anlegen_artikelneu');


  if($anlegen_artikelneu!='')
  { 

    if($bezeichnung!='' && $menge!='' && $preis!='')
    { 

      $neue_nummer = $this->app->erp->NeueArtikelNummer($artikelart,$this->app->User->GetFirma(),$projekt);

      // anlegen als artikel
      $this->app->DB->Insert("INSERT INTO artikel (id,typ,nummer,projekt,name_de,umsatzsteuer,adresse,firma)  
          VALUES ('','$artikelart','$neue_nummer','$projekt','$bezeichnung','$umsatzsteuerklasse','$lieferant','".$this->app->User->GetFirma()."')");

      $artikel_id = $this->app->DB->GetInsertID();
      // einkaufspreis anlegen

      $this->app->DB->Insert("INSERT INTO verkaufspreise (id,artikel,adresse,objekt,projekt,preis,ab_menge,angelegt_am,bearbeiter)
          VALUES ('','$artikel_id','$id','Standard','$projekt','$preis','$menge',NOW(),'".$this->app->User->GetName()."')");

      $lieferdatum = $this->app->String->Convert($lieferdatum,'%1.%2.%3','%3-%2-%1');
      $startdatum= $this->app->String->Convert($startdatum,'%1.%2.%3','%3-%2-%1');

      if($art==='abo') {
        $wiederholend=1;
      } else {
        $wiederholend=0;
      }

       if(!$this->app->erp->CheckDateValidate($startdatum)) $startdatum=date('Y-m-d');
      $this->app->DB->Insert("INSERT INTO abrechnungsartikel (id,artikel,bezeichnung,nummer,menge,preis, sort,lieferdatum, steuerklasse, status,projekt,wiederholend,zahlzyklus,adresse,startdatum) 
          VALUES ('','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$wiederholend','$zahlzyklus','$id','$startdatum')");

      $this->app->Location->execute("index.php?module=adresse&action=artikel&id=$id");
    }
    $this->app->Tpl->Set('NEUMESSAGE',"<div class=\"error\">Bestellnummer, bezeichnung, Menge und Preis sind Pflichtfelder!</div>");
  }

  $ajaxbuchen = $this->app->Secure->GetPOST('ajaxbuchen');
  if($ajaxbuchen!='')
  {
    $artikel = $this->app->Secure->GetPOST('artikel');
    $nummer = $this->app->Secure->GetPOST('nummer');
    $projekt = $this->app->Secure->GetPOST('projekt');
    $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
    $sort = $this->app->DB->Select("SELECT MAX(sort) FROM angebot_position WHERE auftrag='$id' LIMIT 1");
    $sort = $sort + 1;
    $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
    $bezeichnung = $artikel;
    $neue_nummer = $nummer;
    $waehrung = 'EUR';
    $umsatzsteuerklasse = $this->app->DB->Select("SELECT umsatzsteuerklasse FROM artikel WHERE nummer='$nummer' LIMIT 1");
    $vpe = 'einzeln';

    //        $this->app->DB->Insert("INSERT INTO angebot_position (id,angebot,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
    //          VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");
  }

  $cmd = $this->app->Secure->GetGET('cmd');
  if($cmd === 'smlsave'){

    $smlid = (int)$this->app->Secure->GetPOST('id');
    $bezeichnung = trim($this->app->Secure->GetPOST('bezeichnung'));
    $smlrabatt = trim($this->app->Secure->GetPOST('smlrabatt'));
    $abweichende_rechnungsadresse = trim($this->app->Secure->GetPOST('abwrechnungsadresse'));
    $projekt = trim($this->app->Secure->GetPOST('projekt'));
    $adressid = trim($this->app->Secure->GetPOST('aid'));

    $error = '';

    if($bezeichnung == ''){
      $error .= "Bitte Bezeichnung ausfüllen\n";
    }

    if($smlrabatt != ''){
      $smlrabatt = str_replace(',','.',$smlrabatt);
    }

    if($abweichende_rechnungsadresse != ''){
      $abwrg = explode(' ', $abweichende_rechnungsadresse);
      $abwrgid = $abwrg[0];

      $abwrgid = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '$abwrgid' LIMIT 1");
      if($abwrgid != ''){
      }else{
        $error .= 'Bitte gültige abweichende Rechnungsadresse ausfüllen'."\n";
      }
    }

    if($projekt != ''){
      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projekt' LIMIT 1");
      if($projektid != ''){
      }else{
        $error .= 'Bitte gültiges Projekt ausfüllen'."\n";
      }
    }

    if($adressid != ''){
      $adressid = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '$adressid' LIMIT 1");
      if($adressid != ''){
      }else{
        $error .= 'Keine gültige Adresse'."\n";
      }
    }

    if($error == ''){
      if($smlid){
        $this->app->DB->Update("UPDATE adresse_abosammelrechnungen SET bezeichnung = '$bezeichnung', rabatt = '$smlrabatt', abweichende_rechnungsadresse = '$abwrgid', projekt = '$projektid' WHERE id = '$smlid'");

        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
      $this->app->DB->Insert("INSERT INTO adresse_abosammelrechnungen (bezeichnung, rabatt, abweichende_rechnungsadresse, projekt, adresse) VALUES ('$bezeichnung', '$smlrabatt', '$abwrgid', '$projektid', '$adressid')");
      echo json_encode(array('status'=>1));
      $this->app->ExitXentral();
    }
    echo json_encode(array('status'=>0,'statusText'=>$error));
    $this->app->ExitXentral();
  }
  if($cmd === 'smledit'){
    $smlid = (int)$this->app->Secure->GetPOST('id');
      
    $data = $this->app->DB->SelectRow("SELECT abs.id, abs.bezeichnung, abs.rabatt, abs.abweichende_rechnungsadresse, abs.projekt FROM adresse_abosammelrechnungen as abs WHERE abs.id = '$smlid' LIMIT 1");

    if($data){
      if($data['abweichende_rechnungsadresse'] == 0){
        $data['abweichende_rechnungsadresse'] = '';
      }else{
        if($data['abweichende_rechnungsadresse'] != ''){
          $abwadressid = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '".$data['abweichende_rechnungsadresse']."' LIMIT 1");
          if($abwadressid != ""){
            $abwadressname = $this->app->DB->Select("SELECT name FROM adresse WHERE id = '$abwadressid' LIMIT 1");
            if($abwadressname != ""){
              $abwkundennr = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$abwadressid' LIMIT 1");
              $abwlieferantennr = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id = '$abwadressid' LIMIT 1");

              $restadresse = '';
              $restadresse .= '(Kdr: '.$abwkundennr.' ';

              if($abwlieferantennr != ''){
                $restadresse .= 'Liefr: '.$abwlieferantennr;
              }

              $restadresse .= ')';

              $data['abweichende_rechnungsadresse'] = $abwadressid.' '.$abwadressname." ".$restadresse;

            }
          }
        }
      }


      if($data['projekt'] == 0){
        $data['projekt'] = '';
      }else{
        if($data['projekt'] > 0){
          $projektabkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '".$data['projekt']."' LIMIT 1");
          if($projektabkuerzung != ''){
            $data['projekt'] = $projektabkuerzung;
          }else{
            $data['projekt'] = '';
          }
        }
      }

      if($data['rabatt'] != ''){
        $data['rabatt'] = str_replace('.', ',', $data['rabatt']);
      }

    }else{
      $data['id'] = 0;
      $data['bezeichnung'] = '';
      $data['rabatt'] = '';
      $data['abweichende_rechnungsadresse'] = '';
      $data['projekt'] = '';
    }

    echo json_encode($data);
    $this->app->ExitXentral();


  }
  if($cmd === "smldelete"){
    $smlid = (int) $this->app->Secure->GetPOST('id');
    $this->app->DB->Update("DELETE FROM adresse_abosammelrechnungen WHERE id = '$smlid'");
        
    echo json_encode(array('status'=>1));
    $this->app->ExitXentral();
  }



  if(1)
  {
    $this->app->Tpl->Set('ARTIKELART',$this->app->erp->GetSelect($this->app->erp->GetArtikelart(),$artikelart));
    $this->app->Tpl->Set('VPE',$this->app->erp->GetSelect($this->app->erp->GetVPE(),$vpe));
    $this->app->Tpl->Set('WAEHRUNG',$this->app->erp->GetSelect($this->app->erp->GetWaehrung(),$vpe));
    $this->app->Tpl->Set('UMSATZSTEUERKLASSE',$this->app->erp->GetSelect($this->app->erp->GetUmsatzsteuerklasse(),$umsatzsteuerklasse));
    $this->app->Tpl->Set('PROJEKT',$this->app->erp->GetProjektSelect($projekt));
    $this->app->Tpl->Set('PREIS',$preis);
    $this->app->Tpl->Set('MENGE',$menge);
    $this->app->Tpl->Set('LIEFERDATUM',$lieferdatum);
    $this->app->Tpl->Set('ZAHLZYKLUS',$zahlzyklus);
    $this->app->Tpl->Set('BEZEICHNUNG',$bezeichung);

    $this->app->Tpl->Set('SUBSUBHEADING','Neuen Artikel anlegen');
    //      $this->app->Tpl->Parse('INHALT',"aboabrechnungsartikel_artikelneu.tpl");
    //     $this->app->Tpl->Set(EXTEND,"<input type=\"submit\" value=\"Artikel anlegen\" name=\"anlegen_artikelneu\">");
    $this->app->Tpl->Parse('UEBERSICHT','rahmen70.tpl');
    $this->app->Tpl->Set('EXTEND','');
    $this->app->Tpl->Set('INHALT','');
    $this->app->YUI->DatePicker('startdatum');
    $this->app->YUI->DatePicker('enddatum');
    $this->app->YUI->DatePicker('abgerechnetbis');
    $this->app->YUI->CkEditor('beschreibung','belege');
    $this->app->YUI->CkEditor('editbeschreibung2','belege',array('height'=>'100'));

    /* ende neu anlegen formular */
    /* ende neu anlegen formular */
    // child table einfuegen

    //Wurde mit LiveTabelle ersetzt
   /*$menu = array(//"up"=>"upartikel",
        //                          "down"=>"downartikel",
        //"add"=>"addstueckliste",
        "edit"=>"positioneneditpopup",
        "del"=>"delartikel");
    // wiederholende artikel
    $sql = "SELECT aa.bezeichnung,art.nummer, au.name as bearbeiter, if(aa.angelegtam='0000-00-00','-',DATE_FORMAT(aa.angelegtam,'%d.%m.%Y')) as 'angelegt am', if(aa.abgerechnetbis='0000-00-00','-',DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y')) as 'abgerechnet bis', 
      if(aa.enddatum='0000-00-00','-',DATE_FORMAT(aa.enddatum,'%d.%m.%Y')) as enddatum,
      aa.preis as preis, aa.waehrung, aa.menge as menge, if(aa.wiederholend=1 OR aa.preisart='monat' OR aa.preisart='jahr' OR aa.preisart='monatx','wdh','einmalig') as art, aa.dokument,aa.id as id
      FROM abrechnungsartikel aa LEFT JOIN artikel art ON art.id=aa.artikel LEFT JOIN user u ON u.id=aa.angelegtvon LEFT JOIN adresse au ON au.id=u.adresse
      WHERE aa.adresse='$id'";

    $this->app->YUI->SortList('TAB1',$this,$menu,$sql,false);*/

    //    $this->app->YUI->TableSearch('TAB1',"adresse_abo");


    $this->app->YUI->AutoComplete('e_projekt', 'projektname', 1);
    $this->app->YUI->AutoComplete('e_abwrechnungsadresse', 'adresse');
    $this->app->YUI->AutoComplete('editsammelrechnung','abosammelrechnungen','',"&adresse=$id");


    $this->app->Tpl->Set('ADRESSE',$id);
    $this->app->YUI->TableSearch('TAB1', 'adressealleabos', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('SORTPOPUP', 'adressealleabossort', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('TAB2','abrechnungsartikel_gruppe', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('TAB3','abrechnungsartikel', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->YUI->TableSearch('TAB4', 'abosammelrechnungen', 'show','','',basename(__FILE__), __CLASS__);


    $this->app->YUI->AutoComplete('editansprechpartner','ansprechpartner','',"&adresse=$id");
    $this->app->Tpl->Set('MONATLICH', number_format($this->getAboMonthSum($id),2,',','.'));
    $this->app->Tpl->Parse('PAGE','adresse_abo.tpl');
  }
}


function AdresseSEPAMandat()
{
  $projekt = '';
  $id = $this->app->Secure->GetGET('id');

  if(class_exists('SepaMandatCustom'))
  {
    $pdf = new SepaMandatCustom($this->app,$projekt);
  }else{
    $pdf = new SepaMandat($this->app,$projekt);
  }



  $data = $this->app->DB->SelectArr("SELECT name,strasse, ansprechpartner,ort,plz,land,email,kundennummer, inhaber, bank, swift, iban,firmensepa,mandatsreferenzart,mandatsreferenz FROM adresse WHERE id='$id' LIMIT 1");
  $pdf->firmensepa=$data[0]['firmensepa'];
  $pdf->mandatsreferenzart=$data[0]['mandatsreferenzart'];


  $musterlinie = '_____________________________________';

  $pdf->firma=$this->app->erp->ReadyForPDF($data[0]['name']);
  if($pdf->firma=='') {
    $pdf->firma = $musterlinie;
  }
  $pdf->name=$this->app->erp->ReadyForPDF($data[0]['ansprechpartner']);
  if($pdf->name=='') {
    $pdf->name=$musterlinie;
  }
  $pdf->strasse=$this->app->erp->ReadyForPDF($data[0]['strasse']);
  if($pdf->strasse=='') {
    $pdf->strasse=$musterlinie;
  }
  $pdf->plzOrt=$this->app->erp->ReadyForPDF($data[0]['land']).'-'.$this->app->erp->ReadyForPDF($data[0]['plz'])." ".$this->app->erp->ReadyForPDF($data[0]['ort']);
  if($pdf->plzOrt=='-' || $pdf->plzOrt=='- ') {
    $pdf->plzOrt=$musterlinie;
  }
  $pdf->email=$this->app->erp->ReadyForPDF($data[0]['email']);
  if($pdf->email=='') {
    $pdf->email=$musterlinie;
  }

  $pdf->kundenNr=$this->app->erp->ReadyForPDF($data[0]['kundennummer']);
  //$pdf->benutzername="info@embedded-projects.net";

  $pdf->glID=$this->app->erp->Firmendaten('sepaglaeubigerid');

  if($data[0]['mandatsreferenz']==''){
    $pdf->mandatsRef = $this->app->erp->ReadyForPDF($data[0]['kundennummer']);
  }
  else{
    $pdf->mandatsRef = $data[0]['mandatsreferenz'];
  }

  $pdf->swift=$data[0]['swift'];
  if($pdf->swift=='') {
    $pdf->swift='_____________________________________';
  }
  $pdf->iban=$data[0]['iban'];
  if($pdf->iban=='') {
    $pdf->iban='_____________________________________';
  }
  $pdf->bank=$data[0]['bank'];
  if($pdf->bank=='') {
    $pdf->bank='_____________________________________';
  }

  $pdf->ermaechText='Ich ermaechtige die '.$this->app->erp->GetFirmaName().", Zahlungen von meinem Konto mittels Lastschrift einzuziehen. Zugleich weise ich mein Kreditinstitut an, die von der ".$this->app->erp->GetFirmaName()." ".$this->app->erp->Firmendaten("strasse")." ".$this->app->erp->Firmendaten("plz")." ".$this->app->erp->Firmendaten('ort').' auf mein Konto gezogenen Lastschriften einzuloesen.';

  if($pdf->firmensepa!='1')
  {
    $pdf->hinweis='Hinweis: Ich kann (Wir können) innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem (unserem) Kreditinstitut vereinbarten Bedingungen.';
  } else {
    $pdf->hinweis='Hinweis: Dieses Lastschriftmandat dient nur dem Einzug von Lastschriften, die auf Konten von Unternehmen gezogen sind. Ich / Wir sind nicht berechtigt, nach der erfolgten Einlösung eine Erstattung des belasteten Betrags zu verlangen. Ich bin / Wir sind berechtigt, mein / unser Kreditinstitut bis zum Fälligkeitstag anzuweisen, Lastschriften nicht einzulösen.';
  }

  $pdf->render();

  $name = $this->app->erp->UmlauteEntfernen($this->app->erp->ReadyForPDF($data[0]['name']));
  $name = str_replace(' ','',$name);
  $name = str_replace(',','',$name);
  $name = str_replace('.','',$name);
  $kundennummer = $data[0]['kundennummer'];

  $filename = $tmp.date('Ymd').'_'.$kundennummer.'_'.$name.'_SEPAMANDAT.pdf';
  $pdf->Output($filename,'D');
  $this->app->ExitXentral();
}

function AdresseArtikel()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET('id');

  // neues arbeitspaket
  //$widget = new WidgetAbrechnungsartikel(&$this->app,'TAB2');
  //$widget->Create();


  // child table einfuegen

  $menu = array('up'=>'upartikel',
      'down'=>'downartikel',
      //"add"=>"addstueckliste",
      'edit'=>'artikeleditpopup',
      'del'=>'delartikel');

  // wiederholende artikel
  $this->app->Tpl->Set('SUBSUBHEADING','wiederholende Artikel');
  $sql = "SELECT aa.bezeichnung, DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y') as abgerechnet, 
    aa.preis as preis, aa.waehrung, aa.menge as menge, aa.id as id
    FROM abrechnungsartikel aa
    WHERE aa.adresse='$id' AND aa.wiederholend=1";
  $this->app->YUI->SortList('INHALT',$this,$menu,$sql,false);
  $this->app->Tpl->Parse('TAB1','rahmen70.tpl');
  $this->app->Tpl->Set('INHALT','');


  // einmalige artikel
  $this->app->Tpl->Set('SUBSUBHEADING','einmalige Artikel');
  $sql = "SELECT aa.bezeichnung, DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y') as abgerechnet, 
    aa.preis as preis, aa.waehrung, aa.menge as menge, aa.id as id
    FROM abrechnungsartikel aa
    WHERE aa.adresse='$id' AND aa.wiederholend=0 AND aa.abgerechnet=0";
  $this->app->YUI->SortList('INHALT',$this,$menu,$sql,false);
  $this->app->Tpl->Parse('TAB1','rahmen70.tpl');

  $this->app->Tpl->Set('AKTIV_TAB1','selected');
  $this->app->Tpl->Parse('PAGE','artikeluebersicht.tpl');
}

function AdresseArtikelEditPopup()
{
  $id = $this->app->Secure->GetGET('id');

  // nach page inhalt des dialogs ausgeben
  $widget = new WidgetAbrechnungsartikel($this->app,'PAGE');
  $sid = $this->app->DB->Select("SELECT adresse FROM abrechnungsartikel WHERE id='$id' LIMIT 1");
  $widget->form->SpecialActionAfterExecute('close_refresh',
      "index.php?module=adresse&action=artikel&id=$sid");
  $widget->Edit();
  $this->app->BuildNavigation=false;
}

function UpArtikel()
{
  $this->app->YUI->SortListEvent('up','abrechnungsartikel','adresse');
  $this->AdresseArtikel();
}

function DownArtikel()
{
  $this->app->YUI->SortListEvent('down','abrechnungsartikel','adresse');
  $this->AdresseArtikel();
}


function DelArtikel()
{
  $this->app->YUI->SortListEvent('del','abrechnungsartikel','adresse');
  $this->AdresseArtikelPosition();
}

function AdresseVerein()
{
  $id = $this->app->Secure->GetGET('id');
  $submit = $this->app->Secure->GetPOST('submit');
  $this->AdresseMenu();

  $data['verein_mitglied_seit'] = $this->app->Secure->GetPOST('verein_mitglied_seit');
  $data['verein_mitglied_bis'] = $this->app->Secure->GetPOST('verein_mitglied_bis');
  $data['verein_spendenbescheinigung'] = $this->app->Secure->GetPOST('verein_spendenbescheinigung');
  $data['verein_mitglied_aktiv'] = $this->app->Secure->GetPOST('verein_mitglied_aktiv');

  if($data['verein_spendenbescheinigung']!='1') {
    $data['verein_spendenbescheinigung']=0;
  }
  if($data['verein_mitglied_aktiv']!='1') {
    $data['verein_mitglied_aktiv']=0;
  }

  $data['verein_mitglied_seit'] = $this->app->String->Convert($data['verein_mitglied_seit'],"%1.%2.%3","%3-%2-%1");
  $data['verein_mitglied_bis'] = $this->app->String->Convert($data['verein_mitglied_bis'],"%1.%2.%3","%3-%2-%1");

  $this->app->YUI->DatePicker('verein_mitglied_seit');
  $this->app->YUI->DatePicker('verein_mitglied_bis');

  if($submit!='')
  {
    $this->app->DB->Update("UPDATE adresse SET 
      verein_mitglied_seit='".$data['verein_mitglied_seit']."',
      verein_mitglied_bis='".$data['verein_mitglied_bis']."',
      verein_spendenbescheinigung='".$data['verein_spendenbescheinigung']."',
      verein_mitglied_aktiv='".$data['verein_mitglied_aktiv']."' 
      WHERE id='$id'");
  } 


  $data = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='$id'");

  if($data['verein_spendenbescheinigung']=='1') {
    $this->app->Tpl->Set('VEREIN_SPENDENBESCHEINIGUNG','checked');
  }
  if($data['verein_mitglied_aktiv']=='1') {
    $this->app->Tpl->Set('VEREIN_MITGLIED_AKTIV','checked');
  }
  $this->app->Tpl->Set('VEREIN_MITGLIED_SEIT',str_replace('..','',$this->app->String->Convert($data['verein_mitglied_seit'],"%3-%2-%1","%1.%2.%3")));
  $this->app->Tpl->Set('VEREIN_MITGLIED_BIS',str_replace('..','',$this->app->String->Convert($data['verein_mitglied_bis'],"%3-%2-%1","%1.%2.%3")));

  $this->app->Tpl->Parse('PAGE','adresse_verein.tpl');
}

  function AdresseMinidetailLieferadressen(){
    $id = $this->app->Secure->GetGET('id');

    $interne_bemerkung = $this->app->DB->Select("SELECT interne_bemerkung FROM lieferadressen WHERE id = '$id' AND interne_bemerkung != '' LIMIT 1");
    $hinweis = $this->app->DB->Select("SELECT hinweis FROM lieferadressen WHERE id = '$id' AND interne_bemerkung != '' LIMIT 1");

    $this->app->Tpl->Set('TABELLE', "<table width='100%'><tr><td style='background-color:#e0e0e0'><b>Interne Bemerkung</b></td></tr>");

    if($interne_bemerkung != ''){
        $this->app->Tpl->Add('TABELLE',"<tr><td>".$interne_bemerkung."</td></tr>");
    }else{
      $this->app->Tpl->Add('TABELLE', "<tr><td><div class=\"info\">Keine Daten vorhanden!</div></td></tr>");
    }

    $this->app->Tpl->Add('TABELLE', "<table width='100%'><tr><td style='background-color:#e0e0e0'><b>Lieferhinweis</b></td></tr>");
    if($hinweis != ''){
        $this->app->Tpl->Add('TABELLE',"<tr><td>".$hinweis."</td></tr>");
    }else{
      $this->app->Tpl->Add('TABELLE', "<tr><td><div class=\"info\">Keine Daten vorhanden!</div></td></tr>");
    }

    $this->app->Tpl->Add('TABELLE', '</table>');

    $this->app->Tpl->Output('adresse_lieferadressen_minidetail.tpl');
    $this->app->ExitXentral();
  }

  function AdresseMinidetailAnsprechpartner(){
    $id = $this->app->Secure->GetGET('id');

    $interne_bemerkung = $this->app->DB->Select("SELECT interne_bemerkung FROM ansprechpartner WHERE id = '$id' AND interne_bemerkung != '' LIMIT 1");
    if($interne_bemerkung != ""){
      $this->app->Tpl->Set("TABELLE", "<table width='100%'><tr><td style='background-color:#e0e0e0'><b>Interne Bemerkung</b></td></tr><tr><td>".$interne_bemerkung."</td></tr></table>");
    }else{
      $this->app->Tpl->Set("TABELLE", "<div class=\"info\">Keine Daten vorhanden!</div>");
    }

    $this->app->Tpl->Output("adresse_lieferadressen_minidetail.tpl");
    $this->app->ExitXentral();
  }


  function down($a,$x) {
    if( count($a)-1 > $x ) {
      $b = array_slice($a,0,$x,true);
      $b[] = $a[$x+1];
      $b[] = $a[$x];
      $b += array_slice($a,$x+2,count($a),true);
      return($b);
    }
    return $a;
  }

  function up($a,$x) {
    if( $x > 0 && $x < count($a) ) {
      $b = array_slice($a,0,($x-1),true);
      $b[] = $a[$x];
      $b[] = $a[$x-1];
      $b += array_slice($a,($x+1),count($a),true);
      return($b);
    }
    return $a;
  }

  public function AdresseAnschriftString($id,$ansprechpartner=false)
  {
    $type = 'adresse';
    if($ansprechpartner){
      $type = 'ansprechpartner';
    }

    $vorlage = $this->app->erp->Firmendaten('reihenfolge_zwischenspeicher');
    $tmp='';
    if($vorlage!='')
    {
      if($type==='ansprechpartner')
      {
        $data = $this->app->DB->SelectArr(
          sprintf(
          'SELECT a.* ,t.bezeichnung as typbezeichnung 
            FROM adresse a 
            LEFT JOIN adresse_typ t on t.type=a.typ 
            WHERE a.id=%d 
            LIMIT 1',
          (int)$id
          )
        );
        $data[0]['name']  = $this->app->DB->Select(
          sprintf(
          "SELECT name FROM adresse WHERE id=%d LIMIT 1",
            (int)$data[0]['adresse']
          )
        );
      } else {
        $data = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$id' LIMIT 1");
      }
      if(!empty($data))
      {
        foreach($data[0] as $feld=>$value)
        {
          $vorlage = str_replace('{ADRESSE_'.strtoupper($feld).'}',$value,$vorlage);
        }
        $vorlage = preg_replace('/^\h*\v+/m', '', $vorlage);
        $vorlage = str_replace("\r\n", "\\n", $vorlage);
        $tmp = $vorlage;
      }
    } else {
      if($id > 0)
      {
        if($type==='ansprechpartner')
        {
          $result = $this->app->DB->SelectArr(
            sprintf(
              'SELECT *,name as ansprechpartner,ansprechpartner_land as land 
            FROM ansprechpartner WHERE id=%d LIMIT 1',
              (int)$id
            )
          );
          $result[0]['name']  = $this->app->DB->Select(
            sprintf(
            'SELECT name FROM adresse WHERE id=%d LIMIT 1',
              $result[0]['adresse']
            )
          );
        } else {
          $result = $this->app->DB->SelectArr(
            sprintf('SELECT * FROM adresse WHERE id=%d LIMIT 1',
              $id
            )
          );
        }

        if($result[0]['name']!="") $tmp .= $result[0]['name']."\\n";
        if($result[0]['ansprechpartner']!="") $tmp .= $result[0]['ansprechpartner']."\\n";
        if($result[0]['abteilung']!="") $tmp .= $result[0]['abteilung']."\\n";
        if($result[0]['unterabteilung']!="") $tmp .= $result[0]['unterabteilung']."\\n";
        if($result[0]['adresszusatz']!="") $tmp .= $result[0]['adresszusatz']."\\n";
        if($result[0]['strasse']!="") $tmp .= $result[0]['strasse']."\\n";
        $tmp .= $result[0]['land']."-".$result[0]['plz']." ".$result[0]['ort'];
      }
    }
    return $this->app->erp->ReadyForPDF($tmp);
  }

  /**
   * @param int $id
   *
   * @return int
   */
  public function CopyAdresse($id){
    $this->app->DB->MysqlCopyRow('adresse','id',$id);
    $idNew = $this->app->DB->GetInsertID();
    //$roles = $this->app->DB->SelectArr("SELECT * FROM adresse_rolle WHERE adresse = '$id'");
    $this->app->DB->Insert("INSERT INTO adresse_rolle (adresse, projekt, subjekt, praedikat, objekt, parameter, von, bis) 
                            SELECT $idNew, projekt, subjekt, praedikat, objekt, parameter, von, bis FROM adresse_rolle WHERE adresse = '$id'");

    return $idNew;
  }

  /**
   * @param int $adresseId  AdressID
   * @param int $maxResults SQL-Limit; Maximale Anzahl der Ergebnisse
   *
   * @return string
   */
  protected function GetAddressCrmSql($adresseId, $maxResults = 20)
  {
    $adresseId = (int)$adresseId;
    $limit = (int)$maxResults;

    $sql = '
      SELECT
        DATE_FORMAT(a.datum, "%d.%m.%Y %H:%i") AS datum,
        if(ifnull(a.internebezeichnung,\'\') = \'\', a.title, concat(a.title,\'<br /><i style="color:grey">\',a.internebezeichnung,\'</i>\')) AS bezeichnung,
        a.ansprechpartner,
        a.abkuerzung AS projekt,
        a.bearbeiter,
        a.art,
        a.gesendet,
        a.pdf
      FROM 
      (
        (
          SELECT
            d.id,
            CONCAT(DATE_FORMAT(d.datum, "%Y-%m-%d"), " ", IF(d.uhrzeit IS NULL OR DATE_FORMAT(d.uhrzeit, "%H:%i")="00:00", "", DATE_FORMAT(d.uhrzeit, "%H:%i")) ) as datum,
            d.betreff as title,if(d.typ = \'email\',if(d.ansprechpartner <> \'\',d.ansprechpartner,d.email_an),d.ansprechpartner) as ansprechpartner,
            p.abkuerzung as abkuerzung,
            if(bearbeiter!="",bearbeiter,a2.name) as bearbeiter,
            CONCAT(UCASE(LEFT(d.typ, 1)), SUBSTRING(d.typ, 2)) as art,
            CONCAT(IF(d.sent = 1, "JA", "NEIN"),"<a data-type=dokumente data-id=", d.id, "></a>") as gesendet,
            "" as pdf,
            concat("1","-",d.id) as did,
            d.content as suchtext,
            d.internebezeichnung
          FROM
            dokumente d
          LEFT JOIN projekt p ON p.id=d.projekt
          LEFT JOIN adresse a2 ON a2.id=adresse_from
          WHERE
            adresse_to = ' . $adresseId . '
        )

        UNION ALL
        (
          SELECT
            ds.id,
            CONCAT(DATE_FORMAT(ds.zeit, "%Y-%m-%d")," ", IF(DATE_FORMAT(ds.zeit, "%H:%i")="00:00", "", DATE_FORMAT(ds.zeit, "%H:%i"))) as datum,
            ds.betreff  as title,ds.ansprechpartner ,
            p.abkuerzung as abkuerzung,
            ds.bearbeiter  as bearbeiter,
            CONCAT(UCASE(LEFT(ds.dokument, 1)), SUBSTRING(ds.dokument, 2),"<span style=\"display:none;\" class=\"editlink\">index.php?module=",ds.dokument,"&action=edit&id=",ds.parameter,"</span>") as art,
            CONCAT(IF(ds.versendet = 1, "JA", "NEIN"),"<a data-type=dokumente_send data-id=", ds.id, "></a>") as gesendet,
            concat("<a href=\"index.php?module=",ds.dokument,"&action=pdf&id=",ds.parameter,"\"><img src=./themes/' . $this->app->Conf->WFconf['defaulttheme'] . '/images/pdf.svg></a>") as pdf,
            concat("2","-",ds.id) as did,
            ds.text as suchtext,
            ifnull(d1.internebezeichnung,
            ifnull(d2.internebezeichnung,
            ifnull(d3.internebezeichnung,
            ifnull(d4.internebezeichnung,
            ifnull(d5.internebezeichnung,
            ifnull(d6.internebezeichnung,
             \'\'))))))  as internebezeichnung
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
            ds.adresse = ' . $adresseId . '
        )
        
        UNION ALL
        (
          SELECT
            k.id,
            CONCAT(DATE_FORMAT(k.von, "%Y-%m-%d")," ", IF(DATE_FORMAT(k.von, "%H:%i")="00:00", "", DATE_FORMAT(k.von, "%H:%i"))) as datum,
            k.bezeichnung COLLATE utf8_general_ci as title,\'\' as ansprechpartner,
            p.abkuerzung as abkuerzung,
            a2.name COLLATE utf8_general_ci as bearbeiter,
            "Kalender" as art,
            CONCAT("<a data-type=kalender data-id=", k.id, "></a>") as gesendet,
            "" as pdf,
            concat("6","-",k.id) as did,
            k.beschreibung COLLATE utf8_general_ci as suchtext,
            \'\' as internebezeichnung
          FROM
            kalender_event k 
            LEFT JOIN adresse a2 ON k.adresseintern = a2.id
            LEFT JOIN projekt p ON p.id=k.projekt
          WHERE
            k.adresse = ' . $adresseId . '
        )
        ';



    if($this->app->erp->RechteVorhanden('wiedervorlage', 'list')){
      if($this->app->erp->GetKonfiguration('adresse_crm_collateerror') && method_exists($this->app->YUI, 'ConvertLatin1UTF')){
        $sql .= '
          UNION ALL
          (
            SELECT
              w.id,
              CONCAT(DATE_FORMAT(datum_erinnerung, "%Y-%m-%d"), " ", IF(zeit_erinnerung IS NULL OR DATE_FORMAT(zeit_erinnerung, "%H:%i")="00:00", "", DATE_FORMAT(zeit_erinnerung, "%H:%i")) ) as datum,
              ' . $this->app->YUI->ConvertLatin1UTF('w.bezeichnung') . '   as title,\'\' as ansprechpartner,
              ' . $this->app->YUI->ConvertLatin1UTF('p.abkuerzung') . '   as abkuerzung,
              adr.name COLLATE utf8_general_ci as bearbeiter,
              CONCAT("Wiedervorlage") as art,
              CONCAT("<a data-type=wiedervorlage data-id=", w.id, "></a>") as gesendet,
              "" as pdf,
              concat("5","-",w.id) as did,
              ' . $this->app->YUI->ConvertLatin1UTF('w.beschreibung') . '  as suchtext,
              \'\' as internebezeichnung
            FROM
              wiedervorlage w left join adresse adr on w.bearbeiter = adr.id
              LEFT JOIN projekt p ON p.id=w.projekt
            WHERE
              w.adresse = ' . $adresseId . '
          )';
      }else{
        $sql .= '
          UNION ALL
          (
            SELECT
              w.id,
              CONCAT(DATE_FORMAT(datum_erinnerung, "%Y-%m-%d"), " ", IF(zeit_erinnerung IS NULL OR DATE_FORMAT(zeit_erinnerung, "%H:%i")="00:00", "", DATE_FORMAT(zeit_erinnerung, "%H:%i")) ) as datum,
              w.bezeichnung  COLLATE utf8_general_ci as title,\'\' as ansprechpartner,
              p.abkuerzung  COLLATE utf8_general_ci as abkuerzung,
              adr.name COLLATE utf8_general_ci as bearbeiter,
              CONCAT("Wiedervorlage") as art,
              CONCAT("<a data-type=wiedervorlage data-id=", w.id, "></a>") as gesendet,
              "" as pdf,
              concat("5","-",w.id) as did,
              w.beschreibung COLLATE utf8_general_ci as suchtext,
              \'\' as internebezeichnung
            FROM
              wiedervorlage w left join adresse adr on w.bearbeiter = adr.id
              LEFT JOIN projekt p ON p.id=w.projekt
            WHERE
              w.adresse = ' . $adresseId . '
          )';
      }
    }
    $sql .= ') AS a ';
    $sql .= 'ORDER BY a.datum DESC ';
    $sql .= 'LIMIT 0, ' . $maxResults;

    return $sql;
  }
}

