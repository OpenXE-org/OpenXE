#!/bin/bash
set -eo pipefail

# Configuration defaults
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_NAME="${DB_NAME:-openxe}"
DB_USER="${DB_USER:-openxe}"
DB_PASSWORD="${DB_PASSWORD:-openxe}"
USERDATA_PATH="${USERDATA_PATH:-/var/www/html/userdata}"
AUTO_MIGRATE="${AUTO_MIGRATE:-false}"
IMPORT_SAMPLE="${IMPORT_SAMPLE:-false}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-openxe}"
DB_WAIT_TIMEOUT="${DB_WAIT_TIMEOUT:-60}"

ensure_config() {
    local config_file="/var/www/html/conf/user.inc.php"
    if [ ! -f "$config_file" ]; then
        echo "[OpenXE Entrypoint] Generating ${config_file} from environment variables..."
        mkdir -p /var/www/html/conf
        cat > "$config_file" <<EOF
<?php
\$this->WFdbhost='${DB_HOST}';
\$this->WFdbport=${DB_PORT};
\$this->WFdbname='${DB_NAME}';
\$this->WFdbuser='${DB_USER}';
\$this->WFdbpass='${DB_PASSWORD}';
\$this->WFuserdata='${USERDATA_PATH}';
EOF
        if [ "$(id -u)" = "0" ]; then
            chown www-data:www-data "$config_file"
            chmod 640 "$config_file"
        fi
    else
        echo "[OpenXE Entrypoint] Existing ${config_file} detected, preserving configuration."
    fi
}

ensure_userdata_dirs() {
    if [ "$(id -u)" = "0" ]; then
        mkdir -p "${USERDATA_PATH}/tmp" "${USERDATA_PATH}/dms" "/var/www/html/www/cache"
        chown -R www-data:www-data "${USERDATA_PATH}" "/var/www/html/www/cache"
    fi
}

wait_for_db() {
    if [ -z "$DB_HOST" ]; then
        return 0
    fi

    echo "[OpenXE Entrypoint] Waiting for database at ${DB_HOST}:${DB_PORT} (timeout: ${DB_WAIT_TIMEOUT}s)..."
    local start_time
    start_time=$(date +%s)

    until mysql --skip-ssl -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
        local current_time
        current_time=$(date +%s)
        local elapsed=$((current_time - start_time))

        if [ "$elapsed" -ge "$DB_WAIT_TIMEOUT" ]; then
            echo "[OpenXE Entrypoint] ERROR: Database connection timed out after ${DB_WAIT_TIMEOUT} seconds!" >&2
            exit 1
        fi

        echo "[OpenXE Entrypoint] Database not ready yet, waiting... (${elapsed}s/${DB_WAIT_TIMEOUT}s)"
        sleep 2
    done

    echo "[OpenXE Entrypoint] Database is ready and reachable."
}

run_migrate() {
    echo "[OpenXE Entrypoint] Checking database status for '${DB_NAME}'..."
    cd /var/www/html/upgrade

    local table_count
    table_count=$(mysql --skip-ssl -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -sN -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME';" 2>/dev/null || echo "0")
    table_count=$(echo "$table_count" | tr -d '[:space:]')
    table_count=${table_count:-0}

    echo "[OpenXE Entrypoint] Existing tables in '${DB_NAME}': ${table_count}"

    if [ "$table_count" -eq 0 ] 2>/dev/null; then
        echo "[OpenXE Entrypoint] Database is empty. Running initial schema installation via upgrade.php..."
        php data/upgrade.php -db -do

        if [ "$IMPORT_SAMPLE" = "true" ]; then
            if [ -f /var/www/html/database/beispiel.sql ]; then
                echo "[OpenXE Entrypoint] Importing sample data (beispiel.sql)..."
                mysql --skip-ssl -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < /var/www/html/database/beispiel.sql || true
            fi
        fi

        # Seed initial admin user if not present
        local user_exists
        user_exists=$(mysql --skip-ssl -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -sN -e "SELECT COUNT(*) FROM user WHERE username = '$ADMIN_USER';" 2>/dev/null || echo "0")
        user_exists=$(echo "$user_exists" | tr -d '[:space:]')

        if [ "$user_exists" -eq 0 ] 2>/dev/null; then
            echo "[OpenXE Entrypoint] Creating initial administrator user '${ADMIN_USER}'..."
            local pass_hash
            pass_hash=$(php -r "echo password_hash('$ADMIN_PASSWORD', PASSWORD_BCRYPT);")
            mysql --skip-ssl -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "
                INSERT INTO user (username, passwordhash, type, activ, role, description)
                VALUES ('$ADMIN_USER', '$pass_hash', 'admin', 1, 'Administrator', 'Initial System Administrator');
            "
            echo "[OpenXE Entrypoint] Administrator user '${ADMIN_USER}' created."
        fi
    else
        echo "[OpenXE Entrypoint] Applying pending database schema upgrades (upgrade.php -db -do)..."
        php data/upgrade.php -db -do
    fi

    echo "[OpenXE Entrypoint] Database migration finished successfully."
}

# Main Command Dispatcher
case "$1" in
    migrate)
        ensure_config
        ensure_userdata_dirs
        wait_for_db
        run_migrate
        exit 0
        ;;
    check)
        ensure_config
        wait_for_db
        echo "[OpenXE Entrypoint] Running dry-run database check..."
        cd /var/www/html/upgrade
        php data/upgrade.php -db
        exit 0
        ;;
    apache|apache2-foreground|"")
        ensure_config
        ensure_userdata_dirs
        wait_for_db
        if [ "$AUTO_MIGRATE" = "true" ]; then
            run_migrate
        fi
        echo "[OpenXE Entrypoint] Starting Apache web server..."
        exec apache2-foreground
        ;;
    *)
        ensure_config
        exec "$@"
        ;;
esac
