<?php
/**
 * Add support for plugin Smash Balloon Instagram Feed - basic and pro version (https://smashballoon.com/instagram-feed)
 * 
 * @since 4.7.3.1
 * @added_by Günter
 */

if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

/**
 * Return if plugin not active
 */
if ( ! class_exists( 'SB_Instagram_Feed' ) )
{
	return;
}


require_once 'class-avia-instagram-feed-widget.php';


if( ! class_exists( 'Avia_Instagram_Feed' ) )
{
	class Avia_Instagram_Feed 
	{
		/**
		 * @since 4.7.3.1
		 * @var string 
		 */
		static public $plugin = 'https://wordpress.org/plugins/instagram-feed/';
		
		/**
		 * @since 4.7.3.1
		 * @var string 
		 */
		static public $plugin_pro = 'https://smashballoon.com/instagram-feed/demo/';
		
		/**
		 * @since 4.7.3.1
		 * @var string 
		 */
		static protected $shortcode_name = 'instagram-feed';
	
		/**
		 * Holds the instance of this class
		 * 
		 * @since 4.7.3.1
		 * @var Avia_Instagram_Feed 
		 */
		static private $_instance = null;
		
		/**
		 *
		 * @since 4.7.3.1
		 * @var array 
		 */
		protected $pro_sc_params;
		
		/**
		 *
		 * @since 4.7.3.1
		 * @var array 
		 */
		protected $unit_params;
		
		/**
		 *
		 * @since 4.7.3.1
		 * @var array 
		 */
		protected $hex_params;

		/**
		 *
		 * @since 4.7.3.1
		 * @var array 
		 */
		protected $number_params;
		

		/**
		 * Return the instance of this class
		 * 
		 * @since 4.7.3.1
		 * @return Avia_Instagram_Feed
		 */
		static public function instance()
		{
			if( is_null( Avia_Instagram_Feed::$_instance ) )
			{
				Avia_Instagram_Feed::$_instance = new Avia_Instagram_Feed();
			}
			
			return Avia_Instagram_Feed::$_instance;
		}
		
		
		/**
		 * @since 4.7.3.1
		 */
		public function __construct() 
		{
			if( ! $this->is_plugin_active() )
			{
				return;
			}
			
			$this->pro_sc_params = $this->get_pro_sc_params();
			$this->unit_params = $this->get_unit_params();
			$this->hex_params = $this->get_hex_params();
			$this->number_params = $this->get_number_params();
			
			register_widget( 'Avia_Instagram_Feed_Widget' );
		}
		
		/**
		 * @since 4.7.3.1
		 */
		public function __destruct() 
		{
			unset( $this->pro_sc_params );
			unset( $this->unit_params );
			unset( $this->hex_params );
			unset( $this->number_params );
		}
		
		/**
		 * Returns if plugin or pro version is activated
		 * 
		 * @since 4.7.3.1
		 * @param boolean
		 */
		public function is_plugin_active() 
		{
			return class_exists( 'SB_Instagram_Feed' );
		}
	
		
		/**
		 * Returns if pro version is activated
		 * 
		 * @since 4.7.3.1
		 * @param boolean
		 */
		public function is_proversion_active() 
		{

			return class_exists( 'SB_Instagram_Feed_Pro' );
		}
		
		/**
		 * Returns an array of shortcode parameters with default values.
		 * Contains basic and pro params
		 * 
		 * @since 4.7.3.1
		 * @param array $merge
		 * @return array
		 */
		public function get_default_sc_params( $merge = array() ) 
		{
			$default = array( 
						'type'				=> 'user',
						'user'				=> '',
						'hashtag'			=> '',
						'order'				=> '',
						'width'				=> '',
						'height'			=> '',
						'background'		=> '',
						'class'				=> '',
						'layout'			=> 'isb-grid',
						'num'				=> 9,
						'nummobile'			=> '',
						'cols'				=> 3,
						'colsmobile'		=> '',
						'imagepadding'		=> '',
						'carouselrows'		=> '',
						'carouselloop'		=> '',
						'carouselarrows'	=> '',
						'carouselpag'		=> '',
						'carouselautoplay'	=> '',
						'carouseltime'		=> '',
						'highlighttype'		=> '',
						'highlightpattern'	=> '',
						'highlightoffset'	=> '',
						'highlighthashtag'	=> '',
						'sortby'			=> '',
						'imageres'			=> '',
						'media'				=> '',
						'disablelightbox'	=> '',
						'disablemobile'		=> '',
						'captionlinks'		=> '',
						'lightboxcomments'	=> '',
						'numcomments'		=> '',
						'hovercolor'		=> '',
						'hovertextcolor'	=> '',
						'hoverdisplay'		=> '',
						'showheader'		=> '',
						'showbio'			=> '',
						'headersize'		=> '',
						'headercolor'		=> '',
						'showbutton'		=> '',
						'buttoncolor'		=> '',
						'buttontextcolor'	=> '',
						'buttontext'		=> __( 'Load More Photos', 'avia_framework' ),
						'showfollow'		=> '',
						'followcolor'		=> '',
						'followtextcolor'	=> '',
						'followtext'		=> __( 'Follow Me', 'avia_framework' ),
						'showcaption'		=> '',
						'captionlength'		=> '',
						'captioncolor'		=> '',
						'captionsize'		=> '',
						'showlikes'			=> '',
						'likescolor'		=> '',
						'likessize'			=> '',
						'excludewords'		=> '',
						'includewords'		=> '',
						'whitelist'			=> '',
						'autoscroll'		=> '',
						'autoscrolldistance'	=> ''
					);
			
			if( is_array( $merge ) && ! empty( $merge ) )
			{
				$default = array_merge( $default, $merge );
			}
			
			/**
			 * @since 4.7.3.1
			 * @param array $default
			 * @param array $merge
			 * @return array
			 */
			return apply_filters( 'avf_instagram_feed_smash_default_sc_params', $default, $merge );
		}
		
		
		/**
		 * Returns an array of pro plugin sc parameters
		 * 
		 * @since 4.7.3.1
		 * @return array
		 */
		public function get_pro_sc_params() 
		{
			if( empty( $this->pro_sc_params ) )
			{
				$this->pro_sc_params = array(
									'type',
									'hashtag',
									'layout',
									'nummobile',
									'colsmobile',
									'carouselrows',
									'carouselloop',
									'carouselarrows',
									'carouselpag',
									'carouselautoplay',
									'carouseltime',
									'highlighttype',
									'highlightpattern',
									'highlightoffset',
									'highlighthashtag',
									'media',
									'disablelightbox',
									'captionlinks',
									'lightboxcomments',
									'numcomments',
									'hovercolor',
									'hovertextcolor',
									'hoverdisplay',
									'showcaption',
									'captionlength',
									'captioncolor',
									'captionsize',
									'showlikes',
									'likescolor',
									'likessize',
									'excludewords',
									'includewords',
									'whitelist',
									'autoscroll',
									'autoscrolldistance'
								);
			}
			
			/**
			 * @since 4.7.3.1
			 * @param array $this->pro_sc_params
			 * @return array
			 */
			return apply_filters( 'avf_instagram_feed_smash_pro_sc_params', $this->pro_sc_params );
		}
		
		/**
		 * Returns an array of unit parameters
		 * (includes px or %)
		 * 
		 * @since 4.7.3.1
		 * @return array
		 */
		public function get_unit_params()
		{
			if( empty( $this->unit_params ) )
			{
				$this->unit_params = array(
										'width',
										'height',
										'imagepadding'
									);
			}
				
			/**
			 * @since 4.7.3.1
			 * @param array $this->unit_params
			 * @return array
			 */
			return apply_filters( 'avf_instagram_feed_smash_unit_params', $this->unit_params );
		}
		
		/**
		 * Returns an array of parameters containing hex values for colors
		 * (#fdfdff)
		 * 
		 * @since 4.7.3.1
		 * @return array
		 */
		public function get_hex_params()
		{
			if( empty( $this->hex_params ) )
			{
				$this->hex_params = array(
										'background',
										'hovercolor',
										'hovertextcolor',
										'headercolor',
										'buttoncolor',
										'buttontextcolor',
										'followcolor',
										'followtextcolor',
										'captioncolor',
										'likescolor',
									);
			}
			
			/**
			 * @since 4.7.3.1
			 * @param array $this->hex_params
			 * @return array
			 */
			return apply_filters( 'avf_instagram_feed_smash_hex_params', $this->hex_params );
		}
		
		/**
		 * Returns an array of parameters containing int number values
		 * 
		 * @since 4.7.3.1
		 * @return array
		 */
		public function get_number_params()
		{
			if( empty( $this->number_params ) )
			{
				$this->number_params = array(
										'carouseltime',
										'highlightpattern',
										'highlightoffset',
										'numcomments',
										'captionlength',
										'captionsize',
										'likessize'
									);
			}
			
			/**
			 * @since 4.7.3.1
			 * @param array $this->hex_params
			 * @return array
			 */
			return apply_filters( 'avf_instagram_feed_smash_number_params', $this->number_params );
		}
		
				
		/**
		 * Returns the shortcode string including all parameters that are not default.
		 * If pro is not active, pro parameters are not rendered.
		 * 
		 * @since 4.7.3.1
		 * @param array $params
		 * @return string
		 */
		public function create_shortcode( array $params ) 
		{
			$default = $this->get_default_sc_params();
			$pro_params = $this->get_pro_sc_params();
			$unit_params = $this->get_unit_params();
			$number_params = $this->get_number_params();
			
			$sc_params = array();
			
			$params = shortcode_atts( $default, $params, Avia_Instagram_Feed::$shortcode_name );
			
			foreach( $params as $key => $value ) 
			{
				$value = trim( $value );
				if( '' == $value )
				{
					continue;
				}
				
				if( ! $this->is_proversion_active() && in_array( $key, $pro_params ) )
				{
					continue;
				}
				
				if( in_array( $key, $number_params ) )
				{
					$value = $this->validate_number( $value );
					if( '' == $value )
					{
						continue;
					}
				}
				else if( in_array( $key, $unit_params ) )
				{
					$value = $this->validate_unit( $value );
					if( '' == $value )
					{
						continue;
					}
					
					$unit = '';
					if( false !== strpos( $value, 'px' ) )
					{
						$unit = 'px';
					} 
					else if( false !== strpos( $value, '%' ) )
					{
						$unit = '%';
					}
					
					if( '' == $unit )
					{
						continue;
					}
					
					$value = trim( str_replace( $unit, '', $value ) );
					
					$sc_params[] = "{$key}unit='{$unit}'";
				}
				
				//	was added to avoid breaking CSS due to conditional option in widget being a CSS keyword
				if( 0 === strpos( $value, 'isb-' ) )
				{
					$value = substr( $value, 4 );
				}
				
				$value = str_replace( "'", '"', $value );
				$sc_params[] = "{$key}='{$value}'";
			}
			
			$sc = '[' . Avia_Instagram_Feed::$shortcode_name;
			
			if( ! empty( $sc_params ) )
			{
				$sc .= ' ' . implode( ' ', $sc_params );
			}
			
			$sc .= ']';
			
			return $sc;
		}
		
		/**
		 * Validates a value to be a number and % or px (= default).
		 * An invalid number returns empty string to default to plugin defaults.
		 * Returns e.g. 5px  15%
		 * 
		 * @since 4.7.3.1
		 * @param string $value
		 * @return string
		 */
		public function validate_unit( $value ) 
		{
			$value = trim( strtolower( $value ) );
			
			$unit = 'px';
			if( false !== strpos( $value, 'px' ) )
			{
				$value = str_replace( 'px', '', $value );
			}
			else if( false !== strpos( $value, '%' ) )
			{
				$value = str_replace( '%', '', $value );
				$unit = '%';
			}
			
			$value = trim( $value );
			
			if( ! is_numeric( $value ) )
			{
				return '';
			}
			
			return $value . $unit;
		}
		
		/**
		 * Validates a value to be a hex number - 6 digits
		 * 
		 * @since 4.7.3.1
		 * @param string $value
		 * @return string
		 */
		public function validate_hex_color( $value ) 
		{
			$value = ltrim( trim( strtolower( $value ) ), '#' );
			
			if( ! ctype_xdigit( $value ) )
			{
				return '';
			}
			
			if( strlen( $value ) == 6 )
			{
				return '#' . $value;
			}
			
			if( strlen( $value ) != 3 )
			{
				return '';
			}
			
			$new_value = '#' . str_repeat( substr( $value, 0, 1 ), 2 ) . str_repeat( substr( $value, 1, 1 ), 2 ) . str_repeat( substr( $value, 2, 1 ), 2 );
			
			return $new_value;
		}
		
		/**
		 * Validates a value to be an integer number
		 * 
		 * @since 4.7.3.1
		 * @param string $value
		 * @return string
		 */
		public function validate_number( $value ) 
		{
			$value = trim( strtolower( $value ) );
			
			if( ! is_numeric( $value ) )
			{
				return '';
			}
			
			return (int) $value;
		}
				
	
	
	}		//	end class Avia_Instagram_Feed
	
	
	/**
	 * Returns the main instance of Avia_Instagram_Feed to prevent the need to use globals
	 * 
	 * @since 4.7.3.1
	 * @return Avia_Instagram_Feed
	 */
	function AviaInstagramFeed()
	{
		return Avia_Instagram_Feed::instance();
	}
	
	/**
	 * Activate class
	 */
	AviaInstagramFeed();
	
}
