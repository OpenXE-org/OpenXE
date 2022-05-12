<?php
// thx to  Tobias Redmann



/**
 * 
 */
require_once(dirname(__DIR__).'/class.versanddienstleister.php');
class Versandart_intraship extends Versanddienstleister{
  
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

  /**
   * Constructor for Shipment SDK
   * 
   * @param type $api_einstellungen
   * @param type $customer_info
   */
   
  function __construct(&$app, $id) {
    $this->id = $id;
    $this->app = &$app;
    $einstellungen_json = $this->app->DB->Select("SELECT einstellungen_json FROM versandarten WHERE id = '$id' LIMIT 1");
    $this->paketmarke_drucker = $this->app->DB->Select("SELECT paketmarke_drucker FROM versandarten WHERE id = '$id' LIMIT 1");
    $this->export_drucker = $this->app->DB->Select("SELECT export_drucker FROM versandarten WHERE id = '$id' LIMIT 1");
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

    $this->errors = array();
    $data = $this->einstellungen;
    $this->info = array(
            'company_name'    => $data['intraship_company_name'],
            'street_name'     => $data['intraship_street_name'],
            'street_number'   => $data['intraship_street_number'],
            'zip'             => $data['intraship_zip'],
            'country'         => $data['intraship_country'],
            'city'            => $data['intraship_city'],
            'email'           => $data['intraship_email'],
            'phone'           => $data['intraship_phone'],
            'internet'        => $data['intraship_internet'],
            'contact_person'  => $data['intraship_contact_person'],
            'export_reason'  => $data['intraship_exportgrund']
            );
  //function __construct($api_einstellungen, $customer_info) {



        if($land!=$this->app->erp->Firmendaten("land"))
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
    'partnerid' => array('typ'=>'text','bezeichnung'=>'Partner ID Inland:','info'=>'meist 01, manchmal 02, 03 etc.'),
    'partnerid_welt' => array('typ'=>'text','bezeichnung'=>'Partner ID Welt:','info'=>'falls leer wird die inländische Partner ID verwendet.'),
    'api_user' => array('typ'=>'text','bezeichnung'=>'API User:','info'=>'Bitte anfragen beim Support'),
    'api_password' => array('typ'=>'text','bezeichnung'=>'API Passwort:','info'=>'Bitte anfragen beim Support'),
    'intraship_retourenaccount' => array('typ'=>'text','bezeichnung'=>'Retouren Account:','info'=>'14 Stellige DHL-Retoure Abrechnungsnummer'),
    'intraship_retourenlabel'=>array('typ'=>'checkbox','bezeichnung'=>'Vorauswahl Retourenlabel:','info'=>'Druckt Retourenlabel mit'),
    
    'intraship_company_name' => array('typ'=>'text','bezeichnung'=>'Versender Firma:'),
    'intraship_street_name' => array('typ'=>'text','bezeichnung'=>'Versender Strasse:'),
    'intraship_street_number' => array('typ'=>'text','bezeichnung'=>'Versender Strasse Nr.:'),
    //'intraship_name' => array('typ'=>'text','bezeichnung'=>'Versender Ansprechpartner:'),
    'intraship_zip' => array('typ'=>'text','bezeichnung'=>'Versender PLZ:'),
    'intraship_city' => array('typ'=>'text','bezeichnung'=>'Versender Stadt:'),
    'intraship_country' => array('typ'=>'text','bezeichnung'=>'Versender Land:','info'=>'germany'),
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
    
    'intraship_exportgrund' => array('typ'=>'text','bezeichnung'=>'Export:','info'=>'z.B. Computer Zubehör'),
    
    'intraship_WeightInKG' => array('typ'=>'text','bezeichnung'=>'Standard Gewicht:','info'=>'in KG'),
    'intraship_LengthInCM' => array('typ'=>'text','bezeichnung'=>'Standard Länge:','info'=>'in cm'),
    'intraship_WidthInCM' => array('typ'=>'text','bezeichnung'=>'Standard Breite:','info'=>'in cm'),
    'intraship_HeightInCM' => array('typ'=>'text','bezeichnung'=>'Standard Höhe:','info'=>'in cm'),
    'intraship_PackageType' => array('typ'=>'text','bezeichnung'=>'Standard Paket:','info'=>'z.B. PL'),    
    
    'log'=>array('typ'=>'checkbox','bezeichnung'=>'Logging')
    );
    
  }
  
 
  public function Paketmarke($doctyp, $docid, $target = '', $error = false)
  {
    $id = $docid;
    $drucken = $this->app->Secure->GetPOST("drucken");
    $anders = $this->app->Secure->GetPOST("anders");
    $land = $this->app->Secure->GetPOST("land");
    $tracking_again = $this->app->Secure->GetGET("tracking_again");


    $versandmit= $this->app->Secure->GetPOST("versandmit");
    $trackingsubmit= $this->app->Secure->GetPOST("trackingsubmit");
    $versandmitbutton = $this->app->Secure->GetPOST("versandmitbutton");
    $tracking= $this->app->Secure->GetPOST("tracking");
    $trackingsubmitcancel= $this->app->Secure->GetPOST("trackingsubmitcancel");
    $retourenlabel = $this->app->Secure->GetPOST("retourenlabel");    
    
    $kg= $this->app->Secure->GetPOST("kg1");
    $name= $this->app->Secure->GetPOST("name");
    $name2= $this->app->Secure->GetPOST("name2");
    $name3= $this->app->Secure->GetPOST("name3");
    $strasse= $this->app->Secure->GetPOST("strasse");
    $hausnummer= $this->app->Secure->GetPOST("hausnummer");
    $plz= $this->app->Secure->GetPOST("plz");
    $ort= $this->app->Secure->GetPOST("ort");
    $email= $this->app->Secure->GetPOST("email");
    $phone= $this->app->Secure->GetPOST("phone");
    $nummeraufbeleg= $this->app->Secure->GetPOST("nummeraufbeleg");

    if($sid=="")
      $sid= $this->app->Secure->GetGET("sid");

    if($zusatz=="express")
      $this->app->Tpl->Set('ZUSATZ',"Express");

    if($zusatz=="export")
      $this->app->Tpl->Set('ZUSATZ',"Export");

    $id = $this->app->Secure->GetGET("id");
    $drucken = $this->app->Secure->GetPOST("drucken");
    $anders = $this->app->Secure->GetPOST("anders");
    $land = $this->app->Secure->GetGET("land");
    if($land=="") $land = $this->app->Secure->GetPOST("land");
    $tracking_again = $this->app->Secure->GetGET("tracking_again");


    $versandmit= $this->app->Secure->GetPOST("versandmit");
    $trackingsubmit= $this->app->Secure->GetPOST("trackingsubmit");
    $versandmitbutton = $this->app->Secure->GetPOST("versandmitbutton");
  $tracking= $this->app->Secure->GetPOST("tracking");
  $trackingsubmitcancel= $this->app->Secure->GetPOST("trackingsubmitcancel");
  $retourenlabel = $this->app->Secure->GetPOST("retourenlabel");
  if($typ=="DHL" || $typ=="dhl")
    $versand = "dhl";
  else if($typ=="Intraship")
    $versand = "intraship";
  else $versand = $typ;

  if($sid == "versand")
  {
    $projekt = $this->app->DB->Select("SELECT projekt FROM versand WHERE id='$id' LIMIT 1");
  }else{
    $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
  }
  $intraship_weightinkg = $this->app->DB->Select("SELECT intraship_weightinkg FROM projekt WHERE id='$projekt' LIMIT 1");


  if($trackingsubmit!="" || $trackingsubmitcancel!="")
  {

    if($sid=="versand")
    {
      // falche tracingnummer bei DHL da wir in der Funktion PaketmarkeDHLEmbedded sind
      if((strlen($tracking) < 12 || strlen($tracking) > 20) && $trackingsubmitcancel=="" && ($typ=="DHL" || $typ=="Intraship"))
      {
        header("Location: index.php?module=versanderzeugen&action=frankieren&id=$id&land=$land&tracking_again=1");
        exit;
      }
      else
      {
        $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versand', tracking='$tracking',
            versendet_am=NOW(),versendet_am_zeitstempel=NOW(), abgeschlossen='1',logdatei=NOW() WHERE id='$id' LIMIT 1");

        $this->app->erp->VersandAbschluss($id);
        //versand mail an kunden
        $this->app->erp->Versandmail($id);

        $weiterespaket=$this->app->Secure->GetPOST("weiterespaket");
        $lieferscheinkopie=$this->app->Secure->GetPOST("lieferscheinkopie");
        if($weiterespaket=="1")
        {
          if($lieferscheinkopie=="1") $lieferscheinkopie=0; else $lieferscheinkopie=1;
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
      }
      exit;
    } else {
      //direkt aus dem Lieferschein
      if($id > 0)
      {
        $adresse = $this->app->DB->Select("SELECT adresse FROM lieferschein WHERE id='$id' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
        $kg = $this->app->Secure->GetPOST("kg1");
        if($kg=="") {
          $kg = $this->app->erp->VersandartMindestgewicht($id);
        }
        $auftrag = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id = '$id'");
        $this->app->DB->Insert("INSERT INTO versand (id,versandunternehmen, tracking,
          versendet_am,abgeschlossen,lieferschein,
          freigegeben,firma,adresse,projekt,gewicht,paketmarkegedruckt,anzahlpakete)
            VALUES ('','$versand','$tracking',NOW(),1,'$id',1,'".$this->app->User->GetFirma()."',
            '$adresse','$projekt','$kg','1','1') ");
        $versandId = $this->app->DB->GetInsertID();
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
  }


  if($versandmitbutton!="")
  {

    if($sid=="versand")
    {
      $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versandmit',
          versendet_am=NOW(),versendet_am_zeitstempel=NOW(),abgeschlossen='1' WHERE id='$id' LIMIT 1");

      $this->VersandAbschluss($id);
      //versand mail an kunden
      $this->Versandmail($id);

      header("Location: index.php?module=versanderzeugen&action=offene");
      exit;
    }
  }

  if($sid=="versand")
  {
    // wenn paketmarke bereits gedruckt nur tracking scannen
    $paketmarkegedruckt = $this->app->DB->Select("SELECT paketmarkegedruckt FROM versand WHERE id='$id' LIMIT 1");

    if($paketmarkegedruckt>=1)
      $tracking_again=1;
  }
    
    if($anders!="")
    {
      
    }
    else  if(($drucken!="" || $tracking_again=="1") && !$error)
    {



      if($tracking_again!="1")
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
        $name = substr($this->app->erp->ReadyForPDF($name),0,30);
        $name2 = $this->app->erp->ReadyForPDF($name2);
        $name3 = $this->app->erp->ReadyForPDF($name3);
        $strasse = $this->app->erp->ReadyForPDF($strasse);
        $hausnummer = $this->app->erp->ReadyForPDF($hausnummer);
        $plz = $this->app->erp->ReadyForPDF($plz);
        $ort = $this->app->erp->ReadyForPDF(html_entity_decode($ort));
        $land = $this->app->erp->ReadyForPDF($land);

        //SetKonfigurationValue($name,$value)

        $module = $this->app->Secure->GetGET("module");
        //TODO Workarrond fuer lieferschein
        if($module=="lieferschein")
        {
          $lieferschein = $id;
        }
        else {
          $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");
          if($lieferschein <=0) $lieferschein=$id;
        }

        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
        $lieferscheinnummer = "LS".$this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");

        //pruefe ob es auftragsnummer gibt dann nehmen diese
        $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
        if($auftragid > 0)
        {
          $nummeraufbeleg = "AB".$this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
        } else {
          $nummeraufbeleg = $lieferscheinnummer;
        }

        $rechnung = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein='$lieferschein' LIMIT 1");

        $rechnung_data = $this->app->DB->SelectArr("SELECT * FROM rechnung WHERE id='$rechnung' LIMIT 1");

        // fuer export
        $email = $rechnung_data[0]['email']; //XXX
        $phone = $rechnung_data[0]['telefon']; //XXX
        $rechnungssumme = $rechnung_data[0]['soll']; //XXX

        if($rechnung){
          $artikel_positionen = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung='$rechnung'");
        } else {
          $artikel_positionen = $this->app->DB->SelectArr("SELECT * FROM lieferschein_position WHERE lieferschein='$lieferschein'");

        }
        $altersfreigabe = 0;
        for($i=0;$i<count($artikel_positionen);$i++)
        {
          $artikelaltersfreigabe = (int)$this->app->DB->Select("SELECT altersfreigabe FROM artikel WHERE id = '".$artikel_positionen[$i]['artikel']."' LIMIT 1");
          if($artikelaltersfreigabe > $altersfreigabe)$altersfreigabe = $artikelaltersfreigabe;
          //$lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='".$artikel_positionen[$i]['artikel']."' LIMIT 1");

          //if($lagerartikel=="1")
          {
            if($artikel_positionen[$i]['waehrung']=="") {
              $artikel_positionen[$i]['waehrung']="EUR";
            }
            $gewicht = $this->app->DB->Select("SELECT gewicht FROM artikel WHERE id='".$artikel_positionen[$i]['artikel']."' LIMIT 1");
            $porto = $this->app->DB->Select("SELECT porto FROM artikel WHERE id='".$artikel_positionen[$i]['artikel']."' LIMIT 1");

            if($gewicht <=0) $gewicht=0;

            //if($gewicht > 0) $gewicht = $artikel_positionen[$i]['menge']*$gewicht;
            //else $gewicht = 1.1*$artikel_positionen[$i]['menge'];

            if($porto!="1")
            {
              $artikel[] = array( 'description'=>substr($artikel_positionen[$i]['bezeichnung'],0,40),
                'countrycode'=>'DE',
                'commodity_code'=>'95061110', // zoll nummer?
                'amount'=>round($artikel_positionen[$i]['menge']),
                'netweightinkg'=>$gewicht,
                'grossweightinkg'=>$gewicht,
                'value'=>$artikel_positionen[$i]['preis'],
                'currency'=>$artikel_positionen[$i]['waehrung']);
            }
          }
        }

        $data = $this->einstellungen;

        if($phone=="") $phone=$data['intraship_phone'];



        if($land!=$this->app->erp->Firmendaten("land"))
        {
          if(!empty($data['partnerid_welt']) && !empty($data['partnerid_welt']))$this->einstellungen['partnerid'] = $data['partnerid_welt'];
        }

        if(isset($data['intraship_retourenaccount']) && $data['intraship_retourenaccount'])$einstellungen['intraship_retourenaccount'] = $data['intraship_retourenaccount'];

        // your company info
        $info = array(
            'company_name'    => $data['intraship_company_name'],
            'street_name'     => $data['intraship_street_name'],
            'street_number'   => $data['intraship_street_number'],
            'zip'             => $data['intraship_zip'],
            'country'         => $data['intraship_country'],
            'city'            => $data['intraship_city'],
            'email'           => $data['intraship_email'],
            'phone'           => $data['intraship_phone'],
            'internet'        => $data['intraship_internet'],
            'contact_person'  => $data['intraship_contact_person'],
            'export_reason'  => $data['intraship_exportgrund']
            );

        // receiver details
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
            'email'          => $email,
            'phone'          => $phone,
            'ordernumber'   => $nummeraufbeleg,
            'weight'        => $kg,
            'amount'        => str_replace(",",".",$rechnungssumme),
            'currency'        => 'EUR'
            );
        if(!is_null($zusatz) && isset($zusatz['abholdatum']))$customer_details['abholdatum'] = $zusatz['abholdatum'];
        if(!is_null($zusatz) && isset($zusatz['retourenlabel']))$customer_details['intraship_retourenlabel'] = $zusatz['retourenlabel'];
        if($altersfreigabe > 0)$customer_details['altersfreigabe'] = $altersfreigabe;
        //$dhl = new DHLBusinessShipment($einstellungen, $info);

        $shipment_details['WeightInKG'] = $data['intraship_WeightInKG'];
        $shipment_details['LengthInCM'] = $data['intraship_LengthInCM'];
        $shipment_details['WidthInCM'] = $data['intraship_WidthInCM'];
        $shipment_details['HeightInCM'] = $data['intraship_HeightInCM'];
        $shipment_details['PackageType'] = $data['intraship_PackageType'];

        if($data['intraship_note']=="") $data['intraship_note'] = $rechnungsnummer;

        if($land==$this->app->erp->Firmendaten("land"))
        {
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

            $cod_details = array(
                'amount'=>str_replace(",",".",$betrag),
                'currency'=>'EUR'
                );

            $response = $this->createNationalShipment($customer_details,$shipment_details,$bank_details,$cod_details,$rechnungsnummer);
          } else {
            //$customer_details['ordernumber']="";
            $response = $this->createNationalShipment($customer_details,$shipment_details);
          }
        } else {
          $customer_details['EU'] = $this->app->erp->IstEU($land)?1:0;
          $response = $this->createWeltShipment($customer_details,null,$bank_details,$cod_details,$artikel);
          if($response)
          {
          // Zoll Papiere
            //$dhl = new DHLBusinessShipment($einstellungen, $info);
            $response_export = $this->GetExportDocDD($response['shipment_number']);
          }else{
            $dump = $this->app->erp->VarAsString($this->errors);
            $this->app->erp->Protokoll("Fehler Intraship API beim Erstellen Label fuer Versand $id LS $lieferschein",$dump);
          }
        }
  
        $data['intraship_drucker'] = $this->paketmarke_drucker;
        $data['druckerlogistikstufe2'] = $this->export_drucker;
        
        if(!$data['intraship_drucker'])
        {
          if($this->app->erp->GetStandardPaketmarkendrucker()>0)
            $data['intraship_drucker'] = $this->app->erp->GetStandardPaketmarkendrucker();
        }

        if(!$data['druckerlogistikstufe2'])
        {
          if($this->app->erp->GetInstrashipExport($projekt)>0)
            $data['druckerlogistikstufe2'] = $this->app->erp->GetInstrashipExport($projekt);
        }



        if($response)
        {
          //$response['label_url']
          //$response['shipment_number']
          $tmppdf = $this->app->erp->DownloadFile($response['label_url'],"Intraship_Versand_".$id."_","pdf");
          $this->app->erp->Protokoll("Erfolg Paketmarke Drucker ".$data['intraship_drucker']," Datei: $tmppdf URL: ".$response['label_url']);
          $this->app->printer->Drucken($data['intraship_drucker'],$tmppdf);
          unlink($tmppdf);
        } else {
          $dump = $this->app->erp->VarAsString($this->errors);
          $this->app->erp->Protokoll("Fehler Intraship API beim Erstellen Label fuer Versand $id LS $lieferschein",$dump);
        }

        if($response_export)
        {
          $tmppdf = $this->app->erp->DownloadFile($response_export['export_url'],"Export_Intraship_Versand_".$id."_");
          $this->app->erp->Protokoll("Erfolg Export Dokumente Drucker ".$data['druckerlogistikstufe2']," Datei: $tmppdf URL: ".$response_export['export_url']);
          $this->app->printer->Drucken($data['druckerlogistikstufe2'],$tmppdf);
          unlink($tmppdf);
        } else {
          $dump = $this->app->erp->VarAsString($this->errors);
          $this->app->erp->Protokoll("Fehler Intraship Export Dokument API beim Erstellen fuer Versand $id LS $lieferschein",$dump);
        }
        
        if($response)return false;
        return $this->errors;
        
      }
      
      
      if($this->app->Secure->GetPOST('drucken') || $this->app->Secure->GetPOST('anders'))
      {
        
        
      }else{
        if(empty($this->Eintellungen['retourenaccount']) || !$this->Eintellungen['retourenaccount'])            
        {
          $this->app->Tpl->Add('VORRETOURENLABEL', '<!--');
          $this->app->Tpl->Add('NACHRETOURENLABEL', '-->');
        }
        if(isset($this->Eintellungen['retourenlabel']) && $this->Eintellungen['retourenlabel'])$this->app->Tpl->Add('RETOURENLABEL',' checked="checked" '); 
      }
    }
   
    //$this->info = $customer_info;
    if($target)$this->app->Tpl->Parse($target,'versandarten_intraship.tpl');
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

        error_log(print_r($message, true));

      } else {

        error_log($message);

      }

    }

  }

  function buildClient($retoure, $altersfreigabe = false) {

    $header = $this->buildAuthHeader();

    $location = self::PRODUCTION_URL;
    //$location = self::SANDBOX_URL;

    $auth_params = array(
        'login' => $this->einstellungen['api_user'],
        'password' => $this->einstellungen['api_password'],
        'location'  => $location,
        'trace' => 1,
        'connection_timeout' => 30
        );

    $this->log($auth_params);
    try {
      $this->client = new SoapClient($altersfreigabe?self::API_URL22:($retoure?self::API_URL2:self::API_URL), $auth_params);
    } catch(SoapFault $exception)
    {
      die("Verbindungsfehler: ".$exception->getMessage());
      $this->errors[] = "Verbindungsfehler: ".$exception->getMessage();
      return;
    }
    try {
      $this->client->__setSoapHeaders($header);
    } catch(SoapFault $exception)
    {
      die("Verbindungsfehler: ".$exception->getMessage());
      $this->errors[] = "Verbindungsfehler: ".$exception->getMessage();
      return;
    }

    $this->log($this->client);

  }

  function createNationalShipment($customer_details, $shipment_details = null, $bank_details = null, $cod_details = null) {  
    $api2 = false;
    $api22 = isset($customer_details['altersfreigabe']) && $customer_details['altersfreigabe'];
    if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])$api2 = true;

    if(isset($this->einstellungen['leitcodierung']) && $this->einstellungen['leitcodierung'])$api22 = true;
    
    $this->buildClient($api2, $api22);

    $shipment = array();

    // Version
    $shipment['Version']  = array('majorRelease' => '1', 'minorRelease' => '0');
    if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])
    {
      $shipment['Version']['minorRelease'] = '1';
    }
    if($api22)
    {
      $shipment['Version']  = array('majorRelease' => '2', 'minorRelease' => '0');
    }

    if($customer_details['country_code']=="" || $customer_details['country_code']=="DE") 
    { 
      $customer_details['country_code']="DE";
      $customer_details['country_zip']="germany";
    } else if ($customer_details['country_code']=="UK"){
      $customer_details['country_zip']="england";
    } else {
      $customer_details['country_zip']="other";
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
      $s['product'] = "V01PAK";
    }else{
      $s['ProductCode']               = 'EPN';
    }
    if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])
    {
      $s['ReturnShipmentBillingNumber'] = $this->einstellungen['intraship_retourenaccount'];
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

    if($api22)
    {
      $s['accountNumber'] = $this->einstellungen['ekp'].(strlen($this->einstellungen['partnerid'] <= 2)?$this->einstellungen['partnerid']."01":$this->einstellungen['partnerid']);
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
      $s['Service']['ServiceGroupOther']['COD']  = array();
      $s['Service']['ServiceGroupOther']['COD']['CODAmount'] = $cod_details['amount'];
      $s['Service']['ServiceGroupOther']['COD']['CODCurrency'] = $cod_details['currency'];
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

    $shipper['Address']['Origin'] = array('countryISOCode' => 'DE');

    $shipper['Communication']                   = array();
    if (preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9._-] +)+$/" , $this->info['email'])) {
      $shipper['Communication']['email']          = $this->info['email'];
    }
    $shipper['Communication']['phone']          = $this->info['phone'];
    $shipper['Communication']['internet']       = $this->info['internet'];
    $shipper['Communication']['contactPerson']  = $this->info['contact_person'];


    $shipment['ShipmentOrder']['Shipment']['Shipper'] = $shipper;

    if($api22)
    {
      $receiver = array();
      $receiver['name1'] = $customer_details['name1'];
      if($customer_details['name2']!="")
        $receiver['name2'] = $customer_details['name2'];
      if($customer_details['name3']!="")
        $receiver['name3'] = $customer_details['name3'];




      $receiver['Address'] = array();
      if($customer_details['name2']!="")
        $receiver['Address']['addressAddition'] = $customer_details['name2'];
      $receiver['Address']['streetName']     = $customer_details['street_name'];
      $receiver['Address']['streetNumber']   = $customer_details['street_number'];
      $receiver['Address']['zip']            = $customer_details['zip'];
      $receiver['Address']['city']           = $customer_details['city'];
      $receiver['Communication']                   = array();

      if($customer_details['c/o']!="")
        $receiver['Communication']['contactPerson']  = $customer_details['c/o'];

      $receiver['Address']['Origin'] = array('countryISOCode' => $customer_details['country_code']);

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

      $ReturnReceiver['Address']['Origin'] = array('countryISOCode' => 'DE');

      $ReturnReceiver['Communication']                   = array();
      if (preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9._-] +)+$/" , $this->info['email'])) {
        $shipper['Communication']['email']          = $this->info['email'];
      }
      $ReturnReceiver['Communication']['phone']          = $this->info['phone'];
      $ReturnReceiver['Communication']['internet']       = $this->info['internet'];
      $ReturnReceiver['Communication']['contactPerson']  = $this->info['contact_person'];


      $shipment['ShipmentOrder']['Shipment']['ReturnReceiver'] = $ReturnReceiver;
    }
    try {      
      if($api22)
      {
        $response = $this->client->createShipmentOrder($shipment);
      }
      else{
        $response = $this->client->CreateShipmentDD($shipment);
      }
     
    } catch(SoapFault $exception)
    {
      if(trim($exception->getMessage()) == 'Authorization Required')
      {
        $this->errors[] = 'Fehlerhafte Intraship Zugangsdaten';
        return false;
      }
      $this->errors[] = "Fehler von Intraship: ".$exception->getMessage();
      return;
    }
    if (is_soap_fault($response) || (isset($response->status) && $response->status->StatusCode != 0 || isset($response->Status) && $response->Status->statusCode != 0)) {
      $this->errors[] = "<b>Fehlermeldung von DHL:</b>";

      if (is_soap_fault($response)) {

        $this->errors[] = $response->faultstring;

      } else {

        $this->errors[] = isset($response->status)?$response->status->StatusMessage:$response->Status->statusMessage;

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
    } else {
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

  }


  function createWeltShipment($customer_details, $shipment_details = null, $bank_details = null, $cod_details = null,$artikel=null) {
    $api2 = false;
    $api22 = isset($customer_details['altersfreigabe']) && $customer_details['altersfreigabe'];
    //if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])$api2 = true;
    $api22 = false;
    $this->buildClient($api2);

    $shipment = array();

    if($customer_details['country_code']=="DE")
    {
      $customer_details['country']="germany";
    } else if ($customer_details['country_code']=="UK")
    {                      
      $customer_details['country']="england";
    } else {
      $customer_details['country']="other";
    } 

    // Version
    if($api22)
    {
      $shipment['Version']  = array('majorRelease' => '2', 'minorRelease' => '0');
    }else{
      $shipment['Version']  = array('majorRelease' => '1', 'minorRelease' => '0');
      if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])
      {
        $shipment['Version']['minorRelease'] = '1';
      }
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
      $s['product'] = isset($customer_details['EU'])&&$customer_details['EU']?"V54EPAK": "V53WPAK";
      $s['accountNumber'] =$this->einstellungen['ekp'].$this->einstellungen['partnerid']."01";
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
    


    if(isset($customer_details['intraship_retourenlabel']) && $customer_details['intraship_retourenlabel'] && isset($this->einstellungen['intraship_retourenaccount']) && $this->einstellungen['intraship_retourenaccount'])
    {
      $s['ReturnShipmentBillingNumber'] = $this->einstellungen['intraship_retourenaccount'];
    }
    
    if($api22)
    {
      if ($shipment_details == null) {
        $s['ShipmentItem']  = array();
        $s['ShipmentItem']['weightInKG'] = '3';
        $s['ShipmentItem']['lengthInCM'] = '50';
        $s['ShipmentItem']['widthInCM']  = '30';
        $s['ShipmentItem']['heightInCM'] = '15';
      }      
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
      $s['Service']['ServiceGroupOther']['COD']  = array();
      $s['Service']['ServiceGroupOther']['COD']['CODAmount'] = $cod_details['amount'];
      $s['Service']['ServiceGroupOther']['COD']['CODCurrency'] = $cod_details['currency'];
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
    $shipment['ShipmentOrder']['Shipment']['ShipmentDetails'] = $s;

    //$shipment['ShipmentOrder']['Shipment']['ShipmentDetails'] = $s;


    $shipper = array();
    $shipper['Company'] = array();
    $shipper['Company']['Company'] = array();
    $shipper['Company']['Company']['name1'] = $this->info['company_name'];

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

    $shipper['Address']['Origin'] = array('countryISOCode' => 'DE');



    $shipper['Communication']                   = array();
    if (preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9._-] +)+$/" , $this->info['email'])) {
      $shipper['Communication']['email']          = $this->info['email'];
    }
    if(empty($this->info['phone']))$this->info['phone'] = '0';
    $this->info['phone'] = str_replace('+','00',trim($this->info['phone']));
    $this->info['phone'] = preg_replace('![^0-9]!', '', $this->info['phone']); 
    if(empty($this->info['phone']))$this->info['phone'] = '0';
    
    $shipper['Communication']['phone']          = $this->info['phone'];
    $shipper['Communication']['internet']       = $this->info['internet'];

    $shipper['Communication']['contactPerson']  = $this->info['contact_person'];


    $shipment['ShipmentOrder']['Shipment']['Shipper'] = $shipper;

    $receiver = array();



    $receiver['Company']['Company']  = array();
    $receiver['Company']['Company']['name1'] = $customer_details['name1'];
    if($customer_details['name2'])$receiver['Company']['Company']['name2'] = $customer_details['name2'];

    if($customer_details['name2']!="")
      $tmp_name = explode(' ',$customer_details['name2'],2);
    else
      $tmp_name = explode(' ',$customer_details['name1'],2);

    if(isset($tmp_name[2]) && $tmp_name[2]){
      $receiver['Company']['Person']  = array();
      $receiver['Company']['Person']['firstname'] = $tmp_name[0];
      $receiver['Company']['Person']['lastname'] = $tmp_name[1];
    }
    /*
       $receiver['Company'] = array();
       $receiver['Company']['Person']  = array();
       $receiver['Company']['Person']['firstname'] = $customer_details['first_name'];
       $receiver['Company']['Person']['lastname'] = $customer_details['last_name'];
     */
    $receiver['Address'] = array();
    $receiver['Address']['streetName']     = $customer_details['street_name'];
    $receiver['Address']['streetNumber']   = $customer_details['street_number'];
    if($api22)
    {
      $receiver['Address']['zip']            = $customer_details['zip'];
    }else{
      $receiver['Address']['Zip']            = array();
      $receiver['Address']['Zip'][strtolower($customer_details['country'])]  = $customer_details['zip'];
    }
    $receiver['Address']['city']           = $customer_details['city'];
    $receiver['Communication']                   = array();
    if (preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9._-] +)+$/" , $customer_details['email'])) {
      $receiver['Communication']['email']          = $customer_details['email'];
    }
    
    if(empty($customer_details['phone']))$customer_details['phone'] = '0';
    $customer_details['phone'] = str_replace('+','00',trim($customer_details['phone']));
    $customer_details['phone'] = preg_replace('![^0-9]!', '', $customer_details['phone']); 
    if(empty($customer_details['phone']))$customer_details['phone'] = '0';
    $receiver['Communication']['phone']          = $customer_details['phone'];

    if($customer_details['c/o']=="") $customer_details['c/o'] = $customer_details['name2'];
    if($customer_details['c/o']=="") $customer_details['c/o'] = $customer_details['name1'];
    $receiver['Communication']['contactPerson']  = $customer_details['c/o'];

    $receiver['Address']['Origin'] = array('countryISOCode' => $customer_details['country_code']);

    $shipment['ShipmentOrder']['Shipment']['Receiver'] = $receiver;

    
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
      if (preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9._-] +)+$/" , $this->info['email'])) {
        $shipper['Communication']['email']          = $this->info['email'];
      }
      $ReturnReceiver['Communication']['phone']          = $this->info['phone'];
      $ReturnReceiver['Communication']['internet']       = $this->info['internet'];
      $ReturnReceiver['Communication']['contactPerson']  = $this->info['contact_person'];


      $shipment['ShipmentOrder']['Shipment']['ReturnReceiver'] = $ReturnReceiver;
    }

    if($api22)
    {
      //$export['invoiceType'] = "commercial";
      //$export['invoiceDate'] = date('Y-m-d');
      $export['invoiceNumber'] = $customer_details['ordernumber'];
      $export['exportType'] = 0;
      $export['exportTypeDescription'] = $this->info['export_reason'];
      $export['commodityCode'] = "";
      $export['termsOfTrade'] = "DDP";
      $export['amount'] = count($artikel);
      //$export['Description'] = $this->info['export_reason'];
      //$export['CountryCodeOrigin'] = "DE";
      $export['additionalFee'] = "10";
      $export['customsValue'] = number_format($customer_details['amount'],2,".","");
      $export['customsCurrency'] = $customer_details['currency'];
      //$export['permitNumber'] = "";

    }else{
      $export['InvoiceType'] = "commercial";
      $export['InvoiceDate'] = date('Y-m-d');
      $export['InvoiceNumber'] = $customer_details['ordernumber'];
      $export['ExportType'] = 0;
      $export['ExportTypeDescription'] = $this->info['export_reason'];
      $export['CommodityCode'] = "";
      $export['TermsOfTrade'] = "DDP";
      $export['Amount'] = count($artikel);
      $export['Description'] = $this->info['export_reason'];
      $export['CountryCodeOrigin'] = "DE";
      $export['AdditionalFee'] = "10";
      $export['CustomsValue'] = number_format($customer_details['amount'],2,".","");
      $export['CustomsCurrency'] = $customer_details['currency'];
      $export['PermitNumber'] = "";
    }

    $p=0;
    for($i=0;$i<count($artikel);$i++)
    {
      if($p>4 && (($export['ExportDocPosition'][4]['CustomsValue'] + $artikel[$i]['value']*$artikel[$i]['amount']) > 0)) {
        if($artikel[$i]['currency']=="") $artikel[$i]['currency']="EUR";
        if($api22)
        {
          $export['ExportDocPosition'][4]['description'] = "Additional Positions";
          $export['ExportDocPosition'][4]['countryCodeOrigin'] = $artikel[$i]['countrycode'];
          $export['ExportDocPosition'][4]['commodityCode'] = $artikel[$i]['commodity_code'];
          $export['ExportDocPosition'][4]['amount'] += $artikel[$i]['amount'];
          $export['ExportDocPosition'][4]['netWeightInKG'] += $artikel[$i]['netweightinkg'];
          $export['ExportDocPosition'][4]['grossWeightInKG'] += $artikel[$i]['grossweightinkg'];
          $export['ExportDocPosition'][4]['customsValue'] += number_format($artikel[$i]['value']*$artikel[$i]['amount'],2,".","");
          $export['ExportDocPosition'][4]['customsCurrency'] = $artikel[$i]['currency'];
          
        }else{
          $export['ExportDocPosition'][4]['Description'] = "Additional Positions";
          $export['ExportDocPosition'][4]['CountryCodeOrigin'] = $artikel[$i]['countrycode'];
          $export['ExportDocPosition'][4]['CommodityCode'] = $artikel[$i]['commodity_code'];
          $export['ExportDocPosition'][4]['Amount'] += $artikel[$i]['amount'];
          $export['ExportDocPosition'][4]['NetWeightInKG'] += $artikel[$i]['netweightinkg'];
          $export['ExportDocPosition'][4]['GrossWeightInKG'] += $artikel[$i]['grossweightinkg'];
          $export['ExportDocPosition'][4]['CustomsValue'] += number_format($artikel[$i]['value']*$artikel[$i]['amount'],2,".","");
          $export['ExportDocPosition'][4]['CustomsCurrency'] = $artikel[$i]['currency'];
        }
      } else {

        if($artikel[$i]['value']*$artikel[$i]['amount'] > 0)
        {
          if($artikel[$i]['currency']=="") $artikel[$i]['currency']="EUR";
          if($api22)
          {
            $export['ExportDocPosition'][$p]['description'] = preg_replace("/[^a-z0-9-.\ \]\[\)\(]/i",'',str_replace(array('ä','Ä','ö','Ö','ü','Ü','ß'),array('ae','Ae','oe','Oe','ue','Ue','ss'),$artikel[$i]['description']));
            $export['ExportDocPosition'][$p]['countryCodeOrigin'] = $artikel[$i]['countrycode'];
            $export['ExportDocPosition'][$p]['commodityCode'] = $artikel[$i]['commodity_code'];
            $export['ExportDocPosition'][$p]['amount'] = round($artikel[$i]['amount']);
            $export['ExportDocPosition'][$p]['netWeightInKG'] = $artikel[$i]['netweightinkg'];
            $export['ExportDocPosition'][$p]['grossWeightInKG'] = $artikel[$i]['grossweightinkg'];
            
          }else{
            $export['ExportDocPosition'][$p]['Description'] = preg_replace("/[^a-z0-9-.\ \]\[\)\(]/i",'',str_replace(array('ä','Ä','ö','Ö','ü','Ü','ß'),array('ae','Ae','oe','Oe','ue','Ue','ss'),$artikel[$i]['description']));
            $export['ExportDocPosition'][$p]['CountryCodeOrigin'] = $artikel[$i]['countrycode'];
            $export['ExportDocPosition'][$p]['CommodityCode'] = $artikel[$i]['commodity_code'];
            $export['ExportDocPosition'][$p]['Amount'] = round($artikel[$i]['amount']);
            $export['ExportDocPosition'][$p]['NetWeightInKG'] = $artikel[$i]['netweightinkg'];
            $export['ExportDocPosition'][$p]['GrossWeightInKG'] = $artikel[$i]['grossweightinkg'];
          }

          if($artikel[$i]['value']*$artikel[$i]['amount'] > 0)
            $export['ExportDocPosition'][$p]['CustomsValue'] = number_format($artikel[$i]['value']*$artikel[$i]['amount'],2,".","");
          $export['ExportDocPosition'][$p]['CustomsCurrency'] = $artikel[$i]['currency'];
          $p++;
        }
      }
    }

    $shipment['ShipmentOrder']['Shipment']['ExportDocument'] = $export;
    try {
      
      if($api22)
      {
        $response = $this->client->createShipmentOrder($shipment);
      }else{
        $response = $this->client->CreateShipmentDD($shipment);
      }
    } catch(SoapFault $exception)
    {
      if(trim($exception->getMessage()) == 'Authorization Required')
      {
        $this->errors[] = 'Fehlerhafte Intraship Zugangsdaten';
        return false;
      }
      $this->errors[] = "Fehler von Intraship: ".$exception->getMessage();
      return;
    }

    

    
    file_put_contents("request.xml",$this->client->__getLastRequest());
    file_put_contents("response.xml",$this->client->__getLastResponse());

    if (is_soap_fault($response) || (isset($response->status) && $response->status->StatusCode != 0 || isset($response->Status) && $response->Status->statusCode != 0)) {
      $this->errors[] = "<b>Fehlermeldung von DHL:</b>";

      if (is_soap_fault($response)) {

        $this->errors[] = $response->faultstring;
      } else {

        $this->errors[] = isset($response->status)?$response->status->StatusMessage:$response->Status->statusMessage;

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

    } else {

      $r = array();
      $r['shipment_number']   = (String) $response->CreationState->ShipmentNumber->shipmentNumber;
      $r['piece_number']      = (String) $response->CreationState->PieceInformation->PieceNumber->licensePlate;
      $r['label_url']         = (String) $response->CreationState->Labelurl;
      return $r;
    }
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

    // Fixme
    try {
      $response = $this->client->GetExportDocDD($shipment);
    } catch(SoapFault $exception)
    {
      if(trim($exception->getMessage()) == 'Authorization Required')
      {
        $this->errors[] = 'Fehlerhafte Intraship Zugangsdaten';
        return false;
      }
      
      $this->errors[] = "Fehler von Intraship: ".$exception->getMessage();
      return false;
    }
    
    if (is_soap_fault($response) || (isset($response->status) && $response->status->StatusCode != 0 || isset($response->Status) && $response->Status->statusCode != 0)) {
      $this->errors[] = "<b>Fehlermeldung von DHL:</b>";
      if (is_soap_fault($response)) {
        $this->errors[] = $response->faultstring;
      } else {
        $this->errors[] = isset($response->status)?$response->status->StatusMessage:$response->Status->statusMessage;
      }
      return false;
    } else {
      $r = array();
      $r['export_pdf']   = (String) $response->ExportDocData->ExportDocPDFData;
      $r['export_url']   = (String) $response->ExportDocData->ExportDocURL;
      return $r;
    }
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

?>
