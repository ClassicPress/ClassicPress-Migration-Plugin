#!/usr/bin/env bash

cd "$(dirname "$0")"
cd ..

rm -rf upgrade-to-classicpress.zip upgrade-to-classicpress/
mkdir upgrade-to-classicpress/

cp -var \
	LICENSE \
	assets/ \
	lib/ \
	readme.txt \
	upgrade-to-classicpress.php \
	upgrade-to-classicpress/

zip -r upgrade-to-classicpress.zip upgrade-to-classicpress/

rm -rf upgrade-to-classicpress/
