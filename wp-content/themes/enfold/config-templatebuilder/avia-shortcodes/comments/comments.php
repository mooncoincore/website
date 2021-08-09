<?php
/**
 * Comments Element
 * 
 * Add a comment form and comments list to the template
 */
 
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) { die('-1'); }



if ( ! class_exists( 'avia_sc_comments_list' ) )
{
	class avia_sc_comments_list extends aviaShortcodeTemplate
	{		
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'yes';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Comments', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-comments.png';
			$this->config['order']			= 5;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_comments_list';
			$this->config['tinyMCE']		= array( 'disable' => 'true' );
			$this->config['tooltip']		= __( 'Add a comment form and comments list to the template', 'avia_framework' );
			//$this->config['drag-level']	= 1;
			$this->config['disabling_allowed'] = 'manually';
			$this->config['disabled']		= array(
												'condition'	=> ( avia_get_option( 'disable_blog' ) == 'disable_blog' ), 
												'text'		=> __( 'This element is disabled in your theme options. You can enable it in Enfold &raquo; Performance', 'avia_framework' )
											);
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
		}
			
			
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-comments', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/comments/comments.css', array( 'avia-layout' ), false );
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
			//if the element is disabled
			if( true === $this->config['disabled']['condition'] )
			{
				$this->elements = array(
					
					array(	
								'type'			=> 'template',
								'template_id'	=> 'element_disabled',
								'args'			=> array(
														'desc'	=> $this->config['disabled']['text']
													)
							),
						);

				return;
			}
			
			
			$this->elements = array(
				
				array(
						'type' 	=> 'tab_container', 
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
			$params['content'] = null; //remove to allow content elements
			
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
			
			$default = $this->sync_sc_defaults_array();
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			
			$element_styling->create_callback_styles( $atts );
			
			$classes = array(
						'av-buildercomment',
						$element_id
					);
			
			$element_styling->add_classes( 'container', $classes );
			$element_styling->add_classes_from_array( 'container', $meta, 'custom_class' );
			
			if( function_exists( 'avia_blog_class_string' ) )
			{
				$element_styling->add_classes( 'container', avia_blog_class_string() );
			}
			
			
			$selectors = array(
						'container'	=> ".av-buildercomment.{$element_id}"
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
			global $post;
			
			$result = $this->get_element_styles( compact( array( 'atts', 'content', 'shortcodename', 'meta' ) ) );
			
			extract( $result );
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 
			
			extract( $atts );

			$comment = '';

			$need_moderation = get_option( 'comment_moderation', 0 );
			if( is_numeric( $need_moderation ) && ( 1 == (int) $need_moderation ) )
			{
				$comment_entries = get_comments( array( 'type' => 'comment', 'post_id' => $post->ID ) );

				$total = 0;
				$first = 0;

				foreach( $comment_entries as $index => $entry ) 
				{
					if( is_numeric( $entry->comment_approved ) && ( 0 === (int) $entry->comment_approved ) )
					{
						( 0 == $index ) ? $first ++ : $total ++;
					}
				}

				if( ( $first != 0 ) || ( $total != 0 ) )
				{
					if( ( $first != 0 ) && ( $total != 0 ) )
					{
						$info = sprintf( __( 'The last comment and %d other comment(s) need to be approved.', 'avia_framework' ), $total );
					}
					else if( $first != 0 )
					{
						$info = __( 'The last comment needs to be approved.', 'avia_framework' );
					}
					else
					{
						$info = sprintf( __( '%d comment(s) need to be approved.', 'avia_framework' ), $total );
					}

					$comment .=	'<div class="av-buildercomment-unapproved">';
					$comment .=		'<span>' . $info . '</span>';
					$comment .=	'</div>';
				}
			}

			ob_start(); //start buffering the output instead of echoing it
			comments_template(); //wordpress function that loads the comments template 'comments.php'
			$comment .= ob_get_clean();
			
			
			$style_tag = $element_styling->get_style_tag( $element_id );
			$container_class = $element_styling->get_class_string( 'container' );
			
			$output  = '';
			$output .= $style_tag;
			$output .= "<div {$meta['custom_el_id']} class='{$container_class} {$av_display_classes}'>";
			$output .=		$comment;
			$output .= '</div>';
			
			return $output;
		}
	}
}
