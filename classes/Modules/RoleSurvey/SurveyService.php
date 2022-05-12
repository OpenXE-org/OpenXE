<?php

declare(strict_types=1);

namespace Xentral\Modules\RoleSurvey;


use Xentral\Components\Database\Database;
use Xentral\Modules\RoleSurvey\Exception\InvalidArgumentException;

class SurveyService
{
    /** @var Database $db */
    private $db;

    /** @var SurveyGateway $gateway */
    private $gateway;

    /**
     * SurveyService constructor.
     *
     * @param Database      $db
     * @param SurveyGateway $gateway
     */
    public function __construct(Database $db, SurveyGateway $gateway)
    {
        $this->db = $db;
        $this->gateway = $gateway;
    }

    /**
     * @param string $name
     * @param string $module
     * @param string $action
     * @param bool   $oncePerUser
     * @param bool   $sendToXentral
     *
     * @return int
     */
    public function create($name, $module, $action, $oncePerUser, $sendToXentral): int
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name is empty');
        }

        $survey = $this->gateway->getByName($name);
        if (!empty($survey)) {
            throw new InvalidArgumentException('survey allready exists');
        }

        $this->db->perform(
            'INSERT INTO `survey` (`name`, `module`, `action`, `once_per_user`, `send_to_xentral`) 
            VALUES (:name, :module, :action, :oncePerUser, :sendToXentral)',
            [
                'name'          => $name,
                'module'        => (string)$module,
                'action'        => (string)$action,
                'oncePerUser' => (int)(bool)$oncePerUser,
                'sendToXentral' => (int)(bool)$sendToXentral,
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param int $surveyId
     * @param int $userId
     */
    public function clearUserData($surveyId, $userId): void
    {
        $survey = $this->gateway->getById($surveyId);
        if (empty($survey)) {
            throw new InvalidArgumentException('survey not found');
        }
        $this->db->perform(
            'DELETE FROM `survey_user` WHERE `survey_id` = :surveyId AND `user_id` = :userId',
            [
                'surveyId' => $surveyId,
                'userId'   => $userId,
            ]
        );
    }

    /**
     * @param int   $surveyId
     * @param int   $userId
     * @param array $data
     *
     * @return int
     */
    public function saveUserAnswer($surveyId, $userId, $data): int
    {
        $survey = $this->gateway->getById($surveyId);
        if (empty($survey)) {
            throw new InvalidArgumentException('survey not found');
        }

        $filled = $this->gateway->getFilledSurveyByUser($surveyId, $userId);
        if (empty($filled)) {
            $this->db->perform(
                'INSERT INTO `survey_user` (`survey_id`, `user_id`, `data`, `created_at`) 
                VALUES (:surveyId, :userId, :data, NOW())',
                [
                    'surveyId' => (int)$surveyId,
                    'userId'   => (int)$userId,
                    'data'     => (string)json_encode($data),
                ]
            );

            return $this->db->lastInsertId();
        }

        $json = json_decode($filled['data'], true);
        if (empty($json)) {
            $json = [];
        }
        $data = array_merge($json, $data);

        $this->db->perform(
            'UPDATE `survey_user` SET `data` = :data WHERE `id` = :id',
            [
                'data' => (string)json_encode($data),
                'id'   => (int)$filled['id'],
            ]
        );

        return (int)$filled['id'];
    }

    /**
     * @param int    $surveyId
     * @param string $url
     * @param string $serial
     * @param string $key
     */
    public function sendToXentral($surveyId, $url, $serial, $key): void
    {
        $survey = $this->gateway->getById($surveyId);
        if (empty($survey)) {
            throw new InvalidArgumentException('survey not found');
        }
        if (empty($survey['send_to_xentral'])) {
            throw new InvalidArgumentException('survey not marked as sendable');
        }

        $filled = $this->gateway->getFilledBySurvey($surveyId);
        if (empty($filled)) {
            throw new InvalidArgumentException('no data to send');
        }

        if (empty($serial) || empty($key)) {
            throw new InvalidArgumentException('no serial number');
        }

        foreach($filled as $rowKey => $row) {
            $filled[$rowKey]['name'] = $survey['name'];
        }

        $paras = [
            'serial'     => $serial,
            'schluessel' => $key,
            'paras'      => json_encode($filled),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($paras));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if (substr((string)$info['http_code'], '2') !== 0) {
            throw new InvalidArgumentException('xentral: ' . $ret);
        }
    }
}
