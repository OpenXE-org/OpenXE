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
include __DIR__.'/_gen/lager.php';
class Lager extends GenLager {
  /** @var Application $app */
  var $app;

  /**
   * @param string $arttab
   * @param string $tab1
   * @param string $tab2
   *
   * @return string
   */
  static function LetzterEK($arttab = 'art', $tab1 = 'e1', $tab2 = 'e')
  {
    return "
    (
      SELECT $tab2.artikel, $tab2.waehrung, $tab2.preis FROM
      (SELECT  max( $tab2.id ) AS id, artikel
          FROM einkaufspreise $tab2
          WHERE $tab2.geloescht !=1
          AND (
            ifnull($tab2.gueltig_bis,'0000-00-00') = '0000-00-00'
            OR $tab2.gueltig_bis >= CURDATE( ) 
            )
          GROUP BY artikel) ".$tab1." 
      INNER JOIN einkaufspreise ".$tab2." ON $tab1.id = $tab2.id
    )
    ";
  }

  /**
   * @param string $typ
   * @param bool   $live
   *
   * @return string
   */
  static function Waehrung($typ = 'letzterek', $live = true)
  {
    if(!$live)
    {
      switch($typ)
      {
        case 'letzterek':
          return "if(ifnull(lw.preis_letzterek,0) <> 0,if(lw.waehrungletzt<>'',lw.waehrungletzt,'EUR'),if(ifnull(ek.waehrung,'')<>'',ek.waehrung,'EUR'))";
        break;
        case 'letzerekarchiv':
          return "if(ifnull(lw.preis_letzterek,0) <> 0,if(lw.waehrungletzt<>'',lw.waehrungletzt,'EUR'),'EUR')";
          break;
        case 'inventurwertarchiv':
          return "if(ifnull(lw.inventurwert,0) <> 0,'EUR',
           
            if(ifnull(lw.preis_letzterek,0) <> 0,
            if(lw.waehrungletzt <> '',lw.waehrungletzt,'EUR'),
            'EUR'
            )
          )";
          break;
        case 'inventurwert':
          return "if(ifnull(lw.inventurwert,0) <> 0,'EUR',
            if(ifnull(art.inventurekaktiv,0) <> 0
            ,  'EUR',
            if(ifnull(lw.preis_letzterek,0) <> 0,
            if(lw.waehrungletzt <> '',lw.waehrungletzt,'EUR'),
            if(ifnull(ek.waehrung,'')<>'',ek.waehrung,'EUR')
            )
            )
          )";
        break;
        case 'kalkulierterekarchiv':
          return "
            if(
                ifnull(lw.preis_kalkulierterek,0) <> 0
              ,
                if(lw.waehrungkalk<>'',lw.waehrungkalk,'EUR')
              ,   
                if(ifnull(lw.preis_letzterek,0) <> 0,
                       if(lw.waehrungletzt<>'',lw.waehrungletzt,'EUR')
                       ,'EUR'
                )
          )";
          break;
        default:
          return "
            if(ifnull(lw.preis_kalkulierterek,0) <> 0,if(lw.waehrungkalk<>'',lw.waehrungkalk,'EUR'),
                  if(ifnull(art.verwendeberechneterek,0) <> 0, if(ifnull(art.berechneterekwaehrung,'')<>'',art.berechneterekwaehrung,'EUR')
                    ,
                  if(ifnull(lw.preis_letzterek,0) <> 0,if(lw.waehrungletzt<>'',lw.waehrungletzt,'EUR'),if(ifnull(ek.waehrung,'')<>'',ek.waehrung,'EUR'))
                  )
          )";
        break;
      }
    }else{
      switch($typ)
      {
        case 'letzterek':
        case 'letzterekarchiv':
          return "if(ifnull(ek.waehrung,'')<>'',ek.waehrung,'EUR')";
        break;
        case 'inventurwert':
        case 'inventurwertarchiv':
          return "if(ifnull(art.inventurekaktiv,0) <> 0,'EUR',if(ifnull(ek.waehrung,'')<>'',ek.waehrung,'EUR'))";
        break;
        default:
          return "            
            if(ifnull(art.verwendeberechneterek,0) <> 0,if(ifnull(art.berechneterekwaehrung,'')<>'',art.berechneterekwaehrung,'EUR')
              ,
              if(ifnull(ek.waehrung,'')<>'',ek.waehrung,'EUR')
            )
          ";
        break;
      }
    }
  }

  /**
   * @param string $typ
   * @param bool   $live
   *
   * @return string
   */
  public static function EinzelPreis($typ = 'letzterek', $live = true)
  {
    if(!$live)
    {
      switch($typ)
      {
        case 'letzterekarchiv':
          return "if(ifnull(lw.preis_letzterek,0) <> 0,lw.preis_letzterek,0)";
          break;
        case 'letzterek':
          return "if(ifnull(lw.preis_letzterek,0) <> 0,lw.preis_letzterek,ifnull(ek.preis,0))";
        break;
        case 'inventurwertarchiv':
          return "if(ifnull(lw.inventurwert,0) <> 0,lw.inventurwert,
            ifnull(lw.preis_letzterek,0)
          )";
          break;
        case 'inventurwert':
          return "if(ifnull(lw.inventurwert,0) <> 0,lw.inventurwert,
            if(ifnull(art.inventurekaktiv,0) <> 0
            ,  art.inventurek,
            if(ifnull(lw.preis_letzterek,0) <> 0,lw.preis_letzterek,ifnull(ek.preis,0))
            )
          )";
        break;
        case 'kalkulierterekarchiv':
          return "
            if(
                ifnull(lw.preis_kalkulierterek,0) <> 0,
                lw.preis_kalkulierterek,
                ifnull(lw.preis_letzterek,0) 
             )";
          break;
        default:
          return "
            if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.preis_kalkulierterek,
                  if(ifnull(art.verwendeberechneterek,0) <> 0,art.berechneterek
                    ,if(ifnull(lw.preis_letzterek,0) <> 0,
                        lw.preis_letzterek,
                        ifnull(ek.preis,0)
                    )
                  )
          )";
        break;
      }
    }else{
      switch($typ)
      {
        case 'letzterek':
        case 'letzterekarchiv':
          return "ifnull(ek.preis,0)";
        break;
        case 'inventurwertarchiv':
        case 'inventurwert':
          return "if(ifnull(art.inventurekaktiv,0) <> 0,art.inventurek,ifnull(ek.preis,0))";
        break;
        default:
          return "            
            if(ifnull(art.verwendeberechneterek,0) <> 0,art.berechneterek
              ,
              ifnull(ek.preis,0)
            )
          ";
        break;
      }
    }
  }

  /**
   * @param string $typ
   * @param bool   $live
   *
   * @return string
   */
  public static function KursJoin($typ, $live = true)
  {
    return " LEFT JOIN (
              SELECT max(kurs) as kurs, waehrung_von, waehrung_nach FROM waehrung_umrechnung WHERE  (isnull(gueltig_bis) OR gueltig_bis >= now() OR gueltig_bis = '0000-00-00') AND (waehrung_von LIKE 'EUR' OR waehrung_nach LIKE 'EUR') GROUP BY waehrung_von,waehrung_nach
              ) wt ON wt.waehrung_nach <> 'EUR' AND wt.waehrung_nach = ".self::Waehrung($typ, $live)." OR wt.waehrung_von <> 'EUR' AND wt.waehrung_von = ".self::Waehrung($typ, $live)."  ";

  }

  /**
   * @param Application $app
   * @param string      $typ
   * @param null|bool   $live
   *
   * @return string
   */
  public static function PreisUmrechnung($app, $typ, $live = null)
  {
    $kursusd = $app->erp->GetWaehrungUmrechnungskurs('EUR','USD');
    $kurschf = $app->erp->GetWaehrungUmrechnungskurs('EUR','CHF');
    
    if(!$live)
    {
        return '
              if(
                ifnull(lw.kursletzt,0)<> 0
              ,
                1 / lw.kursletzt
              ,
                if(
                  ifnull(wt.kurs,0) <> 0
                ,
                  if(
                    wt.waehrung_nach = '.self::Waehrung($typ, $live).',
                    (1/wt.kurs),
                    wt.kurs
                  )
                ,
                  if('.self::Waehrung($typ, $live).' = \'USD\', 
                      1.0 / '.$kursusd.',
                      if('.self::Waehrung($typ, $live).' = \'CHF\', 
                        1.0 / '.$kurschf.',
                      1)
                  )
                )
              )
              * 
              '.self::EinzelPreis($typ, $live);
      
    }
    return '
              
                if(
                  ifnull(wt.kurs,0) <> 0
                ,
                  if(
                    wt.waehrung_nach =   '.self::Waehrung($typ, $live).',
                    (1/wt.kurs),
                    wt.kurs
                  )
                ,
                  if('.self::Waehrung($typ, $live).' = \'USD\', 
                      1.0 / '.$kursusd.',
                      if('.self::Waehrung($typ, $live).' = \'CHF\', 
                        1.0 / '.$kurschf.',
                      1)
                  )
                )
              *
              '.self::EinzelPreis($typ, $live);

    
    /*
    
    if(!$live)
    {//aus Cronjob
      if($typ == 'letzterek')
      {
        return '
              if(
                ifnull(lw.kursletzt,0)<> 0
              ,
                1 / lw.kursletzt
              ,
                if(
                  ifnull(wt.kurs,0) <> 0
                ,
                  if(
                    wt.waehrung_nach = lw.waehrungletzt,
                    (1/wt.kurs),
                    wt.kurs
                  )
                ,
                  if(lw.waehrungletzt = \'USD\', 
                      1.0 / '.$kursusd.',
                      if(lw.waehrungletzt = \'CHF\', 
                        1.0 / '.$kurschf.',
                      1)
                  )
                )
              )
              * ifnull(lw.preis_letzterek,0)
              ';
      }else{
        return '
              if(
                if(ifnull(lw.preis_kalkulierterek,0) <> 0,ifnull(lw.kurskalk,0),ifnull(lw.kursletzt,0))<> 0
              ,
                1 / if(ifnull(lw.preis_kalkulierterek,0) <> 0,ifnull(lw.kurskalk,0),ifnull(lw.kursletzt,0))
              ,
                if(
                  ifnull(wt.kurs,0) <> 0
                ,
                  if(
                    wt.waehrung_nach =   if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.kurskalk,lw.kursletzt),
                    (1/wt.kurs),
                    wt.kurs
                  )
                ,
                  if(if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.kurskalk,lw.kursletzt) = \'USD\', 
                      1.0 / '.$kursusd.',
                      if(if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.kurskalk,lw.kursletzt) = \'CHF\', 
                        1.0 / '.$kurschf.',
                      1)
                  )
                )
              )
              * if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.preis_kalkulierterek,ifnull(lw.preis_letzterek,0))
              ';
      }
    }else{
      if($typ == 'letzterek')
      {
        return '
                if(
                  ifnull(wt.kurs,0) <> 0
                ,
                  if(
                    wt.waehrung_nach = ek.waehrung,
                    (1/wt.kurs),
                    wt.kurs
                  )
                ,
                  if(ifnull(ek.waehrung,\'\') = \'USD\', 
                      1.0 / '.$kursusd.',
                      if(ifnull(ek.waehrung,\'\') = \'CHF\', 
                        1.0 / '.$kurschf.',
                      1)
                  )
                )
              
              * ifnull(ek.preis,0)
              ';
      }else{
        return '
                if(
                  ifnull(wt.kurs,0) <> 0
                ,
                  if(
                    wt.waehrung_nach =   if(ifnull(art.berechneterek,0) <> 0,art.berechneterekwaehrung,ifnull(ek.waehrung,\'\')),
                    (1/wt.kurs),
                    wt.kurs
                  )
                ,
                  if(if(ifnull(art.berechneterek,0) <> 0,art.berechneterekwaehrung,ifnull(ek.waehrung,\'\')) = \'USD\', 
                      1.0 / '.$kursusd.',
                      if(if(ifnull(art.berechneterek,0) <> 0,art.berechneterekwaehrung,ifnull(ek.waehrung,\'\')) = \'CHF\', 
                        1.0 / '.$kurschf.',
                      1)
                  )
                )
              
              * if(ifnull(art.berechneterek,0) <> 0,art.berechneterek,ifnull(ek.preis,0))
              ';
      }

    }
    */
  }

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public static function TableSearch($app, $name, $erlaubtevars)
  {
    $extended_mysql55 = ",'de_DE'";
    switch($name)
    {
      case 'lagerbestandsberechnung':
        $allowed['lager'] = array('wert');
        // headings
        $heading = array('Artikel-Nr.', 'Bezeichnung','Projekt', 'Lager','Menge','Letzter Preis', 'Gesamt','Inventurwert','Inv. Gesamt' ,'Men&uuml;');
        $width = array('10%', '50%','5%', '20%', '10%', '10%','10%', '10%', '1%');
        $findcols = array('a.nummer', 'a.name_de', 'p.abkuerzung',"CONCAT(la.bezeichnung,' / ',lp.kurzbezeichnung)", 'l.menge', "IFNULL(k.minpreis,0)", "IFNULL(k.minpreis,0)*l.menge",'IFNULL(a.inventurek,0)','IFNULL(a.inventurek,0)*l.menge', 'l.id');
        $searchsql = array('a.nummer', 'a.name_de','p.abkuerzung','la.bezeichnung','lp.kurzbezeichnung');
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=artikel&action=lager&id=%value%\" target=\"_blank\">" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>" . "</td></tr></table>";
        $sumcol = array(7,9);
        $alignright = array(5,6,7,8,9);
        // SQL statement
        //$sql = "SELECT SQL_CALC_FOUND_ROWS l.id, a.nummer, a.name_de, CONCAT(la.bezeichnung,' / ',lp.kurzbezeichnung) , ".$this->app->erp->FormatMenge('l.menge').",FORMAT(IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.geloescht!=1 AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis <=NOW()) AND e.artikel=l.artikel ORDER by e.id DESC LIMIT 1),0),2{$extended_mysql55}) as preis, FORMAT(IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.geloescht!=1 AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis <=NOW()) AND e.artikel=l.artikel ORDER by e.id DESC LIMIT 1),0)*l.menge,2{$extended_mysql55}) as wert, a.id FROM lager_platz_inhalt l LEFT JOIN artikel a ON a.id=l.artikel LEFT JOIN lager_platz lp ON lp.id=l.lager_platz LEFT JOIN lager la ON la.id=lp.lager";

        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, a.nummer, a.name_de, p.abkuerzung, CONCAT(la.bezeichnung,' / ',lp.kurzbezeichnung) ,".$app->erp->FormatMenge('l.menge').",
        FORMAT(IFNULL(k.minpreis,0),2{$extended_mysql55}) as preis, 
        FORMAT(IFNULL(k.minpreis,0)*l.menge,2{$extended_mysql55}) as wert,FORMAT(IFNULL(a.inventurek,0),2{$extended_mysql55}) as inv,FORMAT(IFNULL(a.inventurek,0)*l.menge,2{$extended_mysql55}) as invges, a.id 
        FROM lager_platz_inhalt l LEFT JOIN artikel a ON a.id=l.artikel 
        LEFT JOIN (SELECT  max( e.id ) AS maxid, artikel
          FROM einkaufspreise e
          WHERE e.geloescht !=1
          AND (
            e.gueltig_bis = '0000-00-00'
            OR e.gueltig_bis >= NOW( )
            )
          GROUP BY artikel) k1 ON k1.artikel = a.id
        LEFT JOIN (SELECT id, preis as minpreis FROM einkaufspreise ) k ON k1.maxid = k.id
        LEFT JOIN projekt p ON p.id=a.projekt 
        LEFT JOIN lager_platz lp ON lp.id=l.lager_platz 
        LEFT JOIN lager la ON la.id=lp.lager";


        $where = " a.id > 0  AND a.geloescht!=1 ";

        //$groupby=" GROUP by z.adresse_abrechnung ";

        // gesamt anzahl

        $count = "SELECT COUNT(l.id) FROM lager_platz_inhalt l LEFT JOIN artikel a ON a.id=l.artikel WHERE a.id > 0 AND a.geloescht!=1";

        break;
      case 'lagerplatztabelle':
        $allowed['lager'] = array('platz');

        // headings

        $heading = array('Bezeichnung', 'Nachschublager', 'Verbrauchslager','POS Lager', 'kein Auto-Versand','Volumen','Regalart','Kategorie','Kommissions- / Produktionslager','Sortierung','Men&uuml;');
        $width = array('15%', '10%', '10%','5%','5%','10%','10%','5%','10%','8%','1%');
        $findcols = array('l.kurzbezeichnung', "IF(l.autolagersperre,'kein Versand aus diesem Lager','')", "IF(l.verbrauchslager,'ja','')","IF(l.poslager,'ja','')","IF(l.sperrlager,'ja','')",'breite','regalart','abckategorie','a.name','l.rownumber','id');
        $searchsql = array('l.kurzbezeichnung','regalart','abckategorie','a.name');
        $defaultorder = 4;
        $defaultorderdesc = 1;
        $alignright = [10];
        $menu = "<table><tr><td nowrap><a href=\"index.php?module=lager&action=platzeditpopup&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lager&action=deleteplatz&id=%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=PrintDialog(\"index.php?module=lager&action=regaletiketten&id=%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/labelprinter.png\" border=\"0\"></a></td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, 
          l.kurzbezeichnung, if(l.autolagersperre,'kein Versand aus diesem Lager','') as autolagersperre, 
                if(l.verbrauchslager,'ja','') as verbrauchslager,
                if(l.poslager,'ja','') as poslager,
                if(l.sperrlager,'ja','') as sperrlager,
                if(l.laenge!=0.0,CONCAT(l.laenge,'/',l.breite,'/',l.hoehe),'-') as volumen,
                l.regalart,l.abckategorie, a.name, l.rownumber,
                l.id as menu FROM lager_platz l LEFT JOIN adresse a ON a.id=l.adresse ";

        $id = $app->Secure->GetGET('id');

        // fester filter
        $where = " l.geloescht=0 AND l.id!=0 AND l.lager='$id' ";
        $count = "SELECT COUNT(id) FROM lager_platz WHERE geloescht=0 AND lager='$id' ";
        break;
      case 'lagerletztebewegungen':
        $allowed['lager'] = array('letztebewegungen');

        // headings

        // headings

        $heading = array('Datum', 'Lager', 'Menge', 'Nummer', 'Artikel', 'Richtung', 'Referenz', 'Bearbeiter', 'Projekt', 'Men&uuml;');
        $width = array('1%', '5%', '5%', '5%', '5%', '5%', '40%', '20%', '5%', '1%');
        $findcols = array('zeit', 'lager', 'menge', 'nummer', 'name_de', 'Richtung', 'referenz', 'bearbeiter', 'projekt', 'id');
        $searchsql = array('lpi.referenz', 'lpi.bearbeiter', 'p.abkuerzung', 'DATE_FORMAT(lpi.zeit,\'%d.%m.%Y\')', 'lp.kurzbezeichnung', 'a.name_de', 'a.nummer');
        $defaultorder = 10; //Optional wenn andere Reihenfolge gewuenscht
        $alignright = array(3);
        $defaultorderdesc = 1;
        $menu = "-";

        $sql = "SELECT SQL_CALC_FOUND_ROWS lpi.id,
                DATE_FORMAT(lpi.zeit,'%d.%m.%Y') as datum, lp.kurzbezeichnung as lager, trim(lpi.menge)+0 as menge, 
                a.nummer, a.name_de, if(lpi.eingang,'Eingang','Ausgang') as Richtung, substring(lpi.referenz,1,60) as referenz, lpi.bearbeiter as bearbeiter, p.abkuerzung as projekt, 
                lpi.id FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id LEFT JOIN artikel a ON a.id=lpi.artikel";


        $count = "SELECT COUNT(lpi.id) FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id ";
        break;
      case 'lagertabelle':
        $allowed['lager'] = array('list');
        $defaultCountry = $app->erp->Firmendaten('land');
        if(empty($defaultCountry)) {
          $defaultCountry = 'DE';
        }
        // headings
        $heading = array('Bezeichnung', 'Projekt','Kommissions- / Produktionslager','Land','Men&uuml;');
        $width = array('60%', '10%','20%','5%', '8%');
        $findcols = array('l.bezeichnung', 'p.abkuerzung','a.name',"IF(a.land IS NULL OR a.land = '', '$defaultCountry', a.land)", 'l.id');
        $searchsql = array('l.bezeichnung', 'p.abkuerzung','a.name',"IF(a.land IS NULL OR a.land = '', '$defaultCountry', a.land)",'p.name');
        $defaultorder = 4;
        $defaultorderdesc = 1;
        $menu = "<table><tr><td nowrap><a href=\"index.php?module=lager&action=edit&id=%value%\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lager&action=delete&id=%value%\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=PrintDialog(\"index.php?module=lager&action=regaletiketten&id=%value%&cmd=all\");><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/labelprinter.png\" border=\"0\"></a></td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, l.bezeichnung, p.abkuerzung, a.name,
                           IF(a.land IS NULL OR a.land = '', '$defaultCountry', a.land),
              l.id as menu 
            FROM `lager` AS `l`
            LEFT JOIN `projekt` AS `p` ON p.id=l.projekt 
            LEFT JOIN `adresse` AS `a` ON a.id=l.adresse ";

        // fester filter
        $where = " l.geloescht=0 AND l.id!=0 ".$app->erp->ProjektRechte(); // heute mal wieder projekt rein
        $count = "SELECT COUNT(l.id) 
                  FROM `lager` AS `l`
                  LEFT JOIN `projekt` AS `p` ON p.id=l.projekt 
                  WHERE l.geloescht=0 ".$app->erp->ProjektRechte(); // heute mal wieder projekt rein
        break;
      case "lagerdifferenzenlagerplatz":
        $allowed['lager'] = array('differenzenlagerplatz');

        // headings
        $heading = array('Artikel-Nr.', 'Artikel', 'Projekt', 'Eingang', 'Ausgang', 'Berechnet', 'Bestand', 'Differenz','Lagerplatz', 'Men&uuml;');
        $width = array('10%', '40%', '10%', '10%', '5%', '5%', '5%', '5%', '5%', '10%');
        $findcols = array('a.nummer', 'a.name_de', 'p.abkuerzung', 'l.eingang', 'l.ausgang', 'l.berechnet', 'l.bestand', 'l.differenz', 'l.lager_platz', 'a.id');
        $searchsql = array('a.nummer', 'a.name_de', 'p.abkuerzung', 'l.eingang', 'l.ausgang', 'l.berechnet', 'l.bestand', 'l.differenz', 'lp.kurzbezeichnung');
        $defaultorder = 6;
        $defaultorderdesc = 1;
        $alignright = array(4,5,6,7,8);
        $menu = "<a href=\"index.php?module=artikel&action=lager&id=%value%\" target=\"_blank\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;<a href=\"#\" onclick=LagerplatzdifferenzenEdit(\"%value%\") ><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\" border=\"0\"></a>";

        /*
               ifnull((SELECT SUM(l.menge) FROM lager_bewegung l WHERE l.artikel=a.id AND l.eingang=1),0)-
               ifnull((SELECT SUM(l.menge) FROM lager_bewegung l WHERE l.artikel=a.id AND l.eingang=0),0)-
               ifnull((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) as differenz,
        */

        // SQL statement
        $sql = "
          SELECT 
            SQL_CALC_FOUND_ROWS a.id, 
            a.nummer, 
            a.name_de, 
            p.abkuerzung,
            ".$app->erp->FormatMenge('l.eingang').",
            ".$app->erp->FormatMenge('l.ausgang').",
            ".$app->erp->FormatMenge('l.berechnet').",
            ".$app->erp->FormatMenge('l.bestand').",
            if(l.bestand > l.berechnet, CONCAT('<font color=red>',".$app->erp->FormatMenge('l.berechnet').",'</font>'),
            ".$app->erp->FormatMenge('l.differenz')."), 
            lp.kurzbezeichnung,
            CONCAT(a.id,'_',lp.id)
          FROM 
            lager_differenzen l 
            LEFT JOIN artikel a ON a.id=l.artikel
            LEFT JOIN lager_platz lp ON lp.id = l.lager_platz
            LEFT JOIN projekt p ON a.projekt = p.id
        ";

        // fester filter
        $where = " l.user='" . $app->User->GetID() . "' AND l.lager_platz != 0 ";
        $count = "SELECT COUNT(l.id) FROM lager_differenzen l WHERE l.user='" . $app->User->GetID() . "' AND l.lager_platz != 0 ";
        break;
      case 'lagerdifferenzen':
        $allowed['lager'] = array('differenzen');

        // headings
        $heading = array('Artikel-Nr.', 'Artikel', 'Eingang', 'Ausgang', 'Berechnet', 'Bestand', 'Differenz', 'Men&uuml;');
        $width = array('10%', '40%', '10%', '10%', '10%', '10%', '10%', '10%');
        $findcols = array('a.nummer', 'a.name_de', 'l.eingang', 'l.ausgang', 'l.berechnet', 'l.bestand', 'l.differenz', 'a.id');
        $searchsql = array('kurzbezeichnung');
        $defaultorder = 6;
        $defaultorderdesc = 1;
        $alignright = array(3,4,5,6,7);
        $menu = "<a href=\"index.php?module=artikel&action=lager&id=%value%\" target=\"_blank\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" . "&nbsp;";

        /*
               ifnull((SELECT SUM(l.menge) FROM lager_bewegung l WHERE l.artikel=a.id AND l.eingang=1),0)-
               ifnull((SELECT SUM(l.menge) FROM lager_bewegung l WHERE l.artikel=a.id AND l.eingang=0),0)-
               ifnull((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) as differenz,
        */

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer, a.name_de, ".$app->erp->FormatMenge('l.eingang').",".$app->erp->FormatMenge('l.ausgang').",
        ".$app->erp->FormatMenge('l.berechnet')."   ,".$app->erp->FormatMenge('l.bestand')." ,
              if(l.bestand > l.berechnet, CONCAT('<font color=red>',".$app->erp->FormatMenge('l.differenz').",'</font>'),".$app->erp->FormatMenge('l.differenz')."), a.id FROM lager_differenzen l 
                LEFT JOIN artikel a ON a.id=l.artikel";

        // fester filter
        $where = " l.user='" . $app->User->GetID() . "' AND l.lager_platz = 0 ";
        $count = "SELECT COUNT(l.id) FROM lager_differenzen l WHERE l.user='" . $app->User->GetID() . "' AND l.lager_platz = 0 ";
        break;
      case "lager_wert":
        $allowed['lager'] = array('wert');
        $app->DB->Select("SELECT waehrungkalk,waehrungletzt,kurskalk,kursletzt FROM lagerwert LIMIT 1");
        if($app->DB->error())
        {
          $app->erp->CheckColumn("waehrungkalk", "VARCHAR(16)", "lagerwert", "NOT NULL DEFAULT ''");
          $app->erp->CheckColumn("waehrungletzt", "VARCHAR(16)", "lagerwert", "NOT NULL DEFAULT ''");
          $app->erp->CheckColumn("kurskalk","DECIMAL(19,8)", "lagerwert", "NOT NULL DEFAULT '0'");
          $app->erp->CheckColumn("kursletzt","DECIMAL(19,8)", "lagerwert", "NOT NULL DEFAULT '0'");
        }
        $preisart = (String)$app->YUI->TableSearchFilter($name, 1, 'preisart', $app->User->GetParameter("lager_wert_preisart"));
        if($preisart == '')
        {
          $preisart = 'letzterek';
        }
        
        $artikel = (String)$app->YUI->TableSearchFilter($name, 2, 'artikel', $app->User->GetParameter("lager_wert_artikel"));
        if($artikel)
        {
          $artikel = explode(' ', $artikel);
          $artikel = $app->DB->Select("SELECT id FROM artikel WHERE nummer = '".reset($artikel)."' AND (geloescht = 0 OR isnull(geloescht)) LIMIT 1");
        }

        $datum = (String)$app->YUI->TableSearchFilter($name, 3, 'datum', $app->User->GetParameter("lager_wert_datum"));
        if($datum)
        {
          $datum = $app->String->Convert($datum, '%1.%2.%3', '%3-%2-%1');
        }else{
          $datum = date('Y-m-d');
        }
        $colmenge = 'lw.menge';
        if($datum == date('Y-m-d'))
        {
          $live = true;
          $colmenge = 'lpi.menge';
        }else{
          $live = false;
          $_datum = $app->DB->Select("SELECT max(datum) FROM lagerwert WHERE datum <= '$datum' AND '$datum' < curdate() ");
          if($_datum)
          {
            $datum = $_datum;
          }
        }

        $lager = (String)$app->YUI->TableSearchFilter($name, 4, 'lager', $app->User->GetParameter("lager_lager"));
        if($lager)
        {
          $lager = $app->DB->Select("SELECT id FROM lager WHERE bezeichnung = '$lager' AND (geloescht = 0 OR isnull(geloescht)) LIMIT 1");
        }
        $lagerplatz = (String)$app->YUI->TableSearchFilter($name, 5, 'lagerplatz', $app->User->GetParameter("lager_lagerplatz"));
        if($lagerplatz)
        {
          $lagerplatz = explode(' ', $lagerplatz);
          $lagerplatz = $app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung = '".reset($lagerplatz)."' AND (geloescht = 0 OR isnull(geloescht)) LIMIT 1");
        }
        $gruppierenlager = (int)$app->YUI->TableSearchFilter($name, 6, 'gruppierenlager', $app->User->GetParameter("lager_wert_gruppierenlager"),0,'checkbox');
        $preiseineuro = (int)$app->YUI->TableSearchFilter($name, 7, 'preiseineuro', $app->User->GetParameter("lager_wert_preiseineuro"),0,'checkbox');
        if($preiseineuro)
        {
          $kursusd = $app->erp->GetWaehrungUmrechnungskurs('EUR','USD');
          $kurschf = $app->erp->GetWaehrungUmrechnungskurs('EUR','CHF');
        }
        $artikelkategorie = (String)$app->YUI->TableSearchFilter($name, 8, 'artikelkategorie', $app->User->GetParameter("lager_wert_artikelkategorie"));
        $artikelkategorie = explode(" ", $artikelkategorie);
        $artikelkategorieid = $artikelkategorie[0];
        $artikelkategorieid = $app->DB->Select("SELECT id FROM artikelkategorien WHERE id = '$artikelkategorieid' LIMIT 1");
        if($artikelkategorieid != ''){
          $artikelkategorie = $artikelkategorieid;
        }else{
          $artikelkategorie = 0;
        }
        //if($artikelkategorie)$artikelkategorie = $app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung LIKE '%$artikelkategorie%' LIMIT 1");
        $colgewicht ="if(lw.gewicht = 0,ifnull(art.gewicht,'0') ,lw.gewicht) *lw.menge";
        $colvolumen = "if(lw.volumen=0,ifnull(art.laenge,'0')*ifnull(art.breite,'0')*ifnull(art.hoehe,'0'),lw.volumen)*lw.menge";
        $colkurzbezeichnung = 'lp.kurzbezeichnung';
        $colbezeichnung = 'lag.bezeichnung';
        if($live)
        {
          $colgewicht = "ifnull(art.gewicht,'0') * ifnull(lpi.menge,0)";
          $colvolumen = "ifnull(art.laenge,'0')*ifnull(art.breite,'0')*ifnull(art.hoehe,'0')* ifnull(lpi.menge,0)";
          $colkurzbezeichnung = 'lpi.kurzbezeichnung';
          $colbezeichnung = 'lpi.bezeichnung';
        }
        $heading = array('Datum','Artikel-Nr.','Artikel','Artikelkategorie','Lager','Lagerplatz','Menge','Gewicht','Volumen','EK-Preis','Gesamt','W&auml;hrung','letzte Bewegung', '');
        $width = array('5%','10%','20%','10%','10%','10%','5%','5%','5%','5%','5%','5%','8%', '1%');
        $findcols = array('lw.datum','art.nummer','art.name_de','(select bezeichnung from artikelkategorien where id=(select SUBSTRING_INDEX(SUBSTRING_INDEX(art.typ, \'kat\', 1), \'_\', 1) as type from artikel where id=art.id))', $colbezeichnung,$colkurzbezeichnung,$colmenge,$colgewicht,$colvolumen);
        $kursjoin = "";

        $numbercols = array(9, 10);
        $datecols = array(0);


        if($preisart == 'letzterek')
        {
          if($preiseineuro){
            $kursjoin = self::KursJoin($preisart, $datum);
            $dummy = self::PreisUmrechnung($app, $preisart, $live);
            /*$dummy = '
            if(
              ifnull(lw.kursletzt,0)<> 0
            ,
              1 / lw.kursletzt
            ,
              if(
                ifnull(wt.kurs,0) <> 0
              ,
                if(
                  wt.waehrung_nach = lw.waehrungletzt,
                  (1/wt.kurs),
                  wt.kurs
                )
              ,
                if(lw.waehrungletzt = \'USD\', 
                    1.0 / '.$kursusd.',
                    if(lw.waehrungletzt = \'CHF\', 
                      1.0 / '.$kurschf.',
                    1)
                )
              )
            )
            * ifnull(lw.preis_letzterek,0)
            ';*/
          }else{
            //$dummy = 'ifnull(lw.preis_letzterek,0)';
            $dummy = self::EinzelPreis($preisart,$live);
          }
          $findcols[] = $dummy;
          $preiscol = $app->erp->FormatPreis($dummy,2);
          $gesamtcol = "(".$dummy.'*'.$colmenge.")";
          $findcols[] = $gesamtcol;
          //$waehrungcol = 'lw.waehrungletzt';
          $waehrungcol = self::Waehrung($preisart,$live);
          $findcols[] = $waehrungcol;
        }elseif($preisart == 'inventurwert'){
          
          if($preiseineuro){
            $dummy = self::PreisUmrechnung($app, $preisart, $live);
            $kursjoin = self::KursJoin($preisart, $datum);
          }else{
            $dummy = 'if(ifnull(lw.inventurwert,0) = 0 AND art.inventurekaktiv = 1, ifnull(art.inventurek,0), ifnull(lw.inventurwert,0))';
            $dummy = $dummy = self::EinzelPreis($preisart,$live);            
          }
          

          $findcols[] = $dummy;
          $preiscol = $app->erp->FormatPreis($dummy,2);
          $findcols[] = $dummy.'*'.$colmenge;
          //$gesamtcol = $app->erp->FormatPreis($dummy.'*'.$colmenge,2);
          $gesamtcol = "(".$dummy.'*'.$colmenge.")";
          //$waehrungcol = "'EUR'";
          $waehrungcol = self::Waehrung($preisart,$live);
          $findcols[] = $waehrungcol;
        }else{
          if($preiseineuro){
            $kursjoin = self::KursJoin($preisart, $datum);

            /*$dummy = '
            if(
              if(ifnull(lw.preis_kalkulierterek,0) <> 0,ifnull(lw.kurskalk,0),ifnull(lw.kursletzt,0))<> 0
            ,
              1 / if(ifnull(lw.preis_kalkulierterek,0) <> 0,ifnull(lw.kurskalk,0),ifnull(lw.kursletzt,0))
            ,
              if(
                ifnull(wt.kurs,0) <> 0
              ,
                if(
                  wt.waehrung_nach =   if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.kurskalk,lw.kursletzt),
                  (1/wt.kurs),
                  wt.kurs
                )
              ,
                if(if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.kurskalk,lw.kursletzt) = \'USD\', 
                    1.0 / '.$kursusd.',
                    if(if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.kurskalk,lw.kursletzt) = \'CHF\', 
                      1.0 / '.$kurschf.',
                    1)
                )
              )
            )
            * if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.preis_kalkulierterek,ifnull(lw.preis_letzterek,0))
            ';*/
            $dummy = self::PreisUmrechnung($app, $preisart, $live);
            //$dummy = 'if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.preis_kalkulierterek,ifnull(lw.preis_letzterek,0))';
          }else{
            //$dummy = 'if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.preis_kalkulierterek,ifnull(lw.preis_letzterek,0))';
            $dummy = self::EinzelPreis($preisart,$live);
          }
          $findcols[] = $dummy;
          $preiscol = $app->erp->FormatPreis($dummy,2);
          $findcols[] = '('.$dummy.'*'.$colmenge.')';
          //$gesamtcol = $app->erp->FormatPreis('('.$dummy.'*'.$colmenge.')',2);
          $gesamtcol = '('.$dummy.'*'.$colmenge.')';
          //$waehrungcol = 'if(ifnull(lw.preis_kalkulierterek,0) <> 0,lw.waehrungkalk,lw.waehrungletzt)';
          $waehrungcol = self::Waehrung($preisart,$live);
          $findcols[] = $waehrungcol;
        }
        
        $findcols[] = 'lw.letzte_bewegung';
        $findcols[] = 'art.id';
        
        //$searchsql = array('art.nummer','art.name_de','lag.bezeichnung','lp.kurzbezeichnung');
        $searchsql = $findcols;
        $searchsql[0] = "date_format(lw.datum,'%d.%m.%Y')";
        $searchsql[11] = "date_format(lw.letzte_bewegung,'%d.%m.%Y %H:%i:%s')";

        //$columnfilter = true;
        $defaultorder = 1;
        $defaultorderdesc = 0;
        $alignright = array(7,8,9,10,11);
        $sumcol = array(8,9,11);
        $onequeryperuser = true;
        $joinek = ' LEFT JOIN '.self::LetzterEK('art', 'e1','e2').' ek ON art.id = ek.artikel';

        if($artikelkategorie > 0){
          $joinartikelbaum = ' LEFT JOIN artikelbaum_artikel aba ON art.id = aba.artikel';
        }

        $waehrungcolanz = $waehrungcol;
        if($preiseineuro){
          $waehrungcolanz = "'EUR'";
        }
        
        if(!$live)
        {
          $sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS art.id, date_format(lw.datum,'%d.%m.%Y'), art.nummer, art.name_de, (select bezeichnung from artikelkategorien where id=(select SUBSTRING_INDEX(SUBSTRING_INDEX(art.typ, 'kat', 1), '_', 1) as type from artikel where id=art.id)) as artikelkategorie, lag.bezeichnung, lp.kurzbezeichnung, 
          ".$app->erp->FormatMenge('lw.menge',2).",".$app->erp->FormatPreis($colgewicht,2).",".$app->erp->FormatPreis($colvolumen,2)."
          , $preiscol, ".$app->erp->FormatPreis($gesamtcol,2).", $waehrungcolanz ,ifnull(date_format(lw.letzte_bewegung,'%d.%m.%Y %H:%i:%s'), ''), art.id 
          FROM artikel art
          INNER JOIN lagerwert lw  ON lw.artikel = art.id AND (isnull(art.geloescht) OR art.geloescht = 0) AND art.lagerartikel = 1
          $joinek
          $kursjoin
          $joinartikelbaum
          
          ";
          $where = " lw.datum = '$datum' ";
          if($gruppierenlager)
          {
            $sql .= "INNER JOIN (SELECT '' as kurzbezeichnung ) lp ON lp.kurzbezeichnung = ''
              INNER JOIN lager lag ON lw.lager = lag.id 
              ";
            
            $where .= " AND lw.lager <> 0";
            if($lager)
            {
              $where .= " AND lw.lager = '$lager' ";
            }
            if($lagerplatz)
            {
              $where .= " AND lw.lager_platz = '$lagerplatz' ";
            }
          }else{
            $sql .= "INNER JOIN lager_platz lp ON lp.id = lw.lager_platz
                    INNER JOIN lager lag ON lag.id = lp.lager
            ";
            $where .= " AND lw.lager = 0";
            if($lager)
            {
              $where .= " AND lw.lager = '$lager' ";
            }
            if($lagerplatz)
            {
              $where .= " AND lw.lager_platz = '$lagerplatz' ";
            }
          }
        }else{

          $findcols[0] = 'curdate()';

          $sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS art.id, date_format(curdate(),'%d.%m.%Y'), art.nummer, art.name_de, (select bezeichnung from artikelkategorien where id=(select SUBSTRING_INDEX(SUBSTRING_INDEX(art.typ, 'kat', 1), '_', 1) as type from artikel where id=art.id)) as artikelkategorie, lpi.bezeichnung, lpi.kurzbezeichnung, 
          ".$app->erp->FormatMenge($colmenge,2).",".$app->erp->FormatPreis($colgewicht,2).",".$app->erp->FormatPreis($colvolumen,2)."
          , $preiscol, ".$app->erp->FormatPreis($gesamtcol,2).", $waehrungcolanz ,ifnull(date_format(lbew.zeit,'%d.%m.%Y %H:%i:%s'), ''), art.id 
          FROM artikel art
          $joinek
          $joinartikelbaum
          LEFT JOIN lagerwert lw ON lw.artikel = art.id AND lw.datum = '$datum' AND lw.datum < curdate()
          $kursjoin
          ";
          $where = " (isnull(art.geloescht) OR art.geloescht = 0) AND art.lagerartikel = 1 ";
          if($gruppierenlager)
          {
            $sql .= "INNER JOIN (
              SELECT lager_platz_inhalt.artikel, sum(lager_platz_inhalt.menge) as menge, '' as kurzbezeichnung,lager.bezeichnung, lager.id as lager 
              FROM lager_platz_inhalt
              INNER JOIN lager_platz ON lager_platz_inhalt.lager_platz = lager_platz.id
              INNER JOIN lager ON lager_platz.lager = lager.id
              GROUP BY lager_platz_inhalt.artikel, lager.id
            ) lpi ON lpi.artikel = art.id AND (isnull(art.geloescht) OR art.geloescht = 0) AND art.lagerartikel = 1
            LEFT JOIN (
              SELECT max(lb1.logdatei) as zeit, lb1.artikel, lp1.lager as lager
              FROM lager_bewegung lb1 
              INNER JOIN lager_platz lp1 ON lb1.lager_platz = lp1.id AND ifnull(lp1.geloescht, 0) = 0
              INNER JOIN lager l1 ON lp1.lager = l1.id AND ifnull(l1.geloescht,0) = 0
              GROUP BY lb1.artikel,lp1.lager
            ) lbew ON lpi.artikel = lbew.artikel AND lpi.lager = lbew.lager
            ";
            
            $where .= " AND lpi.lager <> 0";
            if($lager)
            {
              $where .= " AND lpi.lager = '$lager' ";
            }
            //if($lagerplatz)$where .= " AND lpi.lager_platz = '$lagerplatz' ";        
          }else{
            $sql .= "INNER JOIN (
              SELECT lager_platz_inhalt.artikel, sum(lager_platz_inhalt.menge) as menge, lager_platz.kurzbezeichnung,lager.bezeichnung, lager.id as lager,lager_platz.id as lager_platz
              FROM lager_platz_inhalt
              INNER JOIN lager_platz ON lager_platz_inhalt.lager_platz = lager_platz.id
              INNER JOIN lager ON lager_platz.lager = lager.id
              GROUP BY lager_platz_inhalt.artikel, lager.id, lager_platz.id
            ) lpi ON lpi.artikel = art.id
            LEFT JOIN (
              SELECT max(lb1.logdatei) as zeit, lb1.artikel, lp1.id as lager_platz
              FROM lager_bewegung lb1 
              INNER JOIN lager_platz lp1 ON lb1.lager_platz = lp1.id AND ifnull(lp1.geloescht, 0) = 0
              INNER JOIN lager l1 ON lp1.lager = l1.id AND ifnull(l1.geloescht,0) = 0
              GROUP BY lb1.artikel,lp1.id
            ) lbew ON lpi.artikel = lbew.artikel AND lpi.lager_platz = lbew.lager_platz            
            ";
            
            $where .= " AND lpi.lager <> 0";
            if($lager)
            {
              $where .= " AND lpi.lager = '$lager' ";
            }
          }

          $findcols[10] = "CAST($gesamtcol as DECIMAL(10,2))";
          $findcols[11] = $waehrungcol;
          $findcols[12] = "ifnull(lbew.zeit, '')";
          $searchsql[12] = "date_format(lbew.zeit,'%d.%m.%Y %H:%i:%s')";
          
        }
        
        if($artikel)
        {
          $where .= " AND art.id = '$artikel' ";
        }
        if($artikelkategorie > 0){
          $where .= " AND (aba.kategorie = '$artikelkategorie' OR art.typ = '".$artikelkategorie."_kat') "; 
          //$where .= " AND art.typ = '".$artikelkategorie."_kat' ";
        }
        $sql = $app->YUI->CodiereSQLForOneQuery($sql, $name);

        $groupby = "";
        $count = "";

      break;
      case "lager_zwischenlager_eingang":
        $allowed['lager'] = array('buchenzwischenlager');
        $heading = array('Artikel','Nummer','EAN','Lager','Menge','VPE','Grund','Projekt','Men&uuml;');
        $width = array('20%','10%','10%','10%','5%','5%','20%','10%','1%');
        $findcols = array('a.name_de','a.nummer','a.ean','lp.kurzbezeichnung','z.menge','z.vpe','z.grund','p.abkuerzung', 'z.id');
        $searchsql = array('a.name_de','a.nummer','a.ean','lp.kurzbezeichnung');
        $alignright = array(4);
        $hide767 = array(5,6,7);
        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,a.name_de,a.nummer,a.ean,lp.kurzbezeichnung,trim(z.menge)+0,z.vpe,z.grund, p.abkuerzung as projekt, z.id FROM zwischenlager z 
        LEFT JOIN artikel a ON a.id=z.artikel 
        LEFT JOIN lager_platz lp ON a.lager_platz = lp.id
        LEFT JOIN projekt p ON p.id=z.projekt        
        ";
        
        $delete = "";
        if($app->erp->RechteVorhanden("lager", "buchenzwischenlagerdelete")){        
          $delete = "<a href=\"#\" onclick=\"DeleteZw(%value%);\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
        }

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id=%value%\"><img border=\"0\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\"></a>&nbsp;<a onclick=\"DialogZwischenlager(%value%);\" href=\"#\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/add.png\" border=\"0\"></a>&nbsp;$delete</td></tr></table>";
        
        $where = "z.richtung='eingang'";
        $count = "SELECT count(z.id) FROM zwischenlager z WHERE $where";
      
      break;
      case "lager_zwischenlager_ausgang":
        $allowed['lager'] = array('buchenzwischenlager');
        $heading = array('Artikel','Nummer','EAN','Lager','Menge','VPE','Grund','Projekt','Men&uuml;');
        $width = array('20%','10%','10%','10%','5%','5%','20%','10%','1%');
        $findcols = array('a.name_de','a.nummer','a.ean','lp.kurzbezeichnung','z.menge','z.vpe','z.grund','p.abkuerzung', 'z.id');
        $searchsql = array('a.name_de','a.nummer','a.ean','lp.kurzbezeichnung');
        $alignright = array(4);
        $hide767 = array(5,6,7);
        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,a.name_de,a.nummer,a.ean,lp.kurzbezeichnung,trim(z.menge)+0,z.vpe,z.grund, p.abkuerzung as projekt, z.id FROM zwischenlager z 
        LEFT JOIN artikel a ON a.id=z.artikel 
        LEFT JOIN lager_platz lp ON a.lager_platz = lp.id
        LEFT JOIN projekt p ON p.id=z.projekt        
        ";
        
        $delete = '';
        if($app->User->GetType()=="admin"){
          $delete = "<a href=\"#\" onclick=\"DeleteZw(%value%);\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>";
        }
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id=%value%\"><img border=\"0\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\"></a>&nbsp;$delete</td></tr></table>";
        $where = "z.richtung='ausgang'";
        $count = "SELECT count(z.id) FROM zwischenlager z WHERE $where";
      break;      
      
      case "lager_bestand":
        $allowed['lager'] = array('bestand');
        $app->Tpl->Add('JQUERYREADY', "$('#ohnebestand').click( function() { fnFilterColumn1( 0 ); } );");
        $app->Tpl->Add('JQUERYREADY', "$('#chargengruppierung').click( function() { fnFilterColumn2( 0 ); } );");
        for ($r = 1;$r <= 2;$r++) {
          $app->Tpl->Add('JAVASCRIPT', '
             function fnFilterColumn' . $r . ' ( i )                                         {
             if(oMoreData' . $r . $name . '==1)                                         oMoreData' . $r . $name . ' = 0;
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
        //$defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht

        //$defaultorderdesc = 1;
        //$sumcol = 4;
        $alignright = array(4);
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=artikel&action=lager&id=%value%\" target=\"_blank\"><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\"  border=\"0\"></a></td></tr></table>";
        $more_data1 = $app->Secure->GetGET("more_data1");
        $more_data2 = 0;
        $menucol = 6;
        $heading = array('Artikel-Nr.','Artikel');
        $findcols = array('a.nummer','a.name_de');
        $searchsql = array('a.nummer','a.name_de');
        $width = array('10%','30%');
        $heading[] = 'Lagerplatz';
               
        $width[] = '10%';
        
        $findcols[] = 'lp.kurzbezeichnung';
        $searchsql[] = 'lp.kurzbezeichnung';

        $heading[] = 'Projekt';
               
        $width[] = '10%';
        
        $findcols[] = 'p.abkuerzung';
        $searchsql[] = 'p.abkuerzung';


        $heading[] = 'Menge';
        if($more_data2)
        {
          $findcols[] = 'menge';
        }else{
          $findcols[] = 'menge';
        }
        if($more_data2)
        {
          $findcols[] = 'verkaufbare';
          $findcols[] = 'reserviert';
        }else{
          $findcols[] = 'verkaufbare';
          $findcols[] = 'reserviert';
        }
        $heading[] = 'Verkaufbare&nbsp;<sup>*</sup>';
        $heading[] = 'Reserviert&nbsp;<sup>*</sup>';
        $heading[] = 'Men&uuml;';

        $width[] = '10%';
        $width[] = '10%';
        $width[] = '1%';

        $findcols[] = 'a.id';

        //$moreinfo = true;
        if($more_data2)
        {
          $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\" target=\"_blank\">',a.nummer,'</a>'),a.name_de,";
        }else{
          $sql = "SELECT SQL_CALC_FOUND_ROWS lpi.id, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\" target=\"_blank\">',a.nummer,'</a>'),a.name_de,";
        }        

        
        if($more_data2)
        {
          $sql .= "lp.kurzbezeichnung,p.abkuerzung,

            ".$app->erp->FormatMenge("ifnull(lpi.menge,0)")." as fmenge ,
            ".$app->erp->FormatMenge("IFNULL(mengemhd, ifnull(mengecharge,0))")." as fmengecharge , 
            ".$app->erp->FormatMenge("IFNULL(lpi2.gmenge,0) - IFNULL(r.reserviert,0)")." as verkaufbare, 
            ".$app->erp->FormatMenge("IFNULL(r.reserviert,0)")." as reserviert,
            a.id ";
          $sql .= "
          FROM artikel a
          INNER JOIN (
            SELECT artikel, sum(menge) as menge,lager_platz 
            FROM lager_platz_inhalt 
            GROUP BY artikel, lager_platz
          ) AS lpi ON lpi.artikel = a.id
          INNER JOIN (
            SELECT artikel, sum(menge) as gmenge 
            FROM lager_platz_inhalt 
            GROUP BY artikel
          ) AS lpi2 ON a.id = lpi2.artikel
          INNER JOIN lager_platz lp ON lp.id = lpi.lager_platz
          LEFT JOIN (
            SELECT artikel, lager_platz, charge, sum(menge) as mengecharge , NULL as mhddatum
            FROM lager_charge GROUP BY artikel, lager_platz, charge
          ) c ON c.lager_platz = lpi.lager_platz AND c.artikel = a.id
          LEFT JOIN (
            SELECT artikel, lager_platz, IFNULL(charge,'') as charge2, mhddatum, SUM(menge) as mengemhd
            FROM `lager_mindesthaltbarkeitsdatum`
            GROUP BY artikel, lager_platz, IFNULL(charge,''), mhddatum
          ) AS m ON m.lager_platz = lpi.lager_platz AND m.artikel = a.id
          LEFT JOIN (
            SELECT artikel, SUM(menge) as reserviert 
            FROM lager_reserviert 
            GROUP BY artikel
          ) r ON r.artikel=a.id 
          LEFT JOIN lager l ON l.id=lp.lager 
          LEFT JOIN projekt p ON p.id=l.projekt
          ";
          
        }else{
          $sql .= "lp.kurzbezeichnung,p.abkuerzung,
            ".$app->erp->FormatMenge("ifnull(lpi.menge,0)")." as fmenge ,
            '' as mengecharge, 
            ".$app->erp->FormatMenge("IFNULL(lpi2.gmenge,0) - IFNULL(r.reserviert,0)")." as verkaufbare, ".$app->erp->FormatMenge("IFNULL(r.reserviert,0)")." as reserviert, a.id ";

          $sql .= "
          FROM lager_platz_inhalt lpi 
          LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz 
          LEFT JOIN lager l ON l.id=lp.lager 
          LEFT JOIN projekt p ON p.id=l.projekt
          INNER JOIN artikel a ON a.id=lpi.artikel 
          INNER JOIN (
            SELECT artikel, SUM(menge) as gmenge
            FROM lager_platz_inhalt
            GROUP BY artikel
          ) AS lpi2 ON lpi.artikel = lpi2.artikel
          LEFT JOIN (SELECT artikel, SUM(menge) as reserviert FROM lager_reserviert GROUP BY artikel) r ON r.artikel=a.id";
        }        
        $subwhere = "";
        if($more_data1) {
          $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer,a.name_de,";
        
          $sql .= "'-',p.abkuerzung,'0' as menge,'0' as mengecharge, '0' as verkaufbare, ".$app->erp->FormatMenge("IFNULL(r.reserviert,0)")." as reserviert, a.id 
          FROM artikel AS `a` 
          LEFT JOIN (SELECT lpi.artikel, lp.kurzbezeichnung FROM `lager_platz_inhalt` AS `lpi` LEFT JOIN `lager_platz` AS `lp` ON lp.id=lpi.lager_platz GROUP BY lpi.artikel) AS `lp` ON lp.artikel=a.id
          LEFT JOIN (SELECT lr.artikel, SUM(lr.menge) AS `reserviert` FROM `lager_reserviert` AS `lr` GROUP BY lr.artikel) r ON r.artikel=a.id 
          LEFT JOIN `projekt` AS `p` ON p.id=a.projekt";
          $subwhere = " AND a.lagerartikel=1 AND ((SELECT SUM(lpi.menge) FROM lager_platz_inhalt lpi WHERE lpi.artikel=a.id) <=0 OR ISNULL((SELECT SUM(lpi.menge) FROM lager_platz_inhalt lpi WHERE lpi.artikel=a.id))) ";

        }
        if($more_data2) {
          $subwhere .= ' AND (NOT ISNULL(m.artikel) OR NOT ISNULL(c.artikel)) ';
        }

        $where = " a.geloescht!=1 ".$subwhere." ".$app->erp->ProjektRechte();
        //$count = "select count(distinct ms.datum) FROM mitarbeiterzeiterfassung_sollstunden ms where ms.adresse = '$adresse' AND ms.datum >= '$von' AND ms.datum <= '$bis'";
        /*if($more_data1){
          $count = "SELECT count(a.id) FROM artikel a WHERE " . $where;
        }
        else{
          if($more_data2)
          {
            $count = "SELECT count(c.id) FROM lager_charge c INNER JOIN artikel a ON a.id=c.artikel WHERE ".$where;
          }else{
            $count = "SELECT count(lpi.id) FROM lager_platz_inhalt lpi LEFT JOIN artikel a ON a.id=lpi.artikel LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz WHERE ".$where;
          }          
        }*/
          
                 
      break;

      case "lager_bewegunglist":
        $allowed['lager'] = array('bewegunglist');

        $heading = array('Regal', 'Nummer', 'Name', 'Ein-/Ausgang','Menge', 'Datum', 'Referenz', '');
        $width = array('10%', '8%', '25%', '8%', '8%', '8%', '27%', '1%');

        $findcols = array('p.kurzbezeichnung', 'a.nummer', 'a.name_de', "IF(i.eingang, 'Eingang', 'Ausgang')", "CAST(if(i.eingang, CONCAT('+',trim(i.menge)+0), CONCAT('-',trim(i.menge)+0)) AS decimal(10,2))", 'i.zeit', 'i.referenz', 'i.id');
        $searchsql = array('p.kurzbezeichnung', 'a.nummer', 'a.name_de', "IF(i.eingang, 'Eingang', 'Ausgang')", "if(i.eingang, CONCAT('+',' ',".$app->erp->FormatMenge('i.menge')."), CONCAT('-',' ',".$app->erp->FormatMenge('i.menge')."))", "DATE_FORMAT(i.zeit,'%d.%m.%Y')", 'i.referenz');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $alignright = array(5);

        $datecols = array(5);
        $numbercols = array(4);

        $id = $app->Secure->GetGET("id");
        $sql = "SELECT SQL_CALC_FOUND_ROWS i.id, p.kurzbezeichnung as regal, a.nummer, a.name_de, IF(i.eingang, 'Eingang', 'Ausgang'), if(i.eingang, CONCAT('+',' ',".$app->erp->FormatMenge('i.menge')."), CONCAT('-',' ',".$app->erp->FormatMenge('i.menge').")) as menge, DATE_FORMAT(i.zeit,'%d.%m.%Y') as datum, i.referenz, i.id FROM lager_bewegung i LEFT JOIN lager_platz p ON p.id=i.lager_platz LEFT JOIN artikel a ON i.artikel=a.id";

             
        $lagerplatz = $app->User->GetParameter("lager_bewegung_lagerplatz");
        $lagerplatzid = $app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung = '$lagerplatz' AND kurzbezeichnung != '' LIMIT 1");
        
        $artikel = $app->User->GetParameter("lager_bewegung_artikel");
        $artikeldaten = explode(" ", $artikel, 2);
        $artikelnr = $artikeldaten[0];
        $artikelname = $artikeldaten[1];
        $artikelid = $app->DB->Select("SELECT id FROM artikel WHERE name_de = '$artikelname' AND name_de != '' AND nummer = '$artikelnr' AND nummer != '' LIMIT 1");


        /*if($lagerplatzid != "" && $artikelid != ""){
          $where = " i.id > 0 AND i.lager_platz = '$lagerplatzid' AND i.artikel = '$artikelid'";
        }elseif($lagerplatzid != "" && $artikelid == ""){
          $where = " i.id > 0 AND i.lager_platz = '$lagerplatzid'";
        }elseif($lagerplatzid == "" && $artikelid != ""){
          $where = " i.id > 0 AND i.artikel = '$artikelid'";
        }else{
          $where = " i.id > 0";
        }*/

        if($lagerplatzid != ""){
          $lagerplatzwhere = " AND i.lager_platz = '$lagerplatzid'";
        }else{
          $lagerplatzwhere = "";
        }

        if($artikelid != ""){
          $artikelwhere = " AND i.artikel = '$artikelid'";
        }else{
          $artikelwhere = "";
        }

        $where = " i.id > 0 AND p.lager = '$id'".$lagerplatzwhere.$artikelwhere;
        
        $count = "SELECT count(i.id) FROM lager_bewegung i LEFT JOIN lager_platz p ON p.id = i.lager_platz WHERE $where";

        $app->Tpl->Set("LAGER_PLATZ_BEWEGUNG", $lagerplatz);
        $app->Tpl->Set("ARTIKEL_BEWEGUNG", $artikel);
      

      break;

      case "lager_allebewegungenlist":
        $allowed['lager'] = array('allebewegungenlist');

        $heading = array('Lager', 'Regal', 'Nummer', 'Name', 'Ein-/Ausgang', 'Menge', 'Datum', 'Referenz', 'Bearbeiter', 'Projekt','');
        $width = array('9%', '9%', '8%', '18%', '5%', '5%', '5%', '18%', '10%', '5%','1%');

        $findcols = array('l.bezeichnung', 'lp.kurzbezeichnung', 'a.nummer', 'a.name_de', "IF(i.eingang, 'Eingang', 'Ausgang')", "CAST(if(i.eingang, CONCAT('+',trim(i.menge)+0), CONCAT('-',trim(i.menge)+0)) AS decimal(10,2))", 'i.zeit', 'i.referenz', 'i.bearbeiter', 'p.abkuerzung', 'i.id');
        $searchsql = array('l.bezeichnung', 'lp.kurzbezeichnung', 'a.nummer', 'a.name_de', "IF(i.eingang, CONCAT('+',' ',".$app->erp->FormatMenge('i.menge')."), CONCAT('-',' ',".$app->erp->FormatMenge('i.menge')."))", 'DATE_FORMAT(i.zeit,\'%d.%m.%Y\')', 'i.referenz', 'i.bearbeiter', 'p.abkuerzung');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $alignright = array(6);

        $datecols = array(6);

        $sql = "SELECT SQL_CALC_FOUND_ROWS i.id, l.bezeichnung, lp.kurzbezeichnung as regal, a.nummer, a.name_de, IF(i.eingang, 'Eingang', 'Ausgang'), if(i.eingang, CONCAT('+', ' ', trim(i.menge)+0), CONCAT('-', ' ', trim(i.menge)+0)) as menge, DATE_FORMAT(i.zeit,'%d.%m.%Y') as datum, i.referenz, i.bearbeiter, p.abkuerzung AS projektbewegung, i.id FROM lager_bewegung i LEFT JOIN lager_platz lp ON lp.id=i.lager_platz LEFT JOIN lager l ON lp.lager = l.id LEFT JOIN artikel a ON i.artikel=a.id LEFT JOIN projekt p ON l.projekt = p.id";
   
               
        $lager = $app->User->GetParameter("lager_bewegungalle_lager");
        
        $lagerplatz = $app->User->GetParameter("lager_bewegungalle_lagerplatz");
        $lagerplatzid = $app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung = '$lagerplatz' AND kurzbezeichnung != '' LIMIT 1"); 

        $artikel = $app->User->GetParameter("lager_bewegungalle_artikel");
        $artikeldaten = explode(" ", $artikel, 2);
        $artikelnr = $artikeldaten[0];
        $artikelname = $artikeldaten[1];
        $artikelid = $app->DB->Select("SELECT id FROM artikel WHERE name_de = '$artikelname' AND name_de != '' AND nummer = '$artikelnr' AND nummer != '' LIMIT 1");

        if($lager != ""){
          $lagerwhere = " AND l.bezeichnung = '$lager'";
        }else{
          $lagerwhere = "";
        }

        if($lagerplatzid != ""){
          $lagerplatzwhere = " AND i.lager_platz = '$lagerplatzid'";
        }else{
          $lagerplatzwhere = "";
        }

        if($artikelid != ""){
          $artikelwhere = " AND i.artikel = '$artikelid'";
        }else{
          $artikelwhere = "";
        }


        $where = " i.id > 0".$lagerwhere.$lagerplatzwhere.$artikelwhere.$app->erp->ProjektRechte();
        
        $count = "SELECT count(i.id) FROM lager_bewegung i LEFT JOIN lager_platz lp ON lp.id = i.lager_platz LEFT JOIN lager l ON l.id=lp.lager LEFT JOIN projekt p ON l.projekt = p.id WHERE $where";

        $app->Tpl->Set("LAGER_BEWEGUNG_ALLE", $lager);
        $app->Tpl->Set("LAGER_PLATZ_BEWEGUNG_ALLE", $lagerplatz);
        $app->Tpl->Set("ARTIKEL_BEWEGUNG_ALLE", $artikel);

      break;

      case "lager_inhaltlist":
        $allowed['lager'] = array('lager_inhaltlist');

        $heading = array('Regal', 'Artikel', 'Nummer', 'Projekt', 'Menge', 'Verkaufbare', 'Reserviert', '');
        $width = array('10%', '30%', '10%', '15%', '15%', '10%', '10%', '1%');

        $findcols = array('p.kurzbezeichnung', 't.artikelname', 't.nummer', 'pro.abkuerzung', 't.menge', 't.verkaufbare', 't.reserviert', 't.artikel');
        $searchsql = array('p.kurzbezeichnung', 't.artikelname', 't.nummer', 'pro.abkuerzung', $app->erp->FormatMenge('t.menge'), $app->erp->FormatMenge('t.verkaufbare'), $app->erp->FormatMenge('t.reserviert'));

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $alignright = array(5,6,7);
        $numbercols = array(4,5,6);
       
        $sql = "SELECT SQL_CALC_FOUND_ROWS t.artikel, IFNULL(p.kurzbezeichnung, ' '), t.artikelname, t.nummer, pro.abkuerzung, ".$app->erp->FormatMenge('t.menge').", ".$app->erp->FormatMenge('t.verkaufbare').", ".$app->erp->FormatMenge('t.reserviert').", t.artikel
        FROM lager_platz p 
        JOIN (SELECT i.lager_platz, a.id as artikel, IFNULL(a.name_de, ' ') as artikelname, IFNULL(a.nummer, ' ') as nummer, a.projekt, IFNULL(SUM(i.menge), 0) as menge, IFNULL(SUM(i.menge),0) - IFNULL(SUM(r.menge),0) as verkaufbare, IFNULL(SUM(r.menge), 0) as reserviert FROM lager_platz_inhalt i
        LEFT JOIN artikel a ON i.artikel=a.id 
        LEFT JOIN (SELECT artikel, SUM(menge) as menge FROM lager_reserviert GROUP BY artikel) r ON r.artikel=a.id 
        GROUP BY a.id, i.lager_platz) t ON t.lager_platz= p.id LEFT JOIN projekt pro ON pro.id=t.projekt ";

        //$groupby = " GROUP BY p.kurzbezeichnung, a.id";

        $lagerplatz = $app->User->GetParameter("lager_inhalt_lagerplatz");
        $lagerplatzid = $app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung = '$lagerplatz' AND kurzbezeichnung != '' LIMIT 1");

        $artikel = $app->User->GetParameter("lager_inhalt_artikel");
        $artikeldaten = explode(" ", $artikel, 2);
        $artikelnr = $artikeldaten[0];
        $artikelname = $artikeldaten[1];
        $artikelid = $app->DB->Select("SELECT id FROM artikel WHERE name_de = '$artikelname' AND name_de != '' AND nummer = '$artikelnr' AND nummer != '' LIMIT 1");

        $lagerid = $app->Secure->GetGET("id");

        if($lagerplatzid != ""){
          $lagerplatzwhere = " AND t.lager_platz = '$lagerplatzid'";
        }else{
          $lagerplatzwhere = "";
        }

        if($artikelid != ""){
          $artikelwhere = " AND t.artikel = '$artikelid'";
        }else{
          $artikelwhere = "";
        }

        $where = " p.lager = '$lagerid'".$lagerplatzwhere.$artikelwhere;

        //$count = "SELECT count(p.kurzbezeichnung) FROM lager_platz p LEFT JOIN lager_platz_inhalt i ON p.id=i.lager_platz LEFT JOIN artikel a ON i.artikel=a.id LEFT JOIN lager_reserviert r ON r.artikel=a.id LEFT JOIN projekt pro ON pro.id=a.projekt WHERE $where";

        $app->Tpl->Set("LAGER_PLATZ_INHALT", $lagerplatz);
        $app->Tpl->Set("ARTIKEL_INHALT", $artikel);

      break;

 
      case "lager_reservierungen":
        $allowed['lager'] = array('reservierungen');
               
        $heading = array('Kunde','Belegart','Belegnr','Status','Artikel','Menge','Projekt','Grund','Men&uuml;');
        $width = array('20%','20%','5%','10%','20%','1%');
        $findcols = array('t.kunde', 't.typ', 't.belegnr', 't.status', 't.Artikel', 't.menge', 't.projekt', 't.grund', 't.rid');
        $searchsql = array('t.kunde', 't.typ', 't.belegnr', 't.status', 't.Artikel', $app->erp->FormatMenge('t.menge'), 't.projekt', 't.grund');
        
        $defaultorder = 1; //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc = 1;
        //$sumcol = 9;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"#\" onclick=DeleteDialog(\"index.php?module=lager&action=artikelentfernenreserviert&reservierung=%value%\"); ><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a></td></tr></table>";
        $alignright = array(6);
        $numbercols = array(5);
        $menucol = 5;
        //$moreinfo = true;
        $sql = "
                  SELECT
            SQL_CALC_FOUND_ROWS t.rid,
            t.kunde,
            t.typ,
            t.belegnr,
            t.status,
            t.Artikel,
            ".$app->erp->FormatMenge('t.menge').",
            t.projekt,
            t.grund,
            t.rid
            
          FROM 
          (
            (
              SELECT  r.id as rid, adr.name as kunde,'Auftrag' as typ,if(auf.belegnr = '','ENTWURF',auf.belegnr) as belegnr ,if(auf.status = '','angelegt',auf.status) as status, a.name_de as Artikel,r.menge,p.abkuerzung as projekt,r.grund, r.id FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
              p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id
              INNER JOIN auftrag auf ON auf.id = r.parameter AND r.objekt = 'auftrag'
            )
            UNION ALL 
            (
              SELECT  r.id as rid, adr.name as kunde,'Lieferschein' as typ,if(l.belegnr = '','ENTWURF',l.belegnr) as belegnr ,if(l.status = '','angelegt',l.status) as status, a.name_de as Artikel,r.menge,p.abkuerzung as projekt,r.grund, r.id FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
              p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id        
              INNER JOIN lieferschein l ON l.id = r.parameter AND r.objekt = 'lieferschein'
            )
            UNION ALL 
            (
              SELECT  r.id as rid, adr.name as kunde,'Produktion' as typ,if(l.belegnr = '','ENTWURF',l.belegnr) as belegnr ,if(l.status = '','angelegt',l.status) as status, a.name_de as Artikel,r.menge,p.abkuerzung as projekt,r.grund, r.id FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
              p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id        
              INNER JOIN produktion l ON l.id = r.parameter AND r.objekt = 'produktion'
            )
            UNION ALL 
            (
              SELECT  r.id as rid, adr.name as kunde,'Auftrag' as typ,'GEL&Ouml;SCHT' as belegnr ,'GEL&Ouml;SCHT' as status, a.name_de as Artikel,r.menge,p.abkuerzung as projekt,r.grund, r.id FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
              p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id
              LEFT JOIN auftrag auf ON auf.id = r.parameter AND r.objekt = 'auftrag' WHERE isnull(auf.id) AND r.objekt = 'auftrag'
            )
            UNION ALL 
            (
              SELECT  r.id as rid, adr.name as kunde,'Lieferschein' as typ,'GEL&Ouml;SCHT' as belegnr ,'GEL&Ouml;SCHT' as status, a.name_de as Artikel,r.menge,p.abkuerzung as projekt,r.grund, r.id FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
              p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id        
              LEFT JOIN lieferschein l ON l.id = r.parameter AND r.objekt = 'lieferschein' WHERE isnull(l.id) AND r.objekt = 'lieferschein'
            )
            UNION ALL 
            (
              SELECT  r.id as rid, adr.name as kunde,'Produktion' as typ,'GEL&Ouml;SCHT' as belegnr ,'GEL&Ouml;SCHT' as status, a.name_de as Artikel,r.menge,p.abkuerzung as projekt,r.grund, r.id FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
              p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id        
              LEFT JOIN produktion l ON l.id = r.parameter AND r.objekt = 'produktion' WHERE isnull(l.id) AND r.objekt = 'produktion'
            )
            UNION ALL 
            (
              SELECT  r.id as rid, adr.name as kunde,r.objekt as typ,'' as belegnr , '' as status,  a.name_de as Artikel,r.menge,p.abkuerzung as projekt,r.grund, r.id FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
              p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id WHERE r.objekt <> 'auftrag' AND r.objekt <> 'lieferschein'  AND r.objekt <> 'produktion'          
            )
        
        ) t 
        
        ";
        
        
        //$count = "select count(distinct ms.datum) FROM mitarbeiterzeiterfassung_sollstunden ms where ms.adresse = '$adresse' AND ms.datum >= '$von' AND ms.datum <= '$bis'";
        $count = "SELECT count(r.id) FROM lager_reserviert r";
            
      break;


      case "lager_nachschublager":
        $allowed['lager'] = array('nachschublager');

        $heading = array('Nummer','Name','Nachschublager','Nachschublagerplatz','Lagerbestand','Menge','Lagervorschlag', 'Lagerbestand','Menge Lager (Min)','Menge Lager (Max)', 'Umzulagernde Menge', '');
        $width = array('10%','20%','5%','5%','10%','10%','10%', '10%', '10%', '10%','10%','1%');
        $findcols = array('a.nummer', 'a.name_de','l.bezeichnung', 'lp.kurzbezeichnung', 's.amount','vorschlag.needed', 'lp2.kurzbezeichnung', 'vorschlag.amount', 's.amount','vorschlag.storage_min_amount','vorschlag.storage_max_amount', 's.id');
        $searchsql = array('a.nummer', 'a.name_de','l.bezeichnung', 'lp.kurzbezeichnung', $app->erp->FormatMenge('s.amount'),
          'lp2.kurzbezeichnung', $app->erp->FormatMenge('vorschlag.amount'), $app->erp->FormatMenge('s.amount_to_relocate'));

        $defaultorder = 1;
        $defaultorderdesc = 0;


        $alignright = array(5,6,7,8,9,10,11);
        $numbercols = array(4,5,6,7,8, 9,10);


        $where = "s.is_replenishment = 1 AND vorschlag.is_replenishment = 0";

        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, a.nummer, a.name_de, l.bezeichnung, lp.kurzbezeichnung, 
        ".$app->erp->FormatMenge('s.amount').", 
        ".$app->erp->FormatMenge('vorschlag.needed').",
        lp2.kurzbezeichnung, 
        ".$app->erp->FormatMenge('vorschlag.amount').", 
        IF(vorschlag.storage_min_amount > 0, ".$app->erp->FormatMenge('vorschlag.storage_min_amount').",'-'),
        IF(vorschlag.storage_max_amount > 0,".$app->erp->FormatMenge('vorschlag.storage_max_amount').",'-'),
        ".$app->erp->FormatMenge(
          'IF(
            vorschlag.storage_min_amount > 0 AND vorschlag.storage_min_amount >s.amount_to_relocate, 
          IF(s.amount > vorschlag.storage_min_amount, vorschlag.storage_min_amount,s.amount),
          IF(s.amount >s.amount_to_relocate, s.amount_to_relocate,s.amount)
          )').", s.id 
        FROM stock_replenishment_list s 
        LEFT JOIN artikel a ON s.article_id = a.id
        JOIN lager_platz lp ON lp.id = s.storage_area_id
        LEFT JOIN lager l ON l.id=lp.lager
        LEFT JOIN stock_replenishment_list vorschlag ON s.article_id = vorschlag.article_id
        JOIN lager_platz lp2 ON lp2.id = vorschlag.storage_area_id";

      break;
    }
    
    $erg = [];
   
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
   * Lager constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    
    $this->app = $app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("create", "LagerCreate");
    $this->app->ActionHandler("edit", "LagerEdit");
    $this->app->ActionHandler("list", "LagerList");
    $this->app->ActionHandler("platz", "LagerPlatz");
    //$this->app->ActionHandler("bewegung", "LagerBewegung");
    $this->app->ActionHandler("bewegung", "LagerBewegungPopup");
    $this->app->ActionHandler("allebewegungen", "LagerAlleBewegungen");
    $this->app->ActionHandler("inhalt", "LagerInhalt");
    $this->app->ActionHandler("pdf", "LagerPDF");
    $this->app->ActionHandler("bestand", "LagerBestand");
    $this->app->ActionHandler("wert", "LagerWert");
    $this->app->ActionHandler("platzeditpopup", "LagerPlatzEditPopup");
    $this->app->ActionHandler("delete", "LagerDelete");
    $this->app->ActionHandler("deleteplatz", "LagerPlatzDelete");
    $this->app->ActionHandler("etiketten", "LagerEtiketten");
    $this->app->ActionHandler("etikettenlist", "LagerEtikettenlist");
    $this->app->ActionHandler("zwischenlager", "LagerZwischenlager");
    $this->app->ActionHandler("regaletiketten", "LagerRegalEtiketten");
    $this->app->ActionHandler("reservierungen", "LagerReservierungen");
    $this->app->ActionHandler("buchen", "LagerBuchen");
    $this->app->ActionHandler("buchenzwischenlager", "LagerBuchenZwischenlager");
    $this->app->ActionHandler("buchenzwischenlagerdelete", "LagerBuchenZwischenlagerDelete");
    $this->app->ActionHandler("bucheneinlagern", "LagerBuchenEinlagern");
    $this->app->ActionHandler("buchenauslagern", "LagerBuchenAuslagern");
    $this->app->ActionHandler("artikelentfernenreserviert", "LagerArtikelEntfernenReserviert");
    $this->app->ActionHandler("letztebewegungen", "LagerLetzteBewegungen");
    $this->app->ActionHandler("schnelleinlagern", "LagerSchnellEinlagern");
    
    $this->app->ActionHandler("schnellumlagern", "LagerSchnellUmlagern");
    $this->app->ActionHandler("schnellauslagern", "LagerSchnellAuslagern");
    
    $this->app->ActionHandler("differenzen", "LagerDifferenzen");
    $this->app->ActionHandler("differenzenlagerplatz", "LagerDifferenzenLagerplatz");

    $this->erstes=0;

    $id = $this->app->Secure->GetGET('id');
    $nummer = $this->app->Secure->GetPOST('nummer');
    if ($nummer == '' && $id > 0)
    {
      $lager = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$id' LIMIT 1");
    }
    else
    {
      $lager = $nummer;
    }
    $woher = $this->app->Secure->GetPOST('woher');
    $action = $this->app->Secure->GetGET('action');
    $cmd = $this->app->Secure->GetGET('cmd');
    $this->app->erp->Headlines('Lager');
    if ($action === 'bucheneinlagern')
    {
      if ($cmd === 'zwischenlager')
      {
        $lager = 'Zwischenlager';
      }
      else {
        $lager = 'Manuelle Lageranpassung';
      }
    }
    $this->app->Tpl->Set('UEBERSCHRIFT', 'Lager: ' . $lager);
    $this->app->ActionHandlerListen($app);
  }
  
  public function Install()
  {
    $this->cleanUpInvalidStorages();
  }

  public function cleanUpInvalidStorages(): void
  {
    $hasStorageLocationWithEmptyNameOrStorage = $this->app->DB->Select(
      "SELECT COUNT(`id`) FROM `lager_platz` WHERE (`kurzbezeichnung` = '' OR `lager` = 0) LIMIT 1"
      ) > 0;
    if($hasStorageLocationWithEmptyNameOrStorage) {
      $this->app->DB->Delete(
        "DELETE `lp`
        FROM `lager_platz` AS `lp`
        LEFT JOIN `lager_platz_inhalt` AS `lpi` ON lp.id = lpi.lager_platz
        WHERE (lp.kurzbezeichnung = '' OR `lager` = 0) AND lpi.id IS NULL"
      );
    }
    $hasStorageLocationsWithZeroId = $this->app->DB->Select("SELECT COUNT(`id`) FROM `lager_platz` WHERE `id` = 0") > 0;
    if($hasStorageLocationsWithZeroId) {
      $this->app->DB->Delete("DELETE FROM `lager_platz` WHERE `id` = 0");
      $this->app->erp->CheckAlterTable(
        "ALTER TABLE `lager_platz` ADD PRIMARY KEY (`id`)"
      );
      $this->app->erp->CheckAlterTable("ALTER TABLE `lager_platz` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");
      if(!empty($this->app->DB->error())) {
        $this->app->erp->repairNullIds('lager_platz', 'kurzbezeichnung');
      }
    }

    $hasStoragesWithZeroId = $this->app->DB->Select("SELECT COUNT(`id`) FROM `lager` WHERE `id` = 0") > 0;
    if($hasStoragesWithZeroId) {
      $this->app->DB->Delete(
        "DELETE `l`
        FROM `lager` AS `l`
        LEFT JOIN `lager_platz` AS `lp` ON l.id = lp.lager AND lp.geloescht = 0
        WHERE l.id = 0 AND lp.id IS NULL"
      );
      $hasStoragesWithZeroId = $this->app->DB->Select("SELECT COUNT(`id`) FROM `lager` WHERE `id` = 0") > 0;
      if(!$hasStoragesWithZeroId) {
        $this->app->erp->CheckAlterTable(
          "ALTER TABLE `lager` ADD PRIMARY KEY (`id`)"
        );
        $this->app->erp->CheckAlterTable("ALTER TABLE `lager` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");
        if(empty($this->app->DB->error())) {
          return;
        }
      }
      $this->app->erp->repairNullIds('lager', 'bezeichnung');
    }
    $this->removeDoubledNamedStorageLocations();
    $this->removeDoubledNamedStorages();
  }

  public function removeDoubledNamedStorages(): void
  {
    $storageWithSameName = $this->app->DB->SelectFirstCols(
      "SELECT `bezeichnung`
      FROM `lager`
      WHERE `geloescht` = 0 OR `geloescht` IS NULL 
      GROUP BY `bezeichnung`
      HAVING COUNT(`id`) > 1"
    );
    if(empty($storageWithSameName)) {
      return;
    }
    $nameString = implode("', '", array_map([$this->app->DB, 'real_escape_string'], $storageWithSameName));
    $storageWithSameName = $this->app->DB->SelectArr(
      "SELECT l.id, l.bezeichnung, lp.lager
      FROM `lager` AS `l`
      LEFT JOIN (
          SELECT `lager` FROM `lager_platz` WHERE `geloescht` = 0 OR `geloescht` IS NULL GROUP BY `lager`
      ) AS `lp` ON l.id = lp.lager
      WHERE (l.geloescht IS NULL OR l.geloescht = 0) AND l.bezeichnung IN ('{$nameString}') 
      ORDER BY l.bezeichnung, lp.lager DESC"
    );
    if(empty($storageWithSameName)) {
      return;
    }
    $lastStorageName = null;
    foreach($storageWithSameName as $storage) {
      if($storage['bezeichnung'] !== $lastStorageName) {
        $lastStorageName = $storage['bezeichnung'];
        continue;
      }
      if(!empty($storage['lager'])) {
        continue;
      }
      $this->app->DB->Delete("UPDATE `lager` SET `geloescht` = 1 WHERE `id` = {$storage['id']}");
    }
  }

  public function removeDoubledNamedStorageLocations(): void
  {
    $storageLocationsWithSameName = $this->app->DB->SelectFirstCols(
      "SELECT `kurzbezeichnung`
      FROM `lager_platz`
      WHERE `geloescht` = 0 OR `geloescht` IS NULL 
      GROUP BY `kurzbezeichnung`
      HAVING COUNT(`id`) > 1"
    );
    if(empty($storageLocationsWithSameName)) {
      return;
    }
    $nameString = implode("', '", array_map([$this->app->DB, 'real_escape_string'], $storageLocationsWithSameName));
    $storageLocationsWithSameName = $this->app->DB->SelectArr(
      "SELECT lp.id, lp.kurzbezeichnung, lpi.lager_platz
      FROM `lager_platz` AS `lp`
      LEFT JOIN (
          SELECT `lager_platz` FROM `lager_platz_inhalt` GROUP BY `lager_platz`
      ) AS `lpi` ON lp.id = lpi.lager_platz
      WHERE (lp.geloescht IS NULL OR lp.geloescht = 0) AND lp.kurzbezeichnung IN ('{$nameString}') 
      ORDER BY lp.kurzbezeichnung, lpi.lager_platz DESC"
    );
    if(empty($storageLocationsWithSameName)) {
      return;
    }
    $lastStorageName = null;
    foreach($storageLocationsWithSameName as $storage) {
      if($storage['kurzbezeichnung'] !== $lastStorageName) {
        $lastStorageName = $storage['kurzbezeichnung'];
        continue;
      }
      if(!empty($storage['lager_platz'])) {
        continue;
      }
      $this->app->DB->Delete("UPDATE `lager_platz` SET `geloescht` = 1 WHERE `id` = {$storage['id']}");
    }
  }



  public function LagerSchnellEinlagern()
  {
    $this->LagerBuchenMenu();
    $submit = $this->app->Secure->GetPOST('submit');
    $nummer = $this->app->Secure->GetPOST('nummer');
    $menge = $this->app->Secure->GetPOST('menge');
    $grundreferenz = $this->app->Secure->GetPOST('grundreferenz');

    if($submit!="")
    {

    }

    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer');
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');
    $this->app->Tpl->Parse('TAB1',"lager_schnelleinlagern.tpl");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");
  } 


  function LagerAbsolutInventur($artikel,$lager_platz = false)
  {

    if ($lager_platz) {
      $query = "
      SELECT 
      menge,
      referenz
        FROM
        lager_bewegung
        WHERE 
        artikel='".$artikel."' 
        AND 
        eingang=1 
        AND
        permanenteinventur<=0
        AND 
        referenz LIKE 'Inventur%'
        ";

      $query .= " AND lager_platz = '" . $lager_platz . "' ";
    } else {
      $query = "
      SELECT 
      lb.menge,
      lb.referenz
        FROM
        lager_bewegung lb
        LEFT JOIN lager_platz l ON l.id=lb.lager_platz
        WHERE 
        lb.artikel='".$artikel."' 
        AND 
        lb.eingang=1 
        AND
        lb.permanenteinventur<=0
        AND 
        lb.referenz LIKE 'Inventur%'
        AND l.verbrauchslager!=1
        ";
    }
    $eingang = 0;
    $ausgang = 0;
    $result = $this->app->DB->SelectArr($query);
    $cresult = !empty($result)?count($result):0;
    for($i=0;$i<$cresult;$i++)
    {
      $tmp_eingang = trim(str_replace('neu:','',strstr ( $result[$i]['referenz'] , "neu:", false)));
      if($tmp_eingang != $result[$i]['menge'])
      {
        // TODO alte Menge auf korrekte neue setzten!
        $eingang += $tmp_eingang;
      } else {
        $eingang += $result[$i]['menge'];
      }

      $pattern = '/alt:(.*?)neu:/';
      preg_match($pattern, $result[$i]['referenz'], $matches);
      $tmp_ausgang = trim($matches[1]);

      if(is_numeric($tmp_ausgang))
      {
        $ausgang += $tmp_ausgang;
      }
    }
    return $eingang - $ausgang;//$this->app->DB->Select("SELECT SUM(menge) FROM lager_bewegung WHERE artikel='".$artikelarr[$i]."' AND eingang=1");
  }

  /**
   * @param int  $artikel
   * @param bool $lager_platz
   *
   * @return float|null
   */
  function LagerAbsolutEingang($artikel, $lager_platz = false)
  {

   if ($lager_platz) {
    $query = "
      SELECT 
      SUM(menge) 
      FROM 
      lager_bewegung 
      WHERE 
      artikel='".$artikel."' 
      AND 
      eingang=1 
      AND
      permanenteinventur<=0
      AND 
      referenz 
      NOT LIKE 'Inventur%'
      ";
      $query .= " AND lager_platz = '" . $lager_platz . "' ";
    } else {
      $query = "
      SELECT 
      SUM(lb.menge) 
      FROM 
      lager_bewegung lb
      LEFT JOIN lager_platz l ON l.id=lb.lager_platz
      WHERE 
      lb.artikel='".$artikel."' 
      AND 
      lb.eingang=1 
      AND
      lb.permanenteinventur<=0
      AND 
      lb.referenz 
      NOT LIKE 'Inventur%'
      AND 
      l.verbrauchslager!=1
      ";
    }

    return $this->app->DB->Select($query);
  }

  /**
   * @param int  $artikel
   * @param bool $lager_platz
   *
   * @return float|null
   */
  function LagerAbsolutAusgang($artikel, $lager_platz = false)
  {
    if ($lager_platz) {
      $query = "
      SELECT 
      SUM(menge) 
      FROM 
      lager_bewegung 
      WHERE 
      artikel='".$artikel."' 
      AND 
      eingang=0 
      AND
      permanenteinventur<=0
      AND 
      referenz 
      NOT LIKE 'Inventur%'
      ";


      $query .= " AND lager_platz = '" . $lager_platz . "' ";
    } else {

  $query = "
      SELECT 
      SUM(lb.menge) 
      FROM 
      lager_bewegung lb
      LEFT JOIN lager_platz l ON l.id=lb.lager_platz
      WHERE 
      lb.artikel='".$artikel."' 
      AND 
      lb.eingang=0
      AND
      lb.permanenteinventur<=0
      AND 
      lb.referenz 
      NOT LIKE 'Inventur%'
      AND 
      l.verbrauchslager!=1
      ";


      }

    return $this->app->DB->Select($query);
  }

  public function LagerDifferenzen()
  {
    $this->LagerHauptmenu();
      $this->app->Tpl->Set('VERS','Enterprise');
      $this->app->Tpl->Set('MODUL','Enterprise');
      $this->app->Tpl->Parse('PAGE', "only_version.tpl");
  } 

  public function LagerDifferenzenLagerplatz() {

    $this->LagerHauptmenu();
      $this->app->Tpl->Set('VERS','Enterprise');
      $this->app->Tpl->Set('MODUL','Enterprise');
      $this->app->Tpl->Parse('PAGE', "only_version.tpl");

  }


  public function LagerSchnellUmlagern()
  {
    $this->LagerBuchenMenu();
    $submit = $this->app->Secure->GetPOST('submit');
    $nummer = $this->app->Secure->GetPOST('nummer');
    $get_nummer = $this->app->Secure->GetGET('nummer');
    $menge = $this->app->Secure->GetPOST('menge');
    $grundreferenz = $this->app->Secure->GetPOST('grundreferenz');
    $ziellager = $this->app->Secure->GetPOST('ziellager');

    if($get_nummer!=''){
      $this->app->Tpl->Set('FOCUS', 'ziellager');
    }
    else{
      $this->app->Tpl->Set('FOCUS', 'nummer');
    }


    $lager_platz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$ziellager' AND kurzbezeichnung!='' LIMIT 1");     
    if($lager_platz<=0 && $ziellager > 0)
    {
      $lager_platz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE id='$ziellager' LIMIT 1");      
      $ziellager = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lager_platz' LIMIT 1");     
    }

    if($grundreferenz!='') {
      $this->app->User->SetParameter('lager_schnellumlagern_grund',$grundreferenz);
    }
    if($ziellager!='') {
      $this->app->User->SetParameter('lager_schnellumlagern_ziellager',$ziellager);
    }

    if($submit!='')
    {
      $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND nummer!='' LIMIT 1");      
      if($artikelid <=0)
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' AND geloescht <> 1 LIMIT 1");     
      if($artikelid <=0)
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE herstellernummer='$nummer' AND herstellernummer!='' AND geloescht <> 1 LIMIT 1");     

      $name_de = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$artikelid' LIMIT 1");
      //$projekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$artikelid' LIMIT 1");

      if($artikelid > 0 && $lager_platz > 0)
      {
        $anzahl_artikel = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikelid'");
        if($anzahl_artikel >= $menge)
        {
          // auslagern bevorzugt aus lager_platz ansonsten von den anderen
          $this->app->erp->LagerAutoAuslagernArtikel($artikelid,$menge,$grundreferenz);

          // einlagern lager_platz
          $this->app->erp->LagerEinlagern($artikelid,$menge,$lager_platz,$projekt,$grundreferenz);

          $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Der Artikel $name_de wurde $menge mal umgelagert!</div>");
          $this->app->Location->execute('index.php?module=lager&action=schnellumlagern&msg='.$msg);
        }
        if($anzahl_artikel > 0){
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel ist nur maximal $anzahl_artikel im Lager vorhanden! Bitte korrekte Menge angeben!</div>");
        }
        else{
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel hat keinen Bestand im Lager!</div>");
        }
        $this->app->Location->execute('index.php?module=lager&action=schnellumlagern&msg='.$msg);
      }
      if($artikelid<=0)
      {
        $msg = "<div class=\"error\">Der Artikel mit der Nummer $nummer wurde nicht gefunden!</div>";
        $get_nummer = '';
      }
      else{
        $get_nummer = $nummer;
      }

      if($lager_platz<=0)
      {
        $msg .= "<div class=\"error\">Das Ziellager $ziellager wurde nicht gefunden!</div>";
        $this->app->User->SetParameter("lager_schnellumlagern_ziellager","");
      }
      $msg = $this->app->erp->base64_url_encode($msg);
      $this->app->Location->execute("index.php?module=lager&action=schnellumlagern&msg=$msg&nummer=$get_nummer");
    }
    $msg = $this->app->Secure->GetGET('msg');
    if($msg==''){
      $this->app->Tpl->Set('MESSAGE', '<div class="info">{|Der Artikel wird wenn vorhanden aus dem Standardlager ausgelagert.|}</div>');
    }

    if($grundreferenz==''){
      $grundreferenz = $this->app->User->GetParameter('lager_schnellumlagern_grund');
    }
    if($ziellager=='') {
      $ziellager=$this->app->User->GetParameter('lager_schnellumlagern_ziellager');
    }
    $this->app->Tpl->Set('GRUNDREFERENZ',$grundreferenz);
    $this->app->Tpl->Set('ZIELLAGER',$ziellager);

    $this->app->Tpl->Set('NUMMER',$get_nummer);
    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer',1);
    $this->app->YUI->AutoComplete('ziellager','lagerplatz');
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');
    $this->app->Tpl->Parse('PAGE',"lager_schnellumlagern.tpl");
  }




  public function LagerSchnellAuslagern()
  {
    $this->LagerBuchenMenu();
    $submit = $this->app->Secure->GetPOST('submit');
    $nummer = $this->app->Secure->GetPOST('nummer');
    $menge = $this->app->Secure->GetPOST('menge');
    $grundreferenz = $this->app->Secure->GetPOST('grundreferenz');

    if($grundreferenz!='')
    {
      $this->app->User->SetParameter("lager_schnellauslagern_grund",$grundreferenz);
    }

    if($submit!='')
    {
      $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND nummer!='' LIMIT 1");      
      if($artikelid <=0)
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' AND geloescht <> 1 LIMIT 1");     
      if($artikelid <=0)
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE herstellernummer='$nummer' AND herstellernummer!='' AND geloescht <> 1 LIMIT 1");     

      $name_de = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$artikelid' LIMIT 1");

      if($artikelid > 0)
      {
        $anzahl_artikel = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikelid'");
        if($anzahl_artikel >= $menge )
        {
          // auslagern bevorzugt aus lager_platz ansonsten von den anderen
          $this->app->erp->LagerAutoAuslagernArtikel($artikelid,$menge,$grundreferenz);
          $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Der Artikel $name_de wurde $menge mal ausgelagert!</div>");
          $this->app->Location->execute("index.php?module=lager&action=schnellauslagern&msg=$msg");
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel ist nur maximal $anzahl_artikel im Lager vorhanden! Bitte korrekte Menge angeben!</div>");
        $this->app->Location->execute("index.php?module=lager&action=schnellauslagern&msg=$msg");
      }
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel mit der Nummer $nummer wurde nicht gefunden!</div>");
      $this->app->Location->execute("index.php?module=lager&action=schnellauslagern&msg=$msg");
    } 
    $msg = $this->app->Secure->GetGET('msg');
    if($msg==''){
      $this->app->Tpl->Set('MESSAGE', '<div class="info">Der Artikel wird wenn vorhanden aus dem Standardlager ausgelagert.</div>');
    }
    
    if($grundreferenz=='')
    {
      $grundreferenz=$this->app->User->GetParameter('lager_schnellauslagern_grund');
    }
    $this->app->Tpl->Set('GRUNDREFERENZ',$grundreferenz);

    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer',1);
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');
    $this->app->Tpl->Parse('PAGE','lager_schnellauslagern.tpl');
  } 
  
  public function LagerBestand()
  {
    $this->LagerHauptmenu();
    $this->app->erp->MenuEintrag('index.php?module=lager&action=list','zur&uuml;ck zur &Uuml;bersicht');
    $this->app->erp->Headlines('','Bestand');
    $this->app->YUI->TableSearch('TAB1', 'lager_bestand', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','lager_bestand.tpl');
  }


  public function LagerWert()
  {
      $this->LagerHauptmenu();
      $this->app->Tpl->Set('VERS','Professional');
      $this->app->Tpl->Set('MODUL','Professional');
      $this->app->Tpl->Parse('PAGE', 'only_version.tpl');
  }

  public function LagerBuchenZwischenlagerDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->DB->Delete("DELETE FROM zwischenlager WHERE id='$id' LIMIT 1");
    }
    $this->app->Location->execute('index.php?module=lager&action=buchenzwischenlager&top=TGFnZXI=');
  }


  public function LagerPlatzDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    //if(is_numeric($id))
    //  $this->app->DB->Delete("DELETE FROM lager WHERE id='$id' LIMIT 1");

    $numberofarticles = $this->app->DB->Select(
      sprintf(
        'SELECT id FROM lager_platz_inhalt WHERE lager_platz=%d LIMIT 1',
        (int)$id
      )
    );
    $lager = 0;
    if($numberofarticles > 0)
    {
      $msg = $this->app->erp->base64_url_encode('<div class="error">In diesem Lager existieren Artikel. Es k&ouml;nnen nur leere Lagerpl&auml;tze bzw. Lager ohne Lagerpl&auml;tze gel&ouml;scht werden!</div>');
    }
    else {
      if($id > 0){
        $lager = $this->app->DB->Select(
          sprintf('SELECT lager FROM lager_platz WHERE id = %d LIMIT 1',(int)$id)
        );
        $this->app->DB->Delete(
          sprintf('DELETE FROM lager_platz WHERE id=%d LIMIT 1', (int)$id)
        );
        $this->app->DB->Update(
          sprintf('UPDATE artikel SET lager_platz=0 WHERE lager_platz = %d', (int)$id)
        );
      }
      $msg = $this->app->erp->base64_url_encode('<div class="error2">Das Regal wurde gel&ouml;scht!</div>');
    }

    $ref = $_SERVER['HTTP_REFERER'];
    if(($pos = strpos($ref,'&msg=')) > 0)
    {
      $ref = substr($ref, 0, $pos);
    }
    if(empty($ref)) {
      $ref = 'index.php?module=lager&action=platz&id='.$lager;
    }

    $this->app->Location->execute($ref.'&msg='.$msg);
  }


  public function LagerDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    
    $numberofarticles = $this->app->DB->Select(
      sprintf('SELECT id FROM lager_platz WHERE lager=%d LIMIT 1', (int)$id)
    );

    if($numberofarticles > 0)
    {
      $msg = $this->app->erp->base64_url_encode('<div class="error">In diesem Lager existieren noch Lagerpl&auml;tze. Es k&ouml;nnen nur leere Lager gel&ouml;scht werden!</div>');
    }
    else {
      $this->app->DB->Select(sprintf('DELETE FROM lager WHERE id=%d LIMIT 1', (int)$id));
      $msg = $this->app->erp->base64_url_encode('<div class="error2">Das Lager wurde gel&ouml;scht!</div>');
    }

    $ref = $_SERVER['HTTP_REFERER'];
    if(($pos = strpos($ref,'&msg=')) > 0) {
      $ref = substr($ref, 0, $pos);
    }
    if(empty($ref)) {
      $ref = 'index.php?module=lager&action=list';
    }
    $this->app->Location->execute($ref.'&msg='.$msg);
  }


  public function LagerArtikelEntfernenReserviert() {
    $reservierung = $this->app->Secure->GetGET('reservierung');
    if (is_numeric($reservierung)) {
      $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE id='$reservierung'");
    }
    $this->app->Location->execute('index.php?module=lager&action=reservierungen');
  }
  
  function LagerBuchen() {;
    $this->LagerBuchenZwischenlager();
  }
  function LagerKalkMenu() {
    $this->app->erp->Headlines('Bestellvorschlag');
    if($this->app->erp->RechteVorhanden('schnellproduktion','bestellvorschlag')) {
      $this->app->erp->MenuEintrag('index.php?module=schnellproduktion&action=bestellvorschlag','Produktionsartikel Bestellvorschlag');
    }
  }


  function LagerBuchenZwischenlager() {
    $this->LagerBuchenMenu();
    //$this->app->Tpl->Set(TABTEXT, "Zwischenlager");
    $this->app->erp->Headlines('Zwischenlager');
    $this->app->Tpl->Set('SUBSUBHEADING', '{|EINGANG Zwischenlager Stand|} ' . date('d.m.Y'));
    // easy table mit arbeitspaketen YUI als template
    if($this->app->User->GetType()==='admin'){
      $delete = "<a href=\"#\" onclick=\"if(!confirm('Artikel wirklich aus dem Zwischenlager nehmen?')) return false; else window.location.href='index.php?module=lager&action=buchenzwischenlagerdelete&id=%value%';\"><img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>";
    }

    /*$table = new EasyTable($this->app);
    $table->Query("SELECT a.name_de as artikel,a.nummer as nummer,z.menge,z.vpe,z.grund, p.abkuerzung as projekt, z.id FROM zwischenlager z LEFT JOIN artikel a ON a.id=z.artikel LEFT JOIN projekt p ON 
        p.id=z.projekt WHERE  z.richtung='eingang'");
    $table->DisplayNew('INHALT', "<a href=\"index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/forward.svg\"></a>&nbsp;$delete");
    */
    $this->app->YUI->TableSearch('INHALT', 'lager_zwischenlager_eingang', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('TAB1', 'rahmen70.tpl');
    $this->app->Tpl->Set('INHALT', '');
    $this->app->Tpl->Set('SUBSUBHEADING', '{|AUSGANG Zwischenlager Stand|} ' . date('d.m.Y'));
    // easy table mit arbeitspaketen YUI als template
    /*$table = new EasyTable($this->app);
    $table->Query("SELECT a.name_de as artikel,z.menge,z.vpe,z.grund, p.abkuerzung as projekt, z.id FROM zwischenlager z LEFT JOIN artikel a ON a.id=z.artikel LEFT JOIN projekt p ON 
        p.id=z.projekt WHERE z.richtung='ausgang' ORDER by z.id DESC");
    $table->DisplayNew('INHALT', "<a href=\"index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/forward.svg\"></a>&nbsp;$delete");
    */
    $this->app->YUI->TableSearch('INHALT', 'lager_zwischenlager_ausgang', 'show','','',basename(__FILE__), __CLASS__);
    
    $this->app->Tpl->Parse('TAB1', 'rahmen70.tpl');
    $this->app->Tpl->Set('AKTIV_TAB1', 'selected');
    $this->app->Tpl->Add('TAB1','<script>
    function DeleteZw(zid)
    {
       if(!confirm(\'Artikel wirklich aus dem Zwischenlager nehmen?\')) return false; else window.location.href=\'index.php?module=lager&action=buchenzwischenlagerdelete&id=\'+zid+\'\';      
    }
    
    </script>');
    $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
  }

  function LagerBuchenEinlagern() {
    session_start();
    $this->LagerBuchenMenu();
    $this->app->erp->Headlines('Lager', 'Einlagern');
    $id = $this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd'); // vom zwischen lager!
    $menge = str_replace(',','.',$this->app->Secure->GetPOST('menge'));
    $submit = $this->app->Secure->GetPOST('submit');
    $back = $this->app->Secure->GetGET('back');
    $vpeid = $this->app->Secure->GetGET('vpeid');
    $chargesnmhdbemerkung = $this->app->Secure->GetPOST('chargesnmhdbemerkung');

    $grund = $this->app->Secure->GetPOST('grund');
    $artikelid = $this->app->Secure->GetGET('artikelid');

    $artikelbeschreibung = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$artikelid' AND lagerartikel=1 LIMIT 1");


    if($back==='artikel' && $artikelid!='')
    {
      $menge_anzeige = $this->app->Secure->GetGET('menge');
      $this->app->Tpl->Set('NEINDOCHNICHTSTART','<!--');
      $this->app->Tpl->Set('NEINDOCHNICHTENDE','-->');
    }

    $this->app->YUI->AutoComplete('projekt','projektname');
    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer',1);
    $this->app->YUI->AutoComplete('regal','lagerplatz');
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');

    if($cmd==='zwischenlager')
    {
      $this->app->Tpl->Set('MENGEREADONLY','readonly');
      $this->app->Tpl->Set('WOHERREADONLYSTART','<!--');
      $this->app->Tpl->Set('WOHERREADONLYENDE','-->');

      $mhd = $this->app->DB->SelectArr("SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE zwischenlagerid='$id'");
      $cmhd = !empty($mhd)?count($mhd):0;
      for($i=1;$i<=$cmhd;$i++)
      {
        $this->app->Tpl->Add('SRNINFO',"<tr><td></td><td>MHD: ".$mhd[$i-1]['mhddatum']."</td></tr>");
        $this->app->Tpl->Add('MHDVALUE',$this->app->String->Convert($mhd[$i-1]['mhddatum'],"%3-%2-%1","%1.%2.%3"));
      }
      $charge = $this->app->DB->SelectArr("SELECT * FROM lager_charge WHERE zwischenlagerid='$id'");
      $ccharge = !empty($charge)?count($charge):0;
      for($i=1;$i<=$ccharge;$i++)
      {
        $this->app->Tpl->Add('SRNINFO',"<tr><td></td><td>Charge: ".$charge[$i-1]['charge']."</td></tr>");
      }
      $srn = $this->app->DB->SelectArr("SELECT * FROM lager_seriennummern WHERE zwischenlagerid='$id'");
      $csrn = !empty($srn)?count($srn):0;
      for($i=1;$i<=$csrn;$i++)
      {
        $this->app->Tpl->Add('SRNINFO',"<tr><td></td><td>Seriennummer: ".$srn[$i-1]['seriennummer']."</td></tr>");
      }

      $this->app->Tpl->Set('SHOWCHRSTART','<!--');
      $this->app->Tpl->Set('SHOWCHREND','-->');
      if(count($mhd) <=0)
      {
        $this->app->Tpl->Set('SHOWMHDSTART','<!--');
        $this->app->Tpl->Set('SHOWMHDEND','-->');
      }
      $this->app->Tpl->Set('SHOWSRNSTART','<!--');
      $this->app->Tpl->Set('SHOWSRNEND','-->');
    } else {
      $this->app->Tpl->Set('WOHERREADONLYSTART2','<!--');
      $this->app->Tpl->Set('WOHERREADONLYENDE2','-->');
    }

    // wenn projekt angeben
    if ($this->app->Secure->GetPOST('projekt') != '')
    {
      $projekt = $this->app->Secure->GetPOST('projekt');
      $projekt = explode(' ', $projekt);
      $projekt = $projekt[0];
      if(!is_numeric($projekt))
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");

      $_SESSION['projekt'] = $projekt;
    }

    $projekt = $_SESSION['projekt'];
    $regal = $this->app->Secure->GetPOST('regal');
    $frmRegal = $regal;

    $submitStorageLocationId = $this->app->Secure->GetGET('submitstoragelocationid');
    $storageLocationId = $this->app->Secure->GetGET('storagelocationid');
    $interimonconsumption = $this->app->Secure->GetGET('interimonconsumption');
    $comment = $this->app->Secure->GetGET('comment');
    $suggestedbestbeforebatch = $this->app->Secure->GetPOST('suggestedbestbeforebatch');
    if(empty($suggestedbestbeforebatch)) {
      $suggestedbestbeforebatch = $this->app->Secure->GetGET('suggestedbestbeforebatch');
    }

    $this->app->Tpl->Set('SUGGESTEDBATCHBEFORE', $suggestedbestbeforebatch);
    $regal_id = 0;
    if((!empty($submitStorageLocationId) || !empty($storageLocationId)) && empty($submit) && empty($regal)) {
      $submit = !empty($submitStorageLocationId);
      $regal_id = !empty($submitStorageLocationId)?$submitStorageLocationId:$storageLocationId;
      if(empty($grund)) {
        $grund = $comment;
      }
      $regal = $this->app->DB->Select(
        sprintf(
          'SELECT kurzbezeichnung FROM lager_platz WHERE id = %d LIMIT 1',
          $regal_id
        )
      );
      $frmRegal = $regal;
    }


    if((String)$regal!=='') {
      $regal_id = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$regal' LIMIT 1");
    }
    
    if(is_numeric($regal_id)){
      $regal = $regal_id;
    }

    $nummer = $this->app->Secure->GetPOST('nummer');
    //$nummer = explode(' ', $nummer);
    //$nummer = $nummer[0];

    if ((String)$nummer === '' && $cmd !== 'zwischenlager' && $artikelid=='') {
      $this->app->Tpl->Set('MSGARTIKEL', "<br>{|Jetzt Artikel abscannen!|}");
      $this->app->Tpl->Set('ARTIKELSTYLE', "style=\"border: 2px solid red;width:200px;\"");
    }

    $woher = $this->app->Secure->GetPOST('woher');
    $zwischenlagerid = $this->app->Secure->GetPOST('zwischenlager');
    $menge = str_replace(',','.',$this->app->Secure->GetPOST('menge'));
 
    $grundreferenz = $this->app->Secure->GetPOST('grundreferenz');
    // hier nur rein wenn artikel lager und projekt sinn machen sonst   
    //message ausgeben und artikel wirklich aus zwischenlager
    $alles_komplett = 0;
    if ($woher === 'Zwischenlager' && $zwischenlagerid <= 0) {
      $grund.= "<li>Artikel kommt nicht aus Zwischenlager!</li>";
      $alles_komplett++;
    }


    $artikel_tmp = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND nummer!='' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
    $ean = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
    if($artikel_tmp <=0 && $ean > 0) 
    { 
      $artikel_tmp = $ean;
      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$ean' AND lagerartikel=1 LIMIT 1");
    }
    $artikelcheck = $this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikel_tmp' AND lagerartikel=1 LIMIT 1");

    $artikel_quickcheck = 0;
    if ($submit !='' && ($artikelcheck != $artikel_tmp || $artikel_tmp == '' || $artikel_tmp == 0)) {
      $grund.= "<li>Artikel-Nummer gibt es nicht!</li>";
      $alles_komplett++;
      $artikel_quickcheck = 1;
    }

    // gibts regal
    $regalcheck = $this->app->DB->Select("SELECT id FROM lager_platz WHERE id='$regal' LIMIT 1");
    if ($regalcheck != $regal || $regal == '' || $regal == 0) {
      $grund.= "<li>Regal gibt es nicht!</li>";
      $alles_komplett++;
    }

    if ($alles_komplett > 0 && $regal != '') {
      $this->app->Tpl->Set('MESSAGELAGER', "<div class=\"error\">Artikel wurde nicht gebucht! Grund:<ul>$grund</ul> </div>");
    } else {
      if ($artikel_quickcheck == 1 && (String)$nummer !== '') {
        $this->app->Tpl->Set('MESSAGELAGER', "<div class=\"error\">Achtung! Artikelnummer  
            gibt es nicht, oder der Artikel ist kein Lagerartikel! </div>");
        $nummer =""; 
      }
    }
    if ((String)$nummer === '' && $cmd == '' && $woher == '') {
      $_SESSION['woher'] = 'Manuelle Lageranpassung';
    }

    $artArr = $this->app->DB->SelectRow(
      sprintf(
        'SELECT chargenverwaltung,mindesthaltbarkeitsdatum, seriennummern 
          FROM artikel 
          WHERE id=%d LIMIT 1',
        (int)$artikel_tmp
      )
    );
    $chargenverwaltung= $artArr['chargenverwaltung'];
    $mindesthaltbarkeitsdatum = $artArr['mindesthaltbarkeitsdatum'];
    $suggestedbatch = '';
    $suggestedbestbefore = '';
    if($mindesthaltbarkeitsdatum && $chargenverwaltung) {
      $suggestedbestbefore = substr($suggestedbestbeforebatch,0,10);
      if(strpos($suggestedbestbefore,'-')!== false) {
        $suggestedbestbefore = $this->app->String->Convert($suggestedbestbefore,'%3-%2-%1','%1.%2.%3');
      }
      $suggestedbatch = ltrim(substr($suggestedbestbeforebatch,10),'-');
    }
    elseif($mindesthaltbarkeitsdatum) {
      $suggestedbestbefore = $suggestedbestbeforebatch;
    }
    elseif($chargenverwaltung) {
      $suggestedbatch = $suggestedbestbeforebatch;
    }


    $seriennummern = $artArr['seriennummern'];
    if($seriennummern != '' && $seriennummern !== 'keine') {
      $menge = (int)$menge;
    }
    // pruefen einlagern

    $error = 0;
    // Pflichtfelder pruefen
    $checkmhd = $this->app->String->Convert($this->app->Secure->GetPOST('mhd'),'%1.%2.%3','%3-%2-%1');
    if($mindesthaltbarkeitsdatum=='1' && !$this->app->erp->CheckDateValidate($checkmhd)) {
      $error++;
    }

    if(($chargenverwaltung=='2' || $chargenverwaltung=="1") && $this->app->Secure->GetPOST('charge')=='' && $zwischenlagerid <=0) {
      $error++;
    }
    if( ($seriennummern !=='keine' && $seriennummern !=='vomprodukt' && $seriennummern !=='eigene' && $seriennummern!='') && $cmd!=='zwischenlager') {
      $tmpcheck = $this->app->Secure->GetPOST("seriennummern");
      for($checkser=0;$checkser < $menge; $checkser++)  {
        if($tmpcheck[$checkser]==''){
          $error++;
        }
      }
    }

    if($submit!='' && $error > 0) {
      $alles_komplett++;
      //$this->app->Tpl->Add('MESSAGE',"<div class=\"error\">Achtung! Bitte alle Pflichtfelder (Regal, MHD, Charge, Seriennummer) ausf&uuml;llen!</div>");
    }

    if ($alles_komplett == 0 && $regal != '') {
      $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
      $name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
      $einheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE id='$artikel' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
      // pruefe ob es einen ek fuers projekt gibt sonst meckern!!!
      //echo "buchen entweder aus zwischenlager, prpoduktion oder so";
      if ($woher === 'Zwischenlager') {
        $this->app->erp->LagerEinlagerVomZwischenlager($zwischenlagerid, $menge, $regal, $projekt,$grundreferenz);

        $gesamt = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' AND lager_platz='$regal'");
        $gesamt_alle = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel'");
        if(is_float($menge) || is_numeric($menge))
        {
          $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Der Artikel $name wurde $menge mal eingelagert. Anzahl Regal: <b>".(float)$gesamt." $einheit</b> Anzahl Komplettbestand alle Regale: <b>".(float)$gesamt_alle." $einheit</b></div>");
        } else {
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Fehler: Unbekannte oder falsche Menge: $menge bei Artikel \"$name\". Der Artikel wurde nicht eingelagert! </div>");
        }
 
        $this->app->Location->execute('index.php?module=lager&action=buchenzwischenlager&msg='.$msg);
      }
      if ($woher === 'Manuelle Lageranpassung'){
        $_SESSION['projekt'] = $projekt;

        $this->app->erp->LagerEinlagernDifferenz($artikel, $menge, $regal, $projekt,$grundreferenz,"",(int)$this->app->Secure->GetGET('vpeid'));

        //$this->app->erp->LogFile("ETIKETT1 A $artikel M $menge R $regal"); //BENE
        //$data['lager_platz_name'] = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$regal'");
        //$this->app->erp->EtikettenDrucker("artikel_klein",1,'artikel',$artikel,$data);

        $gesamt = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' AND lager_platz='$regal'");
        $gesamt_alle = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel'");
        if(is_float($menge) || is_numeric($menge))
        {
          $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Der Artikel $name wurde $menge mal eingelagert. Anzahl Regal: <b>".(float)$gesamt." $einheit</b> Anzahl Komplettbestand alle Regale: <b>".(float)$gesamt_alle." $einheit</b></div>");
        }
        else {
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Fehler: Unbekannte oder falsche Menge: $menge bei Artikel \"$name\". Der Artikel wurde nicht eingelagert! </div>");
        }
        
        // Mindesthaltbarkeitsdatum buchen
        $chargemindest = $this->app->Secure->GetPOST('charge');
        $isInterim = $interimonconsumption && $this->app->DB->Select(
            sprintf(
              'SELECT id FROM lager_platz WHERE id = %d AND verbrauchslager = 1',
              (int)$regal
            )
          );
        if($mindesthaltbarkeitsdatum > 0) {
          $mhd = $this->app->String->Convert($this->app->Secure->GetPOST('mhd'),'%1.%2.%3','%3-%2-%1');
          $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($artikel,$menge,$regal,$mhd,$chargemindest,'','','',trim($chargesnmhdbemerkung.' '.$grundreferenz),0,$isInterim);
        }

        if($chargenverwaltung > 0) {  
          $datum = date('Y-m-d');
          $this->app->erp->AddChargeLagerOhneBewegung($artikel,$menge,$regal,$datum,$chargemindest,$chargesnmhdbemerkung,0,'',0,$isInterim);
        }

        //Seriennummern buchen
        $tmpcheck = $this->app->Secure->GetPOST('seriennummern');


        if($artikelid!=''){
          $this->app->Location->execute('index.php?module=artikel&action=lager&id='.$artikelid.($msg!=""?"&msg=".$msg:""));
        }

        $this->app->Location->execute('index.php?module=lager&action=bucheneinlagern&msg='.$msg);
      }
      // wenn von zwischenlager dann header location nach zwischenlager
      // sonst einlagern
    }

    // kommt direkt vom zwischenlager
    if ($cmd === 'zwischenlager') {
      $_SESSION['woher'] = 'Zwischenlager';
      $zwischenlagerArr = $this->app->DB->SelectRow("SELECT artikel,projekt,menge,vpe FROM zwischenlager WHERE id='$id' LIMIT 1");
      $projekt = $zwischenlagerArr['projekt'];
      $menge = $zwischenlagerArr['menge'];
      $artikel = $zwischenlagerArr['artikel'];
      $artikelArr = $this->app->DB->SelectRow("SELECT nummer,name_de, lager_platz,standardbild FROM artikel WHERE id='$artikel' LIMIT 1");
      $nummer = $artikelArr['nummer'];
      $name_de = $artikelArr['name_de'];
      $lagerplatz =  $artikelArr['lager_platz'];
      $lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lagerplatz' LIMIT 1");
      $vpe = $zwischenlagerArr['vpe'];
      if ($projekt == '' || $projekt == 0) {
        $projekt = 1;
      } // default projekt
      $standardbild = $artikelArr['standardbild'];
      if ($standardbild == '') {
        $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel' LIMIT 1");
      }
      $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr valign=\"top\"><td>Bezeichnung:</td><td>$name_de</td></tr>");
      if ($standardbild > 0) {
        $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr valign=\"top\"><td>Bild:</td><td align=\"center\"><img src=\"index.php?module=dateien&action=send&id=$standardbild\" width=\"110\"></td></tr>");
      }

      if($lagerbezeichnung!='')
      {
        $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr valign=\"top\"><td></td><td><br></td></tr><tr ><td>Regalvorschlag:</td><td><font size=\"5\"><b onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\";>$lagerbezeichnung</b></font></td></tr>");
      } else {
        $lagermeist = $this->app->DB->SelectRow("SELECT lager_platz, SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' GROUP BY lager_platz ORDER by 2 DESC LIMIT 1");
        $lagerplatz = $lagermeist['lager_platz'];
        $lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$lagermeist['lager_platz']}' LIMIT 1");
        //$lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1");
        //$lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lagerplatz' LIMIT 1");

        if ($lagerplatz == '' || $lagerplatz == 0) {
          $lagerbezeichnung = 'Regal frei w&auml;hlen';
        }

        $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr valign=\"top\"><td></td><td><br></td></tr><tr ><td>Regalvorschlag:</td><td><font size=\"5\"><b onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\";>$lagerbezeichnung</b></font></td></tr>");

      }
      $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr valign=\"top\"><td><br><br><b>Regal:</b></td><td><br><br><input type=\"text\" name=\"regal\" id=\"regal\" style=\"border: 2px solid;width:200px;\"><br>Jetzt Regal abscannen!</td></tr>
          <input type=\"hidden\" name=\"zwischenlager\" value=\"$id\">");
      $this->app->Tpl->Add('ZWISCHENLAGERINFO', '<script type="text/javascript">
          document.getElementById("regal").focus();
          </script>');



    } else {

      if (($menge == '' || $menge == 0) && $cmd!=='umlagern') {
        $menge = 1;
      }

      if ($this->app->Secure->GetPOST('woher') != '') {
        $_SESSION['woher'] = $this->app->Secure->GetPOST('woher');
      }

      if ((String)$this->app->Secure->GetPOST('nummer') !== '' || $artikelid > 0) {
        $nummer = $this->app->Secure->GetPOST('nummer');
        //$nummer = explode(' ', $nummer);
        //$nummer = $nummer[0];

        if($artikelid > 0){
          $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikelid' LIMIT 1");
          $this->app->Tpl->Set('NUMMER', $nummer);
        }

        $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");

        $ean = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
        if($artikel <=0 && $ean > 0) { 
          $artikel = $ean;
          $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$ean' LIMIT 1");
        }

        if($artikel > 0)
        {
          $articleRow = $this->app->DB->SelectRow(
            sprintf(
              'SELECT name_de, lager_platz, einheit FROM artikel WHERE id = %d',
              $artikel
            )
          );
          $name_de = $articleRow['name_de'];
          $lagermeist = $this->app->DB->SelectArr("SELECT lager_platz, SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' GROUP BY lager_platz ORDER by 2 DESC LIMIT 1");
          $lagermeist = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$lagermeist[0]['lager_platz']}' LIMIT 1");
          //$name_de = $articleRow['lager_platz'];
          $einheit = $articleRow['einheit'];
          $lagerplatz = $articleRow['lager_platz'];// $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1");
          $lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lagerplatz' LIMIT 1");
          if ($lagerplatz == '' || $lagerplatz == 0) {
            $lagerbezeichnung = 'Regal frei w&auml;hlen';
          }
          //$vpe  = $this->app->DB->Select("SELECT vpe FROM artikel WHERE id='$id' LIMIT 1");
          $vpe = 'einzeln';
          //$projekt = $this->app->DB->Select("SELECT projekt FROM zwischenlager WHERE id='$id' LIMIT 1");
          if ($projekt == '' || $projekt == 0) {
            $projekt = 1; // default projekt
          }


          if($chargenverwaltung !='2' && $chargenverwaltung !='1')
          {
            $this->app->Tpl->Set('SHOWCHRSTART','<!--');
            $this->app->Tpl->Set('SHOWCHREND','-->');
          } else {
            //        $this->app->YUI->DatePicker("mhd");
            $frmcharge = $this->app->Secure->GetPOST('charge');
            $this->app->Tpl->Set('CHARGEVALUE', !empty($frmcharge)?$frmcharge:$suggestedbatch);
          }


          if($mindesthaltbarkeitsdatum !='1')
          {
            $this->app->Tpl->Set('SHOWMHDSTART','<!--');
            $this->app->Tpl->Set('SHOWMHDEND','-->');
          } else {
            $this->app->YUI->DatePicker('mhd');
            $frmmhd = $this->app->Secure->GetPOST('mhd');
            if(empty($frmmhd)) {
              $frmmhd = $suggestedbestbefore;
            }
            $this->app->Tpl->Set('MHDVALUE', $frmmhd);
          }


          if($seriennummern === 'keine' || $seriennummern ==='vomprodukt' || $seriennummern ==='eigene' || $menge <= 0 ||  $seriennummern=='')
          {
            $this->app->Tpl->Set('SHOWSRNSTART','<!--');
            $this->app->Tpl->Set('SHOWSRNEND','-->');
          } else {
            // Generator felder fuer seriennummern
            $this->app->Tpl->Add('SERIENNUMMERN','<table><tr><td>Nr.</td><td>{|Seriennummer|}</td></tr>');
            $tmp = $this->app->Secure->GetPOST("seriennummern");
            for($ij=1;$ij<=$menge;$ij++)
            {
              $value = $tmp[$ij-1];
              $this->app->Tpl->Add('SERIENNUMMERN',"<tr><td>$ij</td><td><input type=\"text\" name=\"seriennummern[]\" id=\"seriennummern_$ij\" size=\"30\" value=\"$value\"></td></tr>");
              $this->app->Tpl->Add('SERIENNUMMERNENTERJUMP', "document.getElementById('seriennummern_$ij').addEventListener('keypress', function(event) {if (event.keyCode == 13) {event.preventDefault();document.getElementById(".($ij < $menge?"'seriennummern_".($ij+1)."'":"'projekt'").").focus()}});");
            }
            $this->app->Tpl->Add('SERIENNUMMERN','</table>');
          }

          $standardbild = $this->app->erp->GetArtikelStandardbild($artikel,true);

          if ($standardbild > 0)
            $this->app->Tpl->Set('STANDARDBILD', "<tr valign=\"top\"><td>{|Bild|}:</td><td><img src=\"index.php?module=dateien&action=send&id=$standardbild\" width=\"110\"></td></tr>");

          if(trim($einheit) != ''){
            $name_de .= " (Einheit: ".$einheit.")";
          }

          $this->app->Tpl->Set('NAMEDE',$name_de);
          if($lagermeist!="" || $lagermeist!=0){
            $this->app->Tpl->Set('LAGERMEIST',"<b onclick=\"document.getElementById('regal').value='$lagermeist'\";>$lagermeist</b> ({|aktuell am meisten im Lager|})");
            if($lagerbezeichnung!='' && $lagerbezeichnung!=='Regal frei w&auml;hlen')
              $this->app->Tpl->Add('LAGERMEIST',"<br><b onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\";>$lagerbezeichnung</b> ({|Standardlager|})");
          } else {
            $this->app->Tpl->Set('LAGERBEZEICHNUNG',"<b onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\";>$lagerbezeichnung</b>");

          }

          $this->app->Tpl->Set('REGALVALUE',$frmRegal);

          $this->app->Tpl->Parse('ZWISCHENLAGERINFO', 'lager_regal.tpl');
        } else {

          //falsche artikelnummer 
          $nummer = '';
          $this->app->Tpl->Set('MSGARTIKEL', '<br>{|Jetzt Artikel abscannen!|}');
          $this->app->Tpl->Set('ARTIKELSTYLE', 'style="border: 2px solid red"');
          $this->app->Tpl->Set('ZWISCHENLAGERINFO', '<script type="text/javascript">document.getElementById("nummer").focus();</script>');
          $this->app->Tpl->Set('SHOWCHRSTART','<!--');
          $this->app->Tpl->Set('SHOWCHREND','-->');
          
          $this->app->Tpl->Set('SHOWMHDSTART','<!--');
          $this->app->Tpl->Set('SHOWMHDEND','-->');
          $this->app->Tpl->Set('SHOWSRNSTART','<!--');
          $this->app->Tpl->Set('SHOWSRNEND','-->');
        }
      } else {
        $this->app->Tpl->Set('ZWISCHENLAGERINFO', '<script type="text/javascript">document.getElementById("nummer").focus();</script>');
        if($artikel <=0)
        {
          $this->app->Tpl->Set('SHOWCHRSTART','<!--');
          $this->app->Tpl->Set('SHOWCHREND','-->');
          $this->app->Tpl->Set('SHOWMHDSTART','<!--');
          $this->app->Tpl->Set('SHOWMHDEND','-->');
          $this->app->Tpl->Set('SHOWSRNSTART','<!--');
          $this->app->Tpl->Set('SHOWSRNEND','-->');
        }
      }
    }
    $this->app->Tpl->Set('NAME', $name_de);
    if (!isset($_SESSION['woher']) || $_SESSION['woher'] == '') {
      $_SESSION['woher'] = 'Manuelle Lageranpassung';
    }
    if ($_SESSION['woher'] === "Zwischenlager") {
      $this->app->Tpl->Set('ZWISCHENLAGER', "selected");
    }
    if ($_SESSION['woher'] === "Produktion") {
      $this->app->Tpl->Set('PRODUKTION', "selected");
    }
    if ($_SESSION['woher'] === "Manuelle Lageranpassung") {
      $this->app->Tpl->Set('DIFFERENZ', "selected");
    }
    if ($_SESSION['woher'] === "Umlagern") {
      $this->app->Tpl->Set('UMLAGERN', "selected");
    }
    $projekt = $_SESSION['projekt'];

    if($cmd==='umlagern' && $this->app->Secure->GetPOST('menge')=='')
      $menge = (float)str_replace(',','.',$this->app->Secure->GetGET('menge'));

    if($cmd==='umlagern' && $this->app->Secure->GetPOST('grund')=='')
      $grundreferenz = $this->app->erp->base64_url_decode($this->app->Secure->GetGET('grund'));

    if(empty($grundreferenz) && !empty($comment))
    {
      $grundreferenz = $comment;
    }

    // aus zwischenlager
    $menge_get = str_replace(',','.',$this->app->Secure->GetGET('menge'));
    if($menge_get  > 0) {
      $menge = $menge_get;
    }


    $this->app->Tpl->Set('MENGE', $this->app->erp->ReplaceMenge(0,(String)$menge,0));
    $this->app->Tpl->Set('GRUNDREFERENZ', $grundreferenz);
    $this->app->Tpl->Set('NUMMER', $nummer);

    $this->app->Tpl->Set('VPE', $vpe);
    $pr_name = $this->app->DB->Select("SELECT CONCAT(abkuerzung) FROM projekt WHERE id='$projekt' LIMIT 1");
    $this->app->Tpl->Set('PROJEKT', $pr_name);//$this->app->erp->GetProjektSelect($projekt, &$color_selected));
    //$this->app->Tpl->Set(TABTEXT, "Einlagern");

    $this->app->Tpl->Parse('TAB1', "einlagern.tpl");
    $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
  }

  function LagerBuchenAuslagern() {
    $this->LagerBuchenMenu();
    $cmd = $this->app->Secure->GetGET('cmd');
    $action = $this->app->Secure->GetGET('action');
    $error = 0;

    $this->app->Tpl->Set('CMD',$cmd);
    $this->app->Tpl->Set('ACTION',$action);


    if($this->app->erp->Version()==='stock')
    {
      $this->app->Tpl->Set('STARTDISABLESTOCK', '<!--');
      $this->app->Tpl->Set('ENDEDISABLESTOCK', '-->');
    }


    session_start();
    if($cmd==='umlagern') {
      //$this->app->Tpl->Set(TABTEXT, "Auslagern");

      $this->app->Tpl->Set('STARTNICHTUMLAGERN', '<!--');
      $this->app->Tpl->Set('ENDENICHTUMLAGERN', '-->');

    }
    else {
      //$this->app->Tpl->Set(TABTEXT, "Auslagern");
      $this->app->Tpl->Set('STARTUMLAGERN', '<!--');
      $this->app->Tpl->Set('ENDEUMLAGERN', '-->');
    }

    $this->app->Tpl->Set('FOCUSFIELD','document.getElementById("nummer").focus();');
    $this->app->erp->Headlines('Lager', 'Auslagern');
    // checken ob die daten passen
    $nummer = $this->app->Secure->GetPOST('nummer');
    $grund = $this->app->Secure->GetPOST('grund');
    $grundreferenz = $this->app->Secure->GetPOST('grundreferenz');
    $adresse = $this->app->Secure->GetPOST('adresse');
    $projekt = $this->app->Secure->GetPOST('projekt');
    $menge = str_replace(',','.',$this->app->Secure->GetPOST('menge'));
    $submit = $this->app->Secure->GetPOST('submit');
    $artikelid = $this->app->Secure->GetGET('artikelid');
    $regal = $this->app->Secure->GetPOST('regal');
    $regalneu = $this->app->Secure->GetPOST("regalneu");
    $comment = $this->app->Secure->GetGET('comment');
    $amount = $this->app->Secure->GetGET('amount');
    if(empty($amount)) {
      $amount = $this->app->Secure->GetGET('menge');
    }
    if(empty($grundreferenz)) {
      $grundreferenz = $comment;
    }
    $frmRegal = $regal;
    $allowSubmit = true;
    $submitStorageLocationId = $this->app->Secure->GetGET('submitstoragelocationid');
    $interimonconsumption = $this->app->Secure->GetGET('interimonconsumption');
    $storageLocationId = $this->app->Secure->GetGET('storagelocationid');
    $regal_id = 0;
    if((!empty($submitStorageLocationId) || !empty($storageLocationId)) && empty($submit) && empty($regal)) {
      $submit = !empty($submitStorageLocationId)?'1':'';
      if(!$submit && empty($this->app->Secure->POST)) {
        $allowSubmit = false;
      }
      $regal_id = !empty($submitStorageLocationId)?$submitStorageLocationId:$storageLocationId;
      $regal = $this->app->DB->Select(
        sprintf(
          'SELECT kurzbezeichnung FROM lager_platz WHERE id = %d LIMIT 1',
          $regal_id
        )
      );
      $frmRegal = $regal;
    }

    $frmMenge = $menge;
    if ($menge == '' || $menge == '0') {
      $menge = 1;
    }
    //session_close();


    if($projekt!=''){
      $_SESSION['projekt'] = $projekt;
    }

    $projekt= $_SESSION['projekt'];

    //  $nummer = explode(' ', $nummer);
    //    $nummer = $nummer[0];

    if ($this->app->Secure->GetPOST('nummer') != '' || $artikelid > 0) {
      $nummer = $this->app->Secure->GetPOST('nummer');
      //$nummer = explode(' ', $nummer);
      //$nummer = $nummer[0];

      if($artikelid > 0){
        $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikelid' AND lagerartikel=1 LIMIT 1");
        $checkartikel = $this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikelid' AND lagerartikel=1 LIMIT 1");
        $artikel = $artikelid;
        if($allowSubmit) {
          $submit='1';
        }
        $this->app->Tpl->Set('NUMMER', $nummer);
      }
    }

    $projekt = explode(' ', $projekt);
    $projekt = $projekt[0];

    if(empty($regal_id)){
      $regal_id = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$regal' AND kurzbezeichnung!='' LIMIT 1");
    }
    if(is_numeric($regal_id))
      $regal = $regal_id;

    $regalneu_id = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$regalneu' AND kurzbezeichnung!='' LIMIT 1");
    if(is_numeric($regalneu_id))
      $regalneu = $regalneu_id;

    if ($submit != '') {
      //projekt pruefen

      $checkprojekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      if ($projekt == "" || $checkprojekt != $projekt) {
        //$error++;
        //$this->app->Tpl->Set(MSGPROJEKT,"<font color=\"red\">Projekt gibt es nicht!</font>");
        $projektid = $this->app->DB->Select("SELECT standardprojekt  FROM firma WHERE id='" . $this->app->User->GetFirma() . "' LIMIT 1");
      }

      //adresse pruefen
      $adressearray = explode(' ', $adresse);
      $checkadresse = $this->app->DB->Select("SELECT id FROM adresse WHERE id='{$adressearray[0]}' LIMIT 1");
      $checkname = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$adressearray[0]}' LIMIT 1");

      /*
         if (!is_numeric($adressearray[0]) || $adressearray[0] != $checkadresse) {
         $error++;
         $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Bitte eine g&uuml;ltige Adresse angeben!</div>");
         }
       */

      if (!is_numeric($menge) || $menge == 0) {
        $error++;
        $this->app->Tpl->Set('MSGMENGE', "<font color=\"red\">Wert ist keine Zahl oder Null.</font>");
      }
      $ean = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
      $artikel_tmp = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
      if($artikel_tmp <=0 && $ean > 0)
      {
        $artikel_tmp = $ean;
        $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$ean' LIMIT 1");
      }

      $checkartikel = $this->app->DB->Select("SELECT nummer FROM artikel WHERE nummer='{$nummer}' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
      $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='{$nummer}' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");

      $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE nummer='{$nummer}' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
      $einheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE nummer='{$nummer}' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
      $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM artikel WHERE nummer='{$nummer}' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
      if($seriennummer === 'keine') {
        $seriennummer = '';
      }

      if ($nummer != $checkartikel && ($nummer!=''||$nummer!=0)) {
        $error++;
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">{|Diese Artikelnummer gibt es nicht, oder der Artikel ist kein Lagerartikel!|}</div>");
        $nummer = '';

      }
      //z.B. es liegen 1 1 5 und man will 6 haben
      $checkregal = $this->app->DB->Select("SELECT id FROM lager_platz WHERE id='$regal' LIMIT 1");
      $checkregalneu = $this->app->DB->Select("SELECT id FROM lager_platz WHERE id='$regalneu' LIMIT 1");
      if (($regal != '' && $checkregal == $regal) && $error == 0) {
        //regal gibt schon mal liegt jetzt der artikel noch in diesem regal?
        $summe = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE lager_platz='$regal' AND artikel='$artikel'");
        if ($summe <= 0) {
          $this->app->Tpl->Set('MESSAGELAGER', "<div class=\"error\">{|Artikel gibt es in diesem Regal nicht!|}</div>");
        } else if ($summe < $menge) {
          $this->app->Tpl->Set('MESSAGELAGER', "<div class=\"error\">{|Zu wenig Artikel im Regal! Bitte kleinere Menge w&auml;hlen!|} ({|Summe|}: $summe)</div>");
        } else {
          // zeige alle in dem Lager an sortiert nach MHD
          $tmpsrn = false;
          $anzmhd = 0;
          $anzchargen = 0;
          $anzsrn = 0;
          $tmpmhd = $this->app->DB->SelectArr("SELECT *, replace(trim(menge)+0,'.',',')  as mhdmenge FROM lager_mindesthaltbarkeitsdatum WHERE 
              lager_platz='$regal' AND artikel='$artikel' ORDER by mhddatum, id");
          $tmpcharge = $this->app->DB->SelectArr("SELECT *, replace(trim(menge)+0,'.',',')  as cmenge FROM lager_charge WHERE 
              lager_platz='$regal' AND artikel='$artikel' ORDER by charge, id");
          if($tmpmhd) {
            foreach($tmpmhd as $v) {
              $anzmhd += $v['menge'];
            }
          }
          if($tmpcharge) {
            foreach($tmpcharge as $v) {
              $anzchargen += $v['menge'];
            }
          }

          $this->app->Tpl->Set('ANZSRN', $anzsrn);
          $this->app->Tpl->Set('ANZMHD', $anzmhd);
          $this->app->Tpl->Set('ANZCHARGEN', $anzchargen);
          $this->app->Tpl->Set('SUMME', $summe);
          if($anzsrn > 0)  { 
            $this->app->Tpl->Add('SRNINFO',"<tr><td></td><td>{|MHD|}</td><td>{|Seriennummer|}</td><td>{|Charge|}</td></tr>");
          } else if (count($tmpmhd) > 0) {
            $this->app->Tpl->Add('SRNINFO',"<tr><td></td><td>{|Mindesthalt.|}</td><td width=30></td><td>{|Charge|}</td></tr>");
          } else if (count($tmpcharge) > 0) {
            $this->app->Tpl->Add('SRNINFO',"<tr><td></td><td>{|Charge|}</td></tr>");
          }
          $artikelArr = $this->app->DB->SelectRow(
            sprintf(
              'SELECT seriennummern,chargenverwaltung,mindesthaltbarkeitsdatum FROM artikel WHERE id = %d LIMIT 1',
              (int)$artikel
            )
          );
          $check_seriennummer = $artikelArr['seriennummern'];
          if($check_seriennummer !== 'keine' && $check_seriennummer != '') {
            $menge = (int)$menge;
          }
          $check_charge = $artikelArr['chargenverwaltung'];
          $check_mhd = $artikelArr['mindesthaltbarkeitsdatum'];
          $regaltreffer='1';

          if($check_seriennummer==='vomprodukteinlagern')
          {
            $ctmpsrn= !empty($tmpsrn)?count($tmpsrn):0;
            for($y=0;$y<$ctmpsrn;$y++)
            {
              $regaltreffer="1";
              if($y < $menge) {
                $checked='checked';
              } else {
                $checked='';
              }

              if($tmpsrn[$y]['mhddatum']=='') {
                $tmpsrn[$y]['mhddatum'] = ' - ';
              } else {
                $tmpsrn[$y]['mhddatum'] = $this->app->String->Convert($tmpsrn[$y]['mhddatum'],'%1-%2-%3','%3.%2.%1');
              }
              if($tmpsrn[$y]['seriennummer']=='') {
                $tmpsrn[$y]['seriennummer'] = ' - ';
              }
              if($tmpsrn[$y]['charge']=='') {
                $tmpsrn[$y]['charge'] = ' - ';
              }

              $this->app->Tpl->Add('SRNINFO',"<tr>
                  <td><input type=\"checkbox\" onclick=\"countChecks(this)\" name=\"lager_srn_id[]\" value=\"".$tmpsrn[$y]['id']."\" $checked>&nbsp;$out</td>
                  <td>".$tmpsrn[$y]['mhddatum']."</td>
                  <td>".$tmpsrn[$y]['seriennummer']."</td>
                  <td>".$tmpsrn[$y]['charge']."</td></tr>");
            }
          } else if ($check_mhd=="1")
          {

            $this->app->Tpl->Add("JQUERYREADY","checklagermengen();"); 
            $kommamodus = false;
            if($menge != round($menge) || $check_seriennummer == '' || $check_seriennummer === 'keine') {
              $kommamodus = true;
            }
            $ctmpmhd = !empty($tmpmhd)?count($tmpmhd):0;
            for($y=0;$y<$ctmpmhd;$y++)
            {
              if($tmpmhd[$y]['menge'] != 1) {
                $kommamodus = true;
              }
            }
            if($kommamodus)
            {
              $tmenge = 0;
              for($y=0;$y<$ctmpmhd;$y++)
              {
                if($tmpmhd[$y]['mhddatum']=='') {
                  $tmpmhd[$y]['mhddatum'] = ' - ';
                } else {
                  $tmpmhd[$y]['mhddatum'] = $this->app->String->Convert($tmpmhd[$y]['mhddatum'],'%1-%2-%3','%3.%2.%1');
                }
                if($tmpmhd[$y]['charge']=='') {
                  $tmpmhd[$y]['charge'] = ' - ';
                }
                
                if($menge - $tmenge <= 0){
                  $tm = 0;
                }elseif($menge - $tmenge >= $tmpmhd[$y]['menge'])
                {
                  $tm = $tmpmhd[$y]['menge'];
                }elseif($menge - $tmenge < $tmpmhd[$y]['menge']){
                  $tm =  $menge - $tmenge;
                }
                $tmenge += $menge;
                $this->app->Tpl->Add('SRNINFO',"<tr>
                    <td><input type=\"hidden\" name=\"lager_mhd_id[]\" value=\"".$tmpmhd[$y]['id']."\" ><input type=\"text\" size=\"6\" class=\"chargenmengen mhdmenge\" onchange=\"checklagermengen();\" name=\"lager_mhd_auswahl[]\" value=\"\" /> / <input type=\"hidden\"  class=\"lager_charge_menge\" name=\"lager_mhd_menge[]\" value=\"".$tmpmhd[$y]['mhdmenge']."\" />".$tmpmhd[$y]['mhdmenge']."&nbsp;$out</td>
                    <td>".$tmpmhd[$y]['mhddatum']."</td><td></td>
                    <td>".$tmpmhd[$y]['charge']."</td></tr>");
                if($y == 0)$this->app->Tpl->Set('ONCHANGEMENGE',' onchange="checklagermengen();" ');
              }            
            }else{
              for($y=0;$y<$ctmpmhd;$y++)
              {
                $regaltreffer="1";
                if($y < $menge) $checked="checked"; else $checked="";

                if($tmpmhd[$y]['mhddatum']=="") $tmpmhd[$y]['mhddatum'] = " - "; else $tmpmhd[$y]['mhddatum'] = $this->app->String->Convert($tmpmhd[$y]['mhddatum'],'%1-%2-%3','%3.%2.%1');
                if($tmpmhd[$y]['charge']=="") $tmpmhd[$y]['charge'] = " - ";

                $this->app->Tpl->Add('SRNINFO',"<tr>
                    <td><input type=\"checkbox\" onclick=\"countChecks(this)\" name=\"lager_mhd_id[]\" value=\"".$tmpmhd[$y]['id']."\" $checked>&nbsp;$out</td>
                    <td>".$tmpmhd[$y]['mhddatum']."</td><td></td>
                    <td>".$tmpmhd[$y]['charge']."</td></tr>");
              }
            }

          } else if ($check_charge=="2" || $check_charge == "1")
          {
            $this->app->Tpl->Add("JQUERYREADY","checklagermengen();"); 
            
            $kommamodus = false;
            if($menge != round($menge) || $check_seriennummer == '' || $check_seriennummer === 'keine')$kommamodus = true;
            $ctmpcharge = !empty($tmpcharge)?count($tmpcharge):0;
            for($y=0;$y<$ctmpcharge;$y++)
            {
              if($tmpcharge[$y]['menge'] != 1)$kommamodus = true;
            }
            if($kommamodus)
            {
              $tmenge = 0;
              for($y=0;$y<$ctmpcharge;$y++)
              {
                $regaltreffer="1";
                if($menge - $tmenge <= 0){
                  $tm = 0;
                }elseif($menge - $tmenge >= $tmpmhd[$y]['menge'])
                {
                  $tm = $tmpmhd[$y]['menge'];
                }elseif($menge - $tmenge < $tmpmhd[$y]['menge']){
                  $tm =  $menge - $tmenge;
                }
                $tmenge += $menge;
                //if($y < $menge) $checked="checked"; else $checked="";

                if($tmpcharge[$y]['charge']=="") $tmpcharge[$y]['charge'] = " - ";

                $this->app->Tpl->Add('SRNINFO',"<tr>
                    <td><input type=\"hidden\" name=\"lager_charge_id[]\" value=\"".$tmpcharge[$y]['id']."\" ><input class=\"chargenmengen\" onchange=\"checklagermengen();\" type=\"text\" size=\"6\" name=\"lager_charge_auswahl[]\" value=\"\" /> / <input type=\"hidden\" class=\"lager_charge_menge\" name=\"lager_charge_menge[]\" value=\"".$tmpcharge[$y]['cmenge']."\" />".$this->app->erp->ReplaceMenge(0,$tmpcharge[$y]['cmenge'],0)."&nbsp;$out</td>
                    <td>".$tmpcharge[$y]['charge']."</td></tr>");
                if($y == 0)$this->app->Tpl->Set('ONCHANGEMENGE',' onchange="checklagermengen();" ');
              }                    
              
            }else{
              for($y=0;$y<$ctmpcharge;$y++)
              {
                $regaltreffer="1";
                $checked = '';
                if($y < $menge) {
                  $checked='checked';
                }

                if($tmpcharge[$y]['charge']=='') $tmpcharge[$y]['charge'] = ' - ';

                $this->app->Tpl->Add('SRNINFO',"<tr>
                    <td><input type=\"checkbox\" onclick=\"countChecks(this)\" name=\"lager_charge_id[]\" value=\"".$tmpcharge[$y]['id']."\" $checked>&nbsp;$out</td>
                    <td>".$tmpcharge[$y]['charge']."</td></tr>");
              }
            }
          }

          //$regaltreffer="1";

          $this->app->Tpl->Add('ZWISCHENLAGERINFO',"<input type=\"hidden\" name=\"abschluss_auslagern\" value=\"1\">");

          $allow = 0;

          if($check_seriennummer!=="keine" || $check_charge=="2" || $check_charge=="1" || $check_mhd=="1")
          {
            if($this->app->Secure->GetPOST("abschluss_auslagern")=="1")
              $allow=1;
          } else $allow=1;

          if($cmd==='umlagern' && $regal  > 0 && $checkregalneu!=$regalneu) {
            $allow=0;
          }
          if($cmd==='umlagern' && $regalneu =='' ) {
            $allow=0;
          }
          $this->app->erp->RunHook('lager_buchenauslagern_display1', 4, $artikel, $cmd, $regal, $regalneu);

          if($allow){
            $lager_srn_id = $this->app->Secure->GetPOST("lager_srn_id");

            $lager_mhd_id = $this->app->Secure->GetPOST('lager_mhd_id');
            $lager_mhd_menge = $this->app->Secure->GetPOST('lager_mhd_menge');
            $lager_mhd_auswahl = $this->app->Secure->GetPOST('lager_mhd_auswahl');
            if($lager_mhd_menge)
            {
              $clager_mhd_id = !empty($lager_mhd_id)?count($lager_mhd_id):0;
              for($q=0;$q<$clager_mhd_id;$q++){
                if($lager_mhd_auswahl[$q])
                {
                  $lager_mhd_auswahl[$q] = round((float)str_replace(',','.',$lager_mhd_auswahl[$q]),4);
                  $lager_mhd_menge[$q] = round((float)str_replace(',','.',$lager_mhd_menge[$q]),4);
                  if($lager_mhd_auswahl[$q] <= $lager_mhd_menge[$q])
                  {
                    $passendArr = $this->app->DB->SelectRow("SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE id='".$lager_mhd_id[$q]."' LIMIT 1");
                    $passende_charge = $passendArr['charge'];
                    $passende_mhd = $passendArr['mhddatum'];
                    $passende_lager_platz = $passendArr['lager_platz'];
                    $isInterim = $interimonconsumption && $this->app->DB->Select(
                        sprintf(
                          'SELECT id FROM lager_platz WHERE id = %d AND verbrauchslager = 1',
                          (int)$passende_lager_platz
                        )
                      );
                    $passende_artikel = $passendArr['artikel'];
                    $chargenmenge = $lager_mhd_auswahl[$q];
                    while($chargenmenge > 0)
                    {
                      $checkcharge = $this->app->DB->SelectRow(
                        "SELECT * 
                        FROM `lager_charge` 
                        WHERE `charge`='".$passende_charge."' 
                        AND `lager_platz`='$passende_lager_platz' AND `artikel`='$passende_artikel' 
                        LIMIT 1"
                      );
                      if(!$checkcharge) {
                        break;
                      }
                      $checkmenge = $checkcharge['menge'];
                      if($checkmenge <= 0) {
                        $this->app->DB->Delete(
                          sprintf(
                            'DELETE FROM `lager_charge` WHERE `menge` <= 0 AND `artikel` = %d AND `lager_platz` = %d',
                            $passende_artikel, $passende_lager_platz
                          )
                        );
                      }
                      if($checkmenge <= $chargenmenge)
                      {
                        $this->app->erp->ChargeAuslagernLog($passende_artikel, $passende_lager_platz, $passende_charge, $chargenmenge['menge'],$grundreferenz!=''?$grundreferenz:'Auslagern', '', 0, $chargenmenge['id'],0,$isInterim);
                        $chargenmenge -= $checkmenge;
                      }elseif($checkmenge > $chargenmenge)
                      {
                        $this->app->erp->ChargeAuslagernLog($passende_artikel, $passende_lager_platz, $passende_charge, $chargenmenge, $grundreferenz!=''?$grundreferenz:'Auslagern', '', 0, $chargenmenge['id'],0,$isInterim);
                        $chargenmenge = 0;
                        break;
                      }
                    }
                    if($lager_mhd_auswahl[$q] == $lager_mhd_menge[$q])
                    {
                      $this->app->DB->Delete("DELETE FROM lager_mindesthaltbarkeitsdatum WHERE id = '".$lager_mhd_id[$q]."' LIMIT 1");
                    }else{
                      $this->app->DB->Update("UPDATE lager_mindesthaltbarkeitsdatum SET menge = menge - ".$lager_mhd_auswahl[$q]." WHERE id = '".$lager_mhd_id[$q]."' LIMIT 1");
                    }

                    $this->app->erp->MHDLog($passende_artikel, $passende_lager_platz, 0, $passende_mhd, $lager_mhd_auswahl[$q], $grundreferenz!=''?$grundreferenz:'manuell auslagern', '', 0, $passende_charge,0, $isInterim);
                    if($cmd==='umlagern') {
                      $_passende_mhd = $passende_mhd;
                      $_passende_charge = $passende_charge;
                      $_internebemerkung = '';
                      $this->app->erp->RunHook('lager_buchenauslagern_mhdlagerbewegung',7, $passende_artikel,$lager_mhd_auswahl[$q],$regalneu,$passende_mhd,$passende_charge, $regal,$_internebemerkung);
                      $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($passende_artikel,$lager_mhd_auswahl[$q],$regalneu,$passende_mhd,$passende_charge,"","",0,$_internebemerkung);
                      $passende_mhd = $_passende_mhd;
                      $passende_charge = $_passende_charge;
                    }
                  }
                }
              }
            }else{
              $clager_mhd_id = !empty($lager_mhd_id)?count($lager_mhd_id):0;
              for($q=0;$q<$clager_mhd_id;$q++){
                $passendArr = $this->app->DB->SelectRow("SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE id='".$lager_mhd_id[$q]."' LIMIT 1");
                $passende_charge = $passendArr['charge'];
                $passende_mhd = $passendArr['mhddatum'];
                $passende_lager_platz = $passendArr['lager_platz'];
                $isInterim = $interimonconsumption && $this->app->DB->Select(
                    sprintf(
                      'SELECT id FROM lager_platz WHERE id = %d AND verbrauchslager = 1',
                      (int)$passende_lager_platz
                    )
                  );
                $passende_artikel = $passendArr['artikel'];
                $passende_menge = $passendArr['menge'];
                $this->app->DB->Delete("DELETE FROM lager_mindesthaltbarkeitsdatum WHERE id='".$lager_mhd_id[$q]."' LIMIT 1");
                $this->app->DB->Delete("DELETE FROM lager_charge WHERE charge='".$passende_charge."' 
                    AND lager_platz='$passende_lager_platz' AND artikel='$passende_artikel' LIMIT 1");
                $this->app->erp->MHDLog($passende_artikel, $passende_lager_platz, 0, $passende_mhd,$passende_menge, 'manuell auslagern', "", 0, $passende_charge,0, $isInterim);
                // umlagern3
                if($cmd==='umlagern')
                {
                  $_passende_mhd = $passende_mhd;
                  $_passende_charge = $passende_charge;
                  $_internebemerkung = '';
                  $this->app->erp->RunHook('lager_buchenauslagern_mhdlagerbewegung',7, $passende_artikel,$passende_menge,$regalneu,$passende_mhd,$passende_charge, $regal, $_internebemerkung);
                  $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($passende_artikel,$passende_menge<=0?1:$passende_menge,$regalneu,$passende_mhd,$passende_charge,"","",0,$_internebemerkung);
                  $passende_mhd = $_passende_mhd;
                  $passende_charge = $_passende_charge;
                }
              }
            }

            $lager_charge_id = $this->app->Secure->GetPOST('lager_charge_id');
            $lager_charge_menge = $this->app->Secure->GetPOST('lager_charge_menge');
            $lager_charge_auswahl = $this->app->Secure->GetPOST('lager_charge_auswahl');
            if($lager_charge_menge)
            {
              $clager_charge_id = !empty($lager_charge_id)?count($lager_charge_id):0;
              for($q=0;$q<$clager_charge_id;$q++){
                $passende_arr = $this->app->DB->SelectRow("SELECT * FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                if($passende_arr){
                  $passende_artikel = $passende_arr['artikel'];//$this->app->DB->Select("SELECT artikel FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                  $passende_datum = $passende_arr['datum'];//$this->app->DB->Select("SELECT datum FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                  $passende_charge = $passende_arr['charge'];//$this->app->DB->Select("SELECT charge FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                  $lager_charge_menge[$q] = $passende_arr['menge'];
                  if($lager_charge_auswahl[$q])
                  {
                    $lager_charge_auswahl[$q] = round((float)str_replace(',','.',$lager_charge_auswahl[$q]),4);
                    //$lager_charge_menge[$q] = round((float)str_replace(',','.',$lager_charge_menge[$q]),4);
                    if($lager_charge_auswahl[$q] <= $lager_charge_menge[$q])
                    {
                      $isInterim = $interimonconsumption && $this->app->DB->Select(
                          sprintf(
                            'SELECT id FROM lager_platz WHERE id = %d AND verbrauchslager = 1',
                            (int)$passende_arr['lager_platz']
                          )
                        );
                      if($lager_charge_auswahl[$q] == $lager_charge_menge[$q])
                      {
                        $this->app->erp->ChargeAuslagernLog($passende_artikel, $passende_arr['lager_platz'], $passende_charge, $lager_charge_menge[$q], $grundreferenz!=''?$grundreferenz:'Auslagern', '', 0, $passende_arr['id'],0,$isInterim);
                        //$this->app->DB->Delete("DELETE FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                        //umlagern3
                        if($cmd==='umlagern') {
                          $this->app->erp->AddChargeLagerOhneBewegung($passende_artikel,$lager_charge_auswahl[$q],$regalneu,$passende_datum,$passende_charge);
                        }
                      }elseif($lager_charge_auswahl[$q] < $lager_charge_menge[$q])
                      {
                        $this->app->erp->ChargeAuslagernLog($passende_artikel, $passende_arr['lager_platz'], $passende_charge, $lager_charge_auswahl[$q], $grundreferenz!=''?$grundreferenz:'Auslagern', '', 0, $passende_arr['id'],0,$isInterim);
                        //$this->app->DB->Update("UPDATE lager_charge set menge = menge - ".$lager_charge_auswahl[$q]." WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                        if($cmd==='umlagern') {
                          $this->app->erp->AddChargeLagerOhneBewegung($passende_artikel,$lager_charge_auswahl[$q],$regalneu,$passende_datum,$passende_charge);
                        }
                      }
                    }
                  }
                }
              }              
            }else{
              $clager_charge_id = !empty($lager_charge_id)?count($lager_charge_id):0;
              for($q=0;$q<$clager_charge_id;$q++){
                $passende_arr = $this->app->DB->SelectRow("SELECT * FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                if($passende_arr)
                {
                  $passende_artikel = $passende_arr['artikel'];//$this->app->DB->Select("SELECT artikel FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                  $passende_datum = $passende_arr['datum'];// $this->app->DB->Select("SELECT datum FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                  $passende_charge = $passende_arr['charge'];//$this->app->DB->Select("SELECT charge FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                  $passende_menge = $passende_arr['menge'];//$this->app->DB->Select("SELECT menge FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                  //$this->app->DB->Delete("DELETE FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
                  $isInterim = $interimonconsumption && $this->app->DB->Select(
                      sprintf(
                        'SELECT id FROM lager_platz WHERE id = %d AND verbrauchslager = 1',
                        (int)$passende_arr['lager_platz']
                      )
                    );
                  $this->app->erp->ChargeAuslagernLog($passende_artikel, $passende_arr['lager_platz'], $passende_charge, $passende_menge, $grundreferenz!=''?$grundreferenz:'Auslagern', '', 0, $lager_charge_id[$q],0, $isInterim);

                  //umlagern3
                  if($cmd==='umlagern')
                  {
                    $this->app->erp->AddChargeLagerOhneBewegung($passende_artikel,$passende_menge<=0?1:$passende_menge,$regalneu,$passende_datum,$passende_charge);
                  }
                }
              }
            }

            if($seriennummer!='') {
              $tmp_sn = ' SN:'.$seriennummer;
            } else {
              $tmp_sn = '';
            }

            $bestand = $this->app->erp->ArtikelImLager($artikel);

            if($grundreferenz!=''){
              $grundtext = "$grund f&uuml;r $checkname: $grundreferenz $tmp_sn";
            } else {
              $grundtext = "$grund $checkname $tmp_sn";
            }
            

            // umlagern3 lager_bewegung buchen

            // wenn enticklung auf mitarbeiter buchen
            if ($grund === 'Entwicklungsmuster') {
              $this->app->DB->Insert("INSERT INTO projekt_inventar (id,artikel,menge,bestellung, projekt,   
                adresse,  mitarbeiter,   vpe,zeit) VALUES ('','$artikel','$menge','','$projekt','$adresse','" . $this->app->User->GetName() . "', 'einzeln',NOW())");
            }
            //ziehe menge ab von lager_platz_inhalt
            $tmpcheck = $this->app->DB->Select("SELECT id FROM lager_platz_inhalt WHERE lager_platz='$regal' AND artikel='$artikel' AND menge >='$menge' LIMIT 1");
            $mengeimregel = $this->app->erp->ArtikelImLagerPlatz($artikel,$regal);
            // wenn es ein lager mit genug gibt nimm dieses
            //if ($tmpcheck > 0) {
            if($mengeimregel >= $menge)
            {
              $summezumcheckenneu = $mengeimregel - $menge;
              $this->app->erp->LagerAuslagernRegal($artikel,$regal,$menge,$projektid,$grundtext);
            }elseif($mengeimregel > 0)
            {
              $this->app->erp->LagerAuslagernRegal($artikel,$regal,$mengeimregel,$projektid,$grundtext);
              $summezumcheckenneu = 0;
            }
            // umlagern3 in lager_platz_inhalt buchen
            if($cmd==='umlagern')
            {
              $this->app->erp->LagerEinlagernDifferenz($artikel,$menge,$regalneu,$projektid,"Umlagern");
              //$this->app->erp->LogFile("ETIKETT2 A $artikel M $menge R $regalneu"); //BENE
              //$data['lager_platz_name'] = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$regalneu'");
              //$this->app->erp->EtikettenDrucker("artikel_klein",1,'artikel',$artikel,$data);
            }

            $name = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$artikel' LIMIT 1");
            if($cmd==='umlagern')
              $gesamt = $this->app->DB->Select("SELECT replace(trim(SUM(menge))+0,'.',',') FROM lager_platz_inhalt WHERE artikel='$artikel' AND lager_platz='$regalneu'");
            else
              $gesamt = $this->app->DB->Select("SELECT replace(trim(SUM(menge))+0,'.',',') FROM lager_platz_inhalt WHERE artikel='$artikel' AND lager_platz='$regal'");
            $gesamt_alle = $this->app->DB->Select("SELECT replace(trim(SUM(menge))+0,'.',',') FROM lager_platz_inhalt WHERE artikel='$artikel'");
            $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Artikel $name wurde $menge mal ausgelagert. Anzahl Regal: <b>".floatval($gesamt)." $einheit</b> Anzahl Komplettbestand alle Regale: <b>".floatval($gesamt_alle)." $einheit</b></div>");

            if($artikelid > 0)
            {
              $this->app->Location->execute('index.php?module=artikel&action=lager&id='.$artikelid.'&msg='.$msg);
            }
              //$msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Artikel $name wurde umgelagert. Der n&auml;chste Artikel kann jetzt umgelagert werden.</div>");
            if($cmd==='umlagern'){
              $this->app->Location->execute('index.php?module=lager&action=buchenauslagern&cmd=umlagern&msg='.$msg);
            }
            $this->app->Location->execute('index.php?module=lager&action=buchenauslagern&msg='.$msg);
              
          } // ende allow
          if ($regalneu != '' && $regal > 0 && $cmd==='umlagern') {
            $msgregal = "Dieses Regal gibt es nicht!";
            $this->app->Tpl->Set('MESSAGELAGER', "<div class=\"error\">$msgregal</div>");
            $regalcheck = 0;
          }

          // ende auslagern
        }
      } else {
        //$error++;
        if ($regal != '') {
          $msgregal = "Dieses Regal gibt es nicht!";
          $this->app->Tpl->Set('MESSAGELAGER', "<div class=\"error\">$msgregal</div>");
          $regalcheck = 0;
        }
      }
      if ($error == 0 && $regalcheck == 0) {
        $standardbild = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='$artikel' LIMIT 1");
        if ($standardbild == '') {
          $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel' LIMIT 1");
        }

        if(trim($einheit) != ''){
          $name_de .= " (Einheit: ".$einheit.")";
        }

        $this->app->Tpl->Add('BEZEICHNUNG', "<tr valign=\"top\"><td>Aktueller Artikel:</td><td>$name_de</td></tr>"); //BENE
        if ($standardbild > 0) {
          $this->app->Tpl->Add('BEZEICHNUNG', "<tr valign=\"top\"><td>Bild:</td><td><img src=\"index.php?module=dateien&action=send&id=$standardbild\" width=\"110\"></td></tr>");
        }

        $lagermeist = $this->app->DB->SelectArr("SELECT lager_platz, SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' GROUP BY lager_platz ORDER by 2 DESC LIMIT 1");
        $lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$lagermeist[0]['lager_platz']}' LIMIT 1");

        $standard_lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1");
        $standard_lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$standard_lagerplatz' LIMIT 1");

        if($lagerbezeichnung!=$standard_lagerbezeichnung && $standard_lagerbezeichnung!="")
          $standardlageranzeigen = "<b onclick=\"document.getElementById('regal').value='$standard_lagerbezeichnung'\";>$standard_lagerbezeichnung</b> (Standardlager)";

        //echo "huhuh $cmd regal $regal regalvalue $regalvalue checkregal $checkregal regaltreffer $regaltreffer";
        if($regaltreffer=="1") {
          $regalvalue=$this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$regal' LIMIT 1");
        } else {
          $regalvalue='';
        }
        //if($regal !="" && $regalvalue=="") $regalvalue=$regal;
        if($regalvalue!='' && $cmd==='umlagern' && $regal > 0 && $regal==$checkregal)
        {
          if($this->app->erp->Version()!=="stock")
          {
            $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr ><td>Regalvorschlag:</td><td align=\"left\"><input type=\"button\"  onclick=\"document.getElementById('regal').value='$regalvalue'\"; value=\"$regalvalue\"></td></tr>");
          }
          $this->app->Tpl->Set('FOCUSFIELD','document.getElementById("regal").focus();');
          $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr valign=\"top\"><td><b>Zielregal:</b></td><td align=\"left\"><input type=\"text\" style=\"width:200px;border: 2px solid red\" name=\"regalneu\" id=\"regal\" value=\"\"><br>Jetzt Regal abscannen!<script type=\"text/javascript\">document.getElementById('menge').style.backgroundColor='#ececec'; document.getElementById('nummer').style.backgroundColor='#ececec'; document.getElementById('grundreferenz').style.backgroundColor='#ececec';
              document.getElementById('grundreferenz').readOnly=true;
              document.getElementById('menge').readOnly=true;
              document.getElementById('nummer').readOnly=true;
              </script>
              <input type=\"hidden\" name=\"regal\" value=\"$regalvalue\"></td></tr>");
        } else {
          if($this->app->erp->Version()==="stock")
          {
            if($this->app->Secure->GetPOST('regal')=="" && $this->app->Secure->GetGET("regal")=="") //TODO
              $regalvalue = $lagerbezeichnung; //TODO
            $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr ><td>Regalvorschlag:</td><td align=\"left\"><input type=\"button\" onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\" value=\"$lagerbezeichnung\" > (Standardlager)<br>$standardlageranzeigen</td></tr>");
          }
          else {

            $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr ><td>Regalvorschlag:</td><td align=\"left\"><input type=\"button\" onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\" value=\"$lagerbezeichnung\" > (aktuell am meisten im Lager)<br>$standardlageranzeigen</td></tr>");
          }

          if($lagerbezeichnung!='' && empty($regalvaluestock) && $regal!='') {
            $regalvaluestock=$lagerbezeichnung;
          }
          if($regal!='') {
            $regalvaluestock=$regalvalue;
          }
          $this->app->Tpl->Add('ZWISCHENLAGERINFO', "<tr valign=\"top\"><td><b>Entnahmeregal:</b></td><td align=\"left\"><input type=\"text\" style=\"width:200px;border: 2px solid red;\" name=\"regal\" id=\"regal\" value=\"$regalvaluestock\"><br>Jetzt Regal abscannen!</td></tr>");
          $this->app->Tpl->Set('FOCUSFIELD','document.getElementById("regal").focus();');
        }
        // letzt einstellung von grad
        $this->app->Tpl->Add('ZWISCHENLAGERINFO', '<script type="text/javascript">
            document.getElementById("regal").focus();
            </script>');
      } else if ($error == 0) {
        echo "speichern adresse $checkadresse projekt $projekt menge $menge";
      }
    }
    if ($nummer == '') {
      $this->app->Tpl->Set('ARTIKELSTYLE', "style=\"border: 2px solid red\"");
    }
    if(empty($frmMenge) && !empty($amount)) {
      $menge = $amount;
    }
    $this->app->Tpl->Set('MENGE', $this->app->erp->ReplaceMenge(0,(String)$menge,0));
    $this->app->Tpl->Set('GRUNDREFERENZ', $grundreferenz);

    $art_name = $this->app->DB->Select("SELECT CONCAT(nummer) FROM artikel WHERE nummer='$nummer' AND geloescht!=1 AND lagerartikel=1 LIMIT 1");
    $this->app->Tpl->Set('NUMMER', $art_name);

    $pr_name = $this->app->DB->Select("SELECT CONCAT(abkuerzung) FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
    $this->app->Tpl->Set('ADRESSE', $adresse);
    if ($_SESSION['grund'] === 'Interner Entwicklungsbedarf') $this->app->Tpl->Set('MUSTER', 'selected');
    if ($_SESSION['grund'] === 'RMA / Reparatur / Reklamation') $this->app->Tpl->Set('RMA', 'selected');
    if ($_SESSION['grund'] === 'Alte Bestellung') $this->app->Tpl->Set('ALTE', 'selected');
    if ($_SESSION['grund'] === 'Kundenauftrag / Produktion') $this->app->Tpl->Set('PRODUKTION', 'selected');
    if ($_SESSION['grund'] === 'Manuelle Lageranpassung') $this->app->Tpl->Set('DIFFERENZ', 'selected');
    if ($_SESSION['grund'] === 'Umlagern') $this->app->Tpl->Set('UMLAGERN', 'selected');
    //$this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");
    $this->app->YUI->AutoComplete('projekt', 'projektname', 1);
    $this->app->YUI->AutoComplete('adresse', 'adresse');
    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer',1);
    $this->app->YUI->AutoComplete('regal','lagerplatz');
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');
    //$this->app->YUI->AutoComplete(ADRESSEAUTO,"adresse",array('id','name','kundennummer'),"CONCAT(id,' ',name)");
    $this->app->Tpl->Set('PROJEKT', $pr_name);
    $this->app->Tpl->Parse('TAB1', 'auslagern.tpl');
    $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
  }

  function LagerLetzteBewegungen()
  {
    $this->LagerBuchenMenu();

    $this->app->YUI->TableSearch('TAB1', 'lagerletztebewegungen','show','','',basename(__FILE__), __CLASS__);

    $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
  } 


  function LagerBuchenMenu() {
    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->Headlines('Lager');
    if($this->app->erp->Version()!=='stock') {
      $this->app->erp->MenuEintrag("index.php?module=lager&action=buchenauslagern&cmd=umlagern&id=$id", 'Umlagern');
      $this->app->erp->MenuEintrag("index.php?module=lager&action=buchenauslagern&id=$id", 'Auslagern');
      $this->app->erp->MenuEintrag("index.php?module=lager&action=bucheneinlagern&id=$id", 'Einlagern');
      $this->app->erp->MenuEintrag("index.php?module=lager&action=buchenzwischenlager&id=$id", 'Zwischenlager');
      $this->app->erp->MenuEintrag('index.php?module=lager&action=buchen', 'Zur&uuml;ck zur &Uuml;bersicht');
      $this->app->erp->MenuEintrag('index.php?module=lager&action=schnellauslagern', 'Schnell-Auslagern');
    }
    
    $this->app->erp->MenuEintrag('index.php?module=lager&action=schnellumlagern', 'Schnell-Umlagern');
    $this->app->erp->MenuEintrag('index.php?module=lager&action=letztebewegungen', 'Letzte Bewegungen');
  }
  function LagerReservierungen() {
    $this->app->erp->MenuEintrag('index.php?module=lager&action=reservierungen','&Uuml;bersicht');
    $this->app->erp->Headlines('Lager', 'Reservierungen');
    $this->app->Tpl->Set('MESSAGE','<div class="info">Reservierungen Stand ' . date('d.m.Y').'</div>');
    $this->app->YUI->TableSearch('TAB1', 'lager_reservierungen', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Set('AKTIV_TAB1', 'selected');
    $this->app->Tpl->Parse('PAGE', 'lager_reservierungen.tpl');
  }
  

  public function LagerRegalEtiketten() {
    $id = (int)$this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGET('cmd');

    if ($cmd==='all') {
      $arr = $this->app->DB->SelectArr(sprintf('SELECT id,kurzbezeichnung FROM lager_platz WHERE lager=%d',$id));
    }
    else{
      $arr = $this->app->DB->SelectArr(sprintf('SELECT id,kurzbezeichnung FROM lager_platz WHERE id=%d LIMIT 1',$id));
    }
    if(!empty($arr)){
      foreach($arr as $row) {
        $this->app->erp->EtikettenDrucker('lagerplatz_klein', 1, 'lager_platz', $row['id']);
      }
    }
    $ref = $_SERVER['HTTP_REFERER'];
    if(empty($ref)) {
      if ($cmd==='all'){
        $ref = 'index.php?module=lager&action=list';
      } else {
        $ref = 'index.php?module=lager&action=edit&id='.$id;
      }
    }
    $this->app->Location->execute($ref);
  }



  function LagerZweistufigEtikettDrucken($artikelId, $lagerplatzId, $menge){

    if($artikelId <= 0 || $artikelId == ''){
      return;
    }

    $etikettId = $this->app->User->GetParameter('lager_zweistufig_etiketten');
    $druckerId = $this->app->User->GetParameter('lager_zweistufig_etikettendrucker');

    if($etikettId > 0 && $etikettId != '' && $druckerId > 0 && $druckerId != ''){
      $lagerplatz = '';
      if($lagerplatzId != '' && $lagerplatzId > 0){
        $lagerplatz = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id = '$lagerplatzId' LIMIT 1");
      }

      $artikelDaten = $this->app->DB->SelectRow("SELECT name_de, nummer FROM artikel WHERE id = '$artikelId' LIMIT 1");

      $variablen = null;
      $variablen['name_de'] = $artikelDaten['name_de'];
      $variablen['artikel_name_de'] = $artikelDaten['name_de'];
      $variablen['nummer'] = $artikelDaten['nummer'];
      $variablen['artikel_nummer_de'] = $artikelDaten['nummer'];
      $variablen['menge'] = $menge;
      $variablen['lagerplatz'] = $lagerplatz;

      $etikettProZeile = $this->app->User->GetParameter('lager_zweistufig_etiketten_eins_pro_zeile');
      if($etikettProZeile){
        $menge = 1;
      }

      $this->app->erp->EtikettenDrucker($etikettId,$menge,'artikel',$artikelId,$variablen,'',$druckerId);
    }
  }

  function LagerAuslagernProduktionbasiert()
  {
    $this->app->Tpl->Set('TABTEXT','');
    $this->app->erp->MenuEintrag("index.php?module=lager&action=artikelfuerlieferungen&cmd=produktion","&Uuml;bersicht");
    // offene auslagerungen
    $result = $this->app->DB->SelectArr("SELECT r.parameter FROM lager_reserviert r LEFT JOIN produktion p ON p.id=r.parameter
        WHERE r.objekt='produktion' AND (p.status='abgeschlossen' OR p.status='gestartet') GROUP BY r.parameter");

    $gesamtanzahlartikel = 0;

    //TODO YUI Start  
 
    $this->app->Tpl->Set('TAB1', "<table border=0 width=100% class=\"mkTable\">
        <tr><td><b>Produktion</b></td><td><b>Bezeichnung</b></td><td align=center><b>Auslagern</b></td></tr>");
    $cresult = !empty($result)?count($result):0;
    for ($w = 0;$w < $cresult;$w++) {
      $produktion = $result[$w]['parameter'];

      $bezeichnung = $this->app->erp->ProduktionName($produktion);

          $nummer = $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$produktion' LIMIT 1");
          $this->app->Tpl->Add('TAB1', "<tr><td>Produktion $nummer</td><td>$bezeichnung</td><td align=center><a href=\"index.php?module=lager&action=auslagernproduktion&id=$produktion&cmd=produktion\"><img src=\"./themes/[THEME]/images/forward.svg\"></a></td></tr>");
          $artikellistesumm = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='produktion' AND parameter='$produktion'");
          if (count($artikellistesumm) == 0) continue;
          $artikelliste = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='produktion' AND parameter='$produktion'");

          $gesamtanzahlartikel  = $gesamtanzahlartikel + count($artikelliste);
     }
     $this->app->Tpl->Add('TAB1', "</table>");

    //TODO YUI Ende

          if ($gesamtanzahlartikel <= 0) {
          $this->app->Tpl->Set('MESSAGE', "<div class=\"info\">{|Aktuell gibt es keine Artikel f&uuml;r Produktionen, da keine Produktionen gestartet wurden.|}</div>");
          $this->app->Tpl->Set('TAB1','');
          }

  }


  function LagerAuslagernProjektbasiert()
  {
    $projekt = 1;
    $projektearr = $this->app->DB->SelectArr("SELECT id FROM projekt WHERE geloescht!=1");
    $projektearr[] = 0;
    $gesamtanzahlartikel = 0;
    // start projekt schleife
    $cprojektarr = !empty($projektearr)?count($projektearr):0;
    for ($w = 0;$w < $cprojektarr;$w++) {
      $this->app->Tpl->Set('INHALT', '');
      $projekt = $projektearr[$w]['id'];
      $projektName = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt' LIMIT 1");
      if ($projekt == 0 || $projekt == "") $projektName = "Ohne Projekt";
      $artikellistesumm = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='lieferschein' AND projekt='$projekt' ");

      if (count($artikellistesumm) == 0) continue;
      $this->app->Tpl->Add('INHALT', "<h2>$projektName Lieferungen Stand " . date('d.m.Y') . "</h2>");
      $artikelliste = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='lieferschein' AND projekt='$projekt' ");
      $orderarray = $this->LagerAuslagernArtikelliste($artikelliste,$projekt,true);
      $gesamtanzahlartikel = count($orderarray);
      $this->LagerAuslagernArtikellisteRender($orderarray);
    } // ende projekt schleife
    if ($gesamtanzahlartikel <= 0) {
      if($this->app->erp->RechteVorhanden('kommissionierlauf','list') && !$this->app->DB->Select("SELECT id FROM projekt WHERE ifnull(geloescht,0) = 0 AND kommissionierverfahren  = 'zweistufig' LIMIT 1"))
      {
        $this->app->Location->execute('index.php?module=kommissionierlauf&action=list');
      }
      $this->app->Tpl->Set('MESSAGE', "<div class=\"info\">Aktuell gibt es keine Artikel f&uuml;r Lieferungen.</div>");
    }
    $this->app->erp->MenuEintrag('index.php?module=kommissionierlauf&action=list','Kommissionierl&auml;ufe');
    $this->app->erp->MenuEintrag('index.php?module=lager&action=artikelfuerlieferungen','2-stufige Kommissionierung');
  }



    /**
     * @param array  $artikelliste
     * @param string $projekt
     */
    function LagerReihenfolgeArtikelliste($artikelliste,$projekt='')
    {
      $cartikelliste = !empty($artikelliste)?count($artikelliste):0;
      for ($i = 0;$i < $cartikelliste;$i++) {
        $artikel = $artikelliste[$i]['artikel'];
        echo $artikel."<br>";
      }
    }

    function LagerAuslagernReihenfolge($artikelliste,$projekt="")
    { 
      return $artikelliste;
      // Reihenfolge abholen
      $orderarray = $this->LagerAuslagernArtikelliste($artikelliste,$projekt,true);
      $corderarray = !empty($orderarray)?count($orderarray):0;
      for($i=0;$i<$corderarray;$i++)
      { 
        $artikel = $orderarray[$i]["artikel"];
        $kurzbezeichnung = $orderarray[$i]["kurzbezeichnung"];
        $tmparray[$artikel]=$kurzbezeichnung;
      }
      echo "schritt 1<br>";
      print_r($orderarray);
      echo "<br>";
      echo "schritt 2<br>";
      print_r($tmparray);
      echo "<br>";
      echo "schritt 3<br>";

      // neu sortieren
      asort($tmparray);
      if(count($tmparray)>0)
      { 
        foreach($tmparray as $key=>$value)
        {
          $newartikelliste[]=array("artikel"=>$key);
        }
      }
      print_r($newartikelliste);
      return $newartikelliste;
    }
    //function LagerAuslagernList($artikelliste,$projekt="",$getorder=false)
    function LagerAuslagernArtikelliste($artikelliste,$projekt="",$getorder=false)
    {
      $cmd = $this->app->Secure->GetGET('cmd');

      $tmpanzahl = 0;+
      $gesamtanzahlartikel = 0;
      $cartikelliste = !empty($artikelliste)?count($artikelliste):0;
      for ($i = 0;$i < $cartikelliste;$i++) {
        $gesamtanzahlartikel++;
        $artikel = $artikelliste[$i]['artikel'];
        $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' AND lagerartikel=1 LIMIT 1");
        $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' AND lagerartikel=1 LIMIT 1");
        //wieviel stueck braucht man denn von dem artikel?

        if(is_numeric($projekt))
          $gesamtbedarf = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE objekt='lieferschein' AND projekt='$projekt' AND artikel='$artikel' ");
        else
          $gesamtbedarf = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE objekt='lieferschein' AND projekt='0' AND artikel='$artikel' ");

        //$artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt WHERE artikel='$artikel' AND projekt='$projekt'");

        // standardlager artikel 
        $standardlagerartikel = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel'");
        // Zeige nur Artikel an die im Lager sind!

        $tmp_check_standardlager = $this->app->DB->Select("SELECT SUM(lpi.menge) FROM lager_platz_inhalt lpi LEFT JOIN lager_platz l ON l.id=lpi.lager_platz WHERE 
            lpi.artikel='$artikel' AND lpi.lager_platz='$standardlagerartikel' AND l.autolagersperre!='1' AND l.sperrlager!='1'");

        // erst standarlager ausraeumen bis zu wenig drin ist
        // und dann die lager an denene am wenigsten ist
        if($tmp_check_standardlager>=$gesamtbedarf)
          $artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt lpi LEFT JOIN lager_platz l ON l.id=lpi.lager_platz WHERE 
              lpi.artikel='$artikel' AND lager_platz='$standardlagerartikel' AND l.autolagersperre!='1' AND l.sperrlager!='1' ORDER by lpi.menge ASC");
        else
          $artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt lpi LEFT JOIN lager_platz l ON l.id=lpi.lager_platz WHERE 
              lpi.artikel='$artikel' AND l.autolagersperre!='1' AND l.sperrlager!='1' ORDER by lpi.menge ASC");

        $cartikel_in_regalen = !empty($artikel_in_regalen)?count($artikel_in_regalen):0;
        for ($j = 0;$j < $cartikel_in_regalen;$j++) {
          $tmpanzahl++;
          $menge_im_platz = $artikel_in_regalen[$j]['menge'];
          $kurzbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$artikel_in_regalen[$j]['lager_platz']}' LIMIT 1");
          $lagerplatzid = $artikel_in_regalen[$j]['lager_platz'];

          if ($menge_im_platz <= $gesamtbedarf) {
            $tmpmenge = $menge_im_platz;
          } else {
            $tmpmenge = $gesamtbedarf;
          }
          $rest = $menge_im_platz - $tmpmenge; //$this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' AND firma='".$this->app->User->GetFirma()."'") - $tmpmenge;
          if ($rest == 0) {
            $rest = '-';
          }

          $orderarray[]=array('tmpmenge'=>$tmpmenge,'artikel'=>$artikel,'nummer'=>$nummer,'lager_platz'=>$lagerplatzid,"kurzbezeichnung"=>$kurzbezeichnung,"link_lagerplatzid"=>$artikel_in_regalen[$j][id],"link_lager"=>$lagerplatzid,"projekt"=>$projekt,"cmd"=>$cmd,"abkuerzung"=>$this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt' LIMIT 1"),'name_de'=>$name_de,"produktion"=>$produktion);

          $gesamtbedarf = $gesamtbedarf - $tmpmenge;
          if ($gesamtbedarf == 0) break;
        }
      }
      return $orderarray;
    }

  /**
   * @param array $orderarray
   */
    function LagerAuslagernArtikellisteRender($orderarray)
    {
      $array = $orderarray;
      $cols = array('kurzbezeichnung'=>SORT_ASC, 'nummer'=>SORT_ASC);

      $colarr = array();
      foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { 
          $colarr[$col]['_'.$k] = strtolower($row[$col]); 
        }
      }
      //$eval = 'array_multisort(';
      //foreach ($cols as $col => $order) {
      //  $eval .= '$colarr[\''.$col.'\'],'.$order.',';
      //}
      //$eval = substr($eval,0,-1).');';

      //eval($eval);
      array_multisort($colarr['kurzbezeichnung'], SORT_ASC, $colarr['kurzbezeichnung'], SORT_ASC);
      $ret = array();
      foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
          $k = substr($k,1);
          if (!isset($ret[$k])) {
            $ret[$k] = $array[$k];
          }
          $ret[$k][$col] = $array[$k][$col];
        }
      }
      $orderarray = $ret;

      $htmltable = new HTMLTable(0, '100%', '', 3, 1);
      if ($this->app->User->GetType() === 'admin') {
        $htmltable->AddRowAsHeading(array('Menge', 'Nummer', 'Artikel', 'Projekt', 'Regal', 'Regal', 'Aktion'));
      }//, 'Entfernen'));
      else {
        $htmltable->AddRowAsHeading(array('Menge', 'Nummer', 'Artikel', 'Projekt', 'Regal', 'Regal', 'Aktion'));
      }
      $htmltable->ChangingRowColors('#e0e0e0', '#fff');

      $tmpanzahl=1;
      foreach($orderarray as $row) {
        if ($tmpanzahl == 1 && $this->erstes!=1) { 
          $this->erstes=1; 
          $erstes = 'erstes';
        }
        else {
          $erstes = '';
        }

        $tmpanzahl++;

        $htmltable->NewRow();

        $htmltable->AddCol((float)$row['tmpmenge']);
        $htmltable->AddCol($row['nummer']);
        $htmltable->AddCol($row['name_de']);
        $htmltable->AddCol($row['abkuerzung']);
        $htmltable->AddCol($row['kurzbezeichnung']);

        $htmltable->AddCol("Regal: <input type=\"text\" size=\"10\" id=\"$erstes\"                 onchange=\"if(!confirm('Artikelnummer ".$row['nummer']." wurde ".$row['tmpmenge']." mal entnommen?')) return false; else window.location.href='index.php?module=lager&action=artikelfuerlieferungen&cmd=".$row['cmd']."&artikel=".$row['artikel']."&menge=".$row['tmpmenge']."&projekt=".$row['projekt']."&produktion=".$row['produktion']."&lagerplatzid=".$row['link_lagerplatzid']."&lager='+this.value;\">");            

        $htmltable->AddCol("<a href=\"#\" onclick=\"if(!confirm('Artikelnummer ".$row['nummer']." wurde ".$row['tmpmenge']." mal entnommen?')) return false; else window.location.href='index.php?module=lager&action=artikelfuerlieferungen&cmd=".$row['cmd']."&artikel=".$row['artikel']."&menge=".$row['tmpmenge']."&projekt=".$row['projekt']."&produktion=".$row['produktion']."&lagerplatzid=".$row['link_lagerplatzid']."&lager=".$row['link_lager']."';\"><img src=\"./themes/[THEME]/images/forward.svg\"></a>");            

        //        if ($this->app->User->GetType() == "admin") $htmltable->AddCol("<a href=\"#\" onclick=\"if(!confirm('Artikel aus Lieferungen und Reservierungen nehmen?')) return false; else window.location.href='index.php?module=lager&action=artikelentfernen&produktion=".$row['produktion']."&projekt=".$row['projekt']."&artikel=".$row['artikel']."&cmd=".$row['cmd']."';\"><img src=\"./themes/[THEME]/images/delete.svg\"></a>");

      }
      //bestimme regalplaetze fuer artikel
      $this->app->Tpl->Add('INHALT', $htmltable->Get());
      // und enter abfangen!!!
      $this->app->Tpl->Add('INHALT', "<script type=\"text/javascript\">document.getElementById(\"erstes\").focus(); </script>");
      //$table->DisplayNew('INHALT', "<a href=\"index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/einlagern.png\"></a>");
      $this->app->Tpl->Parse('TAB1', 'rahmen70_ohneform.tpl');

    }

    function LagerZwischenlager() {
      $this->app->Tpl->Add('TABS', '<li><h2>Zwischenlager</h2></li>');
      $id = $this->app->Secure->GetGET('id');
      $this->app->Tpl->Set('TABNAME', 'Inhalt');
      $this->app->Tpl->Set('SUBSUBHEADING', 'Zwischenlager Stand ' . date('d.m.Y'));
      // easy table mit arbeitspaketen YUI als template
      $table = new EasyTable($this->app);
      $table->Query("SELECT a.name_de,z.menge,z.vpe,z.grund,z.richtung, p.abkuerzung, z.id FROM zwischenlager z LEFT JOIN artikel a ON a.id=z.artikel LEFT JOIN projekt p ON 
          p.id=z.projekt ");
      $table->DisplayNew('INHALT', "<a href=\"index.php?module=lager&action=bewegungpopup&frame=false&id=%value%\" 
          onclick=\"makeRequest(this);return false\">Info</a>");
      $this->app->Tpl->Parse('TAB1', 'rahmen70.tpl');
      $this->app->Tpl->Set('AKTIV_TAB1', 'selected');
      $this->app->Tpl->Parse('PAGE', 'tabeinzeln.tpl');
    }
    /*function LagerBewegung() {
      $this->LagerMenu();
      $id = $this->app->Secure->GetGET('id');
      $this->app->Tpl->Set('TABNAME', "Lager Bewegungen");
      $lager = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$id' ");
      $this->app->Tpl->Set('SUBSUBHEADING', "Bewegungen Lager: $lager bis zum " . date('d.m.Y'));
      // easy table mit arbeitspaketen YUI als template
      $table = new EasyTable($this->app);
      $table->Query("SELECT p.kurzbezeichnung as Regal, 
          p.id FROM lager_platz p 
          WHERE lager='$id' ORDER by 1");
      $table->DisplayNew('INHALT', "<a href=\"index.php?module=lager&action=bewegungpopup&frame=false&id=%value%\" 
          onclick=\"makeRequest(this);return false\">Info</a>");
      $this->app->Tpl->Parse('TAB1', "rahmen70.tpl");
      $this->app->Tpl->Set('AKTIV_TAB1', "selected");
      $this->app->Tpl->Parse('PAGE', "tabeinzeln.tpl");
    }*/
    function LagerBewegungPopup() {
      $this->LagerMenu();

      $this->app->Tpl->Parse('TAB1', 'rahmen70.tpl');
      $this->app->Tpl->Set('AKTIV_TAB1', 'selected');

      $id = $this->app->Secure->GetGET('id');

      $lager = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$id' ");

      $this->app->erp->MenuEintrag('index.php?module=lager&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');

      $id = $this->app->Secure->GetGET('id');

      $this->app->Tpl->Set("MESSAGE", "<div class=\"info\" style=\"margin-top:7px\">Bewegungen Lager: $lager bis zum " . date('d.m.Y')."</div>");


      $this->app->YUI->AutoComplete('lager_platz_bewegung', 'lagerplatz');
      $this->app->YUI->AutoComplete('artikel_bewegung', 'artikelnummer');
      $this->app->YUI->AutoSaveUserParameter('lager_platz_bewegung','lager_bewegung_lagerplatz','updateLiveTable();');
      $this->app->YUI->AutoSaveUserParameter('artikel_bewegung','lager_bewegung_artikel','updateLiveTable();');


      $this->app->YUI->TableSearch('TAB1','lager_bewegunglist', 'show','','',basename(__FILE__), __CLASS__);
      $this->app->Tpl->Parse('PAGE', 'lager_bewegung_list.tpl');
    }


    function LagerAlleBewegungen()
    {
      $this->LagerHauptmenu();
      $this->app->Tpl->Set('SUBSUBHEADING', 'Alle Lagerbewegungen bis zum '.date('d.m.Y'));
      $this->app->erp->MenuEintrag('index.php?module=lager&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');

      $this->app->YUI->AutoComplete('lager_bewegung_alle', 'lager');
      $this->app->YUI->AutoComplete('lager_platz_bewegung_alle', 'lagerplatz');
      $this->app->YUI->AutoComplete('artikel_bewegung_alle', 'artikelnummer');

      $this->app->YUI->AutoSaveUserParameter('lager_bewegung_alle','lager_bewegungalle_lager','updateLiveTable();');
      $this->app->YUI->AutoSaveUserParameter('artikel_bewegung_alle','lager_bewegungalle_artikel','updateLiveTable();');
      $this->app->YUI->AutoSaveUserParameter('lager_platz_bewegung_alle','lager_bewegungalle_lagerplatz','updateLiveTable();');

      $this->app->YUI->TableSearch('TAB1','lager_allebewegungenlist', 'show','','',basename(__FILE__), __CLASS__);
      $this->app->Tpl->Parse('PAGE', "lager_allebewegungen_list.tpl");
    }


    function LagerInhalt() {

      $this->LagerMenu();

      $pdf = $this->app->Secure->GetPOST('pdf');
      $nachartikel = $this->app->Secure->GetPOST('nachartikel');

      if($pdf != '' && $nachartikel == 1){
        $this->LagerPDFArtikel();
      }elseif($pdf != '' && $nachartikel == 0){
        $this->LagerPDF();
      }

      $this->app->YUI->AutoComplete('lager_platz_inhalt', 'lagerplatz');
      $this->app->YUI->AutoComplete('artikel_inhalt', 'artikelnummer');

      $this->app->YUI->AutoSaveUserParameter('artikel_inhalt','lager_inhalt_artikel','updateLiveTable();');
      $this->app->YUI->AutoSaveUserParameter('lager_platz_inhalt','lager_inhalt_lagerplatz','updateLiveTable();');

      $this->app->YUI->TableSearch('TAB1','lager_inhaltlist', 'show','','',basename(__FILE__), __CLASS__);
      $this->app->Tpl->Parse('PAGE', 'lager_inhaltneu.tpl');
    }

    function LagerPDFArtikel(){
      $lagerid = $this->app->Secure->GetGET('id');

      $lagername = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id = '$lagerid' LIMIT 1");

      $lagerplatzuser = $this->app->User->GetParameter('lager_inhalt_lagerplatz');
      $lagerplatziduser = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung = '$lagerplatzuser' AND kurzbezeichnung != '' LIMIT 1");

      $artikeluser = $this->app->User->GetParameter('lager_inhalt_artikel');
      $artikeldaten = explode(' ', $artikeluser, 2);
      $artikelnr = $artikeldaten[0];
      $artikelname = $artikeldaten[1];
      $artikeliduser = $this->app->DB->Select("SELECT id FROM artikel WHERE name_de = '$artikelname' AND name_de != '' AND nummer = '$artikelnr' AND nummer != '' LIMIT 1");

      if($lagerplatziduser != ''){
        $lagerplatzwhere = " AND li.lager_platz = '$lagerplatziduser'";
      }else{
        $lagerplatzwhere = '';
      }

      if($artikeliduser != ''){
        $artikelwhere = "AND li.artikel = '$artikeliduser'";
      }else{
        $artikelwhere = '';
      }

      
      $where = "l.lager = '$lagerid'".$lagerplatzwhere.$artikelwhere.' ORDER BY a.nummer';

      //$artikel = $this->app->DB->SelectArr("SELECT DISTINCT a.id as id, a.name_de as name_de, a.nummer as nummer FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz LEFT JOIN artikel a ON lm.artikel=a.id WHERE $where");

      $artikel = $this->app->DB->SelectArr("SELECT DISTINCT a.id as id, a.name_de as name_de, a.nummer as nummer FROM lager_platz_inhalt li LEFT JOIN lager_platz l ON l.id=li.lager_platz LEFT JOIN artikel a ON li.artikel = a.id WHERE $where");
      
      
      //Neue PDF Datei erstellen
      $pdf=new SuperFPDF('P','mm','A4',$this->app);
      $pdf->AddPage();

      $pdf->SetFont('Arial', 'B', 15);
      $pdf->Cell(100, 8, 'Lagerbestand '.'Lager: '.$lagername, 0, 0, 'L');
      $pdf->SetFont('Arial','',10);
      $pdf->Cell(0, 8, date('d.m.Y'), 0, 1, 'R');
      $pdf->Cell(0, 8, 'Bearbeiter: '.$this->app->User->GetName(), 0, 1, 'R');
      $pdf->Ln();

      foreach($artikel as $key=>$value){
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(80, 8, $value['name_de'], 0, 0, 'L');
        $pdf->Cell(70, 8, ' Artikelnr: '.$value['nummer'], 0, 0, 'L');
        $pdf->Cell(0, 8, $pdf->Code128($pdf->GetX(), $pdf->GetY(), $this->app->erp->UmlauteEntfernen($value['nummer']), 30, 5), 0, 1, "L");
        $pdf->SetFont('Arial','',10);


        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+190, $pdf->GetY());
        

        $artikelid = $value['id'];
        $artikelnr = $value['nummer'];
        $artikelname = $value['name_de'];

        //$regale = $this->app->DB->SelectArr("SELECT DISTINCT l.id as id, l.kurzbezeichnung as kurzbezeichnung FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz LEFT JOIN artikel a ON lm.artikel=a.id WHERE l.lager = '$lagerid' AND lm.artikel = '".$value['id']."' $lagerplatzwhere ORDER BY l.id");

        $regale = $this->app->DB->SelectArr("SELECT DISTINCT l.id as id, l.kurzbezeichnung as kurzbezeichnung FROM lager_platz l LEFT JOIN lager_platz_inhalt li ON l.id = li.lager_platz LEFT JOIN artikel a ON a.id = li.artikel WHERE l.lager = '$lagerid' AND li.artikel = '".$value['id']."' $lagerplatzwhere ORDER BY l.id");
        
        
        foreach($regale as $key2=>$value2){
          $pdf->Ln(4);
          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(40, 5, $value2['kurzbezeichnung'], 0, 0, 'L');

          $gesamt = $this->app->DB->Select('SELECT '.$this->app->erp->FormatMenge('SUM(li.menge)')." as menge FROM lager_platz_inhalt li JOIN lager_platz l ON li.lager_platz = l.id WHERE li.artikel = '".$value['id']."' AND li.lager_platz = '".$value2['id']."'");

          $pdf->Cell(0, 5, 'Gesamtbestand: '.$gesamt, 0, 1, 'L');

          $pdf->SetFont('Arial','',10);

          $artikelvorhanden = $this->app->DB->Select("SELECT lm.id FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz LEFT JOIN artikel a ON lm.artikel = a.id WHERE l.lager = '$lagerid' AND lm.artikel = '".$value['id']."' AND lm.lager_platz = '".$value2['id']."' LIMIT 1");

          
          if($artikelvorhanden != ''){

            $chargenmhd = $this->app->DB->SelectArr("SELECT ".$this->app->erp->FormatMenge('lm.menge')." as menge, lm.mhddatum as mhd, lm.charge as charge, lm.internebemerkung as internebemerkung, lm.artikel as artikel FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz LEFT JOIN artikel a ON lm.artikel=a.id WHERE l.lager = '$lagerid' AND lm.artikel = '".$value['id']."' AND lm.lager_platz = '".$value2['id']."'");

            $gesamtchargen = $this->app->DB->Select("SELECT ".$this->app->erp->FormatMenge('SUM(lm.menge)')." as menge FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id = lm.lager_platz LEFT JOIN artikel a ON lm.artikel = a.id WHERE l.lager = '$lagerid' AND lm.artikel = '".$value['id']."' AND lm.lager_platz = '".$value2['id']."'");

            $pdf->Cell(40, 5, $pdf->Code128($pdf->GetX(), $pdf->GetY(), $this->app->erp->UmlauteEntfernen($value2['kurzbezeichnung']), 30, 5), 0, 0, "L");

            $pdf->SetTableWidths(array(40,30,40));
            $pdf->SetFont('Arial','B',10);
            $pdf->SetTableFonts(array(array('Arial','B',10),array('Arial','B',10),array('Arial','B',10)));
            $pdf->TableRow(array('Menge', 'MHD', 'Charge'));
            $pdf->SetFont('Arial','',10);
            $pdf->SetTableFonts(array(array('Arial','',10),array('Arial','',10),array('Arial','',10)));

            foreach($chargenmhd as $key3=>$value3){
              $pdf->SetX($pdf->GetX()+40);
              $pdf->TableRow(array($value3['menge'], date('d.m.Y', strtotime($value3['mhd'])), $value3['charge']));

            }

            if($gesamt != $gesamtchargen){
              if($gesamtchargen < $gesamt){
                $zuwenig = $gesamt-$gesamtchargen;
                $pdf->Cell(0, 5, "", 0, 1, "L");
                $pdf->SetX($pdf->GetX()+40);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 5, 'Hinweis: Es sind '.$zuwenig.' Chargen-/MHD Einträge zu wenig vorhanden.', 0, 1, 'L');
                $pdf->SetFont('Arial','',10);
              }
              $zuviel = $gesamtchargen - $gesamt;
              $pdf->Cell(0, 5, '', 0, 1, 'L');
              $pdf->SetX($pdf->GetX()+40);
              $pdf->SetFont('Arial', 'B', 10);
              $pdf->Cell(0, 5, 'Hinweis: Es sind '.$zuviel.' Chargen-/MHD Einträge zu viel vorhanden.', 0, 1, 'L');
              $pdf->SetFont('Arial','',10);
            }


          }else{
            $pdf->Cell(40, 5, $pdf->Code128($pdf->GetX(), $pdf->GetY(), $value2['kurzbezeichnung'], 30, 5), 0, 0, "L");
            $pdf->Ln();
          }

          $pdf->Ln();
          $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+190, $pdf->GetY());
          //$pdf->Ln();
        }
        $pdf->Ln();
        $pdf->Ln();
      }

      $pdf->Output(date('Ymd').'_'.'Artikelbestandsliste_nach_Artikel.pdf','D');
      $this->app->ExitXentral();
    }

    function LagerPDF(){
      $lagerid = $this->app->Secure->GetGET('id');

      $lagername = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id = '$lagerid' LIMIT 1");

      $lagerplatzuser = $this->app->User->GetParameter('lager_inhalt_lagerplatz');
      $lagerplatziduser = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung = '$lagerplatzuser' AND kurzbezeichnung != '' LIMIT 1");

      $artikeluser = $this->app->User->GetParameter('lager_inhalt_artikel');
      $artikeldaten = explode(" ", $artikeluser, 2);
      $artikelnr = $artikeldaten[0];
      $artikelname = $artikeldaten[1];
      $artikeliduser = $this->app->DB->Select("SELECT id FROM artikel WHERE name_de = '$artikelname' AND name_de != '' AND nummer = '$artikelnr' AND nummer != '' LIMIT 1");


      if($lagerplatziduser != ''){
        $lagerplatzwhere = " AND li.lager_platz = '$lagerplatziduser'";
      }else{
        $lagerplatzwhere = '';
      }

      if($artikeliduser != ''){
        $artikelwhere = "AND li.artikel = '$artikeliduser'";
      }else{
        $artikelwhere = '';
      }
      
      $where = "l.lager = '$lagerid'".$lagerplatzwhere.$artikelwhere." ORDER BY a.nummer";

      //$regale = $this->app->DB->SelectArr("SELECT DISTINCT l.id as id, l.kurzbezeichnung as kurzbezeichnung FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz LEFT JOIN artikel a ON lm.artikel=a.id WHERE l.lager = '$lagerid' $lagerplatzwhere ORDER BY l.id");

      $regale = $this->app->DB->SelectArr("SELECT DISTINCT l.id as id, l.kurzbezeichnung as kurzbezeichnung FROM lager_platz l LEFT JOIN lager_platz_inhalt li ON l.id = li.lager_platz LEFT JOIN artikel a ON a.id = li.artikel WHERE l.lager = '$lagerid' $lagerplatzwhere $artikelwhere ORDER BY l.kurzbezeichnung");

      //Neue PDF Datei erstellen
      $pdf=new SuperFPDF('P','mm','A4',$this->app);
      $pdf->AddPage();

      $pdf->SetFont('Arial', 'B', 15);
      $pdf->Cell(100, 8, 'Lagerbestand '.'Lager: '.$lagername, 0, 0, 'L');
      $pdf->SetFont('Arial','',10);
      $pdf->Cell(0, 8, date('d.m.Y'), 0, 1, 'R');
      $pdf->Cell(0, 8, 'Bearbeiter: '.$this->app->User->GetName(), 0, 1, 'R');
      $pdf->Ln();

      foreach($regale as $key=>$value){

        $pdf->SetFont('Arial','B',15);
        $pdf->Cell(80, 10, $value['kurzbezeichnung'], 0, 0, 'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0, 10, $pdf->Code128($pdf->GetX(), $pdf->GetY(), $this->app->erp->UmlauteEntfernen($value['kurzbezeichnung']), 50, 7), 0, 1, "L");

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, 5, 'Artikelnr', 0, 0, 'L');
        $pdf->Cell(80, 5, 'Artikel', 0, 1, 'L');
        $pdf->SetFont('Arial','',10);

        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+190, $pdf->GetY());

        //$artikel = $this->app->DB->SelectArr("SELECT DISTINCT a.id as id, a.name_de as name_de, a.nummer as nummer FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz LEFT JOIN artikel a ON lm.artikel=a.id WHERE lm.lager_platz = '".$value['id']."' $artikelwhere ORDER BY lm.artikel");

        $artikel = $this->app->DB->SelectArr("SELECT DISTINCT a.id as id, a.name_de as name_de, a.nummer as nummer FROM lager_platz_inhalt li LEFT JOIN lager_platz l ON l.id=li.lager_platz LEFT JOIN artikel a ON li.artikel = a.id WHERE li.lager_platz = '".$value['id']."' $artikelwhere ORDER BY li.artikel");

        foreach($artikel as $key2=>$value2){
          $pdf->Ln(4);

          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(40, 5, $value2['nummer'], 0, 0, 'L');
          $pdf->Cell(30, 5, $value2['name_de'], 0, 1, 'L');
          $pdf->SetFont('Arial','',10);
          
          $gesamt = $this->app->DB->Select("SELECT ".$this->app->erp->FormatMenge('SUM(li.menge)')." as menge FROM lager_platz_inhalt li JOIN lager_platz l ON li.lager_platz = l.id WHERE li.artikel = '".$value2['id']."' AND li.lager_platz = '".$value['id']."'");

          $pdf->SetX($pdf->GetX()+40);
          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(0, 8, 'Gesamtbestand: '.$gesamt, 0, 1, 'L');
          $pdf->SetFont('Arial','',10);

          $pdf->Cell(40, 5, $pdf->Code128($pdf->GetX(),$pdf->GetY(),$this->app->erp->UmlauteEntfernen($value2['nummer']),30,5), 0, 0, "L");

          $artikelvorhanden = $this->app->DB->Select("SELECT lm.id FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz LEFT JOIN artikel a ON lm.artikel = a.id WHERE l.lager = '$lagerid' AND lm.artikel = '".$value2['id']."' AND lm.lager_platz = '".$value['id']."' LIMIT 1");

          if($artikelvorhanden != ''){

            $chargenmhd = $this->app->DB->SelectArr('SELECT '.$this->app->erp->FormatMenge('lm.menge')." as menge, lm.mhddatum as mhd, lm.charge as charge, lm.internebemerkung as internebemerkung, lm.artikel as artikel FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz LEFT JOIN artikel a ON lm.artikel=a.id WHERE l.lager = '$lagerid' AND lm.artikel = '".$value2['id']."' AND lm.lager_platz = '".$value['id']."'");

            $gesamtchargen = $this->app->DB->Select('SELECT '.$this->app->erp->FormatMenge('SUM(lm.menge)')." as menge FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id = lm.lager_platz LEFT JOIN artikel a ON lm.artikel = a.id WHERE l.lager = '$lagerid' AND lm.artikel = '".$value2['id']."' AND lm.lager_platz = '".$value['id']."'");

            //$pdf->SetX($pdf->GetX()+40);

            $pdf->SetTableWidths(array(40,30,40));
            $pdf->SetFont('Arial','B',10);
            $pdf->SetTableFonts(array(array('Arial','B',10),array('Arial','B',10),array('Arial','B',10)));
            $pdf->TableRow(array('Menge', 'MHD', 'Charge'));
            $pdf->SetFont('Arial','',10);
            $pdf->SetTableFonts(array(array('Arial','',10),array('Arial','',10),array('Arial','',10)));
            
            foreach($chargenmhd as $key3=>$value3){
              $pdf->SetX($pdf->GetX()+40);
              $pdf->TableRow(array($value3['menge'], date('d.m.Y', strtotime($value3['mhd'])), $value3['charge']));

            }

            if($gesamt != $gesamtchargen){
              if($gesamtchargen < $gesamt){
                $zuwenig = $gesamt - $gesamtchargen;
                $pdf->Cell(0, 5, '', 0, 1, 'L');
                $pdf->SetX($pdf->GetX()+40);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 5, "Hinweis: Es sind ".$zuwenig." Chargen-/MHD Einträge zu wenig vorhanden.", 0, 1, "L");
                $pdf->SetFont('Arial','',10);
              }else{
                $zuviel = $gesamtchargen - $gesamt;
                $pdf->Cell(0, 5, '', 0, 1, 'L');
                $pdf->SetX($pdf->GetX()+40);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 5, "Hinweis: Es sind ".$zuviel." Chargen-/MHD Einträge zu viel vorhanden.", 0, 1, "L");
                $pdf->SetFont('Arial','',10);
              }
            }
          }else{
            $pdf->Ln();
          }

          $pdf->Ln();
          $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+190, $pdf->GetY());
          //$pdf->Ln();
        }
        $pdf->Ln();
        $pdf->Ln();
      }

      $pdf->Output(date('Ymd').'_'.'Artikelbestandsliste.pdf','D');
      $this->app->ExitXentral();
    }
    
    public function LagerPlatz() {
      $this->LagerMenu();
      $id = $this->app->Secure->GetGET('id');
      // neues arbeitspaket


      $import = $this->app->Secure->GetPOST('import');
      $importcsv = $this->app->Secure->GetPOST('importcsv');
      if($import!='' || $importcsv!='')
      {
        if($import!='')
        {        
          $lagerimport = $this->app->Secure->GetPOST('lagerimport');
          $lagerimport = str_replace(['\\r\\n','"', ' '],["\r\n",'',''],$lagerimport);
          if (strpos($lagerimport, ',') !== false) {
            $tmp = explode(',',$lagerimport);
          } else {
            $tmp = explode("\r\n",$lagerimport);
          }
        } else if ($importcsv!='') {
          $lagerimport = file_get_contents($_FILES['csv']['tmp_name']);
          $lagerimport = str_replace(['\\r\\n','"', ' ',','],["\r\n",'','',';'],$lagerimport);
          
          if (strpos($lagerimport, ';') !== false) {
            $tmp2 = explode("\n",$lagerimport);
            foreach($tmp2 as $key=>$row)
            {
              $rowtmp = explode(";",$row);
              if(isset($rowtmp[0]) && $rowtmp[0]!="")
              {
                $tmp[] = $rowtmp[0];
                $tmpsort[] = $rowtmp[1];
              }
            }
          }
          else {
            $tmp = explode("\r\n",$lagerimport);
          }
        }

        $neue=0;
        $neuesort=0;
        $ctmp = !empty($tmp)?count($tmp):0;
        for($i=0;$i<$ctmp;$i++)
        {
          $lagerabkuerzung = $tmp[$i];
          $lagersort = $tmpsort[$i];
          // new line + spaces entfernen
          $lagerabkuerzung = trim(preg_replace('/\s+/', ' ', $lagerabkuerzung));
          $lagersort = trim(preg_replace('/\s+/', ' ', $lagersort));

          if(empty($lagerabkuerzung)){
            continue;
          }

          $check = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$lagerabkuerzung' LIMIT 1");
          if($check <= 0)
          {
            // Anlegen
            $check = $this->app->erp->CreateLagerplatz($id,$lagerabkuerzung);
                      
            $neue++;
          }
          if($check > 0 && $lagersort!="")
          {
            $neuesort++;
            $this->app->DB->Update("UPDATE lager_platz SET rownumber='".$lagersort."' WHERE id='".$check."' LIMIT 1");
          }
        }

      

        $this->app->Tpl->Set('IMPORT',$lagerimport);

        if(count($tmp) > 0) {
          $sorttxt = ($neuesort > 0?" ($neuesort x Reihenfolge angepasst)":"");
          if($neue == 1){
            $this->app->Tpl->Set('MESSAGE3', "<div class=\"info\">$neue Regal wurde neu angelegt$sorttxt!</div>");
          }
          else if($neue > 1){
            $this->app->Tpl->Set('MESSAGE3', "<div class=\"info\">$neue Lagerpl&auml;tze wurden neu angelegt$sorttxt!</div>");
          }
          else{
            $this->app->Tpl->Set('MESSAGE3', "<div class=\"info\">Keine neuen Lagerpl&auml;tze angelegt! Alle bereits gefunden$sorttxt.</div>");
          }
        }
        else{
          $this->app->Tpl->Set('MESSAGE3', "<div class=\"error\">Es wurden keine Lagerpl&auml;tze angegeben!</div>");
        }
      } 

      $speichern = $this->app->Secure->GetPOST('speichern');

      if($speichern!='') {
        $kurzbezeichnung = $this->app->Secure->GetPOST('kurzbezeichnung');
        $autolagersperre=$this->app->Secure->GetPOST('autolagersperre');
        $verbrauchslager=$this->app->Secure->GetPOST('verbrauchslager');
        $breite=$this->app->Secure->GetPOST('breite');
        $laenge=$this->app->Secure->GetPOST('laenge');
        $hoehe=$this->app->Secure->GetPOST('hoehe');
        $sperrlager=$this->app->Secure->GetPOST('sperrlager');
        $poslager=$this->app->Secure->GetPOST('poslager');
        $abckategorie=$this->app->Secure->GetPOST('abckategorie');
        $regalart=$this->app->Secure->GetPOST('regalart');
        $rownumber=(int)$this->app->Secure->GetPOST('rownumber');
        $allowproduction=(int)$this->app->Secure->GetPOST('allowproduction');

        $allowed = '/[^a-z0-9A-Z\-\_\.]/i';
        $kurzbezeichnung = preg_replace($allowed,'',$kurzbezeichnung);
        $kurzbezeichnung =  substr($kurzbezeichnung,0,15);

        $adresse=$this->app->Secure->GetPOST('adresse');
        $adresse = trim($adresse);      
        $rest = explode(' ',$adresse);      
        $rest = $rest[0];      
        $adresseid =  $this->app->DB->Select("SELECT id FROM adresse WHERE id='$rest' AND geloescht=0 LIMIT 1");

        $check = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$kurzbezeichnung' AND kurzbezeichnung!='' LIMIT 1");
        if($check<=0 && $kurzbezeichnung!='') {
          $breite = str_replace(',','.',$breite);
          $hoehe = str_replace(',','.',$hoehe);
          $laenge = str_replace(',','.',$laenge);

          $this->app->DB->Insert(
            sprintf(
              "INSERT INTO lager_platz 
                (lager,kurzbezeichnung,autolagersperre,verbrauchslager,sperrlager,
                 breite,laenge,hoehe,poslager,adresse,abckategorie,regalart,rownumber,allowproduction)
              VALUES 
                     (%d,'%s',%d,%d,%d,%f,%f,%f,%d,%d,'%s','%s', %d, %d)",
              $id, $kurzbezeichnung, $autolagersperre,$verbrauchslager,$sperrlager,
              $breite, $laenge, $hoehe,$poslager,$adresseid, $abckategorie,$regalart, $rownumber,$allowproduction
            )
          );

          $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Das Regal wurde angelegt!</div>");
          $this->app->Location->execute("index.php?module=lager&action=platz&id=$id&msg=$msg");
        }
        if($kurzbezeichnung==''){
          $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Das Regal wurde nicht angelegt! Bitte geben Sie einen Namen an!</div>");
        }
        else{
          $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Das Regal wurde nicht angelegt! Der Name existiert bereits in diesem oder einem anderem Lager. 
              Bitte einen anderen w&auml;hlen!</div>");
        }
      }

      $this->app->YUI->AutoComplete('adresse','adresse');

      $this->app->Tpl->Set('KURZBEZEICHNUNG',$kurzbezeichnung);
      $this->app->Tpl->Set('ADRESSE',$adresse);
      if($autolagersperre=='1') {
        $this->app->Tpl->Set('AUTOLAGERSPERRE','checked');
      }
      if($poslager=='1') {
        $this->app->Tpl->Set('POSLAGER','checked');
      }
      if($verbrauchslager=='1') {
        $this->app->Tpl->Set('VERBRAUCHSLAGER','checked');
      }
      if($sperrlager=='1') {
        $this->app->Tpl->Set('SPERRLAGER','checked');
      }

      if($allowproduction=='1') {
        $this->app->Tpl->Set('ALLOWPRODUCTION','checked');
      }

      $this->app->Tpl->Set('ABCKATEGORIE',$this->app->erp->GetLagerABCKategorie($abckategorie));
      $this->app->Tpl->Set('REGALART',$this->app->erp->GetLagerRegalart($regalart));
      $this->app->YUI->HideFormular('sperrlager',['checked'=>'dummy','unchecked'=>'trsperrlager']);
      $this->app->Tpl->Parse('TAB2', "lager_platz.tpl");


      $this->app->Tpl->Set('SUBSUBHEADING', 'Lagerpl&auml;tze');

      $this->app->YUI->TableSearch('TAB1', 'lagerplatztabelle', 'show','','',basename(__FILE__), __CLASS__);

      $this->app->Tpl->Parse('PAGE', 'lagerplatzuebersicht.tpl');
    }

    public function LagerPlatzEditPopup() {
      $frame = $this->app->Secure->GetGET('frame');
      $id = $this->app->Secure->GetGET('id');
      // nach page inhalt des dialogs ausgeben
      //      $widget = new WidgetLager_platz($this->app,TAB1);
      $sid = $this->app->DB->Select("SELECT lager FROM lager_platz WHERE id='$id' LIMIT 1");

      $this->app->erp->MenuEintrag("index.php?module=lager&action=platz&id=$sid","zur&uuml;ck zur &Uuml;bersicht");

      $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=lager&action=platz&id=$sid';\">");
      //      $widget->form->SpecialActionAfterExecute("close_refresh", "index.php?module=lager&action=platz&id=$sid");
      //      $widget->Edit();

      $speichern = $this->app->Secure->GetPOST('speichern');

      if($speichern!='')
      {
        $kurzbezeichnung = $this->app->Secure->GetPOST('kurzbezeichnung');
        $autolagersperre=$this->app->Secure->GetPOST('autolagersperre');
        $verbrauchslager=$this->app->Secure->GetPOST('verbrauchslager');
        $sperrlager=$this->app->Secure->GetPOST('sperrlager');
        $poslager=$this->app->Secure->GetPOST('poslager');
        $breite=$this->app->Secure->GetPOST('breite');
        $laenge=$this->app->Secure->GetPOST('laenge');
        $hoehe=$this->app->Secure->GetPOST('hoehe');
        $adresse=$this->app->Secure->GetPOST('adresse');
        $abckategorie=$this->app->Secure->GetPOST('abckategorie');
        $regalart=$this->app->Secure->GetPOST('regalart');
        $rownumber = $this->app->Secure->GetPOST('rownumber');
        $allowproduction = $this->app->Secure->GetPOST('allowproduction');

        $allowed = '/[^a-z0-9A-Z\-\_\.]/i';
        $kurzbezeichnung = preg_replace($allowed,'',$kurzbezeichnung);
        $kurzbezeichnung =  substr($kurzbezeichnung,0,15);
  
        $adresse=$this->app->Secure->GetPOST('adresse');
        $adresse = trim($adresse);      
        $rest = explode(' ',$adresse);      
        $rest = $rest[0];      
        $adresseid =  $this->app->DB->Select("SELECT id FROM adresse WHERE id='$rest' AND geloescht=0 LIMIT 1");


        $check = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$kurzbezeichnung' AND kurzbezeichnung!='' AND id!='$id' LIMIT 1");
        if($check<=0 && $kurzbezeichnung!='')
        {
          $breite = str_replace(',','.',$breite);
          $hoehe = str_replace(',','.',$hoehe);
          $laenge = str_replace(',','.',$laenge);

          $this->app->DB->Update(
            sprintf(
              "UPDATE lager_platz 
              SET kurzbezeichnung='%s',autolagersperre=%d,verbrauchslager=%d,sperrlager=%d,poslager=%d, adresse=%d,
              breite=%f,laenge=%f,hoehe=%f,abckategorie='%s',regalart='%s', rownumber = %d, allowproduction = %d
              WHERE id=%d 
              LIMIT 1",
              $kurzbezeichnung,$autolagersperre, $verbrauchslager,$sperrlager,$poslager,$adresseid,
              $breite,$laenge,$hoehe,$abckategorie,$regalart,$rownumber,$allowproduction,
              $id
            )
          );

          $msg = $this->app->erp->base64_url_encode('<div class="info">Das Regal wurde ge&auml;ndert!</div>');
          $this->app->Location->execute("index.php?module=lager&action=platz&id=$sid&msg=$msg");
        }
        if($kurzbezeichnung==''){
          $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Das Regal wurde nicht ge&auml;ndert! Bitte geben Sie einen Namen an!</div>");
        }
        else{
          $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Das Regal wurde nicht ge&auml;ndert! Der Name existiert in diesem oder einen anderem Lager bereits. Bitte einen anderen w&auml;hlen!</div>");
        }
        $this->app->Tpl->Set('ADRESSE',$adresse);
        $this->app->Tpl->Set('KURZBEZEICHNUNG',$kurzbezeichnung);
        if($autolagersperre=='1') {
          $this->app->Tpl->Set('AUTOLAGERSPERRE','checked');
        }
        if($verbrauchslager=='1') {
          $this->app->Tpl->Set('VERBRAUCHSLAGER','checked');
        }
        if($sperrlager=='1') {
          $this->app->Tpl->Set('SPERRLAGER','checked');
        }
        if($poslager=='1') {
          $this->app->Tpl->Set('POSLAGER','checked');
        }
        if($allowproduction=='1') {
          $this->app->Tpl->Set('ALLOWPRODUCTION','checked');
        }

        $this->app->Tpl->Set('ABCKATEGORIE',$this->app->erp->GetLagerABCKategorie($abckategorie));
        $this->app->Tpl->Set('REGALART',$this->app->erp->GetLagerRegalart($regalart));

      } else {

        $tmp = $this->app->DB->SelectRow(
          sprintf(
            'SELECT * FROM lager_platz WHERE id=%d LIMIT 1', 
            $id
          )
        );
        $kurzbezeichnung = $tmp['kurzbezeichnung'];
        $autolagersperre = $tmp['autolagersperre'];
        $verbrauchslager = $tmp['verbrauchslager'];
        $adresse = $tmp['adresse'];

        $breite = $tmp['breite'];
        $laenge = $tmp['laenge'];
        $hoehe = $tmp['hoehe'];
        $rownumber = $tmp['rownumber'];

        $sperrlager = $tmp['sperrlager'];
        $poslager = $tmp['poslager'];
        $abckategorie = $tmp['abckategorie'];
        $regalart = $tmp['regalart'];
        $allowproduction = $tmp['allowproduction'];
        $this->app->Tpl->Set('KURZBEZEICHNUNG',$kurzbezeichnung);
        $adresse = $this->app->DB->Select("SELECT CONCAT(id,' ',name) FROM adresse WHERE id='$adresse' LIMIT 1");
        $this->app->Tpl->Set('ADRESSE',$adresse);

        $this->app->Tpl->Set('LAENGE',$laenge);
        $this->app->Tpl->Set('BREITE',$breite);
        $this->app->Tpl->Set('HOEHE',$hoehe);
        $this->app->Tpl->Set('ROWNUMBER',$rownumber);

        $this->app->Tpl->Set('ABCKATEGORIE',$this->app->erp->GetLagerABCKategorie($abckategorie));
        $this->app->Tpl->Set('REGALART',$this->app->erp->GetLagerRegalart($regalart));

        if($autolagersperre=='1') {
          $this->app->Tpl->Set('AUTOLAGERSPERRE','checked');
        }
        if($verbrauchslager=='1') {
          $this->app->Tpl->Set('VERBRAUCHSLAGER','checked');
        }
        if($sperrlager=='1') {
          $this->app->Tpl->Set('SPERRLAGER','checked');
        }
        if($poslager=='1') {
          $this->app->Tpl->Set('POSLAGER','checked');
        }

        if($allowproduction == '1') {
          $this->app->Tpl->Set('ALLOWPRODUCTION','checked');
        }
      }

      $this->app->erp->MenuEintrag('index.php?module=lager&action=platzeditpopup&id='.$id,'Details'); 
      $this->app->YUI->AutoComplete('adresse','adresse');
      $this->app->YUI->HideFormular('sperrlager',['checked'=>'dummy','unchecked'=>'trsperrlager']);
      $this->app->Tpl->Parse('TAB1', 'lager_platz.tpl');

      $this->app->Tpl->Set('TABNAME', 'Regal');
      $this->app->Tpl->Parse('PAGE', 'tabview.tpl');
    }

    public function LagerCreate() {

      $this->app->erp->Headlines('Lager', 'anlegen');
      $this->app->erp->MenuEintrag('index.php?module=lager&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
      parent::LagerCreate();
    }

    public function LagerEtikettenlist()
    {
      $this->app->erp->Headlines('','Etiketten');

      $this->app->YUI->AutoComplete('von','lagerplatz');
      $this->app->YUI->AutoComplete('bis','lagerplatz');

      $etikettenauswahl = $this->app->Secure->GetPOST('etikettenauswahl');
      $etikettendrucker = $this->app->Secure->GetPOST('etikettendrucker');
      $von = $this->app->Secure->GetPOST('von');
      $bis = $this->app->Secure->GetPOST('bis');

      $cmd = $this->app->Secure->GetGET('cmd');
      $data = $this->app->Secure->GetGET('data');

      if($cmd==='holeanzahl'){
        if($von!='')
        {
          $error = '';
          $data = $this->app->erp->base64_url_encode(serialize(array('drucker'=>$etikettendrucker,'etiketten'=>$etikettenauswahl,'von'=>$von,'bis'=>$bis)));
          $tmp = $this->app->DB->SelectArr("SELECT kurzbezeichnung FROM lager_platz WHERE kurzbezeichnung >='$von' AND kurzbezeichnung<='$bis'");

          if(count($tmp)>0){
            echo json_encode(array('status'=>1, 'anzahl' => count($tmp), 'daten'=>$data));
            $this->app->ExitXentral();
          }
          
          $error .= 'In diesem Bereich wurden keine Lagerplätze gefunden!';
          echo json_encode(array('status'=>0,'statusText'=>$error));
          $this->app->ExitXentral();
        }
        $this->app->ExitXentral();
      }
      if($cmd==='print')
      {
        $data = unserialize($this->app->erp->base64_url_decode($data));

        $arr = $this->app->DB->SelectArr("SELECT kurzbezeichnung,id FROM lager_platz WHERE kurzbezeichnung >='".$data['von']."' AND kurzbezeichnung<='".$data['bis']."' ORDER by kurzbezeichnung");
        if($data['etiketten']>0){
          $label = $data['etiketten'];
        }
        else
        {
          $label = "lagerplatz_klein";
        }
        $carr = !empty($arr)?count($arr):0;
        for ($i = 0;$i < $carr;$i++) {
          $this->app->erp->EtikettenDrucker($label,1,'lager_platz',$arr[$i]['id'],'','',$data['drucker']);
        }

        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Etiketten wurden gedruckt!</div>");
        echo json_encode(array('status'=>1));
        $this->app->ExitXentral();
      }
      $this->LagerHauptmenu();
      $etiketten = $this->app->erp->GetSelectEtiketten("lagerplatz_klein",$etikettenauswahl);
      if($etiketten=="") $etiketten="<option>Standard</option>";

      $drucker = $this->app->erp->GetSelectEtikettenDrucker($etikettendrucker);

      $druckbutton = "<input type=\"button\" onclick=\"Etikettendrucken()\" class=\"btnBlue\" value=\"{|Drucken|}\">";
      $this->app->Tpl->Set('FORMULAR',"<form action=\"\" method=\"post\"><table class=\"mkTableFormular\">
        <tr><td>{|Lagerplatz|} ({|von|}):</td><td><input type=\"text\" name=\"von\" id=\"von\" value=\"$von\"></td></tr>
        <tr><td>{|Lagerplatz|} ({|bis|}):</td><td><input type=\"text\" name=\"bis\" id=\"bis\" value=\"$bis\">&nbsp;$druckbutton</td></tr>
        <tr><td>{|Etikett|}:</td><td><select name=\"etikettenauswahl\" id=\"etikettenauswahl\">".$etiketten."</select></td></tr>
        <tr><td>{|Drucker|}:</td><td><select name=\"etikettendrucker\" id=\"etikettendrucker\">".$drucker."</select></td></tr></table>
        </form><br><br>");
       
      $this->app->Tpl->Parse('PAGE', 'lager_etikettenlist.tpl');
    }

    public function LagerHauptmenu() {
      if($this->app->Secure->GetGET('action')!=='list') {
        $this->app->erp->MenuEintrag('index.php?module=lager&action=list','zur&uuml;ck zur &Uuml;bersicht');
      }
      
      $this->app->erp->MenuEintrag('index.php?module=lager&action=list', '&Uuml;bersicht');
      $this->app->erp->MenuEintrag('index.php?module=lager&action=etikettenlist', 'Lageretiketten');
      $this->app->erp->MenuEintrag('index.php?module=lager&action=create', 'Neues Lager anlegen');
      $this->app->erp->MenuEintrag('index.php?module=lager&action=bestand', 'Lagerbestand');
      $this->app->erp->MenuEintrag('index.php?module=lager&action=wert', 'Lagerbestandsberechnung');
      $this->app->erp->MenuEintrag('index.php?module=lager&action=allebewegungen', 'Bewegungen');

      if($this->app->erp->Version()!=='stock')
      {
      }

      if($this->app->erp->RechteVorhanden('lagerinventur','bestand')){
       $this->app->erp->MenuEintrag('index.php?module=lagerinventur&action=bestand', 'Inventur');
      }

      $this->app->erp->Headlines('Lagerverwaltung');
      $this->app->erp->RunMenuHook('lager_hauptmenu');
    }


    function LagerDoppelteWarnung()
    {
      $check_double_lager = $this->app->DB->SelectArr("SELECT bezeichnung, COUNT(bezeichnung) AS NumOccurrences FROM lager WHERE geloescht!=1 GROUP BY bezeichnung HAVING ( COUNT(bezeichnung) > 1 )");    
      $ccheck_double_lager = !empty($check_double_lager)?count($check_double_lager):0;
      if($ccheck_double_lager>0) {
        $bezeichnung = '';
        for($icheck=0;$icheck<$ccheck_double_lager;$icheck++) {
          $bezeichnung .= ' ' . $check_double_lager[$icheck]['bezeichnung'];
        }
        /*if(trim($bezeichnung)=='') {
          $belege='ohne Bezeichnung';
        }*/

        //$gesamt_lager= $ccheck_double_lager;
        $this->app->Tpl->Set('MESSAGE','<div class="error">Achtung! Doppelte Bezeichnungen: '.$bezeichnung.'</div>');      
        //$this->app->erp->InternesEvent($this->app->User->GetID(),'Achtung! Doppelte Bezeichnungen: '.$bezeichnung,"warning",0);
      }
    }

    public function LagerList() {
      $this->LagerHauptmenu();
      $this->LagerDoppelteWarnung();

      $this->app->YUI->TableSearch('TAB1', 'lagertabelle', 'show','','',basename(__FILE__), __CLASS__);
      $this->app->Tpl->Parse('PAGE', "lageruebersicht.tpl");
    }


    public function LagerMenu() {
      $id = $this->app->Secure->GetGET('id');
      $this->app->erp->Headlines('Lager');

      $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$id' LIMIT 1");

      $this->app->erp->Headlines('Lagerverwaltung', $bezeichnung);

      $this->app->erp->MenuEintrag("index.php?module=lager&action=edit&id=$id", "Details");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=platz&id=$id", "Regale");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=inhalt&id=$id", "Bestand");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=bewegung&id=$id", "Bewegungen");

      $this->app->erp->MenuEintrag("index.php?module=lager&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
    }

    public function LagerEdit() {
      $this->app->Tpl->Set('AKTIV_TAB1', "selected");
      $this->app->Tpl->Set('ABBRECHEN',"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=lager&action=list';\">");
      parent::LagerEdit();
      $this->LagerDoppelteWarnung();
      $this->LagerMenu();
    }

    public function LagerEtiketten() {
      $this->LagerMenu();
      $this->app->Tpl->Set('PAGE', "<br><br><br>Etiketten");
    }
}
