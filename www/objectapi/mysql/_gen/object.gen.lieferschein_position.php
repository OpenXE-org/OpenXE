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

class ObjGenLieferschein_Position
{

  private  $id;
  private  $lieferschein;
  private  $artikel;
  private  $projekt;
  private  $bezeichnung;
  private  $beschreibung;
  private  $internerkommentar;
  private  $nummer;
  private  $seriennummer;
  private  $menge;
  private  $lieferdatum;
  private  $vpe;
  private  $sort;
  private  $status;
  private  $bemerkung;
  private  $geliefert;
  private  $abgerechnet;
  private  $logdatei;
  private  $explodiert_parent_artikel;
  private  $einheit;
  private  $zolltarifnummer;
  private  $herkunftsland;
  private  $artikelnummerkunde;
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
  private  $lieferdatumkw;
  private  $auftrag_position_id;
  private  $kostenlos;
  private  $lagertext;
  private  $teilprojekt;
  private  $explodiert_parent;
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
  private  $zolleinzelwert;
  private  $zollgesamtwert;
  private  $zollwaehrung;
  private  $zolleinzelgewicht;
  private  $zollgesamtgewicht;
  private  $nve;
  private  $packstueck;
  private  $vpemenge;
  private  $einzelstueckmenge;
  private  $ausblenden_im_pdf;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM lieferschein_position WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->lieferschein=$result['lieferschein'];
    $this->artikel=$result['artikel'];
    $this->projekt=$result['projekt'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->beschreibung=$result['beschreibung'];
    $this->internerkommentar=$result['internerkommentar'];
    $this->nummer=$result['nummer'];
    $this->seriennummer=$result['seriennummer'];
    $this->menge=$result['menge'];
    $this->lieferdatum=$result['lieferdatum'];
    $this->vpe=$result['vpe'];
    $this->sort=$result['sort'];
    $this->status=$result['status'];
    $this->bemerkung=$result['bemerkung'];
    $this->geliefert=$result['geliefert'];
    $this->abgerechnet=$result['abgerechnet'];
    $this->logdatei=$result['logdatei'];
    $this->explodiert_parent_artikel=$result['explodiert_parent_artikel'];
    $this->einheit=$result['einheit'];
    $this->zolltarifnummer=$result['zolltarifnummer'];
    $this->herkunftsland=$result['herkunftsland'];
    $this->artikelnummerkunde=$result['artikelnummerkunde'];
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
    $this->lieferdatumkw=$result['lieferdatumkw'];
    $this->auftrag_position_id=$result['auftrag_position_id'];
    $this->kostenlos=$result['kostenlos'];
    $this->lagertext=$result['lagertext'];
    $this->teilprojekt=$result['teilprojekt'];
    $this->explodiert_parent=$result['explodiert_parent'];
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
    $this->zolleinzelwert=$result['zolleinzelwert'];
    $this->zollgesamtwert=$result['zollgesamtwert'];
    $this->zollwaehrung=$result['zollwaehrung'];
    $this->zolleinzelgewicht=$result['zolleinzelgewicht'];
    $this->zollgesamtgewicht=$result['zollgesamtgewicht'];
    $this->nve=$result['nve'];
    $this->packstueck=$result['packstueck'];
    $this->vpemenge=$result['vpemenge'];
    $this->einzelstueckmenge=$result['einzelstueckmenge'];
    $this->ausblenden_im_pdf=$result['ausblenden_im_pdf'];
  }

