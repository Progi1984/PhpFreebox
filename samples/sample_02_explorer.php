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

    if($oFreebox->login() == PhpFreebox::ACCESS_GRANTED){
        foreach ($oFreebox->getStorageDisk() as $disk) {
            foreach ($disk['partitions'] as $keyPartition => $partition) {
                $path = base64_decode($partition['path']);
                echo 'Nom du chemin : '.$path.EOL;

                $arrayFiles = $oFreebox->getFsList($path);
                foreach ($arrayFiles as $file) {
                    if($file['type'] == 'dir'){
                        echo '[DIR] ';
                    } else {
                        echo '[FILE] ';
                    }
                    echo $file['name'].EOL;

                    if($file['name'] == 'Vidéos'){
                        $arrayMedia = $oFreebox->getFsList(base64_decode($file['path']));
                        foreach ($arrayMedia as $media) {
                            if($media['type'] == 'dir'){
                                echo '[DIR] ';
                            } else {
                                echo '[FILE] ';
                            }
                            echo $file['name'].'/'.$media['name'];
                            if($media['type'] == 'file'){
                                echo ' ('.$media['size'].' bytes)';
                                echo EOL;
                                echo '[FILE] URL > '.$oFreebox->getFsFileUrl($media['name'], 200);
                            }
                            echo EOL;
                        }
                    }
                }
            }
        }
    }

