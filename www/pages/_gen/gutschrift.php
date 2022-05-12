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

class GenGutschrift { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","GutschriftCreate");
    $this->app->ActionHandler("edit","GutschriftEdit");
    $this->app->ActionHandler("copy","GutschriftCopy");
    $this->app->ActionHandler("list","GutschriftList");
    $this->app->ActionHandler("delete","GutschriftDelete");

    $this->app->Tpl->Set("HEADING","Gutschrift");    //$this->app->ActionHandlerListen($app);
  }

  function GutschriftCreate(){
    $this->app->Tpl->Set("HEADING","Gutschrift (Anlegen)");
      $this->app->PageBuilder->CreateGen("gutschrift_create.tpl");
  }

  function GutschriftEdit(){
    $this->app->Tpl->Set("HEADING","Gutschrift (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("gutschrift_edit.tpl");
  }

  function GutschriftCopy(){
    $this->app->Tpl->Set("HEADING","Gutschrift (Kopieren)");
      $this->app->PageBuilder->CreateGen("gutschrift_copy.tpl");
  }

  function GutschriftDelete(){
    $this->app->Tpl->Set("HEADING","Gutschrift (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("gutschrift_delete.tpl");
  }

  function GutschriftList(){
    $this->app->Tpl->Set("HEADING","Gutschrift (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("gutschrift_list.tpl");
  }

} 
?>