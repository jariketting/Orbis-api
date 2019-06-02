<?php
namespace Orbis;

/**
 * Class contains helper functions used in this project (acts like a functions.php file)
 *
 * Class Helper
 * @package Orbis
 */
class Helper
{
    CONST CONFIG_LOC = '../config.ini';

    /**
     * Initialize the config
     */
    public static function initConfig() : void {
        //stop application if config is not loaded
        if(!Config::loadConfig(self::CONFIG_LOC))
            JsonResponse::error('Unable to load config.', 'The config file could not be loaded.');
    }
}