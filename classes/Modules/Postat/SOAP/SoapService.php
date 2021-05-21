<?php

declare(strict_types=1);

namespace Xentral\Modules\Postat\SOAP;

use Xentral\Modules\Postat\SOAP\Method\GetAllowedServicesForCountry;
use Xentral\Modules\Postat\SOAP\Method\ImportShipment;
use Xentral\Modules\Postat\SOAP\Parameter\Countries;
use Xentral\Modules\Postat\SOAP\Parameter\ShipmentRow;
use Xentral\Modules\Postat\SOAP\Result\AllowedServicesResult;

/**
 * Constructs the individual methods that the SOAP API service exposes.
 */
class SoapService
{
    /** @var PostAtClient */
    private $postAtClient;

    /**
     * Methods constructor.
     *
     * @param PostAtClient $postAtClient
     */
    public function __construct(PostAtClient $postAtClient)
    {
        $this->postAtClient = $postAtClient;
    }

    /**
     * Get available postal services for the given countries.
     *
     * @param array $countries Array of ISO 3166 ALPHA-2 country codes.
     *
     * @throws PostAtException
     *
     * @return AllowedServicesResult
     */
    public function getAllowedServicesForCountry(array $countries): AllowedServicesResult
    {
        $countries = new Countries($countries);
        $soapMethod = new GetAllowedServicesForCountry($this->postAtClient);

        return $soapMethod->call($countries);
    }

    /**
     * Generates a shipping label for the given package and chosen delivery type.
     *
     * The $shipmentRow parameter is an associative array of the delivery details:
     *
     *     [
     *         'row' => [
     *            'PrinterObject' => [
     *                'LabelFormatID' => '100x150',
     *                'LanguageID' => 'pdf',
     *                'PaperLayoutID' => '2xA5inA4'
     *             ],
     *             'Number' => 'XEN-12345,
     *             'DeliveryServiceThirdPartyID' => 28,
     *             etc...
     *         ],
     *    ]
     *
     * See the official Post.at SOAP API documentation for further details.
     *
     * @param array $shipmentRow
     *
     * @throws PostAtException
     *
     * @return mixed The raw SOAP API result containing ImportShipmentResult.
     *               See the official Post.at SOAP API documentation for details.
     */
    public function importShipment(array $shipmentRow)
    {
        $shipmentRow = new ShipmentRow($shipmentRow);
        $soapMethod = new ImportShipment($this->postAtClient);

        return $soapMethod->call($shipmentRow);
    }
}
