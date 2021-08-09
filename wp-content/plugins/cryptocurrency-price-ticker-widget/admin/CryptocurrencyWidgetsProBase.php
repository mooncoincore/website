<?php
namespace CryptocurrencyWidgetsProREG;

	class CryptocurrencyWidgetsProBase {
    	public $key = "A6A5758D3FBB2506";
    	private $product_id = "4";
    	private $product_base = "ccpwp";
    	private $server_host = "https://license.coolplugins.net/wp-json/licensor/";
    	private $hasCheckUpdate=true;
    	private $pluginFile;
        private static $selfobj=null;
        private $version="";
        private $isTheme=false;
        private $emailAddress = "";
		function __construct($plugin_base_file='')
		{
			$this->pluginFile=$plugin_base_file;
            $dir=dirname($plugin_base_file);
            $dir=str_replace('\\','/',$dir);
            if(strpos($dir,'wp-content/themes')!==FALSE){
                $this->isTheme=true;
            }
			$this->version=$this->getCurrentVersion();
			if($this->hasCheckUpdate) {
				if(function_exists("add_action")){
					add_action( 'admin_post_ccpwp_fupc', function(){
						update_option('_site_transient_update_plugins','');
						update_option('_site_transient_update_themes','');
						set_site_transient('update_themes', null);
						wp_redirect(  admin_url( 'plugins.php' ) );
						exit;
					});
					add_action( 'init', [$this,"initActionHandler"]);

				}
				if(function_exists("add_filter")) {
					//
					if($this->isTheme){
						add_filter('pre_set_site_transient_update_themes', [$this, "PluginUpdate"]);
						add_filter('themes_api', [$this, 'checkUpdateInfo'], 10, 3);
					}else{
						add_filter('pre_set_site_transient_update_plugins', [$this, "PluginUpdate"]);
						add_filter('plugins_api', [$this, 'checkUpdateInfo'], 10, 3);
						add_filter( 'plugin_row_meta', function($links, $plugin_file ){
							if ( $plugin_file == plugin_basename( $this->pluginFile ) ) {
								$links[] = " <a class='edit coption' href='" . esc_url( admin_url( 'admin-post.php' ) . '?action=ccpwp_fupc' ) . "'>Update Check</a>";
							}
							return $links;
						}, 10, 2 );
					}



				}


			}
		}
		public function setEmailAddress( $emailAddress ) {
            $this->emailAddress = $emailAddress==''?get_option('admin_email'):$emailAddress;
        }
		function initActionHandler(){
			$handler=hash("crc32b",$this->product_id.$this->key.$this->getDomain())."_handle";
			if(isset($_GET['action']) && $_GET['action']==$handler){
				$this->handleServerRequest();
				exit;
			}
		}
		function handleServerRequest(){
			$type=isset($_GET['type'])?strtolower($_GET['type']):"";
			switch ($type){
				case "rl": //remove license
					$this->removeOldWPResponse();
					$obj=new \stdClass();
					$obj->product=$this->product_id;
					$obj->status=true;
					echo $this->encryptObj($obj);
					return;
				case "dl": //delete plugins
					$obj          = new \stdClass();
					$obj->product = $this->product_id;
					$obj->status  = false;
					$this->removeOldWPResponse();
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					if($this->isTheme){
						$res=delete_theme($this->pluginFile);
						if(!is_wp_error($res)){
							$obj->status  = true;
						}
						echo $this->encryptObj( $obj);
					}else {
						$res=delete_plugins([plugin_basename($this->pluginFile)]);
						if(!is_wp_error($res)){
							$obj->status  = true;
						}
						echo $this->encryptObj( $obj);
					}
					return;
				default:
					return;
			}
		}
		function getCurrentVersion(){
			if( !function_exists('get_plugin_data') ){
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$data=get_plugin_data($this->pluginFile);
			if(isset($data['Version'])){
				return $data['Version'];
			}
			return 0;
		}
		function __plugin_updateInfo(){
            if(function_exists("wp_remote_get")) {
                $licenseInfo=self::GetRegisterInfo();
                if(!empty($licenseInfo->license_key)) {
                    $response = wp_remote_get( $this->server_host . "product/update/" . $this->product_id . "/".$licenseInfo->license_key );
                    if ( is_array( $response ) ) {
                        $body         = $response['body'];
                        $responseJson = json_decode( $body );
                        if ( is_object( $responseJson ) && ! empty( $responseJson->status ) && ! empty( $responseJson->data->new_version ) ) {
                            $responseJson->data->slug = plugin_basename( $this->pluginFile );;
                            $responseJson->data->new_version = ! empty( $responseJson->data->new_version ) ? $responseJson->data->new_version : "";
                            $responseJson->data->url         = ! empty( $responseJson->data->url ) ? $responseJson->data->url : "";
                            $responseJson->data->package     = ! empty( $responseJson->data->download_link ) ? $responseJson->data->download_link : "";

                            $responseJson->data->sections    = (array) $responseJson->data->sections;
                            $responseJson->data->plugin      = plugin_basename( $this->pluginFile );
                            $responseJson->data->icons       = (array) $responseJson->data->icons;
                            $responseJson->data->banners     = (array) $responseJson->data->banners;
                            $responseJson->data->banners_rtl = (array) $responseJson->data->banners_rtl;
                            unset( $responseJson->data->IsStoppedUpdate );

                            return $responseJson->data;
                        }
                    }
                }
            }
            return null;
        }
		function PluginUpdate($transient)
		{
			$response = $this->__plugin_updateInfo();
			if(!empty($response->plugin)){
                if($this->isTheme){
                    $theme_data = wp_get_theme();
                    $index_name="".$theme_data->get_stylesheet();
                }else{
                    $index_name=$response->plugin;
                }
                if (!empty($response) && version_compare($this->version, $response->new_version, '<')) {
                    unset($response->download_link);
                    unset($response->IsStoppedUpdate);
                    $transient->response[$index_name] = (object)$response;
                }
            }
            return $transient;
		}
		final function checkUpdateInfo($false, $action, $arg) {
			if($this->isTheme){
				if ( $arg->slug === $this->product_base){
					$response =$this->__plugin_updateInfo();
					if ( !empty($response)) {
						return $response;
					}
				}
			}else{
				if ( isset( $arg->slug ) && $arg->slug === plugin_basename($this->pluginFile) ) {
					$response =$this->__plugin_updateInfo();
					if ( !empty($response)) {
						return $response;
					}
				}
			}

			return $false;
		}

		/**
		 * @param $plugin_base_file
		 *
		 * @return self|null
		 */
		static function &getInstance($plugin_base_file=null) {
			if(empty(self::$selfobj)){
				if(!empty($plugin_base_file)) {
					self::$selfobj = new self( $plugin_base_file );
				}
			}
			return self::$selfobj;
		}

		private function encrypt($plainText,$password='') {
			if(empty($password)){
				$password=$this->key;
			}
			$plainText=rand(10,99).$plainText.rand(10,99);
			$method = 'aes-256-cbc';
			$key = substr( hash( 'sha256', $password, true ), 0, 32 );
			$iv = substr(strtoupper(md5($password)),0,16);
			return base64_encode( openssl_encrypt( $plainText, $method, $key, OPENSSL_RAW_DATA, $iv ) );
		}
		private function decrypt($encrypted,$password='') {
			if(empty($password)){
				$password=$this->key;
			}
			$method = 'aes-256-cbc';
			$key = substr( hash( 'sha256', $password, true ), 0, 32 );
			$iv = substr(strtoupper(md5($password)),0,16);
			$plaintext=openssl_decrypt( base64_decode( $encrypted ), $method, $key, OPENSSL_RAW_DATA, $iv );
			return substr($plaintext,2,-2);
		}

		function encryptObj( $obj ) {
			$text = serialize( $obj );

			return $this->encrypt( $text );
		}

		private function decryptObj( $ciphertext ) {
			$text = $this->decrypt( $ciphertext );

			return unserialize( $text );
		}

		private function getDomain() {
			if ( defined( "WPINC" ) && function_exists( "get_bloginfo" ) ) {
				return get_bloginfo( 'url' );
			} else {
				$base_url = ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == "on" ) ? "https" : "http" );
				$base_url .= "://" . $_SERVER['HTTP_HOST'];
				$base_url .= str_replace( basename( $_SERVER['SCRIPT_NAME'] ), "", $_SERVER['SCRIPT_NAME'] );

				return $base_url;
			}
		}

		private function getEmail() {
            return $this->emailAddress;
        }
		private function processs_response($response){
			$resbk="";
			if ( ! empty( $response ) ) {
				if ( ! empty( $this->key ) ) {
					$resbk=$response;
					$response = $this->decrypt( $response );
				}
				$response = json_decode( $response );

				if ( is_object( $response ) ) {
					return $response;
				} else {
					$response=new \stdClass();
					$response->status = false;
					$response->msg    = "Response Error, contact with the author or update the plugin or theme";
					if(!empty($bkjson)){
                        $bkjson=@json_decode($resbk);
                        if(!empty($bkjson->msg)){
                            $response->msg    = $bkjson->msg;
                        }
					}
					$response->data = NULL;
					return $response;

				}
			}
			$response=new \stdClass();
			$response->msg    = "unknown response";
			$response->status = false;
			$response->data = NULL;

			return $response;
		}
		private function _request( $relative_url, $data, &$error = '' ) {
			$response         = new \stdClass();
			$response->status = false;
			$response->msg    = "Empty Response";
			$finalData        = json_encode( $data );
			if ( ! empty( $this->key ) ) {
				$finalData = $this->encrypt( $finalData );
			}
			$url = rtrim( $this->server_host, '/' ) . "/" . ltrim( $relative_url, '/' );
			if(function_exists('wp_remote_post')) {
				$serverResponse = wp_remote_post($url, array(
						'method' => 'POST',
						'sslverify' => false,
						'timeout' => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => array(),
						'body' => $finalData,
						'cookies' => array()
					)
				);


				if (is_wp_error($serverResponse)) {
					$response->msg    = $serverResponse->get_error_message();;
					$response->status = false;
					$response->data = NULL;
					return $response;
				} else {
					 if(!empty($serverResponse['body']) && $serverResponse['body']!="GET404"){
                        return $this->processs_response($serverResponse['body']);
                    }
				}

			}
			if(!extension_loaded('curl')){
                $response->msg    = "Curl extension is missing";
                $response->status = false;
                $response->data = NULL;
                return $response;
			}
			//curl when fall back
			$curl             = curl_init();
			curl_setopt_array( $curl, array(
				CURLOPT_URL            => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_ENCODING       => "",
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 30,
				CURLOPT_CUSTOMREQUEST  => "POST",
				CURLOPT_POSTFIELDS     => $finalData,
				CURLOPT_HTTPHEADER     => array(
					"Content-Type: text/plain",
					"cache-control: no-cache"
				),
			) );
			$serverResponse = curl_exec( $curl );
			//echo $response;
			$error = curl_error( $curl );
			curl_close( $curl );
			if ( ! empty( $serverResponse ) ) {
				return $this->processs_response($serverResponse);
			}
			$response->msg    = "unknown response";
			$response->status = false;
			$response->data = NULL;
			return $response;
		}

		private function getParam( $purchase_key, $app_version, $admin_email = '' ) {
			$req               = new \stdClass();
			$req->license_key  = $purchase_key;
			$req->email        = ! empty( $admin_email ) ? $admin_email : $this->getEmail();
			$req->domain       = $this->getDomain();
			$req->app_version  = $app_version;
			$req->product_id   = $this->product_id;
			$req->product_base = $this->product_base;

			return $req;
		}

		function SaveWPResponse( $response ) {
			$key=hash('crc32b',$this->getDomain().$this->product_id."LIC");
			$data=$this->encrypt(serialize($response),$this->getDomain());
			update_option($key,$data) OR add_option($key,$data);
		}
		private function getOldWPResponse() {
			$key=hash('crc32b',$this->getDomain().$this->product_id."LIC");
			$response= get_option($key,null);
			if(empty($response)){
				return null;
			}
			return unserialize($this->decrypt($response,$this->getDomain()));
		}
		private function removeOldWPResponse() {
			$key=hash('crc32b',$this->getDomain().$this->product_id."LIC");
			return delete_option($key);
		}
		public static function RemoveLicenseKey($plugin_base_file,&$message = "") {
			$obj=self::getInstance($plugin_base_file);
			return $obj->_removeWPPluginLicense($message);
		}
		public static function CheckWPPlugin($purchase_key, $email,&$error = "", &$responseObj = null,$plugin_base_file="") {
			$obj=self::getInstance($plugin_base_file);
			$obj->setEmailAddress($email);
			return $obj->_CheckWPPlugin($purchase_key, $error, $responseObj);
		}
		final function _removeWPPluginLicense(&$message=''){
			$oldRespons=$this->getOldWPResponse();
			if(!empty($oldRespons->is_valid)) {
				if ( ! empty( $oldRespons->license_key ) ) {
					$param    = $this->getParam( $oldRespons->license_key, $this->version );
					$response = $this->_request( 'product/deactive/'.$this->product_id, $param, $message );
					if ( empty( $response->code ) ) {
						if ( ! empty( $response->status ) ) {
							$message = $response->msg;
							$this->removeOldWPResponse();
							return true;
						}else{
							$message = $response->msg;
						}
					}else{
						$message=$response->message;
					}
				}
			}
			return false;

		}
		public static function GetRegisterInfo() {
			if(!empty(self::$selfobj)){
				return self::$selfobj->getOldWPResponse();
			}
			return null;

		}

		final function _CheckWPPlugin( $purchase_key, &$error = "", &$responseObj = null ) {
			if(empty($purchase_key)){
				$this->removeOldWPResponse();
				$error="";
				return false;
			}
			$oldRespons=$this->getOldWPResponse();
			$isForce=false;
			if(!empty($oldRespons)) {
				if ( ! empty( $oldRespons->expire_date ) && strtolower( $oldRespons->expire_date ) != "no expiry" && strtotime( $oldRespons->expire_date ) < time() ) {
					$isForce = true;
				}
				if ( ! $isForce && ! empty( $oldRespons->is_valid ) && $oldRespons->next_request > time() && ( ! empty( $oldRespons->license_key ) && $purchase_key == $oldRespons->license_key ) ) {
					$responseObj = clone $oldRespons;
					unset( $responseObj->next_request );

					return true;
				}
			}
			$param    = $this->getParam( $purchase_key, $this->version );
			$response = $this->_request( 'product/active/'.$this->product_id, $param, $error );
			if(empty($response->code)) {
				if ( ! empty( $response->status ) ) {
					if ( ! empty( $response->data ) ) {
						$serialObj   = $this->decrypt( $response->data, $param->domain );

						$licenseObj = unserialize( $serialObj );
						if ( $licenseObj->is_valid ) {
							$responseObj = new \stdClass();
							$responseObj->is_valid = $licenseObj->is_valid;
							if($licenseObj->request_duration>0) {
								$responseObj->next_request = strtotime("+ {$licenseObj->request_duration} hour");
							}else{
								$responseObj->next_request=time();
							}
							$responseObj->expire_date = $licenseObj->expire_date;
							$responseObj->support_end = $licenseObj->support_end;
							$responseObj->license_title = $licenseObj->license_title;
							$responseObj->license_key = $purchase_key;
							$responseObj->msg = $response->msg;
							$this->SaveWPResponse($responseObj);
							unset($responseObj->next_request);
							return true;
						}else {
							$this->removeOldWPResponse();
							$error = !empty($response->msg)?$response->msg:"";
						}
					} else {
						$error = "Invalid data";
					}

				} else {
					$error = $response->msg;
				}
			}else{
				$error=$response->message;
			}

			return false;
		}

	}