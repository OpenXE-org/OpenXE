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
include ("_gen/artikelgruppen.php");

class Artikelgruppen extends GenArtikelgruppen {
  var $app;
  
  function __construct(&$app) {
    //parent::GenArtikelgruppen($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArtikelgruppenCreate");
    $this->app->ActionHandler("delete","ArtikelgruppenDelete");
    $this->app->ActionHandler("edit","ArtikelgruppenEdit");
    $this->app->ActionHandler("list","ArtikelgruppenList");

    $this->app->ActionHandlerListen($app);
  }


  function ArtikelgruppenDelete()
  {
    $this->ArtikelgruppenMenu();
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Delete("DELETE FROM artikelgruppen WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=artikelgruppen&action=list");
    exit;
  }


  function ArtikelgruppenCreate()
  {
    $this->ArtikelgruppenMenu();
    parent::ArtikelgruppenCreate();
  }

  function ArtikelgruppenList()
  {
    $this->ArtikelgruppenMenu();
    parent::ArtikelgruppenList();
  }

  function ArtikelgruppenMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Add('KURZUEBERSCHRIFT',"Artikelgruppen");
    $this->app->erp->MenuEintrag("index.php?module=artikelgruppen&action=create","Artikelgruppe anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=artikelgruppen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


  function ArtikelgruppenEdit()
  {
    $this->ArtikelgruppenMenu();

    parent::ArtikelgruppenEdit();
  }





}

?>
