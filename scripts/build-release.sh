#!/usr/bin/env bash
set -euo pipefail
root=$(cd "$(dirname "$0")/.." && pwd)
cd "$root"
rm -f tutor.zip
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-interaction --no-scripts --optimize-autoloader
python3 scripts/package.py tutor.zip
php scripts/verify-package.php tutor.zip
