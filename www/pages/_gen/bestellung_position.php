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

class GenBestellung_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Bestellung_PositionCreate");
    $this->app->ActionHandler("edit","Bestellung_PositionEdit");
    $this->app->ActionHandler("copy","Bestellung_PositionCopy");
    $this->app->ActionHandler("list","Bestellung_PositionList");
    $this->app->ActionHandler("delete","Bestellung_PositionDelete");

    $this->app->Tpl->Set("HEADING","Bestellung_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Bestellung_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Bestellung_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("bestellung_position_create.tpl");
  }

  function Bestellung_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Bestellung_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("bestellung_position_edit.tpl");
  }

  function Bestellung_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Bestellung_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("bestellung_position_copy.tpl");
  }

  function Bestellung_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Bestellung_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("bestellung_position_delete.tpl");
  }

  function Bestellung_PositionList(){
    $this->app->Tpl->Set("HEADING","Bestellung_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("bestellung_position_list.tpl");
  }

} 
?>