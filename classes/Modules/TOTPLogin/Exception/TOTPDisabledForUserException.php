<?php

namespace Xentral\Modules\TOTPLogin\Exception;

class TOTPDisabledForUserException extends \RuntimeException implements TOTPLoginExceptionInterface
{
    /**
     * @param int $userId
     *
     * @return TOTPDisabledForUserException
     */
    public static function fromUserId($userId)
    {
        return new TOTPDisabledForUserException("TOTP login is disabled for user ID {$userId}");
    }
}
