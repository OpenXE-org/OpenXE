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
include ("_gen/adresse_typ.php");

class Adresse_typ extends GenAdresse_typ {
  var $app;
  
  function __construct(&$app) {
    //parent::GenAdresse_typ($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Adresse_typCreate");
    $this->app->ActionHandler("edit","Adresse_typEdit");
    $this->app->ActionHandler("list","Adresse_typList");
    $this->app->ActionHandler("delete","Adresse_typDelete");

    $this->app->erp->Headlines('Adresse Typ');

    $this->app->ActionHandlerListen($app);
  }


  function Adresse_typCreate()
  {
    $this->Adresse_typMenu();
    parent::Adresse_typCreate();
  }

  function Adresse_typList()
  {
    $this->Adresse_typMenu();
    parent::Adresse_typList();
  }

  function Adresse_typMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=adresse_typ&action=create","Neue Versandart anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    {
      $this->app->erp->MenuEintrag("index.php?module=adresse_typ&action=list","&Uuml;bersicht");
      $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    }
    else if($this->app->Secure->GetGET("action")=="edit")
      $this->app->erp->MenuEintrag("index.php?module=adresse_typ&action=edit&id=$id","Details");
    else
      $this->app->erp->MenuEintrag("index.php?module=adresse_typ&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }



  function Adresse_typEdit()
  {
    $this->Adresse_typMenu();
    parent::Adresse_typEdit();
  }

  function Adresse_typDelete()
  {
    $id = $this->app->Secure->GetGET("id");

    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Versandart wurde gel&ouml;scht!</div>");
    $this->app->DB->Update("DELETE FROM adresse_typ WHERE id='$id' LIMIT 1");

    header("Location: index.php?module=adresse_typ&action=list&msg=$msg");  
    exit;
  } 



}

