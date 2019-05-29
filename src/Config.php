<?php


namespace Orbis;


class Config
{
    private static $_ini = null;

    public static function loadConfig(string $filename) : bool {
        $ini = parse_ini_file($filename, true);

        if($ini) {
            self::$_ini = $ini;

            return true;
        }


        return false;
    }

    public static function getConfig() {
        return self::$_ini;
    }
}