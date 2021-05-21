<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemConfig\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\SystemConfig\Exception\InvalidArgumentException;
use Xentral\Modules\SystemConfig\Exception\ValueTooLargeException;
use Xentral\Modules\SystemConfig\Gateway\SystemConfigGateway;
use Xentral\Modules\SystemConfig\Helper\SystemConfigHelper;
use Xentral\Modules\SystemConfig\Interfaces\SystemConfigSerializableInterface;

final class SystemConfigService
{
    /** @var Database $db */
    private $db;

    /** @var SystemConfigGateway $gateway */
    private $gateway;

    /** @var SystemConfigHelper $helper */
    private $helper;

    /**
     * @param SystemConfigGateway $gateway
     * @param Database            $database
     * @param SystemConfigHelper  $helper
     */
    public function __construct(
        SystemConfigGateway $gateway,
        Database $database,
        SystemConfigHelper $helper
    ) {
        $this->gateway = $gateway;
        $this->db = $database;
        $this->helper = $helper;
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
        $configurationKey = $this->helper->getValidatedConfigurationKey($namespace, $key);

        $sql = 'DELETE FROM `konfiguration` 
                WHERE `name` = :name';
        $values = [
            'name' => $configurationKey,
        ];

        $this->db->perform($sql, $values);
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
        $sql = 'DELETE FROM `konfiguration` 
                WHERE `name` = :name';
        $values = [
            'name' => $key,
        ];

        $this->db->perform($sql, $values);
    }

    /**
     * @param SystemConfigSerializableInterface $object
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setObject(SystemConfigSerializableInterface $object): void
    {
        $namespace = $object->getSystemConfigNamespace();
        $key = $object->getSystemConfigKey();

        $this->setValue($namespace, $key, serialize($object->toArray()));
    }

    /**
     * @param string $namespace
     * @param string $key
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     * @throws ValueTooLargeException
     *
     * @return void
     */
    public function setValue(string $namespace, string $key, $value): void
    {
        $configurationKey = $this->helper->getValidatedConfigurationKey($namespace, $key);

        $allowedSize = 65536;

        if (strlen($value) > $allowedSize) {
            throw new ValueTooLargeException('Value to be saved is too large. Maximum allowed Size: ' . $allowedSize);
        }

        if (!$this->gateway->isKeyExisting($namespace, $key)) {
            $this->createConfigEntry($namespace, $key);
        }

        $sql = 'UPDATE `konfiguration` 
                SET `wert` = :value 
                WHERE  `name` = :name';
        $values = [
            'name'  => $configurationKey,
            'value' => (string)$value,
        ];

        $this->db->perform($sql, $values);
    }

    /**
     * @param string $namespace
     * @param string $key
     *
     * @return void
     */
    private function createConfigEntry(string $namespace, string $key): void
    {
        $configurationKey = $this->helper->getValidatedConfigurationKey($namespace, $key);
        $sql = 'INSERT INTO `konfiguration` (`name`,  `wert`, `firma`, `adresse`) 
                    VALUES (:name, :value, 0, 0)';
        $values = [
            'name'  => $configurationKey,
            'value' => '',
        ];

        $this->db->perform($sql, $values);
    }
}
