<?php

namespace Xentral\Carrier\SendCloud;

use Exception;
use Xentral\Carrier\SendCloud\Data\Document;
use Xentral\Carrier\SendCloud\Data\ParcelCreation;
use Xentral\Carrier\SendCloud\Data\ParcelResponse;
use Xentral\Carrier\SendCloud\Data\SenderAddress;
use Xentral\Carrier\SendCloud\Data\ShippingProduct;

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

  public function GetSenderAddresses(): array
  {
    $uri = self::PROD_BASE_URI . '/user/addresses/sender';
    $response = $this->sendRequest($uri);
    $res = array();
    foreach ($response->sender_addresses as $item)
      $res[] = SenderAddress::fromApiResponse($item);
    return $res;
  }

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
    return array_map(fn($x) => ShippingProduct::fromApiResponse($x), $response ?? []);
  }

  /**
   * @throws Exception
   */
  public function CreateParcel(ParcelCreation $parcel): ParcelResponse|string|null
  {
    $uri = self::PROD_BASE_URI . '/parcels';
    $response = $this->sendRequest($uri, null, true, [
        'parcel' => $parcel->toApiRequest()
    ]);
    if (isset($response->parcel))
      return ParcelResponse::fromApiResponse($response->parcel);
    if (isset($response->error))
      return $response->error->message;
    return null;
  }

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
    return curl_exec($curl);
  }

  function sendRequest(string $uri, array $query_params = null, bool $post = false, array $postFields = null)
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

    $output = curl_exec($curl);
    curl_close($curl);

    return json_decode($output);
  }
}