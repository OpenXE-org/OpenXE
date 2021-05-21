<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleApi\Data;

use Xentral\Modules\GoogleApi\Exception\GoogleCredentialsException;

final class GoogleCredentialsData
{
    /** @var string|null $clientId */
    private $clientId;

    /** @var string|null $clientSecret */
    private $clientSecret;

    /** @var string|null $redirectUri */
    private $redirectUri;

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     */
    public function __construct(
        ?string $clientId,
        ?string $clientSecret,
        ?string $redirectUri
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return string|null
     */
    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    /**
     * @return string|null
     */
    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    /**
     * @return string|null
     */
    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    /**
     * @throws GoogleCredentialsException
     *
     * @return void
     */
    public function validate(): void
    {
        if (empty($this->getClientId())) {
            throw new GoogleCredentialsException('Google client-id not set.');
        }
        if (empty($this->getClientSecret())) {
            throw new GoogleCredentialsException('Google client secret not set.');
        }
    }
}
