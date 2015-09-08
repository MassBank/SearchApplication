<?php

// DIRECTORY_SEPARATOR adds a slash to the end of the path
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// set a constant that holds the project's "application" folder, like "/var/www/application".
define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);

// turn on output buffering
ob_start ();

require(APP . '/core/autoloader.php');

// define routes
Router::get('search', 'search@index'); 				// GET request
Router::get('search/quick', 'search@quick'); 		// GET request
Router::get('search/peak', 'search@peak'); 			// GET request

Router::post('data/sync', 'data@sync'); 			// POST request
Router::get('data/merge', 'data@merge_resource'); 	// GET request
// Router::get('data/sync', 'data@sync');
// Router::get('data/mergedata', 'data@merge_resource_data');
// Router::get('data', 'data@index');

// execute matched routes
Router::dispatch();

// Flush (send) the output buffer
ob_flush ();

?>