<?php
/**
 * Progress Bars
 * 
 * Creates some progress bars
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_progressbar' ) )
{
	class avia_sc_progressbar extends aviaShortcodeTemplate
	{
		/**
		 * Holds the main attributes of the shortcode to be able to access in item shortcode
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		protected $parent_atts;
		
		/**
		 * 
		 * @since 4.8.4
		 * @param \AviaBuilder $builder
		 */
		public function __construct( \AviaBuilder $builder ) 
		{
			parent::__construct( $builder );
			
			$this->parent_atts = array();
		}
		
		/**
		 * @since 4.8.4
		 */
		public function __destruct() 
		{
			parent::__destruct();
			
			unset( $this->parent_atts );
		}
		
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Progress Bars', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-progressbar.png';
			$this->config['order']			= 30;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']	 	= 'av_progress';
			$this->config['shortcode_nested'] = array( 'av_progress_bar' );
			$this->config['tooltip']	 	= __( 'Create some progress bars', 'avia_framework' );
			$this->config['preview']	 	= true;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
			$this->config['name_item']		= __( 'Progress Bar Item', 'avia_framework' );
			$this->config['tooltip_item']	= __( 'A Progress Bars Element Item', 'avia_framework' );
		}

		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-progress-bar', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/progressbar/progressbar.css', array( 'avia-layout' ), false );

			//load js
			wp_enqueue_script( 'avia-module-numbers', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/numbers/numbers.js', array( 'avia-shortcodes' ), false, true );
			wp_enqueue_script( 'avia-module-progress-bar', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/progressbar/progressbar.js', array( 'avia-shortcodes' ), false, true );
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
							'template_id'	=> $this->popup_key( 'content_bars' )
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
													$this->popup_key( 'styling_general' ),
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
								'template_id'	=> $this->popup_key( 'advanced_animation' )
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
							'name'			=> __( 'Add/Edit Progress Bars', 'avia_framework' ),
							'desc'			=> __( 'Here you can add, remove and edit the various progress bars.', 'avia_framework' ),
							'type'			=> 'modal_group',
							'id'			=> 'content',
							'modal_title'	=> __( 'Edit Progress Bars', 'avia_framework' ),
							'editable_item'	=> true,
							'lockable'		=> true,
							'tmpl_set_default'	=> false,
							'std'			=> array(
													array(
														'title'			=> __( 'Skill or Task', 'avia_framework' ), 
														'icon'			=> '43', 
														'progress'		=> '100', 
														'icon_select'	=> 'no'
													),
												),
							'subelements'	=> $this->create_modal()
						),
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_bars' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Progress Bar Style', 'avia_framework' ),
							'desc' 	=> __( 'Choose the styling of the progress bar here', 'avia_framework' ),
							'id' 	=> 'bar_styling_secondary',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Rounded Big Bars', 'avia_framework' )	=> '',
												__( 'Minimal Bars', 'avia_framework' )		=> 'av-small-bar'
											)
						),

						array(
							'name' 	=> __( 'Show Progress Bar percentage?', 'avia_framework' ),
							'desc' 	=> __( 'Choose if you want to show the numeric percentage of the progress bar', 'avia_framework' ),
							'id' 	=> 'show_percentage',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'required'	=> array( 'bar_styling_secondary', 'equals', 'av-small-bar' ),
							'subtype'	=> array(
												__( 'Hide', 'avia_framework' )	=> '',
												__( 'Show', 'avia_framework' )	=> 'av-show-bar-percentage'
											)
						),		

						array(
							'name' 	=> __( 'Progress Bar Height?', 'avia_framework' ),
							'desc' 	=> __( 'Set the height of the progress bar', 'avia_framework' ),
							'id' 	=> 'bar_height',
							'type' 	=> 'select',
							'std' 	=> '10',
							'lockable'	=> true,
							'required'	=> array( 'bar_styling_secondary', 'equals',  'av-small-bar' ),
							'subtype'	=> AviaHtmlHelper::number_array( 1, 50, 1, array(), 'px' )
						),

				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'General Styling', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_general' ), $template );
			
			
			$c = array(
						array(
							'name' 	=> __( 'Progress Bar Coloring', 'avia_framework' ),
							'desc' 	=> __( 'Choose the coloring of the progress bar here', 'avia_framework' ),
							'id' 	=> 'bar_styling',
							'type' 	=> 'select',
							'std' 	=> 'av-striped-bar',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Striped', 'avia_framework' )		=> 'av-striped-bar',
												__( 'Single Color', 'avia_framework' )	=> 'av-flat-bar' 
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
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Progress Bar Animation enabled?', 'avia_framework' ),
							'desc' 	=> __( 'Choose if you want to enable the continuous animation of the progress bar', 'avia_framework' ),
							'id' 	=> 'bar_animation',
							'type' 	=> 'select',
							'std' 	=> 'av-animated-bar',
							'lockable'	=> true,
							'required'	=> array( 'bar_styling', 'not', 'av-flat-bar' ),
							'subtype'	=> array(
												__( 'Enabled', 'avia_framework' )	=> 'av-animated-bar',
												__( 'Disabled', 'avia_framework' )	=> 'av-fixed-bar'
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
							'template_id'	=> $this->popup_key( 'modal_content_bar' )
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
							'template_id'	=> $this->popup_key( 'modal_styling_colors' )
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
							'name'		=> __( 'Progress Bars Title', 'avia_framework' ),
							'desc'		=> __( 'Enter the Progress Bars title here', 'avia_framework' ) ,
							'id'		=> 'title',
							'type'		=> 'input',
							'std'		=> '',
							'lockable'	=> true
						),

						array(
							'name'		=> __( 'Progress in &percnt;', 'avia_framework' ),
							'desc'		=> __( 'Select a number between 0 and 100', 'avia_framework' ),
							'id'		=> 'progress',
							'type'		=> 'select',
							'std'		=> '100',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 0, 100, 1, array(), '%' )
						),

						array(
							'name'		=> __( 'Icon', 'avia_framework' ),
							'desc'		=> __( 'Should an icon be displayed at the left side of the progress bar', 'avia_framework' ),
							'id'		=> 'icon_select',
							'type'		=> 'select',
							'std'		=> 'no',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No Icon', 'avia_framework' )			=> 'no',
												__( 'Yes, display Icon', 'avia_framework' )	=> 'yes'
											)
						),

						array(
							'name'		=> __( 'List Item Icon','avia_framework' ),
							'desc'		=> __( 'Select an icon for your list item below','avia_framework' ),
							'id'		=> 'icon',
							'type'		=> 'iconfont',
							'required'	=> array( 'icon_select', 'equals', 'yes' ),
							'std'		=> '',
							'lockable'	=> true,
							'locked'	=> array( 'icon', 'font' )
						),
									
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_bar' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'named_colors',
							'name'			=> __( 'Bar Color', 'avia_framework' ),
							'desc'			=> __( 'Choose a color for your progress bar here', 'avia_framework' ),
							'id'			=> 'color',
							'std'			=> 'theme-color',
							'custom'		=> false,
							'lockable'		=> true,
							'translucent'	=> array(),
							'no_alternate'	=> true
						),

				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_colors' ), $c );
			
		}

		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * 
		 * @param array $params this array holds the default values for $content and $args.
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_sub_element( $params )
		{
			$default = array();
			$locked = array();
			$attr = $params['args'];
			Avia_Element_Templates()->set_locked_attributes( $attr, $this, $this->config['shortcode_nested'][0], $default, $locked );
			
			
			$template = $this->update_template_lockable( 'title', '{{title}}: ', $locked );
			$template_percent = $this->update_template_lockable( 'progress', '{{progress}}%', $locked );

			extract( av_backend_icon( array( 'args' => $attr ) ) ); // creates $font and $display_char if the icon was passed as param 'icon' and the font as 'font' 

			if( empty( $attr['icon_select'] ) ) 
			{
				$params['args']['icon_select'] = 'no';
				$attr['icon_select'] = 'no';
			}

			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_title_container' data-update_element_template='yes'>";
			$params['innerHtml'] .=		'<span ' . $this->class_by_arguments_lockable( 'icon_select', $attr, $locked ) . '>';
			$params['innerHtml'] .=			'<span ' . $this->class_by_arguments_lockable( 'font', $font, $locked ) . '>';
			$params['innerHtml'] .=				'<span ' . $this->update_option_lockable( array( 'icon', 'icon_fakeArg' ), $locked ) . " class='avia_tab_icon'>{$display_char}</span>";
			$params['innerHtml'] .=			'</span>';
			$params['innerHtml'] .=			"<span {$template} >{$attr['title']}: </span>";
			$params['innerHtml'] .=			"<span {$template_percent} >{$attr['progress']}%</span>";
			$params['innerHtml'] .=		'</span>';
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
						'position'				=> 'left', 
						'bar_styling'			=> 'av-striped-bar', 
						'bar_styling_secondary'	=> '',
						'show_percentage'		=> '',
						'bar_height'			=> 10,
						'bar_animation'			=> 'av-animated-bar',
				
						'bars'					=> array()			//	prepared bars - updated with CET content
					);
			
			$default = $this->sync_sc_defaults_array( $default, 'no_modal_item', 'no_content' );
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			$bars = ShortcodeHelper::shortcode2array( $content );
			
			foreach( $bars as &$bar )
			{
				$item_def = $this->get_default_modal_group_args();
				Avia_Element_Templates()->set_locked_attributes( $bar['attr'], $this, $this->config['shortcode_nested'][0], $item_def, $locked, $bar['content'] );
			}
			
			unset( $bar );
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			$atts['bars'] = $bars;
			
			
			$classes = array(
						'avia-progress-bar-container', 
						$element_id,
						$atts['bar_styling'],
						$atts['bar_animation'],
						$atts['bar_styling_secondary'],
						'avia_animate_when_almost_visible'
					);
			
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			if( ! empty( $atts['bar_styling_secondary'] ) )
			{
				$element_styling->add_styles( 'container-bar', array( 'height' => $atts['bar_height'] . 'px' ) );
			}
			
			
			$selectors = array(
							'container'		=> ".avia-progress-bar-container.{$element_id}",
							'container-bar'	=> ".avia-progress-bar-container.{$element_id} .progress",
				);
			
			$element_styling->add_selectors( $selectors );
			
			$result['default'] = $default;
			$result['atts'] = $atts;
			$result['content'] = $content;
			$result['element_styling'] = $element_styling;
			
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
			//	get settings from container element and remove to get correct element id (override self::has_global_attributes() to activate)
			$parent_atts = isset( $args['atts']['parent_atts'] ) ? $args['atts']['parent_atts'] : null;
			unset( $args['atts']['parent_atts'] );
			
			$result = parent::get_element_styles_item( $args );
			
			extract( $result );
			
			if( is_null( $parent_atts ) )
			{
				$parent_atts = $this->parent_atts;
			}
			
			$default = array(
						'color'			=> 'theme-color', 
						'progress'		=> '100', 
						'title'			=> '', 
						'icon'			=> '', 
						'font'			=> '', 
						'icon_select'	=> 'no'
					);
			
			
			$default = $this->sync_sc_defaults_array( $default, 'modal_item', 'no_content' );
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode_nested'][0] );
			
			$classes = array(
						'avia-progress-bar', 
						$element_id,
						$atts['color'] . '-bar',
						'icon-bar-' . $atts['icon_select']
					);
			
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			$element_styling->add_styles( 'progress-bar', array( 'width' => $atts['progress'] . '%' ) );
			
			
			$selectors = array(
						'container'		=> ".avia-progress-bar-container .avia-progress-bar.{$element_id}",
						'progress-bar'	=> "#top .avia-progress-bar-container .avia-progress-bar.{$element_id} .bar"
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
			
			if( empty( $atts['bars'] ) )
			{
				return '';
			}
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 
			
			extract( $atts );
			
			$this->parent_atts = $atts;
			
			
			$bar_html = '';
			
			foreach( $bars as $bar )
			{
				$bar_html .= $this->add_bar_html( $bar );
			}
				

			$style_tag = $element_styling->get_style_tag( $element_id );
			$item_tag = $element_styling->style_tag_html( $this->subitem_inline_styles, 'sub-' . $element_id );
			$container_class = $element_styling->get_class_string( 'container' );
				
			$output  = '';
			$output .= $style_tag;
			$output .= $item_tag;
			$output .= "<div {$meta['custom_el_id']} class='{$container_class} {$av_display_classes}'>";
			$output .=		$bar_html;
			$output .= '</div>';

			return $output;
		}
		
		/**
		 * Create a single progress bar HTML and set stylings
		 * 
		 * @since 4.8.4
		 * @param array $bar
		 * @return string
		 */
		protected function add_bar_html( array $bar )
		{
			//	init parameters for normal shortcode handler
			$atts = $bar['attr'];
			$content = $bar['content'];
			$shortcodename = $this->config['shortcode_nested'][0];
			
			
			$result = $this->get_element_styles_item( compact( array( 'atts', 'content', 'shortcodename' ) ) );
			
			extract( $result );
			
			extract( $atts );
			
			
			
			$display_char = av_icon( $atts['icon'], $atts['font'] );
			
			
			$this->subitem_inline_styles .= $element_styling->get_style_tag( $element_id, 'rules_only' );
			$container_class = $element_styling->get_class_string( 'container' );
			
			$bar_html = '';
			$bar_html .= "<div class='{$container_class} {$atts['color']}-bar icon-bar-{$atts['icon_select']}'>";

			if( $atts['icon_select'] == 'yes' || $atts['title'] )
			{
				$bar_html .= '<div class="progressbar-title-wrap">';
				$bar_html .=		"<div class='progressbar-icon'><span class='progressbar-char' {$display_char}></span></div>";
				$bar_html .=		"<div class='progressbar-title'>{$atts['title']}</div>";
				$bar_html .= '</div>';
			}

			if( ! empty( $this->parent_atts['bar_styling_secondary']) && ! empty( $this->parent_atts['show_percentage'] ) )
			{ 
				$bar_html .= '<div class="progressbar-percent" data-timer="2200">';
				$bar_html .=	"<span class='av-bar-counter __av-single-number' data-number='{$atts['progress']}'>0</span>%";
				$bar_html .= '</div>';
			}

			$bar_html .= 	'<div class="progress">';
			$bar_html .=		'<div class="bar-outer">';
			$bar_html .=			"<div class='bar' data-progress='{$atts['progress']}'></div>";
			$bar_html .=		'</div>';
			$bar_html .=	'</div>';
			
			$bar_html .= '</div>';
			
			return $bar_html;
		}
	}
}
