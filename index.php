<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/autoloader.php';

(new App\Controller\Index())->run($argv);
