<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailer\Transport;

use Xentral\Components\Mailer\Exception\OAuthCredentialsException;
use Xentral\Components\Mailer\Transport\PhpMailerOAuthAuthentificationInterface;
use Xentral\Modules\Office365Api\Data\Office365AccountData;
use Xentral\Modules\Office365Api\Exception\AuthorizationExpiredException;
use Xentral\Modules\Office365Api\Exception\NoAccessTokenException;
use Xentral\Modules\Office365Api\Exception\NoRefreshTokenException;
use Xentral\Modules\Office365Api\Service\Office365AccountGateway;
use Xentral\Modules\Office365Api\Service\Office365AuthorizationService;

final class PhpMailerOffice365Authentification implements PhpMailerOAuthAuthentificationInterface
{
    /** @var Office365AccountGateway */
    private $gateway;

    /** @var Office365AuthorizationService */
    private $authorizationService;

    /** @var Office365AccountData */
    private $office365Account;

    public function __construct(
        Office365AccountGateway $gateway,
        Office365AuthorizationService $authorizationService,
        Office365AccountData $office365Account
    ) {
        $this->gateway = $gateway;
        $this->authorizationService = $authorizationService;
        $this->office365Account = $office365Account;
    }

    public function getOauth64(): string
    {
        try {
            $properties = $this->gateway->getAccountProperties($this->office365Account->getId());
            $emailAddress = $properties->get('email_address');

            if ($this->office365Account === null || $emailAddress === null) {
                error_log("Office365 OAuth: Missing credentials - account: " . ($this->office365Account ? $this->office365Account->getId() : 'null') . ", email: " . ($emailAddress ?? 'null'));
                throw new OAuthCredentialsException('Office365 OAuth - missing credentials');
            }

            $token = $this->gateway->getAccessToken($this->office365Account->getId());

            if ($token === null) {
                error_log("Office365 OAuth: No access token available for account " . $this->office365Account->getId());
                throw new NoAccessTokenException('No access token available');
            }

            error_log("Office365 OAuth: Token TTL = " . $token->getTimeToLive() . " seconds");

            if ($token->getTimeToLive() < 30) {
                error_log("Office365 OAuth: Token expired, refreshing...");
                $token = $this->authorizationService->refreshAccessToken($this->office365Account);
                error_log("Office365 OAuth: Token refreshed");
            }

            $offlineToken = $token->getToken();
            $oauthString = sprintf(
                "user=%s\001auth=Bearer %s\001\001",
                $emailAddress,
                $offlineToken
            );

            error_log("Office365 OAuth: Generated XOAUTH2 string for " . $emailAddress);

            return base64_encode($oauthString);
        } catch (\Exception $e) {
            error_log("Office365 OAuth: Exception in getOauth64(): " . $e->getMessage());
            throw $e;
        }
    }
}
