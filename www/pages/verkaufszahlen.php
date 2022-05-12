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
use Xentral\Widgets\Chart\BarDataset;
use Xentral\Widgets\Chart\Chart;
use Xentral\Widgets\Chart\HtmlRenderer;
use Xentral\Widgets\Chart\PeriodMatcher;
use Xentral\Widgets\Chart\PieDataset;

class Verkaufszahlen {
  /** @var Application $app */
  var $app;
  /** @var string */
  const MODULE_NAME = 'SalesFigure';
  /** @var bool */
  protected $useJson = true;
  /**
   * Verkaufszahlen constructor.
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

    $this->app->ActionHandler("list","VerkaufszahlenList");
    $this->app->ActionHandler("details","VerkaufszahlenDetails");

    $this->app->Tpl->Set('UEBERSCHRIFT','Verkaufszahlen');
    $this->app->ActionHandlerListen($app);
  }
  
  public function Install()
  {
    $this->app->erp->CheckTable('verkaufszahlen_chart');
    $this->app->erp->CheckColumn('id','int(11)','verkaufszahlen_chart','DEFAULT 0 NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('aktiv', 'tinyint(1)', 'verkaufszahlen_chart', 'DEFAULT 1 NOT NULL');
    $this->app->erp->CheckColumn('regs', 'tinyint(1)', 'verkaufszahlen_chart', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('monat', 'tinyint(1)', 'verkaufszahlen_chart', 'DEFAULT 1 NOT NULL');
    $this->app->erp->CheckColumn('bezeichnung', 'varchar(255)', 'verkaufszahlen_chart', 'DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('bearbeiter', 'varchar(255)', 'verkaufszahlen_chart', 'DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('zeitstempel', 'timestamp', 'verkaufszahlen_chart','DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckColumn('sort','int(11)','verkaufszahlen_chart','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckTable('verkaufszahlen_chart_projekt');
    $this->app->erp->CheckColumn('id','int(11)','verkaufszahlen_chart_projekt','DEFAULT 0 NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('chart','int(11)','verkaufszahlen_chart_projekt','DEFAULT 0');
    $this->app->erp->CheckColumn('projekt','int(11)','verkaufszahlen_chart_projekt','DEFAULT 0');
    $this->app->erp->CheckColumn('aktiv', 'tinyint(1)', 'verkaufszahlen_chart_projekt', 'DEFAULT 1 NOT NULL');
    $this->app->erp->CheckIndex('verkaufszahlen_chart_projekt',['chart','projekt']);
    
    if(!$this->app->DB->Select('SELECT id FROM verkaufszahlen_chart WHERE aktiv = 1 LIMIT 1'))
    {
      $this->app->DB->Insert("INSERT INTO verkaufszahlen_chart (aktiv, bearbeiter) VALUES (1,'initial')");
    }
  }

  /**
   * @param $diagrammid
   * @param bool $onlyids Wenn false Liste mit id, bezeichnung und Farbe, sonst einfaches Array mit Projekt ids
   * @return array Aktive Projekte zum Diagramm
   */
  protected function GetDiagrammProjekte($diagrammid, $onlyids = false)
  {
    if($diagrammid > 0){
      $projekte_arr = $this->app->DB->SelectArr(
        "SELECT DISTINCT vcp.projekt, pr.abkuerzung, pr.farbe 
       FROM verkaufszahlen_chart_projekt vcp INNER JOIN projekt pr ON vcp.projekt = pr.id AND pr.geloescht = 0 AND vcp.aktiv = 1 
       WHERE vcp.chart = '" . $diagrammid . "' ORDER BY pr.abkuerzung"
      );
    }
    if($diagrammid <= 0 || empty($projekte_arr)){
        $projekte_arr = $this->app->DB->SelectArr(sprintf(
          "SELECT p.id as projekt, p.abkuerzung, p.farbe 
         FROM projekt AS p WHERE p.verkaufszahlendiagram = '1' AND p.geloescht != '1' %s
         ORDER BY p.abkuerzung",
          $this->app->erp->ProjektRechte('id')
        ));
    }

    if(!$onlyids)
    {
      return $projekte_arr;
    }
    if(empty($projekte_arr))
    {
      return array();
    }
    $ret = array();
    foreach($projekte_arr as $v)
    {
      $ret[] = $v['projekt'];
    }
    return $ret;
  }
  
