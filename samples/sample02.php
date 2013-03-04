<?php
	/**
	 * Play with Airplay and photo
	 */

	include '../PHPFreebox/PHPFreebox.php';
	include 'config.php';

	$oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
	//$oFreebox->_debug  = true;
  $oFreebox->airPlay_setPicture(getcwd().'\statics\wikimedia_commons_caracal.jpg', PHPFreebox::AIRPLAY_SLIDE_RIGHT, 3000);
