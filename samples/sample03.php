<?php
  /**
   * Gestion du Réseau
   */

  include '../PHPFreebox/PHPFreebox.php';
  include 'config.php';

  $oFreebox = new PHPFreebox(FREEBOX_API_URL, FREEBOX_API_LOGIN, FREEBOX_API_PASSWORD);
  //$oFreebox->_debug  = true;
  $oFreebox->_useCache = true;
  $oFreebox->network_setWifiStatus(false);
  $oFreebox->network_setWifiStatus(true);

  echo '<ul>';
    echo '<li> Mat&eacute;riel :';
      echo '<ul>';
        echo '<li> Addresse MAC '.$oFreebox->network_getMacAddress();
      echo '</ul>';
    echo '</li>';
    echo '<li> WIFI :';
      echo '<ul>';
        echo '<li> Config :';
          echo '<ul>';
            echo '<li> Activ&eacute; : '.$oFreebox->network_getWifiConfig('is_enabled').'</li>';
            echo '<li> Canal : '.$oFreebox->network_getWifiConfig('channel').'</li>';
            echo '<li> Mode 802.11n : '.$oFreebox->network_getWifiConfig('mode').'</li>';
          echo '</ul>';
        echo '</li>';
        echo '<li> R&eacute;seau Personnel :';
          echo '<ul>';
            echo '<li> Activ&eacute; : '.$oFreebox->network_getWifiConfig_PrivateNetwork('is_enabled').'</li>';
            echo '<li> Cacher le SSID : '.$oFreebox->network_getWifiConfig_PrivateNetwork('is_ssid_hidden').'</li>';
            echo '<li> SSID : '.$oFreebox->network_getWifiConfig_PrivateNetwork('ssid').'</li>';
            echo '<li> Type de protection : '.$oFreebox->network_getWifiConfig_PrivateNetwork('encryption').'</li>';
            echo '<li> Cl&eacute; : '.htmlentities($oFreebox->network_getWifiConfig_PrivateNetwork('key')).'</li>';
            echo '<li> Filtrage d\'adresse MAC : '.$oFreebox->network_getWifiConfig_PrivateNetwork('is_macfilter_enabled').'</li>';
            echo '<li> Version du protocole EAPOL : '.$oFreebox->network_getWifiConfig_PrivateNetwork('version_eapol').'</li>';
          echo '</ul>';
        echo '</li>';
        echo '<li> FreeWifi :';
        echo '<ul>';
          echo '<li> Activ&eacute; : '.$oFreebox->network_getWifiConfig_FreeWifi('is_enabled').'</li>';
        echo '</ul>';
        echo '</li>';
      echo '</ul>';
    echo '</li>';
  echo '</ul>';
