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

class GenWissensdatenbank { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","WissensdatenbankCreate");
    $this->app->ActionHandler("edit","WissensdatenbankEdit");
    $this->app->ActionHandler("copy","WissensdatenbankCopy");
    $this->app->ActionHandler("list","WissensdatenbankList");
    $this->app->ActionHandler("delete","WissensdatenbankDelete");

    $this->app->Tpl->Set("HEADING","Wissensdatenbank");    //$this->app->ActionHandlerListen($app);
  }

  function WissensdatenbankCreate(){
    $this->app->Tpl->Set("HEADING","Wissensdatenbank (Anlegen)");
      $this->app->PageBuilder->CreateGen("wissensdatenbank_create.tpl");
  }

  function WissensdatenbankEdit(){
    $this->app->Tpl->Set("HEADING","Wissensdatenbank (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("wissensdatenbank_edit.tpl");
  }

  function WissensdatenbankCopy(){
    $this->app->Tpl->Set("HEADING","Wissensdatenbank (Kopieren)");
      $this->app->PageBuilder->CreateGen("wissensdatenbank_copy.tpl");
  }

  function WissensdatenbankDelete(){
    $this->app->Tpl->Set("HEADING","Wissensdatenbank (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("wissensdatenbank_delete.tpl");
  }

  function WissensdatenbankList(){
    $this->app->Tpl->Set("HEADING","Wissensdatenbank (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("wissensdatenbank_list.tpl");
  }

} 
?>