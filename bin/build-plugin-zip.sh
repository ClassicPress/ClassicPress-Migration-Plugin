#!/usr/bin/env bash

cd "$(dirname "$0")"
cd ..

rm -rf switch-to-classicpress.zip switch-to-classicpress/
mkdir switch-to-classicpress/

cp -var \
	LICENSE \
	lib/ \
	README.md \
	switch-to-classicpress.php \
	switch-to-classicpress/

zip -r switch-to-classicpress.zip switch-to-classicpress/

rm -rf switch-to-classicpress/
