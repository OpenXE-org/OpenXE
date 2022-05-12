<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Wrapper;

use Xentral\Components\Database\Database;

final class PipedriveResubmissionWrapper
{
    /** @var Database $db */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param array $deal
     *
     * @return int
     */
    public function addResubmission(array $deal): int
    {
        $this->db->perform(
            'INSERT INTO `wiedervorlage` (`bezeichnung`,
                             `datum_angelegt`,
                             `zeit_angelegt`,
                             `datum_erinnerung`,
                             `zeit_erinnerung`,
                             `stages`,
                             `chance`,
                             `beschreibung`,
                             `ergebnis`)
                    VALUES(:bezeichnung,
                           :datum_angelegt,
                           :zeit_angelegt,
                           :datum_erinnerung,
                           :zeit_erinnerung,
                           :stages,
                           :chance, \'\', \'\'
                           )',
            $deal
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param int   $resubmissionId
     * @param array $deal
     *
     * @return void
     */
    public function updateResubmission(int $resubmissionId, array $deal): void
    {
        $sql = sprintf(
            'UPDATE `wiedervorlage`
                 SET `chance` = :chance, `bezeichnung` = :bezeichnung, `datum_angelegt` = :datum_angelegt,
                         `zeit_angelegt` = :zeit_angelegt, `datum_erinnerung` = :datum_erinnerung,
                         `zeit_erinnerung` = :zeit_erinnerung, `stages` = :stages
                 WHERE id = %d',
            $resubmissionId
        );

        $this->db->perform($sql, $deal);
    }

    /**
     * @param string $name
     * @param string $shortName
     * @param int    $project
     *
     * @return int
     */
    public function addResubmissionView(string $name, string $shortName, int $project = 0): int
    {
        $this->db->perform(
            'INSERT INTO `wiedervorlage_view` (`name`, `shortname`, `project`, `active`)
             VALUES (:name, :desc_short, :project, 1)',
            [
                'name'       => $name,
                'desc_short' => $shortName,
                'project'    => $project,
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param array $stage
     *
     * @return int
     */
    public function addResubmissionStage(array $stage): int
    {
        $this->db->perform(
            'INSERT INTO `wiedervorlage_stages` (`kurzbezeichnung`, `name`,
                                    `stageausblenden`, `sort`, `view`, `ausblenden`)
                            VALUES(:desc, :name,:enabled, :position,:wiedervorlage_view_id,:ausblenden)',
            $stage
        );

        return $this->db->lastInsertId();
    }
}
