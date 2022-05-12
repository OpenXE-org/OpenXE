<?php

declare(strict_types=1);

namespace Xentral\Modules\Postat\SOAP;

use Xentral\Components\Logger\Logger;

class PostAtClient
{
    /** @var */
    private $apiConfig;

    /** @var Logger */
    private $logger;

    /**
     * PostAtClient constructor.
     *
     * @param $apiConfig
     * @param Logger $logger
     */
    public function __construct(Config $apiConfig, Logger $logger)
    {
        $this->apiConfig = $apiConfig;
        $this->logger = $logger;
    }

    /**
     * Make a SOAP API call.
     *
     * @param MethodInterface $method
     *
     * @throws PostAtException
     *
     * @return mixed
     */
    public function call(MethodInterface $method)
    {
        $body = $method->getBody();

        $body = $this->appendConfiguration($body);

        try {
            $client = new \SoapClient($this->apiConfig->getSoapUrl());
        } catch (\SoapFault $exception) {
            $this->logger->error('Cannot connect to the Post.at SOAP API.', ['exception' => $exception]);

            // The exception thrown above would be too technical for the end
            // user, so use a more user-friendly message instead.
            throw new PostAtException(
                'Configuration of the Post.at module is invalid. Please verify the configuration.'
            );
        }

        $response = $client->__soapCall($this->getClassName($method), [$body]);

        if ($response->errorCode !== null) {
            // A user-friendly error such as "This package type is not available for the chosen target country".
            throw new PostAtException("{$response->errorCode} - {$response->errorMessage}");
        }

        return $response;
    }

    /**
     * Appends SOAP body entries that are required for every request.
     *
     *  - clientID: DebitorID
     *  - orgUnitID: OrganisationID; unique for a customerID
     *  - orgUnitGuid: Unique GUID of the customerID
     *
     * For some reason the location of these entries is different within the SOAP
     * body structure depending on which API method is used. This function attempts
     * to cover all use cases.
     *
     * @param array $body
     *
     * @return array $body
     */
    private function appendConfiguration(array $body): array
    {
        if (isset($body['row'])) {
            $body['row']['ClientID'] = $this->apiConfig->getClientId();
            $body['row']['OrgUnitID'] = $this->apiConfig->getOrgUnitId();
            $body['row']['OrgUnitGuid'] = $this->apiConfig->getOrgUnitGuid();

            return $body;
        }

        if (isset($body['addresses'])) {
            // TODO Add this once one of the address API methods gets implemented.
        }

        // In simple requests the entries are at the root of the body.
        $body['clientID'] = $this->apiConfig->getClientId();
        $body['orgUnitID'] = $this->apiConfig->getOrgUnitId();
        $body['orgUnitGuid'] = $this->apiConfig->getOrgUnitGuid();

        return $body;
    }

    /**
     * Gets the short name of the class (the part without the namespace).
     *
     * @param $method
     *
     * @throws \ReflectionException
     *
     * @return string
     */
    private function getClassName($method): string
    {
        return (new \ReflectionClass($method))->getShortName();
    }
}
