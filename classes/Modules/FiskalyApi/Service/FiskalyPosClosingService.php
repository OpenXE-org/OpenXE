<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;

use erpAPI;
use Exception;
use DateTime;
use DateTimeZone;
use Xentral\Components\Database\Database;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\BusinessCaseCollection;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosing;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingCashStatement;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingHead;
use Xentral\Modules\FiskalyApi\Data\CashPointClosing\CashPointClosingPayment;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponse;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionReponseCollection;
use Xentral\Modules\FiskalyApi\Factory\FiskalyCashPointClosingFactory;
use Xentral\Modules\FiskalyApi\Wrapper\TaxSettingWrapper;

class FiskalyPosClosingService implements FiskalyPosClosingInterface
{
    /** @var Database $db */
    private $db;

    /** @var FiskalyCashPointClosingFactory $cashPointFactory */
    private $cashPointFactory;

    /** @var TaxSettingWrapper $taxSettingWrapper */
    private $taxSettingWrapper;

    /**
     * FiskalyPosClosingService constructor.
     *
     * @param Database                       $db
     * @param FiskalyCashPointClosingFactory $cashPointFactory
     * @param TaxSettingWrapper              $taxSettingWrapper
     */
    public function __construct(
        Database $db,
        FiskalyCashPointClosingFactory $cashPointFactory,
        TaxSettingWrapper $taxSettingWrapper
    ) {
        $this->db = $db;
        $this->cashPointFactory = $cashPointFactory;
        $this->taxSettingWrapper = $taxSettingWrapper;
    }

    public function getNextCashPointClosingExportId(string $clientId): int
    {
        return 1 + (int)$this->db->fetchValue(
                'SELECT  MAX(`cash_point_closing_export_id`) 
                FROM `fiskaly_cash_point_closing`
                WHERE `client_id` = :client_id',
                ['client_id' => $clientId]
            );
    }

    /**
     * @param string      $clientId
     * @param string|null $date
     *
     * @throws Exception
     * @return TransactionReponseCollection
     */
    public function getOpenTransactions(string $clientId, ?string $date = null): TransactionReponseCollection
    {
        $transactions = $this->db->fetchCol(
            'SELECT ft.json_response
            FROM `fiskaly_transaction` AS `ft`
            INNER JOIN `fiskaly_tranaction_mapping` AS `ftm` ON ft.id = ftm.fiskaly_transaction_id
            LEFT JOIN `fiskaly_cash_point_closing_transaction` AS `fcpct` ON ft.id = fcpct.fiskaly_transaction_id
            WHERE ft.client_id = :client_id AND fcpct.id IS NULL
            GROUP BY ft.id',
            ['client_id' => $clientId]
        );
        $instance = new TransactionReponseCollection();
        foreach ($transactions as $transactionJson) {
            $instance->addTransactionResponse(TransactionReponse::fromApiResult(json_decode($transactionJson, false)));
        }
        if ($date !== null) {
            return $instance->filterDate($date);
        }

        return $instance;
    }

    /**
     * @param string $clientId
     *
     * @throws Exception
     * @return array
     */
    public function getOpenPointClosingDates(string $clientId): array
    {
        $openTransactions = $this->getOpenTransactions($clientId);

        return $openTransactions->getTransactionDates();
    }

    /**
     * @param int    $projectId
     * @param string $date
     *
     * @return bool
     */
    public function isPosCountingExistsForDate(int $projectId, string $date): bool
    {
        return $this->db->fetchValue(
                'SELECT `id`
            FROM `pos_zaehlungen`
            WHERE `projekt` = :project_id AND DATE(`zeitstempel`) = :date
            LIMIT 1',
                [
                    'project_id' => $projectId,
                    'date'       => $date,
                ]
            ) !== false;
    }

    /**
     * @param string      $clientId
     * @param string|null $date
     *
     * @throws Exception
     * @return CashPointClosing
     */
    public function getNextCashPointClosing(string $clientId, ?string $date = null): CashPointClosing
    {
        if ($date === null) {
            $openTransactions = $this->getOpenTransactions(
                $clientId,
                (new DateTime('now', new DateTimeZone('UTC')))->format(
                    'Y-m-d'
                )
            );
        } else {
            $openTransactions = $this->getOpenTransactions($clientId, $date);
        }
        $trxs = $openTransactions->getTrxIds();
        $firstTransaction = $openTransactions->getBoundedTransactionWithClientId($clientId, true);
        $lastLastTransaction = $openTransactions->getBoundedTransactionWithClientId($clientId, false);
        $this->cashPointFactory->setTaxNormal($this->getNormalTaxForClientId($clientId));
        $paymentCollection = $this->cashPointFactory->getPaymentTypesFromTransactionCollection($openTransactions);
        $sum = $paymentCollection->getSum();
        $cashCollection = $paymentCollection->filterByType('CASH');
        $cashSum = $cashCollection->getSum();

        $posJournalEntries = $this->getPosJounralEntriesByTrxs($trxs);
        $posSessions = $this->getPosSessionsByTrxs($trxs);
        $businessCases = new BusinessCaseCollection();
        foreach ($posJournalEntries as $posJournalArray) {
            $businessCases = $businessCases->combine(
                $this->cashPointFactory->createBusinessCaseCollection($posJournalArray)
            );
        }
        $payment = new CashPointClosingPayment(
            $sum,
            $cashSum,
            $this->cashPointFactory->getCashAmountByCurrencyCollection($paymentCollection),
            $this->cashPointFactory->getCashPointClosingPaymentTypeCollectionByPosJournalCollection($posJournalEntries)
        );

        $instance = new CashPointClosing(
            $clientId, $this->getNextCashPointClosingExportId($clientId),
            new CashPointClosingHead(
                new DateTime('now', new DateTimeZone('UTC')),
                $firstTransaction->getId(),
                $lastLastTransaction->getId(),
                $date === null ? null : (new DateTime($date, new DateTimeZone('UTC')))
            ),
            new CashPointClosingCashStatement($businessCases, $payment),
            $this->cashPointFactory->getCashPointClosingTransactionCollection(
                $openTransactions,
                $posJournalEntries,
                $posSessions
            )
        );


        return $instance;
    }

