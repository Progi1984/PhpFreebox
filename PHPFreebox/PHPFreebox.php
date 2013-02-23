<?php 

	/**
	 * 
	 * @author Progi1984
	 *
	 */
	class PHPFreebox{
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
	  
	  //===============================================
	  // API Configuration
	  //===============================================
	  
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