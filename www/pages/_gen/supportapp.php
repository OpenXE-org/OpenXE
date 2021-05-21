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

class GenSupportapp { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","SupportappCreate");
    $this->app->ActionHandler("edit","SupportappEdit");
    $this->app->ActionHandler("copy","SupportappCopy");
    $this->app->ActionHandler("list","SupportappList");
    $this->app->ActionHandler("delete","SupportappDelete");

    $this->app->Tpl->Set("HEADING","Supportapp");    //$this->app->ActionHandlerListen($app);
  }

  function SupportappCreate(){
    $this->app->Tpl->Set("HEADING","Supportapp (Anlegen)");
      $this->app->PageBuilder->CreateGen("supportapp_create.tpl");
  }

  function SupportappEdit(){
    $this->app->Tpl->Set("HEADING","Supportapp (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("supportapp_edit.tpl");
  }

  function SupportappCopy(){
    $this->app->Tpl->Set("HEADING","Supportapp (Kopieren)");
      $this->app->PageBuilder->CreateGen("supportapp_copy.tpl");
  }

  function SupportappDelete(){
    $this->app->Tpl->Set("HEADING","Supportapp (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("supportapp_delete.tpl");
  }

  function SupportappList(){
    $this->app->Tpl->Set("HEADING","Supportapp (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("supportapp_list.tpl");
  }

} 
?>