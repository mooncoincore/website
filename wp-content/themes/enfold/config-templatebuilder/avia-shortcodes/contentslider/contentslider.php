<?php
/**
 * Content Slider
 * 
 * Shortcode that display a content slider element
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_content_slider' ) )
{
  class avia_sc_content_slider extends aviaShortcodeTemplate
  {
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Content Slider', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-contentslider.png';
			$this->config['order']			= 83;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_content_slider';
			$this->config['shortcode_nested'] = array( 'av_content_slide' );
			$this->config['tooltip'] 	    = __( 'Display a content slider element', 'avia_framework' );
			$this->config['preview'] 		= false;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['name_item']		= __( 'Content Slider Item', 'avia_framework' );
			$this->config['tooltip_item']	= __( 'A Content Slider Element Item', 'avia_framework' );
		}

		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-slideshow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow.css', array( 'avia-layout' ), false );
			wp_enqueue_style( 'avia-module-postslider', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/postslider/postslider.css', array( 'avia-layout' ), false );
			wp_enqueue_style( 'avia-module-slideshow-contentpartner', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/contentslider/contentslider.css', array( 'avia-module-slideshow' ), false );

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
								'template_id'	=> $this->popup_key( 'content_slides' ),
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
								'template_id'	=> $this->popup_key( 'styling_controls' ),
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
								'template_id'	=> $this->popup_key( 'advanced_heading' ),
								'nodescription' => true
							),
				
						array(
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_animation' ),
								'nodescription' => true
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
							'name'  => __( 'Heading', 'avia_framework' ),
							'desc'  => __( 'Do you want to display a heading above the slides?', 'avia_framework' ),
							'id'    => 'heading',
							'type'  => 'input',
							'std'   => '',
							'lockable'	=> true,
						),
				
						array(
							'name'			=> __( 'Add/Edit Slides', 'avia_framework' ),
							'desc'			=> __( 'Here you can add, remove and edit the slides you want to display.', 'avia_framework' ),
							'id'			=> 'content',
							'type'			=> 'modal_group',
							'modal_title'	=> __( 'Edit Form Element', 'avia_framework' ),
							'std'			=> array(
													array( 'title' => __( 'Slide 1', 'avia_framework' ), 'tags' => '' ),
													array( 'title' => __( 'Slide 2', 'avia_framework' ), 'tags' => '' ),

												),
							'editable_item'	=> true,
							'lockable'		=> true,
							'tmpl_set_default'	=> false,
							'subelements'	=> $this->create_modal()
						)
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_slides' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Slider controls', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to display slider control buttons?', 'avia_framework' ),
							'id' 	=> 'navigation',
							'type' 	=> 'select',
							'std' 	=> 'arrows',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Yes, display arrow control buttons', 'avia_framework' )		=> 'arrows',
												__( 'Yes, display dot control buttons', 'avia_framework' )			=> 'dots',
												__( 'No, do not display any control buttons', 'avia_framework' )	=> 'no'
											),
						),

						array(
							'name' 	=> __( 'Columns', 'avia_framework' ),
							'desc' 	=> __( 'How many Slide columns should be displayed?', 'avia_framework' ),
							'id' 	=> 'columns',
							'type' 	=> 'select',
							'std' 	=> '1',
							'lockable'	=> true,
							'subtype'	=> array(	
												__( '1 Column', 'avia_framework' )	=> '1',
												__( '2 Columns', 'avia_framework' )	=> '2',
												__( '3 Columns', 'avia_framework' )	=> '3',
												__( '4 Columns', 'avia_framework' )	=> '4',
												__( '5 Columns', 'avia_framework' )	=> '5',
												__( '6 Columns', 'avia_framework' )	=> '6'
											)
						),
				
						array(
							'name' 	=> __( 'Font Colors', 'avia_framework' ),
							'desc' 	=> __( 'Either use the themes default colors or apply some custom ones', 'avia_framework' ),
							'id' 	=> 'font_color',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array( 
												__( 'Default', 'avia_framework' )				=> '',
												__( 'Define Custom Colors', 'avia_framework' )	=> 'custom'
											),
						),
					
						array(	
							'name' 	=> __( 'Custom Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'color',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'container_class' => 'av_half av_half_first',
							'lockable'	=> true,
							'required'	=> array( 'font_color', 'equals', 'custom' )
						),	
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_controls' ), $c );
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'type'				=> 'template',
							'template_id'		=> 'heading_tag',
							'theme_default'		=> 'h3',
							'context'			=> __CLASS__,
							'lockable'			=> true,
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Heading Tag', 'avia_framework' ),
								'content'		=> $c 
							),
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_heading' ), $template );
			
			$c = array(
						array(
							'name' 	=> __( 'Transition', 'avia_framework' ),
							'desc' 	=> __( 'Choose the transition for your content slider.', 'avia_framework' ),
							'id' 	=> 'animation',
							'type' 	=> 'select',
							'std' 	=> 'slide',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Slide', 'avia_framework' )	=> 'slide',
												__( 'Fade', 'avia_framework' )	=> 'fade'
											),
						),

                    

						array(
							'name' 	=> __( 'Autorotation active?', 'avia_framework' ),
							'desc' 	=> __( 'Check if the content slider should rotate by default', 'avia_framework' ),
							'id' 	=> 'autoplay',
							'type' 	=> 'select',
							'std' 	=> 'false',
							'lockable'	=> true,
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
							'subtype'	=> array( '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10', '15'=>'15', '20'=>'20', '30'=>'30', '40'=>'40', '60'=>'60', '100'=>'100' )
						),
						
				
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
							'template_id'	=> $this->popup_key( 'modal_content_slide' )
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
													$this->popup_key( 'modal_advanced_heading' ),
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
							'name' 	=> __( 'Slide Title', 'avia_framework' ),
							'desc' 	=> __( 'Enter the slide title here (Better keep it short)', 'avia_framework' ),
							'id' 	=> 'title',
							'type' 	=> 'input',
							'std' 	=> 'Slide Title',
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),
				
						array(
							'name' 	=> __( 'Slide Content', 'avia_framework' ),
							'desc' 	=> __( 'Enter some content here', 'avia_framework' ),
							'id' 	=> 'content',
							'type' 	=> 'tiny_mce',
							'std' 	=> __( 'Slide Content goes here', 'avia_framework' ),
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),
							
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_slide' ), $c );
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'type'				=> 'template',
							'template_id'		=> 'heading_tag',
							'theme_default'		=> 'h3',
							'context'			=> __CLASS__,
							'lockable'			=> true
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Heading Tag', 'avia_framework' ),
								'content'		=> $c 
							),
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_heading' ), $template );
			
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'linkpicker_toggle',
							'name'			=> __( 'Title Link?', 'avia_framework' ),
							'desc'			=> __( 'Where should your title link to?', 'avia_framework' ),
							'lockable'		=> true,
							'subtypes'		=> array( 'no', 'manually', 'single', 'taxonomy' )
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
			
			$template = $this->update_template_lockable( 'heading', ' - <strong>{{heading}}</strong>', $locked );
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
			
			$template = $this->update_template_lockable( 'title', '{{title}}', $locked );

			$params['innerHtml']  = '<div data-update_element_template="yes">';
			$params['innerHtml'] .=		"<div class='avia_title_container' {$template}>{$attr['title']}</div>";
			$params['innerHtml'] .= '</div>';

			return $params;
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
						'type'				=> 'slider',
						'autoplay'			=> 'false',
						'animation'			=> 'fade',
						'interval'			=> 5,
						'navigation'		=> 'arrows',
						'heading'			=> '',
						'columns'			=> 3,
						'font_color'		=> '',
						'color'				=> '',
						'styling'			=> '',
						'av-desktop-hide'	=> '',
						'av-medium-hide'	=> '',
						'av-small-hide'		=> '',
						'av-mini-hide'		=> ''
					);
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			$meta = aviaShortcodeTemplate::set_frontend_developer_heading_tag( $atts, $meta );

			$add = array(
					'handle'			=> $shortcodename,
					'content'			=> ShortcodeHelper::shortcode2array( $content, 1 ),
					'class'				=> $meta['el_class'],
					'custom_markup'		=> $meta['custom_markup'],
					'el_id'				=> $meta['custom_el_id'],
					'heading_tag'		=> $meta['heading_tag'],
					'heading_class'		=> $meta['heading_class'],
					'caller'			=> $this
				);
			
			$defaults = array_merge( $default, $add );
			
			$atts = shortcode_atts( $defaults, $atts, $this->config['shortcode'] );

			foreach( $atts['content'] as $key => &$item ) 
			{
				$item_def = $this->get_default_modal_group_args();
				Avia_Element_Templates()->set_locked_attributes( $item['attr'], $this, $this->config['shortcode_nested'][0], $item_def, $locked, $item['content'] );
			}
			
			unset( $item );

			if( $atts['font_color'] == 'custom' )
			{
				$atts['class'] .= ' av_inherit_color';
				$atts['styling'] .= ! empty( $atts['color'] ) ? " color:{$atts['color']}; " : '';
				if( $atts['styling'] ) 
				{
					$atts['styling'] = " style='{$atts['styling']}'" ;
				}
			}
				
			$slider = new avia_content_slider( $atts );
			
			return $slider->html();
		}

	}
}


if ( ! class_exists( 'avia_content_slider' ) )
{
	class avia_content_slider
	{
		
		/**
		 * slider count for the current page
		 * 
		 * @var int 
		 */
		static public $slider = 0;
		
		/**
		 * base config set on initialization
		 * 
		 * @var array 
		 */
		protected $config;

		/**
		 * 
		 * @param array $config
		 */
		public function __construct( $config )
		{
			$this->config = array_merge( array(
									'type'          => 'grid',
									'autoplay'		=> 'false',
									'animation'     => 'fade',
									'handle'		=> '',
									'heading'		=> '',
									'navigation'    => 'arrows',
									'columns'       => 3,
									'interval'		=> 5,
									'class'			=> '',
									'custom_markup' => '',
									'css_id'		=> '',
									'content'		=> array(),
									'styling'		=> '',
									'el_id'			=> '',
									'heading_tag'	=> '',
									'heading_class'	=> '',
									'caller'		=> null
								), $config );
		}

		/**
		 * 
		 * @since 4.5.7.2
		 */
		public function __destruct() 
		{
			unset( $this->config );
		}

		/**
		 * 
		 * @return string
		 */
		public function html()
		{
			$output = '';
			$counter = 0;
            avia_content_slider::$slider++;
			
			if( empty( $this->config['content'] ) ) 
			{
				return $output;
			}

            //$html .= empty($this->subslides) ? $this->default_slide() : $this->advanced_slide();
			
			extract( AviaHelper::av_mobile_sizes( $this->config ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 
            extract( $this->config );
			
			$default_heading = ! empty( $heading_tag ) ? $heading_tag : 'h3';
			$args = array(
						'heading'		=> $default_heading,
						'extra_class'	=> $heading_class
					);

			$extra_args = array( $this, 'slider_title' );

			/**
			 * @since 4.5.5
			 * @return array
			 */
			$args = apply_filters( 'avf_customize_heading_settings', $args, __CLASS__, $extra_args );

			$heading1 = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
			$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : $heading_class;

            $extraClass = 'first';
            $grid = 'one_third';
            $slide_loop_count = 1;
            $loop_counter = 1;
            $total = $columns % 2 ? 'odd' : 'even';
			$heading = ! empty( $this->config['heading'] ) ? "<{$heading1} class='{$css}'>{$this->config['heading']}</{$heading1}>" : '&nbsp;';
            $slide_count = count( $content );

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
            }

            $data = AviaHelper::create_data_string( array( 'autoplay' => $autoplay, 'interval' => $interval, 'animation' => $animation, 'show_slide_delay' => 30 ) );

            $thumb_fallback = '';
            $output .= "<div {$el_id} {$data} class='avia-content-slider-element-container avia-content-slider-element-{$type} avia-content-slider avia-smallarrow-slider avia-content-{$type}-active avia-content-slider" . avia_content_slider::$slider . " avia-content-slider-{$total} {$class} {$av_display_classes}' {$styling}>";

                $heading_class = '';
                if( $navigation == 'no' ) 
				{
					$heading_class .= ' no-content-slider-navigation ';
				}
				
                if( $heading == '&nbsp;' ) 
				{
					$heading_class .= ' no-content-slider-heading ';
				}

				$output .= "<div class='avia-smallarrow-slider-heading {$heading_class}'>";
				$output .= "<div class='new-special-heading'>{$heading}</div>";

				if( $slide_count > $columns && $type == 'slider' && $navigation != 'no' )
	            {
	                if( $navigation == 'dots' ) 
					{
						$output .= $this->slide_navigation_dots();
					}
					
                    if( $navigation == 'arrows' ) 
					{
						$output .= $this->slide_navigation_arrows();
					}
	            }
				$output .= '</div>';


				$output .= "<div class='avia-content-slider-inner'>";

                foreach( $content as $key => $value )
                {
					$link = $linktarget = '';
					
					$meta = aviaShortcodeTemplate::set_frontend_developer_heading_tag( $value['attr'] );

                    extract( $value['attr'] );

                    $link = AviaHelper::get_url( $link );
					$blank = AviaHelper::get_link_target( $linktarget );
                    
                    $parity = $loop_counter % 2 ? 'odd' : 'even';
                    $last = $slide_count == $slide_loop_count ? ' post-entry-last ' : '';
                    $post_class = "post-entry slide-entry-overview slide-loop-{$slide_loop_count} slide-parity-{$parity} {$last}";

                    if( $loop_counter == 1 ) 
					{
						$output .= "<div class='slide-entry-wrap'>";
					}

                    $markup = avia_markup_helper( array( 'context' => 'entry', 'echo' => false, 'custom_markup' => $custom_markup ) );
                    $output .= "<section class='slide-entry flex_column {$post_class} {$grid} {$extraClass}' $markup>";

                    $markup = avia_markup_helper( array( 'context' => 'entry_title', 'echo' => false, 'custom_markup' => $custom_markup ) );
					
					$default_heading = ! empty( $meta['heading_tag'] ) ? $meta['heading_tag'] : 'h3';
					$args = array(
								'heading'		=> $default_heading,
								'extra_class'	=> $meta['heading_class']
							);

					$extra_args = array( $this, 'slider_entry' );

					/**
					 * @since 4.5.5
					 * @return array
					 */
					$args = apply_filters( 'avf_customize_heading_settings', $args, __CLASS__, $extra_args );

					$heading1 = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
					$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : $meta['heading_class'];
					
                    $output .= ! empty( $title ) ? "<{$heading1} class='slide-entry-title entry-title {$css}' $markup>" : '';
                    $output .= ( ! empty( $link ) && ! empty( $title ) ) ? "<a href='{$link}' $blank title='" . esc_attr($title) . "'>" . $title . '</a>' : $title;
                    $output .= ! empty( $title ) ? "</{$heading1}>" : '';

                    $markup = avia_markup_helper( array( 'context' => 'entry_content', 'echo' => false, 'custom_markup' => $custom_markup ) );
                    $output .= ! empty( $value['content'] ) ? "<div class='slide-entry-excerpt entry-content' $markup>" . ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $value['content'] ) ) . '</div>' : '';

                    $output .= '</section>';

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

			$output .= '</div>';

			return $output;
		}

		/**
		 * Create arrows to scroll content slides
		 * 
		 * @since 4.8.3			reroute to aviaFrontTemplates
		 * @return string
		 */
        protected function slide_navigation_arrows()
        {
			$args = array(
						'context'	=> get_class(),
						'params'	=> $this->config
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
						'total_entries'		=> count( $this->config['content'] ),
						'container_entries'	=> $this->config['columns'],
						'context'			=> get_class(),
						'params'			=> $this
					);
			
			return aviaFrontTemplates::slide_navigation_dots( $args );
        }
	}
}


