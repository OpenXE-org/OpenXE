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

class RetourePDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="retoure";
    $this->doctypeOrig="Retoure";
    parent::__construct($this->app,$projekt);
  } 

  function GetRetoure($id,$info="",$extrafreitext="")
  {
    $this->doctypeid = $id;
    $this->parameter = $info;
    if(method_exists($this->app->erp,'RetoureSeriennummernberechnen'))$this->app->erp->RetoureSeriennummernberechnen($id);
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');

    // das muss vom retoure sein!!!!
    $this->setRecipientLieferadresse($id,"retoure");

    // OfferNo, customerId, OfferDate

    $data = $this->app->DB->SelectRow(
      "SELECT kundennummer,adresse,sprache,lieferscheinid,vertrieb,bearbeiter,email,telefon,
       DATE_FORMAT(datum,'%d.%m.%Y') as datum,ohne_artikeltext,
       belegnr,land,freitext,projekt,bodyzusatz,ohne_briefpapier,
       ihrebestellnummer,abweichendebezeichnung as retoureersatz, auftrag 
       FROM retoure WHERE id='$id'"
    );

    extract($data,EXTR_OVERWRITE);
    $kundennummer = $data['kundennummer'];
    $adresse = $data['adresse'];
    $sprache = $data['sprache'];
    $lieferscheinid = $data['lieferscheinid'];
    $vertrieb = $data['vertrieb'];
    $bearbeiter = $data['bearbeiter'];

    $datum = $data['datum'];
    $ohne_artikeltext = $data['ohne_artikeltext'];
    $belegnr = $data['belegnr'];
    $land = $data['land'];
    $freitext = $data['freitext'];
    $projekt = $data['projekt'];
    $bodyzusatz = $data['bodyzusatz'];
    $ohne_briefpapier = $data['ohne_briefpapier'];
    $ihrebestellnummer = $data['ihrebestellnummer'];
    $retoureersatz = $data['retoureersatz'];
    $telefon = $data['telefon'];
    $email = $data['email'];
    $auftrag = $data['auftrag'];
    $projektabkuerzung = $this->app->DB->Select(
      sprintf(
        'SELECT abkuerzung FROM projekt WHERE id = %d',
        $projekt
      )
    );


    $this->projekt = $projekt;
    if(empty($kundennummer))$kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    if(empty($sprache))$sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
    $this->sprache = $sprache;
    $this->app->erp->BeschriftungSprache($sprache);
    $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='".$data['lieferscheinid']."' LIMIT 1");
    $lieferscheindatum = $this->app->DB->Select("SELECT DATE_FORMAT(datum, '%d.%m.%Y') AS datum FROM lieferschein WHERE id = '".$data['lieferscheinid']."' LIMIT 1");
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);
    $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);


    $trackingnummer = $this->app->DB->Select("SELECT tracking FROM versand WHERE retoure='$id' LIMIT 1");

    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }

    if($belegnr=="" || $belegnr=="0") $belegnr = "- ".$this->app->erp->Beschriftung("dokument_entwurf");

    $this->zusatzfooter = " (RT$belegnr)";


    if($retoureersatz) 
    $this->doctypeOrig=($this->app->erp->Beschriftung("bezeichnungretoureersatz")?$this->app->erp->Beschriftung("bezeichnungretoureersatz"):$this->app->erp->Beschriftung("dokument_retoure")).$info." $belegnr";
    else $this->doctypeOrig = $this->app->erp->Beschriftung("dokument_retoure").$info." $belegnr";


    if($retoure=="") $retoure = "-";
    if($kundennummer=="") $kundennummer= "-";
    if($auftrag=='') {
      $auftrag = '-';
    }

    $bearbeiteremail = $this->app->DB->Select("SELECT b.email FROM rechnung r LEFT JOIN adresse b ON b.id=r.bearbeiterid WHERE r.id='$id' LIMIT 1");
    $bearbeitertelefon = $this->app->DB->Select("SELECT b.telefon FROM rechnung r LEFT JOIN adresse b ON b.id=r.bearbeiterid WHERE r.id='$id' LIMIT 1");

    if($bearbeiter==$vertrieb) $vertrieb="";
    /** @var \Xentral\Modules\Company\Service\DocumentCustomizationService $service */
    $service = $this->app->Container->get('DocumentCustomizationService');
    if($block = $service->findActiveBlock('corr', 'return_order', $projekt)) {
      $sCD = $service->parseBlockAsArray($this->getLanguageCodeFrom($this->sprache),'corr', 'return_order',[
        'RETOURENNUMMER' => $belegnr,
        'DATUM'          => $datum,
        'KUNDENNUMMER'   => $kundennummer,
        'BEARBEITER'     => $bearbeiter,
        'BEARBEITEREMAIL' => $bearbeiteremail,
        'BEARBEITERTELEFON' => $bearbeitertelefon,
        'VERTRIEB'       => $vertrieb,
        'PROJEKT'        => $projektabkuerzung,
        'IHREBESTELLNUMMER'  => $ihrebestellnummer,
        'LIEFERSCHEINNUMMER' => $lieferschein,
        'LIEFERSCHEINDATUM' => $lieferscheindatum,
        'AUFTRAGSNUMMER' => $auftrag,
        'EMAIL'          => $email,
        'TELEFON'        => $telefon
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
      if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden){
        $sCD = array($this->app->erp->Beschriftung("dokument_lieferschein") => $lieferschein, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_lieferdatum") => $datum);
        if(!$briefpapier_bearbeiter_ausblenden){
          if($bearbeiter) $sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")] = $bearbeiter;
        }elseif(!$briefpapier_vertrieb_ausblenden){
          if($vertrieb) $sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")] = $vertrieb;
        }
        $this->setCorrDetails($sCD);
      }else{
        $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_lieferschein") => $lieferschein, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_lieferdatum") => $datum, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb));
      }
    }

    $body=$this->app->erp->Beschriftung("retoure_header");
    if($bodyzusatz!="") $body=$body."\r\n".$bodyzusatz;
    $body = $this->app->erp->ParseUserVars("retoure",$id,$body);

    if ($versandart!="" && $trackingnummer!="" && $this->app->erp->Firmendaten("festetrackingnummer")=="1"){
        $versandinfo = "$versandart: $trackingnummer\r\n";
    }else{ $versandinfo ="";}

     if($this->app->erp->Firmendaten("footer_reihenfolge_retoure_aktivieren")=="1")      {
        $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_retoure");
        if($footervorlage=="")
          $footervorlage = "{FOOTERVERSANDINFO}{FOOTERFREITEXT}{FOOTEREXTRAFREITEXT}\r\n{FOOTERTEXTVORLAGELIEFERSCHEIN}";
        $footervorlage = str_replace('{FOOTERVERSANDINFO}',$versandinfo,$footervorlage);
        $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
        $footervorlage = str_replace('{FOOTEREXTRAFREITEXT}',$extrafreitext,$footervorlage);
        $footervorlage = str_replace('{FOOTERTEXTVORLAGELIEFERSCHEIN}',$this->app->erp->Beschriftung("retoure_footer"),$footervorlage);
        $footervorlage  = $this->app->erp->ParseUserVars("retoure",$id,$footervorlage);
        $footer = $footervorlage;
      } else {
        $footer = $versandinfo."$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("retoure",$id,$this->app->erp->Beschriftung("retoure_footer"));
      }


    $this->setTextDetails(array(
          "body"=>$body,
          "footer"=>$footer));

    $artikel = $this->app->DB->SelectArr("SELECT * FROM retoure_position WHERE retoure='$id' ORDER By sort");
    $belege_subpositionenstuecklisten = $this->app->erp->Firmendaten('belege_subpositionenstuecklisten');
    $belege_stuecklisteneinrueckenmm = $this->app->erp->Firmendaten('belege_stuecklisteneinrueckenmm');
    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM retoure_position WHERE retoure='$id' LIMIT 1");
    foreach($artikel as $key=>$value)
    {
      $snummer = '';
      $seriennummernliste="";
      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

      if($value['explodiert_parent_artikel'] > 0)
      {
        if($belege_subpositionenstuecklisten || $belege_stuecklisteneinrueckenmm)$value['bezeichnung'] = ltrim(ltrim($value['bezeichnung'],'*'));
        if(isset($lvl) && isset($lvl[$value['explodiert_parent']]))
        {
          $value['lvl'] = $lvl[$value['explodiert_parent']] + 1;
        }else{
          $value['lvl'] = 1;
        }
        $lvl[$value['id']] = $value['lvl'];
        $check_ausblenden = $this->app->DB->Select("SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$value['explodiert_parent_artikel']."' LIMIT 1");
        if(!$check_ausblenden && isset($ausblenden) && in_array($value['explodiert_parent'], $ausblenden))
        {
          $check_ausblenden = true;
        }
        if($check_ausblenden)
        {
          $ausblenden[] = $value['id'];
        }
      } else 
      {
        $check_ausblenden=0;
        $lvl[$value['id']] = 0;
      }

      if(!$this->app->erp->Export($land))
      {
        $value['zolltarifnummer']="";
        $value['herkunftsland']="";
      }

      if($ohne_artikeltext=="1") $value['beschreibung']="";


      $value['menge'] = floatval($value['menge']);

      if($value['ausblenden_im_pdf']) $check_ausblenden=1;

      if($check_ausblenden!=1)
      {
        $this->addItem(array('amount'=>$value['menge'],'lvl'=>$value['lvl'],
              'itemno'=>$value['nummer'],
              'artikel'=>$value['artikel'],
              'desc'=>ltrim($value['beschreibung']).(strpos($value['beschreibung'], str_replace(' ', '', $value['lagertext'])) !== false?'':($value['beschreibung']!=""?"\r\n":'').$value['lagertext']),
              'unit'=>$value['einheit'],
              'hersteller'=>$value['hersteller'],
              'artikelnummerkunde'=>$value['artikelnummerkunde'],
              'lieferdatum'=>$value['lieferdatum'],
              'lieferdatumkw'=>$value['lieferdatumkw'],
              'zolltarifnummer'=>$value['zolltarifnummer'],
              'herkunftsland'=>$value['herkunftsland'],
              'herstellernummer'=>trim($value['herstellernummer']),
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
              "name"=>$value['bezeichnung']));
      }
    }


    /* Dateiname */
    $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM retoure WHERE id='$id' LIMIT 1");
    $belegnr= $this->app->DB->Select("SELECT belegnr FROM retoure WHERE id='$id' LIMIT 1");
    $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    $tmp_name = str_replace('.','',$tmp_name);

    $this->filename = $datum."_RT".$belegnr.".pdf";
    $this->setBarcode($belegnr);
  }


}
