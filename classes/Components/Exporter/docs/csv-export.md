# CSV-Exporter

## Datei erstellen

```php
<?php

use Xentral\Components\Database\Database;
use Xentral\Components\Exporter\Csv\CsvExporter;

/** @var Database $db */
$db = $container->get('Database');
$data = $db->yieldAll('SELECT e.* FROM employees AS e');

$filePath = tempnam(sys_get_temp_dir(), 'employees') . '.csv';

$exporter = new CsvExporter();
$exporter->export($filePath, $data);
```

##### Rückgabewerte

* Die `export()` Methode liefert nichts zurück. 
* Die `exportToResource()` gibt die geöffnete Ressource zurück. Die Ressource muss manuell (mit `fclose()`)
  geschlossen werden. Der Dateizeiger ist auf `EOF` platziert. 

##### Funktionsparameter

Beide `export*()`-Methoden nehmen die gleichen Parameter entgegen.

* Als erster Parameter wird ein absoluter Dateipfad erwartet. Alternativ kann ein Stream Wrapper angegeben werden.
Beispiele:
	* `php://output` um die CSV direkt auszugeben (`echo`).
	* `php://memory` um in den Arbeitsspeicher zu schreiben.
	* `php://temp` um in eine temporäre Datei zu schreiben.

* Der zweite Parameter nimmt die Daten entgegen die als CSV exportiert werden sollen. Folgende Typen sind erlaubt:
	* `array`
	* `Generator`
	* `Iterator`
	* `IteratorAggregate`

##### Konstruktor-Parameter

* Der erste Konstruktor-Parameter ist optional und nimmt die CSV-Konfiguration entgegen; 
siehe _Erweiterte Beispiele_ > _CSV konfigurieren_.

## Datei-Download erstellen

```php
<?php

use Xentral\Components\Database\Database;
use Xentral\Components\Exporter\Csv\CsvExporter;

/** @var Database $db */
$db = $container->get('Database');
$data = $db->yieldAll('SELECT e.* FROM employees AS e');

$exporter = new CsvExporter();
$resource = $exporter->exportToResource('php://memory', $data);
rewind($resource);
$stat = fstat($resource);

header('Cache-Control: must-revalidate');
header('Pragma: must-revalidate');
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="employees.csv"');
header('Content-Length: ' . $stat['size']);
fpassthru($resource);
fclose($resource);
```


## Erweiterte Beispiele

### CSV konfigurieren

```php
<?php

use Xentral\Components\Exporter\Csv\CsvConfig;
use Xentral\Components\Exporter\Csv\CsvExporter;

$csvConfig = new CsvConfig();
$csvConfig->setDelimiter(';');
$csvConfig->setEnclosure('"');
$csvConfig->setSourceCharset('UTF-8');
$csvConfig->setTargetCharset('ISO-8859-1');

$exporter = new CsvExporter($csvConfig);
$exporter->export($filePath, $data);
```

### Daten zusammenführen

```php
<?php

use Xentral\Components\Exporter\Csv\CsvExporter;
use Xentral\Components\Exporter\Collection\DataCollection;

$collection = new DataCollection($headline, $employees);

$exporter = new CsvExporter();
$exporter->export($filePath, $collection);
```

### Daten formatieren

```php
<?php

use Xentral\Components\Exporter\Csv\CsvExporter;
use Xentral\Components\Exporter\Collection\FormatterCollection;

$formatter = new FormatterCollection($data, function ($row) {
    $row['fullname'] = $row['firstname'] . ' ' . $row['lastname'];
    
    return $row;
});

$exporter = new CsvExporter();
$exporter->export($filePath, $formatter);
```

Statt einer anonymen Funktion (`Closure`) kann ein `callable` übergeben werden. 
