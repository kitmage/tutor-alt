#!/usr/bin/env bash
set -euo pipefail
root=$(cd "$(dirname "$0")/.." && pwd)
cd "$root"
rm -f tutor.zip
python3 scripts/package.py tutor.zip
php scripts/verify-package.php tutor.zip
