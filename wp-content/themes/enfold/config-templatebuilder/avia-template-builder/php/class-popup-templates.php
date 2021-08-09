<?php
/**
 * Class defines option templates for ALB elements
 * These templates replace an element in the options array.
 * Nested templates are supported.
 * 
 * Basic structure, not all arguments are supported by every template element (example):
 * 
 *			array(	
 *						'type'					=> 'template',
 *						'template_id'			=> 'date_query',
 *						'required'				=> ! isset() | array()     //	used for all elements
 *						'template_required'		=> array( 
 *														0	=> array( 'slide_type', 'is_empty_or', 'entry-based' )
 *													),
 *						'content'				=> ! isset() | array( array of elements - can be templates also )
 *						'templates_include'		=> ! isset() | array( list of needed subtemplates ),
 *						'subtype'				=> mixed					//	allows to change subtype e.g. for select boxes
 *						'args'					=> mixed					//	e.g. shortcode class
 *													
 *					),
 * 
 * Also allows to store HTML code snippets (can be used in editor elements like e.g. 'element streches/fullwidth').
 * 
 * @added_by Günter
 * @since 4.5.7.1
 * @since 4.6.4			supports dynamic added templates
 * @since 4.8.4			moved basic methods to base class
 */

if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if( ! class_exists( 'Avia_Popup_Templates' ) )
{
	
	class Avia_Popup_Templates extends aviaBuilder\base\aviaPopupTemplatesCallback
	{
		
		/**
		 * Holds the instance of this class
		 * 
		 * @since 4.5.7.1
		 * @var Avia_Popup_Templates 
		 */
		static private $_instance = null;
		

		/**
		 * Return the instance of this class
		 * 
		 * @since 4.5.7.1
		 * @return Avia_Popup_Templates
		 */
		static public function instance()
		{
			if( is_null( Avia_Popup_Templates::$_instance ) )
			{
				Avia_Popup_Templates::$_instance = new Avia_Popup_Templates();
			}
			
			return Avia_Popup_Templates::$_instance;
		}
		
		/**
		 * @since 4.5.7.1
		 */
		protected function __construct()
		{
			parent::__construct();
			
			$this->set_predefined_html_templates();
			
			/**
			 * Allow 3-rd party to register own templates
			 * 
			 * @since 4.6.4
			 * @param Avia_Popup_Templates $this
			 */
			do_action( 'ava_popup_register_dynamic_templates', $this );
		}
		
		/**
		 * @since 4.6.4
		 */
		public function __destruct() 
		{
			parent::__destruct();
		}
		

		
		/**
		 * Returns a toggle container section.
		 * Content is filled from
		 *		- 'content'
		 *		- 'templates_include'
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array|false
		 */
		protected function toggle_container( array $element )
		{
			$title = ! empty( $element['title'] ) ? $element['title'] : __( 'Click to view content', 'avia_framework' );
			$open = array(
						'type'          => 'toggle_container',
						'nodescription' => true
					);
			
			$close = array(
						'type'          => 'toggle_container_close',
						'nodescription' => true
					);
			
			$content = false;
			if( ! empty( $element['content'] ) )
			{
				$content = $element['content'];
			}
			else if( ! empty( $element['templates_include'] ) )
			{
				$content = $this->get_templates_to_include( $element );
			}
			
			if( empty( $content ) )
			{
				return false;
			}
			
			$result = array_merge( array( $open ), $content, array( $close ) );
			return $result;
		}
		
		/**
		 * Returns a toggle section.
		 * Content is filled from
		 *		- 'content'
		 *		- 'templates_include'
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array|false
		 */
		protected function toggle( array $element )
		{
			$title = ! empty( $element['title'] ) ? $element['title'] : __( 'Click to view content', 'avia_framework' );
			$class = ! empty( $element['container_class'] ) ? $element['container_class'] : '';
			
			$required = ! empty( $element['required'] ) ? $element['required'] : array();
			
			$open = array(
						'type'				=> 'toggle',
						'name'				=> $title,
						'nodescription'		=> true,
						'container_class'	=> $class,
						'required'			=> $required
					);
			
			$close = array(
						'type'          => 'toggle_close',
						'nodescription' => true,
					);
			
			$content = false;
			if( ! empty( $element['content'] ) )
			{
				$content = $element['content'];
			}
			else if( ! empty( $element['templates_include'] ) )
			{
				$content = $this->get_templates_to_include( $element );
			}
			
			if( empty( $content ) )
			{
				return false;
			}
			
			$result = array_merge( array( $open ), $content, array( $close ) );
			return $result;
		}
		
		/**
		 * Returns a font sizes icon switcher section.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function font_sizes_icon_switcher( array $element )
		{
			
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			else
			{
				$subtype = array(
							'default'	=> AviaHtmlHelper::number_array( 8, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
							'medium'	=> AviaHtmlHelper::number_array( 8, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
							'small'		=> AviaHtmlHelper::number_array( 8, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
							'mini'		=> AviaHtmlHelper::number_array( 8, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
						);
			}
			
			if( isset( $element['id_sizes'] ) && is_array( $element['id_sizes'] ) )
			{
				$id_sizes = $element['id_sizes'];
			}
			else
			{
				$id_sizes = array(
							'default'	=> 'size',
							'medium'	=> 'av-medium-font-size',
							'small'		=> 'av-small-font-size',
							'mini'		=> 'av-mini-font-size'
						);
			}
			
			if( isset( $element['desc_sizes'] ) && is_array( $element['desc_sizes'] ) )
			{
				$desc_sizes = $element['desc_sizes']; 
			}
			else
			{
				$desc_sizes = array(
							'default'	=> __( 'Font Size (Default)', 'avia_framework' ),
							'medium'	=> __( 'Font Size for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
							'small'		=> __( 'Font Size for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
							'mini'		=> __( 'Font Size for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
						);
			}
			
			$titles = array(
							'default'	=> __( 'Default', 'avia_framework' ),
							'medium'	=> __( 'Tablet Landscape', 'avia_framework' ),
							'small'		=> __( 'Tablet Portrait', 'avia_framework' ),
							'mini'		=> __( 'Mobile', 'avia_framework' ),
						);
			
			$icons = array(
							'default'	=> 'desktop',
							'medium'	=> 'tablet-landscape',
							'small'		=> 'tablet-portrait',
							'mini'		=> 'mobile'
						);
			
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array(
							array(
								'type' 	=> 'icon_switcher_container',
								'name'  => ! empty( $element['name'] ) ? $element['name'] : '',
								'desc' 	=> ! empty( $element['desc'] ) ? $element['desc'] : '',
//								'icon'  => __( 'Content', 'avia_framework' ),
								'nodescription' => true,
								'required'	=> isset( $element['required'] ) ? $element['required'] : array()
							),	
						
						);
			
			foreach( $id_sizes as $size => $id ) 
			{
				$template[] = array(
								'type' 	=> 'icon_switcher',
								'name'	=> $titles[ $size ],
								'icon'	=> $icons[ $size ],
								'nodescription' => true
							);
				
				$template[] = array(	
								'name'	=> $desc_sizes[ $size ],
								'desc'	=> __( 'Size of the text in px', 'avia_framework' ),
								'id'	=> $id_sizes[ $size],
								'type'	=> 'select',
								'std'	=> '',
								'lockable'	=> $lockable,
								'subtype'	=> $subtype[ $size],
								
							);
				
				$template[] = array(
								'type' 	=> 'icon_switcher_close', 
								'nodescription' => true
						);
			}
			
			$template[] = array(
								'type' 	=> 'icon_switcher_container_close', 
								'nodescription' => true
							);
			
			return $template;
		}
		
		/**
		 * Returns a columns count icon switcher section.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function columns_count_icon_switcher( array $element )
		{
			if( isset( $element['heading'] ) && is_array( $element['heading'] ) )
			{
				$heading = $element['heading'];
			}
			else
			{
				$info  = __( 'Set the column count for this element, based on the device screensize.', 'avia_framework' ) . '<br/><small>';
				$info .= __( 'Please note that changing the default will overwrite any individual &quot;landscape&quot; width settings. Each item will have the same width', 'avia_framework' ) . '</small>';
				
				$heading = array(
								'name' 	=> __( 'Element Columns', 'avia_framework' ),
								'desc' 	=> $info,
								'type' 	=> 'heading',
								'description_class' => 'av-builder-note av-neutral',
							);
			}
			
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			else
			{
				$responsive = array(
									__( 'Use Default', 'avia_framework' )	=> '',
									__( '1 Column', 'avia_framework' )		=> '1',
									__( '2 Columns', 'avia_framework' )		=> '2',
									__( '3 Columns', 'avia_framework' )		=> '3',
									__( '4 Columns', 'avia_framework' )		=> '4'
								);
				
				$subtype = array(
							'default'	=> array(
												__( 'Automatic, based on screen width', 'avia_framework' )	=> 'flexible',
												__( '2 Columns', 'avia_framework' )	=> '2',
												__( '3 Columns', 'avia_framework' )	=> '3',
												__( '4 Columns', 'avia_framework' )	=> '4',
												__( '5 Columns', 'avia_framework' )	=> '5',
												__( '6 Columns', 'avia_framework' )	=> '6'
											),
							'medium'	=> $responsive,		
							'small'		=> $responsive,	
							'mini'		=> $responsive
						);
			}
			
			if( isset( $element['std'] ) && is_array( $element['std'] ) )
			{
				$std = $element['std'];
			}
			else
			{
				$std = array(
							'default'	=> 'flexible',
							'medium'	=> '',
							'small'		=> '',
							'mini'		=> ''
						);
			}
			
			if( isset( $element['id_sizes'] ) && is_array( $element['id_sizes'] ) )
			{
				$id_sizes = $element['id_sizes'];
			}
			else
			{
				$id_sizes = array(
							'default'	=> 'columns',
							'medium'	=> 'av-medium-columns',
							'small'		=> 'av-small-columns',
							'mini'		=> 'av-mini-columns'
						);
			}
			
			if( isset( $element['desc_sizes'] ) && is_array( $element['desc_sizes'] ) )
			{
				$desc_sizes = $element['desc_sizes']; 
			}
			else
			{
				$desc_sizes = array(
							'default'	=> __( 'Column count (Default)', 'avia_framework' ),
							'medium'	=> __( 'Column count for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
							'small'		=> __( 'Column count for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
							'mini'		=> __( 'Column count for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
						);
			}
			
			$titles = array(
							'default'	=> __( 'Default', 'avia_framework' ),
							'medium'	=> __( 'Tablet Landscape', 'avia_framework' ),
							'small'		=> __( 'Tablet Portrait', 'avia_framework' ),
							'mini'		=> __( 'Mobile', 'avia_framework' ),
						);
			
			$icons = array(
							'default'	=> 'desktop',
							'medium'	=> 'tablet-landscape',
							'small'		=> 'tablet-portrait',
							'mini'		=> 'mobile'
						);
			
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array();
							
			if( ! empty( $heading ) )
			{
				$template[] = $heading;
			}
			
			$template[] = array(
								'type' 	=> 'icon_switcher_container',
								'name'  => ! empty( $element['name'] ) ? $element['name'] : '',
								'desc' 	=> ! empty( $element['desc'] ) ? $element['desc'] : '',
//								'icon'  => __( 'Content', 'avia_framework' ),
								'nodescription' => true,
								'required'	=> isset( $element['required'] ) ? $element['required'] : array()
							);
			
			
			foreach( $id_sizes as $size => $id ) 
			{
				$template[] = array(
								'type' 	=> 'icon_switcher',
								'name'	=> $titles[ $size ],
								'icon'	=> $icons[ $size ],
								'nodescription' => true
							);
				
				$template[] = array(	
								'name'	=> $desc_sizes[ $size ],
								'desc'	=> __( 'How many columns do you want to use', 'avia_framework' ),
								'id'	=> $id_sizes[ $size ],
								'type'	=> 'select',
								'std'	=> $std[ $size ],
								'lockable'	=> $lockable,
								'subtype'	=> $subtype[ $size ]
							);
				
				$template[] = array(
								'type' 	=> 'icon_switcher_close', 
								'nodescription' => true
						);
			}
			
			$template[] = array(
								'type' 	=> 'icon_switcher_container_close', 
								'nodescription' => true
							);
			
			return $template;
		}
		
		/**
		 * Returns a screen options toggle section.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function screen_options_toggle( array $element )
		{

			$screen = $this->screen_options_tab( $element, false );
			
			$template = array(
							array(
								'type'          => 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Responsive', 'avia_framework' ),
								'content'		=> $screen,
								'nodescription'	=> true
							)
						);
			
			return $template;
		}
		
		/**
		 * Returns a screen options toggle for columns.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function columns_visibility_toggle( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$desc  = __( 'Set the visibility for this element, based on the device screensize.', 'avia_framework' ) . '<br><small>';
			$desc .= __( 'In order to prevent breaking the layout it is only possible to change the visibility settings for columns once they take up the full screen width, which means only on mobile devices', 'avia_framework' ) . '</small>';

			$c = array(
						
						array(
							'name' 	=> __( 'Element Visibility', 'avia_framework' ),
							'desc' 	=> $desc,
							'type' 	=> 'heading',
							'description_class' => 'av-builder-note av-neutral',
						),
								
						array(	
							'name' 	=> __( 'Mobile display', 'avia_framework' ),
							'desc' 	=> __( 'Display settings for this element when viewed on smaller screens', 'avia_framework' ),
							'id' 	=> 'mobile_display',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> $lockable,
							'subtype'	=> array(	
											__( 'Always display', 'avia_framework' )			=> '',
											__( 'Hide on mobile devices', 'avia_framework' )	=> 'av-hide-on-mobile',
										)
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Responsive', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			return $template;
		}
		
		/**
		 * Returns a developer options toggle section.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function developer_options_toggle( array $element )
		{
			$dev = array();
			$shortcode = isset( $element['args']['sc'] ) && $element['args']['sc'] instanceof aviaShortcodeTemplate ? $element['args']['sc'] : null;
			if( is_null( $shortcode ) )
			{
				return $dev;
			}
			
			$nested = isset( $element['args']['nested'] ) ? $element['args']['nested'] : '';
			$visible = $shortcode->get_developer_elements( $dev, $nested );
			if( empty( $dev ) )
			{
				return $dev;
			}
			
			$template = array(
							array(
								'type'          => 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Developer Settings', 'avia_framework' ),
								'content'		=> $dev,
								'nodescription'	=> true,
								'container_class'	=> $visible
							)
						);
			
			return $template;
		}
		
		/**
		 * Element Disabled In Performance Tab Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function element_disabled( array $element )
		{
			$default = __( 'This element is disabled in your theme options. You can enable it in Enfold &raquo; Performance', 'avia_framework' );
			$anchor = ! empty( $element['args']['anchor'] ) ? trim( $element['args']['anchor'] ) : 'goto_performance';
			
			$desc  = ! empty( $element['args']['desc'] ) ? trim( $element['args']['desc'] ) : $default;
			$desc .= '<br/><br/><a target="_blank" href="' . admin_url( 'admin.php?page=avia#' . $anchor ) . '">' . __( 'Enable it here', 'avia_framework' ) . '</a><br/><br/>';
			
			$template = array(
							array(
								'name' 	=> __( 'Element disabled', 'avia_framework' ),
								'desc' 	=> $desc,
								'type' 	=> 'heading',
								'description_class' => 'av-builder-note av-error',
							)
						
				);
			
			return $template;
		}
		
		/**
		 * Video Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function avia_builder_post_type_option( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$desc = __( "Select which post types should be used. Note that your taxonomy will be ignored if you do not select an assign post type. If you don't select post type all registered post types will be used", 'avia_framework' ); 

			$required = isset( $element['required'] ) && is_array( $element['required'] ) ? $element['required'] : array();
			
			$template = array(
							array(
								'name' 	=> __( 'Select Post Type', 'avia_framework' ),
								'desc' 	=> $desc,
								'id' 	=> 'post_type',
								'type' 	=> 'select',
								'std' 	=> '',
								'multiple'	=> 6,
								'lockable'	=> $lockable,
								'required'	=> $required,
								'subtype'	=> AviaHtmlHelper::get_registered_post_type_array()
							)
				);
			
			return $template;
		}
		
		
		/**
		 * Linkpicker Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function linkpicker_toggle( array $element )
		{
			$id = isset( $element['id'] ) ? $element['id'] : 'link';
			$name = ! empty( $element['name'] ) ? $element['name'] : __( 'Text Link?', 'avia_framework' );
			$desc = ! empty( $element['desc'] ) ? $element['desc'] : __( 'Apply a link to the text?', 'avia_framework' );
			$std = ! empty( $element['std'] ) ? $element['std'] : '';
			$required = ! empty( $element['required'] ) ? $element['required'] : array();
			$link_required = ! empty( $element['link_required'] ) ? $element['link_required'] : array( $id, 'not', '' );
			$target_id = isset( $element['target_id'] ) ? $element['target_id'] : 'linktarget';
			$target_std = isset( $element['target_std'] ) ? $element['target_std'] : '';
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$subtype = array();
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			else
			{
				$subtype_keys = ! empty( $element['subtypes'] ) ? $element['subtypes'] : array( 'no', 'manually', 'single', 'taxonomy' );
			
				foreach( $subtype_keys as $key ) 
				{
					switch( $key )
					{
						case 'no':
							$subtype[ __( 'No Link', 'avia_framework' ) ] = '';
							break;
						case 'default':
							$subtype[ __( 'Use Default Link', 'avia_framework' ) ] = 'default';
							break;
						case 'manually':
							$subtype[ __( 'Set Manually', 'avia_framework' ) ] = 'manually';
							break;
						case 'single':
							$subtype[ __( 'Single Entry', 'avia_framework' ) ] = 'single';
							break;
						case 'taxonomy':
							$subtype[ __( 'Taxonomy Overview Page', 'avia_framework' ) ] = 'taxonomy';
							break;
						case 'lightbox':
							$subtype[ __( 'Open in Lightbox', 'avia_framework' ) ] = 'lightbox';
							break;
						default:
							break;
					}
				}
			}
			
			$c = array(
						array(
							'name'		=> $name,
							'desc'		=> $desc,
							'id'		=> $id,
							'type'		=> 'linkpicker',
							'std'		=> $std,
							'fetchTMPL'	=> true,
							'lockable'	=> $lockable,
							'required'	=> $required,
							'subtype'	=> $subtype
						)
				);
			
			if( ! isset( $element['no_target'] ) || true !== $element['no_target'] )
			{
				$c[] = array(
							'name'		=> __( 'Open in new window', 'avia_framework' ),
							'desc'		=> __( 'Do you want to open the link in a new window', 'avia_framework' ),
							'id'		=> $target_id,
							'type'		=> 'select',
							'std'		=> $target_std,
							'lockable'	=> $lockable,
							'required'	=> $link_required,
							'subtype'	=> AviaHtmlHelper::linking_options(),
						);
			}
			
			if( isset( $element['no_toggle'] ) && true === $element['no_toggle'] )
			{
				$template = $c;
			}
			else
			{
				$template = array(
								array(	
									'type'			=> 'template',
									'template_id'	=> 'toggle',
									'title'			=> __( 'Link Settings', 'avia_framework' ),
									'content'		=> $c 
								),
					);
			}
			
			return $template;
		}
		
		/**
		 * Additional Leaflet Map and Leaflet Marker attributes
		 * 
		 * @since 4.8.2
		 * @param array $element
		 * @return array
		 */
		protected function leaflet_attributes_toggle( array $element )
		{
			$id = isset( $element['id'] ) ? $element['id'] : 'add_attr';
			$name = isset( $element['name'] ) ? $element['name'] : __( 'Additional Attributes', 'avia_framework' );
			
			
			$desc  = __( 'Enter additional shortcode attributes here that are not available with predefined options.', 'avia_framework' ) . ' ';
			$desc .= __( 'We recommend to enter each option in a new line like:', 'avia_framework' ) . '<br /><br />';
			$desc .= __( 'option1="value1"', 'avia_framework' ) . '<br />';
			$desc .= __( 'option2="value2"', 'avia_framework' );
			
			$c = array(
						array(
							'name'		=> $name,
							'desc'		=> $desc,
							'id'		=> $id,
							'type'		=> 'textarea',
							'std'		=> '',
						)
				);
			
			$template = array(
								array(	
									'type'			=> 'template',
									'template_id'	=> 'toggle',
									'title'			=> __( 'Additional Shortcode Attributes', 'avia_framework' ),
									'content'		=> $c 
								),
					);
			
			return $template;
		}
		
		
		/**
		 * Video Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function video( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$text = '';
			
			// if self hosted is disabled
			if( avia_get_option( 'disable_mediaelement' ) == 'disable_mediaelement' )
			{
				$text = __( 'Please link to an external video by URL', 'avia_framework' ) . '<br/><br/>' .
						__( 'A list of all supported Video Services can be found on', 'avia_framework' ) .
						" <a target='_blank' href='http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F'>WordPress.org</a>. Youtube videos will display additional info like title, share link, related videos, ...<br/><br/>" .
						__( 'Working examples:', 'avia_framework' ) . '<br/>' .
						'<strong>https://vimeo.com/1084537</strong><br/>' .
						'<strong>https://www.youtube.com/watch?v=G0k3kHtyoqc</strong><br/><br/>'.
						'<strong class="av-builder-note">' . __( 'Using self hosted videos is currently disabled. You can enable it in Enfold &raquo; Performance', 'avia_framework' ) . '</strong><br/>';

			}
			// if youtube/vimeo is disabled
			else if( avia_get_option( 'disable_video' ) == 'disable_video' )
			{
				$text = __( 'Either upload a new video or choose an existing video from your media library', 'avia_framework' ) . '<br/><br/>'.
						__( 'Different Browsers support different file types (mp4, ogv, webm). If you embed an example.mp4 video the video player will automatically check if an example.ogv and example.webm video is available and display those versions in case its possible and necessary','avia_framework' ) . '<br/><br/><strong class="av-builder-note">' .
						__( 'Using external services like Youtube or Vimeo is currently disabled. You can enable it in Enfold &raquo; Performance', 'avia_framework' ) . '</strong><br/>';

			}
			// all video enabled
			else
			{
				$text = __( 'Either upload a new video, choose an existing video from your media library or link to a video by URL', 'avia_framework' ) . '<br/><br/>'.
						__( 'A list of all supported Video Services can be found on', 'avia_framework' ).
						" <a target='_blank' href='http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F'>WordPress.org</a>. YouTube videos will display additional info like title, share link, related videos, ...<br/><br/>".
						__( 'Working examples, in case you want to use an external service:', 'avia_framework' ) . '<br/>'.
						'<strong>https://vimeo.com/1084537</strong><br/>' .
						'<strong>https://www.youtube.com/watch?v=G0k3kHtyoqc</strong><br/><br/>' .
						'<strong>'.__( 'Attention when using self hosted HTML 5 Videos', 'avia_framework' ) . ':</strong><br/>' .
						__( 'Different Browsers support different file types (mp4, ogv, webm). If you embed an example.mp4 video the video player will automatically check if an example.ogv and example.webm video is available and display those versions in case its possible and necessary', 'avia_framework' ) . '<br/>';
			}
			
			
			$template = array();
			$id = ! empty( $element['id'] ) ? $element['id'] :'video';
			$required = ! empty( $element['required'] ) ? $element['required'] : array();
			
			$template[] = array(	
								'name'		=> __( 'Choose Video', 'avia_framework' ),
								'desc'		=> $text,
								'id'		=> $id,
								'type'		=> 'video',
								'title'		=> __( 'Select Video', 'avia_framework' ),
								'button'	=> __( 'Use Video', 'avia_framework' ),
								'std'		=> 'https://',
								'lockable'	=> $lockable,
								'required'	=> $required,
							);
						
			if( ! empty( $element['args']['html_5_urls'] ) )
			{
				$desc = __( 'Either upload a new video, choose an existing video from your media library or link to a video by URL. If you want to make sure that all browser can display your video upload a mp4, an ogv and a webm version of your video.','avia_framework' );

				for( $i = 1; $i <= 2; $i++ )
				{
					$element = $template[0];
					
					$element['id'] = "{$id}_{$i}";
					$element['name'] =  __( 'Choose Another Video (HTML5 Only)', 'avia_framework' );
					$element['desc'] = $desc;
					
					$template[] = $element;
				}
			}
			
			return $template;
		}
		
		/**
		 * Slideshow Video Player Settings Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function slideshow_player( array $element )
		{
			$required = ! empty( $element['required'] ) ? $element['required'] : array();
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array(
							array(	
								'name'		=> __( 'Disable Autoplay', 'avia_framework' ),
								'desc'		=> __( 'Check if you want to disable video autoplay when this slide shows. Autoplayed videos will be muted by default.', 'avia_framework' ) ,
								'id'		=> 'video_autoplay',
								'type'		=> 'checkbox',
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> $required
							),
				
							array(	
								'name'	=> __( 'Hide Video Controls', 'avia_framework' ),
								'desc'		=> __( 'Check if you want to hide the controls (works for youtube and self hosted videos)', 'avia_framework' ) ,
								'id'		=> 'video_controls',
								'type'		=> 'checkbox',
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> $required,
							),

							array(	
								'name'		=> __( 'Mute Video Player', 'avia_framework' ),
								'desc'		=> __( 'Check if you want to mute the video', 'avia_framework' ) ,
								'id'		=> 'video_mute',
								'type'		=> 'checkbox',
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> $required,
							),

							array(	
								'name'		=> __( 'Loop Video Player', 'avia_framework' ),
								'desc'		=> __( 'Check if you want to loop the video (instead of showing the next slide the video will play from the beginning again)', 'avia_framework' ) ,
								'id'		=> 'video_loop',
								'type'		=> 'checkbox',
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> $required,
							)
				);
			
			return $template;
		}
		
		/**
		 * Slideshow Fallback Image Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function slideshow_fallback_image( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array(
			
							array(	
								'name'		=> __( 'Choose a preview/fallback image', 'avia_framework' ),
								'desc'		=> __( 'Either upload a new, or choose an existing image from your media library', 'avia_framework' ) . '<br/><small>' . __( "Video on most mobile devices can't be controlled properly with JavaScript, so you can upload a fallback image which will be displayed instead. This image is also used if lazy loading is active.", 'avia_framework' ) . '</small>',
								'id'		=> 'mobile_image',
								'type'		=> 'image',
								'fetch'		=> 'id',
								'title'		=> __( 'Choose Image', 'avia_framework' ),
								'button'	=> __( 'Choose Image','avia_framework' ),
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> array( 'slide_type', 'equals', 'video' ),
							),
									
							array(	
								'name'		=> __( 'Mobile Fallback Image Link', 'avia_framework' ),
								'desc'		=> __( 'You can enter a link to a video on youtube or vimeo that will open in a lightbox when the fallback image is clicked by the user. Links to self hosted videos will be opened in a new browser window on your mobile device or tablet', 'avia_framework' ),
								'id'		=> 'fallback_link',
								'type'		=> 'input',
								'std'		=> 'https://',
								'lockable'	=> $lockable,
								'required'	=> array( 'mobile_image', 'not', '' ),
							)
					);
			
			return $template;
		}
		
		/**
		 * Slideshow Overlay Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function slideshow_overlay( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array(
							array(	
								'name' 	=> __( 'Enable Overlay?', 'avia_framework' ),
								'desc' 	=> __( 'Check if you want to display a transparent color and/or pattern overlay above your slideshow image/video', 'avia_framework' ),
								'id' 	=> 'overlay_enable',
								'type' 	=> 'checkbox',
								'std' 	=> '',
								'lockable'	=> $lockable
							),

							array(
								'name' 	=> __( 'Overlay Opacity', 'avia_framework' ),
								'desc' 	=> __( 'Set the opacity of your overlay: 0.1 is barely visible, 1.0 is opaque ', 'avia_framework' ),
								'id' 	=> 'overlay_opacity',
								'type' 	=> 'select',
								'std' 	=> '0.5',
								'lockable'	=> $lockable,
								'required'	=> array( 'overlay_enable', 'not', '' ),
								'subtype'	=> array(   
													__( '0.1', 'avia_framework' )	=> '0.1',
													__( '0.2', 'avia_framework' )	=> '0.2',
													__( '0.3', 'avia_framework' )	=> '0.3',
													__( '0.4', 'avia_framework'	)	=> '0.4',
													__( '0.5', 'avia_framework' )	=> '0.5',
													__( '0.6', 'avia_framework' )	=> '0.6',
													__( '0.7', 'avia_framework' )	=> '0.7',
													__( '0.8', 'avia_framework' )	=> '0.8',
													__( '0.9', 'avia_framework' )	=> '0.9',
													__( '1.0', 'avia_framework' )	=> '1',
												)
							),

							array(
								'name' 	=> __( 'Overlay Color', 'avia_framework' ),
								'desc' 	=> __( 'Select a custom color for your overlay here. Leave empty if you want no color overlay', 'avia_framework' ),
								'id' 	=> 'overlay_color',
								'type' 	=> 'colorpicker',
								'std' 	=> '',
								'lockable'	=> $lockable,
								'required'	=> array( 'overlay_enable', 'not', '' )
							),

							array(
								'name'		=> __( 'Background Image', 'avia_framework'),
								'desc'		=> __( 'Select an existing or upload a new background image', 'avia_framework'),
								'id'		=> 'overlay_pattern',
								'type'		=> 'select',
								'folder'	=> 'images/background-images/',
								'folderlabel'	=> '',
								'group'		=> 'Select predefined pattern',
								'exclude'	=> array( 'fullsize-', 'gradient' ),
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> array( 'overlay_enable', 'not', '' ),
								'subtype'	=> array(
													__( 'No Background Image', 'avia_framework')	=> '',
													__( 'Upload custom image', 'avia_framework')	=> 'custom'
												)
							),

							array(
								'name'		=> __( 'Custom Pattern', 'avia_framework' ),
								'desc'		=> __( 'Upload your own seamless pattern', 'avia_framework' ),
								'id'		=> 'overlay_custom_pattern',
								'type'		=> 'image',
								'fetch'		=> 'url',
								'secondary_img' => true,
								'title'		=> __( 'Insert Pattern', 'avia_framework' ),
								'button'	=> __( 'Insert', 'avia_framework' ),
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> array( 'overlay_pattern', 'equals', 'custom' )
							)
				
				);

			return $template;
		}
		
		/**
		 * Slideshow Buttons Link Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function slideshow_button_links( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array(
				
					array(	
							'name' 	=> __( 'Apply a link or buttons to the slide?', 'avia_framework' ),
							'desc' 	=> __( "You can choose to apply the link to the whole image or to add 'Call to Action Buttons' that get appended to the caption", 'avia_framework' ),
							'id' 	=> 'link_apply',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> $lockable,
							'subtype'	=> array(
												__( 'No Link for this slide', 'avia_framework' ) => '',
												__( 'Apply Link to Image', 'avia_framework' )	=> 'image',
												__( 'Attach one button', 'avia_framework' )		=> 'button',
												__( 'Attach two buttons', 'avia_framework' )	=> 'button button-two'
											)
					),

					array(	
							'name' 	=> __( 'Image Link?', 'avia_framework' ),
							'desc' 	=> __( 'Where should the Image link to?', 'avia_framework' ),
							'id' 	=> 'link',
							'type'		=> 'linkpicker',
							'fetchTMPL'	=> true,
							'std'		=> '',
							'lockable'	=> $lockable,
							'required'	=> array( 'link_apply', 'equals', 'image' ),
							'subtype'	=> array(	
												__( 'Open Image in Lightbox', 'avia_framework' )	=> 'lightbox',
												__( 'Set Manually', 'avia_framework' )				=> 'manually',
												__( 'Single Entry', 'avia_framework' )				=> 'single',
												__( 'Taxonomy Overview Page', 'avia_framework' )	=> 'taxonomy',
											),
							
					),
							
					array(	
							'name' 	=> __( 'Open Link in new Window?', 'avia_framework' ),
							'desc' 	=> __( 'Select here if you want to open the linked page in a new window', 'avia_framework' ),
							'id' 	=> 'link_target',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> $lockable,
							'required'	=> array( 'link', 'not_empty_and', 'lightbox' ),
							'subtype'	=> AviaHtmlHelper::linking_options()
					),   
	
					array(	
							'name' 	=> __( 'Button 1 Label', 'avia_framework' ),
							'desc' 	=> __( 'This is the text that appears on your button.', 'avia_framework' ),
							'id' 	=> 'button_label',
							'type' 	=> 'input',
							'std' 	=> 'Click me',
							'container_class' => 'av_half av_half_first',
							'lockable'	=> $lockable,
							'required'	=> array( 'link_apply', 'contains', 'button' )
					),	
								            
					array(	
							'type'			=> 'template',
							'template_id'	=> 'named_colors',
							'name'			=> __( 'Button 1 Color', 'avia_framework' ),
							'desc'			=> __( 'Choose a color for your button here', 'avia_framework' ),
							'id'			=> 'button_color',
							'std'			=> 'light',
							'container_class' => 'av_half',
							'lockable'		=> $lockable,
							'required'		=> array( 'link_apply', 'contains', 'button' )
					),
								
					array(	
							'name' 	=> __( 'Button 1 Link?', 'avia_framework' ),
							'desc' 	=> __( 'Where should the Button link to?', 'avia_framework' ),
							'id' 	=> 'link1',
							'type' 	=> 'linkpicker',
							'std' 	=> '',
							'fetchTMPL'	=> true,
							'container_class' => 'av_half av_half_first',
							'lockable'	=> $lockable,
							'required'	=> array( 'link_apply', 'contains', 'button' ),
							'subtype'	=> array(	
												__( 'Set Manually', 'avia_framework' )	=> 'manually',
												__( 'Single Entry', 'avia_framework' )	=> 'single',
												__( 'Taxonomy Overview Page', 'avia_framework' )	=> 'taxonomy',
											)
					),

					array(	
							'name' 	=> __( 'Button 1 Link Target?', 'avia_framework' ),
							'desc' 	=> __( 'Select here if you want to open the linked page in a new window', 'avia_framework' ),
							'id' 	=> 'link_target1',
							'type' 	=> 'select',
							'std' 	=> '',
							'container_class' => 'av_half',
							'lockable'	=> $lockable,
							'required'	=> array( 'link_apply', 'contains', 'button' ),
							'subtype'	=> AviaHtmlHelper::linking_options()
					),   						
								
					array(	
							'name' 	=> __( 'Button 2 Label', 'avia_framework' ),
							'desc' 	=> __( 'This is the text that appears on your second button.', 'avia_framework' ),
							'id' 	=> 'button_label2',
							'type' 	=> 'input',
							'std' 	=> 'Click me',
							'container_class' => 'av_half av_half_first',
							'lockable'	=> $lockable,
							'required'	=> array( 'link_apply', 'contains',' button-two' )
					),	
								            
					array(	
							'type'			=> 'template',
							'template_id'	=> 'named_colors',
							'name'			=> __( 'Button 2 Color', 'avia_framework' ),
							'desc'			=> __( 'Choose a color for your second button here', 'avia_framework' ),
							'id'			=> 'button_color2',
							'std'			=> 'light',
							'container_class' => 'av_half',
							'lockable'		=> $lockable,
							'required'		=> array( 'link_apply', 'contains', 'button-two' )
					),
						
					array(	
							'name' 	=> __( 'Button 2 Link?', 'avia_framework' ),
							'desc' 	=> __( 'Where should the Button link to?', 'avia_framework' ),
							'id' 	=> 'link2',
							'type' 	=> 'linkpicker',
							'fetchTMPL'	=> true,
							'std' 	=> '',
							'container_class' => 'av_half av_half_first',
							'lockable'	=> $lockable,
							'required'	=> array( 'link_apply', 'contains','button-two' ),
							'subtype'	=> array(	
												__( 'Set Manually', 'avia_framework' ) => 'manually',
												__( 'Single Entry', 'avia_framework' ) => 'single',
												__( 'Taxonomy Overview Page',  'avia_framework' ) => 'taxonomy',
											),
							
					),

					array(	
							'name' 	=> __( 'Button 2 Link Target?', 'avia_framework' ),
							'desc' 	=> __( 'Select here if you want to open the linked page in a new window', 'avia_framework' ),
							'id' 	=> 'link_target2',
							'type' 	=> 'select',
							'std' 	=> '',
							'container_class' => 'av_half',
							'lockable'	=> $lockable,
							'required'	=> array( 'link_apply', 'contains', 'button-two' ),
							'subtype'	=> AviaHtmlHelper::linking_options()
					)

				);

			return $template;
		}
		
		/**
		 * Button Color Template
		 * 
		 * @since 4.7.5.1
		 * @param array $element
		 * @return array
		 */
		protected function button_colors( array $element )
		{
			$color_id = isset( $element['color_id'] ) ? $element['color_id'] : 'color';
			$custom_id = isset( $element['custom_id'] ) && is_string( $element['custom_id'] ) ? $element['custom_id'] : 'custom';
			$required = isset( $element['required'] ) ? $element['required'] : array();
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			if( isset( $element['ids'] ) && is_array( $element['ids'] ) )
			{
				$ids = $element['ids'];
				
				if( ! isset( $ids['bg']['gradient'] ) )
				{
					$ids['bg']['gradient'] = $custom_id . '_grad';
				}
			}
			else
			{
				$ids = array(
						'bg'		=> array(
										'color'		=> $color_id . '_bg',
										'custom'	=> 'custom',
										'custom_id'	=> $custom_id . '_bg',
										'gradient'	=> $custom_id . '_grad'
									),
						'bg_hover'	=> array(
										'color'		=> $color_id . '_bg_hover',
										'custom'	=> 'custom',
										'custom_id'	=> $custom_id . '_bg_hover',
									),
						'font'		=> array(
										'color'		=> $color_id . '_font',
										'custom'	=> 'custom',
										'custom_id'	=> $custom_id . '_font',
									),
						'font_hover' => array(
										'color'		=> $color_id . '_font_hover',
										'custom'	=> 'custom',
										'custom_id'	=> $custom_id . '_font_hover',
									),
						);
			}
			
			if( isset( $element['name'] ) && is_array( $element['name'] ) )
			{
				$name = $element['name'];
			}
			else
			{
				$name = array(
							'bg'			=> array(
													'default'	=> __( 'Button Background Color', 'avia_framework' ),
													'custom'	=> __( 'Custom Button Background Color', 'avia_framework' ),
											),
							'bg_hover'		=> array(	
													'default'	=> __( 'Button Background Color On Hover', 'avia_framework' ),
													'custom'	=> __( 'Custom Button Background Color On Hover', 'avia_framework' ),
											),
							'font'			=> array(
													'default'	=> __( 'Button Font Color', 'avia_framework' ),
													'custom'	=> __( 'Custom Button Font Color', 'avia_framework' ),
											),
							'font_hover'	=> array(
													'default'	=> __( 'Button Font Color On Hover', 'avia_framework' ),
													'custom'	=> __( 'Custom Button Font Color On Hover', 'avia_framework' )
											)
						);
			}
			
			if( isset( $element['desc'] ) && is_array( $element['desc'] ) )
			{
				$desc = $element['desc'];
			}
			else
			{
				$desc = array(
							'bg'			=>	__( 'Select background color for your button here', 'avia_framework' ),
							'bg_hover'		=>	__( 'Select background color on hover for your button here (translucent and theme colors might not always work properly when using custom border stylings).', 'avia_framework' ),
							'font'			=>	__( 'Select font color for your button here', 'avia_framework' ),
							'font_hover'	=>	__( 'Select font color on hover for your button here', 'avia_framework' )
						);
			}
			
			if( isset( $element['std'] ) && is_array( $element['std'] ) )
			{
				$std = $element['std'];
			}
			else
			{
				$std = array(
							'bg'				=>	'theme-color',
							'bg_hover'			=>	'theme-color-highlight',
							'font'				=>	'#ffffff',
							'font_hover'		=>	'#ffffff',
							'custom_bg'			=>	'#444444',
							'custom_bg_hover'	=>	'#444444',
							'custom_font'		=>	'#ffffff',
							'custom_font_hover'	=>	'#ffffff'
						);
			}
			
			if( isset( $element['translucent'] ) && is_array( $element['translucent'] ) )
			{
				$translucent = $element['translucent'];
			}
			else
			{
				$translucent = array(
									'bg'			=>	'',
									'bg_hover'		=>	'',
									'font'			=>	array(),
									'font_hover'	=>	array()
								);
			}
			
			$template = array(
				
					array(	
						'type'			=> 'template',
						'template_id'	=> 'named_colors',
						'id'			=> $ids['bg']['color'],
						'name'			=> $name['bg']['default'],
						'desc'			=> $desc['bg'],
						'std'			=> $std['bg'],
						'translucent'	=> $translucent['bg'],
						'custom'		=> $ids['bg']['custom'],
						'gradient'		=> $ids['bg']['gradient'],
						'lockable'		=> $lockable,
						'required'		=> $required
					),
				
					array(	
						'type'			=> 'template',
						'template_id'	=> 'gradient_colors',
						'id'			=> $ids['bg']['gradient'],
						'lockable'		=> $lockable,
						'hover'			=> true,
						'required'		=> array( $ids['bg']['color'], 'equals', $ids['bg']['gradient'] )
					),

					array(	
						'name'		=> $name['bg']['custom'],
						'desc'		=> $desc['bg'],
						'id'		=> $ids['bg']['custom_id'],
						'type'		=> 'colorpicker',
						'std'		=> $std['custom_bg'],
						'lockable'	=> $lockable,
						'required'	=> array( $ids['bg']['color'], 'equals', $ids['bg']['custom'] )
					),
				
					array(	
						'type'			=> 'template',
						'template_id'	=> 'named_colors',
						'id'			=> $ids['bg_hover']['color'],
						'name'			=> $name['bg_hover']['default'],
						'desc'			=> $desc['bg_hover'],
						'std'			=> $std['bg_hover'],
						'translucent'	=> $translucent['bg_hover'],
						'custom'		=> $ids['bg_hover']['custom'],
						'lockable'		=> $lockable,
//						'required'		=> $required,
						'required'		=> array( $ids['bg']['color'], 'not', $ids['bg']['gradient'] )
					),

					array(	
						'name'		=> $name['bg_hover']['custom'],
						'desc'		=> $desc['bg_hover'],
						'id'		=> $ids['bg_hover']['custom_id'],
						'type'		=> 'colorpicker',
						'std'		=> $std['custom_bg_hover'],
						'lockable'	=> $lockable,
						'required'	=> array( $ids['bg_hover']['color'], 'equals', $ids['bg_hover']['custom'] )
					),
				
					array(	
						'type'			=> 'template',
						'template_id'	=> 'named_colors',
						'id'			=> $ids['font']['color'],
						'name'			=> $name['font']['default'],
						'desc'			=> $desc['font'],
						'std'			=> $std['font'],
						'translucent'	=> $translucent['font'],
						'custom'		=> $ids['font']['custom'],
						'lockable'		=> $lockable,
						'required'		=> $required
					),
				
					array(	
						'name'		=> $name['font']['custom'],
						'desc'		=> $desc['font'],
						'id'		=> $ids['font']['custom_id'],
						'type'		=> 'colorpicker',
						'std'		=> $std['font'],
						'lockable'	=> $lockable,
						'required'	=> array( $ids['font']['color'], 'equals', $ids['font']['custom'] )
					),
				
					array(	
						'type'			=> 'template',
						'template_id'	=> 'named_colors',
						'id'			=> $ids['font_hover']['color'],
						'name'			=> $name['font_hover']['default'],
						'desc'			=> $desc['font_hover'],
						'std'			=> $std['font_hover'],
						'translucent'	=> array(),
						'no_theme_col'	=> true,
						'custom'		=> $ids['font_hover']['custom'],
						'lockable'		=> $lockable,
						'required'		=> $required
					),
				
					array(	
						'name' 	=> $name['font_hover']['custom'],
						'desc' 	=> $desc['font_hover'],
						'id' 	=> $ids['font_hover']['custom_id'],
						'type' 	=> 'colorpicker',
						'std' 	=> $std['font_hover'],
						'lockable'	=> $lockable,
						'required'	=> array( $ids['font_hover']['color'], 'equals', $ids['font_hover']['custom'] )
					)
				
				);
			
			return $template;
		}
		
		/**
		 * Named Color Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function named_colors( array $element )
		{
			$name = isset( $element['name'] ) ? $element['name'] : __( 'Button Color', 'avia_framework' );
			$desc = isset( $element['desc'] ) ? $element['desc'] : __( 'Choose a color for your button here', 'avia_framework' );
			$id = isset( $element['id'] ) ? $element['id'] : 'color';
			$std = isset( $element['std'] ) ? $element['std'] : 'theme-color';
			$required = isset( $element['required'] ) ? $element['required'] : array();
			$container_class  = isset( $element['container_class'] ) ? $element['container_class'] : '';
			$theme_col_key = isset( $element['theme-col-key'] ) ? $element['theme-col-key'] : 'theme-color';
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			if( isset( $element['translucent'] ) && is_array( $element['translucent'] ) )
			{
				$translucent = $element['translucent'];
			}
			else
			{
				$translucent = array(
									__( 'Light Transparent', 'avia_framework' )	=> 'light',
									__( 'Dark Transparent', 'avia_framework' )	=> 'dark',
								);
			}
			
			$colored = array(
							__( 'Theme Color', 'avia_framework' )			=> $theme_col_key,
							__( 'Theme Color Highlight', 'avia_framework' )	=> 'theme-color-highlight',
							__( 'Theme Color Subtle', 'avia_framework' )	=> 'theme-color-subtle',
							__( 'White', 'avia_framework' )		=> 'white',
							__( 'Blue', 'avia_framework' )		=> 'blue',
							__( 'Red',  'avia_framework' )		=> 'red',
							__( 'Green', 'avia_framework' )		=> 'green',
							__( 'Orange', 'avia_framework' )	=> 'orange',
							__( 'Aqua', 'avia_framework' )		=> 'aqua',
							__( 'Teal', 'avia_framework' )		=> 'teal',
							__( 'Purple', 'avia_framework' )	=> 'purple',
							__( 'Pink', 'avia_framework' )		=> 'pink',
							__( 'Silver', 'avia_framework' )	=> 'silver',
							__( 'Grey', 'avia_framework' )		=> 'grey',
							__( 'Black', 'avia_framework' )		=> 'black',
						);
			
			if( ! empty( $element['no_alternate'] ) )
			{
				array_splice( $colored, 1, 2 );
			}
			else if( ! empty( $element['no_theme_col'] ) )
			{
				array_splice( $colored, 0, 3 );
			}
			
			if( ! empty( $element['custom'] ) )
			{
				$val = true === $element['custom'] ? 'custom' : $element['custom'];
				$colored[ __( 'Custom Color', 'avia_framework' ) ] = $val;
			}
			
			if( ! empty( $element['gradient'] ) )
			{
				$val = true === $element['gradient'] ? 'gradient' : $element['gradient'];
				$colored[ __( 'Gradient Color', 'avia_framework' ) ] = $val;
			}
			
			$e = array(
						'name'		=> $name,
						'desc'		=> $desc,
						'id'		=> $id,
						'type'		=> 'select',
						'std'		=> $std,
						'container_class' => $container_class,
						'required'	=> $required,
						'lockable'	=> $lockable,
						'subtype'	=> array()		
				);
			
			$subtype = array();
			
			if( ! empty( $translucent ) )
			{
				$subtype[ __( 'Translucent Buttons', 'avia_framework' ) ] = $translucent;
				$subtype[ __( 'Colored Buttons', 'avia_framework' ) ] = $colored;
			}
			else
			{
				$subtype = $colored;
			}
			
			/**
			 * @since 4.8
			 * @param array $subtype
			 * @param array $element
			 * @return array
			 */
			$e['subtype'] = apply_filters( 'avf_alb_popup_templates_named_colors', $subtype, $element );
			
			$template = array( $e );
			
			return $template;
		}
		
		/**
		 * Hover Opacity Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function hover_opacity( array $element )
		{
			if( ! isset( $element['name'] ) )
			{
				$element['name'] = __( 'Opacity On Hover', 'avia_framework' );
			}
			
			if( ! isset( $element['desc'] ) )
			{
				$element['desc'] = __( 'Select the opacity for the element on hover (Opacity setting for a possible background gradient color or some effects may have precedence !).', 'avia_framework' );
			}
			
			return $this->opacity( $element );
		}
		
		/**
		 * Opacity on Hover Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function opacity( array $element )
		{
			$id = isset( $element['id'] ) ? $element['id'] : 'hover_opacity';
			$name = isset( $element['name'] ) ? $element['name'] : __( 'Opacity', 'avia_framework' );
			$desc  = isset( $element['desc'] ) ? $element['desc'] : __( 'Select the opacity for the element. Please keep in mind that other settings might have precedence and overrule this.', 'avia_framework' );
			$std = isset( $element['std'] ) ? $element['std'] : '';
			$required = isset( $element['required'] ) ? $element['required'] : array();
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			$container_class = isset( $element['container_class'] ) ? $element['container_class'] : '';
			
			$opacity = array(
						'name'		=> $name,
						'desc'		=> $desc,
						'id'		=> $id,
						'type'		=> 'select',
						'std'		=> $std,
						'lockable'	=> $lockable,
						'required'	=> $required,
						'subtype'	=> \AviaHtmlHelper::number_array( 0.0, 1, 0.1, array( __( 'Theme default', 'avia_framework' ) => '' ) ),
					);
			
			if( ! empty( $container_class ) )
			{
				$opacity['container_class'] = $container_class;
			}
			
			$template = array( $opacity );
			
			return $template;
		}
		
		/**
		 * Masonry Captions Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function masonry_captions( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array(
				
					array(
						'name' 	=> __( 'Element Title and Excerpt', 'avia_framework' ),
						'desc' 	=> __( 'You can choose if you want to display title and/or excerpt', 'avia_framework' ),
						'id' 	=> 'caption_elements',
						'type' 	=> 'select',
						'std' 	=> 'title excerpt',
						'lockable'	=> $lockable,
						'subtype'	=> array(
											__( 'Display Title and Excerpt', 'avia_framework' )	=> 'title excerpt',
											__( 'Display Title', 'avia_framework' )				=> 'title',
											__( 'Display Excerpt', 'avia_framework' )			=> 'excerpt',
											__( 'Display Neither', 'avia_framework' )			=> 'none',
										)
					),	

					array(
						'name' 	=> __( 'Element Title and Excerpt Styling', 'avia_framework' ),
						'desc' 	=> __( 'You can choose the styling for the title and excerpt here', 'avia_framework' ),
						'id' 	=> 'caption_styling',
						'type' 	=> 'select',
						'std' 	=> 'always',
						'lockable'	=> $lockable,
						'required'	=> array( 'caption_elements', 'not', 'none' ),
						'subtype'	=> array(
											__( 'Default display (at the bottom of the elements image)', 'avia_framework' )	=> '',
											__( 'Display as centered overlay (overlays the image)', 'avia_framework' )		=> 'overlay',
										)
					),	

					array(
						'name' 	=> __( 'Element Title and Excerpt display settings', 'avia_framework' ),
						'desc' 	=> __( 'You can choose whether to always display Title and Excerpt or only on hover', 'avia_framework' ),
						'id' 	=> 'caption_display',
						'type' 	=> 'select',
						'std' 	=> 'always',
						'lockable'	=> $lockable,
						'required'	=> array( 'caption_elements', 'not', 'none' ),
						'subtype'	=> array(
											__( 'Always Display', 'avia_framework' )			=> 'always',
											__( 'Display on mouse hover', 'avia_framework' )	=> 'on-hover',
											__( 'Hide on mouse hover', 'avia_framework' )		=> 'on-hover-hide',
										)
					)	
				);
			
			return $template;
		}
		
		
		/**
		 * Background Image Position Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function background_image_position( array $element )
		{
			$id_pos = isset( $element['args']['id_pos'] ) ? trim(  $element['args']['id_pos'] ) : 'background_position';
			$id_repeat = isset( $element['args']['id_repeat'] ) ? trim(  $element['args']['id_repeat'] ) : 'background_repeat';
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array();
				
			$template[] = array(
							'name' 	=> __( 'Background Image Position', 'avia_framework' ),
							'id' 	=> $id_pos,
							'type' 	=> 'select',
							'std' 	=> 'top left',
							'lockable'	=> $lockable,
							'required'	=> array( 'src', 'not','' ),
							'subtype'	=> array(   
											__( 'Top Left', 'avia_framework' )       => 'top left',
											__( 'Top Center', 'avia_framework' )     => 'top center',
											__( 'Top Right', 'avia_framework' )      => 'top right',
											__( 'Bottom Left', 'avia_framework' )    => 'bottom left',
											__( 'Bottom Center', 'avia_framework' )  => 'bottom center',
											__( 'Bottom Right', 'avia_framework' )   => 'bottom right',
											__( 'Center Left', 'avia_framework' )    => 'center left',
											__( 'Center Center', 'avia_framework' )  => 'center center',
											__( 'Center Right', 'avia_framework' )   => 'center right'
										)
					);
			
			$sub = array(  
						__( 'No Repeat', 'avia_framework' )          => 'no-repeat',
						__( 'Repeat', 'avia_framework' )             => 'repeat',
						__( 'Tile Horizontally', 'avia_framework' )  => 'repeat-x',
						__( 'Tile Vertically', 'avia_framework' )    => 'repeat-y',
						__( 'Stretch to fit (stretches image to cover the element)', 'avia_framework' )             => 'stretch',
						__( 'Scale to fit (scales image so the whole image is always visible)', 'avia_framework' )	=> 'contain'
					);
			
			if( ! empty( $element['args']['repeat_remove'] ) )
			{
				foreach( $sub as $key => $value ) 
				{
					if( in_array( $value, $element['args']['repeat_remove'] ) )
					{
						unset( $sub[ $key ] );
					}
				}
			}

			$template[] = array(
							'name' 	=> __( 'Background Repeat', 'avia_framework' ),
							'id' 	=> $id_repeat,
							'type' 	=> 'select',
							'std' 	=> 'no-repeat',
							'lockable'	=> $lockable,
							'required'	=> array( 'src', 'not','' ),
							'subtype'	=> $sub
				);

			return $template;
		}
		
		
		/**
		 * Date Query Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function date_query( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array(
				
					array(	
							'name' 		=> __( 'Do you want to filter entries by date?', 'avia_framework' ),
							'desc' 		=> __( 'Do you want to display entries within date boundaries only? Can be used e.g. to create archives.', 'avia_framework' ),
							'id' 		=> 'date_filter',
							'type' 		=> 'select',
							'std'		=> '',
							'lockable'	=> $lockable,
							'subtype'	=> array( 
												__( 'Display all entries', 'avia_framework' )		=> '',
												__( 'Filter entries by date', 'avia_framework' )	=> 'date_filter'
											)
						),
					
					array(	
							'name'		=> __( 'Start Date', 'avia_framework' ),
							'desc'		=> __( 'Pick a start date.', 'avia_framework' ),
							'id'		=> 'date_filter_start',
							'type'		=> 'datepicker',
							'std'		=> '',
							'container_class'	=> 'av_third av_third_first',
							'lockable'	=> $lockable,
							'required'	=> array( 'date_filter', 'equals', 'date_filter' ),
							'dp_params'	=> array(
												'dateFormat'        => 'yy/mm/dd',
												'changeMonth'		=> true,
												'changeYear'		=> true,
												'container_class'	=> 'select_dates_30'
											)
						),
					
					array(	
							'name'		=> __( 'End Date', 'avia_framework' ),
							'desc'		=> __( 'Pick the end date. Leave empty to display all entries after the start date.', 'avia_framework' ),
							'id'		=> 'date_filter_end',
							'type'		=> 'datepicker',
							'std'		=> '',
							'container_class'	=> 'av_2_third',
							'lockable'	=> $lockable,
							'required'	=> array( 'date_filter', 'equals', 'date_filter' ),
							'dp_params'	=> array(
												'dateFormat'        => 'yy/mm/dd',
												'changeMonth'		=> true,
												'changeYear'		=> true,
												'container_class'	=> 'select_dates_30'
											)
						),
					
					array(	
							'name'			=> __( 'Date Formt','avia_framework' ),
							'desc'			=> __( 'Define the same date format as used in date picker', 'avia_framework' ),
							'id'			=> 'date_filter_format',
							'container_class'	=> 'avia-hidden',
							'type'			=> 'input',
							'std'			=> 'yy/mm/dd'
						)
									
				);
			
				if( ! empty ( $element['template_required'][0] ) )
				{
					$template[0]['required'] = $element['template_required'][0];
				}
				
			return $template;
		}
		
		/**
		 * Returns a complete tab for element template selection
		 * (Supports element and item elements)
		 * 
		 * @since 4.8
		 * @param array $element
		 * @return array
		 */
		protected function element_template_selection_tab( array $element )
		{
			$template = array();
			
			if( ! Avia_Element_Templates()->element_templates_enabled() )
			{
				return $template;
			}
			
			$shortcode = isset( $element['args']['sc'] ) && $element['args']['sc'] instanceof aviaShortcodeTemplate ? $element['args']['sc'] : null;
			if( is_null( $shortcode ) )
			{
				return $template;
			}
			
			if( ! Avia_Element_Templates()->is_editable_base_element( $shortcode ) )
			{
				return $template;
			}
			
			$modal_group = isset( $element['args']['modal_group'] ) && ( true === $element['args']['modal_group'] );
			
			$check = '';
			$check_opt = avia_get_option( 'alb_show_locked_modal_options' );
			
			if( ! current_theme_supports( 'show_advanced_custom_element_options' ) )
			{
				if( avia_get_option( 'alb_locked_modal_options', '' ) != '' )
				{
					$check = 'show_locked_options_fakeArg';
				}
			}
			else
			{
				if( $check_opt != '' )
				{
					if( ! current_user_can( 'manage_options' ) )
					{
						$check = ( 'show_all' == $check_opt ) ? 'show_locked_options_fakeArg' : '';
					}
					else
					{
						$check = 'show_locked_options_fakeArg';
					}
				}
			}
			
			$template[] = array(
								'type'          => 'tab',
								'name'          => __( 'Customize', 'avia_framework' ),
								'class'			=> 'el-template-container',
								'nodescription' => true
							);
			
			$desc = __( 'Select to use the locked values of an existing custom element as a base for this element. Any changes to the locked settings of the underlying custom element(s) will be used. When using a cache plugin do not forget to invalidate server cache whenever you make changes to these elements.', 'avia_framework' );
			
			
			if( isset( $element['args']['reload_msg'] ) && true === $element['args']['reload_msg'] )
			{
				$desc .= '<br /><br />';
				$desc .= '<strong class="avia-reload-message" style="color: red;">';
				$desc .=	__( 'After changing the custom element template the new visual appearance is not recognized in the base element. Please save and reopen the base element to load visual support for this template.', 'avia_framework' );
				$desc .= '</strong>';
			}
			
			$template[] = array(
								'name'				=> __( 'Select a Custom Element to lock values', 'avia_framework' ),
								'desc'				=> $desc,
								'id'				=> 'element_template',
								'type'				=> 'select',
								'std'				=> '',
								'container_class'	=> 'avia-element-template-select',
								'data'				=> array(
															'template_selector'	=> 'element',
														),
								'subtype'			=> 'element_templates',
								'additional'		=> array(
															'shortcode'		=> $shortcode,
															'modal_group'	=> $modal_group
														),
								'tmpl_set_default'	=> false
							);
			
			$template[] = array(
//								'desc'				=> __( 'Store the ID of the custom element if one custom element for subitems is selected in theme options (internal use only)', 'avia_framework' ),
								'id'				=> 'one_element_template',
								'type'				=> 'hidden',
								'std'				=> '',
								'tmpl_set_default'	=> false
							);
			
			$template[] = array(
								'name'				=> __( 'Basic Custom Element Information', 'avia_framework' ),
								'desc'				=> __( 'Click button to edit custom element name and tooltip.', 'avia_framework' ),
								'title'				=> __( 'Edit Name And Tooltip', 'avia_framework' ),
								'type'				=> 'action_button',
								'container_class'	=> 'avia-element-edit-cpt-button',
								'modal_on_load'		=> 'modal_btn_edit_custom_element_cpt'
							);
			
			$template[] = array(
								'name'				=> __( 'Save as a new custom element template', 'avia_framework' ),
								'desc'				=> __( 'Click button to create a new custom element template with the settings of this element and lock the options you like.', 'avia_framework' ),
								'title'				=> __( 'Save As New Custom Element', 'avia_framework' ),
								'type'				=> 'action_button',
								'container_class'	=> 'avia-element-new-from-alb-button',
								'modal_on_load'		=> 'modal_btn_new_custom_element_from_alb'
							);
			
			$template[] = array(	
								'name'				=> __( 'Show Locked Options', 'avia_framework' ),
								'desc'				=> __( 'By default values of locked options are hidden to clean up the modal window. Check to show the options and values', 'avia_framework' ),
								'id'				=> 'show_locked_options_fakeArg',
								'type'				=> 'checkbox',
								'std'				=> '',
								'std_fakeArg'		=> $check,
								'container_class'	=> 'avia-element-show-locked-options-container',
								'class'				=> 'avia-element-show-locked-options avia-fake-input',
								'tmpl_set_default'	=> false
							);
			
			
			$template[] = array(
								'type'          => 'tab_close',
								'nodescription'	=> true
							);
			
			return $template;
			
		}
		
		/**
		 * Complete Screen Options Tab with several content options
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @param boolean $all				for backwards comp prior 4.6.4
		 * @return array
		 */
		protected function screen_options_tab( array $element, $all = true )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array();
			
			/**
			 * This is the default template when missing
			 */
			$sub_templates =  array( 'screen_options_visibility' );
			
			if( isset( $element['templates_include'] ) && ! empty( $element['templates_include']  ) )
			{
				$sub_templates = (array) $element['templates_include'];
			}
			
			if(  true === $all )
			{
				$template[] = array(
								'type'          => 'tab',		//	new --->  toggle
								'name'          => __( 'Responsive', 'avia_framework' ),
								'nodescription' => true
							);
			}
			
			foreach( $sub_templates as $sub_template ) 
			{
				if( false !== $this->template_exists( $sub_template ) )
				{
					$temp = array(	
									'type'          => 'template',
									'template_id'   => $sub_template,
									'lockable'		=> $lockable,
								);		
					
					if( isset( $element['subtype'][ $sub_template ] ) && is_array( $element['subtype'][ $sub_template ] ) )
					{
						$temp['subtype'] = $element['subtype'][ $sub_template ];
					}
					
					$template[] = $temp;
				}
			}
								
			if(  true === $all )
			{
				$template[] = array(
								'type'          => 'tab_close',
								'nodescription' => true
							);
			}					
						
			return $template;
		}
		
		
		/**
		 * Simple checkboxes for element visibility
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function screen_options_visibility( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$template = array(
							
							array(
									'type' 				=> 'heading',
									'name'              => __( 'Element Visibility', 'avia_framework' ),
									'desc'              => __( 'Set the visibility for this element, based on the device screensize.', 'avia_framework' ),
							),
							
							array(	
									'desc'              => __( 'Hide on large screens (wider than 990px - eg: Desktop)', 'avia_framework' ),
									'id'                => 'av-desktop-hide',
									'type'              => 'checkbox',
									'std'               => '',
									'container_class'   => 'av-multi-checkbox',
									'lockable'			=> $lockable
								),
								
							array(	

									'desc'              => __( 'Hide on medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
									'id'                => 'av-medium-hide',
									'type'              => 'checkbox',
									'std'               => '',
									'container_class'   => 'av-multi-checkbox',
									'lockable'			=> $lockable,
								),
										
							array(	

									'desc'              => __( 'Hide on small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
									'id'                => 'av-small-hide',
									'type'              => 'checkbox',
									'std'               => '',
									'container_class'   => 'av-multi-checkbox',
									'lockable'			=> $lockable,
								),
										
							array(	
									
									'desc'              => __( 'Hide on very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
									'id'                => 'av-mini-hide',
									'type'              => 'checkbox',
									'std'               => '',
									'container_class'   => 'av-multi-checkbox',
									'lockable'			=> $lockable,
								)
							
						);
			
			return $template;
		}
		
		/**
		 * Select boxes for Title Font Sizes
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function font_sizes_title( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$subtype = AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' );
			
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			
			$template = array(
				
							array(	
									'name'		=> __( 'Font Size for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
									'id'		=> 'av-medium-font-size-title',
									'type'		=> 'select',
									'std'		=> '',
									'lockable'	=> $lockable,
									'subtype'	=> $subtype,
									
								),
						            
							array(	
									'name'		=> __( 'Font Size for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
									'id'		=> 'av-small-font-size-title',
									'type'		=> 'select',
									'std'		=> '',
									'lockable'	=> $lockable,
									'subtype'	=> $subtype
								),
						            
							array(	
									'name'		=> __( 'Font Size for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
									'id'		=> 'av-mini-font-size-title',
									'type'		=> 'select',
									'std'		=> '',
									'lockable'	=> $lockable,
									'subtype'	=> $subtype
								)
				
						);
			
			return $template;
		}
		
		/**
		 * Select boxes for Content Font Sizes
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function font_sizes_content( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$subtype = AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' );
			
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			
			$template = array(
							array(	
									'name'		=> __( 'Font Size for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
									'id'		=> 'av-medium-font-size',
									'type'		=> 'select',
									'std'		=> '',
									'lockable'	=> $lockable,
									'subtype'	=> $subtype
								),
						            
							array(	
									'name'		=> __( 'Font Size for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
									'id'		=> 'av-small-font-size',
									'type'		=> 'select',
									'std'		=> '',
									'lockable'	=> $lockable,
									'subtype'	=> $subtype
								),
						            
							array(	
									'name'		=> __( 'Font Size for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
									'id'		=> 'av-mini-font-size',
									'type'		=> 'select',
									'std'		=> '',
									'lockable'	=> $lockable,
									'subtype'	=> $subtype
								)				
						);
			
			return $template;
		}

		/**
		 * Select boxes for Heading Font Size
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function heading_font_size( array $element )
		{
			$title = array(
							array(
									'name'		=> __( 'Heading Font Size', 'avia_framework' ),
									'desc'		=> __( 'Set the font size for the heading, based on the device screensize.', 'avia_framework' ),
									'type'		=> 'heading',
									'description_class' => 'av-builder-note av-neutral',
								)
							);
			
			$fonts = $this->font_sizes_title( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Content Font Size
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function content_font_size( array $element )
		{
			$title = array(
							array(
									'name'		=> __( 'Content Font Size', 'avia_framework' ),
									'desc'		=> __( 'Set the font size for the content, based on the device screensize.', 'avia_framework' ),
									'type'		=> 'heading',
									'description_class' => 'av-builder-note av-neutral',
								)
						);
			
			$fonts = $this->font_sizes_content( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Subheading Font Size
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function subheading_font_size( array $element )
		{
			$template = $this->content_font_size( $element );
			
			$title = array( 
							array(
								'name'		=> __( 'Subheading Font Size', 'avia_framework' ),
								'desc'		=> __( 'Set the font size for the subheading, based on the device screensize.', 'avia_framework' ),
								'type'		=> 'heading',
								'description_class'	=> 'av-builder-note av-neutral',
							)
						);
			
			
			$fonts = $this->font_sizes_content( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Number Font Size (countdown)
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function number_font_size( array $element )
		{
			$title = array(
							array(
									'name'		=> __( 'Number Font Size', 'avia_framework' ),
									'desc'		=> __( 'Set the font size for the number, based on the device screensize.', 'avia_framework' ),
									'type'		=> 'heading',
									'description_class' => 'av-builder-note av-neutral',
							)
						);
			
			$fonts = $this->font_sizes_title( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Text Font Size (countdown)
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function text_font_size( array $element )
		{
			$title = array(
							array(
									'name'		=> __( 'Text Font Size', 'avia_framework' ),
									'desc'		=> __( 'Set the font size for the text, based on the device screensize.', 'avia_framework' ),
									'type'		=> 'heading',
									'description_class' => 'av-builder-note av-neutral',
							)
						);
			
			$fonts = $this->font_sizes_content( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Columns ( 1 - 4 )
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function column_count( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$subtype = AviaHtmlHelper::number_array( 1, 4, 1, array( __( 'Default', 'avia_framework' ) => '' ) );
			
			$template = array(
				
							array(	
									'name'		=> __( 'Column count for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
									'id'		=> 'av-medium-columns',
									'type'		=> 'select',
									'std'		=> '',
									'lockable'	=> $lockable,
									'subtype'	=> $subtype,
								),
						            
							array(	
									'name'		=> __( 'Column count for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
									'id'		=> 'av-small-columns',
									'type'		=> 'select',
									'std'		=> '',
									'lockable'	=> $lockable,
									'subtype'	=> $subtype,
								),
						            
							array(	
									'name'		=> __( 'Column count for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
									'id'		=> 'av-mini-columns',
									'type'		=> 'select',
									'std'		=> '',
									'lockable'	=> $lockable,
									'subtype'	=> $subtype,
								),  	
							  
				);
			
			return $template;
		}
		
		/**
		 * Select box for <h. > tag and inputfield for custom class
		 * 
		 * @since 4.5.7.2
		 * @param array $element
		 * @return array
		 */
		protected function heading_tag( array $element )
		{
			$setting = Avia_Builder()->get_developer_settings( 'heading_tags' );
			$class = in_array( $setting, array( 'deactivate', 'hide' ) ) ? 'avia-hidden' : '';
			
			$allowed = array( 
							__( 'Theme default', 'avia_framework' )	=> '',
							'H1'	=> 'h1', 
							'H2'	=> 'h2', 
							'H3'	=> 'h3', 
							'H4'	=> 'h4', 
							'H5'	=> 'h5', 
							'H6'	=> 'h6',
							'P'		=> 'p',
							'DIV'	=> 'div',
							'SPAN'	=> 'span'
						);
			
			
			$rendered_subtype = isset( $element['subtype'] ) ? $element['subtype'] : $allowed;
			$default = isset( $element['theme_default'] ) ? $element['theme_default'] : array_keys( $rendered_subtype )[0];
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			/**
			 * Filter possible tags for element
			 * 
			 * @since 4.5.7.2
			 * @param array $rendered_subtype
			 * @param array $element
			 * @return array
			 */
			$subtype = apply_filters( 'avf_alb_element_heading_tags', $rendered_subtype, $element );
			if( ! is_array( $subtype ) || empty( $subtype ) )
			{
				$subtype = $rendered_subtype;
			}
			
			$std = isset( $element['std'] ) ? $element['std'] : '';
			if( ! in_array( $std, $subtype ) )
			{
				$std = ( 1 == count( $subtype ) ) ? array_values( $subtype )[0] : array_values( $subtype )[1];
			}
			
			$template = array();
				
			$templ = array(	
							'name'				=> sprintf( __( 'Heading Tag (Theme Default is &lt;%s&gt;)', 'avia_framework' ), $default ),
							'desc'				=> __( 'Select a heading tag for this element. Enfold only provides CSS for theme default tags, so it might be necessary to add a custom CSS class below and adjust the CSS rules for this element.', 'avia_framework' ),
							'id'				=> 'heading_tag',
							'type'				=> 'select',
							'std'				=> $std,
							'container_class'	=> $class,
							'lockable'			=> $lockable,
							'subtype'			=> $subtype,
						);
			
			if( isset( $element['required'] ) && is_array( $element['required'] ) )
			{
				$templ['required'] = $element['required'];
			}
			
			$template[] = $templ;
				
			$templ = array(	
							'name'				=> __( 'Custom CSS Class For Heading Tag', 'avia_framework' ),
							'desc'				=> __( 'Add a custom css class for the heading here. Make sure to only use allowed characters (latin characters, underscores, dashes and numbers).', 'avia_framework' ),
							'id'				=> 'heading_class',
							'type'				=> 'input',
							'std'				=> '',
							'container_class'	=> $class,
							'lockable'			=> $lockable,
						);
			
			if( isset( $element['required'] ) && is_array( $element['required'] ) )
			{
				$templ['required'] = $element['required'];
			}
			
			$template[] = $templ;
			
			return $template;
		}
		
		/**
		 * Geolocation Template - Creates a container to fetch long and lat from a given address
		 * 
		 * @since 4.8.2
		 * @param array $element
		 * @return array
		 */
		protected function geolocation_toggle( array $element  )
		{
			$title = isset( $element['title'] ) ? $element['title'] : __( 'Marker Location', 'avia_framework' );
			
			$c = array(
						array(
							'name'		=> __( 'Street and Number', 'avia_framework' ),
							'desc'		=> __( 'Enter the street and streetnumber seperated by space, e.g. Teststreet 15.', 'avia_framework' ),
							'id'		=> 'geo_street',
							'type'		=> 'input',
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Postalcode (Zip Code)', 'avia_framework' ),
							'desc'		=> __( 'Enter the postalcode (Zip code) for the city, e.g. 12454', 'avia_framework' ),
							'id'		=> 'geo_postalcode',
							'type'		=> 'input',
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'City', 'avia_framework' ),
							'desc'		=> __( 'Enter the city name, e.g. Denver', 'avia_framework' ),
							'id'		=> 'geo_city',
							'type'		=> 'input',
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Country', 'avia_framework' ),
							'desc'		=> __( 'Enter the Country, e.g. Canada', 'avia_framework' ),
							'id'		=> 'geo_country',
							'type'		=> 'input',
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Optional State', 'avia_framework' ),
							'desc'		=> __( 'Optionally enter State to identify the location', 'avia_framework' ),
							'id'		=> 'geo_state',
							'type'		=> 'input',
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Optional County', 'avia_framework' ),
							'desc'		=> __( 'Optionally enter County to identify the location', 'avia_framework' ),
							'id'		=> 'geo_county',
							'type'		=> 'input',
							'std'		=> ''
						),
				
						array(
							'name'				=> __( 'Fetch coordinates', 'avia_framework' ),
							'desc'				=> __( 'Click button to fetch the coordinates for the address above to speed up loading of map in frontend.', 'avia_framework' ),
							'title'				=> __( 'Fetch coordinates for address above', 'avia_framework' ),
							'title_active'		=> __( 'Fetching address .......', 'avia_framework' ),
							'type'				=> 'action_button',
							'container_class'	=> 'avia-geolocation_get_coordinates',
							'modal_on_load'		=> 'modal_btn_geolocation_get_coordinates'
						),
				
						array(
							'name'		=> __( 'Longitude', 'avia_framework' ),
							'desc'		=> __( 'Enter the longitude of your adress, use "." for comma, e.g. 48.21475', 'avia_framework' ),
							'id'		=> 'geo_lng',
							'type'		=> 'input',
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Latitude', 'avia_framework' ),
							'desc'		=> __( 'Enter the latitude of your adress, use "." for comma, e.g. 16.37366388888', 'avia_framework' ),
							'id'		=> 'geo_lat',
							'type'		=> 'input',
							'std'		=> ''
						),
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> $title,
								'content'		=> $c 
							),
					);
			
			return $template;
		}
		
		/**
		 * Lazy Load Template 
		 * 
		 * @since 4.7.6.3
		 * @deprecated 4.7.6.4
		 * @param array $element
		 * @return array
		 */
		protected function lazy_loading( array $element )
		{
			_deprecated_function( 'Avia_Popup_Templates::lazy_loading', '4.7.6.4', 'Avia_Popup_Templates::lazy_loading_toggle' );
			
			$element['no_toggle'] = true;
			
			return $this->lazy_loading_toggle( $element );
		}
		
		/**
		 * Lazy Load Template 
		 * 
		 * @since 4.7.6.4
		 * @param array $element
		 * @return array
		 */
		protected function lazy_loading_toggle( array $element )
		{
			$desc  = __( 'Lazy loading of images using pure HTML is a feature introduced with WP 5.5 as a standard feature to speed up page loading. But it may not be compatible with animations and might break functionality of your page.', 'avia_framework' ) . ' '; 
			$desc .= __( 'Therefore this feature is disabled by default. Please check carefully that everything is working as you expect when you enable this feature for this element.', 'avia_framework' );
					
			$id = isset( $element['id'] ) && ! empty( $element['id'] ) ? $element['id'] : 'lazy_loading';
			$std = isset( $element['std'] ) && in_array( $element['std'] , array( 'disabled', 'enabled' ) ) ? $element['std'] : 'disabled';
			$required = isset( $element['required'] ) && is_array( $element['required'] ) ? $element['required'] : array();
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$c = array(
							array(
								'name'		=> __( 'Lazy Loading Of Images', 'avia_framework' ),
								'desc'		=> $desc,
								'id'		=> $id,
								'type'		=> 'select',
								'std'		=> $std,
								'lockable'	=> $lockable,
								'required'	=> $required,
								'subtype'	=> array(
													__( 'Do not use lazy loading', 'avia_framework' )	=> 'disabled',
													__( 'Enable lazy loading', 'avia_framework' )		=> 'enabled'
												)
							)
				);
			
			if( isset( $element['no_toggle'] ) && true === $element['no_toggle'] )
			{
				$template = $c;
			}
			else
			{
				$template = array(
								array(	
									'type'			=> 'template',
									'template_id'	=> 'toggle',
									'title'			=> __( 'Performance', 'avia_framework' ),
									'content'		=> $c 
								),
					);
			}
			
			return $template;
		}
		
		/**
		 * Returns the image size toggle for WooCommerce Sliders and Grid
		 * 
		 * @since 4.8
		 * @param array $element
		 * @return array
		 */
		protected function wc_image_size_toggle( array $element ) 
		{
			global $_wp_additional_image_sizes;
			
			$id = isset( $element['id'] ) ? $element['id'] : 'image_size';
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$sizes = array();
			$std = '';
			
			if( is_array( $_wp_additional_image_sizes ) )
			{
				if( array_key_exists( 'woocommerce_thumbnail', $_wp_additional_image_sizes ) )
				{
					$std = 'woocommerce_thumbnail';
					$key = sprintf( __( 'Use default - WooCommerce Thumbnail (%d*%d)', 'avia_framework' ), $_wp_additional_image_sizes['woocommerce_thumbnail']['width'], $_wp_additional_image_sizes['woocommerce_thumbnail']['height'] );
					$sizes[ $key ] = 'woocommerce_thumbnail';
				}
				else if( array_key_exists( 'shop_catalog', $_wp_additional_image_sizes ) )
				{
					$std = 'shop_catalog';
					$key = sprintf( __( 'Use default - Shop Catalog (%d*%d)', 'avia_framework' ), $_wp_additional_image_sizes['shop_catalog']['width'], $_wp_additional_image_sizes['shop_catalog']['height'] );
					$sizes[ $key ] = 'shop_catalog';
				}
			}
			
			if( empty( $sizes ) )
			{
				$sizes[ __( 'Use default', 'avia_framework' ) ] = '';
			}
			
			$sizes = array_merge( $sizes, AviaHelper::get_registered_image_sizes( array( $std ), false, true ) );
			
			$c = array(
					
						array(
							'name'		=> __( 'Select image size', 'avia_framework' ),
							'desc'		=> __( 'Depending on your layout It might be better to select a larger image size for better image quality. Default size can be changed at Dashboard -&gt; Appearance -&gt; Customize -&gt; WooCommerce -&gt; Product Images -&gt; Thumbnail.', 'avia_framework' ),
							'id'		=> $id,
							'type'		=> 'select',
							'std'		=> $std,
							'lockable'	=> $lockable,
							'subtype'	=>  $sizes
						)
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Image Size', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			
			return $template;
		}


		/**
		 *  Select boxes for WooCommerce Options for non product elements
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function wc_options_non_products( array $element )
		{
			$required = array( 'link', 'parent_in_array', implode( ',', get_object_taxonomies( 'product', 'names' ) ) );
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$sort = array( 
							__( 'Use default (defined at Dashboard -&gt; Customize -&gt; WooCommerce)', 'avia_framework' )	=> '',
							__( 'Sort alphabetically', 'avia_framework' )			=> 'title',
							__( 'Sort by most recent', 'avia_framework' )			=> 'date',
							__( 'Sort by price', 'avia_framework' )					=> 'price',
							__( 'Sort by popularity', 'avia_framework' )			=> 'popularity',
							__( 'Sort randomly', 'avia_framework' )					=> 'rand',
							__( 'Sort by menu order and name', 'avia_framework' )	=> 'menu_order',
							__( 'Sort by average rating', 'avia_framework' )		=> 'rating',
							__( 'Sort by relevance', 'avia_framework' )				=> 'relevance',
							__( 'Sort by Product ID', 'avia_framework' )			=> 'id'
						);
			
			/**
			 * @since 4.5.7.1
			 * @param array $sort
			 * @param array $element
			 * @return array
			 */
			$sort = apply_filters( 'avf_alb_wc_options_non_products_sort', $sort, $element );
			
			
			$template = array();
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Out of Stock Product visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products. Default setting can be set at Woocommerce -&gt Settings -&gt Products -&gt Inventory -&gt Out of stock visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_visible',
								'type'		=> 'select',
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> $required,
								'subtype'	=> array(
													__( 'Use default WooCommerce Setting (Settings -&gt; Products -&gt; Out of stock visibility)', 'avia_framework' ) => '',
													__( 'Hide products out of stock', 'avia_framework' )	=> 'hide',
													__( 'Show products out of stock', 'avia_framework' )	=> 'show'
												)
							);
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Hidden Products visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products depending on catalog visibility. Can be set independently for each product: Edit Product -&gt Publish panel -&gt Catalog visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_hidden',
								'type'		=> 'select',
								'std'		=> 'hide',
								'lockable'	=> $lockable,
								'required'	=> $required,
								'subtype'	=> array(
													__( 'Show all products', 'avia_framework' )			=> '',
													__( 'Hide hidden products', 'avia_framework' )		=> 'hide',
													__( 'Show hidden products only', 'avia_framework' )	=> 'show'
												)
							);
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Featured Products visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products depending on checkbox &quot;This is a featured product&quot; in catalog visibility. Can be set independently for each product: Edit Product -&gt Publish panel -&gt Catalog visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_featured',
								'type'		=> 'select',
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> $required,
								'subtype'	=> array(
													__( 'Show all products', 'avia_framework' )				=> '',
													__( 'Hide featured products', 'avia_framework' )		=> 'hide',
													__( 'Show featured products only', 'avia_framework' )	=> 'show'
												)
							);
					
			$template[] = array(
								'name'		=> __( 'WooCommerce Sorting Options', 'avia_framework' ),
								'desc'		=> __( 'Here you can choose how to sort the products. Default setting can be set at Dashboard -&gt; Appearance -&gt; Customize -&gt; WooCommerce -&gt; Product Catalog -&gt; Default Product Sorting', 'avia_framework' ),
								'id'		=> 'prod_order_by',
								'type'		=> 'select',
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> $required,
								'subtype'	=> $sort
							);
				
			$template[] = array(
								'name'		=> __( 'WooCommerce Sorting Order', 'avia_framework' ),
								'desc'		=> __( 'Here you can choose the order of the result products. Default setting can be set at Dashboard -&gt; Appearance -&gt; Customize -&gt; WooCommerce -&gt; Product Catalog -&gt; Default Product Sorting', 'avia_framework' ),
								'id'		=> 'prod_order',
								'type'		=> 'select',
								'std'		=> '',
								'lockable'	=> $lockable,
								'required'	=> $required,
								'subtype'	=> array( 
													__( 'Use default (defined at Dashboard -&gt; Customize -&gt; WooCommerce)', 'avia_framework' ) => '',
													__( 'Ascending', 'avia_framework' )			=> 'ASC',
													__( 'Descending', 'avia_framework' )		=> 'DESC'
												)
							);
			
			return $template;
		}
		
		
		/**
		 *  Select boxes for WooCommerce Options for product elements
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function wc_options_products( array $element )
		{
			$lockable = isset( $element['lockable'] ) ? $element['lockable'] : false;
			
			$sort = array( 
							__( 'Use default (defined at Dashboard -&gt; Customize -&gt; WooCommerce)', 'avia_framework' )	=> '0',
							__( 'Sort alphabetically', 'avia_framework' )			=> 'title',
							__( 'Sort by most recent', 'avia_framework' )			=> 'date',
							__( 'Sort by price', 'avia_framework' )					=> 'price',
							__( 'Sort by popularity', 'avia_framework' )			=> 'popularity',
							__( 'Sort randomly', 'avia_framework' )					=> 'rand',
							__( 'Sort by menu order and name', 'avia_framework' )	=> 'menu_order',
							__( 'Sort by average rating', 'avia_framework' )		=> 'rating',
							__( 'Sort by relevance', 'avia_framework' )				=> 'relevance',
							__( 'Sort by Product ID', 'avia_framework' )			=> 'id'
						);
			
			$sort_std = '0';
			
			if( ! empty( $element['sort_dropdown'] ) )
			{
				$sort = array_merge( array( __( 'Let user pick by displaying a dropdown with sort options (default value is defined at Default product sorting)', 'avia_framework' ) => 'dropdown' ), $sort );
				$sort_std = 'dropdown';
			}
			
			/**
			 * @since 4.5.7.1
			 * @param array $sort
			 * @param array $element
			 * @return array
			 */
			$sort = apply_filters( 'avf_alb_wc_options_non_products_sort', $sort, $element );
			
			$template = array();
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Out of Stock Product visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products. Default setting can be set at Woocommerce -&gt Settings -&gt Products -&gt Inventory -&gt Out of stock visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_visible',
								'type'		=> 'select',
								'std'		=> '',
								'lockable'	=> $lockable,
								'subtype'	=> array(
													__( 'Use default WooCommerce Setting (Settings -&gt; Products -&gt; Out of stock visibility)', 'avia_framework' ) => '',
													__( 'Hide products out of stock', 'avia_framework' )	=> 'hide',
													__( 'Show products out of stock', 'avia_framework' )	=> 'show'
												)
							);
					
				
			$template[] = array(
								'name'		=> __( 'WooCommerce Hidden Products visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products depending on catalog visibility. Can be set independently for each product: Edit Product -&gt Publish panel -&gt Catalog visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_hidden',
								'type'		=> 'select',
								'std'		=> '',
								'lockable'	=> $lockable,
								'subtype'	=> array(
													__( 'Show all products', 'avia_framework' )			=> '',
													__( 'Hide hidden products', 'avia_framework' )		=> 'hide',
													__( 'Show hidden products only', 'avia_framework' )	=> 'show'
												)
							);
				
			$template[] = array(
								'name'		=> __( 'WooCommerce Featured Products visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products depending on checkbox &quot;This is a featured product&quot; in catalog visibility. Can be set independently for each product: Edit Product -&gt Publish panel -&gt Catalog visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_featured',
								'type'		=> 'select',
								'std'		=> '',
								'lockable'	=> $lockable,
								'subtype'	=> array(
													__( 'Show all products', 'avia_framework' )				=> '',
													__( 'Hide featured products', 'avia_framework' )		=> 'hide',
													__( 'Show featured products only', 'avia_framework' )	=> 'show'
												)
							);
				
			$template[] = array(
								'name'		=> __( 'WooCommerce Sidebar Filters', 'avia_framework' ),
								'desc'		=> __( 'Allow to filter products for this element using the 3 WooCommerce sidebar filters: Filter Products by Price, Rating, Attribute. These filters are only shown on the selected WooCommerce Shop page (WooCommerce -&gt; Settings -&gt; Products -&gt; General -&gt; Shop Page) or on product category pages. You may also use a custom widget area for the sidebar.', 'avia_framework' ),
								'id'		=> 'wc_prod_additional_filter',
								'type'		=> 'select',
								'std'		=> '',
								'lockable'	=> $lockable,
								'subtype'	=> array(
													__( 'Ignore filters', 'avia_framework' )	=> '',
													__( 'Use filters', 'avia_framework' )		=> 'use_additional_filter'
												)
							);		
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Sorting Options', 'avia_framework' ),
								'desc'		=> __( 'Here you can choose how to sort the products. Default setting can be set at Dashboard -&gt; Appearance -&gt; Customize -&gt; WooCommerce -&gt; Product Catalog -&gt; Default Product Sorting', 'avia_framework' ),
								'id'		=> 'sort',
								'type'		=> 'select',
								'std'		=> $sort_std,
								'lockable'	=> $lockable,
								'subtype'	=> $sort
							);
									
			$template[] = array(
								'name'		=> __( 'WooCommerce Sorting Order', 'avia_framework' ),
								'desc'		=> __( 'Here you can choose the order of the result products. Default setting can be set at Dashboard -&gt; Appearance -&gt; Customize -&gt; WooCommerce -&gt; Product Catalog -&gt; Default Product Sorting', 'avia_framework' ),
								'id'		=> 'prod_order',
								'type'		=> 'select',
								'std'		=> '',
								'lockable'	=> $lockable,
								'subtype'	=> array( 
													__( 'Use default (defined at Dashboard -&gt; Customize -&gt; WooCommerce)', 'avia_framework' ) => '',
													__( 'Ascending', 'avia_framework' )			=> 'ASC',
													__( 'Descending', 'avia_framework' )		=> 'DESC'
												)
							);
			
			return $template;
		}
		
		/**
		 * Adds theme defined html templates for ALB
		 * 
		 * @since 4.6.4
		 */
		protected function set_predefined_html_templates()
		{
			$c  = '';
			
			$c .=	'<div class="avia-flex-element">';
			$c .=		__( 'This element will stretch across the whole screen by default.', 'avia_framework' ) . '<br/>';
			$c .=		__( 'If you put it inside a color section or column it will only take up the available space', 'avia_framework' );
			$c .=		'<div class="avia-flex-element-2nd">' . __( 'Currently:', 'avia_framework' );
			$c .=			'<span class="avia-flex-element-stretched">&laquo; ' . __( 'Stretch fullwidth', 'avia_framework') . ' &raquo;</span>';
			$c .=			'<span class="avia-flex-element-content">| ' . __( 'Adjust to content width', 'avia_framework' ) . ' |</span>';
			$c .=		'</div>';
			$c .=	'</div>';
			
			$this->html_templates['alb_element_fullwidth_stretch'] = $c;
		}
	
	}
	
	/**
	 * Returns the main instance of Avia_Popup_Templates to prevent the need to use globals
	 * 
	 * @since 4.3.2
	 * @return Avia_Popup_Templates
	 */
	function AviaPopupTemplates() 
	{
		return Avia_Popup_Templates::instance();
	}
	
}		//	end Avia_Popup_Templates

