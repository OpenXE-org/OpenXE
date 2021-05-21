<?php

use Xentral\Components\Database\Database;
use Xentral\Components\Http\JsonResponse;
use Xentral\Modules\Dhl\Api\DhlApi;
use Xentral\Modules\Dhl\Content\PackageContent;
use Xentral\Modules\Dhl\Exception\DhlBaseException;
use Xentral\Modules\Dhl\Exception\InvalidCredentialsException;
use Xentral\Modules\Dhl\Exception\InvalidRequestDataException;
use Xentral\Modules\Dhl\Exception\UnknownProductException;
use Xentral\Modules\Dhl\Factory\DhlApiFactory;
use Xentral\Modules\Dhl\Request\CreateInterationalShipmentRequest;
use Xentral\Modules\Dhl\Request\CreateNationalShipmentRequest;

require_once dirname(__DIR__) . '/class.versanddienstleister.php';

class Versandart_dhl extends Versanddienstleister
{

  private $einstellungen;

  private $info;

  private $credentials;

  public $paketmarke_drucker;
  public $export_drucker;

  public $errors;

  public $name;
  protected $voucherId;

  /**
   * Versandart_internetmarke constructor.
   *
   * @param ApplicationCore $app
   * @param int             $id
   */
  function __construct($app, $id)
  {
    $this->id = $id;
    $this->app = $app;
    $einstellungenArr = $this->app->DB->SelectRow("SELECT einstellungen_json,paketmarke_drucker,export_drucker FROM versandarten WHERE id = '$id' LIMIT 1");
    $einstellungen_json = $einstellungenArr['einstellungen_json'];
    $this->paketmarke_drucker = $einstellungenArr['paketmarke_drucker'];
    $this->export_drucker = $einstellungenArr['export_drucker'];

    $this->name = 'DHL 3.0';
    if($einstellungen_json){
      $this->einstellungen = json_decode($einstellungen_json, true);
    }else{
      $this->einstellungen = [];
    }
    $this->errors = [];
  }

  function ShowUserdata()
  {
    if(isset($this->app->Conf->WFuserdata)){
      return 'Userdata-Ordner: ' . $this->app->Conf->WFuserdata;
    }
  }

  public function Einstellungen($target = 'return')
  {
    if($this->app->Secure->GetPOST('testen')){
      $parameter1 = $this->einstellungen['pfad'];
      if($parameter1){
        if(is_dir($parameter1)){
          if(substr($parameter1, -1) !== '/'){
            $parameter1 .= '/';
          }

          if(file_put_contents($parameter1 . 'wawision_test.txt', 'TEST')){
            $this->app->Tpl->Add('MESSAGE',
              '<div class="info">Datei ' . $parameter1 . 'wawision_test.txt' . ' wurde erstellt!</div>');
          }else{
            $this->app->Tpl->Add('MESSAGE', '<div class="error">Datei konnte nicht angelegt werden!</div>');
          }
        }else{
          $this->app->Tpl->Add('MESSAGE',
            '<div class="error">Speicherort existiert nicht oder ist nicht erreichbar!</div>');
        }
      }else{
        $this->app->Tpl->Add('MESSAGE', '<div class="error">Bitte einen Speicherort angeben!</div>');
      }
    }

    parent::Einstellungen($target);
  }

  //TODO ....

  /*function Trackinglink($tracking, &$notsend, &$link, &$rawlink)
    {
    $notsend = 0;
  //$rawlink = 'https://tracking.dpd.de/parcelstatus/?locale=de_DE&query='.$tracking;
  $rawlink = ' https://www.gls-group.eu/276-I-PORTAL-WEB/content/GLS/DE03/DE/5004.htm?txtRefNo='.$tracking.'&txtAction=71000';
  $link = 'GLS Versand: '.$tracking.' ('.$rawlink.')';
  }*/

  public function GetBezeichnung()
  {
    return 'DHL 3.0';
  }

