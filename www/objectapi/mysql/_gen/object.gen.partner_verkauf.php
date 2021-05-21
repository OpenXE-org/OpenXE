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

class ObjGenPartner_Verkauf
{

  private  $id;
  private  $auftrag;
  private  $artikel;
  private  $menge;
  private  $partner;
  private  $freigabe;
  private  $abgerechnet;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM partner_verkauf WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->auftrag=$result[auftrag];
    $this->artikel=$result[artikel];
    $this->menge=$result[menge];
    $this->partner=$result[partner];
    $this->freigabe=$result[freigabe];
    $this->abgerechnet=$result[abgerechnet];
  }

  public function Create()
  {
    $sql = "INSERT INTO partner_verkauf (id,auftrag,artikel,menge,partner,freigabe,abgerechnet)
      VALUES('','{$this->auftrag}','{$this->artikel}','{$this->menge}','{$this->partner}','{$this->freigabe}','{$this->abgerechnet}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE partner_verkauf SET
      auftrag='{$this->auftrag}',
      artikel='{$this->artikel}',
      menge='{$this->menge}',
      partner='{$this->partner}',
      freigabe='{$this->freigabe}',
      abgerechnet='{$this->abgerechnet}'
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

    $sql = "DELETE FROM partner_verkauf WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->auftrag="";
    $this->artikel="";
    $this->menge="";
    $this->partner="";
    $this->freigabe="";
    $this->abgerechnet="";
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
  function SetAuftrag($value) { $this->auftrag=$value; }
  function GetAuftrag() { return $this->auftrag; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetPartner($value) { $this->partner=$value; }
  function GetPartner() { return $this->partner; }
  function SetFreigabe($value) { $this->freigabe=$value; }
  function GetFreigabe() { return $this->freigabe; }
  function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  function GetAbgerechnet() { return $this->abgerechnet; }

}

?>