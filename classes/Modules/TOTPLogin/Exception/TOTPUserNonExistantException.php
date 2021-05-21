<?php

namespace Xentral\Modules\TOTPLogin\Exception;

class TOTPUserNonExistantException extends \RuntimeException implements TOTPLoginExceptionInterface
{
    /**
     * @param int $userId
     *
     * @return TOTPUserNonExistantException
     */
    public static function fromUserId($userId)
    {
        return new TOTPUserNonExistantException("User ID {$userId} is not existent");
    }
}
