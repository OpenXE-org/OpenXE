<?php

namespace Xentral\Modules\DownloadSpooler;

use Xentral\Components\Database\Database;

final class DownloadSpoolerGateway
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
     * @param int      $userId
     * @param int|null $printerId
     *
     * @return array
     */
    public function getUnprintedFileIdsByUser($userId, $printerId = null)
    {
        $select = $this->db
            ->select()
            ->cols(['d.id'])
            ->from('drucker_spooler AS d')
            ->where('d.gedruckt = ?', 0)
            ->where('d.user = ?', (int)$userId);

        if ($printerId !== null) {
            $select->where('d.drucker = ?', (int)$printerId);
        }

        return $this->db->fetchCol(
            $select->getStatement(),
            $select->getBindValues()
        );
    }

    /**
     * @param int      $userId
     * @param int|null $printerId
     *
     * @return int
     */
    public function getUnprintedFilesCountByUser($userId, $printerId = null)
    {
        $select = $this->db
            ->select()
            ->cols(['COUNT(d.id)'])
            ->from('drucker_spooler AS d')
            ->where('d.gedruckt = ?', 0)
            ->where('d.user = ?', (int)$userId);

        if ($printerId !== null) {
            $select->where('d.drucker = ?', (int)$printerId);
        }

        return (int)$this->db->fetchValue(
            $select->getStatement(),
            $select->getBindValues()
        );
    }

    /**
     * @param int $printerId
     *
     * @return string|null
     */
    public function getPrinterNameById($printerId)
    {
        $select = $this->db
            ->select()
            ->cols(['d.name'])
            ->from('drucker AS d')
            ->where('d.id = ?', (int)$printerId);

        return $this->db->fetchValue(
            $select->getStatement(),
            $select->getBindValues()
        );
    }
}
