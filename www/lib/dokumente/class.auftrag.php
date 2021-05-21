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

class AuftragPDF extends BriefpapierCustom {
  public $doctype;

  function __construct($app,$projekt="",$proforma="")
  {
    $this->app=$app;
    //parent::Briefpapier();
    if($proforma=="")
    {
      $this->doctypeOrig="Auftrag";
      $this->doctype="auftrag";
    }
    else
    { 
      $this->doctypeOrig="Proformarechnung";
      $this->doctype="proforma";
    }
    parent::__construct($this->app,$projekt);
  } 


  function GetAuftrag($id)
  {
    $this->doctypeid = $id;
    $lvl = null;
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
    $this->setRecipientLieferadresse($id,"auftrag");

    $data = $this->app->DB->SelectRow(
      "SELECT adresse, kundennummer, sprache, angebotid, vertrieb, bearbeiter, projekt,
       DATE_FORMAT(datum,'%d.%m.%Y') AS datum, DATE_FORMAT(datum,'%Y%m%d') as datum2, 
       land, ustid, ust_befreit, keinsteuersatz, belegnr, freitext, typ, bodyzusatz, 
       systemfreitext, telefax, abweichendebezeichnung AS auftragersatz, waehrung, 
       zahlungsweise, zahlungszieltage, zahlungszieltageskonto, zahlungszielskonto, 
       ihrebestellnummer, ohne_briefpapier, schreibschutz, gesamtsumme , email, telefon
       FROM auftrag WHERE id='$id' LIMIT 1"
    );
    extract($data,EXTR_OVERWRITE);
    $adresse = $data['adresse'];
    $kundennummer = $data['kundennummer'];
    $sprache = $data['sprache'];
    $angebotid = $data['angebotid'];
    $vertrieb = $data['vertrieb'];
    $bearbeiter = $data['bearbeiter'];
    $datum = $data['datum'];
    $datum2 = $data['datum2'];
    $projekt = $data['projekt'];
    $land = $data['land'];
    $ustid = $data['ustid'];
    $ust_befreit = $data['ust_befreit'];
    $keinsteuersatz = $data['keinsteuersatz'];
    $belegnr = $data['belegnr'];
    $freitext = $data['freitext'];
    $typ = $data['typ'];
    $bodyzusatz = $data['bodyzusatz'];
    $systemfreitext = $data['systemfreitext'];
    $telefax = $data['telefax'];
    $auftragersatz = $data['auftragersatz'];
    $waehrung = $data['waehrung'];

    $zahlungsweise = $data['zahlungsweise'];
    $zahlungszieltage = $data['zahlungszieltage'];
    $zahlungszieltageskonto = $data['zahlungszieltageskonto'];
    $zahlungszielskonto = $data['zahlungszielskonto'];
    $ihrebestellnummer = $data['ihrebestellnummer'];
    $ohne_briefpapier = $data['ohne_briefpapier'];
    $schreibschutz = $data['schreibschutz'];
    $gesamtsumme = $data['gesamtsumme'];
    $telefon = $data['telefon'];
    $email = $data['email'];
    $projektabkuerzung = $this->app->DB->Select(sprintf('SELECT abkuerzung FROM projekt WHERE id = %d', $projekt));
    $summe = 0;
    


    // OfferNo, customerId, OfferDate

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
    $this->sprache = $sprache;
    $this->anrede = $typ;

    if($angebotid > 0)
      $angebot= $this->app->DB->Select("SELECT belegnr FROM angebot WHERE id='$angebotid' LIMIT 1");
    else
      $angebot ="";

    $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);



    if($belegnr=="" || $belegnr=="0") $belegnr = "- ".$this->app->erp->Beschriftung("dokument_entwurf");

    if($this->doctype=="auftrag")
    {
      if($auftragersatz)
        $this->doctypeOrig=($this->app->erp->Beschriftung("bezeichnungauftragersatz")?$this->app->erp->Beschriftung("bezeichnungauftragersatz"):$this->app->erp->Beschriftung("dokument_auftrag_auftragsbestaetigung"))." $belegnr";
      else
        $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_auftrag_auftragsbestaetigung")." $belegnr";
    }
    else
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_proformarechnung")." $belegnr";

    $this->zusatzfooter = " (AB$belegnr)";

