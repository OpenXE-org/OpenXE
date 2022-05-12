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

class Databaseviewer {
  /** @var erpooSystem $app */
  var $app;

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public static function TableSearch($app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case 'database_table_view':
        $table = $app->User->GetParameter('xcs_tree_name');
        $cols = [];
        if(!empty($table)) {
          $columns = $app->DB->SelectArrCache(
            sprintf(
              'SHOW COLUMNS FROM `%s` ', $table), 60, 'xcs_column'
          );

          if(!empty($columns)) {
            foreach($columns as $key => $column) {
              $cols[] = reset($column);
              if($key > 74) {
                break;
              }
            }
          }
        }

        $firstCol = reset($cols);
        $heading = array_merge($cols, ['']);
        $findcols = array_merge($cols, [$firstCol]);
        $searchsql = $cols;
        $sql = sprintf(
          "SELECT `%s`, IFNULL(`%s`, '')  FROM `%s`",
          $firstCol, implode("`,''), IFNULL(`", $cols), $table
        );
        $fastcount = sprintf('SELECT COUNT(*) FROM `%s`', $table);
        break;
    }

    $erg = [];

    foreach($erlaubtevars as $k => $v) {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  /**
   * Databaseviewer constructor.
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

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler("list", "DatabaseViewerList");
    $this->app->ActionHandler("treeajax", "DatabaseViewerTreeAjax");

    $this->app->ActionHandlerListen($app);
  }

  public function DatabaseViewerMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=databaseviewer&action=list', '&Uuml;bersicht');
  }

  public function DatabaseViewerList()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'filtertree') {
      $tableName = $this->app->Secure->GetPOST('name');
      $this->app->User->SetParameter('xcs_tree_id',$this->app->Secure->GetPOST('id'));
      $this->app->User->SetParameter('xcs_tree_name', $tableName);
      $tableColumns = $this->app->DB->SelectArrCache(
        sprintf(
          'SHOW COLUMNS FROM `%s` ', $tableName), 60, 'xcs_column'
      );
      $html = '';
      if(count($tableColumns) > 75) {
        $html = '<div class="warning">Die Tabellenansicht wurde auf 75 Spalten gek&uuml;rzt</div>';
      }
      $this->app->YUI->TableSearch('PAGE', 'database_table_view', 'show', '', '', basename(__FILE__), __CLASS__);
      $html .= $this->app->Tpl->FinalParse('databaseviewer_table.tpl');
      echo json_encode(array('status'=>1,'html'=>$html));
      $this->app->ExitXentral();
    }
    if($this->app->Secure->GetGET('module')==='databaseviewer'){
      $this->DatabaseViewerMenu();
    }

    $this->app->erp->Headlines('Datenbank Ansicht');
    $this->app->Tpl->Set(
      'TREE',
      '<div id="treediv">'.
      $this->app->Tpl->Parse('return', 'databaseviewer_tree.tpl',true).
      '</div>'
    );
    $this->app->Tpl->Parse('PAGE', 'databaseviewer_sqlviewer.tpl');
  }

  /**
   * @param null|array $tables
   */
  public function getTables(&$tables)
  {
    $res = $this->app->DB->SelectArrCache('SHOW TABLES',60, 'xcs_table');
    if(empty($res)) {
      return;
    }
    foreach($res as $table) {
      $table = reset($table);
      $tables[] = $table;
    }
  }

  public function DatabaseViewerTreeAjax()
  {
    $disabled = false;
    $tables = null;
    $this->getTables($tables);

    if(!empty($tables)) {
      $maxlvl = 0;
      foreach($tables as $k => $table) {
        $name = 'node'.$k;
        $$name = new stdClass();
        $$name->id = $k;
        $$name->label = $table;
        $$name->checkbox = false;
        $$name->inode = false;
        $$name->radio = false;
        if($disabled) {
          $$name->disabled = true;
        }
        $baum[] = $$name;
      }
      echo json_encode($baum);
      $this->app->erp->ExitWawi();
    }
    echo '[]';
    $this->app->erp->ExitWawi();
  }

}
