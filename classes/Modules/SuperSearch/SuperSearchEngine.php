<?php

namespace Xentral\Modules\SuperSearch;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\SuperSearch\Exception\InvalidArgumentException;
use Xentral\Modules\SuperSearch\SearchEngine\SearchTermParser;
use Xentral\Widgets\SuperSearch\Result\ResultCollection;
use Xentral\Widgets\SuperSearch\Result\ResultGroup;
use Xentral\Widgets\SuperSearch\Result\ResultItem;

final class SuperSearchEngine
{
    /** @var Database $db */
    private $db;

    /** @var SearchTermParser $searchTermParser */
    private $searchTermParser;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->searchTermParser = new SearchTermParser();
        $this->db = $database;
    }

    /**
     * @param string     $searchTerm
     * @param array|null $projectIds  Projekt-IDs die der Benutzer aufrufen darf;
     *                                null = Keine Einschränkung (nur bei Admins)
     * @param array|null $moduleNames Module die der Benutzer aufrufen darf;
     *                                null = Keine Einschränkung (nur bei Admins)
     * @param int        $resultLimit Anzahl der Ergebnisse
     *
     * @throws InvalidArgumentException
     *
     * @return ResultCollection
     */
    public function search($searchTerm, array $projectIds = null, array $moduleNames = null, $resultLimit = 30)
    {
        $resultLimit = (int)$resultLimit;
        if ($resultLimit < 1) {
            throw new InvalidArgumentException('Parameter value $resultLimit is invalid.');
        }

        $searchTerm = $this->searchTermParser->parse($searchTerm);

        // Ergebnisse mit Projekt-ID 0 immer anzeigen (z.b. Appstore-Ergebnisse)
        if (is_array($projectIds) && !in_array(0, $projectIds, true)) {
            $projectIds[] = 0;
        }
        if (is_array($moduleNames) && !in_array('appstore', $moduleNames, true)) {
            $moduleNames[] = 'appstore';
        }

        $sqlProjects = '';
        $sqlModules = '';
        $bindValues = [
            'search_term'  => $searchTerm,
            'result_limit' => $resultLimit,
        ];
        if ($projectIds !== null) {
            $sqlProjects = ' AND sii.project_id IN (:project_ids) ';
            $bindValues['project_ids'] = (array)$projectIds;
        }
        if ($moduleNames !== null) {
            $sqlModules = ' AND (sig.module IN (:module_names) OR sig.module IS NULL) ';
            $bindValues['module_names'] = (array)$moduleNames;
        }

        $sql =
            "SELECT 
                 sii.index_name, sii.index_id, sig.title AS `index_title`, sii.project_id, 
                 sii.title, sii.subtitle, sii.additional_infos, sii.link, sii.search_words
             FROM `supersearch_index_item` AS `sii`
             INNER JOIN `supersearch_index_group` AS `sig` ON sii.index_name = sig.name
             WHERE MATCH (sii.search_words) AGAINST (:search_term IN BOOLEAN MODE) 
             {$sqlProjects}
             {$sqlModules}
             AND sii.outdated = 0 AND sig.active = 1
             LIMIT 0, :result_limit";
        $data = $this->db->fetchAll($sql, $bindValues);

        return $this->buildResultCollection($data);
    }

    /**
     * @param array $data
     *
     * @return ResultCollection
     */
    private function buildResultCollection($data)
    {
        $lastIndexUpdate = $this->getRecentIndexTime();
        $results = new ResultCollection([], $lastIndexUpdate);

        foreach ($data as $item) {
            if (!$results->hasGroup($item['index_name'])) {
                $results->addGroup(new ResultGroup($item['index_name'], $item['index_title']));
            }
            /** @var ResultGroup $group */
            $group = $results->getGroup($item['index_name']);
            $group->addItem(ResultItem::fromDbState($item));
        }

        return $results;
    }

    /**
     * Liefert den Zeitpunkt wann der Index das letzte Mal aktualisiert wurde
     *
     * @return DateTimeInterface|null
     */
    private function getRecentIndexTime()
    {
        $sql =
            'SELECT MAX(GREATEST(IFNULL(sig.last_full_update, 1), IFNULL(sig.last_diff_update, 1))) AS `last_update` 
             FROM `supersearch_index_group` AS sig WHERE sig.active = 1';
        $value = $this->db->fetchValue($sql);

        if (empty($value)) {
            return null;
        }

        try {
            $dateTime = new DateTimeImmutable($value);
        } catch (Exception $exception) {
            return null;
        }

        return $dateTime;
    }
}
