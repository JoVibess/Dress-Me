#!/bin/sh
set -e

cd /var/www

mkdir -p public/uploads/try-on/customer public/uploads/try-on/generated

chown -R www-data:www-data var public/assets public/uploads assets 2>/dev/null || true
find var -type d -exec chmod 775 {} + 2>/dev/null || true
find public/uploads -type d -exec chmod 775 {} + 2>/dev/null || true

su -s /bin/sh www-data -c "php bin/console cache:clear --env=prod --no-debug"

exec "$@"
