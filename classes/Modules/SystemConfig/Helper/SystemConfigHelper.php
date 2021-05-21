<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemConfig\Helper;

use Xentral\Modules\SystemConfig\Exception\InvalidArgumentException;

final class SystemConfigHelper
{

    /** @var string $delimiter */
    private $delimiter = '__';
    /** @var int $allowedTotalLength */
    private $allowedTotalLength;

    public function __construct()
    {
        $this->setAllowedTotalLength(255 - strlen($this->getDelimiter()));
    }

    /**
     * @param int $allowedLength
     */
    private function setAllowedTotalLength(int $allowedLength): void
    {
        $this->allowedTotalLength = $allowedLength;
    }

    /**
     * @return string
     */
    private function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $namespace
     * @param string $key
     *
     * @return string
     */
    public function getValidatedConfigurationKey($namespace, $key): string
    {
        $this->validateNamespaceAndKey($namespace, $key);

        return $this->getConfigurationKey($namespace, $key);
    }

    /**
     * @param string $namespace
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function validateNamespaceAndKey(string $namespace, string $key): void
    {
        if (empty($namespace)) {
            throw new InvalidArgumentException('Required value "namespace" is empty.');
        }
        if (empty($key)) {
            throw new InvalidArgumentException('Required value "key" is empty.');
        }

        $pattern = '/^_|[^a-z0-9_]|_$/';

        if (preg_match($pattern, $namespace)) {
            $message = 'Value "namespace" contains illegal characters. Valid Pattern: ' . $pattern;
            throw new InvalidArgumentException($message);
        }
        if (preg_match($pattern, $key)) {
            $message = 'Value "key" contains illegal characters. Valid Pattern: ' . $pattern;
            throw new InvalidArgumentException($message);
        }

        if (strlen(strtolower($namespace . $this->getDelimiter() . $key)) > $this->allowedTotalLength) {
            $message = sprintf(
                'Combined length of "namespace" and "key" exceeds the allowed length of %d characters.',
                $this->allowedTotalLength
            );
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * @param string $namespace
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    private function getConfigurationKey($namespace, $key): string
    {
        return strtolower($namespace . $this->getDelimiter() . $key);
    }

    /**
     * @return int
     */
    public function getAllowedTotalLength(): int
    {
        return $this->allowedTotalLength;
    }

}
