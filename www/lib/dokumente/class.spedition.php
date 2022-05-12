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

class SpeditionPDF extends BriefpapierCustom {
  public $doctype;
  /** @var ApplicationCore $app */
  public $app;
  function __construct($app,$projekt='')
  {
    $this->app=$app;
    //parent::Briefpapier();

    $this->doctypeOrig='Spedition';
    $this->doctype='spedition';

    parent::__construct($this->app,$projekt);
  }

  function GetSpedition($id)
  {
    $this->doctypeid = $id;
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    $adresse = $this->app->DB->Select("SELECT adresse FROM spedition WHERE id='$id' LIMIT 1");
    //$this->setRecipientDB($adresse);
    $this->setRecipientLieferadresse($id,'spedition');


    // OfferNo, customerId, OfferDate

    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    $sprache = $this->app->DB->Select("SELECT sprache FROM spedition WHERE id='$id' LIMIT 1");
    if(empty($sprache))$sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
    $this->sprache = $sprache;
    $this->app->erp->BeschriftungSprache($sprache);

    $angebot ='';

    /*$vertrieb= $this->app->DB->Select("SELECT vertrieb FROM spedition WHERE id='$id' LIMIT 1");
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);
    $bearbeiter= $this->app->DB->Select("SELECT bearbeiter FROM spedition WHERE id='$id' LIMIT 1");
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);*/

    $vertrieb = '';
    $bearbeiter = '';

    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM spedition WHERE id='$id' LIMIT 1");
    $land = $this->app->DB->Select("SELECT land FROM spedition WHERE id='$id' LIMIT 1");
    $ustid = '';//$this->app->DB->Select("SELECT ustid FROM spedition WHERE id='$id' LIMIT 1");
    $status = $this->app->DB->Select("SELECT status FROM spedition WHERE id='$id' LIMIT 1");
    $keinsteuersatz = '';//$this->app->DB->Select("SELECT keinsteuersatz FROM spedition WHERE id='$id' LIMIT 1");
    $belegnr = $this->app->DB->Select("SELECT if(status='angelegt','ENTWURF',CONCAT(DATE_FORMAT(datum,'%Y%m%d'),'/',id)) FROM spedition WHERE id='$id' LIMIT 1");


    $freitext = $this->app->DB->Select("SELECT freitext FROM spedition WHERE id='$id' LIMIT 1");
    $this->anrede = $this->app->DB->Select("SELECT typ FROM spedition WHERE id='$id' LIMIT 1");
    $bodyzusatz = $this->app->DB->Select("SELECT bodyzusatz FROM spedition WHERE id='$id' LIMIT 1");

    $telefax= '';//$this->app->DB->Select("SELECT telefax FROM spedition WHERE id='$id' LIMIT 1");
    $auftragersatz = '';//$this->app->DB->Select("SELECT abweichendebezeichnung FROM spedition WHERE id='$id' LIMIT 1");
    $waehrung = $this->app->DB->Select("SELECT waehrung FROM spedition WHERE id='$id' LIMIT 1");
    if($waehrung)$this->waehrung = $waehrung;

    if($belegnr=='' || $belegnr=='0') {
      $belegnr = 'Speditionsauftrag - '.$this->app->erp->Beschriftung('dokument_entwurf');
    }
    else {
      $belegnr = 'Speditionsauftrag '.$belegnr;
    }



    $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_spedition_bestaetigung")." $belegnr";


    $this->zusatzfooter = " (SP$belegnr)";

    $auftrag = '-';
    if($kundennummer=='') {
      $kundennummer= '-';
    }

    $zahlungsweise = '';//$this->app->DB->Select("SELECT zahlungsweise FROM spedition WHERE id='$id' LIMIT 1");
    $zahlungsstatus = '';//$this->app->DB->Select("SELECT zahlungsstatus FROM spedition WHERE id='$id' LIMIT 1");
    $zahlungszieltage = '';//$this->app->DB->Select("SELECT zahlungszieltage FROM spedition WHERE id='$id' LIMIT 1");
    $zahlungszieltageskonto = '';//$this->app->DB->Select("SELECT zahlungszieltageskonto FROM spedition WHERE id='$id' LIMIT 1");
    $zahlungszielskonto = '';//$this->app->DB->Select("SELECT zahlungszielskonto FROM spedition WHERE id='$id' LIMIT 1");
    $ihrebestellnummer = '';//$this->app->DB->Select("SELECT ihrebestellnummer FROM spedition WHERE id='$id' LIMIT 1");
    $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);

    $ohne_briefpapier = '';//$this->app->DB->Select("SELECT ohne_briefpapier FROM spedition WHERE id='$id' LIMIT 1");

    if($ohne_briefpapier=='1')
    {
      $this->logofile = '';
      $this->briefpapier='';
      $this->briefpapier2='';
    }

    //$zahlungstext = "\nZahlungsweise: $zahlungsweise ";
    $zahlungstext = "\n".ucfirst($zahlungsweise);

    if($zahlungsweise==='lastschrift' || $zahlungsweise==='einzugsermaechtigung')
      $zahlungsweise='lastschrift';

    if($zahlungsweise==='rechnung')
    {
      // das ist immer ein Vorschlag und keine Rechnung! Daher hier anderen Text!
      if($zahlungszieltage >0){
        $zahlung_rechnung_de = $this->app->erp->Beschriftung('zahlung_rechnung_de');
        if(empty($zahlung_rechnung_de)){
          $zahlungstext = $this->app->erp->Beschriftung('dokument_zahlung_rechnung_anab');
        }
        else{
          $zahlungstext = $zahlung_rechnung_de;
        }
      }
      else{
        $zahlungstext = $this->app->erp->Beschriftung('zahlung_rechnung_sofort_de');
      }

      if($this->app->erp->Firmendaten('eigener_skontotext')=='1' && $zahlungszielskonto>0)
      {
        $skontotext = $this->app->erp->Beschriftung('eigener_skontotext_anab');
        $skontotext = str_replace('{ZAHLUNGSZIELSKONTO}',$zahlungszielskonto,$skontotext);
        $skontotext = str_replace('{ZAHLUNGSZIELTAGESKONTO}',$zahlungszieltageskonto,$skontotext);
        $zahlungstext .= "\n".$skontotext;
      } else {
        if($zahlungszielskonto>0) $zahlungstext .= "\n".$this->app->erp->Beschriftung('dokument_skonto')." $zahlungszielskonto% ".$this->app->erp->Beschriftung('dokument_innerhalb')." $zahlungszieltageskonto ".$this->app->erp->Beschriftung('dokument_tagen');
      }
    }

