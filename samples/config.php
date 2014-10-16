<?php
    session_start();

    define('EOL', PHP_SAPI == 'cli' ? PHP_EOL : '<br />');

    /** Error reporting */
    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

    define('PATH_DATA', getcwd().'/data');

    if(!is_writeable(PATH_DATA)){
        throw new Exception('PATH_DATA ('.PATH_DATA.') not writable');
    }
