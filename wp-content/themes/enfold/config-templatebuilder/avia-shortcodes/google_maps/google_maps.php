<?php
/**
 * Google Maps Shortcode
 * 
 * Allows to display a Google Map with one or more locations
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_gmaps' ) ) 
{
	class avia_sc_gmaps extends aviaShortcodeTemplate
	{
		/**
		 *
		 * @var int 
		 */
		static $map_count = 0;
		
		/**
		 *
		 * @var array 
		 */
		static $js_vars = array();
			
		/**
		 * Create the config array for the shortcode button
		 */
		public function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['is_fullwidth']	= 'yes';
			$this->config['self_closing']	= 'no';

			$this->config['name']			= __( 'Google Map', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-maps.png';
			$this->config['order']			= 5;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_google_map';
			$this->config['shortcode_nested'] = array( 'av_gmap_location' );
			$this->config['tooltip'] 	    = __( 'Display a google map with one or multiple locations', 'avia_framework' );
			$this->config['drag-level'] 	= 3;
			$this->config['disabling_allowed'] = true;
			$this->config['disabled']		= array(
													'condition'	=> Av_Google_Maps()->is_loading_prohibited(), 
													'text'		=> __( 'This element has been disabled in your theme options. You can enable it in Enfold &raquo; Google Services.', 'avia_framework' )
												);
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}

			
		public function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-maps', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/google_maps/google_maps.css', array( 'avia-layout' ), false );

			/**
			 * Necessary js is handled by class av_google_maps
			 */
		}
			

		/**
		 * Popup Elements
		 *
		 * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
		 * opens a modal window that allows to edit the element properties
		 *
		 * @return void
		 */
		public function popup_elements()
		{
			//if the element is disabled
			if( true === $this->config['disabled']['condition'] )
			{
				$this->elements = array(
					
					array(	
								'type'			=> 'template',
								'template_id'	=> 'element_disabled',
								'args'			=> array(
														'desc'		=> $this->config['disabled']['text'],
														'anchor'	=> 'goto_google'
													)
							),
						);

				return;
			}

			
			$this->elements = array(
				
				array(
						'type' 	=> 'tab_container', 
						'nodescription' => true
					),
						
				array(
						'type' 	=> 'tab',
						'name'  => __( 'Content', 'avia_framework' ),
						'nodescription' => true
					),
				
					array(
							'type'		=> 'template',
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array( 
													$this->popup_key( 'content_locations' ),
													$this->popup_key( 'content_controls' ),
													$this->popup_key( 'content_settings' )
												),
							'nodescription' => true
						),
				
				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),
				
				array(
						'type' 	=> 'tab',
						'name'  => __( 'Styling', 'avia_framework' ),
						'nodescription' => true
					),
				
					array(	
							'type'			=> 'template',
							'template_id'	=> $this->popup_key( 'styling_colors' )
						),
					
				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),
				
				array(
						'type' 	=> 'tab',
						'name'  => __( 'Advanced', 'avia_framework' ),
						'nodescription' => true
					),
				
					array(
							'type' 	=> 'toggle_container',
							'nodescription' => true
						),

						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_privacy' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> 'screen_options_toggle'
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> 'developer_options_toggle',
								'args'			=> array( 'sc' => $this )
							),
				
					array(
							'type' 	=> 'toggle_container_close',
							'nodescription' => true
						),
				
				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),

				array(
						'type' 	=> 'tab_container_close',
						'nodescription' => true
					)
					
				);

		}
		
				/**
		 * Create and register templates for easier maintainance
		 * 
		 * @since 4.6.4
		 */
		protected function register_dynamic_templates()
		{
			$this->register_modal_group_templates();
			
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name'			=> __( 'Add/Edit Map Locations', 'avia_framework' ),
							'desc'			=> __( 'Here you can add, remove and edit the map locations for your Google Map.', 'avia_framework' ),
							'type'			=> 'modal_group',
							'id'			=> 'content',
							'modal_title'	=> __( 'Edit Location', 'avia_framework' ),
							'std'			=> array( array( 'address' => '', 'type' => 'text', 'check' => 'is_empty' ) ),
							'subelements' 	=> $this->create_modal()
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Google Maps Locations', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_locations' ), $template );
			
			
			
			$c = array(
						array(	
							'name' 	=> __( 'Display Zoom Control?', 'avia_framework' ),
							'desc' 	=> __( 'Check to display the controls at the right side of the map', 'avia_framework' ) ,
							'id' 	=> 'zoom_control',
							'std' 	=> 'active',
							'type' 	=> 'checkbox'
						),
					
						array(
							'name' 	=> __( 'Display Map Type Selector', 'avia_framework' ),
							'desc' 	=> __( 'Choose to display the map type selector dropdown at the left of your map', 'avia_framework' ),
							'id' 	=> 'maptype_control',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Hide', 'avia_framework' )									=>	'',
												__( 'Dropdown', 'avia_framework' )								=>	'dropdown',
												__( 'Horizontal buttons', 'avia_framework' )					=>	'horizontal',
												__( 'Responsive (choosen by Google API)', 'avia_framework' )	=>	'default',
											)
							),
				
						array(	
							'name' 	=> __( 'Display Streetview Activation Control?', 'avia_framework' ),
							'desc' 	=> __( 'Check to display the control to activate streetview at the right side of the map', 'avia_framework' ) ,
							'id' 	=> 'streetview_control',
							'std' 	=> 'active',
							'type' 	=> 'checkbox'
						),
					
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Map Controls', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_controls' ), $template );
			
			
			$c = array(
						array(
							'name' 	=> __( 'Zoom Level', 'avia_framework' ),
							'desc' 	=> __( 'Choose the zoom of the map on a scale from 1 (very far away) to 19 (very close)', 'avia_framework' ),
							'id' 	=> 'zoom',
							'type' 	=> 'select',
							'std' 	=> '16',
							'subtype'	=> AviaHtmlHelper::number_array( 1, 19, 1, array( __( 'Set Zoom level automatically to show all markers', 'avia_framework' ) => 'auto' ) )
						),
						
						array(
							'name' 	=> __( 'Choose initial map view', 'avia_framework' ),
							'desc' 	=> __( 'Choose the initial map view after loading the map', 'avia_framework' ),
							'id' 	=> 'maptype_id',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Roadmap', 'avia_framework' )		=> '',
												__( 'Satellite', 'avia_framework' )		=> 'SATELLITE',
												__( 'Hybrid', 'avia_framework' )		=> 'HYBRID',
												__( 'Terrain', 'avia_framework' )		=> 'TERRAIN',
											)
						),
					
				
					);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Map Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_settings' ), $template );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Map height', 'avia_framework' ),
							'desc' 	=> __( "You can either define a fixed height in pixel like '300px' or enter a width/height ratio like 16:9", 'avia_framework' ),
							'id' 	=> 'height',
							'type' 	=> 'input',
							'std' 	=> '400px',
						),
						
						array(
							'name' 	=> __( 'Color Saturation', 'avia_framework' ),
							'desc' 	=> __( 'Choose the saturation of your map', 'avia_framework' ),
							'id' 	=> 'saturation',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Full color fill', 'avia_framework' )			=> 'fill',
												__( 'Oversaturated', 'avia_framework' )				=> '100',
												__( 'Slightly oversaturated', 'avia_framework' )	=> '50',
												__( 'Normal Saturation', 'avia_framework' )			=> '',
												__( 'Muted colors', 'avia_framework' )				=> '-50',
												__( 'Greyscale', 'avia_framework' )					=> '-100'
											)
						),
						
						array(
							'name' 	=> __( 'Custom Overlay Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom color for your Map here. The map will be tinted with that color. Leave empty if you want to use the default map color', 'avia_framework' ),
							'id' 	=> 'hue',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
						)
				
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_colors' ), $c );
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Link to Google Maps', 'avia_framework' ),
							'desc' 	=> __( 'Choose the way the map is loaded', 'avia_framework' ),
							'id' 	=> 'google_link',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Show Google Maps immediately', 'avia_framework' )				=> '',
												__( 'User must accept to show Google Maps', 'avia_framework' )		=> 'confirm_link',
												__( 'Only open Google Maps in new window', 'avia_framework' )		=> 'page_only'
											)
						),
					
						array(
							'name' 	=> __( 'Button text confirm to load Google Maps', 'avia_framework' ),
							'desc' 	=> __( 'Enter the text to display that user has to accept to load the map.', 'avia_framework' ),
							'id' 	=> 'confirm_button',
							'type' 	=> 'input',
							'std' 	=> __( 'Click to load Google Maps', 'avia_framework' ),
							'required'	=> array( 'google_link', 'equals', 'confirm_link' ),
						),
					
						array(
							'name' 	=> __( 'Direct link to Google Maps page', 'avia_framework' ),
							'desc' 	=> __( 'Enter the text to display that we link to Google Maps page.', 'avia_framework' ),
							'id' 	=> 'page_link_text',
							'type' 	=> 'input',
							'std' 	=> __( 'Open Google Maps in a new window', 'avia_framework' ),
							'required'	=> array( 'google_link', 'equals', 'page_only' ),
							),
					
						array(
							'name'		=> __( 'Fallback Image to replace Google Maps', 'avia_framework' ),
							'desc'		=> __( 'Upload or select a fallback image that is displayed instead of Google Maps or until the map is loaded', 'avia_framework' ),
							'id'		=> 'google_fallback',
							'type'		=> 'image',
							'title'		=> __( 'Insert Fallback Image', 'avia_framework' ),
							'button'	=> __( 'Insert', 'avia_framework' ),
							'std'		=> '',
						),
					
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Conditional Load', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_privacy' ), $template );
			
			
		}
		
		/**
		 * Creates the modal popup for a single entry
		 * 
		 * @since 4.6.4
		 * @return array
		 */
		protected function create_modal()
		{
			$elements = array(
				
				array(
						'type' 	=> 'tab_container', 
						'nodescription' => true
					),
						
				array(
						'type' 	=> 'tab',
						'name'  => __( 'Content', 'avia_framework' ),
						'nodescription' => true
					),
				
					array(
							'type'			=> 'template',
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array(
													$this->popup_key( 'modal_content_location' ),
													$this->popup_key( 'modal_content_marker' )
												),
							'nodescription' => true
						),
				
				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),
				
				
				array(
						'type' 	=> 'tab_container_close',
						'nodescription' => true
					)
				
				
				);
			
			return $elements;
		}
		
		/**
		 * Register all templates for the modal group popup
		 * 
		 * @since 4.6.4
		 */
		protected function register_modal_group_templates()
		{
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Full Adress', 'avia_framework' ),
							'desc' 	=> __( "Enter the Address, then hit the 'Fetch Coordinates' Button. If the address was found the coordinates will be displayed", 'avia_framework' ),
							'id' 	=> 'address',
							'std' 	=> '',
							'type' 	=> 'gmap_adress'
						),
									
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Location', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_location' ), $template );
			
			
			/**
			 * Layout Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Marker Tooltip', 'avia_framework' ),
							'desc' 	=> __( 'Enter some text here. If the user clicks on the marker the text will be displayed', 'avia_framework' ),
							'id' 	=> 'content',
							'type' 	=> 'textarea',
							'std' 	=> '',
						),
			                        
						array(	
							'name' 	=> __( 'Display Tooltip by default', 'avia_framework' ),
							'desc' 	=> __( 'Check to display the tooltip by default. If unchecked user must click the marker to show the tooltip', 'avia_framework' ),
							'id' 	=> 'tooltip_display',
							'std' 	=> '',
							'required'	=> array( 'content', 'not', '' ),
							'type' 	=> 'checkbox'
						),

						array(
							'name' 	=> __( 'Custom Map Marker Image', 'avia_framework' ),
							'desc' 	=> __( 'Use a custom Image as marker. (make sure that you use a square image, otherwise it will be cropped)', 'avia_framework' ) . '<br/><small>' . __( 'Leave empty if you want to use the default marker', 'avia_framework' ) . '</small>',
							'id' 	=> 'marker',
							'fetch' => 'id',
							'type' 	=> 'image',
							'title' => __( 'Insert Marker Image', 'avia_framework' ),
							'std' 	=> '',
							'button'	=> __( 'Insert','avia_framework' )
						),
									
						array(
							'name' 	=> __( 'Custom Map Marker Image Size', 'avia_framework' ),
							'desc' 	=> __( 'How big should the marker image be displayed in height and width.', 'avia_framework' ),
							'id' 	=> 'imagesize',
							'type' 	=> 'select',
							'std' 	=> '40',
							'required' 	=> array( 'marker', 'not', '' ),
							'subtype' => array(
												__( '20px * 20px', 'avia_framework' )	=> '20',
												__( '30px * 30px', 'avia_framework' )	=> '30',
												__( '40px * 40px', 'avia_framework' )	=> '40',
												__( '50px * 50px', 'avia_framework' )	=> '50',
												__( '60px * 60px', 'avia_framework' )	=> '60',
												__( '70px * 70px', 'avia_framework' )	=> '70',
												__( '80px * 80px', 'avia_framework' )	=> '80',
											),
						)
									
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Marker And Tooltip', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_marker' ), $template );
			
		}
			
		/**
		 * Editor Element - this function defines the visual appearance of an element on the AviaBuilder Canvas
		 * Most common usage is to define some markup in the $params['innerHtml'] which is then inserted into the drag and drop container
		 * Less often used: $params['data'] to add data attributes, $params['class'] to modify the className
		 *
		 *
		 * @param array $params			this array holds the default values for $content and $args. 
		 * @return array				the return array usually holds an innerHtml key that holds item specific markup.
		 */
		public function editor_element( $params )
		{	
			$element = $this->get_popup_element_by_id( 'google_link' );

			$info = '';

			/**
			 * Element has been disabled ??
			 */
			if( false !== $element )
			{
				$google_links = $element['subtype'];

				$update_template =	'<span class="av-gmap-{{google_link}}">';

				foreach( $google_links as $info => $google_link )
				{
					$update_template .=		'<span class="av-gmap-link-' . $google_link . '">' . $info . '</span>';
				}

				$update_template .=	'</span>';

				$update	= $this->update_template( 'google_link', $update_template );

				$selected = empty( $params['args']['google_link'] ) ? '' : $params['args']['google_link'];
				$template = str_replace('{{google_link}}', $selected, $update_template );

				$info = ' -  <span ' . $update . '>' . $template . '</span>';
			}

			$params['innerHtml']  =	"<img src='{$this->config['icon']}' title='{$this->config['name']}' />";
			$params['innerHtml'] .=	"<div class='avia-element-label av-google-maps'>{$this->config['name']} {$info}</div>";
			$params['innerHtml'] .=	AviaPopupTemplates()->get_html_template( 'alb_element_fullwidth_stretch' );
			
			return $params;
		}

		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * 
		 * @param array $params			this array holds the default values for $content and $args. 
		 * @return array				the return array usually holds an innerHtml key that holds item specific markup.
		 */
		public function editor_sub_element( $params )
		{
			$template = $this->update_template('address', __('Address', 'avia_framework' ). ': {{address}}');

			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_title_container' {$template}>" . __( 'Address', 'avia_framework' ) . ": {$params['args']['address']}</div>";

			return $params;
		}
			
			
			
		/**
		 * Frontend Shortcode Handler
		 *
		 * @param array $atts					array of attributes
		 * @param string $content				text within enclosing form of shortcode element 
		 * @param string $shortcodename			the shortcode found, when == callback name
		 * @return string						$output returns the modified html string 
		 */
		public function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
			/**
			 * If disabled with filter - only show a message to editors 
			 */
			if( Av_Google_Maps()->is_loading_prohibited() )
			{
				$out = '';

				if( current_user_can( 'edit_posts' ) )
				{
					$out .=	'<span class="av-shortcode-disabled-notice">';
					$out .=		'<strong>' . __( 'Admin notice for:', 'avia_framework' ) . '</strong><br>' . $this->config['name'] . '<br><br>';
					$out .=		__( 'This element was disabled with theme option', 'avia_framework' ) . '<br>';
					$out .=	'</span>';
				}

				return $out;
			}

				
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			$atts = shortcode_atts( array(
						'id'					=> '',
						'height'				=> '',
						'hue'					=> '',
						'saturation'			=> '',
						'zoom'					=> '',
						'zoom_control'			=> '',
						'streetview_control'	=> '',
						'mobile_drag_control'	=> '',
						'maptype_control'		=> '',
						'maptype_id'			=> '',
						'google_link'			=> '',
						'confirm_button'		=> '',
						'page_link_text'		=> '',
						'google_fallback'		=> '',
						'handle'				=> $shortcodename,
						'content'				=> ShortcodeHelper::shortcode2array( $content, 1 )

					), $atts, $this->config['shortcode'] );

			$atts['zoom_control'] 			= empty( $atts['zoom_control'] ) ? false : true;
			$atts['pan_control']  			= empty( $atts['pan_control'] ) ? false : true;
			$atts['mobile_drag_control']  	= empty( $atts['mobile_drag_control'] ) ? true : false;
			$atts['streetview_control']  	= empty( $atts['streetview_control'] ) ? false : true;

			extract( $atts );
				
			/**
			 * Allow to change the conditional display setting - e.g. if user is opt in and allows to connect directly
			 * 
			 * @since 4.4
			 * @param string $google_link			'' | 'confirm_link' | 'page_only'
			 * @param string $context
			 * @param mixed $object
			 * @param array $atts
			 * @param array|string $meta
			 * @return string
			 */
			$original_google_link = $google_link;
			$google_link = apply_filters( 'avf_conditional_setting_external_links', $google_link, __CLASS__, $this, $atts, $meta );
			if( ! in_array( $google_link, array( '', 'confirm_link', 'page_only' ) ) )
			{
				$google_link = $original_google_link;
			}

			$output  		= '';
			$class 			= '';
			$skipSecond 	= false;
			avia_sc_gmaps::$map_count = Av_Google_Maps()->get_maps_count() + 1;
				
			$params['class'] = "avia-google-maps avia-google-maps-section main_color {$av_display_classes} {$meta['el_class']} {$class}";
			$params['open_structure'] = false;
			$params['id'] = AviaHelper::save_string( $meta['custom_id_val'], '-', 'avia-google-map-nr-' . avia_sc_gmaps::$map_count );


			//we dont need a closing structure if the element is the first one or if a previous fullwidth element was displayed before
			if( isset( $meta['index'] ) && $meta['index'] == 0 ) 
			{
				$params['close'] = false;
			}
			
			if( ! empty( $meta['siblings']['prev']['tag'] ) && in_array( $meta['siblings']['prev']['tag'], AviaBuilder::$full_el_no_section ) ) 
			{
				$params['close'] = false;
			}

			$html_fallback_url = '';	
			if( ! empty( $google_fallback ) )
			{
				$image = is_numeric( $google_fallback ) ? wp_get_attachment_image_src( $google_fallback, 'large' ) : $google_fallback;
				if( is_array( $image ) ) 
				{
					$image = $image[0];
				}

				if( ! empty( $image ) )
				{
					$html_fallback_url .= 'background-image:url(' . $image . ');';
				}
			}
				
			$html_overlay = '';
			$location = $content[0]['attr'];
			if( empty( $location['address'] ) )
			{
				$adress1 = ! empty( $location['lat'] ) ? $location['lat'] : '';
				$adress2 = ! empty( $location['long'] ) ? $location['long'] : '';
			}
			else
			{
				if( ! isset( $location['address'] ) ) 
				{ 
					$location['address'] = '';
				}
				if( ! isset( $location['postcode'] ) ) 
				{ 
					$location['postcode'] = '';
				}
				if( ! isset( $location['city'] ) ) 
				{
					$location['city'] = '';
				}
				if( ! isset( $location['state'] ) ) 
				{ 
					$location['state'] = '';
				}
				if( ! isset( $location['country'] ) ) 
				{ 
					$location['country'] = '';
				}

				$adress1 = $location['address'] . ' ' . $location['postcode'] . ' ' . $location['city'] . ' ' . $location['state'] . ' ' . $location['country'];
				$adress2 = '';
			}
			
			$url = av_google_maps::api_destination_url( $adress1, $adress2 );
				
			if( ( 'confirm_link' == $google_link ) || ( 'page_only' == $google_link ) )
			{
				$button_class = empty( $html_fallback_url ) ? ' av_text_confirm_link_visible' : '';

				$text_overlay = '';
				if( 'confirm_link' == $google_link )
				{
					$html_overlay = '<a href="#" class="av_gmaps_confirm_link av_text_confirm_link' . $button_class . '">';
					$text_overlay = esc_html( $confirm_button );
				}
				else
				{
					$html_overlay .= '<a class="av_gmaps_page_only av_text_confirm_link' . $button_class . '" href="' . esc_url( $url ) . '" target="_blank">';
					$text_overlay = esc_html( $page_link_text );
				}

				$html_overlay .= '<span>' . $text_overlay . '</span></a>';

				/**
				 * @since 4.4.2
				 * @param string		output string
				 * @param object		context
				 * @param array
				 * @param array
				 */
				$filter_args = array(
							$html_overlay,
							$this,
							$atts,
							$meta
					);
				$html_overlay = apply_filters_ref_array( 'avf_google_maps_confirm_overlay', $filter_args );
			}
				
			//an overlay that might be necessary if the user disables the map by the use of our [av_privacy_google_maps] function
			$by_browser_disabled_overlay = '';
			$by_browser_disabled_overlay .= '<a class="av_gmaps_browser_disabled av_text_confirm_link av_text_confirm_link_visible" href="' . $url . '" target="_blank">';
			$by_browser_disabled_overlay .= __( 'Maps were disabled by the visitor on this site. Click to open the map in a new window.', 'avia_framework' );
			$by_browser_disabled_overlay .= '</a>';

			$map_id = '';
			if( 'page_only' != $google_link )
			{
				$add = empty( $google_link ) ? 'unconditionally' : 'delayed';

				/**
				 * Allow to filter Google Maps data array
				 * 
				 * @since 4.4
				 * @param array $content
				 * @param string context
				 * @param object
				 * @param array additional args
				 * @return array
				 */
				$content = apply_filters( 'avf_google_maps_data', $content, __CLASS__, $this, array( $atts, $meta ) );

				$map_id = $this->add_map( $content, $atts, $add );
			}
				
			switch( $google_link )
			{
				case 'confirm_link':
					$show_class = 'av_gmaps_show_delayed';
					break;
				case 'page_only':
					$show_class = 'av_gmaps_show_page_only';
					break;
				case '':
				default:
					$show_class = 'av_gmaps_show_unconditionally';
					break;
			}

			if( empty( $html_fallback_url ) )
			{
				$show_class .= ' av-no-fallback-img';
			}

			$styles = $this->define_height( $height );
			if( ! empty( $html_fallback_url ) )
			{
				$styles[] = $html_fallback_url;
			}
				
			$style = '';
			if( ! empty( $styles ) )
			{
				$style = " style='" . implode( ' ', $styles ) . "'";
			}

			$add_id = ShortcodeHelper::is_top_level() ? '' : $meta['custom_el_id'];
			$add_css = ShortcodeHelper::is_top_level() ? '' : $meta['custom_class'];

			$out =	"<div {$add_id} class='av_gmaps_sc_main_wrap av_gmaps_main_wrap {$add_css}'>";

			if( empty( $map_id ) )
			{
				$out .=		"<div class='avia-google-map-container avia-google-map-sc {$show_class} {$av_display_classes}' {$style}>";
			}
			else
			{
				$out .=		"<div id='{$map_id}' class='avia-google-map-container avia-google-map-sc {$show_class} {$av_display_classes}' data-mapid='{$map_id}' {$style}>";
			}

			$out .= 			$by_browser_disabled_overlay;
			$out .=				$html_overlay;
			$out .=			'</div>';

			$out .=	'</div>';
				
			//if the element is nested within a section or a column dont create the section shortcode around it
			if( ! ShortcodeHelper::is_top_level() ) 
			{
				return $out;
			}

			$output .=		avia_new_section( $params );
			$output .=		$out;
			$output .=	'</div>'; //close section

			//if the next tag is a section dont create a new section from this shortcode
			if( ! empty( $meta['siblings']['next']['tag'] ) && in_array( $meta['siblings']['next']['tag'],  AviaBuilder::$full_el ) )
			{
				$skipSecond = true;
			}

			//if there is no next element dont create a new section.
			if( empty( $meta['siblings']['next']['tag'] ) )
			{
				$skipSecond = true;
			}

			if( empty( $skipSecond ) ) 
			{
				$output .= avia_new_section( array( 'close' => false, 'id' => 'after_full_slider_' . avia_sc_gmaps::$map_count ) );
			}

			return $output;
		}
			
		/**
		 * Returns the height styling
		 * 
		 * @since 4.4
		 * @param string $height
		 * @return array
		 */
		protected function define_height( $height )
		{	
			$style = array();

			//	apply a ratio via bottom padding
			if( strpos( $height, ':' ) !== false )
			{
				$height = explode( ':', $height );
				$height = ( 100 / (int) $height[0]) * $height[1];
				$style[] = "padding-bottom: {$height}%;";
			}
			else // set a fixed height
			{
				$height = (int) $height;
				$style[] = "height: {$height}px;";
			}

			return $style;
		}
			
		/**
		 * Adds the map to the map list and returns the id for the map
		 * 
		 * @since 4.3.2
		 * @param array $shortcodes
		 * @param array $atts
		 * @param string
		 * @return string
		 */
		protected function add_map( array $shortcodes, array $atts, $add )
		{
			$data = array();
			$data['marker'] = array();

			foreach( $shortcodes as $key => $shortcode )
			{
				$data['marker'][ $key ] = array();

				foreach( $shortcode['attr'] as $attr_key => $attr )
				{
					$data['marker'][ $key ][ $attr_key ] = $attr;
				}

				//get attachment data
				if( ! empty( $shortcode['content'] ) ) 
				{	
					$data['marker'][ $key ]['content'] = wpautop(ShortcodeHelper::avia_remove_autop( $shortcode['content'], true ) );
				}

				//get attachment data
				if( ! empty($shortcode['attr']['marker'] ) ) 
				{
					$image = wp_get_attachment_image_src( $shortcode['attr']['marker'], 'thumbnail' );
					if( ! empty( $image[0] ) )
					{
						$data['marker'][ $key ]['icon'] = $image[0];
					}
				}
			}

			$first_level = array( 'hue', 'zoom', 'saturation', 'zoom_control', 'streetview_control', 'pan_control', 'mobile_drag_control', 'maptype_control', 'maptype_id' );
			foreach( $first_level as $var )
			{
				$data[ $var ] = $atts[$var];
			}

			$map_id = Av_Google_Maps()->add_map( $data, $add );
			return $map_id;
		}

	}
}

