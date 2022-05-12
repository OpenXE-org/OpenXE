<?php

declare(strict_types=1);

namespace Xentral\Modules\ApiAccount\Service;

use Psr\Log\LogLevel;
use Xentral\Components\Database\Database;
use Xentral\Components\Logger\Logger;
use Xentral\Modules\ApiAccount\Data\ApiAccountData;
use Xentral\Modules\ApiAccount\Exception\ApiAccountNotFoundException;
use Xentral\Modules\ApiAccount\Exception\InvalidArgumentException;
use Xentral\Modules\SystemConfig\Exception\ConfigurationKeyNotFoundException;
use Xentral\Modules\SystemConfig\SystemConfigModule;

final class ApiAccountService
{
    /** @var Database $db */
    private $db;
    /** @var SystemConfigModule */
    private $systemConfig;
    /** @var Logger */
    private $logger;

    public function __construct(Database $database, SystemConfigModule $systemConfig, Logger $logger)
    {
        $this->db = $database;
        $this->systemConfig = $systemConfig;
        $this->logger = $logger;
    }

    /**
     * @param ApiAccountData $apiAccount
     *
     * @return int
     */
    public function createApiAccount(ApiAccountData $apiAccount): int
    {
        $this->db->perform(
            'INSERT INTO `api_account` 
            (`bezeichnung`,`initkey`, `importwarteschlange_name`, `event_url`, `remotedomain`, `aktiv`, 
             `importwarteschlange`, `cleanutf8`, `uebertragung_account`, `projekt`, `permissions`,
             `is_legacy`, `ishtmltransformation`) 
            VALUES (:name, :init_key, :import_queue_name, :event_url, :remotedomain, :active, :import_queue, 
                    :cleanutf8, :transfer_account_id, :project_id, :permissions, :is_legacy, :is_html_transformation)',
            [
                'name'                => $apiAccount->getName(),
                'init_key'            => $apiAccount->getInitKey(),
                'import_queue_name'   => $apiAccount->getImportQueueName(),
                'event_url'           => $apiAccount->getEventUrl(),
                'remotedomain'        => $apiAccount->getRemoteDomain(),
                'active'              => $apiAccount->isActive(),
                'import_queue'        => $apiAccount->isImportQueueActive(),
                'cleanutf8'           => $apiAccount->isCleanUtf8Active(),
                'transfer_account_id' => $apiAccount->getTransferAccountId(),
                'project_id'          => $apiAccount->getProjectId(),
                'permissions'         => $apiAccount->getPermissions(),
                'is_legacy'           => $apiAccount->isLegacy(),
                'is_html_transformation' => $apiAccount->isHtmlTransformationActive(),
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param ApiAccountData $apiAccount
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function updateApiAccount(ApiAccountData $apiAccount): void
    {
        $apiAccountId = $apiAccount->getId();
        if ($apiAccountId < 1) {
            throw new InvalidArgumentException(sprintf('Api account with id %d not found', $apiAccountId));
        }

        $this->db->perform(
            'UPDATE `api_account` 
            SET `bezeichnung` = :name, 
                `initkey` = :initkey,
                `importwarteschlange_name` = :import_queue_name,
                `event_url` = :event_url,
                `remotedomain` = :remotedomain,
                `aktiv` = :active,
                `importwarteschlange` = :import_queue,
                `cleanutf8` = :cleanutf8,
                `uebertragung_account` = :transfer_account_id,
                `projekt` = :project_id,
                `permissions` = :permissions,
                `is_legacy` = :is_legacy,
                `ishtmltransformation` = :is_html_transformation
            WHERE `id` = :id',
            [
                'name'                => $apiAccount->getName(),
                'initkey'             => $apiAccount->getInitKey(),
                'import_queue_name'   => $apiAccount->getImportQueueName(),
                'event_url'           => $apiAccount->getEventUrl(),
                'remotedomain'        => $apiAccount->getRemoteDomain(),
                'active'              => $apiAccount->isActive(),
                'import_queue'        => $apiAccount->isImportQueueActive(),
                'cleanutf8'           => $apiAccount->isCleanUtf8Active(),
                'transfer_account_id' => $apiAccount->getTransferAccountId(),
                'project_id'          => $apiAccount->getProjectId(),
                'permissions'         => $apiAccount->getPermissions(),
                'is_legacy'           => $apiAccount->isLegacy(),
                'is_html_transformation' => $apiAccount->isHtmlTransformationActive(),
                'id'                  => $apiAccountId,
            ]
        );
    }

    /**
     * @param int $apiAccountId
     *
     * @throws InvalidArgumentException
     *
     * @throws ApiAccountNotFoundException
     *
     * @return ApiAccountData
     */
    public function getApiAccountById(int $apiAccountId): ApiAccountData
    {
        if ($apiAccountId === 0) {
            try {
                $this->logger->log(LogLevel::DEBUG, 'API account retrieved with id 0');
                $apiAccountId = $this->systemConfig->getValue('apiaccount', 'migratedapiid');
            } catch (ConfigurationKeyNotFoundException $e) {
                throw new ApiAccountNotFoundException(sprintf('Api account with id %d not found', $apiAccountId), 0, $e);
            }
        }

        $apiAccountRow = $this->db->fetchRow(
            'SELECT a.id, a.bezeichnung, a.initkey, a.importwarteschlange_name, a.event_url, a.remotedomain, a.aktiv,
             a.importwarteschlange, a.cleanutf8, a.uebertragung_account, a.projekt, a.permissions, a.is_legacy, a.ishtmltransformation
            FROM `api_account` AS `a`
            WHERE a.id = :id
            LIMIT 1',
            ['id' => $apiAccountId]
        );

        if (empty($apiAccountRow)) {
            throw new ApiAccountNotFoundException(sprintf('Api account with id %d not found', $apiAccountId));
        }

        return ApiAccountData::fromDbState($apiAccountRow);
    }

    /**
     * @param string $apiAccountRemoteDomain
     *
     * @throws InvalidArgumentException
     *
     * @throws ApiAccountNotFoundException
     *
     * @return ApiAccountData
     */
    public function getApiAccountByRemoteDomain(string $apiAccountRemoteDomain): ApiAccountData
    {
        if ($apiAccountRemoteDomain === '') {
            throw new InvalidArgumentException(
                sprintf('Api account with remote domain %s not found', $apiAccountRemoteDomain)
            );
        }

        $apiAccountRow = $this->db->fetchRow(
            'SELECT a.id, a.bezeichnung, a.initkey, a.importwarteschlange_name, a.event_url, a.remotedomain, a.aktiv,
             a.importwarteschlange, a.cleanutf8, a.uebertragung_account, a.projekt, a.permissions, a.is_legacy, a.ishtmltransformation
            FROM `api_account` AS `a`
            WHERE a.remotedomain = :remotedomain
            LIMIT 1',
            ['remotedomain' => $apiAccountRemoteDomain]
        );

        if (empty($apiAccountRow)) {
            throw new ApiAccountNotFoundException(sprintf('Api account with id %d not found', $apiAccountRemoteDomain));
        }

        return ApiAccountData::fromDbState($apiAccountRow);
    }


    /**
     * @param int $apiAccountId
     *
     * @return void
     */
    public function deleteApiAccountById(int $apiAccountId): void
    {
        $this->db->perform(
            'DELETE FROM `api_account` WHERE `id` = :id',
            ['id' => $apiAccountId]
        );
    }
}
