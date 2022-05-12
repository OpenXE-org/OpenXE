<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Gateway;

use Xentral\Components\Database\Database;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Service\PipedriveConfigurationService;

final class PipedriveContactGateway
{
    /** @var Database $db */
    private $db;

    /** @var PipedriveConfigurationService $configurationService */
    private $configurationService;

    /**
     * @param Database                      $db
     * @param PipedriveConfigurationService $configurationService
     */
    public function __construct(Database $db, PipedriveConfigurationService $configurationService)
    {
        $this->db = $db;
        $this->configurationService = $configurationService;
    }

    /**
     * @param int $pdContactId Pipedrive contact ID
     *
     * @return array
     */
    public function getMappingByPipedriveId(int $pdContactId): array
    {
        return $this->db->fetchRow(
            'SELECT
            p.id,
            p.created_at,
            p.data,
            p.address_id
            FROM `pipedrive_contacts` AS `p`
            WHERE p.hidden = 0 AND p.pd_contact_id = :id',
            ['id' => $pdContactId]
        );
    }

    /**
     * @param int $addressId Xentral Address ID
     *
     * @return array
     */
    public function getMappingByAddressId(int $addressId): array
    {
        return $this->db->fetchRow(
            'SELECT
            p.id,
            p.created_at,
            p.data,
            p.pd_contact_id
            FROM `pipedrive_contacts` AS `p`
            WHERE p.hidden = 0 AND p.address_id = :id',
            ['id' => $addressId]
        );
    }

    /**
     * @param int  $addressId
     * @param bool $withStatusField
     *
     * @throws PipedriveConfigurationException
     *
     * @return array
     */
    public function getInternalAddressById(int $addressId, bool $withStatusField = false): array
    {
        $placeHolder = '';
        if ($withStatusField === true) {
            // @codeCoverageIgnoreStart
            $leadFields = $this->configurationService->matchSelectedAddressFreeField();
            $lsField = $leadFields['pipedrive_ls_field'];
            $placeHolder = ", a.{$lsField}";
            // @codeCoverageIgnoreEnd
        }

        $sql = 'SELECT
            a.id,
            a.lead,
            a.typ,
            a.sprache,
            a.name,
            a.vorname,
            a.nachname,
            a.land,
            a.ort,
            a.plz,
            a.bundesstaat,
            a.telefon,
            a.email %s
            FROM `adresse` AS `a`
            WHERE a.geloescht = 0 AND a.id = :id';

        return $this->db->fetchRow(sprintf($sql, $placeHolder), ['id' => $addressId]);
    }

}
