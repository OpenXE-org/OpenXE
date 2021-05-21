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

class GenWaage_Artikel { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Waage_ArtikelCreate");
    $this->app->ActionHandler("edit","Waage_ArtikelEdit");
    $this->app->ActionHandler("copy","Waage_ArtikelCopy");
    $this->app->ActionHandler("list","Waage_ArtikelList");
    $this->app->ActionHandler("delete","Waage_ArtikelDelete");

    $this->app->Tpl->Set('HEADING',"Waage_Artikel");    $this->app->ActionHandlerListen($app);
  }

  function Waage_ArtikelCreate(){
    $this->app->Tpl->Set('HEADING',"Waage_Artikel (Anlegen)");
      $this->app->PageBuilder->CreateGen("waage_artikel_create.tpl");
  }

  function Waage_ArtikelEdit(){
    $this->app->Tpl->Set('HEADING',"Waage_Artikel (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("waage_artikel_edit.tpl");
  }

  function Waage_ArtikelCopy(){
    $this->app->Tpl->Set('HEADING',"Waage_Artikel (Kopieren)");
      $this->app->PageBuilder->CreateGen("waage_artikel_copy.tpl");
  }

  function Waage_ArtikelDelete(){
    $this->app->Tpl->Set('HEADING',"Waage_Artikel (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("waage_artikel_delete.tpl");
  }

  function Waage_ArtikelList(){
    $this->app->Tpl->Set('HEADING',"Waage_Artikel (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("waage_artikel_list.tpl");
  }

} 
?>