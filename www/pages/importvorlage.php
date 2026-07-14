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

use Xentral\Components\Exporter\Collection\DataCollection;
use Xentral\Components\Exporter\Csv\CsvConfig;
use Xentral\Components\Exporter\Csv\CsvExporter;
use Xentral\Components\Exporter\Json\JsonExporter;
use Xentral\Components\Exporter\Json\JsonConfig;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\ImportTemplate\Exception\ImportTemplateNotFoundException;
use Xentral\Modules\ImportTemplate\Service\ImportTemplateJsonService;
use Xentral\Modules\ImportTemplate\Exception\InvalidTemplateDataException;

include '_gen/importvorlage.php';

class Importvorlage extends GenImportvorlage {
  /** @var Application $app*/
  var $app;

  /** @var int $limit_datensaetze */
  var $limit_datensaetze;

  /** @var $languageCodesForProperties array */
  protected $languageCodesForProperties;

  const MODULE_NAME = 'ImportMasterdata';

  const FORMAT_CSV = 0;
  const FORMAT_CSV_ZIP = 1;
  const FORMAT_FILES_ZIP = 2;

    /*
        Mapping of numeric fields for conversion
        Regex
    */

  const numeric_fields = array(
        'stuecklistemenge', // 1
        'lieferanteinkaufnetto\D+([\d]+)*\D*', // 3
        'verkaufspreis\D+([\d]+)*\D*', //  10
        'lager_menge_addieren', // 1
        'provision', // 1
        'vk_geplant', // 1
        'ek_geplant', // 1
        'berechneterek', // 1
        'inventurek', // 1
        'gewicht', // 1
        'breite', // 1
        'hoehe', // 1
        'laenge', // 1
        'lager_menge_addieren\D+([\d]+)*\D*', // 5
        'lager_menge_total\D+([\d]+)*\D*', //  5
        'verkaufspreis\D+([\d]+)*\D*netto', //  10
        'verkaufspreis\D+([\d]+)*\D*preisfuermenge', // 10
        'verkaufspreis\D+([\d]+)*\D*menge', // 10
        'verkaufspreisvgruppe', // 10
        'verkaufspreis\D+([\d]+)*\D*kundennummer', // 10
        'pseudopreis', // 1
        'provision\D+([\d]+)*\D*', // 2
    );

    /*
    * List of fields that are handled in the code but not in the switch statement, to suppress warnings
    * Regex
    */
    const handled_fields = array(
        '/nummer/',
        '/lieferantname/',
        '/lieferantnummer/',
        '/datei(?<nummer>\d)/',
        '/dateistichwort(?<nummer>\d)/'
    );

  public $javascript = [
    './classes/Modules/ImportMasterdata/www/js/import_masterdata.js',
  ];

  /**
   * Importvorlage constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    //parent::GenImportvorlage($app);
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->limit_datensaetze=1;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ImportvorlageCreate");
    $this->app->ActionHandler("edit","ImportvorlageEdit");
    $this->app->ActionHandler("import","ImportvorlageImport");
    $this->app->ActionHandler("list","ImportvorlageList");
    $this->app->ActionHandler("delete","ImportvorlageDelete");
    $this->app->ActionHandler("uebersicht","ImportvorlageUebersicht");
    $this->app->ActionHandler("adressen","ImportvorlageAdressen");
    $this->app->ActionHandler("adresseedit","ImportvorlageAdresseEdit");
    $this->app->ActionHandler("rueckgaengig","ImportvorlageRueckgaengig");
    $this->app->ActionHandler("formate","ImportvorlageFormate");
    $this->app->ActionHandler("downloadcsv","ImportvorlageDownloadCsv");
    $this->app->ActionHandler("downloadjson","ImportvorlageDownloadJson");
    $this->app->ActionHandler("copy","ImportvorlageCopy");
    $this->app->ActionHandler("preview","ImportvorlageDownloadPreview");

    $this->app->ActionHandlerListen($app);

    $this->app->erp->Headlines('Daten Import');
  }

  /**
   * @param ApplicationCore $app
   * @param string          $name
   * @param array           $erlaubtevars
   *
   * @return array
   */
  public function TableSearch($app, $name, $erlaubtevars)
  {
    $isAdmin = $this->app->User->GetType() === 'admin';
    $id = (int)$this->app->Secure->GetGET('id');
    switch ($name) {
      case 'importvorlage_list':
        $allowed = ['importvorlage' => ['import','list']];
        $heading = array('','angelegt am','Bearbeiter', 'Template',  'Datei', 'Datens&auml;tze','Fortschritt', 'Status', 'Men&uuml;');
        $width = array('1%','10%', '10%', '10%', '25%', '5%', '5%', '20%',  '1%');
        $datecols = [1];
        $statusMapping =
          [
            'created'     => 'nicht freigeben',
            'in_queue'    => 'freigegeben',
            'cancelled'   => 'abgebrochen',
            'done'        => 'abgeschlossen',
            'complete'    => 'abgeschlossen',
            'error'       => 'Fehler',
            'in_progress' => 'wird ausgeführt',
          ];

        $statusCol = 'CONCAT(';
        foreach($statusMapping as $status => $statusMessage) {
          $statusCol .= sprintf("IF(imd.status = '%s', '%s',",
            $this->app->DB->real_escape_string($status),
            $this->app->DB->real_escape_string($statusMessage)
          );
        }
        $statusCol .= "'')".str_repeat(')', (!empty($statusMapping)?count($statusMapping):0));

        $searchsql = [
          'adr.name',
          "DATE_FORMAT(imd.created_at,'%d.%m.%Y %H:%i:%s')",
          'iv.bezeichnung',
          'imd.filename',
          $statusCol,
        ];
        $findcols = [
          'imd.id',
          'imd.created_at',
          'adr.name',
          'iv.bezeichnung',
          'imd.filename',
          'count_rows',
          'imported_rows',
          $statusCol,
          'imd.id'
        ];
        $menu = '<table cellpadding=0 cellspacing=0><tr><td nowrap>';
        $menu .= '<img src="./themes/'
          .$this->app->Conf->WFconf['defaulttheme']
          .'/images/forward.svg" alt="aktivieren" class="activatejob" data-id="%value%" />';
        $menu .= '&nbsp;';
        $menu .= '<img src="./themes/'
          .$this->app->Conf->WFconf['defaulttheme']
          .'/images/delete.svg" alt="löschen" class="deletejob" data-id="%value%" />';
        $menu .= '</td></tr></table>';
        $menucol = (!empty($heading)?count($heading):0);
        $filterComplete = $this->app->YUI->TableSearchFilter($name,1 ,'importvorlage-filter-complete',0,0,'checkbox');
        $filterCancelled = $this->app->YUI->TableSearchFilter($name,2 ,'importvorlage-filter-cancelled',0,0,'checkbox');
        $sql = "SELECT imd.id,
                           CONCAT('<input type=\"checkbox\" data-id=\"',imd.id,'\" class=\"select\" />'),
        DATE_FORMAT(imd.created_at,'%d.%m.%Y %H:%i:%s'),adr.name,
                           iv.bezeichnung,imd.filename, imd.count_rows,imd.imported_rows,
                           CONCAT($statusCol,' ', imd.message),
                           imd.id
        FROM `importmasterdata` AS `imd`
        INNER JOIN `importvorlage` AS `iv` ON imd.template_id = iv.id
        INNER JOIN `user` AS `u` ON imd.user_id = u.id
        LEFT JOIN `adresse` AS `adr` ON u.adresse = adr.id
        ";
        $where = ' imd.id > 0 ';
        if(!$isAdmin) {
          $where .= sprintf(' AND imd.user_id = %d ', $this->app->User->GetID());
        }
        if($id > 0) {
          $where .= sprintf(' AND imd.template_id = %d ', $id);
        }
        if(!$filterComplete) {
          $where .= " AND imd.status NOT IN ('complete', 'done') ";
        }
        if(!$filterCancelled) {
          $where .= " AND imd.status <> 'cancelled' ";
        }
        if($filterCancelled && $filterCancelled) {
          $where .= " AND imd.status IN ('complete', 'done', 'cancelled') ";
        }
        elseif($filterCancelled) {
          $where .= " AND imd.status = 'cancelled' ";
        }
        elseif($filterComplete) {
          $where .= " AND imd.status IN ('complete', 'done') ";
        }

        $fastcount = "SELECT COUNT(imd.id) FROM `importmasterdata` AS `imd`
        INNER JOIN `importvorlage` AS `iv` ON imd.template_id = iv.id
        INNER JOIN `user` AS `u` ON imd.user_id = u.id
        LEFT JOIN `adresse` AS `adr` ON u.adresse = adr.id ";

        break;
    }

    $erg = [];
    foreach($erlaubtevars as $k => $v) {
      if(isset($$v)){
        $erg[$v] = $$v;
      }
    }
    return $erg;
  }

  public function Install()
  {
    $this->app->erp->CheckTable('importmasterdata');
    $this->app->erp->CheckColumn('user_id', 'INT(11)', 'importmasterdata', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('template_id', 'INT(11)', 'importmasterdata', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('count_rows', 'INT(11)', 'importmasterdata', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('imported_rows', 'INT(11)', 'importmasterdata', 'DEFAULT 0 NOT NULL');
    $this->app->erp->CheckColumn('filename', 'VARCHAR(255)', 'importmasterdata', "DEFAULT '' NOT NULL ");
    $this->app->erp->CheckColumn('status', 'VARCHAR(255)', 'importmasterdata', "DEFAULT 'created' NOT NULL ");
    $this->app->erp->CheckColumn('message', 'VARCHAR(255)', 'importmasterdata', "DEFAULT '' NOT NULL ");
    $this->app->erp->CheckAlterTable(
      "ALTER TABLE `importmasterdata` CHANGE `message` `message` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';"
    );
    $this->app->erp->CheckColumn('created_at', 'TIMESTAMP', 'importmasterdata', 'DEFAULT CURRENT_TIMESTAMP NOT NULL');
    $this->app->erp->CheckIndex('importmasterdata', 'status');
    $this->app->erp->CheckIndex('importmasterdata', 'user_id');
    $this->app->erp->CheckIndex('importmasterdata', 'template_id');
    $this->app->erp->CheckProzessstarter('Importvorlage', 'periodisch', '5', '2017-01-01 00:00:00', 'cronjob', 'importvorlage', 1);
  }

  public function doCronjob()
  {

    $jobs = $this->app->DB->SelectArr(
      sprintf(
        "SELECT * FROM `importmasterdata` WHERE `status` = 'in_queue' ORDER BY `created_at` LIMIT 1"
      )
    );
    if(empty($jobs)) {
      return;
    }
    foreach($jobs as $job) {
      $job = $this->app->DB->SelectRow(
        sprintf(
          "SELECT * FROM `importmasterdata` WHERE `status` = 'in_queue' AND `id` = %d ", $job['id']
        )
      );
      if(empty($job)) {
        continue;
      }
      if(empty($job['filename'])) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE `importmasterdata` SET `status` = 'error', `message` = 'keine Datei' WHERE `id` = %d ",
            $job['id']
          )
        );
        continue;
      }
      if(!is_file($job['filename'])) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE `importmasterdata` SET `status` = 'error', `message` = 'Datei nicht gefunden' WHERE `id` = %d ",
            $job['id']
          )
        );
        continue;
      }
      $parameter = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM importvorlage WHERE id = %d',
          $job['template_id']
        )
      );
      if(empty($parameter)) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE `importmasterdata` SET `status` = 'error', `message` = 'Template nicht gefunden' WHERE `id` = %d ",
            $job['id']
          )
        );
        continue;
      }
      $parameter['stueckliste_csv'] = $job['filename'];
      $parameter['is_cronjob'] = true;
      $parameter['importmasterdata_id'] = $job['id'];

      $result = $this->ImportvorlageDo($parameter['charset'], $parameter); // global_data missing here...

        if ($result['success']) {
            $status = 'done';
        } else {
            $status = 'error';
        }

      $this->app->DB->Update(
        sprintf(
          "UPDATE `importmasterdata` SET `status` = '".$status."', `message` = '".$result['message']."' WHERE `id` = %d ",
          $job['id']
        )
      );
    }
  }

  public function ImportvorlageCopy()
  {

    $id = (int)$this->app->Secure->GetGET('id');

    /** @var ImportTemplateJsonService $importService */
    $importService = $this->app->Container->get('ImportTemplateJsonService');

    try {
      $importTemplate = $importService->getImportTemplate($id);

      $label = $importTemplate->getLabel();
      $importTemplate->updateLabel($label . ' - Kopie');

      $newimportTemplateId = $importService->insertImportTemplate($importTemplate);

      $msg = $this->app->erp->base64_url_encode('<div class="success">Kopie erfolgreich erstellt.</div>');
      $this->app->Location->execute('index.php?module=importvorlage&action=edit&id=' . $newimportTemplateId . '&msg=' . $msg);

    } catch (ImportTemplateNotFoundException $e) {
      $msg = '{|Keine Kopie angelegt. Die ID existiert in der Datenbank nicht.|}';
    } catch (RuntimeException $e) {
      $msg = '{|Die Kopie konnte nicht angelegt werden.|}';
    }

    if(!empty($msg)){
      $msg = $this->app->erp->base64_url_encode('<div class="error">' . $msg . '</div>');
      $this->app->Location->execute('index.php?module=importvorlage&action=list&msg=' . $msg);
    }
  }

  public function ImportvorlageDownloadJson(){

    $id = (int)$this->app->Secure->GetGET('id');
    $this->GenerateJsonDownload($id);
    $this->app->ExitXentral();
  }

  private function GenerateJsonDownload($importvorlageId){

    /** @var ImportTemplateJsonService $importService */
    $importService = $this->app->Container->get('ImportTemplateJsonService');
    $importTemplate = $importService->getImportTemplate($importvorlageId);

    $config = new JsonConfig(JSON_PRETTY_PRINT);
    /** @var JsonExporter $jsonExporter */
    $jsonExporter = new JsonExporter($config);

    $fileName = StringUtil::toFilename($importTemplate->getLabel());

    $handle = $jsonExporter->exportToResource('php://memory', $importTemplate);

    rewind($handle);
    $stat = fstat($handle);
    header('Cache-Control: must-revalidate');
    header('Pragma: must-revalidate');
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="'.$fileName.'.json');
    header('Content-Length: ' . $stat['size']);
    fpassthru($handle);
    fclose($handle);
    $this->app->ExitXentral();
  }

  public function ImportvorlageDownloadCsv(){
    $id = (int)$this->app->Secure->GetGET('id');
    $this->GenerateCsvDownload($id);
    $this->app->ExitXentral();
  }

  /**
   * @param int $patternId
   */
  private function GenerateCsvDownload($patternId){

    $sql = "SELECT fields FROM importvorlage WHERE id=%d";
    $sql = sprintf($sql,$patternId);
    $fields = $this->app->DB->Select($sql);

    if(!empty($fields)){

      $this->cleanFields($fields);
      $colNames = explode(';',$fields);
      foreach ($colNames as $i => $c){
        $colNames[$i] = str_replace(':','',strstr(trim($c),':'));
      }

      $config = new CsvConfig(';', '"');
      $exporter = new CsvExporter($config);
      $data = new DataCollection([$colNames]);
      $handle = $exporter->exportToResource('php://memory', $data);

      rewind($handle);
      $stat = fstat($handle);
      header('Cache-Control: must-revalidate');
      header('Pragma: must-revalidate');
      header('Content-type: text/csv');
      header('Content-Disposition: attachment; filename="tabellenvorlage.csv"');
      header('Content-Length: ' . $stat['size']);
      fpassthru($handle);
      fclose($handle);
      $this->app->ExitXentral();
    }
  }

  /**
   * @param string $fields
   *
   * @return string
   */
  private function cleanFields(string $fields){

    $exploded = preg_split("/[\r\n]+/",$fields);
    $ret = [];
    foreach ($exploded as $e){

      $e = str_replace(strstr($e,';'),'',$e);
      $ret[] = $e;
    }
    $ret = implode(';'.PHP_EOL,$ret).';';
    return $ret;
  }

  public function ImportvorlageFormate(){

    $this->ImportvorlageMenu();
    $this->app->Tpl->Add('STICHWOERTER', implode(', ',array_column($this->app->erp->GetDateiTypen('artikel'),'wert')));
    $this->app->Tpl->Add('DATEIOBJEKTE', implode(', ',array_keys($this->app->erp->GetAllowedDateiObjekte())));
    $this->app->Tpl->Add('SPRACHEN', implode(', ',array_column($this->app->erp->GetSprachenSelect(),'iso')));
    $this->app->Tpl->Add('SPRACHEN', implode(', ',array_keys($this->app->erp->GetSprachenSelect())));

    $allowed_datei_objekte = $this->app->erp->GetAllowedDateiObjekte();

    foreach ($allowed_datei_objekte as $key => $allowed_datei_objekt) {
        $this->app->Tpl->Add('DATEISUCHFELDER', $space.$key.": ".implode(', ',$allowed_datei_objekt['suchfelder']));
        $space = "<br>";
    }

    $this->app->Tpl->Parse('PAGE','importvorlage_formate.tpl');
  }

  public function ImportvorlageAdresseEdit()
  {
    $this->app->Tpl->Parse('TAB1','importvorlage_uebersicht.tpl');
    $this->app->Tpl->Set('TABTEXT','Import');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function ImportvorlageUebersicht()
  {

    $hasRight = $this->app->erp->RechteVorhanden('belegeimport', 'list');
    if(!$hasRight){
      $this->app->Tpl->Set('BELEGEIMPORTSTART','<!--');
      $this->app->Tpl->Set('BELEGEIMPORTEND','-->');
    }
    $this->app->Tpl->Parse('TAB1','importvorlage_uebersicht.tpl');
    $this->app->Tpl->Set('TABTEXT','Import');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function ImportvorlageAdressen()
  {
    $this->app->YUI->TableSearch('TAB1','adresse_import');
    $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=uebersicht','Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->Tpl->Set('TABTEXT','Import');
    $this->app->Tpl->Parse('PAGE','tabview.tpl');
  }

  public function ImportvorlageRueckgaengig()
  {
    $sid = $this->app->Secure->GetGET('sid');
    if($sid <= 0) {
      return;
    }

    $tmp = $this->app->DB->SelectRow(
      sprintf(
        "SELECT *,DATE_FORMAT(`zeitstempel`,'%%d.%%m.%%Y %%H:%%i') as zeit
        FROM `importvorlage_log`
        WHERE `ersterdatensatz` = 1 AND `user`= %d
        ORDER BY `zeitstempel` DESC
        LIMIT 1",
        $this->app->User->GetID()
      )
    );
    if(empty($tmp) || $tmp['id']!=$sid) {
      return;
    }

    $zeitstempel = $this->app->DB->Select(
      sprintf(
        'SELECT `zeitstempel` FROM `importvorlage_log` WHERE `id` = %d LIMIT 1',
        $sid
      )
    );
    $this->app->erp->ImportvorlageLogDelete($zeitstempel);
    $msg=$this->app->erp->base64_url_encode("<div class=\"info\">Import r&uuml;ckg&auml;ngig gemacht.</div>");
    $this->app->Location->execute('index.php?module=importvorlage&action=list&msg='.$msg);
  }

  public function ImportvorlageCreate()
  {
    $this->ImportvorlageMenu();
    parent::ImportvorlageCreate();
  }

  public function ImportvorlageDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    if(is_numeric($id)) {
      $this->app->DB->Delete(
        sprintf(
          'DELETE FROM `importvorlage` WHERE `id` = %d',
          $id
        )
      );
      $msg=$this->app->erp->base64_url_encode("<div class=\"info\">Die Importvorlagen wurde gelöscht!</div>");
      $this->app->Location->execute('index.php?module=importvorlage&action=list&msg='.$msg);
    }
    $this->ImportvorlageList();
  }

  /**
   * @param int $jobId
   *
   * @return array
   */
  protected function getJobById($jobId)
  {
    $job = $this->app->DB->SelectRow(sprintf('SELECT * FROM `importmasterdata` WHERE `id` = %d', $jobId));
    if(empty($job)) {
      $job = [];
    }

    return $job;
  }

  /**
   * @param string $fileName
   *
   * @return bool
   */
  public function isValidJobFile($fileName)
  {
    if(empty($fileName)) {
      return false;
    }
    $folder = $this->app->erp->GetTMP();
    if(stripos($fileName, $folder) !== 0) {
      return false;
    }
    $file = substr($fileName, strlen($folder));
    if(strpos($file, '..') !== false) {
      return false;
    }

    return is_file($fileName);
  }

  /**
   * @param int $jobId
   *
   * @return array
   */
  public function deleteJob($jobId)
  {
    $ret = ['status' => 0];
    $job = $this->getJobById($jobId);
    if(empty($job)) {
      return $ret;
    }
    if($this->isValidJobFile($job['filename'])) {
      @unlink($job['filename']);
    }

    $this->app->DB->Update(
      sprintf(
        "UPDATE `importmasterdata`
        SET `status` = 'cancelled'
        WHERE `id` = %d AND `status` NOT IN ('done', 'complete')",
        $jobId
      )
    );

    if($this->app->DB->affected_rows() > 0) {
      $ret['status'] = 1;
    }

    return $ret;
  }

  /**
   * @param int $jobId
   *
   * @return array
   */
  public function activateJob($jobId)
  {
    $ret = ['status' => 0];
    $job = $this->getJobById($jobId);
    if(empty($job)) {
      return $ret;
    }
    if($this->isValidJobFile($job['filename'])) {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `importmasterdata`
        SET `status` = 'in_queue'
        WHERE `id` = %d",
          $jobId
        )
      );
      if($this->app->DB->affected_rows() > 0) {
        $ret['status'] = 1;
      }
    }
    else {
      $ret['error'] = 'File not found';
    }

    return $ret;
  }

  /**
   * @param string $selection
   * @param array  $jobIds
   *
   * @return array
   */
  protected function batch($selection, $jobIds)
  {
    $ret = ['status' => 0];
    if(empty($jobIds)  || !in_array($selection, ['activate', 'delete'])) {
      return $ret;
    }
    $ret['ok'] = 0;
    $ret['not_ok'] = 0;
    if($selection === 'activate') {
      foreach($jobIds as $jobId) {
        $subRet = $this->activateJob($jobId);
        if(!empty($subRet['status'])) {
          $ret['ok']++;
          $ret['status'] = 1;
        }
        else{
          $ret['not_ok']++;
        }
      }

      return $ret;
    }

    foreach($jobIds as $jobId) {
      $subRet = $this->deleteJob($jobId);
      if(!empty($subRet['status'])){
        $ret['ok']++;
        $ret['status'] = 1;
      }
      else{
        $ret['not_ok']++;
      }
    }
    return $ret;
  }

  public function ImportvorlageList()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    if($cmd === 'batch') {
      $selection = $this->app->Secure->GetPOST('selection');
      $jobIds = $this->app->Secure->GetPOST('jobIds');
      $json = $this->batch($selection, $jobIds);
      header('Content-Type: application/json');
      echo json_encode($json);
      $this->app->ExitXentral();
    }
    if($cmd === 'deletejob') {
      $json = $this->deleteJob($this->app->Secure->GetPOST('jobid'));
      header('Content-Type: application/json');
      echo json_encode($json);
      $this->app->ExitXentral();
    }
    if($cmd === 'activatejob') {
      $json = $this->activateJob($this->app->Secure->GetPOST('jobid'));
      header('Content-Type: application/json');
      echo json_encode($json);
      $this->app->ExitXentral();
    }
    $this->ImportvorlageMenu();
    if($this->app->DB->Select('SELECT COUNT(id) FROM importvorlage') <=0)
    {

   /* Erst ab 17.1 mit Wiedervorlagen und Notizen
      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Wiedervorlagen', '1:datum_faellig;\r\n2:uhrzeit_faellig;\r\n3:kundennummer;\r\n4:mitarbeiternummer;\r\n5:betreff;\r\n6:text;\r\n7:abgeschlossen;', '', 'wiedervorlagen', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");

      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Notizen', '1:datum;\r\n2:uhrzeit;\r\n3:kundennummer;\r\n4:mitarbeiternummer;\r\n5:betreff;\r\n6:text;', '', 'notizen', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");
      */
      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Artikel Stücklisten erstellen (nach normalen Artikelimport)', '1:nummer;\r\n2:stuecklistevonartikel;\r\n3:stuecklistemenge;', '', 'artikel', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");

      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Artikel-Varianten verknüpfen (nach normalen Artikelimport)', '1:nummer;\r\n2:variante_von;', '', 'artikel', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");

      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Artikel', '1:nummer;\r\n2:name_de;\r\n3:artikelbeschreibung_de;\r\n4:kurztext_de;\r\n5:internerkommentar;\r\n6:hersteller;\r\n7:ean;\r\n8:gewicht;\r\n9:lieferantennummer;\r\n10:lieferantbestellnummer;\r\n11:lieferanteinkaufnetto;\r\n12:lieferanteinkaufmenge;\r\n13:verkaufspreis1netto;\r\n14:verkaufspreis1menge;\r\n15:lagerartikel;\r\n16:lager_platz;\r\n17:lager_menge_total;\r\n18:mindestlager;', '', 'artikel', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");

      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Ansprechpartner', '1:kundennummer;\r\n2:name;\r\n3:typ;\r\n4:email;\r\n5:telefon;\r\n6:telefax;\r\n7:mobil;\r\n8:strasse;\r\n9:plz;\r\n10:ort;\r\n11:abteilung;\r\n12:anschreiben;', '', 'adresse', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");

      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Lieferanten', '1:lieferantennummer;\r\n2:firma;\r\n3:typ;\r\n4:strasse;\r\n5:plz;\r\n6:ort;\r\n7:telefon;\r\n8:mobil;\r\n9:telefax;\r\n10:internetseite;\r\n11:ansprechpartner;\r\n12:anschreiben;\r\n13:email;\r\n14:land;\r\n15:sprache;\r\n16:kundennummerlieferant;\r\n17:zahlungsweiselieferant;\r\n18:zahlungszieltagelieferant;\r\n19:zahlungszieltageskontolieferant;\r\n20:zahlungszielskontolieferant;\r\n21:sonstiges;', '', 'adresse', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");

      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Geschäftskunden', '1:kundennummer;\r\n2:firma;\r\n3:typ;\r\n4:strasse;\r\n5:plz;\r\n6:ort;\r\n7:telefon;\r\n8:mobil;\r\n9:telefax;\r\n10:internetseite;\r\n11:ansprechpartner;\r\n12:anschreiben;\r\n13:email;\r\n14:land;\r\n15:sprache;\r\n16:ustid;\r\n17:steuernummer;\r\n18:zahlungsweise;\r\n19:zahlungszieltage;\r\n20:zahlungszieltageskonto;\r\n21:zahlungszielskonto;\r\n22:sonstiges;', '', 'adresse', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");

      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Privatkunden', '1:kundennummer;\r\n2:name;\r\n3:typ;\r\n4:strasse;\r\n5:plz;\r\n6:ort;\r\n7:telefon;\r\n8:mobil;\r\n9:telefax;\r\n10:email;\r\n11:anschreiben;\r\n12:land;\r\n13:sprache;\r\n14:sonstiges;', '', 'adresse', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");


    }
