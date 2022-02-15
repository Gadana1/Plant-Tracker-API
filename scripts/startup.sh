#!/bin/bash

# exit when any command fails
set -e

# Install dependencies
echo 'Installing dependencies'
composer install

# Perform after install function
echo 'Running after install script'
/var/www/scripts/after_install.sh

# Start Supervisor
echo 'Start Supervisor...'
/usr/bin/supervisord -c /etc/supervisord.conf
