<?php
/**
 * This class provides methods for managing uploaded SVG files that can be used as dividers.
 * 
 * 
 * @author		Günter
 * @since 4.8.4
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( 'aviaSvgShapes' ) )
{

	class aviaSvgShapes
	{
		/**
		 * Holds the instance of this class
		 * 
		 * @since 4.8.4
		 * @var aviaSvgShapes 
		 */
		static private $_instance = null;
		
		/**
		 * Native shapes included with theme
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		protected $native_shapes;
		
		/**
		 * Holds a list of all available shapes - etended with user registered shapes
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		protected $shapes;
		
	
		/**
		 * Filterable path to native svg files
		 * 
		 * @since 4.8.4
		 * @var string
		 */
		protected $native_files_dir;
		
		/**
		 * Filterable full path including / to directory for custom svg files.
		 * Defaults to ../uploads/avia_shapes/
		 * 
		 * @since 4.8.4
		 * @var string
		 */
		protected $custom_files_dir;
		
		/**
		 * Cache of already loaded svg file content
		 * 
		 *		'id'	=> file_content
		 * 
		 * for negative 'id' will be 'id-negative'
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		protected $svg_cache;
		
		/**
		 * Return the instance of this class
		 * 
		 * @since 4.8.4
		 * @return aviaSvgShapes
		 */
		static public function instance()
		{
			if( is_null( aviaSvgShapes::$_instance ) )
			{
				aviaSvgShapes::$_instance = new aviaSvgShapes();
			}
			
			return aviaSvgShapes::$_instance;
		}
		
		/**
		 * @since 4.8.4
		 */
		protected function __construct() 
		{
			$this->init_native_shapes();
			
			$this->shapes = null;
			$this->custom_files_dir = null;
			$this->svg_cache = array();
			
			/**
			 * 
			 * @since 4.8.4
			 * @param string $path
			 * @return string
			 */
			$this->native_files_dir = apply_filters( 'avf_svg_shapes_native_files_directory', Avia_Builder()->paths['pluginPath'] . 'assets/dividers/' );
		}
		
		/**
		 * @since 4.8.4
		 */
		public function __destruct() 
		{
			unset( $this->native_shapes );
			unset( $this->shapes );
			unset( $this->svg_cache );
		}
		
		/**
		 * Structure for a shape:
		 * 
		 *		'key'	=> array(
		 *						'title'					=> string		Name displayed in select box
		 *						'has_negative'			=> true,		if set returns filename-negative.svg
		 *						'has_flip'				=> true,		if rotate 180° makes sense
		 *						'has_width'				=> true,		if you can select a width in %
		 *						'path'					=> string		full path exclude filename where file is located - defaults to native_files_dir or custom_files_dir
		 *						'filename'				=> string		defaults to 'key.svg', .svg is appended by default if missing
		 *						'attachment'			=> int			attachment id of uploaded media
		 *						'attachment_negative'	=> int			attachment id of negative uploaded media
		 *					)
		 * @since 4.8.4
		 * @since 4.8.4.1		added attachment, attachment_negative
		 */
		protected function init_native_shapes()
		{
			$native_shapes = array(
				
						'mountains'	=> array(
										'key'			=> 'mountains',
										'title'			=> __( 'Mountains', 'avia_framework' ),
										'has_flip'		=> true,
										'has_width'		=> true,
									),
						'drops'		=> array(
										'key'			=> 'drops',
										'title'			=> __( 'Drops', 'avia_framework' ),
										'has_negative' => true,
										'has_flip'		=> true
									),
						'clouds'	=> array(
										'key'			=> 'clouds',
										'title'			=> __( 'Clouds', 'avia_framework' ),
										'has_negative' => true,
										'has_flip'		=> true
									),
						'zigzag'	=> array(
										'key'			=> 'zigzag',
										'title'			=> __( 'Zigzag', 'avia_framework' ),
										'has_width'		=> true,
									),
						'pyramids'	=> array(
										'key'			=> 'pyramids',
										'title'			=> __( 'Pyramids', 'avia_framework' ),
										'has_negative'	=> true,
										'has_flip'		=> true,
										'has_width'		=> true,
									),
						'triangle'	=> array(
										'key'			=> 'triangle',
										'title'			=> __( 'Triangle', 'avia_framework' ),
										'has_negative'	=> true,
										'has_width'		=> true,
									),
						'triangle-asymmetrical'	=> array(
										'key'			=> 'triangle-asymmetrical',
										'title'			=> __( 'Triangle Asymmetrical', 'avia_framework' ),
										'has_negative'	=> true,
										'has_flip'		=> true,
										'has_width'		=> true,
									),
						'tilt'	=> array(
										'key'			=> 'tilt',
										'title'			=> __( 'Tilt', 'avia_framework' ),
										'has_flip'		=> true,
									),
						'opacity-tilt'	=> array(
										'key'			=> 'opacity-tilt',
										'title'			=> __( 'Tilt Opacity', 'avia_framework' ),
										'has_flip'		=> true,
										'has_width'		=> true,
									),
						'opacity-fan'	=> array(
										'key'			=> 'opacity-fan',
										'title'			=> __( 'Fan Opacity', 'avia_framework' ),
										'has_width'		=> true,
									),
						'curve'	=> array(
										'key'			=> 'curve',
										'title'			=> __( 'Curve', 'avia_framework' ),
										'has_negative'	=> true,
										'has_width'		=> true,
									),
						'curve-asymmetrical'	=> array(
										'key'			=> 'curve-asymmetrical',
										'title'			=> __( 'Curve Asymmetrical', 'avia_framework' ),
										'has_negative'	=> true,
										'has_flip'		=> true,
										'has_width'		=> true,
									),
						'waves'	=> array(
										'key'			=> 'waves',
										'title'			=> __( 'Waves', 'avia_framework' ),
										'has_negative'	=> true,
										'has_flip'		=> true,
										'has_width'		=> true,
									),
						'wave-brush'	=> array(
										'key'			=> 'wave-brush',
										'title'			=> __( 'Waves Brush', 'avia_framework' ),
										'has_flip'		=> true,
										'has_width'		=> true,
									),
						'waves-pattern'	=> array(
										'key'			=> 'waves-pattern',
										'title'			=> __( 'Waves Pattern', 'avia_framework' ),
										'has_flip'		=> true,
										'has_width'		=> true,
									),
						'arrow'	=> array(
										'key'			=> 'arrow',
										'title'			=> __( 'Arrow', 'avia_framework' ),
										'has_negative'	=> true,
										'has_width'		=> true,
									),
						'split'	=> array(
										'key'			=> 'split',
										'title'			=> __( 'Split', 'avia_framework' ),
										'has_negative'	=> true,
										'has_width'		=> true,
									),
						'book'	=> array(
										'key'			=> 'book',
										'title'			=> __( 'Book', 'avia_framework' ),
										'has_negative'	=> true,
										'has_width'		=> true,
									)
				);
			
			/**
			 * Extend the native shapes. Make sure to add 'path' attribute otherwise the files will not be found if not located in default folder !!
			 * 
			 * @since 4.8.4
			 * @param array $native_shapes
			 * @rturn array
			 */
			$this->native_shapes = apply_filters( 'avf_native_svg_shapes', $native_shapes );
		}
		
		/**
		 * Returns all shapes, a requested shape or false, if $key does not exist.
		 * 
		 * @since 4.8.4
		 * @param string|null $key
		 * @return array|false
		 */
		public function get_shapes( $key = null ) 
		{
			if( is_null( $this->shapes ) )
			{
				$this->init_shapes();
			}
			
			if( is_null( $key ) )
			{
				return $this->shapes;
			}
			
			return isset( $this->shapes[ $key ] ) ? $this->shapes[ $key ] : false;
		}
		
		/**
		 * Initialise svg shapes and merge native shapes and user shapes with a filter.
		 * Does not check, if the files exist.
		 * 
		 * @since 4.8.4
		 */
		protected function init_shapes() 
		{
			/**
			 * Add your custom shapes. For structure of array see init_native_shapes().
			 * Make sure to return a valid array and that the files exist and has a similar structure like the native files.
			 * 
			 * @since 4.8.4
			 * @param array
			 * @return array
			 */
			$user_shapes = apply_filters( 'avf_custom_svg_shapes', array() );
			
			$this->shapes = array_merge( $this->native_shapes, $user_shapes );
		}
		
		/**
		 * Return attribute value of a shape - null if not exist
		 * 
		 * @since 4.8.4
		 * @param string $shape
		 * @param string $attribute
		 * @return null|mixed
		 */
		public function get_attribute( $shape, $attribute )
		{
			$shapes = $this->get_shapes();
			
			if( ! isset( $shapes[ $shape ] ) || ! isset( $shapes[ $shape ][ $attribute ] ) )
			{
				return false;
			}
			
			return $shapes[ $shape ][ $attribute ];
		}


		/**
		 * Returns the selectbox content for modal popup
		 * 
		 * @since 4.8.4
		 * @return array
		 */
		public function modal_popup_select_dividers() 
		{
			$select = array();
			
			$shapes = $this->get_shapes();
			
			foreach( $shapes as $key => $shape ) 
			{
				$ui_key = $this->is_native( $key ) ? $shape['title'] : $shape['title'] . ' ' . __( '(Custom SVG)', 'avia_framework' );
				$select[ $ui_key ] = $key;
			}
			
			ksort( $select );
			
			$no = array(
						__( 'No divider', 'avia_framework' )	=> ''
					);
			
			$select = array_merge( $no, $select );
			
			/**
			 * @since 4.8.4
			 * @param array $select
			 * @return array
			 */
			return apply_filters( 'avf_modal_select_svg_shapes', $select );
		}
		
		/**
		 * Returns the required array for various input fields
		 * 
		 * @since 4.8.4
		 * @param string $what
		 * @param string $id
		 * @return array
		 */
		public function modal_popup_required( $what, $id ) 
		{
			$shapes = $this->get_shapes();
			
			$found = array();
			
			foreach( $shapes as $key => $shape )
			{
				switch( $what )
				{
					case 'width':
						$check = 'has_width';
						break;
					case 'flip':
						$check = 'has_flip';
						break;
					case 'invert':
						$check = 'has_negative';
						break;
					default:
						continue 2;
				}
				
				if( isset( $shape[ $check ] ) && true === $shape[ $check ] )
				{
					$found[] = $key;
				}
			}
			
			if( empty( $found ) )
			{
				return $found;
			}
			
			return array( $id, 'parent_in_array', implode( ',', $found ) );
		}
		
		/**
		 * Checks if a shape key is a native shape or not
		 * 
		 * @since 4.8.4
		 * @param string $key
		 * @return boolean
		 */
		public function is_native( $key ) 
		{
			return isset( $this->native_shapes[ $key ] );
		}
		
		/**
		 * Returns if a shape has an inverted file
		 * 
		 * @since 4.8.4
		 * @param string $key
		 * @return boolean
		 */
		public function can_invert( $key ) 
		{
			$shape = $this->get_shapes( $key );
			
			if( false === $shape )
			{
				return false;
			}
			
			return isset( $shape['has_negative'] ) && true === $shape['has_negative'];
		}
		
		/**
		 * Returns if a shape has can flip
		 * 
		 * @since 4.8.4
		 * @param string $key
		 * @return boolean
		 */
		public function can_flip( $key ) 
		{
			$shape = $this->get_shapes( $key );
			
			if( false === $shape )
			{
				return false;
			}
			
			return isset( $shape['has_flip'] ) && true === $shape['has_flip'];
		}
		
		/**
		 * Returns if a shape supports the width option
		 * 
		 * @since 4.8.4
		 * @param string $key
		 * @return boolean
		 */
		public function supports_width( $key ) 
		{
			$shape = $this->get_shapes( $key );
			
			if( false === $shape )
			{
				return false;
			}
			
			return isset( $shape['has_width'] ) && true === $shape['has_width'];
		}

		/**
		 * Returns the filtered subdirectories below WP uploads directory that holds the css files for a post
		 * 
		 * @since 4.8.4
		 * @return string
		 */
		public function custom_files_directory() 
		{
			if( is_null( $this->custom_files_dir ) )
			{
				$wp_upload_dir = wp_upload_dir();
				
				$dir = trailingslashit( trailingslashit( $wp_upload_dir['basedir'] ) . 'avia_custom_shapes' );
				
				/**
				 * @since 4.8.4
				 * @param string $dir
				 * @return string
				 */
				$dir = trailingslashit( apply_filters( 'avf_custom_svg_shapes_files_directory', $dir ) );
				
				$this->custom_files_dir = str_replace( '\\', '/', $dir );
			}
			
			return $this->custom_files_dir;
		}
		
		
		/**
		 * Returns the path to a given shape
		 * 
		 * @since 4.8.4
		 * @param array $shape
		 * @return string
		 */
		public function get_file_path( array $shape ) 
		{
			if( ! empty( $shape['path'] ) )
			{
				return trailingslashit( $shape['path'] );
			}

			if( $this->is_native( $shape['key'] ) )
			{
				return $this->native_files_dir;
			}
			
		    return $this->custom_files_directory();
		}
		
		/**
		 * Returns the filename of the svg
		 * 
		 * @since 4.8.4.1
		 * @param array $shape
		 * @param string $invert
		 * @return string
		 */
		protected function get_attachment_filename( array $shape, $invert ) 
		{
			if( ! empty( $invert ) )
			{
				$id = ! empty( $shape['attachment_negative'] ) ? $shape['attachment_negative'] : false;
			}
			else 
			{
				$id = ! empty( $shape['attachment'] ) ? $shape['attachment'] : false;
			}
			
			$id = 1958;
					
			if( false === $id )
			{
				return false;
			}
			
			$file = get_attached_file( $id );
			
			if( false === stripos( $file, '.svg' ) )
			{
				return false;
			}
			
			return $file;
		}
		
		/**
		 * Returns the filename of the svg
		 * 
		 * @since 4.8.4
		 * @param array $shape
		 * @param string $invert
		 * @return string
		 */
		protected function get_file_name( array $shape, $invert )
		{
			$fn = empty( $shape['filename'] ) ? $shape['key'] : $shape['filename'];
			
			if( empty( $invert ) )
			{
				if( false === stripos( $fn, '.svg' ) )
				{
					$fn .= '.svg';
				}
				
				return $fn;
			}
			
			if( false !== stripos( $fn, '.svg' ) )
			{
				$fn = str_replace( '.svg', '-negative.svg', $fn );
			}
			else 
			{
				$fn .= '-negative.svg';
			}
			
			return $fn;
		}
		
		/**
		 * Get the svg HTML and return in correct wrapped divs and classes
		 * 
		 * @since 4.8.4
		 * @param array $atts
		 * @param aviaElementStyling $element_styling
		 * @param string $which							'both' | 'top' | 'bottom'
		 * @return string
		 */
		public function get_svg_dividers( $atts, aviaElementStyling $element_styling, $which = 'both' )
		{
			if( empty( $atts['svg_div_top'] ) && empty( $atts['svg_div_bottom'] ) )
			{
				return '';
			}
			
			$output  = '';
			
			if( in_array( $which, array( 'both', 'top' ) ) )
			{
				$args = array(
							'key'		=> $atts['svg_div_top'],
							'invert'	=> $atts['svg_div_top_invert'],
						);

				$svg = $this->get_svg_html( $args );

				if( false !== $svg )
				{
					$container_class = $element_styling->get_class_string( 'divider-top' );

					$output .= "<div class='{$container_class}'>";
					$output .=		$svg;
					$output .= '</div>';
				}
			}
			
			if( in_array( $which, array( 'both', 'bottom' ) ) )
			{
				$args = array(
							'key'		=> $atts['svg_div_bottom'],
							'invert'	=> $atts['svg_div_bottom_invert'],
						);

				$svg = $this->get_svg_html( $args );

				if( false !== $svg )
				{
					$container_class = $element_styling->get_class_string( 'divider-bottom' );

					$output .= "<div class='{$container_class}'>";
					$output .=		$svg;
					$output .= '</div>';
				}
			}
			
			return $output;
		}


		/**
		 * Returns the inline HTML for a given shape. The file content is returned 1:1,
		 * there is no check for validity. So make sure you provide a correct svg file html.
		 * 
		 * @since 4.8.4
		 * @param array $params
		 * @return string|false
		 */
		public function get_svg_html( array $params ) 
		{
			$default = array(
						'key'		=> '',
						'invert'	=> ''
					);
			
			$params = array_merge( $default, $params );
			
			if( empty( $params['key'] ) )
			{
				return false;
			}
			
			if( ! $this->can_invert( $params['key'] ) )
			{
				$params['invert'] = '';
			}
			
			$cached = $this->get_cache( $params );
			if( false !== $cached )
			{
				return $cached;
			}
			
			$shape = $this->get_shapes( $params['key'] );
			if( false === $shape )
			{
				return false;
			}
			
			$filename = $this->get_attachment_filename( $shape, $params['invert'] );
			if( false === $filename )
			{
				$path = $this->get_file_path( $shape );
				$name = $this->get_file_name( $shape, $params['invert'] );
				
				$filename = $path . $name;
			}
			
			$svg = file_get_contents( $filename );
			
			if( false === $svg || empty( $svg ) )
			{
				return false;
			}
			
			$svg = trim( $svg );
			
			$this->save_cache( $params, $svg );
			
			return $svg;
		}
		
		/**
		 * Return cached scg file content
		 * 
		 * @since 4.8.4
		 * @param array $params
		 * @return string
		 */
		protected function get_cache( array $params ) 
		{
			$key = empty( $params['invert'] ) ? $params['key'] : $params['key'] . '-negative';
			
			return isset( $this->svg_cache[ $key ] ) ? $this->svg_cache[ $key ] : false;
		}
		
		/**
		 * Save svg content to cache
		 * 
		 * @since 4.8.4
		 * @param array $params
		 * @param string $svg_content
		 */
		protected function save_cache( array $params, $svg_content )
		{
			$key = empty( $params['invert'] ) ? $params['key'] : $params['key'] . '-negative';
			
			$this->svg_cache[ $key ] = $svg_content;
		}
		
	}
	
	
	/**
	 * Returns the main instance of aviaSvgShapes to prevent the need to use globals
	 * 
	 * @since 4.8.4
	 * @return aviaSvgShapes
	 */
	function AviaSvgShapes() 
	{
		return aviaSvgShapes::instance();
	}
	
}

