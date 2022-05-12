<?php

namespace Xentral\Components\EnvironmentConfig;

final class EnvironmentConfig
{
    /**
     * @var string $databaseHost
     */
    private $databaseHost;

    /**
     * @var string $databaseName
     */
    private $databaseName;

    /**
     * @var string $databaseUser
     */
    private $databaseUser;

    /**
     * @var string $databasePassword
     */
    private $databasePassword;

    /**
     * @var int $databasePort
     */
    private $databasePort;

    /**
     * @var string $userdataDirectoryPath
     */
    private $userdataDirectoryPath;

    /**
     * @var array|null $ioncubeSystemInformation
     */
    private $ioncubeSystemInformation;

    /**
     * @param string $databaseHost
     * @param string $databaseName
     * @param string $databaseUser
     * @param string $databasePassword
     * @param int    $databasePort
     * @param string $userdataDirectoryPath
     * @param array  $ioncubeSystemInformation
     */
    public function __construct(
        string $databaseHost,
        string $databaseName,
        string $databaseUser,
        string $databasePassword,
        int $databasePort,
        string $userdataDirectoryPath,
        ?array $ioncubeSystemInformation
    ) {
        $this->databaseHost = $databaseHost;
        $this->databaseName = $databaseName;
        $this->databaseUser = $databaseUser;
        $this->databasePassword = $databasePassword;
        $this->databasePort = $databasePort;
        $this->userdataDirectoryPath = $userdataDirectoryPath;
        $this->ioncubeSystemInformation = $ioncubeSystemInformation;
    }

    /**
     * @return string
     */
    public function getDatabaseHost(): string
    {
        return $this->databaseHost;
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function getDatabaseUser(): string
    {
        return $this->databaseUser;
    }

    /**
     * @return string
     */
    public function getDatabasePassword(): string
    {
        return $this->databasePassword;
    }

    /**
     * @return int
     */
    public function getDatabasePort(): int
    {
        return $this->databasePort;
    }

    /**
     * @return string
     */
    public function getUserdataDirectoryPath(): string
    {
        return $this->userdataDirectoryPath;
    }

    /**
     * @return ?array
     */
    public function getIoncubeSystemInformation(): ?array
    {
        return $this->ioncubeSystemInformation;
    }

    /**
     * @return bool
     */
    public function isSystemHostedOnCloud(): bool
    {
        return !empty($this->ioncubeSystemInformation['iscloud']['value']);
    }

    /**
     * @return bool
     */
    public function isSystemFlaggedAsDevelopmentVersion(): bool
    {
        return !empty($this->ioncubeSystemInformation['isdevelopmentversion']['value']);
    }

    /**
     * @return bool
     */
    public function isSystemFlaggedAsTestVersion(): bool
    {
        return !empty($this->ioncubeSystemInformation['testlizenz']['value']);
    }

    /**
     * @return int
     */
    public function getMaxUser(): int
    {
        if (!isset($this->ioncubeSystemInformation['maxuser']['value'])) {
            return 0;
        }

        return (int)$this->ioncubeSystemInformation['maxuser']['value'];
    }

    /**
     * @return int
     */
    public function getMaxLightUser(): int
    {
        if (!isset($this->ioncubeSystemInformation['maxlightuser']['value'])) {
            return 0;
        }

        return (int)$this->ioncubeSystemInformation['maxlightuser']['value'];
    }

    /**
     * @return int|null
     */
    public function getExpirationTimeStamp(): ?int
    {
        if (!isset($this->ioncubeSystemInformation['expdate']['value'])) {
            return 0;
        }

        return (int)$this->ioncubeSystemInformation['expdate']['value'];
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getValueOfSpecificIoncubeSystemInformation(string $name): ?string
    {
        if ($this->ioncubeSystemInformation === null) {
            return null;
        }

        if (array_key_exists($name, $this->ioncubeSystemInformation)) {
            return $this->ioncubeSystemInformation[$name]['value'];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getSystemFallbackEmailAddresses(): array
    {
        $emailAddresses = [];
        $mailAddressSelfBuyCustomer = (string)$this->getValueOfSpecificIoncubeSystemInformation('buyemail');
        if ($mailAddressSelfBuyCustomer !== '') {
            $emailAddresses[] = $mailAddressSelfBuyCustomer;
        }

        $mailAddressCustomerLicence = (string)$this->getValueOfSpecificIoncubeSystemInformation('emaillicence');
        if ($mailAddressCustomerLicence !== ''
            && strpos($mailAddressCustomerLicence, '@') !== false
            && strpos($mailAddressCustomerLicence, '@xentral.com') === false
            && strpos($mailAddressCustomerLicence, '@xentral.biz') === false) {
            $emailAddresses[] = $mailAddressCustomerLicence;
        }

        //in old licences email-address of customer can be insert in name instead email
        $nameCustomerLicence = (string)$this->getValueOfSpecificIoncubeSystemInformation('namelicence');
        if ($nameCustomerLicence !== ''
            && strpos($nameCustomerLicence, '@') !== false
            && strpos($nameCustomerLicence, '@xentral.com') === false
            && strpos($nameCustomerLicence, '@xentral.biz') === false) {
            $emailAddresses[] = $nameCustomerLicence;
        }

        return $emailAddresses;
    }
}
