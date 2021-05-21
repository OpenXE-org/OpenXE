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
class Protokoll {
  /** @var Application $app */
  var $app;

  /**
   * Protokoll constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern)
    {
      return;
    }
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","ProtokollList");
    $this->app->ActionHandler("minidetail","ProtokollMiniDetail");
    //$this->app->ActionHandler("delete","ProtokollDelete");
    //$this->app->ActionHandler("deleteall","ProtokollDeleteAll");

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
      case "protokoll":

        // START EXTRA checkboxen

        // ENDE EXTRA checkboxen

        $allowed['protokoll'] = array('list');

        // headings
        $heading = array('', 'ID', 'Zeit', 'Bearbeiter', 'Module', 'Action', 'Parameter', 'Funktion', 'Meldung', 'Men&uuml;');
        $width = array('4%', '4%', '15%', '10%', '10%', '10%', '10%', '10%', '40%', '10%', '5%');
        $findcols = array('open', 'a.id', 'a.datum', 'a.bearbeiter', 'a.module', 'a.action', 'a.parameter', 'a.funktionsname', 'a.meldung', 'a.id');
        $searchsql = array("DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s')", 'a.bearbeiter', 'a.module', 'a.meldung', 'a.action', 'a.parameter', 'a.funktionsname');
        $defaultorder = 2;
        $defaultorderdesc = 1;
        $menucol = 1;
        $moreinfo = true;
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?%value%\" target=\"_blank\">" . "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>" .

          //                                              "&nbsp;".

          //                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=logfile&action=delete&id=%value%\");>".


          //                                                               "<img src=\"themes/{$this->app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>".

          "</td></tr></table>";

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf['defaulttheme']}/images/details_open.png class=details>' as open, a.id,DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s'), a.bearbeiter, 
                                           a.module, a.action, a.parameter, a.funktionsname,a.meldung,CONCAT('module=',a.module,'&action=',a.action,'&id=',a.parameter) FROM protokoll a";

        //$where = "d.firma='".$this->app->User->GetFirma()."'";

        //$groupby=" GROUP by z.adresse_abrechnung ";


        // gesamt anzahl

        $count = "SELECT COUNT(a.id) FROM protokoll a";
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


  function ProtokollMiniDetail()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $dump = $this->app->DB->SelectRow(
      sprintf(
        'SELECT argumente,funktionsname,dump FROM protokoll WHERE id=%d LIMIT 1',
        $id
      )
    );
    echo '<pre>Argumente der Funktion '.$dump['funktionsname'].':<br><br>'.base64_decode($dump['argumente']).'</pre>';
    echo '<br><br><pre>Dump:'.$dump['dump'].'</pre>';
    $this->app->ExitXentral();
  }

  function ProtokollList()
  {
    $this->ProtokollMenu();
    $this->app->YUI->TableSearch('TAB1','protokoll','show','','',basename(__FILE__), __CLASS__);
    $this->app->Tpl->Parse('PAGE',"protokoll_list.tpl");
  }

  function ProtokollMenu()
  {
    $this->app->erp->Headlines('Protokoll');
    $this->app->erp->MenuEintrag('index.php?module=einstellungen&action=list','Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=protokoll&action=list','Aktualisieren');
    $this->app->erp->RunMenuHook('protokoll');
  }

  public function Install() {
    $this->app->erp->CheckTable('protokoll');
    $this->app->erp->CheckColumn('id','int(11)','protokoll','NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('meldung','TEXT','protokoll','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('dump','TEXT','protokoll','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('module','VARCHAR(64)','protokoll','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('action','VARCHAR(64)','protokoll','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('bearbeiter','VARCHAR(64)','protokoll','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('funktionsname','VARCHAR(64)','protokoll','DEFAULT \'\' NOT NULL');
    $this->app->erp->CheckColumn('datum','DATETIME','protokoll');
    $this->app->erp->CheckColumn('parameter','int(11)','protokoll','DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('argumente','TEXT','protokoll','DEFAULT \'\' NOT NULL');
  }

  /**
   * @param int $days
   * @param int $count
   *
   * @return array
   */
  public function getModuleStats($days = 90, $count = 10)
  {
    return $this->app->DB->SelectPairs(
      sprintf(
        "SELECT ms.module, SUM(ms.view_count) AS view_count
        FROM `module_stat` AS `ms`
        WHERE created_date > DATE_SUB(CURDATE(), INTERVAL %d DAY)
        GROUP BY ms.module
        ORDER BY SUM(ms.view_count) DESC 
        LIMIT %d",
        $days, $count
      )
    );
  }

  /**
   * @param string $module
   * @param string $action
   */
  public function addModuleStat($module = '', $action = '')
  {
    if($module=='') {
      $module = $this->app->Secure->GetGET('module');
    }
    if($action=='') {
      $action = $this->app->Secure->GetGET('action');
    }
    if(empty($module) || empty($action)) {
      return;
    }
    if(in_array($module, ['welcome','ajax','dateien','wizard'])) {
      return;
    }
    if(in_array($action, ['ajax','getfile','positionen'])) {
      return;
    }

    if($module === 'artikel' && $action === 'thumbnail') {
      return;
    }

    if($module === 'massenbearbeitung' && $action === 'edit') {
      return;
    }

    if($module === 'kalender' && $action === 'data') {
      return;
    }

    $date = date('Y-m-d');
    $this->app->DB->Update(
      sprintf(
        "UPDATE `module_stat` SET `view_count` = `view_count` + 1
        WHERE `module` = '%s' AND `action` = '%s' AND `created_date` = '%s'",
        $this->app->DB->real_escape_string($module),
        $this->app->DB->real_escape_string($action),
        $this->app->DB->real_escape_string($date)
      )
    );
    if($this->app->DB->affected_rows() > 0) {
      return;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `module_stat` (`module`, `action`, `created_date`, `view_count`)
           VALUES ('%s', '%s', '%s', 1)",
        $this->app->DB->real_escape_string($module),
        $this->app->DB->real_escape_string($action),
        $this->app->DB->real_escape_string($date)
      )
    );
  }

  public function addProtokoll($meldung='',$dump='',$module='',$action='',$functionname='')
  {
    $isEmpty = empty($meldung) && empty($dump) && empty($module) && empty($action) && empty($functionname);
    $isSecure = isset($this->app->Secure);
    if($module=='' && $isSecure) {
      $module = $this->app->Secure->GetGET('module');
    }
    if($action=='' && $isSecure) {
      $action = $this->app->Secure->GetGET('action');
    }

    if($isEmpty && $isSecure) {
      $this->addModuleStat();
    }

    $id = 0;
    if($isSecure){
      $id = $this->app->Secure->GetGET('id');
    }

    if($functionname=='')
    {
      if (strnatcmp(phpversion(),'5.0.0') >= 0)
      {
        $backtrace = debug_backtrace();
        $functionname = $backtrace[1]['function'];
        if($functionname!=='Run'){
          $argumente = base64_encode(print_r($backtrace[1]['args'], true));
        }
      }
    }
    if(!isset($argumente)) {
      $argumente = '';
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO protokoll (module,`action`,meldung,dump,datum,bearbeiter,funktionsname,parameter,argumente)
        VALUES ('%s','%s','%s','%s',NOW(),'%s','%s',%d,'%s')",
        $module, $action, $meldung, $dump,
        isset($this->app->User)?$this->app->DB->real_escape_string($this->app->User->GetName()):'',
        $this->app->DB->real_escape_string($functionname),(int)$id, $argumente
      )
    );

    return $this->app->DB->GetInsertID();
  }
}
