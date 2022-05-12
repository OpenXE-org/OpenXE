<?php

namespace Xentral\Modules\DocuvitaSolutionsApi;


use Application;
use CURLFile;
use RuntimeException;
use Xentral\Components\Database\Database;
use Xentral\Core\DependencyInjection\ServiceContainer;
use Xentral\Modules\DocuvitaSolutionsApi\Data\DocuvitaSolutionsTemplateData;
use ZipArchive;

class DocuvitaSolutionsApiService
{
    const KEY_LAST_ARCHIVE_ID = 'docuvita_solutions_last_archive_id';
    const KEY_LAST_FILE_ID = 'docuvita_solutions_last_file_id';

    /** @var ServiceContainer */
    private $container;
    /**
     * @var Database
     */
    private $db;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $endpoint;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $syncStart;
    /**
     * @var string
     */
    private $systemReference;
    /**
     * @var string
     */
    private $templateAddress;
    /**
     * @var string
     */
    private $templateProject;
    /**
     * @var string
     */
    private $templateOffer;
    /**
     * @var string
     */
    private $templateInvoice;
    /**
     * @var string
     */
    private $templateCredit;
    /**
     * @var string
     */
    private $templateDeliveryNote;
    /**
     * @var string
     */
    private $templateOrder;

    /** @var string */
    private $templateOrderIn;

    /** @var string */
    private $templateVerbindlichkeit;

    /** @var Application */
    private $app;

    /** @var int */
    private $lastArchiveID;

    /** @var int */
    private $lastFileID;

    /** @var string[] */
    private $missingFiles = [];

    /** @var int */
    private $sqlLimit = 100;


    /**
     * DocuvitaSolutionsApiService constructor.
     *
     * @param Application $app
     * @param string      $username
     * @param string      $password
     * @param string      $endpoint
     * @param string      $syncStart
     * @param string      $systemReference
     * @param string      $templateAddress
     * @param string      $templateProject
     * @param string      $templateOffer
     * @param string      $templateInvoice
     * @param string      $templateCredit
     * @param string      $templateDeliveryNote
     * @param string      $templateOrder
     * @param string      $templateOrderIn
     * @param string      $templateVerbindlichkeit
     */
    public function __construct(
        $app,
        $username,
        $password,
        $endpoint,
        $syncStart,
        $systemReference,
        $templateAddress,
        $templateProject,
        $templateOffer,
        $templateInvoice,
        $templateCredit,
        $templateDeliveryNote,
        $templateOrder,
        $templateOrderIn,
        $templateVerbindlichkeit
    ) {
        $this->app = $app;
        $this->container = $app->Container;
        $this->db = $this->container->get('Database');

        $this->username = $username;
        $this->password = $password;
        $this->endpoint = $endpoint;
        $this->syncStart = $syncStart;
        $this->systemReference = $systemReference;

        $this->templateAddress = $templateAddress;
        $this->templateProject = $templateProject;
        $this->templateOffer = $templateOffer;
        $this->templateInvoice = $templateInvoice;
        $this->templateCredit = $templateCredit;
        $this->templateDeliveryNote = $templateDeliveryNote;
        $this->templateOrder = $templateOrder;
        $this->templateOrderIn = $templateOrderIn;
        $this->templateVerbindlichkeit = $templateVerbindlichkeit;

        $this->lastArchiveID = $this->app->erp->GetKonfiguration(self::KEY_LAST_ARCHIVE_ID);
        if (empty($this->lastArchiveID)) {
            $this->lastArchiveID = -1;
        } else {
            $this->lastArchiveID = (int)$this->lastArchiveID;
        }

        $this->lastFileID = $this->app->erp->GetKonfiguration(self::KEY_LAST_FILE_ID);
        if (empty($this->lastFileID)) {
            $this->lastFileID = -1;
        } else {
            $this->lastFileID = (int)$this->lastFileID;
        }
    }


