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
        // Downloads
        print_r('Downloads : '.EOL);
        foreach ($oFreebox->getDownloads() as $download) {
            echo 'Fichier "'.$download['name'].'" ('.$download['size'].' bytes) : Statut = ';
            switch($download['status']){
                case 'done':        echo 'Téléchargé'; break;
                case 'stopped':     echo 'Arrêét'; break;
                case 'queued':      echo 'Ajouté à la queue'; break;
                case 'starting':    echo 'Se prépare au téléchargement'; break;
                case 'downloading': echo 'Téléchargement en cours'; break;
                case 'stopping':    echo 'Téléchargement en cours de finalisation'; break;
                case 'error':       echo 'Erreur lors du téléchargement'; break;
                case 'checking':    echo '(NZB) Vérification en cours'; break;
                case 'repairing':   echo '(NZB) Réparation en cours'; break;
                case 'extracting':  echo '(NZB) Extraction en cours'; break;
                case 'seeding':     echo '(BT) Partage en cours'; break;
                case 'retry':       echo 'A télécharger de nouveau'; break;
            }
            echo EOL;
        }
    }

