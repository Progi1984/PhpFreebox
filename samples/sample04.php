<?php
  /**
   *
   */

  include '../PHPFreebox/PHPFreebox.php';
  include 'config.php';

  $oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
  //$oFreebox->_debug = true;
  // Sample File : ISO Ubuntu
  $dlReturn = $oFreebox->seedbox_addLink('http://releases.ubuntu.com/releases/precise/ubuntu-12.04.2-desktop-i386.iso');
  echo 'addLink : '; var_dump($dlReturn);
  // Sample Torrent : ISO Fedora
  $dlReturn = $oFreebox->seedbox_addTorrentURL('http://torrent.fedoraproject.org/torrents/Fedora-18-source-DVD.torrent');
  echo 'addTorrentURL : '; var_dump($dlReturn);
  // Sample Torrent : ISO Fedora
  $dlReturn = $oFreebox->seedbox_addTorrentFile(getcwd().DIRECTORY_SEPARATOR.'statics'.DIRECTORY_SEPARATOR.'Fedora-18-i386-DVD.torrent');
  echo 'addTorrentFile : '; var_dump($dlReturn);
  if($dlReturn !== false && is_int($dlReturn)){
    $oFreebox->seedbox_stopTorrent($dlReturn);
    $oFreebox->seedbox_startTorrent($dlReturn);
  }
