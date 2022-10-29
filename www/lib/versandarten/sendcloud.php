<?php

use Xentral\Carrier\SendCloud\Data\Document;
use Xentral\Carrier\SendCloud\Data\ParcelCreation;
use Xentral\Carrier\SendCloud\Data\ParcelItem;
use Xentral\Carrier\SendCloud\Data\ParcelResponse;
use Xentral\Carrier\SendCloud\SendCloudApi;
use Xentral\Carrier\SendCloud\Data\SenderAddress;
use Xentral\Carrier\SendCloud\Data\ShippingProduct;
use Xentral\Carrier\SendCloud\Data\ShippingMethod;

require_once dirname(__DIR__) . '/class.versanddienstleister.php';

class Versandart_sendcloud extends Versanddienstleister
{
  protected SendCloudApi $api;
  protected array $options;

  public function __construct(Application $app, ?int $id)
  {
    parent::__construct($app, $id);
    if (!isset($this->id))
      return;
    $this->api = new SendCloudApi($this->settings->public_key, $this->settings->private_key);
    $this->options['customs_shipment_types'] = [
        0 => 'Geschenk',
        1 => 'Dokumente',
        2 => 'Kommerzielle Waren',
        3 => 'Erprobungswaren',
        4 => 'Rücksendung'
    ];
  }

  protected function FetchOptionsFromApi()
  {
    $list = $this->api->GetSenderAddresses();
    foreach ($list as $item) {
      /* @var SenderAddress $item */
      $senderAddresses[$item->Id] = $item;
    }
    $senderCountry = $senderAddresses[$this->settings->sender_address]->Country ?? 'DE';
    $list = $this->api->GetShippingProducts($senderCountry);
    foreach ($list as $item) {
      /* @var ShippingProduct $item */
      $shippingProducts[$item->Code] = $item;
    }

    $this->options['senders'] = array_map(fn(SenderAddress $x) => strval($x), $senderAddresses ?? []);
    $this->options['products'] = array_map(fn(ShippingProduct $x) => $x->Name, $shippingProducts ?? []);
    $this->options['products'][0] = '';
    $this->options['selectedProduct'] = $shippingProducts[$this->settings->shipping_product] ?? [];
    natcasesort($this->options['products']);
  }

  public function AdditionalSettings(): array
  {
    $this->FetchOptionsFromApi();
    return [
        'public_key' => ['typ' => 'text', 'bezeichnung' => 'API Public Key:'],
        'private_key' => ['typ' => 'text', 'bezeichnung' => 'API Private Key:'],
        'sender_address' => ['typ' => 'select', 'bezeichnung' => 'Absender-Adresse:', 'optionen' => $this->options['senders']],
        'shipping_product' => ['typ' => 'select', 'bezeichnung' => 'Versand-Produkt:', 'optionen' => $this->options['products']],
        'default_customs_shipment_type' => ['typ' => 'select', 'bezeichnung' => 'Sendungsart:', 'optionen' => $this->options['customs_shipment_types']],
    ];
  }

