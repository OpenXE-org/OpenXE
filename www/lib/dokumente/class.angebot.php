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

class AngebotPDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="angebot";
    $this->doctypeOrig="Angebot";
    parent::__construct($this->app,$projekt);
  } 

  function GetAngebot($id)
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
    $this->setRecipientLieferadresse($id,"angebot");
    $email = '';
    $telefon = '';
    $data = $this->app->DB->SelectRow("SELECT adresse, kundennummer, sprache, ustid, ust_befreit, keinsteuersatz, land, 
       anfrage, vertrieb, bearbeiter, DATE_FORMAT(datum,'%d.%m.%Y') AS datum, 
       DATE_FORMAT(gueltigbis,'%d.%m.%Y') AS gueltigbis, belegnr, freitext, typ, zahlungsweise, 
       abweichendebezeichnung AS angebotersatz, zahlungszieltage, zahlungszieltageskonto, 
       zahlungszielskonto, projekt, waehrung, bodyzusatz, ohne_briefpapier,DATE_FORMAT(datum,'%Y%m%d') as datum2 
        FROM angebot WHERE id='$id' LIMIT 1");

    extract($data,EXTR_OVERWRITE);
    $adresse = $data['adresse'];
    $kundennummer = $data['kundennummer'];
    $sprache = $data['sprache'];
    $ustid = $data['ustid'];
    $ust_befreit = $data['ust_befreit'];
    $keinsteuersatz = $data['keinsteuersatz'];
    $land = $data['land'];

    $anfrage = $data['anfrage'];
    $vertrieb = $data['vertrieb'];
    $bearbeiter = $data['bearbeiter'];
    $freitext = $data['freitext'];
    $gueltigbis = $data['gueltigbis'];
    $datum = $data['datum'];
    $belegnr = $data['belegnr'];
    $typ = $data['typ'];
    $zahlungsweise = $data['zahlungsweise'];
    $angebotersatz = $data['angebotersatz'];
    $zahlungszieltage = $data['zahlungszieltage'];
    $zahlungszieltageskonto = $data['zahlungszieltageskonto'];

    $zahlungszielskonto = $data['zahlungszielskonto'];
    $projekt = $data['projekt'];
    $waehrung = $data['waehrung'];
    $ohne_briefpapier = $data['ohne_briefpapier'];
    $bodyzusatz = $data['bodyzusatz'];
    $datum2 = $data['datum2'];

    if(empty($kundennummer)) {
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    }
    if(empty($sprache)) {
      $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
    }

    $this->app->erp->BeschriftungSprache($sprache);
    if($waehrung) {
      $this->waehrung = $waehrung;
    }
    $this->projekt = $projekt;
    $projektabkuerzung = $this->app->DB->Select(sprintf('SELECT abkuerzung FROM projekt WHERE id = %d', $projekt));
    $this->sprache = $sprache;
    $this->anrede = $typ;

    $zahlungsweise = $this->app->erp->ReadyForPDF($zahlungsweise);
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);
    $anfrage = $this->app->erp->ReadyForPDF($anfrage);

    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }

    $zahlungstext = $this->app->erp->Zahlungsweisetext("angebot", $id);
