<?php
ini_set('display_errors', 1);
ini_set('html_errors', false);
// Console mode is always in debug mode
define('DEBUG',true);
 //Check php version 5.4 only
if (version_compare(phpversion(), '5.4.0', '<') == true) { die ('PHP 5.4 Only'); }
// Define directory separator for different web servers based on Windows or Linux
define ('DIRSEP', DIRECTORY_SEPARATOR);
define('ROOTDIR', '/var/www/production');
define("CLI", true);
// include Autoload
include(__DIR__.'/../autoload.php'); // Autoload  PSR-4 only
$_SERVER['HTTP_HOST'] = '127.0.0.1'; // Set default HTTP HOST like local IP address
if(is_file(ROOTDIR.DIRSEP.'cli'.DIRSEP.'colors.inc.php'))
    require_once ROOTDIR.DIRSEP.'cli'.DIRSEP.'colors.inc.php';
\isv\IS::app()->cli();               // Start cli mode