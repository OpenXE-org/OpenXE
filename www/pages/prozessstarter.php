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
include '_gen/prozessstarter.php';

class Prozessstarter extends GenProzessstarter {
  /** @var Application $app */
  var $app;
  /** @var int */
  protected $currentCronjobId;
  /** @var int */
  protected $currentCronjobLogId;
  /** @var string */
  protected $currentCronjobName;
  /** @var int */
  protected $parentId;

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public function TableSearch($app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch ($name) {
      case 'prozessstarterlist':
        $allowed['prozessstarter'] = array('list');

        // START EXTRA checkboxen
        $this->app->Tpl->Add('JQUERYREADY', "$('#nuraktiv').click( function() { fnFilterColumn1( 0 ); } );");
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


        // START EXTRA checkboxen

        // ENDE EXTRA checkboxen


        // headings

        $heading = array('','Bezeichnung', 'Art', 'Periode', 'mind. Empfohlen', 'Aktiviert', 'Laeuft', 'Letzte Ausf&uuml;hrung', 'Typ', 'Parameter','Status', 'Men&uuml;');
        $width = array('1%','15%', '10%', '10%', '10%', '10%','10%', '15%', '5%', '10%','20%', '1%');
        $findcols = array('p.id','p.bezeichnung', 'p.art', 'CAST(p.periode as SIGNED)',"recommended_period",
          "if(p.aktiv,'ja','-')", 'p.mutex', 'p.letzteausfuerhung', 'p.typ', 'p.parameter','p.status', 'p.id');
        $searchsql = array('p.bezeichnung', 'p.art', 'p.periode', 'p.aktiv', 'p.mutex',
          "date_format(p.letzteausfuerhung, '%d.%m.%Y %H:%i:%s')", 'p.typ', 'p.parameter','p.status');
        $defaultorder = 2;
        $defaultorderdesc = 0;
        $menu = '<table cellpadding=0 cellspacing=0>';
        $menu .= '<tr>';
        $menu .= '<td nowrap>';
        $menu .= '<a href="javascript:;" onclick="ProzessstarterEdit(%value%);">';
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= '</a>&nbsp;';
        $menu .= "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=prozessstarter&action=delete&id=%value%\");>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= '</a>&nbsp;';
        $menu .= "<a href=\"#\" onclick=resetDialog(%value%);>";
        $menu .= "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/play.png\" border=\"0\">";
        $menu .= '</a>&nbsp;';
        $menu .= '</td>';
        $menu .= '</tr>';
        $menu .= '</table>';
        $moreinfo = true;
        $menucol = count($heading) - 1;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS p.id,
           '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' AS open, 
                           p.bezeichnung, p.art, 
                           p.periode,
                           IF(p.recommended_period >0 AND p.art='periodisch', p.recommended_period,'-'),
                           if(p.aktiv,'ja','-') as aktiviert, if(p.mutex,
        CONCAT(
        'ja',if(p.mutexcounter > 0,concat(' (Anzahl Versuche ',p.mutexcounter,')'),'')
        ),'-') 
        as laeuft, date_format(p.letzteausfuerhung, '%d.%m.%Y %H:%i:%s') as 'letzte Ausf&uuml;hrung', 
                           p.typ, p.parameter,concat(p.status_zeit,' ',p.status), p.id 
        FROM `prozessstarter` AS `p` ";
        $where = ' p.id > 0 ';

        $more_data1 = $this->app->Secure->GetGET('more_data1');
        if ($more_data1 == 1) {
          $subwhere[] = ' p.aktiv=1  ';
        }
        if(!empty($subwhere)){
          $tmp = implode(' AND ', $subwhere);
        }
        if($tmp!=''){
          $where .= " AND ($tmp)";
        }

        //$groupby=" GROUP by z.adresse_abrechnung ";

        // gesamt anzahl

        $count = "SELECT COUNT(id) FROM prozessstarter p WHERE $where";
        break;
    }

    $erg = array();

    foreach($erlaubtevars as $k => $v)
    {
      if(isset($$v)){
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  /**
   * Prozessstarter constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ProzessstarterCreate");
    $this->app->ActionHandler("edit","ProzessstarterEdit");
    $this->app->ActionHandler("list","ProzessstarterList");
    $this->app->ActionHandler("delete","ProzessstarterDelete");
    $this->app->ActionHandler("reset","ProzessstarterReset");
    $this->app->ActionHandler("chart","ProzessstarterChart");
    $this->app->ActionHandler("minidetail","ProzessstarterMinidetail");

    $this->app->ActionHandlerListen($app);
  }

  public function Install() {
    $this->app->erp->CheckTable('cronjob_log');
    $this->app->erp->CheckColumn('parent_id','INT(11)','cronjob_log','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('cronjob_id','INT(11)','cronjob_log','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('memory_usage','INT(11)','cronjob_log','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('memory_peak','INT(11)','cronjob_log','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('cronjob_name','varchar(255)','cronjob_log','DEFAULT "" NOT NULL');
    $this->app->erp->CheckColumn('change_time','timestamp','cronjob_log','DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckColumn('status','varchar(32)','cronjob_log','DEFAULT "" NOT NULL');
    $this->app->erp->CheckIndex('cronjob_log', ['cronjob_id','change_time']);
    $this->app->erp->RegisterHook('ajax_filter_hook1','prozessstarter','ProzessstarterAjaxFilter');
    $this->app->erp->RegisterHook('systemhealth_fill_entries','prozessstarter','ProzessstarterSystemhealthFillEntries');

    $this->app->erp->CheckTable('cronjob_starter_running');
    $this->app->erp->CheckColumn('uid','VARCHAR(23)','cronjob_starter_running','NOT NULL');
    $this->app->erp->CheckColumn('active','TINYINT(1)','cronjob_starter_running','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('type','VARCHAR(10)','cronjob_starter_running','NOT NULL');
    $this->app->erp->CheckColumn('task_id','INT(11)','cronjob_starter_running','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('last_time','TIMESTAMP','cronjob_starter_running','DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckIndex('cronjob_starter_running',['uid','type']);
    if(!$this->app->erp->GetKonfiguration('prozessstarter_log_repair2')) {
      $cronjobLogs = $this->app->DB->Query('SELECT * FROM cronjob_log WHERE cronjob_id > 0 ORDER BY cronjob_id, change_time, id');
      $cronjobId = 0;
      $parentId = 0;
      while($cronjobLog = $this->app->DB->Fetch_Assoc($cronjobLogs)) {
        if($cronjobId !== $cronjobLog['cronjob_id']) {
          $cronjobId = $cronjobLog['cronjob_id'];
          $parentId = 0;
        }
        if($cronjobLog['status'] === 'start' || $cronjobLog['status'] === 'gestartet') {
          $parentId = $cronjobLog['id'];
        }
        elseif($parentId != $cronjobLog['parent_id']) {
          $this->app->DB->Update(
            sprintf(
              'UPDATE cronjob_log SET parent_id = %d WHERE id = %d',
              $parentId, $cronjobLog['id']
            )
          );
        }
      }
      $this->app->erp->SetKonfigurationValue('prozessstarter_log_repair2', 1);
    }
    $this->removeKilledCronjobs();
  }

  /**
   * @return array
   */
  public function getToFrequentlyCronjobs()
  {
    return $this->app->DB->SelectFirstCols(
      sprintf(
        "SELECT DISTINCT `parameter`
        FROM `prozessstarter` 
        WHERE `aktiv` = 1 AND `art` = 'periodisch' AND `periode` < `recommended_period` AND `recommended_period` > 0"
      )
    );
  }

  /**
   * @param array $parmeterList
   *
   * @return string
   */
  public function getFrequentlyMessage($parmeterList)
  {
    if(empty($parmeterList)) {
      return '';
    }
    if(count($parmeterList) === 1) {
      return 'Der Prozessstarter '
        .implode(', ', $parmeterList).' l&auml;uft h&auml;ufiger als empfohlen';
    }

    return 'Die Prozessstarter '
        .implode(', ', $parmeterList).' laufen h&auml;ufiger als empfohlen';
  }

  /**
   * @param int $id
   *
   * @return bool
   */
  public function resetPeriode($id = 0)
  {
    $this->app->DB->Update(
      sprintf(
        "UPDATE `prozessstarter`
        SET `periode` = `recommended_period` 
        WHERE `aktiv` = 1 AND `art` = 'periodisch' AND `periode` < `recommended_period` AND `recommended_period` > 0
            AND (%d = 0 OR `id` = %d)",
        $id, $id
      )
    );

    $ret = $this->app->DB->affected_rows() > 0;
    if($ret) {
      $this->ProzessstarterSystemhealthFillEntries();
    }

    return $ret;
  }

  public function ProzessstarterSystemhealthFillEntries()
  {
    $countToFrequently = $this->getToFrequentlyCronjobs();
    $message = $this->getFrequentlyMessage($countToFrequently);
    if(!empty($message)) {
      $message = '<a target="_blank" href="index.php?module=prozessstarter&action=list">'.$message.'</a>';
    }

    /** @var Systemhealth $systemhealth */
    $systemhealth = $this->app->loadModule('systemhealth');
    $systemhealth->changeStatus('cronjobs', 'frequently', empty($message)?'OK':'warning', $message);
  }

  /**
   * @param string $filtername
   * @param array  $newarr
   * @param string $term
   * @param string $term2
   * @param string $term3
   */
  public function ProzessstarterAjaxFilter($filtername, &$newarr, $term, $term2, $term3)
  {
    if($filtername === 'prozessstarter_aktiv'){
      $arr = $this->app->DB->SelectArr(
        "SELECT DISTINCT CONCAT(parameter,' ',bezeichnung) as `name` 
        FROM prozessstarter 
        WHERE parameter LIKE '%$term%' OR bezeichnung = '%$term%' 
        ORDER BY parameter"
      );
      if(empty($arr)) {
        return;
      }

      foreach($arr as $row) {
        $newarr[] = $row['name'];
      }
    }
  }

  /**
   * @param string $date
   * @param int    $roundMinutes
   *
   * @return string
   */
  protected function roundDate($date, $roundMinutes = 15) {
    $return = substr($date,0,14);
    $minutes = substr($date,14,2);
    $minutes = floor($minutes / $roundMinutes) * $roundMinutes;
    if(strlen($minutes) === 1) {
      $minutes = '0'.$minutes;
    }

    return $return.$minutes.':00';
  }

  /**
   * @param array  $data
   * @param string $field
   *
   * @return array
   */
  protected function seperateData($data, $field) {
    $ret = [];
    foreach($data as $row) {
      $ret['K'.$row[$field]][] = $row;
    }

    return $ret;
  }

  /**
   * @param array        $data
   * @param string       $fieldValue
   * @param string       $dateField
   * @param DateInterval $interval
   *
   * @return array
   */
  protected function roundDataDates($data, $fieldValue, $dateField, $interval) {
    $return = [];
    if(empty($data)) {
      return [];
    }
    $seconds = 60 * $interval->i;
    foreach($data as $key => $arr) {
      $cronjobIntervals = [];
      foreach($arr as $rowIndex => $row) {
        if($row[$fieldValue] > 0) {
          if(empty($cronjobIntervals) || empty($cronjobIntervals[count($cronjobIntervals) - 1])) {
            $cronjobIntervals[] = ['start' => $this->roundDate($row[$dateField]), 'startexact'=>$row[$dateField]];
          }
          $cronjobIntervals[count($cronjobIntervals) - 1]['end'] = $this->roundDate($row[$dateField], $interval->i);
          $cronjobIntervals[count($cronjobIntervals) - 1]['endexact'] = $row[$dateField];
        }
        else {
          if(empty($cronjobIntervals)) {
            continue;
          }
          if(!empty($cronjobIntervals[count($cronjobIntervals) - 1])) {
            $cronjobIntervals[count($cronjobIntervals) - 1]['end'] = $this->roundDate($row[$dateField], $interval->i);
            $cronjobIntervals[count($cronjobIntervals) - 1]['endexact'] = $row[$dateField];
          }
          $cronjobIntervals[] = [];
        }
      }
      $return[$key] = [];
      $dateToKeys = [];
      foreach($cronjobIntervals as $cronjobInterval) {
        if(empty($cronjobInterval)) {
          continue;
        }

        $start = new DateTime($cronjobInterval['start']);
        $end = new DateTime($cronjobInterval['end']);

        $val = 100* (
            (strtotime($cronjobInterval['endexact']) - strtotime($cronjobInterval['startexact'])+0.1) /
            (strtotime($cronjobInterval['start']==$cronjobInterval['end']?$cronjobInterval['start']+$seconds:$cronjobInterval['end'])
              -strtotime($cronjobInterval['start']))
          );
        if($val > 100) {
          $val = 100;
        }

        $dateFielValue = $start->format('d.m.Y H:i:s');
        if(isset($dateToKeys[$dateFielValue])) {
          $return[$key][$dateToKeys[$dateFielValue]][$fieldValue] += $val;
          if($return[$key][$dateToKeys[$dateFielValue]][$fieldValue] > 100) {
            $return[$key][$dateToKeys[$dateFielValue]][$fieldValue] = 100;
          }
        }
        else {
          $return[$key][] = [$dateField => $dateFielValue, $fieldValue => $val];
          $dateToKeys[$dateFielValue] = count($return[$key])-1;
        }
        while(strtotime($start->format('Y-m-d H:i:s')) < strtotime($end->format('Y-m-d H:i:s'))) {
          $start->add($interval);
          $dateFielValue = $start->format('d.m.Y H:i:s');
          if(isset($dateToKeys[$dateFielValue])) {
            $return[$key][$dateToKeys[$dateFielValue]][$fieldValue] += $val;
          }
          else {
            $return[$key][] = [$dateField => $dateFielValue, $fieldValue => $val];
            $dateToKeys[$dateFielValue] = count($return[$key])-1;
          }
        }
      }
    }

    return $return;
  }

  /**
   * @param array  $data
   * @param string $field
   * @param string $nameField
   *
   * @return mixed
   */
  protected function formatData($data, $field = 'running',$nameField = 'bezeichnung') {
    if(empty($data)) {
      return $data;
    }
    foreach($data as $key => $arr) {
      if(empty($arr)) {
        continue;
      }
      foreach($arr as $rowIndex => $row) {
        $data[$key][$rowIndex][$row[$nameField]] = $row[$field]?round($row[$field],2):0;//sqrt
      }
    }

    return $data;
  }

  /**
   * @param string $formelementDate
   * @param string $formelementTime
   * @param bool   $from
   *
   * @return array
   */
  public function getDatesFromForm($formelementDate, $formelementTime, $from = false) {
    $last12h = $this->app->Secure->GetPOST('last12h');
    $last6h = $this->app->Secure->GetPOST('last6h');
    if(!empty($last12h) || !empty($last6h)) {
      $diff = 12;
      if(!empty($last6h)) {
        $diff = 6;
      }
      if($from) {
        list($date, $time) = explode(' ', (new DateTime())->add(new DateInterval('PT5M'))->sub(new DateInterval('PT'.$diff.'H'))->format('d.m.Y H:i'),2);
      }
      else {
        list($date, $time) = explode(' ', (new DateTime())->add(new DateInterval('PT5M'))->format('d.m.Y H:i'),2);
      }
    }
    else{
      if(!empty($formelementDate)) {
        $date = $this->app->Secure->GetPOST($formelementDate);
      }
      else{
        $date = '';
      }
      if(!empty($formelementTime)) {
        $time = $this->app->Secure->GetPOST($formelementTime);
      }
      else{
        $time = '';
      }
    }
    if(empty($time)) {
      $time = '00:00:00';
    }
    else {
      $time = substr($time,0,5). ':00';
    }
    if(empty($date)) {
      $date = new DateTime();
      if($from) {
        $date->sub(new DateInterval('P1D'));
      }
      $dateFormated = $date->format('d.m.Y');
      $timeFormated = $date->format('H:i');
      $date = $date->format('Y-m-d H:i:s');
    }
    elseif(strpos($date,'.') !== false) {
      $dateFormated = substr($date,0, 10);
      $date = $this->app->String->Convert($dateFormated,'%1.%2.%3','%3-%2-%1').' '.$time;
      $timeFormated = substr($time,0,5);
    }
    else {
      $dateFormated = $this->app->String->Convert($date,'%3-%2-%1','%1.%2.%3').' '.$time;
      $timeFormated = substr($time,0,5);
    }

    $date = $this->roundDate($date);

    return [$date, $dateFormated, $timeFormated];
  }


  /**
   * @param string $dateFrom
   * @param string $dateTo
   * @param int    $cronjobId
   * @param int    $limit
   *
   * @return string
   */
  protected function getChart($dateFrom, $dateTo, $cronjobId = 0, $limit = 0) {
    $extra = '';
    if($cronjobId > 0) {
      $extra = sprintf(' AND p.id = %d ',$cronjobId);
    }

    /*$sql = sprintf(
      '
        SELECT DATE_FORMAT(cl2.change_time, \'%%d.%%m.%%Y %%H:%%i\') as `date`, 
        IF(cl2.status <> \'abgeschlossen\' AND SUBSTRING(cl2.status,1,5) != \'mutex\' ,1,0) as `running` ,cl2.status,cl2.cronjob_id,p.bezeichnung
        FROM (
          SELECT change_time, id ,cronjob_id
          FROM cronjob_log 
          WHERE status = \'start\'
        ) AS cl1
        INNER JOIN (
          SELECT change_time, id, status, parent_id ,cronjob_id
          FROM cronjob_log
        ) AS cl2 ON (cl1.id = cl2.parent_id OR cl1.id = cl2.id) AND cl1.cronjob_id = cl2.cronjob_id
        INNER JOIN prozessstarter AS p ON cl1.cronjob_id = p.id AND p.aktiv = 1 
        WHERE (cl2.change_time >= \'%s\' OR cl1.change_time >= \'%s\')
        AND (cl2.change_time <= \'%s\' OR cl1.change_time <= \'%s\') 
        %s
        ORDER BY cl2.cronjob_id,cl2.change_time, cl2.id
        ', $dateFrom, $dateFrom,$dateTo,$dateTo, $extra
    );*/
    $sql = sprintf(
      '
        SELECT DATE_FORMAT(cl.change_time, \'%%d.%%m.%%Y %%H:%%i\') as `date`, 
        IF(cl.status <> \'abgeschlossen\' AND SUBSTRING(cl.status,1,5) != \'mutex\' ,1,0) as `running` ,
        cl.status,cl.cronjob_id,p.bezeichnung
        FROM cronjob_log as cl
        INNER JOIN prozessstarter AS p ON cl.cronjob_id = p.id AND p.aktiv = 1  
        WHERE cl.change_time >= \'%s\'
        AND cl.change_time <= \'%s\'  
        %s
        ORDER BY cl.cronjob_id,cl.change_time, cl.id
        ', (new DateTime($dateFrom))->sub(new DateInterval( 'P1D'))->format('Y-m-d H:i:s') ,
      $dateTo, $extra
    );
    $cronjobArr = $this->app->DB->SelectArr($sql);
    $begin = new DateTime($dateFrom);
    if($dateFrom === $dateTo) {
      $dateTo = new DateTime($dateFrom);
      $dateTo = $dateTo->add(new DateInterval( 'P1D'))->format('Y-m-d H:i:s');
    }

    $end = new DateTime($dateTo);

    list($months, $days, $hours) = explode(' ',$end->diff($begin)->format('%m %d %h'));
    $days += $months * 30;
    if($days === 0) {
      $interval = new DateInterval('PT1M');
    }
    elseif($days <= 1) {
      $interval = new DateInterval('PT5M');
    }
    else {
      $interval = new DateInterval('PT15M');
    }
    foreach($cronjobArr as $row) {
      $cronjobIdToName['K'.$row['cronjob_id']] = $row['bezeichnung'];
    }

    $seperated = $this->seperateData($cronjobArr, 'cronjob_id');
    $seperated = $this->roundDataDates($seperated, 'running','date', $interval);

    foreach($seperated as $cronjobIdKey => $rows) {
      foreach($rows as $rowKey => $row) {
        $seperated[$cronjobIdKey][$rowKey]['bezeichnung'] = $cronjobIdToName[$cronjobIdKey];
      }
    }

    $seperated = $this->formatData($seperated,'running');

    $sorted = [];
    foreach($seperated as $cronjobIdKey => $rows) {
      $maxValue = 0;
      foreach($rows as $element) {
        if($element['running'] > $maxValue) {
          $maxValue = $element['running'];
        }
      }
      $sorted[$cronjobIdKey] = $maxValue;
    }
    array_multisort($sorted,SORT_DESC,$seperated);
    $matchedDatas = [];
    $period = new PeriodMatcher($begin, $end, $interval, 'd.m.Y H:i:s');
    $maxCronjobs = $limit === 0?999999:$limit;
    foreach($seperated as $cronjobIdKey => $cronjobRow) {
      $maxCronjobs--;
      if($maxCronjobs < 0) {
        unset($seperated[$cronjobIdKey]);
        continue;
      }
      $matchedDatas[$cronjobIdKey] = $period->matchData(
        $cronjobRow,
        'date'
        ,$cronjobIdToName[$cronjobIdKey]
      );
    }

    $labels = $period->getDates();
    $options = [];
    $chart = new Chart('line',[],[],$options);
    $chart->addLabels($labels);
    $first = true;
    foreach($seperated as $cronjobIdKey => $matchedData) {
      if($first) {
        $first = false;
        $chart->addDatasetAsYAxis(
          new Dataset($cronjobIdToName[$cronjobIdKey],
            $matchedDatas[$cronjobIdKey]), 'left' );//,'logarithmic'
      }
      else {
        $chart->addDataset(new Dataset($cronjobIdToName[$cronjobIdKey], $matchedDatas[$cronjobIdKey]));
      }
    }

    $chartRenderer = new HtmlRenderer($chart, 'Laufzeiten', 400, 150);

    return $chartRenderer->render();
  }

  public function ProzessstarterChart() {
    $this->ProzessstarterMenu();

    list($dateFrom, $dateFromTpl, $dateFromTime) =
      $this->getDatesFromForm('prozessstarter_chart_from','prozessstarter_chart_fromtime',true);
    list($dateTo, $dateToTpl, $dateToTime) =
      $this->getDatesFromForm('prozessstarter_chart_to','prozessstarter_chart_totime',false);

    $cronjobFilter = $this->app->Secure->GetPOST('prozessstarter_chart_cronjob');
    $this->app->Tpl->Set('PROZESSSTARTER_CHART_FROM',$dateFromTpl);
    $this->app->Tpl->Set('PROZESSSTARTER_CHART_TO',$dateToTpl);
    $this->app->Tpl->Set('PROZESSSTARTER_CHART_FROMTIME',$dateFromTime);
    $this->app->Tpl->Set('PROZESSSTARTER_CHART_TOTIME',$dateToTime);
    $this->app->Tpl->Set('PROZESSSTARTER_CHART_CRONJOB',$cronjobFilter);

    if(!empty($cronjobFilter)) {
      $cronjobFilter = $this->app->DB->Select(
        sprintf(
          'SELECT id FROM prozessstarter WHERE parameter = \'%s\''
          ,$cronjobFilter
        )
      );
    }
    $limit = $this->app->Secure->GetPOST('prozessstarter_chart_limit')?4:0;
    if($limit > 0) {
      $this->app->Tpl->Set('PROZESSSTARTER_CHART_LIMIT',' checked="checked" ');
    }

    $this->app->Tpl->Set('TAB1', $this->getChart($dateFrom, $dateTo, $cronjobFilter, $limit));

    $this->app->YUI->DatePicker('prozessstarter_chart_from');
    $this->app->YUI->DatePicker('prozessstarter_chart_to');
    $this->app->YUI->TimePicker('prozessstarter_chart_fromtime');
    $this->app->YUI->TimePicker('prozessstarter_chart_totime');
    $this->app->YUI->AutoComplete('prozessstarter_chart_cronjob','prozessstarter_aktiv',1);
    $this->app->Tpl->Parse('PAGE','prozessstarter_chart.tpl');
  }

  public function ProzessstarterMinidetail() {
    $cronjobId = (int)$this->app->Secure->GetGET('id');
    $dateFrom = $this->getDatesFromForm('','',true);
    $dateFrom = reset($dateFrom);
    $dateTo = $this->getDatesFromForm('','',false);
    $dateTo = reset($dateTo);
    echo $this->getChart($dateFrom, $dateTo, $cronjobId);
    echo '<script type="text/javascript" src="./js/chart-helper.js?v=2" ></script>';

    $lastCronjobLogs = $this->app->DB->SelectFirstCols(
      sprintf(
        'SELECT `id` FROM `cronjob_log` WHERE `cronjob_id` = %d AND `parent_id` = 0 ORDER BY `id` DESC LIMIT 5',
        $cronjobId
      )
    );
    if(!empty($lastCronjobLogs)) {
      $table = new EasyTable($this->app);
      $sql =         sprintf(
        "SELECT DATE_FORMAT(cl2.`change_time`,'%%d.%%m.%%Y %%H:%%i:%%s') AS `Datum`, 
       IF(cl.id=cl2.id,CONCAT('<b>',cl2.status,'</b>'), cl2.`status`) AS `Status` 
          FROM `cronjob_log` AS `cl`
          INNER JOIN `cronjob_log` AS `cl2` ON (cl.id = cl2.parent_id AND cl2.parent_id IN (%s)) 
                                                 OR (cl.id = cl2.id AND cl2.id IN (%s)) 
          WHERE cl.`id` IN (%s)
          ORDER BY cl.`id` DESC, cl2.id",
        implode(',', $lastCronjobLogs),
        implode(',', $lastCronjobLogs),
        implode(',', $lastCronjobLogs)
      );
      $table->Query($sql);
      $table->width_headings = ['20%'];
      echo $table->DisplayNew('return','Status','noAction');
    }
    $this->app->erp->ExitWawi();
  }

  /**
   * @param string   $status
   * @param null|int $prozessstarter
   */
  public function setCronjobStatus($status, $prozessstarter = null)
  {
    if($prozessstarter !== null) {
      $this->currentCronjobId = $prozessstarter;
      if(emptY($this->currentCronjobId)) {
        $this->parentId = 0;
        $this->currentCronjobName = '';
      }
    }

    if(!$this->currentCronjobId){
      return;
    }
    if($status === 'gestartet' || $status === 'start') {
      $this->currentCronjobName = $this->app->DB->Select(
        sprintf(
          'SELECT `parameter` FROM `prozessstarter` WHERE `id` = %d LIMIT 1',
          $this->currentCronjobId
        )
      );
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `cronjob_log`
            (`cronjob_id`, `cronjob_name`, `status`,`change_time`,`memory_usage`,`memory_peak`)
          VALUES (%d, '%s', 'start',now(),%d,%d)",
          $this->currentCronjobId, $this->currentCronjobName,
          memory_get_usage()/1024, memory_get_peak_usage()/1024
        )
      );
      $this->parentId = $this->app->DB->GetInsertID();
    }
    else {
      if(empty($this->currentCronjobName)) {
        $this->currentCronjobName = $this->app->DB->Select(
          sprintf(
            'SELECT `parameter` FROM `prozessstarter` WHERE `id` = %d LIMIT 1',
            $this->currentCronjobId
          )
        );
      }
      if(empty($this->parentId)) {
        $this->parentId = (int)$this->app->DB->Select(
          sprintf(
            'SELECT `id` 
            FROM `cronjob_log` 
            WHERE status = \'start\' AND `cronjob_id` = %d 
            ORDER BY `change_time` DESC, `id` DESC 
            LIMIT 1',
            $this->currentCronjobId
          )
        );
      }
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `cronjob_log` 
         (`parent_id`, `cronjob_id`, `cronjob_name`,`status`,`change_time`,`memory_usage`,`memory_peak`)
          VALUES (%d, %d, '%s', '%s',now(),%d,%d)",
          $this->parentId, $this->currentCronjobId, $this->currentCronjobName, $status,
          memory_get_usage()/1024, memory_get_peak_usage()/1024
        )
      );
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE `prozessstarter` SET `status` = '%s', 
        `status_zeit` = now() WHERE `id` = %d LIMIT 1",
        $this->app->DB->real_escape_string($status), $this->currentCronjobId
      )
    );
  }

