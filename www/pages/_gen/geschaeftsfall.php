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

class GenGeschaeftsfall { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","GeschaeftsfallCreate");
    $this->app->ActionHandler("edit","GeschaeftsfallEdit");
    $this->app->ActionHandler("copy","GeschaeftsfallCopy");
    $this->app->ActionHandler("list","GeschaeftsfallList");
    $this->app->ActionHandler("delete","GeschaeftsfallDelete");

    $this->app->Tpl->Set('HEADING',"Geschaeftsfall");    $this->app->ActionHandlerListen($app);
  }

  function GeschaeftsfallCreate(){
    $this->app->Tpl->Set('HEADING',"Geschaeftsfall (Anlegen)");
      $this->app->PageBuilder->CreateGen("geschaeftsfall_create.tpl");
  }

  function GeschaeftsfallEdit(){
    $this->app->Tpl->Set('HEADING',"Geschaeftsfall (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("geschaeftsfall_edit.tpl");
  }

  function GeschaeftsfallCopy(){
    $this->app->Tpl->Set('HEADING',"Geschaeftsfall (Kopieren)");
      $this->app->PageBuilder->CreateGen("geschaeftsfall_copy.tpl");
  }

  function GeschaeftsfallDelete(){
    $this->app->Tpl->Set('HEADING',"Geschaeftsfall (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("geschaeftsfall_delete.tpl");
  }

  function GeschaeftsfallList(){
    $this->app->Tpl->Set('HEADING',"Geschaeftsfall (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("geschaeftsfall_list.tpl");
  }

} 
?>