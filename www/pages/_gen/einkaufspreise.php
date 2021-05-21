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

class GenEinkaufspreise { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","EinkaufspreiseCreate");
    $this->app->ActionHandler("edit","EinkaufspreiseEdit");
    $this->app->ActionHandler("copy","EinkaufspreiseCopy");
    $this->app->ActionHandler("list","EinkaufspreiseList");
    $this->app->ActionHandler("delete","EinkaufspreiseDelete");

    $this->app->Tpl->Set("HEADING","Einkaufspreise");    //$this->app->ActionHandlerListen($app);
  }

  function EinkaufspreiseCreate(){
    $this->app->Tpl->Set("HEADING","Einkaufspreise (Anlegen)");
      $this->app->PageBuilder->CreateGen("einkaufspreise_create.tpl");
  }

  function EinkaufspreiseEdit(){
    $this->app->Tpl->Set("HEADING","Einkaufspreise (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("einkaufspreise_edit.tpl");
  }

  function EinkaufspreiseCopy(){
    $this->app->Tpl->Set("HEADING","Einkaufspreise (Kopieren)");
      $this->app->PageBuilder->CreateGen("einkaufspreise_copy.tpl");
  }

  function EinkaufspreiseDelete(){
    $this->app->Tpl->Set("HEADING","Einkaufspreise (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("einkaufspreise_delete.tpl");
  }

  function EinkaufspreiseList(){
    $this->app->Tpl->Set("HEADING","Einkaufspreise (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("einkaufspreise_list.tpl");
  }

} 
?>