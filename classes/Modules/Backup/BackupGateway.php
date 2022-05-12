<?php

namespace Xentral\Modules\Backup;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;

/**
 * Class BackupGateway
 *
 * @package Xentral\Modules\Backup
 */
final class BackupGateway
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
     * @return array
     */
    public function getTables()
    {
        return $this->db->fetchCol('SHOW TABLES');
    }

    /**
     * @return array
     */
    public function getTablesChecksum()
    {
        $ahCheckSum = [];
        foreach ($this->getTables() as $sTable) {
            $ahCheckSum[$sTable] = 0;
            if ($hResult = $this->db->fetchRow('CHECKSUM TABLE ' . $sTable)) {
                $num = 0;
                try {
                    $num = $this->db->fetchValue('SELECT COUNT(*) AS `anzahl` FROM ' . $sTable);
                } catch (DatabaseExceptionInterface $exception) {
                    // nothing
                }
                $ahCheckSum[$sTable] = [
                    'checksum' => array_key_exists('Checksum', $hResult) ? $hResult['Checksum'] : 0,
                    'items'    => $num,
                ];
            }
        }

        return $ahCheckSum;
    }

    /**
     * @return array
     */
    public function getAdminUserIds()
    {
        $query = $this->db->select()
            ->cols(['u.id'])
            ->from('user AS u')
            ->where('u.activ = ?', 1)
            ->where('u.type = ?', 'admin');

        return $this->db->fetchAll($query->getStatement(), $query->getBindValues());
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getBackupById($id)
    {
        if (is_numeric($id)) {
            $query = $this->db->select()
                ->cols(['b.id', 'b.adresse', 'b.name', 'b.dateiname', 'b.datum'])
                ->from('backup AS b')
                ->where('b.id = ?', $id);

            return $this->db->fetchRow($query->getStatement(), $query->getBindValues());
        }

        return [];
    }

    /**
     * @return array
     */
    public function getLatestBackup()
    {
        $sql = 'SELECT b.id, b.name,b.dateiname, b.adresse,b.datum FROM backup AS `b` ORDER BY b.datum DESC LIMIT 1';

        return $this->db->fetchRow($sql);
    }
}
