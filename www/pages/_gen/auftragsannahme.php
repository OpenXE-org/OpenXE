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

class GenAuftragsannahme { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AuftragsannahmeCreate");
    $this->app->ActionHandler("edit","AuftragsannahmeEdit");
    $this->app->ActionHandler("copy","AuftragsannahmeCopy");
    $this->app->ActionHandler("list","AuftragsannahmeList");
    $this->app->ActionHandler("delete","AuftragsannahmeDelete");

    $this->app->Tpl->Set('HEADING',"Auftragsannahme");    $this->app->ActionHandlerListen($app);
  }

  function AuftragsannahmeCreate(){
    $this->app->Tpl->Set('HEADING',"Auftragsannahme (Anlegen)");
      $this->app->PageBuilder->CreateGen("auftragsannahme_create.tpl");
  }

  function AuftragsannahmeEdit(){
    $this->app->Tpl->Set('HEADING',"Auftragsannahme (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("auftragsannahme_edit.tpl");
  }

  function AuftragsannahmeCopy(){
    $this->app->Tpl->Set('HEADING',"Auftragsannahme (Kopieren)");
      $this->app->PageBuilder->CreateGen("auftragsannahme_copy.tpl");
  }

  function AuftragsannahmeDelete(){
    $this->app->Tpl->Set('HEADING',"Auftragsannahme (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("auftragsannahme_delete.tpl");
  }

  function AuftragsannahmeList(){
    $this->app->Tpl->Set('HEADING',"Auftragsannahme (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("auftragsannahme_list.tpl");
  }

} 
?>
