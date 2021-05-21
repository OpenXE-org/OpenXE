<?php

declare(strict_types=1);

namespace Xentral\Modules\Postat\SOAP;

class Config
{
    private $clientid;

    private $orgunitid;

    private $orgunitguid;

    private $soapurl;

    /**
     * Config constructor.
     *
     * @param array $config
     *
     * @throws PostAtException
     */
    public function __construct(array $config)
    {
        $requiredSettings = [
            'soapurl',
            'clientid',
            'orgunitid',
            'orgunitguid',
        ];

        foreach ($requiredSettings as $setting) {
            if (empty($config[$setting])) {
                throw new PostAtException(
                    'Configuration of the Post.at module is invalid. Please verify the configuration.'
                );
            }

            $this->$setting = $config[$setting];
        }
    }

    /**
     * @return string The URL of the SOAP API endpoint.
     */
    public function getSoapUrl(): string
    {
        return $this->soapurl;
    }

    /**
     * Get ClientId (a.k.a DebitorID).
     *
     * @return int
     */
    public function getClientId(): int
    {
        return (int) $this->clientid;
    }

    /**
     * Get OrganisationID; unique for a customerID.
     *
     * @return int
     */
    public function getOrgUnitId(): int
    {
        return (int) $this->orgunitid;
    }

    /**
     * Get unique GUID of the customerID.
     *
     * @return string
     */
    public function getOrgUnitGuid(): string
    {
        return $this->orgunitguid;
    }
}
