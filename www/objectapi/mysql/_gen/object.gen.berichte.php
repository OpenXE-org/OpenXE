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

class ObjGenBerichte
{

  private  $id;
  private  $name;
  private  $beschreibung;
  private  $internebemerkung;
  private  $struktur;
  private  $spaltennamen;
  private  $spaltenbreite;
  private  $spaltenausrichtung;
  private  $variablen;
  private  $sumcols;
  private  $doctype;
  private  $doctype_actionmenu;
  private  $doctype_actionmenuname;
  private  $doctype_actionmenufiletype;
  private  $project;
  private  $ftpuebertragung;
  private  $ftphost;
  private  $ftpport;
  private  $ftpuser;
  private  $ftppassword;
  private  $ftpuhrzeit;
  private  $ftpletzteuebertragung;
  private  $ftpnamealternativ;
  private  $emailuebertragung;
  private  $emailempfaenger;
  private  $emailbetreff;
  private  $emailuhrzeit;
  private  $emailletzteuebertragung;
  private  $emailnamealternativ;
  private  $typ;
  private  $ftppassivemode;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `berichte` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->name=$result['name'];
    $this->beschreibung=$result['beschreibung'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->struktur=$result['struktur'];
    $this->spaltennamen=$result['spaltennamen'];
    $this->spaltenbreite=$result['spaltenbreite'];
    $this->spaltenausrichtung=$result['spaltenausrichtung'];
    $this->variablen=$result['variablen'];
    $this->sumcols=$result['sumcols'];
    $this->doctype=$result['doctype'];
    $this->doctype_actionmenu=$result['doctype_actionmenu'];
    $this->doctype_actionmenuname=$result['doctype_actionmenuname'];
    $this->doctype_actionmenufiletype=$result['doctype_actionmenufiletype'];
    $this->project=$result['project'];
    $this->ftpuebertragung=$result['ftpuebertragung'];
    $this->ftphost=$result['ftphost'];
    $this->ftpport=$result['ftpport'];
    $this->ftpuser=$result['ftpuser'];
    $this->ftppassword=$result['ftppassword'];
    $this->ftpuhrzeit=$result['ftpuhrzeit'];
    $this->ftpletzteuebertragung=$result['ftpletzteuebertragung'];
    $this->ftpnamealternativ=$result['ftpnamealternativ'];
    $this->emailuebertragung=$result['emailuebertragung'];
    $this->emailempfaenger=$result['emailempfaenger'];
    $this->emailbetreff=$result['emailbetreff'];
    $this->emailuhrzeit=$result['emailuhrzeit'];
    $this->emailletzteuebertragung=$result['emailletzteuebertragung'];
    $this->emailnamealternativ=$result['emailnamealternativ'];
    $this->typ=$result['typ'];
    $this->ftppassivemode=$result['ftppassivemode'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `berichte` (`id`,`name`,`beschreibung`,`internebemerkung`,`struktur`,`spaltennamen`,`spaltenbreite`,`spaltenausrichtung`,`variablen`,`sumcols`,`doctype`,`doctype_actionmenu`,`doctype_actionmenuname`,`doctype_actionmenufiletype`,`project`,`ftpuebertragung`,`ftphost`,`ftpport`,`ftpuser`,`ftppassword`,`ftpuhrzeit`,`ftpletzteuebertragung`,`ftpnamealternativ`,`emailuebertragung`,`emailempfaenger`,`emailbetreff`,`emailuhrzeit`,`emailletzteuebertragung`,`emailnamealternativ`,`typ`,`ftppassivemode`)
      VALUES(NULL,'{$this->name}','{$this->beschreibung}','{$this->internebemerkung}','{$this->struktur}','{$this->spaltennamen}','{$this->spaltenbreite}','{$this->spaltenausrichtung}','{$this->variablen}','{$this->sumcols}','{$this->doctype}','{$this->doctype_actionmenu}','{$this->doctype_actionmenuname}','{$this->doctype_actionmenufiletype}','{$this->project}','{$this->ftpuebertragung}','{$this->ftphost}','{$this->ftpport}','{$this->ftpuser}','{$this->ftppassword}','{$this->ftpuhrzeit}','{$this->ftpletzteuebertragung}','{$this->ftpnamealternativ}','{$this->emailuebertragung}','{$this->emailempfaenger}','{$this->emailbetreff}','{$this->emailuhrzeit}','{$this->emailletzteuebertragung}','{$this->emailnamealternativ}','{$this->typ}','{$this->ftppassivemode}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `berichte` SET
      `name`='{$this->name}',
      `beschreibung`='{$this->beschreibung}',
      `internebemerkung`='{$this->internebemerkung}',
      `struktur`='{$this->struktur}',
      `spaltennamen`='{$this->spaltennamen}',
      `spaltenbreite`='{$this->spaltenbreite}',
      `spaltenausrichtung`='{$this->spaltenausrichtung}',
      `variablen`='{$this->variablen}',
      `sumcols`='{$this->sumcols}',
      `doctype`='{$this->doctype}',
      `doctype_actionmenu`='{$this->doctype_actionmenu}',
      `doctype_actionmenuname`='{$this->doctype_actionmenuname}',
      `doctype_actionmenufiletype`='{$this->doctype_actionmenufiletype}',
      `project`='{$this->project}',
      `ftpuebertragung`='{$this->ftpuebertragung}',
      `ftphost`='{$this->ftphost}',
      `ftpport`='{$this->ftpport}',
      `ftpuser`='{$this->ftpuser}',
      `ftppassword`='{$this->ftppassword}',
      `ftpuhrzeit`='{$this->ftpuhrzeit}',
      `ftpletzteuebertragung`='{$this->ftpletzteuebertragung}',
      `ftpnamealternativ`='{$this->ftpnamealternativ}',
      `emailuebertragung`='{$this->emailuebertragung}',
      `emailempfaenger`='{$this->emailempfaenger}',
      `emailbetreff`='{$this->emailbetreff}',
      `emailuhrzeit`='{$this->emailuhrzeit}',
      `emailletzteuebertragung`='{$this->emailletzteuebertragung}',
      `emailnamealternativ`='{$this->emailnamealternativ}',
      `typ`='{$this->typ}',
      `ftppassivemode`='{$this->ftppassivemode}'
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

    $sql = "DELETE FROM `berichte` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->name='';
    $this->beschreibung='';
    $this->internebemerkung='';
    $this->struktur='';
    $this->spaltennamen='';
    $this->spaltenbreite='';
    $this->spaltenausrichtung='';
    $this->variablen='';
    $this->sumcols='';
    $this->doctype='';
    $this->doctype_actionmenu='';
    $this->doctype_actionmenuname='';
    $this->doctype_actionmenufiletype='';
    $this->project='';
    $this->ftpuebertragung='';
    $this->ftphost='';
    $this->ftpport='';
    $this->ftpuser='';
    $this->ftppassword='';
    $this->ftpuhrzeit='';
    $this->ftpletzteuebertragung='';
    $this->ftpnamealternativ='';
    $this->emailuebertragung='';
    $this->emailempfaenger='';
    $this->emailbetreff='';
    $this->emailuhrzeit='';
    $this->emailletzteuebertragung='';
    $this->emailnamealternativ='';
    $this->typ='';
    $this->ftppassivemode='';
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
  public function SetName($value) { $this->name=$value; }
  public function GetName() { return $this->name; }
  public function SetBeschreibung($value) { $this->beschreibung=$value; }
  public function GetBeschreibung() { return $this->beschreibung; }
  public function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  public function GetInternebemerkung() { return $this->internebemerkung; }
  public function SetStruktur($value) { $this->struktur=$value; }
  public function GetStruktur() { return $this->struktur; }
  public function SetSpaltennamen($value) { $this->spaltennamen=$value; }
  public function GetSpaltennamen() { return $this->spaltennamen; }
  public function SetSpaltenbreite($value) { $this->spaltenbreite=$value; }
  public function GetSpaltenbreite() { return $this->spaltenbreite; }
  public function SetSpaltenausrichtung($value) { $this->spaltenausrichtung=$value; }
  public function GetSpaltenausrichtung() { return $this->spaltenausrichtung; }
  public function SetVariablen($value) { $this->variablen=$value; }
  public function GetVariablen() { return $this->variablen; }
  public function SetSumcols($value) { $this->sumcols=$value; }
  public function GetSumcols() { return $this->sumcols; }
  public function SetDoctype($value) { $this->doctype=$value; }
  public function GetDoctype() { return $this->doctype; }
  public function SetDoctype_Actionmenu($value) { $this->doctype_actionmenu=$value; }
  public function GetDoctype_Actionmenu() { return $this->doctype_actionmenu; }
  public function SetDoctype_Actionmenuname($value) { $this->doctype_actionmenuname=$value; }
  public function GetDoctype_Actionmenuname() { return $this->doctype_actionmenuname; }
  public function SetDoctype_Actionmenufiletype($value) { $this->doctype_actionmenufiletype=$value; }
  public function GetDoctype_Actionmenufiletype() { return $this->doctype_actionmenufiletype; }
  public function SetProject($value) { $this->project=$value; }
  public function GetProject() { return $this->project; }
  public function SetFtpuebertragung($value) { $this->ftpuebertragung=$value; }
  public function GetFtpuebertragung() { return $this->ftpuebertragung; }
  public function SetFtphost($value) { $this->ftphost=$value; }
  public function GetFtphost() { return $this->ftphost; }
  public function SetFtpport($value) { $this->ftpport=$value; }
  public function GetFtpport() { return $this->ftpport; }
  public function SetFtpuser($value) { $this->ftpuser=$value; }
  public function GetFtpuser() { return $this->ftpuser; }
  public function SetFtppassword($value) { $this->ftppassword=$value; }
  public function GetFtppassword() { return $this->ftppassword; }
  public function SetFtpuhrzeit($value) { $this->ftpuhrzeit=$value; }
  public function GetFtpuhrzeit() { return $this->ftpuhrzeit; }
  public function SetFtpletzteuebertragung($value) { $this->ftpletzteuebertragung=$value; }
  public function GetFtpletzteuebertragung() { return $this->ftpletzteuebertragung; }
  public function SetFtpnamealternativ($value) { $this->ftpnamealternativ=$value; }
  public function GetFtpnamealternativ() { return $this->ftpnamealternativ; }
  public function SetEmailuebertragung($value) { $this->emailuebertragung=$value; }
  public function GetEmailuebertragung() { return $this->emailuebertragung; }
  public function SetEmailempfaenger($value) { $this->emailempfaenger=$value; }
  public function GetEmailempfaenger() { return $this->emailempfaenger; }
  public function SetEmailbetreff($value) { $this->emailbetreff=$value; }
  public function GetEmailbetreff() { return $this->emailbetreff; }
  public function SetEmailuhrzeit($value) { $this->emailuhrzeit=$value; }
  public function GetEmailuhrzeit() { return $this->emailuhrzeit; }
  public function SetEmailletzteuebertragung($value) { $this->emailletzteuebertragung=$value; }
  public function GetEmailletzteuebertragung() { return $this->emailletzteuebertragung; }
  public function SetEmailnamealternativ($value) { $this->emailnamealternativ=$value; }
  public function GetEmailnamealternativ() { return $this->emailnamealternativ; }
  public function SetTyp($value) { $this->typ=$value; }
  public function GetTyp() { return $this->typ; }
  public function SetFtppassivemode($value) { $this->ftppassivemode=$value; }
  public function GetFtppassivemode() { return $this->ftppassivemode; }

}
