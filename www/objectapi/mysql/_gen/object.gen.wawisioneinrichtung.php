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

class ObjGenWawisioneinrichtung
{

  private  $id;
  private  $adresse;
  private  $mitarbeiter;
  private  $startdatum;
  private  $zeitgeplant;
  private  $version;
  private  $bemerkung;
  private  $status;
  private  $phase;
  private  $intervall;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `wawisioneinrichtung` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->adresse=$result['adresse'];
    $this->mitarbeiter=$result['mitarbeiter'];
    $this->startdatum=$result['startdatum'];
    $this->zeitgeplant=$result['zeitgeplant'];
    $this->version=$result['version'];
    $this->bemerkung=$result['bemerkung'];
    $this->status=$result['status'];
    $this->phase=$result['phase'];
    $this->intervall=$result['intervall'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `wawisioneinrichtung` (`id`,`adresse`,`mitarbeiter`,`startdatum`,`zeitgeplant`,`version`,`bemerkung`,`status`,`phase`,`intervall`)
      VALUES(NULL,'{$this->adresse}','{$this->mitarbeiter}','{$this->startdatum}','{$this->zeitgeplant}','{$this->version}','{$this->bemerkung}','{$this->status}','{$this->phase}','{$this->intervall}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `wawisioneinrichtung` SET
      `adresse`='{$this->adresse}',
      `mitarbeiter`='{$this->mitarbeiter}',
      `startdatum`='{$this->startdatum}',
      `zeitgeplant`='{$this->zeitgeplant}',
      `version`='{$this->version}',
      `bemerkung`='{$this->bemerkung}',
      `status`='{$this->status}',
      `phase`='{$this->phase}',
      `intervall`='{$this->intervall}'
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

    $sql = "DELETE FROM `wawisioneinrichtung` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->adresse='';
    $this->mitarbeiter='';
    $this->startdatum='';
    $this->zeitgeplant='';
    $this->version='';
    $this->bemerkung='';
    $this->status='';
    $this->phase='';
    $this->intervall='';
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
  public function SetAdresse($value) { $this->adresse=$value; }
  public function GetAdresse() { return $this->adresse; }
  public function SetMitarbeiter($value) { $this->mitarbeiter=$value; }
  public function GetMitarbeiter() { return $this->mitarbeiter; }
  public function SetStartdatum($value) { $this->startdatum=$value; }
  public function GetStartdatum() { return $this->startdatum; }
  public function SetZeitgeplant($value) { $this->zeitgeplant=$value; }
  public function GetZeitgeplant() { return $this->zeitgeplant; }
  public function SetVersion($value) { $this->version=$value; }
  public function GetVersion() { return $this->version; }
  public function SetBemerkung($value) { $this->bemerkung=$value; }
  public function GetBemerkung() { return $this->bemerkung; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetPhase($value) { $this->phase=$value; }
  public function GetPhase() { return $this->phase; }
  public function SetIntervall($value) { $this->intervall=$value; }
  public function GetIntervall() { return $this->intervall; }

}
