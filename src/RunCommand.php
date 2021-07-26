<?php

namespace Zenstruck\PMA\Server\Command;

use Symfony\Bundle\WebServerBundle\WebServer;
use Symfony\Bundle\WebServerBundle\WebServerConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RunCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Starts/Stops the phpMyAdmin web server')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (!\file_exists($addressFile = $this->getAddressFile())) {
            throw new \RuntimeException('phpMyAdmin not initialized. Run "phpmyadmin init".');
        }

        $server = new WebServer();

        if ($server->isRunning($this->getPidFile())) {
            $server->stop($this->getPidFile());
            $io->success('Stopped the phpMyAdmin web server.');

            return 0;
        }

        $config = new WebServerConfig(
            $this->getDocumentRoot(),
            'dev',
            \file_get_contents($addressFile)
        );

        if (WebServer::STARTED === $server->start($config, $this->getPidFile())) {
            $io->success(\sprintf('phpMyAdmin web server listening on http://%s', $config->getAddress()));
        }

        return 0;
    }

    protected function getPidFile()
    {
        return $this->getDocumentRoot().'/.pid';
    }
}
