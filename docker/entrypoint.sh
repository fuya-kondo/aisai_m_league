#!/usr/bin/env bash
set -euo pipefail

DB_CONNECT_FILE="/var/www/html/config/db_connect.php"

if [ ! -f "${DB_CONNECT_FILE}" ]; then
  cat > "${DB_CONNECT_FILE}" <<'PHP'
<?php

class Database
{
    private static $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: 'db';
            $port = getenv('DB_PORT') ?: '3306';
            $database = getenv('DB_DATABASE') ?: 'fuyakondo_aisaimleague';
            $username = getenv('DB_USERNAME') ?: 'aisai_user';
            $password = getenv('DB_PASSWORD') ?: 'aisai_pass';

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);
            self::$instance = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$instance;
    }
}
PHP
  echo "[entrypoint] generated config/db_connect.php"
fi

exec "$@"
