# fiskaly SDK for PHP

The fiskaly SDK includes an HTTP client that is needed<sup>[1](#fn1)</sup> for accessing the [kassensichv.io](https://kassensichv.io) API that implements a cloud-based, virtual **CTSS** (Certified Technical Security System) / **TSE** (Technische Sicherheitseinrichtung) as defined by the German **KassenSichV** ([Kassen­sich­er­ungsver­ord­nung](https://www.bundesfinanzministerium.de/Content/DE/Downloads/Gesetze/2017-10-06-KassenSichV.pdf)).

## Supported Versions

* PHP 7.1+

## Features

- [X] Automatic authentication handling (fetch/refresh JWT and re-authenticate upon 401 errors).
- [X] Automatic retries on failures (server errors or network timeouts/issues).
- [ ] Automatic JSON parsing and serialization of request and response bodies.
- [X] Future: [<a name="fn1">1</a>] compliance regarding [BSI CC-PP-0105-2019](https://www.bsi.bund.de/SharedDocs/Downloads/DE/BSI/Zertifizierung/Reporte/ReportePP/pp0105b_pdf.pdf?__blob=publicationFile&v=7) which mandates a locally executed SMA component for creating signed log messages. 
- [ ] Future: Automatic offline-handling (collection and documentation according to [Anwendungserlass zu § 146a AO](https://www.bundesfinanzministerium.de/Content/DE/Downloads/BMF_Schreiben/Weitere_Steuerthemen/Abgabenordnung/AO-Anwendungserlass/2019-06-17-einfuehrung-paragraf-146a-AO-anwendungserlass-zu-paragraf-146a-AO.pdf?__blob=publicationFile&v=1))

## Integration

### Composer

The PHP SDK is available for a download via [Composer](https://getcomposer.org/).

Packagist - [Package Repository](https://packagist.org/packages/fiskaly/fiskaly-sdk-php).

Simply execute this command from the shell in your project directory:

```bash
$ composer require fiskaly/fiskaly-sdk-php
```

Or you can manually add the package to your `composer.json` file:

```json
"require": {
    "fiskaly/fiskaly-sdk-php": "*"
}
```
then run 
```bash 
$ composer install 
```

Finally, be sure to include the autoloader in your code:

```php
<?php
require_once('vendor/autoload.php');
```

### Service

Additionally, to the SDK, you'll also need the fiskaly service. Follow these steps to integrate it into your project:

1. Go to [https://developer.fiskaly.com/downloads#service](https://developer.fiskaly.com/downloads#service)
2. Download the appropriate service build for your platform
3. Start the service

## Usage

### Demo

```php
<?php
require __DIR__ . '\\vendor\\autoload.php';
use FiskalyClient\FiskalyClient;

/** initialize the fiskaly API client class using credentials */
try {
    $client = FiskalyClient::createUsingCredentials('http://localhost:8080/invoke', $_ENV["FISKALY_API_KEY"], $_ENV["FISKALY_API_SECRET"], 'https://kassensichv.io/api/v1');
} catch (Exception $e) {
    exit($e);
}
/** get version of client and SMAERS */
try {
    $version = $client->getVersion();
    echo "Version: ", $version, "\n\n";
} catch (Exception $e) {
    exit($e);
}
```

Another way to create FiskalyClient object is using `context` string.
You can get it via `getContext` method and save it in memory via $_SESSION variable 
or persistent in cache or database.
```php
<?php
/** initialize the fiskaly API client class using context */
try {
    $client = FiskalyClient::createUsingContext('http://localhost:8080/invoke', $_SESSION["FISKALY_CONTEXT"]);
} catch (Exception $e) {
    exit($e);
}
```

### Client Configuration

The SDK is built on the [fiskaly Client](https://developer.fiskaly.com/en/docs/client-documentation) which can be [configured](https://developer.fiskaly.com/en/docs/client-documentation#configuration) through the SDK.

A reason why you would do this, is to enable the [debug mode](https://developer.fiskaly.com/en/docs/client-documentation#debug-mode).

#### Enabling the debug mode

The following code snippet demonstrates how to enable the debug mode in the client.

```php
<?php
/** configure client */
try {
    $config_params = array(
        'debug_level' => 4,
        'debug_file' =>  __DIR__ . '../../fiskaly.log',
        'client_timeout' =>  5000,
        'smaers_timeout' =>  2000,
    );
    $config = $client->configure($config_params);
    echo "Configuration: ", $config, "\n\n";
} catch (Exception $e) {
    exit($e);
}
````

## Related

* [fiskaly.com](https://fiskaly.com)
* [dashboard.fiskaly.com](https://dashboard.fiskaly.com)
* [kassensichv.io](https://kassensichv.io)
* [kassensichv.net](https://kassensichv.net)

