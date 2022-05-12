<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;

use Datetime;
use DateTimeZone;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\FiskalyApi\Data\Export;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponse;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponseCollection;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionRequest;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;
use Xentral\Modules\FiskalyApi\Exception\InvalidTransactionException;

final class FiskalyTransactionPosSessionService implements FiskalyTransactionPosSessionInterface
{
    /** @var Database $db */
    private $db;

    /**
     * FiskalyTransactionPosSessionService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $trxId
     *
     * @return array|null
     */
    public function get(string $trxId): ?array
    {
        return $this->db->fetchRow(
            'SELECT * FROM `fiskaly_transaction` WHERE `trx_id` = :trx_id',
            ['trx_id' => $trxId]
        );
    }

    /**
     * @param TransactionReponseCollection $transactionResponseCollection
     */
    public function insertTransactions(TransactionReponseCollection $transactionResponseCollection): void
    {
        foreach ($transactionResponseCollection as $transactionResponse) {
            $trxId = $transactionResponse->getId();
            if (!empty($this->get($trxId))) {
                continue;
            }
            $this->create(null, $transactionResponse);
        }
    }

    /**
     * @param string $trxId
     *
     * @return int|null
     */
    public function getTransactionIdFromTrxId(string $trxId): ?int
    {
        $fiskalyTransactionId = $this->db->fetchValue(
            'SELECT `id` FROM `fiskaly_transaction` WHERE `trx_id` = :trx_id',
            [
                'trx_id' => $trxId,
            ]
        );

        return $fiskalyTransactionId === false ? null : (int)$fiskalyTransactionId;
    }

    /**
     * @param string $document
     * @param int    $documentId
     *
     * @return array
     */
    public function getTransactionFromDocument(string $document, int $documentId): array
    {
        return $this->db->fetchRow(
            'SELECT ft.*
            FROM `fiskaly_transaction` AS `ft`
            INNER JOIN `fiskaly_tranaction_mapping` AS `ftm` ON ft.id = ftm.fiskaly_transaction_id
            WHERE ftm.document = :document AND ftm.document_id = :document_id',
            [
                'document'               => $document,
                'document_id'            => $documentId,
            ]
        );
    }

