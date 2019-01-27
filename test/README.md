This directory contains tools and scripts to automatically create WordPress sites for multiple PHP and WP versions, using [lando](https://github.com/lando/lando), a tool that wraps `docker` and `docker-compose`.

This is currently the best way to test the migration plugin.  Future versions may include automated browser tests of the full site using a tool like [puppeteer](https://github.com/GoogleChrome/puppeteer).

## Instructions

- [Install `docker-ce`](https://docs.docker.com/install/linux/docker-ce/debian/#set-up-the-repository)
- [Install `lando`](https://docs.devwithlando.io/installation/installing.html)
- Run `test/lando-setup.sh`.  Other possible arguments (passed as environment variables):

```
PHP_VERSION=7.3 test/lando-setup.sh
WP_VERSION=4.9.7 test/lando-setup.sh
WP_MULTISITE=true ./test/lando-setup.sh
```

- Access the site at `http://test.lndo.site:8000/` (or `http://one.lndo.site:8000/` and `http://two.lndo.site:8000/` for `WP_MULTISITE=true`).  You can log in with username `admin` and password `admin`.
