# PHP 8.5 Kompatibilitäts-Update für OpenXE

## Zusammenfassung
OpenXE hat laut `composer.json` PHP 8.1 als Mindestanforderung, aber es gibt kritische Kompatibilitätsprobleme für PHP 8.3, 8.4 und 8.5.

## 🔥 Kritische PHP 8.4/8.5 Breaking Changes

| Problem | Status | Betroffene Dateien | Priorität |
|---------|--------|-------------------|-----------|
| Implicitly nullable parameters | ✅ Gefixt (Script) | ~100+ Dateien | **KRITISCH** |
| Dynamic Properties | ✅ Gefixt (Core) | `ApplicationCore`, `Application`, `Config` | **HOCH** |
| `each()` Funktion | ✅ Gefixt | `fpdi.php`, `fpdf.php`, `fpdf_2.php`, `imap.inc.php`, `table.php` | **HOCH** |
| Deprecated Hash-Funktionen | ⚠️ Teilweise | `getmyinvoices.php` | **MITTEL** |
| Undefined array keys/null | ✅ Gefixt | `shopimporter_woocommerce.php`, `class.remote.php` | **HOCH** |
| Deprecated UTF-8 Funktionen | ✅ Gefixt (Script) | ~45+ Stellen | **HOCH** |

---

## Phase 1: KRITISCH - Implicitly Nullable Parameters (PHP 8.4)

### Problem
PHP 8.4 deprecates implicitly nullable parameters. Code wie:

```php
function foo($bar = null) { }  // ⚠️ Deprecated
```

Muss werden:

```php
function foo(?$bar = null) { }  // ✅ Correct
```

### Betroffene Bereiche
- ~95+ Funktionen in `www/lib/`
- Alle Methoden mit `= null` Parameter aber ohne `?` Typdeklaration

### Automatisierung
✅ **Ja** - mit Regex-basiertem Script

#### Automatisierungs-Script
```bash
# Findet alle `= null` Parameter und fügt `?` hinzu (vorsichtig verwenden!)
# WARNUNG: Manuell reviewen vor dem Commit!
find www -name "*.php" -exec sed -i 's/\(\$[a-zA-Z_][a-zA-Z0-9_]*\) = null/?\1 = null/g' {} \;
```

> [!WARNING]
> **Dieser Script muss manuell nachbearbeitet werden!** Einige Fälle benötigen individuelle Prüfung.

---

## Phase 2: KRITISCH - Dynamic Properties (PHP 8.2+)

### Problem
Seit PHP 8.2 sind dynamische Properties deprecated, ab PHP 9.0 wird es ein Fehler sein.

### Lösung

#### Option A: `#[AllowDynamicProperties]` Attribut (Quick Fix)
Für jede Klasse die dynamische Properties verwendet:

```php
#[AllowDynamicProperties]
class MyClass {
    // ...
}
```

#### Option B: Explizite Property-Deklaration (Best Practice)
Alle Properties explizit deklarieren:

```php
class MyClass {
    public $property1;
    protected $property2;
    // ...
}
```

### Betroffene Core-Klassen

#### [MODIFY] [phpwf/class.application_core.php](file:///c:/Users/3D%20Partner/Documents/OpenXe/OpenXE/phpwf/class.application_core.php)
```php
#[AllowDynamicProperties]
class ApplicationCore
{
    // Die Klasse nutzt __get() und __set() magic methods intensiv
```

#### [MODIFY] [phpwf/class.application.php](file:///c:/Users/3D%20Partner/Documents/OpenXe/OpenXE/phpwf/class.application.php)
```php
#[AllowDynamicProperties]
class Application extends ApplicationCore
{
    // Erbt das Verhalten von ApplicationCore
```

### Scan erforderlich
Analysiere alle Klassen ohne Property-Deklarationen die Properties zur Laufzeit setzen:

```bash
# Findet Klassen die möglicherweise dynamic properties verwenden
grep -r "this->" www/ --include="*.php" | grep -v "public \$\|private \$\|protected \$"
```

---

## Phase 3: HOCH - `each()` Funktion ersetzen

### Problem
`each()` wurde in PHP 8.0 entfernt. 100+ Dateien betroffen.

### Ersetzung

