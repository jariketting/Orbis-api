<?php
namespace Orbis;


class Post
{
    static function get($name) {
        if(isset($_POST))
            if(isset($_POST[$name]))
                return $_POST[$name];

        return '';
    }
}