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

class ObjGenRetoure_Position
{

  private  $id;
  private  $retoure;
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
  private  $lieferdatumkw;
  private  $auftrag_position_id;
  private  $lieferschein_position_id;
  private  $kostenlos;
  private  $lagertext;
  private  $teilprojekt;
  private  $explodiert_parent;
  private  $ausblenden_im_pdf;
  private  $grund;
  private  $grundbeschreibung;
  private  $aktion;
  private  $aktionbeschreibung;
  private  $menge_eingang;
  private  $menge_gutschrift;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `retoure_position` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->retoure=$result['retoure'];
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
    $this->lieferdatumkw=$result['lieferdatumkw'];
    $this->auftrag_position_id=$result['auftrag_position_id'];
    $this->lieferschein_position_id=$result['lieferschein_position_id'];
    $this->kostenlos=$result['kostenlos'];
    $this->lagertext=$result['lagertext'];
    $this->teilprojekt=$result['teilprojekt'];
    $this->explodiert_parent=$result['explodiert_parent'];
    $this->ausblenden_im_pdf=$result['ausblenden_im_pdf'];
    $this->grund=$result['grund'];
    $this->grundbeschreibung=$result['grundbeschreibung'];
    $this->aktion=$result['aktion'];
    $this->aktionbeschreibung=$result['aktionbeschreibung'];
    $this->menge_eingang=$result['menge_eingang'];
    $this->menge_gutschrift=$result['menge_gutschrift'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `retoure_position` (`id`,`retoure`,`artikel`,`projekt`,`bezeichnung`,`beschreibung`,`internerkommentar`,`nummer`,`seriennummer`,`menge`,`lieferdatum`,`vpe`,`sort`,`status`,`bemerkung`,`geliefert`,`abgerechnet`,`logdatei`,`explodiert_parent_artikel`,`einheit`,`zolltarifnummer`,`herkunftsland`,`artikelnummerkunde`,`freifeld1`,`freifeld2`,`freifeld3`,`freifeld4`,`freifeld5`,`freifeld6`,`freifeld7`,`freifeld8`,`freifeld9`,`freifeld10`,`freifeld11`,`freifeld12`,`freifeld13`,`freifeld14`,`freifeld15`,`freifeld16`,`freifeld17`,`freifeld18`,`freifeld19`,`freifeld20`,`freifeld21`,`freifeld22`,`freifeld23`,`freifeld24`,`freifeld25`,`freifeld26`,`freifeld27`,`freifeld28`,`freifeld29`,`freifeld30`,`freifeld31`,`freifeld32`,`freifeld33`,`freifeld34`,`freifeld35`,`freifeld36`,`freifeld37`,`freifeld38`,`freifeld39`,`freifeld40`,`lieferdatumkw`,`auftrag_position_id`,`lieferschein_position_id`,`kostenlos`,`lagertext`,`teilprojekt`,`explodiert_parent`,`ausblenden_im_pdf`,`grund`,`grundbeschreibung`,`aktion`,`aktionbeschreibung`,`menge_eingang`,`menge_gutschrift`)
      VALUES(NULL,'{$this->retoure}','{$this->artikel}','{$this->projekt}','{$this->bezeichnung}','{$this->beschreibung}','{$this->internerkommentar}','{$this->nummer}','{$this->seriennummer}','{$this->menge}','{$this->lieferdatum}','{$this->vpe}','{$this->sort}','{$this->status}','{$this->bemerkung}','{$this->geliefert}','{$this->abgerechnet}','{$this->logdatei}','{$this->explodiert_parent_artikel}','{$this->einheit}','{$this->zolltarifnummer}','{$this->herkunftsland}','{$this->artikelnummerkunde}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->freifeld4}','{$this->freifeld5}','{$this->freifeld6}','{$this->freifeld7}','{$this->freifeld8}','{$this->freifeld9}','{$this->freifeld10}','{$this->freifeld11}','{$this->freifeld12}','{$this->freifeld13}','{$this->freifeld14}','{$this->freifeld15}','{$this->freifeld16}','{$this->freifeld17}','{$this->freifeld18}','{$this->freifeld19}','{$this->freifeld20}','{$this->freifeld21}','{$this->freifeld22}','{$this->freifeld23}','{$this->freifeld24}','{$this->freifeld25}','{$this->freifeld26}','{$this->freifeld27}','{$this->freifeld28}','{$this->freifeld29}','{$this->freifeld30}','{$this->freifeld31}','{$this->freifeld32}','{$this->freifeld33}','{$this->freifeld34}','{$this->freifeld35}','{$this->freifeld36}','{$this->freifeld37}','{$this->freifeld38}','{$this->freifeld39}','{$this->freifeld40}','{$this->lieferdatumkw}','{$this->auftrag_position_id}','{$this->lieferschein_position_id}','{$this->kostenlos}','{$this->lagertext}','{$this->teilprojekt}','{$this->explodiert_parent}','{$this->ausblenden_im_pdf}','{$this->grund}','{$this->grundbeschreibung}','{$this->aktion}','{$this->aktionbeschreibung}','{$this->menge_eingang}','{$this->menge_gutschrift}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `retoure_position` SET
      `retoure`='{$this->retoure}',
      `artikel`='{$this->artikel}',
      `projekt`='{$this->projekt}',
      `bezeichnung`='{$this->bezeichnung}',
      `beschreibung`='{$this->beschreibung}',
      `internerkommentar`='{$this->internerkommentar}',
      `nummer`='{$this->nummer}',
      `seriennummer`='{$this->seriennummer}',
      `menge`='{$this->menge}',
      `lieferdatum`='{$this->lieferdatum}',
      `vpe`='{$this->vpe}',
      `sort`='{$this->sort}',
      `status`='{$this->status}',
      `bemerkung`='{$this->bemerkung}',
      `geliefert`='{$this->geliefert}',
      `abgerechnet`='{$this->abgerechnet}',
      `logdatei`='{$this->logdatei}',
      `explodiert_parent_artikel`='{$this->explodiert_parent_artikel}',
      `einheit`='{$this->einheit}',
      `zolltarifnummer`='{$this->zolltarifnummer}',
      `herkunftsland`='{$this->herkunftsland}',
      `artikelnummerkunde`='{$this->artikelnummerkunde}',
      `freifeld1`='{$this->freifeld1}',
      `freifeld2`='{$this->freifeld2}',
      `freifeld3`='{$this->freifeld3}',
      `freifeld4`='{$this->freifeld4}',
      `freifeld5`='{$this->freifeld5}',
      `freifeld6`='{$this->freifeld6}',
      `freifeld7`='{$this->freifeld7}',
      `freifeld8`='{$this->freifeld8}',
      `freifeld9`='{$this->freifeld9}',
      `freifeld10`='{$this->freifeld10}',
      `freifeld11`='{$this->freifeld11}',
      `freifeld12`='{$this->freifeld12}',
      `freifeld13`='{$this->freifeld13}',
      `freifeld14`='{$this->freifeld14}',
      `freifeld15`='{$this->freifeld15}',
      `freifeld16`='{$this->freifeld16}',
      `freifeld17`='{$this->freifeld17}',
      `freifeld18`='{$this->freifeld18}',
      `freifeld19`='{$this->freifeld19}',
      `freifeld20`='{$this->freifeld20}',
      `freifeld21`='{$this->freifeld21}',
      `freifeld22`='{$this->freifeld22}',
      `freifeld23`='{$this->freifeld23}',
      `freifeld24`='{$this->freifeld24}',
      `freifeld25`='{$this->freifeld25}',
      `freifeld26`='{$this->freifeld26}',
      `freifeld27`='{$this->freifeld27}',
      `freifeld28`='{$this->freifeld28}',
      `freifeld29`='{$this->freifeld29}',
      `freifeld30`='{$this->freifeld30}',
      `freifeld31`='{$this->freifeld31}',
      `freifeld32`='{$this->freifeld32}',
      `freifeld33`='{$this->freifeld33}',
      `freifeld34`='{$this->freifeld34}',
      `freifeld35`='{$this->freifeld35}',
      `freifeld36`='{$this->freifeld36}',
      `freifeld37`='{$this->freifeld37}',
      `freifeld38`='{$this->freifeld38}',
      `freifeld39`='{$this->freifeld39}',
      `freifeld40`='{$this->freifeld40}',
      `lieferdatumkw`='{$this->lieferdatumkw}',
      `auftrag_position_id`='{$this->auftrag_position_id}',
      `lieferschein_position_id`='{$this->lieferschein_position_id}',
      `kostenlos`='{$this->kostenlos}',
      `lagertext`='{$this->lagertext}',
      `teilprojekt`='{$this->teilprojekt}',
      `explodiert_parent`='{$this->explodiert_parent}',
      `ausblenden_im_pdf`='{$this->ausblenden_im_pdf}',
      `grund`='{$this->grund}',
      `grundbeschreibung`='{$this->grundbeschreibung}',
      `aktion`='{$this->aktion}',
      `aktionbeschreibung`='{$this->aktionbeschreibung}',
      `menge_eingang`='{$this->menge_eingang}',
      `menge_gutschrift`='{$this->menge_gutschrift}'
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

    $sql = "DELETE FROM `retoure_position` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->retoure='';
    $this->artikel='';
    $this->projekt='';
    $this->bezeichnung='';
    $this->beschreibung='';
    $this->internerkommentar='';
    $this->nummer='';
    $this->seriennummer='';
    $this->menge='';
    $this->lieferdatum='';
    $this->vpe='';
    $this->sort='';
    $this->status='';
    $this->bemerkung='';
    $this->geliefert='';
    $this->abgerechnet='';
    $this->logdatei='';
    $this->explodiert_parent_artikel='';
    $this->einheit='';
    $this->zolltarifnummer='';
    $this->herkunftsland='';
    $this->artikelnummerkunde='';
    $this->freifeld1='';
    $this->freifeld2='';
    $this->freifeld3='';
    $this->freifeld4='';
    $this->freifeld5='';
    $this->freifeld6='';
    $this->freifeld7='';
    $this->freifeld8='';
    $this->freifeld9='';
    $this->freifeld10='';
    $this->freifeld11='';
    $this->freifeld12='';
    $this->freifeld13='';
    $this->freifeld14='';
    $this->freifeld15='';
    $this->freifeld16='';
    $this->freifeld17='';
    $this->freifeld18='';
    $this->freifeld19='';
    $this->freifeld20='';
    $this->freifeld21='';
    $this->freifeld22='';
    $this->freifeld23='';
    $this->freifeld24='';
    $this->freifeld25='';
    $this->freifeld26='';
    $this->freifeld27='';
    $this->freifeld28='';
    $this->freifeld29='';
    $this->freifeld30='';
    $this->freifeld31='';
    $this->freifeld32='';
    $this->freifeld33='';
    $this->freifeld34='';
    $this->freifeld35='';
    $this->freifeld36='';
    $this->freifeld37='';
    $this->freifeld38='';
    $this->freifeld39='';
    $this->freifeld40='';
    $this->lieferdatumkw='';
    $this->auftrag_position_id='';
    $this->lieferschein_position_id='';
    $this->kostenlos='';
    $this->lagertext='';
    $this->teilprojekt='';
    $this->explodiert_parent='';
    $this->ausblenden_im_pdf='';
    $this->grund='';
    $this->grundbeschreibung='';
    $this->aktion='';
    $this->aktionbeschreibung='';
    $this->menge_eingang='';
    $this->menge_gutschrift='';
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
  public function SetRetoure($value) { $this->retoure=$value; }
  public function GetRetoure() { return $this->retoure; }
  public function SetArtikel($value) { $this->artikel=$value; }
  public function GetArtikel() { return $this->artikel; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetBezeichnung($value) { $this->bezeichnung=$value; }
  public function GetBezeichnung() { return $this->bezeichnung; }
  public function SetBeschreibung($value) { $this->beschreibung=$value; }
  public function GetBeschreibung() { return $this->beschreibung; }
  public function SetInternerkommentar($value) { $this->internerkommentar=$value; }
  public function GetInternerkommentar() { return $this->internerkommentar; }
  public function SetNummer($value) { $this->nummer=$value; }
  public function GetNummer() { return $this->nummer; }
  public function SetSeriennummer($value) { $this->seriennummer=$value; }
  public function GetSeriennummer() { return $this->seriennummer; }
  public function SetMenge($value) { $this->menge=$value; }
  public function GetMenge() { return $this->menge; }
  public function SetLieferdatum($value) { $this->lieferdatum=$value; }
  public function GetLieferdatum() { return $this->lieferdatum; }
  public function SetVpe($value) { $this->vpe=$value; }
  public function GetVpe() { return $this->vpe; }
  public function SetSort($value) { $this->sort=$value; }
  public function GetSort() { return $this->sort; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetBemerkung($value) { $this->bemerkung=$value; }
  public function GetBemerkung() { return $this->bemerkung; }
  public function SetGeliefert($value) { $this->geliefert=$value; }
  public function GetGeliefert() { return $this->geliefert; }
  public function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  public function GetAbgerechnet() { return $this->abgerechnet; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetExplodiert_Parent_Artikel($value) { $this->explodiert_parent_artikel=$value; }
  public function GetExplodiert_Parent_Artikel() { return $this->explodiert_parent_artikel; }
  public function SetEinheit($value) { $this->einheit=$value; }
  public function GetEinheit() { return $this->einheit; }
  public function SetZolltarifnummer($value) { $this->zolltarifnummer=$value; }
  public function GetZolltarifnummer() { return $this->zolltarifnummer; }
  public function SetHerkunftsland($value) { $this->herkunftsland=$value; }
  public function GetHerkunftsland() { return $this->herkunftsland; }
  public function SetArtikelnummerkunde($value) { $this->artikelnummerkunde=$value; }
  public function GetArtikelnummerkunde() { return $this->artikelnummerkunde; }
  public function SetFreifeld1($value) { $this->freifeld1=$value; }
  public function GetFreifeld1() { return $this->freifeld1; }
  public function SetFreifeld2($value) { $this->freifeld2=$value; }
  public function GetFreifeld2() { return $this->freifeld2; }
  public function SetFreifeld3($value) { $this->freifeld3=$value; }
  public function GetFreifeld3() { return $this->freifeld3; }
  public function SetFreifeld4($value) { $this->freifeld4=$value; }
  public function GetFreifeld4() { return $this->freifeld4; }
  public function SetFreifeld5($value) { $this->freifeld5=$value; }
  public function GetFreifeld5() { return $this->freifeld5; }
  public function SetFreifeld6($value) { $this->freifeld6=$value; }
  public function GetFreifeld6() { return $this->freifeld6; }
  public function SetFreifeld7($value) { $this->freifeld7=$value; }
  public function GetFreifeld7() { return $this->freifeld7; }
  public function SetFreifeld8($value) { $this->freifeld8=$value; }
  public function GetFreifeld8() { return $this->freifeld8; }
  public function SetFreifeld9($value) { $this->freifeld9=$value; }
  public function GetFreifeld9() { return $this->freifeld9; }
  public function SetFreifeld10($value) { $this->freifeld10=$value; }
  public function GetFreifeld10() { return $this->freifeld10; }
  public function SetFreifeld11($value) { $this->freifeld11=$value; }
  public function GetFreifeld11() { return $this->freifeld11; }
  public function SetFreifeld12($value) { $this->freifeld12=$value; }
  public function GetFreifeld12() { return $this->freifeld12; }
  public function SetFreifeld13($value) { $this->freifeld13=$value; }
  public function GetFreifeld13() { return $this->freifeld13; }
  public function SetFreifeld14($value) { $this->freifeld14=$value; }
  public function GetFreifeld14() { return $this->freifeld14; }
  public function SetFreifeld15($value) { $this->freifeld15=$value; }
  public function GetFreifeld15() { return $this->freifeld15; }
  public function SetFreifeld16($value) { $this->freifeld16=$value; }
  public function GetFreifeld16() { return $this->freifeld16; }
  public function SetFreifeld17($value) { $this->freifeld17=$value; }
  public function GetFreifeld17() { return $this->freifeld17; }
  public function SetFreifeld18($value) { $this->freifeld18=$value; }
  public function GetFreifeld18() { return $this->freifeld18; }
  public function SetFreifeld19($value) { $this->freifeld19=$value; }
  public function GetFreifeld19() { return $this->freifeld19; }
  public function SetFreifeld20($value) { $this->freifeld20=$value; }
  public function GetFreifeld20() { return $this->freifeld20; }
  public function SetFreifeld21($value) { $this->freifeld21=$value; }
  public function GetFreifeld21() { return $this->freifeld21; }
  public function SetFreifeld22($value) { $this->freifeld22=$value; }
  public function GetFreifeld22() { return $this->freifeld22; }
  public function SetFreifeld23($value) { $this->freifeld23=$value; }
  public function GetFreifeld23() { return $this->freifeld23; }
  public function SetFreifeld24($value) { $this->freifeld24=$value; }
  public function GetFreifeld24() { return $this->freifeld24; }
  public function SetFreifeld25($value) { $this->freifeld25=$value; }
  public function GetFreifeld25() { return $this->freifeld25; }
  public function SetFreifeld26($value) { $this->freifeld26=$value; }
  public function GetFreifeld26() { return $this->freifeld26; }
  public function SetFreifeld27($value) { $this->freifeld27=$value; }
  public function GetFreifeld27() { return $this->freifeld27; }
  public function SetFreifeld28($value) { $this->freifeld28=$value; }
  public function GetFreifeld28() { return $this->freifeld28; }
  public function SetFreifeld29($value) { $this->freifeld29=$value; }
  public function GetFreifeld29() { return $this->freifeld29; }
  public function SetFreifeld30($value) { $this->freifeld30=$value; }
  public function GetFreifeld30() { return $this->freifeld30; }
  public function SetFreifeld31($value) { $this->freifeld31=$value; }
  public function GetFreifeld31() { return $this->freifeld31; }
  public function SetFreifeld32($value) { $this->freifeld32=$value; }
  public function GetFreifeld32() { return $this->freifeld32; }
  public function SetFreifeld33($value) { $this->freifeld33=$value; }
  public function GetFreifeld33() { return $this->freifeld33; }
  public function SetFreifeld34($value) { $this->freifeld34=$value; }
  public function GetFreifeld34() { return $this->freifeld34; }
  public function SetFreifeld35($value) { $this->freifeld35=$value; }
  public function GetFreifeld35() { return $this->freifeld35; }
  public function SetFreifeld36($value) { $this->freifeld36=$value; }
  public function GetFreifeld36() { return $this->freifeld36; }
  public function SetFreifeld37($value) { $this->freifeld37=$value; }
  public function GetFreifeld37() { return $this->freifeld37; }
  public function SetFreifeld38($value) { $this->freifeld38=$value; }
  public function GetFreifeld38() { return $this->freifeld38; }
  public function SetFreifeld39($value) { $this->freifeld39=$value; }
  public function GetFreifeld39() { return $this->freifeld39; }
  public function SetFreifeld40($value) { $this->freifeld40=$value; }
  public function GetFreifeld40() { return $this->freifeld40; }
  public function SetLieferdatumkw($value) { $this->lieferdatumkw=$value; }
  public function GetLieferdatumkw() { return $this->lieferdatumkw; }
  public function SetAuftrag_Position_Id($value) { $this->auftrag_position_id=$value; }
  public function GetAuftrag_Position_Id() { return $this->auftrag_position_id; }
  public function SetLieferschein_Position_Id($value) { $this->lieferschein_position_id=$value; }
  public function GetLieferschein_Position_Id() { return $this->lieferschein_position_id; }
  public function SetKostenlos($value) { $this->kostenlos=$value; }
  public function GetKostenlos() { return $this->kostenlos; }
  public function SetLagertext($value) { $this->lagertext=$value; }
  public function GetLagertext() { return $this->lagertext; }
  public function SetTeilprojekt($value) { $this->teilprojekt=$value; }
  public function GetTeilprojekt() { return $this->teilprojekt; }
  public function SetExplodiert_Parent($value) { $this->explodiert_parent=$value; }
  public function GetExplodiert_Parent() { return $this->explodiert_parent; }
  public function SetAusblenden_Im_Pdf($value) { $this->ausblenden_im_pdf=$value; }
  public function GetAusblenden_Im_Pdf() { return $this->ausblenden_im_pdf; }
  public function SetGrund($value) { $this->grund=$value; }
  public function GetGrund() { return $this->grund; }
  public function SetGrundbeschreibung($value) { $this->grundbeschreibung=$value; }
  public function GetGrundbeschreibung() { return $this->grundbeschreibung; }
  public function SetAktion($value) { $this->aktion=$value; }
  public function GetAktion() { return $this->aktion; }
  public function SetAktionbeschreibung($value) { $this->aktionbeschreibung=$value; }
  public function GetAktionbeschreibung() { return $this->aktionbeschreibung; }
  public function SetMenge_Eingang($value) { $this->menge_eingang=$value; }
  public function GetMenge_Eingang() { return $this->menge_eingang; }
  public function SetMenge_Gutschrift($value) { $this->menge_gutschrift=$value; }
  public function GetMenge_Gutschrift() { return $this->menge_gutschrift; }

}
