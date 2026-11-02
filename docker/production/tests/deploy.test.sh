#!/usr/bin/env bash
set -Eeuo pipefail

repository_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../../.." && pwd)"
test_root="$(mktemp -d "${TMPDIR:-/tmp}/booking-deploy-test.XXXXXXXX")"
trap 'rm -rf -- "$test_root"' EXIT
mkdir "$test_root/bin"

# No Docker socket, server, registry, database or real credentials are used.
cat > "$test_root/bin/docker" <<'MOCK'
#!/usr/bin/env bash
set -eu
printf '%s\n' "$*" >> "$MOCK_LOG"
case "$*" in
    *'ps --status running -q app'*)
        [[ "$SCENARIO" == fresh ]] || echo existing-app
        ;;
    *'SELECT COUNT(*)'*)
        if [[ "$SCENARIO" == fresh ]]; then echo 0; else echo 3; fi
        ;;
    *mysqldump*)
        echo 'mock SQL backup'
        [[ "$SCENARIO" != backup-failure ]]
        ;;
    *'php artisan migrate --force'*)
        [[ "$SCENARIO" != migration-failure ]]
        ;;
    *'up -d --wait --wait-timeout 180'*)
        [[ "$SCENARIO" != health-failure ]]
        ;;
esac
MOCK
chmod +x "$test_root/bin/docker"
export PATH="$test_root/bin:$PATH"
APP_IMAGE="ghcr.io/example/booking-app@sha256:$(printf '%064d' 1)"
WEB_IMAGE="ghcr.io/example/booking-web@sha256:$(printf '%064d' 2)"
export APP_IMAGE WEB_IMAGE

assert_not_called() {
    if grep -q "$1" "$MOCK_LOG"; then
        echo "Unexpected Docker command: $1" >&2
        exit 1
    fi
}

for SCENARIO in success fresh backup-failure migration-failure health-failure; do
    export SCENARIO
    export DEPLOY_PATH="$test_root/$SCENARIO"
    export RELEASE_ID="test-$SCENARIO"
    export MOCK_LOG="$DEPLOY_PATH/commands.log"
    mkdir "$DEPLOY_PATH"
    printf 'APP_KEY=fixture-only\n' > "$DEPLOY_PATH/app.env"
    printf 'DOCKER_DB_PASSWORD=fixture-only\n' > "$DEPLOY_PATH/production.env"
    printf 'previous-release\n' > "$DEPLOY_PATH/current-release"

    result=0
    bash "$repository_root/docker/production/deploy.sh" > "$DEPLOY_PATH/output.log" 2>&1 || result=$?
    case "$SCENARIO" in
        success|fresh)
            [[ "$result" == 0 ]]
            [[ "$(cat "$DEPLOY_PATH/current-release")" == "$RELEASE_ID" ]]
            grep -q 'up -d --wait --wait-timeout 180' "$MOCK_LOG"
            if [[ "$SCENARIO" == success ]]; then
                gzip -t "$DEPLOY_PATH/backups/$RELEASE_ID.sql.gz"
            else
                assert_not_called mysqldump
            fi
            ;;
        *)
            [[ "$result" != 0 ]]
            [[ "$(cat "$DEPLOY_PATH/current-release")" == previous-release ]]
            if [[ "$SCENARIO" == backup-failure ]]; then
                assert_not_called 'php artisan migrate'
                [[ ! -e "$DEPLOY_PATH/backups/$RELEASE_ID.sql.gz" ]]
            elif [[ "$SCENARIO" == migration-failure ]]; then
                assert_not_called 'up -d --no-deps --force-recreate app'
                assert_not_called 'php artisan up'
            fi
            ;;
    esac
    echo "PASS: $SCENARIO"
done

# A mutable tag must be rejected before any Docker command is called.
export RELEASE_ID=test-mutable
export APP_IMAGE=ghcr.io/example/booking-app:latest
previous_commands="$(wc -l < "$MOCK_LOG")"
if bash "$repository_root/docker/production/deploy.sh" > "$DEPLOY_PATH/rejected.log" 2>&1; then
    echo 'Mutable image reference was unexpectedly accepted.' >&2
    exit 1
fi
[[ "$(wc -l < "$MOCK_LOG")" == "$previous_commands" ]]
echo 'PASS: mutable image rejected'
