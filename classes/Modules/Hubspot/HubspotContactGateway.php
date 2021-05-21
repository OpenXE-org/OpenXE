<?php

namespace Xentral\Modules\Hubspot;

use Xentral\Components\Database\Database;

final class HubspotContactGateway
{
    /** @var Database $db */
    private $db;
    /**
     * @var HubspotConfigurationService
     */
    private $configurationService;

    /**
     * @param Database                    $db
     * @param HubspotConfigurationService $configurationService
     */
    public function __construct(Database $db, HubspotConfigurationService $configurationService)
    {
        $this->db = $db;
        $this->configurationService = $configurationService;
    }

    /**
     * @param int         $hsContactId
     * @param null|string $type
     *
     * @return array
     */
    public function getMappingByHubspotId(int $hsContactId, ?string $type = 'address'): array
    {
        $sql = '
            SELECT
            h.id,
            h.created_at,
            h.data,
            h.address_id,
            h.type
            FROM `hubspot_contacts` AS `h` WHERE h.hidden = 0 AND h.hs_contact_id = :id';
        $where = ['id' => $hsContactId];

        if ($type !== null) {
            $sql .= ' AND h.type = :type';
            $where['type'] = $type;
        }

        return $this->db->fetchRow($sql, $where);
    }

    /**
     * @param int    $addressId
     * @param string $type
     *
     * @return array
     */
    public function getMappingByAddressId(int $addressId, string $type = 'address'): array
    {
        return $this->db->fetchRow(
            'SELECT
            h.id,
            h.created_at,
            h.data,
            h.hs_contact_id
            FROM `hubspot_contacts` AS `h` WHERE h.hidden = 0 AND h.address_id = :id AND h.type = :type',
            ['id' => $addressId, 'type' => $type]
        );
    }


    /**
     * @param int  $addressId
     * @param bool $withStatusField
     *
     * @throws Exception\HubspotException
     *
     * @return array
     */
    public function getAddressById(int $addressId, bool $withStatusField = false): array
    {
        $placeHolder = '';
        if ($withStatusField === true) {
            $leadFields = $this->configurationService->matchSelectedAddressFreeField();
            $lrField = $leadFields['hubspot_lr_field'];
            $lsField = $leadFields['hubspot_ls_field'];
            $placeHolder = ",a.`{$lrField}`, a.`{$lsField}`";
        }

        $numberOfEmployeesField = $this->configurationService->tryGetConfiguration('hubspot_numberofemployees_field');

        if (!empty($numberOfEmployeesField)) {
            $fieldName = str_replace('adresse', '', $numberOfEmployeesField);
            $placeHolder .= ",a.`{$fieldName}` AS numberofemployees";
        }

        $settings = $this->configurationService->getSettings();
        $defaultCustomFields = array_key_exists('hubspot_address_free_fields', $settings) ?
            $settings['hubspot_address_free_fields'] : [];
        if (!empty($defaultCustomFields)) {
            foreach ($defaultCustomFields as $defaultCustomField => $systemField) {
                $fieldName = str_replace('adresse', '', $systemField);
                $placeHolder .= ",a.`{$fieldName}` AS `xthubspot_{$defaultCustomField}`";
            }
        }

        $sql = 'SELECT
            a.id,
            a.`lead`,
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
            a.strasse,
            a.vertrieb,
            a.email %s FROM `adresse` AS `a` WHERE a.geloescht = 0 AND a.id = :id';

        return $this->db->fetchRow(sprintf($sql, $placeHolder), ['id' => $addressId]);
    }

    /**
     * @param int         $hsContactId
     * @param array       $types
     *
     * @return bool
     */
    public function hubspotContactExists(int $hsContactId, array $types = []): bool
    {
        $sql = '
            SELECT
            h.id
            FROM `hubspot_contacts` AS `h` WHERE h.hidden = 0 AND h.hs_contact_id = :id';
        $where = ['id' => $hsContactId];

        if (!empty($types)) {
            $sqlType = implode("','", $types);
            $sql .= ' AND h.type IN(:type)';
            $where['type'] = $sqlType;
        }

        return !empty($this->db->fetchValue($sql, $where));
    }

    /**
     * @param int $contactPersonId
     *
     * @return array
     */
    public function getContactPersonData(int $contactPersonId): array
    {
        $sql = 'SELECT
            ap.id,
            ap.adresse AS address_id,
            ap.typ,
            ap.sprache,
            ap.name,
            ap.vorname,
            ap.bereich,
            ap.land,
            ap.ort,
            ap.plz,
            ap.strasse,
            ap.telefon,
            ap.email FROM `ansprechpartner` AS `ap` WHERE ap.geloescht = 0 AND ap.id = :id';

        return $this->db->fetchRow($sql, ['id' => $contactPersonId]);
    }

    /**
     * @param int $contactPersonId
     *
     * @return array
     */
    public function getHubspotMappingByPersonId(int $contactPersonId): array
    {
        $sql = 'SELECT  hc.hs_contact_id AS `company_id`,
       (SELECT `hs_contact_id` FROM `hubspot_contacts` WHERE `address_id` = :cid) AS `contact_id`
        FROM `ansprechpartner` AS `ap`
            JOIN `hubspot_contacts` AS `hc` ON(ap.adresse = hc.address_id AND hc.type = :type)
        WHERE ap.id = :cid';

        return $this->db->fetchRow($sql, ['cid' => $contactPersonId, 'type' => 'company']);
    }

    /**
     * @param int $noteId
     *
     * @return array
     */
    public function getAddressInfoByNoteId(int $noteId): array
    {
        $sql = 'SELECT d.adresse_to AS `address_id`,
                   a.typ AS `type`,
                   a.name, d.betreff AS `object`,
                   d.content FROM `dokumente` AS `d`
            JOIN `adresse` AS `a` ON(d.adresse_to = a.id)
            WHERE d.`id` = :note_id AND d.typ = :type';

        return $this->db->fetchRow($sql, ['note_id' => $noteId, 'type' => 'notiz']);
    }

    /**
     * @param int $companyId
     *
     * @return bool
     */
    public function hubspotSaleStaffExists(int $companyId): bool
    {
        $sql = '
            SELECT
            h.id
            FROM `hubspot_contacts` AS `h` WHERE h.hidden = 0 AND h.data = :company AND h.type = :type';
        $where = ['company' => (string)$companyId, 'type' => 'sale_staff'];

        return !empty($this->db->fetchValue($sql, $where));
    }
}
