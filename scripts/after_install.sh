
#!/bin/bash

# exit when any command fails
set -e

echo Generate Laravel APP Key
php /var/www/artisan key:generate -q

echo Clear cached configs
php /var/www/artisan config:clear -q

echo Optimize Laravel
php /var/www/artisan optimize -q
php /var/www/artisan view:cache -q

echo Perform Laravel Migration
php /var/www/artisan migrate --force

echo Restart Laravel Queue to pick up on changes
php /var/www/artisan queue:restart -q

echo Update File Permissions
chmod -R -f 777 /var/www/storage /var/www/bootstrap/cache
chmod -R -f 755 /var/www/nginx /var/www/scripts
chmod -R -f 755 /var/www/.env
