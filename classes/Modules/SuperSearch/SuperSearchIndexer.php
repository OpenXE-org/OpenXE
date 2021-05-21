<?php

namespace Xentral\Modules\SuperSearch;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Modules\SuperSearch\Exception\InvalidReturnValueException;
use Xentral\Modules\SuperSearch\Exception\ProviderIncompatibleException;
use Xentral\Modules\SuperSearch\Exception\ProviderMissingException;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\BulkIndexProviderInterface;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\DiffIndexProviderInterface;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\FullIndexProviderInterface;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\ItemIndexProviderInterface;
use Xentral\Modules\SuperSearch\SearchIndex\Provider\SearchIndexProviderInterface;

final class SuperSearchIndexer
{
    /** @var Database $db */
    private $db;

    /** @var array|SearchIndexProviderInterface[] $provider */
    private $provider;

    /**
     * @param Database                             $database
     * @param SearchIndexProviderInterface[]|array $provider Nur aktive Provider übergeben!
     */
    public function __construct(Database $database, array $provider = [])
    {
        $this->db = $database;
        $this->provider = $provider;
    }

    /**
     * Gibt Meta-Information zu den Providern zurück; nur von aktiven Providern
     *
     * @return array
     */
    public function getProviderMetaData()
    {
        $meta = [];
        foreach ($this->provider as $provider) {
            $meta[] = [
                'name'   => $provider->getIndexName(),
                'title'  => $provider->getIndexTitle(),
                'module' => $provider->getModuleName(),
            ];
        }

        return $meta;
    }

    /**
     * Tatsächliche Index-Größe ermitteln (gruppiert nach Index) (nur von aktiven Providern)
     *
     * @return array
     */
    public function getProviderIndexSizesCurrent()
    {
        $sql =
            'SELECT sig.name, COUNT(sii.id) AS `index_size` 
             FROM `supersearch_index_group` AS `sig` 
             LEFT JOIN `supersearch_index_item` AS `sii` ON sig.name = sii.index_name AND sii.outdated = 0 
             WHERE sig.active = 1
             GROUP BY sig.name';
        $indexSizes = $this->db->fetchPairs($sql);

        // Fehlende Indexe ergänzen, falls Provider registriert ist aber noch nie gelaufen ist
        foreach ($this->provider as $provider) {
            $indexName = $provider->getIndexName();
            if (!isset($indexSizes[$indexName])) {
                $indexSizes[$indexName] = null;
            }
        }
        ksort($indexSizes);

        return $indexSizes;
    }

    /**
     * Potentielle Index-Größe ermitteln (gruppiert nach Index) (nur von aktiven Providern)
     *
     * @return array
     */
    public function getProviderIndexSizesPotential()
    {
        $indexSizes = [];
        foreach ($this->provider as $provider) {

            // Möglich Index-Größe beim Provider erfragen
            $indexSizePotential = null;
            if ($provider instanceof BulkIndexProviderInterface) {
                $indexSizePotential = $provider->getTotalCount();
            }

            $indexSizes[$provider->getIndexName()] = $indexSizePotential;
        }
        ksort($indexSizes);

        return $indexSizes;
    }

    /**
     * @param string $name Index-Name
     *
     * @throws ProviderMissingException
     * @throws InvalidReturnValueException
     *
     * @return void
     */
    public function updateIndexFull($name)
    {
        /** @var FullIndexProviderInterface $provider */
        $provider = $this->tryGetProviderByIndexName($name);
        if ($provider === null) {
            throw new ProviderMissingException(sprintf('Provider for index "%s" is missing', $name));
        }

        if ($provider instanceof BulkIndexProviderInterface) {

            /** @var BulkIndexProviderInterface $provider */
            $totalCount = $provider->getTotalCount();
            if ($totalCount < 0) {
                throw new InvalidReturnValueException(sprintf(
                    'Method %s::getTotalCount() returned an invalid value "%s". Total count must be a positive number.',
                    get_class($provider),
                    $totalCount
                ));
            }

            $itemsPerStep = $provider->getBulkSize();
            $currentOffset = 0;

            // Alle Index-Einträge als "veraltet" markieren
            $this->markIndexAsOutdated($name);

            do {
                $items = $provider->getBulkItems($currentOffset, $itemsPerStep);

                $this->db->beginTransaction();
                foreach ($items as $item) {
                    $this->saveItem($item);
                }
                unset($items);
                $this->db->commit();

                $currentOffset += $itemsPerStep;

            } while ($currentOffset < $totalCount);

            // Full-Update-Zeitpunkt aktualisieren
            $this->updateLastFullUpdateTime($name);
            // Diff-Update-Zeitpunkt ebenfalls aktualisieren > Nächstes Diff-Update dann ab diesem Zeitpunkt
            $this->updateLastDiffUpdateTime($name);
            // Als "veraltet" markierte Index-Einträge löschen
            $this->deleteOutdatedIndexItems($name);
        }

        if (
            $provider instanceof FullIndexProviderInterface &&
            !$provider instanceof BulkIndexProviderInterface
        ) {
            $this->markIndexAsOutdated($name);

            $items = $provider->getAllItems();
            $this->db->beginTransaction();
            foreach ($items as $item) {
                $this->saveItem($item);
            }
            $this->db->commit();

            $this->updateLastFullUpdateTime($name);
            $this->deleteOutdatedIndexItems($name);
        }
    }

