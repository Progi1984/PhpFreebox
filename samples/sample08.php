<?php
  include '../PHPFreebox/PHPFreebox.php';
  include 'config.php';

  $oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
  //$oFreebox->_debug  = true;
  $oFreebox->_useCache = true;

  // Doc : Transmission RPC (https://trac.transmissionbt.com/browser/trunk/extras/rpc-spec.txt#L441)
  echo '<ul>';
    echo '<li>max global download speed (KBps) :                                                                 : '.$oFreebox->transmission_get('alt-speed-down').'</li>';
    echo '<li>true means use the alt speeds                                                                      : '.$oFreebox->transmission_get('alt-speed-enabled').'</li>';
    echo '<li>when to turn on alt speeds (units: minutes after midnight)                                         : '.$oFreebox->transmission_get('alt-speed-time-begin').'</li>';
    echo '<li>true means the scheduled on/off times are used                                                     : '.$oFreebox->transmission_get('alt-speed-time-enabled').'</li>';
    echo '<li>when to turn off alt speeds (units: same)                                                          : '.$oFreebox->transmission_get('alt-speed-time-end').'</li>';
    echo '<li>what day(s) to turn on alt speeds (look at tr_sched_day)                                           : '.$oFreebox->transmission_get('alt-speed-time-day').'</li>';
    echo '<li>max global upload speed (KBps)                                                                     : '.$oFreebox->transmission_get('alt-speed-up').'</li>';
    echo '<li>Adresse de la blocklist pour utiliser pour "blocklist-update"                                      : '.$oFreebox->transmission_get('blocklist-url').'</li>';
    echo '<li>Etat de la blocklist(true means enabled)                                                           : '.$oFreebox->transmission_get('blocklist-enabled').'</li>';
    echo '<li>number of rules in the blocklist                                                                   : '.$oFreebox->transmission_get('blocklist-size').'</li>';
    echo '<li>maximum size of the disk cache (MB)                                                                : '.$oFreebox->transmission_get('cache-size-mb').'</li>';
    echo '<li>location of transmission\'s configuration directory                                                : '.$oFreebox->transmission_get('config-dir').'</li>';
    echo '<li>default path to download torrents                                                                  : '.utf8_decode($oFreebox->transmission_get('download-dir')).'</li>';
    echo '<li>max number of torrents to download at once (see download-queue-enabled)                            : '.$oFreebox->transmission_get('download-queue-size').'</li>';
    echo '<li>if true, limit how many torrents can be downloaded at once                                         : '.$oFreebox->transmission_get('download-queue-enabled').'</li>';
    echo '<li>Etat du DHT pour les torrents publics                                                              : '.$oFreebox->transmission_get('dht-enabled').'</li>';
    echo '<li>Etat de l\'encyption forc&eacute;e des Pairs ("required", "preferred", "tolerated")                : '.$oFreebox->transmission_get('encryption').'</li>';
    echo '<li>torrents we\'re seeding will be stopped if they\'re idle for this long                             : '.$oFreebox->transmission_get('idle-seeding-limit').'</li>';
    echo '<li>true if the seeding inactivity limit is honored by default                                         : '.$oFreebox->transmission_get('idle-seeding-limit-enabled').'</li>';
    echo '<li>path for incomplete torrents, when enabled                                                         : '.utf8_decode($oFreebox->transmission_get('incomplete-dir')).'</li>';
    echo '<li>true means keep torrents in incomplete-dir until done                                              : '.$oFreebox->transmission_get('incomplete-dir-enabled').'</li>';
    echo '<li>true means allow Local Peer Discovery in public torrents                                           : '.$oFreebox->transmission_get('lpd-enabled').'</li>';
    echo '<li>maximum global number of peers                                                                     : '.$oFreebox->transmission_get('peer-limit-global').'</li>';
    echo '<li>maximum global number of peers                                                                     : '.$oFreebox->transmission_get('peer-limit-per-torrent').'</li>';
    echo '<li>true means allow pex in public torrents                                                            : '.$oFreebox->transmission_get('pex-enabled').'</li>';
    echo '<li>port number                                                                                        : '.$oFreebox->transmission_get('peer-port').'</li>';
    echo '<li>true means pick a random peer port on launch                                                       : '.$oFreebox->transmission_get('peer-port-random-on-start').'</li>';
    echo '<li>true means enabled                                                                                 : '.$oFreebox->transmission_get('port-forwarding-enabled').'</li>';
    echo '<li>whether or not to consider idle torrents as stalled                                                : '.$oFreebox->transmission_get('queue-stalled-enabled').'</li>';
    echo '<li>torrents that are idle for N minuets aren\'t counted toward seed-queue-size or download-queue-size : '.$oFreebox->transmission_get('queue-stalled-minutes').'</li>';
    echo '<li>true means append ".part" to incomplete files                                                      : '.$oFreebox->transmission_get('rename-partial-files').'</li>';
    echo '<li>the current RPC API version                                                                        : '.$oFreebox->transmission_get('rpc-version').'</li>';
    echo '<li>the minimum RPC API version supported                                                              : '.$oFreebox->transmission_get('rpc-version-minimum').'</li>';
    echo '<li>filename of the script to run                                                                      : '.$oFreebox->transmission_get('script-torrent-done-filename').'</li>';
    echo '<li>whether or not to call the "done" script                                                           : '.$oFreebox->transmission_get('script-torrent-done-enabled').'</li>';
    echo '<li>La valeur du ratio de seed pour les torrents &agrave; utiliser                                     : '.$oFreebox->transmission_get('seedRatioLimit').'</li>';
    echo '<li>Etat du ratio                                                                                      : '.$oFreebox->transmission_get('seedRatioLimited').'</li>';
    echo '<li>max number of torrents to uploaded at once (see seed-queue-enabled)                                : '.$oFreebox->transmission_get('seed-queue-size').'</li>';
    echo '<li>if true, limit how many torrents can be uploaded at once                                           : '.$oFreebox->transmission_get('seed-queue-enabled').'</li>';
    echo '<li>max global download speed (KBps)                                                                   : '.$oFreebox->transmission_get('speed-limit-down').'</li>';
    echo '<li>true means enabled                                                                                 : '.$oFreebox->transmission_get('speed-limit-down-enabled').'</li>';
    echo '<li>max global upload speed (KBps)                                                                     : '.$oFreebox->transmission_get('speed-limit-up').'</li>';
    echo '<li>true means enabled                                                                                 : '.$oFreebox->transmission_get('speed-limit-up-enabled').'</li>';
    echo '<li>true means added torrents will be started right away                                               : '.$oFreebox->transmission_get('start-added-torrents').'</li>';
    echo '<li>true means the .torrent file of added torrents will be deleted                                     : '.$oFreebox->transmission_get('trash-original-torrent-files').'</li>';
    echo '<li>see below                                                                                          : '.print_r($oFreebox->transmission_get('units'), true).'</li>';
    echo '<li>true means allow utp                                                                               : '.$oFreebox->transmission_get('utp-enabled').'</li>';
    echo '<li>long version string                                                                                : '.$oFreebox->transmission_get('version').'</li>';
  echo '</ul>';

  $value = $oFreebox->transmission_get('encryption');
  echo 'GET Variable "encryption" : '.$value.'<br />';
  echo 'SET Variable "encryption"'.$oFreebox->transmission_set('encryption', ($value == 'required' ? 'preferred' : 'required')).'<br />';
  echo 'GET Variable "encryption" : '.$oFreebox->transmission_get('encryption').'<br />';

  print_r($oFreebox->transmission_exec('session-stats'));