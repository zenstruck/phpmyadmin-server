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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class InitCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setName('init')
            ->setDescription('Initialize phpMyAdmin')
            ->addArgument('version', InputArgument::OPTIONAL, 'phpMyAdmin version (blank for latest)')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'MySQL Host', 'localhost')
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'MySQL Port', '3306')
            ->addOption('user', null, InputOption::VALUE_REQUIRED, 'MySQL User', 'root')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'MySQL Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $version = $input->getArgument('version');
        $mysqlHost = $input->getOption('host');
        $mysqlPort = $input->getOption('port');
        $mysqlUser = $input->getOption('user');
        $mysqlPass = $input->getOption('password');
        $fs = new Filesystem();

        $fs->remove($this->documentRoot());

        $package = 'phpmyadmin/phpmyadmin';

        if ($version) {
            $package .= ':'.$version;
        }

        $io->comment(\sprintf('Downloading phpMyAdmin <info>%s</info>...', $version ?: 'latest'));

        if (!$fs->exists($orgDir = $this->organizationDir())) {
            $fs->mkdir($orgDir);
        }

        if (!$composer = (new ExecutableFinder())->find('composer')) {
            throw new \RuntimeException('Composer is required: https://symfony.com/download#step-1-install-symfony-cli');
        }

        $this->executeProcess([$composer, 'create-project', $package, $this->folderName()], $orgDir, $io);

        $io->comment('Generating config.inc.php');
        $config = \file_get_contents(__DIR__.'/../resources/config.inc.template');
        \file_put_contents($this->documentRoot().'/config.inc.php', \strtr($config, [ // @phpstan-ignore-line
            '%%mysql_host%%' => $mysqlHost,
            '%%mysql_port%%' => $mysqlPort,
            '%%mysql_user%%' => $mysqlUser,
            '%%mysql_password%%' => $mysqlPass,
        ]));

        $io->comment('Attaching Proxy Domain...');
        $this->executeSymfonyCommand(['proxy:domain:attach', 'phpmyadmin'], $io);

        $io->success('Initialized phpMyAdmin, run "phpmyadmin" to start web server.');

        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $definition = $this->getDefinition();

        $input->setArgument('version', $io->ask($definition->getArgument('version')->getDescription()));

        foreach (['host', 'port', 'user'] as $option) {
            $input->setOption($option, $io->ask(
                $definition->getOption($option)->getDescription(),
                $definition->getOption($option)->getDefault() // @phpstan-ignore-line
            ));
        }

        $input->setOption('password', $io->askHidden($definition->getOption('password')->getDescription()));
    }
}