    /**
     * @param string            $name Index-Name
     * @param DateTimeInterface $since
     *
     * @throws ProviderMissingException
     *
     * @return void
     */
    public function updateIndexSince($name, DateTimeInterface $since)
    {
        /** @var DiffIndexProviderInterface $provider */
        $provider = $this->tryGetProviderByIndexName($name);
        if ($provider === null) {
            throw new ProviderMissingException(sprintf('Provider for index "%s" is missing', $name));
        }

        if (!$provider instanceof DiffIndexProviderInterface) {
            return;
        }

        $items = $provider->getItemsSince($since);
        $this->db->beginTransaction();
        foreach ($items as $item) {
            $this->saveItem($item);
        }
        $this->db->commit();

        $this->updateLastDiffUpdateTime($name);
    }

    /**
     * @param string $name
     *
     * @return DateTimeInterface|null
     */
    public function getLastFullIndexTime($name)
    {
        $dateString = $this->db->fetchValue(
            'SELECT sig.last_full_update FROM `supersearch_index_group` AS `sig` WHERE sig.name = :index_name',
            ['index_name' => (string)$name]
        );

        try {
            if (!empty($dateString)) {
                return new DateTimeImmutable($dateString);
            }
        } catch (Exception $exception) {
            // nope - return null
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return DateTimeInterface|null
     */
    public function getLastDiffIndexTime($name)
    {
        $dateString = $this->db->fetchValue(
            'SELECT sig.last_diff_update FROM `supersearch_index_group` AS `sig` WHERE sig.name = :index_name',
            ['index_name' => (string)$name]
        );

        try {
            if (!empty($dateString)) {
                return new DateTimeImmutable($dateString);
            }
        } catch (Exception $exception) {
            // nope - return null
        }

        return null;
    }

    /**
     * @param IndexIdentifier $identifier
     *
     * @return void
     */
    public function deleteIndexItem(IndexIdentifier $identifier)
    {
        $sql =
            'DELETE FROM `supersearch_index_item` 
             WHERE `index_name` = :index_name AND `index_id` = :index_id
             LIMIT 1';
        $bindValues = [
            'index_name' => $identifier->getName(),
            'index_id'   => $identifier->getId(),
        ];

        $this->db->perform($sql, $bindValues);
    }

    /**
     * @param IndexIdentifier $identifier
     *
     * @throws ProviderMissingException
     * @throws ProviderIncompatibleException
     *
     * @return void
     */
    public function updateIndexItem(IndexIdentifier $identifier)
    {
        /** @var ItemIndexProviderInterface $provider */
        $provider = $this->tryGetProviderByIndexName($identifier->getName());
        if ($provider === null) {
            throw new ProviderMissingException(sprintf(
                'Provider for index "%s" is missing.', $identifier->getName()
            ));
        }

        if (!$provider instanceof ItemIndexProviderInterface) {
            throw new ProviderIncompatibleException(sprintf(
                'Provider for index "%s" is incompatible. Provider %s does not implement %s.',
                $identifier->getName(),
                get_class($provider),
                ItemIndexProviderInterface::class
            ));
        }

        $item = $provider->getItem($identifier);
        if ($item === null) {
            return;
        }

        $this->saveItem($item);
    }
    
    /**
     * Updates or creates an index item
     *
     * @param IndexItem $item
     *
     * @throws ProviderMissingException
     *
     * @return void
     */
    private function saveItem(IndexItem $item)
    {
        $existingItemId = (int)$this->db->fetchValue(
            'SELECT sii.id
             FROM `supersearch_index_item` AS `sii`
             WHERE sii.index_name = :index_name AND sii.index_id = :index_id',
            [
                'index_name' => $item->identifier->getName(),
                'index_id'   => $item->identifier->getId(),
            ]
        );

        if ($existingItemId > 0) {
            $this->updateItem($item);
        } else {
            $this->createItem($item);
        }
    }

    /**
     * @param IndexItem $item
     *
     * @throws DatabaseExceptionInterface
     *
     * @return void
     */
    private function updateItem(IndexItem $item)
    {
        $searchWords = '';
        if (!empty($item->data->getWords())) {
            $searchWords = implode(' | ', $item->data->getWords());
        }
        $additionalInfos = null;
        if (!empty($item->data->getAdditionalInfos())) {
            $additionalInfos = implode(' ## ', $item->data->getAdditionalInfos());
        }

        $sql =
            'UPDATE `supersearch_index_item` 
             SET
                 `project_id` = :project_id,
                 `title` = :title,
                 `subtitle` = :subtitle,
                 `additional_infos` = :additional_infos,
                 `link` = :link,
                 `search_words` = :search_words,
                 `created_at` = `created_at`,
                 `updated_at` = NOW(),
                 `outdated` = 0
             WHERE `index_name` = :index_name AND `index_id` = :index_id
             LIMIT 1';

        $bindValues = [
            'index_name'       => $item->identifier->getName(),
            'index_id'         => $item->identifier->getId(),
            'project_id'       => $item->data->getProjectId(),
            'title'            => $item->data->getTitle(),
            'link'             => $item->data->getLink(),
            'subtitle'         => $item->data->getSubTitle(),
            'search_words'     => $searchWords,
            'additional_infos' => $additionalInfos,
        ];

        $this->db->perform($sql, $bindValues);
    }

    /**
     * @param IndexItem $item
     *
     * @return void
     */
    private function createItem(IndexItem $item)
    {
        $searchWords = '';
        if (!empty($item->data->getWords())) {
            $searchWords = implode(' | ', $item->data->getWords());
        }
        $additionalInfos = null;
        if (!empty($item->data->getAdditionalInfos())) {
            $additionalInfos = implode(' ## ', $item->data->getAdditionalInfos());
        }

        $sql =
            'INSERT INTO `supersearch_index_item` 
                (`index_name`, `index_id`, `project_id`, `title`, `subtitle`, `additional_infos`, 
                 `link`, `search_words`, `outdated`, `created_at`, `updated_at`) 
             VALUES 
                (:index_name, :index_id, :project_id, :title, :subtitle, :additional_infos, 
                 :link, :search_words, 0, NOW(), NULL)';
        $bindValues = [
            'index_name'       => $item->identifier->getName(),
            'index_id'         => $item->identifier->getId(),
            'project_id'       => $item->data->getProjectId(),
            'title'            => $item->data->getTitle(),
            'link'             => $item->data->getLink(),
            'subtitle'         => $item->data->getSubTitle(),
            'search_words'     => $searchWords,
            'additional_infos' => $additionalInfos,
        ];

        $this->db->perform($sql, $bindValues);
    }

    /**
     * Alle Einträge als veraltet markieren
     *
     * Beim Update/Insert eines Eintrags wird dieser wieder auf ungelöscht gestellt bevor er wirklich gelöscht wird.
     *
     * @param string $indexName
     *
     * @return void
     */
    private function markIndexAsOutdated($indexName)
    {
        $sql = 'UPDATE `supersearch_index_item` SET `outdated` = 1 WHERE `index_name` = :index_name';
        $this->db->perform($sql, ['index_name' => (string)$indexName]);
    }

    /**
     * @param string $indexName
     *
     * @return void
     */
    private function deleteOutdatedIndexItems($indexName)
    {
        $sql = 'DELETE FROM `supersearch_index_item` WHERE `index_name` = :index_name AND `outdated` = 1';
        $this->db->perform($sql, ['index_name' => (string)$indexName]);
    }

    /**
     * @param string $name
     *
     * @return SearchIndexProviderInterface|null
     */
    private function tryGetProviderByIndexName($name)
    {
        foreach ($this->provider as $provider) {
            if ($name === $provider->getIndexName()) {
                return $provider;
            }
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    private function updateLastFullUpdateTime($name)
    {
        $sql = 'UPDATE `supersearch_index_group` SET `last_full_update` = NOW() WHERE `name` = :index_name LIMIT 1';
        $this->db->perform($sql, ['index_name' => (string)$name]);
    }

    /**
     * @param string $name
     *
     * @return void
     */
    private function updateLastDiffUpdateTime($name)
    {
        $sql = 'UPDATE `supersearch_index_group` SET `last_diff_update` = NOW() WHERE `name` = :index_name LIMIT 1';
        $this->db->perform($sql, ['index_name' => (string)$name]);
    }
}
