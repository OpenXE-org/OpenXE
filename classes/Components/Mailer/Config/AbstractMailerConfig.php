<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Config;

abstract class AbstractMailerConfig implements MailerConfigInterface
{
    /** @var array $data */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function getConfigValue(string $key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
    {
        return $this->data;
    }
}
