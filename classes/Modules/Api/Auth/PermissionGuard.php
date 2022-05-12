<?php

declare(strict_types=1);

namespace Xentral\Modules\Api\Auth;

use Xentral\Components\Database\Database;
use Xentral\Modules\Api\Error\ApiError;
use Xentral\Modules\Api\Exception\AuthorizationErrorException;

class PermissionGuard
{
    /** @var Database */
    private $database;

    /** @var int */
    private $apiAccountId;

    /**
     * PermissionGuard constructor.
     *
     * @param Database $database
     * @param int      $apiAccountId
     */
    public function __construct(Database $database, int $apiAccountId)
    {
        $this->database = $database;
        $this->apiAccountId = $apiAccountId;
    }

    /**
     * @param string $neededPermission
     */
    public function check(string $neededPermission): void
    {
        $permissions = $this->getApiAccountPermissions();

        $hasPermission = in_array($neededPermission, $permissions);

        if (!$hasPermission) {
            throw new AuthorizationErrorException(
                'Api account has not needed permissions',
                ApiError::CODE_API_ACCOUNT_PERMISSION_MISSING
            );
        }
    }

    /**
     * @param string $action
     *
     * @return void
     */
    public function checkStandardApiAction(string $action): void
    {
        $neededPermission = 'standard_' . strtolower($action);
        $this->check($neededPermission);
    }

    /**
     * @return array
     */
    private function getApiAccountPermissions(): array
    {
        $jsonEncodedPermissions = $this->database->fetchValue(
            'SELECT `permissions` FROM `api_account` WHERE `id` = :api_account_id',
            ['api_account_id' => $this->apiAccountId]
        );

        if( $jsonEncodedPermissions === null ) {
            return [];
        }

        $permissions = json_decode($jsonEncodedPermissions, true);

        return is_array($permissions)
            ? $permissions
            : [];
    }
}
