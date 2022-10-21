<?php

/**
 * 
 */

use Xentral\Components\Logger\Logger;

require_once(dirname(__DIR__).'/class.versanddienstleister.php');
class Versandart_dhlversenden extends Versanddienstleister{

  const API_URL = 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/1.0/geschaeftskundenversand-api-1.0.wsdl';
  const API_URL2 = 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/1.1/geschaeftskundenversand-api-1.1.wsdl';
  const API_URL22 = 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/2.2/geschaeftskundenversand-api-2.2.wsdl';
  const SANDBOX_URL = 'https://cig.dhl.de/services/sandbox/soap';
  const PRODUCTION_URL = 'https://cig.dhl.de/services/production/soap';

  private $einstellungen;

  private $info;

  private $client;
  
  public $paketmarke_drucker;
  public $export_drucker;

  public $errors;

  public $name;

  /** @var Logger */
  private $logger;

  /**
   * Constructor for Shipment SDK
   * 
   * @param type $api_einstellungen
   * @param type $customer_info
   */
   
  function __construct($app, $id) {
    $land = '';
    $this->id = $id;
    $this->app = $app;
    $einstellungenArr = $this->app->DB->SelectRow("SELECT einstellungen_json,paketmarke_drucker,export_drucker FROM versandarten WHERE id = '$id' LIMIT 1");
    $einstellungen_json = $einstellungenArr['einstellungen_json'];
    $this->paketmarke_drucker = $einstellungenArr['paketmarke_drucker'];
    $this->export_drucker = $einstellungenArr['export_drucker'];
    $this->name="DHL Versenden";

    $this->logger = $app->Container->get('Logger');

    if($einstellungen_json)
    {
      $this->einstellungen = json_decode($einstellungen_json,true);
    }else{
      $this->einstellungen = array();
    } 
    $this->einstellungen = $this->einstellungen;
    if(isset($this->einstellungen['intraship_partnerid']) && $this->einstellungen['intraship_partnerid'] )$this->einstellungen['partnerid'] = $this->einstellungen['intraship_partnerid'];
    if(!isset($this->einstellungen['partnerid']))
    {
      $this->einstellungen['partnerid'] = '01';      
    }
    if(!isset($this->einstellungen['partnerid']) || $this->einstellungen['partnerid']=="")
    {
      $this->einstellungen['partnerid'] = '01';
    }

    if($this->einstellungen['intraship_countryISOCode']=="") $this->einstellungen['intraship_countryISOCode'] = "DE";

    $this->errors = array();
    $data = $this->einstellungen;
    $this->info = array(
            'company_name'    => $data['intraship_company_name'],
            'street_name'     => $data['intraship_street_name'],
            'street_number'   => $data['intraship_street_number'],
            'zip'             => $data['intraship_zip'],
            'country'         => $data['intraship_country'],
            'countryISOCode'  => $data['intraship_countryISOCode'],
            'city'            => $data['intraship_city'],
            'email'           => trim($data['intraship_email']),
            'phone'           => $data['intraship_phone'],
            'internet'        => $data['intraship_internet'],
            'contact_person'  => $data['intraship_contact_person'],
            'export_reason'  => $data['intraship_exportgrund']
            );



        if($land!=$this->einstellungen['intraship_countryISOCode'])
        {
          if(!empty($data['intraship_partnerid_welt']) && !empty($data['intraship_partnerid_welt']))$einstellungen['partnerid'] = $data['intraship_partnerid_welt'];
        }

        if(isset($data['intraship_retourenaccount']) && $data['intraship_retourenaccount'])$einstellungen['intraship_retourenaccount'] = $data['intraship_retourenaccount'];

  
    /*
  
    $this->einstellungen = $api_einstellungen;
    $this->info = $customer_info;

    if($this->einstellungen['partnerid']=="") $this->einstellungen['partnerid'] = '01';

    $this->errors = array();
    */
  }
  
  public function GetBezeichnung()
  {
    return 'DHL Instrahship';
  }
  
  function EinstellungenStruktur()
  {
    return array(
    
    
    'user' => array('typ'=>'text','bezeichnung'=>'Benutzer:','info'=>'geschaeftskunden_api (Versenden/Intraship-Benutzername)'),
    'signature' => array('typ'=>'text','bezeichnung'=>'Signature:','info'=>'Dhl_ep_test1 (Versenden/IntrashipPasswort)'),
    'ekp' => array('typ'=>'text','bezeichnung'=>'EKP','info'=>'5000000000 (gültige DHL Kundennummer)'),
    'partnerid' => array('typ'=>'text','bezeichnung'=>'Partner ID Inland:','info'=>'meist 01, manchmal 02, 03 etc. bzw. wenn vier- und drittletzte Stelle anders hier 4-stellig angeben z.B. 8601'),
    'partnerid_welt' => array('typ'=>'text','bezeichnung'=>'Partner ID Welt:','info'=>'Versand Welt (vierstellig meist 5301)'),
    'partnerid_connect' => array('typ'=>'text','bezeichnung'=>'Partner ID EU oder Connect:','info'=>'Versand von DE nach EU oder von AT in ausgewählte Länder Europas (Komplette EKP mit Endung 5401 in DE oder 8701 in AT)'),
    'euistwelt'=>array('typ'=>'checkbox','bezeichnung'=>'Bei EU immer Welt verwenden:'),
    'premiumversand'=>array('typ'=>'checkbox','bezeichnung'=>'Premiumversand verwenden:'),
    'intraship_retourenaccount' => array('typ'=>'text','bezeichnung'=>'Retouren Account:','info'=>'14 Stellige DHL-Retoure Abrechnungsnummer'),
    'intraship_retourenlabel'=>array('typ'=>'checkbox','bezeichnung'=>'Vorauswahl Retourenlabel:','info'=>'Druckt Retourenlabel mit'),
    
    'intraship_company_name' => array('typ'=>'text','bezeichnung'=>'Versender Firma:'),
    'intraship_street_name' => array('typ'=>'text','bezeichnung'=>'Versender Strasse:'),
    'intraship_street_number' => array('typ'=>'text','bezeichnung'=>'Versender Strasse Nr.:'),
    //'intraship_name' => array('typ'=>'text','bezeichnung'=>'Versender Ansprechpartner:'),
    'intraship_zip' => array('typ'=>'text','bezeichnung'=>'Versender PLZ:'),
    'intraship_city' => array('typ'=>'text','bezeichnung'=>'Versender Stadt:'),
    'intraship_country' => array('typ'=>'text','bezeichnung'=>'Versender Land:','info'=>'germany'),
    'intraship_countryISOCode' => array('typ'=>'text','bezeichnung'=>'Versender ISO Code:','info'=>'DE'),
    'intraship_email' => array('typ'=>'text','bezeichnung'=>'Versender E-Mail:'),
    'intraship_phone' => array('typ'=>'text','bezeichnung'=>'Versender Telefon:'),
    'intraship_internet' => array('typ'=>'text','bezeichnung'=>'Versender Web:'),
    'intraship_contact_person' => array('typ'=>'text','bezeichnung'=>'Versender Ansprechpartner:'),
    

    'intraship_account_owner' => array('typ'=>'text','bezeichnung'=>'Nachnahme Bank Inhaber:'),
    'intraship_account_number' => array('typ'=>'text','bezeichnung'=>'Nachnahme Kontonummer:'),
    'intraship_code' => array('typ'=>'text','bezeichnung'=>'Nachnahme BLZ:'),
    'intraship_bank_name' => array('typ'=>'text','bezeichnung'=>'Nachnahme Bank Name:'),
    'intraship_iban' => array('typ'=>'text','bezeichnung'=>'Nachnahme IBAN:'),

    'intraship_bic' => array('typ'=>'text','bezeichnung'=>'Nachnahme BIC:'),
    'nachnahmeextra' => array('typ'=>'checkbox','bezeichnung'=>'Nachnahme Gebühr aktivieren:'),    
    'nachnahmegebuehr' => array('typ'=>'text','bezeichnung'=>'Nachnahme Gebühr:','info'=>'z.B. 2,00 wird auf Rechnungsbetrag addiert, da DHL dies als extra Gebühr für sich behält'),    
    
    'intraship_exportgrund' => array('typ'=>'text','bezeichnung'=>'Export bzw. Sonstiges:','info'=>'z.B. Computer Zubehör'),
    
    'intraship_WeightInKG' => array('typ'=>'text','bezeichnung'=>'Standard Gewicht:','info'=>'in KG'),
    'runden' => array('typ'=>'checkbox','bezeichnung'=>'Gewicht runden auf Ganzzahl:'),    
    'intraship_LengthInCM' => array('typ'=>'text','bezeichnung'=>'Standard Länge:','info'=>'in cm'),
    'intraship_WidthInCM' => array('typ'=>'text','bezeichnung'=>'Standard Breite:','info'=>'in cm'),
    'intraship_HeightInCM' => array('typ'=>'text','bezeichnung'=>'Standard Höhe:','info'=>'in cm'),
    'intraship_PackageType' => array('typ'=>'text','bezeichnung'=>'Standard Paket:','info'=>'z.B. PL'),    

    'intraship_Product' => array('typ'=>'text','bezeichnung'=>'Standard Produkt:','info'=>'z.B. in DE: V01PAK oder AT: V86PARCEL'),

    'intraship_vorausverfuegung'=>array('typ'=>'select','bezeichnung'=>'Vorausverf&uuml;gung: ', 'optionen' => array('-' => 'keine Vorausverf&uuml;gung', 'IMMEDIATE' => 'Sofortige R&uuml;cksendung an den Absender', 'AFTER_DEADLINE' => 'R&uuml;cksenden an den Absender nach Ablauf der Frist', 'ABANDONMENT' => 'Preisgabe des Pakets durch den Absender (entgeltfrei)')),

    'sperrgut' => array('typ'=>'checkbox','bezeichnung'=>'Sperrgut:'),
    'keineversicherung' => array('typ'=>'checkbox','bezeichnung'=>'Extra Versicherung ausschalten:','info'=>'Option muss von Hand im Paketmarkendialog gesetzt werden.'),
    'leitcodierung' => array('typ'=>'checkbox','bezeichnung'=>'Leitcodierung aktivieren:'),
    'use_shipping_article_from_order_on_export' => ['typ' => 'checkbox', 'bezeichnung' => 'Bei Export Porto aus Auftrag senden:'],
    'autotracking'=>array('typ'=>'checkbox','bezeichnung'=>'Tracking übernehmen:'), 
    'log'=>array('typ'=>'checkbox','bezeichnung'=>'Logging')
    );
    
  }
  
  public function VersandartMindestgewicht()
  {
    if(!isset($this->einstellungen['intraship_WeightInKG']))return 2;
    if($this->einstellungen['intraship_WeightInKG'] === '')return 2;
    return str_replace(',','.',$this->einstellungen['intraship_WeightInKG']);
  }

  public function PaketmarkeDrucken($id, $sid)
  {
    $adressdaten = $this->GetAdressdaten($id, $sid);
    $ret = $this->Paketmarke($sid, $id, '', false, $adressdaten);
    if($sid === 'lieferschein'){
      $deliverNoteRow = $this->app->DB->SelectRow(
        sprintf('SELECT adresse, projekt, versandart FROM lieferschein WHERE id = %d LIMIT 1',
          (int)$id
        )
      ); 
      $adresse = $deliverNoteRow['adresse'];
      $projekt = $deliverNoteRow['projekt'];
      $versandart = $deliverNoteRow['versandart'];
      $adressvalidation = 2;
      if($ret) {
        $adressvalidation = 1;
      }
      $tracking = '';
      if(isset($adressdaten['tracking'])) {
        $tracking = $adressdaten['tracking'];
      }
      if(!isset($adressdaten['versandid'])) {
        $adressdaten['versandid'] = $this->app->DB->Select("SELECT id FROM versand WHERE abgeschlossen = 0 AND tracking = '' AND lieferschein = '$id' LIMIT 1");
      }

      if(method_exists($this,'deleteTrackingFromUserdata')) {
        $this->deleteTrackingFromUserdata($tracking);
      }

      if(!isset($adressdaten['versandid']))
      {
        $this->app->DB->Insert("INSERT INTO versand (versandunternehmen, tracking,
              versendet_am,abgeschlossen,lieferschein,freigegeben,firma,adresse,projekt,paketmarkegedruckt,adressvalidation)
              VALUES ('$versandart','$tracking',NOW(),1,'$id',1,'1','$adresse','$projekt',1,'$adressvalidation') ");
        $adressdaten['versandid'] = $this->app->DB->GetInsertID();
      }elseif($tracking){
        $this->app->DB->Update("UPDATE versand SET freigegeben = 1, abgeschlossen = 1, tracking =1, paketmarkegedruckt = 1, tracking= '$tracking',adressvalidation = '$adressvalidation', versendet_am = now() WHERE id = '".$adressdaten['versandid']."' LIMIT 1");
        $this->app->DB->Update("UPDATE versand SET versandunternehmen = versandart WHERE id = '".$adressdaten['versandid']."' AND versandunternehmen = '' LIMIT 1");
        $this->app->DB->Update("UPDATE versand SET versandunternehmen = '$versandart' WHERE id = '".$adressdaten['versandid']."' AND versandunternehmen = '' LIMIT 1");
      }
      $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id = '$id' LIMIT 1");
      if($auftragid) {
        $this->app->DB->Update("UPDATE auftrag SET schreibschutz = 1, status = 'abgeschlossen' WHERE id = '$auftragid' AND status = 'freigegeben' LIMIT 1");
      }
      if($adressvalidation == 1)
      {
        $this->app->erp->LieferscheinProtokoll($id, 'Paketmarke automatisch gedruckt');
        if($adressdaten['versandid']) {
          return $adressdaten['versandid'];
        }
      }elseif($adressvalidation == 2)
      {
        $this->app->erp->LieferscheinProtokoll($id, 'automatisches Paketmarke Drucken fehlgeschlagen');
      }
    }
    return $ret;
  }

