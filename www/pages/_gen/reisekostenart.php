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

class GenReisekostenart { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ReisekostenartCreate");
    $this->app->ActionHandler("edit","ReisekostenartEdit");
    $this->app->ActionHandler("copy","ReisekostenartCopy");
    $this->app->ActionHandler("list","ReisekostenartList");
    $this->app->ActionHandler("delete","ReisekostenartDelete");

    $this->app->Tpl->Set('HEADING',"Reisekostenart");    $this->app->ActionHandlerListen($app);
  }

  function ReisekostenartCreate(){
    $this->app->Tpl->Set('HEADING',"Reisekostenart (Anlegen)");
      $this->app->PageBuilder->CreateGen("reisekostenart_create.tpl");
  }

  function ReisekostenartEdit(){
    $this->app->Tpl->Set('HEADING',"Reisekostenart (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("reisekostenart_edit.tpl");
  }

  function ReisekostenartCopy(){
    $this->app->Tpl->Set('HEADING',"Reisekostenart (Kopieren)");
      $this->app->PageBuilder->CreateGen("reisekostenart_copy.tpl");
  }

  function ReisekostenartDelete(){
    $this->app->Tpl->Set('HEADING',"Reisekostenart (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("reisekostenart_delete.tpl");
  }

  function ReisekostenartList(){
    $this->app->Tpl->Set('HEADING',"Reisekostenart (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("reisekostenart_list.tpl");
  }

} 
?>