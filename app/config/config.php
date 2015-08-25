<?php

/**
 * Configuration
 */
/**
 * Configuration for: Error reporting
 * Useful to show every little problem during development, but only show hard errors in production
 */
error_reporting(E_ALL);

/**
 * Configuration for: MySQL
 */
ini_set('max_execution_time', 300);
ini_set("default_socket_timeout", 300);
ini_set('memory_limit','256M');
ini_set('mysql.connect_timeout', 300);
ini_set('user_ini.cache_ttl', 300);
set_time_limit(300);
ini_set('display_errors', 1);
// ini_set('display_errors', 0);
ini_set('log_errors', 1);

// set_time_limit(60);
// ini_set('mysql.connect_timeout', 60);
// ini_set('max_execution_time', 60);
// ini_set('default_socket_timeout', 60);
// ini_set("display_errors", 1);

/**
 * Configuration for: TIMEZONE
 */
date_default_timezone_set("Japan");

/**
 * Configuration for: URL
 * Here we auto-detect your applications URL and the potential sub-folder. Works perfectly on most servers and in local
 * development environments (like WAMP, MAMP, etc.). Don't touch this unless you know what you do.
 *
 * URL_PUBLIC_FOLDER:
 * The folder that is visible to public, users will only have access to that folder so nobody can have a look into
 * "/application" or other folder inside your application or call any other .php file than index.php inside "/public".
 *
 * URL_PROTOCOL:
 * The protocol. Don't change unless you know exactly what you do.
 *
 * URL_DOMAIN:
 * The domain. Don't change unless you know exactly what you do.
 *
 * URL_SUB_FOLDER:
 * The sub-folder. Leave it like it is, even if you don't use a sub-folder (then this will be just "/").
 *
 * URL:
 * The final, auto-detected URL (build via the segments above). If you don't want to use auto-detection,
 * then replace this line with full URL (and sub-folder) and a trailing slash.
 */
define('URL_PUBLIC_FOLDER', 'public');
define('URL_PROTOCOL', 'http://');
define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
define('URL_SUB_FOLDER', str_replace(URL_PUBLIC_FOLDER, '', dirname($_SERVER['SCRIPT_NAME'])));
define('URL', URL_PROTOCOL . URL_DOMAIN . URL_SUB_FOLDER);
/**
 * Configuration for: Database
 * This is the place where you define your database credentials, database type etc.
 */
define('DB_TYPE', 'mysql');
define('DB_HOST', '<database host>');
define('DB_NAME', '<database name>');
define('DB_USER', '<database user>');
define('DB_PASS', '<database password>');

define('LOG_FILE_PREFIX', 'massbankapi');
define('LOG_FILE_FOLDER', ROOT . 'log/');

define('SITETITLE', 'MassBank | Database');

//set prefix for sessions
define('SESSION_PREFIX','massbank_');
?>