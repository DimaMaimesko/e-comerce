#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/opt/e-commerce"
COMPOSE_FILE="docker-compose.staging.yml"

cd "$APP_DIR"

docker compose -f "$COMPOSE_FILE" pull
docker compose -f "$COMPOSE_FILE" up -d --remove-orphans

docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force
docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:clear
docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan view:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan event:cache

docker compose -f "$COMPOSE_FILE" up -d --force-recreate queue scheduler

docker compose -f "$COMPOSE_FILE" exec -T app php artisan storage:link || true
