<?php

namespace Xentral\Modules\SuperSearch;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;

final class SuperSearchService
{
    /** @var Database $db */
    private $db;

    /** @var SuperSearchIndexer $indexer */
    private $indexer;

    /**
     * @param Database           $database
     * @param SuperSearchIndexer $indexer
     */
    public function __construct(Database $database, SuperSearchIndexer $indexer)
    {
        $this->db = $database;
        $this->indexer = $indexer;
    }

    /**
     * @param string $indexName
     *
     * @return bool
     */
    public function existsIndex($indexName)
    {
        $sql = 'SELECT sig.id FROM `supersearch_index_group` AS `sig` WHERE sig.name = :index_name';
        $check = (int)$this->db->fetchValue($sql, ['index_name' => (string)$indexName]);

        return $check > 0;
    }

    /**
     * @param string      $indexName
     * @param string      $indexTitle
     * @param string|null $moduleName
     *
     * @return void
     */
    public function createIndex($indexName, $indexTitle, $moduleName = null)
    {
        $sql =
            'INSERT INTO `supersearch_index_group` 
                 (`id`, `name`, `title`, `module`, `active`, `last_full_update`, `last_diff_update`) 
             VALUES 
                 (NULL, :index_name, :index_title, :module_name, 1, NULL, NULL)';
        $this->db->perform($sql, [
            'index_name' => (string)$indexName,
            'index_title' => (string)$indexTitle,
            'module_name' => $moduleName !== null ? (string)$moduleName : null,
        ]);
    }

    /**
     * @param string $indexName
     *
     * @return void
     */
    public function deleteIndex($indexName)
    {
        $this->db->beginTransaction();

        try {
            $sql = 'DELETE FROM `supersearch_index_group` WHERE `name` = :index_name LIMIT 1';
            $this->db->perform($sql, ['index_name' => (string)$indexName]);

            $sql = 'DELETE FROM `supersearch_index_item` WHERE `index_name` = :index_name';
            $this->db->perform($sql, ['index_name' => (string)$indexName]);

            $this->db->commit();
        } catch (DatabaseExceptionInterface $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /**
     * @param string $indexName
     *
     * @return void
     */
    public function activateIndex($indexName)
    {
        $sql = 'UPDATE `supersearch_index_group` SET `active` = 1 WHERE `name` = :index_name LIMIT 1';
        $this->db->perform($sql, ['index_name' => (string)$indexName]);
    }

    /**
     * @param string $indexName
     *
     * @return void
     */
    public function deactivateIndex($indexName)
    {
        $sql =
            'UPDATE `supersearch_index_group` 
             SET `active` = 0, `last_full_update` = NULL, `last_diff_update` = NULL 
             WHERE `name` = :index_name 
             LIMIT 1';
        $this->db->perform($sql, ['index_name' => (string)$indexName]);

        // Index leeren
        $sql = 'DELETE FROM `supersearch_index_item` WHERE `index_name` = :index_name';
        $this->db->perform($sql, ['index_name' => (string)$indexName]);
    }

    /**
     * @return bool
     */
    public function isIndexEmpty()
    {
        $sql = 'SELECT COUNT(sii.id) AS total_count FROM `supersearch_index_item` AS `sii` WHERE sii.outdated = 0';
        $check = (int)$this->db->fetchValue($sql);

        return $check === 0;
    }

    /**
     * @return array
     */
    public function getIndexStats()
    {
        $sql =
            'SELECT sig.name, sig.title, sig.module, sig.active, sig.last_full_update, sig.last_diff_update
             FROM `supersearch_index_group` AS `sig`';
        $groups = $this->db->fetchAll($sql);

        // Fehlende Provider hinzufügen (Provider die noch nie liefen)
        $groupNames = array_column($groups, 'name');
        $meta = $this->indexer->getProviderMetaData();
        foreach ($meta as $info) {
            if (!in_array($info['name'], $groupNames, true)) {
                $groups[] = [
                    'name'             => $info['name'],
                    'title'            => $info['title'],
                    'module'           => $info['module'],
                    'active'           => null,
                    'last_full_update' => null,
                    'last_diff_update' => null,
                ];
            }
        }

        // Index-Statistik hinzufügen
        $indexSizeCurrent = $this->indexer->getProviderIndexSizesCurrent();
        $indexSizePotential = $this->indexer->getProviderIndexSizesPotential();
        foreach ($groups as $indexName => &$group) {
            $indexName = $group['name'];
            $group['index_size_current'] = isset($indexSizeCurrent[$indexName]) ? $indexSizeCurrent[$indexName] : null;
            $group['index_size_potential'] = isset($indexSizePotential[$indexName]) ? $indexSizePotential[$indexName] : null;
            if ($group['active'] === 1) {
                $group['active'] = true;
            }
            if ($group['active'] === 0) {
                $group['active'] = false;
            }
        }
        unset($group);

        return $groups;
    }
}
