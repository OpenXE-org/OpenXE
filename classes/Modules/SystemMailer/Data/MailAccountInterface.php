<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailer\Data;

interface MailAccountInterface
{
    /** @var string TYPE_SMTP */
    const TYPE_SMTP = 'smtp';

    /** @var string TYPE_IMAP */
    const TYPE_IMAP = 'imap';

    /** @var string TYPE_GOOGLE */
    const TYPE_GOOGLE = 'oauth_google';

    /**
     * @return string
     */
    public function getType():string;

    /**
     * @return string
     */
    public function getServerAddress():string;

    /**
     * @return string
     */
    public function getClientAlias():string;

    /**
     * @return string
     */
    public function getSenderEmailAddress():string;

    /**
     * @return string
     */
    public function getSenderName():string;

    /**
     * @return string
     */
    public function getUserName():string;
}
