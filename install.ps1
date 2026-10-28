# PHP 8.3 Installation - Vereinfacht
$PHPVersion = "8.3.15"
$InstallDir = "$env:USERPROFILE\PHP83"
$PHPZipUrl = "https://windows.php.net/downloads/releases/php-$PHPVersion-Win32-vs16-x64.zip"

Write-Host "=== PHP 8.4 Installation ===" -ForegroundColor Cyan
Write-Host ""

# Schritt 1: Verzeichnis
Write-Host "[1/4] Erstelle $InstallDir..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path $InstallDir -Force | Out-Null
Write-Host "  OK" -ForegroundColor Green

# Schritt 2: Download
Write-Host "[2/4] Lade PHP herunter (ca. 30MB)..." -ForegroundColor Yellow
$PHPZip = "$env:TEMP\php.zip"
$ProgressPreference = 'SilentlyContinue'
Invoke-WebRequest -Uri $PHPZipUrl -OutFile $PHPZip -UseBasicParsing
Write-Host "  OK" -ForegroundColor Green

# Schritt 3: Entpacken
Write-Host "[3/4] Entpacke..." -ForegroundColor Yellow
Expand-Archive -Path $PHPZip -DestinationPath $InstallDir -Force
Write-Host "  OK" -ForegroundColor Green

# Schritt 4: Konfiguration
Write-Host "[4/4] Konfiguriere..." -ForegroundColor Yellow
Copy-Item "$InstallDir\php.ini-production" "$InstallDir\php.ini" -Force
$ext = @"


extension=curl
extension=gd
extension=mbstring
extension=mysqli
extension=pdo_mysql
extension=openssl
extension=soap
extension=zip
"@
Add-Content "$InstallDir\php.ini" $ext

# Composer
Write-Host "Installiere Composer..." -ForegroundColor Yellow
Invoke-WebRequest -Uri "https://getcomposer.org/composer.phar" -OutFile "$InstallDir\composer.phar" -UseBasicParsing
@"
@echo off
php "%~dp0composer.phar" %*
"@ | Set-Content "$InstallDir\composer.bat" -Encoding ASCII

Write-Host "  OK" -ForegroundColor Green
Remove-Item $PHPZip -Force

Write-Host ""
Write-Host "=== INSTALLATION ABGESCHLOSSEN ===" -ForegroundColor Green
Write-Host ""
Write-Host "PHP: $InstallDir\php.exe" -ForegroundColor Cyan
Write-Host "Composer: $InstallDir\composer.bat" -ForegroundColor Cyan
Write-Host ""
Write-Host "WEITER MIT:" -ForegroundColor Yellow
Write-Host ""
Write-Host "  cd 'c:\Users\3D Partner\Documents\OpenXe\OpenXE'" -ForegroundColor White
Write-Host "  & '$InstallDir\composer.bat' update" -ForegroundColor White
Write-Host ""
