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

class ObjGenLager_Platz_Inhalt
{

  private  $id;
  private  $lager_platz;
  private  $artikel;
  private  $menge;
  private  $vpe;
  private  $bearbeiter;
  private  $bestellung;
  private  $projekt;
  private  $firma;
  private  $logdatei;
  private  $inventur;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->lager_platz=$result[lager_platz];
    $this->artikel=$result[artikel];
    $this->menge=$result[menge];
    $this->vpe=$result[vpe];
    $this->bearbeiter=$result[bearbeiter];
    $this->bestellung=$result[bestellung];
    $this->projekt=$result[projekt];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
    $this->inventur=$result[inventur];
  }

  public function Create()
  {
    $sql = "INSERT INTO lager_platz_inhalt (id,lager_platz,artikel,menge,vpe,bearbeiter,bestellung,projekt,firma,logdatei,inventur)
      VALUES('','{$this->lager_platz}','{$this->artikel}','{$this->menge}','{$this->vpe}','{$this->bearbeiter}','{$this->bestellung}','{$this->projekt}','{$this->firma}','{$this->logdatei}','{$this->inventur}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE lager_platz_inhalt SET
      lager_platz='{$this->lager_platz}',
      artikel='{$this->artikel}',
      menge='{$this->menge}',
      vpe='{$this->vpe}',
      bearbeiter='{$this->bearbeiter}',
      bestellung='{$this->bestellung}',
      projekt='{$this->projekt}',
      firma='{$this->firma}',
      logdatei='{$this->logdatei}',
      inventur='{$this->inventur}'
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

    $sql = "DELETE FROM lager_platz_inhalt WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->lager_platz="";
    $this->artikel="";
    $this->menge="";
    $this->vpe="";
    $this->bearbeiter="";
    $this->bestellung="";
    $this->projekt="";
    $this->firma="";
    $this->logdatei="";
    $this->inventur="";
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
  function SetLager_Platz($value) { $this->lager_platz=$value; }
  function GetLager_Platz() { return $this->lager_platz; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetBestellung($value) { $this->bestellung=$value; }
  function GetBestellung() { return $this->bestellung; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetInventur($value) { $this->inventur=$value; }
  function GetInventur() { return $this->inventur; }

}

?>