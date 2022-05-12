# Http Dateiuploads

In den Folgenden Beispielen wird gezeigt, wie man Http Datei Uploads realisieren und verarbeiten kann.

## Einfacher Dateiupload

### Frontend (HTML)

Der Dateiupload nutzt im Frontend ein HTML-Formular.
Wichtig: Parameter `enctype="multipart/form-data` muss gesetzt sein.

```html
<form action="?module=upload&action=upload" method="post" enctype="multipart/form-data">
  <input name="file" type="file">
  <input name="anotherfile" type="file">
  <button type="submit">UPLOAD</button>
</form>
```

### Verarbeitung in PHP

Bei einem Dateiupload werden die Dateien von PHP vor der Programmausführung entgegengenommen und in `/tmp` gespeichert.
Zusätzlich werden die Informationen über den Upload in der `$_FILES` Variable gespeichert.

`var_dump($_FILES)` erzeugt diesen Output:

```php
array (size=2)
  'file' => 
    array (size=5)
      'name' => string 'my_uploaded_file.md' (length=19)
      'type' => string 'text/markdown' (length=13)
      'tmp_name' => string '/tmp/phphoXkRU' (length=14)
      'error' => int 0
      'size' => int 8972
  'anotherfile' => 
    array (size=5)
      'name' => string 'my_other_file.txt.xml' (length=28)
      'type' => string 'text/xml' (length=8)
      'tmp_name' => string '/tmp/php2NqM0Z' (length=14)
      'error' => int 0
      'size' => int 9499
```

### Verarbeitung im Controller

Auf die Uploadinfo kann man pro datei über die Request Klasse zugreifen:

```php
$request = $this->app->Container->get('Request');
$fileUpload = $request->getFile('file');
```

`getFile` liefert eine Instanz von `FileUpload` zurück.

#### Validierung

Bevor der FileUpload weiterverwertet wird, sollte aus Sicherheitsgründen die Integrität des Uploads überprüft werden.
Dafür stehen mehrere Methoden zur Verfügung:

* `$fileUpload->isValid()` prüft, ob die Datei zu einem gültigen `POST` Upload gehört und sollte **immer** ausgeführt werden.
* `$fileUpload->isFile()` prüft, ob die Datei im `/tmp` existiert.
* `$fileUpload->isReadable()` prüft, ob die Datei gelesen werden kann.
* `$fileUpload->hasError()` prüft, ob ein Http Error vorliegt.

**Hinweis:** Wenn ein Http Fehler vorliegt, kann man den Grund dafür per `$fileUpload->getErrorMessage()` ermitteln
und ggf. an den Client weitergeben. Liegt allerdings kein Fehler vor, führt diese Methode zu einer Exception!

#### Speicherung

Wenn ein Upload dauerhaft gespeichert werden soll, muss die Datei aus dem `/temp` an einen dauerhaften Speicherort
verschoben werden:

```php
$fileUpload->move('/var/storage', 'upload.txt');
```

**Hinweis:** Wenn sich bereits eine Datei mit diesem Namen am Zielort befindet, wird eine Exception geworfen und die
Datei wird **nicht** überschrieben oder anderweitig gespeichert.

#### Zugriff

Man kann auf zwei Arten auf den Inhalt des Uploads zugreifen:

* `$fileUpload->getContent()` liefert den Inhalt als `string` zurück. (empfohlen für kleinere Dateien)
* `$fileUpload->createContentStream()` liefert eine `resource`, aus der der Inhalt gestreamed werden kann. 

**Tipp:** Mit `$fileUpload->getMimeType()` kann man vor dem Einlesen nocht prüfen, ob der User die Datei im
richtigen Format (json, csv, xml usw.) hochgeladen hat.

## Dateiupload in Array

### Frontend (HTML)

Für den Dateiupload können Dateien auch in zusammengehörigen Arrays hochgeladen werden.

Beispiel: Upload einer eigenen Schriftart. Hier werden vier dateien hochgeladen, die aber semantisch zusammengehören.

```html
<form action="?module=upload&action=upload" method="post" enctype="multipart/form-data">
  <input name="font[default]" type="file">
  <input name="font[bold]" type="file">
  <input name="font[italic]" type="file">
  <input name="font[bolditalic]" type="file">
  <button type="submit">UPLOAD</button>
</form>
```

### Verarbeitung in PHP

In diesem Fall wird die `$_FILES` Variable in einer anderen hierarchie aufgebaut:

```php
array (size=1)
  'font' => 
    array (size=5)
      'name' => 
        array (size=4)
          'regular' => string 'LiberationMono-Regular.ttf' (length=26)
          'bold' => string 'LiberationMono-Bold.ttf' (length=23)
          'italic' => string 'LiberationMono-Italic.ttf' (length=25)
          'bolditalic' => string 'LiberationMono-BoldItalic.ttf' (length=29)
      'type' => 
        array (size=4)
          'regular' => string 'font/ttf' (length=8)
          'bold' => string 'font/ttf' (length=8)
          'italic' => string 'font/ttf' (length=8)
          'bolditalic' => string 'font/ttf' (length=8)
      'tmp_name' => 
        array (size=4)
          'regular' => string '/tmp/phpN3NU7D' (length=14)
          'bold' => string '/tmp/phpKzBu8u' (length=14)
          'italic' => string '/tmp/php9wVd9l' (length=14)
          'bolditalic' => string '/tmp/phpRYicad' (length=14)
      'error' => 
        array (size=4)
          'regular' => int 0
          'bold' => int 0
          'italic' => int 0
          'bolditalic' => int 0
      'size' => 
        array (size=4)
          'regular' => int 108172
          'bold' => int 105460
          'italic' => int 124012
          'bolditalic' => int 118296
```

### Verarbeitung im Controller

Auf die Uploadinfo muss jetzt anders zugegriffen werden, da `getFile` nur die oberste Ebene des Arrays ausgibt.

```php
$request = $this->app->Container->get('Request');
$fileArray = $request->getFile('font');
$fontRegular = $fileArray['regular'];
$fontBold = $fileArray['bold'];
```

Alternativ ist auch möglich:

```php
$request = $this->app->Container->get('Request');
$fileArray = $request->files->all();
$fontRegular = $fileArray['font']['regular'];
$fontBold = $fileArray['font']['bold'];
```

Beispiel: über alle Uploads iterieren:

```php
$request = $this->app->Container->get('Request');
foreach($request->files as $fontType => $file) {
    doSomething($fontType, $file);
}
```

Mit den einzelnen `FileUpload` Objekten verfährt man nun genau wie im oberen Beispiel.

**Hinweis:** Die Hierarchie der Dateiuploads kann beliebig tief geschachtelt sein. Getestet wird aber nur bis zur
dritten Ebene.

