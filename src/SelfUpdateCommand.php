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

use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SelfUpdateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Updates phpmyadmin.phar to the latest version')
            ->addOption('rollback', 'r', InputOption::VALUE_NONE, 'Rollback to a previous version')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $updater = new Updater(null, false, Updater::STRATEGY_GITHUB);
        $updater->setBackupPath($backup = \sys_get_temp_dir().'/phpmyadmin.phar.bak');
        $updater->setRestorePath($backup);

        if ($input->getOption('rollback')) {
            if ($updater->rollback()) {
                $io->success('Successfully rolled back.');

                return 0;
            }

            throw new \RuntimeException('Could not rollback.');
        }

        $updater->getStrategy()->setPackageName('zenstruck/phpmyadmin-server');
        $updater->getStrategy()->setPharName('phpmyadmin.phar');
        $updater->getStrategy()->setCurrentLocalVersion($current = $this->getApplication()->getVersion()); // @phpstan-ignore-line

        if (!$updater->update()) {
            $io->success(\sprintf('You are already using the latest available phpmyadmin-server (%s).', $current));

            return 0;
        }

        $io->success(\sprintf('Updated phpmyadmin-server to %s.', $updater->getNewVersion()));

        return 0;
    }
}