  protected function DrawDiagramm($target, $daten){
    $this->app->Tpl->Set('DIAGRAMMWOCHE','');
    $this->app->Tpl->Set('DIAGRAMMMONAT','');
    $subwherea = null;

    // Ermitteln ob es Projekte gibt für die Diagramme gezeichnet werden sollen

    $projekte_arr = $this->GetDiagrammProjekte($daten['id']);
    if(!empty($projekte_arr))
    {
      $ertesdatum = $this->app->DB->Select("SELECT DATE_SUB(date_format(now(),'%Y-%m-01'),INTERVAL 12 MONTH)");
      foreach($projekte_arr as $k => $v)
      {
        $subwherea[] = $v['projekt'];
        $idtoabkuerzung[$v['projekt']] = $v['abkuerzung'];
      }

      // Daten basierend auf Rechnungen oder Aufträgen?
      $dataBasedOnInvoices = (bool)$daten['regs'];

      // Wochendiagramm zusammenbauen
      $weekChart = $this->BuildWeekChart($idtoabkuerzung, $dataBasedOnInvoices);
      $weekRenderer = new HtmlRenderer($weekChart, '', 400, 120);
      $this->app->Tpl->Set('DIAGRAMMWOCHE', $weekRenderer->render());

      // 12-Monats-Diagramm anzeigen?
      if($daten['monat']){
        $yearChart = $this->BuildFullYearChart($idtoabkuerzung, $ertesdatum, $dataBasedOnInvoices);
        $yearRenderer = new HtmlRenderer($yearChart, '', 400, 80);
        $this->app->Tpl->Set('DIAGRAMMMONAT', $yearRenderer->render());
      }

      // Diagramm-Titel
      if (empty($daten['bezeichnung'])){
        $chartTitle = $daten['regs'] ? 'Gesamtübersicht Rechnungen / Gutschriften' : 'Gesamtübersicht Auftragseingang';
      } else {
        $chartTitle = $daten['bezeichnung'];
      }
      $this->app->Tpl->Set('BESCHRIFTUNG1', $chartTitle);
    }

    //heute

    if($daten['regs'])
    {
      $this->app->Tpl->Set('BELEGTYP', 'Rechnungen / Gutschriften');
    }else{
      $this->app->Tpl->Set('BELEGTYP', 'Auftr&auml;ge');
    }
    
    if($subwherea)
    {
      $pakete = $this->getPackages(
        " v.versendet_am=DATE_FORMAT(NOW(),'%Y-%m-%d') 
        AND l.projekt in (".implode(', ', $subwherea).") ".$this->app->erp->ProjektRechte('l.projekt')
      );
      $this->app->Tpl->Set(
        'PAKETE',
        $pakete
        //$this->app->DB->Select("SELECT COUNT(v.id) FROM versand v INNER JOIN lieferschein l ON v.lieferschein = l.id WHERE v.versendet_am=DATE_FORMAT(NOW(),'%Y-%m-%d') AND l.projekt in (".implode(', ', $subwherea).") ".$this->app->erp->ProjektRechte('l.projekt')."")
      );
    }else{
      $pakete = $this->getPackages(
        " v.versendet_am=DATE_FORMAT(NOW(),'%Y-%m-%d') 
         ".$this->app->erp->ProjektRechte('l.projekt')
      );
      $this->app->Tpl->Set(
        'PAKETE',
        $pakete
        //$this->app->DB->Select("SELECT COUNT(v.id) FROM versand INNER JOIN lieferschein l ON v.lieferschein = l.id WHERE v.versendet_am=DATE_FORMAT(NOW(),'%Y-%m-%d') ".$this->app->erp->ProjektRechte('l.projekt')."")
      );
    }
    
    if($daten['regs'])
    {
      if($subwherea)
      {
        $data = $this->app->DB->SelectArr('SELECT SUM(umsatz_netto) as umsatz_netto2,'.'SUM(erloes_netto)'." as erloes_netto2 FROM `rechnung` WHERE datum=DATE_FORMAT(NOW(),'%Y-%m-%d') AND ( status='versendet' OR status='freigegeben' OR status ='storniert') AND projekt in (".implode(', ', $subwherea).') '.$this->app->erp->ProjektRechte('projekt'));
        $data2 = $this->app->DB->SelectArr('SELECT SUM(-umsatz_netto) as umsatz_netto2,'.'SUM(-erloes_netto)'." as erloes_netto2 FROM `gutschrift` WHERE datum=DATE_FORMAT(NOW(),'%Y-%m-%d') AND ( status='versendet' OR status='freigegeben') AND projekt in (".implode(', ', $subwherea).') '.$this->app->erp->ProjektRechte('projekt'));
        $data[0]['umsatz_netto2'] += (float)str_replace(',','',$data2[0]['umsatz_netto2']);
        $data[0]['erloes_netto2'] += (float)str_replace(',','',$data2[0]['erloes_netto2']);
      }else{
        $data = $this->app->DB->SelectArr('SELECT SUM(umsatz_netto) as umsatz_netto2,'.'SUM(erloes_netto)'." as erloes_netto2 FROM `rechnung` WHERE datum=DATE_FORMAT(NOW(),'%Y-%m-%d') AND ( status='versendet' OR status='freigegeben' OR status ='storniert')  ".$this->app->erp->ProjektRechte('projekt'));
        $data2 = $this->app->DB->SelectArr('SELECT SUM(-umsatz_netto) as umsatz_netto2,'.'SUM(-erloes_netto)'." as erloes_netto2 FROM `gutschrift` WHERE datum=DATE_FORMAT(NOW(),'%Y-%m-%d') AND ( status='versendet' OR status='freigegeben') ".$this->app->erp->ProjektRechte('projekt'));
        $data[0]['umsatz_netto2'] += (float)str_replace(',','',$data2[0]['umsatz_netto2']);
        $data[0]['erloes_netto2'] += (float)str_replace(',','',$data2[0]['erloes_netto2']);
      }
    }else{      
      if($subwherea)
      {
        $data = $this->app->DB->SelectArr('SELECT SUM(umsatz_netto) as umsatz_netto2, '.'SUM(erloes_netto)'." as erloes_netto2 FROM `auftrag` WHERE datum=DATE_FORMAT(NOW(),'%Y-%m-%d') AND ( status='abgeschlossen' OR status='freigegeben') AND projekt in (".implode(', ', $subwherea).') '.$this->app->erp->ProjektRechte('projekt'));
      }else{
        $data = $this->app->DB->SelectArr('SELECT SUM(umsatz_netto) as umsatz_netto2,'.'SUM(erloes_netto)'." as erloes_netto2 FROM `auftrag` WHERE datum=DATE_FORMAT(NOW(),'%Y-%m-%d') AND ( status='abgeschlossen' OR status='freigegeben') ".$this->app->erp->ProjektRechte('projekt'));
      }
    }

    $einnahmen_auftrag = str_replace(',','',$data[0]['umsatz_netto2']);
    $ausgaben_auftrag = str_replace(',','',$data[0]['umsatz_netto2']) - str_replace(',','',$data[0]['erloes_netto2']);

    $deckungsbeitrag = $einnahmen_auftrag - $ausgaben_auftrag;
    $deckungsbeitragprozent = $einnahmen_auftrag == 0?0:($deckungsbeitrag / $einnahmen_auftrag)*100;

    if($einnahmen_auftrag <=0) {
      $einnahmen_auftrag='0.00';
    }
    $this->app->Tpl->Set('UMSATZ',number_format($einnahmen_auftrag,2,',','.').' &euro;');
    //SELECT e.preis,ap.menge,ap.bezeichnung,ap.artikel FROM auftrag auf LEFT JOIN auftrag_position ap ON ap.auftrag=auf.id  RIGHT JOIN einkaufspreise e ON e.artikel=ap.artikel WHERE auf.datum=DATE_FORMAT(NOW(),'%Y-%m-%d') AND (auf.status='abgeschlossen' OR auf.status='freigegeben') AND (e.gueltig_bis > NOW() OR e.gueltig_bis!='0000-00-00') AND e.geloescht!=1 GROUP by e.artikel
    $this->app->Tpl->Set('DECKUNGSBEITRAG',number_format($deckungsbeitrag,2,',','.') );
    $this->app->Tpl->Set('DECKUNGSBEITRAGPROZENT',number_format($deckungsbeitragprozent,2,',','.'));	
    $this->app->Tpl->Parse('STATISTIKHEUTE','verkaufszahlen_statistik.tpl');

    //gestern
    if($subwherea)
    {
      $pakete = $this->getPackages(
        " v.versendet_am=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%Y-%m-%d') 
        AND l.projekt in (".implode(', ', $subwherea).") ".$this->app->erp->ProjektRechte('l.projekt')
      );
      $this->app->Tpl->Set(
        'PAKETE',
        $pakete
        //$this->app->DB->Select("SELECT COUNT(v.id) FROM versand v INNER JOIN lieferschein l ON v.lieferschein = l.id WHERE v.versendet_am=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%Y-%m-%d') AND l.projekt in (".implode(', ', $subwherea).") ".$this->app->erp->ProjektRechte('l.projekt')."")
      );
    }else{
      $pakete = $this->getPackages(
        " v.versendet_am=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%Y-%m-%d')
        AND l.projekt in (".implode(', ', $subwherea).") ".$this->app->erp->ProjektRechte('l.projekt')
      );
      $this->app->Tpl->Set(
        'PAKETE',
        $pakete
        //$this->app->DB->Select("SELECT COUNT(v.id) FROM versand v INNER JOIN lieferschein l ON v.lieferschein = l.id WHERE v.versendet_am=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%Y-%m-%d') ".$this->app->erp->ProjektRechte('l.projekt')."")
      );
    }
    
    if($daten['regs'])
    {
      if($subwherea)
      {
        $data = $this->app->DB->SelectArr('SELECT SUM(umsatz_netto) as umsatz_netto2,SUM(erloes_netto) as erloes_netto2 FROM `rechnung` WHERE datum=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),\'%Y-%m-%d\') AND ( status=\'versendet\' OR status=\'freigegeben\' OR status =\'storniert\') AND projekt in ('.implode(', ', $subwherea).') '.$this->app->erp->ProjektRechte('projekt'));
        $data2 = $this->app->DB->SelectArr('SELECT SUM(-umsatz_netto) as umsatz_netto2,SUM(-erloes_netto) as erloes_netto2 FROM `gutschrift` WHERE datum=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),\'%Y-%m-%d\') AND ( status=\'versendet\' OR status=\'freigegeben\') AND projekt in ('.implode(', ', $subwherea).') '.$this->app->erp->ProjektRechte('projekt'));
        $data[0]['umsatz_netto2'] += $data2[0]['umsatz_netto2'];
        $data[0]['erloes_netto2'] += $data2[0]['erloes_netto2'];
      }else{
        $data = $this->app->DB->SelectArr('SELECT SUM(umsatz_netto) as umsatz_netto2,SUM(erloes_netto) as erloes_netto2 FROM `rechnung` WHERE datum=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),\'%Y-%m-%d\') AND ( status=\'versendet\' OR status=\'freigegeben\' OR status =\'storniert\')  '.$this->app->erp->ProjektRechte('projekt'));
        $data2 = $this->app->DB->SelectArr('SELECT SUM(-umsatz_netto) as umsatz_netto2,SUM(-erloes_netto) as erloes_netto2 FROM `gutschrift` WHERE datum=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),\'%Y-%m-%d\') AND ( status=\'versendet\' OR status=\'freigegeben\') '.$this->app->erp->ProjektRechte('projekt'));
        $data[0]['umsatz_netto2'] += $data2[0]['umsatz_netto2'];
        $data[0]['erloes_netto2'] += $data2[0]['erloes_netto2'];
      }
    }else{     
      if($subwherea)
      {
        $data = $this->app->DB->SelectArr("SELECT 
          SUM(umsatz_netto) as umsatz_netto2,SUM(erloes_netto) as erloes_netto2 FROM `auftrag` WHERE datum=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%Y-%m-%d') AND ( status='abgeschlossen' OR status='freigegeben') AND projekt in (".implode(', ', $subwherea).") ".$this->app->erp->ProjektRechte('projekt'));
      }else{
        $data = $this->app->DB->SelectArr("SELECT 
          SUM(umsatz_netto) as umsatz_netto2,SUM(erloes_netto) as erloes_netto2 FROM `auftrag` WHERE datum=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%Y-%m-%d') AND ( status='abgeschlossen' OR status='freigegeben') ".$this->app->erp->ProjektRechte('projekt')."");
      }
    }

    // $umsatz = $data[0]['gesamtsumme2'];
    $einnahmen_auftrag = $data[0]['umsatz_netto2'];
    $ausgaben_auftrag = $data[0]['umsatz_netto2'] - $data[0]['erloes_netto2'];

    $deckungsbeitrag = $einnahmen_auftrag - $ausgaben_auftrag;
    $deckungsbeitragprozent = $einnahmen_auftrag == 0?0:($deckungsbeitrag / $einnahmen_auftrag)*100;

    if($einnahmen_auftrag <=0) {
      $einnahmen_auftrag='0.00';
    }
    $this->app->Tpl->Set('UMSATZ',number_format($einnahmen_auftrag,2,',','.').' &euro;');
    //SELECT e.preis,ap.menge,ap.bezeichnung,ap.artikel FROM auftrag auf LEFT JOIN auftrag_position ap ON ap.auftrag=auf.id  RIGHT JOIN einkaufspreise e ON e.artikel=ap.artikel WHERE auf.datum=DATE_FORMAT(NOW(),'%Y-%m-%d') AND (auf.status='abgeschlossen' OR auf.status='freigegeben') AND (e.gueltig_bis > NOW() OR e.gueltig_bis!='0000-00-00') AND e.geloescht!=1 GROUP by e.artikel
    $this->app->Tpl->Set('DECKUNGSBEITRAG',number_format($deckungsbeitrag,2,',','.') );
    $this->app->Tpl->Set('DECKUNGSBEITRAGPROZENT',number_format($deckungsbeitragprozent,2,',','.'));
    $this->app->Tpl->Parse('STATISTIKGESTERN','verkaufszahlen_statistik.tpl');

    $subwhereSql = $this->app->erp->ProjektRechte('a.projekt');
    if(!empty($subwherea)) {
      $subwhereSql .=  sprintf(' AND a.projekt IN (%s) ', implode(', ', $subwherea));
    }
    list($imversand, $offen) = $this->getVersandStats($subwhereSql);

    $this->app->Tpl->Set('IMVERSAND',number_format($imversand,0,',','.') );
    $this->app->Tpl->Set('AUFTRAEGEOFFEN',number_format($offen,0,',','.'));
    $this->app->Tpl->Parse('STATISTIKAUFTRAEGE','verkaufszahlen_statistik2.tpl');
    $this->app->Tpl->Set('ID', $daten['id']);
    $this->app->Tpl->Parse($target, 'verkaufszahlen_listdetail.tpl');
  }

  /**
   * @param string $subwhere
   * @param string $joinInvoice
   * @param string $joinCreditnote
   *
   * @return float[]
   */
  public function getInvoiceStats($subwhere, $joinInvoice = '', $joinCreditnote = '')
  {
    $data = $this->app->DB->SelectRow(
      'SELECT SUM(r.umsatz_netto) as umsatz_netto2,SUM(r.erloes_netto) as erloes_netto2 
      FROM `rechnung` AS `r`
      $joinInvoice
      WHERE  ( r.status=\'versendet\' OR r.status=\'freigegeben\' OR r.status =\'storniert\') 
                  '.$subwhere
    );
    $data2 = $this->app->DB->SelectRow(
      'SELECT SUM(-r.umsatz_netto) as umsatz_netto2,SUM(-r.erloes_netto) as erloes_netto2 
      FROM `gutschrift` AS `r`
      $joinCreditnote
      WHERE  
      AND ( r.status=\'versendet\' OR r.status=\'freigegeben\') '
      .$subwhere
    );
    $data['umsatz_netto2'] += $data2['umsatz_netto2'];
    $data['erloes_netto2'] += $data2['erloes_netto2'];

    $einnahmen_auftrag = $data['umsatz_netto2'];
    $ausgaben_auftrag = $data['umsatz_netto2'] - $data['erloes_netto2'];

    $deckungsbeitrag = $einnahmen_auftrag - $ausgaben_auftrag;
    $deckungsbeitragprozent = $einnahmen_auftrag == 0?0:($deckungsbeitrag / $einnahmen_auftrag)*100;

    if($einnahmen_auftrag <=0) {
      $einnahmen_auftrag=0;
    }

    return [(float)$einnahmen_auftrag, (float)$deckungsbeitrag, (float)$deckungsbeitragprozent];
  }

  /**
   * @param string $subwhere
   * @param string $join
   *
   * @return int
   */
  public function getPackages($subwhere, $join = '')
  {
    return (int)$this->app->DB->Select(
      "SELECT COUNT(v.id) 
      FROM versand v 
      INNER JOIN lieferschein l ON v.lieferschein = l.id
      $join
      WHERE ".$subwhere
    );
  }

  /**
   * @param string $subwhere
   *
   * @return float[]
   */
  public function getOrderStats($subwhere)
  {
    $data = $this->app->DB->SelectRow(
      "SELECT 
          SUM(umsatz_netto) as umsatz_netto2,SUM(erloes_netto) as erloes_netto2 
      FROM `auftrag` 
      WHERE ( status='abgeschlossen' OR status='freigegeben') 
          ".$subwhere
      );
    $einnahmen_auftrag = $data['umsatz_netto2'];
    $ausgaben_auftrag = $data['umsatz_netto2'] - $data['erloes_netto2'];

    $deckungsbeitrag = $einnahmen_auftrag - $ausgaben_auftrag;
    $deckungsbeitragprozent = $einnahmen_auftrag == 0?0:($deckungsbeitrag / $einnahmen_auftrag)*100;

    if($einnahmen_auftrag <=0) {
      $einnahmen_auftrag=0;
    }

    return [(float)$einnahmen_auftrag, (float)$deckungsbeitrag, (float)$deckungsbeitragprozent];
  }

  /**
   * @param string $subwhere
   *
   * @return int[]
   */
  public function getVersandStats($subwhere) {

    $imversand = $this->app->DB->Select(
      "SELECT count(a.id) 
      FROM auftrag a 
      INNER JOIN lieferschein l ON a.id = l.auftragid 
      LEFT JOIN versand v ON l.id = v.lieferschein 
      WHERE v.abgeschlossen!='1' AND (tracking <> '' OR weitererlieferschein <> 1) 
        AND v.cronjob = 0 ".$subwhere
    );
    $offen = $this->app->DB->Select(
      "SELECT count(a.id) 
      FROM auftrag a 
      WHERE  a.status='freigegeben' ".$subwhere
    );
    return [
      (int)$imversand, (int)$offen
    ];
  }

  public function VerkaufszahlenList()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'geteasytablelist') {
      $element = $this->app->Secure->GetPOST('element');
      $html = $this->getEasyTable($element);
      echo json_encode(['element'=>$element,'html'=>$html]);
      $this->app->ExitXentral();
    }
    if($cmd === 'getchart')
    {
      $el = (int)$this->app->Secure->GetPOST('el');
      $arr = $this->app->DB->SelectRow("SELECT * FROM verkaufszahlen_chart WHERE id = '$el' LIMIT 1");
      $projekte = null;
      $ret = array('bezeichnung'=>'','regs'=>0,'monat'=>1);
      if($arr) {
        $ret = array('bezeichnung'=>$arr['bezeichnung'],'regs'=>$arr['regs'],'monat'=>$arr['monat']);
        if($this->app->DB->Select("SELECT p.id FROM projekt p INNER JOIN verkaufszahlen_chart_projekt vcp ON p.id = vcp.projekt AND vcp.chart = '$el' WHERE p.geloescht <> 1 LIMIT 1")){
          $projekte = $this->app->DB->SelectArr("SELECT p.id, vcp.aktiv FROM projekt p LEFT JOIN verkaufszahlen_chart_projekt vcp ON p.id = vcp.projekt AND vcp.chart = '$el' WHERE p.geloescht <> 1 ");
        }
      }
      if(!$projekte)
      {
        $projekte = $this->app->DB->SelectArr('SELECT p.id, p.verkaufszahlendiagram as aktiv FROM projekt p WHERE p.geloescht <> 1 ');
      }
      if($projekte)
      {
        foreach($projekte as $k => $v){
          $ret['projekte'][(int)$v['id']] = (int)$v['aktiv'];
        }
      }
      $ret['id'] = $el;
      echo json_encode($ret);
      $this->app->ExitXentral();
    }
    
    if($cmd === 'savechart') 
    {
      $ret = array('status'=>1);
      $el = (int)$this->app->Secure->GetPOST('sid');
      if(!$el)
      {
        $this->app->DB->Insert('INSERT INTO verkaufszahlen_chart (aktiv) VALUES (1)');
        $el = $this->app->DB->GetInsertID();
      }
      $this->app->DB->Update("UPDATE verkaufszahlen_chart SET bezeichnung = '".$this->app->Secure->GetPOST('bezeichnung')."', bearbeiter = '".$this->app->DB->real_escape_string($this->app->User->GetName())."', monat = '".(int)$this->app->Secure->GetPOST('monat')."', regs = '".(int)$this->app->Secure->GetPOST('regs')."' WHERE id = '$el' LIMIT 1");
      $projekte = $this->app->DB->Select('SELECT p.id FROM projekt p WHERE p.geloescht <> 1 ');
      foreach($projekte as $pr)
      {
        $check = $this->app->DB->Select("SELECT id FROM verkaufszahlen_chart_projekt WHERE chart = '$el' AND projekt = '".$pr['id']."' LIMIT 1");
        $projekt = $this->app->Secure->GetPOST('projekt');
        if($check)
        {
          if($projekt && is_array($projekt) && in_array($pr['id'],$projekt))
          {
            $this->app->DB->Update("UPDATE verkaufszahlen_chart_projekt SET aktiv = 1 WHERE id = '$check' LIMIT 1");
          }else{
            $this->app->DB->Update("UPDATE verkaufszahlen_chart_projekt SET aktiv = 0 WHERE id = '$check' LIMIT 1");
          }
        }elseif($projekt && is_array($projekt) && in_array($pr['id'],$projekt)){
          $this->app->DB->Insert("INSERT INTO verkaufszahlen_chart_projekt (chart, projekt, aktiv) VALUES ('$el','".$pr['id']."','1')");
        }
      }
      echo json_encode($ret);
      $this->app->ExitXentral();
    }
    
    if($cmd === 'deletechart') 
    {
      $ret = array('status'=>1);
      $el = (int)$this->app->Secure->GetPOST('sid');
      if($el)
      {
        $this->app->DB->Delete("DELETE FROM verkaufszahlen_chart_projekt WHERE chart = '$el'");
        $this->app->DB->Delete("DELETE FROM verkaufszahlen_chart WHERE id = '$el'");
      }
      echo json_encode($ret);
      $this->app->ExitXentral();
    }
    
    if(!$this->app->DB->Select('SELECT id FROM verkaufszahlen_chart WHERE aktiv = 1 LIMIT 1'))
    {
      $this->Install();
    }
    $useJson = $this->useJson;
    $this->VerkaufszahlenMenu();

    
    $diagramme = $this->app->DB->SelectArr('SELECT * FROM verkaufszahlen_chart WHERE aktiv = 1 ORDER BY sort');

    if(!empty($diagramme))
    {
      $projektefirst = $this->GetDiagrammProjekte($diagramme[0]['id'],true);
      if(count($diagramme) <= 1)
      {
        $this->app->Tpl->Set('VORDEAKTIVIEREN','/*');
        $this->app->Tpl->Set('NACHDEAKTIVIEREN','*/');
      }
      foreach($diagramme as $diagramm)
      {
        $this->DrawDiagramm('DIAGRAMME', $diagramm);
      }
    }else{
      $projektefirst = array();
    }
    
    
    //$this->app->Tpl->Set('TABTEXT',"Verkaufszahlen");

    $projekte_arr = $this->app->DB->SelectArr("SELECT id as projekt FROM projekt WHERE verkaufszahlendiagram='1' AND geloescht!='1'  ".$this->app->erp->ProjektRechte('id').' order by abkuerzung');
    if(!empty($projekte_arr)){
      $cprojekte_arr = count($projekte_arr);
      for ($i = 0; $i < $cprojekte_arr; $i++) {
        $projekte[] = $projekte_arr[$i]['projekt'];

        $abkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='" . $projekte_arr[$i]['projekt'] . "' LIMIT 1");
        $farbe = $this->app->DB->Select("SELECT farbe FROM projekt WHERE id='" . $projekte_arr[$i]['projekt'] . "' LIMIT 1");

        if($farbe == '') {
          $farbe = '#eee';
        }

        if($i < count($projekte_arr) - 1){
          $this->app->Tpl->Add('PLOTLEGENDS', '{ label: "' . $abkuerzung . '",  data: d' . ($i + 1) . ', color: "' . $farbe . '"},');
        }
        else{
          $this->app->Tpl->Add('PLOTLEGENDS', '{ label: "' . $abkuerzung . '",  data: d' . ($i + 1) . ', color: "' . $farbe . '"}');
        }
      }
    }

    $betraege = $this->app->DB->SelectArr("SELECT date_format(a.datum,'%Y-%m-%d') as datum,p.id, CEILING(SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1)))) as betrag
            FROM auftrag_position ap LEFT JOIN auftrag a ON ap.auftrag=a.id 
            LEFT JOIN projekt p ON p.id=a.projekt WHERE a.status!='storniert' AND a.belegnr!='0' AND a.datum > date_sub(now(),INTERVAL 13 DAY)  group by p.id,a.datum order by a.datum ,p.id");
            

      
    $spalte = 0;
    $daten = '';
    $projekte_daten = array();
    for ($zaehler = -12; $zaehler <= 0; $zaehler++) {
      $daten .=  "[$spalte,'".date('D', strtotime('+' . $zaehler . ' day')).'<br>'.date('d.m', strtotime("+" . $zaehler . " day"))."']";

      $tagdatum =  date('Y-m-d', strtotime('+' . $zaehler . ' day'));


      foreach($projekte as $value)
      {
        $betrag = '';
        if(!empty($betraege)){
          foreach($betraege as $betr)
          {
            if($betr['id'] == $value && $betr['datum'] == $tagdatum){
              $betrag = $betr['betrag'];
            }
          }
        }
        
        if($betrag!=''){
          $betrag = "'".$betrag."'";
        }
        else {
          $betrag='0.0';
        }

        $projekte_daten[$value] .= "[$spalte,$betrag]";
      }
      if($zaehler<0)
      {
        $daten .= ',';
        foreach($projekte as $value) {
          $projekte_daten[$value] .= ',';
        }
      }
      $spalte++;
    } 
    
    

    $this->app->Tpl->Set('DATUM',"[$daten]");

    $nummer = 1;
    $var = '';
    if(!empty($projekte)){
      foreach ($projekte as $value) {
        $var .= 'var d'.$nummer.' = [' . $projekte_daten[$value] . '];';
        $nummer++;
      }
    }
    $this->app->Tpl->Set('VARIABLEN',$var);

    //Auftragseingang leztzten 2 Wochen
    if(!$useJson){
      $table = new EasyTable($this->app);
      $arr = $this->getDetailQueries('TAGESUEBERSICHT');
      $table->Query($arr);
      if(!empty($table->datasets)){
        $pakete = $this->getDetailQueries('TAGESUEBERSICHTPAKETE');
        if($pakete){
          foreach ($pakete as $paket) {
            if($paket['pakete']){
              foreach ($table->datasets as $k => $d) {
                if($d['datum'] == $paket['datum']){
                  $table->datasets[$k]['pakete'] = $paket['pakete'];
                }
              }
            }
          }
        }
      }
      $table->DisplayNew('TAGESUEBERSICHT', 'Pakete', 'noAction');
    }
    /* extend */

    $summe = $this->app->DB->Select("SELECT SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1))) FROM auftrag_position ap INNER JOIN auftrag a ON ap.auftrag=a.id WHERE (a.status!='storniert' and a.status!='angelegt') ".$this->app->erp->ProjektRechte('a.projekt')."");
    $summe_gs = $this->app->DB->Select("SELECT SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1))) FROM gutschrift_position ap INNER JOIN gutschrift a ON ap.gutschrift=a.id WHERE (a.status!='storniert' and a.status!='angelegt') ".$this->app->erp->ProjektRechte('a.projekt').""); 

    $summe30 = $this->app->DB->Select("SELECT SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1))) FROM auftrag_position ap INNER JOIN auftrag a ON ap.auftrag=a.id 
        WHERE a.datum > date_add(NOW(), interval -30 day) AND (a.status!='storniert' and a.status!='angelegt') ".$this->app->erp->ProjektRechte('a.projekt')."");
    $summe30_gs = $this->app->DB->Select("SELECT SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1))) FROM gutschrift_position ap INNER JOIN gutschrift a ON ap.gutschrift=a.id 
        WHERE a.datum > date_add(NOW(), interval -30 day) AND (a.status!='storniert' and a.status!='angelegt') ".$this->app->erp->ProjektRechte('a.projekt')."");

    $summemenge = count($this->app->DB->SelectArr("SELECT 
          COUNT(a.datum) FROM auftrag_position ap INNER JOIN auftrag a ON ap.auftrag=a.id WHERE (a.status!='storniert' and a.status!='angelegt') ".$this->app->erp->ProjektRechte('a.projekt')."
          GROUP by a.datum, a.projekt "));

    if($summemenge < 30)
    {
      $summe_gutschriften = $summe_gs;
      $summe_auftrag = $summe;
      $durchschnitt = ($summe-$summe_gs) / $summemenge; 
      $summe= number_format(($summe-$summe_gs),2);
      $tage = $summemenge;
    } else {
      $summe_gutschriften = $summe30_gs;
      $summe_auftrag = $summe30;
      $durchschnitt = ($summe30-$summe30_gs) / 30;  // wenn mehr als 30 tage
      $summe= number_format(($summe30-$summe30_gs),2);
      $tage = 30;
    }

    $summe_gutschriften = number_format($summe_gutschriften,2);
    $summe_auftrag = number_format($summe_auftrag,2);

    $durchschnitt = number_format($durchschnitt,2);
    $this->app->Tpl->Set('EXTEND',"Summe: $summe_auftrag &euro; (abzgl. Gutschriften $summe_gutschriften &euro; = pro Tag $durchschnitt seit $tage Tagen)");


    /* tages uebersicht detail */

    $table = new EasyTable($this->app);
    $table->Query("SELECT 
        DATE_FORMAT(a.datum,'%d.%m.%Y') as datum,p.abkuerzung as projekt, ".$this->app->erp->FormatPreis("SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1)))")." as Auftragseingang, COUNT(ap.id) as positionen, 
        CONCAT('<a href=\"index.php?module=verkaufszahlen&action=details&frame=false&id=',DATE_FORMAT(a.datum,'%Y-%m-%d'),'-',a.projekt,'\" onclick=\"makeRequest(this); return false;\">Details</a>') as id FROM auftrag_position ap INNER JOIN auftrag a ON ap.auftrag=a.id 
        LEFT JOIN projekt p ON p.id=a.projekt WHERE a.status!='storniert' ".$this->app->erp->ProjektRechte('a.projekt')." GROUP by a.datum DESC, a.projekt LIMIT 14");
    $table->DisplayNew('TAGESUEBERSICHTDETAIL','');

    // top artikel
    if(!$useJson) {
      $table = new EasyTable($this->app);
      $arr = $this->getDetailQueries('TOPARTIKEL');
      $table->Query($arr);
      $table->DisplayNew('TOPARTIKEL', "Bezeichnung", "noAction");
    }

    //heute

    $this->app->Tpl->Set('PAKETE',$this->app->DB->Select("SELECT COUNT(v.id) FROM versand v INNER JOIN lieferschein l ON v.lieferschein = l.id WHERE v.versendet_am=DATE_FORMAT(NOW(),'%Y-%m-%d') ".$this->app->erp->ProjektRechte('l.projekt').""));
    $data = $this->app->DB->SelectArr("SELECT ifnull(SUM(umsatz_netto),0) as umsatz_netto2,ifnull(SUM(erloes_netto),0) as erloes_netto2 FROM `auftrag` 
      WHERE datum=DATE_FORMAT(NOW(),'%Y-%m-%d') AND ( status='abgeschlossen' OR status='freigegeben') ".
      $this->app->erp->ProjektRechte('projekt').
      (!empty($projektefirst)? ' AND projekt in ('.implode(', ', $projektefirst).')' :''));
    $einnahmen_auftrag = 0;
    $ausgaben_auftrag = 0;
    $deckungsbeitrag = 0;
    $deckungsbeitragprozent = 0;
    if(!empty($data)) {
      $einnahmen_auftrag = $data[0]['umsatz_netto2'];
      $ausgaben_auftrag = $data[0]['umsatz_netto2'] - $data[0]['erloes_netto2'];

      $deckungsbeitrag = $einnahmen_auftrag - $ausgaben_auftrag;
      $deckungsbeitragprozent = $einnahmen_auftrag != 0? ($deckungsbeitrag / $einnahmen_auftrag) * 100:0;
    }
    if($einnahmen_auftrag <=0) {
      $einnahmen_auftrag='0.00';
    }
    $this->app->Tpl->Set('UMSATZ',number_format($einnahmen_auftrag,2,',','.').' &euro;');
    $this->app->Tpl->Set('DECKUNGSBEITRAG',number_format($deckungsbeitrag,2,',','.') );	
    $this->app->Tpl->Set('DECKUNGSBEITRAGPROZENT',number_format($deckungsbeitragprozent,2,',','.'));	
    $this->app->Tpl->Parse('STATISTIKHEUTE','verkaufszahlen_statistik.tpl');

    //gestern

    $this->app->Tpl->Set('PAKETE',$this->app->DB->Select("SELECT COUNT(v.id) FROM versand v INNER JOIN lieferschein l ON v.lieferschein = l.id WHERE v.versendet_am=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%Y-%m-%d') ".$this->app->erp->ProjektRechte('l.projekt').""));

    $data = $this->app->DB->SelectArr("SELECT 
        ifnull(SUM(umsatz_netto),0) as umsatz_netto2,ifnull(SUM(erloes_netto),0) as erloes_netto2 FROM `auftrag` 
        WHERE datum=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%Y-%m-%d') AND ( status='abgeschlossen' OR status='freigegeben') ".
      $this->app->erp->ProjektRechte('projekt').
      (!empty($projektefirst)? ' AND projekt in ('.implode(', ', $projektefirst).')' :''));

    $einnahmen_auftrag = 0;
    $ausgaben_auftrag = 0;
    $deckungsbeitrag = 0;
    $deckungsbeitragprozent = 0;
    if(!empty($data)){
      $einnahmen_auftrag = $data[0]['umsatz_netto2'];
      $ausgaben_auftrag = $data[0]['umsatz_netto2'] - $data[0]['erloes_netto2'];

      $deckungsbeitrag = $einnahmen_auftrag - $ausgaben_auftrag;
      $deckungsbeitragprozent = $einnahmen_auftrag!=0? ($deckungsbeitrag / $einnahmen_auftrag) * 100:0;
    }
    if($einnahmen_auftrag <=0) {
      $einnahmen_auftrag='0.00';
    }
    $this->app->Tpl->Set('UMSATZ',number_format($einnahmen_auftrag,2,',','.').' &euro;');
    $this->app->Tpl->Set('DECKUNGSBEITRAG',number_format($deckungsbeitrag,2,',','.') );
    $this->app->Tpl->Set('DECKUNGSBEITRAGPROZENT',number_format($deckungsbeitragprozent,2,',','.'));
    $this->app->Tpl->Parse('STATISTIKGESTERN','verkaufszahlen_statistik.tpl');

    $projekte = $this->app->DB->SelectArr("SELECT id, abkuerzung FROM projekt WHERE geloescht = 0 ".$this->app->erp->ProjektRechte('id')." ORDER BY abkuerzung");
    if(!empty($projekte)) {
      foreach($projekte as $projekt) {
        $this->app->Tpl->Add(
          'POPUPPROJEKTE',
          '<tr>
            <td colspan="2">
              <input type="checkbox" value="'.$projekt['id'].'" name="projekt[]" id="projekt_'.
              $projekt['id'].'" />&nbsp;'.
              $projekt['abkuerzung'].'
            </td>
          </tr>'
        );
      }
    }
    $this->app->Tpl->Parse('TAB1','verkaufszahlen_list.tpl');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  /**
   * @param string $type
   * @param int    $seconds
   *
   * @return array|null
   */
  public function getDetailQueries($type, $seconds = 3600)
  {
    switch(strtoupper($type)) {
      case 'TAGESUEBERSICHTPAKETE':
        return $this->app->DB->SelectArrCache("SELECT DATE_FORMAT(v.versendet_am,'%d.%m.%Y') as datum,
            count(v.id) as pakete 
          from versand v 
          INNER JOIN lieferschein l ON v.lieferschein = l.id 
          WHERE 1 ".$this->app->erp->ProjektRechte('l.projekt').'  group by v.versendet_am',
          $seconds, 'verkaufszahlen'
        );
        break;
      case 'TAGESUEBERSICHT':
        return $this->app->DB->SelectArrCache("SELECT 
          DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, 
          ".$this->app->erp->FormatPreis('SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1)))',2)." as Auftragseingang , 
          '' as pakete
          FROM auftrag_position ap 
          INNER JOIN auftrag a ON ap.auftrag=a.id 
          WHERE (a.status='freigegeben' OR a.status='abgeschlossen') AND (a.belegnr!='0' OR a.belegnr!='') ".$this->app->erp->ProjektRechte('a.projekt')."  GROUP by a.datum DESC LIMIT 30",
          $seconds, 'verkaufszahlen'
        );
        break;
      case 'TAGESUEBERSICHTDETAIL':
        return $this->app->DB->SelectArrCache(
          "SELECT 
          DATE_FORMAT(a.datum,'%d.%m.%Y') as datum,p.abkuerzung as projekt, ".$this->app->erp->FormatPreis("SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1)))", 2)." as 'Auftragseingang Summe', COUNT(ap.id) as positionen, 
          CONCAT('<a href=\"index.php?module=verkaufszahlen&action=details&frame=false&id=',DATE_FORMAT(a.datum,'%Y-%m-%d'),'-',a.projekt,'\" onclick=\"makeRequest(this); return false;\">Details</a>') as id FROM auftrag_position ap LEFT JOIN auftrag a ON ap.auftrag=a.id 
          LEFT JOIN projekt p ON p.id=a.projekt WHERE a.status!='storniert' ".$this->app->erp->ProjektRechte('a.projekt')." GROUP by a.datum DESC, a.projekt LIMIT 14",
          $seconds, 'verkaufszahlen'
        );
      break;
      case 'TAGESUEBERSICHTDETAILGESTERN':
        return $this->app->DB->SelectArrCache(
          "SELECT                                                                                                                                     
        DATE_FORMAT(a.datum,'%d.%m.%Y') as datum,p.abkuerzung as projekt, ".$this->app->erp->FormatPreis("SUM(ap.preis*ap.menge*(IF(ap.rabatt > 0, (100-ap.rabatt)/100, 1)))")." as 'Auftragseingang Summe', COUNT(ap.id) as positionen,                         
        CONCAT('<a href=\"index.php?module=verkaufszahlen&action=details&frame=false&id=',DATE_FORMAT(a.datum,'%Y-%m-%d'),'-',a.projekt,'\" onclick=\"makeRequest(this); return false;\">Details</a>') as id FROM auftrag_position ap LEFT JOIN auftrag a ON ap.auftrag=a.id 

          LEFT JOIN projekt p ON p.id=a.projekt WHERE a.status!='storniert' ".$this->app->erp->ProjektRechte('a.projekt')." GROUP by a.datum DESC, a.projekt LIMIT 14",
          $seconds, 'verkaufszahlen'
        );
        break;
      case 'JAHR':
        return $this->app->DB->SelectArrCache(
          "
      SELECT EXTRACT(MONTH FROM a.datum) as monat, EXTRACT(YEAR FROM a.datum) as jahr, 
      " . $this->app->erp->FormatPreis("(SELECT SUM(auf.gesamtsumme) FROM auftrag auf
       WHERE EXTRACT(MONTH FROM auf.datum)=monat AND EXTRACT(YEAR FROM auf.datum)=jahr AND auf.status!='storniert' AND auf.status!='angelegt'  " . $this->app->erp->ProjektRechte('auf.projekt') . " )") . " as auftraege, 

      " . $this->app->erp->FormatPreis("SUM(a.soll)") . " as rechnungen, 
      " . $this->app->erp->FormatPreis("(SELECT SUM(g.soll) FROM gutschrift g
       WHERE EXTRACT(MONTH FROM g.datum)=monat AND EXTRACT(YEAR FROM g.datum)=jahr AND g.status!='storniert' AND g.status!='angelegt'  " . $this->app->erp->ProjektRechte('g.projekt') . " )") . " as gutschriften, 

      " . $this->app->erp->FormatPreis("(SUM(a.soll) - IFNULL((SELECT SUM(g.soll) FROM gutschrift g
                             WHERE EXTRACT(MONTH FROM g.datum)=monat AND EXTRACT(YEAR FROM g.datum)=jahr AND g.status!='storniert' AND g.status!='angelegt'),0)  " . $this->app->erp->ProjektRechte('g.projekt') . " )") . " as umsatz

      FROM rechnung a WHERE a.status!='angelegt' " . $this->app->erp->ProjektRechte('a.projekt') . "
      GROUP By monat,jahr ORDER by jahr DESC, monat DESC LIMIT 12",
          $seconds, 'verkaufszahlen'
        );
        break;
      case 'JAHRESUEBERSICHTPROJEKTE':
        return $this->app->DB->SelectArrCache(
          '       
        SELECT '.$this->app->erp->FormatPreis('SUM(a.soll)').' as rechnungen,
        '.$this->app->erp->FormatPreis("IFNULL((SELECT SUM(g.soll) FROM gutschrift g WHERE g.status!='storniert' AND g.status!='angelegt' AND EXTRACT(YEAR FROM g.datum)=EXTRACT(YEAR FROM NOW()) AND g.projekt=p.id  ".$this->app->erp->ProjektRechte('g.projekt')."),0)")." as gutschriften,
        ".$this->app->erp->FormatPreis("SUM(a.soll)-IFNULL((SELECT SUM(g.soll) FROM gutschrift g WHERE g.status!='storniert' AND g.status!='angelegt' AND EXTRACT(YEAR FROM g.datum)=EXTRACT(YEAR FROM NOW()) AND g.projekt=p.id  ".$this->app->erp->ProjektRechte('g.projekt')." ),0)")." as umsatz,
        p.abkuerzung as projekt,COUNT(a.id) as anzahl_rechnungen FROM rechnung a LEFT JOIN projekt p ON p.id=a.projekt  
        WHERE a.status!='angelegt' AND a.status!='storniert' AND EXTRACT(YEAR FROM a.datum)=EXTRACT(YEAR FROM NOW())  ".$this->app->erp->ProjektRechte('a.projekt')."
        GROUP By projekt ORDER by umsatz",
          $seconds, 'verkaufszahlen'
        );
        break;
      case 'GUTSCHRIFTJAHR':
        return $this->app->DB->SelectArrCache(
          "
        SELECT EXTRACT(MONTH FROM a.datum) as month, EXTRACT(YEAR FROM a.datum) as year, FORMAT(SUM(a.soll),2) 
        FROM gutschrift a 
        WHERE (a.status!='storniert' AND a.status!='angelegt') ".$this->app->erp->ProjektRechte('a.projekt')."
        GROUP By month,year ORDER by year DESC, month DESC",
          $seconds, 'verkaufszahlen'
        );
        break;
      case 'ANGEBOTJAHR':
        return $this->app->DB->SelectArrCache(
          "
        SELECT EXTRACT(MONTH FROM a.datum) as month, EXTRACT(YEAR FROM a.datum) as year, FORMAT(SUM(ap.preis*ap.menge),2) 
        FROM angebot a LEFT JOIN angebot_position ap ON a.id=ap.angebot 
        WHERE (a.status!='storniert' AND a.status!='angelegt') ".$this->app->erp->ProjektRechte('a.projekt')."
        GROUP By month,year ORDER by year DESC, month DESC",
          $seconds, 'verkaufszahlen'
        );
        break;
      case 'TOPARTIKEL':
        return $this->app->DB->SelectArrCache(
          "SELECT trim(SUM(ap.menge))+0 as menge,ap.nummer, ap.bezeichnung FROM auftrag_position ap LEFT JOIN artikel a ON a.id=ap.artikel INNER JOIN
        auftrag auf ON ap.auftrag=auf.id WHERE auf.datum >= DATE_SUB(NOW(),INTERVAL 90 day) AND a.lagerartikel=1 ".$this->app->erp->ProjektRechte('auf.projekt')." GROUP BY ap.artikel ORDER by 1 DESC LIMIT 30",
          $seconds, 'verkaufszahlen'
        );
        break;
    }

    return null;
  }

  /**
   * @param string $element
   *
   * @return string
   */
  public function getEasyTable($element)
  {
    if(empty($element)) {
      return '';
    }
    switch(strtoupper($element)) {
      case 'TAGESUEBERSICHT':
        $table = new EasyTable($this->app);
        $arr = $this->getDetailQueries('TAGESUEBERSICHT');
        $table->Query($arr);
        if(!empty($table->datasets)){
          $pakete = $this->getDetailQueries('TAGESUEBERSICHTPAKETE');
          if($pakete){
            foreach ($pakete as $paket) {
              if($paket['pakete']){
                foreach ($table->datasets as $k => $d) {
                  if($d['datum'] == $paket['datum']){
                    $table->datasets[$k]['pakete'] = $paket['pakete'];
                  }
                }
              }
            }
          }
        }
        return $table->DisplayNew('return', 'Pakete', 'noAction');
        break;
      case 'TAGESUEBERSICHTDETAILGESTERN':
      case 'TAGESUEBERSICHTDETAIL':
        $lastCol = 'Umsatz';
        break;
      case 'JAHRESUEBERSICHTPROJEKTE':
        $lastCol = 'Anzahl Rechnungen';
        break;
      case 'TOPARTIKEL':
        $lastCol = 'Bezeichnung';
        break;
      case 'ANGEBOTJAHR':
      case 'GUTSCHRIFTJAHR':
      case 'JAHR':
        $lastCol = 'Jahr';
        break;
      default:
        $lastCol = 'Umsatz';
        break;
    }
    if(empty($lastCol)) {
      return '';
    }
    $table = new EasyTable($this->app);
    $table->Query($this->getDetailQueries($element));
    return $table->DisplayNew('return', strtoupper($element),$lastCol,'noAction');
  }

  public function VerkaufszahlenDetails()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'getdetails') {
      $element = $this->app->Secure->GetPOST('element');
      $html = $this->getEasyTable($element);
      echo json_encode(['element'=>$element,'html'=>$html]);
      $this->app->ExitXentral();
    }
    $this->VerkaufszahlenMenu();
    $useJson = $this->useJson;

    if(!$useJson){
      /* tages uebersicht detail */
      $table = new EasyTable($this->app);
      $arr = $this->getDetailQueries('TAGESUEBERSICHTDETAIL');
      $table->Query($arr);
      $table->DisplayNew('TAGESUEBERSICHTDETAIL', '');
    }

    /* tages uebersicht detail */
    /*
    $table = new EasyTable($this->app);
    $arr = $this->getDetailQueries('TAGESUEBERSICHTDETAILGESTERN');
    $table->Query($arr);
    $table->DisplayNew('TAGESUEBERSICHTDETAILGESTERN','');
    $table = new EasyTable($this->app);
    $table->Query('SELECT SUM(ap.menge) menge,ap.bezeichnung FROM auftrag_position ap INNER JOIN auftrag auf ON ap.auftrag = auf.id LEFT JOIN artikel a ON a.id=ap.artikel WHERE 1  '.$this->app->erp->ProjektRechte('auf.projekt').' GROUP BY ap.artikel ORDER by 1 DESC LIMIT 14');
    //$table->Query("SELECT SUM(ap.menge) menge,ap.bezeichnung FROM auftrag_position ap LEFT JOIN artikel a ON a.id=ap.artikel WHERE a.lagerartikel=1 GROUP BY ap.artikel ORDER by 1 DESC LIMIT 14");
    $table->DisplayNew('TOPARTIKEL','Umsatz','noAction');
    */
    /* umsatz gesamt */
    if(!$useJson) {
      $table = new EasyTable($this->app);
      $arr = $this->getDetailQueries('JAHR');
      $table->Query($arr);
      $table->DisplayNew('JAHR', "Umsatz", "noAction");
    }

    // Umsatzanteile aktuelles Jahr (Rechnungen und Gutschriften)
    $projektesummen = $this->app->DB->SelectArr(
      "SELECT 
          SUM(r.soll) - IFNULL((SELECT SUM(g.soll) FROM gutschrift AS g WHERE g.status != 'storniert' AND g.status != 'angelegt' AND EXTRACT(YEAR FROM g.datum)=EXTRACT(YEAR FROM NOW()) AND g.projekt = p.id),0) AS summe, 
          IFNULL(p.abkuerzung, 'Ohne Projekt') AS projekt, 
          p.farbe AS farbe 
        FROM rechnung AS r LEFT JOIN projekt AS p ON p.id = r.projekt 
        WHERE r.status != 'storniert' AND r.status != 'angelegt' AND EXTRACT(YEAR FROM r.datum) = EXTRACT(YEAR FROM NOW()) 
          ".$this->app->erp->ProjektRechte('r.projekt')."
        GROUP By r.projekt"
    );
    if ($projektesummen === null) {
      $projektesummen = [];
    }

    $colors = array_column($projektesummen, 'farbe');
    $labels = array_column($projektesummen, 'projekt');
    $data = array_column($projektesummen, 'summe');
    $data = array_map('floatVal', $data);

    $dataset = new PieDataset(date('Y'), $data);
    $dataset->setColors($colors);

    $projektsummenChart = new Chart('doughnut', $labels, [$dataset]);
    $projektsummenRenderer = new HtmlRenderer($projektsummenChart, '', 400, 300, ['style' => 'max-width:400px;max-height:300px;']);
    $this->app->Tpl->Set('UMSATZPIE', $projektsummenRenderer->render());


    // jahres uebersicht projekte
    if(!$useJson) {
      $table = new EasyTable($this->app);
      $arr = $this->getDetailQueries('JAHRESUEBERSICHTPROJEKTE');
      $table->Query($arr);
      $table->DisplayNew('JAHRESUEBERSICHTPROJEKTE', 'Anzahl Rechnungen', 'noAction');
    }
    /*
    // gutschriften
    $table = new EasyTable($this->app);
    $arr = $this->getDetailQueries('GUTSCHRIFTJAHR');
    $table->Query($arr);
    $table->DisplayNew('GUTSCHRIFTJAHR','Jahr','noAction');


    // angebot
    $table = new EasyTable($this->app);
    $arr = $this->getDetailQueries('ANGEBOTJAHR');
    $table->Query($arr);
    $table->DisplayNew('ANGEBOTJAHR','Jahr','noAction');*/


    $this->app->Tpl->Parse('TAB1','verkaufszahlen_details.tpl');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function VerkaufszahlenMenu()
  {
    $this->app->erp->Headlines('Verkaufszahlen');
    $this->app->erp->MenuEintrag('index.php?module=verkaufszahlen&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=verkaufszahlen&action=details','Details');
  }

  // Daten für 12-Monats-Diagramm, basierend auf Rechnungen und Gutschriften
  protected function GetFullYearChartDataBasedOnInvoices($projectId, $startDate)
  {
    return $this->app->DB->SelectArr(
      "SELECT r.monthyear, SUM(r.total) AS total 
      FROM (
        (
          SELECT DATE_FORMAT(r2.datum, '%m/%Y') AS monthyear,  
          (SUM(rp.preis * rp.menge * (100 - rp.rabatt) / 100)) AS total 
          FROM rechnung_position rp INNER JOIN rechnung r2 ON rp.rechnung = r2.id 
          WHERE r2.datum > '$startDate' 
          AND r2.projekt = '$projectId' ". $this->app->erp->ProjektRechte('r2.projekt') . "
          GROUP BY `monthyear` 
        ) 
        UNION ALL
        (
          SELECT DATE_FORMAT(r2.datum, '%m/%Y') AS monthyear, 
          -(SUM(rp.preis * rp.menge * (100 - rp.rabatt) / 100)) AS total 
          FROM gutschrift_position rp INNER JOIN gutschrift r2 ON rp.gutschrift = r2.id 
          WHERE r2.datum > '$startDate' AND r2.status <> 'storniert' 
          AND r2.projekt = '$projectId' " . $this->app->erp->ProjektRechte('r2.projekt') . "
          GROUP BY `monthyear` 
        )
      ) AS r 
      GROUP BY `monthyear` 
      ORDER BY `monthyear`"
    );
  }

  // Daten für 12-Monats-Diagramm, basierend auf Aufträgen
  protected function GetFullYearChartDataBasedOnOrders($projectId, $startDate)
  {
    return $this->app->DB->SelectArr(
      "SELECT DATE_FORMAT(a.datum,'%m/%Y') AS monthyear, 
      (SUM(ap.preis * ap.menge * (100 - ap.rabatt) / 100)) AS total 
      FROM auftrag AS a 
      INNER JOIN auftrag_position AS ap ON a.id = ap.auftrag AND a.status <> 'storniert' AND a.belegnr <> '' AND a.datum > '$startDate'
      WHERE a.projekt = '$projectId' " . $this->app->erp->ProjektRechte('a.projekt') . "
      GROUP BY `monthyear` 
      ORDER BY `monthyear`"
    );
  }


  // Daten für Wochendiagramm, basierend auf Rechnungen und Gutschriften
  protected function GetWeekChartDataBasedOnInvoices($projectId)
  {
    return $this->app->DB->SelectArr(
      "SELECT r.datum, SUM(r.betrag) AS betrag 
       FROM (
         (
           SELECT r2.datum, SUM(rp.preis * rp.menge * (100 - rp.rabatt) / 100) AS betrag 
           FROM rechnung_position AS rp 
           INNER JOIN rechnung AS r2 ON rp.rechnung = r2.id 
           WHERE r2.datum > date_sub(CURDATE(), INTERVAL 7 DAY) 
           AND r2.projekt = '$projectId' " . $this->app->erp->ProjektRechte('r2.projekt') . " 
           GROUP BY r2.datum 
         ) 
         UNION ALL
         (
           SELECT r2.datum, -(SUM(rp.preis * rp.menge * (100 - rp.rabatt) / 100)) AS betrag 
           FROM gutschrift_position AS rp 
           INNER JOIN gutschrift AS r2 ON rp.gutschrift = r2.id 
           WHERE r2.datum > date_sub(CURDATE(), INTERVAL 7 DAY) AND r2.status <> 'storniert' 
           AND r2.projekt = '$projectId' " . $this->app->erp->ProjektRechte('r2.projekt') . " 
           GROUP BY r2.datum 
         )
       ) AS r 
       GROUP BY r.datum 
       ORDER BY r.datum"
    );
  }

  // Daten für Wochendiagramm, basierend auf Aufträgen
  protected function GetWeekChartDataBasedOnOrders($projectId)
  {
    return $this->app->DB->SelectArr(
      "SELECT a.datum, 
        SUM(ap.preis * ap.menge * (100 - ap.rabatt) / 100) AS betrag
      FROM auftrag AS a 
      INNER JOIN auftrag_position AS ap ON a.id = ap.auftrag 
        AND a.status <> 'storniert' AND a.belegnr <> '' AND a.datum > DATE_SUB(CURDATE(), INTERVAL 30 DAY)
      WHERE a.projekt = '$projectId' " . $this->app->erp->ProjektRechte('r2.projekt') . "
      GROUP BY a.datum 
      ORDER BY a.datum"
    );
  }

  // 12-Monats-Diagramm erstellen
  protected function BuildFullYearChart($projects, $startDate, $dataBasedOnInvoices)
  {
    $start = new DateTimeImmutable($startDate);
    $end = new DateTimeImmutable('now');
    $interval = new DateInterval('P1M');
    $period = new PeriodMatcher($start, $end, $interval, 'm/Y');
    $labels = $period->getDates();

    $fullYearChart = new Chart('bar');
    $fullYearChart->addLabels($labels);

    foreach ($projects as $projectId => $projectAbbreviation) {
      if ($dataBasedOnInvoices) {
        $data = $this->GetFullYearChartDataBasedOnInvoices($projectId, $startDate);
      } else {
        $data = $this->GetFullYearChartDataBasedOnOrders($projectId, $startDate);
      }
      $matchedData = $period->matchData($data, 'monthyear', 'total');
      $dataset = new BarDataset($projectAbbreviation, $matchedData);
      $fullYearChart->addDataset($dataset);
    }

    return $fullYearChart;
  }

  // Wochen-Diagramm erstellen
  protected function BuildWeekChart($projects, $dataBasesOnInvoices)
  {
    $start = new DateTimeImmutable('-7 day');
    $end = new DateTimeImmutable('now + 1 day');
    $interval = new DateInterval('P1D');
    $period = new PeriodMatcher($start, $end, $interval, 'Y-m-d');
    $labels = $period->getDates('d.m.Y');

    $weekChart = new Chart('bar');
    $weekChart->addLabels($labels);

    foreach ($projects as $projectId => $projectAbbreviation) {
      if($dataBasesOnInvoices === true){
        $data = $this->GetWeekChartDataBasedOnInvoices($projectId);
      }else{
        $data = $this->GetWeekChartDataBasedOnOrders($projectId);
      }

      $matchedData = $period->matchData($data, 'datum', 'betrag');
      $dataset = new BarDataset($projectAbbreviation, $matchedData);
      $weekChart->addDataset($dataset);
    }

    return $weekChart;
  }
}
