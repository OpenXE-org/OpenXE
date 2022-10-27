<?php

use Xentral\Carrier\SendCloud\Data\Document;
use Xentral\Carrier\SendCloud\Data\ParcelCreation;
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
    $this->options['selectedProduct'] = $shippingProducts[$this->settings->shipping_product];
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
    ];
  }

  public function Paketmarke(string $target, string $doctype, int $docid): void
  {
    $address = $this->GetAdressdaten($docid, $doctype);
    $submit = false;

    if ($this->app->Secure->GetPOST('drucken') != '') {
      $submit = true;
      $parcel = new ParcelCreation();
      $parcel->SenderAddressId = $this->settings->sender_address;
      $parcel->ShippingMethodId = $this->app->Secure->GetPOST('method');
      $parcel->Name = $this->app->Secure->GetPOST('name');
      $parcel->CompanyName = $this->app->Secure->GetPOST('name3');
      $parcel->Country = $this->app->Secure->GetPOST('land');
      $parcel->PostalCode = $this->app->Secure->GetPOST('plz');
      $parcel->City = $this->app->Secure->GetPOST('ort');
      $parcel->Address = $this->app->Secure->GetPOST('strasse');
      $parcel->Address2 = $this->app->Secure->GetPOST('name2');
      $parcel->HouseNumber = $this->app->Secure->GetPOST('hausnummer');
      $parcel->EMail = $this->app->Secure->GetPOST('email');
      $parcel->Telephone = $this->app->Secure->GetPOST('phone');
      $parcel->CountryState = $this->app->Secure->GetPOST('state');
      $parcel->CustomsInvoiceNr = $this->app->Secure->GetPOST('rechnungsnummer');
      $parcel->CustomsShipmentType = $this->app->Secure->GetPOST('sendungsart');
      $parcel->TotalInsuredValue = (int)$this->app->Secure->GetPOST('versicherungssumme');
      $parcel->OrderNumber = $this->app->Secure->GetPOST('bestellnummer');
      $weight = $this->app->Secure->GetPOST('weight');
      $parcel->Weight = floatval($weight) * 1000;
      $result = $this->api->CreateParcel($parcel);
      if ($result instanceof ParcelResponse) {
        $sql = "INSERT INTO versand 
            (adresse, lieferschein, versandunternehmen, gewicht, tracking, tracking_link, anzahlpakete) 
            VALUES 
            ({$address['addressId']}, {$address['lieferscheinId']}, '$this->type',
             '$weight', '$result->TrackingNumber', '$result->TrackingUrl', 1)";
        $this->app->DB->Insert($sql);
        $this->app->Tpl->addMessage('info', "Paketmarke wurde erfolgreich erstellt: $result->TrackingNumber");

        $doc = $result->GetDocumentByType(Document::TYPE_LABEL);
        $filename = $this->app->erp->GetTMP().join('_', ['Sendcloud', $doc->Type, $doc->Size, $result->TrackingNumber]).'.pdf';
        file_put_contents($filename, $this->api->DownloadDocument($doc));
        $this->app->printer->Drucken($this->paketmarke_drucker, $filename);
      } else {
        $this->app->Tpl->addMessage('error', $result);
      }
    }

    $this->app->Tpl->Set('NAME', $submit ? $this->app->Secure->GetPOST('name') : $address['name']);
    $this->app->Tpl->Set('NAME2', $submit ? $this->app->Secure->GetPOST('name2') : $address['name2']);
    $this->app->Tpl->Set('NAME3', $submit ? $this->app->Secure->GetPOST('name3') : $address['name3']);
    $this->app->Tpl->Set('LAND', $this->app->erp->SelectLaenderliste($submit ? $this->app->Secure->GetPOST('land') : $address['land']));
    $this->app->Tpl->Set('PLZ', $submit ? $this->app->Secure->GetPOST('plz') : $address['plz']);
    $this->app->Tpl->Set('ORT', $submit ? $this->app->Secure->GetPOST('ort') : $address['ort']);
    $this->app->Tpl->Set('STRASSE', $submit ? $this->app->Secure->GetPOST('strasse') : $address['strasse']);
    $this->app->Tpl->Set('HAUSNUMMER', $submit ? $this->app->Secure->GetPOST('hausnummer') : $address['hausnummer']);
    $this->app->Tpl->Set('EMAIL', $submit ? $this->app->Secure->GetPOST('email') : $address['email']);
    $this->app->Tpl->Set('TELEFON', $submit ? $this->app->Secure->GetPOST('phone') : $address['phone']);
    $this->app->Tpl->Set('WEIGHT', $submit ? $this->app->Secure->GetPOST('weight') : $address['standardkg']);
    $this->app->Tpl->Set('LENGTH', $submit ? $this->app->Secure->GetPOST('length') : '');
    $this->app->Tpl->Set('WIDTH', $submit ? $this->app->Secure->GetPOST('width') : '');
    $this->app->Tpl->Set('HEIGHT', $submit ? $this->app->Secure->GetPOST('height') : '');
    $this->app->Tpl->Set('ORDERNUMBER', $submit ? $this->app->Secure->GetPOST('order_number') : $address['order_number']);
    $this->app->Tpl->Set('INVOICENUMBER', $submit ? $this->app->Secure->GetPOST('invoice_number') : $address['invoice_number']);

    $method = $this->app->Secure->GetPOST('method');
    $this->FetchOptionsFromApi();
    /** @var ShippingProduct $product */
    $product = $this->options['selectedProduct'];
    $methods = [];
    /** @var ShippingMethod $item */
    foreach ($product->ShippingMethods as $item)
      $methods[$item->Id] = $item->Name;
    $this->app->Tpl->addSelect('METHODS', 'method', 'method', $methods, $method);
    $this->app->Tpl->Parse($target, 'versandarten_sendcloud.tpl');
  }

}