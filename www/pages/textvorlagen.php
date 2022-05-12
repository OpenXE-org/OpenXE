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

use Xentral\Modules\TextTemplate\DataTable\TextTemplateDataTable;
use Xentral\Widgets\DataTable\DataTableBuildConfig;
use Xentral\Widgets\DataTable\Service\DataTableRequestHandler;

class Textvorlagen
{
  /** @var ApplicationCore $app */
  public $app;

  /**
   * @param ApplicationCore $app
   */
  function __construct($app)
  {
    $this->app=$app;

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("show","TextvorlagenShow");
    $this->app->ActionHandler("save","TextvorlagenSave");

    $this->app->DefaultActionHandler("show");

    $this->app->ActionHandlerListen($app);
  }

  public function TextvorlagenShow()
  {
    $cmd = $this->app->Secure->GetGET('cmd');

    if ($cmd === 'table') {
      $buildConfig = new DataTableBuildConfig(
        'texttemplates',
        TextTemplateDataTable::class,
        'index.php?module=textvorlagen&action=show&cmd=table',
        false
      );

      /** @var DataTableRequestHandler $handler */
      $handler = $this->app->Container->get('DataTableRequestHandler');
      if ($handler->canHandleRequest($buildConfig)){
        $response = $handler->handleRequest($buildConfig);
        $response->send();
        $this->app->ExitXentral();
      }
    }

    $this->app->YUI->AutoComplete("textvorlageprojekt","projektname", 1);
    $this->app->ExitXentral();
    exit;
  }
  
  public function TextvorlagenSave()
  {
    $id = (int)$this->app->Secure->GetPOST('textvorlageid');
    $name = $this->app->Secure->GetPOST('textvorlagename');
    $text = $_POST['textvorlagetext'];
    //$text = $this->app->Secure->GetPOST('textvorlagetext');
    $text = str_replace("\n","<br>",$text);
    $text = str_replace("\r","",$text);
    $text = $this->app->DB->real_escape_string($text);
    $projekt = $this->app->Secure->GetPOST('textvorlageprojekt');
    $stichwoerter = $this->app->Secure->GetPOST('textvorlagestichworter');
    $delete = $this->app->Secure->GetPOST("deletetextvorlage");
    if($id > 0)
    {
      if($delete)
      {
        $this->app->DB->Delete("delete from textvorlagen where id = ".$id);
        exit;
      }
      $this->app->DB->Update("update textvorlagen set name = '".($name)."', text = '".($text)."', projekt = '".($projekt)."', stichwoerter = '".($stichwoerter)."' where id = ".$id);
    }else{
      $row = $this->app->DB->SelectArr("Select * from textvorlagen where name like '".($name)."' and projekt like '".($projekt)."' and stichwoerter like '".($stichwoerter)."' limit 1");
      if($row)
      {
        $this->app->DB->Update("update textvorlagen set name = '".($name)."', text = '".($text)."', projekt = '".($projekt)."', stichwoerter = '".($stichwoerter)."' where id = ".$row[0]['id']);
      }else{
        $this->app->DB->Insert("insert into textvorlagen (name, text, projekt, stichwoerter) values('".($name)."','".($text)."','".($projekt)."','".($stichwoerter)."')");
      }      
    }
    exit;
  }

}