    /**
     * @param array $trxs
     *
     * @return array
     */
    private function getPosJounralEntriesByTrxs(array $trxs): array
    {
        return $this->db->fetchGroup(
            "SELECT ft.trx_id, pj.*
            FROM `fiskaly_transaction` AS `ft`
            INNER JOIN `fiskaly_tranaction_mapping` AS `ftm` ON ft.id = ftm.fiskaly_transaction_id
            INNER JOIN `pos_journal` AS `pj` ON ftm.document = 'pos_journal' AND ftm.document_id = pj.id
            WHERE ft.trx_id IN (:trx_ids)",
            [
                'trx_ids' => $trxs,
            ]
        );
    }

    /**
     * @param array $trxs
     *
     * @return array
     */
    private function getPosSessionsByTrxs(array $trxs): array
    {
        $posSessions = $this->db->fetchPairs(
            "SELECT ft.trx_id, ps.data
            FROM `fiskaly_transaction` AS `ft`
            INNER JOIN `fiskaly_tranaction_mapping` AS `ftm` ON ft.id = ftm.fiskaly_transaction_id
            INNER JOIN `pos_sessions` AS `ps` ON ftm.document = 'pos_session' AND ftm.document_id = ps.id
            WHERE ft.trx_id IN (:trx_ids)",
            [
                'trx_ids' => $trxs,
            ]
        );

        $posSessions = array_map(
            static function ($posSession) {
                return unserialize($posSession, ['allowed_classes' => false]);
            },
            $posSessions
        );
        $addressIds = [];
        $cashierIds = [];
        foreach ($posSessions as $posSession) {
            $cashierId = $posSession['kassiererId'] ?? null;
            $addressId = $posSession['addrid'] ?? null;
            if ($addressId !== null && !in_array($addressId, $addressIds, true)) {
                $addressIds[] = $addressId;
            }
            if ($cashierId !== null && !in_array($cashierId, $cashierIds, true)) {
                $cashierIds[] = $cashierId;
            }
        }
        $cashierAddresses = $this->getAddressesFromCashierIds($cashierIds);

        $addesses = $this->getAddressesFromIds($addressIds);
        foreach ($posSessions as $positionKey => $posSession) {
            $posSessions[$positionKey]['address'] = !empty($addesses[$posSession['addrid'] ?? '']) ? reset(
                $addesses[$posSession['addrid']]
            ) : null;
        }
        foreach ($posSessions as $positionKey => $posSession) {
            if (!empty($posSessions[$positionKey]['address']) || empty($posSession['kassiererId'])) {
                continue;
            }
            if (empty($cashierAddresses[$posSession['kassiererId']])) {
                continue;
            }
            $posSessions[$positionKey]['address'] = reset($cashierAddresses[$posSession['kassiererId']]);
            $posSessions[$positionKey]['addrid'] = $posSessions[$positionKey]['address']['id'];
            $posSessions[$positionKey]['addr']['name'] = $posSessions[$positionKey]['address']['name'];
        }

        return $posSessions;
    }

    /**
     * @param array $addressIds
     *
     * @return array
     */
    private function getAddressesFromIds(array $addressIds): array
    {
        if (empty($addressIds)) {
            return [];
        }

        return $this->db->fetchGroup(
            "SELECT adr.id, IF(l.iso3 IS NULL OR l.iso3 = '', IF(l.iso = 'AT', 'AUT', 'DEU'), l.iso3) AS `land_iso3`,
                adr.* 
            FROM `adresse` AS `adr`
            LEFT JOIN `laender` AS `l` ON adr.land = l.iso
            WHERE adr.id IN (:address_ids)",
            ['address_ids' => $addressIds]
        );
    }

    /**
     * @param array $cashierIds
     *
     * @return array
     */
    private function getAddressesFromCashierIds(array $cashierIds): array
    {
        if (empty($cashierIds)) {
            return [];
        }

        return $this->db->fetchGroup(
            "SELECT pk.kassenkennung,
                IF(l.iso3 IS NULL OR l.iso3 = '', IF(l.iso = 'AT', 'AUT', 'DEU'), l.iso3) AS `land_iso3`, adr.* 
            FROM `adresse` AS `adr`
            INNER JOIN `pos_kassierer` AS `pk` ON adr.id = pk.adresse
            LEFT JOIN `laender` AS `l` ON adr.land = l.iso
            WHERE pk.kassenkennung IN (:cashier_ids)",
            ['cashier_ids' => $cashierIds]
        );
    }

    /**
     * @param string $clientId
     *
     * @return float
     */
    private function getNormalTaxForClientId(string $clientId): float
    {
        $projectId = $this->db->fetchValue(
            'SELECT pr.id 
            FROM `fiskaly_pos_mapping` AS `fpm` 
            INNER JOIN `projekt` AS `pr` 
            WHERE fpm.client_uuid = :client_id
            LIMIT 1',
            ['client_id' => $clientId]
        );

        return $this->taxSettingWrapper->getStandardTaxRate($projectId);
    }
}
