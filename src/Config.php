<?php


namespace Orbis;


class Config
{
    private static $_ini = null;

    public static function loadConfig(string $filename) : bool {
        if(file_exists($filename))
            self::$_ini = parse_ini_file($filename, true);

        if(self::$_ini)
            return true;

        return false;
    }

    public static function getConfig() {
        return self::$_ini;
    }
}