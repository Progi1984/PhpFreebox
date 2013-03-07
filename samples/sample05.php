<?php
/**
 *
 */

include '../PHPFreebox/PHPFreebox.php';
include 'config.php';

$oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
//$oFreebox->_debug = true;
$resDownloads = $oFreebox->seedbox_list();
echo '<ul>';
foreach($resDownloads as $item){
  if($item['type'] == 'http'){
    echo '<li>T&eacute;l&eacute;chargement de type FICHIER :';
      echo '<ul>';
        echo '<li> ID : '.$item['id'].'</li>';;
        echo '<li> Nom : '.$item['name'].'</li>';
        echo '<li> URL : '.$item['url'].'</li>';
        echo '<li> Bytes (total) : '.$item['size'].'</li>';
        echo '<li> Bytes (transf&eacute;r&eacute;) : '.$item['transferred'].'</li>';
        echo '<li> Statut : '.$item['status'].'</li>';
        echo '<li> Erreur : '.$item['errmsg'].'</li>';
        echo '<li> Taux de t&eacute;l&eacute;chargement : '.$item['rx_rate'].'</li>';
      echo '</ul>';
    echo '</li>';
  }
  if($item['type'] == 'torrent'){
    echo '<li>T&eacute;l&eacute;chargement de type TORRENT :';
      echo '<ul>';
        echo '<li> ID : '.$item['id'].'</li>';;
        echo '<li> Nom : '.$item['name'].'</li>';
        echo '<li> Bytes (total) : '.$item['size'].'</li>';
        echo '<li> Bytes (transf&eacute;r&eacute;) : '.$item['transferred'].'</li>';
        echo '<li> Statut : '.$item['status'].'</li>';
        echo '<li> Peer (Tx) : '.$item['tx_peer'].'</li>';
        echo '<li> Peer (Rx) : '.$item['rx_peer'].'</li>';
        echo '<li> Rate (Tx) : '.$item['tx_rate'].'</li>';
        echo '<li> Rate (Tx) : '.$item['rx_rate'].'</li>';
      echo '</ul>';
    echo '</li>';
  }
}
echo '<ul>';