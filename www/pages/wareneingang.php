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
use Xentral\Components\Http\JsonResponse;

class Wareneingang
{
  /** @var Application $app */
  public $app;
  /** @var string */
  const MODULE_NAME = 'GoodsReceipt';

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public function TableSearch($app, $name, $erlaubtevars)
  {
    $id = $this->app->Secure->GetGET('id');
    switch($name) {
      case 'wareneingangartikelmanuellerfassen':
        $allowed['wareneingang'] = array('manuellerfassen');
        $this->app->Tpl->Add('JQUERYREADY', "$('#eigene').click( function() { fnFilterColumn1( 0 ); } );");
        for ($r = 1;$r < 2;$r++) {
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



        // headings
        $alignright = array(3);
        $heading = array('Nummer', 'Artikel', 'Lagerbestand', 'Projekt', 'Men&uuml;');
        $width = array( '10%', '60%', '5%', '15%', '1%');
        $findcols = array( 'nummer', 'name_de', 'ifnull((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0 )', 'projekt', 'id');

        if ($this->app->erp->Firmendaten("artikel_suche_kurztext") == "1") {
          $searchsql = array('a.name_de', 'kurztext_de', 'a.nummer', 'p.abkuerzung', "a.hersteller", "a.herstellernummer", "a.anabregs_text", "(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1)");
        } else {
          $searchsql = array('a.name_de', 'a.nummer', 'p.abkuerzung', "a.hersteller", "a.herstellernummer", "(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1)");
        }
        $searchsql[] = 'a.ean';
        $searchsql[] = 'a.herstellernummer';
        $paket = $this->app->Secure->GetGET("id");
        $menu = "<a href=\"index.php?module=wareneingang&action=distrietiketten&id=$paket&pos=%value%&menge=1&cmd=manuell\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>";

        // SQL statement


        $more_data1 = $this->app->Secure->GetGET("more_data1");

        if($more_data1=="1")
        {
          $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");
          $subwhere = " AND (a.adresse=$adresse OR ( (e.gueltig_bis >= NOW() OR e.gueltig_bis='0000-00-00' OR e.gueltig_bis IS NULL) AND e.adresse=$adresse) )";

          $addjoin = " RIGHT JOIN einkaufspreise e ON e.adresse=a.adresse ";

          $groupby = " GROUP BY a.id ";

        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, 
                CONCAT('<span style=display:none>',a.name_de,'</span>',if(a.intern_gesperrt,CONCAT('<strike>',

                if(a.variante AND a.variante_von > 0,CONCAT(name_de,' <font color=#848484>(Variante von ',ifnull((SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),''),')</font>'),name_de)

                  ,'</strike>'),

                    if(a.variante AND a.variante_von > 0,CONCAT(name_de,' <font color=#848484>(Variante von ',ifnull((SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),''),')</font>'),name_de)

                      )) as name_de, 
                CONCAT('<span style=display:none>',a.name_de,'</span>',trim((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id))+0) as lagerbestand,  
                  p.abkuerzung as projekt, a.id as menu 
                  FROM  artikel a 
                  LEFT JOIN projekt p ON p.id=a.projekt ".$addjoin;

        $where = "a.geloescht=0 $subwhere " . $this->app->erp->ProjektRechte();

        $moreinfo = false;
        $count = "SELECT COUNT(a.id) FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt $addjoin WHERE a.geloescht=0 $subwhere " . $this->app->erp->ProjektRechte();
        break;
      case 'wareneingang_lieferant':
        $allowed['wareneingang'] = array('distriinhalt');

        // headings
        $id = $this->app->Secure->GetGET('id');
        $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");
        $wareneingangauftragzubestellung = $this->app->erp->Firmendaten('wareneingangauftragzubestellung');
        if($wareneingangauftragzubestellung)
        {
          $heading = array('Bestellnummer', 'Nummer', 'Bestellung', 'Beschreibung', 'Lieferdatum', 'Projekt', 'Menge', 'Geliefert', 'Offen', 'Auftrag', 'Menge', 'Aktion');
          $width = array('5%', '5%', '5%', '30%', '5%', '5%', '5%', '5%', '5%', '5%', '5%', '5%');
          $findcols = array('bp.bestellnummer', 'art.nummer', 'b.belegnr',
            "CONCAT(art.name_de,'<br>Bei Lieferant: ',bp.bezeichnunglieferant, 
                                   IF(b.internebemerkung != '' AND b.internebemerkung IS NOT NULL, CONCAT('<br>Interne Bemerkung: ',b.internebemerkung),''),
                                   IF(b.internebezeichnung != '' AND b.internebezeichnung IS NOT NULL, CONCAT('<br>Interne Bezeichnung: ',b.internebezeichnung), ''),
                                   IF(b.bestellungbestaetigtabnummer != '' AND b.bestellungbestaetigtabnummer IS NOT NULL, CONCAT('<br>AB Nummer Lieferant: ',b.bestellungbestaetigtabnummer), ''),
                                   IF(b.bestaetigteslieferdatum != '' AND b.bestaetigteslieferdatum IS NOT NULL AND b.bestaetigteslieferdatum != '0000-00-00', CONCAT('<br>Best. Lieferdatum: ',DATE_FORMAT(b.bestaetigteslieferdatum, '%d.%m.%Y')),'')
                                   )",
            "if(bp.lieferdatum,bp.lieferdatum,'sofort')", 'p.abkuerzung', 'bp.menge', 'bp.geliefert', 'offen','auftrag','auftragmenge', 'bp.id');
          $searchsql = array('bp.bestellnummer', 'art.nummer', 'art.ean', 'b.belegnr', "CONCAT(art.name_de,'<br>Bei Lieferant: ',bp.bezeichnunglieferant, '<i style=color:#999>',
                                   IF(b.internebemerkung != '' AND b.internebemerkung IS NOT NULL, CONCAT('<br>Interne Bemerkung: ',b.internebemerkung),''),
                                   IF(b.internebezeichnung != '' AND b.internebezeichnung IS NOT NULL, CONCAT('<br>Interne Bezeichnung: ',b.internebezeichnung), ''),
                                   IF(b.bestellungbestaetigtabnummer != '' AND b.bestellungbestaetigtabnummer IS NOT NULL, CONCAT('<br>AB Nummer Lieferant: ',b.bestellungbestaetigtabnummer), ''),
                                   IF(b.bestaetigteslieferdatum != '' AND b.bestaetigteslieferdatum IS NOT NULL AND b.bestaetigteslieferdatum != '0000-00-00', CONCAT('<br>Best. Lieferdatum: ',DATE_FORMAT(b.bestaetigteslieferdatum, '%d.%m.%Y')),'')
                                   )", "if(bp.lieferdatum,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort')", 'p.abkuerzung', 'bp.menge', 'bp.geliefert',"if((SELECT COUNT(auf2.id) FROM auftrag auf2 INNER JOIN auftrag_position ap2 ON auf2.id = ap2.auftrag WHERE bp.auftrag_position_id = ap2.id ) > 0,(SELECT auf2.belegnr FROM auftrag auf2 INNER JOIN auftrag_position ap2 ON auf2.id = ap2.auftrag WHERE bp.auftrag_position_id = ap2.id ORDER BY belegnr LIMIT 1),'-' )");
        }else{
          $heading = array('Bestellnummer', 'Nummer', 'Bestellung', 'Beschreibung', 'Lieferdatum', 'Projekt', 'Menge', 'Geliefert', 'Offen', 'Aktion');
          $width = array('5%', '5%', '5%', '30%', '5%', '5%', '5%', '5%', '5%', '5%');
          $findcols = array('bp.bestellnummer', 'art.nummer', 'b.belegnr',
            "CONCAT(art.name_de,'<br>Bei Lieferant: ',bp.bezeichnunglieferant, 
                                   IF(b.internebemerkung != '' AND b.internebemerkung IS NOT NULL, CONCAT('<br>Interne Bemerkung: ',b.internebemerkung),''),
                                   IF(b.internebezeichnung != '' AND b.internebezeichnung IS NOT NULL, CONCAT('<br>Interne Bezeichnung: ',b.internebezeichnung), ''),
                                   IF(b.bestellungbestaetigtabnummer != '' AND b.bestellungbestaetigtabnummer IS NOT NULL, CONCAT('<br>AB Nummer Lieferant: ',b.bestellungbestaetigtabnummer), ''),
                                   IF(b.bestaetigteslieferdatum != '' AND b.bestaetigteslieferdatum IS NOT NULL AND b.bestaetigteslieferdatum != '0000-00-00', CONCAT('<br>Best. Lieferdatum: ',DATE_FORMAT(b.bestaetigteslieferdatum, '%d.%m.%Y')),'')
                                   )",
            "if(bp.lieferdatum,bp.lieferdatum,'sofort')", 'p.abkuerzung', 'bp.menge', 'bp.geliefert', $this->app->erp->FormatMenge("bp.menge -  bp.geliefert"), 'bp.id');
          $searchsql = array('bp.bestellnummer', 'art.nummer', 'b.belegnr', "CONCAT(art.name_de,'<br>Bei Lieferant: ',bp.bezeichnunglieferant, '<i style=color:#999>',
                                   IF(b.internebemerkung != '' AND b.internebemerkung IS NOT NULL, CONCAT('<br>Interne Bemerkung: ',b.internebemerkung),''),
                                   IF(b.internebezeichnung != '' AND b.internebezeichnung IS NOT NULL, CONCAT('<br>Interne Bezeichnung: ',b.internebezeichnung), ''),
                                   IF(b.bestellungbestaetigtabnummer != '' AND b.bestellungbestaetigtabnummer IS NOT NULL, CONCAT('<br>AB Nummer Lieferant: ',b.bestellungbestaetigtabnummer), ''),
                                   IF(b.bestaetigteslieferdatum != '' AND b.bestaetigteslieferdatum IS NOT NULL AND b.bestaetigteslieferdatum != '0000-00-00', CONCAT('<br>Best. Lieferdatum: ',DATE_FORMAT(b.bestaetigteslieferdatum, '%d.%m.%Y')),'')
                                   ,'</i>')", "if(bp.lieferdatum,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort')", 'p.abkuerzung', 'bp.menge', 'bp.geliefert', $this->app->erp->FormatMenge("bp.menge -  bp.geliefert"), 'art.ean', 'art.herstellernummer');
        }

        $alignright = array(7,8,9);
        $datecols = array(4);

        //$defaultorder = 6;  //Optional wenn andere Reihenfolge gewuenscht

        //$defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><form style=\"padding: 0px; margin: 0px;\" action=\"\" method=\"post\" name=\"eprooform\">Menge:&nbsp;<input type=\"text\" size=\"5\" name=\"pos[%value%]\">&nbsp;<input type=\"submit\" value=\"zuordnen\" name=\"submit\"></form></td></tr></table>";

        //&NBSP;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 4;

        if($this->app->erp->Firmendaten("wareneingang_lagerartikel")) $lagerartikel = "AND art.lagerartikel = 1";
        else $lagerartikel = "";
        $receiptDocument = $this->app->erp->ModulVorhanden('receiptdocument');
        if($receiptDocument) {
          $this->app->DB->Select('SELECT id FROM receiptdocument LIMIT 1');
          if($this->app->DB->error()) {
            $receiptDocument = false;
          }
        }
        $rdJoin = '';
        $colBeschreibung = "CONCAT(art.name_de,'<br>Bei Lieferant: ',bp.bezeichnunglieferant, '<i style=color:#999>',
                                   IF(b.internebemerkung != '' AND b.internebemerkung IS NOT NULL, CONCAT('<br>Interne Bemerkung: ',b.internebemerkung),''),
                                   IF(b.internebezeichnung != '' AND b.internebezeichnung IS NOT NULL, CONCAT('<br>Interne Bezeichnung: ',b.internebezeichnung), ''),
                                   IF(b.bestellungbestaetigtabnummer != '' AND b.bestellungbestaetigtabnummer IS NOT NULL, CONCAT('<br>AB Nummer Lieferant: ',b.bestellungbestaetigtabnummer), ''),
                                   IF(b.bestaetigteslieferdatum != '' AND b.bestaetigteslieferdatum IS NOT NULL AND b.bestaetigteslieferdatum != '0000-00-00', CONCAT('<br>Best. Lieferdatum: ',DATE_FORMAT(b.bestaetigteslieferdatum, '%d.%m.%Y')),'')
                                   ,'</i>'
                                   )";
        if(!empty($receiptDocument)){
          $rdJoin = "  LEFT JOIN (
                  SELECT supplier_order_id, MAX(useredit_id) AS useredit, MAX(useredit_time) AS  useredit_time
                  FROM receiptdocument 
                  WHERE (status = 'angelegt' OR status = '') AND supplier_order_id > 0 
                  AND useredit_time <> '0000-00-00 00:00:00' AND DATE_SUB(NOW(), INTERVAL 600 SECOND) < useredit_time
                  GROUP BY supplier_order_id
              ) AS rd ON b.id = rd.supplier_order_id ";
          $colBeschreibung = "CONCAT(art.name_de,'<br>Bei Lieferant: ',bp.bezeichnunglieferant, '<i style=color:#999>',
                                   IF(b.internebemerkung != '' AND b.internebemerkung IS NOT NULL, CONCAT('<br>Interne Bemerkung: ',b.internebemerkung),''),
                                   IF(b.internebezeichnung != '' AND b.internebezeichnung IS NOT NULL, CONCAT('<br>Interne Bezeichnung: ',b.internebezeichnung), ''),
                                   IF(b.bestellungbestaetigtabnummer != '' AND b.bestellungbestaetigtabnummer IS NOT NULL, CONCAT('<br>AB Nummer Lieferant: ',b.bestellungbestaetigtabnummer), ''),
                                   IF(b.bestaetigteslieferdatum != '' AND b.bestaetigteslieferdatum IS NOT NULL AND b.bestaetigteslieferdatum != '0000-00-00', CONCAT('<br>Best. Lieferdatum: ',DATE_FORMAT(b.bestaetigteslieferdatum, '%d.%m.%Y')),'')
                                   ,'</i>',
                                   IF(
                IFNULL(rd.useredit,0) <= 0,'',
                CONCAT('<br><font color=red><b>(in Bearbeitung von ', (SELECT a2.name FROM user u2 LEFT JOIN adresse a2 ON a2.id=u2.adresse WHERE u2.id=rd.useredit LIMIT 1),')</b></font>')  
              )
                                   )";
        }
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS bp.id, bp.bestellnummer, art.nummer, b.belegnr as `Bestellung`, 
                $colBeschreibung as beschreibung,
                if(bp.lieferdatum,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, p.abkuerzung as projekt, 
                ".$this->app->erp->FormatMenge('bp.menge').", ".$this->app->erp->FormatMenge('bp.geliefert').", 
                ".$this->app->erp->FormatMenge('bp.menge -  bp.geliefert')." as offen, 
                bp.id 
                FROM bestellung_position bp
                INNER JOIN bestellung b ON bp.bestellung=b.id
                $rdJoin
                INNER JOIN artikel art ON art.id=bp.artikel $lagerartikel 
                LEFT JOIN projekt p ON b.projekt=p.id ";
        if($wareneingangauftragzubestellung)
        {
          $sql = "SELECT SQL_CALC_FOUND_ROWS bp.id, bp.bestellnummer, art.nummer, b.belegnr as `Bestellung`, 
                  $colBeschreibung as beschreibung,                                      
                  if(bp.lieferdatum,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, p.abkuerzung as projekt, 
                  trim(bp.menge)+0, trim(bp.geliefert)+0, trim(bp.menge -  bp.geliefert)+0 as offen, 
                  if((SELECT COUNT(auf2.id) FROM auftrag auf2 INNER JOIN auftrag_position ap2 ON auf2.id = ap2.auftrag WHERE bp.auftrag_position_id = ap2.id ) > 0,(SELECT auf2.belegnr FROM auftrag auf2 INNER JOIN auftrag_position ap2 ON auf2.id = ap2.auftrag WHERE bp.auftrag_position_id = ap2.id ORDER BY belegnr LIMIT 1),'-' ) AS auftrag,
                  if((SELECT sum(ap2.menge) FROM auftrag auf2 INNER JOIN auftrag_position ap2 ON auf2.id = ap2.auftrag WHERE bp.auftrag_position_id = ap2.id ) > 0,(SELECT TRIM(sum(ap2.menge))+0 FROM auftrag auf2 INNER JOIN auftrag_position ap2 ON auf2.id = ap2.auftrag WHERE bp.auftrag_position_id = ap2.id ),'-' ) AS auftragmenge,
                  bp.id 
                  FROM bestellung_position bp
                  INNER JOIN bestellung b ON bp.bestellung=b.id 
                  $rdJoin
                  INNER JOIN artikel art ON art.id=bp.artikel $lagerartikel 
                  LEFT JOIN projekt p ON b.projekt=p.id ";

        }

        $where = " b.adresse='$adresse' AND b.belegnr != '' 
              AND bp.geliefert < bp.menge AND (bp.abgeschlossen IS NULL OR bp.abgeschlossen=0)  AND (b.status='versendet' OR b.status='freigegeben') " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "
              SELECT COUNT(bp.id) FROM bestellung_position bp INNER JOIN bestellung b ON bp.bestellung=b.id INNER JOIN artikel art ON art.id=bp.artikel $lagerartikel LEFT JOIN projekt p ON bp.projekt=p.id WHERE b.adresse='$adresse' AND b.belegnr !='' AND bp.geliefert < bp.menge AND (bp.abgeschlossen IS NULL OR bp.abgeschlossen=0) AND (b.status='versendet' OR b.status='freigegeben') " . $this->app->erp->ProjektRechte();
        $moreinfo = false;
        $this->app->erp->RunHook('warneingang_tablesearch_wareneingang_lieferant', 4, $id, $sql, $where, $count);
        break;
      case 'paketannahme_retoure':
        $allowed['wareneingang'] = array('distriinhalt');
        $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");
        $heading = array(
          'Nummer', 'Beschreibung', 'Retoure', 'Datum', 'Projekt', 'Menge',
          'Geliefert', 'Offen', 'Aktion'
        );
        $menucol = count($heading);
        $width = array('5%', '5%', '5%', '30%', '5%', '5%', '5%', '5%', '5%', '5%');
        $findcols = array('art.nummer','art.name_de',  'b.belegnr',
          "DATE_FORMAT(b.datum,'%d.%m.%Y')",
          'p.abkuerzung', 'bp.menge', 'bp.menge_eingang',
          $this->app->erp->FormatMenge('bp.menge -  bp.menge_eingang'), 'bp.id'
        );
        $searchsql = array('art.nummer','art.name_de',  'b.belegnr',
          "DATE_FORMAT(b.datum,'%d.%m.%Y')", 'p.abkuerzung', 'bp.menge',
          'bp.menge_eingang',
          $this->app->erp->FormatMenge('bp.menge -  bp.menge_eingang'),
          'art.ean', 'art.herstellernummer'
        );
        $this->app->DB->SelectRow(
          'SELECT `return_order_id`, `id`, `useredit_time`, `status` FROM `receiptdocument` LIMIT 1'
        );
        $count = "SELECT COUNT(bp.id) 
        FROM `retoure` as `b`
        INNER JOIN `retoure_position` as `bp` ON b.id = bp.retoure 
        LEFT JOIN `projekt` AS `p` ON b.projekt = p.id 
        ";
        if(empty($this->app->DB->error())){

          $sql = "SELECT SQL_CALC_FOUND_ROWS bp.id, art.nummer, 
                           CONCAT(art.name_de,
                               IF(
                IFNULL(rd.useredit,0) <= 0,'',
                CONCAT('<br><font color=red><b>(in Bearbeitung von ',     
                    (SELECT a2.name FROM user u2 LEFT JOIN adresse a2 ON a2.id=u2.adresse WHERE u2.id=rd.useredit LIMIT 1),')</b></font>')
              ) 
                               ), b.belegnr,
                           DATE_FORMAT(b.datum,'%d.%m.%Y'),
                           p.abkuerzung, 
              trim(bp.menge)+0 AS menge, trim(bp.menge_eingang)+0 AS eingang, 
                           trim(bp.menge -  bp.menge_eingang)+0 as offen,
                           bp.id
        FROM `retoure` AS `b`
        LEFT JOIN (
            SELECT `return_order_id`, MAX(`useredit_id`) AS `useredit`, MAX(`useredit_time`) AS  `useredit_time`
            FROM `receiptdocument` 
            WHERE (`status` = 'angelegt' OR `status` = '') AND `return_order_id` > 0 
            AND `useredit_time` <> '0000-00-00 00:00:00' AND DATE_SUB(NOW(), INTERVAL 600 SECOND) < `useredit_time`
            GROUP BY `return_order_id`
        ) AS `rd` ON b.id = rd.return_order_id
        LEFT JOIN (
            SELECT `return_order_id`
            FROM `receiptdocument` 
            WHERE (`status` = 'fertiggestellt' OR `status` = 'abgeschlossen') AND `return_order_id` > 0 
            GROUP BY `return_order_id`
        ) AS `rd2` ON b.id = rd2.return_order_id
        INNER JOIN retoure_position as bp ON b.id = bp.retoure
        INNER JOIN artikel AS art ON bp.artikel = art.id
        LEFT JOIN projekt AS p ON b.projekt = p.id
        ";

          $count .= "
          LEFT JOIN (
            SELECT `return_order_id`, MAX(`useredit_id`) AS `useredit`, MAX(`useredit_time`) AS  `useredit_time`
            FROM `receiptdocument` 
            WHERE (`status` = 'angelegt' OR `status` = '') AND `return_order_id` > 0 
            AND `useredit_time` <> '0000-00-00 00:00:00' AND DATE_SUB(NOW(), INTERVAL 600 SECOND) < `useredit_time`
            GROUP BY `return_order_id`
          ) AS `rd` ON b.id = rd.return_order_id
          LEFT JOIN (
              SELECT `return_order_id`
              FROM `receiptdocument` 
              WHERE (`status` = 'fertiggestellt' OR `status` = 'abgeschlossen') AND `return_order_id` > 0 
              GROUP BY `return_order_id`
          ) AS `rd2` ON b.id = rd2.return_order_id
          ";
        }
        else {
          $sql = "SELECT SQL_CALC_FOUND_ROWS bp.id, art.nummer, 
                           art.name_de
                              , b.belegnr,
                           DATE_FORMAT(b.datum,'%d.%m.%Y'),
                           p.abkuerzung, 
              trim(bp.menge)+0 AS menge, trim(bp.menge_eingang)+0 AS eingang, 
                           trim(bp.menge -  bp.menge_eingang)+0 as offen,
                           bp.id
        FROM retoure as b
        INNER JOIN retoure_position as bp ON b.id = bp.retoure
        INNER JOIN artikel AS art ON bp.artikel = art.id
        LEFT JOIN projekt AS p ON b.projekt = p.id
        ";
        }
        $where = sprintf(' b.adresse = %d ', $adresse).
          " AND bp.menge_eingang < bp.menge AND b.belegnr <> '' AND b.status <> 'storniert' ".
          $this->app->erp->ProjektRechte('b.projekt');


        $count .= " WHERE $where ";

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><form style=\"padding: 0px; margin: 0px;\" action=\"\" method=\"post\" name=\"eprooform\">Menge:&nbsp;<input type=\"text\" size=\"5\" name=\"retourepos[%value%]\">&nbsp;<input type=\"submit\" value=\"zuordnen\" name=\"submit\"></form></td></tr></table>";
        $this->app->erp->RunHook('wareneingang_tablesearch_wareneingang_retoure', 4, $id, $sql, $where, $count);
        break;
      case 'paketannahme':
        $allowed['wareneingang'] = array('paketannahme');
        $this->app->Tpl->Add('JQUERYREADY', "$('#nurkunden').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#nurlieferanten').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#kundenmitrma').click( function() { fnFilterColumn3( 0 ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#lieferungfehlt').click( function() { fnFilterColumn4( 0 ); } );");

        $this->app->Tpl->Add('JQUERYREADY', "$('#zeitvon').change( function() { fnFilterColumn5(  $('#zeitvon').val() ); } );$('#zeitvon').click( function() { fnFilterColumn5(  $('#zeitvon').val() ); } );");
        $this->app->Tpl->Add('JQUERYREADY', "$('#zeitbis').change( function() { fnFilterColumn6(  $('#zeitbis').val() ); } );$('#zeitbis').click( function() { fnFilterColumn6(  $('#zeitbis').val() ); } );");

        $forcerowclick=true;
        $rowclickaction="paketannahme&vorlage=adresse";
        $hide767 = [7, 8];
        for ($r = 1;$r < 5;$r++) {
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

        for ($r = 5;$r < 7;$r++) {
          $this->app->Tpl->Add('JAVASCRIPT', '
                  function fnFilterColumn' . $r . ' ( i )
                  {
                  oMoreData' . $r . $name . ' = i;

                  $(\'#' . $name . '\').dataTable().fnFilter( 
                    \'\',
                    i, 
                    0,0
                    );
                  }
                  ');
        }

        $more_data1 = $this->app->Secure->GetGET("more_data1");

        if ($more_data1 == 1) $subwhere[] = " a.kundennummer != '' ";
        $more_data2 = $this->app->Secure->GetGET("more_data2");

        if ($more_data2 == 1) $subwhere[] = " a.lieferantennummer != '' ";

        $more_data3 = $this->app->Secure->GetGET("more_data3");
        $tmpfrom2 = '';
        if($more_data3 == 1)
        {
          $subwhere2[] = " rma.status != 'storniert' ";
          $tmpfrom2 = ' INNER JOIN rma_artikel rma ON a.id = rma.adresse ';
        }

        $more_data4 = $this->app->Secure->GetGET("more_data4");
        $more_data5 = $this->app->Secure->GetGET("more_data5");
        $more_data6 = $this->app->Secure->GetGET("more_data6");
        $tmpfrom3 = '';
        if($more_data4 == 1)
        {
          $tmpfrom3 = ' INNER JOIN bestellung b ON a.id = b.adresse INNER JOIN bestellung_position bp ON b.id = bp.bestellung ';
          $subwhere2[] = " (bp.geliefert < bp.menge AND (b.status!='abgeschlossen' AND b.status!='angelegt' AND b.status!='storniert')) ";
        }
        $tmpfrom4 = '';
        $tmpfrom5 = '';
        $tmpwhere = '';
        $tmpwhere2 = '';
        $datumvon = '';
        $datumbis = '';
        if(!empty($more_data5) || !empty($more_data6))
        {

          if(!empty($more_data5))$datumvon = $this->app->String->Convert($more_data5,"%1.%2.%3","%3-%2-%1");
          if(!empty($more_data6))$datumbis = $this->app->String->Convert($more_data6,"%1.%2.%3","%3-%2-%1");
          if($more_data4 == 1)
          {

          }else{
            if($more_data2 == 1 || $more_data1 != 1)$tmpfrom4 = ' LEFT JOIN bestellung b on a.id = b.adresse ';

          }
          if($more_data2 == 1 || $more_data1 != 1)
          {
            if(!empty($more_data5))
            {
              $tmpwhere = "( date(b.versendet_am) >= '$datumvon' ";
            }
            if(!empty($more_data6))
            {
              if(!empty($tmpwhere))
              {
                $tmpwhere .= " AND date(b.versendet_am) <= '$datumbis' ";
              }else{
                $tmpwhere .= "(date(b.versendet_am) <= '$datumbis' ";
              }
            }
            if(!empty($tmpwhere))$tmpwhere .= ') ';
          }
          if($more_data1 == 1 || $more_data2 != 1)
          {
            $tmpfrom5 = ' LEFT JOIN lieferschein l on a.id = l.adresse ';
            if(!empty($more_data5))
            {
              $tmpwhere2 = " ((l.status = 'versendet' OR l.status = 'freigegeben') AND date(l.datum) >= '$datumvon' ";
            }
            if(!empty($more_data6))
            {
              if(!empty($tmpwhere2))
              {
                $tmpwhere2 .= " AND date(l.datum) <= '$datumbis' ";
              }else{
                $tmpwhere2 .= "((l.status = 'versendet' OR l.status = 'freigegeben') AND date(l.datum) <= '$datumbis' ";
              }
            }
            if(!empty($tmpwhere2))$tmpwhere2 .= ') ';
          }
          if(!empty($tmpwhere) || !empty($tmpwhere2))
          {
            if(!empty($tmpwhere) && !empty($tmpwhere2))
            {
              $subwhere2[] = " (".$tmpwhere." OR ".$tmpwhere2.") ";
            }else{
              if(!empty($tmpwhere))
              {
                $subwhere2[] = $tmpwhere;
              }else{
                $subwhere2[] = $tmpwhere2;
              }
            }

          }
        }
        $tmp = '';
        if(!empty($subwhere))
        {
          foreach($subwhere as $s) {
            $tmp.= " AND " . $s;
          }
        }
        $tmp2 = '';
        if(!empty($subwhere2))
        {
          foreach($subwhere2 as $s2) {
            $tmp2.= " AND " . $s2;
          }
        }



        // headings
        $heading = array('Name', 'Kunde', 'Lieferant', 'Land', 'PLZ', 'Ort', 'E-Mail', 'Projekt', 'Men&uuml;');
        $width = array('25%', '10%', '5%', '5%', '5%', '5%', '25%', '5%', '1%');
        $findcols = array('a.name', 'a.kundennummer', 'a.lieferantennummer', 'a.land', 'a.plz', 'a.ort', 'a.email', 'a.projekt', 'a.id');
        $searchsql = array('a.ort', 'a.name', 'p.abkuerzung', 'a.land', 'a.plz', 'a.email', 'a.kundennummer', 'a.lieferantennummer', 'a.ansprechpartner');
        if ($this->app->erp->Firmendaten('adresse_freitext1_suche')) {
          $searchsql[] = 'a.freifeld1';
          $searchsql[] = 'a.freifeld2';
        }

        $defaultorder = 2;
        $defaultorderdesc = 1;
        if($this->app->erp->RechteVorhanden("wareneingang","distrietiketten") && $this->app->erp->RechteVorhanden("wareneingang","distriabschluss")){
          $menu = "<a href=\"index.php?module=wareneingang&action=paketannahme&id=%value%&vorlage=adresse\"><img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>";
        }

        // SQL statement

        //if(a.typ = 'herr' OR a.typ = 'frau',CONCAT(a.vorname,' ',a.name),a.name) as name,
          if ($this->app->erp->Firmendaten("adresse_freitext1_suche")) {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT(a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')) as name,
                    if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                      if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
                        FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt $tmpfrom2 $tmpfrom3 $tmpfrom4 $tmpfrom5";
          } else {
            $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.name as name,
                    if(a.kundennummer!='',a.kundennummer,'-') as kundennummer,
                      if(a.lieferantennummer!='',a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
                        FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt $tmpfrom2 $tmpfrom3 $tmpfrom4 $tmpfrom5";
          }
        // fester filter
        $where = "a.geloescht=0 $tmp $tmp2 " . $this->app->erp->ProjektRechte();
        $groupby = " GROUP BY a.id ";
        $count = "SELECT COUNT(a.id) FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 $tmp " . $this->app->erp->ProjektRechte();
        break;
      case 'wareneingang_kunderetoure':
        $allowed['wareneingang'] = array('distriinhalt');

        // headings
        $id = $this->app->Secure->GetGET('id');
        $adresse = (int)$this->app->DB->Select(sprintf('SELECT adresse FROM paketannahme WHERE id = %d LIMIT 1', $id));
        $heading = array('Nummer', 'Beschreibung', 'SN/Charge/MHD', 'Projekt', 'Menge', 'Retoure', 'Lieferschein', 'Datum', 'Aktion','');
        $width = array('5%', '30%', '5%', '5%', '5%', '5%', '5%', '5%', '5%','1%');
        $findcols = array('lp.nummer', 'lp.bezeichnung', 'sn.seriennummer', 'p.abkuerzung', 'if(isnull(sn.menge), lp.menge,sn.menge)', 'rma.menge', 'l.belegnr', 'l.datum', 'lp.id','lp.id');
        $searchsql = array('lp.nummer', 'lp.bezeichnung', 'sn.seriennummer','lp.beschreibung', 'p.abkuerzung', 'trim(if(isnull(sn.menge), lp.menge,sn.menge))+0', 'lp.geliefert', 'l.belegnr', "DATE_FORMAT(l.datum,'%d.%m.%Y')", 'lp.id','lp.id');

        $alignright = array(5,6);

        //$defaultorder = 6;  //Optional wenn andere Reihenfolge gewuenscht

        //$defaultorderdesc=1;

        $mCol = "CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>Menge:&nbsp;"
          ."<input class=\"qty\" value=\"',IFNULL(
          (
            SELECT ".$this->app->erp->FormatMenge('ra.quantity')." 
            FROM returnorder_quantity AS ra 
            WHERE ra.delivery_note_id = lp.id 
              AND ra.serialnumber = IFNULL(sn.seriennummer,'')
              AND ra.bestbefore = IFNULL(bbf.mhd,'')
              AND ra.batch = IFNULL(batch.charge,'')
            LIMIT 1
          ),''
          ),'\" type=\"text\" size=\"5\" name=\"pos',lp.id,'\" data-dnpid=\"',lp.id,'\" data-bestbefore=\"',IFNULL(bbf.mhd,''),'\" data-batch=\"',IFNULL(batch.charge,''),'\" data-serialnumber=\"',IFNULL(sn.seriennummer,''),'\" />"
          ."</td></tr></table>')";

        $menu = '';

        //&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 8;

        $kommissionskonsignationslager = true;
        if(!$this->app->erp->ModulVorhanden('kommissionskonsignationslager')) {
          $kommissionskonsignationslager = false;
        }
        if($kommissionskonsignationslager
          && !$this->app->DB->Select("SELECT id FROM kommissionskonsignationslager_positionen LIMIT 1")) {
          $kommissionskonsignationslager = false;
        }


        // SQL statement

        $rmamengecol = "
        if( 
           ifnull(rma2.menge,0)+IFNULL(rma4.menge,0) > 0,
           concat(".$this->app->erp->FormatMenge('ifnull(rma2.menge,0)+ifnull(rma4.menge,0)')."),
           if(
              ifnull(rma.menge,0)+ifnull(rma3.menge,0) > 0,
              concat(".$this->app->erp->FormatMenge('ifnull(rma.menge,0)+ifnull(rma3.menge,0)').",
              if(isnull(sn.pos),'',' (Retoure gesamt)')
           )
           ,''
          )
        ),
        ";
        $rmajoin = "LEFT JOIN (
            SELECT sum(menge) as menge,pos 
            FROM rma_artikel WHERE seriennummer = '' 
            GROUP BY pos
          ) rma ON lp.id = rma.pos ";

        $rmajoin2 = "LEFT JOIN (
            SELECT sum(menge) as menge,pos,seriennummer 
            FROM rma_artikel WHERE seriennummer != '' 
            GROUP BY pos,seriennummer
          ) rma2 ON lp.id = rma2.pos AND sn.seriennummer = rma2.seriennummer";


        $rmajoin3 = "
            LEFT JOIN (
              SELECT SUM(menge) AS menge, lieferschein_position_id 
              FROM `retoure_position` 
              WHERE `lieferschein_position_id` > 0
              GROUP BY lieferschein_position_id
            ) AS rma3 ON lp.id = rma3.lieferschein_position_id 
            AND (sn.seriennummer IS NULL OR sn.seriennummer = '')
        ";

        $rmajoin4 = "
              LEFT JOIN (
                SELECT count(bcr.id) as menge, bcr.wert as seriennummer, 
                rop.lieferschein_position_id 
                FROM `beleg_chargesnmhd` AS bcr 
                INNER JOIN retoure_position AS rop ON bcr.pos = rop.id
                AND bcr.doctype = 'retoure' AND bcr.type='sn'
                GROUP BY rop.lieferschein_position_id, bcr.wert
              ) AS rma4 ON lp.id = rma4.lieferschein_position_id 
              AND sn.seriennummer = rma4.seriennummer
        ";


        if($kommissionskonsignationslager) {
          $kommname = $this->app->erp->Firmendaten('kommissionskonsignationslager');
          if($kommname === '') {
            $kommname = 'Kommissions-/Konsignationslager';
          }

          $sql = "SELECT SQL_CALC_FOUND_ROWS lp.id,lp.nummer, 
               concat(lp.bezeichnung, if(isnull(komm.menge),'',' <i>($kommname)</i>')) as beschreibung,
                           
          if(
            sn.seriennummer IS NULL,
            IF(bbf.mhd IS NULL,
                IF(batch.charge IS NULL,
                  '',
                  CONCAT('Charge: ',batch.charge)
                ),
                CONCAT('MHD: ', bbf.mhd)                  
            ),
            CONCAT('SN: ', sn.seriennummer)
          ),
                p.abkuerzung as projekt,
                           
                                trim(if(isnull(sn.menge), 
                    if(isnull(bbf.menge), 
                        IF(isnull(batch.menge),lp.menge,batch.menge)
                        , bbf.menge)
                    ,sn.menge))+0, 
                           
                           
                $rmamengecol
                l.belegnr as lieferschein, 
                 DATE_FORMAT(l.datum,'%d.%m.%Y') as datum, if(isnull(sn.seriennummer),lp.id,
                $mCol,
                     concat(lp.id,'-',sn.seriennummer)),
                           
                FROM lieferschein_position lp
                $rmajoin
                LEFT JOIN lieferschein l ON lp.lieferschein=l.id 
                LEFT JOIN projekt p ON l.projekt=p.id 
                LEFT JOIN artikel a ON a.id=lp.artikel 
                LEFT JOIN (
                    (
                      SELECT '1' as menge, wert as seriennummer, pos 
                      FROM `beleg_chargesnmhd` 
                      WHERE doctype = 'lieferschein' AND type='sn' 
                    ) UNION ALL
                    (
                        SELECT '1' as menge,seriennummer, lieferscheinpos as pos 
                        FROM seriennummern
                    )
                ) sn ON sn.pos = lp.id
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, 
                    IF(IFNULL(wert2,'') = '',wert ,CONCAT(wert,'-',IFNULL(wert2,''))) AS mhd,
                    pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='mhd'
                    GROUP BY wert,pos,wert2
                ) AS bbf ON bbf.pos = lp.id AND a.mindesthaltbarkeitsdatum = 1 AND sn.pos IS NULL
                   AND (a.seriennummern = 'keine' OR a.seriennummern = '')  
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, wert AS charge, pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='charge'
                    GROUP BY wert,pos
                ) AS batch ON batch.pos = lp.id AND a.mindesthaltbarkeitsdatum = 0 AND a.chargenverwaltung > 0 
                    AND (a.seriennummern = 'keine' OR a.seriennummern = '')  AND sn.pos IS NULL 
                LEFT JOIN 
                 (SELECT lieferschein_position, sum(menge) as menge FROM kommissionskonsignationslager_positionen WHERE menge > ausgelagert GROUP BY lieferschein_position) komm ON lp.id = komm.lieferschein_position
                 
                $rmajoin2                 
                $rmajoin3                 
                $rmajoin4                 
                ";
        }
        else{

          $sql = "SELECT SQL_CALC_FOUND_ROWS lp.id,lp.nummer, lp.bezeichnung as beschreibung, 
                           
          if(
            sn.seriennummer IS NULL,
            IF(bbf.mhd IS NULL,
                IF(batch.charge IS NULL,
                  '',
                  CONCAT('Charge: ',batch.charge)
                ),
                CONCAT('MHD: ', bbf.mhd)                  
            ),
            CONCAT('SN: ', sn.seriennummer)
          ),
                           
          p.abkuerzung as projekt,
                           
                                trim(if(isnull(sn.menge), 
                    if(isnull(bbf.menge), 
                        IF(isnull(batch.menge),lp.menge,batch.menge)
                        , bbf.menge)
                    ,sn.menge))+0, 
                $rmamengecol 
                l.belegnr as lieferschein, 
           DATE_FORMAT(l.datum,'%d.%m.%Y') as datum,
           $mCol,
           if(isnull(sn.seriennummer),lp.id,concat(lp.id,'-',sn.seriennummer)) 
                FROM lieferschein_position lp
                $rmajoin
                LEFT JOIN lieferschein l ON lp.lieferschein=l.id 
                LEFT JOIN projekt p ON l.projekt=p.id 
                LEFT JOIN artikel a ON a.id=lp.artikel 
                LEFT JOIN 
                (
                  (
                    SELECT '1' as menge, wert as seriennummer, pos 
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='sn' 
                  ) 
                  UNION ALL
                  (
                    SELECT '1' as menge,seriennummer, lieferscheinpos as pos 
                    FROM seriennummern
                  )
                ) sn ON sn.pos = lp.id
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, 
                    IF(IFNULL(wert2,'') = '',wert ,CONCAT(wert,'-',IFNULL(wert2,''))) AS mhd,
                    pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='mhd'
                    GROUP BY wert,pos,wert2
                ) AS bbf ON bbf.pos = lp.id AND a.mindesthaltbarkeitsdatum = 1 AND sn.pos IS NULL
                   AND (a.seriennummern = 'keine' OR a.seriennummern = '')  
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, wert AS charge, pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='charge'
                    GROUP BY wert,pos
                ) AS batch ON batch.pos = lp.id AND a.mindesthaltbarkeitsdatum = 0 AND a.chargenverwaltung > 0 
                    AND (a.seriennummern = 'keine' OR a.seriennummern = '')  AND sn.pos IS NULL 
                $rmajoin2
                $rmajoin3
                $rmajoin4
                ";
        }

