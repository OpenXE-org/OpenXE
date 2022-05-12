<?php

namespace Xentral\Modules\User\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\User\Exception\InvalidArgumentException;
use Xentral\Modules\User\Exception\UserConfigKeyNotFoundException;

final class UserConfigService
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param string $key
     * @param int    $userId
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function has($key, $userId)
    {
        $key = $this->ensureKey($key);
        $userId = $this->ensureUserId($userId);

        $value = $this->db->fetchValue(
            'SELECT k.value FROM userkonfiguration AS k 
             WHERE k.user = :user_id AND k.name = :key',
            ['user_id' => $userId, 'key' => $key]
        );

        return $value !== false;
    }

    /**
     * @param string $key
     * @param int    $userId
     *
     * @throws UserConfigKeyNotFoundException
     *
     * @return mixed
     */
    public function get($key, $userId)
    {
        $key = $this->ensureKey($key);
        $userId = $this->ensureUserId($userId);

        $value = $this->db->fetchValue(
            'SELECT k.value FROM userkonfiguration AS k 
             WHERE k.user = :user_id AND k.name = :key 
             LIMIT 1',
            ['user_id' => $userId, 'key' => $key]
        );

        if ($value === false) {
            throw new UserConfigKeyNotFoundException(sprintf(
                'User config key "%s" not found for user "%s".',
                $key,
                $userId
            ));
        }

        return $this->transformReturnValue($value);
    }

    /**
     * Like $this->get(); but no exception if key does not exists
     *
     * @param string $key
     * @param int    $userId
     *
     * @return string|null null if config key does not exists
     */
    public function tryGet($key, $userId)
    {
        $key = $this->ensureKey($key);
        $userId = $this->ensureUserId($userId);

        $value = $this->db->fetchValue(
            'SELECT k.value FROM userkonfiguration AS k 
             WHERE k.user = :user_id AND k.name = :key',
            ['user_id' => $userId, 'key' => $key]
        );

        if ($value === false) {
            return null;
        }

        return $this->transformReturnValue($value);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param int    $userId
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function set($key, $value, $userId)
    {
        $key = $this->ensureKey($key);
        $userId = $this->ensureUserId($userId);
        $value = $this->transformToDatabaseValue($value);

        if ($this->has($key, $userId)) {
            $this->db->perform(
                'UPDATE userkonfiguration SET `value` = :value 
                 WHERE `name` = :key AND `user` = :user_id LIMIT 1',
                ['key' => $key, 'value' => $value, 'user_id' => $userId]
            );
        } else {
            $this->db->perform(
                'INSERT INTO userkonfiguration (`id`, `user`, `name`, `value`) 
                 VALUES (NULL, :user_id, :key, :value)',
                ['key' => $key, 'value' => $value, 'user_id' => $userId]
            );
        }
    }

    /**
     * @param string $key
     * @param int    $userId
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function delete($key, $userId)
    {
        $key = $this->ensureKey($key);
        $userId = $this->ensureUserId($userId);

        $this->db->perform(
            'DELETE FROM userkonfiguration  
             WHERE `name` = :key AND `user` = :user_id LIMIT 1',
            ['key' => $key, 'user_id' => $userId]
        );
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function transformReturnValue($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        if (strtoupper($value) === 'NULL') {
            return null;
        }
        if (strtolower($value) === 'false') {
            return false;
        }
        if (strtolower($value) === 'true') {
            return true;
        }
        if (is_numeric($value)) {
            $pointCount = substr_count($value, '.');
            if ($pointCount === 0) {
                return (int)$value;
            }
            if ($pointCount === 1) {
                return (float)$value;
            }
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    private function transformToDatabaseValue($value)
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value === true ? 'true' : 'false';
        }

        return (string)$value;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    private function ensureKey($key)
    {
        if (empty($key)) {
            throw new InvalidArgumentException('Required parameter "key" is empty.');
        }

        return (string)$key;
    }

    /**
     * @param int $userId
     *
     * @throws InvalidArgumentException
     *
     * @return int
     */
    private function ensureUserId($userId)
    {
        if ((int)$userId <= 0) {
            throw new InvalidArgumentException('Required parameter "userId" is empty.');
        }

        return (int)$userId;
    }
}
