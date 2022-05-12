# Database-Komponente

###### Verwendete Libraries

* **Aura.SqlQuery**:
  * Composer: `aura/sqlquery:2.7.*`
  * Packagist: https://packagist.org/packages/aura/sqlquery
  * GitHub: https://github.com/auraphp/Aura.SqlQuery
  * Docs: https://github.com/auraphp/Aura.SqlQuery/tree/2.x

* **~~Aura.Sql~~** Wird in 19.4 entfernt
  * Composer: `aura/sql:3.*`
  * Packagist: https://packagist.org/packages/aura/sql
  * GitHub: https://github.com/auraphp/Aura.Sql
  * Docs: https://github.com/auraphp/Aura.Sql/blob/3.x/docs/index.md

##### Database-Komponente aus Container holen

```php
$db = $container->get('Database');
```

Im alten Bereich:

```php
$db = $this->app->Container->get('Database');
```

## Themen

* [Datensätze abrufen (fetch)](fetch_results.md)
* [Datensätze abrufen mit Generatoren (yield)](yield_results.md)
* [Datensätze ändern](data_manipulation.md)
* [Transaktionen](transactions.md)
* [Named Parameter / Prepared Statements](named_parameter.md)
* [SQL Query Builder](query_builder.md)

## Exceptions

Die Database-Komponente verwendet intern `mysqli`. Im Unterschied zu `mysqli` werden in Fehlerfällen aber
Exceptions geworfen; z.B.:

* Wenn die Verbindung zur Datenbank fehlschlägt => `ConnectionException`
* Wenn ein SQL-Statement fehlerhaft ist oder aus anderen Gründen nicht erfolgreich ausgeführt 
  werden kann  => `QueryFailureException`
* Wenn `Named Parameter` fehlen => `MissingParameterException`

##### ExceptionInterface

Alle Exceptions die von der Database-Komponente geworfen werden implementieren das  
`\Xentral\Components\Database\Exception\DatabaseExceptionInterface` Interface.
