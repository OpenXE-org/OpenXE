<?php

namespace Xentral\Modules\SuperSearch\SystemHealth;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Exception;
use Xentral\Components\Database\Database;

final class SuperSearchHealthChecker
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @return array
     */
    public function getHealthStatus()
    {
        $sql =
            'SELECT 
                 sig.id, sig.name, sig.title, sig.module, 
                 IFNULL(sig.last_diff_update, sig.last_full_update) AS `last_cron_run`, 
                 MIN(IFNULL(sii.updated_at, sii.created_at)) AS `oldest_index_item`
             FROM `supersearch_index_group` AS sig
             LEFT JOIN `supersearch_index_item` AS sii ON sig.name = sii.index_name AND sii.outdated = 0
             WHERE sig.active = 1 
             GROUP BY sig.name';
        $indexes = $this->db->fetchAll($sql);

        $outdatedTime = new DateTime('now');
        $outdatedTime->sub(new DateInterval('PT48H'));

        foreach ($indexes as $index) {
            $lastCronRun = $this->tryCreateDateTimeObject($index['last_cron_run']);
            if ($lastCronRun === null) {
                // Prozessstarter ist für diesen Index nie gelaufen
                return $this->buildResultForNotRunningSheduler($index['name']);
            }
            if ($lastCronRun !== null) {
                if ($lastCronRun < $outdatedTime) {
                    // Prozessstarter ist für diesen Index seit mehr als 48 Stunden nicht mehr gelaufen
                    return $this->buildResultForOutdatedShedulerRunDate($index['name']);
                }
            }

            $oldestIndexItem = $this->tryCreateDateTimeObject($index['oldest_index_item']);
            if ($oldestIndexItem !== null) {
                if ($oldestIndexItem < $outdatedTime) {
                    // Such-Index enthält Einträge die seit mehr als 48 Stunden nicht mehr aktualisiert wurden
                    return $this->buildResultForOutdatedIndexItem($index['name']);
                }
            }
        }

        // Wenn Code bis hierhin gelauf ist, ist alles in Ordnung
        return [
            'type'    => 'ok', // string [ok|warning|error]
            'message' => null, // string|null
        ];
    }

    /**
     * @param string $indexName
     *
     * @return array
     */
    private function buildResultForNotRunningSheduler($indexName)
    {
        return [
            'type'    => 'error',
            'message' => sprintf(
                'Befüllung für Such-Index "%s" wurde noch nie ausgeführt.', $indexName
            ),
        ];
    }

    /**
     * @param string $indexName
     *
     * @return array
     */
    private function buildResultForOutdatedShedulerRunDate($indexName)
    {
        return [
            'type'    => 'error',
            'message' => sprintf(
                'Befüllung des Such-Index "%s" wurde seit mehr als 48 Stunden nicht mehr ausgeführt.', $indexName
            ),
        ];
    }

    /**
     * @param string $indexName
     *
     * @return array
     */
    private function buildResultForOutdatedIndexItem($indexName)
    {
        return [
            'type'    => 'warning',
            'message' => sprintf(
                'Der Such-Index "%s" enthält Einträge die seit mehr als 48 Stunden nicht mehr aktualisiert wurden.',
                $indexName
            ),
        ];
    }

    /**
     * @param string|null $dateTimeString
     *
     * @return DateTimeImmutable|null
     */
    private function tryCreateDateTimeObject($dateTimeString = null)
    {
        try {
            if (!empty($dateTimeString)) {
                return new DateTimeImmutable($dateTimeString);
            }
        } catch (Exception $exception) {
        }

        return null;
    }
}
