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

class ObjGenImportvorlage
{

  private  $id;
  private  $bezeichnung;
  private  $ziel;
  private  $internebemerkung;
  private  $fields;
  private  $letzterimport;
  private  $mitarbeiterletzterimport;
  private  $importtrennzeichen;
  private  $importerstezeilenummer;
  private  $importdatenmaskierung;
  private  $importzeichensatz;
  private  $utf8decode;
  private  $charset;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `importvorlage` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->ziel=$result['ziel'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->fields=$result['fields'];
    $this->letzterimport=$result['letzterimport'];
    $this->mitarbeiterletzterimport=$result['mitarbeiterletzterimport'];
    $this->importtrennzeichen=$result['importtrennzeichen'];
    $this->importerstezeilenummer=$result['importerstezeilenummer'];
    $this->importdatenmaskierung=$result['importdatenmaskierung'];
    $this->importzeichensatz=$result['importzeichensatz'];
    $this->utf8decode=$result['utf8decode'];
    $this->charset=$result['charset'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `importvorlage` (`id`,`bezeichnung`,`ziel`,`internebemerkung`,`fields`,`letzterimport`,`mitarbeiterletzterimport`,`importtrennzeichen`,`importerstezeilenummer`,`importdatenmaskierung`,`importzeichensatz`,`utf8decode`,`charset`)
      VALUES(NULL,'{$this->bezeichnung}','{$this->ziel}','{$this->internebemerkung}','{$this->fields}','{$this->letzterimport}','{$this->mitarbeiterletzterimport}','{$this->importtrennzeichen}','{$this->importerstezeilenummer}','{$this->importdatenmaskierung}','{$this->importzeichensatz}','{$this->utf8decode}','{$this->charset}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `importvorlage` SET
      `bezeichnung`='{$this->bezeichnung}',
      `ziel`='{$this->ziel}',
      `internebemerkung`='{$this->internebemerkung}',
      `fields`='{$this->fields}',
      `letzterimport`='{$this->letzterimport}',
      `mitarbeiterletzterimport`='{$this->mitarbeiterletzterimport}',
      `importtrennzeichen`='{$this->importtrennzeichen}',
      `importerstezeilenummer`='{$this->importerstezeilenummer}',
      `importdatenmaskierung`='{$this->importdatenmaskierung}',
      `importzeichensatz`='{$this->importzeichensatz}',
      `utf8decode`='{$this->utf8decode}',
      `charset`='{$this->charset}'
      WHERE (`id`='{$this->id}')";

    $this->app->DB->Update($sql);
  }

  public function Delete($id='')
  {
    if(is_numeric($id))
    {
      $this->id=$id;
    }
    else
      return -1;

    $sql = "DELETE FROM `importvorlage` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->bezeichnung='';
    $this->ziel='';
    $this->internebemerkung='';
    $this->fields='';
    $this->letzterimport='';
    $this->mitarbeiterletzterimport='';
    $this->importtrennzeichen='';
    $this->importerstezeilenummer='';
    $this->importdatenmaskierung='';
    $this->importzeichensatz='';
    $this->utf8decode='';
    $this->charset='';
  }

  public function Copy()
  {
    $this->id = '';
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

  public function SetId($value) { $this->id=$value; }
  public function GetId() { return $this->id; }
  public function SetBezeichnung($value) { $this->bezeichnung=$value; }
  public function GetBezeichnung() { return $this->bezeichnung; }
  public function SetZiel($value) { $this->ziel=$value; }
  public function GetZiel() { return $this->ziel; }
  public function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  public function GetInternebemerkung() { return $this->internebemerkung; }
  public function SetFields($value) { $this->fields=$value; }
  public function GetFields() { return $this->fields; }
  public function SetLetzterimport($value) { $this->letzterimport=$value; }
  public function GetLetzterimport() { return $this->letzterimport; }
  public function SetMitarbeiterletzterimport($value) { $this->mitarbeiterletzterimport=$value; }
  public function GetMitarbeiterletzterimport() { return $this->mitarbeiterletzterimport; }
  public function SetImporttrennzeichen($value) { $this->importtrennzeichen=$value; }
  public function GetImporttrennzeichen() { return $this->importtrennzeichen; }
  public function SetImporterstezeilenummer($value) { $this->importerstezeilenummer=$value; }
  public function GetImporterstezeilenummer() { return $this->importerstezeilenummer; }
  public function SetImportdatenmaskierung($value) { $this->importdatenmaskierung=$value; }
  public function GetImportdatenmaskierung() { return $this->importdatenmaskierung; }
  public function SetImportzeichensatz($value) { $this->importzeichensatz=$value; }
  public function GetImportzeichensatz() { return $this->importzeichensatz; }
  public function SetUtf8Decode($value) { $this->utf8decode=$value; }
  public function GetUtf8Decode() { return $this->utf8decode; }
  public function SetCharset($value) { $this->charset=$value; }
  public function GetCharset() { return $this->charset; }

}
