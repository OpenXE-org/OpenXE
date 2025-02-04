<?php
/*
 * SPDX-FileCopyrightText: 2022-2024 Andreas Palm
 * SPDX-FileCopyrightText: 2019 Xentral (c) Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg, Germany
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

use Xentral\Modules\ShippingMethod\Model\CreateShipmentResult;
use Xentral\Modules\ShippingMethod\Model\CustomsInfo;
use Xentral\Modules\ShippingMethod\Model\Product;
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;

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

  public function __construct(ApplicationCore $app, ?int $id)
  {
    $this->app = $app;
    if ($id === null || $id === 0)
      return;
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

  public function isEtikettenDrucker(): bool
  {
    return false;
  }

  public abstract function GetName(): string;

  public function GetAdressdaten($id, $sid): array
  {
    $auftragId = $lieferscheinId = $rechnungId = $versandId = 0;
    if ($sid === 'rechnung')
      $rechnungId = $id;
    if ($sid === 'lieferschein') {
      $lieferscheinId = $id;
      $auftragId = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id=$lieferscheinId LIMIT 1");
      $rechnungId = $this->app->DB->Select("SELECT id FROM rechnung WHERE lieferschein = '$lieferscheinId' LIMIT 1");
      if ($rechnungId <= 0)
        $rechnungId = $this->app->DB->Select("SELECT rechnungid FROM lieferschein WHERE id='$lieferscheinId' LIMIT 1");
    }

    if ($auftragId <= 0 && $rechnungId > 0)
      $auftragId = $this->app->DB->Select("SELECT auftragid FROM rechnung WHERE id=$rechnungId LIMIT 1");

    if ($sid === 'rechnung' || $sid === 'lieferschein' || $sid === 'adresse') {
    
      $ret['addresstype'] = 0; // 0 = firma, 1 = packstation, 2 = postfiliale, 3 = privatadresse
    
      $docArr = $this->app->DB->SelectRow("SELECT * FROM `$sid` WHERE id = $id LIMIT 1");
      $ret['addressId'] = $docArr['adresse'];
      $ret['auftragId'] = $auftragId;
      $ret['rechnungId'] = $rechnungId;
      $ret['lieferscheinId'] = $lieferscheinId;

      $addressfields = ['name', 'adresszusatz', 'abteilung', 'ansprechpartner', 'unterabteilung', 'ort', 'plz',
          'strasse', 'land'];       

      $ret['original'] = array_filter($docArr, fn($key) => in_array($key, $addressfields), ARRAY_FILTER_USE_KEY);

      if ($docArr['typ'] == "firma") {
        $ret['company_name'] = $docArr['name'];
        $ret['addresstype'] = 0;
      } else {
        $ret['addresstype'] = 3;
      }

      $ret['contact_name'] = $docArr['ansprechpartner'];
      
      $ret['company_division'] = join(
                        ';', 
                        array_filter(
                            [
                                $docArr['abteilung'],
                                $docArr['unterabteilung']
                            ],
                            fn(string $item) => !empty(trim($item))
                        )
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
        $ret['addresstype'] = 1;
        $ret['parcelstationNumber'] = $hausnummer;
      } else if (str_contains($docArr['strasse'], 'Postfiliale')) {
        $ret['addresstype'] = 2;
        $ret['postofficeNumber'] = $hausnummer;
      }

      $tmp = join(' ', [$docArr['ansprechpartner'], $docArr['abteilung'], $docArr['unterabteilung']]);
      if (preg_match("/\d{6,10}/", $tmp, $match)) {
        $ret['postnumber'] = $match[0];
      }

      if ($auftragId > 0) {
        $internet = $this->app->DB->Select("SELECT internet FROM auftrag WHERE id = $auftragId LIMIT 1");
        if (!empty($internet))
          $orderNumberParts[] = $internet;
      }
      if (!empty($docArr['ihrebestellnummer'])) {
        $orderNumberParts[] = $docArr['ihrebestellnummer'];
      }
      $orderNumberParts[] = ucfirst($sid)." ".$docArr['belegnr'];
      $ret['order_number'] = implode(' / ', $orderNumberParts);
    }

    // wenn rechnung im spiel entweder durch versand oder direkt rechnung
    if ($rechnungId > 0) {
      $invoice_data = $this->app->DB->SelectRow("SELECT zahlungsweise, soll, belegnr FROM rechnung WHERE id='$rechnungId' LIMIT 1");
      $ret['zahlungsweise'] = $invoice_data['zahlungsweise'];
      $ret['betrag'] = $invoice_data['soll'];
      $ret['invoice_number'] = $invoice_data['belegnr'];

      if ($invoice_data['zahlungsweise'] === 'nachnahme') {
        $ret['nachnahme'] = true;
      }
    }

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
    $ret['positions'] = $this->app->DB->SelectArr($sql) ?? [];
  
    return $ret;
  }

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
  public function AdditionalSettings(): array
  {
    return [];
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
   * @param int $versand
   * @param int $lieferschein
   */
  public function SetTracking($tracking, $versand = 0, $lieferschein = 0, $trackingLink = '')
  {
    //if($versand > 0) $this->app->DB->Update("UPDATE versand SET tracking=CONCAT(tracking,if(tracking!='',';',''),'".$tracking."') WHERE id='$versand' LIMIT 1");
    $this->app->User->SetParameter('versand_lasttracking', $tracking);
    $this->app->User->SetParameter('versand_lasttracking_link', $trackingLink);
    $this->app->User->SetParameter('versand_lasttracking_versand', $versand);
    $this->app->User->SetParameter('versand_lasttracking_lieferschein', $lieferschein);
  }

  /**
   * @param string $tracking
   */
  public function deleteTrackingFromUserdata($tracking)
  {
    if (empty($tracking)) {
      return;
    }
    $trackingUser = !empty($this->app->User) && method_exists($this->app->User, 'GetParameter') ?
        $this->app->User->GetParameter('versand_lasttracking') : '';
    if (empty($trackingUser) || $trackingUser !== $tracking) {
      return;
    }

    $this->app->User->SetParameter('versand_lasttracking', '');
    $this->app->User->SetParameter('versand_lasttracking_link', '');
    $this->app->User->SetParameter('versand_lasttracking_versand', '');
    $this->app->User->SetParameter('versand_lasttracking_lieferschein', '');
  }

  /**
   * @param string $tracking
   *
   * @return string mixed
   */
  public function TrackingReplace($tracking)
  {
    return $tracking;
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

  public function Paketmarke(string $target, string $docType, int $docId, $gewicht = 0, $versandpaket = null): void
  {
    $address = $this->GetAdressdaten($docId, $docType);
    $address['weight'] = $gewicht;

    if (isset($_SERVER['CONTENT_TYPE']) && ($_SERVER['CONTENT_TYPE'] === 'application/json')) {
      $json = json_decode(file_get_contents('php://input'));
      $ret = [];
      if ($json->submit == 'print') {
        $result = $this->CreateShipment($json, $address);
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
                        {$address['lieferscheinId']},
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

    $address['shipment_type'] = CustomsInfo::CUSTOMS_TYPE_GOODS;
    $products = $this->GetShippingProducts();
    $products = array_combine(array_column($products, 'Id'), $products);
    $address['product'] = $products[0]->Id ?? '';

    $countries = $this->app->DB->SelectArr("SELECT iso, bezeichnung_de name, eu FROM laender ORDER BY bezeichnung_de");
    if(!empty($countries)) {
        $countries = array_combine(array_column($countries, 'iso'), $countries);    
    } else {
        $countries = Array();        
        $this->app->Tpl->addMessage('error', 'L&auml;nderliste ist leer. Siehe Einstellungen -> L&auml;nderliste.', false, 'PAGE');
    }  
    
    switch ($this->shippingMail) {
        case -1:
            $address['email'] = '';
        break;
        case 1:
            // User text template (not implemented)
        break;
        default:            
        break;
    }

    $json['form'] = $address;
    $json['countries'] = $countries;
    $json['products'] = $products;
    $json['customs_shipment_types'] = [
        CustomsInfo::CUSTOMS_TYPE_GIFT => 'Geschenk',
        CustomsInfo::CUSTOMS_TYPE_DOCUMENTS => 'Dokumente',
        CustomsInfo::CUSTOMS_TYPE_GOODS => 'Handelswaren',
        CustomsInfo::CUSTOMS_TYPE_SAMPLE => 'Erprobungswaren',
        CustomsInfo::CUSTOMS_TYPE_RETURN => 'Rücksendung'
    ];
    $json['messages'] = [];
    $json['submitting'] = false;
    $json['form']['services'] = [
        Product::SERVICE_PREMIUM => false
    ];

    $this->app->Tpl->Set('JSON', json_encode($json));
    $this->app->Tpl->Set('CARRIERNAME', $this->GetName());
    $this->app->Tpl->Parse($target, 'createshipment.tpl');
  }

  public abstract function CreateShipment(object $json, array $address): CreateShipmentResult;

  /**
   * @return Product[]
   */
  public abstract function GetShippingProducts(): array;

  public abstract function GetShipmentStatus(string $tracking): ShipmentStatus|null;
}