    /**
     * @param string $uploadGuid
     * @param string $filePath
     * @param string $fileName
     *
     * @return array
     */
    public function uploadFile($uploadGuid, $filePath, $fileName)
    {
        $curl = curl_init("{$this->endpoint}/fileupload?guid={$uploadGuid}&format=json");


        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'file' => new CURLFile($filePath, null, $fileName),
            ],
        ]);
        $response = curl_exec($curl);

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);


        curl_close($curl);

        $this->checkCode($code, $response);

        return json_decode($response);

    }

    public function sendFileAsString($fileName, $content, $uploadGuid)
    {
        $curl = curl_init("{$this->endpoint}/fileupload?guid={$uploadGuid}&format=json");
        $boundary = '------------------------' . uniqid();
        $body = "--$boundary\r\n"
            . "Content-Disposition: form-data; name=\"file\"; filename=\"$fileName\"\r\n"
            . "Content-Type: application/pdf\r\n\r\n"
            . "$content\r\n"
            . "--$boundary--\r\n";


        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Content-Length: ' . (strlen($body)),
                "Content-Type: multipart/form-data; boundary=$boundary",
                'Transfer-Encoding: chunked',
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
        ]);
        $response = curl_exec($curl);

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);


        curl_close($curl);

        $this->checkCode($code, $response);

        return json_decode($response);
    }

    private function log($message, $dump = '')
    {
        $this->app->erp->LogFile($message, $dump, 'docuvitasolutions');
    }

    /**
     * @return void
     */
    public function export()
    {
        $this->log('starting export');
        $dataObjects = [
            $this->generateAddressTemplates(),
            $this->generateProjectTemplates(),
            $this->generateOfferTemplates(),
            $this->generateInvoiceTemplates(),
            $this->generateCreditTemplates(),
            $this->generateDeliveryNoteTemplates(),
            $this->generateOrderTemplates(),
            $this->generateOrderInTemplates(),
            $this->generateVerbindlichkeitTemplates(),
        ];
        if (!empty($this->missingFiles)) {
            $this->log("missing files", implode("\n", $this->missingFiles));
        }
        for ($i = 0, $count = count($dataObjects); $i < $count; $i++) {
            if (count($dataObjects[$i]->getQueryResult()) === 0) {
                unset($dataObjects[$i]);
            }
        }

        if (count($dataObjects) === 0) {
            $this->log('no data left to export');

            $this->app->erp->SetKonfigurationValue(self::KEY_LAST_ARCHIVE_ID, $this->lastArchiveID);

            return;
        }

        $zipFilePath = $this->generateZipFromTemplates($dataObjects);

        if (!file_exists($zipFilePath)) {
            throw new RuntimeException('Fehler beim Erstellen der ZIP');
        }

        $uploadGuid = $this->generateImportSessionGuid();
        $this->uploadFile($uploadGuid, $zipFilePath, 'upload.zip');

        unlink($zipFilePath);


        // Save uploaded objects
        foreach ($dataObjects as $templateObject) {
            /** @var DocuvitaSolutionsTemplateData $templateObject */
            if (in_array($templateObject->getRefType(), ['adresse', 'projekt'])) {
                $sqlValues = [];
                $idIdentifier = "{$templateObject->getRefType()}___id";
                $type = $this->db->escapeString($templateObject->getRefType());
                /** @var DocuvitaSolutionsTemplateData $templateObject */
                foreach ($templateObject->getQueryResult() as $queryResult) {
                    $id = $this->db->escapeInt($queryResult[$idIdentifier]);
                    $sqlValues[] = "({$type}, {$id})";
                }
                $sql = implode(',', $sqlValues);
                $this->db->perform("INSERT INTO docuvitasolutions_export (ref_type, ref_id) VALUES {$sql}");
            } else {
                foreach ($templateObject->getQueryResult() as $queryResult) {
                    if (in_array('pdfarchiv___id', array_keys($queryResult))) {
                        $this->lastArchiveID = max($this->lastArchiveID, (int)$queryResult['pdfarchiv___id']);
                    } else if (in_array('datei_stichwoerter___id', array_keys($queryResult))) {
                        $this->lastFileID = max($this->lastFileID, (int)$queryResult['datei_stichwoerter___id']);
                    }
                }
            }
        }

        $this->app->erp->SetKonfigurationValue(self::KEY_LAST_ARCHIVE_ID, $this->lastArchiveID);
        $this->app->erp->SetKonfigurationValue(self::KEY_LAST_FILE_ID, $this->lastFileID);
        $this->log('export finished');
    }

    private function generateUniqColumnNames($tableNames)
    {
        $mappingTable = include(__DIR__ . '/database_mapping/database_mapping.php');
        $mapping = [];

        foreach ($tableNames as $tableName) {
            $mappingElement = $mappingTable[$tableName];
            if ($mappingElement == null) {
                throw new RuntimeException("mapping for {$tableName} not found");
            }
            $mapping = array_merge($mapping, $mappingTable[$tableName]);
        }

        $lines = [];

        foreach ($mapping as $templateName => $databaseName) {
            $databaseName = $this->db->escapeIdentifier($databaseName);
            $templateName = $this->db->escapeIdentifier($templateName);
            $lines[] = "{$databaseName} AS {$templateName}";
        }

        $sql = implode(',', $lines);

        return $sql;
    }

    /**
     * @return DocuvitaSolutionsTemplateData
     */
    private function generateProjectTemplates()
    {
        $columnNames = $this->generateUniqColumnNames(['adresse', 'projekt']);

        return $this->createTemplatesFromSQL($this->templateProject,
            "SELECT $columnNames FROM projekt LEFT JOIN adresse ON projekt.kunde = adresse.id WHERE projekt.id NOT IN (SELECT e.ref_id FROM docuvitasolutions_export as e WHERE e.ref_type = \"projekt\") LIMIT {$this->sqlLimit}",
            'projekt'
        );
    }

    /**
     * @return DocuvitaSolutionsTemplateData
     */
    private function generateAddressTemplates()
    {
        $columnNames = $this->generateUniqColumnNames(['adresse']);

        return $this->createTemplatesFromSQL($this->templateAddress,
            "SELECT $columnNames FROM adresse WHERE adresse.id NOT IN (SELECT e.ref_id FROM docuvitasolutions_export as e WHERE e.ref_type = \"adresse\") LIMIT {$this->sqlLimit}",
            'adresse'
        );
    }

    /**
     * @return DocuvitaSolutionsTemplateData
     */
    private function generateInvoiceTemplates()
    {
        $columnNames = $this->generateUniqColumnNames(['rechnung', 'pdfarchiv', 'projekt', 'adresse']);

        return $this->createTemplatesFromSQL($this->templateInvoice, <<<SQL
SELECT $columnNames
FROM rechnung
         INNER JOIN pdfarchiv ON pdfarchiv.table_id = rechnung.id
         LEFT JOIN projekt ON rechnung.projekt = projekt.id
         LEFT JOIN adresse ON rechnung.adresse = adresse.id
WHERE pdfarchiv.table_name = "rechnung"
  AND pdfarchiv.keinhintergrund = 0
  AND pdfarchiv.id > {$this->lastArchiveID}
  LIMIT {$this->sqlLimit}
SQL
            ,
            'rechnung'
        );
    }

    /**
     * @return DocuvitaSolutionsTemplateData
     */
    private function generateOrderTemplates()
    {
        $refType = 'auftrag';
        $columnNames = $this->generateUniqColumnNames([$refType, 'pdfarchiv', 'projekt', 'adresse']);

        return $this->createTemplatesFromSQL($this->templateOrder, <<<SQL
SELECT $columnNames
FROM {$refType}
         INNER JOIN pdfarchiv ON pdfarchiv.table_id = {$refType}.id
         LEFT JOIN projekt ON {$refType}.projekt = projekt.id
         LEFT JOIN adresse ON {$refType}.adresse = adresse.id
WHERE pdfarchiv.table_name = "{$refType}"
  AND pdfarchiv.keinhintergrund = 0
  AND pdfarchiv.id > {$this->lastArchiveID}
  LIMIT {$this->sqlLimit}
SQL
            ,
            $refType
        );
    }

    /**
     * @return DocuvitaSolutionsTemplateData
     */
    private function generateOfferTemplates()
    {
        $refType = 'angebot';
        $columnNames = $this->generateUniqColumnNames([$refType, 'pdfarchiv', 'projekt', 'adresse']);

        return $this->createTemplatesFromSQL($this->templateOffer, <<<SQL
SELECT $columnNames
FROM {$refType}
         INNER JOIN pdfarchiv ON pdfarchiv.table_id = {$refType}.id
         LEFT JOIN projekt ON {$refType}.projekt = projekt.id
         LEFT JOIN adresse ON {$refType}.adresse = adresse.id
WHERE pdfarchiv.table_name = "{$refType}"
  AND pdfarchiv.keinhintergrund = 0
  AND pdfarchiv.id > {$this->lastArchiveID}
  LIMIT {$this->sqlLimit}
SQL
            ,
            $refType
        );
    }

    /**
     * @return DocuvitaSolutionsTemplateData
     */
    private function generateOrderInTemplates()
    {
        $refType = 'bestellung';
        $columnNames = $this->generateUniqColumnNames([$refType, 'pdfarchiv', 'projekt', 'adresse']);

        return $this->createTemplatesFromSQL($this->templateOrderIn, <<<SQL
SELECT $columnNames
FROM {$refType}
         INNER JOIN pdfarchiv ON pdfarchiv.table_id = {$refType}.id
         LEFT JOIN projekt ON {$refType}.projekt = projekt.id
         LEFT JOIN adresse ON {$refType}.adresse = adresse.id
WHERE pdfarchiv.table_name = "{$refType}"
  AND pdfarchiv.keinhintergrund = 0
  AND pdfarchiv.id > {$this->lastArchiveID}
  LIMIT {$this->sqlLimit}
SQL
            ,
            $refType
        );
    }

    /**
     * @return DocuvitaSolutionsTemplateData
     */
    private function generateVerbindlichkeitTemplates()
    {
        $refType = 'verbindlichkeit';
        $columnNames = $this->generateUniqColumnNames([$refType, 'datei_stichwoerter', 'projekt', 'adresse']);

        return $this->createTemplatesFromSQL($this->templateVerbindlichkeit, <<<SQL
SELECT $columnNames
FROM {$refType}
         INNER JOIN datei_stichwoerter ON datei_stichwoerter.parameter = {$refType}.id
         LEFT JOIN projekt ON {$refType}.projekt = projekt.id
         LEFT JOIN adresse ON {$refType}.adresse = adresse.id
WHERE datei_stichwoerter.objekt = "{$refType}"
  AND datei_stichwoerter.id > {$this->lastFileID}
  LIMIT {$this->sqlLimit}
SQL
            ,
            $refType
        );
    }

    /**
     * @return DocuvitaSolutionsTemplateData
     */
    private function generateDeliveryNoteTemplates()
    {
        $refType = 'lieferschein';
        $columnNames = $this->generateUniqColumnNames([$refType, 'pdfarchiv', 'projekt', 'adresse']);

        return $this->createTemplatesFromSQL($this->templateDeliveryNote, <<<SQL
SELECT $columnNames
FROM {$refType}
         INNER JOIN pdfarchiv ON pdfarchiv.table_id = {$refType}.id
         LEFT JOIN projekt ON {$refType}.projekt = projekt.id
         LEFT JOIN adresse ON {$refType}.adresse = adresse.id
WHERE pdfarchiv.table_name = "{$refType}"
  AND pdfarchiv.keinhintergrund = 0
  AND pdfarchiv.id > {$this->lastArchiveID}
  LIMIT {$this->sqlLimit}
SQL
            ,
            $refType
        );
    }


    /**
     * @return DocuvitaSolutionsTemplateData
     */
    private function generateCreditTemplates()
    {
        $columnNames = $this->generateUniqColumnNames(['gutschrift', 'pdfarchiv', 'projekt', 'adresse']);

        return $this->createTemplatesFromSQL($this->templateCredit, <<<SQL
SELECT $columnNames
FROM gutschrift
         INNER JOIN pdfarchiv ON pdfarchiv.table_id = gutschrift.id
         LEFT JOIN projekt ON gutschrift.projekt = projekt.id
         LEFT JOIN adresse ON gutschrift.adresse = adresse.id
WHERE pdfarchiv.table_name = "gutschrift"
  AND pdfarchiv.keinhintergrund = 0
  AND pdfarchiv.id > {$this->lastArchiveID}
  LIMIT {$this->sqlLimit}
SQL
            ,
            'gutschrift'
        );
    }

    /**
     * @param string $projectTemplate
     * @param string $query
     * @param string $refType
     *
     * @return DocuvitaSolutionsTemplateData
     */
    private function createTemplatesFromSQL($projectTemplate, $query, $refType)
    {
        if ($this->sqlLimit === 0 || empty(trim($projectTemplate))) {
            return new DocuvitaSolutionsTemplateData('', [], $refType);
        }
        /** @var Application $app */
        $objects = $this->db->fetchAll($query);
        $realObjects = [];

        $data = '';

        foreach ($objects as $object) {
            if (in_array('pdfarchiv___table_id', array_keys($object))) {

                $tableID = $object['pdfarchiv___table_id'];
                $path = implode(str_split($tableID), '/');
                $file = "{$this->app->Conf->WFuserdata}/pdfarchiv/{$this->app->Conf->WFdbname}/{$object['pdfarchiv___doctype']}/{$path}/{$object['pdfarchiv___dateiname']}";

                if (!file_exists($file)) {
                    $this->missingFiles[] = $file;
                    continue;
                };
            } else {
                if ($refType == 'verbindlichkeit') {
                    $fileID = $object['datei_stichwoerter___datei'];
                    $path = $this->getDMSPath($fileID);

                    if (!file_exists($path)) {
                        $this->missingFiles = $path;
                        continue;
                    }
                }
            }

            $realObjects[] = $object;
            $data .= $this->prepareTemplate($projectTemplate, $object);
        }

        $this->sqlLimit -= count($realObjects);

        return new DocuvitaSolutionsTemplateData($data, $realObjects, $refType);
    }

    /**
     * @param int $fileID
     *
     * @return string
     */
    private function getDMSPath($fileID)
    {
        $path = $this->app->erp->GetDMSPath($fileID);
        $path = $path . "/{$fileID}";

        return $path;
    }

    /**
     * @param $template
     * @param $row
     *
     * @return mixed
     */
    private function prepareTemplate($template, $row)
    {
        foreach ($row as $key => $value) {
            $key = str_replace('___', ':', strtoupper($key));
            $template = str_ireplace('{' . $key . '}', htmlspecialchars($value), $template);
        }

        return $template;
    }

    /**
     * @param string $importName
     *
     * @return
     */
    public function generateImportSessionGuid($importName = 'Xentral Import')
    {
        $guid = $this->generateGUID();

        $json = json_encode([
            'BatchImportGuid'        => $guid,
            //TODO give meaningful name
            'ImportName'             => $importName,
            "ImportTagName"          => "Xentral",
            'StartImportAfterUpload' => 'true',
            'UserName'               => $this->username,
            'Password'               => $this->password,
            'SystemReference'        => $this->systemReference,
        ]);


        $curl = curl_init("$this->endpoint/importapi_uploadimportset/?format=json");
        curl_setopt_array($curl, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json),
            ],
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $resultString = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $this->checkCode($code, $resultString);
        $result = json_decode($resultString);

        return $result->UploadGuid;
    }


    /**
     *
     */
    public function testLogin()
    {
        $query = json_encode([
            'UserName'              => $this->username,
            'UserPassword'          => $this->password,
            'SystemReferenceId'     => $this->systemReference,
            // 'PerformIntegratedUserLogin' => true,
            'SessionType'           => 1,
            'LogoutExistingSession' => false,
        ]);
        $curl = curl_init("$this->endpoint/userlogin");
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $query,
        ]);

        $resultString = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $result = json_decode($resultString);
        $this->checkCode($code, $resultString);

        return $result->UploadGuid;
    }

    /**
     * @return string
     */
    private function generateGUID()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
            mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    /**
     * @param $code
     * @param $result
     */
    private function checkCode($code, $result)
    {
        switch ($code){
            case 0: throw new RuntimeException("Server nicht erreichbar");
            case 401:
            case 403: throw new RuntimeException('Zugangsdaten ungültig');
        }
        // check if 2xx http code
        if (((int)$code / 100) !== 2) {
            throw new RuntimeException("HTTP exception $code: $result");
        }
    }

    /**
     * @param DocuvitaSolutionsTemplateData[] $templateObjects
     *
     * @return string
     */
    public function generateZipFromTemplates($templateObjects)
    {
        $objectXml = implode('', array_map(function ($template) {
            /** @var DocuvitaSolutionsTemplateData $template */
            return $template->getTemplate();
        }, $templateObjects));

        $data =
            <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<import>
  <data>
    $objectXml
</data>
</import>
EOF;
        $zipPath = $this->app->getTmpFolder() . 'docuvita.zip';

        $zipFile = new ZipArchive();
        if ($error = $zipFile->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("Fehler beim &Ouml;ffnen der Zip-Datei ({$error})");
        }

        foreach ($templateObjects as $templateObject) {
            if (in_array($templateObject->getRefType(),
                ['rechnung', 'gutschrift', 'auftrag', 'lieferschein', 'bestellung', 'angebot'])) {
                foreach ($templateObject->getQueryResult() as $queryResult) {
                    $tableID = $queryResult['pdfarchiv___table_id'];
                    $path = implode(str_split($tableID), '/');

                    $file = "{$this->app->Conf->WFuserdata}/pdfarchiv/{$this->app->Conf->WFdbname}/{$queryResult['pdfarchiv___doctype']}/{$path}/{$queryResult['pdfarchiv___dateiname']}";
                    $zipFile->addFile($file, $queryResult['pdfarchiv___dateiname']);
                }
            } else {
                if ($templateObject->getRefType() == 'verbindlichkeit') {
                    foreach ($templateObject->getQueryResult() as $queryResult) {
                        $fileID = $queryResult['datei_stichwoerter___datei'];
                        $path = $this->getDMSPath($fileID);

                        $zipFile->addFile($path, $queryResult['datei_stichwoerter___datei']);
                    }
                }
            }
        }
        $zipFile->addFromString('meta.dvImport', $data);

        $zipFile->close();

        return $zipPath;
    }
}

