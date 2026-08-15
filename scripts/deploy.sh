#!/usr/bin/env bash
set -euo pipefail

REPO=/var/www/Drab-ALibda-SMS
APP="$REPO/darb-alibda-sms"
PHP=/usr/bin/php8.4
COMPOSER=/usr/local/bin/composer

log(){ echo "[$(date '+%F %T')] $*"; }

cd "$APP"

log "composer install (prod)…"
$PHP "$COMPOSER" install --no-interaction --prefer-dist --no-dev --optimize-autoloader

log "migrate + seed…"; $PHP artisan migrate --force --seed
log "assets…";     $PHP artisan storage:link || true; $PHP artisan filament:assets
log "caches…";     $PHP artisan config:cache; $PHP artisan route:cache; $PHP artisan view:cache
log "perms…";      chown -R www-data:www-data "$REPO"; chmod -R ug+rwX "$APP/storage" "$APP/bootstrap/cache"
log "reload fpm…"; systemctl reload php8.4-fpm

log "== Deploy OK =="
