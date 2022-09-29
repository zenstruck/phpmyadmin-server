<?php

namespace Zenstruck\PMA\Server\Command;

use Symfony\Bundle\WebServerBundle\WebServer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class StatusCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setName('status')
            ->setDescription('Check the status of the phpMyAdmin server')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $server = new WebServer();
        $config = $this->webserverConfig();

        if ($server->isRunning($this->pidFile())) {
            $io->success(\sprintf('phpMyAdmin web server is running (http://%s).', $config->getAddress()));

            return 0;
        }

        $io->warning('phpMyAdmin web server is NOT running.');

        return 1;
    }
}
