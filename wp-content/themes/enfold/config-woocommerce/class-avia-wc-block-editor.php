<?php
/**
 * Integration of WC Block Elements.
 * Class is only loaded when block editor is selected in theme options.
 * 
 * @since 4.6.4
 * @since WC 3.8
 * @added_by Günter
 */

if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if( ! class_exists( 'Avia_WC_Block_Editor' ) )
{
	class Avia_WC_Block_Editor
	{
		/**
		 * @since 4.6.4
		 * @var Avia_WC_Block_Editor 
		 */
		static private $_instance = null;
		
		
		/**
		 * Returns the only instance
		 * 
		 * @since 4.6.4
		 * @return Avia_WC_Block_Editor
		 */
		static public function instance()
		{
			if( is_null( Avia_WC_Block_Editor::$_instance ) )
			{
				Avia_WC_Block_Editor::$_instance = new Avia_WC_Block_Editor();
			}
			
			return Avia_WC_Block_Editor::$_instance;
		}
		
		/**
		 * @since 4.6.4
		 */
		public function __construct() 
		{
			
			if( ! is_admin() )
			{
				add_action( 'wp_head', array( $this, 'handler_wp_head_icon_fonts' ), 1 );
			}
			
			if( 'theme_styles' == AviaGutenberg()->block_editor_theme_support() )
			{
				add_action( 'admin_init', array( $this, 'handler_wp_register_scripts' ), 10 );
				
				add_filter( 'avf_gutenberg_fonts_selectors', array( $this, 'handler_avf_gutenberg_fonts_selectors' ), 10, 4 );
				add_filter( 'avf_gutenberg_create_styles_rules', array( $this, 'handler_avf_create_styles_rules' ), 10, 2 );
				
				add_filter( 'avia_dynamic_css_output', array( $this, 'handler_avia_dynamic_css_output' ), 10, 2 );
			}
		}
		
		/**
		 * 
		 * @since 4.6.4
		 */
		public function handler_wp_register_scripts()
		{	
			$vn = avia_get_theme_version();
			$template_url = get_template_directory_uri();
			
			wp_register_style( 'avia_wc_blocks_admin_css', $template_url . '/config-woocommerce/admin/woo-admin-blocks.css', array( 'avia-modal-style', 'avia-builder-style', 'avia_gutenberg_css' ), $vn );
			Avia_Builder()->add_registered_admin_style( 'avia_wc_blocks_admin_css' );
		}
		
		/**
		 * Fix problem that icon fonts are not loaded
		 * 
		 * @since 4.6.4
		 */
		public function handler_wp_head_icon_fonts()
		{
			$url = plugins_url( 'woocommerce/assets/fonts' );

			$out = '<style type="text/css">';

			$out .= '@font-face { ';
			$out .=		"font-family: 'star'; ";
			$out .=		"src: url('{$url}/star.eot'); ";
			$out .=		"src: url('{$url}/star.eot?#iefix') format('embedded-opentype'), ";
			$out .=		"	  url('{$url}/star.woff') format('woff'), ";
			$out .=		"	  url('{$url}/star.ttf') format('truetype'), ";
			$out .=		"	  url('{$url}/star.svg#star') format('svg'); ";
			$out .=		'font-weight: normal; ';
			$out .=		'font-style: normal; ';
			$out .= ' } ';
			
			$out .= '@font-face { ';
			$out .=		"font-family: 'WooCommerce'; ";
			$out .=		"src: url('{$url}/WooCommerce.eot'); ";
			$out .=		"src: url('{$url}/WooCommerce.eot?#iefix') format('embedded-opentype'), ";
			$out .=		"	  url('{$url}/WooCommerce.woff') format('woff'), ";
			$out .=		"	  url('{$url}/WooCommerce.ttf') format('truetype'), ";
			$out .=		"	  url('{$url}/WooCommerce.svg#WooCommerce') format('svg'); ";
			$out .=		'font-weight: normal; ';
			$out .=		'font-style: normal; ';
			$out .= ' } ';

			$out .= '</style>';
			
			echo $out;
		}
		
		/**
		 * Add selectors to use google webfonts (selected in theme options)
		 * 
		 * @since 4.6.4
		 * @param array $selectors
		 * @param array $rule
		 * @param array $rules
		 * @param int $index
		 * @return array
		 */
		public function handler_avf_gutenberg_fonts_selectors( array $selectors, array $rule, array $rules, $index )
		{
			//	Selectors for "Heading Font" option 
			$google_webfont = array(
									'.wc-block-featured-product__wrapper h2',
									'.wc-block-featured-category__wrapper h2'
								);
			
			//	Selectors for "Font for your body text" option 
			$default_font = array(
									'.wc-block-all-reviews div',
									'.wc-block-reviews-by-category div',
									'.wc-block-featured-product__wrapper',
									'.wc-block-grid ul',
									'.wc-block-product-categories ul',
									'.wc-block-product-categories div'
								);
			
			if( ! empty( $selectors['google_webfont'] ) )
			{
				$google_webfont = array_merge( $selectors['google_webfont'], $google_webfont );
			}
			
			if( ! empty( $selectors['default_font'] ) )
			{
				$default_font = array_merge( $selectors['default_font'], $default_font );
			}
			
			$selectors['google_webfont'] = $google_webfont;
			$selectors['default_font'] = $default_font;
			
			return $selectors;
		}

		/**
		 * Add specific backend styles for WC Blocks
		 * 
		 * @since 4.6.4
		 * @param array $rules
		 * @param Avia_Gutenberg_Dynamic_Styles $style_object
		 * @return array
		 */
		public function handler_avf_create_styles_rules( $rules, $style_object )
		{
			global $avia_config;
			
			if( ! isset(  $avia_config['backend_colors']['color_set']['main_color'] ) )
			{
				return $rules;
			}
			
			$colors = $avia_config['backend_colors']['color_set']['main_color'];
			
			$new = array();
			

			$constant_font = avia_backend_calc_preceived_brightness( $colors['primary'], 230 ) ?  '#ffffff' : $colors['bg'];
			$button_border  = avia_backend_calculate_similar_color( $colors['primary'], 'darker', 2 );
			$button_border2 = avia_backend_calculate_similar_color( $colors['secondary'], 'darker', 2);
			
			$sel = array(
						'.wc-block-grid .wc-block-grid__product-title',
						'.wc-block-grid .wc-block-grid__product-price',
					);
			
			$styles = array(
							"color: {$colors['primary']}",
						);
							
			$new[] = array(
							'key'		=> 'block_direct_input',
							'selectors'	=> $sel,
							'styles'	=> $styles
						);
			
			$sel = array(
						'.wc-block-grid .wc-block-grid__product-price del',
					);
			
			$styles = array(
							"color: {$colors['meta']}"
						);
							
			$new[] = array(
							'key'		=> 'block_direct_input',
							'selectors'	=> $sel,
							'styles'	=> $styles
						);
			
			
			$sel = array(
						'.wc-block-grid .wp-block-button a',
						'.wc-block-featured-product__wrapper .wp-block-button .wp-block-button__link',
						'.wc-block-featured-category__wrapper .wp-block-button .wp-block-button__link',
						'.wc-block-load-more .wp-block-button__link'
					);
			
			$styles = array(
							"background-color: {$colors['primary']}",
							"color: $constant_font",
							"border-color: $button_border"
						);
								
			$new[] = array(
							'key'		=> 'block_direct_input',
							'selectors'	=> $sel,
							'styles'	=> $styles
						);
			
			$sel = array(
						'.wc-block-featured-product__wrapper .wp-block-button.is-style-outline .wp-block-button__link',
						'.wc-block-featured-category__wrapper .wp-block-button.is-style-outline .wp-block-button__link'
					);
			
			$styles = array(
							"background-color: transparent",
						);
			
			$new[] = array(
							'key'		=> 'block_direct_input',
							'selectors'	=> $sel,
							'styles'	=> $styles
						);
			
			$sel = array(
						'.wc-block-grid .wp-block-button a:hover',
						'.wc-block-featured-product__wrapper .wp-block-button .wp-block-button__link:hover',
						'.wc-block-featured-category__wrapper .wp-block-button .wp-block-button__link:hover',
						'.wc-block-load-more .wp-block-button__link:hover'
					);
			
			$styles = array(
							"background-color: {$colors['secondary']}",
							"color: {$colors['bg']}",
							"border-color: $button_border2"
						);
							
			$new[] = array(
							'key'		=> 'block_direct_input',
							'selectors'	=> $sel,
							'styles'	=> $styles
						);
		
			
			$rules = array_merge( $rules, $new );
			
			return $rules;
		}
		
		
		/**
		 * Output dynamic frontend CSS for WC blocks
		 * 
		 * @since 4.6.4
		 * @param string $output
		 * @param array $color_set
		 * @return array
		 */
		public function handler_avia_dynamic_css_output( $output, $color_set )
		{
			/*color sets*/
			foreach( $color_set as $key => $colors ) // iterates over the color sets: usually $key is either: header_color, main_color, footer_color, socket_color
			{
				if( ! in_array( $key, array( 'main_color' ) ) )
				{
					continue;
				}
				
				$key = ".".$key;
				
				extract( $colors );
				
				$constant_font = avia_backend_calc_preceived_brightness( $primary, 230 ) ?  '#ffffff' : $bg;
				$button_border  = avia_backend_calculate_similar_color( $primary, 'darker', 2 );
				$button_border2 = avia_backend_calculate_similar_color( $secondary, 'darker', 2);
				
				
				$output .= "
				
$key .wc-block-grid .wp-block-button a,
$key .wc-block-featured-product .wp-block-button:not(.is-style-outline) a,
$key .wc-block-featured-category .wp-block-button:not(.is-style-outline) a,
$key .wc-block-load-more .wp-block-button__link{
	background-color: $primary;
	color: $constant_font;
	border-color: $button_border;
}

$key .wc-block-featured-product .wp-block-button .is-style-outline a,
$key .wc-block-featured-category .wp-block-button .is-style-outline a{
	background-color: transparent;
	color: $constant_font;
	border-color: $button_border;
}



$key .wc-block-grid .wp-block-button a:hover,
$key .wc-block-featured-product .wp-block-button:not(.is-style-outline) a:hover,
$key .wc-block-featured-category .wp-block-button:not(.is-style-outline) a:hover,
$key .wc-block-load-more .wp-block-button__link:hover{
	background-color: $secondary;
	color: $bg;
	border-color: $button_border2;
}

$key .wc-block-featured-product .wp-block-button .is-style-outline a:hover,
$key .wc-block-featured-category .wp-block-button .is-style-outline a:hover{
	background-color: transparent;
	color: $bg;
	border-color: $button_border2;
}

$key .wc-block-grid .wc-block-grid__product-title,
$key .wc-block-grid .wc-block-grid__product-price price{
	color: $primary;
}

				";
			}
			
			//unset all vars with the help of variable vars :)
			foreach( $colors as $key => $val )
			{ 
				unset( $$key );
			}
			
			return $output;
		}
	}
	
	
	/**
	 * Returns the main instance of Avia_WC_Block_Editor to prevent the need to use globals
	 * 
	 * @since 4.6.4
	 * @return Avia_WC_Block_Editor
	 */
	function Avia_WCBlockEditor() 
	{
		return Avia_WC_Block_Editor::instance();
	}
	
	Avia_WCBlockEditor();
}