<?php
  include '../PHPFreebox/PHPFreebox.php';
  include 'config.php';

  $oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
  //$oFreebox->_debug  = true;

  $oFreebox->fs_createDirectory('/Disque dur/Musiques/AAAA');
  $oFreebox->fs_createDirectory('/Disque dur/Musiques/BBBB');
  $oFreebox->fs_move('/Disque dur/Musiques/BBBB', '/Disque dur/Musiques/AAAA');
  $oFreebox->fs_copy('/Disque dur/Musiques/AAAA', '/Disque dur/Musiques/CCCC');
  $oFreebox->fs_removeDirectory('/Disque dur/Musiques/AAAA');
  $oFreebox->fs_removeDirectory('/Disque dur/Musiques/CCCC');