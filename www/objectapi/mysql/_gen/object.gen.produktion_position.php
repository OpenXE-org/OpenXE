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

class ObjGenProduktion_Position
{

  private  $id;
  private  $produktion;
  private  $artikel;
  private  $projekt;
  private  $bezeichnung;
  private  $beschreibung;
  private  $internerkommentar;
  private  $nummer;
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
  private  $geliefert_menge;
  private  $explodiert;
  private  $explodiert_parent;
  private  $logdatei;
  private  $nachbestelltexternereinkauf;
  private  $beistellung;
  private  $externeproduktion;
  private  $steuersatz;
  private  $steuertext;
  private  $erloese;
  private  $erloesefestschreiben;
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
  private  $stuecklistestufe;
  private  $teilprojekt;
  private  $einheit;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `produktion_position` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->produktion=$result['produktion'];
    $this->artikel=$result['artikel'];
    $this->projekt=$result['projekt'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->beschreibung=$result['beschreibung'];
    $this->internerkommentar=$result['internerkommentar'];
    $this->nummer=$result['nummer'];
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
    $this->geliefert_menge=$result['geliefert_menge'];
    $this->explodiert=$result['explodiert'];
    $this->explodiert_parent=$result['explodiert_parent'];
    $this->logdatei=$result['logdatei'];
    $this->nachbestelltexternereinkauf=$result['nachbestelltexternereinkauf'];
    $this->beistellung=$result['beistellung'];
    $this->externeproduktion=$result['externeproduktion'];
    $this->steuersatz=$result['steuersatz'];
    $this->steuertext=$result['steuertext'];
    $this->erloese=$result['erloese'];
    $this->erloesefestschreiben=$result['erloesefestschreiben'];
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
    $this->stuecklistestufe=$result['stuecklistestufe'];
    $this->teilprojekt=$result['teilprojekt'];
    $this->einheit=$result['einheit'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `produktion_position` (`id`,`produktion`,`artikel`,`projekt`,`bezeichnung`,`beschreibung`,`internerkommentar`,`nummer`,`menge`,`preis`,`waehrung`,`lieferdatum`,`vpe`,`sort`,`status`,`umsatzsteuer`,`bemerkung`,`geliefert`,`geliefert_menge`,`explodiert`,`explodiert_parent`,`logdatei`,`nachbestelltexternereinkauf`,`beistellung`,`externeproduktion`,`steuersatz`,`steuertext`,`erloese`,`erloesefestschreiben`,`freifeld1`,`freifeld2`,`freifeld3`,`freifeld4`,`freifeld5`,`freifeld6`,`freifeld7`,`freifeld8`,`freifeld9`,`freifeld10`,`freifeld11`,`freifeld12`,`freifeld13`,`freifeld14`,`freifeld15`,`freifeld16`,`freifeld17`,`freifeld18`,`freifeld19`,`freifeld20`,`freifeld21`,`freifeld22`,`freifeld23`,`freifeld24`,`freifeld25`,`freifeld26`,`freifeld27`,`freifeld28`,`freifeld29`,`freifeld30`,`freifeld31`,`freifeld32`,`freifeld33`,`freifeld34`,`freifeld35`,`freifeld36`,`freifeld37`,`freifeld38`,`freifeld39`,`freifeld40`,`stuecklistestufe`,`teilprojekt`,`einheit`)
      VALUES(NULL,'{$this->produktion}','{$this->artikel}','{$this->projekt}','{$this->bezeichnung}','{$this->beschreibung}','{$this->internerkommentar}','{$this->nummer}','{$this->menge}','{$this->preis}','{$this->waehrung}','{$this->lieferdatum}','{$this->vpe}','{$this->sort}','{$this->status}','{$this->umsatzsteuer}','{$this->bemerkung}','{$this->geliefert}','{$this->geliefert_menge}','{$this->explodiert}','{$this->explodiert_parent}','{$this->logdatei}','{$this->nachbestelltexternereinkauf}','{$this->beistellung}','{$this->externeproduktion}','{$this->steuersatz}','{$this->steuertext}','{$this->erloese}','{$this->erloesefestschreiben}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->freifeld4}','{$this->freifeld5}','{$this->freifeld6}','{$this->freifeld7}','{$this->freifeld8}','{$this->freifeld9}','{$this->freifeld10}','{$this->freifeld11}','{$this->freifeld12}','{$this->freifeld13}','{$this->freifeld14}','{$this->freifeld15}','{$this->freifeld16}','{$this->freifeld17}','{$this->freifeld18}','{$this->freifeld19}','{$this->freifeld20}','{$this->freifeld21}','{$this->freifeld22}','{$this->freifeld23}','{$this->freifeld24}','{$this->freifeld25}','{$this->freifeld26}','{$this->freifeld27}','{$this->freifeld28}','{$this->freifeld29}','{$this->freifeld30}','{$this->freifeld31}','{$this->freifeld32}','{$this->freifeld33}','{$this->freifeld34}','{$this->freifeld35}','{$this->freifeld36}','{$this->freifeld37}','{$this->freifeld38}','{$this->freifeld39}','{$this->freifeld40}','{$this->stuecklistestufe}','{$this->teilprojekt}','{$this->einheit}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `produktion_position` SET
      `produktion`='{$this->produktion}',
      `artikel`='{$this->artikel}',
      `projekt`='{$this->projekt}',
      `bezeichnung`='{$this->bezeichnung}',
      `beschreibung`='{$this->beschreibung}',
      `internerkommentar`='{$this->internerkommentar}',
      `nummer`='{$this->nummer}',
      `menge`='{$this->menge}',
      `preis`='{$this->preis}',
      `waehrung`='{$this->waehrung}',
      `lieferdatum`='{$this->lieferdatum}',
      `vpe`='{$this->vpe}',
      `sort`='{$this->sort}',
      `status`='{$this->status}',
      `umsatzsteuer`='{$this->umsatzsteuer}',
      `bemerkung`='{$this->bemerkung}',
      `geliefert`='{$this->geliefert}',
      `geliefert_menge`='{$this->geliefert_menge}',
      `explodiert`='{$this->explodiert}',
      `explodiert_parent`='{$this->explodiert_parent}',
      `logdatei`='{$this->logdatei}',
      `nachbestelltexternereinkauf`='{$this->nachbestelltexternereinkauf}',
      `beistellung`='{$this->beistellung}',
      `externeproduktion`='{$this->externeproduktion}',
      `steuersatz`='{$this->steuersatz}',
      `steuertext`='{$this->steuertext}',
      `erloese`='{$this->erloese}',
      `erloesefestschreiben`='{$this->erloesefestschreiben}',
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
      `stuecklistestufe`='{$this->stuecklistestufe}',
      `teilprojekt`='{$this->teilprojekt}',
      `einheit`='{$this->einheit}'
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

    $sql = "DELETE FROM `produktion_position` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->produktion='';
    $this->artikel='';
    $this->projekt='';
    $this->bezeichnung='';
    $this->beschreibung='';
    $this->internerkommentar='';
    $this->nummer='';
    $this->menge='';
    $this->preis='';
    $this->waehrung='';
    $this->lieferdatum='';
    $this->vpe='';
    $this->sort='';
    $this->status='';
    $this->umsatzsteuer='';
    $this->bemerkung='';
    $this->geliefert='';
    $this->geliefert_menge='';
    $this->explodiert='';
    $this->explodiert_parent='';
    $this->logdatei='';
    $this->nachbestelltexternereinkauf='';
    $this->beistellung='';
    $this->externeproduktion='';
    $this->steuersatz='';
    $this->steuertext='';
    $this->erloese='';
    $this->erloesefestschreiben='';
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
    $this->stuecklistestufe='';
    $this->teilprojekt='';
    $this->einheit='';
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
  public function SetProduktion($value) { $this->produktion=$value; }
  public function GetProduktion() { return $this->produktion; }
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
  public function SetMenge($value) { $this->menge=$value; }
  public function GetMenge() { return $this->menge; }
  public function SetPreis($value) { $this->preis=$value; }
  public function GetPreis() { return $this->preis; }
  public function SetWaehrung($value) { $this->waehrung=$value; }
  public function GetWaehrung() { return $this->waehrung; }
  public function SetLieferdatum($value) { $this->lieferdatum=$value; }
  public function GetLieferdatum() { return $this->lieferdatum; }
  public function SetVpe($value) { $this->vpe=$value; }
  public function GetVpe() { return $this->vpe; }
  public function SetSort($value) { $this->sort=$value; }
  public function GetSort() { return $this->sort; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetUmsatzsteuer($value) { $this->umsatzsteuer=$value; }
  public function GetUmsatzsteuer() { return $this->umsatzsteuer; }
  public function SetBemerkung($value) { $this->bemerkung=$value; }
  public function GetBemerkung() { return $this->bemerkung; }
  public function SetGeliefert($value) { $this->geliefert=$value; }
  public function GetGeliefert() { return $this->geliefert; }
  public function SetGeliefert_Menge($value) { $this->geliefert_menge=$value; }
  public function GetGeliefert_Menge() { return $this->geliefert_menge; }
  public function SetExplodiert($value) { $this->explodiert=$value; }
  public function GetExplodiert() { return $this->explodiert; }
  public function SetExplodiert_Parent($value) { $this->explodiert_parent=$value; }
  public function GetExplodiert_Parent() { return $this->explodiert_parent; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetNachbestelltexternereinkauf($value) { $this->nachbestelltexternereinkauf=$value; }
  public function GetNachbestelltexternereinkauf() { return $this->nachbestelltexternereinkauf; }
  public function SetBeistellung($value) { $this->beistellung=$value; }
  public function GetBeistellung() { return $this->beistellung; }
  public function SetExterneproduktion($value) { $this->externeproduktion=$value; }
  public function GetExterneproduktion() { return $this->externeproduktion; }
  public function SetSteuersatz($value) { $this->steuersatz=$value; }
  public function GetSteuersatz() { return $this->steuersatz; }
  public function SetSteuertext($value) { $this->steuertext=$value; }
  public function GetSteuertext() { return $this->steuertext; }
  public function SetErloese($value) { $this->erloese=$value; }
  public function GetErloese() { return $this->erloese; }
  public function SetErloesefestschreiben($value) { $this->erloesefestschreiben=$value; }
  public function GetErloesefestschreiben() { return $this->erloesefestschreiben; }
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
  public function SetStuecklistestufe($value) { $this->stuecklistestufe=$value; }
  public function GetStuecklistestufe() { return $this->stuecklistestufe; }
  public function SetTeilprojekt($value) { $this->teilprojekt=$value; }
  public function GetTeilprojekt() { return $this->teilprojekt; }
  public function SetEinheit($value) { $this->einheit=$value; }
  public function GetEinheit() { return $this->einheit; }

}
