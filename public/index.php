<?php
require_once '../vendor/autoload.php';
use Orbis\Config;
use Orbis\Database;
use Orbis\Helper;
use Orbis\JsonResponse;
use Orbis\Router;
use Orbis\Session;

Helper::initConfig();

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
    JsonResponse::error('Unable to connect to database.', 'No connection could be made to the database');

/**
 * Start routing
 */
if(!Router::getType())
    JsonResponse::error('No type given.', 'No type was provided in request.', 400);

switch (Router::getType()) {
    case 'validate_session':
        Session::validate();
        break;
    default:
        JsonResponse::error('Invalid type given', 'An type provided but seems to be invalid', 400);
        break;
}

JsonResponse::print();