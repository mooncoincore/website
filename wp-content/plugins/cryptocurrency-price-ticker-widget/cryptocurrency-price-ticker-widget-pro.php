<?php
/**
 * Plugin Name:Cryptocurrency Widgets PRO
 * Description:Cryptocurrency widgets pro WordPress plugin displays current price & chart widgets of crypto coins - bitcoin, ethereum, ripple etc.
 * Author:Cool Plugins 
 * Author URI:https://www.coolplugins.net
 * Version: 2.8
 * License: GPL2
 * Text Domain:ccpw
 * Domain Path: languages
 * @package Cryptocurrency Price Ticker Widget PRO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CCPWP_VERSION', '2.8' );
define( 'CCPW_DB_MIGRATED_VERSION', '0.1' );
define( 'CCPWP_PRO_FILE', __FILE__ );
define( 'CCPWP_PATH', plugin_dir_path( CCPWP_PRO_FILE ) );
define( 'CCPWP_URL', plugin_dir_url( CCPWP_PRO_FILE ) );
define( 'CCPWP_API' , "https://api-beta.coinexchangeprice.com/v1/" );
/**
 * Class Crypto_Currency_Price_Widget
 */
final class Crypto_Currency_Price_Widget_Pro {

	/**
	 * Plugin instance.
	 *
	 * @var Crypto_Currency_Price_Widget
	 * @access private
	 */
	private static $instance = null;
	public $shortcode_obj=null;

	/**
	 * Get plugin instance.
	 *
	 * @return Crypto_Currency_Price_Widget
	 * @static
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct() {

		register_activation_hook( CCPWP_PRO_FILE , array( $this,'ccpwp_activate'));
		register_deactivation_hook( CCPWP_PRO_FILE, array( $this, 'ccpwp_deactivate' ) );
		// includes all required files
		$this->ccpwp_includes();
		$this->ccpwp_installation_date();
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_filter( 'script_loader_tag', array( $this, 'ccpw_defer_scripts' ), 10, 3 );
		add_action('init', array($this,'ccpwp_plugin_version_verify' ));
		// ajax call for datatable server processing
		add_action('wp_ajax_ccpwp_get_coins_list', 'ccpwp_get_coins_list');
		add_action('wp_ajax_nopriv_ccpwp_get_coins_list', 'ccpwp_get_coins_list');
		// ajax call for chart
		add_action('wp_ajax_ccpw_small_charts','ccpw_small_chart_data');
		add_action('wp_ajax_nopriv_ccpw_small_charts','ccpw_small_chart_data');

		// add only admin hook for ajax request
		add_action( 'wp_ajax_ccpwp_remove_major_update_notice', array($this, 'ccpwp_remove_major_update_notice' ));

		add_action( 'rest_api_init', function () {
			register_rest_route( 'ccpw/v1', 'generate-chart', array(
			  'methods' => 'GET',
			  'callback' => 'ccpw_generate_chart' ,
			  'permission_callback' => '__return_true'
			  ));
		  	});

		add_action( 'wp_footer', array($this,'ccpwp_ticker_in_footer') );
	
		add_action( 'wp_footer', array($this,'ccpwp_news_ticker_in_footer'));
		add_action( 'wp_footer', array($this,'ccpwp_enable_ticker') );
		//jetpack images
		add_filter( 'jetpack_photon_skip_for_url',array( $this,'cmc_photon_only_allow_local'), 9, 4 );
		// check coin market cap plugin is activated.
		add_action('admin_init', array($this, 'ccpwp_check_cmc_activated'));
			  
		if(is_admin()){
		// Only one plugin must be active at a time
		add_action( 'admin_init',array($this,'ccpwp_is_free_version_active') );
		add_action( 'admin_menu', array($this, 'ccpwp_add_submenu'), 20 );
		add_action('admin_enqueue_scripts', array($this,'ccpw_settings_custom_assets'));
		//add_action( 'admin_notices', array($this,'ccpwp_admin_notice_for_major_update'));	
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this,'ccpwp_add_widgets_action_links'));
			//automatic updates
		}
		add_filter( 'cmb2_condition_based_enqueue', function($custom_posts){
				array_push( $custom_posts ,'ccpw');
				return $custom_posts;
		},10 );

		add_filter('cron_schedules', array($this, 'ccpwp_cron_schedules'));
		add_action('ccpwp_coins_autosave', array($this, 'ccpwp_cron_coins_autoupdater'));
	}

	function ccpwp_add_submenu(){
		add_submenu_page( 'cool-crypto-plugins' , 'Crypto Widgets Pro', '<strong>Crypto Widgets Pro</strong>', 'manage_options', 'edit.php?post_type=ccpw', false, 20 );
		add_submenu_page( 'cool-crypto-plugins' , 'Crypto Widgets Pro', ' ↳ All Widgets', 'manage_options', 'edit.php?post_type=ccpw', false, 21 );	// just an alias to above menu
		add_submenu_page( 'cool-crypto-plugins' , 'Add New Widget', ' ↳ Add New Widget', 'manage_options', 'post-new.php?post_type=ccpw', false, 22 );
	}
/*
|--------------------------------------------------------------------------
| Cron status schedule(s)
|-------------------------------------------------------------------------- 
*/
	function ccpwp_cron_schedules($schedules)
	{
		// 5 minute schedule for grabing all coins 
		if (!isset($schedules["5min"])) {
			$schedules["5min"] = array(
				'interval' => 5 * 60,
				'display' => __('Once every 5 minutes')
			);
		}
		return $schedules;
	}

