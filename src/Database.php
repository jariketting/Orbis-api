<?php
namespace Orbis;

use PDO;
use PDOException;

/**
 * Database handler
 *
 * Class Database
 * @package Orbis
 */
class Database
{
    private static $_connection = null; //stores the database connection

    /**
     * Init the database connection, has to be done once before trying to access the database.
     *
     * @param string $host hostname of database
     * @param string $db name of database
     * @param string $user username of database
     * @param string $passwd password of database
     *
     * @return bool true if successful connected, false on any error
     */
    public static function init(string $host, string $db, string $user, string $passwd) : bool {
        //try to connect, catch any errors
        try {
            //create new PDO database connection with given credentials
            self::$_connection = new PDO("mysql:host=$host;dbname=$db", $user, $passwd);

            // set the PDO error mode to exception
            self::$_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return true; //successful connection has been made
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Returns connection
     *
     * @return PDO
     */
    public static function get() : PDO {
        return self::$_connection;
    }
}