/*
    $tmp = $this->app->DB->SelectArr("SELECT *,DATE_FORMAT(zeitstempel,'%d.%m.%Y %H:%i') as zeit FROM importvorlage_log WHERE ersterdatensatz='1' ORDER by zeitstempel DESC LIMIT 1");

    if($tmp[0]['id'] > 0 && $tmp[0]['tabelle']=="adresse")
    {
      $name_import = $this->app->DB->Select("SELECT bezeichnung FROM importvorlage WHERE id='".$tmp[0]['importvorlage']."' LIMIT 1");
      $user_name = $this->app->DB->Select("SELECT a.name FROM user u LEFT JOIN adresse a ON a.id=u.adresse WHERE u.id='".$tmp[0]['user']."' LIMIT 1");
      $this->app->Tpl->Set('MESSAGE',"<div class=\"info\">Letzter Adressimport: $name_import am ".$tmp[0]['zeit']." Uhr von $user_name (<a href=\"#\" onclick=\"if(!confirm('Wirklich den Import r&uuml;ckg&auml;ngig machen?')) return false; else window.location.href='index.php?module=importvorlage&action=rueckgaengig&sid=".$tmp[0]['id']."';\">Import r&uuml;ckg&auml;ngig machen</a>).</div>");
    }
*/

    $jsonupload = $this->app->Secure->GetPost("jsonupload");

    if(!empty($jsonupload)){
      $msg = $this->installJsonTemplate();

      if(!empty($msg)){
        $msg=$this->app->erp->base64_url_encode('<div class="error">'.$msg.'</div>');
        $this->app->Location->execute('index.php?module=importvorlage&action=list&msg='.$msg);
      }
    }

    $this->app->YUI->TableSearch('TAB1', 'importvorlage');
    $this->app->YUI->TableSearch('TAB2', 'importvorlage_list', 'show', '', '', basename(__FILE__), __CLASS__);
    $this->app->erp->checkActiveCronjob('importvorlage');
    $this->app->Tpl->Parse('PAGE', 'importvorlage_overview.tpl');
    //parent::ImportvorlageList();
  }

  public function installJsonTemplate(){

    $msg='';
    if(!empty($_FILES['jsonfile']['tmp_name'])){
      $jsonfile = $_FILES['jsonfile'];

      if($jsonfile['type']=='application/json'){
        if($jsonfile['size']>0){
          $content = file_get_contents($jsonfile['tmp_name']);
          $templateData = json_decode($content,true);

          /** @var ImportTemplateJsonService $importService */
          $importService = $this->app->Container->get('ImportTemplateJsonService');
          try{
            $templateId = $importService->insertAndValidateImportTemplate($templateData);
            if(empty($templateId)){
              $msg ='{|Fehler beim Anlegen der Vorlage.|}';
            }
          }
          catch(InvalidTemplateDataException $e){
            $msg = '{|Die Vorlagendatei enthält nicht genügend oder falsche Daten. Bitte Bezeichnung und CSV Felder prüfen.|}';
          }
        }
        else{
          $msg ='{|Die Datei hat keinen Inhalt.|}';
        }
      }
      else{
        $msg = '{|Die hochgeladene Datei hat das falsche Dateiformat.|}';
      }
    }
    else{
      $msg = '{|Keine Datei hochgeladen.|}';
    }

    if(empty($msg)){
      $msg = $this->app->erp->base64_url_encode('<div class="success">Vorlage erfolgreich importiert.</div>');
      $this->app->Location->execute('index.php?module=importvorlage&action=edit&id='.$templateId.'&msg='.$msg);
    }

    return $msg;
  }

  public function ImportvorlageMenu()
  {
    $id = $this->app->Secure->GetGET('id');
    $bezeichnung = empty($id)?'':$this->app->DB->Select(
      sprintf(
        'SELECT `bezeichnung` FROM `importvorlage` WHERE `id` = %d LIMIT 1',
        $id
      )
    );
    $this->app->Tpl->Set('KURZUEBERSCHRIFT2',$bezeichnung);

    if($this->app->Secure->GetGET('action')==='list')
    {
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=list', '&Uuml;bersicht');
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=formate&id='.$id,'Formate');
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=uebersicht','Zur&uuml;ck zur &Uuml;bersicht');
    }
    else
    {
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=edit&id='.$id,'Details');
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=import&id='.$id,'Import starten: Datei heraufladen');
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=formate&id='.$id,'Formate');      
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=list','Zur&uuml;ck zur &Uuml;bersicht');
    }

    $this->importVorlagePopup();
  }

  public function importVorlagePopup(){
    $this->app->ModuleScriptCache->IncludeJavascriptFiles('importTemplate', [
      './classes/Modules/ImportTemplate/www/js/importtemplate_json_file_upload.js',
    ]);
  }

  public function ImportvorlageEdit()
  {
    $this->ImportvorlageMenu();

    $id = (int)$this->app->Secure->GetGET('id');
    $this->app->Tpl->Set('CSVDOWNLOADLINK','index.php?module=importvorlage&action=downloadcsv&id='.$id);

    parent::ImportvorlageEdit();
  }

  /**
   * @param int        $id
   * @param null|array $parameter
   *
   * @return bool
   */
  function ImportvorlageGetFieldsNew($id, $parameter = null)
  {
    if(empty($parameter) || empty($parameter['fields'])) {
      $fields = $this->app->DB->Select(
        sprintf(
          'SELECT `fields` FROM `importvorlage` WHERE `id` = %d LIMIT 1',
          $id
        )
      );
    }
    else {
      $fields = $parameter['fields'];
    }
    $fields = $this->cleanFields($fields);

    $colnr = 1; // For self-counting

    $fieldsarray = explode(';',$fields);
    foreach($fieldsarray as $key =>  $fieldsrow) {
      $fieldsarray_items = explode(':',$fieldsrow,3);
      if (count($fieldsarray_items) == 1) { // No separator given -> self-count
        $fieldsarray_items[1] = $fieldsarray_items[0];
        $fieldsarray_items[0] = $colnr++;
      }
      $fieldsarray_items1 = trim(str_replace('!','',$fieldsarray_items[1]));
      if($fieldsarray_items[1]!=""){
        if(strpos($fieldsarray_items[0],'"') === false)
        {
          $erg[$key]['nr'] = trim($fieldsarray_items[0]);
          $erg[$key]['field'] = $fieldsarray_items1;
          if(strpos($fieldsarray_items[1],'!') !== false)
          {
            $erg[$key]['inv'] = true;
          }
        } else {
          $erg[$key]['field'] = $fieldsarray_items1;
          $erg[$key]['vorlage'] = trim(trim($fieldsarray_items[0]),'"');
        }
        if(isset($fieldsarray_items[2]) && trim($fieldsarray_items[2])){
          $erg[$key]['bedingung'] = trim($fieldsarray_items[2]);
        }
      }
    }
    if(isset($erg)) {
      return $erg;
    }

    return false;
  }

  /**
   * @param int $id
   *
   * @return array|null
   */
  function ImportvorlageGetFieldsInverse($id)
  {
    $fields = $this->cleanFields(
      $this->app->DB->Select(
        sprintf(
          'SELECT `fields` FROM `importvorlage` WHERE `id` = %d LIMIT 1',
          $id
        )
      )
    );

    $fieldsarray = explode(';',$fields);
    $cFieldsarray = (!empty($fieldsarray)?count($fieldsarray):0);
    for($i=0;$i<$cFieldsarray;$i++) {
      $fieldsarray_items = explode(':',$fieldsarray[$i]);
      if($fieldsarray_items[1]!=''){
        if(strpos($fieldsarray_items[0],'"') === false){
          if(strpos($fieldsarray_items[1],'!') !== false){
            $csv_fields[$fieldsarray_items[0]] = true;
          }
          else {
            $csv_fields[$fieldsarray_items[0]] = false;
          }
        }
      }
    }

    return $csv_fields;
  }

  /**
   * @param int $id
   *
   * @return bool|array
   */
  function ImportvorlageGetVorlage($id)
  {
    $fields = $this->cleanFields(
      $this->app->DB->Select(
        sprintf(
          'SELECT `fields` FROM `importvorlage` WHERE `id` = %d LIMIT 1',
          $id
        )
      )
    );

    $fieldsarray = explode(';',$fields);
    $cFieldsarray = (!empty($fieldsarray)?count($fieldsarray):0);
    for($i=0;$i<$cFieldsarray;$i++) {
      $fieldsarray_items = explode(':',$fieldsarray[$i]);
      if($fieldsarray_items[1]!=''){
        if(strpos($fieldsarray_items[0],'"') !== false) {
          $vorlage[trim($fieldsarray_items[1])] = trim(trim($fieldsarray_items[0]),'"');
        }
      }
    }
    if(isset($vorlage)) {
      return $vorlage;
    }

    return false;
  }

  /**
   * @param null|int $id
   */
  public function ImportvorlageImport($id = null)
  {
    $_id = $id;
    if(!$_id) {
      $id = (int)$this->app->Secure->GetGET('id');
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=edit&id='.$id,'Details');
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=import&id='.$id,'Import starten: Datei heraufladen');
      $this->app->erp->MenuEintrag('index.php?module=importvorlage&action=formate&id='.$id,'Formate');
    }

    set_time_limit (0);
    $upload = $this->app->Secure->GetPOST('upload');
    $selcharsets = array('UTF8'=>'UTF-8','ISO-8859-1'=>'ISO-8859-1','CP850'=>'CP850');
    $sel = '<select id="selcharset">';
    $charset = $this->app->DB->Select("SELECT charset from importvorlage where id = '$id'");
    if($upload!='') {
      $charset = $this->app->Secure->GetPOST('charset');
    }
    $this->app->Tpl->Set('CHARSET',$charset);
    foreach($selcharsets as $k => $v) {
      $sel .= '<option value="'.$k.'"'.($charset == $k?' selected="selected" ':'').'>'.$v.'</option>';
    }
    $sel .= '</select>';
    $this->app->Tpl->Set('SELCHARSET',$sel);
    $this->app->Tpl->Add('JAVASCRIPT','
        $(document).ready(function() {
          $("#selcharset").on("change",function(){
              $("#charset").val($("#selcharset").val());
              });
          });

        ');

    $importVorlageRow = $this->app->DB->SelectRow(
      sprintf('SELECT * FROM importvorlage WHERE id=%d LIMIT 1',
        (int)$id)
    );
    $bezeichnung = $importVorlageRow['bezeichnung'];
    $importtrennzeichen = $importVorlageRow['importtrennzeichen'];
    $importerstezeilenummer = $importVorlageRow['importerstezeilenummer'];
    $importdatenmaskierung = $importVorlageRow['importdatenmaskierung'];
    $importzeichensatz = $importVorlageRow['importzeichensatz'];
    $fields = $this->cleanFields($importVorlageRow['fields']);
    $ziel = $importVorlageRow['ziel'];
    $format = $importVorlageRow['format'];
    $utf8decode = 0;//$this->app->DB->Select("SELECT utf8decode FROM importvorlage WHERE id='$id' LIMIT 1");

    $fieldset = $this->ImportvorlageGetFieldsNew($id);

    $importtrennzeichen = $this->formatSeprator($importtrennzeichen);

    if($importdatenmaskierung==='gaensefuesschen') {
      $importdatenmaskierung='"';
    }

    $global_data['dateitypen_artikel'] = $this->app->erp->getDateiTypen('artikel');
    $global_data['dateiobjekte'] = $this->app->erp->GetAllowedDateiObjekte();

    if($upload!='') {
      $isCronjobActive = $this->app->DB->Select(
        "SELECT `id` FROM `prozessstarter` WHERE `aktiv` = 1 AND `parameter` = 'importvorlage' LIMIT 1"
      );
      $uploaded_file_name = $this->app->erp->GetTMP().'importvorlage'.$this->app->User->GetID();
      $uploaded_file_original_name = $_FILES['userfile']['name'];

      if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaded_file_name)) {
        //$importfilename = $_FILES['userfile']['name'];
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Datei '".$uploaded_file_name."' konnte nicht ge&ouml;ffnet werden. Eventuell ist die Datei zu gro&szlig; oder die Schreibrechte stimmen nicht!</div>  ");
        $this->app->Location->execute("index.php?module=importvorlage&action=import&id=$id&msg=$msg");
      }

      ini_set('auto_detect_line_endings', true);

        $additional_files = array();
        $stueckliste_csv = null;

        switch ($format) {
            case SELF::FORMAT_FILES_ZIP:
            // break omitted
            case SELF::FORMAT_CSV_ZIP:
                if (strtolower(pathinfo($uploaded_file_original_name, PATHINFO_EXTENSION)) != 'zip') {
                    $this->app->Tpl->AddMessage('ERROR', 'Datei muss .zip Endung haben.');
                    break;
                }
                $zip = new ZipArchive();
                if ($zip->open($uploaded_file_name, ZipArchive::CHECKCONS) !== true) {
                    throw new Exception(sprintf('Failure to open file "%s"', $uploaded_file_name));
                }
                $unzipped_files_folder = $this->app->erp->GetTMP()."importupload_".uniqid();
                if (!file_exists($unzipped_files_folder) && !@mkdir($unzipped_files_folder) && !is_dir($unzipped_files_folder)) {
                    throw new Exception(sprintf('Failure to create directory "%s"', $unzipped_files_folder));
                }
                for( $i = 0; $i < $zip->numFiles; $i++ ) {
                    $stat = $zip->statIndex($i);
                    $filesize = $stat['size'];
                    $filename = $stat['name'];

                    if ($filesize > 0) {
                        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) == 'csv' && empty($stueckliste_csv)) {
                            $stueckliste_csv = $unzipped_files_folder."/".$filename;
                            $zip->extractTo($unzipped_files_folder, $filename);
                            if ($format == SELF::FORMAT_CSV_ZIP) {
                                break;
                            }
                        } else if ($format == SELF::FORMAT_FILES_ZIP) {
                            $zip->extractTo($unzipped_files_folder, $filename);
                            $additional_files[] = array('pathinzip' => $filename, 'path' => $unzipped_files_folder."/".$filename);
                        }
                    }
                }
                $zip->close();       
            break;
            case SELF::FORMAT_CSV:
            // break omitted
            default:
                if (strtolower(pathinfo($uploaded_file_original_name, PATHINFO_EXTENSION)) != 'csv') {
                    $this->app->Tpl->AddMessage('ERROR', 'Datei muss .csv Endung haben.');
                    break;
                }
                $stueckliste_csv = $uploaded_file_name;
            break;
        }
        $global_data['additional_files'] = $additional_files;
        $this->app->User->SetParameter('importvorlage_stueckliste', $stueckliste_csv);
        $this->app->User->SetParameter('importvorlage_additional_files', serialize($additional_files));

        if (!empty($stueckliste_csv)) {
            if (($handle = fopen($stueckliste_csv, 'r')) !== FALSE) {
                $rowcounter = 0;
                $rowcounter_real = 0;
                $create_count = 0;
                $update_count = 0;
                $prepare_result = array();
                while (($data = fgetcsv($handle, 0, $importtrennzeichen)) !== FALSE) {
                    $rowcounter++;
                    if($rowcounter >= $importerstezeilenummer) {
                      $rowcounter_real++;
                      foreach($data as $key=>$value) {
                        if($charset && strtoupper($charset) !== 'UTF-8' && strtoupper($charset) !== 'UTF8') {
                          $data[$key] = iconv($charset, 'UTF-8', $data[$key]."\0") ;
                        }
                      }
                      foreach($data as $key=>$value) {
                        $data[$key] = trim( $data[$key] );
                        $data[$key] = str_replace('""', '"', $data[$key]);
                        $data[$key] = preg_replace("/^\"(.*)\"$/sim", "$1", $data[$key]);
                      }
                      $this->ImportPrepareRow($rowcounter, $ziel, $data, $fieldset, $create_count, $update_count, $prepare_result, global_data: $global_data);
                    }
                }
            } else {
                throw new Exception(sprintf('Failure to open file "%s"', $stueckliste_csv));
            }
            fclose($handle);

            $jobId = 0;
            if($isCronjobActive){
              $jobId = $this->create($this->app->User->GetID(), $id, $stueckliste_csv, $rowcounter_real);
            }
      
            $preview_headings = array_merge(['Zeile','Nummer','Aktion','Info','|'],array_column($prepare_result[0]['values'],'field'));
            $action_translate = array('none' => 'Keine', 'create' => 'Neu', 'update' => 'Aktualisieren');

            $preview_data = "";
            $preview_data .= $importdatenmaskierung.implode($importdatenmaskierung.$importtrennzeichen.$importdatenmaskierung,$preview_headings).$importdatenmaskierung.PHP_EOL;
            foreach ($prepare_result as $prepare_row) {
                $row = array_merge(array($prepare_row['row'],$prepare_row['nummer'],$action_translate[$prepare_row['action']],$prepare_row['action_anzeige'],'|'),array_column($prepare_row['values'],'value'));
                $preview_data .= $importdatenmaskierung.implode($importdatenmaskierung.$importtrennzeichen.$importdatenmaskierung,$row).$importdatenmaskierung.PHP_EOL;
            }
            $preview_file_name = $this->app->erp->GetTMP().'importvorschau'.$this->app->User->GetID();
            file_put_contents($preview_file_name, $preview_data);

            $et = new EasyTable($this->app);
            $et->headings = $preview_headings;

            foreach ($prepare_result as $prepare_row) {
                $row = array_merge(array($prepare_row['row'],$prepare_row['nummer'],$action_translate[$prepare_row['action']],$prepare_row['action_anzeige'],'|'),array_column($prepare_row['values'],'value'));
                $et->AddRow($row);
                if (++$prepare_row_limit == 50) {
                    $limit_erreicht = true;
                    break;            
                }
            }
            $et->DisplayNew('ERGEBNIS',"");

            if ($limit_erreicht) {
                $limit_text = '<i>Vorschau: Es werden aktuell nur 50 von <b>'
                . $rowcounter_real . '</b> Datens&auml;tzen angezeigt. Importiert werden aber alle ';
            }

            $this->app->Tpl->Add(
                'IMPORTBUTTON',
                '<div class="info"><input type="submit" name="import" value="importieren">'
                . $limit_text
                . $rowcounter_real . ' Datens&auml;tze, davon '.$create_count.' neu, '.$update_count.' ge&auml;ndert.<input type="hidden" name="importdateiname" value="'
                . $stueckliste_csv . '"><input type="hidden" name="jobid" value="'.$jobId.'" />'
                . ' <a href="index.php?module=importvorlage&action=preview">Vorschau als CSV herunterladen</a>'
                . '</div>'
            );
            
        } // !empty($stueckliste) file ready
    } // upload

    $import = $this->app->Secure->GetPOST('import');
    if($jobId > 0 && !empty($upload) && empty($import)) {
      $this->app->Tpl->Add(
        'MESSAGE',
        '<div class="info">Import wurde an die Warteschlange erfolgreich &uuml;bergeben.
        Klicken sie auf &quot;Importieren$quot; um den Import zu starten.</div>'
      );
    }
    $jobId = (int)$this->app->Secure->GetPOST('jobid');
    if($jobId > 0 && !empty($import)) {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `importmasterdata` SET `status` = 'in_queue' WHERE `id` = %d",
          $jobId
        )
      );
      $this->app->Tpl->Set('MESSAGE', '<div class="info">Import an Prozessstarter &uuml;bergeben.</div>');
    }
    elseif($import!='') {
      $result = $this->ImportvorlageDo($charset, array('stueckliste_csv' => $stueckliste_csv), global_data: $global_data);
      if(is_file($stueckliste_csv)) {
        unlink($stueckliste_csv);
      }

      if ($result['success']) {
        $this->app->Tpl->AddMessage('success',"Import durchgef&uuml;hrt: ".$result['rows']." Zeilen.");
        if (!empty($result['message'])) {
            $this->app->Tpl->AddMessage('info',$result['message'], html: true);
        }
      } else {
        $this->app->Tpl->AddMessage('error',"Import fehlerhaft: ".$result['message']."", html: true);
      }
    }

    if(!$_id) {
      $this->app->Tpl->Set('KURZUEBERSCHRIFT2',$bezeichnung);
      $this->app->Tpl->Parse('TAB1','importvorlage_import.tpl');
      $this->app->Tpl->Set('TABTEXT', 'Import');
      $this->app->Tpl->Set('TABTEXT2', 'Warteschlange');
      $this->app->YUI->TableSearch('TAB2', 'importvorlage_list', 'show','','',basename(__FILE__), __CLASS__);
      $this->app->Tpl->Parse('PAGE','tabview2.tpl');
    }
  }

  /**
   * @param int    $userId
   * @param int    $templateId
   * @param string $uploadFile
   * @param int    $countRows
   *
   * @return int
   */
  public function create($userId, $templateId, $uploadFile, $countRows)
  {
    if($userId <= 0 || $templateId <= 0 || empty($uploadFile) || !is_file($uploadFile)) {
      return 0;
    }

    $uploadFileTo = $uploadFile.uniqid('importvorlge', true);
    if(!rename($uploadFile, $uploadFileTo)) {
      return 0;
    }

    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `importmasterdata`
        (user_id, template_id, count_rows, imported_rows, filename, status, created_at)
        VALUES (%d, %d, %d, 0, '%s', 'created',NOW())",
        $userId, $templateId, $countRows, $this->app->DB->real_escape_string($uploadFileTo)
      )
    );

    return (int)$this->app->DB->GetInsertID();
  }

  /**
   * @param string $seprator
   *
   * @return string
   */
  public function formatSeprator($seprator) {
    if($seprator==='semikolon') {
      return ';';
    }
    if($seprator==='komma') {
      return ',';
    }

    return $seprator;
  }

  /**
   * @param string $fieldstring
   * @param int    $firstRowToImport
   * @param string $importtrennzeichen
   * @param string $csvFile
   *
   * @return array
   */
  public function getFields($fieldstring, $firstRowToImport, $importtrennzeichen = ';', $csvFile = '')
  {
    $csv_fields_keys = [];
    $csv_fields_name = [];
    $vorlage = [];

    $fieldsarray = explode(';',$fieldstring);
    foreach($fieldsarray as $field) {
      //for($i=0;$i<(!empty($fieldsarray)?count($fieldsarray):0);$i++)
      //{
      $fieldsarray_items = explode(':',$field);
      foreach($fieldsarray_items as $k => $v) {
        $fieldsarray_items[$k] = trim($v);
      }
      if((!empty($fieldsarray_items)?count($fieldsarray_items):0) > 1){
        if($fieldsarray_items[1] != ''){
          if($fieldsarray_items[0] == (int)$fieldsarray_items[0]){
            //$csv_fields[$fieldsarray_items[0]]= $fieldsarray_items[1];
            $csv_fields_keys[] = $fieldsarray_items[0];
            $csv_fields_name[] = $fieldsarray_items[1];
          }else{
            $vorlage[$fieldsarray_items[1]] = trim($fieldsarray_items[0], '"');
          }
        }
      }
    }

    if(!empty($fieldsarray) && empty($csv_fields_keys) && empty($csv_fields_name) && empty($vorlage) && $firstRowToImport > 1 && $csvFile != '' && is_file($csvFile)) {
      if (($handle = fopen($csvFile, 'r')) !== FALSE)
      {
        if (($row = fgetcsv($handle, 0, $importtrennzeichen)) !== FALSE) {
          foreach($row as $key => $value) {
            $value = trim($value);
            if($value == '') {
              continue;
            }
            $csv_fields_keys[] = $key + 1;
            $csv_fields_name[] = $value;
          }
        }
        fclose($handle);
      }
    }

    return [$csv_fields_keys, $csv_fields_name, $vorlage];
  }

  /**
   * @param string $stueckliste_csv
   * @param int    $id
   * @param string $charset
   * @param null   $parameter
   *
   * @return mixed
   */
  function ImportvorlageGetCSV($stueckliste_csv,$id, $charset = '', $parameter = null)
  {
    // einlesen von der CSV Datei
    if(empty($parameter) || !empty($id)) {
      $importVorlageRow = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM importvorlage WHERE id= %d LIMIT 1',
          $id)
      );
    }
    if(empty($parameter) || empty($parameter['fields'])) {
      $fieldstmp = $importVorlageRow['fields'];
    }
    else{
      $fieldstmp = $parameter['fields'];
    }

    $fieldstmp = $this->cleanFields($fieldstmp);

    if(empty($parameter) || empty($parameter['importtrennzeichen'])) {
      $importtrennzeichen = $importVorlageRow['importtrennzeichen'];
    }
    else {
      $importtrennzeichen = $parameter['importtrennzeichen'];
    }
    if(empty($parameter) || empty($parameter['importerstezeilenummer'])) {
      $importerstezeilenummer = $importVorlageRow['importerstezeilenummer'];
    }
    else{
      $importerstezeilenummer = $parameter['importerstezeilenummer'];
    }
    if(empty($parameter) || empty($parameter['importdatenmaskierung'])) {
      $importdatenmaskierung = $importVorlageRow['importdatenmaskierung'];
    }
    else{
      $importdatenmaskierung = $parameter['importdatenmaskierung'];
    }
    if(empty($parameter) || empty($parameter['importzeichensatz'])) {
      $importzeichensatz = $importVorlageRow['importzeichensatz'];
    }
    else{
      $importzeichensatz = $parameter['importzeichensatz'];
    }
    $utf8decode = 0;
    $importtrennzeichen = $this->formatSeprator($importtrennzeichen);

    if($importdatenmaskierung==='gaensefuesschen') {
      $importdatenmaskierung='"';
    }

    list($csv_fields_keys, $csv_fields_name, $vorlage) = $this->getFields(
      $fieldstmp,
      $importerstezeilenummer,
      $importtrennzeichen,
      $stueckliste_csv
    );

    $number_of_fields = (!empty($csv_fields_keys)?count($csv_fields_keys):0);
    if (($handle = fopen($stueckliste_csv, 'r')) !== FALSE) {
      $rowcounter = 0;
      $rowcounter_real = 0;
      while (($data = fgetcsv($handle, 0, $importtrennzeichen)) !== FALSE) {
        $rowcounter++;
        $num = (!empty($data)?count($data):0);
        if($rowcounter >= $importerstezeilenummer) {
          if(empty($data)) {
            continue;
          }
          $isempty = true;
          foreach($data as $key=>$value) {
            if(!empty($value)) {
              $isempty = false;
            }
            if($charset && strtoupper($charset) !== 'UTF-8' && strtoupper($charset) !== 'UTF8') {
              $data[$key] = iconv($charset, 'UTF-8', $value."\0");
            }
          }
          if($isempty) {
            continue;
          }
          $rowcounter_real++;
          foreach($data as $key=>$value) {
            $data[$key] = trim( $data[$key] );
            $data[$key] = str_replace('""', '"', $data[$key]);
            $data[$key] = preg_replace("/^\"(.*)\"$/sim", "$1", $data[$key]);
          }

          for($j=0;$j<$number_of_fields;$j++) {
            $value = trim($data[($csv_fields_keys[$j]-1)]);
            //$fieldname = $csv_fields[$csv_fields_keys[$j]];
            $fieldname = $csv_fields_name[$j];
            $tmp[$fieldname][$rowcounter_real] = $value;
            $tmp['cmd'][$rowcounter_real] = 'create';
            $tmp['checked'][$rowcounter_real] = 1;

            if($fieldname === 'ean' && $value != ''){
              if(!isset($tmp['nummer'][$rowcounter_real]) && !$tmp['nummer'][$rowcounter_real]){
                $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE ean='$value' AND ean<>'' AND geloescht <> 1 LIMIT 1");
                if(!is_array($nummer) && $nummer){
                  $tmp['nummer'][$rowcounter_real] = $nummer;
                  $tmp['cmd'][$rowcounter_real] = 'update';
                }
              }
            }
          }
          if(isset($vorlage)) {
            foreach($vorlage as $k => $v) {
              $tmp[$v][$rowcounter_real] = $k;
            }
          }
        }
      }
      $number_of_rows = $rowcounter;
      fclose($handle);
      if(empty($parameter['nodelete'])){
// TEST
//        unlink($stueckliste_csv);
// TEST
      }
    } else {
        return null;
    }
    return $tmp;
  }

  /**
   * @param string     $charset
   * @param array|null $parameter
   *
   * @return array('success', 'message', result_objects array('id','type'), 'rows')
   */
  public function ImportvorlageDo($charset = '', $parameter = null, $global_data = array())
  {
    $importvorlagedoresult = array('success' => true, 'messages' => array(), 'result_objects' => array(), 'rows' => 0);
    $id = 0;
    if(empty($parameter) || !isset($parameter['id'])) {
      $id = $this->app->Secure->GetGET('id');
    } else {
        $id = $parameter['id'];
    }
    $isCronjob = !empty($parameter['is_cronjob']) && !empty($parameter['importmasterdata_id']);
    if(empty($parameter['ziel'])) {
      $ziel = $this->app->DB->Select("SELECT ziel FROM importvorlage WHERE id='$id' LIMIT 1");
    }
    else {
      $ziel = $parameter['ziel'];
    }
    $fieldset = $this->ImportvorlageGetFieldsNew($id, $parameter);
    if(empty($parameter) || !isset($parameter['row'])){
      $tmp = $this->app->Secure->GetPOST('row');
    }
    else {
      $tmp = $parameter['row'];
    }
    $ekpreisaenderungen = 0;
    $vkpreisaenderungen = 0;

    if(empty($parameter['stueckliste_csv'])) {
        $stueckliste_csv = $this->app->User->GetParameter('importvorlage_stueckliste');
        $additional_files = unserialize($this->app->User->GetParameter('importvorlage_additional_files'));
        $global_data['additional_files'] = $additional_files;
    }
    else {
      $stueckliste_csv = $parameter['stueckliste_csv'];
    }

    if($stueckliste_csv != '') {
      $tmp = $this->ImportvorlageGetCSV($stueckliste_csv,$id,$charset, $parameter);
    } else {
        return(array('success' => false, 'message' => 'Keine Datei angegeben.'));
    }

    if ($tmp === null) {
        return(array('success' => false, 'message' => 'Datei konnte nicht verarbeitet werden.'));
    }

    $result_objects = array(); // ('id', 'type')

    $ersterdatensatz = 1;
    $zeitstempel = time();

    $number_of_rows = empty($tmp['cmd'])?0:count($tmp['cmd']);
//    $number_of_rows = $number_of_rows + 2; // ?!?!

    if($isCronjob) {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `importmasterdata` SET `status` = 'in_progress' WHERE `id` = %d ",
          $parameter['importmasterdata_id']
        )
      );
    }

    for($i=1;$i<=$number_of_rows;$i++) {
      unset($felder);
      unset($lieferantid);
      unset($kundenid);
      unset($artikelid);
      unset($adressid);
      if(isset($tmp['nummer']) && isset($tmp['nummer'][$i])) {
        $tmp['nummer'][$i] = str_replace(' ','',trim($tmp['nummer'][$i]));
      }
      if(isset($tmp['matrixproduktvon']) && isset($tmp['matrixproduktvon'][$i])) {
        $tmp['matrixproduktvon'][$i] = trim($tmp['matrixproduktvon'][$i]);
      }
      if(isset($tmp['kundennummer']) && isset($tmp['kundennummer'][$i])) {
        $tmp['kundennummer'][$i] = str_replace(' ','',trim($tmp['kundennummer'][$i]));
      }

      // Export compatibility
      if(isset($tmp['lieferantnummer'][$i]) && !isset($tmp['lieferantennummer'][$i])) {
        $tmp['lieferantennummer'][$i] = $tmp['lieferantennummer'][$i];
      }

      if(isset($tmp['lieferantennummer']) && isset($tmp['lieferantennummer'][$i])) {
        $tmp['lieferantennummer'][$i] = str_replace(' ','',trim($tmp['lieferantennummer'][$i]));
      }
      if($tmp['lieferantennummer'][$i]!='' && ($tmp['kundennummer'][$i]!=='NEW' || $tmp['kundennummer'][$i]!=='NEU')) {
        $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$this->app->DB->real_escape_string($tmp['lieferantennummer'][$i])."'
            AND lieferantennummer!='' LIMIT 1");
      }

      if($tmp['kundennummer'][$i]!='' && ($tmp['kundennummer'][$i]!=='NEW' || $tmp['kundennummer'][$i]!=='NEU')) {
        $kundenid = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$this->app->DB->real_escape_string($tmp['kundennummer'][$i])."' AND kundennummer!='' LIMIT 1");
      }

      if($kundenid<=0) {
        $kundenid=0;
      }
      if($lieferantid<=0) {
        $lieferantid=0;
      }

      if($lieferantid<=0 && $tmp['lieferantname'][$i]!='') {
        $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE name='".$this->app->DB->real_escape_string($tmp['lieferantname'][$i])."' LIMIT 1");
      }

      if($ziel!=='adresse') {
        if(!empty($tmp['nummer'][$i])){
          $articleNumber = $this->app->DB->real_escape_string($tmp['nummer'][$i]);
          $artikelid = $this->app->DB->Select(
            "SELECT `id` FROM `artikel`
                WHERE `nummer`= '{$articleNumber}'
                AND `nummer` != ''
                AND `nummer` != 'DEL'
                AND `geloescht` <> 1
                LIMIT 1"
          );
        }
        elseif(!empty($tmp['ean'][$i])) {
            $ean = $this->app->DB->real_escape_string($tmp['ean'][$i]);
            $artikelid = $this->app->DB->Select(
                "SELECT `id`
                FROM `artikel`
                WHERE `herstellernummer`= '{$ean}'
                AND `herstellernummer` != ''
                AND `nummer` != 'DEL'
                AND `geloescht` <> 1
                LIMIT 1"
            );
        }
        elseif(!empty($tmp['herstellernummer'][$i])) {
          $supplierArticleNumber = $this->app->DB->real_escape_string($tmp['herstellernummer'][$i]);
          $supplierName = $this->app->DB->real_escape_string($tmp['hersteller'][$i]);
          $artikelid = $this->app->DB->Select(
            "SELECT `id`
            FROM `artikel`
            WHERE `herstellernummer`= '{$supplierArticleNumber}'
                AND `hersteller` = '{$supplierName}'
                AND `herstellernummer` != ''
                AND `nummer` != 'DEL'
                AND `geloescht` <> 1
                LIMIT 1"
          );
        }
      }

      if($ziel === 'artikel')
      {
        foreach($fieldset as $k => $v) {
          $bedingung = '';
          $value = '';
          $fieldname = '';
          if(isset($fieldset[$k]['bedingung'])) {
            $bedingung = $fieldset[$k]['bedingung'];
          }
          if(trim(strtolower($bedingung)) === 'unique') {
            if($v['field'] && isset($tmp[$v['field']]) && isset($tmp[$v['field']][$i]) && $tmp[$v['field']][$i]) {
              if(!isset($artikelid) || !$artikelid){
                $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE " . $v['field'] . "='" . $this->app->DB->real_escape_string($tmp[$v['field']][$i]) . "' AND nummer!='' LIMIT 1");
              }
            }
          }
        }
      }

      if($ziel === 'adresse') {
        foreach($fieldset as $k => $v) {
          $bedingung = '';
          $value = '';
          $fieldname = '';
          if(isset($fieldset[$k]['bedingung'])) {
            $bedingung = $fieldset[$k]['bedingung'];
          }elseif(isset($v['vorlage'])) {
            $tmp[$v['field']][$i] = $v['vorlage'];
          }
          if(trim(strtolower($bedingung)) === 'unique') {
            if($v['field'] && isset($tmp[$v['field']]) && isset($tmp[$v['field']][$i]) && $tmp[$v['field']][$i])
            {
              $adressid = $this->app->DB->Select("SELECT id FROM adresse WHERE ".$v['field']."='".$this->app->DB->real_escape_string($tmp[$v['field']][$i])."' LIMIT 1");
              if($adressid)
              {
                if(isset($tmp['kundennummer'][$i]) && (strtoupper(trim($tmp['kundennummer'][$i])) === 'NEW' || strtoupper(trim($tmp['kundennummer'][$i])) === 'NEU'))
                {
                  $kundenid = $adressid;
                  $tmp['kundennummer'][$i] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id = '$adressid' LIMIT 1");
                }
                if(isset($tmp['lieferantennummer'][$i]) && (strtoupper(trim($tmp['lieferantennummer'][$i])) === 'NEW' || strtoupper(trim($tmp['lieferantennummer'][$i])) === 'NEU'))
                {
                  $lieferantid = $adressid;
                  $tmp['lieferantennummer'][$i] = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id = '$adressid' LIMIT 1");
                }
              }
            }
          }
        }
      }
      if($ziel === "einkauf") {
        foreach($fieldset as $k => $v) {
          $bedingung = "";
          $value = "";
          $fieldname = "";
          if(isset($fieldset[$k]['bedingung'])) {
            $bedingung = $fieldset[$k]['bedingung'];
          }
          if(trim(strtolower($bedingung)) === 'sonstiges' && $v['field'] === 'lieferantennummer' && $tmp[$v['field']][$i] != '') {
            $tmp['lieferantennummer'][$i] = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE sonstiges='".$this->app->DB->real_escape_string($tmp[$v['field']][$i])."' LIMIT 1");
            $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$this->app->DB->real_escape_string($tmp['lieferantennummer'][$i])."' LIMIT 1");
          }
        }
      }

        // Convert numeric_fields
        foreach($fieldset as $k => $v) {
            foreach (SELF::numeric_fields as $numeric_field) {
                if (preg_match("/".$numeric_field."/",$v['field'])) {
                    $tmp[$v['field']][$i] = str_replace(',','.',$tmp[$v['field']][$i]);
                }
            }
        }

      // HERE START OF PROCESSING OF THE ROWS
      // INSIDE FOR LOOP
      // $i -> loop counter row number starting with 1
      // $number_of_rows
      // access data -> $tmp['column_name'][$i]
      // $tmp['cmd'] -> create or update
      // $tmp['checked'] -> 0 or 1

      switch($ziel)
      {
        case "einkauf":
        case "artikel":
          // START NEW CODE CREATE ARTIKEL
          if(empty($artikelid) && $tmp['cmd'][$i] == 'create')
          {
            $projektid = 0;
            if($tmp['projekt'][$i]!='')
            {
              $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$this->app->DB->real_escape_string($tmp['projekt'][$i])."' AND abkuerzung!='' LIMIT 1");
            }
            if( strtoupper($tmp['nummer'][$i]) === 'NEW' ||  strtoupper($tmp['nummer'][$i]) === 'NEU' || $tmp['nummer'][$i] == '')
            {
              if(empty($projektid) && !empty($this->projekt)) {
                $projektid = $this->projekt;
              }
              if($tmp['typ'][$i] > 0){
                $felder['nummer'] = $this->app->erp->GetNextArtikelnummer($tmp['typ'][$i], 1, $projektid);
              }
              else if($tmp['artikelkategorie'][$i] > 0){
                $felder['nummer'] = $this->app->erp->GetNextArtikelnummer($tmp['artikelkategorie'][$i], 1, $projektid);
              }
              else if ($tmp['artikelkategorie_name'][$i] !='')
              {
                $tmp_katname = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung='".$this->app->DB->real_escape_string($tmp['artikelkategorie_name'][$i])."'  order by geloescht LIMIT 1");
                $felder['nummer']=$this->app->erp->GetNextArtikelnummer($tmp_katname, 1, $projektid);
              }
              else{
                if(empty($tmp['name'][$i]) && empty($tmp['name_de'][$i])){
                    $importvorlagedoresult['messages'][] = "Leerer Artikel, Zeile: ".$i;
                    break;
                }
                $felder['nummer']=$this->app->erp->GetNextArtikelnummer('produkt', 1, $projektid);
              }
            } else {
                $felder['nummer'] = $tmp['nummer'][$i];
            }
            $artikelid = $this->app->erp->ImportCreateArtikel($felder,false);
            $tmp['cmd'][$i] = "update";
            $importvorlagedoresult['messages'][] = "Neuer Artikel, Zeile: ".$i." Nummer: ".$felder['nummer'];
          }

          // END NEW CODE CREATE ARTIKEL

          /*
            note: A LOT of code has been removed above for creating a new article
            it should all be covered by the below update code
            This note and the below true-condition can be removed later
          */

          if (true) {
            if($artikelid > 0)
            {
              foreach($fieldset as $key=>$val)
              {
                $valu = $val['field'];

                $bedingung = '';
                $value = '';
                if(isset($val['bedingung'])) {
                  $bedingung = $val['bedingung'];
                }

                if(isset($val['nr']))
                {
                  $value = trim($tmp[$valu][$i]);
                  if(isset($val['inv']))
                  {
                    if($value != '1')
                    {
                      $value = 1;
                    }else{
                      $value = 0;
                    }
                  }
                }
                elseif(isset($val['vorlage']))
                {
                  $value = $val['vorlage'];
                }
                if(isset($val['bedingung'])) {
                  $value = $this->ImportvorlageBedingung($value, $val['bedingung']);
                }
                $tmp[$valu][$i] = $value;
                $value = $valu;
              }
              $this->app->erp->RunHook('importvorlage_artikel', 3, $artikelid, $tmp, $i);

              foreach($fieldset as $key=>$val)
              {
                $value = $val['field'];
                switch($value)
                {
                  case "name_de":
                  case "name_en":
                  case "uebersicht_de":
                  case "uebersicht_en":
                  case "beschreibung_online_de":
                  case "beschreibung_online_en":
                  case "metatitle_de":
                  case "metatitle_en":
                  case "metadescription_de":
                  case "metadescription_en":
                  case "metakeywords_de":
                  case "metakeywords_en":
                  case "kurztext_en":
                  case "kurztext_de":
                  case "lagerartikel":
                  case "ean":
                  case "chargenverwaltung":
                  case "herkunftsland":
                  case "zolltarifnummer":
                  case "xvp":
                  case "inaktiv":
                  case "ursprungsregion":
                  case "produktion":
                  case "gewicht":
                  case "laenge":
                  case "breite":
                  case "hoehe":
                  case "mindestlager":
                  case "mindestbestellung":
                  case "hersteller":
                  case "internerkommentar":
                  case "herstellerlink":
                  case "herstellernummer":
                  case "allelieferanten":
                  case "geraet":
                  case "serviceartikel":
                  case "steuer_erloese_inland_normal":
                  case "steuer_erloese_inland_ermaessigt":
                  case "steuer_aufwendung_inland_nichtsteuerbar":
                  case "steuer_erloese_inland_innergemeinschaftlich":
                  case "steuer_erloese_inland_eunormal":
                  case "steuer_erloese_inland_euermaessigt":
                  case "steuer_erloese_inland_export":
                  case "steuer_aufwendung_inland_normal":
                  case "steuer_aufwendung_inland_ermaessigt":
                  case "steuer_aufwendung_inland_innergemeinschaftlich":
                  case "steuer_aufwendung_inland_eunormal":
                  case "steuer_aufwendung_inland_euermaessigt":
                  case "steuer_aufwendung_inland_import":
                  case "mindesthaltbarkeitsdatum":
                  case "seriennummern":
                  case "freifeld1":
                  case "freifeld2":
                  case "freifeld3":
                  case "freifeld4":
                  case "freifeld5":
                  case "freifeld6":
                  case "freifeld7":
                  case "freifeld8":
                  case "freifeld9":
                  case "freifeld10":
                  case "freifeld11":
                  case "freifeld12":
                  case "freifeld13":
                  case "freifeld14":
                  case "freifeld15":
                  case "freifeld16":
                  case "freifeld17":
                  case "freifeld18":
                  case "freifeld19":
                  case "freifeld20":
                  case "freifeld21":
                  case "freifeld22":
                  case "freifeld23":
                  case "freifeld24":
                  case "freifeld25":
                  case "freifeld26":
                  case "freifeld27":
                  case "freifeld28":
                  case "freifeld29":
                  case "freifeld30":
                  case "freifeld31":
                  case "freifeld32":
                  case "freifeld33":
                  case "freifeld34":
                  case "freifeld35":
                  case "freifeld36":
                  case "freifeld37":
                  case "freifeld38":
                  case "freifeld39":
                  case "freifeld40":
                  case "autolagerlampe":
                  case "pseudolager":
                  case "lagerkorrekturwert":
                  case "restmenge":
                  case "pseudopreis":
                  case "intern_gesperrt":
                  case "geloescht":
                  case "juststueckliste":
                  case "stueckliste":
                  case "intern_gesperrtgrund":
                  case "inventurek":
                  case "berechneterek":
                  case "berechneterekwaehrung":
                  case "inventurekaktiv":
                  case "verwendeberechneterek":
                  case 'artikelautokalkulation':
                  case 'artikelabschliessenkalkulation':
                  case 'artikelfifokalkulation':
                    $this->app->DB->Update("UPDATE artikel SET ".$value."='".$this->app->DB->real_escape_string($tmp[$value][$i])."' WHERE id='".$artikelid."' LIMIT 1");
                    break;
                  case "matrixproduktvon":
                  case "vkmeldungunterdruecken":
                    if($tmp[$value][$i] == 1 || $tmp[$value][$i] == 0)$this->app->DB->Update("UPDATE artikel SET ".$value."='".$this->app->DB->real_escape_string($tmp[$value][$i])."' WHERE id='".$artikelid."' LIMIT 1");
                  break;
                  case "beschreibung_de":
                  case "artikelbeschreibung_de":
                    $this->app->DB->Update("UPDATE artikel SET anabregs_text='".$this->app->DB->real_escape_string($tmp[$value][$i])."' WHERE id='".$artikelid."' LIMIT 1");
                    break;
                  case "beschreibung_en":
                  case "artikelbeschreibung_en":
                    $this->app->DB->Update("UPDATE artikel SET anabregs_text_en='".$this->app->DB->real_escape_string($tmp[$value][$i])."' WHERE id='".$artikelid."' LIMIT 1");
                    break;
                  case "projekt":
                    if($tmp['projekt'][$i]!="")
                    {
                      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$this->app->DB->real_escape_string($tmp['projekt'][$i])."' AND abkuerzung!='' LIMIT 1");
                    }
                    $this->app->DB->Update("UPDATE artikel SET projekt='".$projektid."' WHERE id='".$artikelid."' LIMIT 1");
                    break;
                  case "artikelkategorie":
                  case "typ":

                    if($tmp['artikelkategorie'][$i]!="")
                    {
                      $tmp['typ'][$i] = $tmp['artikelkategorie'][$i];
                    }
                    if(is_numeric($tmp['typ'][$i]))
                    {

                      $this->app->DB->Update("UPDATE artikel SET typ='".$tmp['typ'][$i]."_kat' WHERE id='".$artikelid."' LIMIT 1");
                    } else {
                      $this->app->DB->Update("UPDATE artikel SET typ='".$this->app->DB->real_escape_string($tmp['typ'][$i])."' WHERE id='".$artikelid."' LIMIT 1");
                    }

                    break;
                  case "artikelkategorie_name":
                    if(!empty($tmp['artikelkategorie_name'][$i])){
                      $check = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung like '".$this->app->DB->real_escape_string($tmp['artikelkategorie_name'][$i])."'  order by geloescht LIMIT 1");
                      if(!$check)
                      {
                        $this->app->DB->Insert("INSERT INTO artikelkategorien (bezeichnung) values ('".$this->app->DB->real_escape_string($tmp['artikelkategorie_name'][$i])."')");
                        $check = $this->app->DB->GetInsertID();
                      }else $this->app->DB->Update("UPDATE artikelkategorien set geloescht = 0 WHERE id = '$check' LIMIT 1");
                      if($check)$this->app->DB->Update("UPDATE artikel SET typ='".$check."_kat' WHERE id='".$artikelid."' LIMIT 1");
                    }
                    break;
                  case "artikelbaum1":
                  case "artikelbaum2":
                  case "artikelbaum3":
                  case "artikelbaum4":
                  case "artikelbaum5":
                  case "artikelbaum6":
                  case "artikelbaum7":
                  case "artikelbaum8":
                  case "artikelbaum9":
                  case "artikelbaum10":
                  case "artikelbaum11":
                  case "artikelbaum12":
                  case "artikelbaum13":
                  case "artikelbaum14":
                  case "artikelbaum15":
                  case "artikelbaum16":
                  case "artikelbaum17":
                  case "artikelbaum18":
                  case "artikelbaum19":
                  case "artikelbaum20":
                    $artikelbaumanweisung = $tmp[$value][$i];
                    if($artikelbaumanweisung != ''){
                      if(strtolower($artikelbaumanweisung) == 'clear'){
                        $this->app->DB->Delete("DELETE FROM artikelbaum_artikel WHERE artikel = '$artikelid'");
                      }
                      $artikelbaumteile = explode("|", $artikelbaumanweisung);
                      $artikelbaumzumhinzufuegen = array();
                      $artikelbaumtmpkategorieid = 0;
                      for ($ii=0; $ii < (!empty($artikelbaumteile)?count($artikelbaumteile):0); $ii++) {
                        $kategorieid = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE geloescht = 0 AND bezeichnung = '".$this->app->DB->real_escape_string($artikelbaumteile[$ii])."' AND parent = '$artikelbaumtmpkategorieid' LIMIT 1");
                        if($kategorieid == ''){
                          break;
                        }else{
                          $artikelbaumzumhinzufuegen[] = $kategorieid;
                          $artikelbaumtmpkategorieid = $kategorieid;
                        }
                      }

                      for ($ii=0; $ii < (!empty($artikelbaumzumhinzufuegen)?count($artikelbaumzumhinzufuegen):0); $ii++) {
                        $vorhanden = $this->app->DB->Select("SELECT id FROM artikelbaum_artikel WHERE artikel = '$artikelid' AND kategorie = '".$artikelbaumzumhinzufuegen[$ii]."' LIMIT 1");
                        if($vorhanden == ''){
                          $this->app->DB->Insert("INSERT INTO artikelbaum_artikel (artikel, kategorie) VALUES ('$artikelid','".$artikelbaumzumhinzufuegen[$ii]."')");
                        }
                      }
                    }
                    break;
                 /* case "artikelunterkategorie_name":
                    if(!empty($tmp['artikelunterkategorie_name'][$i]) && (!empty($tmp['artikelkategorie_name'][$i]) || !empty($tmp['artikelkategorie'][$i]))){
                      if(!empty($tmp['artikelkategorie'][$i]))
                      {
                        $check = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE id = '".(int)$tmp['artikelkategorie'][$i]."'  order by geloescht LIMIT 1");
                      }else{
                        $check = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung like '".$this->app->DB->real_escape_string($tmp['artikelkategorie_name'][$i])."'  order by geloescht LIMIT 1");
                        if(!$check)
                        {
                          $this->app->DB->Insert("INSERT INTO artikelkategorien (bezeichnung) values ('".$this->app->DB->real_escape_string($tmp['artikelkategorie_name'][$i])."')");
                          $check = $this->app->DB->GetInsertID();
                        }else $this->app->DB->Update("UPDATE artikelkategorien set geloescht = 0 WHERE id = '$check' LIMIT 1");
                      }
                      if($check)
                      {
                        $felder['typ'] = $check.'_kat';
                        $this->app->DB->Update("UPDATE artikel SET typ='".$check."_kat' WHERE id='".$artikelid."' LIMIT 1");
                        $check2 = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung like '".$this->app->DB->real_escape_string($tmp['artikelunterkategorie_name'][$i])."'  order by geloescht, parent = '$check' DESC  LIMIT 1");
                        if(!$check2)
                        {
                          $this->app->DB->Insert("INSERT INTO artikelkategorien (bezeichnung,parent) values ('".$this->app->DB->real_escape_string($tmp['artikelunterkategorie_name'][$i])."', '$check')");
                          $check2 = $this->app->DB->GetInsertID();
                        }else $this->app->DB->Update("UPDATE artikelkategorien set geloescht = 0 WHERE id = '$check2' LIMIT 1");
                        if($check2)
                        {
                          $check3 = $this->app->DB->Select("SELECT id FROM artikelbaum_artikel WHERE kategorie = '$check2' AND artikel = '$artikelid' LIMIT 1");
                          if(!mysqli_error($this->app->DB->connection) && !$check3)
                          {
                            $this->app->DB->Insert("INSERT INTO artikelbaum_artikel (kategorie, artikel) VALUES ('$check2','$artikelid')");
                          }
                        }
                      }
                    }
                    break;*/
                  case "stuecklistevonartikel":
                    if($tmp['stuecklistevonartikel'][$i]!="" && $artikelid)
                    {

                      $tmpartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$tmp['stuecklistevonartikel'][$i]."' AND nummer!='' LIMIT 1");

                      if($tmpartikelid > 0)
                      {
                        $this->app->DB->Update("UPDATE artikel set stueckliste = '1' WHERE id = '$tmpartikelid' LIMIT 1");
                        $this->app->DB->Update("UPDATE artikel typ='produktion' WHERE id = '$tmpartikelid' AND typ = '' LIMIT 1");

                        $stuecklistecheck = $this->app->DB->Select("SELECT id FROM stueckliste where stuecklistevonartikel = '$tmpartikelid' and artikel = '$artikelid' LIMIT 1");
                        if(!$stuecklistecheck)
                        {
                          $sort = 1 + (int)$this->app->DB->Select("SELECT max(sort) FROM stueckliste where stuecklistevonartikel = '$tmpartikelid' LIMIT 1");
                          if(!$this->app->erp->IstStuecklistenZirkel($artikelid, $tmpartikelid))$this->app->DB->Insert("INSERT INTO stueckliste (artikel, stuecklistevonartikel,menge,layer,place,sort,firma) values ('$artikelid','$tmpartikelid','1','Top','DP','$sort','1')");
                          $stuecklistecheck = $this->app->DB->GetInsertID();
                        }

                        if($stuecklistecheck)
                        {
                          if(isset($tmp['stuecklistemenge'][$i]) && $tmp['stuecklistemenge'][$i] != "")
                          {
                            $menge = round((double) $tmp['stuecklistemenge'][$i],4);
                            if($menge <= 0)$menge = 1;
                            $this->app->DB->Update("UPDATE stueckliste set menge = '$menge'  where stuecklistevonartikel = '$tmpartikelid' and artikel = '$artikelid' LIMIT 1");
                          }
                          if($tmp['stuecklisteart'][$i] != "")
                          {
                            $art = $this->app->DB->real_escape_string($tmp['stuecklisteart'][$i]);
                            $this->app->DB->Update("UPDATE stueckliste SET art = '$art'  where stuecklistevonartikel = '$tmpartikelid' and artikel = '$artikelid' LIMIT 1");
                          }

                          if($tmp['stuecklistelayer'][$i] != "")
                          {
                            $layer = $this->app->DB->real_escape_string($tmp['stuecklistelayer'][$i]);
                            $this->app->DB->Update("UPDATE stueckliste SET layer = '$layer'  where stuecklistevonartikel = '$tmpartikelid' and artikel = '$artikelid' LIMIT 1");
                          }
                          if($tmp['stuecklisteplace'][$i] != "")
                          {
                            $place = $this->app->DB->real_escape_string($tmp['stuecklisteplace'][$i]);
                            $this->app->DB->Update("UPDATE stueckliste set place = '$place' where stuecklistevonartikel = '$tmpartikelid' and artikel = '$artikelid' LIMIT 1");
                          }
                        }
                      }
                    }

                    break;
                  case "einheit":
                    if($tmp['einheit'][$i] != "")
                    {
                      if($artikelid)$this->app->DB->Update("UPDATE artikel SET einheit='".$this->app->DB->real_escape_string($tmp['einheit'][$i])."' WHERE id='".$artikelid."' LIMIT 1");
                    }
                    break;
                  case "umsatzsteuer":
                    if($tmp[$value][$i]=="" || $tmp[$value][$i]=="19.00" || $tmp[$value][$i]=="normal" ||
                        $tmp[$value][$i]=="19%" || $tmp[$value][$i]=="19.00%" || $tmp[$value][$i]=="19")
                    {
                      $this->app->DB->Update("UPDATE artikel SET umsatzsteuer='normal' WHERE id='".$artikelid."' LIMIT 1");
                    }
                    if($tmp[$value][$i]=="7.00" || $tmp[$value][$i]=="7%" || $tmp[$value][$i]=="7.00%" || $tmp[$value][$i]=="7" || $tmp[$value][$i]=="ermaessigt")
                    {
                      $this->app->DB->Update("UPDATE artikel SET umsatzsteuer='ermaessigt' WHERE id='".$artikelid."' LIMIT 1");
                    }
                    if($tmp[$value][$i]=="befreit")$this->app->DB->Update("UPDATE artikel SET umsatzsteuer='befreit' WHERE id='".$artikelid."' LIMIT 1");
                    break;
                  case "matrixprodukt":
                    if($tmp[$value][$i]=="1")
                    {
                      $this->app->DB->Update("UPDATE artikel SET matrixprodukt=1 WHERE id='".$artikelid."' LIMIT 1");
                      if(!empty($tmp['matrixgruppe1']) && !empty($tmp['matrixgruppe1'][$i]) && (String)$tmp['matrixgruppe1'][$i] !=='')
                      {
                        $matrixgruppe2 = 0;
                        $matrixgruppe1 = $this->app->DB->Select("SELECT id FROM `matrixprodukt_eigenschaftengruppen` WHERE aktiv = 1 AND name ='". $this->app->DB->real_escape_string($tmp['matrixgruppe1'][$i])."' LIMIT 1");
                        if($matrixgruppe1)
                        {
                          $matrixgruppenname1 = $this->app->DB->Select("SELECT name FROM `matrixprodukt_eigenschaftengruppen` WHERE id = '$matrixgruppe1' LIMIT 1");
                          $optionen1 = $this->app->DB->SelectArr("SELECT * FROM `matrixprodukt_eigenschaftenoptionen` WHERE gruppe = '$matrixgruppe1' AND aktiv = 1 ORDER by sort, id ");
                          $gruppenok = true;
                          if(!$optionen1)$gruppenok = false;
                          if(!empty($tmp['matrixgruppe2']) && !empty($tmp['matrixgruppe2'][$i]) && (String)$tmp['matrixgruppe2'][$i] !=='')
                          {
                            $matrixgruppe2 = $this->app->DB->Select("SELECT id FROM `matrixprodukt_eigenschaftengruppen` WHERE aktiv = 1 AND name ='". $this->app->DB->real_escape_string($tmp['matrixgruppe2'][$i])."' LIMIT 1");
                            if(!$matrixgruppe2)
                            {
                              $gruppenok = false;
                            }else{
                              $matrixgruppenname2 = $this->app->DB->Select("SELECT name FROM `matrixprodukt_eigenschaftengruppen` WHERE id = '$matrixgruppe2' LIMIT 1");
                              $optionen2 = $this->app->DB->SelectArr("SELECT * FROM `matrixprodukt_eigenschaftenoptionen` WHERE gruppe = '$matrixgruppe2' AND aktiv = 1 ORDER by sort, id ");
                              if(!$optionen2)$gruppenok = false;
                            }
                          }
                          if($gruppenok)
                          {
                            $existgruppen = $this->app->DB->SelectArr("SELECT * FROM matrixprodukt_eigenschaftengruppen_artikel WHERE artikel = '$artikelid'");
                            if(!$existgruppen)
                            {
                              $this->app->DB->Insert("INSERT INTO matrixprodukt_eigenschaftengruppen_artikel (artikel, aktiv, name, sort) SELECT '$artikelid' as artikel, '1' as aktiv, name, '0' as sort  FROM
                                        matrixprodukt_eigenschaftengruppen WHERE id = '$matrixgruppe1'
                              ");
                              if(!empty($tmp['matrixgruppe2']) && !empty($tmp['matrixgruppe2'][$i]) && (String)$tmp['matrixgruppe2'][$i] !=='' && $matrixgruppe2)
                              {
                                $this->app->DB->Insert("INSERT INTO matrixprodukt_eigenschaftengruppen_artikel (artikel, aktiv, name, sort) SELECT '$artikelid' as artikel, '1' as aktiv, name, '1' as sort FROM
                                          matrixprodukt_eigenschaftengruppen WHERE id = '$matrixgruppe2'
                                ");
                              }
                              $existgruppen = $this->app->DB->SelectArr("SELECT * FROM matrixprodukt_eigenschaftengruppen_artikel WHERE artikel = '$artikelid'");
                            }

                            if($existgruppen)
                            {
                              $gruppe1found = false;
                              $gruppe2found = false;
                              foreach($existgruppen as $kg => $vg)
                              {
                                if(strtolower($vg['name']) == strtolower($tmp['matrixgruppe1'][$i]))
                                {
                                  $gruppe1found = $vg['id'];
                                  $optionen1ex = $this->app->DB->SelectArr("SELECT * FROM `matrixprodukt_eigenschaftenoptionen_artikel` WHERE artikel = '$artikelid' AND gruppe = '$gruppe1found' AND aktiv = 1 ORDER BY sort");
                                }
                                if($matrixgruppe2)
                                {
                                  if(strtolower($vg['name']) == strtolower($tmp['matrixgruppe2'][$i]))
                                  {
                                    $gruppe2found = $vg['id'];
                                    $optionen2ex = $this->app->DB->SelectArr("SELECT * FROM `matrixprodukt_eigenschaftenoptionen_artikel` WHERE artikel = '$artikelid' AND gruppe = '$gruppe2found' AND aktiv = 1 ORDER BY sort");
                                  }
                                }
                              }
                              if(!$gruppe1found)$gruppenok = false;
                              if($matrixgruppe2 && !$gruppe2found)$gruppenok = false;
                            }
                            if($gruppenok)
                            {
                              if(!$gruppe1found)
                              {
                                $this->app->DB->Insert("INSERT INTO matrixprodukt_eigenschaftengruppen_artikel (artikel, aktiv, name, sort) SELECT '$artikelid' as artikel, '1' as aktiv, name, '0' as sort  FROM
                                          matrixprodukt_eigenschaftengruppen WHERE id = '$matrixgruppe1'
                                ");
                                $gruppe1found = $this->app->DB->GetInsertID();
                              }
                              if($matrixgruppe2 && !$gruppe2found)
                              {
                                $this->app->DB->Insert("INSERT INTO matrixprodukt_eigenschaftengruppen_artikel (artikel, aktiv, name, sort) SELECT '$artikelid' as artikel, '1' as aktiv, name, '1' as sort FROM
                                          matrixprodukt_eigenschaftengruppen WHERE id = '$matrixgruppe2'
                                ");
                                $gruppe2found = $this->app->DB->GetInsertID();
                              }
                              foreach($optionen1 as $ko => $vo)
                              {
                                $foundoption1 = false;
                                if(isset($optionen1ex)){
                                  foreach($optionen1ex as $koa => $voa)
                                  {
                                    if(strtolower($vo['name']) == strtolower($voa['name']))
                                    {
                                      $foundoption1 = $voa['id'];
                                      $optionen1[$ko]['matrixprodukt_eigenschaftenoptionen_artikel'] = $foundoption1;
                                    }
                                  }
                                }
                                if(!$foundoption1)
                                {
                                  $this->app->DB->Insert("INSERT INTO matrixprodukt_eigenschaftenoptionen_artikel (name,name_ext, artikel, sort, gruppe, aktiv, erstellt, bearbeiter)
                                    SELECT name,name_ext, '$artikelid' as artikel, sort, '$gruppe1found' as gruppe, '1' as aktiv, now() as erstellt, '".$this->app->DB->real_escape_string($this->app->User->GetName())."' as bearbeiter
                                    FROM matrixprodukt_eigenschaftenoptionen WHERE id = '".$vo['id']."'
                                  ");
                                  $optionen1[$ko]['matrixprodukt_eigenschaftenoptionen_artikel'] = $this->app->DB->GetInsertID();
                                }
                              }
                              if($matrixgruppe2)
                              {
                                foreach($optionen2 as $ko => $vo)
                                {
                                  $foundoption2 = false;
                                  if(isset($optionen2ex)){
                                    foreach($optionen2ex as $koa => $voa)
                                    {
                                      if(strtolower($vo['name']) == strtolower($voa['name']))
                                      {
                                        $foundoption2 = $voa['id'];
                                        $optionen2[$ko]['matrixprodukt_eigenschaftenoptionen_artikel'] = $foundoption2;
                                      }
                                    }
                                  }
                                  if(!$foundoption2)
                                  {
                                    $this->app->DB->Insert("INSERT INTO matrixprodukt_eigenschaftenoptionen_artikel (name,name_ext, artikel, sort, gruppe, aktiv, erstellt, bearbeiter)
                                      SELECT name,name_ext, '$artikelid' as artikel, sort, '$gruppe2found' as gruppe, '1' as aktiv, now() as erstellt, '".$this->app->DB->real_escape_string($this->app->User->GetName())."' as bearbeiter
                                      FROM matrixprodukt_eigenschaftenoptionen WHERE id = '".$vo['id']."'
                                    ");
                                    $optionen2[$ko]['matrixprodukt_eigenschaftenoptionen_artikel'] = $this->app->DB->GetInsertID();
                                  }
                                }
                              }
                              if($matrixgruppe2)
                              {
                                foreach($optionen1 as $ko => $vo)
                                {
                                  foreach($optionen2 as $ko2 => $vo2)
                                  {
                                    $check = $this->app->DB->Select("
                                    SELECT a.id FROM artikel a
                                    INNER JOIN `matrixprodukt_optionen_zu_artikel` moza1 ON a.id = moza1.artikel AND moza1.option_id = '".$vo['matrixprodukt_eigenschaftenoptionen_artikel']."'
                                    INNER JOIN `matrixprodukt_optionen_zu_artikel` moza2 ON a.id = moza2.artikel AND moza2.option_id = '".$vo2['matrixprodukt_eigenschaftenoptionen_artikel']."'
                                    LIMIT 1
                                    ");
                                    if(!$check)
                                    {
                                      $checkarr = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE id = '$artikelid' LIMIT 1");
                                      if($checkarr)
                                      {
                                        unset($checkarr[0]['ean']);
                                        unset($checkarr[0]['nummer']);


                                        $matrixartikelnummer = str_replace('|',',',$tmp['matrixartikelnummer'][$i]);
                                        if($matrixartikelnummer == 2)
                                        {
                                          $checkarr[0]['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikelid' LIMIT 1");
                                          $checkarr[0]['nummer'] .= '-'.trim(preg_replace('#[^-_A-Za-z0-9]#', '',
                                              str_replace( array('Ü','Ö','Ä','ß','&UUML;','&AUML;','&OUML;','&SZLIG;',' '), array('UE','OE','AE','SS','UE','AE','OE','SS','_'),
                                              trim(strtoupper( $this->app->DB->Select("SELECT name FROM `matrixprodukt_eigenschaftenoptionen_artikel` WHERE id = '".$vo['matrixprodukt_eigenschaftenoptionen_artikel']."' ") ))
                                              )
                                          )).'-'.trim(preg_replace('#[^-_A-Za-z0-9]#', '',
                                              str_replace( array('Ü','Ö','Ä','ß','&UUML;','&AUML;','&OUML;','&SZLIG;',' '), array('UE','OE','AE','SS','UE','AE','OE','SS','_'),
                                              trim(strtoupper( $this->app->DB->Select("SELECT name FROM `matrixprodukt_eigenschaftenoptionen_artikel` WHERE id = '".$vo2['matrixprodukt_eigenschaftenoptionen_artikel']."' ") ))
                                              )
                                          ));
                                        }elseif($matrixartikelnummer && $matrixartikelnummer[0] == '3')
                                        {
                                          $checkarr[0]['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikelid' LIMIT 1");
                                          $matrixartikelnummera = explode(',',$matrixartikelnummer);
                                          $prefixtrennzeichen = '-';
                                          if(isset($matrixartikelnummera[1]))$prefixtrennzeichen = $matrixartikelnummera[1];
                                          if(strlen($prefixtrennzeichen) == 0)$prefixtrennzeichen = '-';
                                          $prefixstellen = 2;
                                          if(isset($matrixartikelnummera[2]))$prefixstellen = $matrixartikelnummera[2];
                                          if($prefixstellen < 1)$prefixstellen = 1;
                                          $prefixnaechstenummer = 1;
                                          if(!empty($matrixartikelnummera[3]))$prefixnaechstenummer = 1;
                                          $zaehler = 0;
                                          while($zeahler < 1000)
                                          {
                                            $zeahler++;
                                            $_prefixnaechstenummer = $prefixnaechstenummer;
                                            if(strlen($_prefixnaechstenummer) < $prefixstellen)
                                            {
                                              $prefixnaechstenummer = str_repeat('0', $prefixstellen-strlen($_prefixnaechstenummer)).$_prefixnaechstenummer;
                                            }else{
                                              $prefixnaechstenummer = $_prefixnaechstenummer;
                                            }

                                            $neuenummer = $checkarr[0]['nummer'].trim($prefixtrennzeichen).$prefixnaechstenummer;
                                            if(!$this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '".$this->app->DB->real_escape_string($neuenummer)."' LIMIT 1"))
                                            {
                                              break;
                                            }else{
                                              $prefixnaechstenummer++;
                                            }
                                          }
                                          $checkarr[0]['nummer'] = $neuenummer;
                                        }else{
                                          $checkarr[0]['nummer'] = $this->app->erp->GetNextArtikelnummer($checkarr[0]['typ'], 1, $checkarr[0]['projekt']);
                                        }

                                        unset($checkarr[0]['id']);
                                        unset($checkarr[0]['zolltarifnummer']);
                                        unset($checkarr[0]['xvp']);
                                        unset($checkarr[0]['hersteller']);
                                        for($fi = 1; $fi <= 40; $fi++)unset($checkarr[0]['freifeld'.$fi]);
                                        unset($checkarr[0]['anabregs_text']);
                                        unset($checkarr[0]['anabregs_text_en']);
                                        unset($checkarr[0]['kurztext_de']);
                                        unset($checkarr[0]['kurztext_en']);
                                        unset($checkarr[0]['beschreibung_de']);
                                        unset($checkarr[0]['beschreibung_en']);
                                        unset($checkarr[0]['links_de']);
                                        unset($checkarr[0]['links_en']);
                                        unset($checkarr[0]['startseite_de']);
                                        unset($checkarr[0]['startseite_en']);
                                        if(isset($tmp['name_de']) && isset($tmp['name_de'][$i]))$checkarr[0]['name_de'] = $tmp['name_de'][$i];
                                        if(isset($tmp['name_en']) && isset($tmp['name_en'][$i]))$checkarr[0]['name_en'] = $tmp['name_en'][$i];
                                        $checkarr[0]['matrixprodukt'] = 0;
                                        $checkarr[0]['variante'] = 1;
                                        $checkarr[0]['variante_von'] = $artikelid;
                                        if(isset($tmp['matrixnamefuerunterartikel']) && $tmp['matrixnamefuerunterartikel'][$i])
                                        {
                                          $checkarr[0]['name_de'] = $checkarr[0]['name_de'].' '.$matrixgruppenname1.': '.$vo['name'].' '.$matrixgruppenname2.': '.$vo2['name'];
                                          if($checkarr[0]['name_en'])$checkarr[0]['name_en'] = $checkarr[0]['name_en'].' '.$matrixgruppenname1.': '.$vo['name'].' '.$matrixgruppenname2.': '.$vo2['name'];
                                        }
                                        $check = $this->app->erp->InsertUpdateArtikel($checkarr[0]);
                                        if(!empty($check) && !in_array($check, array_column($result_objects, 'id'))) {
                                          $result_objects[] = array('id' => $check, 'type' => 'artikel');
                                        }
                                        if($check)
                                        {
                                          $vkarr = $this->app->DB->SelectArr("SELECT * FROM verkaufspreise WHERE artikel = '$artikelid'");
                                          if($vkarr)
                                          {
                                            foreach($vkarr as $vv)
                                            {
                                              $vv['artikel'] = $check;
                                              unset($vv['id']);
                                              $this->app->DB->Insert("INSERT INTO verkaufspreise (id) VALUES ('')");
                                              $newvkid = $this->app->DB->GetInsertID();
                                              $this->app->FormHandler->ArrayUpdateDatabase("verkaufspreise",$newvkid,$vv,true);

                                            }
                                          }


                                          $this->app->DB->Insert("INSERT INTO matrixprodukt_optionen_zu_artikel (artikel, option_id) VALUES ('$check','".$vo['matrixprodukt_eigenschaftenoptionen_artikel']."')");
                                          $this->app->DB->Insert("INSERT INTO matrixprodukt_optionen_zu_artikel (artikel, option_id) VALUES ('$check','".$vo2['matrixprodukt_eigenschaftenoptionen_artikel']."')");
                                        }
                                        unset($checkarr[0]);
                                      }
                                    }
                                  }
                                }
                              }else{
                                foreach($optionen1 as $ko => $vo)
                                {
                                  $check = $this->app->DB->Select("
                                  SELECT a.id FROM artikel a
                                  INNER JOIN `matrixprodukt_optionen_zu_artikel` moza1 ON a.id = moza1.artikel AND moza1.option_id = '".$vo['matrixprodukt_eigenschaftenoptionen_artikel']."'
                                  LIMIT 1
                                  ");
                                  if(!$check)
                                  {
                                    $checkarr = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE id = '$artikelid' LIMIT 1");
                                    if($checkarr)
                                    {
                                      unset($checkarr[0]['ean']);
                                      unset($checkarr[0]['id']);
                                      unset($checkarr[0]['zolltarifnummer']);
                                      unset($checkarr[0]['xvp']);
                                      unset($checkarr[0]['hersteller']);
                                      for($fi = 1; $fi <= 40; $fi++)unset($checkarr[0]['freifeld'.$fi]);
                                      unset($checkarr[0]['anabregs_text']);
                                      unset($checkarr[0]['anabregs_text_en']);
                                      unset($checkarr[0]['kurztext_de']);
                                      unset($checkarr[0]['kurztext_en']);
                                      unset($checkarr[0]['beschreibung_de']);
                                      unset($checkarr[0]['beschreibung_en']);
                                      unset($checkarr[0]['links_de']);
                                      unset($checkarr[0]['links_en']);
                                      unset($checkarr[0]['startseite_de']);
                                      unset($checkarr[0]['startseite_en']);
                                      unset($checkarr[0]['nummer']);
                                      if(isset($tmp['name_de']) && isset($tmp['name_de'][$i]))$checkarr[0]['name_de'] = $tmp['name_de'][$i];
                                      if(isset($tmp['name_en']) && isset($tmp['name_en'][$i]))$checkarr[0]['name_en'] = $tmp['name_en'][$i];
                                      $matrixartikelnummer = str_replace('|',',',$tmp['matrixartikelnummer'][$i]);
                                      if($matrixartikelnummer == 2)
                                      {
                                        $checkarr[0]['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikelid' LIMIT 1");
                                        $checkarr[0]['nummer'] .= '-'.trim(preg_replace('#[^-_A-Za-z0-9]#', '',
                                            str_replace( array('Ü','Ö','Ä','ß','&UUML;','&AUML;','&OUML;','&SZLIG;',' '), array('UE','OE','AE','SS','UE','AE','OE','SS','_'),
                                            trim(strtoupper( $this->app->DB->Select("SELECT name FROM `matrixprodukt_eigenschaftenoptionen_artikel` WHERE id = '".$vo['matrixprodukt_eigenschaftenoptionen_artikel']."' ") ))
                                            )
                                        ));
                                      }elseif($matrixartikelnummer && $matrixartikelnummer[0] == '3')
                                      {
                                        $checkarr[0]['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikelid' LIMIT 1");
                                        $matrixartikelnummera = explode(',',$matrixartikelnummer);
                                        $prefixtrennzeichen = '-';
                                        if(isset($matrixartikelnummera[1]))$prefixtrennzeichen = $matrixartikelnummera[1];
                                        if(strlen($prefixtrennzeichen) == 0)$prefixtrennzeichen = '-';
                                        $prefixstellen = 2;
                                        if(isset($matrixartikelnummera[2]))$prefixstellen = $matrixartikelnummera[2];
                                        if($prefixstellen < 1)$prefixstellen = 1;
                                        $prefixnaechstenummer = 1;
                                        if(!empty($matrixartikelnummera[3]))$prefixnaechstenummer = 1;
                                        $zaehler = 0;
                                        while($zeahler < 1000)
                                        {
                                          $zeahler++;
                                          $_prefixnaechstenummer = $prefixnaechstenummer;
                                          if(strlen($_prefixnaechstenummer) < $prefixstellen)
                                          {
                                            $prefixnaechstenummer = str_repeat('0', $prefixstellen-strlen($_prefixnaechstenummer)).$_prefixnaechstenummer;
                                          }else{
                                            $prefixnaechstenummer = $_prefixnaechstenummer;
                                          }

                                          $neuenummer = $checkarr[0]['nummer'].trim($prefixtrennzeichen).$prefixnaechstenummer;
                                          if(!$this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '".$this->app->DB->real_escape_string($neuenummer)."' LIMIT 1"))
                                          {
                                            break;
                                          }else{
                                            $prefixnaechstenummer++;
                                          }
                                        }
                                        $checkarr[0]['nummer'] = $neuenummer;
                                      }else{
                                        $checkarr[0]['nummer'] = $this->app->erp->GetNextArtikelnummer($checkarr[0]['typ'], 1, $checkarr[0]['projekt']);
                                      }

                                      $checkarr[0]['matrixprodukt'] = 0;
                                      $checkarr[0]['variante'] = 1;
                                      $checkarr[0]['variante_von'] = $artikelid;
                                      if(isset($tmp['matrixnamefuerunterartikel']) && $tmp['matrixnamefuerunterartikel'][$i])
                                      {
                                        $checkarr[0]['name_de'] = $checkarr[0]['name_de'].' '.$matrixgruppenname1.': '.$vo['name'];
                                        if($checkarr[0]['name_en'])$checkarr[0]['name_en'] = $checkarr[0]['name_en'].' '.$matrixgruppenname1.': '.$vo['name'];
                                      }
                                      $check = $this->app->erp->InsertUpdateArtikel($checkarr[0]);
                                      if(!empty($check) && !in_array($check, $result_objects)) {
                                        $result_objects[] = array('id' => $check, 'type' => 'artikel');
                                      }
                                      if($check)
                                      {
                                        $vkarr = $this->app->DB->SelectArr("SELECT * FROM verkaufspreise WHERE artikel = '$artikelid'");
                                        if($vkarr)
                                        {
                                          foreach($vkarr as $vv)
                                          {
                                            $vv['artikel'] = $check;
                                            unset($vv['id']);
                                            $this->app->DB->Insert("INSERT INTO verkaufspreise (id) VALUES ('')");
                                            $newvkid = $this->app->DB->GetInsertID();
                                            $this->app->FormHandler->ArrayUpdateDatabase("verkaufspreise",$newvkid,$vv,true);

                                          }
                                        }
                                        $this->app->DB->Insert("INSERT INTO matrixprodukt_optionen_zu_artikel (artikel, option_id) VALUES ('$check','".$vo['matrixprodukt_eigenschaftenoptionen_artikel']."')");
                                      }
                                      unset($checkarr[0]);
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                        if(isset($optionen1))unset($optionen1);
                        if(isset($optionen2))unset($optionen2);
                        if(isset($optionen1ex))unset($optionen1ex);
                        if(isset($optionen2ex))unset($optionen2ex);
                      }
                    }
                    else
                      $this->app->DB->Update("UPDATE artikel SET matrixprodukt=0 WHERE id='".$artikelid."' LIMIT 1");
                    break;
                  break;
                  case "aktiv":
                    if($tmp[$value][$i]=="0")
                      $this->app->DB->Update("UPDATE artikel SET inaktiv=1 WHERE id='".$artikelid."' LIMIT 1");
                    else
                      $this->app->DB->Update("UPDATE artikel SET inaktiv=0 WHERE id='".$artikelid."' LIMIT 1");
                    break;
                  case "variante_von":
                    if(trim($tmp[$value][$i])!="")
                    {
                      // schaue ob
                      $tmpartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".trim($tmp[$value][$i])."' AND nummer!='' LIMIT 1");
                      if($tmpartikelid == '') {
                        $tmpartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE ean = '".trim($tmp[$value][$i])."' AND geloescht <> 1");
                      }
                      if($tmpartikelid == '') {
                        $tmpartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE herstellernummer = '".trim($tmp[$value][$i])."' AND geloescht <> 1 ");
                      }
                      if($tmpartikelid > 0)
                      {
                         $this->app->DB->Update("UPDATE artikel SET variante_von='".$tmpartikelid."',variante=1
                            WHERE id='".$artikelid."' AND id!='".$tmpartikelid."' LIMIT 1");
                      } else {
                        $errormessage .= "Hauptartikel f&uuml;r Variante nicht gefunden: ".trim($tmp[$value][$i]).".<br>";
                      }
                    }
                    break;
                  case  "lieferanteinkaufvpepreis":
                    if($tmp['lieferanteinkaufvpemenge'][$i] > 0 && $tmp['lieferanteinkaufmenge'][$i]<=0 && $tmp['lieferanteinkaufvpepreis'][$i]  > 0)
                    {
                      $tmp['lieferanteinkaufnetto'][$i] = $tmp['lieferanteinkaufvpepreis'][$i] / $tmp['lieferanteinkaufvpemenge'][$i]*1.00;

                      if($tmp['lieferanteinkaufmenge'][$i]<=0)
                        $tmp['lieferanteinkaufmenge'][$i] = 1;

                      if($artikelid && $lieferantid)
                      {
                        $aktlieferantid = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id = '$artikelid' LIMIT 1");
                        if(!$aktlieferantid)$this->app->DB->Update("UPDATE artikel SET adresse = '$lieferantid' WHERE id = '$artikelid' LIMIT 1");

                      }
                      $lieferantartikelbezeichnung = "";
                      if($tmp['lieferantartikelbezeichnung'][$i])
                      {
                        $lieferantartikelbezeichnung = $this->app->DB->real_escape_string($tmp['lieferantartikelbezeichnung'][$i]);

                      }

                      $ekid = $this->app->erp->AddEinkaufspreis($artikelid,$tmp['lieferanteinkaufmenge'][$i],
                          $lieferantid,$nr,$lieferantartikelbezeichnung,
                          $tmp['lieferanteinkaufnetto'][$i],$tmp['lieferanteinkaufwaehrung'][$i],$tmp['lieferanteinkaufvpemenge'][$i]);

                      $this->UpdateEinkaufspreiseExtraWerte($ekid,$tmp,"");
                    }
                    break;
                  case  "lieferanteinkaufvpepreis2":
                    if($tmp['lieferanteinkaufvpemenge2'][$i] > 0 && $tmp['lieferanteinkaufmenge2'][$i]<=0 && $tmp['lieferanteinkaufvpepreis2'][$i]  > 0)
                    {
                      $tmp['lieferanteinkaufnetto2'][$i] = $tmp['lieferanteinkaufvpepreis2'][$i] / $tmp['lieferanteinkaufvpemenge2'][$i]*1.00;

                      if($tmp['lieferanteinkaufmenge2'][$i]<=0)
                        $tmp['lieferanteinkaufmenge2'][$i] = 1;

                      if($artikelid && $lieferantid)
                      {
                        $aktlieferantid = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id = '$artikelid' LIMIT 1");
                        if(!$aktlieferantid)$this->app->DB->Update("UPDATE artikel SET adresse = '$lieferantid' WHERE id = '$artikelid' LIMIT 1");

                      }
                      $lieferantartikelbezeichnung = "";
                      if($tmp['lieferantartikelbezeichnung2'][$i])
                      {
                        $lieferantartikelbezeichnung = $this->app->DB->real_escape_string($tmp['lieferantartikelbezeichnung2'][$i]);
                      }

                      $ekid = $this->app->erp->AddEinkaufspreis($artikelid,$tmp['lieferanteinkaufmenge2'][$i],
                          $lieferantid,$nr,$lieferantartikelbezeichnung,
                          $tmp['lieferanteinkaufnetto2'][$i],$tmp['lieferanteinkaufwaehrung2'][$i],$tmp['lieferanteinkaufvpemenge2'][$i]);

                      $this->UpdateEinkaufspreiseExtraWerte($ekid,$tmp,"2");
                    }
                    break;

                  case  "lieferanteinkaufvpepreis3":
                    if($tmp['lieferanteinkaufvpemenge3'][$i] > 0 && $tmp['lieferanteinkaufmenge3'][$i]<=0 && $tmp['lieferanteinkaufvpepreis3'][$i]  > 0)
                    {
                      $tmp['lieferanteinkaufnetto3'][$i] = $tmp['lieferanteinkaufvpepreis3'][$i] / $tmp['lieferanteinkaufvpemenge3'][$i]*1.00;

                      if($tmp['lieferanteinkaufmenge3'][$i]<=0)
                        $tmp['lieferanteinkaufmenge3'][$i] = 1;

                      if($artikelid && $lieferantid)
                      {
                        $aktlieferantid = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id = '$artikelid' LIMIT 1");
                        if(!$aktlieferantid)$this->app->DB->Update("UPDATE artikel SET adresse = '$lieferantid' WHERE id = '$artikelid' LIMIT 1");

                      }
                      $lieferantartikelbezeichnung = "";
                      if($tmp['lieferantartikelbezeichnung3'][$i])
                      {
                        $lieferantartikelbezeichnung = $this->app->DB->real_escape_string($tmp['lieferantartikelbezeichnung3'][$i]);
                      }

                      $ekid = $this->app->erp->AddEinkaufspreis($artikelid,$tmp['lieferanteinkaufmenge3'][$i],
                          $lieferantid,$nr,$lieferantartikelbezeichnung,
                          $tmp['lieferanteinkaufnetto3'][$i],$tmp['lieferanteinkaufwaehrung3'][$i],$tmp['lieferanteinkaufvpemenge3'][$i]);

                      $this->UpdateEinkaufspreiseExtraWerte($ekid,$tmp,"3");
                    }
                    break;

                  case "lager_lieferant":
                    if($tmp['lager_lieferant'][$i] != '' && intval($tmp['lager_lieferant'][$i])){
                      $this->app->DB->Update("UPDATE einkaufspreise SET lager_lieferant='".$tmp['lager_lieferant'][$i]."' WHERE adresse='".$lieferantid."' AND artikel='".$artikelid."'" );
                    }
                    break;

                  case "einkaufspreisnetto":
                    $tmp['lieferanteinkaufnetto'][$i] = $tmp['einkaufspreisnetto'][$i];
                    // break omitted
                  case  "lieferanteinkaufnetto":
                    $einkaufsdaten = $this->app->DB->SelectRow("SELECT id,preis,bestellnummer FROM einkaufspreise WHERE ab_menge='".$tmp['lieferanteinkaufmenge'][$i]."' AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW()) AND adresse='".$lieferantid."' AND artikel='".$artikelid."' LIMIT 1");
                    if($einkaufsdaten){
                      $alterek = $einkaufsdaten['preis'];
                      $ekid = $einkaufsdaten['id'];
                      $altelieferantbestellnummer = $einkaufsdaten['bestellnummer'];
                    }else{
                      $alterek = null;
                      $ekid = null;
                      $altelieferantbestellnummer = null;
                    }

                    if($tmp['lieferantbestellnummer'][$i]!='') {
                      $nr = $tmp['lieferantbestellnummer'][$i];
                    }
                    else if($tmp['herstellernummer'][$i]!='') {
                      $nr = $tmp['herstellernummer'][$i];
                    }
                    else {
                      $nr = '';
                    }//$tmp['name_de'][$i];

                    if($alterek != $tmp['lieferanteinkaufnetto'][$i] || $altelieferantbestellnummer != $nr)
                    {
                      $ekpreisaenderungen++;
                      $this->app->DB->Update("UPDATE einkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY)
                          WHERE adresse='".$lieferantid."' AND artikel='".$artikelid."'
                          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW())
                          AND ab_menge='".$tmp['lieferanteinkaufmenge'][$i]."' LIMIT 1");

                      if($tmp['lieferanteinkaufvpemenge'][$i] > 0 && $tmp['lieferanteinkaufmenge'][$i]<=0 && $tmp['lieferanteinkaufvpepreis'][$i]  > 0)
                      {
                        $tmp['lieferanteinkaufnetto'][$i] = $tmp['lieferanteinkaufvpepreis'][$i] / $tmp['lieferanteinkaufvpemenge'][$i]*1.00;
                      }

                      if($tmp['lieferanteinkaufmenge'][$i]<=0){
                        $tmp['lieferanteinkaufmenge'][$i] = 1;
                      }
                      if($artikelid && $lieferantid)
                      {
                        $aktlieferantid = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id = '$artikelid' LIMIT 1");
                        if(!$aktlieferantid) {
                          $this->app->DB->Update("UPDATE artikel SET adresse = '$lieferantid' WHERE id = '$artikelid' LIMIT 1");
                        }
                      }
                      $lieferantartikelbezeichnung = '';
                      if($tmp['lieferantartikelbezeichnung'][$i])
                      {
                        $lieferantartikelbezeichnung = $this->app->DB->real_escape_string($tmp['lieferantartikelbezeichnung'][$i]);
                      }
                      $ekid = $this->app->erp->AddEinkaufspreis($artikelid,$tmp['lieferanteinkaufmenge'][$i], $lieferantid,$nr,$lieferantartikelbezeichnung, $tmp['lieferanteinkaufnetto'][$i],$tmp['lieferanteinkaufwaehrung'][$i],$tmp['lieferanteinkaufvpemenge'][$i]);

                    }
                    if($ekid > 0){
                      foreach ($tmp as $keyx => $valuex){
                        if(strpos($keyx,'lieferant') !== false){
                          $tmp[rtrim($keyx,'1')] = $valuex;
                        }
                      }
                      $this->UpdateEinkaufspreiseExtraWerte($ekid, $tmp);
                    }
                    break;
                  case  "lieferanteinkaufnetto2":
                    $einkaufsdaten = $this->app->DB->SelectRow("SELECT id,preis,bestellnummer FROM einkaufspreise WHERE ab_menge='".$tmp['lieferanteinkaufmenge2'][$i]."' AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW()) AND adresse='".$lieferantid."' AND artikel='".$artikelid."' LIMIT 1");
                    if($einkaufsdaten){
                      $alterek = $einkaufsdaten['preis'];
                      $ekid = $einkaufsdaten['id'];
                      $altelieferantbestellnummer = $einkaufsdaten['bestellnummer'];
                    }else{
                      $alterek = null;
                      $ekid = null;
                      $altelieferantbestellnummer = null;
                    }

                    if($tmp['lieferantbestellnummer2'][$i]!='') {
                      $nr = $tmp['lieferantbestellnummer2'][$i];
                    }
                    else if($tmp['herstellernummer'][$i]!='') {
                      $nr = $tmp['herstellernummer'][$i];
                    }
                    else {
                      $nr = '';
                    }//$tmp['name_de'][$i];

                    if($alterek != $tmp['lieferanteinkaufnetto2'][$i] || $altelieferantbestellnummer != $nr)
                    {
                      $ekpreisaenderungen++;
                      $this->app->DB->Update("UPDATE einkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY)
                          WHERE adresse='".$lieferantid."' AND artikel='".$artikelid."'
                          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW())
                          AND ab_menge='".$tmp['lieferanteinkaufmenge2'][$i]."' LIMIT 1");

                      if($tmp['lieferanteinkaufvpemenge2'][$i] > 0 && $tmp['lieferanteinkaufmenge2'][$i]<=0 && $tmp['lieferanteinkaufvpepreis2'][$i]  > 0)
                      {
                        $tmp['lieferanteinkaufnetto2'][$i] = $tmp['lieferanteinkaufvpepreis2'][$i] / $tmp['lieferanteinkaufvpemenge2'][$i]*1.00;
                      }

                      if($tmp['lieferanteinkaufmenge2'][$i]<=0){
                        $tmp['lieferanteinkaufmenge2'][$i] = 1;
                      }
                      if($artikelid && $lieferantid)
                      {
                        $aktlieferantid = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id = '$artikelid' LIMIT 1");
                        if(!$aktlieferantid) {
                          $this->app->DB->Update("UPDATE artikel SET adresse = '$lieferantid' WHERE id = '$artikelid' LIMIT 1");
                        }
                      }
                      $lieferantartikelbezeichnung = '';
                      if($tmp['lieferantartikelbezeichnung2'][$i])
                      {
                        $lieferantartikelbezeichnung = $this->app->DB->real_escape_string($tmp['lieferantartikelbezeichnung2'][$i]);
                      }

                      $ekid = $this->app->erp->AddEinkaufspreis($artikelid,$tmp['lieferanteinkaufmenge2'][$i], $lieferantid,$nr,$lieferantartikelbezeichnung, $tmp['lieferanteinkaufnetto2'][$i],$tmp['lieferanteinkaufwaehrung2'][$i],$tmp['lieferanteinkaufvpemenge2'][$i]);

                    }
                    if($ekid > 0){
                      $this->UpdateEinkaufspreiseExtraWerte($ekid, $tmp,'2');
                    }
                    break;
                  case  "lieferanteinkaufnetto3":
                    $einkaufsdaten = $this->app->DB->SelectRow("SELECT id,preis,bestellnummer FROM einkaufspreise WHERE ab_menge='".$tmp['lieferanteinkaufmenge3'][$i]."' AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW()) AND adresse='".$lieferantid."' AND artikel='".$artikelid."' LIMIT 1");
                    if($einkaufsdaten){
                      $alterek = $einkaufsdaten['preis'];
                      $edkid = $einkaufsdaten['id'];
                      $altelieferantbestellnummer = $einkaufsdaten['bestellnummer'];
                    }else{
                      $alterek = null;
                      $ekid = null;
                      $altelieferantbestellnummer = null;
                    }

                    if($tmp['lieferantbestellnummer3'][$i]!='') {
                      $nr = $tmp['lieferantbestellnummer3'][$i];
                    }
                    else if($tmp['herstellernummer'][$i]!='') {
                      $nr = $tmp['herstellernummer'][$i];
                    }
                    else {
                      $nr = '';
                    }//$tmp['name_de'][$i];

                    if($alterek != $tmp['lieferanteinkaufnetto3'][$i] || $altelieferantbestellnummer != $nr)
                    {
                      $ekpreisaenderungen++;
                      $this->app->DB->Update("UPDATE einkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY)
                          WHERE adresse='".$lieferantid."' AND artikel='".$artikelid."'
                          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW())
                          AND ab_menge='".$tmp['lieferanteinkaufmenge3'][$i]."' LIMIT 1");

                      if($tmp['lieferanteinkaufvpemenge3'][$i] > 0 && $tmp['lieferanteinkaufmenge3'][$i]<=0 && $tmp['lieferanteinkaufvpepreis3'][$i]  > 0)
                      {
                        $tmp['lieferanteinkaufnetto3'][$i] = $tmp['lieferanteinkaufvpepreis3'][$i] / $tmp['lieferanteinkaufvpemenge3'][$i]*1.00;
                      }


                      if($tmp['lieferanteinkaufmenge3'][$i]<=0){
                        $tmp['lieferanteinkaufmenge3'][$i] = 1;
                      }
                      if($artikelid && $lieferantid)
                      {
                        $aktlieferantid = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id = '$artikelid' LIMIT 1");
                        if(!$aktlieferantid)$this->app->DB->Update("UPDATE artikel SET adresse = '$lieferantid' WHERE id = '$artikelid' LIMIT 1");

                      }
                      $lieferantartikelbezeichnung = '';
                      if($tmp['lieferantartikelbezeichnung3'][$i])
                      {
                        $lieferantartikelbezeichnung = $this->app->DB->real_escape_string($tmp['lieferantartikelbezeichnung3'][$i]);
                      }

                      $ekid = $this->app->erp->AddEinkaufspreis($artikelid,$tmp['lieferanteinkaufmenge3'][$i], $lieferantid,$nr,$lieferantartikelbezeichnung, $tmp['lieferanteinkaufnetto3'][$i],$tmp['lieferanteinkaufwaehrung3'][$i],$tmp['lieferanteinkaufvpemenge3'][$i]);

                    }
                    if($ekid > 0){
                      $this->UpdateEinkaufspreiseExtraWerte($ekid, $tmp,'3');
                    }
                    break;
                  case "adresse":
                    if (!empty($tmp['adresse'][$i])) {
                        $standardlieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer = '".$this->app->DB->real_escape_string($tmp['adresse'][$i])."' LIMIT 1");
                        if ($empty($standardlieferantid)) {
                            $standardlieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE name LIKE '".$this->app->DB->real_escape_string($tmp['adresse'][$i])."' LIMIT 1");
                        }
                        if ($standardlieferantid != '') {
                            $this->app->DB->Update("UPDATE artikel SET adresse='$standardlieferantid' WHERE id='".$artikelid."' LIMIT 1");
                        } else {
                            $importvorlagedoresult['messages'][] = "Lieferant nicht gefunden: ".$tmp[$value][$i];
                        }
                    } else {
                        $this->app->DB->Update("UPDATE artikel SET adresse=0 WHERE id='".$artikelid."' LIMIT 1");
                    }
                   break;
                  case  "verkaufspreisnetto":
                  case  "verkaufspreis1netto":
                  case  "verkaufspreis2netto":
                  case  "verkaufspreis3netto":
                  case  "verkaufspreis4netto":
                  case  "verkaufspreis5netto":
                  case  "verkaufspreis6netto":
                  case  "verkaufspreis7netto":
                  case  "verkaufspreis8netto":
                  case  "verkaufspreis9netto":
                  case  "verkaufspreis10netto":

                    $verkaufspreisanzahl = str_replace(array('verkaufspreis','netto'),array('',''),$value);

                    $gruppe = '';
                    $_kundenid = 0;
                    if(isset($tmp['verkaufspreis'.$verkaufspreisanzahl.'kundennummer']) && $tmp['verkaufspreis'.$verkaufspreisanzahl.'kundennummer'][$i]) {
                      $_kundenid = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht = 0 AND kundennummer = '".$this->app->DB->real_escape_string($tmp['verkaufspreis'.$verkaufspreisanzahl.'kundennummer'][$i])."' and kundennummer != '' LIMIT 1");
                    }
                    if(isset($tmp['verkaufspreis'.$verkaufspreisanzahl.'gruppe'][$i])){
                      $gruppe = $this->app->DB->Select("SELECT id FROM gruppen where kennziffer = '".$tmp['verkaufspreis'.$verkaufspreisanzahl.'gruppe'][$i]."' LIMIT 1");
                    }

                    $altervk = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE artikel='$artikelid' AND ab_menge='".$tmp['verkaufspreis'.$verkaufspreisanzahl.'menge'][$i]."'
                        AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW() ) AND adresse ='$_kundenid' ".($gruppe?" AND gruppe = '".$gruppe."'":" AND ((gruppe IS NULL) or gruppe = '') ")." LIMIT 1");

                    if($altervk != $tmp['verkaufspreis'.$verkaufspreisanzahl.'netto'][$i] && $tmp['verkaufspreis'.$verkaufspreisanzahl.'netto'][$i])
                    {
                      $vkpreisaenderungen++;
                      $gueltigab = null;
                      if($tmp['verkaufspreis'.$verkaufspreisanzahl.'gueltigab'][$i] !== ''){
                        $gueltigab = $this->normalizeDate($tmp['verkaufspreis'.$verkaufspreisanzahl.'gueltigab'][$i]);
                      }
                      $gueltigbis = null;
                      if($tmp['verkaufspreis'.$verkaufspreisanzahl.'gueltigbis'][$i] !== ''){
                        $gueltigbis = $this->normalizeDate($tmp['verkaufspreis'.$verkaufspreisanzahl.'gueltigbis'][$i]);
                      }
                      //verkaufspreis3internerkommentar'][$i]

                      $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY)
                          WHERE artikel='".$artikelid."' AND adresse='$_kundenid' ".($gruppe?" AND gruppe = '".$gruppe."'":" AND ((gruppe IS NULL) or gruppe = '') ")."
                          AND ab_menge='".$tmp['verkaufspreis'.$verkaufspreisanzahl.'menge'][$i]."' LIMIT 1");

                      $verkaufspreis1stueckdivisor = 1;
                      if($tmp['verkaufspreis'.$verkaufspreisanzahl.'preisfuermenge'][$i]!='') {
                        $verkaufspreis1stueckdivisor = $tmp['verkaufspreis'.$verkaufspreisanzahl.'preisfuermenge'][$i];
                      }
                      if($verkaufspreis1stueckdivisor < 1) {
                        $verkaufspreis1stueckdivisor = 1;
                      }

                      if($gruppe)
                      {
                        $this->app->erp->AddVerkaufspreisGruppe($artikelid,$tmp['verkaufspreis'.$verkaufspreisanzahl.'menge'][$i],$gruppe,$tmp['verkaufspreis'.$verkaufspreisanzahl.'netto'][$i]/$verkaufspreis1stueckdivisor,$tmp['verkaufspreis'.$verkaufspreisanzahl.'waehrung'][$i],isset($tmp['verkaufspreis'.$verkaufspreisanzahl.'artikelnummerbeikunde'])?$tmp['verkaufspreis'.$verkaufspreisanzahl.'artikelnummerbeikunde'][$i]:'',$gueltigab,$tmp['verkaufspreis'.$verkaufspreisanzahl.'internerkommentar'][$i], $gueltigbis);
                      }else{
                        $this->app->erp->AddVerkaufspreis($artikelid,$tmp['verkaufspreis'.$verkaufspreisanzahl.'menge'][$i],
                            $_kundenid,$tmp['verkaufspreis'.$verkaufspreisanzahl.'netto'][$i]/$verkaufspreis1stueckdivisor,$tmp['verkaufspreis'.$verkaufspreisanzahl.'waehrung'][$i],isset($tmp['verkaufspreis'.$verkaufspreisanzahl.'artikelnummerbeikunde'])?$tmp['verkaufspreis'.$verkaufspreisanzahl.'artikelnummerbeikunde'][$i]:'',$gruppe, $gueltigab,$tmp['verkaufspreis'.$verkaufspreisanzahl.'internerkommentar'][$i], $gueltigbis);
                      }
                    }
                  break;
                  case "lager_platz":
                  case "lager_platz2":
                  case "lager_platz3":
                  case "lager_platz4":
                  case "lager_platz5":
                    for($lpk = 1; $lpk <= 5; $lpk++) {
                      if($tmp['lager_platz'.($lpk>1?$lpk:'')][$i]!=''){
                        $lager_id = $this->app->DB->Select("SELECT lager FROM lager_platz WHERE
                            kurzbezeichnung='".$tmp['lager_platz'.($lpk>1?$lpk:'')][$i]."' AND kurzbezeichnung!='' AND geloescht!='1' LIMIT 1");
                        if($lager_id <=0)
                        {
                          $lager_id = $this->app->DB->Select("SELECT id FROM lager WHERE geloescht!='1' LIMIT 1");
                        }
                        $felder['lagerartikel']=1;
                        $tmp['lagerartikel'][$i]=1;
                        $this->app->DB->Update("UPDATE artikel SET lagerartikel='1' WHERE id='$artikelid' LIMIT 1");
                        $regal = $this->app->erp->CreateLagerplatz($lager_id,$tmp['lager_platz'.($lpk>1?$lpk:'')][$i]);
                        if($lpk === 1 && !isset($tmp['lager_menge_total']) && !isset($tmp['lager_menge_addieren'])){
                          $this->app->DB->Update("UPDATE `artikel` SET `lager_platz` = '{$regal}' WHERE id = '{$artikelid}' LIMIT 1");
                        }
                        if($tmp['lager_menge_addieren'.($lpk>1?$lpk:'')][$i] > 0){
                          $vpeid = 0;
                          if(isset($tmp['lager_vpe_menge'.$lpk]) && $tmp['lager_vpe_menge'.$lpk][$i] >= 0)
                          {
                            $vpeid = $this->app->erp->CreateLagerPlatzInhaltVPE($artikelid, $tmp['lager_vpe_menge'.$lpk][$i], $tmp['lager_vpe_gewicht'.$lpk][$i],
                              $tmp['lager_vpe_laenge'.$lpk][$i], $tmp['lager_vpe_breite'.$lpk][$i], $tmp['lager_vpe_hoehe'.$lpk][$i],
                              $tmp['lager_vpe_menge'.$lpk.'2'][$i], $tmp['lager_vpe_gewicht'.$lpk.'2'][$i],
                              $tmp['lager_vpe_laenge'.$lpk.'2'][$i], $tmp['lager_vpe_breite'.$lpk.'2'][$i], $tmp['lager_vpe_hoehe'.$lpk.'2'][$i]);
                          }
                          $this->app->erp->LagerEinlagernDifferenz($artikelid,$tmp['lager_menge_addieren'.($lpk>1?$lpk:'')][$i],$regal,"","Importzentrale",1, $vpeid);
                        }
                        //chargen importieren
                        if(!empty($tmp['lager_mhd'.($lpk>1?$lpk:'')][$i]) && !empty($tmp['lager_charge'.($lpk>1?$lpk:'')][$i])){
                          $this->app->erp->AddChargeLagerOhneBewegung(
                            $artikelid,
                            $tmp['lager_menge_addieren' . ($lpk > 1 ? $lpk : '')][$i],
                            $regal,
                            '',
                            $tmp['lager_charge' . ($lpk > 1 ? $lpk : '')][$i]
                          );
                          $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($artikelid, $tmp['lager_menge_addieren' . ($lpk > 1 ? $lpk : '')][$i], $regal, date('Y-m-d', strtotime($tmp['lager_mhd' . ($lpk > 1 ? $lpk : '')][$i])), $tmp['lager_charge' . ($lpk > 1 ? $lpk : '')][$i]);
                        }
                        else if (!empty($tmp['lager_mhd'.($lpk>1?$lpk:'')][$i])){
                          $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($artikelid, $tmp['lager_menge_addieren' . ($lpk > 1 ? $lpk : '')][$i], $regal, date('Y-m-d', strtotime($tmp['lager_mhd' . ($lpk > 1 ? $lpk : '')][$i])), "");
                        }
                        else if(!empty($tmp['lager_charge'.($lpk>1?$lpk:'')][$i])){
                          $this->app->erp->AddChargeLagerOhneBewegung($artikelid, $tmp['lager_menge_addieren' . ($lpk > 1 ? $lpk : '')][$i], $regal, '', $tmp['lager_charge' . ($lpk > 1 ? $lpk : '')][$i]);
                        }

                        if($tmp['lager_menge_total'.($lpk>1?$lpk:'')][$i] >= 0 && $regal > 0 && $tmp['lager_menge_total'.($lpk>1?$lpk:'')][$i]!="")
                        {
                          $tmp_anzahl_lager = $this->app->DB->Select("SELECT SUM(lpi.menge) FROM lager_platz_inhalt lpi LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz
                            WHERE lpi.artikel='$artikelid' AND lp.id='$regal'");
                          if($tmp_anzahl_lager > 0)
                            $this->app->erp->LagerAuslagernRegal($artikelid,$regal,$tmp_anzahl_lager,$projekt,"Importzentrale");
                          // komplett leeren

                          if(isset($tmp['lager_vpe_menge'.$lpk]) && $tmp['lager_vpe_menge'.$lpk][$i] >= 0)
                          {
                            $vpeid = $this->app->erp->CreateLagerPlatzInhaltVPE($artikelid, $tmp['lager_vpe_menge'.$lpk][$i], $tmp['lager_vpe_gewicht'.$lpk][$i],
                              $tmp['lager_vpe_laenge'.$lpk][$i], $tmp['lager_vpe_breite'.$lpk][$i], $tmp['lager_vpe_hoehe'.$lpk][$i],
                              $tmp['lager_vpe_menge'.$lpk.'2'][$i], $tmp['lager_vpe_gewicht'.$lpk.'2'][$i],
                              $tmp['lager_vpe_laenge'.$lpk.'2'][$i], $tmp['lager_vpe_breite'.$lpk.'2'][$i], $tmp['lager_vpe_hoehe'.$lpk.'2'][$i]);
                          }else {
                            $vpeid = 0;
                          }

                          $this->app->erp->LagerEinlagernDifferenz($artikelid,$tmp['lager_menge_total'.($lpk>1?$lpk:'')][$i],$regal,"","Importzentrale",1, $vpeid);
                        }
                      }
                    }
                    break;
                  case "provision1":
                  case "provision2":
                    switch($value)
                    {
                      case "provision1":
                        $pi = 1;
                      break;
                      case "provision2":
                        $pi = 2;
                      break;
                    }

                    if(!empty($tmp['provision'.$pi][$i]))
                    {
                      if(strpos($tmp['provision'.$pi][$i],'%') != false) {
                        $tmp['provision'.$pi][$i] = (float)(str_replace(array('%',','),array('','.'),$tmp['provision'.$pi][$i]));
                      }
                      if(!empty($tmp['provisiongruppe'.$pi][$i]))
                      {
                        if(is_numeric($tmp['provisiongruppe'.$pi][$i]))
                        {
                          $gruppenid = (int)$tmp['provisiongruppe'.$pi][$i];
                        }else{
                          $gruppenid = $this->app->DB->Select("SELECT id FROM gruppen WHERE name like '".$this->app->DB->real_escape_string($tmp['provisiongruppe'.$pi][$i])."' LIMIT 1");
                          if(!$gruppenid) {
                            $gruppenid = $this->app->DB->Select("SELECT id FROM gruppen WHERE kennziffer like '".$this->app->DB->real_escape_string($tmp['provisiongruppe'.$pi][$i])."' LIMIT 1");
                          }
                        }
                        if($gruppenid)
                        {
                          $checkprovision = $this->app->DB->Select("SELECT id FROM provision_regeln WHERE artikel = '$artikelid' AND gruppe = '$gruppenid' LIMIT 1");
                          if(!$this->app->DB->error())
                          {
                            if($checkprovision)
                            {
                              $this->app->DB->Update("UPDATE provision_regeln SET provision = '".(float)$tmp['provision'.$pi][$i]."' WHERE id = '$checkprovision' LIMIT 1");
                              $this->app->DB->Update("UPDATE provision_regeln SET bis = '0000-00-00' WHERE id = '$checkprovision' LIMIT 1");
                            }else{
                              $this->app->DB->Insert("INSERT INTO provision_regeln (artikel, gruppe, provision, typ) VALUES ('$artikelid','$gruppenid','".(float)$tmp['provision'.$pi][$i]."','')");
                              $checkprovision = $this->app->DB->GetInsertID();
                            }
                            if(!empty($tmp['provisiontyp'.$pi][$i]))
                            {
                              $this->app->DB->Update("UPDATE provision_regeln SET typ = '".strtolower(trim($this->app->DB->real_escape_string($tmp['provisiontyp'.$pi][$i])))."' WHERE id = '$checkprovision' LIMIT 1");
                            }
                          }
                        }
                      }elseif(empty($tmp['provisiongruppe'.$pi])){
                        if(empty($tmp['provisionadresse'.$pi][$i]) && empty($tmp['provisionmitarbeiternummer'.$pi][$i]))
                        {
                          $checkprovision = $this->app->DB->Select("SELECT id FROM provisionenartikel_provision WHERE adresse = 0 AND artikel = '$artikelid' LIMIT 1");
                          if($checkprovision)
                          {
                            $this->app->DB->Update("UPDATE provisionenartikel_provision SET provision = '".(float)$tmp['provision'.$pi][$i]."' WHERE id = '$checkprovision' LIMIT 1");
                            $this->app->DB->Update("UPDATE provisionenartikel_provision SET gueltigbis = '0000-00-00' WHERE id = '$checkprovision' LIMIT 1");
                          }else{
                            $this->app->DB->Insert("INSERT INTO provisionenartikel_provision (artikel, adresse, provision) VALUES ('$artikelid','0','".(float)$tmp['provision'.$pi][$i]."')");
                            $checkprovision = $this->app->DB->GetInsertID();
                          }

                          if(!empty($tmp['provisiontyp'.$pi][$i]))
                          {
                            $this->app->DB->Update("UPDATE provisionenartikel_provision SET provisiontyp = '".strtolower(trim($this->app->DB->real_escape_string($tmp['provisiontyp'.$pi][$i])))."' WHERE id = '$checkprovision' LIMIT 1");
                          }
                        }elseif(!empty($tmp['provisionadresse'.$pi][$i])){
                          $checkprovision = $this->app->DB->Select("SELECT id FROM provisionenartikel_provision WHERE adresse = '".(int)$tmp['provisionadresse'.$pi][$i]."' AND artikel = '$artikelid' LIMIT 1");
                          if($checkprovision)
                          {
                            $this->app->DB->Update("UPDATE provisionenartikel_provision SET provision = '".(float)$tmp['provision'.$pi][$i]."' WHERE id = '$checkprovision' LIMIT 1");
                            $this->app->DB->Update("UPDATE provisionenartikel_provision SET gueltigbis = '0000-00-00' WHERE id = '$checkprovision' LIMIT 1");
                          }else{
                            $this->app->DB->Insert("INSERT INTO provisionenartikel_provision (artikel, adresse, provision) VALUES ('$artikelid','".(int)$tmp['provisionadresse'.$pi][$i]."','".(float)$tmp['provision'.$pi][$i]."')");
                            $checkprovision = $this->app->DB->GetInsertID();
                          }
                          if(!empty($tmp['provisiontyp'.$pi][$i]))
                          {
                            $this->app->DB->Update("UPDATE provisionenartikel_provision SET provisiontyp = '".strtolower(trim($this->app->DB->real_escape_string($tmp['provisiontyp'.$pi][$i])))."' WHERE id = '$checkprovision' LIMIT 1");
                          }
                        }elseif(!empty($tmp['provisionmitarbeiternummer'.$pi][$i]))
                        {
                          $provisionadresse = (int)$this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer = '".$this->app->DB->real_escape_string($tmp['provisionmitarbeiternummer'.$pi][$i])."' LIMIT 1");
                          if($provisionadresse)
                          {
                            $checkprovision = $this->app->DB->Select("SELECT id FROM provisionenartikel_provision WHERE adresse = '".$provisionadresse."' AND artikel = '$artikelid' LIMIT 1");
                            if($checkprovision)
                            {
                              $this->app->DB->Update("UPDATE provisionenartikel_provision SET provision = '".(float)$tmp['provision'.$pi][$i]."' WHERE id = '$checkprovision' LIMIT 1");
                              $this->app->DB->Update("UPDATE provisionenartikel_provision SET gueltigbis = '0000-00-00' WHERE id = '$checkprovision' LIMIT 1");
                            }else{
                              $this->app->DB->Insert("INSERT INTO provisionenartikel_provision (artikel, adresse, provision) VALUES ('$artikelid','".$provisionadresse."','".(float)$tmp['provision'.$pi][$i]."')");
                              $checkprovision = $this->app->DB->GetInsertID();
                            }
                            if(!empty($tmp['provisiontyp'.$pi][$i]))
                            {
                              $this->app->DB->Update("UPDATE provisionenartikel_provision SET provisiontyp = '".strtolower(trim($this->app->DB->real_escape_string($tmp['provisiontyp'.$pi][$i])))."' WHERE id = '$checkprovision' LIMIT 1");
                            }
                          }
                        }
                      }
                    }
                  break;
                  case 'standardlagerplatz':
                    if (empty($tmp['standardlagerplatz'][$i])) {
                        $lagerplatz = 'NULL';
                    } else {
                        $lagerplatz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='".$tmp['standardlagerplatz'][$i]."' AND kurzbezeichnung!='' AND geloescht!='1' LIMIT 1");
                        if (empty($lagerplatz)) {
                            $importvorlagedoresult['messages'][] = "Lagerplatz nicht gefunden: ".$tmp['standardlagerplatz'][$i];
                            break;
                        }
                    }
                    $this->app->DB->Update("UPDATE `artikel` SET `lager_platz` = $lagerplatz WHERE id = '{$artikelid}' LIMIT 1");
                  break;
                  default:      
                    $handled = false;
                    foreach (SELF::handled_fields as $handled_field) {
                        if (preg_match($handled_field, $value)) {
                            $handled = true;
                            break;
                        }
                    }
                    if (!$handled) {
                        $importvorlagedoresult['messages'][] = "Feld unbekannt: ".$value;
                    }
                  break;
                }
              }
              if(isset($this->teilprojekt) && isset($this->projekt))
              {
                $sort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM projekt_artikel WHERE teilprojekt = '".$this->teilprojekt."'");
                $menge = 1;
                if(!empty($felder['menge'])){
                  $menge = $felder['menge'];
                }
                if($menge < 1){
                  $menge = 1;
                }
                $this->app->DB->Insert("INSERT INTO projekt_artikel (projekt, teilprojekt,artikel, sort,geplant) VALUES ('".$this->projekt."','".$this->teilprojekt."','$artikelid','$sort','$menge')");
                $projektartikel = $this->app->DB->GetInsertID();
                $vk = $this->app->erp->GetVerkaufspreis($artikelid,$menge);
                $ek = $this->app->erp->GetEinkaufspreis($artikelid, $menge, $this->app->DB->Select("SELECT adresse FROM artikel WHERE id = '$artikelid' LIMIT 1"));
                if(isset($felder['vk_geplant'])){
                  $vk = (double)$felder['vk_geplant'];
                }
                if(isset($felder['ek_geplant'])){
                  $vk = (double)$felder['ek_geplant'];
                }
                if($vk){
                  $this->app->DB->Update("UPDATE projekt_artikel SET vk_geplant = '$vk' WHERE id = '$projektartikel' LIMIT 1");
                }
                if($ek){
                  $this->app->DB->Update("UPDATE projekt_artikel SET ek_geplant = '$ek' WHERE id = '$projektartikel' LIMIT 1");
                }
              }
            } // if($artikelid > 0)
          } // END artikel true condition

          if($this->app->DB->Select("SELECT id FROM artikel WHERE id ='$artikelid' LIMIT 1")){ // !?!?!

              //Sprachen
              $erlaubtefelder= array('name','kurztext','beschreibung','beschreibung_online','meta_title',
                'meta_description','meta_keywords','katalog_bezeichnung','katalog_text','katalogartikel','shop','aktiv');
              $zuImportierendeSprachen = [];
              foreach ($tmp as $feldname => $feldwerte) {
                if(strpos($feldname,'_') !== false){
                  $feldnametmp = explode('_',$feldname);
                  $sprache = $feldnametmp[(!empty($feldnametmp)?count($feldnametmp):0)-1];
                  unset($feldnametmp[(!empty($feldnametmp)?count($feldnametmp):0)-1]);
                  $feldnameohnepsrache = implode('_',$feldnametmp);

                  $output_array = [];
                  preg_match('/\d+/', $feldnameohnepsrache, $output_array);
                  $sprachenSet = '';
                  if(!empty($output_array[0])){
                    $sprachenSet = $output_array[0];
                    $feldnameohnepsrache = str_replace($sprachenSet,'',$feldnameohnepsrache);
                  }

                  $haystack = $zuImportierendeSprachen[$sprache]?$zuImportierendeSprachen[$sprache]:array();
                  if(in_array($feldnameohnepsrache, $erlaubtefelder,false) && !in_array($sprachenSet,$haystack,false)){
                    $zuImportierendeSprachen[$sprache][] = $sprachenSet;
                  }
                }
              }
              $vorhandeSprachenAusDatenbank = $this->app->DB->SelectArr('SELECT iso FROM sprachen WHERE aktiv = 1');
              $vorhandeneSprachen = [];
              foreach ($vorhandeSprachenAusDatenbank as $spracheintrag){
                $vorhandeneSprachen[] = $spracheintrag['iso'];
              }
              foreach ($zuImportierendeSprachen as $sprache => $sprachenSets){
                foreach ($sprachenSets as $sprachenSet){
                  if((!empty($sprachenSet) || (strtoupper($sprache)!=='DE' && strtoupper($sprache)!=='EN'))
                    && in_array(strtoupper($sprache),$vorhandeneSprachen,false)){
                    $upzudatendeFelder = [];
                    foreach ($erlaubtefelder as $erlaubtesFeld){
                      if(isset($tmp[$erlaubtesFeld.$sprachenSet.'_'.$sprache][$i])){
                        $upzudatendeFelder[$this->app->DB->real_escape_string($erlaubtesFeld)] =
                          $this->app->DB->real_escape_string($tmp[$erlaubtesFeld.$sprachenSet.'_'.$sprache][$i]);
                      }
                    }
                    $shopId = '0';
                    if(!empty($upzudatendeFelder['shop'])){
                      $shopId = $upzudatendeFelder['shop'];
                    }
                    if(empty($shopId)){
                      $shopId = '0';
                    }
                    unset($upzudatendeFelder['shop']);
                    if(empty($upzudatendeFelder)){
                      continue;
                    }
                    $query = sprintf("SELECT id FROM artikel_texte WHERE sprache = '%s' AND artikel=%d AND shop=%d LIMIT 1",
                      $this->app->DB->real_escape_string(strtoupper($sprache)),
                      $artikelid,
                      $this->app->DB->real_escape_string($shopId));
                    $atid = $this->app->DB->Select($query);
                    if(empty($atid)){
                      $query = sprintf("INSERT INTO artikel_texte (artikel,sprache, shop, %s) VALUES (%d,'%s',%d,'%s')",
                        implode(',',array_keys($upzudatendeFelder)),
                        $artikelid,
                        $this->app->DB->real_escape_string(strtoupper($sprache)),
                        $shopId,
                        implode("','",$upzudatendeFelder));
                      $this->app->DB->Insert($query);
                    }else{
                      $fieldsToSet = [];
                      foreach ($upzudatendeFelder AS $feldname => $feldwert){
                        $fieldsToSet[] = "$feldname = '$feldwert'";
                      }
                      $query = sprintf('UPDATE artikel_texte SET %s WHERE id=%d',
                        implode(', ',$fieldsToSet),
                        $atid);
                      $this->app->DB->Update($query);
                    }
                  }
                }
            } // Sprachen

            // Artikeleigenschaften
            // leer = löschen

            $artikeleigenschaften = array();
            foreach ($tmp as $feldname => $feldwerte) {
                if (strpos($feldname,'eigenschaftname') !== false) {
                    $eigenschaftspaltennummer = substr($feldname,strlen('eigenschaftname'));
                    $artikeleigenschaften[$feldwerte[$i]] = $tmp['eigenschaftwert'.$eigenschaftspaltennummer][$i];
                }
            }
            foreach ($artikeleigenschaften as $key => $value) {
                $sql = "INSERT INTO artikeleigenschaften (name) VALUES ('".$key."') ON DUPLICATE KEY UPDATE name = '".$key."'";
                $this->app->DB->Update($sql);
                $sql = "INSERT INTO artikeleigenschaftenwerte (artikel, artikeleigenschaften, wert) VALUES ('".$artikelid."' ,(SELECT id FROM artikeleigenschaften WHERE name = '".$key."'), '".$value."') ON DUPLICATE KEY UPDATE wert = '".$value."'";
                $this->app->DB->Update($sql);
            }
            $sql = "DELETE FROM artikeleigenschaftenwerte WHERE wert = ''";
            $this->app->DB->Delete($sql);

            //freifelduebersetzungen
            foreach ($tmp as $feldname => $feldwerte) {
              if(strpos($feldname,'freifeld') !== false && strpos($feldname,'_')>0){
                $felddaten = explode('_',$feldname);
                $feldnummer = (int)substr($felddaten[0],8);
                $sprache = strtoupper($felddaten[1]);
                if($feldnummer>=1 && $feldnummer<=40 && $this->app->DB->Select("SELECT id FROM sprachen WHERE iso='$sprache' AND iso<>'' AND aktiv = 1 LIMIT 1")){
                  $sqla = null;
                  for ($f = 1; $f <= 40; $f++) {
                    $sqla[] = ' SELECT '.$f.' as nummer ';
                  }
                  $sql = "INSERT INTO artikel_freifelder (artikel, sprache, nummer, wert)
                    SELECT '$artikelid', s.iso, n.nummer,''
                    FROM (SELECT iso FROM sprachen WHERE aktiv = 1 AND iso <> 'DE' AND iso <> '' GROUP BY iso) s
                    INNER JOIN (".implode(' UNION ', $sqla).") n
                    LEFT JOIN artikel_freifelder af ON s.iso = af.sprache AND af.artikel = '$artikelid' AND n.nummer = af.nummer
                    WHERE  isnull(af.id)
                  ";
                  $this->app->DB->Insert($sql);

                  $feldid = $this->app->DB->Select("SELECT id FROM artikel_freifelder WHERE artikel='$artikelid' AND sprache='$sprache' AND nummer='$feldnummer'");
                  $wert = $feldwerte[$i];
                  if($feldid){
                    $this->app->DB->Update("UPDATE artikel_freifelder SET wert='$wert' WHERE id='$feldid'");
                  }else{
                    $this->app->DB->Insert("INSERT INTO artikel_freifelder (artikel, sprache, nummer, wert) VALUES ('$artikelid', '$sprache','$feldnummer', '$wert')");
                  }
                }
              }
            }

            //Shops
            foreach ($tmp as $feldname => $feldwerte) {
              $feldtmp = explode('_',$feldname);
              if($feldtmp['0'] === 'shop' || $feldtmp['0'] === 'aktiv'){
                if(isset($feldtmp['1'])){
                  $shopid = (int)$feldtmp['1'];
                  $wert = (int)$feldwerte[$i];
                  if($this->app->DB->Select("SELECT id FROM shopexport WHERE id ='$shopid' LIMIT 1")){
                    $artikelonlineshopsid = $this->app->DB->Select("SELECT id FROM artikel_onlineshops WHERE artikel='$artikelid' AND shop='$shopid'");
                    if($feldtmp['0'] === 'shop'){
                      if($artikelonlineshopsid < 1){
                        if($wert == 1){
                          $this->app->DB->Insert("INSERT INTO artikel_onlineshops (artikel, shop, aktiv, ausartikel,autolagerlampe) VALUES ('$artikelid','$shopid',1,1,0)");
                        }
                      }elseif($wert == 0){
                        $this->app->DB->Select("DELETE FROM artikel_onlineshops WHERE artikel='$artikelid' AND shop='$shopid'");
                      }
                    }else if($feldtmp['0'] === 'aktiv'){
                      if($wert > 1){
                        $wert = 1;
                      }
                      $this->app->DB->Update("UPDATE artikel_onlineshops SET aktiv='$wert' WHERE artikel='$artikelid' AND shop='$shopid'");
                    }
                  }
                }
              }
            }

            //Fremdnummern
            foreach ($tmp as $feldname => $feldwerte) {
              $feldtmp = explode('_',$feldname);

              $output_array = [];
              preg_match('/\d+/', $feldtmp[0], $output_array);
              $fremdnummerSet = '0';
              if(!empty($output_array[0])){
                $fremdnummerSet = $output_array[0];
                $feldtmp['0'] = str_replace($fremdnummerSet,'',$feldtmp['0']);
              }

              if(!empty($fremdnummerSet) && !empty($feldtmp['1']) &&  $feldtmp['0'] === 'fremdnummer'){
                $shopId = (int)$feldtmp['1'];
                $shopId = $this->app->DB->Select("SELECT id FROM shopexport WHERE id ='$shopId' LIMIT 1");
                if(empty($shopId)){
                  //Shop ID nicht vorhanden, überspringen
                  continue;
                }
                $bezeichnung = '';
                if(!empty($tmp['fremdnummerbezeichnung'.$fremdnummerSet.'_'.$shopId][$i])){
                  $bezeichnung = $tmp['fremdnummerbezeichnung'.$fremdnummerSet.'_'.$shopId][$i];
                }
                $fremdnummer = $feldwerte[$i];

                $query = sprintf("SELECT id FROM artikelnummer_fremdnummern
                                WHERE artikel='%d' AND shopid='%d' AND bezeichnung='%s' AND nummer='%s' LIMIT 1",
                          $artikelid,$shopId,$bezeichnung,$fremdnummer);
                $fremdnummerId = $this->app->DB->Select($query);

                //Alle alten inaktiv machen
                $query = sprintf("UPDATE artikelnummer_fremdnummern SET aktiv='0' WHERE artikel=%d AND bezeichnung='%s' AND shopid=%d",
                  $artikelid,$bezeichnung,$shopId);
                $this->app->DB->Update($query);

                if(empty($fremdnummerId)){
                  $query = sprintf("INSERT INTO artikelnummer_fremdnummern (artikel, aktiv, nummer, shopid, zeitstempel, bearbeiter, bezeichnung)
                    VALUES ('%d','1','%s','%d',NOW(),'%s','%s')",
                    $artikelid,
                    trim($this->app->DB->real_escape_string($feldwerte[$i])),
                    $shopId,
                    $this->app->DB->real_escape_string($this->app->User->GetName()),
                    $bezeichnung);
                  $this->app->DB->Insert($query);
                }elseif(!empty($fremdnummer)){
                  $query = sprintf("UPDATE artikelnummer_fremdnummern SET aktiv='1',nummer='%s' WHERE id=%d",
                    $fremdnummer,$fremdnummerId);
                  $this->app->DB->Update($query);
                }
              }
            } // Fremdnummern
          }
          break; // HERE END artikel einkauf
            case "zeiterfassung":
            case "wiedervorlagen":
            case "notizen":
          if($tmp['cmd'][$i]==='create')
          {
            if($tmp['nummer'][$i]!='')
            {
              foreach($fieldset as $k => $v)
              {
                $bedingung = '';
                $value = '';
                $fieldname = '';
                if(isset($fieldset[$k]['bedingung'])) {
                  $bedingung = $fieldset[$k]['bedingung'];
                }

                if(isset($v['nr']))
                {
                  $value = trim($tmp[$v['field']][$i]);
                  if(isset($v['inv']))
                  {
                    if($value != '1')
                    {
                      $value = 1;
                    }else{
                      $value = 0;
                    }
                  }
                }
                elseif(isset($v['vorlage']))
                {
                  $value = $v['vorlage'];
                }
                if(isset($v['bedingung'])) {
                  $value = $this->ImportvorlageBedingung($value, $v['bedingung']);
                }
                $fieldname = $v['field'];
                $felder[$fieldname] = $value;
              }
            }

            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp['kundennummer'][$i]."' LIMIT 1");
            $mitarbeiteradresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='".$tmp['mitarbeiternummer'][$i]."' LIMIT 1");
            if($ziel==='zeiterfassung')
            {
              $datumVon = $this->normalizeDate($tmp['datum_von'][$i]);
              $datumBis = $this->normalizeDate($tmp['datum_bis'][$i]);
              $vonZeit = $datumVon.' '.$tmp['zeit_von'][$i].':00';
              $bisZeit = $datumBis.' '.$tmp['zeit_bis'][$i].':00';
              $ort = '';
              $projekt = '';
              $art = '';
              $kunde = $adresse;
              if(!$tmp['taetigkeit'][$i]){
                $tmp['taetigkeit'][$i]='Zeiterfassung';
              }
              $mitarbeiterid  = $this->app->User->GetID();
              if(isset($tmp['mitarbeiternummer'][$i]) && $tmp['mitarbeiternummer'][$i]){
                $mitarbeiternummer = $tmp['mitarbeiternummer'][$i];
                $mitarbeiterid = $this->app->DB->Select("SELECT id FROM adresse where mitarbeiternummer = '$mitarbeiternummer' AND mitarbeiternummer <> '' AND geloescht=0 LIMIT 1");
              }
              if(!$mitarbeiterid){
                break;
              }
              $this->app->erp->AddArbeitszeit($mitarbeiterid, $vonZeit, $bisZeit, $tmp['taetigkeit'][$i], $tmp['details'][$i],$ort, $projekt, 0,$art,$kunde);
            }
            else if ($ziel==='wiedervorlagen') {
              $betreff = $tmp['betreff'][$i];
              $text = $tmp['text'][$i];
              $datum_faellig = date('Y-m-d',strtotime($tmp['datum_faellig'][$i]));
              if($tmp['uhrzeit_faellig'][$i]==''){
                $uhrzeit_faellig = '00:00:00';
              }
              else if(strlen($tmp['uhrzeit_faellig'][$i])<=5){
                $uhrzeit_faellig = $tmp['uhrzeit_faellig'][$i] . ':00';
              }
              else {
                $uhrzeit_faellig = $tmp['uhrzeit_faellig'][$i];
              }

              if($tmp['abgeschlossen'][$i]!='1') {
                $abgeschlossen=0;
              } else $abgeschlossen=1;
              if($tmp['prio'][$i]!='1') {
                $prio=0;
              } else {
                $prio=1;
              }

              $this->app->DB->Insert("INSERT INTO wiedervorlage (id,adresse,adresse_mitarbeiter,bearbeiter,bezeichnung,beschreibung,datum_erinnerung,zeit_erinnerung,datum_angelegt,abgeschlossen,prio)
                VALUES ('',$adresse,$mitarbeiteradresse,".$mitarbeiteradresse.",'$betreff','$text','$datum_faellig','$uhrzeit_faellig',NOW(),$abgeschlossen,$prio)");

            }
            else if ($ziel==='notizen')
            {
              $betreff = $tmp['betreff'][$i];
              $text = $tmp['text'][$i];
              $datum = date('Y-m-d',strtotime($tmp['datum'][$i]));
              if($tmp['uhrzeit'][$i]==''){
                $uhrzeit = '00:00:00';
              }
              else if(strlen($tmp['uhrzeit'][$i])<=5){
                $uhrzeit = $tmp['uhrzeit'][$i] . ':00';
              }
              else {
                $uhrzeit = $tmp['uhrzeit'][$i];
              }

		if (is_null($mitarbeiteradresse)) {
			$mitarbeiteradresse = '';
		}

              if($mitarbeiteradresse>0){
                $bearbeiter = $this->app->DB->Select("SELECT CONCAT(mitarbeiternummer,' ',name) FROM adresse WHERE id='$mitarbeiteradresse' LIMIT 1");
              }
              else {
                $bearbeiter='Import';
              }

		$query = "INSERT INTO dokumente (id,adresse_to,adresse_from,typ,betreff,content,datum,uhrzeit,bearbeiter)
                VALUES ('','$adresse','$mitarbeiteradresse','notiz','$betreff','$text','$datum','$uhrzeit','$bearbeiter')";

              $this->app->DB->Insert($query);
            }
          } // HERE END UPDATE ARTIKEL true condition
          break;
            case "adresse":

          // wenn import per datei
          if($stueckliste_csv !=''){
            $tmp['checked'][$i] = 1;
          }

          if($tmp['projekt'][$i]!='')
          {
            $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$this->app->DB->real_escape_string($tmp['projekt'][$i])."' AND abkuerzung!='' LIMIT 1");
          }

          // automatisch create und update erkennen
          if($tmp['kundennummer'][$i]=="" && $tmp['lieferantennummer'][$i]=="" && $tmp['name'][$i]=="" && $tmp['firma'][$i]=="")
          {
            $tmp['cmd'][$i]="none";
            $tmp['checked'][$i]=0;
          }
          else if($tmp['kundennummer'][$i]=="" && ($tmp['name'][$i]!="" || $tmp['firma'][$i]!="") && $tmp['lieferantennummer'][$i]=="")
          {
            $tmp['cmd'][$i]="create";
          }
          else if($tmp['lieferantennummer'][$i]!="" || $tmp['kundennummer'][$i]!="")
          {
            $checkkunde = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$this->app->DB->real_escape_string($tmp['kundennummer'][$i])."' AND kundennummer!='' LIMIT 1");
            if($checkkunde <= 0)
              $tmp['cmd'][$i]="create";
            else
              $tmp['cmd'][$i]="update";

            if($checkkunde <= 0)
            {
              $checklieferant = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$this->app->DB->real_escape_string($tmp['lieferantennummer'][$i])."' AND lieferantennummer!='' LIMIT 1");
              if($checklieferant <= 0){
                $tmp['cmd'][$i] = "create";
              }
              else{
                $tmp['cmd'][$i] = "update";
              }
            }
          }



          // automatisch create und update erkennen
          if($tmp['cmd'][$i]==='create')
          {
            $adresse=0;


            foreach($fieldset as $k => $v)
            {
              $bedingung = '';
              $value = '';
              $fieldname = '';
              if(isset($v['bedingung'])) {
                $bedingung = $v['bedingung'];
              }

              if(isset($v['nr']))
              {
                $value = trim($tmp[$v['field']][$i]);
                if(isset($v['inv']))
                {
                  if($value != "1")
                  {
                    $value = 1;
                  }else{
                    $value = 0;
                  }
                }
              }
              elseif(isset($v['vorlage']))
              {
                $value = $v['vorlage'];
              }
              if(isset($v['bedingung'])) {
                $value = $this->ImportvorlageBedingung($value, $v['bedingung']);
              }
              $fieldname = $v['field'];
              $felder[$fieldname] = $value;
            }


            if(($tmp['kundennummer'][$i]!='' && $tmp['kundennummer'][$i]!=='NEW' && $tmp['kundennummer'][$i]!=='NEU')
              || ($tmp['lieferantennummer'][$i]!='' && $tmp['lieferantennummer'][$i]!=='NEW' && $tmp['lieferantennummer'][$i]!=='NEU'))
            {
              $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$this->app->DB->real_escape_string($tmp['kundennummer'][$i])."' AND kundennummer!='' LIMIT 1");
              if($adresse <=0){
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='" . $this->app->DB->real_escape_string($tmp['lieferantennummer'][$i]) . "' AND lieferantennummer!='' LIMIT 1");
              }
            }
            $felder['strasse'] = $felder['strasse']." ".$felder['hausnummer'];
            if($felder['strasse_hausnummer']!="") $felder['strasse'] = $felder['strasse_hausnummer'];

            $felder['email'] = str_replace(" ","",$felder['email']);

            $felder['sprache'] = strtolower($felder['sprache']);
            $felder['typ'] = $this->TypErkennung($felder['typ']);

            $laenderliste = $this->app->erp->GetSelectLaenderliste(true);
            if(array_key_exists($felder['land'], $laenderliste)){
              $felder['land'] = $laenderliste[$felder['land']];
            }else{
              if(!in_array($felder['land'], $laenderliste)){
                $felder['land'] = 'DE';
              }
            }
            if($felder['land'] == ''){
              $felder['land'] = 'DE';
            }


            $felder['zahlungsweise']  = strtolower($felder['zahlungsweise']);
            $felder['versandart']  = strtolower($felder['versandart']);
            $felder['ust_befreit']  = $this->UstBefreitErkennung($felder['ust_befreit']);
            $felder['umsatzsteuer_lieferant'] = $this->UstErkennungLieferant($felder['umsatzsteuer_lieferant']);

            if($felder['geburtstag']!="")
            {
              if(strpos($felder['geburtstag'],'.')!==false) {
                $felder['geburtstag'] = $this->app->String->Convert($felder['geburtstag'],"%1.%2.%3","%3-%2-%1");
              }
            }
            if($felder['lead']!="")
            {
              $felder['lead'] = '1';
            }

            if($felder['mandatsreferenzdatum']!="")
            {
              if(strpos($felder['mandatsreferenzdatum'],'.')!==false) {
                $felder['mandatsreferenzdatum'] = $this->app->String->Convert($felder['mandatsreferenzdatum'],"%1.%2.%3","%3-%2-%1");
              }
            }

            if($felder['liefersperredatum']!="")
            {
              if(strpos($felder['liefersperredatum'],'.')!==false) {
                $felder['liefersperredatum'] = $this->app->String->Convert($felder['liefersperredatum'],"%1.%2.%3","%3-%2-%1");
              }
            }

            if(isset($felder['liefersperre']))
            {
              if($felder['liefersperre'] === "1" || strtolower($felder['liefersperre']) === 'x'|| substr(strtolower($felder['liefersperre']),0,1) === 'j')
              {
                $felder['liefersperre'] = 1;
              }elseif($felder['liefersperre'] === "0" || strtolower($felder['liefersperre']) === ''|| substr(strtolower($felder['liefersperre']),0,1) === 'n')
              {
                $felder['liefersperre'] = 0;
              }
            }


            if($felder['plz_ort']!="")
            {
              $felder['plz'] = strstr($felder['plz_ort'], ' ', true);
              $felder['ort'] = strstr($felder['plz_ort'], ' ');
            }

            if($felder['name']!="" || $felder['firma']!="")
            {
              if($felder['firma']!="")
              {
                if($felder['vorname']!="")
                  $felder['ansprechpartner']=$felder['vorname']." ".$felder['name'];
                else
                  $felder['ansprechpartner']=$felder['name'];

                $felder['name']=$felder['firma'];
                $felder['typ']='firma';
              } else {
                if($felder['vorname']!="" && $felder['nachname']=="")
                  $felder['name']=$felder['vorname']." ".$felder['name'];
              }

              $felder['typ'] = $this->TypErkennung($felder['typ']);

              if($felder['rechnung_firma']!="")
              {
                if($felder['rechnung_vorname']!="")
                  $felder['rechnung_ansprechpartner']=$felder['rechnung_vorname']." ".$felder['rechnung_name'];
                else
                  $felder['rechnung_ansprechpartner']=$felder['rechnung_name'];

                $felder['rechnung_name']=$felder['rechnung_firma'];
                $felder['rechnung_typ']='firma';
              } else {
                if($felder['rechnung_vorname']!="" && $felder['rechnung_nachname']=="")
                  $felder['rechnung_name']=$felder['rechnung_vorname']." ".$felder['rechnung_name'];
              }

              if($felder['rechnung_typ']!="")
                $felder['rechnung_typ'] = $this->TypErkennung($felder['rechnung_typ']);



              $als_ansprechpartner_speichern = false;
              //pruefe ob datensatz ein ansprechpartner werden soll
              if($felder['lieferantennummer']!=str_replace("ANSPRECHPARTNER","",$felder['lieferantennummer']))
              {
                // Dieser Datensatz wird als ansprechpartner verwendet
                $als_ansprechpartner_speichern = true;
                $ermittle_adresse = str_replace("ANSPRECHPARTNER","",$felder['lieferantennummer']);
                $ermittle_adresse = rtrim(ltrim($ermittle_adresse," :"));
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='$ermittle_adresse' AND lieferantennummer!='' LIMIT 1");

                if($tmp['strasse_hausnummer'][$i]!="") $tmp['strasse'][$i] = $felder['strasse_hausnummer'];
                $tmp['typ'][$i] = $this->TypErkennung($felder['typ']);

                $tmp['name'][$i] = $felder['name'];

                $data_fields = array('typ','name','abteilung','unterabteilung','adresszusatz','titel','strasse','ort','plz',
                    'land','telefon','telefax','email','mobil','anschreiben');

                foreach($data_fields as $tmp_key=>$data_field_key)
                {
                  $data_ansprechpartner[$data_field_key] = $tmp[$data_field_key][$i];
                }
                $this->app->erp->ImportCreateAnsprechpartner($adresse,$data_ansprechpartner);
              }
              else if($felder['kundennummer']!=str_replace('ANSPRECHPARTNER','',$felder['kundennummer']))
              {
                // Dieser Datensatz wird als ansprechpartner verwendet
                $als_ansprechpartner_speichern = true;
                $ermittle_adresse = str_replace("ANSPRECHPARTNER",'',$felder['kundennummer']);
                $ermittle_adresse = rtrim(ltrim($ermittle_adresse,' :'));
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$ermittle_adresse' AND kundennummer!='' LIMIT 1");

                if($tmp['strasse_hausnummer'][$i]!='') {
                  $tmp['strasse'][$i] = $felder['strasse_hausnummer'];
                }
                $tmp['typ'][$i] = $this->TypErkennung($felder['typ']);

                $tmp['name'][$i] = $felder['name'];

                $data_fields = array('typ','name','abteilung','unterabteilung','adresszusatz','titel','strasse','ort','plz',
                    'land','telefon','telefax','email','mobil','anschreiben');

                foreach($data_fields as $tmp_key=>$data_field_key)
                {
                  $data_ansprechpartner[$data_field_key] = $tmp[$data_field_key][$i];
                }
                $this->app->erp->ImportCreateAnsprechpartner($adresse,$data_ansprechpartner);
              }
              else {
                $loeschen_lfr_new=false;
                if(strtoupper($felder['lieferantennummer'])==='NEW' || strtoupper($felder['lieferantennummer'])==='NEU'){
                  $loeschen_lfr_new = true;
                }

                $loeschen_kd_new=false;
                if(strtoupper($felder['kundennummer'])==='NEW' || strtoupper($felder['kundennummer'])==='NEU' ){
                  $loeschen_kd_new = true;
                }

                $loeschen_mi_new=false;
                if(strtoupper($felder['mitarbeiternummer'])==='NEW' || strtoupper($felder['mitarbeiternummer'])==='NEU' ){
                  $loeschen_mi_new = true;
                }

                if($loeschen_lfr_new) {
                  $felder['lieferantennummer']='';
                }
                if($loeschen_kd_new) {
                  $felder['kundennummer']='';
                }
                if($loeschen_mi_new) {
                  $felder['mitarbeiternummer']='';
                }
                foreach($felder as $kk => $vv){
                  $__felder[$kk] = $this->app->DB->real_escape_string($vv);
                }
                $adresse =$this->app->erp->ImportCreateAdresse($__felder, false);
                unset($__felder);
                $this->app->erp->ImportvorlageLog($id,$zeitstempel,'adresse',$adresse,$ersterdatensatz);
                $ersterdatensatz=0;

                if($felder['lieferantennummer']!='' || $loeschen_lfr_new)
                {
                  $this->app->erp->AddRolleZuAdresse($adresse, 'Lieferant', 'von','Projekt',$projektid);
                }
                if($felder['kundennummer']!='' || $loeschen_kd_new)
                {
                  $this->app->erp->AddRolleZuAdresse($adresse, 'Kunde', 'von','Projekt',$projektid);
                }
                if($felder['mitarbeiternummer']!='' || $loeschen_mi_new)
                {
                  $this->app->erp->AddRolleZuAdresse($adresse, 'Mitarbeiter', 'von','Projekt',$projektid);
                }
              }

              if($adresse)
              {
                for($gk = 1; $gk <= 5; $gk++)
                {
                  $key = "gruppe".$gk;
                  if(trim($tmp[$key][$i]) != '')
                  {
                    $gr = $this->app->DB->Select("SELECT id FROM gruppen WHERE kennziffer = '".$this->app->DB->real_escape_string(trim($tmp[$key][$i]))."' LIMIT 1");
                    if(!$gr) {
                      $gr = $this->app->DB->Select("SELECT id FROM gruppen WHERE name = '".$this->app->DB->real_escape_string(trim($tmp[$key][$i]))."' LIMIT 1");
                    }
                    if($gr)
                    {
                      $check = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE adresse = '$adresse' AND objekt like 'Gruppe' AND parameter = '$gr' AND (bis = '0000-00-00' OR isnull(bis) OR bis >= curdate()) LIMIT 1");
                      if(!$check)
                      {
                        $projekt = $projektid;
                        if(!$projekt) {
                          $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id = '$adresse' LIMIT 1");
                        }
                        $this->app->DB->Insert("INSERT INTO adresse_rolle (adresse, projekt, subjekt, praedikat, objekt, parameter, von, bis) VALUES ('$adresse','$projekt','Mitglied','von','Gruppe','$gr',curdate(),'0000-00-00')");
                      }
                    }
                  }
                }
              }

              foreach ($felder as $key => $value) {
                if($key==="kundennrkontobuchhaltung")
                {
                  $this->app->DB->Update("UPDATE adresse SET kundennummer_buchhaltung = '".$value."' WHERE id='$adresse' LIMIT 1");
                }
                if($key==="lieferantennrkontobuchhaltung")
                {
                  $this->app->DB->Update("UPDATE adresse SET lieferantennummer_buchhaltung = '".$value."' WHERE id='$adresse' LIMIT 1");
                }
              }

              for ($l=1; $l<=3; $l++){
                //!$als_ansprechpartner_speichern bezieht sich auf die Hauptadresse aus dem Datensatz
                // Ansprechpartner werden nur als Ansprechpartner gespeichert wenn die Huaptadresse ihrerseits kein Ansprechpartner ist
                if(!$als_ansprechpartner_speichern && !empty($felder['ansprechpartner'.$l.'name']))
                {
                  unset($data);
                  $data['name']=$felder['ansprechpartner'.$l.'name'];
                  $data['typ']=$this->TypErkennung($felder['ansprechpartner'.$l.'typ']);
                  $data['strasse']=$felder['ansprechpartner'.$l.'strasse'];
                  $data['sprache']=$felder['ansprechpartner'.$l.'sprache'];
                  $data['bereich']=$felder['ansprechpartner'.$l.'bereich'];
                  $data['abteilung']=$felder['ansprechpartner'.$l.'abteilung'];
                  $data['unterabteilung']=$felder['ansprechpartner'.$l.'unterabteilung'];
                  $data['land']=$felder['ansprechpartner'.$l.'land'];
                  $data['ort']=$felder['ansprechpartner'.$l.'ort'];
                  $data['plz']=$felder['ansprechpartner'.$l.'plz'];
                  $data['telefon']=$felder['ansprechpartner'.$l.'telefon'];
                  $data['telefax']=$felder['ansprechpartner'.$l.'telefax'];
                  $data['mobil']=$felder['ansprechpartner'.$l.'mobil'];
                  $data['email']=$felder['ansprechpartner'.$l.'email'];
                  $data['sonstiges']=$felder['ansprechpartner'.$l.'sonstiges'];
                  $data['adresszusatz']=$felder['ansprechpartner'.$l.'adresszusatz'];
                  $data['ansprechpartner_land']=$felder['ansprechpartner'.$l.'ansprechpartner_land'];
                  $data['anschreiben']=$felder['ansprechpartner'.$l.'anschreiben'];
                  $data['titel']=$felder['ansprechpartner'.$l.'titel'];
                  $data['marketingsperre']=$felder['ansprechpartner'.$l.'marketingsperre'];
                  $vorhanden = !empty($this->app->DB->Select("SELECT id FROM ansprechpartner WHERE adresse='$adresse'
                    AND name='".$data['name']."' AND strasse='".$data['strasse']."' AND ort='".$data['ort']."'
                    AND plz='".$data['plz']."' AND email='".$data['email']."' LIMIT 1"));

                  if(!$vorhanden){
                    $this->app->erp->CreateAnsprechpartner($adresse,$data);
                  }
                }

                if(!empty($felder['lieferadresse'.$l.'name']))
                {
                  unset($data);
                  $data['name']=$felder['lieferadresse'.$l.'name'];
                  $data['typ']=$this->TypErkennung($felder['lieferadresse'.$l.'typ']);
                  $data['strasse']=$felder['lieferadresse'.$l.'strasse'];
                  $data['abteilung']=$felder['lieferadresse'.$l.'abteilung'];
                  $data['unterabteilung']=$felder['lieferadresse'.$l.'unterabteilung'];
                  $data['land']=$felder['lieferadresse'.$l.'land'];
                  $data['ort']=$felder['lieferadresse'.$l.'ort'];
                  $data['plz']=$felder['lieferadresse'.$l.'plz'];
                  $data['telefon']=$felder['lieferadresse'.$l.'telefon'];
                  $data['telefax']=$felder['lieferadresse'.$l.'telefax'];
                  $data['email']=$felder['lieferadresse'.$l.'email'];
                  $data['sonstiges']=$felder['lieferadresse'.$l.'sonstiges'];
                  $data['adresszusatz']=$felder['lieferadresse'.$l.'adresszusatz'];
                  $data['ansprechpartner']=$felder['lieferadresse'.$l.'ansprechpartner'];
                  $data['standardlieferadresse']=$felder['lieferadresse'.$l.'standardlieferadresse'];
                  $data['gln']=$felder['lieferadresse'.$l.'gln'];
                  $data['ustid']=$felder['lieferadresse'.$l.'ustid'];
                  $ust_befreit = (string)$felder['lieferadresse'.$l.'ust_befreit'];
                  switch (strtolower($ust_befreit)){
                    case '0':
                    case 'inland':
                      $ust_befreit = 0;
                      break;
                    case '1':
                    case 'eu-lieferung':
                      $ust_befreit = 1;
                      break;
                    case '2':
                    case 'export':
                      $ust_befreit = 2;
                      break;
                    case '3':
                    case 'steuerfrei inland':
                      $ust_befreit = 3;
                      break;
                  }
                  if(!empty($ust_befreit)){
                    $data['ust_befreit']=$ust_befreit;
                  }

                  $this->app->erp->CreateLieferadresse($adresse,$data);
                }
              }
              //rolle verpassen
            }

          }
          else if($tmp['cmd'][$i]==='update')// && $tmp['checked'][$i]=="1")
          {

            $adresse=0;
            $ansprechpartnerarray = array();
            if($tmp['kundennummer'][$i]!="" || $tmp['lieferantennummer'][$i]!="")
            {
              $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp['kundennummer'][$i]."' AND kundennummer!='' LIMIT 1");
              if($adresse <=0)
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$tmp['lieferantennummer'][$i]."' AND lieferantennummer!='' LIMIT 1");
            }

            if($adresse > 0)
            {
              $fields_tmp = '';

              foreach($fieldset as $k => $v)
              {
                $bedingung = '';
                $value = '';
                $fieldname = '';
                if(isset($fieldset[$k]['bedingung'])) {
                  $bedingung = $fieldset[$k]['bedingung'];
                }

                if(isset($fieldset[$k]['nr']))
                {
                  $value = trim($data[$fieldset[$k]['nr'] - 1]);
                  if(isset($fieldset[$k]['inv']))
                  {
                    if($value != "1")
                    {
                      $value = 1;
                    }else{
                      $value = 0;
                    }
                  }
                } elseif(isset($fieldset[$k]['vorlage']))
                {
                  $value = $fieldset[$k]['vorlage'];
                }
                if(isset($fieldset[$k]['bedingung'])) {
                  $value = $this->ImportvorlageBedingung($value, $fieldset[$k]['bedingung']);
                }
                $fieldname = $fieldset[$k]['field'];
                $felder[$fieldname] = $value;

                $key = $fieldname;
                $value = $fieldname;

                $felder[$key]=$tmp[$value][$i];

                if($key=="typ" || $key=="zahlungsweise"){
                  $tmp[$value][$i] = strtolower($tmp[$value][$i]);
                }

                if($key=="typ" || $key=="rechnung_typ")
                {
                  $tmp[$value][$i] = $this->TypErkennung($tmp[$value][$i]);
                }

                if($key=="ust_befreit")
                {
                  $tmp[$value][$i] = $this->UstBefreitErkennung($tmp[$value][$i]);
                }
                if($key==='umsatzsteuer_lieferant'){
                  $tmp[$value][$i] = $this->UstErkennungLieferant($tmp[$value][$i]);
                }

                if($key=="land") {
                  $laenderliste = $this->app->erp->GetSelectLaenderliste(true);
                  if(array_key_exists($tmp[$value][$i], $laenderliste)){
                    $tmp[$value][$i] = $laenderliste[$tmp[$value][$i]];
                  }else{
                    if(!in_array($tmp[$value][$i], $laenderliste)){
                      $tmp[$value][$i] = 'DE';
                    }
                  }
                  if($tmp[$value][$i] == ''){
                    $tmp[$value][$i] = 'DE';
                  }
                  //if($tmp[$value][$i]=="Deutschland" || $tmp[$value][$i]=="Germany" || $tmp[$value][$i]=="")
                   // $tmp[$value][$i] = "DE";
                }

                if($key=="rechnung_vorname")
                {
                  if($tmp['rechnung_vorname'][$i]!="") $tmp['rechnung_name'][$i] = $tmp['rechnung_vorname'][$i]." ".$tmp['rechnung_name'][$i];
                }

                if($key=="name")
                {
                  if($tmp['vorname'][$i]!="" && $tmp['firma'][$i]=="") $tmp['name'][$i] = $tmp['vorname'][$i]." ".$tmp['name'][$i];

                  if($tmp['firma'][$i]!='') {
                    if($tmp['name'][$i]!="") {
                      if($tmp['vorname'][$i]!="")
                        $this->app->DB->Update("UPDATE adresse SET ansprechpartner='".$tmp['vorname'][$i]." ".$tmp['name'][$i]."' WHERE id='$adresse' LIMIT 1");
                      else
                        $this->app->DB->Update("UPDATE adresse SET ansprechpartner='".$tmp['name'][$i]."' WHERE id='$adresse' LIMIT 1");
                    }
                    $this->app->DB->Update("UPDATE adresse SET name='".$tmp['firma'][$i]."' WHERE id='$adresse' LIMIT 1");
                  }
                }
                if($key=="firma")
                {
                  if(!empty($tmp['firma'][$i])){
                    $this->app->DB->Update("UPDATE adresse SET typ='firma', name='".$tmp['firma'][$i]."',ansprechpartner='".$tmp['name'][$i]."' WHERE id='$adresse' LIMIT 1");
                    if($tmp['vorname'][$i]!="") {
                      $this->app->DB->Update("UPDATE adresse SET typ='firma', ansprechpartner='".$tmp['vorname'][$i]." ".$tmp['name'][$i]."' WHERE id='$adresse' LIMIT 1");
                    }
                  }
                }
                if($key=="kundennrkontobuchhaltung")
                {
                  $this->app->DB->Update("UPDATE adresse SET kundennummer_buchhaltung = '".$tmp[$key][$i]."' WHERE id='$adresse' LIMIT 1");
                }
                if($key=="lieferantennrkontobuchhaltung")
                {
                  $this->app->DB->Update("UPDATE adresse SET lieferantennummer_buchhaltung = '".$tmp[$key][$i]."' WHERE id='$adresse' LIMIT 1");
                }

                switch($key)
                {
                  case "gruppe1":
                  case "gruppe2":
                  case "gruppe3":
                  case "gruppe4":
                  case "gruppe5":
                    if(trim($tmp[$key][$i]) != '')
                    {
                      $gr = $this->app->DB->Select("SELECT id FROM gruppen WHERE kennziffer = '".$this->app->DB->real_escape_string(trim($tmp[$key][$i]))."' LIMIT 1");
                      if(!$gr) {
                        $gr = $this->app->DB->Select("SELECT id FROM gruppen WHERE name = '".$this->app->DB->real_escape_string(trim($tmp[$key][$i]))."' LIMIT 1");
                      }
                      if($gr)
                      {
                        $check = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE adresse = '$adresse' AND objekt like 'Gruppe' AND parameter = '$gr' AND (bis = '0000-00-00' OR isnull(bis) OR bis >= curdate()) LIMIT 1");
                        if(!$check)
                        {
                          $projekt = $projektid;
                          if(!$projekt) {
                            $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id = '$adresse' LIMIT 1");
                          }
                          $this->app->DB->Insert("INSERT INTO adresse_rolle (adresse, projekt, subjekt, praedikat, objekt, parameter, von, bis) VALUES ('$adresse','$projekt','Mitglied','von','Gruppe','$gr',curdate(),'0000-00-00')");
                        }
                      }

                    }
                  break;
                }

                if($key==="liefersperre")
                {
                  if(isset($tmp['liefersperre'][$i]))
                  {
                    if($tmp['liefersperre'][$i] === "1" || strtolower($tmp['liefersperre'][$i]) === 'x'|| substr(strtolower($tmp['liefersperre'][$i]),0,1) === 'j')
                    {
                      $tmp['liefersperre'][$i] = 1;
                      $this->app->DB->Update("UPDATE adresse SET liefersperre='1' WHERE id='$adresse' LIMIT 1");
                    }
                    elseif($tmp['liefersperre'][$i] === "0" || strtolower($tmp['liefersperre'][$i]) === ''|| substr(strtolower($tmp['liefersperre'][$i]),0,1) === 'n')
                    {
                      $tmp['liefersperre'][$i] = 0;
                      $this->app->DB->Update("UPDATE adresse SET liefersperre='0' WHERE id='$adresse' LIMIT 1");
                    }
                  }
                }


                if($key=="plz_ort")
                {
                  $tmp['plz'][$i] = strstr($felder['plz_ort'], ' ', true);
                  $tmp['ort'][$i] = strstr($felder['plz_ort'], ' ');
                  $this->app->DB->Update("UPDATE adresse SET plz='".$tmp['plz'][$i]."', ort='".$tmp['ort'][$i]."' WHERE id='$adresse' LIMIT 1");
                }

                if($key=="strasse" || $key=="hausnummer")
                {
                  if($tmp['hausnummer'][$i]!="")
                    $tmp['strasse'][$i] = $tmp['strasse'][$i]." ".$tmp['hausnummer'][$i];
                }

                if($key=="strasse_hausnummer")
                {
                  $this->app->DB->Update("UPDATE adresse SET strasse='".$this->app->DB->real_escape_string($tmp['strasse_hausnummer'][$i])."' WHERE id='$adresse' LIMIT 1");
                }

                if($key=="geburtstag")
                {
                  if(strpos($tmp[$value][$i],'.')!==false) {
                    $tmp[$value][$i] = $this->app->String->Convert($tmp[$value][$i],"%1.%2.%3","%3-%2-%1");
                  }
                }

                if($key=="mandatsreferenzdatum")
                {
                  if(strpos($tmp[$value][$i],'.')!==false) {
                    $tmp[$value][$i] = $this->app->String->Convert($tmp[$value][$i],'%1.%2.%3','%3-%2-%1');
                  }
                }

                if($key==='liefersperredatum')
                {
                  if(strpos($tmp[$value][$i],'.')!==false) {
                    $tmp[$value][$i] = $this->app->String->Convert($tmp[$value][$i],'%1.%2.%3','%3-%2-%1');
                  }
                }

                $fields_tmp = " ".$v['field']."='".$this->app->DB->real_escape_string($tmp[$value][$i])."' ";
                $this->app->DB->Update("UPDATE adresse SET $fields_tmp WHERE id='$adresse' LIMIT 1");

                if(strpos($key, "ansprechpartner") === 0 && strlen($key) > strlen("ansprechpartner")){
                  $ansprechpartnerarray[$key] = $felder[$key];
                }
              }

              for ($nspri=1; $nspri <= 3 ; $nspri++) {
                unset($data);
                if(!empty($ansprechpartnerarray['ansprechpartner'.$nspri.'name'])){
                  $data['name']=$felder['ansprechpartner'.$nspri.'name'];
                  $data['typ']=$this->TypErkennung($felder['ansprechpartner'.$nspri.'typ']);
                  $data['strasse']=$felder['ansprechpartner'.$nspri.'strasse'];
                  $data['sprache']=$felder['ansprechpartner'.$nspri.'sprache'];
                  $data['bereich']=$felder['ansprechpartner'.$nspri.'bereich'];
                  $data['abteilung']=$felder['ansprechpartner'.$nspri.'abteilung'];
                  $data['unterabteilung']=$felder['ansprechpartner'.$nspri.'unterabteilung'];
                  $data['land']=$felder['ansprechpartner'.$nspri.'land'];
                  $data['ort']=$felder['ansprechpartner'.$nspri.'ort'];
                  $data['plz']=$felder['ansprechpartner'.$nspri.'plz'];
                  $data['telefon']=$felder['ansprechpartner'.$nspri.'telefon'];
                  $data['telefax']=$felder['ansprechpartner'.$nspri.'telefax'];
                  $data['mobil']=$felder['ansprechpartner'.$nspri.'mobil'];
                  $data['email']=$felder['ansprechpartner'.$nspri.'email'];
                  $data['sonstiges']=$felder['ansprechpartner'.$nspri.'sonstiges'];
                  $data['adresszusatz']=$felder['ansprechpartner'.$nspri.'adresszusatz'];
                  $data['ansprechpartner_land']=$felder['ansprechpartner'.$nspri.'ansprechpartner_land'];
                  $data['anschreiben']=$felder['ansprechpartner'.$nspri.'anschreiben'];
                  $data['titel']=$felder['ansprechpartner'.$nspri.'titel'];
                  $data['marketingsperre']=$felder['ansprechpartner'.$nspri.'marketingsperre'];
                  $vorhanden = !empty($this->app->DB->Select("SELECT id FROM ansprechpartner WHERE adresse='$adresse'
                    AND name='".$data['name']."' AND strasse='".$data['strasse']."' AND ort='".$data['ort']."'
                    AND plz='".$data['plz']."' AND email='".$data['email']."' LIMIT 1"));

                  if(!$vorhanden){
                    $this->app->erp->CreateAnsprechpartner($adresse, $data);
                  }
                }

                if(!empty($felder['lieferadresse'.$nspri.'name']))
                {
                  unset($data);
                  $data['name']=$felder['lieferadresse'.$nspri.'name'];
                  $data['typ']=$this->TypErkennung($felder['lieferadresse'.$nspri.'typ']);
                  $data['strasse']=$felder['lieferadresse'.$nspri.'strasse'];
                  $data['abteilung']=$felder['lieferadresse'.$nspri.'abteilung'];
                  $data['unterabteilung']=$felder['lieferadresse'.$nspri.'unterabteilung'];
                  $data['land']=$felder['lieferadresse'.$nspri.'land'];
                  $data['ort']=$felder['lieferadresse'.$nspri.'ort'];
                  $data['plz']=$felder['lieferadresse'.$nspri.'plz'];
                  $data['telefon']=$felder['lieferadresse'.$nspri.'telefon'];
                  $data['telefax']=$felder['lieferadresse'.$nspri.'telefax'];
                  $data['email']=$felder['lieferadresse'.$nspri.'email'];
                  $data['sonstiges']=$felder['lieferadresse'.$nspri.'sonstiges'];
                  $data['adresszusatz']=$felder['lieferadresse'.$nspri.'adresszusatz'];
                  $data['ansprechpartner']=$felder['lieferadresse'.$nspri.'ansprechpartner'];
                  $data['standardlieferadresse']=$felder['lieferadresse'.$nspri.'standardlieferadresse'];
                  $data['gln']=$felder['lieferadresse'.$nspri.'gln'];
                  $data['ustid']=$felder['lieferadresse'.$nspri.'ustid'];
                  $ust_befreit = (string)$felder['lieferadresse'.$nspri.'ust_befreit'];
                  switch (strtolower($ust_befreit)){
                    case '0':
                    case 'inland':
                      $ust_befreit = 0;
                      break;
                    case '1':
                    case 'eu-lieferung':
                      $ust_befreit = 1;
                      break;
                    case '2':
                    case 'export':
                      $ust_befreit = 2;
                      break;
                    case '3':
                    case 'steuerfrei inland':
                      $ust_befreit = 3;
                      break;
                  }
                  if(!empty($ust_befreit)){
                    $data['ust_befreit']=$ust_befreit;
                  }
                  $vorhanden = !empty($this->app->DB->Select("SELECT id FROM lieferadressen WHERE adresse='$adresse'
                    AND name='".$data['name']."' AND ort='".$data['ort']."' AND plz='".$data['plz']."'
                    AND strasse='".$data['strasse']."' AND adresszusatz='".$data['adresszusatz']."' AND email='".$data['email']."'"));
                  if(!$vorhanden){
                    $this->app->erp->CreateLieferadresse($adresse,$data);
                  }
                }
              }
            }
          }
          break;
          case 'kontorahmen':

            // Create a row dataset (without checked and cmd)
            $update_sql = "";
            $row = array();
            $comma = "";
            foreach ($tmp as $key => $value) {
                if ($key != 'cmd' && $key != 'checked') {
                    $row[$key] = $value[$i];
                    $comma = ", ";
                }
            }

            if (empty($row['sachkonto'])) {
                break;
            }


            $art_array = array(
                '1' => 'Aufwendungen',
                '2' => 'Erlöse',
                '3' => 'Geldtransit',
                '9' => 'Saldo'
            );

            $row['art'] = array_search($row['art'], $art_array);
            $row['projekt'] = $this->app->erp->ReplaceProjekt(true,$row['projekt'],true); // Parameters: Target db?, value, from form?

            $sql = "SELECT * FROM kontorahmen WHERE sachkonto = '".$row['sachkonto']."'";
            $result = $this->app->DB->SelectArr($sql);

            if (!empty($result)) {

                $comma = "";
                foreach ($row as $key => $value) {
                    $update_sql .= $comma."`".$key."` = '".$value."'";
                    $comma = ", ";
                }

                $sql = "UPDATE kontorahmen SET ".$update_sql." WHERE `sachkonto` = '".$row['sachkonto']."'";
                $result = $this->app->DB->Update($sql);
            } else {
                $sql = "INSERT INTO kontorahmen (".
                        implode(", ",array_keys($row)).
                        ") VALUES ('".
                        implode("', '",array_values($row)).
                        "')";
                $result = $this->app->DB->Update($sql);
            }

          break;
          case 'kontoauszug':

            $allowed_fields = array('konto','buchung','betrag','betrag2','waehrung','buchungstext','buchungstext2','buchungstext3','buchungstext4');

            $error = false;
            // Create a row dataset (without checked and cmd)
            $update_sql = "";
            $row = array();
            $comma = "";
            foreach ($tmp as $key => $value) {
                if ($key != 'cmd' && $key != 'checked') {
                    if (in_array($key,$allowed_fields)) {
                        $row[$key] = $value[$i];
                        $comma = ", ";
                    } else {
                        $importvorlagedoresult['messages'][] = "Feld nicht korrekt: ".$key;
                        $importvorlagedoresult['success'] = false;
                        $error = true;
                    }
                }
            }

            if (!$error) {
                // Collect texts
                $buchungstext = "";
                $comma = "";
                foreach ($row as $key => $value) {
                    if (str_starts_with($key,'buchungstext')) {
                        if (!empty($value)) {
                            $buchungstext .= $comma.$value;
                            $comma = ", ";
                        }
                        unset($row[$key]);
                    }
                }
                $row['buchungstext'] = $buchungstext;

                $row['soll'] = $row['betrag'];
                unset($row['betrag']);

                if (empty($row['soll'])) {
                    $row['soll'] = $row['betrag2'];
                }
                unset($row['betrag2']);

                $row['soll'] = $this->app->erp->ReplaceBetrag(true,$row['soll']);

                $row['buchung'] = $this->app->erp->ReplaceDatum(true,$row['buchung'],false);

                // Sanitize input
                foreach ($row as $key => $value) {
                    $row[$key] = $this->app->DB->real_escape_string($value);
                }

                // Calculate hash
                $hash_fields = array('buchung','soll','waehrung','buchungstext');
                $hash_text = "";

                foreach($hash_fields as $hash_field) {
                    $hash_text .= $row[$hash_field];
                }
                $row['pruefsumme'] = md5($hash_text);

                $sql = "SELECT id FROM konten WHERE kurzbezeichnung ='".$row['konto']."' LIMIT 1";
                $kontoid = $this->app->DB->SelectArr($sql);

                if (!empty($kontoid)) {

                    $row['konto'] = $kontoid[0]['id'];
                    $row['importdatum'] = date("Y-m-d H:i:s");

                    $sql = "SELECT pruefsumme FROM kontoauszuege WHERE pruefsumme='".$row['pruefsumme']."' AND konto ='".$row['konto']."' AND importfehler IS NULL";
                    $result = $this->app->DB->SelectArr($sql);

                    if (!empty($result)) {
                        $errormessage .= "Doppelter Eintrag (nicht importiert): ".$row['buchungstext']."<br>";
                    } else {
                        $sql = "INSERT INTO kontoauszuege (".
                                implode(", ",array_keys($row)).
                                ") VALUES ('".
                                implode("', '",array_values($row)).
                                "')";

                        $result = $this->app->DB->Update($sql);
                    }
                } else {
                    $errormessage .= "Konto nicht gefunden: ".$row['konto'].".<br>";
                }
            }

          break;
          case 'stueckliste':

            $allowed_fields = array('stuecklistevonartikel','artikel','menge','art','referenz','layer','wert','bauform','xpos','ypos','zachse','place','rotation');

            // Create a row dataset (without checked and cmd)
            $row = array();
            $error_text = "";

            $error = $this->create_row_set($tmp, $i, $allowed_fields, $row, $error_text);

            if ($error !== false) {
                $sql = "SELECT id FROM artikel WHERE stueckliste = 1 AND nummer = '".$row['stuecklistevonartikel']."'";
                $von_id = $this->app->DB->SelectArr($sql);
                if (empty($von_id)) {
                    $errormessage .= "Fehlerhafter 'Stueckliste von'-Artikel \"".$row['stuecklistevonartikel']."\"<br>";
                    break;
                }
                $row['stuecklistevonartikel'] = $von_id[0]['id'];

                $sql = "SELECT id FROM artikel WHERE nummer = '".$row['artikel']."'";
                $artikel_id = $this->app->DB->SelectArr($sql);
                if (empty($artikel_id)) {
                    $errormessage .= "Fehlerhafter Artikel \"".$row['artikel']."\"<br>";
                    break;
                }
                $row['artikel'] = $artikel_id[0]['id'];

                if(empty($row['menge'])) {
                    $row['menge'] = 1;
                }
                if(empty($row['art'])) {
                    $row['art'] = 'et';
                }

                if(empty($row['place']) || $row['place'] == 'DNP') {
                    $row['place'] = 'DNP';
                } else {
                    $row['place'] = 'DP';
                }

                $sql = "INSERT INTO stueckliste (".
                                implode(", ",array_keys($row)).
                                ") VALUES ('".
                                implode("', '",array_values($row)).
                                "')";

                $result = $this->app->DB->Update($sql);

                } else if(!$first_checked) {
                    $first_checked = true;
                    $importvorlagedoresult['messages'][] = $error_text;
                    $importvorlagedoresult['successs'] = false;
                }

            break;
            case 'dateien':

                // Create a row dataset (without checked and cmd)
                $row = array();
                $error_text = "";
                $allowed_fields = array('dateiaktion', 'quellpfad', 'objekt', 'objektsuchfeld', 'objektnummer', 'stichwort', 'dateiname', 'titel', 'beschreibung', 'sprache', 'dms-objekt', 'dms-objektsuchfeld', 'dms-objektnummer', 'dms-stichwort', 'dms-dateiname');
                $error = $this->create_row_set($tmp, $i, $allowed_fields, $row, $error_text);
                $dateien_result = $this->PrepareDateien($tmp, $i, $allowed_fields, $global_data);
                $dateiname = basename($dateien_result['result_row']['additional_file']['path']);
                if ($error !== false) {
                    switch ($row['dateiaktion']) {
                        case 'zip':
                            $fileid = $this->app->erp->CreateDateiWithStichwort(
                                        name: empty($row['dateiname'])?$dateiname:$row['dateiname'],
                                        titel: $row['titel'],
                                        beschreibung: $row['bescheibung'],
                                        nummer: '',
                                        datei: $dateien_result['result_row']['additional_file']['path'],
                                        ersteller: $this->app->User->GetName(),
                                        subjekt: $row['stichwort'],
                                        objekt: $dateien_result['result_row']['datei_objekt']['wert'],
                                        parameter: $dateien_result['result_row']['datei_objekt']['id']
                                );
                            if (empty($fileid)) {
                                $importvorlagedoresult['messages'][] = "Datei wurde nicht angelegt: ".$dateiname;
                                $importvorlagedoresult['success'] = false;
                            } else {
                                $additional_files[$key]['imported'] = true;
                            }
                        break;
                        case 'url':
                            $importvorlagedoresult['messages'][] = 'Dateiaktion URL nicht implementiert';
                            $importvorlagedoresult['successs'] = false;
                        break;
                        case 'aendern':
                            foreach ($dateien_result['result_row']['dms_dateien'] as $datei_id) {
                                if (!empty($row['stichwort'])) {
                                    $this->app->erp->AddDateiStichwort($datei_id,$row['stichwort'],$dateien_result['result_row']['datei_objekt']['wert'],$dateien_result['result_row']['datei_objekt']['id']);
                                }
                                $this->app->erp->ModifyDateiMetadata($datei_id, $row['dateiname'], $row['titel'], $row['beschreibung']);
                            }
                        break;
                        case 'entfernen':
                            $importvorlagedoresult['messages'][] = 'Dateiaktion entfernen nicht implementiert';
                        break;
                        default:
                            $importvorlagedoresult['messages'][] = 'Aktion nicht implementiert '.$row['aktion'];
                        break;
                    } // switch
                } else if(!$first_checked) {
                    $first_checked = true;
                    $importvorlagedoresult['messages'][] = $error_text;
                    $importvorlagedoresult['successs'] = false;
                }
            break;
        }

        // HERE END OF PROCESSING THE ROWS switch($ziel);

        if($isCronjob) {
          $this->app->DB->Update(
            sprintf(
              "UPDATE `prozessstarter`
              SET `mutexcounter` = 0, `mutex` = 1
              WHERE `parameter` = 'importvorlage' AND `aktiv` = 1"
            )
          );
          $this->app->DB->Update(
            sprintf(
              'UPDATE `importmasterdata` SET `imported_rows` = %d WHERE `id` = %d AND `count_rows` >= %d',
              $i, $parameter['importmasterdata_id'], $i
            )
          );
          $importMasterData = $this->app->DB->SelectRow(
            sprintf(
              'SELECT `status` FROM `importmasterdata` WHERE `id` = %d',
              $parameter['importmasterdata_id']
            )
          );
          if(empty($importMasterData) || $importMasterData['status'] === 'cancelled') {
            break;
          }
        }
      } // Loop

      $importvorlagedoresult['messages'] = array_unique($importvorlagedoresult['messages']);
      $importvorlagedoresult['message'] = implode('<br>',$importvorlagedoresult['messages']);
      unset($importvorlagedoresult['messages']);
      $importvorlagedoresult['rows'] = $number_of_rows;
      return ($importvorlagedoresult);
  }
// END ImportvorlageDo()

  /**
   * @param string $name
   *
   * @return int|null
   */
  public function getArticlePropertyIdByName(string $name): ?int
  {
    $name = $this->app->DB->real_escape_string($name);
    $propertyId = $this->app->DB->Select(
      "SELECT `id`
      FROM `artikeleigenschaften`
      WHERE `geloescht` <> 1 AND `name` = '{$name}'
      LIMIT 1"
    );
    if($propertyId > 0) {
      return (int)$propertyId;
    }

    return null;
  }

  /**
   * @param int    $articleId
   * @param int    $propertyId
   * @param string $propertyValue
   *
   * @return int|null
   */
  public function getPropertyValueIdByName(int $articleId, int $propertyId, string $propertyValue): ?int
  {
    $propertyValue = $this->app->DB->real_escape_string($propertyValue);
    $propertyValueId = $this->app->DB->Select(
      "SELECT `id`
      FROM `artikeleigenschaftenwerte`
      WHERE `artikel` = {$articleId} AND `artikeleigenschaften` = {$propertyId} AND `wert` = '{$propertyValue}'
      LIMIT 1"
    );
    if($propertyValueId > 0) {
      return (int)$propertyValueId;
    }

    return null;
  }

  /**
   * @param int $articleId
   * @param int $propertyId
   *
   * @return array|null
   */
  public function getFirstPropertyValueByPropertyId(int $articleId, int $propertyId): ?array
  {
    return $this->app->DB->SelectRow(
      "SELECT *
      FROM `artikeleigenschaftenwerte`
      WHERE `artikel` = {$articleId} AND `artikeleigenschaften` = {$propertyId}
      LIMIT 1"
    );
  }

  /**
   * @param string $name
   *
   * @return int
   */
  public function createArticleProperty(string $name): int
  {
    $name = $this->app->DB->real_escape_string($name);
    $this->app->DB->Insert("INSERT INTO `artikeleigenschaften` (`name`) VALUES ('{$name}')");
    return (int)$this->app->DB->GetInsertID();
  }

  /**
   * @param int    $articleId
   * @param int    $propertyId
   * @param string $propertyValue
   *
   * @return int
   */
  public function createArticlePropertyValue(int $articleId, int $propertyId, string $propertyValue): int
  {
    $propertyValue = $this->app->DB->real_escape_string($propertyValue);
    $this->app->DB->Insert(
      "INSERT INTO `artikeleigenschaftenwerte`
        (`artikel`, `artikeleigenschaften`, `wert`) VALUES
       ({$articleId}, {$propertyId}, '{$propertyValue}')"
    );

    return (int)$this->app->DB->GetInsertID();
  }

  /**
   * @param int    $propertyValueId
   * @param string $propertyValue
   */
  public function updateArticlePropertyValue(int $propertyValueId, string $propertyValue): void
  {
    $propertyValue = $this->app->DB->real_escape_string($propertyValue);
    $this->app->DB->Update(
      "UPDATE `artikeleigenschaftenwerte` SET `wert` = '{$propertyValue}'
      WHERE `id` = {$propertyValueId}"
    );
  }

  /**
   * @param int $propertyValueId
   */
  public function deletePropertyValue(int $propertyValueId): void
  {
    $this->app->DB->Update(
      "DELETE FROM `artikeleigenschaftenwerte`
      WHERE `id` = {$propertyValueId}"
    );
  }

  /**
   * @return array
   */
  public function getLanguageCodesForProperties(): array
  {
    if($this->languageCodesForProperties !== null) {
      return $this->languageCodesForProperties;
    }
    $this->languageCodesForProperties = array_map('strtolower', $this->app->DB->SelectFirstCols(
      "SELECT DISTINCT `iso` FROM `sprachen` WHERE `aktiv` = 1")
    );

    return $this->languageCodesForProperties;
  }

  /**
   * @param int   $propertyIndex
   * @param int   $rowIndex
   * @param array $columnsArray
   *
   * @return array
   */
  public function getPropertyTranslationCodesByPropertyIndex(int $propertyIndex, int $rowIndex, array $columnsArray): array
  {
    $languages = $this->getLanguageCodesForProperties();
    $languageCodes = [];
    foreach ($languages as $language) {
      if(isset($columnsArray["eigenschaftwert{$propertyIndex}_{$language}"][$rowIndex])) {
        $propertyValue = (string)$columnsArray["eigenschaftwert{$propertyIndex}_{$language}"][$rowIndex];
        $propertyName = !empty($columnsArray["eigenschaftname{$propertyIndex}_{$language}"][$rowIndex]) ?
          $columnsArray["eigenschaftname{$propertyIndex}_{$language}"][$rowIndex] : null;
        if($propertyName === null) {
          $propertyName = !empty($columnsArray['eigenschaftnameeindeutig' . $propertyIndex][$rowIndex]) ?
            $columnsArray['eigenschaftname' . $propertyIndex][$rowIndex] : null;
        }
        if($propertyName === null) {
          $propertyName = !empty($columnsArray['eigenschaftname' . $propertyIndex][$rowIndex]) ?
            $columnsArray['eigenschaftname' . $propertyIndex][$rowIndex] : null;
        }
        if($propertyName === null) {
          continue;
        }
        $languageCodes[$language] = ['property_name' => $propertyName, 'property_value' => $propertyValue];
      }
    }

    return $languageCodes;
  }

  /**
   * @param int    $articleId
   * @param string $languageCode
   * @param string $propertyNameFrom
   */
  public function deletePropertyTranslations(
    int $articleId, string $languageCode, string $propertyNameFrom
  ): void
  {
    $languageCode = $this->app->DB->real_escape_string(strtoupper($languageCode));
    $propertyNameFrom = $this->app->DB->real_escape_string($propertyNameFrom);
    $this->app->DB->Delete(
      "DELETE FROM `article_property_translation`
      WHERE `article_id` = {$articleId}
      AND `property_from` = '{$propertyNameFrom}' AND `language_to` = '{$languageCode}'"
    );
  }

  /**
   * @param int    $articleId
   * @param string $languageCode
   * @param string $propertyNameFrom
   * @param string $propertyValue
   */
  public function deletePropertyTranslationByValue(
    int $articleId, string $languageCode, string $propertyNameFrom, string $propertyValue
  ): void
  {
    $languageCode = $this->app->DB->real_escape_string(strtoupper($languageCode));
    $propertyNameFrom = $this->app->DB->real_escape_string($propertyNameFrom);
    $propertyValue = $this->app->DB->real_escape_string($propertyValue);
    $this->app->DB->Delete(
      "DELETE FROM `article_property_translation`
      WHERE `article_id` = {$articleId}
      AND `property_from` = '{$propertyNameFrom}' AND `language_to` = '{$languageCode}'
        AND `property_value_from` = '{$propertyValue}'"
    );
  }

  /**
   * @param int    $articleId
   * @param string $languageTo
   * @param string $propertyNameFrom
   * @param string $propertyValueFrom
   * @param string $propertyNameTo
   * @param string $propertyValueTo
   * @param string $languageFrom
   *
   * @return int
   */
  public function createArticlePropertyTranslation(
    int $articleId,
    string $languageTo,
    string $propertyNameFrom,
    string $propertyValueFrom,
    string $propertyNameTo,
    string $propertyValueTo,
    string $languageFrom = 'DE'
  ): int
  {
    $languageFrom = $this->app->DB->real_escape_string(strtoupper($languageFrom));
    $languageTo = $this->app->DB->real_escape_string(strtoupper($languageTo));
    $propertyNameFrom = $this->app->DB->real_escape_string($propertyNameFrom);
    $propertyValueFrom = $this->app->DB->real_escape_string($propertyValueFrom);
    $propertyNameTo = $this->app->DB->real_escape_string($propertyNameTo);
    $propertyValueTo = $this->app->DB->real_escape_string($propertyValueTo);
    $this->app->DB->Insert(
      "INSERT INTO `article_property_translation`
      (`article_id`, `language_from`, `language_to`,
     `property_from`, `property_value_from`,
     `property_to`, `property_value_to`)
     VALUES ({$articleId}, '{$languageFrom}', '{$languageTo}',
             '{$propertyNameFrom}', '{$propertyValueFrom}',
             '{$propertyNameTo}', '{$propertyValueTo}'
     )"
    );

    return (int)$this->app->DB->GetInsertID();
  }

  /**
   * @param int         $propertyTranslationId
   * @param string      $propertyNameTo
   * @param string      $propertyValueTo
   * @param string|null $propertyNameFrom
   * @param string|null $propertyValueFrom
   */
  public function updatePropertyTranslation(
    int $propertyTranslationId,
    string $propertyNameTo,
    string $propertyValueTo,
    ?string $propertyNameFrom = null,
    ?string $propertyValueFrom = null
  ): void
  {
    $propertyNameTo = $this->app->DB->real_escape_string($propertyNameTo);
    $propertyValueTo = $this->app->DB->real_escape_string($propertyValueTo);
    $this->app->DB->Update(
      "UPDATE `article_property_translation`
      SET `property_to` = '{$propertyNameTo}', `property_value_to` = '{$propertyValueTo}'
      WHERE `id` = {$propertyTranslationId}"
    );
    if($propertyNameFrom === null) {
      return;
    }
    $propertyNameFrom = $this->app->DB->real_escape_string($propertyNameFrom);
    $propertyValueFrom = $this->app->DB->real_escape_string($propertyValueFrom);
    $this->app->DB->Update(
      "UPDATE `article_property_translation`
      SET `property_from` = '{$propertyNameFrom}', `property_value_from` = '{$propertyValueFrom}'
      WHERE `id` = {$propertyTranslationId}"
    );
  }

  /**
   * @param int    $articleId
   * @param string $languageCode
   * @param string $propertyNameFrom
   * @param string $propertyValueFrom
   *
   * @return array|null
   */
  public function getPropertyTranslation(
    int $articleId,
    string $languageCode,
    string $propertyNameFrom,
    string $propertyValueFrom
  ): ?array
  {
    $languageCode = $this->app->DB->real_escape_string(strtoupper($languageCode));
    $propertyNameFrom = $this->app->DB->real_escape_string($propertyNameFrom);
    $propertyValueFrom = $this->app->DB->real_escape_string($propertyValueFrom);
    return $this->app->DB->SelectRow(
      "SELECT *
      FROM `article_property_translation`
      WHERE `article_id` = {$articleId} AND `language_to` = '{$languageCode}'
        AND `property_from` = '{$propertyNameFrom}' AND `property_value_from` = '{$propertyValueFrom}'"
    );
  }

  /**
   * @param int         $articleId
   * @param string      $languageCode
   * @param string      $propertyNameFrom
   * @param string      $propertyValueFrom
   * @param string      $propertyNameTo
   * @param string      $propertyValueTo
   * @param string|null $oldPropertyValue
   */
  public function tryUpdatePropertyTranslation(
    int $articleId,
    string $languageCode,
    string $propertyNameFrom,
    string $propertyValueFrom,
    string $propertyNameTo,
    string $propertyValueTo,
    ?string $oldPropertyValue = null
  ): void
  {
    $propertyTranslation = $this->getPropertyTranslation(
      $articleId, $languageCode, $propertyNameFrom, $propertyValueFrom
    );
    if($propertyTranslation === null && $oldPropertyValue !== null) {
      $propertyTranslation = $this->getPropertyTranslation(
        $articleId, $languageCode, $propertyNameFrom, $oldPropertyValue
      );
    }
    if($propertyTranslation === null) {
      $this->createArticlePropertyTranslation(
        $articleId, $languageCode, $propertyNameFrom, $propertyValueFrom, $propertyNameTo, $propertyValueTo
      );
      return;
    }
    $this->updatePropertyTranslation(
      (int)$propertyTranslation['id'], $propertyNameTo, $propertyValueTo, $propertyNameFrom, $propertyValueFrom
    );
  }

  /**
   * @param int    $articleId
   * @param int    $propertyIndex
   * @param int    $rowIndex
   * @param array  $columnsArray
   * @param string $propertyNameFrom
   * @param string $propertyValueFrom
   */
  public function createTranslationByPropertyNameAndValue(
    int $articleId,
    int $propertyIndex,
    int $rowIndex,
    array $columnsArray,
    string $propertyNameFrom,
    string $propertyValueFrom
  ): void
  {
    foreach ($this->getPropertyTranslationCodesByPropertyIndex($propertyIndex, $rowIndex, $columnsArray)
             as $languageCode => $property
    ) {
      $propertyNameTo = $property['property_name'];
      $propertyValueTo = $property['property_value'];
      $this->createArticlePropertyTranslation(
        $articleId, $languageCode, $propertyNameFrom, $propertyValueFrom,
        $propertyNameTo,
        $propertyValueTo
      );
    }
  }

  /**
   * @param int   $articleId
   * @param int   $propertyIndex
   * @param int   $rowIndex
   * @param array $columnsArray
   */
  public function updateUniquePropertyNameByIndex(
    int $articleId, int $propertyIndex, int $rowIndex, array $columnsArray
  ): void
  {
  }

  /**
   * @param int   $articleId
   * @param       $propertyIndex
   * @param int   $rowIndex
   * @param array $columnsArray
   */
  public function updateArticlePropertyByIndex(
    int $articleId, int $propertyIndex, int $rowIndex, array $columnsArray
  ): void
  {
  }


  /**
   * @param int   $articleId
   * @param int   $rowIndex
   * @param array $columnsArray
   */
  public function updateArticleProperties(int $articleId, int $rowIndex, array $columnsArray): void
  {
  }

  /**
   * @param int    $ekid
   * @param array  $data
   * @param string $prefix
   */
  function UpdateEinkaufspreiseExtraWerte($ekid,$data,$prefix='')
  {
    if($ekid <=0) {
      return;
    }

    $datum_werte = array('rahmenvertrag_von','rahmenvertrag_bis','preis_anfrage_vom','gueltig_bis','datum_lagerlieferant');

    $zahlen_werte = array('sicherheitslager','rahmenvertrag_menge','lieferzeit_aktuell','lieferzeit_standard','lager_lieferant');

    $text_werte = array('rahmenvertrag','bemerkung','nichtberechnet');

    foreach($datum_werte as $key) {
      $keyi = 'lieferant'.$key.$prefix;
      if(isset($data[$keyi][1])) {
        if(strpos($data[$keyi][1],'.')!==false){
          $data[$keyi][1] = $this->app->String->Convert($data[$keyi][1], '%1.%2.%3', '%3-%2-%1');
        }
        $updatevalue[] ="$key='".$data[$keyi][1]."'";
      }
    }

    foreach($zahlen_werte as $key) {
      $keyi = 'lieferant'.$key.$prefix;
      if(isset($data[$keyi][1])) {
        $data[$keyi][1] = str_replace(',','.',$data[$keyi][1]);
        $updatevalue[] ="$key='".$data[$keyi][1]."'";
      }
    }

    foreach($text_werte as $key) {
      $keyi = 'lieferant'.$key.$prefix;
      if(isset($data[$keyi][1])) {
        $updatevalue[] ="$key='".$data[$keyi][1]."'";
      }
    }

    if(is_array($updatevalue)) {
      $updatesql = implode(',',$updatevalue);
      $this->app->DB->Update("UPDATE einkaufspreise SET $updatesql WHERE id='$ekid' LIMIT 1");
    }
  }

  /**
   * @param string $value
   * @param string $bedingung
   *
   * @return string
   */
  function ImportvorlageBedingung($value, $bedingung)
  {
    if(strpos($bedingung,'?') === false || strpos($bedingung,':') === false) {
      return $value;
    }

    $beda = explode('?',$bedingung,2);
    $beda[1] = trim(trim($beda[1]),'"');
    $beda[1] = str_replace('%value%',$value,$beda[1]);

    $beda2 = explode(':',$beda[1],2);
    if(isset($beda2[1])) {
      $beda[1] = $beda2[0];
      $beda2[1] = trim(trim($beda2[1]),'"');
      $beda2[1] = str_replace('%value%',$value,$beda2[1]);
      if(strpos($beda[0],'>=') !== false) {
        $beda3 = explode('>=',$beda[0],2);
        $beda3[1] = trim(trim($beda3[1]),'"');
        if(trim(trim($value),'"') >= trim(trim($beda3[1]),'"')) {
          return $beda[1];
        }
        return $beda2[1];
      }
      if(strpos($beda[0],'<=') !== false) {
        $beda3 = explode('<=',$beda[0],2);
        $beda3[1] = trim(trim($beda3[1]),'"');
        if(trim(trim($value),'"') <= trim(trim($beda3[1]),'"')) {
          return $beda[1];
        }
        return $beda2[1];
      }
      if(strpos($beda[0],'>') !== false) {
        $beda3 = explode('>',$beda[0],2);
        $beda3[1] = trim(trim($beda3[1]),'"');
        if(trim(trim($value),'"') > trim(trim($beda3[1]),'"')) {
          return $beda[1];
        }
        return $beda2[1];
      }
      if(strpos($beda[0],'<') !== false) {
        $beda3 = explode('<',$beda[0],2);
        $beda3[1] = trim(trim($beda3[1]),'"');
        if(trim(trim($value),'"') < trim(trim($beda3[1]),'"')) {
          return $beda[1];
        }
        return $beda2[1];
      }
      if(strpos($beda[0],'!=') !== false) {
        $beda3 = explode('!=',$beda[0],2);
        $beda3[1] = trim(trim($beda3[1]),'"');
        if(trim(trim($value),'"') != trim(trim($beda3[1]),'"')) {
          return $beda[1];
        }
        return $beda2[1];
      }
      if(strpos($beda[0],'==') !== false) {
        $beda3 = explode('==',$beda[0],2);
        $beda3[1] = trim(trim($beda3[1]),'"');
        if(trim(trim($value),'"') == trim(trim($beda3[1]),'"')) {
          return $beda[1];
        }

        return $beda2[1];
      }
      if(strpos($beda[0],'=') !== false) {
        $beda3 = explode('=',$beda[0],2);
        $beda3[1] = trim(trim($beda3[1]),'"');
        if(trim(trim($value),'"') == trim(trim($beda3[1]),'"')) {
          return $beda[1];
        }
        return $beda2[1];
      }
    }

    return $value;
  }

  /**
   * @param string $ziel
   * @param array  $fieldset
   */
  function ImportPrepareHeader($ziel,$fieldset)
  {
    $number_of_fields =(!empty($fieldset)?count($fieldset):0);

    switch($ziel)
    {
      case "einkauf":
      case "artikel":
        $this->app->Tpl->Add('ERGEBNIS','<tr><td width="100"><b>Auswahl</b></td><td width="100"><b>Aktion</b></td><td><b>Artikel</b></td>');
        break;
      case "adresse":
        $this->app->Tpl->Add('ERGEBNIS','<tr><td width="100"><b>Auswahl</b></td><td width="100"><b>Aktion</b></td><td><b>Adresse</b></td>');
        break;

      case "zeiterfassung":
      case "wiedervorlagen":
      case "notizen":
        $this->app->Tpl->Add('ERGEBNIS','<tr><td width="100"><b>Auswahl</b></td>
            <td width="100"><b>Aktion</b></td><td><b>Kunde</b></td>');
        break;
      case "provisionenartikel":
        $this->app->Tpl->Add('ERGEBNIS', '<tr><td width="100"></td><td width="100"><b>Aktion</b></td><td><b></b></td>');
        break;
      default:
        $this->app->Tpl->Add('ERGEBNIS','<tr><td width="100"><b>Auswahl</b></td>
            <td width="100"><b>Aktion</b></td><td><b></b></td>');
      break;
    }

    for($j=0;$j<$number_of_fields;$j++) {
      if(isset($fieldset[$j]) && isset($fieldset[$j]['field'])) {
        $this->app->Tpl->Add('ERGEBNIS','<td><b>'.$fieldset[$j]['field'].'</b></td>');
      }
    }

    $this->app->Tpl->Add('ERGEBNIS','</tr>');
  }

  /**
   * @param int    $rowcounter
   * @param string $ziel
   * @param array  $data
   * @param array  $fieldset
   */
  function ImportPrepareRow($rowcounter,$ziel,$data,$fieldset, &$create_count, &$update_count, &$prepare_result, $global_data)
  {
    $number_of_fields =(!empty($fieldset)?count($fieldset):0);
    //Standard
    $fields['waehrung'] = 'EUR';
    $result_row = array();

    $herstellernummermehrfachvergeben = false;
    $output = '';
    for($j=0;$j<$number_of_fields;$j++) {
      $value = '';
      $bedingung = false;
      if(isset($fieldset[$j]['bedingung'])) {
        $bedingung = $fieldset[$j]['bedingung'];
      }
      if(isset($fieldset[$j]['nr'])) {
        $value = trim($data[$fieldset[$j]['nr'] - 1]);
        if(isset($fieldset[$j]['inv'])) {
          if($value != '1') {
            $value = 1;
          }
          else{
            $value = 0;
          }
        }
      }
      elseif(isset($fieldset[$j]['vorlage'])) {
        $value = $fieldset[$j]['vorlage'];
      }
      if(isset($fieldset[$j]['bedingung'])) {
        $value = $this->ImportvorlageBedingung($value, $fieldset[$j]['bedingung']);
      }
      $fieldname = $fieldset[$j]['field'];
      switch($fieldname) {
        case "herstellernummer":
          $fields['herstellernummer'] = $value;
          if($value != ''){
            $nummervonhersteller = $this->app->DB->Select("SELECT nummer
                FROM artikel WHERE herstellernummer='".$this->app->DB->real_escape_string($value)."' AND herstellernummer <> '' AND geloescht <> 1");
            if(!is_array($nummervonhersteller)){
              if($nummervonhersteller > 0){
                $fields['nummer'] = $nummervonhersteller;
                $nummer = $nummervonhersteller;
              }
            }else{
              $herstellernummermehrfachvergeben = true;
            }

          }
          //if($fields[herstellernummer]<=0) $fields[herstellernummer]="";
          break;
        case "matrixproduktvon":
          $fields['matrixproduktvon'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE nummer='".$this->app->DB->real_escape_string($value)."' AND geloescht <> 1 LIMIT 1");
          break;
        case "matrixproduktgruppe1":
          $fields['matrixproduktgruppe1'] = $value;
        break;
        case "nummer":
          $fields['nummer'] = trim($value);

          $fields['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE nummer='".$this->app->DB->real_escape_string($fields['nummer'])."' LIMIT 1");
          $nummer = $fields['nummer'];
          foreach($fieldset as $k => $v) {
            $bedingung = '';
            if(isset($fieldset[$k]['bedingung'])) {
              $bedingung = $fieldset[$k]['bedingung'];
            }
            if(trim(strtolower($bedingung)) !== 'unique') {
              continue;
            }
            if(empty($v['field']) || empty($fields[$v['field']])) {
              continue;
            }

            $adressid = $this->app->DB->Select(
              "SELECT id
              FROM artikel
              WHERE ".$v['field']."='".$this->app->DB->real_escape_string($fields[$v['field']])."'
              LIMIT 1"
            );
            if($adressid) {
              if(isset($fields['nummer']) && (strtoupper($value) === 'NEW' || strtoupper($value) === 'NEU')) {
                $fields['nummer'] = $this->app->DB->Select(
                  "SELECT nummer FROM artikel WHERE id = '".$adressid."' LIMIT 1"
                );
              }
            }
          }
          break;
        case "lieferantennummer":
          $fields['lieferantennummer'] = $value;
          $fields['lieferantennummer'] = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE lieferantennummer='".$this->app->DB->real_escape_string($fields['lieferantennummer'])."' LIMIT 1");

          foreach($fieldset as $k => $v) {
            $bedingung = '';
            if(isset($fieldset[$k]['bedingung'])) {
              $bedingung = $fieldset[$k]['bedingung'];
            }
            if(trim(strtolower($bedingung)) !== 'unique'){
              continue;
            }

            if(empty($v['field']) || empty($fields[$v['field']])) {
              continue;
            }

            $adressid = $this->app->DB->Select(
              "SELECT id
              FROM adresse
              WHERE ".$v['field']."='".$this->app->DB->real_escape_string($fields[$v['field']])."'
              LIMIT 1"
            );
            if($adressid) {
              if(isset($fields['lieferantennummer'])
                && (strtoupper($value) === 'NEW' || strtoupper($value) === 'NEU')) {
                $fields['lieferantennummer'] = $this->app->DB->Select(
                  "SELECT lieferantennummer FROM adresse WHERE id = '".$adressid."' LIMIT 1"
                );
              }
            }
          }

          $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$this->app->DB->real_escape_string($fields['lieferantennummer'])."' LIMIT 1");
          break;
        case "kundennummer":
          $fields['kundennummer'] = $value;
          $fields['kundennummer'] = $this->app->DB->Select(
            "SELECT kundennummer
            FROM adresse
            WHERE kundennummer='".$this->app->DB->real_escape_string($fields['kundennummer'])."'
            LIMIT 1"
          );
          foreach($fieldset as $k => $v) {
            $bedingung = '';
            if(isset($fieldset[$k]['bedingung'])) {
              $bedingung = $fieldset[$k]['bedingung'];
            }
            if(trim(strtolower($bedingung)) !== 'unique') {
              continue;
            }
            if(empty($v['field']) || empty($fields[$v['field']])) {
              continue;
            }

            $adressid = $this->app->DB->Select(
              "SELECT id
              FROM adresse
              WHERE ".$v['field']."='".$this->app->DB->real_escape_string($fields[$v['field']])."'
              LIMIT 1"
            );
            if($adressid) {
              if(isset($fields['kundennummer']) && (strtoupper($value) === 'NEW' || strtoupper($value) === 'NEU')) {
                $fields['kundennummer'] = $this->app->DB->Select(
                  "SELECT kundennummer FROM adresse WHERE id = '".$adressid."' LIMIT 1"
                );
              }
            }
          }
          break;
        case "ab_menge":
          $fields['ab_menge'] = $value;
          break;
        case "ean":
          $fields['ean'] = $value;
          if($value != ''){
            if(!isset($fields['nummer']) && !$fields['nummer']){
              $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE ean='$value' AND ean<>'' AND geloescht <> 1 LIMIT 1");
              if(!is_array($nummer) && $nummer) {
                $fields['nummer'] = $nummer;
              }
            }
          }
          break;
        case "waehrung":
          $fields['waehrung'] = $value;
          break;
        case "ekpreis":
          $value = str_replace('EUR','',$value);
          $value = str_replace(' ','',$value);
          if(preg_match('#^(?<integer>.*)(?<separator>[\.,])(?<decimals>[0-9]+)$#', $value, $matches)){
            /* clean integer and append decimals with your own separator */
            $number = ((int) preg_replace('#[^0-9]+#', '', $matches['integer']) . ',' . $matches['decimals']);
          }
          else{
            $number = (int) preg_replace('#[^0-9]+#', '', $input);
          }
          // $formatter = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);

          // prüfe von rechts letztes zeichen das keine 0 ist

          // let's print the international format for the en_US locale
          $value = $number;
          $fields['ekpreis'] = $value;
          break;
        case "datum_von":
          $value = $this->app->String->Convert($value,'%1.%2.%3','%3-%2-%1');
          $fields['datum_von'] = $value;
          break;
        case "datum_bis":
          $value = $this->app->String->Convert($value,'%1.%2.%3','%3-%2-%1');
          $fields['datum_bis'] = $value;
          break;
        case "kennung":
          $fields['kennung'] = $value;
          break;
        case "zeit_bis":
          $fields['zeit_bis'] = $value;
          break;
        case "zeit_von":
          $fields['zeit_von'] = $value;
          break;
        default:
          $fields[$fieldname] = $value;
      }
      $result_row[] = array('field' => $fieldname, 'value' => $value);
    } // fields

    switch($ziel)
    {
      case "einkauf":
        $checked = "checked";
        if($fields['lieferantennummer']=="")
        {
          $action_anzeige = "Lieferant fehlt";
          $action="none";
          $checked="";
        }
        else if($fields['lieferantennummer']!="" && $fields['nummer']!="")
        {
          $nummer = trim($fields['nummer']);
          $action_anzeige = "Artikelnr. gefunden";
          $action="update";
        }
        else if($fields['lieferantennummer']!="" && $fields['herstellernummer']!="")
        {
          $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE herstellernummer='".$this->app->DB->real_escape_string($fields['herstellernummer'])."' AND geloescht <>'1'");
          if(!is_array($nummer)){
            $action_anzeige = "Herstellernr. gefunden";
            $action="update";
          }else{
            $action_anzeige = "Herstellernr. mehrfach vergeben";
            $action="none";
          }
        }
        else if($fields['lieferantennummer']!="" && $fields['bestellnummer']!="")
        {
          $artikelid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE bestellnummer='".$this->app->DB->real_escape_string($fields['bestellnummer'])."'
              AND adresse='".$lieferantid."' LIMIT 1");
          $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='".$artikelid."' LIMIT 1");
          $action_anzeige = "Bestellnr. gefunden";
          $action="update";
        }


        else {
          $action_anzeige = "Artikel- oder Herstellernr. fehlt";
          $action="none";
          $checked="";
        }
        break;
      case "adresse":
        if($fields['kundennummer']=="" && $fields['lieferantennummer']=="" && $fields['name']=="" && $fields['firma']=="" )
        {
          $action_anzeige = "Kd.- und Lieferanten-Nr. name und firma fehlt";
          $action="none";
          $checked="";
        }
        else if($fields['kundennummer']=="" && ($fields['name']!="" || $fields['firma']!="") && $fields['lieferantennummer']=="")
        {
          $action="create";
          $checked="checked";
        }
        else if($fields['lieferantennummer']!="" || $fields['kundennummer']!="")
        {
          $checkkunde = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$this->app->DB->real_escape_string($fields['kundennummer'])."' AND kundennummer!='' LIMIT 1");
          if($checkkunde <= 0)
          {
            $action="create";
            $checked="checked";
          } else {
            $action_anzeige = "Kundennummer gefunden";
            $action="update";
            $checked="checked";
          }

          if($checkkunde <= 0) {
            $checklieferant = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$this->app->DB->real_escape_string($fields['lieferantennummer'])."' AND lieferantennummer!='' LIMIT 1");
            if($checklieferant <= 0)
            {
              $action="create";
              $checked="checked";
            } else {
              $action_anzeige = "Lieferantennummer gefunden";
              $action="update";
              $checked="checked";
            }
          }
        }

        break;
      case "artikel":
        if($herstellernummermehrfachvergeben){
          $action_anzeige = "Herstellernummer mehfach vergeben";
          $action="none";
          $checked="";
        }
        elseif((String)$fields['ean']==="" && (String)$fields['nummer']==="" && (String)$fields['name_de']==="" && ((String)$fields['matrixproduktvon'] === "" || (String)$fields['matrixproduktgruppe1'] === ""))
        {
          $action_anzeige = "Artikelnummer, name_de und EAN fehlt";
          $action="none";
          $checked="";
        }
        elseif((String)$fields['nummer']==="" && $fields['name_de']!="")
        {
          $action="create";
          $checked="checked";
        }
        elseif($fields['nummer']!="")
        {
          $action="update";
          $checked="checked";
        }
        elseif($fields['matrixproduktvon'] != "" && $fields['matrixproduktgruppe1'] != "")
        {
          $action="create";
          $checked="checked";
        }
        elseif(!$fields['nummer'] && $fields['ean']!="")
        {
          $action_anzeige = "Kein Artikel zu EAN gefunden";
          $action="none";
          $checked="";
        }
        break;
      case "zeiterfassung":
      case "wiedervorlagen":
      case "notizen":
        $checked = "checked";
        $nummer='';
        if($fields['kundennummer']!=''){
          $nummer = $this->app->DB->Select(
            "SELECT kundennummer FROM adresse WHERE kundennummer='" . $fields['kundennummer'] . "' LIMIT 1"
          );
        }

        if($nummer==''){
          $action_anzeige = "Kundennummer fehlt";
          $action="none";
          $checked="";
        }
        else {
          $action="create";
        }
        break;
      case "provisionenartikel":
        $action_anzeige = "";
        $action="create";
        $checked = "";
        break;
        case 'dateien':
        
            $action_anzeige = '';
            $allowed_fields = array('dateiaktion', 'quellpfad', 'objekt', 'objektsuchfeld', 'objektnummer', 'stichwort', 'dateiname', 'titel', 'beschreibung', 'sprache', 'dms-objekt', 'dms-objektsuchfeld', 'dms-objektnummer', 'dms-stichwort', 'dms-dateiname');
            // Create a row dataset (without checked and cmd)
            foreach($fields as $key => $value) {
                $checkfields[$key][0] = $value;            
            }
            unset($checkfields['waehrung']);
            $row = array();
            $error_text = "";
            $error = $this->create_row_set($checkfields, 0, $allowed_fields, $row, $error_text);
            if ($error !== true) {
                $action_anzeige .= $error_text;
                break;
            }

            $dateien_result = $this->PrepareDateien($checkfields, 0, $allowed_fields, $global_data);
            $nummer = $dateien_result['nummer'];
            $action = $dateien_result['action'];
            $action_anzeige = $dateien_result['action_anzeige'];
            $result_row[] = $dateien_result['result_row'];
        
        break;
    }

    switch ($action) {
        case 'create':
            $create_count++;
        break;
        case 'update':
            $update_count++;
        break;
        default:
            $action = 'none';
        break;
    }

    $prepare_result[] = array(
                    'row' => $rowcounter,
                    'nummer' => $nummer,
                    'action' => $action,
                    'action_anzeige' => $action_anzeige,
                    'values' => $result_row
    );
  }

  /**
   * @param string $umsatzsteuer_lieferant
   *
   * @return string
   */
  function UstErkennungLieferant($umsatzsteuer_lieferant){
    $umsatzsteuer_lieferant = strtolower($umsatzsteuer_lieferant);
    switch ($umsatzsteuer_lieferant){
      case 'eulieferung':
      case 'eu-lieferung':
        return 'eulieferung';
        break;
      case 'import':
        return 'import';
        break;
    }

    return 'inland';
  }

  /**
   * @param string $ust_befreit
   *
   * @return int
   */
  function UstBefreitErkennung($ust_befreit)
  {
    $ust_befreit = strtolower($ust_befreit);
    switch($ust_befreit) {
      case "inland":
        return 0;
        break;
      case "eu-lieferung":
        return 1;
        break;
      case "export":
        return 2;
        break;
      case "steuerfrei_inland":
        return 3;
        break;
      default:
        return (int)$ust_befreit;
    }
  }

  /**
   * @param string $typ
   *
   * @return string
   */
  function TypErkennung($typ)
  {
    $typ = strtolower($typ);
    switch($typ)
    {
      case "mr": $typ="herr"; break;
      case "mr.": $typ="herr"; break;
      case "ms": $typ="frau"; break;
      case "ms.": $typ="frau"; break;
      case "mrs": $typ="frau"; break;
      case "mrs.": $typ="frau"; break;
      case "company": $typ="firma"; break;
    }
    return $typ;
  }

  /**
   * @param string $datetocheck
   *
   * @return string|null
   */
  private function normalizeDate($datetocheck){
    $normalizedDate = null;
    if(strpos($datetocheck, '.') > 0){
      $datetocheck = $this->app->String->Convert($datetocheck,'%1.%2.%3','%3-%2-%1');
    }

    $datum = DateTime::createFromFormat('Y-m-d', $datetocheck);
    if($datum && $datum->format('Y-m-d') === $datetocheck){
      $normalizedDate = $datetocheck;
    }

    return $normalizedDate;
  }

/*
*   Create a cleaned row set
*   Return true if ok, else see error_message
*/
   private function create_row_set(array $tmp, $pos, array $allowed_fields, array &$result_row, string &$error_message) : bool {
        $result_ok = true;
        $result_row = array();
        $error_message = "";
        foreach ($tmp as $key => $value) {
            if ($key != 'cmd' && $key != 'checked') {
                if (in_array($key,$allowed_fields)) {
                    $result_row[$key] = $value[$pos];
                } else {
                    $error_message .= "Feld nicht korrekt: ".$key."<br>";
                    $result_ok = false;
                }
            }
        }
        return($result_ok);
  }

    private function PrepareDateien(&$tmp, $i, $allowed_fields, $global_data) {

        do { // Allow breaks
            $result = array();
            $result_row = array();

            $fields = array();
            foreach($allowed_fields as $field) {
                $fields[$field] = $tmp[$field][$i];
            }

            $dateiaktion = $fields['dateiaktion'];
            // Check source file
            if (in_array($dateiaktion,['zip'])) {
                $key_in_files = array_search($fields['quellpfad'], array_column((array) $global_data['additional_files'],'pathinzip'));

                if ($key_in_files === false) {
                    $action_anzeige .= 'Datei in ZIP nicht gefunden: '.$fields['quellpfad'];
                    break;
                }
                $result_row['additional_file'] = $global_data['additional_files'][$key_in_files];
            }

            // check dms file
            $dms_objekt = $global_data['dateiobjekte'][strtolower($fields['dms-objekt'])]['wert'];
            $dms_suchfeld = strtolower($fields['dms-objektsuchfeld']);
            $dms_objeknummer = $fields['dms-objektnummer'];
            $dms_stichwort = $global_data['dateitypen_artikel'][strtolower($fields['dms-stichwort'])];
            $dms_dateiname = $fields['dms-dateiname'];
                    
            if (in_array($dateiaktion,['aendern', 'entfernen'])) {
                if (empty($dms_objekt) || empty($dms_objeknummer)) {
                    $action_anzeige .= "DMS-Objekt nicht angegeben";
                    break;
                }
                $objekt = $this->app->erp->getDateiObjekt($dms_objekt,$dms_objeknummer,$dms_suchfeld);
                if (empty($objekt)) {
                    $action_anzeige .= "DMS-Objekt nicht gefunden: ".$dms_objekt." ".$dms_objeknummer;
                    break;
                }
                $dms_dateien = $this->app->erp->GetDateiSubjektObjekt($dms_stichwort['wert'],$dms_objekt,$objekt['id'],$dms_dateiname);
                if (empty($dms_dateien)) {
                    $action_anzeige .= 'Keine passenden Dateien gefunden im DMS';
                    break;
                }
                if (!empty($dms_dateien)) {
                    if (count($dms_dateien) > 1 && !empty($fields['dateiname'])) {
                        $action_anzeige .= "Dateiname mehrfach (".count($dms_dateien)." Dateien im DMS)";
                        break;
                    }
                }
                $result_row['dms_dateien'] = $dms_dateien;
            }
            
            // check stichwort
            $stichwort = $global_data['dateitypen_artikel'][strtolower($fields['stichwort'])];
            if (empty($stichwort) && empty($fields['dateiname']) && empty($fields['titel']) && empty($fields['beschreibung'])) {
                $action_anzeige .= 'Stichwort nicht gefunden: '.$fields['stichwort'];
                break;
            }

            // Check target objekt
            if (in_array($dateiaktion,['zip','url','aendern','entfernen'])) {
                $objekt = $this->app->erp->getDateiObjekt($fields['objekt'],$fields['objektnummer'],$fields['objektsuchfeld']);
                if (empty($objekt) && empty($fields['dateiname']) && empty($fields['titel']) && empty($fields['beschreibung'])) {
                    $action_anzeige .= "Objekt nicht gefunden: ".$fields['objekt']." ".$fields['objektnummer'].$check;
                    break;
                }
                $result_row['datei_objekt'] = $objekt;
                $nummer = $fields['objektnummer'];
            }

            // Check existing target file objekt
            if (in_array($dateiaktion,['zip','url'])) {
                $dateiname = basename($fields['quellpfad']);
                $dms_dateien = $this->app->erp->GetDateiSubjektObjekt($stichwort['wert'],$objekt['objekt'],$objekt['id'],$fields['dateiname']);
            }
            
            switch ($dateiaktion) {
                case 'url':
                    $action_anzeige .= 'Dateiaktion URL nicht implementiert. ';
                break;
                case 'zip':
                    if (empty($dms_dateien)) {
                        $action = 'create';
                        $action_anzeige .= 'Datei von '.strtoupper($dateiaktion).' laden';
                    } else {
                        $action = 'update';
                        $action_anzeige .= count($dms_dateien).' Datei(en) von '.strtoupper($dateiaktion).' aktualisieren';
                    }
                break;
                case 'aendern':
                    $action = 'update';
                    $action_anzeige .= count($dms_dateien).' Datei(en) ändern im DMS';
                break;
                case 'entfernen':
                    $action = 'update';
                    $action_anzeige .= count($dms_dateien).' Dateiverknüpfung(en) entfernen im DMS';
                break;
                default:
                    $action_anzeige .= 'Unbekannte Dateiaktion'.$dateiaktion;
                break;
            }
        } while (false); // Just once with breaks

        $result = array(
            'nummer' => $nummer,
            'action' => $action,
            'action_anzeige' => $action_anzeige,
            'result_row' => $result_row
        );

        return($result);
    }

    function ImportvorlageDownloadPreview() {
        header('Cache-Control: must-revalidate');
        header('Pragma: must-revalidate');
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="importvorschau.csv"');
        $preview_file_name = $this->app->erp->GetTMP().'importvorschau'.$this->app->User->GetID();
        echo(file_get_contents($preview_file_name, $preview_data));
        $this->app->ExitXentral();
    }
}

