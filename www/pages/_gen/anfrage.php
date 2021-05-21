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

class GenAnfrage { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AnfrageCreate");
    $this->app->ActionHandler("edit","AnfrageEdit");
    $this->app->ActionHandler("copy","AnfrageCopy");
    $this->app->ActionHandler("list","AnfrageList");
    $this->app->ActionHandler("delete","AnfrageDelete");

    $this->app->Tpl->Set("HEADING","Anfrage");    //$this->app->ActionHandlerListen($app);
  }

  function AnfrageCreate(){
    $this->app->Tpl->Set("HEADING","Anfrage (Anlegen)");
      $this->app->PageBuilder->CreateGen("anfrage_create.tpl");
  }

  function AnfrageEdit(){
    $this->app->Tpl->Set("HEADING","Anfrage (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("anfrage_edit.tpl");
  }

  function AnfrageCopy(){
    $this->app->Tpl->Set("HEADING","Anfrage (Kopieren)");
      $this->app->PageBuilder->CreateGen("anfrage_copy.tpl");
  }

  function AnfrageDelete(){
    $this->app->Tpl->Set("HEADING","Anfrage (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("anfrage_delete.tpl");
  }

  function AnfrageList(){
    $this->app->Tpl->Set("HEADING","Anfrage (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("anfrage_list.tpl");
  }

} 
?>