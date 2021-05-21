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

class GenArtikel { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArtikelCreate");
    $this->app->ActionHandler("edit","ArtikelEdit");
    $this->app->ActionHandler("copy","ArtikelCopy");
    $this->app->ActionHandler("list","ArtikelList");
    $this->app->ActionHandler("delete","ArtikelDelete");

    $this->app->Tpl->Set("HEADING","Artikel");    //$this->app->ActionHandlerListen($app);
  }

  function ArtikelCreate(){
    $this->app->Tpl->Set("HEADING","Artikel (Anlegen)");
      $this->app->PageBuilder->CreateGen("artikel_create.tpl");
  }

  function ArtikelEdit(){
    $this->app->Tpl->Set("HEADING","Artikel (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("artikel_edit.tpl");
  }

  function ArtikelCopy(){
    $this->app->Tpl->Set("HEADING","Artikel (Kopieren)");
      $this->app->PageBuilder->CreateGen("artikel_copy.tpl");
  }

  function ArtikelDelete(){
    $this->app->Tpl->Set("HEADING","Artikel (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("artikel_delete.tpl");
  }

  function ArtikelList(){
    $this->app->Tpl->Set("HEADING","Artikel (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("artikel_list.tpl");
  }

} 
?>