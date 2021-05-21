<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Service;

use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\GoogleApi\Data\GoogleAccessTokenData;
use Xentral\Modules\GoogleApi\Data\GoogleAccountPropertyValue;
use Xentral\Modules\GoogleApi\Data\GoogleAccountPropertyCollection;
use Xentral\Modules\GoogleApi\Data\GoogleAccountData;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountAlreadyExistsException;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountDeleteException;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountNotFoundException;
use Xentral\Modules\GoogleApi\Exception\InvalidArgumentException;

final class GoogleAccountService
{
    /** @var GoogleAccountGateway $gateway */
    private $gateway;

    /** @var Database $db */
    private $db;

    /**
     * @param GoogleAccountGateway $gateway
     * @param Database             $database
     */
    public function __construct(GoogleAccountGateway $gateway, Database $database)
    {
        $this->gateway = $gateway;
        $this->db = $database;
    }

    /**
     * @param int         $userId
     * @param string|null $identifier
     * @param string|null $refreshToken
     *
     * @throws InvalidArgumentException
     * @throws GoogleAccountAlreadyExistsException
     * @throws GoogleAccountNotFoundException
     *
     * @return GoogleAccountData
     */
    public function createAccount(int $userId, ?string $identifier, string $refreshToken = null): GoogleAccountData
    {
        if ($userId < 1) {
            throw new InvalidArgumentException('Cannot create Google Account without User Id.');
        }
        try {
            $this->gateway->getAccountByUser($userId);
            throw new GoogleAccountAlreadyExistsException('A Google account already exists for this user.');
        } catch (GoogleAccountNotFoundException $e) {
        }

        $account = new GoogleAccountData(null, $userId, $identifier, $refreshToken);
        $id = $this->insertAccount($account);

        return $this->gateway->getAccount($id);
    }

    /**
     * @param GoogleAccountData $account
     *
     * @return int
     */
    public function saveAccount(GoogleAccountData $account): int
    {
        if ($account->getId() === null || !$this->gateway->existsAccount($account->getId())) {
            return $this->insertAccount($account);
        }

        return $this->updateAccount($account);
    }

    /**
     * Deletes the google user account entry and all associated tokens, scopes and properties
     *
     * @param int $id
     *
     * @throws GoogleAccountDeleteException
     *
     * @return void
     */
    public function deleteAccount(int $id): void
    {
        $this->db->beginTransaction();
        try {
            $queries = [
                'DELETE FROM `google_account` WHERE `id` = :id',
                'DELETE FROM `google_access_token` WHERE `google_account_id` = :id',
                'DELETE FROM `google_account_property` WHERE `google_account_id` = :id',
                'DELETE FROM `google_account_scope` WHERE `google_account_id` = :id',
            ];
            foreach ($queries as $sql) {
                $this->db->perform($sql, ['id' => $id]);
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new GoogleAccountDeleteException('Could not Delete Google Account', $e->getCode(), $e);
        }
        $this->db->commit();
    }

    /**
     * @param GoogleAccessTokenData $token
     *
     * @return void
     */
    public function saveAccessToken(GoogleAccessTokenData $token): void
    {
        $values = $token->toArray();
        $update = 'UPDATE `google_access_token` 
                    SET `token` = :token, `expires` = :expires
                    WHERE `google_account_id` = :google_account_id';
        $affected = $this->db->fetchAffected($update, $values);
        if ($affected > 0) {
            return;
        }
        $insert = 'INSERT INTO `google_access_token` (`google_account_id`, `token`, `expires`) 
                   VALUES (:google_account_id, :token, :expires)';
        $this->db->perform($insert, $values);
    }

    /**
     * @param GoogleAccessTokenData $token
     *
     * @return void
     */
    public function deleteAccessToken(GoogleAccessTokenData $token): void
    {
        $sql = 'DELETE FROM `google_access_token` WHERE `google_account_id` = :google_account_id';
        $this->db->perform($sql, $token->toArray());
    }

    /**
     * @param int                             $accountId
     * @param GoogleAccountPropertyCollection $properties
     *
     * @return void
     */
    public function saveAccountProperties(int $accountId, GoogleAccountPropertyCollection $properties): void
    {
        foreach ($properties->getAll() as $key => $property) {
            if ($property === null) {
                $this->deleteProperty($accountId, $key);
                continue;
            }
            if ($property->getId() === null) {
                $this->insertProperty($accountId, $property);
                continue;
            }
            $this->updateProperty($accountId, $property);
        }
    }

    /**
     * @param int    $accountId
     * @param string $scope
     *
     * @return void
     */
    public function saveAccountScope(int $accountId, string $scope): void
    {
        $existingScopes = $this->gateway->getScopes($accountId);
        if (in_array($scope, $existingScopes, true)) {
            return;
        }
        $sql = 'INSERT INTO `google_account_scope` (`google_account_id`, `scope`)
                VALUES (:account_id, :scope)';
        $this->db->perform($sql, ['account_id' => $accountId, 'scope' => $scope]);
    }

    /**
     * @param int $accountId
     *
     * @return void
     */
    public function deleteAccountScopes(int $accountId): void
    {
        $sql = 'DELETE FROM `google_account_scope` WHERE `google_account_id` = :account_id';
        $this->db->perform($sql, ['account_id' => $accountId]);
    }

    /**
     * @param GoogleAccountData $account
     *
     * @return int
     */
    private function insertAccount(GoogleAccountData $account): int
    {
        $sql = 'INSERT INTO `google_account` (`user_id`, `refresh_token`, `identifier`) VALUES
                (:user_id, :refresh_token, :identifier)';
        $values = $account->toArray();
        $this->db->perform($sql, $values);

        return $this->db->lastInsertId();
    }

    /**
     * @param GoogleAccountData $account
     *
     * @return int
     */
    private function updateAccount(GoogleAccountData $account): int
    {
        $sql = 'UPDATE `google_account` SET 
                     `user_id` = :user_id,
                     `identifier` = :identifier,
                     `refresh_token` = :refresh_token
                 WHERE `id` = :id';
        $values = $account->toArray();
        $this->db->perform($sql, $values);

        return $account->getId();
    }

    /**
     * @param int                        $accountId
     * @param GoogleAccountPropertyValue $property
     *
     * @return void
     */
    private function insertProperty(int $accountId, GoogleAccountPropertyValue $property): void
    {
        $sql = 'INSERT INTO `google_account_property` (`google_account_id`, `varname`, `value`) 
                        VALUES (:account_id, :varname, :value)';
        $values = $property->toArray();
        $values['account_id'] = $accountId;
        $this->db->perform($sql, $values);
    }

    /**
     * @param int                        $accountId
     * @param GoogleAccountPropertyValue $property
     *
     * @return void
     */
    private function updateProperty(int $accountId, GoogleAccountPropertyValue $property): void
    {
        $sql = 'UPDATE `google_account_property` 
                SET `google_account_id` = :account_id, `varname` = :varname, `value` = :value
                WHERE `id` = :id';
        $values = $property->toArray();
        $values['account_id'] = $accountId;
        $this->db->perform($sql, $values);
    }

    /**
     * @param int    $accountId
     * @param string $varname
     *
     * @return void
     */
    private function deleteProperty(int $accountId, string $varname): void
    {
        $sql = 'DELETE FROM `google_account_property` 
                WHERE `google_account_id` = :account_id AND `varname` = :varname';
        $values = ['account_id' => $accountId, 'varname' => $varname];
        $this->db->perform($sql, $values);
    }
}
