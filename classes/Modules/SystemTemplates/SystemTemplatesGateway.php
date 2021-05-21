<?php


namespace Xentral\Modules\SystemTemplates;

use Xentral\Components\Database\Database;
use Xentral\Modules\Backup\BackupGateway;

final class SystemTemplatesGateway
{
    /** @var Database $db */
    private $db;

    /** @var BackupGateway $gateway */
    private $gateway;

    /**
     * SystemTemplatesGateway constructor.
     *
     * @param Database      $db
     * @param BackupGateway $gateway
     */
    public function __construct(Database $db, BackupGateway $gateway)
    {
        $this->db = $db;
        $this->gateway = $gateway;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getTemplateById(int $id): array
    {
        return $this->db->fetchRow(
            'SELECT
            s.id,
            s.title,
            s.category,
            s.description,
            s.filename,
            s.created_at,
            s.footer_icons
            FROM `systemtemplates` AS `s` WHERE s.hidden = 0 AND s.id = :id',
            ['id' => $id]
        );
    }

    /**
     * @return array
     */
    public function getTables(): array
    {
        return $this->gateway->getTables();
    }

    /**
     * @return array
     */
    public function getTablesChecksum(): array
    {
        return $this->gateway->getTablesChecksum();
    }

    /**
     * @return array
     */
    public function getAdminUserIds(): array
    {
        return $this->gateway->getAdminUserIds();
    }
}
