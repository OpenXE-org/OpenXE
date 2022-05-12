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

class GenReisekosten_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Reisekosten_PositionCreate");
    $this->app->ActionHandler("edit","Reisekosten_PositionEdit");
    $this->app->ActionHandler("copy","Reisekosten_PositionCopy");
    $this->app->ActionHandler("list","Reisekosten_PositionList");
    $this->app->ActionHandler("delete","Reisekosten_PositionDelete");

    $this->app->Tpl->Set("HEADING","Reisekosten_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Reisekosten_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Reisekosten_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("reisekosten_position_create.tpl");
  }

  function Reisekosten_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Reisekosten_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("reisekosten_position_edit.tpl");
  }

  function Reisekosten_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Reisekosten_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("reisekosten_position_copy.tpl");
  }

  function Reisekosten_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Reisekosten_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("reisekosten_position_delete.tpl");
  }

  function Reisekosten_PositionList(){
    $this->app->Tpl->Set("HEADING","Reisekosten_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("reisekosten_position_list.tpl");
  }

} 
?>