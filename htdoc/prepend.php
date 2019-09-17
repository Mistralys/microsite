<?php 

require_once 'vendor/autoload.php';

define('MIGRATOR_ROOT', dirname(__FILE__));
define('MIGRATOR_URL', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].str_replace('index.php', '', $_SERVER['PHP_SELF']));
define('MIGRATOR_FRAMEWORK_URL', 'https://naeroth.com:3710/svn/ApplicationFramework');
define('MIGRATOR_FRAMEWORK_NAME', '1and1/application_framework');
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.MIGRATOR_ROOT.'/assets/classes');

session_start();

$localConfig = MIGRATOR_ROOT.'/config-local.php';
if(!file_exists($localConfig)) {
    die('Local configuration file does not exist.');
}

require_once $localConfig;

$requiredDefines = array(
    'MIGRATOR_ONEANDONE_LAPTOP',
    'MIGRATOR_SVN_BINARIES_PATH',
    'MIGRATOR_SVN_PATH',
    'MIGRATOR_FRAMEWORK_PATH',
    'MIGRATOR_FRAMEWORK_AUTH',
    'MIGRATOR_SVN_HOST',
    'MIGRATOR_DB_HOST',
    'MIGRATOR_DB_USER',
    'MIGRATOR_DB_PASSWORD',
);

foreach($requiredDefines as $name) {
    if(!defined($name)) {
        die('Configuration setting ['.$name.'] missing.');
    }
}

require_once MIGRATOR_ROOT.'/assets/functions.php';
require_once MIGRATOR_ROOT.'/assets/app-simulation.php';
require_once 'Migrator.php';
require_once 'UI.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);