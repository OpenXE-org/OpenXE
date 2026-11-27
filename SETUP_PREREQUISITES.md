# PHP & Composer Installation - Schritt-für-Schritt Anleitung

## ⚡ Schnellstart (Automatische Installation)

### Als Administrator ausführen:

1. **PowerShell als Administrator öffnen**:
   - `Windows-Taste` + `X` drücken
   - "Windows PowerShell (Administrator)" auswählen

2. **Ausführungsrichtlinie erlauben** (einmalig):
   ```powershell
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   ```

3. **Installationsskript ausführen**:
   ```powershell
   cd "c:\Users\3D Partner\Documents\OpenXe\OpenXE"
   .\install_php_composer.ps1
   ```

4. **NEUES PowerShell-Fenster öffnen** und testen:
   ```powershell
   php -v
   composer --version
   ```

---

## 🛠️ Manuelle Installation (falls Skript fehlschlägt)

### Option A: PHP 8.4 Manuell

1. **PHP herunterladen**:
   - Besuchen: https://windows.php.net/download/
   - Wählen: **PHP 8.4.x** → **VS16 x64 Thread Safe** (ZIP)
   - Herunterladen und nach `C:\PHP84` entpacken

2. **php.ini konfigurieren**:
   ```powershell
   cd C:\PHP84
   copy php.ini-production php.ini
   notepad php.ini
   ```
   
   In `php.ini` folgende Zeilen aktivieren (`;` entfernen):
   ```ini
   extension=curl
   extension=gd
   extension=mbstring
   extension=mysqli
   extension=pdo_mysql
   extension=openssl
   extension=soap
   extension=zip
   ```

3. **PHP zum PATH hinzufügen**:
   - `Windows-Taste` drücken → "Umgebungsvariablen" suchen
   - "Systemumgebungsvariablen bearbeiten" öffnen
   - "Umgebungsvariablen" → "Path" unter System → "Bearbeiten"
   - "Neu" → `C:\PHP84` hinzufügen
   - OK → OK → OK

### Option B: Composer Manuell

1. **Composer-Setup herunterladen**:
   - Besuchen: https://getcomposer.org/download/
   - **Composer-Setup.exe** herunterladen und ausführen
   - Installer wird PHP automatisch finden und konfigurieren

---

## 🧪 Installation Verifizieren

**Neue PowerShell öffnen** und testen:

```powershell
# PHP Version prüfen
php -v
# Sollte zeigen: PHP 8.4.x

# Composer Version prüfen
composer --version
# Sollte zeigen: Composer version 2.x

# Extensions prüfen
php -m | Select-String -Pattern "curl|gd|mysqli|soap"
```

---

## 🚀 Nächste Schritte nach erfolgreicher Installation

```powershell
# Zum Projekt navigieren
cd "c:\Users\3D Partner\Documents\OpenXe\OpenXE"

# Composer Dependencies installieren/aktualisieren
composer update --no-interaction

# Autoloader optimieren
composer dump-autoload --optimize

# Syntax Check
php -l composer.json
```

---

## ❌ Troubleshooting

### "php ist nicht als Cmdlet erkannt"
- **Lösung**: Neues PowerShell-Fenster öffnen nach PATH-Änderung
- **Alternative**: Vollständigen Pfad verwenden: `C:\PHP84\php.exe -v`

### "Composer requires PHP 8.x"
- **Lösung**: Sicherstellen, dass PHP 8.4 installiert ist und im PATH steht
- **Prüfen**: `where.exe php` sollte `C:\PHP84\php.exe` zeigen

### "Extension 'mysqli' not found"
- **Lösung**: `php.ini` prüfen und Extensions aktivieren
- **Pfad**: `C:\PHP84\php.ini`
- `;` vor `extension=mysqli` entfernen

### "composer update" schlägt fehl (Memory)
```powershell
php -d memory_limit=-1 C:\ProgramData\ComposerSetup\bin\composer.phar update
```

---

## 🔍 Wichtige Pfade

| Was | Pfad |
|-----|------|
| PHP Installation | `C:\PHP84` |
| PHP Executable | `C:\PHP84\php.exe` |
| PHP Config | `C:\PHP84\php.ini` |
| Composer | `C:\ProgramData\ComposerSetup\bin\composer.bat` |
| Projekt | `c:\Users\3D Partner\Documents\OpenXe\OpenXE` |

---

## ✅ Bereit für OpenXE Update

Nach erfolgreicher Installation:

1. ✅ PHP 8.4 installiert und im PATH
2. ✅ Composer installiert und funktionsfähig
3. ✅ Erforderliche Extensions aktiviert

**Weiter mit**: `PHP85_INSTALLATION_GUIDE.md` → Schritt 1
