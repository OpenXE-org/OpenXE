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

class ObjGenDrucker
{

  private  $id;
  private  $name;
  private  $bezeichnung;
  private  $befehl;
  private  $aktiv;
  private  $firma;
  private  $tomail;
  private  $tomailtext;
  private  $tomailsubject;
  private  $adapterboxip;
  private  $adapterboxseriennummer;
  private  $adapterboxpasswort;
  private  $anbindung;
  private  $art;
  private  $faxserver;
  private  $format;
  private  $keinhintergrund;
  private  $json;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `drucker` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->name=$result['name'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->befehl=$result['befehl'];
    $this->aktiv=$result['aktiv'];
    $this->firma=$result['firma'];
    $this->tomail=$result['tomail'];
    $this->tomailtext=$result['tomailtext'];
    $this->tomailsubject=$result['tomailsubject'];
    $this->adapterboxip=$result['adapterboxip'];
    $this->adapterboxseriennummer=$result['adapterboxseriennummer'];
    $this->adapterboxpasswort=$result['adapterboxpasswort'];
    $this->anbindung=$result['anbindung'];
    $this->art=$result['art'];
    $this->faxserver=$result['faxserver'];
    $this->format=$result['format'];
    $this->keinhintergrund=$result['keinhintergrund'];
    $this->json=$result['json'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `drucker` (`id`,`name`,`bezeichnung`,`befehl`,`aktiv`,`firma`,`tomail`,`tomailtext`,`tomailsubject`,`adapterboxip`,`adapterboxseriennummer`,`adapterboxpasswort`,`anbindung`,`art`,`faxserver`,`format`,`keinhintergrund`,`json`)
      VALUES(NULL,'{$this->name}','{$this->bezeichnung}','{$this->befehl}','{$this->aktiv}','{$this->firma}','{$this->tomail}','{$this->tomailtext}','{$this->tomailsubject}','{$this->adapterboxip}','{$this->adapterboxseriennummer}','{$this->adapterboxpasswort}','{$this->anbindung}','{$this->art}','{$this->faxserver}','{$this->format}','{$this->keinhintergrund}','{$this->json}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `drucker` SET
      `name`='{$this->name}',
      `bezeichnung`='{$this->bezeichnung}',
      `befehl`='{$this->befehl}',
      `aktiv`='{$this->aktiv}',
      `firma`='{$this->firma}',
      `tomail`='{$this->tomail}',
      `tomailtext`='{$this->tomailtext}',
      `tomailsubject`='{$this->tomailsubject}',
      `adapterboxip`='{$this->adapterboxip}',
      `adapterboxseriennummer`='{$this->adapterboxseriennummer}',
      `adapterboxpasswort`='{$this->adapterboxpasswort}',
      `anbindung`='{$this->anbindung}',
      `art`='{$this->art}',
      `faxserver`='{$this->faxserver}',
      `format`='{$this->format}',
      `keinhintergrund`='{$this->keinhintergrund}',
      `json`='{$this->json}'
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

    $sql = "DELETE FROM `drucker` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->name='';
    $this->bezeichnung='';
    $this->befehl='';
    $this->aktiv='';
    $this->firma='';
    $this->tomail='';
    $this->tomailtext='';
    $this->tomailsubject='';
    $this->adapterboxip='';
    $this->adapterboxseriennummer='';
    $this->adapterboxpasswort='';
    $this->anbindung='';
    $this->art='';
    $this->faxserver='';
    $this->format='';
    $this->keinhintergrund='';
    $this->json='';
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
  public function SetBezeichnung($value) { $this->bezeichnung=$value; }
  public function GetBezeichnung() { return $this->bezeichnung; }
  public function SetBefehl($value) { $this->befehl=$value; }
  public function GetBefehl() { return $this->befehl; }
  public function SetAktiv($value) { $this->aktiv=$value; }
  public function GetAktiv() { return $this->aktiv; }
  public function SetFirma($value) { $this->firma=$value; }
  public function GetFirma() { return $this->firma; }
  public function SetTomail($value) { $this->tomail=$value; }
  public function GetTomail() { return $this->tomail; }
  public function SetTomailtext($value) { $this->tomailtext=$value; }
  public function GetTomailtext() { return $this->tomailtext; }
  public function SetTomailsubject($value) { $this->tomailsubject=$value; }
  public function GetTomailsubject() { return $this->tomailsubject; }
  public function SetAdapterboxip($value) { $this->adapterboxip=$value; }
  public function GetAdapterboxip() { return $this->adapterboxip; }
  public function SetAdapterboxseriennummer($value) { $this->adapterboxseriennummer=$value; }
  public function GetAdapterboxseriennummer() { return $this->adapterboxseriennummer; }
  public function SetAdapterboxpasswort($value) { $this->adapterboxpasswort=$value; }
  public function GetAdapterboxpasswort() { return $this->adapterboxpasswort; }
  public function SetAnbindung($value) { $this->anbindung=$value; }
  public function GetAnbindung() { return $this->anbindung; }
  public function SetArt($value) { $this->art=$value; }
  public function GetArt() { return $this->art; }
  public function SetFaxserver($value) { $this->faxserver=$value; }
  public function GetFaxserver() { return $this->faxserver; }
  public function SetFormat($value) { $this->format=$value; }
  public function GetFormat() { return $this->format; }
  public function SetKeinhintergrund($value) { $this->keinhintergrund=$value; }
  public function GetKeinhintergrund() { return $this->keinhintergrund; }
  public function SetJson($value) { $this->json=$value; }
  public function GetJson() { return $this->json; }

}
