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

class ObjGenUebersetzung
{

  private  $id;
  private  $label;
  private  $beschriftung;
  private  $sprache;
  private  $original;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM uebersetzung WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->label=$result['label'];
    $this->beschriftung=$result['beschriftung'];
    $this->sprache=$result['sprache'];
    $this->original=$result['original'];
  }

  public function Create()
  {
    $sql = "INSERT INTO uebersetzung (id,label,beschriftung,sprache,original)
      VALUES('','{$this->label}','{$this->beschriftung}','{$this->sprache}','{$this->original}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE uebersetzung SET
      label='{$this->label}',
      beschriftung='{$this->beschriftung}',
      sprache='{$this->sprache}',
      original='{$this->original}'
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

    $sql = "DELETE FROM uebersetzung WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->label="";
    $this->beschriftung="";
    $this->sprache="";
    $this->original="";
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
  function SetLabel($value) { $this->label=$value; }
  function GetLabel() { return $this->label; }
  function SetBeschriftung($value) { $this->beschriftung=$value; }
  function GetBeschriftung() { return $this->beschriftung; }
  function SetSprache($value) { $this->sprache=$value; }
  function GetSprache() { return $this->sprache; }
  function SetOriginal($value) { $this->original=$value; }
  function GetOriginal() { return $this->original; }

}

?>