# Http-Response

Die Response-Klassen dienen dazu eine gültige Response zu erstellen und an den Client zu senden.

Für die unterschiedliche Response-Arten gibt es mehrere Klassen um die Anwendung zu erleichtern:

* `Response` – Universelle Klasse
* `FileResponse` – Zum Senden von Dateien
* `JsonResponse` – Zum Senden von JSON-Inhalten (z.B. für AJAX-Requests)
* `RedirectResponse` – Zum Umleiten des Clients auf eine andere URL

## Response Klasse

Die Klasse `Response` wird für alle Response-Arten benutzt, für die keine spezielle Klasse existiert. 

#### Beispiel

```php
use Xentral\Components\Http\Response;

$response = new Response('This is my response body.');
$response->setContentType('text/html', 'utf-8');
$response->addHeader('Cache-Control', 'no-cache');
$response->send();
```

### Überblick

1. Response erstellen
2. Eigene Header anfügen oder überschreiben
3. Response-Body setzen
4. Response an Client senden

### Response erstellen

```php
use Xentral\Components\Http\Response;

$response = new Response(
    'This is my response Content.',
    Response::HTTP_CREATED,             //alle HTTP status Codes sind als Konstante verfügbar
    ['Cache-Control' => ['no-cache']],
    '1.0',
    'Created'
);
```
Erzeugt folgende Response:
```http request
HTTP/1.0 201 Created

Cache-Control: no-cache
Content-Type: text/html; charset=utf-8
Content-Length: 28

This is my response Content.
```

### Header hinzufügen/ändern

#### `addHeader`

Mit `addHeader('Header-Name', 'Value')` wird ein neuer Header bzw.
ein weiterer Wert zu einem bestehenden Header hinzugefügt.

**Hinweis:** Bei einigen Headern wie z.B. `Content-Type`, `Content-Length`, `Content-Disposition` und `Date` kann nur
ein Wert zugewiesen werden. Bei der Übergabe von mehreren Werten wird ein `InvalidArgumentException` geworfen.

#### `setHeader`

Mit `setHeader('Header-Name', ['Value1', 'value2])` wird ein neuer Header gesetzt und dabei ein 
bestehender Header überschrieben. Hier können mehrere Werte in einem `array` übergeben werden.

### Response-Body setzen

Mit `setContent('Mein Inhalt als String')` wird der Response-Body gesetzt.

**Hinweis:** `setContent` berechnet und setzt zusätzlich den `Content-Length` Header. Wird `null` als Parameter
übergeben, so wird der `Content-Length` Header entfernt.

### Response an Client senden

Mit `send()` wird die Response abgeschickt. Vor dem Senden wird die Response noch modifiziert:
- Falls noch nicht vorhanden wird der `Date` Header gesetzt.
- Falls der Response-Body `null` ist, werden der `Content-Type`- und `Content-Length` Header entfernt.


## RedirectResponse Klasse

Die RedirectResponse-Klasse vereinfacht das Umleiten auf andere Seiten.

#### Beispiel

```php
use Xentral\Components\Http\Response;
use Xentral\Components\Http\RedirectResponse;

$redirect = RedirectResponse::createFromUrl('index.php?module=auftrag&action=list');
$redirect->setStatusCode(Response::HTTP_MOVED_PERMANENTLY);
$redirect->send();
```

Im Beispiel wird der Statuscode geändert. Per default ist `302 Found` als HTTP Status gesetzt.

## JsonResponse Klasse

Die JsonResponse Klasse vereinfacht das Erstellen von JSON-formatierten Antworten.

#### Beispiel

```php
use Xentral\Components\Http\JsonResponse;

$data = [
    'data' => [
        'id'   => '1234',
        'typ'  => 'herr',
        'name' => 'Max Mustermann',
    ]
];

$response = new JsonResponse($data);
```

Erzeugt folgende Response:

```http request
HTTP/1.1 200 OK

Content-Type: application/json; charset=utf-8
Content-Length: 59

{"data":{"id":"1234","typ":"herr","name":"Max Mustermann"}}
```

Als FileResponse kann ein `array` oder ein `JsonSerializable`-Objekt übergeben werden.

## FileResponse Klasse

Die FileResponse Klasse vereinfacht das Erstellen von Datei-Downloads.

### `FileResponse::createFromFile()`

Im folgenden Beispiel enthält `/tmp/file.txt` den Text `Hallo Welt`.

```php
use Xentral\Components\Http\FileResponse;

$fileResponse = FileResponse::createFromFile('/tmp/file.txt', 'download.txt');
```

Erzeugt folgende Response:

```http request
HTTP/1.1 200 OK

Content-Disposition: attachment; filename*="download.txt"; filename="download.txt"
Content-Type: text/plain; charset=utf-8
Content-Length: 20

Hallo Welt
```

Es wird versucht den Content-Type anhand des Mimetyps der Datei zu ermitteln. Falls die Erkennung fehlschlägt wird 
der Content-Type auf `application/octet-stream` gesetzt. Der Content-Type kann mit `$response->setContentType()` 
überschrieben werden.

### `FileResponse::createForcedDownload()`

Erzwingt den Download des Response-Contents. Das ist bei PDF's und Bildern besonders nützlich. 
Diese werden im Browser oft im Viewer geöffnet anstatt heruntergeladen zu werden.

`createForcedDownload` setzt den Content-Type `application/force-download`, dadurch erhält der User
den "Speichern unter"-Dialog.