```php
// Alt:
while (list($key, $value) = each($array)) {
    // code
}

// Neu:
foreach ($array as $key => $value) {
    // code
}
```

### Betroffene Dateien
- `eproosystem.php`
- `devices/index.php`
- `widgets/artikeltable.php`
- `pages/adapterbox.php`
- 100+ weitere Dateien

### Automatisierung
⚠️ **Teilweise** - Jeder Fall muss individuell geprüft werden, da `each()` auch `reset()` auf dem Array durchführt.

---

## Phase 4: MITTEL - Deprecated Hash-Funktionen (PHP 8.4)

### Betroffene Funktionen

| Funktion | Dateien | Replacement | PHP Version |
|----------|---------|-------------|-------------|
| `md5()` | 40+ | `hash('sha256', ...)` | Deprecated 8.4 |
| `sha1()` | 12+ | `hash('sha256', ...)` | Deprecated 8.4 |
| `uniqid()` | 19+ | `bin2hex(random_bytes(16))` | Deprecated 8.4 |

> [!IMPORTANT]
> Diese sind nur für **kryptographische Zwecke** deprecated! Für nicht-kryptographische Verwendung (z.B. Cache-Keys) sind sie weiterhin OK.

> [!CAUTION]
> **Prüfen ob `md5()` / `sha1()` für Passwörter/Security verwendet werden!**

---

## Phase 5: HOCH - Null-Safety Fixes

### Aktuelle bekannte Issues
- ✅ `class.remote.php` Zeile 155: Bereits gefixt
- ✅ `shopimporter_woocommerce.php` Zeile 812: Bereits gefixt

### Weitere Scans nötig
- Undefined array key Zugriffe
- Null-Pointer dereferences
- Array offset Zugriffe ohne Prüfung

### Deprecated UTF-8 Funktionen
Scan für deprecated Funktionen in PHP 8.2-8.4:

```php
// Deprecated:
utf8_encode($string);
utf8_decode($string);

// Replacement:
mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
```

---

## Phase 6: Dependency Updates

### [MODIFY] [composer.json](file:///c:/Users/3D%20Partner/Documents/OpenXe/OpenXE/composer.json)

```json
{
  "require": {
    "php": "^8.4",
    "guzzlehttp/guzzle": "^7.8",
    "smarty/smarty": "^4.3"
  },
  "config": {
    "platform": {
      "php": "8.4"
    }
  }
}
```

**Wichtige Dependency-Updates:**
- PHP: `^8.1` → `^8.4`
- Guzzle: `^6.5.5` → `^7.8` (Major version bump!)
- Smarty: `v3.1.39` → `^4.3` (Major version bump!)
- Andere Dependencies auf Kompatibilität prüfen

> [!WARNING]
> Guzzle 7 und Smarty 4 haben Breaking Changes! Sorgfältige Tests erforderlich.

---

## Verifizierungsplan

### 1. Automatisierte Static Analysis

#### PHP Lint für alle Dateien
```bash
find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \; | grep -v "No syntax errors"
```

#### PHPStan für Static Analysis
```bash
composer require --dev phpstan/phpstan
vendor/bin/phpstan analyse www classes phpwf --level 5
```

#### PHP CodeSniffer für Coding Standards
```bash
composer require --dev squizlabs/php_codesniffer
vendor/bin/phpcs --standard=PSR12 www/ classes/ phpwf/
```

### 2. Automatisierte Unit Tests

#### PHPUnit Tests ausführen
```bash
# Alle Unit Tests
vendor/bin/phpunit --testsuite Unit

# Component Tests
vendor/bin/phpunit --testsuite Components/Util
vendor/bin/phpunit --testsuite Components/Database
vendor/bin/phpunit --testsuite Components/Http

# Integration Tests (falls DB verfügbar)
vendor/bin/phpunit --testsuite Integration
```

### 3. Manuelle Funktionstests

#### Kritische User-Workflows
- [ ] **Login & Authentifizierung**
  - Login mit korrekten Credentials
  - Login mit falschen Credentials
  - Logout-Funktion
  - Session-Handling

- [ ] **Dashboard & Navigation**
  - Dashboard lädt korrekt
  - Alle Menüpunkte erreichbar
  - Widgets werden angezeigt

