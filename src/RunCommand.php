<?php

namespace Zenstruck\PMA\Server\Command;

use Symfony\Bundle\WebServerBundle\WebServer;
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
        $config = $this->webserverConfig();
        $server = new WebServer();

        if ($server->isRunning($this->pidFile())) {
            $server->stop($this->pidFile());
            $io->success('Stopped the phpMyAdmin web server.');

            return 0;
        }

        if (WebServer::STARTED === $server->start($config, $this->pidFile())) {
            $io->success(\sprintf('phpMyAdmin web server listening on http://%s', $config->getAddress()));
        }

        return 0;
    }
}
