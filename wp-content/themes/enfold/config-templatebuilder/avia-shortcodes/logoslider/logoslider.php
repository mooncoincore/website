<?php
/**
 * Partner/Logo Element
 * 
 * Shortcode that allows to display a simple partner logo grid or slider
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_partner_logo' ) )
{
	class avia_sc_partner_logo extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Partner/Logo Element', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-partner.png';
			$this->config['order']			= 7;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_partner';
			$this->config['shortcode_nested'] = array( 'av_partner_logo' );
			$this->config['tooltip'] 	    = __( 'Display a partner/logo Grid or Slider', 'avia_framework' );
			$this->config['preview'] 		= false;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'el_id';
			$this->config['id_show']		= 'yes';
			$this->config['name_item']		= __( 'Partner/Logo Item', 'avia_framework' );
			$this->config['tooltip_item']	= __( 'A Partner/Logo Element Item', 'avia_framework' );
		}

		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-slideshow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow.css', array( 'avia-layout' ), false );
			wp_enqueue_style( 'avia-module-slideshow-contentpartner', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/contentslider/contentslider.css', array( 'avia-module-slideshow' ), false );
			wp_enqueue_style( 'avia-module-postslider', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/postslider/postslider.css', array( 'avia-layout' ), false );

				//load js
			wp_enqueue_script( 'avia-module-slideshow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow.js', array( 'avia-shortcodes' ), false, true );

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
								'template_id'	=> $this->popup_key( 'content_entries' )
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
													$this->popup_key( 'styling_slider' ),
													$this->popup_key( 'styling_image' ),
													$this->popup_key( 'styling_columns' )
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
							'type' 	=> 'toggle_container',
							'nodescription' => true
						),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_heading' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_animation_slider' )
							),
				
						array(
								'type'			=> 'template',
								'template_id'	=> 'lazy_loading_toggle',
								'lockable'		=> true
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> 'screen_options_toggle',
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
							'type'			=> 'modal_group',
							'id'			=> 'content',
							'container_class'	=> 'avia-element-fullwidth avia-multi-img',
							'modal_title'	=> __( 'Edit Form Element', 'avia_framework' ),
							'add_label'		=> __( 'Add single image', 'avia_framework' ),
							'std'			=> array(),
							'editable_item'	=> true,
							'lockable'		=> true,
							'tmpl_set_default'	=> false,
							'creator'		=> array(
												'name'		=> __( 'Add Images', 'avia_framework' ),
												'desc'		=> __( 'Here you can add new Images to the partner/logo element.', 'avia_framework' ),
												'id'		=> 'id',
												'type'		=> 'multi_image',
												'title'		=> __( 'Add multiple Images', 'avia_framework' ),
												'button'	=> __( 'Insert Images', 'avia_framework' ),
												'std'		=> ''
											),
							'subelements'	=> $this->create_modal()
						),
				
						array(
							'name' 	=> __( 'Heading', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to display a heading above the images?', 'avia_framework' ),
							'id' 	=> 'heading',
							'type' 	=> 'input',
							'std' 	=> '',
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),
					
				
//						array(	
//							'name' 	=> __( 'Use first slides caption as permanent caption', 'avia_framework' ),
//							'desc' 	=> __( 'If checked the caption will be placed on top of the slider. Please be aware that all slideshow link settings and other captions will be ignored then', 'avia_framework' ) ,
//							'id' 	=> 'perma_caption',
//							'std' 	=> '',
//							'type' 	=> 'checkbox'
//						),
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_entries' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(

						array(	
								'type'			=> 'template',
								'template_id'	=> 'columns_count_icon_switcher',
								'std'			=> array(
														'default'	=> '3'
													),
								'lockable'		=> true,
								'heading'		=> array(),
								'id_sizes'		=>	array(
														'default'	=> 'columns'
													),
								'subtype'		=> array(
														'default'	=> array(
																	__( '1 Columns', 'avia_framework' )	=> '1',
																	__( '2 Columns', 'avia_framework' )	=> '2',
																	__( '3 Columns', 'avia_framework' )	=> '3',
																	__( '4 Columns', 'avia_framework' )	=> '4',
																	__( '5 Columns', 'avia_framework' )	=> '5',
																	__( '6 Columns', 'avia_framework' )	=> '6',
																	__( '7 Columns', 'avia_framework' )	=> '7',
																	__( '8 Columns', 'avia_framework' )	=> '8',
																)
													)
							),
						);
				
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Columns', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_columns' ), $template );
			
			
			$c = array(
						array(
							'name' 	=> __( 'Logo Image Size', 'avia_framework' ),
							'desc' 	=> __( 'Choose image size for your slideshow.', 'avia_framework' ),
							'id' 	=> 'size',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> AviaHelper::get_registered_image_sizes( array( 'thumbnail', 'logo', 'widget', 'slider_thumb' ) )
						),
					
							
						array(
							'name' 	=> __( 'Image size behaviour', 'avia_framework' ),
							'desc' 	=> __( 'Should the image stretch to fill the available space?', 'avia_framework' ),
							'id' 	=> 'img_size_behave',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(	
												__( 'Image stretches to fill the available space', 'avia_framework' )	=> '',
												__( 'Do not stretch image. If more space is available image will be centered.', 'avia_framework' )	=> 'no_stretch',
							)
						),
					
						array(
							'name' 	=> __( 'Display Border around images?', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to display a light border around the images?', 'avia_framework' ),
							'id' 	=> 'border',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Display border', 'avia_framework' )		=> '',
												__( 'Do not display border', 'avia_framework' )	=> 'av-border-deactivate'
											),
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Images', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_image' ), $template );
			
			
			$c = array(
						array(
							'name' 	=> __( 'Logo Slider or Logo Grid Layout', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to use a grid or a slider to display the logos?', 'avia_framework' ),
							'id' 	=> 'type',
							'type' 	=> 'select',
							'std' 	=> 'slider',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Slider', 'avia_framework' )	=> 'slider',
												__( 'Grid', 'avia_framework' )		=> 'grid'
											),
						),
				
						array(
							'name' 	=> __( 'Slider controls', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to display slider control buttons?', 'avia_framework' ),
							'id' 	=> 'navigation',
							'type' 	=> 'select',
							'std' 	=> 'arrows',
							'lockable'	=> true,
							'required' 	=> array( 'type', 'equals', 'slider' ),
							'subtype'	=> array(
												__( 'Yes, display arrow control buttons', 'avia_framework' )		=> 'arrows',
												__( 'Yes, display dot control buttons', 'avia_framework' )			=> 'dots',
												__( 'No, do not display any control buttons', 'avia_framework' )	=> 'no'
											),
                    ),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Slider', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_slider' ), $template );
			
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(	
							'type'				=> 'template',
							'template_id'		=> 'heading_tag',
							'theme_default'		=> 'h3',
							'context'			=> __CLASS__,
							'lockable'			=> true,
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Heading', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_heading' ), $template );
			
			$c = array(
						array(
							'name' 	=> __( 'Transition', 'avia_framework' ),
							'desc' 	=> __( 'Choose the transition for your logo slider.', 'avia_framework' ),
							'id' 	=> 'animation',
							'type' 	=> 'select',
							'std' 	=> 'slide',
							'lockable'	=> true,
							'required'	=> array( 'type', 'equals', 'slider' ),
							'subtype'	=> array(
												__( 'Slide', 'avia_framework' )	=> 'slide',
												__( 'Fade', 'avia_framework' )	=> 'fade'
											),
						),

						array(
							'name' 	=> __( 'Autorotation active?', 'avia_framework' ),
							'desc' 	=> __( 'Check if the logo slider should rotate by default', 'avia_framework' ),
							'id' 	=> 'autoplay',
							'type' 	=> 'select',
							'std' 	=> 'false',
							'lockable'	=> true,
							'required'	=> array( 'type', 'equals', 'slider' ),
							'subtype'	=> array(
												__( 'Yes', 'avia_framework' )	=> 'true',
												__( 'No', 'avia_framework' )	=> 'false'
											)
						),

						array(
							'name' 	=> __( 'Slider autorotation duration', 'avia_framework' ),
							'desc' 	=> __( 'Images will be shown the selected amount of seconds.', 'avia_framework' ),
							'id' 	=> 'interval',
							'type' 	=> 'select',
							'std' 	=> '5',
							'lockable'	=> true,
							'required'	=> array( 'autoplay', 'equals', 'true' ),
							'subtype'	=> array( '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10', '15'=>'15', '20'=>'20', '30'=>'30', '40'=>'40', '60'=>'60', '100'=>'100' ) 
						),
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Slider Animation', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_animation_slider' ), $template );
			
			
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
								'template_id'	=> $this->popup_key( 'modal_content_slidecontent' )
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
								'template_id'	=> $this->popup_key( 'modal_advanced_link' )
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
							'name' 	=> __( 'Choose another Image', 'avia_framework' ),
							'desc' 	=> __( 'Either upload a new, or choose an existing image from your media library', 'avia_framework' ),
							'id' 	=> 'id',
							'fetch' => 'id',
							'type' 	=> 'image',
							'title'		=> __( 'Change Image', 'avia_framework' ),
							'button'	=> __( 'Change Image', 'avia_framework' ),
							'std'		=> '',
							'lockable'	=> true,
							'locked'	=> array( 'id' )
						),


						array(
							'name' 	=> __( 'Image Caption', 'avia_framework' ),
							'desc' 	=> __( 'Display a image caption on hover', 'avia_framework' ),
							'id' 	=> 'hover',
							'type' 	=> 'input',
							'std' 	=> '',
							'lockable'	=> true,
						)
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_slidecontent' ), $c );
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Partner/Logo Link?', 'avia_framework' ),
							'desc' 	=> __( 'Where should the image/logo link to?', 'avia_framework' ),
							'id' 	=> 'link',
							'type' 	=> 'linkpicker',
							'fetchTMPL'	=> true,
							'std'		=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No Link', 'avia_framework' )		=> '',
												__( 'Lightbox', 'avia_framework' )		=> 'lightbox',
												__( 'Set Manually', 'avia_framework' )	=> 'manually',
												__( 'Single Entry', 'avia_framework' )	=> 'single',
												__( 'Taxonomy Overview Page', 'avia_framework' )	=> 'taxonomy',
											),
						),

						array(
							'name' 	=> __( 'Link Title', 'avia_framework' ),
							'desc' 	=> __( 'Enter a link title', 'avia_framework' ),
							'id' 	=> 'linktitle',
							'type' 	=> 'input',
							'std' 	=> '',
							'lockable'	=> true,
							'required'	=> array( 'link', 'equals', 'manually' )
						),

						array(
							'name' 	=> __( 'Open Link in new Window?', 'avia_framework' ),
							'desc' 	=> __( 'Select here if you want to open the linked page in a new window', 'avia_framework' ),
							'id' 	=> 'link_target',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'required'	=> array( 'link', 'not_empty_and', 'lightbox' ),
							'subtype'	=> AviaHtmlHelper::linking_options()
						),
				
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
			
			$template = $this->update_template_lockable( 'heading', ' - <strong>{{heading}}</strong>' , $locked );
			$heading = ! empty( $attr['heading'] ) ? "- <strong>{$attr['heading']}</strong>" : '';
			
			$params = parent::editor_element( $params );
			
			$params['innerHtml'] .= "<div class='avia-element-label' {$template} data-update_element_template='yes'>{$heading}</div>";
			
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
			$default = array();
			$locked = array();
			$attr = $params['args'];
			Avia_Element_Templates()->set_locked_attributes( $attr, $this, $this->config['shortcode_nested'][0], $default, $locked );
			
			$img_template = $this->update_option_lockable( array( 'id', 'img_fakeArg' ), $locked );
			$template = $this->update_option_lockable( 'hover', $locked );

			$thumbnail = isset( $attr['id'] ) ? wp_get_attachment_image( $attr['id'] ) : '';

			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_title_container' data-update_element_template='yes'>";
			$params['innerHtml'] .=		"<span class='avia_slideshow_image' {$img_template}>{$thumbnail}</span>";
			$params['innerHtml'] .=		"<div class='avia_slideshow_content'>";
			$params['innerHtml'] .=			"<h4 class='avia_title_container_inner' {$template} >{$attr['hover']}</h4>";
			$params['innerHtml'] .=		'</div>';
			$params['innerHtml'] .= '</div>';

			return $params;
		}


		/**
		 * Returns false by default.
		 * Override in a child class if you need to change this behaviour.
		 * 
		 * @since 4.2.1
		 * @param string $shortcode
		 * @return boolean
		 */
		public function is_nested_self_closing( $shortcode )
		{
			if( in_array( $shortcode, $this->config['shortcode_nested'] ) )
			{
				return true;
			}

			return false;
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
			$default = array(
						'type'          => 'grid',
						'size'			=> 'featured',
						'ids'    	 	=> '',
						'autoplay'		=> 'false',
						'navigation'    => 'arrows',
						'animation'     => 'slide',
						'interval'		=> 5,
						'lazy_loading'	=> 'disabled',
						'heading'		=> '',
						'hover'			=> '',
						'columns'       => 3,
						'border'		=> '',
						'img_size_behave' => '',
						'heading_tag'	=> '',
						'heading_class'	=> '',
					);
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			$meta = aviaShortcodeTemplate::set_frontend_developer_heading_tag( $atts, $meta );
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			$add = array(
						'handle'		=> $shortcodename,
						'content'		=> ShortcodeHelper::shortcode2array( $content ),
						'class'			=> $meta['el_class'] . ' ' . $av_display_classes,
						'custom_markup' => $meta['custom_markup'],
						'custom_el_id'	=> ! empty( $meta['custom_el_id'] ) ? $meta['custom_el_id'] : '',
						'heading_tag'	=> ! empty( $meta['heading_tag'] ) ? $meta['heading_tag'] : '',
						'heading_class'	=> ! empty( $meta['heading_class'] ) ? $meta['heading_class'] : '',
					);
			
			$defaults = array_merge( $default, $add );

			$atts = shortcode_atts( $defaults, $atts, $this->config['shortcode'] );

			foreach( $atts['content'] as $key => &$item ) 
			{
				$item_def = $this->get_default_modal_group_args();
				Avia_Element_Templates()->set_locked_attributes( $item['attr'], $this, $this->config['shortcode_nested'][0], $item_def, $locked, $item['content'] );
			}
			
			unset( $item );
			
			$logo = new avia_partner_logo( $atts );
			return $logo->html();
		}

	}
}


