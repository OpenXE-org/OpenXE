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

class ObjGenRetoure
{

  private  $id;
  private  $datum;
  private  $projekt;
  private  $belegnr;
  private  $bearbeiter;
  private  $lieferschein;
  private  $lieferscheinid;
  private  $auftrag;
  private  $auftragid;
  private  $freitext;
  private  $status;
  private  $adresse;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $adresszusatz;
  private  $ansprechpartner;
  private  $plz;
  private  $ort;
  private  $land;
  private  $abweichendelieferadresse;
  private  $liefername;
  private  $lieferabteilung;
  private  $lieferunterabteilung;
  private  $lieferstrasse;
  private  $lieferadresszusatz;
  private  $lieferansprechpartner;
  private  $lieferplz;
  private  $lieferort;
  private  $lieferland;
  private  $ustid;
  private  $email;
  private  $telefon;
  private  $telefax;
  private  $betreff;
  private  $kundennummer;
  private  $versandart;
  private  $versand;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $inbearbeitung_user;
  private  $logdatei;
  private  $vertriebid;
  private  $vertrieb;
  private  $ust_befreit;
  private  $ihrebestellnummer;
  private  $anschreiben;
  private  $usereditid;
  private  $useredittimestamp;
  private  $lieferantenretoure;
  private  $lieferantenretoureinfo;
  private  $lieferant;
  private  $schreibschutz;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $typ;
  private  $internebemerkung;
  private  $ohne_briefpapier;
  private  $lieferid;
  private  $ansprechpartnerid;
  private  $projektfiliale;
  private  $projektfiliale_eingelagert;
  private  $zuarchivieren;
  private  $internebezeichnung;
  private  $angelegtam;
  private  $kommissionierung;
  private  $sprache;
  private  $bundesland;
  private  $gln;
  private  $rechnungid;
  private  $bearbeiterid;
  private  $keinerechnung;
  private  $ohne_artikeltext;
  private  $abweichendebezeichnung;
  private  $bodyzusatz;
  private  $lieferbedingung;
  private  $titel;
  private  $standardlager;
  private  $kommissionskonsignationslager;
  private  $bundesstaat;
  private  $teillieferungvon;
  private  $teillieferungnummer;
  private  $gutschrift_id;
  private  $fortschritt;
  private  $storage_ok;
  private  $replacementorder_id;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `retoure` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->datum=$result['datum'];
    $this->projekt=$result['projekt'];
    $this->belegnr=$result['belegnr'];
    $this->bearbeiter=$result['bearbeiter'];
    $this->lieferschein=$result['lieferschein'];
    $this->lieferscheinid=$result['lieferscheinid'];
    $this->auftrag=$result['auftrag'];
    $this->auftragid=$result['auftragid'];
    $this->freitext=$result['freitext'];
    $this->status=$result['status'];
    $this->adresse=$result['adresse'];
    $this->name=$result['name'];
    $this->abteilung=$result['abteilung'];
    $this->unterabteilung=$result['unterabteilung'];
    $this->strasse=$result['strasse'];
    $this->adresszusatz=$result['adresszusatz'];
    $this->ansprechpartner=$result['ansprechpartner'];
    $this->plz=$result['plz'];
    $this->ort=$result['ort'];
    $this->land=$result['land'];
    $this->abweichendelieferadresse=$result['abweichendelieferadresse'];
    $this->liefername=$result['liefername'];
    $this->lieferabteilung=$result['lieferabteilung'];
    $this->lieferunterabteilung=$result['lieferunterabteilung'];
    $this->lieferstrasse=$result['lieferstrasse'];
    $this->lieferadresszusatz=$result['lieferadresszusatz'];
    $this->lieferansprechpartner=$result['lieferansprechpartner'];
    $this->lieferplz=$result['lieferplz'];
    $this->lieferort=$result['lieferort'];
    $this->lieferland=$result['lieferland'];
    $this->ustid=$result['ustid'];
    $this->email=$result['email'];
    $this->telefon=$result['telefon'];
    $this->telefax=$result['telefax'];
    $this->betreff=$result['betreff'];
    $this->kundennummer=$result['kundennummer'];
    $this->versandart=$result['versandart'];
    $this->versand=$result['versand'];
    $this->firma=$result['firma'];
    $this->versendet=$result['versendet'];
    $this->versendet_am=$result['versendet_am'];
    $this->versendet_per=$result['versendet_per'];
    $this->versendet_durch=$result['versendet_durch'];
    $this->inbearbeitung_user=$result['inbearbeitung_user'];
    $this->logdatei=$result['logdatei'];
    $this->vertriebid=$result['vertriebid'];
    $this->vertrieb=$result['vertrieb'];
    $this->ust_befreit=$result['ust_befreit'];
    $this->ihrebestellnummer=$result['ihrebestellnummer'];
    $this->anschreiben=$result['anschreiben'];
    $this->usereditid=$result['usereditid'];
    $this->useredittimestamp=$result['useredittimestamp'];
    $this->lieferantenretoure=$result['lieferantenretoure'];
    $this->lieferantenretoureinfo=$result['lieferantenretoureinfo'];
    $this->lieferant=$result['lieferant'];
    $this->schreibschutz=$result['schreibschutz'];
    $this->pdfarchiviert=$result['pdfarchiviert'];
    $this->pdfarchiviertversion=$result['pdfarchiviertversion'];
    $this->typ=$result['typ'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->ohne_briefpapier=$result['ohne_briefpapier'];
    $this->lieferid=$result['lieferid'];
    $this->ansprechpartnerid=$result['ansprechpartnerid'];
    $this->projektfiliale=$result['projektfiliale'];
    $this->projektfiliale_eingelagert=$result['projektfiliale_eingelagert'];
    $this->zuarchivieren=$result['zuarchivieren'];
    $this->internebezeichnung=$result['internebezeichnung'];
    $this->angelegtam=$result['angelegtam'];
    $this->kommissionierung=$result['kommissionierung'];
    $this->sprache=$result['sprache'];
    $this->bundesland=$result['bundesland'];
    $this->gln=$result['gln'];
    $this->rechnungid=$result['rechnungid'];
    $this->bearbeiterid=$result['bearbeiterid'];
    $this->keinerechnung=$result['keinerechnung'];
    $this->ohne_artikeltext=$result['ohne_artikeltext'];
    $this->abweichendebezeichnung=$result['abweichendebezeichnung'];
    $this->bodyzusatz=$result['bodyzusatz'];
    $this->lieferbedingung=$result['lieferbedingung'];
    $this->titel=$result['titel'];
    $this->standardlager=$result['standardlager'];
    $this->kommissionskonsignationslager=$result['kommissionskonsignationslager'];
    $this->bundesstaat=$result['bundesstaat'];
    $this->teillieferungvon=$result['teillieferungvon'];
    $this->teillieferungnummer=$result['teillieferungnummer'];
    $this->gutschrift_id=$result['gutschrift_id'];
    $this->fortschritt=$result['fortschritt'];
    $this->storage_ok=$result['storage_ok'];
    $this->replacementorder_id=$result['replacementorder_id'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `retoure` (`id`,`datum`,`projekt`,`belegnr`,`bearbeiter`,`lieferschein`,`lieferscheinid`,`auftrag`,`auftragid`,`freitext`,`status`,`adresse`,`name`,`abteilung`,`unterabteilung`,`strasse`,`adresszusatz`,`ansprechpartner`,`plz`,`ort`,`land`,`abweichendelieferadresse`,`liefername`,`lieferabteilung`,`lieferunterabteilung`,`lieferstrasse`,`lieferadresszusatz`,`lieferansprechpartner`,`lieferplz`,`lieferort`,`lieferland`,`ustid`,`email`,`telefon`,`telefax`,`betreff`,`kundennummer`,`versandart`,`versand`,`firma`,`versendet`,`versendet_am`,`versendet_per`,`versendet_durch`,`inbearbeitung_user`,`logdatei`,`vertriebid`,`vertrieb`,`ust_befreit`,`ihrebestellnummer`,`anschreiben`,`usereditid`,`useredittimestamp`,`lieferantenretoure`,`lieferantenretoureinfo`,`lieferant`,`schreibschutz`,`pdfarchiviert`,`pdfarchiviertversion`,`typ`,`internebemerkung`,`ohne_briefpapier`,`lieferid`,`ansprechpartnerid`,`projektfiliale`,`projektfiliale_eingelagert`,`zuarchivieren`,`internebezeichnung`,`angelegtam`,`kommissionierung`,`sprache`,`bundesland`,`gln`,`rechnungid`,`bearbeiterid`,`keinerechnung`,`ohne_artikeltext`,`abweichendebezeichnung`,`bodyzusatz`,`lieferbedingung`,`titel`,`standardlager`,`kommissionskonsignationslager`,`bundesstaat`,`teillieferungvon`,`teillieferungnummer`,`gutschrift_id`,`fortschritt`,`storage_ok`,`replacementorder_id`)
      VALUES(NULL,'{$this->datum}','{$this->projekt}','{$this->belegnr}','{$this->bearbeiter}','{$this->lieferschein}','{$this->lieferscheinid}','{$this->auftrag}','{$this->auftragid}','{$this->freitext}','{$this->status}','{$this->adresse}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->strasse}','{$this->adresszusatz}','{$this->ansprechpartner}','{$this->plz}','{$this->ort}','{$this->land}','{$this->abweichendelieferadresse}','{$this->liefername}','{$this->lieferabteilung}','{$this->lieferunterabteilung}','{$this->lieferstrasse}','{$this->lieferadresszusatz}','{$this->lieferansprechpartner}','{$this->lieferplz}','{$this->lieferort}','{$this->lieferland}','{$this->ustid}','{$this->email}','{$this->telefon}','{$this->telefax}','{$this->betreff}','{$this->kundennummer}','{$this->versandart}','{$this->versand}','{$this->firma}','{$this->versendet}','{$this->versendet_am}','{$this->versendet_per}','{$this->versendet_durch}','{$this->inbearbeitung_user}','{$this->logdatei}','{$this->vertriebid}','{$this->vertrieb}','{$this->ust_befreit}','{$this->ihrebestellnummer}','{$this->anschreiben}','{$this->usereditid}','{$this->useredittimestamp}','{$this->lieferantenretoure}','{$this->lieferantenretoureinfo}','{$this->lieferant}','{$this->schreibschutz}','{$this->pdfarchiviert}','{$this->pdfarchiviertversion}','{$this->typ}','{$this->internebemerkung}','{$this->ohne_briefpapier}','{$this->lieferid}','{$this->ansprechpartnerid}','{$this->projektfiliale}','{$this->projektfiliale_eingelagert}','{$this->zuarchivieren}','{$this->internebezeichnung}','{$this->angelegtam}','{$this->kommissionierung}','{$this->sprache}','{$this->bundesland}','{$this->gln}','{$this->rechnungid}','{$this->bearbeiterid}','{$this->keinerechnung}','{$this->ohne_artikeltext}','{$this->abweichendebezeichnung}','{$this->bodyzusatz}','{$this->lieferbedingung}','{$this->titel}','{$this->standardlager}','{$this->kommissionskonsignationslager}','{$this->bundesstaat}','{$this->teillieferungvon}','{$this->teillieferungnummer}','{$this->gutschrift_id}','{$this->fortschritt}','{$this->storage_ok}','{$this->replacementorder_id}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `retoure` SET
      `datum`='{$this->datum}',
      `projekt`='{$this->projekt}',
      `belegnr`='{$this->belegnr}',
      `bearbeiter`='{$this->bearbeiter}',
      `lieferschein`='{$this->lieferschein}',
      `lieferscheinid`='{$this->lieferscheinid}',
      `auftrag`='{$this->auftrag}',
      `auftragid`='{$this->auftragid}',
      `freitext`='{$this->freitext}',
      `status`='{$this->status}',
      `adresse`='{$this->adresse}',
      `name`='{$this->name}',
      `abteilung`='{$this->abteilung}',
      `unterabteilung`='{$this->unterabteilung}',
      `strasse`='{$this->strasse}',
      `adresszusatz`='{$this->adresszusatz}',
      `ansprechpartner`='{$this->ansprechpartner}',
      `plz`='{$this->plz}',
      `ort`='{$this->ort}',
      `land`='{$this->land}',
      `abweichendelieferadresse`='{$this->abweichendelieferadresse}',
      `liefername`='{$this->liefername}',
      `lieferabteilung`='{$this->lieferabteilung}',
      `lieferunterabteilung`='{$this->lieferunterabteilung}',
      `lieferstrasse`='{$this->lieferstrasse}',
      `lieferadresszusatz`='{$this->lieferadresszusatz}',
      `lieferansprechpartner`='{$this->lieferansprechpartner}',
      `lieferplz`='{$this->lieferplz}',
      `lieferort`='{$this->lieferort}',
      `lieferland`='{$this->lieferland}',
      `ustid`='{$this->ustid}',
      `email`='{$this->email}',
      `telefon`='{$this->telefon}',
      `telefax`='{$this->telefax}',
      `betreff`='{$this->betreff}',
      `kundennummer`='{$this->kundennummer}',
      `versandart`='{$this->versandart}',
      `versand`='{$this->versand}',
      `firma`='{$this->firma}',
      `versendet`='{$this->versendet}',
      `versendet_am`='{$this->versendet_am}',
      `versendet_per`='{$this->versendet_per}',
      `versendet_durch`='{$this->versendet_durch}',
      `inbearbeitung_user`='{$this->inbearbeitung_user}',
      `logdatei`='{$this->logdatei}',
      `vertriebid`='{$this->vertriebid}',
      `vertrieb`='{$this->vertrieb}',
      `ust_befreit`='{$this->ust_befreit}',
      `ihrebestellnummer`='{$this->ihrebestellnummer}',
      `anschreiben`='{$this->anschreiben}',
      `usereditid`='{$this->usereditid}',
      `useredittimestamp`='{$this->useredittimestamp}',
      `lieferantenretoure`='{$this->lieferantenretoure}',
      `lieferantenretoureinfo`='{$this->lieferantenretoureinfo}',
      `lieferant`='{$this->lieferant}',
      `schreibschutz`='{$this->schreibschutz}',
      `pdfarchiviert`='{$this->pdfarchiviert}',
      `pdfarchiviertversion`='{$this->pdfarchiviertversion}',
      `typ`='{$this->typ}',
      `internebemerkung`='{$this->internebemerkung}',
      `ohne_briefpapier`='{$this->ohne_briefpapier}',
      `lieferid`='{$this->lieferid}',
      `ansprechpartnerid`='{$this->ansprechpartnerid}',
      `projektfiliale`='{$this->projektfiliale}',
      `projektfiliale_eingelagert`='{$this->projektfiliale_eingelagert}',
      `zuarchivieren`='{$this->zuarchivieren}',
      `internebezeichnung`='{$this->internebezeichnung}',
      `angelegtam`='{$this->angelegtam}',
      `kommissionierung`='{$this->kommissionierung}',
      `sprache`='{$this->sprache}',
      `bundesland`='{$this->bundesland}',
      `gln`='{$this->gln}',
      `rechnungid`='{$this->rechnungid}',
      `bearbeiterid`='{$this->bearbeiterid}',
      `keinerechnung`='{$this->keinerechnung}',
      `ohne_artikeltext`='{$this->ohne_artikeltext}',
      `abweichendebezeichnung`='{$this->abweichendebezeichnung}',
      `bodyzusatz`='{$this->bodyzusatz}',
      `lieferbedingung`='{$this->lieferbedingung}',
      `titel`='{$this->titel}',
      `standardlager`='{$this->standardlager}',
      `kommissionskonsignationslager`='{$this->kommissionskonsignationslager}',
      `bundesstaat`='{$this->bundesstaat}',
      `teillieferungvon`='{$this->teillieferungvon}',
      `teillieferungnummer`='{$this->teillieferungnummer}',
      `gutschrift_id`='{$this->gutschrift_id}',
      `fortschritt`='{$this->fortschritt}',
      `storage_ok`='{$this->storage_ok}',
      `replacementorder_id`='{$this->replacementorder_id}'
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

    $sql = "DELETE FROM `retoure` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->datum='';
    $this->projekt='';
    $this->belegnr='';
    $this->bearbeiter='';
    $this->lieferschein='';
    $this->lieferscheinid='';
    $this->auftrag='';
    $this->auftragid='';
    $this->freitext='';
    $this->status='';
    $this->adresse='';
    $this->name='';
    $this->abteilung='';
    $this->unterabteilung='';
    $this->strasse='';
    $this->adresszusatz='';
    $this->ansprechpartner='';
    $this->plz='';
    $this->ort='';
    $this->land='';
    $this->abweichendelieferadresse='';
    $this->liefername='';
    $this->lieferabteilung='';
    $this->lieferunterabteilung='';
    $this->lieferstrasse='';
    $this->lieferadresszusatz='';
    $this->lieferansprechpartner='';
    $this->lieferplz='';
    $this->lieferort='';
    $this->lieferland='';
    $this->ustid='';
    $this->email='';
    $this->telefon='';
    $this->telefax='';
    $this->betreff='';
    $this->kundennummer='';
    $this->versandart='';
    $this->versand='';
    $this->firma='';
    $this->versendet='';
    $this->versendet_am='';
    $this->versendet_per='';
    $this->versendet_durch='';
    $this->inbearbeitung_user='';
    $this->logdatei='';
    $this->vertriebid='';
    $this->vertrieb='';
    $this->ust_befreit='';
    $this->ihrebestellnummer='';
    $this->anschreiben='';
    $this->usereditid='';
    $this->useredittimestamp='';
    $this->lieferantenretoure='';
    $this->lieferantenretoureinfo='';
    $this->lieferant='';
    $this->schreibschutz='';
    $this->pdfarchiviert='';
    $this->pdfarchiviertversion='';
    $this->typ='';
    $this->internebemerkung='';
    $this->ohne_briefpapier='';
    $this->lieferid='';
    $this->ansprechpartnerid='';
    $this->projektfiliale='';
    $this->projektfiliale_eingelagert='';
    $this->zuarchivieren='';
    $this->internebezeichnung='';
    $this->angelegtam='';
    $this->kommissionierung='';
    $this->sprache='';
    $this->bundesland='';
    $this->gln='';
    $this->rechnungid='';
    $this->bearbeiterid='';
    $this->keinerechnung='';
    $this->ohne_artikeltext='';
    $this->abweichendebezeichnung='';
    $this->bodyzusatz='';
    $this->lieferbedingung='';
    $this->titel='';
    $this->standardlager='';
    $this->kommissionskonsignationslager='';
    $this->bundesstaat='';
    $this->teillieferungvon='';
    $this->teillieferungnummer='';
    $this->gutschrift_id='';
    $this->fortschritt='';
    $this->storage_ok='';
    $this->replacementorder_id='';
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
  public function SetDatum($value) { $this->datum=$value; }
  public function GetDatum() { return $this->datum; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetBelegnr($value) { $this->belegnr=$value; }
  public function GetBelegnr() { return $this->belegnr; }
  public function SetBearbeiter($value) { $this->bearbeiter=$value; }
  public function GetBearbeiter() { return $this->bearbeiter; }
  public function SetLieferschein($value) { $this->lieferschein=$value; }
  public function GetLieferschein() { return $this->lieferschein; }
  public function SetLieferscheinid($value) { $this->lieferscheinid=$value; }
  public function GetLieferscheinid() { return $this->lieferscheinid; }
  public function SetAuftrag($value) { $this->auftrag=$value; }
  public function GetAuftrag() { return $this->auftrag; }
  public function SetAuftragid($value) { $this->auftragid=$value; }
  public function GetAuftragid() { return $this->auftragid; }
  public function SetFreitext($value) { $this->freitext=$value; }
  public function GetFreitext() { return $this->freitext; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetAdresse($value) { $this->adresse=$value; }
  public function GetAdresse() { return $this->adresse; }
  public function SetName($value) { $this->name=$value; }
  public function GetName() { return $this->name; }
  public function SetAbteilung($value) { $this->abteilung=$value; }
  public function GetAbteilung() { return $this->abteilung; }
  public function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  public function GetUnterabteilung() { return $this->unterabteilung; }
  public function SetStrasse($value) { $this->strasse=$value; }
  public function GetStrasse() { return $this->strasse; }
  public function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  public function GetAdresszusatz() { return $this->adresszusatz; }
  public function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  public function GetAnsprechpartner() { return $this->ansprechpartner; }
  public function SetPlz($value) { $this->plz=$value; }
  public function GetPlz() { return $this->plz; }
  public function SetOrt($value) { $this->ort=$value; }
  public function GetOrt() { return $this->ort; }
  public function SetLand($value) { $this->land=$value; }
  public function GetLand() { return $this->land; }
  public function SetAbweichendelieferadresse($value) { $this->abweichendelieferadresse=$value; }
  public function GetAbweichendelieferadresse() { return $this->abweichendelieferadresse; }
  public function SetLiefername($value) { $this->liefername=$value; }
  public function GetLiefername() { return $this->liefername; }
  public function SetLieferabteilung($value) { $this->lieferabteilung=$value; }
  public function GetLieferabteilung() { return $this->lieferabteilung; }
  public function SetLieferunterabteilung($value) { $this->lieferunterabteilung=$value; }
  public function GetLieferunterabteilung() { return $this->lieferunterabteilung; }
  public function SetLieferstrasse($value) { $this->lieferstrasse=$value; }
  public function GetLieferstrasse() { return $this->lieferstrasse; }
  public function SetLieferadresszusatz($value) { $this->lieferadresszusatz=$value; }
  public function GetLieferadresszusatz() { return $this->lieferadresszusatz; }
  public function SetLieferansprechpartner($value) { $this->lieferansprechpartner=$value; }
  public function GetLieferansprechpartner() { return $this->lieferansprechpartner; }
  public function SetLieferplz($value) { $this->lieferplz=$value; }
  public function GetLieferplz() { return $this->lieferplz; }
  public function SetLieferort($value) { $this->lieferort=$value; }
  public function GetLieferort() { return $this->lieferort; }
  public function SetLieferland($value) { $this->lieferland=$value; }
  public function GetLieferland() { return $this->lieferland; }
  public function SetUstid($value) { $this->ustid=$value; }
  public function GetUstid() { return $this->ustid; }
  public function SetEmail($value) { $this->email=$value; }
  public function GetEmail() { return $this->email; }
  public function SetTelefon($value) { $this->telefon=$value; }
  public function GetTelefon() { return $this->telefon; }
  public function SetTelefax($value) { $this->telefax=$value; }
  public function GetTelefax() { return $this->telefax; }
  public function SetBetreff($value) { $this->betreff=$value; }
  public function GetBetreff() { return $this->betreff; }
  public function SetKundennummer($value) { $this->kundennummer=$value; }
  public function GetKundennummer() { return $this->kundennummer; }
  public function SetVersandart($value) { $this->versandart=$value; }
  public function GetVersandart() { return $this->versandart; }
  public function SetVersand($value) { $this->versand=$value; }
  public function GetVersand() { return $this->versand; }
  public function SetFirma($value) { $this->firma=$value; }
  public function GetFirma() { return $this->firma; }
  public function SetVersendet($value) { $this->versendet=$value; }
  public function GetVersendet() { return $this->versendet; }
  public function SetVersendet_Am($value) { $this->versendet_am=$value; }
  public function GetVersendet_Am() { return $this->versendet_am; }
  public function SetVersendet_Per($value) { $this->versendet_per=$value; }
  public function GetVersendet_Per() { return $this->versendet_per; }
  public function SetVersendet_Durch($value) { $this->versendet_durch=$value; }
  public function GetVersendet_Durch() { return $this->versendet_durch; }
  public function SetInbearbeitung_User($value) { $this->inbearbeitung_user=$value; }
  public function GetInbearbeitung_User() { return $this->inbearbeitung_user; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetVertriebid($value) { $this->vertriebid=$value; }
  public function GetVertriebid() { return $this->vertriebid; }
  public function SetVertrieb($value) { $this->vertrieb=$value; }
  public function GetVertrieb() { return $this->vertrieb; }
  public function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  public function GetUst_Befreit() { return $this->ust_befreit; }
  public function SetIhrebestellnummer($value) { $this->ihrebestellnummer=$value; }
  public function GetIhrebestellnummer() { return $this->ihrebestellnummer; }
  public function SetAnschreiben($value) { $this->anschreiben=$value; }
  public function GetAnschreiben() { return $this->anschreiben; }
  public function SetUsereditid($value) { $this->usereditid=$value; }
  public function GetUsereditid() { return $this->usereditid; }
  public function SetUseredittimestamp($value) { $this->useredittimestamp=$value; }
  public function GetUseredittimestamp() { return $this->useredittimestamp; }
  public function SetLieferantenretoure($value) { $this->lieferantenretoure=$value; }
  public function GetLieferantenretoure() { return $this->lieferantenretoure; }
  public function SetLieferantenretoureinfo($value) { $this->lieferantenretoureinfo=$value; }
  public function GetLieferantenretoureinfo() { return $this->lieferantenretoureinfo; }
  public function SetLieferant($value) { $this->lieferant=$value; }
  public function GetLieferant() { return $this->lieferant; }
  public function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  public function GetSchreibschutz() { return $this->schreibschutz; }
  public function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  public function GetPdfarchiviert() { return $this->pdfarchiviert; }
  public function SetPdfarchiviertversion($value) { $this->pdfarchiviertversion=$value; }
  public function GetPdfarchiviertversion() { return $this->pdfarchiviertversion; }
  public function SetTyp($value) { $this->typ=$value; }
  public function GetTyp() { return $this->typ; }
  public function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  public function GetInternebemerkung() { return $this->internebemerkung; }
  public function SetOhne_Briefpapier($value) { $this->ohne_briefpapier=$value; }
  public function GetOhne_Briefpapier() { return $this->ohne_briefpapier; }
  public function SetLieferid($value) { $this->lieferid=$value; }
  public function GetLieferid() { return $this->lieferid; }
  public function SetAnsprechpartnerid($value) { $this->ansprechpartnerid=$value; }
  public function GetAnsprechpartnerid() { return $this->ansprechpartnerid; }
  public function SetProjektfiliale($value) { $this->projektfiliale=$value; }
  public function GetProjektfiliale() { return $this->projektfiliale; }
  public function SetProjektfiliale_Eingelagert($value) { $this->projektfiliale_eingelagert=$value; }
  public function GetProjektfiliale_Eingelagert() { return $this->projektfiliale_eingelagert; }
  public function SetZuarchivieren($value) { $this->zuarchivieren=$value; }
  public function GetZuarchivieren() { return $this->zuarchivieren; }
  public function SetInternebezeichnung($value) { $this->internebezeichnung=$value; }
  public function GetInternebezeichnung() { return $this->internebezeichnung; }
  public function SetAngelegtam($value) { $this->angelegtam=$value; }
  public function GetAngelegtam() { return $this->angelegtam; }
  public function SetKommissionierung($value) { $this->kommissionierung=$value; }
  public function GetKommissionierung() { return $this->kommissionierung; }
  public function SetSprache($value) { $this->sprache=$value; }
  public function GetSprache() { return $this->sprache; }
  public function SetBundesland($value) { $this->bundesland=$value; }
  public function GetBundesland() { return $this->bundesland; }
  public function SetGln($value) { $this->gln=$value; }
  public function GetGln() { return $this->gln; }
  public function SetRechnungid($value) { $this->rechnungid=$value; }
  public function GetRechnungid() { return $this->rechnungid; }
  public function SetBearbeiterid($value) { $this->bearbeiterid=$value; }
  public function GetBearbeiterid() { return $this->bearbeiterid; }
  public function SetKeinerechnung($value) { $this->keinerechnung=$value; }
  public function GetKeinerechnung() { return $this->keinerechnung; }
  public function SetOhne_Artikeltext($value) { $this->ohne_artikeltext=$value; }
  public function GetOhne_Artikeltext() { return $this->ohne_artikeltext; }
  public function SetAbweichendebezeichnung($value) { $this->abweichendebezeichnung=$value; }
  public function GetAbweichendebezeichnung() { return $this->abweichendebezeichnung; }
  public function SetBodyzusatz($value) { $this->bodyzusatz=$value; }
  public function GetBodyzusatz() { return $this->bodyzusatz; }
  public function SetLieferbedingung($value) { $this->lieferbedingung=$value; }
  public function GetLieferbedingung() { return $this->lieferbedingung; }
  public function SetTitel($value) { $this->titel=$value; }
  public function GetTitel() { return $this->titel; }
  public function SetStandardlager($value) { $this->standardlager=$value; }
  public function GetStandardlager() { return $this->standardlager; }
  public function SetKommissionskonsignationslager($value) { $this->kommissionskonsignationslager=$value; }
  public function GetKommissionskonsignationslager() { return $this->kommissionskonsignationslager; }
  public function SetBundesstaat($value) { $this->bundesstaat=$value; }
  public function GetBundesstaat() { return $this->bundesstaat; }
  public function SetTeillieferungvon($value) { $this->teillieferungvon=$value; }
  public function GetTeillieferungvon() { return $this->teillieferungvon; }
  public function SetTeillieferungnummer($value) { $this->teillieferungnummer=$value; }
  public function GetTeillieferungnummer() { return $this->teillieferungnummer; }
  public function SetGutschrift_Id($value) { $this->gutschrift_id=$value; }
  public function GetGutschrift_Id() { return $this->gutschrift_id; }
  public function SetFortschritt($value) { $this->fortschritt=$value; }
  public function GetFortschritt() { return $this->fortschritt; }
  public function SetStorage_Ok($value) { $this->storage_ok=$value; }
  public function GetStorage_Ok() { return $this->storage_ok; }
  public function SetReplacementorder_Id($value) { $this->replacementorder_id=$value; }
  public function GetReplacementorder_Id() { return $this->replacementorder_id; }

}
