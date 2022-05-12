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

use Xentral\Widgets\Chart\Chart;
use Xentral\Widgets\Chart\Dataset;
use Xentral\Widgets\Chart\HtmlRenderer;
use Xentral\Widgets\Chart\PeriodMatcher;
use Xentral\Widgets\Chart\PieDataset;

class Dashboard
{
  /** @var Application $app */
  protected $app;

  const MODULE_NAME = 'Dashboard';

  /** @var array $javascript */
  public $javascript = [
    './classes/Modules/Dashboard/www/js/hammer.js-2.0.8/hammer.min.js',
    './classes/Modules/Dashboard/www/js/muuri-0.7.1/muuri.min.js',
    './classes/Modules/Dashboard/www/js/jquery.fittext.js',
    './classes/Modules/Dashboard/www/js/dashboard.js',
  ];

  /** @var array $stylesheet */
  public $stylesheet = [
    './classes/Modules/Dashboard/www/css/dashboard-relative.css',
  ];

  /**
   * Dashboard constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    if($intern){
      return;
    }

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler('list', 'DashboardList');
    $this->app->DefaultActionHandler('list');
    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
    /*$this->app->erp->CheckTable("chat");
    $this->app->erp->CheckColumn("id", "int(11)", "chat", "NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("user_from", "INT(11)", "chat", "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("user_to", "INT(11)", "chat", "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("message", "TEXT", "chat", "DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn("prio", "TINYINT(1)", "chat", "DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("zeitstempel", "DATETIME", "chat");
    $this->app->erp->CheckIndex("chat", "user_from");
    $this->app->erp->CheckIndex("chat", "user_to");*/
  }

  protected function DashboardMenu()
  {
    $this->app->erp->Headlines('Dashboard');
    $this->app->erp->MenuEintrag('index.php?module=dashboard&action=list', '&Uuml;bersicht');
  }

  public function DashboardList()
  {
    // Top 5 Artikel (90 Tage)
    $articlesRenderer = new HtmlRenderer($this->GetArticlesChart(), 'Top 5 Artikel (90 Tage)', 400, 400);
    $this->app->Tpl->Set('ARTICLECHART', $articlesRenderer->render());

    $orderChartRenderer = new HtmlRenderer($this->GetExampleChartRenderer(), 'Aufträge', 400, 400);
    $this->app->Tpl->Set('ORDERCHART', $orderChartRenderer->render());

    $this->DashboardMenu();
    $this->app->Tpl->Parse('PAGE', 'dashboard_list.tpl');
  }

  /**
   * @return Chart
   * @throws Exception
   */
  protected function GetExampleChartRenderer()
  {
    $orderCount = [
        ['month' => '02/2018', 'count' => mt_rand(0, 15)],
        ['month' => '03/2018', 'count' => mt_rand(0, 15)],
        ['month' => '04/2018', 'count' => mt_rand(0, 15)],
        ['month' => '05/2018', 'count' => mt_rand(0, 15)],
        ['month' => '06/2018', 'count' => mt_rand(0, 15)],
        ['month' => '07/2018', 'count' => mt_rand(0, 15)],
        ['month' => '08/2018', 'count' => mt_rand(0, 15)],
        ['month' => '09/2018', 'count' => mt_rand(0, 15)],
        ['month' => '10/2018', 'count' => mt_rand(0, 15)],
        ['month' => '11/2018', 'count' => mt_rand(0, 15)],
        ['month' => '12/2018', 'count' => mt_rand(0, 15)],
        ['month' => '01/2019', 'count' => mt_rand(0, 15)],
    ];

    // Daten auf Zeitachse matchen; Leere Monate werden aufgefüllt
    $begin = new DateTime(sprintf('01.01.2018'));
    $end = new DateTime('28.02.2019');
    $interval = new DateInterval('P1M');
    $period = new PeriodMatcher($begin, $end, $interval, 'm/Y');
    $labels = $period->getDates();
    $orderCountData = $period->matchData($orderCount, 'month', 'count');

    // Diagramm zusammenbauen
    $chart = new Chart('line', [], [], ['aspectRatio' => 1]);
    $chart->addLabels($labels);
    $chart->addDataset(new Dataset('Aufträge', $orderCountData));

    return $chart;
  }

  protected function GetArticlesChart()
  {
    $chartData = $this->app->DB->SelectArr(
      "SELECT 
           CONCAT(art.nummer, ' ', SUBSTRING(art.name_de, 1, 30)) AS artikel, 
           ROUND(SUM(rp.menge * rp.preis), 2) AS menge 
         FROM artikel AS art INNER JOIN rechnung_position AS rp ON art.id = rp.artikel 
         INNER JOIN rechnung AS r ON r.id = rp.rechnung
         WHERE r.status != 'angelegt' AND r.status <> 'storniert' AND r.status != 'angelegt' 
         AND DATEDIFF(NOW(), r.datum) <= 90 
         GROUP BY art.id 
         ORDER BY SUM(rp.menge * rp.preis) DESC 
         LIMIT 5"
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
