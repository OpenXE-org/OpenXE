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

class GenStueckliste { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","StuecklisteCreate");
    $this->app->ActionHandler("edit","StuecklisteEdit");
    $this->app->ActionHandler("copy","StuecklisteCopy");
    $this->app->ActionHandler("list","StuecklisteList");
    $this->app->ActionHandler("delete","StuecklisteDelete");

    $this->app->Tpl->Set("HEADING","Stueckliste");    //$this->app->ActionHandlerListen($app);
  }

  function StuecklisteCreate(){
    $this->app->Tpl->Set("HEADING","Stueckliste (Anlegen)");
      $this->app->PageBuilder->CreateGen("stueckliste_create.tpl");
  }

  function StuecklisteEdit(){
    $this->app->Tpl->Set("HEADING","Stueckliste (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("stueckliste_edit.tpl");
  }

  function StuecklisteCopy(){
    $this->app->Tpl->Set("HEADING","Stueckliste (Kopieren)");
      $this->app->PageBuilder->CreateGen("stueckliste_copy.tpl");
  }

  function StuecklisteDelete(){
    $this->app->Tpl->Set("HEADING","Stueckliste (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("stueckliste_delete.tpl");
  }

  function StuecklisteList(){
    $this->app->Tpl->Set("HEADING","Stueckliste (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("stueckliste_list.tpl");
  }

} 
?>