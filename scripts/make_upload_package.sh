#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
OUT_DIR="${ROOT_DIR}/upload_package_${TIMESTAMP}"
ARCHIVE_PATH="${OUT_DIR}.tar.gz"
INCLUDE_DB_CONNECT=0

if [[ "${1:-}" == "--with-db-connect" ]]; then
  INCLUDE_DB_CONNECT=1
fi

mkdir -p "${OUT_DIR}"
mkdir -p "${OUT_DIR}/config"

copy_dir() {
  local rel="$1"
  if [[ -d "${ROOT_DIR}/${rel}" ]]; then
    cp -a "${ROOT_DIR}/${rel}" "${OUT_DIR}/${rel}"
  fi
}

copy_file() {
  local rel="$1"
  if [[ -f "${ROOT_DIR}/${rel}" ]]; then
    mkdir -p "${OUT_DIR}/$(dirname "${rel}")"
    cp -a "${ROOT_DIR}/${rel}" "${OUT_DIR}/${rel}"
  fi
}

# Application core
copy_dir "controller"
copy_dir "model"
copy_dir "view"
copy_dir "lib"
copy_dir "resources"
copy_dir "vendor"

# Root files
copy_file "index.php"
copy_file "favicon.ico"
copy_file "favicon.png"

# Use production rewrite rules if present
if [[ -f "${ROOT_DIR}/.htaccess.production" ]]; then
  cp -a "${ROOT_DIR}/.htaccess.production" "${OUT_DIR}/.htaccess"
else
  copy_file ".htaccess"
fi

# Config files (exclude secrets by default)
copy_file "config/environment.php"
copy_file "config/import_file.php"
if [[ ${INCLUDE_DB_CONNECT} -eq 1 ]]; then
  copy_file "config/db_connect.php"
fi

cat > "${OUT_DIR}/UPLOAD_NOTE.txt" <<'EOF'
Manual upload package for Xserver.

Included:
- app source directories and vendor
- index.php, favicon files
- .htaccess (from .htaccess.production if available)
- config/environment.php
- config/import_file.php

Not included by default:
- config/db_connect.php
- config/api_keys.php
- Docker related files
- README / .git files

If needed, prepare server-side config/db_connect.php and config/api_keys.php.
EOF

tar -czf "${ARCHIVE_PATH}" -C "${ROOT_DIR}" "$(basename "${OUT_DIR}")"

echo "Created package directory:"
echo "  ${OUT_DIR}"
echo "Created archive:"
echo "  ${ARCHIVE_PATH}"
echo
echo "If you also want config/db_connect.php included:"
echo "  ./scripts/make_upload_package.sh --with-db-connect"
