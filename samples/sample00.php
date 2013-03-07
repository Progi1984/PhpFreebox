<?php
  include '../PHPFreebox/PHPFreebox.php';
  include 'config.php';

  $oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
  //$oFreebox->_debug  = true;

  echo 'Freebox : '.$oFreebox->getVersionFreebox().'<br />';
  echo 'PHPFreebox : '.$oFreebox->getVersion().'<br /><br />';

  echo 'Serial : '.$oFreebox->system_getSerial().'<br /><br />';
  $iUptime = $oFreebox->system_getUptime();
  $iUptimeSec = $iUptime;
  echo 'Uptime : '.$iUptimeSec.'s<br />';

  $iUptimeMin = (int)($iUptime/60);
  $iUptimeSec = $iUptime - ($iUptimeMin * 60);
  echo 'Uptime : '.$iUptimeMin.'m '.$iUptimeSec.'s<br />';

  $iUptimeHour = (int)($iUptime/60/60);
  $iUptimeMin = (int)(($iUptime - ($iUptimeHour * 60 * 60))/60);
  $iUptimeSec = $iUptime - ($iUptimeHour * 60 * 60) - ($iUptimeMin * 60);
  echo 'Uptime : '.$iUptimeHour.'h '.$iUptimeMin.'m '.$iUptimeSec.'s<br />';

  $iUptimeDay = (int)($iUptime/60/60/24);
  $iUptimeHour = (int)(($iUptime - $iUptimeDay * 60 * 60 * 24) /60 /60);
  $iUptimeMin = (int)(($iUptime - ($iUptimeDay * 60 * 60 * 24) - ($iUptimeHour * 60 * 60))/60);
  $iUptimeSec = $iUptime - ($iUptimeDay * 60 * 60 * 24) - ($iUptimeHour * 60 * 60) - ($iUptimeMin * 60);
  echo 'Uptime : '.$iUptimeDay.'d  '.$iUptimeHour.'h '.$iUptimeMin.'m '.$iUptimeSec.'s<br />';

  // Reboot
  //$result = $oFreebox->system_reboot(5);
  //echo 'reboot : '; var_dump($result);