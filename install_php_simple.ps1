# ⚡ PHP & Composer Installation - EINFACHE Variante (ohne Admin)
# Diese Version installiert PHP und Composer im Benutzerverzeichnis

$ErrorActionPreference = "Stop"

# Konfiguration
$PHPVersion = "8.4.2"
$UserDir = $env:USERPROFILE
$InstallDir = "$UserDir\PHP84"
$PHPZipUrl = "https://windows.php.net/downloads/releases/php-$PHPVersion-Win32-vs16-x64.zip"

Write-Host "=== PHP 8.4 Installation (Benutzerverzeichnis) ===" -ForegroundColor Cyan
Write-Host "Installationsort: $InstallDir" -ForegroundColor White
Write-Host ""

# Verzeichnis erstellen
Write-Host "[1/4] Erstelle Verzeichnis..." -ForegroundColor Yellow
if (!(Test-Path $InstallDir)) {
    New-Item -ItemType Directory -Path $InstallDir -Force | Out-Null
    Write-Host "  ✓ Erstellt" -ForegroundColor Green
}
else {
    Write-Host "  ✓ Existiert bereits" -ForegroundColor Green
}

# PHP herunterladen
Write-Host ""
Write-Host "[2/4] Lade PHP $PHPVersion herunter..." -ForegroundColor Yellow
Write-Host "  (Dies kann einige Minuten dauern...)" -ForegroundColor Gray
$PHPZipPath = "$env:TEMP\php-$PHPVersion.zip"
try {
    $ProgressPreference = 'SilentlyContinue'
    Invoke-WebRequest -Uri $PHPZipUrl -OutFile $PHPZipPath -UseBasicParsing
    Write-Host "  ✓ Heruntergeladen (~30 MB)" -ForegroundColor Green
}
catch {
    Write-Host "  ✗ Fehler: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "MANUELLE INSTALLATION ERFORDERLICH:" -ForegroundColor Yellow
    Write-Host "1. Öffnen Sie: https://windows.php.net/download/" -ForegroundColor White
    Write-Host "2. Laden Sie herunter: PHP 8.4.x VS16 x64 Thread Safe (ZIP)" -ForegroundColor White
    Write-Host "3. Entpacken Sie nach: $InstallDir" -ForegroundColor White
    Write-Host ""
    Read-Host "Drücken Sie Enter"
    exit 1
}

# Entpacken
Write-Host ""
Write-Host "[3/4] Entpacke PHP..." -ForegroundColor Yellow
try {
    Expand-Archive -Path $PHPZipPath -DestinationPath $InstallDir -Force
    Write-Host "  ✓ Entpackt" -ForegroundColor Green
}
catch {
    Write-Host "  ✗ Fehler: $_" -ForegroundColor Red
    exit 1
}

# Konfiguration
Write-Host ""
Write-Host "[4/4] Konfiguriere PHP..." -ForegroundColor Yellow
$phpIniPath = "$InstallDir\php.ini"
if (!(Test-Path $phpIniPath)) {
    Copy-Item "$InstallDir\php.ini-production" $phpIniPath -Force
}

# Extensions aktivieren
$extensions = @"

; === OpenXE Required Extensions ===
extension=curl
extension=gd
extension=mbstring
extension=mysqli
extension=pdo_mysql
extension=openssl
extension=soap
extension=zip
"@
Add-Content -Path $phpIniPath -Value $extensions
Write-Host "  ✓ Extensions aktiviert" -ForegroundColor Green

# Cleanup
Remove-Item $PHPZipPath -Force -ErrorAction SilentlyContinue

# Fertig
Write-Host ""
Write-Host "=== ✅ PHP Installation abgeschlossen ===" -ForegroundColor Green
Write-Host ""
Write-Host "PHP installiert in: $InstallDir" -ForegroundColor Cyan
Write-Host ""

# Composer Portable installieren
Write-Host "=== Composer Installation ===" -ForegroundColor Cyan
$ComposerDir = "$InstallDir"
$ComposerPhar = "$ComposerDir\composer.phar"
$ComposerUrl = "https://getcomposer.org/composer.phar"

Write-Host "Lade Composer herunter..." -ForegroundColor Yellow
try {
    Invoke-WebRequest -Uri $ComposerUrl -OutFile $ComposerPhar -UseBasicParsing
    Write-Host "  ✓ Composer heruntergeladen" -ForegroundColor Green
    
    # composer.bat erstellen
    $composerBat = @"
@echo off
"$InstallDir\php.exe" "$ComposerPhar" %*
"@
    $composerBat | Set-Content "$ComposerDir\composer.bat" -Encoding ASCII
    Write-Host "  ✓ composer.bat erstellt" -ForegroundColor Green
}
catch {
    Write-Host "  ✗ Fehler: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== 🎯 NÄCHSTE SCHRITTE ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. WICHTIG: Führen Sie JETZT aus (kopieren + Enter):" -ForegroundColor Yellow
Write-Host ""
Write-Host "   `$env:Path = `"`$env:Path;$InstallDir`"" -ForegroundColor White
Write-Host ""
Write-Host "2. Testen Sie die Installation:" -ForegroundColor Yellow
Write-Host ""
Write-Host "   & '$InstallDir\php.exe' -v" -ForegroundColor White
Write-Host "   & '$InstallDir\composer.bat' --version" -ForegroundColor White
Write-Host ""
Write-Host "3. OpenXE Dependencies installieren:" -ForegroundColor Yellow
Write-Host ""
Write-Host "   cd 'c:\Users\3D Partner\Documents\OpenXe\OpenXE'" -ForegroundColor White
Write-Host "   & '$InstallDir\composer.bat' update --no-interaction" -ForegroundColor White
Write-Host ""
Write-Host "=== ODER für permanente PATH-Änderung ===" -ForegroundColor Cyan
Write-Host "Als Administrator ausführen:" -ForegroundColor Yellow
Write-Host "   [Environment]::SetEnvironmentVariable('Path', [Environment]::GetEnvironmentVariable('Path', 'User') + ';$InstallDir', 'User')" -ForegroundColor White
Write-Host ""
