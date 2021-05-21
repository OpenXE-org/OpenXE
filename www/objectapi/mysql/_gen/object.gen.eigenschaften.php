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

class ObjGenEigenschaften
{

  private  $id;
  private  $artikel;
  private  $art;
  private  $bezeichnung;
  private  $beschreibung;
  private  $menge;
  private  $einheit;
  private  $menge2;
  private  $einheit2;
  private  $menge3;
  private  $einheit3;
  private  $wert;
  private  $wert2;
  private  $wert3;
  private  $hauptkategorie;
  private  $unterkategorie;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM eigenschaften WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->artikel=$result[artikel];
    $this->art=$result[art];
    $this->bezeichnung=$result[bezeichnung];
    $this->beschreibung=$result[beschreibung];
    $this->menge=$result[menge];
    $this->einheit=$result[einheit];
    $this->menge2=$result[menge2];
    $this->einheit2=$result[einheit2];
    $this->menge3=$result[menge3];
    $this->einheit3=$result[einheit3];
    $this->wert=$result[wert];
    $this->wert2=$result[wert2];
    $this->wert3=$result[wert3];
    $this->hauptkategorie=$result[hauptkategorie];
    $this->unterkategorie=$result[unterkategorie];
  }

  public function Create()
  {
    $sql = "INSERT INTO eigenschaften (id,artikel,art,bezeichnung,beschreibung,menge,einheit,menge2,einheit2,menge3,einheit3,wert,wert2,wert3,hauptkategorie,unterkategorie)
      VALUES('','{$this->artikel}','{$this->art}','{$this->bezeichnung}','{$this->beschreibung}','{$this->menge}','{$this->einheit}','{$this->menge2}','{$this->einheit2}','{$this->menge3}','{$this->einheit3}','{$this->wert}','{$this->wert2}','{$this->wert3}','{$this->hauptkategorie}','{$this->unterkategorie}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE eigenschaften SET
      artikel='{$this->artikel}',
      art='{$this->art}',
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      menge='{$this->menge}',
      einheit='{$this->einheit}',
      menge2='{$this->menge2}',
      einheit2='{$this->einheit2}',
      menge3='{$this->menge3}',
      einheit3='{$this->einheit3}',
      wert='{$this->wert}',
      wert2='{$this->wert2}',
      wert3='{$this->wert3}',
      hauptkategorie='{$this->hauptkategorie}',
      unterkategorie='{$this->unterkategorie}'
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

    $sql = "DELETE FROM eigenschaften WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->artikel="";
    $this->art="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->menge="";
    $this->einheit="";
    $this->menge2="";
    $this->einheit2="";
    $this->menge3="";
    $this->einheit3="";
    $this->wert="";
    $this->wert2="";
    $this->wert3="";
    $this->hauptkategorie="";
    $this->unterkategorie="";
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
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetEinheit($value) { $this->einheit=$value; }
  function GetEinheit() { return $this->einheit; }
  function SetMenge2($value) { $this->menge2=$value; }
  function GetMenge2() { return $this->menge2; }
  function SetEinheit2($value) { $this->einheit2=$value; }
  function GetEinheit2() { return $this->einheit2; }
  function SetMenge3($value) { $this->menge3=$value; }
  function GetMenge3() { return $this->menge3; }
  function SetEinheit3($value) { $this->einheit3=$value; }
  function GetEinheit3() { return $this->einheit3; }
  function SetWert($value) { $this->wert=$value; }
  function GetWert() { return $this->wert; }
  function SetWert2($value) { $this->wert2=$value; }
  function GetWert2() { return $this->wert2; }
  function SetWert3($value) { $this->wert3=$value; }
  function GetWert3() { return $this->wert3; }
  function SetHauptkategorie($value) { $this->hauptkategorie=$value; }
  function GetHauptkategorie() { return $this->hauptkategorie; }
  function SetUnterkategorie($value) { $this->unterkategorie=$value; }
  function GetUnterkategorie() { return $this->unterkategorie; }

}

?>