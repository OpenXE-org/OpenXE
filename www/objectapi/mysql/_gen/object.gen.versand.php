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

class ObjGenVersand
{

  private  $id;
  private  $adresse;
  private  $rechnung;
  private  $lieferschein;
  private  $versandart;
  private  $projekt;
  private  $gewicht;
  private  $freigegeben;
  private  $bearbeiter;
  private  $versender;
  private  $abgeschlossen;
  private  $versendet_am;
  private  $versandunternehmen;
  private  $tracking;
  private  $download;
  private  $firma;
  private  $logdatei;
  private  $keinetrackingmail;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM versand WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->rechnung=$result[rechnung];
    $this->lieferschein=$result[lieferschein];
    $this->versandart=$result[versandart];
    $this->projekt=$result[projekt];
    $this->gewicht=$result[gewicht];
    $this->freigegeben=$result[freigegeben];
    $this->bearbeiter=$result[bearbeiter];
    $this->versender=$result[versender];
    $this->abgeschlossen=$result[abgeschlossen];
    $this->versendet_am=$result[versendet_am];
    $this->versandunternehmen=$result[versandunternehmen];
    $this->tracking=$result[tracking];
    $this->download=$result[download];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
    $this->keinetrackingmail=$result[keinetrackingmail];
  }

  public function Create()
  {
    $sql = "INSERT INTO versand (id,adresse,rechnung,lieferschein,versandart,projekt,gewicht,freigegeben,bearbeiter,versender,abgeschlossen,versendet_am,versandunternehmen,tracking,download,firma,logdatei,keinetrackingmail)
      VALUES('','{$this->adresse}','{$this->rechnung}','{$this->lieferschein}','{$this->versandart}','{$this->projekt}','{$this->gewicht}','{$this->freigegeben}','{$this->bearbeiter}','{$this->versender}','{$this->abgeschlossen}','{$this->versendet_am}','{$this->versandunternehmen}','{$this->tracking}','{$this->download}','{$this->firma}','{$this->logdatei}','{$this->keinetrackingmail}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE versand SET
      adresse='{$this->adresse}',
      rechnung='{$this->rechnung}',
      lieferschein='{$this->lieferschein}',
      versandart='{$this->versandart}',
      projekt='{$this->projekt}',
      gewicht='{$this->gewicht}',
      freigegeben='{$this->freigegeben}',
      bearbeiter='{$this->bearbeiter}',
      versender='{$this->versender}',
      abgeschlossen='{$this->abgeschlossen}',
      versendet_am='{$this->versendet_am}',
      versandunternehmen='{$this->versandunternehmen}',
      tracking='{$this->tracking}',
      download='{$this->download}',
      firma='{$this->firma}',
      logdatei='{$this->logdatei}',
      keinetrackingmail='{$this->keinetrackingmail}'
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

    $sql = "DELETE FROM versand WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->rechnung="";
    $this->lieferschein="";
    $this->versandart="";
    $this->projekt="";
    $this->gewicht="";
    $this->freigegeben="";
    $this->bearbeiter="";
    $this->versender="";
    $this->abgeschlossen="";
    $this->versendet_am="";
    $this->versandunternehmen="";
    $this->tracking="";
    $this->download="";
    $this->firma="";
    $this->logdatei="";
    $this->keinetrackingmail="";
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
  function SetRechnung($value) { $this->rechnung=$value; }
  function GetRechnung() { return $this->rechnung; }
  function SetLieferschein($value) { $this->lieferschein=$value; }
  function GetLieferschein() { return $this->lieferschein; }
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetGewicht($value) { $this->gewicht=$value; }
  function GetGewicht() { return $this->gewicht; }
  function SetFreigegeben($value) { $this->freigegeben=$value; }
  function GetFreigegeben() { return $this->freigegeben; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetVersender($value) { $this->versender=$value; }
  function GetVersender() { return $this->versender; }
  function SetAbgeschlossen($value) { $this->abgeschlossen=$value; }
  function GetAbgeschlossen() { return $this->abgeschlossen; }
  function SetVersendet_Am($value) { $this->versendet_am=$value; }
  function GetVersendet_Am() { return $this->versendet_am; }
  function SetVersandunternehmen($value) { $this->versandunternehmen=$value; }
  function GetVersandunternehmen() { return $this->versandunternehmen; }
  function SetTracking($value) { $this->tracking=$value; }
  function GetTracking() { return $this->tracking; }
  function SetDownload($value) { $this->download=$value; }
  function GetDownload() { return $this->download; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetKeinetrackingmail($value) { $this->keinetrackingmail=$value; }
  function GetKeinetrackingmail() { return $this->keinetrackingmail; }

}

?>