  public function Create()
  {
    $sql = "INSERT INTO lieferschein_position (id,lieferschein,artikel,projekt,bezeichnung,beschreibung,internerkommentar,nummer,seriennummer,menge,lieferdatum,vpe,sort,status,bemerkung,geliefert,abgerechnet,logdatei,explodiert_parent_artikel,einheit,zolltarifnummer,herkunftsland,artikelnummerkunde,freifeld1,freifeld2,freifeld3,freifeld4,freifeld5,freifeld6,freifeld7,freifeld8,freifeld9,freifeld10,lieferdatumkw,auftrag_position_id,kostenlos,lagertext,teilprojekt,explodiert_parent,freifeld11,freifeld12,freifeld13,freifeld14,freifeld15,freifeld16,freifeld17,freifeld18,freifeld19,freifeld20,freifeld21,freifeld22,freifeld23,freifeld24,freifeld25,freifeld26,freifeld27,freifeld28,freifeld29,freifeld30,freifeld31,freifeld32,freifeld33,freifeld34,freifeld35,freifeld36,freifeld37,freifeld38,freifeld39,freifeld40,zolleinzelwert,zollgesamtwert,zollwaehrung,zolleinzelgewicht,zollgesamtgewicht,nve,packstueck,vpemenge,einzelstueckmenge,ausblenden_im_pdf)
      VALUES('','{$this->lieferschein}','{$this->artikel}','{$this->projekt}','{$this->bezeichnung}','{$this->beschreibung}','{$this->internerkommentar}','{$this->nummer}','{$this->seriennummer}','{$this->menge}','{$this->lieferdatum}','{$this->vpe}','{$this->sort}','{$this->status}','{$this->bemerkung}','{$this->geliefert}','{$this->abgerechnet}','{$this->logdatei}','{$this->explodiert_parent_artikel}','{$this->einheit}','{$this->zolltarifnummer}','{$this->herkunftsland}','{$this->artikelnummerkunde}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->freifeld4}','{$this->freifeld5}','{$this->freifeld6}','{$this->freifeld7}','{$this->freifeld8}','{$this->freifeld9}','{$this->freifeld10}','{$this->lieferdatumkw}','{$this->auftrag_position_id}','{$this->kostenlos}','{$this->lagertext}','{$this->teilprojekt}','{$this->explodiert_parent}','{$this->freifeld11}','{$this->freifeld12}','{$this->freifeld13}','{$this->freifeld14}','{$this->freifeld15}','{$this->freifeld16}','{$this->freifeld17}','{$this->freifeld18}','{$this->freifeld19}','{$this->freifeld20}','{$this->freifeld21}','{$this->freifeld22}','{$this->freifeld23}','{$this->freifeld24}','{$this->freifeld25}','{$this->freifeld26}','{$this->freifeld27}','{$this->freifeld28}','{$this->freifeld29}','{$this->freifeld30}','{$this->freifeld31}','{$this->freifeld32}','{$this->freifeld33}','{$this->freifeld34}','{$this->freifeld35}','{$this->freifeld36}','{$this->freifeld37}','{$this->freifeld38}','{$this->freifeld39}','{$this->freifeld40}','{$this->zolleinzelwert}','{$this->zollgesamtwert}','{$this->zollwaehrung}','{$this->zolleinzelgewicht}','{$this->zollgesamtgewicht}','{$this->nve}','{$this->packstueck}','{$this->vpemenge}','{$this->einzelstueckmenge}','{$this->ausblenden_im_pdf}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE lieferschein_position SET
      lieferschein='{$this->lieferschein}',
      artikel='{$this->artikel}',
      projekt='{$this->projekt}',
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      internerkommentar='{$this->internerkommentar}',
      nummer='{$this->nummer}',
      seriennummer='{$this->seriennummer}',
      menge='{$this->menge}',
      lieferdatum='{$this->lieferdatum}',
      vpe='{$this->vpe}',
      sort='{$this->sort}',
      status='{$this->status}',
      bemerkung='{$this->bemerkung}',
      geliefert='{$this->geliefert}',
      abgerechnet='{$this->abgerechnet}',
      logdatei='{$this->logdatei}',
      explodiert_parent_artikel='{$this->explodiert_parent_artikel}',
      einheit='{$this->einheit}',
      zolltarifnummer='{$this->zolltarifnummer}',
      herkunftsland='{$this->herkunftsland}',
      artikelnummerkunde='{$this->artikelnummerkunde}',
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
      lieferdatumkw='{$this->lieferdatumkw}',
      auftrag_position_id='{$this->auftrag_position_id}',
      kostenlos='{$this->kostenlos}',
      lagertext='{$this->lagertext}',
      teilprojekt='{$this->teilprojekt}',
      explodiert_parent='{$this->explodiert_parent}',
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
      zolleinzelwert='{$this->zolleinzelwert}',
      zollgesamtwert='{$this->zollgesamtwert}',
      zollwaehrung='{$this->zollwaehrung}',
      zolleinzelgewicht='{$this->zolleinzelgewicht}',
      zollgesamtgewicht='{$this->zollgesamtgewicht}',
      nve='{$this->nve}',
      packstueck='{$this->packstueck}',
      vpemenge='{$this->vpemenge}',
      einzelstueckmenge='{$this->einzelstueckmenge}',
      ausblenden_im_pdf='{$this->ausblenden_im_pdf}'
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

    $sql = "DELETE FROM lieferschein_position WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->lieferschein="";
    $this->artikel="";
    $this->projekt="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->internerkommentar="";
    $this->nummer="";
    $this->seriennummer="";
    $this->menge="";
    $this->lieferdatum="";
    $this->vpe="";
    $this->sort="";
    $this->status="";
    $this->bemerkung="";
    $this->geliefert="";
    $this->abgerechnet="";
    $this->logdatei="";
    $this->explodiert_parent_artikel="";
    $this->einheit="";
    $this->zolltarifnummer="";
    $this->herkunftsland="";
    $this->artikelnummerkunde="";
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
    $this->lieferdatumkw="";
    $this->auftrag_position_id="";
    $this->kostenlos="";
    $this->lagertext="";
    $this->teilprojekt="";
    $this->explodiert_parent="";
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
    $this->zolleinzelwert="";
    $this->zollgesamtwert="";
    $this->zollwaehrung="";
    $this->zolleinzelgewicht="";
    $this->zollgesamtgewicht="";
    $this->nve="";
    $this->packstueck="";
    $this->vpemenge="";
    $this->einzelstueckmenge="";
    $this->ausblenden_im_pdf="";
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
  function SetLieferschein($value) { $this->lieferschein=$value; }
  function GetLieferschein() { return $this->lieferschein; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetInternerkommentar($value) { $this->internerkommentar=$value; }
  function GetInternerkommentar() { return $this->internerkommentar; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetSeriennummer($value) { $this->seriennummer=$value; }
  function GetSeriennummer() { return $this->seriennummer; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetLieferdatum($value) { $this->lieferdatum=$value; }
  function GetLieferdatum() { return $this->lieferdatum; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetGeliefert($value) { $this->geliefert=$value; }
  function GetGeliefert() { return $this->geliefert; }
  function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  function GetAbgerechnet() { return $this->abgerechnet; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetExplodiert_Parent_Artikel($value) { $this->explodiert_parent_artikel=$value; }
  function GetExplodiert_Parent_Artikel() { return $this->explodiert_parent_artikel; }
  function SetEinheit($value) { $this->einheit=$value; }
  function GetEinheit() { return $this->einheit; }
  function SetZolltarifnummer($value) { $this->zolltarifnummer=$value; }
  function GetZolltarifnummer() { return $this->zolltarifnummer; }
  function SetHerkunftsland($value) { $this->herkunftsland=$value; }
  function GetHerkunftsland() { return $this->herkunftsland; }
  function SetArtikelnummerkunde($value) { $this->artikelnummerkunde=$value; }
  function GetArtikelnummerkunde() { return $this->artikelnummerkunde; }
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
  function SetLieferdatumkw($value) { $this->lieferdatumkw=$value; }
  function GetLieferdatumkw() { return $this->lieferdatumkw; }
  function SetAuftrag_Position_Id($value) { $this->auftrag_position_id=$value; }
  function GetAuftrag_Position_Id() { return $this->auftrag_position_id; }
  function SetKostenlos($value) { $this->kostenlos=$value; }
  function GetKostenlos() { return $this->kostenlos; }
  function SetLagertext($value) { $this->lagertext=$value; }
  function GetLagertext() { return $this->lagertext; }
  function SetTeilprojekt($value) { $this->teilprojekt=$value; }
  function GetTeilprojekt() { return $this->teilprojekt; }
  function SetExplodiert_Parent($value) { $this->explodiert_parent=$value; }
  function GetExplodiert_Parent() { return $this->explodiert_parent; }
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
  function SetZolleinzelwert($value) { $this->zolleinzelwert=$value; }
  function GetZolleinzelwert() { return $this->zolleinzelwert; }
  function SetZollgesamtwert($value) { $this->zollgesamtwert=$value; }
  function GetZollgesamtwert() { return $this->zollgesamtwert; }
  function SetZollwaehrung($value) { $this->zollwaehrung=$value; }
  function GetZollwaehrung() { return $this->zollwaehrung; }
  function SetZolleinzelgewicht($value) { $this->zolleinzelgewicht=$value; }
  function GetZolleinzelgewicht() { return $this->zolleinzelgewicht; }
  function SetZollgesamtgewicht($value) { $this->zollgesamtgewicht=$value; }
  function GetZollgesamtgewicht() { return $this->zollgesamtgewicht; }
  function SetNve($value) { $this->nve=$value; }
  function GetNve() { return $this->nve; }
  function SetPackstueck($value) { $this->packstueck=$value; }
  function GetPackstueck() { return $this->packstueck; }
  function SetVpemenge($value) { $this->vpemenge=$value; }
  function GetVpemenge() { return $this->vpemenge; }
  function SetEinzelstueckmenge($value) { $this->einzelstueckmenge=$value; }
  function GetEinzelstueckmenge() { return $this->einzelstueckmenge; }
  function SetAusblenden_Im_Pdf($value) { $this->ausblenden_im_pdf=$value; }
  function GetAusblenden_Im_Pdf() { return $this->ausblenden_im_pdf; }

}

?>