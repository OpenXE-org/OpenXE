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

class ObjGenEtiketten
{

  private  $id;
  private  $name;
  private  $xml;
  private  $bemerkung;
  private  $ausblenden;
  private  $verwendenals;
  private  $labelbreite;
  private  $labelhoehe;
  private  $labelabstand;
  private  $labeloffsetx;
  private  $labeloffsety;
  private  $format;
  private  $manuell;
  private  $anzahlprozeile;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM etiketten WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->name=$result['name'];
    $this->xml=$result['xml'];
    $this->bemerkung=$result['bemerkung'];
    $this->ausblenden=$result['ausblenden'];
    $this->verwendenals=$result['verwendenals'];
    $this->labelbreite=$result['labelbreite'];
    $this->labelhoehe=$result['labelhoehe'];
    $this->labelabstand=$result['labelabstand'];
    $this->labeloffsetx=$result['labeloffsetx'];
    $this->labeloffsety=$result['labeloffsety'];
    $this->format=$result['format'];
    $this->manuell=$result['manuell'];
    $this->anzahlprozeile=$result['anzahlprozeile'];
  }

  public function Create()
  {
    $sql = "INSERT INTO etiketten (id,name,xml,bemerkung,ausblenden,verwendenals,labelbreite,labelhoehe,labelabstand,labeloffsetx,labeloffsety,format,manuell,anzahlprozeile)
      VALUES('','{$this->name}','{$this->xml}','{$this->bemerkung}','{$this->ausblenden}','{$this->verwendenals}','{$this->labelbreite}','{$this->labelhoehe}','{$this->labelabstand}','{$this->labeloffsetx}','{$this->labeloffsety}','{$this->format}','{$this->manuell}','{$this->anzahlprozeile}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE etiketten SET
      name='{$this->name}',
      xml='{$this->xml}',
      bemerkung='{$this->bemerkung}',
      ausblenden='{$this->ausblenden}',
      verwendenals='{$this->verwendenals}',
      labelbreite='{$this->labelbreite}',
      labelhoehe='{$this->labelhoehe}',
      labelabstand='{$this->labelabstand}',
      labeloffsetx='{$this->labeloffsetx}',
      labeloffsety='{$this->labeloffsety}',
      format='{$this->format}',
      manuell='{$this->manuell}',
      anzahlprozeile='{$this->anzahlprozeile}'
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

    $sql = "DELETE FROM etiketten WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->name="";
    $this->xml="";
    $this->bemerkung="";
    $this->ausblenden="";
    $this->verwendenals="";
    $this->labelbreite="";
    $this->labelhoehe="";
    $this->labelabstand="";
    $this->labeloffsetx="";
    $this->labeloffsety="";
    $this->format="";
    $this->manuell="";
    $this->anzahlprozeile="";
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
  function SetXml($value) { $this->xml=$value; }
  function GetXml() { return $this->xml; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetAusblenden($value) { $this->ausblenden=$value; }
  function GetAusblenden() { return $this->ausblenden; }
  function SetVerwendenals($value) { $this->verwendenals=$value; }
  function GetVerwendenals() { return $this->verwendenals; }
  function SetLabelbreite($value) { $this->labelbreite=$value; }
  function GetLabelbreite() { return $this->labelbreite; }
  function SetLabelhoehe($value) { $this->labelhoehe=$value; }
  function GetLabelhoehe() { return $this->labelhoehe; }
  function SetLabelabstand($value) { $this->labelabstand=$value; }
  function GetLabelabstand() { return $this->labelabstand; }
  function SetLabeloffsetx($value) { $this->labeloffsetx=$value; }
  function GetLabeloffsetx() { return $this->labeloffsetx; }
  function SetLabeloffsety($value) { $this->labeloffsety=$value; }
  function GetLabeloffsety() { return $this->labeloffsety; }
  function SetFormat($value) { $this->format=$value; }
  function GetFormat() { return $this->format; }
  function SetManuell($value) { $this->manuell=$value; }
  function GetManuell() { return $this->manuell; }
  function SetAnzahlprozeile($value) { $this->anzahlprozeile=$value; }
  function GetAnzahlprozeile() { return $this->anzahlprozeile; }

}

?>