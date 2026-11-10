# OpenXE Docker Setup mit Fork-Unterstützung

Dieses Docker-Setup ermöglicht es, OpenXE einfach zu testen und zu entwickeln, einschließlich der Möglichkeit, verschiedene Forks und Branches zu verwenden.

## Features

- **Multi-Repository Support**: Teste jeden OpenXE Fork oder Branch
- **Automatische Datenbank-Initialisierung**: Nutzt das OpenXE Upgrade-System
- **Automatische Updates**: Führt Datenbank-Upgrades bei jedem Start aus
- **Persistente Daten**: Userdata bleibt zwischen Container-Neustarts erhalten

## Voraussetzungen

- Docker und Docker Compose installiert
- Mindestens 2GB freier RAM
- Ports 80 und 3306 verfügbar (oder in docker-compose.yml anpassen)

## Schnellstart

### 1. Original OpenXE Repository (Standard)

```bash
cd /Users/sgo0002t/Desktop/openxe/OpenXE
docker-compose up -d
```

Dies verwendet den lokalen Code im aktuellen Verzeichnis.

### 2. Einen Fork testen

Um einen Fork zu testen, setze die Umgebungsvariablen `REPO_URL` und `BRANCH`:

```bash
export REPO_URL=https://github.com/Avatarsia/OpenXE.git
export BRANCH=api-doc-2
docker-compose up -d --build
```

**Wichtige Beispiele:**

```bash
# Avatarsia Fork, Branch api-doc-2
export REPO_URL=https://github.com/Avatarsia/OpenXE.git
export BRANCH=api-doc-2

# Original OpenXE Repository, main Branch
export REPO_URL=https://github.com/OpenXE-org/OpenXE.git
export BRANCH=main

# Ein anderer Branch aus dem Original
export REPO_URL=https://github.com/OpenXE-org/OpenXE.git
export BRANCH=develop
```

### 3. Mit Beispieldaten

Um Beispieldaten zu importieren:

```bash
export IMPORT_SAMPLE=true
export REPO_URL=https://github.com/OpenXE-org/OpenXE.git
export BRANCH=main
docker-compose up -d --build
```

### 4. Mit eigenem Admin-Passwort

Um ein eigenes Admin-Passwort zu setzen:

```bash
export ADMIN_PASSWORD=MeinSicheresPasswort123
export REPO_URL=https://github.com/OpenXE-org/OpenXE.git
export BRANCH=main
docker-compose up -d --build
```

Der Admin-User wird automatisch bei der ersten Initialisierung erstellt.

## Umgebungsvariablen

| Variable | Standard | Beschreibung |
|----------|---------|--------------|
| `REPO_URL` | `local` | Git Repository URL oder `local` für lokalen Code |
| `BRANCH` | `main` | Git Branch der ausgecheckt werden soll |
| `IMPORT_SAMPLE` | `false` | `true` um Beispieldaten zu importieren |
| `ADMIN_PASSWORD` | `openxe` | Passwort für den Admin-User |
| `DB_HOST` | `db` | MySQL/MariaDB Hostname |
| `DB_NAME` | `openxe` | Datenbank Name |
| `DB_USER` | `openxe` | Datenbank Benutzer |
| `DB_PASSWORD` | `openxe` | Datenbank Passwort |

## Zugriff

Nach dem Start ist OpenXE erreichbar unter:

- **Web-Interface**: http://localhost
  - **Login**: `admin`
  - **Passwort**: `openxe` (oder der Wert von `ADMIN_PASSWORD`)

- **MySQL**: localhost:3306
  - Benutzer: `openxe`
  - Passwort: `openxe`
  - Datenbank: `openxe`

## Container verwalten

### Status prüfen
```bash
docker-compose ps
```

### Logs anzeigen
```bash
# Alle Logs
docker-compose logs -f

# Nur OpenXE App Logs
docker logs openxe-app -f

# Nur Datenbank Logs
docker logs openxe-db -f
```

### Container stoppen
```bash
docker-compose down
```

### Container stoppen und Datenbank löschen
```bash
docker-compose down -v
```

### Neustart mit neuem Fork/Branch

```bash
# Alte Container stoppen und Datenbank löschen
docker-compose down -v

# Neuen Fork konfigurieren
export REPO_URL=https://github.com/IhrUsername/OpenXE.git
export BRANCH=ihr-branch

# Neu starten
docker-compose up -d --build
```

## Entwicklungs-Workflow

### Lokalen Code testen

1. Klone das Repository:
   ```bash
   git clone https://github.com/OpenXE-org/OpenXE.git OpenXE
   cd OpenXE
   ```

2. Mache deine Änderungen im Code

