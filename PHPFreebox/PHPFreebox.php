<?php 

	/**
	 * 
	 * @author Progi1984
	 *
	 */
	class PHPFreebox{
    const SYS_VERSION = '0.01-devel';
    // Constantes AirPlay
    const AIRPLAY_NONE = 'None';
    const AIRPLAY_SLIDE_LEFT = 'SlideLeft';
    const AIRPLAY_SLIDE_RIGHT = 'SlideRight';
    const AIRPLAY_DISSOLVE = 'Dissolve';
    // Constantes Réseau
    const NETWORK_WIFI_802_11n_Disabled = 'disabled';
    const NETWORK_WIFI_802_11n_20Mhz = '20';
    const NETWORK_WIFI_802_11n_40Mhz_Upper = '40_upper';
    const NETWORK_WIFI_802_11n_40Mhz_Lower = '40_lower';

    /**
     * @var array
     */
    private $_arrCache;
		/**
		 * 
		 * @var string
		 */
		private $_url;
		/**
		 * 
		 * @var string
		 */
		private $_login;
		/**
		 * 
		 * @var string
		 */
		private $_password;
		/**
		 * 
		 * @var string
		 */
		private $_cookie;
    /**
     *
     * @var string
     */
    private $_CSRFToken;
		/**
		 * 
		 * @var Curl Object
		 */
		private $_oCurl;
    /**
     * Error
     * @var string
     */
    private $_error;

    /**
     *
     * @var boolean
     */
    public $_debug;
    /**
     * @var boolean
     */
    public $_useCache;

    private $_urlFreeboxPlayer = '192.168.1.32';
    private $_portFreeboxPlayer = 7000;
		
		public function __construct($psUrl, $psLogin, $psPassword) {
			$this->_url = $psUrl;
			$this->_login = $psLogin;
			$this->_password = $psPassword;
			$this->_debug = false;
      $this->_useCache = false;
			// Initialization : CURL
			$this->_oCurl = curl_init();
			// Initialization : Cookie
			$this->_getCookie();
		}
		public function __destruct() {
			if($this->_oCurl){
				curl_close($this->_oCurl);
			}
		}
		
		/**
		 * Get the cookie
		 * @throws Exception
		 * @return string
		 */
		private function _getCookie(){
	    curl_setopt($this->_oCurl, CURLOPT_URL, $this->_url.'/login.php');
	    curl_setopt($this->_oCurl, CURLOPT_RETURNTRANSFER, 1);
	    
	    // Get the header
	    curl_setopt($this->_oCurl, CURLOPT_HEADER, 1);
	    
	    // Post informations
	    curl_setopt($this->_oCurl, CURLOPT_POST, 1);
	    curl_setopt($this->_oCurl, CURLOPT_POSTFIELDS, array('login' => $this->_login, 'passwd' => $this->_password));
	    
	    $data = curl_exec($this->_oCurl);
	    
	    // Get the cookie
	    preg_match('/FBXSID=\"([^"]*)/', $data, $matches);
	    
	    // W/Problem, no Cookies
	    if(count($matches) != 2){
	      throw new Exception('PHPFreebox : _getCookie : No Cookies');
	    }
      $this->_cookie = $matches[1];

      // X-FBX-CSRF-Token
      $data = explode("\r\n", $data);
      foreach($data as $item){
        if(substr($item, 0, 16) == 'X-FBX-CSRF-Token'){
          $this->_CSRFToken = trim(str_replace('X-FBX-CSRF-Token:', '', $item));
          break;
        }
      }
      return true;
    }
	  
	  /**
	   * Api call
	   * @param string $psMethod
	   * @param array $arrParameters
	   * @throws Exception
		 * @return string
	   */
	  public function _apiJSONGet($psMethod, array $arrParameters = array() ){
      // Utilisation du cache
      if($this->_useCache == true
        && isset($this->_arrCache['method'])
        && isset($this->_arrCache['result'])
        && $this->_arrCache['method'] == $psMethod
        && is_array($this->_arrCache['result'])){
        return $this->_arrCache['result'];
      } else {
        $arrPost = array();
        $arrPost['method'] = $psMethod;
        $arrPost['csrf_token'] = $this->_CSRFToken;
        $arrPost['jsonrpc'] = '2.0';
        if(!empty($arrParameters)){
          $arrPost['params'] = $arrParameters;
        }
        curl_setopt($this->_oCurl, CURLOPT_HEADER, false);
        $sPage = explode('.', $psMethod);
        $sPage = $sPage[0].'.cgi';
        curl_setopt($this->_oCurl, CURLOPT_URL, $this->_url.'/'.$sPage);
        curl_setopt($this->_oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_oCurl, CURLOPT_POST, true);
        curl_setopt($this->_oCurl, CURLOPT_COOKIE, 'FBXSID="'.$this->_cookie.'"');
        curl_setopt($this->_oCurl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        // Form : JSON/RPC
        curl_setopt($this->_oCurl, CURLOPT_POSTFIELDS, json_encode($arrPost));
        $sReturnJSON = curl_exec($this->_oCurl);
        if($this->_debug == true){
          echo '<pre>'.print_r($sReturnJSON, true).'</pre>';
        }
        $sReturnData = json_decode($sReturnJSON, true);
        if($this->_debug == true){
          echo '<pre>'.print_r($sReturnData, true).'</pre>';
        }
        if($sReturnData === false){
          throw new Exception('PHPFreebox : _api : JSON Error');
        }
        if(isset($sReturnData['error'])){
          throw new Exception('PHPFreebox : _api : error : '.json_encode($sReturnData['error']));
        }
        $this->_arrCache['method'] = $psMethod;
        $this->_arrCache['result'] = $sReturnData['result'];

        return $sReturnData['result'];
      }
	  }

    /**
     * @param string $psMethod
     * @param array $arrParameters
     * @param boolean $bPostFile
     * @throws Exception
     * @return bool
     */
    public function _apiJSONPost($psMethod, array $arrParameters = array(), $bPostFile = false){
      $arrParameters['csrf_token'] = $this->_CSRFToken;
      $arrParameters['method'] = $psMethod;

      $sPage = explode('.', $psMethod);
      $sPage = $sPage[0].'.cgi';
      curl_setopt($this->_oCurl, CURLOPT_HEADER, false);
      curl_setopt($this->_oCurl, CURLOPT_URL, $this->_url.'/'.$sPage);
      curl_setopt($this->_oCurl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($this->_oCurl, CURLOPT_POST, true);
      curl_setopt($this->_oCurl, CURLOPT_COOKIE, 'FBXSID="'.$this->_cookie.'"');
      if($bPostFile == false){
        curl_setopt($this->_oCurl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8','X-Requested-With: XMLHttpRequest'));
        curl_setopt($this->_oCurl, CURLOPT_POSTFIELDS, http_build_query($arrParameters));
      } else {
        curl_setopt($this->_oCurl, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest'));
        curl_setopt($this->_oCurl, CURLOPT_POSTFIELDS, $arrParameters);
      }
      $sReturn = curl_exec($this->_oCurl);
      if($this->_debug){
        echo 'CURL ('.curl_errno($this->_oCurl).') : '.curl_error($this->_oCurl);
        echo '<pre>'.print_r($sReturn, true).'</pre>';
      }
      if($sReturn === false){
        $this->setError('CURL ('.curl_errno($this->_oCurl).') : '.curl_error($this->_oCurl));
        return false;
      } else {
        if($bPostFile == false){
          return true;
        } else {
          $sReturnData = json_decode($sReturn, true);
          if($this->_debug){
            echo '<pre>'.print_r($sReturnData, true).'</pre>';
          }
          if(isset($sReturnData['error'])){
            if($sReturnData['error'] == 11){
              $this->setError('File ever existing in SeedBox');
              return false;
            } else {
              throw new Exception('PHPFreebox : _api : error : '.json_encode($sReturnData['error']));
            }
          } else {
            return $sReturnData['result'];
          }
        }
      }
    }

    private function setError($error = ''){
      $this->_error = $error;
    }

	  //===============================================
	  // API Configuration
	  //===============================================

    //===============================================
    // API AirPlay
    //===============================================
    public function airPlay_setPicture($psImage, $psTransition = AIRPLAY_NONE, $piTimeOut = 1000){
      // Init Error
      $this->setError();

      $hFile = fopen($psImage, 'rb');
      if($hFile){
        // Using a PUT method i.e. -XPUT
        curl_setopt($this->_oCurl, CURLOPT_PUT, true);
        curl_setopt($this->_oCurl, CURLOPT_URL, 'http://'.$this->_urlFreeboxPlayer.':'.$this->_portFreeboxPlayer.'/photo');

        curl_setopt($this->_oCurl, CURLOPT_HTTPHEADER, array('User-Agent: MediaControl/1.0', 'X-Apple-Transition: '.$psTransition, 'Content-Length: ' . filesize($psImage), 'Accept:', 'Content-Type:', 'Expect:', 'Host:'));
        curl_setopt($this->_oCurl, CURLOPT_TIMEOUT_MS, $piTimeOut);
        // Binary transfer i.e. --data-BINARY
        curl_setopt($this->_oCurl, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($this->_oCurl, CURLOPT_INFILE, $hFile);
        curl_setopt($this->_oCurl, CURLOPT_INFILESIZE, filesize($psImage));

        curl_setopt($this->_oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_oCurl, CURLOPT_VERBOSE, true);
        curl_setopt($this->_oCurl, CURLOPT_SSL_VERIFYPEER, 0);
        $sReturnData = curl_exec($this->_oCurl);
        if ($sReturnData === FALSE) {
          if(curl_error($this->_oCurl == CURLE_OPERATION_TIMEOUTED)){
            return true;
          } else {
            $this->setError('CURL ('.curl_errno($this->_oCurl).') : '.curl_error($this->_oCurl));
            return false;
          }
        } else {
          return true;
        }

        curl_setopt($this->_oCurl, CURLOPT_PUT, false);
        fclose($hFile);
      } else {
        return null;
      }
    }

	  //===============================================
	  // API FileSystem
	  //===============================================
	  /**
	   * 
	   * @param string $psDirectory
	   * @return array
	   */
	  public function fs_listDirectory($psDirectory = ''){
	  	return $this->_apiJSONGet('fs.list', array($psDirectory));
	  }
	  public function removeFile(){
	  	
	  }
	  public function fs_createDirectory($psDirectory){
      return $this->_apiJSONGet('fs.mkdir', array($psDirectory));
	  }
	  public function fs_removeDirectory($psDirectory){
      return $this->_apiJSONGet('fs.remove', array($psDirectory));
	  }
	  public function downloadFile($psElement, $psLocalFile){
      curl_setopt($this->_oCurl, CURLOPT_URL, $this->_url.'/fs.cgi?file='.urlencode($psElement));
      curl_setopt($this->_oCurl, CURLOPT_COOKIE, 'FBXSID="'.$this->_cookie.'"');
      curl_setopt($this->_oCurl, CURLOPT_RETURNTRANSFER, true);
      $data = curl_exec($this->_oCurl);
      if($data === false){
        return false;
      } else {
        file_put_contents($psLocalFile, $data);
        return true;
      }
    }
    public function fs_copy($psElement, $psDestination){
      return $this->_apiJSONGet('fs.copy', array(array($psElement), $psDestination));
    }
    public function fs_move($psElement, $psDestination){
      return $this->_apiJSONGet('fs.move', array(array($psElement), $psDestination));
	  }

    //===============================================
    // API Réseau
    //===============================================
    /*
     * @param boolean $pbEnabled
     * @param integer $piChannel
     * @param string $psMode
     * @return boolean
     */
    public function network_setWifiStatus($pbEnabled, $piChannel = 11, $psMode = PHPFreebox::NETWORK_WIFI_802_11n_20Mhz){
      if(!is_bool($pbEnabled)){
        return false;
      }
      if(!is_int($piChannel)){
        return false;
      }
      if($piChannel < 1 || $piChannel > 13){
        return false;
      }
      if($psMode != PHPFreebox::NETWORK_WIFI_802_11n_Disabled
        && $psMode != PHPFreebox::NETWORK_WIFI_802_11n_20Mhz
        && $psMode != PHPFreebox::NETWORK_WIFI_802_11n_40Mhz_Lower
        && $psMode != PHPFreebox::NETWORK_WIFI_802_11n_40Mhz_Upper){
        return false;
      }

      // Parametres
      $arrParameters = array();
      $arrParameters['channel'] = $piChannel;
      $arrParameters['config'] = 'Valider';
      $arrParameters['ht_mode'] = $psMode;
      if($pbEnabled == true){
        $arrParameters['enabled'] = 'on';
      }
      return $this->_apiJSONPost('wifi.ap_params_set', $arrParameters);
    }

    public function network_getWifiConfig($psInfo){
      if(is_string($psInfo) && in_array($psInfo, array('channel', 'is_enabled', 'mode'))){
        $arrResult = $this->_apiJSONGet('wifi.config_get');
        switch($psInfo){
          case 'channel' : return $arrResult['ap_params']['channel']; break;
          case 'is_enabled' : return $arrResult['ap_params']['enabled']; break;
          case 'mode':
            switch($arrResult['ap_params']['ht']['ht_mode']){
              case 20: return PHPFreebox::NETWORK_WIFI_802_11n_20Mhz;
            }
            break;
        }

      }
      return '';
    }
    public function network_getWifiConfig_FreeWifi($psInfo){
      if(is_string($psInfo) && in_array($psInfo, array('is_enabled'))){
        $arrResult = $this->_apiJSONGet('wifi.config_get');
        switch($psInfo){
          case 'is_enabled' : return $arrResult['bss']['freewifi']['params']['enabled']; break;
        }

      }
      return '';
    }
    public function network_getWifiConfig_PrivateNetwork($psInfo){
      if(is_string($psInfo) && in_array($psInfo, array('encryption', 'is_enabled', 'is_macfilter_enabled', 'is_ssid_hidden', 'key', 'ssid', 'version_eapol'))){
        $arrResult = $this->_apiJSONGet('wifi.config_get');
        switch($psInfo){
          case 'encryption' : return $arrResult['bss']['perso']['params']['encryption']; break;
          case 'is_enabled' : return $arrResult['bss']['perso']['params']['enabled']; break;
          case 'is_macfilter_enabled' : return $arrResult['bss']['perso']['params']['mac_filter']; break;
          case 'is_ssid_hidden' : return $arrResult['bss']['perso']['params']['hide_ssid']; break;
          case 'key' : return $arrResult['bss']['perso']['params']['key']; break;
          case 'ssid' : return $arrResult['bss']['perso']['params']['ssid']; break;
          case 'version_eapol' : return $arrResult['bss']['perso']['params']['eapol_version']; break;
        }

      }
      return '';
    }
    public function network_getMacAddress(){
      return $this->_apiJSONGet('system.mac_address_get');
    }

    //===============================================
    // API SeedBox
    //===============================================
	  public function seedbox_addLink($psUrl){
      $arrParameters = array();
      $arrParameters['url'] = $psUrl;
      $arrParameters['user'] = $this->_login;
      return $this->_apiJSONPost('download.http_add', $arrParameters);
    }
    public function seedbox_addTorrentURL($psUrl){
      $arrParameters = array();
      $arrParameters['url'] = $psUrl;
      $arrParameters['user'] = $this->_login;
      return $this->_apiJSONPost('download.torrent_add', $arrParameters);
    }
    public function seedbox_addTorrentFile($psFile){
      $arrParameters = array();
      $arrParameters['data'] = '@'.$psFile;
      $arrParameters['url'] = '';
      $arrParameters['user'] = $this->_login;
      return $this->_apiJSONPost('download.torrent_add', $arrParameters, true);
    }
    public function seedbox_list(){
      return $this->_apiJSONGet('download.list');
    }
    public function seedbox_removeLink($piId){
      return $this->_apiJSONGet('download.remove', array(0 => 'http', 1 => $piId));
    }
    public function seedbox_stopLink($piId){
      return $this->_apiJSONGet('download.stop', array(0 => 'http', 1 => $piId));
    }
    public function seedbox_startLink($piId){
      return $this->_apiJSONGet('download.start', array(0 => 'http', 1 => $piId));
    }
    public function seedbox_removeTorrent($piId){
      return $this->_apiJSONGet('download.remove', array(0 => 'torrent', 1 => $piId));
    }
    public function seedbox_stopTorrent($piId){
      return $this->_apiJSONGet('download.stop', array(0 => 'torrent', 1 => $piId));
    }
    public function seedbox_startTorrent($piId){
      return $this->_apiJSONGet('download.start', array(0 => 'torrent', 1 => $piId));
    }

	  //===============================================
	  // API Status
	  //===============================================
	  public function getVersionFreebox(){
	  	return $this->_apiJSONGet('system.fw_release_get');
	  }
    public function getVersion(){
      return PHPFreebox::SYS_VERSION;
    }

    //===============================================
    // API Systeme
    //===============================================
    public function system_reboot($piTimeout = 1){
      $arrParameters = array();
      $arrParameters['timeout'] = $piTimeout;
      return $this->_apiJSONPost('system.reboot', $arrParameters);
    }
    public function system_getSerial(){
      return $this->_apiJSONGet('system.serial_get');
    }
    public function system_getUptime(){
      return $this->_apiJSONGet('system.uptime_get');
    }

    //===============================================
    // API Television
    //===============================================
    public function TV_getPlaylistM3U(){
      return file_get_contents('http://'.$this->_url.'/freeboxtv/playlist.m3u');
    }
	}