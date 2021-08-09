<?php
/**
 * Product List
 *
 * Display a List of Product Entries
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( 'woocommerce' ) )
{
	add_shortcode('av_productlist', 'avia_please_install_woo');
	return;
}

if ( ! class_exists( 'avia_sc_productlist' ) )
{
	class avia_sc_productlist extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'yes';
			$this->config['base_element']	= 'yes';
			
			$this->config['name']			= __( 'Product List', 'avia_framework' );
			$this->config['tab']			= __( 'Plugin Additions', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-catalogue.png';
			$this->config['order']			= 20;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_productlist';
			$this->config['tooltip']		= __( 'Display a List of Product Entries', 'avia_framework' );
			$this->config['drag-level']		= 3;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
		}
		
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-catalogue', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/catalogue/catalogue.css', array( 'avia-layout' ), false );
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
													$this->popup_key( 'styling_columns' ),
													$this->popup_key( 'styling_pagination' )
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
							'name' 	=> __( 'Which Entries?', 'avia_framework' ),
							'desc' 	=> __( 'Select which entries should be displayed by selecting a taxonomy', 'avia_framework' ),
							'id' 	=> 'categories',
							'type' 	=> 'select',
							'taxonomy'	=> 'product_cat',
							'subtype'	=> 'cat',
							'multiple'	=> 6,
							'std'		=> '',
							'lockable'	=> true
						),
				
						array(
							'name' 	=> __( 'Product Images', 'avia_framework' ),
							'desc' 	=> __( 'Should product image be displayed?', 'avia_framework' ),
							'id' 	=> 'show_images',
							'type' 	=> 'select',
							'std' 	=> 'yes',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'yes', 'avia_framework' )	=> 'yes',
												__( 'no', 'avia_framework' )	=> 'no'
											)
						)
				);
			
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
							'template_id' 	=> 'wc_options_products',
							'sort_dropdown'	=> true,
							'lockable'		=> true
						),
				
						array(
							'name'		=> __( 'Offset Number', 'avia_framework' ),
							'desc'		=> __( 'The offset determines where the query begins pulling products. Useful if you want to remove a certain number of products because you already query them with another product grid. Attention: Use this option only if the product sorting of the product grids match and do not allow the user to pick the sort order!', 'avia_framework' ),
							'id'		=> 'offset',
							'type'		=> 'select',
							'std'		=> '0',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 1, 100, 1, array( __( 'Deactivate offset', 'avia_framework' ) => '0', __( 'Do not allow duplicate posts on the entire page (set offset automatically)', 'avia_framework' ) => 'no_duplicates' ) )
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
							'name' 	=> __( 'Columns', 'avia_framework' ),
							'desc' 	=> __( 'How many columns should be displayed?', 'avia_framework' ),
							'id' 	=> 'columns',
							'type' 	=> 'select',
							'std' 	=> '1',
							'lockable'	=> true,
							'subtype'	=> array(	
												__( '1 Column', 'avia_framework' )	=> '1',
												__( '2 Columns', 'avia_framework' )	=> '2',
												__( '3 Columns', 'avia_framework' )	=> '3',
												__( '4 Columns', 'avia_framework' )	=> '4',
												
											)
						)

				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Columns', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_columns' ), $template );
			
			$c = array(
						array(
							'name' 	=> __( 'Entry Number', 'avia_framework' ),
							'desc' 	=> __( 'How many items should be displayed?', 'avia_framework' ),
							'id' 	=> 'items',
							'type' 	=> 'select',
							'std' 	=> '9',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 1, 100, 1, array( 'All' => '-1' ) ) 
						),
				
						array(
							'name' 	=> __( 'Pagination', 'avia_framework' ),
							'desc' 	=> __( 'Should a pagination be displayed?', 'avia_framework' ),
							'id' 	=> 'paginate',
							'type' 	=> 'select',
							'std' 	=> 'yes',
							'lockable'	=> true,
							'required'	=> array( 'items', 'not', '-1' ),
							'subtype'	=> array(
												__( 'yes', 'avia_framework' )	=> 'yes',
												__( 'no', 'avia_framework' )	=> 'no'
											)
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Pagination', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_pagination' ), $template );
			
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Item Links', 'avia_framework' ),
							'desc' 	=> __( 'What should happen if a user clicks the product link?', 'avia_framework' ),
							'id' 	=> 'link_behavior',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Show single product page', 'avia_framework' ) => '',
												__( 'Add item to cart (if item has variations the single product page will be opened)', 'avia_framework' ) => 'add_cart'
											)
						)
								
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Link Behaviour', 'avia_framework' ),
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
			global $avia_config;
			
			//	fix for seo plugins which execute the do_shortcode() function before the WooCommerce plugin is loaded
			if( ! function_exists( 'WC' ) || ! WC() instanceof WooCommerce || ! is_object( WC()->query ) )
			{
				return '';
			}
			
			$default = avia_product_slider::get_defaults();
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			
			$screen_sizes = AviaHelper::av_mobile_sizes( $atts );
			
			$atts['el_id'] = $meta['custom_el_id'];
			$atts['class'] = $meta['el_class'];
			$atts['custom_class'] = $meta['custom_class'];
			$atts['autoplay'] = 'no';
			$atts['type'] = 'list';
			
			$atts = array_merge( $atts, $screen_sizes );
			
			$slider = new avia_product_slider( $atts );
			$slider->query_entries();
			
				//	force to ignore WC default setting - see hooked function avia_wc_product_is_visible
			$avia_config['woocommerce']['catalog_product_visibility'] = 'show_all';
			$html = $slider->html_list();
			
				//	reset again
			$avia_config['woocommerce']['catalog_product_visibility'] = 'use_default';
			
			return $html;
		}
	}
}



