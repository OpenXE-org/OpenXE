# OpenXE Docker & Kubernetes Leitfaden

Dieses Setup stellt ein **einheitliches, schlankes und produktionsreifes Docker-Image** für OpenXE bereit, das gleichermaßen für die **lokale Entwicklung**, den **Produktionsbetrieb** und **Kubernetes-Cluster** ausgelegt ist.

---

## 1. Architektur-Prinzipien

- **Einheitliches Image (Single Source of Truth):** Dasselbe Image läuft in Entwicklung, Test und Produktion. Keine getrennten Dockerfiles, kein Konfigurations-Drift.
- **Unveränderlichkeit (Immutable Image):** Der Quellcode und die Frontend-Assets (`www/dist` via Vite) sind fest im Image gebacken. Kein instabiles `git clone` zur Laufzeit.
- **Multi-Stage Build:**
  - **Stage 1 (`frontend`):** Schlankes `node:20-alpine` baut die Vue 3 / Vite-Assets (`npm ci && npm run build`).
  - **Stage 2 (`production`):** `php:8.5-apache` (konfigurierbar über `ARG PHP_VERSION=8.5`) mit vorkompilierten PHP-Erweiterungen (`gd`, `ldap`, `imap`, `mysqli`, `pdo_mysql`, `soap`, `zip`, `bcmath`, `pcntl`, `intl`, `exif`, `opcache`).
- **12-Factor Konfiguration:** Automatische Erstellung der `conf/user.inc.php` aus Umgebungsvariablen beim Start (sofern nicht manuell als Volume eingebunden).
- **Entkoppelte Datenbank-Migrationen:** Das Image enthält einen CLI-Dispatcher für atomare, sichere Datenbank-Updates ohne Race Conditions.

---

## 2. Schnellstart (Docker Compose)

### Standard-Start (Standalone / Produktion / Test)

```bash
docker compose up -d
```

