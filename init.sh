#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DOCS="${ROOT}/docs"

mkdir -p "${DOCS}"

SITE_STATIC_EXPORT=1 SITE_LANG=en php "${ROOT}/index.php" > "${DOCS}/index.html"
SITE_STATIC_EXPORT=1 SITE_LANG=ru php "${ROOT}/index.php" > "${DOCS}/ru.html"

for name in css img js; do
  src="${ROOT}/ref/${name}"
  if [[ -d "${src}" ]]; then
    mkdir -p "${DOCS}/${name}"
    rsync -a "${src}/" "${DOCS}/${name}/"
    echo "Copied ${src}/ -> ${DOCS}/${name}/"
  else
    echo "Skip (no dir): ${src}" >&2
  fi
done

echo "Created ${DOCS}/index.html and ${DOCS}/ru.html"
