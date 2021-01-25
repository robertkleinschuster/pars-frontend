#!/bin/sh
export PATH=/opt/plesk/php/7.4/bin:$PATH:$HOME/bin
php /usr/lib64/plesk-9.0/composer.phar update --no-dev --no-interaction &> deploy.log
php /usr/lib64/plesk-9.0/composer.phar install --no-dev --no-interaction &> deploy.log
