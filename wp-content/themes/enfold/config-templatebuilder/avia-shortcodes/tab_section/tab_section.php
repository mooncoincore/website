<?php
/**
 * Tab Section
 * 
 * Add a fullwidth section with tabs that can contain columns and other elements
 */

 // Don't load directly
if ( ! defined( 'ABSPATH' ) ) { die( '-1' ); }



if ( ! class_exists( 'avia_sc_tab_section' ) )
{
	//load the subsection shhortcode
	include_once( 'tab_sub_section.php' );
	
	class avia_sc_tab_section extends aviaShortcodeTemplate
	{

		static $count = 0;
		static $tab = 0;
		static $admin_active = 1;
		static $tab_titles = array();
		static $tab_icons = array();
		static $tab_images = array();
		static $tab_atts = array();

			
		/**
		 * Create the config array for the tab section
		 */
		function shortcode_insert_button()
		{
			$this->config['version']			= '1.0';
			$this->config['is_fullwidth']		= 'yes';
			$this->config['type']				= 'layout';
			$this->config['self_closing']		= 'no';
			$this->config['contains_text']		= 'no';
			$this->config['layout_children']	= array( 'av_tab_sub_section' );

			$this->config['name']				= __( 'Tab Section', 'avia_framework' );
			$this->config['icon']				= AviaBuilder::$path['imagesURL'] . 'sc-tabsection.png';
			$this->config['tab']				= __( 'Layout Elements', 'avia_framework' );
			$this->config['order']				= 13;
			$this->config['shortcode']			= 'av_tab_section';
			$this->config['html_renderer']		= false;
			$this->config['tinyMCE']			= array( 'disable' => 'true' );
			$this->config['tooltip']			= __( 'Add a fullwidth section with tabs that can contain columns and other elements', 'avia_framework' );
			$this->config['drag-level']			= 1;
			$this->config['drop-level']			= 100;
			$this->config['disabling_allowed']	= true;

			$this->config['id_name']			= 'id';
			$this->config['id_show']			= 'always';				//	we use original code - not $meta
			$this->config['aria_label']			= 'yes';
		}
			
		function admin_assets()
		{
			$ver = AviaBuilder::VERSION;
			
			wp_register_script( 'avia_tab_section_js', AviaBuilder::$path['assetsURL'] . 'js/avia-tab-section.js', array( 'avia_builder_js', 'avia_modal_js' ), $ver, true );
			Avia_Builder()->add_registered_admin_script( 'avia_tab_section_js' );
		}
			
			
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-tabsection', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/tab_section/tab_section.css', array( 'avia-layout' ), false );
			//load js
			wp_enqueue_script( 'avia-module-tabsection', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/tab_section/tab_section.js', array( 'avia-shortcodes' ), false, true );
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
						'name'  => __( 'Layout' , 'avia_framework' ),
						'nodescription' => true
					),
				
					array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'layout_general' )
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
													$this->popup_key( 'styling_padding' ),
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
								'template_id'	=> 'screen_options_toggle'
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
						'id'	=> 'av_element_hidden_in_editor',
						'type'	=> 'hidden',
						'std'	=> '0'
					),
				            
				array(	
						'id'	=> 'av_admin_tab_active',
						'type'	=> 'hidden',
						'std'	=> '1'
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
			 * Layout Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Initial Open', 'avia_framework' ),
							'desc' 	=> __( 'Enter the Number of the Tab that should be open initially.', 'avia_framework' ),
							'id' 	=> 'initial',
							'type' 	=> 'input',
							'std' 	=> '1',							
						),
				
						array(
							'name' 	=> __( 'Tab Position', 'avia_framework' ),
							'desc'  => __( 'Define the position of the tab buttons', 'avia_framework' ),
							'id' 	=> 'tab_pos',
							'type' 	=> 'select',
							'std' 	=> 'av-tab-above-content',
							'subtype'	=> array(   
												__( 'Display Tabs above content', 'avia_framework' )	=> 'av-tab-above-content',
												__( 'Display Tabs below content', 'avia_framework' )	=> 'av-tab-below-content',
											)
						),
				
						array(
							'name'		=> __( 'Content height', 'avia_framework' ),
							'id'		=> 'content_height',
							'desc'		=> __( 'Define the behaviour for the size of the content tabs when switching between the tabs.', 'avia_framework' ),
							'type'		=> 'select',
							'std'		=> '',
							'required'	=> array( 'tab_pos', 'contains', 'av-tab-above-content' ),
							'subtype'	=> array(  	
												__( 'Same height for all tabs', 'avia_framework' )	=> '', 
												__( 'Auto adjust to content', 'avia_framework' )	=> 'av-tab-content-auto'
											)
						),
				
						array(
							'name' 	=> __( 'Tab Buttons Out Of Screen Behaviour', 'avia_framework' ),
							'desc'  => __( 'Select to display arrows in tab button area if tab buttons are out of screens viewport to draw visitors attention that there are more buttons available.', 'avia_framework' ),
							'id' 	=> 'tab_arrows',
							'type' 	=> 'select',
							'std' 	=> 'av-tab-above-content',
							'subtype'	=> array(   
												__( 'Display arrows', 'avia_framework' )		=> '',
												__( 'Do not display arrows', 'avia_framework' )	=> 'av-tab-arrows-hide',
											)
						)
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'layout_general' ), $c );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Tab Padding', 'avia_framework' ),
							'id' 	=> 'tab_padding',
							'desc'  => __( 'Define the tab titles top and bottom padding (only works if no icon is displayed at the top off the tab title)', 'avia_framework' ),
							'type' 	=> 'select',
							'std' 	=> 'default',
							'subtype'	=> array(  	
												__( 'No Padding', 'avia_framework' )		=> 'none', 
												__( 'Small Padding', 'avia_framework' )		=> 'small',
												__( 'Default Padding', 'avia_framework' )	=> 'default',
												__( 'Large Padding', 'avia_framework' )		=> 'large',
											)
						),
				
						array(
							'name' 	=> __( 'Content Padding', 'avia_framework' ),
							'desc'  => __( 'Define the sections top and bottom padding', 'avia_framework' ),
							'id' 	=> 'padding',
							'type' 	=> 'select',
							'std' 	=> 'default',
							'subtype'	=> array(   
												__( 'No Padding', 'avia_framework' )		=> 'no-padding',
												__( 'Small Padding', 'avia_framework' )		=> 'small',
												__( 'Default Padding', 'avia_framework' )	=> 'default',
												__( 'Large Padding', 'avia_framework' )		=> 'large',
												__( 'Huge Padding', 'avia_framework' )		=> 'huge',
											)
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Padding', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_padding' ), $template );
			
			$c = array(
						array(
							'name'  => __( 'Tab Title Background Color', 'avia_framework' ),
							'desc'  => __( 'Here you can set the background color of the tab title bar. Enter no value if you want to use the standard color.', 'avia_framework' ),
							'id'    => 'bg_color',
							'type'  => 'colorpicker',
							'rgba' 	=> true,
							'std' 	=> ''
						),	
                    
						array(
							'name'  => __( 'Inactive Tab Font Color', 'avia_framework' ),
							'desc'  => __( 'Here you can set the text color of the tab. Enter no value if you want to use the standard font color.', 'avia_framework' ),
							'id'    => 'color',
							'type'  => 'colorpicker',
							'rgba' 	=> true,
							'std' 	=> ''
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
			
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Content transition', 'avia_framework' ),
							'desc'  => __( 'Define the transition between tab content', 'avia_framework' ),
							'id' 	=> 'transition',
							'type' 	=> 'select',
							'std' 	=> 'av-tab-no-transition',
							'subtype'	=> array(   
												__( 'None', 'avia_framework' )	=> 'av-tab-no-transition',
												__( 'Slide', 'avia_framework' )	=> 'av-tab-slide-transition',
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
				
			extract( $params );

			avia_sc_tab_section::$tab = 0;
			avia_sc_tab_section::$tab_titles = array();
			avia_sc_tab_section::$admin_active = ! empty( $args['av_admin_tab_active'] ) ? $args['av_admin_tab_active'] : 1;


			$name = $this->config['shortcode'];
			$data['shortcodehandler'] 	= $this->config['shortcode'];
			$data['modal_title'] 		= $this->config['name'];
			$data['modal_ajax_hook'] 	= $this->config['shortcode'];
			$data['dragdrop-level'] 	= $this->config['drag-level'];
			$data['allowed-shortcodes']	= $this->config['shortcode'];

			if( ! empty( $this->config['modal_on_load'] ) )
			{
				$data['modal_on_load'] 	= $this->config['modal_on_load'];
			}

			$dataString  = AviaHelper::create_data_string( $data );
				
				
			if( $content )
			{
				$final_content = $this->builder->do_shortcode_backend( $content );
				$text_area = ShortcodeHelper::create_shortcode_by_array( $name, $content, $args );
			}
			else
			{
				$tab = new avia_sc_tab_sub_section( $this->builder );
				$params = array( 
								'content'	=> '', 
								'args'		=> array(), 
								'data'		=> ''
							);
				
				$final_content  = '';
				$final_content .= $tab->editor_element( $params );
				$final_content .= $tab->editor_element( $params );
				$final_content .= $tab->editor_element( $params );
				$final_content .= $tab->editor_element( $params );
				$text_area = ShortcodeHelper::create_shortcode_by_array( $name, '[av_tab_sub_section][/av_tab_sub_section][av_tab_sub_section][/av_tab_sub_section][av_tab_sub_section][/av_tab_sub_section][av_tab_sub_section][/av_tab_sub_section]', $args );

			}
				
			$title_id = ! empty( $args['id'] ) ? ': ' . ucfirst( $args['id'] ) : '';
			$hidden_el_active = ! empty( $args['av_element_hidden_in_editor'] ) ? 'av-layout-element-closed' : '';



			$output  = "<div class='avia_tab_section {$hidden_el_active} avia_layout_section avia_pop_class avia-no-visual-updates {$name} av_drag' {$dataString}>";

			$output .=		"<div class='avia_sorthandle menu-item-handle'>";
			$output .=			"<span class='avia-element-title'>{$this->config['name']}<span class='avia-element-title-id'>{$title_id}</span></span>";
			$output .=			"<a class='avia-delete'  href='#delete' title='" . __( 'Delete Tab Section', 'avia_framework' ) . "'>x</a>";
			$output .=			"<a class='avia-toggle-visibility'  href='#toggle' title='" . __( 'Show/Hide Tab Section', 'avia_framework' ) . "'></a>";

			if( ! empty( $this->config['popup_editor'] ) )
			{
				$output .=		"<a class='avia-edit-element'  href='#edit-element' title='" . __( 'Edit Tab Section', 'avia_framework' ) . "'>" . __( 'edit', 'avia_framework' ) . '</a>';
			}
			
			$output .=			"<a class='avia-save-element'  href='#save-element' title='" . __( 'Save Element as Template', 'avia_framework' ) . "'>+</a>";
			$output .=			"<a class='avia-clone'  href='#clone' title='" . __( 'Clone Tab Section', 'avia_framework' ) . "' >" . __( 'Clone Tab Section', 'avia_framework' ) . '</a>';
			$output .=		'</div>';
					
			$output .=		"<div class='avia_inner_shortcode avia_connect_sort av_drop' data-dragdrop-level='{$this->config['drop-level']}'>";

			$output  .=			"<div class='avia_tab_section_titles'>";
			
			//create tabs
			for( $i = 1; $i <= avia_sc_tab_section::$tab; $i ++ )
			{
				$active_tab = $i == avia_sc_tab_section::$admin_active ? 'av-admin-section-tab-active' : '';
				$tab_title = isset( avia_sc_tab_section::$tab_titles[$i] ) ? avia_sc_tab_section::$tab_titles[$i] : '';

				$output  .=			"<a href='#' data-av-tab-section-title='{$i}' class='av-admin-section-tab {$active_tab}'><span class='av-admin-section-tab-move-handle'></span><span class='av-tab-title-text-wrap-full'>" . __( 'Tab', 'avia_framework' ) . " <span class='av-tab-nr'>{$i}</span><span class='av-tab-custom-title'>{$tab_title}</span></span></a>";
			}
				
			//$output .=			"<a class='avia-clone-tab avia-add'  href='#clone-tab' title='".__('Clone Last Tab', 'avia_framework' )."'>".__('Clone Last Tab', 'avia_framework' )."</a>";
			$output .=				"<a class='avia-add-tab avia-add'  href='#add-tab' title='" . __( 'Add Tab', 'avia_framework' ) . "'>" . __( 'Add Tab', 'avia_framework' ) . '</a>';
			$output .=			'</div>';

			$output .=			"<textarea data-name='text-shortcode' cols='20' rows='4'>{$text_area}</textarea>";
			$output .=			$final_content;

			$output .=		'</div>';

			$output .=		"<a class='avia-layout-element-hidden' href='#'>" . __( 'Tab Section content hidden. Click here to show it', 'avia_framework' ) . '</a>';

			$output .= '</div>';

			return $output;
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
						'initial'			=> 1,
						'tab_pos'			=> 'av-tab-above-content',
						'content_height'	=> '',
						'tab_arrows'		=> '',
						'padding'			=> 'default',
						'tab_padding'		=> 'default', 
						'bg_color'			=> '', 
						'color'				=> '', 
						'transition'		=> 'av-tab-no-transition', 
						'id'				=> ''
					);
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			extract( $atts );
			
			
			avia_sc_tab_section::$tab = 0;
			avia_sc_tab_section::$tab_titles = array();
			avia_sc_tab_section::$tab_icons = array();
			avia_sc_tab_section::$tab_images = array();
			avia_sc_tab_section::$tab_atts = array();
			avia_sc_tab_section::$count++;

			
			
			
			$output = '';
				
			$params['class'] = "av-tab-section-container entry-content-wrapper main_color {$transition} {$content_height} {$av_display_classes} {$tab_pos} {$meta['el_class']}";
			$params['open_structure'] = false; 
			$params['id'] = AviaHelper::save_string( $id, '-', 'av-tab-section-' . avia_sc_tab_section::$count );
			$params['custom_markup'] = $meta['custom_markup'];
			$params['aria_label'] = $meta['aria_label'];
				
				
			//we dont need a closing structure if the element is the first one or if a previous fullwidth element was displayed before
			if( isset( $meta['index'] ) && $meta['index'] == 0 ) 
			{
				$params['close'] = false;
			}
			if( ! empty( $meta['siblings']['prev']['tag'] ) && in_array( $meta['siblings']['prev']['tag'], AviaBuilder::$full_el_no_section ) ) 
			{
				$params['close'] = false;
			}

			if( isset( $meta['index'] ) && $meta['index'] > 0 ) 
			{
				$params['class'] .= ' submenu-not-first';
			}

			avia_sc_tab_sub_section::$attr = $atts;
			$final_content = ShortcodeHelper::avia_remove_autop( $content,true ) ;

			$width = avia_sc_tab_section::$tab * 100;
			$tabs = '';
			$tab_style = '';
			$custom_tab_color = '';
			$arrow = "<span class='av-tab-arrow-container'><span></span></span>";
			
			if( $atts['initial'] <= 0 )
			{
				$atts['initial'] = 1;
			}
			else if( $atts['initial'] > avia_sc_tab_section::$tab ) 
			{
				$atts['initial'] = avia_sc_tab_section::$tab;
			}

			for( $i = 1; $i <= avia_sc_tab_section::$tab; $i ++ )
			{
				$icon 	= ! empty( avia_sc_tab_section::$tab_icons[ $i ] ) ? avia_sc_tab_section::$tab_icons[ $i ] : '';
				$image  = ! empty( avia_sc_tab_section::$tab_images[ $i ] ) ? avia_sc_tab_section::$tab_images[ $i ] : '';

				$extraClass  = '';
				$extraClass .= ! empty( $icon ) ? 'av-tab-with-icon ' : 'av-tab-no-icon ';
				$extraClass .= ! empty( $image ) ? 'av-tab-with-image noHover ' : 'av-tab-no-image ';
				$extraClass .= avia_sc_tab_section::$tab_atts[ $i ]['tab_image_style'];

				/**
				 * Bugfix: Set no-scroll to avoid auto smooth scroll when initialising tab section and multiple tab sections are on a page - removed in js.
				 */
				$active_tab = $i == $atts['initial'] ? 'av-active-tab-title no-scroll' : '';					

				$tab_title = ! empty( avia_sc_tab_section::$tab_titles[ $i ] ) ? avia_sc_tab_section::$tab_titles[ $i ] : '';
				if( $tab_title == '' && empty( $image ) && empty( $icon ) )
				{
					$tab_title = __( 'Tab', 'avia_framework' ) . ' ' . $i;
				}

				$tab_link = AviaHelper::valid_href( $tab_title, '-', 'av-tab-section-' . avia_sc_tab_section::$count . '-' . $i );
				$tab_id = 'av-tab-section-' . avia_sc_tab_section::$count . '-' . $i;
				
				/**
				 * layout is broken since adding aria-controls $tab_id with 4.7.6
				 * Fixes problem with non latin letters like greek
				 */
				if( $tab_id == $tab_link )
				{
					$tab_link .= '-link';
				}
				
				if( $tab_title == '' )
				{
					$extraClass .= ' av-tab-without-text ';
				}
				
				/**
				 * @since 4.8
				 * @param string $tab_link
				 * @param string $tab_title
				 * @return string
				 */
				$tab_link = apply_filters( 'avf_tab_section_link_hash', $tab_link, $tab_title );
				
				$tabs .= "<a href='#{$tab_link}' data-av-tab-section-title='{$i}' class='av-section-tab-title {$active_tab} {$extraClass} ' role='tab' tabindex='0' aria-controls='{$tab_id}'>{$icon}{$image}<span class='av-outer-tab-title'><span class='av-inner-tab-title'>{$tab_title}</span></span>{$arrow}</a>";
			}

			if( ! empty( $atts['bg_color'] ) )
			{
				$tab_style .= AviaHelper::style_string( $atts, 'bg_color', 'background-color' );
			}
				
			if( ! empty( $atts['color'] ) )
			{
				$tab_style .= AviaHelper::style_string( $atts, 'color', 'color' );
				$custom_tab_color = 'av-custom-tab-color';
			}

			if( ! empty( $tab_style ) ) 
			{
				$tab_style = "style='{$tab_style}'";
			}
			
			$tabs_final =  "<div class='av-tab-section-tab-title-container avia-tab-title-padding-{$tab_padding} {$custom_tab_color}' {$tab_style} role='tablist'>{$tabs}</div>";
			$tabs_final .= $this->slide_navigation_arrows( $atts );
			
			$output .=  avia_new_section( $params );
			$output .=  "<div class='av-tab-section-outer-container'>";
			
			if( $tab_pos == 'av-tab-above-content' ) 
			{
				$output .=  $tabs_final;
			}

			$output .= "<div class='av-tab-section-inner-container avia-section-{$padding}' style='width:{$width}vw; left:" . ( ( $atts['initial'] -1 ) * -100 ) . "%;'>";

			$output .=		"<span class='av_prev_tab_section av_tab_navigation'></span><span class='av_next_tab_section av_tab_navigation'></span>";
			$output .=		$final_content;
			$output .= '</div>';
				
			if( $tab_pos == 'av-tab-below-content' ) 
			{
				$output .=  $tabs_final;
			}

			$output .=  '</div>';
			$output .= avia_section_after_element_content( $meta , 'after_submenu', false);

			// added to fix https://kriesi.at/support/topic/footer-disseapearing/#post-427764
			avia_sc_section::$close_overlay = '';


			return $output;
		}
		
		/**
		 * Create arrows to scroll slides
		 * 
		 * @since 4.8.3			reroute to aviaFrontTemplates
		 * @param array $atts
		 * @return string
		 */
		protected function slide_navigation_arrows( array $atts )
		{
			if( $atts['tab_arrows'] != '' )
			{
				return '';
			}
				
			$args = array(
						'class_main'	=> 'avia-slideshow-arrows av-tabsection-arrow',
						'class_prev'	=> 'av_prev_tab_section av-tab-section-slide',
						'class_next'	=> 'av_next_tab_section av-tab-section-slide',
						'context'		=> get_class(),
						'params'		=> $atts
					);
			
			return aviaFrontTemplates::slide_navigation_arrows( $args );
		}
	}
}

