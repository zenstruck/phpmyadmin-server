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
    protected function configure(): void
    {
        $this
            ->setName('run')
            ->setDescription('Starts/Stops the phpMyAdmin web server')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!\file_exists($address = $this->addressFile()) || !\file_exists($router = $this->routerFile())) {
            throw new \RuntimeException('phpMyAdmin not initialized. Run "phpmyadmin init".');
        }

        $server = new WebServer();

        if ($server->isRunning($this->pidFile())) {
            $server->stop($this->pidFile());
            $io->success('Stopped the phpMyAdmin web server.');

            return 0;
        }

        $config = new WebServerConfig(
            $this->documentRoot(),
            'dev',
            \file_get_contents($address),
            $router
        );

        if (WebServer::STARTED === $server->start($config, $this->pidFile())) {
            $io->success(\sprintf('phpMyAdmin web server listening on http://%s', $config->getAddress()));
        }

        return 0;
    }

    protected function pidFile(): string
    {
        return $this->documentRoot().'/.pid';
    }
}
