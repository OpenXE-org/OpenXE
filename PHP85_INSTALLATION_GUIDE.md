# Installations- und Test-Anleitung

## Voraussetzungen
- PHP 8.4 oder 8.5 installiert und im PATH
- Composer installiert und im PATH
- Zugriff auf die Datenbank

## Schritt 1: Composer Dependencies aktualisieren

```bash
cd "c:\Users\3D Partner\Documents\OpenXe\OpenXE"

# Dependencies aktualisieren (mit Guzzle 7 und Smarty 4)
composer update --no-interaction

# Bei Fehlern: Composer cache leeren
composer clear-cache
composer update --no-interaction
```

## Schritt 2: Autoloader regenerieren

```bash
composer dump-autoload --optimize
```

## Schritt 3: Syntax-Prüfung

```bash
# Alle PHP-Dateien auf Syntax-Fehler prüfen
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \; | grep -v "No syntax errors"

# Windows PowerShell Alternative:
Get-ChildItem -Path . -Filter *.php -Recurse -Exclude vendor | ForEach-Object { php -l $_.FullName }
```

## Schritt 4: Unit Tests ausführen

```bash
# Alle Unit Tests
vendor/bin/phpunit --testsuite Unit

# Specific Component Tests
vendor/bin/phpunit --testsuite Components/Database
vendor/bin/phpunit --testsuite Components/Http
```

## Schritt 5: Static Analysis (Optional aber empfohlen)

### PHPStan installieren und ausführen
```bash
composer require --dev phpstan/phpstan
vendor/bin/phpstan analyse www classes phpwf --level 5
```

## Schritt 6: Manuelle Funktionstests

### Login & Basic Navigation
1. Login mit gültigen Credentials
2. Dashboard öffnen
3. Durch Hauptmenüs navigieren

### Artikel-Verwaltung
1. Artikel anlegen
2. Artikel bearbeiten
3. Artikel suchen/filtern
4. Artikel löschen

### WooCommerce Integration (falls verwendet)
1. WooCommerce-Sync ausführen
2. Produkte importieren
3. Bestellungen importieren
4. Bestandsabgleich prüfen

### Error Logs prüfen
```bash
# Error Log überwachen
tail -f /var/log/php/error.log

# Windows: PHP Error Log Pfad in php.ini prüfen
php --ini
# Dann die error_log Einstellung suchen

# OpenXE spezifische Logs
tail -f userdata/tmp/*/xentral.log
```

## Schritt 7: Performance Benchmark (Optional)

### Apache Bench
```bash
# Baseline vor dem Update (wenn vorhanden)
ab -n 1000 -c 10 http://localhost/index.php?module=artikel&action=list > benchmark_before.txt

# Nach dem Update
ab -n 1000 -c 10 http://localhost/index.php?module=artikel&action=list > benchmark_after.txt

# Vergleichen
diff benchmark_before.txt benchmark_after.txt
```

## Bekannte Issues nach Update

### Guzzle 6 → 7 Breaking Changes
- `GuzzleHttp\Client` API hat sich geändert
- Exception-Handling könnte betroffen sein
- Siehe: https://github.com/guzzle/guzzle/blob/master/UPGRADING.md#60-to-70

**Prüfen Sie besonders**:
- API-Aufrufe in `www/pages/api.php`
- HTTP-Client-Verwendung in `classes/Components/HttpClient/`
- WooCommerce-Integration in `www/pages/shopimporter_woocommerce.php`

### Smarty 3 → 4 Breaking Changes
- Template-Syntax könnte betroffen sein
- Smarty-Plugins müssen möglicherweise angepasst werden

**Prüfen Sie**:
- Template-Rendering in allen Modulen
- Custom Smarty-Plugins
- Template-Cache leeren: `rm -rf userdata/tmp/smarty/*`

## Troubleshooting

### Composer Update schlägt fehl
```bash
# Memory Limit erhöhen
php -d memory_limit=-1 /path/to/composer.phar update

# Oder in composer.json:
export COMPOSER_MEMORY_LIMIT=-1
composer update
```

### PHPUnit Tests schlagen fehl
1. Datenbank-Konfiguration prüfen
2. Test-Datenbank initialisieren
3. Einzelne Test-Suites ausführen um Fehlerquelle einzugrenzen

### Performance-Probleme nach Update
1. OPcache aktivieren (in php.ini)
2. Composer autoloader optimize: `composer dump-autoload --optimize --no-dev`
3. Smarty Template-Cache leeren

## Rollback bei Problemen

```bash
# Git Tag zum Rollback
git reset --hard v1.x.x-pre-php85-update

# Database Backup wiederherstellen
mysql -u username -p database_name < backup_YYYYMMDD.sql

# Dependencies wiederherstellen
git checkout composer.lock
composer install --no-dev
```

## Nach erfolgreichem Update

1. ✅ Error Logs 48h überwachen
2. ✅ Performance-Metriken vergleichen
3. ✅ Benutzer-Feedback sammeln
4. ✅ Backup-Strategie dokumentieren
5. ✅ Deployment-Prozess dokumentieren

## Support & Dokumentation

- PHP 8.4 Migration Guide: https://www.php.net/manual/en/migration84.php
- PHP 8.5 (upcoming): https://wiki.php.net/rfc#php_85
- OpenXE Documentation: Check `SERVER_INSTALL.md`, `INSTALL.md`
- GitHub Issues: https://github.com/OpenXE-org/OpenXE/issues
