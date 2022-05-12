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

class GenExportvorlage { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ExportvorlageCreate");
    $this->app->ActionHandler("edit","ExportvorlageEdit");
    $this->app->ActionHandler("copy","ExportvorlageCopy");
    $this->app->ActionHandler("list","ExportvorlageList");
    $this->app->ActionHandler("delete","ExportvorlageDelete");

    $this->app->Tpl->Set("HEADING","Exportvorlage");    //$this->app->ActionHandlerListen($app);
  }

  function ExportvorlageCreate(){
    $this->app->Tpl->Set("HEADING","Exportvorlage (Anlegen)");
      $this->app->PageBuilder->CreateGen("exportvorlage_create.tpl");
  }

  function ExportvorlageEdit(){
    $this->app->Tpl->Set("HEADING","Exportvorlage (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("exportvorlage_edit.tpl");
  }

  function ExportvorlageCopy(){
    $this->app->Tpl->Set("HEADING","Exportvorlage (Kopieren)");
      $this->app->PageBuilder->CreateGen("exportvorlage_copy.tpl");
  }

  function ExportvorlageDelete(){
    $this->app->Tpl->Set("HEADING","Exportvorlage (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("exportvorlage_delete.tpl");
  }

  function ExportvorlageList(){
    $this->app->Tpl->Set("HEADING","Exportvorlage (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("exportvorlage_list.tpl");
  }

} 
?>