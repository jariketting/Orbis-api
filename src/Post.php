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
    static function get($name) {
        //validate post
        if(isset($_POST))
            if(isset($_POST[$name]))
                return $_POST[$name]; //return post param

        return ''; //return empty string on default
    }

    /**
     * Check if post param isset
     *
     * @param $name
     *
     * @return bool
     */
    static function exists($name) : bool {
        //return false if post is not set
        if(!isset($_POST)) return false;

        //check if value exists
        return isset($_POST[$name]);
    }
}