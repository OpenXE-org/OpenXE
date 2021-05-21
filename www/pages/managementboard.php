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
use Xentral\Widgets\Chart\HtmlRenderer;
use Xentral\Widgets\Chart\Chart;
use Xentral\Widgets\Chart\PeriodMatcher;
use Xentral\Widgets\Chart\Dataset;
use Xentral\Widgets\Chart\PieDataset;

class Managementboard
{
  /** @var Application */
  public $app;
  /** @var int  */
  public $cashTime = 3600;

  public $lastCacheTime = null;

  /**
   * Managementboard constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    //parent::GenArtikel($app);
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","ManagementboardList");
    $this->app->ActionHandler("cash","ManagementboardCash");
    $this->app->ActionHandler("supergraph","ManagementboardSupergraph");
    $this->app->DefaultActionHandler("list");
    $this->app->erp->Headlines('Managementboard');
    $this->app->ActionHandlerListen($app);
  }

  public function ClearCache()
  {
    $this->app->erp->ClearSqlCache('mangementboard');
  }

  public function ManagementboardMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=managementboard&action=list","Auftr&auml;ge");  
    $this->app->erp->MenuEintrag("index.php?module=managementboard&action=cash","Finanzen / Cashflow");
    $this->app->erp->MenuEintrag("index.php?module=managementboard&action=supergraph","Angebote / Auftr&auml;ge");
    $this->app->erp->RunMenuHook('managementboard');
  }
  
  public function ManagementboardSupergraph()
  {
    $this->ManagementboardMenu();
    $this->app->erp->Headlines('','Angebote / Aufträge');
    // Angebote
    $offerCount = $this->app->DB->SelectArrCache(
      "SELECT DATE_FORMAT(an.datum, '%m/%Y') AS `month`, COUNT(an.id) AS `count` FROM angebot AS an 
       WHERE an.status <> 'storniert' AND an.status != 'angelegt' 
       AND DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m-01') < an.datum 
       GROUP BY `month`",
      $this->cashTime, 'mangementboard'
    );

    // Aufträge
    $orderCount = $this->app->DB->SelectArrCache(
      "SELECT DATE_FORMAT(ab.datum, '%m/%Y') AS `month`, COUNT(ab.id) AS `count` FROM auftrag AS ab 
       WHERE ab.status <> 'storniert' AND ab.status!='angelegt' 
       AND DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m-01') < ab.datum 
       GROUP BY `month`",
      $this->cashTime, 'mangementboard'
    );

    // Rechnungen
    $invoiceCount = $this->app->DB->SelectArrCache(
      "SELECT DATE_FORMAT(re.datum, '%m/%Y') AS `month`, COUNT(re.id) AS `count` FROM rechnung AS re 
       WHERE re.status != 'angelegt' 
       AND DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m-01') < re.datum 
       GROUP BY `month`",
      $this->cashTime, 'mangementboard'
    );

    // Gutschriften
    $creditNoteCount = $this->app->DB->SelectArrCache(
      "SELECT DATE_FORMAT(gu.datum, '%m/%Y') AS `month`, COUNT(gu.id) AS `count` FROM gutschrift AS gu 
       WHERE gu.status != 'angelegt'  
       AND DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m-01') < gu.datum 
       GROUP BY `month`",
      $this->cashTime, 'mangementboard'
    );

    // Daten auf Zeitachse matchen; Leere Monate werden aufgefüllt
    $begin = new DateTime(sprintf('01.01.%s', date('Y')));
    $end = new DateTime('last day of this month');
    $interval = new DateInterval('P1M');
    $period = new PeriodMatcher($begin, $end, $interval, 'm/Y');
    $labels = $period->getDates();
    $offerCountData = $period->matchData($offerCount, 'month', 'count');
    $orderCountData = $period->matchData($orderCount, 'month', 'count');
    $invoiceCountData = $period->matchData($invoiceCount, 'month', 'count');
    $creditNoteCountData = $period->matchData($creditNoteCount, 'month', 'count');

    // Diagramm zusammenbauen
    $chart = new Chart('line');
    $chart->addLabels($labels);
    $chart->addDataset(new Dataset('{|Angebote|}', $offerCountData));
    $chart->addDataset(new Dataset('{|Aufträge|}', $orderCountData));
    $chart->addDataset(new Dataset('{|Rechnungen|}', $invoiceCountData));
    $chart->addDataset(new Dataset('{|Gutschriften|}', $creditNoteCountData));
    $chart->accumulateData();

    // Diagramm rendern
    $renderer = new HtmlRenderer($chart, '{|Kumulierte Angebote / Auftr&auml;ge / Rechnungen / Gutschriften|}', 400, 120);
    $this->app->Tpl->Set('TAB1', $renderer->render());
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  /**
   * @param int|null $seconds
   *
   * @return string
   */
  public function getMinYear($seconds = null)
  {
    if($seconds === null) {
      $seconds = $this->cashTime;
    }
    $dateY = date('Y');
    $minjahr = $this->app->DB->SelectArrCache(
      "SELECT MIN(un.jahr) AS minjahr FROM (
        SELECT DISTINCT YEAR(an.datum) AS jahr FROM angebot AS an WHERE an.datum <> '0000-00-00' UNION ALL 
        SELECT DISTINCT YEAR(au.datum) AS jahr FROM auftrag AS au WHERE au.datum <> '0000-00-00' UNION ALL 
        SELECT DISTINCT YEAR(re.datum) AS jahr FROM rechnung AS re WHERE re.datum <> '0000-00-00' UNION ALL 
        SELECT DISTINCT YEAR(gu.datum) AS jahr FROM gutschrift AS gu WHERE gu.datum <> '0000-00-00'
      ) AS un GROUP BY un.jahr LIMIT 1",
      $seconds, 'managementboard'
    );
    if(!empty($minjahr)) {
      $minjahr = reset($minjahr);
      $minjahr = reset($minjahr);
    }
    if(!$minjahr) {
      $minjahr = $dateY;
    }

