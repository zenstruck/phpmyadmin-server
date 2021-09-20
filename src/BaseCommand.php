<?php

namespace Zenstruck\PMA\Server\Command;

use Symfony\Component\Console\Command\Command;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class BaseCommand extends Command
{
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
}
