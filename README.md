# phpMyAdmin Web Server

[![Latest Stable Version](http://img.shields.io/packagist/v/zenstruck/phpmyadmin-server.svg?style=flat-square)](https://packagist.org/packages/zenstruck/phpmyadmin-server)
[![License](http://img.shields.io/packagist/l/zenstruck/phpmyadmin-server.svg?style=flat-square)](https://packagist.org/packages/zenstruck/phpmyadmin-server)

This library gives you a `phpmyadmin` command to start/stop a php webserver running phpMyAdmin
in the background.

## Installation

It is best to install globally with composer:

```
composer global request zenstruck/phpmyadmin-server
```

**Note**: Ensure you have `~/.composer/vendor/bin` in your `PATH` to give access to the `phpmyadmin`
command.

## Initialization

```
phpmyadmin init
```

This command will ask you questions about your setup and download the latest version of phpMyAdmin
to `~/.phpmyadmin`.

## Start/Stop Server

```
phpmyadmin
```

If you ever need to change your configuration, run `phpmyadmin init` again.
