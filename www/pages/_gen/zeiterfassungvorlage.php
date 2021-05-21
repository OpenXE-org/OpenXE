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

class GenZeiterfassungvorlage { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ZeiterfassungvorlageCreate");
    $this->app->ActionHandler("edit","ZeiterfassungvorlageEdit");
    $this->app->ActionHandler("copy","ZeiterfassungvorlageCopy");
    $this->app->ActionHandler("list","ZeiterfassungvorlageList");
    $this->app->ActionHandler("delete","ZeiterfassungvorlageDelete");

    $this->app->Tpl->Set("HEADING","Zeiterfassungvorlage");    //$this->app->ActionHandlerListen($app);
  }

  function ZeiterfassungvorlageCreate(){
    $this->app->Tpl->Set("HEADING","Zeiterfassungvorlage (Anlegen)");
      $this->app->PageBuilder->CreateGen("zeiterfassungvorlage_create.tpl");
  }

  function ZeiterfassungvorlageEdit(){
    $this->app->Tpl->Set("HEADING","Zeiterfassungvorlage (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("zeiterfassungvorlage_edit.tpl");
  }

  function ZeiterfassungvorlageCopy(){
    $this->app->Tpl->Set("HEADING","Zeiterfassungvorlage (Kopieren)");
      $this->app->PageBuilder->CreateGen("zeiterfassungvorlage_copy.tpl");
  }

  function ZeiterfassungvorlageDelete(){
    $this->app->Tpl->Set("HEADING","Zeiterfassungvorlage (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("zeiterfassungvorlage_delete.tpl");
  }

  function ZeiterfassungvorlageList(){
    $this->app->Tpl->Set("HEADING","Zeiterfassungvorlage (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("zeiterfassungvorlage_list.tpl");
  }

} 
?>