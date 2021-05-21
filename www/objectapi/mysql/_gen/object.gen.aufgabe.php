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

class ObjGenAufgabe
{

  private  $id;
  private  $adresse;
  private  $aufgabe;
  private  $beschreibung;
  private  $prio;
  private  $projekt;
  private  $kostenstelle;
  private  $initiator;
  private  $angelegt_am;
  private  $startdatum;
  private  $startzeit;
  private  $intervall_tage;
  private  $stunden;
  private  $abgabe_bis;
  private  $abgeschlossen;
  private  $abgeschlossen_am;
  private  $sonstiges;
  private  $bearbeiter;
  private  $logdatei;
  private  $startseite;
  private  $oeffentlich;
  private  $emailerinnerung;
  private  $emailerinnerung_tage;
  private  $note_x;
  private  $note_y;
  private  $note_z;
  private  $note_color;
  private  $pinwand;
  private  $vorankuendigung;
  private  $status;
  private  $ganztags;
  private  $zeiterfassung_pflicht;
  private  $zeiterfassung_abrechnung;
  private  $kunde;
  private  $pinwand_id;
  private  $sort;
  private  $abgabe_bis_zeit;
  private  $email_gesendet_vorankuendigung;
  private  $email_gesendet;
  private  $teilprojekt;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->adresse=$result['adresse'];
    $this->aufgabe=$result['aufgabe'];
    $this->beschreibung=$result['beschreibung'];
    $this->prio=$result['prio'];
    $this->projekt=$result['projekt'];
    $this->kostenstelle=$result['kostenstelle'];
    $this->initiator=$result['initiator'];
    $this->angelegt_am=$result['angelegt_am'];
    $this->startdatum=$result['startdatum'];
    $this->startzeit=$result['startzeit'];
    $this->intervall_tage=$result['intervall_tage'];
    $this->stunden=$result['stunden'];
    $this->abgabe_bis=$result['abgabe_bis'];
    $this->abgeschlossen=$result['abgeschlossen'];
    $this->abgeschlossen_am=$result['abgeschlossen_am'];
    $this->sonstiges=$result['sonstiges'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->logdatei=$result['logdatei'];
    $this->startseite=$result['startseite'];
    $this->oeffentlich=$result['oeffentlich'];
    $this->emailerinnerung=$result['emailerinnerung'];
    $this->emailerinnerung_tage=$result['emailerinnerung_tage'];
    $this->note_x=$result['note_x'];
    $this->note_y=$result['note_y'];
    $this->note_z=$result['note_z'];
    $this->note_color=$result['note_color'];
    $this->pinwand=$result['pinwand'];
    $this->vorankuendigung=$result['vorankuendigung'];
    $this->status=$result['status'];
    $this->ganztags=$result['ganztags'];
    $this->zeiterfassung_pflicht=$result['zeiterfassung_pflicht'];
    $this->zeiterfassung_abrechnung=$result['zeiterfassung_abrechnung'];
    $this->kunde=$result['kunde'];
    $this->pinwand_id=$result['pinwand_id'];
    $this->sort=$result['sort'];
    $this->abgabe_bis_zeit=$result['abgabe_bis_zeit'];
    $this->email_gesendet_vorankuendigung=$result['email_gesendet_vorankuendigung'];
    $this->email_gesendet=$result['email_gesendet'];
    $this->teilprojekt=$result['teilprojekt'];
  }

