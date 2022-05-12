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

class GenProhejt { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ProhejtCreate");
    $this->app->ActionHandler("edit","ProhejtEdit");
    $this->app->ActionHandler("copy","ProhejtCopy");
    $this->app->ActionHandler("list","ProhejtList");
    $this->app->ActionHandler("delete","ProhejtDelete");

    $this->app->Tpl->Set("HEADING","Prohejt");    //$this->app->ActionHandlerListen($app);
  }

  function ProhejtCreate(){
    $this->app->Tpl->Set("HEADING","Prohejt (Anlegen)");
      $this->app->PageBuilder->CreateGen("prohejt_create.tpl");
  }

  function ProhejtEdit(){
    $this->app->Tpl->Set("HEADING","Prohejt (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("prohejt_edit.tpl");
  }

  function ProhejtCopy(){
    $this->app->Tpl->Set("HEADING","Prohejt (Kopieren)");
      $this->app->PageBuilder->CreateGen("prohejt_copy.tpl");
  }

  function ProhejtDelete(){
    $this->app->Tpl->Set("HEADING","Prohejt (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("prohejt_delete.tpl");
  }

  function ProhejtList(){
    $this->app->Tpl->Set("HEADING","Prohejt (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("prohejt_list.tpl");
  }

} 
?>