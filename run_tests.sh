#!/usr/bin/env bash
# ITCS333 A4 — local test runner (mirrors GitHub Actions).
set -euo pipefail
cd "$(dirname "$0")"
if [ ! -d tests/vendor ]; then
  echo "Installing test dependencies (first run only)..."
  composer install --working-dir=tests --no-interaction --quiet
fi
export A4_PORT="${A4_PORT:-8091}"
php tests/vendor/bin/phpunit -c tests/phpunit.xml --testdox
