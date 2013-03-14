<?php
  include '../PHPFreebox/PHPFreebox.php';
  include 'config.php';

  $oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
  $oFreebox->_debug  = true;

  $oFreebox->remote_setKey(FREEBOX_REMOTE_CODE);
  //$oFreebox->remote_sendCommand(PHPFreebox::REMOTE_KEY_POWER); sleep(5);
  //$oFreebox->remote_sendCommand(PHPFreebox::REMOTE_KEY_OK);
  $oFreebox->remote_setChannel(1);
