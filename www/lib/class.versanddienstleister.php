<?php
/*
 * SPDX-FileCopyrightText: 2022-2024 Andreas Palm
 * SPDX-FileCopyrightText: 2019 Xentral (c) Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg, Germany
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

use Xentral\Components\Http\Request;
use Xentral\Modules\ShippingMethod\Model\AddressType;
use Xentral\Modules\ShippingMethod\Model\CreateShipmentResult;
use Xentral\Modules\ShippingMethod\Model\CustomsDeclaration;
use Xentral\Modules\ShippingMethod\Model\CustomsDeclarationItem;
use Xentral\Modules\ShippingMethod\Model\Product;
use Xentral\Modules\ShippingMethod\Model\Shipment;
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;
use Xentral\Modules\ShippingMethod\Model\ShipmentType;

abstract class Versanddienstleister
{
  protected int $id;
  protected ApplicationCore $app;
  protected string $type;
  protected int $projectId;
  protected ?int $labelPrinterId;
  protected ?int $documentPrinterId;
  protected int $shippingMail;
  protected ?int $businessLetterTemplateId;
  protected ?object $settings;
  protected array $errors; // To allow catching of exceptions in the constructor and evaluating them above

  protected Request $request;

  public function __construct(ApplicationCore $app, ?int $id)
  {
    $this->app = $app;
    $this->errors = array();
    $this->request = $this->app->Container->get('Request');
    if ($id === null || $id === 0) {
        $this->errors[] = "No ID given";
        return;
    }
    $this->id = $id;
    $row = $this->app->DB->SelectRow("SELECT * FROM versandarten WHERE id=$this->id");
    $this->type = $row['type'];
    $this->projectId = $row['projekt'];
    $this->labelPrinterId = $row['paketmarke_drucker'];
    $this->documentPrinterId = $row['export_drucker'];
    $this->shippingMail = $row['versandmail'];
    $this->businessLetterTemplateId = $row['geschaeftsbrief_vorlage'];
    $this->settings = json_decode($row['einstellungen_json']);
  }

  // Returns an array of errors if any occurred
  public function getErrors() : array {
    return($this->errors);
  }

  public function isEtikettenDrucker(): bool
  {
    return false;
  }

  protected function GetAdressdaten(int $lieferscheinId): array
  {
    $docArr = $this->app->DB->SelectRow("SELECT * FROM lieferschein WHERE id = $lieferscheinId LIMIT 1");

    $addressfields = ['name', 'adresszusatz', 'abteilung', 'ansprechpartner', 'unterabteilung', 'ort', 'plz',
        'strasse', 'land'];

    $ret['original'] = array_filter($docArr, fn($key) => in_array($key, $addressfields), ARRAY_FILTER_USE_KEY);

    if ($docArr['typ'] == "firma") {
      $ret['companyName'] = $docArr['name'];
      $ret['addresstype'] = AddressType::COMPANY;
    } else {
      $ret['addresstype'] = AddressType::PRIVATE;
    }

    $ret['contactName'] = $docArr['ansprechpartner'];

    $ret['companyDivision'] = join(
        ';',
        array_filter(
            [
                $docArr['abteilung'],
                $docArr['unterabteilung'],
            ],
            fn(string $item) => !empty(trim($item)),
        ),
    );
            
    $ret['name'] = $docArr['name'];
    $ret['address2'] = $docArr['adresszusatz'];
    $ret['city'] = $docArr['ort'];
    $ret['zip'] = $docArr['plz'];
    $ret['country'] = $docArr['land'];
    $ret['phone'] = $docArr['telefon'];
    $ret['email'] = $docArr['email'];

    $strasse = trim($docArr['strasse']);
    $ret['streetwithnumber'] = $strasse;
    $hausnummer = trim($this->app->erp->ExtractStreetnumber($strasse));
    $strasse = trim(str_replace($hausnummer, '', $strasse));
    $strasse = str_replace('.', '', $strasse);

    if ($strasse == '') {
      $strasse = trim($hausnummer);
      $hausnummer = '';
    }
    $ret['street'] = $strasse;
    $ret['streetnumber'] = $hausnummer;

    if (str_contains($docArr['strasse'], 'Packstation')) {
      $ret['addresstype'] = AddressType::PARCELSTATION;
      $ret['parcelstationNumber'] = $hausnummer;
    } else if (str_contains($docArr['strasse'], 'Postfiliale')) {
      $ret['addresstype'] = AddressType::SHOP;
      $ret['postofficeNumber'] = $hausnummer;
    }

    $tmp = join(' ', [$docArr['ansprechpartner'], $docArr['abteilung'], $docArr['unterabteilung']]);
    if (preg_match("/\d{6,10}/", $tmp, $match)) {
      $ret['postnumber'] = $match[0];
    }

    return $ret;
  }

  protected function GetCustomsDeclaration(int $lieferscheinId): CustomsDeclaration {
      $ret = new CustomsDeclaration();
      $ret->shipmentType = ShipmentType::GOODS;

      $sql = "SELECT r.zahlungsweise, r.soll, r.belegnr
              FROM rechnung r
              JOIN lieferschein l on l.rechnungid = r.id
              WHERE l.id=$lieferscheinId LIMIT 1";
      $invoice_data = $this->app->DB->SelectRow($sql);
//      $ret['zahlungsweise'] = $invoice_data['zahlungsweise'];
//      $ret['betrag'] = $invoice_data['soll'];
      $ret->invoiceNumber = $invoice_data['belegnr'] ?? '';



      $sql = "SELECT
        lp.bezeichnung,
        lp.menge,
        coalesce(nullif(lp.zolltarifnummer, '0'), nullif(rp.zolltarifnummer, '0'), nullif(a.zolltarifnummer, '')) as zolltarifnummer,
        coalesce(nullif(lp.herkunftsland, '0'), nullif(rp.herkunftsland, '0'), nullif(a.herkunftsland, '')) as herkunftsland,
        coalesce(nullif(lp.zolleinzelwert, '0'), rp.preis *(1-rp.rabatt/100), 0) as zolleinzelwert,
        coalesce(nullif(lp.zolleinzelgewicht, 0), a.gewicht) as zolleinzelgewicht,
        lp.zollwaehrung
      FROM lieferschein_position lp
      JOIN artikel a on lp.artikel = a.id
      LEFT OUTER JOIN auftrag_position ap on lp.auftrag_position_id = ap.id
      LEFT OUTER JOIN rechnung_position rp on ap.id = rp.auftrag_position_id
      LEFT OUTER JOIN rechnung r on rp.rechnung = r.id
      WHERE lp.lieferschein = $lieferscheinId
      AND a.lagerartikel = 1
      AND r.status != 'storniert'
      ORDER BY lp.sort";
      foreach ($this->app->DB->SelectArr($sql) as $row) {
          $pos = new CustomsDeclarationItem();
          $pos->description = $row['bezeichnung'];
          $pos->quantity = $row['menge'];
          $pos->hsCode = $row['zolltarifnummer'] ?? '';
          $pos->originCountryCode = $row['herkunftsland'] ?? '';
          $pos->itemValue = floatval($row['zolleinzelwert']);
          $pos->itemWeight = floatval($row['zolleinzelgewicht']);
          $ret->positions[] = $pos;
      }
      return $ret;
  }

  protected function GetShipmentDefaults(int $lieferscheinId) : Shipment {
        $shipment = new Shipment();
        $shipment->address = $this->GetAdressdaten($lieferscheinId);
        $shipment->customsDeclaration = $this->GetCustomsDeclaration($lieferscheinId);

        $sql = "SELECT l.belegnr 
              FROM lieferschein l
              WHERE l.id = $lieferscheinId";
        $data = $this->app->DB->SelectRow($sql);
        $shipment->reference = $data['belegnr'];

        return $shipment;
    }

  /**
   * Renders all additional settings as fields into $target
   * @param string $target template placeholder for rendered output
   * @param array $form data for form values (from database or form submit)
   * @return void
   */
  public function RenderAdditionalSettings(string $target, array $form): void
  {
    $fields = $this->AdditionalSettings();
    if ($this->app->Secure->GetPOST('speichern')) {
      $json = $this->app->DB->Select("SELECT einstellungen_json FROM versandarten WHERE id = '" . $this->id . "' LIMIT 1");
      $modul = $this->app->DB->Select("SELECT modul FROM versandarten WHERE id = '" . $this->id . "' LIMIT 1");
      if (!empty($json)) {
        $json = @json_decode($json, true);
      } else {
        $json = array();
        foreach ($fields as $name => $val) {
          if (isset($val['default'])) {
            $json[$name] = $val['default'];
          }
        }
      }
      if (empty($json)) {
        $json = null;
      }
      foreach ($fields as $name => $val) {

        if ($modul === $this->app->Secure->GetPOST('modul_name')) {
          $json[$name] = $this->app->Secure->GetPOST($name, '', '', 1);
        }

        if (isset($val['replace'])) {
          switch ($val['replace']) {
            case 'lieferantennummer':
              $json[$name] = $this->app->erp->ReplaceLieferantennummer(1, $json[$name], 1);
              break;
          }
        }
      }
      $json_str = $this->app->DB->real_escape_string(json_encode($json));
      $this->app->DB->Update("UPDATE versandarten SET einstellungen_json = '$json_str' WHERE id = '" . $this->id . "' LIMIT 1");
    }
    $html = '';

    foreach ($fields as $name => $val) // set missing default values
    {
      if (isset($val['default']) && !isset($form[$name])) {
        $form[$name] = $val['default'];
      }
    }
    foreach ($fields as $name => $val) {
      if (isset($val['heading']))
        $html .= '<tr><td colspan="2"><b>' . html_entity_decode($val['heading']) . '</b></td></tr>';

      $html .= '<tr><td>' . ($val['bezeichnung'] ?? $name) . '</td><td>';
      if (isset($val['replace'])) {
        switch ($val['replace']) {
          case 'lieferantennummer':
            $form[$name] = $this->app->erp->ReplaceLieferantennummer(0, $form[$name], 0);
            $this->app->YUI->AutoComplete($name, 'lieferant', 1);
            break;
          case 'shop':
            $form[$name] .= ($form[$name] ? ' ' . $this->app->DB->Select("SELECT bezeichnung FROM shopexport WHERE id = '" . (int)$form[$name] . "'") : '');
            $this->app->YUI->AutoComplete($name, 'shopnameid');
            break;
          case 'etiketten':
            $this->app->YUI->AutoComplete($name, 'etiketten');
            break;
        }
      }
      switch ($val['typ'] ?? 'text') {
        case 'textarea':
          $html .= '<textarea name="' . $name . '" id="' . $name . '">' . ($form[$name] ?? '') . '</textarea>';
          break;
        case 'checkbox':
          $html .= '<input type="checkbox" name="' . $name . '" id="' . $name . '" value="1" ' . ($form[$name] ?? false ? ' checked="checked" ' : '') . ' />';
          break;
        case 'select':
          $html .= $this->app->Tpl->addSelect('return', $name, $name, $val['optionen'], $form[$name]);
          break;
        case 'submit':
          if (isset($val['text']))
            $html .= '<form method="POST"><input type="submit" name="' . $name . '" value="' . $val['text'] . '"></form>';
          break;
        case 'custom':
          if (isset($val['function'])) {
            $tmpfunction = $val['function'];
            if (method_exists($this, $tmpfunction)) {
              $html .= $this->$tmpfunction();
            }
          }
          break;
        default:
          $html .= '<input type="text"'
              . (!empty($val['size']) ? ' size="' . $val['size'] . '"' : '')
              . (!empty($val['placeholder']) ? ' placeholder="' . $val['placeholder'] . '"' : '')
              . ' name="' . $name . '" id="' . $name . '" value="' . (isset($form[$name]) ? htmlspecialchars($form[$name]) : '') . '" />';
          break;
      }
      if (isset($val['info']) && $val['info'])
        $html .= ' <i>' . $val['info'] . '</i>';

      $html .= '</td></tr>';
    }
    $this->app->Tpl->Add($target, $html);
  }

  /**
   * Validate form data for this module
   * Form data is passed by reference so replacements (id instead of text) can be performed here as well
   * @param array $form submitted form data
   * @return array
   */
  public function ValidateSettings(array &$form): array
  {
    return [];
  }


  /**
   * @param string $tracking
   * @param int $notsend
   * @param string $link
   * @param string $rawlink
   *
   * @return bool
   */
  public function Trackinglink($tracking, &$notsend, &$link, &$rawlink)
  {
    $notsend = 0;
    $rawlink = '';
    $link = '';
    return true;
  }

  public function Paketmarke(string $target, int $lieferscheinId, $gewicht = 0, $versandpaket = null): void
  {
    $this->app->ModuleScriptCache->IncludeJavascriptModules('ShippingMethod', ['classes/Modules/ShippingMethod/www/js/shipment.entry.js']);
    $shipment = $this->GetShipmentDefaults($lieferscheinId);
    $shipment->package->weight = $gewicht;

    if ($this->request->getMethod() === 'POST' && $this->request->getContentType() === 'json') {
      $json = $this->request->getJson();
      $ret = [];
      if ($json->submit == 'print') {
        $result = $this->CreateShipment($json);
        if ($result->Success) {
            if (empty($versandpaket)) {
                $sql = "INSERT INTO versandpakete 
                      (
                        lieferschein_ohne_pos,
                        gewicht,
                        tracking,
                        tracking_link,
                        status,
                        versandart,
                        versender
                      ) 
                      VALUES 
                      (
                        $lieferscheinId,
                        '$json->weight',
                        '$result->TrackingNumber',
                        '$result->TrackingUrl',
                        'neu',
                        '$this->type',
                        '".$this->app->User->GetName()."'
                    )";
                $this->app->DB->Insert($sql);
                $versandpaket = $this->app->DB->GetInsertID();
            }
            else {
                $sql = "UPDATE versandpakete SET 
                            gewicht = '".$json->weight."',
                            tracking = '".$result->TrackingNumber."',
                            tracking_link = '".$result->TrackingUrl."'
                        WHERE id = '".$versandpaket."'
                        ";             
                $this->app->DB->Update($sql);      
            }       

            $filename = join('_', [$this->type, 'Label', $result->TrackingNumber]) . '.pdf';
            $filefullpath = $this->app->erp->GetTMP() . $filename;
            file_put_contents($filefullpath, $result->Label);
            $this->app->erp->CreateDateiWithStichwort(
                $filename,
                'Paketmarke '.$this->type.' '.$result->TrackingNumber,
                'Paketmarke Versandpaket Nr. '.$versandpaket,
                '',
                $filefullpath,
                $this->app->User->GetName(),
                'paketmarke',
                'versandpaket',
                $versandpaket
            );

            $this->app->printer->Drucken($this->labelPrinterId, $filefullpath);

            if (isset($result->ExportDocuments)) {
                $filefullpath = $this->app->erp->GetTMP() . join('_', [$this->type, 'ExportDoc', $result->TrackingNumber]) . '.pdf';
                file_put_contents($filefullpath, $result->ExportDocuments);
                $this->app->printer->Drucken($this->documentPrinterId, $filefullpath);
            }
            $ret['messages'][] = ['class' => 'info', 'text' => "Paketmarke wurde erfolgreich erstellt: $result->TrackingNumber"];
            if ($result->AdditionalInfo != null)
                $ret['messages'][] = ['class' => 'info', 'text' => $result->AdditionalInfo];
        } else {
            $ret['messages'] = array_map(fn(string $item) => ['class' => 'error', 'text' => $item], array_unique($result->Errors));
        }
      }
      header('Content-Type: application/json');
      echo json_encode($ret);
      $this->app->ExitXentral();
    }

    $products = $this->GetShippingProducts();
    $products = array_combine(array_column($products, 'Id'), $products);
    $shipment->productId = $products[0]->Id ?? '';

    $countries = $this->app->DB->SelectArr("SELECT iso, bezeichnung_de name, eu FROM laender ORDER BY bezeichnung_de");
    if(!empty($countries)) {
        $countries = array_combine(array_column($countries, 'iso'), $countries);    
    } else {
        $countries = Array();        
        $this->app->Tpl->addMessage('error', 'L&auml;nderliste ist leer. Siehe Einstellungen -> L&auml;nderliste.', false, 'PAGE');
    }  
    
    switch ($this->shippingMail) {
        case -1:
            $shipment->address['email'] = '';
        break;
        case 1:
            // User text template (not implemented)
        break;
        default:            
        break;
    }


    $json['model'] = $shipment;
    $json['countries'] = $countries;
    $json['products'] = $products;
    $json['messages'] = [];
    $json['carrier'] = $this->GetName();

    $this->app->Tpl->Set('JSON', json_encode($json));
    $this->app->Tpl->Parse($target, 'createshipment.tpl');
  }

    public abstract function GetName(): string;
    /**
     * Returns an array of additional field definitions to be stored for this module:
     * [
     *   'field_name' => [
     *     'typ' => text(default)|textarea|checkbox|select
     *     'default' => default value
     *     'optionen' => just for selects [key=>value]
     *     'size' => size attribute for text fields
     *     'placeholder' => placeholder attribute for text fields
     *   ]
     * ]
     *
     * @return array
     */
    public abstract function AdditionalSettings(): array;
    protected abstract function CreateShipment(object $json): CreateShipmentResult;
    /**
   * @return Product[]
   */
    protected abstract function GetShippingProducts(): array;
    protected abstract function GetShipmentStatus(string $tracking): ShipmentStatus|null;
}
