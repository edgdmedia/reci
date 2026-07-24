#!/usr/bin/env bash
set -euo pipefail

status_file="${1:-/tmp/reci-deleted.txt}"

if [[ ! -f "$status_file" ]]; then
  echo "Missing status file: $status_file" >&2
  exit 1
fi

echo "Potentially missing binary/media assets:"
grep -E '\.(png|jpg|jpeg|gif|svg|pdf|mp3|m4a|aiff|avif|wpress)$' "$status_file" || true
