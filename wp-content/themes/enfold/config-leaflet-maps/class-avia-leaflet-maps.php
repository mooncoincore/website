<?php
/**
 * Adds support for plugin Leaflet Maps
 * 
 * Plugin URI: https://wordpress.org/plugins/leaflet-map/
 * Github: https://github.com/bozdoz/wp-plugin-leaflet-map
 * Leaflet download: https://leafletjs.com/index.html
 * 
 * @added_by Günter
 * @since 4.8.2
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if( ! class_exists( 'Avia_Leaflet_Maps' ) )
{
	class Avia_Leaflet_Maps
	{
		/**
		 * Holds the instance of this class ( or of a derived class )
		 * 
		 * @since 4.8.2
		 * @var Avia_Leaflet_Maps 
		 */
		private static $_instance = null;
		
		/**
		 * @since 4.8.2
		 * @var boolean
		 */
		protected $enabled;
		
		/**
		 * @since 4.8.2
		 * @var int
		 */
		protected $map_count;


		/**
		 * Return the instance of this class
		 *
		 * @since 4.8.2
		 * @return Avia_Leaflet_Maps
		 */
		static public function instance()
		{
			if( is_null( Avia_Leaflet_Maps::$_instance ) )
			{
				Avia_Leaflet_Maps::$_instance = new Avia_Leaflet_Maps();
			}
			
			return Avia_Leaflet_Maps::$_instance;
		}
		
		/**
		 * @since 4.8.2
		 */
		protected function __construct() 
		{
			$this->enabled = null;
			$this->map_count = 0;
			
			add_action( 'init', array( $this, 'handler_wp_init_plugin_settings' ), 50 );
			add_action( 'init', array( $this, 'handler_wp_register_scripts' ), 10 );
		}
		
		/**
		 * @since 4.8.2
		 */
		public function __destruct() 
		{
			;
		}
		
		/**
		 * 
		 * @since 4.8.2
		 */
		public function handler_wp_register_scripts()
		{	
			if( ! $this->is_active() )
			{
				return;
			}
			
			$vn = avia_get_theme_version();
			
			$template_url = get_template_directory_uri();
			
			wp_register_script( 'avia_leaflet_maps_backend_script', $template_url . '/config-leaflet-maps/js/avia-leaflet-maps.js', array( 'jquery' ), $vn, true );
			Avia_Builder()->add_registered_admin_script( 'avia_leaflet_maps_backend_script' );
			
		}
		
		/**
		 * Make sure to override the plugin options with our settings, reset or leave unchanged
		 * 
		 * @since 4.8.2
		 */
		public function handler_wp_init_plugin_settings() 
		{
			if( ! $this->is_active() )
			{
				return;
			}
			
			$template_url = get_template_directory_uri();
			
			$plugin_settings = Leaflet_Map_Plugin_Settings::init();
			
			$plugin_opt = $plugin_settings->options;
			
			$plugin_js = get_option( $plugin_settings->prefix . 'js_url' );
			$plugin_css = get_option( $plugin_settings->prefix . 'js_url' );
			
			$theme_js = avia_get_option( 'leaflet_maps_js_file' );
			$theme_css = avia_get_option( 'leaflet_maps_css_file' );
			
			//	fallback if user forgot to enter one setting
			if( 'custom' == $theme_js && 'custom' == $theme_css && ( empty( $theme_js ) || empty( $theme_css ) ) )
			{
				$theme_js = '';
				$theme_css = '';
			}
			
			$update = false;
			if( empty( $theme_js ) )
			{
				$url = $template_url . '/config-leaflet-maps/assets/leafletjs/leaflet.js';
				
				if( $plugin_js != $url )
				{
					$update = true;
				}
			}
			else if( 'default' == $theme_js )
			{
				$url = $plugin_opt['js_url']->default;
				
				if( $plugin_js != $url )
				{
					$update = true;
				}
			}
			
			if( $update )
			{
				update_option( $plugin_settings->prefix . 'js_url', $url );
			}
			
			$update = false;
			if( empty( $theme_css ) )
			{
				$url = $template_url . '/config-leaflet-maps/assets/leafletjs/leaflet.css';
				
				if( $plugin_css != $url )
				{
					$update = true;
				}
			}
			else if( 'default' == $theme_js )
			{
				$url = $plugin_opt['css_url']->default;
				
				if( $plugin_css != $url )
				{
					$update = true;
				}
			}
			
			if( $update )
			{
				update_option( $plugin_settings->prefix . 'css_url', $url );
			}
		}


		/**
		 * Checks if this feature is enabled
		 * 
		 * @since 4.8.2
		 * @return boolean
		 */
		public function is_enabled() 
		{
			if( ! is_bool( $this->enabled ) )
			{
				$enabled = avia_get_option( 'leaflet_maps_enable_feature', '' ) == 'enabled';
				
				/**
				 * @since 4.8.2
				 * @param boolean $enabled
				 * @return boolean
				 */
				$this->enabled = apply_filters( 'avf_avia_leaflet_maps_enabled', $enabled );
			}
			
			return $this->enabled;
		}
		
		/**
		 * Return Map count - gets incremented  when a map is created
		 * 
		 * @since 4.8.2
		 * @return int
		 */
		public function get_map_count() 
		{
			return $this->map_count;
		}

		/**
		 * Checks, if plugin is active AND enabled in theme options
		 * 
		 * @since 4.8.2
		 * @return boolean
		 */
		public function is_active() 
		{
			return class_exists( 'Leaflet_Map' ) && $this->is_enabled();
		}
		
		/**
		 * Creates the HTML for a map: creates and executes the leaflet shortcode
		 * 
		 * @since 4.8.2
		 * @param array $sc_atts
		 * @return string
		 */
		public function get_maps_html_from_alb_shortode( array $sc_atts ) 
		{
			$this->map_count ++;
			
			$output = '';
			
			$output .= $this->get_map_shortcode( $sc_atts );
			
			foreach( $sc_atts['content'] as $key => $marker_item ) 
			{
				$output .= $this->get_marker_shortcode( $marker_item, $key );
			}
			
			/**
			 * Add additional map shortcodes
			 * 
			 * @since 4.8.2
			 * @param string $output
			 * @param array $sc_atts
			 * @return string
			 */
			$out = apply_filters( 'avf_leaflet_maps_output', $output, $sc_atts );
			
			$html = do_shortcode( $out );
			
			return $html;
		}
		
		/**
		 * Returns the leaflet shortcode for the map
		 * 
		 * @since 4.8.2
		 * @param array $sc_atts
		 * @return string
		 */
		protected function get_map_shortcode( array $sc_atts )
		{	
			$map_params = array();
			
			$lon_lat = $this->create_long_lat_string( $sc_atts );
			
			if( false === $lon_lat )
			{
				$map_params[] = $this->create_address_string( $sc_atts );
			}
			else
			{
				$map_params[] = $lon_lat;
			}
			
			$map_params[] = ( $sc_atts['zoomcontrol'] == 'control' ) ? 'zoomcontrol="1"' : 'zoomcontrol="0"';
			$map_params[] = ( $sc_atts['scrollwheel'] == '' ) ? 'scrollwheel="1"' : 'scrollwheel="0"';
			$map_params[] = ( $sc_atts['touch_zoom'] == '' ) ? 'touchZoom="1"' : '!touchZoom';
			$map_params[] = ( $sc_atts['keyboard_support'] == '' ) ? 'keyboard="1"' : 'keyboard="0"';
			$map_params[] = ( $sc_atts['show_scale'] == '' ) ? 'show_scale="1"' : 'show_scale="0"';
			$map_params[] = ( $sc_atts['dragging'] == 'no_drag' ) ? 'dragging="0"' : 'dragging="1"';
			$map_params[] = ( $sc_atts['closepopuponclick'] == 'close_click' ) ? 'closepopuponclick="1"' : 'closepopuponclick="0"';
			
			if( is_numeric( $sc_atts['zoom'] ) )
			{
				$map_params[] = 'zoom="' . $sc_atts['zoom'] . '"';
			}
			else
			{
				$map_params[] = 'fitbounds="true"';
			}	
			
			$map_params[] = 'min_zoom="' . $sc_atts['min_zoom'] . '"';
			$map_params[] = 'max_zoom="' . $sc_atts['max_zoom'] . '"';
			$map_params[] = 'width="' . $sc_atts['width'] . '%"';
			$map_params[] = 'height="' . $sc_atts['height'] . 'px"';
			
			/**
			 * Fix bug with Safari Browser
			 * https://kriesi.at/support/topic/leaflet-marker-popup-not-working/
			 */
			$map_params[] = 'tap="false"';
			
			$map_params[] = ( $sc_atts['detect_retina'] == 'detect_retina' ) ? 'detect-retina="1"' : '!detect-retina';
			
			$add_params = $this->additional_sc_params( $sc_atts['map_attr'] );
			
			if( ! empty( $add_params ) )
			{
				$map_params = array_merge( $map_params, $add_params );
			}
			
			/**
			 * @since 4.8.2
			 * @param array $map_params
			 * @param array $sc_atts
			 * @param string $context
			 * @return array
			 */
			$map_params = apply_filters( 'avf_leaflet_maps_params', $map_params, $sc_atts, 'get_map_shortcode' );
			
			$output = '[leaflet-map ' . implode( ' ', $map_params ) . ']';
			
			return $output;
		}
		
		/**
		 * Returns the leaflet shortcode for a marker
		 * 
		 * @since 4.8.2
		 * @param array $marker_item
		 * @param int $marker_index
		 * @return string
		 */
		protected function get_marker_shortcode( array $marker_item, $marker_index )
		{
			$output = '';
			$map_params = array();
			
			$sc_atts = $marker_item['attr'];
			
			$lng = $this->create_long_lat_string( $sc_atts );
			
			if( false === $lng )
			{
				$map_params[] = $this->create_address_string( $sc_atts );
			}
			else
			{
				$map_params[] = $lng;
			}
			
			$map_params[] = ( $sc_atts['draggable'] == 'draggable' ) ? 'draggable="1"' : 'draggable="0"';
			$map_params[] = ( $sc_atts['visible'] == 'visible' ) ? 'visible="1"' : 'visible="0"';
			
			if( trim( $sc_atts['tooltip'] ) != '' )
			{
				$map_params[] = 'title="' . trim( $sc_atts['tooltip'] ) . '"';
			}
			
			$this->add_custom_marker_icon( $map_params, $sc_atts );
			
			if( ! empty( $sc_atts['marker_opacity'] ) && is_numeric( $sc_atts['marker_opacity'] ) )
			{
				$map_params[] = 'opacity="' . ( $sc_atts['marker_opacity'] / 100.0 ) . '"';
			}
			
			
			$add_params = $this->additional_sc_params( $sc_atts['marker_attr'] );
			
			if( ! empty( $add_params ) )
			{
				$map_params = array_merge( $map_params, $add_params );
			}
			
			$popup = trim( ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $marker_item['content'] ) ) );
			
			//	remove surroundig p tag
			if( 0 === strpos( $popup, '<p>' ) )
			{
				$close = strrpos( $popup, '</p>' );
				
				if( false !== $close )
				{
					$popup = substr_replace( $popup, '', $close, 4 );
				}
				
				$popup = substr( $popup, 3 );
			}
			
			/**
			 * @since 4.8.2
			 * @param array $map_params
			 * @param array $sc_atts
			 * @param string $context
			 * @param int $marker_index
			 * @return array
			 */
			$map_params = apply_filters( 'avf_leaflet_maps_params', $map_params, $sc_atts, 'get_marker_shortcode', $marker_index );
			
			
			$output .= '[leaflet-marker ' . implode( ' ', $map_params ) . ']' . $popup . '[/leaflet-marker]';

			return $output;
		}

		/**
		 * Creates an address shortcode attribute string
		 * 
		 * @since 4.8.2
		 * @param array $param
		 * @return string
		 */
		protected function create_address_string( array &$param ) 
		{
			$address = array();
			
			$default = array(
						'geo_street'		=> '',
						'geo_postalcode'	=> '',
						'geo_city'			=> '',
						'geo_country'		=> '',
						'geo_state'			=> '',
						'geo_county'		=> '',
					);
			
			$addr = array_merge( $default, $param );
			
			foreach( $default as $key => $value ) 
			{
				if( ! empty( $addr[ $key ] ) )
				{
					$address[] = $addr[ $key ];
				}
			}
			
			if( empty( $address ) )
			{
				return '';
			}
			
			$address = 'address="' . implode( ', ', $address ) . '"';
			
			return $address;
		}
		
		/**
		 * Creates a longitude and latitude shortcode attribute string
		 * 
		 * @since 4.8.2
		 * @param array $param
		 * @return string
		 */
		protected function create_long_lat_string ( array &$param ) 
		{
			$address = array();
			
			$default = array(
						'geo_lng'	=> '',
						'geo_lat'	=> ''
					);
			
			$addr = array_merge( $default, $param );
			
			if( empty( $addr['geo_lng'] ) || empty( $addr['geo_lat'] ) || ! is_numeric( $addr['geo_lng'] ) || ! is_numeric( $addr['geo_lat'] ) )
			{
				return false;
			}
			
			$address = 'lng="' . $addr['geo_lng'] . '" lat="' . $addr['geo_lat'] . '"';
			
			return $address;
		}
		
		/**
		 * Adds a custom marker icon to the marker shortcode
		 * 
		 * @since 4.8.2
		 * @param array $param
		 * @param array $sc_atts
		 */
		protected function add_custom_marker_icon( array &$param, array &$sc_atts )
		{
			if( empty( $sc_atts['custom_marker'] ) )
			{
				return;
			}
			
			$url = '';
			
			//	fallback to default if necessary info is missing
			if( 'custom_url' == $sc_atts['custom_marker'] )
			{
				if( '' == trim( $sc_atts['icon_url'] ) )
				{
					return;
				}
				
				$url = $sc_atts['icon_url'];
			}
			else if( 'upload_custom' == $sc_atts['custom_marker'] )
			{
				if( '' == trim( $sc_atts['icon_upload'] ) )
				{
					if( '' == $sc_atts['attachment'] || ! is_numeric( $sc_atts['attachment'] ) )
					{
						return;
					}
					
					$size = ! empty( $sc_atts['attachment_size'] ) ? $sc_atts['attachment_size'] : 'thumbnail';
					$img = wp_get_attachment_image_src( $sc_atts['attachment'], $size );
					
					$url = $img[0];
				}
				else
				{
					$url = $sc_atts['icon_upload'];
				}
			}
			else
			{
				return;
			}
			
			$width = ! empty( $sc_atts['icon_width'] ) ? $sc_atts['icon_width'] : 30;
			$height = ! empty( $sc_atts['icon_height'] ) ? $sc_atts['icon_height'] : 50;
			$left = ! empty( $sc_atts['icon_anchor_left'] ) ? $sc_atts['icon_anchor_left'] : 15;
			$top = ! empty( $sc_atts['icon_anchor_top'] ) ? $sc_atts['icon_anchor_top'] : 60;
			
			$param[] = 'iconUrl="' . esc_url( $url ) . '"';
			$param[] = 'iconSize="' . "{$width},{$height}" . '"';
			$param[] = 'iconAnchor="' . "{$left},{$top}" . '"';
			
		}
		
		/**
		 * Splits the textarea content and returns an array of the parameters
		 * 
		 * @since 4.8.2
		 * @param string $params
		 * @return array
		 */
		protected function additional_sc_params( $params )
		{
			if( trim( $params ) == '' )
			{
				return array();
			}
			
			//	remove <br> tag and empty lines
			$params = preg_replace( '#<br\s*/?>#i', "\n", $params );
			
			$arr = explode( "\n", $params );
			$arr = array_map( 'trim', $arr );
			$arr = array_filter( $arr );
			
			return $arr;
		}
	
	}
	
	/**
	 * Returns the main instance of aviaElementTemplates to prevent the need to use globals
	 *
	 * @since 4.8.2
	 * @return Avia_Leaflet_Maps
	 */
	function AviaLeafletMaps()
	{
		return Avia_Leaflet_Maps::instance();
	}

	/**
	 * Activate filter and action hooks
	 */
	AviaLeafletMaps();
	
	
}
