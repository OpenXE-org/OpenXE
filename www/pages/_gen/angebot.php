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

class GenAngebot { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AngebotCreate");
    $this->app->ActionHandler("edit","AngebotEdit");
    $this->app->ActionHandler("copy","AngebotCopy");
    $this->app->ActionHandler("list","AngebotList");
    $this->app->ActionHandler("delete","AngebotDelete");

    $this->app->Tpl->Set("HEADING","Angebot");    //$this->app->ActionHandlerListen($app);
  }

  function AngebotCreate(){
    $this->app->Tpl->Set("HEADING","Angebot (Anlegen)");
      $this->app->PageBuilder->CreateGen("angebot_create.tpl");
  }

  function AngebotEdit(){
    $this->app->Tpl->Set("HEADING","Angebot (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("angebot_edit.tpl");
  }

  function AngebotCopy(){
    $this->app->Tpl->Set("HEADING","Angebot (Kopieren)");
      $this->app->PageBuilder->CreateGen("angebot_copy.tpl");
  }

  function AngebotDelete(){
    $this->app->Tpl->Set("HEADING","Angebot (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("angebot_delete.tpl");
  }

  function AngebotList(){
    $this->app->Tpl->Set("HEADING","Angebot (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("angebot_list.tpl");
  }

} 
?>