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

class GenSpedition_Avi { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Spedition_AviCreate");
    $this->app->ActionHandler("edit","Spedition_AviEdit");
    $this->app->ActionHandler("copy","Spedition_AviCopy");
    $this->app->ActionHandler("list","Spedition_AviList");
    $this->app->ActionHandler("delete","Spedition_AviDelete");

    $this->app->Tpl->Set("HEADING","Spedition_Avi");    //$this->app->ActionHandlerListen($app);
  }

  function Spedition_AviCreate(){
    $this->app->Tpl->Set("HEADING","Spedition_Avi (Anlegen)");
      $this->app->PageBuilder->CreateGen("spedition_avi_create.tpl");
  }

  function Spedition_AviEdit(){
    $this->app->Tpl->Set("HEADING","Spedition_Avi (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("spedition_avi_edit.tpl");
  }

  function Spedition_AviCopy(){
    $this->app->Tpl->Set("HEADING","Spedition_Avi (Kopieren)");
      $this->app->PageBuilder->CreateGen("spedition_avi_copy.tpl");
  }

  function Spedition_AviDelete(){
    $this->app->Tpl->Set("HEADING","Spedition_Avi (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("spedition_avi_delete.tpl");
  }

  function Spedition_AviList(){
    $this->app->Tpl->Set("HEADING","Spedition_Avi (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("spedition_avi_list.tpl");
  }

} 
?>