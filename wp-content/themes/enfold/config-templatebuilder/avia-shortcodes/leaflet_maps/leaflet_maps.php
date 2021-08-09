<?php
/* 
 * Implements the open source Leaflet Open Street Maps.
 * 
 * @since 4.8.2
 */

if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( 'Leaflet_Map' ) )
{
	if( ! function_exists( 'av_leaflet_map_fallback' ) )
	{
		function av_leaflet_map_fallback()
		{
			if( ! current_user_can( 'edit_posts' ) )
			{
				return '';
			}
			
			$url = '<a href="https://wordpress.org/plugins/leaflet-map/">';
			
			$out  = '<p>';
			$out .=		sprintf( __( 'Please install and activate the plugin %1$sLeaflet Map%2$s to display your maps.', 'avia_framework' ), $url, '</a>' );
			$out .= '</p>';
			
			return $out;
		}
		
		add_shortcode( 'av_leaflet_map', 'av_leaflet_map_fallback' );
	}
	
	return;
}

if ( ! class_exists( 'avia_sc_leaflet_map' ) ) 
{
	
	class avia_sc_leaflet_map extends aviaShortcodeTemplate
	{

		/**
		 * Create the config array for the shortcode button
		 * 
		 * @since 4.8.2
		 */
		public function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['is_fullwidth']	= 'yes';
			$this->config['self_closing']	= 'no';
//			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'OSM - Leaflet Map', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-leaflet-maps.png';
			$this->config['order']			= 5;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_leaflet_map';
			$this->config['shortcode_nested'] = array( 'av_leaflet_marker_item' );
			$this->config['tooltip']		= __( 'Displays a map based on OpenStreetMaps and Leaflet Map plugin', 'avia_framework' );
			$this->config['drag-level'] 	= 3;
			$this->config['preview'] 		= false;
			$this->config['disabling_allowed'] = true;
			$this->config['disabled']		= array(
													'condition'	=> ! AviaLeafletMaps()->is_enabled(), 
													'text'		=> __( 'This element has been disabled in your theme options. You can enable it in Enfold -&raquo; Theme Extensions -&raquo; Leaflet Maps.', 'avia_framework' )
												);
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
			$this->config['name_item']		= __( 'OSM Marker Item', 'avia_framework' );
			$this->config['tooltip_item']	= __( 'A OSM Map Marker', 'avia_framework' );
        }
		
		/**
		 * @since 4.8.2
		 */
		public function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-leaflet-maps', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/leaflet_maps/leaflet_maps.css', array( 'avia-layout' ), false );
		}

		/**
		 * Popup Elements
		 *
		 * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
		 * opens a modal window that allows to edit the element properties
		 *
		 * @since 4.8.2
		 * @return void
		 */
		public function popup_elements()
		{

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
							'type'			=> 'template',
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array( 
													$this->popup_key( 'content_markers' ),
													$this->popup_key( 'content_initial' ),
													$this->popup_key( 'content_interaction' ),
													$this->popup_key( 'content_zoom' )
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
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array( 
													$this->popup_key( 'styling_resolution' ),
													$this->popup_key( 'styling_map_size' )
												),
							'nodescription'	=> true
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
								'template_id'	=> $this->popup_key( 'advanced_attributes' )
							),
			   
						array(
								'type'			=> 'template',
								'template_id'	=> 'screen_options_toggle',
								'lockable'		=> true,
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
				
//				array(	
//						'type'			=> 'template',
//						'template_id'	=> 'element_template_selection_tab',
//						'args'			=> array( 'sc' => $this )
//					),

				array(
						'type' 	=> 'tab_container_close',
						'nodescription' => true
					)

			);

		}

		/**
		 * Create and register templates for easier maintainance
		 * 
		 * @since 4.8.2
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
							'name'			=> __( 'Add/Edit Marker Locations', 'avia_framework' ),
							'desc'			=> __( 'Here you can add, remove and edit the marker locations for your map.', 'avia_framework' ),
							'type'			=> 'modal_group',
							'id'			=> 'content',
							'modal_title'	=> __( 'Edit Location', 'avia_framework' ),
							'std'			=> array( 
													array( 
														'geo_street'		=> 'Stephansplatz 1', 
														'geo_postalcode'	=> '1010', 
														'geo_city'			=> 'Vienna', 
														'geo_country'		=> 'Austria' 
													) 
												),
							'subelements' 	=> $this->create_modal()
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Markers', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_markers' ), $template );
			
			$c = array(
						array(	
							'type'				=> 'template',
							'template_id'		=> 'geolocation_toggle',
							'title'				=> __( 'Initial Map Position', 'avia_framework' ),
						)
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_initial' ), $c );
			
			$c = array(
						
						array(
							'name'		=> __( 'Dragging Of Map', 'avia_framework' ),
							'desc'		=> __( 'Choose whether the map can be draggable with mouse/touch or not', 'avia_framework' ),
							'id'		=> 'dragging',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'Allow dragging', 'avia_framework' )		=> '',
												__( 'Do not allow dragging', 'avia_framework' )	=> 'no_drag'
											)
						),
				
						array(
							'name'		=> __( 'Marker Popup Handling', 'avia_framework' ),
							'desc'		=> __( 'Choose if you want popups of the markers to close when user clicks the map', 'avia_framework' ),
							'id'		=> 'closepopuponclick',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'Do not close with click on map', 'avia_framework' )	=> '',
												__( 'Close with click on map', 'avia_framework' )			=> 'close_click'
											)
						),
				
						array(
							'name'		=> __( 'Keyboard Support', 'avia_framework' ),
							'desc'		=> __( 'Choose if you want keyboard support for interacting with the map', 'avia_framework' ),
							'id'		=> 'keyboard_support',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'Yes', 'avia_framework' )	=> '',
												__( 'No', 'avia_framework' )	=> 'no'
											)
						)
				
					);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Map Interaction', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_interaction' ), $template );
			
			$c = array(
				
						array(
							'name'		=> __( 'Zoom Control', 'avia_framework' ),
							'desc'		=> __( 'Choose to add a zoom control to the map', 'avia_framework' ),
							'id'		=> 'zoomcontrol',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'No Zoom Control', 'avia_framework' )		=> '',
												__( 'Display Zoom Control', 'avia_framework' )	=> 'control'
											)
						),
				
						array(
							'name'		=> __( 'Scroll Wheel Zoom', 'avia_framework' ),
							'desc'		=> __( 'Choose if the map can be zoomed by using the mouse wheel.', 'avia_framework' ),
							'id'		=> 'scrollwheel',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'Yes', 'avia_framework' )			=> '',
												__( 'No', 'avia_framework' )			=> 'no',
											)
						),
				
						array(
							'name'		=> __( 'Touch Zoom', 'avia_framework' ),
							'desc'		=> __( 'Choose if the map should support zoom by touch and drag', 'avia_framework' ),
							'id'		=> 'touch_zoom',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'Yes', 'avia_framework' )			=> '',
												__( 'No', 'avia_framework' )			=> 'no',
											)
						),
				
						array(
							'name'		=> __( 'Initial Zoom Level', 'avia_framework' ),
							'desc'		=> __( 'Choose to display all markers on pageload or set the zoom level when map is loaded on a scale from 0 (very far away) to 20 (very close)', 'avia_framework' ),
							'id'		=> 'zoom',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> AviaHtmlHelper::number_array( 0, 20, 1, array( __( 'Set Zoom level automatically to show all markers', 'avia_framework' ) => '' ) ) 
						),
						
						array(
							'name'		=> __( 'Minimum Zoom Level', 'avia_framework' ),
							'desc'		=> __( 'Choose the smallest possible zoom level', 'avia_framework' ),
							'id'		=> 'min_zoom',
							'type'		=> 'select',
							'std'		=> '0',
							'subtype'	=> AviaHtmlHelper::number_array( 0, 20, 1 ) 
						),
				
						array(
							'name'		=> __( 'Maximum Zoom Level', 'avia_framework' ),
							'desc'		=> __( 'Choose the largest possible zoom level.', 'avia_framework' ),
							'id'		=> 'max_zoom',
							'type'		=> 'select',
							'std'		=> '20',
							'subtype'	=> AviaHtmlHelper::number_array( 0, 20, 1 ) 
						),
				
						array(
							'name'		=> __( 'Scale Information', 'avia_framework' ),
							'desc'		=> __( 'Choose to show scale information on the map', 'avia_framework' ),
							'id'		=> 'show_scale',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'Yes', 'avia_framework' )			=> '',
												__( 'No', 'avia_framework' )			=> 'no',
											)
						)			

					);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Zoom', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_zoom' ), $template );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
				
						array(
							'name'		=> __( 'Grayscale', 'avia_framework' ),
							'desc'		=> __( 'Select if you want to grayscale the map. 0% is unchanged, 100% complete gray. This feature is not supported by older browsers.', 'avia_framework' ),
							'id'		=> 'grayscale',
							'type'		=> 'select',
							'std'		=> '0',
							'subtype'	=> AviaHtmlHelper::number_array( 0, 100, 25, array(), ' %' ) 
						),
				
						array(
							'name'		=> __( 'Detect Retina Display', 'avia_framework' ),
							'desc'		=> __( 'Choose to autodetect a retina screen', 'avia_framework' ),
							'id'		=> 'detect_retina',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'No', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )	=> 'detect_retina'
											)
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Color and Display', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_resolution' ), $template );
			
			$c = array(
				
						array(
							'name'		=> __( 'Width', 'avia_framework' ),
							'desc'		=> __( 'Select the width of the map in % of the container containing the map.', 'avia_framework' ),
							'id'		=> 'width',
							'type'		=> 'select',
							'std'		=> '100',
							'subtype'	=> AviaHtmlHelper::number_array( 0, 100, 1, array(), ' %' ) 
						),
				
						array(
							'name'		=> __( 'Height', 'avia_framework' ),
							'desc'		=> __( 'Select the height of the map in pixel.', 'avia_framework' ),
							'id'		=> 'height',
							'type'		=> 'select',
							'std'		=> '250',
							'subtype'	=> AviaHtmlHelper::number_array( 0, 2500, 1, array(), ' px' ) 
						)
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Map Size', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_map_size' ), $template );
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'leaflet_attributes_toggle',
							'id'			=> 'map_attr',
							'name'			=> __( 'Additional Map Attributes', 'avia_framework' )
						),
						
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_attributes' ), $c );
			
		}
		
		
		/**
		 * Creates the modal popup for a single entry
		 * 
		 * @since 4.8.2
		 * @return array
		 */
		protected function create_modal()
		{
			$elements = array(
				
				array(
						'type'			=> 'tab_container', 
						'nodescription'	=> true
					),
						
				array(
						'type'			=> 'tab',
						'name'			=> __( 'Content', 'avia_framework' ),
						'nodescription'	=> true
					),
				
					array(
							'type'				=> 'template',
							'template_id'		=> 'toggle_container',
							'templates_include'	=> array(
														$this->popup_key( 'modal_content_location' ),
														$this->popup_key( 'modal_content_marker' )
													),
							'nodescription'		=> true
						),
				
				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),
				
				array(
						'type'			=> 'tab',
						'name'			=> __( 'Styling', 'avia_framework' ),
						'nodescription'	=> true
					),
				
					array(
							'type'				=> 'template',
							'template_id'		=> 'toggle_container',
							'templates_include'	=> array(
														$this->popup_key( 'modal_styling_marker' ),
														$this->popup_key( 'modal_styling_visibility' )
													),
							'nodescription'		=> true
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
								'template_id'	=> $this->popup_key( 'modal_advanced_attributes' )
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
			
			return $elements;
			
		}
		
		/**
		 * Register all templates for the modal group popup
		 * 
		 * @since 4.8.2
		 */
		protected function register_modal_group_templates()
		{
			
			/**
			 * Content Tab
			 * ===========
			 */
			$c = array(
						array(	
							'type'				=> 'template',
							'template_id'		=> 'geolocation_toggle',
							'title'				=> __( 'Marker Location', 'avia_framework' ),
						)
				);
			
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_location' ), $c );

			$c = array(
						array(
							'name'		=> __( 'Draggable Marker', 'avia_framework' ),
							'desc'		=> __( 'Choose if the marker is draggable or fixed', 'avia_framework' ),
							'id'		=> 'draggable',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'Not draggable', 'avia_framework' )	=> '',
												__( 'Draggable', 'avia_framework' )		=> 'draggable'
											)
						),
				
						array(
							'name'		=> __( 'Marker Popup', 'avia_framework' ),
							'desc'		=> __( 'Enter some text here that will be displayed above the marker. You may also include a link.', 'avia_framework' ),
							'id'		=> 'content',
							'type'		=> 'textarea',
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Marker Popup Visibility', 'avia_framework' ),
							'desc'		=> __( 'Choose if the marker popup is visible on pageload or marker needs to be clicked to open it', 'avia_framework' ),
							'id'		=> 'visible',
							'type'		=> 'select',
							'std'		=> '',
							'required'	=> array( 'content', 'not', '' ),
							'subtype'	=> array(
												__( 'Click to open popup', 'avia_framework' )		=> '',
												__( 'Show popup on pageload', 'avia_framework' )	=> 'visible'
											)
						),
				
						array(
							'name'		=> __( 'Tooltip', 'avia_framework' ),
							'desc'		=> __( 'Enter an optional tooltip that is displayed when you hover over the marker (different from Marker Popup)', 'avia_framework' ),
							'id'		=> 'tooltip',
							'type'		=> 'input',
							'std'		=> ''
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Marker', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_marker' ), $template );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name'		=> __( 'Styling of Marker', 'avia_framework' ),
							'desc'		=> __( 'Choose to use default plugin marker icon or upload a custom icon (png or jpg)', 'avia_framework' ),
							'id'		=> 'custom_marker',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array(
												__( 'Default icon', 'avia_framework' )			=> '',
												__( 'Upload custom icon', 'avia_framework' )	=> 'upload_custom',
												__( 'Use a custom link', 'avia_framework' )		=> 'custom_url',
											)
						),
				
						array(
							'name'		=> __( 'Choose Icon Image', 'avia_framework' ),
							'desc'		=> __( 'Either upload a new, or choose an existing image from your media library', 'avia_framework' ),
							'id'		=> 'icon_upload',
							'type'		=> 'image',
							'title'		=> __( 'Insert Icon Image', 'avia_framework' ),
							'button'	=> __( 'Insert', 'avia_framework' ),
							'std'		=> AviaBuilder::$path['imagesURL'] . 'placeholder.jpg',
							'required'	=> array( 'custom_marker', 'equals', 'upload_custom' )
						),
				
						array(
							'name'		=> __( 'Custom URL To Marker Icon', 'avia_framework' ),
							'desc'		=> __( 'Enter a custom URL to the marker icon', 'avia_framework' ),
							'id'		=> 'icon_url',
							'type'		=> 'input',
							'std'		=> '',
							'required'	=> array( 'custom_marker', 'equals', 'custom_url' )
						),
				
						array(
							'name'		=> __( 'Icon Width', 'avia_framework' ),
							'desc'		=> __( 'Select the width of the icon in pixel', 'avia_framework' ),
							'id'		=> 'icon_width',
							'type'		=> 'select',
							'std'		=> '30',
							'subtype'	=> AviaHtmlHelper::number_array( 10, 300, 1, array(), ' px' ),
							'required'	=> array( 'custom_marker', 'not', '' )
						),
				
						array(
							'name'		=> __( 'Height', 'avia_framework' ),
							'desc'		=> __( 'Select the height of the icon in pixel', 'avia_framework' ),
							'id'		=> 'icon_height',
							'type'		=> 'select',
							'std'		=> '50',
							'subtype'	=> AviaHtmlHelper::number_array( 10, 300, 1, array(), ' px' ),
							'required'	=> array( 'custom_marker', 'not', '' ),
						),
				
						array(
							'name'		=> __( 'Icon Anchor Left', 'avia_framework' ),
							'desc'		=> __( 'Select the left position of the icon anchor in pixel', 'avia_framework' ),
							'id'		=> 'icon_anchor_left',
							'type'		=> 'select',
							'std'		=> '15',
							'subtype'	=> AviaHtmlHelper::number_array( 10, 300, 1, array(), ' px' ),
							'required'	=> array( 'custom_marker', 'not', '' )
						),
				
						array(
							'name'		=> __( 'Icon Anchor Top', 'avia_framework' ),
							'desc'		=> __( 'Select the top position of the icon anchor in pixel', 'avia_framework' ),
							'id'		=> 'icon_anchor_top',
							'type'		=> 'select',
							'std'		=> '60',
							'subtype'	=> AviaHtmlHelper::number_array( 10, 300, 1, array(), ' px' ),
							'required'	=> array( 'custom_marker', 'not', '' )
						)
				
					);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Marker Style', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_marker' ), $template );
			
			$c = array(
						array(
							'name'		=> __( 'Opacity', 'avia_framework' ),
							'desc'		=> __( 'Select the opacity of the marker (0 = no opacity)', 'avia_framework' ),
							'id'		=> 'marker_opacity',
							'type'		=> 'select',
							'std'		=> '0',
							'subtype'	=> AviaHtmlHelper::number_array( 0, 100, 1, array(), ' %' ),
						)
					);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Visibility', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_visibility' ), $template );
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'leaflet_attributes_toggle',
							'id'			=> 'marker_attr',
							'name'			=> __( 'Additional Marker Attributes', 'avia_framework' )
						),
						
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_attributes' ), $c );
			
		}
		
		/**
		 * Editor Element - this function defines the visual appearance of an element on the AviaBuilder Canvas
		 * Most common usage is to define some markup in the $params['innerHtml'] which is then inserted into the drag and drop container
		 * Less often used: $params['data'] to add data attributes, $params['class'] to modify the className
		 *
		 *
		 * @param array $params this array holds the default values for $content and $args. 
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_element( $params )
		{	
			$params = parent::editor_element( $params );
			$params['innerHtml'] .=	AviaPopupTemplates()->get_html_template( 'alb_element_fullwidth_stretch' );

			return $params;
		}
		
		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * 
		 * @param array $params
		 * @return array						usually holds an innerHtml key that holds item specific markup.
		 */
		public function editor_sub_element( $params )
		{
			$params['innerHtml']  = '';
			
			$params['innerHtml'] .= '<div class="avia_title_container">';
			$params['innerHtml'] .=		__( 'Address', 'avia_framework' ) . ': ';
			$params['innerHtml'] .=		'<span ' . $this->update_option_lockable( 'geo_street' ) . '>' . $params['args']['geo_street'] . '</span>, ';
			$params['innerHtml'] .=		'<span ' . $this->update_option_lockable( 'geo_postalcode' ) . '>' . $params['args']['geo_postalcode'] . '</span> ';
			$params['innerHtml'] .=		'<span ' . $this->update_option_lockable( 'geo_city' ) . '>' . $params['args']['geo_city'] . '</span>, ';
			$params['innerHtml'] .=		'<span ' . $this->update_option_lockable( 'geo_country' ) . '>' . $params['args']['geo_country'] . '</span>';
			$params['innerHtml'] .= '</div>';
			
			return $params;
		}
		
		/**
		 * Frontend Shortcode Handler
		 *
		 * @since 4.8.2
		 * @param array $atts					array of attributes
		 * @param string $content				text within enclosing form of shortcode element 
		 * @param string $shortcodename			the shortcode found, when == callback name
		 * @return string						$output returns the modified html string 
		 */
		public function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
			/**
			 * If disabled only show a message to editors 
			 */
			if( ! AviaLeafletMaps()->is_enabled() )
			{
				$out = '';

				if( current_user_can( 'edit_posts' ) )
				{
					$out .=	'<span class="av-shortcode-disabled-notice">';
					$out .=		'<strong>' . __( 'Admin notice for:', 'avia_framework' ) . '</strong> ' . $this->config['name'] . '<br />';
					$out .=		__( 'This element was disabled with theme option', 'avia_framework' ) . '<br />';
					$out .=	'</span>';
				}

				return $out;
			}
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			$defaults = array(
							'geo_street'		=> '',
							'geo_postalcode'	=> '',
							'geo_city'			=> '',
							'geo_country'		=> '',
							'geo_state'			=> '',
							'geo_county'		=> '',
							'geo_lng'			=> '',
							'geo_lat'			=> '',
							'zoomcontrol'		=> '',
							'scrollwheel'		=> '',
							'touch_zoom'		=> '',
							'keyboard_support'	=> '',
							'show_scale'		=> '',
							'dragging'			=> '',
							'closepopuponclick'	=> '',
							'zoom'				=> '',
							'min_zoom'			=> '',
							'max_zoom'			=> '',
							'grayscale'			=> 0,
							'detect_retina'		=> '',
							'width'				=> '100',
							'height'			=> '250',
							'map_attr'			=> '',

							'content'			=> ShortcodeHelper::shortcode2array( $content, 1 )
						);
			
			$defaults_marker = array(
							'geo_street'		=> '',
							'geo_postalcode'	=> '',
							'geo_city'			=> '',
							'geo_country'		=> '',
							'geo_state'			=> '',
							'geo_county'		=> '',
							'geo_lng'			=> '',
							'geo_lat'			=> '',
							'draggable'			=> '',
							'visible'			=> '',
							'tooltip'			=> '',
							'custom_marker'		=> '',
							'icon_upload'		=> '',
							'attachment'		=> '',			//	added by image upload element
							'attachment_size'	=> '',			//	added by image upload element
							'icon_url'			=> '',
							'icon_width'		=> '',
							'icon_height'		=> '',
							'icon_anchor_left'	=> '',
							'icon_anchor_top'	=> '',
							'marker_opacity'	=> 0,
							'marker_attr'		=> ''
						);	
					
			$atts = shortcode_atts( $defaults, $atts, $this->config['shortcode'] );
			
			foreach ( $defaults['content'] as $key => &$marker ) 
			{
				$marker['attr'] = shortcode_atts( $defaults_marker, $marker['attr'], $this->config['shortcode_nested'][0] );
			}
			
			unset( $marker );
			
			$class = '';
			$show_class = '';
			$style = '';
			
			
			//	generate HTML for map
			$map = AviaLeafletMaps()->get_maps_html_from_alb_shortode( $atts );
			
			$map_count = AviaLeafletMaps()->get_map_count();
			$map_id = ! empty( $meta['custom_el_id'] ) ? $meta['custom_el_id'] : 'avia-leaflet-map-' . $map_count;
			
			if( ! empty( $atts['grayscale'] ) )
			{
				$show_class .= ' avia-grayscale-' . $atts['grayscale'];
			}
			
			
			$output = '';
			
			$skipSecond = false;
			$params['class'] = "avia-leaflet-maps avia-leaflet-maps-section main_color {$av_display_classes} {$meta['el_class']} {$class}";
			$params['open_structure'] = false;
			$params['id'] = AviaHelper::save_string( $meta['custom_id_val'], '-', 'avia-leaflet-map-nr-' . $map_count );


			//	we dont need a closing structure if the element is the first one or if a previous fullwidth element was displayed before
			if( isset( $meta['index'] ) && $meta['index'] == 0 ) 
			{
				$params['close'] = false;
			}
			
			if( ! empty( $meta['siblings']['prev']['tag'] ) && in_array( $meta['siblings']['prev']['tag'], AviaBuilder::$full_el_no_section ) ) 
			{
				$params['close'] = false;
			}

			$add_id = ShortcodeHelper::is_top_level() ? '' : $meta['custom_el_id'];
			$add_css = ShortcodeHelper::is_top_level() ? '' : $meta['custom_class'];
			
			
			$out =	"<div {$add_id} class='av_leaflet_sc_main_wrap av_leaflet_main_wrap {$add_css}'>";
			$out .=		"<div id='{$map_id}' class='avia-leaflet-map-container avia-leaflet-map-sc {$show_class} {$av_display_classes}' {$style}>";
			$out .=			$map;
			$out .=		'</div>';
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
				$output .= avia_new_section( array( 'close' => false, 'id' => 'after_full_leaflet_map_' . $map_count ) );
			}

			return $output;
		}
		
		
	}

}




