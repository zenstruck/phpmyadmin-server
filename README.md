# phpMyAdmin Web Server

[![CI](https://github.com/zenstruck/phpmyadmin-server/actions/workflows/ci.yml/badge.svg)](https://github.com/zenstruck/phpmyadmin-server/actions/workflows/ci.yml)
[![Latest Stable Version](http://img.shields.io/packagist/v/zenstruck/phpmyadmin-server.svg)](https://packagist.org/packages/zenstruck/phpmyadmin-server)
[![License](http://img.shields.io/packagist/l/zenstruck/phpmyadmin-server.svg)](https://packagist.org/packages/zenstruck/phpmyadmin-server)

This library gives you a `phpmyadmin` command to start/stop a php webserver running phpMyAdmin
in the background.

## Installation

### PHAR

The preferred method of installation is to use the PHAR which can be downloaded from the most
recent [GitHub Release](https://github.com/zenstruck/phpmyadmin-server/releases). This method
ensures you will not have any dependency conflict issue.

```bash
wget https://github.com/zenstruck/phpmyadmin-server/releases/latest/download/phpmyadmin.phar -O phpmyadmin && chmod +x phpmyadmin
mv phpmyadmin ~/bin # assumes ~/bin is in your PATH
```

### Composer

```bash
composer global require zenstruck/phpmyadmin-server
```

**Note**: Ensure you have `~/.config/composer/vendor/bin` in your `PATH` to give access to the `phpmyadmin`
command.

## Initialization

```bash
phpmyadmin init
```

This command will ask you questions about your setup and download the latest version of phpMyAdmin
to `~/.phpmyadmin`.

> *Note*: If you ever need to change your configuration, run `phpmyadmin init` again.

## Start/Stop Server

```bash
phpmyadmin
```

## Check Status

```bash
phpmyadmin status
```

This command exits with `0` if running and `1` if not. You can add the following in your `.bash_profile`
to ensure it's always running:

```bash
phpmyadmin status || phpmyadmin
```

## Self-Update

If installed via [PHAR](#phar), use the `self-update` command:

```bash
phpmyadmin self-update
```
