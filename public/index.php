<?php
require_once '../vendor/autoload.php';

use Orbis\Helper;
use Orbis\JsonResponse;
use Orbis\Router;
use Orbis\Session;
use Orbis\User;

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
    case 'login':
        Session::login();
        break;
    case 'logout':
        Session::logout();
        break;
    case 'reset_password':
        User::resetPassword();
        break;
    case 'user';
        User::request();
        break;

    default:
        JsonResponse::error('Invalid type given', 'A type was provided but seems to be invalid', 400);
        break;
}

JsonResponse::print();