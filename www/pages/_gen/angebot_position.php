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

class GenAngebot_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Angebot_PositionCreate");
    $this->app->ActionHandler("edit","Angebot_PositionEdit");
    $this->app->ActionHandler("copy","Angebot_PositionCopy");
    $this->app->ActionHandler("list","Angebot_PositionList");
    $this->app->ActionHandler("delete","Angebot_PositionDelete");

    $this->app->Tpl->Set("HEADING","Angebot_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Angebot_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Angebot_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("angebot_position_create.tpl");
  }

  function Angebot_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Angebot_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("angebot_position_edit.tpl");
  }

  function Angebot_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Angebot_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("angebot_position_copy.tpl");
  }

  function Angebot_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Angebot_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("angebot_position_delete.tpl");
  }

  function Angebot_PositionList(){
    $this->app->Tpl->Set("HEADING","Angebot_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("angebot_position_list.tpl");
  }

} 
?>