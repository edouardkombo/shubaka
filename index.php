<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/autoloader.php';

$container = (new App\Controller\ServiceContainer())->load(__DIR__ . '/App/Config/Services.json');
$process = (new App\Controller\Process($argv, $container));
$action = (new App\Controller\Action($container));
(new App\Controller\Index($container, $process, $action))->run();
