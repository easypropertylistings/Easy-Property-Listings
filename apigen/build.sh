#!/bin/sh
#
# Builds the EPL code reference into epl-apidocs/.
#
#   sh apigen/build.sh                 # for docs.easypropertylistings.com.au
#   sh apigen/build.sh http://localhost:8888/epl-docs-preview/   # links for a local preview
#
# 1. ApiGen 7 (pinned, installed once into ~/.epl-apigen, outside the plugin).
# 2. The hook list (hook-docs.html).
# 3. The EPL stylesheet, .htaccess redirects for old URLs, and robots.txt.
#
# The title's version comes from the plugin header, so there is nothing to edit per release.

set -e

APIGEN_VERSION='7.0.0-alpha.6'
APIGEN_DIR="${EPL_APIGEN_DIR:-$HOME/.epl-apigen}"
PLUGIN_DIR="$(cd "$(dirname "$0")/.." && pwd)"
OUTPUT_DIR="$PLUGIN_DIR/epl-apidocs"
BASE_URL="${1:-https://docs.easypropertylistings.com.au/}"

cd "$PLUGIN_DIR"

VERSION="$(sed -n 's/^ \* Version: *//p' easy-property-listings.php | head -1 | tr -d '[:space:]')"
if [ -z "$VERSION" ]; then
	echo 'No "Version:" in easy-property-listings.php.' >&2
	exit 1
fi

if [ "$(cat "$APIGEN_DIR/.epl-installed-version" 2>/dev/null)" != "$APIGEN_VERSION" ]; then
	echo "Installing ApiGen $APIGEN_VERSION into $APIGEN_DIR"
	rm -rf "${APIGEN_DIR:?}"
	composer create-project --no-interaction --no-dev --quiet apigen/apigen "$APIGEN_DIR" "$APIGEN_VERSION"
	echo "$APIGEN_VERSION" > "$APIGEN_DIR/.epl-installed-version"
fi

rm -rf "${OUTPUT_DIR:?}"

# Warnings about missing WordPress and Elementor classes are expected: they aren't part of EPL.
php -d memory_limit=2G "$APIGEN_DIR/bin/apigen" \
	--title "Easy Property Listings $VERSION Code Reference" \
	--base-url "$BASE_URL" \
	--output "$OUTPUT_DIR" \
	--quiet

php apigen/hook-docs.php "$OUTPUT_DIR"

cp apigen/theme/assets/* "$OUTPUT_DIR/assets/"
cp apigen/htaccess "$OUTPUT_DIR/.htaccess"
printf 'User-agent: *\nDisallow:\nSitemap: %ssitemap.xml\n' "$BASE_URL" > "$OUTPUT_DIR/robots.txt"

echo "Easy Property Listings $VERSION code reference: $OUTPUT_DIR ($(ls "$OUTPUT_DIR" | wc -l | tr -d ' ') files)"
