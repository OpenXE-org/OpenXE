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

class GenService { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ServiceCreate");
    $this->app->ActionHandler("edit","ServiceEdit");
    $this->app->ActionHandler("copy","ServiceCopy");
    $this->app->ActionHandler("list","ServiceList");
    $this->app->ActionHandler("delete","ServiceDelete");

    $this->app->Tpl->Set("HEADING","Service");    //$this->app->ActionHandlerListen($app);
  }

  function ServiceCreate(){
    $this->app->Tpl->Set("HEADING","Service (Anlegen)");
      $this->app->PageBuilder->CreateGen("service_create.tpl");
  }

  function ServiceEdit(){
    $this->app->Tpl->Set("HEADING","Service (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("service_edit.tpl");
  }

  function ServiceCopy(){
    $this->app->Tpl->Set("HEADING","Service (Kopieren)");
      $this->app->PageBuilder->CreateGen("service_copy.tpl");
  }

  function ServiceDelete(){
    $this->app->Tpl->Set("HEADING","Service (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("service_delete.tpl");
  }

  function ServiceList(){
    $this->app->Tpl->Set("HEADING","Service (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("service_list.tpl");
  }

} 
?>