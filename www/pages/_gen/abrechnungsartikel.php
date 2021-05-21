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

class GenAbrechnungsartikel { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AbrechnungsartikelCreate");
    $this->app->ActionHandler("edit","AbrechnungsartikelEdit");
    $this->app->ActionHandler("copy","AbrechnungsartikelCopy");
    $this->app->ActionHandler("list","AbrechnungsartikelList");
    $this->app->ActionHandler("delete","AbrechnungsartikelDelete");

    $this->app->Tpl->Set("HEADING","Abrechnungsartikel");    //$this->app->ActionHandlerListen($app);
  }

  function AbrechnungsartikelCreate(){
    $this->app->Tpl->Set("HEADING","Abrechnungsartikel (Anlegen)");
      $this->app->PageBuilder->CreateGen("abrechnungsartikel_create.tpl");
  }

  function AbrechnungsartikelEdit(){
    $this->app->Tpl->Set("HEADING","Abrechnungsartikel (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("abrechnungsartikel_edit.tpl");
  }

  function AbrechnungsartikelCopy(){
    $this->app->Tpl->Set("HEADING","Abrechnungsartikel (Kopieren)");
      $this->app->PageBuilder->CreateGen("abrechnungsartikel_copy.tpl");
  }

  function AbrechnungsartikelDelete(){
    $this->app->Tpl->Set("HEADING","Abrechnungsartikel (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("abrechnungsartikel_delete.tpl");
  }

  function AbrechnungsartikelList(){
    $this->app->Tpl->Set("HEADING","Abrechnungsartikel (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("abrechnungsartikel_list.tpl");
  }

} 
?>