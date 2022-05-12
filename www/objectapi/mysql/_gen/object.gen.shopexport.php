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

class ObjGenShopexport
{

  private  $id;
  private  $bezeichnung;
  private  $typ;
  private  $url;
  private  $passwort;
  private  $token;
  private  $challenge;
  private  $projekt;
  private  $cms;
  private  $firma;
  private  $logdatei;
  private  $geloescht;
  private  $artikelporto;
  private  $artikelnachnahme;
  private  $artikelimport;
  private  $artikelimporteinzeln;
  private  $demomodus;
  private  $aktiv;
  private  $lagerexport;
  private  $artikelexport;
  private  $multiprojekt;
  private  $artikelnachnahme_extraartikel;
  private  $vorabbezahltmarkieren_ohnevorkasse_bar;
  private  $einzelsync;
  private  $utf8codierung;
  private  $auftragabgleich;
  private  $rabatteportofestschreiben;
  private  $artikelnummernummerkreis;
  private  $holealle;
  private  $ab_nummer;
  private  $direktimport;
  private  $ust_ok;
  private  $anzgleichzeitig;
  private  $datumvon;
  private  $datumbis;
  private  $tmpdatumvon;
  private  $tmpdatumbis;
  private  $holeallestati;
  private  $cronjobaktiv;
  private  $nummersyncstatusaendern;
  private  $zahlungsweisenmapping;
  private  $versandartenmapping;
  private  $artikelnummeruebernehmen;
  private  $artikelbeschreibungauswawision;
  private  $artikelbeschreibungenuebernehmen;
  private  $stuecklisteergaenzen;
  private  $adressupdate;
  private  $kundenurvonprojekt;
  private  $add_debitorennummer;
  private  $debitorennummer;
  private  $sendonlywithtracking;
  private  $shopbilderuebertragen;
  private  $adressennichtueberschreiben;
  private  $auftraegeaufspaeter;
  private  $autoversandbeikommentardeaktivieren;
  private  $artikeltexteuebernehmen;
  private  $artikelportoermaessigt;
  private  $artikelrabatt;
  private  $artikelrabattsteuer;
  private  $positionsteuersaetzeerlauben;
  private  $json;
  private  $freitext;
  private  $artikelbezeichnungauswawision;
  private  $angeboteanlegen;
  private  $artikelnummerbeimanlegenausshop;
  private  $shoptyp;
  private  $modulename;
  private  $maxmanuell;
  private  $preisgruppe;
  private  $variantenuebertragen;
  private  $crosssellingartikeluebertragen;
  private  $staffelpreiseuebertragen;
  private  $lagergrundlage;
  private  $portoartikelanlegen;
  private  $nurneueartikel;
  private  $startdate;
  private  $ueberschreibe_lagerkorrekturwert;
  private  $lagerkorrekturwert;
  private  $vertrieb;
  private  $eigenschaftenuebertragen;
  private  $kategorienuebertragen;
  private  $stornoabgleich;
  private  $nurpreise;
  private  $steuerfreilieferlandexport;
  private  $gutscheineuebertragen;
  private  $gesamtbetragfestsetzen;
  private  $lastschriftdatenueberschreiben;
  private  $gesamtbetragfestsetzendifferenz;
  private  $api_account_id;
  private  $api_account_token;
  private  $autoversandoption;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM `shopexport` WHERE (`id` = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->typ=$result['typ'];
    $this->url=$result['url'];
    $this->passwort=$result['passwort'];
    $this->token=$result['token'];
    $this->challenge=$result['challenge'];
    $this->projekt=$result['projekt'];
    $this->cms=$result['cms'];
    $this->firma=$result['firma'];
    $this->logdatei=$result['logdatei'];
    $this->geloescht=$result['geloescht'];
    $this->artikelporto=$result['artikelporto'];
    $this->artikelnachnahme=$result['artikelnachnahme'];
    $this->artikelimport=$result['artikelimport'];
    $this->artikelimporteinzeln=$result['artikelimporteinzeln'];
    $this->demomodus=$result['demomodus'];
    $this->aktiv=$result['aktiv'];
    $this->lagerexport=$result['lagerexport'];
    $this->artikelexport=$result['artikelexport'];
    $this->multiprojekt=$result['multiprojekt'];
    $this->artikelnachnahme_extraartikel=$result['artikelnachnahme_extraartikel'];
    $this->vorabbezahltmarkieren_ohnevorkasse_bar=$result['vorabbezahltmarkieren_ohnevorkasse_bar'];
    $this->einzelsync=$result['einzelsync'];
    $this->utf8codierung=$result['utf8codierung'];
    $this->auftragabgleich=$result['auftragabgleich'];
    $this->rabatteportofestschreiben=$result['rabatteportofestschreiben'];
    $this->artikelnummernummerkreis=$result['artikelnummernummerkreis'];
    $this->holealle=$result['holealle'];
    $this->ab_nummer=$result['ab_nummer'];
    $this->direktimport=$result['direktimport'];
    $this->ust_ok=$result['ust_ok'];
    $this->anzgleichzeitig=$result['anzgleichzeitig'];
    $this->datumvon=$result['datumvon'];
    $this->datumbis=$result['datumbis'];
    $this->tmpdatumvon=$result['tmpdatumvon'];
    $this->tmpdatumbis=$result['tmpdatumbis'];
    $this->holeallestati=$result['holeallestati'];
    $this->cronjobaktiv=$result['cronjobaktiv'];
    $this->nummersyncstatusaendern=$result['nummersyncstatusaendern'];
    $this->zahlungsweisenmapping=$result['zahlungsweisenmapping'];
    $this->versandartenmapping=$result['versandartenmapping'];
    $this->artikelnummeruebernehmen=$result['artikelnummeruebernehmen'];
    $this->artikelbeschreibungauswawision=$result['artikelbeschreibungauswawision'];
    $this->artikelbeschreibungenuebernehmen=$result['artikelbeschreibungenuebernehmen'];
    $this->stuecklisteergaenzen=$result['stuecklisteergaenzen'];
    $this->adressupdate=$result['adressupdate'];
    $this->kundenurvonprojekt=$result['kundenurvonprojekt'];
    $this->add_debitorennummer=$result['add_debitorennummer'];
    $this->debitorennummer=$result['debitorennummer'];
    $this->sendonlywithtracking=$result['sendonlywithtracking'];
    $this->shopbilderuebertragen=$result['shopbilderuebertragen'];
    $this->adressennichtueberschreiben=$result['adressennichtueberschreiben'];
    $this->auftraegeaufspaeter=$result['auftraegeaufspaeter'];
    $this->autoversandbeikommentardeaktivieren=$result['autoversandbeikommentardeaktivieren'];
    $this->artikeltexteuebernehmen=$result['artikeltexteuebernehmen'];
    $this->artikelportoermaessigt=$result['artikelportoermaessigt'];
    $this->artikelrabatt=$result['artikelrabatt'];
    $this->artikelrabattsteuer=$result['artikelrabattsteuer'];
    $this->positionsteuersaetzeerlauben=$result['positionsteuersaetzeerlauben'];
    $this->json=$result['json'];
    $this->freitext=$result['freitext'];
    $this->artikelbezeichnungauswawision=$result['artikelbezeichnungauswawision'];
    $this->angeboteanlegen=$result['angeboteanlegen'];
    $this->artikelnummerbeimanlegenausshop=$result['artikelnummerbeimanlegenausshop'];
    $this->shoptyp=$result['shoptyp'];
    $this->modulename=$result['modulename'];
    $this->maxmanuell=$result['maxmanuell'];
    $this->preisgruppe=$result['preisgruppe'];
    $this->variantenuebertragen=$result['variantenuebertragen'];
    $this->crosssellingartikeluebertragen=$result['crosssellingartikeluebertragen'];
    $this->staffelpreiseuebertragen=$result['staffelpreiseuebertragen'];
    $this->lagergrundlage=$result['lagergrundlage'];
    $this->portoartikelanlegen=$result['portoartikelanlegen'];
    $this->nurneueartikel=$result['nurneueartikel'];
    $this->startdate=$result['startdate'];
    $this->ueberschreibe_lagerkorrekturwert=$result['ueberschreibe_lagerkorrekturwert'];
    $this->lagerkorrekturwert=$result['lagerkorrekturwert'];
    $this->vertrieb=$result['vertrieb'];
    $this->eigenschaftenuebertragen=$result['eigenschaftenuebertragen'];
    $this->kategorienuebertragen=$result['kategorienuebertragen'];
    $this->stornoabgleich=$result['stornoabgleich'];
    $this->nurpreise=$result['nurpreise'];
    $this->steuerfreilieferlandexport=$result['steuerfreilieferlandexport'];
    $this->gutscheineuebertragen=$result['gutscheineuebertragen'];
    $this->gesamtbetragfestsetzen=$result['gesamtbetragfestsetzen'];
    $this->lastschriftdatenueberschreiben=$result['lastschriftdatenueberschreiben'];
    $this->gesamtbetragfestsetzendifferenz=$result['gesamtbetragfestsetzendifferenz'];
    $this->api_account_id=$result['api_account_id'];
    $this->api_account_token=$result['api_account_token'];
    $this->autoversandoption=$result['autoversandoption'];
    $this->autosendarticle=$result['autosendarticle'];
  }

