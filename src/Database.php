<?php


namespace Orbis;


use PDO;
use PDOException;

class Database
{
    private static $_connection = null;

    public static function init(string $host, string $db, string $user, string $passwd) : bool {
        try {
            self::$_connection = new PDO("mysql:host=$host;dbname=$db", $user, $passwd);

            // set the PDO error mode to exception
            self::$_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
}