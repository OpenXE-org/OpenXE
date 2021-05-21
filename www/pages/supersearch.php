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

use Xentral\Components\Http\JsonResponse;
use Xentral\Modules\SuperSearch\Scheduler\SuperSearchFullIndexTask;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SuperSearchEngine;
use Xentral\Modules\SuperSearch\SuperSearchIndexer;
use Xentral\Modules\SuperSearch\SuperSearchService;
use Xentral\Widgets\SuperSearch\Query\DetailQuery;
use Xentral\Widgets\SuperSearch\Result\ResultDetail;

class SuperSearch
{
  /** @var string MODULE_NAME */
  const MODULE_NAME = 'SuperSearch';

  /** @var ApplicationCore $app */
  public $app;

  /** @var array $javascript */
  public $javascript = [
    './classes/Modules/SuperSearch/www/js/supersearch_ui.js',
  ];

  /**
   * @param ApplicationCore $app
   * @param bool            $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;

    if($intern !== false){
      return;
    }

    $this->app->ActionHandlerInit($this);
    $this->app->DefaultActionHandler('settings');
    $this->app->ActionHandler('settings', 'SuperSearchSettings');
    $this->app->ActionHandler('ajax', 'SuperSearchAjax');
    $this->app->ActionHandlerListen($app);
  }

  /**
   * @return void
   */
  public function Install()
  {
    $this->app->erp->CheckTable('supersearch_index_item');
    $this->app->erp->CheckColumn('id', 'INT(10) UNSIGNED', 'supersearch_index_item', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('index_name', 'VARCHAR(16)', 'supersearch_index_item', 'NOT NULL');
    $this->app->erp->CheckColumn('index_id', 'VARCHAR(38)', 'supersearch_index_item', 'NOT NULL');
    $this->app->erp->CheckColumn('project_id', 'INT(10) UNSIGNED', 'supersearch_index_item', 'NOT NULL DEFAULT 0');
    $this->app->erp->CheckColumn('title', 'VARCHAR(128)', 'supersearch_index_item', 'NOT NULL');
    $this->app->erp->CheckColumn('subtitle', 'VARCHAR(128)', 'supersearch_index_item', 'NULL DEFAULT NULL');
    $this->app->erp->CheckColumn('additional_infos', 'VARCHAR(255)', 'supersearch_index_item', 'NULL DEFAULT NULL');
    $this->app->erp->CheckColumn('link', 'VARCHAR(128)', 'supersearch_index_item', 'NOT NULL');
    $this->app->erp->CheckColumn('search_words', 'TEXT', 'supersearch_index_item', "NOT NULL DEFAULT ''");
    $this->app->erp->CheckColumn('outdated', 'TINYINT(1) UNSIGNED', 'supersearch_index_item', "NOT NULL DEFAULT '0'");
    $this->app->erp->CheckColumn('created_at', 'TIMESTAMP', 'supersearch_index_item', 'NOT NULL DEFAULT CURRENT_TIMESTAMP');
    $this->app->erp->CheckColumn('updated_at', 'TIMESTAMP', 'supersearch_index_item', 'NULL DEFAULT NULL');
    $this->app->erp->CheckIndex('supersearch_index_item', 'project_id');
    $this->app->erp->CheckFulltextIndex('supersearch_index_item', 'search_words');
    $this->app->erp->CheckAlterTable('ALTER TABLE `supersearch_index_item` ADD UNIQUE KEY `index_identifier` (`index_name`, `index_id`)');

    $this->app->erp->CheckTable('supersearch_index_group');
    $this->app->erp->CheckColumn('id', 'INT(10) UNSIGNED', 'supersearch_index_group', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('name', 'VARCHAR(16)', 'supersearch_index_group', 'NOT NULL');
    $this->app->erp->CheckColumn('title', 'VARCHAR(32)', 'supersearch_index_group', 'NOT NULL');
    $this->app->erp->CheckColumn('module', 'VARCHAR(38)', 'supersearch_index_group', 'NULL DEFAULT NULL');
    $this->app->erp->CheckColumn('active', 'TINYINT(1) UNSIGNED', 'supersearch_index_group', "NOT NULL DEFAULT '1'");
    $this->app->erp->CheckColumn('last_full_update', 'TIMESTAMP', 'supersearch_index_group', 'NULL DEFAULT NULL');
    $this->app->erp->CheckColumn('last_diff_update', 'TIMESTAMP', 'supersearch_index_group', 'NULL DEFAULT NULL');
    $this->app->erp->CheckAlterTable('ALTER TABLE `supersearch_index_group` ADD UNIQUE KEY `name` (`name`)');

    $this->app->erp->CheckProzessstarter('SuperSearch Index-Full', 'uhrzeit', '', '2017-01-01 02:30:00', 'cronjob', 'supersearch_index_full', 1);
    $this->app->erp->CheckProzessstarter('SuperSearch Index-Diff', 'periodisch', '3600', '', 'cronjob', 'supersearch_index_diff', 1);

    $this->app->erp->RegisterHook('article_delete', 'supersearch', 'SuperSearchOnArticleDelete');
    $this->app->erp->RegisterHook('address_delete', 'supersearch', 'SuperSearchOnAddressDelete');
  }

  /**
   * @param int $articleId
   */
  public function SuperSearchOnArticleDelete($articleId)
  {
    /** @var SuperSearchIndexer $indexer */
    $indexer = $this->app->Container->get('SuperSearchIndexer');
    $identifier = new IndexIdentifier('articles', (int)$articleId);
    $indexer->deleteIndexItem($identifier);
  }

  /**
   * @param int $addressId
   */
  public function SuperSearchOnAddressDelete($addressId)
  {
    /** @var SuperSearchIndexer $indexer */
    $indexer = $this->app->Container->get('SuperSearchIndexer');
    $identifier = new IndexIdentifier('addresses', (int)$addressId);
    $indexer->deleteIndexItem($identifier);
  }

  /**
   * @return void
   */
  public function SuperSearchMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=supersearch&action=settings', '&Uuml;bersicht');
  }

  /**
   * @return void
   */
  public function SuperSearchSettings()
  {
    $cmd = $this->app->Secure->GetPOST('cmd');
    switch ($cmd) {

      case 'run-full-index-task':
        // Full-Index-Task ausführen
        $response = $this->HandleSuperSearchSettingsFullIndexTask();
        $response->send();
        $this->app->ExitXentral();
        break;

      case 'activate-provider':
        $response = $this->HandleSuperSearchSettingsActivateProvider();
        $response->send();
        $this->app->ExitXentral();
        break;

      case 'deactivate-provider':
        $response = $this->HandleSuperSearchSettingsDeactivateProvider();
        $response->send();
        $this->app->ExitXentral();
        break;
    }

    // Prüfen ob beide Cronjobs aktiv sind
    $this->app->erp->checkActiveCronjob('supersearch_index_full');
    $this->app->erp->checkActiveCronjob('supersearch_index_diff');

    /** @var SuperSearchService $searchService */
    $searchService = $this->app->Container->get('SuperSearchService');
    if ($searchService->isIndexEmpty()) {
      $searchIndexEmptyMessage = 'Der Such-Index ist leer. Die Einstellungen stehen erst zur Verfügung wenn der Such-Index einmalig gefüllt wurde.';
      $this->app->Tpl->Add('MESSAGE', sprintf('<div class="info">%s</div>', $searchIndexEmptyMessage));
    }

    // Tabelle mit Index-Statistik zusammenbauen
    $table = '<table class="mkTable">';
    $table .= '<tr><th align="left">Index</th>';
    $table .= '<th>Index-Größe (aktuell)</th>';
    $table .= '<th>Index-Größe (potentiell)</th>';
    $table .= '<th>Letzes Index-Update</th>';
    $table .= '<th>Provider-Status</th>';
    $table .= '<th>Aktionen</th>';
    $table .= '</tr>';
    $stats = $searchService->getIndexStats();
    foreach ($stats as $info) {
      $lastUpdate = $this->getGreatestDateTime($info['last_full_update'], $info['last_diff_update']);
      $table .= '<tr>';
      $table .= '<td><label>' . $info['title'] . ' (' . $info['name'] .')</label></td>';
      $table .= '<td>' . ($info['index_size_current'] !== null ? $info['index_size_current'] : '<em>Unbekannt</em>') . '</td>';
      $table .= '<td>' . ($info['index_size_potential'] !== null ? $info['index_size_potential'] : '<em>Unbekannt</em>') . '</td>';
      $table .= '<td>' . ($lastUpdate !== null ? $lastUpdate->format('d.m.Y H:i:s') : '<em>NULL</em>') . '</td>';
      $table .= '<td>';
      if ($info['active'] === true) { $table .= 'Aktiv'; }
      if ($info['active'] === false) { $table .= 'Inaktiv'; }
      if ($info['active'] === null) { $table .= '<em>Unbekannt</em>'; }
      $table .= '</td>';
      $buttonTemplate = '<td><button type="button" data-index-name="%s" class="button button-secondary %s">%s</button></td>';
      if ($info['active'] === true){
        $table .= sprintf($buttonTemplate, $info['name'], 'button-provider-deactivate', 'Such-Index deaktivieren');
      }
      if ($info['active'] === false) {
        $table .= sprintf($buttonTemplate, $info['name'], 'button-provider-activate', 'Such-Index aktivieren');
      }
      if ($info['active'] === null) {
        $table .= '<td></td>';
      }
      $table .= '</tr>';
    }
    $table .= '</table>';
    // ENDE Tabelle mit Index-Statistik zusammenbauen

    $this->SuperSearchMenu();
    $this->app->Tpl->Set('UEBERSCHRIFT', 'SuperSearch');
    $this->app->Tpl->Set('KURZUEBERSCHRIFT', 'SuperSearch');
    $this->app->Tpl->Set('TABTEXT', 'SuperSearch');

    $this->app->Tpl->Set('INDEXSTATSTABLE', $table);
    $this->app->Tpl->Parse('PAGE', 'supersearch_settings.tpl');
  }

  /**
   * @return void
   */
  public function SuperSearchAjax()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    switch ($cmd) {

      case 'search':
        // Suchergebnisse ermitteln
        $response = $this->HandleSuperSearchAjaxSearch();
        break;

      case 'detail':
        // Detail-Informationen nachladen: Bei Klick auf ein einzelnes Ergebnis
        $response = $this->HandleSuperSearchAjaxDetail();
        break;
    }

    if (!isset($response)) {
      $response = new JsonResponse(
        ['success' => false, 'error' => 'Parameter \'cmd\' ungültig.'],
        JsonResponse::HTTP_NOT_FOUND
      );
    }

    // Ausgabe
    $response->send();
    $this->app->erp->ExitWawi();
  }