  /**
   * @return array[]
   */
  public function getCreateForm()
  {
    return [
      [
        'id' => 0,
        'name' => 'usernameGroup',
        'inputs' => [
          [
            'label' => 'Benutzername',
            'type' => 'text',
            'name' => 'dhl_username',
            'validation' => true,
          ],
        ],
      ],
      [
        'id' => 1,
        'name' => 'passwordGroup',
        'inputs' => [
          [
            'label' => 'Passwort',
            'type' => 'text',
            'name' => 'dhl_password',
            'validation' => true,
          ]
        ],
      ],
      [
        'id' => 2,
        'name' => 'accountNumberGroup',
        'inputs' => [
          [
            'label' => 'Abrechnungsnummer',
            'type' => 'text',
            'name' => 'dhl_accountnumber',
            'validation' => true,
          ]
        ],
      ]
    ];
  }

  /**
   * @param array $postData
   *
   * @return array
   */
  public function updatePostDataForAssistent($postData): array
  {
    $name = $this->app->erp->Firmendaten('name');
    $street = $this->app->erp->Firmendaten('strasse');
    $zip = $this->app->erp->Firmendaten('plz');
    $city = $this->app->erp->Firmendaten('ort');
    $country = $this->app->erp->Firmendaten('land');
    $houseNo = '';

    $streetParts = explode(' ', $street);
    $partsCount = count($streetParts);

    if($partsCount >= 2){
      $street = implode(' ', array_slice($streetParts, 0, $partsCount - 1));
      $houseNo = $streetParts[$partsCount - 1];
    }

    $postData['dhl_origin_name'] = $name;
    $postData['dhl_origin_street'] = $street;
    $postData['dhl_origin_houseno'] = $houseNo;
    $postData['dhl_origin_zip'] = $zip;
    $postData['dhl_origin_city'] = $city;
    $postData['dhl_origin_country'] = $country;

    return $postData;
  }

  /**
   * @return JsonResponse|null
   */
  public function AuthByAssistent()
  {
    $step = (int)$this->app->Secure->GetPOST('step');
    if($step == 0){
      $username = $this->app->Secure->GetPOST('dhl_username');
      $password = $this->app->Secure->GetPOST('dhl_password');
      $accountnumber = $this->app->Secure->GetPOST('dhl_accountnumber');

      $error = null;
      if(empty($username)){
        $error = 'Bitte Nutzernamen eingeben';
      }else if(empty($password)){
        $error = 'Bitte Passwort eingeben';
      }else if(empty($accountnumber)){
        $error = 'Bitte Abrechnungsnummer eingeben';
      }

      if($error != null) {
        return new JsonResponse(
          ['error' => $error],
          JsonResponse::HTTP_BAD_REQUEST
        );
      }

      try{
        $this->testCredentials($username, $password, $accountnumber);
      }catch (DhlBaseException $e){
        return new JsonResponse(
          ['error' => $e->getMessage()],
          JsonResponse::HTTP_BAD_REQUEST
        );
      }
    }

    return null;
  }


  /**
   * @return array
   */
  public function getStructureDataForClickByClickSave(): array
  {
    return $this->updatePostDataForAssistent([]);
  }

