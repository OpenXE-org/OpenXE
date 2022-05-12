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

class GenAufgabe { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AufgabeCreate");
    $this->app->ActionHandler("edit","AufgabeEdit");
    $this->app->ActionHandler("copy","AufgabeCopy");
    $this->app->ActionHandler("list","AufgabeList");
    $this->app->ActionHandler("delete","AufgabeDelete");

    $this->app->Tpl->Set("HEADING","Aufgabe");    //$this->app->ActionHandlerListen($app);
  }

  function AufgabeCreate(){
    $this->app->Tpl->Set("HEADING","Aufgabe (Anlegen)");
      $this->app->PageBuilder->CreateGen("aufgabe_create.tpl");
  }

  function AufgabeEdit(){
    $this->app->Tpl->Set("HEADING","Aufgabe (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("aufgabe_edit.tpl");
  }

  function AufgabeCopy(){
    $this->app->Tpl->Set("HEADING","Aufgabe (Kopieren)");
      $this->app->PageBuilder->CreateGen("aufgabe_copy.tpl");
  }

  function AufgabeDelete(){
    $this->app->Tpl->Set("HEADING","Aufgabe (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("aufgabe_delete.tpl");
  }

  function AufgabeList(){
    $this->app->Tpl->Set("HEADING","Aufgabe (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("aufgabe_list.tpl");
  }

} 
?>