  /**
   * @return JsonResponse
   */
  protected function HandleSuperSearchSettingsFullIndexTask()
  {
    /** @var SuperSearchFullIndexTask $indexTask */
    $indexTask = $this->app->Container->get('SuperSearchFullIndexTask');

    try {
      $indexTask->execute();
      $indexTask->cleanup();
    } catch (Exception $exception) {
      $indexTask->cleanup();
      return new JsonResponse(
        ['success' => false, 'error' => $exception->getMessage()],
        JsonResponse::HTTP_INTERNAL_SERVER_ERROR
      );
    }

    return new JsonResponse(['success' => true]);
  }

  /**
   * @return JsonResponse
   */
  protected function HandleSuperSearchSettingsActivateProvider()
  {
    $indexName = $this->app->Secure->GetPOST('index_name');

    try {
      /** @var SuperSearchService $searchService */
      $searchService = $this->app->Container->get('SuperSearchService');
      $searchService->activateIndex($indexName);
    } catch (Exception $exception) {
      return new JsonResponse(
        ['success' => false, 'error' => $exception->getMessage()],
        JsonResponse::HTTP_INTERNAL_SERVER_ERROR
      );
    }

    return new JsonResponse(['success' => true]);
  }

  /**
   * @return JsonResponse
   */
  protected function HandleSuperSearchSettingsDeactivateProvider()
  {
    $indexName = $this->app->Secure->GetPOST('index_name');

    try {
      /** @var SuperSearchService $searchService */
      $searchService = $this->app->Container->get('SuperSearchService');
      $searchService->deactivateIndex($indexName);
    } catch (Exception $exception) {
      return new JsonResponse(
        ['success' => false, 'error' => $exception->getMessage()],
        JsonResponse::HTTP_INTERNAL_SERVER_ERROR
      );
    }

    return new JsonResponse(['success' => true]);
  }

