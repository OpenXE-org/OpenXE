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

class GenProjekte { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ProjekteCreate");
    $this->app->ActionHandler("edit","ProjekteEdit");
    $this->app->ActionHandler("copy","ProjekteCopy");
    $this->app->ActionHandler("list","ProjekteList");
    $this->app->ActionHandler("delete","ProjekteDelete");

    $this->app->Tpl->Set('HEADING',"Projekte");    $this->app->ActionHandlerListen($app);
  }

  function ProjekteCreate(){
    $this->app->Tpl->Set('HEADING',"Projekte (Anlegen)");
      $this->app->PageBuilder->CreateGen("projekte_create.tpl");
  }

  function ProjekteEdit(){
    $this->app->Tpl->Set('HEADING',"Projekte (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("projekte_edit.tpl");
  }

  function ProjekteCopy(){
    $this->app->Tpl->Set('HEADING',"Projekte (Kopieren)");
      $this->app->PageBuilder->CreateGen("projekte_copy.tpl");
  }

  function ProjekteDelete(){
    $this->app->Tpl->Set('HEADING',"Projekte (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("projekte_delete.tpl");
  }

  function ProjekteList(){
    $this->app->Tpl->Set('HEADING',"Projekte (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("projekte_list.tpl");
  }

} 
?>