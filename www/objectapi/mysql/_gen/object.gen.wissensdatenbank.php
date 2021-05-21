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

class ObjGenWissensdatenbank
{

  private  $id;
  private  $archiviert;
  private  $ueberschrift;
  private  $hinweis;
  private  $tags;
  private  $text;
  private  $bemerkung;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM wissensdatenbank WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->archiviert=$result['archiviert'];
    $this->ueberschrift=$result['ueberschrift'];
    $this->hinweis=$result['hinweis'];
    $this->tags=$result['tags'];
    $this->text=$result['text'];
    $this->bemerkung=$result['bemerkung'];
  }

  public function Create()
  {
    $sql = "INSERT INTO wissensdatenbank (id,archiviert,ueberschrift,hinweis,tags,text,bemerkung)
      VALUES('','{$this->archiviert}','{$this->ueberschrift}','{$this->hinweis}','{$this->tags}','{$this->text}','{$this->bemerkung}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE wissensdatenbank SET
      archiviert='{$this->archiviert}',
      ueberschrift='{$this->ueberschrift}',
      hinweis='{$this->hinweis}',
      tags='{$this->tags}',
      text='{$this->text}',
      bemerkung='{$this->bemerkung}'
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

    $sql = "DELETE FROM wissensdatenbank WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->archiviert="";
    $this->ueberschrift="";
    $this->hinweis="";
    $this->tags="";
    $this->text="";
    $this->bemerkung="";
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
  function SetArchiviert($value) { $this->archiviert=$value; }
  function GetArchiviert() { return $this->archiviert; }
  function SetUeberschrift($value) { $this->ueberschrift=$value; }
  function GetUeberschrift() { return $this->ueberschrift; }
  function SetHinweis($value) { $this->hinweis=$value; }
  function GetHinweis() { return $this->hinweis; }
  function SetTags($value) { $this->tags=$value; }
  function GetTags() { return $this->tags; }
  function SetText($value) { $this->text=$value; }
  function GetText() { return $this->text; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }

}

?>