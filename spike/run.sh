#!/bin/sh
# Usage: spike/run.sh <oag-psr18|kiota> <Test class>. Needs spike.env with PUNTJES_CLIENT_ID and PUNTJES_CLIENT_SECRET.
set -eu
client=$1
test=$2
spike=$(cd "$(dirname "$0")" && pwd)
app=${PUNTJES_APP:-$HOME/Repositories/Puntjes}
voucher=$(cd "$app" && ./vendor/bin/sail artisan tinker --execute "$(cat "$spike/fixtures/voucher.php")" | grep '^SPIKE_VOUCHER=' | cut -d= -f2-)
docker run --rm -u "$(id -u):$(id -g)" --network puntjes_sail \
  --env-file "${SPIKE_ENV:?set SPIKE_ENV to the file holding the client id and secret}" \
  -e PUNTJES_BASE_URL=http://laravel.test/api/v1 -e SPIKE_VOUCHER="$voucher" \
  -v "$spike:/spike" -w "/spike/$client" php:8.4-cli \
  vendor/bin/phpunit --bootstrap vendor/autoload.php --cache-directory /tmp --display-warnings "../tests/$test.php"
