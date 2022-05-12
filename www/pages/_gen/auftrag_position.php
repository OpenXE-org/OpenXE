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

class GenAuftrag_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Auftrag_PositionCreate");
    $this->app->ActionHandler("edit","Auftrag_PositionEdit");
    $this->app->ActionHandler("copy","Auftrag_PositionCopy");
    $this->app->ActionHandler("list","Auftrag_PositionList");
    $this->app->ActionHandler("delete","Auftrag_PositionDelete");

    $this->app->Tpl->Set("HEADING","Auftrag_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Auftrag_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Auftrag_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("auftrag_position_create.tpl");
  }

  function Auftrag_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Auftrag_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("auftrag_position_edit.tpl");
  }

  function Auftrag_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Auftrag_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("auftrag_position_copy.tpl");
  }

  function Auftrag_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Auftrag_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("auftrag_position_delete.tpl");
  }

  function Auftrag_PositionList(){
    $this->app->Tpl->Set("HEADING","Auftrag_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("auftrag_position_list.tpl");
  }

} 
?>