<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Data;

use Xentral\Modules\Office365Api\Exception\Office365OAuthException;

final class Office365CredentialsData
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $redirectUri;

    /** @var string */
    private $tenantId;

    public function __construct(string $clientId, string $clientSecret, string $redirectUri, string $tenantId)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->tenantId = $tenantId;
    }

    public function validate(): void
    {
        if (empty($this->clientId)) {
            throw new Office365OAuthException('Client ID is required');
        }
        if (empty($this->clientSecret)) {
            throw new Office365OAuthException('Client Secret is required');
        }
        if (empty($this->redirectUri)) {
            throw new Office365OAuthException('Redirect URI is required');
        }
        if (empty($this->tenantId)) {
            throw new Office365OAuthException('Tenant ID is required');
        }
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }
}
