#!/bin/sh
# Kiota 1.35 PHP writes @QueryParameter("snake_name") annotations without importing the class,
# so doctrine/annotations throws on the first request that sets such a parameter.
set -eu
grep -rl '@QueryParameter' "$1" | while read -r file; do
  grep -q '^use Microsoft\\Kiota\\Abstractions\\QueryParameter;' "$file" ||
    sed -i '0,/^namespace .*;$/s//&\n\nuse Microsoft\\Kiota\\Abstractions\\QueryParameter;/' "$file"
done
