#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

find "$ROOT/public" -type f -name "*.html" -print0 | xargs -0 -I{} perl -0777 -pe 's/<!--.*?-->//gs' -i {}

find "$ROOT/public" -type f \( -name "*.html" -o -name "*.css" -o -name "*.js" \) -print0 | xargs -0 -I{} perl -0777 -pe 's:/\*.*?\*/::gs' -i {}

find "$ROOT/public" -type f \( -name "*.html" -o -name "*.js" \) -print0 | xargs -0 perl -pe 's/^\s*\/\/.*$//mg' -i

find "$ROOT/public" -type f \( -name "*.html" -o -name "*.css" -o -name "*.js" \) -print0 | xargs -0 sed -i -E ':a;/^\n*$/{$d;N;ba};/\n{3,}/s//\n\n/g'

if [ -f "$ROOT/.env" ]; then
  sed -i -E '/^\s*#/d' "$ROOT/.env"
fi

if [ -f "$ROOT/public/.htaccess" ]; then
  sed -i -E '/^\s*#/d' "$ROOT/public/.htaccess"
fi

if [ -f "$ROOT/Dockerfile" ]; then
  sed -i -E '/^\s*#/d' "$ROOT/Dockerfile"
fi

if [ -f "$ROOT/docker-compose.yml" ]; then
  sed -i -E '/^\s*#/d' "$ROOT/docker-compose.yml"
fi

find "$ROOT" -type f -name "*.sh" ! -path "$ROOT/vendor/*" -print0 | xargs -0 -I{} sed -i -E '/^\s*#!/!{/^\s*#/d}' {}

if [ -f "$ROOT/scripts/strip_php_comments.php" ]; then
  php "$ROOT/scripts/strip_php_comments.php" public || true
fi

echo "[strip_misc] done"
