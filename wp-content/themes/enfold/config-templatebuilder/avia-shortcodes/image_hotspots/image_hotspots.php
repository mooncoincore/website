<?php
/**
 * Image with Hotspots
 * 
 * Shortcode which inserts an image with one or many hotspots that show tooltips
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_image_hotspots' ) )
{
	class avia_sc_image_hotspots extends aviaShortcodeTemplate
	{
		/**
		 * @since 4.5.7.2
		 * @var int 
		 */
		static protected $img_hotspot_count = 0;

		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['is_fullwidth']	= 'yes';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Image with Hotspots', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-image-hotspot.png';
			$this->config['order']			= 95;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_image_hotspot';
			$this->config['shortcode_nested'] = array( 'av_image_spot' );
			$this->config['modal_data'] 	= array( 'modal_class' => 'bigscreen' );
			$this->config['tooltip'] 	    = __( 'Inserts an image with one or many hotspots that show tooltips', 'avia_framework' );
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}

		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-hotspot', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/image_hotspots/image_hotspots.css', array( 'avia-layout' ), false );

				//load js
			wp_enqueue_script( 'avia-module-hotspot', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/image_hotspots/image_hotspots.js', array( 'avia-shortcodes' ), false, true );
		}
			
			
		/**
		 * Popup Elements
		 *
		 * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
		 * opens a modal window that allows to edit the element properties
		 *
		 * @return void
		 */
		function popup_elements()
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
							'template_id'	=> $this->popup_key( 'content_image' )
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
							'template_id'	=> $this->popup_key( 'styling_general' )
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
								'template_id'	=> $this->popup_key( 'advanced_animation' )
							),
				
						array(
								'type'			=> 'template',
								'template_id'	=> 'lazy_loading_toggle',
								'lockable'		=> true
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> 'screen_options_toggle',
								'templates_include'	=> array( 
													$this->popup_key( 'advanced_mobile' ),
													'screen_options_visibility'
												),
								'lockable'		=> true
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
						'type'			=> 'template',
						'template_id'	=> 'element_template_selection_tab',
						'args'			=> array( 'sc' => $this )
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
							'name'		=> __( 'Choose Image','avia_framework' ),
							'desc'		=> __( 'Either upload a new, or choose an existing image from your media library. Once an Image has been selected you can add your Hotspots', 'avia_framework' ),
							'id'		=> 'src',
							'type'		=> 'image',
							'title'		=> __( 'Insert Image', 'avia_framework' ),
							'button'	=> __( 'Insert', 'avia_framework' ),
							'std'		=> AviaBuilder::$path['imagesURL'] . 'placeholder-full.jpg',
							'container_class' => 'av-hotspot-container',
							'lockable'	=> true,
							'locked'	=> array( 'src', 'attachment', 'attachment_size' )
						),
						
						array(
							'name'			=> __( 'Add/Edit your hotspots.', 'avia_framework' ),
							'desc'			=> __( 'Here you can add, remove and edit the locations, tooltips and appearance for your hotspots.', 'avia_framework' ),
							'type'			=> 'modal_group',
							'id'			=> 'content',
							'modal_title'	=> __( 'Edit Hotspot Tooltip', 'avia_framework' ),
							'add_label'		=> __( 'Add Hotspot', 'avia_framework' ),
							'std'			=> array(),
							'editable_item'	=> true,
							'lockable'		=> true,
							'tmpl_set_default'	=> false,
							'special_modal'	=> array(
													'type'					=> 'hotspot',
													'image_container_class'	=> 'av-hotspot-container'
												),
							'subelements'	=> $this->create_modal()
						)
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_image' ), $c );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Hotspot Styling', 'avia_framework' ),
							'desc' 	=> __( 'Select the hotspot styling', 'avia_framework' ),
							'id' 	=> 'hotspot_layout',
							'type' 	=> 'select',
							'std' 	=> 'numbered',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Numbered Hotspot', 'avia_framework' )	=> 'numbered',
												__( 'Blank Hotspot', 'avia_framework' )		=> 'blank'
											)
						)					
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_general' ), $c );
			
			/**
			 * Advanced Tab
			 * =============
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Image Fade in Animation', 'avia_framework' ),
							'desc' 	=> __( "Add a small animation to the image when the user first scrolls to the image position. This is only to add some 'spice' to the site and only works in modern browsers", 'avia_framework' ),
							'id' 	=> 'animation',
							'type' 	=> 'select',
							'std' 	=> 'no-animation',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'None', 'avia_framework' )			=> 'no-animation',
												__( 'Simple Fade in', 'avia_framework' ) => 'fade-in',
												__( 'Pop up', 'avia_framework' )		=> 'pop-up',
												__( 'Top to Bottom', 'avia_framework' )	=> 'top-to-bottom',
												__( 'Bottom to Top', 'avia_framework' )	=> 'bottom-to-top',
												__( 'Left to Right', 'avia_framework' )	=> 'left-to-right',
												__( 'Right to Left', 'avia_framework' )	=> 'right-to-left'
											)
						),
				
						array(
							'name' 	=> __( 'Show Tooltips', 'avia_framework' ),
							'desc' 	=> __( 'Select when to display the tooltips', 'avia_framework' ),
							'id' 	=> 'hotspot_tooltip_display',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'On Mouse Hover', 'avia_framework' )						=> '',
												__( 'Always', 'avia_framework' )								=> 'av-permanent-tooltip',
												__( 'Show On Mouse Hover - Hide On Click', 'avia_framework' )	=> 'av-close-on-click-tooltip'
											)
						)
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Animation', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_animation' ), $template );
			
			$c = array(
						array(	
							'name' 	=> __( 'Hotspot on mobile devices', 'avia_framework' ),
							'desc' 	=> __( 'Check if you always want to show the tooltips on mobile phones below the image. Recommended if your tooltips contain a lot of text', 'avia_framework' ),
							'id' 	=> 'hotspot_mobile',
							'type' 	=> 'checkbox',
							'std' 	=> 'true',
							'lockable'	=> true
						)		

				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_mobile' ), $c );
			
			
			
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
							'template_id'	=> $this->popup_key( 'modal_content_text' )
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
													$this->popup_key( 'modal_styling_tooltip' ),
													$this->popup_key( 'modal_styling_colors' )
												),
							'nodescription' => true
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
							'type'			=> 'template',
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array(
													$this->popup_key( 'modal_advanced_link' )
												),
							'nodescription' => true
						),
				
				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),
				
				array(	
						'type'			=> 'template',
						'template_id'	=> 'element_template_selection_tab',
						'args'			=> array( 
												'sc'			=> $this,
												'modal_group'	=> true
											)
					),
				
				array(
						'id'	=> 'hotspot_pos',
						'std'	=> '',
						'type'	=> 'hidden'
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
							'name' 	=> __( 'Tooltip', 'avia_framework' ),
							'desc' 	=> __( 'Enter a short descriptive text that appears if the user places his mouse above the hotspot', 'avia_framework' ) ,
							'id' 	=> 'content',
							'type' 	=> 'tiny_mce',
							'std' 	=> '',
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_text' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Tooltip Position', 'avia_framework' ),
							'desc' 	=> __( 'Select where to display the tooltip in relation to the hotspot', 'avia_framework' ),
							'id' 	=> 'tooltip_pos',
							'type' 	=> 'select',
							'std' 	=> 'av-tt-pos-above av-tt-align-left',
							'lockable'	=> true,
							'subtype'	=> array(
												'Above'	=> array(
																__( 'Top Left', 'avia_framework' )		=> 'av-tt-pos-above av-tt-align-left',
																__( 'Top Right', 'avia_framework' )		=> 'av-tt-pos-above av-tt-align-right',
																__( 'Top Centered', 'avia_framework' )	=> 'av-tt-pos-above av-tt-align-centered',
															),
												'Below'	=> array(
																__( 'Bottom Left', 'avia_framework' )		=> 'av-tt-pos-below av-tt-align-left',
																__( 'Bottom Right', 'avia_framework' )		=> 'av-tt-pos-below av-tt-align-right',
																__( 'Bottom Centered', 'avia_framework' )	=> 'av-tt-pos-below av-tt-align-centered',
															),
												'Left'	=> array(
																__( 'Left Top', 'avia_framework' )		=> 'av-tt-pos-left av-tt-align-top',
																__( 'Left Bottom', 'avia_framework' )	=> 'av-tt-pos-left av-tt-align-bottom',
																__( 'Left Centered', 'avia_framework' )	=> 'av-tt-pos-left av-tt-align-centered',
															),
												'Right'=> array(
																__( 'Right Top', 'avia_framework' )			=> 'av-tt-pos-right av-tt-align-top',
																__( 'Right Bottom', 'avia_framework' )		=> 'av-tt-pos-right av-tt-align-bottom',
																__( 'Right Centered', 'avia_framework' )	=> 'av-tt-pos-right av-tt-align-centered',
															)
											)
						),
				
						array(
							'name' 	=> __( 'Tooltip Width', 'avia_framework' ),
							'desc' 	=> __( 'Select the width of the tooltip. Height is based on the content', 'avia_framework' ),
							'id' 	=> 'tooltip_width',
							'type' 	=> 'select',
							'std' 	=> 'av-tt-default-width',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )		=> 'av-tt-default-width',
												__( 'Large', 'avia_framework' )			=> 'av-tt-large-width',
												__( 'Extra Large', 'avia_framework' )	=> 'av-tt-xlarge-width',
											),
						),
									
						array(	
							'name' 	=> __( 'Tooltip Style', 'avia_framework' ),
							'desc' 	=> __( 'Choose the style of your tooltip', 'avia_framework' ) ,
							'id' 	=> 'tooltip_style',
							'type' 	=> 'select',
							'std' 	=> 'main_color',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )					=> 'main_color',
												__( 'Default with drop shadow', 'avia_framework' )	=> 'main_color av-tooltip-shadow',
												__( 'Transparent Dark', 'avia_framework' )			=> 'transparent_dark'
											)
						)				

				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Tooltip', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_tooltip' ), $template );
			
			$c = array(
						array(
							'name' 	=> __( 'Hotspot Color', 'avia_framework' ),
							'desc' 	=> __( 'Set the colors of your hotspot', 'avia_framework' ),
							'id' 	=> 'hotspot_color',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )	=> '',
												__( 'Custom', 'avia_framework' )	=> 'custom',
											),
						),
				
						array(	
							'name' 	=> __( 'Custom Background Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom background color here', 'avia_framework' ),
							'id' 	=> 'custom_bg',
							'type' 	=> 'colorpicker',
							'std' 	=> '#ffffff',
							'lockable'	=> true,
							'required'	=> array( 'hotspot_color', 'equals', 'custom' )
						),	
										
						array(	
							'name' 	=> __( 'Custom Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color here', 'avia_framework' ),
							'id' 	=> 'custom_font',
							'type' 	=> 'colorpicker',
							'std' 	=> '#888888',
							'lockable'	=> true,
							'required'	=> array( 'hotspot_color', 'equals', 'custom' )
						),
									
						array(	
							'name' 	=> __( 'Custom Pulse Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom pulse color here', 'avia_framework' ),
							'id' 	=> 'custom_pulse',
							'type' 	=> 'colorpicker',
							'std' 	=> '#ffffff',
							'lockable'	=> true,
							'required'	=> array( 'hotspot_color', 'equals', 'custom' )
						)

				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Colors', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_colors' ), $template );
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'linkpicker_toggle',
							'name'			=> __( 'Hotspot Link?', 'avia_framework' ),
							'desc'			=> __( 'Where should your hotspot link to?', 'avia_framework' ),
							'target_id'		=> 'link_target',
							'no_toggle'		=> true,
							'lockable'		=> true,
							'subtypes'		=> array( 'no', 'manually', 'single', 'taxonomy' )
						)

				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_link' ), $c );
			
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
			$default = array();
			$locked = array();
			$attr = $params['args'];
			Avia_Element_Templates()->set_locked_attributes( $attr, $this, $this->config['shortcode'], $default, $locked );
			
			$template = $this->update_template_lockable( 'src', "<img src='{{src}}' alt=''/>", $locked );
			
			$img = '';
			
			if( ! empty( $attr['attachment'] ) && ! empty( $attr['attachment_size'] ) )
			{
				$img = wp_get_attachment_image( $attr['attachment'], $attr['attachment_size'] );
			}
			else if( isset( $attr['src'] ) && is_numeric( $attr['src'] ) )
			{
				$img = wp_get_attachment_image( $attr['src'], 'large' );
			}
			else if( ! empty( $attr['src'] ) )
			{
				$img = "<img src='" . esc_attr( $attr['src'] ) . "' alt=''  />";
			}


			$html = AviaPopupTemplates()->get_html_template( 'alb_element_fullwidth_stretch' );
			$button = '<span class="av_hotspot_image_caption button button-primary button-large">' . __( 'Image with Hotspots - Click to insert image and hotspots', 'avia_framework' ) . '</span>';
			
			$pos = strrpos( $html, '</div>' );
			$html = substr( $html, 0, $pos ) . $button . '</div>';
				
			$params['innerHtml']  =	'<div class="avia_image avia_hotspot_image avia_image_style" data-update_element_template="yes">';
			$params['innerHtml'] .=		"<div class='avia_image_container avia-align-center ' {$template}>{$img}</div>";
			$params['innerHtml'] .=		$html;
			$params['innerHtml'] .= '</div>';

			$params['class'] = '';

			return $params;
		}
			
		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * @param array $params this array holds the default values for $content and $args. 
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_sub_element( $params )
		{
			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $params['args']['linktarget'] ) )
			{
				$params['args']['link_target'] = $params['args']['linktarget'];
			}
			
			$default = array();
			$locked = array();
			$attr = $params['args'];
			Avia_Element_Templates()->set_locked_attributes( $attr, $this, $this->config['shortcode_nested'][0], $default, $locked );
			
			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_title_container' data-hotspot_pos='{$attr['hotspot_pos']}' data-update_element_template='yes'>" . __( 'Hotspot', 'avia_framework' ) . ' </div>';

			return $params;
		}
		
		/**
		 * Create custom stylings 
		 * 
		 * @since 4.8.4
		 * @param array $args
		 * @return array
		 */
		protected function get_element_styles( array $args ) 
		{
			$result = parent::get_element_styles( $args );
			
			extract( $result );
			
			$default = array(
						'animation'					=> 'no-animation', 
						'attachment'				=> '', 
						'attachment_size'			=> '', 
						'hotspot_layout'			=> 'numbered', 
						'hotspot_mobile'			=> '', 
						'hotspot_tooltip_display'	=> '',
						'lazy_loading'				=> 'disabled',
						'src'						=> '',
						'img_h'						=> '',
						'img_w'						=> '',
						'img_alt'					=> '',
						'img_title'					=> '',
						'attachment_id'				=> 0,
						'hotspots'					=> array()		//	CET modified hotspots
				);
			
			$default = $this->sync_sc_defaults_array( $default, 'no_modal_item', 'no_content' );
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			$hotspots = ShortcodeHelper::shortcode2array( $content, 1 );
			
			foreach( $hotspots as $key => &$item ) 
			{
				$item_def = $this->get_default_modal_group_args();
				Avia_Element_Templates()->set_locked_attributes( $item['attr'], $this, $this->config['shortcode_nested'][0], $item_def, $locked, $item['content'] );
			}
			
			unset( $item );
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			$atts['hotspots'] = $hotspots;
			
			if( ! empty( $atts['attachment'] ) )
			{
				/**
				 * Allows e.g. WPML to reroute to translated image
				 */
				$posts = get_posts( array(
								'include'			=> $atts['attachment'],
								'post_status'		=> 'inherit',
								'post_type'			=> 'attachment',
								'post_mime_type'	=> 'image',
								'order'				=> 'ASC',
								'orderby'			=> 'post__in' )
							);

				if( is_array( $posts ) && ! empty( $posts ) )
				{
					$attachment_entry = $posts[0];
					$atts['attachment_id'] = $attachment_entry->ID;

					$alt = get_post_meta( $attachment_entry->ID, '_wp_attachment_image_alt', true );
					$atts['img_alt'] = ! empty( $alt ) ? esc_attr( $alt ) : '';
					$atts['img_title'] = trim( $attachment_entry->post_title ) ? esc_attr( $attachment_entry->post_title ) : '';

					if( ! empty( $attachment_size ) )
					{
						$src = wp_get_attachment_image_src( $attachment_entry->ID, $attachment_size );
						$atts['img_h'] = ! empty( $src[2] ) ? $src[2] : '';
						$atts['img_w'] = ! empty( $src[1] ) ? $src[1] : '';
						$atts['src'] = ! empty( $src[0] ) ? $src[0] : '';
					}
				}
			}
			
			
			
	
			$classes = array(
						'av-hotspot-image-container', 
						$element_id
					);
			
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			
			if( ! empty( $atts['src'] ) )
			{
				//some custom classes
				$element_styling->add_classes( 'container', array( "av-hotspot-{$atts['hotspot_layout']}", $atts['hotspot_tooltip_display'] ) );
				
				if( $atts['animation'] != 'no-animation' )
				{
					$element_styling->add_classes( 'container', array( 'avia_animated_image', 'avia_animate_when_almost_visible', $atts['animation'] ) );
				}
				
				if( ! empty( $atts['hotspot_mobile'] ) )
				{
					$element_styling->add_classes( 'container', 'av-mobile-fallback-active' );
				}
			}
			
			
			$selectors = array(
						'container'	=> ".av-hotspot-image-container.{$element_id}"
					);
			
			$element_styling->add_selectors( $selectors );
			
			$result['default'] = $default;
			$result['atts'] = $atts;
			$result['content'] = $content;
			$result['element_styling'] = $element_styling;
			$result['meta'] = $meta;
			
			return $result;
		}
		
		/**
		 * Create custom stylings for items
		 * (also called when creating header implicit)
		 * 
		 * @since 4.8.4
		 * @param array $args
		 * @return array
		 */
		protected function get_element_styles_item( array $args ) 
		{	
			$result = parent::get_element_styles_item( $args );
			
			extract( $result );
			
			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $atts['linktarget'] ) )
			{
				$atts['link_target'] = $atts['linktarget'];
			}
			
			$default = array(
							'tooltip_width'	=> 'av-tt-default-width', 
							'tooltip_pos'	=> 'av-tt-pos-above av-tt-align-left', 
							'hotspot_pos'	=> '50,50', 
							'output'		=> '', 
							'hotspot_color'	=> '', 
							'custom_bg'		=> '', 
							'custom_font'	=> '', 
							'custom_pulse'	=> '', 
							'tooltip_style'	=> 'main_color', 
							'link'			=> '', 
							'link_target'	=> ''
						);
			
			$default = $this->sync_sc_defaults_array( $default, 'modal_item', 'no_content' );
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode_nested'][0] );

			//	hidden, set in editor
			if( empty( $atts['hotspot_pos'] ) ) 
			{
				$atts['hotspot_pos'] = '50,50';
			}

			$classes = array(
						'av-image-hotspot',
						$element_id
					);
			
			$element_styling->add_classes( 'container', $classes );
			
			
			$hotspot_pos = explode( ',', $atts['hotspot_pos'] );
			$element_styling->add_styles( 'container', array( 
													'top'	=> $hotspot_pos[0] . '%',
													'left'	=> $hotspot_pos[1] . '%'
												) );
			
			if( 'custom' == $atts['hotspot_color'] )
			{
				$element_styling->add_styles( 'container-inner', array( 
													'background-color'	=> $atts['custom_bg'],
													'color'				=> $atts['custom_font']
												) );
				
				$element_styling->add_styles( 'container-pulse', array( 'background-color'	=> $atts['custom_pulse'] ) );
			}
			
			
			$selectors = array(
						'container'			=> ".av-hotspot-image-container .av-image-hotspot.{$element_id}",
						'container-inner'	=> ".av-hotspot-image-container .av-image-hotspot.{$element_id} .av-image-hotspot_inner",
						'container-pulse'	=> ".av-hotspot-image-container .av-image-hotspot.{$element_id} .av-image-hotspot-pulse",
					);
			
			$element_styling->add_selectors( $selectors );
			
			$result['default'] = $default;
			$result['atts'] = $atts;
			$result['content'] = $content;
			$result['element_styling'] = $element_styling;
			
			return $result;
		}

		/**
		 * Frontend Shortcode Handler
		 *
		 * @param array $atts array of attributes
		 * @param string $content text within enclosing form of shortcode element
		 * @param string $shortcodename the shortcode found, when == callback name
		 * @return string $output returns the modified html string
		 */
		function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{	
			$result = $this->get_element_styles( compact( array( 'atts', 'content', 'shortcodename', 'meta' ) ) );
			
			extract( $result );
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			extract( $atts );
			
			
			avia_sc_image_hotspots::$img_hotspot_count ++;
			
			$output = '';
				
			//no src? return
			if( ! empty( $src ) )
			{
				$extra_class = ! ShortcodeHelper::is_top_level() ? ' av-non-fullwidth-hotspot-image' : '';
				
				$hotspot_html 	= '';
				$tooltip_html 	= '';
				$counter 		= 1;

				foreach( $hotspots as $hotspot )
				{ 
					if( ! empty( $hotspot_mobile ) ) 
					{
						$tooltip_html .= $this->add_fallback_tooltip( $hotspot, $counter, $hotspot_tooltip_display );
					}

					$extraClass  = ! empty( $hotspot_mobile ) ? ' av-mobile-fallback-active ' : '';
					$extraClass .= ! empty( $hotspot_tooltip_display ) ? " {$hotspot_tooltip_display}-single " : '';

					$hotspot_html .= $this->add_hotspot( $hotspot, $counter, $extraClass, $hotspot_tooltip_display );
					$counter ++; 
				}

				$hw = '';
				if( ! empty( $img_h ) ) 
				{
					$hw .= ' height="' . $img_h . '"';
				}
				if( ! empty( $img_w ) ) 
				{
					$hw .= ' width="' . $img_w . '"';
				}
				
				$markup_img = avia_markup_helper( array( 'context' => 'image', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );
				$markup_url = avia_markup_helper( array( 'context' => 'image_url', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );

				$el_id = ShortcodeHelper::is_top_level() ? '' : $meta['custom_el_id'];
				$img_tag = "<img class='avia_image' src='{$src}' alt='{$img_alt}' title='{$img_title}' {$hw} {$markup_url} />";
				$img_tag = Av_Responsive_Images()->prepare_single_image( $img_tag, $attachment_id, $lazy_loading );

				
				$style_tag = $element_styling->get_style_tag( $element_id );
				$item_tag = $element_styling->style_tag_html( $this->subitem_inline_styles, 'sub-' . $element_id );
				$container_class = $element_styling->get_class_string( 'container' );
			
				$output .= $style_tag;
				$output .= $item_tag;
				$output .= "<div {$el_id} class='{$container_class} {$av_display_classes} {$extra_class}' {$markup_img}>";
				$output .= 		"<div class='av-hotspot-container'>";
				$output .= 			"<div class='av-hotspot-container-inner-cell'>";
				$output .= 				"<div class='av-hotspot-container-inner-wrap'>";
				$output .= 					$hotspot_html;
				$output .= 					$img_tag;
				$output .= 				'</div>';
				$output .= 			'</div>';
				$output .= 		'</div>';
				$output .=		$tooltip_html;
				$output .= '</div>';				
			}
			
			$output = Av_Responsive_Images()->make_content_images_responsive( $output );
			
			$this->subitem_inline_styles = '';
				
			if( ! ShortcodeHelper::is_top_level() ) 
			{
				return $output;
			}
				
			$skipSecond = false;
			$params['class'] = "main_color av-fullwidth-hotspots {$meta['el_class']} {$av_display_classes}";
			$params['open_structure'] = false;
			$params['id'] = AviaHelper::save_string( $meta['custom_id_val'] , '-', 'av-sc-img-hotspot-' . avia_sc_image_hotspots::$img_hotspot_count );
			$params['custom_markup'] = $meta['custom_markup'];

			//we don't need a closing structure if the element is the first one or if a previous fullwidth element was displayed before
			if( $meta['index'] == 0 ) 
			{
				$params['close'] = false;
			}
			
			if( ! empty( $meta['siblings']['prev']['tag'] ) && in_array( $meta['siblings']['prev']['tag'], AviaBuilder::$full_el_no_section ) ) 
			{
				$params['close'] = false;
			}

			$section  = avia_new_section( $params );
			$section .=		$output;
			$section .= '</div>'; //close section
				
				
			//if the next tag is a section dont create a new section from this shortcode
			if( ! empty( $meta['siblings']['next']['tag'] ) && in_array( $meta['siblings']['next']['tag'], AviaBuilder::$full_el ) )
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
				$section .= avia_new_section( array( 'close' => false, 'id' => 'after_image_hotspots' ) );
			}

			return $section;
		}
			
		/**
		 * 
		 * @since < 4.0
		 * @param array $hotspot
		 * @param int $counter
		 * @param string $extraClass
		 * @param string $hotspot_tooltip_display
		 * @return string
		 */
		protected function add_hotspot( array $hotspot, $counter, $extraClass = '', $hotspot_tooltip_display = '' )
		{
			//	init parameters for normal shortcode handler
			$atts = $hotspot['attr'];
			$content = $hotspot['content'];
			$shortcodename = $this->config['shortcode_nested'][0];
			
			
			$result = $this->get_element_styles_item( compact( array( 'atts', 'content', 'shortcodename' ) ) );
			
			extract( $result );
			
			extract( $atts );
			
			//	prepare content for data attribute
			$content = esc_attr( ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $content ) ) );

			$tags = array( 'div', 'div' );
			if( ! empty( $link ) )
			{
				$link = AviaHelper::get_url( $link, false );
				$blank = AviaHelper::get_link_target( $link_target );
				$tags = array( "a href={$link} {$blank}", 'a' );
			}

			$layout = explode( ' ', $tooltip_pos );
			$data_pos = isset( $layout[0] ) ? str_replace( 'av-tt-pos-', '', $layout[0] ) : 'top';
			$data_align	= isset( $layout[1] ) ? str_replace( 'av-tt-align-', '', $layout[1] ) : 'centered';
			
			switch( $data_pos )
			{
				case 'above':
					$data_pos = 'top';
					break;
				case 'below':
					$data_pos = 'bottom';
					break;
			}

			
			$this->subitem_inline_styles .= $element_styling->get_style_tag( $element_id, 'rules_only' );
			$container_class = $element_styling->get_class_string( 'container' );
			
			$output .=	"<div class='{$container_class} av-image-hotspot-{$counter} {$hotspot_tooltip_display}' data-avia-tooltip-position='{$data_pos}' data-avia-tooltip-alignment='{$data_align}' data-avia-tooltip-class='{$tooltip_width} {$tooltip_pos} {$extraClass} {$tooltip_style} av-tt-hotspot' data-avia-tooltip='{$content}'>";
			$output .=		"<{$tags[0]} class='av-image-hotspot_inner'>{$counter}</{$tags[1]}>";
			$output .=		"<div class='av-image-hotspot-pulse'></div>";
			$output .=	'</div>';

			return $output;
		}
			
		/**
		 * 
		 * @param array $hotspot
		 * @param int $counter
		 * @param string $hotspot_tooltip_display
		 * @return string
		 */
		protected function add_fallback_tooltip( $hotspot, $counter, $hotspot_tooltip_display = '' )
		{
			$content = $hotspot['content'];

			if( empty( $content ) ) 
			{
				return;
			}

			$output  =	'';
			$output .=	"<div class='av-hotspot-fallback-tooltip av-image-hotspot-{$counter} {$hotspot_tooltip_display}'>";
			$output .=		'<div class="av-hotspot-fallback-tooltip-count">';
			$output .=			$counter;
			$output .=			'<div class="avia-arrow"></div>';
			$output .=		'</div>';
			$output .=		'<div class="av-hotspot-fallback-tooltip-inner clearfix">';
			$output .=			ShortcodeHelper::avia_apply_autop( $content );
			$output .=		'</div>';
			$output .=	'</div>';

			return $output;
		}

	}
}








