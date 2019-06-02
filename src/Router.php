<?php
namespace Orbis;

/**
 * Handles routing
 *
 * Class Router
 * @package Orbis
 */
class Router
{
    private static  $_request = null;

    private static  $_type,
                    $_action,
                    $_identifier;

    private static function _init() {
        if(isset($_SERVER['REDIRECT_URL'])) self::$_request = $_SERVER['REDIRECT_URL'];
        else self::$_request = '';

        if(self::$_request == '/') self::$_request = '';

        $params = explode('/', self::$_request);

        self::$_type = (string)((isset($params[1])) ? $params[1] : '');
        self::$_action = (string)((isset($params[2])) ? $params[2] : '');
        self::$_identifier = (int)((isset($params[3])) ? $params[3] : '');

        if( self::$_action != 'get'     &&
            self::$_action != 'update'  &&
            self::$_action != 'add'     &&
            self::$_action != 'delete'
        ) {
            self::$_action = '';
            self::$_identifier = 0;
        }
    }

    public static function getType() : string {
        if(self::$_request === null) self::_init();

        return self::$_type;
    }

    public static function getAction() : string {
        if(self::$_request === null) self::_init();

        return self::$_action;
    }

    public static function getIdentifier() : int {
        if(self::$_request === null) self::_init();

        return self::$_identifier;
    }
}