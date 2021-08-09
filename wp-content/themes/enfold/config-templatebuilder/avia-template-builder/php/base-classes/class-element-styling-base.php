<?php
namespace aviaBuilder\base;
use \AviaHelper;

/**
 * This base class implements extended styling support. It contains the methods necessary to store and handle access.
 * 
 * Adds support to generate <style> tags, style rules and class strings for a single shortcode element.
 * 
 * @author		Günter
 * @since 4.8.4
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( __NAMESPACE__ . '\aviaElementStylingBase' ) )
{
	class aviaElementStylingBase 
	{
		/**
		 * Shortcode we need to create inline styles
		 * 
		 * @since 4.8.4
		 * @var aviaShortcodeTemplate
		 */
		protected $shortcode;
		
		/**
		 * 
		 * @since 4.8.4
		 * @var boolean
		 */
		protected $is_modal_item;
		
		/**
		 * Elements that are needed to create styles (depending on $is_modal_item)
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		protected $elements;
		
		/**
		 * Unique element id normally built from args of shortcode before creating this object
		 * 
		 * @var string
		 */
		protected $element_id;
		
		/**
		 * Stores already combined keyframes, styles and classes for callback option elements, id of element is main key 
		 * (e.g. 'box-shadow' => '5px 4px 3px red;' ):
		 * 
		 *		'$id' => array(
		 *					'keyframes'	=> array ( 'value', ... )
		 *					'styles'	=> array ( 'attr' => 'value' )
		 *					'classes'	=> array ( 'value', ... )
		 *				)
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		protected $callback_settings;
		
		/**
		 * Stores styles for seperate containers:
		 * 
		 *		'container' => array( 'attr' => 'value' )
		 * 
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		protected $container_styles;
		
		/**
		 * Stores classes for seperate containers:
		 * 
		 *		'container' => array( 'value', ... )
		 * 
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		protected $container_classes;
		
		/**
		 * Array of selectors for styles
		 * 
		 *		'selector' => array( 'container_id' )
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		protected $style_selectors;
		
		/**
		 * @since 4.8.4
		 * @var string
		 */
		protected $new_ln;
		
		/**
		 *
		 * @since 4.8.4
		 * @param \aviaShortcodeTemplate $shortcode
		 * @param string $element_id
		 */
		protected function __construct( \aviaShortcodeTemplate $shortcode, $element_id )
		{
			$this->shortcode = $shortcode;
			$this->element_id = $element_id;
			
			$this->is_modal_item = false;
			$this->elements = array();
			
			$this->callback_settings = array();
			$this->container_styles = array();
			$this->container_classes = array();
			$this->style_selectors = array();
			
			$this->new_ln = "\n";
		}
		
		/**
		 * @since 4.8.4
		 */
		public function __destruct()
		{
			unset( $this->shortcode );
			unset( $this->elements );
			unset( $this->callback_settings );
			unset( $this->container_styles );
			unset( $this->container_classes );
			unset( $this->style_selectors );
		}
		
		/**
		 * Scans the element for attributes that have a callback and executes to create them for the shortcode
		 * 
		 * @since 4.8.4
		 * @param array $atts
		 * @param boolean $is_modal_item
		 */
		public function create_callback_styles( array &$atts, $is_modal_item = false ) 
		{
			$this->set_elements( $is_modal_item );
			
			foreach( $this->elements as &$element ) 
			{
				if( ! isset( $element['styles_cb'] ) || ! is_array( $element['styles_cb'] ) || ! isset( $element['styles_cb']['method'] ) )
				{
					continue;
				}
				
				if( ! method_exists( $this, $element['styles_cb']['method'] ) )
				{
					continue;
				}
				
				call_user_func( array( $this, $element['styles_cb']['method'] ), $element, $atts );
			}
		}
		
		/**
		 * Adds a styles array to the corresponding container styles.
		 * If $skip_empty != 'skip_empty' empty strings are also added.
		 * 
		 * @since 4.8.4
		 * @param string $container
		 * @param array $styles
		 * @param false|string $skip_empty				'skip_empty' | 'no_skip_empty' | false
		 */
		public function add_styles( $container, array $styles, $skip_empty = 'skip_empty' ) 
		{
			if( ! isset( $this->container_styles[ $container ] ) )
			{
				$this->container_styles[ $container ] = array();
			}
			
			if( 'skip_empty' == $skip_empty )
			{
				$styles = array_filter( $styles, function( $value ) { return ( ! is_null( $value ) && $value !== '' ); } );
			}
			
			if( empty( $styles ) )
			{
				return;
			}
			
			$this->container_styles[ $container ] = array_merge( $this->container_styles[ $container ], $styles );
		}
		
		/**
		 * Adds styles if a container has styles already
		 * 
		 * @since 4.8.4
		 * @param type $container_id
		 * @param array $styles
		 */
		public function add_styles_conditionally( $container_id, array $styles ) 
		{
			if( $this->has_styles( $container_id ) )
			{
				$this->add_styles( $container_id, $styles );
			}
		}
		
		/**
		 * Adds a single class or a class array to the corresponding container array.
		 * 
		 * @since 4.8.4
		 * @param string $container
		 * @param string|array $classes
		 */
		public function add_classes( $container, $classes ) 
		{
			if( ! isset( $this->container_classes[ $container ] ) )
			{
				$this->container_classes[ $container ] = array();
			}
			
			if( ! is_array( $classes ) )
			{
				$classes = array( $classes );
			}
			
			$filtered = array_filter( $classes );
			
			if( empty( $filtered ) )
			{
				return;
			}
			
			$this->container_classes[ $container ] = array_merge( $this->container_classes[ $container ], $filtered );
		}
		
		/**
		 * Checks if key exists in $source and adds value to $container
		 * 
		 * @since 4.8.4
		 * @param string $container
		 * @param array $source
		 * @param string|array $key
		 */
		public function add_classes_from_array( $container, array $source, $key ) 
		{
			if( empty( $key ) )
			{
				return;
			}
			
			if( ! is_array( $key ) )
			{
				$key = array( $key );
			}
			
			$add = array();
			
			foreach( $key as $index ) 
			{
				if( isset( $source[ $index ] ) && ! empty( $source[ $index ] ) )
				{
					$add[] = $source[ $index ];
					
				}
			}
			
			if( ! empty( $add ) )
			{
				$this->add_classes( $container, $add );
			}
		}


		/**
		 * Returns the class string for a given container
		 * 
		 * @since 4.8.4
		 * @param string $container
		 * @return string
		 */
		public function get_class_string( $container ) 
		{
			if( ! isset( $this->container_classes[ $container ] ) || !is_array( $this->container_classes[ $container ] ) )
			{
				return '';
			}
			
			return implode( ' ', $this->container_classes[ $container ] );
		}
		
		/**
		 * Add containers and the selectors to style_selectors array.
		 * Selectors and containers must be unique and valid array keys.
		 * Containers are added to already existing selectors.
		 * 
		 * @since 4.8.4
		 * @param array $selectors
		 */
		public function add_selectors( array $selectors )
		{
			$sel = array_flip( $selectors );
			
			foreach( $sel as $selector => $container ) 
			{
				if( ! isset( $this->style_selectors[ $selector ] ) )
				{
					$this->style_selectors[ $selector ] = array();
				}
				
				if( ! is_array( $container ) )
				{
					$container = array( $container );
				}
				
				$this->style_selectors[ $selector ] = array_merge( $this->style_selectors[ $selector ], $container );
			}
		}

		/**
		 * Adds callback styles to a style container
		 * 
		 * @since 4.8.4
		 * @param string $container
		 * @param array $callback_id
		 */
		public function add_callback_styles( $container, array $callback_id ) 
		{
			$this->add_callback_data( $container, 'styles', $callback_id );
		}
		
		/**
		 * Adds callback classes to a class container
		 * 
		 * @since 4.8.4
		 * @param string $container
		 * @param array $callback_id
		 */
		public function add_callback_classes( $container, array $callback_id ) 
		{
			$this->add_callback_data( $container, 'classes', $callback_id );
		}
		
		/**
		 * Adds callback data to a container
		 * 
		 * @since 4.8.4
		 * @param string $container
		 * @param string $source				'styles' | 'classes'
		 * @param array $callback_id
		 */
		protected function add_callback_data( $container, $source, array $callback_id ) 
		{
			if( empty( $callback_id ) )
			{
				return;
			}
			
			if( ! in_array( $source, array( 'styles' , 'classes' ) ) )
			{
				return;
			}
			
			$data = $this->get_callback_data_array( $source, $callback_id );
			
			if( 'styles' == $source )
			{
				$this->add_styles( $container, $data );
			}
			else
			{
				$this->add_classes( $container, $data );
			}
		}


		/**
		 * Returns the stored styles or classes array for the id.
		 * Structure and content depends on the element type
		 * 
		 * @since 4.8.4
		 * @param string $id
		 * @param string $what				'styles' | 'classes'
		 * @return array
		 */
		public function get_callback_settings( $id, $what = 'styles' ) 
		{
			if( ! isset( $this->callback_settings[ $id ] ) )
			{
				return array();
			}
			
			return isset( $this->callback_settings[ $id ][ $what ] ) ? $this->callback_settings[ $id ][ $what ] : array();
		}
		
		/**
		 * Extracts the styles or classes for the given id's and returns them in an array
		 * 
		 * @since 4.8.4
		 * @param string $source				'styles' | 'classes'
		 * @param string|array $ids
		 * @return array
		 */
		public function get_callback_data_array( $source, ...$ids ) 
		{
			$attr = array();
			
			foreach ( $ids as $id ) 
			{
				if( is_array( $id ) )
				{
					$a = array();
					 
					foreach( $id as $id_sub ) 
					{
						$sub = $this->get_callback_data_array( $source, $id_sub );
						$a = array_merge( $a, $sub );
					}
					
					$attr = array_merge( $attr, $a );
					continue;
				}
				
				$data = $this->get_callback_settings( $id, $source );
				
				foreach( $data as $key => $value ) 
				{
					if( 'styles' == $source )
					{
						$attr[ $key ] = $value;
					}
					else
					{
						$attr[] = $value;
					}
				}
			}
			
			return $attr;
		}

		/**
		 * Returns a final style string to add to HTML. Accepts several style arrays and merges them to a single string
		 * 
		 * @since 4.8.4
		 * @param array ...$styles_arrays
		 * @return string
		 */
		public function inline_style_string( array ...$styles_arrays ) 
		{
			$styles = '';
			
			foreach( $styles_arrays as $styles_array ) 
			{
				foreach( $styles_array as $key => $value ) 
				{
					$styles .= AviaHelper::style_string( $styles_array, $key );
				}
			}
			
			if( ! empty( $styles ) )
			{
				//	wrap in style=""
				$styles = AviaHelper::style_string( $styles );
			}
			
			return $styles;
		}
		
		/**
		 * Returns the complete <style> tag including all selectors added with add_selectors
		 * 
		 * @since 4.8.4
		 * @param string $element_id
		 *  @param string $return				'tag' | 'rules_only'
		 * @return string
		 */
		public function get_style_tag( $element_id, $return = 'tag' ) 
		{
			if( AviaPostCss()->shortcode_styles_processed( $element_id ) )
			{
				return '';
			}
			
			return $this->create_style_tag( $this->style_selectors, $element_id, $return );
		}

		/**
		 * Returns the complete styling rules including all selectors added with add_selectors
		 * 
		 * @since 4.8.4
		 * @param string $element_id
		 * @return string
		 */
		public function get_style_rules() 
		{
			$rules = $this->create_keyframe_rules();
			$rules .= $this->create_style_rules( $this->style_selectors );
			
			return $rules;
		}
		
		
		/**
		 * Returns the rules for a given selectors array.
		 * Rules containing '' are removed.
		 * 
		 * @since 4.8.4
		 * @param array $selectors
		 * @return string
		 */
		public function create_style_rules( $selectors = array() ) 
		{
			$out = '';
			
			if( empty( $selectors ) )
			{
				return $out;
			}
			
			foreach( $selectors as $selector => $container_ids ) 
			{
				$styles = array();
				$rules = array();
				
				if( ! is_array( $container_ids ) )
				{
					$container_ids = array( $container_ids );
				}
				
				foreach( $container_ids as $container_id ) 
				{
					if( ! isset( $this->container_styles[ $container_id ] ) )
					{
						continue;
					}

					$rules = array_merge( $rules, $this->container_styles[ $container_id ] );
				}
			
				if( empty( $rules ) )
				{
					continue;
				}
				
				foreach( $rules as $key => $value ) 
				{
					$r = trim( AviaHelper::style_string( $rules, $key ) );
					if( '' != $r )
					{
						$styles[] = $r;
					}
				}
			
				if( empty( $styles ) )
				{
					continue;
				}
			
				$out .= $selector . '{' . $this->new_ln . implode( $this->new_ln, $styles ) . $this->new_ln . '}' . $this->new_ln;
			}
			
			return $out;
		}
		
		/**
		 * Returns all the keyframes
		 * 
		 * @since 4.8.4
		 * @return string
		 */
		public function create_keyframe_rules() 
		{
			$rules = '';
			
			foreach( $this->callback_settings as $setting ) 
			{
				$keyframes = \AviaHelper::array_value( $setting, 'keyframes', array() );
				if( empty( $keyframes ) )
				{
					continue;
				}
				
				$rules .= implode( $this->new_ln, $keyframes ) . $this->new_ln;
			}
			
			return $rules;
		}

		/**
		 * Returns the complete <style> tag a given selector array
		 * 
		 * @since 4.8.4
		 * @param array $selectors
		 * @param string $tag_id
		 * @param string $return				'tag' | 'rules_only'
		 * @return string
		 */
		public function create_style_tag( $selectors = array(), $tag_id = '', $return = 'tag' )
		{
			$rules = $this->create_keyframe_rules();
			$rules .= $this->create_style_rules( $selectors );
			
			if( empty( $rules ) )
			{
				return '';
			}
			
			if( 'rules_only' == $return )
			{
				return $rules;
			}
			
			return $this->style_tag_html( $rules, $tag_id );
		}
		
		/**
		 * Creates the <style> html for given rule string
		 * 
		 * @since 4.8.4
		 * @param string $rules
		 * @param string $tag_id
		 * @return string
		 */
		public function style_tag_html( $rules, $tag_id = '' ) 
		{
			if( empty( $rules ) )
			{
				return '';
			}
			
			$id = ! empty( $tag_id ) ? 'id="style-css-' . $tag_id . '"' : '';
			$out = $this->new_ln . '<style type="text/css" ' . $id . '>'. $this->new_ln . $rules . '</style>' . $this->new_ln;
			
			return $out;
		}

		/**
		 * Filter the elements to scan
		 * 
		 * @since 4.8.4
		 * @param boolean $is_modal_item
		 */
		protected function set_elements( $is_modal_item = false )
		{
			$this->is_modal_item = $is_modal_item;
			$this->elements = ! $is_modal_item ? $this->shortcode->elements : $this->shortcode->get_modal_group_subelements();
		}
	
		/**
		 * Checks if style exist for a given container
		 * 
		 * 
		 * @param string $container_id
		 * @return boolean
		 */
		public function has_styles( $container_id ) 
		{
			return isset( $this->container_styles[ $container_id ] ) && ! empty( $this->container_styles[ $container_id ] );
		}

	}
}
