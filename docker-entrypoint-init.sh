#!/bin/bash
set -e

# user.inc.php automatisch mit Umgebungsvariablen füllen, nur wenn noch nicht vorhanden
if [ ! -f /var/www/html/conf/user.inc.php ]; then
  echo "[Entrypoint] Erstelle user.inc.php ..."
  mkdir -p /var/www/html/conf
  cat > /var/www/html/conf/user.inc.php <<EOF
<?php
\$this->WFdbhost='${DB_HOST:-db}';
\$this->WFdbname='${DB_NAME:-openxe}';
\$this->WFdbuser='${DB_USER:-openxe}';
\$this->WFdbpass='${DB_PASSWORD:-openxe}';
\$this->WFuserdata='/var/www/html/userdata';
EOF
  chown www-data:www-data /var/www/html/conf/user.inc.php
else
  echo "[Entrypoint] user.inc.php existiert bereits, überspringe Erstellung."
fi

echo "[Entrypoint-DEBUG] REPO_URL=$REPO_URL"
# Standardwerte
BRANCH="${BRANCH:-main}"
REPO_URL="${REPO_URL:-local}"
IMPORT_SAMPLE="${IMPORT_SAMPLE:-false}"

# Nur klonen, wenn explizit ein anderes REPO_URL als das lokale Repo gesetzt wurde
if [ -n "$REPO_URL" ] && [ "$REPO_URL" != "local" ]; then
  echo "[Entrypoint] Klone Repo $REPO_URL (Branch: $BRANCH) ..."

  # Lösche alles außer userdata und conf
  find /var/www/html -mindepth 1 -not -path '/var/www/html/userdata*' -not -path '/var/www/html/conf*' -delete 2>/dev/null || true

  # Klone Repository
  git clone --branch "$BRANCH" --single-branch "$REPO_URL" /var/www/html-tmp

  # Kopiere alle Dateien außer .git ins Ziel
  shopt -s dotglob
  cp -r /var/www/html-tmp/* /var/www/html/ 2>/dev/null || true
  rm -rf /var/www/html-tmp

  chown -R www-data:www-data /var/www/html
else
  echo "[Entrypoint] Lokaler Code wird verwendet, kein Klonen."
  # Kopiere lokalen Code wenn /var/www/html leer ist
  if [ ! -f /var/www/html/index.php ]; then
    echo "[Entrypoint] Kopiere lokalen Code ..."
    shopt -s dotglob
    cp -r /var/www/html-local/* /var/www/html/ 2>/dev/null || true
    chown -R www-data:www-data /var/www/html
  fi
fi

# Warten bis DB erreichbar ist
if [ -n "$DB_HOST" ]; then
  echo "[Entrypoint] Warte auf Datenbank ($DB_HOST) ..."
  until mysql --skip-ssl -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
    echo "[Entrypoint] Warte auf MySQL..."
    sleep 2
  done
  echo "[Entrypoint] Datenbank ist erreichbar."
fi

# Prüfe ob DB leer ist und importiere ggf. Struktur und Beispieldaten
echo "[Entrypoint] Prüfe Datenbank-Status ..."

# MySQL-Client verwenden statt PHP
TABLES=$(mysql --skip-ssl -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -sN -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME';" 2>/dev/null || echo "0")
TABLES=$(echo "$TABLES" | tr -d '[:space:]')

# Fallback auf 0, wenn TABLES leer ist
TABLES=${TABLES:-0}

echo "[Entrypoint] Gefundene Tabellen: $TABLES"

if [ "$TABLES" -eq 0 ] 2>/dev/null; then
  echo "[Entrypoint] Datenbank ist leer, initialisiere mit OpenXE Upgrade-System ..."

  # Nutze das OpenXE Upgrade-System für eine saubere Initialisierung
  if [ -f /var/www/html/upgrade/data/upgrade.php ]; then
    echo "[Entrypoint] Führe OpenXE Upgrade (upgrade.php -db -do) aus ..."
    cd /var/www/html/upgrade
    php data/upgrade.php -db -do
    if [ $? -eq 0 ]; then
      echo "[Entrypoint] OpenXE Upgrade erfolgreich abgeschlossen."
    else
      echo "[Entrypoint] WARNUNG: OpenXE Upgrade hat Fehler gemeldet (kann normal sein)."
    fi
  else
    echo "[Entrypoint] WARNUNG: upgrade.php nicht gefunden, versuche manuellen Import ..."

    # Fallback: Manueller Import
    if [ -f /var/www/html/database/struktur.sql ]; then
      echo "[Entrypoint] Importiere Datenbankstruktur (struktur.sql) ..."
      # Setze SQL Mode um DEFINER und Strict Mode Probleme zu vermeiden
      mysql --skip-ssl -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "SET GLOBAL sql_mode='NO_ENGINE_SUBSTITUTION';"
      sed 's/DEFINER=[^ ]*//g' /var/www/html/database/struktur.sql | \
        mysql --skip-ssl -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME"
      if [ $? -eq 0 ]; then
        echo "[Entrypoint] Struktur erfolgreich importiert."
      else
        echo "[Entrypoint] FEHLER beim Import der Struktur!"
      fi
    fi
  fi

  # Importiere Beispieldaten, wenn IMPORT_SAMPLE=true
  if [ "$IMPORT_SAMPLE" = "true" ]; then
    if [ -f /var/www/html/database/beispiel.sql ]; then
      echo "[Entrypoint] Importiere Beispieldaten (beispiel.sql) ..."
      mysql --skip-ssl -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < /var/www/html/database/beispiel.sql
      if [ $? -eq 0 ]; then
        echo "[Entrypoint] Beispieldaten erfolgreich importiert."
      else
        echo "[Entrypoint] WARNUNG: Fehler beim Import der Beispieldaten."
      fi
    else
      echo "[Entrypoint] WARNUNG: beispiel.sql nicht gefunden unter /var/www/html/database/beispiel.sql"
    fi
  else
    echo "[Entrypoint] IMPORT_SAMPLE ist nicht aktiviert, überspringe Beispieldaten."
  fi

  # Erstelle Admin-User mit Passwort
  ADMIN_PASSWORD="${ADMIN_PASSWORD:-openxe}"
  echo "[Entrypoint] Erstelle Admin-User (Login: admin, Passwort: $ADMIN_PASSWORD) ..."

  # Generiere Passwort-Hash (bcrypt für OpenXE)
  ADMIN_PASSWORD_HASH=$(php -r "echo password_hash('$ADMIN_PASSWORD', PASSWORD_BCRYPT);")

  # Prüfe ob User bereits existiert
  USER_EXISTS=$(mysql --skip-ssl -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -sN -e "SELECT COUNT(*) FROM user WHERE username='admin';" 2>/dev/null || echo "0")
  USER_EXISTS=$(echo "$USER_EXISTS" | tr -d '[:space:]')
  USER_EXISTS=${USER_EXISTS:-0}

  if [ "$USER_EXISTS" -eq 0 ]; then
    # Erstelle neuen Admin-User
    mysql --skip-ssl -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" <<EOF
