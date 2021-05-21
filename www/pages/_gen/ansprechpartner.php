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

class GenAnsprechpartner { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AnsprechpartnerCreate");
    $this->app->ActionHandler("edit","AnsprechpartnerEdit");
    $this->app->ActionHandler("copy","AnsprechpartnerCopy");
    $this->app->ActionHandler("list","AnsprechpartnerList");
    $this->app->ActionHandler("delete","AnsprechpartnerDelete");

    $this->app->Tpl->Set("HEADING","Ansprechpartner");    //$this->app->ActionHandlerListen($app);
  }

  function AnsprechpartnerCreate(){
    $this->app->Tpl->Set("HEADING","Ansprechpartner (Anlegen)");
      $this->app->PageBuilder->CreateGen("ansprechpartner_create.tpl");
  }

  function AnsprechpartnerEdit(){
    $this->app->Tpl->Set("HEADING","Ansprechpartner (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("ansprechpartner_edit.tpl");
  }

  function AnsprechpartnerCopy(){
    $this->app->Tpl->Set("HEADING","Ansprechpartner (Kopieren)");
      $this->app->PageBuilder->CreateGen("ansprechpartner_copy.tpl");
  }

  function AnsprechpartnerDelete(){
    $this->app->Tpl->Set("HEADING","Ansprechpartner (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("ansprechpartner_delete.tpl");
  }

  function AnsprechpartnerList(){
    $this->app->Tpl->Set("HEADING","Ansprechpartner (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("ansprechpartner_list.tpl");
  }

} 
?>