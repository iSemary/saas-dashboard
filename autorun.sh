#!/usr/bin/env bash

# Unified autorun: sets up and starts the entire SaaS Dashboard
# Usage: ./autorun.sh [--skip-backend] [--skip-frontend] [--skip-build] [--force]

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$SCRIPT_DIR"
BACKEND_DIR="$ROOT_DIR/backend"
LANDLORD_DIR="$ROOT_DIR/landlord-frontend"
TENANT_DIR="$ROOT_DIR/tenant-frontend"
PUBLIC_DIR="$BACKEND_DIR/public"

SKIP_BACKEND=false
SKIP_FRONTEND=false
SKIP_BUILD=false
FORCE=false

for arg in "$@"; do
  case "$arg" in
    --skip-backend)  SKIP_BACKEND=true ;;
    --skip-frontend) SKIP_FRONTEND=true ;;
    --skip-build)    SKIP_BUILD=true ;;
    --force)         FORCE=true ;;
    --help|-h)
      echo "Usage: ./autorun.sh [options]"
      echo ""
      echo "  --skip-backend   Skip backend setup (migrations, passport, etc.)"
      echo "  --skip-frontend  Skip frontend build"
      echo "  --skip-build     Skip npm build, only create symlinks"
      echo "  --force          Force rebuild frontends even if out/ exists"
      echo "  --help           Show this help"
      exit 0
      ;;
  esac
done

echo "╔══════════════════════════════════════════════════════════╗"
echo "║           🚀 SaaS Dashboard — Unified Autorun            ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""

# ─── 1. Backend Setup ────────────────────────────────────────────
if [ "$SKIP_BACKEND" = false ]; then
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
  echo "  📦 Backend Setup"
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

  cd "$BACKEND_DIR"

  # Install Composer dependencies
  if [ ! -d "vendor" ]; then
    echo "  → Installing Composer dependencies..."
    composer install --no-interaction
  else
    echo "  ✓ Composer dependencies installed"
  fi

  # Copy .env if missing
  if [ ! -f ".env" ] && [ -f ".env.example" ]; then
    echo "  → Copying .env.example → .env"
    cp .env.example .env
    php artisan key:generate --force
  fi

  # Run landlord migrations + seed
  if [ "$FORCE" = true ] || [ ! -f ".setup-done-landlord" ]; then
    echo "  → Running landlord:setup..."
    php artisan landlord:setup --force || true
    touch .setup-done-landlord
  else
    echo "  ✓ Landlord setup already done (use --force to redo)"
  fi

  # Run tenant migrations + seed
  if [ "$FORCE" = true ] || [ ! -f ".setup-done-tenant" ]; then
    echo "  → Running tenant:setup..."
    php artisan tenant:setup --force || true
    touch .setup-done-tenant
  else
    echo "  ✓ Tenant setup already done (use --force to redo)"
  fi

  # Passport keys
  if [ ! -f "storage/oauth-private.key" ]; then
    echo "  → Installing Passport keys..."
    php artisan passport:keys --force 2>/dev/null || true
  fi

  # Storage link
  php artisan storage:link 2>/dev/null || true

  echo ""
fi

# ─── 2. Frontend Build ───────────────────────────────────────────
if [ "$SKIP_FRONTEND" = false ]; then
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
  echo "  🎨 Frontend Build"
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

  NEED_BUILD=false

  if [ "$FORCE" = true ]; then
    NEED_BUILD=true
  elif [ ! -d "$LANDLORD_DIR/out" ] || [ ! -d "$TENANT_DIR/out" ]; then
    NEED_BUILD=true
  fi

  if [ "$SKIP_BUILD" = false ] && [ "$NEED_BUILD" = true ]; then
    # Install npm deps if needed
    for dir in "$LANDLORD_DIR" "$TENANT_DIR"; do
      if [ ! -d "$dir/node_modules" ]; then
        echo "  → Installing npm dependencies in $(basename $dir)..."
        cd "$dir"
        npm install
      fi
    done

    echo "  → Building landlord-frontend..."
    cd "$LANDLORD_DIR"
    npm run build

    echo "  → Building tenant-frontend..."
    cd "$TENANT_DIR"
    npm run build
  else
    echo "  ✓ Frontend builds already exist (use --force to rebuild)"
  fi

  # Symlinks for static assets
  echo "  → Setting up symlinks..."
  for target in landlord-assets tenant-assets; do
    [ -L "$PUBLIC_DIR/$target" ] && rm "$PUBLIC_DIR/$target"
    [ -d "$PUBLIC_DIR/$target" ] && rm -rf "$PUBLIC_DIR/$target"
  done

  ln -sfn "$LANDLORD_DIR/out" "$PUBLIC_DIR/landlord-assets"
  ln -sfn "$TENANT_DIR/out" "$PUBLIC_DIR/tenant-assets"
  echo "  ✓ public/landlord-assets → landlord-frontend/out"
  echo "  ✓ public/tenant-assets  → tenant-frontend/out"

  echo ""
fi

# ─── 3. Start Server ─────────────────────────────────────────────
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  🌐 Starting Server"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "  Landlord:  http://landlord.saas.test:8000"
echo "  Tenant #1: http://customer1.saas.test:8000"
echo "  Tenant #2: http://customer2.saas.test:8000"
echo "  API:       http://localhost:8000/api/..."
echo ""
echo "  Press Ctrl+C to stop"
echo ""

cd "$BACKEND_DIR"
php artisan serve --host=0.0.0.0
