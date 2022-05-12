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

class GenBerichte { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","BerichteCreate");
    $this->app->ActionHandler("edit","BerichteEdit");
    $this->app->ActionHandler("copy","BerichteCopy");
    $this->app->ActionHandler("list","BerichteList");
    $this->app->ActionHandler("delete","BerichteDelete");

    $this->app->Tpl->Set("HEADING","Berichte");    //$this->app->ActionHandlerListen($app);
  }

  function BerichteCreate(){
    $this->app->Tpl->Set("HEADING","Berichte (Anlegen)");
      $this->app->PageBuilder->CreateGen("berichte_create.tpl");
  }

  function BerichteEdit(){
    $this->app->Tpl->Set("HEADING","Berichte (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("berichte_edit.tpl");
  }

  function BerichteCopy(){
    $this->app->Tpl->Set("HEADING","Berichte (Kopieren)");
      $this->app->PageBuilder->CreateGen("berichte_copy.tpl");
  }

  function BerichteDelete(){
    $this->app->Tpl->Set("HEADING","Berichte (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("berichte_delete.tpl");
  }

  function BerichteList(){
    $this->app->Tpl->Set("HEADING","Berichte (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("berichte_list.tpl");
  }

} 
?>