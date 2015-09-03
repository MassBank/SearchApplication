<?php

require_once APP . '/model/log/log4massbank.php';
require_once APP . '/config/config.php';
require_once APP . '/core/error.php';

function massbank_autoloader($class)
{
	
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
	
    $code = NULL;
    $log = new Log4Massbank();
    $log->error($str . "->" . $file . "(" . $line . ")");
	$_controller = new Error(new ErrorException( $str, $code, $num, $file, $line ));
	$_controller->index();
	die;
}

function massbank_fatal_error_handler()
{
	$error = error_get_last();
	if ( $error["type"] == E_ERROR ) {
		$log = new Log4Massbank();
		$log->error($error["message"] . "->" . $error["file"] . "(" . $error["line"] . ")");
		massbank_error_handler( $error["type"], $error["message"], $error["file"], $error["line"] );
	}
}

// run autoloader
spl_autoload_register ( 'massbank_autoloader' );
set_error_handler ( 'massbank_error_handler' );
register_shutdown_function ( 'massbank_fatal_error_handler' );

// start sessions
Session::init ();

?>