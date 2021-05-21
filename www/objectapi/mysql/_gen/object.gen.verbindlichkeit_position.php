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

class ObjGenVerbindlichkeit_Position
{

  private  $id;
  private  $verbindlichkeit;
  private  $sort;
  private  $artikel;
  private  $projekt;
  private  $bestellung;
  private  $nummer;
  private  $bestellnummer;
  private  $waehrung;
  private  $einheit;
  private  $vpe;
  private  $bezeichnung;
  private  $umsatzsteuer;
  private  $status;
  private  $beschreibung;
  private  $lieferdatum;
  private  $steuersatz;
  private  $steuertext;
  private  $preis;
  private  $menge;
  private  $kostenstelle;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `verbindlichkeit_position` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->verbindlichkeit=$result['verbindlichkeit'];
    $this->sort=$result['sort'];
    $this->artikel=$result['artikel'];
    $this->projekt=$result['projekt'];
    $this->bestellung=$result['bestellung'];
    $this->nummer=$result['nummer'];
    $this->bestellnummer=$result['bestellnummer'];
    $this->waehrung=$result['waehrung'];
    $this->einheit=$result['einheit'];
    $this->vpe=$result['vpe'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->umsatzsteuer=$result['umsatzsteuer'];
    $this->status=$result['status'];
    $this->beschreibung=$result['beschreibung'];
    $this->lieferdatum=$result['lieferdatum'];
    $this->steuersatz=$result['steuersatz'];
    $this->steuertext=$result['steuertext'];
    $this->preis=$result['preis'];
    $this->menge=$result['menge'];
    $this->kostenstelle=$result['kostenstelle'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `verbindlichkeit_position` (`id`,`verbindlichkeit`,`sort`,`artikel`,`projekt`,`bestellung`,`nummer`,`bestellnummer`,`waehrung`,`einheit`,`vpe`,`bezeichnung`,`umsatzsteuer`,`status`,`beschreibung`,`lieferdatum`,`steuersatz`,`steuertext`,`preis`,`menge`,`kostenstelle`)
      VALUES(NULL,'{$this->verbindlichkeit}','{$this->sort}','{$this->artikel}','{$this->projekt}','{$this->bestellung}','{$this->nummer}','{$this->bestellnummer}','{$this->waehrung}','{$this->einheit}','{$this->vpe}','{$this->bezeichnung}','{$this->umsatzsteuer}','{$this->status}','{$this->beschreibung}','{$this->lieferdatum}','{$this->steuersatz}','{$this->steuertext}','{$this->preis}','{$this->menge}','{$this->kostenstelle}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `verbindlichkeit_position` SET
      `verbindlichkeit`='{$this->verbindlichkeit}',
      `sort`='{$this->sort}',
      `artikel`='{$this->artikel}',
      `projekt`='{$this->projekt}',
      `bestellung`='{$this->bestellung}',
      `nummer`='{$this->nummer}',
      `bestellnummer`='{$this->bestellnummer}',
      `waehrung`='{$this->waehrung}',
      `einheit`='{$this->einheit}',
      `vpe`='{$this->vpe}',
      `bezeichnung`='{$this->bezeichnung}',
      `umsatzsteuer`='{$this->umsatzsteuer}',
      `status`='{$this->status}',
      `beschreibung`='{$this->beschreibung}',
      `lieferdatum`='{$this->lieferdatum}',
      `steuersatz`='{$this->steuersatz}',
      `steuertext`='{$this->steuertext}',
      `preis`='{$this->preis}',
      `menge`='{$this->menge}',
      `kostenstelle`='{$this->kostenstelle}'
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

    $sql = "DELETE FROM `verbindlichkeit_position` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->verbindlichkeit='';
    $this->sort='';
    $this->artikel='';
    $this->projekt='';
    $this->bestellung='';
    $this->nummer='';
    $this->bestellnummer='';
    $this->waehrung='';
    $this->einheit='';
    $this->vpe='';
    $this->bezeichnung='';
    $this->umsatzsteuer='';
    $this->status='';
    $this->beschreibung='';
    $this->lieferdatum='';
    $this->steuersatz='';
    $this->steuertext='';
    $this->preis='';
    $this->menge='';
    $this->kostenstelle='';
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
  public function SetVerbindlichkeit($value) { $this->verbindlichkeit=$value; }
  public function GetVerbindlichkeit() { return $this->verbindlichkeit; }
  public function SetSort($value) { $this->sort=$value; }
  public function GetSort() { return $this->sort; }
  public function SetArtikel($value) { $this->artikel=$value; }
  public function GetArtikel() { return $this->artikel; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetBestellung($value) { $this->bestellung=$value; }
  public function GetBestellung() { return $this->bestellung; }
  public function SetNummer($value) { $this->nummer=$value; }
  public function GetNummer() { return $this->nummer; }
  public function SetBestellnummer($value) { $this->bestellnummer=$value; }
  public function GetBestellnummer() { return $this->bestellnummer; }
  public function SetWaehrung($value) { $this->waehrung=$value; }
  public function GetWaehrung() { return $this->waehrung; }
  public function SetEinheit($value) { $this->einheit=$value; }
  public function GetEinheit() { return $this->einheit; }
  public function SetVpe($value) { $this->vpe=$value; }
  public function GetVpe() { return $this->vpe; }
  public function SetBezeichnung($value) { $this->bezeichnung=$value; }
  public function GetBezeichnung() { return $this->bezeichnung; }
  public function SetUmsatzsteuer($value) { $this->umsatzsteuer=$value; }
  public function GetUmsatzsteuer() { return $this->umsatzsteuer; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetBeschreibung($value) { $this->beschreibung=$value; }
  public function GetBeschreibung() { return $this->beschreibung; }
  public function SetLieferdatum($value) { $this->lieferdatum=$value; }
  public function GetLieferdatum() { return $this->lieferdatum; }
  public function SetSteuersatz($value) { $this->steuersatz=$value; }
  public function GetSteuersatz() { return $this->steuersatz; }
  public function SetSteuertext($value) { $this->steuertext=$value; }
  public function GetSteuertext() { return $this->steuertext; }
  public function SetPreis($value) { $this->preis=$value; }
  public function GetPreis() { return $this->preis; }
  public function SetMenge($value) { $this->menge=$value; }
  public function GetMenge() { return $this->menge; }
  public function SetKostenstelle($value) { $this->kostenstelle=$value; }
  public function GetKostenstelle() { return $this->kostenstelle; }

}
