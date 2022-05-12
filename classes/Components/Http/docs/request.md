# Http-Komponente

Die Http-Komponente ist eine objektorientierte Abstraction der HTTP-Spezifikation.

## Request-Klasse

Die Request-Klasse beinhaltet `$_GET`, `$_POST`, `$_FILES`, `$_COOKIE` (todo), und `$_SERVER`.

### Request erstellen

```php
$request = Request::createFromGlobals();
```

ist das gleiche wie

```php
$request = new Request(
    $_GET,
    $_POST,
    $_FILES,
    $_SERVER
    $_COOKIE,
);
```

createFromGlobals ist die empfohlene Methode

##### Request aus Container holen

```php
$request = $container->get('Request');
```

Im alten Bereich:

```php
$request = $this->app->Container->get('Request');
```

### Request-Parameter abrufen

* `$request->getGet()` für den Zugriff auf `$_GET`
* `$request->getPost()` für den Zugriff auf `$_POST`
* `$request->getFiles()` für den Zugriff auf `$_FILES`
* `$request->getServer()` für den Zugriff auf `$_SERVER`
* `$request->getCookie()` für den Zugriff auf `$_COOKIE` (TODO)

###### Beispiele

* `$request->getGet('value')` wie `$_GET['value']`  
* `$request->getPost('value')` wie `$_POST['value']`
* `$request->getServer('SERVER_NAME')` wie `$_SERVER['SERVER_NAME']`

#### ReadonlyParameterCollection

Die public Eigenschaften `get`, `post`, `files` und `server` liefern Instanzen der `ReadonlyParameterCollection`-Klasse.
Die Klasse bietet einige Hilfsmethoden:

* `has()` – Gibt `true` zurück wenn der Parameter gesetzt wurde
* `get()` – Gibt den Parameter zurück falls dieser gesetzt wurde; andernfalls `null`
* `all()` – Gibt alle gesetzten Parameter zurück


* `getBool()` – Wandelt den Wert zu Boolean
* `getInt()` – Wandelt den Wert zu Integer
* `getDigits()` – Wandelt den Wert zu String und entfernt alle Zeichen außer Zahlen `[0-9]`
* `getAlpha()` – Wandelt den Wert zu String und entfernt alle Zeichen außer Buchstaben `[a-z, A-Z]`
* `getAlphaNum()` – wie `getAlpha()` zusätzlich Zahlen `[a-z, A-Z, 0-9]`
* `getAlphaNumWithDashes()` – wie `getAlphaDigit()` zusätzlich Minus und Unterstrich `[a-z, A-Z, 0-9, -, _]`

##### Default-Werte

Die Getter-Methoden der `ParameterCollection` nehmen als zweiten Parameter einen Default-Wert entgegen.
Der Default-Wert wird verwendet wenn der Parameter nicht gesetzt ist.

###### Beispiele

* `$request->post->get('cmd', 'download')`  
	Gibt `'download'` zurück, falls `$_POST['cmd']'` nicht gesetzt ist.
* `$request->post->getBool('active', true)`  
  Gibt `true` zurück, falls `$_POST['active']'` nicht gesetzt ist. 

### Nützliches

Nachfolgende Beispielausgaben gehen von folgendem Request aus:

```http request
POST /wawision-19.1/www/index.php?module=welcome&action=settings HTTP/1.1

Host: 192.168.0.177
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:64.0) Gecko/20100101 Firefox/64.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: de-DE,de;q=0.8,en-US;q=0.5,en;q=0.3
Accept-Encoding: gzip, deflate
Referer: http://192.168.0.177/wawision-19.1/www/index.php?module=welcome&action=settings
Content-Type: application/x-www-form-urlencoded
Content-Length: 101
Connection: keep-alive
Cookie: PHPSESSID=19n48qro8d9blluqveg3dm1qth
Upgrade-Insecure-Requests: 1

startseite=&defaultcolor=%23FFFFFF&chat_popup=1&callcenter_notification=1&submit_startseite=Speichern
```

#### `$request->isSecure()`

Kam der Request über eine geschützte Verbindung?

Ausgabe: `false`

#### `$request->isAjax()`

Kam der Request über XHR?

Ausgabe: `false`

#### `$request->isCli()`

Kam der Request über eine Kommandozeile?

Ausgabe: `false`

#### `$request->getMethod()`

HTTP-Verb in Grossbuchstaben.

Ausgabe: `POST`

#### `$request->getContentType()`

Der hintere Teil von Content-Type Header.

Ausgabe: `x-www-form-urlencoded`

Beispiele:
* `json` bei `application/json`
* `html` bei `text/html`

#### `$request->getAcceptableContentTypes()`

```php
array (
   0 => 'text/html',
   1 => 'application/xhtml+xml',
   2 => 'application/xml',
   3 => '*/*',
 )
```

#### `$request->getContent()`

Gibt den Request-Body zurück.

Ausgabe: `startseite=&defaultcolor=%23FFFFFF&chat_popup=1&callcenter_notification=1&submit_startseite=Speichern`

#### `$request->getFullUri()`

Nicht mehr benutzen; stattdessen getFullUrl oder getBaseUrl verwenden.

`http://192.168.0.177/wawision-19.1/www/index.php?module=welcome&action=settings`

#### `$request->getFullUrl()`

Gibt die komplette Url zurück.

Ausgabe: `http://192.168.0.177/wawision-19.1/www/index.php?module=welcome&action=settings`

#### `$request->getBaseUrl()`

Gibt die Url ohne GET parameter zurück.

Ausgabe: `http://192.168.0.177/wawision-19.1/www/index.php`

#### `$request->getUrlForPath('/mypath')`

Gibt die URL um den angegebenen Pfad erweitert zurück.
Der Pfad muss mit `/` beginnen.

Ausgabe: `http://192.168.0.177/wawision-19.1/www/mypath`

#### `$request->getBasePath()`

Gibt den Pfad zwischen URL und aktuellen SCRIPT_NAME an.

##### Beispiel1:

```php
$_SERVER[
    'REQUEST_URI' => '/www/path/?value=1',
    'SCRIPT_NAME' => '/www/path/index.php',
]
```

Ausgabe: `/`

##### Beispiel2:

URL: 'http://192.168.0.177/wawision/www/api/v1/dateien/50' => '/v1/dateien/50'

#### `$request->getRequestUri()`

Gibt die relative Url ab dem Host zurück; wie `$_SERVER['REQUEST_URI']`

Ausgabe: `/wawision-19.1/www/index.php?module=welcome&action=settings`

#### `$request->isFailsafeUri()`

Gibt `true` zurück, wenn die Failsafe-URI im Request benutzt wurde.

Beispiel Failsafe-Uri: /api/index.php?path=/v1/adressen

#### `$request->getPathInfo()`

Gibt den PathInfo-Teil der Url zurück.

Ausgabe: `''`

#### `$request->getSchemeAndHttpHost()`

Gibt HTTP Schema und Host aus.

Ausgabe: `http://localhost`
