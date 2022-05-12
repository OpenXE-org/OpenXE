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

class GenInhalt { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","InhaltCreate");
    $this->app->ActionHandler("edit","InhaltEdit");
    $this->app->ActionHandler("copy","InhaltCopy");
    $this->app->ActionHandler("list","InhaltList");
    $this->app->ActionHandler("delete","InhaltDelete");

    $this->app->Tpl->Set('HEADING',"Inhalt");    $this->app->ActionHandlerListen($app);
  }

  function InhaltCreate(){
    $this->app->Tpl->Set('HEADING',"Inhalt (Anlegen)");
      $this->app->PageBuilder->CreateGen("inhalt_create.tpl");
  }

  function InhaltEdit(){
    $this->app->Tpl->Set('HEADING',"Inhalt (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("inhalt_edit.tpl");
  }

  function InhaltCopy(){
    $this->app->Tpl->Set('HEADING',"Inhalt (Kopieren)");
      $this->app->PageBuilder->CreateGen("inhalt_copy.tpl");
  }

  function InhaltDelete(){
    $this->app->Tpl->Set('HEADING',"Inhalt (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("inhalt_delete.tpl");
  }

  function InhaltList(){
    $this->app->Tpl->Set('HEADING',"Inhalt (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("inhalt_list.tpl");
  }

} 
?>