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
	
	$filename = APP . "/exception/" . strtolower ( $class ) . ".php";
	if (file_exists ( $filename )) {
		require $filename;
	}
	
	$filename = APP . "/model/util/" . strtolower ( $class ) . ".php";
	if (file_exists ( $filename )) {
		require $filename;
	}
	
	$filename = APP . "/model/service/" . strtolower ( $class ) . ".php";
	if (file_exists ( $filename )) {
		require $filename;
	}
	
	$filename = APP . "/entity/param/" . strtolower ( $class ) . ".php";
	if (file_exists ( $filename )) {
		require $filename;
	}
	
}

// run autoloader
spl_autoload_register ( 'autoloader' );

require_once APP . '/config/config.php';

// start sessions
Session::init ();

?>