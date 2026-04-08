# NetworkPrinter Module Wrapper

Duenner OpenXE-Modul-Wrapper fuer die `NetworkPrinter`-Library, die unter
`www/lib/Printer/NetworkPrinter/` als Drucker-Plugin liegt. Dieser Wrapper
fuegt nichts an der Library hinzu — er macht sie lediglich ueber den OpenXE
DI-Container erreichbar, damit andere Module den Drucker per Service-Lookup
holen koennen, ohne den `require_once`-Pfad an mehreren Stellen zu duplizieren.

## Architecture Overview

OpenXE laedt Drucker-Plugins zur Laufzeit ueber
`pages/drucker.php::loadPrinterModul($modul, $id)` per direkter
`require_once`-Kette aus `www/lib/Printer/<Modul>/<Modul>.php`. Die `NetworkPrinter`-Library
folgt diesem Schema: ein globaler Klassenname (`class NetworkPrinter extends
PrinterBase`), kein Composer, kein PSR-4-Namespace. Das ist Absicht — nur so
findet `loadPrinterModul()` die Klasse per Filesystem-Konvention.

Dieser Modul-Wrapper liegt parallel dazu unter
`classes/Modules/NetworkPrinter/`. Er enthaelt keine Geschaeftslogik, sondern
nur einen `Bootstrap.php`, der per OpenXE-Service-Auto-Discovery (`registerServices()`)
die Library laedt und eine Factory-Closure als DI-Service registriert.

### Warum kein Code-Umzug

1. **Update-Sicherheit**: Die Library ist als Pull-Request
   [openxe-org/openxe#257](https://github.com/openxe-org/openxe/pull/257)
   bei upstream offen. Beim Merge wuerde ein parallel umgezogener Code-Stand
   im Fork harte Konflikte erzeugen.
2. **Existierende Aufrufer**: `loadPrinterModul()` erwartet die Klasse in
   `www/lib/Printer/<Name>/<Name>.php`. Ein Umzug nach `classes/Modules/...`
   wuerde das Plugin fuer den Drucker-Spooler unsichtbar machen.
3. **Globale Klassen**: Die Library nutzt root-namespaced
   `class NetworkPrinter extends PrinterBase`. Ein nachtraegliches
   `namespace Xentral\...` zu setzen wuerde die Auto-Loading-Konvention
   brechen.

Der Wrapper referenziert die Library ausschliesslich per Pfad, nicht per
Symbol-Import. Dadurch ueberlebt er einen upstream-Merge ohne Aenderung.

## File Layout

```
classes/Modules/NetworkPrinter/
  Bootstrap.php         # DI-Registrierung + Factory-Closure
  README.md             # diese Datei

www/lib/Printer/NetworkPrinter/         (UNVERAENDERT, Plugin-Pfad)
  NetworkPrinter.php    # Hauptklasse, extends PrinterBase
  PrinterType.php
  Protocol.php
  Driver/
    DriverInterface.php
    EscPosDriver.php
    IppDriver.php
    LprDriver.php
    RawDriver.php
  Exception/
    PrinterException.php
    PrinterCommunicationException.php
    PrinterConfigException.php
    PrinterConnectionException.php
    PrinterProtocolException.php
  Status/
    StatusMonitor.php
  Util/
    ConnectionTest.php
    IppEncoder.php
    PdfBatcher.php
```

## Requirements

- PHP 7.4 bis 8.5
- Keine Composer-Dependencies
- Keine zusaetzlichen PHP-Extensions ueber das hinaus, was OpenXE selbst
  voraussetzt (`sockets`, `openssl`, optional `snmp` fuer Status-Monitoring)

## Usage

```php
/** @var \Xentral\Core\DependencyInjection\ContainerInterface $container */
$factory = $container->get('NetworkPrinterFactory');

// $printerId = id eines Eintrags in der Tabelle `drucker` mit anbindung='NetworkPrinter'
$printer = $factory($printerId);

// Standard-OpenXE-Plugin-Methode aus PrinterBase / NetworkPrinter
$printer->printDocument($pdfPath, $copies);
```

Die Factory liefert eine `callable(int $printerId): \NetworkPrinter`. Die
eigentliche Verbindungs-Konfiguration (host, port, protocol, lpr_queue,
auth_*) zieht `NetworkPrinter` ueber `PrinterBase::getSettings()` selbst aus
der `drucker`-Tabelle anhand der ID — der Aufrufer muss nichts uebergeben
ausser der ID.

## Installation

1. Branch `feature/network-printer-module` auschecken (oder die Files manuell
   nach `classes/Modules/NetworkPrinter/` kopieren).
2. Service-Cache invalidieren, damit OpenXE den neuen Bootstrap erkennt:
   ```
   rm "{WFuserdata}/tmp/{WFdbname}/cache_services.php"
   ```
   (`{WFuserdata}` und `{WFdbname}` aus `www/conf/_config.php`.)
3. Beim naechsten Request baut OpenXE den Service-Container neu auf und
   `NetworkPrinterFactory` ist verfuegbar.

Kein Datenbank-Migration-Step, kein Install-Script. Der Drucker-Eintrag in
der `drucker`-Tabelle wird wie bei allen Druckern ueber das OpenXE-Backend
unter Stammdaten -> Drucker -> Anbindung "Netzwerkdrucker (IP)" angelegt.

## Status

Dieser Modul-Wrapper ist update-safe. Die zugrunde liegende Library
(`www/lib/Printer/NetworkPrinter/`) ist bei upstream als Pull-Request
[openxe-org/openxe#257](https://github.com/openxe-org/openxe/pull/257)
offen. Wenn der PR gemerged wird, bleibt der Wrapper kompatibel, weil er nur
auf den Pfad `www/lib/Printer/NetworkPrinter/NetworkPrinter.php` und auf den
globalen Klassennamen `\NetworkPrinter` referenziert — beides bleibt nach
einem upstream-Merge unveraendert.

## License

Siehe OpenXE-Hauptlizenz im Root des Repositories.
