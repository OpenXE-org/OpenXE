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

class ObjGenDta
{

  private  $id;
  private  $adresse;
  private  $datum;
  private  $name;
  private  $konto;
  private  $blz;
  private  $betrag;
  private  $vz1;
  private  $vz2;
  private  $vz3;
  private  $lastschrift;
  private  $gutschrift;
  private  $kontointern;
  private  $datei;
  private  $status;
  private  $firma;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM dta WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->datum=$result[datum];
    $this->name=$result[name];
    $this->konto=$result[konto];
    $this->blz=$result[blz];
    $this->betrag=$result[betrag];
    $this->vz1=$result[vz1];
    $this->vz2=$result[vz2];
    $this->vz3=$result[vz3];
    $this->lastschrift=$result[lastschrift];
    $this->gutschrift=$result[gutschrift];
    $this->kontointern=$result[kontointern];
    $this->datei=$result[datei];
    $this->status=$result[status];
    $this->firma=$result[firma];
  }

  public function Create()
  {
    $sql = "INSERT INTO dta (id,adresse,datum,name,konto,blz,betrag,vz1,vz2,vz3,lastschrift,gutschrift,kontointern,datei,status,firma)
      VALUES('','{$this->adresse}','{$this->datum}','{$this->name}','{$this->konto}','{$this->blz}','{$this->betrag}','{$this->vz1}','{$this->vz2}','{$this->vz3}','{$this->lastschrift}','{$this->gutschrift}','{$this->kontointern}','{$this->datei}','{$this->status}','{$this->firma}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE dta SET
      adresse='{$this->adresse}',
      datum='{$this->datum}',
      name='{$this->name}',
      konto='{$this->konto}',
      blz='{$this->blz}',
      betrag='{$this->betrag}',
      vz1='{$this->vz1}',
      vz2='{$this->vz2}',
      vz3='{$this->vz3}',
      lastschrift='{$this->lastschrift}',
      gutschrift='{$this->gutschrift}',
      kontointern='{$this->kontointern}',
      datei='{$this->datei}',
      status='{$this->status}',
      firma='{$this->firma}'
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

    $sql = "DELETE FROM dta WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->datum="";
    $this->name="";
    $this->konto="";
    $this->blz="";
    $this->betrag="";
    $this->vz1="";
    $this->vz2="";
    $this->vz3="";
    $this->lastschrift="";
    $this->gutschrift="";
    $this->kontointern="";
    $this->datei="";
    $this->status="";
    $this->firma="";
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
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetKonto($value) { $this->konto=$value; }
  function GetKonto() { return $this->konto; }
  function SetBlz($value) { $this->blz=$value; }
  function GetBlz() { return $this->blz; }
  function SetBetrag($value) { $this->betrag=$value; }
  function GetBetrag() { return $this->betrag; }
  function SetVz1($value) { $this->vz1=$value; }
  function GetVz1() { return $this->vz1; }
  function SetVz2($value) { $this->vz2=$value; }
  function GetVz2() { return $this->vz2; }
  function SetVz3($value) { $this->vz3=$value; }
  function GetVz3() { return $this->vz3; }
  function SetLastschrift($value) { $this->lastschrift=$value; }
  function GetLastschrift() { return $this->lastschrift; }
  function SetGutschrift($value) { $this->gutschrift=$value; }
  function GetGutschrift() { return $this->gutschrift; }
  function SetKontointern($value) { $this->kontointern=$value; }
  function GetKontointern() { return $this->kontointern; }
  function SetDatei($value) { $this->datei=$value; }
  function GetDatei() { return $this->datei; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }

}

?>