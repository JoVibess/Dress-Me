#!/bin/sh
set -e

cd /var/www

chown -R www-data:www-data var public/assets assets 2>/dev/null || true
find var -type d -exec chmod 775 {} + 2>/dev/null || true

su -s /bin/sh www-data -c "php bin/console cache:clear --env=prod --no-debug"

exec "$@"
