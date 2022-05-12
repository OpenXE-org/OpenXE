<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailer\Transport;

use Xentral\Components\Mailer\Exception\OAuthCredentialsException;
use Xentral\Components\Mailer\Transport\PhpMailerOAuthAuthentificationInterface;
use Xentral\Modules\GoogleApi\Data\GoogleAccountData;
use Xentral\Modules\GoogleApi\Exception\AuthorizationExpiredException;
use Xentral\Modules\GoogleApi\Exception\NoAccessTokenException;
use Xentral\Modules\GoogleApi\Exception\NoRefreshTokenException;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleApi\Service\GoogleAuthorizationService;

final class PhpMailerGoogleAuthentification implements PhpMailerOAuthAuthentificationInterface
{
    /** @var GoogleAccountGateway */
    private $gateway;

    /** @var GoogleAuthorizationService */
    private $authorizationService;

    /** @var GoogleAccountData  */
    private $googleAccount;

    /**
     * @param GoogleAccountGateway       $gateway
     * @param GoogleAuthorizationService $authorizationService
     * @param GoogleAccountData          $googleAccount
     */
    public function __construct(
        GoogleAccountGateway $gateway,
        GoogleAuthorizationService $authorizationService,
        GoogleAccountData $googleAccount
    )
    {
        $this->gateway = $gateway;
        $this->authorizationService = $authorizationService;
        $this->googleAccount = $googleAccount;
    }

    /**
     * GetOauth64
     *
     * encode the user email related to this request along with the token in base64
     * this is used for authentication, in the phpmailer smtp class
     *
     * @throws OAuthCredentialsException
     * @throws NoAccessTokenException
     * @throws NoRefreshTokenException
     * @throws AuthorizationExpiredException
     *
     * @return string
     */
    public function getOauth64():string
    {
        $gmailAddress = $this->gateway->getAccountProperties($this->googleAccount->getId())
            ->get('gmail_address');
        if ($this->googleAccount === null || $gmailAddress === null) {
            throw new OAuthCredentialsException('SMTP Google OAuth - missing credentials');
        }
        $token = $this->gateway->getAccessToken($this->googleAccount->getId());
        if ($token->getTimeToLive() < 30) {
            $token = $this->authorizationService->refreshAccessToken($this->googleAccount);
        }
        $offlineToken = $token->getToken();

        return base64_encode(
            sprintf(
                "user=%s\001auth=Bearer %s\001\001",
                $gmailAddress,
                $offlineToken
            )
        );
    }
}
