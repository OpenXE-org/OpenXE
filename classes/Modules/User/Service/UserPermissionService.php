<?php

namespace Xentral\Modules\User\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\User\Exception\InvalidArgumentException;

final class UserPermissionService
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
     * @param int    $grantingUserId
     * @param string $grantingUserName
     * @param int    $receivingUserId
     * @param string $receivingUserName
     * @param string $module
     * @param string $action
     * @param bool   $permission
     *
     * @return void
     */
    public function log(
        $grantingUserId,
        $grantingUserName,
        $receivingUserId,
        $receivingUserName,
        $module,
        $action,
        $permission
    ) {
        $grantingUserId = $this->ensureUserId($grantingUserId);
        $grantingUserName = $this->ensureNotEmptyString($grantingUserName);
        $receivingUserId = $this->ensureUserId($receivingUserId);
        $receivingUserName = $this->ensureNotEmptyString($receivingUserName);
        $module = $this->ensureNotEmptyString($module);
        $action = $this->ensureNotEmptyString($action);
        $permission = $this->ensureBoolean($permission);
        $permission = $this->transformPermissionToDatabaseValue($permission);

        $this->db->perform(
            'INSERT INTO permissionhistory (
                 `id`, `granting_user_id`, `granting_user_name`,
                 `receiving_user_id`,`receiving_user_name`,`module`,`action`,`permission`
             ) VALUES (
                 NULL, :granting_user_id, :granting_user_name, 
                 :receiving_user_id, :receiving_user_name, :module, :action, :permission
             )',
            [
                'granting_user_id'    => $grantingUserId,
                'granting_user_name'  => $grantingUserName,
                'receiving_user_id'   => $receivingUserId,
                'receiving_user_name' => $receivingUserName,
                'module'              => $module,
                'action'              => $action,
                'permission'          => $permission,
            ]
        );
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
            throw new InvalidArgumentException('Required parameter user ID is empty.');
        }

        return (int)$userId;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function ensureNotEmptyString($value)
    {
        if ($value === '') {
            throw new InvalidArgumentException('Required parameter is empty string.');
        }

        return $value;
    }

    /**
     * @param boolean $value
     *
     * @return boolean
     */
    private function ensureBoolean($value)
    {
        $type = gettype($value);
        if ($type !== 'boolean') {
            throw new InvalidArgumentException(sprintf('Wrong type "%s". Only "boolean" is allowed.', $type));
        }

        return $value;
    }

    /**
     * @param boolean $boolean
     *
     * @return int
     */
    private function transformPermissionToDatabaseValue($boolean)
    {
        return $boolean === true ? 1 : 0;
    }
}
