<?php
require_once '../vendor/autoload.php';
use Orbis\Config;
use Orbis\Database;
use Orbis\JsonResponse;

$response = new JsonResponse(); //create response

//stop application if config is not loaded
if(!Config::loadConfig('../config.ini'))
    $response->error('Unable to load config.', 'The config file could not be loaded.');

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
    $response->error('Unable to connect to database.', 'No connection could be made to the database');

/**
 * Do stuff
 */

$response->print();