  function EinstellungenStruktur()
  {
    if(!empty($this->einstellungen['dhl_username']) && !empty($this->einstellungen['dhl_password'])){
      try{
        $this->testCredentials($this->einstellungen['dhl_username'], $this->einstellungen['dhl_password'], $this->einstellungen['dhl_accountnumber']);
        $this->app->Tpl->Set('MESSAGE', '<div class="info">Zugangsdaten erfolgreich &uuml;berpr&uuml;ft</div>');
      }catch (DhlBaseException $e){
        $this->app->Tpl->Set('MESSAGE', '<div class="error">' . $e->getMessage() . '</div>');
      }
    }



    return [
      'dhl_username' => ['typ' => 'text', 'bezeichnung' => 'Benutzername:'],
      'dhl_password' => ['typ' => 'text', 'bezeichnung' => 'Passwort:'],
      'dhl_accountnumber' => ['typ' => 'text', 'bezeichnung' => 'Abrechnungsnummer:'],
      'dhl_origin_name' => ['typ' => 'text', 'bezeichnung' => 'Versender Name:'],
      'dhl_origin_street' => ['typ' => 'text', 'bezeichnung' => 'Versender Strasse:'],
      'dhl_origin_houseno' => ['typ' => 'text', 'bezeichnung' => 'Versender Hausnummer:'],
      'dhl_origin_city' => ['typ' => 'text', 'bezeichnung' => 'Versender Ort:'],
      'dhl_origin_zip' => ['typ' => 'text', 'bezeichnung' => 'Versender PLZ:'],
      'dhl_origin_country' => ['typ' => 'text', 'bezeichnung' => 'Versender Land (2-stellig):'],
      'dhl_origin_email' => ['typ' => 'text', 'bezeichnung' => 'Versender Email:'],

      'dhl_height' => ['typ' => 'text', 'bezeichnung' => 'Standardh&ouml;he'],
      'dhl_width' => ['typ' => 'text', 'bezeichnung' => 'Standardbreite'],
      'dhl_length' => ['typ' => 'text', 'bezeichnung' => 'Standardl&auml;nge'],

      'dhl_export_product_type' => [
        'typ' => 'select',
        'bezeichnung' => 'Export Producttyp',
        'optionen' => [
          'PRESENT' => 'Geschenke',
          'COMMERCIAL_SAMPLE' => 'Kommerzielle Probe',
          'DOCUMENT' => 'Dokumente',
          'RETURN_OF_GOODS' => 'Rücksendungen',
          'OTHER' => 'Andere',
        ]
      ],
      'dhl_export_product_type_description' => [
        'typ' => 'text',
        'bezeichnung' => 'Beschreibung im Falle von "Andere"'
      ],

      'dhl_product' => [
        'typ' => 'select',
        'bezeichnung' => 'Produkt:',
        'optionen' => [
          'V01PAK' => 'Paket national',
          'V53WPAK' => 'Paket international'
        ],
      ],
      'dhl_coding' => ['typ' => 'checkbox', 'bezeichnung' => 'Leitcodierung aktivieren'],
      'autotracking' => ['typ' => 'checkbox', 'bezeichnung' => 'Tracking übernehmen:'],
    ];
  }

  public function testCredentials($username, $password, $accountNumber){
    /** @var DhlApiFactory $dhlApiFactory */
    $dhlApiFactory = $this->app->Container->get('DhlApiFactory');

    /** @var DhlApi $dhlApi */
    $dhlApi = $dhlApiFactory->createProductionInstance(
      $username,
      $password,
      $accountNumber,
      '',
      '',
      '',
      '',
      '',
      '',
      ''
    );

    try {
      $dhlApi->validateShipment(new CreateNationalShipmentRequest(
        date("Y-m-d"),
        1.0,
        10,
        20,
        30,
        "Max muster",
        '',
        '',
        'Teststr. 1',
        '11',
        '86153',
        'Augsburg',
        'DE',
        'max.muster@xentral.com',
        false
      ));
    }catch (InvalidRequestDataException $e){
      // do nothing, test data is invalid
    }
  }

