<?php
require_once dirname(__DIR__) . '/class.versanddienstleister.php';

class Versandart_internetmarke extends Versanddienstleister
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

        $this->ppl = 'ppl_480.csv';

        $this->app->erp->LogFile("Internetmarke start", "Product version: {$this->ppl}");
        $einstellungenArr = $this->app->DB->SelectRow("SELECT einstellungen_json,paketmarke_drucker,export_drucker FROM versandarten WHERE id = '$id' LIMIT 1");
        $einstellungen_json = $einstellungenArr['einstellungen_json'];
        $this->paketmarke_drucker = $einstellungenArr['paketmarke_drucker'];
        $this->export_drucker = $einstellungenArr['export_drucker'];

        $this->name = 'Internetmarke';
        if ($einstellungen_json) {
            $this->einstellungen = json_decode($einstellungen_json, true);
        } else {
            $this->einstellungen = [];
        }

        $this->einstellungen['api_accountnumber'] = $this->einstellungen['accountnumber'];
        $this->credentials = $this->einstellungen;
        $this->errors = [];
        $data = $this->einstellungen;
        $this->info = $this->einstellungen;

    }

    function ShowUserdata()
    {
        if (isset($this->app->Conf->WFuserdata)) {
            return 'Userdata-Ordner: ' . $this->app->Conf->WFuserdata;
        }
    }

    public function Einstellungen($target = 'return')
    {
        if ($this->app->Secure->GetPOST('testen')) {
            $parameter1 = $this->einstellungen['pfad'];
            if ($parameter1) {
                if (is_dir($parameter1)) {
                    if (substr($parameter1, -1) !== '/') {
                        $parameter1 .= '/';
                    }

                    if (file_put_contents($parameter1 . 'wawision_test.txt', 'TEST')) {
                        $this->app->Tpl->Add('MESSAGE',
                            '<div class="info">Datei ' . $parameter1 . 'wawision_test.txt' . ' wurde erstellt!</div>');
                    } else {
                        $this->app->Tpl->Add('MESSAGE', '<div class="error">Datei konnte nicht angelegt werden!</div>');
                    }
                } else {
                    $this->app->Tpl->Add('MESSAGE',
                        '<div class="error">Speicherort existiert nicht oder ist nicht erreichbar!</div>');
                }
            } else {
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
        return 'Internetmarke';
    }

    function EinstellungenStruktur()
    {
        // include __DIR__ . '/../../../cronjobs/internetmarke.php';
        // $this->app->DB->Update('UPDATE prozessstarter AS p SET p.letzteausfuerhung="1970-01-01 00:00:00" WHERE p.parameter="internetmarke";');
        return [
            'dhl_marke_email'    => ['typ' => 'text', 'bezeichnung' => 'Portokasse:'],
            'dhl_marke_password' => ['typ' => 'text', 'bezeichnung' => 'Passwort:'],
            'origin_company'     => ['typ' => 'text', 'bezeichnung' => 'Versender Firma:'],
            //        'origin_surname' => array('typ'=>'text','bezeichnung'=>'Versender Vorname:'),
            //        'origin_lastname' => array('typ'=>'text','bezeichnung'=>'Versender Nachname:'),
            'origin_street'      => ['typ' => 'text', 'bezeichnung' => 'Versender Strasse:'],
            'origin_houseno'     => ['typ' => 'text', 'bezeichnung' => 'Versender Hausnummer:'],
            'origin_city'        => ['typ' => 'text', 'bezeichnung' => 'Versender Ort:'],
            'origin_zip'         => ['typ' => 'text', 'bezeichnung' => 'Versender PLZ:'],
            'origin_country'     => ['typ' => 'text', 'bezeichnung' => 'Versender Land (2-stellig):'],
            'product'            => [
                'typ'         => 'select',
                'bezeichnung' => 'Produkt:',
                'optionen'    => $this->getProductList(),
            ],
            'format'             => [
                'typ'         => 'select',
                'bezeichnung' => 'Format:',
                'optionen'    => $this->pageFormatList(),
            ],
            'autotracking'       => ['typ' => 'checkbox', 'bezeichnung' => 'Tracking übernehmen:'],
        ];

    }

    public function PaketmarkeDrucken($id, $sid)
    {
        $adressdaten = $this->GetAdressdaten($id, $sid);
        $ret = $this->Paketmarke($sid, $id, '', false, $adressdaten);
        if ($sid === 'lieferschein') {
            $deliveryNoteArr = $this->app->DB->SelectRow("SELECT adresse,projekt,versandert,auftragid FROM lieferschein WHERE id = '$id' LIMIT 1");
            $adresse = $deliveryNoteArr['adresse'];
            $projekt = $deliveryNoteArr['projekt'];
            $versandart = $deliveryNoteArr['versandart'];
            $adressvalidation = 2;
            if ($ret) {
                $adressvalidation = 1;
            }
            $tracking = '';
            if (isset($adressdaten['tracking'])) {
                $tracking = $adressdaten['tracking'];
            }
            if (!isset($adressdaten['versandid'])) {
                $adressdaten['versandid'] = $this->app->DB->Select("SELECT id FROM versand WHERE abgeschlossen = 0 AND tracking = '' AND lieferschein = '$id' LIMIT 1");
            }
            if (!isset($adressdaten['versandid'])) {
                $this->app->DB->Insert("INSERT INTO versand (versandunternehmen, tracking,
              versendet_am,abgeschlossen,lieferschein,freigegeben,firma,adresse,projekt,paketmarkegedruckt,adressvalidation)
              VALUES ($versandart','$tracking',NOW(),1,'$id',1,'1','$adresse','$projekt',1,'$adressvalidation') ");
                $adressdaten['versandid'] = $this->app->DB->GetInsertID();
            } elseif ($tracking) {
                $this->app->DB->Update("UPDATE versand SET freigegeben = 1, abgeschlossen = 1, tracking =1, paketmarkegedruckt = 1, tracking= '$tracking',adressvalidation = '$adressvalidation', versendet_am = now() WHERE id = '" . $adressdaten['versandid'] . "' LIMIT 1");
                $this->app->DB->Update("UPDATE versand SET versandunternehmen = versandart WHERE id = '" . $adressdaten['versandid'] . "' AND versandunternehmen = '' LIMIT 1");
                $this->app->DB->Update("UPDATE versand SET versandunternehmen = '$versandart' WHERE id = '" . $adressdaten['versandid'] . "' AND versandunternehmen = '' LIMIT 1");
            }
            $auftragid = $deliveryNoteArr['auftragid'];
            if ($auftragid) {
                $this->app->DB->Update("UPDATE auftrag SET schreibschutz = 1, status = 'abgeschlossen' WHERE id = '$auftragid' AND status = 'freigegeben' LIMIT 1");
            }
            if ($adressvalidation == 1) {
                $this->app->erp->LieferscheinProtokoll($id, 'Paketmarke automatisch gedruckt');
                if ($adressdaten['versandid']) {
                    return $adressdaten['versandid'];
                }
            } elseif ($adressvalidation == 2) {
                $this->app->erp->LieferscheinProtokoll($id, 'automatisches Paketmarke Drucken fehlgeschlagen');
            }
        }

        return $ret;
    }

    public function Paketmarke($doctyp, $docid, $target = '', $error = false, &$adressdaten = null)
    {
        $id = $docid;
        $sid = $doctyp;
        if ($adressdaten === null) {
            $drucken = $this->app->Secure->GetPOST('drucken');
            $anders = $this->app->Secure->GetPOST('anders');
            $tracking_again = $this->app->Secure->GetGET('tracking_again');
            $module = $this->app->Secure->GetPOST('module');
            if(empty($module)) {
                $module = $doctyp;
            }
        } else {
            $drucken = 1;
            $anders = '';
            $tracking_again = '';
            $module = $doctyp;
        }

        if ($drucken != '' || $tracking_again == '1') {

            if ($tracking_again != "1") {
                $versandId = 0;
                if ($module === 'retoure') {
                    $Query = $this->app->DB->SelectRow("SELECT * FROM retoure where id='$id'");
                }
                elseif ($module === 'versand') {
                    $versandId = $id;
                    $lieferschein = $this->app->DB->Select("SELECT lieferschein WHERE id = '$id' LIMIT 1");
                    $Query = $this->app->DB->SelectRow("SELECT * FROM lieferschein where id='$lieferschein'");
                }
                else {
                    $Query = $this->app->DB->SelectRow("SELECT * FROM lieferschein where id='$id'");
                }
                $projekt = $Query['projekt'];
                $Adresse = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='" . $Query['adresse'] . "'");
                $product = '';
                $Country = $Query['land'];
                if ($adressdaten === null) {
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
                    $Company = "Company";


                    $product = $this->app->Secure->GetPOST("product");
                } else {
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
                }

                if (!$product) {
                    $product = $this->einstellungen['product'];
                }

                $ei = $this->einstellungen;

                if (!isset($this->einstellungen['format']) || $this->einstellungen['format'] == -1) {
                    $this->errors[] = 'Bitte Format einstellen';
                    $link = 'fail';
                } else {
                    //$this->TIMESTAMP = "17072017-155000";
                    $token = $this->getToken();
                    if ($token !== 'fail') {
                        if($Name2 !== ''){
                            $Name3 = $Name2;
                        }
                        $link = $this->checkoutPDF($token, $Name, $Name3, $Street, $HouseNo, $ZipCode, $City, $Country,
                            $this->einstellungen, $product, $sid, $lieferschein, $id);
                        if ($adressdaten !== null) {
                            $adressdaten['tracking'] = $this->voucherId;
                        }
                    }
                }

                if ($token !== 'fail' && $link !== 'fail') {
                    $data['drucker'] = $this->paketmarke_drucker;
                    $data['druckerlogistikstufe2'] = $this->export_drucker;

                    if (!$data['drucker']) {
                        if ($this->app->erp->GetStandardPaketmarkendrucker() > 0) {
                            $data['drucker'] = $this->app->erp->GetStandardPaketmarkendrucker();
                        }
                    }

                    if (!$data['druckerlogistikstufe2']) {
                        if ($this->app->erp->GetStandardVersanddrucker($projekt) > 0) {
                            $data['druckerlogistikstufe2'] = $this->app->erp->GetStandardVersanddrucker($projekt);
                        }
                    }
                    $pdf = file_get_contents($link);
                    $datei = $this->app->erp->GetTMP() . 'DhlMarkeLabel' . ".pdf";

                    file_put_contents($datei, $pdf);

                    $spoolerId = $this->app->printer->Drucken($data['drucker'], $datei);
                    if($spoolerId > 0 && $versandId > 0) {
                      $this->app->DB->Update(
                        sprintf(
                          'UPDATE versand SET lastspooler_id = %d, lastprinter = %d WHERE id = %d',
                          $spoolerId, $data['drucker'], $versandId
                        )
                      );
                    }
                    if ($module === 'retoure') {
                        if (@is_file($datei) && @filesize($datei)) {
                            $fileid = $this->app->erp->CreateDatei('DhlMarkeLabel_' . $this->app->DB->Select("SELECT belegnr FROM retoure WHERE id = '$id' LIMIT 1") . '.pdf',
                                'Anhang', '', "", $datei,
                                $this->app->DB->real_escape_string($this->app->User->GetName()));
                            $this->app->erp->AddDateiStichwort($fileid, 'anhang', 'retoure', $id);
                        }
                    }

                    unlink($datei);
                    if ($adressdaten !== null) {
                        return true;
                    }
                }
            }
        }
        if ($adressdaten !== null) {
            return false;
        }
        if ($target) {
            $formats = $this->retrievePageFormats();
            $pageFormats = new SimpleXMLElement($formats);
            $pageFormats->registerXPathNamespace("a", "http://oneclickforapp.dpag.de/V3");
            $formats = $pageFormats->xpath('//a:pageFormat');
            $formatOptions = "";
            foreach ($formats as $format) {
                $formatOptions .= "<option value=\"{$format->id}\">{$format->name}</option>";
            }
            $this->app->Tpl->Set('PAGE_FORMATS', $formatOptions);
            if (!isset($this->einstellungen['product']) || $this->einstellungen['product'] == -1) {
                $this->getProducts();
            } else {
                $htmlString = '<tr><td><select name="product" style="visibility: hidden"><option value="' . $this->einstellungen['product'] . '">Test</option></select></td></tr>';
                $this->app->Tpl->Set('PRODUCT_LIST', $htmlString);
            }
            if (!isset($this->einstellungen["format"]) || $this->einstellungen["format"] == -1) {
                $this->errors[] = 'Bitte Format einstellen';
            }
            $Query = $this->app->DB->SelectRow("SELECT * FROM lieferschein where id='$id'");
            $this->app->Tpl->Set("ADRESSZUSATZ", $Query['adresszusatz']);
            $this->app->Tpl->Parse($target, 'versandarten_internetmarke.tpl');
        }
        if (count($this->errors) > 0) {
            return $this->errors;
        }
    }

    function pageFormatList()
    {
        $formats = $this->retrievePageFormats();
        $pageFormats = new SimpleXMLElement($formats);
        $pageFormats->registerXPathNamespace("a", "http://oneclickforapp.dpag.de/V3");
        $formats = $pageFormats->xpath('//a:pageFormat');
        //var_dump($formats);
        $formats1 = [-1 => "Bitte auswählen"];
        foreach ($formats as $format) {
            $formats1[(int)$format->id] = $format->name;
            if (!filter_var($format->isAddressPossible, FILTER_VALIDATE_BOOLEAN)) {
                $formats1[(int)$format->id] .= ' (Keine Adressanzeige vorgesehen)';
            }
        }

        return $formats1;
    }

    function getProductList()
    {
        $csvFile = fopen(__DIR__ . "/{$this->ppl}", "r");
        if (!$csvFile) {
            $this->app->erp->Logfile("Internetmarke: Kann Produktdaten nicht finden.");
            $this->errors[] = "Kann Produktdaten nicht finden.";

            //throw new Exception("Error openinig product csv");
            return 'fail';
        }
        $options = [];
        $options[-1] = "Alle zur Auswahl in Paketmarkendialog";
        $line = fgetcsv($csvFile, 0, ";");
        $htmlString = "";
        while ($line != null) {
            if ($line[4] != "") {
                //$htmlString .= '<option value="' . $line[2] . '"><div title="test">' . mb_convert_encoding($line[4], "UTF-8", "ASCII") . " (" . money_format('%!n', (float)str_replace(",", ".", $line[5])) . "€)</div></option>";
                $options[(string)$line[2]] = mb_convert_encoding($line[4], "UTF-8",
                        "ASCII") . " (" . money_format('%!n', (float)str_replace(",", ".", $line[5])) . ")";
            }
            $line = fgetcsv($csvFile, 0, ";");
        }
        fclose($csvFile);

        return $options;
    }

    function getProducts($return = false)
    {
        $csvFile = fopen(__DIR__ . "/{$this->ppl}", "r");
        if (!$csvFile) {
            $this->app->erp->Logfile("Internetmarke: Kann Produktdaten nicht finden.");
            $this->errors[] = "Kann Produktdaten nicht finden.";

            //throw new Exception("Error openinig product csv");
            return 'fail';
        }
        $line = fgetcsv($csvFile, 0, ";");
        $htmlString = "";
        while ($line != null) {
            if ($line[4] != "") {
                $htmlString .= '<option value="' . $line[2] . '"><div title="test">' . mb_convert_encoding($line[4],
                        "UTF-8", "ASCII") . " (" . money_format('%!n',
                        (float)str_replace(",", ".", $line[5])) . "€)</div></option>";
                if ($return) {
                    return $line[2];
                }
            }
            $line = fgetcsv($csvFile, 0, ";");
        }
        $this->app->Tpl->Set('PRODUCT_LIST',
            '<tr><td>Produkt: </td><td><select name="product">' . $htmlString . '</select></td></tr>');
        fclose($csvFile);
    }


  /**
   * @param string $token
   *
   * @return string
   */
    public function getProductPricesRequestXml($token)
    {
      return '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v3="http://oneclickforapp.dpag.de/V3">
  <soapenv:Header>' . $this->getHeader() . '</soapenv:Header>
  <soapenv:Body>
    <v3:RetrieveContractProductsRequest>
      <v3:userToken>'.$token.'</v3:userToken>
    </v3:RetrieveContractProductsRequest>
  </soapenv:Body>
</soapenv:Envelope>';
    }

    function getProductPrice($id)
    {
        $price = 0.00;
        $csvFile = fopen(__DIR__ . "/{$this->ppl}", "r");
        if (!$csvFile) {
            $this->app->erp->Logfile("Internetmarke: Kann Produktdaten nicht öffnen");
            $this->errors[] = "Kann Produktdaten nicht finden.";

            return 'fail';
            //throw new Exception("Error openinig product csv");
        }
        $line = fgetcsv($csvFile, 0, ";");
        $htmlString = "";
        while ($line != null) {
            if ($line[4] != "") {
                if ($line[2] == $id) {
                    $price = (float)str_replace(",", ".", $line[5]);
                    break;
                }
            }
            $line = fgetcsv($csvFile, 0, ";");
        }

        fclose($csvFile);

        return $price;
    }


    public function Export($daten)
    {

    }


    private function log($message)
    {
        if (isset($this->einstellungen['log'])) {
            if (is_array($message) || is_object($message)) {
                error_log(print_r($message, true));
            } else {
                error_log($message);
            }
        }
    }


    function checkoutPDF($token, $name, $additional, $street, $houseno, $plz, $city, $country, $ei, $product, $sid, $lieferschein, $id)
    {
        if(!is_file(__DIR__.'/ppl_prices_'.$this->id.'.xml')){
          $xml = $this->getProductPricesRequestXml($token);
          $curlData = $this->curl($xml);
          file_put_contents(__DIR__ . '/ppl_prices_' . $this->id . '.xml', $curlData);
        }
        $prices = [];
        try {
          $curlData = file_get_contents(__DIR__ . '/ppl_prices_' . $this->id . '.xml');
          $doc = new DOMDocument();
          $doc->loadXML($curlData);

          $children = $doc->getElementsByTagName('products');
          for ($i = 0; $i < $children->length; $i++) {
            $child[$i] = $children->item($i);
            if($child[$i]->hasChildNodes()){
              $haschild[$i] = $child[$i]->childNodes;
              if($haschild[$i]->length >= 2){
                $price = '';
                $code = '';
                for ($j = 0; $j < $haschild[$i]->length; $j++) {
                  $child1 = $haschild[$i]->item($j);
                  if($child1->nodeName === 'productCode'){
                    $code = $child1->nodeValue;
                  }
                  if($child1->nodeName === 'price'){
                    $price = $child1->nodeValue;
                  }
                }
                if(!empty($code) && !empty($price)){
                  $prices[$code] = $price / 100;
                }
              }
            }
          }
        }
        catch (Exception $e) {
          
        }
        $price = $this->getProductPrice($product);
        if(!empty($prices[$product])) {
          $price = $prices[$product];
        }
        $c = $this->app->erp->GetSelectLaenderliste();

        // anscheinend geben die manchmal komma an
        if ((strpos($name, ',') !== false) && !(strpos($name, ' ') !== false)) {
            $name = str_replace(',', ' ', $name);
        }

        if (strpos($name, ' ') !== false) {
            $firstname = trim(strstr($name, ' ', true));
            $lastname = trim(strstr($name, ' '));
        } else {
            $firstname = $name;
            $lastname = "";
        }


        if ($ei["origin_company"] == "") {
            $ei["origin_company"] = $ei["origin_firstname"] . " " . $ei["origin_lastname"];
        }


        $country = $this->getAlpha3FromAlpa2($country);
        $data = '<?xml version="1.0" encoding="UTF-8"?>
      <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v3="http://oneclickforapp.dpag.de/V3">
      <soapenv:Header>' . $this->getHeader() . '</soapenv:Header>
      <soapenv:Body>
      <v3:CheckoutShoppingCartPDFRequest>
      <v3:userToken>' . $token . '</v3:userToken>
      <v3:pageFormatId>' . $this->einstellungen["format"] . '</v3:pageFormatId>
      <!-- <v3:ppl>33</v3:ppl> -->
      <v3:positions>
      <v3:productCode>' . (int)$product . '</v3:productCode>
      <!-- <v3:imageID>79929186</v3:imageID> -->
      <v3:address>
      <v3:sender>
      <v3:name>
      <v3:companyName>
      <v3:company>' . $ei["origin_company"] . '</v3:company>
      </v3:companyName>
      </v3:name>
      <v3:address>
      <v3:street>' . $ei["origin_street"] . '</v3:street>
      <v3:houseNo>' . $ei["origin_houseno"] . '</v3:houseNo>
      <v3:zip>' . $ei["origin_zip"] . '</v3:zip>
      <v3:city>' . $ei["origin_city"] . '</v3:city>
      <v3:country>' . $ei["origin_country"] . '</v3:country>
      </v3:address>              
      </v3:sender>
      <v3:receiver> 
      <v3:name>
      <v3:personName>
      <v3:firstname>' . $firstname . '</v3:firstname> 
      <v3:lastname>' . $lastname . '</v3:lastname> 
      </v3:personName>
      </v3:name>
      <v3:address>
      <v3:additional>' . $additional . '</v3:additional>
      <v3:street>' . $street . '</v3:street>
      <v3:houseNo>' . $houseno . '</v3:houseNo>
      <v3:zip>' . $plz . '</v3:zip>
      <v3:city>' . $city . '</v3:city>
      <v3:country>' . $country . '</v3:country>
      </v3:address>              
      </v3:receiver>
      </v3:address>
      <v3:voucherLayout>AddressZone</v3:voucherLayout>
      <v3:position>
      <v3:labelX>1</v3:labelX>
      <v3:labelY>1</v3:labelY>
      <v3:page>1</v3:page>
      </v3:position>
      </v3:positions>
      <v3:total>' . ($price * 100) . '</v3:total>
      <v3:createManifest>false</v3:createManifest>
      <v3:createShippingList>2</v3:createShippingList>
      </v3:CheckoutShoppingCartPDFRequest>
      </soapenv:Body>
      </soapenv:Envelope>';

        $this->app->erp->Logfile("Internetmarke checkout data", base64_encode($data));


        $data = str_replace('&amp;', '&', $data);
        $data = str_replace('&', '&amp;', $data);

        $curlData = $this->curl($data);
        $xml = new SimpleXMLElement($curlData);
        $xml->registerXPathNamespace("a", "http://oneclickforapp.dpag.de/V3");
        $errorArray = $xml->xpath("//faultstring");
        if (sizeof($errorArray) > 0) {
            $errorString = "";
            foreach ($errorArray as $error) {
                $errorString .= (string)$error . "\n";
            }
            //echo $curlData;
            $this->app->erp->Logfile("Internetmarke Checkout Error", $errorString);
            print("<!-- $curlData -->");
            $this->errors[] = $errorString;

            return 'fail';
            //throw new Exception($errorString);
        }
        $link = $xml->xpath("//a:link");
        $link = ((string)$link[0][0]);

        $countrycheck = str_replace('DEU','DE',$country);
        $voucherID = (string)$xml->xpath("//a:voucherId")[0];
        if ($countrycheck !== 'DE') {
            $voucherID = (string)$xml->xpath('//a:trackId')[0];
        }
        $this->voucherId = $voucherID;
        if ($this->einstellungen['autotracking'] == "1") {
          $this->SetTracking($voucherID,$sid==='versand'?$id:0, $lieferschein);
        }

        return $link;
    }

    function retrievePublicGallery()
    {
        $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
      xmlns:v3="http://oneclickforapp.dpag.de/V3">
      <soapenv:Header>' . $this->getHeader() . '</soapenv:Header>
      <soapenv:Body>
      <v3:RetrievePublicGalleryRequest/>
      </soapenv:Body>
      </soapenv:Envelope>';

        return format(curl($data));
    }


    function retrievePageFormats()
    {
        $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
      xmlns:v3="http://oneclickforapp.dpag.de/V3">
      <soapenv:Header>' . $this->getHeader() . '</soapenv:Header>
      <soapenv:Body>
      <v3:RetrievePageFormatsRequest/>
      </soapenv:Body>
      </soapenv:Envelope>';

        return $this->curl($data);
    }

    function format($xml)
    {
        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);
        $dom->formatOutput = true;

        return $dom->saveXml();
    }

    function getPageFormats()
    {
        $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
      xmlns:v3="http://oneclickforapp.dpag.de/V3">
      <soapenv:Header>' . $this->getHeader() . '</soapenv:Header>
      <soapenv:Body>
      <v3:RetrievePageFormatsRequest/>
      </soapenv:Body>
      </soapenv:Envelope>';

        return curl($data);
    }

    function getVoucher()
    {
        $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
      xmlns:onec="http://oneclickforapp.dpag.de/V3" xmlns:v3="http://oneclickforapp.dpag.de/V3">
      <soapenv:Header>' . $this->getHeader() . '</soapenv:Header>
      <soapenv:Body>
      <v3:RetrievePreviewVoucherPDFRequest>
      <v3:productCode>1</v3:productCode>
      <v3:imageID>79929186</v3:imageID>
      <v3:voucherLayout>AddressZone</v3:voucherLayout>
      <v3:pageFormatId>7</v3:pageFormatId>
      </v3:RetrievePreviewVoucherPDFRequest>
      </soapenv:Body>
      </soapenv:Envelope>';

        return curl($data);
    }

    function getShopOrderID($token)
    {
        $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
      xmlns:v3="http://oneclickforapp.dpag.de/V3">
      <soapenv:Header>' . $this->getHeader() . '</soapenv:Header>
      <soapenv:Body>
      <v3:CreateShopOrderIdRequest>
      <v3:userToken>' . $token . '</v3:userToken>
      </v3:CreateShopOrderIdRequest>
      </soapenv:Body>
      </soapenv:Envelope>';

        $curlData = curl($data);

        $xml = new SimpleXMLElement($curlData);
        $xml->registerXPathNamespace('a', 'http://oneclickforapp.dpag.de/V3');
        $id = $xml->xpath("//a:shopOrderId");
        $id = (string)$id[0][0];

        return $id;
    }

    function curl($data)
    {
        $curl = curl_init();
        $endpoint_URL = "https://internetmarke.deutschepost.de/OneClickForAppV3";
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $endpoint_URL);
        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            ["Content-Type: text/xml; charset=UTF-8", "SOAPAction: \"/soap/action/query\"", "Content-length: " . strlen($data)]);
        $curlData = curl_exec($curl);

        return $curlData;
    }

    function getToken()
    {
        $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v3="http://oneclickforapp.dpag.de/V3">
      <soapenv:Header>' . $this->getHeader() . '</soapenv:Header>
      <soapenv:Body>
      <v3:AuthenticateUserRequest>
      <v3:username>' . $this->einstellungen['dhl_marke_email'] . '</v3:username>
      <v3:password>' . $this->einstellungen['dhl_marke_password'] . '</v3:password>
      </v3:AuthenticateUserRequest>
      </soapenv:Body>
      </soapenv:Envelope>';

        //echo $data . "\n";

        $curl = curl_init();

        $endpoint_URL = "https://internetmarke.deutschepost.de/OneClickForAppV3";
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $endpoint_URL);
        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            ["Content-Type: text/xml", "SOAPAction: \"/soap/action/query\"", "Content-length: " . strlen($data)]);
        $curlData = curl_exec($curl);

        //echo format($curlData);

        $simpleXml = new SimpleXMLElement($curlData);
        $simpleXml->registerXPathNamespace('a', "http://oneclickforapp.dpag.de/V3");
        $simpleXml->registerXPathNamespace('ns2', "http://schemas.xmlsoap.org/soap/envelope/");
        $errorArray = $simpleXml->xpath("//faultstring");
        if (sizeof($errorArray) > 0) {
            $errorString = "";
            foreach ($errorArray as $error) {
                $errorString .= (string)$error . "\n";
            }
            $this->app->erp->Logfile("Internetmarke Token Error", $errorString);
            $this->errors[] = $errorString;

            return 'fail';
            //throw new Exception($errorString);
        }
        $user = $simpleXml->xpath("//a:userToken");
        $token = (string)$user[0][0];

        return $token;

    }

    function getHeader()
    {
        $PARTNER_ID = "AEHWA";
        $KEY_PHASE = "1";
        $KEY = "nHHNZblHkRWpQeXq1c9nnRVsp8M8rAnC";
        $TIMESTAMP = date('dmY-His');
        $hashString = "$PARTNER_ID::" . $TIMESTAMP . "::$KEY_PHASE::$KEY";
        $hash = md5($hashString);
        $hash = substr($hash, 0, 8);

        $data = '<v3:PARTNER_ID>' . $PARTNER_ID . '</v3:PARTNER_ID>
      <v3:REQUEST_TIMESTAMP>' . $TIMESTAMP . '</v3:REQUEST_TIMESTAMP>
      <v3:KEY_PHASE>' . $KEY_PHASE . '</v3:KEY_PHASE>
      <v3:PARTNER_SIGNATURE>' . $hash . '</v3:PARTNER_SIGNATURE>';

        return $data;
    }

    function getAlpha3FromAlpa2($alpha2)
    {
        $countries = [
            "AF" => "AFG",
            "AL" => "ALB",
            "DZ" => "DZA",
            "AS" => "ASM",
            "AD" => "AND",
            "AO" => "AGO",
            "AI" => "AIA",
            "AQ" => "ATA",
            "AG" => "ATG",
            "AR" => "ARG",
            "AM" => "ARM",
            "AW" => "ABW",
            "AU" => "AUS",
            "AT" => "AUT",
            "AZ" => "AZE",
            "BS" => "BHS",
            "BH" => "BHR",
            "BD" => "BGD",
            "BB" => "BRB",
            "BY" => "BLR",
            "BE" => "BEL",
            "BZ" => "BLZ",
            "BJ" => "BEN",
            "BM" => "BMU",
            "BT" => "BTN",
            "BO" => "BOL",
            "BA" => "BIH",
            "BW" => "BWA",
            "BV" => "BVT",
            "BR" => "BRA",
            "IO" => "IOT",
            "BN" => "BRN",
            "BG" => "BGR",
            "BF" => "BFA",
            "BI" => "BDI",
            "KH" => "KHM",
            "CM" => "CMR",
            "CA" => "CAN",
            "CV" => "CPV",
            "KY" => "CYM",
            "CF" => "CAF",
            "TD" => "TCD",
            "CL" => "CHL",
            "CN" => "CHN",
            "CX" => "CXR",
            "CC" => "CCK",
            "CO" => "COL",
            "KM" => "COM",
            "CG" => "COG",
            "CD" => "COD",
            "CK" => "COK",
            "CR" => "CRI",
            "CI" => "CIV",
            "HR" => "HRV",
            "CU" => "CUB",
            "CY" => "CYP",
            "CZ" => "CZE",
            "DK" => "DNK",
            "DJ" => "DJI",
            "DM" => "DMA",
            "DO" => "DOM",
            "EC" => "ECU",
            "EG" => "EGY",
            "SV" => "SLV",
            "GQ" => "GNQ",
            "ER" => "ERI",
            "EE" => "EST",
            "ET" => "ETH",
            "FK" => "FLK",
            "FO" => "FRO",
            "FJ" => "FJI",
            "FI" => "FIN",
            "FR" => "FRA",
            "GF" => "GUF",
            "PF" => "PYF",
            "TF" => "ATF",
            "GA" => "GAB",
            "GM" => "GMB",
            "GE" => "GEO",
            "DE" => "DEU",
            "GH" => "GHA",
            "GI" => "GIB",
            "GR" => "GRC",
            "GL" => "GRL",
            "GD" => "GRD",
            "GP" => "GLP",
            "GU" => "GUM",
            "GT" => "GTM",
            "GG" => "GGY",
            "GN" => "GIN",
            "GW" => "GNB",
            "GY" => "GUY",
            "HT" => "HTI",
            "HM" => "HMD",
            "VA" => "VAT",
            "HN" => "HND",
            "HK" => "HKG",
            "HU" => "HUN",
            "IS" => "ISL",
            "IN" => "IND",
            "ID" => "IDN",
            "IR" => "IRN",
            "IQ" => "IRQ",
            "IE" => "IRL",
            "IM" => "IMN",
            "IL" => "ISR",
            "IT" => "ITA",
            "JM" => "JAM",
            "JP" => "JPN",
            "JE" => "JEY",
            "JO" => "JOR",
            "KZ" => "KAZ",
            "KE" => "KEN",
            "KI" => "KIR",
            "KP" => "PRK",
            "KR" => "KOR",
            "KW" => "KWT",
            "KG" => "KGZ",
            "LA" => "LAO",
            "LV" => "LVA",
            "LB" => "LBN",
            "LS" => "LSO",
            "LR" => "LBR",
            "LY" => "LBY",
            "LI" => "LIE",
            "LT" => "LTU",
            "LU" => "LUX",
            "MO" => "MAC",
            "MK" => "MKD",
            "MG" => "MDG",
            "MW" => "MWI",
            "MY" => "MYS",
            "MV" => "MDV",
            "ML" => "MLI",
            "MT" => "MLT",
            "MH" => "MHL",
            "MQ" => "MTQ",
            "MR" => "MRT",
            "MU" => "MUS",
            "YT" => "MYT",
            "MX" => "MEX",
            "FM" => "FSM",
            "MD" => "MDA",
            "MC" => "MCO",
            "MN" => "MNG",
            "ME" => "MNE",
            "MS" => "MSR",
            "MA" => "MAR",
            "MZ" => "MOZ",
            "MM" => "MMR",
            "NA" => "NAM",
            "NR" => "NRU",
            "NP" => "NPL",
            "NL" => "NLD",
            "AN" => "ANT",
            "NC" => "NCL",
            "NZ" => "NZL",
            "NI" => "NIC",
            "NE" => "NER",
            "NG" => "NGA",
            "NU" => "NIU",
            "NF" => "NFK",
            "MP" => "MNP",
            "NO" => "NOR",
            "OM" => "OMN",
            "PK" => "PAK",
            "PW" => "PLW",
            "PS" => "PSE",
            "PA" => "PAN",
            "PG" => "PNG",
            "PY" => "PRY",
            "PE" => "PER",
            "PH" => "PHL",
            "PN" => "PCN",
            "PL" => "POL",
            "PT" => "PRT",
            "PR" => "PRI",
            "QA" => "QAT",
            "RE" => "REU",
            "RO" => "ROU",
            "RU" => "RUS",
            "RW" => "RWA",
            "SH" => "SHN",
            "KN" => "KNA",
            "LC" => "LCA",
            "PM" => "SPM",
            "VC" => "VCT",
            "WS" => "WSM",
            "SM" => "SMR",
            "ST" => "STP",
            "SA" => "SAU",
            "SN" => "SEN",
            "RS" => "SRB",
            "SC" => "SYC",
            "SL" => "SLE",
            "SG" => "SGP",
            "SK" => "SVK",
            "SI" => "SVN",
            "SB" => "SLB",
            "SO" => "SOM",
            "ZA" => "ZAF",
            "GS" => "SGS",
            "ES" => "ESP",
            "LK" => "LKA",
            "SD" => "SDN",
            "SR" => "SUR",
            "SJ" => "SJM",
            "SZ" => "SWZ",
            "SE" => "SWE",
            "CH" => "CHE",
            "SY" => "SYR",
            "TW" => "TWN",
            "TJ" => "TJK",
            "TZ" => "TZA",
            "TH" => "THA",
            "TL" => "TLS",
            "TG" => "TGO",
            "TK" => "TKL",
            "TO" => "TON",
            "TT" => "TTO",
            "TN" => "TUN",
            "TR" => "TUR",
            "TM" => "TKM",
            "TC" => "TCA",
            "TV" => "TUV",
            "UG" => "UGA",
            "UA" => "UKR",
            "AE" => "ARE",
            "GB" => "GBR",
            "US" => "USA",
            "UM" => "UMI",
            "UY" => "URY",
            "UZ" => "UZB",
            "VU" => "VUT",
            "VE" => "VEN",
            "VN" => "VNM",
            "VG" => "VGB",
            "VI" => "VIR",
            "WF" => "WLF",
            "EH" => "ESH",
            "YE" => "YEM",
            "ZM" => "ZMB",
            "ZW" => "ZWE",
        ];

        return $countries[$alpha2];
    }

}
