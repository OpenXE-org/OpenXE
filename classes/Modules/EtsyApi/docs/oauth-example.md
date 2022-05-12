# Etsy-OAuth

## Beispiel

```php
<?php

use Xentral\Modules\EtsyApi\Credential\ClientCredentialData;
use Xentral\Modules\EtsyApi\Credential\TemporaryCredentialData;
use Xentral\Modules\EtsyApi\Credential\TokenCredentialData;
use Xentral\Modules\EtsyApi\EtsyOAuthHelper;

$clientCredentials = new ClientCredentialData('identifier', 'xxx_secret_xxx');
$callbackUrl = 'http://example.com/somefile';
$scopes = ['listings_r', 'listings_w'];

$etsyHelper = new EtsyOAuthHelper($clientCredentials, $callbackUrl, $scopes);

session_start();

if (isset($_GET['user'])) {

    /*
     * SCHRITT 3
     */

    if (!isset($_SESSION['token_credentials'])) {
      die('No token credentials.');
    }

    // Token-Credentials aus Session wiederherstellen
    $tokenCredentials = TokenCredentialData::fromString($_SESSION['token_credentials']);
    
    $method = 'PUT';
    $apiUrl = 'https://openapi.etsy.com/v2/listings/12345678/inventory';
    $data = [
      'listing_id' => 12345678,
      'products' => '[{"product_id":98765431,"sku":"700001","property_values":[],"offerings":[{"offering_id":999888777,"price":{"amount":7900,"divisor":100,"currency_code":"EUR","currency_formatted_short":"\u20ac79.00","currency_formatted_long":"\u20ac79.00 EUR","currency_formatted_raw":"79.00"},"quantity":1,"is_enabled":1,"is_deleted":0}],"is_deleted":0}]',
    ];
    
    $headers = $etsyHelper->getHeaders($tokenCredentials, $method, $apiUrl, $data);
    $client = new GuzzleHttp\Client();
    $response = $client->put($apiUrl, [
      'headers' => $headers,
      'form_params' => $data,
    ]);
    
    if ($response->getStatusCode() === 200) {
        die('Erfolg');
    }

} elseif (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) {

    /*
     * SCHRITT 2
     */

    if (!isset($_SESSION['temporary_credentials'])) {
      die('No temporary credentials.');
    }

    // Temporary-Credentials aus Schritt 1 wiederherstellen
    $temporaryCredentials = TemporaryCredentialData::fromString($_SESSION['temporary_credentials']);

    // Teil 3 der OAuth 1.0 Authentifizierung: Token-Credentials (früher Access-Tokens) abholen
    $tokenCredentials = $etsyHelper->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);

    // Temporary-Credentials löschen und Token-Credentials in Session speichern
    unset($_SESSION['temporary_credentials']);
    $_SESSION['token_credentials'] = $tokenCredentials->toString();
    session_write_close();

    // Benutzer zu Schritt 3 umleiten
    header('Location: http://example.com/somefile?user=user');
    exit;

} else {

    /*
     * SCHRITT 1
     */

    // Teil 1 der OAuth 1.0 Authentifizierung: Temporary-Credentials abholen
    // Diese identifizieren uns als Client beim Server
    $temporaryCredentials = $etsyHelper->getTemporaryCredentials();

    // Credentials in Session speichern; für übernächsten Schritt
    $_SESSION['temporary_credentials'] = $temporaryCredentials->toString();
    session_write_close();

    // Teil 2 der OAuth 1.0 Authentifizierung: Resource Owner auf die Login-Seite umleiten
    $redirectUrl = $etsyHelper->getAuthorizationUrl($temporaryCredentials);
    header('Location: ' . $redirectUrl);
    exit;
}
```
