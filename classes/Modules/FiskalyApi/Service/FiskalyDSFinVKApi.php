<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;


use Exception;
use FiskalyClient\errors\exceptions\FiskalyClientException;
use FiskalyClient\errors\exceptions\FiskalyHttpException;
use FiskalyClient\errors\exceptions\FiskalyHttpTimeoutException;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosing;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingApiResponse;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingApiResponseCollection;
use Xentral\Modules\FiskalyApi\Data\CashRegister;
use Xentral\Modules\FiskalyApi\Data\VatDefinition;

class FiskalyDSFinVKApi extends FiskalyApi
{
    /** @var string */
    private const ENDPOINT_BASE = 'https://dsfinvk.fiskaly.com/api/v0/';

    /**
     * FiskalyDSFinVKApi constructor.
     *
     * @param string $smaEndpoint
     * @param string $apiKey
     * @param string $apiSecret
     *
     * @throws Exception
     */
    public function __construct(string $smaEndpoint, string $apiKey, string $apiSecret)
    {
        parent::__construct($smaEndpoint, $apiKey, $apiSecret, self::ENDPOINT_BASE);
    }

    /**
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return array
     */
    public function getVatDefinitions(): array
    {
        $result = $this->callApiGet('vat_definitions');

        return array_map([VatDefinition::class, 'fromApiResult'], $result->data);
    }

    /**
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return array
     */
    public function getCashRegisters(): array
    {
        $result = $this->callApiGet('cash_registers');
        return array_map([CashRegister::class,'fromApiResult'], $result->data);
    }

    /**
     * @param string $clientId
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return CashRegister|null
     */
    public function getCashRegister(string $clientId): ?CashRegister
    {
        $result = $this->callApiGet("cash_registers/{$clientId}");
        if(empty($result)) {
            return null;
        }

        return CashRegister::fromApiResult($result);
    }

    /**
     * @param string|null $purchaserAgencyId
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     *
     * @return CashRegister[]
     */
    public function getPurchaserAgencies(?string $purchaserAgencyId = null): array
    {
        if($purchaserAgencyId === null) {
            $result = $this->callApiGet('purchaser_agencies');

            return array_map([CashRegister::class, 'fromApiResult'] , $result->data);
        }

        $result = $this->callApiGet("purchaser_agencies/{$purchaserAgencyId}");

        return [
            CashRegister::fromApiResult($result)
        ];
    }

    /**
     * @param CashRegister $cashRegister
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return CashRegister
     */
    public function putRegister(CashRegister $cashRegister): CashRegister
    {
        $clientId = $cashRegister->getClientId();
        $body = $cashRegister->toArray();
        $result = $this->callApiPut("cash_registers/{$clientId}", json_encode($body));

        return CashRegister::fromApiResult($result);
    }

    /**
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     *
     * @return mixed
     */
    public function getCashPointClosings(): CashPointClosingApiResponseCollection
    {
       $result = $this->callApiGet('cash_point_closings');

       return CashPointClosingApiResponseCollection::fromApiResult($result->data);
    }

    /**
     * @param string $closingId
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return CashPointClosingApiResponse
     */
    public function getCashPointClosing(string $closingId): CashPointClosingApiResponse
    {
        $result = $this->callApiGet("cash_point_closings/{$closingId}");

        return CashPointClosingApiResponse::fromApiResult($result);
    }

    /**
     * @param string $closingId
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return mixed
     */
    public function getCashPointClosingDetails(string $closingId) {
        $result = $this->callApiGet("cash_point_closings/{$closingId}/details");

        return $result;
    }

    /**
     * @param CashPointClosing $cashPointClosing
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     *
     * @return CashPointClosingApiResponse
     */
    public function sendCashPointClosings(CashPointClosing $cashPointClosing): CashPointClosingApiResponse
    {
        $result = $this->callApiPut(
            "cash_point_closings/{$cashPointClosing->getClosingId()}", json_encode($cashPointClosing->toApiResult())
        );

        return CashPointClosingApiResponse::fromApiResult($result);
    }
}
