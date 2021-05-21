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

class Geraete  {
  var $app;
  
  function __construct(&$app) {
    $this->app=&$app;

    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
      $this->app->Tpl->Set('SUBHEADING',": ".
        $this->app->DB->Select("SELECT nummer FROM artikel WHERE id=$id LIMIT 1"));

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","GeraeteCreate");
    $this->app->ActionHandler("edit","GeraeteEdit");
    $this->app->ActionHandler("list","GeraeteList");


    $this->app->ActionHandlerListen($app);

  }


  function GeraeteCreate()
  {
    $this->app->Tpl->Add('TABS',
      "<a class=\"tab\" href=\"index.php?module=artikel&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a>");
  }

  function GeraeteList()
  {
    $this->app->Tpl->Add('KURZUEBERSCHRIFT',"Ger&auml;teverwaltung");
/*
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Drucker</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Adapter-Boxen</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Computer / Terminals</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Stationen</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Zugangstoken</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Backup Server</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">Netzwerkstruktur</a></li>");
    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=create\">&Uuml;bersicht</a></li>");
*/

    $this->app->Tpl->Parse('TAB1',"geraete.tpl");
    $this->app->Tpl->Set('TABTEXT',"Ger&auml;teverwaltung");
    $this->app->Tpl->Parse('PAGE',"tabview.tpl");

  }
  function GeraeteMenu()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Geraete</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">St&uuml;ckliste</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Verkauf</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Einkauf</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Projekte</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Lager</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Dateien</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=edit&id=$id\">Provisionen</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=etiketten&id=$id\">Etiketten</a>&nbsp;");
    //$this->app->Tpl->Add(TABS,"<a href=\"index.php?module=artikel&action=kosten&id=$id\">Gesamtkalkulation</a>&nbsp;");
    $this->app->Tpl->Add('TABS',"<a class=\"tab\" href=\"index.php?module=artikel&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a>&nbsp;");
  }


  function GeraeteEdit()
  {
    $this->GeraeteMenu();
    $this->app->Tpl->Set('TABLE_ADRESSE_KONTAKTHISTORIE',"TDB");
    $this->app->Tpl->Set('TABLE_ADRESSE_ROLLEN',"TDB");

    $this->app->Tpl->Set('TABLE_ADRESSE_USTID',"TDB");

  }





}

?>
