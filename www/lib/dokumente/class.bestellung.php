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
if(!class_exists('BriefpapierCustom'))
{
  class BriefpapierCustom extends Briefpapier
  {
    
  }
}

class BestellungPDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="bestellung";
    $this->doctypeOrig="Bestellung";
    $this->bestellungohnepreis=0;
    parent::__construct($this->app,$projekt);
  } 


  function GetBestellung($id)
  {
    $this->doctypeid = $id;
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $this->setRecipientLieferadresse($id,"bestellung");

    $data = $this->app->DB->SelectRow(
      "SELECT adresse,projekt, sprache, angebot, ustid, keineartikelnummern, bestellbestaetigung, artikelnummerninfotext, 
            einkaeufer, belegnr, freitext, bodyzusatz, ohne_briefpapier, abweichendebezeichnung, bestellungohnepreis, 
       kundennummerlieferant AS kundennummer, DATE_FORMAT(datum,'%d.%m.%Y') AS datum, DATE_FORMAT(datum,'%Y%m%d') as datum2,
       lieferantennummer
            FROM bestellung 
            WHERE id='$id' 
            LIMIT 1"
    );
    extract($data,EXTR_OVERWRITE);
    $adresse = $data['adresse'];
    $sprache = $data['sprache'];
    $angebot = $data['angebot'];
    $ustid = $data['ustid'];
    $projekt = $data['projekt'];
    $keineartikelnummern = $data['keineartikelnummern'];
    $bestellbestaetigung = $data['bestellbestaetigung'];
    $artikelnummerninfotext = $data['artikelnummerninfotext'];
    $einkaeufer = $data['einkaeufer'];
    $belegnr = $data['belegnr'];
    $freitext = $data['freitext'];
    $bodyzusatz = $data['bodyzusatz'];
    $ohne_briefpapier = $data['ohne_briefpapier'];
    $abweichendebezeichnung = $data['abweichendebezeichnung'];
    $bestellungohnepreis = $data['bestellungohnepreis'];
    $kundennummer = $data['kundennummer'];
    $datum = $data['datum'];
    $datum2 = $data['datum2'];
    $lieferantennummer = $data['lieferantennummer'];

    $this->bestellungohnepreis=$data['bestellungohnepreis'];

    if(empty($kundennummer)) {
      $kundennummer = $this->app->DB->Select("SELECT kundennummerlieferant FROM adresse WHERE id='$adresse' LIMIT 1");
    }
    if($einkaeufer=='') {
      $einkaeufer = $this->app->DB->Select("SELECT bearbeiter FROM bestellung WHERE id='$id' LIMIT 1");
    }
    if(empty($sprache)) {
      $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
    }

    $kundennummer = $this->app->erp->ReadyForPDF($kundennummer);
    $einkaeufer = $this->app->erp->ReadyForPDF($einkaeufer);
    $angebot = $this->app->erp->ReadyForPDF($angebot);
    $this->app->erp->BeschriftungSprache($sprache);
    $this->sprache = $sprache;

    $projektabkuerzung = $this->app->DB->Select(sprintf('SELECT abkuerzung FROM projekt WHERE id = %d', $projekt));

    if($this->bestellungohnepreis) {
      $this->nichtsichtbar_summe = 1;
    }

    if($ohne_briefpapier=='1') {
      $this->logofile = '';
      $this->briefpapier='';
      $this->briefpapier2='';
    }

    if($belegnr=='' || $belegnr=='0') {
      $belegnr = '- '.$this->app->erp->Beschriftung('dokument_entwurf');
    }

    $this->doctypeOrig=$this->app->erp->Beschriftung('dokument_bestellung')." $belegnr";

    if($abweichendebezeichnung)
    {
      $this->doctypeOrig=($this->app->erp->Beschriftung('bezeichnungbestellungersatz')?$this->app->erp->Beschriftung('bezeichnungbestellungersatz'):$this->app->erp->Beschriftung('dokument_bestellung'))." $belegnr";
    }
    else {
      $this->doctypeOrig=$this->app->erp->Beschriftung('dokument_bestellung')." $belegnr";
    }

    if($angebot=='') {
      $angebot = '-';
    }
    if($kundennummer=='') {
      $kundennummer= '-';
    }

    if(!$this->app->erp->BestellungMitUmsatzeuer($id)) {
      $this->ust_befreit=true;
    }

    /** @var \Xentral\Modules\Company\Service\DocumentCustomizationService $service */
    $service = $this->app->Container->get('DocumentCustomizationService');
      if($block = $service->findActiveBlock('corr', 'suppliers_order', $projekt)) {
      $sCD = $service->parseBlockAsArray($this->getLanguageCodeFrom($this->sprache),'corr', 'suppliers_order',[
        'BESTELLNUMMER'  => $belegnr,
        'DATUM'          => $datum,
        'KUNDENNUMMER'   => $kundennummer,
        'EINKAEUFER'     => $einkaeufer,
        'LIEFERANTENNUMMER' => $lieferantennummer,
        'PROJEKT'        => $projektabkuerzung,
        'EMAIL'          => '',
        'TELEFON'        => '',
        'BEARBEITER'     => '',
        'VERTRIEB'       => '',
      ], $projekt);
      if(!empty($sCD)) {
        switch($block['fontstyle']) {
          case 'f':
            $this->setBoldCorrDetails($sCD);
            break;
          case 'i':
            $this->setItalicCorrDetails($sCD);
            break;
          case 'fi':
            $this->setItalicBoldCorrDetails($sCD);
            break;
          default:
            $this->setCorrDetails($sCD, true);
            break;
        }
      }
    }
    else{
      if($briefpapier_bearbeiter_ausblenden){
        $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_bestellung_angebotnummer") => $angebot,
          $this->app->erp->Beschriftung("dokument_bestellung_unserekundennummer") => $kundennummer,
          $this->app->erp->Beschriftung("dokument_bestelldatum") => $datum));
      }else{
        $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_bestellung_angebotnummer") => $angebot,
          $this->app->erp->Beschriftung("dokument_bestellung_unserekundennummer") => $kundennummer,
          $this->app->erp->Beschriftung("dokument_bestelldatum") => $datum,
          $this->app->erp->Beschriftung("dokument_bestellung_einkauf") => $einkaeufer));
      }
    }

    if(!$this->app->erp->BestellungMitUmsatzeuer($id) && $ustid!='' ) {
      //$steuer = "\nSteuerfreie innergemeinschaftliche Lieferung. Ihre USt-IdNr. $ustid Land: $land";
      $this->ust_befreit=true;
      if($keinsteuersatz!='1') {
        $steuer = $this->app->erp->Beschriftung('eu_lieferung_vermerk');
      }
      $steuer = str_replace('{USTID}',$ustid,$steuer);
      $steuer = str_replace('{LAND}',$land,$steuer);
    }

    $body=$this->app->erp->Beschriftung('bestellung_header');
    if($bodyzusatz!='') {
      $body=$body."\r\n".$bodyzusatz;
    }
    $body = $this->app->erp->ParseUserVars('bestellung',$id,$body);

    if($this->app->erp->Firmendaten('footer_reihenfolge_bestellung_aktivieren')=='1')
    {
      $footervorlage = $this->app->erp->Firmendaten('footer_reihenfolge_bestellung');
      if($footervorlage==''){
        $footervorlage = "{FOOTERFREITEXT}\r\n{FOOTERTEXTVORLAGEBESTELLUNG}";
      }
      $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
      $footervorlage = str_replace('{FOOTERTEXTVORLAGEBESTELLUNG}',$this->app->erp->Beschriftung("bestellung_footer"),$footervorlage);
      $footervorlage  = $this->app->erp->ParseUserVars("bestellung",$id,$footervorlage);
      $footer = $footervorlage;
    } else {
      $footer = $freitext."\r\n".$this->app->erp->ParseUserVars('bestellung',$id,$this->app->erp->Beschriftung("bestellung_footer"));
    }

    if($bestellbestaetigung) {
      $this->setTextDetails(array(
            "body"=>$body,
            "footer"=>$footer."\r\n".$this->app->erp->Beschriftung('dokument_bestellung_bestaetigung')));
    } else 
    {
      $this->setTextDetails(array(
            "body"=>$body,
            "footer"=>$footer));
    }
    $artikel = $this->app->DB->SelectArr(
      "SELECT bp.*, art.ean AS artean, art.nummer AS artnummer, art.herstellernummer AS artherstellernummer, 
       art.einheit as arteinheit, art.hersteller AS arthersteller
        FROM bestellung_position AS bp 
        LEFT JOIN artikel AS art ON bp.artikel = art.id
        WHERE bp.bestellung='$id' 
        ORDER By bp.sort"
    );
    if(empty($artikel)) {
      $artikel = [];
    }
    $steuersatzV = $this->app->erp->GetSteuersatzNormal(false,$id,'bestellung');
    $steuersatzR = $this->app->erp->GetSteuersatzErmaessigt(false,$id,'bestellung');
    $gesamtsteuern = 0;
    $mitumsatzsteuer = $this->app->erp->BestellungMitUmsatzeuer($id);
    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM bestellung_position WHERE bestellung='$id' LIMIT 1");
    $summe = 0;
    foreach($artikel as $key=>$value) {
      $lieferdatum = $this->app->String->Convert($value['lieferdatum'],'%1-%2-%3','%3.%2.%1');

      if($lieferdatum==='00.00.0000') {
        $lieferdatum ='';
      }//$this->app->erp->Beschriftung("dokument_lieferdatum_sofort");

      if($value['umsatzsteuer'] !== 'ermaessigt' && $value['umsatzsteuer'] !== 'befreit') {
        $value['umsatzsteuer'] = 'normal';
      }
      $tmpsteuersatz = null;
      $tmpsteuertext = null;
      $this->app->erp->GetSteuerPosition('bestellung', $value['id'],$tmpsteuersatz, $tmpsteuertext);
      if($value['steuersatz'] === null || $value['steuersatz'] < 0) {
        if($value['umsatzsteuer'] === 'ermaessigt') {
          $value['steuersatz'] = $steuersatzR;
        }
        elseif($value['umsatzsteuer'] === 'befreit') {
          $value['steuersatz'] = 0;
        }else{
          $value['steuersatz'] = $steuersatzV;
        }
        if($tmpsteuersatz !== null) {
          $value['steuersatz'] = $tmpsteuersatz;
        }
      }
      if($tmpsteuertext && !$value['steuertext']) {
        $value['steuertext'] = $tmpsteuertext;
      }
      if(!$mitumsatzsteuer) {
        $value['steuersatz'] = 0;
      }
      //	if(!$this->app->erp->BestellungMitUmsatzeuer($id)) $value[umsatzsteuer] = ""; 

      if($keineartikelnummern==1) {
        $value['bestellnummer'] = $this->app->erp->Beschriftung('dokument_bestellung_keineartikelnummer');
      }

      $ohne_artikeltext = $this->app->DB->Select("SELECT ohne_artikeltext FROM ".$this->table." WHERE id='".$this->id."' LIMIT 1");
      if($ohne_artikeltext=='1') {
        $value['beschreibung']='';
      }

      $value['artikelnummer']= $value['artnummer'];// $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      if($artikelnummerninfotext) {
        if($value['bestellnummer']!=''){
          $value['beschreibung'] = $value['beschreibung'] . "\n" . $this->app->erp->Beschriftung('dokument_bestellung_bestellnummer') . ': ' . $value['bestellnummer'];
        }
        $value['bestellnummer']=$value['artikelnummer'];
      } else {
        if($value['artikelnummer']!=''){
          $value['beschreibung'] = $value['beschreibung'] . "\n" . $this->app->erp->Beschriftung('dokument_bestellung_unsereartikelnummer') . ': ' . $value['artikelnummer'];
        }
      }

      if($value['vpe'] > 1 && is_numeric($value['vpe'])) {
        $value['beschreibung'] = $value['beschreibung']."\n".$this->app->erp->Beschriftung('dokument_bestellung_mengeinvpe').': '.$value['vpe'];
 				//umschalbar in der Zukunft
        $value['preis'] = $value['preis']*$value['menge']/($value['menge'] / $value['vpe']);
        $value['menge'] = round($value['menge'] / $value['vpe'],2);
        $value['einheit'] = "VPE";
			}
      elseif((String)$value['einheit'] === '') {
        $value['einheit'] = $value['arteinheit'];// $this->app->DB->Select("SELECT einheit FROM artikel WHERE id = '".$value['artikel']."' LIMIT 1");
        if((String)$value['einheit'] === '') {
          $value['einheit'] = $this->app->erp->Firmendaten('artikeleinheit_standard');
        }
      }

      if($value['beschreibung']!='') {
        $newline="\n";
      }

      if($this->bestellungohnepreis) {
        $value['preis'] = '-';
      }
  
      if($value['waehrung']!='' && $value['waehrung']!=$this->waehrung){
        $this->waehrung = $value['waehrung'];
      }

      $value['menge'] = (float)$value['menge'];
      $value['herstellernummer'] = $value['artherstellernummer'];// $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $value['arthersteller'];//$this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

      $this->addItem(
        array(
          'belegposition'=>$value['id'],
          'artikel'=>$value['artikel'],
          'currency'=>$value['waehrung'],
          'amount'=>$value['menge'],
          'price'=>$value['preis'],
          'tax'=>$value['umsatzsteuer'],'steuersatz'=>$value['steuersatz'],
          'steuertext'=>$value['steuertext'],
          'vpe'=>$value['vpe'],
          'unit'=>$value['einheit'],
          'itemno'=>$value['bestellnummer'],
          'desc'=>$value['beschreibung'].($lieferdatum!=''?$newline.$this->app->erp->Beschriftung('dokument_lieferdatum').': '.$lieferdatum:''),
          'hersteller'=>$value['hersteller'],
          'herstellernummer'=>$value['herstellernummer'],
          'freifeld1'=>$value['freifeld1'],
          'freifeld2'=>$value['freifeld2'],
          'freifeld3'=>$value['freifeld3'],
          'freifeld4'=>$value['freifeld4'],
          'freifeld5'=>$value['freifeld5'],
          'freifeld6'=>$value['freifeld6'],
          'freifeld7'=>$value['freifeld7'],
          'freifeld8'=>$value['freifeld8'],
          'freifeld9'=>$value['freifeld9'],
          'freifeld10'=>$value['freifeld10'],
          'freifeld11'=>$value['freifeld11'],
          'freifeld12'=>$value['freifeld12'],
          'freifeld13'=>$value['freifeld13'],
          'freifeld14'=>$value['freifeld14'],
          'freifeld15'=>$value['freifeld15'],
          'freifeld16'=>$value['freifeld16'],
          'freifeld17'=>$value['freifeld17'],
          'freifeld18'=>$value['freifeld18'],
          'freifeld19'=>$value['freifeld19'],
          'freifeld20'=>$value['freifeld20'],
          'freifeld21'=>$value['freifeld21'],
          'freifeld22'=>$value['freifeld22'],
          'freifeld23'=>$value['freifeld23'],
          'freifeld24'=>$value['freifeld24'],
          'freifeld25'=>$value['freifeld25'],
          'freifeld26'=>$value['freifeld26'],
          'freifeld27'=>$value['freifeld27'],
          'freifeld28'=>$value['freifeld28'],
          'freifeld29'=>$value['freifeld29'],
          'freifeld30'=>$value['freifeld30'],
          'freifeld31'=>$value['freifeld31'],
          'freifeld32'=>$value['freifeld32'],
          'freifeld33'=>$value['freifeld33'],
          'freifeld34'=>$value['freifeld34'],
          'freifeld35'=>$value['freifeld35'],
          'freifeld36'=>$value['freifeld36'],
          'freifeld37'=>$value['freifeld37'],
          'freifeld38'=>$value['freifeld38'],
          'freifeld39'=>$value['freifeld39'],
          'freifeld40'=>$value['freifeld40'],
          "name"=>$value['bezeichnunglieferant']
        )
      );
            
      $netto_gesamt = $value['menge']*$value['preis'];
      $summe += $netto_gesamt;
      if(!isset($summen[$value['steuersatz']])) {
        $summen[$value['steuersatz']] = 0;
      }
      $summen[$value['steuersatz']] += ($netto_gesamt/100)*$value['steuersatz'];
      $gesamtsteuern +=($netto_gesamt/100)*$value['steuersatz'];
    }
/*
    $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position WHERE bestellung='$id'");
    $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position WHERE bestellung='$id' AND (umsatzsteuer='normal' || umsatzsteuer='') ")/100 * $this->app->erp->GetSteuersatzNormal(false,$id,"bestellung");
    $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM bestellung_position WHERE bestellung='$id' AND umsatzsteuer='ermaessigt'")/100 * $this->app->erp->GetSteuersatzErmaessigt(false,$id,"bestellung");
*/
    if($this->bestellungohnepreis!=1)
    {
      if($this->app->erp->BestellungMitUmsatzeuer($id))
      {
        $this->setTotals(
          array('totalArticles'=>$summe,'total'=>$summe + $gesamtsteuern,'summen'=>$summen,'totalTaxV'=>0,'totalTaxR'=>0)
        );
        //$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
      } else{
        $this->setTotals(array('totalArticles' => $summe, 'total' => $summe));
      }
    }

    /* Dateiname */
    //$tmp_name = str_replace([' ','.'],'', trim($this->recipient['enterprise']));
    $this->filename = $datum2.'_BE'.$belegnr.'.pdf';
    $this->setBarcode($belegnr);
  }
}
