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

class GenSpedition { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","SpeditionCreate");
    $this->app->ActionHandler("edit","SpeditionEdit");
    $this->app->ActionHandler("copy","SpeditionCopy");
    $this->app->ActionHandler("list","SpeditionList");
    $this->app->ActionHandler("delete","SpeditionDelete");

    $this->app->Tpl->Set("HEADING","Spedition");    //$this->app->ActionHandlerListen($app);
  }

  function SpeditionCreate(){
    $this->app->Tpl->Set("HEADING","Spedition (Anlegen)");
      $this->app->PageBuilder->CreateGen("spedition_create.tpl");
  }

  function SpeditionEdit(){
    $this->app->Tpl->Set("HEADING","Spedition (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("spedition_edit.tpl");
  }

  function SpeditionCopy(){
    $this->app->Tpl->Set("HEADING","Spedition (Kopieren)");
      $this->app->PageBuilder->CreateGen("spedition_copy.tpl");
  }

  function SpeditionDelete(){
    $this->app->Tpl->Set("HEADING","Spedition (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("spedition_delete.tpl");
  }

  function SpeditionList(){
    $this->app->Tpl->Set("HEADING","Spedition (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("spedition_list.tpl");
  }

} 
?>