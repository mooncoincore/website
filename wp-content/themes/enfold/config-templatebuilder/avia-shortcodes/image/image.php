<?php
/**
 * Image
 * 
 * Shortcode which inserts an image of your choice
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_image' ) )
{
	class avia_sc_image extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Image', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-image.png';
			$this->config['order']			= 100;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_image';
//			$this->config['modal_data']     = array( 'modal_class' => 'mediumscreen' );
			$this->config['tooltip'] 	    = __( 'Inserts an image of your choice', 'avia_framework' );
			$this->config['preview'] 		= 1;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}

		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-image', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/image/image.css', array( 'avia-layout' ), false );
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
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array( 
													$this->popup_key( 'styling_image_styling' ),
													$this->popup_key( 'styling_image_caption' )
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
								'template_id'	=> $this->popup_key( 'advanced_animation' ),
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_link' ),
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_seo' ),
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
						'args'			=> array( 
												'sc'	=> $this
											)
					),

				array(
						'type' 	=> 'tab_container_close',
						'nodescription' => true
					),	
					
				array(	
						'id'	=> 'av_element_hidden_in_editor',
						'type'	=> 'hidden',
						'std'	=> '0'
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
			/**
			 * Content Tab
			 * ===========
			 */
						
			$c = array(
						array(
							'name'		=> __( 'Choose Image', 'avia_framework' ),
							'desc'		=> __( 'Either upload a new, or choose an existing image from your media library', 'avia_framework' ),
							'id'		=> 'src',
							'type'		=> 'image',
							'title'		=> __( 'Insert Image', 'avia_framework' ),
							'button'	=> __( 'Insert', 'avia_framework' ),
							'std'		=> AviaBuilder::$path['imagesURL'] . 'placeholder.jpg',
							'lockable'	=> true,
							'locked'	=> array( 'src', 'attachment', 'attachment_size' )
						),
				
						array(
							'name'		=> __( 'Copyright Info', 'avia_framework' ),
							'desc'		=> __( 'Use the media manager to add/edit the copyright info.', 'avia_framework' ),
							'id'		=> 'copyright',
							'type'		=> 'select',
							'std'		=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No', 'avia_framework' )									=> '',
												__( 'Yes, always display copyright info', 'avia_framework' )	=> 'always',
												__( 'Yes, display icon and reveal copyright info on hover', 'avia_framework' )	=> 'icon-reveal',
											)
						),
	
						array(
							'name' 	=> __( 'Image Caption', 'avia_framework' ),
							'desc' 	=> __( 'Display a caption overlay?', 'avia_framework' ),
							'id' 	=> 'caption',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )	=> 'yes',
											)
						),
									
						array(
							'name' 		=> __( 'Caption', 'avia_framework' ),
							'desc'		=> __( 'Add your caption text', 'avia_framework' ),
							'id' 		=> 'content',
							'type' 		=> 'textarea',
							'std' 		=> '',
							'lockable'	=> true,
							'required'	=> array( 'caption', 'equals', 'yes' )
						),

					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_image' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						
						array(	
							'name'		=> __( 'Caption custom font size?', 'avia_framework' ),
							'desc'		=> __( 'Size of your caption in pixel', 'avia_framework' ),
							'id'		=> 'font_size',
							'type'		=> 'select',
							'std'		=> '',
							'lockable'	=> true,
							'required'	=> array( 'caption', 'equals', 'yes' ),
							'subtype'	=> AviaHtmlHelper::number_array( 10, 40, 1, array( 'Default' => '' ), 'px' )
						),

						array(
							'name'		=> __( 'Caption Overlay Opacity', 'avia_framework' ),
							'desc'		=> __( 'Set the opacity of your overlay: 0.1 is barely visible, 1.0 is opaque ', 'avia_framework' ),
							'id'		=> 'overlay_opacity',
							'type'		=> 'select',
							'std'		=> '0.4',
							'lockable'	=> true,
							'required'	=> array( 'caption', 'equals','yes' ),
							'subtype'	=> array(   
												__( '0.1', 'avia_framework' )	=> '0.1',
												__( '0.2', 'avia_framework' )	=> '0.2',
												__( '0.3', 'avia_framework' )	=> '0.3',
												__( '0.4', 'avia_framework' )	=> '0.4',
												__( '0.5', 'avia_framework' )	=> '0.5',
												__( '0.6', 'avia_framework' )	=> '0.6',
												__( '0.7', 'avia_framework' )	=> '0.7',
												__( '0.8', 'avia_framework' )	=> '0.8',
												__( '0.9', 'avia_framework' )	=> '0.9',
												__( '1.0', 'avia_framework' )	=> '1',
											)
						),
								  		
						array(
							'name'		=> __( 'Caption Overlay Background Color', 'avia_framework' ),
							'desc'		=> __( 'Select a background color for your overlay here.', 'avia_framework' ),
							'id'		=> 'overlay_color',
							'type'		=> 'colorpicker',
							'container_class' => 'av_half av_half_first',
							'std'		=> '#000000',
							'lockable'	=> true,
							'required'	=> array( 'caption', 'equals', 'yes' )
						),	
									
						array(	
							'name' 	=> __( 'Caption Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a font color for your overlay here.', 'avia_framework' ),
							'id' 	=> 'overlay_text_color',
							'type' 	=> 'colorpicker',
							'std' 	=> '#ffffff',
							'container_class' => 'av_half',
							'lockable'	=> true,
							'required'	=> array( 'caption', 'equals', 'yes' ),
						),
				
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Image Caption', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_image_caption' ), $template );
			
			
			
			$c = array(
						array(
							'name' 	=> __( 'Image Styling', 'avia_framework' ),
							'desc' 	=> __( 'Choose a styling variaton', 'avia_framework' ),
							'id' 	=> 'styling',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Default',  'avia_framework' )	=> '',
												__( 'Circle (image height and width must be equal)',  'avia_framework' )	=> 'circle',
												__( 'No Styling (no border, no border radius etc)',  'avia_framework' )		=> 'no-styling',
											)
						),

						array(
							'name' 	=> __( 'Image Alignment', 'avia_framework' ),
							'desc' 	=> __( 'Choose here, how to align your image', 'avia_framework' ),
							'id' 	=> 'align',
							'type' 	=> 'select',
							'std' 	=> 'center',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Center',  'avia_framework' )		=> 'center',
												__( 'Right',  'avia_framework' )		=> 'right',
												__( 'Left',  'avia_framework' )			=> 'left',
												__( 'No special alignment', 'avia_framework' )	=> '',
											)
						)

				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Image Styling', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_image_styling' ), $template );
			
			
			
			/**
			 * Advanced Tab
			 * ============
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
												__( 'None', 'avia_framework' )	=> 'no-animation',
												__( 'Fade Animations', 'avia_framework')	=> array(
															__( 'Fade in', 'avia_framework' )	=> 'fade-in',
															__( 'Pop up', 'avia_framework' )	=> 'pop-up',
														),
												__( 'Slide Animations', 'avia_framework' ) => array(
															__( 'Top to Bottom', 'avia_framework' ) => 'top-to-bottom',
															__( 'Bottom to Top', 'avia_framework' ) => 'bottom-to-top',
															__( 'Left to Right', 'avia_framework' ) => 'left-to-right',
															__( 'Right to Left', 'avia_framework' ) => 'right-to-left',
														),
												__( 'Rotate',  'avia_framework' ) => array(
															__( 'Full rotation', 'avia_framework' )			=> 'av-rotateIn',
															__( 'Bottom left rotation', 'avia_framework' )	=> 'av-rotateInUpLeft',
															__( 'Bottom right rotation', 'avia_framework' )	=> 'av-rotateInUpRight',
														)
											)
						),
				
						array(
							'name' 	=> __( 'Image Hover effect', 'avia_framework' ),
							'desc' 	=> __( 'Add a mouse hover effect to the image', 'avia_framework' ),
							'id' 	=> 'hover',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No', 'avia_framework' )	=> '',
												__( 'Yes, slightly increase the image size', 'avia_framework' )	=> 'av-hover-grow',
												__( 'Yes, slightly zoom the image', 'avia_framework' )			=> 'av-hover-grow av-hide-overflow',
											)
						),
				
						array(
							'name' 	=> __( 'Caption Appearance', 'avia_framework' ),
							'desc' 	=> __( 'When to display the caption?', 'avia_framework' ),
							'id' 	=> 'appearance',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'required'	=> array( 'caption', 'equals', 'yes' ),
							'subtype'	=> array(
												__( 'Always display caption', 'avia_framework' )	=> '',
												__( 'Only display on hover', 'avia_framework' )		=> 'on-hover',
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
							'name'	=> __( 'Image Link?', 'avia_framework' ),
							'desc'	=> __( 'Where should your image link to?', 'avia_framework' ),
							'id'	=> 'link',
							'type'	=> 'linkpicker',
							'fetchTMPL'	=> true,
							'std'		=> '',
							'lockable'	=> true,
							'subtype'	=> array(
											__( 'No Link', 'avia_framework' )	=> '',
											__( 'Lightbox', 'avia_framework' )	=> 'lightbox',
											__( 'Set Manually', 'avia_framework' )	=> 'manually',
											__( 'Single Entry', 'avia_framework' )	=> 'single',
											__( 'Taxonomy Overview Page', 'avia_framework' )	=> 'taxonomy',
										)
						),
				
						array(
							'name'	=> __( 'Open new tab/window', 'avia_framework' ),
							'desc'	=> __( 'Do you want to open the link url in a new tab/window?', 'avia_framework' ),
							'id'	=> 'target',
							'type'	=> 'select',
							'std'	=> '',
							'lockable'	=> true,
							'required'	=> array( 'link', 'not_empty_and', 'lightbox' ),
							'subtype'	=> AviaHtmlHelper::linking_options()
						),

					);
						
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Image Link Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_link' ), $template );
			
			$c = array(
						array(
							'name' 			=> __( 'Custom Title Attribute', 'avia_framework' ),
							'desc' 			=> __( 'Add a custom title attribute limited to this instance, replaces media gallery settings.', 'avia_framework' ),
							'id' 			=> 'title_attr',
							'type' 			=> 'input',
							'std' 			=> '',
							'lockable'		=> true,
						),

						array(
							'name' 			=> __( 'Custom Alt Attribute', 'avia_framework' ),
							'desc' 			=> __( 'Add a custom alt attribute limited to this instance, replaces media gallery settings.', 'avia_framework' ),
							'id' 			=> 'alt_attr',
							'type' 			=> 'input',
							'std' 			=> '',
							'lockable'		=> true,
						)
					);
			
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'SEO improvements', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_seo' ), $template );
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

			$params['innerHtml']  = "<div class='avia_image avia_image_style avia_hidden_bg_box' data-update_element_template='yes'>";
			$params['innerHtml'] .=		'<div ' . $this->class_by_arguments_lockable( 'align', $attr, $locked ) . '>';
			$params['innerHtml'] .=			"<div class='avia_image_container' {$template}>{$img}</div>";
			$params['innerHtml'] .=		'</div>';
			$params['innerHtml'] .= '</div>';
			
			$params['class'] = '';

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
							'src'				=> '',
							'title_attr'		=> '',
							'alt_attr'			=> '',
							'animation'			=> 'no-animation', 
							'lazy_loading'		=> 'disabled',
							'link'				=> '', 
							'attachment'		=> '', 
							'attachment_size'	=> '', 
							'target'			=> '', 
							'styling'			=> '', 
							'caption'			=> '',
							'copyright'			=> '',
							'font_size'			=> '', 
							'appearance'		=> '', 
							'hover'				=> '',
							'align'				=> 'center',
							'overlay_opacity'	=> '0.4', 
							'overlay_color'		=> '#444444', 
							'overlay_text_color'	=> '#ffffff',
				
								//	added for shortcode handler html output
							'attachment_id'		=> '', 
							'src_original'		=> '',			//	save original source in case shortcode was called directly and ID was added to source
							'img_h'				=> '',
							'img_w'				=> '',
							'copyright_text'	=> ''
						);
			
			$default = $this->sync_sc_defaults_array( $default, 'no_modal_item', 'no_content' );
			
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			//	save original value for shortcode handler
			$atts['src_original'] = $atts['src'];
			
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
										'orderby'			=> 'post__in' 
									)
								);

				if( is_array( $posts ) && ! empty( $posts ) )
				{
					$attachment_entry = $posts[0];
					$atts['attachment_id'] = $attachment_entry->ID;
					
					if( ! empty( $atts['alt_attr'] ) )
					{
						$alt = $atts['alt_attr'];
					}
					else
					{
						$alt = get_post_meta( $attachment_entry->ID, '_wp_attachment_image_alt', true );
					}
					
					$atts['alt_attr'] = ! empty( $alt ) ? esc_attr( trim( $alt ) ) : '';
					
					if( ! empty( $atts['title_attr'] ) )
					{
						$title = $atts['title_attr'];
					}
					else
					{
						$title = $attachment_entry->post_title;
					}
					
					$atts['title_attr'] = ! empty( $title ) ? esc_attr( trim( $title ) ) : '';
					
					if( $atts['copyright'] !== '') 
					{
						$copyright_text = get_post_meta( $attachment_entry->ID, '_avia_attachment_copyright', true );
						
						/**
						 * Allow to filter the copyright text
						 * 
						 * @since 4.7.4.1
						 * @param string $copyright_text
						 * @param string $shortcodename			context calling the filter
						 * @param int $attachment_entry->ID
						 * @return string
						 */
						$atts['copyright_text'] = apply_filters( 'avf_attachment_copyright_text', $copyright_text, $shortcodename, $attachment_entry->ID );
					}

					if( ! empty( $atts['attachment_size'] ) )
					{
						$src = wp_get_attachment_image_src( $attachment_entry->ID, $atts['attachment_size'] );

						$atts['img_h'] = ! empty( $src[2] ) ? $src[2] : '';
						$atts['img_w'] = ! empty( $src[1] ) ? $src[1] : '';
						$atts['src'] = ! empty( $src[0] ) ? $src[0] : '';
					}
				}
			}
			else
			{
				$atts['attachment'] = false;
			}
			
			if( empty( $atts['src'] ) )
			{
				$result['default'] = $default;
				$result['atts'] = $atts;
				$result['content'] = $content;

				return $result;
			}
			
			
			$element_styling->create_callback_styles( $atts );
			
			if( is_numeric( $atts['src'] ) )
			{
				$classes = array(
							'avia_image',
							$element_id
					);
			}
			else
			{
				$classes = array(
							'avia-image-container',
							$element_id
					);
			}
			
			$classes[] = "av-styling-{$atts['styling']}";
			
			if( ! empty( $atts['hover'] ) )
			{
				$classes[] = $atts['hover'];
			}
			
			if( $atts['animation'] != 'no-animation' )
			{
				$classes[] = 'avia_animated_image';
				$classes[] = 'avia_animate_when_almost_visible';
				$classes[] = $atts['animation'];
			}
			
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes( 'container', $this->class_by_arguments( 'align', $atts, true, 'array' ) );
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			if( ! is_numeric( $atts['src'] ) )
			{
				
				$element_styling->add_styles( 'container-overlay', array( 'color' => $atts['overlay_text_color'] ) );
			
				if( ! empty( $atts['font_size'] ) )
				{
					$element_styling->add_styles( 'container-overlay', array( 'font-size' => $atts['font_size'] . 'px' ) );
				}
				
				if( $atts['caption'] == 'yes' )
				{
					$element_styling->add_styles( 'container-caption', array( 'opacity' => $atts['overlay_opacity'] ) );
					$element_styling->add_styles( 'container-caption', array( 'background-color' => $atts['overlay_color'] ) );
					
					$element_styling->add_classes( 'container', 'noHover' );
					
					if( empty( $atts['appearance'] ) ) 
					{
						$atts['appearance'] = 'hover-deactivate';
					}
					
					$element_styling->add_classes( 'container', "av-overlay-{$atts['appearance']}" );
				}
				
				if( ! empty( $atts['copyright_text'] ) )
				{
					$element_styling->add_classes( 'container', 'av-has-copyright' );
					
					if( ! empty( $atts['copyright'] ) )
					{
						$element_styling->add_classes( 'container', "av-copyright-{$atts['copyright']}" );
					}
				}
				
				$selectors = array(
							'container'				=> ".avia-image-container.{$element_id}",
							'container-caption'		=> ".avia-image-container.{$element_id} .av-caption-image-overlay-bg",
							'container-overlay'		=> ".avia-image-container.{$element_id} .av-image-caption-overlay-center",
						);
			
				$element_styling->add_selectors( $selectors );
			}
			
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
			
			if( empty( $atts['src'] ) )
			{
				return '';
			}
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes
			
			extract( $atts );
			

			$output = '';
			
			if( is_numeric( $src_original ) )
			{
				$img_atts = array(
							'class'	=> $element_styling->get_class_string( 'container' )
						);

				if( ! empty( $img_h ) ) 
				{
					$img_atts['height'] = $img_h;
				}
				if( ! empty( $img_w ) ) 
				{
					$img_atts['width'] = $img_w;
				}

				if( $lazy_loading != 'enabled' )
				{
					Av_Responsive_Images()->add_attachment_id_to_not_lazy_loading( $src );
				}

				$output .= wp_get_attachment_image( $src, 'large', false, $img_atts );
			}
			else
			{
				$link = AviaHelper::get_url( $link, $attachment, true );
				$blank = AviaHelper::get_link_target( $target );

				$overlay = '';

				if( $caption == 'yes' )
				{	
					$overlay  = "<div class='av-image-caption-overlay'>";
					$overlay .=		"<div class='av-caption-image-overlay-bg'></div>";
					$overlay .=		"<div class='av-image-caption-overlay-position'>";
					$overlay .=			"<div class='av-image-caption-overlay-center'>";
					$overlay .=				ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $content ) );
					$overlay .=			'</div>';
					$overlay .=		'</div>';
					$overlay .=	'</div>';
				}

				$copyright_tag = '';
				if( ! empty( $copyright_text ) )
				{
					$copyright_tag = '<small class="avia-copyright">' . esc_html( $copyright_text ) . '</small>';
				}

				$markup_url = avia_markup_helper( array( 'context' => 'image_url', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );
				$markup_img = avia_markup_helper( array( 'context' => 'image', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );

				$style_tag = $element_styling->get_style_tag( $element_id );
				$container_class = $element_styling->get_class_string( 'container' );

				$output .= $style_tag;
				$output .= "<div {$meta['custom_el_id']} class='{$container_class} {$av_display_classes}' {$markup_img}>";
				$output .=		'<div class="avia-image-container-inner">';
				$output .=			'<div class="avia-image-overlay-wrap">';

				if( ! empty( $link ) )
				{
					$img_tag = "<img class='avia_image' src='{$src}' alt='{$alt_attr}' title='{$title_attr}' {$markup_url} />";
					$img_tag = Av_Responsive_Images()->prepare_single_image( $img_tag, $attachment_id, $lazy_loading );
					$lightbox_attr = Av_Responsive_Images()->html_attr_image_src( $link, false );

					$output .=			"<a {$lightbox_attr} class='avia_image' {$blank}>{$overlay}{$img_tag}</a>";
				}
				else
				{
					$hw = '';
					if( ! empty( $img_h ) ) 
					{
						$hw .= ' height="' . $img_h . '"';
					}

					if( ! empty( $img_w ) ) 
					{
						$hw .= ' width="' . $img_w . '"';
					}

					$img_tag = "<img class='avia_image' src='{$src}' alt='{$alt_attr}' title='{$title_attr}' {$hw} {$markup_url} />";
					$img_tag = Av_Responsive_Images()->prepare_single_image( $img_tag, $attachment_id, $lazy_loading );

					$output .=			"{$overlay}{$img_tag}";
				}
					
				$output .=			'</div>';
				$output .=		$copyright_tag;

				$output .=		'</div>';
				$output .= '</div>';
			}

			return Av_Responsive_Images()->make_content_images_responsive( $output );
		}

	}
}

