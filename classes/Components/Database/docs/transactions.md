# Transaktionnen

## Transaktion starten

```php
$db->beginTransaction();
```

## Transaktion übernehmen / Commit

```php
$db->commit();
```

## Transaktion zurücknehmen / Rollback

```php
$db->rollback();
```

## Prüfen ob Transaktion gestartet ist

```php
$db->inTransaction();
```
