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
include ("_gen/artikeleinheit.php");

class Artikeleinheit extends GenArtikeleinheit {
  var $app;
  
  function __construct(&$app) {
    //parent::GenArtikeleinheit($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArtikeleinheitCreate");
    $this->app->ActionHandler("edit","ArtikeleinheitEdit");
   	$this->app->ActionHandler("list","ArtikeleinheitList");
   	$this->app->ActionHandler("delete","ArtikeleinheitDelete");

    $this->app->ActionHandlerListen($app);
  }


  function ArtikeleinheitCreate()
  {
    $this->ArtikeleinheitMenu();
    parent::ArtikeleinheitCreate();
  }

	function ArtikeleinheitDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		if(is_numeric($id))
		{
			$this->app->DB->Delete("DELETE FROM artikeleinheit WHERE id='$id'");
		}

		$this->ArtikeleinheitList();
	}


  function ArtikeleinheitList()
  {
    $this->ArtikeleinheitMenu();
    parent::ArtikeleinheitList();
  }

  function ArtikeleinheitMenu()
  {
    $id = (int)$this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=artikeleinheit&action=create","Neu");
    if($this->app->Secure->GetGET("action")=="list")
      $this->app->erp->MenuEintrag("index.php?module=artikeleinheit&action=list","&Uuml;bersicht");  
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=artikeleinheit&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function ArtikeleinheitEdit()
  {
    $this->ArtikeleinheitMenu();
    parent::ArtikeleinheitEdit();
  }





}
