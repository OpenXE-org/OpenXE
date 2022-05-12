<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Config;

use Xentral\Components\Mailer\Exception\MailerConfigException;

interface MailerConfigInterface
{
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null @todo: try to use type annotation
     */
    public function getConfigValue(string $key, $default = null);

    /**
     * @throws MailerConfigException
     *
     * @return void
     */
    public function validate(): void;

    /**
     * @return array
     */
    public function getValues(): array;
}
