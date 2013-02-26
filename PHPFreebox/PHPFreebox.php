<?php 

	/**
	 * 
	 * @author Progi1984
	 *
	 */
	class PHPFreebox{
    // Constantes AirPlay
    const AIRPLAY_NONE = 'None';
    const AIRPLAY_SLIDE_LEFT = 'SlideLeft';
    const AIRPLAY_SLIDE_RIGHT = 'SlideRight';
    const AIRPLAY_DISSOLVE = 'Dissolve';

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
		 * @var Curl Object
		 */
		private $_oCurl;
		/**
		 * 
		 * @var boolean
		 */
		private $_debug;
    /**
     * Error
     * @var string
     */
    private $_error;

    private $_urlFreeboxPlayer = 'freebox-player.local';
    private $_portFreeboxPlayer = 7000;
		
		public function __construct($psUrl, $psLogin, $psPassword) {
			$this->_url = $psUrl;
			$this->_login = $psLogin;
			$this->_password = $psPassword;
			$this->_debug = false;
			// Initialization : CURL
			$this->_oCurl = curl_init();
			// Initialization : Cookie
			$this->_cookie = $this->_getCookie();
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
	    return $matches[1];
	  }
	  
	  /**
	   * Api call
	   * @param string $psMethod
	   * @param array $arrParameters
	   * @throws Exception
		 * @return string
	   */
	  public function _api($psMethod, array $arrParameters = array() ){
			$sPage = explode('.', $psMethod);
	    $sPage = $sPage[0].'.cgi';
	    
	    curl_setopt($this->_oCurl, CURLOPT_HEADER, 0);
	    curl_setopt($this->_oCurl, CURLOPT_URL, $this->_url.'/'.$sPage);
	    curl_setopt($this->_oCurl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($this->_oCurl, CURLOPT_POST, 1);
	    curl_setopt($this->_oCurl, CURLOPT_COOKIE, 'FBXSID='.$this->_cookie);
	    curl_setopt($this->_oCurl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	    // Form : JSON/RPC
	    curl_setopt($this->_oCurl, CURLOPT_POSTFIELDS, json_encode(array('jsonrpc' => '2.0', 'method' => $psMethod, 'params' => $arrParameters)));
	    $sReturnJSON = curl_exec($this->_oCurl);
	    if($this->_debug){
	    	echo '<pre>'.print_r($sReturnJSON, true).'</pre>';
	    }
	    $sReturnData = json_decode($sReturnJSON, true);
	    if($this->_debug){
	    	echo '<pre>'.print_r($sReturnJSON, true).'</pre>';
	    }
	    
	    if($sReturnData === false){
	      throw new Exception('PHPFreebox : _api : JSON Error');
	    }
	    if(isset($retour_json['error'])){ 
	      throw new Exception('PHPFreebox : _api : error : '.json_encode($retour_json));
	    }
	    return $sReturnData['result'];
	  }
	  
	  public function setDebug($pbDebug = false){
	  	if(is_bool($pbDebug)){
	  		$this->_debug = $pbDebug;
	  	}
	  	return $this;
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
        curl_setopt($this->_oCurl, CURLOPT_URL, 'http://192.168.1.32:'.$this->_portFreeboxPlayer.'/photo');

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
	  public function listDirectory($psDirectory = ''){
	  	return $this->_api('fs.list', array($psDirectory));
	  }
	  public function removeFile(){
	  	
	  }
	  public function createDirectory(){
	  
	  }
	  public function removeDirectory(){
	  	
	  }
	  public function downloadFile(){
	  	
	  }
	  public function uploadFile(){
	  	
	  }
	  public function moveFile(){
	  	
	  }
	  public function moveDirectory(){
	  	
	  }
	  
	  //===============================================
	  // API SeedBox
	  //===============================================
	  
	  //===============================================
	  // API Status
	  //===============================================
	  public function getVersion(){
	  	
	  }
	}