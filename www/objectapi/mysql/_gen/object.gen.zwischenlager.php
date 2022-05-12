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

class ObjGenZwischenlager
{

  private  $id;
  private  $bearbeiter;
  private  $projekt;
  private  $artikel;
  private  $menge;
  private  $vpe;
  private  $grund;
  private  $lager_von;
  private  $lager_nach;
  private  $richtung;
  private  $erledigt;
  private  $objekt;
  private  $parameter;
  private  $firma;
  private  $logdatei;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM zwischenlager WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->bearbeiter=$result[bearbeiter];
    $this->projekt=$result[projekt];
    $this->artikel=$result[artikel];
    $this->menge=$result[menge];
    $this->vpe=$result[vpe];
    $this->grund=$result[grund];
    $this->lager_von=$result[lager_von];
    $this->lager_nach=$result[lager_nach];
    $this->richtung=$result[richtung];
    $this->erledigt=$result[erledigt];
    $this->objekt=$result[objekt];
    $this->parameter=$result[parameter];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO zwischenlager (id,bearbeiter,projekt,artikel,menge,vpe,grund,lager_von,lager_nach,richtung,erledigt,objekt,parameter,firma,logdatei)
      VALUES('','{$this->bearbeiter}','{$this->projekt}','{$this->artikel}','{$this->menge}','{$this->vpe}','{$this->grund}','{$this->lager_von}','{$this->lager_nach}','{$this->richtung}','{$this->erledigt}','{$this->objekt}','{$this->parameter}','{$this->firma}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE zwischenlager SET
      bearbeiter='{$this->bearbeiter}',
      projekt='{$this->projekt}',
      artikel='{$this->artikel}',
      menge='{$this->menge}',
      vpe='{$this->vpe}',
      grund='{$this->grund}',
      lager_von='{$this->lager_von}',
      lager_nach='{$this->lager_nach}',
      richtung='{$this->richtung}',
      erledigt='{$this->erledigt}',
      objekt='{$this->objekt}',
      parameter='{$this->parameter}',
      firma='{$this->firma}',
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

    $sql = "DELETE FROM zwischenlager WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bearbeiter="";
    $this->projekt="";
    $this->artikel="";
    $this->menge="";
    $this->vpe="";
    $this->grund="";
    $this->lager_von="";
    $this->lager_nach="";
    $this->richtung="";
    $this->erledigt="";
    $this->objekt="";
    $this->parameter="";
    $this->firma="";
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
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetGrund($value) { $this->grund=$value; }
  function GetGrund() { return $this->grund; }
  function SetLager_Von($value) { $this->lager_von=$value; }
  function GetLager_Von() { return $this->lager_von; }
  function SetLager_Nach($value) { $this->lager_nach=$value; }
  function GetLager_Nach() { return $this->lager_nach; }
  function SetRichtung($value) { $this->richtung=$value; }
  function GetRichtung() { return $this->richtung; }
  function SetErledigt($value) { $this->erledigt=$value; }
  function GetErledigt() { return $this->erledigt; }
  function SetObjekt($value) { $this->objekt=$value; }
  function GetObjekt() { return $this->objekt; }
  function SetParameter($value) { $this->parameter=$value; }
  function GetParameter() { return $this->parameter; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>