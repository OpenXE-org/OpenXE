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
class MahnwesenPDF extends Briefpapier {
  public $doctype;
  public $doctypeid;

  /**
   * MahnwesenPDF constructor.
   *
   * @param Application $app
   * @param string|int  $projekt
   */
  public function __construct($app,$projekt='')
  {
    $this->app=$app;
    //parent::Briefpapier();
    $this->doctype='rechnung';
    $this->doctypeOrig='Rechnung';
    parent::__construct($this->app,$projekt);
  }

  /**
   * @param int        $id
   * @param string     $als
   * @param int        $doppeltmp
   * @param null|string $_datum
   */
  public function GetRechnung($id,$als='',$doppeltmp=0, $_datum = null)
  {
    $this->parameter = $als;
    $briefpapier_bearbeiter_ausblenden = $this->app->erp->Firmendaten('briefpapier_bearbeiter_ausblenden');
    $briefpapier_vertrieb_ausblenden = $this->app->erp->Firmendaten('briefpapier_vertrieb_ausblenden');
    $this->doctypeid=$id;
    //      $this->setRecipientDB($adresse);
    $this->setRecipientLieferadresse($id,'rechnung');

    $data = $this->app->DB->SelectRow(
      "SELECT adresse, sprache, auftrag, buchhaltung, bearbeiter, vertrieb, lieferschein AS lieferscheinid, projekt, 
       DATE_FORMAT(datum,'%d.%m.%Y') AS datum, DATE_FORMAT(mahnwesen_datum,'%d.%m.%Y') AS mahnwesen_datum, 
       DATE_FORMAT(lieferdatum,'%d.%m.%Y') AS lieferdatum, 
       belegnr, doppel, freitext, systemfreitext, ustid, typ, keinsteuersatz, soll, ist, land, zahlungsweise, 
       zahlungsstatus, zahlungszieltage, zahlungszieltageskonto, zahlungszielskonto, ohne_briefpapier, 
       ihrebestellnummer, ust_befreit, waehrung, 
       DATE_FORMAT(DATE_ADD(datum, INTERVAL zahlungszieltage DAY),'%d.%m.%Y') AS zahlungdatum, 
       DATE_FORMAT(DATE_ADD(datum, INTERVAL zahlungszieltageskonto DAY),'%d.%m.%Y') AS zahlungszielskontodatum, 
       abweichendebezeichnung AS rechnungersatz 
      FROM rechnung WHERE id='$id' 
      LIMIT 1"
    );
    extract($data,EXTR_OVERWRITE);
    $adresse = $data['adresse'];
    $sprache = $data['sprache'];
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
    $doppel = $data['doppel'];
    $freitext = $data['freitext'];
    $systemfreitext = $data['systemfreitext'];
    $ustid = $data['ustid'];
    $typ = $data['typ'];
    $keinsteuersatz = $data['keinsteuersatz'];
    $soll = $data['soll'];
    $ist = $data['ist'];
    $land = $data['land'];
    $zahlungsweise = $data['zahlungsweise'];

    $zahlungsstatus = $data['zahlungsstatus'];
    $zahlungszieltage = $data['zahlungszieltage'];
    $zahlungszieltageskonto = $data['zahlungszieltageskonto'];
    $zahlungszielskonto = $data['zahlungszielskonto'];
    $ohne_briefpapier = $data['ohne_briefpapier'];

    $ihrebestellnummer = $data['ihrebestellnummer'];
    $ust_befreit = $data['ust_befreit'];
    $waehrung = $data['waehrung'];

    $zahlungdatum = $data['zahlungdatum'];
    $zahlungszielskontodatum = $data['zahlungszielskontodatum'];

    $rechnungersatz = $data['rechnungersatz'];

    if(empty($sprache)) {
      $sprache = $this->app->DB->Select("SELECT sprache FROM adresse WHERE id='$adresse' LIMIT 1");
    }
    $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");
    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");

    $this->app->erp->BeschriftungSprache($sprache);
    if($waehrung) {
      $this->waehrung = $waehrung;
    }
    if($doppeltmp==1) {
      $doppel = $doppeltmp;
    }
    $this->projekt = $projekt;
    $this->anrede = $typ;

    $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);
    $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
    $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);

    if($ohne_briefpapier=='1')
    {
      $this->logofile = '';
      $this->briefpapier='';
      $this->briefpapier2='';
    }

    if($zahlungszieltageskonto <=0)
      $zahlungszielskontodatum = $zahlungdatum;

    if(!$this->app->erp->RechnungMitUmsatzeuer($id)){
      $this->ust_befreit=true;
    }


    $zahlungsweise = strtolower($zahlungsweise);
    //if($zahlungsweise=="rechnung"&&$zahlungsstatus!="bezahlt")
    if($zahlungsweise==='rechnung' || $zahlungsweise==='einzugsermaechtigung' || $zahlungsweise==='lastschrift') {
      if($zahlungsweise==='rechnung') {
        if($zahlungszieltage==0){
          $zahlungsweisetext = $this->app->erp->Beschriftung('zahlung_rechnung_sofort_de');
          if($zahlungsweisetext=='') {
            $zahlungsweisetext ='Rechnung zahlbar sofort. ';
          }
        }
        else {
          $zahlungsweisetext = $this->app->erp->Beschriftung('zahlung_rechnung_de');
          if($zahlungsweisetext=='') {
            $zahlungsweisetext = 'Rechnung zahlbar innerhalb von {ZAHLUNGSZIELTAGE} Tagen bis zum {ZAHLUNGBISDATUM}. ';
          }
          $zahlungsweisetext = str_replace('{ZAHLUNGSZIELTAGE}',$zahlungszieltage,$zahlungsweisetext);
          $zahlungsweisetext = str_replace('{ZAHLUNGBISDATUM}',$zahlungdatum,$zahlungsweisetext);
        }

        if($zahlungszielskonto!=0) {
          $zahlungsweisetext .="\n".$this->app->erp->Beschriftung('dokument_skonto')." $zahlungszielskonto % ".$this->app->erp->Beschriftung('dokument_innerhalb')." $zahlungszieltageskonto ".$this->app->erp->Beschriftung('dokument_tagebiszum').' '.$zahlungszielskontodatum;
        }
      } else {
        //lastschrift
        $zahlungsweisetext = $this->app->erp->Beschriftung('zahlung_'.$zahlungsweise.'_de');
        if($zahlungsweisetext=='') {
          $zahlungsweisetext ='Der Betrag wird von Ihrem Konto abgebucht.';
        }
        if($zahlungszielskonto!=0){
          $zahlungsweisetext .= "\r\n" . $this->app->erp->Beschriftung('dokument_skonto') . " $zahlungszielskonto % aus Zahlungskonditionen";
        }
      }

    } 
    else {
      $zahlungsweisetext = $this->app->erp->Beschriftung('zahlung_'.$zahlungsweise.'_de');
      if($zahlungsweisetext=='' || $zahlungsweise==='vorkasse'){
        $zahlungsweisetext = $this->app->erp->Beschriftung('dokument_zahlung_per') . ' ' . ucfirst($zahlungsweise);
      }
    }

    if($zahlungszielskonto!=0)
    {
      $zahlungsweisetext = str_replace('{ZAHLUNGSZIELSKONTO}',$zahlungszielskonto,$zahlungsweisetext);
      $zahlungsweisetext = str_replace('{ZAHLUNGSZIELTAGESKONTO}',$zahlungszieltageskonto,$zahlungsweisetext);
      $zahlungsweisetext = str_replace('{ZAHLUNGSZIELSKONTODATUM}',$zahlungszielskontodatum,$zahlungsweisetext);
    }
    else {
      $zahlungsweisetext = str_replace('{ZAHLUNGSZIELSKONTO}','',$zahlungsweisetext);
      $zahlungsweisetext = str_replace('{ZAHLUNGSZIELTAGESKONTO}','',$zahlungsweisetext);
      $zahlungsweisetext = str_replace('{ZAHLUNGSZIELSKONTODATUM}','',$zahlungsweisetext);
    }

    if($belegnr=='' || $belegnr=='0') {
      $belegnr = '- '.$this->app->erp->Beschriftung('dokument_entwurf');
    }
    else {
      if($doppel==1 || $als==='doppel'){
        $belegnr .= ' (' . $this->app->erp->Beschriftung('dokument_rechnung_kopie') . ')';
      }
    }
    $posanzeigen = true;
    if($als==='zahlungserinnerung')
    {
      $this->doctypeOrig=$this->app->erp->Beschriftung('dokument_zahlungserinnerung').' '.(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration('mahnwesen_ze_pos') === '0') {
        $posanzeigen = false;
      }
    }
    else if($als==='mahnung1')
    {
      $this->doctypeOrig=$this->app->erp->Beschriftung('dokument_mahnung1').' '.(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration('mahnwesen_1_pos') === '0') {
        $posanzeigen = false;
      }
    }
    else if($als==='mahnung2')
    {
      $this->doctypeOrig=$this->app->erp->Beschriftung('dokument_mahnung2').' '.(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration('mahnwesen_2_pos') === '0') {
        $posanzeigen = false;
      }
    }
    else if($als==='mahnung3')
    {
      $this->doctypeOrig=$this->app->erp->Beschriftung('dokument_mahnung3').' '.(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration('mahnwesen_3_pos') === '0') {
        $posanzeigen = false;
      }
    }
    else if($als==='inkasso') {
      $this->doctypeOrig=$this->app->erp->Beschriftung('dokument_mahnunginkasso').' '.(is_null($_datum)?$mahnwesen_datum:$_datum);
      if($this->app->erp->GetKonfiguration('mahnwesen_inkasso_pos') === '0') {
        $posanzeigen = false;
      }
    }
    else
    {
      if($rechnungersatz){
        $this->doctypeOrig = ($this->app->erp->Beschriftung('bezeichnungrechnungersatz')
            ? $this->app->erp->Beschriftung('bezeichnungrechnungersatz')
            : $this->app->erp->Beschriftung('dokument_rechnung')) . " $belegnr";
      }
      else{
        $this->doctypeOrig = $this->app->erp->Beschriftung('dokument_rechnung') . " $belegnr";
      }
    }



    $this->zusatzfooter = " (RE$belegnr)";

    //if($rechnung=="") $rechnung = "-";
    if($kundennummer=='') {
      $kundennummer= '-';
    }

    if(true || $auftrag=='0') {
      $auftrag = $belegnr;
    }
    if($lieferschein=='0') {
      $lieferschein= '-';
    }
    if($lieferschein=='') {
      $lieferschein= '-';
    }

    $datumlieferschein = $this->app->DB->Select(
      "SELECT DATE_FORMAT(datum, '%d.%m.%Y') 
      FROM lieferschein 
      WHERE id='$lieferscheinid' 
      LIMIT 1"
    );

    if($datumlieferschein==='00.00.0000') {
      $datumlieferschein = $datum;
    }
    if($lieferdatum==='00.00.0000') {
      $lieferdatum = $datum;
    }
    if($mahnwesen_datum==='00.00.0000') {
      $mahnwesen_datum = '';
    }

    //* start
    if($this->app->erp->GetKonfiguration('mahnwesen_bearbeiter_anzeigen') === '0') {
      $briefpapier_bearbeiter_ausblenden = true;
      $briefpapier_vertrieb_ausblenden = true;
    }
    
    
    if($briefpapier_bearbeiter_ausblenden || $briefpapier_vertrieb_ausblenden)
    {
      if($lieferschein!='-')
      {
        if($auftrag!='-')
        {
          $sCD = array(
            $this->app->erp->Beschriftung('dokument_rechnung')=>$auftrag,
            $this->app->erp->Beschriftung('dokument_rechnungsdatum')=>$datum,
            $this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,
            $this->app->erp->Beschriftung('auftrag_bezeichnung_bestellnummer')=>$ihrebestellnummer,
                
          );
        }
        else
        {
          $sCD = array(
            $this->app->erp->Beschriftung('dokument_rechnungsdatum')=>$datum,
            $this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,
            $this->app->erp->Beschriftung('auftrag_bezeichnung_bestellnummer')=>$ihrebestellnummer,
          );
        }
      }
      else {
        if($auftrag!='-')
        {
          $sCD = array(
            $this->app->erp->Beschriftung('dokument_rechnung')=>$auftrag,
            $this->app->erp->Beschriftung('dokument_rechnungsdatum')=>$datum,
            $this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,
            $this->app->erp->Beschriftung('auftrag_bezeichnung_bestellnummer')=>$ihrebestellnummer
          );
        }
        else
        {
          $sCD = array(
            $this->app->erp->Beschriftung('dokument_rechnungsdatum')=>$datum,
            $this->app->erp->Beschriftung('bezeichnungkundennummer')=>$kundennummer,
            $this->app->erp->Beschriftung('auftrag_bezeichnung_bestellnummer')=>$ihrebestellnummer,
            $this->app->erp->Beschriftung('dokument_lieferdatum')=>$lieferdatum,
            $this->app->erp->Beschriftung('dokument_ansprechpartner')=>$buchhaltung
          );
        }
      }
      if(!$briefpapier_bearbeiter_ausblenden)
      {
        if($bearbeiter) {
          $sCD[$this->app->erp->Beschriftung('auftrag_bezeichnung_bearbeiter')] = $bearbeiter;
        }
      }
      elseif(!$briefpapier_vertrieb_ausblenden)
      {
        if($vertrieb) {
          $sCD[$this->app->erp->Beschriftung('auftrag_bezeichnung_vertrieb')] = $vertrieb;
        }
      }
      $this->setCorrDetails($sCD);
      
    }else{
      if($vertrieb!=$bearbeiter)
      {
        if($lieferschein!='-')
        {
          if($auftrag!="-"){
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_rechnung") => $auftrag, $this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,

              $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb
            ));
          }
          else{
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,

              $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb
            ));
          }
        }
        else {
          if($auftrag!="-"){
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_rechnung") => $auftrag, $this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,

              $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb
            ));
          }
          else{
            $this->setCorrDetails(array($this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,

              $this->app->erp->Beschriftung("auftrag_bezeichnung_bearbeiter") => $bearbeiter, $this->app->erp->Beschriftung("auftrag_bezeichnung_vertrieb") => $vertrieb
            ));
          }
        }
        //*ende hack
      } else {
        //start hack
        if($lieferschein!='-')
        {
          if($auftrag!='-'){
            $this->setCorrDetails(
              array(
                $this->app->erp->Beschriftung('dokument_rechnung') => $auftrag,
                $this->app->erp->Beschriftung('dokument_rechnungsdatum') => $datum,
                $this->app->erp->Beschriftung('bezeichnungkundennummer') => $kundennummer,
                $this->app->erp->Beschriftung('auftrag_bezeichnung_bestellnummer') => $ihrebestellnummer,
                $this->app->erp->Beschriftung('auftrag_bezeichnung_bearbeiter') => $bearbeiter
              )
            );
          }
          else{
            $this->setCorrDetails(
              array(
                $this->app->erp->Beschriftung('dokument_rechnungsdatum') => $datum,
                $this->app->erp->Beschriftung('bezeichnungkundennummer') => $kundennummer,
                $this->app->erp->Beschriftung('auftrag_bezeichnung_bestellnummer') => $ihrebestellnummer,
                $this->app->erp->Beschriftung('auftrag_bezeichnung_bearbeiter') => $bearbeiter
              )
            );
          }
        }
        else {
          if($auftrag!='-'){
            $this->setCorrDetails(array($this->app->erp->Beschriftung('dokument_rechnung') => $auftrag, $this->app->erp->Beschriftung("dokument_rechnungsdatum") => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,

              $this->app->erp->Beschriftung('auftrag_bezeichnung_bearbeiter') => $bearbeiter
            ));
          }
          else{
            $this->setCorrDetails(array($this->app->erp->Beschriftung('dokument_rechnungsdatum') => $datum, $this->app->erp->Beschriftung("bezeichnungkundennummer") => $kundennummer, $this->app->erp->Beschriftung("auftrag_bezeichnung_bestellnummer") => $ihrebestellnummer,

              $this->app->erp->Beschriftung('dokument_ansprechpartner') => $buchhaltung
            ));
          }
        }
      }
    }
    //ende hack
    $isExport = $this->app->erp->Export($land);
    //if(!$this->app->erp->RechnungMitUmsatzeuer($id) && $ustid!="" )
    if(!$this->app->erp->RechnungMitUmsatzeuer($id) && $keinsteuersatz!='1'){
      $this->ust_befreit=true;
      if($keinsteuersatz!='1'){
        if($isExport){
          $steuer = $this->app->erp->Beschriftung('export_lieferung_vermerk');
        }
        else{
          $steuer = $this->app->erp->Beschriftung('eu_lieferung_vermerk');
        }
        $steuer = str_replace('{USTID}',$ustid,$steuer);
        $steuer = str_replace('{LAND}',$land,$steuer);
      }
    }

    if($als!=''){
      $body = $this->app->erp->MahnwesenBody($id,$als,$_datum,$sprache);
      if($posanzeigen){
        $footer = $this->app->erp->ParseUserVars('rechnung', $id, $this->app->erp->Beschriftung('rechnung_footer'));
      }
    }
    else {
      $body = $this->app->erp->Beschriftung('rechnung_header');
      $body = $this->app->erp->ParseUserVars('rechnung',$id,$body);
      if($systemfreitext!='') {
        $systemfreitext = "\r\n\r\n".$systemfreitext;
      }
      $footer = "$freitext"."\r\n".$this->app->erp->ParseUserVars('rechnung',$id,$this->app->erp->Beschriftung('rechnung_footer').
        "\r\n$steuer\r\n$zahlungsweisetext").$systemfreitext;
    }

    $this->setTextDetails(array(
          'body'=>$body,
          'footer'=>$footer));

    $artikel = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung='$id' ORDER By sort");
    if($this->app->erp->Firmendaten('modul_verband')=='1') {
      $this->rabatt=1;
    }
    elseif($this->rabatt != 1) {
      $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM rechnung_position WHERE rechnung='$id'");
      if($summe_rabatt > 0){
        $this->rabatt = 1;
      }
    }
    
    $steuersatzV = $this->app->erp->GetSteuersatzNormal(false,$id,'rechnung');
    $steuersatzR = $this->app->erp->GetSteuersatzErmaessigt(false,$id,'rechnung');
    $gesamtsteuern = 0;
    $mitumsatzsteuer = $this->app->erp->RechnungMitUmsatzeuer($id);
    $summe = 0;

    foreach($artikel as $key=>$value)  {
      if($value['umsatzsteuer'] !== 'ermaessigt' && $value['umsatzsteuer'] !== 'befreit') {
        $value['umsatzsteuer'] = 'normal';
      }
      $tmpsteuersatz = null;
      $tmpsteuertext = null;
      $this->app->erp->GetSteuerPosition('rechnung', $value['id'],$tmpsteuersatz, $tmpsteuertext);
      if($value['steuersatz'] === null || $value['steuersatz'] < 0)
      {
        if($value['umsatzsteuer'] === 'ermaessigt')
        {
          $value['steuersatz'] = $steuersatzR;         
        }
        elseif($value['umsatzsteuer'] === 'befreit')
        {
          $value['steuersatz'] = 0;
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
      if(!$mitumsatzsteuer) {
        $value['steuersatz'] = 0;
      }
      $limit = 60;	
      $summary= $value['bezeichnung'];
      if (strlen($summary) > $limit) {
        $value['desc']= $value['bezeichnung'];
        $value['bezeichnung'] = substr($summary, 0, strrpos(substr($summary, 0, $limit), ' ')) . '...';
      }

      $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");
      $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value['artikel']."' LIMIT 1");

/*
      $value['zolltarifnummer'] = $this->app->DB->Select("SELECT zolltarifnummer FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");
      $value['herkunftsland'] = $this->app->DB->Select("SELECT herkunftsland FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");

      if($ust_befreit==2 && $value['zolltarifnummer']!="")
      {
        $value[beschreibung] = $value[beschreibung]."\r\nCustoms tariff number: ".$value['zolltarifnummer']." Country of origin: ".$value['herkunftsland'];
      }
*/

      if($value['explodiert_parent_artikel'] > 0) {
        $check_ausblenden = $this->app->DB->Select(
          "SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$value['explodiert_parent_artikel']."' LIMIT 1"
        );
      }
      else {
        $check_ausblenden=0;
      }

      if($value['ausblenden_im_pdf']) {
        $check_ausblenden=1;
      }

      if(!$isExport) {
        $value['zolltarifnummer']='';
        $value['herkunftsland']='';
      }


      $value['menge'] = (float)$value['menge'];
      if($check_ausblenden!=1 && $posanzeigen)// && $als=="") //TODO MAHNWESEN
      {
        $this->addItem(array('currency'=>$value['waehrung'],
              'amount'=>$value['menge'],
              'price'=>$value['preis'],
              'tax'=>$value['umsatzsteuer'],
              'steuersatz'=>$value['steuersatz'],
              'itemno'=>$value['nummer'],
              'unit'=>$value['einheit'],
              'desc'=>$value['beschreibung'],
              'hersteller'=>$value['hersteller'],
              'zolltarifnummer'=>$value['zolltarifnummer'],
              'herkunftsland'=>$value['herkunftsland'],
              'herstellernummer'=>trim($value['herstellernummer']),
              'artikelnummerkunde'=>$value['artikelnummerkunde'],
              'lieferdatum'=>$value['lieferdatum'],
              'lieferdatumkw'=>$value['lieferdatumkw'],
              'grundrabatt'=>$value['grundrabatt'],
              'rabatt1'=>$value['rabatt1'],
              'rabatt2'=>$value['rabatt2'],
              'rabatt3'=>$value['rabatt3'],
              'rabatt4'=>$value['rabatt4'],
              'rabatt5'=>$value['rabatt5'],
              'steuertext'=>$value['steuertext'],
              'name'=>ltrim($value['bezeichnung']),
              'rabatt'=>$value['rabatt']));
      }

      $netto_gesamt = $value['menge']*($value['preis']-($value['preis']/100*$value['rabatt']));
      $summe = $summe + $netto_gesamt;
      if(!isset($summen[$value['steuersatz']])) {
        $summen[$value['steuersatz']] = 0;
      }
      $summen[$value['steuersatz']] += ($netto_gesamt/100)*$value['steuersatz'];
      $gesamtsteuern +=($netto_gesamt/100)*$value['steuersatz'];
      /*
      if($value['umsatzsteuer']=="" || $value['umsatzsteuer']=="normal")
      {
        $summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal(false,$id,"rechnung"));
      }
      else {
        $summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt(false,$id,"rechnung"));
      }
      */

    }
    /*
       $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM rechnung_position WHERE rechnung='$id'");
       $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM rechnung_position WHERE rechnung='$id' AND (umsatzsteuer!='ermaessigt')")/100 * 19;
       $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM rechnung_position WHERE rechnung='$id' AND umsatzsteuer='ermaessigt'")/100 * 7;
     */     
    if($this->app->erp->RechnungMitUmsatzeuer($id)) {
      //$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $gesamtsteuern,"summen"=>$summen,"totalTaxV"=>0,"totalTaxR"=>0));
      //$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
      $this->setTotals(array('totalArticles'=>$summe,'total'=>$summe + $gesamtsteuern,'summen'=>$summen,'totalTaxV'=>0,'totalTaxR'=>0));
    }
    else {
      $this->setTotals(array('totalArticles'=>$summe,'total'=>$summe));
    }

    /* Dateiname */
    //$tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
    //$tmp_name = str_replace('.','',$tmp_name);

    if($als==''){
      $this->filename = $datum . '_RE' . $belegnr . '.pdf';
    }
    else{
      $this->filename = $datum . '_MA' . $belegnr . '.pdf';
    }

    $this->setBarcode($belegnr);
  }

}
