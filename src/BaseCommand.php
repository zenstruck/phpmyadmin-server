<?php

namespace Zenstruck\PMA\Server\Command;

use Symfony\Component\Console\Command\Command;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class BaseCommand extends Command
{
    protected function getFolderName()
    {
        return 'phpmyadmin-server';
    }

    protected function getOrganizationDir()
    {
        return "{$_SERVER['HOME']}/.config/zenstruck";
    }

    protected function getDocumentRoot()
    {
        return "{$this->getOrganizationDir()}/{$this->getFolderName()}";
    }

    protected function getAddressFile()
    {
        return "{$this->getDocumentRoot()}/.address";
    }

    protected function getRouterFile()
    {
        return "{$this->getDocumentRoot()}/.router.php";
    }
}
