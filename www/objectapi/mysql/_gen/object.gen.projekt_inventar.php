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

class ObjGenProjekt_Inventar
{

  private  $id;
  private  $artikel;
  private  $menge;
  private  $bestellung;
  private  $projekt;
  private  $adresse;
  private  $mitarbeiter;
  private  $vpe;
  private  $zeit;
  private  $logdatei;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM projekt_inventar WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->artikel=$result[artikel];
    $this->menge=$result[menge];
    $this->bestellung=$result[bestellung];
    $this->projekt=$result[projekt];
    $this->adresse=$result[adresse];
    $this->mitarbeiter=$result[mitarbeiter];
    $this->vpe=$result[vpe];
    $this->zeit=$result[zeit];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO projekt_inventar (id,artikel,menge,bestellung,projekt,adresse,mitarbeiter,vpe,zeit,logdatei)
      VALUES('','{$this->artikel}','{$this->menge}','{$this->bestellung}','{$this->projekt}','{$this->adresse}','{$this->mitarbeiter}','{$this->vpe}','{$this->zeit}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE projekt_inventar SET
      artikel='{$this->artikel}',
      menge='{$this->menge}',
      bestellung='{$this->bestellung}',
      projekt='{$this->projekt}',
      adresse='{$this->adresse}',
      mitarbeiter='{$this->mitarbeiter}',
      vpe='{$this->vpe}',
      zeit='{$this->zeit}',
      logdatei='{$this->logdatei}'
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

    $sql = "DELETE FROM projekt_inventar WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->artikel="";
    $this->menge="";
    $this->bestellung="";
    $this->projekt="";
    $this->adresse="";
    $this->mitarbeiter="";
    $this->vpe="";
    $this->zeit="";
    $this->logdatei="";
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
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetBestellung($value) { $this->bestellung=$value; }
  function GetBestellung() { return $this->bestellung; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetMitarbeiter($value) { $this->mitarbeiter=$value; }
  function GetMitarbeiter() { return $this->mitarbeiter; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetZeit($value) { $this->zeit=$value; }
  function GetZeit() { return $this->zeit; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>