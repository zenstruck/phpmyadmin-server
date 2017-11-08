# phpMyAdmin Web Server

Run phpMyAdmin in the background with a PHP webserver. This library gives you a `phpmyadmin` command
to start/stop a php webserver running phpMyAdmin.

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
