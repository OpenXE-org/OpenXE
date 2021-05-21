
# Datensätze abrufen

## `fetchAll()`

```php
$data = $db->fetchAll('SELECT a.id, a.typ, a.name_de FROM artikel AS a WHERE a.id > 5');
```
```
array (
  0 => 
  array (
    'id' => 6,
    'typ' => 'produkt',
    'name_de' => 'LED Anzeige RLED 24-8',
  ),
  1 => 
  array (
    'id' => 7,
    'typ' => 'produkt',
    'name_de' => 'Schalter S3 24V 5A',
  ),
  ...
)
```

## `fetchAssoc()`

Wie `fetchAll()` allerdings wird das Ergebnis der ersten Spalte als Index verwendet.

```php
$data = $db->fetchAssoc('SELECT a.id, a.typ, a.name_de FROM artikel AS a WHERE a.id > 5');
```
```
array (
  6 => 
  array (
    'id' => 6,
    'typ' => 'produkt',
    'name_de' => 'LED Anzeige RLED 24-8',
  ),
  7 => 
  array (
    'id' => 7,
    'typ' => 'produkt',
    'name_de' => 'Schalter S3 24V 5A',
  ),
  ...
)
```

## `fetchRow()`

Liefert die Ergebnisse der ersten Zeile als assoziatives Array. Die Spaltennamen werden als Index verwendet.

```php
$data = $db->fetchRow('SELECT a.id, a.name_de, a.name_en, a.logdatei FROM artikel AS a WHERE a.id > 5');
```
```
array (
  'id' => 6,
  'name_de' => 'LED Anzeige RLED 24-8',
  'name_en' => '',
  'logdatei' => '2015-10-26 17:26:27',
)
```

## `fetchValue()`

Liefert nur das Ergebnis der ersten Zeile und Spalte zurück.

```php
$data = $db->fetchValue('SELECT a.name_de, name_en, a.logdatei FROM artikel AS a WHERE a.id > 5');
```
```
'LED Anzeige RLED 24-8'
```

## `fetchCol()`

Liefert nur die Ergebnisse der ersten Spalte zurück.

```php
$data = $db->fetchCol('SELECT a.name_de, a.name_en, a.logdatei FROM artikel AS a WHERE a.id > 5');
```
```
array (
  0 => 'LED Anzeige RLED 24-8',
  1 => 'Schalter S3 24V 5A',
  2 => 'Gehäuse GHK5 20x30x10',
  ...
)
```

## `fetchPairs()`

Gibt ein (eindimentionales) assoziatives Array zurück. Der Wert der ersten Spalte wird als Index verwendet und die zweite Spalte als Wert.
**Erwartet werden genau zwei Spalten, andernfalls wird eine Exception geworfen.**

```php
$data = $db->fetchPairs('SELECT a.nummer, a.name_de FROM artikel AS a WHERE a.id > 5');
```
```
array (
  700006 => 'LED Anzeige RLED 24-8',
  700007 => 'Schalter S3 24V 5A',
  700008 => 'Gehäuse GHK5 20x30x10',
  ...
)
```

## `fetchGroup()`

Verhält sich wie `fetchAssoc()`; mit der Ausnahme dass die Werte der ersten Spalte gruppiert werden. 

```php
$data = $db->fetchGroup(
    'SELECT a.typ, a.nummer, a.name_de, a.logdatei FROM artikel AS a WHERE a.id > 5'
);
```
```
array (
  'produkt' => 
  array (
    0 => 
    array (
      'typ' => 'produkt',
      'nummer' => '700006',
      'name_de' => 'LED Anzeige RLED 24-8',
      'logdatei' => '2015-10-26 17:26:27',
    ),
    1 => 
    array (
      'typ' => 'produkt',
      'nummer' => '700007',
      'name_de' => 'Schalter S3 24V 5A',
      'logdatei' => '2015-10-26 17:26:59',
    ),
    2 => 
    array (
      'typ' => 'produkt',
      'nummer' => '700008',
      'name_de' => 'Gehäuse GHK5 20x30x10',
      'logdatei' => '2018-05-23 05:18:52',
    ),
  ),
  'gebuehr' => 
  array (
    0 => 
    array (
      'typ' => 'gebuehr',
      'nummer' => '100001',
      'name_de' => 'Versandkosten',
      'logdatei' => '2018-06-17 10:09:36',
    ),
  ),
)
```
