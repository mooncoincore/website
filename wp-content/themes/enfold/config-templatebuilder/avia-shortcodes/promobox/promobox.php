<?php
/**
 * Promo Box
 * 
 * Creates a notification box with call to action button
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_promobox' ) )
{
	class avia_sc_promobox extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Promo Box', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL']. 'sc-promobox.png';
			$this->config['order']			= 50;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_promobox';
			$this->config['tooltip'] 	    = __( 'Creates a notification box with call to action button', 'avia_framework' );
			$this->config['preview'] 		= 'xlarge';
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}

		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-promobox', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/promobox/promobox.css', array( 'avia-layout' ), false );
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
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array( 
													$this->popup_key( 'content_promo' ),
													$this->popup_key( 'content_button' )
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
													$this->popup_key( 'styling_button' ),
													$this->popup_key( 'styling_colors' )
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
								'template_id'	=> 'effects_toggle',
								'lockable'		=> true,
								'include'		=> array( 'sonar_effect', 'hover_opacity' ),
								'ids'			=> array(
														'sonar_effect'	=> 'sonar_promo'
													),
								'names'			=> array(
														'hover_opacity'	=> __( 'Button Opacity On Hover', 'avia_framework' )
													),
								'container_class'	=> array(
														'sonar_effect'	=> array( '', 'av_half av_half_first', 'av_half', 'av_half av_half_first', 'av_half' )
													)
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
							'name' 	=> __( 'Content', 'avia_framework' ),
							'desc' 	=> __( 'Enter some content for Promo Box', 'avia_framework' ),
							'id' 	=> 'content',
							'type' 	=> 'tiny_mce',
							'std' 	=> __( 'Welcome Stranger! This is an example Text for your fantastic Promo Box! Feel Free to delete it and replace it with your own fancy Message!', 'avia_framework' ),
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),

				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Text', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_promo' ), $template );
			
			$c = array(
						array(
							'name' 	=> __( 'Promo Box Button', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to display a Call to Action Button on the right side of the box?', 'avia_framework' ),
							'id' 	=> 'button',
							'type' 	=> 'select',
							'std' 	=> 'yes',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Yes', 'avia_framework' )	=> 'yes',
												__( 'No', 'avia_framework' )	=> 'no',
											)
						),

						array(	
							'name' 	=> __( 'Button Label', 'avia_framework' ),
							'desc' 	=> __( 'This is the text that appears on your button.', 'avia_framework' ),
							'id' 	=> 'label',
							'type' 	=> 'input',
							'std'		=> __( 'Click me', 'avia_framework' ),
							'lockable'	=> true,
							'tmpl_set_default'	=> false,
							'required'	=> array( 'button', 'equals','yes' )
						),
				
						array(
							'name' 	=> __( 'Button Icon', 'avia_framework' ),
							'desc' 	=> __( 'Should an icon be displayed at the left side of the button', 'avia_framework' ),
							'id' 	=> 'icon_select',
							'type' 	=> 'select',
							'std' 	=> 'no',
							'lockable'	=> true,
							'required'	=> array( 'button', 'equals', 'yes' ),
							'subtype'	=> array(
												__( 'No Icon', 'avia_framework' )			=> 'no',
												__( 'Yes, display Icon', 'avia_framework' )	=> 'yes'
											)
						),
				
						array(
							'name' 	=> __( 'Button Icon','avia_framework' ),
							'desc' 	=> __( 'Select an icon for your Button below','avia_framework' ),
							'id' 	=> 'icon',
							'type' 	=> 'iconfont',
							'std' 	=> '',
							'lockable'	=> true,
							'locked'	=> array( 'icon', 'font' ),
							'required'	=> array( 'icon_select', 'equals', 'yes' )
						),
				
						array(	
							'name' 	=> __( 'Button Label display', 'avia_framework' ),
							'desc' 	=> __( 'Select how to display the label', 'avia_framework' ),
							'id' 	=> 'label_display',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'required'	=> array( 'button', 'equals', 'yes' ),
							'subtype'	=> array(
												__( 'Always display', 'avia_framework' )	=> '' ,	
												__( 'Display on hover', 'avia_framework' )	=> 'av-button-label-on-hover',
											)
						)

				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Button', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_button' ), $template );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						
						array(	
							'type'			=> 'template',
							'template_id'	=> 'named_colors',
							'name'			=> __( 'Button Color', 'avia_framework' ),
							'desc'			=> __( 'Choose a color for your button bar here', 'avia_framework' ),
							'id'			=> 'color',
							'std'			=> 'theme-color',
							'lockable'		=> true,
							'required'		=> array( 'button', 'equals', 'yes' ),
							'custom'		=> true,
							'translucent'	=> array()
						),
				
						array(
							'name'		=> __( 'Custom Background Color', 'avia_framework' ),
							'desc'		=> __( 'Select a custom background color for your Button here', 'avia_framework' ),
							'id'		=> 'custom_bg',
							'type'		=> 'colorpicker',
							'std'		=> '#444444',
							'lockable'	=> true,
							'required'	=> array( 'color', 'equals', 'custom' )
						),

						array(
							'name'		=> __( 'Custom Font Color', 'avia_framework' ),
							'desc'		=> __( 'Select a custom font color for your Button here', 'avia_framework' ),
							'id'		=> 'custom_font',
							'type'		=> 'colorpicker',
							'rgba'		=> true,
							'std'		=> '#ffffff',
							'lockable'	=> true,
							'required'	=> array( 'color', 'equals', 'custom' )
						),

						array(
							'name'		=> __( 'Button Size', 'avia_framework' ),
							'desc'		=> __( 'Choose the size of your button here', 'avia_framework' ),
							'id'		=> 'size',
							'type'		=> 'select',
							'std'		=> 'large',
							'lockable'	=> true,
							'required'	=> array( 'button', 'equals', 'yes' ),
							'subtype'	=> array(
												__( 'Small', 'avia_framework' )		=> 'small',
												__( 'Medium', 'avia_framework' )	=> 'medium',
												__( 'Large', 'avia_framework' )		=> 'large',
							)
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Button', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_button' ), $template );
			
			
			
			$c = array(
						array(
							'name'		=> __( 'Colors', 'avia_framework' ),
							'desc'		=> __( 'Select to use the themes default colors or apply custom ones', 'avia_framework' ),
							'id'		=> 'box_color',
							'type'		=> 'select',
							'std'		=> '',
							'lockable'	=> true,
							'subtype'	=> array( 
												__( 'Default', 'avia_framework' )						=> '',
												__( 'Few settings only', 'avia_framework' )				=> 'custom',
												__( 'Extended, simple background', 'avia_framework' )	=> 'ext_simple',
												__( 'Extended, gradient background', 'avia_framework' )	=> 'ext_grad',
										),
						),
					
						array(	
							'name'		=> __( 'Custom Font Color', 'avia_framework' ),
							'desc'		=> __( 'Select a custom font color here', 'avia_framework' ),
							'id'		=> 'box_custom_font',
							'type'		=> 'colorpicker',
							'rgba'		=> true,
							'std'		=> '#ffffff',
							'lockable'	=> true,
							'required'	=> array( 'box_color', 'not', '' )
						),	
					
						array(	
							'name'		=> __( 'Custom Background Color', 'avia_framework' ),
							'desc'		=> __( 'Select a custom background color here', 'avia_framework' ),
							'id'		=> 'box_custom_bg',
							'type'		=> 'colorpicker',
							'rgba'		=> true,
							'std'		=> '#444444',
							'lockable'	=> true,
							'required'	=> array( 'box_color', 'parent_in_array', 'custom,ext_simple' )
						),	
						
						array(	
							'name'		=> __( 'Custom Border Color', 'avia_framework' ),
							'desc'		=> __( 'Select a custom border color here', 'avia_framework' ),
							'id'		=> 'box_custom_border',
							'type'		=> 'colorpicker',
							'rgba'		=> true,
							'std'		=> '#333333',
							'lockable'	=> true,
							'required'	=> array( 'box_color', 'equals', 'custom' )
						),
				
						array(
							'type'			=> 'template',
							'template_id'	=> 'gradient_colors',
							'lockable'		=> true,
							'container_class'	=> array( 'av_half av_half_first', 'av_half', 'av_half av_half_first', 'av_half' ),
							'required'		=> array( 'box_color', 'equals', 'ext_grad' )
						),
				
						array(
							'type'			=> 'template',
							'template_id'	=> 'border',
							'id'			=> 'border_promo',
							'lockable'		=> true,
							'default_check'	=> true,
							'required'		=> array( 'box_color', 'parent_in_array', 'ext_simple,ext_grad' )
						),
				
						array(
							'type'			=> 'template',
							'template_id'	=> 'border_radius',
							'id'			=> 'border_radius_promo',
							'lockable'		=> true,
							'required'		=> array( 'box_color', 'parent_in_array', 'ext_simple,ext_grad' )
						),
				
						array(
							'type'			=> 'template',
							'template_id'	=> 'box_shadow',
							'id'			=> 'box_shadow_promo',
							'lockable'		=> true,
							'default_check'	=> true,
							'required'		=> array( 'box_color', 'parent_in_array', 'ext_simple,ext_grad' )
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Promo Box', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_colors' ), $template );
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'linkpicker_toggle',
							'name'			=> __( 'Button Link?', 'avia_framework' ),
							'desc'			=> __( 'Where should your button link to?', 'avia_framework' ),
							'lockable'		=> true,
							'subtypes'		=> array( 'manually', 'single', 'taxonomy' ),
							'required'		=> array( 'button', 'equals', 'yes' ),
							'target_id'		=> 'link_target'
						),
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_link' ), $c );
			
		}

		/**
		 * Editor Element - this function defines the visual appearance of an element on the AviaBuilder Canvas
		 * Most common usage is to define some markup in the $params['innerHtml'] which is then inserted into the drag and drop container
		 * Less often used: $params['data'] to add data attributes, $params['class'] to modify the className
		 *
		 * @param array $params this array holds the default values for $content and $args.
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_element( $params )
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
			$content = $params['content'];
			Avia_Element_Templates()->set_locked_attributes( $attr, $this, $this->config['shortcode'], $default, $locked, $content );
			
			extract( av_backend_icon( array( 'args' => $attr ) ) ); // creates $font and $display_char if the icon was passed as param 'icon' and the font as 'font'

			$params['class'] = '';
			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_textblock avia_textblock_style' data-update_element_template='yes'>";
			$params['innerHtml'] .=		'<div ' . $this->class_by_arguments_lockable( 'button', $attr, $locked ) . '>';
			$params['innerHtml'] .=			'<div ' . $this->update_option_lockable( 'content', $locked ) . " class='avia-promocontent'>" . stripslashes( wpautop( trim( $content ) ) ) . '</div>';
			$params['innerHtml'] .=			"<div class='avia_button_box avia_hidden_bg_box'>";
			$params['innerHtml'] .=				'<div ' . $this->class_by_arguments_lockable( 'icon_select, color, size', $attr, $locked ) . '>';
			$params['innerHtml'] .=					'<span ' . $this->class_by_arguments_lockable( 'font', $font, $locked ) . '>';
			$params['innerHtml'] .=						'<span ' . $this->update_option_lockable( array( 'icon', 'icon_fakeArg' ), $locked ) . " class='avia_button_icon'>{$display_char}</span>";
			$params['innerHtml'] .=					'</span>';
			$params['innerHtml'] .=					'<span ' . $this->update_option_lockable( 'label', $locked ) . " class='avia_iconbox_title' >{$attr['label']}</span>";
			$params['innerHtml'] .= '			</div>';
			$params['innerHtml'] .= '		</div>';
			$params['innerHtml'] .= '	</div>';
			$params['innerHtml'] .= '</div>';

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
			
			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $atts['linktarget'] ) )
			{
				$atts['link_target'] = $atts['linktarget'];
			}
			
			$default = array(
						'button'		=> 'yes',
						'label'			=> 'Click me',
						'link'			=> '',
						'link_target'	=> '',
						'color'			=> 'theme-color',
						'custom_bg'		=> '#444444',
						'custom_font'	=> '#ffffff',
						'size'			=> 'small',
						'position'		=> 'center',
						'icon_select'	=> 'yes',
						'icon'			=> '',
						'font'			=> '',
						'box_color'		=> '',
						'box_custom_bg'	=> '',
						'box_custom_font'	=>'',
						'box_custom_border'	=>'',
						'label_display'	=> ''
					);
			
			$default = $this->sync_sc_defaults_array( $default, 'no_modal_item', 'no_content' );
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			$atts['position'] = 'right';
			
			$element_styling->create_callback_styles( $atts );
			
			$classes = array(
						'av_promobox',
						$element_id
					);
			
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes( 'container', $this->class_by_arguments( 'button', $atts, true, 'array' ) );
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			if( $atts['box_color']  == 'custom' )
			{
				if( ! empty( $atts['box_custom_font'] ) )   
				{
					$element_styling->add_styles( 'container', array( 'color' => $atts['box_custom_font'] ) );
				}
				
				if( ! empty( $atts['box_custom_bg'] ) )   
				{
					$element_styling->add_styles( 'container', array( 'background' => $atts['box_custom_bg'] ) );
				}
				
				if( ! empty( $atts['box_custom_border'] ) )   
				{
					$element_styling->add_styles( 'container', array( 'border-color' => $atts['box_custom_border'] ) );
				}
			}
			else if( in_array( $atts['box_color'], array( 'ext_simple', 'ext_grad' ) ) )
			{
				if( ! empty( $atts['box_custom_font'] ) )   
				{
					$element_styling->add_styles( 'container', array( 'color' => $atts['box_custom_font'] ) );
				}
				
				if( $atts['box_color'] == 'ext_simple' &&  ! empty( $atts['box_custom_bg'] ) )
				{
					$element_styling->add_styles( 'container', array( 'background-color' => $atts['box_custom_bg'] ) );
				}
				else if( $atts['box_color'] == 'ext_grad' )
				{
					$element_styling->add_callback_styles( 'container', array( 'gradient_color' ) );
				}
				
				$element_styling->add_callback_styles( 'container', array( 'border_promo', 'border_radius_promo', 'box_shadow_promo' ) );
			}
			
			if( ! empty( $atts['sonar_promo_effect'] ) )
			{
				$element_styling->add_classes( 'container', 'avia-sonar-shadow' );
				$element_styling->add_callback_styles( 'container-after', array( 'border_radius_promo' ) );
				
				if( false !== strpos( $atts['sonar_promo_effect'], 'shadow' ) )
				{
					if( 'shadow_permanent' == $atts['sonar_promo_effect'] )
					{
						$element_styling->add_callback_styles( 'container-after', array( 'sonar_promo' ) );
					}
					else
					{
						$element_styling->add_callback_styles( 'container-after-hover', array( 'sonar_promo' ) );
					}
				}
				else
				{
					if( false !== strpos( $atts['sonar_promo_effect'], 'permanent' ) )
					{
						$element_styling->add_callback_styles( 'container', array( 'sonar_promo' ) );
					}
					else
					{
						$element_styling->add_callback_styles( 'container-hover', array( 'sonar_promo' ) );
					}
				}
			}
			
			$selectors = array(
						'container'				=> ".av_promobox.{$element_id}",
						'container-after'		=> ".av_promobox.{$element_id}:after",
						'container-hover'		=> ".av_promobox.{$element_id}:hover",
						'container-after-hover'	=> ".av_promobox.{$element_id}:hover:after"
					);
			
			$element_styling->add_selectors( $selectors );
			
			
			$result['default'] = $default;
			$result['atts'] = $atts;
			$result['content'] = $content;
			
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

			$button = '';
			
			if( $atts['button'] == 'yes' )
			{
				global $shortcode_tags;

				$fake = true;
				$button .= call_user_func( $shortcode_tags['av_button'], $atts, null, 'av_button', $fake );
			}
			
			$style_tag = $element_styling->get_style_tag( $element_id );
			$container_class = $element_styling->get_class_string( 'container' );

			$output  = '';
			$output .= $style_tag;
			$output .= "<div {$meta['custom_el_id']} class='{$container_class} {$av_display_classes}'>";
			$output .=		"<div class='avia-promocontent'>" . stripslashes( wpautop( trim( $content ) ) ) . '</div>';
			$output .=		$button;
			$output.= '</div>';

			return do_shortcode( $output );
		}

	}
}
