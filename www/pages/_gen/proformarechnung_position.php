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

class GenProformarechnung_Position { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Proformarechnung_PositionCreate");
    $this->app->ActionHandler("edit","Proformarechnung_PositionEdit");
    $this->app->ActionHandler("copy","Proformarechnung_PositionCopy");
    $this->app->ActionHandler("list","Proformarechnung_PositionList");
    $this->app->ActionHandler("delete","Proformarechnung_PositionDelete");

    $this->app->Tpl->Set("HEADING","Proformarechnung_Position");    //$this->app->ActionHandlerListen($app);
  }

  function Proformarechnung_PositionCreate(){
    $this->app->Tpl->Set("HEADING","Proformarechnung_Position (Anlegen)");
      $this->app->PageBuilder->CreateGen("proformarechnung_position_create.tpl");
  }

  function Proformarechnung_PositionEdit(){
    $this->app->Tpl->Set("HEADING","Proformarechnung_Position (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("proformarechnung_position_edit.tpl");
  }

  function Proformarechnung_PositionCopy(){
    $this->app->Tpl->Set("HEADING","Proformarechnung_Position (Kopieren)");
      $this->app->PageBuilder->CreateGen("proformarechnung_position_copy.tpl");
  }

  function Proformarechnung_PositionDelete(){
    $this->app->Tpl->Set("HEADING","Proformarechnung_Position (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("proformarechnung_position_delete.tpl");
  }

  function Proformarechnung_PositionList(){
    $this->app->Tpl->Set("HEADING","Proformarechnung_Position (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("proformarechnung_position_list.tpl");
  }

} 
?>