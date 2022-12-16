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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class BaseCommand extends Command
{
    final protected function isRunning(OutputInterface $output): bool
    {
        $process = $this->executeSymfonyCommand(['server:status'], $output);

        return !\str_contains($process->getOutput(), 'Not Running');
    }

    /**
     * @param string[] $parameters
     */
    final protected function executeProcess(array $parameters, string $workingDir, OutputInterface $output): Process
    {
        $process = (new Process($parameters, $workingDir))
            ->setTimeout(null)
        ;

        $process->run(function($type, $buffer) use ($output) {
            if ($output->isVerbose()) {
                $output->writeln($buffer);
            }
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process;
    }

    /**
     * @param string[] $parameters
     */
    final protected function executeSymfonyCommand(array $parameters, OutputInterface $output): Process
    {
        if (!\is_dir($this->documentRoot())) {
            throw new \RuntimeException('phpMyAdmin not initialized. Run "phpmyadmin init".');
        }

        if (!$symfony = (new ExecutableFinder())->find('symfony')) {
            throw new \RuntimeException('Symfony CLI is required: https://symfony.com/download#step-1-install-symfony-cli');
        }

        return $this->executeProcess(\array_merge([$symfony], $parameters), $this->documentRoot(), $output);
    }

    final protected function folderName(): string
    {
        return 'phpmyadmin-server';
    }

    final protected function organizationDir(): string
    {
        return "{$_SERVER['HOME']}/.config/zenstruck";
    }

    final protected function documentRoot(): string
    {
        return "{$this->organizationDir()}/{$this->folderName()}";
    }
}
