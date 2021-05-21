<?php

declare(strict_types=1);

namespace Xentral\Modules\Postat\SOAP\Method;

use Xentral\Modules\Postat\SOAP\MethodInterface;
use Xentral\Modules\Postat\SOAP\ParameterInterface;
use Xentral\Modules\Postat\SOAP\PostAtClient;
use Xentral\Modules\Postat\SOAP\PostAtException;
use Xentral\Modules\Postat\SOAP\Result\AllowedServicesResult;

class GetAllowedServicesForCountry implements MethodInterface
{
    /** @var PostAtClient */
    private $postAtClient;

    /** @var array Array of ISO 3166 ALPHA-2 country codes. */
    private $countries;

    /**
     * AllowedServicesForCountry constructor.
     *
     * @param PostAtClient $postAtClient
     */
    public function __construct(PostAtClient $postAtClient)
    {
        $this->postAtClient = $postAtClient;
    }

    /**
     * Call the API endpoint.
     *
     * @param ParameterInterface $countries
     *
     * @return AllowedServicesResult
     * @throws PostAtException
     */
    public function call(ParameterInterface $countries)
    {
        $this->countries = $countries->getData();

        $response = $this->postAtClient->call($this);

        return new AllowedServicesResult($response);
    }

    /**
     * Get content for the SOAP body element.
     *
     * @return array[]
     */
    public function getBody(): array
    {
        return [
            'countryList' => $this->countries,
        ];
    }
}
