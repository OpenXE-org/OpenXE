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

class ObjGenBestellung_Position
{

  private  $id;
  private  $bestellung;
  private  $artikel;
  private  $projekt;
  private  $bezeichnunglieferant;
  private  $bestellnummer;
  private  $beschreibung;
  private  $menge;
  private  $preis;
  private  $waehrung;
  private  $lieferdatum;
  private  $vpe;
  private  $sort;
  private  $status;
  private  $umsatzsteuer;
  private  $bemerkung;
  private  $geliefert;
  private  $mengemanuellgeliefertaktiviert;
  private  $manuellgeliefertbearbeiter;
  private  $abgerechnet;
  private  $logdatei;
  private  $abgeschlossen;
  private  $einheit;
  private  $zolltarifnummer;
  private  $herkunftsland;
  private  $artikelnummerkunde;
  private  $auftrag_position_id;
  private  $preisanfrage_position_id;
  private  $freifeld1;
  private  $freifeld2;
  private  $freifeld3;
  private  $freifeld4;
  private  $freifeld5;
  private  $freifeld6;
  private  $freifeld7;
  private  $freifeld8;
  private  $freifeld9;
  private  $freifeld10;
  private  $auswahlmenge;
  private  $auswahletiketten;
  private  $auswahllagerplatz;
  private  $teilprojekt;
  private  $steuersatz;
  private  $steuertext;
  private  $erloese;
  private  $erloesefestschreiben;
  private  $freifeld11;
  private  $freifeld12;
  private  $freifeld13;
  private  $freifeld14;
  private  $freifeld15;
  private  $freifeld16;
  private  $freifeld17;
  private  $freifeld18;
  private  $freifeld19;
  private  $freifeld20;
  private  $skontobetrag;
  private  $freifeld21;
  private  $freifeld22;
  private  $freifeld23;
  private  $freifeld24;
  private  $freifeld25;
  private  $freifeld26;
  private  $freifeld27;
  private  $freifeld28;
  private  $freifeld29;
  private  $freifeld30;
  private  $freifeld31;
  private  $freifeld32;
  private  $freifeld33;
  private  $freifeld34;
  private  $freifeld35;
  private  $freifeld36;
  private  $freifeld37;
  private  $freifeld38;
  private  $freifeld39;
  private  $freifeld40;
  private  $kostenstelle;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM bestellung_position WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->bestellung=$result['bestellung'];
    $this->artikel=$result['artikel'];
    $this->projekt=$result['projekt'];
    $this->bezeichnunglieferant=$result['bezeichnunglieferant'];
    $this->bestellnummer=$result['bestellnummer'];
    $this->beschreibung=$result['beschreibung'];
    $this->menge=$result['menge'];
    $this->preis=$result['preis'];
    $this->waehrung=$result['waehrung'];
    $this->lieferdatum=$result['lieferdatum'];
    $this->vpe=$result['vpe'];
    $this->sort=$result['sort'];
    $this->status=$result['status'];
    $this->umsatzsteuer=$result['umsatzsteuer'];
    $this->bemerkung=$result['bemerkung'];
    $this->geliefert=$result['geliefert'];
    $this->mengemanuellgeliefertaktiviert=$result['mengemanuellgeliefertaktiviert'];
    $this->manuellgeliefertbearbeiter=$result['manuellgeliefertbearbeiter'];
    $this->abgerechnet=$result['abgerechnet'];
    $this->logdatei=$result['logdatei'];
    $this->abgeschlossen=$result['abgeschlossen'];
    $this->einheit=$result['einheit'];
    $this->zolltarifnummer=$result['zolltarifnummer'];
    $this->herkunftsland=$result['herkunftsland'];
    $this->artikelnummerkunde=$result['artikelnummerkunde'];
    $this->auftrag_position_id=$result['auftrag_position_id'];
    $this->preisanfrage_position_id=$result['preisanfrage_position_id'];
    $this->freifeld1=$result['freifeld1'];
    $this->freifeld2=$result['freifeld2'];
    $this->freifeld3=$result['freifeld3'];
    $this->freifeld4=$result['freifeld4'];
    $this->freifeld5=$result['freifeld5'];
    $this->freifeld6=$result['freifeld6'];
    $this->freifeld7=$result['freifeld7'];
    $this->freifeld8=$result['freifeld8'];
    $this->freifeld9=$result['freifeld9'];
    $this->freifeld10=$result['freifeld10'];
    $this->auswahlmenge=$result['auswahlmenge'];
    $this->auswahletiketten=$result['auswahletiketten'];
    $this->auswahllagerplatz=$result['auswahllagerplatz'];
    $this->teilprojekt=$result['teilprojekt'];
    $this->steuersatz=$result['steuersatz'];
    $this->steuertext=$result['steuertext'];
    $this->erloese=$result['erloese'];
    $this->erloesefestschreiben=$result['erloesefestschreiben'];
    $this->freifeld11=$result['freifeld11'];
    $this->freifeld12=$result['freifeld12'];
    $this->freifeld13=$result['freifeld13'];
    $this->freifeld14=$result['freifeld14'];
    $this->freifeld15=$result['freifeld15'];
    $this->freifeld16=$result['freifeld16'];
    $this->freifeld17=$result['freifeld17'];
    $this->freifeld18=$result['freifeld18'];
    $this->freifeld19=$result['freifeld19'];
    $this->freifeld20=$result['freifeld20'];
    $this->skontobetrag=$result['skontobetrag'];
    $this->freifeld21=$result['freifeld21'];
    $this->freifeld22=$result['freifeld22'];
    $this->freifeld23=$result['freifeld23'];
    $this->freifeld24=$result['freifeld24'];
    $this->freifeld25=$result['freifeld25'];
    $this->freifeld26=$result['freifeld26'];
    $this->freifeld27=$result['freifeld27'];
    $this->freifeld28=$result['freifeld28'];
    $this->freifeld29=$result['freifeld29'];
    $this->freifeld30=$result['freifeld30'];
    $this->freifeld31=$result['freifeld31'];
    $this->freifeld32=$result['freifeld32'];
    $this->freifeld33=$result['freifeld33'];
    $this->freifeld34=$result['freifeld34'];
    $this->freifeld35=$result['freifeld35'];
    $this->freifeld36=$result['freifeld36'];
    $this->freifeld37=$result['freifeld37'];
    $this->freifeld38=$result['freifeld38'];
    $this->freifeld39=$result['freifeld39'];
    $this->freifeld40=$result['freifeld40'];
    $this->kostenstelle=$result['kostenstelle'];
  }

  public function Create()
  {
    $sql = "INSERT INTO bestellung_position (id,bestellung,artikel,projekt,bezeichnunglieferant,bestellnummer,beschreibung,menge,preis,waehrung,lieferdatum,vpe,sort,status,umsatzsteuer,bemerkung,geliefert,mengemanuellgeliefertaktiviert,manuellgeliefertbearbeiter,abgerechnet,logdatei,abgeschlossen,einheit,zolltarifnummer,herkunftsland,artikelnummerkunde,auftrag_position_id,preisanfrage_position_id,freifeld1,freifeld2,freifeld3,freifeld4,freifeld5,freifeld6,freifeld7,freifeld8,freifeld9,freifeld10,auswahlmenge,auswahletiketten,auswahllagerplatz,teilprojekt,steuersatz,steuertext,erloese,erloesefestschreiben,freifeld11,freifeld12,freifeld13,freifeld14,freifeld15,freifeld16,freifeld17,freifeld18,freifeld19,freifeld20,skontobetrag,freifeld21,freifeld22,freifeld23,freifeld24,freifeld25,freifeld26,freifeld27,freifeld28,freifeld29,freifeld30,freifeld31,freifeld32,freifeld33,freifeld34,freifeld35,freifeld36,freifeld37,freifeld38,freifeld39,freifeld40,kostenstelle)
      VALUES('','{$this->bestellung}','{$this->artikel}','{$this->projekt}','{$this->bezeichnunglieferant}','{$this->bestellnummer}','{$this->beschreibung}','{$this->menge}','{$this->preis}','{$this->waehrung}','{$this->lieferdatum}','{$this->vpe}','{$this->sort}','{$this->status}','{$this->umsatzsteuer}','{$this->bemerkung}','{$this->geliefert}','{$this->mengemanuellgeliefertaktiviert}','{$this->manuellgeliefertbearbeiter}','{$this->abgerechnet}','{$this->logdatei}','{$this->abgeschlossen}','{$this->einheit}','{$this->zolltarifnummer}','{$this->herkunftsland}','{$this->artikelnummerkunde}','{$this->auftrag_position_id}','{$this->preisanfrage_position_id}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->freifeld4}','{$this->freifeld5}','{$this->freifeld6}','{$this->freifeld7}','{$this->freifeld8}','{$this->freifeld9}','{$this->freifeld10}','{$this->auswahlmenge}','{$this->auswahletiketten}','{$this->auswahllagerplatz}','{$this->teilprojekt}','{$this->steuersatz}','{$this->steuertext}','{$this->erloese}','{$this->erloesefestschreiben}','{$this->freifeld11}','{$this->freifeld12}','{$this->freifeld13}','{$this->freifeld14}','{$this->freifeld15}','{$this->freifeld16}','{$this->freifeld17}','{$this->freifeld18}','{$this->freifeld19}','{$this->freifeld20}','{$this->skontobetrag}','{$this->freifeld21}','{$this->freifeld22}','{$this->freifeld23}','{$this->freifeld24}','{$this->freifeld25}','{$this->freifeld26}','{$this->freifeld27}','{$this->freifeld28}','{$this->freifeld29}','{$this->freifeld30}','{$this->freifeld31}','{$this->freifeld32}','{$this->freifeld33}','{$this->freifeld34}','{$this->freifeld35}','{$this->freifeld36}','{$this->freifeld37}','{$this->freifeld38}','{$this->freifeld39}','{$this->freifeld40}','{$this->kostenstelle}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE bestellung_position SET
      bestellung='{$this->bestellung}',
      artikel='{$this->artikel}',
      projekt='{$this->projekt}',
      bezeichnunglieferant='{$this->bezeichnunglieferant}',
      bestellnummer='{$this->bestellnummer}',
      beschreibung='{$this->beschreibung}',
      menge='{$this->menge}',
      preis='{$this->preis}',
      waehrung='{$this->waehrung}',
      lieferdatum='{$this->lieferdatum}',
      vpe='{$this->vpe}',
      sort='{$this->sort}',
      status='{$this->status}',
      umsatzsteuer='{$this->umsatzsteuer}',
      bemerkung='{$this->bemerkung}',
      geliefert='{$this->geliefert}',
      mengemanuellgeliefertaktiviert='{$this->mengemanuellgeliefertaktiviert}',
      manuellgeliefertbearbeiter='{$this->manuellgeliefertbearbeiter}',
      abgerechnet='{$this->abgerechnet}',
      logdatei='{$this->logdatei}',
      abgeschlossen='{$this->abgeschlossen}',
      einheit='{$this->einheit}',
      zolltarifnummer='{$this->zolltarifnummer}',
      herkunftsland='{$this->herkunftsland}',
      artikelnummerkunde='{$this->artikelnummerkunde}',
      auftrag_position_id='{$this->auftrag_position_id}',
      preisanfrage_position_id='{$this->preisanfrage_position_id}',
      freifeld1='{$this->freifeld1}',
      freifeld2='{$this->freifeld2}',
      freifeld3='{$this->freifeld3}',
      freifeld4='{$this->freifeld4}',
      freifeld5='{$this->freifeld5}',
      freifeld6='{$this->freifeld6}',
      freifeld7='{$this->freifeld7}',
      freifeld8='{$this->freifeld8}',
      freifeld9='{$this->freifeld9}',
      freifeld10='{$this->freifeld10}',
      auswahlmenge='{$this->auswahlmenge}',
      auswahletiketten='{$this->auswahletiketten}',
      auswahllagerplatz='{$this->auswahllagerplatz}',
      teilprojekt='{$this->teilprojekt}',
      steuersatz='{$this->steuersatz}',
      steuertext='{$this->steuertext}',
      erloese='{$this->erloese}',
      erloesefestschreiben='{$this->erloesefestschreiben}',
      freifeld11='{$this->freifeld11}',
      freifeld12='{$this->freifeld12}',
      freifeld13='{$this->freifeld13}',
      freifeld14='{$this->freifeld14}',
      freifeld15='{$this->freifeld15}',
      freifeld16='{$this->freifeld16}',
      freifeld17='{$this->freifeld17}',
      freifeld18='{$this->freifeld18}',
      freifeld19='{$this->freifeld19}',
      freifeld20='{$this->freifeld20}',
      skontobetrag='{$this->skontobetrag}',
      freifeld21='{$this->freifeld21}',
      freifeld22='{$this->freifeld22}',
      freifeld23='{$this->freifeld23}',
      freifeld24='{$this->freifeld24}',
      freifeld25='{$this->freifeld25}',
      freifeld26='{$this->freifeld26}',
      freifeld27='{$this->freifeld27}',
      freifeld28='{$this->freifeld28}',
      freifeld29='{$this->freifeld29}',
      freifeld30='{$this->freifeld30}',
      freifeld31='{$this->freifeld31}',
      freifeld32='{$this->freifeld32}',
      freifeld33='{$this->freifeld33}',
      freifeld34='{$this->freifeld34}',
      freifeld35='{$this->freifeld35}',
      freifeld36='{$this->freifeld36}',
      freifeld37='{$this->freifeld37}',
      freifeld38='{$this->freifeld38}',
      freifeld39='{$this->freifeld39}',
      freifeld40='{$this->freifeld40}',
      kostenstelle='{$this->kostenstelle}'
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

    $sql = "DELETE FROM bestellung_position WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bestellung="";
    $this->artikel="";
    $this->projekt="";
    $this->bezeichnunglieferant="";
    $this->bestellnummer="";
    $this->beschreibung="";
    $this->menge="";
    $this->preis="";
    $this->waehrung="";
    $this->lieferdatum="";
    $this->vpe="";
    $this->sort="";
    $this->status="";
    $this->umsatzsteuer="";
    $this->bemerkung="";
    $this->geliefert="";
    $this->mengemanuellgeliefertaktiviert="";
    $this->manuellgeliefertbearbeiter="";
    $this->abgerechnet="";
    $this->logdatei="";
    $this->abgeschlossen="";
    $this->einheit="";
    $this->zolltarifnummer="";
    $this->herkunftsland="";
    $this->artikelnummerkunde="";
    $this->auftrag_position_id="";
    $this->preisanfrage_position_id="";
    $this->freifeld1="";
    $this->freifeld2="";
    $this->freifeld3="";
    $this->freifeld4="";
    $this->freifeld5="";
    $this->freifeld6="";
    $this->freifeld7="";
    $this->freifeld8="";
    $this->freifeld9="";
    $this->freifeld10="";
    $this->auswahlmenge="";
    $this->auswahletiketten="";
    $this->auswahllagerplatz="";
    $this->teilprojekt="";
    $this->steuersatz="";
    $this->steuertext="";
    $this->erloese="";
    $this->erloesefestschreiben="";
    $this->freifeld11="";
    $this->freifeld12="";
    $this->freifeld13="";
    $this->freifeld14="";
    $this->freifeld15="";
    $this->freifeld16="";
    $this->freifeld17="";
    $this->freifeld18="";
    $this->freifeld19="";
    $this->freifeld20="";
    $this->skontobetrag="";
    $this->freifeld21="";
    $this->freifeld22="";
    $this->freifeld23="";
    $this->freifeld24="";
    $this->freifeld25="";
    $this->freifeld26="";
    $this->freifeld27="";
    $this->freifeld28="";
    $this->freifeld29="";
    $this->freifeld30="";
    $this->freifeld31="";
    $this->freifeld32="";
    $this->freifeld33="";
    $this->freifeld34="";
    $this->freifeld35="";
    $this->freifeld36="";
    $this->freifeld37="";
    $this->freifeld38="";
    $this->freifeld39="";
    $this->freifeld40="";
    $this->kostenstelle="";
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
  function SetBestellung($value) { $this->bestellung=$value; }
  function GetBestellung() { return $this->bestellung; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBezeichnunglieferant($value) { $this->bezeichnunglieferant=$value; }
  function GetBezeichnunglieferant() { return $this->bezeichnunglieferant; }
  function SetBestellnummer($value) { $this->bestellnummer=$value; }
  function GetBestellnummer() { return $this->bestellnummer; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetPreis($value) { $this->preis=$value; }
  function GetPreis() { return $this->preis; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetLieferdatum($value) { $this->lieferdatum=$value; }
  function GetLieferdatum() { return $this->lieferdatum; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetUmsatzsteuer($value) { $this->umsatzsteuer=$value; }
  function GetUmsatzsteuer() { return $this->umsatzsteuer; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetGeliefert($value) { $this->geliefert=$value; }
  function GetGeliefert() { return $this->geliefert; }
  function SetMengemanuellgeliefertaktiviert($value) { $this->mengemanuellgeliefertaktiviert=$value; }
  function GetMengemanuellgeliefertaktiviert() { return $this->mengemanuellgeliefertaktiviert; }
  function SetManuellgeliefertbearbeiter($value) { $this->manuellgeliefertbearbeiter=$value; }
  function GetManuellgeliefertbearbeiter() { return $this->manuellgeliefertbearbeiter; }
  function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  function GetAbgerechnet() { return $this->abgerechnet; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetAbgeschlossen($value) { $this->abgeschlossen=$value; }
  function GetAbgeschlossen() { return $this->abgeschlossen; }
  function SetEinheit($value) { $this->einheit=$value; }
  function GetEinheit() { return $this->einheit; }
  function SetZolltarifnummer($value) { $this->zolltarifnummer=$value; }
  function GetZolltarifnummer() { return $this->zolltarifnummer; }
  function SetHerkunftsland($value) { $this->herkunftsland=$value; }
  function GetHerkunftsland() { return $this->herkunftsland; }
  function SetArtikelnummerkunde($value) { $this->artikelnummerkunde=$value; }
  function GetArtikelnummerkunde() { return $this->artikelnummerkunde; }
  function SetAuftrag_Position_Id($value) { $this->auftrag_position_id=$value; }
  function GetAuftrag_Position_Id() { return $this->auftrag_position_id; }
  function SetPreisanfrage_Position_Id($value) { $this->preisanfrage_position_id=$value; }
  function GetPreisanfrage_Position_Id() { return $this->preisanfrage_position_id; }
  function SetFreifeld1($value) { $this->freifeld1=$value; }
  function GetFreifeld1() { return $this->freifeld1; }
  function SetFreifeld2($value) { $this->freifeld2=$value; }
  function GetFreifeld2() { return $this->freifeld2; }
  function SetFreifeld3($value) { $this->freifeld3=$value; }
  function GetFreifeld3() { return $this->freifeld3; }
  function SetFreifeld4($value) { $this->freifeld4=$value; }
  function GetFreifeld4() { return $this->freifeld4; }
  function SetFreifeld5($value) { $this->freifeld5=$value; }
  function GetFreifeld5() { return $this->freifeld5; }
  function SetFreifeld6($value) { $this->freifeld6=$value; }
  function GetFreifeld6() { return $this->freifeld6; }
  function SetFreifeld7($value) { $this->freifeld7=$value; }
  function GetFreifeld7() { return $this->freifeld7; }
  function SetFreifeld8($value) { $this->freifeld8=$value; }
  function GetFreifeld8() { return $this->freifeld8; }
  function SetFreifeld9($value) { $this->freifeld9=$value; }
  function GetFreifeld9() { return $this->freifeld9; }
  function SetFreifeld10($value) { $this->freifeld10=$value; }
  function GetFreifeld10() { return $this->freifeld10; }
  function SetAuswahlmenge($value) { $this->auswahlmenge=$value; }
  function GetAuswahlmenge() { return $this->auswahlmenge; }
  function SetAuswahletiketten($value) { $this->auswahletiketten=$value; }
  function GetAuswahletiketten() { return $this->auswahletiketten; }
  function SetAuswahllagerplatz($value) { $this->auswahllagerplatz=$value; }
  function GetAuswahllagerplatz() { return $this->auswahllagerplatz; }
  function SetTeilprojekt($value) { $this->teilprojekt=$value; }
  function GetTeilprojekt() { return $this->teilprojekt; }
  function SetSteuersatz($value) { $this->steuersatz=$value; }
  function GetSteuersatz() { return $this->steuersatz; }
  function SetSteuertext($value) { $this->steuertext=$value; }
  function GetSteuertext() { return $this->steuertext; }
  function SetErloese($value) { $this->erloese=$value; }
  function GetErloese() { return $this->erloese; }
  function SetErloesefestschreiben($value) { $this->erloesefestschreiben=$value; }
  function GetErloesefestschreiben() { return $this->erloesefestschreiben; }
  function SetFreifeld11($value) { $this->freifeld11=$value; }
  function GetFreifeld11() { return $this->freifeld11; }
  function SetFreifeld12($value) { $this->freifeld12=$value; }
  function GetFreifeld12() { return $this->freifeld12; }
  function SetFreifeld13($value) { $this->freifeld13=$value; }
  function GetFreifeld13() { return $this->freifeld13; }
  function SetFreifeld14($value) { $this->freifeld14=$value; }
  function GetFreifeld14() { return $this->freifeld14; }
  function SetFreifeld15($value) { $this->freifeld15=$value; }
  function GetFreifeld15() { return $this->freifeld15; }
  function SetFreifeld16($value) { $this->freifeld16=$value; }
  function GetFreifeld16() { return $this->freifeld16; }
  function SetFreifeld17($value) { $this->freifeld17=$value; }
  function GetFreifeld17() { return $this->freifeld17; }
  function SetFreifeld18($value) { $this->freifeld18=$value; }
  function GetFreifeld18() { return $this->freifeld18; }
  function SetFreifeld19($value) { $this->freifeld19=$value; }
  function GetFreifeld19() { return $this->freifeld19; }
  function SetFreifeld20($value) { $this->freifeld20=$value; }
  function GetFreifeld20() { return $this->freifeld20; }
  function SetSkontobetrag($value) { $this->skontobetrag=$value; }
  function GetSkontobetrag() { return $this->skontobetrag; }
  function SetFreifeld21($value) { $this->freifeld21=$value; }
  function GetFreifeld21() { return $this->freifeld21; }
  function SetFreifeld22($value) { $this->freifeld22=$value; }
  function GetFreifeld22() { return $this->freifeld22; }
  function SetFreifeld23($value) { $this->freifeld23=$value; }
  function GetFreifeld23() { return $this->freifeld23; }
  function SetFreifeld24($value) { $this->freifeld24=$value; }
  function GetFreifeld24() { return $this->freifeld24; }
  function SetFreifeld25($value) { $this->freifeld25=$value; }
  function GetFreifeld25() { return $this->freifeld25; }
  function SetFreifeld26($value) { $this->freifeld26=$value; }
  function GetFreifeld26() { return $this->freifeld26; }
  function SetFreifeld27($value) { $this->freifeld27=$value; }
  function GetFreifeld27() { return $this->freifeld27; }
  function SetFreifeld28($value) { $this->freifeld28=$value; }
  function GetFreifeld28() { return $this->freifeld28; }
  function SetFreifeld29($value) { $this->freifeld29=$value; }
  function GetFreifeld29() { return $this->freifeld29; }
  function SetFreifeld30($value) { $this->freifeld30=$value; }
  function GetFreifeld30() { return $this->freifeld30; }
  function SetFreifeld31($value) { $this->freifeld31=$value; }
  function GetFreifeld31() { return $this->freifeld31; }
  function SetFreifeld32($value) { $this->freifeld32=$value; }
  function GetFreifeld32() { return $this->freifeld32; }
  function SetFreifeld33($value) { $this->freifeld33=$value; }
  function GetFreifeld33() { return $this->freifeld33; }
  function SetFreifeld34($value) { $this->freifeld34=$value; }
  function GetFreifeld34() { return $this->freifeld34; }
  function SetFreifeld35($value) { $this->freifeld35=$value; }
  function GetFreifeld35() { return $this->freifeld35; }
  function SetFreifeld36($value) { $this->freifeld36=$value; }
  function GetFreifeld36() { return $this->freifeld36; }
  function SetFreifeld37($value) { $this->freifeld37=$value; }
  function GetFreifeld37() { return $this->freifeld37; }
  function SetFreifeld38($value) { $this->freifeld38=$value; }
  function GetFreifeld38() { return $this->freifeld38; }
  function SetFreifeld39($value) { $this->freifeld39=$value; }
  function GetFreifeld39() { return $this->freifeld39; }
  function SetFreifeld40($value) { $this->freifeld40=$value; }
  function GetFreifeld40() { return $this->freifeld40; }
  function SetKostenstelle($value) { $this->kostenstelle=$value; }
  function GetKostenstelle() { return $this->kostenstelle; }

}

?>