        $where = " (l.adresse='$adresse' AND (l.status='versendet' OR l.status='freigegeben') AND a.lagerartikel=1) " .
          $this->app->erp->ProjektRechte();

        // gesamt anzahl
        /*$count = "SELECT COUNT(lp.id)
            FROM lieferschein_position lp
            LEFT JOIN lieferschein l ON lp.lieferschein=l.id 
            LEFT JOIN projekt p ON l.projekt=p.id 
            LEFT JOIN artikel a ON a.id=lp.artikel 
            WHERE ($where)";*/
        $count = '';
        $moreinfo = false;
        break;
      case 'wareneingang_kunde':
        $allowed['wareneingang'] = array('distriinhalt');

        // headings
        $id = $this->app->Secure->GetGET('id');
        $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");
        $heading = array('Nummer', 'Beschreibung', 'SN/Charge/MHD', 'Projekt', 'Menge', 'Retoure', 'Lieferschein', 'Datum', 'Aktion');
        $width = array('5%', '30%', '5%', '5%', '5%', '5%', '5%', '5%', '5%');
        $findcols = array('lp.nummer', 'lp.bezeichnung', 'sn.seriennummer', 'p.abkuerzung', 'if(isnull(sn.menge), lp.menge,sn.menge)', 'rma.menge', 'l.belegnr', 'l.datum', 'lp.id');
        $searchsql = array('lp.nummer', 'lp.bezeichnung', 'sn.seriennummer','lp.beschreibung', 'p.abkuerzung', 'trim(if(isnull(sn.menge), lp.menge,sn.menge))+0', 'lp.geliefert', 'l.belegnr', "DATE_FORMAT(l.datum,'%d.%m.%Y')", 'lp.id');

