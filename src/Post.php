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
     * Initialize post
     */
    static function init() : void {
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        $contentType = strtolower($contentType);

        //Make sure that the content type of the POST request has been set to application/json
        if($contentType == 'application/json') {

            //Receive the RAW post data.
            $content = trim(file_get_contents("php://input"));

            $content = json_decode($content, JSON_OBJECT_AS_ARRAY);

            if (json_last_error() === JSON_ERROR_NONE)
                $_POST = $content;
        }
    }

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