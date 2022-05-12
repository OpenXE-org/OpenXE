<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Transport;

interface PhpMailerOAuthAuthentificationInterface
{
    /**
     * @return string OAuth token
     */
    public function getOauth64():string;
}
