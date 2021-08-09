<?php
/**
 * Catalogue
 * 
 * Creates a pricing list
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( !class_exists( 'avia_sc_catalogue' ) )
{
	class avia_sc_catalogue extends aviaShortcodeTemplate
	{
		/**
		 * @since 4.7.6.2
		 * @var string 
		 */
		protected $html_lazy_loading;
		
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $screen_options;
		
		/**
		 * 
		 * @since 4.5.5
		 * @param AviaBuilder $builder
		 */
		public function __construct( $builder ) 
		{
			$this->screen_options = array();
				
			parent::__construct( $builder );
		}
		
		/**
		 * @since 4.5.5
		 */
		public function __destruct() 
		{
			parent::__destruct();
			
			unset( $this->screen_options );
		}
		
		
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Catalogue', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-catalogue.png';
			$this->config['order']			= 20;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_catalogue';
			$this->config['shortcode_nested'] = array( 'av_catalogue_item' );
			$this->config['tooltip']		= __( 'Creates a pricing list', 'avia_framework' );
			$this->config['preview']		= true;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
			$this->config['name_item']		= __( 'Catalogue Item', 'avia_framework' );
			$this->config['tooltip_item']	= __( 'A Catalogue Element Item', 'avia_framework' );
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
								'template_id'	=> $this->popup_key( 'content_catalog' )
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
								'template_id'	=> 'lazy_loading_toggle',
								'std'			=> 'enabled',
								'lockable'		=> true
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
							'name'			=> __( 'Add/Edit List items', 'avia_framework' ),
							'desc'			=> __( 'Here you can add, remove and edit the items of your item list.', 'avia_framework' ),
							'id'			=> 'content',
							'type'			=> 'modal_group',
							'modal_title'	=> __( 'Edit List Item', 'avia_framework' ),
							'std'			=> array(
													array( 
														'title'	=> __( 'List Item 1', 'avia_framework' ),
														'price'	=> __( '€ 100,-', 'avia_framework' )
													),
													array( 
														'title'	=> __( 'List Item 2', 'avia_framework' ),
														'price'	=> __( '€ 200,-', 'avia_framework' )
													),
													array( 
														'title'	=> __( 'List Item 3', 'avia_framework' ),
														'price'	=> __( '€ 300,-', 'avia_framework' )
													)
												),
							'editable_item'	=> true,
							'lockable'		=> true,
							'tmpl_set_default'	=> false,
							'subelements'	=> $this->create_modal()
						)
				
				);
			
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_catalog' ), $c );
				
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
							'template_id'	=> $this->popup_key( 'modal_content_item' )
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
							'template_id'	=> $this->popup_key( 'modal_advanced_link' )
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
							'name' 	=> __( 'List Item Title', 'avia_framework' ),
							'desc' 	=> __( 'Enter the list item title here', 'avia_framework' ) ,
							'id' 	=> 'title',
							'type' 	=> 'input',
							'std' 	=> 'List Title',
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),
									
						array(
							'name' 	=> __( 'List Item Description', 'avia_framework' ),
							'desc' 	=> __( 'Enter the item description here', 'avia_framework' ) ,
							'id' 	=> 'content',
							'type' 	=> 'tiny_mce',
							'std' 	=> __( 'Enter your description here', 'avia_framework' ),
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),
									
						array(
							'name' 	=> __( 'Pricing', 'avia_framework' ),
							'desc' 	=> __( 'Enter the price for the item here. Eg: 34$, 55.5€, £12', 'avia_framework' ),
							'id' 	=> 'price',
							'type' 	=> 'input',
							'std' 	=> '',
							'lockable'	=> true,
							'tmpl_set_default'	=> false
						),
									
						array(	
							'name'		=> __( 'Thumbnail Image','avia_framework' ),
							'desc'		=> __( 'Either upload a new, or choose an existing image from your media library','avia_framework' ),
							'id'		=> 'id',
							'type'		=> 'image',
							'fetch'		=> 'id',
							'title'		=> __( 'Change Image','avia_framework' ),
							'button'	=> __( 'Change Image','avia_framework' ),
							'std'		=> '',
							'lockable'	=> true
						),
				
						array(
							'name' 	=> __( 'Disable Item?', 'avia_framework' ),
							'desc' 	=> __( 'Temporarily disable and hide the item without deleting it, if its out of stock', 'avia_framework' ),
							'id' 	=> 'disabled',
							'type' 	=> 'checkbox',
							'std'	=> '',
							'lockable'	=> true
						),
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_item' ), $c );
			
			
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Item Link?', 'avia_framework' ),
							'desc' 	=> __( 'Where should your item link to?', 'avia_framework' ),
							'id' 	=> 'link',
							'type' 	=> 'linkpicker',
							'std' 	=> '',
							'fetchTMPL'	=> true,
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'No Link', 'avia_framework' )					=> '',
												__( 'Open bigger version of Thumbnail Image in lightbox (image needs to be set)', 'avia_framework' ) => 'lightbox',
												__( 'Set Manually', 'avia_framework' )				=> 'manually',
												__( 'Single Entry', 'avia_framework' )				=> 'single',
												__( 'Taxonomy Overview Page', 'avia_framework' )	=> 'taxonomy',
											),
						),
				
						array(
							'name' 	=> __( 'Open new tab/window', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to open the link url in a new tab/window?', 'avia_framework' ),
							'id' 	=> 'target',
							'type' 	=> 'select',
							'std'	=> '',
							'lockable'	=> true,
							'required'	=> array( 'link', 'not_empty_and', 'lightbox' ),
							'subtype'	=> AviaHtmlHelper::linking_options()
						)
				                        
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_link' ), $c );
			
		}
		

		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * 
		 * @param array $params		holds the default values for $content and $args.
		 * @return array			usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_sub_element( $params )
		{
			$default = array();
			$locked = array();
			$attr = $params['args'];
			Avia_Element_Templates()->set_locked_attributes( $attr, $this, $this->config['shortcode_nested'][0], $default, $locked );
			
			$template = $this->update_template_lockable( 'title', __( 'Item', 'avia_framework' ) . ': {{title}}', $locked );

			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_title_container' data-update_element_template='yes'>";
			$params['innerHtml'] .=		'<div ' . $this->class_by_arguments_lockable( 'disabled', $attr, $locked ) . '>';
			$params['innerHtml'] .=		"<span {$template}>" . __( 'Item', 'avia_framework' ) . ": {$attr['title']}</span></div>";
			$params['innerHtml'] .= '</div>';

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
			
			$default = array( 
							'lazy_loading'	=> 'enabled'
						);
			
			$default = $this->sync_sc_defaults_array( $default, 'no_modal_item', 'no_content' );
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			$this->screen_options = AviaHelper::av_mobile_sizes( $atts );

			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			$this->html_lazy_loading = $atts['lazy_loading'];
			
			$classes = array(
						'av-catalogue-container', 
						$element_id
					);
			
			$element_styling->add_classes( 'container', $classes );
			
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			
			$selectors = array(
						'container'	=> ".av-catalogue-container.{$element_id}"
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
		 * 
		 * @since 4.8.4
		 * @param array $args
		 * @return array
		 */
		protected function get_element_styles_item( array $args ) 
		{	
			$result = parent::get_element_styles_item( $args );
			
			/**
			 * Fixes a problem when 3-rd party plugins call nested shortcodes without executing main shortcode  (like YOAST in wpseo-filter-shortcodes)
			 */
			if( empty( $this->screen_options ) )
			{
				return $result;
			}
			
			extract( $result );
			
			$default = array( 
						'title'		=> '', 
						'price'		=> '', 
						'link'		=> '', 
						'target'	=> '', 
						'disabled'	=> '', 
						'id'		=> ''
				);
			
			$default = $this->sync_sc_defaults_array( $default, 'modal_item', 'no_content' );
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );

			extract( $this->screen_options ); //return $av_font_classes, $av_title_font_classes and $av_display_classes	
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode_nested'][0] );
			
			$classes = array(
						'av-catalogue-item',
						$element_id
					);
			
			$element_styling->add_classes( 'container', $classes );

			$selectors = array(
						'container'	=> ".av-catalogue-container .av-catalogue-item.{$element_id}"
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
			
			extract( $this->screen_options ); //return $av_font_classes, $av_title_font_classes and $av_display_classes	
			
			extract( $atts );
			
			$style_tag = $element_styling->get_style_tag( $element_id );
			$container_class = $element_styling->get_class_string( 'container' );
			
			$output	 = '';
			$output .= $style_tag;
			$output .=	"<div {$meta['custom_el_id']} class='{$container_class} {$av_display_classes}'>";

			$output .=		"<ul class='av-catalogue-list'>";
			$output .=			ShortcodeHelper::avia_remove_autop( $content, true );
			$output .=		'</ul>';
			$output .=	'</div>';

			return Av_Responsive_Images()->make_content_images_responsive( $output );
		}

		/**
		 * 
		 * @param array $atts
		 * @param string $content
		 * @param string $shortcodename
		 * @return string
		 */
		public function av_catalogue_item( $atts, $content = '', $shortcodename = '' )
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

			extract( $atts );
			
			if( $disabled ) 
			{
				return '';
			}
				
			$item_markup = array( 'open' => 'div', 'close' => 'div' );
			$image = '';
			$blank = '';

			if( $link )
			{
				if( $link == 'lightbox' && $id )
				{
					$link = AviaHelper::get_url( $link, $id, true );
				}
				else
				{
					$link = AviaHelper::get_url( $link );
					$blank = AviaHelper::get_link_target( $target );
				}

				$lightbox_attr = Av_Responsive_Images()->html_attr_image_src( $link, false );
				$item_markup = array( 'open' => "a {$lightbox_attr} {$blank}", 'close' => 'a' );
			}
				
			if( ! empty( $id ) )
			{
				/**
				 * Allows e.g. WPML to reroute to translated image
				 */
				$posts = get_posts( array(
										'include'			=> $id,
										'post_status'		=> 'inherit',
										'post_type'			=> 'attachment',
										'post_mime_type'	=> 'image',
										'order'				=> 'ASC',
										'orderby'			=> 'post__in' )
									);

				if( is_array( $posts ) && ! empty( $posts ) )
				{
					$attachment_entry = $posts[0];

					$alt = get_post_meta( $attachment_entry->ID, '_wp_attachment_image_alt', true );
					$alt = ! empty( $alt ) ? esc_attr( $alt ) : '';
					$img_title = trim( $attachment_entry->post_title ) ? esc_attr( $attachment_entry->post_title ) : '';
					$src = wp_get_attachment_image_src( $attachment_entry->ID, 'square' );
					$src = ! empty( $src[0] ) ? $src[0] : '';

					$image = "<img src='{$src}' title='{$img_title}' alt='{$alt}' class='av-catalogue-image' />";
					$image = Av_Responsive_Images()->prepare_single_image( $image, $attachment_entry->ID, $this->html_lazy_loading );
				}
			}
			
			$style_tag = $element_styling->get_style_tag( $element_id );
			$container_class = $element_styling->get_class_string( 'container' );

			$output  = '';
			$output .= $style_tag;
			$output .= '<li>';
			$output .=		"<{$item_markup['open']} class='{$container_class}'>";
			$output .=			$image;
			$output .=			'<div class="av-catalogue-item-inner">';
			$output .=				'<div class="av-catalogue-title-container">';
			$output .=					"<div class='av-catalogue-title'>{$title}</div>";
			$output .=					"<div class='av-catalogue-price'>{$price}</div>";
			$output .=				'</div>';
			$output .=				'<div class="av-catalogue-content">' . do_shortcode( $content ) . '</div>';
			$output .=			'</div>';
			$output .=		"</{$item_markup['close']}>";
			$output .= '</li>';
			
			return $output;
		}

	}
}
