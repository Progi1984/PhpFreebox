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
    $oFreebox->seedbox_removeLink($item['id']);
  }
  if($item['type'] == 'torrent'){
    $oFreebox->seedbox_removeTorrent($item['id']);
  }
}
echo '<ul>';