if ( ! class_exists( 'avia_partner_logo' ) )
{
	class avia_partner_logo
	{
		/**
		 * slider count for the current page
		 * 
		 * @var int 
		 */
		static protected $slider = 0;
		
		/**
		 * base config set on initialization
		 * 
		 * @var array 
		 */
		protected $config;
		
		/**
		 * attachment posts for the current slider
		 * 
		 * @var array 
		 */
		protected $slides;
		
		/**
		 * number of slides
		 * 
		 * @var int 
		 */
		protected $slide_count;
		
		/**
		 * unique array of slide id's
		 * 
		 * @var array 
		 */
		protected $id_array;

		
		/**
		 * 
		 * @param array $config
		 */
		public function __construct( array $config )
		{
			$this->slides = array();
			$this->slide_count = 0;
			$this->id_array = array();

			$this->config = array_merge( array(
								'type'          => 'grid',
								'size'			=> 'featured',
								'ids'    	 	=> '',
								'autoplay'		=> 'false',
								'navigation'    => 'arrows',
								'animation'     => 'slide',
								'handle'		=> '',
								'heading'		=> '',
								'border'		=> '',
								'columns'       => 3,
								'interval'		=> 5,
								'class'			=> '',
								'custom_markup' => '',
								'custom_el_id'	=> '',
								'heading_tag'	=> '',
								'heading_class'	=> '',
								'hover'			=> '',
								'css_id'		=> '',
								'img_size_behave' =>'',
								'lazy_loading'	=> 'disabled',
								'content'		=> array()
				
						), $config );


			/**
			 * @since 4.7.6.2
			 * @param array $this->config
			 * @return array
			 */
			$this->config = apply_filters( 'avf_partner_logo_config', $this->config );
			
			
			//if we got subslides overwrite the id array
			if( ! empty( $this->config['content'] ) )
			{
				$this->extract_subslides( $this->config['content'] );
			}

			$this->set_slides( $this->config['ids'] );
		}
		
		/**
		 * 
		 * @since 4.2.5
		 * @added_by Günter
		 */
		public function __destruct() 
		{
			unset( $this->slides );
			unset( $this->config );
			unset( $this->id_array );
		}

		/**
		 * 
		 * @param string $ids
		 * @return void
		 */
		public function set_slides($ids)
		{
			if( empty( $ids ) ) 
			{
				return;
			}

			$this->slides = get_posts( array(
										'include'		=> $ids,
										'post_status'	=> 'inherit',
										'post_type'		=> 'attachment',
										'post_mime_type' => 'image',
										'order'			=> 'ASC',
										'orderby'		=> 'post__in'
									) );


			//resort slides so the id of each slide matches the post id
			$new_slides = array();
			$new_ids = array();
			foreach( $this->slides as $slide )
			{
				$new_slides[ $slide->ID ] = $slide;
				$new_ids[] = $slide->ID;
			}
			
			$slideshow_data = array();
			$slideshow_data['slides'] = $new_slides;
			$slideshow_data['id_array'] = explode( ',', $ids );
			$slideshow_data['slide_count'] = count( $slideshow_data['id_array'] );
			
			/**
			 * @used_by				config-wpml\config.php				10
			 * @since 4.4.2
			 */
			$slideshow_data = apply_filters( 'avf_avia_builder_slideshow_filter', $slideshow_data, $this );
			
			$this->slides 		= $slideshow_data['slides'];
			$this->id_array 	= $slideshow_data['id_array'];
			$this->slide_count 	= $slideshow_data['slide_count'];
		}

		public function set_size( $size )
		{
			$this->config['size'] = $size;
		}

		public function set_extra_class( $class )
		{
			$this->config['class'] .= " {$class}";
		}



		public function html()
		{
			$output = '';
			$counter = 0;
			avia_partner_logo::$slider++;
			
			if( $this->slide_count == 0 ) 
			{
				return $output;
			}

            extract( $this->config );
			
			$default_heading = ! empty( $heading_tag ) ? $heading_tag : 'h3';
			$args = array(
						'heading'		=> $default_heading,
						'extra_class'	=> $heading_class
					);

			$extra_args = array( $this );

			/**
			 * @since 4.5.5
			 * @return array
			 */
			$args = apply_filters( 'avf_customize_heading_settings', $args, __CLASS__, $extra_args );

			$heading_tag = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
			$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : $heading_class;

				
            $extraClass 		= 'first';
            $grid 				= 'one_third';
            $slide_loop_count 	= 1;
            $loop_counter		= 1;
            $total				= $columns % 2 ? 'odd' : 'even';
			$heading 			= ! empty( $heading ) ? "<{$heading_tag} class='{$css}'>{$heading}</{$heading_tag}>" : '&nbsp;';

            switch( $columns )
            {
                case '1': 
					$grid = 'av_fullwidth'; 
					break;
                case '2': 
					$grid = 'av_one_half'; 
					break;
                case '3':
					$grid = 'av_one_third'; 
					break;
                case '4': 
					$grid = 'av_one_fourth'; 
					break;
                case '5': 
					$grid = 'av_one_fifth'; 
					break;
                case '6': 
					$grid = 'av_one_sixth'; 
					break;
                case '7': 
					$grid = 'av_one_seventh'; 
					break;
                case '8': 
					$grid = 'av_one_eighth'; 
					break;
            }

            $data = AviaHelper::create_data_string( array( 'autoplay' => $autoplay, 'interval' => $interval, 'animation' => $animation ) );

            $thumb_fallback = '';
            $output .= "<div {$custom_el_id} {$data} class='avia-logo-element-container {$border} avia-logo-{$type} avia-content-slider avia-smallarrow-slider avia-content-{$type}-active noHover avia-content-slider".avia_partner_logo::$slider." avia-content-slider-{$total} {$class}' >";

			$heading_class = '';
			if( $navigation == 'no' ) 
			{
				$heading_class .= ' no-logo-slider-navigation ';
			}
				
			if( $heading == '&nbsp;') 
			{
				$heading_class .= ' no-logo-slider-heading ';
			}

			$output .= "<div class='avia-smallarrow-slider-heading $heading_class'>";

			if( $heading != '&nbsp;' || $navigation != 'no' )
			{
				$output .= "<div class='new-special-heading'>{$heading}</div>";
			}

			if( count( $this->id_array ) > $columns && $type == 'slider' && $navigation != 'no' )
			{
				if( $navigation == 'arrows' ) 
				{
					$output .= $this->slide_navigation_arrows();
				}
			}

			$output .= '</div>';


			$output .= "<div class='avia-content-slider-inner'>";


			foreach( $this->subslides as $key => $slides )
			{
				$id = isset( $slides['attr']['id'] ) ? $slides['attr']['id'] : 0;
				$img = '';
				$link = '';
				$img = '';

				if( isset( $this->slides[ $id ] ) )
				{
					$slide = $this->slides[ $id ];
					$img_src = wp_get_attachment_image_src( $slide->ID, $size );

					if( empty( $ratio_style ) )
					{
						$height = intval($img_src[2]);
						$width  = intval($img_src[1]);
						$ratio  = ( 100 / $width ) * $height;
						$ratio_style = "padding-bottom:{$ratio}%;";
					}

					$meta = array_merge( array( 'link' => '', 'link_target' => '', 'linktitle' => '', 'hover' => '', 'custom_markup' => '' ), $slides['attr'] );
					extract($meta);

					$style = "style='{$ratio_style} background-image:url({$img_src[0]});'";
					$img = "<span class='av-partner-fake-img' {$style}></span>";

					if( $img_size_behave == 'no_stretch' )
					{
					   $img = wp_get_attachment_image( $slide->ID, $size );
					   $img = Av_Responsive_Images()->make_image_responsive( $img, $slide->ID, $this->config['lazy_loading'] );
					}

					$link = AviaHelper::get_url( $link, $slide->ID, true );
					$blank = AviaHelper::get_link_target( $link_target );
				}

				$parity			= $loop_counter % 2 ? 'odd' : 'even';
				$last       	= $this->slide_count == $slide_loop_count ? " post-entry-last " : '';
				$post_class 	= "post-entry slide-entry-overview slide-loop-{$slide_loop_count} slide-parity-{$parity} {$last}";
				$thumb_class	= 'real-thumbnail';
				$single_data 	= empty( $hover ) ? '' : 'data-avia-tooltip="' . $hover . '"';

				if( $loop_counter == 1 ) 
				{
					$output .= "<div class='slide-entry-wrap' >";
				}

				if( ! empty( $link ) )
				{
					$lightbox_attr = Av_Responsive_Images()->html_attr_image_src( $link, false );
					$imgage = "<a {$lightbox_attr} data-rel='slide-" . avia_partner_logo::$slider . "' class='slide-image' title='{$linktitle}' {$blank}>{$img}</a>";
				}
				else
				{
					$imgage = $img;
				}
				
				$output .= "<div {$single_data} class='slide-entry flex_column no_margin {$post_class} {$grid} {$extraClass} {$thumb_class}'>";
				$output .=		$imgage;
				$output .= '</div>';

				$loop_counter ++;
				$slide_loop_count ++;
				$extraClass = '';

				if( $loop_counter > $columns )
				{
					$loop_counter = 1;
					$extraClass = 'first';
				}

				if( $loop_counter == 1 || ! empty( $last ) )
				{
					$output .= '</div>';
				}
			}

			$output .= '</div>';


			if( count( $this->id_array ) > $columns && $type == 'slider' && $navigation != 'no' )
			{
				if( $navigation == 'dots' ) 
				{
					$output .= $this->slide_navigation_dots();
				}
			}

			$output .= '</div>';

			return $output;
		}

		/**
		 * Create arrows to scroll slides
		 * 
		 * @since 4.8.3			reroute to aviaFrontTemplates
		 * @return string
		 */
		protected function slide_navigation_arrows()
		{
			$args = array(
						'context'		=> get_class(),
						'params'		=> $this->config
					);
			
			return aviaFrontTemplates::slide_navigation_arrows( $args );
		}

		/**
		 * 
		 * @return string
		 */
		protected function slide_navigation_dots()
		{
			$args = array(
						'total_entries'		=> $this->slide_count,
						'container_entries'	=> $this->config['columns'],
						'context'			=> get_class(),
						'params'			=> $this->config
					);
			
			
			return aviaFrontTemplates::slide_navigation_dots( $args );
		}

		/**
		 * 
		 * @param array $slide_array
		 */
		protected function extract_subslides( $slide_array )
		{
			$this->config['ids'] = array();
			$this->subslides = array();

			foreach( $slide_array as $key => $slide )
			{
				$this->subslides[ $key ] = $slide;
				$this->config['ids'][] = $slide['attr']['id'];
			}

			$this->config['ids'] = implode( ',', $this->config['ids'] );
			
			unset( $this->config['content'] );
		}
	}
}
