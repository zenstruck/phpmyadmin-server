<?php

namespace Zenstruck\PMA\Server\Command;

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

        if ($this->isRunning($io)) {
            $this->executeSymfonyCommand(['server:stop'], $io);
            $io->success('Stopped the phpMyAdmin web server.');

            return 0;
        }

        if (!$io->isQuiet()) {
            $io->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        }

        $this->executeSymfonyCommand(['server:start', '-d'], $io);

        return 0;
    }
}
