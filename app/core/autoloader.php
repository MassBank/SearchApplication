<?php

require_once APP . '/config/config.php';
require_once APP . '/core/error.php';

function massbank_autoloader($class)
{
	include_class_file("/controller/", $class);
	include_class_file("/core/", $class);
	include_class_file("/exception/", $class);
	include_class_file("/model/util/", $class);
	include_class_file("/model/service/", $class);
	include_class_file("/entity/param/", $class);
	include_class_file("/model/log/", $class);
	include_class_file("/model/logic/", $class);
	include_class_file("/model/db/", $class);
}

function include_class_file($dir, $class)
{
	$filename = APP . $dir . strtolower ( $class ) . ".php";
	if (file_exists ( $filename )) {
		require_once $filename;
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