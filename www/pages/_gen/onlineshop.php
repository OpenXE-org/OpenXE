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

class GenOnlineshop { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","OnlineshopCreate");
    $this->app->ActionHandler("edit","OnlineshopEdit");
    $this->app->ActionHandler("copy","OnlineshopCopy");
    $this->app->ActionHandler("list","OnlineshopList");
    $this->app->ActionHandler("delete","OnlineshopDelete");

    $this->app->Tpl->Set('HEADING',"Onlineshop");    $this->app->ActionHandlerListen($app);
  }

  function OnlineshopCreate(){
    $this->app->Tpl->Set('HEADING',"Onlineshop (Anlegen)");
      $this->app->PageBuilder->CreateGen("onlineshop_create.tpl");
  }

  function OnlineshopEdit(){
    $this->app->Tpl->Set('HEADING',"Onlineshop (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("onlineshop_edit.tpl");
  }

  function OnlineshopCopy(){
    $this->app->Tpl->Set('HEADING',"Onlineshop (Kopieren)");
      $this->app->PageBuilder->CreateGen("onlineshop_copy.tpl");
  }

  function OnlineshopDelete(){
    $this->app->Tpl->Set('HEADING',"Onlineshop (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("onlineshop_delete.tpl");
  }

  function OnlineshopList(){
    $this->app->Tpl->Set('HEADING',"Onlineshop (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("onlineshop_list.tpl");
  }

} 
?>