<?php

require_once __DIR__ . '/vendor/autoload.php';

//PSR-4 Autoloader
/**
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \App\Baz\Qux class
 * from /path/to/project/App/Baz/Qux.php:
 *
 *      new \App\Baz\Qux;
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {

	// project-specific namespace prefix
	$prefix = 'App\\';

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/App/';

	// does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
			// no, move to the next registered autoloader
			return;
	}

	// get the relative class name
	$relative_class = substr($class, $len);

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

	// if the file exists, require it
	if (file_exists($file)) {
			require $file;
	}
});

$pattern = ucfirst($argv[1]);

$namespace = "App\\Generators\\$pattern\\Handler";
$class = new $namespace();
$class->prompt();
$class->design();
$class->report();
