#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
STYLE_FILE="$ROOT_DIR/style.css"
DIST_DIR="$ROOT_DIR/dist"
STAGING_ROOT="$DIST_DIR/.package-staging"
THEME_SLUG="reci-media-hub"
BUMP_VERSION="${BUMP_VERSION:-0}"

if [[ ! -f "$STYLE_FILE" ]]; then
  echo "Missing style.css at $STYLE_FILE" >&2
  exit 1
fi

CURRENT_VERSION="$(grep -E '^Version:' "$STYLE_FILE" | head -n1 | sed 's/^Version:[[:space:]]*//')"
if [[ -z "$CURRENT_VERSION" ]]; then
  echo "Could not determine theme version from style.css" >&2
  exit 1
fi

VERSION="$CURRENT_VERSION"
if [[ "$BUMP_VERSION" == "1" ]]; then
  IFS='.' read -r MAJOR MINOR PATCH <<< "$CURRENT_VERSION"
  if [[ -z "${MAJOR:-}" || -z "${MINOR:-}" || -z "${PATCH:-}" ]]; then
    echo "Version '$CURRENT_VERSION' is not in MAJOR.MINOR.PATCH format" >&2
    exit 1
  fi

  NEXT_PATCH=$((PATCH + 1))
  VERSION="${MAJOR}.${MINOR}.${NEXT_PATCH}"

  perl -0pi -e 's/^Version:\h*.*/Version: '"$VERSION"'/m' "$STYLE_FILE"
  echo "Bumped theme version: $CURRENT_VERSION -> $VERSION"
else
  echo "Packaging committed theme version: $VERSION"
fi

PACKAGE_DIR="$DIST_DIR/${THEME_SLUG}-v${VERSION}"
THEME_DIR="$PACKAGE_DIR/$THEME_SLUG"
ZIP_PATH="$DIST_DIR/${THEME_SLUG}-v${VERSION}.zip"

rm -rf "$STAGING_ROOT" "$PACKAGE_DIR" "$ZIP_PATH"
mkdir -p "$THEME_DIR"

copy_path() {
  local path="$1"
  if [[ -e "$ROOT_DIR/$path" ]]; then
    mkdir -p "$(dirname "$THEME_DIR/$path")"
    cp -R "$ROOT_DIR/$path" "$THEME_DIR/$path"
  fi
}

# Core theme runtime files
copy_path "404.php"
copy_path "README.md"
copy_path "author.php"
copy_path "footer-reflection.php"
copy_path "footer.php"
copy_path "front-page.php"
copy_path "functions.php"
copy_path "header-reflection.php"
copy_path "header.php"
copy_path "index.php"
copy_path "page.php"
copy_path "postcss.config.cjs"
copy_path "style.css"
copy_path "tailwind.config.js"
copy_path "theme.json"
copy_path "tsconfig.json"
copy_path "vite.config.js"

# Runtime directories
copy_path "assets"
copy_path "demo-content" # Remove once remote demo content is live
copy_path "inc"
copy_path "legacy-block"
copy_path "modules"
copy_path "src"
copy_path "template-parts"
copy_path "templates"

# Remove dev/junk files from staged package
rm -rf "$THEME_DIR/node_modules"
rm -rf "$THEME_DIR/dist"
rm -rf "$THEME_DIR/.git"
rm -rf "$THEME_DIR/.github"
rm -rf "$THEME_DIR/.claude"
rm -rf "$THEME_DIR/scripts"
rm -rf "$THEME_DIR/sample"
rm -rf "$THEME_DIR/reflection-gallery"
rm -f "$THEME_DIR/.DS_Store"

find "$THEME_DIR" -name '.DS_Store' -delete
find "$THEME_DIR" -name 'node_modules' -type d -prune -exec rm -rf {} +

mkdir -p "$DIST_DIR"

(
  cd "$PACKAGE_DIR"
  zip -qr "$ZIP_PATH" "$THEME_SLUG"
)

echo "Package created: $ZIP_PATH"
