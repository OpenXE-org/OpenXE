<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;

use DateTime;
use DateTimeZone;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingApiResponse;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponse;
use Xentral\Modules\FiskalyApi\Exception\InvalidArgumentException;

final class FiskalyCashPointClosingDBService implements FiskalyCashPointClosingDBInterface
{
    /** @var Database $db */
    private $db;

    /**
     * CashPointClosingDBService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param CashPointClosingApiResponse $cashPointClosingApiResponse
     *
     * @throws Exception
     * @return int
     */
    public function create(CashPointClosingApiResponse $cashPointClosingApiResponse): int
    {
        $this->db->perform(
            'INSERT INTO `fiskaly_cash_point_closing` 
            (`closing_id`, `client_id`, `cash_point_closing_export_id`, `state`, 
             `export_creation_date`, `time_start`, `time_end`, `trx_start`, `trx_end`)   
            VALUES (:closing_id, :client_id, :cash_point_closing_export_id, :state,
                   :export_creation_date, :time_start, :time_end, :trx_start, :trx_end )',
            [
                'closing_id' => $cashPointClosingApiResponse->getClosingId(),
                'client_id' => $cashPointClosingApiResponse->getClientId(),
                'cash_point_closing_export_id' => $cashPointClosingApiResponse->getCashPointClosingExportId(),
                'state' => $cashPointClosingApiResponse->getState(),
                'export_creation_date' => (new Datetime('now', new DateTimeZone('UTC')))->setTimeStamp(
                    $cashPointClosingApiResponse->getExportCreationDate()
                )->format('Y-m-d H:i:s'),
                'time_start' => null,
                'time_end' => null,
                'trx_start' => $cashPointClosingApiResponse->getFirstTransactionExportId(),
                'trx_end' => $cashPointClosingApiResponse->getLastTransactionExportId(),
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param string $closingId
     *
     * @return CashPointClosingApiResponse|null
     */
    public function getByClosingId(string $closingId): ?CashPointClosingApiResponse
    {
        $id = $this->getIdByClosingId($closingId);
        if ($id === null) {
            return null;
        }

        return $this->get($id);
    }

    /**
     * @param int $id
     *
     * @return CashPointClosingApiResponse|null
     */
    public function get(int $id): ?CashPointClosingApiResponse
    {
        $row = $this->db->fetchRow(
            'SELECT * FROM `fiskaly_cash_point_closing` WHERE `id` = :id',
            ['id' => $id]
        );
        if (empty($row)) {
            return null;
        }

        return CashPointClosingApiResponse::fromDbState($row);
    }

    /**
     * @param string $closingId
     *
     * @return int|null
     */
    public function getIdByClosingId(string $closingId): ?int
    {
        $id = $this->db->fetchValue(
            'SELECT `id` FROM `fiskaly_cash_point_closing` WHERE `closing_id` = :closing_id',
            ['closing_id' => $closingId]
        );
        if ($id === false) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @param CashPointClosingApiResponse $cashPointClosing
     * @param TransactionReponse          $transaction
     *
     * @return int
     */
    public function createTransactionMapping(
        CashPointClosingApiResponse $cashPointClosing,
        TransactionReponse $transaction
    ): int {
        $cashPointClosingId = $this->getIdByClosingId($cashPointClosing->getClosingId());
        if ($cashPointClosingId === null) {
            throw new InvalidArgumentException("cashPointClosingId {$cashPointClosing->getClosingId()} not found");
        }
        $transactionId = $transaction->getId();
        $transactionDbId = $this->getTransactionDbId($transactionId);
        if ($transactionDbId === null) {
            throw new InvalidArgumentException("Transaction {$transactionId} not found");
        }
        $this->db->perform(
            'INSERT INTO `fiskaly_cash_point_closing_transaction` 
            (`fiskaly_cash_point_closing_id`, `fiskaly_transaction_id`) 
            VALUES (:cash_point_closing_id, :transaction_id)',
            [
                'cash_point_closing_id' => $cashPointClosingId,
                'transaction_id'        => $transactionDbId,
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param CashPointClosingApiResponse $cashPointClosingApiResponse
     */
    public function update(CashPointClosingApiResponse $cashPointClosingApiResponse): void
    {
        $cashPointClosingId = $this->getIdByClosingId($cashPointClosingApiResponse->getClosingId());
        if ($cashPointClosingId === null) {
            throw new InvalidArgumentException(
                "cashPointClosingId {$cashPointClosingApiResponse->getClosingId()} not found"
            );
        }

        $this->db->perform(
            'UPDATE `fiskaly_cash_point_closing` SET `state` = :state WHERE `id` = :id',
            ['state' => $cashPointClosingApiResponse->getState(), 'id' => $cashPointClosingId]
        );
    }

    /**
     * @param string $clientId
     * @param string $state
     *
     * @return array
     */
    public function getClosingIdsByState(string $clientId, string $state): array
    {
        return $this->db->fetchCol(
            'SELECT `closing_id` FROM `fiskaly_cash_point_closing` WHERE `client_id` = :client_id AND `state` = :state',
            [
                'client_id' => $clientId,
                'state'     => $state,
            ]
        );
    }

    /**
     * @param string $transactionId
     *
     * @return int|null
     */
    private function getTransactionDbId(string $transactionId): ?int
    {
        $result = $this->db->fetchValue(
            'SELECT `id` FROM `fiskaly_transaction` WHERE `trx_id` = :trx_id',
            ['trx_id' => $transactionId]
        );
        if ($result === false) {
            return null;
        }

        return (int)$result;
    }
}
