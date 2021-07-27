<?php

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
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Updates phpmyadmin.phar to the latest version')
            ->addOption('rollback', 'r', InputOption::VALUE_NONE, 'Rollback to a previous version')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $updater = new Updater(null, false, Updater::STRATEGY_GITHUB);
        $updater->setBackupPath(\sys_get_temp_dir().'/phpmyadmin.phar.bak');

        if ($input->getOption('rollback')) {
            if ($updater->rollback()) {
                $io->success('Successfully rolled back.');

                return 0;
            }

            throw new \RuntimeException('Could not rollback.');
        }

        $updater->getStrategy()->setPackageName('zenstruck/phpmyadmin-server');
        $updater->getStrategy()->setPharName('phpmyadmin.phar');
        $updater->getStrategy()->setCurrentLocalVersion($current = $this->getApplication()->getVersion());

        if (!$updater->update()) {
            $io->success(\sprintf('You are already using the latest available phpmyadmin-server (%s).', $current));

            return 0;
        }

        $io->success(\sprintf('Updated phpmyadmin-server to %s.', $updater->getNewVersion()));

        return 0;
    }
}