- Web-Oberfläche: [http://localhost:8081](http://localhost:8081)
- Standard-Login: `admin` / `openxe`
- Die Datenbank wird beim ersten Start automatisch initialisiert.

---

## 3. Entwicklungs-Modus (Local Development)

Für die tägliche Entwicklung am PHP- oder JavaScript-Code kann das lokale Arbeitsverzeichnis gemountet werden:

### Live PHP-Code Mount
Entferne das Kommentarzeichen in `docker-compose.yml`:
```yaml
volumes:
  - userdata:/var/www/html/userdata
  - .:/var/www/html  # Lokaler Code überlagert das Image
```

### Frontend-Entwicklung mit Vite HMR (Hot Module Replacement)
Wenn du an Vue-Komponenten oder JavaScript-Dateien arbeitest, starte den Vite-Dev-Server über das Compose-Profil `dev`:

```bash
docker compose --profile dev up
```
Vite serviert Änderungen in Echtzeit über Port `5173`.

---

## 4. Updates & Datenbank-Migrationen

### Das Problem traditioneller Git-Updates in Containern
OpenXE verfügt über ein eigenes Upgradesystem (`upgrade.sh` bzw. `upgrade/data/upgrade.php`), das in klassischen Bare-Metal- oder VM-Installationen per `git` (`git fetch`, `git checkout`) die Programmdateien aktualisiert und anschließend die Datenbank anpasst.

In Docker- und Kubernetes-Umgebungen ist ein dateibasiertes Update per Git innerhalb des Containers jedoch ein schwerwiegendes Anti-Pattern:
1. **Container sind ephemer (flüchtig):** Jeder Neustart, Node-Wechsel oder Pod-Tausch würde auf dem ursprünglichen Image-Stand neu starten – ein im Container per Git durchgeführtes Update ginge verloren.
2. **Bedeutungsverlust des Image-Tags:** Ein getaggter Container (z. B. `openxe:v1.13.0`) würde nicht mehr verlässlich die Version 1.13.0 enthalten, sondern einen beliebigen Git-Zustand.
3. **Inkonsistenz bei mehreren Replikas:** Laufen mehrere Container parallel auf einem Cluster, würden sie bei unkoordinierten Git-Pulls auseinanderdriften.
4. **Kein atomares Rollback:** Rollbacks auf frühere Stände sind bei In-Container-Git-Updates nicht mehr reproduzierbar möglich.

### Die Cloud-Native Update-Lösung
Updates erfolgen nach dem Prinzip: **Neues Image ziehen (Code-Update) + Schema-Migration ausführen (DB-Update)**.

Der datenbankbezogene Teil des OpenXE-Upgraders (`upgrade/data/upgrade.php -db -do`) wird dabei weiterhin vollständig und nativ genutzt – er wird jedoch entkoppelt und idempotent ausgeführt:

#### Update mit Docker Compose:
```bash
# 1. Neues Image für die Zielversion abrufen
docker compose pull

# 2. Datenbank-Migration ausführen (stoppt nach getaner Arbeit)
docker compose run --rm migrate

# 3. Container mit dem neuen Image neu starten
docker compose up -d
```

#### Update auf Kubernetes:
In Kubernetes darf ein Webserver-Pod niemals automatisch bei jedem Start unkontrolliert Schemata manipulieren (Gefahr paralleler Race Conditions bei mehreren Pods). Stattdessen wird die Migration als Kubernetes-Job vor dem Rollout ausgeführt:

```bash
# 1. Migrations-Job ausführen
kubectl apply -f deploy/kubernetes/job-migrate.yaml

# 2. Warten, bis der Job erfolgreich abgeschlossen ist
kubectl wait --for=condition=complete job/openxe-migrate --timeout=120s

# 3. Neues Image im Deployment ausrollen
kubectl set image deployment/openxe openxe=ghcr.io/openxe-org/openxe:v1.13.1
```

---

## 5. Subcommands des Entrypoints

Das Image unterstützt flexible Aufruf-Modi:

| Befehl | Zweck |
|---|---|
| `docker run openxe` *(Default)* | Startet den Apache-Webserver. Wenn `AUTO_MIGRATE=true` gesetzt ist, wird vorher die DB geprüft/aktualisiert. |
| `docker run openxe migrate` | Führt die Datenbank-Initialisierung bzw. das Schema-Upgrade (`upgrade/data/upgrade.php -db -do`) aus und beendet sich. |
| `docker run openxe check` | Führt eine Trockenlauf-Prüfung (Dry-Run) des Datenbankschemas aus (`upgrade.php -db`). |
| `docker run openxe <befehl>` | Führt einen beliebigen Befehl im Container aus (z. B. `bash` oder `php -v`). |

---

## 6. Umgebungsvariablen

| Variable | Standardwert | Beschreibung |
|---|---|---|
| `DB_HOST` | `db` | Hostname des MariaDB/MySQL-Servers |
| `DB_PORT` | `3306` | Port des Datenbankservers |
| `DB_NAME` | `openxe` | Name der Datenbank |
| `DB_USER` | `openxe` | Benutzername für die Datenbank |
| `DB_PASSWORD` | `openxe` | Passwort für die Datenbank |
| `AUTO_MIGRATE` | `false` | `true`: Führt beim Apache-Start automatisch DB-Upgrades aus (empfohlen für lokales Dev/Single-Node; in K8s `false` belassen). |
| `IMPORT_SAMPLE` | `false` | `true`: Importiert beim ersten Setup Beispieldaten (`database/beispiel.sql`). |
| `ADMIN_USER` | `admin` | Benutzername des initialen Administrators |
| `ADMIN_PASSWORD` | `openxe` | Passwort des initialen Administrators (bcrypt) |
| `USERDATA_PATH` | `/var/www/html/userdata` | Pfad zum persistenten Verzeichnis für Dokumente, DMS und Uploads |
| `DB_WAIT_TIMEOUT`| `60` | Maximale Wartezeit auf die Datenbankverbindung in Sekunden |
| `TZ` | `Europe/Berlin` | Zeitzone des Containers |

---

## 7. Kubernetes Deployment

Im Verzeichnis `deploy/kubernetes/` befinden sich vollständige Referenz-Manifeste:
- `deployment.yaml`: Rolling-Update Deployment mit Readiness- & Liveness-Probes (`/index.php`).
- `job-migrate.yaml`: Einmaliger Migrations-Job für Releases.
- `service.yaml`: ClusterIP-Service auf Port 80.
- `pvc.yaml`: PersistentVolumeClaim für das Verzeichnis `/var/www/html/userdata` (DMS, PDFs, Cache).
- `ingress.yaml`: Standard Ingress-Konfiguration mit `spec.ingressClassName`.
- `httproute.yaml`: Moderne Konfiguration für die Kubernetes Gateway API (`gateway.networking.k8s.io`).
- `kustomization.yaml`: Kustomize-Bündel.
