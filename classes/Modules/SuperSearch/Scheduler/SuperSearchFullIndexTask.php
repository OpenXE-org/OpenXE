<?php

namespace Xentral\Modules\SuperSearch\Scheduler;

use Xentral\Modules\SuperSearch\Exception\SchedulerTaskAlreadyRunningException;
use Xentral\Modules\SuperSearch\Exception\SuperSearchExceptionInterface;
use Xentral\Modules\SuperSearch\SuperSearchIndexer;
use Xentral\Modules\SuperSearch\SuperSearchService;
use Xentral\Modules\SuperSearch\Wrapper\CompanyConfigWrapper;

final class SuperSearchFullIndexTask
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
     * @throws SuperSearchExceptionInterface
     *
     * @return void
     */
    public function execute()
    {
        // Prüfen ob Full-Index-Cronjob bereits läuft > Mehrfachausführung verhindern
        $fullIndexActive = (int)$this->config->get('supersearch_full_index_task_mutex');
        if ($fullIndexActive > 0) {
            throw new SchedulerTaskAlreadyRunningException(
                'SuperSearch full index task is already running. Task can only run once at a time.'
            );
        }

        // Full-Index-Cronjob als Aktiv markieren >
        // Diff-Index-Cronjob prüft den Wert und überspringt dann wenn Full-Index-Cronjob läuft.
        $this->config->set('supersearch_full_index_task_mutex', '1');
        $this->updateIndexes();
    }

    /**
     * @return void
     */
    public function cleanup()
    {
        $this->config->set('supersearch_full_index_task_mutex', '0');
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

            // Such-Index befüllen
            $this->indexer->updateIndexFull($indexName);
        }
    }
}
