<?php
require_once '../vendor/autoload.php';

use Orbis\Helper;
use Orbis\JsonResponse;
use Orbis\Router;
use Orbis\Session;

Helper::initConfig();
Helper::initDatabase();

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