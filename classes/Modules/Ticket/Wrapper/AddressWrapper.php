<?php

declare(strict_types=1);

namespace Xentral\Modules\Ticket\Wrapper;

use Xentral\Components\Database\Database;

class AddressWrapper
{
    /** @var Database $db */
    private $db;

    /**
     * AddressWrapper constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $emailAddress
     *
     * @return int|null
     */
    public function tryGetAddressIdByEmailAddress(string $emailAddress): ?int
    {
        $values = ['email_address' => $emailAddress];
        $searchByEmail = 'SELECT a.id FROM `adresse` AS `a`
                           WHERE a.email LIKE :email_address AND a.geloescht = 0
                           ORDER BY a.id DESC';
        $id = $this->db->fetchValue($searchByEmail, $values);
        if ($id !== null && $id > 0) {
            return $id;
        }

        $searchByResponsePerson = 'SELECT ap.adresse FROM `ansprechpartner` AS `ap`
                                    WHERE ap.email LIKE :email_address
                                    ORDER BY ap.id DESC';
        $id = $this->db->fetchValue($searchByResponsePerson, $values);
        if ($id !== null && $id > 0) {
            return $id;
        }

        $searchByContactInfo = 'SELECT ak.adresse FROM `adresse_kontakte` AS `ak`
                                 WHERE ak.kontakt LIKE :email_address ORDER BY ak.id DESC';
        $id = $this->db->fetchValue($searchByContactInfo, $values);

        return $id;
    }
}
