<?php

require_once(dirname(__FILE__).'/../class.versanddienstleister.php');
class Versandart_sonstiges extends Versanddienstleister{

  private $einstellungen;

  private $info;

  private $client;
  private $credentials;

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

    $this->credentials = $this->einstellungen;
    //$this->errors = array();
    $data = $this->einstellungen;
    $this->info = $this->einstellungen;


  }

  public function GetBezeichnung()
  {
    return 'UPS';
  }

  function EinstellungenStruktur()
  {
    return array();

  }

  public function VersandartMindestgewicht()
  {
    if(!isset($this->einstellungen['WeightInKG']))return 1;
    if($this->einstellungen['WeightInKG'] === '')return 1;
    return str_replace(',','.',$this->einstellungen['WeightInKG']);
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
    $phone= $this->app->Secure->GetPOST("telefon");
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
    if($land=="")$land = $this->app->Secure->GetPOST("land");

    if($name3=="" && $land!=$this->app->erp->Firmendaten("land")) $name3=$name;

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

    if($trackingsubmit!="" || $trackingsubmitcancel!="")
    {

      if($sid==='versand') {
        // falche tracingnummer bei DHL da wir in der Funktion PaketmarkeDHLEmbedded sind
        if((strlen($tracking) < 12 || strlen($tracking) > 20) && $trackingsubmitcancel=='' && ($typ==='DHL' || $typ==='Intraship')) {
          $this->app->Location->execute("index.php?module=versanderzeugen&action=frankieren&id=$id&land=$land&tracking_again=1");
        }
        $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versand', tracking='$tracking',
            versendet_am=NOW(),versendet_am_zeitstempel=NOW(), abgeschlossen='1',logdatei=NOW() WHERE id='$id' LIMIT 1");

        $this->app->erp->VersandAbschluss($id);
        $this->app->erp->RunHook('versanderzeugen_frankieren_hook1', 1, $id);
        //versand mail an kunden
        $this->app->erp->Versandmail($id);

        $weiterespaket=$this->app->Secure->GetPOST("weiterespaket");
        $lieferscheinkopie=$this->app->Secure->GetPOST("lieferscheinkopie");
        if($weiterespaket=='1') {
          if($lieferscheinkopie=='1') {
            $lieferscheinkopie=0;
          }
          else {
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
          $this->app->Location->execute('index.php?module=versanderzeugen&action=einzel&id='.$newid);
        }
        $url = 'index.php?module=versanderzeugen&action=offene';
        $lieferschein = $this->app->DB->Select(sprintf('SELECT lieferschein FROM versand WHERE id = %d', $id));
        $this->app->erp->RunHook('paketmarke_abschluss_url', 2, $lieferschein, $url);
        $this->app->Location->execute($url);
      }
      //direkt aus dem Lieferschein
      if($id > 0) {
        $adresse = $this->app->DB->Select("SELECT adresse FROM lieferschein WHERE id='$id' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");
        $kg = $this->app->Secure->GetPOST("kg1");
        if($kg=="") {
            $kg = $this->app->erp->VersandartMindestgewicht($id);
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
    else  if(($drucken!="" || $tracking_again=="1") && !$error )
    {
      if($tracking_again!="1")
      {
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
        $lieferscheinnummer = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");

        //pruefe ob es auftragsnummer gibt dann nehmen diese
        /*
        $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
        if($auftragid > 0)
        {
          $nummeraufbeleg = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftragid' LIMIT 1");
        } else {
          $nummeraufbeleg = $lieferscheinnummer;
        }
        */
        $nummeraufbeleg = $lieferscheinnummer;

        $rechnung = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein='$lieferschein' LIMIT 1");

        $rechnung_data = $this->app->DB->SelectArr("SELECT * FROM rechnung WHERE id='$rechnung' LIMIT 1");

        // fuer export
        $email = $rechnung_data[0]['email']; //XXX
        if($phone=="")
          $phone = $rechnung_data[0]['telefon']; //XXX
        $rechnungssumme = $rechnung_data[0]['soll']; //XXX

        if($rechnung){
          $artikel_positionen = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung='$rechnung'");
        } else {
          $artikel_positionen = $this->app->DB->SelectArr("SELECT * FROM lieferschein_position WHERE lieferschein='$lieferschein'");
        }

        $data = $this->einstellungen;

        // your customer and api credentials from/for dhl
        $credentials = array(
            'api_user'  => $data['api_user'],
            'api_password'  => $data['api_password'],
            'api_accountnumber'  => $data['accountnumber'],
            'api_key'  => $data['api_key'],
            'log' => true
            );

        // your company info
        $info = array(
            'company_name'    => $data['company_name'],
            'street_name'     => $data['street_name'],
            'street_number'   => $data['street_number'],
            'zip'             => $data['zip'],
            'country'         => $data['country'],
            'city'            => $data['city'],
            'email'           => $data['email'],
            'phone'           => $data['phone'],
            'internet'        => $data['internet'],
            'contact_person'  => $data['contact_person'],
            'export_reason'  => $data['exportgrund']
            );
        // receiver details
        $customer_details = array(
            'name1'    => $name,
            'name2'     => $name2,
            'c/o'           => $name3,
            'street_name'   => $strasse,
            'street_number' => $hausnummer,
            //'country'       => 'germany',
            'country_code'       => $land,
            'zip'           => $plz,
            'city'          => $ort,
            'email'          => $email,
            'phone'          => $phone,
            'ordernumber'   => $nummeraufbeleg,
            'ordernumber2'   => $lieferscheinnummer,
            'weight'        => $kg,
            'amount'        => str_replace(",",".",$rechnungssumme),
            'currency'        => 'EUR'
            );


        $shipment_details['WeightInKG'] = $data['WeightInKG'];
        $shipment_details['LengthInCM'] = $data['LengthInCM'];
        $shipment_details['WidthInCM'] = $data['WidthInCM'];
        $shipment_details['HeightInCM'] = $data['HeightInCM'];
        $shipment_details['PackageType'] = $data['PackageType'];

        $shipment_details['service_code'] = $data['service_code'];
        $shipment_details['service_description'] = $data['service_description'];
        $shipment_details['package_code'] = $data['package_code'];
        $shipment_details['package_description'] = $data['package_description'];
        $shipment_details['exportgrund'] = $data['exportgrund'];

        if($data['note']=="") $data['note'] = $rechnungsnummer;

        //$response = $this->createShipment($customer_details,$shipment_details);
        

        $data['sonstiges_drucker'] = $this->paketmarke_drucker;
        $data['druckerlogistikstufe2'] = $this->export_drucker;

       
        if($this->app->erp->GetStandardPaketmarkendrucker()>0)
          $data['sonstiges_drucker'] = $this->app->erp->GetStandardPaketmarkendrucker();
     

        if($this->app->erp->GetStandardVersanddrucker($projekt)>0)
          $data['druckerlogistikstufe2'] = $this->app->erp->GetStandardVersanddrucker($projekt);
      }


      if($this->app->Secure->GetPOST('drucken') || $this->app->Secure->GetPOST('anders'))
      {


      }else{

      }
    }  

    //$this->info = $customer_info;
    if($target)$this->app->Tpl->Parse($target,'versandarten_sonstiges.tpl');
  }


  public function Export($daten)
  {

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



}