    /**
     * @param string $trxId
     * @param string $document
     * @param int    $documentId
     *
     * @return int
     */
    public function tryMapDocument(string $trxId, string $document, int $documentId): int
    {
        $fiskalyTransactionId = $this->getTransactionIdFromTrxId($trxId);
        $mappingId = $fiskalyTransactionId === null ? false : $this->db->fetchValue(
            'SELECT ftm.id
            FROM `fiskaly_tranaction_mapping` AS `ftm`
            WHERE ftm.fiskaly_transaction_id = :fiskaly_transaction_id 
              AND ftm.document = :document 
              AND ftm.document_id = :document_id',
            [
                'fiskaly_transaction_id' => $fiskalyTransactionId,
                'document'               => $document,
                'document_id'            => $documentId,
            ]
        );
        if ($mappingId !== false) {
            return (int)$mappingId;
        }
        $this->db->perform(
            'INSERT INTO `fiskaly_tranaction_mapping` (`fiskaly_transaction_id`, `document`, `document_id`)
            VALUES (:fiskaly_transaction_id, :document, :document_id)',
            [
                'fiskaly_transaction_id' => $fiskalyTransactionId,
                'document'               => $document,
                'document_id'            => $documentId,
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param TransactionRequest|null $request
     * @param TransactionReponse|null $response
     *
     * @return int
     */
    public function create(
        ?TransactionRequest $request,
        ?TransactionReponse $response
    ): int {
        if ($request === null && $response === null) {
            throw new InvalidArgumentException('response or request required');
        }
        $trxId = $request === null ? $response->getId() : $request->getId();
        if (!empty($this->get($trxId))) {
            throw new InvalidTransactionException('Transaction already exists');
        }
        $this->db->perform(
            'INSERT INTO `fiskaly_transaction` 
            (`tss_id`, `client_id`, `trx_id`, `state`,
             `time_start`, `time_end`, `json_request`, `json_response`) 
             VALUES (:tss_id, :client_id, :trx_id, :state, 
                     NULL, NULL, :json_request, :json_response)',
            [
                'tss_id'        => $request === null ? $response->getTssId() : $request->getTssId(),
                'client_id'     => $request === null ? $response->getClientId() : $request->getClientId(),
                'trx_id'        => $trxId,
                'state'         => $response === null ? null : $response->getState(),
                'json_request'  => $request === null ? null : json_encode($request->toArray()),
                'json_response' => $response === null ? null : json_encode($response->toArray()),
            ]
        );

        $fiskalyTransactionId = $this->db->lastInsertId();
        if ($response === null) {
            return $fiskalyTransactionId;
        }
        if ($response->getTimeStart() !== null) {
            $this->db->perform(
                'UPDATE `fiskaly_transaction` 
                SET `time_start` = FROM_UNIXTIME(:time_start)
                WHERE `id` = :id',
                [
                    'time_start' => $response->getTimeStart()->getTimestamp(),
                    'id'         => $fiskalyTransactionId,
                ]
            );
        }
        if ($response->getTimeEnd() !== null) {
            $this->db->perform(
                'UPDATE `fiskaly_transaction` 
                SET `time_end` = FROM_UNIXTIME(:time_end)
                WHERE `id` = :id',
                [
                    'time_end' => $response->getTimeEnd()->getTimestamp(),
                    'id'       => $fiskalyTransactionId,
                ]
            );
        }

        return $fiskalyTransactionId;
    }

    /**
     * @param int                $fiskalyTransactionPosSessionId
     * @param TransactionRequest $request
     * @param TransactionReponse $response
     */
    public function update(
        int $fiskalyTransactionPosSessionId,
        TransactionRequest $request,
        TransactionReponse $response
    ): void {
        if ($response->getTimeEnd() === null) {
            $this->db->perform(
                'UPDATE `fiskaly_transaction` 
                SET `state` = :state,
                    `time_end` = NULL,
                    `json_request` = :json_request,
                    `json_response` = :json_response
                WHERE `id` = :id',
                [
                    'state'         => $response->getState(),
                    'json_request'  => json_encode($request->toApiResult()),
                    'json_response' => json_encode($response->toApiResult()),
                    'id'            => $fiskalyTransactionPosSessionId,
                ]
            );

            return;
        }
        $this->db->perform(
            'UPDATE `fiskaly_transaction` 
            SET `state` = :state,
                `time_end` = FROM_UNIXTIME(:time_end),
                `json_request` = :json_request,
                `json_response` = :json_response
            WHERE `id` = :id',
            [
                'state'         => $response->getState(),
                'time_end'      => $response->getTimeEnd()->getTimestamp(),
                'json_request'  => json_encode($request->toApiResult()),
                'json_response' => json_encode($response->toApiResult()),
                'id'            => $fiskalyTransactionPosSessionId,
            ]
        );
    }

    /**
     * @param Export $export
     *
     * @throws Exception
     */
    public function createOrUpdateExport(Export $export): void
    {
        if ($this->getExportIdFromUuid($export->getUuId()) === null) {
            $this->createExport($export);

            return;
        }
        $this->updateExport($export);
    }

    /**
     * @param Export $export
     *
     * @throws Exception
     * @return int
     */
    public function createExport(Export $export): int
    {
        $this->db->perform(
            'INSERT INTO `fiskaly_kassensichv_export` 
            (`uuid`, `type`, `env`, `tssid`, `state`, `href`, `time_request`, `time_start`, `time_end`) 
            VALUES (:uuid, :type, :env, :tssid, :state, :href, :time_request, :time_start, :time_end)',
            [
                'uuid'         => $export->getUuId(),
                'type'         => $export->getType(),
                'env'          => $export->getEnv(),
                'tssid'        => $export->getTssId(),
                'state'        => $export->getState(),
                'href'         => $export->getHref(),
                'time_request' => $export->getTimeRequest() === null ? null : (new Datetime(
                    'now',
                    new DateTimeZone('UTC')
                ))->setTimeStamp(
                    $export->getTimeRequest()
                )->format('Y-m-d H:i:s'),
                'time_start'   => $export->getTimeStart() === null ? null : (new Datetime(
                    'now', new DateTimeZone('UTC')
                ))->setTimeStamp($export->getTimeStart())
                    ->format('Y-m-d H:i:s'),
                'time_end'     => $export->getTimeEnd() === null ? null : (new Datetime(
                    'now', new DateTimeZone('UTC')
                ))->setTimeStamp($export->getTimeEnd())
                    ->format('Y-m-d H:i:s'),
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param Export $export
     *
     * @throws Exception
     */
    public function updateExport(Export $export): void
    {
        $this->db->perform(
            'UPDATE `fiskaly_kassensichv_export` 
            SET `state` = :state,
                `href` = :href,
                `time_request` = :time_request,
                `time_start` = :time_start,
                `time_end` = :time_end
            WHERE `uuid` = :uuid',
            [
                'uuid'         => $export->getUuId(),
                'state'        => $export->getState(),
                'href'         => $export->getHref(),
                'time_request' => (new Datetime('now', new DateTimeZone('UTC')))->setTimeStamp(
                    $export->getTimeRequest()
                )->format('Y-m-d H:i:s'),
                'time_start'   => (new Datetime('now', new DateTimeZone('UTC')))->setTimeStamp($export->getTimeStart())
                    ->format('Y-m-d H:i:s'),
                'time_end'     => (new Datetime('now', new DateTimeZone('UTC')))->setTimeStamp($export->getTimeEnd())
                    ->format('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * @param string $tssId
     *
     * @return array
     */
    public function getExportUrlsNotInDms(string $tssId): array
    {
        return $this->db->fetchAll(
            "SELECT fke.id, fke.href, fke.uuid 
            FROM `fiskaly_kassensichv_export` AS `fke`
            LEFT JOIN `datei_stichwoerter` AS `ds` ON fke.id = ds.parameter AND ds.objekt = 'fiskaly_kassensichv_export'
            WHERE `fke`.state = 'COMPLETED' AND fke.tssid = :tssid AND ds.id IS NULL",
            ['tssid' => $tssId]
        );
    }

    /**
     * @param string      $state
     * @param string|null $tssId
     *
     * @return array
     */
    public function getUuIdsByState(string $state, ?string $tssId = null): array
    {
        if ($tssId === null) {
            return $this->db->fetchCol(
                'SELECT `uuid` FROM `fiskaly_kassensichv_export` WHERE `state` = :state',
                [
                    'state' => $state,
                ]
            );
        }

        return $this->db->fetchCol(
            'SELECT `uuid` FROM `fiskaly_kassensichv_export` WHERE `tssid` = :tssid AND `state` = :state',
            [
                'tssid' => $tssId,
                'state' => $state,
            ]
        );
    }

    /**
     * @param string $uuid
     *
     * @return int|null
     */
    private function getExportIdFromUuid(string $uuid): ?int
    {
        $id = $this->db->fetchValue(
            'SELECT `id` FROM `fiskaly_kassensichv_export` WHERE `uuid` = :uuid',
            ['uuid' => $uuid]
        );

        return $id === false ? null : (int)$id;
    }
}
