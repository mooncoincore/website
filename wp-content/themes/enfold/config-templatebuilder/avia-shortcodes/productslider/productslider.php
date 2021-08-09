<?php
/**
 * Product Slider
 *
 * Display a Slideshow of Product Entries
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( 'woocommerce' ) )
{
	add_shortcode( 'av_productslider', 'avia_please_install_woo' );
	return;
}


if ( ! class_exists( 'avia_sc_productslider' ) )
{
	class avia_sc_productslider extends aviaShortcodeTemplate
	{

		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'yes';
			$this->config['base_element']	= 'yes';
			
			$this->config['name']			= __( 'Product Slider', 'avia_framework' );
			$this->config['tab']			= __( 'Plugin Additions', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-postslider.png';
			$this->config['order']			= 30;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_productslider';
			$this->config['tooltip']		= __( 'Display a Slideshow of Product Entries', 'avia_framework' );
			$this->config['drag-level']		= 3;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
		}
		
		
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-slideshow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow.css', array( 'avia-layout' ), false );
			wp_enqueue_style( 'avia-module-postslider', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/postslider/postslider.css', array( 'avia-module-slideshow' ), false );
			wp_enqueue_style( 'avia-module-catalogue', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/catalogue/catalogue.css', array( 'avia-layout' ), false );
			
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
													$this->popup_key( 'styling_columns' ),
													$this->popup_key( 'styling_image_size' ),
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
							'lockable'		=> true
						),
				
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
							'name' 	=> __( 'Offset Number', 'avia_framework' ),
							'desc' 	=> __( 'The offset determines where the query begins pulling products. Useful if you want to remove a certain number of products because you already query them with another product slider. Attention: Use this option only if the product sorting of the product sliders match!', 'avia_framework' ),
							'id' 	=> 'offset',
							'type' 	=> 'select',
							'std' 	=> '0',
							'lockable'	=> true,
							'subtype'	=> AviaHtmlHelper::number_array( 1, 100, 1, array( __( 'Deactivate offset', 'avia_framework') => '0', __( 'Do not allow duplicate posts on the entire page (set offset automatically)', 'avia_framework' ) => 'no_duplicates' ) ) ),

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
							'std' 	=> '3',
							'lockable'	=> true,
							'subtype'	=> array(	
												__( '2 Columns', 'avia_framework' )	=> '2',
												__( '3 Columns', 'avia_framework' )	=> '3',
												__( '4 Columns', 'avia_framework' )	=> '4',
												__( '5 Columns', 'avia_framework' )	=> '5',
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
			
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'wc_image_size_toggle',
								'lockable'		=> true
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_image_size' ), $template );

			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
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
							'required'	=> array( 'autoplay', 'equals', 'yes' ),
							'subtype'	=> array( '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10', '15'=>'15', '20'=>'20', '30'=>'30', '40'=>'40', '60'=>'60', '100'=>'100' )
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
			
			//fix for seo plugins which execute the do_shortcode() function before the WooCommerce plugin is loaded
			if( ! function_exists( 'WC' ) || ! WC() instanceof WooCommerce || ! is_object( WC()->query ) )
			{
				return '';
			}
			
			$default = avia_product_slider::get_defaults();
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			
			$screen_sizes = AviaHelper::av_mobile_sizes( $atts );
			
			$atts['class'] = $meta['el_class'];
			$atts['el_id'] = $meta['custom_el_id'];
			
			$atts = array_merge( $atts, $screen_sizes );
			
			$slider = new avia_product_slider( $atts );
			$slider->query_entries();
			
				//	force to ignore WC default setting - see hooked function avia_wc_product_is_visible
			$avia_config['woocommerce']['catalog_product_visibility'] = 'show_all';
			$html = $slider->html();
			
				//	reset again
			$avia_config['woocommerce']['catalog_product_visibility'] = 'use_default';
			
			return $html;
		}

	}
}


if ( ! class_exists( 'avia_product_slider' ) )
{
	class avia_product_slider
	{
		/**
		 * 
		 * @var int 
		 */
		static protected $slide = 0;
		
		/**
		 *
		 * @var array 
		 */
		protected $atts;
		
		/**
		 *
		 * @since 4.7.6.4
		 * @var int 
		 */
		protected $current_page;
		
		
		public function __construct( $atts = array() )
		{
			$this->current_page = 1;
			
			$this->atts = shortcode_atts( avia_product_slider::get_defaults(), $atts, 'av_productslider' );
			
			if( $this->atts['items'] < 0 )
			{
				$this->atts['paginate'] = 'no';
			}
		}

		/**
		 * @since 4.5.7.2
		 */
		public function __destruct() 
		{
			unset( $this->atts );
		}
		
		/**
		 * Return defaults array
		 * 
		 * @since 4.8
		 * @return array
		 */
		static public function get_defaults() 
		{
			$defaults = array(
						'type'				=> 'slider', // can also be used as grid
						'style'				=> '', //no_margin
						'columns'			=> '4',
						'image_size'		=> '',
						'items'				=> '16',
						'wc_prod_visible'	=>	'',
						'wc_prod_hidden'	=>	'',
						'wc_prod_featured'	=>	'',
						'wc_prod_additional_filter'		=> '',
						'taxonomy'			=> 'product_cat',
						'post_type'			=> 'product',
						'contents'			=> 'excerpt',
						'autoplay'			=> 'no',
						'animation'			=> 'fade',
						'paginate'			=> 'no',
						'interval'			=> 5,
						'class'				=> '',
						'sort'				=> '',
						'prod_order'		=> '',
						'offset'			=> 0,
						'link_behavior'		=> '',
						'show_images'		=> 'yes',
						'categories'		=> array(),
						'av_display_classes'	=> '',
						'el_id'				=> '',			//	must contain id="...."
						'custom_class'		=> ''
					);
			
			return $defaults;
		}


		/**
		 * 
		 * @return string
		 */
		public function html()
		{
			global $woocommerce, $woocommerce_loop, $avia_config;
			
			$output = '';

			avia_product_slider::$slide ++;
			
			extract( $this->atts );

			$extraClass 		= 'first';
			$post_loop_count 	= 1;
			$loop_counter		= 1;
			$autoplay 			= $autoplay == 'no' ? false : true;
			$total				= $columns % 2 ? 'odd' : 'even';
			$woocommerce_loop['columns'] = $columns;

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
				default:
					$grid = 'av_one_third';  
					break;
			}

			//	Add filter to change default WC image size
			add_filter( 'avf_wc_before_shop_loop_item_title_img_size', array( $this, 'handler_wc_image_size_slider' ), 1000, 1 );

			$data = AviaHelper::create_data_string( array( 'autoplay' => $autoplay, 'interval' => $interval, 'animation' => $animation, 'hoverpause' => 1 ) );

			ob_start();

			if ( have_posts() )
			{
				echo "<div {$el_id} {$data} class='template-shop avia-content-slider avia-content-{$type}-active avia-product-slider" . avia_product_slider::$slide . " avia-content-slider-{$total} {$class} {$av_display_classes} shop_columns_{$columns}' >";

				if( $sort == 'dropdown' ) 
				{
					avia_woocommerce_frontend_search_params();
				}

				echo 	"<div class='avia-content-slider-inner'>";

				if( $type == 'grid' ) 
				{
					echo '<ul class="products">';
				}

				while ( have_posts() ) : the_post();
			
					if( $loop_counter == 1 && $type == 'slider' ) 
					{
						echo '<ul class="products slide-entry-wrap">';
					}


					if( function_exists( 'wc_get_template_part' ) )
					{
						wc_get_template_part( 'content', 'product'  );
					}
					else
					{
						woocommerce_get_template_part( 'content', 'product' );
					}

					$loop_counter ++;
					$post_loop_count ++;

					if( $loop_counter > $columns )
					{
						$loop_counter = 1;
					}

					if( $loop_counter == 1 && $type == 'slider' )
					{
						echo '</ul>';
					}

				endwhile; // end of the loop.

				if( $loop_counter != 1 || $type == 'grid' )
				{
					echo '</ul>';
				}

				echo 	'</div>';

				if( $post_loop_count -1 > $columns && $type == 'slider' )
				{
					echo $this->slide_navigation_arrows();
				}

				echo '</div>';
			}
			else 
			{
				if( function_exists( 'woocommerce_product_subcategories' ) )
				{
					if ( ! woocommerce_product_subcategories( array( 'before' => '<ul class="products">', 'after' => '</ul>' ) ) )
					{
						echo '<p>' . __( 'No products found which match your selection.', 'avia_framework' ) . '</p>';
					}
				}
			}

			echo '<div class="clear"></div>';

			$products = ob_get_clean();
			
			remove_filter( 'avf_wc_before_shop_loop_item_title_img_size', array( $this, 'handler_wc_image_size_slider' ), 1000 );
			
			$output .= $products;

			if( $paginate == 'yes' && $avia_pagination = avia_pagination( '', 'nav', 'avia-element-paging', $this->current_page ) ) 
			{
				$output .= "<div class='pagination-wrap pagination-slider {$av_display_classes}'>{$avia_pagination}</div>";
			}

			/**
			 * @since WC 3.3.0 we have to reset WC loop counter otherwise layout might break
			 */
			if( function_exists( 'wc_reset_loop' ) )
			{
				wc_reset_loop();
			}
			
			wp_reset_query();
			return $output;
		}
		
		/**
		 * 
		 * @return string
		 */
		public function html_list()
		{
			global $woocommerce, $avia_config, $wp_query;
			
			$output = '';

			extract( $this->atts );

			$extraClass 		= 'first';
			$post_loop_count 	= 0;
			$loop_counter		= 0;
			$total				= $columns % 2 ? 'odd' : 'even';
			$posts_per_col		= ceil( $wp_query->post_count / $columns );

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
				default:
					$grid = 'av_fullwidth';  
					break;
			}

			ob_start();

			if ( have_posts() )
			{	
				while ( have_posts() ) : the_post();
				
					avia_product_slider::$slide ++;
					$post_loop_count ++;
					$loop_counter ++;
					if( $loop_counter === 1 )
					{
						echo "<div {$el_id} class='{$grid} {$extraClass} {$custom_class} flex_column av-catalogue-column {$av_display_classes} '>";
						echo "<div class='av-catalogue-container av-catalogue-container-woo' >";
						echo "<ul class='av-catalogue-list'>";
						$extraClass = '';
					}
				
					global $product;
					
					$link 	= 	$product->add_to_cart_url();
					$ajax_class = 'add_to_cart_button product_type_simple';
					$text	= '';
					$title 	= 	get_the_title();
					$content = 	strip_tags(get_the_excerpt());
					$price = 	$product->get_price_html();
					$rel   = '';
					$product_type = $product->get_type();
					
					/**
					 * Choose product types that link to single product pages when clicked and not ajax add to cart
					 * (currently only class avia_sc_productlist supports this option)
					 * 
					 * @since 4.5.4
					 * @return array
					 */
					$force_product_page_array = apply_filters( 'avf_slider_add_to_cart_via_product_page', array( 'variable' ), $this );
					
					if( empty( $link_behavior ) || in_array( $product_type, $force_product_page_array ) )
					{
						$cart_url = get_the_permalink();
						$ajax_class = '';
					}
					else
					{
						$cart_url = $product->add_to_cart_url();
						$ajax_class = $product->is_purchasable() ? 'add_to_cart_button ajax_add_to_cart' : '';
						$rel = $product->is_purchasable() ? "rel='nofollow'" : '';
					}
					
					$product_id = method_exists( $product , 'get_id' ) ? $product->get_id() : $product->id;
					$product_type = method_exists( $product , 'get_type' ) ? $product->get_type() : $product->product_type;
					
					$image = get_the_post_thumbnail( $product_id, 'square', array( 'class' => "av-catalogue-image av-cart-update-image av-catalogue-image-{$show_images}" ) );
					
					$text .= $image;
					$text .= "<div class='av-catalogue-item-inner'>";
					$text .=	"<div class='av-catalogue-title-container'><div class='av-catalogue-title av-cart-update-title'>{$title}</div><div class='av-catalogue-price av-cart-update-price'>{$price}</div></div>";
					$text .=	"<div class='av-catalogue-content'>{$content}</div>";
					$text .= '</div>';
					
					echo '<li>';
					
					//copied from templates/loop/add-to-cart.php - class and rel attr changed, as well as text
					
					echo apply_filters( 'woocommerce_loop_add_to_cart_link',
								sprintf( '<a %s href="%s" data-product_id="%s" data-product_sku="%s" class="av-catalogue-item %s product_type_%s product-nr-%d">%s</a>',
									$rel,
									esc_url( $cart_url ),
									esc_attr( $product_id ),
									esc_attr( $product->get_sku() ),
									$ajax_class,
									esc_attr( $product_type ),
									avia_product_slider::$slide,
									$text
								),
							$product );
		
					echo '</li>';
		
					if( $loop_counter == $posts_per_col || $post_loop_count == $wp_query->post_count )
					{
						echo '</ul>';
						echo '</div>';
						echo '</div>';
						$loop_counter = 0;
					}	

				endwhile; // end of the loop.
				
			}

			$products = ob_get_clean();
			
			$output .= $products;

			if( $paginate == 'yes' && $avia_pagination = avia_pagination( '', 'nav', 'avia-element-paging', $this->current_page ) ) 
			{
				$output .= "<div class='pagination-wrap pagination-slider {$av_display_classes} '>{$avia_pagination}</div>";
			}
			
			/**
			 * @since WC 3.3.0 we have to reset WC loop counter otherwise layout might break
			 */
			if( function_exists( 'wc_reset_loop' ) )
			{
				wc_reset_loop();
			}
			
			wp_reset_query();
			
			return $output;
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
		 * Fetch new entries
		 * 
		 * @param array $params
		 */
		public function query_entries( $params = array() )
		{
			global $woocommerce, $avia_config;

			$query = array();
			if( empty( $params ) ) 
			{
				$params = $this->atts;
			}

			if( ! empty( $params['categories'] ) )
			{
				//get the product categories
				$terms 	= explode( ',', $params['categories'] );
			}
			
			$this->current_page = ( $params['paginate'] == 'no' || $params['type'] == 'slider' ) ? 1:  avia_get_current_pagination_number( 'avia-element-paging' );
			
			//if we find no terms for the taxonomy fetch all taxonomy terms
			if( empty($terms[0]) || is_null( $terms[0] ) || $terms[0] === 'null' )
			{
				$term_args = array( 
								'taxonomy'		=> $params['taxonomy'],
								'hide_empty'	=> true
							);
				/**
				 * To display private posts you need to set 'hide_empty' to false, 
				 * otherwise a category with ONLY private posts will not be returned !!
				 * 
				 * You also need to add post_status 'private' to the query params with filter avia_product_slide_query.
				 * 
				 * @since 4.4.2
				 * @added_by Günter
				 * @param array $term_args 
				 * @param array $params 
				 * @return array
				 */
				$term_args = apply_filters( 'avf_av_productslider_term_args', $term_args, $params );

				$allTax = AviaHelper::get_terms( $term_args );

				$terms = array();
				foreach( $allTax as $tax )
				{
					$terms[] = $tax->term_id;
				}
			}

			if( $params['sort'] == 'dropdown' )
			{
				$avia_config['woocommerce']['default_posts_per_page'] = $params['items'];
				$ordering 	= $woocommerce->query->get_catalog_ordering_args();
				$order 		= $ordering['order'];
				$orderBY 	= $ordering['orderby'];

				if( ! empty( $avia_config['shop_overview_products_overwritten'] ) && $params['items'] != -1 )
				{
					$params['items'] = $avia_config['shop_overview_products'];
				}
			}
			else
			{
				$avia_config['woocommerce']['disable_sorting_options'] = true;

				$chk_sort = ( empty( $params['sort'] ) || $params['sort'] == '0' ) ? '' : $params['sort'];
				$ordering 	= avia_wc_get_product_query_order_args( $chk_sort, $params['prod_order'] );

				$order 		= $ordering['order'];
				$orderBY 	= $ordering['orderby'];
			}


            if( $params['offset'] == 'no_duplicates' )
            {
                $params['offset'] = 0;
                $no_duplicates = true;
            }
            
            if( $params['offset'] == 0 )
			{
				$params['offset'] = false;
			}


			// Meta query - replaced by Tax query in WC 3.0.0
			$meta_query = array();
			$tax_query = array();
			
			avia_wc_set_out_of_stock_query_params( $meta_query, $tax_query, $params['wc_prod_visible'] );
			avia_wc_set_hidden_prod_query_params( $meta_query, $tax_query, $params['wc_prod_hidden'] );
			avia_wc_set_featured_prod_query_params( $meta_query, $tax_query, $params['wc_prod_featured'] );
			
			if( 'use_additional_filter' == $params['wc_prod_additional_filter'] )
			{
				avia_wc_set_additional_filter_args( $meta_query, $tax_query );
			}

			$avia_config['woocommerce']['disable_sorting_options'] = true;
			
			//	sets filter hooks !!
			$ordering_args = avia_wc_get_product_query_order_args( $orderBY, $order );
			
			if( ! empty( $terms ) )
			{
				$tax_query[] =  array( 	
									'taxonomy' 	=>	$params['taxonomy'],
									'field' 	=>	'id',
									'terms' 	=>	$terms,
									'operator' 	=>	'IN'
							);
			}			
			
			$query = array(
							'post_type'				=>	$params['post_type'],
							'post_status'			=>	'publish',
							'ignore_sticky_posts'	=>	1,
							'paged'					=>	$this->current_page,
							'offset'            	=>	$params['offset'],
							'post__not_in'			=>	( !empty( $no_duplicates ) ) ? $avia_config['posts_on_current_page'] : array(),
							'posts_per_page'		=>	$params['items'],
							'orderby'				=>	$ordering_args['orderby'],
							'order'					=>	$ordering_args['order'],
							'meta_query'			=>	$meta_query,
							'tax_query'				=>	$tax_query
												);
			
			
			if ( ! empty( $ordering_args['meta_key'] ) ) 
			{
	 			$query['meta_key'] = $ordering_args['meta_key'];
	 		}

			/**
			 * @used_by			currently unused
			 * 
			 * @since < 4.0
			 * @param array $query
			 * @param array $params
			 * @param array $ordering_args
			 * @return array 
			 */
			$query = apply_filters( 'avia_product_slide_query', $query, $params, $ordering_args );
			query_posts( $query );
			
		    // store the queried post ids in
            if( have_posts() )
            {
                while( have_posts() )
                {
                    the_post();
                    $avia_config['posts_on_current_page'][] = get_the_ID();
                }
            }
			
				//	remove all filters
			avia_wc_clear_catalog_ordering_args_filters();
			$avia_config['woocommerce']['disable_sorting_options'] = false;
		}
		
		/**
		 * Returns the selected image size
		 * 
		 * @since 4.8
		 * @param string $size
		 * @return string
		 */
		public function handler_wc_image_size_slider( $size )
		{
			return ! empty( $this->atts['image_size'] ) ? $this->atts['image_size'] : $size;
		}
	}
}
