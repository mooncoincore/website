<?php
/**
 * Button Row
 *
 * Displays a set of buttons with links
 * Each button can be styled individually
 * 
 *
 * @author tinabillinger
 * @since 4.3
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if ( ! class_exists( 'avia_sc_buttonrow' ) ) 
{
    class avia_sc_buttonrow extends aviaShortcodeTemplate
    {
		use \aviaBuilder\traits\scNamedColors;
		use \aviaBuilder\traits\scButtonStyles;
		
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $screen_options;
		
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $alignment;
		
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $spacing;
		
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $spacing_unit;
		
		/**
		 * 
		 * @since 4.5.5
		 * @param AviaBuilder $builder
		 */
		public function __construct( $builder ) 
		{
			parent::__construct( $builder );
			
			$this->screen_options = array();
			$this->alignment = '';
			$this->spacing = '';
			$this->spacing_unit = '';
			$this->_construct_scNamedColors();
			$this->_construct_scButtonStyles();
		}
		
		/**
		 * @since 4.5.5
		 */
		public function __destruct() 
		{
			$this->_destruct_scNamedColors();
			$this->_destruct_scButtonStyles();
			unset( $this->screen_options );
			
			parent::__destruct();
		}
		
		/**
		 * Create the config array for the shortcode button
		 */
		public function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Button Row', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-buttonrow.png';
			$this->config['order']			= 84;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_buttonrow';
			$this->config['shortcode_nested'] = array( 'av_buttonrow_item' );
			$this->config['tooltip']		= __( 'Displays multiple buttons beside each other', 'avia_framework' );
			$this->config['preview']		= true;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
			$this->config['name_item']		= __( 'Button Row Item', 'avia_framework' );
			$this->config['tooltip_item']	= __( 'A Button Row Button Element', 'avia_framework' );
		}

        public function extra_assets()
        {
			//load css
			wp_enqueue_style( 'avia-module-button', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/buttons/buttons.css', array( 'avia-layout' ), false );
			wp_enqueue_style( 'avia-module-buttonrow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/buttonrow/buttonrow.css', array( 'avia-layout' ), false );
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
								'template_id'	=> $this->popup_key( 'content_buttonrow' )
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
								'template_id'	=> $this->popup_key( 'styling_appearance' )
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
							'name'			=> __( 'Add/Edit Buttons', 'avia_framework' ),
							'desc'			=> __( 'Here you can add, remove and edit buttons.', 'avia_framework' ),
							'type'			=> 'modal_group',
							'id'			=> 'content',
							'modal_title'	=> __( 'Edit Button', 'avia_framework' ),
							'editable_item'	=> true,
							'lockable'		=> true,
							'tmpl_set_default'	=> false,
							'std'			=> array(
													array( 'label' => __( 'Click me', 'avia_framework' ), 'icon' => '4' ),
													array( 'label' => __( 'Call to Action', 'avia_framework' ), 'icon' => '5' ),
													array( 'label' => __( 'Click me', 'avia_framework' ), 'icon' => '6' ),
												),
							'subelements'	=> $this->create_modal()
						),
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_buttonrow' ), $c );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Align Buttons', 'avia_framework' ),
							'desc' 	=> __( 'Choose the alignment of your buttons here', 'avia_framework' ),
							'id' 	=> 'alignment',
							'type' 	=> 'select',
							'std' 	=> 'center',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Align Left', 'avia_framework' )	=> 'left',
												__( 'Align Center', 'avia_framework' )	=> 'center',
												__( 'Align Right', 'avia_framework' )	=> 'right',
											)
						),
				
						array(
							'name'	=> __( 'Space between buttons', 'avia_framework' ),
							'desc'	=> __( 'Define the space between the buttons. Leave blank for default space. Make sure you enter a valid positive number.', 'avia_framework' ),
							'id'	=> 'button_spacing',
							'container_class' => 'av_half',
							'type'	=> 'input',
							'std'	=> '5',
							'lockable'	=> true
						),

						array(
							'name'	=> __( 'Unit', 'avia_framework' ),
							'desc'	=> __( 'Unit for the spacing', 'avia_framework' ),
							'id'	=> 'button_spacing_unit',
							'container_class' => 'av_half',
							'type'	=> 'select',
							'std'	=> 'px',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'px', 'avia_framework' )	=> 'px',
												__( '%', 'avia_framework' )		=> '%',
												__( 'em', 'avia_framework' )	=> 'em',
												__( 'rem', 'avia_framework' )	=> 'rem',
											)
						)
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_appearance' ), $c );
			
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
													$this->popup_key( 'modal_content_button' ),
													$this->popup_key( 'modal_content_link' )
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
													$this->popup_key( 'modal_styling_appearance' ),
													$this->popup_key( 'modal_styling_colors' ),
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
							'name'		=> __( 'Button Label', 'avia_framework' ),
							'desc'		=> __( 'This is the text that appears on your button.', 'avia_framework' ),
							'id'		=> 'label',
							'type'		=> 'input',
							'std'		=> __( 'Click me', 'avia_framework' ),
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),
				
						array(	
							'name' 	=> __( 'Button Icon', 'avia_framework' ),
							'desc' 	=> __( 'Should an icon be displayed at the left side of the button', 'avia_framework' ),
							'id' 	=> 'icon_select',
							'type' 	=> 'select',
							'std' 	=> 'yes',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No Icon', 'avia_framework' )							=> 'no',
												__( 'Yes, display Icon to the left', 'avia_framework' )		=> 'yes' ,	
												__( 'Yes, display Icon to the right', 'avia_framework' )	=> 'yes-right-icon',
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_button' ), $template );
			
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_link' ), $c );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Button Size', 'avia_framework' ),
							'desc' 	=> __( 'Choose the size of your button here', 'avia_framework' ),
							'id' 	=> 'size',
							'type' 	=> 'select',
							'std' 	=> 'small',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Small', 'avia_framework' )		=> 'small',
												__( 'Medium', 'avia_framework' )	=> 'medium',
												__( 'Large', 'avia_framework' )		=> 'large',
												__( 'X Large', 'avia_framework' )	=> 'x-large',
											)
						),
							
						array(	
							'name' 	=> __( 'Button Label display', 'avia_framework' ),
							'desc' 	=> __( 'Select how to display the label', 'avia_framework' ),
							'id' 	=> 'label_display',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Always display', 'avia_framework' )	=> '',	
												__( 'Display on hover', 'avia_framework' )	=> 'av-button-label-on-hover',
											)
						),
					
						array(	
							'name'		=> __( 'Button Title Attribute', 'avia_framework' ),
							'desc'		=> __( 'Add a title attribute for this button.', 'avia_framework' ),
							'id'		=> 'title_attr',
							'type'		=> 'input',
							'std'		=> '',
							'lockable'	=> true,
							'required'	=> array( 'label_display', 'equals', '' )
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_appearance' ), $template );
			
			$c = array(
				
						array(	
							'name'		=> __( 'Button Colors Selection', 'avia_framework' ),
							'desc'		=> __( 'Select the available options for button colors. Switching to advanced options for already existing buttons you need to set all options (color settings from basic options are ignored).', 'avia_framework' ),
							'id'		=> 'color_options',
							'type'		=> 'select',
							'std'		=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Basic options only', 'avia_framework' )	=> '',	
												__( 'Advanced options', 'avia_framework' )		=> 'color_options_advanced',
											)
						),
				
						array(	
							'type'			=> 'template',
							'template_id'	=> 'named_colors',
							'custom'		=> true,
							'lockable'		=> true,
							'required'		=> array( 'color_options', 'equals', '' )
						),
				
						array(	
							'name'		=> __( 'Custom Background Color', 'avia_framework' ),
							'desc'		=> __( 'Select a custom background color for your button here', 'avia_framework' ),
							'id'		=> 'custom_bg',
							'type'		=> 'colorpicker',
							'std'		=> '#444444',
							'lockable'	=> true,
							'required'	=> array( 'color', 'equals', 'custom' )
						),	
						
						array(	
							'name'		=> __( 'Custom Font Color', 'avia_framework' ),
							'desc'		=> __( 'Select a custom font color for your button here', 'avia_framework' ),
							'id'		=> 'custom_font',
							'type'		=> 'colorpicker',
							'std'		=> '#ffffff',
							'lockable'	=> true,
							'required'	=> array( 'color', 'equals', 'custom' )
						),
				
						array(	
							'type'			=> 'template',
							'template_id'	=> 'button_colors',
							'color_id'		=> 'btn_color',
							'custom_id'		=> 'btn_custom',
							'lockable'		=> true,
							'required'		=> array( 'color_options', 'not', '' )
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

		}
				

		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * @param array $params this array holds the default values for $content and $args.
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		public function editor_sub_element( $params )
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

			$template = $this->update_template_lockable( 'label', __( 'Button', 'avia_framework' ) . ': {{label}}', $locked );

			extract( av_backend_icon( array( 'args' => $attr ) ) ); // creates $font and $display_char if the icon was passed as param 'icon' and the font as 'font'

			$params['innerHtml'] = '';
			$params['innerHtml'] .= "<div class='avia_title_container' data-update_element_template='yes'>";
			$params['innerHtml'] .=		'<span ' . $this->class_by_arguments_lockable( 'font', $font, $locked ) . '>';
			$params['innerHtml'] .=			'<span ' . $this->update_option_lockable( array( 'icon', 'icon_fakeArg' ), $locked ) . " class='avia_tab_icon'>{$display_char}</span>";
			$params['innerHtml'] .=		'</span>';
			$params['innerHtml'] .= "<span {$template} >" . __( 'Button', 'avia_framework' ) . ": {$attr['label']}</span></div>";

			return $params;
		}
		
		/**
		 * 
		 * @since 4.5.5
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
		 * Override base class - we have global attributes here
		 *
		 * @since 4.8.4
		 * @return boolean
		 */
		public function has_global_attributes() 
		{
			return true;
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
						'alignment'				=> 'center',
						'button_spacing'		=> '5',
						'button_spacing_unit'	=> 'px'
					);
			
			$default = $this->sync_sc_defaults_array( $default, 'no_modal_item', 'no_content' );
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			$this->screen_options = AviaHelper::av_mobile_sizes( $atts );
			
			
			$classes = array(
						'avia-buttonrow-wrap',
						$element_id,
						'avia-buttonrow-' . $atts['alignment']
					);
			
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			$selectors = array(
						'container'		=> ".avia-buttonrow-wrap.{$element_id}"
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
		 * 
		 * @since 4.8.4
		 * @param array $args
		 * @return array
		 */
		protected function get_element_styles_item( array $args ) 
		{
			//	get settings from container element and remove to get correct element id (override self::has_global_attributes() to activate)
			$parent_atts = isset( $args['atts']['parent_atts'] ) ? $args['atts']['parent_atts'] : null;
			unset( $args['atts']['parent_atts'] );
			
			$result = parent::get_element_styles_item( $args );
			
			extract( $result );
			
			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $atts['linktarget'] ) )
			{
				$atts['link_target'] = $atts['linktarget'];
			}
			
			$default = $this->get_default_btn_atts();
			$default = $this->sync_sc_defaults_array( $default, 'modal_item' );
			

			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );

			$atts = shortcode_atts( $default, $atts, $this->config['shortcode_nested'][0] );

			if( $atts['icon_select'] == 'yes' ) 
			{
				$atts['icon_select'] = 'yes-left-icon';
			}
			
			$classes = array(
						'avia-button',
						$element_id
					);
			
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes( 'container', $this->class_by_arguments( 'icon_select, size', $atts, true, 'array' ) );
			
			$this->set_button_styes( $element_styling, $atts, true );
			
			if( ! is_array( $parent_atts ) )
			{
				$spacing = $this->spacing;
				$spacing_unit = $this->spacing_unit;
				$alignment = $this->alignment;
			}
			else
			{
				$spacing = isset( $parent_atts['button_spacing'] ) && is_numeric( $parent_atts['button_spacing'] ) ? $parent_atts['button_spacing'] : '';
				$spacing_unit = isset( $parent_atts['button_spacing_unit'] ) && ! empty( $parent_atts['button_spacing_unit'] ) ? $parent_atts['button_spacing_unit'] : 'px';
				$alignment = isset( $parent_atts['alignment'] ) && ! empty( $parent_atts['alignment'] ) ? $parent_atts['alignment'] : 'center';
			}
			
			if( ! empty( $spacing ) )
			{
				$spacing_string = $spacing . $spacing_unit;
				
				$element_styling->add_styles( 'container', array( 'margin-bottom' => $spacing_string ) );
				
				switch( $alignment )
				{
					case 'left':
						$element_styling->add_styles( 'container', array( 'margin-right' => $spacing_string ) );
						break;
					case 'right':
						$element_styling->add_styles( 'container', array( 'margin-left' => $spacing_string ) );
						break;
					case 'center':
					default:
						$spacing_string = round( $spacing / 2 ) . $spacing_unit;
						$element_styling->add_styles( 'container', array( 'margin-right' => $spacing_string ) );
						$element_styling->add_styles( 'container', array( 'margin-left' => $spacing_string ) );
						break;
				}
			}
			
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
		public function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
			$result = $this->get_element_styles( compact( array( 'atts', 'content', 'shortcodename', 'meta' ) ) );
			
			extract( $result );
			
			extract( $this->screen_options );	//return $av_font_classes, $av_title_font_classes and $av_display_classes

			extract( $atts );
			

			$this->alignment = $alignment;
			$this->spacing = is_numeric( $button_spacing ) && $button_spacing > 0 ? $button_spacing : '';
			$this->spacing_unit = $button_spacing_unit;

			
			$style_tag = $element_styling->get_style_tag( $element_id );
			$container_class = $element_styling->get_class_string( 'container' );
				
			
			$output  =	'';
			$output .=	$style_tag;
			$output .=	"<div {$meta['custom_el_id']} class='{$container_class} {$av_display_classes}'>";
			$output .=		ShortcodeHelper::avia_remove_autop( $content, true );
			$output .=	'</div>';

			$this->screen_options = array();
			
			return $output;
        }

		/**
		 * Shortcode handler
		 * 
		 * @param array $atts
		 * @param string $content
		 * @param string $shortcodename
		 * @return string
		 */
		public function av_buttonrow_item( $atts, $content = '', $shortcodename = '' )
		{
			/**
			 * Fixes a problem when 3-rd party plugins call nested shortcodes without executing main shortcode  (like YOAST in wpseo-filter-shortcodes)
			 */
			if( empty( $this->screen_options ) )
			{
				return '';
			}
			
			$result = $this->get_element_styles_item( compact( array( 'atts', 'content', 'shortcodename' ) ) );
			
			extract( $result );

			extract( $this->screen_options ); //return $av_font_classes, $av_title_font_classes and $av_display_classes

			$data = '';
			$style_hover = '';
			$background_hover = '';
			
			$display_char = av_icon( $atts['icon'], $atts['font'] );

			if( '' != $atts['color_options'] )
			{
				if( 'custom' != $atts['btn_color_bg_hover'] && 'btn_custom_grad' != $atts['btn_color_bg'] )
				{
					//	must be added otherwise we get a bottom border !!!
//					$style_hover = "style='background-color:{$atts['btn_color_bg_hover']};'";
					
					if( $this->is_special_button_color( $atts['btn_color_bg_hover'] ) )
					{
						$background_hover = "<span class='avia_button_background avia-button avia-color-" . $atts['btn_color_bg_hover'] . "' {$style_hover}></span>";
					}
				}
			}
           
			if( ! empty( $atts['label_display'] ) && $atts['label_display'] == 'av-button-label-on-hover' ) 
			{
				$data .= 'data-avia-tooltip="' . htmlspecialchars( $atts['label'] ) . '"';
				$atts['label'] = '';
			}

			$blank = AviaHelper::get_link_target( $atts['link_target'] );
            $link = trim( AviaHelper::get_url( $atts['link'] ) );
            $link = ( in_array( $link, array( 'http://', 'https://', 'manually' ) ) ) ? '' : $link;
			
			$title_attr = ! empty( $atts['title_attr'] ) && empty( $atts['label_display'] ) ? 'title="' . esc_attr( $atts['title_attr'] ) . '"' : '';
			
			
			$content_html = '';
			if( 'yes-left-icon' == $atts['icon_select'] ) 
			{
				$content_html .= "<span class='avia_button_icon avia_button_icon_left ' {$display_char}></span>";
			}

			$content_html .= "<span class='avia_iconbox_title' >" . $atts['label'] . "</span>";

			if( 'yes-right-icon' == $atts['icon_select'] ) 
			{
				$content_html .= "<span class='avia_button_icon avia_button_icon_right' {$display_char}></span>";
			}
			
			$style_tag = $element_styling->get_style_tag( $element_id );
			$container_class = $element_styling->get_class_string( 'container' );

			$output  = '';
			$output .= $style_tag;

			$output .=	"<a href='{$link}' {$data} class='{$container_class}' {$blank} {$title_attr}>";
			$output .=		$content_html;
			$output .=		$background_hover;
			$output .=	'</a>';

			return $output;
        }
    }
}
