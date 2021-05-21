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

class ObjGenUser
{

  private  $id;
  private  $username;
  private  $password;
  private  $repassword;
  private  $description;
  private  $settings;
  private  $parentuser;
  private  $activ;
  private  $type;
  private  $adresse;
  private  $fehllogins;
  private  $standarddrucker;
  private  $firma;
  private  $logdatei;
  private  $startseite;
  private  $hwtoken;
  private  $hwkey;
  private  $hwcounter;
  private  $motppin;
  private  $motpsecret;
  private  $externlogin;
  private  $hwdatablock;
  private  $passwordmd5;
  private  $internebezeichnung;
  private  $gpsstechuhr;
  private  $kalender_passwort;
  private  $kalender_aktiv;
  private  $vorlage;
  private  $standardetikett;
  private  $standardfax;
  private  $rfidtag;
  private  $kalender_ausblenden;
  private  $projekt_bevorzugen;
  private  $projekt;
  private  $email_bevorzugen;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM user WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->username=$result[username];
    $this->password=$result[password];
    $this->repassword=$result[repassword];
    $this->description=$result[description];
    $this->settings=$result[settings];
    $this->parentuser=$result[parentuser];
    $this->activ=$result[activ];
    $this->type=$result[type];
    $this->adresse=$result[adresse];
    $this->fehllogins=$result[fehllogins];
    $this->standarddrucker=$result[standarddrucker];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
    $this->startseite=$result[startseite];
    $this->hwtoken=$result[hwtoken];
    $this->hwkey=$result[hwkey];
    $this->hwcounter=$result[hwcounter];
    $this->motppin=$result[motppin];
    $this->motpsecret=$result[motpsecret];
    $this->externlogin=$result[externlogin];
    $this->hwdatablock=$result[hwdatablock];
    $this->passwordmd5=$result[passwordmd5];
    $this->internebezeichnung=$result[internebezeichnung];
    $this->gpsstechuhr=$result[gpsstechuhr];
    $this->kalender_passwort=$result[kalender_passwort];
    $this->kalender_aktiv=$result[kalender_aktiv];
    $this->vorlage=$result[vorlage];
    $this->standardetikett=$result[standardetikett];
    $this->standardfax=$result[standardfax];
    $this->rfidtag=$result[rfidtag];
    $this->kalender_ausblenden=$result[kalender_ausblenden];
    $this->projekt_bevorzugen=$result[projekt_bevorzugen];
    $this->projekt=$result[projekt];
    $this->email_bevorzugen=$result[email_bevorzugen];
  }

  public function Create()
  {
    $sql = "INSERT INTO user (id,username,password,repassword,description,settings,parentuser,activ,type,adresse,fehllogins,standarddrucker,firma,logdatei,startseite,hwtoken,hwkey,hwcounter,motppin,motpsecret,externlogin,hwdatablock,passwordmd5,internebezeichnung,gpsstechuhr,kalender_passwort,kalender_aktiv,vorlage,standardetikett,standardfax,rfidtag,kalender_ausblenden,projekt_bevorzugen,projekt,email_bevorzugen)
      VALUES('','{$this->username}','{$this->password}','{$this->repassword}','{$this->description}','{$this->settings}','{$this->parentuser}','{$this->activ}','{$this->type}','{$this->adresse}','{$this->fehllogins}','{$this->standarddrucker}','{$this->firma}','{$this->logdatei}','{$this->startseite}','{$this->hwtoken}','{$this->hwkey}','{$this->hwcounter}','{$this->motppin}','{$this->motpsecret}','{$this->externlogin}','{$this->hwdatablock}','{$this->passwordmd5}','{$this->internebezeichnung}','{$this->gpsstechuhr}','{$this->kalender_passwort}','{$this->kalender_aktiv}','{$this->vorlage}','{$this->standardetikett}','{$this->standardfax}','{$this->rfidtag}','{$this->kalender_ausblenden}','{$this->projekt_bevorzugen}','{$this->projekt}','{$this->email_bevorzugen}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE user SET
      username='{$this->username}',
      password='{$this->password}',
      repassword='{$this->repassword}',
      description='{$this->description}',
      settings='{$this->settings}',
      parentuser='{$this->parentuser}',
      activ='{$this->activ}',
      type='{$this->type}',
      adresse='{$this->adresse}',
      fehllogins='{$this->fehllogins}',
      standarddrucker='{$this->standarddrucker}',
      firma='{$this->firma}',
      logdatei='{$this->logdatei}',
      startseite='{$this->startseite}',
      hwtoken='{$this->hwtoken}',
      hwkey='{$this->hwkey}',
      hwcounter='{$this->hwcounter}',
      motppin='{$this->motppin}',
      motpsecret='{$this->motpsecret}',
      externlogin='{$this->externlogin}',
      hwdatablock='{$this->hwdatablock}',
      passwordmd5='{$this->passwordmd5}',
      internebezeichnung='{$this->internebezeichnung}',
      gpsstechuhr='{$this->gpsstechuhr}',
      kalender_passwort='{$this->kalender_passwort}',
      kalender_aktiv='{$this->kalender_aktiv}',
      vorlage='{$this->vorlage}',
      standardetikett='{$this->standardetikett}',
      standardfax='{$this->standardfax}',
      rfidtag='{$this->rfidtag}',
      kalender_ausblenden='{$this->kalender_ausblenden}',
      projekt_bevorzugen='{$this->projekt_bevorzugen}',
      projekt='{$this->projekt}',
      email_bevorzugen='{$this->email_bevorzugen}'
      WHERE (id='{$this->id}')";

    $this->app->DB->Update($sql);
  }

  public function Delete($id="")
  {
    if(is_numeric($id))
    {
      $this->id=$id;
    }
    else
      return -1;

    $sql = "DELETE FROM user WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->username="";
    $this->password="";
    $this->repassword="";
    $this->description="";
    $this->settings="";
    $this->parentuser="";
    $this->activ="";
    $this->type="";
    $this->adresse="";
    $this->fehllogins="";
    $this->standarddrucker="";
    $this->firma="";
    $this->logdatei="";
    $this->startseite="";
    $this->hwtoken="";
    $this->hwkey="";
    $this->hwcounter="";
    $this->motppin="";
    $this->motpsecret="";
    $this->externlogin="";
    $this->hwdatablock="";
    $this->passwordmd5="";
    $this->internebezeichnung="";
    $this->gpsstechuhr="";
    $this->kalender_passwort="";
    $this->kalender_aktiv="";
    $this->vorlage="";
    $this->standardetikett="";
    $this->standardfax="";
    $this->rfidtag="";
    $this->kalender_ausblenden="";
    $this->projekt_bevorzugen="";
    $this->projekt="";
    $this->email_bevorzugen="";
  }

  public function Copy()
  {
    $this->id = "";
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

  function SetId($value) { $this->id=$value; }
  function GetId() { return $this->id; }
  function SetUsername($value) { $this->username=$value; }
  function GetUsername() { return $this->username; }
  function SetPassword($value) { $this->password=$value; }
  function GetPassword() { return $this->password; }
  function SetRepassword($value) { $this->repassword=$value; }
  function GetRepassword() { return $this->repassword; }
  function SetDescription($value) { $this->description=$value; }
  function GetDescription() { return $this->description; }
  function SetSettings($value) { $this->settings=$value; }
  function GetSettings() { return $this->settings; }
  function SetParentuser($value) { $this->parentuser=$value; }
  function GetParentuser() { return $this->parentuser; }
  function SetActiv($value) { $this->activ=$value; }
  function GetActiv() { return $this->activ; }
  function SetType($value) { $this->type=$value; }
  function GetType() { return $this->type; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetFehllogins($value) { $this->fehllogins=$value; }
  function GetFehllogins() { return $this->fehllogins; }
  function SetStandarddrucker($value) { $this->standarddrucker=$value; }
  function GetStandarddrucker() { return $this->standarddrucker; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetStartseite($value) { $this->startseite=$value; }
  function GetStartseite() { return $this->startseite; }
  function SetHwtoken($value) { $this->hwtoken=$value; }
  function GetHwtoken() { return $this->hwtoken; }
  function SetHwkey($value) { $this->hwkey=$value; }
  function GetHwkey() { return $this->hwkey; }
  function SetHwcounter($value) { $this->hwcounter=$value; }
  function GetHwcounter() { return $this->hwcounter; }
  function SetMotppin($value) { $this->motppin=$value; }
  function GetMotppin() { return $this->motppin; }
  function SetMotpsecret($value) { $this->motpsecret=$value; }
  function GetMotpsecret() { return $this->motpsecret; }
  function SetExternlogin($value) { $this->externlogin=$value; }
  function GetExternlogin() { return $this->externlogin; }
  function SetHwdatablock($value) { $this->hwdatablock=$value; }
  function GetHwdatablock() { return $this->hwdatablock; }
  function SetPasswordmd5($value) { $this->passwordmd5=$value; }
  function GetPasswordmd5() { return $this->passwordmd5; }
  function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  function GetInternebezeichnung() { return $this->internebezeichnung; }
  function SetGpsstechuhr($value) { $this->gpsstechuhr=$value; }
  function GetGpsstechuhr() { return $this->gpsstechuhr; }
  function SetKalender_Passwort($value) { $this->kalender_passwort=$value; }
  function GetKalender_Passwort() { return $this->kalender_passwort; }
  function SetKalender_Aktiv($value) { $this->kalender_aktiv=$value; }
  function GetKalender_Aktiv() { return $this->kalender_aktiv; }
  function SetVorlage($value) { $this->vorlage=$value; }
  function GetVorlage() { return $this->vorlage; }
  function SetStandardetikett($value) { $this->standardetikett=$value; }
  function GetStandardetikett() { return $this->standardetikett; }
  function SetStandardfax($value) { $this->standardfax=$value; }
  function GetStandardfax() { return $this->standardfax; }
  function SetRfidtag($value) { $this->rfidtag=$value; }
  function GetRfidtag() { return $this->rfidtag; }
  function SetKalender_Ausblenden($value) { $this->kalender_ausblenden=$value; }
  function GetKalender_Ausblenden() { return $this->kalender_ausblenden; }
  function SetProjekt_Bevorzugen($value) { $this->projekt_bevorzugen=$value; }
  function GetProjekt_Bevorzugen() { return $this->projekt_bevorzugen; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetEmail_Bevorzugen($value) { $this->email_bevorzugen=$value; }
  function GetEmail_Bevorzugen() { return $this->email_bevorzugen; }

}

?>