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

class GenArtikelkategorien { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArtikelkategorienCreate");
    $this->app->ActionHandler("edit","ArtikelkategorienEdit");
    $this->app->ActionHandler("copy","ArtikelkategorienCopy");
    $this->app->ActionHandler("list","ArtikelkategorienList");
    $this->app->ActionHandler("delete","ArtikelkategorienDelete");

    $this->app->Tpl->Set("HEADING","Artikelkategorien");    //$this->app->ActionHandlerListen($app);
  }

  function ArtikelkategorienCreate(){
    $this->app->Tpl->Set("HEADING","Artikelkategorien (Anlegen)");
      $this->app->PageBuilder->CreateGen("artikelkategorien_create.tpl");
  }

  function ArtikelkategorienEdit(){
    $this->app->Tpl->Set("HEADING","Artikelkategorien (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("artikelkategorien_edit.tpl");
  }

  function ArtikelkategorienCopy(){
    $this->app->Tpl->Set("HEADING","Artikelkategorien (Kopieren)");
      $this->app->PageBuilder->CreateGen("artikelkategorien_copy.tpl");
  }

  function ArtikelkategorienDelete(){
    $this->app->Tpl->Set("HEADING","Artikelkategorien (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("artikelkategorien_delete.tpl");
  }

  function ArtikelkategorienList(){
    $this->app->Tpl->Set("HEADING","Artikelkategorien (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("artikelkategorien_list.tpl");
  }

} 
?>