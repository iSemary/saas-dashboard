#!/usr/bin/env bash

# Dev autorun: opens ONE gnome-terminal window with 3 tabs:
#   1. Backend  — runs ./autorun.sh
#   2. Tenant   — npm run dev in tenant-frontend
#   3. Landlord — npm run dev in landlord-frontend
#
# Usage: ./dev.autorun.sh [autorun-args...]

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$SCRIPT_DIR"
LANDLORD_DIR="$ROOT_DIR/landlord-frontend"
TENANT_DIR="$ROOT_DIR/tenant-frontend"

if ! command -v gnome-terminal >/dev/null 2>&1; then
  echo "Error: gnome-terminal is not installed." >&2
  exit 1
fi

AUTORUN_ARGS="$*"

gnome-terminal \
  --tab --title="Backend"  --working-directory="$ROOT_DIR"     -- bash -c "./autorun.sh $AUTORUN_ARGS; exec bash" \
  --tab --title="Tenant"   --working-directory="$TENANT_DIR"   -- bash -c "npm run dev; exec bash" \
  --tab --title="Landlord" --working-directory="$LANDLORD_DIR" -- bash -c "npm run dev; exec bash"
