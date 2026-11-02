#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

: "${DEPLOY_PATH:?Set DEPLOY_PATH to the server configuration directory}"
: "${APP_IMAGE:?Set APP_IMAGE to the published PHP image}"
: "${WEB_IMAGE:?Set WEB_IMAGE to the published Nginx image}"
: "${RELEASE_ID:?Set a unique RELEASE_ID}"

if [[ "$DEPLOY_PATH" != /* || "$DEPLOY_PATH" == / || ! "$RELEASE_ID" =~ ^[a-zA-Z0-9._-]+$ ]]; then
    echo 'DEPLOY_PATH must be an absolute directory and RELEASE_ID a simple release identifier.' >&2
    exit 1
fi
for image in "$APP_IMAGE" "$WEB_IMAGE"; do
    if [[ ! "$image" =~ ^ghcr\.io/[a-z0-9/_.-]+@sha256:[a-f0-9]{64}$ ]]; then
        echo 'Deployments require immutable GHCR image digests.' >&2
        exit 1
    fi
done
for file in production.env app.env; do
    if [[ ! -s "$DEPLOY_PATH/$file" ]]; then
        echo "Configure $DEPLOY_PATH/$file before enabling deployments." >&2
        exit 1
    fi
done
if ! grep -Eq '^APP_KEY=.+$' "$DEPLOY_PATH/app.env"; then
    echo 'Configure the existing APP_KEY in app.env before deploying.' >&2
    exit 1
fi

mkdir -p "$DEPLOY_PATH/releases" "$DEPLOY_PATH/backups"
# Also prevent simultaneous manual deployments on this server.
exec 9>"$DEPLOY_PATH/deploy.lock"
flock -n 9 || { echo 'Another deployment is running.' >&2; exit 1; }

repository_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd)"
release_path="$DEPLOY_PATH/releases/$RELEASE_ID"
mkdir "$release_path"
cp "$repository_root/compose.production.yml" "$release_path/compose.yml"
printf 'APP_IMAGE=%s\nWEB_IMAGE=%s\n' "$APP_IMAGE" "$WEB_IMAGE" > "$release_path/images.env"

compose=(docker compose --project-name booking-production --project-directory "$DEPLOY_PATH"
    --env-file "$DEPLOY_PATH/production.env" --env-file "$release_path/images.env"
    --file "$release_path/compose.yml")

maintenance=false
trap 'echo "Deployment failed. Inspect Docker logs and $release_path. Maintenance enabled: $maintenance. No database rollback was attempted." >&2' ERR

"${compose[@]}" config --quiet
"${compose[@]}" pull
"${compose[@]}" up -d --wait --wait-timeout 360 db

if [[ -n "$("${compose[@]}" ps --status running -q app)" ]]; then
    "${compose[@]}" exec -T app php artisan down --retry=60
    maintenance=true
fi

# Stop background writes before taking a backup or running migrations.
"${compose[@]}" stop queue scheduler
# These variables are expanded inside the database container, not on the host.
# shellcheck disable=SC2016
table_count="$("${compose[@]}" exec -T db sh -c '
    export MYSQL_PWD="$MYSQL_ROOT_PASSWORD"
    mysql --user=root --batch --skip-column-names "$MYSQL_DATABASE" \
        --execute="SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()"
')"
if [[ "$table_count" != '0' ]]; then
    backup="$DEPLOY_PATH/backups/$RELEASE_ID.sql.gz"
    # shellcheck disable=SC2016
    "${compose[@]}" exec -T db sh -c '
        export MYSQL_PWD="$MYSQL_ROOT_PASSWORD"
        exec mysqldump --user=root --single-transaction --routines --events \
            --triggers --set-gtid-purged=OFF "$MYSQL_DATABASE"
    ' | gzip > "$backup.partial"
    mv -- "$backup.partial" "$backup"
fi

"${compose[@]}" run --rm --no-deps app php artisan migrate --force --no-interaction
# Initializes storage permissions before the unprivileged background services start.
"${compose[@]}" up -d --no-deps --force-recreate app
"${compose[@]}" exec -T app php artisan view:cache
"${compose[@]}" exec -T app php artisan up
maintenance=false
"${compose[@]}" up -d --no-deps --force-recreate queue scheduler webserver
"${compose[@]}" up -d --wait --wait-timeout 180

# Only advance the current-release pointer after the HTTP healthcheck passes.
printf '%s\n' "$RELEASE_ID" > "$DEPLOY_PATH/current-release.next"
mv -- "$DEPLOY_PATH/current-release.next" "$DEPLOY_PATH/current-release"
echo "Deployment $RELEASE_ID succeeded. Configuration: $release_path"
