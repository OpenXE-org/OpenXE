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
class GenPos_Kassierer { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Pos_KassiererCreate");
    $this->app->ActionHandler("edit","Pos_KassiererEdit");
    $this->app->ActionHandler("copy","Pos_KassiererCopy");
    $this->app->ActionHandler("list","Pos_KassiererList");
    $this->app->ActionHandler("delete","Pos_KassiererDelete");

    $this->app->Tpl->Set('HEADING',"Pos_Kassierer");    $this->app->ActionHandlerListen($app);
  }

  function Pos_KassiererCreate(){
    $this->app->Tpl->Set('HEADING',"Pos_Kassierer (Anlegen)");
      $this->app->PageBuilder->CreateGen("pos_kassierer_create.tpl");
  }

  function Pos_KassiererEdit(){
    $this->app->Tpl->Set('HEADING',"Pos_Kassierer (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("pos_kassierer_edit.tpl");
  }

  function Pos_KassiererCopy(){
    $this->app->Tpl->Set('HEADING',"Pos_Kassierer (Kopieren)");
      $this->app->PageBuilder->CreateGen("pos_kassierer_copy.tpl");
  }

  function Pos_KassiererDelete(){
    $this->app->Tpl->Set('HEADING',"Pos_Kassierer (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("pos_kassierer_delete.tpl");
  }

  function Pos_KassiererList(){
    $this->app->Tpl->Set('HEADING',"Pos_Kassierer (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("pos_kassierer_list.tpl");
  }

} 
?>