  public function Paketmarke(string $target, string $docType, int $docId): void
  {
    $address = $this->GetAdressdaten($docId, $docType);

    if (isset($_SERVER['HTTP_CONTENT_TYPE']) && ($_SERVER['HTTP_CONTENT_TYPE'] === 'application/json')) {
      $json = json_decode(file_get_contents('php://input'));
      $response = [];
      if ($json->submit == 'print') {
        header('Content-Type: application/json');
        $parcel = new ParcelCreation();
        $parcel->SenderAddressId = $this->settings->sender_address;
        $parcel->ShippingMethodId = $json->method;
        $parcel->Name = $json->l_name;
        $parcel->CompanyName = $json->l_companyname;
        $parcel->Country = $json->land;
        $parcel->PostalCode = $json->plz;
        $parcel->City = $json->ort;
        $parcel->Address = $json->strasse;
        $parcel->Address2 = $json->l_address2;
        $parcel->HouseNumber = $json->hausnummer;
        $parcel->EMail = $json->email;
        $parcel->Telephone = $json->telefon;
        $parcel->CountryState = $json->bundesland;
        $parcel->CustomsInvoiceNr = $json->invoice_number;
        $parcel->CustomsShipmentType = $json->sendungsart;
        $parcel->TotalInsuredValue = $json->total_insured_value;
        $parcel->OrderNumber = $json->order_number;
        foreach ($json->positions as $pos) {
          $item = new ParcelItem();
          $item->HsCode = $pos->zolltarifnummer;
          $item->Description = $pos->bezeichnung;
          $item->Quantity = $pos->menge;
          $item->OriginCountry = $pos->herkunftsland;
          $item->Price = $pos->zolleinzelwert;
          $item->Weight = $pos->zolleinzelgewicht * 1000;
          $parcel->ParcelItems[] = $item;
        }
        $parcel->Weight = floatval($json->weight) * 1000;
        $result = $this->api->CreateParcel($parcel);
        if ($result instanceof ParcelResponse) {
          $sql = "INSERT INTO versand 
            (adresse, lieferschein, versandunternehmen, gewicht, tracking, tracking_link, anzahlpakete) 
            VALUES 
            ({$address['addressId']}, {$address['lieferscheinId']}, '$this->type',
             '$json->weight', '$result->TrackingNumber', '$result->TrackingUrl', 1)";
          $this->app->DB->Insert($sql);
          $response['messages'][] = ['class' => 'info', 'text' => "Paketmarke wurde erfolgreich erstellt: $result->TrackingNumber"];

          $doc = $result->GetDocumentByType(Document::TYPE_LABEL);
          $filename = $this->app->erp->GetTMP() . join('_', ['Sendcloud', $doc->Type, $doc->Size, $result->TrackingNumber]) . '.pdf';
          file_put_contents($filename, $this->api->DownloadDocument($doc));
          $this->app->printer->Drucken($this->labelPrinterId, $filename);

          $doc = $result->GetDocumentByType(Document::TYPE_CN23);
          $filename = $this->app->erp->GetTMP() . join('_', ['Sendcloud', $doc->Type, $doc->Size, $result->TrackingNumber]) . '.pdf';
          file_put_contents($filename, $this->api->DownloadDocument($doc));
          $this->app->printer->Drucken($this->documentPrinterId, $filename);
        } else {
          $response['messages'][] = ['class' => 'error', 'text' => $result];
        }
        echo json_encode($response);
        $this->app->ExitXentral();
      }
    }

    $address['l_name'] = empty(trim($address['ansprechpartner'])) ? trim($address['name']) : trim($address['ansprechpartner']);
    $address['l_companyname'] = !empty(trim($address['ansprechpartner'])) ? trim($address['name']) : '';
    $address['l_address2'] = join(';', array_filter([
        $address['abteilung'],
        $address['unterabteilung'],
        $address['adresszusatz']
    ], fn(string $item) => !empty(trim($item))));

    $this->FetchOptionsFromApi();
    /** @var ShippingProduct $product */
    $product = $this->options['selectedProduct'];
    $methods = [];
    /** @var ShippingMethod $item */
    foreach ($product->ShippingMethods as $item)
      $methods[$item->Id] = $item->Name;
    $address['method'] = array_key_first($methods);
    $address['sendungsart'] = $this->settings->default_customs_shipment_type;

    $this->app->Tpl->Set('JSON', json_encode($address));
    $this->app->Tpl->Set('JSON_COUNTRIES', json_encode($this->app->erp->GetSelectLaenderliste()));
    $this->app->Tpl->Set('JSON_METHODS', json_encode($methods));
    $this->app->Tpl->Set('JSON_CUSTOMS_SHIPMENT_TYPES', json_encode($this->options['customs_shipment_types']));
    $this->app->Tpl->Parse($target, 'versandarten_sendcloud.tpl');
  }

}