	function ccpw_defer_scripts( $tag, $handle, $src ) {
	
		// The handles of the enqueued scripts we want to defer
		$defer_scripts = array(
			'ccpw-chart-js'
		);

		// The handles of the enqueued scripts we want to async
		$async_scripts = array( 
		);

		$module_scripts= array(
			'ccpw-binance-live-widget',
			'ccpw-binance-socket',
			'ccpw_stream'
		);
	
		if ( in_array( $handle, $async_scripts ) ) {
			return '<script src="' . $src . '" async="async" type="text/javascript"></script>' . "\n";
		}
		
		if ( in_array( $handle, $defer_scripts ) ) {
			return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
		}

		if( in_array($handle, $module_scripts) ){
			return '<script src="' . $src . '" type="module" type="text/javascript"></script>' . "\n";
		}
		
		return $tag;
	}

/*
|--------------------------------------------------------------------------
| initialize cron : MUST USE ON PLUGIN ACTIVATION
|--------------------------------------------------------------------------
*/
	public function ccpwp_cron_job_init(){
		if (!wp_next_scheduled('ccpwp_coins_autosave')) {
			wp_schedule_event(time(), '5min', 'ccpwp_coins_autosave');
		}
	}

/*
|--------------------------------------------------------------------------
| Must be run through cron
|--------------------------------------------------------------------------
*/
	public function ccpwp_cron_coins_autoupdater(){
		if ( !class_exists('CoinMarketCap')) {
			//update coins data;
			ccpwp_get_api_data();
		}
	}

	/**
	 * Load plugin function files here.
	 */
	public function ccpwp_includes() {

		if( is_admin() ){
			require_once CCPWP_PATH . 'admin/registration-settings.php';
			require_once CCPWP_PATH . 'admin/addon-dashboard-page/addon-dashboard-page.php';
			cool_plugins_crypto_addon_settings_page('crypto','cool-crypto-plugins','Cryptocurrency plugins by CoolPlugins.net', 'Crypto Plugins','dashicons-chart-area');
		}

		/**
		 * Get the bootstrap!
		 * (Update path to use cmb2 or CMB2, depending on the name of the folder.
		 * Case-sensitive is important on some systems.)
		 */
			include_once CCPWP_PATH . 'admin/cmb2/init.php';

			if($this->ccpwp_get_post_type_page()=="ccpw"){
				include_once CCPWP_PATH . 'admin/cmb2/cmb2-conditionals.php';
				include_once CCPWP_PATH . 'admin/cmb2/cmb-field-select2/cmb-field-select2.php';
			
			}
		
			if( is_admin() ){
				include_once CCPWP_PATH . 'admin/init-api.php';
				require_once CCPWP_PATH . 'admin/class.review-notice.php';
			}
			
			 require CCPWP_PATH . '/includes/ccpw-functions.php';
			 require CCPWP_PATH . '/includes/ccpw-db-helper.php';
			 include_once CCPWP_PATH . '/includes/ccpw-shortcode.php';
			
			 include_once CCPWP_PATH . 'admin/ccpw-post-type.php';
			 
			 include_once CCPWP_PATH . '/includes/ccpw-widget.php';
			 $this->shortcode_obj=new CCPW_Shortcode();
			 new CCPW_Posttype();
	}

/*
|--------------------------------------------------------------------------
|  check admin side post type page
|--------------------------------------------------------------------------
*/
	function ccpwp_get_post_type_page() {
		global $post, $typenow, $current_screen;
	
		if ( $post && $post->post_type ){
				return $post->post_type;
		}elseif( $typenow ){
				return $typenow;
		}elseif( $current_screen && $current_screen->post_type ){
				return $current_screen->post_type;
		}
		elseif( isset( $_REQUEST['post_type'] ) ){
				return sanitize_key( $_REQUEST['post_type'] );
		}
		elseif ( isset( $_REQUEST['post'] ) ) {
		return get_post_type( $_REQUEST['post'] );
		}
		return null;
	}
	

/*
|--------------------------------------------------------------------------
| Code you want to run when all other plugins loaded.
|--------------------------------------------------------------------------
*/
		public function load_textdomain() {
			load_plugin_textdomain( 'ccpw', false, basename(dirname(__FILE__)) . '/languages/' );
		}

