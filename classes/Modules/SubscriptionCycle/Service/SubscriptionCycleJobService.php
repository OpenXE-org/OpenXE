<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Service;


use DateTimeInterface;
use Xentral\Components\Database\Database;
use Xentral\Modules\SubscriptionCycle\Exception\InvalidArgumentException;

final class SubscriptionCycleJobService
{
    private $db;

    /**
     * SubscriptionCycleJobService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $subscriptionCycleJobId
     */
    public function delete(int $subscriptionCycleJobId): void
    {
        $this->db->perform(
            'DELETE FROM `subscription_cycle_job` WHERE `id` = :subscription_cycle_job_id',
            ['subscription_cycle_job_id' => $subscriptionCycleJobId]
        );
    }

    /**
     * @param int    $addressId
     * @param string $documentType
     *
     * @throws InvalidArgumentException
     */
    public function deleteJobsByAddressIdAndDoctype(int $addressId, string $documentType): void
    {
        $this->ensureDocumentType($documentType);
        $this->db->perform(
            'DELETE FROM `subscription_cycle_job` WHERE `address_id` = :address_id AND `document_type` = :document_type',
            [
                'address_id'    => $addressId,
                'document_type' => $documentType,
            ]
        );
    }

    /**
     * @param int                    $addressId
     * @param string                 $documentType
     * @param string|null            $jobType
     * @param int|null               $printerId
     * @param DateTimeInterface|null $simulatedDay
     *
     * @throws InvalidArgumentException
     *
     * @return int
     */
    public function create(int $addressId, string $documentType, ?string $jobType, ?int $printerId, ?DateTimeInterface $simulatedDay = null): int
    {
        $this->ensureDocumentType($documentType);
        $this->db->perform(
            'INSERT INTO `subscription_cycle_job` 
            (`address_id`, `document_type`, `job_type`, `printer_id`, `created_at`, `simulated_day`)
            VALUES (:address_id, :document_type, :job_type, :printer_id, NOW(), :simulated_day)',
            [
                'address_id'    => $addressId,
                'document_type' => $documentType,
                'job_type'      => $jobType,
                'printer_id'    => $printerId,
                'simulated_day' => $simulatedDay === null ? null : $simulatedDay->format('Y-m-d'),
            ]
        );

        return (int)$this->db->lastInsertId();
    }

    /**
     * @param int $subscriptionCycleJobId
     *
     * @return array
     */
    public function getJob(int $subscriptionCycleJobId): array
    {
        return $this->db->fetchRow(
            'SELECT * FROM `subscription_cycle_job` WHERE `id` = :subscription_cycle_job_id',
            ['subscription_cycle_job_id' => $subscriptionCycleJobId]
        );
    }

    /**
     * @param int|null $limit
     *
     * @return array
     */
    public function listAll(?int $limit = null): array
    {
        if ($limit !== null) {
            return $this->db->fetchAll('SELECT * FROM `subscription_cycle_job` LIMIT :limit', ['limit' => $limit]);
        }

        return $this->db->fetchAll('SELECT * FROM `subscription_cycle_job`');
    }

    /**
     * @param string $documentType
     *
     * @throws InvalidArgumentException
     *
     * @return int[]
     */
    public function getAddressIdsByDocumentType(string $documentType): array
    {
        $this->ensureDocumentType($documentType);

        return array_map(
            'intval',
            $this->db->fetchCol(
                'SELECT `address_id` FROM `subscription_cycle_job` WHERE `document_type` = :document_type',
                ['document_type' => $documentType]
            )
        );
    }

    /**
     * @param string $documentType
     *
     * @throws InvalidArgumentException
     */
    private function ensureDocumentType(string $documentType): void
    {
        if (!in_array($documentType, ['rechnung', 'auftrag'])) {
            throw new InvalidArgumentException("{$documentType} is not a valid documentType");
        }
    }
}
