<?php

namespace Xentral\Modules\Pos\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Pos\Exception\InvalidArgumentException;
use Xentral\Modules\Pos\Exception\RuntimeException;

final class PosJournalService
{

    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * Create a JournalEntry
     *
     * @param string      $type
     * @param int         $projectId
     * @param string      $doctype
     * @param int         $doctypeId
     * @param string      $documentNumber
     * @param string      $paymentType
     * @param float|null  $amountGross
     * @param float|null  $amountNet
     * @param string      $currency
     * @param float       $tax
     * @param string      $revenueAccount
     * @param string      $journalText
     * @param string|null $date
     * @param int         $cashRegisterId
     * @param int         $addressId
     * @param string      $createdBy
     * @param string|null $businessTransactionType
     *
     * @throws InvalidArgumentException|RuntimeException
     *
     * @return int|false Created PosJournal-ID
     */
    public function create(
        $type,
        $projectId,
        $doctype,
        $doctypeId,
        $documentNumber,
        $paymentType,
        $amountGross,
        $amountNet,
        $currency,
        $tax,
        $revenueAccount,
        $journalText,
        $date = null,
        $cashRegisterId = 0,
        $addressId = 0,
        $createdBy = '',
        $businessTransactionType = null
    ) {
        if (empty($type)) {
            throw new InvalidArgumentException(
                'Type is empty'
            );
        }
        if ($amountGross === null && $amountNet === null) {
            throw new InvalidArgumentException('Amount not given');
        }

        if ($amountGross === null) {
            $amountGross = $amountNet * (1.0 + $tax / 100.0);
        }
        if ($amountNet === null) {
            $amountNet = (float)$amountGross * (1 / (1.0 + $tax / 100.0));
        }

        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        // Create notification
        $this->db->perform(
            'INSERT INTO `pos_journal` 
            (
             `entry_date`, `project_id`, `type`, `pos_function`, `doctype`, `payment_type`, `doctype_id`, `document_number`, 
             `journal_text`, `revenue_account`, `currency`, `tax`, `amount_gross`, `amount_net`, `created_at`,
             `cashregister_id`, `address_id`, `created_by`, `business_transaction_type`
            )
            VALUES (
             :date, :project_id, :type, :pos_function,:doctype, :payment_type, :doctype_id, :document_number, 
             :journal_text, :revenue_account, :currency, :tax, :amount_gross, :amount_net, NOW(),
             :cashregister_id, :address_id, :created_by, :business_transaction_type
            )',
            [
                'type'                      => (string)$type,
                'project_id'                => (int)$projectId,
                'pos_function'              => '',
                'doctype'                   => (string)$doctype,
                'payment_type'              => (string)$paymentType,
                'doctype_id'                => (int)$doctypeId,
                'document_number'           => (string)$documentNumber,
                'journal_text'              => (string)$journalText,
                'revenue_account'           => (string)$revenueAccount,
                'currency'                  => (string)$currency,
                'tax'                       => (float)$tax,
                'amount_gross'              => (float)$amountGross,
                'amount_net'                => (float)$amountNet,
                'cashregister_id'           => (int)$cashRegisterId,
                'address_id'                => (int)$addressId,
                'date'                      => (string)$date,
                'created_by'                => (string)$createdBy,
                'business_transaction_type' => $businessTransactionType,
            ]
        );
        $insertId = (int)$this->db->lastInsertId();
        if ($insertId === 0) {
            throw new RuntimeException('Pos Journal could not be created.');
        }

        return $insertId;
    }
}
