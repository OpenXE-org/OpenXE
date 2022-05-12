<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Data;

final class AccountCredentialsData
{
    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;
    /** @var string */
    private $redirectUrl;

    /**
     * AccountCredentialsData constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUrl
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     *
     * @return AccountCredentialsData
     */
    public function setClientId(string $clientId): AccountCredentialsData
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     *
     * @return AccountCredentialsData
     */
    public function setClientSecret(string $clientSecret): AccountCredentialsData
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     *
     * @return AccountCredentialsData
     */
    public function setRedirectUrl(string $redirectUrl): AccountCredentialsData
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }
}
