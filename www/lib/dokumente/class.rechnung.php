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

class RechnungPDF extends BriefpapierCustom {
  /** @var string $doctype */
  public $doctype;
  /** @var int $doctypeid */
  public $doctypeid;

  /**
   * RechnungPDF constructor.
   *
   * @param Application $app
   * @param string|int  $projekt
   * @param array       $styleData
   */
  public function __construct($app,$projekt="",$styleData=null)
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype="rechnung";
    $this->doctypeOrig="Rechnung";
    parent::__construct($this->app,$projekt,$styleData);
  }

  public function GetRechnung($id,$als="",$doppeltmp=0, $_datum = null)
  {
    $this->parameter = $als;
    if($this->app->erp->Firmendaten("steuerspalteausblenden")=="1")
    { 
      // pruefe ob es mehr als ein steuersatz gibt // wenn ja dann darf man sie nicht ausblenden
      $check = $this->app->erp->SteuerAusBeleg($this->doctype,$id);
      if(count($check)>1)$this->ust_spalteausblende=false;
      else $this->ust_spalteausblende=true;
    }
    $lvl = null;
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    $this->doctypeid=$id;
    //      $this->setRecipientDB($adresse);
    $this->setRecipientLieferadresse($id,"rechnung");

    $data = $this->app->DB->SelectRow(
      "SELECT r.adresse, if(r.auftragid > 0,a.belegnr,r.auftrag) as auftrag, r.buchhaltung, r.bearbeiter, 
       r.vertrieb, r.lieferschein AS lieferscheinid, r.projekt, DATE_FORMAT(r.datum,'%d.%m.%Y') AS datum, 
       DATE_FORMAT(r.mahnwesen_datum,'%d.%m.%Y') AS mahnwesen_datum, 
       DATE_FORMAT(r.lieferdatum,'%d.%m.%Y') AS lieferdatum, r.belegnr, r.bodyzusatz, r.doppel, 
       r.freitext, r.systemfreitext, r.ustid, r.typ, r.keinsteuersatz, r.soll, r.ist, r.land, 
       r.zahlungsweise, r.zahlungsstatus, r.zahlungszieltage, r.zahlungszieltageskonto, 
       r.zahlungszielskonto, r.ohne_briefpapier, r.ihrebestellnummer, r.ust_befreit, r.waehrung, 
       r.versandart, 
       DATE_FORMAT(DATE_ADD(r.datum, INTERVAL r.zahlungszieltage DAY),'%d.%m.%Y') AS zahlungdatum, 
       DATE_FORMAT(DATE_ADD(r.datum, INTERVAL r.zahlungszieltageskonto DAY),'%d.%m.%Y') AS zahlungszielskontodatum, 
       r.abweichendebezeichnung AS rechnungersatz, 
       r.kundennummer, r.sprache, r.schreibschutz, r.soll AS gesamtsumme,
       DATE_FORMAT(r.datum,'%Y%m%d') as datum2, r.telefon, r.email
       FROM rechnung r LEFT JOIN auftrag a ON a.id=r.auftragid WHERE r.id='$id' LIMIT 1"
    );
    extract($data,EXTR_OVERWRITE);
    $adresse = $data['adresse'];
    $auftrag = $data['auftrag'];
    $buchhaltung = $data['buchhaltung'];
    $bearbeiter = $data['bearbeiter'];
    $vertrieb = $data['vertrieb'];
    $lieferscheinid = $data['lieferscheinid'];
    $projekt = $data['projekt'];
    $datum = $data['datum'];
    $mahnwesen_datum = $data['mahnwesen_datum'];
    $lieferdatum = $data['lieferdatum'];
    $belegnr = $data['belegnr'];
    $bodyzusatz = $data['bodyzusatz'];
    $doppel = $data['doppel'];
    $freitext = $data['freitext'];
    $systemfreitext = $data['systemfreitext'];
    $ustid = $data['ustid'];
    $typ = $data['typ'];
    $keinsteuersatz = $data['keinsteuersatz'];
    $soll = $data['soll'];
    $ist = $data['ist'];
    $soll = $data['soll'];
    $land = $data['land'];
    $zahlungsweise = $data['zahlungsweise'];
    $zahlungsstatus = $data['zahlungsstatus'];
    $zahlungszieltage = $data['zahlungszieltage'];
    $zahlungszieltageskonto = $data['zahlungszieltageskonto'];
    $zahlungszielskonto = $data['zahlungszielskonto'];
    $versandart = $data['versandart'];
    $zahlungdatum = $data['zahlungdatum'];
    $zahlungszielskontodatum = $data['zahlungszielskontodatum'];

    $ihrebestellnummer = $data['ihrebestellnummer'];
    $ust_befreit = $data['ust_befreit'];
    $waehrung = $data['waehrung'];
    $ohne_briefpapier = $data['ohne_briefpapier'];

    $rechnungersatz = $data['rechnungersatz'];
    $kundennummer = $data['kundennummer'];
    $sprache = $data['sprache'];
    $schreibschutz = $data['schreibschutz'];
    $gesamtsumme = $data['gesamtsumme'];
    $datum2 = $data['datum2'];
    $email = $data['email'];
    $telefon = $data['telefon'];

    $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");

    if(empty($als) || $als === 'doppel') {
      $rechnungsnummeranzeigen = false;
    }
    elseif(!empty($belegnr)){
      $rechnungsnummeranzeigen = true;
    }
    $projektabkuerzung = $this->app->DB->Select(sprintf('SELECT abkuerzung FROM projekt WHERE id = %d', $projekt));
    if(empty($kundennummer)) {
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
    }
    if(empty($sprache)) {
      $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
    }

    $trackingnummer = $this->app->DB->Select("SELECT tracking FROM versand WHERE rechnung='$id' LIMIT 1");

    $this->app->erp->BeschriftungSprache($sprache);
    if($waehrung) {
      $this->waehrung = $waehrung;
    }
    if($doppeltmp == 1) {
      $doppel = $doppeltmp;
    }
    $this->sprache = $sprache;
    $this->projekt = $projekt;
    $this->anrede = $typ;  

    $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);

    if($ohne_briefpapier=="1")
    {
      $this->logofile = "";
      $this->briefpapier="";
      $this->briefpapier2="";
    }

    if($zahlungszieltageskonto<=0)
      $zahlungszielskontodatum = $zahlungdatum;

    if(!$this->app->erp->RechnungMitUmsatzeuer($id)){
      $this->ust_befreit=true;
    }

    $zahlungsweisetext = $this->app->erp->Zahlungsweisetext("rechnung",$id);

    
    if($doppel==1) $als = "doppel";

    if($belegnr=="" || $belegnr=="0") $belegnr = "- ".$this->app->erp->Beschriftung("dokument_entwurf");
    else {
      if($doppel==1 || $als=="doppel")
        $belegnr .= " (".$this->app->erp->Beschriftung("dokument_rechnung_kopie").")";
    }

    $posanzeigen = true;    
    if($als=="zahlungserinnerung")
    {
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_zahlungserinnerung")." ".(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration("mahnwesen_ze_pos") === '0')$posanzeigen = false;
    }
    else if($als=="mahnung1") 
    {
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_mahnung1")." ".(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration("mahnwesen_1_pos") === '0')$posanzeigen = false;
    }
    else if($als=="mahnung2")
    {
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_mahnung2")." ".(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration("mahnwesen_2_pos") === '0')$posanzeigen = false;
    }
    else if($als=="mahnung3") 
    {
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_mahnung3")." ".(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration("mahnwesen_3_pos") === '0')$posanzeigen = false;
    }
    else if($als=="inkasso") 
    {
      $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_mahnunginkasso")." ".(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration("mahnwesen_inkasso_pos") === '0')$posanzeigen = false;
    }
    else
    {
      if($rechnungersatz)
        $this->doctypeOrig=($this->app->erp->Beschriftung("bezeichnungrechnungersatz")?$this->app->erp->Beschriftung("bezeichnungrechnungersatz"):$this->app->erp->Beschriftung("dokument_rechnung"))." $belegnr";
      else
        $this->doctypeOrig=$this->app->erp->Beschriftung("dokument_rechnung")." $belegnr";
    }



    $this->zusatzfooter = " (RE$belegnr)";

    $rechnung = "-";
    if($kundennummer=="") $kundennummer= "-";

    if($auftrag=="0") $auftrag = "-";
    if($lieferschein=="0") $lieferschein= "-";
    if($lieferschein=="") $lieferschein= "-";

    $datumlieferschein = $this->app->DB->Select("SELECT DATE_FORMAT(datum, '%d.%m.%Y') 
        FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");

    if($datumlieferschein=="00.00.0000") $datumlieferschein = $datum;
    if($lieferdatum=="00.00.0000") $lieferdatum = $datum;
    if($mahnwesen_datum=="00.00.0000") $mahnwesen_datum = "";

    $bearbeiteremail = $this->app->DB->Select("SELECT b.email FROM rechnung r LEFT JOIN adresse b ON b.id=r.bearbeiterid WHERE r.id='$id' LIMIT 1");
    $bearbeitertelefon = $this->app->DB->Select("SELECT b.telefon FROM rechnung r LEFT JOIN adresse b ON b.id=r.bearbeiterid WHERE r.id='$id' LIMIT 1");

    //* start
    if($rechnungsnummeranzeigen)
    {
      $sCD = array($this->app->erp->Beschriftung("dokument_rechnung")=>$belegnr);
    }else{
      $sCD = array();
    }

    /** @var \Xentral\Modules\Company\Service\DocumentCustomizationService $service */
    $service = $this->app->Container->get('DocumentCustomizationService');
    if($block = $service->findActiveBlock('corr', 'invoice', $projekt)) {
      $sCD = $service->parseBlockAsArray($this->getLanguageCodeFrom($this->sprache),'corr', 'invoice',[
        'RECHNUNGSNUMMER' => $belegnr,
        'DATUM'          => $datum,
        'KUNDENNUMMER'   => $kundennummer,
        'BEARBEITER'     => $bearbeiter,
        'BEARBEITEREMAIL' => $bearbeiteremail,
        'BEARBEITERTELEFON' => $bearbeitertelefon,
        'VERTRIEB'       => $vertrieb,
        'PROJEKT'        => $projektabkuerzung,
        'IHREBESTELLNUMMER'  => $ihrebestellnummer,
        'AUFTRAGSNUMMER' => $auftrag,
        'LIEFERSCHEINNUMMER'   => $lieferschein,
        'LIEFERSCHEINDATUM' => $datumlieferschein,
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
      if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden) {
        if($lieferschein!='-') {
          if($auftrag!="-") {
            $sCD = array_merge($sCD, array($this->app->erp->Beschriftung("dokument_auftrag")=>$auftrag,$this->app->erp->Beschriftung("dokument_rechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferschein")=>$lieferschein,$this->app->erp->Beschriftung("dokument_lieferdatum")=>$datumlieferschein
                  ));
          }
          else {
            $sCD = array_merge($sCD, array($this->app->erp->Beschriftung("dokument_rechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferschein")=>$lieferschein,$this->app->erp->Beschriftung("dokument_lieferdatum")=>$datumlieferschein
                  ));
          }
        }
        else {
          if($auftrag!='-') {
            $sCD = array_merge($sCD, array($this->app->erp->Beschriftung("dokument_auftrag")=>$auftrag,$this->app->erp->Beschriftung("dokument_rechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferdatum")=>$lieferdatum
                  ));
          }
          else {
            $sCD = array_merge($sCD, array($this->app->erp->Beschriftung("dokument_rechnungsdatum")=>$datum,$this->app->erp->Beschriftung("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
                  $this->app->erp->Beschriftung("dokument_lieferdatum")=>$lieferdatum,
                  $this->app->erp->Beschriftung("dokument_ansprechpartner")=>$buchhaltung
                  ));
          }
        }
        if(!$briefpapier_bearbeiter_ausblenden) {
          if($bearbeiter) {
            $sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter")] = $bearbeiter;
          }
        }
        elseif(!$briefpapier_vertrieb_ausblenden) {
          if($vertrieb)$sCD[$this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb")] = $vertrieb;
        }
        $this->setCorrDetails($sCD);
      }
      else {
        if($vertrieb!=$bearbeiter) {
          if($lieferschein!='-') {
            if($auftrag!='-'){
              $this->setCorrDetails(array_merge($sCD, array($this->app->erp->Beschriftung("dokument_auftrag") => $auftrag, $this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferschein") => $lieferschein, $this->app->erp->Beschriftung("dokument_lieferdatum") => $datumlieferschein,
                $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb
              )));
            }
            else{
              $this->setCorrDetails(array_merge($sCD, array($this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferschein") => $lieferschein, $this->app->erp->Beschriftung("dokument_lieferdatum") => $datumlieferschein,
                $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb
              )));
            }
          }
          else {
            if($auftrag!='-') {
              $this->setCorrDetails(array_merge($sCD, array($this->app->erp->Beschriftung("dokument_auftrag") => $auftrag, $this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferdatum") => $lieferdatum,
                $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb
              )));
            }
            else {
              $this->setCorrDetails(array_merge($sCD, array($this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferdatum") => $lieferdatum,
                $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb
              )));
            }
          }
          //*ende hack
        } else {
          //start hack
          if($lieferschein!='-') {
            if($auftrag!='-') {
              $this->setCorrDetails(array_merge($sCD, array($this->app->erp->Beschriftung("dokument_auftrag") => $auftrag, $this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferschein") => $lieferschein, $this->app->erp->Beschriftung("dokument_lieferdatum") => $datumlieferschein,
                $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter
              )));
            }
            else{
              $this->setCorrDetails(array_merge($sCD, array($this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferschein") => $lieferschein, $this->app->erp->Beschriftung("dokument_lieferdatum") => $datumlieferschein,
                $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter
              )));
            }
          }
          else {
            if($auftrag!='-') {
              $this->setCorrDetails(array_merge($sCD, array($this->app->erp->Beschriftung("dokument_auftrag") => $auftrag, $this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferdatum") => $lieferdatum,
                $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter
              )));
            }
            else {
              $this->setCorrDetails(array_merge($sCD, array($this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,
                $this->app->erp->Beschriftung("dokument_lieferdatum") => $lieferdatum,
                $this->app->erp->Beschriftung("dokument_ansprechpartner") => $buchhaltung
              )));
            }
          }
        }
      }
    }

    if($keinsteuersatz!="1")
    {
      if($ust_befreit!=0) $this->ust_befreit=true;
      if($keinsteuersatz!="1"){
        if($ust_befreit==2)//$this->app->erp->Export($land))
          $steuer = $this->app->erp->Beschriftung("export_lieferung_vermerk");
        else {
          if($ust_befreit==1 && $ustid!="")//$this->app->erp->IstEU($land))
            $steuer = $this->app->erp->Beschriftung("eu_lieferung_vermerk");
        }

        $kennungustid = substr(strtoupper($ustid),0,2);
        if(($kennungustid!=strtoupper($land)) && $kennungustid!="" && $this->app->erp->IsEU($kennungustid))
        { 
          $steuer = str_replace('{LAND}',$kennungustid,$steuer);
        }
        else {
          $steuer = str_replace('{LAND}',$land,$steuer);
        }

        $steuer = str_replace('{USTID}',$ustid,$steuer);
      }
    }



    if($als!="" && $als!="doppel")
    {
      $body = $this->app->erp->MahnwesenBody($id,$als,$_datum);
      $footer =$this->app->erp->ParseUserVars("rechnung",$id, $this->app->erp->Beschriftung("rechnung_footer"));
    }
    else {
      $body = $this->app->erp->Beschriftung("rechnung_header");
      if($bodyzusatz!="") $body=$body."\r\n".$bodyzusatz;
      $body = $this->app->erp->ParseUserVars("rechnung",$id,$body);

      if ($versandart!="" && $trackingnummer!="" && $this->app->erp->Firmendaten("festetrackingnummer")=="1"){
        $versandinfo = "$versandart: $trackingnummer\r\n";
      }else{ $versandinfo ="";}


      if($systemfreitext!="") $systemfreitext = "\r\n\r\n".$systemfreitext;

      if($this->app->erp->Firmendaten("footer_reihenfolge_rechnung_aktivieren")=="1")
      {
        $footervorlage = $this->app->erp->Firmendaten("footer_reihenfolge_rechnung");
        if($footervorlage=="")
          $footervorlage = "{FOOTERVERSANDINFO}{FOOTERFREITEXT}\r\n{FOOTERTEXTVORLAGERECHNUNG}\r\n{FOOTERSTEUER}\r\n{FOOTERZAHLUNGSWEISETEXT}{FOOTERSYSTEMFREITEXT}";

        $footervorlage = str_replace('{FOOTERVERSANDINFO}',$versandinfo,$footervorlage);
        $footervorlage = str_replace('{FOOTERFREITEXT}',$freitext,$footervorlage);
        $footervorlage = str_replace('{FOOTERTEXTVORLAGERECHNUNG}',$this->app->erp->Beschriftung("rechnung_footer"),$footervorlage);
        $footervorlage = str_replace('{FOOTERSTEUER}',$steuer,$footervorlage);
        $footervorlage = str_replace('{FOOTERZAHLUNGSWEISETEXT}',$zahlungsweisetext,$footervorlage);
        $footervorlage = str_replace('{FOOTERSYSTEMFREITEXT}',$systemfreitext,$footervorlage);
        $footervorlage  = $this->app->erp->ParseUserVars("rechnung",$id,$footervorlage);
        $footer = $footervorlage;
      } else {
        $footer = $versandinfo."$freitext"."\r\n".$this->app->erp->ParseUserVars("rechnung",$id,$this->app->erp->Beschriftung("rechnung_footer").
          "\r\n$steuer\r\n$zahlungsweisetext").$systemfreitext;
      }
    }

    $this->setTextDetails(array(
          "body"=>$body,
          "footer"=>$footer));

    $artikel = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung='$id' ORDER By sort");
    $this->app->erp->RunHook('rechnungpdf_getrechnung', 2, $id, $artikel);
    $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM rechnung_position WHERE rechnung='$id'");
    if($summe_rabatt <> 0) $this->rabatt=1;

    if($this->app->erp->Firmendaten("modul_verband")=="1") $this->rabatt=1; 
    $steuersatzV = $this->app->erp->GetSteuersatzNormal(false,$id,"rechnung");
    $steuersatzR = $this->app->erp->GetSteuersatzErmaessigt(false,$id,"rechnung");
    $gesamtsteuern = 0;
    $mitumsatzsteuer = $this->app->erp->RechnungMitUmsatzeuer($id);

    $gesamtsumme = 0;
    $summe = 0;
    $belege_subpositionenstuecklisten = $this->app->erp->Firmendaten('belege_subpositionenstuecklisten');
    $belege_stuecklisteneinrueckenmm = $this->app->erp->Firmendaten('belege_stuecklisteneinrueckenmm');
    if(!$schreibschutz)$gesamtsumme = 0;
    $lastausblenden = false;
    foreach($artikel as $key=>$value)
    {
      $artikel[$key]['anzahlunterartikel'] = 0;
      $artikel[$key]['keineeinzelartikelanzeigen'] = $this->app->DB->Select("SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      if($lastausblenden === false)
      {
        if($artikel[$key]['keineeinzelartikelanzeigen'])$lastausblenden = $key;
      }else{
        if($value['explodiert_parent_artikel'] > 0)
        {
          $artikel[$lastausblenden]['anzahlunterartikel']++;
        }elseif($artikel[$key]['keineeinzelartikelanzeigen']){
          $lastausblenden = $key;
        }else $lastausblenden = false;
      }
    }
    //$positionenkaufmaenischrunden = $this->app->erp->Firmendaten('positionenkaufmaenischrunden');
    $positionenkaufmaenischrunden = $this->app->erp->Projektdaten($projekt,"preisberechnung");
    $viernachkommastellen_belege = $this->app->erp->Firmendaten('viernachkommastellen_belege');
    foreach($artikel as $key=>$value)
    {
      if($value['umsatzsteuer'] != "ermaessigt" && $value['umsatzsteuer'] != "befreit") $value['umsatzsteuer'] = "normal";
      $tmpsteuersatz = null;
      $tmpsteuertext = null;
      $this->app->erp->GetSteuerPosition('rechnung', $value['id'],$tmpsteuersatz, $tmpsteuertext);
      if(is_null($value['steuersatz']) || $value['steuersatz'] < 0)
      {
        if($value['umsatzsteuer'] == "ermaessigt")
        {
          $value['steuersatz'] = $steuersatzR;
        }elseif($value['umsatzsteuer'] == "befreit")
        {
          $value['steuersatz'] = 0;
        }else{
          $value['steuersatz'] = $steuersatzV;
        }
        if(!is_null($tmpsteuersatz))$value['steuersatz'] = $tmpsteuersatz;
      }
      if($tmpsteuertext && !$value['steuertext'])$value['steuertext'] = $tmpsteuertext;
      if(!$mitumsatzsteuer)$value['steuersatz'] = 0;

      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

      $ohne_artikeltext = $this->app->DB->Select("SELECT ohne_artikeltext FROM ".$this->table." WHERE id='".$this->id."' LIMIT 1");
      if($ohne_artikeltext=="1") $value['beschreibung']="";

      if($value['explodiert_parent_artikel'] > 0)
      {
        if($value['preis'] == 0)
        {
          $value['preis'] = "-"; $value['umsatzsteuer']="hidden";
        }
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

      $value = $this->CheckPosition($value,"rechnung",$this->doctypeid,$value['id']);

      $value['menge'] = (float)$value['menge'];

      if($check_ausblenden!=1 && $posanzeigen)// && $als=="") //TODO MAHNWESEN
      {
        $this->addItem(array(
              'belegposition'=>$value['id'],
              'currency'=>$value['waehrung'],'lvl'=>isset($value['lvl'])?$value['lvl']:0,
              'amount'=>$value['menge'],
              'price'=>$value['preis'],
              'tax'=>$value['umsatzsteuer'],
              'steuersatz'=>$value['steuersatz'],
              'itemno'=>$value['nummer'],
              'artikel'=>$value['artikel'],
              'unit'=>$value['einheit'],
              'desc'=>$value['beschreibung'],
              'hersteller'=>$value['hersteller'],
              'zolltarifnummer'=>$value['zolltarifnummer'],
              'herkunftsland'=>$value['herkunftsland'],
              'herstellernummer'=>trim($value['herstellernummer']),
              'artikelnummerkunde'=>$value['artikelnummerkunde'],
              'lieferdatum'=>$value['lieferdatum'],
              'lieferdatumkw'=>$value['lieferdatumkw'],
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
              'steuertext'=>$value['steuertext'],
              "name"=>ltrim($value['bezeichnung']),
              "keinrabatterlaubt"=>$value['keinrabatterlaubt'],
              "rabatt"=>$value['rabatt'],
              "keineeinzelartikelanzeigen"=>$value['keineeinzelartikelanzeigen'],
              "anzahlunterartikel"=>$value['anzahlunterartikel']));
      }
      if($positionenkaufmaenischrunden == 3) {
        $netto_gesamt = $value['menge'] * round($value['preis'] - ($value['preis'] / 100 * $value['rabatt']),2);
      }else{
        $netto_gesamt = $value['menge'] * ($value['preis'] - ($value['preis'] / 100 * $value['rabatt']));
      }
      $netto_gesamt_ungerundet = $netto_gesamt;
      if($positionenkaufmaenischrunden)
      {
        $netto_gesamt = round($netto_gesamt, 2);
      }
      
      $summe = $summe + $netto_gesamt;
      
      if(!isset($summen[$value['steuersatz']]))$summen[$value['steuersatz']] = 0;
      $tmpbrutto = ($netto_gesamt_ungerundet/100)*$value['steuersatz'];
      if($positionenkaufmaenischrunden)
      {
        $tmpbrutto = round($tmpbrutto, 2);
      }
      $summen[$value['steuersatz']] += $tmpbrutto;
      $gesamtsteuern += $tmpbrutto;
      /*
      if($value['umsatzsteuer']=="" || $value['umsatzsteuer']=="normal")
      {
        $summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal(false,$id,"rechnung"));
        $totalV = $totalV + $netto_gesamt;
      }
      else {
        $summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt(false,$id,"rechnung"));
        $totalR = $totalR + $netto_gesamt;
      }
      */

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
    
    if($this->app->erp->RechnungMitUmsatzeuer($id))
    {
      //$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR,"totalR"=>$totalR,"totalV"=>$totalV));
      $this->setTotals(array("totalArticles"=>$summe,"total"=>($gesamtsumme != 0?$gesamtsumme:( $summe + $gesamtsteuern)),"summen"=>$summen,"totalTaxV"=>0,"totalTaxR"=>0));
    } else
    {
      $this->setTotals(array("totalArticles"=>$summe,"total"=>($gesamtsumme != 0?$gesamtsumme:$summe)));
    }

    /* Dateiname */
    $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    $tmp_name = str_replace('.','',$tmp_name);

    if($als=="" || $als=="doppel")
      $this->filename = $datum2."_RE".$belegnr.".pdf";
    else
      $this->filename = $datum2."_MA".$belegnr.".pdf";

    $this->setBarcode($belegnr);
  }


}
