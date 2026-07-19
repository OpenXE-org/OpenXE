<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Service;

use DateTime;
use Xentral\Components\Database\Database;
use Xentral\Modules\Office365Api\Data\Office365AccessTokenData;
use Xentral\Modules\Office365Api\Data\Office365AccountData;
use Xentral\Modules\Office365Api\Data\Office365AccountPropertyCollection;
use Xentral\Modules\Office365Api\Data\Office365AccountPropertyValue;
use Xentral\Modules\Office365Api\Exception\Office365OAuthException;

final class Office365AccountGateway
{
    /** @var Database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAccount(int $id): ?Office365AccountData
    {
        $query = 'SELECT * FROM `office365_account` WHERE `id` = :id';
        $result = $this->database->fetchRow($query, ['id' => $id]);

        if (empty($result)) {
            return null;
        }

        return Office365AccountData::fromDbRow($result);
    }

    public function getAccountByEmailAddress(string $email): ?Office365AccountData
    {
        $query = <<<SQL
            SELECT oa.* FROM `office365_account` oa
            INNER JOIN `office365_account_property` oap ON oa.id = oap.office365_account_id
            WHERE oap.varname = 'email_address' AND oap.value = :email
            LIMIT 1
        SQL;

        $result = $this->database->fetchRow($query, ['email' => $email]);

        if (empty($result)) {
            return null;
        }

        return Office365AccountData::fromDbRow($result);
    }

    public function getAccountByUserId(int $userId): ?Office365AccountData
    {
        $query = 'SELECT * FROM `office365_account` WHERE `user_id` = :user_id LIMIT 1';
        $result = $this->database->fetchRow($query, ['user_id' => $userId]);

        if (empty($result)) {
            return null;
        }

        return Office365AccountData::fromDbRow($result);
    }

    public function getAccessToken(int $accountId): ?Office365AccessTokenData
    {
        $query = 'SELECT * FROM `office365_access_token` WHERE `office365_account_id` = :account_id ORDER BY `id` DESC LIMIT 1';
        $result = $this->database->fetchRow($query, ['account_id' => $accountId]);

        if (empty($result)) {
            return null;
        }

        return Office365AccessTokenData::fromArray($result);
    }

    public function saveAccessToken(int $accountId, Office365AccessTokenData $token): void
    {
        $tokenArray = $token->toArray();

        $query = <<<SQL
            INSERT INTO `office365_access_token` (office365_account_id, token, expires)
            VALUES (:account_id, :token, :expires)
            ON DUPLICATE KEY UPDATE
                token = VALUES(token),
                expires = VALUES(expires),
                updated_at = NOW()
        SQL;

        $this->database->perform($query, [
            'account_id' => $accountId,
            'token' => $tokenArray['token'],
            'expires' => $tokenArray['expires']
        ]);
    }

    public function saveAccount(Office365AccountData $account): int
    {
        $query = <<<SQL
            INSERT INTO `office365_account` (user_id, identifier, refresh_token, tenant_id)
            VALUES (:user_id, :identifier, :refresh_token, :tenant_id)
            ON DUPLICATE KEY UPDATE
                refresh_token = VALUES(refresh_token),
                tenant_id = VALUES(tenant_id),
                updated_at = NOW()
        SQL;

        $this->database->perform($query, [
            'user_id' => $account->getUserId(),
            'identifier' => $account->getIdentifier(),
            'refresh_token' => $account->getRefreshToken(),
            'tenant_id' => $account->getTenantId()
        ]);

        if ($account->getId() === 0) {
            return (int)$this->database->lastInsertId();
        }

        return $account->getId();
    }

    public function getAccountProperties(int $accountId): Office365AccountPropertyCollection
    {
        $query = 'SELECT varname, value FROM `office365_account_property` WHERE `office365_account_id` = :account_id';
        $results = $this->database->fetchAll($query, ['account_id' => $accountId]);

        $properties = [];
        if (!empty($results)) {
            foreach ($results as $row) {
                $properties[] = new Office365AccountPropertyValue($row['varname'], $row['value']);
            }
        }

        return new Office365AccountPropertyCollection($properties);
    }

    public function saveAccountProperty(int $accountId, string $name, string $value): void
    {
        $query = <<<SQL
            INSERT INTO `office365_account_property` (office365_account_id, varname, value)
            VALUES (:account_id, :varname, :value)
            ON DUPLICATE KEY UPDATE
                value = VALUES(value),
                updated_at = NOW()
        SQL;

        $this->database->perform($query, [
            'account_id' => $accountId,
            'varname' => $name,
            'value' => $value
        ]);
    }

    public function hasAccountScope(int $accountId, string $scope): bool
    {
        $query = 'SELECT COUNT(*) as count FROM `office365_account_scope` WHERE `office365_account_id` = :account_id AND `scope` = :scope';
        $result = $this->database->fetchRow($query, ['account_id' => $accountId, 'scope' => $scope]);

        return isset($result['count']) && (int)$result['count'] > 0;
    }

    public function saveAccountScope(int $accountId, string $scope): void
    {
        if ($this->hasAccountScope($accountId, $scope)) {
            return;
        }

        $query = 'INSERT INTO `office365_account_scope` (office365_account_id, scope) VALUES (:account_id, :scope)';
        $this->database->perform($query, ['account_id' => $accountId, 'scope' => $scope]);
    }

    public function getScopes(int $accountId): array
    {
        $query = 'SELECT scope FROM `office365_account_scope` WHERE `office365_account_id` = :account_id';
        $results = $this->database->fetchAll($query, ['account_id' => $accountId]);

        if (empty($results)) {
            return [];
        }

        return array_map(fn($row) => $row['scope'], $results);
    }
}
