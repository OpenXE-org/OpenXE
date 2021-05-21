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


class GutschriftPDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="gutschrift";
    $this->doctypeOrig="Gutschrift";
    parent::__construct($this->app,$projekt);
  } 


  function GetGutschrift($id)
  {
    $this->doctypeid = $id;

    if($this->app->erp->Firmendaten("steuerspalteausblenden")=="1")
    { 
      // pruefe ob es mehr als ein steuersatz gibt // wenn ja dann darf man sie nicht ausblenden
      $check = $this->app->erp->SteuerAusBeleg($this->doctype,$id);
      if(count($check)>1)$this->ust_spalteausblende=false;
      else $this->ust_spalteausblende=true;
    }

    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    //$this->setRecipientDB($adresse);
    $this->setRecipientLieferadresse($id,"gutschrift");

    $data = $this->app->DB->SelectRow(
      "SELECT adresse,kundennummer, sprache, rechnungid, buchhaltung, bearbeiter, vertrieb, 
       lieferschein AS lieferscheinid, DATE_FORMAT(datum,'%d.%m.%Y') AS datum, 
       DATE_FORMAT(lieferdatum,'%d.%m.%Y') AS lieferdatum, belegnr, freitext, ustid, ust_befreit, 
       stornorechnung, keinsteuersatz, land, typ, zahlungsweise, zahlungsstatus, zahlungszieltage, 
       zahlungszielskonto, projekt, waehrung, bodyzusatz, 
       DATE_FORMAT(DATE_ADD(datum, INTERVAL zahlungszieltage DAY),'%d.%m.%Y') AS zahlungsdatum, 
       ohne_briefpapier, ihrebestellnummer,DATE_FORMAT(datum,'%Y%m%d') as datum2, email, telefon  
        FROM gutschrift WHERE id='$id' LIMIT 1"
    );
    extract($data,EXTR_OVERWRITE);
    $adresse = $data['adresse'];
    $kundennummer = $data['kundennummer'];
    $sprache = $data['sprache'];
    $rechnungid = $data['rechnungid'];
    $buchhaltung = $data['buchhaltung'];
    $email = $data['email'];
    $telefon = $data['telefon'];
    $bearbeiter = $data['bearbeiter'];
    $vertrieb = $data['vertrieb'];
    $lieferscheinid = $data['lieferscheinid'];
    $datum = $data['datum'];
    $lieferdatum = $data['lieferdatum'];
    $belegnr = $data['belegnr'];
    $freitext = $data['freitext'];
    $ustid = $data['ustid'];
    $ust_befreit = $data['ust_befreit'];
    $stornorechnung = $data['stornorechnung'];
    $keinsteuersatz = $data['keinsteuersatz'];
    $land = $data['land'];
    $typ = $data['typ'];
    $zahlungsweise = $data['zahlungsweise'];
    $zahlungszieltage = $data['zahlungszieltage'];

    $zahlungszielskonto = $data['zahlungszielskonto'];
    $projekt = $data['projekt'];
    $waehrung = $data['waehrung'];
    $bodyzusatz = $data['bodyzusatz'];
    $zahlungsdatum = $data['zahlungsdatum'];
    $ohne_briefpapier = $data['ohne_briefpapier'];
    $ihrebestellnummer = $data['ihrebestellnummer'];
    $datum2 = $data['datum2'];
    $projektabkuerzung = $this->app->DB->Select(sprintf('SELECT abkuerzung FROM projekt WHERE id = %d', $projekt));
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    if(empty($sprache)){
      $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
    }
    $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");
    $lieferscheindatum = $this->app->DB->Select("SELECT DATE_FORMAT(datum, '%d.%m.%Y') AS datum FROM lieferschein WHERE id = '$lieferscheinid' LIMIT 1");
    $rechnung = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$rechnungid' LIMIT 1");
    $rechnungsdatum = $this->app->DB->Select("SELECT DATE_FORMAT(datum, '%d.%m.%Y') AS datum FROM rechnung WHERE id = '$rechnungid' LIMIT 1");
    $auftrag = $this->app->DB->Select("SELECT auftrag FROM rechnung WHERE id = '$rechnungid' LIMIT 1");

    $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);

    $this->app->erp->BeschriftungSprache($sprache);
    if($waehrung)$this->waehrung = $waehrung;
    $this->sprache = $sprache;
    $this->projekt = $projekt;
    $this->anrede = $typ;

    if($vertrieb==$bearbeiter && (!$briefpapier_bearbeiter_ausblenden && !$briefpapier_vertrieb_ausblenden)) $vertrieb=""; 

    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }

    //      $zahlungsweise = strtolower($zahlungsweise);

    if($zahlungsweise=="lastschrift" || $zahlungsweise=="einzugsermaechtigung")
    {
      $zahlungsweisetext = "\n".$this->app->erp->Beschriftung("dokument_offene_lastschriften");
    }

    //if($zahlungszielskonto>0) $zahlungsweisetext .= "\n".$this->app->erp->Beschriftung("dokument_skonto")." $zahlungszielskonto% ".$this->app->erp->Beschriftung("dokument_auszahlungskonditionen");

    if($zahlungszielskonto!=0)
       $zahlungsweisetext .="\r\n".$this->app->erp->Beschriftung("dokument_skontoanderezahlungsweisen");

    $zahlungsweisetext = str_replace('{ZAHLUNGSZIELSKONTO}',number_format($zahlungszielskonto,2,',','.'),$zahlungsweisetext);

    if($belegnr=="" || $belegnr=="0") $belegnr = "- ".$this->app->erp->Beschriftung("dokument_entwurf");


    if($stornorechnung)
      $this->doctypeOrig=$this->app->erp->Beschriftung("bezeichnungstornorechnung")." $belegnr";
    else
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_gutschrift")." $belegnr";

    if($gutschrift=="") $gutschrift = "-";
    if($kundennummer=="") $kundennummer= "-";

    if($auftrag=="0") $auftrag = "-";
    if($lieferschein=="0") $lieferschein= "-";

    $bearbeiteremail = $this->app->DB->Select("SELECT b.email FROM gutschrift g LEFT JOIN adresse b ON b.id=g.bearbeiterid WHERE g.id='$id' LIMIT 1");
    $bearbeitertelefon = $this->app->DB->Select("SELECT b.telefon FROM gutschrift g LEFT JOIN adresse b ON b.id=g.bearbeiterid WHERE g.id='$id' LIMIT 1");

    /** @var \Xentral\Modules\Company\Service\DocumentCustomizationService $service */
    $service = $this->app->Container->get('DocumentCustomizationService');
      if($block = $service->findActiveBlock('corr', 'credit_note', $projekt)) {
      $sCD = $service->parseBlockAsArray($this->getLanguageCodeFrom($this->sprache),'corr', 'credit_note',[
        'GUTSCHRIFTSNUMMER' => $belegnr,
        'DATUM'             => $datum,
        'RECHNUNGSNUMMER'   => $rechnung,
        'RECHNUNGSDATUM'    => $rechnungsdatum,
        'KUNDENNUMMER'      => $kundennummer,
        'BEARBEITER'        => $bearbeiter,
        'BEARBEITEREMAIL'   => $bearbeiteremail,
        'BEARBEITERTELEFON' => $bearbeitertelefon,
        'VERTRIEB'          => $vertrieb,
        'PROJEKT'           => $projektabkuerzung,
        'AUFTRAGSNUMMER'    => $auftrag,
        'LIEFERSCHEINNUMMER' => $lieferschein,
        'LIEFERSCHEINDATUM' => $lieferscheindatum,
        'EMAIL'             => $email,
        'TELEFON'           => $telefon



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

      //$this->setCorrDetails(array("Auftrag"=>$auftrag,"Datum"=>$datum,"Ihre Kunden-Nr."=>$kundennummer,"Lieferschein"=>$lieferschein,"Buchhaltung"=>$buchhaltung));
      if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden){
        if($rechnung != ""){
          $sCD = array($this->app->erp->Beschriftung("dokument_rechnung") => $rechnung, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_datum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer);
        }else{
          $sCD = array($this->app->erp->Beschriftung("dokument_datum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer);
          //$this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_datum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer));
        }
        if(!$briefpapier_bearbeiter_ausblenden){
          if($bearbeiter) $sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")] = $bearbeiter;
        }elseif(!$briefpapier_vertrieb_ausblenden){
          if($vertrieb) $sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")] = $vertrieb;
        }

      }else{
        if($rechnung != "")
          $sCD = array($this->app->erp->Beschriftung("dokument_rechnung") => $rechnung, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_datum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb);
        else
          $sCD = array($this->app->erp->Beschriftung("dokument_datum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb);
      }

      if($lieferdatum != "00.00.0000")
        $sCD[$this->app->erp->Beschriftung("dokument_lieferdatum")] = $lieferdatum;


      $this->setCorrDetails($sCD);
    }

    if($keinsteuersatz!="1")
    {

      if($ust_befreit==2)//$this->app->erp->Export($land))
          $steuer = $this->app->erp->Beschriftung("export_lieferung_vermerk");
      else {
        if($ust_befreit==1 && $ustid!="")//$this->app->erp->IstEU($land))
          $steuer = $this->app->erp->Beschriftung("eu_lieferung_vermerk");
      }
      $steuer = str_replace('{USTID}',$ustid,$steuer);
      $steuer = str_replace('{LAND}',$land,$steuer);
    }

    $gutschrift_header=$this->app->erp->Beschriftung("gutschrift_header");
    if($bodyzusatz!="") $gutschrift_header=$gutschrift_header."\r\n".$bodyzusatz;

    if($stornorechnung)
    {
      $gutschrift_header = str_replace('{ART}',$this->app->erp->Beschriftung("bezeichnungstornorechnung"),$gutschrift_header);
    } else {
      $gutschrift_header = str_replace('{ART}',$this->app->erp->Beschriftung("dokument_gutschrift"),$gutschrift_header);
    } 

    $gutschrift_header = $this->app->erp->ParseUserVars("gutschrift",$id,$gutschrift_header);


      if($this->app->erp->Firmendaten("footer_reihenfolge_gutschrift_aktivieren")=="1")      {        
        $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_gutschrift");        
        if($footervorlage=="")          
          $footervorlage = "{FOOTERFREITEXT}\r\n{FOOTERTEXTVORLAGEGUTSCHRIFT}\r\n{FOOTERSTEUER}\r\n{FOOTERZAHLUNGSWEISETEXT}";

        $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
        $footervorlage = str_replace('{FOOTERTEXTVORLAGEGUTSCHRIFT}',$this->app->erp->Beschriftung("gutschrift_footer"),$footervorlage);        
        $footervorlage = str_replace('{FOOTERSTEUER}',$steuer,$footervorlage);        
        $footervorlage = str_replace('{FOOTERZAHLUNGSWEISETEXT}',$zahlungsweisetext,$footervorlage);        
        $footervorlage  = $this->app->erp->ParseUserVars("gutschrift",$id,$footervorlage);        
        $footer = $footervorlage;
      } else {
        $footer = "$freitext"."\r\n".$this->app->erp->ParseUserVars("gutschrift",$id,$this->app->erp->Beschriftung("gutschrift_footer"))."\r\n$zahlungsweisetext\r\n$steuer";
      }


    $this->setTextDetails(array(
          "body"=>$gutschrift_header,
          "footer"=>$footer));

    $artikel = $this->app->DB->SelectArr("SELECT * FROM gutschrift_position WHERE gutschrift='$id' ORDER By sort");

    if(!$this->app->erp->GutschriftMitUmsatzeuer($id)) $this->ust_befreit=true;

    $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM gutschrift_position WHERE gutschrift='$id'");
    if($summe_rabatt <> 0) $this->rabatt=1;

    if($this->app->erp->Firmendaten("modul_verband")=="1") $this->rabatt=1; 

    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM gutschrift_position WHERE gutschrift='$id' LIMIT 1");
    $steuersatzV = $this->app->erp->GetSteuersatzNormal(false,$id,"gutschrift");
    $steuersatzR = $this->app->erp->GetSteuersatzErmaessigt(false,$id,"gutschrift");
    $gesamtsteuern = 0;
    $mitumsatzsteuer = $this->app->erp->GutschriftMitUmsatzeuer($id);
    $belege_subpositionenstuecklisten = $this->app->erp->Firmendaten('belege_subpositionenstuecklisten');
    $belege_stuecklisteneinrueckenmm = $this->app->erp->Firmendaten('belege_stuecklisteneinrueckenmm');
    //$positionenkaufmaenischrunden = $this->app->erp->Firmendaten('positionenkaufmaenischrunden');
    $positionenkaufmaenischrunden = $this->app->erp->Projektdaten($projekt,"preisberechnung");
    $viernachkommastellen_belege = $this->app->erp->Firmendaten('viernachkommastellen_belege');
    foreach($artikel as $key=>$value)
    {
      if($value['umsatzsteuer'] != "ermaessigt" && $value['umsatzsteuer'] != "befreit") $value['umsatzsteuer'] = "normal";
      $tmpsteuersatz = null;
      $tmpsteuertext = null;
      $this->app->erp->GetSteuerPosition('gutschrift', $value['id'],$tmpsteuersatz, $tmpsteuertext);
      if(is_null($value['steuersatz']) || $value['steuersatz'] < 0)
      {
        if($value['umsatzsteuer'] == "ermaessigt")
        {
          $value['steuersatz'] = $steuersatzR;
        }elseif($value['umsatzsteuer'] == "befreit")
        {
          $value['steuersatz'] = $steuersatzR;
        }else{
          $value['steuersatz'] = $steuersatzV;
        }
        if(!is_null($tmpsteuersatz))$value['steuersatz'] = $tmpsteuersatz;
      }
      if($tmpsteuertext && !$value['steuertext'])$value['steuertext'] = $tmpsteuertext;
      if(!$mitumsatzsteuer)$value['steuersatz'] = 0;
      // negative Darstellung bei Stornorechnung
      if($stornorechnung) $value['preis'] = $value['preis'] *-1;

      if(!$this->app->erp->Export($land))
      {
        $value['zolltarifnummer']="";
        $value['herkunftsland']="";
      }

      $value = $this->CheckPosition($value,"gutschrift",$this->doctypeid,$value['id']);

      $value['menge'] = floatval($value['menge']);

      if($value['explodiert_parent_artikel'] > 0)
      {
        if($belege_subpositionenstuecklisten || $belege_stuecklisteneinrueckenmm)$value['bezeichnung'] = ltrim(ltrim($value['bezeichnung'],'*'));
        if(isset($lvl) && isset($lvl[$value['explodiert_parent_artikel']]))
        {
          $value['lvl'] = $lvl[$value['explodiert_parent_artikel']] + 1;
        }else{
          $value['lvl'] = 1;
        }
        $lvl[$value['artikel']] = $value['lvl'];
        $check_ausblenden = $this->app->DB->Select("SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$value['explodiert_parent_artikel']."' LIMIT 1");
        if(!$check_ausblenden && isset($ausblenden) && in_array($value['explodiert_parent_artikel'], $ausblenden))
        {
          $check_ausblenden = true;
        }
        if($check_ausblenden)
        {
          $ausblenden[] = $value['artikel'];
        }
      } else 
      {
        $check_ausblenden=0;
        $lvl[$value['artikel']] = 0;
        $value['lvl'] = 0;
      }

      if($value['ausblenden_im_pdf']) $check_ausblenden=1;

     $ohne_artikeltext = $this->app->DB->Select("SELECT ohne_artikeltext FROM ".$this->table." WHERE id='".$this->id."' LIMIT 1");
     if($ohne_artikeltext=="1") $value['beschreibung']="";

     if($check_ausblenden!=1)
     {
      $this->addItem(array('currency'=>$value['waehrung'],'lvl'=>isset($value['lvl'])?$value['lvl']:0,
            'amount'=>$value['menge'],
            'price'=>$value['preis'],
            'tax'=>$value['umsatzsteuer'],
            'steuersatz'=>$value['steuersatz'],
            'steuertext'=>$value['steuertext'],
            'itemno'=>$value['nummer'],
            'artikel'=>$value['artikel'],
            'unit'=>$value['einheit'],
            'desc'=>$value['beschreibung'],
            "name"=>ltrim($value['bezeichnung']),
            'artikelnummerkunde'=>$value['artikelnummerkunde'],
            'lieferdatum'=>$value['lieferdatum'],
            'lieferdatumkw'=>$value['lieferdatumkw'],
              'zolltarifnummer'=>$value['zolltarifnummer'],
              'herkunftsland'=>$value['herkunftsland'],
            'ohnepreis'=>$value['ohnepreis'],
            'grundrabatt'=>$value['grundrabatt'],
            'rabatt1'=>$value['rabatt1'],
            'rabatt2'=>$value['rabatt2'],
            'rabatt3'=>$value['rabatt3'],
            'rabatt4'=>$value['rabatt4'],
            'rabatt5'=>$value['rabatt5'],
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
              "keinrabatterlaubt"=>$value['keinrabatterlaubt'],
              "rabatt"=>$value['rabatt']));
      }
      if($positionenkaufmaenischrunden == 3){
        $netto_gesamt = $value['menge'] * round($value['preis'] - ($value['preis'] / 100 * $value['rabatt']),2);
      }else{
        $netto_gesamt = $value['menge'] * ($value['preis'] - ($value['preis'] / 100 * $value['rabatt']));
      }
      if($positionenkaufmaenischrunden)
      {
        $netto_gesamt = round($netto_gesamt, 2);
      }
      $summe = $summe + $netto_gesamt;
      if(!isset($summen[$value['steuersatz']]))$summen[$value['steuersatz']] = 0;
      $summen[$value['steuersatz']] += ($netto_gesamt/100)*$value['steuersatz'];
      $gesamtsteuern +=($netto_gesamt/100)*$value['steuersatz'];
      /*
      if($value['umsatzsteuer']=="" || $value['umsatzsteuer']=="normal")
      {
        $summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal(false,$id,"gutschrift"));
      }
      else {
        $summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt(false,$id,"gutschrift"));
      }*/

    }
    
    if($positionenkaufmaenischrunden && isset($summen) && is_array($summen))
    {
      $gesamtsteuern = 0;
      foreach($summen as $k => $v)
      {
        $summen[$k] = round($v, 2);
        $gesamtsteuern += round($v, 2);
      }
    }
    if($positionenkaufmaenischrunden)
    {
      list($summe,$gesamtsumme, $summen) = $this->app->erp->steuerAusBelegPDF($this->table, $this->id);
      $gesamtsteuern = $gesamtsumme - $summe;
    }
    
    /*
       $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM gutschrift_position WHERE gutschrift='$id'");

       $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM gutschrift_position WHERE gutschrift='$id' AND (umsatzsteuer='normal' or umsatzsteuer='')")/100 * 19;
       $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM gutschrift_position WHERE gutschrift='$id' AND umsatzsteuer='ermaessigt'")/100 * 7;
     */     
    if($this->app->erp->GutschriftMitUmsatzeuer($id))
    {
      $this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $gesamtsteuern,"summen"=>$summen,"totalTaxV"=>0,"totalTaxR"=>0));
      //$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
    } else
      $this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));

    /* Dateiname */
    $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    $tmp_name = str_replace('.','',$tmp_name);

    if($stornorechnung)
      $this->filename = $datum2."_STORNO_".$belegnr.".pdf";
    else
      $this->filename = $datum2."_GS".$belegnr.".pdf";

    $this->setBarcode($belegnr);
  }


}