  public function Create()
  {
    $sql = "INSERT INTO `shopexport` (`id`,`bezeichnung`,`typ`,`url`,`passwort`,`token`,`challenge`,`projekt`,`cms`,`firma`,`logdatei`,`geloescht`,`artikelporto`,`artikelnachnahme`,`artikelimport`,`artikelimporteinzeln`,`demomodus`,`aktiv`,`lagerexport`,`artikelexport`,`multiprojekt`,`artikelnachnahme_extraartikel`,`vorabbezahltmarkieren_ohnevorkasse_bar`,`einzelsync`,`utf8codierung`,`auftragabgleich`,`rabatteportofestschreiben`,`artikelnummernummerkreis`,`holealle`,`ab_nummer`,`direktimport`,`ust_ok`,`anzgleichzeitig`,`datumvon`,`datumbis`,`tmpdatumvon`,`tmpdatumbis`,`holeallestati`,`cronjobaktiv`,`nummersyncstatusaendern`,`zahlungsweisenmapping`,`versandartenmapping`,`artikelnummeruebernehmen`,`artikelbeschreibungauswawision`,`artikelbeschreibungenuebernehmen`,`stuecklisteergaenzen`,`adressupdate`,`kundenurvonprojekt`,`add_debitorennummer`,`debitorennummer`,`sendonlywithtracking`,`shopbilderuebertragen`,`adressennichtueberschreiben`,`auftraegeaufspaeter`,`autoversandbeikommentardeaktivieren`,`artikeltexteuebernehmen`,`artikelportoermaessigt`,`artikelrabatt`,`artikelrabattsteuer`,`positionsteuersaetzeerlauben`,`json`,`freitext`,`artikelbezeichnungauswawision`,`angeboteanlegen`,`artikelnummerbeimanlegenausshop`,`shoptyp`,`modulename`,`maxmanuell`,`preisgruppe`,`variantenuebertragen`,`crosssellingartikeluebertragen`,`staffelpreiseuebertragen`,`lagergrundlage`,`portoartikelanlegen`,`nurneueartikel`,`startdate`,`ueberschreibe_lagerkorrekturwert`,`lagerkorrekturwert`,`vertrieb`,`eigenschaftenuebertragen`,`kategorienuebertragen`,`stornoabgleich`,`nurpreise`,`steuerfreilieferlandexport`,`gutscheineuebertragen`,`gesamtbetragfestsetzen`,`lastschriftdatenueberschreiben`,`gesamtbetragfestsetzendifferenz`,`api_account_id`,`api_account_token`,`autoversandoption`,`autosendarticle`)
      VALUES(NULL,'{$this->bezeichnung}','{$this->typ}','{$this->url}','{$this->passwort}','{$this->token}','{$this->challenge}','{$this->projekt}','{$this->cms}','{$this->firma}','{$this->logdatei}','{$this->geloescht}','{$this->artikelporto}','{$this->artikelnachnahme}','{$this->artikelimport}','{$this->artikelimporteinzeln}','{$this->demomodus}','{$this->aktiv}','{$this->lagerexport}','{$this->artikelexport}','{$this->multiprojekt}','{$this->artikelnachnahme_extraartikel}','{$this->vorabbezahltmarkieren_ohnevorkasse_bar}','{$this->einzelsync}','{$this->utf8codierung}','{$this->auftragabgleich}','{$this->rabatteportofestschreiben}','{$this->artikelnummernummerkreis}','{$this->holealle}','{$this->ab_nummer}','{$this->direktimport}','{$this->ust_ok}','{$this->anzgleichzeitig}','{$this->datumvon}','{$this->datumbis}','{$this->tmpdatumvon}','{$this->tmpdatumbis}','{$this->holeallestati}','{$this->cronjobaktiv}','{$this->nummersyncstatusaendern}','{$this->zahlungsweisenmapping}','{$this->versandartenmapping}','{$this->artikelnummeruebernehmen}','{$this->artikelbeschreibungauswawision}','{$this->artikelbeschreibungenuebernehmen}','{$this->stuecklisteergaenzen}','{$this->adressupdate}','{$this->kundenurvonprojekt}','{$this->add_debitorennummer}','{$this->debitorennummer}','{$this->sendonlywithtracking}','{$this->shopbilderuebertragen}','{$this->adressennichtueberschreiben}','{$this->auftraegeaufspaeter}','{$this->autoversandbeikommentardeaktivieren}','{$this->artikeltexteuebernehmen}','{$this->artikelportoermaessigt}','{$this->artikelrabatt}','{$this->artikelrabattsteuer}','{$this->positionsteuersaetzeerlauben}','{$this->json}','{$this->freitext}','{$this->artikelbezeichnungauswawision}','{$this->angeboteanlegen}','{$this->artikelnummerbeimanlegenausshop}','{$this->shoptyp}','{$this->modulename}','{$this->maxmanuell}','{$this->preisgruppe}','{$this->variantenuebertragen}','{$this->crosssellingartikeluebertragen}','{$this->staffelpreiseuebertragen}','{$this->lagergrundlage}','{$this->portoartikelanlegen}','{$this->nurneueartikel}','{$this->startdate}','{$this->ueberschreibe_lagerkorrekturwert}','{$this->lagerkorrekturwert}','{$this->vertrieb}','{$this->eigenschaftenuebertragen}','{$this->kategorienuebertragen}','{$this->stornoabgleich}','{$this->nurpreise}','{$this->steuerfreilieferlandexport}','{$this->gutscheineuebertragen}','{$this->gesamtbetragfestsetzen}','{$this->lastschriftdatenueberschreiben}','{$this->gesamtbetragfestsetzendifferenz}','{$this->api_account_id}','{$this->api_account_token}','{$this->autoversandoption}','{$this->autosendarticle}')";

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id)) {
      return -1;
    }

    $sql = "UPDATE `shopexport` SET
      `bezeichnung`='{$this->bezeichnung}',
      `typ`='{$this->typ}',
      `url`='{$this->url}',
      `passwort`='{$this->passwort}',
      `token`='{$this->token}',
      `challenge`='{$this->challenge}',
      `projekt`='{$this->projekt}',
      `cms`='{$this->cms}',
      `firma`='{$this->firma}',
      `logdatei`='{$this->logdatei}',
      `geloescht`='{$this->geloescht}',
      `artikelporto`='{$this->artikelporto}',
      `artikelnachnahme`='{$this->artikelnachnahme}',
      `artikelimport`='{$this->artikelimport}',
      `artikelimporteinzeln`='{$this->artikelimporteinzeln}',
      `demomodus`='{$this->demomodus}',
      `aktiv`='{$this->aktiv}',
      `lagerexport`='{$this->lagerexport}',
      `artikelexport`='{$this->artikelexport}',
      `multiprojekt`='{$this->multiprojekt}',
      `artikelnachnahme_extraartikel`='{$this->artikelnachnahme_extraartikel}',
      `vorabbezahltmarkieren_ohnevorkasse_bar`='{$this->vorabbezahltmarkieren_ohnevorkasse_bar}',
      `einzelsync`='{$this->einzelsync}',
      `utf8codierung`='{$this->utf8codierung}',
      `auftragabgleich`='{$this->auftragabgleich}',
      `rabatteportofestschreiben`='{$this->rabatteportofestschreiben}',
      `artikelnummernummerkreis`='{$this->artikelnummernummerkreis}',
      `holealle`='{$this->holealle}',
      `ab_nummer`='{$this->ab_nummer}',
      `direktimport`='{$this->direktimport}',
      `ust_ok`='{$this->ust_ok}',
      `anzgleichzeitig`='{$this->anzgleichzeitig}',
      `datumvon`='{$this->datumvon}',
      `datumbis`='{$this->datumbis}',
      `tmpdatumvon`='{$this->tmpdatumvon}',
      `tmpdatumbis`='{$this->tmpdatumbis}',
      `holeallestati`='{$this->holeallestati}',
      `cronjobaktiv`='{$this->cronjobaktiv}',
      `nummersyncstatusaendern`='{$this->nummersyncstatusaendern}',
      `zahlungsweisenmapping`='{$this->zahlungsweisenmapping}',
      `versandartenmapping`='{$this->versandartenmapping}',
      `artikelnummeruebernehmen`='{$this->artikelnummeruebernehmen}',
      `artikelbeschreibungauswawision`='{$this->artikelbeschreibungauswawision}',
      `artikelbeschreibungenuebernehmen`='{$this->artikelbeschreibungenuebernehmen}',
      `stuecklisteergaenzen`='{$this->stuecklisteergaenzen}',
      `adressupdate`='{$this->adressupdate}',
      `kundenurvonprojekt`='{$this->kundenurvonprojekt}',
      `add_debitorennummer`='{$this->add_debitorennummer}',
      `debitorennummer`='{$this->debitorennummer}',
      `sendonlywithtracking`='{$this->sendonlywithtracking}',
      `shopbilderuebertragen`='{$this->shopbilderuebertragen}',
      `adressennichtueberschreiben`='{$this->adressennichtueberschreiben}',
      `auftraegeaufspaeter`='{$this->auftraegeaufspaeter}',
      `autoversandbeikommentardeaktivieren`='{$this->autoversandbeikommentardeaktivieren}',
      `artikeltexteuebernehmen`='{$this->artikeltexteuebernehmen}',
      `artikelportoermaessigt`='{$this->artikelportoermaessigt}',
      `artikelrabatt`='{$this->artikelrabatt}',
      `artikelrabattsteuer`='{$this->artikelrabattsteuer}',
      `positionsteuersaetzeerlauben`='{$this->positionsteuersaetzeerlauben}',
      `json`='{$this->json}',
      `freitext`='{$this->freitext}',
      `artikelbezeichnungauswawision`='{$this->artikelbezeichnungauswawision}',
      `angeboteanlegen`='{$this->angeboteanlegen}',
      `artikelnummerbeimanlegenausshop`='{$this->artikelnummerbeimanlegenausshop}',
      `shoptyp`='{$this->shoptyp}',
      `modulename`='{$this->modulename}',
      `maxmanuell`='{$this->maxmanuell}',
      `preisgruppe`='{$this->preisgruppe}',
      `variantenuebertragen`='{$this->variantenuebertragen}',
      `crosssellingartikeluebertragen`='{$this->crosssellingartikeluebertragen}',
      `staffelpreiseuebertragen`='{$this->staffelpreiseuebertragen}',
      `lagergrundlage`='{$this->lagergrundlage}',
      `portoartikelanlegen`='{$this->portoartikelanlegen}',
      `nurneueartikel`='{$this->nurneueartikel}',
      `startdate`='{$this->startdate}',
      `ueberschreibe_lagerkorrekturwert`='{$this->ueberschreibe_lagerkorrekturwert}',
      `lagerkorrekturwert`='{$this->lagerkorrekturwert}',
      `vertrieb`='{$this->vertrieb}',
      `eigenschaftenuebertragen`='{$this->eigenschaftenuebertragen}',
      `kategorienuebertragen`='{$this->kategorienuebertragen}',
      `stornoabgleich`='{$this->stornoabgleich}',
      `nurpreise`='{$this->nurpreise}',
      `steuerfreilieferlandexport`='{$this->steuerfreilieferlandexport}',
      `gutscheineuebertragen`='{$this->gutscheineuebertragen}',
      `gesamtbetragfestsetzen`='{$this->gesamtbetragfestsetzen}',
      `lastschriftdatenueberschreiben`='{$this->lastschriftdatenueberschreiben}',
      `gesamtbetragfestsetzendifferenz`='{$this->gesamtbetragfestsetzendifferenz}',
      `api_account_id`='{$this->api_account_id}',
      `api_account_token`='{$this->api_account_token}',
      `autoversandoption`='{$this->autoversandoption}',
      `autosendarticle`='{$this->autosendarticle}'
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

    $sql = "DELETE FROM `shopexport` WHERE (`id`='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id='';
    $this->bezeichnung='';
    $this->typ='';
    $this->url='';
    $this->passwort='';
    $this->token='';
    $this->challenge='';
    $this->projekt='';
    $this->cms='';
    $this->firma='';
    $this->logdatei='';
    $this->geloescht='';
    $this->artikelporto='';
    $this->artikelnachnahme='';
    $this->artikelimport='';
    $this->artikelimporteinzeln='';
    $this->demomodus='';
    $this->aktiv='';
    $this->lagerexport='';
    $this->artikelexport='';
    $this->multiprojekt='';
    $this->artikelnachnahme_extraartikel='';
    $this->vorabbezahltmarkieren_ohnevorkasse_bar='';
    $this->einzelsync='';
    $this->utf8codierung='';
    $this->auftragabgleich='';
    $this->rabatteportofestschreiben='';
    $this->artikelnummernummerkreis='';
    $this->holealle='';
    $this->ab_nummer='';
    $this->direktimport='';
    $this->ust_ok='';
    $this->anzgleichzeitig='';
    $this->datumvon='';
    $this->datumbis='';
    $this->tmpdatumvon='';
    $this->tmpdatumbis='';
    $this->holeallestati='';
    $this->cronjobaktiv='';
    $this->nummersyncstatusaendern='';
    $this->zahlungsweisenmapping='';
    $this->versandartenmapping='';
    $this->artikelnummeruebernehmen='';
    $this->artikelbeschreibungauswawision='';
    $this->artikelbeschreibungenuebernehmen='';
    $this->stuecklisteergaenzen='';
    $this->adressupdate='';
    $this->kundenurvonprojekt='';
    $this->add_debitorennummer='';
    $this->debitorennummer='';
    $this->sendonlywithtracking='';
    $this->shopbilderuebertragen='';
    $this->adressennichtueberschreiben='';
    $this->auftraegeaufspaeter='';
    $this->autoversandbeikommentardeaktivieren='';
    $this->artikeltexteuebernehmen='';
    $this->artikelportoermaessigt='';
    $this->artikelrabatt='';
    $this->artikelrabattsteuer='';
    $this->positionsteuersaetzeerlauben='';
    $this->json='';
    $this->freitext='';
    $this->artikelbezeichnungauswawision='';
    $this->angeboteanlegen='';
    $this->artikelnummerbeimanlegenausshop='';
    $this->shoptyp='';
    $this->modulename='';
    $this->maxmanuell='';
    $this->preisgruppe='';
    $this->variantenuebertragen='';
    $this->crosssellingartikeluebertragen='';
    $this->staffelpreiseuebertragen='';
    $this->lagergrundlage='';
    $this->portoartikelanlegen='';
    $this->nurneueartikel='';
    $this->startdate='';
    $this->ueberschreibe_lagerkorrekturwert='';
    $this->lagerkorrekturwert='';
    $this->vertrieb='';
    $this->eigenschaftenuebertragen='';
    $this->kategorienuebertragen='';
    $this->stornoabgleich='';
    $this->nurpreise='';
    $this->steuerfreilieferlandexport='';
    $this->gutscheineuebertragen='';
    $this->gesamtbetragfestsetzen='';
    $this->lastschriftdatenueberschreiben='';
    $this->gesamtbetragfestsetzendifferenz='';
    $this->api_account_id='';
    $this->api_account_token='';
    $this->autoversandoption='';
    $this->autosendarticle='';
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
  public function SetBezeichnung($value) { $this->bezeichnung=$value; }
  public function GetBezeichnung() { return $this->bezeichnung; }
  public function SetTyp($value) { $this->typ=$value; }
  public function GetTyp() { return $this->typ; }
  public function SetUrl($value) { $this->url=$value; }
  public function GetUrl() { return $this->url; }
  public function SetPasswort($value) { $this->passwort=$value; }
  public function GetPasswort() { return $this->passwort; }
  public function SetToken($value) { $this->token=$value; }
  public function GetToken() { return $this->token; }
  public function SetChallenge($value) { $this->challenge=$value; }
  public function GetChallenge() { return $this->challenge; }
  public function SetProjekt($value) { $this->projekt=$value; }
  public function GetProjekt() { return $this->projekt; }
  public function SetCms($value) { $this->cms=$value; }
  public function GetCms() { return $this->cms; }
  public function SetFirma($value) { $this->firma=$value; }
  public function GetFirma() { return $this->firma; }
  public function SetLogdatei($value) { $this->logdatei=$value; }
  public function GetLogdatei() { return $this->logdatei; }
  public function SetGeloescht($value) { $this->geloescht=$value; }
  public function GetGeloescht() { return $this->geloescht; }
  public function SetArtikelporto($value) { $this->artikelporto=$value; }
  public function GetArtikelporto() { return $this->artikelporto; }
  public function SetArtikelnachnahme($value) { $this->artikelnachnahme=$value; }
  public function GetArtikelnachnahme() { return $this->artikelnachnahme; }
  public function SetArtikelimport($value) { $this->artikelimport=$value; }
  public function GetArtikelimport() { return $this->artikelimport; }
  public function SetArtikelimporteinzeln($value) { $this->artikelimporteinzeln=$value; }
  public function GetArtikelimporteinzeln() { return $this->artikelimporteinzeln; }
  public function SetDemomodus($value) { $this->demomodus=$value; }
  public function GetDemomodus() { return $this->demomodus; }
  public function SetAktiv($value) { $this->aktiv=$value; }
  public function GetAktiv() { return $this->aktiv; }
  public function SetLagerexport($value) { $this->lagerexport=$value; }
  public function GetLagerexport() { return $this->lagerexport; }
  public function SetArtikelexport($value) { $this->artikelexport=$value; }
  public function GetArtikelexport() { return $this->artikelexport; }
  public function SetMultiprojekt($value) { $this->multiprojekt=$value; }
  public function GetMultiprojekt() { return $this->multiprojekt; }
  public function SetArtikelnachnahme_Extraartikel($value) { $this->artikelnachnahme_extraartikel=$value; }
  public function GetArtikelnachnahme_Extraartikel() { return $this->artikelnachnahme_extraartikel; }
  public function SetVorabbezahltmarkieren_Ohnevorkasse_Bar($value) { $this->vorabbezahltmarkieren_ohnevorkasse_bar=$value; }
  public function GetVorabbezahltmarkieren_Ohnevorkasse_Bar() { return $this->vorabbezahltmarkieren_ohnevorkasse_bar; }
  public function SetEinzelsync($value) { $this->einzelsync=$value; }
  public function GetEinzelsync() { return $this->einzelsync; }
  public function SetUtf8Codierung($value) { $this->utf8codierung=$value; }
  public function GetUtf8Codierung() { return $this->utf8codierung; }
  public function SetAuftragabgleich($value) { $this->auftragabgleich=$value; }
  public function GetAuftragabgleich() { return $this->auftragabgleich; }
  public function SetRabatteportofestschreiben($value) { $this->rabatteportofestschreiben=$value; }
  public function GetRabatteportofestschreiben() { return $this->rabatteportofestschreiben; }
  public function SetArtikelnummernummerkreis($value) { $this->artikelnummernummerkreis=$value; }
  public function GetArtikelnummernummerkreis() { return $this->artikelnummernummerkreis; }
  public function SetHolealle($value) { $this->holealle=$value; }
  public function GetHolealle() { return $this->holealle; }
  public function SetAb_Nummer($value) { $this->ab_nummer=$value; }
  public function GetAb_Nummer() { return $this->ab_nummer; }
  public function SetDirektimport($value) { $this->direktimport=$value; }
  public function GetDirektimport() { return $this->direktimport; }
  public function SetUst_Ok($value) { $this->ust_ok=$value; }
  public function GetUst_Ok() { return $this->ust_ok; }
  public function SetAnzgleichzeitig($value) { $this->anzgleichzeitig=$value; }
  public function GetAnzgleichzeitig() { return $this->anzgleichzeitig; }
  public function SetDatumvon($value) { $this->datumvon=$value; }
  public function GetDatumvon() { return $this->datumvon; }
  public function SetDatumbis($value) { $this->datumbis=$value; }
  public function GetDatumbis() { return $this->datumbis; }
  public function SetTmpdatumvon($value) { $this->tmpdatumvon=$value; }
  public function GetTmpdatumvon() { return $this->tmpdatumvon; }
  public function SetTmpdatumbis($value) { $this->tmpdatumbis=$value; }
  public function GetTmpdatumbis() { return $this->tmpdatumbis; }
  public function SetHoleallestati($value) { $this->holeallestati=$value; }
  public function GetHoleallestati() { return $this->holeallestati; }
  public function SetCronjobaktiv($value) { $this->cronjobaktiv=$value; }
  public function GetCronjobaktiv() { return $this->cronjobaktiv; }
  public function SetNummersyncstatusaendern($value) { $this->nummersyncstatusaendern=$value; }
  public function GetNummersyncstatusaendern() { return $this->nummersyncstatusaendern; }
  public function SetZahlungsweisenmapping($value) { $this->zahlungsweisenmapping=$value; }
  public function GetZahlungsweisenmapping() { return $this->zahlungsweisenmapping; }
  public function SetVersandartenmapping($value) { $this->versandartenmapping=$value; }
  public function GetVersandartenmapping() { return $this->versandartenmapping; }
  public function SetArtikelnummeruebernehmen($value) { $this->artikelnummeruebernehmen=$value; }
  public function GetArtikelnummeruebernehmen() { return $this->artikelnummeruebernehmen; }
  public function SetArtikelbeschreibungauswawision($value) { $this->artikelbeschreibungauswawision=$value; }
  public function GetArtikelbeschreibungauswawision() { return $this->artikelbeschreibungauswawision; }
  public function SetArtikelbeschreibungenuebernehmen($value) { $this->artikelbeschreibungenuebernehmen=$value; }
  public function GetArtikelbeschreibungenuebernehmen() { return $this->artikelbeschreibungenuebernehmen; }
  public function SetStuecklisteergaenzen($value) { $this->stuecklisteergaenzen=$value; }
  public function GetStuecklisteergaenzen() { return $this->stuecklisteergaenzen; }
  public function SetAdressupdate($value) { $this->adressupdate=$value; }
  public function GetAdressupdate() { return $this->adressupdate; }
  public function SetKundenurvonprojekt($value) { $this->kundenurvonprojekt=$value; }
  public function GetKundenurvonprojekt() { return $this->kundenurvonprojekt; }
  public function SetAdd_Debitorennummer($value) { $this->add_debitorennummer=$value; }
  public function GetAdd_Debitorennummer() { return $this->add_debitorennummer; }
  public function SetDebitorennummer($value) { $this->debitorennummer=$value; }
  public function GetDebitorennummer() { return $this->debitorennummer; }
  public function SetSendonlywithtracking($value) { $this->sendonlywithtracking=$value; }
  public function GetSendonlywithtracking() { return $this->sendonlywithtracking; }
  public function SetShopbilderuebertragen($value) { $this->shopbilderuebertragen=$value; }
  public function GetShopbilderuebertragen() { return $this->shopbilderuebertragen; }
  public function SetAdressennichtueberschreiben($value) { $this->adressennichtueberschreiben=$value; }
  public function GetAdressennichtueberschreiben() { return $this->adressennichtueberschreiben; }
  public function SetAuftraegeaufspaeter($value) { $this->auftraegeaufspaeter=$value; }
  public function GetAuftraegeaufspaeter() { return $this->auftraegeaufspaeter; }
  public function SetAutoversandbeikommentardeaktivieren($value) { $this->autoversandbeikommentardeaktivieren=$value; }
  public function GetAutoversandbeikommentardeaktivieren() { return $this->autoversandbeikommentardeaktivieren; }
  public function SetArtikeltexteuebernehmen($value) { $this->artikeltexteuebernehmen=$value; }
  public function GetArtikeltexteuebernehmen() { return $this->artikeltexteuebernehmen; }
  public function SetArtikelportoermaessigt($value) { $this->artikelportoermaessigt=$value; }
  public function GetArtikelportoermaessigt() { return $this->artikelportoermaessigt; }
  public function SetArtikelrabatt($value) { $this->artikelrabatt=$value; }
  public function GetArtikelrabatt() { return $this->artikelrabatt; }
  public function SetArtikelrabattsteuer($value) { $this->artikelrabattsteuer=$value; }
  public function GetArtikelrabattsteuer() { return $this->artikelrabattsteuer; }
  public function SetPositionsteuersaetzeerlauben($value) { $this->positionsteuersaetzeerlauben=$value; }
  public function GetPositionsteuersaetzeerlauben() { return $this->positionsteuersaetzeerlauben; }
  public function SetJson($value) { $this->json=$value; }
  public function GetJson() { return $this->json; }
  public function SetFreitext($value) { $this->freitext=$value; }
  public function GetFreitext() { return $this->freitext; }
  public function SetArtikelbezeichnungauswawision($value) { $this->artikelbezeichnungauswawision=$value; }
  public function GetArtikelbezeichnungauswawision() { return $this->artikelbezeichnungauswawision; }
  public function SetAngeboteanlegen($value) { $this->angeboteanlegen=$value; }
  public function GetAngeboteanlegen() { return $this->angeboteanlegen; }
  public function SetArtikelnummerbeimanlegenausshop($value) { $this->artikelnummerbeimanlegenausshop=$value; }
  public function GetArtikelnummerbeimanlegenausshop() { return $this->artikelnummerbeimanlegenausshop; }
  public function SetShoptyp($value) { $this->shoptyp=$value; }
  public function GetShoptyp() { return $this->shoptyp; }
  public function SetModulename($value) { $this->modulename=$value; }
  public function GetModulename() { return $this->modulename; }
  public function SetMaxmanuell($value) { $this->maxmanuell=$value; }
  public function GetMaxmanuell() { return $this->maxmanuell; }
  public function SetPreisgruppe($value) { $this->preisgruppe=$value; }
  public function GetPreisgruppe() { return $this->preisgruppe; }
  public function SetVariantenuebertragen($value) { $this->variantenuebertragen=$value; }
  public function GetVariantenuebertragen() { return $this->variantenuebertragen; }
  public function SetCrosssellingartikeluebertragen($value) { $this->crosssellingartikeluebertragen=$value; }
  public function GetCrosssellingartikeluebertragen() { return $this->crosssellingartikeluebertragen; }
  public function SetStaffelpreiseuebertragen($value) { $this->staffelpreiseuebertragen=$value; }
  public function GetStaffelpreiseuebertragen() { return $this->staffelpreiseuebertragen; }
  public function SetLagergrundlage($value) { $this->lagergrundlage=$value; }
  public function GetLagergrundlage() { return $this->lagergrundlage; }
  public function SetPortoartikelanlegen($value) { $this->portoartikelanlegen=$value; }
  public function GetPortoartikelanlegen() { return $this->portoartikelanlegen; }
  public function SetNurneueartikel($value) { $this->nurneueartikel=$value; }
  public function GetNurneueartikel() { return $this->nurneueartikel; }
  public function SetStartdate($value) { $this->startdate=$value; }
  public function GetStartdate() { return $this->startdate; }
  public function SetUeberschreibe_Lagerkorrekturwert($value) { $this->ueberschreibe_lagerkorrekturwert=$value; }
  public function GetUeberschreibe_Lagerkorrekturwert() { return $this->ueberschreibe_lagerkorrekturwert; }
  public function SetLagerkorrekturwert($value) { $this->lagerkorrekturwert=$value; }
  public function GetLagerkorrekturwert() { return $this->lagerkorrekturwert; }
  public function SetVertrieb($value) { $this->vertrieb=$value; }
  public function GetVertrieb() { return $this->vertrieb; }
  public function SetEigenschaftenuebertragen($value) { $this->eigenschaftenuebertragen=$value; }
  public function GetEigenschaftenuebertragen() { return $this->eigenschaftenuebertragen; }
  public function SetKategorienuebertragen($value) { $this->kategorienuebertragen=$value; }
  public function GetKategorienuebertragen() { return $this->kategorienuebertragen; }
  public function SetStornoabgleich($value) { $this->stornoabgleich=$value; }
  public function GetStornoabgleich() { return $this->stornoabgleich; }
  public function SetNurpreise($value) { $this->nurpreise=$value; }
  public function GetNurpreise() { return $this->nurpreise; }
  public function SetSteuerfreilieferlandexport($value) { $this->steuerfreilieferlandexport=$value; }
  public function GetSteuerfreilieferlandexport() { return $this->steuerfreilieferlandexport; }
  public function SetGutscheineuebertragen($value) { $this->gutscheineuebertragen=$value; }
  public function GetGutscheineuebertragen() { return $this->gutscheineuebertragen; }
  public function SetGesamtbetragfestsetzen($value) { $this->gesamtbetragfestsetzen=$value; }
  public function GetGesamtbetragfestsetzen() { return $this->gesamtbetragfestsetzen; }
  public function SetLastschriftdatenueberschreiben($value) { $this->lastschriftdatenueberschreiben=$value; }
  public function GetLastschriftdatenueberschreiben() { return $this->lastschriftdatenueberschreiben; }
  public function SetGesamtbetragfestsetzendifferenz($value) { $this->gesamtbetragfestsetzendifferenz=$value; }
  public function GetGesamtbetragfestsetzendifferenz() { return $this->gesamtbetragfestsetzendifferenz; }
  public function SetApi_Account_Id($value) { $this->api_account_id=$value; }
  public function GetApi_Account_Id() { return $this->api_account_id; }
  public function SetApi_Account_Token($value) { $this->api_account_token=$value; }
  public function GetApi_Account_Token() { return $this->api_account_token; }
  public function SetAutoversandoption($value) { $this->autoversandoption=$value; }
  public function GetAutoversandoption() { return $this->autoversandoption; }
  public function SetAutosendarticle($value) { $this->autosendarticle=$value; }
  public function GetAutosendarticle() { return $this->autosendarticle; }

}
