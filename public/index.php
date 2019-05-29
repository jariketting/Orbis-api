<?php
require_once '../vendor/autoload.php';
use Orbis\Config;

//stop application if config is not loaded
if(!Config::loadConfig('../config.ini'))
    die();

//show errors when in debug mode
if(Config::getConfig()['SETTINGS']['debug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

//header('Content-Type: application/json');

Orbis\Helper::test();