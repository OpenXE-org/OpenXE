<?php

namespace Xentral\Modules\SuperSearch\Scheduler;

use Xentral\Modules\SuperSearch\SuperSearchIndexer;
use Xentral\Modules\SuperSearch\SuperSearchService;
use Xentral\Modules\SuperSearch\Wrapper\CompanyConfigWrapper;

final class SuperSearchDiffIndexTask
{
    /** @var SuperSearchService $service */
    private $service;

    /** @var SuperSearchIndexer $indexer */
    private $indexer;

    /** @var CompanyConfigWrapper $config */
    private $config;

    /**
     * @param SuperSearchService   $service
     * @param SuperSearchIndexer   $indexer
     * @param CompanyConfigWrapper $config
     */
    public function __construct(SuperSearchService $service, SuperSearchIndexer $indexer, CompanyConfigWrapper $config)
    {
        $this->service = $service;
        $this->indexer = $indexer;
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $fullIndexCronjobActive = (int)$this->config->get('supersearch_full_index_task_mutex');

        // Diff-Index nur ausführen wenn Full-Index-Cronjob gerade nicht läuft
        if ($fullIndexCronjobActive === 0) {
            $this->updateIndexes();
        }

        // Full-Index läuft gerade > Zähler erhöhen
        if ($fullIndexCronjobActive > 0) {
            $this->config->set('supersearch_full_index_task_mutex', (string)($fullIndexCronjobActive + 1));
        }

        // Full-Index läuft schon sehr lange (eher ein Fehler) > Zähler zurücksetzen
        if ($fullIndexCronjobActive > 3) {
            $this->config->set('supersearch_full_index_task_mutex', '0');
        }
    }

    /**
     * @return void
     */
    private function updateIndexes()
    {
        $meta = $this->indexer->getProviderMetaData();
        foreach ($meta as $row) {
            $indexName = $row['name'];
            $indexTitle = $row['title'];
            $moduleName = $row['module'];

            // Sicherstellen dass die Indexe für die registrierten Provider vorhanden sind
            if (!$this->service->existsIndex($indexName)) {
                $this->service->createIndex($indexName, $indexTitle, $moduleName);
            }

            /*
             * Such-Index befüllen
             */

            // Diff-Index wurde schon einmal ausgeführt > Diff-Index wieder ausführen
            $lastDiffIndexTime = $this->indexer->getLastDiffIndexTime($indexName);
            if ($lastDiffIndexTime !== null) {
                $this->indexer->updateIndexSince($indexName, $lastDiffIndexTime);
                continue;
            }

            // Diff-Index und Full-Index wurden noch nie ausgeführt => FullIndex ausführen
            $lastFullIndexTime = $this->indexer->getLastFullIndexTime($indexName);
            if ($lastDiffIndexTime === null && $lastFullIndexTime === null) {
                $this->indexer->updateIndexFull($indexName);
            }
        }
    }

    /**
     * @return void
     */
    public function cleanup()
    {
        // Nothing to do
    }
}
