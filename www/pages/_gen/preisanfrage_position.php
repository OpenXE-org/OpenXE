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

class GenPreisanfrage_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Preisanfrage_PositionCreate");
    $this->app->ActionHandler("edit","Preisanfrage_PositionEdit");
    $this->app->ActionHandler("copy","Preisanfrage_PositionCopy");
    $this->app->ActionHandler("list","Preisanfrage_PositionList");
    $this->app->ActionHandler("delete","Preisanfrage_PositionDelete");

    $this->app->Tpl->Set("HEADING","Preisanfrage_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Preisanfrage_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Preisanfrage_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("preisanfrage_position_create.tpl");
  }

  function Preisanfrage_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Preisanfrage_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("preisanfrage_position_edit.tpl");
  }

  function Preisanfrage_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Preisanfrage_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("preisanfrage_position_copy.tpl");
  }

  function Preisanfrage_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Preisanfrage_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("preisanfrage_position_delete.tpl");
  }

  function Preisanfrage_PositionList(){
    $this->app->Tpl->Set("HEADING","Preisanfrage_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("preisanfrage_position_list.tpl");
  }

} 
?>