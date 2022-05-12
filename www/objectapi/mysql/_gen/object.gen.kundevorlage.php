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

class ObjGenKundevorlage
{

  private  $id;
  private  $adresse;
  private  $zahlungsweise;
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $versandart;
  private  $logdatei;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM kundevorlage WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->zahlungsweise=$result[zahlungsweise];
    $this->zahlungszieltage=$result[zahlungszieltage];
    $this->zahlungszieltageskonto=$result[zahlungszieltageskonto];
    $this->zahlungszielskonto=$result[zahlungszielskonto];
    $this->versandart=$result[versandart];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO kundevorlage (id,adresse,zahlungsweise,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,versandart,logdatei)
      VALUES('','{$this->adresse}','{$this->zahlungsweise}','{$this->zahlungszieltage}','{$this->zahlungszieltageskonto}','{$this->zahlungszielskonto}','{$this->versandart}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE kundevorlage SET
      adresse='{$this->adresse}',
      zahlungsweise='{$this->zahlungsweise}',
      zahlungszieltage='{$this->zahlungszieltage}',
      zahlungszieltageskonto='{$this->zahlungszieltageskonto}',
      zahlungszielskonto='{$this->zahlungszielskonto}',
      versandart='{$this->versandart}',
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

    $sql = "DELETE FROM kundevorlage WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->zahlungsweise="";
    $this->zahlungszieltage="";
    $this->zahlungszieltageskonto="";
    $this->zahlungszielskonto="";
    $this->versandart="";
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
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  function GetZahlungsweise() { return $this->zahlungsweise; }
  function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  function GetZahlungszieltage() { return $this->zahlungszieltage; }
  function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>