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
}