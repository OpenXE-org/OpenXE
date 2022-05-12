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

class ObjGenProzessstarter
{

  private  $id;
  private  $bezeichnung;
  private  $bedingung;
  private  $art;
  private  $startzeit;
  private  $letzteausfuerhung;
  private  $periode;
  private  $typ;
  private  $parameter;
  private  $aktiv;
  private  $mutex;
  private  $mutexcounter;
  private  $firma;
  private  $art_filter;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM prozessstarter WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->bedingung=$result['bedingung'];
    $this->art=$result['art'];
    $this->startzeit=$result['startzeit'];
    $this->letzteausfuerhung=$result['letzteausfuerhung'];
    $this->periode=$result['periode'];
    $this->typ=$result['typ'];
    $this->parameter=$result['parameter'];
    $this->aktiv=$result['aktiv'];
    $this->mutex=$result['mutex'];
    $this->mutexcounter=$result['mutexcounter'];
    $this->firma=$result['firma'];
    $this->art_filter=$result['art_filter'];
  }

  public function Create()
  {
    $sql = "INSERT INTO prozessstarter (id,bezeichnung,bedingung,art,startzeit,letzteausfuerhung,periode,typ,parameter,aktiv,mutex,mutexcounter,firma,art_filter)
      VALUES('','{$this->bezeichnung}','{$this->bedingung}','{$this->art}','{$this->startzeit}','{$this->letzteausfuerhung}','{$this->periode}','{$this->typ}','{$this->parameter}','{$this->aktiv}','{$this->mutex}','{$this->mutexcounter}','{$this->firma}','{$this->art_filter}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE prozessstarter SET
      bezeichnung='{$this->bezeichnung}',
      bedingung='{$this->bedingung}',
      art='{$this->art}',
      startzeit='{$this->startzeit}',
      letzteausfuerhung='{$this->letzteausfuerhung}',
      periode='{$this->periode}',
      typ='{$this->typ}',
      parameter='{$this->parameter}',
      aktiv='{$this->aktiv}',
      mutex='{$this->mutex}',
      mutexcounter='{$this->mutexcounter}',
      firma='{$this->firma}',
      art_filter='{$this->art_filter}'
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

    $sql = "DELETE FROM prozessstarter WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->bedingung="";
    $this->art="";
    $this->startzeit="";
    $this->letzteausfuerhung="";
    $this->periode="";
    $this->typ="";
    $this->parameter="";
    $this->aktiv="";
    $this->mutex="";
    $this->mutexcounter="";
    $this->firma="";
    $this->art_filter="";
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
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBedingung($value) { $this->bedingung=$value; }
  function GetBedingung() { return $this->bedingung; }
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetStartzeit($value) { $this->startzeit=$value; }
  function GetStartzeit() { return $this->startzeit; }
  function SetLetzteausfuerhung($value) { $this->letzteausfuerhung=$value; }
  function GetLetzteausfuerhung() { return $this->letzteausfuerhung; }
  function SetPeriode($value) { $this->periode=$value; }
  function GetPeriode() { return $this->periode; }
  function SetTyp($value) { $this->typ=$value; }
  function GetTyp() { return $this->typ; }
  function SetParameter($value) { $this->parameter=$value; }
  function GetParameter() { return $this->parameter; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetMutex($value) { $this->mutex=$value; }
  function GetMutex() { return $this->mutex; }
  function SetMutexcounter($value) { $this->mutexcounter=$value; }
  function GetMutexcounter() { return $this->mutexcounter; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetArt_Filter($value) { $this->art_filter=$value; }
  function GetArt_Filter() { return $this->art_filter; }

}

?>