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

class GenArbeitsnachweis { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArbeitsnachweisCreate");
    $this->app->ActionHandler("edit","ArbeitsnachweisEdit");
    $this->app->ActionHandler("copy","ArbeitsnachweisCopy");
    $this->app->ActionHandler("list","ArbeitsnachweisList");
    $this->app->ActionHandler("delete","ArbeitsnachweisDelete");

    $this->app->Tpl->Set("HEADING","Arbeitsnachweis");    //$this->app->ActionHandlerListen($app);
  }

  function ArbeitsnachweisCreate(){
    $this->app->Tpl->Set("HEADING","Arbeitsnachweis (Anlegen)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_create.tpl");
  }

  function ArbeitsnachweisEdit(){
    $this->app->Tpl->Set("HEADING","Arbeitsnachweis (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_edit.tpl");
  }

  function ArbeitsnachweisCopy(){
    $this->app->Tpl->Set("HEADING","Arbeitsnachweis (Kopieren)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_copy.tpl");
  }

  function ArbeitsnachweisDelete(){
    $this->app->Tpl->Set("HEADING","Arbeitsnachweis (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_delete.tpl");
  }

  function ArbeitsnachweisList(){
    $this->app->Tpl->Set("HEADING","Arbeitsnachweis (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("arbeitsnachweis_list.tpl");
  }

} 
?>