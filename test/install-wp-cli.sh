#!/bin/bash

# This is a workaround for the following issue:
# https://github.com/lando/lando/issues/1197#issuecomment-429733044

mkdir -p /var/www/.composer/vendor/bin
wget https://github.com/wp-cli/wp-cli/releases/download/v2.0.1/wp-cli-2.0.1.phar -O /var/www/.composer/vendor/bin/wp
chmod +x /var/www/.composer/vendor/bin/wp
wp --version
