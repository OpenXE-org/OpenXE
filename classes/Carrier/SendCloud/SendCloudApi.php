<?php

/*
 * SPDX-FileCopyrightText: 2022-2024 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\SendCloud;

use Exception;
use Xentral\Carrier\SendCloud\Data\Document;
use Xentral\Carrier\SendCloud\Data\ParcelCreation;
use Xentral\Carrier\SendCloud\Data\ParcelResponse;
use Xentral\Carrier\SendCloud\Data\SenderAddress;
use Xentral\Carrier\SendCloud\Data\ShippingProduct;
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;

class SendCloudApi
{
  /**
   * @var ?string $public_key
   */
  protected ?string $public_key;
  /**
   * @var ?string $private_key
   */
  protected ?string $private_key;

  const PROD_BASE_URI = 'https://panel.sendcloud.sc/api/v2';

  public function __construct($public_key, $private_key)
  {
    $this->public_key = $public_key;
    $this->private_key = $private_key;
  }

  /**
   * @throws SendcloudApiException
   */
  public function GetSenderAddresses(): array
  {
    $uri = self::PROD_BASE_URI . '/user/addresses/sender';
    $response = $this->sendRequest($uri);
    foreach ($response['body']->sender_addresses as $item)
      $res[] = SenderAddress::fromApiResponse($item);
    return $res ?? [];
  }

  /**
   * @throws SendcloudApiException
   */
  public function GetShippingProducts(string $sourceCountry, ?string $targetCountry = null, ?int $weight = null,
                                      ?int   $height = null, ?int $length = null, ?int $width = null): array
  {
    $uri = self::PROD_BASE_URI . '/shipping-products';
    $params = ['from_country' => $sourceCountry];
    if ($targetCountry !== null)
      $params['to_country'] = $targetCountry;
    if ($weight !== null && $weight > 0)
      $params['weight'] = $weight;
    if ($height !== null && $height > 0) {
      $params['height'] = $height;
      $params['height_unit'] = 'centimeter';
    }
    if ($length !== null && $length > 0) {
      $params['length'] = $length;
      $params['length_unit'] = 'centimeter';
    }
    if ($width !== null && $width > 0) {
      $params['width'] = $width;
      $params['width_unit'] = 'centimeter';
    }
    $response = $this->sendRequest($uri, $params);
    return array_map(fn($x) => ShippingProduct::fromApiResponse($x), $response['body'] ?? []);
  }

  /**
   * @throws SendcloudApiException
   */
  public function CreateParcel(ParcelCreation $parcel): ParcelResponse|string|null
  {
    $uri = self::PROD_BASE_URI . '/parcels?errors=verbose-carrier';
    $response = $this->sendRequest($uri, null, true, ['parcel' => $parcel->toApiRequest()], [200,400]);
    switch ($response['code']) {
      case 200:
        if (isset($response['body']->parcel))
          try {
            return ParcelResponse::fromApiResponse($response['body']->parcel);
          } catch (Exception $e) {
            throw new SendcloudApiException(previous: $e);
          }
        break;
      case 400:
        if (isset($response['body']->error))
          return $response['body']->error->message;
        break;
    }
    throw SendcloudApiException::fromResponse($response);
  }

  /**
   * @throws SendcloudApiException
   */
  public function DownloadDocument(Document $document): string
  {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $document->Link,
        CURLOPT_HTTPHEADER => [
            "Authorization: Basic " . base64_encode($this->public_key . ':' . $this->private_key)
        ],
    ]);
    $output = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    if ($code != 200)
      throw SendcloudApiException::fromResponse(['code' => $code, 'body' => $output]);
    return $output;
  }

  /**
   * @throws SendcloudApiException
   */
  public function GetTrackingStatus(string $trackingNumber): ShipmentStatus|null
  {
      $uri = self::PROD_BASE_URI . '/tracking/' . $trackingNumber;
      $response = $this->sendRequest($uri);
      $highest = null;
      foreach ($response['body']->statuses as $status) {
          switch ($status->parent_status) {
              case 'announcing':
              case 'ready-to-send':
                  if ($highest === null) $highest = ShipmentStatus::Announced;
                  break;
              case 'to-sorting':
              case 'at-sorting-centre':
              case 'shipment-on-route':
              case 'driver-on-route':
                  $highest = ShipmentStatus::EnRoute;
                  break;
              case 'delivered': return ShipmentStatus::Delivered;
          }
      }
      return $highest;
  }

  /**
   * @throws SendcloudApiException
   */
  function sendRequest(string $uri, array $query_params = null, bool $post = false, array $postFields = null,
                       array $allowedResponseCodes = [200]): ?array
  {
    if (empty($this->public_key) || empty($this->private_key))
      return null;

    $curl = curl_init();
    if (is_array($query_params)) {
      $uri .= '?' . http_build_query($query_params);
    }
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $uri,
        CURLOPT_HTTPHEADER => [
            "Authorization: Basic " . base64_encode($this->public_key . ':' . $this->private_key),
            'Content-Type: application/json'
        ],
    ]);
    if ($post === true) {
      curl_setopt_array($curl, [
          CURLOPT_POST => true,
          CURLOPT_POSTFIELDS => json_encode($postFields)
      ]);
    }

    $output = json_decode(curl_exec($curl));
    $code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);

    $ret = [
        'code' => $code,
        'body' => $output,
    ];

    if (!in_array($code, $allowedResponseCodes))
      throw SendcloudApiException::fromResponse($ret);

    return $ret;
  }
}