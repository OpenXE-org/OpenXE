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

class ObjGenAnfrage_Position
{

  private  $id;
  private  $anfrage;
  private  $artikel;
  private  $projekt;
  private  $nummer;
  private  $bezeichnung;
  private  $beschreibung;
  private  $internerkommentar;
  private  $menge;
  private  $sort;
  private  $bemerkung;
  private  $preis;
  private  $logdatei;
  private  $steuersatz;
  private  $steuertext;
  private  $grundrabatt;
  private  $rabattsync;
  private  $rabatt1;
  private  $rabatt2;
  private  $rabatt3;
  private  $rabatt4;
  private  $rabatt5;
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
  private  $geliefert;
  private  $vpe;
  private  $einheit;
  private  $lieferdatum;
  private  $lieferdatumkw;
  private  $erloese;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM anfrage_position WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->anfrage=$result['anfrage'];
    $this->artikel=$result['artikel'];
    $this->projekt=$result['projekt'];
    $this->nummer=$result['nummer'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->beschreibung=$result['beschreibung'];
    $this->internerkommentar=$result['internerkommentar'];
    $this->menge=$result['menge'];
    $this->sort=$result['sort'];
    $this->bemerkung=$result['bemerkung'];
    $this->preis=$result['preis'];
    $this->logdatei=$result['logdatei'];
    $this->steuersatz=$result['steuersatz'];
    $this->steuertext=$result['steuertext'];
    $this->grundrabatt=$result['grundrabatt'];
    $this->rabattsync=$result['rabattsync'];
    $this->rabatt1=$result['rabatt1'];
    $this->rabatt2=$result['rabatt2'];
    $this->rabatt3=$result['rabatt3'];
    $this->rabatt4=$result['rabatt4'];
    $this->rabatt5=$result['rabatt5'];
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
    $this->geliefert=$result['geliefert'];
    $this->vpe=$result['vpe'];
    $this->einheit=$result['einheit'];
    $this->lieferdatum=$result['lieferdatum'];
    $this->lieferdatumkw=$result['lieferdatumkw'];
    $this->erloese=$result['erloese'];
  }

  public function Create()
  {
    $sql = "INSERT INTO anfrage_position (id,anfrage,artikel,projekt,nummer,bezeichnung,beschreibung,internerkommentar,menge,sort,bemerkung,preis,logdatei,steuersatz,steuertext,grundrabatt,rabattsync,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,freifeld1,freifeld2,freifeld3,freifeld4,freifeld5,freifeld6,freifeld7,freifeld8,freifeld9,freifeld10,geliefert,vpe,einheit,lieferdatum,lieferdatumkw,erloese)
      VALUES('','{$this->anfrage}','{$this->artikel}','{$this->projekt}','{$this->nummer}','{$this->bezeichnung}','{$this->beschreibung}','{$this->internerkommentar}','{$this->menge}','{$this->sort}','{$this->bemerkung}','{$this->preis}','{$this->logdatei}','{$this->steuersatz}','{$this->steuertext}','{$this->grundrabatt}','{$this->rabattsync}','{$this->rabatt1}','{$this->rabatt2}','{$this->rabatt3}','{$this->rabatt4}','{$this->rabatt5}','{$this->freifeld1}','{$this->freifeld2}','{$this->freifeld3}','{$this->freifeld4}','{$this->freifeld5}','{$this->freifeld6}','{$this->freifeld7}','{$this->freifeld8}','{$this->freifeld9}','{$this->freifeld10}','{$this->geliefert}','{$this->vpe}','{$this->einheit}','{$this->lieferdatum}','{$this->lieferdatumkw}','{$this->erloese}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE anfrage_position SET
      anfrage='{$this->anfrage}',
      artikel='{$this->artikel}',
      projekt='{$this->projekt}',
      nummer='{$this->nummer}',
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      internerkommentar='{$this->internerkommentar}',
      menge='{$this->menge}',
      sort='{$this->sort}',
      bemerkung='{$this->bemerkung}',
      preis='{$this->preis}',
      logdatei='{$this->logdatei}',
      steuersatz='{$this->steuersatz}',
      steuertext='{$this->steuertext}',
      grundrabatt='{$this->grundrabatt}',
      rabattsync='{$this->rabattsync}',
      rabatt1='{$this->rabatt1}',
      rabatt2='{$this->rabatt2}',
      rabatt3='{$this->rabatt3}',
      rabatt4='{$this->rabatt4}',
      rabatt5='{$this->rabatt5}',
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
      geliefert='{$this->geliefert}',
      vpe='{$this->vpe}',
      einheit='{$this->einheit}',
      lieferdatum='{$this->lieferdatum}',
      lieferdatumkw='{$this->lieferdatumkw}',
      erloese='{$this->erloese}'
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

    $sql = "DELETE FROM anfrage_position WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->anfrage="";
    $this->artikel="";
    $this->projekt="";
    $this->nummer="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->internerkommentar="";
    $this->menge="";
    $this->sort="";
    $this->bemerkung="";
    $this->preis="";
    $this->logdatei="";
    $this->steuersatz="";
    $this->steuertext="";
    $this->grundrabatt="";
    $this->rabattsync="";
    $this->rabatt1="";
    $this->rabatt2="";
    $this->rabatt3="";
    $this->rabatt4="";
    $this->rabatt5="";
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
    $this->geliefert="";
    $this->vpe="";
    $this->einheit="";
    $this->lieferdatum="";
    $this->lieferdatumkw="";
    $this->erloese="";
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
  function SetAnfrage($value) { $this->anfrage=$value; }
  function GetAnfrage() { return $this->anfrage; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetInternerkommentar($value) { $this->internerkommentar=$value; }
  function GetInternerkommentar() { return $this->internerkommentar; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetPreis($value) { $this->preis=$value; }
  function GetPreis() { return $this->preis; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetSteuersatz($value) { $this->steuersatz=$value; }
  function GetSteuersatz() { return $this->steuersatz; }
  function SetSteuertext($value) { $this->steuertext=$value; }
  function GetSteuertext() { return $this->steuertext; }
  function SetGrundrabatt($value) { $this->grundrabatt=$value; }
  function GetGrundrabatt() { return $this->grundrabatt; }
  function SetRabattsync($value) { $this->rabattsync=$value; }
  function GetRabattsync() { return $this->rabattsync; }
  function SetRabatt1($value) { $this->rabatt1=$value; }
  function GetRabatt1() { return $this->rabatt1; }
  function SetRabatt2($value) { $this->rabatt2=$value; }
  function GetRabatt2() { return $this->rabatt2; }
  function SetRabatt3($value) { $this->rabatt3=$value; }
  function GetRabatt3() { return $this->rabatt3; }
  function SetRabatt4($value) { $this->rabatt4=$value; }
  function GetRabatt4() { return $this->rabatt4; }
  function SetRabatt5($value) { $this->rabatt5=$value; }
  function GetRabatt5() { return $this->rabatt5; }
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
  function SetGeliefert($value) { $this->geliefert=$value; }
  function GetGeliefert() { return $this->geliefert; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetEinheit($value) { $this->einheit=$value; }
  function GetEinheit() { return $this->einheit; }
  function SetLieferdatum($value) { $this->lieferdatum=$value; }
  function GetLieferdatum() { return $this->lieferdatum; }
  function SetLieferdatumkw($value) { $this->lieferdatumkw=$value; }
  function GetLieferdatumkw() { return $this->lieferdatumkw; }
  function SetErloese($value) { $this->erloese=$value; }
  function GetErloese() { return $this->erloese; }

}

?>