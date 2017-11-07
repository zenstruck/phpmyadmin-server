<?php

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

require __DIR__.'/vendor/autoload.php';

$phpmyadminDir = $_SERVER['HOME'].'/.phpmyadmin';
$pidFile = __DIR__.'/pid';

if (!file_exists($phpmyadminDir)) {
    throw new \RuntimeException("phpMyAdmin it not yet initiated in '$phpmyadminDir'.");
}

if (file_exists($pidFile)) {
    echo "Stopping server...\n";
    unlink($pidFile);

    exit;
}

copy(__DIR__.'/config.inc.php', $phpmyadminDir.'/config.inc.php');

$pid = pcntl_fork();

if ($pid < 0) {
    throw new \RuntimeException('Unable to start the server process.');
}

if ($pid > 0) {
    echo "Starting server...\n";
    exit;
}

if (posix_setsid() < 0) {
    throw new \RuntimeException('Unable to set the child process as session leader.');
}

$process = new Process([
    (new PhpExecutableFinder())->find(),
    '-S',
    'localhost:8888',
]);
$process->setWorkingDirectory($phpmyadminDir);
$process->disableOutput();
$process->start();

if (!$process->isRunning()) {
    throw new \RuntimeException('Unable to start the server process.');
}

file_put_contents($pidFile, $process->getPid());

// stop the web server when the lock file is removed
while ($process->isRunning()) {
    if (!file_exists($pidFile)) {
        $process->stop();
    }

    sleep(1);
}
