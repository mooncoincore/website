<?php
/**
 * Events Countdown
 * 
 * Display a countdown to the next upcoming event
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( 'Tribe__Events__Main' ) )
{
	if( ! function_exists( 'av_countdown_events_fallback' ) )
	{
		function av_countdown_events_fallback()
		{
			return "<p>Please install the <a href='https://wordpress.org/plugins/the-events-calendar/'>The Events Calendar</a> or <a href='http://mbsy.co/6cr37'>The Events Calendar Pro</a> Plugin to display the countdown</p>";
		}
		
		add_shortcode( 'av_events_countdown', 'av_countdown_events_fallback' );
	}
	
	return;
}

 
if ( ! class_exists( 'avia_sc_events_countdown' ) ) 
{
	
	class avia_sc_events_countdown extends aviaShortcodeTemplate
	{
		
		/**
		 *
		 * @var array 
		 */
		protected $time_array;


		/**
		 * UTC startdate of first event
		 * 
		 * @since 4.5.6
		 * @var string 
		 */
		protected $start_date_utc;


		/**
		 * 
		 * @since 4.2.1
		 */
		public function __destruct() 
		{
			parent::__destruct();

			unset( $this->time_array );
		}
			
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			/**
			 * inconsistent behaviour up to 4.2: a new element was created with a close tag, after editing it was self closing !!!
			 * @since 4.2.1: We make new element self closing now because no id='content' exists.
			 */
			$this->config['self_closing']	= 'yes';
			$this->config['version']		= '1.0';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Events Countdown', 'avia_framework' );
			$this->config['tab']			= __( 'Plugin Additions', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-countdown.png';
			$this->config['order']			= 14;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_events_countdown';
			$this->config['tooltip']		= __( 'Display a countdown to the next upcoming event', 'avia_framework' );
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

			$this->start_date_utc = '';
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
								'template_id'	=> $this->popup_key( 'advanced_heading' )
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
							'name'		=> __( 'Which Entries?', 'avia_framework' ),
							'desc'		=> __( 'Select one or more taxonomies to get the next event for countdown. If none are selected all events are used.', 'avia_framework' ),
							'id'		=> 'categories',
							'type'		=> 'select',
							'taxonomy'	=> Tribe__Events__Main::TAXONOMY,
							'subtype'	=> 'cat',
							'multiple'	=> 6,
							'std'		=> '',
							'lockable'	=> true
						),
				
						array(
							'name' 	=> __( 'Display Event Title?', 'avia_framework' ),
							'desc' 	=> __( 'Choose here, if you want to display the event title', 'avia_framework' ),
							'id' 	=> 'title',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No Title, timer only', 'avia_framework' )	=> '',
												__( 'Title on top', 'avia_framework' )			=> 'top',
												__( 'Title below', 'avia_framework' )			=> 'bottom',
												)
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
					
						array(
							'name' 	=> __( 'Problems with next upcoming event incorrect?', 'avia_framework' ),
							'desc' 	=> __( 'Sometimes the next upcoming event is not queried correctly. This is a known bug. In this case select a number of days to start the query before today. Usually 3-4 days should fix the problem.', 'avia_framework' ),
							'id' 	=> 'query_fix',
							'type' 	=> 'select',
							'std' 	=> '0',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 1, 30, 1, array( __( 'No problems', 'avia_framework' ) => '0') )
						)
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_countdown' ), $c );
			
			
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
							
						array(	
							'name' 	=> __( 'Number Font Size', 'avia_framework' ),
							'desc' 	=> __( 'Size of your numbers in Pixel', 'avia_framework' ),
							'id' 	=> 'size',
							'type' 	=> 'select',
							'std'	=> '',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 20, 90, 1, array( __( 'Default Size', 'avia_framework' ) => '' ) ),
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Text Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_general' ), $template );
			
			$c = array(
						array(
							'name' 	=> __( 'Colors', 'avia_framework' ),
							'desc' 	=> __( 'Choose the colors here', 'avia_framework' ),
							'id' 	=> 'style',
							'type' 	=> 'select',
							'std' 	=> 'av-default-style',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Default',	'avia_framework' )			=> 'av-default-style',
												__( 'Theme colors',	'avia_framework' )		=> 'av-colored-style',
												__( 'Transparent Light', 'avia_framework' )	=> 'av-trans-light-style',
												__( 'Transparent Dark',  'avia_framework' )	=> 'av-trans-dark-style',
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
							'type'				=> 'template',
							'template_id'		=> 'heading_tag',
							'theme_default'		=> 'h3',
							'context'			=> __CLASS__,
							'lockable'			=> true,
							'required'			=> array( 'title', 'not', '' )
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
			$default = array(
						'categories' 	=> '',
						'min'			=> '1',
						'max'			=> '5',
						'align'			=> 'av-align-center',
						'size'			=> '',
						'title'			=> '',
						'query_fix' 	=> '0',
						'heading_tag'	=> '',
						'heading_class'	=> '',
					);
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			$meta = aviaShortcodeTemplate::set_frontend_developer_heading_tag( $atts, $meta );

			$atts = array_merge ( $default, $atts );

			$find_post = true;
			$offset = 0;

			while( $find_post )
			{
				$next = $this->fetch_upcoming( $offset, $atts['query_fix'], $atts['categories'] );

				$offset ++;

				if( empty( $next[0] ) || ! $this->already_started( $next ) )
				{
					$find_post = false;
				}
			}
				
			if( ! empty( $next[0]->event_date_utc ) )
			{
				//	backwards compatibility
				$event_date = $next[0]->event_date_utc;
			}
			else if( ! empty( $next[0]->event_date ) )
			{
				$event_date = $next[0]->event_date;
			}
			else
			{
				$event_date = '';
			}

			if( empty( $next[0] ) || empty( $event_date ) || empty( $this->start_date_utc ) ) 
			{
				return '';
			}

			$events_date = explode( ' ', $this->start_date_utc );

			if( isset( $events_date[0] ) )
			{
				$atts['date'] = date( 'm/d/Y', strtotime( $events_date[0] ) );
			}
				
			if( isset( $events_date[1] ) )
			{
				$events_date = explode( ':', $events_date[1] );
				$atts['hour'] = $events_date[0];
				$atts['minute'] = $events_date[1];
			}

			$atts['link'] 	= get_permalink( $next[0]->ID );
			$title 			= get_the_title( $next[0]->ID );

			if( ! empty( $atts['title'] ) )
			{
				$atts['title']  = array( $atts['title'] => __( 'Upcoming','avia_framework' ) . ': ' . $title );
			}

			$atts['timezone'] = 'UTC';

			$timer = new avia_sc_countdown( $this->builder );
			$output = $timer->shortcode_handler( $atts, $content, $shortcodename, $meta );

			return $output;
		}
		
		/**
		 * 
		 * @since < 4.0
		 * @param int $offset
		 * @param int $query_fix
		 * @param string $categories
		 * @return WP_Query
		 */
		protected function fetch_upcoming( $offset = 0, $query_fix = 0, $categories = '' )
		{
			$start_date = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ) - $query_fix, date( 'Y' ) ) );

			$terms = ( ! empty( $categories ) ) ? explode( ',', $categories ) : array();

			$query = array(
							'paged'				=> 1, 
							'posts_per_page'	=> 1, 
							'eventDisplay'		=> 'list', 
							'offset'			=> $offset,
							'start_date'		=> $start_date
						);

			if( isset( $terms[0] ) && ! empty( $terms[0] ) && ! is_null( $terms[0] ) && $terms[0] != 'null' )
			{
				$query['tax_query'] = array( 	
											array( 	'taxonomy' 	=> Tribe__Events__Main::TAXONOMY,
													'field' 	=> 'id',
													'terms' 	=> $terms,
													'operator' 	=> 'IN'
												)
											);

			}

			$upcoming = Tribe__Events__Query::getEvents( $query, false );

			return $upcoming;
		}

		/**
		 * 
		 * @since < 4.0
		 * @param WP_Query $next
		 * @return boolean
		 */
		protected function already_started( Array $next )
		{
			$this->start_date_utc = '';

			//	backwards compatibility
			if( empty( $next[0]->event_date_utc ) && empty( $next[0]->event_date ) ) 
			{
				return true;
			}

			/**
			 * Compare UTC times ( https://www.php.net/manual/en/function.time.php#100220 )
			 */
			$today = date( 'Y-m-d H:i:s' );
			$this->start_date_utc = get_post_meta( $next[0]->ID, '_EventStartDateUTC', true );

			if( empty( $this->start_date_utc ) )
			{
				return true;
			}

			if( $today < $this->start_date_utc )
			{
				return false;
			}

			return true;
		}

	}
}
