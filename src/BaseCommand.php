<?php

namespace Zenstruck\PMA\Server\Command;

use Symfony\Bundle\WebServerBundle\WebServerConfig;
use Symfony\Component\Console\Command\Command;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class BaseCommand extends Command
{
    protected function webserverConfig(): WebServerConfig
    {
        if (!\file_exists($address = $this->addressFile()) || !\file_exists($router = $this->routerFile())) {
            throw new \RuntimeException('phpMyAdmin not initialized. Run "phpmyadmin init".');
        }

        return new WebServerConfig($this->documentRoot(), 'dev', \file_get_contents($address), $router);
    }

    protected function folderName(): string
    {
        return 'phpmyadmin-server';
    }

    protected function organizationDir(): string
    {
        return "{$_SERVER['HOME']}/.config/zenstruck";
    }

    protected function documentRoot(): string
    {
        return "{$this->organizationDir()}/{$this->folderName()}";
    }

    protected function addressFile(): string
    {
        return "{$this->documentRoot()}/.address";
    }

    protected function routerFile(): string
    {
        return "{$this->documentRoot()}/.router.php";
    }

    protected function pidFile(): string
    {
        return $this->documentRoot().'/.pid';
    }
}
