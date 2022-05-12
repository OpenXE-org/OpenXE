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

class ObjGenWaage_Artikel
{

  private  $id;
  private  $artikel;
  private  $reihenfolge;
  private  $beschriftung;
  private  $mhddatum;
  private  $etikettendrucker;
  private  $etikett;
  private  $waage;
  private  $etikettxml;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM waage_artikel WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->artikel=$result[artikel];
    $this->reihenfolge=$result[reihenfolge];
    $this->beschriftung=$result[beschriftung];
    $this->mhddatum=$result[mhddatum];
    $this->etikettendrucker=$result[etikettendrucker];
    $this->etikett=$result[etikett];
    $this->waage=$result[waage];
    $this->etikettxml=$result[etikettxml];
  }

  public function Create()
  {
    $sql = "INSERT INTO waage_artikel (id,artikel,reihenfolge,beschriftung,mhddatum,etikettendrucker,etikett,waage,etikettxml)
      VALUES('','{$this->artikel}','{$this->reihenfolge}','{$this->beschriftung}','{$this->mhddatum}','{$this->etikettendrucker}','{$this->etikett}','{$this->waage}','{$this->etikettxml}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE waage_artikel SET
      artikel='{$this->artikel}',
      reihenfolge='{$this->reihenfolge}',
      beschriftung='{$this->beschriftung}',
      mhddatum='{$this->mhddatum}',
      etikettendrucker='{$this->etikettendrucker}',
      etikett='{$this->etikett}',
      waage='{$this->waage}',
      etikettxml='{$this->etikettxml}'
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

    $sql = "DELETE FROM waage_artikel WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->artikel="";
    $this->reihenfolge="";
    $this->beschriftung="";
    $this->mhddatum="";
    $this->etikettendrucker="";
    $this->etikett="";
    $this->waage="";
    $this->etikettxml="";
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
  function SetReihenfolge($value) { $this->reihenfolge=$value; }
  function GetReihenfolge() { return $this->reihenfolge; }
  function SetBeschriftung($value) { $this->beschriftung=$value; }
  function GetBeschriftung() { return $this->beschriftung; }
  function SetMhddatum($value) { $this->mhddatum=$value; }
  function GetMhddatum() { return $this->mhddatum; }
  function SetEtikettendrucker($value) { $this->etikettendrucker=$value; }
  function GetEtikettendrucker() { return $this->etikettendrucker; }
  function SetEtikett($value) { $this->etikett=$value; }
  function GetEtikett() { return $this->etikett; }
  function SetWaage($value) { $this->waage=$value; }
  function GetWaage() { return $this->waage; }
  function SetEtikettxml($value) { $this->etikettxml=$value; }
  function GetEtikettxml() { return $this->etikettxml; }

}

?>