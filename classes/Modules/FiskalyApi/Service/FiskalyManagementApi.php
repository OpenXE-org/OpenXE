<?php


namespace Xentral\Modules\FiskalyApi\Service;


use FiskalyClient\errors\exceptions\FiskalyClientException;
use FiskalyClient\errors\exceptions\FiskalyHttpException;
use FiskalyClient\errors\exceptions\FiskalyHttpTimeoutException;
use Xentral\Modules\FiskalyApi\Data\BillingAddress;
use Xentral\Modules\FiskalyApi\Data\Organisation;
use Xentral\Modules\FiskalyApi\Data\User;

class FiskalyManagementApi extends FiskalyApi
{

    private const ENDPOINT_BASE = 'https://dashboard.fiskaly.com/api/v0/';


    public function __construct(string $smaEndpoint, string $apiKey, string $apiSecret)
    {
        parent::__construct($smaEndpoint, $apiKey, $apiSecret, self::ENDPOINT_BASE);
    }

    /**
     * @param string|null $uuId
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     *
     * @return Organisation[]
     */
    public function getOrganisations(?string $uuId = null): array
    {
        if ($uuId === null) {
            $organisations = $this->callApiGet('organizations');

            return array_map([Organisation::class, 'fromApiResult'], $organisations->data);
        }

        $organisation = $this->callApiGet("organizations/{$uuId}");

        return [Organisation::fromApiResult($organisation)];
    }

    public function getUsers(string $organisationUuId): array
    {
        $result = $this->callApiGet("/organizations/{$organisationUuId}/users");

        return array_map([User::class, 'fromApiResult'], $result->data);
    }

    public function getBillingAddresses(): array
    {
        $result = $this->callApiGet('billing-addresses');

        return array_map([BillingAddress::class, 'fromApiResult'], $result->data);
    }
}