    $zahlungsweise = ucfirst($zahlungsweise);

    if($telefax!="")
    {
      if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden)
      {
        $scD = array($this->app->erp->Beschriftung('dokument_angebot')=>$angebot,$this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,$this->app->erp->Beschriftung('spedition_bezeichnung_bestellnummer')=>$ihrebestellnummer,$this->app->erp->Beschriftung('dokument_auftragsdatum')=>$datum);
        if(!$briefpapier_bearbeiter_ausblenden)
        {
          if($bearbeiter)$scD[$this->app->erp->Beschriftung('spedition_bezeichnung_bearbeiter')] = $bearbeiter;
        }elseif(!$briefpapier_vertrieb_ausblenden)
        {
          if($vertrieb)$scD[$this->app->erp->Beschriftung('spedition_bezeichnung_vertrieb')] = $vertrieb;
        }
        $this->setCorrDetails($scD);
        //$this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot")=>$angebot,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("spedition_bezeichnung_bestellnummer")=>$ihrebestellnummer,$this->app->erp->Beschriftung("dokument_auftragsdatum")=>$datum));
      }else{
        if($vertrieb!=$bearbeiter)
          $this->setCorrDetails(array($this->app->erp->Beschriftung('dokument_angebot')=>$angebot,$this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,
            $this->app->erp->Beschriftung('spedition_bezeichnung_bestellnummer')=>$ihrebestellnummer,$this->app->erp->Beschriftung('dokument_auftragsdatum')=>$datum,$this->app->erp->Beschriftung('spedition_bezeichnung_bearbeiter')=>$bearbeiter,$this->app->erp->Beschriftung('spedition_bezeichnung_vertrieb')=>$vertrieb));
        else
          $this->setCorrDetails(array($this->app->erp->Beschriftung('dokument_angebot')=>$angebot,$this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,$this->app->erp->Beschriftung('spedition_bezeichnung_bestellnummer')=>$ihrebestellnummer,$this->app->erp->Beschriftung('dokument_auftragsdatum')=>$datum,$this->app->erp->Beschriftung('spedition_bezeichnung_bearbeiter')=>$bearbeiter));
      }
    }
    else
    {
      if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden)
      {
        $scD = array($this->app->erp->Beschriftung('dokument_angebot')=>$angebot,$this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,$this->app->erp->Beschriftung('spedition_bezeichnung_bestellnummer')=>$ihrebestellnummer,$this->app->erp->Beschriftung('dokument_auftragsdatum')=>$datum);
        if(!$briefpapier_bearbeiter_ausblenden)
        {
          if($bearbeiter)$scD[$this->app->erp->Beschriftung('spedition_bezeichnung_bearbeiter')] = $bearbeiter;
        }elseif(!$briefpapier_vertrieb_ausblenden)
        {
          if($vertrieb)$scD[$this->app->erp->Beschriftung('spedition_bezeichnung_vertrieb')] = $vertrieb;
        }
        $this->setCorrDetails($scD);
        //$this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot")=>$angebot,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("spedition_bezeichnung_bestellnummer")=>$ihrebestellnummer,$this->app->erp->Beschriftung("dokument_auftragsdatum")=>$datum));
      }else{
        if($vertrieb!=$bearbeiter)
          $this->setCorrDetails(array($this->app->erp->Beschriftung('dokument_angebot')=>$angebot,$this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,$this->app->erp->Beschriftung('spedition_bezeichnung_bestellnummer')=>$ihrebestellnummer,$this->app->erp->Beschriftung('dokument_auftragsdatum')=>$datum,$this->app->erp->Beschriftung("spedition_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Beschriftung("spedition_bezeichnung_vertrieb")=>$vertrieb));
        else
          $this->setCorrDetails(array($this->app->erp->Beschriftung('dokument_angebot')=>$angebot,$this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,$this->app->erp->Beschriftung('spedition_bezeichnung_bestellnummer')=>$ihrebestellnummer,$this->app->erp->Beschriftung('dokument_auftragsdatum')=>$datum,$this->app->erp->Beschriftung("spedition_bezeichnung_bearbeiter")=>$bearbeiter));
      }
    }


    if(!$this->app->erp->AuftragMitUmsatzeuer($id) && $keinsteuersatz!='1')
    {
      if($this->app->erp->Export($land)){
        $steuerzeile = $this->app->erp->Beschriftung('export_lieferung_vermerk');
      }
      else{
        $steuerzeile = $this->app->erp->Beschriftung('eu_lieferung_vermerk');
      }
      $steuerzeile = str_replace(['{USTID}','{LAND}'],[$ustid,$land],$steuerzeile);
    }


    if($this->app->erp->Firmendaten('footer_reihenfolge_auftrag_aktivieren')=='1')
    {
      $footervorlage = $this->app->erp->Firmendaten('footer_reihenfolge_auftrag');
      if($footervorlage=='')
        $footervorlage = "{FOOTERFREITEXT}\r\n{FOOTERTEXTVORLAGEAUFTRAG}\r\n{FOOTERSTEUER}\r\n{FOOTERZAHLUNGSWEISETEXT}";
      $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
      $footervorlage = str_replace('{FOOTERTEXTVORLAGEAUFTRAG}',$this->app->erp->Beschriftung('spedition_footer'),$footervorlage);
      $footervorlage = str_replace('{FOOTERSTEUER}',$steuerzeile,$footervorlage);
      $footervorlage = str_replace('{FOOTERZAHLUNGSWEISETEXT}',$zahlungstext,$footervorlage);
      $footervorlage  = $this->app->erp->ParseUserVars('auftrag',$id,$footervorlage);
      $footer = $footervorlage;
    } else {
      $footer = "$freitext\r\n".$this->app->erp->ParseUserVars('auftrag',$id,$this->app->erp->Beschriftung('spedition_footer')."\r\n$steuerzeile\r\n$zahlungstext");
    }



    $body=$this->app->erp->Beschriftung('spedition_header');
    if($bodyzusatz!='') $body=$body.'\r\n'.$bodyzusatz;
    $body = $this->app->erp->ParseUserVars('auftrag',$id,$body);
    $this->setTextDetails(array(
      'body'=>$body,
      'footer'=>$footer));

    if(!$this->app->erp->AuftragMitUmsatzeuer($id)) {
      $this->ust_befreit=true;
    }

    $artikel = $this->app->DB->SelectArr("SELECT sa.* FROM spedition_packstuecke sa 
    
    WHERE sa.avi='$id'");

    foreach($artikel as $key=>$value) {
      $steuersatzR = null;
      $steuersatzV = null;
      $mitumsatzsteuer = null;

      $tmpsteuersatz = null;
      $tmpsteuertext = null;
      $summe = 0;
      $gesamtsteuern = 0;

      if($value['steuersatz'] === null || $value['steuersatz'] < 0) {
        if($value['umsatzsteuer'] === 'ermaessigt'){
          $value['steuersatz'] = $steuersatzR;
        }
        else{
          $value['steuersatz'] = $steuersatzV;
        }
        if($tmpsteuersatz !== null) {
          $value['steuersatz'] = $tmpsteuersatz;
        }
      }
      if($tmpsteuertext && !$value['steuertext']) {
        $value['steuertext'] = $tmpsteuertext;
      }
      if(!$mitumsatzsteuer)$value['steuersatz'] = 0;

      if($value['explodiert'] > 0 ) {
        $value['bezeichnung'] = $value['bezeichnung'].' '.$this->app->erp->Beschriftung('dokument_stueckliste');
      }
      if($value['explodiert_parent'] > 0) { $value['preis'] = '-'; $value['umsatzsteuer']='hidden';
        $value['bezeichnung'] = '-'.$value['bezeichnung'];
      }

      // Herstellernummer von Artikel
      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

      $check_ausblenden=0;

      $value['zolltarifnummer']='';
      $value['herkunftsland']='';

      $value['charge'] = $this->app->DB->Select("SELECT wert FROM beleg_chargesnmhd WHERE doctype='lieferschein' AND doctypeid='".$value['lieferschein']."' AND type='charge' LIMIT 1");
      $value['mhd'] = $this->app->DB->Select("SELECT wert FROM beleg_chargesnmhd WHERE doctype='lieferschein' AND doctypeid='".$value['lieferschein']."' AND type='mhd' LIMIT 1");

      $value['menge'] = (float)$value['menge'];
      $value = $this->CheckPosition($value,'auftrag',$this->doctypeid,$value['id']);
      if($check_ausblenden!=1)
      {
        $this->addItem(array('currency'=>$value['waehrung'],
          'amount'=>$value['menge'],
          'price'=>$value['preis'],
          'tax'=>$value['umsatzsteuer'],
          'steuersatz'=>$value['steuersatz'],
          'steuertext'=>$value['steuertext'],
          'itemno'=>$value['nummer'],
          'unit'=>$value['einheit'],
          'desc'=>$value['beschreibung'],

          'verpackung'=>$value['verpackung'],
          'anzahlpackstuecke'=>$value['anzahlpackstuecke'],
          'inhalt'=>$value['inhalt'],
          'gewicht'=>$value['gewicht'],
          'nve'=>$value['nve'],
          'charge'=>$value['charge'],
          'mhd'=>$value['mhd'],

          'name'=>ltrim($value['name']),
          'strasse'=>$value['strasse'],
          'hausnummer'=>$value['hausnummer'],
          'plz'=>$value['plz'],
          'ort'=>$value['ort'],
          'land'=>$this->app->GetLandLang($value['land'],$sprache),
          'rabatt'=>$value['rabatt']));
      }
      $netto_gesamt = $value['menge']*($value['preis']-($value['preis']/100*$value['rabatt']));
      $summe = $summe + $netto_gesamt;
      if(!isset($summen[$value['steuersatz']])) {
        $summen[$value['steuersatz']] = 0;
      }
      $summen[$value['steuersatz']] += ($netto_gesamt/100)*$value['steuersatz'];
      $gesamtsteuern +=($netto_gesamt/100)*$value['steuersatz'];

    }

    if($this->app->erp->AuftragMitUmsatzeuer($id))
    {
      $this->setTotals(array('totalArticles'=>$summe,'total'=>$summe + $gesamtsteuern,'summen'=>$summen,'totalTaxV'=>0,'totalTaxR'=>0));
    } else{
      $this->setTotals(array('totalArticles' => $summe, 'total' => $summe));
    }

    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM spedition WHERE id='$id' LIMIT 1");
    $belegnr= '';//$this->app->DB->Select("SELECT belegnr FROM spedition WHERE id='$id' LIMIT 1");
    /*$tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    $tmp_name = str_replace('.','',$tmp_name);*/

    $this->filename = $datum.'_SAB'.$belegnr.'.pdf';

    $this->setBarcode($belegnr);
  }

  public function renderItems() {
    $projekt = 0;
    //    if($this->bestellungohnepreis) $this->doctype='lieferschein';
    $posWidth     = $this->app->erp->Firmendaten('breite_position');
    $amWidth     = $this->app->erp->Firmendaten('breite_menge');
    $itemNoWidth = $this->app->erp->Firmendaten('breite_nummer');
    $einheitWidth    = $this->app->erp->Firmendaten('breite_einheit');
    $descWidth    = $this->app->erp->Firmendaten('breite_artikel');
    $taxWidth    = $this->app->erp->Firmendaten('breite_steuer');
    $belege_subpositionen = $this->app->erp->Firmendaten('belege_subpositionen');


    if($this->doctype==='arbeitsnachweis') {
      $itemNoWidth = 20;
      $taxWidth = 40;
      $descWidth   = 95;
    }
    else if($this->doctype!=='lieferschein' && $this->doctype!=='produktion' && $this->doctype!=='preisanfrage') {
      if($descWidth <=0) {
        $descWidth = 76;
      }

      if($taxWidth <=0) {
        $taxWidth = 15;
      }
    }
    else {
      $itemNoWidth = 30;
      $descWidth   = 91;
      if($taxWidth <=0) {
        $taxWidth = 15;
      }
    }

    if($this->rabatt=='1') {
      $descWidth = $descWidth - 15;
    }
    $priceWidth = 20;
    $sumWidth   = 20;
    $rabattWidth   = 15;
    // $lineLength = $amWidth + $itemNoWidth + $descWidth + $taxWidth + $priceWidth + $sumWidth;

    $cellhoehe   = 5;

    // render table header
    if(isset($this->textDetails['body'])) {
      $this->Ln();
    }
    else {
      $this->Ln(8);
    }
    $tabellenbeschriftung  = $this->app->erp->Firmendaten('tabellenbeschriftung');

    $this->SetX($this->app->erp->Firmendaten('abstand_seitenrandlinks')+1); // eventuell einstellbar per GUI

    $this->SetFont($this->GetFont(),'B',$tabellenbeschriftung);
    $xtest = $this->GetX();
    $this->Cell($posWidth,6,$this->app->erp->Beschriftung('dokument_position'));
    if($this->doctype!=='arbeitsnachweis')
    {
      if($this->doctype==='zahlungsavis')
      {
        $this->Cell($itemNoWidth,6,'Nummer');
        $this->Cell($descWidth-$einheitWidth+$taxWidth+$priceWidth+$rabattWidth,6,'Beleg');

        $this->Cell($amWidth,6,'',0,0,'R');
      }
      else {
        $this->Cell($itemNoWidth,6,'Anzahl');//$this->app->erp->Beschriftung('dokument_artikelnummer'));
        if($this->app->erp->Firmendaten('artikeleinheit')=='1'){
          //$this->Cell($descWidth-$einheitWidth,6,$this->app->erp->Beschriftung('dokument_artikel'));
        }else{
          $this->Cell($descWidth,6,'Verpackung',0,0,'L');//$this->app->erp->Beschriftung('dokument_artikel'));
        }
        $this->Cell($descWidth,6,'Lieferadresse',0,0,'L');//$this->app->erp->Beschriftung('dokument_menge'),0,0,'R');
      }
    } else {
      $this->Cell($taxWidth,6,'Mitarbeiter');
      $this->Cell($itemNoWidth,6,'Ort');
      $this->Cell($descWidth,6,'Tätigkeit');
      $this->Cell($amWidth,6,'Stunden',0,0,'R');
    }


    if ($this->doctype==='lieferschein' || $this->doctype==='preisanfrage') {
      if($this->app->erp->Firmendaten('artikeleinheit')=='1'){
        $this->Cell($einheitWidth, 6, $this->app->erp->Beschriftung('dokument_einheit'), 0, 0, 'R');
      }
    }
    else if ($this->doctype==='zahlungsavis') {
      $this->Cell($sumWidth,6,$this->app->erp->Beschriftung('dokument_gesamt'),0,0,'R');
    }

    $this->Ln();
    $this->Line($this->app->erp->Firmendaten("abstand_seitenrandlinks")+1, $this->GetY(), 210-$this->app->erp->Firmendaten('abstand_seitenrandrechts'), $this->GetY());
    $this->Ln(2);

    // render table body
    $tabelleninhalt  = $this->app->erp->Firmendaten('tabelleninhalt');

    $this->SetFont($this->GetFont(),'',$tabelleninhalt);
    $topos = 0;
    if(is_array($this->items)) {
      $topos = count($this->items);
    }
    $zwischenpositionen = $this->app->DB->Select("SELECT count(distinct pos) FROM beleg_zwischenpositionen WHERE doctype='".$this->doctype."' AND doctypeid='".$this->doctypeid."' AND pos > 0 AND pos <'$topos' AND (postype = 'gruppe'  OR postype = 'zwischensumme' OR postype = 'gruppensumme') ORDER by sort");
    if($zwischenpositionen < 1) {
      $belege_subpositionen = false;
    }
    $hauptnummer = 0;
    $posoffset = 0;
    if($belege_subpositionen) {
      $hauptnummer = 1;
    }
    $langeartikelnummern = $this->app->erp->Firmendaten('langeartikelnummern');
    $pos=0;
    foreach($this->items as $item){
      $iszwichenpos = $this->DrawZwischenpositionen($pos);
      $item['name'] = ($langeartikelnummern?"\r\n":'').$this->app->erp->ReadyForPDF($item['name']);
      $item['desc'] = $this->app->erp->ReadyForPDF($item['desc']);
      $item['itemno'] = $this->app->erp->ReadyForPDF($item['itemno']);
      $item['herstellernummer'] = $this->app->erp->ReadyForPDF($item['herstellernummer']);
      $item['artikelnummerkunde'] = $this->app->erp->ReadyForPDF($item['artikelnummerkunde']);
      $item['lieferdatum'] = $this->app->erp->ReadyForPDF($item['lieferdatum']);
      $item['hersteller'] = $this->app->erp->ReadyForPDF($item['hersteller']);

      //TODO Soll einstellbar werden: Zeilenabstand in Tabelle normal mittel
      $cellhoehe  = 3;
      //position

      if($iszwichenpos && $belege_subpositionen && $pos > $posoffset)
      {
        $hauptnummer++;
        $posoffset = $pos;
      }

      $pos++;
      $posstr = $pos;
      if($belege_subpositionen && $hauptnummer)
      {
        $posstr = $hauptnummer.'.'.($pos-$posoffset);
      }
      $this->SetX($xtest);
      $this->Cell($posWidth,$cellhoehe,$posstr,0,0,'C');
      //artikelnummer
      if($this->doctype==='arbeitsnachweis') {
        $this->Cell($taxWidth,$cellhoehe,trim($item['person']),0);

        $zeilenuntertext  = $this->app->erp->Firmendaten('zeilenuntertext');
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        // ort
        $tmpy = $this->GetY();
        $tmpx = $this->GetX();
        $this->MultiCell($itemNoWidth,($zeilenuntertext/2),trim($item['itemno']),0); // 4 = abstand
        $tmpy2 = $this->GetY();
        $this->SetXY($tmpx+$itemNoWidth,$tmpy);
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      }
      else {
        //TODO BENE
        if($this->doctype==='lieferschein' && $this->app->erp->Firmendaten('modul_verband')=='1'){
          $this->SetFont($this->GetFont(), '', $tabelleninhalt + 3);
        }
        else{
          $this->SetFont($this->GetFont(), '', $tabelleninhalt);
        }
        if(isset($item['anzahlpackstuecke'])) {
          $this->Cell($itemNoWidth,$cellhoehe,($item['anzahlpackstuecke']?$item['anzahlpackstuecke']:55));
        }
        else {
          $this->Cell($itemNoWidth);
        }
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      }

      $position_x   = $this->GetX();
      $position_y   = $this->GetY();

// start am Ende der Zeile Zeichnen
      $this->SetAutoPageBreak(false,$this->app->erp->Firmendaten('abstand_umbruchunten')); //2306BS
      // Artikel Name
      if($item['tax']!=='hidden')
        $this->SetFont($this->GetFont(),'B',$tabelleninhalt);

      if($this->app->erp->Firmendaten('artikeleinheit')=='1'){
        $this->MultiCell($amWidth-$einheitWidth,$cellhoehe,$item['verpackung'],0,Alignment.LEFT, false);
      }else{

        $xverpackung = $this->GetX();
        if($item['verpackung']!="")
        {
          $this->MultiCell($descWidth,$cellhoehe,$item['verpackung'],0,Alignment.LEFT, false);
        }
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);

        $this->SetX($xverpackung);
        $this->Cell($descWidth,$cellhoehe,"Inhalt: ".$item['inhalt'],0,1,"L");
        $this->SetX($xverpackung);
        $this->Cell($descWidth,$cellhoehe,"Gewicht: ".$item['gewicht'],0,1,"L");
        $this->SetX($xverpackung);
        $this->Cell($descWidth,$cellhoehe,'NVE: '.$item['nve'],0,1,"L");

        if($item['mhd']!='')
        {
          $this->SetX($xverpackung);
          $this->Cell($descWidth,$cellhoehe,'MHD: '.$item['mhd'],0,1,"L");
        }

        if($item['charge']!='')
        {
          $this->SetX($xverpackung);
          $this->Cell($descWidth,$cellhoehe,'Charge: '.$item['charge'],0,1,"L");
        }
      }

      $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      $this->SetAutoPageBreak(true,$this->app->erp->Firmendaten('abstand_umbruchunten')); //2306BS

      $position_y_end_name   = $this->GetY();
//
      // wenn vorhanden Artikel Einheit
      if($this->app->erp->Firmendaten('artikeleinheit')=='1')
        $this->SetXY(($position_x + $descWidth-$einheitWidth), $position_y);
      else
        $this->SetXY(($position_x + $descWidth), $position_y);


      if($this->doctype==='arbeitsnachweis')
        $this->SetXY(($position_x + $descWidth), $position_y);


      // Menge

      if($this->doctype==='zahlungsavis'){
        $this->Cell($amWidth,$cellhoehe,'',0,0,'R');
      }else{

        $xadresse = $this->GetX();
        $this->Cell($descWidth,$cellhoehe,$item['name'],0,1,'L');
        $this->SetX($xadresse);
        $this->Cell($descWidth,$cellhoehe,$item['strasse']." ".$item['hausnummer'],0,1,'L');
        $this->SetX($xadresse);
        $this->Cell($descWidth,$cellhoehe,$item['plz']." ".$item['ort'],0,1,'L');
        $this->SetX($xadresse);
        $this->Cell($descWidth,$cellhoehe,$item['land'],0,1,'L');
      }

      if($this->doctype!=='lieferschein' && $this->doctype!=='arbeitsnachweis' && $this->doctype!=='produktion' && $this->doctype!=='preisanfrage') {
        if($this->app->erp->Firmendaten('artikeleinheit')=='1')
        {
          if($item['unit']!='') {
            $einheit = $item['unit'];
          }
          else {
            $einheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE 
                nummer='".$item['itemno']."' LIMIT 1");
            if($einheit=='') {
              $einheit = $this->app->erp->Firmendaten('artikeleinheit_standard');
            }
          }
        }

        if($item['tax']!=='hidden')
        {
          if($this->ust_befreit>0) {
            $item['tax'] = 0;
          } else {
            if($item['tax'] === 'normal') {
              $item['tax'] = $this->app->erp->GetSteuersatzNormal(true,$this->id,$this->table)-1;
            }
            else {
              $item['tax'] = $this->app->erp->GetSteuersatzErmaessigt(true,$this->id,$this->table)-1;
            }
          }
          if(isset($item['steuersatz'])) {
            $item['tax'] = $item['steuersatz'] / 100;
          }
        }
        // wenn steuerfrei komplett immer 0 steuer anzeigen
        $item['tmptax'] = $item['tax'] + 1;

        // standard anzeige mit steuer
        if($this->app->erp->Firmendaten('kleinunternehmer')!='1'){
          if($item['tax']==="hidden"){
            //$this->Cell($taxWidth,$cellhoehe,"",0,0,'R');
          } else {
            $tax = $item['tax']; //= $tax; //="USTV"?0.19:0.07;
            $tax *= 100; $tax = $tax."%";
          }
        }

        if($this->doctype!=='lieferschein' && $this->doctype!=='produktion' && $this->doctype!=='preisanfrage') {
          // preis pro Artikel
          // zentale rabatt spalte
          if($this->rabatt=='1') {
            $rabatt_string='';

            //rabatt
            if($item['grundrabatt'] > 0 || $item['rabatt1'] > 0 || $item['rabatt2'] > 0)
            {
              if($item['grundrabatt']>0) $rabatt_string .= $item['grundrabatt']."%\r\n";
              if($item['rabatt1']>0) $rabatt_string .= $item['rabatt1']."%\r\n";
              if($item['rabatt2']>0) $rabatt_string .= $item['rabatt2']."%\r\n";
              if($item['rabatt3']>0) $rabatt_string .= $item['rabatt3']."%\r\n";
              if($item['rabatt4']>0) $rabatt_string .= $item['rabatt4']."%\r\n";
              if($item['rabatt5']>0) $rabatt_string .= $item['rabatt5']."%\r\n";


              $tmpy = $this->GetY();
              $tmpx = $this->GetX();

              $this->SetFont($this->GetFont(),'',6);
              if($item['keinrabatterlaubt']=='1' || $item['rabatt']<=0 || $item['rabatt']==='') {
                $rabatt_or_porto = $this->app->DB->Select("SELECT id FROM artikel WHERE 
                    nummer='".$item['itemno']."' AND (porto='1' OR rabatt='1') LIMIT 1");
                if($rabatt_or_porto)
                  $rabatt_string='';
                else {
                  if($this->app->erp->Firmendaten('modul_verband')=='1') $rabatt_string='SNP';
                  else $rabatt_string='';
                }
              }
              $this->SetXY($tmpx+$rabattWidth,$tmpy);
              $this->SetFont($this->GetFont(),'',$tabelleninhalt);
            }
          }
        }
        else {

          if(($this->anrede==='firma' || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype==='bestellung' || $this->app->erp->Firmendaten("immernettorechnungen",$projekt)=="1")
            && $this->app->erp->Firmendaten('immerbruttorechnungen',$projekt)!='1')
            $this->Cell($priceWidth,$cellhoehe,$item['ohnepreis']?'':number_format((double)$item['price'], $this->anzahlkomma, ',', '.'),0,0,'R');
          else
            $this->Cell($priceWidth,$cellhoehe,$item['ohnepreis']?'':number_format((double)$item['price']*$item['tmptax'], $this->anzahlkomma, ',', '.'),0,0,'R');
        }
        //$this->Cell($sumWidth,$cellhoehe,number_format($item['tprice'], 2, ',', '').' '.$item['currency'],0,0,'R');
        if($this->rabatt=='1')
        {
          //gesamt preis
          if ($item['tax']==='hidden'){
            $this->Cell($priceWidth,$cellhoehe,'',0,0,'R');
          }
          else {
            if($this->rabatt=='1'){
              if(($this->anrede==='firma' || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype==='bestellung' || $this->app->erp->Firmendaten("immernettorechnungen",$projekt)=="1")
                && $this->app->erp->Firmendaten("immerbruttorechnungen",$projekt)!="1")

                $this->Cell($sumWidth,$cellhoehe,$item['ohnepreis']?'':number_format((double)$item['tprice'], $this->anzahlkomma, ',', '.'),0,0,'R');
              else
                $this->Cell($sumWidth,$cellhoehe,$item['ohnepreis']?'':number_format((double)$item['tprice']*$item['tmptax'], $this->anzahlkomma, ',', '.'),0,0,'R');
            }
            else {
              if(($this->anrede==='firma' || $this->app->erp->AnzeigeBelegNetto($this->anrede,$projekt) || $this->doctype==='bestellung' || $this->app->erp->Firmendaten("immernettorechnungen",$projekt)=="1")
                && $this->app->erp->Firmendaten('immerbruttorechnungen',$projekt)!='1')
                $this->Cell($sumWidth,$cellhoehe,$item['ohnepreis']?'':number_format((double)$item['tprice'], $this->anzahlkomma, ',', '.'),0,0,'R');
              else
                $this->Cell($sumWidth,$cellhoehe,$item['ohnepreis']?'':number_format((double)$item['tprice']*$item['tmptax'], $this->anzahlkomma, ',', '.'),0,0,'R');
            }
          }
        }

      }
      else if($this->app->erp->Firmendaten('artikeleinheit')=='1' && ($this->doctype==='lieferschein' || $this->doctype==='preisanfrage'))
      {
        if($item['unit']!=''){
          $einheit = $item['unit'];
        }
        else {
          $einheit = $this->app->DB->Select("SELECT einheit FROM artikel WHERE
                nummer='".$item['itemno']."' LIMIT 1");
          if($einheit=='') {
            $einheit = $this->app->erp->Firmendaten('artikeleinheit_standard');
          }
        }
        $this->Cell($einheitWidth,$cellhoehe,$this->app->erp->ReadyForPDF($einheit),0,0,'R');
      }

      $this->Ln();
      if($this->app->erp->Firmendaten('herstellernummerimdokument')=='1' && $item['herstellernummer']!='')
      {
        if($item['desc']!="")
          $item['desc']=$item['desc']."\r\nPN: ".$item['herstellernummer'];
        else
          $item['desc']='PN: '.$item['herstellernummer'];
      }

      if($item['lieferdatum']!='' && $item['lieferdatum']!='0000-00-00' && $item['lieferdatum']!=='00.00.0000')
      {

        if(strpos($item['lieferdatum'],'-')!==false){
          $item['lieferdatum'] = $this->app->erp->ReadyForPDF($this->app->String->Convert($item['lieferdatum'], '%1-%2-%3', '%3.%2.%1'));
        }

        if($item['lieferdatumkw']==1) {
          $ddate = $this->app->String->Convert($item['lieferdatum'],'%3.%2.%1','%1-%2-%3');
          $duedt = explode('-', $ddate);
          $date  = mktime(0, 0, 0, $duedt[1], $duedt[2], $duedt[0]);
          $week  = date('W/Y', $date);
          $item['lieferdatum'] = $this->app->erp->Beschriftung('dokument_lieferdatumkw').' '.$week;
        }

        if($item['desc']!='') {
          $item['desc'] = $item['desc'] . "\r\n" . $this->app->erp->Beschriftung('dokument_lieferdatum') . ': ' . $item['lieferdatum'];
        }
        else{
          $item['desc'] = $this->app->erp->Beschriftung('dokument_lieferdatum') . ': ' . $item['lieferdatum'];
        }
      }

      if($this->app->erp->Firmendaten('freifelderimdokument')=='1') {
        for($ifreifeld=1;$ifreifeld<=20;$ifreifeld++) {
          if($item['freifeld'.$ifreifeld]!='') {
            if($item['desc']!=''){
              $item['desc'] = $item['desc'] . "\r\n" . $this->app->erp->Beschriftung('artikel_freifeld' . $ifreifeld) . ': ' . $item['freifeld' . $ifreifeld];
            }
            else{
              $item['desc'] = $this->app->erp->Beschriftung('artikel_freifeld' . $ifreifeld) . ': ' . $item['freifeld' . $ifreifeld];
            }
          }
        }
      }

      if($item['artikelnummerkunde']!='' && $item['artikelnummerkunde']!='0'){
        if($item['desc']!=''){
          $item['desc'] = $item['desc'] . "\r\n" . $this->app->erp->Beschriftung('dokument_artikelnummerkunde') . ': ' . $item['artikelnummerkunde'];
        }
        else{
          $item['desc'] = $this->app->erp->Beschriftung('dokument_artikelnummerkunde') . ': ' . $item['artikelnummerkunde'];
        }
      }

      if($item['zolltarifnummer']!='' && $item['zolltarifnummer']!='0'){
        if($item['desc']!=''){
          $item['desc'] = $item['desc'] . "\r\n" . $this->app->erp->Beschriftung('dokument_zolltarifnummer') . ': ' . $item['zolltarifnummer'] . ' ' . $this->app->erp->Beschriftung('dokument_herkunftsland') . ': ' . $item['herkunftsland'];
        }
        else {
          $item['desc']=$this->app->erp->Beschriftung('dokument_zolltarifnummer').': '.$item['zolltarifnummer'].' '.$this->app->erp->Beschriftung('dokument_herkunftsland').': '.$item['herkunftsland'];
        }
      }

      if($item['ean']!='' && $item['ean']!='0'){
        if($item['desc']!=''){
          $item['desc'] = $item['desc'] . '\r\n' . $this->app->erp->Beschriftung('dokument_ean') . ': ' . $item['ean'];
        }
        else{
          $item['desc'] = $this->app->erp->Beschriftung('dokument_ean') . ': ' . $item['ean'];
        }
      }

      if($item['desc']!='') {
        //Herstellernummer einblenden wenn vorhanden und aktiviert

        $zeilenuntertext  = $this->app->erp->Firmendaten('zeilenuntertext');
        $this->SetY($position_y_end_name+$this->app->erp->Firmendaten('abstand_name_beschreibung'));
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        $this->Cell($posWidth);
        $this->Cell($itemNoWidth);
        if($this->doctype==='arbeitsnachweis') {
          $this->Cell($taxWidth);
        }

        if($this->doctype==='lieferschein' && $this->app->erp->Firmendaten('modul_verband')=='1'){
          $this->SetFont($this->GetFont(), '', $tabelleninhalt + 1);
        }

        if($this->app->erp->Firmendaten('briefhtml')=='1'){
          $html = $this->app->erp->ReadyForPDF($this->app->erp->RemoveNewlineAfterBreaks($item['desc']));
          if($this->app->erp->Firmendaten('artikeleinheit')=='1'){
            if($this->app->erp->Firmendaten('breite_artikelbeschreibung')){
              $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$this->WriteHTML($html),0,'L'); // 4 = abstand
              if(!empty($item['steuertext'])) {
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
            else{
              $this->MultiCell($descWidth-$einheitWidth,($zeilenuntertext/2),$this->WriteHTMLCell($descWidth-$einheitWidth,$html),0,'L'); // 4 = abstand //ALT
              if(!empty($item['steuertext'])) {
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
          }
          else {
            if($this->app->erp->Firmendaten('breite_artikelbeschreibung')=='1') {
              $this->MultiCell($descWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$this->WriteHTML($html),0,'L'); // 4 = abstand
              if(!empty($item['steuertext'])) {
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
            else {
              $this->MultiCell($descWidth,($zeilenuntertext/2),$this->WriteHTMLCell($descWidth,$html),0,'L'); // 4 = abstand //ALT
              if(!empty($item['steuertext'])) {
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
          }
        }
        else {
          if($this->app->erp->Firmendaten('artikeleinheit')=='1') {
            if($this->app->erp->Firmendaten('breite_artikelbeschreibung')) {
              $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand
              if(!empty($item['steuertext'])) {
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
            else {
              $this->MultiCell($descWidth-$einheitWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand //ALT
              if(!empty($item['steuertext'])) {
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
          }
          else {
            if($this->app->erp->Firmendaten('breite_artikelbeschreibung')=='1') {
              $this->MultiCell($descWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand
              if(!empty($item['steuertext'])) {
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
            else {
              $this->MultiCell($descWidth,($zeilenuntertext/2),trim($item['desc']),0,'L'); // 4 = abstand //ALT
              if(!empty($item['steuertext'])) {
                $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
              }
            }
          }
        }
        $this->Cell($taxWidth);
        $this->Cell($amWidth);
        $this->Ln();
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);

        $zeilenuntertext  = $this->app->erp->Firmendaten('zeilenuntertext');
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        $this->Cell($posWidth);
        $this->Cell($itemNoWidth);
        if($this->doctype==='arbeitsnachweis') {
          $this->Cell($taxWidth);
        }
        if($this->app->erp->Firmendaten('artikeleinheit')=='1'){
          $this->MultiCell($descWidth - $einheitWidth, 4, '', 0); // 4 = abstand zwischen Artikeln
        }
        else{
          $this->MultiCell($descWidth, 4, '', 0); // 4 = abstand zwischen Artikeln
        }
        $this->Cell($taxWidth);
        $this->Cell($amWidth);
        $this->Ln();
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      } else {
        $zeilenuntertext  = $this->app->erp->Firmendaten('zeilenuntertext');
        $this->SetY($position_y_end_name);
        $this->SetFont($this->GetFont(),'',$zeilenuntertext);
        $this->Cell($posWidth);
        $this->Cell($itemNoWidth);
        if($this->doctype==='arbeitsnachweis') {
          $this->Cell($taxWidth);
        }

        if($this->app->erp->Firmendaten('artikeleinheit')=='1')
        {
          $this->MultiCell($descWidth-$einheitWidth,3,trim($item['desc']),0); // 4 = abstand
          if(!empty($item['steuertext'])) {
            $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
          }
        }
        else
        {
          $this->MultiCell($descWidth,3,trim($item['desc']),0); // 4 = abstand
          if(!empty($item['steuertext'])) {
            $this->MultiCell($descWidth-$einheitWidth+$taxWidth + $sumWidth + $rabattWidth,($zeilenuntertext/2),$item['steuertext'],0,'L');
          }
        }

        $this->Cell($taxWidth);
        $this->Cell($amWidth);
        $this->Ln();
        $this->SetFont($this->GetFont(),'',$tabelleninhalt);
      }
      $this->SetY($this->GetY()+5);
    }
    $this->DrawZwischenpositionen($pos);

    $this->Line($this->app->erp->Firmendaten('abstand_seitenrandlinks')+1, $this->GetY(), 210-$this->app->erp->Firmendaten('abstand_seitenrandrechts'), $this->GetY());
  }

  public function renderTotals() {
    $this->SetY($this->GetY()+1);

    $differenz_wegen_abstand = $this->app->erp->Firmendaten('abstand_gesamtsumme_lr');

    if($this->doctype!=='lieferschein' && $this->doctype!=='arbeitsnachweis' && $this->doctype!=='preisanfrage') {
      $this->Ln(1);
      $this->SetFont($this->GetFont(),'',$this->app->erp->Firmendaten('schriftgroesse_gesamt'));
      $this->Cell($differenz_wegen_abstand,2,'',0);
      if(!($this->app->erp->Firmendaten('kleinunternehmer')!='1' && $this->doctype!=='zahlungsavis')){

        //kleinunzernehmer
        $this->Cell(30,5,'',0,0,'L');
        $this->Cell(40,5,'',0,'L','R');
      }
      $this->Ln();

      if(isset($this->totals['modeOfDispatch'])) {
        $versand = 'Versand: '.$this->totals['modeOfDispatch'];
      }
      else {
        $versand = 'Versandkosten: ';
      }
      if(isset($this->totals['priceOfDispatch'])) {
        $this->Cell($differenz_wegen_abstand,2,'',0);
        $this->Cell(30,5,$versand,0,'L','L');
        $this->Cell(40,5,number_format((double)$this->totals['priceOfDispatch'], 2, ',', '.').' '.$this->waehrung,0,'L','R');
      }
      //$this->Ln();

      if(isset($this->totals['priceOfPayment']) && $this->totals['priceOfPayment']!='0.00'){
        $this->Cell($differenz_wegen_abstand,2,'',0);
        $this->Cell(30,5,$this->totals['modeOfPayment'],0,'L','L');
        $this->Cell(40,5,number_format((double)$this->totals['priceOfPayment'], 2, ',', '.').' '.$this->waehrung,0,'L','R');
        $this->Ln();
      }

      $this->SetY($this->GetY());
      $this->SetFont($this->GetFont(),'',$this->app->erp->Firmendaten('schriftgroesse_gesamt_steuer'));

      if(isset($this->totals['totalTaxV']) && $this->totals['totalTaxV']!='0.00'){
        $this->Cell($differenz_wegen_abstand,1,'',0);

        if($this->app->erp->Firmendaten('kleinunternehmer')=='1'){
          //kleinunternehmer
          $this->Cell(30,3,'',0,'L','L');
          $this->Cell(40,3,'',0,'L','R');
        }
        $this->Ln();
      }

      if(isset($this->totals['totalTaxR']) && $this->totals['totalTaxR']!='0.00'){
        $this->Cell($differenz_wegen_abstand,1,'',0);

        if($this->app->erp->Firmendaten('kleinunternehmer')=='1'){
          //kleinunternehmer
          $this->Cell(30,3,'',0,'L','L');
          $this->Cell(40,3,"",0,'L','R');
        }
        $this->Ln();
      }

      if(isset($this->totals['summen']))
      {
        ksort($this->totals['summen'], SORT_NUMERIC);
        foreach($this->totals['summen'] as $k => $value)
        {
          $this->Cell($differenz_wegen_abstand,1,'',0);

          if($this->app->erp->Firmendaten('kleinunternehmer')=='1'){
            //kleinunternehmer
            $this->Cell(30,3,'',0,'L','L');
            $this->Cell(40,3,'',0,'L','R');
          }
          $this->Ln();
        }
      }

      if(!isset($this->totals['totalTaxR']) && !isset($this->totals['totalTaxV']) && !isset($this->totals['summen']) && $this->doctype!=='zahlungsavis')
      {
        $this->Cell($differenz_wegen_abstand,3,'',0);

        if($this->app->erp->Firmendaten('kleinunternehmer')=='1')
        {
          //kleinunternehmer
          $this->Cell(30,3,'',0,'L','L');
          $this->Cell(40,3,'',0,'L','R');
        }
        $this->Ln();
      }
      $this->SetY($this->GetY()+2);
    }

    $this->SetFont($this->GetFont(),'B',$this->app->erp->Firmendaten('schriftgroesse_gesamt'));
    $this->Cell($differenz_wegen_abstand,5,'',0);

    $this->Ln();

    $this->SetY($this->GetY()+20);

    $this->SetFont($this->GetFont(),'',$this->app->erp->Firmendaten('schriftgroesse_gesamt_steuer'));
    $this->Cell(60,5,'Datum: ',0,0,'L');
    $this->Line(26, $this->GetY()+4, 60,$this->GetY()+4);

    $this->Cell(60,5,'Uhrzeit: ',0,0,'L');
    //$this->SetX(150);
    $this->Line(86.5,$this->GetY()+4, 120.5,$this->GetY()+4);

    $this->SetY($this->GetY()+25);

    $this->Cell(60,5,'Unterschrift: ',0,0,'L');
    $this->Line(32,$this->GetY()+4, 120.5, $this->GetY()+4);

    $this->SetY($this->GetY()+10);
  }
}
