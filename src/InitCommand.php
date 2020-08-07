<?php

namespace Zenstruck\PMA\Server\Command;

use Symfony\Component\Console\Input\InputInterface;
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $address = $io->ask('Address?', '127.0.0.1:8888');
        $mysqlHost = $io->ask('MySQL Host?', 'localhost');
        $mysqlPort = $io->ask('MySQL Port?', '3306');
        $mysqlUser = $io->ask('MySQL User?', 'root');
        $mysqlPass = $io->askHidden('MySQL Password?', function ($value) {
            return (string) $value;
        });

        (new Filesystem())->remove($this->getDocumentRoot());

        $io->comment('Downloading latest version of phpMyAdmin...');

        (new Process('composer create-project phpmyadmin/phpmyadmin .phpmyadmin', $this->getHomeDir()))
            ->setTimeout(null)
            ->run()
        ;

        $io->comment('Generating config.inc.php');
        $config = file_get_contents(__DIR__.'/../resources/config.inc.template');
        file_put_contents($this->getDocumentRoot().'/config.inc.php', strtr($config, [
            '%%mysql_host%%' => $mysqlHost,
            '%%mysql_port%%' => $mysqlPort,
            '%%mysql_user%%' => $mysqlUser,
            '%%mysql_password%%' => $mysqlPass,
        ]));

        $io->comment('Writing address file...');
        file_put_contents($this->getAddressFile(), $address);

        $io->success('Initialized phpMyAdmin, run "phpmyadmin" to start web server.');
    }
}
