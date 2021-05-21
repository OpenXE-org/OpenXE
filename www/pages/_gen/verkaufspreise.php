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

class GenVerkaufspreise { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","VerkaufspreiseCreate");
    $this->app->ActionHandler("edit","VerkaufspreiseEdit");
    $this->app->ActionHandler("copy","VerkaufspreiseCopy");
    $this->app->ActionHandler("list","VerkaufspreiseList");
    $this->app->ActionHandler("delete","VerkaufspreiseDelete");

    $this->app->Tpl->Set("HEADING","Verkaufspreise");    //$this->app->ActionHandlerListen($app);
  }

  function VerkaufspreiseCreate(){
    $this->app->Tpl->Set("HEADING","Verkaufspreise (Anlegen)");
      $this->app->PageBuilder->CreateGen("verkaufspreise_create.tpl");
  }

  function VerkaufspreiseEdit(){
    $this->app->Tpl->Set("HEADING","Verkaufspreise (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("verkaufspreise_edit.tpl");
  }

  function VerkaufspreiseCopy(){
    $this->app->Tpl->Set("HEADING","Verkaufspreise (Kopieren)");
      $this->app->PageBuilder->CreateGen("verkaufspreise_copy.tpl");
  }

  function VerkaufspreiseDelete(){
    $this->app->Tpl->Set("HEADING","Verkaufspreise (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("verkaufspreise_delete.tpl");
  }

  function VerkaufspreiseList(){
    $this->app->Tpl->Set("HEADING","Verkaufspreise (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("verkaufspreise_list.tpl");
  }

} 
?>