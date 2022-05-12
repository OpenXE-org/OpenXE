<?php

declare(strict_types=1);

namespace Xentral\Modules\TimeManagement\Service;

use DateTimeInterface;
use Xentral\Components\Database\Database;
use Xentral\Modules\TimeManagement\Exception\InvalidQueryException;

final class TimeManagementTargetHourService
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
     * @param string $vacationRequestToken
     * @param string $internalComment
     *
     */
    public function saveInternalComment(string $vacationRequestToken, string $internalComment): void
    {
        $sql =
            'UPDATE `mitarbeiterzeiterfassung_sollstunden` SET
            `internal_comment` = :internal_comment                                                  
            WHERE `vacation_request_token` = :vacation_request_token';

        $this->db->perform(
            $sql,
            [
                'vacation_request_token' => $vacationRequestToken,
                'internal_comment'       => $internalComment,
            ]
        );
    }

    /**
     * @param int               $addressId
     * @param DateTimeInterface $date
     * @param string            $oldType
     * @param string            $newType
     *
     */
    public function updateTargetHourType(
        int $addressId,
        DateTimeInterface $date,
        string $oldType,
        string $newType
    ): void {
        $sql =
            'UPDATE `mitarbeiterzeiterfassung_sollstunden` SET 
            `kuerzel` = REPLACE(`kuerzel`,:old_type, :new_type) 
            WHERE `adresse` = :address_id
            AND `datum` = :date';

        $numAffected = (int)$this->db->fetchAffected(
            $sql,
            [
                'old_type'   => $oldType,
                'new_type'   => $newType,
                'address_id' => $addressId,
                'date'       => $date->format('Y-m-d'),
            ]
        );

        if ($numAffected == 0) {
            throw new InvalidQueryException(
                'Target hour could not be updated. Maybe wrong arguments. addressId: ' . $addressId .
                ', dateString: ' . $date->format('Y-m-d') .
                ', oldType: ' . $oldType .
                ', newType: ' . $newType
            );
        }
    }

    /**
     * @param int               $addressId
     * @param DateTimeInterface $date
     * @param string            $requestToken
     *
     * @throws InvalidQueryException
     */
    public function updateVacationRequestToken(int $addressId, DateTimeInterface $date, string $requestToken): void
    {
        $sql =
            'UPDATE `mitarbeiterzeiterfassung_sollstunden` SET 
            `vacation_request_token` = :vacation_request_token
            WHERE `adresse` = :address_id
            AND `datum` = :date';

        $numAffected = (int)$this->db->fetchAffected(
            $sql,
            [
                'vacation_request_token' => $requestToken,
                'address_id'             => $addressId,
                'date'                   => $date->format('Y-m-d'),
            ]
        );

        if ($numAffected == 0) {
            throw new InvalidQueryException(
                'Target hour could not be updated. Maybe wrong arguments. addressId: ' . $addressId .
                ', dateString: ' . $date->format('Y-m-d') .
                ', requestToken: ' . $requestToken
            );
        }
    }
}
