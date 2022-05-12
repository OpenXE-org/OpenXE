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

class ObjGenEmailbackup
{

  private  $id;
  private  $angezeigtername;
  private  $internebeschreibung;
  private  $benutzername;
  private  $passwort;
  private  $server;
  private  $smtp;
  private  $ticket;
  private  $imap_sentfolder_aktiv;
  private  $imap_sentfolder;
  private  $imap_port;
  private  $imap_type;
  private  $autoresponder;
  private  $geschaeftsbriefvorlage;
  private  $autoresponderbetreff;
  private  $autorespondertext;
  private  $projekt;
  private  $emailbackup;
  private  $adresse;
  private  $firma;
  private  $loeschtage;
  private  $geloescht;
  private  $ticketloeschen;
  private  $ticketabgeschlossen;
  private  $ticketqueue;
  private  $ticketprojekt;
  private  $ticketemaileingehend;
  private  $smtp_extra;
  private  $smtp_ssl;
  private  $smtp_port;
  private  $smtp_frommail;
  private  $smtp_fromname;
  private  $autosresponder_blacklist;
  private  $eigenesignatur;
  private  $signatur;
  private  $mutex;
  private  $abdatum;
  private  $email;
  private  $client_alias;
  private  $smtp_authtype;
  private  $smtp_authparam;
  private  $smtp_loglevel;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `emailbackup` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->angezeigtername=$result['angezeigtername'];
    $this->internebeschreibung=$result['internebeschreibung'];
    $this->benutzername=$result['benutzername'];
    $this->passwort=$result['passwort'];
    $this->server=$result['server'];
    $this->smtp=$result['smtp'];
    $this->ticket=$result['ticket'];
    $this->imap_sentfolder_aktiv=$result['imap_sentfolder_aktiv'];
    $this->imap_sentfolder=$result['imap_sentfolder'];
    $this->imap_port=$result['imap_port'];
    $this->imap_type=$result['imap_type'];
    $this->autoresponder=$result['autoresponder'];
    $this->geschaeftsbriefvorlage=$result['geschaeftsbriefvorlage'];
    $this->autoresponderbetreff=$result['autoresponderbetreff'];
    $this->autorespondertext=$result['autorespondertext'];
    $this->projekt=$result['projekt'];
    $this->emailbackup=$result['emailbackup'];
    $this->adresse=$result['adresse'];
    $this->firma=$result['firma'];
    $this->loeschtage=$result['loeschtage'];
    $this->geloescht=$result['geloescht'];
    $this->ticketloeschen=$result['ticketloeschen'];
    $this->ticketabgeschlossen=$result['ticketabgeschlossen'];
    $this->ticketqueue=$result['ticketqueue'];
    $this->ticketprojekt=$result['ticketprojekt'];
    $this->ticketemaileingehend=$result['ticketemaileingehend'];
    $this->smtp_extra=$result['smtp_extra'];
    $this->smtp_ssl=$result['smtp_ssl'];
    $this->smtp_port=$result['smtp_port'];
    $this->smtp_frommail=$result['smtp_frommail'];
    $this->smtp_fromname=$result['smtp_fromname'];
    $this->autosresponder_blacklist=$result['autosresponder_blacklist'];
    $this->eigenesignatur=$result['eigenesignatur'];
    $this->signatur=$result['signatur'];
    $this->mutex=$result['mutex'];
    $this->abdatum=$result['abdatum'];
    $this->email=$result['email'];
    $this->client_alias=$result['client_alias'];
    $this->smtp_authtype=$result['smtp_authtype'];
    $this->smtp_authparam=$result['smtp_authparam'];
    $this->smtp_loglevel=$result['smtp_loglevel'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `emailbackup` (`id`,`angezeigtername`,`internebeschreibung`,`benutzername`,`passwort`,`server`,`smtp`,`ticket`,`imap_sentfolder_aktiv`,`imap_sentfolder`,`imap_port`,`imap_type`,`autoresponder`,`geschaeftsbriefvorlage`,`autoresponderbetreff`,`autorespondertext`,`projekt`,`emailbackup`,`adresse`,`firma`,`loeschtage`,`geloescht`,`ticketloeschen`,`ticketabgeschlossen`,`ticketqueue`,`ticketprojekt`,`ticketemaileingehend`,`smtp_extra`,`smtp_ssl`,`smtp_port`,`smtp_frommail`,`smtp_fromname`,`autosresponder_blacklist`,`eigenesignatur`,`signatur`,`mutex`,`abdatum`,`email`,`client_alias`,`smtp_authtype`,`smtp_authparam`,`smtp_loglevel`)
      VALUES(NULL,'{$this->angezeigtername}','{$this->internebeschreibung}','{$this->benutzername}','{$this->passwort}','{$this->server}','{$this->smtp}','{$this->ticket}','{$this->imap_sentfolder_aktiv}','{$this->imap_sentfolder}','{$this->imap_port}','{$this->imap_type}','{$this->autoresponder}','{$this->geschaeftsbriefvorlage}','{$this->autoresponderbetreff}','{$this->autorespondertext}','{$this->projekt}','{$this->emailbackup}','{$this->adresse}','{$this->firma}','{$this->loeschtage}','{$this->geloescht}','{$this->ticketloeschen}','{$this->ticketabgeschlossen}','{$this->ticketqueue}','{$this->ticketprojekt}','{$this->ticketemaileingehend}','{$this->smtp_extra}','{$this->smtp_ssl}','{$this->smtp_port}','{$this->smtp_frommail}','{$this->smtp_fromname}','{$this->autosresponder_blacklist}','{$this->eigenesignatur}','{$this->signatur}','{$this->mutex}','{$this->abdatum}','{$this->email}','{$this->client_alias}','{$this->smtp_authtype}','{$this->smtp_authparam}','{$this->smtp_loglevel}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `emailbackup` SET
      `angezeigtername`='{$this->angezeigtername}',
      `internebeschreibung`='{$this->internebeschreibung}',
      `benutzername`='{$this->benutzername}',
      `passwort`='{$this->passwort}',
      `server`='{$this->server}',
      `smtp`='{$this->smtp}',
      `ticket`='{$this->ticket}',
      `imap_sentfolder_aktiv`='{$this->imap_sentfolder_aktiv}',
      `imap_sentfolder`='{$this->imap_sentfolder}',
      `imap_port`='{$this->imap_port}',
      `imap_type`='{$this->imap_type}',
      `autoresponder`='{$this->autoresponder}',
      `geschaeftsbriefvorlage`='{$this->geschaeftsbriefvorlage}',
      `autoresponderbetreff`='{$this->autoresponderbetreff}',
      `autorespondertext`='{$this->autorespondertext}',
      `projekt`='{$this->projekt}',
      `emailbackup`='{$this->emailbackup}',
      `adresse`='{$this->adresse}',
      `firma`='{$this->firma}',
      `loeschtage`='{$this->loeschtage}',
      `geloescht`='{$this->geloescht}',
      `ticketloeschen`='{$this->ticketloeschen}',
      `ticketabgeschlossen`='{$this->ticketabgeschlossen}',
      `ticketqueue`='{$this->ticketqueue}',
      `ticketprojekt`='{$this->ticketprojekt}',
      `ticketemaileingehend`='{$this->ticketemaileingehend}',
      `smtp_extra`='{$this->smtp_extra}',
      `smtp_ssl`='{$this->smtp_ssl}',
      `smtp_port`='{$this->smtp_port}',
      `smtp_frommail`='{$this->smtp_frommail}',
      `smtp_fromname`='{$this->smtp_fromname}',
      `autosresponder_blacklist`='{$this->autosresponder_blacklist}',
      `eigenesignatur`='{$this->eigenesignatur}',
      `signatur`='{$this->signatur}',
      `mutex`='{$this->mutex}',
      `abdatum`='{$this->abdatum}',
      `email`='{$this->email}',
      `client_alias`='{$this->client_alias}',
      `smtp_authtype`='{$this->smtp_authtype}',
      `smtp_authparam`='{$this->smtp_authparam}',
      `smtp_loglevel`='{$this->smtp_loglevel}'
      WHERE (`id`='{$this->id}')";

    $this->app->DB->Update($sql);
  }

  public function Delete($id='')
  {
    if(is_numeric($id))
    {
      $this->id=$id;
    }
    else
      return -1;

    $sql = "DELETE FROM `emailbackup` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->angezeigtername='';
    $this->internebeschreibung='';
    $this->benutzername='';
    $this->passwort='';
    $this->server='';
    $this->smtp='';
    $this->ticket='';
    $this->imap_sentfolder_aktiv='';
    $this->imap_sentfolder='';
    $this->imap_port='';
    $this->imap_type='';
    $this->autoresponder='';
    $this->geschaeftsbriefvorlage='';
    $this->autoresponderbetreff='';
    $this->autorespondertext='';
    $this->projekt='';
    $this->emailbackup='';
    $this->adresse='';
    $this->firma='';
    $this->loeschtage='';
    $this->geloescht='';
    $this->ticketloeschen='';
    $this->ticketabgeschlossen='';
    $this->ticketqueue='';
    $this->ticketprojekt='';
    $this->ticketemaileingehend='';
    $this->smtp_extra='';
    $this->smtp_ssl='';
    $this->smtp_port='';
    $this->smtp_frommail='';
    $this->smtp_fromname='';
    $this->autosresponder_blacklist='';
    $this->eigenesignatur='';
    $this->signatur='';
    $this->mutex='';
    $this->abdatum='';
    $this->email='';
    $this->client_alias='';
    $this->smtp_authtype='';
    $this->smtp_authparam='';
    $this->smtp_loglevel='';
  }

  public function Copy()
  {
    $this->id = '';
    $this->Create();
  }

 /** 
   Mit dieser Funktion kann man einen Datensatz suchen 
   dafuer muss man die Attribute setzen nach denen gesucht werden soll
   dann kriegt man als ergebnis den ersten Datensatz der auf die Suche uebereinstimmt
   zurueck. Mit Next() kann man sich alle weiteren Ergebnisse abholen
   **/ 

  public function Find()
  {
    //TODO Suche mit den werten machen
  }

  public function FindNext()
  {
    //TODO Suche mit den alten werten fortsetzen machen
  }

 /** Funktionen um durch die Tabelle iterieren zu koennen */ 

  public function Next()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

  public function First()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

 /** dank dieser funktionen kann man die tatsaechlichen werte einfach 
  ueberladen (in einem Objekt das mit seiner klasse ueber dieser steht)**/ 

  public function SetId($value) { $this->id=$value; }
  public function GetId() { return $this->id; }
  public function SetAngezeigtername($value) { $this->angezeigtername=$value; }
  public function GetAngezeigtername() { return $this->angezeigtername; }
  public function SetInternebeschreibung($value) { $this->internebeschreibung=$value; }
  public function GetInternebeschreibung() { return $this->internebeschreibung; }
  public function SetBenutzername($value) { $this->benutzername=$value; }
  public function GetBenutzername() { return $this->benutzername; }
  public function SetPasswort($value) { $this->passwort=$value; }
  public function GetPasswort() { return $this->passwort; }
  public function SetServer($value) { $this->server=$value; }
  public function GetServer() { return $this->server; }
  public function SetSmtp($value) { $this->smtp=$value; }
  public function GetSmtp() { return $this->smtp; }
  public function SetTicket($value) { $this->ticket=$value; }
  public function GetTicket() { return $this->ticket; }
  public function SetImap_Sentfolder_Aktiv($value) { $this->imap_sentfolder_aktiv=$value; }
  public function GetImap_Sentfolder_Aktiv() { return $this->imap_sentfolder_aktiv; }
  public function SetImap_Sentfolder($value) { $this->imap_sentfolder=$value; }
  public function GetImap_Sentfolder() { return $this->imap_sentfolder; }
  public function SetImap_Port($value) { $this->imap_port=$value; }
  public function GetImap_Port() { return $this->imap_port; }
  public function SetImap_Type($value) { $this->imap_type=$value; }
  public function GetImap_Type() { return $this->imap_type; }
  public function SetAutoresponder($value) { $this->autoresponder=$value; }
  public function GetAutoresponder() { return $this->autoresponder; }
  public function SetGeschaeftsbriefvorlage($value) { $this->geschaeftsbriefvorlage=$value; }
  public function GetGeschaeftsbriefvorlage() { return $this->geschaeftsbriefvorlage; }
  public function SetAutoresponderbetreff($value) { $this->autoresponderbetreff=$value; }
  public function GetAutoresponderbetreff() { return $this->autoresponderbetreff; }
  public function SetAutorespondertext($value) { $this->autorespondertext=$value; }
  public function GetAutorespondertext() { return $this->autorespondertext; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetEmailbackup($value) { $this->emailbackup=$value; }
  public function GetEmailbackup() { return $this->emailbackup; }
  public function SetAdresse($value) { $this->adresse=$value; }
  public function GetAdresse() { return $this->adresse; }
  public function SetFirma($value) { $this->firma=$value; }
  public function GetFirma() { return $this->firma; }
  public function SetLoeschtage($value) { $this->loeschtage=$value; }
  public function GetLoeschtage() { return $this->loeschtage; }
  public function SetGeloescht($value) { $this->geloescht=$value; }
  public function GetGeloescht() { return $this->geloescht; }
  public function SetTicketloeschen($value) { $this->ticketloeschen=$value; }
  public function GetTicketloeschen() { return $this->ticketloeschen; }
  public function SetTicketabgeschlossen($value) { $this->ticketabgeschlossen=$value; }
  public function GetTicketabgeschlossen() { return $this->ticketabgeschlossen; }
  public function SetTicketqueue($value) { $this->ticketqueue=$value; }
  public function GetTicketqueue() { return $this->ticketqueue; }
  public function SetTicketprojekt($value) { $this->ticketprojekt=$value; }
  public function GetTicketprojekt() { return $this->ticketprojekt; }
  public function SetTicketemaileingehend($value) { $this->ticketemaileingehend=$value; }
  public function GetTicketemaileingehend() { return $this->ticketemaileingehend; }
  public function SetSmtp_Extra($value) { $this->smtp_extra=$value; }
  public function GetSmtp_Extra() { return $this->smtp_extra; }
  public function SetSmtp_Ssl($value) { $this->smtp_ssl=$value; }
  public function GetSmtp_Ssl() { return $this->smtp_ssl; }
  public function SetSmtp_Port($value) { $this->smtp_port=$value; }
  public function GetSmtp_Port() { return $this->smtp_port; }
  public function SetSmtp_Frommail($value) { $this->smtp_frommail=$value; }
  public function GetSmtp_Frommail() { return $this->smtp_frommail; }
  public function SetSmtp_Fromname($value) { $this->smtp_fromname=$value; }
  public function GetSmtp_Fromname() { return $this->smtp_fromname; }
  public function SetAutosresponder_Blacklist($value) { $this->autosresponder_blacklist=$value; }
  public function GetAutosresponder_Blacklist() { return $this->autosresponder_blacklist; }
  public function SetEigenesignatur($value) { $this->eigenesignatur=$value; }
  public function GetEigenesignatur() { return $this->eigenesignatur; }
  public function SetSignatur($value) { $this->signatur=$value; }
  public function GetSignatur() { return $this->signatur; }
  public function SetMutex($value) { $this->mutex=$value; }
  public function GetMutex() { return $this->mutex; }
  public function SetAbdatum($value) { $this->abdatum=$value; }
  public function GetAbdatum() { return $this->abdatum; }
  public function SetEmail($value) { $this->email=$value; }
  public function GetEmail() { return $this->email; }
  public function SetClient_Alias($value) { $this->client_alias=$value; }
  public function GetClient_Alias() { return $this->client_alias; }
  public function SetSmtp_Authtype($value) { $this->smtp_authtype=$value; }
  public function GetSmtp_Authtype() { return $this->smtp_authtype; }
  public function SetSmtp_Authparam($value) { $this->smtp_authparam=$value; }
  public function GetSmtp_Authparam() { return $this->smtp_authparam; }
  public function SetSmtp_Loglevel($value) { $this->smtp_loglevel=$value; }
  public function GetSmtp_Loglevel() { return $this->smtp_loglevel; }

}
