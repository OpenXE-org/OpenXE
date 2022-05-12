<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient;

use Xentral\Components\MailClient\Client\ImapMailClient;
use Xentral\Components\MailClient\Config\ImapMailClientConfigInterface;

final class MailClientFactory
{
    /**
     * @param ImapMailClientConfigInterface $config
     *
     * @return ImapMailClient
     */
    public function createImapClient(ImapMailClientConfigInterface $config): ImapMailClient
    {
        return new ImapMailClient($config);
    }
}
