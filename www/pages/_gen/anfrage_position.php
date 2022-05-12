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

class GenAnfrage_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Anfrage_PositionCreate");
    $this->app->ActionHandler("edit","Anfrage_PositionEdit");
    $this->app->ActionHandler("copy","Anfrage_PositionCopy");
    $this->app->ActionHandler("list","Anfrage_PositionList");
    $this->app->ActionHandler("delete","Anfrage_PositionDelete");

    $this->app->Tpl->Set("HEADING","Anfrage_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Anfrage_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Anfrage_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("anfrage_position_create.tpl");
  }

  function Anfrage_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Anfrage_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("anfrage_position_edit.tpl");
  }

  function Anfrage_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Anfrage_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("anfrage_position_copy.tpl");
  }

  function Anfrage_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Anfrage_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("anfrage_position_delete.tpl");
  }

  function Anfrage_PositionList(){
    $this->app->Tpl->Set("HEADING","Anfrage_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("anfrage_position_list.tpl");
  }

} 
?>