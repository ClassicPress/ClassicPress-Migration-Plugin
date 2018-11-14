#!/bin/bash

# exit on error
set -e

cd "$(dirname "$0")"
cd ..

rm -rf test/site/wp-content/plugins/upgrade-to-classicpress/
mkdir -p test/site/wp-content/plugins/upgrade-to-classicpress/
cp -var upgrade-to-classicpress.php lib/ \
    test/site/wp-content/plugins/upgrade-to-classicpress/
