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

class GenRetoure { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","RetoureCreate");
    $this->app->ActionHandler("edit","RetoureEdit");
    $this->app->ActionHandler("copy","RetoureCopy");
    $this->app->ActionHandler("list","RetoureList");
    $this->app->ActionHandler("delete","RetoureDelete");

    $this->app->Tpl->Set("HEADING","Retoure");    //$this->app->ActionHandlerListen($app);
  }

  function RetoureCreate(){
    $this->app->Tpl->Set("HEADING","Retoure (Anlegen)");
      $this->app->PageBuilder->CreateGen("retoure_create.tpl");
  }

  function RetoureEdit(){
    $this->app->Tpl->Set("HEADING","Retoure (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("retoure_edit.tpl");
  }

  function RetoureCopy(){
    $this->app->Tpl->Set("HEADING","Retoure (Kopieren)");
      $this->app->PageBuilder->CreateGen("retoure_copy.tpl");
  }

  function RetoureDelete(){
    $this->app->Tpl->Set("HEADING","Retoure (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("retoure_delete.tpl");
  }

  function RetoureList(){
    $this->app->Tpl->Set("HEADING","Retoure (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("retoure_list.tpl");
  }

} 
?>