  public function ProzessstarterReset(){
    $id = (int)$this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->DB->Update(
        sprintf('UPDATE prozessstarter SET letzteausfuerhung=\'0000-00-00 00:00:00\'  WHERE id = %s', $id)
      );
    }

    $data = ['success' => true];
    echo json_encode($data);
    $this->app->ExitXentral();
  }

  public function ProzessstarterDelete()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->DB->Delete(
        sprintf(
          'DELETE FROM `prozessstarter` WHERE `id` = %d LIMIT 1',
          $id
        )
      );
      $this->app->DB->Update(
        sprintf(
          'UPDATE cronjob_starter_running SET `active` = -1 WHERE `task_id` = %d AND `active` = 1 ',
          $id
        )
      );
      $this->ProzessstarterSystemhealthFillEntries();
    }

    $this->app->Location->execute('index.php?module=prozessstarter&action=list');
  }

  public function ProzessstarterCreate()
  {
    $this->ProzessstarterMenu();
    parent::ProzessstarterCreate();
  }

  public function ProzessstarterList()
  {
    if($this->app->Secure->GetPOST('resetperiode')) {
      $this->resetPeriode();
      $this->app->Location->execute('index.php?module=prozessstarter&action=list');
    }
    if($this->app->Secure->GetPOST('resetcronjob')) {
      $this->resetCronjobs(60);
    }
    $this->ProzessstarterMenu();
    $letzteraufruf = $this->app->erp->GetKonfiguration('prozessstarter_letzteraufruf');
    $diff = time() - strtotime($letzteraufruf);
    if($diff > 60*5+1) // mit sicherheitsabstand :-)
    {
      $this->app->Tpl->Set('MESSAGE','<div class="error">Es sieht so aus, als ob der Prozessstarter Cronjob nicht regelm&auml;&szlig;ig ausgef&uuml;hrt wird! Bitte aktivieren Sie diesen (<a href="http://helpdesk.wawision.de/doku.php?id=entwickler:grundinstallation#einrichten_des_heartbeat-cronjobs_optional" target="_blank">Link zu Helpdesk</a>)!</div>');
    }
    if($this->app->DB->Select(
      "SELECT id FROM prozessstarter WHERE aktiv = 1 AND parameter = 'autoversand' LIMIT 1"
    )) {
      if($this->app->DB->Select(
        "SELECT id FROM prozessstarter WHERE aktiv = 1 AND parameter = 'autoversand' AND periode < 30 AND art = 'periodisch' LIMIT 1"
      ))
      {
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="error">Der Prozess Autoversand ist auf eine Periodendauer von unter 30 Minuten eingestellt. Deshalb wird er nicht ausgef&uuml;hrt. Eine zu niedrige Einstellung verursacht eine zu hohe Serverlast</div>'
        );
      }
      else {
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="warning">Der Prozess Autoversand kann eine hohe Serverlast verursachen. Es ist ratsam die Art auf Uhrzeit oder die Periode auf eine hohe Zahl zu stellen.</div>'
        );
      }      
    }
    
    $system_cronjob_memory_limit = (string)$this->app->erp->GetKonfiguration('system_cronjob_memory_limit');
    $system_cronjob_max_execution_time = (string)$this->app->erp->GetKonfiguration('system_cronjob_max_execution_time');
    if($system_cronjob_memory_limit !== '') {
      if((int)$system_cronjob_memory_limit > 0 && (int)$system_cronjob_memory_limit <  256 * 1024 * 1024) {
        $this->app->Tpl->Add('MESSAGE','<div class="error">Es steht zu wenig Arbeitsspeicher zur Verf&uuml;gung ('.(round($system_cronjob_memory_limit / 1024 / 1024)).' MB). Dies kann zu Problemen bei den Prozessstartern f&uuml;hren. Technische Info: Bitte passen Sie f&uuml;r den PHP CLI Prozess den Arbeitsspeicher an (z.B. 256 MB oder 512 MB).</div>');
      }
    }
    if($system_cronjob_max_execution_time !== '' && (int)$system_cronjob_max_execution_time > 0
      && (int)$system_cronjob_max_execution_time < 300) {
      if($system_cronjob_max_execution_time <= 30) {
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="error">Die Prozessstarterlaufzeit betr&auml;gt nur '.$system_cronjob_max_execution_time.' Sekunden.</div>'
        );
      }
      else{
        $this->app->Tpl->Add(
          'MESSAGE',
          '<div class="warning">Die Prozessstarterlaufzeit betr&auml;gt nur '.$system_cronjob_max_execution_time.' Sekunden.</div>'
        );
      }
    }
    
    if($this->app->Secure->GetPOST('deaktivieren')) {
      $this->app->erp->SetCheckCronjob(false);
    }
    elseif($this->app->Secure->GetPOST('aktivieren')) {
      $this->app->erp->SetCheckCronjob(true);
      $this->app->Tpl->Add('MESSAGE','<div class="info">{|Prozessstarter wurden global aktiviert!|}</div>');
    }
    $maintenance = $this->app->erp->GetKonfiguration('update_maintenance') == '1';
    if($maintenance) {
      $this->app->erp->SetKonfigurationValue('update_maintenance', 0);
      //$this->app->Tpl->Add('MESSAGE','<div class="error">{|Xentral befindet sich im Updatemodus: Prozessstarter sind deaktiviert!|}</div>');
    }

    if(method_exists($this->app->erp, 'CheckCronjob')) {
      try {
        $checkCronjob = $this->app->erp->CheckCronjob(false);
      }
      catch (Exception $e) {
        $checkCronjob = $this->app->erp->CheckCronjob();
      }

      if($checkCronjob) {
        $this->app->Tpl->Set('CRONJOBBUTTON','<input type="submit" class="btnBlueNew" name="deaktivieren" value="{|Prozessstarter global deaktivieren|}" />');
      }
      else {
        $this->app->Tpl->Add('MESSAGE', '<div class="error">{|Achtung: Prozessstarter sind global deaktiviert!|}</div>');
        $this->app->Tpl->Set('CRONJOBBUTTON', '<input type="submit" class="btnBlueNew" name="aktivieren" value="{|Prozessstarter global aktivieren|}" />');
      }
    }

    list($runningTime, $runningPeriod) = $this->getRunningCronjobs(60);
    if($runningPeriod + $runningTime > 0){
      $this->app->Tpl->Add(
        'MESSAGE',
        '<form method="POST"><div class="warning">Es laufen '.
        ($runningPeriod+ $runningTime).
        ' Prozessstarter bereits mehr als eine Stunde. <input type="submit" name="resetcronjob" value="{|zur&uuml;cksetzen|}" /></div></form>'
      );
    }

    if(!empty($this->app->Secure->GetGET('id'))){
      $this->app->Tpl->Add(
        'JAVASCRIPT',
        'window.onload=function(){ ProzessstarterEdit(' . $this->app->Secure->GetGET('id') . ');};'
      );
    }

    $countToFrequently = $this->getToFrequentlyCronjobs();
    $message = $this->getFrequentlyMessage($countToFrequently);
    if(!empty($message)) {
      $this->app->Tpl->Add(
        'MESSAGE',
        '<div class="warning">
          <form method="post">'
            .$message.' <input type="submit" name="resetperiode" value="zur&uuml;cksetzen" />
          </form>
        </div>'
      );
    }

    $this->app->YUI->TableSearch('TAB1','prozessstarterlist', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','prozessstarter_list.tpl');
  }

  public function ProzessstarterMenu()
  {
    $this->app->erp->Headlines('Prozessstarter');
    $this->app->erp->MenuEintrag('index.php?module=prozessstarter&action=list','&Uuml;bersicht');
    $this->app->erp->MenuEintrag('ProzessstarterEdit(0)','Neuer Eintrag');
    if($this->app->Secure->GetGET('action')==='list'){
      $this->app->erp->MenuEintrag('index.php?module=einstellungen&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    else {
      $this->app->erp->MenuEintrag('index.php?module=prozessstarter&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    }
    $this->app->erp->MenuEintrag('index.php?module=prozessstarter&action=chart','Laufzeiten');
  }

  public function ProzessstarterEdit()
  {
    $this->ProzessstarterMenu();
    //parent::ProzessstarterEdit();
    if($this->app->Secure->GetGET('cmd')==='get'){
      $id = (int)$this->app->Secure->GetPOST('id');
      
      $data = $this->app->DB->SelectRow(
        "SELECT p.id, p.bezeichnung, p.art, p.art_filter, p.startzeit, p.letzteausfuerhung, p.periode, 
          p.typ, p.parameter, p.aktiv 
        FROM prozessstarter p 
        WHERE p.id = '$id' 
        LIMIT 1"
      );
      
      if(empty($data)){
        $data['id'] = 0;
        $data['bezeichnung'] = '';
        $data['art'] = '';
        $data['art_filter'] = '';
        $data['startzeit'] = '';
        $data['letzteausfuerhung'] = '';
        $data['periode'] = '';
        $data['typ'] = '';
        $data['parameter'] = '';
        $data['aktiv'] = 0;

      }
      echo json_encode($data);
      $this->app->ExitXentral();
    }
    if($this->app->Secure->GetGET('cmd')==='save'){
      $this->ProzessstarterSave();
    }

    $this->app->Tpl->Parse('PAGE', 'prozessstarter_list.tpl');
  }


  protected function ProzessstarterSave()
  { 
    $id = (int)$this->app->Secure->GetPOST('id');
    $bezeichnung = trim($this->app->Secure->GetPOST('bezeichnung'));
    $art = trim($this->app->Secure->GetPOST('art'));
    $art_filter = trim($this->app->Secure->GetPOST('art_filter'));
    $startzeit = trim($this->app->Secure->GetPOST('startzeit'));
    $letzteausfuerhung = trim($this->app->Secure->GetPOST('letzteausfuerhung'));
    $periode = trim($this->app->Secure->GetPOST('periode'));
    $typ = trim($this->app->Secure->GetPOST('typ'));
    $parameter = trim($this->app->Secure->GetPOST('parameter'));
    $aktiv = $this->app->Secure->GetPOST('aktiv');

    $error = '';

    if($bezeichnung == ''){
      $error .= "Bitte Bezeichnung ausfüllen\n";
    }

    if($parameter == ''){
      $error .= "Bitte Parameter ausfüllen"."\n";
    }

    if($art === 'uhrzeit'){
      $periode = '';      
    }

    if(!empty($error)){
      echo json_encode(array('status'=>0,'statusText'=>$error));
      $this->app->ExitXentral();
    }

    if($id) {
      if($this->app->DB->Select(sprintf('SELECT id FROM prozessstarter WHERE id = %d AND mutex = 1 LIMIT 1', $id)) || !$aktiv){
        $this->app->DB->Update(
          sprintf(
            'UPDATE cronjob_starter_running SET `active` = -1 WHERE task_id = %d AND `active` = 1 ',
            $id
          )
        );
      }

      $this->app->DB->Update(
        "UPDATE prozessstarter 
        SET mutex =0, mutexcounter = 0, bezeichnung = '$bezeichnung', art = '$art', art_filter = '$art_filter', 
            startzeit = '$startzeit', letzteausfuerhung = '$letzteausfuerhung', periode = '$periode', 
            typ = '$typ', parameter = '$parameter', aktiv = '$aktiv' 
        WHERE id = '$id'"
      );
      $this->ProzessstarterSystemhealthFillEntries();
      echo json_encode(array('status'=>1));
      $this->app->ExitXentral();
    }
    $this->app->DB->Insert(
      "INSERT INTO prozessstarter 
        (bezeichnung, art, art_filter, startzeit, letzteausfuerhung, periode, typ, parameter, aktiv) 
      VALUES 
       ('$bezeichnung', '$art', '$art_filter', '$startzeit', '$letzteausfuerhung', '$periode', '$typ', '$parameter', '$aktiv')"
    );

    echo json_encode(array('status'=>1));
    $this->app->ExitXentral();
  }

  /**
   * @param string $uid
   * @param array  $task
   *
   * @return bool
   */
  public function checkCronjobRunning($uid, $task) {
    $this->removeKilledCronjobs();

    $this->app->DB->Delete(
      'DELETE FROM cronjob_starter_running WHERE last_time < DATE_SUB(NOW(), INTERVAL 6 HOUR) AND `active` = 1'
    );

    if($task['art'] === 'uhrzeit') {
      return true;
    }
    if(
      $this->app->DB->Select(
        sprintf(
          'SELECT id FROM cronjob_starter_running WHERE `active` = 1 AND uid = \'%s\' LIMIT 1',
          $this->app->DB->real_escape_string($uid)
        )
      )
    ) {
      return true;
    }
    $crojobLimit = $this->app->erp->Firmendaten('cronjob_limit');
    if($crojobLimit <= 0) {
      $crojobLimit = 3;
    }

    list($runningTime, $runningPeriod) = $this->getRunningCronjobs();
    return $runningPeriod + $runningTime < $crojobLimit;
  }

  /**
   * @param array $output
   * @param array $task
   */
  public function checkCronjobMemory($output, $task)
  {
    if(empty($output)) {
      return;
    }

    foreach($output as $row) {
      if(strpos($row, 'Segmentation fault')) {
        try {
          /** @var Systemhealth $systemhealth */
          $systemhealth = $this->app->erp->LoadModul('systemhealth');
          if(!empty($systemhealth) && method_exists($systemhealth,'createEntryIfError')) {
            $systemhealth->createEntryIfError(
              'cronjobs','segmentation_fault','Segmentation Fault Fehler','error', $row, true
            );
            $systemhealth->createEvent('cronjobs','segmentation_fault','error','prozessstarter',$task['id']);
          }
        }
        catch(Exception $e) {

        }
        break;
      }
    }

    foreach($output as $row)  {
      if(strpos($row, 'Out of memory') === false) {
        continue;
      }
      try {
        /** @var Systemhealth $systemhealth */
        $systemhealth = $this->app->erp->LoadModul('systemhealth');
        if(empty($systemhealth) || !method_exists($systemhealth, 'changeStatus')) {
          return;
        }
        $systemhealth->changeStatus('cronjobs', 'out_of_memory', 'warning', $task['bezeichnung'] . ': ' . $row);
        $systemhealth->createEvent('cronjobs','out_of_memory','warning','prozessstarter',$task['id']);
      }
      catch(Exception $e) {

      }
      return;
    }
  }

  /**
   * @param string|int $uid
   * @param array      $task
   */
  public function checkIfCronjobRunning($uid, $task)
  {
    $runningTask = $this->getCronjobByUid($uid);
    if(empty($runningTask) || empty($runningTask['task_id']) || $runningTask['active'] != 1
      || $runningTask['task_id'] != $task['id']
    ) {
      return;
    }
    $this->app->erp->LogFile(
      sprintf(
        'Cronjob %s with pid: %s and taskid: %d cronjob_starter_running_id %d was not closed cleanly by starter-proccess.',
        $task['bezeichnung'], $uid, $task['id'], $runningTask['id']
      )
    );
    $this->setCronjobStatus('error', $task['id']);
    $this->setCronjobRunning($uid, $task['id'], false);
  }

  /**
   * @param string|int $uid
   * @param bool       $fromStarter2
   */
  public function closeAndLogCronjob($uid, $fromStarter2 = false)
  {
    $cronjob = $this->getCronjobByUid($uid);
    if(empty($cronjob)) {
      return;
    }

    if($fromStarter2) {
      $this->app->erp->LogFile('Cronjob '.$cronjob['cronjob_name'].' called exit uid: '.$uid);
      if(!empty($cronjob['task_id'])) {
        $this->setCronjobStatus('error', $cronjob['task_id']);
        $this->setCronjobRunning($uid, $cronjob['task_id'], false);
      }
      return;
    }
    $this->app->erp->LogFile('Cronjob with uid: '.$uid.' was killed by module: '.$cronjob['cronjob_name']);
    if(!empty($cronjob['task_id'])) {
      $this->setCronjobStatus('error', $cronjob['task_id']);
      try {
        /** @var Systemhealth $systemhealth */
        $systemhealth = $this->app->erp->LoadModul('systemhealth');
        if($systemhealth !== null && method_exists($systemhealth, 'createEvent')){
          $systemhealth->createEvent('cronjobs', 'errors', 'warning', 'prozessstarter', $cronjob['task_id']);
        }
      }
      catch (Exception $d) {

      }
    }
    $this->setCronjobRunning($uid, null,false);
  }

  public function removeKilledCronjobs()
  {
    if(!function_exists('posix_getsid') ||
      !function_exists('getmypid') ||
      empty(posix_getsid(getmypid()))){
      return;
    }
    foreach ($this->getAllRunningCronjobs() as $cronjob) {
      if(!is_numeric($cronjob['uid']) || $cronjob['uid'] <= 0) {
        continue;
      }
      if(!posix_getsid($cronjob['uid'])){
        $this->app->DB->Update(
          sprintf(
            'UPDATE `cronjob_starter_running` SET `active` = -1 WHERE `id` = %d AND `active` = 1',
            $cronjob['id']
          )
        );
        if($this->app->DB->affected_rows() > 0) {
          $this->app->erp->LogFile(
            $this->app->DB->real_escape_string(
              'Cronjob '.(
                $this->app->DB->Select(
                  sprintf(
                    'SELECT bezeichnung FROM prozessstarter WHERE id = %d LIMIT 1 ',
                    $cronjob['task_id']
                  )
                )
              ).' with pid: '.
              $cronjob['uid'].' and taskid: '.
              $cronjob['task_id'].' cronjob_starter_running_id '.
              $cronjob['id'].' was not closed cleanly by starter-proccess.'
            )
          );
        }
      }
      elseif($cronjob['task_id'] > 0) {
        $this->app->DB->Update(
          sprintf(
            'UPDATE prozessstarter 
            SET mutexcounter = 1, letzteausfuerhung = NOW() 
            WHERE mutexcounter > 1 AND aktiv = 1 AND id = %d AND letzteausfuerhung > DATE_SUB(NOW(), INTERVAL 1 HOUR)',
            $cronjob['task_id']
          )
        );
        $this->app->DB->Update(
          sprintf(
            'UPDATE prozessstarter 
            SET mutexcounter = 1, letzteausfuerhung = NOW() 
            WHERE mutexcounter > 4 AND aktiv = 1 AND id = %d AND letzteausfuerhung > DATE_SUB(NOW(), INTERVAL 5 MINUTE)',
            $cronjob['task_id']
          )
        );
      }
    }
  }

  /**
   * @param int $minutes
   *
   * @return int
   */
  protected function resetCronjobs($minutes = 0) {
    $this->app->DB->Update(
      sprintf(
        'UPDATE `cronjob_starter_running` 
        SET `active` = -1 
        WHERE `active` = 1 AND `last_time` < DATE_SUB(NOW(), INTERVAL %d MINUTE)',
        (int)$minutes
      )
    );
    return (int)$this->app->DB->affected_rows();
  }

  /**
   * @return array
   */
  public function getAllRunningCronjobs()
  {
    $runningTasks = $this->app->DB->SelectArr(
      'SELECT `uid`, `type`, `id`, `task_id` 
        FROM `cronjob_starter_running` 
        WHERE `active` = 1'
    );
    if(empty($runningTasks)) {
      return [];
    }

    return $runningTasks;
  }

  /**
   * @param string|int $uid
   *
   * @return array
   */
  public function getCronjobByUid($uid)
  {
    $tasks = $this->app->DB->SelectRow(
      sprintf(
        'SELECT csr.`uid`, csr.`type`, csr.`id`, csr.`task_id`, csr.`active`, p.bezeichnung AS `cronjob_name` 
        FROM `cronjob_starter_running` AS csr
        LEFT JOIN `prozessstarter` AS p ON csr.`task_id` = p.`id`
        WHERE csr.`uid` = \'%s\' AND csr.`uid` <> \'\'',
        $this->app->DB->real_escape_string($uid)
      )
    );
    if(empty($tasks)) {
      return [];
    }

    return $tasks;
  }


  /**
   * @param int $minutes
   *
   * @return array
   */
  public function getRunningCronjobs($minutes = 0) {
    if($minutes <= 0){
      $runningTasks = $this->app->DB->SelectArr(
        'SELECT COUNT(DISTINCT uid) AS co, type 
        FROM cronjob_starter_running 
        WHERE `active` = 1 
        GROUP BY type'
      );
    }
    else {
      $runningTasks = $this->app->DB->SelectArr(
        sprintf(
          'SELECT COUNT(DISTINCT uid) AS co, type 
          FROM cronjob_starter_running 
          WHERE `active` = 1 AND last_time < DATE_SUB(NOW(), INTERVAL %d MINUTE) 
          GROUP BY `type`',
          (int)$minutes
        )
      );
    }
    if(empty($runningTasks)) {
      return [0, 0];
    }
    $runningTime = 0;
    $runningPeriod = 0;
    foreach($runningTasks as $runningTask) {
      if($runningTask['type'] === 'uhrzeit') {
        $runningTime = (int)$runningTask['co'];
      }
      elseif($runningTask['type'] === 'periodisch') {
        $runningPeriod = (int)$runningTask['co'];
      }
    }

    return [$runningTime, $runningPeriod];
  }

  /**
   * @return int|string|null
   */
  public function getNewUid()
  {
    $uid = function_exists('getmypid')?getmypid():null;
    if(!empty($uid) &&
      !$this->app->DB->Select(
        sprintf(
          'SELECT `id` FROM `cronjob_starter_running` WHERE `active` = 1 AND `uid` = \'%s\'',
          $this->app->DB->real_escape_string($uid)
        )
      )
    ) {
      $this->changeUid($uid);
      
      return $uid;
    }

    return uniqid('', false);
  }

  /**
   * @param string $uid
   */
  public function changeUid($uid)
  {
    $this->app->DB->Update(
      sprintf(
        'UPDATE `cronjob_starter_running`
             SET `uid` = \'%s\' WHERE `uid` = \'%s\'',
        $this->app->DB->real_escape_string($uid.uniqid('', false)),$this->app->DB->real_escape_string($uid)
      )
    );
  }

  /**
   * @param int|string $uid
   * @param null       $task
   * @param bool       $active
   */
  public function setCronjobRunning($uid, $task = null, $active = true) {
    if($active === false) {
      if(!empty($task)) {
        $this->app->DB->Update(
          sprintf(
            'UPDATE cronjob_starter_running SET `task_id` = 0, last_time = NOW() WHERE uid = \'%s\' AND `active` = 1',
            $this->app->DB->real_escape_string($uid)
          )
        );
        return;
      }
      $this->app->DB->Update(
        sprintf(
          'UPDATE cronjob_starter_running SET `active` = 0, last_time = NOW() WHERE uid = \'%s\'',
          $this->app->DB->real_escape_string($uid)
        )
      );
      if(is_numeric($uid)){
        $this->changeUid($uid);
      }
      return;
    }
    $check = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM cronjob_starter_running WHERE uid = \'%s\' LIMIT 1',
        $this->app->DB->real_escape_string($uid)
      )
    );
    if(empty($check)) {
      $this->app->DB->Insert(
        sprintf(
          'INSERT INTO `cronjob_starter_running` (`active`, `last_time`, `type`, `uid`, `task_id`) 
            VALUES (1, NOW(), \'%s\', \'%s\', %d)',
          $this->app->DB->real_escape_string($task['art']), $this->app->DB->real_escape_string($uid), (int)$task['id']
        )
      );
      return;
    }
    if(!empty($check['task_id']) && ($uid != $check['uid'] || $check['task_id'] != $task['id'])) {
      $this->app->erp->LogFile(
        $this->app->DB->real_escape_string(
          sprintf(
            'Cronjob: %s id: %d was not cleanly closed.',
            $this->app->DB->Select(
              sprintf(
                'SELECT bezeichnung FROM prozessstarter WHERE id = %d LIMIT 1',
                $task['id']
              )
            ) ,
            $task['id']
          )
        )
      );
    }
    $this->app->DB->Update(
      sprintf(
        'UPDATE `cronjob_starter_running` SET `active` = 1, `last_time` = NOW(), `type` = \'%s\', task_id = %d WHERE id = %d LIMIT 1',
        $this->app->DB->real_escape_string($task['art']), (int)$task['id'],(int)$check['id']
      )
    );
    $this->app->DB->Delete(
      sprintf(
        'DELETE FROM `cronjob_starter_running` WHERE `id` <> %d AND `uid` = \'%s\'',
        $check['id'], $this->app->DB->real_escape_string($uid)
      )
    );
  }

    /**
     * @param string $parameter
     * @param string $target
     * @param bool   $withForm
     */
  public function checkActiveCronjob($parameter, $target = 'MESSAGE', $withForm = true)
  {
    if(empty($parameter)) {
      return;
    }

    $hasPermission = $this->app->erp->RechteVorhanden('prozessstarter','edit');

    $activated = false;
    if($hasPermission === true && $this->app->Secure->GetPOST('activatecronjob')) {
      $cronjobId = (int)$this->app->Secure->GetPOST('cronjobid');
      $this->app->DB->Update(
        sprintf(
          "UPDATE prozessstarter SET aktiv = 1 WHERE id = %d AND parameter = '%s'",
          $cronjobId, $this->app->DB->real_escape_string($parameter)
        )
      );
      if($this->app->DB->affected_rows() > 0) {
        $activated = true;
      }
    }
    $cronjob = $this->app->DB->SelectRow(
      sprintf(
        "SELECT p.id, p.aktiv, p.bezeichnung
        FROM prozessstarter as p 
        WHERE p.parameter = '%s' 
        ORDER BY aktiv = 1 DESC 
        LIMIT 1",
        $this->app->DB->real_escape_string($parameter)
      )
    );

    $message = '';

    if(empty($cronjob)) {
        $link = '';
        if($hasPermission === true) {
            $link = '<a href="?module=prozessstarter&action=list">Zu Prozessstartern</a>';
        }
        $message = sprintf(
            '<div class="error">Der Prozessstarter %s existiert nicht. %s</div>',
            $this->app->DB->real_escape_string($parameter),
            $link
        );
        $this->app->Tpl->Add($target, $message);

        return;
    }
    if(empty($cronjob['aktiv'])) {
      $button = '';
      if($hasPermission && $withForm) {
        $button = sprintf('<input type="hidden" name="cronjobid" value="%d" />
        <input type="submit" name="activatecronjob" value="aktivieren" />', $cronjob['id']);
      }

      $this->app->Tpl->Add(
        $target,
        ($withForm?'<form method="post"> ':'').
        sprintf(
          '
          <div class="warning">Der Prozessstarter %s ist nicht aktiv. 
          %s
          </div>
        ',
          $cronjob['bezeichnung'],
          $button
        ).
        ($withForm?'</form>':'')
      );

      return;
    }

    if (!$this->isCronjobRunningRegular($cronjob['id'])) {
        $message = sprintf(
            '<div class="error">Es sieht so aus, als ob der Prozessstarter "%s" nicht regelmäßig
                    ausgeführt wird! Bitte aktivieren Sie diesen (<a href="http://helpdesk.wawision.de/doku.php?
                    id=entwickler:grundinstallation#einrichten_des_heartbeat-cronjobs_optional"
                    target="_blank">Link zu Helpdesk</a>)!
                    </div>',
            $parameter
        );
        $this->app->Tpl->Add($target, $message);

        return;
    }


    if($activated) {
        $message = sprintf(
            '<div class="info">Der Prozessstarter %s wurde aktiviert.</div>',
            $cronjob['bezeichnung']
        );
        $this->app->Tpl->Add($target, $message);
    }
  }

  /**
     * @param $id
     *
     * @return bool
     */
  public function isCronjobRunningRegular($id)
  {
      $cronjob = $this->app->DB->SelectRow(
          sprintf(
              "SELECT p.id, p.aktiv, p.art, p.letzteausfuerhung, p.periode, p.art_filter, p.startzeit
              FROM `prozessstarter` as `p` 
              WHERE p.id = '%s' 
              ORDER BY p.aktiv = 1 DESC
              LIMIT 1",
              $this->app->DB->real_escape_string($id)
          )
      );

      if (empty($cronjob)) {
          return false;
      }
      if ($cronjob['letzteausfuerhung'] === '0000-00-00 00:00:00') {
          return true;
      }

      $lastTime = DateTime::createFromFormat('Y-m-d H:i:s',$cronjob['letzteausfuerhung']);
      if ($lastTime === false) {
          return false;
      }
      try {
        $now = new DateTime('now');
        $nowDb = new DateTime($this->app->DB->Select('SELECT NOW()'));
      } catch (Exception $e) {
          return false;
      }

      $period = 0;
      if ($cronjob['art'] === 'periodisch') {
          $period = (float)$cronjob['periode'];
          $period += 5; //tolerance
      }
      $dayOfWeekNumber = (int)$now->format('w');
      if ($cronjob['art'] === 'uhrzeit') {
          $period = 1;
          if((int)$cronjob['art_filter'] > 0) {
              $period = $dayOfWeekNumber - (int)$cronjob['art_filter'];
              if ($period < 1) {
                  $period = 7 + $period;
              }
          }
          $period++; //rolerance
          $period *= 1440;
      }

      $diff = ceil(($now->getTimestamp() - $lastTime->getTimestamp()) / 60);
      $diffDb = ceil(($nowDb->getTimestamp() - $lastTime->getTimestamp()) / 60);

      return $period > $diff || $period > $diffDb;
  }
}
