<?php

/*
 * Copyright (c) 2022 OpenXE project
 */

use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Components\Logger\Logger;

class Emailbackup {

    function __construct($app, $intern = false) {
        $this->app = $app;
        if ($intern)
            return;

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "emailbackup_list");        
        $this->app->ActionHandler("create", "emailbackup_edit"); // This automatically adds a "New" button
        $this->app->ActionHandler("edit", "emailbackup_edit");
        $this->app->ActionHandler("delete", "emailbackup_delete");
        $this->app->ActionHandler("test_smtp",'emailbackup_test_smtp');
        $this->app->ActionHandler("test_imap",'emailbackup_test_imap');

        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install() {
        /* Fill out manually later */
    }

    static function TableSearch(&$app, $name, $erlaubtevars) {
        switch ($name) {
            case "emailbackup_list":
                $allowed['emailbackup_list'] = array('list');
//                $heading = array('angezeigtername', 'internebeschreibung', 'benutzername', 'passwort', 'server', 'smtp', 'ticket', 'imap_sentfolder_aktiv', 'imap_sentfolder', 'imap_port', 'imap_type', 'autoresponder', 'geschaeftsbriefvorlage', 'autoresponderbetreff', 'autorespondertext', 'projekt', 'emailbackup', 'adresse', 'firma', 'loeschtage', 'geloescht', 'ticketloeschen', 'ticketabgeschlossen', 'ticketqueue', 'ticketprojekt', 'ticketemaileingehend', 'smtp_extra', 'smtp_ssl', 'smtp_port', 'smtp_frommail', 'smtp_fromname', 'client_alias', 'smtp_authtype', 'smtp_authparam', 'smtp_loglevel', 'autosresponder_blacklist', 'eigenesignatur', 'signatur', 'mutex', 'abdatum', 'email', 'Men&uuml;');
                $heading = array('email', 'angezeigtername', 'internebeschreibung', 'benutzername', 'server', 'smtp', 'ticket', 'emailbackup', 'Men&uuml;');                
$width = array('10%'); // Fill out manually later

                $findcols = array('angezeigtername', 'internebeschreibung', 'benutzername', 'passwort', 'server', 'smtp', 'ticket', 'imap_sentfolder_aktiv', 'imap_sentfolder', 'imap_port', 'imap_type', 'autoresponder', 'geschaeftsbriefvorlage', 'autoresponderbetreff', 'autorespondertext', 'projekt', 'emailbackup', 'adresse', 'firma', 'loeschtage', 'geloescht', 'ticketloeschen', 'ticketabgeschlossen', 'ticketqueue', 'ticketprojekt', 'ticketemaileingehend', 'smtp_extra', 'smtp_ssl', 'smtp_port', 'smtp_frommail', 'smtp_fromname', 'client_alias', 'smtp_authtype', 'smtp_authparam', 'smtp_loglevel', 'autosresponder_blacklist', 'eigenesignatur', 'signatur', 'mutex', 'abdatum', 'email');
                $searchsql = array('angezeigtername', 'internebeschreibung', 'benutzername', 'passwort', 'server', 'smtp', 'ticket', 'imap_sentfolder_aktiv', 'imap_sentfolder', 'imap_port', 'imap_type', 'autoresponder', 'geschaeftsbriefvorlage', 'autoresponderbetreff', 'autorespondertext', 'projekt', 'emailbackup', 'adresse', 'firma', 'loeschtage', 'geloescht', 'ticketloeschen', 'ticketabgeschlossen', 'ticketqueue', 'ticketprojekt', 'ticketemaileingehend', 'smtp_extra', 'smtp_ssl', 'smtp_port', 'smtp_frommail', 'smtp_fromname', 'client_alias', 'smtp_authtype', 'smtp_authparam', 'smtp_loglevel', 'autosresponder_blacklist', 'eigenesignatur', 'signatur', 'mutex', 'abdatum', 'email');

                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>" . "<a href=\"index.php?module=emailbackup&action=edit&id=%value%\"><img src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=emailbackup&action=delete&id=%value%\");>" . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\"></a>" . "</td></tr></table>";

//                $sql = "SELECT id, angezeigtername, internebeschreibung, benutzername, passwort, server, smtp, ticket, imap_sentfolder_aktiv, imap_sentfolder, imap_port, imap_type, autoresponder, geschaeftsbriefvorlage, autoresponderbetreff, autorespondertext, projekt, emailbackup, adresse, firma, loeschtage, geloescht, ticketloeschen, ticketabgeschlossen, ticketqueue, ticketprojekt, ticketemaileingehend, smtp_extra, smtp_ssl, smtp_port, smtp_frommail, smtp_fromname, client_alias, smtp_authtype, smtp_authparam, smtp_loglevel, autosresponder_blacklist, eigenesignatur, signatur, mutex, abdatum, email, id FROM emailbackup";
                $sql = "SELECT SQL_CALC_FOUND_ROWS id, email, angezeigtername, internebeschreibung, benutzername, server, smtp, ticket, emailbackup, id FROM emailbackup";

                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM emailbackup WHERE $where";
//                $groupby = "";

                break;
        }

        $erg = false;

        foreach ($erlaubtevars as $k => $v) {
            if (isset($$v)) {
                $erg[$v] = $$v;
            }
        }
        return $erg;
    }
    
