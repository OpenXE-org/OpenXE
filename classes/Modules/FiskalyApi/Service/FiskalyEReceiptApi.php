<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;

use Exception;
use FiskalyClient\errors\exceptions\FiskalyClientException;
use FiskalyClient\errors\exceptions\FiskalyHttpException;
use FiskalyClient\errors\exceptions\FiskalyHttpTimeoutException;

class FiskalyEReceiptApi extends FiskalyApi
{
    /** @var string */
    private const ENDPOINT_BASE = 'https://ereceipt.fiskaly.dev/api/v0/';

    /**
     * FiskalyEReceiptApi constructor.
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
     * @param int         $limit
     * @param int         $offset
     * @param string|null $tssId
     *
     * @throws FiskalyClientException
     * @throws FiskalyHttpException
     * @throws FiskalyHttpTimeoutException
     * @return mixed
     */
    public function listEReceipts(int $limit = 100, int $offset = 0, ?string $tssId = null)
    {
        if($tssId === null) {
            $result = $this->callApiGet("issuer/e_receipts");
            //$result = $this->callApiGet("issuer/e_receipts?limit={$limit}&offset={$offset}");
        }
        else {
            $result = $this->callApiGet("issuer/e_receipts?limit={$limit}&offset={$offset}&tss_id={$tssId}");
        }

        return $result;
    }
}
