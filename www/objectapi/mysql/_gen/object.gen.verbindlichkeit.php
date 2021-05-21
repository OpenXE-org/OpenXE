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

class ObjGenVerbindlichkeit
{

  private  $id;
  private  $belegnr;
  private  $status_beleg;
  private  $schreibschutz;
  private  $rechnung;
  private  $zahlbarbis;
  private  $betrag;
  private  $umsatzsteuer;
  private  $ustid;
  private  $summenormal;
  private  $summeermaessigt;
  private  $summesatz3;
  private  $summesatz4;
  private  $steuersatzname3;
  private  $steuersatzname4;
  private  $skonto;
  private  $skontobis;
  private  $skontofestsetzen;
  private  $freigabe;
  private  $freigabemitarbeiter;
  private  $bestellung;
  private  $adresse;
  private  $projekt;
  private  $teilprojekt;
  private  $auftrag;
  private  $status;
  private  $bezahlt;
  private  $kontoauszuege;
  private  $firma;
  private  $logdatei;
  private  $bestellung1;
  private  $bestellung1betrag;
  private  $bestellung1bemerkung;
  private  $bestellung1projekt;
  private  $bestellung1kostenstelle;
  private  $bestellung1auftrag;
  private  $bestellung2;
  private  $bestellung2betrag;
  private  $bestellung2bemerkung;
  private  $bestellung2kostenstelle;
  private  $bestellung2auftrag;
  private  $bestellung2projekt;
  private  $bestellung3;
  private  $bestellung3betrag;
  private  $bestellung3bemerkung;
  private  $bestellung3kostenstelle;
  private  $bestellung3auftrag;
  private  $bestellung3projekt;
  private  $bestellung4;
  private  $bestellung4betrag;
  private  $bestellung4bemerkung;
  private  $bestellung4kostenstelle;
  private  $bestellung4auftrag;
  private  $bestellung4projekt;
  private  $bestellung5;
  private  $bestellung5betrag;
  private  $bestellung5bemerkung;
  private  $bestellung5kostenstelle;
  private  $bestellung5auftrag;
  private  $bestellung5projekt;
  private  $bestellung6;
  private  $bestellung6betrag;
  private  $bestellung6bemerkung;
  private  $bestellung6kostenstelle;
  private  $bestellung6auftrag;
  private  $bestellung6projekt;
  private  $bestellung7;
  private  $bestellung7betrag;
  private  $bestellung7bemerkung;
  private  $bestellung7kostenstelle;
  private  $bestellung7auftrag;
  private  $bestellung7projekt;
  private  $bestellung8;
  private  $bestellung8betrag;
  private  $bestellung8bemerkung;
  private  $bestellung8kostenstelle;
  private  $bestellung8auftrag;
  private  $bestellung8projekt;
  private  $bestellung9;
  private  $bestellung9betrag;
  private  $bestellung9bemerkung;
  private  $bestellung9kostenstelle;
  private  $bestellung9auftrag;
  private  $bestellung9projekt;
  private  $bestellung10;
  private  $bestellung10betrag;
  private  $bestellung10bemerkung;
  private  $bestellung10kostenstelle;
  private  $bestellung10auftrag;
  private  $bestellung10projekt;
  private  $bestellung11;
  private  $bestellung11betrag;
  private  $bestellung11bemerkung;
  private  $bestellung11kostenstelle;
  private  $bestellung11auftrag;
  private  $bestellung11projekt;
  private  $bestellung12;
  private  $bestellung12betrag;
  private  $bestellung12bemerkung;
  private  $bestellung12projekt;
  private  $bestellung12kostenstelle;
  private  $bestellung12auftrag;
  private  $bestellung13;
  private  $bestellung13betrag;
  private  $bestellung13bemerkung;
  private  $bestellung13kostenstelle;
  private  $bestellung13auftrag;
  private  $bestellung13projekt;
  private  $bestellung14;
  private  $bestellung14betrag;
  private  $bestellung14bemerkung;
  private  $bestellung14kostenstelle;
  private  $bestellung14auftrag;
  private  $bestellung14projekt;
  private  $bestellung15;
  private  $bestellung15betrag;
  private  $bestellung15bemerkung;
  private  $bestellung15kostenstelle;
  private  $bestellung15auftrag;
  private  $bestellung15projekt;
  private  $waehrung;
  private  $zahlungsweise;
  private  $eingangsdatum;
  private  $buha_konto1;
  private  $buha_belegfeld1;
  private  $buha_betrag1;
  private  $buha_konto2;
  private  $buha_belegfeld2;
  private  $buha_betrag2;
  private  $buha_konto3;
  private  $buha_belegfeld3;
  private  $buha_betrag3;
  private  $buha_konto4;
  private  $buha_belegfeld4;
  private  $buha_betrag4;
  private  $buha_konto5;
  private  $buha_belegfeld5;
  private  $buha_betrag5;
  private  $rechnungsdatum;
  private  $rechnungsfreigabe;
  private  $kostenstelle;
  private  $beschreibung;
  private  $sachkonto;
  private  $art;
  private  $verwendungszweck;
  private  $dta_datei;
  private  $frachtkosten;
  private  $internebemerkung;
  private  $ustnormal;
  private  $ustermaessigt;
  private  $uststuer3;
  private  $uststuer4;
  private  $betragbezahlt;
  private  $bezahltam;
  private  $klaerfall;
  private  $klaergrund;
  private  $kurs;
  private  $skonto_erhalten;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `verbindlichkeit` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->belegnr=$result['belegnr'];
    $this->status_beleg=$result['status_beleg'];
    $this->schreibschutz=$result['schreibschutz'];
    $this->rechnung=$result['rechnung'];
    $this->zahlbarbis=$result['zahlbarbis'];
    $this->betrag=$result['betrag'];
    $this->umsatzsteuer=$result['umsatzsteuer'];
    $this->ustid=$result['ustid'];
    $this->summenormal=$result['summenormal'];
    $this->summeermaessigt=$result['summeermaessigt'];
    $this->summesatz3=$result['summesatz3'];
    $this->summesatz4=$result['summesatz4'];
    $this->steuersatzname3=$result['steuersatzname3'];
    $this->steuersatzname4=$result['steuersatzname4'];
    $this->skonto=$result['skonto'];
    $this->skontobis=$result['skontobis'];
    $this->skontofestsetzen=$result['skontofestsetzen'];
    $this->freigabe=$result['freigabe'];
    $this->freigabemitarbeiter=$result['freigabemitarbeiter'];
    $this->bestellung=$result['bestellung'];
    $this->adresse=$result['adresse'];
    $this->projekt=$result['projekt'];
    $this->teilprojekt=$result['teilprojekt'];
    $this->auftrag=$result['auftrag'];
    $this->status=$result['status'];
    $this->bezahlt=$result['bezahlt'];
    $this->kontoauszuege=$result['kontoauszuege'];
    $this->firma=$result['firma'];
    $this->logdatei=$result['logdatei'];
    $this->bestellung1=$result['bestellung1'];
    $this->bestellung1betrag=$result['bestellung1betrag'];
    $this->bestellung1bemerkung=$result['bestellung1bemerkung'];
    $this->bestellung1projekt=$result['bestellung1projekt'];
    $this->bestellung1kostenstelle=$result['bestellung1kostenstelle'];
    $this->bestellung1auftrag=$result['bestellung1auftrag'];
    $this->bestellung2=$result['bestellung2'];
    $this->bestellung2betrag=$result['bestellung2betrag'];
    $this->bestellung2bemerkung=$result['bestellung2bemerkung'];
    $this->bestellung2kostenstelle=$result['bestellung2kostenstelle'];
    $this->bestellung2auftrag=$result['bestellung2auftrag'];
    $this->bestellung2projekt=$result['bestellung2projekt'];
    $this->bestellung3=$result['bestellung3'];
    $this->bestellung3betrag=$result['bestellung3betrag'];
    $this->bestellung3bemerkung=$result['bestellung3bemerkung'];
    $this->bestellung3kostenstelle=$result['bestellung3kostenstelle'];
    $this->bestellung3auftrag=$result['bestellung3auftrag'];
    $this->bestellung3projekt=$result['bestellung3projekt'];
    $this->bestellung4=$result['bestellung4'];
    $this->bestellung4betrag=$result['bestellung4betrag'];
    $this->bestellung4bemerkung=$result['bestellung4bemerkung'];
    $this->bestellung4kostenstelle=$result['bestellung4kostenstelle'];
    $this->bestellung4auftrag=$result['bestellung4auftrag'];
    $this->bestellung4projekt=$result['bestellung4projekt'];
    $this->bestellung5=$result['bestellung5'];
    $this->bestellung5betrag=$result['bestellung5betrag'];
    $this->bestellung5bemerkung=$result['bestellung5bemerkung'];
    $this->bestellung5kostenstelle=$result['bestellung5kostenstelle'];
    $this->bestellung5auftrag=$result['bestellung5auftrag'];
    $this->bestellung5projekt=$result['bestellung5projekt'];
    $this->bestellung6=$result['bestellung6'];
    $this->bestellung6betrag=$result['bestellung6betrag'];
    $this->bestellung6bemerkung=$result['bestellung6bemerkung'];
    $this->bestellung6kostenstelle=$result['bestellung6kostenstelle'];
    $this->bestellung6auftrag=$result['bestellung6auftrag'];
    $this->bestellung6projekt=$result['bestellung6projekt'];
    $this->bestellung7=$result['bestellung7'];
    $this->bestellung7betrag=$result['bestellung7betrag'];
    $this->bestellung7bemerkung=$result['bestellung7bemerkung'];
    $this->bestellung7kostenstelle=$result['bestellung7kostenstelle'];
    $this->bestellung7auftrag=$result['bestellung7auftrag'];
    $this->bestellung7projekt=$result['bestellung7projekt'];
    $this->bestellung8=$result['bestellung8'];
    $this->bestellung8betrag=$result['bestellung8betrag'];
    $this->bestellung8bemerkung=$result['bestellung8bemerkung'];
    $this->bestellung8kostenstelle=$result['bestellung8kostenstelle'];
    $this->bestellung8auftrag=$result['bestellung8auftrag'];
    $this->bestellung8projekt=$result['bestellung8projekt'];
    $this->bestellung9=$result['bestellung9'];
    $this->bestellung9betrag=$result['bestellung9betrag'];
    $this->bestellung9bemerkung=$result['bestellung9bemerkung'];
    $this->bestellung9kostenstelle=$result['bestellung9kostenstelle'];
    $this->bestellung9auftrag=$result['bestellung9auftrag'];
    $this->bestellung9projekt=$result['bestellung9projekt'];
    $this->bestellung10=$result['bestellung10'];
    $this->bestellung10betrag=$result['bestellung10betrag'];
    $this->bestellung10bemerkung=$result['bestellung10bemerkung'];
    $this->bestellung10kostenstelle=$result['bestellung10kostenstelle'];
    $this->bestellung10auftrag=$result['bestellung10auftrag'];
    $this->bestellung10projekt=$result['bestellung10projekt'];
    $this->bestellung11=$result['bestellung11'];
    $this->bestellung11betrag=$result['bestellung11betrag'];
    $this->bestellung11bemerkung=$result['bestellung11bemerkung'];
    $this->bestellung11kostenstelle=$result['bestellung11kostenstelle'];
    $this->bestellung11auftrag=$result['bestellung11auftrag'];
    $this->bestellung11projekt=$result['bestellung11projekt'];
    $this->bestellung12=$result['bestellung12'];
    $this->bestellung12betrag=$result['bestellung12betrag'];
    $this->bestellung12bemerkung=$result['bestellung12bemerkung'];
    $this->bestellung12projekt=$result['bestellung12projekt'];
    $this->bestellung12kostenstelle=$result['bestellung12kostenstelle'];
    $this->bestellung12auftrag=$result['bestellung12auftrag'];
    $this->bestellung13=$result['bestellung13'];
    $this->bestellung13betrag=$result['bestellung13betrag'];
    $this->bestellung13bemerkung=$result['bestellung13bemerkung'];
    $this->bestellung13kostenstelle=$result['bestellung13kostenstelle'];
    $this->bestellung13auftrag=$result['bestellung13auftrag'];
    $this->bestellung13projekt=$result['bestellung13projekt'];
    $this->bestellung14=$result['bestellung14'];
    $this->bestellung14betrag=$result['bestellung14betrag'];
    $this->bestellung14bemerkung=$result['bestellung14bemerkung'];
    $this->bestellung14kostenstelle=$result['bestellung14kostenstelle'];
    $this->bestellung14auftrag=$result['bestellung14auftrag'];
    $this->bestellung14projekt=$result['bestellung14projekt'];
    $this->bestellung15=$result['bestellung15'];
    $this->bestellung15betrag=$result['bestellung15betrag'];
    $this->bestellung15bemerkung=$result['bestellung15bemerkung'];
    $this->bestellung15kostenstelle=$result['bestellung15kostenstelle'];
    $this->bestellung15auftrag=$result['bestellung15auftrag'];
    $this->bestellung15projekt=$result['bestellung15projekt'];
    $this->waehrung=$result['waehrung'];
    $this->zahlungsweise=$result['zahlungsweise'];
    $this->eingangsdatum=$result['eingangsdatum'];
    $this->buha_konto1=$result['buha_konto1'];
    $this->buha_belegfeld1=$result['buha_belegfeld1'];
    $this->buha_betrag1=$result['buha_betrag1'];
    $this->buha_konto2=$result['buha_konto2'];
    $this->buha_belegfeld2=$result['buha_belegfeld2'];
    $this->buha_betrag2=$result['buha_betrag2'];
    $this->buha_konto3=$result['buha_konto3'];
    $this->buha_belegfeld3=$result['buha_belegfeld3'];
    $this->buha_betrag3=$result['buha_betrag3'];
    $this->buha_konto4=$result['buha_konto4'];
    $this->buha_belegfeld4=$result['buha_belegfeld4'];
    $this->buha_betrag4=$result['buha_betrag4'];
    $this->buha_konto5=$result['buha_konto5'];
    $this->buha_belegfeld5=$result['buha_belegfeld5'];
    $this->buha_betrag5=$result['buha_betrag5'];
    $this->rechnungsdatum=$result['rechnungsdatum'];
    $this->rechnungsfreigabe=$result['rechnungsfreigabe'];
    $this->kostenstelle=$result['kostenstelle'];
    $this->beschreibung=$result['beschreibung'];
    $this->sachkonto=$result['sachkonto'];
    $this->art=$result['art'];
    $this->verwendungszweck=$result['verwendungszweck'];
    $this->dta_datei=$result['dta_datei'];
    $this->frachtkosten=$result['frachtkosten'];
    $this->internebemerkung=$result['internebemerkung'];
    $this->ustnormal=$result['ustnormal'];
    $this->ustermaessigt=$result['ustermaessigt'];
    $this->uststuer3=$result['uststuer3'];
    $this->uststuer4=$result['uststuer4'];
    $this->betragbezahlt=$result['betragbezahlt'];
    $this->bezahltam=$result['bezahltam'];
    $this->klaerfall=$result['klaerfall'];
    $this->klaergrund=$result['klaergrund'];
    $this->kurs=$result['kurs'];
    $this->skonto_erhalten=$result['skonto_erhalten'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `verbindlichkeit` (`id`,`belegnr`,`status_beleg`,`schreibschutz`,`rechnung`,`zahlbarbis`,`betrag`,`umsatzsteuer`,`ustid`,`summenormal`,`summeermaessigt`,`summesatz3`,`summesatz4`,`steuersatzname3`,`steuersatzname4`,`skonto`,`skontobis`,`skontofestsetzen`,`freigabe`,`freigabemitarbeiter`,`bestellung`,`adresse`,`projekt`,`teilprojekt`,`auftrag`,`status`,`bezahlt`,`kontoauszuege`,`firma`,`logdatei`,`bestellung1`,`bestellung1betrag`,`bestellung1bemerkung`,`bestellung1projekt`,`bestellung1kostenstelle`,`bestellung1auftrag`,`bestellung2`,`bestellung2betrag`,`bestellung2bemerkung`,`bestellung2kostenstelle`,`bestellung2auftrag`,`bestellung2projekt`,`bestellung3`,`bestellung3betrag`,`bestellung3bemerkung`,`bestellung3kostenstelle`,`bestellung3auftrag`,`bestellung3projekt`,`bestellung4`,`bestellung4betrag`,`bestellung4bemerkung`,`bestellung4kostenstelle`,`bestellung4auftrag`,`bestellung4projekt`,`bestellung5`,`bestellung5betrag`,`bestellung5bemerkung`,`bestellung5kostenstelle`,`bestellung5auftrag`,`bestellung5projekt`,`bestellung6`,`bestellung6betrag`,`bestellung6bemerkung`,`bestellung6kostenstelle`,`bestellung6auftrag`,`bestellung6projekt`,`bestellung7`,`bestellung7betrag`,`bestellung7bemerkung`,`bestellung7kostenstelle`,`bestellung7auftrag`,`bestellung7projekt`,`bestellung8`,`bestellung8betrag`,`bestellung8bemerkung`,`bestellung8kostenstelle`,`bestellung8auftrag`,`bestellung8projekt`,`bestellung9`,`bestellung9betrag`,`bestellung9bemerkung`,`bestellung9kostenstelle`,`bestellung9auftrag`,`bestellung9projekt`,`bestellung10`,`bestellung10betrag`,`bestellung10bemerkung`,`bestellung10kostenstelle`,`bestellung10auftrag`,`bestellung10projekt`,`bestellung11`,`bestellung11betrag`,`bestellung11bemerkung`,`bestellung11kostenstelle`,`bestellung11auftrag`,`bestellung11projekt`,`bestellung12`,`bestellung12betrag`,`bestellung12bemerkung`,`bestellung12projekt`,`bestellung12kostenstelle`,`bestellung12auftrag`,`bestellung13`,`bestellung13betrag`,`bestellung13bemerkung`,`bestellung13kostenstelle`,`bestellung13auftrag`,`bestellung13projekt`,`bestellung14`,`bestellung14betrag`,`bestellung14bemerkung`,`bestellung14kostenstelle`,`bestellung14auftrag`,`bestellung14projekt`,`bestellung15`,`bestellung15betrag`,`bestellung15bemerkung`,`bestellung15kostenstelle`,`bestellung15auftrag`,`bestellung15projekt`,`waehrung`,`zahlungsweise`,`eingangsdatum`,`buha_konto1`,`buha_belegfeld1`,`buha_betrag1`,`buha_konto2`,`buha_belegfeld2`,`buha_betrag2`,`buha_konto3`,`buha_belegfeld3`,`buha_betrag3`,`buha_konto4`,`buha_belegfeld4`,`buha_betrag4`,`buha_konto5`,`buha_belegfeld5`,`buha_betrag5`,`rechnungsdatum`,`rechnungsfreigabe`,`kostenstelle`,`beschreibung`,`sachkonto`,`art`,`verwendungszweck`,`dta_datei`,`frachtkosten`,`internebemerkung`,`ustnormal`,`ustermaessigt`,`uststuer3`,`uststuer4`,`betragbezahlt`,`bezahltam`,`klaerfall`,`klaergrund`,`kurs`,`skonto_erhalten`)
      VALUES(NULL,'{$this->belegnr}','{$this->status_beleg}','{$this->schreibschutz}','{$this->rechnung}','{$this->zahlbarbis}','{$this->betrag}','{$this->umsatzsteuer}','{$this->ustid}','{$this->summenormal}','{$this->summeermaessigt}','{$this->summesatz3}','{$this->summesatz4}','{$this->steuersatzname3}','{$this->steuersatzname4}','{$this->skonto}','{$this->skontobis}','{$this->skontofestsetzen}','{$this->freigabe}','{$this->freigabemitarbeiter}','{$this->bestellung}','{$this->adresse}','{$this->projekt}','{$this->teilprojekt}','{$this->auftrag}','{$this->status}','{$this->bezahlt}','{$this->kontoauszuege}','{$this->firma}','{$this->logdatei}','{$this->bestellung1}','{$this->bestellung1betrag}','{$this->bestellung1bemerkung}','{$this->bestellung1projekt}','{$this->bestellung1kostenstelle}','{$this->bestellung1auftrag}','{$this->bestellung2}','{$this->bestellung2betrag}','{$this->bestellung2bemerkung}','{$this->bestellung2kostenstelle}','{$this->bestellung2auftrag}','{$this->bestellung2projekt}','{$this->bestellung3}','{$this->bestellung3betrag}','{$this->bestellung3bemerkung}','{$this->bestellung3kostenstelle}','{$this->bestellung3auftrag}','{$this->bestellung3projekt}','{$this->bestellung4}','{$this->bestellung4betrag}','{$this->bestellung4bemerkung}','{$this->bestellung4kostenstelle}','{$this->bestellung4auftrag}','{$this->bestellung4projekt}','{$this->bestellung5}','{$this->bestellung5betrag}','{$this->bestellung5bemerkung}','{$this->bestellung5kostenstelle}','{$this->bestellung5auftrag}','{$this->bestellung5projekt}','{$this->bestellung6}','{$this->bestellung6betrag}','{$this->bestellung6bemerkung}','{$this->bestellung6kostenstelle}','{$this->bestellung6auftrag}','{$this->bestellung6projekt}','{$this->bestellung7}','{$this->bestellung7betrag}','{$this->bestellung7bemerkung}','{$this->bestellung7kostenstelle}','{$this->bestellung7auftrag}','{$this->bestellung7projekt}','{$this->bestellung8}','{$this->bestellung8betrag}','{$this->bestellung8bemerkung}','{$this->bestellung8kostenstelle}','{$this->bestellung8auftrag}','{$this->bestellung8projekt}','{$this->bestellung9}','{$this->bestellung9betrag}','{$this->bestellung9bemerkung}','{$this->bestellung9kostenstelle}','{$this->bestellung9auftrag}','{$this->bestellung9projekt}','{$this->bestellung10}','{$this->bestellung10betrag}','{$this->bestellung10bemerkung}','{$this->bestellung10kostenstelle}','{$this->bestellung10auftrag}','{$this->bestellung10projekt}','{$this->bestellung11}','{$this->bestellung11betrag}','{$this->bestellung11bemerkung}','{$this->bestellung11kostenstelle}','{$this->bestellung11auftrag}','{$this->bestellung11projekt}','{$this->bestellung12}','{$this->bestellung12betrag}','{$this->bestellung12bemerkung}','{$this->bestellung12projekt}','{$this->bestellung12kostenstelle}','{$this->bestellung12auftrag}','{$this->bestellung13}','{$this->bestellung13betrag}','{$this->bestellung13bemerkung}','{$this->bestellung13kostenstelle}','{$this->bestellung13auftrag}','{$this->bestellung13projekt}','{$this->bestellung14}','{$this->bestellung14betrag}','{$this->bestellung14bemerkung}','{$this->bestellung14kostenstelle}','{$this->bestellung14auftrag}','{$this->bestellung14projekt}','{$this->bestellung15}','{$this->bestellung15betrag}','{$this->bestellung15bemerkung}','{$this->bestellung15kostenstelle}','{$this->bestellung15auftrag}','{$this->bestellung15projekt}','{$this->waehrung}','{$this->zahlungsweise}','{$this->eingangsdatum}','{$this->buha_konto1}','{$this->buha_belegfeld1}','{$this->buha_betrag1}','{$this->buha_konto2}','{$this->buha_belegfeld2}','{$this->buha_betrag2}','{$this->buha_konto3}','{$this->buha_belegfeld3}','{$this->buha_betrag3}','{$this->buha_konto4}','{$this->buha_belegfeld4}','{$this->buha_betrag4}','{$this->buha_konto5}','{$this->buha_belegfeld5}','{$this->buha_betrag5}','{$this->rechnungsdatum}','{$this->rechnungsfreigabe}','{$this->kostenstelle}','{$this->beschreibung}','{$this->sachkonto}','{$this->art}','{$this->verwendungszweck}','{$this->dta_datei}','{$this->frachtkosten}','{$this->internebemerkung}','{$this->ustnormal}','{$this->ustermaessigt}','{$this->uststuer3}','{$this->uststuer4}','{$this->betragbezahlt}','{$this->bezahltam}','{$this->klaerfall}','{$this->klaergrund}','{$this->kurs}','{$this->skonto_erhalten}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `verbindlichkeit` SET
      `belegnr`='{$this->belegnr}',
      `status_beleg`='{$this->status_beleg}',
      `schreibschutz`='{$this->schreibschutz}',
      `rechnung`='{$this->rechnung}',
      `zahlbarbis`='{$this->zahlbarbis}',
      `betrag`='{$this->betrag}',
      `umsatzsteuer`='{$this->umsatzsteuer}',
      `ustid`='{$this->ustid}',
      `summenormal`='{$this->summenormal}',
      `summeermaessigt`='{$this->summeermaessigt}',
      `summesatz3`='{$this->summesatz3}',
      `summesatz4`='{$this->summesatz4}',
      `steuersatzname3`='{$this->steuersatzname3}',
      `steuersatzname4`='{$this->steuersatzname4}',
      `skonto`='{$this->skonto}',
      `skontobis`='{$this->skontobis}',
      `skontofestsetzen`='{$this->skontofestsetzen}',
      `freigabe`='{$this->freigabe}',
      `freigabemitarbeiter`='{$this->freigabemitarbeiter}',
      `bestellung`='{$this->bestellung}',
      `adresse`='{$this->adresse}',
      `projekt`='{$this->projekt}',
      `teilprojekt`='{$this->teilprojekt}',
      `auftrag`='{$this->auftrag}',
      `status`='{$this->status}',
      `bezahlt`='{$this->bezahlt}',
      `kontoauszuege`='{$this->kontoauszuege}',
      `firma`='{$this->firma}',
      `logdatei`='{$this->logdatei}',
      `bestellung1`='{$this->bestellung1}',
      `bestellung1betrag`='{$this->bestellung1betrag}',
      `bestellung1bemerkung`='{$this->bestellung1bemerkung}',
      `bestellung1projekt`='{$this->bestellung1projekt}',
      `bestellung1kostenstelle`='{$this->bestellung1kostenstelle}',
      `bestellung1auftrag`='{$this->bestellung1auftrag}',
      `bestellung2`='{$this->bestellung2}',
      `bestellung2betrag`='{$this->bestellung2betrag}',
      `bestellung2bemerkung`='{$this->bestellung2bemerkung}',
      `bestellung2kostenstelle`='{$this->bestellung2kostenstelle}',
      `bestellung2auftrag`='{$this->bestellung2auftrag}',
      `bestellung2projekt`='{$this->bestellung2projekt}',
      `bestellung3`='{$this->bestellung3}',
      `bestellung3betrag`='{$this->bestellung3betrag}',
      `bestellung3bemerkung`='{$this->bestellung3bemerkung}',
      `bestellung3kostenstelle`='{$this->bestellung3kostenstelle}',
      `bestellung3auftrag`='{$this->bestellung3auftrag}',
      `bestellung3projekt`='{$this->bestellung3projekt}',
      `bestellung4`='{$this->bestellung4}',
      `bestellung4betrag`='{$this->bestellung4betrag}',
      `bestellung4bemerkung`='{$this->bestellung4bemerkung}',
      `bestellung4kostenstelle`='{$this->bestellung4kostenstelle}',
      `bestellung4auftrag`='{$this->bestellung4auftrag}',
      `bestellung4projekt`='{$this->bestellung4projekt}',
      `bestellung5`='{$this->bestellung5}',
      `bestellung5betrag`='{$this->bestellung5betrag}',
      `bestellung5bemerkung`='{$this->bestellung5bemerkung}',
      `bestellung5kostenstelle`='{$this->bestellung5kostenstelle}',
      `bestellung5auftrag`='{$this->bestellung5auftrag}',
      `bestellung5projekt`='{$this->bestellung5projekt}',
      `bestellung6`='{$this->bestellung6}',
      `bestellung6betrag`='{$this->bestellung6betrag}',
      `bestellung6bemerkung`='{$this->bestellung6bemerkung}',
      `bestellung6kostenstelle`='{$this->bestellung6kostenstelle}',
      `bestellung6auftrag`='{$this->bestellung6auftrag}',
      `bestellung6projekt`='{$this->bestellung6projekt}',
      `bestellung7`='{$this->bestellung7}',
      `bestellung7betrag`='{$this->bestellung7betrag}',
      `bestellung7bemerkung`='{$this->bestellung7bemerkung}',
      `bestellung7kostenstelle`='{$this->bestellung7kostenstelle}',
      `bestellung7auftrag`='{$this->bestellung7auftrag}',
      `bestellung7projekt`='{$this->bestellung7projekt}',
      `bestellung8`='{$this->bestellung8}',
      `bestellung8betrag`='{$this->bestellung8betrag}',
      `bestellung8bemerkung`='{$this->bestellung8bemerkung}',
      `bestellung8kostenstelle`='{$this->bestellung8kostenstelle}',
      `bestellung8auftrag`='{$this->bestellung8auftrag}',
      `bestellung8projekt`='{$this->bestellung8projekt}',
      `bestellung9`='{$this->bestellung9}',
      `bestellung9betrag`='{$this->bestellung9betrag}',
      `bestellung9bemerkung`='{$this->bestellung9bemerkung}',
      `bestellung9kostenstelle`='{$this->bestellung9kostenstelle}',
      `bestellung9auftrag`='{$this->bestellung9auftrag}',
      `bestellung9projekt`='{$this->bestellung9projekt}',
      `bestellung10`='{$this->bestellung10}',
      `bestellung10betrag`='{$this->bestellung10betrag}',
      `bestellung10bemerkung`='{$this->bestellung10bemerkung}',
      `bestellung10kostenstelle`='{$this->bestellung10kostenstelle}',
      `bestellung10auftrag`='{$this->bestellung10auftrag}',
      `bestellung10projekt`='{$this->bestellung10projekt}',
      `bestellung11`='{$this->bestellung11}',
      `bestellung11betrag`='{$this->bestellung11betrag}',
      `bestellung11bemerkung`='{$this->bestellung11bemerkung}',
      `bestellung11kostenstelle`='{$this->bestellung11kostenstelle}',
      `bestellung11auftrag`='{$this->bestellung11auftrag}',
      `bestellung11projekt`='{$this->bestellung11projekt}',
      `bestellung12`='{$this->bestellung12}',
      `bestellung12betrag`='{$this->bestellung12betrag}',
      `bestellung12bemerkung`='{$this->bestellung12bemerkung}',
      `bestellung12projekt`='{$this->bestellung12projekt}',
      `bestellung12kostenstelle`='{$this->bestellung12kostenstelle}',
      `bestellung12auftrag`='{$this->bestellung12auftrag}',
      `bestellung13`='{$this->bestellung13}',
      `bestellung13betrag`='{$this->bestellung13betrag}',
      `bestellung13bemerkung`='{$this->bestellung13bemerkung}',
      `bestellung13kostenstelle`='{$this->bestellung13kostenstelle}',
      `bestellung13auftrag`='{$this->bestellung13auftrag}',
      `bestellung13projekt`='{$this->bestellung13projekt}',
      `bestellung14`='{$this->bestellung14}',
      `bestellung14betrag`='{$this->bestellung14betrag}',
      `bestellung14bemerkung`='{$this->bestellung14bemerkung}',
      `bestellung14kostenstelle`='{$this->bestellung14kostenstelle}',
      `bestellung14auftrag`='{$this->bestellung14auftrag}',
      `bestellung14projekt`='{$this->bestellung14projekt}',
      `bestellung15`='{$this->bestellung15}',
      `bestellung15betrag`='{$this->bestellung15betrag}',
      `bestellung15bemerkung`='{$this->bestellung15bemerkung}',
      `bestellung15kostenstelle`='{$this->bestellung15kostenstelle}',
      `bestellung15auftrag`='{$this->bestellung15auftrag}',
      `bestellung15projekt`='{$this->bestellung15projekt}',
      `waehrung`='{$this->waehrung}',
      `zahlungsweise`='{$this->zahlungsweise}',
      `eingangsdatum`='{$this->eingangsdatum}',
      `buha_konto1`='{$this->buha_konto1}',
      `buha_belegfeld1`='{$this->buha_belegfeld1}',
      `buha_betrag1`='{$this->buha_betrag1}',
      `buha_konto2`='{$this->buha_konto2}',
      `buha_belegfeld2`='{$this->buha_belegfeld2}',
      `buha_betrag2`='{$this->buha_betrag2}',
      `buha_konto3`='{$this->buha_konto3}',
      `buha_belegfeld3`='{$this->buha_belegfeld3}',
      `buha_betrag3`='{$this->buha_betrag3}',
      `buha_konto4`='{$this->buha_konto4}',
      `buha_belegfeld4`='{$this->buha_belegfeld4}',
      `buha_betrag4`='{$this->buha_betrag4}',
      `buha_konto5`='{$this->buha_konto5}',
      `buha_belegfeld5`='{$this->buha_belegfeld5}',
      `buha_betrag5`='{$this->buha_betrag5}',
      `rechnungsdatum`='{$this->rechnungsdatum}',
      `rechnungsfreigabe`='{$this->rechnungsfreigabe}',
      `kostenstelle`='{$this->kostenstelle}',
      `beschreibung`='{$this->beschreibung}',
      `sachkonto`='{$this->sachkonto}',
      `art`='{$this->art}',
      `verwendungszweck`='{$this->verwendungszweck}',
      `dta_datei`='{$this->dta_datei}',
      `frachtkosten`='{$this->frachtkosten}',
      `internebemerkung`='{$this->internebemerkung}',
      `ustnormal`='{$this->ustnormal}',
      `ustermaessigt`='{$this->ustermaessigt}',
      `uststuer3`='{$this->uststuer3}',
      `uststuer4`='{$this->uststuer4}',
      `betragbezahlt`='{$this->betragbezahlt}',
      `bezahltam`='{$this->bezahltam}',
      `klaerfall`='{$this->klaerfall}',
      `klaergrund`='{$this->klaergrund}',
      `kurs`='{$this->kurs}',
      `skonto_erhalten`='{$this->skonto_erhalten}'
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

    $sql = "DELETE FROM `verbindlichkeit` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->belegnr='';
    $this->status_beleg='';
    $this->schreibschutz='';
    $this->rechnung='';
    $this->zahlbarbis='';
    $this->betrag='';
    $this->umsatzsteuer='';
    $this->ustid='';
    $this->summenormal='';
    $this->summeermaessigt='';
    $this->summesatz3='';
    $this->summesatz4='';
    $this->steuersatzname3='';
    $this->steuersatzname4='';
    $this->skonto='';
    $this->skontobis='';
    $this->skontofestsetzen='';
    $this->freigabe='';
    $this->freigabemitarbeiter='';
    $this->bestellung='';
    $this->adresse='';
    $this->projekt='';
    $this->teilprojekt='';
    $this->auftrag='';
    $this->status='';
    $this->bezahlt='';
    $this->kontoauszuege='';
    $this->firma='';
    $this->logdatei='';
    $this->bestellung1='';
    $this->bestellung1betrag='';
    $this->bestellung1bemerkung='';
    $this->bestellung1projekt='';
    $this->bestellung1kostenstelle='';
    $this->bestellung1auftrag='';
    $this->bestellung2='';
    $this->bestellung2betrag='';
    $this->bestellung2bemerkung='';
    $this->bestellung2kostenstelle='';
    $this->bestellung2auftrag='';
    $this->bestellung2projekt='';
    $this->bestellung3='';
    $this->bestellung3betrag='';
    $this->bestellung3bemerkung='';
    $this->bestellung3kostenstelle='';
    $this->bestellung3auftrag='';
    $this->bestellung3projekt='';
    $this->bestellung4='';
    $this->bestellung4betrag='';
    $this->bestellung4bemerkung='';
    $this->bestellung4kostenstelle='';
    $this->bestellung4auftrag='';
    $this->bestellung4projekt='';
    $this->bestellung5='';
    $this->bestellung5betrag='';
    $this->bestellung5bemerkung='';
    $this->bestellung5kostenstelle='';
    $this->bestellung5auftrag='';
    $this->bestellung5projekt='';
    $this->bestellung6='';
    $this->bestellung6betrag='';
    $this->bestellung6bemerkung='';
    $this->bestellung6kostenstelle='';
    $this->bestellung6auftrag='';
    $this->bestellung6projekt='';
    $this->bestellung7='';
    $this->bestellung7betrag='';
    $this->bestellung7bemerkung='';
    $this->bestellung7kostenstelle='';
    $this->bestellung7auftrag='';
    $this->bestellung7projekt='';
    $this->bestellung8='';
    $this->bestellung8betrag='';
    $this->bestellung8bemerkung='';
    $this->bestellung8kostenstelle='';
    $this->bestellung8auftrag='';
    $this->bestellung8projekt='';
    $this->bestellung9='';
    $this->bestellung9betrag='';
    $this->bestellung9bemerkung='';
    $this->bestellung9kostenstelle='';
    $this->bestellung9auftrag='';
    $this->bestellung9projekt='';
    $this->bestellung10='';
    $this->bestellung10betrag='';
    $this->bestellung10bemerkung='';
    $this->bestellung10kostenstelle='';
    $this->bestellung10auftrag='';
    $this->bestellung10projekt='';
    $this->bestellung11='';
    $this->bestellung11betrag='';
    $this->bestellung11bemerkung='';
    $this->bestellung11kostenstelle='';
    $this->bestellung11auftrag='';
    $this->bestellung11projekt='';
    $this->bestellung12='';
    $this->bestellung12betrag='';
    $this->bestellung12bemerkung='';
    $this->bestellung12projekt='';
    $this->bestellung12kostenstelle='';
    $this->bestellung12auftrag='';
    $this->bestellung13='';
    $this->bestellung13betrag='';
    $this->bestellung13bemerkung='';
    $this->bestellung13kostenstelle='';
    $this->bestellung13auftrag='';
    $this->bestellung13projekt='';
    $this->bestellung14='';
    $this->bestellung14betrag='';
    $this->bestellung14bemerkung='';
    $this->bestellung14kostenstelle='';
    $this->bestellung14auftrag='';
    $this->bestellung14projekt='';
    $this->bestellung15='';
    $this->bestellung15betrag='';
    $this->bestellung15bemerkung='';
    $this->bestellung15kostenstelle='';
    $this->bestellung15auftrag='';
    $this->bestellung15projekt='';
    $this->waehrung='';
    $this->zahlungsweise='';
    $this->eingangsdatum='';
    $this->buha_konto1='';
    $this->buha_belegfeld1='';
    $this->buha_betrag1='';
    $this->buha_konto2='';
    $this->buha_belegfeld2='';
    $this->buha_betrag2='';
    $this->buha_konto3='';
    $this->buha_belegfeld3='';
    $this->buha_betrag3='';
    $this->buha_konto4='';
    $this->buha_belegfeld4='';
    $this->buha_betrag4='';
    $this->buha_konto5='';
    $this->buha_belegfeld5='';
    $this->buha_betrag5='';
    $this->rechnungsdatum='';
    $this->rechnungsfreigabe='';
    $this->kostenstelle='';
    $this->beschreibung='';
    $this->sachkonto='';
    $this->art='';
    $this->verwendungszweck='';
    $this->dta_datei='';
    $this->frachtkosten='';
    $this->internebemerkung='';
    $this->ustnormal='';
    $this->ustermaessigt='';
    $this->uststuer3='';
    $this->uststuer4='';
    $this->betragbezahlt='';
    $this->bezahltam='';
    $this->klaerfall='';
    $this->klaergrund='';
    $this->kurs='';
    $this->skonto_erhalten='';
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
  public function SetBelegnr($value) { $this->belegnr=$value; }
  public function GetBelegnr() { return $this->belegnr; }
  public function SetStatus_Beleg($value) { $this->status_beleg=$value; }
  public function GetStatus_Beleg() { return $this->status_beleg; }
  public function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  public function GetSchreibschutz() { return $this->schreibschutz; }
  public function SetRechnung($value) { $this->rechnung=$value; }
  public function GetRechnung() { return $this->rechnung; }
  public function SetZahlbarbis($value) { $this->zahlbarbis=$value; }
  public function GetZahlbarbis() { return $this->zahlbarbis; }
  public function SetBetrag($value) { $this->betrag=$value; }
  public function GetBetrag() { return $this->betrag; }
  public function SetUmsatzsteuer($value) { $this->umsatzsteuer=$value; }
  public function GetUmsatzsteuer() { return $this->umsatzsteuer; }
  public function SetUstid($value) { $this->ustid=$value; }
  public function GetUstid() { return $this->ustid; }
  public function SetSummenormal($value) { $this->summenormal=$value; }
  public function GetSummenormal() { return $this->summenormal; }
  public function SetSummeermaessigt($value) { $this->summeermaessigt=$value; }
  public function GetSummeermaessigt() { return $this->summeermaessigt; }
  public function SetSummesatz3($value) { $this->summesatz3=$value; }
  public function GetSummesatz3() { return $this->summesatz3; }
  public function SetSummesatz4($value) { $this->summesatz4=$value; }
  public function GetSummesatz4() { return $this->summesatz4; }
  public function SetSteuersatzname3($value) { $this->steuersatzname3=$value; }
  public function GetSteuersatzname3() { return $this->steuersatzname3; }
  public function SetSteuersatzname4($value) { $this->steuersatzname4=$value; }
  public function GetSteuersatzname4() { return $this->steuersatzname4; }
  public function SetSkonto($value) { $this->skonto=$value; }
  public function GetSkonto() { return $this->skonto; }
  public function SetSkontobis($value) { $this->skontobis=$value; }
  public function GetSkontobis() { return $this->skontobis; }
  public function SetSkontofestsetzen($value) { $this->skontofestsetzen=$value; }
  public function GetSkontofestsetzen() { return $this->skontofestsetzen; }
  public function SetFreigabe($value) { $this->freigabe=$value; }
  public function GetFreigabe() { return $this->freigabe; }
  public function SetFreigabemitarbeiter($value) { $this->freigabemitarbeiter=$value; }
  public function GetFreigabemitarbeiter() { return $this->freigabemitarbeiter; }
  public function SetBestellung($value) { $this->bestellung=$value; }
  public function GetBestellung() { return $this->bestellung; }
  public function SetAdresse($value) { $this->adresse=$value; }
  public function GetAdresse() { return $this->adresse; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetTeilprojekt($value) { $this->teilprojekt=$value; }
  public function GetTeilprojekt() { return $this->teilprojekt; }
  public function SetAuftrag($value) { $this->auftrag=$value; }
  public function GetAuftrag() { return $this->auftrag; }
  public function SetStatus($value) { $this->status=$value; }
  public function GetStatus() { return $this->status; }
  public function SetBezahlt($value) { $this->bezahlt=$value; }
  public function GetBezahlt() { return $this->bezahlt; }
  public function SetKontoauszuege($value) { $this->kontoauszuege=$value; }
  public function GetKontoauszuege() { return $this->kontoauszuege; }
  public function SetFirma($value) { $this->firma=$value; }
  public function GetFirma() { return $this->firma; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetBestellung1($value) { $this->bestellung1=$value; }
  public function GetBestellung1() { return $this->bestellung1; }
  public function SetBestellung1Betrag($value) { $this->bestellung1betrag=$value; }
  public function GetBestellung1Betrag() { return $this->bestellung1betrag; }
  public function SetBestellung1Bemerkung($value) { $this->bestellung1bemerkung=$value; }
  public function GetBestellung1Bemerkung() { return $this->bestellung1bemerkung; }
  public function SetBestellung1Projekt($value) { $this->bestellung1projekt=$value; }
  public function GetBestellung1Projekt() { return $this->bestellung1projekt; }
  public function SetBestellung1Kostenstelle($value) { $this->bestellung1kostenstelle=$value; }
  public function GetBestellung1Kostenstelle() { return $this->bestellung1kostenstelle; }
  public function SetBestellung1Auftrag($value) { $this->bestellung1auftrag=$value; }
  public function GetBestellung1Auftrag() { return $this->bestellung1auftrag; }
  public function SetBestellung2($value) { $this->bestellung2=$value; }
  public function GetBestellung2() { return $this->bestellung2; }
  public function SetBestellung2Betrag($value) { $this->bestellung2betrag=$value; }
  public function GetBestellung2Betrag() { return $this->bestellung2betrag; }
  public function SetBestellung2Bemerkung($value) { $this->bestellung2bemerkung=$value; }
  public function GetBestellung2Bemerkung() { return $this->bestellung2bemerkung; }
  public function SetBestellung2Kostenstelle($value) { $this->bestellung2kostenstelle=$value; }
  public function GetBestellung2Kostenstelle() { return $this->bestellung2kostenstelle; }
  public function SetBestellung2Auftrag($value) { $this->bestellung2auftrag=$value; }
  public function GetBestellung2Auftrag() { return $this->bestellung2auftrag; }
  public function SetBestellung2Projekt($value) { $this->bestellung2projekt=$value; }
  public function GetBestellung2Projekt() { return $this->bestellung2projekt; }
  public function SetBestellung3($value) { $this->bestellung3=$value; }
  public function GetBestellung3() { return $this->bestellung3; }
  public function SetBestellung3Betrag($value) { $this->bestellung3betrag=$value; }
  public function GetBestellung3Betrag() { return $this->bestellung3betrag; }
  public function SetBestellung3Bemerkung($value) { $this->bestellung3bemerkung=$value; }
  public function GetBestellung3Bemerkung() { return $this->bestellung3bemerkung; }
  public function SetBestellung3Kostenstelle($value) { $this->bestellung3kostenstelle=$value; }
  public function GetBestellung3Kostenstelle() { return $this->bestellung3kostenstelle; }
  public function SetBestellung3Auftrag($value) { $this->bestellung3auftrag=$value; }
  public function GetBestellung3Auftrag() { return $this->bestellung3auftrag; }
  public function SetBestellung3Projekt($value) { $this->bestellung3projekt=$value; }
  public function GetBestellung3Projekt() { return $this->bestellung3projekt; }
  public function SetBestellung4($value) { $this->bestellung4=$value; }
  public function GetBestellung4() { return $this->bestellung4; }
  public function SetBestellung4Betrag($value) { $this->bestellung4betrag=$value; }
  public function GetBestellung4Betrag() { return $this->bestellung4betrag; }
  public function SetBestellung4Bemerkung($value) { $this->bestellung4bemerkung=$value; }
  public function GetBestellung4Bemerkung() { return $this->bestellung4bemerkung; }
  public function SetBestellung4Kostenstelle($value) { $this->bestellung4kostenstelle=$value; }
  public function GetBestellung4Kostenstelle() { return $this->bestellung4kostenstelle; }
  public function SetBestellung4Auftrag($value) { $this->bestellung4auftrag=$value; }
  public function GetBestellung4Auftrag() { return $this->bestellung4auftrag; }
  public function SetBestellung4Projekt($value) { $this->bestellung4projekt=$value; }
  public function GetBestellung4Projekt() { return $this->bestellung4projekt; }
  public function SetBestellung5($value) { $this->bestellung5=$value; }
  public function GetBestellung5() { return $this->bestellung5; }
  public function SetBestellung5Betrag($value) { $this->bestellung5betrag=$value; }
  public function GetBestellung5Betrag() { return $this->bestellung5betrag; }
  public function SetBestellung5Bemerkung($value) { $this->bestellung5bemerkung=$value; }
  public function GetBestellung5Bemerkung() { return $this->bestellung5bemerkung; }
  public function SetBestellung5Kostenstelle($value) { $this->bestellung5kostenstelle=$value; }
  public function GetBestellung5Kostenstelle() { return $this->bestellung5kostenstelle; }
  public function SetBestellung5Auftrag($value) { $this->bestellung5auftrag=$value; }
  public function GetBestellung5Auftrag() { return $this->bestellung5auftrag; }
  public function SetBestellung5Projekt($value) { $this->bestellung5projekt=$value; }
  public function GetBestellung5Projekt() { return $this->bestellung5projekt; }
  public function SetBestellung6($value) { $this->bestellung6=$value; }
  public function GetBestellung6() { return $this->bestellung6; }
  public function SetBestellung6Betrag($value) { $this->bestellung6betrag=$value; }
  public function GetBestellung6Betrag() { return $this->bestellung6betrag; }
  public function SetBestellung6Bemerkung($value) { $this->bestellung6bemerkung=$value; }
  public function GetBestellung6Bemerkung() { return $this->bestellung6bemerkung; }
  public function SetBestellung6Kostenstelle($value) { $this->bestellung6kostenstelle=$value; }
  public function GetBestellung6Kostenstelle() { return $this->bestellung6kostenstelle; }
  public function SetBestellung6Auftrag($value) { $this->bestellung6auftrag=$value; }
  public function GetBestellung6Auftrag() { return $this->bestellung6auftrag; }
  public function SetBestellung6Projekt($value) { $this->bestellung6projekt=$value; }
  public function GetBestellung6Projekt() { return $this->bestellung6projekt; }
  public function SetBestellung7($value) { $this->bestellung7=$value; }
  public function GetBestellung7() { return $this->bestellung7; }
  public function SetBestellung7Betrag($value) { $this->bestellung7betrag=$value; }
  public function GetBestellung7Betrag() { return $this->bestellung7betrag; }
  public function SetBestellung7Bemerkung($value) { $this->bestellung7bemerkung=$value; }
  public function GetBestellung7Bemerkung() { return $this->bestellung7bemerkung; }
  public function SetBestellung7Kostenstelle($value) { $this->bestellung7kostenstelle=$value; }
  public function GetBestellung7Kostenstelle() { return $this->bestellung7kostenstelle; }
  public function SetBestellung7Auftrag($value) { $this->bestellung7auftrag=$value; }
  public function GetBestellung7Auftrag() { return $this->bestellung7auftrag; }
  public function SetBestellung7Projekt($value) { $this->bestellung7projekt=$value; }
  public function GetBestellung7Projekt() { return $this->bestellung7projekt; }
  public function SetBestellung8($value) { $this->bestellung8=$value; }
  public function GetBestellung8() { return $this->bestellung8; }
  public function SetBestellung8Betrag($value) { $this->bestellung8betrag=$value; }
  public function GetBestellung8Betrag() { return $this->bestellung8betrag; }
  public function SetBestellung8Bemerkung($value) { $this->bestellung8bemerkung=$value; }
  public function GetBestellung8Bemerkung() { return $this->bestellung8bemerkung; }
  public function SetBestellung8Kostenstelle($value) { $this->bestellung8kostenstelle=$value; }
  public function GetBestellung8Kostenstelle() { return $this->bestellung8kostenstelle; }
  public function SetBestellung8Auftrag($value) { $this->bestellung8auftrag=$value; }
  public function GetBestellung8Auftrag() { return $this->bestellung8auftrag; }
  public function SetBestellung8Projekt($value) { $this->bestellung8projekt=$value; }
  public function GetBestellung8Projekt() { return $this->bestellung8projekt; }
  public function SetBestellung9($value) { $this->bestellung9=$value; }
  public function GetBestellung9() { return $this->bestellung9; }
  public function SetBestellung9Betrag($value) { $this->bestellung9betrag=$value; }
  public function GetBestellung9Betrag() { return $this->bestellung9betrag; }
  public function SetBestellung9Bemerkung($value) { $this->bestellung9bemerkung=$value; }
  public function GetBestellung9Bemerkung() { return $this->bestellung9bemerkung; }
  public function SetBestellung9Kostenstelle($value) { $this->bestellung9kostenstelle=$value; }
  public function GetBestellung9Kostenstelle() { return $this->bestellung9kostenstelle; }
  public function SetBestellung9Auftrag($value) { $this->bestellung9auftrag=$value; }
  public function GetBestellung9Auftrag() { return $this->bestellung9auftrag; }
  public function SetBestellung9Projekt($value) { $this->bestellung9projekt=$value; }
  public function GetBestellung9Projekt() { return $this->bestellung9projekt; }
  public function SetBestellung10($value) { $this->bestellung10=$value; }
  public function GetBestellung10() { return $this->bestellung10; }
  public function SetBestellung10Betrag($value) { $this->bestellung10betrag=$value; }
  public function GetBestellung10Betrag() { return $this->bestellung10betrag; }
  public function SetBestellung10Bemerkung($value) { $this->bestellung10bemerkung=$value; }
  public function GetBestellung10Bemerkung() { return $this->bestellung10bemerkung; }
  public function SetBestellung10Kostenstelle($value) { $this->bestellung10kostenstelle=$value; }
  public function GetBestellung10Kostenstelle() { return $this->bestellung10kostenstelle; }
  public function SetBestellung10Auftrag($value) { $this->bestellung10auftrag=$value; }
  public function GetBestellung10Auftrag() { return $this->bestellung10auftrag; }
  public function SetBestellung10Projekt($value) { $this->bestellung10projekt=$value; }
  public function GetBestellung10Projekt() { return $this->bestellung10projekt; }
  public function SetBestellung11($value) { $this->bestellung11=$value; }
  public function GetBestellung11() { return $this->bestellung11; }
  public function SetBestellung11Betrag($value) { $this->bestellung11betrag=$value; }
  public function GetBestellung11Betrag() { return $this->bestellung11betrag; }
  public function SetBestellung11Bemerkung($value) { $this->bestellung11bemerkung=$value; }
  public function GetBestellung11Bemerkung() { return $this->bestellung11bemerkung; }
  public function SetBestellung11Kostenstelle($value) { $this->bestellung11kostenstelle=$value; }
  public function GetBestellung11Kostenstelle() { return $this->bestellung11kostenstelle; }
  public function SetBestellung11Auftrag($value) { $this->bestellung11auftrag=$value; }
  public function GetBestellung11Auftrag() { return $this->bestellung11auftrag; }
  public function SetBestellung11Projekt($value) { $this->bestellung11projekt=$value; }
  public function GetBestellung11Projekt() { return $this->bestellung11projekt; }
  public function SetBestellung12($value) { $this->bestellung12=$value; }
  public function GetBestellung12() { return $this->bestellung12; }
  public function SetBestellung12Betrag($value) { $this->bestellung12betrag=$value; }
  public function GetBestellung12Betrag() { return $this->bestellung12betrag; }
  public function SetBestellung12Bemerkung($value) { $this->bestellung12bemerkung=$value; }
  public function GetBestellung12Bemerkung() { return $this->bestellung12bemerkung; }
  public function SetBestellung12Projekt($value) { $this->bestellung12projekt=$value; }
  public function GetBestellung12Projekt() { return $this->bestellung12projekt; }
  public function SetBestellung12Kostenstelle($value) { $this->bestellung12kostenstelle=$value; }
  public function GetBestellung12Kostenstelle() { return $this->bestellung12kostenstelle; }
  public function SetBestellung12Auftrag($value) { $this->bestellung12auftrag=$value; }
  public function GetBestellung12Auftrag() { return $this->bestellung12auftrag; }
  public function SetBestellung13($value) { $this->bestellung13=$value; }
  public function GetBestellung13() { return $this->bestellung13; }
  public function SetBestellung13Betrag($value) { $this->bestellung13betrag=$value; }
  public function GetBestellung13Betrag() { return $this->bestellung13betrag; }
  public function SetBestellung13Bemerkung($value) { $this->bestellung13bemerkung=$value; }
  public function GetBestellung13Bemerkung() { return $this->bestellung13bemerkung; }
  public function SetBestellung13Kostenstelle($value) { $this->bestellung13kostenstelle=$value; }
  public function GetBestellung13Kostenstelle() { return $this->bestellung13kostenstelle; }
  public function SetBestellung13Auftrag($value) { $this->bestellung13auftrag=$value; }
  public function GetBestellung13Auftrag() { return $this->bestellung13auftrag; }
  public function SetBestellung13Projekt($value) { $this->bestellung13projekt=$value; }
  public function GetBestellung13Projekt() { return $this->bestellung13projekt; }
  public function SetBestellung14($value) { $this->bestellung14=$value; }
  public function GetBestellung14() { return $this->bestellung14; }
  public function SetBestellung14Betrag($value) { $this->bestellung14betrag=$value; }
  public function GetBestellung14Betrag() { return $this->bestellung14betrag; }
  public function SetBestellung14Bemerkung($value) { $this->bestellung14bemerkung=$value; }
  public function GetBestellung14Bemerkung() { return $this->bestellung14bemerkung; }
  public function SetBestellung14Kostenstelle($value) { $this->bestellung14kostenstelle=$value; }
  public function GetBestellung14Kostenstelle() { return $this->bestellung14kostenstelle; }
  public function SetBestellung14Auftrag($value) { $this->bestellung14auftrag=$value; }
  public function GetBestellung14Auftrag() { return $this->bestellung14auftrag; }
  public function SetBestellung14Projekt($value) { $this->bestellung14projekt=$value; }
  public function GetBestellung14Projekt() { return $this->bestellung14projekt; }
  public function SetBestellung15($value) { $this->bestellung15=$value; }
  public function GetBestellung15() { return $this->bestellung15; }
  public function SetBestellung15Betrag($value) { $this->bestellung15betrag=$value; }
  public function GetBestellung15Betrag() { return $this->bestellung15betrag; }
  public function SetBestellung15Bemerkung($value) { $this->bestellung15bemerkung=$value; }
  public function GetBestellung15Bemerkung() { return $this->bestellung15bemerkung; }
  public function SetBestellung15Kostenstelle($value) { $this->bestellung15kostenstelle=$value; }
  public function GetBestellung15Kostenstelle() { return $this->bestellung15kostenstelle; }
  public function SetBestellung15Auftrag($value) { $this->bestellung15auftrag=$value; }
  public function GetBestellung15Auftrag() { return $this->bestellung15auftrag; }
  public function SetBestellung15Projekt($value) { $this->bestellung15projekt=$value; }
  public function GetBestellung15Projekt() { return $this->bestellung15projekt; }
  public function SetWaehrung($value) { $this->waehrung=$value; }
  public function GetWaehrung() { return $this->waehrung; }
  public function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  public function GetZahlungsweise() { return $this->zahlungsweise; }
  public function SetEingangsdatum($value) { $this->eingangsdatum=$value; }
  public function GetEingangsdatum() { return $this->eingangsdatum; }
  public function SetBuha_Konto1($value) { $this->buha_konto1=$value; }
  public function GetBuha_Konto1() { return $this->buha_konto1; }
  public function SetBuha_Belegfeld1($value) { $this->buha_belegfeld1=$value; }
  public function GetBuha_Belegfeld1() { return $this->buha_belegfeld1; }
  public function SetBuha_Betrag1($value) { $this->buha_betrag1=$value; }
  public function GetBuha_Betrag1() { return $this->buha_betrag1; }
  public function SetBuha_Konto2($value) { $this->buha_konto2=$value; }
  public function GetBuha_Konto2() { return $this->buha_konto2; }
  public function SetBuha_Belegfeld2($value) { $this->buha_belegfeld2=$value; }
  public function GetBuha_Belegfeld2() { return $this->buha_belegfeld2; }
  public function SetBuha_Betrag2($value) { $this->buha_betrag2=$value; }
  public function GetBuha_Betrag2() { return $this->buha_betrag2; }
  public function SetBuha_Konto3($value) { $this->buha_konto3=$value; }
  public function GetBuha_Konto3() { return $this->buha_konto3; }
  public function SetBuha_Belegfeld3($value) { $this->buha_belegfeld3=$value; }
  public function GetBuha_Belegfeld3() { return $this->buha_belegfeld3; }
  public function SetBuha_Betrag3($value) { $this->buha_betrag3=$value; }
  public function GetBuha_Betrag3() { return $this->buha_betrag3; }
  public function SetBuha_Konto4($value) { $this->buha_konto4=$value; }
  public function GetBuha_Konto4() { return $this->buha_konto4; }
  public function SetBuha_Belegfeld4($value) { $this->buha_belegfeld4=$value; }
  public function GetBuha_Belegfeld4() { return $this->buha_belegfeld4; }
  public function SetBuha_Betrag4($value) { $this->buha_betrag4=$value; }
  public function GetBuha_Betrag4() { return $this->buha_betrag4; }
  public function SetBuha_Konto5($value) { $this->buha_konto5=$value; }
  public function GetBuha_Konto5() { return $this->buha_konto5; }
  public function SetBuha_Belegfeld5($value) { $this->buha_belegfeld5=$value; }
  public function GetBuha_Belegfeld5() { return $this->buha_belegfeld5; }
  public function SetBuha_Betrag5($value) { $this->buha_betrag5=$value; }
  public function GetBuha_Betrag5() { return $this->buha_betrag5; }
  public function SetRechnungsdatum($value) { $this->rechnungsdatum=$value; }
  public function GetRechnungsdatum() { return $this->rechnungsdatum; }
  public function SetRechnungsfreigabe($value) { $this->rechnungsfreigabe=$value; }
  public function GetRechnungsfreigabe() { return $this->rechnungsfreigabe; }
  public function SetKostenstelle($value) { $this->kostenstelle=$value; }
  public function GetKostenstelle() { return $this->kostenstelle; }
  public function SetBeschreibung($value) { $this->beschreibung=$value; }
  public function GetBeschreibung() { return $this->beschreibung; }
  public function SetSachkonto($value) { $this->sachkonto=$value; }
  public function GetSachkonto() { return $this->sachkonto; }
  public function SetArt($value) { $this->art=$value; }
  public function GetArt() { return $this->art; }
  public function SetVerwendungszweck($value) { $this->verwendungszweck=$value; }
  public function GetVerwendungszweck() { return $this->verwendungszweck; }
  public function SetDta_Datei($value) { $this->dta_datei=$value; }
  public function GetDta_Datei() { return $this->dta_datei; }
  public function SetFrachtkosten($value) { $this->frachtkosten=$value; }
  public function GetFrachtkosten() { return $this->frachtkosten; }
  public function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  public function GetInternebemerkung() { return $this->internebemerkung; }
  public function SetUstnormal($value) { $this->ustnormal=$value; }
  public function GetUstnormal() { return $this->ustnormal; }
  public function SetUstermaessigt($value) { $this->ustermaessigt=$value; }
  public function GetUstermaessigt() { return $this->ustermaessigt; }
  public function SetUststuer3($value) { $this->uststuer3=$value; }
  public function GetUststuer3() { return $this->uststuer3; }
  public function SetUststuer4($value) { $this->uststuer4=$value; }
  public function GetUststuer4() { return $this->uststuer4; }
  public function SetBetragbezahlt($value) { $this->betragbezahlt=$value; }
  public function GetBetragbezahlt() { return $this->betragbezahlt; }
  public function SetBezahltam($value) { $this->bezahltam=$value; }
  public function GetBezahltam() { return $this->bezahltam; }
  public function SetKlaerfall($value) { $this->klaerfall=$value; }
  public function GetKlaerfall() { return $this->klaerfall; }
  public function SetKlaergrund($value) { $this->klaergrund=$value; }
  public function GetKlaergrund() { return $this->klaergrund; }
  public function SetKurs($value) { $this->kurs=$value; }
  public function GetKurs() { return $this->kurs; }
  public function SetSkonto_Erhalten($value) { $this->skonto_erhalten=$value; }
  public function GetSkonto_Erhalten() { return $this->skonto_erhalten; }

}