	/**
	 * Show admin notice for major plugin update.
	 */
	function ccpwp_admin_notice_for_major_update(){
		$plugin_info = get_plugin_data( __FILE__ , true, true );
		$isUpdated = get_option( 'recent_ccpwp_updated_v'.CCPWP_VERSION );
		$isEnable =  get_option('ccpwp_remove_update_notice_v'.CCPWP_VERSION) ;
		if( $isUpdated!=false && $isEnable == false ){
			printf(__('<div class="ccpwp-major-update notice notice-warning is-dismissible important"><h3><strong>%s</strong>: This is a major plugin update, You must follow these update guidelines - <a href="https://bit.ly/cryptocurrency-update" target="_blank">click here</a></h3></div>'),$plugin_info['Name']);
		}

	}

/*
|--------------------------------------------------------------------------
| Run when deactivate plugin.
|--------------------------------------------------------------------------
*/
	public function ccpwp_deactivate() {
		$this->deactivate_license();
		// remove cron job
		if( wp_next_scheduled('ccpwp_coins_autosave') ){
			wp_clear_scheduled_hook('ccpwp_coins_autosave');
		}
		GLOBAL $wpdb;	
		// remove database if CMC is not installed
		$option_table = $wpdb->base_prefix . 'options';
		if ( get_option('cmc-dynamic-links') === false || get_option('cmc-dynamic-links') == '' ){		
			$db = new ccpwp_database();
			$db->drop_table();
			// delete transient related to coin data
			$wpdb->query( "DELETE FROM $option_table WHERE option_name LIKE '%transient_cmc-saved-coindata%' "  );
		}
		$wpdb->query( "DELETE FROM $option_table WHERE option_name LIKE '%transient_ccpw-chart-cache%' "  );
	}
	// load custom assets on widget settings panel
    public function ccpw_settings_custom_assets(){

		$isUpdated = get_option( 'recent_ccpwp_updated_v'.CCPWP_VERSION );
		$isEnable =  get_option('ccpwp_remove_update_notice_v'.CCPWP_VERSION) ;
		// enqueue script only if required
		if( $isUpdated!=false && $isEnable == false ){
			wp_enqueue_script( 'ccpwp-admin-notice', CCPWP_URL . 'assets/admin/js/ccpwp-admin-notice.js', array('jquery'), CCPWP_VERSION, true);
			wp_localize_script( 'ccpwp-admin-notice', 'ccpwp_data', array( 'ajax_url'=> admin_url( 'admin-ajax.php' )) );
		}

		if($this->ccpwp_get_post_type_page()=="ccpw"){
			wp_enqueue_script('ccpw-settings-custom-scripts',CCPWP_URL.'assets/admin/js/settings-custom-scripts.js',
			 array('jquery','cmb2-conditionals'),CCPWP_PRO_FILE, true);
			wp_enqueue_style( 'ccpw-custom-styles', CCPWP_URL.'assets/admin/css/ccpwp-admin-styles.min.css', null, CCPWP_VERSION);
		}
	}

/*
|--------------------------------------------------------------------------
| Run when activate plugin.
|--------------------------------------------------------------------------
*/
	public function ccpwp_activate() {
		$DB = new ccpwp_database();
		$DB->create_table();
		ccpwp_get_api_data();
		$this->ccpwp_cron_job_init();
        update_option("ccpw-v",CCPWP_VERSION);
        update_option("ccpw-type","PRO");
	}

/*
|--------------------------------------------------------------------------
| Shows ticker in Footer
|--------------------------------------------------------------------------
*/
	function ccpwp_ticker_in_footer(){

		if (!wp_script_is('jquery', 'done')) {
			wp_enqueue_script('jquery');
		}
		 $id=get_option('ccpw-p-id');
		 $ids_arr=array();
	
		if($id){
		$type = get_post_meta($id,'type', true );
		//------------------------------------------------------------------
		$page_select = get_post_meta($id,'disable_from_pages', true ) ;
		$ids_arr= explode(',',$page_select);
		global $wp_query;
	
		 if($type=="ticker"){
				$ticker_position = get_post_meta($id,'ticker_position', true );
				if($ticker_position=="header"||$ticker_position=="footer"){
					if ( is_object($wp_query->post) && !in_array($wp_query->post->ID,$ids_arr))
					{
						echo do_shortcode("[ccpw id=".$id."]");
					}
			 }
		  }
		}	
	
	}

/*
|--------------------------------------------------------------------------
| Shows News ticker in Footer
|--------------------------------------------------------------------------
*/
function ccpwp_news_ticker_in_footer(){

		 $id=get_option('ccpw-news-p-id');
		 $ids_arr=array();
		 if(!is_admin()){
		if($id){
		$type = get_post_meta($id,'type', true );
		//------------------------------------------------------------------
		$page_select = get_post_meta($id,'disable_from_pages', true ) ;
		$ids_arr= explode(',',$page_select);
		global $wp_query;
		if($type=="rss-feed"){
			$rss_layout = get_post_meta($id,'rss_style', true );
			$rss_ticker_position = get_post_meta($id,'rss_ticker_position', true );
				if($rss_layout=="ticker-rss" && $rss_ticker_position=="rss-header"||$rss_ticker_position=="rss-footer"){
					if (!in_array($wp_query->post->ID,$ids_arr))
					{
						echo do_shortcode("[ccpw id=".$id."]");
					}
			 }
		}
	 }
	}
}

/*
|--------------------------------------------------------------------------
| Show div after DOM ready
|--------------------------------------------------------------------------
*/
	function ccpwp_enable_ticker(){
		wp_add_inline_script('ccpw_bxslider_js',
		'jQuery(document).ready(function($){
			$(".ccpw-ticker-cont").fadeIn();     
		});'
	
