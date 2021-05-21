# Filesystem-Komponente

## Beispiele

### FTP

```php
<?php

/** @var \Xentral\Components\Filesystem\FilesystemFactory $factory */
$factory = $this->app->Container->get('FilesystemFactory');

$config = new \Xentral\Components\Filesystem\Adapter\FtpConfig('192.168.0.123', 'username', 'passwort', '/root-dir');
$ftp = $factory->createFtp($config);

if ($ftp->has('/some/file.txt')) {
    $contents = $ftp->read('/some/file.txt');
} else {
    $ftp->write('/some/file.txt', 'hello world!');
}
```

### Lokales Dateisystem

```php
<?php

/** @var \Xentral\Components\Filesystem\FilesystemFactory $factory */
$factory = $this->app->Container->get('FilesystemFactory');

$local = $factory->createLocal(dirname(__DIR__));

if ($local->has('/some/file.txt')) {
    $contents = $local->read('/some/file.txt');
} else {
    $local->write('/some/file.txt', 'hello world!');
}
```

### Große Dateien kopieren

```php
<?php

$ftp = $factory->createFtp($config);
$local = $factory->createLocal(dirname(__DIR__));

$ftp->writeStream('/ziel-pfad', $local->readStream('/quell-pfad'));
```

### Datei-Uploads

```php
<?php

$factory = $this->app->Container->get('FilesystemFactory');
$local = $factory->createLocal(dirname(__DIR__));

$stream = fopen($_FILES['upload']['tmp_name'], 'rb+');
$local->writeStream(
    'uploads/' . $_FILES['upload']['name'],
    $stream
);

if (is_resource($stream)) {
    fclose($stream);
}
```

### FilesystemSyncCache

```php
<?php

$ftp = $factory->createFtp($config);
$sync = $factory->createSync($ftp, 1);

$changes = $sync->listChanges('/sub', true); // Hinzugekommene und geänderte Dateien abrufen 
$deletes = $sync->listDeleted('/sub', true); // Gelöschte Dateien abrufen
```

## API

### FilesystemInterface

---

#### `has($path)`

Prüfen ob eine Datei oder ein Verzeichnis existiert.

Rückgabe: `true` oder `false`

---

#### `listContents($directory = '', $recursive = false)`

Verzeichnisinhalte abrufen.

Rückgabe: `array` mit `PathInfo`-Objekten

---

#### `listDirs($directory = '', $recursive = false)`

Verzeichnisinhalte abrufen; nur Verzeichnisse.

Rückgabe: `array` mit `PathInfo`-Objekten

---

#### `listFiles($directory = '', $recursive = false)`

Verzeichnisinhalte abrufen; nur Dateien.

Rückgabe: `array` mit `PathInfo`-Objekten

---

#### `listPaths($directory = '', $recursive = false)`

Verzeichnisinhalte abrufen; nur Pfadauflistung; keine Details.

Rückgabe: `array` mit Pfaden als `string`

---

#### `getInfo($path)`

Informationen über eine Datei oder ein Verzeichnis abrufen.

Rückgabe: `PathInfo`-Objekt

---

#### `getType($path)`

Prüfen ob Pfad eine Datei oder ein Verzeichnis ist.

Rückgabe: `'dir'` oder `'file''`

---

#### `getSize($path)`

Dateigröße abrufen.

Rückgabe: Dateigröße als `int` oder `false` bei einem Verzeichnis.

---

#### `getTimestamp($path)`

Datum der letzten Änderung abrufen.

Rückgabe: Timestamp als `int` oder `false` falls Information nicht verfügbar 
(z.B. bei Verzeichnissen über FTP).

---

#### `getMimetype($path)`

Mimetype abrufen.

Rückgabe: Mimetype als `string` oder `'directory'` bei einem Verzeichnis.

---

#### `read($path)`

Datei-Inhalt abrufen.

Rückgabe: `string`

---

#### `readStream($path)`

Datei-Inhalt als Stream abrufen.

Rückgabe: `resource`

---

#### `write($path, $contents, array $config = [])`
#### `writeStream($path, $resource, array $config = [])`

Datei anlegen.

Besonderheit: Wenn Zieldatei bereits existiert, wird eine `FileExistsException` geworfen.

Rückgabe: `true` oder `false`

---

#### `put($path, $contents, array $config = [])`
#### `putStream($path, $resource, array $config = [])`

Datei anlegen oder überschreiben.

Besonderheit: Im Unterschied zu `write()` und `writeStream()` wird keine Exception geworfen wenn das Ziel bereits existiert.

Rückgabe: `true` oder `false`

---

#### `createDir($dirname)`

Verzeichnis anlegen; funktioniert auch rekursiv.

Rückgabe: `true` oder `false`

---

#### `deleteDir($dirname)`

Verzeichnis löschen; funktioniert auch wenn Verzeichnis nicht leer.

Rückgabe: `true` oder `false`

---

#### `delete($path)`

Einzelne Datei löschen.

Rückgabe: `true` oder `false`

---

#### `rename($path, $newpath)`

Datei umbenennen.

Rückgabe: `true` oder `false`

---

#### `copy($path, $newpath)`

Datei kopieren.

Rückgabe: `true` oder `false`

---