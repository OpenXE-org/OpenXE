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
class Logfile {
  /** @var Application $app */
  var $app;
  /** @var null|bool */
  protected $logging = null;

  /**
   * Logfile constructor.
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

    $this->app->ActionHandler("list","LogfileList");
    $this->app->ActionHandler("delete","LogfileDelete");
    $this->app->ActionHandler("deleteall","LogfileDeleteAll");
    $this->app->ActionHandler("minidetail","LogfileMiniDetail");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }

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
    switch($name)
    {
      case 'logfile':
        $allowed['logfile'] = array('list');

        // START EXTRA checkboxen

        // ENDE EXTRA checkboxen


        // headings

        $heading = array('', 'ID', 'Zeit', 'Bearbeiter', 'Module', 'Action', 'Funktion', 'Meldung', 'Men&uuml;');
        $width = array('4%', '4%', '15%', '10%', '10%', '10%', '10%', '40%', '10%', '5%');
        $findcols = array('open', 'a.id', 'a.datum', 'a.bearbeiter', 'a.module', 'a.action', 'a.funktionsname', 'a.meldung', 'a.id');
        $searchsql = array("DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s')", 'a.bearbeiter', 'a.module', 'a.meldung', 'a.action', 'a.funktionsname');
        $defaultorder = 2;
        $defaultorderdesc = 1;
        $menucol = 1;
        $moreinfo = true;
        $menu = '<table cellpadding=0 cellspacing=0><tr><td nowrap>' .

          //                                                              "<a href=\"index.php?%value%\" target=\"_blank\">".

          //                                                             "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>".


          //                                              "&nbsp;".

          "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=logfile&action=delete&id=%value%\");>" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, a.id,DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s'), a.bearbeiter,
                                           a.module, a.action, a.funktionsname,a.meldung, a.id FROM logfile a";


        if($app->erp->GetKonfiguration('logfile_use_fulltext')){
          $matchesql = [
            'sqlpre' => $sql . ' INNER JOIN (', 
            'sqlpost' => ' ) AS `matches` ON matches.id = a.id ', 
            'elements' => []
          ];
          $matchesql['elements'][] = [
            'sql' => 'SELECT l.id
                      FROM logfile l',
            'where' => ["DATE_FORMAT(l.datum,'%d.%m.%Y %H:%i:%s')", 'l.bearbeiter', 'l.module', 'l.action', 'l.funktionsname'],
            'match' => ['l.meldung', 'l.dump']
          ];
        }else{
          unset($matchesql);
        }


        // gesamt anzahl

        $count = 'SELECT COUNT(a.id) FROM logfile a';
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

  public function LogFileMiniDetail()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $dump = $this->app->DB->SelectRow("SELECT funktionsname,dump FROM logfile WHERE id='$id' LIMIT 1");
    }
    if(!empty($dump)){
      echo '<pre>Funktion ' . $dump['funktionsname'] . ':<br></pre>';
      echo '<br><br><pre>Dump:' . $dump['dump'] . '</pre><br>';
      if(is_array(unserialize($dump['dump']))){
        echo '<pre>';
        print_r(unserialize($dump['dump']));
        echo '</pre>';
      }
    }
    $this->app->ExitXentral();
  }

  public function LogfileDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    if($id > 0){
      $this->app->DB->Delete(
        sprintf(
          'DELETE FROM `logfile` WHERE `id` = %d LIMIT 1',
          $id
        )
      );
    }
    $msg = $this->app->erp->base64_url_encode('<div class="error2">Der Logeintrag wurde gel&ouml;scht!</div>  ');
    $this->app->Location->execute('index.php?module=logfile&action=list&msg='.$msg);
  }	


  public function LogfileDeleteAll()
  {
    $this->app->DB->Delete('DELETE FROM `logfile`');
    $this->app->DB->Query('OPTIMIZE TABLE `logfile`');
    $msg = $this->app->erp->base64_url_encode('<div class="error2">Alle Logeintr&auml;ge wurden wurden gel&ouml;scht!</div>  ');
    $this->app->Location->execute('index.php?module=logfile&action=list&msg='.$msg);
  }	

  public function LogfileList()
  {
    $this->LogfileMenu();
    $isLoggingActive = $this->isLoggingActive();
    if($isLoggingActive) {
      $this->app->Tpl->Set('LOGGINGACTIVE', 'checked');
    }
    $this->app->YUI->AutoSaveKonfiguration('logginActive', 'logfile_logging_active');
    //$this->app->erp->InternesEvent($this->app->User->GetID(),"Hallo","alarm",1);

    $this->app->YUI->TableSearch('TAB1','logfile','show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE','logfile_list.tpl');
  }

  public function LogfileMenu()
  {
    $this->app->erp->Headlines('Logdatei');
    $this->app->erp->MenuEintrag('index.php?module=einstellungen&action=list','Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=logfile&action=list','Aktualisieren');
    $this->app->erp->MenuEintrag('index.php?module=logfile&action=deleteall','Alle Eintr&auml;ge l&ouml;schen');
  }

  public function Install() {
    $this->app->erp->CheckTable('logfile');
    $this->app->erp->CheckColumn('id','int(11)','logfile','NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('meldung','TEXT','logfile','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('dump','TEXT','logfile','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('module','VARCHAR(64)','logfile','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('action','VARCHAR(64)','logfile','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('bearbeiter','VARCHAR(64)','logfile','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('funktionsname','VARCHAR(64)','logfile','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('datum','DATETIME','logfile');

    $hasFullIndex = $this->app->erp->CheckFulltextIndex('logfile',['meldung', 'dump']);
    $this->app->erp->SetKonfigurationValue('logfile_use_fulltext', $hasFullIndex);
  }

  /**
   * @return bool
   */
  public function isLoggingActive()
  {
    if($this->logging !== null) {
      return (bool)$this->logging;
    }
    $loggingActive = (string)$this->app->erp->GetKonfiguration('logfile_logging_active');
    if($loggingActive === '') {
      $this->app->erp->SetKonfigurationValue('logfile_logging_active', '0');
      $this->logging = false;

      return false;
    }
    $this->logging = (bool)$loggingActive;

    return $this->logging;
  }

  /**
   * @param bool $status
   */
  public function changeLoggingStatus($status)
  {
    $this->logging = (bool)$status;
    $this->app->erp->SetKonfigurationValue('logfile_logging_active', (int)$this->logging);
  }

  /**
   * @param array|string $meldung
   * @param string       $dump
   * @param string       $module
   * @param string       $action
   * @param string       $functionname
   *
   * @return int
   */
  public function addLogFile($meldung,$dump='',$module='',$action='',$functionname='')
  {
    if(!$this->isLoggingActive()) {
      return 0;
    }
    if($functionname=='') {
      if (strnatcmp(phpversion(),'5.0.0') >= 0) {
        $backtrace = debug_backtrace();
        $functionname = isset($backtrace[1]) && isset($backtrace[1]['function'])?$backtrace[1]['function']:'';
        if($functionname === 'LogFile') {
          $functionname = isset($backtrace[2]) && isset($backtrace[2]['function'])?$backtrace[2]['function']:'';
        }
      }
    }
    if(is_array($meldung)) {
      $meldung = $this->app->DB->real_escape_string(print_r($meldung, true));
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO logfile (module,action,meldung,dump,datum,bearbeiter,funktionsname)
        VALUES ('%s','%s','%s','%s',NOW(),'','%s')",
        $module, $action,$meldung,$dump,$functionname
      )
    );

    return (int)$this->app->DB->GetInsertID();
  }
}
