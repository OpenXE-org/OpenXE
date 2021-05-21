<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Service;

use Xentral\Modules\GoogleApi\Data\GoogleCredentialsData;
use Xentral\Modules\GoogleApi\Wrapper\CompanyConfigWrapper;

final class GoogleCredentialsService implements GoogleCredentialsServiceInterface
{
    /** @var CompanyConfigWrapper $config */
    private $config;

    public function __construct(CompanyConfigWrapper $config)
    {
        $this->config = $config;
    }

    /**
     * @return GoogleCredentialsData
     */
    public function getCredentials(): GoogleCredentialsData
    {
        $clientID = $this->config->get(self::KEY_CLIENT_ID);
        $secret = $this->config->get(self::KEY_CLIENT_SECRET);
        $uri = $this->config->get(self::KEY_REDIRECT_URI);

        return new GoogleCredentialsData($clientID, $secret, $uri);
    }

    /**
     * @return bool
     */
    public function existCredentials(): bool
    {
        $clientID = $this->config->get(self::KEY_CLIENT_ID);
        $secret = $this->config->get(self::KEY_CLIENT_SECRET);

        return (is_string($clientID) && $clientID !== '') && (is_string($secret) && $secret !== '');
    }

    /**
     * @param GoogleCredentialsData $account
     *
     * @return void
     */
    public function saveCredentials(GoogleCredentialsData $account): void
    {
        $this->config->set(self::KEY_CLIENT_ID, $account->getClientId());
        $this->config->set(self::KEY_CLIENT_SECRET, $account->getClientSecret());
        $this->config->set(self::KEY_REDIRECT_URI, $account->getRedirectUri());
    }

    /**
     * @return void
     */
    public function deleteCredentials(): void
    {
        $this->config->set(self::KEY_CLIENT_ID, null);
        $this->config->set(self::KEY_CLIENT_SECRET, null);
        $this->config->set(self::KEY_REDIRECT_URI, null);
    }
}
