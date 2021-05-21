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

class WidgetGenemailbackup
{

  private $app;            //application object  
  public $form;            //store form object  
  protected $parsetarget;    //target for content

  public function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function emailbackupDelete()
  {
    
    $this->form->Execute("emailbackup","delete");

    $this->emailbackupList();
  }

  function Edit()
  {
    $this->form->Edit();
  }

  function Copy()
  {
    $this->form->Copy();
  }

  public function Create()
  {
    $this->form->Create();
  }

  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"SUUUCHEEE");
  }

  public function Summary()
  {
    $this->app->Tpl->Set($this->parsetarget,"grosse Tabelle");
  }

  function Form()
  {
    $this->form = $this->app->FormHandler->CreateNew("emailbackup");
    $this->form->UseTable("emailbackup");
    $this->form->UseTemplate("emailbackup.tpl",$this->parsetarget);

    $field = new HTMLInput("angezeigtername","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("email","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("benutzername","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("internebeschreibung","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("passwort","password","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("server","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("imap_port","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("imap_type",0,"imap_type","","","0");
    $field->AddOption('IMAP','1');
    $field->AddOption('IMAP mit SSL','3');
    $field->AddOption('IMAP für Google','5');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("imap_sentfolder_aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("imap_sentfolder","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);


    $field = new HTMLCheckbox("smtp_extra","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("smtp","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("smtp_port","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("smtp_ssl",0,"smtp_ssl","","","0");
    $field->AddOption('Keine Verschl&uuml;sselung','0');
    $field->AddOption('TLS','1');
    $field->AddOption('SSL','2');
    $this->form->NewField($field);

    $field = new HTMLSelect("smtp_authtype",0,"smtp_authtype","","","0");
    $field->AddOption('Standard','');
    $field->AddOption('PHPMailer 6.1','smtp');
    $field->AddOption('Google OAuth','oauth_google');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("smtp_loglevel","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("smtp_frommail","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("smtp_fromname","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("client_alias","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("ticket",0,"ticket","","","0");
    $field->AddOption('deaktiviert','0');
    $field->AddOption('Ticket-System aktivieren','1');
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("ticketqueue","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("abdatum","text","","12","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ticketemaileingehend","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ticketloeschen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ticketabgeschlossen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("emailbackup",0,"emailbackup","","","0");
    $field->AddOption('deaktiviert','0');
    $field->AddOption('Archivierung aktivieren','1');
    $this->form->NewField($field);

    $field = new HTMLInput("loeschtage","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLSelect("autoresponder",0,"autoresponder","","","0");
    $field->AddOption('aus','0');
    $field->AddOption('aktiv','1');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autosresponder_blacklist","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("geschaeftsbriefvorlage","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("autoresponderbetreff","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("autorespondertext",10,80,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("eigenesignatur","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("signatur",10,80,"","","","","0");   
    $this->form->NewField($field);


  }

}

?>