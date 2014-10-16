<?php
    include '../src/PhpFreebox/PhpFreebox.php';
    include 'functions.php';
    include 'config.php';

    use \Progi1984\PhpFreebox;

    $oFreebox = new PHPFreebox('fr.progi1984.phpfreebox', 'PHPFreebox', PHPFreebox::VERSION, 'PC Test');

    if(sessionIsset('PHPFreebox_appToken') && sessionIsset('PHPFreebox_appTrackId') && sessionGet('PHPFreebox_appToken') != '' && sessionGet('PHPFreebox_appTrackId') != '' ){
        $oFreebox->setAppToken(sessionGet('PHPFreebox_appToken'));
        $oFreebox->setAppTrackId(sessionGet('PHPFreebox_appTrackId'));
    } else {
        sessionSet('PHPFreebox_appToken', $oFreebox->getAppToken());
        sessionSet('PHPFreebox_appTrackId', $oFreebox->getAppTrackId());
    }

    if($oFreebox->login() == PHPFreebox::ACCESS_GRANTED){
        // WIFI
        print_r('WIFI > Etat : '.$oFreebox->isWifiEnabled().EOL);
        print_r('WIFI > Filtrage Mac : ');
        switch($oFreebox->getWifiMACFilterState()){
            case 'disabled':
                print_r('Désactivé');
                break;
            case 'whitelist':
                print_r('Liste blanche');
                break;
            case 'blacklist':
                print_r('Liste noire');
                break;
        }
        print_r(EOL);

        // Connexion
        $connection = $oFreebox->getConnection();
        print_r('Connexion > Status : ');
        switch($connection['state']){
            case 'going_up': print_r('Initialisation'); break;
            case 'up': print_r('Connecté'); break;
            case 'going_down': print_r('Déconnexion en cours'); break;
            case 'down': print_r('Déconnexion'); break;
        }
        print_r(EOL);
        print_r('Connexion > Débit UP : '.$connection['bandwidth_up'].' bits/s'.EOL);
        print_r('Connexion > Débit DOWN : '.$connection['bandwidth_down'].' bits/s'.EOL);
        print_r('Connexion > Données reçues : '.$connection['bytes_up'].' bytes'.EOL);
        print_r('Connexion > Données émis : '.$connection['bytes_down'].' bytes'.EOL);
        print_r('Connexion > IP v4 : '.$connection['ipv4'].' bits/s'.EOL);
        print_r('Connexion > IP v6 : '.$connection['ipv6'].' bits/s'.EOL);

        // System
        $system = $oFreebox->getSystem();
        print_r('System > Uptime : '.$system['uptime'].EOL);
        print_r('System > Adresse MAC : '.$system['mac'].EOL);

        // Disks
        print_r('Disques : '.EOL);
        foreach ($oFreebox->getStorageDisk() as $disk) {
            print_r('Disque '.$disk['model'].' > Temperature : '.$disk['temp'].'°C'.EOL);
            print_r('Disque '.$disk['model'].' > N° de série : '.$disk['serial'].EOL);
            print_r('Disque '.$disk['model'].' > Taille totale : '.$disk['total_bytes'].' bytes'.EOL);
            foreach ($disk['partitions'] as $keyPartition => $partition) {
                print_r('Disque '.$disk['model'].' > Partition #'.$keyPartition.' >  Nom : '.$partition['label'].EOL);
                print_r('Disque '.$disk['model'].' > Partition #'.$keyPartition.' >  Chemin : '.base64_decode($partition['path']).EOL);
                print_r('Disque '.$disk['model'].' > Partition #'.$keyPartition.' >  Espace libre : '.$partition['free_bytes'].' bytes'.EOL);
                print_r('Disque '.$disk['model'].' > Partition #'.$keyPartition.' >  Espace occupé : '.$partition['used_bytes'].' bytes'.EOL);
            }

        }

    }

