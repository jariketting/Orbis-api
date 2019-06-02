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

        $config = Config::getConfig(); //store config

        //show errors when in debug mode
        if($config['SETTINGS']['debug']) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
    }

    /**
     * Initialize database connection
     */
    public static function initDatabase() : void {
        $config = Config::getConfig(); //store config

        //setup database
        if(!Database::init(
            $config['DATABASE']['host'],
            $config['DATABASE']['db'],
            $config['DATABASE']['user'],
            $config['DATABASE']['passwd']
        ))
            JsonResponse::error('Unable to connect to database.', 'No connection could be made to the database');
    }
}