		,'before');
	}
	/*
	For ask for reviews code
	*/
	function ccpwp_installation_date(){
		 $get_installation_time = strtotime("now");
   	 	  add_option('ccpw_activation_time', $get_installation_time ); 
	}	

	//check coin market cap plugin is activated. then enable links
	function ccpwp_check_cmc_activated(){
		if (is_plugin_active('coin-market-cap/coin-market-cap.php') || class_exists('CoinMarketCap')) {
			update_option('cmc-dynamic-links', true);
		} else {
			ccpwp_get_allCoins();
			update_option('cmc-dynamic-links', false);
		}
	}

/*
|--------------------------------------------------------------------------
|  make sure it always run to avoid conflict between free and pro version
|--------------------------------------------------------------------------
|  it must be fired on admin-area after plugins loaded
|--------------------------------------------------------------------------
*/
	public function ccpwp_is_free_version_active(){
		if(is_plugin_active( 'cryptocurrency-price-ticker-widget/cryptocurrency-price-ticker-widget.php' ) )  {
			deactivate_plugins( 'cryptocurrency-price-ticker-widget/cryptocurrency-price-ticker-widget.php' );
			delete_transient('cmc-saved-coindata');
			add_action( 'admin_notices', function(){ ?>
			<style>div#message.updated {
				display: none;
			}</style>
				<div class="notice notice-error is-dismissible">
					<p><?php
					_e('Cryptocurrency Price Ticker Widget PRO: Cryptocurrency Widgets is <strong>deactivated</strong> as you have already activated the pro version.','ccpwx');
					?>
					</p>
				</div>

			<?php } );
		}
	}

