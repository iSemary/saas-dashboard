#!/usr/bin/env bash

# Legacy autorun — now redirects to the unified script at project root
# Use: ./autorun.sh from the saas-dashboard root instead

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

echo "⚠️  This script is deprecated. Use the unified autorun at the project root:"
echo "    cd $ROOT_DIR && ./autorun.sh"
echo ""

exec "$ROOT_DIR/autorun.sh" "$@"
