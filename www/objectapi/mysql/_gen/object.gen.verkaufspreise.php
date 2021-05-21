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

class ObjGenVerkaufspreise
{

  private  $id;
  private  $artikel;
  private  $objekt;
  private  $projekt;
  private  $adresse;
  private  $preis;
  private  $waehrung;
  private  $ab_menge;
  private  $vpe;
  private  $vpe_menge;
  private  $angelegt_am;
  private  $gueltig_bis;
  private  $bemerkung;
  private  $bearbeiter;
  private  $logdatei;
  private  $firma;
  private  $geloescht;
  private  $kundenartikelnummer;
  private  $art;
  private  $gruppe;
  private  $apichange;
  private  $nichtberechnet;
  private  $gueltig_ab;
  private  $kurs;
  private  $kursdatum;
  private  $inbelegausblenden;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM verkaufspreise WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->artikel=$result['artikel'];
    $this->objekt=$result['objekt'];
    $this->projekt=$result['projekt'];
    $this->adresse=$result['adresse'];
    $this->preis=$result['preis'];
    $this->waehrung=$result['waehrung'];
    $this->ab_menge=$result['ab_menge'];
    $this->vpe=$result['vpe'];
    $this->vpe_menge=$result['vpe_menge'];
    $this->angelegt_am=$result['angelegt_am'];
    $this->gueltig_bis=$result['gueltig_bis'];
    $this->bemerkung=$result['bemerkung'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->logdatei=$result['logdatei'];
    $this->firma=$result['firma'];
    $this->geloescht=$result['geloescht'];
    $this->kundenartikelnummer=$result['kundenartikelnummer'];
    $this->art=$result['art'];
    $this->gruppe=$result['gruppe'];
    $this->apichange=$result['apichange'];
    $this->nichtberechnet=$result['nichtberechnet'];
    $this->gueltig_ab=$result['gueltig_ab'];
    $this->kurs=$result['kurs'];
    $this->kursdatum=$result['kursdatum'];
    $this->inbelegausblenden=$result['inbelegausblenden'];
  }

  public function Create()
  {
    $sql = "INSERT INTO verkaufspreise (id,artikel,objekt,projekt,adresse,preis,waehrung,ab_menge,vpe,vpe_menge,angelegt_am,gueltig_bis,bemerkung,bearbeiter,logdatei,firma,geloescht,kundenartikelnummer,art,gruppe,apichange,nichtberechnet,gueltig_ab,kurs,kursdatum,inbelegausblenden)
      VALUES('','{$this->artikel}','{$this->objekt}','{$this->projekt}','{$this->adresse}','{$this->preis}','{$this->waehrung}','{$this->ab_menge}','{$this->vpe}','{$this->vpe_menge}','{$this->angelegt_am}','{$this->gueltig_bis}','{$this->bemerkung}','{$this->bearbeiter}','{$this->logdatei}','{$this->firma}','{$this->geloescht}','{$this->kundenartikelnummer}','{$this->art}','{$this->gruppe}','{$this->apichange}','{$this->nichtberechnet}','{$this->gueltig_ab}','{$this->kurs}','{$this->kursdatum}','{$this->inbelegausblenden}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE verkaufspreise SET
      artikel='{$this->artikel}',
      objekt='{$this->objekt}',
      projekt='{$this->projekt}',
      adresse='{$this->adresse}',
      preis='{$this->preis}',
      waehrung='{$this->waehrung}',
      ab_menge='{$this->ab_menge}',
      vpe='{$this->vpe}',
      vpe_menge='{$this->vpe_menge}',
      angelegt_am='{$this->angelegt_am}',
      gueltig_bis='{$this->gueltig_bis}',
      bemerkung='{$this->bemerkung}',
      bearbeiter='{$this->bearbeiter}',
      logdatei='{$this->logdatei}',
      firma='{$this->firma}',
      geloescht='{$this->geloescht}',
      kundenartikelnummer='{$this->kundenartikelnummer}',
      art='{$this->art}',
      gruppe='{$this->gruppe}',
      apichange='{$this->apichange}',
      nichtberechnet='{$this->nichtberechnet}',
      gueltig_ab='{$this->gueltig_ab}',
      kurs='{$this->kurs}',
      kursdatum='{$this->kursdatum}',
      inbelegausblenden='{$this->inbelegausblenden}'
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

    $sql = "DELETE FROM verkaufspreise WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->artikel="";
    $this->objekt="";
    $this->projekt="";
    $this->adresse="";
    $this->preis="";
    $this->waehrung="";
    $this->ab_menge="";
    $this->vpe="";
    $this->vpe_menge="";
    $this->angelegt_am="";
    $this->gueltig_bis="";
    $this->bemerkung="";
    $this->bearbeiter="";
    $this->logdatei="";
    $this->firma="";
    $this->geloescht="";
    $this->kundenartikelnummer="";
    $this->art="";
    $this->gruppe="";
    $this->apichange="";
    $this->nichtberechnet="";
    $this->gueltig_ab="";
    $this->kurs="";
    $this->kursdatum="";
    $this->inbelegausblenden="";
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
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetObjekt($value) { $this->objekt=$value; }
  function GetObjekt() { return $this->objekt; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetPreis($value) { $this->preis=$value; }
  function GetPreis() { return $this->preis; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetAb_Menge($value) { $this->ab_menge=$value; }
  function GetAb_Menge() { return $this->ab_menge; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetVpe_Menge($value) { $this->vpe_menge=$value; }
  function GetVpe_Menge() { return $this->vpe_menge; }
  function SetAngelegt_Am($value) { $this->angelegt_am=$value; }
  function GetAngelegt_Am() { return $this->angelegt_am; }
  function SetGueltig_Bis($value) { $this->gueltig_bis=$value; }
  function GetGueltig_Bis() { return $this->gueltig_bis; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetKundenartikelnummer($value) { $this->kundenartikelnummer=$value; }
  function GetKundenartikelnummer() { return $this->kundenartikelnummer; }
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetGruppe($value) { $this->gruppe=$value; }
  function GetGruppe() { return $this->gruppe; }
  function SetApichange($value) { $this->apichange=$value; }
  function GetApichange() { return $this->apichange; }
  function SetNichtberechnet($value) { $this->nichtberechnet=$value; }
  function GetNichtberechnet() { return $this->nichtberechnet; }
  function SetGueltig_Ab($value) { $this->gueltig_ab=$value; }
  function GetGueltig_Ab() { return $this->gueltig_ab; }
  function SetKurs($value) { $this->kurs=$value; }
  function GetKurs() { return $this->kurs; }
  function SetKursdatum($value) { $this->kursdatum=$value; }
  function GetKursdatum() { return $this->kursdatum; }
  function SetInbelegausblenden($value) { $this->inbelegausblenden=$value; }
  function GetInbelegausblenden() { return $this->inbelegausblenden; }

}

?>