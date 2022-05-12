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

class ObjGenEvent
{

  private  $id;
  private  $beschreibung;
  private  $kategorie;
  private  $zeit;
  private  $objekt;
  private  $parameter;
  private  $bearbeiter;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM event WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->beschreibung=$result[beschreibung];
    $this->kategorie=$result[kategorie];
    $this->zeit=$result[zeit];
    $this->objekt=$result[objekt];
    $this->parameter=$result[parameter];
    $this->bearbeiter=$result[bearbeiter];
  }

  public function Create()
  {
    $sql = "INSERT INTO event (id,beschreibung,kategorie,zeit,objekt,parameter,bearbeiter)
      VALUES('','{$this->beschreibung}','{$this->kategorie}','{$this->zeit}','{$this->objekt}','{$this->parameter}','{$this->bearbeiter}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE event SET
      beschreibung='{$this->beschreibung}',
      kategorie='{$this->kategorie}',
      zeit='{$this->zeit}',
      objekt='{$this->objekt}',
      parameter='{$this->parameter}',
      bearbeiter='{$this->bearbeiter}'
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

    $sql = "DELETE FROM event WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->beschreibung="";
    $this->kategorie="";
    $this->zeit="";
    $this->objekt="";
    $this->parameter="";
    $this->bearbeiter="";
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
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetKategorie($value) { $this->kategorie=$value; }
  function GetKategorie() { return $this->kategorie; }
  function SetZeit($value) { $this->zeit=$value; }
  function GetZeit() { return $this->zeit; }
  function SetObjekt($value) { $this->objekt=$value; }
  function GetObjekt() { return $this->objekt; }
  function SetParameter($value) { $this->parameter=$value; }
  function GetParameter() { return $this->parameter; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }

}

?>