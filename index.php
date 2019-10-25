<?php

namespace App;

require_once __DIR__ . '/vendor/autoload.php';

//Require all files in subdirectories
loadDependencies('/Logic/Architecture/Interfaces/');
loadDependencies('/Logic/Architecture/Abstracts/');
loadDependencies('/Logic/');

function loadDependencies($folder) {
	$dir = new \RecursiveDirectoryIterator(__DIR__ . $folder);
	foreach (new \RecursiveIteratorIterator($dir) as $file) {
		if (!is_dir($file)) {
			if( fnmatch('*.php', $file) ) {
				require_once $file;
			}
		}
	}
}

$language = ucfirst($argv[1]);
$patternName = ucfirst($argv[2]);

$namespace = "App\\Logic\\Generators\\$language\\DesignPattern\\$patternName";
$class = new $namespace();
$class->prompt();
$class->design();
$class->report();