- [ ] **Artikel-Verwaltung**
  - Artikel anlegen
  - Artikel bearbeiten
  - Artikel löschen
  - Artikelliste durchsuchen

- [ ] **WooCommerce Integration**
  - WooCommerce-Sync durchführen
  - Produkte importieren
  - Variationen importieren (siehe vorherige Fixes)
  - Bestandsabgleich

- [ ] **Shop-Import/Export**
  - Produkte exportieren
  - Bestellungen importieren
  - CSV-Export testen

- [ ] **Cronjobs**
  - Alle Cronjobs 24h überwachen
  - Error-Logs auf Deprecation Warnings prüfen
  - Performance-Metriken vergleichen

#### Error Log Monitoring
```bash
# PHP Error Log überwachen während Tests
tail -f /var/log/php/error.log | grep -E "Deprecated|Warning|Fatal"

# OpenXE spezifische Logs
tail -f userdata/tmp/*/xentral.log
```

### 4. Performance-Benchmarks

```bash
# Vor dem Update
ab -n 1000 -c 10 http://localhost/index.php?module=artikel&action=list

# Nach dem Update (Vergleich)
ab -n 1000 -c 10 http://localhost/index.php?module=artikel&action=list
```

---

## Implementierungsstrategie

### ✅ Option A: Schrittweise (Empfohlen)

1. **Sprint 1: Critical Fixes**
   - Phase 2: Dynamic properties (`#[AllowDynamicProperties]` zu Core-Klassen)
   - Phase 5: Null-Safety Fixes (bekannte Issues)
   - Deployment + 1 Woche Monitoring

2. **Sprint 2: Parameter Fixes**
   - Phase 1: Implicitly nullable parameters (automatisiert + Review)
   - Deployment + Tests

3. **Sprint 3: each() Replacement**
   - Phase 3: `each()` zu `foreach` konvertieren
   - Intensives Testing erforderlich

4. **Sprint 4: Dependencies**
   - Phase 6: Composer Dependencies updaten
   - Besonders Guzzle 7 und Smarty 4 testen

5. **Sprint 5: Optional Clean-up**
   - Phase 4: Hash-Funktionen (nur Security-relevante)

**Vorteil:** Testbar, risikoarm, inkrementell deploybar

### ❌ Option B: Big Bang
Alle Änderungen auf einmal machen.

**Nachteil:** Hohes Risiko, schwer zu debuggen, lange Test-Phase

---

## Rollback-Strategie

Für jeden Sprint:
1. Git-Branch erstellen: `feature/php84-sprint-X`
2. Vollständiges Datenbank-Backup vor Deployment
3. Code-Snapshot mit `git tag v1.12.x-pre-php84-sprintX`
4. Monitoring für 48h nach Deployment
5. Bei kritischen Fehlern: Sofortiger Rollback

```bash
# Rollback durchführen
git checkout main
git reset --hard v1.12.x-pre-php84-sprint1
# Datenbank wiederherstellen
mysql < backup_pre_sprint1.sql
```

---

## Checkliste vor Go-Live

- [ ] Alle PHPUnit Tests bestanden
- [ ] PHPStan Level 5 ohne Fehler
- [ ] Manuelle Tests durchgeführt und dokumentiert
- [ ] Error Logs 48h sauber (keine Deprecated Warnings)
- [ ] Performance-Benchmarks vergleichbar oder besser
- [ ] Backup-Strategie getestet
- [ ] Rollback-Plan dokumentiert
- [ ] Stakeholder informiert
- [ ] Monitoring Alerts konfiguriert

---

## Zusätzliche PHP 8.4/8.5 Features nutzen

Nach erfolgreichem Update können folgende Features aktiviert werden:

### Property Hooks (PHP 8.4)
```php
class User {
    public string $name {
        set(string $value) => strtolower($value);
    }
}
```

### Array Functions (PHP 8.4)
```php
// array_find(), array_find_key(), array_any(), array_all()
$result = array_find($users, fn($u) => $u->id === 5);
```

### JIT Compiler Optimizations
PHP 8.4 verbessert den JIT Compiler - keine Code-Änderungen nötig, nur `php.ini`:

```ini
opcache.enable=1
opcache.jit_buffer_size=100M
opcache.jit=tracing
```

