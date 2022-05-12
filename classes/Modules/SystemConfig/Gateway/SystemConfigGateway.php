<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemConfig\Gateway;

use Exception;
use TypeError;
use Xentral\Components\Database\Database;
use Xentral\Modules\SystemConfig\Exception\ConfigurationKeyNotFoundException;
use Xentral\Modules\SystemConfig\Exception\InvalidArgumentException;
use Xentral\Modules\SystemConfig\Exception\SystemConfigClassCreationFailed;
use Xentral\Modules\SystemConfig\Exception\SystemConfigClassTypeError;
use Xentral\Modules\SystemConfig\Helper\SystemConfigHelper;
use Xentral\Modules\SystemConfig\Interfaces\SystemConfigSerializableInterface;

final class SystemConfigGateway
{
    /** @var Database $db */
    private $db;

    /** @var SystemConfigHelper $helper */
    private $helper;

    /**
     * @param Database           $database
     * @param SystemConfigHelper $helper
     */
    public function __construct(
        Database $database,
        SystemConfigHelper $helper
    ) {
        $this->db = $database;
        $this->helper = $helper;
    }

    /**
     * @param string $namespace
     * @param string $key
     * @param mixed  $default
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function tryGetValue(string $namespace, string $key, string $default = null): ?string
    {
        $configurationKey = $this->helper->getValidatedConfigurationKey($namespace, $key);

        $result = $this->fetchValueFromDatabase($configurationKey);

        if ($result === false) {
            if(is_null($default)){
                return null;
            }
            return (string)$default;
        }

        return (string)$result;
    }

    /**
     * @param string $configurationKey
     *
     * @return false|string
     */
    protected function fetchValueFromDatabase($configurationKey)
    {
        $sql = 'SELECT `wert`
                FROM `konfiguration` 
                WHERE `name` = :name LIMIT 1';
        $values = [
            'name' => $configurationKey,
        ];

        return $this->db->fetchValue($sql, $values);
    }

    /**
     * @param string $namespace
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function isKeyExisting(string $namespace, string $key): bool
    {
        $configurationKey = $this->helper->getValidatedConfigurationKey($namespace, $key);

        $sql = 'SELECT `name` 
                FROM `konfiguration` 
                WHERE `name` = :name';
        $values = [
            'name' => $configurationKey,
        ];

        return !empty($this->db->fetchValue($sql, $values));
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
        if (!in_array(SystemConfigSerializableInterface::class, class_implements($class), true)) {
            $message = sprintf('Class %s does not implement %s', $class, SystemConfigSerializableInterface::class);
            throw new InvalidArgumentException($message);
        }

        /** @var SystemConfigSerializableInterface $class */
        $namespace = $class::getSystemConfigNamespace();
        $key = $class::getSystemConfigKey();

        $result = $this->getValue($namespace, $key);

        try {
            return $class::fromArray(unserialize($result, ['allowed_classes' => false]));
        } catch (Exception $exception) {
            $message = 'Was not able to create object for class ' . $class;
            throw new SystemConfigClassCreationFailed($message, $exception->getCode(), $exception);
        } catch (TypeError $error) {
            throw new SystemConfigClassTypeError($error->getMessage(), $error->getCode(), $error);
        }
    }

    /**
     * @param string $namespace
     * @param string $key
     *
     * @throws InvalidArgumentException
     * @throws ConfigurationKeyNotFoundException
     *
     * @return string
     */
    public function getValue(string $namespace, string $key): string
    {
        $configurationKey = $this->helper->getValidatedConfigurationKey($namespace, $key);

        $result = $this->fetchValueFromDatabase($configurationKey);

        if ($result === false) {
            $message = sprintf(
                'Key "%s" was not found in namespace "%s".',
                $key,
                $namespace
            );
            throw new ConfigurationKeyNotFoundException($message);
        }

        return (string)$result;
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
        $result = $this->fetchValueFromDatabase($key);
        if($result === false){
            return null;
        }
        return (string)$this->fetchValueFromDatabase($key);
    }

}
