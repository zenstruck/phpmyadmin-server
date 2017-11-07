<?php

use Symfony\Component\Process\Process;

require __DIR__.'/vendor/autoload.php';

$home = $_SERVER['HOME'];
$phpmyadminDir = $home.'/.phpmyadmin';

if (file_exists($phpmyadminDir)) {
    throw new \RuntimeException("phpMyAdmin already initiated in '$phpmyadminDir'.");
}

(new Process('composer create-project phpmyadmin/phpmyadmin .phpmyadmin', $home))->run();
