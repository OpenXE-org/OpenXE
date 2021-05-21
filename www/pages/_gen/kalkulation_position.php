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

class GenKalkulation_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Kalkulation_PositionCreate");
    $this->app->ActionHandler("edit","Kalkulation_PositionEdit");
    $this->app->ActionHandler("copy","Kalkulation_PositionCopy");
    $this->app->ActionHandler("list","Kalkulation_PositionList");
    $this->app->ActionHandler("delete","Kalkulation_PositionDelete");

    $this->app->Tpl->Set("HEADING","Kalkulation_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Kalkulation_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Kalkulation_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("kalkulation_position_create.tpl");
  }

  function Kalkulation_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Kalkulation_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("kalkulation_position_edit.tpl");
  }

  function Kalkulation_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Kalkulation_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("kalkulation_position_copy.tpl");
  }

  function Kalkulation_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Kalkulation_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("kalkulation_position_delete.tpl");
  }

  function Kalkulation_PositionList(){
    $this->app->Tpl->Set("HEADING","Kalkulation_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("kalkulation_position_list.tpl");
  }

} 
?>