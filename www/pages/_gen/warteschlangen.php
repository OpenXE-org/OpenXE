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

class GenWarteschlangen { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","WarteschlangenCreate");
    $this->app->ActionHandler("edit","WarteschlangenEdit");
    $this->app->ActionHandler("copy","WarteschlangenCopy");
    $this->app->ActionHandler("list","WarteschlangenList");
    $this->app->ActionHandler("delete","WarteschlangenDelete");

    $this->app->Tpl->Set("HEADING","Warteschlangen");    //$this->app->ActionHandlerListen($app);
  }

  function WarteschlangenCreate(){
    $this->app->Tpl->Set("HEADING","Warteschlangen (Anlegen)");
      $this->app->PageBuilder->CreateGen("warteschlangen_create.tpl");
  }

  function WarteschlangenEdit(){
    $this->app->Tpl->Set("HEADING","Warteschlangen (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("warteschlangen_edit.tpl");
  }

  function WarteschlangenCopy(){
    $this->app->Tpl->Set("HEADING","Warteschlangen (Kopieren)");
      $this->app->PageBuilder->CreateGen("warteschlangen_copy.tpl");
  }

  function WarteschlangenDelete(){
    $this->app->Tpl->Set("HEADING","Warteschlangen (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("warteschlangen_delete.tpl");
  }

  function WarteschlangenList(){
    $this->app->Tpl->Set("HEADING","Warteschlangen (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("warteschlangen_list.tpl");
  }

} 
?>