<?php

// DIRECTORY_SEPARATOR adds a slash to the end of the path
define ( 'ROOT', dirname ( __DIR__ ) . DIRECTORY_SEPARATOR );
// set a constant that holds the project's "application" folder, like "/var/www/application".
define ( 'APP', ROOT . 'app' . DIRECTORY_SEPARATOR );

require_once APP . '/config/config.php';
require_once APP . '/core/autoloader.php';
require_once APP . '/model/mq/mq_worker.php';

$log = new Log4Massbank();
$log->info("[START] worker run");
$mq_worker = new MqWorker();
$log->info("[END] worker run");
?>