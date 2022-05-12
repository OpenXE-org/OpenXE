<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php

use Xentral\Modules\DocumentScanner\DataTable\DocScanFilesDataTable;
use Xentral\Widgets\DataTable\DataTableBuildConfig;
use Xentral\Widgets\DataTable\Service\DataTableRequestHandler;

class Docscan
{
    /** @var erpooSystem $app */
    var $app;

    /** @var string MODULE_NAME */
    const MODULE_NAME = 'DocumentScanner';

    /** @var array $javascript */
    public $javascript = [
        './classes/Modules/DocumentScanner/www/js/document-scanner.js',
    ];

    /** @var array $stylesheet */
    public $stylesheet = [
        './classes/Modules/DocumentScanner/www/css/document-scanner.css',
    ];

    /**
     * @param erpooSystem $app
     * @param bool        $intern
     */
    public function __construct($app, $intern = false)
    {
        $this->app = $app;
        if ($intern) {
            return;
        }

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "DocscanList");
        $this->app->ActionHandler("edit", "DocscanEdit");
        $this->app->ActionHandler("preview", "DocscanFilePreview");
        $this->app->DefaultActionHandler("list");
        $this->app->ActionHandlerListen($app);
    }

    public function Install()
    {
        if ($this->app->DB->CheckTableExistence("scanbot")) {
            $this->app->DB->Query("RENAME TABLE `scanbot` TO `docscan`");
            $this->app->DB->Query("UPDATE `datei_stichwoerter` SET objekt = 'DocScan' WHERE objekt = 'scanbot'");
        }

        $this->app->erp->CheckTable("docscan");
        $this->app->erp->CheckColumn("id", "int(11)", "docscan", "NOT NULL AUTO_INCREMENT");
        $this->app->erp->CheckColumn("datei", "int", "docscan");
        $this->app->erp->CheckColumn("kategorie", "text", "docscan");

        $this->app->erp->CheckTable("docscan_metadata");
        $this->app->erp->CheckColumn("id", "INT(10) UNSIGNED", "docscan_metadata", "NOT NULL AUTO_INCREMENT");
        $this->app->erp->CheckColumn("docscan_id", "INT(10) UNSIGNED", "docscan_metadata", "NOT NULL");
        $this->app->erp->CheckColumn("meta_key", "VARCHAR(32)", "docscan_metadata", "NOT NULL");
        $this->app->erp->CheckColumn("meta_value", "VARCHAR(32)", "docscan_metadata", "NOT NULL");
    }

    protected function DocscanMenu()
    {
        $this->app->erp->Headlines('Dokumenten Scanner');
        $this->app->erp->MenuEintrag('index.php?module=docscan&action=list', '&Uuml;bersicht');
        if ($this->app->Secure->GetGET('action') === 'list') {
            $this->app->erp->MenuEintrag('index.php?module=welcome&action=start', 'Zur&uuml;ck zur &Uuml;bersicht');
        } else {
            $this->app->erp->MenuEintrag('index.php?module=docscan&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
        }
    }

    public function DocscanList()
    {
        $cmd = $this->app->Secure->GetGET('cmd');
        if ($cmd === 'drop-file') {
            header('Content-Type: application/json');
            echo $this->DocscanAjaxHandleFileDrop();
            $this->app->erp->ExitWawi();
        }

        $this->DocscanMenu();

        /** @var DataTableRequestHandler $handler */
        $handler = $this->app->Container->get('DataTableRequestHandler');
        $buildConfig = new DataTableBuildConfig(
            'docscan_files',
            DocScanFilesDataTable::class,
            'index.php?module=docscan&action=list&cmd=table'
        );

        if ($cmd === 'table'){
          if ($handler->canHandleRequest($buildConfig)){
            $response = $handler->handleRequest($buildConfig);
            $response->send();
            $this->app->ExitXentral();
          }
        }

        $htmlData = $handler->generateHtml($buildConfig);
        $this->app->Tpl->Set('TAB1', $htmlData);

        // Datei-Zuweisen-Dialog
        $table = new FileTable($this->app, 'adressen', 0);
        $this->app->Tpl->Set('FILEDIALOGCONTENT', $table->GetTabsHtml());

        // Datei-Upload
        $this->DocscanUploadHandling();
        $this->app->Tpl->Set('ID', 0);
        $this->app->Tpl->Add('EXTRASTICHWOERTER', '<option value="Belege">Beleg</option>');
        $this->app->Tpl->Add('EXTRASTICHWOERTER', '<option value="Quittung">Quittung</option>');
        $this->app->Tpl->Add('EXTRASTICHWOERTER', '<option value="Sonstige">Sonstige Datei</option>');
        $this->app->Tpl->Add('EXTRASTICHWOERTER', '<option value="Deckblatt">Deckblatt</option>');
        $this->app->Tpl->Add('EXTRASTICHWOERTER', '<option value="Anhang">Anhang</option>');
        $this->app->Tpl->Parse('DOCSCANUPLOAD', 'datei_neudirekt2.tpl');

        $this->app->Tpl->Parse('PAGE', 'docscan_list.tpl');
    }

    protected function DocscanUploadHandling()
    {
        if (isset($_FILES['upload'])) {

            // Upload über Drag-and-Drop
            if (isset($_POST['dateiv'])) {
                $dateinamen = $this->app->Secure->GetPOST('dateiname');
                $dateititel = $this->app->Secure->GetPOST('dateititel');
                $beschreibungen = $this->app->Secure->GetPOST('beschreibung');
                $stichwoerter = $this->app->Secure->GetPOST('dateistichwort');
                foreach ($_POST['dateiv'] as $k => $v) {
                    $name = $this->app->DB->real_escape_string($dateinamen[$k]);
                    $titel = $this->app->DB->real_escape_string($dateititel[$k]);
                    $beschreibung = $this->app->DB->real_escape_string($beschreibungen[$k]);
                    $stichwort = $this->app->DB->real_escape_string($stichwoerter[$k]);

                    $data = explode(',', $v);
                    $encodedData = str_replace(' ', '+', $data[1]);
                    $decodedData = base64_decode($encodedData);

                    $this->app->Tpl->Set('TITLE', $titel);
                    $this->app->Tpl->Set('BESCHREIBUNG', $beschreibung);

                    if (empty($v)) {
                        $this->app->Tpl->Set('ERROR', "<div class=\"error\">Keine Datei ausgew&auml;hlt!</div>");
                        $this->app->erp->EnableTab("tabs-2");
                    } else {
                        if (empty($titel)) {
                            $titel = $name;
                        }
                        $fileid = $this->app->erp->CreateDatei($name, $titel, $beschreibung, "", $decodedData,
                            $this->app->User->GetName());
                        $this->app->DB->Insert("INSERT INTO docscan (id, datei) VALUES (NULL, '$fileid')");
                        $docscanId = $this->app->DB->GetInsertID();
                        $this->app->erp->AddDateiStichwort($fileid, $stichwort, 'DocScan', $docscanId);
                    }
                }
            }

            // Upload über HTML-Formular
            if (!isset($_POST['dateiv'])) {
                $fileName = $this->app->DB->real_escape_string($_FILES['upload']['name']);
                $fileTmp = $this->app->DB->real_escape_string($_FILES['upload']['tmp_name']);
                $titel = $this->app->DB->real_escape_string($this->app->Secure->GetPOST("titel"));
                $beschreibung = $this->app->DB->real_escape_string($this->app->Secure->GetPOST("beschreibung"));
                $stichwort = $this->app->DB->real_escape_string($this->app->Secure->GetPOST("stichwort"));

                $this->app->Tpl->Set('TITLE', $titel);
                $this->app->Tpl->Set('BESCHREIBUNG', $beschreibung);

                if (empty($fileTmp)) {
                    $this->app->Tpl->Set('ERROR', "<div class=\"error\">Keine Datei ausgew&auml;hlt!</div>");
                    $this->app->erp->EnableTab("tabs-2");
                } else {
                    if (empty($titel)) {
                        $titel = $fileName;
                    }
                    $fileid = $this->app->erp->CreateDatei($fileName, $titel, $beschreibung, "", $fileTmp,
                        $this->app->User->GetName());
                    $this->app->DB->Insert("INSERT INTO docscan (id, datei) VALUES (NULL, '$fileid')");
                    $docscanId = $this->app->DB->GetInsertID();
                    $this->app->erp->AddDateiStichwort($fileid, $stichwort, 'DocScan', $docscanId);
                }
            }
        }
    }

    /**
     * @return string
     */
    protected function DocscanAjaxHandleFileDrop()
    {
        $result = ['success' => false];

        if (isset($_POST['name']) && isset($_POST['data'])) {
            $fileName = $this->app->Secure->GetPOST('name');
            $value = $this->app->Secure->GetPOST('data', null);
            $offset = strpos($value, ',');
            if ($offset === false) {
                return json_encode($result);
            }

            $fileEncoded = base64_decode(substr($value, $offset + 1));
            $fileId = $this->app->erp->CreateDatei($fileName, $fileName, "", "", $fileEncoded,
                $this->app->User->GetName());

            $this->app->DB->Update("INSERT INTO `docscan` (id, datei) VALUES (NULL, '$fileId')");
            $docscanId = $this->app->DB->GetInsertID();

            $this->app->erp->AddDateiStichwort($fileId, 'Bild', 'DocScan', $docscanId);

            $result['success'] = true;
            $result['file'] = (int)$fileId;
        }

        return json_encode($result);
    }

    public function DocscanEdit()
    {
        $cmd = $this->app->Secure->GetGET("cmd");
        if ($cmd === "table-data") {
            header('Content-Type: application/json');
            echo $this->DocscanAjaxGetDataTableResultJson();
            $this->app->erp->ExitWawi();
        }
        if ($cmd === "table-html") {
            header('Content-Type: text/html');
            echo $this->DocscanAjaxGetDataTableHtml();
            $this->app->erp->ExitWawi();
        }
        if ($cmd === "table-settings") {
            header('Content-Type: application/json');
            echo $this->DocscanAjaxGetDataTableSettingsJson();
            $this->app->erp->ExitWawi();
        }
        if ($cmd === "assign-file") {
            header('Content-Type: application/json');
            echo $this->DocscanAjaxAssignFileKeyword();
            $this->app->erp->ExitWawi();
        }
        if ($cmd === "delete-file") {
            header('Content-Type: application/json');
            echo $this->DocscanAjaxDeleteFile();
            $this->app->erp->ExitWawi();
        }
        if ($cmd === "create-liability") {
            header('Content-Type: application/json');
            echo $this->DocscanAjaxCreateLiability();
            $this->app->erp->ExitWawi();
        }
    }

    /**
     * Datei-Stichwort zuweisen
     *
     * @return string
     */
    protected function DocscanAjaxAssignFileKeyword()
    {
        try {
            $keyword = strtolower($this->app->Secure->GetPOST('keyword'));
            $fileId = (int)$this->app->Secure->GetPOST('file');
            $objectId = (int)$this->app->Secure->GetPOST('object');

            if ($fileId === 0) {
                throw new RuntimeException('Ungültige File-ID.');
            }
            if ($objectId === 0) {
                throw new RuntimeException('Ungültige Objekt-ID.');
            }
            if (!in_array($keyword, ['adressen', 'bestellung', 'kasse', 'reisekosten', 'verbindlichkeit'], true)) {
                throw new RuntimeException(sprintf('Stichwort "%s" ist nicht zulässig.', $keyword));
            }

            $this->app->erp->AddDateiStichwort($fileId, 'Belege', ucfirst($keyword), $objectId);
            $result = ['success' => true];

        } catch (Exception $e) {
            $result = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return json_encode($result);
    }

    /**
     * Datei löschen
     *
     * @return string
     */
    protected function DocscanAjaxDeleteFile()
    {
        $fileId = (int)$this->app->Secure->GetPOST('file');

        // Prüfen ob Datei wirklich über DocScan hochgeladen wurde
        $docscanId = (int)$this->app->DB->Select("SELECT d.id FROM docscan AS d WHERE d.datei = '$fileId'");
        if ($docscanId === 0) {
          return json_encode([
            'success' => false,
            'error' => 'Datei konnte nicht gelöscht werden. Datei wurde nicht über den Dokumenten Scanner hochgeladen.',
          ]);
        }

        // Prüfen ob noch andere Verknüpfungen/Stichwörter auf diese Datei existieren
        $usageCount = (int)$this->app->DB->Select(
          "SELECT COUNT(ds.id) AS usage_count 
               FROM datei_stichwoerter AS ds
               WHERE ds.datei = '$fileId' AND ds.objekt != 'DocScan'"
        );
        if ($usageCount > 0) {
          return json_encode([
            'success' => false,
            'error' => 'Datei konnte nicht gelöscht werden. Es existieren Verknüpfungen auf diese Datei.',
          ]);
        }

        $success = $this->app->erp->DeleteDatei($fileId);
        if ($success === false) {
          return json_encode([
            'success' => false,
            'error' => 'Datei konnte aus unbekannten Gründen nicht gelöscht werden. Fehlercode #DOCSCAN01',
          ]);
        }

        $this->app->DB->Delete("DELETE FROM docscan WHERE id = '$docscanId' AND datei = '$fileId' LIMIT 1");

        return json_encode(['success' => $success]);
    }

    /**
     * @return string
     */
    /*protected function DocscanAjaxGetDialogHtml()
    {
      return
        '<div id="filetabs">
        <ul>
          <li data-type="adressen"><a href="#adressen-tab">Adressen</a></li>
          <li data-type="bestellung"><a href="#bestellung-tab">Bestellungen</a></li>
          <li data-type="kasse"><a href="#kasse-tab">Kassenbuch</a></li>
          <li data-type="reisekosten"><a href="#reisekosten-tab">Reisekosten</a></li>
          <li data-type="verbindlichkeit"><a href="#verbindlichkeit-tab">Verbindlichkeiten</a></li>
        </ul>
        <div id="adressen-tab"></div>
        <div id="bestellung-tab"></div>
        <div id="kasse-tab"></div>
        <div id="reisekosten-tab"></div>
        <div id="verbindlichkeit-tab"></div>
      </div>';
    }*/

    /**
     * @return false|string
     */
    protected function DocscanAjaxGetDataTableSettingsJson()
    {
        $fileId = (int)$this->app->Secure->GetGET('id');
        $docType = $this->app->Secure->GetGET('type');
        $table = new FileTable($this->app, $docType, $fileId);

        return json_encode($table->GetSettings(
            sprintf('./index.php?module=docscan&action=edit&cmd=table-data&type=%s&id=%s', $docType, $fileId)
        ));
    }

    /**
     * @return string HTML-Tabelle
     */
    protected function DocscanAjaxGetDataTableHtml()
    {
        $fileId = (int)$this->app->Secure->GetGET('id');
        $docType = $this->app->Secure->GetGET('type');
        $table = new FileTable($this->app, $docType, $fileId);

        return $table->GetTabContentHtml();
    }

    /**
     * @return string
     */
    protected function DocscanAjaxGetDataTableResultJson()
    {
        $fileId = (int)$this->app->Secure->GetGET('id');
        $docType = (string)$this->app->Secure->GetGET('type');
        $columns = (array)$this->app->Secure->GetGET('columns');
        $search = (array)$this->app->Secure->GetGET('search');
        $order = (array)$this->app->Secure->GetGET('order');
        $offset = (int)$this->app->Secure->GetGET('start');
        $limit = (int)$this->app->Secure->GetGET('length');
        $draw = (int)$this->app->Secure->GetGET('draw');

        if ($fileId === 0) {
            return json_encode(['error' => 'Datei-ID darf nicht leer sein']);
        }

        foreach ($columns as $column) {
            if ($column['data'] === 'dateianzahl') {
                if (!empty($column['search']['value'])) {
                    $filter['dateianzahl'] = true;
                } else {
                    $filter['dateianzahl'] = false;
                }
                if ($draw === 1) {
                    $filter['dateianzahl'] = true;
                }
            }
        }

        $table = new FileTable($this->app, $docType, $fileId);
        $searchQuery = !empty($search['value']) ? $search['value'] : null;
        $orderCol = (int)$order[0]['column'];
        $orderDir = strtolower($order[0]['dir']) === 'desc' ? 'DESC' : 'ASC';

        return json_encode($table->GetData($filter, $searchQuery, $orderCol, $orderDir, $offset, $limit, $draw));
    }

    /**
     * @return string
     */
    public function DocscanAjaxCreateLiability()
    {
        $fileId = (int)$this->app->Secure->GetGET('id');
        $liabilityId = (int)$this->app->erp->CreateVerbindlichkeit();

        // Datei-Stichwort zuweisen
        if ($liabilityId > 0) {
            $this->app->erp->AddDateiStichwort($fileId, 'Belege', 'Verbindlichkeit', $liabilityId);
            $result = [
                'success' => true,
                'liability' => $liabilityId,
            ];
        } else {
            $result = ['success' => false];
        }

        return json_encode($result);
    }

    public function DocscanFilePreview()
    {
        $id = (int)$this->app->Secure->GetGET("id");
        $endung = $this->app->erp->GetDateiEndung($id);
        $sendFile = !empty($this->app->Secure->GetGET("sendfile"));

        if ($sendFile === false) {

            $data = [
                'endung' => $endung,
            ];

            if ($endung === 'pdf') {
                $data['iframe_src'] = './js/production/generic/web/viewer.html?file=';
                $data['iframe_src'] .= urlencode('../../../../index.php?module=docscan&action=preview&sendfile=true&id=' . $id);
            } else {
                $data['iframe_src'] = './index.php?module=docscan&action=preview&sendfile=true&id=' . $id;
            }

            header('content-type: application/json');
            echo json_encode($data);
            $this->app->erp->ExitWawi();
        }

        if ($sendFile === true) {
            $this->DisplayFilePreview($id);
        }
    }

    /**
     * @param int $dateiId
     *
     * @return void
     */
    protected function DisplayFilePreview($dateiId)
    {
        $endung = $this->app->erp->GetDateiEndung($dateiId);
        $dateiname = $this->app->erp->GetDateiName($dateiId);

        // Bilder
        if ($endung === 'jpg' || $endung === 'jpeg' || $endung === 'png') {
            switch ($endung) {
                case 'jpg':
                case 'jpeg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
            }
            header('Content-Type: ' . $contentType);
            header('Content-Disposition: inline; filename="' . $dateiname . '"');
            echo $this->app->erp->GetDatei($dateiId);
            $this->app->erp->ExitWawi();
        }

        // PDFs
        if ($endung === 'pdf') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $dateiname . '"');
            echo $this->app->erp->GetDatei($dateiId);
            $this->app->erp->ExitWawi();
        }

        // Fallback bei nicht unterstützter Dateiendung
        $Brief = new SuperFPDF('P', 'mm', 'A4');
        $Brief->filename = "Docscan_{$dateiId}.pdf";
        $Brief->AddPage();
        $Brief->SetTextColor(200);
        $Brief->SetFont('Arial', 'B', 80);
        $Brief->Rotate(45, 45, 180);
        $Brief->Text(45, 180, "DATEI FEHLT");
        $Brief->Rotate(0);
        $Brief->SetTextColor(0);
        $Brief->displayAnhaenge();
        $this->app->erp->ExitWawi();
    }
}
