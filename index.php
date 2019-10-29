<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/autoloader.php';


$pattern = ucfirst($argv[1]);

$namespace = "App\\Generators\\$pattern\\Handler";
$class = new $namespace();
$class->prompt()
->design()
->report();
