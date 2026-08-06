<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Service;

use Xentral\Modules\Office365Api\Data\Office365CredentialsData;
use Xentral\Modules\Office365Api\Exception\Office365OAuthException;
use Xentral\Modules\Office365Api\Wrapper\CompanyConfigWrapper;

final class Office365CredentialsService
{
    /** @var CompanyConfigWrapper */
    private $configWrapper;

    public function __construct(CompanyConfigWrapper $configWrapper)
    {
        $this->configWrapper = $configWrapper;
    }

    public function getCredentials(): Office365CredentialsData
    {
        $clientId = $this->configWrapper->get('office365_client_id');
        $clientSecret = $this->configWrapper->get('office365_client_secret');
        $redirectUri = $this->configWrapper->get('office365_redirect_uri');
        $tenantId = $this->configWrapper->get('office365_tenant_id');

        $credentials = new Office365CredentialsData(
            $clientId ?? '',
            $clientSecret ?? '',
            $redirectUri ?? '',
            $tenantId ?? ''
        );

        $credentials->validate();

        return $credentials;
    }

    public function existCredentials(): bool
    {
        $clientId = $this->configWrapper->get('office365_client_id');
        $clientSecret = $this->configWrapper->get('office365_client_secret');
        $tenantId = $this->configWrapper->get('office365_tenant_id');

        return !empty($clientId) && !empty($clientSecret) && !empty($tenantId);
    }

    public function saveCredentials(Office365CredentialsData $credentials): void
    {
        $this->configWrapper->set('office365_client_id', $credentials->getClientId());
        $this->configWrapper->set('office365_client_secret', $credentials->getClientSecret());
        $this->configWrapper->set('office365_redirect_uri', $credentials->getRedirectUri());
        $this->configWrapper->set('office365_tenant_id', $credentials->getTenantId());
    }

    public function deleteCredentials(): void
    {
        $this->configWrapper->delete('office365_client_id');
        $this->configWrapper->delete('office365_client_secret');
        $this->configWrapper->delete('office365_redirect_uri');
        $this->configWrapper->delete('office365_tenant_id');
    }
}
