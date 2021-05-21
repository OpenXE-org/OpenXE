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

class GenVerbindlichkeit { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","VerbindlichkeitCreate");
    $this->app->ActionHandler("edit","VerbindlichkeitEdit");
    $this->app->ActionHandler("copy","VerbindlichkeitCopy");
    $this->app->ActionHandler("list","VerbindlichkeitList");
    $this->app->ActionHandler("delete","VerbindlichkeitDelete");

    $this->app->Tpl->Set("HEADING","Verbindlichkeit");    //$this->app->ActionHandlerListen($app);
  }

  function VerbindlichkeitCreate(){
    $this->app->Tpl->Set("HEADING","Verbindlichkeit (Anlegen)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_create.tpl");
  }

  function VerbindlichkeitEdit(){
    $this->app->Tpl->Set("HEADING","Verbindlichkeit (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_edit.tpl");
  }

  function VerbindlichkeitCopy(){
    $this->app->Tpl->Set("HEADING","Verbindlichkeit (Kopieren)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_copy.tpl");
  }

  function VerbindlichkeitDelete(){
    $this->app->Tpl->Set("HEADING","Verbindlichkeit (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_delete.tpl");
  }

  function VerbindlichkeitList(){
    $this->app->Tpl->Set("HEADING","Verbindlichkeit (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_list.tpl");
  }

} 
?>