  public function Create()
  {
    $sql = "INSERT INTO aufgabe (id,adresse,aufgabe,beschreibung,prio,projekt,kostenstelle,initiator,angelegt_am,startdatum,startzeit,intervall_tage,stunden,abgabe_bis,abgeschlossen,abgeschlossen_am,sonstiges,bearbeiter,logdatei,startseite,oeffentlich,emailerinnerung,emailerinnerung_tage,note_x,note_y,note_z,note_color,pinwand,vorankuendigung,status,ganztags,zeiterfassung_pflicht,zeiterfassung_abrechnung,kunde,pinwand_id,sort,abgabe_bis_zeit,email_gesendet_vorankuendigung,email_gesendet,teilprojekt)
      VALUES('','{$this->adresse}','{$this->aufgabe}','{$this->beschreibung}','{$this->prio}','{$this->projekt}','{$this->kostenstelle}','{$this->initiator}','{$this->angelegt_am}','{$this->startdatum}','{$this->startzeit}','{$this->intervall_tage}','{$this->stunden}','{$this->abgabe_bis}','{$this->abgeschlossen}','{$this->abgeschlossen_am}','{$this->sonstiges}','{$this->bearbeiter}','{$this->logdatei}','{$this->startseite}','{$this->oeffentlich}','{$this->emailerinnerung}','{$this->emailerinnerung_tage}','{$this->note_x}','{$this->note_y}','{$this->note_z}','{$this->note_color}','{$this->pinwand}','{$this->vorankuendigung}','{$this->status}','{$this->ganztags}','{$this->zeiterfassung_pflicht}','{$this->zeiterfassung_abrechnung}','{$this->kunde}','{$this->pinwand_id}','{$this->sort}','{$this->abgabe_bis_zeit}','{$this->email_gesendet_vorankuendigung}','{$this->email_gesendet}','{$this->teilprojekt}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE aufgabe SET
      adresse='{$this->adresse}',
      aufgabe='{$this->aufgabe}',
      beschreibung='{$this->beschreibung}',
      prio='{$this->prio}',
      projekt='{$this->projekt}',
      kostenstelle='{$this->kostenstelle}',
      initiator='{$this->initiator}',
      angelegt_am='{$this->angelegt_am}',
      startdatum='{$this->startdatum}',
      startzeit='{$this->startzeit}',
      intervall_tage='{$this->intervall_tage}',
      stunden='{$this->stunden}',
      abgabe_bis='{$this->abgabe_bis}',
      abgeschlossen='{$this->abgeschlossen}',
      abgeschlossen_am='{$this->abgeschlossen_am}',
      sonstiges='{$this->sonstiges}',
      bearbeiter='{$this->bearbeiter}',
      logdatei='{$this->logdatei}',
      startseite='{$this->startseite}',
      oeffentlich='{$this->oeffentlich}',
      emailerinnerung='{$this->emailerinnerung}',
      emailerinnerung_tage='{$this->emailerinnerung_tage}',
      note_x='{$this->note_x}',
      note_y='{$this->note_y}',
      note_z='{$this->note_z}',
      note_color='{$this->note_color}',
      pinwand='{$this->pinwand}',
      vorankuendigung='{$this->vorankuendigung}',
      status='{$this->status}',
      ganztags='{$this->ganztags}',
      zeiterfassung_pflicht='{$this->zeiterfassung_pflicht}',
      zeiterfassung_abrechnung='{$this->zeiterfassung_abrechnung}',
      kunde='{$this->kunde}',
      pinwand_id='{$this->pinwand_id}',
      sort='{$this->sort}',
      abgabe_bis_zeit='{$this->abgabe_bis_zeit}',
      email_gesendet_vorankuendigung='{$this->email_gesendet_vorankuendigung}',
      email_gesendet='{$this->email_gesendet}',
      teilprojekt='{$this->teilprojekt}'
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

    $sql = "DELETE FROM aufgabe WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->aufgabe="";
    $this->beschreibung="";
    $this->prio="";
    $this->projekt="";
    $this->kostenstelle="";
    $this->initiator="";
    $this->angelegt_am="";
    $this->startdatum="";
    $this->startzeit="";
    $this->intervall_tage="";
    $this->stunden="";
    $this->abgabe_bis="";
    $this->abgeschlossen="";
    $this->abgeschlossen_am="";
    $this->sonstiges="";
    $this->bearbeiter="";
    $this->logdatei="";
    $this->startseite="";
    $this->oeffentlich="";
    $this->emailerinnerung="";
    $this->emailerinnerung_tage="";
    $this->note_x="";
    $this->note_y="";
    $this->note_z="";
    $this->note_color="";
    $this->pinwand="";
    $this->vorankuendigung="";
    $this->status="";
    $this->ganztags="";
    $this->zeiterfassung_pflicht="";
    $this->zeiterfassung_abrechnung="";
    $this->kunde="";
    $this->pinwand_id="";
    $this->sort="";
    $this->abgabe_bis_zeit="";
    $this->email_gesendet_vorankuendigung="";
    $this->email_gesendet="";
    $this->teilprojekt="";
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
  function SetAufgabe($value) { $this->aufgabe=$value; }
  function GetAufgabe() { return $this->aufgabe; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetPrio($value) { $this->prio=$value; }
  function GetPrio() { return $this->prio; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetKostenstelle($value) { $this->kostenstelle=$value; }
  function GetKostenstelle() { return $this->kostenstelle; }
  function SetInitiator($value) { $this->initiator=$value; }
  function GetInitiator() { return $this->initiator; }
  function SetAngelegt_Am($value) { $this->angelegt_am=$value; }
  function GetAngelegt_Am() { return $this->angelegt_am; }
  function SetStartdatum($value) { $this->startdatum=$value; }
  function GetStartdatum() { return $this->startdatum; }
  function SetStartzeit($value) { $this->startzeit=$value; }
  function GetStartzeit() { return $this->startzeit; }
  function SetIntervall_Tage($value) { $this->intervall_tage=$value; }
  function GetIntervall_Tage() { return $this->intervall_tage; }
  function SetStunden($value) { $this->stunden=$value; }
  function GetStunden() { return $this->stunden; }
  function SetAbgabe_Bis($value) { $this->abgabe_bis=$value; }
  function GetAbgabe_Bis() { return $this->abgabe_bis; }
  function SetAbgeschlossen($value) { $this->abgeschlossen=$value; }
  function GetAbgeschlossen() { return $this->abgeschlossen; }
  function SetAbgeschlossen_Am($value) { $this->abgeschlossen_am=$value; }
  function GetAbgeschlossen_Am() { return $this->abgeschlossen_am; }
  function SetSonstiges($value) { $this->sonstiges=$value; }
  function GetSonstiges() { return $this->sonstiges; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetStartseite($value) { $this->startseite=$value; }
  function GetStartseite() { return $this->startseite; }
  function SetOeffentlich($value) { $this->oeffentlich=$value; }
  function GetOeffentlich() { return $this->oeffentlich; }
  function SetEmailerinnerung($value) { $this->emailerinnerung=$value; }
  function GetEmailerinnerung() { return $this->emailerinnerung; }
  function SetEmailerinnerung_Tage($value) { $this->emailerinnerung_tage=$value; }
  function GetEmailerinnerung_Tage() { return $this->emailerinnerung_tage; }
  function SetNote_X($value) { $this->note_x=$value; }
  function GetNote_X() { return $this->note_x; }
  function SetNote_Y($value) { $this->note_y=$value; }
  function GetNote_Y() { return $this->note_y; }
  function SetNote_Z($value) { $this->note_z=$value; }
  function GetNote_Z() { return $this->note_z; }
  function SetNote_Color($value) { $this->note_color=$value; }
  function GetNote_Color() { return $this->note_color; }
  function SetPinwand($value) { $this->pinwand=$value; }
  function GetPinwand() { return $this->pinwand; }
  function SetVorankuendigung($value) { $this->vorankuendigung=$value; }
  function GetVorankuendigung() { return $this->vorankuendigung; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetGanztags($value) { $this->ganztags=$value; }
  function GetGanztags() { return $this->ganztags; }
  function SetZeiterfassung_Pflicht($value) { $this->zeiterfassung_pflicht=$value; }
  function GetZeiterfassung_Pflicht() { return $this->zeiterfassung_pflicht; }
  function SetZeiterfassung_Abrechnung($value) { $this->zeiterfassung_abrechnung=$value; }
  function GetZeiterfassung_Abrechnung() { return $this->zeiterfassung_abrechnung; }
  function SetKunde($value) { $this->kunde=$value; }
  function GetKunde() { return $this->kunde; }
  function SetPinwand_Id($value) { $this->pinwand_id=$value; }
  function GetPinwand_Id() { return $this->pinwand_id; }
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetAbgabe_Bis_Zeit($value) { $this->abgabe_bis_zeit=$value; }
  function GetAbgabe_Bis_Zeit() { return $this->abgabe_bis_zeit; }
  function SetEmail_Gesendet_Vorankuendigung($value) { $this->email_gesendet_vorankuendigung=$value; }
  function GetEmail_Gesendet_Vorankuendigung() { return $this->email_gesendet_vorankuendigung; }
  function SetEmail_Gesendet($value) { $this->email_gesendet=$value; }
  function GetEmail_Gesendet() { return $this->email_gesendet; }
  function SetTeilprojekt($value) { $this->teilprojekt=$value; }
  function GetTeilprojekt() { return $this->teilprojekt; }

}

?>