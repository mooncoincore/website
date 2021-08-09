<?php
/**
 * Horizontal Gallery
 * 
 * Creates a horizontal scrollable gallery
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_gallery_horizontal' ) )
{
	class avia_sc_gallery_horizontal extends aviaShortcodeTemplate
	{
		/**
		 *
		 * @var int 
		 */
		static protected $hor_gallery = 0;

		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['is_fullwidth']	= 'yes';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Horizontal Gallery', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-accordion-slider.png';
			$this->config['order']			= 6;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_horizontal_gallery';
			$this->config['tooltip']        = __( 'Creates a horizontal scrollable gallery', 'avia_framework' );
			$this->config['preview'] 		= false;
			$this->config['drag-level'] 	= 3;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'always';				//	we use original code - not $meta
		}


		function extra_assets()
		{
			wp_enqueue_style( 'avia-module-slideshow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow.css', array( 'avia-layout' ), false );

			//load css
			wp_enqueue_style( 'avia-module-gallery-hor', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/gallery_horizontal/gallery_horizontal.css', array( 'avia-module-slideshow' ), false );

				//load js
			wp_enqueue_script( 'avia-module-gallery-hor', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/gallery_horizontal/gallery_horizontal.js', array( 'avia-shortcodes' ), false, true );
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
													$this->popup_key( 'styling_gallery' ),
													$this->popup_key( 'styling_controls' )
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
								'template_id'	=> $this->popup_key( 'advanced_link' )
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
			
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name'		=> __( 'Edit Gallery', 'avia_framework' ),
							'desc'		=> __( 'Create a new Gallery by selecting existing or uploading new images', 'avia_framework' ),
							'id'		=> 'ids',
							'type'		=> 'gallery',
							'title'		=> __( 'Add/Edit Gallery', 'avia_framework' ),
							'button'	=> __( 'Insert Images', 'avia_framework' ),
							'std'		=> '',
							'modal_class' => 'av-show-image-custom-link',
							'lockable'	=> true
						)
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_entries' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Gallery Height', 'avia_framework' ),
							'desc' 	=> __( 'Set the gallery height in relation to the gallery container width', 'avia_framework' ),
							'id' 	=> 'height',
							'type' 	=> 'select',
							'std' 	=> '25',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 0, 50, 5, array(),'%' )
						),
					
						array(
							'name' 	=> __( 'Image Size', 'avia_framework' ),
							'desc' 	=> __( 'Choose size for each image', 'avia_framework' ),
							'id' 	=> 'size',
							'type' 	=> 'select',
							'std' 	=> 'large',
							'lockable'	=> true,
							'subtype'	=> AviaHelper::get_registered_image_sizes( array( 'logo' ) )
						),
				
						array(
							'name' 	=> __( 'Gap between images', 'avia_framework' ),
							'desc' 	=> __( 'Select the gap between the images', 'avia_framework' ),
							'id' 	=> 'gap',
							'type' 	=> 'select',
							'std' 	=> 'large',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No Gap', 'avia_framework' )		=> 'no',
												__( '1 Pixel Gap', 'avia_framework' )	=> '1px',
												__( 'Large Gap', 'avia_framework' )		=> 'large',
											)
						),    	
	                
						array(
							'name' 	=> __( 'Active Image Style', 'avia_framework' ),
							'desc' 	=> __( 'How do you want to display the active image', 'avia_framework' ),
							'id' 	=> 'active',
							'type' 	=> 'select',
							'std' 	=> 'enlarge',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No effect', 'avia_framework' )		=> '',
												__( 'Enlarge Image', 'avia_framework' )	=> 'enlarge',
											)
						),  
	                
						array(
							'name' 	=> __( 'Initial Active Image', 'avia_framework' ),
							'desc' 	=> __( 'Enter the Number of the image that should be open initially.', 'avia_framework' ),
							'id' 	=> 'initial',
							'type' 	=> 'input',
							'std' 	=> '',
							'lockable'	=> true
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Gallery Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_gallery' ), $template );
			
			$c = array(
						array(	
							'name' 	=> __( 'Gallery control styling?', 'avia_framework' ),
							'desc' 	=> __( 'Here you can select if and how to display the slideshow controls', 'avia_framework' ),
							'id' 	=> 'control_layout',
							'type' 	=> 'select',
							'std' 	=> 'av-control-default',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )		=> 'av-control-default',
												__( 'Minimal White', 'avia_framework' )	=> 'av-control-minimal', 
												__( 'Minimal Black', 'avia_framework' )	=> 'av-control-minimal av-control-minimal-dark',
												__( 'Hidden', 'avia_framework' )		=> 'av-control-hidden'
											)
						),
					
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Control Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_controls' ), $template );
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Image Link', 'avia_framework' ),
							'desc' 	=> __( 'By default images link to a larger image version in a lightbox. You can change this here. A custom link can be added when editing the images in the gallery.', 'avia_framework' ),
							'id' 	=> 'links',
							'type' 	=> 'select',
							'std' 	=> 'active',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Lightbox linking active', 'avia_framework' )						=> 'active',
												__( 'Use custom link (fallback is no link)', 'avia_framework' )			=> '',
												__( 'No, don\'t add a link to the images at all', 'avia_framework' )	=> 'no_links',
											)
						),
				
						array(
							'name'		=> __( 'Custom link destination', 'avia_framework' ),
							'desc'		=> __( 'Select where an existing custom link should be opened.', 'avia_framework' ),
							'id'		=> 'link_dest',
							'type'		=> 'select',
							'std'		=> '',
							'lockable'	=> true,
							'required'	=> array( 'links', 'equals', '' ),
							'subtype'	=> array(
												__( 'Open in same window', 'avia_framework' )		=> '',
												__( 'Open in a new window', 'avia_framework' )		=> '_blank'
											)
						),
					
						array(
							'name'		=> __( 'Lightbox image description text', 'avia_framework' ),
							'desc'		=> __( 'Select which text defined in the media gallery is displayed below the lightbox image.', 'avia_framework' ),
							'id'		=> 'lightbox_text',
							'type'		=> 'select',
							'std'		=> '',
							'lockable'	=> true,
							'required'	=> array( 'links', 'equals', 'active' ),
							'subtype'	=> array(
												__( 'No text', 'avia_framework' )										=> 'no_text',
												__( 'Image title', 'avia_framework' )									=> '',
												__ ('Image description (or image title if empty)', 'avia_framework' )	=> 'description',
												__( 'Image caption (or image title if empty)', 'avia_framework' )		=> 'caption'
											)
						)
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Link Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_link' ), $template );
			
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
						'height'      		=> '400',
						'size' 				=> 'large',
						'links' 			=> 'active',
						'lightbox_text'		=> '',				//	default to title
						'link_dest'			=> '',
						'gap'				=> 'large',
						'ids'    	 		=> '',
						'active'    		=> 'enlarge',
						'control_layout'	=> 'av-control-default',
						'initial'			=> '',
						'id'				=> '',
						'lazy_loading'		=> 'enabled',
