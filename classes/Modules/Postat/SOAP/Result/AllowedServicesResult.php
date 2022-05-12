<?php

declare(strict_types=1);

namespace Xentral\Modules\Postat\SOAP\Result;

class AllowedServicesResult
{
    /** @var The raw data received from the SOAP API. */
    private $data;

    /**
     * AllowedServicesResult constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get two-level associative array of service codes and the service names.
     *
     * Can be used for example to generate options for a <select> element.
     *
     * [
     *     45 => [
     *         'name' => 'Premium Int. Outbound B2B',
     *         'features' => [
     *             '007' => '24-Stundenpaket',
     *             '022' => 'Nachnahme COD International',
     *             '024' => 'Zerbrechlich international',
     *             '063' => 'Höherversicherung',
     *             '065' => 'Postlagernd',
     *             '074' => 'Gefahrgut - begrenzte Menge (LQ)',
     *          ],
     *    ],
     *    46 => [
     *         'name' => 'Post Express International',
     *         'features' => [],
     *     ],
     *     etc...
     * ]
     *
     * @return array $options
     */
    public function toOptionsArray(): array
    {
        $services = $this->data->GetAllowedServicesForCountryResult->CarrierServiceRow;

        $options = [];
        foreach ($services as $service) {
            $features = [];
            foreach ($service->FeatureList->AdditionalInformationResult as $test) {
                $features[$test->ThirdPartyID] = $test->Name;
            }

            $options[$service->ThirdPartyID] = [
                'name' => $service->Name,
                'features' => $features,
            ];
        }

        return $options;
    }
}
