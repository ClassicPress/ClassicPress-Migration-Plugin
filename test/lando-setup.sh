#!/bin/bash

# exit on error
set -e
# show commands as they are executed
set -x

WP_MULTISITE="${WP_MULTISITE:-false}"
WP_VERSION="${WP_VERSION:-4.9.7}"
WP_LOCALE="${WP_LOCALE:-en_US}"
PHP_VERSION="${PHP_VERSION:-7.0}"

cd "$(dirname "$0")"
cd ..

# Hack - awaiting https://github.com/lando/lando/pull/750
perl -pi -we "s/^  php: .*/  php: '$PHP_VERSION'/" .lando.yml

lando start -v
lando wp --version || lando bash --verbose test/install-wp-cli.sh

rm -rf test/site/[a-z.]*

lando wp core download \
    --path=test/site/ \
    --version=$WP_VERSION \
    --locale=$WP_LOCALE

lando wp config create \
    --path=test/site/ \
    --dbname=wordpress \
    --dbuser=wordpress \
    --dbpass=wordpress \
    --dbhost=database

lando wp db reset --path=test/site/ --yes

config_set() {
    lando wp config set \
        --path=test/site/ \
        --type=constant \
        "$@"
}

config_set --raw WP_AUTO_UPDATE_CORE false
config_set --raw WP_DEBUG true

if [ "$WP_MULTISITE" = true ]; then
    wp_url=$(lando info --format=json | php test/get-wp-url.php 'http://one.lndo.site')

    lando wp core multisite-install \
        --path=test/site/ \
        --subdomains \
        --url="$wp_url" \
        --title="My Test Site One" \
        --admin_user="admin" \
        --admin_password="admin" \
        --admin_email="admin@example.com" \
        --skip-email

    # https://wordpress.stackexchange.com/a/299114/64369
    config_set ADMIN_COOKIE_PATH /
    config_set COOKIE_DOMAIN ''
    config_set COOKIEPATH ''
    config_set SITECOOKIEPATH ''

    site_two_id=$(
        lando wp site create \
            --path=test/site/ \
            --slug=two \
            --title="My Test Site Two" \
            --porcelain \
        | tr -cd '[0-9]'
    )
    # The new site URL is wrong: 'http://two.one.lndo.site8000/'
    site_two_url=${wp_url/one/two}
    site_two_domain=${site_two_url#http://}
    site_two_domain=${site_two_domain%/}
    lando wp db query --path=test/site/ "
        update wp_blogs
        set domain = '$site_two_domain'
        where blog_id = '$site_two_id'
    "
    lando wp db query --path=test/site/ "
        update wp_${site_two_id}_options
        set option_value = '$site_two_url'
        where option_name in ('siteurl', 'home')
    "

    cp -va test/htaccess-multisite.conf test/site/.htaccess

else # single site install
    wp_url=$(lando info --format=json | php test/get-wp-url.php 'http://test.lndo.site')

    lando wp core install \
        --path=test/site/ \
        --url="$wp_url" \
        --title="My Test Site" \
        --admin_user="admin" \
        --admin_password="admin" \
        --admin_email="admin@example.com" \
        --skip-email

fi

echo "Testing site URL: $wp_url"

./test/copy-plugin.sh

if [ "$WP_MULTISITE" = true ]; then
    lando wp plugin activate \
        --path=test/site/ \
        --network \
        switch-to-classicpress
else
    lando wp plugin activate \
        --path=test/site/ \
        switch-to-classicpress
fi

echo
echo "Test site is ACTIVE: $wp_url"
echo "username: admin"
echo "password: admin"