  public function GetName3($adressdaten = null)
  {
    if($adressdaten === null){
      return $this->app->Secure->GetPOST('name3');
    }
    return !empty($adressdaten['name3'])?$adressdaten['name3']:'';
  }
  
  public function Paketmarke($doctyp, $docid, $target = '', $error = false, &$adressdaten = null)
  {
    $zusatz = '';
    $typ = '';
    $id = $docid;
    $sid = $doctyp;
    if($adressdaten === null) {
      $drucken = $this->app->Secure->GetPOST('drucken');
      $anders = $this->app->Secure->GetPOST('anders');
      $land = $this->app->Secure->GetPOST('land');
      $tracking_again = $this->app->Secure->GetGET('tracking_again');

      $versandmit = $this->app->Secure->GetPOST('versandmit');
      $trackingsubmit = $this->app->Secure->GetPOST('trackingsubmit');
      $versandmitbutton = $this->app->Secure->GetPOST('versandmitbutton');
      $tracking = $this->app->Secure->GetPOST('tracking');
      $trackingsubmitcancel = $this->app->Secure->GetPOST('trackingsubmitcancel');
      //$retourenlabel = $this->app->Secure->GetPOST('retourenlabel');

      $kg = $this->app->Secure->GetPOST('kg1');
      $name = $this->app->Secure->GetPOST('name');
      $name2 = $this->app->Secure->GetPOST('name2');
      $strasse = $this->app->Secure->GetPOST('strasse');
      $hausnummer = $this->app->Secure->GetPOST('hausnummer');
      $plz = $this->app->Secure->GetPOST('plz');
      $ort = $this->app->Secure->GetPOST('ort');
      $email = $this->app->Secure->GetPOST('email');
      $phone = $this->app->Secure->GetPOST('telefon');
      //$nummeraufbeleg = $this->app->Secure->GetPOST('nummeraufbeleg');
      $sid= $this->app->Secure->GetGET('sid');
    }else{
      $drucken = 1;
      $anders = '';
      $land = $adressdaten['land'];
      $tracking_again = '';

      $versandmit = '';
      $trackingsubmit = '';
      $versandmitbutton = '';
      $tracking = '';
      $trackingsubmitcancel = '';
      //$retourenlabel = '';

      $kg = $adressdaten['standardkg'];
      $name = $adressdaten['name'];
      $name2 = $adressdaten['name2'];
      $strasse = $adressdaten['strasse'];
      $hausnummer = $adressdaten['hausnummer'];
      $plz = $adressdaten['plz'];
      $ort = $adressdaten['ort'];
      $email = $adressdaten['email'];
      $phone = $adressdaten['phone'];
      //$nummeraufbeleg = 1;//$this->app->Secure->GetPOST('nummeraufbeleg');
    }
    $name3 = $this->GetName3($adressdaten);
    

    if($adressdaten === null) {

      if ($zusatz === 'express')
        $this->app->Tpl->Set('ZUSATZ', 'Express');

      if ($zusatz === 'export'){
        $this->app->Tpl->Set('ZUSATZ', 'Export');
      }

      if ($this->einstellungen['keineversicherung'] == '1') {
        $this->app->Tpl->Set('VERSICHERT', '');
        $this->app->Tpl->Set('EXTRAVERSICHERT', '');
      }
      if ($this->einstellungen['leitcodierung'] == '1') {
        $this->app->Tpl->Set('LEITCODIERUNG', ' checked="checked" ');
      }

      if ($this->einstellungen['intraship_retourenlabel'] == '1'){
        $this->app->Tpl->Add('RETOURENLABEL', ' checked="checked" ');
      }


      $id = $this->app->Secure->GetGET('id');

      $betrag = $this->app->Secure->GetPOST('betrag');
      $nachnahme = $this->app->Secure->GetPOST('nachnahme');
      $_altersfreigabe = $this->app->Secure->GetPOST('altersfreigabe');
      $versichert = $this->app->Secure->GetPOST('versichert');
      $leitcodierung = $this->app->Secure->GetPOST('leitcodierung');
      $wunschtermin = $this->app->Secure->GetPOST('wunschtermin');
      $wunschzeitraum = $this->app->Secure->GetPOST('wunschzeitraum');
      $wunschlieferdatum = $this->app->Secure->GetPOST('wunschlieferdatum');
      $versicherungssumme = $this->app->Secure->GetPOST('versicherungssumme');
      if($leitcodierung!='1') {
        $this->einstellungen['leitcodierung']=false;
      }
    }else{
      $betrag = isset($adressdaten['betrag'])?$adressdaten['betrag']:'';
      $nachnahme = isset($adressdaten['nachnahme'])?$adressdaten['nachnahme']:'';
      $_altersfreigabe = isset($adressdaten['altersfreigabe'])?$adressdaten['altersfreigabe']:'';
      $versichert = isset($adressdaten['altersfreigabe'])?$adressdaten['altersfreigabe']:'';

      $wunschtermin = isset($adressdaten['wunschtermin'])?$adressdaten['wunschtermin']:'';
      $wunschzeitraum = isset($adressdaten['wunschzeitraum'])?$adressdaten['wunschzeitraum']:'';
      $wunschlieferdatum = isset($adressdaten['wunschlieferdatum'])?$adressdaten['wunschlieferdatum']:'';
      $versicherungssumme = isset($adressdaten['versicherungssumme'])?$adressdaten['versicherungssumme']:'';
    }


    if($typ==='DHL' || $typ==='dhl'){
      $versand = 'dhl';
    }
    else if($typ==='Intraship'){
      $versand = 'intraship';
    }
    else {
      $versand = $typ;
    }

    if($sid === 'versand')
    {
      if($adressdaten === null) {
        $this->app->Tpl->Set("TRACKINGMANUELL",'&nbsp;<input type="button" value="Trackingnummer direkt eingeben" onclick="window.location.href=\'index.php?module=versanderzeugen&action=frankieren&id='.$id.'&tracking_again=1\'" name="anders">&nbsp;');
      }
      //$projekt = $this->app->DB->Select("SELECT projekt FROM versand WHERE id='$id' LIMIT 1");
    }else{
      //$projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
    }
    /*$intraship_weightinkg = $this->app->DB->Select("SELECT intraship_weightinkg FROM projekt WHERE id='$projekt' LIMIT 1");

    if($trackingsubmit == "" && $trackingsubmitcancel=="" && $drucken == "" && $tracking_again=="")
    {


    }*/
    if($target) {
      $this->app->YUI->DatePicker('wunschlieferdatum');
    }
    if(!$_POST && $target)
    {
      if($adressdaten)
      {
        $module = $doctyp;
      }else{
        $module = $this->app->Secure->GetGET('module');
      }
      //TODO Workarrond fuer lieferschein
      if($module==='lieferschein')
      {
        $lieferschein = $id;
      }
      else {
        $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
        if($lieferschein <=0) {
          $lieferschein=$id;
        }
      }

      //$projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
      //$lieferscheinnummer = "LS".$this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");

        //pruefe ob es auftragsnummer gibt dann nehmen diese
      $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
      if($auftragid > 0)
      {
        $versandbeschreibung = (String)$this->app->DB->Select("SELECT ap.beschreibung FROM auftrag_position ap INNER JOIN artikel art ON ap.artikel = art.id AND ap.auftrag = '$auftragid' AND art.porto = 1 LIMIT 1");
        if($versandbeschreibung !== '')
        {
          if(preg_match_all('/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})\s([0-9]{1,2})\:([0-9]{2})/i', $versandbeschreibung,$matches, PREG_OFFSET_CAPTURE))
          {
            $_wunschdatum = $this->app->String->Convert((strlen($matches[3][0][0]) == 2?'20':'').$matches[3][0][0]."-".(strlen($matches[2][0][0]) == 1?'0':'').$matches[2][0][0]."-".(strlen($matches[1][0][0]) == 1?'0':'').$matches[1][0][0],'%1-%2-%3','%3.%2.%1');

            $_wunschzeit = (String)(strlen($matches[4][0][0] == 1?'0':'').$matches[4][0][0]);
            $this->app->Tpl->Set('WUNSCHTERMIN', ' checked="checked" ');
            $this->app->Tpl->Set('WUNSCHLIEFERDATUM', $_wunschdatum);
            switch($_wunschzeit)
            {
              case '10':
                $this->app->Tpl->Set('WUNSCH10001200', ' checked="checked" ');
              break;
              case '12':
                $this->app->Tpl->Set('WUNSCH12001400', ' checked="checked" ');
              break;
              case '14':
                $this->app->Tpl->Set('WUNSCH14001600', ' checked="checked" ');
              break;
              case '16':
                $this->app->Tpl->Set('WUNSCH16001800', ' checked="checked" ');
              break;
              case '18':
                $this->app->Tpl->Set('WUNSCH18002000', ' checked="checked" ');
              break;
              case '19':
                $this->app->Tpl->Set('WUNSCH19002100', ' checked="checked" ');
              break;
            }
          }
        }
      }
    }elseif($target && $_POST)
    {
      switch(substr($wunschzeitraum,0,2))
      {
        case '10':
          $this->app->Tpl->Set('WUNSCH10001200', ' checked="checked" ');
        break;
        case '12':
          $this->app->Tpl->Set('WUNSCH12001400', ' checked="checked" ');
        break;
        case '14':
          $this->app->Tpl->Set('WUNSCH14001600', ' checked="checked" ');
        break;
        case '16':
          $this->app->Tpl->Set('WUNSCH16001800', ' checked="checked" ');
        break;
        case '18':
          $this->app->Tpl->Set('WUNSCH18002000', ' checked="checked" ');
        break;
        case '19':
          $this->app->Tpl->Set('WUNSCH19002100', ' checked="checked" ');
        break;
      }
      if($wunschtermin)$this->app->Tpl->Set('WUNSCHTERMIN', ' checked="checked" ');
      $this->app->Tpl->Set('WUNSCHLIEFERDATUM', $wunschlieferdatum);
    }

    if($trackingsubmit!='' || $trackingsubmitcancel!='')
    {

      if($sid==='versand')
      {
        // falche tracingnummer bei DHL da wir in der Funktion PaketmarkeDHLEmbedded sind
        if((strlen(trim($tracking)) < 12 || strlen(trim($tracking)) > 21) && $trackingsubmitcancel=="" && ($typ=="DHL" || $typ=="Intraship") && $this->einstellungen['intraship_countryISOCode']=="DE")
        {
          header("Location: index.php?module=versanderzeugen&action=frankieren&id=$id&land=$land&tracking_again=1");
          exit;
        }
        if(method_exists($this,'deleteTrackingFromUserdata')) {
          $this->deleteTrackingFromUserdata($tracking);
        }

        $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versand', tracking='$tracking',
            versendet_am=NOW(),versendet_am_zeitstempel=NOW(), abgeschlossen='1',logdatei=NOW() WHERE id='$id' LIMIT 1");

        $this->app->erp->VersandAbschluss($id);
        $this->app->erp->RunHook('versanderzeugen_frankieren_hook1', 1, $id);
        //versand mail an kunden
        $this->app->erp->Versandmail($id);

        $weiterespaket=$this->app->Secure->GetPOST('weiterespaket');
        $lieferscheinkopie=$this->app->Secure->GetPOST('lieferscheinkopie');
        if($weiterespaket=='1')
        {
          if($lieferscheinkopie=='1') {
            $lieferscheinkopie=0;
          } else {
            $lieferscheinkopie=1;
          }
          //$this->app->erp->LogFile("Lieferscheinkopie $lieferscheinkopie");
          $all = $this->app->DB->SelectArr("SELECT * FROM versand WHERE id='$id' LIMIT 1");
          $this->app->DB->Insert("INSERT INTO versand (id,adresse,rechnung,lieferschein,versandart,projekt,bearbeiter,versender,versandunternehmen,firma,
            keinetrackingmail,gelesen,paketmarkegedruckt,papieregedruckt,weitererlieferschein)
              VALUES ('','{$all[0]['adresse']}','{$all[0]['rechnung']}','{$all[0]['lieferschein']}','{$all[0]['versandart']}','{$all[0]['projekt']}',
                '{$all[0]['bearbeiter']}','{$all[0]['versender']}','{$all[0]['versandunternehmen']}',
                '{$all[0]['firma']}','{$all[0]['keinetrackingmail']}','{$all[0]['gelesen']}',0,$lieferscheinkopie,1)");

          $newid = $this->app->DB->GetInsertID();
          header("Location: index.php?module=versanderzeugen&action=einzel&id=$newid");
        } else {
          header("Location: index.php?module=versanderzeugen&action=offene");
        }
        
        exit;
      }
      //direkt aus dem Lieferschein
      if($id > 0)
      {
        $adresse = $this->app->DB->Select("SELECT adresse FROM lieferschein WHERE id='$id' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
        $kg = $this->app->Secure->GetPOST('kg1');
        if($kg=='') {
          $kg = $this->app->erp->VersandartMindestgewicht($id);
        }
        if(method_exists($this,'deleteTrackingFromUserdata')) {
          $this->deleteTrackingFromUserdata($tracking);
        }
        $this->app->DB->Insert("INSERT INTO versand (id,versandunternehmen, tracking,
          versendet_am,abgeschlossen,lieferschein,
          freigegeben,firma,adresse,projekt,gewicht,paketmarkegedruckt,anzahlpakete)
            VALUES ('','$versand','$tracking',NOW(),1,'$id',1,'".$this->app->User->GetFirma()."','$adresse','$projekt','$kg','1','1') ");
        $versandId = $this->app->DB->GetInsertID();
        $auftrag = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id = '$id'");
        $shop = $this->app->DB->Select("SELECT shop FROM auftrag WHERE id = '$auftrag' LIMIT 1");
        $auftragabgleich=$this->app->DB->Select("SELECT auftragabgleich FROM shopexport WHERE id='$shop' LIMIT 1");

        if($shop > 0 && $auftragabgleich=="1")
        {
          //$this->LogFile("Tracking gescannt");
          $this->app->remote->RemoteUpdateAuftrag($shop,$auftrag);
        }
        $this->app->erp->sendPaymentStatus($versandId);
        $this->app->Location->execute('index.php?module=lieferschein&action=paketmarke&id='.$id);
      }
      
    }

    if($versandmitbutton!='')
    {

      if($sid==='versand')
      {
        $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versandmit',
            versendet_am=NOW(),versendet_am_zeitstempel=NOW(),abgeschlossen='1' WHERE id='$id' LIMIT 1");

        $this->app->erp->VersandAbschluss($id);
        //versand mail an kunden
        $this->app->erp->Versandmail($id);

        header("Location: index.php?module=versanderzeugen&action=offene");
        exit;
      }
    }

    if($sid==='versand')
    {
      // wenn paketmarke bereits gedruckt nur tracking scannen
      $paketmarkegedruckt = $this->app->DB->Select("SELECT paketmarkegedruckt FROM versand WHERE id='$id' LIMIT 1");

      if($paketmarkegedruckt>=1)
        $tracking_again=1;
    }
    
    if($anders!='')
    {
      
    }
    
    /*if(!($drucken!='' || $tracking_again=='1') || $error)
    {      
      if($sid==='versand')
      {
        $tid = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
        $rechnung  = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$id' LIMIT 1");
        $sid = 'lieferschein';
      } else {
        $tid = $id;
      }
      
    }*/
    
    else  if(($drucken!='' || $tracking_again=='1') && !$error)
    {
      if($tracking_again!='1')
      {
        $kg = (float)str_replace(',','.',$kg);

        $abholdatum = $this->app->Secure->GetPOST("abholdatum");
        $retourenlabel= $this->app->Secure->GetPOST("retourenlabel");
        if($retourenlabel)
        {
          $this->app->Tpl->Set('RETOURENLABEL', ' checked="checked" ');
          $zusaetzlich['retourenlabel'] = 1;
        }
        if($abholdatum){
          $this->app->Tpl->Set('ABHOLDATUM',$abholdatum);
          $zusaetzlich['abholdatum'] = date('Y-m-d', strtotime($abholdatum));
          $this->app->User->SetParameter("paketmarke_abholdatum",$zusaetzlich['abholdatum']);
        }

        $kg = (float)(str_replace(',','.',$kg));
        $kg = round($kg,2);
        $name = mb_substr($this->app->erp->ReadyForPDF($name), 0, 30, 'UTF-8');
        $name2 = $this->app->erp->ReadyForPDF($name2);
        $name3 = $this->app->erp->ReadyForPDF($name3);
        $strasse = $this->app->erp->ReadyForPDF($strasse);
        $hausnummer = $this->app->erp->ReadyForPDF($hausnummer);
        $plz = $this->app->erp->ReadyForPDF($plz);
        $ort = $this->app->erp->ReadyForPDF(html_entity_decode($ort));
        $land = $this->app->erp->ReadyForPDF($land);

        if(is_numeric($name) && strtolower($strasse)==='packstation' && $name2!='')
        {
          $tmp_name = $name;
          $name  = $name2;
          $name2 =$tmp_name;
        }

        //SetKonfigurationValue($name,$value)
        if($adressdaten === null){
          $module = $this->app->Secure->GetGET('module');
        }else{
          $module = $doctyp;
        }
        //TODO Workarrond fuer lieferschein
        if($module==='lieferschein')
        {
          $lieferschein = $id;
          $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
          $versandId = 0;
        }
        elseif($module === 'retoure') {
          $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM retoure WHERE id='$id' LIMIT 1");
          $projekt = $this->app->DB->Select("SELECT projekt FROM retoure WHERE id='$id' LIMIT 1");
          $versandId = 0;
        }
        else {
          $versandId = $id;
          $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
          if($lieferschein <=0) {
            $lieferschein=$id;
          }
          $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
        }

        
        $lieferscheinnummer = 'LS'.$this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");

        //pruefe ob es auftragsnummer gibt dann nehmen diese
        $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
        if($auftragid > 0)
        {
          $nummeraufbeleg = 'AB'.$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
        } else {
          $nummeraufbeleg = $lieferscheinnummer;
        }

        $rechnung = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein='$lieferschein' LIMIT 1");
        $invoiceCurrency = null;
        $rechnung_data = $this->app->DB->SelectArr("SELECT * FROM rechnung WHERE id='$rechnung' LIMIT 1");
        if($rechnung_data) {
          $rechnungsnummer = $rechnung_data[0]['belegnr'];
          $invoiceCurrency = $rechnung_data[0]['waehrung'];
        }

        // check if proformarechnung exists
        $proformarechnungsnummer = $this->app->DB->Select("SELECT belegnr FROM proformarechnung WHERE lieferschein='$lieferschein' AND belegnr!='' LIMIT 1");
        if($proformarechnungsnummer!="") {
          $nummeraufbeleg =  $proformarechnungsnummer;
        }

        $teillieferungvon = $this->app->DB->Select("SELECT teillieferungvon FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
        $teillieferungvon2 = $this->app->DB->Select("SELECT id FROM lieferschein WHERE teillieferungvon='$lieferschein' LIMIT 1");

        $rechnungssumme = $rechnung_data[0]['soll']; //XXX
        $shippingFee = 0;

        if($rechnung && $teillieferungvon <=0 && $teillieferungvon2 <=0){
          $invoiceData =  $this->app->DB->SelectRow(
            "SELECT `zahlungsweise`, `soll`, `waehrung` FROM `rechnung` WHERE `id` = '{$rechnung}' LIMIT 1"
          );
          $zahlungsweise = $invoiceData['zahlungsweise'];
          $soll = $invoiceData['soll'];
          $invoiceCurrency = $invoiceData['waehrung'];
          $this->app->Tpl->Set('BETRAG',$soll);

          if($zahlungsweise==='nachnahme'){
            $this->app->Tpl->Set('NACHNAHME', 'checked');
          }

          if($soll >= 500 && $soll <= 2500 && $this->einstellungen['keineversicherung']!='1'){
            $this->app->Tpl->Set('VERSICHERT', 'checked');
          }

          if($soll > 2500 && $this->einstellungen['keineversicherung']!='1'){
            $this->app->Tpl->Set('EXTRAVERSICHERT', 'checked');
          }

    

          $artikel_positionen = $this->app->DB->SelectArr(
            "SELECT rp.*,if(lp.zolltarifnummer!='',lp.zolltarifnummer,rp.zolltarifnummer) as zolltarifnummer 
            FROM rechnung_position rp 
            LEFT JOIN lieferschein_position lp ON lp.auftrag_position_id=rp.auftrag_position_id AND lp.auftrag_position_id > 0 
            WHERE rp.rechnung='$rechnung'");
          if($versicherungssumme==0) {
            $versicherungssumme = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id = '$rechnung' LIMIT 1");
          }

        } else {
          // wenn mindestens ein zollwert definiert ist dann nur diese nehmen
          $checkzoll = $this->app->DB->Select("SELECT lp.id FROM lieferschein_position lp WHERE lp.lieferschein='$lieferschein' AND lp.zolleinzelwert>0 LIMIT 1");
          if($checkzoll > 0)
          { 
            $artikel_positionen = $this->app->DB->SelectArr("SELECT lp.menge, lp.bezeichnung,if(lp.zolleinzelwert>0,lp.zolleinzelwert,(ap.preis-(ap.preis/100*ap.rabatt))) as preis, lp.zolltarifnummer,
              if(lp.zollwaehrung!='',lp.zollwaehrung,ap.waehrung) as waehrung,lp.artikel, lp.zolltarifnummer FROM lieferschein_position lp LEFT JOIN auftrag_position ap ON ap.id=lp.auftrag_position_id 
              LEFT JOIN artikel a ON a.id=lp.artikel WHERE lp.lieferschein='$lieferschein' AND ap.explodiert!=1 AND a.lagerartikel=1");
 
            $rechnungssumme = $this->app->DB->Select("SELECT SUM(lp.menge*if(lp.zolleinzelwert>0,lp.zolleinzelwert,(ap.preis-(ap.preis/100*ap.rabatt)))) 
              FROM lieferschein_position lp LEFT JOIN auftrag_position ap ON ap.id=lp.auftrag_position_id LEFT JOIN artikel a ON a.id=ap.artikel WHERE lp.lieferschein='$lieferschein' AND ap.explodiert!=1 AND a.porto!=1 ");
            if($this->app->erp->Export($land) && $this->einstellungen['use_shipping_article_from_order_on_export'] == 1) {
              $shippingFee = $this->app->DB->Select(
                "SELECT SUM(ap.menge * IF(ap.zolleinzelwert>0, ap.zolleinzelwert, (ap.preis - (ap.preis / 100 * ap.rabatt)))) 
                FROM `lieferschein` AS `l` 
                INNER JOIN `auftrag_position` AS `ap` ON ap.auftrag=l.auftragid 
                INNER JOIN `artikel` AS `a` ON a.id=ap.artikel 
                WHERE l.id = '{$lieferschein}' AND ap.explodiert != 1 AND a.porto = 1 "
              );
            }
            else{
              $shippingFee = $this->app->DB->Select("SELECT SUM(lp.menge*if(lp.zolleinzelwert>0,lp.zolleinzelwert,(ap.preis-(ap.preis/100*ap.rabatt)))) 
              FROM lieferschein_position lp LEFT JOIN auftrag_position ap ON ap.id=lp.auftrag_position_id LEFT JOIN artikel a ON a.id=ap.artikel WHERE lp.lieferschein='$lieferschein' AND ap.explodiert!=1 AND a.porto=1 ");
            }
          } else {
            $artikel_positionen = $this->app->DB->SelectArr("SELECT lp.menge, lp.bezeichnung,(ap.preis-(ap.preis/100*ap.rabatt)) as preis, lp.zolltarifnummer,
              if(lp.zollwaehrung!='',lp.zollwaehrung,ap.waehrung) as waehrung,lp.artikel, lp.zolltarifnummer FROM lieferschein_position lp LEFT JOIN auftrag_position ap ON ap.id=lp.auftrag_position_id 
              LEFT JOIN artikel a ON a.id=lp.artikel WHERE lp.lieferschein='$lieferschein' AND a.lagerartikel=1 ");
            $rechnungssumme = $this->app->DB->Select("SELECT SUM(lp.menge*(ap.preis-(ap.preis/100*ap.rabatt))) 
              FROM lieferschein_position lp LEFT JOIN auftrag_position ap ON ap.id=lp.auftrag_position_id LEFT JOIN artikel a ON a.id=ap.artikel WHERE lp.lieferschein='$lieferschein' AND a.porto!=1 ");
            if($this->app->erp->Export($land) && $this->einstellungen['use_shipping_article_from_order_on_export'] == 1) {
              $shippingFee = $this->app->DB->Select(
                "SELECT SUM(ap.menge * (ap.preis-ap.preis / 100 * ap.rabatt)) 
                FROM `lieferschein` AS `l` 
                INNER JOIN `auftrag_position` AS `ap` ON ap.auftrag=l.auftragid 
                INNER JOIN `artikel` AS `a` ON a.id=ap.artikel 
                WHERE l.id = '{$lieferschein}' AND ap.explodiert != 1 AND a.porto = 1 "
              );
            }
            else{
              $shippingFee = $this->app->DB->Select("SELECT SUM(lp.menge*(ap.preis-(ap.preis/100*ap.rabatt))) 
              FROM lieferschein_position lp LEFT JOIN auftrag_position ap ON ap.id=lp.auftrag_position_id LEFT JOIN artikel a ON a.id=ap.artikel WHERE lp.lieferschein='$lieferschein' AND a.porto=1 ");
            }
          }
          if($versicherungssumme==0) $versicherungssumme = $rechnungssumme;
        }

        $gesamtgewichtzoll = $this->app->DB->Select("SELECT SUM(a.gewicht*lp.menge) FROM lieferschein_position lp LEFT JOIN artikel a ON a.id=lp.artikel WHERE lp.lieferschein='$lieferschein' AND a.lagerartikel=1");
        $gesamtgewichtzoll = $gesamtgewichtzoll / $this->app->erp->GewichtzuKgFaktor();
        if( ($kg>0 && $gesamtgewichtzoll > 0) && ($gesamtgewichtzoll > $kg))
        { 
           $gewichtfaktor = $kg/$gesamtgewichtzoll;
        } else $gewichtfaktor=1;

        $altersfreigabe = 0;
        $cartikel_positionen = !empty($artikel_positionen)?count($artikel_positionen):0;
        for($i=0;$i<$cartikel_positionen;$i++)
        {
          $artikelaltersfreigabe = (int)$this->app->DB->Select("SELECT altersfreigabe FROM artikel WHERE id = '".$artikel_positionen[$i]['artikel']."' LIMIT 1");
          if($artikelaltersfreigabe > $altersfreigabe)$altersfreigabe = $artikelaltersfreigabe;
          //$lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='".$artikel_positionen[$i]['artikel']."' LIMIT 1");

          //if($lagerartikel=="1")
          {
            if($artikel_positionen[$i]['waehrung'] == ''){
              if(!empty($invoiceCurrency)){
                $artikel_positionen[$i]['waehrung'] = $invoiceCurrency;
              }else{
                $artikel_positionen[$i]['waehrung'] = 'EUR';
              }
            }
            $gewicht = $this->app->DB->Select("SELECT gewicht FROM artikel WHERE id='".$artikel_positionen[$i]['artikel']."' LIMIT 1");
            //$porto = $this->app->DB->Select("SELECT porto FROM artikel WHERE id='".$artikel_positionen[$i]['artikel']."' LIMIT 1");
            $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='".$artikel_positionen[$i]['artikel']."' LIMIT 1");
            $zolltarifnummer = ($artikel_positionen[$i]['zolltarifnummer']!="" && $artikel_positionen[$i]['zolltarifnummer']!="0"?$artikel_positionen[$i]['zolltarifnummer']:$this->app->DB->Select("SELECT zolltarifnummer FROM artikel WHERE id='".$artikel_positionen[$i]['artikel']."' LIMIT 1"));
            $herkunftsland = $this->app->DB->Select("SELECT herkunftsland FROM artikel WHERE id='".$artikel_positionen[$i]['artikel']."' LIMIT 1");
            if(strlen($herkunftsland) > 2) {
              $herkunftsland = $this->app->erp->FindISOCountry($herkunftsland);
            }
            if(!$herkunftsland || strlen($herkunftsland) > 2) {
              $herkunftsland = 'DE';
            }
            if($gewicht > 0) {
              $gewicht = $artikel_positionen[$i]['menge']*$gewicht / $this->app->erp->GewichtzuKgFaktor();
            }
            else {
              $gewicht = 0.1*$artikel_positionen[$i]['menge'];
            }
            //if($gewicht > 0) $gewicht = $artikel_positionen[$i]['menge']*$gewicht;
            //else $gewicht = 0.1*$artikel_positionen[$i]['menge'];
            if($lagerartikel!='1') {
              $gewicht=0;
            }
            if( $gewicht > 0)
            {
              $artikel[] = array( 'description'=>mb_substr($artikel_positionen[$i]['bezeichnung'], 0, 40, 'UTF-8'),
                'countrycode'=>$herkunftsland,
                'commodity_code'=>$zolltarifnummer,
                'amount'=>$artikel_positionen[$i]['menge'],
                'netweightinkg'=>$gewicht*$gewichtfaktor,
                'grossweightinkg'=>$gewicht*$gewichtfaktor,
                'value'=>$artikel_positionen[$i]['preis'],
                'currency'=>$artikel_positionen[$i]['waehrung']);
            }
          }
        }

        $data = $this->einstellungen;

        if($land!=$this->einstellungen['intraship_countryISOCode'])
        {
          if(!empty($data['partnerid_welt']) && !empty($data['partnerid_welt'])) {
            $this->einstellungen['partnerid'] = $data['partnerid_welt'];
          }
        }

        if(isset($data['intraship_retourenaccount']) && $data['intraship_retourenaccount']) {
          $einstellungen['intraship_retourenaccount'] = $data['intraship_retourenaccount'];
        }

        // your company info
        /*$info = array(
            'company_name'    => $data['intraship_company_name'],
            'street_name'     => $data['intraship_street_name'],
            'street_number'   => $data['intraship_street_number'],
            'zip'             => $data['intraship_zip'],
            'country'         => $data['intraship_country'],
            'city'            => $data['intraship_city'],
            'email'           => trim($data['intraship_email']),
            'phone'           => $data['intraship_phone'],
            'internet'        => $data['intraship_internet'],
            'contact_person'  => $data['intraship_contact_person'],
            'export_reason'  => $data['intraship_exportgrund']
            );*/
        // receiver details

        if (! (float) $rechnungssumme && !empty($artikel_positionen)) {
          $fullAmount = 0;
          foreach ($artikel_positionen as $position) {
            if (is_array($position) && array_key_exists('preis', $position)) {
              $fullAmount += (float) $position['preis'] * (float) $position['menge'];
            }
          }
          $rechnungssumme = $fullAmount;
        }

        $rechnungssumme = max($rechnungssumme, 1);
        $customer_details = array(
            'name1'    => $name,
            'name2'     => $name2,
            'c/o'           => $name3,
            'name3'           => $name3,
            'street_name'   => $strasse,
            'street_number' => $hausnummer,
            //    'country'       => 'germany',
            'country_code'       => $land,
            'zip'           => $plz,
            'city'          => $ort,
            'email'          => trim($email),
            'ordernumber'   => $nummeraufbeleg,
            'invoicenumber'   => $rechnungsnummer,
            'proformanumber'   => $proformarechnungsnummer,
            'weight'        => $kg,
            'amount'        => str_replace(",",".",$rechnungssumme),
            'shippingFee'   => str_replace(",",".",$shippingFee),
            'currency'        => !empty($artikel_positionen[0]['waehrung']) ? $artikel_positionen[0]['waehrung'] : 'EUR'
            );
        if(!empty($phone)){
          $customer_details['phone'] = $phone;
        }
        if(!is_null($zusaetzlich) && isset($zusaetzlich['abholdatum'])) {
          $customer_details['abholdatum'] = $zusaetzlich['abholdatum'];
        }
        if(!is_null($zusaetzlich) && isset($zusaetzlich['retourenlabel'])) {
          $customer_details['intraship_retourenlabel'] = $zusaetzlich['retourenlabel'];
        }
        if($altersfreigabe > 0 && $_altersfreigabe) {
          $customer_details['altersfreigabe'] = $altersfreigabe;
        }
        if($versichert && $versicherungssumme)
        {
          $customer_details['versichert'] = $versichert;
          $customer_details['versicherungssumme'] = $versicherungssumme;         
        }
        
        if($wunschtermin)$customer_details['wunschtermin'] = $wunschtermin;
        if($wunschzeitraum)$customer_details['wunschzeitraum'] = $wunschzeitraum;
        if($wunschlieferdatum)
        {
          if(strpos($wunschlieferdatum,'.') !== false)
          {
            $customer_details['wunschlieferdatum'] = $this->app->String->Convert($wunschlieferdatum,'%1.%2.%3','%3-%2-%1');
          }else{
            $customer_details['wunschlieferdatum'] = $wunschlieferdatum;
          }
        }
        
        //$dhl = new DHLBusinessShipment($einstellungen, $info);

        $shipment_details['WeightInKG'] = $data['intraship_WeightInKG'];
        $shipment_details['LengthInCM'] = $data['intraship_LengthInCM'];
        $shipment_details['WidthInCM'] = $data['intraship_WidthInCM'];
        $shipment_details['HeightInCM'] = $data['intraship_HeightInCM'];
        $shipment_details['PackageType'] = $data['intraship_PackageType'];

        if($data['intraship_note']=='') {
          $data['intraship_note'] = $rechnungsnummer;
        }
        if($nachnahme && $betrag > 0)
        {
          $bank_details = array(
                'account_owner' => $data['intraship_account_owner'],
                'account_number' => $data['intraship_account_number'],
                'bank_code' => $data['intraship_bank_code'],
                'bank_name' => $data['intraship_bank_name'],
                'note' => $data['intraship_note'],
                'iban' => $data['intraship_iban'],
                'bic' => $data['intraship_bic']
                );

            //if($this->einstellungen['nachnahmeextra']=="1") $betrag = $betrag + str_replace(',','.',$this->einstellungen['nachnahmegebuehr']);

            $cod_details = array(
                'amount'=>str_replace(',','.',$betrag),
                'currency'=> !empty($artikel_positionen[0]['waehrung']) ? $artikel_positionen[0]['waehrung'] : 'EUR'
                );
        }
        if($land==$this->einstellungen['intraship_countryISOCode'])
        {
          if($nachnahme && $betrag > 0)
          {
            $response = $this->createNationalShipment($customer_details,$shipment_details,$bank_details,$cod_details);
          } else {
            //$customer_details['ordernumber']="";
            $response = $this->createNationalShipment($customer_details,$shipment_details);
          }
        } else {
          $customer_details['EU'] = $this->app->erp->IstEU($land)?1:0;
          $response = $this->createWeltShipment($customer_details,$shipment_details,$bank_details,$cod_details,$artikel);
          if($response)
          {

          // Zoll Papiere
            //$dhl = new DHLBusinessShipment($einstellungen, $info);
            $response_export = $this->GetExportDocDD($response['shipment_number']);
          }else{
            $dump = $this->app->erp->VarAsString($this->errors);
            $this->app->erp->Protokoll("Fehler DHL Versenden API beim Erstellen Label fuer Versand $id LS $lieferschein",$dump);
          }
        }
  
        $data['intraship_drucker'] = $this->paketmarke_drucker;
        $data['druckerlogistikstufe2'] = $this->export_drucker;
        
        if($this->app->erp->GetStandardPaketmarkendrucker()>0){
          $data['intraship_drucker'] = $this->app->erp->GetStandardPaketmarkendrucker();
        }
        

        if($this->app->erp->GetInstrashipExport($projekt)>0){
          $data['druckerlogistikstufe2'] = $this->app->erp->GetInstrashipExport($projekt);
        }

        if($response)
        {
          //$response['label_url']
          //$response['shipment_number']
          $tmppdf = $this->app->erp->DownloadFile($response['label_url'],'Intraship_Versand_'.$id.'_','pdf');
          if($this->einstellungen['autotracking']=='1'){
            $this->SetTracking($response['shipment_number'],$sid==='versand'?$id:0, $lieferschein);
          }
          $this->app->erp->Protokoll("Erfolg Paketmarke Drucker ".$data['intraship_drucker']," Datei: $tmppdf URL: ".$response['label_url']);
          $spoolerId = $this->app->printer->Drucken($data['intraship_drucker'],$tmppdf);
          if($versandId && $spoolerId) {
            $this->app->DB->Update(
              sprintf(
                'UPDATE versand SET lastspooler_id = %d, lastprinter = %d WHERE id = %d',
                $spoolerId, $data['intraship_drucker'], $versandId
              )
            );
          }
          if($module === 'retoure')
          {
            if(@is_file($tmppdf) && @filesize($tmppdf))
            {
              $fileid = $this->app->erp->CreateDatei('Paketmarke_'.$this->app->DB->Select("SELECT belegnr FROM retoure WHERE id = '$id' LIMIT 1").'.pdf', 'Anhang', '', "", $tmppdf, $this->app->DB->real_escape_string($this->app->User->GetName()));
              $this->app->erp->AddDateiStichwort($fileid, 'anhang', 'retoure', $id);
            }
          }
          
          unlink($tmppdf);
        } else {
          $dump = $this->app->erp->VarAsString($this->errors);
          $this->app->erp->Protokoll("Fehler DHL Versenden API beim Erstellen Label fuer Versand $id LS $lieferschein",$dump);
        }

        if($response_export)
        {
          $tmppdf = $this->app->erp->DownloadFile($response_export['export_url'],"Export_Intraship_Versand_".$id."_","pdf");
          $this->app->erp->Protokoll("Erfolg Export Dokumente Drucker ".$data['druckerlogistikstufe2']," Datei: $tmppdf URL: ".$response_export['export_url']);
          $spoolerId = $this->app->printer->Drucken($data['druckerlogistikstufe2'],$tmppdf);
          if($versandId && $spoolerId) {
            $this->app->DB->Update(
              sprintf(
                'UPDATE versand SET lastexportspooler_id = %d, lastexportprinter = %d WHERE id = %d',
                $spoolerId, $data['druckerlogistikstufe2'], $versandId
              )
            );
          }
          if($module === 'retoure')
          {
            if(@is_file($tmppdf) && @filesize($tmppdf))
            {
              $fileid = $this->app->erp->CreateDatei('Export_'.$this->app->DB->Select("SELECT belegnr FROM retoure WHERE id = '$id' LIMIT 1").'.pdf', 'Anhang', '', "", $tmppdf, $this->app->DB->real_escape_string($this->app->User->GetName()));
              $this->app->erp->AddDateiStichwort($fileid, 'anhang', 'retoure', $id);
            }
          }
          
          unlink($tmppdf);
        } elseif($land!=$this->einstellungen['intraship_countryISOCode']) {
          $dump = $this->app->erp->VarAsString($this->errors);
          $this->app->erp->Protokoll("Fehler DHL Versenden Export Dokument API beim Erstellen fuer Versand $id LS $lieferschein",$dump);
        }
        if($adressdaten)
        {
          if($response)
          {
            $adressdaten['tracking'] = $response['shipment_number'];
            return true;
          }
          return false;
        }
        if($response){
          return false;
        }
        return $this->errors;
      }

      if($this->app->Secure->GetPOST('drucken') || $this->app->Secure->GetPOST('anders'))
      {

      }else{
        if(empty($this->einstellungen['retourenaccount']) || !$this->einstellungen['retourenaccount'])            
        {
          $this->app->Tpl->Add('VORRETOURENLABEL', '<!--');
          $this->app->Tpl->Add('NACHRETOURENLABEL', '-->');
        }
        if(isset($this->einstellungen['retourenlabel']) && $this->einstellungen['retourenlabel']) {
          $this->app->Tpl->Add('RETOURENLABEL',' checked="checked" ');
        }
        if($target) {
          $this->app->Tpl->Set('VERSICHERUNGSSUMME',$versicherungssumme);
        }
      }
    }else{
      if($adressdaten === null){
        $module = $this->app->Secure->GetGET('module');
      }
      else {
        $module = $doctyp;
      }
      //TODO Workarrond fuer lieferschein
      if($module==='lieferschein')
      {
        $lieferschein = $id;
        //$projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
      }elseif($module === 'retoure')
      {
        $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM retoure WHERE id='$id' LIMIT 1");
        //$projekt = $this->app->DB->Select("SELECT projekt FROM retoure WHERE id='$id' LIMIT 1");
      } else {
        $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
        //$projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
        if($lieferschein <=0) {
          $lieferschein=$id;
        }
      }

      
      //$lieferscheinnummer = "LS".$this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");

      //pruefe ob es auftragsnummer gibt dann nehmen diese
      $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
      if($auftragid > 0)
      {
        //$nummeraufbeleg = "AB".$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
        if($versicherungssumme==0) $versicherungssumme = $this->app->DB->Select("SELECT gesamtsumme FROM auftrag WHERE id='$auftragid' LIMIT 1");
      } else {
        //$nummeraufbeleg = $lieferscheinnummer;
      }

      $rechnung = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein='$lieferschein' LIMIT 1");
      if($rechnung && $versicherungssumme==0) {
        $versicherungssumme = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$rechnung' LIMIT 1");
      }
      if($target) {
        $this->app->Tpl->Set('VERSICHERUNGSSUMME',$versicherungssumme);
      }
    }

    // Schutz keine Retouren im Ausland
    $checkland = $this->app->DB->Select("SELECT land FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
    if($checkland!=$this->app->erp->Firmendaten('land')){
      $this->app->Tpl->Set('RETOURENLABEL', '');
    }
   
    //$this->info = $customer_info;
    if($target)
    {
      $this->app->YUI->HideFormular('versichert',array('checked'=>'','unchecked'=>'versicherung'));
      $this->app->YUI->HideFormular('wunschtermin',array('checked'=>'','unchecked'=>'wunschzeitraum'));

      $this->app->Tpl->Parse($target,'versandarten_dhlversenden.tpl');
    }
  }
  
  public function Export($daten)
  {
    
  }
  
  function Trackinglink($tracking, &$notsend, &$link, &$rawlink)
  {
    $notsend = 0;
    $rawlink = 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc='.$tracking;
    $link = 'DHL Versand: '.$tracking.' ('.$rawlink.')';
    return true;
  }

  private function log($message) {

    if (isset($this->einstellungen['log'])) {

      if (is_array($message) || is_object($message)) {
        $this->logger->debug('DHL parameter', (array) $message);
      } else {
        $this->logger->debug($message);
      }

    }

  }

  function buildClient($retoure='', $altersfreigabe = false) {

    $header = $this->buildAuthHeader();

    $location = self::PRODUCTION_URL;
    //$location = self::SANDBOX_URL;

    $auth_params = array(
        'login' => 'wawision_1',
        'password' => '3KAuCebZmr9bu0ERtNHKcTI8sN5aY9',
        'location'  => $location,
        'trace' => $this->einstellungen['log']?1:0,
        'connection_timeout' => 30
        );

    $this->log($auth_params);
    try {
      $this->client = new SoapClient($altersfreigabe?self::API_URL22:($retoure?self::API_URL2:self::API_URL), $auth_params);
    } catch(SoapFault $exception)
    {
      die('Verbindungsfehler: '.$exception->getMessage());
      $this->errors[] = 'Verbindungsfehler: '.$exception->getMessage();
      return;
    }
    try {
      $this->client->__setSoapHeaders($header);
    } catch(SoapFault $exception)
    {
      die('Verbindungsfehler: '.$exception->getMessage());
      $this->errors[] = 'Verbindungsfehler: '.$exception->getMessage();
      return;
    }

    $this->log($this->client);

  }

  function createNationalShipment($customer_details, $shipment_details = null, $bank_details = null, $cod_details = null) {
    $api2 = false;
    $api22 = (isset($customer_details['altersfreigabe']) && $customer_details['altersfreigabe']) || (isset($customer_details['wunschtermin']) && $customer_details['wunschtermin']) || (isset($customer_details['versichert']) && $customer_details['versichert'] && isset($customer_details['versicherungssumme']) && $customer_details['versicherungssumme']);
    if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount']) {
      $api2 = true;
    }
    if(isset($this->einstellungen['leitcodierung']) && $this->einstellungen['leitcodierung'])$api22 = true;

    $api22 = true; // immer 26.05.2019 BS

    $this->buildClient($api2, $api22);

    $shipment = array();

    // Version
    $shipment['Version']  = array('majorRelease' => '1', 'minorRelease' => '0');
/*
    if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])
    {
      $shipment['Version']['minorRelease'] = '1';
    }
*/
    if($api22)
    {
      $shipment['Version']  = array('majorRelease' => '2', 'minorRelease' => '0');
    }

    if($customer_details['country_code']=='' || $customer_details['country_code']=='DE') 
    { 
      $customer_details['country_code']='DE';
      $customer_details['country_zip']='germany';
    } else if ($customer_details['country_code']=='UK'){
      $customer_details['country_zip']='england';
    } else {
      $customer_details['country_zip']='other';
    }

    // Order
    $shipment['ShipmentOrder'] = array();

    // Fixme
    if($api22)
    {
      $shipment['ShipmentOrder']['sequenceNumber']  = '01';
    }else{
      $shipment['ShipmentOrder']['SequenceNumber']  = '1';
    }
    // Shipment
    $s = array();
    if($api22)
    {
      if($this->einstellungen['intraship_Product']=="") $this->einstellungen['intraship_Product']="V01PAK";
      $s['product'] = $this->einstellungen['intraship_Product'];
    }else{
      $s['ProductCode']               = 'EPN';
    }


    if($customer_details['intraship_retourenlabel']=="1")
    {
      if($api22) {
        $s['returnShipmentAccountNumber'] = $this->einstellungen['intraship_retourenaccount'];
        $s['returnShipmentReference'] = $customer_details['ordernumber'];
      } else {
        $s['ReturnShipmentBillingNumber'] = $this->einstellungen['intraship_retourenaccount'];
      }
    }


    if($api22)
    {
      if(!empty($customer_details['abholdatum']) && $customer_details['abholdatum']!="0000-00-00")
        $s['shipmentDate']              = $customer_details['abholdatum'];
      else
        $s['shipmentDate']              = date('Y-m-d');
    }else{    
      if(!empty($customer_details['abholdatum']) && $customer_details['abholdatum']!="0000-00-00")
        $s['ShipmentDate']              = $customer_details['abholdatum'];
      else
        $s['ShipmentDate']              = date('Y-m-d');
    }

    if($this->einstellungen['runden']) {
      $shipment_details['WeightInKG'] = round($shipment_details['WeightInKG']);
      $customer_details['weight'] = round($customer_details['weight']);
    }

    if($api22)
    {
      $s['accountNumber'] = $this->einstellungen['ekp'].(strlen($this->einstellungen['partnerid']) <= 2?"01".$this->einstellungen['partnerid']:$this->einstellungen['partnerid']);
      if ($shipment_details == null) {
        $s['ShipmentItem']  = array();
        $s['ShipmentItem']['weightInKG'] = '5';
        $s['ShipmentItem']['lengthInCM'] = '50';
        $s['ShipmentItem']['widthInCM']  = '50';
        $s['ShipmentItem']['heightInCM'] = '50';
      } else {
        $s['ShipmentItem']  = array();
        $s['ShipmentItem']['weightInKG'] = $shipment_details['WeightInKG'];
        $s['ShipmentItem']['lengthInCM'] = $shipment_details['LengthInCM'];
        $s['ShipmentItem']['widthInCM']  = $shipment_details['WidthInCM'];
        $s['ShipmentItem']['heightInCM'] = $shipment_details['HeightInCM'];
      }
   
      // Falls ein Gewicht angegeben worden ist
      if($customer_details['weight']!="")
        $s['ShipmentItem']['weightInKG'] = $customer_details['weight'];
    
    
    }else{
      $s['EKP']                       = $this->einstellungen['ekp'];

      $s['Attendance']                = array();
      $s['Attendance']['partnerID']   = substr($this->einstellungen['partnerid'],0,2);
      
      
      if ($shipment_details == null) {
        $s['ShipmentItem']  = array();
        $s['ShipmentItem']['WeightInKG'] = '5';
        $s['ShipmentItem']['LengthInCM'] = '50';
        $s['ShipmentItem']['WidthInCM']  = '50';
        $s['ShipmentItem']['HeightInCM'] = '50';
        // FIXME: What is this
        $s['ShipmentItem']['PackageType'] = 'PL';
      } else {
        $s['ShipmentItem']  = array();
        $s['ShipmentItem']['WeightInKG'] = $shipment_details['WeightInKG'];
        $s['ShipmentItem']['LengthInCM'] = $shipment_details['LengthInCM'];
        $s['ShipmentItem']['WidthInCM']  = $shipment_details['WidthInCM'];
        $s['ShipmentItem']['HeightInCM'] = $shipment_details['HeightInCM'];
        // FIXME: What is this
        $s['ShipmentItem']['PackageType'] = $shipment_details['PackageType'];
      }
   
      // Falls ein Gewicht angegeben worden ist
      if($customer_details['weight']!="")
        $s['ShipmentItem']['WeightInKG'] = $customer_details['weight'];
    }

    if(isset($this->einstellungen['intraship_vorausverfuegung']) && $this->einstellungen['intraship_vorausverfuegung'] != '' && $this->einstellungen['intraship_vorausverfuegung'] != '-' && $api22){
      $s['Service']['Endorsement']['active'] = 1;
      $s['Service']['Endorsement']['type'] = $this->einstellungen['intraship_vorausverfuegung'];
    }

    if($bank_details != null)
    {
      $s['BankData']  = array();
      $s['BankData']['accountOwner'] = $bank_details['account_owner'];
      $s['BankData']['accountNumber'] = $bank_details['account_number'];
      $s['BankData']['bankCode'] = $bank_details['bank_code'];
      $s['BankData']['bankName'] = $bank_details['bank_name'];
      $s['BankData']['iban'] = $bank_details['iban'];
      $s['BankData']['bic'] = $bank_details['bic'];
      if($api22)
      {
        $s['BankData']['note1'] = $bank_details['note'];
      }else{
        $s['BankData']['note'] = $bank_details['note'];
      }
    } 
    

    if($cod_details != null)
    {
      if($this->einstellungen['nachnahmeextra']=="1") $cod_details['amount'] = $cod_details['amount'] + str_replace(',','.',$this->einstellungen['nachnahmegebuehr']);
      if($api22)
      {
        if($this->einstellungen['intraship_countryISOCode']=='AT')
        {
          if($customer_details['country']=='DE') $s['product']='V87PARCEL.V87COD';
          if($customer_details['country']=='AT') $s['product']='V86PARCEL.V86BLNN';
        }


        $s['Service']['CashOnDelivery']  = array();
        $s['Service']['CashOnDelivery']['codAmount'] = $cod_details['amount'];
        $s['Service']['CashOnDelivery']['active'] = 1;
      }else{
        //$s['Service']  = array();
        //$s['Service']['ServiceGroupOther']  = array();
        $s['Service']['ServiceGroupOther']['COD']  = array();
        $s['Service']['ServiceGroupOther']['COD']['CODAmount'] = $cod_details['amount'];
        $s['Service']['ServiceGroupOther']['COD']['CODCurrency'] = $cod_details['currency'];
      }
    }

    // Auftragnummer auf Label
    if($api22)
    {
      $s['customerReference']=$customer_details['ordernumber'];
      if(isset($customer_details['altersfreigabe']) && $customer_details['altersfreigabe'])
      {
        if($customer_details['altersfreigabe'] >= 16)
        {
          $s['Service']['VisualCheckOfAge'] = array();
          $s['Service']['VisualCheckOfAge']['active'] = 1;
          $s['Service']['VisualCheckOfAge']['type'] = $customer_details['altersfreigabe'] > 16?'A18':'A16';
        }
      }
      if(isset($customer_details['wunschtermin']) && $customer_details['wunschtermin'])
      {
        if(isset($customer_details['wunschzeitraum']) && $customer_details['wunschzeitraum'])
        {
          $s['Service']['PreferredTime'] = array();
          $s['Service']['PreferredTime']['active'] = 1;
          $s['Service']['PreferredTime']['type'] = $customer_details['wunschzeitraum'];
        }
        if(isset($customer_details['wunschlieferdatum']) && $customer_details['wunschlieferdatum'])
        {
          $s['Service']['PreferredDay'] = array();
          $s['Service']['PreferredDay']['active'] = 1;
          $s['Service']['PreferredDay']['details'] = $customer_details['wunschlieferdatum'];
          if($s['product'] !== 'V01PAK' && $s['product'] !== 'V06PAK')
          {
            $s['product'] = 'V01PAK';
          }
        }
      }
      
      if(isset($customer_details['versichert']) && $customer_details['versichert'] && isset($customer_details['versicherungssumme']) && $customer_details['versicherungssumme'])
      {
        $s['Service']['AdditionalInsurance'] = array();
        $s['Service']['AdditionalInsurance']['active'] = 1;
        $s['Service']['AdditionalInsurance']['insuranceAmount'] = number_format(str_replace(',','.',$customer_details['versicherungssumme']),2,'.','');
      }
      
      
      
    }else{
      $s['CustomerReference']=$customer_details['ordernumber'];
    }
    if($this->einstellungen['sperrgut'] == '1') {
      $s['Service']['BulkyGoods'] = array();
      $s['Service']['BulkyGoods']['active'] = 1;
    }

    $shipment['ShipmentOrder']['Shipment']['ShipmentDetails'] = $s;

    //$shipment['ShipmentOrder']['Shipment']['ShipmentDetails'] = $s;
    $shipper = array();
    if($api22)
    {
      $shipper['Name'] = array();
      $shipper['Name']['name1'] = $this->info['company_name'];
    }else{
      $shipper['Company'] = array();
      $shipper['Company']['Company'] = array();
      $shipper['Company']['Company']['name1'] = $this->info['company_name'];
    }
    $shipper['Address'] = array();
    $shipper['Address']['streetName']     = $this->info['street_name'];
    $shipper['Address']['streetNumber']   = $this->info['street_number'];
    if($api22)
    {
      $shipper['Address']['zip']            = $this->info['zip'];
    }else{
      $shipper['Address']['Zip']            = array();
      $shipper['Address']['Zip'][strtolower($this->info['country'])]  = $this->info['zip'];
    }
    $shipper['Address']['city']           = $this->info['city'];

    $shipper['Address']['Origin'] = array('countryISOCode' => $this->info['countryISOCode']);

    $shipper['Communication']                   = array();
    if ($this->info['email']!="") {
      $shipper['Communication']['email']          = $this->info['email'];
    }
    $shipper['Communication']['phone']          = $this->info['phone'];
    $shipper['Communication']['internet']       = $this->info['internet'];
    $shipper['Communication']['contactPerson']  = $this->info['contact_person'];


    $shipment['ShipmentOrder']['Shipment']['Shipper'] = $shipper;

    if($api22)
    {
      if(isset($this->einstellungen['leitcodierung']) && $this->einstellungen['leitcodierung'])
      {
        $shipment['ShipmentOrder']['PrintOnlyIfCodeable'] = array();
        $shipment['ShipmentOrder']['PrintOnlyIfCodeable']['active'] = "1";
      }
      
      $receiver = array();
      $receiver['name1'] = $customer_details['name1'];

      $receiver['Address'] = array();

      if(stripos($customer_details['street_name'], 'packstation') !== false) {
        if(is_numeric($customer_details['name1']))
        $receiver['Packstation']['postNumber']=$customer_details['name1'];
        else if(is_numeric($customer_details['name2']) && $receiver['Packstation']['postNumber']=="")
        $receiver['Packstation']['postNumber']=$customer_details['name2'];

        $receiver['Packstation']['packstationNumber']=$customer_details['street_number'];
        $receiver['Packstation']['zip']=$customer_details['zip'];
        $receiver['Packstation']['city']=$customer_details['city'];
        $receiver['Packstation']['Origin']=array('countryISOCode' => $customer_details['country_code']);
      } //else {

      if($customer_details['name2']!="")
        $receiver['Address']['name2'] = $customer_details['name2'];
      if($customer_details['name3']!="")
        $receiver['Address']['name3'] = $customer_details['name3'];

      if($customer_details['name2']!="")
        $receiver['Address']['addressAddition'] = $customer_details['name2'];
      if($customer_details['name3']!="")
        $receiver['Address']['dispatchingInformation'] = $customer_details['name3'];
 
      $receiver['Address']['streetName']     = $customer_details['street_name'];
      $receiver['Address']['streetNumber']   = $customer_details['street_number'];
      $receiver['Address']['zip']            = $customer_details['zip'];
      $receiver['Address']['city']           = $customer_details['city'];

      $receiver['Address']['Origin'] = array('countryISOCode' => $customer_details['country_code']);
      //}
      $receiver['Communication']                   = array();
      if($customer_details['c/o']=="") $customer_details['c/o'] = $customer_details['name2'];
      if($customer_details['c/o']=="") $customer_details['c/o'] = $customer_details['name1'];
      $receiver['Communication']['contactPerson']  = $customer_details['c/o'];

      if ($customer_details['email']!="") {
        $receiver['Communication']['email']          = $customer_details['email'];
      }
      if ($customer_details['phone']!="") {
        $receiver['Communication']['phone']          = $customer_details['phone'];
      }


      $shipment['ShipmentOrder']['Shipment']['Receiver'] = $receiver;
        
      
    }else{
      $receiver = array();

      $receiver['Company'] = array();

      /*
         $receiver['Company']['Person']  = array();
         $receiver['Company']['Person']['firstname'] = $customer_details['first_name'];
         $receiver['Company']['Person']['lastname'] = $customer_details['last_name'];
       */

      $receiver['Company']['Company']  = array();
      $receiver['Company']['Company']['name1'] = $customer_details['name1'];
      $receiver['Company']['Company']['name2'] = $customer_details['name2'];


      $receiver['Address'] = array();
      $receiver['Address']['streetName']     = $customer_details['street_name'];
      $receiver['Address']['streetNumber']   = $customer_details['street_number'];
      $receiver['Address']['Zip']            = array();
      $receiver['Address']['Zip'][strtolower($customer_details['country_zip'])]  = $customer_details['zip'];
      $receiver['Address']['city']           = $customer_details['city'];
      $receiver['Communication']                   = array();
      $receiver['Communication']['contactPerson']  = $customer_details['c/o'];
      if($customer_details['email']!="")
        $receiver['Communication']['email']  = $customer_details['email'];



      $receiver['Address']['Origin'] = array('countryISOCode' => $customer_details['country_code']);

      $shipment['ShipmentOrder']['Shipment']['Receiver'] = $receiver;
    }
    if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])
    {
      $ReturnReceiver = array();
      if($api22)
      {
        $ReturnReceiver['Name'] = array();
        $ReturnReceiver['Name']['name1'] = $this->info['company_name'];
      }else{
        $ReturnReceiver['Company'] = array();
        $ReturnReceiver['Company']['Company'] = array();
        $ReturnReceiver['Company']['Company']['name1'] = $this->info['company_name'];
      }
      $ReturnReceiver['Address'] = array();
      $ReturnReceiver['Address']['streetName']     = $this->info['street_name'];
      $ReturnReceiver['Address']['streetNumber']   = $this->info['street_number'];
      if($api22)
      {
        $ReturnReceiver['Address']['zip']            = $this->info['zip'];
      }else{
        $ReturnReceiver['Address']['Zip']            = array();
        $ReturnReceiver['Address']['Zip'][strtolower($this->info['country'])]  = $this->info['zip'];
      }
      $ReturnReceiver['Address']['city']           = $this->info['city'];

      $ReturnReceiver['Address']['Origin'] = array('countryISOCode' => $this->info['countryISOCode']);

      $ReturnReceiver['Communication']                   = array();
      if ($this->info['email']!="") {
        $shipper['Communication']['email']          = $this->info['email'];
      }
      $ReturnReceiver['Communication']['phone']          = $this->info['phone'];
      $ReturnReceiver['Communication']['internet']       = $this->info['internet'];
      $ReturnReceiver['Communication']['contactPerson']  = $this->info['contact_person'];


      $shipment['ShipmentOrder']['Shipment']['ReturnReceiver'] = $ReturnReceiver;
      // $shipment['ShipmentOrder']['Shipment']['ShipmentDetails']['Service'] = ['PackagingReturn' => ['active' => 1]];
    }
    $this->app->erp->LogFile(['shipment'=>$shipment,'customer_details'=>$customer_details]);
    try {
      if(empty($this->client)) {
        return;
      }
      if($api22)
      {
        $response = $this->client->createShipmentOrder($shipment);
      }
      else{
        $response = $this->client->CreateShipmentDD($shipment);
      }
         
    } catch(SoapFault $exception)
    {
      if($this->einstellungen['log'])
      {

        $this->dumpRequest('request.xml',$this->client->__getLastRequest());
        $this->dumpRequest('response.xml',$this->client->__getLastResponse());
      }

      if(trim($exception->getMessage()) == 'Authorization Required')
      {
        $this->errors[] = 'Fehlerhafte DHL Versenden Zugangsdaten';
        return false;
      }

      $this->errors[] = 'Fehler von DHL Versenden: '.$exception->getMessage();

      return;
    }
    if($this->einstellungen['log'])
    {
      $this->dumpRequest('request.xml',$this->client->__getLastRequest());
      $this->dumpRequest('response.xml',$this->client->__getLastResponse());
    }

    if (is_soap_fault($response) || (isset($response->status) && $response->status->StatusCode != 0 || isset($response->Status) && $response->Status->statusCode != 0)) {
      $this->errors[] = "<b>Fehlermeldung von DHL:</b>";

      if (is_soap_fault($response)) {

        $this->errors[] = $response->faultstring;

      } else {

        $responsetext = isset($response->status)?$response->status->StatusMessage:$response->Status->statusMessage;
        if($responsetext=='In der Sendung trat mindestens ein harter Fehler auf.'){
          $responsetext = 'Fehler bei der Leitcodierung in Adresse. Bitte korrekte Adresse angeben. ';
        }
        if($responsetext=='Die Gewichtsangabe ist kleiner als im CN23-Formular'){
          $responsetext = 'Es müssen für alle Artikel das Gewicht in den Stammdaten gepflegt sein. Es fehlt bei Artikeln das Gewicht. ';
        }



       if($responsetext=='Der Nutzer des Webservice konnte nicht authentifiziert werden.'){
         $responsetext = "DHL Login Failed. Das könnte an einem abgelaufenen Passwort liegen, da DHL in regelmäßigen Abständen ein neues Passwort für den DHL API-User vorschreibt. Mehr Informationen, wie Sie ein neues DHL Passwort vergeben können, finden Sie <a href=\"https://www.wawision.de/helpdesk/intraship#nav-fehlermeldung-beim-versuch-die-dhl-paketmarke-in-wawision-zu-erzeugen-\" target=\"_blank\">hier</a>.";
       }

        if($responsetext===$response->CreationState->LabelData->Status->statusMessage[1] && $responsetext!=''){
        } else {
          $responsetext .= ' '.$response->CreationState->LabelData->Status->statusMessage[1];
        }

        if($this->einstellungen['log'])
        {
          $this->dumpRequest('request.xml',$this->client->__getLastRequest());
          $this->dumpRequest('response.xml',$this->client->__getLastResponse());
        }
 
        $this->errors[] = $responsetext;
      }
      if($response->CreationState->StatusMessage)
      {
        foreach($response->CreationState->StatusMessage as $v)
        {
          $found = false;
          if($this->errors && is_array($this->errors))
          {
            foreach($this->errors as $err)
            {
              if($err == $v)$found = true;
            }
          }
          if(!$found)$this->errors[] = $v;
        }
      }
      return false;
    }
    $r = array();
    if($api22)
    {
      $r['shipment_number']   = (String) $response->CreationState->LabelData->shipmentNumber;
      $r['piece_number']      = (String) $response->CreationState->LabelData->licensePlate;
      $r['label_url']         = (String) $response->CreationState->LabelData->labelUrl;
      $r['gesamt'] = $response;
    }else{      
      $r['shipment_number']   = (String) $response->CreationState->ShipmentNumber->shipmentNumber;
      $r['piece_number']      = (String) $response->CreationState->PieceInformation->PieceNumber->licensePlate;
      $r['label_url']         = (String) $response->CreationState->Labelurl;
    }
    return $r;

  }


  function createWeltShipment($customer_details, $shipment_details = null, $bank_details = null, $cod_details = null,$artikel=null) {
    $api2 = false;
    if(isset($customer_details['altersfreigabe']) && $customer_details['altersfreigabe']) $api22 = true;

    if(isset($this->einstellungen['leitcodierung']) && $this->einstellungen['leitcodierung'])$api22 = true;
    //if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])$api2 = true;

    // TODO HACK
    if($this->einstellungen['intraship_countryISOCode']=='AT') {
      $api22 = true;
    }
    $api22 = true; // immer 26.05.2019 BS

    $this->buildClient($api2, $api22);


    $shipment = array();

    if($customer_details['country_code']=='DE')
    {
      $customer_details['country']='germany';
    } else if ($customer_details['country_code']=='UK')
    {                      
      $customer_details['country']='england';
    } else {
      $customer_details['country']='other';
    } 

    // Version
    if($api22)
    {
      $shipment['Version']  = array('majorRelease' => '2', 'minorRelease' => '0');
    }else{
      $shipment['Version']  = array('majorRelease' => '1', 'minorRelease' => '0');
/*      if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])
      {
        $shipment['Version']['minorRelease'] = '1';
      }
*/
    }

    // Order
    $shipment['ShipmentOrder'] = array();
    $s = array();
    // Fixme
    if($api22)
    {
      $shipment['ShipmentOrder']['sequenceNumber']  = '01';
      if(!empty($customer_details['abholdatum']) && $customer_details['abholdatum']!="0000-00-00")
        $s['shipmentDate']              = $customer_details['abholdatum'];
      else
        $s['shipmentDate']              = date('Y-m-d');

      if($this->einstellungen['intraship_countryISOCode']=='AT') 
      {
        //$s['product'] = isset($customer_details['EU'])&&$customer_details['EU']?"V87PARCEL": "V82PARCEL";
        $s['product'] = ($customer_details['country_code']=="DE"||$customer_details['country_code']=="BE"||$customer_details['country_code']=="NL"||$customer_details['country_code']=="LU"||$customer_details['country_code']=="PL"||$customer_details['country_code']=="SK"||$customer_details['country_code']=="CZ")?"V87PARCEL": "V82PARCEL";
        $s['accountNumber']= ($customer_details['country_code']=="DE"||$customer_details['country_code']=="BE"||$customer_details['country_code']=="NL"||$customer_details['country_code']=="LU"||$customer_details['country_code']=="PL"||$customer_details['country_code']=="SK"||$customer_details['country_code']=="CZ")?$this->einstellungen['ekp'].$this->einstellungen['partnerid_connect']:$this->einstellungen['ekp'].$this->einstellungen['partnerid_welt'];
      }
      else {
        if($this->einstellungen['euistwelt'])
        {
          $s['product'] = "V53WPAK";
          $s['accountNumber'] =$this->einstellungen['ekp'].(strlen($this->einstellungen['partnerid_welt'])<=2?"01".$this->einstellungen['partnerid_welt']:$this->einstellungen['partnerid_welt']);
        }
        else
        {
         if(substr($this->einstellungen['partnerid_connect'], -4)=="5501") $s['product'] = "V55PAK";
         else $s['product'] = isset($customer_details['EU'])&&$customer_details['EU']?"V54EPAK": "V53WPAK";
         $s['accountNumber'] =$this->einstellungen['ekp'].(isset($customer_details['EU'])&&$customer_details['EU']?$this->einstellungen['partnerid_connect']:$this->einstellungen['partnerid_welt']);
        }
      } 


    }else{
      $shipment['ShipmentOrder']['SequenceNumber']  = '1';
      $s['ProductCode']               = 'BPI';
      if(!empty($customer_details['abholdatum']) && $customer_details['abholdatum']!="0000-00-00")
        $s['ShipmentDate']              = $customer_details['abholdatum'];
      else
        $s['ShipmentDate']              = date('Y-m-d');
      $s['EKP']                       = $this->einstellungen['ekp'];

      $s['Attendance']                = array();
      $s['Attendance']['partnerID']   = $this->einstellungen['partnerid'];
    }
    


    if($customer_details['intraship_retourenlabel']=="1")
    {
      if($api22) {
 //       $s['Service']['ReturnReceipt']  = array();
//        $s['Service']['ReturnReceipt']['active'] = 1;
        $s['returnShipmentAccountNumber'] = $this->einstellungen['intraship_retourenaccount'];
        $s['returnShipmentReference'] = $customer_details['ordernumber'];
      } else {
        $s['ReturnShipmentBillingNumber'] = $this->einstellungen['intraship_retourenaccount'];
      }
    }

    if($this->einstellungen['runden'])$customer_details['weight'] = round($customer_details['weight']);
    
    if($api22)
    {
      if ($shipment_details == null) {
        $s['ShipmentItem']  = array();
        $s['ShipmentItem']['weightInKG'] = '3';
        $s['ShipmentItem']['lengthInCM'] = '50';
        $s['ShipmentItem']['widthInCM']  = '30';
        $s['ShipmentItem']['heightInCM'] = '15';
      }      
      else {
        $s['ShipmentItem']  = array();
        $s['ShipmentItem']['weightInKG'] = $shipment_details['WeightInKG'];
        $s['ShipmentItem']['lengthInCM'] = $shipment_details['LengthInCM'];
        $s['ShipmentItem']['widthInCM']  = $shipment_details['WidthInCM'];
        $s['ShipmentItem']['heightInCM'] = $shipment_details['HeightInCM'];
        $s['ShipmentItem']['PackageType'] = $shipment_details['PackageType'];
      }
   // Falls ein Gewicht angegeben worden ist
      if($customer_details['weight']!="")
        $s['ShipmentItem']['weightInKG'] = $customer_details['weight'];

    }else{
      if ($shipment_details == null) {
        $s['ShipmentItem']  = array();
        $s['ShipmentItem']['WeightInKG'] = '3';
        $s['ShipmentItem']['LengthInCM'] = '50';
        $s['ShipmentItem']['WidthInCM']  = '30';
        $s['ShipmentItem']['HeightInCM'] = '15';
        // FIXME: What is this
        $s['ShipmentItem']['PackageType'] = 'PK';
      }
      else {
        $s['ShipmentItem']  = array();
        $s['ShipmentItem']['weightInKG'] = $shipment_details['WeightInKG'];
        $s['ShipmentItem']['lengthInCM'] = $shipment_details['LengthInCM'];
        $s['ShipmentItem']['widthInCM']  = $shipment_details['WidthInCM'];
        $s['ShipmentItem']['heightInCM'] = $shipment_details['HeightInCM'];
        $s['ShipmentItem']['PackageType'] = $shipment_details['PackageType'];
      }

      // Falls ein Gewicht angegeben worden ist
      if($customer_details['weight']!="")
        $s['ShipmentItem']['WeightInKG'] = $customer_details['weight'];
    }
    //$s['Service']['ServiceGroupBusinessPackInternational']['Economy'] = 'true';
    $s['Service']['ServiceGroupBusinessPackInternational']['Premium'] = 'true';

    if($bank_details != null)
    {
      $s['BankData']  = array();
      $s['BankData']['accountOwner'] = $bank_details['account_owner'];
      $s['BankData']['accountNumber'] = $bank_details['account_number'];
      $s['BankData']['bankCode'] = $bank_details['bank_code'];
      $s['BankData']['bankName'] = $bank_details['bank_name'];
      $s['BankData']['iban'] = $bank_details['iban'];
      $s['BankData']['bic'] = $bank_details['bic'];
      $s['BankData']['note'] = $bank_details['note'];
    }



    if($cod_details != null)
    {
      //$s['Service']  = array();
      //$s['Service']['ServiceGroupOther']  = array();

      if($this->einstellungen['nachnahmeextra']=='1') $cod_details['amount'] = $cod_details['amount'] + str_replace(',','.',$this->einstellungen['nachnahmegebuehr']);
      if($api22)
      {
        if($this->einstellungen['intraship_countryISOCode']=='AT')
        {
          if($customer_details['country']=='DE') $s['product']='V87PARCEL.V87COD';
          if($customer_details['country']=='AT') $s['product']='V86PARCEL.V86BLNN';
        }
        $s['Service']['CashOnDelivery']  = array();
        $s['Service']['CashOnDelivery']['codAmount'] = $cod_details['amount'];
        $s['Service']['CashOnDelivery']['active'] = 1;
      } else {
        $s['Service']['ServiceGroupOther']['COD']  = array();
        $s['Service']['ServiceGroupOther']['COD']['CODAmount'] = $cod_details['amount'];
        $s['Service']['ServiceGroupOther']['COD']['CODCurrency'] = $cod_details['currency'];
      }
    }
    // Auftragnummer auf Label
    if($api22)
    {
      $s['customerReference']=$customer_details['ordernumber'];
      if($customer_details['altersfreigabe'] >= 16)
      {
        $s['Service']['VisualCheckOfAge'] = array();
        $s['Service']['VisualCheckOfAge']['active'] = 1;
        $s['Service']['VisualCheckOfAge']['type'] = $customer_details['altersfreigabe'] > 16?'A18':'A16';
      }     
    }else{
      $s['CustomerReference']=$customer_details['ordernumber'];
    }

    if($this->einstellungen['premiumversand']=='1')
    {
      $s['Service']['Premium'] = array();
      $s['Service']['Premium']['active'] = 1;
    }

    if($this->einstellungen['sperrgut'] == '1') {
      $s['Service']['BulkyGoods'] = array();
      $s['Service']['BulkyGoods']['active'] = 1;
    }
    //$s['Service']['ServiceGroupBusinessPackInternational']['Premium'] = 'true';

    $shipment['ShipmentOrder']['Shipment']['ShipmentDetails'] = $s;

    //$shipment['ShipmentOrder']['Shipment']['ShipmentDetails'] = $s;


    $shipper = array();


    if($api22)
    {
      $shipper['Name'] = array();
      $shipper['Name']['name1'] = $this->info['company_name'];
    }else{
      $shipper['Company'] = array();
      $shipper['Company']['Company'] = array();
      $shipper['Company']['Company']['name1'] = $this->info['company_name'];
    }

    $shipper['Address'] = array();
    $shipper['Address']['streetName']     = $this->info['street_name'];
    $shipper['Address']['streetNumber']   = $this->info['street_number'];
    if($api22)
    {
      $shipper['Address']['zip']            = $this->info['zip'];
    }else{
      $shipper['Address']['Zip']            = array();

      $shipper['Address']['Zip'][strtolower($this->info['country'])]  = $this->info['zip'];
    }
    $shipper['Address']['city']           = $this->info['city'];

    $shipper['Address']['Origin'] = array('countryISOCode' => $this->info['countryISOCode']);


    $shipper['Communication']                   = array();


    if ($this->info['email']!='') {
      $shipper['Communication']['email']          = $this->info['email'];
    }

    if(empty($this->info['phone'])) {
      $this->info['phone'] = '0';
    }
    $this->info['phone'] = str_replace('+','00',trim($this->info['phone']));
    $this->info['phone'] = preg_replace('![^0-9]!', '', $this->info['phone']); 
    if(empty($this->info['phone'])) {
      $this->info['phone'] = '0';
    }
    
    $shipper['Communication']['phone']          = $this->info['phone'];
    $shipper['Communication']['internet']       = $this->info['internet'];

    $shipper['Communication']['contactPerson']  = $this->info['contact_person'];


    $shipment['ShipmentOrder']['Shipment']['Shipper'] = $shipper;


    if($api22)
    {

      if(isset($this->einstellungen['leitcodierung']) && $this->einstellungen['leitcodierung'])
      {
        $shipment['ShipmentOrder']['PrintOnlyIfCodeable'] = array();
        $shipment['ShipmentOrder']['PrintOnlyIfCodeable']['active'] = "1";
      }
      
      $receiver = array();
      $receiver['name1'] = $customer_details['name1'];

      if(stripos($customer_details['street_name'], 'packstation') !== false) { 
        if(is_numeric($customer_details['name1']))
        $receiver['Packstation']['postNumber']=$customer_details['name1'];
        else if(is_numeric($customer_details['name2']) && $receiver['Packstation']['postNumber']=="")
        $receiver['Packstation']['postNumber']=$customer_details['name2'];
       
        $receiver['Packstation']['packstationNumber']=$customer_details['street_number'];
        $receiver['Packstation']['zip']=$customer_details['zip'];
        $receiver['Packstation']['city']=$customer_details['city'];
        $receiver['Packstation']['Origin']=array('countryISOCode' => $customer_details['country_code']);
      } else {

      $receiver['Address'] = array();
        if($customer_details['name2']!="")
          $receiver['Address']['addressAddition'] = $customer_details['name2'];
        $receiver['Address']['streetName']     = $customer_details['street_name'];
        $receiver['Address']['streetNumber']   = $customer_details['street_number'];
        $receiver['Address']['zip']            = $customer_details['zip'];
        $receiver['Address']['city']           = $customer_details['city'];

        if($customer_details['country_code']==='AT' && strpos($customer_details['street_number'], '/') !== false )
        {
          $customer_details['street_number'] = str_replace(' ','',$customer_details['street_number']);
          $tmpstiege = explode('/',$customer_details['street_number']);
          //if(isset($tmpstiege[0]))
          //  $customer_details['street_number'] = $tmpstiege[0];

          if(isset($tmpstiege[1]))
            $receiver['Address']['addressAddition']=$tmpstiege[1];

          if(isset($tmpstiege[2]))
            $receiver['Address']['addressAddition2']=$tmpstiege[2];

          //$customer_details['street_number'] = strstr ($customer_details['street_number'], '/',true);
          //$receiver['Address']['streetNumber'] = strstr ($receiver['Address']['streetNumber'], '/',true);
        }
     
        $receiver['Address']['Origin'] = array('countryISOCode' => $customer_details['country_code']);

      }
      $receiver['Communication']                   = array();
      if($customer_details['c/o']=='') $customer_details['c/o'] = $customer_details['name2'];
      if($customer_details['c/o']=='') $customer_details['c/o'] = $customer_details['name1'];
      $receiver['Communication']['contactPerson']  = $customer_details['c/o'];

      if ($customer_details['email']!='') {
        $receiver['Communication']['email']          = $customer_details['email'];
      }

      if ($customer_details['phone']!='') {
        $receiver['Communication']['phone']          = $customer_details['phone'];
      }

      $shipment['ShipmentOrder']['Shipment']['Receiver'] = $receiver;
        
    }else{
      $receiver = array();

      $receiver['Company'] = array();

      $receiver['Company']['Company']  = array();
      $receiver['Company']['Company']['name1'] = $customer_details['name1'];
      $receiver['Company']['Company']['name2'] = $customer_details['name2'];


      if($customer_details['name2']!="")
        $tmp_name = explode(' ',$customer_details['name2'],2);
      else
        $tmp_name = explode(' ',$customer_details['name1'],2);

      if(isset($tmp_name[2]) && $tmp_name[2]){
        $receiver['Company']['Person']  = array();
        $receiver['Company']['Person']['firstname'] = $tmp_name[0];
        $receiver['Company']['Person']['lastname'] = $tmp_name[1];
      }
      $receiver['Address'] = array();
      $receiver['Address']['streetName']     = $customer_details['street_name'];
      $receiver['Address']['streetNumber']   = $customer_details['street_number'];
      $receiver['Address']['Zip']            = array();
      $receiver['Address']['Zip'][strtolower($customer_details['country_zip'])]  = $customer_details['zip'];
      $receiver['Address']['city']           = $customer_details['city'];
      $receiver['Communication']                   = array();
      if($customer_details['c/o']=="") $customer_details['c/o'] = $customer_details['name2'];
      if($customer_details['c/o']=="") $customer_details['c/o'] = $customer_details['name1'];
      $receiver['Communication']['contactPerson']  = $customer_details['c/o'];


      if ($customer_details['email']!="") {
        $receiver['Communication']['email']          = $customer_details['email'];
      }
      if(empty($customer_details['phone']))$customer_details['phone'] = '0';
      $customer_details['phone'] = str_replace('+','00',trim($customer_details['phone']));
      $customer_details['phone'] = preg_replace('![^0-9]!', '', $customer_details['phone']); 
      if(empty($customer_details['phone']))$customer_details['phone'] = '0';
      $receiver['Communication']['phone']          = $customer_details['phone'];
      $receiver['Address']['Origin'] = array('countryISOCode' => $customer_details['country_code']);

      $shipment['ShipmentOrder']['Shipment']['Receiver'] = $receiver;
    }


    if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])
    {
      $ReturnReceiver = array();
      $ReturnReceiver['Company'] = array();
      $ReturnReceiver['Company']['Company'] = array();
      $ReturnReceiver['Company']['Company']['name1'] = $this->info['company_name'];

      $ReturnReceiver['Address'] = array();
      $ReturnReceiver['Address']['streetName']     = $this->info['street_name'];
      $ReturnReceiver['Address']['streetNumber']   = $this->info['street_number'];
      if($api22)
      {
        $ReturnReceiver['Address']['zip']            = $this->info['zip'];
      }else{
        $ReturnReceiver['Address']['Zip']            = array();
        $ReturnReceiver['Address']['Zip'][strtolower($this->info['country'])]  = $this->info['zip'];
      }
      $ReturnReceiver['Address']['city']           = $this->info['city'];

      $ReturnReceiver['Address']['Origin'] = array('countryISOCode' => $customer_details['country_code']);

      $ReturnReceiver['Communication']                   = array();
      if ($this->info['email']!="") {
        $shipper['Communication']['email']          = $this->info['email'];
      }
      $ReturnReceiver['Communication']['phone']          = $this->info['phone'];
      $ReturnReceiver['Communication']['internet']       = $this->info['internet'];
      $ReturnReceiver['Communication']['contactPerson']  = $this->info['contact_person'];


      $shipment['ShipmentOrder']['Shipment']['ReturnReceiver'] = $ReturnReceiver;
    }

    $proformarechnungsnummer = $customer_details['proformanumber']; 

    if($proformarechnungsnummer=="")
    {
      $proformarechnungsnummer = ($customer_details['invoicenumber']!=""?$customer_details['invoicenumber']:$customer_details['ordernumber']);
    }
    if($api22)
    {
      //$export['invoiceType'] = "commercial";
      //$export['invoiceDate'] = date('Y-m-d');
     
      $export['invoiceNumber'] = $proformarechnungsnummer;
      $export['exportType'] = "OTHER";
      $export['exportTypeDescription'] = $this->info['export_reason'];
      $export['commodityCode'] = "";
      $export['termsOfTrade'] = "DDP";
      $export['amount'] = count($artikel);
      $export['placeOfCommital'] = $customer_details['city'];
      //$export['Description'] = $this->info['export_reason'];
      //$export['CountryCodeOrigin'] = "DE";
      $export['additionalFee'] = number_format((float)$customer_details['shippingFee'],2,".","");
      $export['customsValue'] = number_format($customer_details['amount'],2,".","");
      $export['customsCurrency'] = $customer_details['currency'];
      //$export['permitNumber'] = "";

    }else{
      $export['InvoiceType'] = "commercial";
      $export['InvoiceDate'] = date('Y-m-d');
      $export['InvoiceNumber'] = $proformarechnungsnummer;
      $export['ExportType'] = 0;
      $export['ExportTypeDescription'] = $this->info['export_reason'];
      $export['CommodityCode'] = "";
      $export['TermsOfTrade'] = "DDP";
      $export['Amount'] = count($artikel);
      $export['Description'] = $this->info['export_reason'];
      $export['CountryCodeOrigin'] = "DE";
      $export['AdditionalFee'] = number_format((float)$customer_details['shippingFee'],2,".","");
      $export['CustomsValue'] = number_format($customer_details['value'],2,".","");
      $export['CustomsCurrency'] = $customer_details['currency'];
      $export['PermitNumber'] = "";
    }

    $p=0;
    $cartikel = !empty($artikel)?count($artikel):0;
    for($i=0;$i<$cartikel;$i++)
    {
        if($artikel[$i]['currency']=="") $artikel[$i]['currency']="EUR";
        if($api22)
        {
          if($this->info['export_reason']!="") 
            $export['ExportDocPosition'][0]['description'] = $this->info['export_reason'];
          else
            $export['ExportDocPosition'][0]['description'] = "Additional Positions";

          $export['ExportDocPosition'][0]['countryCodeOrigin'] = $artikel[$i]['countrycode'];
          $export['ExportDocPosition'][0]['customsTariffNumber'] = $artikel[$i]['commodity_code'];
          $export['ExportDocPosition'][0]['amount'] = 1;//+= intval($artikel[$i]['amount']);
          if(!isset($export['ExportDocPosition'][0]['netWeightInKG'])){
            $export['ExportDocPosition'][0]['netWeightInKG'] = number_format($artikel[$i]['netweightinkg'], 2, ".", "");
            $export['ExportDocPosition'][0]['grossWeightInKG'] = number_format($artikel[$i]['grossweightinkg'], 2, ".", "");
          }else{
            $export['ExportDocPosition'][0]['netWeightInKG'] = number_format(
              (float)$export['ExportDocPosition'][0]['netWeightInKG'] + (float)$artikel[$i]['netweightinkg'], 2, ".", ""
            );
            $export['ExportDocPosition'][0]['grossWeightInKG'] = number_format(
              (float)$export['ExportDocPosition'][0]['grossWeightInKG'] + (float)$artikel[$i]['grossweightinkg'], 2, ".", ""
            );
          }
          $export['ExportDocPosition'][0]['customsValue'] = number_format($customer_details['amount'],2,".","");
          $export['ExportDocPosition'][0]['customsCurrency'] = $customer_details['currency'];
          $export['ExportDocPosition'][0]['Currency'] = $customer_details['currency'];

        }else{
          if($this->info['export_reason']!="") 
            $export['ExportDocPosition'][0]['Description'] = $this->info['export_reason'];
          else
            $export['ExportDocPosition'][0]['Description'] = "Additional Positions";

          $export['ExportDocPosition'][0]['CountryCodeOrigin'] = $artikel[$i]['countrycode'];
          $export['ExportDocPosition'][0]['CommodityCode'] = $artikel[$i]['commodity_code'];
          $export['ExportDocPosition'][0]['Amount'] = 1;//+= intval($artikel[$i]['amount']);
          if(!isset($export['ExportDocPosition'][0]['netWeightInKG'])){
            $export['ExportDocPosition'][0]['netWeightInKG'] = number_format($artikel[$i]['netweightinkg'], 2, ".", "");
            $export['ExportDocPosition'][0]['grossWeightInKG'] = number_format($artikel[$i]['grossweightinkg'], 2, ".", "");
          }else{
            $export['ExportDocPosition'][0]['netWeightInKG'] = number_format(
              (float)$export['ExportDocPosition'][0]['netWeightInKG'] + (float)$artikel[$i]['netweightinkg'], 2, ".", ""
            );
            $export['ExportDocPosition'][0]['grossWeightInKG'] = number_format(
              (float)$export['ExportDocPosition'][0]['grossWeightInKG'] + (float)$artikel[$i]['grossweightinkg'], 2, ".", ""
            );
          }
          $export['ExportDocPosition'][0]['CustomsValue'] = number_format($customer_details['amount'],2,".","");
          $export['ExportDocPosition'][0]['CustomsCurrency'] = $customer_details['currency'];
          $export['ExportDocPosition'][0]['Currency'] = $customer_details['currency'];
        }
    }
    
    

    if(count($artikel)<=0)$this->errors[]="Bei den Artikel fehlen die Gewichte in den Stammdaten!";

    $shipment['ShipmentOrder']['Shipment']['ExportDocument'] = $export;
    try {
      if(empty($this->client)) {
        return;
      }
      if($api22)
      {
        $response = $this->client->createShipmentOrder($shipment);
      }else{
        $response = $this->client->CreateShipmentDD($shipment);
      }
    } catch(SoapFault $exception)
    {

      if($this->einstellungen['log'])
      {
        $this->dumpRequest("request.xml",$this->client->__getLastRequest());
        $this->dumpRequest("response.xml",$this->client->__getLastResponse());
      }
  
      if(trim($exception->getMessage()) == 'Authorization Required')
      {
        $this->errors[] = 'Fehlerhafte DHL Versenden Zugangsdaten';
        return false;
      }
      $this->errors[] = "Fehler von DHL Versenden: ".$exception->getMessage();
      return;
    }

    if($this->einstellungen['log'])
    {
      $this->dumpRequest("request.xml",$this->client->__getLastRequest());
      $this->dumpRequest("response.xml",$this->client->__getLastResponse());
    }
 

  
    if (((isset($response->status) && $response->status->StatusCode != 0)
        || (isset($response->Status) && $response->Status->statusCode != 0)) || is_soap_fault($response)) {
      $this->errors[] = "<b>Fehlermeldung von DHL:</b>";

      if (is_soap_fault($response)) {
        $this->errors[] = $response->faultstring;
      } else {

        $responsetext = isset($response->status)?$response->status->StatusMessage:$response->Status->statusMessage;
        if($responsetext=='In der Sendung trat mindestens ein harter Fehler auf.')
          $responsetext = 'Fehler bei der Leitcodierung in Adresse. Bitte korrekte Adresse angeben. ';

        if($responsetext=='Der Nutzer des Webservice konnte nicht authentifiziert werden.')
          $responsetext = "DHL Login Failed. Das könnte an einem abgelaufenen Passwort liegen, da DHL in regelmäßigen Abständen ein neues Passwort für den DHL API-User vorschreibt. Mehr Informationen, wie Sie ein neues DHL Passwort vergeben können, finden Sie <a href=\"https://www.wawision.de/helpdesk/intraship#nav-fehlermeldung-beim-versuch-die-dhl-paketmarke-in-wawision-zu-erzeugen-\" target=\"_blank\">hier</a>.";



        if($responsetext=='Exception in extension function java.util.MissingResourceException: Couldnt find 3-letter country code for GERMANY')
          $responsetext = 'Bitte prüfen Sie die Herkunftsländer der Artikel in den Stammdaten. Es müssen 2-stellige ISO Codes angegeben sein.';

        $secondMessage = (String)$response->CreationState->LabelData->Status->statusMessage[1];
        if($secondMessage!="" && $secondMessage!=$responsetext)
        {
          $responsetext .= " ".$response->CreationState->LabelData->Status->statusMessage[1];
        }

        $this->errors[] = $responsetext;
      }
      if($response->CreationState->StatusMessage)
      {
        foreach($response->CreationState->StatusMessage as $v)
        {
          $found = false;
          if($this->errors && is_array($this->errors))
          {
            foreach($this->errors as $err)
            {
              if($err == $v)$found = true;
            }
          }
          if(!$found) {
            $this->errors[] = $v;
          }
        }
      }

      return false;

    }

    if($api22)
    {
      $r['shipment_number']   = (String) $response->CreationState->LabelData->shipmentNumber;
      $r['piece_number']      = (String) $response->CreationState->LabelData->licensePlate;
      $r['label_url']         = (String) $response->CreationState->LabelData->labelUrl;
      $r['gesamt'] = $response;
    }else{      
      $r['shipment_number']   = (String) $response->CreationState->ShipmentNumber->shipmentNumber;
      $r['piece_number']      = (String) $response->CreationState->PieceInformation->PieceNumber->licensePlate;
      $r['label_url']         = (String) $response->CreationState->Labelurl;
    }
    return $r;
  }

  function dumpRequest($fileName, $contents){
    $dir = $this->app->getTmpFolder();
    file_put_contents("{$dir}$fileName", $contents);
  }

  function GetExportDocDD($shippment_number, $api22 = false) {

    $this->buildClient();

    $shipment = array();

    // Version
    if($api22)
    {
      $shipment['Version']  = array('majorRelease' => '2', 'minorRelease' => '0');
    }else{
      $shipment['Version']  = array('majorRelease' => '1', 'minorRelease' => '0');
    }

    // Order
    $shipment['ShipmentNumber'] = array('shipmentNumber'=>$shippment_number);
    //$shipment['DocType'] = 'PDF';
    $shipment['DocType'] = 'URL';
    if(empty($this->client))return false;
    // Fixme
    try {
      if($api22)
        $response = $this->client->getExportDoc($shipment);
      else
        $response = $this->client->GetExportDocDD($shipment);
    } catch(SoapFault $exception)
    {
      if(trim($exception->getMessage()) === 'Authorization Required')
      {
        $this->errors[] = 'Fehlerhafte DHL Versenden Zugangsdaten';
        return false;
      }
      
      $this->errors[] = 'Fehler von DHL Versenden: '.$exception->getMessage();
      return false;
    }
    
    if (((isset($response->status) && $response->status->StatusCode != 0) ||
        (isset($response->Status) && $response->Status->statusCode != 0)) ||
        is_soap_fault($response)
      ) {
      $this->errors[] = "<b>Fehlermeldung von DHL:</b>";
      if (is_soap_fault($response)) {
        $this->errors[] = $response->faultstring;
      } else {

        $this->errors[] = isset($response->status)?$response->status->StatusMessage:$response->Status->statusMessage;
      }
      return false;
    }
    $r = array();
    if($api22)
    {
      $r['export_pdf']   = (String) $response->ExportDocData->exportDocData;
      $r['export_url']   = (String) $response->ExportDocData->exportDocURL;
    } else {
      $r['export_pdf']   = (String) $response->ExportDocData->ExportDocPDFData;
      $r['export_url']   = (String) $response->ExportDocData->ExportDocURL;
    }
    return $r;
  }

  private function buildAuthHeader() {

    $head = $this->einstellungen;

    $auth_params = array(
        'user' => $this->einstellungen['user'],
        'signature' => $this->einstellungen['signature'],
        'type'  => 0

        );
    try{
      $erg = new SoapHeader('http://dhl.de/webservice/cisbase','Authentification', $auth_params);
    } catch(SoapFault $exception)
    {
      $erg = false;
      $this->errors[] = "Authentifizierungsfehler: ".$exception->getMessage();
    }
    return $erg;
  }
}
