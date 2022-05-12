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

class GenWawisioneinrichtung { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","WawisioneinrichtungCreate");
    $this->app->ActionHandler("edit","WawisioneinrichtungEdit");
    $this->app->ActionHandler("copy","WawisioneinrichtungCopy");
    $this->app->ActionHandler("list","WawisioneinrichtungList");
    $this->app->ActionHandler("delete","WawisioneinrichtungDelete");

    $this->app->Tpl->Set("HEADING","Wawisioneinrichtung");    //$this->app->ActionHandlerListen($app);
  }

  function WawisioneinrichtungCreate(){
    $this->app->Tpl->Set("HEADING","Wawisioneinrichtung (Anlegen)");
      $this->app->PageBuilder->CreateGen("wawisioneinrichtung_create.tpl");
  }

  function WawisioneinrichtungEdit(){
    $this->app->Tpl->Set("HEADING","Wawisioneinrichtung (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("wawisioneinrichtung_edit.tpl");
  }

  function WawisioneinrichtungCopy(){
    $this->app->Tpl->Set("HEADING","Wawisioneinrichtung (Kopieren)");
      $this->app->PageBuilder->CreateGen("wawisioneinrichtung_copy.tpl");
  }

  function WawisioneinrichtungDelete(){
    $this->app->Tpl->Set("HEADING","Wawisioneinrichtung (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("wawisioneinrichtung_delete.tpl");
  }

  function WawisioneinrichtungList(){
    $this->app->Tpl->Set("HEADING","Wawisioneinrichtung (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("wawisioneinrichtung_list.tpl");
  }

} 
?>