
# Datensätze abrufen mit Generatoren

Um den Arbeitsspeicherverbrauch gering zu halten bieten sich zum Iterieren von großen Datenmengen 
Generatoren an: https://www.php.net/manual/de/language.generators.overview.php 

Die Database-Komponente stellt für diesen Zweck `yield`-Methoden zur Verfügung.


## `yieldAll()`

Wie `fetchAll()`; jede Zeile ist ein assoziatives Array.

```php
$statement = 'SELECT a.id, a.typ, a.name_de FROM artikel AS a WHERE a.typ = :typ LIMIT 2';
$bindValues = ['typ' => 'produkt'];

foreach ($db->yieldAll($statement, $bindValues) as $row) {
    var_dump($row);
}
```
```
array (size=3)
  'id' => int 2
  'typ' => string 'produkt' (length=7)
  'name_de' => string 'Sechskant-Mutter M10' (length=20)
  
array (size=3)
  'id' => int 3
  'typ' => string 'produkt' (length=7)
  'name_de' => string 'Schalthebel 20x10' (length=17)
```


## `yieldAssoc()`

Wie `fetchAssoc()`; jede Zeile ist ein assoziatives Array; der Key beinhaltet den Wert der ersten Spalte

```php
$statement = 'SELECT a.id, a.typ, a.name_de FROM artikel AS a WHERE a.typ = :typ LIMIT 2';
$bindValues = ['typ' => 'produkt'];

foreach ($db->yieldAssoc($statement, $bindValues) as $key => $row) {
    var_dump($key);
    var_dump($row);
}
```
```
int 2
array (size=3)
  'id' => int 2
  'typ' => string 'produkt' (length=7)
  'name_de' => string 'Sechskant-Mutter M10' (length=20)

int 3
array (size=3)
  'id' => int 3
  'typ' => string 'produkt' (length=7)
  'name_de' => string 'Schalthebel 20x10' (length=17)
```


## `yieldPairs()`

Wie `fetchPairs()`; jede Zeile besteht aus Key-Value-Paaren; der Key beinhaltet den Inhalt der ersten Spalte; 
der Wert den Inhalt der zweiten Spalte.

**Erwartet werden genau zwei Spalten, andernfalls wird eine Exception geworfen.**

```php
$statement = 'SELECT a.id, a.name_de FROM artikel AS a WHERE a.typ = :typ LIMIT 2';
$bindValues = ['typ' => 'produkt'];

foreach ($db->yieldPairs($statement, $bindValues) as $key => $value) {
    var_dump($key);
    var_dump($value);
}
```
```
int 2
string 'Sechskant-Mutter M10' (length=20)

int 3
string 'Schalthebel 20x10' (length=17)
```


## `yieldCol()`

Wie `fetchCol()`; jede Zeile beinhaltet nur den Wert der ersten Spalte.

```php
$statement = 'SELECT a.name_de FROM artikel AS a WHERE a.typ = :typ LIMIT 2';
$bindValues = ['typ' => 'produkt'];

foreach ($db->yieldCol($statement, $bindValues) as $key => $value) {
    var_dump($key);
    var_dump($value);
}
```
```
int 0
string 'Sechskant-Mutter M10' (length=20)

int 1
string 'Schalthebel 20x10' (length=17)
```
