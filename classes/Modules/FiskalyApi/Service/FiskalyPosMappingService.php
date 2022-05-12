<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Service;


use Aura\SqlQuery\Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\FiskalyApi\Data\Organisation;

final class FiskalyPosMappingService
{
    /** @var Database $db */
    private $db;

    /**
     * FiskalyPosMappingService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function listProjects(): array
    {
        return $this->db->fetchAll(
            $this->db->select()
                ->from('projekt')
                ->where('geloescht = 0 AND kasse_konto > 0')
                ->cols(['id', 'name', 'abkuerzung'])
                ->getStatement()
        );
    }

    /**
     * @return array
     */
    public function list(): array
    {
        $query = $this->db->select()
            ->from('fiskaly_pos_mapping AS f')
            ->cols(['f.id', 'f.tss_uuid', 'f.client_uuid', 'f.pos_id']);

        return $this->db->fetchAll($query->getStatement(), $query->getBindValues());
    }

    /**
     * @param string $cashierId
     *
     * @throws Exception
     * @return array
     */
    public function getByCashierId(string $cashierId): array
    {
        $posProjectQuery = $this->db->select()
            ->from('pos_kassierer AS p')
            ->cols(['f.tss_uuid', 'f.client_uuid', 'f.pos_id'])
            ->where('p.kassenkennung=:kennung')
            ->leftJoin('fiskaly_pos_mapping AS f', 'f.pos_id = p.projekt')
            ->bindValue('kennung', $cashierId);

        return $this->db->fetchRow($posProjectQuery->getStatement(), $posProjectQuery->getBindValues());
    }

    /**
     * @param int $cashId
     *
     * @throws Exception
     * @return array|null
     */
    public function getByCashId(int $cashId): ?array
    {
        $query = $this->db->select()
            ->from('fiskaly_pos_mapping AS f')
            ->innerJoin('projekt AS p', 'f.pos_id = p.id')
            ->where('p.kasse_konto=:kasse')
            ->bindValue('kasse', $cashId)
            ->cols(['f.tss_uuid', 'f.organization_id', 'f.client_uuid', 'p.id']);

        return $this->db->fetchRow($query->getStatement(), $query->getBindValues());
    }

    /**
     * @param int $fiskalyPosMappingId
     */
    public function delete(int $fiskalyPosMappingId): void
    {
        $query = $this->db->delete()
            ->from('fiskaly_pos_mapping')
            ->where('id=:id')
            ->bindValue('id', $fiskalyPosMappingId);
        $this->db->perform($query->getStatement(), $query->getBindValues());
    }

    /**
     * @param int $projectId
     *
     * @return array
     */
    public function getTssFromProjectId(int $projectId): array
    {
        $query = $this->db->select()
            ->from('fiskaly_pos_mapping AS f')
            ->where('pos_id=:pos_id')
            ->bindValue('pos_id', $projectId)
            ->cols(['f.tss_uuid', 'f.organization_id', 'f.client_uuid']);

        return $this->db->fetchRow($query->getStatement(), $query->getBindValues());
    }

    /**
     * @param int $projectId
     *
     * @return string|null
     */
    public function getTssIdFromProjectId(int $projectId): ?string
    {
        $query = $this->db->select()
            ->from('fiskaly_pos_mapping AS f')
            ->where('pos_id=:pos_id')
            ->bindValue('pos_id', $projectId)
            ->cols(['f.tss_uuid']);

        $tssId = $this->db->fetchValue($query->getStatement(), $query->getBindValues());
        if ($tssId === false) {
            return null;
        }

        return $tssId;
    }

    /**
     * @param Organisation $organisation
     */
    public function tryCreateOrUpdateOrganization(Organisation $organisation): void
    {
        if ($this->getOrganizationByUuId($organisation->getUuid()) === null) {
            $this->createOrganization($organisation);

            return;
        }
        $this->updateOrganization($organisation);
    }

    /**
     * @param int $id
     *
     * @return Organisation|null
     */
    public function getOrganizationById(int $id): ?Organisation
    {
        $organizationRow = $this->db->fetchRow(
            'SELECT * FROM `fiskaly_organization` WHERE `id` = :id',
            [
                'id' => $id,
            ]
        );
        if (empty($organizationRow)) {
            return null;
        }

        return $this->getOrganizationFromDbEntry($organizationRow);
    }

    /**
     * @param string $uuId
     *
     * @return Organisation|null
     */
    public function getOrganizationByUuId(string $uuId): ?Organisation
    {
        $organizationRow = $this->db->fetchRow(
            'SELECT * FROM `fiskaly_organization` WHERE `fiskaly_organization_id` = :uuid',
            [
                'uuid' => $uuId,
            ]
        );
        if (empty($organizationRow)) {
            return null;
        }

        return $this->getOrganizationFromDbEntry($organizationRow);
    }

    /**
     * @param array $organizationRow
     *
     * @return Organisation
     */
    private function getOrganizationFromDbEntry(array $organizationRow): Organisation
    {
        $envs = [];
        if (!empty($organizationRow['is_environment_live'])) {
            $envs[] = 'LIVE';
        }
        if (!empty($organizationRow['is_environment_test'])) {
            $envs[] = 'TEST';
        }
        $organizationRow['_id'] = $organizationRow['fiskaly_organization_id'];
        $organizationRow['_type'] = $organizationRow['type'];
        $organizationRow['_envs'] = $envs;
        if (!empty($organizationRow['gln'])) {
            $organizationRow['billing_options']['gln'] = $organizationRow['gln'];
        }
        if (!empty($organizationRow['withhold_billing'])) {
            $organizationRow['billing_options']['withhold_billing'] = $organizationRow['withhold_billing'];
        }
        if (!empty($organizationRow['bill_to_organization'])) {
            $organizationRow['billing_options']['bill_to_organization'] = $organizationRow['bill_to_organization'];
        }

        return Organisation::fromDbState($organizationRow);
    }