    return (String)$minjahr;
  }

  /**
   * @param int|string $col
   * @param int|null   $seconds
   *
   * @return float
   */
  public function getCashValues($col, $seconds = null)
  {
    if($seconds === null) {
      $seconds = $this->cashTime;
    }
    $sql = '';
    switch((int)$col) {
      case 0:
        $sql = "SELECT sum(umsatz_netto) as umsatz_netto, sum(erloes_netto) as deckungsbeitrag 
          FROM auftrag 
          WHERE status != 'storniert' AND status!='angelegt' AND date(datum) = date(now())";
        break;
      case 1:
        $sql = "SELECT sum(umsatz_netto) as umsatz_netto, sum(erloes_netto) as deckungsbeitrag 
               FROM auftrag 
               WHERE status != 'storniert' AND status!='angelegt' AND date(datum) = date(CURDATE()-1)";
        break;
      case 8:
        $sql = "SELECT SUM(r.soll-r.ist) 
           FROM rechnung AS r 
           INNER JOIN projekt AS p on r.projekt = p.id 
           WHERE r.zahlungsstatus='offen' AND r.mahnwesen!='forderungsverlust' AND r.belegnr!='' AND r.mahnwesen_gesperrt='0' AND r.mahnwesenfestsetzen!=1 AND p.mahnwesen=1";
        break;
      case 9:
        $sql = "SELECT SUM(v.betrag) FROM `verbindlichkeit` AS `v` WHERE v.status!='bezahlt' AND v.status_beleg!='storniert'";
        break;
      case 10:
        $sql = "select SUM(ap.menge*ap.preis*(100-ap.rabatt)/100)
        FROM auftrag_position ap 
        LEFT JOIN auftrag a ON ap.auftrag=a.id 
        LEFT JOIN adresse ad ON ad.id=a.adresse 
        LEFT JOIN projekt p ON p.id=a.projekt
        WHERE ap.geliefert_menge < ap.menge AND ap.geliefert!=1 AND a.status!='abgeschlossen' AND a.status!='storniert' 
          AND a.status!='angelegt'";
        break;
      case 11:
        $sql = "SELECT SUM(r.soll-r.ist) 
             FROM rechnung AS r 
             INNER JOIN projekt AS p on r.projekt = p.id 
             WHERE r.zahlungsstatus='offen' AND r.mahnwesen!='forderungsverlust' AND r.belegnr!='' AND r.mahnwesen!=''  
               AND r.mahnwesen_gesperrt='0' AND r.mahnwesenfestsetzen!=1 AND p.mahnwesen=1";
        break;
      case 25:
        $sql = "SELECT SUM(if(k.saldo_summieren,k.saldo_betrag+IFNULL((SELECT SUM(ka.haben-ka.soll) 
          FROM kontoauszuege ka 
          WHERE ka.konto=k.id AND ka.buchung >= k.saldo_datum AND (ka.importfehler=0 OR ka.importfehler IS NULL)),0),0)) 
          FROM konten k 
          LEFT JOIN projekt p ON p.id=k.projekt 
          WHERE k.geloescht != 1 ".$this->app->erp->ProjektRechte();
        break;
      case 14:
      case 18:
      case 22:
        $intervall = (int)(($col - 13) / 4);
        $sql = "SELECT SUM(`betrag`) 
                FROM `verbindlichkeit` 
                WHERE DATE_FORMAT(`rechnungsdatum`,'%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL $intervall MONTH),'%Y-%m') 
                  AND `status_beleg` != 'storniert'";
        break;
      case 13:
      case 17:
      case 21:
        $intervall = (int)(($col - 12) / 4);
        if($col{3} === '1') {
          $sql = "SELECT sum(umsatz_netto) FROM rechnung WHERE DATE_FORMAT(datum,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m') AND status!='angelegt'";
        }
        if($col{3} === '2') {
          $sql = "SELECT sum(umsatz_netto) FROM gutschrift WHERE DATE_FORMAT(datum,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m') AND status!='angelegt'";
        }
        break;
      case 15:
      case 19:
      case 23:
        $intervall = (int)(($col - 14) / 4);
        if($col{3} === '1') {
          $sql = "SELECT sum(k.haben) FROM kontoauszuege k LEFT JOIN kontorahmen ko ON ko.sachkonto=k.gegenkonto WHERE (k.importfehler!=1 OR k.importfehler IS NULL) AND 
          DATE_FORMAT(k.buchung,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m')  AND ko.art=1 ";
        }
        if($col{3} === '2') {
          $sql = "SELECT sum(k.soll) FROM kontoauszuege k LEFT JOIN kontorahmen ko ON ko.sachkonto=k.gegenkonto WHERE (k.importfehler!=1 OR k.importfehler IS NULL) AND 
          DATE_FORMAT(k.buchung,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m') AND ko.art=1 ";
        }
        break;
      case 16:
      case 20:
      case 24:

        $intervall = (int)(($col - 15) / 4);
        if($col{3} === '1') {
          $sql = "SELECT sum(haben) FROM kontoauszuege WHERE (importfehler!=1 OR importfehler IS NULL) AND 
            DATE_FORMAT(buchung,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m')";
        }
        if($col{3} === '2') {
          $sql = "SELECT sum(soll) FROM kontoauszuege WHERE (importfehler!=1 OR importfehler IS NULL) AND 
            DATE_FORMAT(buchung,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m')";
        }
        break;

      default:

        return (float)0;
        break;
    }
    if(empty($sql)) {
      return (float)0;
    }

    $lastTime = null;
    $ret = $this->app->DB->SelectArrCache($sql, $seconds, 'managementboard', $lastTime);
    if($lastTime !== null && strtotime($lastTime) > 1) {
      if($this->lastCacheTime === null || strtotime($this->lastCacheTime) > strtotime($lastTime)){
        $this->lastCacheTime = $lastTime;
      }
    }
    if(!empty($ret)) {
      $ret = reset($ret);
      $ret = reset($ret);
      return (float)$ret;
    }

    return (float)0;
  }

  public function ManagementboardCash()
  {
    $this->ManagementboardMenu();
    $this->app->erp->Headlines('','Finanzen / Cashflow');
    $projekt = $this->app->Secure->GetPOST('projekt');
    $this->app->Tpl->Set('PROJEKT',$projekt);
    $projekt = explode(' ',$projekt);
    $projekt = reset($projekt);
    $projekt = (int)$this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '$projekt' LIMIT 1");
    $vergleichsjahr = $this->app->Secure->GetPOST('vergleichsjahr');
    $jahr = $this->app->Secure->GetPOST('jahr');
    $dateY = date('Y');
    if(!$jahr) {
      $jahr = $dateY;
    }

    $minjahr = $this->getMinYear();

    for($i = $minjahr; $i <= $dateY; $i++) {
      $this->app->Tpl->Add('SELJAHR','<option'.($i == $jahr?' selected="selected"':'').'>'.$i.'</option>');
    }
    for($i = $minjahr; $i <= $dateY - 1; $i++)  {
      $this->app->Tpl->Add('SELVERGLEICHSJAHR','<option'.($i == $vergleichsjahr?' selected="selected"':'').'>'.$i.'</option>');
    }

    $this->app->YUI->AutoComplete('projekt','projektname');
    $this->lastCacheTime = $this->app->DB->Select('SELECT NOW()');

    $w[0] = $this->getCashValues(0);// $this->app->DB->Select("SELECT sum(umsatz_netto) as umsatz_netto, sum(erloes_netto) as deckungsbeitrag FROM auftrag WHERE status != 'storniert' AND status!='angelegt' AND date(datum) = date(now())");
    $w[1] = $this->getCashValues(1);//$this->app->DB->Select("SELECT sum(umsatz_netto) as umsatz_netto, sum(erloes_netto) as deckungsbeitrag FROM auftrag WHERE status != 'storniert' AND status!='angelegt' AND date(datum) = date(CURDATE()-1)");




    $w[8] = $this->getCashValues(8);//$this->app->DB->Select("SELECT SUM(r.soll-r.ist) FROM rechnung r LEFT JOIN projekt p on r.projekt = p.id WHERE r.zahlungsstatus='offen' AND r.mahnwesen!='forderungsverlust' AND r.belegnr!='' AND r.mahnwesen_gesperrt='0' AND r.mahnwesenfestsetzen!=1 AND p.mahnwesen=1");
    if($w[8] == ""){
        $w[8] = "0,00";
    }

    $w[9] = $this->getCashValues(9);//$this->app->DB->Select("SELECT SUM(v.betrag) FROM verbindlichkeit v WHERE v.status!='bezahlt' AND v.status!='storniert'");
    if($w[9] == ""){
        $w[9] = "0,00";
    }

    $sql = "select SUM(ap.menge*ap.preis*(100-ap.rabatt)/100)
      FROM auftrag_position ap LEFT JOIN auftrag a ON ap.auftrag=a.id LEFT JOIN adresse ad ON ad.id=a.adresse LEFT JOIN projekt p ON p.id=a.projekt
      WHERE ap.geliefert_menge < ap.menge AND ap.geliefert!=1 AND a.status!='abgeschlossen' AND a.status!='storniert' AND a.status!='angelegt'";
    $w[10] = $this->getCashValues(10);// $this->app->DB->Select($sql);
    if($w[10] == ""){
        $w[10] = "0,00";
    }

    $w[11] = $this->getCashValues(11);//$this->app->DB->Select("SELECT SUM(r.soll-r.ist) FROM rechnung r LEFT JOIN projekt p on r.projekt = p.id WHERE r.zahlungsstatus='offen' AND r.mahnwesen!='forderungsverlust' AND r.belegnr!='' AND r.mahnwesen!=''  AND r.mahnwesen_gesperrt='0' AND r.mahnwesenfestsetzen!=1 AND p.mahnwesen=1");
    if($w[11] == ""){
        $w[11] = "0,00";
    }

    $wi=12; 
    for($intervall=0;$intervall<=2;$intervall++)
    {
      $new = true;
      if($new){
        $w[$wi++] = $this->getCashValues($wi . '.1') - $this->getCashValues($wi . '.2');
      }
      else{
        $w[$wi++] = $this->app->DB->Select("SELECT sum(umsatz_netto) FROM rechnung WHERE DATE_FORMAT(datum,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m') AND status!='angelegt'") -
        $this->app->DB->Select("SELECT sum(umsatz_netto) FROM gutschrift WHERE DATE_FORMAT(datum,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m') AND status!='angelegt'");
      }
      if($new) {
        $w[$wi++] = $this->getCashValues($wi);
      } else{
        $w[$wi++] = $this->app->DB->Select(
          "SELECT SUM(`betrag`) 
          FROM `verbindlichkeit` 
          WHERE DATE_FORMAT(`rechnungsdatum`, '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m') 
            AND `status_beleg` != 'storniert'"
        );
      }
      if($new){
        $w[$wi++] = $this->getCashValues($wi . '.1') - $this->getCashValues($wi . '.2');
      }
      else{
        $w[$wi++] = $this->app->DB->Select("SELECT sum(k.haben) FROM kontoauszuege k LEFT JOIN kontorahmen ko ON ko.sachkonto=k.gegenkonto WHERE (k.importfehler!=1 OR k.importfehler IS NULL) AND
          DATE_FORMAT(k.buchung,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m')  AND ko.art=1 ") -
        $this->app->DB->Select("SELECT sum(k.soll) FROM kontoauszuege k LEFT JOIN kontorahmen ko ON ko.sachkonto=k.gegenkonto WHERE (k.importfehler!=1 OR k.importfehler IS NULL) AND 
          DATE_FORMAT(k.buchung,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m') AND ko.art=1 ");
      }
      if($new){
        $w[$wi++] = $this->getCashValues($wi . '.1') - $this->getCashValues($wi . '.2');
      }
      else{
        $w[$wi++] = $this->app->DB->Select("SELECT sum(haben) FROM kontoauszuege WHERE (importfehler!=1 OR importfehler IS NULL) AND
          DATE_FORMAT(buchung,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m')") -
          $this->app->DB->Select("SELECT sum(soll) FROM kontoauszuege WHERE (importfehler!=1 OR importfehler IS NULL) AND 
          DATE_FORMAT(buchung,'%Y-%m')=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL $intervall MONTH),'%Y-%m')");
      }
    }

    if($w[13] == ""){
        $w[13] = "0,00";
    }
    if($w[17] == ""){
        $w[17] = "0,00";
    }
    if($w[21] == ""){
        $w[21] = "0,00";
    }


    /** @var Rechnungslauf $obj */
    $obj = $this->app->erp->LoadModul('rechnungslauf');
    if($obj){
      $w[24] = $obj->RechnungslaufRechnungslauf(true);
    }
    else {
      $w[24] = 0;
    }

    $w[25] = $this->getCashValues(25);//$this->app->DB->Select("SELECT SUM(if(k.saldo_summieren,k.saldo_betrag+IFNULL((SELECT SUM(ka.haben-ka.soll) FROM kontoauszuege ka WHERE ka.konto=k.id AND ka.buchung >= k.saldo_datum AND (ka.importfehler=0 OR ka.importfehler IS NULL)),0),0)) FROM konten k LEFT JOIN projekt p ON p.id=k.projekt WHERE k.geloescht != 1 ".$this->app->erp->ProjektRechte());

    $this->app->Tpl->Set('FINANZENBESTELLUNGEN',number_format($w[0],2,',','.'));
    $this->app->Tpl->Set('FINANZENVERBINDLICHKEITEN',number_format($w[1],2,',','.'));
    $this->app->Tpl->Set('FINANZENRECHNUNGEN',number_format($w[2],2,',','.'));
    $this->app->Tpl->Set('FINANZENGUTSCHRIFTEN',number_format($w[3],2,',','.'));

    $this->app->Tpl->Set('UMSATZBRUTTO',number_format($w[4],2,',','.'));
    $this->app->Tpl->Set('UMSATZNETTO',number_format($w[5],2,',','.'));
    $this->app->Tpl->Set('DBINEUR',number_format($w[6],2,',','.'));
    $this->app->Tpl->Set('DBINPROZENT',number_format($w[7],2,',','.'));
 
    $this->app->Tpl->Set('OFFENERECHNUNGEN',number_format($w[8],2,',','.'));
    $this->app->Tpl->Set('OFFENEVERBINDLICHKEITEN',number_format($w[9],2,',','.'));
    $this->app->Tpl->Set('OFFENEAUFTRAEGE',number_format($w[10],2,',','.'));
    $this->app->Tpl->Set('MAHNWESEN',number_format($w[11],2,',','.'));
    
    $this->app->Tpl->Set('RECHNUNGGUTSCHRIFT',number_format($w[12],2,',','.'));
    $this->app->Tpl->Set('VERBINDLICHKEITEN',number_format($w[13],2,',','.'));
    $this->app->Tpl->Set('BANKEINNAHMEN',number_format($w[14],2,',','.'));
    $this->app->Tpl->Set('BANKAUSGABEN',number_format($w[15],2,',','.'));
 
    $this->app->Tpl->Set('RECHNUNGGUTSCHRIFTLETZTER',number_format($w[16],2,',','.'));
    $this->app->Tpl->Set('VERBINDLICHKEITENLETZTER',number_format($w[17],2,',','.'));
    $this->app->Tpl->Set('BANKEINNAHMENLETZTER',number_format($w[18],2,',','.'));
    $this->app->Tpl->Set('BANKAUSGABENLETZTER',number_format($w[19],2,',','.'));
    
    $this->app->Tpl->Set('RECHNUNGGUTSCHRIFTVORLETZTER',number_format($w[20],2,',','.'));
    $this->app->Tpl->Set('VERBINDLICHKEITENVORLETZTER',number_format($w[21],2,',','.'));
    $this->app->Tpl->Set('BANKEINNAHMENVORLETZTER',number_format($w[22],2,',','.'));
    $this->app->Tpl->Set('BANKAUSGABENVORLETZTER',number_format($w[23],2,',','.'));

    $this->app->Tpl->Set('ABOLAUF',number_format($w[24],2,',','.'));
    $this->app->Tpl->Set('BANKKONTENGESAMT',number_format($w[25],2,',','.'));
 
    $heute = $this->app->DB->SelectArrCache(
      "SELECT count(id) as co, sum(umsatz_netto) as umsatz_netto, sum(erloes_netto) as deckungsbeitrag 
      FROM auftrag 
      WHERE status != 'storniert' AND status!='angelegt' AND DATE_FORMAT(datum,'%m-%Y') = DATE_FORMAT(NOW(),'%m-%Y')",
      $this->cashTime, 'managementboard'
    );

    // HINWEIS heute = dieser Monat :-)

    $paketeheute = 0;
    $umsatzheute = 0;
    $deckungsbeitragheute = 0;
    $dbprozentheute = 0;
    $deckungsbeitraggestern = 0;
    $dbprozentgestern = 0;
    if($heute)
    {
      $paketeheute = $heute[0]['co'];
      $umsatzheute = $heute[0]['umsatz_netto'];
      $deckungsbeitragheute = $heute[0]['deckungsbeitrag'];
    }
    if($umsatzheute > 0) {
      $dbprozentheute = 100* $deckungsbeitragheute / $umsatzheute;
    }
    if($umsatzgestern > 0) {
      $dbprozentgestern = 100* $deckungsbeitraggestern / $umsatzgestern;
    }
    
    $this->app->Tpl->Set('PAKETEHEUTE',(int)$paketeheute);
    $this->app->Tpl->Set('UMSATZHEUTE',number_format($umsatzheute,2,',','.'));
    $this->app->Tpl->Set('DECKUNGSBEITRAGHEUTE',number_format($deckungsbeitragheute,2,',','.'));
    $this->app->Tpl->Set('DBPROZENTHEUTE',number_format($dbprozentheute,2,',','.'));
    
    $this->app->Tpl->Set('PAKETEGESTERN',(int)$paketegestern);
    $this->app->Tpl->Set('UMSATZGESTERN',number_format($umsatzgestern,2,',','.'));
    $this->app->Tpl->Set('DECKUNGSBEITRAGGESTERN',number_format($deckungsbeitraggestern,2,',','.'));
    $this->app->Tpl->Set('DBPROZENTGESTERN',number_format($dbprozentgestern,2,',','.'));
    $zeitaktuell = (float)$this->app->DB->Select("SELECT sum(TIMESTAMPDIFF(HOUR,von,bis)) FROM zeiterfassung WHERE DATE_FORMAT(von,'%m-%Y') = DATE_FORMAT(NOW(),'%m-%Y')  ");
    $zeitletzte = (float)$this->app->DB->Select("SELECT sum(TIMESTAMPDIFF(HOUR,von,bis)) FROM zeiterfassung WHERE DATE_FORMAT(von,'%m-%Y') = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 MONTH),'%m-%Y')  ");
    $abraktuell = (float)$this->app->DB->Select("SELECT sum(TIMESTAMPDIFF(HOUR,von,bis)) FROM zeiterfassung WHERE DATE_FORMAT(von,'%m-%Y') = DATE_FORMAT(NOW(),'%m-%Y') and abrechnen = 1  ");
    $abrletzte = (float)$this->app->DB->Select("SELECT sum(TIMESTAMPDIFF(HOUR,von,bis)) FROM zeiterfassung WHERE DATE_FORMAT(von,'%m-%Y') = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 MONTH),'%m-%Y') and abrechnen = 1 ");
    
    $this->app->Tpl->Set('ZEITHEUTE',number_format($zeitaktuell,2,',','.'));
    $this->app->Tpl->Set('ZEITWOCHE',number_format($zeitletzte,2,',','.'));
    $this->app->Tpl->Set('ABRHEUTE',number_format($abraktuell,2,',','.'));
    $this->app->Tpl->Set('ABRWOCHE',number_format($abrletzte,2,',','.'));

    $this->DrawDiagramsCash($jahr, $vergleichsjahr, $projekt);

    $this->app->Tpl->Set('LASTCALC', '<i class="grey">letzte Berechnung: '.date('d.m.Y H:i:s', strtotime($this->lastCacheTime)).'</i>');

    $this->app->Tpl->Parse('PAGE','managementboard_cash.tpl');
  }

  /**
   * @param $jahr
   * @param $vergleichsjahr
   * @param $projekt
   */
  public function DrawDiagramsCash($jahr, $vergleichsjahr, $projekt)
  {
    // Diagramm: Umsatz Netto
    $salesChart = $this->GetSalesChart($jahr, $vergleichsjahr, $projekt);
    $salesChartRenderer = new HtmlRenderer($salesChart, '{|Umsatz Netto|}', 400, 160);

    // Diagramm: Kumulierter Umsatz Netto
    $accumulatedSalesChart = clone $salesChart;
    $accumulatedSalesChart->accumulateData();
    $accumulatedSalesChartRenderer = new HtmlRenderer($accumulatedSalesChart, '{|Kumulierter Umsatz Netto|}', 400, 120);

    $this->app->Tpl->Set('DIAGRAMM1', $salesChartRenderer->render());
    $this->app->Tpl->Set('DIAGRAMM2', $accumulatedSalesChartRenderer->render());
    /*
   $chart = new \Chart($this->app,'line');
   $chart->Query("SELECT date_format(datum,'%d.%m.%Y') as tage, count(id) as anzahl, round(sum(umsatz_netto),2) as Umsatz FROM auftrag where datediff(now(),datum) <= 90 AND status != 'storniert' AND status!='angelegt' GROUP BY datum order by datum",true, array('typ'=>'tage','zeitraum'=>90));
   $chart->yAxes = array(1,2);
   $chart->DisplayWithBox('DIAGRAMM3','auftraegeeingang','Auftragseing&auml;nge', 400, 210);
*/
    /*
        $chart = new \Chart($this->app,'line');
        $chart->Query("SELECT date_format(rechnungsdatum,'%d.%m.%Y') as tag, round(sum(betrag),2) as Umsatz FROM verbindlichkeit where datediff(now(),rechnungsdatum) <= 90 AND status != 'storniert' AND status!='angelegt' GROUP BY rechnungsdatum order by rechnungsdatum",true, array('typ'=>'tage','zeitraum'=>90));
        $chart->DisplayWithBox('DIAGRAMM4','verbindlichkeiten','Verbindlichkeiten', 400, 175);
    */
  }

  public function ManagementboardList()
  {
    $this->ManagementboardMenu();
    $this->app->erp->Headlines('','Aufträge');
    // Auftragseingänge
    $incomingOrdersChart = $this->GetIncomingOrdersChart();
    $incomingOrdersRenderer = new HtmlRenderer($incomingOrdersChart, '{|Auftragseingänge|}', 400, 140);
    $this->app->Tpl->Set('AUFTRAGSEINGANG', $incomingOrdersRenderer->render());

    // Bestellungen
    $purchaseOrdersChart = $this->GetPurchaseOrdersChart();
    $purchaseOrdersRenderer = new HtmlRenderer($purchaseOrdersChart, '{|Bestellungen|}', 400, 140);
    $this->app->Tpl->Set('BESTELLUNGEN', $purchaseOrdersRenderer->render());

    // Auftragseingang Projekte im Jahr
    $projectSalesYearChart = $this->GetIncomingOrdersForCurrentYearGroupedByProjectChart();
    $projectSalesYearRenderer = new HtmlRenderer($projectSalesYearChart, '{|Auftragseingang Projekte im Jahr|}', 400, 300);
    $this->app->Tpl->Set('UMSATZPROJEKTEJAHR', $projectSalesYearRenderer->render());

    // Auftragseingang Projekte im Monat
    $projectSalesMonthChart = $this->GetIncomingOrdersForCurrentMonthGroupedByProjectChart();
    $projectSalesMonthRenderer = new HtmlRenderer($projectSalesMonthChart, '{|Auftragseingang Projekte im Monat|}', 400, 300);
    $this->app->Tpl->Set('UMSATZPROJEKTEMONAT', $projectSalesMonthRenderer->render());

    // Mitarbeiter
    $employeesChart = $this->GetEmployeesChart();
    $employeesRenderer = new HtmlRenderer($employeesChart, '{|Mitarbeiter|}', 400, 300);
    $this->app->Tpl->Set('MITARBEITER', $employeesRenderer->render());

    // Top 5 Artikel (90 Tage)
    $articlesChart = $this->GetArticlesChart();
    $articlesRenderer = new HtmlRenderer($articlesChart, '{|Top 5 Artikel (90 Tage)|}', 400, 300);
    $this->app->Tpl->Set('VERKAUFTEARTIKEL', $articlesRenderer->render());

    $this->app->Tpl->Parse('PAGE','managementboard_list.tpl');
  }

  function GetOffersByYearGroupedByMonth($year, $projectId = null)
  {
    // Angebote
    return $this->app->DB->SelectArrCache(sprintf(
      "SELECT DATE_FORMAT(an.datum, '%%m') AS `month`, SUM(an.umsatz_netto) AS `value` FROM angebot AS an
        WHERE an.status <> 'storniert' AND an.status != 'angelegt' AND an.datum >= '%s-01-01' AND an.datum <= '%s-12-31' %s
        GROUP BY `month`",
      $year, $year, (!empty($projectId) ? 'AND an.projekt = ' . (int)$projectId : '')
    ),$this->cashTime, 'mangementboard');
  }

  function GetOrdersByYearGroupedByMonth($year, $projectId = null)
  {
    // Aufträge
    return $this->app->DB->SelectArrCache(sprintf(
      "SELECT DATE_FORMAT(ab.datum, '%%m') AS `month`, SUM(ab.umsatz_netto) AS `value` FROM auftrag AS ab 
        WHERE ab.status <> 'storniert' AND ab.status != 'angelegt' AND ab.datum >= '%s-01-01' AND ab.datum <= '%s-12-31' %s
        GROUP BY `month`",
      $year, $year, (!empty($projectId) ? 'AND ab.projekt = ' . (int)$projectId : '')
    ),$this->cashTime, 'mangementboard');
  }

  function GetInvoicesByYearGroupedByMonth($year, $projectId = null)
  {
    // Rechnungen
    return $this->app->DB->SelectArrCache(sprintf(
      "SELECT DATE_FORMAT(re.datum, '%%m') AS `month`, SUM(re.umsatz_netto) AS `value` FROM rechnung AS re 
       WHERE re.status != 'angelegt' AND re.datum >= '%s-01-01' AND re.datum <= '%s-12-31' %s
       GROUP BY `month`",
      $year, $year, (!empty($projectId) ? 'AND re.projekt = ' . (int)$projectId : '')
    ),$this->cashTime, 'mangementboard');
  }

  function GetCreditNotesByYearGroupedByMonth($year, $projectId = null)
  {
    // Gutschriften
    return $this->app->DB->SelectArrCache(sprintf(
      "SELECT DATE_FORMAT(gu.datum, '%%m') AS `month`, SUM(gu.umsatz_netto) AS `value` FROM gutschrift AS gu 
        WHERE gu.status != 'angelegt' AND gu.datum >= '%s-01-01' AND gu.datum <= '%s-12-31' %s
        GROUP BY `month`",
      $year, $year, (!empty($projectId) ? 'AND gu.projekt = ' . (int)$projectId : '')
    ),$this->cashTime, 'mangementboard');
  }

  function GetSalesChart($year, $comparisonYear, $projectId = null)
  {
    // Daten holen
    $offersCurrentYear = $this->GetOffersByYearGroupedByMonth($year, $projectId);
    $ordersCurrentYear = $this->GetOrdersByYearGroupedByMonth($year, $projectId);
    $invoicesCurrentYear = $this->GetInvoicesByYearGroupedByMonth($year, $projectId);
    $creditNotesCurrentYear = $this->GetCreditNotesByYearGroupedByMonth($year, $projectId);

    if (!empty($comparisonYear)) {
      $offersComparedYear = $this->GetOffersByYearGroupedByMonth($comparisonYear, $projectId);
      $ordersComparedYear = $this->GetOrdersByYearGroupedByMonth($comparisonYear, $projectId);
      $invoicesComparedYear = $this->GetInvoicesByYearGroupedByMonth($comparisonYear, $projectId);
      $creditNotesComparedYear = $this->GetCreditNotesByYearGroupedByMonth($comparisonYear, $projectId);
    }

    // Daten auf Zeitachse matchen; Leere Monate werden aufgefüllt
    $begin = new DateTime(sprintf('01.01.%s', $year));
    $end = new DateTime(sprintf('31.12.%s', $year));
    $interval = new DateInterval('P1M');
    $period = new PeriodMatcher($begin, $end, $interval, 'm');
    $labels = $period->getDates('m/Y');
    $offersCurrentYear = $period->matchData($offersCurrentYear, 'month', 'value');
    $ordersCurrentYear = $period->matchData($ordersCurrentYear, 'month', 'value');
    $invoicesCurrentYear = $period->matchData($invoicesCurrentYear, 'month', 'value');
    $creditNotesCurrentYear = $period->matchData($creditNotesCurrentYear, 'month', 'value');
    if (!empty($comparisonYear)){
      $offersComparedYear = $period->matchData($offersComparedYear, 'month', 'value');
      $ordersComparedYear = $period->matchData($ordersComparedYear, 'month', 'value');
      $invoicesComparedYear = $period->matchData($invoicesComparedYear, 'month', 'value');
      $creditNotesComparedYear = $period->matchData($creditNotesComparedYear, 'month', 'value');
    }

    // Diagramm zusammenbauen
    $chart = new Chart('line');
    $chart->addLabels($labels);
    $chart->addDataset(new Dataset('{|Angebote|} ' . $year, $offersCurrentYear));
    $chart->addDataset(new Dataset('{|Aufträge|} ' . $year, $ordersCurrentYear));
    $chart->addDataset(new Dataset('{|Rechnungen|} ' . $year, $invoicesCurrentYear));
    $chart->addDataset(new Dataset('{|Gutschriften|} ' . $year, $creditNotesCurrentYear));
    if (!empty($comparisonYear)){
      $offersComparedYearDataset = new Dataset('{|Angebote|} ' . $comparisonYear, $offersComparedYear);
      $ordersCurrentYearDataset = new Dataset('{|Aufträge|} ' . $comparisonYear, $ordersComparedYear);
      $invoicesCurrentYearDataset = new Dataset('{|Rechnungen|} ' . $comparisonYear, $invoicesComparedYear);
      $creditNotesCurrentYearDataset = new Dataset('{|Gutschriften|} ' . $comparisonYear, $creditNotesComparedYear);
      $offersComparedYearDataset->setLineStyle(Dataset::LINE_STYLE_DASHED);
      $ordersCurrentYearDataset->setLineStyle(Dataset::LINE_STYLE_DASHED);
      $invoicesCurrentYearDataset->setLineStyle(Dataset::LINE_STYLE_DASHED);
      $creditNotesCurrentYearDataset->setLineStyle(Dataset::LINE_STYLE_DASHED);
      $chart->addDataset($offersComparedYearDataset);
      $chart->addDataset($ordersCurrentYearDataset);
      $chart->addDataset($invoicesCurrentYearDataset);
      $chart->addDataset($creditNotesCurrentYearDataset);
    }

    return $chart;
  }

  function GetIncomingOrdersChart()
  {
    $incomingOrders = $this->app->DB->SelectArrCache(
      "SELECT DATE_FORMAT(a.datum, '%d.%m.%Y') AS tag, COUNT(a.id) AS anzahl, ROUND(SUM(a.umsatz_netto), 2) AS umsatz 
       FROM auftrag AS a WHERE DATEDIFF(NOW(), a.datum) <= 90 AND a.status != 'storniert' AND a.status != 'angelegt' 
       GROUP BY a.datum ORDER BY a.datum",
      $this->cashTime, 'mangementboard'
    );

    $begin = new DateTime('90 days ago');
    $end = new DateTime('now');
    $interval = new DateInterval('P1D');
    $period = new PeriodMatcher($begin, $end, $interval, 'd.m.Y');
    $matchedData1 = $period->matchData($incomingOrders, 'tag', 'anzahl');
    $matchedData2 = $period->matchData($incomingOrders, 'tag', 'umsatz');

    $labels = $period->getDates();
    $incomingOrdersChart = new Chart('line', $labels);
    $incomingOrdersChart->addDatasetAsYAxis(new Dataset('{|Anzahl|}', $matchedData1), 'left', 'linear');
    $incomingOrdersChart->addDatasetAsYAxis(new Dataset('{|Umsatz|}', $matchedData2), 'right', 'linear');

    return $incomingOrdersChart;
  }

  function GetPurchaseOrdersChart()
  {
    $purchaseOrders = $this->app->DB->SelectArrCache(
      "SELECT DATE_FORMAT(b.datum, '%d.%m.%Y') as tag, COUNT(b.id) AS anzahl, ROUND(SUM(b.gesamtsumme), 2) AS umsatz 
       FROM bestellung AS b WHERE DATEDIFF(NOW(), b.datum) <= 90 AND b.status != 'storniert' AND b.status != 'angelegt' 
       GROUP BY b.datum ORDER BY b.datum",
      $this->cashTime, 'mangementboard'
    );

    $begin = new DateTime('90 days ago');
    $end = new DateTime('now');
    $interval = new DateInterval('P1D');
    $period = new PeriodMatcher($begin, $end, $interval, 'd.m.Y');
    $matchedData1 = $period->matchData($purchaseOrders, 'tag', 'anzahl');
    $matchedData2 = $period->matchData($purchaseOrders, 'tag', 'umsatz');

    $labels = $period->getDates();
    $purchaseOrdersChart = new Chart('line', $labels);
    $purchaseOrdersChart->addDatasetAsYAxis(new Dataset('{|Anzahl|}', $matchedData1), 'left', 'linear');
    $purchaseOrdersChart->addDatasetAsYAxis(new Dataset('{|Umsatz|}', $matchedData2), 'right', 'linear');

    return $purchaseOrdersChart;
  }

  function GetIncomingOrdersForCurrentYearGroupedByProjectChart()
  {
    // Auftragseingang Projekte im Jahr
    $chartData = $this->app->DB->SelectArrCache(
      "SELECT IFNULL(p.abkuerzung, 'Ohne Projekt') AS name,
         ROUND(SUM(a.umsatz_netto), 2) AS sales, IFNULL(p.farbe, '') AS color 
       FROM auftrag AS a LEFT JOIN projekt AS p ON a.projekt = p.id 
       WHERE YEAR(a.datum) = YEAR(NOW()) AND a.status != 'storniert' AND a.status != 'angelegt' 
       GROUP BY a.projekt ORDER BY SUM(a.umsatz_netto) DESC LIMIT 5",
      $this->cashTime, 'mangementboard'
    );
    if ($chartData === null) {
      $chartData = [];
    }

    $colors = array_column($chartData, 'color');
    $labels = array_column($chartData, 'name');
    $data = array_column($chartData, 'sales');
    $data = array_map('floatVal', $data);

    $dataset = new PieDataset(date('Y'), $data);
    $dataset->setColors($colors);

    return new Chart('doughnut', $labels, [$dataset]);
  }

  function GetIncomingOrdersForCurrentMonthGroupedByProjectChart()
  {
    // Auftragseingang Projekte im Monat
    $chartData = $this->app->DB->SelectArrCache(
      "SELECT IFNULL(p.abkuerzung,'Ohne Projekt') AS name, 
         ROUND(SUM(a.umsatz_netto), 2) AS sales, IFNULL(p.farbe, '') AS color 
       FROM auftrag a LEFT JOIN projekt p ON a.projekt = p.id  
       WHERE YEAR(a.datum) = YEAR(NOW()) AND MONTH(a.datum) = MONTH(NOW()) 
       AND a.status != 'storniert' AND a.status!='angelegt' 
       GROUP BY a.projekt ORDER BY SUM(a.umsatz_netto) DESC LIMIT 5",
      $this->cashTime, 'mangementboard'
    );
    if ($chartData === null) {
      $chartData = [];
    }

    $colors = array_column($chartData, 'color');
    $labels = array_column($chartData, 'name');
    $data = array_column($chartData, 'sales');
    $data = array_map('floatVal', $data);

    $dataset = new PieDataset(date('Y'), $data);
    $dataset->setColors($colors);

    return new Chart('doughnut', $labels, [$dataset]);
  }

  function GetEmployeesChart()
  {
      $chartData = $this->app->DB->SelectArrCache(
          "SELECT IFNULL(p.abkuerzung, 'Ohne Projekt') AS name, 
           COUNT(DISTINCT a.id) AS `count`, IFNULL(p.farbe, '') AS color 
           FROM adresse AS a INNER JOIN adresse_rolle AS ar ON a.id = ar.adresse 
           AND ar.subjekt LIKE 'Mitarbeiter' AND (ar.bis = '0000-00-00' OR ar.bis >= CURDATE() OR ISNULL(ar.bis)) 
           LEFT JOIN projekt AS p ON a.projekt = p.id  
           WHERE a.geloescht <> 1 GROUP BY a.projekt ORDER by COUNT(DISTINCT a.id) DESC LIMIT 5",
        $this->cashTime, 'mangementboard'
      );
      if ($chartData === null) {
          $chartData = [];
      }

      $colors = array_column($chartData, 'color');
      $labels = array_column($chartData, 'name');
      $data = array_column($chartData, 'count');
      $data = array_map('floatVal', $data);

      $dataset = new PieDataset(date('Y'), $data);
      $dataset->setColors($colors);

      return new Chart('doughnut', $labels, [$dataset]);
  }

  function GetArticlesChart()
  {
      $chartData = $this->app->DB->SelectArrCache(
        "SELECT 
           CONCAT(art.nummer, ' ', SUBSTRING(art.name_de, 1, 30)) AS artikel, 
           ROUND(SUM(rp.menge * rp.preis), 2) AS menge 
         FROM artikel AS art INNER JOIN rechnung_position AS rp ON art.id = rp.artikel 
         INNER JOIN rechnung AS r ON r.id = rp.rechnung
         WHERE r.status != 'angelegt' AND r.status <> 'storniert' AND r.status!='angelegt' 
         AND DATEDIFF(NOW(), r.datum) <= 90 
         GROUP BY art.id ORDER by SUM(rp.menge * rp.preis) DESC LIMIT 5",
        $this->cashTime, 'mangementboard'
      );
      if ($chartData === null) {
          $chartData = [];
      }

      $labels = array_column($chartData, 'artikel');
      $data = array_column($chartData, 'menge');
      $data = array_map('floatVal', $data);

      $dataset = new PieDataset(date('Y'), $data);
      $dataset->setColorByName(Dataset::COLOR_BLUE);

      return new Chart('doughnut', $labels, [$dataset]);
  }
}
