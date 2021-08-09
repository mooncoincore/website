<?php  
if ( ! defined( 'AVIA_FW' ) )	{	exit( 'No direct script access allowed' );	}

/**
 * This file holds the avia_superobject class which is the core of the framework
 *
 * @author		Christian "Kriesi" Budschedl
 * @copyright	Copyright (c) Christian Budschedl
 * @link		http://kriesi.at
 * @link		http://aviathemes.com
 * @since		Version 1.0
 * @package 	AviaFramework
 */


/**
 * AVIA Superobject
 *
 * This class loads the default data of the files in the theme_option_pages folder and builds the option pages accordingly.
 * The class is responsible for loading the options data and adding it to the $avia superobject.
 *
 * The class only gets loaded if it wasnt already defined by a Wordpress Plugin based on the Avia Plugin Framework which uses a similar function set
 *  
 * @package AviaFramework
 * 
 */

if( ! class_exists( 'avia_superobject' ) )
{
	class avia_superobject
	{
	
		/**
		 * Holds the instance of this class
		 * 
		 * @since 4.3
		 * @var avia_superobject
		 */
		static protected $_instance = null;
		
		/**
		 * This is a fallback for WP CLI - wp cache flush
		 * due to some reason global object $avia is removed.
		 * With this we are able to restore it later when needed
		 * 
		 * @since 4.6.4
		 * @var array|null
		 */
		static public $base_data_init = null;
		
		/**
		 * This object holds basic information like theme or plugin name, version, description etc
		 * 
		 * @var array
		 */
		var $base_data;
		
		
		/**
		 * This object holds the information which parent admin page holds which slugs
		 * 
		 * @var array
		 */
		var $subpages;
	
	
		/**
		 * After calling the constructor this variable holds the framework data stored in the database & config files to render the frontend
		 * 
		 * @var array|null
		 */
		var $options;
		
		/**
		 * prefix for database savings, makes sure that multiple plugins and themes can be installed without overwriting each others options
		 * 
		 * @var string
		 */
		var $option_prefix;
		
		/**
		 * option pages retrieved from the config files in theme_option_pages, used to create the avia admin options panel.
		 * 
		 * @var array
		 */
		var $option_pages;
		
		/**
		 * option page data retrieved from the config files in theme_option_pages, used to create the items at the avia admin options panel.
		 * 
		 * @var array
		 */
		var $option_page_data;
		
		/**
		 * This object holds the avia style informations for php generated styles in the backend
		 * 
		 * @var avia_style_generator
		 */
		var $style;

		/**
		 *
		 * @since 4.3
		 * @var AviaTypeFonts 
		 */
		protected $type_fonts;
		
		/**
		 * Option keys that are global option values in case of multiple languages (e.g. WPML).
		 * In that case the value of the default language is used for all other languages.
		 * Option keys must be unique for all option pages (and slugs)
		 * 
		 * @since 4.8
		 * @var array
		 */
		protected $global_keys;
		
		/**
		 * Array to retrieve page for a given slug
		 *			slug	=>	parent
		 * 
		 * @since 4.8
		 * @var array
		 */
		protected $page_ref;
		
		
		/**
		 * Return the instance of this class
		 * 
		 * @since 4.3
		 * @param array|null $base_data
		 * @return avia_superobject
		 */
		static public function instance( $base_data = null )
		{
			if( is_null( avia_superobject::$_instance ) )
			{
				if( ! is_null( $base_data ) )
				{
					avia_superobject::$base_data_init = $base_data;
				}
				
				avia_superobject::$_instance = new avia_superobject( avia_superobject::$base_data_init );
				avia_superobject::$_instance->init();
			}
 
			return avia_superobject::$_instance;
		}
		
	    /**
         * The constructor sets up  $base_data and $option_prefix. It then gets database values and if we are viewing the backend it calls the option page creator as well
		 * 
		 * @param array|null $base_data
         */
		protected function __construct( $base_data )
		{	
			$this->base_data = $base_data;
			$this->option_prefix = 'avia_options_' . avia_backend_safe_string( $this->base_data['prefix'] );
			$this->subpages = array();
			$this->option_pages = array();
			$this->option_page_data = array();
			$this->options = null;
			$this->style = null;
			$this->type_fonts = null;
			$this->global_keys = array();
			$this->page_ref = array();
		}

		/**
		 * @since 4.3
		 */
		public function __destruct() 
		{
			unset( $this->base_data );
			unset( $this->subpages );
			unset( $this->options );
			unset( $this->option_pages );
			unset( $this->option_page_data );
			unset( $this->style );
			unset( $this->type_fonts );
			unset( $this->global_keys );
			unset( $this->page_ref );
		}
		
		
		/**
		 * Must be called immediately after creating the class.
		 * Removed from constructor due to endless loop
		 * 
		 * @since 4.3
		 */
		public function init()
		{
			//set option array
			$this->_create_option_arrays();
			
			if( current_theme_supports( 'avia_mega_menu' ) ) 
			{ 
				new avia_megamenu( $this ); 
			}
			
			$this->style = new avia_style_generator( $this );
			
			add_action( 'wp_footer', array( $this, 'set_javascript_framework_url' ) );
			
			if( is_admin() ) 
			{
				add_action( 'admin_print_scripts', array( $this, 'set_javascript_framework_url' ) );
				new avia_adminpages( $this );
				new avia_meta_box( $this );
				new avia_wp_export( $this );
			}
			
			if( get_theme_support( 'avia_sidebar_manager' ) ) 
			{
				new avia_sidebar();
			}
		}

		

		/**
		 * Get the instance of AviaTypeFonts object
		 * 
		 * @return AviaTypeFonts
		 */
		public function type_fonts() 
		{
			if( is_null( $this->type_fonts ) )
			{
				$this->type_fonts = new AviaTypeFonts();
			}
			
			return $this->type_fonts;
		}
				
		/**
         *  Create the config options to render the admin pages, merge the config files with the database.
         *  @todo: perform a deep merge of nested arrays
         */
		protected function _create_option_arrays()
		{
			//in case we got an option file as well include it and set the options for the theme
			include( AVIA_BASE . '/includes/admin/register-admin-options.php' );
			
			if( isset( $avia_pages ) ) 
			{
				/**
				 * @used_by		avia_auto_updates				1
				 * @param array
				 * @return array
				 */
				$this->option_pages = apply_filters( 'avf_option_page_init', $avia_pages );
			}
			
			if( isset( $avia_elements ) ) 
			{
				/**
				 * @used_by		avia_auto_updates				10
				 * @param array
				 * @return array		
				 */
				$this->option_page_data = apply_filters( 'avf_option_page_data_init', $avia_elements );
			}
			
			//retrieve option pages that were built dynamically as well as those elements
			//
			//	deprecated 4.8.2
			//	
//			$dynamic_pages 	  = get_option( $this->option_prefix . '_dynamic_pages' );
//			$dynamic_elements = get_option( $this->option_prefix . '_dynamic_elements' );
//			
//			//merge them together
//			if( is_array( $dynamic_pages ) )	 
//			{
//				$this->option_pages = array_merge( $this->option_pages, $dynamic_pages );
//			}
//			
//			if( is_array( $dynamic_elements ) )  
//			{
//				$this->option_page_data = array_merge( $this->option_page_data, $dynamic_elements );
//			}
			
			$this->get_global_option_keys();

			//saved option values		
			$database_option = get_option( $this->option_prefix );
			
			//create an array that tells us which parent pages hold which subpages
			foreach( $this->option_pages as $page )
			{
				$this->subpages[ $page['parent'] ][] = $page['slug'];
			}
			
			//	iterate over all non dynamic option pages for default values
			foreach( $avia_pages as $page )
			{
				if( ! isset( $database_option[ $page['parent'] ] ) || $database_option[ $page['parent'] ] == '' ) 
				{	
					$database_option[ $page['parent'] ] = $this->extract_default_values( $this->option_page_data, $page, $this->subpages );
				}
			}
			
			/*
			 *   filter in case user wants to manipulate the default array 
			 *	 (eg: stylswitch plugin wants to filter the options and overrule them)
			 * 
			 * @param array $database_option
			 * @return array
			 */
			$this->options = apply_filters( 'avia_filter_global_options', $database_option );

			/*
			 * Check option pages element ID's for non unique values
			 * 
			 * @since 4.8.2
			 */
			if( ! ( defined( 'WP_DEBUG' ) && ( WP_DEBUG === true ) && is_admin() && current_user_can( 'manage_options' ) ) )
			{
				return;
			}
			
			$ids = array();
			
			foreach( $this->option_page_data as $data ) 
			{
				if( ! isset( $data['id'] ) )
				{
					continue;
				}
				
				if( ! in_array( $data['id'], $ids ) )
				{
					$ids[] = $data['id'];
					continue;
				}
				
				error_log( 'Warning: In Theme Options Pages following element id is not unique: ' . $data['id'] . '  ( element type: ' . $data['type'] . ' )' );
			}
			
		}
		
		/**
		 * Reset the options
		 */
		public function reset_options()
		{
			unset( $this->options, $this->subpages, $this->option_page_data, $this->option_pages );
			
			$this->options = null;
			$this->subpages = array();
			$this->option_pages = array();
			$this->option_page_data = array();
			
			$this->_create_option_arrays();
		}
		
		/**
		 * Extracts the default values from the option_page_data array in case no database savings were done yet
		 * The functions calls itself recursive with a subset of elements if groups are encountered within that array
		 * 
		 * @param array $elements
		 * @param array $page
		 * @param array $subpages
		 * @return array
		 */
		public function extract_default_values( $elements, $page, $subpages )
		{
			$values = array();
			
			foreach( $elements as $element )
			{
				if( in_array( $element['slug'], $subpages[ $page['parent'] ] ) )
				{
					if( $element['type'] == 'group' )
					{
						$values[0][ $element['id'] ] = $this->extract_default_values( $element['subelements'], $page, $subpages );
					}
					else if( isset( $element['id'] ) )
					{
						if( ! isset( $element['std'] ) ) 
						{
							$element['std'] = '';
						}
						
						$values[$element['id']] = $element['std'];
					}
				}
			}
			
			return $values;
		}
		
		/**
         * This function is executed when the admin header is printed and will add the avia_framework_globals to javascript 
         * The avia_framework_globals object contains information about the framework
         */
		function set_javascript_framework_url()
		{
			echo "\n <script type='text/javascript'>\n /* <![CDATA[ */  \n";
			echo "var avia_framework_globals = avia_framework_globals || {};\n";
			echo "    avia_framework_globals.frameworkUrl = '" . AVIA_FW_URL . "';\n";
			echo "    avia_framework_globals.installedAt = '" . AVIA_BASE_URL . "';\n";
			echo "    avia_framework_globals.ajaxurl = '" . apply_filters( 'avia_ajax_url_filter', admin_url( 'admin-ajax.php' ) ) . "';\n";
			echo "/* ]]> */ \n";
			echo "</script>\n \n ";
		}
		
		/**
		 * Scan option keys for global scope
		 * 
		 * @since 4.8
		 */
		protected function get_global_option_keys() 
		{
			foreach( $this->option_pages as $key => $option_page ) 
			{
				$this->page_ref[ $option_page['slug'] ] = $option_page['parent'];
			}
			
			foreach( $this->option_page_data as $info ) 
			{
				if( ! isset( $info['global'] ) || ! isset( $info['id'] ) || true !== $info['global'] )
				{
					continue;
				}
				
				if( ! in_array( $info['id'], $this->global_keys ) )
				{
					$this->global_keys[ $info['id']] = isset( $this->page_ref[ $info['slug'] ] ) ? $this->page_ref[ $info['slug'] ] : $this->option_pages[0]['parent'];
				}
			}
		}
		
		/**
		 * Checks if the option key is a global scope option
		 * 
		 * @since 4.8
		 * @param string $key
		 * @return boolean
		 */
		public function is_global_option( $key ) 
		{
			return isset( $this->global_keys[ $key ] );
		}
		
		/**
		 * Returns the amount of global option keys
		 * 
		 * @since 4.8
		 * @return int
		 */
		public function global_options_count()
		{
			return count( $this->global_keys );
		}
		
		/**
		 * Returns the global option keys array
		 * 
		 * @since 4.8
		 * @return array
		 */
		public function global_option_keys() 
		{
			return $this->global_keys;
		}
	
	}
	
	/**
	 * Returns the main instance of avia_superobject to prevent the need to use globals
	 * 
	 * @since 4.3
	 * @param array|null $base_data
	 * @return avia_superobject
	 */
	function AviaSuperobject( $base_data = null ) 
	{
		return avia_superobject::instance( $base_data );
	}
	
}