-- Setze SQL Mode für Insert
SET SESSION sql_mode = '';

-- Erstelle Admin-User mit bcrypt-Hash und Rolle
INSERT INTO user (username, passwordhash, type, activ, role, description)
VALUES ('admin', '$ADMIN_PASSWORD_HASH', 'admin', 1, 'Administrator', 'System Administrator');

EOF

    if [ $? -eq 0 ]; then
      echo "[Entrypoint] Admin-User erfolgreich erstellt (Login: admin / Passwort: $ADMIN_PASSWORD)"
    else
      echo "[Entrypoint] WARNUNG: Fehler beim Erstellen des Admin-Users"
    fi
  else
    echo "[Entrypoint] Admin-User existiert bereits, überspringe Erstellung."
  fi
else
  echo "[Entrypoint] Datenbank enthält bereits $TABLES Tabellen, kein Import notwendig."

  # Führe Upgrade aus, um sicherzustellen, dass die Datenbank auf dem neuesten Stand ist
  if [ -f /var/www/html/upgrade/data/upgrade.php ]; then
    echo "[Entrypoint] Prüfe auf Datenbank-Updates (upgrade.php -db -do) ..."
    cd /var/www/html/upgrade
    php data/upgrade.php -db -do
    if [ $? -eq 0 ]; then
      echo "[Entrypoint] Datenbank-Updates erfolgreich angewendet."
    else
      echo "[Entrypoint] Keine Updates notwendig oder Fehler aufgetreten."
    fi
  fi
fi

echo "[Entrypoint] Starte Apache ..."

# Starte Apache im Vordergrund
exec apache2-foreground
