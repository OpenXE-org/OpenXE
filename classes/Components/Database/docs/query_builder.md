# SQL Query Builder

###### Verwendete Libraries

* **Aura.SqlQuery**:
  * Composer: `aura/sqlquery:2.7.*`
  * Packagist: https://packagist.org/packages/aura/sqlquery
  * GitHub: https://github.com/auraphp/Aura.SqlQuery
  * Docs: https://github.com/auraphp/Aura.SqlQuery/tree/2.x

## Query Builder erzeugen

```php
$select = $db->select();
$update = $db->update();
$insert = $db->insert();
$delete = $db->delete();
```

## SELECT-Query

https://github.com/auraphp/Aura.SqlQuery/tree/2.x#select

###### Beispiel mit Named-Placeholder

```php
$select = $db->select();
$select
    ->cols(['u.id', 'u.description'])
    ->from('user AS u')
    ->where('u.id = :user_id')
    ->bindValue('user_id', 1);

$result = $db->fetchAll(
    $select->getStatement(),
    $select->getBindValues()
);

var_export($result);
// array(
//     0 => array(
//         'id' => 1,
//         'description' => 'Administrator',
//     ),
// )
```

###### Alternative mit ?-Placeholder

```php
$select = $db->select();
$select
    ->cols(['u.id', 'u.description'])
    ->from('user AS u')
    ->where('u.id = ?', 1);
```

###### Beispiel mit Verschachtelung im WHERE

```php
$select = $db->select();
$select
    ->cols(['u.id', 'u.description'])
    ->from('user AS u')
    ->where('u.activ = ?', 1)
    ->where(function (SelectQuery $query) {
        $query->where('u.type = ?', 'admin')
            ->orWhere('u.type = ?', 'standard');
    });

echo $select->getStatement();
```
```sql
SELECT
    `u`.`id`,
    `u`.`description`
FROM
    `user` AS `u`
WHERE
   `u`.`activ` = :_1_1_
   AND (
       `u`.`type` = :_1_2_
       OR `u`.`type` = :_1_3_
   )
```

## INSERT-Query

https://github.com/auraphp/Aura.SqlQuery/tree/2.x#insert

## UPDATE-Query

https://github.com/auraphp/Aura.SqlQuery/tree/2.x#update

## DELETE-Query

https://github.com/auraphp/Aura.SqlQuery/tree/2.x#delete
