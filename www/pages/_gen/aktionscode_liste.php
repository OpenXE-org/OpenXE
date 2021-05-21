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

class GenAktionscode_Liste { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Aktionscode_ListeCreate");
    $this->app->ActionHandler("edit","Aktionscode_ListeEdit");
    $this->app->ActionHandler("copy","Aktionscode_ListeCopy");
    $this->app->ActionHandler("list","Aktionscode_ListeList");
    $this->app->ActionHandler("delete","Aktionscode_ListeDelete");

    $this->app->Tpl->Set('HEADING',"Aktionscode_Liste");    $this->app->ActionHandlerListen($app);
  }

  function Aktionscode_ListeCreate(){
    $this->app->Tpl->Set('HEADING',"Aktionscode_Liste (Anlegen)");
      $this->app->PageBuilder->CreateGen("aktionscode_liste_create.tpl");
  }

  function Aktionscode_ListeEdit(){
    $this->app->Tpl->Set('HEADING',"Aktionscode_Liste (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("aktionscode_liste_edit.tpl");
  }

  function Aktionscode_ListeCopy(){
    $this->app->Tpl->Set('HEADING',"Aktionscode_Liste (Kopieren)");
      $this->app->PageBuilder->CreateGen("aktionscode_liste_copy.tpl");
  }

  function Aktionscode_ListeDelete(){
    $this->app->Tpl->Set('HEADING',"Aktionscode_Liste (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("aktionscode_liste_delete.tpl");
  }

  function Aktionscode_ListeList(){
    $this->app->Tpl->Set('HEADING',"Aktionscode_Liste (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("aktionscode_liste_list.tpl");
  }

} 
?>