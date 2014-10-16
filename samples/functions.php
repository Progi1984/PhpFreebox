<?php

    function sessionSet($key, $value){
        if(PHP_SAPI == 'cli'){
            return file_put_contents(PATH_DATA.DIRECTORY_SEPARATOR.'PHPWord_'.$key, $value);
        } else {
            return ($_SESSION[$key] = $value);
        }
    }

    function sessionGet($key){
        if(PHP_SAPI == 'cli'){
            return file_get_contents(PATH_DATA.DIRECTORY_SEPARATOR.'PHPWord_'.$key);
        } else {
            return $_SESSION[$key];
        }
    }

    function sessionIsset($key){
        if(PHP_SAPI == 'cli'){
            return file_exists(PATH_DATA.DIRECTORY_SEPARATOR.'PHPWord_'.$key);
        } else {
            return isset($_SESSION[$key]);
        }
    }