3. Starte mit lokalem Code:
   ```bash
   unset REPO_URL  # oder export REPO_URL=local
   docker-compose up -d --build
   ```

4. Der Container verwendet nun deinen lokalen Code aus dem aktuellen Verzeichnis

### Fork testen

1. Setze Fork und Branch:
   ```bash
   export REPO_URL=https://github.com/Avatarsia/OpenXE.git
   export BRANCH=api-doc-2
   ```

2. Starte Container:
   ```bash
   docker-compose down -v  # Alte Daten löschen
   docker-compose up -d --build
   ```

3. Der Container klont automatisch den Fork beim Start

### Zwischen Forks wechseln

```bash
# Stoppe alte Container und lösche Datenbank
docker-compose down -v

# Wechsle zu anderem Fork
export REPO_URL=https://github.com/anderer-user/OpenXE.git
export BRANCH=feature-xyz

# Starte neu
docker-compose up -d --build
```

## Problembehandlung

### Container startet nicht

1. Logs prüfen:
   ```bash
   docker logs openxe-app
   docker logs openxe-db
   ```

2. Container stoppen und neu bauen:
   ```bash
   docker-compose down -v
   docker-compose up -d --build
   ```

### Datenbank-Fehler

Wenn Datenbank-Fehler auftreten (z.B. fehlende Spalten):

```bash
# Container stoppen und Datenbank löschen
docker-compose down -v

# Neu starten - das Upgrade-System initialisiert die DB neu
docker-compose up -d
```

### Port bereits belegt

Wenn Port 80 oder 3306 bereits belegt ist, passe die Ports in docker-compose.yml an:

```yaml
services:
  openxe:
    ports:
      - "8080:80"  # OpenXE auf Port 8080

  db:
    ports:
      - "3307:3306"  # MySQL auf Port 3307
```

### Upgrade-Probleme

Falls das automatische Upgrade fehlschlägt, führe es manuell aus:

```bash
docker exec -it openxe-app bash
cd /var/www/html/upgrade
php data/upgrade.php -db -do
exit
```

## Datenbank-Migration

### Datenbank exportieren

```bash
docker exec openxe-db mysqldump -u openxe -popenxe openxe > backup.sql
```

### Datenbank importieren

```bash
docker exec -i openxe-db mysql -u openxe -popenxe openxe < backup.sql
```

## Architektur

Das Setup besteht aus:

- **Dockerfile**: Baut das OpenXE PHP/Apache Image
  - PHP 8.1 mit allen benötigten Extensions
  - Apache mit mod_rewrite
  - Git für Repository-Klonen

- **docker-compose.yml**: Orchestriert die Container
  - OpenXE App Container
  - MariaDB 10.11 Container
  - Volumes für persistente Daten

- **docker-entrypoint-init.sh**: Initialisierungs-Script
  - Klont Repository wenn REPO_URL gesetzt ist
  - Erstellt Datenbank-Konfiguration
  - Führt OpenXE Upgrade-System aus
  - Startet Apache

## Persistente Daten

- **./userdata**: OpenXE Benutzerdaten (gemounted)
- **dbdata (Volume)**: MariaDB Datenbank (Docker Volume)

Die Datenbank bleibt zwischen Container-Neustarts erhalten, außer du löschst sie explizit mit `docker-compose down -v`.

## Technische Details

### Automatisches Upgrade

Bei jedem Container-Start:
1. Prüft das Script, ob die Datenbank leer ist
2. Wenn leer: Führt `upgrade.php -db -do` aus (initialisiert Datenbank)
3. Wenn nicht leer: Führt trotzdem `upgrade.php -db -do` aus (für Updates)

Dies stellt sicher, dass die Datenbank immer auf dem neuesten Stand ist.

### Repository-Klonen

Wenn `REPO_URL` auf einen Git-Repository-Link gesetzt ist:
1. Löscht alle Dateien außer `userdata` und `conf`
2. Klont das Repository in einen temporären Ordner
3. Kopiert alle Dateien (außer .git) nach `/var/www/html`
4. Setzt die Berechtigungen für www-data

### Lokaler Code

Wenn `REPO_URL=local` oder nicht gesetzt:
1. Verwendet den Code aus dem Build-Context (aktuelles Verzeichnis)
2. Kopiert ihn nach `/var/www/html`
3. Führt normale Initialisierung aus

## Lizenz

Dieses Docker-Setup ist Open Source. OpenXE selbst ist unter der EGPL 3.1 lizenziert.

## Support

Bei Problemen:
1. Prüfe die Logs: `docker logs openxe-app`
2. Prüfe die Container: `docker-compose ps`
3. Erstelle ein Issue im jeweiligen Repository
