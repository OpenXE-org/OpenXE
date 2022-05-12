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

class GenKontorahmen { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","KontorahmenCreate");
    $this->app->ActionHandler("edit","KontorahmenEdit");
    $this->app->ActionHandler("copy","KontorahmenCopy");
    $this->app->ActionHandler("list","KontorahmenList");
    $this->app->ActionHandler("delete","KontorahmenDelete");

    $this->app->Tpl->Set("HEADING","Kontorahmen");    //$this->app->ActionHandlerListen($app);
  }

  function KontorahmenCreate(){
    $this->app->Tpl->Set("HEADING","Kontorahmen (Anlegen)");
      $this->app->PageBuilder->CreateGen("kontorahmen_create.tpl");
  }

  function KontorahmenEdit(){
    $this->app->Tpl->Set("HEADING","Kontorahmen (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("kontorahmen_edit.tpl");
  }

  function KontorahmenCopy(){
    $this->app->Tpl->Set("HEADING","Kontorahmen (Kopieren)");
      $this->app->PageBuilder->CreateGen("kontorahmen_copy.tpl");
  }

  function KontorahmenDelete(){
    $this->app->Tpl->Set("HEADING","Kontorahmen (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("kontorahmen_delete.tpl");
  }

  function KontorahmenList(){
    $this->app->Tpl->Set("HEADING","Kontorahmen (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("kontorahmen_list.tpl");
  }

} 
?>