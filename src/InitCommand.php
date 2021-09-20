<?php

namespace Zenstruck\PMA\Server\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class InitCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initialize phpMyAdmin')
            ->addArgument('version', InputArgument::OPTIONAL, 'phpMyAdmin version (blank for latest)')
            ->addOption('address', null, InputOption::VALUE_REQUIRED, 'URL Address', '127.0.0.1:8888')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'MySQL Host', 'localhost')
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'MySQL Port', '3306')
            ->addOption('user', null, InputOption::VALUE_REQUIRED, 'MySQL User', 'root')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'MySQL Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $version = $input->getArgument('version');
        $address = $input->getOption('address');
        $mysqlHost = $input->getOption('host');
        $mysqlPort = $input->getOption('port');
        $mysqlUser = $input->getOption('user');
        $mysqlPass = $input->getOption('password');
        $fs = new Filesystem();

        $fs->remove($this->getDocumentRoot());

        $package = 'phpmyadmin/phpmyadmin';

        if ($version) {
            $package .= ':'.$version;
        }

        $io->comment(\sprintf('Downloading phpMyAdmin <info>%s</info>...', $version ?: 'latest'));

        if (!$fs->exists($orgDir = $this->getOrganizationDir())) {
            $fs->mkdir($orgDir);
        }

        $process = (new Process(['composer', 'create-project', $package, $this->getFolderName()], $orgDir))
            ->setTimeout(null)
        ;

        $process->run(function($type, $buffer) use ($io) {
            if ($io->isVerbose()) {
                $io->writeln($buffer);
            }
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $io->comment('Generating config.inc.php');
        $config = \file_get_contents(__DIR__.'/../resources/config.inc.template');
        \file_put_contents($this->getDocumentRoot().'/config.inc.php', \strtr($config, [
            '%%mysql_host%%' => $mysqlHost,
            '%%mysql_port%%' => $mysqlPort,
            '%%mysql_user%%' => $mysqlUser,
            '%%mysql_password%%' => $mysqlPass,
        ]));

        $io->comment('Writing address file...');
        \file_put_contents($this->getAddressFile(), $address);

        $io->comment('Writing router file...');
        \file_put_contents(
            $this->getRouterFile(),
            \file_get_contents(__DIR__.'/../vendor/symfony/web-server-bundle/Resources/router.php')
        );

        $io->success('Initialized phpMyAdmin, run "phpmyadmin" to start web server.');

        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $definition = $this->getDefinition();

        $input->setArgument('version', $io->ask($definition->getArgument('version')->getDescription()));

        foreach (['address', 'host', 'port', 'user'] as $option) {
            $input->setOption($option, $io->ask(
                $definition->getOption($option)->getDescription(),
                $definition->getOption($option)->getDefault()
            ));
        }

        $input->setOption('password', $io->askHidden($definition->getOption('password')->getDescription()));
    }
}
