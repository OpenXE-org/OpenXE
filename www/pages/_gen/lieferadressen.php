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

class GenLieferadressen { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","LieferadressenCreate");
    $this->app->ActionHandler("edit","LieferadressenEdit");
    $this->app->ActionHandler("copy","LieferadressenCopy");
    $this->app->ActionHandler("list","LieferadressenList");
    $this->app->ActionHandler("delete","LieferadressenDelete");

    $this->app->Tpl->Set("HEADING","Lieferadressen");    //$this->app->ActionHandlerListen($app);
  }

  function LieferadressenCreate(){
    $this->app->Tpl->Set("HEADING","Lieferadressen (Anlegen)");
      $this->app->PageBuilder->CreateGen("lieferadressen_create.tpl");
  }

  function LieferadressenEdit(){
    $this->app->Tpl->Set("HEADING","Lieferadressen (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("lieferadressen_edit.tpl");
  }

  function LieferadressenCopy(){
    $this->app->Tpl->Set("HEADING","Lieferadressen (Kopieren)");
      $this->app->PageBuilder->CreateGen("lieferadressen_copy.tpl");
  }

  function LieferadressenDelete(){
    $this->app->Tpl->Set("HEADING","Lieferadressen (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("lieferadressen_delete.tpl");
  }

  function LieferadressenList(){
    $this->app->Tpl->Set("HEADING","Lieferadressen (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("lieferadressen_list.tpl");
  }

} 
?>