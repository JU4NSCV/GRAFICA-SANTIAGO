<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$app = new PHPUnit\TextUI\Application();
exit($app->run($_SERVER['argv']));
