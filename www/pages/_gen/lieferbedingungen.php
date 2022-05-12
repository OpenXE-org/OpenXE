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

class GenLieferbedingungen { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","LieferbedingungenCreate");
    $this->app->ActionHandler("edit","LieferbedingungenEdit");
    $this->app->ActionHandler("copy","LieferbedingungenCopy");
    $this->app->ActionHandler("list","LieferbedingungenList");
    $this->app->ActionHandler("delete","LieferbedingungenDelete");

    $this->app->Tpl->Set("HEADING","Lieferbedingungen");    //$this->app->ActionHandlerListen($app);
  }

  function LieferbedingungenCreate(){
    $this->app->Tpl->Set("HEADING","Lieferbedingungen (Anlegen)");
      $this->app->PageBuilder->CreateGen("lieferbedingungen_create.tpl");
  }

  function LieferbedingungenEdit(){
    $this->app->Tpl->Set("HEADING","Lieferbedingungen (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("lieferbedingungen_edit.tpl");
  }

  function LieferbedingungenCopy(){
    $this->app->Tpl->Set("HEADING","Lieferbedingungen (Kopieren)");
      $this->app->PageBuilder->CreateGen("lieferbedingungen_copy.tpl");
  }

  function LieferbedingungenDelete(){
    $this->app->Tpl->Set("HEADING","Lieferbedingungen (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("lieferbedingungen_delete.tpl");
  }

  function LieferbedingungenList(){
    $this->app->Tpl->Set("HEADING","Lieferbedingungen (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("lieferbedingungen_list.tpl");
  }

} 
?>