<?php

namespace Xentral\Modules\AmazonVendorDF\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use Xentral\Modules\AmazonVendorDF\Data\Token;
use Xentral\Modules\AmazonVendorDF\Exception\IssueTokenException;

class TokenService
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function requestToken(string $refreshToken, string $clientId, string $clientSecret)
    {
        try {
            $response = $this->client->request(
                'POST',
                'https://api.amazon.com/auth/o2/token',
                [
                    'form_params' => [
                        'grant_type'    => 'refresh_token',
                        'refresh_token' => $refreshToken,
                        'client_id'     => $clientId,
                        'client_secret' => $clientSecret,
                    ],
                ]
            );
        }catch (BadResponseException $badResponseException){
            throw IssueTokenException::fromResponse($badResponseException->getResponse());
        }

        return Token::fromResponse($response);
    }
}