    /**
     * @param Organisation $organisation
     *
     * @return int
     */
    public function createOrganization(Organisation $organisation): int
    {
        $query = $this->db->insert()
            ->into('fiskaly_organization')
            ->cols(
                [
                    'fiskaly_organization_id'    => $organisation->getUuid(),
                    'managed_by_organization_id' => $organisation->getManagedByOrganizationId(),
                    'type'                       => $organisation->getType(),
                    'name'                       => $organisation->getName(),
                    'display_name'               => $organisation->getDisplayName(),
                    'address_line1'              => $organisation->getAddressLine1(),
                    'address_line2'              => $organisation->getAddressLine2(),
                    'state'                      => $organisation->getState(),
                    'zip'                        => $organisation->getZip(),
                    'town'                       => $organisation->getTown(),
                    'tax_number'                 => $organisation->getTaxNumber(),
                    'vat_id'                     => $organisation->getVatId(),
                    'economy_id'                 => $organisation->getEconomyId(),
                    'country_code'               => $organisation->getCountryCode(),
                    'is_environment_live'        => (int)in_array('LIVE', $organisation->getEnvs()),
                    'is_environment_test'        => (int)in_array('TEST', $organisation->getEnvs()),
                ]
            );
        $this->db->perform(
            $query->getStatement(),
            $query->getBindValues()
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param Organisation $organisation
     */
    public function updateOrganization(Organisation $organisation): void
    {
        $query = $this->db->update()
            ->table('fiskaly_organization')
            ->where('fiskaly_organization_id=:fiskaly_organization_id')
            ->bindValue('fiskaly_organization_id', $organisation->getUuid())
            ->cols(
                [
                    'managed_by_organization_id' => $organisation->getManagedByOrganizationId(),
                    'type'                       => $organisation->getType(),
                    'name'                       => $organisation->getName(),
                    'display_name'               => $organisation->getDisplayName(),
                    'address_line1'              => $organisation->getAddressLine1(),
                    'address_line2'              => $organisation->getAddressLine2(),
                    'state'                      => $organisation->getState(),
                    'zip'                        => $organisation->getZip(),
                    'town'                       => $organisation->getTown(),
                    'tax_number'                 => $organisation->getTaxNumber(),
                    'vat_id'                     => $organisation->getVatId(),
                    'economy_id'                 => $organisation->getEconomyId(),
                    'country_code'               => $organisation->getCountryCode(),
                    'is_environment_live'        => (int)in_array('LIVE', $organisation->getEnvs()),
                    'is_environment_test'        => (int)in_array('TEST', $organisation->getEnvs()),
                ]
            );
        $this->db->perform(
            $query->getStatement(),
            $query->getBindValues()
        );
    }

    /**
     * @param int         $projectId
     * @param string      $tseUuid
     * @param string      $tseDescription
     * @param string      $clientUuid
     * @param string      $clientDescription
     * @param bool|null   $istTestEnvironment
     * @param string|null $organizationId
     *
     * @return int
     */
    public function create(
        int $projectId,
        string $tseUuid,
        string $tseDescription,
        string $clientUuid,
        string $clientDescription,
        ?bool $istTestEnvironment = null,
        ?string $organizationId = null
    ): int {
        $query = $this->db->insert()
            ->into('fiskaly_pos_mapping')
            ->cols(
                [
                    'pos_id'              => $projectId,
                    'tss_uuid'            => $tseUuid,
                    'tss_description'     => $tseDescription,
                    'client_uuid'         => $clientUuid,
                    'client_description'  => $clientDescription,
                    'is_test_environment' => $istTestEnvironment === null ? null : (int)$istTestEnvironment,
                    'organization_id'     => $organizationId ?? null,
                ]
            );
        $this->db->perform(
            $query->getStatement(),
            $query->getBindValues()
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param int         $fiskalyPosMappingId
     * @param int         $projectId
     * @param string      $tseUuid
     * @param string      $tseDescription
     * @param string      $clientUuid
     * @param string      $clientDescription
     * @param bool|null   $istTestEnvironment
     * @param string|null $organizationId
     */
    public function update(
        int $fiskalyPosMappingId,
        int $projectId,
        string $tseUuid,
        string $tseDescription,
        string $clientUuid,
        string $clientDescription,
        ?bool $istTestEnvironment = null,
        ?string $organizationId = null
    ): void {
        $query = $this->db->update()
            ->table('fiskaly_pos_mapping')
            ->where('id=:id')
            ->bindValue('id', $fiskalyPosMappingId)
            ->cols(
                [
                    'pos_id'              => $projectId,
                    'tss_uuid'            => $tseUuid,
                    'tss_description'     => $tseDescription,
                    'client_uuid'         => $clientUuid,
                    'client_description'  => $clientDescription,
                    'is_test_environment' => $istTestEnvironment === null ? null : (int)$istTestEnvironment,
                    'organization_id'     => $organizationId ?? null,
                ]
            );
        $this->db->perform(
            $query->getStatement(),
            $query->getBindValues()
        );
    }
}
