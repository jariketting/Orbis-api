<?php
namespace Orbis;


/**
 * Handles the configuration file for this project.
 * Any environment depended config stuff are stored in the config file.
 *
 * Class Config
 * @package Orbis
 */
class Config
{
    private static $_ini = null; //stores the ini file

    /**
     * Must be done ONCE before trying to access the getConfig function.
     * This parses the ini file and stores in the the $_ini var.
     *
     * @param string $filename filename and dir of the config, example: "../config.ini"
     *
     * @return bool true if successful loading and false on any error
     */
    public static function loadConfig(string $filename) : bool {
        //first check if the file exists then parse it
        if(file_exists($filename))
            self::$_ini = parse_ini_file($filename, true);

        //return true if loaded correctly
        if(self::$_ini)
            return true;

        return false; //return false on default
    }

    /**
     * Returns the loaded config file
     *
     * @return array is returned as array
     */
    public static function getConfig() : array {
        return self::$_ini; //return config file
    }
}