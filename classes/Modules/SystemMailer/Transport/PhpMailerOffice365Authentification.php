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
        $properties = $this->gateway->getAccountProperties($this->office365Account->getId());
        $emailAddress = $properties->get('email_address');

        if ($this->office365Account === null || $emailAddress === null) {
            throw new OAuthCredentialsException('Office365 OAuth - missing credentials');
        }

        $token = $this->gateway->getAccessToken($this->office365Account->getId());

        if ($token === null) {
            throw new NoAccessTokenException('No access token available');
        }

        if ($token->getTimeToLive() < 30) {
            $token = $this->authorizationService->refreshAccessToken($this->office365Account);
        }

        $offlineToken = $token->getToken();

        return base64_encode(
            sprintf(
                "user=%s\001auth=Bearer %s\001\001",
                $emailAddress,
                $offlineToken
            )
        );
    }
}
