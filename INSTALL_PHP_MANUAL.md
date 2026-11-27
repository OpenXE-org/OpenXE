# ⚡ PHP Installation - MANUELLE ANLEITUNG (Empfohlen)

## Die automatische Installation kann aufgrund wechselnder PHP-Versionen fehlschlagen.
## Folgen Sie diesen 5 einfachen Schritten:

## Schritt 1: PHP herunterladen

1. Öffnen Sie: **https://windows.php.net/download/**
2. Wählen Sie die neueste **PHP 8.3.x** Version
3. Laden Sie herunter: **VS16 x64 Thread Safe** (ZIP-Datei, ca. 30MB)
4. Speichern Sie die Datei (z.B. `php-8.3.x-Win32-vs16-x64.zip`)

## Schritt 2: PHP entpacken

1. Erstellen Sie den Ordner: `C:\PHP83`
2. Entpacken Sie die heruntergeladene ZIP-Datei komplett nach `C:\PHP83`
3. Der Ordner sollte danach `php.exe` direkt enthalten (nicht in einem Unterordner!)

## Schritt 3: PHP konfigurieren

1. Öffnen Sie den Ordner `C:\PHP83`
2. Kopieren Sie die Datei `php.ini-production` und benennen Sie die Kopie um in `php.ini`
3. Öffnen Sie `php.ini` mit Notepad
4. Suchen Sie nach diesen Zeilen und **entfernen** Sie das `;` am Anfang:

```ini
;extension=curl        →  extension=curl
;extension=gd          →  extension=gd
;extension=mbstring    →  extension=mbstring
;extension=mysqli      →  extension=mysqli
;extension=pdo_mysql   →  extension=pdo_mysql
;extension=openssl     →  extension=openssl
;extension=soap        →  extension=soap
;extension=zip         →  extension=zip
```

5. Speichern und schließen

## Schritt 4: Composer herunterladen

1. Öffnen Sie: **https://getcomposer.org/download/**
2. Laden Sie **Composer-Setup.exe** herunter
3. Führen Sie **Composer-Setup.exe** aus
4. Der Installer wird PHP automatisch finden und konfigurieren
5. Folgen Sie den Anweisungen (Standard-Einstellungen OK)

## Schritt 5: Installation testen

Öffnen Sie **PowerShell** und führen Sie aus:

```powershell
# PHP Version prüfen
C:\PHP83\php.exe -v

# Composer Version prüfen (nach Neustart der PowerShell)
composer --version
```

**Erwartete Ausgabe:**
```
PHP 8.3.x (cli) (built: ...)
Composer version 2.x.x
```

---

## 🚀 Weiter mit OpenXE Update

Nachdem PHP und Composer installiert sind:

```powershell
cd "c:\Users\3D Partner\Documents\OpenXe\OpenXE"

# Dependencies aktualisieren (Guzzle 7, Smarty 4)
composer update --no-interaction

# Bei Erfolg:
composer dump-autoload --optimize
```

---

## ❓ Häufige Probleme

### "composer ist nicht erkannt"
- **Lösung**: Öffnen Sie ein **neues** PowerShell-Fenster
- **Oder**: Verwenden Sie vollständigen Pfad `C:\ProgramData\ComposerSetup\bin\composer.bat`

### "php.exe kann curl.dll nicht laden"
- **Ursache**: Extension nicht richtig aktiviert in php.ini
- **Lösung**:
  1. Öffnen Sie `C:\PHP83\php.ini`
  2. Suchen Sie `extension_dir` und setzen Sie: `extension_dir = "ext"`
  3. Speichern und nochmal testen

### Download-Links funktionieren nicht
- **Alternativen:** 
  - https://www.php.net/downloads
  - https://windows.php.net/qa/ (pre-release versions)

---

## ✅ Fertig!

Nach erfolgreicher Installation können Sie mit dem PHP 8.5 Update fortfahren:
→ Siehe `PHP85_INSTALLATION_GUIDE.md`
