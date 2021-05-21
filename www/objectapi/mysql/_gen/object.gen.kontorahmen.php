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

class ObjGenKontorahmen
{

  private  $id;
  private  $sachkonto;
  private  $beschriftung;
  private  $bemerkung;
  private  $ausblenden;
  private  $art;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM kontorahmen WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->sachkonto=$result['sachkonto'];
    $this->beschriftung=$result['beschriftung'];
    $this->bemerkung=$result['bemerkung'];
    $this->ausblenden=$result['ausblenden'];
    $this->art=$result['art'];
  }

  public function Create()
  {
    $sql = "INSERT INTO kontorahmen (id,sachkonto,beschriftung,bemerkung,ausblenden,art)
      VALUES('','{$this->sachkonto}','{$this->beschriftung}','{$this->bemerkung}','{$this->ausblenden}','{$this->art}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE kontorahmen SET
      sachkonto='{$this->sachkonto}',
      beschriftung='{$this->beschriftung}',
      bemerkung='{$this->bemerkung}',
      ausblenden='{$this->ausblenden}',
      art='{$this->art}'
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

    $sql = "DELETE FROM kontorahmen WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->sachkonto="";
    $this->beschriftung="";
    $this->bemerkung="";
    $this->ausblenden="";
    $this->art="";
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
  function SetSachkonto($value) { $this->sachkonto=$value; }
  function GetSachkonto() { return $this->sachkonto; }
  function SetBeschriftung($value) { $this->beschriftung=$value; }
  function GetBeschriftung() { return $this->beschriftung; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetAusblenden($value) { $this->ausblenden=$value; }
  function GetAusblenden() { return $this->ausblenden; }
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }

}

?>