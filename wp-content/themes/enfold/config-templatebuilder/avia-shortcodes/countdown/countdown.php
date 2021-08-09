<?php
/**
 * Animated Countdown
 * 
 * Display Numbers that count from 0 to a specific date
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_countdown' ) ) 
{
	
	class avia_sc_countdown extends aviaShortcodeTemplate
	{
		/**
		 * @since 4.5.7.2
		 * @var array 
		 */
		protected $time_array;
		
		/**
		 * @since 4.8
		 * @var array 
		 */
		protected $full_time_array;

		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'yes';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Animated Countdown', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-countdown.png';
			$this->config['order']			= 14;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_countdown';
			$this->config['tooltip']		= __( 'Display a countdown to a specific date', 'avia_framework' );
			$this->config['preview']		= 'xlarge';
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
				
			$this->time_array = array(
								__( 'Second', 'avia_framework' )	=> '1',
								__( 'Minute', 'avia_framework' )	=> '2',	
								__( 'Hour', 'avia_framework' )		=> '3',
								__( 'Day', 'avia_framework' )		=> '4',
								__( 'Week', 'avia_framework' )		=> '5',
								__( 'Month', 'avia_framework' )		=> '6',
								__( 'Year', 'avia_framework' )		=> '7'
							);
			
			$this->full_time_array = array(
					1	=> array( 
								'interval'		=> 1000, 
								'class'			=> 'seconds',
								'label'			=> __( 'Second', 'avia_framework' ),
								'label_multi'	=> __( 'Seconds', 'avia_framework' )
							),
					2	=> array( 
								'interval'		=> 60000,
								'class'			=> 'minutes',
								'label'			=> __( 'Minute', 'avia_framework' ),
								'label_multi'	=> __( 'Minutes', 'avia_framework' )
							),
					3	=> array(
								'interval'		=> 3600000,
								'class'			=> 'hours',
								'label'			=> __( 'Hour', 'avia_framework'),
								'label_multi'	=> __( 'Hours', 'avia_framework' )
							),
					4	=> array(
								'interval'		=> 86400000,
								'class'			=> 'days',
								'label'			=> __( 'Day', 'avia_framework' ),
								'label_multi'	=> __('Days', 'avia_framework' )
							),
					5	=> array( 
								'interval'		=> 604800000,
								'class'			=> 'weeks',
								'label'			=> __( 'Week', 'avia_framework' ),
								'label_multi'	=> __('Weeks', 'avia_framework' )
							),
					6	=> array(
								'interval'		=> 2678400000,
								'class'			=> 'months',
								'label'			=> __( 'Month', 'avia_framework' ),
								'label_multi'	=> __( 'Months', 'avia_framework' )
							),
					7	=> array(
								'interval'		=> 31536000000,
								'class'			=> 'years',
								'label'			=> __( 'Year', 'avia_framework' ),
								'label_multi'	=> __( 'Years', 'avia_framework' )
							)
				);
				
		}

		/**
		 * @since 4.5.7.2
		 */
		public function __destruct() 
		{	
			unset( $this->time_array );
			unset( $this->full_time_array );

			parent::__destruct();
		}

		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-countdown', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/countdown/countdown.css', array( 'avia-layout' ), false );

			//load js
			wp_enqueue_script( 'avia-module-countdown', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/countdown/countdown.js', array( 'avia-shortcodes' ), false, true );
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
								'template_id'	=> $this->popup_key( 'content_countdown' )
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
													$this->popup_key( 'styling_alignment' ),
													$this->popup_key( 'styling_fonts' ),
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
							'name' 	=> __( 'Date', 'avia_framework' ),
							'desc' 	=> __( 'Pick a date in the future.', 'avia_framework' ),
							'id' 	=> 'date',
							'std' 	=> '',
							'type' 	=> 'datepicker',
							'container_class' => 'av_third av_third_first',
							'dp_params'	=> array(
												'dateFormat'	=> 'mm / dd / yy',
												'minDate'		=> 0
											),
							'lockable'	=> true
						),
					
						array(	
							'name' 	=> __( 'Hour', 'avia_framework' ),
							'desc' 	=> __( 'Pick the hour of the day', 'avia_framework' ),
							'id' 	=> 'hour',
							'type' 	=> 'select',
							'std' 	=> '12',
							'container_class' => 'av_third',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 0, 23, 1, array(), ' h' ) 
						),
					
						array(	
							'name' 	=> __( 'Minute', 'avia_framework' ),
							'desc' 	=> __( 'Pick the minute of the hour', 'avia_framework' ),
							'id' 	=> 'minute',
							'type' 	=> 'select',
							'std' 	=> '0',
							'container_class' => 'av_third',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 0, 59, 1, array(),' min' )
						),
								
						array(	
							'name' 	=> __( 'Timezone', 'avia_framework' ),
							'desc' 	=> __( 'Select the timezone of your date.', 'avia_framework' ),
							'id' 	=> 'timezone',
							'type' 	=> 'timezone_choice',
							'std' 	=> '',
							'lockable'	=> true
						),
					
						array(	
							'name' 	=> __( 'Smallest time unit', 'avia_framework' ),
							'desc' 	=> __( 'The smallest unit that will be displayed', 'avia_framework' ),
							'id' 	=> 'min',
							'type' 	=> 'select',
							'std' 	=> '1',
							'lockable'	=> true,
							'subtype'	=> $this->time_array
						),
					
						array(	
							'name' 	=> __( 'Largest time unit', 'avia_framework' ),
							'desc' 	=> __( 'The largest unit that will be displayed', 'avia_framework' ),
							'id' 	=> 'max',
							'type' 	=> 'select',
							'std' 	=> '5',
							'lockable'	=> true,
							'subtype'	=> $this->time_array
						),
					
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_countdown' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Text Alignment', 'avia_framework' ),
							'desc' 	=> __( 'Choose here, how to align your text', 'avia_framework' ),
							'id' 	=> 'align',
							'type' 	=> 'select',
							'std' 	=> 'av-align-center',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Center', 'avia_framework' )	=> 'av-align-center',
												__( 'Right', 'avia_framework' )		=> 'av-align-right',
												__( 'Left', 'avia_framework' )		=> 'av-align-left',
											)
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Alignment', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_alignment' ), $template );
			
			$c = array(
						array(
							'name' 	=> __( 'Colors', 'avia_framework' ),
							'desc' 	=> __( 'Choose the colors here', 'avia_framework' ),
							'id' 	=> 'style',
							'type' 	=> 'select',
							'std' 	=> 'av-default-style',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )			=> 'av-default-style',
												__( 'Theme colors', 'avia_framework' )		=> 'av-colored-style',
												__( 'Transparent Light', 'avia_framework' )	=> 'av-trans-light-style',
												__( 'Transparent Dark', 'avia_framework' )	=> 'av-trans-dark-style'
											)
							),
				
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
			
			
			$c = array(
						array(
							'name'			=> __( 'Number Font Sizes', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the numbers.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'lockable'		=> true,
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 20, 90, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'size',
												'medium'	=> 'av-medium-font-size-title',
												'small'		=> 'av-small-font-size-title',
												'mini'		=> 'av-mini-font-size-title'
											)
						),
				
						array(
							'name'			=> __( 'Text Font Size', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the text.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'lockable'		=> true,
							'subtype'		=> array(
//												'default'	=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
//												'default'	=> 'custom_date_size',
												'medium'	=> 'av-medium-font-size',
												'small'		=> 'av-small-font-size',
												'mini'		=> 'av-mini-font-size'
											)
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Fonts', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_fonts' ), $template );
				
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
						'date' 		=> '', 
						'hour' 		=> '12', 
						'minute' 	=> '0',
						'timezone'	=> '',
						'min' 		=> '1', 
						'max' 		=> '5',
						'align'		=> 'av-align-center',
						'size'		=> '', 
						'style'		=> 'av-default-style', 
						'link'		=> '',			//	used by events_countown
						'title'		=> ''			//	used by events_countown
					);
			
			$default = $this->sync_sc_defaults_array( $default );
			
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			$meta = aviaShortcodeTemplate::set_frontend_developer_heading_tag( $atts, $meta );
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			$element_styling->create_callback_styles( $atts );
			
			$classes = array(
						'av-countdown-timer',
						$element_id,
						$atts['align'],
						$atts['style']
					);
			
			$element_styling->add_classes( 'container', $classes );
			
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			if( ! empty( $atts['size'] ) ) 
			{
				$element_styling->add_styles( 'time', array( 'font-size' => $atts['size'] . 'px' ) );
			}
			
			
			$selectors = array(
						'container'	=> ".av-countdown-timer.{$element_id}",
						'time'		=> ".av-countdown-timer.{$element_id} .av-countdown-time"
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
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 
			
			extract( $atts );
			
			
			
			$current_time_array = $this->clean_up_time_array( $atts );

			$offset = AviaHtmlHelper::get_timezone_offset( $timezone ) * 60;
			$interval = $this->full_time_array[ $min ]['interval'];
			$data_final_time = '';
			
			if( empty( $date ) )
			{
				return '';
			}
			
			$date = explode( '/', $date );

			$data_final_time .= " data-year='" . $date[2] . "'";
			$data_final_time .= " data-month='" . ( (int) $date[0] - 1 ) . "'";
			$data_final_time .= " data-day='" . $date[1] . "'";
			$data_final_time .= " data-hour='" . $hour . "'";
			$data_final_time .= " data-minute='" . $minute . "'";
			$data_final_time .= " data-timezone='" . $offset . "'";

			
				
			$tags = ! empty( $link ) ? array( "a href='{$link}' ", 'a' ) : array( 'span', 'span' );

			$default_heading = ! empty( $meta['heading_tag'] ) ? $meta['heading_tag'] : 'h3';
			$args = array(
						'heading'		=> $default_heading,
						'extra_class'	=> $meta['heading_class']
					);

			$extra_args = array( $this, $atts, $content, 'title' );

			/**
			 * @since 4.5.5
			 * @return array
			 */
			$args = apply_filters( 'avf_customize_heading_settings', $args, __CLASS__, $extra_args );

			$heading = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
			$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : $meta['heading_class'];


			//	used by e.g. events countdown - prepare output string
			if( is_array( $title ) )
			{
				if( isset( $title['top'] ) && ! empty( $title['top'] ) )
				{
					$title['top'] = "<{$heading}><{$tags[0]} class='av-countdown-timer-title av-countdown-timer-title-top {$css}'>{$title['top']}</{$tags[1]}></{$heading}>";
				}
				else
				{
					unset( $title['top'] );
				}
				
				if( isset( $title['bottom'] ) && ! empty( $title['bottom'] ) )
				{
					$title['bottom'] = "<{$heading}><{$tags[0]} class='av-countdown-timer-title av-countdown-timer-title-bottom {$css}'>{$title['bottom']}</{$tags[1]}></{$heading}>";
				}
				else
				{
					unset( $title['bottom'] );
				}
			}
			
			
			$style_tag = $element_styling->get_style_tag( $element_id );
			$container_class = $element_styling->get_class_string( 'container' );
				
			$output  = '';
			$output .= $style_tag;
			$output .= "<div {$meta['custom_el_id']} class='{$container_class} {$av_display_classes}' {$data_final_time} data-interval='{$interval}' data-maximum='{$max}' >";

			if( is_array( $title ) && isset( $title['top'] ) )
			{
				$output .=	$title['top'];
			}

			$output .= 		"<{$tags[0]} class='av-countdown-timer-inner'>";
				
			foreach( array_reverse( $current_time_array ) as $key => $number )
			{
				if( $number >= $min && $number <= $max )
				{
					$class   = $this->full_time_array[ $number ]['class'];
					$single  = $this->full_time_array[ $number ]['label'];
					$multi   = $this->full_time_array[ $number ]['label_multi'];

					$output .= "<span class='av-countdown-cell av-countdown-{$class}'>";
					$output .=		"<span class='av-countdown-cell-inner'>";

					$output .=			"<span class='av-countdown-time {$av_title_font_classes}' data-upate-width='{$class}'>0</span>";
					$output .=			"<span class='av-countdown-time-label {$av_font_classes}' data-label='{$single}' data-label-multi='{$multi}'>{$multi}</span>";

					$output .=		'</span>';
					$output .= '</span>';
				}
			}

			$output .= 		"</{$tags[1]}>";

			if( is_array( $title ) && isset( $title['bottom'] ) )
			{
				$output .=	$title['bottom'];
			}

			$output .= '</div>';
        		
        		
			return $output;
		}
		
		/**
		 * Remove week/month/year depending on setting for "Smallest time unit" and "Largest time unit"
		 * Make sure that $atts['min'] <= $atts['max'] before calling this function
		 * 
		 * @since 4.8
		 * @param array $atts
		 * @return array
		 */
		protected function clean_up_time_array( array &$atts )
		{
			//	fallback 
			if( $atts['min'] > $atts['max'] )
			{
				$atts['min'] = $atts['max'];
			}
			
			$current = array_flip( $this->time_array );
			
			if( $atts['max'] == 5 )
			{
				unset( $current[6] );
				unset( $current[7] );
			}
			else if( in_array( $atts['max'], array( 6, 7 ) ) )
			{
				if( $atts['min'] == 5 )
				{
					$atts['min'] = 6;
				}
				
				unset( $current[5] );
			}
			
			return array_flip( $current );
		}
	}
}

