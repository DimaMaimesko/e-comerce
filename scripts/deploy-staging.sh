#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/opt/e-commerce"
COMPOSE_FILE="docker-compose.staging.yaml"

if docker compose version >/dev/null 2>&1; then
  DOCKER_COMPOSE="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
  DOCKER_COMPOSE="docker-compose"
else
  echo "Docker Compose is not installed on the server."
  exit 1
fi

cd "$APP_DIR"

$DOCKER_COMPOSE -f "$COMPOSE_FILE" pull
$DOCKER_COMPOSE -f "$COMPOSE_FILE" up -d --remove-orphans

$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan migrate --force
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan config:clear
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan config:cache
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan route:cache
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan view:cache
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan event:cache

$DOCKER_COMPOSE -f "$COMPOSE_FILE" up -d --force-recreate queue scheduler

$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan storage:link || true
