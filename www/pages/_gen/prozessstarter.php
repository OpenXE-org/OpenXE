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

class GenProzessstarter { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ProzessstarterCreate");
    $this->app->ActionHandler("edit","ProzessstarterEdit");
    $this->app->ActionHandler("copy","ProzessstarterCopy");
    $this->app->ActionHandler("list","ProzessstarterList");
    $this->app->ActionHandler("delete","ProzessstarterDelete");

    $this->app->Tpl->Set("HEADING","Prozessstarter");    $this->app->ActionHandlerListen($app);
  }

  function ProzessstarterCreate(){
    $this->app->Tpl->Set("HEADING","Prozessstarter (Anlegen)");
      $this->app->PageBuilder->CreateGen("prozessstarter_create.tpl");
  }

  function ProzessstarterEdit(){
    $this->app->Tpl->Set("HEADING","Prozessstarter (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("prozessstarter_edit.tpl");
  }

  function ProzessstarterCopy(){
    $this->app->Tpl->Set("HEADING","Prozessstarter (Kopieren)");
      $this->app->PageBuilder->CreateGen("prozessstarter_copy.tpl");
  }

  function ProzessstarterDelete(){
    $this->app->Tpl->Set("HEADING","Prozessstarter (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("prozessstarter_delete.tpl");
  }

  function ProzessstarterList(){
    $this->app->Tpl->Set("HEADING","Prozessstarter (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("prozessstarter_list.tpl");
  }

} 
?>