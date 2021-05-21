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

class ObjGenKontoauszuege
{

  private  $id;
  private  $konto;
  private  $buchung;
  private  $vorgang;
  private  $soll;
  private  $haben;
  private  $gebuehr;
  private  $waehrung;
  private  $fertig;
  private  $datev_abgeschlossen;
  private  $buchungstext;
  private  $gegenkonto;
  private  $belegfeld1;
  private  $bearbeiter;
  private  $mailbenachrichtigung;
  private  $pruefsumme;
  private  $internebemerkung;
  private  $importfehler;
  private  $importgroup;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM kontoauszuege WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->konto=$result[konto];
    $this->buchung=$result[buchung];
    $this->vorgang=$result[vorgang];
    $this->soll=$result[soll];
    $this->haben=$result[haben];
    $this->gebuehr=$result[gebuehr];
    $this->waehrung=$result[waehrung];
    $this->fertig=$result[fertig];
    $this->datev_abgeschlossen=$result[datev_abgeschlossen];
    $this->buchungstext=$result[buchungstext];
    $this->gegenkonto=$result[gegenkonto];
    $this->belegfeld1=$result[belegfeld1];
    $this->bearbeiter=$result[bearbeiter];
    $this->mailbenachrichtigung=$result[mailbenachrichtigung];
    $this->pruefsumme=$result[pruefsumme];
    $this->internebemerkung=$result[internebemerkung];
    $this->importfehler=$result[importfehler];
    $this->importgroup=$result[importgroup];
  }

  public function Create()
  {
    $sql = "INSERT INTO kontoauszuege (id,konto,buchung,vorgang,soll,haben,gebuehr,waehrung,fertig,datev_abgeschlossen,buchungstext,gegenkonto,belegfeld1,bearbeiter,mailbenachrichtigung,pruefsumme,internebemerkung,importfehler,importgroup)
      VALUES('','{$this->konto}','{$this->buchung}','{$this->vorgang}','{$this->soll}','{$this->haben}','{$this->gebuehr}','{$this->waehrung}','{$this->fertig}','{$this->datev_abgeschlossen}','{$this->buchungstext}','{$this->gegenkonto}','{$this->belegfeld1}','{$this->bearbeiter}','{$this->mailbenachrichtigung}','{$this->pruefsumme}','{$this->internebemerkung}','{$this->importfehler}','{$this->importgroup}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE kontoauszuege SET
      konto='{$this->konto}',
      buchung='{$this->buchung}',
      vorgang='{$this->vorgang}',
      soll='{$this->soll}',
      haben='{$this->haben}',
      gebuehr='{$this->gebuehr}',
      waehrung='{$this->waehrung}',
      fertig='{$this->fertig}',
      datev_abgeschlossen='{$this->datev_abgeschlossen}',
      buchungstext='{$this->buchungstext}',
      gegenkonto='{$this->gegenkonto}',
      belegfeld1='{$this->belegfeld1}',
      bearbeiter='{$this->bearbeiter}',
      mailbenachrichtigung='{$this->mailbenachrichtigung}',
      pruefsumme='{$this->pruefsumme}',
      internebemerkung='{$this->internebemerkung}',
      importfehler='{$this->importfehler}',
      importgroup='{$this->importgroup}'
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

    $sql = "DELETE FROM kontoauszuege WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->konto="";
    $this->buchung="";
    $this->vorgang="";
    $this->soll="";
    $this->haben="";
    $this->gebuehr="";
    $this->waehrung="";
    $this->fertig="";
    $this->datev_abgeschlossen="";
    $this->buchungstext="";
    $this->gegenkonto="";
    $this->belegfeld1="";
    $this->bearbeiter="";
    $this->mailbenachrichtigung="";
    $this->pruefsumme="";
    $this->internebemerkung="";
    $this->importfehler="";
    $this->importgroup="";
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
  function SetKonto($value) { $this->konto=$value; }
  function GetKonto() { return $this->konto; }
  function SetBuchung($value) { $this->buchung=$value; }
  function GetBuchung() { return $this->buchung; }
  function SetVorgang($value) { $this->vorgang=$value; }
  function GetVorgang() { return $this->vorgang; }
  function SetSoll($value) { $this->soll=$value; }
  function GetSoll() { return $this->soll; }
  function SetHaben($value) { $this->haben=$value; }
  function GetHaben() { return $this->haben; }
  function SetGebuehr($value) { $this->gebuehr=$value; }
  function GetGebuehr() { return $this->gebuehr; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetFertig($value) { $this->fertig=$value; }
  function GetFertig() { return $this->fertig; }
  function SetDatev_Abgeschlossen($value) { $this->datev_abgeschlossen=$value; }
  function GetDatev_Abgeschlossen() { return $this->datev_abgeschlossen; }
  function SetBuchungstext($value) { $this->buchungstext=$value; }
  function GetBuchungstext() { return $this->buchungstext; }
  function SetGegenkonto($value) { $this->gegenkonto=$value; }
  function GetGegenkonto() { return $this->gegenkonto; }
  function SetBelegfeld1($value) { $this->belegfeld1=$value; }
  function GetBelegfeld1() { return $this->belegfeld1; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetMailbenachrichtigung($value) { $this->mailbenachrichtigung=$value; }
  function GetMailbenachrichtigung() { return $this->mailbenachrichtigung; }
  function SetPruefsumme($value) { $this->pruefsumme=$value; }
  function GetPruefsumme() { return $this->pruefsumme; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetImportfehler($value) { $this->importfehler=$value; }
  function GetImportfehler() { return $this->importfehler; }
  function SetImportgroup($value) { $this->importgroup=$value; }
  function GetImportgroup() { return $this->importgroup; }

}

?>