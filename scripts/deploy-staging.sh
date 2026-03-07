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

if [ ! -f "$COMPOSE_FILE" ]; then
  echo "Compose file not found: $APP_DIR/$COMPOSE_FILE"
  exit 1
fi

if [ ! -f .env ]; then
  echo "Environment file not found: $APP_DIR/.env"
  exit 1
fi

$DOCKER_COMPOSE -f "$COMPOSE_FILE" pull
$DOCKER_COMPOSE -f "$COMPOSE_FILE" up -d --remove-orphans

$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app sh -c "
  mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache &&
  chown -R www-data:www-data storage bootstrap/cache &&
  chmod -R 775 storage bootstrap/cache
"

$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan migrate --force
#$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan db:seed
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan optimize:clear
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan config:cache
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan route:cache
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan view:cache
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan event:cache
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan queue:restart || true
$DOCKER_COMPOSE -f "$COMPOSE_FILE" exec -T app php artisan storage:link || true

$DOCKER_COMPOSE -f "$COMPOSE_FILE" up -d --force-recreate queue scheduler
