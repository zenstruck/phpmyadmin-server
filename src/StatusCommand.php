<?php

/*
 * This file is part of the zenstruck/phpmyadmin-server package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\PMA\Server\Command;

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

        if (!$io->isQuiet()) {
            $io->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        }

        if ($this->isRunning($io)) {
            return 0;
        }

        return 1;
    }
}
