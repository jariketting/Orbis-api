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
    private static  $_request = null; //stores raw request, null means not initialized

    private static  $_type, //stores request type
                    $_action, //stores request action, must be get, add, update or delete
                    $_identifier; //stores identifier which is always an int

    /**
     * Initialize router
     */
    private static function _init() : void {
        //check if redirect url isset
        if(isset($_SERVER['REDIRECT_URL'])) self::$_request = $_SERVER['REDIRECT_URL'];
        else self::$_request = '';

        //if request is just a slash, set it to empty string
        if(self::$_request == '/') self::$_request = '';

        //explode raw params
        $params = explode('/', self::$_request);

        //set type, action and identifier
        self::$_type = (string)((isset($params[1])) ? $params[1] : '');
        self::$_action = (string)((isset($params[2])) ? $params[2] : '');
        self::$_identifier = (int)((isset($params[3])) ? $params[3] : '');

        //make sure action is valid
        if( self::$_action != 'get'     &&
            self::$_action != 'update'  &&
            self::$_action != 'add'     &&
            self::$_action != 'delete'
        ) {
            self::$_action = '';
            self::$_identifier = 0;
        }
    }

    /**
     * Get request type
     *
     * @return string
     */
    public static function getType() : string {
        if(self::$_request === null) self::_init();

        return self::$_type;
    }

    /**
     * Get request action
     *
     * @return string
     */
    public static function getAction() : string {
        if(self::$_request === null) self::_init();

        return self::$_action;
    }

    /**
     * Get request identifier
     *
     * @return int
     */
    public static function getIdentifier() : int {
        if(self::$_request === null) self::_init();

        return self::$_identifier;
    }
}