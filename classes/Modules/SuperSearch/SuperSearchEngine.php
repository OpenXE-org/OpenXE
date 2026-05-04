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

        $rawSearchTerm = trim((string)$searchTerm);
        $searchTerm = $this->searchTermParser->parse($rawSearchTerm);

        $result = $this->runFulltextSearch($searchTerm, $projectIds, $moduleNames, $resultLimit);

        $canRunFuzzy = $result->isEmpty() && $rawSearchTerm !== '' && strlen($rawSearchTerm) >= 3;
        if ($canRunFuzzy) {
            $result = $this->runFuzzySearch($rawSearchTerm, $projectIds, $moduleNames, $resultLimit);
            $result->setFuzzy(true);
        }

        return $result;
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
     * @param string     $searchTerm
     * @param array|null $projectIds
     * @param array|null $moduleNames
     * @param int        $resultLimit
     *
     * @return ResultCollection
     */
    private function runFulltextSearch($searchTerm, array $projectIds = null, array $moduleNames = null, $resultLimit = 30)
    {
        if ($searchTerm === '') {
            return $this->buildResultCollection([]);
        }

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
     * @param string     $searchTerm
     * @param array|null $projectIds
     * @param array|null $moduleNames
     * @param int        $resultLimit
     *
     * @return ResultCollection
     */
    private function runFuzzySearch($searchTerm, array $projectIds = null, array $moduleNames = null, $resultLimit = 30)
    {
        $words = preg_split('/\s+/u', $searchTerm, -1, PREG_SPLIT_NO_EMPTY);
        if ($words === false) {
            $words = [];
        }
        $words = array_values(array_unique(array_filter($words, static function ($word) {
            return strlen($word) >= 3;
        })));

        if (empty($words)) {
            return $this->buildResultCollection([]);
        }

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
            // Kandidatenpool für die unscharfe Nachbearbeitung bewusst größer halten
            'result_limit' => max($resultLimit * 10, 400),
        ];

        if ($projectIds !== null) {
            $sqlProjects = ' AND sii.project_id IN (:project_ids) ';
            $bindValues['project_ids'] = (array)$projectIds;
        }
        if ($moduleNames !== null) {
            $sqlModules = ' AND (sig.module IN (:module_names) OR sig.module IS NULL) ';
            $bindValues['module_names'] = (array)$moduleNames;
        }

        $likeParts = [];
        foreach ($words as $index => $word) {
            $key = 'fuzzy_word_' . $index;
            $likeParts[] = "LOWER(sii.search_words) LIKE :{$key}";
            $bindValues[$key] = '%' . strtolower($word) . '%';
        }

        $charParts = [];
        $characters = array_values(array_unique(preg_split('//u', strtolower($searchTerm), -1, PREG_SPLIT_NO_EMPTY)));
        foreach ($characters as $index => $char) {
            if (!preg_match('/[a-z0-9]/', $char)) {
                continue;
            }
            $key = 'fuzzy_char_' . $index;
            $charParts[] = "LOWER(sii.search_words) LIKE :{$key}";
            $bindValues[$key] = '%' . $char . '%';
        }

        $filterClauses = [];
        if (!empty($likeParts)) {
            $filterClauses[] = '(' . implode(' OR ', $likeParts) . ')';
        }
        if (!empty($charParts)) {
            $filterClauses[] = '(' . implode(' AND ', $charParts) . ')';
        }

        $filterClause = '';
        if (!empty($filterClauses)) {
            $filterClause = ' AND (' . implode(' OR ', $filterClauses) . ')';
        }

        $sql =
             "SELECT 
                 sii.index_name, sii.index_id, sig.title AS `index_title`, sii.project_id, 
                 sii.title, sii.subtitle, sii.additional_infos, sii.link, sii.search_words
             FROM `supersearch_index_item` AS `sii`
             INNER JOIN `supersearch_index_group` AS `sig` ON sii.index_name = sig.name
             WHERE 1=1 {$filterClause}
             {$sqlProjects}
             {$sqlModules}
             AND sii.outdated = 0 AND sig.active = 1
             LIMIT 0, :result_limit";

        $data = $this->db->fetchAll($sql, $bindValues);
        $filtered = $this->filterFuzzyResults($data, $words, 2);

        return $this->buildResultCollection(array_slice($filtered, 0, $resultLimit));
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

    /**
     * Filtert Kandidaten �ber eine Levenshtein-Distanz je Suchwort und sortiert nach Passgenauigkeit.
     *
     * @param array $data
     * @param array $searchWords
     * @param int   $maxDistance
     *
     * @return array
     */
    private function filterFuzzyResults(array $data, array $searchWords, $maxDistance = 2)
    {
        $results = [];

        foreach ($data as $item) {
            $tokens = $this->collectSearchTokens($item);
            if (empty($tokens)) {
                continue;
            }

            $score = 0;
            $matchesAll = true;
            foreach ($searchWords as $word) {
                $distance = $this->minimumDistance($word, $tokens);
                if ($distance === null || $distance > $maxDistance) {
                    $matchesAll = false;
                    break;
                }
                $score += $distance;
            }

            if ($matchesAll) {
                $item['_fuzzy_score'] = $score;
                $results[] = $item;
            }
        }

        usort($results, static function ($a, $b) {
            if ($a['_fuzzy_score'] === $b['_fuzzy_score']) {
                return strcmp($a['title'], $b['title']);
            }
            return $a['_fuzzy_score'] <=> $b['_fuzzy_score'];
        });

        return array_map(static function ($item) {
            unset($item['_fuzzy_score']);
            return $item;
        }, $results);
    }

    /**
     * @param array $item
     *
     * @return array
     */
    private function collectSearchTokens(array $item)
    {
        $tokens = [];
        $fields = ['title', 'subtitle', 'additional_infos', 'search_words'];
        foreach ($fields as $field) {
            if (!isset($item[$field]) || $item[$field] === null || $item[$field] === '') {
                continue;
            }
            $value = strtolower((string)$item[$field]);
            $parts = $field === 'additional_infos'
                ? explode(' ## ', $value)
                : preg_split('/[\s,;]+/u', $value, -1, PREG_SPLIT_NO_EMPTY);

            if ($parts !== false) {
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part !== '') {
                        $tokens[] = $part;
                    }
                }
            }
        }

        return array_values(array_unique($tokens));
    }

    /**
     * @param string $word
     * @param array  $tokens
     *
     * @return int|null
     */
    private function minimumDistance($word, array $tokens)
    {
        $word = strtolower($word);
        $min = null;
        foreach ($tokens as $token) {
            $distance = levenshtein($word, $token);
            if ($min === null || $distance < $min) {
                $min = $distance;
            }
        }

        return $min;
    }
}