  public function PaketmarkeDrucken($id, $sid)
  {
    $adressdaten = $this->GetAdressdaten($id, $sid);
    $ret = $this->Paketmarke($sid, $id, '', false, $adressdaten);
    if($sid === 'lieferschein'){
      $deliveryNoteArr = $this->app->DB->SelectRow("SELECT adresse,projekt,versandart,auftragid FROM lieferschein WHERE id = '$id' LIMIT 1");
      $adresse = $deliveryNoteArr['adresse'];
      $projekt = $deliveryNoteArr['projekt'];
      $versandart = $deliveryNoteArr['versandart'];
      $adressvalidation = 2;
      if($ret){
        $adressvalidation = 1;
      }
      $tracking = '';
      if(isset($adressdaten['tracking'])){
        $tracking = $adressdaten['tracking'];
      }
      if(!isset($adressdaten['versandid'])){
        $adressdaten['versandid'] = $this->app->DB->Select("SELECT id FROM versand WHERE abgeschlossen = 0 AND tracking = '' AND lieferschein = '$id' LIMIT 1");
      }
      if(!isset($adressdaten['versandid'])){
        $this->app->DB->Insert("INSERT INTO versand (versandunternehmen, tracking,
              versendet_am,abgeschlossen,lieferschein,freigegeben,firma,adresse,projekt,paketmarkegedruckt,adressvalidation)
              VALUES ($versandart','$tracking',NOW(),1,'$id',1,'1','$adresse','$projekt',1,'$adressvalidation') ");
        $adressdaten['versandid'] = $this->app->DB->GetInsertID();
      }elseif($tracking){
        $this->app->DB->Update("UPDATE versand SET freigegeben = 1, abgeschlossen = 1, tracking =1, paketmarkegedruckt = 1, tracking= '$tracking',adressvalidation = '$adressvalidation', versendet_am = now() WHERE id = '" . $adressdaten['versandid'] . "' LIMIT 1");
        $this->app->DB->Update("UPDATE versand SET versandunternehmen = versandart WHERE id = '" . $adressdaten['versandid'] . "' AND versandunternehmen = '' LIMIT 1");
        $this->app->DB->Update("UPDATE versand SET versandunternehmen = '$versandart' WHERE id = '" . $adressdaten['versandid'] . "' AND versandunternehmen = '' LIMIT 1");
      }
      $auftragid = $deliveryNoteArr['auftragid'];
      if($auftragid){
        $this->app->DB->Update("UPDATE auftrag SET schreibschutz = 1, status = 'abgeschlossen' WHERE id = '$auftragid' AND status = 'freigegeben' LIMIT 1");
      }
      if($adressvalidation == 1){
        $this->app->erp->LieferscheinProtokoll($id, 'Paketmarke automatisch gedruckt');
        if($adressdaten['versandid']){
          return $adressdaten['versandid'];
        }
      }elseif($adressvalidation == 2){
        $this->app->erp->LieferscheinProtokoll($id, 'automatisches Paketmarke Drucken fehlgeschlagen');
      }
    }

    return $ret;
  }

  public function Paketmarke($doctyp, $docid, $target = '', $error = false, &$adressdaten = null)
  {
    $id = $docid;
    $sid = $doctyp;
    if($adressdaten === null){
      $drucken = $this->app->Secure->GetPOST('drucken');
      $anders = $this->app->Secure->GetPOST('anders');
      $tracking_again = $this->app->Secure->GetGET('tracking_again');
      $module = $this->app->Secure->GetPOST('module');
      if(empty($module)){
        $module = $doctyp;
      }
    }else{
      $drucken = 1;
      $anders = '';
      $tracking_again = '';
      $module = $doctyp;
    }


    /** @var DhlApiFactory $dhlApiFactory */
    $dhlApiFactory = $this->app->Container->get('DhlApiFactory');

    /** @var DhlApi $dhlApi */
    $dhlApi = $dhlApiFactory->createProductionInstance(
      $this->einstellungen['dhl_username'],
      $this->einstellungen['dhl_password'],
      $this->einstellungen['dhl_accountnumber'],
      $this->einstellungen['dhl_origin_name'],
      $this->einstellungen['dhl_origin_street'],
      $this->einstellungen['dhl_origin_houseno'],
      $this->einstellungen['dhl_origin_zip'],
      $this->einstellungen['dhl_origin_city'],
      $this->einstellungen['dhl_origin_country'],
      $this->einstellungen['dhl_origin_email']
    );

    if($drucken != '' || $tracking_again == '1'){

      if($tracking_again != "1"){
        $versandId = 0;
        if($module === 'retoure'){
          $Query = $this->app->DB->SelectRow("SELECT * FROM retoure where id='$id'");
        }elseif($module === 'versand'){
          $versandId = $id;
          $lieferschein = $this->app->DB->Select("SELECT lieferschein WHERE id = '$id' LIMIT 1");
          $Query = $this->app->DB->SelectRow("SELECT * FROM lieferschein where id='$lieferschein'");
        }else{
          $Query = $this->app->DB->SelectRow("SELECT * FROM lieferschein where id='$id'");
        }
        $projekt = $Query['projekt'];
        $Adresse = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='" . $Query['adresse'] . "'");
        $product = '';
        $Country = $Query['land'];
        if($adressdaten === null){
          $versandmit = $this->app->Secure->GetPOST("versandmit");
          $trackingsubmit = $this->app->Secure->GetPOST("trackingsubmit");
          $versandmitbutton = $this->app->Secure->GetPOST("versandmitbutton");
          $tracking = $this->app->Secure->GetPOST("tracking");
          $trackingsubmitcancel = $this->app->Secure->GetPOST("trackingsubmitcancel");
          $retourenlabel = $this->app->Secure->GetPOST("retourenlabel");

          //$Weight = $this->app->Secure->GetPOST("kg1");
          $Name = $this->app->Secure->GetPOST("name");
          $Name2 = $this->app->Secure->GetPOST("name2");
          $Name3 = $this->app->Secure->GetPOST("name3");
          $Street = $this->app->Secure->GetPOST("strasse");
          $HouseNo = $this->app->Secure->GetPOST("hausnummer");
          $ZipCode = $this->app->Secure->GetPOST("plz");
          $City = $this->app->Secure->GetPOST("ort");
          $Mail = $this->app->Secure->GetPOST("email");
          $Phone = $this->app->Secure->GetPOST("phone");
          $Country = $this->app->Secure->GetPOST("land");
          $Weight = $this->app->Secure->GetPOST('kg1');

          $height = $this->app->Secure->GetPOST('height');
          $wigth = $this->app->Secure->GetPOST('width');
          $length = $this->app->Secure->GetPOST('length');

          $coding = $this->app->Secure->GetPOST('coding') == '1';
        }else{
          $versandmit = '';//$this->app->Secure->GetPOST("versandmit");
          $trackingsubmit = '';//$this->app->Secure->GetPOST("trackingsubmit");
          $versandmitbutton = '';//$this->app->Secure->GetPOST("versandmitbutton");
          $tracking = '';//$this->app->Secure->GetPOST("tracking");
          $trackingsubmitcancel = '';//$this->app->Secure->GetPOST("trackingsubmitcancel");
          $retourenlabel = '';// $this->app->Secure->GetPOST("retourenlabel");

          $Name = $adressdaten["name"];
          $Name2 = $adressdaten["name2"];
          $Name3 = $adressdaten["name3"];
          $Street = $adressdaten["strasse"];
          $HouseNo = $adressdaten["hausnummer"];
          $ZipCode = $adressdaten['plz'];
          $City = $adressdaten['ort'];
          $Mail = $adressdaten['email'];
          $Phone = $adressdaten["telefon"];
          $Country = $adressdaten["land"];
          $Company = "Company";
          $Weight = $adressdaten["standardkg"];
          $coding = $this->einstellungen['dhl_coding'] == 1;

          $height = $this->einstellungen('dhl_height');
          $wigth = $this->einstellungen('dhl_width');
          $length = $this->einstellungen('dhl_length');

        }

        try {
          $shipmentDate = date("Y-m-d");

          switch ($this->einstellungen['dhl_product']) {
            case 'V01PAK':
            {
              $shipmentData = new CreateNationalShipmentRequest(
                $shipmentDate,
                $Weight,
                $length,
                $wigth,
                $height,
                $Name,
                $Name2,
                $Name3,
                $Street,
                $HouseNo,
                $ZipCode,
                $City,
                $Country,
                $Mail,
                $coding
              );
              break;
            }
            case 'V53WPAK':
            {
              $shipmentData = new CreateInterationalShipmentRequest(
                $shipmentDate,
                $Weight,
                $length,
                $wigth,
                $height,
                $Name,
                $Name2,
                $Name3,
                $Street,
                $HouseNo,
                $ZipCode,
                $City,
                $Country,
                $Mail,
                $coding,
                $this->einstellungen['dhl_export_product_type'],
                $this->einstellungen['dhl_export_product_type_description'],
                $this->getPackageContents($Query['id'])
              );
              break;
            }
            default:
            {
              throw new UnknownProductException();
            }
          }

          $createResponse = $dhlApi->createShipment($shipmentData);

          if($this->einstellungen['autotracking'] == "1")
            $this->SetTracking($createResponse->getShipmentNumber(), $sid === 'versand' ? $id : 0, $lieferschein);


          $data['drucker'] = $this->paketmarke_drucker;
          $data['druckerlogistikstufe2'] = $this->export_drucker;

          if(!$data['drucker']){
            if($this->app->erp->GetStandardPaketmarkendrucker() > 0){
              $data['drucker'] = $this->app->erp->GetStandardPaketmarkendrucker();
            }
          }

          if(!$data['druckerlogistikstufe2']){
            if($this->app->erp->GetStandardVersanddrucker($projekt) > 0){
              $data['druckerlogistikstufe2'] = $this->app->erp->GetStandardVersanddrucker($projekt);
            }
          }
          $pdf = $createResponse->getLabelAsPdf();
          $datei = $this->app->erp->GetTMP() . 'DhlLabel_' . $createResponse->getShipmentNumber() . '.pdf';

          file_put_contents($datei, $pdf);

          $spoolerId = $this->app->printer->Drucken($data['drucker'], $datei);
          if($spoolerId > 0 && $versandId > 0){
            $this->app->DB->Update(
              sprintf(
                'UPDATE versand SET lastspooler_id = %d, lastprinter = %d WHERE id = %d',
                $spoolerId, $data['drucker'], $versandId
              )
            );
          }
          if($module === 'retoure'){
            if(@is_file($datei) && @filesize($datei)){
              $fileid = $this->app->erp->CreateDatei('DhlMarkeLabel_' . $this->app->DB->Select("SELECT belegnr FROM retoure WHERE id = '$id' LIMIT 1") . '.pdf',
                'Anhang', '', "", $datei,
                $this->app->DB->real_escape_string($this->app->User->GetName()));
              $this->app->erp->AddDateiStichwort($fileid, 'anhang', 'retoure', $id);
            }
          }

          unlink($datei);
          if($adressdaten !== null){
            return true;
          }

          if($createResponse->containsExportDocuments()){
            $tmppdf = $this->app->erp->GetTMP() . 'DhlExport_' . $createResponse->getShipmentNumber() . '.pdf';
            file_put_contents($tmppdf, $createResponse->getExportPaperAsPdf());
            $spoolerId = $this->app->printer->Drucken($data['druckerlogistikstufe2'], $tmppdf);
            if($versandId && $spoolerId){
              $this->app->DB->Update(
                sprintf(
                  'UPDATE versand SET lastexportspooler_id = %d, lastexportprinter = %d WHERE id = %d',
                  $spoolerId, $data['druckerlogistikstufe2'], $versandId
                )
              );
            }
            if($module === 'retoure'){
              if(@is_file($tmppdf) && @filesize($tmppdf)){
                $fileid = $this->app->erp->CreateDatei('Export_' . $this->app->DB->Select("SELECT belegnr FROM retoure WHERE id = '$id' LIMIT 1") . '.pdf', 'Anhang', '', "", $tmppdf, $this->app->DB->real_escape_string($this->app->User->GetName()));
                $this->app->erp->AddDateiStichwort($fileid, 'anhang', 'retoure', $id);
              }
            }

            unlink($tmppdf);
          }


        } catch (DhlBaseException $e) {
          $this->errors[] = $e->getMessage();
        }
      }
    }
    if($adressdaten !== null){
      return false;
    }
    if($target){
      if($this->einstellungen['dhl_coding'] == '1'){
        $this->app->Tpl->Set('DHL_CODING_CHECKED', 'checked="checked"');
      }

      $this->app->Tpl->Add("HEIGHT", $this->einstellungen['dhl_height']);
      $this->app->Tpl->Add("WIDTH", $this->einstellungen['dhl_width']);
      $this->app->Tpl->Add("LENGTH", $this->einstellungen['dhl_length']);
      $this->app->Tpl->Parse($target, 'versandarten_dhl.tpl');
    }
    if(count($this->errors) > 0){
      return $this->errors;
    }
  }

  private function getPackageContents($deliveryNoteId)
  {
    $contents = [];
    /** @var Database $db */
    $db = $this->app->Container->get('Database');

    $select = $db->select()
      ->from('lieferschein_position AS l')
      ->cols([
        'l.bezeichnung',
        'l.menge',
        'l.zolltarifnummer',
        'l.herkunftsland',
        'a.umsatz_netto_einzeln',
        'g.gewicht'
      ])
      ->leftJoin('auftrag_position AS a', 'l.auftrag_position_id = a.id')
      ->leftJoin('artikel AS g', 'l.artikel = g.id')
      ->where('l.lieferschein=:id')
      ->bindValue('id', $deliveryNoteId);

    $positions = $db->fetchAll($select->getStatement(), $select->getBindValues());

    foreach ($positions as $position) {
      $contents[] = new PackageContent(
        (int)$position['menge'],
        $position['bezeichnung'],
        $position['umsatz_netto_einzeln'],
        $position['herkunftsland'],
        $position['zolltarifnummer'],
        $position['gewicht']
      );
    }

    return $contents;
  }

  public function Export($daten)
  {

  }


  private function log($message)
  {
    if(isset($this->einstellungen['log'])){
      if(is_array($message) || is_object($message)){
        error_log(print_r($message, true));
      }else{
        error_log($message);
      }
    }
  }

}
