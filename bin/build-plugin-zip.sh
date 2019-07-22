#!/usr/bin/env bash

set -e # exit on error

cd "$(dirname "$0")"
cd ..

rm -rf switch-to-classicpress.zip switch-to-classicpress/
mkdir switch-to-classicpress/

cp -var \
	LICENSE \
	languages/ \
	lib/ \
	README.md \
	switch-to-classicpress.php \
	switch-to-classicpress/

zip -r switch-to-classicpress.zip switch-to-classicpress/

rm -rf switch-to-classicpress/

echo "Plugin built successfully: switch-to-classicpress.zip"
