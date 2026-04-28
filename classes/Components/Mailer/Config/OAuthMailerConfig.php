<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Config;

use PHPMailer\PHPMailer\PHPMailer;
use Xentral\Components\Mailer\Exception\MailerConfigException;

final class OAuthMailerConfig extends AbstractMailerConfig
{
    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct($this->getDefaults());
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        if (!array_key_exists('sender_email', $this->data) || $this->data['sender_email'] === '') {
            throw new MailerConfigException('Key "sender_email" is required!');
        }
        if (!array_key_exists('host', $this->data) || $this->data['host'] === '') {
            throw new MailerConfigException('Key "host" is required!');
        }
        if (!array_key_exists('smtp_security', $this->data)) {
            throw new MailerConfigException('Key "smtp_security" is required!');
        }
    }

    /**
     * @return array
     */
    private function getDefaults(): array
    {
        return [
            'priority'             => 3,
            'charset'              => PHPMailer::CHARSET_UTF8,
            'contenttype'          => 'text/html',
            'encoding'             => '8bit',
            'sender_name'          => '',
            'wordwrap'             => 0,
            'mailer'               => 'smtp',
            'hostname'             => '',
            'port'                 => 25,
            'helo'                 => '',
            'timeout'              => 30,
            'singleto'             => false,
            'sendmail'             => '/usr/sbin/sendmail',
            'smtp_enabled'         => true,
            'smtp_autotls_enabled' => false,
            'smtp_options'         => [],
            'smtp_debug'           => 0,
            'smtp_keepalive'       => false,
            'auth_type'            => 'XOAUTH2',
        ];
    }
}
