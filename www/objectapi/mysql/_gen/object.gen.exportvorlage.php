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

class ObjGenExportvorlage
{

  private  $id;
  private  $bezeichnung;
  private  $ziel;
  private  $internebemerkung;
  private  $fields;
  private  $fields_where;
  private  $letzterexport;
  private  $mitarbeiterletzterexport;
  private  $exporttrennzeichen;
  private  $exporterstezeilenummer;
  private  $exportdatenmaskierung;
  private  $exportzeichensatz;
  private  $filterdatum;
  private  $filterprojekt;
  private  $apifreigabe;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `exportvorlage` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->ziel=$result['ziel'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->fields=$result['fields'];
    $this->fields_where=$result['fields_where'];
    $this->letzterexport=$result['letzterexport'];
    $this->mitarbeiterletzterexport=$result['mitarbeiterletzterexport'];
    $this->exporttrennzeichen=$result['exporttrennzeichen'];
    $this->exporterstezeilenummer=$result['exporterstezeilenummer'];
    $this->exportdatenmaskierung=$result['exportdatenmaskierung'];
    $this->exportzeichensatz=$result['exportzeichensatz'];
    $this->filterdatum=$result['filterdatum'];
    $this->filterprojekt=$result['filterprojekt'];
    $this->apifreigabe=$result['apifreigabe'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `exportvorlage` (`id`,`bezeichnung`,`ziel`,`internebemerkung`,`fields`,`fields_where`,`letzterexport`,`mitarbeiterletzterexport`,`exporttrennzeichen`,`exporterstezeilenummer`,`exportdatenmaskierung`,`exportzeichensatz`,`filterdatum`,`filterprojekt`,`apifreigabe`)
      VALUES(NULL,'{$this->bezeichnung}','{$this->ziel}','{$this->internebemerkung}','{$this->fields}','{$this->fields_where}','{$this->letzterexport}','{$this->mitarbeiterletzterexport}','{$this->exporttrennzeichen}','{$this->exporterstezeilenummer}','{$this->exportdatenmaskierung}','{$this->exportzeichensatz}','{$this->filterdatum}','{$this->filterprojekt}','{$this->apifreigabe}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `exportvorlage` SET
      `bezeichnung`='{$this->bezeichnung}',
      `ziel`='{$this->ziel}',
      `internebemerkung`='{$this->internebemerkung}',
      `fields`='{$this->fields}',
      `fields_where`='{$this->fields_where}',
      `letzterexport`='{$this->letzterexport}',
      `mitarbeiterletzterexport`='{$this->mitarbeiterletzterexport}',
      `exporttrennzeichen`='{$this->exporttrennzeichen}',
      `exporterstezeilenummer`='{$this->exporterstezeilenummer}',
      `exportdatenmaskierung`='{$this->exportdatenmaskierung}',
      `exportzeichensatz`='{$this->exportzeichensatz}',
      `filterdatum`='{$this->filterdatum}',
      `filterprojekt`='{$this->filterprojekt}',
      `apifreigabe`='{$this->apifreigabe}'
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

    $sql = "DELETE FROM `exportvorlage` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->bezeichnung='';
    $this->ziel='';
    $this->internebemerkung='';
    $this->fields='';
    $this->fields_where='';
    $this->letzterexport='';
    $this->mitarbeiterletzterexport='';
    $this->exporttrennzeichen='';
    $this->exporterstezeilenummer='';
    $this->exportdatenmaskierung='';
    $this->exportzeichensatz='';
    $this->filterdatum='';
    $this->filterprojekt='';
    $this->apifreigabe='';
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
  public function SetFields_Where($value) { $this->fields_where=$value; }
  public function GetFields_Where() { return $this->fields_where; }
  public function SetLetzterexport($value) { $this->letzterexport=$value; }
  public function GetLetzterexport() { return $this->letzterexport; }
  public function SetMitarbeiterletzterexport($value) { $this->mitarbeiterletzterexport=$value; }
  public function GetMitarbeiterletzterexport() { return $this->mitarbeiterletzterexport; }
  public function SetExporttrennzeichen($value) { $this->exporttrennzeichen=$value; }
  public function GetExporttrennzeichen() { return $this->exporttrennzeichen; }
  public function SetExporterstezeilenummer($value) { $this->exporterstezeilenummer=$value; }
  public function GetExporterstezeilenummer() { return $this->exporterstezeilenummer; }
  public function SetExportdatenmaskierung($value) { $this->exportdatenmaskierung=$value; }
  public function GetExportdatenmaskierung() { return $this->exportdatenmaskierung; }
  public function SetExportzeichensatz($value) { $this->exportzeichensatz=$value; }
  public function GetExportzeichensatz() { return $this->exportzeichensatz; }
  public function SetFilterdatum($value) { $this->filterdatum=$value; }
  public function GetFilterdatum() { return $this->filterdatum; }
  public function SetFilterprojekt($value) { $this->filterprojekt=$value; }
  public function GetFilterprojekt() { return $this->filterprojekt; }
  public function SetApifreigabe($value) { $this->apifreigabe=$value; }
  public function GetApifreigabe() { return $this->apifreigabe; }

}
