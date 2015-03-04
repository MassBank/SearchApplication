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

function massbank_error_handler($num, $str, $file, $line, $context = null)
{
	// more: http://php.net/manual/en/function.set-error-handler.php
	
    if (!(error_reporting() & $num)) {
        // This error code is not included in error_reporting
        return;
    }
    
	require APP . '/core/error.php';
	$_controller = new Error(new ErrorException( $str, 1000001, $num, $file, $line ));
	$_controller->index();
	die;
}
// run autoloader
spl_autoload_register ( 'autoloader' );
set_error_handler( 'massbank_error_handler' );

require_once APP . '/config/config.php';

// start sessions
Session::init ();

?>