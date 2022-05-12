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

class ObjGenDokumente_Send
{

  private  $id;
  private  $dokument;
  private  $zeit;
  private  $bearbeiter;
  private  $adresse;
  private  $ansprechpartner;
  private  $projekt;
  private  $parameter;
  private  $art;
  private  $betreff;
  private  $text;
  private  $geloescht;
  private  $versendet;
  private  $logdatei;
  private  $dateiid;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM dokumente_send WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->dokument=$result[dokument];
    $this->zeit=$result[zeit];
    $this->bearbeiter=$result[bearbeiter];
    $this->adresse=$result[adresse];
    $this->ansprechpartner=$result[ansprechpartner];
    $this->projekt=$result[projekt];
    $this->parameter=$result[parameter];
    $this->art=$result[art];
    $this->betreff=$result[betreff];
    $this->text=$result[text];
    $this->geloescht=$result[geloescht];
    $this->versendet=$result[versendet];
    $this->logdatei=$result[logdatei];
    $this->dateiid=$result[dateiid];
  }

  public function Create()
  {
    $sql = "INSERT INTO dokumente_send (id,dokument,zeit,bearbeiter,adresse,ansprechpartner,projekt,parameter,art,betreff,text,geloescht,versendet,logdatei,dateiid)
      VALUES('','{$this->dokument}','{$this->zeit}','{$this->bearbeiter}','{$this->adresse}','{$this->ansprechpartner}','{$this->projekt}','{$this->parameter}','{$this->art}','{$this->betreff}','{$this->text}','{$this->geloescht}','{$this->versendet}','{$this->logdatei}','{$this->dateiid}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE dokumente_send SET
      dokument='{$this->dokument}',
      zeit='{$this->zeit}',
      bearbeiter='{$this->bearbeiter}',
      adresse='{$this->adresse}',
      ansprechpartner='{$this->ansprechpartner}',
      projekt='{$this->projekt}',
      parameter='{$this->parameter}',
      art='{$this->art}',
      betreff='{$this->betreff}',
      text='{$this->text}',
      geloescht='{$this->geloescht}',
      versendet='{$this->versendet}',
      logdatei='{$this->logdatei}',
      dateiid='{$this->dateiid}'
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

    $sql = "DELETE FROM dokumente_send WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->dokument="";
    $this->zeit="";
    $this->bearbeiter="";
    $this->adresse="";
    $this->ansprechpartner="";
    $this->projekt="";
    $this->parameter="";
    $this->art="";
    $this->betreff="";
    $this->text="";
    $this->geloescht="";
    $this->versendet="";
    $this->logdatei="";
    $this->dateiid="";
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
  function SetDokument($value) { $this->dokument=$value; }
  function GetDokument() { return $this->dokument; }
  function SetZeit($value) { $this->zeit=$value; }
  function GetZeit() { return $this->zeit; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetParameter($value) { $this->parameter=$value; }
  function GetParameter() { return $this->parameter; }
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetBetreff($value) { $this->betreff=$value; }
  function GetBetreff() { return $this->betreff; }
  function SetText($value) { $this->text=$value; }
  function GetText() { return $this->text; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetVersendet($value) { $this->versendet=$value; }
  function GetVersendet() { return $this->versendet; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetDateiid($value) { $this->dateiid=$value; }
  function GetDateiid() { return $this->dateiid; }

}

?>