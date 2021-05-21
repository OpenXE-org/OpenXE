<?php

namespace Xentral\Modules\DownloadSpooler;

use erpAPI;
use Xentral\Components\Database\Database;
use Xentral\Modules\DownloadSpooler\Exception\InvalidArgumentException;
use Xentral\Modules\DownloadSpooler\Exception\RuntimeException;
use ZipArchive;

final class DownloadSpoolerService
{
    /** @var Database $db */
    private $db;

    /**
     * @deprecated
     * @var erpAPI $erp
     */
    private $erp;

    /**
     * @param Database $database
     * @param erpAPI   $erpApi
     */
    public function __construct(Database $database, erpAPI $erpApi)
    {
        $this->db = $database;
        $this->erp = $erpApi;
    }

    /**
     * @example makeZipCompilation([3, 4, 5], 1)
     *
     * @param array    $spoolerIds
     * @param int      $userId
     * @param int|null $printerId
     *
     * @throws RuntimeException|InvalidArgumentException
     *
     * @return string Absolute path to zip file
     */
    public function createZipCompilation($spoolerIds, $userId, $printerId = null)
    {
        $userId = $this->ensureUserId($userId);
        if (empty($spoolerIds)) {
            throw new InvalidArgumentException('Required argument "$spoolerIds" is empty.');
        }

        $select = $this->db
            ->select()
            ->cols(['ds.id', 'ds.zeitstempel', 'ds.filename', 'ds.content'])
            ->from('drucker_spooler AS ds')
            ->where('ds.id IN (?)', $spoolerIds)
            ->where('ds.user = ?', $userId);

        if ($printerId !== null) {
            $select->where('ds.drucker = ?', (int)$printerId);
        }

        $data = $this->db->fetchAll(
            $select->getStatement(),
            $select->getBindValues()
        );
        if (empty($data)) {
            throw new RuntimeException('Data not found.');
        }

        $filePath = tempnam(sys_get_temp_dir(), 'spooler_zip');
        $zip = new ZipArchive();
        $zip->open($filePath, ZipArchive::CREATE);
        foreach ($data as $row) {
            $zip->addFromString(
                "{$row['id']} {$row['zeitstempel']} {$row['filename']}",
                base64_decode($row['content'])
            );
        }
        $zip->close();

        // Mark processed files as printed
        $this->markFilesAsPrinted(array_column($data, 'id'), $printerId);

        return $filePath;
    }

    /**
     * @example makePdfCompilation([3, 4, 5], 1)
     *
     * @param array    $spoolerIds
     * @param int      $userId
     * @param int|null $printerId
     *
     * @throws RuntimeException|InvalidArgumentException
     *
     * @return string Absolute path to pdf compilation file
     */
    public function createPdfCompilation($spoolerIds, $userId, $printerId = null)
    {
        $userId = $this->ensureUserId($userId);
        if (empty($spoolerIds)) {
            throw new InvalidArgumentException('Required argument "$spoolerIds" is empty.');
        }

        $select = $this->db
            ->select()
            ->cols(['ds.id', 'ds.content'])
            ->from('drucker_spooler AS ds')
            ->where('ds.id IN (?)', $spoolerIds)
            ->where('ds.user = ?', $userId);

        if ($printerId !== null) {
            $select->where('ds.drucker = ?', (int)$printerId);
        }

        $data = $this->db->fetchAll(
            $select->getStatement(),
            $select->getBindValues()
        );
        if (empty($data)) {
            throw new RuntimeException('Data not found.');
        }

        // Save each file to temp dir
        $tempPdfFiles = [];
        foreach ($data as $row) {
            $tempPdf = tempnam(sys_get_temp_dir(), 'spooler_pdf');
            file_put_contents($tempPdf, base64_decode($row['content']));
            $tempPdfFiles[] = $tempPdf;
        }

        // Create compilation from temporary files
        $compilationPdf = $this->erp->MergePDF($tempPdfFiles); // @todo Refactor erpAPI out
        $compilationPdfPath = tempnam(sys_get_temp_dir(), 'spooler_pdf');
        file_put_contents($compilationPdfPath, $compilationPdf);

        // Delete temporary files
        foreach ($tempPdfFiles as $tempPdfFile) {
            unlink($tempPdfFile);
        }

        // Mark processed files as printed
        $this->markFilesAsPrinted(array_column($data, 'id'), $printerId);

        return $compilationPdfPath;
    }

    /**
     * Get single spooler file as array
     *
     * @param int $fileId
     * @param int $userId
     *
     * @throws RuntimeException If file id not exists
     *
     * @return array
     */
    public function fetchFile($fileId, $userId)
    {
        $userId = $this->ensureUserId($userId);

        $data = $this->db->fetchRow(
            'SELECT d.filename, d.zeitstempel, d.content 
             FROM drucker_spooler AS d 
             WHERE d.id = :file_id AND d.user = :user_id',
            ['user_id' => $userId, 'file_id' => (int)$fileId]
        );
        if (empty($data)) {
            throw new RuntimeException(sprintf('File ID%s not found.', $fileId));
        }

        $result = [
            'filedate' => $data['zeitstempel'],
            'filename' => $data['filename'],
            'content'  => $data['content'],
        ];

        $this->markFilesAsPrinted([$fileId], $userId);

        return $result;
    }

    /**
     * @param int $fileId
     * @param int $printerId
     *
     * @return bool
     */
    public function deleteFile($fileId, $printerId)
    {
        $fileId = (int)$fileId;
        $printerId = (int)$printerId;
        if ($fileId === 0 || $printerId === 0) {
            throw new InvalidArgumentException('Required parameter "fileId" or "printerId" is empty.');
        }

        // Check that printer id and file id are joining
        $checkPrinterId = (int)$this->db->fetchValue(
            'SELECT d.drucker FROM drucker_spooler AS d WHERE d.id = :file_id',
            ['file_id' => $fileId]
        );
        if ($checkPrinterId !== $printerId) {
            throw new InvalidArgumentException(sprintf(
                'Parameter "fileId":"%s" and "printerId":"%s" are not joining.',
                $fileId,
                $printerId
            ));
        }

        $numRows = (int)$this->db->fetchAffected(
            'DELETE FROM drucker_spooler WHERE id = :file_id AND drucker = :printer_id LIMIT 1',
            ['file_id' => $fileId, 'printer_id' => $printerId]
        );

        return $numRows === 1;
    }

    /**
     * @param array    $spoolerIds
     * @param int      $userId
     * @param int|null $printerId
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function markFilesAsPrinted($spoolerIds, $userId, $printerId = null)
    {
        $userId = $this->ensureUserId($userId);
        if (empty($spoolerIds)) {
            throw new InvalidArgumentException('Required argument "$spoolerIds" is empty.');
        }

        $query = $this->db
            ->update()
            ->table('drucker_spooler')
            ->set('gedruckt', 1)
            ->where('id IN (?)', $spoolerIds)
            ->where('user = ?', $userId);

        if ($printerId !== null) {
            $query->where('drucker = ?', (int)$printerId);
        }

        $this->db->perform(
            $query->getStatement(),
            $query->getBindValues()
        );
    }

    /**
     * @param int $userId
     *
     * @throws InvalidArgumentException
     *
     * @return int
     */
    private function ensureUserId($userId)
    {
        if (empty($userId) || (int)$userId < 0) {
            throw new InvalidArgumentException('Required argument "userId" is empty or invalid.');
        }

        return (int)$userId;
    }
}