    $auftrag = "-";
    if($kundennummer=="") $kundennummer= "-";



    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }

    $bearbeiteremail = $this->app->DB->Select("SELECT b.email FROM auftrag a LEFT JOIN adresse b ON b.id=a.bearbeiterid WHERE a.id='$id' LIMIT 1");
    $bearbeitertelefon = $this->app->DB->Select("SELECT b.telefon FROM auftrag a LEFT JOIN adresse b ON b.id=a.bearbeiterid WHERE a.id='$id' LIMIT 1");


    $zahlungstext = $this->app->erp->Zahlungsweisetext("auftrag", $id);

    //$zahlungsweise = ucfirst($zahlungsweise);
    /** @var \Xentral\Modules\Company\Service\DocumentCustomizationService $service */
    $service = $this->app->Container->get('DocumentCustomizationService');
    if($block = $service->findActiveBlock('corr', 'order', $projekt)) {
      $sCD = $service->parseBlockAsArray($this->getLanguageCodeFrom($this->sprache),'corr', 'order',[
        'AUFTRAGSNUMMER' => $belegnr,
        'DATUM'          => $datum,
        'KUNDENNUMMER'   => $kundennummer,
        'BEARBEITER'     => $bearbeiter,
        'BEARBEITEREMAIL' => $bearbeiteremail,
        'BEARBEITERTELEFON' => $bearbeitertelefon,
        'VERTRIEB'       => $vertrieb,
        'PROJEKT'        => $projektabkuerzung,
        'IHREBESTELLNUMMER'  => $ihrebestellnummer,
        'ANGEBOTSNUMMER' => $angebot,
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
        if(!empty($block['alignment'])) {
          $this->boxalignmentleft = $block['alignment'][0];
          $this->boxalignmentright = $block['alignment'][1];
        }
      }
    }
    else{

      if($telefax != ""){
        if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden){
          $scD = array($this->app->erp->Beschriftung("dokument_angebot") => $angebot, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_auftragsdatum") => $datum);
          if(!$briefpapier_bearbeiter_ausblenden){
            if($bearbeiter) $scD[$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")] = $bearbeiter;
          }elseif(!$briefpapier_vertrieb_ausblenden){
            if($vertrieb) $scD[$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")] = $vertrieb;
          }
          $this->setCorrDetails($scD);
          //$this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot")=>$angebot,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,$this->app->erp->Beschriftung("dokument_auftragsdatum")=>$datum));
        }else{
          if($vertrieb != $bearbeiter)
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot") => $angebot, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer,
              $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_auftragsdatum") => $datum, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb));
          else
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot") => $angebot, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_auftragsdatum") => $datum, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter));
        }
      }else{
        if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden){
          $scD = array($this->app->erp->Beschriftung("dokument_angebot") => $angebot, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_auftragsdatum") => $datum);
          if(!$briefpapier_bearbeiter_ausblenden){
            if($bearbeiter) $scD[$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")] = $bearbeiter;
          }elseif(!$briefpapier_vertrieb_ausblenden){
            if($vertrieb) $scD[$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")] = $vertrieb;
          }
          $this->setCorrDetails($scD);
          //$this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot")=>$angebot,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,$this->app->erp->Beschriftung("dokument_auftragsdatum")=>$datum));
        }else{
          if($vertrieb != $bearbeiter)
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot") => $angebot, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_auftragsdatum") => $datum, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb));
          else
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_angebot") => $angebot, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer, $this->app->erp->Beschriftung("dokument_auftragsdatum") => $datum, $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter));
        }
      }
    }

    if($keinsteuersatz!="1")
    { 
      if($ust_befreit==2)//$this->app->erp->Export($land))
          $steuerzeile = $this->app->erp->Beschriftung("export_lieferung_vermerk");
      else {
        if($ust_befreit==1 && $ustid!="")//$this->app->erp->IstEU($land))
           $steuerzeile = $this->app->erp->Beschriftung("eu_lieferung_vermerk");
      }

      $kennungustid = substr(strtoupper($ustid),0,2);
      if(($kennungustid!=strtoupper($land)) && $kennungustid!="" && $this->app->erp->IsEU($kennungustid))
      { 
        $steuerzeile = str_replace('{LAND}',$kennungustid,$steuerzeile);
      } else {
        $steuerzeile = str_replace('{LAND}',$land,$steuerzeile);
      }

      $steuerzeile = str_replace('{USTID}',$ustid,$steuerzeile);
    }

      if($systemfreitext!="") $systemfreitext = "\r\n\r\n".$systemfreitext;


      if($this->app->erp->Firmendaten("footer_reihenfolge_auftrag_aktivieren")=="1")
      {
        $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_auftrag");
        if($footervorlage=="")          
          $footervorlage = "{FOOTERFREITEXT}\r\n{FOOTERTEXTVORLAGEAUFTRAG}\r\n{FOOTERSTEUER}\r\n{FOOTERZAHLUNGSWEISETEXT}{FOOTERSYSTEMFREITEXT}";        
        $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
        $footervorlage = str_replace('{FOOTERTEXTVORLAGEAUFTRAG}',$this->app->erp->Beschriftung("auftrag_footer"),$footervorlage);
        $footervorlage = str_replace('{FOOTERSTEUER}',$steuerzeile,$footervorlage);        
        $footervorlage = str_replace('{FOOTERZAHLUNGSWEISETEXT}',$zahlungstext,$footervorlage);
        $footervorlage = str_replace('{FOOTERSYSTEMFREITEXT}',$systemfreitext,$footervorlage);
        $footervorlage  = $this->app->erp->ParseUserVars("auftrag",$id,$footervorlage);
        $footer = $footervorlage;
      } else {        
        $footer = "$freitext\r\n".$this->app->erp->ParseUserVars("auftrag",$id,$this->app->erp->Beschriftung("auftrag_footer")."\r\n$steuerzeile\r\n$zahlungstext").$systemfreitext;
      }



    $body=$this->app->erp->Beschriftung("auftrag_header");
    if($bodyzusatz!="") $body=$body."\r\n".$bodyzusatz;
    $body = $this->app->erp->ParseUserVars("auftrag",$id,$body);
    $this->setTextDetails(array(
          "body"=>$body,
          "footer"=>$footer));

    if(!$this->app->erp->AuftragMitUmsatzeuer($id)) {
      $this->ust_befreit=true;
    }

    $artikel = $this->app->DB->SelectArr(
      sprintf(
        'SELECT * FROM auftrag_position WHERE auftrag=%d ORDER BY sort',
        $id
      )
    );
    $summe_rabatt = 0.0;
    $explodiertParents = [];
    if(!empty($artikel)) {
      foreach($artikel as $art) {
        $summe_rabatt += (float)$art['rabatt'];
        if($art['explodiert_parent'] > 0) {
          $explodiertParents[$art['explodiert_parent']][] = $art;
        }
      }
    }
    $this->app->erp->RunHook('BriefpapierGetAuftragArtikel',3,$id, $artikel, $this);
    //$summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM auftrag_position WHERE auftrag='$id'");
    if($summe_rabatt <> 0) {
      $this->rabatt=1;
    }

    if($this->app->erp->Firmendaten("modul_verband")=="1") {
      $this->rabatt=1;
    }
    
    
    $steuersatzV = $this->app->erp->GetSteuersatzNormal(false,$id,"auftrag");
    
    $steuersatzR = $this->app->erp->GetSteuersatzErmaessigt(false,$id,"auftrag");
    
    $gesamtsteuern = 0;
    $mitumsatzsteuer = $this->app->erp->AuftragMitUmsatzeuer($id);
    $belege_subpositionenstuecklisten = $this->app->erp->Firmendaten('belege_subpositionenstuecklisten');
    $belege_stuecklisteneinrueckenmm = $this->app->erp->Firmendaten('belege_stuecklisteneinrueckenmm');
    if(!$schreibschutz) {
      $gesamtsumme = 0;
    }
    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM auftrag_position WHERE auftrag='$id' LIMIT 1");
    //$positionenkaufmaenischrunden = $this->app->erp->Firmendaten('positionenkaufmaenischrunden');
    $projekt = $this->app->DB->Select("SELECT projekt FROM auftrag WHERE id = '$id' LIMIT 1");
    $positionenkaufmaenischrunden = $this->app->erp->Projektdaten($projekt,"preisberechnung");
    $viernachkommastellen_belege = $this->app->erp->Firmendaten('viernachkommastellen_belege');

    $ohne_artikeltext = $this->app->DB->Select("SELECT ohne_artikeltext FROM ".$this->table." WHERE id='".$this->id."' LIMIT 1");
    $beschriftungStueckliste = $this->app->erp->Beschriftung('dokument_stueckliste');
    foreach($artikel as $key=>$value)  {
      if($value['umsatzsteuer'] !== 'ermaessigt' && $value['umsatzsteuer'] !== 'befreit') {
        $value['umsatzsteuer'] = 'normal';
      }
      $tmpsteuersatz = null;
      $tmpsteuertext = null;
      $this->app->erp->GetSteuerPosition('auftrag', $value['id'],$tmpsteuersatz, $tmpsteuertext);
      if($value['steuersatz'] === null || $value['steuersatz'] < 0) {
        if($value['umsatzsteuer'] === 'ermaessigt') {
          $value['steuersatz'] = $steuersatzR;
        }
        elseif($value['umsatzsteuer'] === 'befreit') {
          $value['steuersatz'] = 0;
        }
        else {
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
      
      //if(!$this->app->erp->AuftragMitUmsatzeuer($id)) $value[umsatzsteuer] = ""; 
      $time1 = microtime(true);

      $checksichtbar = 0;
      if(!empty($explodiertParents[$value['id']])) {

        foreach($explodiertParents[$value['id']] as $expPos) {
          if(!$expPos['ausblenden_im_pdf']) {
            $checksichtbar++;
          }
        }

        /*$checksichtbar = $this->app->DB->Select(
          sprintf(
            "SELECT COUNT(id)-SUM(ausblenden_im_pdf)
          FROM auftrag_position 
          WHERE explodiert_parent=%d",
            $value['id']
          )
        );*/
      }

      if($value['explodiert'] > 0 && $checksichtbar > 0) {
        $value['bezeichnung'] = $value['bezeichnung']." ".$beschriftungStueckliste;
      }
      if($value['explodiert_parent'] > 0) { 
        if($value['preis'] == 0) {
          $value['preis'] = "-"; $value['umsatzsteuer']="hidden"; 
        }
        if(!$belege_subpositionenstuecklisten && !$belege_stuecklisteneinrueckenmm) {
          $value['bezeichnung'] = "-".$value['bezeichnung'];
        }
        //$value[beschreibung] .= $value[beschreibung]." (Bestandteil von Stückliste)"; 
      }


      if($ohne_artikeltext=="1") {
        $value['beschreibung']="";
      }

      $artArr = $this->app->DB->SelectRow(
        sprintf(
          'SELECT herstellernummer, hersteller, keineeinzelartikelanzeigen FROM artikel WHERE id = %d',
          $value['artikel']
        )
      );

      // Herstellernummer von Artikel
      $value['herstellernummer'] = $artArr['herstellernummer'];// $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $artArr['hersteller'];//$this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

      $value['keineeinzelartikelanzeigen'] = $artArr['keineeinzelartikelanzeigen'];//$this->app->DB->Select("SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['anzahlunterartikel'] =  !empty($explodiertParents[$value['id']])?count($explodiertParents[$value['id']]):null;  //$this->app->DB->Select("SELECT COUNT(id) FROM auftrag_position WHERE explodiert_parent = '".$value['id']."'");

      if($value['explodiert_parent'] > 0) {
        if(isset($lvl[$value['explodiert_parent']]))
        {
          $value['lvl'] = $lvl[$value['explodiert_parent']] + 1;
          if(!$belege_subpositionenstuecklisten)
          {
            if($value['lvl'] > 1)$value['bezeichnung'] = str_repeat("-", $value['lvl'] - 1).$value['bezeichnung'];
          }
        }else{
          $value['lvl'] = 1;
        }
        $lvl[$value['id']] = $value['lvl'];
        $artikelid_tmp = $this->app->DB->Select("SELECT artikel FROM auftrag_position WHERE id='".$value['explodiert_parent']."' LIMIT 1");
        $check_ausblenden = $this->app->DB->Select("SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$artikelid_tmp."' LIMIT 1");
        if(!$check_ausblenden && isset($ausblenden) && in_array($value['explodiert_parent'], $ausblenden))
        {
          $check_ausblenden = true;
        }
        if($check_ausblenden)
        {
          $ausblenden[] = $value['id'];
        }
      }	else {
        $check_ausblenden=0;
        $lvl[$value['id']] = 0;
      }
      $time3 += microtime(true) - $time1;

      if($value['ausblenden_im_pdf']) {
        $check_ausblenden=1;
      }


      if(!$this->app->erp->Export($land)) {
        $value['zolltarifnummer']='';
        $value['herkunftsland']='';
      }

      $value['menge'] = (float)$value['menge'];
      $value = $this->CheckPosition($value,"auftrag",$this->doctypeid,$value['id']);
      if($check_ausblenden!=1) {
        $this->addItem(array(
              'belegposition'=>$value['id'],
              'currency'=>$value['waehrung'],'lvl'=>$value['lvl'],
              'amount'=>$value['menge'],
              'price'=>$value['preis'],
              'tax'=>$value['umsatzsteuer'],
              'steuersatz'=>$value['steuersatz'],
              'steuertext'=>$value['steuertext'],
              'itemno'=>$value['nummer'],
              'artikel'=>$value['artikel'],
              'unit'=>$value['einheit'],
              'desc'=>$value['beschreibung'],
              'hersteller'=>trim($value['hersteller']),
              'herstellernummer'=>trim($value['herstellernummer']),
              'zolltarifnummer'=>$value['zolltarifnummer'],
              'herkunftsland'=>$value['herkunftsland'],
              'lieferdatum'=>$value['lieferdatum'],
              'lieferdatumkw'=>$value['lieferdatumkw'],
              'artikelnummerkunde'=>$value['artikelnummerkunde'],
            'ohnepreis'=>$value['ohnepreis'],
              'keinrabatterlaubt'=>$value['keinrabatterlaubt'],
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
              "keineeinzelartikelanzeigen"=>$value['keineeinzelartikelanzeigen'],
              "anzahlunterartikel"=>$value['anzahlunterartikel']));
      }
      if($positionenkaufmaenischrunden == 3) {
        $netto_gesamt = $value['menge'] * round($value['preis'] - ($value['preis'] / 100 * $value['rabatt']),2);
      }
      else {
        $netto_gesamt = $value['menge'] * ($value['preis'] - ($value['preis'] / 100 * $value['rabatt']));
      }
      if($positionenkaufmaenischrunden) {
        $netto_gesamt = round($netto_gesamt, 2);
      }
      $summe = $summe + $netto_gesamt;
      if(!isset($summen[$value['steuersatz']])) {
        $summen[$value['steuersatz']] = 0;
      }
      $summen[$value['steuersatz']] += ($netto_gesamt/100)*$value['steuersatz'];
      $gesamtsteuern +=($netto_gesamt/100)*$value['steuersatz'];
    }

    if($positionenkaufmaenischrunden && isset($summen) && is_array($summen)) {
      $gesamtsteuern = 0;
      foreach($summen as $k => $v) {
        $summen[$k] = round($v, 2);
        $gesamtsteuern += round($v, 2);
      }
    }
    if($positionenkaufmaenischrunden) {
      list($summe,$gesamtsumme, $summen) = $this->app->erp->steuerAusBelegPDF($this->table, $this->id);
      $gesamtsteuern = $gesamtsumme - $summe;
    }
    if($this->app->erp->AuftragMitUmsatzeuer($id)) {
      $this->setTotals(
        [
          'totalArticles' => $summe,
          'total'         => ($gesamtsumme != 0?$gesamtsumme:( $summe + $gesamtsteuern)),
          'summen'        =>$summen,
          'totalTaxV'     =>0,
          'totalTaxR'     =>0
        ]
      );
    }
    else {
      $this->setTotals(
        [
          'totalArticles' => ($gesamtsumme != 0 ? $gesamtsumme : $summe),
          'total'         => ($gesamtsumme != 0 ? $gesamtsumme : $summe)
        ]
      );
    }
    
    /* Dateiname */

    $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    $tmp_name = str_replace('.','',$tmp_name);

    if($this->doctype=='auftrag') {
      $this->filename = $datum2 . '_AB' . $belegnr . '.pdf';
    }
    else{
      $this->filename = $datum2 . '_PR' . $belegnr . '.pdf';
    }
    $this->setBarcode($belegnr);
    $this->app->erp->RunHook('BreifpapierGetAuftragEnde',3,$id, $artikel, $this);
  }

}