        $alignright = array(5,6);

        //$defaultorder = 6;  //Optional wenn andere Reihenfolge gewuenscht

        //$defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><form style=\"padding: 0px; margin: 0px;\" action=\"\" method=\"post\" name=\"eprooform\">Menge:&nbsp;<input type=\"text\" size=\"5\" name=\"pos[%value%]\">&nbsp;<input type=\"submit\" value=\"zuordnen\" name=\"submitkunde\"></form></td></tr></table>";

        //&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a></td></tr></table>";
        $menucol = 4;

        $kommissionskonsignationslager = true;
        if(!$this->app->erp->ModulVorhanden('kommissionskonsignationslager'))
        {
          $kommissionskonsignationslager = false;
        }
        if($kommissionskonsignationslager && !$this->app->DB->Select("SELECT id FROM kommissionskonsignationslager_positionen LIMIT 1"))
        {
          $kommissionskonsignationslager = false;
        }


        // SQL statement

        $rmamengecol = "
        if(ifnull(rma2.menge,0) > 0,
        concat(".$this->app->erp->FormatMenge('ifnull(rma2.menge,0)')."),
        if(ifnull(rma.menge,0) > 0,concat(".$this->app->erp->FormatMenge('ifnull(rma.menge,0)').",if(isnull(sn.pos),'',' (Retoure gesamt)')),'')
        ),
        ";
        $rmajoin = "LEFT JOIN (SELECT sum(menge) as menge,pos FROM rma_artikel WHERE seriennummer = '' GROUP BY pos) rma ON lp.id = rma.pos ";
        $rmajoin2 = "LEFT JOIN (SELECT sum(menge) as menge,pos,seriennummer FROM rma_artikel WHERE seriennummer != '' GROUP BY pos,seriennummer) rma2 ON lp.id = rma2.pos AND sn.seriennummer = rma2.seriennummer";
        if($kommissionskonsignationslager)
        {
          $kommname = $this->app->erp->Firmendaten('kommissionskonsignationslager');
          if($kommname === '')
          {
            $kommname = 'Kommissions-/Konsignationslager';
          }

          $sql = "SELECT SQL_CALC_FOUND_ROWS lp.id,lp.nummer, 
                           concat(lp.bezeichnung, if(isnull(komm.menge),'',' <i>($kommname)</i>')) as beschreibung, 
          if(
            sn.seriennummer IS NULL,
            IF(bbf.mhd IS NULL,
                IF(batch.charge IS NULL,
                  '',
                  CONCAT('Charge: ',batch.charge)
                ),
                CONCAT('MHD: ', bbf.mhd)                  
            ),
            CONCAT('SN: ', sn.seriennummer)
          ),
          p.abkuerzung as projekt,
                trim(if(isnull(sn.menge), 
                    if(isnull(bbf.menge), 
                        IF(isnull(batch.menge),lp.menge,batch.menge)
                        , bbf.menge)
                    ,sn.menge))+0, 
                $rmamengecol
                l.belegnr as lieferschein, DATE_FORMAT(l.datum,'%d.%m.%Y') as datum, 
                           if(
            sn.seriennummer IS NULL,
            IF(bbf.mhd IS NULL,
                IF(batch.charge IS NULL,
                  lp.id,
                  CONCAT(lp.id,'-',batch.charge)
                ),
                CONCAT(lp.id,'-', bbf.mhd)                  
            ),
            CONCAT(lp.id,'-', sn.seriennummer)
          ) 
                
                FROM lieferschein_position lp
                $rmajoin
                LEFT JOIN lieferschein l ON lp.lieferschein=l.id 
                LEFT JOIN projekt p ON l.projekt=p.id 
                LEFT JOIN artikel a ON a.id=lp.artikel 
                LEFT JOIN (
                (SELECT '1' as menge, wert as seriennummer, pos FROM `beleg_chargesnmhd` WHERE doctype = 'lieferschein' AND type='sn' ) UNION ALL
                (SELECT '1' as menge,seriennummer, lieferscheinpos as pos FROM seriennummern)
                ) sn ON sn.pos = lp.id
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, 
                    IF(IFNULL(wert2,'') = '',wert ,CONCAT(wert,'-',IFNULL(wert2,''))) AS mhd,
                    pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='mhd'
                    GROUP BY wert,pos,wert2
                ) AS bbf ON bbf.pos = lp.id AND a.mindesthaltbarkeitsdatum = 1 AND sn.pos IS NULL
                   AND (a.seriennummern = 'keine' OR a.seriennummern = '')  
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, wert AS charge, pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='charge'
                    GROUP BY wert,pos
                ) AS batch ON batch.pos = lp.id AND a.mindesthaltbarkeitsdatum = 0 AND a.chargenverwaltung > 0 
                    AND (a.seriennummern = 'keine' OR a.seriennummern = '')  AND sn.pos IS NULL 
                LEFT JOIN 
                 (SELECT lieferschein_position, sum(menge) as menge FROM kommissionskonsignationslager_positionen WHERE menge > ausgelagert GROUP BY lieferschein_position) komm ON lp.id = komm.lieferschein_position
                 
                $rmajoin2                 
                ";
        }else{

          $sql = "SELECT SQL_CALC_FOUND_ROWS lp.id,lp.nummer, lp.bezeichnung as beschreibung, 
          if(
            sn.seriennummer IS NULL,
            IF(bbf.mhd IS NULL,
                IF(batch.charge IS NULL,
                  '',
                  CONCAT('Charge: ',batch.charge)
                ),
                CONCAT('MHD: ', bbf.mhd)                  
            ),
            CONCAT('SN: ', sn.seriennummer)
          ),
          p.abkuerzung as projekt,
                trim(if(isnull(sn.menge), 
                    if(isnull(bbf.menge), 
                        IF(isnull(batch.menge),lp.menge,batch.menge)
                        , bbf.menge)
                    ,sn.menge))+0,
                $rmamengecol 
                l.belegnr as lieferschein, DATE_FORMAT(l.datum,'%d.%m.%Y') as datum, 
                           if(
            sn.seriennummer IS NULL,
            IF(bbf.mhd IS NULL,
                IF(batch.charge IS NULL,
                  lp.id,
                  CONCAT(lp.id,'-',batch.charge)
                ),
                CONCAT(lp.id,'-', bbf.mhd)                  
            ),
            CONCAT(lp.id,'-', sn.seriennummer)
          )

                
                FROM lieferschein_position lp
                $rmajoin
                LEFT JOIN lieferschein l ON lp.lieferschein=l.id 
                LEFT JOIN projekt p ON l.projekt=p.id 
                LEFT JOIN artikel a ON a.id=lp.artikel 
                LEFT JOIN (
                (SELECT '1' as menge, wert as seriennummer, pos FROM `beleg_chargesnmhd` WHERE doctype = 'lieferschein' AND type='sn' ) UNION ALL
                (SELECT '1' as menge,seriennummer, lieferscheinpos as pos FROM seriennummern)
                ) sn ON sn.pos = lp.id
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, 
                    IF(IFNULL(wert2,'') = '',wert ,CONCAT(wert,'-',IFNULL(wert2,''))) AS mhd, 
                    pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='mhd'
                    GROUP BY wert,pos,wert2
                ) AS bbf ON bbf.pos = lp.id AND a.mindesthaltbarkeitsdatum = 1 AND sn.pos IS NULL
                    AND (a.seriennummern = 'keine' OR a.seriennummern = '') 
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, wert AS charge, pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='charge'
                    GROUP BY wert,pos
                ) AS batch ON batch.pos = lp.id AND a.mindesthaltbarkeitsdatum = 0 AND a.chargenverwaltung > 0 
                    AND (a.seriennummern = 'keine' OR a.seriennummern = '')  AND sn.pos IS NULL
                $rmajoin2
                ";
        }

        $where = " (l.adresse='$adresse' AND (l.status='versendet' OR l.status='freigegeben') AND a.lagerartikel=1) " . $this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(lp.id) FROM lieferschein_position lp
              LEFT JOIN lieferschein l ON lp.lieferschein=l.id LEFT JOIN projekt p ON l.projekt=p.id LEFT JOIN artikel a ON a.id=lp.artikel WHERE ($where)";
        $moreinfo = false;
        break;
    }

    $erg = [];

    //$erlaubtevars = array('heading','width','sql','count','findcols','searchsql','defaultorder','defaultorderdesc','menu','menucol','where','groupby','allowed');


    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v))
      {
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  /**
   * Wareneingang constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("main","WareneingangMain");
    $this->app->ActionHandler("list","WareneingangList");
    $this->app->ActionHandler("help","WareneingangHelp");
    $this->app->ActionHandler("vorgang","VorgangAnlegen");
    $this->app->ActionHandler("removevorgang","VorgangEntfernen");
    //$this->app->ActionHandler("create","WareneingangCreate");
    $this->app->ActionHandler("paketannahme","WareneingangPaketannahme");
    $this->app->ActionHandler("paketzustand","WareneingangPaketZustand");
    $this->app->ActionHandler("paketetikett","WareneingangPaketEtikett");
    $this->app->ActionHandler("paketabschliessen","WareneingangPaketAbschliessen");
    $this->app->ActionHandler("distriinhalt","WareneingangPaketDistriInhalt");
    $this->app->ActionHandler("distrietiketten","WareneingangPaketDistriEtiketten");
    $this->app->ActionHandler("distrietikettenbilderfassen","WareneingangPaketDistriEtikettenBilderfassen");
    $this->app->ActionHandler("distriabschluss","WareneingangPaketDistriAbschluss");
    $this->app->ActionHandler("manuellerfassen","WareneingangManuellErfassen");
    $this->app->ActionHandler("minidetail","WareneingangMiniDetail");
    $this->app->ActionHandler("stornieren","WareneingangStornieren");
    $this->app->ActionHandler("settings","WareneingangSettings");

    $this->app->DefaultActionHandler("login");
    $this->app->erp->Headlines('Wareneinang');

    $this->app->ActionHandlerListen($app);
  }

  public function WareneingangSettings()
  {
    $menu = $this->app->Secure->GetGET('menu');

    if($menu === 'paketdistri'){
      $this->WareneingangPaketDistriMenu();
    }
    elseif($menu === 'paket'){
      $this->WareneingangPaketMenu();
    }
    else{
      $this->WareneingangMenu();
    }
    $this->app->YUI->AutoSaveFormular(
      'wareneingang',
      [
        'createreturnorder'=>[
          'bezeichnung' => 'Retouren annehmen mit Retouren-Beleg',
          'type'=>'checkbox',
        ]
      ],
      [
        'legend'=>'Einstellungen',
        'finaltarget' => 'TAB1',
        'target' => 'TABLE',
        'template' => 'table.tpl',
      ]
    );
    $this->app->Tpl->Set('TABLE', '');
    $this->app->erp->RunHook('wareneingang_settings', 0);

    $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
  }

  /**
   * @param bool $mitstandardlager
   * @param bool $forceZwischenlager
   * @param int  $lagerplatz
   *
   * @return array
   */
  public function GetLager($mitstandardlager=false, $forceZwischenlager = false, $lagerplatz = null)
  {
    $tmp = [];
    if($forceZwischenlager || $this->app->erp->Firmendaten('wareneingang_zwischenlager')=='1') {
      $tmp['zwischenlager'] = 'Zwischenlager';
    }
    if($mitstandardlager) {
      $tmp['standardlager'] = 'Standardlager';
    }

    $result = $this->app->DB->SelectArr(
      sprintf(
        "SELECT lp.id, CONCAT(l.bezeichnung,'->',lp.kurzbezeichnung) as kurzbezeichnung 
        FROM lager_platz AS lp 
        INNER JOIN lager AS l ON lp.lager=l.id 
        WHERE lp.kurzbezeichnung!='' AND lp.geloescht <> 1 AND l.geloescht <> 1 AND lp.id = %d
        ORDER BY l.bezeichnung,lp.kurzbezeichnung",
        $lagerplatz
      )
    );
    if(empty($result)) {
      return $tmp;
    }
    foreach($result as $row) {
      $tmp[$row['id']] = $row['kurzbezeichnung'];
    }
    return $tmp;
  }

  public function WareneingangPaketMenu()
  {
    $id = $this->app->Secure->GetGET('id');
    $action = $this->app->Secure->GetGET('action');
    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Add('KURZUEBERSCHRIFT',' Paketannahme');
    $this->app->erp->MenuEintrag('index.php?module=wareneingang&action=paketannahme','Paketannahme');
    $this->app->erp->RunMenuHook('wareneingangpaket');
    $this->app->erp->MenuEintrag(
      'index.php?module=wareneingang&action=settings&menu=paket',
      'Einstellungen',
      $action === 'settings'
    );
  }

  public function WareneingangPaketDistriMenu()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $action = $this->app->Secure->GetGET('action');
    $this->app->Tpl->Set('ID',$id);
    $this->app->Tpl->Add('KURZUEBERSCHRIFT',' Paketdistribution');
    $this->app->erp->MenuEintrag('index.php?module=wareneingang&action=paketannahme','zur Paketannahme');
    if($action==='distribution' && $id > 0){
      $this->app->erp->MenuEintrag('index.php?module=wareneingang&action=distribution&id='.$id, 'Paketannahme');
    }
    else if($action==='distribution'){
      $this->app->erp->MenuEintrag('index.php?module=wareneingang&action=distribution', 'Paketannahme');
    }
    else{
      $this->app->erp->MenuEintrag('index.php?module=wareneingang&action=distriinhalt&id='.$id, 'Paketannahme');
    }
    if($id && $this->app->erp->RechteVorhanden('schneller_wareneingang','distriinhalt'))
    {
      $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id = '$id' LIMIT 1");
      $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id = '$adresse' LIMIT 1");
      if($lieferantennummer != '0' && $lieferantennummer != '')
      {
        $this->app->erp->MenuEintrag('index.php?module=schneller_wareneingang&action=distriinhaltschnell&id='.$id,"schnelle Paketannahme");
      }
    }
    $this->app->erp->RunMenuHook('wareneingangpaketdistri');
    $this->app->erp->MenuEintrag(
      'index.php?module=wareneingang&action=settings&menu=paketdistri',
      'Einstellungen',
      $action === 'settings'
    );
  }

  public function WareneingangStornieren()
  {
    $id = $this->app->Secure->GetGET('id');

    if($id > 0 && is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM paketannahme WHERE id='$id' LIMIT 1");
    }
    $this->app->Location->execute('Location: index.php?module=wareneingang&action=distribution');
  }


  public function WareneingangMenu()
  {
    $action = $this->app->Secure->GetGET('action');
    $this->app->Tpl->Add('KURZUEBERSCHRIFT',' Wareneingang');
    $this->app->erp->MenuEintrag('index.php?module=wareneingang&action=list','&Uuml;bersicht');
    $this->app->erp->RunMenuHook('wareneingang');
    $this->app->erp->MenuEintrag(
      'index.php?module=wareneingang&action=settings',
      'Einstellungen',
      $action==='settings'
    );
  }

  /**
   * @return JsonResponse
   */
  protected function HandleCreateReturnOrderAjaxAction()
  {
    $id = $this->app->Secure->GetPOST('id');
    $addressId = (int)$this->app->DB->Select(sprintf('SELECT adresse FROM paketannahme WHERE id = %d LIMIT 1', $id));
    $sql = "
      SELECT lp.*, IFNULL(sn.seriennummer, '') AS serialnumber,
             rq.bestbefore,rq.batch,
             rq.quantity, rq.id As rqid
      
                FROM lieferschein_position lp
                
                LEFT JOIN lieferschein l ON lp.lieferschein=l.id 
                LEFT JOIN projekt p ON l.projekt=p.id 
                LEFT JOIN artikel a ON a.id=lp.artikel 
                LEFT JOIN
      (
        (
        SELECT '1' as menge, wert as seriennummer, pos 
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='sn' 
                  ) 
                  UNION ALL
      (
        SELECT '1' as menge,seriennummer, lieferscheinpos as pos 
                    FROM seriennummern
                  )
                ) sn ON sn.pos = lp.id
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, 
                    IF(IFNULL(wert2,'') = '',wert ,CONCAT(wert,'-',IFNULL(wert2,''))) AS mhd,
                    pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='mhd'
                    GROUP BY wert,pos,wert2
                ) AS bbf ON bbf.pos = lp.id AND a.mindesthaltbarkeitsdatum = 1 AND sn.pos IS NULL
                   AND (a.seriennummern = 'keine' OR a.seriennummern = '')  
                LEFT JOIN (
                    SELECT SUM(menge) AS menge, wert AS charge, pos
                    FROM `beleg_chargesnmhd` 
                    WHERE doctype = 'lieferschein' AND type='charge'
                    GROUP BY wert,pos
                ) AS batch ON batch.pos = lp.id AND a.mindesthaltbarkeitsdatum = 0 AND a.chargenverwaltung > 0 
                    AND (a.seriennummern = 'keine' OR a.seriennummern = '')  AND sn.pos IS NULL 
                    
        INNER JOIN `returnorder_quantity` AS rq
        ON lp.id = rq.delivery_note_id AND IFNULL(sn.seriennummer, '') = rq.serialnumber 
               AND IFNULL(rq.quantity,0) > 0 AND IFNULL(batch.charge,'') = rq.batch AND IFNULL(bbf.mhd,'') = rq.bestbefore
        WHERE (l.adresse='$addressId' AND (l.status='versendet' OR l.status='freigegeben') 
                   AND a.lagerartikel=1) "
      .$this->app->erp->ProjektRechte()
      ."ORDER BY l.datum, l.id, lp.sort,sn.seriennummer";

    $positions = $this->app->DB->SelectArr($sql);
    if(empty($positions)) {
      return new JsonResponse(['status' => 0, 'error' => 'Position nicht gefunden']);
    }
    /** @var Retoure $returnOrderObj */
    $returnOrderObj = $this->app->erp->LoadModul('retoure');
    if($returnOrderObj === null) {
      return new JsonResponse(['status' => 0, 'error' => 'Retourenmodul nicht gefunden oder deaktiviert']);
    }
    $returnOrderId = $this->app->erp->CreateRetoure($addressId);
    $this->app->erp->LoadRetoureStandardwerte($returnOrderId, $addressId);
    $this->app->erp->RetoureProtokoll($returnOrderId, 'Retoure angelegt');
    foreach($positions as $position) {
      $this->app->DB->Insert("INSERT INTO retoure_position (id) VALUES (NULL)");
      $newposid = $this->app->DB->GetInsertID();
      $idtoid[$position['id']] = $newposid;
      $artikelarr[$newposid] = $position['artikel'];
      $position['lieferschein_position_id'] = $position['id'];
      if(!empty($position['explodiert_parent'])) {
        $position['explodiert_parent'] =
          isset($idtoid[$position['explodiert_parent']])
            ? $idtoid[$position['explodiert_parent']] : 0;
      }
      $position['retoure'] = $returnOrderId;
      $position['menge'] = $position['quantity'];
      $this->app->DB->Delete(sprintf('DELETE FROM `returnorder_quantity` WHERE `id` = %d', $position['rqid']));
      $serialnumber = (string)$position['serialnumber'];
      $bestbefore = (string)$position['bestbefore'];
      $batch = (string)$position['batch'];
      if(!empty($bestbefore)) {
        $batch = ltrim(substr($bestbefore,10),'-');
        $bestbefore = substr($bestbefore,0,10);
        if(strpos($bestbefore, '.') !== false) {
          $bestbefore = $this->app->String->Convert($bestbefore,'%1.%2.%3','%3-%2-%1');
        }
      }
      unset($position['quantity'], $position['rqid'], $position['serialnumber'],$position['lagertext']);
      $this->app->DB->UpdateArr('retoure_position', $newposid,'id', $position, true);
      if($serialnumber !== '' && $serialnumber !== '0') {
        $this->app->erp->CreateBelegPositionMHDCHARGESRN(
          'retoure',$returnOrderId,$newposid,'sn',$serialnumber,1
        );
      }
      elseif(!empty($bestbefore)) {
        if(!empty($batch)) {
          $this->app->erp->CreateBelegPositionMHDCHARGESRN(
            'retoure',$returnOrderId,$newposid,'mhd',$bestbefore,$position['menge'],'charge', $batch
          );
          $this->app->erp->CreateBelegPositionMHDCHARGESRN(
            'retoure',$returnOrderId,$newposid,'charge',$batch,$position['menge']
          );
        }
        else {
          $this->app->erp->CreateBelegPositionMHDCHARGESRN(
            'retoure',$returnOrderId,$newposid,'mhd',$bestbefore,$position['menge']
          );
        }
      }
      elseif(!empty($batch)) {
        $this->app->erp->CreateBelegPositionMHDCHARGESRN(
          'retoure',$returnOrderId,$newposid,'charge',$batch,$position['menge']
        );
      }
    }
    $this->app->erp->BelegFreigabe('retoure', $returnOrderId);

    return new JsonResponse(      [
      'status' => 1,
      'url' =>
        'index.php?module=wareneingang&action=distriinhalt&cmd=createreceiptdocumentreturnorder&id='
        .$returnOrderId
    ]);
  }

  /**
   * @return JsonResponse
   */
  protected function HandleChangeQtyAjaxAction()
  {
    $dnpid = (int)$this->app->Secure->GetPOST('dnpid');
    $dnp = $dnpid <= 0?null:$this->app->DB->SelectRow(
      sprintf(
        'SELECT dn.* FROM `lieferschein_position` AS `dn` WHERE dn.id = %d LIMIT 1',
        $dnpid
      )
    );
    if(empty($dnp)) {
      return new JsonResponse(
        ['status' => 0, 'error' => 'Position nicht gefunden']
      );
    }

    $serialnumber = $this->app->Secure->GetPOST('serialnumber');
    $batch = $this->app->Secure->GetPOST('batch');
    $bestbefore = $this->app->Secure->GetPOST('bestbefore');
    $qty = str_replace(',', '.', $this->app->Secure->GetPOST('value'));
    if(round($qty,4) > round($dnp['menge'],4)) {
      return new JsonResponse(['status' => 0, 'error' => 'Die Menge ist zu groß']);
    }
    $check = $this->app->DB->SelectRow(
      sprintf(
        "SELECT rq.* 
          FROM `returnorder_quantity` AS `rq` 
          WHERE rq.delivery_note_id = %d AND rq.serialnumber = '%s' 
            AND rq.bestbefore = '%s' AND rq.batch = '%s' ",
        $dnpid, $serialnumber, $bestbefore, $batch
      )
    );
    if(empty($check) && $qty !== ''){
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `returnorder_quantity` 
            (`delivery_note_id`, `quantity`, `serialnumber`,`bestbefore`,`batch`) 
            VALUES (%d, %f, '%s','%s','%s') ",
          $dnpid, $qty, $serialnumber, $bestbefore, $batch
        )
      );
    }
    elseif(!empty($check)){
      $this->app->DB->Update(
        sprintf(
          "UPDATE `returnorder_quantity` SET `quantity` = %s WHERE `id` = %d",
          $qty !== '' ? (float)$qty : 'NULL', $check['id']
        )
      );
    }

    return new JsonResponse(['status' => 1]);
  }

  /**
   * @param int $returnOrderId
   *
   * @return array
   */
  public function getNextReturnOrderPosition($returnOrderId)
  {
    return (array)$this->app->DB->SelectRow(
      sprintf(
        "SELECT `rop`.*, art.seriennummern, art.chargenverwaltung, art.mindesthaltbarkeitsdatum
        FROM `retoure_position` AS `rop`
        INNER JOIN `artikel` AS `art` ON rop.artikel = art.id AND (art.geloescht = 0 OR art.geloescht IS NULL)
        INNER JOIN `retoure` AS `ro` ON rop.retoure = ro.id AND ro.status <> 'storniert' AND ro.belegnr <> ''
        WHERE `rop`.retoure = %d AND rop.menge_eingang < rop.menge %s 
        ORDER BY rop.sort, rop.id
        LIMIT 1",
        $returnOrderId, $this->app->erp->ProjektRechte('ro.projekt')
      )
    );
  }

  /**
   * @param int|array $returnOrder
   * @param null|int  $id
   *
   * @return string
   */
  public function getNextReturnOrderLocation($returnOrder, $id = null)
  {
    if($id === null) {
      $id = (int)$this->app->Secure->GetGET('id');
    }
    if(is_array($returnOrder)) {
      $rop = $returnOrder;
      $returnOrderId = $rop['retoure'];
    }
    else{
      $returnOrderId = $returnOrder;
      $rop = $this->getNextReturnOrderPosition($returnOrderId);
    }
    if(empty($rop)) {
      return '';
    }

    $serial = '';
    $serials = [];
    $bestbefore = '';
    $batch = '';
    $amount = $rop['menge'] - $rop['eingang'];
    $isSerial = !empty($rop['seriennummern']) && $rop['seriennummern'] !== 'keine';
    $isBestBefore = !empty($rop['mindesthaltbarkeitsdatum']);
    $isBatch = !empty($rop['chargenverwaltung']);
    if($isSerial) {
      $sns = $this->app->DB->SelectFirstCols(
        sprintf(
          "SELECT `wert`
          FROM `beleg_chargesnmhd` AS `bc` 
          WHERE bc.doctype = 'retoure' AND bc.`doctypeid` = %d AND bc.pos = %d AND `type` = 'sn' AND `wert` <> ''",
          $returnOrderId, $rop['id']
        )
      );

      if(!empty($sns)) {
        $serial = $sns[0];
        $movements = $this->app->DB->SelectFirstCols(
          sprintf(
            "SELECT sl.bezeichnung 
            FROM `seriennummern_log` AS `sl` 
            WHERE `doctype` = 'retoure' AND `doctypeid` = %d AND `artikel` = %d AND `eingang` = 1",
            $returnOrderId, $rop['artikel']
          )
        );

        foreach($sns as $sn) {
          if(!in_array($sn, $movements)) {
            $serials[] = $sn;
          }
        }
        if(!empty($serials)) {
          $serial = reset($serials);
        }
      }
    }
    elseif($isBestBefore) {
      if($isBatch) {
        $sns = $this->app->DB->SelectPairs(
          sprintf(
            "SELECT CONCAT(`wert`, IF(`type2` = 'charge',CONCAT('-', `wert2`),'')), `menge` 
            FROM `beleg_chargesnmhd` AS `bc` 
            WHERE bc.doctype = 'retoure' AND bc.`doctypeid` = %d AND bc.pos = %d AND `type` = 'mhd'",
            $returnOrderId, $rop['id']
          )
        );
      }
      else{
        $sns = $this->app->DB->SelectPairs(
          sprintf(
            "SELECT `wert`, `menge` 
            FROM `beleg_chargesnmhd` AS `bc` 
            WHERE bc.doctype = 'retoure' AND bc.`doctypeid` = %d AND bc.pos = %d AND `type` = 'mhd'",
            $returnOrderId, $rop['id']
          )
        );
      }
      if(!empty($sns)) {
        $movements = $this->app->DB->SelectFirstCols(
          sprintf(
            "SELECT CONCAT(ml.mhddatum,'-',IFNULL(ml.charge,'')) 
            FROM `mhd_log` AS `ml`
            WHERE `doctype` = 'retoure' AND `doctypeid` = %d AND `artikel` = %d AND `eingang` = 1",
            $returnOrderId, $rop['artikel']
          )
        );
        $bestbefores = array_keys($sns);
        $bestbefore = reset($bestbefores);

        foreach ($sns as $sn => $bestBeforeAmount) {
          if(!in_array($sn, $movements)) {
            $bestbefore = $sn;
            if($bestBeforeAmount < $amount) {
              $amount = $bestBeforeAmount;
            }
            break;
          }
        }
        if(strlen($bestbefore) >= 10) {
          $batch = substr($bestbefore, 10);
          $bestbefore = substr($bestbefore, 0, 10);
          if(strpos($bestbefore, '.') === false) {
            $bestbefore = $this->app->String->Convert($bestbefore,'%1-%2-%3', '%3.%2.%1');
          }
          $bestbefore .= $batch;
        }
      }
    }
    elseif($isBatch) {
      $sns = $this->app->DB->SelectPairs(
        sprintf(
          "SELECT `bc`.wert, `bc`.menge
          FROM `beleg_chargesnmhd` AS `bc` 
          WHERE bc.doctype = 'retoure' AND bc.`doctypeid` = %d AND bc.pos = %d AND `type` = 'charge'",
          $returnOrderId, $rop['id']
        )
      );
      if(!empty($sns)){
        $movements = $this->app->DB->SelectFirstCols(
          sprintf(
            "SELECT cl.bezeichnung 
            FROM `chargen_log` AS `cl`
            WHERE `doctype` = 'retoure' AND `doctypeid` = %d AND `artikel` = %d AND `eingang` = 1",
            $returnOrderId, $rop['artikel']
          )
        );
        $batchess = array_keys($sns);
        $batch = reset($batchess);

        foreach ($sns as $sn => $batchAmount) {
          if(!in_array($sn, $movements)) {
            $batch = $sn;
            if($batchAmount < $amount) {
              $amount = $batchAmount;
            }
            break;
          }
        }
      }
    }
    $serialsUrl = '';
    if(!empty($serials)) {
      foreach($serials as $serialVal) {
        $serialsUrl .= '&seriennummer[]='.urlencode($serialVal);
      }
    }
    elseif(!empty($serial)) {
      $serialsUrl .= '&seriennummer[]='.urlencode($serial);
    }
    return sprintf(
        'index.php?module=wareneingang&action=distrietiketten&id=%d&retourepos=%d&menge=%f%s&mhd=%s&charge=%s',
        $id, $rop['id'], $amount, $serialsUrl, urlencode($bestbefore), urlencode($batch)
      );

  }

  /**
   * @param int $returnOrderId
   */
  public function LocatateToReturnOrderPosition($returnOrderId)
  {
    $id = $this->app->Secure->GetGET('id');

    $ropLink = $this->getNextReturnOrderLocation($returnOrderId, $id);
    if(!empty($ropLink)) {
      $this->app->Location->execute($ropLink);
    }

    $userReturnOrder = $this->app->User->GetParameter('wareneingang_from_returnorder');
    if($userReturnOrder != $returnOrderId) {
      return;
    }
    $module = $this->app->User->GetParameter('wareneingang_from_module');
    if(empty($module)) {
      return;
    }
    $action = $this->app->User->GetParameter('wareneingang_from_action');
    if(empty($action)) {
      return;
    }
    $this->app->User->SetParameter('wareneingang_from_returnorder', '');
    $this->app->User->SetParameter('wareneingang_from_module', '');
    $this->app->User->SetParameter('wareneingang_from_action', '');
    if($module !== 'retoure') {
      $id = '';
    }
    $this->app->Location->execute('index.php?module='.$module.'&action='.$action.'&id='.$id);
  }

  public function WareneingangPaketDistriInhalt()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $isCmdFromReturnOrder = $cmd === 'fromreturnorder';
    if($isCmdFromReturnOrder) {
      /** @var Retoure $returnOrderObj */
      $returnOrderObj = $this->app->loadModule('retoure');
      if(!empty($returnOrderObj)) {
        $id = $this->app->Secure->GetGET('id');
        $receiptDocument = $returnOrderObj->getReceiptDocumentArrByParcelId($id);
        if(!empty($receiptDocument['return_order_id'])) {
          $this->LocatateToReturnOrderPosition($receiptDocument['return_order_id']);
        }
      }
    }
    if($cmd === 'createreturnorder') {
      $response = $this->HandleCreateReturnOrderAjaxAction();
      $response->send();
      $this->app->ExitXentral();

    }
    if($cmd === 'changeqty') {
      $response = $this->HandleChangeQtyAjaxAction();
      $response->send();
      $this->app->ExitXentral();
    }
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->RunHook('wareneingang_distriinhalt_start', 1, $id);

    $submit = $this->app->Secure->GetPOST('submit');
    $submitkunde = $this->app->Secure->GetPOST('submitkunde');

    $this->WareneingangPaketDistriMenu();

    if($submit!='') {
      $tmp = $this->app->Secure->GetPOST('pos'); 
      $tmpRetoure = $this->app->Secure->GetPOST('retourepos');
      $col = 'pos';
      if(!empty($tmpRetoure)) {
        $tmp = $tmpRetoure;
        $col= 'retourepos';
      }
      $pos = key($tmp);
      $menge = str_replace(',','.',$tmp[$pos]);
      $seriennummer = '';
      $posa = explode('-', $pos, 2);
      $pos = $posa[0];
      if(isset($posa[1])){
        $seriennummer = $posa[1];
      }
      if($menge<=0) {
        $this->app->Tpl->Set('TAB1','<div class="error">Bitte geben Sie eine Menge an!</div>');
      }
      else {
        if($pos > 0 && !empty($tmpRetoure) && $this->app->User->GetParameter('wareneingang_from_type') === 'manual') {
          $returnOrder = $this->app->DB->Select(
            sprintf(
              "SELECT ro.* 
              FROM `retoure_position` AS `rop` 
              INNER JOIN `retoure` AS `ro` ON rop.retoure = ro.id 
              WHERE rop.`id` = %d AND `ro`.status <> 'angelegt' AND ro.status <> 'storniert' 
                AND rop.menge_eingang < rop.menge",
              $pos
            )
          );
          if(!empty($returnOrder['adresse'])) {
            $receiptDocument = $this->app->DB->SelectRow(
              sprintf(
                "SELECT * 
                FROM `receiptdocument` 
                WHERE `return_order_id` = %d
                ORDER BY `status` = '' OR `status`  = 'angelegt' DESC
                LIMIT 1",
                $returnOrder['retoure']
              )
            );
            if(empty($receiptDocument)) {
              /** @var Retoure $obj */
              $obj = $this->app->loadModule('retoure');
              $parcelReceiptId = null;
              if($obj !== null) {
                $parcelReceiptId = $this->createParcelReceiptFromAdress($returnOrder['adresse']);
                $receiptDocumentId = $obj->createReceiptDocument($returnOrder['adresse'], $parcelReceiptId);
                $this->app->DB->Update(
                  sprintf(
                    'UPDATE `receiptdocument` SET `return_order_id` = %d WHERE `id` = %d',
                    $returnOrder['id'], $receiptDocumentId
                  )
                );
              }
            }
          }
        }
        $this->app->Location->execute("index.php?module=wareneingang&action=distrietiketten&id=$id&$col=$pos&menge=$menge&seriennummer=".$seriennummer);
      }
    }


    $adresse= $this->app->DB->Select(
      sprintf(
        'SELECT `adresse` FROM `paketannahme` WHERE `id` = %d LIMIT 1',
        $id
      )
    );

    $addressRow = empty($adresse)?null: $this->app->DB->SelectRow(
      sprintf(
        'SELECT `name`,`kundennummer`,`lieferantennummer` 
        FROM `adresse` 
        WHERE `id` = %d AND `geloescht` = 0 
        LIMIT 1',
        $adresse
      )
    );

    // pruefe ob 
    $lieferant = empty($addressRow)?null:$addressRow['lieferantennummer'];
    $kunde= empty($addressRow)?null:$addressRow['kundennummer'];

    $name= empty($addressRow)?null:$addressRow['name'];
    $isSupplier = $lieferant!='' && $lieferant!='0';
    $isCustomer = $kunde!='' && $kunde!='0';

    if($isSupplier) {
      //$this->app->Tpl->Set('TAB1TEXT','<li><a href="#tabs-1">Bestellungen</a></li>');
      $this->app->Tpl->Set('TAB1TEXT','Bestellungen');
      //$this->app->Tpl->Set('TAB1START','<div id=\"tabs-1\">');
      //$this->app->Tpl->Set('TAB1ENDE','</div>');

      $this->app->Tpl->Add('TAB1',"<br><h1>Offene Artikel aus Bestellungen bei $name:</h1><br>");
      $this->app->YUI->TableSearch('TAB1','wareneingang_lieferant','show','','',basename(__FILE__), __CLASS__);
    }
    else {
      /*$this->app->Tpl->Set('TAB1START','<!--');
      $this->app->Tpl->Set('TAB1ENDE','-->');*/
      $this->app->Tpl->Set('TAB1START','');
      $this->app->Tpl->Set('TAB1ENDE','');
      $this->app->Tpl->Set('BEFORETAB1', '<!--');
      $this->app->Tpl->Set('AFTERTAB1', '-->');
    }



    if(!$isSupplier && !$isCustomer) {
      $this->app->Tpl->Set('TAB1START','');
      $this->app->Tpl->Set('TAB1ENDE','');
      $this->app->Tpl->Set('BEFORETAB1', '');
      $this->app->Tpl->Set('AFTERTAB1', '');
      $this->app->Tpl->Set(
        'TAB1',
        '<div class="error">Die ausgew&auml;hlte Adresse hat noch keine Rolle Kunde oder Lieferant. 
          Bitte vergeben Sie diese, dann sehen Sie Bestellungen oder versendete Waren.</div>'
      );
    }
    
    $this->app->erp->RunHook('wareneingang_distriinhalt', 1, $id);
    if($showcreateReturnOrder) {
      $this->app->Tpl->addButton(
        'BUTTONS2',
        '✚ Retoure Anlegen',
        '',
        '',
        'newreturnorder button-primary button',
        [
          'id'=>'newreturnorder',
          'data-id' => $id,
        ]
      );
    }
    if($this->app->User->GetParameter('wareneingang_action')==='distribution'){
      $this->app->erp->MenuEintrag(
        'index.php?module=wareneingang&action=distribution',
        'zur&uuml;ck zur &Uuml;bersicht'
      );
    }
    if($isSupplier){
      $this->app->Tpl->Parse('PAGE', 'wareneingangpaketdistribution.tpl');
    }
    else {
      $this->app->Tpl->Parse('PAGE', 'wareneingangpaketdistribution2.tpl');
    }
    $returnordergeprueft = $this->app->Secure->GetPOST('returnordergeprueft');
    $abschliessen = $this->app->Secure->GetPOST('abschliessen');
    if($abschliessen!='' || $returnordergeprueft != '') {
      // paketannahme auf abgeschlossen setzten

      $returnOrderId = empty($returnordergeprueft)?null: $this->app->DB->Select(
        sprintf(
          'SELECT return_order_id FROM receiptdocument WHERE parcel_receipt_id = %d',
          $id
        )
      );
      $this->app->DB->Update("UPDATE paketannahme SET status='abgeschlossen' WHERE id='$id' LIMIT 1");


      $this->app->erp->RunHook('wareneinang_paketannahme_abschliessen',1, $id);
      $this->app->Location->execute('index.php?module=wareneingang&action=paketannahme');
    }

    $manuellerfassen = $this->app->Secure->GetPOST('manuellerfassen');
    if($manuellerfassen!='') {
      $this->app->Location->execute('index.php?module=wareneingang&action=manuellerfassen&id='.$id);
    }
  }

  public function WareneingangMiniDetail()
  {
    $id = $this->app->Secure->GetGET('id');
    if($this->app->Secure->GetGET('cmd') === 'change') {
      $beipack_lieferschein = (int)$this->app->Secure->GetPOST('beipack_lieferschein');
      $beipack_rechnung = (int)$this->app->Secure->GetPOST('beipack_rechnung');
      $renr = $this->app->Secure->GetPOST('renr');
      $lsnr = $this->app->Secure->GetPOST('lsnr');
      $this->app->DB->Update("UPDATE paketannahme SET renr = '$renr',lsnr = '$lsnr',beipack_lieferschein = '$beipack_lieferschein', beipack_rechnung = '$beipack_rechnung' WHERE id = '$id' LIMIT 1");
      echo json_encode(array('status'=>1));
      $this->app->ExitXentral();
    }
    $table = new EasyTable($this->app);    
    $table->Query("SELECT a.nummer, LEFT(a.name_de,30) as artikel, trim(lb.menge)+0 as menge FROM lager_bewegung lb
        LEFT JOIN artikel a ON lb.artikel=a.id
        WHERE lb.paketannahme='$id' ORDER by a.nummer");
    $this->app->Tpl->Set('MD5', md5(microtime(true)));
    $this->app->Tpl->Set('ID', $id);
    $arr = $this->app->DB->SelectRow("SELECT * FROM paketannahme WHERE id = '$id' LIMIT 1");
    if($arr) {
      if($arr['beipack_rechnung']) {
        $this->app->Tpl->Set('BEIPACK_RECHNUNG',' checked="checked" ');
      }
      if($arr['beipack_lieferschein']) {
        $this->app->Tpl->Set('BEIPACK_LIEFERSCHEIN',' checked="checked" ');
      }
      $this->app->Tpl->Set('RENR', $arr['renr']);
      $this->app->Tpl->Set('LSNR', $arr['lsnr']);
    }
    $table->DisplayNew('ARTIKEL','Menge','noAction');
    $this->app->Tpl->Output('wareneingang_minidetail.tpl');
    $this->app->ExitXentral();
  }

  public function WareneingangManuellErfassen()
  {
    $id = $this->app->Secure->GetGET('id');
    $paket = $this->app->Secure->GetGET('paket');
    $this->app->erp->MenuEintrag('index.php?module=wareneingang&action=distriinhalt&id='.$id,'Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=wareneingang&action=manuellerfassen&id='.$id,'Artikel');
    $artikel = $this->app->Secure->GetPOST('artikel');
    if($artikel) {
      $artikelid = $this->app->DB->Select("SELECT a.id FROM artikel a LEFT JOIN projekt pr ON a.projekt = pr.id WHERE (a.nummer = '".$artikel."' OR a.herstellernummer = '".$artikel."' OR a.ean = '".$artikel."') AND a.geloescht = 0 ".$this->app->erp->ProjektRechte()." LIMIT 1");
      if($artikelid) {
        $this->app->Location->execute('index.php?module=wareneingang&action=distrietiketten&id='.(int)$id.'&pos='.$artikelid.'%&menge=1&cmd=manuell');
      }
      $this->app->Tpl->Add('MESSAGE','<div class="error">{|Der Artikel wurde nicht gefunden|}</div>');
    }

    $cmd = $this->app->Secure->GetGET('cmd');

    if($cmd==='add') {
      echo 'huhuh';
    }
    else {
      $this->app->YUI->TableSearch('TAB1','wareneingangartikelmanuellerfassen','show','','',basename(__FILE__), __CLASS__);
    }

    //$this->WareneingangPaketMenu();
    $this->app->Tpl->Parse('PAGE','wareneingang_manuellerfassen.tpl');
  }

  public function WareneingangPaketDistriEtikettenBildErfassen(){
    $this->app->Tpl->Set('TAB1','Die Funktion hat keinen Inhalt. Sie dient lediglich zur Berechtigungserfassung.');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function WareneingangPaketDistriEtiketten()
  {
    $id = $this->app->Secure->GetGET('id');  
    $pos = $this->app->Secure->GetGET('pos');
    $retourepos = $this->app->Secure->GetGET('retourepos');
    $artikelnummer = $this->app->Secure->GetGET('artikelnummer');  
    $seriennummer = $this->app->Secure->GetGET('seriennummer');  
    $menge = str_replace(',','.',$this->app->Secure->GetGET('menge'));
    $nmenge = str_replace(',','.',$this->app->Secure->GetGET('nmenge'));
    if($nmenge > 0) {
      $menge = $nmenge;
    }
    $rma = $this->app->Secure->GetGET('rma');  

    $submit = $this->app->Secure->GetPOST('submit');  
    $lager = $this->app->Secure->GetPOST('lager');  
    $etiketten = $this->app->Secure->GetPOST('etiketten');  
    $anzahlauswahl = $this->app->Secure->GetPOST("anzahlauswahl");  
    $anzahl_fix = $this->app->Secure->GetPOST('anzahl_fix');  
    $anzahl_dyn = $this->app->Secure->GetPOST('anzahl_dyn');  
    $anzahl = $this->app->Secure->GetPOST('anzahl');  
    $bemerkung = $this->app->Secure->GetPOST('bemerkung');  
    $chargesnmhdbemerkung = $this->app->Secure->GetPOST('chargesnmhdbemerkung');  
    $wunsch= $this->app->Secure->GetPOST('wunsch');  
    $cmd= $this->app->Secure->GetGET('cmd');
    $backaction = $this->app->Secure->GetGET('backaction');
    $ismobile = $this->app->Secure->GetGET('ismobile');
    $withZwischenlager = $ismobile || $this->app->erp->Firmendaten('wareneingang_zwischenlager')=='1';

    if($cmd === 'scan') {
      $lagerscan = $this->app->Secure->GetPOST('elagerscan');
      if($lagerscan != '') {
        $lagervorhanden = $this->app->DB->SelectRow(
          "SELECT lp.id, CONCAT(l.bezeichnung,'->',lp.kurzbezeichnung) AS kurzbezeichnung 
          FROM lager_platz AS lp
          INNER JOIN lager AS l ON lp.lager = l.id AND l.geloescht <> 1
          WHERE lp.kurzbezeichnung = '$lagerscan' AND lp.geloescht <> 1 
          LIMIT 1"
        );
        if(!empty($lagervorhanden)){
          echo json_encode(
            array(
              'status'=>1,'id'=>$lagervorhanden['id'],'kurzbezeichnung'=>$lagervorhanden['kurzbezeichnung']
            )
          );
          $this->app->ExitXentral();
        }
        $error = 'Gescanntes Regal existiert nicht';
        echo json_encode(array('status'=>0,'statusText'=>$error));
        $this->app->ExitXentral();
      }
    }

    $this->app->Tpl->Set('ID',$id);
    $weiterleitung = '';
    if($cmd==='manuell'){
      $this->app->DB->Update("UPDATE artikel SET lagerartikel='1' WHERE id='$pos' AND juststueckliste!=1 LIMIT 1");
      $artikel = $pos;
      $this->app->Tpl->Set('ANZAHLAENDERN',"<input type=\"button\" value=\"&auml;ndern\" onclick=\"var menge =  prompt('Neue Menge:',$menge); if(menge > 0) window.location.href=document.URL + '&menge=' + menge;\">");
      //$this->app->Tpl->Set('SHOWANZAHLSTART','<!--'); //BENE war auskommentiert
      //$this->app->Tpl->Set('SHOWANZAHLENDE','-->'); //BENE war auskommentiert
    }
    else if($cmd==='manuell') {
      $artikel = $pos;
      $mitarbeiter = $this->app->User->GetName();
      $projekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$artikel' LIMIT 1");
    }
    else {
      $retarr = null;
      $bparr = null;
      //bestellung
      // bestellung findet man raus ueber pos (bestellung)
      $artikel = 0;
      $projekt = 0;
      $bestellung = 0;
      $vpe = '';
      $menge_bestellung = 0;
      $menge_retoure = 0;
      if(!empty($retourepos)) {
        $retarr = $this->app->DB->SelectRow("SELECT * FROM retoure_position WHERE id='$retourepos' LIMIT 1");
      }
      else{
        $bparr = $this->app->DB->SelectRow("SELECT * FROM bestellung_position WHERE id='$pos' LIMIT 1");
      }
      if(!empty($retarr)) {
        $artikel = $retarr['artikel'];
        $projekt = $retarr['projekt'];
        $retoure = $retarr['retoure'];
        $vpe = $retarr['vpe'];
        $menge_retoure = $retarr['menge'];
      }
      elseif(!empty($bparr)){
        $artikel = $bparr['artikel'];
        $projekt = $bparr['projekt'];
        $bestellung = $bparr['bestellung'];
        $vpe = $bparr['vpe'];
        $menge_bestellung = $bparr['menge'];
      }
      $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");
      $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $retoure_belegnr = '';
      $bestellung_belegnr = '';
      if(!empty($retourepos)) {
        $mitarbeiter = $this->app->DB->Select("SELECT bearbeiter FROM retoure WHERE id='$retoure' LIMIT 1");
        $retoure_belegnr = $this->app->DB->Select("SELECT belegnr FROM retoure WHERE id='$retoure' LIMIT 1");
      }
      else {
        $mitarbeiter = $this->app->DB->Select("SELECT bearbeiter FROM bestellung WHERE id='$bestellung' LIMIT 1");
        $bestellung_belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$bestellung' LIMIT 1");
      }
    }
    $standardlager = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='".$artikel."' LIMIT 1");
    $withStandardlager = $standardlager > 0;

    $artikelRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT lagerartikel,mindesthaltbarkeitsdatum, seriennummern,nummer, name_de,
       artikelcheckliste,funktionstest,endmontage,standardbild,shop,chargenverwaltung
        FROM artikel WHERE id = %d LIMIT 1',
        $artikel
      )
    );
    $lagerartikel = $artikelRow['lagerartikel'];
    $mindesthaltbarkeitsdatum = $artikelRow['mindesthaltbarkeitsdatum'];
    $seriennummern = $artikelRow['seriennummern'];
    if($seriennummern != '' && $seriennummern !== 'keine'){
      $menge = (int)$menge;
    }
    $mitarbeiter_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$mitarbeiter' AND geloescht=0 LIMIT 1");
    $artikelcheckliste = !empty($artikelRow)?$artikelRow['artikelcheckliste']:$this->app->DB->Select("SELECT artikelcheckliste FROM artikel WHERE id='$artikel' LIMIT 1");
    $funktionstest = !empty($artikelRow)?$artikelRow['funktionstest']:$this->app->DB->Select("SELECT funktionstest FROM artikel WHERE id='$artikel' LIMIT 1");
    $endmontage = !empty($artikelRow)?$artikelRow['endmontage']:$this->app->DB->Select("SELECT endmontage FROM artikel WHERE id='$artikel' LIMIT 1");
    $name_de = !empty($artikelRow)?$artikelRow['name_de']:$this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
    $nummer = !empty($artikelRow)?$artikelRow['nummer']:$this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
    $chargenverwaltung= !empty($artikelRow)?$artikelRow['chargenverwaltung']:$this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id='$artikel' LIMIT 1");
    $standardbild = !empty($artikelRow)?$artikelRow['standardbild']:$this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='$artikel' LIMIT 1");
    $shopartikel = !empty($artikelRow)?$artikelRow['shop']:$this->app->DB->Select("SELECT shop FROM artikel WHERE id='$artikel' LIMIT 1");

    if($standardbild==''){
      $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel' LIMIT 1");
    }

    if(empty($retourepos)){
      if(($menge > $menge_bestellung) && $cmd !== 'manuell'){
        $this->app->Tpl->Add('MESSAGE', "<div class=\"error\">Achtung! Es wurden mehr geliefert als in der aktuellen Position bestellt worden sind!
          &nbsp;<input type=\"button\" onclick=\"window.location.href='index.php?module=wareneingang&action=distriinhalt&id=$id'\"
          value=\"Anzahl anpassen\" /></div>");
      }
    }
    else{
      if(($menge > $menge_retoure) && $cmd !== 'manuell'){
        $this->app->Tpl->Add('MESSAGE', "<div class=\"error\">Achtung! Es wurden mehr geliefert als in der aktuellen Position eingangen worden sind!
          &nbsp;<input type=\"button\" onclick=\"window.location.href='index.php?module=wareneingang&action=distriinhalt&id=$id'\"
          value=\"Anzahl anpassen\" /></div>");
      }
    }

    $bilderfassen = $this->app->erp->RechteVorhanden('wareneingang','distrietikettenbilderfassen');
    $stichwoerter = $this->app->erp->getDateiTypen('artikel');
    foreach ($stichwoerter as $stichwort){
      $selected = '';
      $vorauswahl = $this->app->erp->Firmendaten('wareneingangbildtypvorauswahl');
      if ($stichwort['wert'] === $vorauswahl){
        $selected = 'selected';
      }
      $this->app->Tpl->Add('BILDTYPEN','<option value="'.$stichwort['wert'].'" '.$selected.'>'.$stichwort['beschriftung'].'</option>');
    }
    if(empty($bilderfassen)){
      $this->app->Tpl->Set('BILDERFASSENSTART','<!--');
      $this->app->Tpl->Set('BILDERFASSENENDE','-->');
    }

    if($standardbild == '') {
      $this->app->Tpl->Set('SHOWIMGSTART','<!--');
      $this->app->Tpl->Set('SHOWIMGEND','-->');
    }

    $mhdFrm = (string)$this->app->Secure->GetGET('mhd');
    $chargeFrm = (string)$this->app->Secure->GetGET('charge');
    if(strlen($mhdFrm) > 10) {
      $chargeFrm = ltrim(substr($mhdFrm, 10),'-');
      $mhdFrm = substr($mhdFrm, 0, 10);
    }

    if($chargenverwaltung !='2' && $chargenverwaltung != '1') {
      $this->app->Tpl->Set('SHOWCHRSTART','<!--');
      $this->app->Tpl->Set('SHOWCHREND','-->');
    }
    else{
      $this->app->Tpl->Set('CHARGEFRM', $chargeFrm);
    }


    if($mindesthaltbarkeitsdatum !='1') {
      $this->app->Tpl->Set('SHOWMHDSTART','<!--');
      $this->app->Tpl->Set('SHOWMHDEND','-->');
    }
    else {
      $this->app->YUI->DatePicker('mhd');
      $this->app->Tpl->Set('MHDFRM', $mhdFrm);
    }

      $this->app->Tpl->Set('SHOWSRNSTART','<!--');
      $this->app->Tpl->Set('SHOWSRNEND','-->');
    if($standardlager <=0){
      $this->app->Tpl->Set('STANDARDLAGER', 'nicht definiert');
    }
    if(!empty($retarr) && !empty($retarr['default_storagelocation'])) {
      $retarr['default_storagelocation'] = $this->app->DB->Select(
        sprintf(
          "SELECT kurzbezeichnung FROM lager_platz WHERE geloescht <> 1 AND id = %d and kurzbezeichnung <> ''",
          $retarr['default_storagelocation']
        )
      );
    }
    if(!empty($retarr) && !empty($retarr['default_storagelocation'])) {
      $this->app->Tpl->Set('LAGERPLACEHOLDER',' placeholder="'.
             $retarr['default_storagelocation']
        .'" ');
    }
    elseif($standardlager <=0) {
      if($ismobile || $this->app->erp->Firmendaten('wareneingang_zwischenlager')=='1') {
        $this->app->Tpl->Set('LAGERPLACEHOLDER',' placeholder="zwischenlager" ');
      }
      //$this->app->Tpl->Set('LAGER',$this->app->erp->GetSelectAsso($this->app->erp->GetLager(false, $ismobile),$lager));
    }
    else {
      $this->app->Tpl->Set('STANDARDLAGER',$this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='".$standardlager."' LIMIT 1"));
      if($ismobile || $this->app->erp->Firmendaten('wareneingang_zwischenlager')=='1') {
        $this->app->Tpl->Set('LAGERPLACEHOLDER',' placeholder="zwischenlager" ');
      }
      else {
        $this->app->Tpl->Set('LAGERPLACEHOLDER',' placeholder="Standardlager" ');
      }
      //$this->app->Tpl->Set('LAGER',$this->app->erp->GetSelectAsso($this->app->erp->GetLager(true, $ismobile),$lager));
    }

    if($submit=='') {
      $tmpauswahl = $this->app->User->GetParameter('wareneingang_distrietiketten_etiketten'); 
      if($tmpauswahl=='') {
        $tmpauswahl='artikel_klein';
      }
      $this->app->Tpl->Set('ETIKETTEN',$this->app->erp->GetSelectAsso($this->app->erp->GetEtikett(),$tmpauswahl));
    }
    else {
      $this->app->Tpl->Set('ETIKETTEN', $this->app->erp->GetSelectAsso($this->app->erp->GetEtikett(), $etiketten));
    }

    $this->app->Tpl->Set('MENGE',$menge);

    if($this->app->erp->Firmendaten('standardetikettendrucker')>0) {
      $this->app->Tpl->Set('ETIKETTENDRUCKEN','Etiketten drucken.');
      $this->app->Tpl->Set('ANZAHL',0);
      $this->app->Tpl->Set('TEXTBUTTON','Artikel einlagern');
    }
    else {
      $this->app->Tpl->Set('SHOWANZAHLSTART','<!--');
      $this->app->Tpl->Set('SHOWANZAHLENDE','-->');

      $this->app->Tpl->Set('ETIKETTENDRUCKENSTART','<!--');
      $this->app->Tpl->Set('ETIKETTENDRUCKENENDE','-->');
      $this->app->Tpl->Set('TEXTBUTTON','Artikel einlagern');
      $this->app->Tpl->Set('ANZAHL',0);
      $this->app->Tpl->Set('ANZAHLCHECKED','checked');
    }

    $this->app->Tpl->Set('LIEFERANT',$name);
    $this->app->Tpl->Set('MITARBEITER',$mitarbeiter_name);
    $this->app->Tpl->Set('VPE',$vpe);
    $this->app->Tpl->Set('NAME',$name_de);
    $this->app->Tpl->Set('NUMMER',$nummer);
    $this->app->Tpl->Set('DATEI',$standardbild);

    $error = 0;
    // Pflichfelder pruefen
    if($mindesthaltbarkeitsdatum=='1' && $this->app->Secure->GetPOST('mhd')=='') {
      $error++;
    }

    if(($chargenverwaltung=='2' || $chargenverwaltung=='1') && $this->app->Secure->GetPOST('charge')=='') {
      $error++;
    }
    //vomprodukteinlagern hinzugefuegt 08.01.20 LG OS904009
    if(($seriennummern !=='keine' && $seriennummern !=='vomprodukt' && $seriennummern !=='eigene' && $seriennummern != 'vomprodukteinlagern' && $seriennummern !='') && $rma!=='rma') {
      $tmpcheck = $this->app->Secure->GetPOST('seriennummern');
      for($checkser=0;$checkser < $menge; $checkser++) {
        if($tmpcheck[$checkser]==''){
          $error++;
        }
      }
    }

    if($rma==='rma') {
      $error=0;
    }

    if($submit!='' && $error > 0) {
      $this->app->Tpl->Add('MESSAGE','<div class="error">Achtung! Bitte alle Pflichtfelder ausf&uuml;llen!</div>');
    }
    // ende pflichtfelder pruefung

    //    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=distriinhalt&id=$id","zum Paketinhalt");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=manuellerfassen&id=$id","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=distrietiketten&id=$id","Artikel",true);

    $typ = '';
    //weiter mit paket bis fertig

    if($rma==='rma') {
    }
    else {
      if($lagerartikel && !$artikelcheckliste && !$funktionstest && !$endmontage) {
        $this->app->erp->RunHook('wareneingang_display_hook_rma1', 3, $id, $pos, $menge);
        $typ = 'lager';
        $this->app->Tpl->Set('ANZAHLAENDERN',"<input type=\"button\" value=\"&auml;ndern\" onclick=\"var menge =  prompt('Neue Menge:',$menge); if(menge > 0) window.location.href=document.URL + '&nmenge=' + menge;\">");
        //$this->app->Tpl->Add(TAB1TEXT,"<li><a>Lagerartikel</a></li>");

        $this->app->YUI->AutoComplete('lager','lagerplatz',0,'&zwischenlager='.$withZwischenlager.'&withstandardlager='.$withStandardlager);
        $this->app->Tpl->Parse('TAB1','wareneingangpaketdistribution_tab3_lager.tpl');
      }
      else if($artikelcheckliste || $funktionstest || $endmontage) {
      }
      else if (!$lagerartikel && !$artikelcheckliste && !$funktionstest && !$endmontage)
      {
        $typ = 'mitarbeiter';
        $this->app->erp->RunHook('wareneingang_display_hook_rma1', 3, $id, $pos, $menge);
        $this->app->Tpl->Add('TAB1TEXT','<li><a>Artikel f&uuml;r Mitarbeiter</a></li>');
        $this->app->Tpl->Parse('TAB1','wareneingangpaketdistribution_tab3_mitarbeiter.tpl');
      }
      else {
        echo 'Diesen Fall gibt es nicht. Xentral Entwicklung kontaktieren!';
      }
    }

    if($typ === 'lager' && $submit!='' && $error==0) {
      if(empty($lager) && !empty($retarr) && !empty($retarr['default_storagelocation'])) {
        $lager =  $retarr['default_storagelocation'];
      }
      elseif(empty($lager)) {
        if($standardlager <=0) {
          if($ismobile || $this->app->erp->Firmendaten('wareneingang_zwischenlager')=='1') {
            $lager = 'zwischenlager';
          }
        }
        else {
          if($ismobile || $this->app->erp->Firmendaten('wareneingang_zwischenlager')=='1') {
            $lager = 'zwischenlager';
          }
          else {
            $lager = 'standardlager';
          }
        }
      }
      if(empty($lager)) {
        $error++;
      }
      else {
        switch(strtolower($lager)) {
          case 'zwischenlager':
            $lager = 'zwischenlager';
            if(!$withZwischenlager) {
              $error++;
              $this->app->Tpl->Add('MESSAGE1','<div class="error">Wareneingang mit Zwischenlager ist nicht aktiviert</div>');
            }
            break;
          case 'standardlager':
            $lager = $this->app->DB->Select(
              sprintf(
                'SELECT lager_platz FROM artikel WHERE id = %d',
                $artikel
              )
            );
            $lager = empty($lager)?null: $this->app->DB->Select(
              sprintf(
                'SELECT id FROM lager_platz WHERE id = %d AND geloescht <> 1',
                $lager
              )
            );
            if(empty($lager)) {
              $error++;
              $this->app->Tpl->Add('MESSAGE1','<div class="error">Der Artikel hat kein Standardlager</div>');
            }
            break;
          default:
            $frmLager = $lager;
            $lager = $this->app->DB->Select(
              sprintf(
                "SELECT id 
                FROM lager_platz 
                WHERE kurzbezeichnung <> '' AND kurzbezeichnung = '%s' AND geloescht <> 1",
                $frmLager
              )
            );
            if(empty($lager) && is_numeric($frmLager)) {
              $lager = $this->app->DB->Select(
                sprintf(
                  "SELECT id 
                FROM lager_platz 
                WHERE kurzbezeichnung <> '' AND id = %d AND geloescht <> 1",
                  $frmLager
                )
              );
            }
            if(empty($lager)) {
              $error++;
              $this->app->Tpl->Add(
                'MESSAGE1',
                '<div class="error">Lagerplatz '.$frmLager.' wurde nicht gefunden</div>'
              );
            }
            break;
        }
      }
    }

    //befehl ab ins lager, produktion oder mitarbeiter
    if($submit!='' && $error==0) {
      if (!empty($_FILES['wareneingangartikelbild']['tmp_name'])) {
        $stichwort = $this->app->Secure->GetPOST('bildtyp');
        $bildTitel = '';
        $bildBeschreibung = '';
        $chargeNummer = $this->app->Secure->GetPOST('charge');
        $bildName = $_FILES['wareneingangartikelbild']['name'];
        $this->app->erp->RunHook('wareneingang_chargenbildspeichern', 5, $nummer, $chargeNummer, $bildName, $bildTitel, $bildBeschreibung);
        $fileid = $this->app->erp->CreateDatei($bildName, $bildTitel, $bildBeschreibung, '', $_FILES['wareneingangartikelbild']['tmp_name'], $this->app->User->GetName());
        // stichwoerter hinzufuegen
        if(!empty($this->app->Secure->GetPOST('charge'))){
          $this->app->erp->AddDateiStichwort($fileid, $stichwort, 'Artikel', $artikel,false, $this->app->Secure->GetPOST('charge'),'Charge');
        }else if(!empty($this->app->Secure->GetPOST('mhd'))){
          $dstype = 'MHD';
          try {
            $mhddate = date_create_from_format('d.m.Y', $this->app->Secure->GetPOST('mhd'))->getTimestamp();
          }catch (Exception $ex){
            $mhddate='';
          }
          if($mhddate===''){
            $dstype='';
          }
          $this->app->erp->AddDateiStichwort($fileid, $stichwort, 'Artikel', $artikel,false, $mhddate,$dstype);
        }else{
          $this->app->erp->AddDateiStichwort($fileid, $stichwort, 'Artikel', $artikel);
        }
      }

      switch($typ) {
        case 'lager':
          if($anzahlauswahl==='fix') {
            $druckanzahl = $anzahl_fix;
          }
          else {
            $druckanzahl = $anzahl_dyn;
          }
          $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");                     
          $name_de = base64_encode($name_de);  

          $this->app->erp->LagerArtikelZusammenfassen($artikel);

          //$etiketten AUSWAHL etiketten ob gross oder klein
          if($this->app->erp->Firmendaten('standardetikettendrucker')>0) {
            if($druckanzahl>0) {
              $data['charge']=$this->app->Secure->GetPOST("charge");

              // falls BE für Auftrag
              if(!empty($retoure)) {
                $auftragid = $this->app->DB->Select(
                  "SELECT a.id FROM retoure_position bp 
                 LEFT JOIN auftrag_position ap ON bp.auftrag_position_id=ap.id 
                LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE bp.retoure='$retoure' LIMIT 1");

                $data['retoure'] = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$retoure' LIMIT 1");

              }
              else{
                $auftragid = $this->app->DB->Select("SELECT a.id FROM bestellung_position bp LEFT JOIN auftrag_position ap ON bp.auftrag_position_id=ap.id 
                LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE bp.bestellung='$bestellung' LIMIT 1");

                $data['bestellung'] = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$bestellung' LIMIT 1");
              }

              if($auftragid>0) {
                $belegnummer = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
                $data['belegnummer']=$belegnummer;
                $data['auftragsnummer']=$belegnummer;
                $data['auftrag']=$belegnummer;
        
                $adresse = $this->app->DB->Select("SELECT adresse FROM auftrag WHERE id='$auftragid' LIMIT 1"); 
                if($adresse > 0) {
                  $adressearr = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='$adresse' LIMIT 1");
                  foreach($adressearr as $key=>$value) {
                    $data['adresse_'.$key]=$value;
                  }
                }
              }
              $data['etiketten_menge']=$druckanzahl;
              $data['menge']=$anzahl_fix;
              $data['charge']=$this->app->Secure->GetPOST('charge');
              $data['mhd2']=$this->app->Secure->GetPOST('mhd');

              if($lager>0){
                $data['lager_platz_einlagern'] = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='" . $lager . "' LIMIT 1");
              }

              //$data['seriennummer']="";
              $tmpdataseriennummern=$this->app->Secure->GetPOST('seriennummern');

              if($etiketten=='') {
                $etiketten='artikel_klein';
              }
              if(!empty($tmpdataseriennummern) && is_array($tmpdataseriennummern) && count($tmpdataseriennummern)>0) {
                if($etiketten!=='keineetiketten') {
                  foreach($tmpdataseriennummern as $srn)  {
                    $data['seriennummer']=$srn;
                    $druckanzahl = 1;
                    $this->app->erp->EtikettenDrucker($etiketten,$druckanzahl,'artikel',$artikel,$data);
                  }
                }
              }
              else {
                if($etiketten!=='keineetiketten'){
                  $this->app->erp->EtikettenDrucker($etiketten, $druckanzahl, 'artikel', $artikel, $data);
                }
              }
              $this->app->User->SetParameter('wareneingang_distrietiketten_etiketten',$etiketten);
            }
          }

          $zid = 0;
          if($chargenverwaltung=='2' || $chargenverwaltung=='1') {
            $charge = $this->app->Secure->GetPOST('charge');
            $chargemindest = $charge;	
          }
          else {
            $ch = 0;
          }
          $doctype = !empty($retoure)?'retoure':'';
          $doctypeId = !empty($retoure)?$retoure:0;
          if(empty($doctypeId)) {
            $doctypeId = !empty($bestellung)?$bestellung:$id;
          }
          if($doctype === '') {
            $doctype =!empty($bestellung)?'bestellung':'paketannahme';
          }
          // entweder ins zwischenlager 
          if(strtolower($lager)==='zwischenlager') {
            if(!empty($retoure)) {
              $this->app->DB->Insert("INSERT INTO zwischenlager (id,bearbeiter,projekt,artikel,menge,vpe,grund,lager_von,richtung,objekt,parameter,firma,paketannahme)
                VALUES ('','" . $this->app->User->GetName() . "','$projekt','$artikel','$menge','$vpe','Wareneingang von Retoure $retoure_belegnr','Wareneingang','Eingang',
                  'Retoure','$retoure','" . $this->app->User->GetFirma() . "','$id')");
            }
            else{
              $this->app->DB->Insert("INSERT INTO zwischenlager (id,bearbeiter,projekt,artikel,menge,vpe,grund,lager_von,richtung,objekt,parameter,firma,paketannahme)
                VALUES ('','" . $this->app->User->GetName() . "','$projekt','$artikel','$menge','$vpe','Wareneingang von Bestellung $bestellung_belegnr','Wareneingang','Eingang',
                  'Bestellung','$bestellung','" . $this->app->User->GetFirma() . "','$id')");
            }
            $typ = 'zwischenlager';
            $zid = $this->app->DB->GetInsertID();
            $typ2 = 'zwischenlager';
            $tmpid2 = $zid;
          }
          // oder direkt ins manuelle (lagerplatz + lager_bewegung)
          else {
            if(strtolower($lager)==='standardlager'){
              $lager = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='" . $artikel . "' LIMIT 1");
            }

            if($lager<=0){
              $lager = $this->app->DB->Select("SELECT id FROM lager_platz WHERE autolagersperre!=1 AND verbrauchslager!=1 AND geloescht!=1 LIMIT 1");
            }

            if($lager==='zwischenlager'){
              $lagerplatz = 0;
            }
            else{
              $lagerplatz = $lager;
            }

            /*
            //$charge = $this->app->Secure->GetPOST("charge");
            /*if(false && $chargenverwaltung=="1")
            {
              // wenn chargenverwaltung dann chargen id holen!!!! und mit bei lagerung und etikett speichern!
              $this->app->DB->Insert("INSERT INTO chargenverwaltung (id,artikel,bestellung,menge,vpe,zeit,bearbeiter) 
                  VALUES ('','$artikel','$bestellung','$menge','$vpe',NOW(),'".$this->app->User->GetName()."')");
              // drucken (inkl. chargennummer)
              $ch = $this->app->DB->GetInsertID();
              $chargemindest = $ch;
            } else*/

            if(!empty($retoure)) {
              $url = null;
              $this->app->erp->RunHook(
                'wareneingang_before_stock_in_return_order', 7, $id, $retoure, $retourepos, $artikel, $menge, $lager, $url
              );
              if(!empty($url)) {
                $this->app->Location->execute($url);
              }
              $this->app->erp->LagerEinlagern(
                $artikel, $menge, $lager, '', 'Wareneingang von Retoure '.$retoure_belegnr, '', $id,'retoure',$retoure
              );
            }
            else {
              if($cmd === 'manuell') {
                $this->app->erp->LagerEinlagern($artikel, $menge, $lager, '', 'Wareneingang manuell', '', $id);
              }
              else{
                $this->app->erp->LagerEinlagern($artikel, $menge, $lager, '', "Wareneingang von Bestellung $bestellung_belegnr", '', $id);
                $this->app->erp->RunHook('wareneingang_bestellung', 5, $bparr, $artikel, $menge, $lager, $id);
              }
            }
            $typ2 = 'lager_platz';
            $tmpid2 = array('artikel'=>$artikel,'lager_platz'=>$lager,'menge'=>$menge);
          }

          $_adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id = '$id' LIMIT 1");
          $this->app->erp->RunHook('wareneingang_lager_submit', 8, $_adresse, $artikel, $menge,$lagerplatz,$mhd,$chargemindest, $tmpcheck, $weiterleitung);


          $this->app->erp->RunHook('wareneingang_after', 3, $id, $typ2, $tmpid2);
          break;
        case 'mitarbeiter':
          // buchen als mitarbeiter inventar auf das projekt was angegeben ist
          // wenn mitarbeiterartikel muss artikel als inventar dem mitarbeiter gebucht werden fuer projekt bla bla
          $this->app->DB->Insert("INSERT INTO projekt_inventar (id,artikel,menge,projekt,mitarbeiter,bestellung,zeit,vpe)
              VALUES('','$artikel','$menge','$projekt','$mitarbeiter','$bestellung',NOW(),'$vpe')");
          $projekt_inventar_id = $this->app->DB->GetInsertID();
          $this->app->erp->RunHook('wareneingang_after', 3, $id, $typ, $projekt_inventar_id);
          break;
          //$this->app->erp->RunHook('wareneingang_mitarbeiter_submit', 6, $mitarbeiter, $bestellung, $projekt, $artikel, $menge, $weiterleitung);

        default:
          echo 'ACHTUNG DAS DARF NICHT PASSIEREN!! XENTRAL ENTWICKLUNG HOLEN! FEHLER IM PROGRAMM?';
      }

      if($typ!=='rma' && $cmd !== 'manuell') {
       
        // Distribution speichern!
        $this->app->DB->Insert("INSERT INTO paketdistribution 
    (id,bearbeiter,zeit,paketannahme,adresse,artikel,menge,vpe,etiketten,bemerkung,bestellung_position)
            VALUES ('','".$this->app->User->GetName()."',NOW(),'$id','$adresse','$artikel','$menge','$vpe','$etiketten','$bemerkung','$pos')");
        $pdId = $this->app->DB->GetInsertID();
        if(!empty($retourepos)) {
          $this->app->DB->Update(
            sprintf(
              'UPDATE paketdistribution SET bestellung_position = 0, retoure_position = %d WHERE id = %d',
              $retourepos, $pdId
            )
          );
        }

        // anzahl gelieferte erhoehen bestellung_position !!!
        if(!empty($retourepos)) {
          $geliefert = $this->app->DB->Select("SELECT ifnull(menge_eingang,0) FROM retoure_position WHERE id='$retourepos' LIMIT 1");
          //$gesamt_erwartet = $this->app->DB->Select("SELECT menge FROM bestellung_position WHERE id='$pos' LIMIT 1");
          $geliefert += $menge;
          $this->app->DB->Update("UPDATE retoure_position SET menge_eingang='$geliefert' WHERE id='$retourepos' LIMIT 1");
          $this->app->DB->Update(
            sprintf(
              "UPDATE retoure 
              SET fortschritt = 'eingegangen' 
              WHERE id = %d AND IFNULL(fortschritt,'') IN ('angekuenigt','angekuendigt','') ",
              $retoure
            )
          );
        }
        else{
          $geliefert = $this->app->DB->Select("SELECT ifnull(geliefert,0) FROM bestellung_position WHERE id='$pos' LIMIT 1");
          //$gesamt_erwartet = $this->app->DB->Select("SELECT menge FROM bestellung_position WHERE id='$pos' LIMIT 1");
          $geliefert += $menge;
          $this->app->DB->Update("UPDATE bestellung_position SET geliefert='$geliefert' WHERE id='$pos' LIMIT 1");
        }
      }
      // alles passt weiter im abschluss
      if($weiterleitung) {
        $this->app->Location->execute($weiterleitung);
      }
      else {
        if(!empty($backaction) && $backaction === 'paketannahme') {
          $this->app->Location->execute('index.php?module=wareneingang&action='.$backaction);
        }
        if(!empty($retoure) && !empty($retourepos)) {
          $all = $this->app->YUI->TableSearch('', 'paketannahme_retoure','ALL','','',basename(__FILE__),__CLASS__);
          $sorts = $this->app->DB->SelectPairs(
            sprintf(
              "SELECT id,sort FROM retoure_position WHERE retoure = %d",
              $retoure
            )
          );
          $actSort = !empty($sorts[$retourepos])?$sorts[$retourepos]:0;
          $positions = $this->app->DB->SelectArr(
            $all['sql'].' WHERE '.$all['where']
            . sprintf(' ORDER BY bp.menge -  bp.menge_eingang <= 0, 
            bp.menge_eingang <> 0, 
            bp.id = %d, 
            bp.sort <= %d ', $retourepos, $actSort)
          );

          if(!$this->app->DB->Select(
            sprintf(
              "SELECT `id` FROM `retoure_position` WHERE `retoure` = %d AND `menge` > `menge_eingang`",
              $retoure
            )
          )
          ) {
            $positions = null;
          }

          if(!empty($positions)) {
            foreach($positions as $position) {
              $position['eingang'] = str_replace(',','.', $position['eingang']);
              $position['offen'] = str_replace(',','.', $position['offen']);
              if($position['offen'] > 0) {
                $returnOrderId = (int)$this->app->DB->Select(
                  sprintf(
                    'SELECT `retoure` FROM `retoure_position` WHERE `id` = %d',
                    $position['id']
                  )
                );
                if(!empty($doctypeId) && (int)$doctypeId !== $returnOrderId) {
                  continue;
                }
                if($returnOrderId <= 0 || $this->app->User->GetParameter('wareneingang_from_type') === 'manual') {
                  $rop = null;
                }
                else{
                  $rop = $this->getNextReturnOrderPosition($returnOrderId);
                }
                if(!empty($rop) && $rop['id'] == $position['id']) {
                  $loc = $this->getNextReturnOrderLocation($returnOrderId, $id);
                  if(!empty($loc)) {
                    $this->app->Location->execute($loc);
                  }
                }
                if($this->app->User->GetParameter('wareneingang_from_type') === 'manual') {
                  $this->app->Location->execute(
                    'index.php?module=wareneingang&action=distriinhalt&cmd=fromreturnordermanual&id='.$id
                  );
                }

                $this->app->Location->execute(
                  'index.php?module=wareneingang&action=distrietiketten&id='
                  .$id.'&retourepos='
                  .$position['id'].'&menge='.$position['offen']
                  .'&seriennummer=&nmenge='.$position['offen']
                );
              }
            }
          }
          $pos = $retourepos;
        }
        $receiptDocument = $this->app->DB->SelectRow(
          sprintf(
            'SELECT `id`, `status` FROM `receiptdocument` WHERE `return_order_id` = %d LIMIT 1', empty($returnOrderId)?$retoure:$returnOrderId
          )
        );
        $receiptDocumentId = empty($receiptDocument)?0:(int)$receiptDocument['id'];
        $returnOrderComplete = !empty($retoure) && !empty($retourepos) && $positions === null;
        if(!empty($receiptDocumentId)
          && ($returnOrderComplete || in_array($receiptDocument['status'], ['fertiggestellt', 'abgeschlossen']))
        ) {
          $url = '';
          $this->app->erp->RunHook('receiptdocument_close', 2, $receiptDocumentId, $url);
          if(!empty($url) && $url !== 'index.php') {
            $this->app->Location->execute(
              "index.php?module=wareneingang&action=distriabschluss&id=$id&pos=$pos&typ=$typ&rma=$rma"
              .(!empty($doctype)?'&doctype='.$doctype:'').'&locateto='.urlencode($url)
            );
          }
        }

        $this->app->Location->execute(
          "index.php?module=wareneingang&action=distriabschluss&id=$id&pos=$pos&typ=$typ&rma=$rma"
          .(!empty($doctype)?'&doctype='.$doctype:'')
        );
      }
    }

    $this->app->Tpl->Set('AKTIV_TAB2','tabs-1');
    $this->app->Tpl->Parse('PAGE','wareneingang_distrietiketten.tpl');
  }


  public function WareneingangPaketDistriAbschluss()
  {
    $id = $this->app->Secure->GetGET('id');
    $doctype = $this->app->Secure->GetGET('doctype');
    $pos = $this->app->Secure->GetGET('pos');
    $locateTo = $this->app->Secure->GetGET('locateto');
    $fromDoctype = '';
    if($doctype === 'bestellung') {
      $fromDoctype = '&from=bestellung';
    }
    elseif($doctype === 'retoure') {
      $fromDoctype = '&from=retoure';

      if($this->app->User->GetParameter('wareneingang_from') === 'retoure') {
        $all = $this->app->YUI->TableSearch(
          'TAB1', 'paketannahme_retoure', 'ALL', '', '', basename(__FILE__), __CLASS__
        );
        if(!$this->app->DB->Select($all['sql'].' WHERE '.$all['where']. ' LIMIT 1')) {
          $this->app->User->SetParameter('wareneingang_from', '');
          $retoure = $this->app->DB->Select(sprintf('SELECT `retoure` FROM `retoure_position` WHERE `id` = %d', $pos));
          $userReturnOrder = $this->app->User->GetParameter('wareneingang_from_returnorder');
          $module = $this->app->User->GetParameter('wareneingang_from_module');
          $action = $this->app->User->GetParameter('wareneingang_from_action');

          if($retoure == $userReturnOrder && !empty($module) && !empty($action)) {
            $this->app->User->SetParameter('wareneingang_from_returnorder', '');
            $this->app->User->SetParameter('wareneingang_from_module', '');
            $this->app->User->SetParameter('wareneingang_from_action', '');
            if($module !== 'retoure') {
              $retoure = '';
            }
            if(!empty($locateTo)) {
              if($this->app->Location->getLocationUrl($locateTo) !== 'index.php'){
                $this->app->Location->execute($locateTo);
              }
            }
            $this->app->Location->execute('index.php?module='.$module.'&action='.$action.'&id='.$retoure);
          }
          if(!empty($locateTo)) {
            if($this->app->Location->getLocationUrl($locateTo) !== 'index.php') {
              $this->app->Location->execute($locateTo);
            }
          }
          $this->app->Location->execute('index.php?module=retoure&action=list');
        }
      }
    }
    if(empty($pos) && empty($doctype) && $this->app->erp->Firmendaten('wareneingang_gross')=='1'){
      $this->app->DB->Update(
        sprintf(
          "UPDATE `paketannahme` SET `status` = 'abgeschlossen' WHERE `id` = %d LIMIT 1",
          $id
        )
      );
      $this->app->Location->execute('index.php?module=wareneingang&action=distribution');
    }
    if(!empty($locateTo)) {
      if($this->app->Location->getLocationUrl($locateTo) !== 'index.php'){
        $this->app->Location->execute($locateTo);
      }
    }
    $this->app->Location->execute('index.php?module=wareneingang&action=distriinhalt&id='.$id.$fromDoctype);
  }



  
  public function WareneingangPaketZustand()
  {
    $this->WareneingangPaketMenu();
    $id = $this->app->Secure->GetGET('id');
    $submit = $this->app->Secure->GetPOST('submit');
    if($submit!='')
    {
      $this->app->FormHandler->FormUpdateDatabase('paketannahme',$id);

      $this->app->Location->execute('index.php?module=wareneingang&action=paketetikett&id='.$id);
    }
    //$client = new HttpClient("192.168.0.171");
    $wareneingang_kamera_waage = $this->app->erp->Firmendaten('wareneingang_kamera_waage');//$this->app->DB->Select("SELECT wareneingang_kamera_waage FROM firmendaten LIMIT 1");

    if($wareneingang_kamera_waage == '1')
    {
      $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE verwendenals='kamera' OR verwendenals='waage' LIMIT 1");
      $pageContent = $this->app->erp->GetAdapterboxAPIWaage($seriennummer);
    }

    $gewicht = $pageContent;

    //$gewicht = intval($gewicht)-2;

    if($wareneingang_kamera_waage =='1'){
      $this->app->Tpl->Set('GEWICHT', $gewicht);
    }
    else{
      $this->app->Tpl->Set('GEWICHT','none');
    }


    if($wareneingang_kamera_waage == '1'){
      //$datei = HttpClient::quickGet("http://192.168.0.53/snap.jpg");
      $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM adapterbox WHERE verwendenals='kamera' LIMIT 1");
      $datei = $this->app->erp->GetAdapterboxAPIImage($seriennummer,'800','600');

      $tmpname = tempnam($this->app->erp->GetTMP(),'wareneingang').'.jpg';
      file_put_contents($tmpname, $datei);
  
      $ersteller = $this->app->User->GetName();
      $file = $this->app->erp->CreateDatei(date('Ymd')."_paketannahme_$id.jpg","Paketannahme $id",'','',$tmpname,$ersteller);

      unlink($tmpname);

      $this->app->Tpl->Set('FOTO',$file);

      $this->app->erp->AddDateiStichwort($file,'Bild','Paketannahme',$id);

      $this->app->Tpl->Set('LIVEFOTO','<img src="index.php?module=dateien&action=send&id='.$file.'" width="400">');
  }

  if($gewicht <= 0 && $wareneingang_kamera_waage==1){
    $this->app->Tpl->Set('MELDUNG', '<div class="error">Bitte legen Sie das Paket auf die Waage und schie&szlig;en Sie nochmal ein Foto!</div>');
  }
  else if ($gewicht <= 0 && $wareneingang_kamera_waage !=1){
    $this->app->Tpl->Set('MELDUNG', '<div class="info">Status: Ohne Waage und Kamera Funktion</div>');
  }


  if($this->app->erp->Firmendaten('wareneingang_gross')=='1')
  {
    $this->app->Tpl->Parse('TAB1','wareneingangpaketannahme_tab3.tpl');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }
  else{
    $this->app->Tpl->Parse('PAGE', 'wareneingangpaketannahme.tpl');
  }
  $this->app->Tpl->Set('AKTIV_TAB3','tabs-1');
}


public function WareneingangPaketEtikett()
{
  $this->WareneingangPaketMenu();
  $id = $this->app->Secure->GetGET('id');
  $submit = $this->app->Secure->GetPOST('submit');
  if($submit!=''){
    header('Location: index.php?module=wareneingang&action=paketabschliessen&id='.$id);
  }

  if($this->app->erp->Firmendaten('wareneingang_gross')=='1')
  {
    $this->app->Tpl->Parse('TAB1','wareneingangpaketannahme_tab4.tpl');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
    $weiteres= $this->app->Secure->GetPOST('weiteres');
    $abschluss= $this->app->Secure->GetPOST('abschluss');
  
    if($weiteres!=''){
      header('Location: index.php?module=wareneingang&action=paketannahme');
    }
    if($abschluss!='')
    {
        header('Location: index.php?module=wareneingang&action=paketannahme');

    }

  }
  else{
    $this->app->Tpl->Parse('PAGE', 'wareneingangpaketannahme.tpl');
  }

}

public function WareneingangPaketAbschliessen()
{
  $this->WareneingangPaketMenu();
  $id = $this->app->Secure->GetGET('id');
  $weiteres= $this->app->Secure->GetPOST('weiteres');
  $abschluss= $this->app->Secure->GetPOST('abschluss');
  
  if($weiteres!=''){
    $this->app->Location->execute('index.php?module=wareneingang&action=paketannahme');
  }
  if($abschluss!='')
  {
      $this->app->Location->execute('index.php?module=wareneingang&action=paketannahme');
    
  }


  if($this->app->erp->Firmendaten('wareneingang_gross')=='1')
  { 
    $this->app->Tpl->Parse('TAB1','wareneingangpaketannahme_tab5.tpl');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }
  else{
    $this->app->Tpl->Parse('PAGE', 'wareneingangpaketannahme.tpl');
  }
}




public function WareneingangList()
{
  $this->WareneingangMenu();

  $this->app->Tpl->Set('SUBHEADING','Lieferungen');
  //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
  $table = new EasyTable($this->app);
  $table->Query("SELECT '23.11.2009' as datum, 'Olimex' as lieferant,id FROM aufgabe LIMIT 3");
  $table->DisplayNew('INHALT','<a href="index.php?module=ticket&action=assistent&id=%value%">Lesen</a>');
  $this->app->Tpl->Parse('TAB1','rahmen.tpl');
  $this->app->Tpl->Set('INHALT','');

  $this->app->Tpl->Set('AKTIV_TAB1','tabs-1');
  $this->app->Tpl->Parse('PAGE','wareneinganguebersicht.tpl');
}

  /**
   * @param int $addressId
   *
   * @return int
   */
  public function createParcelReceiptFromAdress($addressId)
  {
    $bearbeiter = empty($this->app->User) || !method_exists($this->app->User,'GetName') ?'Cronjob':
      $this->app->DB->real_escape_string($this->app->User->GetName());
    $projectId = (int)$this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$addressId' AND geloescht=0 LIMIT 1");
    $sql = sprintf(
      "INSERT INTO paketannahme (datum,adresse,vorlage,vorlageid,projekt,bearbeiter,status) VALUES
        (NOW(),%d,'adresse',%d,%d,'%s','angenommen')",
      $addressId, $addressId, $projectId, $bearbeiter
    );
    $this->app->DB->Insert($sql);

    return (int)$this->app->DB->GetInsertID();
  }

public function WareneingangPaketannahme()
{
  $this->WareneingangPaketMenu();
  $vorlage= $this->app->Secure->GetGET('vorlage');
  $suche= $this->app->Secure->GetPOST('suche');
  $id = $this->app->Secure->GetGET('id');

  $articlescan = $this->app->Secure->GetPOST('articlescan');
  if($articlescan) {
    $best = $this->app->DB->SelectArr(
      sprintf(
        'SELECT bp.bestellung,MIN(bp.id) as pos, b.adresse, bp.artikel, b.projekt, art.lager_platz
          FROM bestellung AS b
          INNER JOIN bestellung_position AS bp ON b.id = bp.bestellung AND bp.geliefert < bp.menge AND b.belegnr <> \'\' AND 
           (bp.abgeschlossen IS NULL OR bp.abgeschlossen=0)  AND (b.status=\'versendet\' OR b.status=\'freigegeben\')
          INNER JOIN artikel AS art ON bp.artikel = art.id
          LEFT JOIN `artikelnummer_fremdnummern` AS af ON art.id = af.artikel AND af.aktiv = 1 AND af.scannable = 1
          WHERE art.nummer = \'%s\' OR art.ean = \'%s\' OR art.herstellernummer = \'%s\' OR af.nummer = \'%s\' 
          GROUP BY b.id, art.id ',
        $articlescan, $articlescan, $articlescan, $articlescan
      )
    );
    $vorlage = '';
    if(empty($best)) {
      if($this->app->erp->ModulVorhanden('retoure')) {
        $retoure = $this->app->DB->SelectRow(
          sprintf(
            "SELECT ro.id, ro.adresse 
            FROM retoure AS ro 
            INNER JOIN retoure_position AS rp on ro.id = rp.retoure AND rp.menge_eingang < rp.menge 
                   AND ro.status <> 'storniert' AND ro.belegnr <> ''  AND ro.adresse > 0
            WHERE ro.belegnr = '%s' 
            LIMIT 1",
            $articlescan
          )
        );
        if(empty($retoure)) {
          $retoure = $this->app->DB->SelectRow(
            sprintf(
              "SELECT ro.id , ro.adresse
              FROM retoure AS ro
              INNER JOIN retoure_position AS rp on ro.id = rp.retoure AND rp.menge_eingang < rp.menge 
                   AND ro.status <> 'storniert' AND ro.belegnr <> '' AND ro.adresse > 0
              INNER JOIN lieferschein AS dn ON ro.lieferscheinid = dn.id
              WHERE dn.belegnr = '%s' AND dn.belegnr <> '' LIMIT 1",
              $articlescan
            )
          );
        }
        if(empty($retoure)) {
          $retoure = $this->app->DB->SelectRow(
            sprintf(
              "SELECT ro.id, ro.adresse 
              FROM retoure AS ro
              INNER JOIN retoure_position AS rp on ro.id = rp.retoure AND rp.menge_eingang < rp.menge 
                   AND ro.status <> 'storniert' AND ro.belegnr <> '' AND ro.adresse > 0
              INNER JOIN auftrag AS o ON ro.auftragid = o.id
              WHERE o.belegnr = '%s' AND o.belegnr <> '' LIMIT 1",
              $articlescan
            )
          );
        }
        if(empty($retoure)) {
          $retoure = $this->app->DB->SelectRow(
            sprintf(
              "SELECT ro.id, ro.adresse
              FROM retoure AS ro
              INNER JOIN retoure_position AS rp on ro.id = rp.retoure AND rp.menge_eingang < rp.menge 
                   AND ro.status <> 'storniert' AND ro.belegnr <> '' AND ro.adresse > 0
              INNER JOIN lieferschein AS dn ON ro.lieferscheinid = dn.id
              INNER JOIN versand AS v ON dn.id = v.lieferschein
              WHERE v.tracking = '%s' AND v.tracking <> '' LIMIT 1",
              $articlescan
            )
          );
        }
        if(!empty($retoure)) {
          $this->app->User->SetParameter('wareneingang_from_returnorder', $retoure['id']);
          $this->app->User->SetParameter('wareneingang_from_module', 'wareneingang');
          $this->app->User->SetParameter('wareneingang_from_action', 'paketannahme');

          $this->app->Location->execute(
            'index.php?module=wareneingang&action=distriinhalt&cmd=createreceiptdocumentreturnorder&id='
            . $retoure['id']
          );
        }
      }
      $this->app->Tpl->Add('INFO','<div class="warning">Es wurde keine offene Bestellung mit diesem Artikel gefunden.</div>');
    }
    elseif(count($best) > 1) {
      $this->app->Tpl->Add('INFO','<div class="warning">Es wurden mehrere offene Bestellung mit diesem Artikel gefunden.</div>');
    }else{
      $vorlage = 'bestellung';
      $id = (int)$best[0]['bestellung'];
      $pos = $best[0]['pos'];
      $adresse = (int)$best[0]['adresse'];
      $projekt = (int)$best[0]['projekt'];
      $lagerplatz = (int)$best[0]['lager_platz'];
      $ismobile = $this->app->Secure->GetPOST('ismobile');
      $menge = round(
        $this->app->DB->Select(
          sprintf(
            'SELECT menge - geliefert FROM bestellung_position WHERE id = %d LIMIT 1',
            $pos
          )
        ),
        7
      );
      $this->app->User->SetParameter('wareneingang_from', 'wareneingang');
      if($this->app->erp->Firmendaten('wareneingang_gross')!='1') {
        $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());

        $sql = "INSERT INTO paketannahme (datum,adresse,vorlage,vorlageid,projekt,bearbeiter,status) VALUES
      (NOW(),$adresse,'$vorlage',$id,$projekt,'$bearbeiter','angenommen')";
        $this->app->DB->Insert($sql);
        $id = $this->app->DB->GetInsertID();

        $this->app->Location->execute('index.php?module=wareneingang&action=distrietiketten&backaction=paketannahme&id='.$id.'&pos='.$pos.'&menge='.$menge.($lagerplatz > 0?'&lager_platz='.$lagerplatz:'').(!empty($ismobile)?'&ismobile=1':''));
      }
    }
  }
  if($vorlage!='')
  {
    if($vorlage==='bestellung')
    {
      $vorlageid = $id;
      $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$id' LIMIT 1");
    }
    else if ($vorlage==='adresse')
    {
      $adresse = $id;
      $vorlageid = $adresse;
      // standardprojekt von kunde
      $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else {
      $this->app->ExitXentral();
    }

    $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());

    $sql = "INSERT INTO paketannahme (datum,adresse,vorlage,vorlageid,projekt,bearbeiter,status) VALUES
      (NOW(),'$adresse','$vorlage','$vorlageid','$projekt','$bearbeiter','angenommen')";
    $this->app->DB->Insert($sql);
    $id = $this->app->DB->GetInsertID();

    if($this->app->erp->ModulVorhanden('schneller_wareneingang') &&
      $this->app->erp->RechteVorhanden('schneller_wareneingang','distriinhaltschnell')) {
      $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id = '$adresse' LIMIT 1");
      if($lieferantennummer != '' && $lieferantennummer != '0')
      {
        $this->app->Location->execute('index.php?module=schneller_wareneingang&action=distriinhaltschnell&id='.$id);
      }      
    }
    $this->app->Location->execute('index.php?module=wareneingang&action=distriinhalt&id='.$id);
  }

  $rechteproblem = array();
  if(!$this->app->erp->RechteVorhanden('wareneingang','distrietiketten')){
    $rechteproblem[] = 'distrietiketten';
  }
  if(!$this->app->erp->RechteVorhanden('wareneingang','distriabschluss')){
    $rechteproblem[] = 'distriabschluss';
  }
  if(count($rechteproblem) > 0){
    $this->app->Tpl->Set('INFO','<br /><div class="info">Es werden im Menü nicht alle Icons angezeigt, da die nötigen Rechte nicht vorliegen für: Wareneingang > '.implode(', ', $rechteproblem).'</div>');
  }


  $this->app->YUI->TableSearch('SUCHE','paketannahme','show','','',basename(__FILE__), __CLASS__);
  $this->app->YUI->DatePicker('zeitvon');
  $this->app->YUI->DatePicker('zeitbis');

  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum, name, belegnr as bestellung, id FROM bestellung WHERE status!='geliefert'");
  $table->DisplayNew('BESTELLUNGEN','<a href="index.php?module=wareneingang&action=paketannahme&id=%value%&vorlage=bestellung">weiter</a>');

  $this->app->YUI->AutoComplete('suche','adressename');

  $this->app->Tpl->Set('AKTIV_TAB1','tabs-1');
  $this->app->User->SetParameter('wareneingang_action', 'paketannahme');
  $this->app->Tpl->Parse('PAGE','wareneingangpaketannahme.tpl');
}

}
