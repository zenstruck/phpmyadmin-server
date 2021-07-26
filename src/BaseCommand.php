<?php

namespace Zenstruck\PMA\Server\Command;

use Symfony\Component\Console\Command\Command;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class BaseCommand extends Command
{
    protected function getHomeDir()
    {
        return $_SERVER['HOME'];
    }

    protected function getDocumentRoot()
    {
        return $this->getHomeDir().'/.phpmyadmin';
    }

    protected function getAddressFile()
    {
        return $this->getDocumentRoot().'/.address';
    }

    protected function getRouterFile()
    {
        return $this->getDocumentRoot().'/.router.php';
    }
}
