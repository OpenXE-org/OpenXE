<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Service;

use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\GoogleApi\Data\GoogleAccessTokenData;
use Xentral\Modules\GoogleApi\Data\GoogleAccountPropertyValue;
use Xentral\Modules\GoogleApi\Data\GoogleAccountPropertyCollection;
use Xentral\Modules\GoogleApi\Data\GoogleAccountData;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountGatewayException;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountNotFoundException;
use Xentral\Modules\GoogleApi\Exception\NoAccessTokenException;
use Xentral\Modules\GoogleApi\GoogleScope;

final class GoogleAccountGateway
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
     * @param int $id
     *
     * @return bool
     */
    public function existsAccount(int $id): bool
    {
        $sql = 'SELECT ga.id FROM `google_account` AS `ga` WHERE ga.id = :id';
        $resultId = $this->db->fetchValue($sql, ['id' => $id]);

        return $resultId === $id;
    }

    /**
     * @param int $id
     *
     * @throws GoogleAccountNotFoundException
     *
     * @return GoogleAccountData
     */
    public function getAccount(int $id): GoogleAccountData
    {
        $query = $this->buildAccountQuery()
            ->where('ga.id = :id');
        $values = ['id' => $id];
        $account = $this->queryAccount($query, $values);
        if ($account === null) {
            throw new GoogleAccountNotFoundException('Google Account not Available.');
        }

        return $account;
    }

    /**
     * @param int $userId
     *
     * @throws GoogleAccountNotFoundException
     *
     * @return GoogleAccountData
     */
    public function getAccountByUser(int $userId): GoogleAccountData
    {
        $query = $this->buildAccountQuery()
            ->where('ga.user_id = :user_id');
        $values = ['user_id' => $userId];
        $account = $this->queryAccount($query, $values);
        if ($account === null) {
            throw new GoogleAccountNotFoundException(
                sprintf('No Google account found for user "%s"', $userId)
            );
        }

        return $account;
    }

    /**
     * @param string $email
     *
     * @throws GoogleAccountNotFoundException
     *
     * @return GoogleAccountData
     */
    public function getAccountByGmailAddress(string $email): GoogleAccountData
    {
        try {
            $query = $this->buildAccountQuery()
                ->join('', 'google_account_property AS gp', 'ga.id = gp.google_account_id')
                ->where("gp.varname = :varname AND gp.value = :email");
            $values = ['varname' => 'gmail_address', 'email' => $email];
            $account = $this->queryAccount($query, $values);
        } catch (Exception $e) {
            throw new GoogleAccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
        if ($account === null) {
            throw new GoogleAccountNotFoundException(
                sprintf('No Google Account found for email address "%s"', $email)
            );
        }

        return $account;
    }


    /**
     * Gets all accounts that have a certain scope
     *
     * @param string $scope
     *
     * @return GoogleAccountData[]|array
     */
    public function getAccountsByScope(string $scope): array
    {
        try {
            $query = $this->buildAccountQuery()
                ->join('', 'google_account_scope AS gs', 'ga.id = gs.google_account_id')
                ->where('gs.scope = :scope');
            $values = ['scope' => $scope];
            $rows = $this->db->fetchAll($query->getStatement(), $values);
            $accounts = [];
            foreach ($rows as $row) {
                $accounts[] = GoogleAccountData::fromDbState($row);
            }

            return $accounts;
        } catch (Exception $e) {
            throw new GoogleAccountGatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $addressId
     *
     * @throws GoogleAccountGatewayException
     *
     * @return GoogleAccountData|null
     */
    public function tryGetAccountByAddress(int $addressId): ?GoogleAccountData
    {
        try {
            $query = $this->buildAccountQuery()
                ->join('', 'user AS u', 'ga.user_id = u.id')
                ->where('u.adresse = :address_id');
            $values = ['address_id' => $addressId];

            return $this->queryAccount($query, $values);
        } catch (Exception $e) {
            throw new GoogleAccountGatewayException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $accountId
     *
     * @throws NoAccessTokenException
     *
     * @return GoogleAccessTokenData
     */
    public function getAccessToken(int $accountId): GoogleAccessTokenData
    {
        $query = $this->buildTokenQuery()
            ->where('gt.google_account_id = :account_id');
        $values = ['account_id' => $accountId];
        $token = $this->queryToken($query, $values);
        if ($token === null) {
            throw new NoAccessTokenException(
                sprintf('No access token for account "%s" available.', $accountId)
            );
        }

        return $token;
    }

    /**
     * @param int    $accountId
     * @param string $scope
     *
     * @return bool
     */
    public function hasAccountScope(int $accountId, string $scope): bool
    {
        $scopes = $this->getScopes($accountId);
        if (in_array($scope, $scopes, true)) {
            return true;
        }

        return false;
    }

    /**
     * @param int $accountId
     *
     * @return string[] available scopes of the account
     */
    public function getScopes(int $accountId): array
    {
        $sql = 'SELECT gs.id, gs.scope FROM `google_account_scope` AS `gs` WHERE gs.google_account_id = :account_id';
        $pairs = $this->db->fetchPairs($sql, ['account_id' => $accountId]);
        if (!is_array($pairs) || count($pairs) === 0) {
            return [];
        }

        return array_values($pairs);
    }

    /**
     * Will be removed after Dec 31. 2020
     *
     * @deprecated
     *
     * @codeCoverageIgnore
     *
     * @return GoogleAccountData
     */
    public function getCloudPrintAccount(): GoogleAccountData
    {
        $accounts = $this->getAccountsByScope(GoogleScope::CLOUDPRINT);
        if (count($accounts) < 1) {
            throw new GoogleAccountNotFoundException('No cloud printing account available.');
        }

        return $accounts[0];
    }

    /**
     * @param int $accountId
     *
     * @return GoogleAccountPropertyCollection
     */
    public function getAccountProperties(int $accountId): GoogleAccountPropertyCollection
    {
        $sql = 'SELECT gp.id, gp.google_account_id, gp.varname, gp.value 
                FROM `google_account_property` AS `gp`
                WHERE gp.google_account_id = :account_id';
        $result = $this->db->fetchAll($sql, ['account_id'=> $accountId]);
        if (!is_array($result) || count($result) === 0) {
            return new GoogleAccountPropertyCollection([]);
        }
        $properties = [];
        foreach ($result as $row) {
            $properties[] = GoogleAccountPropertyValue::fromDbState($row);
        }

        return new GoogleAccountPropertyCollection($properties);
    }

    /**
     * @return SelectQuery
     */
    private function buildAccountQuery(): SelectQuery
    {
        return $this->db->select()
            ->cols(
                [
                    'ga.id',
                    'ga.user_id',
                    'ga.refresh_token',
                    'ga.identifier',
                ]
            )
            ->from('google_account AS ga');
    }

    /**
     * @param SelectQuery $query
     * @param array       $bindValues
     *
     * @return GoogleAccountData|null
     */
    private function queryAccount(SelectQuery $query, $bindValues = []): ?GoogleAccountData
    {
        $sql = $query->getStatement();
        $resultSet = $this->db->fetchRow($sql, $bindValues);
        if (empty($resultSet)) {
            return null;
        }

        return GoogleAccountData::fromDbState($resultSet);
    }

    /**
     * @return SelectQuery
     */
    private function buildTokenQuery(): SelectQuery
    {
        return $this->db->select()
            ->cols(
                [
                    'gt.google_account_id',
                    'gt.token',
                    'gt.expires',
                ]
            )
            ->from('google_access_token AS gt');
    }

    /**
     * @param SelectQuery $query
     * @param array       $bindValues
     *
     * @return GoogleAccessTokenData|null
     */
    private function queryToken(SelectQuery $query, $bindValues = []): ?GoogleAccessTokenData
    {
        $sql = $query->getStatement();
        $resultSet = $this->db->fetchRow($sql, $bindValues);
        if (empty($resultSet)) {
            return null;
        }

        return GoogleAccessTokenData::fromDbState($resultSet);
    }
}