  /**
   * @return JsonResponse
   */
  protected function HandleSuperSearchAjaxSearch()
  {
    $searchTerm = stripslashes($this->app->Secure->GetPOST('search_query'));
    if (strlen(trim($searchTerm)) >= 3) {

      /** @var SuperSearchEngine $searchEngine */
      $searchEngine = $this->app->Container->get('SuperSearchEngine');
      $projectIds = $this->app->User->GetType() !== 'admin' ? $this->app->User->getUserProjects() : null;
      $moduleNames = $this->app->User->GetType() !== 'admin' ? $this->app->erp->getUserModules() : null;
      $searchResult = $searchEngine->search($searchTerm, $projectIds, $moduleNames);

      // Ergebnis leer > Prüfen ob SuchIndex gefüllt
      if ($searchResult->isEmpty() && $searchResult->getLastIndexUpdateTime() === null) {
        return new JsonResponse(
          [
            'success' => false,
            'error' =>
              '<p>Such-Index ist nicht gefüllt. Bitte prüfen ob Prozessstarter aktiv sind.</p>'.
              '<a class="button button-secondary" href="index.php?module=supersearch&amp;action=settings">'.
              'Zu den SuperSearch-Einstellungen'.
              '</a>',
            'data' => 'index-empty',
          ],
          JsonResponse::HTTP_INTERNAL_SERVER_ERROR
        );
      }
    }


    return new JsonResponse(['success' => true, 'data' => $searchResult]);
  }

