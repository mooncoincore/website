<?php
/**
 * Class that integrates Theme in WP Block editor. 
 * In a first step used to integrate theme css styles.
 * 
 * @since 4.5.5
 * @added_by Günter
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly



if( ! class_exists( 'Avia_Gutenberg_Theme_Integration') )
{
	

	class Avia_Gutenberg_Theme_Integration
	{
		/**
		 * Holds the instance of this class
		 * 
		 * @since 4.5.5
		 * @var Avia_Gutenberg_Theme_Integration 
		 */
		static private $_instance = null;
		
		/**
		 *
		 * @since 4.5.5
		 * @var avia_style_generator 
		 */
		protected $style_generator;

		/**
		 * Return the instance of this class
		 * 
		 * @since 4.5.5
		 * @return Avia_Gutenberg_Theme_Integration
		 */
		static public function instance()
		{
			if( is_null( Avia_Gutenberg_Theme_Integration::$_instance ) )
			{
				Avia_Gutenberg_Theme_Integration::$_instance = new Avia_Gutenberg_Theme_Integration();
			}
			
			return Avia_Gutenberg_Theme_Integration::$_instance;
		}
		
		/**
		 * @since 4.5.5
		 */
		protected function __construct() 
		{
			$this->style_generator = null;
			
			/**
			 * Add block editor customizations to make theme block editor ready, e.g. load theme custom styles
			 */
			add_action( 'after_setup_theme', array( $this, 'handler_after_setup_theme' ), 50 );
			add_action( 'admin_init', array( $this, 'handler_wp_admin_init' ), 50 );
			add_action( 'ava_after_theme_update', array( $this, 'handler_generate_dynamic_stylesheet' ), 50, 2 );				/*after theme update*/
			add_action( 'ava_after_import_demo_settings', array( $this, 'handler_generate_dynamic_stylesheet' ), 50, 2 );		/*after demo settings imoport*/
			add_action( 'avia_ajax_after_save_options_page', array( $this, 'handler_generate_dynamic_stylesheet' ), 50, 2 );	/*after options page saving*/
			add_filter( 'admin_body_class',  array( $this, 'handler_wp_admin_body_class' ), 50, 1 );
			add_action( 'admin_head', array( $this, 'handler_wp_admin_head' ), 50 );
		}
		
		/**
		 * @since 4.5.5
		 */
		public function __destruct() 
		{
			unset( $this->style_generator );
		}
		
		/**
		 * @since 4.5.5
		 * @return avia_style_generator
		 */
		public function get_style_generator()
		{
			if( is_null( $this->style_generator ) )
			{
				$so = AviaSuperobject();
				$this->style_generator = new avia_style_generator( $so, false, false, false );
			}
			
			return $this->style_generator;
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features, e.g. allow Custom Backend Editor Styles
		 * 
		 * @link	https://richtabor.com/add-wordpress-theme-styles-to-gutenberg/
		 * @link	https://github.com/richtabor/york-lite/blob/master/functions.php
		 * @since 4.5.5
		 */
		public function handler_after_setup_theme()
		{
			$template_url = get_template_directory_uri();
			

			add_theme_support( 'editor-styles' );
			
			/**
			 * Static files
			 * add_editor_style also adde "-rtl" to css filename 
			 */
			add_editor_style( $template_url . '/config-gutenberg/css/avia-gutenberg-editor.css' );
			
			/**
			 * Dynamically created styles depending on theme settings
			 */
			if( $this->use_dynamic_stylesheet() )
			{
				$url = $this->get_dynamic_stylesheet_info( 'url' );
				add_editor_style( $url );
			}
			
			/**
			 * Load URL's to Google Webfonts
			 */
			$used_fonts_urls = $this->get_used_fonts_urls();
			if( ! empty( $used_fonts_urls ) )
			{
				foreach( $used_fonts_urls as $used_fonts_url ) 
				{
					add_editor_style( $used_fonts_url );
				}
			}
			
			/*
			 * Enable support for Customizer Selective Refresh.
			 * @link https://make.wordpress.org/core/2016/02/16/selective-refresh-in-the-customizer/
			 */
			add_theme_support( 'customize-selective-refresh-widgets' );
			
			/*
			 * Enable support for responsive embedded content
			 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/#responsive-embedded-content
			 */
			add_theme_support( 'responsive-embeds' );

			/**
			 * Custom colors for use in the editor.
			 *
			 * @link https://wordpress.org/gutenberg/handbook/reference/theme-support/
			 */
			$used_color_palettes = $this->get_used_color_palettes();
			if( ! empty( $used_color_palettes ) )
			{
				add_theme_support( 'editor-color-palette', $used_color_palettes );
			}
			
			/**
			 * Custom font sizes for use in the editor.
			 *
			 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/#block-font-sizes
			 */
			$used_font_sizes = $this->get_used_font_sizes();
			if( ! empty( $used_font_sizes ) )
			{
				add_theme_support( 'editor-font-sizes', $used_font_sizes );
			}
		}
		
		/**
		 * Creates dynamic stylesheet if option does not exist or 'false'
		 * Save options, update theme, demo import forces a rebuild
		 * 
		 * @since 4.5.5
		 */
		public function handler_wp_admin_init()
		{
			$this->handler_generate_dynamic_stylesheet( null, 'check' );
		}
		
		/**
		 * Add font classes to <body> tag
		 * 
		 * @since 4.5.5
		 * @param string $classes
		 * @return string
		 */
		public function handler_wp_admin_body_class( $classes )
		{
			global $avia_config;
			
			$fonts = $avia_config['font_stack'];
			return $classes . ' ' . $avia_config['font_stack'];
		}
		
		
		/**
		 * Adds styles to <head> tag
		 * 
		 * @since 4.5.5
		 */
		public function handler_wp_admin_head()
		{
			if( is_null( $this->style_generator ) )
			{
				$sg = $this->get_style_generator();
				$sg->create_styles();
			}
			else
			{
				$sg = $this->get_style_generator();
			}
			
			echo $sg->link_google_font();
			
			if( ! $this->use_dynamic_stylesheet() )
			{
				if( ! function_exists( 'AviaGutenbergDynamicStyles' ) )
				{
					require_once trailingslashit( get_template_directory() ) . 'config-gutenberg/class-avia-gutenberg-dynamic-styles.php';
				}
				
				$script = $this->handler_generate_dynamic_stylesheet( null, 'script_only' );
				
				if( ! empty( $script ) )
				{
					echo $script;
				}
			}
		}
		
		/**
		 * Creates the dynamic stylesheet file and returns the script to embed inline
		 * 
		 * @since 4.5.5
		 * @param array $options
		 * @param string $action		'create_file'|'check'|'script_only'
		 * @return string|true
		 */
		public function handler_generate_dynamic_stylesheet( $options = array(), $action = 'create_file' )
		{
			if( 'create_file' == $action && ! is_null( $this->style_generator ) )
			{
				unset( $this->style_generator );
				$this->style_generator = null;
			}
			
			$sg = $this->get_style_generator();
			$sg->create_styles();
			
			$name = $this->get_dynamic_safe_name();
			$opt_name = 'avia_gutenberg_dyn_css_exists_' . $name;
			
			if( $this->use_dynamic_stylesheet() && ( 'check' == $action ) )
			{
				$dyn_stylesheet_exists = get_option( $opt_name, 'false' );
				
				if( 'true' == $dyn_stylesheet_exists )
				{
					return true;
				}
			}		

			require_once trailingslashit( get_template_directory() ) . 'config-gutenberg/class-avia-gutenberg-dynamic-styles.php';
				
			AviaGutenbergDynamicStyles()->create_styles();
			
			if( 'script_only' == $action )
			{
				return AviaGutenbergDynamicStyles()->get_head_css();
			}
			
			$saved = true;
			try
			{
				if( $this->use_dynamic_stylesheet() )
				{
					$file = $this->get_dynamic_stylesheet_info( 'path' );
					if( false === $file )
					{
						throw new Exception();
					}
					
					$created = avia_backend_create_file( $file, AviaGutenbergDynamicStyles()->get_dynamic_css_content(), true );
					if( true !== $created )
					{
						throw new Exception();
					}
				}
				else 
				{
					$saved = false;
				}
			}
			catch( Exception $ex ) 
			{
				$saved = false;
			}
			
			$opt_result = $saved ? 'true' : 'false';
			update_option( $opt_name, $opt_result );
			
			return $saved;
		}
		
		/**
		 * Returns the theme dependend name for the dynamic gutenberg css file
		 * 
		 * @since 4.5.5
		 * @return string
		 */
		protected function get_dynamic_safe_name()
		{
			global $avia;
		
			$safe_name = avia_backend_safe_string( $avia->base_data['prefix'] );
			
			/**
			 * @since 4.5.5
			 * @return string
			 */
			$safe_name = apply_filters( 'avf_gutenberg_dynamic_stylesheet_filename', $safe_name );
			return $safe_name;
		}
		
		/**
		 * Check if we use a dynamic stylesheet or we output CSS in header inline
		 * Add an option ????
		 * 
		 * @since 4.5.5
		 * @return boolean
		 */
		public function use_dynamic_stylesheet()
		{
			/**
			 * @since 4.5.5
			 * @return true|mixed			anything except true will be false
			 */
			$use = apply_filters( 'avf_gutenberg_use_dynamic_stylesheet', true );
			return is_bool( $use ) ? $use : false;
		}
		
		/**
		 * Complete URL including filename.
		 * Creates folder if not exists.
		 * 
		 * @since 4.5.5
		 * @param string $what					'url'|'path'|'filename'
		 * @return string|false
		 */
		public function get_dynamic_stylesheet_info( $what = 'path' )
		{
			global $avia;
			
			$safe_name = avia_backend_safe_string( $avia->base_data['prefix'] );
			
			/**
			 * Return filename without extension
			 * 
			 * @since 4.5.5
			 * @return string 
			 */
			$filename = apply_filters( 'avf_gutenberg_dyn_stylesheet_name', 'avia-gutenberg-dynamic-' . $safe_name );
			
			/**
			 * see also function avia_generate_stylesheet()
			 */
			$wp_upload_dir  = wp_upload_dir();
			
			switch( $what )
			{
				case 'filename':
					$ret = $filename;
					break;
				case 'path':
					$stylesheet_path = $wp_upload_dir['basedir'] . '/dynamic_avia';
					$stylesheet_path = str_replace( '\\', '/', $stylesheet_path );
					
					/**
					 * @since 4.5.5
					 * @return string 
					 */
					$stylesheet_path = apply_filters( 'avf_gutenberg_dyn_stylesheet_path',  $stylesheet_path );
					$isdir = avia_backend_create_folder( $stylesheet_path );
					$ret = ( false === $isdir ) ? false : trailingslashit( $stylesheet_path ) . $filename . '.css';;
					break;
				case 'url':
				default:
					$stylesheet_url = $wp_upload_dir['baseurl'] . '/dynamic_avia';
					$stylesheet_url = str_replace( '\\', '/', $stylesheet_url );
					
					/**
					 * @since 4.5.5
					 * @return string 
					 */
					$stylesheet_url = apply_filters( 'avia_gutenberg_dyn_stylesheet_url',  $stylesheet_url );
					$ret = trailingslashit( $stylesheet_url ) . $filename . '.css';
					break;
			}
			
			return $ret;
		}
		
		/**
		 * Returns an array of url's to CSS files of used fonts.
		 * Currently intended only for Google Webfonts.
		 * Not needed as we add our own script to load fonts.
		 * 
		 * @since 4.5.5
		 * @return array|null
		 */
		protected function get_used_fonts_urls()
		{
			$font_families = array();
			
			/**
			 * Following an example only
			 */
//			$font_families1 = array();
//			$font_families1[] = 'Flamenco:400,400i,700,700i';
//			$font_families1[] = 'Playfair Display:400,400i,700,700i';
//			$font_families1[] = 'Lora:400,400i,700,700i';
//			
//			$query_args = array(
//								'family' => rawurlencode( implode( '|', $font_families1 ) ),
//								'subset' => rawurlencode( 'latin,latin-ext' ),
//							);
//			
//			$font_families1 = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
//			
//			
//			$font_families[] = $font_families1;
			
			return $font_families;
		}
		
		/**
		 * Returns an array of colors
		 * 
		 * @since 4.5.5
		 * @return array|null
		 */
		protected function get_used_color_palettes()
		{
			/**
			 * Get all selected colors from theme options
			 * see includes\admin\register-dynamic-styles.php
			 */
			$options = avia_get_option();
			$colors = array();
			
			foreach( $options as $key => $option )
			{
				if( empty( $option ) )
				{
					continue;
				}
				
				if( strpos( $key, 'colorset-' ) === 0 || strpos( $key, 'color-' ) === 0 )
				{
					if( strpos( $option, '#' ) === 0 )
					{
						$colors[ $key ] = $option;
					}
				}
			}
			
			$colors = array_unique( $colors );
			sort( $colors, SORT_STRING );
			
			$color_palettes = array();
			
			foreach( $colors as $color ) 
			{
				$color_palettes[] = array(
										'name'  => $color,
										'slug'  => sprintf( esc_html__( 'col_%s', 'avia_framework' ), $color ),
										'color' => $color
									);
			}
			
			return $color_palettes;
		}
		
		/**
		 * Returns an array of font sizes
		 * 
		 * @since 4.5.5
		 * @return array|null
		 */
		protected function get_used_font_sizes()
		{
			$used_font_sizes = array();
			
			for( $i = 8; $i <= 80; $i++ )
			{
				$used_font_sizes[] = array(
											'name'      => sprintf( esc_html__( '%d px', 'avia_framework' ), $i ),
											'shortName' => sprintf( esc_html__( '%d px', 'avia_framework' ), $i ),
											'size'      => $i,
											'slug'      => "fs{$i}px"
										);
			}
			
			return $used_font_sizes;
		}
		
	}
	
	/**
	 * Returns the main instance of Gutenberg_Dynamic_Styles to prevent the need to use globals
	 * 
	 * @since 4.5.5
	 * @return Avia_Gutenberg_Theme_Integration
	 */
	function AviaGutenbergThemeIntegration()
	{
		return Avia_Gutenberg_Theme_Integration::instance();
	}

}