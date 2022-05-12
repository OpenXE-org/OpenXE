# Named Parameter

`perform()`, `fetch*()` und `yield*()`-Methoden nehmen als zweiten Parameter ein assoziatives Array entgegen.
Mit diesem Array werden lassen sich Werte als *Named Parameter* 'binden'.

```php
$sql = 
    'SELECT a.id, a.nummer, a.name_de 
    FROM artikel AS a 
    WHERE a.typ IN (:types)
    AND a.nummer LIKE :nummers
    LIMIT :start, :length';

$values = [
    'types' => ['produkt', 'gebuehr'],
    'nummers' => '7000%',
    'start' => 0,
    'length' => 3,
];

$data = $db->fetchAll($sql, $values);
```
```
array (
  0 => 
  array (
    'id' => 1,
    'nummer' => '700001',
    'name_de' => 'Schraube M10x20',
  ),
  1 => 
  array (
    'id' => 2,
    'nummer' => '700002',
    'name_de' => 'Sechskant-Mutter M10',
  ),
  2 => 
  array (
    'id' => 3,
    'nummer' => '700003',
    'name_de' => 'Schalthebel 20x10',
  ),
)
```
