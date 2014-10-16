<?php 

namespace Progi1984;

/**
 *
 * @author Progi1984
 *
 */
class PhpFreebox
{
    const VERSION = '0.1.0';

    const ACCESS_UNKNOWN = 'unknown';
    const ACCESS_PENDING = 'pending';
    const ACCESS_TIMEOUT = 'timeout';
    const ACCESS_GRANTED = 'granted';
    const ACCESS_DENIED = 'denied';

    const ERROR_SESSION = 'invalid_session';
    const ERROR_API_RETURN = 'invalid_api_return';

    /**
     * Curl object
     * @var resource
     */
    private $oCurl;
    /**
     * FreeBox URL
     * @var string
     */
    private $url = 'http://mafreebox.free.fr';
    /**
     * Application ID
     * @url http://dev.freebox.fr/sdk/os/login/#tokenrequest-object
     * @var string
     */
    private $loginAppId;
    /**
     * Application Name
     * @url http://dev.freebox.fr/sdk/os/login/#tokenrequest-object
     * @var string
     */
    private $loginAppName;
    /**
     * Application Token
     * @url http://dev.freebox.fr/sdk/os/login/#obtaining-an-app-token
     * @var string
     */
    private $loginAppToken;
    /**
     * Application Version
     * @url http://dev.freebox.fr/sdk/os/login/#tokenrequest-object
     * @var string
     */
    private $loginAppVersion;
    /**
     * Device Name
     * @url http://dev.freebox.fr/sdk/os/login/#tokenrequest-object
     * @var string
     */
    private $loginDeviceName;
    /**
     * Track ID for an app token
     * @url http://dev.freebox.fr/sdk/os/login/#track-authorization-progress
     * @var string
     */
    private $loginTrackId;
    /**
     * @var string
     */
    private $loginChallenge;
    /**
     * @var string
     */
    private $loginPassword;
    /**
     * @var string
     */
    private $loginPasswordSalt;
    /**
     * @var array
     */
    private $sessionPermissions;
    /**
     * @var string
     */
    private $sessionToken;

