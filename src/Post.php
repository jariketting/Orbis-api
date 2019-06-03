<?php
namespace Orbis;

/**
 * Simple class to get post params with some validation
 *
 * Less is more :)
 *
 * Class Post
 * @package Orbis
 */
class Post
{
    /**
     * @param $name
     *
     * @return string
     */
    static function get(string $name) : string {
        //validate post
        if(isset($_POST))
            if(isset($_POST[$name]))
                return $_POST[$name]; //return post param

        return ''; //return empty string on default
    }

    static function overwrite(string $name, string $newValue) : bool {
        if(!self::exists($name))
            return false;

        $_POST[$name] = $newValue;

        return true;
    }

    /**
     * Check if post param isset
     *
     * @param $name
     *
     * @return bool
     */
    static function exists(string $name) : bool {
        //return false if post is not set
        if(!isset($_POST)) return false;

        //check if value exists
        return isset($_POST[$name]);
    }
}