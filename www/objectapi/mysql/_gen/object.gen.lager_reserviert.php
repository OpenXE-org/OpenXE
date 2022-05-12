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

class ObjGenLager_Reserviert
{

  private  $id;
  private  $adresse;
  private  $artikel;
  private  $menge;
  private  $grund;
  private  $objekt;
  private  $parameter;
  private  $projekt;
  private  $firma;
  private  $bearbeiter;
  private  $datum;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM lager_reserviert WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->artikel=$result[artikel];
    $this->menge=$result[menge];
    $this->grund=$result[grund];
    $this->objekt=$result[objekt];
    $this->parameter=$result[parameter];
    $this->projekt=$result[projekt];
    $this->firma=$result[firma];
    $this->bearbeiter=$result[bearbeiter];
    $this->datum=$result[datum];
  }

  public function Create()
  {
    $sql = "INSERT INTO lager_reserviert (id,adresse,artikel,menge,grund,objekt,parameter,projekt,firma,bearbeiter,datum)
      VALUES('','{$this->adresse}','{$this->artikel}','{$this->menge}','{$this->grund}','{$this->objekt}','{$this->parameter}','{$this->projekt}','{$this->firma}','{$this->bearbeiter}','{$this->datum}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE lager_reserviert SET
      adresse='{$this->adresse}',
      artikel='{$this->artikel}',
      menge='{$this->menge}',
      grund='{$this->grund}',
      objekt='{$this->objekt}',
      parameter='{$this->parameter}',
      projekt='{$this->projekt}',
      firma='{$this->firma}',
      bearbeiter='{$this->bearbeiter}',
      datum='{$this->datum}'
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

    $sql = "DELETE FROM lager_reserviert WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->artikel="";
    $this->menge="";
    $this->grund="";
    $this->objekt="";
    $this->parameter="";
    $this->projekt="";
    $this->firma="";
    $this->bearbeiter="";
    $this->datum="";
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
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetGrund($value) { $this->grund=$value; }
  function GetGrund() { return $this->grund; }
  function SetObjekt($value) { $this->objekt=$value; }
  function GetObjekt() { return $this->objekt; }
  function SetParameter($value) { $this->parameter=$value; }
  function GetParameter() { return $this->parameter; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }

}

?>