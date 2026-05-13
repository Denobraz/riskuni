#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PUBLIC="${ROOT}/public"

mkdir -p "${PUBLIC}"

SITE_STATIC_EXPORT=1 SITE_LANG=en php "${ROOT}/index.php" > "${PUBLIC}/index.html"
SITE_STATIC_EXPORT=1 SITE_LANG=ru php "${ROOT}/index.php" > "${PUBLIC}/ru.html"

for name in css img; do
  src="${ROOT}/ref/${name}"
  if [[ -d "${src}" ]]; then
    mkdir -p "${PUBLIC}/${name}"
    rsync -a "${src}/" "${PUBLIC}/${name}/"
    echo "Copied ${src}/ -> ${PUBLIC}/${name}/"
  else
    echo "Skip (no dir): ${src}" >&2
  fi
done

echo "Created ${PUBLIC}/index.html and ${PUBLIC}/ru.html"