  /**
   * @return JsonResponse
   */
  protected function HandleSuperSearchAjaxDetail()
  {
    $detailGroup = $this->app->Secure->GetPOST('detail_group');
    $detailIdentifier = $this->app->Secure->GetPOST('detail_identifier');

    $detailQuery = new DetailQuery($detailGroup, $detailIdentifier);
    $detailResult = new ResultDetail();
    $this->app->erp->RunHook('supersearch_detail', 2, $detailQuery, $detailResult);
    return new JsonResponse(['success' => true, 'data' => $detailResult]);
  }

  /**
   * @param string|null $lastFullUpdate
   * @param string|null $lastDiffUpdate
   *
   * @return DateTimeImmutable|null
   */
  protected function getGreatestDateTime($lastFullUpdate = null, $lastDiffUpdate = null)
  {
    if (!empty($lastFullUpdate)){
      try {
        $lastFullUpdateTime = new \DateTimeImmutable($lastFullUpdate);
      } catch (Exception $exception) {
      }
    }
    if (!empty($lastDiffUpdate)) {
      try {
        $lastDiffUpdateTime = new \DateTimeImmutable($lastDiffUpdate);
      } catch (Exception $exception) {
      }
    }

    if (isset($lastFullUpdateTime) && !isset($lastDiffUpdateTime)) {
      return $lastFullUpdateTime;
    }
    if (!isset($lastFullUpdateTime) && isset($lastDiffUpdateTime)) {
      return $lastDiffUpdateTime;
    }
    if (isset($lastFullUpdateTime) && isset($lastDiffUpdateTime)) {
      if ($lastFullUpdateTime > $lastDiffUpdateTime) {
        return $lastFullUpdateTime;
      } else {
        return $lastDiffUpdateTime;
      }
    }

    return null;
  }
}
