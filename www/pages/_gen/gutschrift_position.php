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

class GenGutschrift_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Gutschrift_PositionCreate");
    $this->app->ActionHandler("edit","Gutschrift_PositionEdit");
    $this->app->ActionHandler("copy","Gutschrift_PositionCopy");
    $this->app->ActionHandler("list","Gutschrift_PositionList");
    $this->app->ActionHandler("delete","Gutschrift_PositionDelete");

    $this->app->Tpl->Set("HEADING","Gutschrift_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Gutschrift_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Gutschrift_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("gutschrift_position_create.tpl");
  }

  function Gutschrift_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Gutschrift_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("gutschrift_position_edit.tpl");
  }

  function Gutschrift_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Gutschrift_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("gutschrift_position_copy.tpl");
  }

  function Gutschrift_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Gutschrift_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("gutschrift_position_delete.tpl");
  }

  function Gutschrift_PositionList(){
    $this->app->Tpl->Set("HEADING","Gutschrift_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("gutschrift_position_list.tpl");
  }

} 
?>