    function emailbackup_list() {
        $this->app->erp->MenuEintrag("index.php?module=emailbackup&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=emailbackup&action=create", "Neu anlegen");

        $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list", "Zur&uuml;ck");

        $this->app->YUI->TableSearch('TAB1', 'emailbackup_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "emailbackup_list.tpl");
    }    

    public function emailbackup_delete() {
        $id = (int) $this->app->Secure->GetGET('id');
        
        $this->app->DB->Delete("DELETE FROM `emailbackup` WHERE `id` = '{$id}'");        
        $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Der Eintrag wurde gel&ouml;scht.</div>");        

        $this->emailbackup_list();
    } 

    /*
     * Edit emailbackup item
     * If id is empty, create a new one
     */
        
    function emailbackup_edit() {
        $id = $this->app->Secure->GetGET('id');
              
        $this->app->Tpl->Set('ID', $id);

        $this->app->erp->MenuEintrag("index.php?module=emailbackup&action=edit&id=$id", "Details");
        $this->app->erp->MenuEintrag("index.php?module=emailbackup&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
        $id = $this->app->Secure->GetGET('id');
        $input = $this->GetInput();
        $submit = $this->app->Secure->GetPOST('submit');
                
        if (empty($id)) {
            // New item
            $id = 'NULL';
        } 

        if ($submit != '')
        {

            // Write to database
            
            // Add checks here

            $columns = "id, ";
            $values = "$id, ";
            $update = "";
    
            $fix = "";

            foreach ($input as $key => $value) {
                $columns = $columns.$fix.$key;
                $values = $values.$fix."'".$value."'";
                $update = $update.$fix.$key." = '$value'";

                $fix = ", ";
            }

//            echo($columns."<br>");
//            echo($values."<br>");
//            echo($update."<br>");            

            $email_exists = $this->app->DB->Select("SELECT id FROM emailbackup WHERE email='".$input['email']."'");            

            if ($id == 'NULL' && !empty($email_exists)) {
              $this->app->Tpl->Set('MESSAGE', "<div class=\"error\">Account existiert bereits!</div>");
              $this->SetInput($input);              
              $this->app->Tpl->Parse('PAGE', "emailbackup_edit.tpl");  
              return;
            }
            else {
              $sql = "INSERT INTO emailbackup (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;
              $this->app->DB->Update($sql);

              if ($id == 'NULL') {
                  $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Das Element wurde erfolgreich angelegt.</div>");
                  header("Location: index.php?module=emailbackup&action=list&msg=$msg");
              } else {
                  $this->app->Tpl->Set('MESSAGE', "<div class=\"success\">Die Einstellungen wurden erfolgreich &uuml;bernommen.</div>");
              }
            } // New account
        } // Submit

        // Load values again from database
        $result = $this->app->DB->SelectArr("SELECT id, angezeigtername, internebeschreibung, benutzername, passwort, server, smtp, ticket, imap_sentfolder_aktiv, imap_sentfolder, imap_port, imap_type, autoresponder, geschaeftsbriefvorlage, autoresponderbetreff, autorespondertext, projekt, emailbackup, adresse, firma, loeschtage, geloescht, ticketloeschen, ticketabgeschlossen, ticketqueue, ticketprojekt, ticketemaileingehend, smtp_extra, smtp_ssl, smtp_port, smtp_frommail, smtp_fromname, client_alias, smtp_authtype, smtp_authparam, smtp_loglevel, autosresponder_blacklist, eigenesignatur, signatur, mutex, abdatum, email, id FROM emailbackup"." WHERE id=$id");

        foreach ($result[0] as $key => $value) {
            $this->app->Tpl->Set(strtoupper($key), $value);   
        }
             
        /*
         * Add displayed items later
         * 

        $this->app->Tpl->Add('KURZUEBERSCHRIFT2', $email);
        $this->app->Tpl->Add('EMAIL', $email);
        $this->app->Tpl->Add('ANGEZEIGTERNAME', $angezeigtername);         
         */

        $this->app->Tpl->Parse('PAGE', "emailbackup_edit.tpl");  
    }

    /**
     * Get all paramters from html form and save into $input
     */
    public function GetInput(): array {
        $input = array();
        //$input['EMAIL'] = $this->app->Secure->GetPOST('email');
        
        $input['angezeigtername'] = $this->app->Secure->GetPOST('angezeigtername');
	$input['internebeschreibung'] = $this->app->Secure->GetPOST('internebeschreibung');
	$input['benutzername'] = $this->app->Secure->GetPOST('benutzername');
	$input['passwort'] = $this->app->Secure->GetPOST('passwort');
	$input['server'] = $this->app->Secure->GetPOST('server');
	$input['smtp'] = $this->app->Secure->GetPOST('smtp');
	$input['ticket'] = $this->app->Secure->GetPOST('ticket');
	$input['imap_sentfolder_aktiv'] = $this->app->Secure->GetPOST('imap_sentfolder_aktiv');
	$input['imap_sentfolder'] = $this->app->Secure->GetPOST('imap_sentfolder');
	$input['imap_port'] = $this->app->Secure->GetPOST('imap_port');
	$input['imap_type'] = $this->app->Secure->GetPOST('imap_type');
	$input['autoresponder'] = $this->app->Secure->GetPOST('autoresponder');
	$input['geschaeftsbriefvorlage'] = $this->app->Secure->GetPOST('geschaeftsbriefvorlage');
	$input['autoresponderbetreff'] = $this->app->Secure->GetPOST('autoresponderbetreff');
	$input['autorespondertext'] = $this->app->Secure->GetPOST('autorespondertext');
	$input['projekt'] = $this->app->Secure->GetPOST('projekt');
	$input['emailbackup'] = $this->app->Secure->GetPOST('emailbackup');
	$input['adresse'] = $this->app->Secure->GetPOST('adresse');
	$input['firma'] = $this->app->Secure->GetPOST('firma');
	$input['loeschtage'] = $this->app->Secure->GetPOST('loeschtage');
	$input['geloescht'] = $this->app->Secure->GetPOST('geloescht');
	$input['ticketloeschen'] = $this->app->Secure->GetPOST('ticketloeschen');
	$input['ticketabgeschlossen'] = $this->app->Secure->GetPOST('ticketabgeschlossen');
	$input['ticketqueue'] = $this->app->Secure->GetPOST('ticketqueue');
	$input['ticketprojekt'] = $this->app->Secure->GetPOST('ticketprojekt');
	$input['ticketemaileingehend'] = $this->app->Secure->GetPOST('ticketemaileingehend');
	$input['smtp_extra'] = $this->app->Secure->GetPOST('smtp_extra');
	$input['smtp_ssl'] = $this->app->Secure->GetPOST('smtp_ssl');
	$input['smtp_port'] = $this->app->Secure->GetPOST('smtp_port');
	$input['smtp_frommail'] = $this->app->Secure->GetPOST('email'); // use only these
	$input['smtp_fromname'] = $this->app->Secure->GetPOST('angezeigtername'); // use only these
	$input['client_alias'] = $this->app->Secure->GetPOST('client_alias');
	$input['smtp_authtype'] = $this->app->Secure->GetPOST('smtp_authtype');
	$input['smtp_authparam'] = $this->app->Secure->GetPOST('smtp_authparam');
	$input['smtp_loglevel'] = $this->app->Secure->GetPOST('smtp_loglevel');
	$input['autosresponder_blacklist'] = $this->app->Secure->GetPOST('autosresponder_blacklist');
	$input['eigenesignatur'] = $this->app->Secure->GetPOST('eigenesignatur');
	$input['signatur'] = $this->app->Secure->GetPOST('signatur');
	$input['mutex'] = $this->app->Secure->GetPOST('mutex');
	$input['abdatum'] = $this->app->Secure->GetPOST('abdatum');
	$input['email'] = $this->app->Secure->GetPOST('email');
	

        return $input;
    }

    /*
     * Set all fields in the page corresponding to $input
     */
    function SetInput($input) {
        // $this->app->Tpl->Set('EMAIL', $input['email']);        
        
        $this->app->Tpl->Set('ANGEZEIGTERNAME', $input['angezeigtername']);
	$this->app->Tpl->Set('INTERNEBESCHREIBUNG', $input['internebeschreibung']);
	$this->app->Tpl->Set('BENUTZERNAME', $input['benutzername']);
	$this->app->Tpl->Set('PASSWORT', $input['passwort']);
	$this->app->Tpl->Set('SERVER', $input['server']);
	$this->app->Tpl->Set('SMTP', $input['smtp']);
	$this->app->Tpl->Set('TICKET', $input['ticket']);
	$this->app->Tpl->Set('IMAP_SENTFOLDER_AKTIV', $input['imap_sentfolder_aktiv']);
	$this->app->Tpl->Set('IMAP_SENTFOLDER', $input['imap_sentfolder']);
	$this->app->Tpl->Set('IMAP_PORT', $input['imap_port']);
	$this->app->Tpl->Set('IMAP_TYPE', $input['imap_type']);
	$this->app->Tpl->Set('AUTORESPONDER', $input['autoresponder']);
	$this->app->Tpl->Set('GESCHAEFTSBRIEFVORLAGE', $input['geschaeftsbriefvorlage']);
	$this->app->Tpl->Set('AUTORESPONDERBETREFF', $input['autoresponderbetreff']);
	$this->app->Tpl->Set('AUTORESPONDERTEXT', $input['autorespondertext']);
	$this->app->Tpl->Set('PROJEKT', $input['projekt']);
	$this->app->Tpl->Set('EMAILBACKUP', $input['emailbackup']);
	$this->app->Tpl->Set('ADRESSE', $input['adresse']);
	$this->app->Tpl->Set('FIRMA', $input['firma']);
	$this->app->Tpl->Set('LOESCHTAGE', $input['loeschtage']);
	$this->app->Tpl->Set('GELOESCHT', $input['geloescht']);
	$this->app->Tpl->Set('TICKETLOESCHEN', $input['ticketloeschen']);
	$this->app->Tpl->Set('TICKETABGESCHLOSSEN', $input['ticketabgeschlossen']);
	$this->app->Tpl->Set('TICKETQUEUE', $input['ticketqueue']);
	$this->app->Tpl->Set('TICKETPROJEKT', $input['ticketprojekt']);
	$this->app->Tpl->Set('TICKETEMAILEINGEHEND', $input['ticketemaileingehend']);
	$this->app->Tpl->Set('SMTP_EXTRA', $input['smtp_extra']);
	$this->app->Tpl->Set('SMTP_SSL', $input['smtp_ssl']);
	$this->app->Tpl->Set('SMTP_PORT', $input['smtp_port']);
	$this->app->Tpl->Set('CLIENT_ALIAS', $input['client_alias']);
	$this->app->Tpl->Set('SMTP_AUTHTYPE', $input['smtp_authtype']);
	$this->app->Tpl->Set('SMTP_AUTHPARAM', $input['smtp_authparam']);
	$this->app->Tpl->Set('SMTP_LOGLEVEL', $input['smtp_loglevel']);
	$this->app->Tpl->Set('AUTOSRESPONDER_BLACKLIST', $input['autosresponder_blacklist']);
	$this->app->Tpl->Set('EIGENESIGNATUR', $input['eigenesignatur']);
	$this->app->Tpl->Set('SIGNATUR', $input['signatur']);
	$this->app->Tpl->Set('MUTEX', $input['mutex']);
	$this->app->Tpl->Set('ABDATUM', $input['abdatum']);
	$this->app->Tpl->Set('EMAIL', $input['email']);
	
    }

  function emailbackup_test_smtp() {

    $id = $this->app->Secure->GetGET('id');

    $result = $this->app->DB->SelectArr("SELECT angezeigtername, email FROM emailbackup WHERE id='$id' LIMIT 1");

    if(
      $this->app->erp->MailSend(
        $result[0]['email'],
        $result[0]['angezeigtername'],
        $result[0]['email'],
        $result[0]['angezeigtername'],
        'OpenXE ERP: Testmail',
        'Dies ist eine Testmail für Account "'.$result[0]['email'].'".',
        '',0,false,'','',
        true
      )
    ) {
      $msg = $this->app->erp->base64_url_encode(
        '<div class="info">Die Testmail wurde erfolgreich versendet an '.$result[0]['email'].'. '.$this->app->erp->mail_error.'</div>'
      );
    }
    else {
      $msg = $this->app->erp->base64_url_encode(
        '<div class="error">Fehler beim Versenden der Testmail: '.$this->app->erp->mail_error.'</div>'
      );
    }
    $this->app->Location->execute("index.php?module=emailbackup&id=$id&action=edit&msg=$msg");
  }

  function emailbackup_test_imap() {

    $id = $this->app->Secure->GetGET('id');

    // get email Account
    /** @var EmailAccountGateway $accountGateway */
    $accountGateway = $this->app->Container->get('EmailAccountGateway');
    $account = $accountGateway->getEmailAccountById($id);

    if(!empty($account)) {
      /** @var Ticket $ticketModule */
      $ticketModule = $this->app->erp->LoadModul('ticket');
      /** @var MailClientFactory $factory */
      $factory = $this->app->Container->get('MailClientFactory');
      /** @var MailClientConfigProvider $configProvider */
      $configProvider = $this->app->Container->get('MailClientConfigProvider');
      /** @var TicketFormatter $formatHelper */
      $formatHelper = $this->app->Container->get('TicketFormatter');
      /** @var TicketImportHelperFactory $importHelperFactory */
      $importHelperFactory = $this->app->Container->get('TicketImportHelperFactory');

        /** @var Logger $logger */
        $logger = $this->app->Container->get('Logger');

      $logger->debug(
          'Start imap test {email}',
          ['email' => $account->getEmailAddress(), 'account' => $account]
      );
      // create mail client
      try {
        $mailConfig = $configProvider->createImapConfigFromAccount($account);
        $mailClient = $factory->createImapClient($mailConfig);
      } catch (Exception $e) {
        $logger->error('Failed to create email client', ['error' => (string)$e, 'account' => $account]);
        $msg = $this->app->erp->base64_url_encode(
        '<div class="error">Fehler IMAP Test: '.$this->app->erp->mail_error.'</div>');
        $error = true;
      }

      // connect mail client
      try {
        $mailClient->connect();
        } catch (Exception $e) {
          $logger->error('Error during imap connection', ['error' => (string)$e, 'account' => $account]);   
        $msg = $this->app->erp->base64_url_encode(
        '<div class="error">Fehler IMAP Test: '.$this->app->erp->mail_error.'</div>');
        $error = true;
      }

      // connet to INBOX folder
      try {
        $mailClient->selectFolder('INBOX');
      } catch (Exception $e) {
        $logger->error('Failed to select INBOX folder', ['error' => (string)$e, 'account' => $account]);
        $msg = $this->app->erp->base64_url_encode(
        '<div class="error">Fehler IMAP Test: '.$this->app->erp->mail_error.'</div>');
        $error = true;
      }

      $mailClient->expunge();
      $mailClient->disconnect();

        if (!$error) {
          $msg = $this->app->erp->base64_url_encode(
          '<div class="info">IMAP Verbindung erfolgreich!</div>');
          $logger->debug(
          'IMAP test ok {email}',
          ['email' => $account->getEmailAddress(), 'account' => $account]
      );

        }
    } else 
    {
        $msg = $this->app->erp->base64_url_encode(
        '<div class="error">Kein Account gefunden!</div>');
            
    }
    $this->app->Location->execute("index.php?module=emailbackup&id=$id&action=edit&msg=$msg");
  } 

}
