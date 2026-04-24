#!/usr/bin/env bash

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
FRONTEND_DIR="$ROOT_DIR/frontend"

run_in_terminal() {
  local title="$1"
  local cmd="$2"

  if command -v gnome-terminal >/dev/null 2>&1; then
    gnome-terminal -- bash -lc "cd \"$ROOT_DIR\" && $cmd; exec bash" &
  elif command -v konsole >/dev/null 2>&1; then
    konsole -e bash -lc "cd \"$ROOT_DIR\" && $cmd; exec bash" &
  elif command -v xfce4-terminal >/dev/null 2>&1; then
    xfce4-terminal -- bash -lc "cd \"$ROOT_DIR\" && $cmd; exec bash" &
  elif command -v xterm >/dev/null 2>&1; then
    xterm -T "$title" -e bash -lc "cd \"$ROOT_DIR\" && $cmd; exec bash" &
  else
    echo "No supported graphical terminal found, running '$title' in this shell."
    bash -lc "cd \"$ROOT_DIR\" && $cmd" &
  fi
}

if [ ! -f "$ROOT_DIR/artisan" ]; then
  echo "Laravel artisan file not found at $ROOT_DIR/artisan"
  exit 1
fi

if [ ! -d "$FRONTEND_DIR" ]; then
  echo "Frontend directory not found at $FRONTEND_DIR"
  exit 1
fi

run_in_terminal "Laravel Backend" "php artisan serve"
run_in_terminal "Next.js Frontend" "cd \"$FRONTEND_DIR\" && npm run dev -- -H 0.0.0.0"

echo "Laravel and Next.js dev servers starting in separate terminals (or background)."
echo ""
echo "Backend: http://localhost:8000"
echo "Frontend: http://localhost:3000 (also accessible from customer1.saas.test:3000)"
echo ""
echo "Note: Configure nginx to proxy customer1.saas.test to Next.js on port 3000"
