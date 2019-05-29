<?php
require_once '../vendor/autoload.php';
use Orbis\Config;
use Orbis\Database;

//TODO fatal errors should be outputted in json format, so it could be handled in the APP

//stop application if config is not loaded
if(!Config::loadConfig('../config.ini'))
    die('Could not get config'); //TODO replace with json error

$config = Config::getConfig(); //store config

//show errors when in debug mode
if($config['SETTINGS']['debug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

//setup database
if(!Database::init(
    $config['DATABASE']['host'],
    $config['DATABASE']['db'],
    $config['DATABASE']['user'],
    $config['DATABASE']['passwd']
))
    die('Could not connect to database'); //TODO replace with json error

//header('Content-Type: application/json');

Orbis\Helper::test(); //just to show it works!