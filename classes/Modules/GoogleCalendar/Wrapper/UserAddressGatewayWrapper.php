<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Wrapper;

use Xentral\Components\Database\Database;

class UserAddressGatewayWrapper
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
     * Finds address id by user id.
     *
     * @param int $userId
     *
     * @return int address Id
     */
    public function getAddressByUser(int $userId): int
    {
        if ($userId < 1) {
            return 0;
        }
        $entry = $this->db->fetchValue(
            'SELECT u.adresse FROM `user` AS `u` WHERE u.id = :userId',
            ['userId' => (int)$userId]
        );

        return (int)$entry;
    }

    /**
     * @param int $addressId
     *
     * @return int userId 0=address has no user
     */
    public function getUserByAddress(int $addressId): int
    {
        if ($addressId < 1) {
            return 0;
        }
        $entry = $this->db->fetchValue(
            'SELECT u.id FROM `user` AS u WHERE u.adresse = :addressId',
            ['addressId' => (int)$addressId]
        );

        return (int)$entry;
    }

    /**
     * @param $addressId
     *
     * @return string e-mail address
     */
    public function getEmailByAddress(int $addressId): string
    {
        if ($addressId < 1) {
            return '';
        }
        $sql = "SELECT a.email AS `email`
                FROM `adresse` AS `a`
                WHERE a.id = :addressId AND a.email <> '' AND a.email IS NOT NULL
                UNION ALL
                SELECT k.kontakt AS `email`
                FROM `adresse_kontakte` AS `k`
                WHERE k.adresse = :addressId AND (k.bezeichnung LIKE 'e%mail' OR k.bezeichnung LIKE '%google%')
                LIMIT 1";
        $values = ['addressId' => $addressId];
        $result = $this->db->fetchRow($sql, $values);
        if (empty($result) || $result['email'] === '') {
            return '';
        }

        return $result['email'];
    }

    /**
     * Finds address Id by e-mail.
     *
     * @param string $email
     *
     * @return int address id
     */
    public function findAddressByEmail(string $email): int
    {
        $values = ['email' => strtolower($email)];
        $address = $this->db->fetchValue(
            'SELECT u.adresse
                    FROM `user` AS `u` 
                    JOIN `google_account` AS `gc` ON u.id = gc.user_id
                    JOIN `google_account_property` AS `gp` ON gp.google_account_id = gc.id
                    WHERE gp.value LIKE :email',
            $values
        );
        if ($address > 0) {
            return $address;
        }

        $address = $this->db->fetchValue(
            'SELECT a.id
                    FROM `adresse` AS `a`
                    WHERE a.email LIKE :email AND a.geloescht = 0',
            $values
        );
        if ($address > 0) {
            return $address;
        }

        $address = $this->db->fetchValue(
            "SELECT k.adresse
                    FROM `adresse_kontakte` AS `k`
                    WHERE (k.bezeichnung LIKE 'e%mail' OR k.bezeichnung LIKE '%google%') AND k.kontakt LIKE :email",
            $values
        );
        if ($address > 0) {
            return $address;
        }

        $address = $this->db->fetchValue(
            "SELECT ap.adresse
                    FROM `ansprechpartner` AS `ap`
                    WHERE ap.email LIKE :email",
            $values
        );
        if ($address > 0) {
            return $address;
        }

        return 0;
    }
}
