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

class ObjGenAdresse_Typ
{

  private  $id;
  private  $type;
  private  $bezeichnung;
  private  $aktiv;
  private  $geloescht;
  private  $projekt;
  private  $netto;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM adresse_typ WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->type=$result['type'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->aktiv=$result['aktiv'];
    $this->geloescht=$result['geloescht'];
    $this->projekt=$result['projekt'];
    $this->netto=$result['netto'];
  }

  public function Create()
  {
    $sql = "INSERT INTO adresse_typ (id,type,bezeichnung,aktiv,geloescht,projekt,netto)
      VALUES('','{$this->type}','{$this->bezeichnung}','{$this->aktiv}','{$this->geloescht}','{$this->projekt}','{$this->netto}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE adresse_typ SET
      type='{$this->type}',
      bezeichnung='{$this->bezeichnung}',
      aktiv='{$this->aktiv}',
      geloescht='{$this->geloescht}',
      projekt='{$this->projekt}',
      netto='{$this->netto}'
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

    $sql = "DELETE FROM adresse_typ WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->type="";
    $this->bezeichnung="";
    $this->aktiv="";
    $this->geloescht="";
    $this->projekt="";
    $this->netto="";
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
  function SetType($value) { $this->type=$value; }
  function GetType() { return $this->type; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetNetto($value) { $this->netto=$value; }
  function GetNetto() { return $this->netto; }

}

?>