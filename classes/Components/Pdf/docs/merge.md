# PDF-Dateien zusammenführen

## Beispiel

```php
<?php

$sourceFiles = [
  '/tmp/example1.pdf',
  '/tmp/example2.pdf',
  '/tmp/example3.pdf',
];
$targetFile = '/tmp/merge.pdf';

$merger = $this->app->Container->get('PdfMerger');
$merger->merge($sourceFiles, $targetFile);
```

Im Fehlerfall wird eine Exception geworfen. Alle Exceptions implementieren 
`\Xentral\Components\Http\Exception\PdfComponentExceptionInterface`. 

Der zweite Parameter der `merge`-Methode ist optional. Wenn `null` übergeben wird, wird eine zufällige Datei im 
System-Temp-Ordner erzeugt. Der Dateipfad der erzeugten Datei steht dann im Rückgabewert der `merge`-Methode.
