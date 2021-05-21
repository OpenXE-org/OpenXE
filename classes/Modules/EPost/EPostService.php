<?php

namespace Xentral\Modules\EPost;

use ApplicationCore;
use RuntimeException;
use Xentral\Engine\Application;
use Xentral\Engine\Container;
use Xentral\Components\Database\Database;

class EPostService
{
    /** @var Database */
    private $database;

    /** @var \Application */
    private $app;

    /**
     * EPostService constructor.
     *
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->database = $container->get('Database');
        $this->app = $container->get('LegacyApplication');
    }

    public function getSyncedFiles(){
        $query = $this->database->select()
            ->from('epost_files')
            ->cols([
                'auftrag',
                'datum',
                'status',
                'datei'
            ]);

        return $this->database->fetchAll($query->getStatement(), $query->getBindValues());
    }

    public function outputFileById($id){
        $query = $this->database->select()
            ->from('epost_files')
            ->cols(['datei'])
            ->where("id={$id}");

        $filePath = $this->database->fetchValue($query->getStatement(), $query->getBindValues());

        if(!file_exists($filePath)){
            throw new RuntimeException("Datei {$filePath} existiert nicht");
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');

        echo file_get_contents($filePath);

        $this->app->ExitXentral();
    }

    private function syncReceipt($receiptId, $belegNr, $projectId, $styleSettings, $outputDir, $markAsSent){
        $receipt = new \RechnungPDF($this->app, $projectId, $styleSettings);
        $receipt->GetRechnung($receiptId);

        $tempFile = $receipt->displayTMP();

        if($outputDir[strlen($outputDir) - 1] != '/') $outputDir .= '/';
        $outputFile = $outputDir . 'RE' .$belegNr . '_' . uniqid() . '.pdf';
        rename($tempFile, $outputFile);

        if(file_exists($tempFile)) throw new RuntimeException("Fehler beim Verschieben der datei {$tempFile}");
        if(!file_exists($outputFile)) throw new RuntimeException("Fehler beim Erstellen der Datei {$outputFile}");

        $query = $this->database->insert()
            ->into('epost_files')
            ->cols([
                'status' => 'erfolgreich',
                'rechnung' => $receiptId,
                'datei' => $outputFile
            ]);

        $this->database->perform($query->getStatement(), $query->getBindValues());

        if($markAsSent){
            $query = $this->database->update()
                ->table('rechnung')
                ->cols([
                    'status' => 'versendet',
                    'versendet' => 1,
                    'schreibschutz' => 1
                ])
                ->where('id=:id')
                ->bindValue('id', $receiptId);

            $this->database->perform($query->getStatement(), $query->getBindValues());
        }
        $this->app->erp->RechnungProtokoll($receiptId, 'An EPost übertragen');
    }

    /**
     * @param array $receiptIds
     * @param array $styleSettings
     * @param string $outputDir
     * @param bool $markAsSent
     *
     * @return void
     */
    public function syncReceipts($receiptIds, $styleSettings, $outputDir, $markAsSent){
        if(empty($outputDir) || !file_exists($outputDir) || !is_dir($outputDir)) throw new RuntimeException('Bitte gültigen Ablageordner angeben!');
        if(empty($receiptIds)) throw new RuntimeException('Keine Aufträge ausgewählt');

        $query = $this->database->select()
            ->cols(['id', 'belegnr', 'projekt'])
            ->from('rechnung');

        foreach ($receiptIds as $receiptId){
            $query = $query->orWhere("id={$receiptId}");
        }

        $receipts = $this->database->fetchAssoc($query->getStatement(), $query->getBindValues());

        foreach ($receiptIds as $receipt) {
            $this->syncReceipt(
                $receipt,
                $receipts[$receipt]['belegnr'],
                $receipts[$receipt]['projekt'],
                $styleSettings,
                $outputDir,
                $markAsSent
            );
        }
    }
}
