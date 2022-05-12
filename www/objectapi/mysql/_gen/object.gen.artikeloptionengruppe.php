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

class ObjGenArtikeloptionengruppe
{

  private  $id;
  private  $name;
  private  $name_de;
  private  $name_en;
  private  $artikel;
  private  $bearbeiter;
  private  $geloescht;
  private  $created;
  private  $projekt;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM artikeloptionengruppe WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->name=$result['name'];
    $this->name_de=$result['name_de'];
    $this->name_en=$result['name_en'];
    $this->artikel=$result['artikel'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->geloescht=$result['geloescht'];
    $this->created=$result['created'];
    $this->projekt=$result['projekt'];
  }

  public function Create()
  {
    $sql = "INSERT INTO artikeloptionengruppe (id,name,name_de,name_en,artikel,bearbeiter,geloescht,created,projekt)
      VALUES('','{$this->name}','{$this->name_de}','{$this->name_en}','{$this->artikel}','{$this->bearbeiter}','{$this->geloescht}','{$this->created}','{$this->projekt}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE artikeloptionengruppe SET
      name='{$this->name}',
      name_de='{$this->name_de}',
      name_en='{$this->name_en}',
      artikel='{$this->artikel}',
      bearbeiter='{$this->bearbeiter}',
      geloescht='{$this->geloescht}',
      created='{$this->created}',
      projekt='{$this->projekt}'
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

    $sql = "DELETE FROM artikeloptionengruppe WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->name="";
    $this->name_de="";
    $this->name_en="";
    $this->artikel="";
    $this->bearbeiter="";
    $this->geloescht="";
    $this->created="";
    $this->projekt="";
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
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetName_De($value) { $this->name_de=$value; }
  function GetName_De() { return $this->name_de; }
  function SetName_En($value) { $this->name_en=$value; }
  function GetName_En() { return $this->name_en; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetCreated($value) { $this->created=$value; }
  function GetCreated() { return $this->created; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }

}

?>