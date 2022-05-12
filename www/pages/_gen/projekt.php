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

class GenProjekt { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ProjektCreate");
    $this->app->ActionHandler("edit","ProjektEdit");
    $this->app->ActionHandler("copy","ProjektCopy");
    $this->app->ActionHandler("list","ProjektList");
    $this->app->ActionHandler("delete","ProjektDelete");

    $this->app->Tpl->Set("HEADING","Projekt");    //$this->app->ActionHandlerListen($app);
  }

  function ProjektCreate(){
    $this->app->Tpl->Set("HEADING","Projekt (Anlegen)");
      $this->app->PageBuilder->CreateGen("projekt_create.tpl");
  }

  function ProjektEdit(){
    $this->app->Tpl->Set("HEADING","Projekt (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("projekt_edit.tpl");
  }

  function ProjektCopy(){
    $this->app->Tpl->Set("HEADING","Projekt (Kopieren)");
      $this->app->PageBuilder->CreateGen("projekt_copy.tpl");
  }

  function ProjektDelete(){
    $this->app->Tpl->Set("HEADING","Projekt (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("projekt_delete.tpl");
  }

  function ProjektList(){
    $this->app->Tpl->Set("HEADING","Projekt (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("projekt_list.tpl");
  }

} 
?>