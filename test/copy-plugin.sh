#!/bin/bash

# exit on error
set -e

cd "$(dirname "$0")"
cd ..

rm -rf test/site/wp-content/plugins/switch-to-classicpress/
mkdir -p test/site/wp-content/plugins/switch-to-classicpress/

cp -var \
    switch-to-classicpress.php \
    languages/ \
    lib/ \
    test/site/wp-content/plugins/switch-to-classicpress/