/*

    //$zahlungstext = "\nZahlungsweise: $zahlungsweise ";
    if($zahlungsweise=="rechnung")
    {
      if($zahlungszieltage >0) $zahlungstext = $this->app->erp->Beschriftung("dokument_zahlung_rechnung_anab");
      else {
        $zahlungstext = $this->app->erp->Beschriftung("zahlung_rechnung_sofort_de");
      }


      if($this->app->erp->Firmendaten("eigener_skontotext")=="1" && $zahlungszielskonto>0)
      {      
        $skontotext = $this->app->erp->Beschriftung("eigener_skontotext_anab");
        $skontotext = str_replace('{ZAHLUNGSZIELSKONTO}',number_format($zahlungszielskonto,2,',','.'),$skontotext);
        $skontotext = str_replace('{ZAHLUNGSZIELTAGESKONTO}',$zahlungszieltageskonto,$skontotext);
        $zahlungstext .= "\n".$skontotext;
      } else {
        if($zahlungszielskonto>0) $zahlungstext .= "\n".$this->app->erp->Beschriftung("dokument_skonto")." ".number_format($zahlungszielskonto,2,',','.')."% ".$this->app->erp->Beschriftung("dokument_innerhalb")." $zahlungszieltageskonto ".$this->app->erp->Beschriftung("dokument_tagen");
      }

    } else {
      $zahlungstext = $this->app->DB->Select("SELECT freitext FROM zahlungsweisen WHERE type='".$zahlungsweise."' AND aktiv='1' AND type!='' LIMIT 1");
      if($zahlungstext=="")
        $zahlungstext = $this->app->erp->Beschriftung("zahlung_".$zahlungsweise."_de");
      if($zahlungstext=="")
        $zahlungstext = $this->app->erp->Beschriftung("dokument_zahlung_per")." ".ucfirst($zahlungsweise);
    }
*/

    $zahlungsweise = ucfirst($zahlungsweise);	
 
    if($belegnr=="" || $belegnr=="0") $belegnr = "- ".$this->app->erp->Beschriftung("dokument_entwurf");

    if($angebotersatz)
      $this->doctypeOrig=($this->app->erp->Beschriftung("bezeichnungangebotersatz")?$this->app->erp->Beschriftung("bezeichnungangebotersatz"):$this->app->erp->Beschriftung("dokument_angebot"))." $belegnr";
    else
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_angebot")." $belegnr";
    

    $this->zusatzfooter = " (AN$belegnr)";

    if($angebot=="") $angebot = "-";
    if($kundennummer=="") $kundennummer= "-";

    $bearbeiteremail = $this->app->DB->Select("SELECT b.email FROM angebot a LEFT JOIN adresse b ON b.id=a.bearbeiterid WHERE a.id='$id' LIMIT 1");
    $bearbeitertelefon = $this->app->DB->Select("SELECT b.telefon FROM angebot a LEFT JOIN adresse b ON b.id=a.bearbeiterid WHERE a.id='$id' LIMIT 1");

    /** @var \Xentral\Modules\Company\Service\DocumentCustomizationService $service */
    $service = $this->app->Container->get('DocumentCustomizationService');
    if($block = $service->findActiveBlock('corr', 'offer', $projekt)) {
      $sCD = $service->parseBlockAsArray($this->getLanguageCodeFrom($this->sprache),'corr', 'offer',[
        'ANGEBOTSNUMMER' => $belegnr,
        'DATUM'          => $datum,
        'KUNDENNUMMER'   => $kundennummer,
        'BEARBEITER'     => $bearbeiter,
        'BEARBEITEREMAIL' => $bearbeiteremail,
        'BEARBEITERTELEFON' => $bearbeitertelefon,
        'VERTRIEB'       => $vertrieb,
        'PROJEKT'        => $projektabkuerzung,
        'ANFRAGENUMMER'  => $anfrage,
        'EMAIL'          => $email,
        'TELEFON'        => $telefon
      ], $projekt);

      if(!empty($block['alignment'])) {
        $this->boxalignmentleft = $block['alignment'][0];
        $this->boxalignmentright = $block['alignment'][1];
      }

      /*$elements =explode("\n", str_replace("\r",'', $this->app->erp->Firmendaten('document_settings_angebot_elements')));
      $previewArr = [
        'DATUM'          => 'datum',
        'BEARBEITER'     => 'bearbeiter',
        'VERTRIEB'       => 'vertrieb',
        'EMAIL'          => 'email',
        'TELEFON'        => 'telefon',
        'ANGEBOTSNUMMER' => 'belegnr',
        'KUNDENNUMMER'   => 'kundennummer',
        'PROJEKT'        => 'projektabkuerzung'
      ];
      foreach($elements as $key => $el) {
        $el = trim($el);
        $elements[$key] = $el;
        foreach($previewArr as $prevKey => $preVal) {
          if(strpos($el, '{'.$prevKey.'}') !== false) {
            if(empty($$preVal)) {

              unset($elements[$key]);
              break;
            }
            $elements[$key] = trim(str_replace('{'.$prevKey.'}', $$preVal, $el));

            break;
          }
        }
      }
      $elements = explode("\n", $this->app->erp->ParseIfVars(implode("\n", $elements)));
      foreach($elements as $key => $el) {
        if(!empty($elements[$key])){
          $row = explode('|', $elements[$key], 2);
          $sCD[trim(rtrim(trim($row[0]),':'))] = !empty($row[1]) ? $row[1] : '';
        }
      }*/

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
        $sCD = array($this->app->erp->Beschriftung("dokument_angebot_anfrage") => $anfrage, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("dokument_datum") => $datum);
        if(!$briefpapier_bearbeiter_ausblenden){
          if($bearbeiter) $sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")] = $bearbeiter;
        }elseif(!$briefpapier_vertrieb_ausblenden){
          if($vertrieb) $sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")] = $vertrieb;
        }
        $this->setCorrDetails($sCD);
        //$this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot_anfrage")=>$anfrage,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("dokument_datum")=>$datum));
      }else{
        if($vertrieb == $bearbeiter){
          $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot_anfrage") => $anfrage, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("dokument_datum") => $datum, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter));
        }else{
          $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot_anfrage") => $anfrage, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("dokument_datum") => $datum, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb));
        }
      }
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

    $body=$this->app->erp->Beschriftung("angebot_header");
    if($bodyzusatz!="") $body=$body."\r\n".$bodyzusatz;


    if($this->app->erp->Firmendaten("footer_reihenfolge_angebot_aktivieren")=="1")      
    {        
      $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_angebot");        
      if($footervorlage=="")          
        $footervorlage = "{FOOTERFREITEXT}\r\n{FOOTERTEXTVORLAGEANGEBOT}\r\n{FOOTERSTEUER}\r\n{FOOTERZAHLUNGSWEISETEXT}";
      $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
      $footervorlage = str_replace('{FOOTERTEXTVORLAGEANGEBOT}',$this->app->erp->Beschriftung("angebot_footer"),$footervorlage);
      $footervorlage = str_replace('{FOOTERSTEUER}',$steuer,$footervorlage);        
      $footervorlage = str_replace('{FOOTERZAHLUNGSWEISETEXT}',$zahlungstext,$footervorlage);
      $footervorlage  = $this->app->erp->ParseUserVars("angebot",$id,$footervorlage);        
      $footer = $footervorlage;
    } else {
      $footer = "$freitext\r\n".$this->app->erp->ParseUserVars("angebot",$id,$this->app->erp->Beschriftung("angebot_footer")."\r\n$steuer\r\n$zahlungstext");
    }


    $body = $this->app->erp->ParseUserVars("angebot",$id,$body);
    $this->setTextDetails(array(
          "body"=>$body,
          "footer"=>$footer));

    $artikel = $this->app->DB->SelectArr("SELECT * FROM angebot_position WHERE angebot='$id' ORDER By sort");
    if(!$this->app->erp->AngebotMitUmsatzeuer($id)) $this->ust_befreit=true;

    $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM angebot_position WHERE angebot='$id'");
    if($summe_rabatt <> 0) $this->rabatt=1;

    if($this->app->erp->Firmendaten("modul_verband")=="1") $this->rabatt=1; 

    $summe = 0;
    $steuersatzV = $this->app->erp->GetSteuersatzNormal(false,$id,"angebot");
    $steuersatzR = $this->app->erp->GetSteuersatzErmaessigt(false,$id,"angebot");
    $gesamtsteuern = 0;
    $mitumsatzsteuer = $this->app->erp->AngebotMitUmsatzeuer($id);
    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM angebot_position WHERE angebot='$id' LIMIT 1");
    $berechnen_aus_teile = false;
    
    foreach($artikel as $key=>$value)
    {
      if($value['explodiert_parent'])
      {
        $explodiert[$value['explodiert_parent']] = true;
      }
    }
    $belege_subpositionenstuecklisten = $this->app->erp->Firmendaten('belege_subpositionenstuecklisten');
    $belege_stuecklisteneinrueckenmm = $this->app->erp->Firmendaten('belege_stuecklisteneinrueckenmm');
    //$positionenkaufmaenischrunden = $this->app->erp->Firmendaten('positionenkaufmaenischrunden');
    $positionenkaufmaenischrunden = $this->app->erp->Projektdaten($projekt,"preisberechnung");
    $viernachkommastellen_belege = $this->app->erp->Firmendaten('viernachkommastellen_belege');
    foreach($artikel as $key=>$value)
    {
      // sichtbare positionen 
      $checksichtbar = $this->app->DB->Select("SELECT COUNT(id)-SUM(ausblenden_im_pdf) FROM angebot_position WHERE explodiert_parent='".$value['id']."'");
      if(isset($explodiert) && $explodiert[$value['id']] && $checksichtbar > 0) $value['bezeichnung'] = $value['bezeichnung']." ".$this->app->erp->Beschriftung("dokument_stueckliste");
      if($value['explodiert_parent'] > 0) { 
        if($value['preis'] == 0){
          $value['preis'] = "-"; $value['umsatzsteuer']="hidden"; 
        }
        if(!$belege_subpositionenstuecklisten && !$belege_stuecklisteneinrueckenmm)$value['bezeichnung'] = "-".$value['bezeichnung'];
        //$value[beschreibung] .= $value[beschreibung]." (Bestandteil von Stückliste)"; 
      }

      $ohne_artikeltext = $this->app->DB->Select("SELECT ohne_artikeltext FROM ".$this->table." WHERE id='".$this->id."' LIMIT 1");
      if($ohne_artikeltext=="1") $value['beschreibung']="";
      
      
      if($value['explodiert_parent'] > 0)
      {
        if(isset($lvl) && isset($lvl[$value['explodiert_parent']]))
        {
          $value['lvl'] = $lvl[$value['explodiert_parent']] + 1;
        }else{
          $value['lvl'] = 1;
        }
        $lvl[$value['id']] = $value['lvl'];
      } else 
      {
        $lvl[$value['id']] = 0;
      }
      
      if($value['umsatzsteuer'] != "ermaessigt") $value['umsatzsteuer'] = "normal";
      $tmpsteuersatz = null;
      $tmpsteuertext = null;
      $this->app->erp->GetSteuerPosition('angebot', $value['id'],$tmpsteuersatz, $tmpsteuertext);
      if(is_null($value['steuersatz']) || $value['steuersatz'] < 0)
      {
        if($value['umsatzsteuer'] == "ermaessigt")
        {
          $value['steuersatz'] = $steuersatzR;
        }else{
          $value['steuersatz'] = $steuersatzV;
        }
        if(!is_null($tmpsteuersatz))$value['steuersatz'] = $tmpsteuersatz;
      }
      if($tmpsteuertext && !$value['steuertext'])$value['steuertext'] = $tmpsteuertext;
      if(!$mitumsatzsteuer)$value['steuersatz'] = 0;
      // Herstellernummer von Artikel
      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

      $is_angebot_mit_bild=0;
      if($is_angebot_mit_bild) {
          $image_tmp = $this->app->erp->GetArtikelStandardbild($value['artikel']);
          $value['image'] = $image_tmp['image'];
          $value['image_type'] = $image_tmp['extenstion'];
      }

      if($value['optional']=="1") $value['bezeichnung'] = $this->app->erp->Beschriftung("dokument_optional").$value['bezeichnung'];


      if(!$this->app->erp->Export($land))
      {
        $value['zolltarifnummer']="";
        $value['herkunftsland']="";
      }

      $value = $this->CheckPosition($value,"angebot",$this->doctypeid,$value['id']);

      $value['menge'] = floatval($value['menge']);
      if(!$value['explodiert_parent'])
      {
        if($value['berechnen_aus_teile'])
        {
          $berechnen_aus_teile = true;
        }else{
          $berechnen_aus_teile = false;
        }
      }
      $value['nicht_einrechnen'] = false;
      if($value['optional']!="1"){
        if($value['explodiert_parent'] != 0 && $berechnen_aus_teile)
        {
          $value['ohnepreis'] = 1;
          $value['nicht_einrechnen'] = true;
          $value['umsatzsteuer'] = 'hidden';
        }  
      }

      if($value['textalternativpreis']!="")
      {
          $value['preis']=$value['textalternativpreis'];
          $value['ohnepreis'] = 2;
          $value['nicht_einrechnen'] = true;
          $value['umsatzsteuer'] = 'hidden';
          $value['menge']="";
      }

      if(!$value['ausblenden_im_pdf'])$this->addItem(array(
            'belegposition'=>$value['id'],
            'currency'=>$value['waehrung'],'lvl'=>$value['lvl'],
            'amount'=>$value['menge'],
            'price'=>$value['preis'],
            'tax'=>$value['umsatzsteuer'],
            'steuersatz'=>$value['steuersatz'],
            'itemno'=>$value['nummer'],
            'artikel'=>$value['artikel'],
            'desc'=>$value['beschreibung'],
            'optional'=>$value['optional'],'nicht_einrechnen'=>$value['nicht_einrechnen'],
            'ohnepreis'=>$value['ohnepreis'],
            'unit'=>$value['einheit'],
            'hersteller'=>$value['hersteller'],
              'zolltarifnummer'=>$value['zolltarifnummer'],
              'herkunftsland'=>$value['herkunftsland'],
            'herstellernummer'=>trim($value['herstellernummer']),
            'lieferdatum'=>$value['lieferdatum'],
            'lieferdatumkw'=>$value['lieferdatumkw'],
            'artikelnummerkunde'=>$value['artikelnummerkunde'],
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
            "name"=>ltrim($value['bezeichnung']),
            "keinrabatterlaubt"=>$value['keinrabatterlaubt'],
            "rabatt"=>$value['rabatt'],
            "steuertext"=>$value['steuertext']));
      if($positionenkaufmaenischrunden == 3){
        $netto_gesamt = $value['menge'] * round($value['preis'] - ($value['preis'] / 100 * $value['rabatt']),2);
      }else{
        $netto_gesamt = $value['menge'] * ($value['preis'] - ($value['preis'] / 100 * $value['rabatt']));
      }
      if($positionenkaufmaenischrunden)
      {
        $netto_gesamt = round($netto_gesamt, 2);
      }
      if($value['optional']!="1"){
        if($value['explodiert_parent'] == 0 || !$berechnen_aus_teile)
        {
          $summe = $summe + $netto_gesamt;
          if(!isset($summen[$value['steuersatz']]))$summen[$value['steuersatz']] = 0;
          $summen[$value['steuersatz']] += ($netto_gesamt/100)*$value['steuersatz'];
          $gesamtsteuern +=($netto_gesamt/100)*$value['steuersatz'];
        }
        /*
        if($value['umsatzsteuer']=="" || $value['umsatzsteuer']=="normal")
        {
          $summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal(false,$id,"angebot"));
        }
        else {
          $summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt(false,$id,"angebot"));
        }*/
      }
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

    if($this->app->erp->AngebotMitUmsatzeuer($id))
    {
      $this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $gesamtsteuern,"summen"=>$summen,"totalTaxV"=>0,"totalTaxR"=>0));
      //$this->setTotals(array("totalArticles"=>$summe,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR,"total"=>$summe+$summeV+$summeR));
    } else {
      $this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));
    }

    /* Dateiname */
    //$tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    //$tmp_name = str_replace('.','',$tmp_name);

    $this->filename = $datum2."_AN".$belegnr.".pdf";
    $this->setBarcode($belegnr);
  }


}
