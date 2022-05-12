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

class ObjGenPaketannahme
{

  private  $id;
  private  $adresse;
  private  $datum;
  private  $verpackungszustand;
  private  $bemerkung;
  private  $foto;
  private  $gewicht;
  private  $bearbeiter;
  private  $projekt;
  private  $vorlage;
  private  $vorlageid;
  private  $zahlung;
  private  $betrag;
  private  $status;
  private  $beipack_rechnung;
  private  $beipack_lieferschein;
  private  $beipack_anschreiben;
  private  $beipack_gesamt;
  private  $bearbeiter_distribution;
  private  $postgrund;
  private  $logdatei;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM paketannahme WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->datum=$result[datum];
    $this->verpackungszustand=$result[verpackungszustand];
    $this->bemerkung=$result[bemerkung];
    $this->foto=$result[foto];
    $this->gewicht=$result[gewicht];
    $this->bearbeiter=$result[bearbeiter];
    $this->projekt=$result[projekt];
    $this->vorlage=$result[vorlage];
    $this->vorlageid=$result[vorlageid];
    $this->zahlung=$result[zahlung];
    $this->betrag=$result[betrag];
    $this->status=$result[status];
    $this->beipack_rechnung=$result[beipack_rechnung];
    $this->beipack_lieferschein=$result[beipack_lieferschein];
    $this->beipack_anschreiben=$result[beipack_anschreiben];
    $this->beipack_gesamt=$result[beipack_gesamt];
    $this->bearbeiter_distribution=$result[bearbeiter_distribution];
    $this->postgrund=$result[postgrund];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO paketannahme (id,adresse,datum,verpackungszustand,bemerkung,foto,gewicht,bearbeiter,projekt,vorlage,vorlageid,zahlung,betrag,status,beipack_rechnung,beipack_lieferschein,beipack_anschreiben,beipack_gesamt,bearbeiter_distribution,postgrund,logdatei)
      VALUES('','{$this->adresse}','{$this->datum}','{$this->verpackungszustand}','{$this->bemerkung}','{$this->foto}','{$this->gewicht}','{$this->bearbeiter}','{$this->projekt}','{$this->vorlage}','{$this->vorlageid}','{$this->zahlung}','{$this->betrag}','{$this->status}','{$this->beipack_rechnung}','{$this->beipack_lieferschein}','{$this->beipack_anschreiben}','{$this->beipack_gesamt}','{$this->bearbeiter_distribution}','{$this->postgrund}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE paketannahme SET
      adresse='{$this->adresse}',
      datum='{$this->datum}',
      verpackungszustand='{$this->verpackungszustand}',
      bemerkung='{$this->bemerkung}',
      foto='{$this->foto}',
      gewicht='{$this->gewicht}',
      bearbeiter='{$this->bearbeiter}',
      projekt='{$this->projekt}',
      vorlage='{$this->vorlage}',
      vorlageid='{$this->vorlageid}',
      zahlung='{$this->zahlung}',
      betrag='{$this->betrag}',
      status='{$this->status}',
      beipack_rechnung='{$this->beipack_rechnung}',
      beipack_lieferschein='{$this->beipack_lieferschein}',
      beipack_anschreiben='{$this->beipack_anschreiben}',
      beipack_gesamt='{$this->beipack_gesamt}',
      bearbeiter_distribution='{$this->bearbeiter_distribution}',
      postgrund='{$this->postgrund}',
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

    $sql = "DELETE FROM paketannahme WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->datum="";
    $this->verpackungszustand="";
    $this->bemerkung="";
    $this->foto="";
    $this->gewicht="";
    $this->bearbeiter="";
    $this->projekt="";
    $this->vorlage="";
    $this->vorlageid="";
    $this->zahlung="";
    $this->betrag="";
    $this->status="";
    $this->beipack_rechnung="";
    $this->beipack_lieferschein="";
    $this->beipack_anschreiben="";
    $this->beipack_gesamt="";
    $this->bearbeiter_distribution="";
    $this->postgrund="";
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
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetVerpackungszustand($value) { $this->verpackungszustand=$value; }
  function GetVerpackungszustand() { return $this->verpackungszustand; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetFoto($value) { $this->foto=$value; }
  function GetFoto() { return $this->foto; }
  function SetGewicht($value) { $this->gewicht=$value; }
  function GetGewicht() { return $this->gewicht; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetVorlage($value) { $this->vorlage=$value; }
  function GetVorlage() { return $this->vorlage; }
  function SetVorlageid($value) { $this->vorlageid=$value; }
  function GetVorlageid() { return $this->vorlageid; }
  function SetZahlung($value) { $this->zahlung=$value; }
  function GetZahlung() { return $this->zahlung; }
  function SetBetrag($value) { $this->betrag=$value; }
  function GetBetrag() { return $this->betrag; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetBeipack_Rechnung($value) { $this->beipack_rechnung=$value; }
  function GetBeipack_Rechnung() { return $this->beipack_rechnung; }
  function SetBeipack_Lieferschein($value) { $this->beipack_lieferschein=$value; }
  function GetBeipack_Lieferschein() { return $this->beipack_lieferschein; }
  function SetBeipack_Anschreiben($value) { $this->beipack_anschreiben=$value; }
  function GetBeipack_Anschreiben() { return $this->beipack_anschreiben; }
  function SetBeipack_Gesamt($value) { $this->beipack_gesamt=$value; }
  function GetBeipack_Gesamt() { return $this->beipack_gesamt; }
  function SetBearbeiter_Distribution($value) { $this->bearbeiter_distribution=$value; }
  function GetBearbeiter_Distribution() { return $this->bearbeiter_distribution; }
  function SetPostgrund($value) { $this->postgrund=$value; }
  function GetPostgrund() { return $this->postgrund; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>