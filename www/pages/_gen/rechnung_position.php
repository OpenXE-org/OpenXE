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

class GenRechnung_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Rechnung_PositionCreate");
    $this->app->ActionHandler("edit","Rechnung_PositionEdit");
    $this->app->ActionHandler("copy","Rechnung_PositionCopy");
    $this->app->ActionHandler("list","Rechnung_PositionList");
    $this->app->ActionHandler("delete","Rechnung_PositionDelete");

    $this->app->Tpl->Set("HEADING","Rechnung_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Rechnung_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Rechnung_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("rechnung_position_create.tpl");
  }

  function Rechnung_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Rechnung_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("rechnung_position_edit.tpl");
  }

  function Rechnung_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Rechnung_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("rechnung_position_copy.tpl");
  }

  function Rechnung_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Rechnung_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("rechnung_position_delete.tpl");
  }

  function Rechnung_PositionList(){
    $this->app->Tpl->Set("HEADING","Rechnung_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("rechnung_position_list.tpl");
  }

} 
?>