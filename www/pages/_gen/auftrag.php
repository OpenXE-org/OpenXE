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

class GenAuftrag { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AuftragCreate");
    $this->app->ActionHandler("edit","AuftragEdit");
    $this->app->ActionHandler("copy","AuftragCopy");
    $this->app->ActionHandler("list","AuftragList");
    $this->app->ActionHandler("delete","AuftragDelete");

    $this->app->Tpl->Set("HEADING","Auftrag");    //$this->app->ActionHandlerListen($app);
  }

  function AuftragCreate(){
    $this->app->Tpl->Set("HEADING","Auftrag (Anlegen)");
      $this->app->PageBuilder->CreateGen("auftrag_create.tpl");
  }

  function AuftragEdit(){
    $this->app->Tpl->Set("HEADING","Auftrag (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("auftrag_edit.tpl");
  }

  function AuftragCopy(){
    $this->app->Tpl->Set("HEADING","Auftrag (Kopieren)");
      $this->app->PageBuilder->CreateGen("auftrag_copy.tpl");
  }

  function AuftragDelete(){
    $this->app->Tpl->Set("HEADING","Auftrag (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("auftrag_delete.tpl");
  }

  function AuftragList(){
    $this->app->Tpl->Set("HEADING","Auftrag (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("auftrag_list.tpl");
  }

} 
?>