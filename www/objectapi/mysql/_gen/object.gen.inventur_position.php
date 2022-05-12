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

class ObjGenInventur_Position
{

  private  $id;
  private  $inventur;
  private  $artikel;
  private  $projekt;
  private  $nummer;
  private  $bezeichnung;
  private  $beschreibung;
  private  $internerkommentar;
  private  $menge;
  private  $sort;
  private  $bemerkung;
  private  $preis;
  private  $logdatei;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM inventur_position WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->inventur=$result['inventur'];
    $this->artikel=$result['artikel'];
    $this->projekt=$result['projekt'];
    $this->nummer=$result['nummer'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->beschreibung=$result['beschreibung'];
    $this->internerkommentar=$result['internerkommentar'];
    $this->menge=$result['menge'];
    $this->sort=$result['sort'];
    $this->bemerkung=$result['bemerkung'];
    $this->preis=$result['preis'];
    $this->logdatei=$result['logdatei'];
  }

  public function Create()
  {
    $sql = "INSERT INTO inventur_position (id,inventur,artikel,projekt,nummer,bezeichnung,beschreibung,internerkommentar,menge,sort,bemerkung,preis,logdatei)
      VALUES('','{$this->inventur}','{$this->artikel}','{$this->projekt}','{$this->nummer}','{$this->bezeichnung}','{$this->beschreibung}','{$this->internerkommentar}','{$this->menge}','{$this->sort}','{$this->bemerkung}','{$this->preis}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE inventur_position SET
      inventur='{$this->inventur}',
      artikel='{$this->artikel}',
      projekt='{$this->projekt}',
      nummer='{$this->nummer}',
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      internerkommentar='{$this->internerkommentar}',
      menge='{$this->menge}',
      sort='{$this->sort}',
      bemerkung='{$this->bemerkung}',
      preis='{$this->preis}',
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

    $sql = "DELETE FROM inventur_position WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->inventur="";
    $this->artikel="";
    $this->projekt="";
    $this->nummer="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->internerkommentar="";
    $this->menge="";
    $this->sort="";
    $this->bemerkung="";
    $this->preis="";
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
  function SetInventur($value) { $this->inventur=$value; }
  function GetInventur() { return $this->inventur; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetInternerkommentar($value) { $this->internerkommentar=$value; }
  function GetInternerkommentar() { return $this->internerkommentar; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetPreis($value) { $this->preis=$value; }
  function GetPreis() { return $this->preis; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>