<?php

// DIRECTORY_SEPARATOR adds a slash to the end of the path
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// set a constant that holds the project's "application" folder, like "/var/www/application".
define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);

// turn on output buffering
ob_start ();

require(APP . '/core/autoloader.php');

//define routes
Router::get('home', 'home@index');
Router::get('search', 'search@index');
Router::get('search/quick', 'search@quick');
Router::get('search/peak', 'search@peak');

Router::get('data', 'data@index');

//execute matched routes
Router::dispatch();

// Flush (send) the output buffer
ob_flush ();

?>