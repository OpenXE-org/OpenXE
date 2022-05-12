# Data Manipulation

## `perform()`

Für alle SQL Anweisung außer `SELECT`. Die Methode gibt nichts zurück. Sollte die Ausführung 
fehlschlagen, so wird eine Exception geworfen.

```php
$sql = 'UPDATE foo SET bar = :bar WHERE id = :id';

$values = [
    'bar' => 'baz',
    'id' => 123,
];

$db->perform($sql, $values);
```

## `fetchAffected()`

Für `INSERT`, `REPLACE`, `UPDATE` und `DELETE` Anweisungen. Gibt die Anzahl der betroffenen Datensätze zurück.

```php
$sql = 'UPDATE foo SET bar = :bar WHERE 1';

$values = [
    'bar' => 'baz',
];

echo $db->fetchAffected($sql, $values);
```

###### Ausgabe

```
42
```

## `lastInsertId()`

Gibt den zuletzt erzeugten Auto-Increment-Wert zurück.

```php
$sql = 'INSERT INTO foo (id, bar) VALUES (NULL, :bar)';

$values = [
    'bar' => 'baz',
];

$db->perform($sql, $values);
// $db->fetchAffected($sql, $values); // Alternative 

echo $db->lastInsertId();
```

###### Ausgabe
```
123
```

## Multiple-Row-Insert

```php
$sql = 'INSERT INTO foo (id, bar) VALUES (NULL, :bar1, :baz1), (NULL, :bar2, :baz2), (NULL, :bar3, :baz3)';

$values = [
    'bar1' => 'bar',
    'baz1' => 'baz',
    'bar2' => 'barbar',
    'baz2' => 'bazbaz',
    'bar3' => 'barbarbar',
    'baz3' => 'bazbazbaz',
];

$db->perform($sql, $values);
// $db->fetchAffected($sql, $values); // Alternative; Rückgabe wäre `3` 
```
