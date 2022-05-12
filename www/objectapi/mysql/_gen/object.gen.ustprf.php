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

class ObjGenUstprf
{

  private  $id;
  private  $adresse;
  private  $name;
  private  $ustid;
  private  $land;
  private  $ort;
  private  $plz;
  private  $rechtsform;
  private  $strasse;
  private  $status;
  private  $datum_online;
  private  $datum_brief;
  private  $bearbeiter;
  private  $briefbestellt;
  private  $logdatei;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM ustprf WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->name=$result[name];
    $this->ustid=$result[ustid];
    $this->land=$result[land];
    $this->ort=$result[ort];
    $this->plz=$result[plz];
    $this->rechtsform=$result[rechtsform];
    $this->strasse=$result[strasse];
    $this->status=$result[status];
    $this->datum_online=$result[datum_online];
    $this->datum_brief=$result[datum_brief];
    $this->bearbeiter=$result[bearbeiter];
    $this->briefbestellt=$result[briefbestellt];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO ustprf (id,adresse,name,ustid,land,ort,plz,rechtsform,strasse,status,datum_online,datum_brief,bearbeiter,briefbestellt,logdatei)
      VALUES('','{$this->adresse}','{$this->name}','{$this->ustid}','{$this->land}','{$this->ort}','{$this->plz}','{$this->rechtsform}','{$this->strasse}','{$this->status}','{$this->datum_online}','{$this->datum_brief}','{$this->bearbeiter}','{$this->briefbestellt}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE ustprf SET
      adresse='{$this->adresse}',
      name='{$this->name}',
      ustid='{$this->ustid}',
      land='{$this->land}',
      ort='{$this->ort}',
      plz='{$this->plz}',
      rechtsform='{$this->rechtsform}',
      strasse='{$this->strasse}',
      status='{$this->status}',
      datum_online='{$this->datum_online}',
      datum_brief='{$this->datum_brief}',
      bearbeiter='{$this->bearbeiter}',
      briefbestellt='{$this->briefbestellt}',
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

    $sql = "DELETE FROM ustprf WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->name="";
    $this->ustid="";
    $this->land="";
    $this->ort="";
    $this->plz="";
    $this->rechtsform="";
    $this->strasse="";
    $this->status="";
    $this->datum_online="";
    $this->datum_brief="";
    $this->bearbeiter="";
    $this->briefbestellt="";
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
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetUstid($value) { $this->ustid=$value; }
  function GetUstid() { return $this->ustid; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetRechtsform($value) { $this->rechtsform=$value; }
  function GetRechtsform() { return $this->rechtsform; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetDatum_Online($value) { $this->datum_online=$value; }
  function GetDatum_Online() { return $this->datum_online; }
  function SetDatum_Brief($value) { $this->datum_brief=$value; }
  function GetDatum_Brief() { return $this->datum_brief; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetBriefbestellt($value) { $this->briefbestellt=$value; }
  function GetBriefbestellt() { return $this->briefbestellt; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>