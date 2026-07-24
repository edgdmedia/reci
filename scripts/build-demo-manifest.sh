#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"

echo "This helper documents where a remote demo manifest should be generated and published."
echo ""
echo "Expected remote outputs:"
echo "  - JSON manifest URL referenced by reci_remote_demo_manifest_url()"
echo "  - Import payloads hosted at stable, cacheable URLs"
echo ""
echo "For now, keep local demo-content/ in the client package until remote import is proven."
echo ""
echo "Manifest schema reference: $ROOT_DIR/docs/demo-content-manifest.schema.json"
