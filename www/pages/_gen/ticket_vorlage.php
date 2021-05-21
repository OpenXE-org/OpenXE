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

class GenTicket_Vorlage { 

  function __construct(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Ticket_VorlageCreate");
    $this->app->ActionHandler("edit","Ticket_VorlageEdit");
    $this->app->ActionHandler("copy","Ticket_VorlageCopy");
    $this->app->ActionHandler("list","Ticket_VorlageList");
    $this->app->ActionHandler("delete","Ticket_VorlageDelete");

    $this->app->Tpl->Set('HEADING',"Ticket_Vorlage");    $this->app->ActionHandlerListen($app);
  }

  function Ticket_VorlageCreate(){
    $this->app->Tpl->Set('HEADING',"Ticket_Vorlage (Anlegen)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_create.tpl");
  }

  function Ticket_VorlageEdit(){
    $this->app->Tpl->Set('HEADING',"Ticket_Vorlage (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_edit.tpl");
  }

  function Ticket_VorlageCopy(){
    $this->app->Tpl->Set('HEADING',"Ticket_Vorlage (Kopieren)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_copy.tpl");
  }

  function Ticket_VorlageDelete(){
    $this->app->Tpl->Set('HEADING',"Ticket_Vorlage (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_delete.tpl");
  }

  function Ticket_VorlageList(){
    $this->app->Tpl->Set('HEADING',"Ticket_Vorlage (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_list.tpl");
  }

} 
?>