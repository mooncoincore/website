<?php
/**
 * Fullwidth Button
 * 
 * Displays a a colored button that stretches across the full width and links to any url of your choice
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_button_full' ) ) 
{
	class avia_sc_button_full extends aviaShortcodeTemplate
	{
		use \aviaBuilder\traits\scNamedColors;
		use \aviaBuilder\traits\scButtonStyles;
		
		/**
		 * @var int
		 */
		static $button_count = 0;
		
		/**
		 * @since 4.8.4
		 * @param AviaBuilder $builder
		 */
		public function __construct( $builder ) 
		{
			parent::__construct( $builder );
			
			$this->_construct_scNamedColors();
			$this->_construct_scButtonStyles();
		}
		
		/**
		 * @since 4.8.4
		 */
		public function __destruct() 
		{
			$this->_destruct_scNamedColors();
			$this->_destruct_scButtonStyles();
			
			parent::__destruct();
		}

		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['is_fullwidth']	= 'yes';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Fullwidth Button', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-button.png';
			$this->config['order']			= 84;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_button_big';
			$this->config['tooltip']		= __( 'Creates a colored button that stretches across the full width', 'avia_framework' );
			$this->config['tinyMCE']		= array( 'tiny_always' => true );
			$this->config['preview']		= true;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}


		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-button', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/buttons/buttons.css', array( 'avia-layout' ), false );
			wp_enqueue_style( 'avia-module-button-fullwidth', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/buttons_fullwidth/buttons_fullwidth.css', array( 'avia-layout' ), false );
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
													$this->popup_key( 'content_button' ),
													$this->popup_key( 'content_link' )
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
													$this->popup_key( 'styling_appearance' ),
													$this->popup_key( 'styling_colors' ),
													'border_toggle',
													'box_shadow_toggle'
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
								'template_id'	=> 'effects_toggle',
								'lockable'		=> true,
								'include'		=> array( 'sonar_effect', 'hover_opacity' )
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
							'name'		=> __( 'Button Title', 'avia_framework' ),
							'desc'		=> __( 'This is the text that appears on your button.', 'avia_framework' ),
							'id'		=> 'label',
							'type'		=> 'input',
							'std'		=> __( 'Click me', 'avia_framework' ),
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),
				
						array(	
							'name'		=> __( 'Additional Description Position', 'avia_framework' ),
							'desc'		=> __( 'Select, where to show an additional description', 'avia_framework' ),
							'id'		=> 'description_pos',
							'type'		=> 'select',
							'std'		=> '',
							'lockable'	=> true,
							'subtype'	=> array(	
												__( 'No description', 'avia_framework' )			=> '',
												__( 'Description above title', 'avia_framework' )	=> 'above',
												__( 'Description below title', 'avia_framework' )	=> 'below',
											),
						),
				
						array(
							'name' 	=> __( 'Additional Description', 'avia_framework' ),
							'desc' 	=> __( 'Enter an additional description', 'avia_framework' ),
							'id' 	=> 'content',
							'type' 	=> 'textarea',
							'std' 	=> '',
							'lockable'	=> true,
							'tmpl_set_default'	=> false,
							'required'	=> array( 'description_pos', 'not', '' )
						),
				
						array(	
							'name' 	=> __( 'Button Icon', 'avia_framework' ),
							'desc' 	=> __( 'Should an icon be displayed at the left side of the button', 'avia_framework' ),
							'id' 	=> 'icon_select',
							'type' 	=> 'select',
							'std' 	=> 'yes-left-icon',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No Icon', 'avia_framework' )										=> 'no',
												__( 'Yes, display Icon to the left of the title', 'avia_framework' )	=> 'yes-left-icon' ,	
												__( 'Yes, display Icon to the right of the title', 'avia_framework' )	=> 'yes-right-icon',
											)
						),
				
						array(	
							'name' 	=> __( 'Button Icon', 'avia_framework' ),
							'desc' 	=> __( 'Select an icon for your Button below', 'avia_framework' ),
							'id' 	=> 'icon',
							'type' 	=> 'iconfont',
							'std' 	=> '',
							'lockable'	=> true,
							'locked'	=> array( 'icon', 'font' ),
							'required'	=> array( 'icon_select', 'not_empty_and', 'no' )
							),
				
						array(	
							'name' 	=> __( 'Icon Visibility', 'avia_framework' ),
							'desc' 	=> __( 'Check to only display icon on hover', 'avia_framework' ),
							'id' 	=> 'icon_hover',
							'type' 	=> 'checkbox',
							'std' 	=> '',
							'lockable'	=> true,
							'required'	=> array( 'icon_select', 'not_empty_and', 'no' )
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
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'linkpicker_toggle',
							'name'			=> __( 'Button Link?', 'avia_framework' ),
							'desc'			=> __( 'Where should your button link to?', 'avia_framework' ),
							'subtypes'		=> array( 'manually', 'single', 'taxonomy' ),
							'target_id'		=> 'link_target',
							'lockable'		=> true
						),
						
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_link' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name'		=> __( 'Button Title Attribute', 'avia_framework' ),
							'desc'		=> __( 'Add a title attribute for this button.', 'avia_framework' ),
							'id'		=> 'title_attr',
							'type'		=> 'input',
							'std'		=> '',
							'lockable'	=> true
						)					
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Appearance', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_appearance' ), $template );
			
			$c = array(
				
						array(	
							'type'			=> 'template',
							'template_id'	=> 'button_colors',
							'lockable'		=> true,
							'ids'			=> array(
												'bg'		=> array(
																'color'		=> 'color',
																'custom'	=> 'custom',
																'custom_id'	=> 'custom_bg',
																'gradient'	=> 'btn_custom_grad'
															),
												'bg_hover'	=> array(
																'color'		=> 'color_hover',
																'custom'	=> 'custom',
																'custom_id'	=> 'custom_bg_hover',
															),
												'font'		=> array(
																'color'		=> 'color_font',
																'custom'	=> 'custom',
																'custom_id'	=> 'custom_font',
															),
												'font_hover' => array(
																'color'		=> 'color_font_hover',
																'custom'	=> 'custom',
																'custom_id'	=> 'custom_font_hover',
															),
												)
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_colors' ), $template );
			
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
			Avia_Element_Templates()->set_locked_attributes( $attr, $this, $this->config['shortcode'], $default, $locked );
			
			extract( av_backend_icon( array( 'args' => $attr ) ) ); // creates $font and $display_char if the icon was passed as param 'icon' and the font as 'font' 

			$inner  = "<div class='avia_button_box avia_hidden_bg_box avia_textblock avia_textblock_style' data-update_element_template='yes'>";
			$inner .=		'<div ' . $this->class_by_arguments_lockable( 'icon_select, color', $attr, $locked ) . '>';
			$inner .=			'<span ' . $this->class_by_arguments_lockable( 'font', $font, $locked ) . '>';
			$inner .=				'<span ' . $this->update_option_lockable( array( 'icon', 'icon_fakeArg' ), $locked ) . " class='avia_button_icon avia_button_icon_left'>{$display_char}</span> ";
			$inner .=			'</span> ';
			$inner .=			'<span ' . $this->update_option_lockable( 'label', $locked ) . " class='avia_iconbox_title' >{$attr['label']}</span> ";
			$inner .=			'<span ' . $this->class_by_arguments_lockable( 'font', $font, $locked ) . '>';
			$inner .=				'<span ' . $this->update_option_lockable( array( 'icon', 'icon_fakeArg' ), $locked ) . " class='avia_button_icon avia_button_icon_right'>{$display_char}</span>";
			$inner .=			'</span>';
			$inner .=		'</div>';
			$inner .= '</div>';

			$params['innerHtml'] = $inner;
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

			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $atts['linktarget'] ) )
			{
				$atts['link_target'] = $atts['linktarget'];
			}
			
			$default = array(
							'label'				=> 'Click me', 
							'link'				=> '', 
							'link_target'		=> '',
							'position'			=> 'center',
							'icon_select'		=> 'no',
							'icon'				=> '', 
							'font'				=> '',
							'icon_hover'		=> '',
							'title_attr'		=> '',
							'description_pos'	=> '',
							'color'				=> 'theme-color',
							'color_hover'		=> 'theme-color-highlight',
							'custom_bg'			=> '#444444',
							'custom_bg_hover'	=> '#444444',
							'color_font'		=> 'custom',
							'custom_font'		=> '#ffffff',
							'color_font_hover'	=> '#ffffff',
							'custom_font_hover'	=> '#ffffff'
						);
			
			$default = $this->sync_sc_defaults_array( $default, 'no_modal_item', 'no_content' );
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );

			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );

			//	modify atts so we can use default styling rules
			$mod_atts = $atts;
			
			$mod_atts['color_options'] = 'color_options_advanced';
			
			$mod_atts['btn_color_bg'] = $atts['color'];
			$mod_atts['btn_custom_bg'] = $atts['custom_bg'];
			$mod_atts['btn_color_bg_hover'] = $atts['color_hover'];
			$mod_atts['btn_custom_bg_hover'] = $atts['custom_bg_hover'];
			$mod_atts['btn_color_font'] = $atts['color_font'];
			$mod_atts['btn_custom_font'] = $atts['custom_font'];
			$mod_atts['btn_color_font_hover'] = $atts['color_font_hover'];
			$mod_atts['btn_custom_font_hover'] = $atts['custom_font_hover'];
			
			$this->set_button_styes( $element_styling, $mod_atts );
			
			
			$classes = array(
						'avia-button',
						'avia-button-fullwidth',
						$element_id
					);
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes( 'container', $this->class_by_arguments( 'icon_select, color', $atts, true, 'array' ) );
			
			$selectors = array(
						'container'					=> "#top #wrap_all .avia-button.{$element_id}",
						'container-hover'			=> "#top #wrap_all .avia-button.{$element_id}:hover",
						'container-hover-overlay'	=> "#top #wrap_all .avia-button.{$element_id}:hover .avia_button_background",
						'container-after'			=> ".avia-button.{$element_id}.avia-sonar-shadow:after",
						'container-after-hover'		=> ".avia-button.{$element_id}.avia-sonar-shadow:hover:after"
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
			
			avia_sc_button_full::$button_count++;
			$background_hover = '';
			
			$display_char = av_icon( $atts['icon'], $atts['font'] );
			
			if( 'custom' != $atts['color_hover'] && 'btn_custom_grad' != $atts['color'] )
			{
				if( $this->is_special_button_color( $atts['color_hover'] ) )
				{
					$background_hover = "<span class='avia_button_background avia-button avia-button-fullwidth avia-color-{$atts['color_hover']}'></span>";
				}
			}
			
			$blank = AviaHelper::get_link_target( $atts['link_target'] );
			$link = AviaHelper::get_url( $atts['link'] );
			$link = $link == 'http://' ? '' : $link;

			$title_attr = ! empty( $atts['title_attr'] ) ? 'title="' . esc_attr( $atts['title_attr'] ) . '"' : '';
			
			
			$style_tag = $element_styling->get_style_tag( $element_id );
			$container_class = $element_styling->get_class_string( 'container' );
			
			$content_html = '';

			if( $content && $atts['description_pos'] == 'above' ) 
			{
				$content_html .= "<div class='av-button-description av-button-description-above'>" . ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $content ) ) . '</div>';
			}

			if( 'yes-left-icon' == $atts['icon_select'] ) 
			{
				$content_html .= "<span class='avia_button_icon avia_button_icon_left' {$display_char}></span>";
			}
				
			$content_html .= "<span class='avia_iconbox_title' >{$atts['label']}</span>";

			if( 'yes-right-icon' == $atts['icon_select'] ) 
			{
				$content_html .= "<span class='avia_button_icon avia_button_icon_right' {$display_char}></span>";
			}

			if( $content && $atts['description_pos'] == 'below' ) 
			{
				$content_html .= "<div class='av-button-description av-button-description-below'>" . ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $content ) ) . '</div>';
			}

			$html  = '';
			$html .= $style_tag;
			
			$html .=	"<a href='{$link}' class='{$container_class} {$av_display_classes}' {$blank}>";
			$html .=		$content_html;
			$html .=		$background_hover;
			$html .=	'</a>';

			$output =  "<div {$meta['custom_el_id']} class='avia-button-wrap avia-button-{$atts['position']} {$meta['el_class']}' {$title_attr}>{$html}</div>";


			$params['class'] = 'main_color av-fullscreen-button avia-no-border-styling ' . $meta['el_class'];
			$params['open_structure'] = false;

			$id = AviaHelper::save_string( $atts['label'], '-' );
			$params['id'] = AviaHelper::save_string( $id, '-', 'av-fullwidth-button-' . avia_sc_button_full::$button_count );
			$params['custom_markup'] = $meta['custom_markup'];

			//we dont need a closing structure if the element is the first one or if a previous fullwidth element was displayed before
			if( $meta['index'] == 0 ) 
			{
				$params['close'] = false;
			}
			
			if( ! empty( $meta['siblings']['prev']['tag'] ) && in_array( $meta['siblings']['prev']['tag'], AviaBuilder::$full_el_no_section ) ) 
			{
				$params['close'] = false;
			}

			if( ! ShortcodeHelper::is_top_level() ) 
			{
				return $output;
			}
			
			global $avia_config;
			if( isset( $avia_config['portfolio_preview_template'] ) && $avia_config['portfolio_preview_template'] > 0 )
			{
				return $output;
			}

			$html  = avia_new_section( $params );
			$html .= $output;
			$html .= avia_section_after_element_content( $meta , 'after_fullwidth_button' );

			return $html;
		}
			
	}
}
