# PHP 8.4 und Composer Installation für Windows
# Dieses Skript lädt PHP 8.4 und Composer herunter und installiert sie

# Erfordert Administrator-Rechte für PATH-Änderungen
#Requires -RunAsAdministrator

$ErrorActionPreference = "Stop"

# Konfiguration
$PHPVersion = "8.4.2"
$InstallDir = "C:\PHP84"
$PHPZipUrl = "https://windows.php.net/downloads/releases/php-$PHPVersion-Win32-vs16-x64.zip"
$ComposerUrl = "https://getcomposer.org/Composer-Setup.exe"

Write-Host "=== PHP 8.4 und Composer Installation ===" -ForegroundColor Cyan
Write-Host ""

# 1. PHP herunterladen und installieren
Write-Host "[1/6] Erstelle Installationsverzeichnis..." -ForegroundColor Yellow
if (!(Test-Path $InstallDir)) {
    New-Item -ItemType Directory -Path $InstallDir -Force | Out-Null
    Write-Host "  ✓ Verzeichnis erstellt: $InstallDir" -ForegroundColor Green
} else {
    Write-Host "  ✓ Verzeichnis existiert bereits: $InstallDir" -ForegroundColor Green
}

Write-Host ""
Write-Host "[2/6] Lade PHP $PHPVersion herunter..." -ForegroundColor Yellow
$PHPZipPath = "$env:TEMP\php-$PHPVersion.zip"
try {
    Invoke-WebRequest -Uri $PHPZipUrl -OutFile $PHPZipPath -UseBasicParsing
    Write-Host "  ✓ PHP heruntergeladen" -ForegroundColor Green
} catch {
    Write-Host "  ✗ Fehler beim Download: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Manuelle Installation erforderlich:" -ForegroundColor Yellow
    Write-Host "1. Besuchen Sie: https://windows.php.net/download/" -ForegroundColor White
    Write-Host "2. Laden Sie 'PHP 8.4 (8.4.x) VS16 x64 Thread Safe' herunter" -ForegroundColor White
    Write-Host "3. Entpacken Sie nach: $InstallDir" -ForegroundColor White
    exit 1
}

Write-Host ""
Write-Host "[3/6] Entpacke PHP..." -ForegroundColor Yellow
try {
    Expand-Archive -Path $PHPZipPath -DestinationPath $InstallDir -Force
    Write-Host "  ✓ PHP entpackt nach $InstallDir" -ForegroundColor Green
} catch {
    Write-Host "  ✗ Fehler beim Entpacken: $_" -ForegroundColor Red
    exit 1
}

# PHP Konfiguration
Write-Host ""
Write-Host "[4/6] Konfiguriere PHP..." -ForegroundColor Yellow
$phpIniPath = "$InstallDir\php.ini"
if (!(Test-Path $phpIniPath)) {
    Copy-Item "$InstallDir\php.ini-production" $phpIniPath -Force
    Write-Host "  ✓ php.ini erstellt" -ForegroundColor Green
}

# Wichtige Extensions aktivieren
$requiredExtensions = @(
    "extension=curl",
    "extension=gd",
    "extension=mbstring",
    "extension=mysqli",
    "extension=pdo_mysql",
    "extension=openssl",
    "extension=soap",
    "extension=zip"
)

$phpIniContent = Get-Content $phpIniPath
$modified = $false
foreach ($ext in $requiredExtensions) {
    if ($phpIniContent -notmatch [regex]::Escape($ext)) {
        Add-Content -Path $phpIniPath -Value $ext
        $modified = $true
        Write-Host "  ✓ Aktiviert: $ext" -ForegroundColor Green
    }
}

if (!$modified) {
    Write-Host "  ✓ Extensions bereits aktiviert" -ForegroundColor Green
}

# PATH aktualisieren
Write-Host ""
Write-Host "[5/6] Füge PHP zum PATH hinzu..." -ForegroundColor Yellow
$currentPath = [Environment]::GetEnvironmentVariable("Path", "Machine")
if ($currentPath -notlike "*$InstallDir*") {
    $newPath = "$currentPath;$InstallDir"
    [Environment]::SetEnvironmentVariable("Path", $newPath, "Machine")
    $env:Path = "$env:Path;$InstallDir"
    Write-Host "  ✓ PHP zum System-PATH hinzugefügt" -ForegroundColor Green
} else {
    Write-Host "  ✓ PHP bereits im PATH" -ForegroundColor Green
}

# Composer installieren
Write-Host ""
Write-Host "[6/6] Installiere Composer..." -ForegroundColor Yellow
$ComposerExePath = "$env:TEMP\Composer-Setup.exe"
try {
    Invoke-WebRequest -Uri $ComposerUrl -OutFile $ComposerExePath -UseBasicParsing
    Write-Host "  ✓ Composer heruntergeladen" -ForegroundColor Green
    
    # Composer installieren (silent mode)
    Start-Process -FilePath $ComposerExePath -ArgumentList "/VERYSILENT", "/SUPPRESSMSGBOXES", "/NORESTART" -Wait
    Write-Host "  ✓ Composer installiert" -ForegroundColor Green
} catch {
    Write-Host "  ✗ Fehler bei Composer-Installation: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Manuelle Composer-Installation:" -ForegroundColor Yellow
    Write-Host "1. Besuchen Sie: https://getcomposer.org/download/" -ForegroundColor White
    Write-Host "2. Laden Sie 'Composer-Setup.exe' herunter und führen Sie es aus" -ForegroundColor White
}

# Cleanup
Remove-Item $PHPZipPath -Force -ErrorAction SilentlyContinue
Remove-Item $ComposerExePath -Force -ErrorAction SilentlyContinue

# Verification
Write-Host ""
Write-Host "=== Installation abgeschlossen ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Bitte öffnen Sie ein NEUES PowerShell-Fenster und führen Sie aus:" -ForegroundColor Yellow
Write-Host "  php -v" -ForegroundColor White
Write-Host "  composer --version" -ForegroundColor White
Write-Host ""
Write-Host "Danach können Sie fortfahren mit:" -ForegroundColor Yellow
Write-Host "  cd 'c:\Users\3D Partner\Documents\OpenXe\OpenXE'" -ForegroundColor White
Write-Host "  composer update --no-interaction" -ForegroundColor White
Write-Host ""

# Pause to read output
Read-Host "Drücken Sie Enter zum Beenden"
