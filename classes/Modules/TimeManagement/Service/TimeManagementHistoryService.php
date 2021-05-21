<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Service;

use DateTimeInterface;
use Xentral\Components\Database\Database;
use Xentral\Modules\TimeManagement\Exception\InvalidArgumentException;
use Xentral\Modules\TimeManagement\Exception\InvalidQueryException;

final class TimeManagementHistoryService
{

    /** @var Database $db */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int               $employeeAddressId
     * @param int               $supervisorAddressId
     * @param string            $oldType
     * @param string            $newType
     * @param string            $requestToken
     * @param DateTimeInterface $from
     * @param DateTimeInterface $till
     * @param string            $comment
     *
     * @throws InvalidArgumentException
     * @throws InvalidQueryException
     */
    public function saveActivity(
        int $employeeAddressId,
        int $supervisorAddressId,
        string $oldType,
        string $newType,
        string $requestToken,
        DateTimeInterface $from,
        DateTimeInterface $till,
        string $comment
    ): void {
        if ($employeeAddressId === 0 && $supervisorAddressId === 0) {
            throw new InvalidArgumentException('No addresses given.');
        }

        if (empty($oldType) && empty($newType)) {
            throw new InvalidArgumentException('No types given.');
        }

        $sql =
            'INSERT INTO `timemanagement_history` (
                `employee_address_id`,
                `supervisor_address_id`,
                `old_day_type`,
                `new_day_type`,
                `request_token`,
                `from`,
                `till`,
                `comment`
            ) VALUES (
                 :employee_address_id,
                 :supervisor_address_id,
                 :old_day_type,
                 :new_day_type,
                 :request_token,
                 :from,
                 :till,
                 :comment
            )';

        $arguments = [
            'employee_address_id'   => $employeeAddressId,
            'supervisor_address_id' => $supervisorAddressId,
            'old_day_type'          => $oldType,
            'new_day_type'          => $newType,
            'request_token'         => $requestToken,
            'from'                  => $from->format('Y-m-d'),
            'till'                  => $till->format('Y-m-d'),
            'comment'               => $comment,
        ];

        $numAffected = (int)$this->db->fetchAffected($sql, $arguments);

        if ($numAffected === 0) {
            throw new InvalidQueryException(
                'Time management history could not be updated. Arguments: ' . implode(', ', $arguments)
            );
        }
    }
}
