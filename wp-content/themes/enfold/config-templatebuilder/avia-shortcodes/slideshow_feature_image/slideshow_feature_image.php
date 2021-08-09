<?php
/**
 * Featured Image Slider
 * 
 * Display a Slideshow of featured images from various posts
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_featureimage_slider' ) )
{
	class avia_sc_featureimage_slider extends aviaShortcodeTemplate
	{
		
		/**
		 *
		 * @var int 
		 */
		static public $slide_count = 0;
		
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['is_fullwidth']	= 'yes';
			$this->config['base_element']	= 'yes';
			
			/**
			 * inconsistent behaviour up to 4.2: a new element was created with a close tag, after editing it was self closing !!!
			 * @since 4.2.1: We make new element self closing now because no id='content' exists.
			 */
			$this->config['self_closing']	= 'yes';
			
			$this->config['name']			= __( 'Featured Image Slider', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-postslider.png';
			$this->config['order']			= 30;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_feature_image_slider';
			$this->config['tooltip']		= __( 'Display a Slideshow of featured images from various posts', 'avia_framework' );
			$this->config['drag-level']		= 3;
			$this->config['preview']		= 0;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}
		
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-slideshow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow.css', array( 'avia-layout' ), false );
			wp_enqueue_style( 'avia-module-slideshow-feature-image', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow_feature_image/slideshow_feature_image.css', array( 'avia-module-slideshow' ), false );

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
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array( 
													$this->popup_key( 'content_entries' ),
													$this->popup_key( 'content_filter' )
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
													$this->popup_key( 'styling_slider' ),
													$this->popup_key( 'styling_preview' ),
													$this->popup_key( 'styling_fonts' )
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
								'template_id'	=> $this->popup_key( 'advanced_animation_slider' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_overlay' )
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
								'desc'		=> __( 'Select which entries should be displayed by selecting a taxonomy', 'avia_framework' ),
								'id'		=> 'link',
								'type'		=> 'linkpicker',
								'fetchTMPL'	=> true,
								'multiple'	=> 6,
								'std'		=> 'category',
								'lockable'	=> true,
								'subtype'	=> array( __( 'Display Entries from:', 'avia_framework' ) => 'taxonomy' )
						),
				
						array(
								'name' 	=> __( 'Title and Read More Button', 'avia_framework' ),
								'desc' 	=> __( 'Choose if you want to only display the post title or title and a call to action button', 'avia_framework' ),
								'id' 	=> 'contents',
								'type' 	=> 'select',
								'std' 	=> 'title',
								'lockable'	=> true,
								'subtype'	=> array(
													__( 'Only Title', 'avia_framework' )						=> 'title',
													__( 'Title + Read More Button', 'avia_framework' )			=> 'title_read_more',
													__( 'Title + Excerpt + Read More Button', 'avia_framework' ) => 'title_excerpt_read_more',
												)
						)				
				);
			
			if( current_theme_supports( 'add_avia_builder_post_type_option' ) )
			{
				$element = array(	
								'type'			=> 'template',
								'template_id'	=> 'avia_builder_post_type_option',
								'lockable'		=> true,
							);
						
				array_unshift( $c, $element );
			}
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Select Entries', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_entries' ), $template );
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id' 	=> 'wc_options_non_products',
							'lockable'		=> true
						),
				
						array(	
							'type'			=> 'template',
							'template_id' 	=> 'date_query',
							'lockable'		=> true
						),
				
						array(
							'name' 	=> __( 'Entry Number', 'avia_framework' ),
							'desc' 	=> __( 'How many items should be displayed?', 'avia_framework' ),
							'id' 	=> 'items',
							'type' 	=> 'select',
							'std' 	=> '3',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 1, 100, 1, array( __( 'All', 'avia_framework' ) => '-1' ) )
						),

						array(
							'name' 	=> __( 'Offset Number', 'avia_framework' ),
							'desc' 	=> __( 'The offset determines where the query begins pulling posts. Useful if you want to remove a certain number of posts because you already query them with another element.', 'avia_framework' ),
							'id' 	=> 'offset',
							'type' 	=> 'select',
							'std' 	=> 'enforce_duplicates',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 1, 100, 1, array(
														__( 'Deactivate offset', 'avia_framework' )	=> '0', 
														__( 'Do not allow duplicate posts on the entire page (set offset automatically)', 'avia_framework' ) => 'no_duplicates',
														__( 'Enforce duplicates (if a blog element on the page should show the same entries as this slider use this setting)', 'avia_framework' ) => 'enforce_duplicates'
													)
												)
						)						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Filter', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_filter' ), $template );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name'  => __( 'Slider Width/Height Ratio', 'avia_framework' ),
							'desc'  => __( 'The slider will always stretch the full available width. Here you can enter the corresponding height (eg: 4:3, 16:9) or a fixed height in px (eg: 300px)', 'avia_framework' ),
							'id'    => 'slider_size',
							'type' 	=> 'input',
							'std' 	=> '16:9',
							'lockable'	=> true
						),
				
						array(	
							'name' 	=> __( 'Slideshow control styling?', 'avia_framework' ),
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
						)
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
			
			$c = array(
						array(
							'name' 	=> __( 'Preview Image Size', 'avia_framework' ),
							'desc' 	=> __( 'Set the image size of the preview images', 'avia_framework' ),
							'id' 	=> 'preview_mode',
							'type' 	=> 'select',
							'std' 	=> 'auto',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Set the preview image size automatically based on slider height', 'avia_framework' )	=> 'auto',
												__( 'Choose the preview image size manually (select thumbnail size)', 'avia_framework' )	=> 'custom'
											)
						),

						array(
							'name' 	=> __( 'Select custom preview image size', 'avia_framework' ),
							'desc' 	=> __( 'Choose image size for Preview Image', 'avia_framework' ),
							'id' 	=> 'image_size',
							'type' 	=> 'select',
							'std' 	=> 'portfolio',
							'lockable'	=> true,
							'required' 	=> array( 'preview_mode', 'equals', 'custom' ),
							'subtype' =>  AviaHelper::get_registered_image_sizes( array( 'logo' ) )
						)
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Preview Image Size', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_preview' ), $template );
			
			
			$c = array(
						array(
							'name'			=> __( 'Caption Title Font Sizes', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the caption titles.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'lockable'		=> true,
							'required'		=> array( 'contents', 'not', '' ),
							'subtype'		=> array(
//												'default'	=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
//												'default'	=> 'custom_title_size',
												'medium'	=> 'av-medium-font-size-title',
												'small'		=> 'av-small-font-size-title',
												'mini'		=> 'av-mini-font-size-title'
											)
						),
				
						array(
							'name'			=> __( 'Caption Content Font Sizes', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the excerpt.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'lockable'		=> true,
							'required'		=> array( 'contents', 'equals', 'title_excerpt_read_more' ),
							'subtype'		=> array(
//												'default'	=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
//												'default'	=> 'custom_excerpt_size',
												'medium'	=> 'av-medium-font-size',
												'small'		=> 'av-small-font-size',
												'mini'		=> 'av-mini-font-size'
											)
						)
						
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
			
			
			
			/**
			 * Animation Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Transition', 'avia_framework' ),
							'desc' 	=> __( 'Choose the transition for your Slider.', 'avia_framework' ),
							'id' 	=> 'animation',
							'type' 	=> 'select',
							'std' 	=> 'fade',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Slide', 'avia_framework' )	=> 'slide',
												__( 'Fade', 'avia_framework' )	=> 'fade'
											),
						),

						array(
							'name' 	=> __( 'Autorotation active?', 'avia_framework' ),
							'desc' 	=> __( 'Check if the slideshow should rotate by default', 'avia_framework' ),
							'id' 	=> 'autoplay',
							'type' 	=> 'select',
							'std' 	=> 'no',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Yes', 'avia_framework' )	=> 'yes',
												__( 'No', 'avia_framework' )	=> 'no'
											)
						),

						array(
							'name' 	=> __( 'Slideshow autorotation duration', 'avia_framework' ),
							'desc' 	=> __( 'Slideshow will rotate every X seconds', 'avia_framework' ),
							'id' 	=> 'interval',
							'type' 	=> 'select',
							'std' 	=> '5',
							'lockable'	=> true,
							'required' 	=> array( 'autoplay', 'equals', 'yes' ),
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
			
			
			$c = array(
						array(	
								'type'			=> 'template',
								'template_id'	=> 'slideshow_overlay',
								'lockable'		=> true
							),
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Overlay', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_overlay' ), $template );
			
			$c = array(
						array(	
							'type'				=> 'template',
							'template_id'		=> 'heading_tag',
							'theme_default'		=> 'h2',
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_heading' ), $template );
			
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
		 * Frontend Shortcode Handler
		 *
		 * @param array $atts array of attributes
		 * @param string $content text within enclosing form of shortcode element
		 * @param string $shortcodename the shortcode found, when == callback name
		 * @return string $output returns the modified html string
		 */
		function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
			$default = array_merge( avia_feature_image_slider::default_args(), $this->get_default_sc_args() );
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			$meta = aviaShortcodeTemplate::set_frontend_developer_heading_tag( $atts, $meta );
			
			
			if( isset( $atts['link'] ) )
			{
				$atts['link'] = explode(',', $atts['link'], 2 );
				$atts['taxonomy'] = $atts['link'][0];

				if( isset( $atts['link'][1] ) )
				{
					$atts['categories'] = $atts['link'][1];
				}
			}

			// $atts['class'] = $meta['el_class'];
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 
			extract( $atts );
			
			$output  	= '';
		    $class = '';
		    
		    
			$skipSecond = false;
			avia_sc_featureimage_slider::$slide_count++;
			
			$params['class'] = "avia-featureimage-slider-wrap main_color {$av_display_classes} {$meta['el_class']} {$class}";
			$params['open_structure'] = false;

			$params['custom_markup'] = $atts['custom_markup'] = $meta['custom_markup'];
			
			//we dont need a closing structure if the element is the first one or if a previous fullwidth element was displayed before
			if( $meta['index'] == 0 ) 
			{
				$params['close'] = false;
			}
			
			if( ! empty( $meta['siblings']['prev']['tag'] ) && in_array( $meta['siblings']['prev']['tag'], AviaBuilder::$full_el_no_section ) ) 
			{
				$params['close'] = false;
			}
			
			if( $meta['index'] > 0 ) 
			{
				$params['class'] .= ' slider-not-first';
			}
			
			$params['id'] = AviaHelper::save_string( $meta['custom_id_val'], '-', 'avia_feature_image_slider_' . avia_sc_featureimage_slider::$slide_count );
			$atts['el_id'] = ! empty( $meta['custom_el_id'] ) ? $meta['custom_el_id'] : ' id="avia_feature_image_slider_' . avia_sc_featureimage_slider::$slide_count . '" ';
			$atts['class'] = $meta['custom_class'];
			$atts['heading_tag'] = $meta['heading_tag'];
			$atts['heading_class'] = $meta['heading_class'];

			if( ShortcodeHelper::is_top_level() ) 
			{
				$atts['el_id'] = '';
				$atts['class'] = '';
			}
			
			$slider = new avia_feature_image_slider( $atts );
			$slider->query_entries();
			
			$slide_html = $slider->html();
			
			//if the element is nested within a section or a column dont create the section shortcode around it
			if( ! ShortcodeHelper::is_top_level() ) 
			{
				return $slide_html;
			}
			
			// $slide_html  = "<div class='container'>" . $slide_html . "</div>";
			
			$output .=  avia_new_section( $params );
			$output .= 	$slide_html;
			$output .= '</div>'; //close section
			
			
			//if the next tag is a section dont create a new section from this shortcode
			if( ! empty( $meta['siblings']['next']['tag'] ) && in_array( $meta['siblings']['next']['tag'],  AviaBuilder::$full_el ) )
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
				$output .= avia_new_section( array( 'close' => false, 'id' => 'after_full_slider_' . avia_sc_slider_full::$slide_count ) );
			}
			
			return $output;
		}

	}
}


