<?php

declare(strict_types=1);

namespace Xentral\Modules\RoleSurvey;


use Xentral\Components\Database\Database;

final class SurveyGateway
{
    /** @var Database $db */
    private $db;

    /**
     * SurveyGateway constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $surveyId
     *
     * @return array
     */
    public function getById($surveyId): array
    {
        return $this->db->fetchRow(
            'SELECT `id`, `name`, `once_per_user`, `send_to_xentral`, `module`, `action` 
            FROM `survey` 
            WHERE `id` = :id',
            ['id' => (int)$surveyId]
        );
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getByName($name): array
    {
        return $this->db->fetchRow(
            'SELECT `id`, `name`, `once_per_user`, `send_to_xentral`, `module`, `action` 
            FROM `survey` 
            WHERE `name` = :name',
            ['name' => (string)$name]
        );
    }

    /**
     * @param string $module
     *
     * @return array
     */
    public function getByModule($module): array
    {
        return $this->db->fetchAll(
            'SELECT `id`, `name`, `once_per_user`, `send_to_xentral`, `module`, `action` 
            FROM `survey` 
            WHERE `module` = :module',
            ['module' => (string)$module]
        );
    }

    /**
     * @param string $module
     * @param string $action
     * @param int    $userId
     *
     * @return array
     */
    public function getOpenSurveysByModuleAndUser($module, $action, $userId): array
    {
        return $this->db->fetchAll(
            'SELECT `s`.`id`, `s`.`name`, `s`.`once_per_user`, `s`.`send_to_xentral`, `s`.`module`, `s`.`action` 
            FROM `survey` AS `s`
            LEFT JOIN `survey_user` AS `su` ON `s`.`id` = `su`.`survey_id` AND `su`.`user_id` = :userId
            WHERE `s`.`module` = :module AND `s`.`action` = :action AND `s`.`once_per_user` = 1
              AND `su`.`id` IS NULL ',
            [
                'module'  => (string)$module,
                'action'  => (string)$action,
                'userId'  => (int)$userId,
            ]
        );
    }

    /**
     * @param int  $surveyId
     * @param int  $userId
     *
     * @return bool
     */
    public function isSurveyOpenForUser($surveyId, $userId): bool
    {
        return !empty(
            $this->db->fetchRow(
                'SELECT `s`.`id`
                FROM `survey` AS `s`
                LEFT JOIN `survey_user` AS `su` ON `s`.`id` = `su`.`survey_id` AND `su`.`user_id` = :userId
                WHERE `s`.`id` = :surveyId AND `su`.`id` IS NULL AND `s`.`once_per_user` = 1
                LIMIT 1',
                [
                    'surveyId' => (int)$surveyId,
                    'userId'   => (int)$userId,
                ]
            )
        );
    }


    /**
     * @param int  $surveyId
     * @param int  $userId
     *
     * @return array
     */
    public function getFilledSurveyByUser($surveyId, $userId): array
    {
        return $this->db->fetchRow(
            'SELECT `su`.`id`, `su`.`survey_id`, `su`.`data`
            FROM `survey` AS `s`
            INNER JOIN `survey_user` AS `su` ON `s`.`id` = `su`.`survey_id` AND `su`.`user_id` = :userId
            WHERE `s`.`id` = :surveyId
            LIMIT 1',
            [
                'surveyId' => (int)$surveyId,
                'userId'   => (int)$userId,
            ]
        );
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getFilledById($id): array
    {
        return $this->db->fetchRow(
            'SELECT `su`.`id`, `su`.`survey_id`, `su`.`data`
            FROM `survey` AS `s`
            INNER JOIN `survey_user` AS `su` ON `s`.`id` = `su`.`survey_id`
            WHERE `su`.`id` = :id
            LIMIT 1',
            [
                'id' => (int)$id,
            ]
        );
    }

    /**
     * @param int $surveyId
     *
     * @return array
     */
    public function getFilledBySurvey($surveyId): array
    {
        return $this->db->fetchAll(
            'SELECT `su`.`id`, `s`.name, `su`.`survey_id`, `su`.`data`
            FROM `survey` AS `s`
            INNER JOIN `survey_user` AS `su` ON `s`.`id` = `su`.`survey_id`
            WHERE `s`.`id` = :surveyId',
            [
                'surveyId' => (int)$surveyId,
            ]
        );
    }
}
