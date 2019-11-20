<?php
/**
 * bootstrapper for the tests suite.
 *
 * @package Microsite
 * @subpackage Tests
 */

    $autoloader = realpath(__DIR__.'/../vendor/autoload.php');

    if($autoloader === false) {
        die('<b>Error:</b> The autoloader does not exist. Please run <code>composer update</code> first.');
    }
    
    require_once $autoloader;

    define('TESTS_ROOT', __DIR__);

    // start session before the tests suite outputs anything to stdout.
    session_start();