/*
|--------------------------------------------------------------------------
|  Check if plugin is just updated from older version to new
|--------------------------------------------------------------------------
*/
	public function ccpwp_plugin_version_verify(){
		$CCPWP_VERSION = get_option('ccpw-v');
		$DB_V = get_option('ccpw-database-migrated-version');
		
		// check database version before running migration
		if( !isset($DB_V) || version_compare( $DB_V, CCPW_DB_MIGRATED_VERSION, '<' ) ){
			$this->ccpw_db_migration();
		}

		if( isset($CCPWP_VERSION) && version_compare( $CCPWP_VERSION, CCPWP_VERSION, '<' ) ){
			$this->ccpwp_deactivate();
			$this->ccpwp_activate();
			update_option('recent_ccpwp_updated_v'.CCPWP_VERSION, date('Y-M-d H:I'));
  		}
	}

/*
|--------------------------------------------------------------------------
|	Migrate widgets from old plugin version to new
|-------------------------------------------------------------------------- 
*/
	function ccpw_db_migration(){

		$posts = get_posts( array('post_type'=>'ccpw', 'posts_per_page'=>-1) );
		$currency;
		$db = new ccpwp_database;
        $db->create_table();
		ccpwp_get_api_data();

		if( !is_array($posts) ){
			return;
		}
		$coins = $db->get_coin_id_by_symbol();
		
		if( is_array($coins) && count($coins)>0 ){
			foreach($posts as $post){
				$response = array();
				$currency = get_post_meta($post->ID,'display_currencies', true);
				
				// ccpwp_update_ua_coins_on_save_post( $post->ID );
				
				if( isset($currency) && is_array($currency) && count($currency)>0 ){
					foreach( $currency as $curr){
						if( !array_key_exists( $curr, $coins ) ){
							break;
						}
						$response[] =  $coins[$curr] ;
					}

					if( isset($response) && is_array($response) && count($response) > 0 ){
						update_post_meta($post->ID,'display_currencies',$response);
					}
				}

			}
			update_option( 'ccpw-database-migrated-version', CCPW_DB_MIGRATED_VERSION );
		}
		
	}

	/**
	 * Only use Photon for images belonging to our site.
	 * @param bool         $skip      Should Photon ignore that image.
	 * @param string       $image_url Image URL.
	 * @param array|string $args      Array of Photon arguments.
	 * @param string|null  $scheme    Image scheme. Default to null.
	 */
	function cmc_photon_only_allow_local( $skip, $image_url, $args, $scheme ) {
	    // Get the site URL, without any protocol.
	    $site_url = preg_replace( '~^(?:f|ht)tps?://~i', '', get_site_url() );
	 
	    /**
	     * If the image URL is from our site,
	     * return default value (false, unless another function overwrites).
	     * Otherwise, do not use Photon with it.
	     */
	    if ( strpos( $image_url, $site_url ) ) {
	        return $skip;
	    } else {
	        return true;
	    }
	}

	/**
	 * Remove major update notice displayed on plugin update
	 */
	function ccpwp_remove_major_update_notice(){
		update_option('ccpwp_remove_update_notice_v' . CCPWP_VERSION, date('Y-M-d H:I') );
		return json_encode( array('response'=>'200','message'=>'Update notice removed') );
		die();
	}

	// custom links for add widgets in all plugins section
	function ccpwp_add_widgets_action_links($links){
		$links[] = '<a style="font-weight:bold" href="'. esc_url( get_admin_url(null, 'post-new.php?post_type=ccpw') ) .'">Add Widgets</a>';
		$links[] = '<a  style="font-weight:bold" href="https://cryptowidgetpro.coolplugins.net/" target="_blank">View Demos</a>';
		return $links;
	
	}

	/**
	 * This function uninstall the license.
	 */
	function deactivate_license(){
		$options = get_option('ccpw_license_registration');
		if( !empty( $options ) && is_array( $options ) && isset( $options['ccpw-purchase-code'] ) ){
			require_once CCPWP_PATH . 'admin/CryptocurrencyWidgetsProBase.php';
			$message = "";
			$response = CryptocurrencyWidgetsProREG\CryptocurrencyWidgetsProBase::RemoveLicenseKey( CCPWP_PRO_FILE, $message );
		}
	}
	
}
function Crypto_Currency_Price_Widget_PRO() {
	return Crypto_Currency_Price_Widget_PRO::get_instance();
}

$ccpwp=Crypto_Currency_Price_Widget_Pro();