    /**
     * Constructor
     */
    public function __construct($appId, $appName, $appVersion, $deviceName)
    {
        // Initialization : CURL
        $this->oCurl = curl_init();

        $this->loginAppId = $appId;
        $this->loginAppName = $appName;
        $this->loginAppVersion = $appVersion;
        $this->loginDeviceName = $deviceName;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->oCurl) {
            curl_close($this->oCurl);
        }
    }

    public function login()
    {
        // #1 : Get the app token
        if (is_null($this->loginAppToken) && is_null($this->loginTrackId)) {
            $this->apiLoginAuthorize();
        }

        // #1.1 : Get the status of the app token
        do {
            // The app should monitor the status until it is different from pending
            $status = $this->apiLoginAuthorize($this->loginTrackId);
        } while ($status == self::ACCESS_PENDING);

        // #1.2 : Access Granted
        if ($status == self::ACCESS_GRANTED) {
            // #2 : Get the challenge
            $this->apiLogin();

            // #3 : Get the password
            $this->loginPassword = hash_hmac('sha1', $this->loginChallenge, $this->getAppToken());

            // #4 : Get the session
            return $this->apiLoginSession();
        }
        return $status;
    }

    public function logout()
    {
        return $this->apiLoginLogout();
    }

    public function resetAccess()
    {
        $this->loginAppToken = null;
        $this->loginTrackId = null;
    }

    public function getAppToken()
    {
        if (is_null($this->loginAppToken)) {
            $this->apiLoginAuthorize();
        }
        return $this->loginAppToken;
    }
    public function setAppToken($appToken)
    {
        $this->loginAppToken = $appToken;
        return $this;
    }

    public function getAppTrackId()
    {
        if (is_null($this->loginTrackId)) {
            $this->apiLoginAuthorize();
        }
        return $this->loginTrackId;
    }
    public function setAppTrackId($appTrackId)
    {
        $this->loginTrackId = $appTrackId;
        return $this;
    }

    public function isWifiEnabled()
    {
        if (is_null($this->sessionToken)) {
            return self::ERROR_SESSION;
        }
        $return = $this->apiWifiConfig();
        if ($return !== false) {
            return ($return['enabled'] == 1 ? true : false);
        }
        return self::ERROR_API_RETURN;
    }
    public function getWifiMACFilterState()
    {
        if (is_null($this->sessionToken)) {
            return self::ERROR_SESSION;
        }
        $return = $this->apiWifiConfig();
        if ($return !== false) {
            return $return['mac_filter_state'];
        }
        return self::ERROR_API_RETURN;
    }

    public function getConnection()
    {
        if (is_null($this->sessionToken)) {
            return self::ERROR_SESSION;
        }
        $return = $this->apiConnection();
        if ($return !== false) {
            return $return;
        }
        return self::ERROR_API_RETURN;
    }
    public function getDownloads()
    {
        if (is_null($this->sessionToken)) {
            return self::ERROR_SESSION;
        }
        $return = $this->apiDownloads();
        if ($return !== false) {
            return $return;
        }
        return self::ERROR_API_RETURN;
    }
    public function getFsFileUrl($path, $iThumbnailHeight = 480)
    {
        $arrayGet = array('inline' => 1);
        if (is_numeric($iThumbnailHeight)) {
            $arrayGet['thumbnail'] = $iThumbnailHeight.'p';
        }
        return $this->url.'/api/v3/dl/'.base64_encode($path).'?'.http_build_query($arrayGet);
    }
    public function getFsList($path, $bOnlyFolder = false, $bCountSubFolder = false, $bRemoveHidden = false)
    {
        if (is_null($this->sessionToken)) {
            return self::ERROR_SESSION;
        }
        $return = $this->apiFsLs($path, array('onlyFolder' => $bOnlyFolder, 'countSubFolder' => $bCountSubFolder, 'removeHidden' => $bRemoveHidden));
        if ($return !== false) {
            return $return;
        }
        return self::ERROR_API_RETURN;
    }
    public function getStorageDisk()
    {
        if (is_null($this->sessionToken)) {
            return self::ERROR_SESSION;
        }
        $return = $this->apiStorageDisk();
        if ($return !== false) {
            return $return;
        }
        return self::ERROR_API_RETURN;
    }
    public function getSystem()
    {
        if (is_null($this->sessionToken)) {
            return self::ERROR_SESSION;
        }
        $return = $this->apiSystem();
        if ($return !== false) {
            return $return;
        }
        return self::ERROR_API_RETURN;
    }

    // Login
    private function apiLogin()
    {
        curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/login/');
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_POST, false);
        $data = curl_exec($this->oCurl);
        $data = json_decode($data, true);
        if (!$data['success']) {
            return false;
        }
        $this->loginChallenge = isset($data['result']['challenge']) ? $data['result']['challenge'] : '';
        $this->loginPasswordSalt = isset($data['result']['password_salt']) ? $data['result']['password_salt'] : '';

        return true;
    }
    private function apiLoginAuthorize($trackId = null)
    {
        if (is_null($trackId)) {
            // Request authorization
            curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/login/authorize/');
            curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->oCurl, CURLOPT_HEADER, false);
            curl_setopt($this->oCurl, CURLOPT_POST, true);
            curl_setopt($this->oCurl, CURLOPT_POSTFIELDS, json_encode(array('app_id' => $this->loginAppId, 'app_name' => $this->loginAppName, 'app_version' => $this->loginAppVersion, 'device_name' => $this->loginDeviceName)));
            $data = curl_exec($this->oCurl);
            $data = json_decode($data, true);
            if (!$data['success']) {
                return false;
            }
            $this->loginAppToken = isset($data['result']['app_token']) ? $data['result']['app_token'] : null;
            $this->loginTrackId = isset($data['result']['track_id']) ? $data['result']['track_id'] : null;

            return true;
        } else {
            // Track authorization progress
            curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/login/authorize/'.$trackId);
            curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->oCurl, CURLOPT_HEADER, false);
            curl_setopt($this->oCurl, CURLOPT_POST, false);
            $data = curl_exec($this->oCurl);
            $data = json_decode($data, true);
            if (!$data['success']) {
                return false;
            }
            return $data['result']['status'];
        }
    }
    private function apiLoginSession()
    {
        curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/login/session/');
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_POST, true);
        curl_setopt($this->oCurl, CURLOPT_POSTFIELDS, json_encode(array('app_id' => $this->loginAppId, 'password' => $this->loginPassword)));
        $data = curl_exec($this->oCurl);
        $data = json_decode($data, true);
        if (!$data['success']) {
            return false;
        }
        $this->sessionPermissions = isset($data['result']['permissions']) ? $data['result']['permissions'] : array();
        $this->sessionToken = isset($data['result']['session_token']) ? $data['result']['session_token'] : null;
        return self::ACCESS_GRANTED;
    }
    private function apiLoginLogout()
    {
        curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/login/logout/');
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_POST, true);
        $data = curl_exec($this->oCurl);
        $data = json_decode($data, true);
        if (!$data['success']) {
            return false;
        }
        $this->loginChallenge = null;
        $this->loginPasswordSalt = null;
        $this->sessionPermissions = null;
        $this->sessionToken = null;

        return true;
    }
    // Connection
    private function apiConnection()
    {
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/connection/');
        curl_setopt($this->oCurl, CURLOPT_HTTPHEADER, array('X-Fbx-App-Auth: '.$this->sessionToken,));
        curl_setopt($this->oCurl, CURLOPT_POST, false);
        $data = curl_exec($this->oCurl);
        $data = json_decode($data, true);
        if (!$data['success']) {
            return false;
        }
        return $data['result'];
    }
    // Downloads
    private function apiDownloads()
    {
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/downloads/');
        curl_setopt($this->oCurl, CURLOPT_HTTPHEADER, array('X-Fbx-App-Auth: '.$this->sessionToken,));
        curl_setopt($this->oCurl, CURLOPT_POST, false);
        $data = curl_exec($this->oCurl);
        $data = json_decode($data, true);
        if (!$data['success']) {
            return false;
        }
        return (isset($data['result']) ? $data['result'] : array());
    }
    // File Systems
    private function apiFsLs($path, $options = array())
    {
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/fs/ls/'.base64_encode($path).'?'.http_build_query($options));
        curl_setopt($this->oCurl, CURLOPT_HTTPHEADER, array('X-Fbx-App-Auth: '.$this->sessionToken,));
        curl_setopt($this->oCurl, CURLOPT_POST, false);
        $data = curl_exec($this->oCurl);
        $data = json_decode($data, true);
        if (!$data['success']) {
            return false;
        }
        return $data['result'];
    }
    // Storage
    private function apiStorageDisk()
    {
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/storage/disk/');
        curl_setopt($this->oCurl, CURLOPT_HTTPHEADER, array('X-Fbx-App-Auth: '.$this->sessionToken,));
        curl_setopt($this->oCurl, CURLOPT_POST, false);
        $data = curl_exec($this->oCurl);
        $data = json_decode($data, true);
        if (!$data['success']) {
            return false;
        }
        return $data['result'];
    }
    // System
    private function apiSystem()
    {
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v3/system/');
        curl_setopt($this->oCurl, CURLOPT_HTTPHEADER, array('X-Fbx-App-Auth: '.$this->sessionToken,));
        curl_setopt($this->oCurl, CURLOPT_POST, false);
        $data = curl_exec($this->oCurl);
        $data = json_decode($data, true);
        if (!$data['success']) {
            return false;
        }
        return $data['result'];
    }
    // WIFI
    private function apiWifiConfig($arrayConfig = array())
    {
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/api/v2/wifi/config/');
        curl_setopt($this->oCurl, CURLOPT_HTTPHEADER, array('X-Fbx-App-Auth: '.$this->sessionToken,));

        if (empty($arrayConfig)) {
            curl_setopt($this->oCurl, CURLOPT_POST, false);
        } else {
            curl_setopt($this->oCurl, CURLOPT_POST, true);
        }
        $data = curl_exec($this->oCurl);
        $data = json_decode($data, true);
        if (!$data['success']) {
            return false;
        }
        return $data['result'];
    }
}