//						'attachments'		=> ''				// array of attachments to avoid double query in shortcode handler
					);

			$default = $this->sync_sc_defaults_array( $default );
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			
			
			$element_styling->create_callback_styles( $atts );
			
			
			$classes = array(
						'av-horizontal-gallery',
						$element_id,
						"av-horizontal-gallery-{$atts['gap']}-gap",
						"av-horizontal-gallery-{$atts['active']}-effect",
						$atts['control_layout']
					);
			
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			
			if( 'enlarge' == $atts['active'] )
			{
				$enlarge_by = 1.3;
				$padding = ( ( $atts['height'] * $enlarge_by ) - $atts['height'] ) / 2;
				
				$element_styling->add_styles( 'container', array( 'padding' => "{$padding}% 0px;" ) );
			}
			
			$element_styling->add_styles( 'container-inner', array( 'padding-bottom' => "{$atts['height']}%" ) );
			
			$selectors = array(
						'container'			=> ".av-horizontal-gallery.{$element_id}",
						'container-inner'	=> ".av-horizontal-gallery.{$element_id} .av-horizontal-gallery-inner",
					);
			
			$element_styling->add_selectors( $selectors );
			
			
			$result['default'] = $default;
			$result['atts'] = $atts;
			$result['content'] = $content;
			$result['meta'] = $meta;
			
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
			
			extract( AviaHelper::av_mobile_sizes( $atts ) );	// return $av_font_classes, $av_title_font_classes and $av_display_classes 
			
			extract( $atts );
			
			
			$attachments = get_posts( array(
								'include'			=> $ids,
								'post_status'		=> 'inherit',
								'post_type'			=> 'attachment',
								'post_mime_type'	=> 'image',
								'order'				=> 'DESC',
								'orderby'			=> 'post__in'
							)
						);
			
			
			$display_char = av_icon( 'ue869', 'entypo-fontello' );
			$data_enlarge = '';

			if( $active == 'enlarge' )
			{
				$enlarge_by = 1.3;
				$data_enlarge = " data-av-enlarge='{$enlarge_by}' ";
			}
				
			$output = '';
						
			if( ! empty( $attachments ) && is_array( $attachments ) )
			{
				avia_sc_gallery_horizontal::$hor_gallery++;
				
				/**
				 * @since 4.8.2
				 * @param string $image_size
				 * @param string $shortcode
				 * @param array $atts
				 * @param string $content
				 * @return string
				 */
				$lightbox_img_size = apply_filters( 'avf_alb_lightbox_image_size', 'large', $this->config['shortcode'], $atts, $content );

				if( ! is_numeric( $initial ) || ( (int) $initial <= 0 ) )
				{
					$initial = '';
				}

				if( ! empty( $initial ) )
				{
					$initial = ( (int) $initial > count( $attachments ) ) ? count( $attachments ) : $initial;
					$initial = "data-av-initial='{$initial}' ";
				}

				$counter = 0;
				$markup = avia_markup_helper( array( 'context' => 'image', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );

				$add_id = ShortcodeHelper::is_top_level() ? '' : $meta['custom_el_id'];
				
				
				$style_tag = $element_styling->get_style_tag( $element_id );
				$container_class = $element_styling->get_class_string( 'container' );

				$output .= $style_tag;
				$output .= "<div {$add_id} class='{$container_class} av-horizontal-gallery-" . self::$hor_gallery . " {$av_display_classes}' {$markup} {$data_enlarge} {$initial}>";

				$output .=		$this->slide_navigation_arrows( $atts );


				$output .=		"<div class='av-horizontal-gallery-inner' data-av-height='{$height}'>";
				$output .=			'<div class="av-horizontal-gallery-slider">';

				foreach( $attachments as $attachment )
				{
					$counter ++;
					$img = wp_get_attachment_image_src( $attachment->ID, $size );
					$lightbox_img_src = Av_Responsive_Images()->responsive_image_src( $attachment->ID, $lightbox_img_size );

					$alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
					$alt = ! empty( $alt ) ? esc_attr( $alt ) : '';

					$title = trim( $attachment->post_title ) ? esc_attr( $attachment->post_title ) : '';
					$description = trim( $attachment->post_content ) ? esc_attr( $attachment->post_content ) : '';
					$caption = trim( $attachment->post_excerpt ) ? esc_attr( $attachment->post_excerpt ) : '';

					$custom_link = get_post_meta( $attachment->ID, 'av-custom-link', true );
					$custom_link = ! empty( $custom_link ) ? esc_attr( $custom_link ) : '';

					$lightbox_title = $title;
					switch( $lightbox_text )
					{
						case 'caption':
							$lightbox_title = ( '' != $caption ) ? $caption : $title;
							break;
						case 'description':
							$lightbox_title = ( '' != $description ) ? $description : $title;
							break;
						case 'no_text':
							$lightbox_title = '';
					}

					if( $links != '' )		//	ignore custom link, if lightbox is active
					{
						$custom_link = '';
					}
					else if( $custom_link != '' )
					{
						if( '' != $title )
						{
							$title = ' - ' . $title;
						}
						$title = __( 'Click to show details', 'avia_framework' ) . $title;
					}

					$output .= "<div class='av-horizontal-gallery-wrap noHover'>";

					if( ( '' == $links ) && ( $custom_link != '' ) ) 
					{
						$target = ( $link_dest != '' ) ?  ' target="' . $link_dest . '" rel="noopener noreferrer"' : '';
						$output .= '<a href="' . $custom_link . '"' . $target . '>';
					}

					$img_tag = "<img class='av-horizontal-gallery-img' width='{$img[1]}' height='{$img[2]}' src='{$img[0]}' title='{$title}' alt='{$alt}' />";	
					$img_tag = Av_Responsive_Images()->prepare_single_image( $img_tag, $attachment->ID, $lazy_loading );
					
					$output .= $img_tag;

					if( ( '' == $links ) && ( $custom_link != '' ) ) 
					{
						$output .= '</a>';
					}
					else if( $links == 'active' )
					{
						$lightbox_attr = Av_Responsive_Images()->html_attr_image_src( $lightbox_img_src, false );
						$output .= "<a {$lightbox_attr} class='av-horizontal-gallery-link' {$display_char} title='{$lightbox_title}' alt='{$alt}'>";		
						$output .= '</a>';
					}
								
					$output .= '</div>';
				}
					
				$output .=			'</div>';
				$output .=		'</div>';
				$output .= '</div>';

			}
			
			$output = Av_Responsive_Images()->make_content_images_responsive( $output );

			if( ! ShortcodeHelper::is_top_level() ) 
			{
				return $output;
			}
				
				
			$params = array();
			$params['class'] = "main_color av-horizontal-gallery-fullwidth avia-no-border-styling {$av_display_classes} {$meta['el_class']}";
			$params['open_structure'] = false;
			$params['id'] = AviaHelper::save_string( $meta['custom_id_val'] , '-', 'av-horizontal-gallery-' . avia_sc_gallery_horizontal::$hor_gallery );
			$params['custom_markup'] = $meta['custom_markup'];
			
			if( $meta['index'] == 0 ) 
			{
				$params['class'] .= ' avia-no-border-styling';
			}

			//we dont need a closing structure if the element is the first one or if a previous fullwidth element was displayed before
			if( $meta['index'] == 0 ) 
			{
				$params['close'] = false;
			}
			
			if( ! empty( $meta['siblings']['prev']['tag'] ) && in_array( $meta['siblings']['prev']['tag'], AviaBuilder::$full_el_no_section ) ) 
			{
				$params['close'] = false;
			}

			$html = $output;

			$output  = avia_new_section( $params );
			$output .= $html;
			$output .= '</div><!-- close section -->'; //close section

			//if the next tag is a section dont create a new section from this shortcode
			if( ! empty( $meta['siblings']['next']['tag']) && in_array( $meta['siblings']['next']['tag'], AviaBuilder::$full_el ) )
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
				$output .= avia_new_section( array( 'close' => false, 'id' => 'after_horizontal_gallery' ) );
			}

			return $output;
		}
		
		/**
		 * Create arrows to scroll image slides
		 * 
		 * @since 4.8.3			reroute to aviaFrontTemplates
		 * @param array $atts
		 * @return string
		 */
		protected function slide_navigation_arrows( array $atts )
		{
			$args = array(
						'class_prev'	=> 'av-horizontal-gallery-prev',
						'class_next'	=> 'av-horizontal-gallery-next',
						'context'		=> get_class(),
						'params'		=> $atts
					);
			
			return aviaFrontTemplates::slide_navigation_arrows( $args );
		}


	}
}