if ( ! class_exists( 'avia_feature_image_slider' ) )
{
	class avia_feature_image_slider
	{
		/**
		 * @since < 4.0
		 * @var int 
		 */
		static public $slider = 0;
		
		/**
		 * @since < 4.0
		 * @var int 
		 */
		protected $slide_count;
		
		/**
		 * @since < 4.0
		 * @var array 
		 */
		protected $atts;
		
		/**
		 * @since < 4.0
		 * @var array 
		 */
		protected $entries;
		
		/**
		 * @since 4.5.6.1
		 * @var array 
		 */
		protected $screen_options;
		
		/**
		 *
		 * @since 4.7.6.4
		 * @var int 
		 */
		protected $current_page;


		/**
		 * @since < 4.0
		 * @param array $atts
		 */
		public function __construct( $atts = array() )
		{
			$this->slide_count = 0;
			$this->screen_options = AviaHelper::av_mobile_sizes( $atts ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 
			$this->current_page = 1;
			
			$this->atts = shortcode_atts( avia_feature_image_slider::default_args(), $atts, 'av_feature_image_slider' );

			if($this->atts['autoplay'] == 'no')   
			{
				$this->atts['autoplay'] = false;
			}
		}
		
		/**
		 * @since 4.5.6.1
		 */
		public function __destruct() 
		{
			unset( $this->atts );
			unset( $this->entries );
			unset( $this->screen_options );
		}
		
		/**
		 * Returns the default args
		 * 
		 * @since 4.8
		 * @return array
		 */
		static public function default_args() 
		{
			$defaults = array(
							'items'					=> '16',
							'taxonomy'				=> 'category',
							'post_type'				=> get_post_types(),
							'contents'				=> 'title',
							'preview_mode'			=> 'auto',
							'image_size'			=> 'portfolio',
							'autoplay'				=> 'no',
							'animation'				=> 'fade',
							'paginate'				=> 'no',
							'use_main_query_pagination' => 'no',
							'interval'				=> 5,
							'class'					=> '',
							'categories'			=> array(),
							'wc_prod_visible'		=> '',
							'wc_prod_hidden'		=> '',
							'wc_prod_featured'		=> '',
							'prod_order_by'			=> '',
							'prod_order'			=> '',
							'custom_query'			=> array(),
							'lightbox_size'			=> 'large',
							'offset'				=> 0,
							'bg_slider'				=> true,
							'keep_pading'			=> true,
							'custom_markup'			=> '',
							'slider_size'			=> '16:9',
							'control_layout'		=> '',
							'overlay_enable'		=> '',
							'overlay_opacity'		=> '',
							'overlay_color'			=> '',
							'overlay_pattern'		=> '',
							'overlay_custom_pattern' => '',
							'date_filter'			=> '',	
							'date_filter_start'		=> '',
							'date_filter_end'		=> '',
							'date_filter_format'	=> 'yy/mm/dd',		//	'yy/mm/dd' | 'dd-mm-yy'	| yyyymmdd
							'el_id'					=> '',
							'heading_tag'			=> '',
							'heading_class'			=> ''
					);
			
			return $defaults;
		}

		/**
		 * 
		 * @return string
		 */
		public function html()
		{
			$html 		= '';
			$counter 	= 0;
			$style   	= '';
			$extraClass = '';
			$style 		= '';
			avia_feature_image_slider::$slider++;
			
			if( $this->slide_count == 0 ) 
			{
				return $html;
			}
			
			if( ! empty( $this->atts['default-height'] ) )
			{
				$style = "style='padding-bottom: {{av-default-heightvar}}%;'";
				$extraClass .= ' av-default-height-applied';
			}
			
			if( strpos( $this->atts['slider_size'], ':') !== false)
			{
				$ratio = explode( ':', trim( $this->atts['slider_size'] ) );
				if( empty( $ratio[0] ) ) 
				{
					$ratio[0] = 16;
				}
				
				if( empty( $ratio[1] ) ) 
				{
					$ratio[1] = 9;
				}
				
				$final_ratio = ((int) $ratio[0] / (int) $ratio[1]);
				$def_height = 'padding-bottom:' . ( 100 / $final_ratio ) . '%';
				
			}
			else
			{
				$def_height  = (int) $this->atts['slider_size'];
				$def_height  = "height: {$def_height}px";
			}
			
			extract( $this->screen_options );
			
			$style = "style='{$def_height}'";
			
			if( ! empty( $this->atts['control_layout'] ) ) 
			{
				$extraClass .= ' ' . $this->atts['control_layout'];
			}
			
            $markup = avia_markup_helper( array( 'context' => 'image', 'echo' => false, 'custom_markup' => $this->atts['custom_markup'] ) );

			$data = AviaHelper::create_data_string( $this->atts );

			$html .= "<div {$this->atts['el_id']} {$data} class='avia-slideshow avia-featureimage-slideshow avia-animated-caption {$av_display_classes} avia-slideshow-" . avia_sc_featureimage_slider::$slide_count . " {$extraClass} avia-slideshow-{$this->atts['image_size']} {$this->atts['class']} avia-{$this->atts['animation']}-slider' {$markup}>";
			$html .=	"<ul class='avia-slideshow-inner avia-slideshow-fixed-height' {$style}>";

			$html .=		$this->default_slide();

			$html .=	'</ul>';

			if( $this->slide_count > 1 )
			{
				$html .= $this->slide_navigation_arrows();
				$html .= $this->slide_navigation_dots();
			}
			
			
			if( ! empty( $this->atts['caption_override'] ) ) 
			{
				$html .= $this->atts['caption_override'];
			}
			

			$html .= '</div>';
			
			if( ! empty( $this->atts['default-height'] ) )
			{
				$html = str_replace( '{{av-default-heightvar}}', $this->atts['default-height'], $html );
			}
			
			return $html;
		}
		
		/**
		 * Renders the usual slides. used when we didn't use sub-shorcodes to define the images but ids
		 * 
		 * @return string
		 */
		protected function default_slide()
		{
			$html = '';
			$counter = 0;
			
			extract( $this->screen_options );

            $markup_url = avia_markup_helper( array( 'context' => 'image_url', 'echo' => false, 'custom_markup' => $this->atts['custom_markup'] ) );

			foreach( $this->entries->posts as $index => $slide )
			{
				$counter ++;
				
				$thumb_id = get_post_thumbnail_id( $slide->ID );
				$slide_data = '';
				$slide_class = '';
				
				$img = wp_get_attachment_image_src( $thumb_id, $this->atts['image_size'] );
				$link = get_post_meta( $slide->ID , '_portfolio_custom_link', true ) != '' ? get_post_meta( $slide->ID , '_portfolio_custom_link_url', true ) : get_permalink( $slide->ID );
				$title = get_the_title( $slide->ID );

				$default_heading = ! empty( $this->atts['heading_tag'] ) ? $this->atts['heading_tag'] : 'h2';
				$args = array(
							'heading'		=> $default_heading,
							'extra_class'	=> $this->atts['heading_class']
						);

				$extra_args = array( $this, $slide, $index, __METHOD__ );

				/**
				 * @since 4.5.5
				 * @return array
				 */
				$args = apply_filters( 'avf_customize_heading_settings', $args, __CLASS__, $extra_args );

				$heading = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
				$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : $this->atts['heading_class'];

				$caption  = '';
				$caption .= '<div class="caption_fullwidth av-slideshow-caption caption_center">';
				$caption .=		'<div class="container caption_container">';
				$caption .=			'<div class="slideshow_caption">';
				$caption .=				'<div class="slideshow_inner_caption">';
				$caption .=					'<div class="slideshow_align_caption">';
				$caption .=						"<{$heading} class='avia-caption-title {$css} {$av_title_font_classes}'><a href='{$link}'>{$title}</a></{$heading}>";

				if( strpos( $this->atts['contents'], 'excerpt' ) !== false )
				{
					$excerpt = ! empty( $slide->post_excerpt ) ? $slide->post_excerpt : avia_backend_truncate( $slide->post_content, apply_filters( 'avf_feature_image_slider_excerpt_length', 320 ), apply_filters( 'avf_feature_image_slider_excerpt_delimiter', ' ' ), '…', true, '' );

					if( ! empty( $excerpt ) )
					{
						$caption .= '<div class="avia-caption-content ' . $av_font_classes . '" itemprop="description">';
						$caption .=		wpautop( $excerpt );
						$caption .= '</div>';
					}
				}

				if( strpos( $this->atts['contents'], 'read_more' ) !== false )
				{
					$caption .= ' <a href="' . $link . '" class="avia-slideshow-button avia-button avia-color-light " data-duration="800" data-easing="easeInOutQuad">' . __( 'Read more', 'avia_framework' ) . '</a>';
				}
				
				$caption .=					'</div>';
				$caption .=				'</div>';
				$caption .=			'</div>';
				$caption .=		'</div>';
				$caption .=		$this->create_overlay();
				$caption .=	'</div>';
					
				if( ! is_array( $img ) ) 
				{
					$slide_class .= ' av-no-image-slider';
				}
				else
				{
					$slide_data = "data-img-url='{$img[0]}'";
				}

				$html .= "<li {$slide_data} class='slide-{$counter} {$slide_class} slide-id-{$slide->ID}'>";
				$html .=	$caption;
				$html .= '</li>';
			}

			return $html;
		}
		
		/**
		 * 
		 * @return string
		 */
		protected function slide_navigation_dots()
		{
			$args = array(
						'total_entries'		=> $this->slide_count,
						'container_entries'	=> 1,
						'context'			=> get_class(),
						'params'			=> $this->atts
					);
			
			
			return aviaFrontTemplates::slide_navigation_dots( $args );
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
						'params'		=> $this->atts
					);
			
			return aviaFrontTemplates::slide_navigation_arrows( $args );
		}
		
		/**
		 * 
		 * @return string
		 */
		protected function create_overlay()
		{
			extract( $this->atts );
			
			/*check/create overlay*/
			$overlay = '';
			
			if( ! empty( $overlay_enable ) )
			{
				$overlay_src = '';
				$overlay = "opacity: {$overlay_opacity}; ";
				
				if( ! empty( $overlay_color ) ) 
				{
					$overlay .= "background-color: {$overlay_color}; ";
				}
				
				if( ! empty( $overlay_pattern ) )
				{
					if( $overlay_pattern == 'custom' )
					{
						$overlay_src = $overlay_custom_pattern;
					}
					else
					{
						$overlay_src = str_replace( '{{AVIA_BASE_URL}}', AVIA_BASE_URL, $overlay_pattern );
					}
				}
				
				if( ! empty( $overlay_src ) ) 
				{
					$overlay .= "background-image: url({$overlay_src}); background-repeat: repeat;";
				}
				
				$overlay = "<div class='av-section-color-overlay' style='{$overlay}'></div>";
			}
			
			return $overlay;
		}

		/**
		 * fetch new entries
		 * 
		 * @since < 4.0
		 * @param array $params
		 */
		public function query_entries( $params = array() )
		{
			global $avia_config;

			if( empty( $params ) ) 
			{
				$params = $this->atts;
			}

			if( empty( $params['custom_query'] ) )
            {
				$query = array();

				if( ! empty( $params['categories'] ) )
				{
					//get the portfolio categories
					$terms 	= explode( ',', $params['categories'] );
				}

				if( $params['use_main_query_pagination'] == 'yes' )
				{
					$this->current_page = ( $params['paginate'] != 'no' ) ? avia_get_current_pagination_number() : 1;
				}
				else
				{
					$this->current_page = ( $params['paginate'] != 'no' ) ? avia_get_current_pagination_number( 'avia-element-paging' ) : 1;
				}
				
				
				//if we find no terms for the taxonomy fetch all taxonomy terms
				if( empty( $terms[0] ) || is_null( $terms[0] ) || $terms[0] === 'null' )
				{
					
					$term_args = array( 
								'taxonomy'		=> $params['taxonomy'],
								'hide_empty'	=> true
							);
					/**
					 * To display private posts you need to set 'hide_empty' to false, 
					 * otherwise a category with ONLY private posts will not be returned !!
					 * 
					 * You also need to add post_status 'private' to the query params with filter avia_feature_image_slider_query.
					 * 
					 * @since 4.4.2
					 * @added_by Günter
					 * @param array $term_args 
					 * @param array $params 
					 * @return array
					 */
					$term_args = apply_filters( 'avf_av_feature_image_slider_term_args', $term_args, $params );

					$allTax = AviaHelper::get_terms( $term_args );
					
					$terms = array();
					foreach( $allTax as $tax )
					{
						$terms[] = $tax->term_id;
					}
				}

				if( $params['offset'] == 'no_duplicates' )
				{
					$params['offset'] = 0;
					$no_duplicates = true;
				}

				if( $params['offset'] == 'enforce_duplicates' )
				{
					$params['offset'] = 0;
					$no_duplicates = false;
				}

				if( empty($params['post_type'] ) ) 
				{
					$params['post_type'] = get_post_types();
				}
				
				if( is_string( $params['post_type'] ) ) 
				{
					$params['post_type'] = explode( ',', $params['post_type'] );
				}

				$orderby = 'date';
				$order = 'DESC';
				
				$date_query = array();
				if( 'date_filter' == $params['date_filter'] )
				{
					$date_query = AviaHelper::add_date_query( $date_query, $params['date_filter_start'], $params['date_filter_end'], $params['date_filter_format'] );
				}
				
				// Meta query - replaced by Tax query in WC 3.0.0
				$meta_query = array();
				$tax_query = array();
				$ordering_args = array();

				// check if taxonomy are set to product or product attributes
				$tax = get_taxonomy( $params['taxonomy'] );
				
				if( class_exists( 'WooCommerce' ) && is_object( $tax ) && isset( $tax->object_type ) && in_array( 'product', (array) $tax->object_type ) )
				{
					$avia_config['woocommerce']['disable_sorting_options'] = true;
					
					avia_wc_set_out_of_stock_query_params( $meta_query, $tax_query, $params['wc_prod_visible'] );
					avia_wc_set_hidden_prod_query_params( $meta_query, $tax_query, $params['wc_prod_hidden'] );
					avia_wc_set_featured_prod_query_params( $meta_query, $tax_query, $params['wc_prod_featured'] );
					
						//	sets filter hooks !!
					$ordering_args = avia_wc_get_product_query_order_args( $params['prod_order_by'], $params['prod_order'] );
							
					$orderby = $ordering_args['orderby'];
					$order = $ordering_args['order'];
				}	

				if( ! empty( $terms ) )
				{
					$tax_query[] =  array(
										'taxonomy' 	=>	$params['taxonomy'],
										'field' 	=>	'id',
										'terms' 	=>	$terms,
										'operator' 	=>	'IN'
								);
				}				
				
				$query = array(	'orderby'		=>	$orderby,
								'order'			=>	$order,
								'paged'			=>	$this->current_page,
								'post_type'		=>	$params['post_type'],
//								'post_status'	=>	'publish',
								'offset'		=>	$params['offset'],
								'posts_per_page' =>	$params['items'],
								'post__not_in'	=>	( ! empty( $no_duplicates ) ) ? $avia_config['posts_on_current_page'] : array(),
								'meta_query'	=>	$meta_query,
								'tax_query'		=>	$tax_query,
								'date_query'	=> $date_query,
							);
				
				if ( ! empty( $ordering_args['meta_key'] ) ) 
				{
					$query['meta_key'] = $ordering_args['meta_key'];
				}
			}
			else
			{
				$query = $params['custom_query'];
			}

			/**
			 * 
			 * @since < 4.0
			 * @param array $query
			 * @param array $params
			 * @return array
			 */
			$query = apply_filters( 'avia_feature_image_slider_query', $query, $params );

			$this->entries = new WP_Query( $query );
			$this->slide_count = $this->entries->post_count;
			
		    // store the queried post ids in
            if( ( $this->entries->post_count > 0 ) && $params['offset'] != 'enforce_duplicates' )
            {
				foreach( $this->entries->posts as $entry )
                {
                    $avia_config['posts_on_current_page'][] = $entry->ID;
                }
            }
			
			if( function_exists( 'WC' ) )
			{
				avia_wc_clear_catalog_ordering_args_filters();
				$avia_config['woocommerce']['disable_sorting_options'] = false;
			}
			
		}
	}
}
