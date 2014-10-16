<?php
    include '../src/PhpFreebox/PhpFreebox.php';
    include 'functions.php';
    include 'config.php';

    use \Progi1984\PhpFreebox;

    $oFreebox = new PhpFreebox('fr.progi1984.phpfreebox', 'PHPFreebox', PhpFreebox::VERSION, 'PC Test');


    if(sessionIsset('PHPFreebox_appToken') && sessionIsset('PHPFreebox_appTrackId') && sessionGet('PHPFreebox_appToken') != '' && sessionGet('PHPFreebox_appTrackId') != '' ){
        $oFreebox->setAppToken(sessionGet('PHPFreebox_appToken'));
        $oFreebox->setAppTrackId(sessionGet('PHPFreebox_appTrackId'));
    } else {
        sessionSet('PHPFreebox_appToken', $oFreebox->getAppToken());
        sessionSet('PHPFreebox_appTrackId', $oFreebox->getAppTrackId());
    }

    if($oFreebox->login() != PhpFreebox::ACCESS_GRANTED){
        sessionSet('PHPFreebox_appToken', '');
        sessionSet('PHPFreebox_appTrackId', '');
        $oFreebox->resetAccess();
        sessionSet('PHPFreebox_appToken', $oFreebox->getAppToken());
        sessionSet('PHPFreebox_appTrackId', $oFreebox->getAppTrackId());
        $oFreebox->login();
    }

