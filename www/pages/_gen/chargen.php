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

class GenChargen { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ChargenCreate");
    $this->app->ActionHandler("edit","ChargenEdit");
    $this->app->ActionHandler("copy","ChargenCopy");
    $this->app->ActionHandler("list","ChargenList");
    $this->app->ActionHandler("delete","ChargenDelete");

    $this->app->Tpl->Set("HEADING","Chargen");    //$this->app->ActionHandlerListen($app);
  }

  function ChargenCreate(){
    $this->app->Tpl->Set("HEADING","Chargen (Anlegen)");
      $this->app->PageBuilder->CreateGen("chargen_create.tpl");
  }

  function ChargenEdit(){
    $this->app->Tpl->Set("HEADING","Chargen (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("chargen_edit.tpl");
  }

  function ChargenCopy(){
    $this->app->Tpl->Set("HEADING","Chargen (Kopieren)");
      $this->app->PageBuilder->CreateGen("chargen_copy.tpl");
  }

  function ChargenDelete(){
    $this->app->Tpl->Set("HEADING","Chargen (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("chargen_delete.tpl");
  }

  function ChargenList(){
    $this->app->Tpl->Set("HEADING","Chargen (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("chargen_list.tpl");
  }

} 
?>