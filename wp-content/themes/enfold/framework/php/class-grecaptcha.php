<?php
/**
 * Base class to handle Google reCAPTCHA API functionality.
 * 
 * @tutorial https://developers.google.com/recaptcha/docs/faq
 * @since 4.5.3
 * @since 4.5.7.2  refactored by Günter
 */
if ( ! defined('AVIA_FW') ) { exit( 'No direct script access allowed' ); }


if( ! class_exists( 'av_google_recaptcha' ) )
{
			
	class av_google_recaptcha 
	{
		
		const API_URL           = 'https://www.google.com/recaptcha/api.js';
		const API_VERIFY_URL    = 'https://www.google.com/recaptcha/api/siteverify';	
		
		const AJAX_VERIFY_NONCE = 'av_google_recaptcha_verify_nonce';
		const TRANSIENT_PREFIX	= 'av_google_recaptcha_';
		
		/**
		 *
		 * @since 4.5.7.2
		 * @var av_google_recaptcha 
		 */
		static protected $_instance = null;
		
		/**
		 * Version of reCAPTCHA
		 * 
		 * @since 4.5.7.2
		 * @var string 
		 */
		protected $version;

		/**
		 *
		 * @since 4.5.7.2
		 * @var array 
		 */
		protected $site_keys;
		
		/**
		 *
		 * @since 4.5.7.2
		 * @var array 
		 */
		protected $secret_keys;
		
		/**
		 * Stores the last verified keys (user might change keys and save without verifying)
		 * 
		 * @since 4.5.7.2
		 * @var array					'' | 'string combination to verify' | 'verify_error'
		 */
		protected $verified_keys;
		
		/**
		 * V3 score threshold (0.0 - 1.0)
		 * 
		 * @since 4.5.7.2
		 * @var float 
		 */
		protected $score;
		
		/**
		 * Backend theme color for V2
		 * 
		 * @since 4.5.7.2
		 * @var string 
		 */
		protected $theme;
		
		/**
		 * Contains the option value
		 * 
		 * @since 4.5.7.2
		 * @var string 
		 */
		protected $display_badge;
		
		/**
		 * Contains the HTML for replacement text
		 * 
		 * @since 4.6.2
		 * @var string 
		 */
		protected $display_badge_html;

		/**
		 * 
		 * @since 4.5.7.2
		 * @var boolean|null 
		 */
		protected $loading_prohibited;


		/**
		 * Return the instance of this class
		 * 
		 * @since 4.5.7.2
		 * @return av_google_recaptcha
		 */
		static public function instance()
		{
			if( is_null( av_google_recaptcha::$_instance ) ) 
			{
				av_google_recaptcha::$_instance = new av_google_recaptcha();
			}

			return av_google_recaptcha::$_instance;
		}	
		
		
		/**
		 * 
		 * @since 4.5.7.2
		 */
		protected function __construct() 
		{
			$this->loading_prohibited = null;
			$this->version = '';
			$this->site_keys = array();
			$this->secret_keys = array();
			$this->verified_keys = array();
			$this->score = 0.5;
			$this->theme = 'light';
			$this->display_badge = '';
			$this->display_badge_html = '';
			
				
			//	needed because not all framework functions are loaded at this point
			add_action( 'after_setup_theme', array( $this, 'handler_after_setup_theme' ), 10 );
			
			add_action( 'init', array( $this, 'handler_wp_register_scripts' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'handler_wp_enqueue_scripts' ), 500 );
			add_action( 'admin_enqueue_scripts', array( $this, 'handler_wp_admin_enqueue_scripts' ), 500 );
			
			add_filter( 'body_class', array( $this, 'handler_body_class' ), 500, 2 );
			
			//	We hook with low priority as reCAPTCHA verification should overrule other positive checks 
			add_filter( 'avf_form_send', array( $this, 'handler_avf_form_send' ), 999999, 4 );
			
			add_action( 'wp_ajax_avia_recaptcha_verify_frontend', array( $this, 'handler_recaptcha_verify_frontend' ), 10 );
			add_action( 'wp_ajax_nopriv_avia_recaptcha_verify_frontend', array( $this, 'handler_recaptcha_verify_frontend' ), 10 );
		}
		
		/**
		 * 
		 * @since 4.5.7.2
		 */
		public function __destruct() 
		{
			unset( $this->site_keys );
			unset( $this->secret_keys );
			unset( $this->verified_keys );
		}
		
		/**
		 * @since 4.5.7.2
		 */
		public function handler_after_setup_theme()
		{
			$this->version = avia_get_option( 'avia_recaptcha_version', '' );
			
			$this->site_keys['avia_recaptcha_v2'] = avia_get_option( 'avia_recaptcha_pkey_v2', '' );
			$this->site_keys['avia_recaptcha_v3'] = avia_get_option( 'avia_recaptcha_pkey_v3', '' );
			
			$this->secret_keys['avia_recaptcha_v2'] = avia_get_option( 'avia_recaptcha_skey_v2', '' );
			$this->secret_keys['avia_recaptcha_v3'] = avia_get_option( 'avia_recaptcha_skey_v3', '' );
			
			$this->verified_keys['avia_recaptcha_v2'] = avia_get_option( 'recaptcha_verified_keys_v2', '' );
			$this->verified_keys['avia_recaptcha_v3'] = avia_get_option( 'recaptcha_verified_keys_v3', '' );
			
			$this->score = avia_get_option( 'avia_recaptcha_score', 5 );
			$this->score = is_numeric( $this->score ) ? $this->score / 10.0 : 0.5;
			
			$this->display_badge = avia_get_option( 'avia_recaptcha_badge', '' );
			if( ! current_theme_supports( 'avia_recaptcha_show_legal_information' ) )
			{
				$this->display_badge = 'contact_only_message';
			}
			
			/**
			 * see https://developers.google.com/recaptcha/docs/faq#id-like-to-hide-the-recaptcha-badge-what-is-allowed
			 * 
			 * @since 4.5.7.2
			 * @param string
			 * @return string
			 */
			$badge =	'<div class="av-google-badge-message hidden">';
			$badge .=		__( 'This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy">Privacy Policy</a> and <a href="https://policies.google.com/terms">Terms of Service</a> apply.', 'avia_framework' );
			$badge .=	'</div>';

			$this->display_badge_html = apply_filters( 'avf_google_recaptcha_badge_content', $badge );

		}

		/**
		 * @since 4.5.7.2
		 */
		public function handler_wp_register_scripts()
		{
			$vn = avia_get_theme_version();
			
			wp_register_script( 'avia_google_recaptcha_front_script' , AVIA_JS_URL . 'conditional_load/avia_google_recaptcha_front.js', array( 'jquery' ), $vn, true );
			wp_register_script( 'avia_google_recaptcha_api_script' , AVIA_JS_URL . 'conditional_load/avia_google_recaptcha_api.js', array( 'jquery' ), $vn, true );
		}
		
		/**
		 * Frontend we load conditionally. This script checks after pageload if loading of main scripts are necessary. 
		 * 
		 * @since 4.5.7.2
		 */
		public function handler_wp_enqueue_scripts()
		{		
			if( $this->is_loading_prohibited() )
			{
				return;
			}
			
			wp_enqueue_script( 'avia_google_recaptcha_front_script' );
			
			$args = array(
					'version'			=> $this->get_version(),
					'site_key2'			=> $this->get_site_key( 'avia_recaptcha_v2' ),
					'site_key3'			=> $this->get_site_key( 'avia_recaptcha_v3' ),
					'api'				=> av_google_recaptcha::API_URL,
					'avia_api_script'	=> AVIA_JS_URL . 'conditional_load/avia_google_recaptcha_api.js',
					'theme'				=> $this->get_theme(),
					'score'				=> $this->get_score(),
					'verify_nonce'		=> wp_create_nonce( av_google_recaptcha::AJAX_VERIFY_NONCE ),
//					'submission_nonce'	=> wp_create_nonce( av_google_recaptcha::AJAX_SUBMISSION_NONCE ),
					'cannot_use'		=> '<h3 class="av-recaptcha-error-main">' . __( 'Sorry, a problem occurred trying to communicate with Google reCAPTCHA API. You are currently not able to submit the contact form. Please try again later - reload the page and also check your internet connection.', 'avia_framework' ) . '</h3>',
					'init_error_msg'	=> __( 'Initial setting failed. Sitekey 2 and/or sitekey 3 missing in frontend.', 'avia_framework' ),
					'v3_timeout_pageload'	=> __( 'Timeout occurred connecting to V3 API on initial pageload', 'avia_framework' ),
					'v3_timeout_verify'	=> __( 'Timeout occurred connecting to V3 API on verifying submit', 'avia_framework' ),
					'v2_timeout_verify'	=> __( 'Timeout occurred connecting to V2 API on verifying you as human. Please try again and check your internet connection. It might be necessary to reload the page.', 'avia_framework' ),
					'verify_msg'		=> __( 'Verify....', 'avia_framework' ),
					'connection_error'	=> __( 'Could not connect to the internet. Please reload the page and try again.', 'avia_framework' ),
					'validate_first'	=> __( 'Please validate that you are a human first', 'avia_framework' ),
					'validate_submit'	=> __( 'Before submitting we validate that you are a human first.', 'avia_framework' ),
					'no_token'			=> __( 'Missing internal token on valid submit - unable to proceed.', 'avia_framework' ),
					'invalid_version'	=> __( 'Invalid reCAPTCHA version found.', 'avia_framework' ),
					'api_load_error'	=> __( 'Google reCAPTCHA API could not be loaded.', 'avia_framework' ),
				);
			
			wp_localize_script( 'avia_google_recaptcha_front_script', 'AviaReCAPTCHA_front', $args );
		}
		
		
		/**
		 * 
		 * @since 4.5.7.2
		 */
		public function handler_wp_admin_enqueue_scripts()
		{
			/**
			 * Some 3rd party plugins need to supress loading scripts
			 * Not loading the scripts might result in breaking backend !!!
			 * Check if everything is working as expected.
			 * 
			 * @since 4.7.5.1
			 * @param boolean
			 * @return string			return 'skip_loading' to prohibit loading of backend scripts
			 */
			$skip_loading = apply_filters( 'avf_skip_enqueue_scripts_backend_grecaptcha', '' );
			
			if( 'skip_loading' === $skip_loading )
			{
				return;
			}
			
			/**
			 * In backend we must enqueue to validate keys and localize script
			 */
			wp_enqueue_script( 'avia_google_recaptcha_front_script' );
			
			$args = array(
					'version'       => $this->get_version(),
//					'site_key'      => $this->get_site_key(),
					'api'           => av_google_recaptcha::API_URL,
					'theme'			=> $this->get_theme(),
					'api_load_error'	=> __( 'Google reCAPTCHA API could not be loaded. We are not able to verify keys. Check your internet connection and try again.', 'avia_framework' ),
					'invalid_version'	=> __( 'Please select Version 2 or 3 for reCAPTCHA', 'avia_framework' ),
					'invalid_keys'		=> __( 'You have to enter a site key and a secret key to verify it.', 'avia_framework' ),
					'v3_timeout'		=> __( 'A network timeout problem occurred. Could be caused by an invalid V3 sitekey. Please recheck the key and try again.', 'avia_framework' )
	            );
			
			wp_localize_script( 'avia_google_recaptcha_front_script', 'AviaReCAPTCHA_data', $args );
		}
		
		/**
		 * We add classes to allow js script to decide what to load
		 * 
		 * @since 4.5.7.2
		 * @param array $classes
		 * @param array $class
		 * @return array
		 */
		public function handler_body_class( array $classes, array $class )
		{
			if( $this->is_loading_prohibited() )
			{
				return $classes;
			}
			
			/**
			 * Allows to disable recaptcha for special pages.
			 * Only makes sense for V3 as V2 is only loaded if needed on page/post.
			 * Be careful not to disable it on pages where you need it !!!
			 * 
			 * @since 4.5.7.2
			 * @param boolean
			 * @return boolean
			 */
			$disable = apply_filters( 'avf_disable_recaptchaV3_for_post', false );
			
			if( false === $disable )
			{
				$classes[] = 'av-recaptcha-enabled';
			}
			
			if( $this->show_extended_errors() )
			{
				$classes[] = 'av-recaptcha-extended-errors';
			}
			
			if( in_array( $this->display_badge, array( 'contact_only_message', 'hide' ) ) )
			{
				$classes[] = 'av-google-badge-hide';
			}
			
			return $classes;
		}
		
		/**
		 * Returns the selected version for the recaptcha
		 * 
		 * @since 4.5.7.2
		 * @return string		'' | 'avia_recaptcha_v2' | 'avia_recaptcha_v3'
		 */
		public function get_version()
		{
			return $this->version;
		}
		
		/**
		 * Returns the site key for the selected version
		 * 
		 * @since 4.5.7.2
		 * @param string $version
		 * @return string
		 */
		public function get_site_key( $version = null )
		{
			if( is_null( $version ) )
			{
				$version = $this->get_version();
			}
			
			return isset( $this->site_keys[ $version ] ) ? $this->site_keys[ $version ] : '';
		}
		
		/**
		 * Returns the secret key for the selected version
		 * 
		 * @since 4.5.7.2
		 * @param string $version
		 * @return string
		 */
		public function get_secret_key( $version = null )
		{
			if( is_null( $version ) )
			{
				$version = $this->get_version();
			}
			
			return isset( $this->secret_keys[ $version ] ) ? $this->secret_keys[ $version ] : '';
		}
		
		
		/**
		 * Returns the last saved verified key string:
		 *		"version site_key secret_key"
		 * 
		 * @since 4.5.7.2
		 * @param string $version
		 * @return string
		 */
		public function get_verified_keys( $version = null )
		{
			if( is_null( $version ) )
			{
				$version = $this->get_version();
			}
			
			return isset( $this->verified_keys[ $version ] ) ? $this->verified_keys[ $version ] : '';
		}
		
		/**
		 * Returns the float value for selected score threshold (0.0 - 1.0)
		 * 
		 * @since 4.5.7.2
		 * @return float
		 */
		public function get_score()
		{
			return $this->score;
		}
		
		/**
		 * Returns the necessary HTML if user selected to use
		 * 
		 * @since 4.5.7.2
		 * @return string
		 */
		public function get_display_badge_html()
		{
			if( ! in_array( $this->display_badge, array( 'message', 'contact_only_message' ) ) )
			{
				return '';
			}
			
			return $this->display_badge_html;
		}

				/**
		 * Returns the selected backend theme style
		 * 
		 * @since 4.5.7.2
		 * @return string				'light' | 'dark'
		 */
		public function get_theme()
		{
			if( ! in_array( $this->theme, array( 'light', 'dark' ) ) )
			{
				$this->theme = 'light';
			}
			
			return $this->theme;
		}

		/**
		 * 
		 * @since 4.5.7.2
		 * @return boolean
		 */
		public function is_loading_prohibited()
		{
			if( is_null( $this->loading_prohibited ) )
			{
				//	Check backend setting
				$prohibited = ! $this->is_activated() || ! $this->are_keys_set();
				
				/**
				 * Filter allows to supress loading of script on desired pages
				 * 
				 * @since 4.5.7.2
				 * @param boolean
				 * @return boolean
				 */
				$prohibited = apply_filters( 'avf_load_google_recaptcha_api_prohibited', $prohibited );
				
				$this->loading_prohibited = is_bool( $prohibited ) ? $prohibited : true;
			}
			
			return $this->loading_prohibited;
		}
		
		/**
		 * Checks if the version activates gRECAPTCHA
		 * 
		 * @since 4.5.7.2
		 * @param string $version
		 * @return boolean
		 */
		public function is_activated( $version = null )
		{
			if( is_null( $version ) )
			{
				$version = $this->get_version();
			}
			
			return in_array( $version, array( 'avia_recaptcha_v2', 'avia_recaptcha_v3' ) );
		}
		
		/**
		 * Checks if all necessary keys are set for a version.
		 * Returns false if an invalid version or if disabled
		 * 
		 * @since 4.5.7.2
		 * @param string $version
		 * @return boolean
		 */
		public function are_keys_set( $version = null )
		{
			if( is_null( $version ) )
			{
				$version = $this->get_version();
			}
			
			$keys2 = ! empty( $this->get_site_key( 'avia_recaptcha_v2' ) ) && ! empty( $this->get_secret_key( 'avia_recaptcha_v2' ) );
			$keys3 = ! empty( $this->get_site_key( 'avia_recaptcha_v3' ) ) && ! empty( $this->get_secret_key( 'avia_recaptcha_v3' ) );
			
			if( 'avia_recaptcha_v2' == $version )
			{
				return $keys2;
			}
			
			if( 'avia_recaptcha_v3' == $version )
			{
				return $keys2 && $keys3;
			}
			
			return false;
		}
		
		/**
		 * Allow admins to see more detailed error messages
		 * 
		 * @since 4.5.7.2
		 * @return boolean
		 */
		protected function show_extended_errors()
		{
			$show_extended_errors = current_user_can( 'manage_options' );
			
			/**
			 * 
			 * 
			 * @since 4.5.7.2
			 * @param boolean
			 * @return boolean				return true to show extended messages
			 */
			return apply_filters( 'avf_recaptcha_show_extended_error_messages', $show_extended_errors );
		}

		/**
		 * Verify a token with gRECAPTCHA.
		 * 
		 * @since 4.5.7.2
		 * @param string $token
		 * @param string|null $secretkey			if null -> verification of key
		 * @return array|\WP_Error					check $result['success'] = true
		 */
		protected function verify_token( $token, $secretkey = null )
		{
			if( is_null( $secretkey ) )
			{
				$secretkey = $this->get_secret_key();
			}
			
			$params = array(
						'body' => array(
										'secret'      => $secretkey,
										'response'    => $token,
										'remoteip'    => $_SERVER['REMOTE_ADDR'],
									)
						);
			
		    $response = wp_safe_remote_post( av_google_recaptcha::API_VERIFY_URL, $params );

			if( $response instanceof WP_Error )
			{
				$msg = $response->get_error_messages();
				$msg = implode( '<br />', $msg );
				
				return new WP_Error( 'site_down', sprintf( __( 'Unable to communicate with gRECAPTCHA: <br /><br />%s', 'avia_framework' ), $msg ) );
			}
				
			$code = wp_remote_retrieve_response_code( $response );
			if ( 200 != $code )
			{
				$msg = wp_remote_retrieve_response_message( $response );
				if( empty( $msg ) )
				{
					$msg = __( 'Unknown error code', 'avia_framework' );
				}
				return new WP_Error( 'invalid_response', sprintf( __( 'gRECAPTCHA returned error %d (= %s).', 'avia_framework' ), $code, $msg ) );
			}

			
			$body = wp_remote_retrieve_body( $response );
		    $result = json_decode( $body, true );
			
			if( true === $result['success'] )
			{
				return $result;
			}
			
			if( isset( $result['error-codes'] ) && is_array( $result['error-codes'] ) )
			{
				foreach( $result['error-codes'] as $key => $value ) 
				{
					switch( $value )
					{
						case 'missing-input-secret':
							$result['error-codes'][ $key ] = __( 'The secret parameter is missing.', 'avia_framework' );
							break;
						case 'invalid-input-secret':
							$result['error-codes'][ $key ] = __( 'The secret parameter is invalid or malformed.', 'avia_framework' );
							break;
						case 'missing-input-response':
							$result['error-codes'][ $key ] = __( 'The response parameter is missing.', 'avia_framework' );
							break;
						case 'invalid-input-response':
							$result['error-codes'][ $key ] = __( 'The response parameter is invalid or malformed.', 'avia_framework' );
							break;
						case 'bad-request':
							$result['error-codes'][ $key ] = __( 'The request is invalid or malformed.', 'avia_framework' );
							break;
						case 'timeout-or-duplicate':
							$result['error-codes'][ $key ] = __( 'The response is no longer valid: either is too old or has been used previously.', 'avia_framework' );
							break;
						default:
							$result['error-codes'][ $key ] =  sprintf( __( '%s - unknown error code', 'avia_framework' ), $value );
					}
				}
			}
			
			return $result;
		}
		
		/**
		 * Callback - verifies the token and creates a transient if valid.
		 * In case of a false score returns
		 * 
		 * @since 4.5.7.2
		 */
		public function handler_recaptcha_verify_frontend()
		{
			header( "Content-Type: application/json" );
			$response = array(
								'success'		=> false,
								'alert'			=> '',
								'score_failed'	=> false,
								'transient'		=> ''
							);

			$show_extended_errors = $this->show_extended_errors();
			
			/**
			 * Nonce check removed with 4.7.4.1
			 * 
			 * Makes problems with caching plugins.
			 * As reCaptcha is bound to a site verifying the reCaptcha should be enough to verify a valid callback.
			 * 
			 */
//			$nonce = isset( $_REQUEST['_wpnonce'] ) ? $_REQUEST['_wpnonce'] : '';
//			$result = wp_verify_nonce( $nonce, av_google_recaptcha::AJAX_VERIFY_NONCE );
//			
//			if( 1 !== $result )
//			{
//				$response['alert'] = __( 'Sorry, but the session time for this page has expired. Please reload the page.', 'avia_framework' );
//				if( true === $show_extended_errors )
//				{
//					$response['alert'] .= '<br />' . __( 'WP Nonce check failed.', 'avia_framework' );
//				}
//				
//				echo json_encode( $response );
//				exit;
//			}
			
			$version = isset( $_REQUEST['version'] ) ? $_REQUEST['version'] : '';
			$token = isset( $_REQUEST['token'] ) ? $_REQUEST['token'] : '';
			$score = isset( $_REQUEST['score'] ) ? $_REQUEST['score'] : -1;
			$action = isset( $_REQUEST['recaptcha_action'] ) ? $_REQUEST['recaptcha_action'] : '';
			$secret_key = $this->get_secret_key( $version );
			

			$check = $this->verify_token( $token, $secret_key );
			
			$validate_error = __( 'Sorry, but the verification failed. Please reload the page and try again.', 'avia_framework' );
			
			if( true !== $check['success'] )
			{
				$response['alert'] = $validate_error;
				if( true === $show_extended_errors )
				{
					$response['alert'] .= '<br />' . __( 'API check returned false.', 'avia_framework' );
				}
				
				echo json_encode( $response );
				exit;
			}
			
			if( 'avia_recaptcha_v3' == $version )
			{
				if( ! isset( $check['score'] ) || ! isset( $check['action'] ) || ( $check['action'] != $action ) )
				{
					$response['alert'] = $validate_error;
					if( true === $show_extended_errors )
					{
						$response['alert'] .= '<br />' . __( 'Invalid V3 response. Actions:', 'avia_framework' ) . $check['action'] . '/' . $action;
					}
				
					echo json_encode( $response );
					exit;
				}
				
				if( (float) $score >= (float)$check['score'] )
				{
					if( true === $show_extended_errors )
					{
						$response['score_failed'] = sprintf( __( 'Score requested %s - returned %s', 'avia_framework' ), $score, $check['score'] );
					}
					else 
					{
						$response['score_failed'] = true;
					}
					
					echo json_encode( $response );
					exit;
				}
			}
			
			$transient = uniqid( av_google_recaptcha::TRANSIENT_PREFIX, true );
			
			/**
			 * @since 4.5.7.2
			 * @param int
			 * @return int
			 */
			$expiration = 30 * MINUTE_IN_SECONDS;
			$expiration = apply_filters( 'avf_recaptcha_transient_expiration', $expiration );
			if( ! is_numeric( $expiration ) )
			{
				$expiration = 30 * MINUTE_IN_SECONDS;
			}
			
			set_transient( $transient, $version, $expiration );
			
			$response['success'] = true;
			$response['transient'] = $transient;
			
			echo json_encode( $response );
			exit;
		}
		
		
		/**
		 * Check if we have a valid token.
		 * This is also a fallback to prevent bots using same token several times.
		 * In case $proceed !== true we also remove the transient to clean up.
		 * 
		 * @since 4.5.7.2
		 * @param boolean $proceed
		 * @param array $new_post
		 * @param array $form_params
		 * @param avia_form $form_class
		 * @return boolean|null						true if you want to continue | null for error message
		 */
		public function handler_avf_form_send( $proceed, array $new_post, array $form_params, avia_form $form_class )
		{
			$use_recaptcha = false;
			
			foreach( $form_class->form_elements as $element ) 
			{
				if( isset( $element['type'] ) && ( 'grecaptcha' == $element['type'] ) )
				{
					$use_recaptcha = $element;
					break;
				}
			}
			
			if( false === $use_recaptcha )
			{
				return $proceed;
			}
			
			$show_extended_errors = $this->show_extended_errors();
			$token = isset( $element['token_input'] ) ? $element['token_input'] : '';
			$token_value = ( isset( $_REQUEST[ $token ] ) ) ? trim( $_REQUEST[ $token ] ) : '';
			$requested_version = ( isset( $_REQUEST[ $token . '-version' ] ) ) ? trim( $_REQUEST[ $token . '-version' ] ) : $element['version'];
			
			if( true !== $proceed )
			{
				if( ! empty( $token_value ) )
				{
					delete_transient( $token_value );
				}
				
				return $proceed;
			}
			
			$reload_msg = '<br />' . __( 'Form could not be submitted. Please reload page and try again.', 'avia_framework' );
			
			if( empty( $token_value ) )
			{
				$form_class->submit_error .= __( 'Invalid form for reCAPTCHA sent.', 'avia_framework' ) . $reload_msg;
				if( $show_extended_errors )
				{
					$form_class->submit_error .= '<br />' . __( 'Name for token field was missing.', 'avia_framework' );
				}
				return null;
			}
			
			$version = get_transient( $token_value );
			
			if( false === $version )
			{
				$form_class->submit_error .= __( 'Token to validate form already expired.', 'avia_framework' ) . $reload_msg;
				if( $show_extended_errors )
				{
					$form_class->submit_error .= '<br />' . __( 'Transient to verify form was missing or expired.', 'avia_framework' );
				}
				return null;
			}
			
			delete_transient( $token_value );
			
			if( $version != $requested_version )
			{
				$form_class->submit_error .= __( 'Token to validate form is not valid.', 'avia_framework' ) . $reload_msg;
				if( $show_extended_errors )
				{
					$form_class->submit_error .= '<br />' . __( 'reCAPTCHA version in transient differs from selected version in element.', 'avia_framework' );
				}
				return null;
			}
			
			return $proceed;
		}




		/**
		 * Output options page backend HTML or perform the key verification and return HTML message
		 * 
		 * @since 4.5.7.2
		 * @param string $api_key
		 * @param boolean $ajax
		 * @param array|boolean|null $check_keys
		 * @param array $element				used in backend for output of key verification
		 * @return string|array
		 */
		public function backend_html( $api_key = '', $ajax = true, $check_keys = false, $element = array() )
		{
			$return = array(
							'html'                 => '',
							'update_input_fields'  => array()
						);
			
			$api_key = trim( $api_key );	
			$valid_key = false;
			
			$response_text  = __( 'Could not connect and verify these API Keys with Google reCAPTCHA.', 'avia_framework' );
			$response_class = "av-notice-error";
			
			$content_default  =			'<h4>' . esc_html__( 'Troubleshooting:', 'avia_framework' ) . '</h4>';
			$content_default .=			'<ol>';
			$content_default .=				'<li>';
			$content_default .=					esc_html__( 'Check if you typed the keys correctly.', 'avia_framework' );
			$content_default .=				'</li>';
			$content_default .=				'<li>';
			$content_default .=					esc_html__( 'If you use the restriction setting on Google try to remove that, wait a few minutes for google to apply your changes and then check again if the keys work here. If it does, you probably have a syntax error in your referrer url', 'avia_framework' );
			$content_default .=				'</li>';
			$content_default .=				'<li>';
			$content_default .=					esc_html__( 'If none of this helps: deactivate all plugins and then check if the API works by using the button above. If thats the case then one of your plugins is interfering.', 'avia_framework' );
			$content_default .=				'</li>';
			$content_default .=			'</ol>';
			
			
			if( $ajax )
			{	
				/**
				 * called by user pressing the ajax check button
				 */
				$token = isset( $check_keys['token'] ) ? trim( $check_keys['token'] ) : '';
				$secretkey = isset( $check_keys['secretkey'] ) ? trim( $check_keys['secretkey'] ) : '';
				$version = isset( $check_keys['version'] ) ? trim( $check_keys['version'] ) : '';
				switch( $version )
				{
					case 'avia_recaptcha_v2':
						$verify_field = 'recaptcha_verified_keys_v2';
						break;
					case 'avia_recaptcha_v3':
						$verify_field = 'recaptcha_verified_keys_v3';
						break;
					default:
						$verify_field = '';
						break;
				}
				
				$check = $this->verify_token( $token, $secretkey );
				
				if( true === $check['success'] )
				{
					$valid_key = true;
					$response_class = '';
					$response_text  = __( 'We were able to properly connect and verify your API keys with Google reCAPTCHA', 'avia_framework' );
					
					//will be stripped from the final output but tells the ajax script to save the page after the check was performed
					$response_text .= ' avia_trigger_save'; 	
					
					$keys = array(
									$check_keys['version'],
									$check_keys['sitekey'],
									$check_keys['secretkey']
								);
					if( ! empty( $verify_field ) )
					{
						$return['update_input_fields'][ $verify_field ] = implode( ' ', $keys );
					}
				}
				else
				{
					$content_default = '';
					if( $check instanceof WP_Error )
					{
						$response_text = $check->get_error_message();
					}
					else
					{
						$msg = '';
						
						if( is_array( $check['error-codes'] ) && count( $check['error-codes'] ) > 0 )
						{
							$msg = implode( '<br />', $check['error-codes'] );
						}
						
						$response_text  = __( 'Error on connecting to Google reCAPTCHA - please retry.', 'avia_framework' );
						
						if( ! empty( $msg) )
						{
							$response_text .= '<br /><br />' . $msg;
						}
					}
					
					if( ! empty( $verify_field ) )
					{
						$return['update_input_fields'][ $verify_field ] = 'verify_error';
					}
				}
			}
			else
			{
				/**
				 * called on a normal page load. in this case we either show the stored result or if we got no stored result we show nothing
				 */
				if( $this->is_activated() )
				{
					switch( $element['id'] )
					{
						case 'avia_recaptcha_key_verify_v2':
							$version = 'avia_recaptcha_v2';
							break;
						case 'avia_recaptcha_key_verify_v3':
							$version = 'avia_recaptcha_v3';
							break;
						default:
							$version = '';
							break;
					}
					
					$keys = array(
									$version,
									$this->get_site_key( $version ),
									$this->get_secret_key( $version )
								);
				
					$check = implode( ' ', $keys );
					
					if( $this->get_verified_keys( $version ) == $check )
					{
						$valid_key = true;
					}
					else if( '' == $this->get_site_key( $version ) && '' == $this->get_secret_key( $version ) )
					{
						$response_class = '';
						$response_text = '';
					}
					else if( 'verify_error' == $this->get_verified_keys() )
					{
						$response_text  = __( 'A connection error occurred last time we tried verify your keys with Google reCAPTCHA - please revalidate the keys.', 'avia_framework' );
					}
					else if( '' == $this->get_verified_keys( $version ) )
					{
						$response_text  = __( 'Please verify the keys', 'avia_framework' );
					}
					else
					{
						$response_text  = __( 'Please verify the keys - the last verified keys are different.', 'avia_framework' );
					}
					
					$content_default = '';
				}
				
				if( $valid_key )
				{
					$response_class = '';
					$response_text  = __( 'Last time we checked we were able to connected to Google reCAPTCHA with your API keys', 'avia_framework' );
				}
			}
			
			if( $valid_key )
			{
				$content_default  = __( 'If you ever change your API key or the URL restrictions of the key please verify the key here again, to test if it works properly','avia_framework');
			}
			
			$output  = '';
			
			if( ! empty( $response_text ) )
			{
				$output  =	"<div class='av-verification-response-wrapper'>";
				$output .=		"<div class='av-text-notice {$response_class}'>";
				$output .=			$response_text;
				$output .=		"</div>";
				$output .=		"<div class='av-verification-cell'>{$content_default}</div>";
				$output .=	"</div>";
			}
			
			if( $ajax )
			{
				$return['html'] = $output;
			}
			else
			{
				$return = $output;
			}
			
			return $return;
		}
		
	}
	
	/**
	 * Returns the main instance of av_google_recaptcha to prevent the need to use globals.
	 * 
	 * @since 4.5.7.2
	 * @return av_google_recaptcha
	 */
	function Avia_Google_reCAPTCHA() 
	{
		return av_google_recaptcha::instance();
	}
}

Avia_Google_reCAPTCHA();


if( ! function_exists( 'av_recaptcha_api_check' ) )
{
	/**
	 * Callback function:
	 *		- ajax callback from verification button
	 *		- php callback when creating output on option page
	 * 
	 * @since 4.5.7.2
	 * @param string $value
	 * @param boolean $ajax
	 * @param array|null $js_value
	 * @param array $element
	 * @return string
	 */
	function av_recaptcha_api_check( $value, $ajax = true, $js_value = null, $element = array() )
	{
		$api = Avia_Google_reCAPTCHA();
		return $api->backend_html( $value, $ajax, $js_value, $element );
	}

}

