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

class ObjGenPaketdistribution
{

  private  $id;
  private  $bearbeiter;
  private  $zeit;
  private  $paketannahme;
  private  $adresse;
  private  $artikel;
  private  $menge;
  private  $vpe;
  private  $etiketten;
  private  $bemerkung;
  private  $bestellung_position;
  private  $logdatei;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM paketdistribution WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->bearbeiter=$result[bearbeiter];
    $this->zeit=$result[zeit];
    $this->paketannahme=$result[paketannahme];
    $this->adresse=$result[adresse];
    $this->artikel=$result[artikel];
    $this->menge=$result[menge];
    $this->vpe=$result[vpe];
    $this->etiketten=$result[etiketten];
    $this->bemerkung=$result[bemerkung];
    $this->bestellung_position=$result[bestellung_position];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO paketdistribution (id,bearbeiter,zeit,paketannahme,adresse,artikel,menge,vpe,etiketten,bemerkung,bestellung_position,logdatei)
      VALUES('','{$this->bearbeiter}','{$this->zeit}','{$this->paketannahme}','{$this->adresse}','{$this->artikel}','{$this->menge}','{$this->vpe}','{$this->etiketten}','{$this->bemerkung}','{$this->bestellung_position}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE paketdistribution SET
      bearbeiter='{$this->bearbeiter}',
      zeit='{$this->zeit}',
      paketannahme='{$this->paketannahme}',
      adresse='{$this->adresse}',
      artikel='{$this->artikel}',
      menge='{$this->menge}',
      vpe='{$this->vpe}',
      etiketten='{$this->etiketten}',
      bemerkung='{$this->bemerkung}',
      bestellung_position='{$this->bestellung_position}',
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

    $sql = "DELETE FROM paketdistribution WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bearbeiter="";
    $this->zeit="";
    $this->paketannahme="";
    $this->adresse="";
    $this->artikel="";
    $this->menge="";
    $this->vpe="";
    $this->etiketten="";
    $this->bemerkung="";
    $this->bestellung_position="";
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
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetZeit($value) { $this->zeit=$value; }
  function GetZeit() { return $this->zeit; }
  function SetPaketannahme($value) { $this->paketannahme=$value; }
  function GetPaketannahme() { return $this->paketannahme; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetEtiketten($value) { $this->etiketten=$value; }
  function GetEtiketten() { return $this->etiketten; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetBestellung_Position($value) { $this->bestellung_position=$value; }
  function GetBestellung_Position() { return $this->bestellung_position; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>