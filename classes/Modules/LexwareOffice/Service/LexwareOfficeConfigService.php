<?php

declare(strict_types=1);

namespace Xentral\Modules\LexwareOffice\Service;

use Xentral\Modules\LexwareOffice\Exception\LexwareOfficeException;
use Xentral\Modules\SystemConfig\SystemConfigModule;

final class LexwareOfficeConfigService
{
    private const NAMESPACE = 'lexwareoffice';
    private const KEY_API = 'api_key';
    private const KEY_SALT = 'encryption_salt';

    public function __construct(private SystemConfigModule $config)
    {
    }

    public function saveApiKey(string $apiKey): void
    {
        $apiKey = trim($apiKey);
        if ($apiKey === '') {
            throw new LexwareOfficeException('Der Lexware Office API-Key darf nicht leer sein.');
        }

        $salt = $this->getOrCreateSalt();
        $encrypted = $this->encrypt($apiKey, $salt);
        $this->config->setValue(self::NAMESPACE, self::KEY_API, $encrypted);
    }

    public function getApiKey(): ?string
    {
        $encrypted = $this->config->tryGetValue(self::NAMESPACE, self::KEY_API);
        if (empty($encrypted)) {
            return null;
        }

        $salt = $this->config->tryGetValue(self::NAMESPACE, self::KEY_SALT);
        if (empty($salt)) {
            return null;
        }

        return $this->decrypt($encrypted, $salt);
    }

    public function hasApiKey(): bool
    {
        return $this->config->isKeyExisting(self::NAMESPACE, self::KEY_API);
    }

    private function encrypt(string $value, string $salt): string
    {
        $cipher = 'AES-256-CBC';
        $key = hash('sha256', $salt, true);
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivLength);

        $ciphertext = openssl_encrypt($value, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) {
            throw new LexwareOfficeException('API-Key konnte nicht verschlüsselt werden.');
        }

        $hmac = hash_hmac('sha256', $ciphertext, $key, true);

        return base64_encode($iv . $hmac . $ciphertext);
    }

    private function decrypt(string $encoded, string $salt): ?string
    {
        $cipher = 'AES-256-CBC';
        $payload = base64_decode($encoded, true);
        if ($payload === false) {
            return null;
        }

        $ivLength = openssl_cipher_iv_length($cipher);
        $key = hash('sha256', $salt, true);

        $iv = substr($payload, 0, $ivLength);
        $hmac = substr($payload, $ivLength, 32);
        $ciphertext = substr($payload, $ivLength + 32);

        if ($iv === false || $hmac === false || $ciphertext === false) {
            return null;
        }

        $calcHmac = hash_hmac('sha256', $ciphertext, $key, true);
        if (!hash_equals($hmac, $calcHmac)) {
            return null;
        }

        $plain = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        return $plain === false ? null : $plain;
    }

    private function getOrCreateSalt(): string
    {
        $salt = $this->config->tryGetValue(self::NAMESPACE, self::KEY_SALT);
        if (!empty($salt)) {
            return $salt;
        }

        $salt = bin2hex(random_bytes(32));
        $this->config->setValue(self::NAMESPACE, self::KEY_SALT, $salt);

        return $salt;
    }
}
