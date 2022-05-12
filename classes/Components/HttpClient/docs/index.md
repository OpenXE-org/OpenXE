# HTTP-Client

## Neue HttpClient-Instanz erzeugen

```php
/** @var \Xentral\Components\HttpClient\HttpClientFactory $factory */
$factory = $container->get('HttpClientFactory');
$client = $factory->createClient();
```

## Requests abschicken

```php
/** @var \Xentral\Components\HttpClient\HttpClientFactory $factory */
/** @var \Xentral\Components\HttpClient\HttpClientInterface $client */
$client = $factory->createClient();

$uri = 'https://httpbin.org/json';
$headers = ['Accept' => 'application/json'];
$request = new \Xentral\Components\HttpClient\Request\ClientRequest('GET', $uri, $headers);

try {
    /** @var \Xentral\Components\HttpClient\Response\ServerResponseInterface $response */
    $response = $client->sendRequest($request);
} catch (\Xentral\Components\HttpClient\Exception\TransferErrorExceptionInterface $exception) {
    $request = $exception->getRequest();
    $response = $exception->hasResponse() ? $exception->getResponse() : null;
    // ...
}
```

## Request-Optionen

Mit den Request-Optionen kann das Standard-Verhalten des Http-Clients beeinflusst werden.

### Standard-Optionen

* Timeout: 0 Sekunden (kein Timeout)
* Umleitungen folgen: Maximal fünf Redirects
* Streaming-Verhalten: Deaktiviert (alles am Stück downloaden)
* SSL-Zertifikatsverifizierung: Aktiv
* HTTP-Protokollversion: 1.1

### Request-Optionen übergeben

Es gibt zwei Möglichkeiten Request-Optionen zu übergeben:
1. Beim Erzeugen der HttpClient-Instanz (`HttpClientFactory::createClient($options)`)
    * Die Optionen gelten dann für alle Requests dieser Instanz.
2. Beim Abschicken eines Requests (`HttpClient::sendRequest($request, $options)`)
    * Die Optionen werden nur für diesen Request angewendet.

Wenn an beiden Stellen Optionen übergeben werden, werden die Optionen zusammengefasst. Bei Konflikten haben die 
Optionen Vorrang die beim Abschicken des Request übergeben werden (2. Möglichkeit).   

### Beispiele

```php
$options = new \Xentral\Components\HttpClient\RequestOptions();
$options->setTimeout(5); // Maximal fünf Sekunden auf eine Antwort warten 
$options->disallowRedirects(); // Keinen Umleitungen folgen
$options->setAuthDigest('username', 'password'); // Per DigestAuth authentifizieren
$options->setHeader('Accept', ['text/html', 'text/plain']); // Accept-Header setzen
```

#### Große Dateien downloaden

Für den Download von großen Dateien empfiehlt es sich die `setStorageLocation()`-Option zu verwenden.
Die Methode nimmt entweder einen Dateipfad oder ein `resource`-Objekt als Parameter entgegen.

Der Vorteil dieser Option besteht darin, dass der Response-Body direkt in eine Datei gestreamt wird.
Das führt zu einem sehr geringen Arbeitsspeicherbedarf.

```php
$options = new \Xentral\Components\HttpClient\RequestOptions();
$options->setStorageLocation('/tmp/large_file');

$response = $client->sendRequest($request, $options);
```

## Fehlerbehandlung

Im Fehlerfall wird standardmäßig eine Exception geworfen. Alle Exceptions implementieren 
`\Xentral\Components\HttpClient\Exception\HttpClientExceptionInterface`.

Desweiteren gibt es spezielle Exceptions die bei HTTP-Protokollfehlern geworfen werden. Diese sind 
alle von `\Xentral\Components\HttpClient\Exception\TransferErrorException` abgeleitet.

### Transfer-Fehler

* Verbindungsaufbau ist fehlgeschlagen
    * Mögliche Ursachen: Gegenstelle ist nicht vorhanden (URL falsch/fehlerhaft), Gegenstelle momentan nicht 
      erreichbar, Routing-Fehler
    * Klasse: `\Xentral\Components\HttpClient\Exception\ConnectionFailedException`

* HTTP-Client-Fehler (HTTP-Status 4xx)
    * Die Ursache des Scheiterns liegt eher im Verantwortungsbereich des HTTP-Clients.
    * Beispiel: Zugriff auf Resource ist nicht erlaubt (HTTP-Status 403)
    * Klasse: `\Xentral\Components\HttpClient\Exception\ClientErrorException`

* HTTP-Server-Fehler (HTTP-Status 5xx)
    * Die Ursache des Scheiterns liegt eher im Verantwortungsbereich der Gegenstelle (Server).
    * Beispiel: Internal Server Error (HTTP-Status 500)
    * Klasse: `\Xentral\Components\HttpClient\Exception\ServerErrorException`

* Zu viele Umleitungen
    * In der Standard-Einstellung sind fünf Weiterleitungen erlaubt. Wird diese Zahl überschritten wird die 
    `TooManyRedirectsException` geworfen. In den Request-Optionen lässt sich die Anzahl der maximal erlaubten 
    Weiterleitungen festlegen. 
    * Klasse: `\Xentral\Components\HttpClient\Exception\TooManyRedirectsException`


### Transfer-Fehler-Exceptions deaktivieren

Über die Request-Optionen können Exceptions für HTTP-Protokollfehler deaktiviert werden. 

```php
$options = new \Xentral\Components\HttpClient\RequestOptions();
$options->disableHttpErrorExceptions();

/** @var \Xentral\Components\HttpClient\HttpClientFactory $factory */
$factory = $container->get('HttpClientFactory');
$client = $factory->createClient($options);

$response = $client->request('GET', 'http://not_existing');
echo $response->getStatusCode(); // Ausgabe: 404
```
