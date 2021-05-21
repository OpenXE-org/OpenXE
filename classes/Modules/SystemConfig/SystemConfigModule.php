<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemConfig;

use Xentral\Modules\SystemConfig\Exception\ConfigurationKeyNotFoundException;
use Xentral\Modules\SystemConfig\Exception\InvalidArgumentException;
use Xentral\Modules\SystemConfig\Exception\ValueTooLargeException;
use Xentral\Modules\SystemConfig\Gateway\SystemConfigGateway;
use Xentral\Modules\SystemConfig\Interfaces\SystemConfigSerializableInterface;
use Xentral\Modules\SystemConfig\Service\SystemConfigService;

final class SystemConfigModule
{
    /** @var SystemConfigGateway $gateway */
    private $gateway;

    /** @var SystemConfigService $service */
    private $service;

    /**
     * @param SystemConfigService $service
     * @param SystemConfigGateway $gateway
     */
    public function __construct(SystemConfigService $service, SystemConfigGateway $gateway)
    {
        $this->service = $service;
        $this->gateway = $gateway;
    }

    /**
     * @param string $namespace
     * @param string $key
     * @param string $value
     *
     * @throws InvalidArgumentException
     * @throws ValueTooLargeException
     *
     * @return void
     */
    public function setValue(string $namespace, string $key, string $value): void
    {
        $this->service->setValue($namespace, $key, $value);
    }

    /**
     * @param SystemConfigSerializableInterface $object
     *
     * @throws InvalidArgumentException
     * @throws ValueTooLargeException
     *
     * @return void
     */
    public function setObject(SystemConfigSerializableInterface $object): void
    {
        $this->service->setObject($object);
    }

    /**
     * @param string $namespace
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function deleteKey(string $namespace, string $key): void
    {
        $this->service->deleteKey($namespace, $key);
    }

    /**
     * @deprecated Used to delete legacy configuration data from the database <br>
     * Ideally this function should only be used to migrate data in combination with the getLegacyValue() function
     *
     * @param string $key
     *
     * @return void
     */
    public function deleteLegacyKey(string $key): void
    {
        $this->service->deleteLegacyKey($key);
    }

    /**
     * @param string $class
     *
     * @throws InvalidArgumentException
     * @throws ConfigurationKeyNotFoundException
     *
     * @return object|SystemConfigSerializableInterface
     */
    public function getObject(string $class)
    {
        return $this->gateway->getObject($class);
    }

    /**
     * @param string $namespace
     * @param string $key
     *
     *
     * @throws InvalidArgumentException
     * @throws ConfigurationKeyNotFoundException
     * @return string
     */
    public function getValue(string $namespace, string $key): string
    {
        return $this->gateway->getValue($namespace, $key);
    }

    /**
     * @param string $namespace
     * @param string $key
     * @param mixed $default
     *
     * @throws InvalidArgumentException
     *
     * @return string|null
     */
    public function tryGetValue(string $namespace, string $key, string $default = null): ?string
    {
        return $this->gateway->tryGetValue($namespace, $key, $default);
    }

    /**
     * @deprecated Used to get legacy configuration data from the database <br>
     * Ideally this function should only be used to migrate data in combination with the deleteLegacyKey() function
     *
     * @param string $key
     *
     * @return string|null
     */
    public function tryGetLegacyValue(string $key): ?string
    {
        return $this->gateway->tryGetLegacyValue($key);
    }

    /**
     * @param string $namespace
     * @param string $key
     *
     * @throws InvalidArgumentException
     * @return bool
     */
    public function isKeyExisting(string $namespace, string $key): bool
    {
        return $this->gateway->isKeyExisting($namespace, $key);
    }

}
