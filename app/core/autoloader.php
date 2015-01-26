<?php

function autoloader($class) {
	
	$filename = APP . "/controller/" . strtolower ( $class ) . ".php";
	if (file_exists ( $filename )) {
		require $filename;
	}
	
	$filename = APP . "/core/" . strtolower ( $class ) . ".php";
	if (file_exists ( $filename )) {
		require $filename;
	}
	
}

// run autoloader
spl_autoload_register ( 'autoloader' );

// start sessions
Session::init ();

require (APP . '/config/config.php');

?>