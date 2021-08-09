<?php

/**
 * Smash Balloon Social Photo Feed Plugin Integration
 * https://wordpress.org/plugins/instagram-feed/ and Pro Version https://smashballoon.com/instagram-feed/demo/
 * 
 * 
 * Display an instagram feed using this plugin (including pro version)
 * 
 * @since 4.7.3.1
 * @added_by Günter
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( 'SB_Instagram_Feed' ) )
{
	if( ! function_exists( 'av_sb_instagram_feed_fallback' ) )
	{
		function av_sb_instagram_feed_fallback()
		{
			if( ! current_user_can( 'edit_posts' ) )
			{
				return '';
			}
			
			$url = '<a href="https://wordpress.org/plugins/instagram-feed/">';
			$pro = '<a href="https://smashballoon.com/instagram-feed/demo/">';
			
			$out  = '<p>';
			$out .=		sprintf( __( 'Please install the plugin %1$sInstagram Feed%3$s or %2$sInstagram Feed Pro%3$s to display your Instagram feed.', 'avia_framework' ), $url, $pro, '</a>' );
			$out .= '</p>';
			
			return $out;
		}
		
		add_shortcode( 'av_sb_instagram_feed', 'av_sb_instagram_feed_fallback' );
	}
	
	return;
}

 
if ( ! class_exists( 'avia_sc_sb_instagram_feed' ) ) 
{
	
	class avia_sc_sb_instagram_feed extends aviaShortcodeTemplate
	{
		
		/**
		 *
		 * @var int 
		 */
		static $feed_count = 0;
		
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['self_closing']	= 'yes';
			$this->config['version']		= '1.0';

			$this->config['name']			= __( 'Instagram Feed', 'avia_framework' );
			$this->config['tab']			= __( 'Plugin Additions', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-instagram-feed.png';
			$this->config['order']			= 20;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_sb_instagram_feed';
			$this->config['tooltip']		= __( 'Display an Instagram feed (Smash Balloon Plugin)', 'avia_framework' );
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
		}

		function extra_assets()
		{
//			//load css
//			wp_enqueue_style( 'avia-module-instagram-feed', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/instagram_feed/instagram_feed.css', array( 'avia-layout' ), false );
//
//			//load js
//			wp_enqueue_script( 'avia-module-instagram-feed', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/instagram_feed/instagram_feed.js', array( 'avia-shortcodes' ), false, true );
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
						'class'	=> '',
						'nodescription' => true
					),
						
				array(
						'type' 	=> 'tab',
						'name'  => __( 'Content', 'avia_framework' ),
						'nodescription' => true
					),
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'content_instagram' )
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
													$this->popup_key( 'styling_layout' ),
													$this->popup_key( 'styling_header' ),
													$this->popup_key( 'styling_load_more' ),
													$this->popup_key( 'styling_follow_me' ),
													$this->popup_key( 'styling_hover' ),
													$this->popup_key( 'styling_caption' ),
													$this->popup_key( 'styling_Likes' ),
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
								'template_id'	=> $this->popup_key( 'advanced_carousel_slider' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_highlight_grid' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_links' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_lightbox' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_autoscroll' )
							),

						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_mobile' )
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
			$cls_pro = ! AviaInstagramFeed()->is_proversion_active() ? ' avia-hidden' : '';
			$default_setting = ' ' . __( 'Leave empty to use default plugin settings.', 'avia_framework' );
				
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name'		=> __( 'Select Content', 'avia_framework' ),
							'desc'		=> __( 'Select what content you want to display.', 'avia_framework' ),
							'id'		=> 'type',
							'type'		=> 'select',
							'std'		=> 'user',
							'container_class'	=> $cls_pro,
							'subtype'	=> array( 
												__( 'Display content from a User ID', 'avia_framework' )			=> 'user',
												__( 'Display content from one or more hashtags', 'avia_framework' )	=> 'hashtag',
											)
						),
				
						array(
							'name'		=> __( 'User Name', 'avia_framework' ),
							'desc'		=> __( 'Your Instagram User Name. This must be from a connected account on the &quot;Configure&quot; tab of the plugin.', 'avia_framework' ) . $default_setting,
							'id'		=> 'user',
							'type'		=> 'input',
							'std'		=> '',
							'required'	=> array( 'type', 'equals', 'user' )
						),
			
						array(
							'name'		=> __( 'Hashtag(s)', 'avia_framework' ),
							'desc'		=> __( 'Any hashtag. Separate multiple hashtags by commas (e.g. #awesome,#mytag)', 'avia_framework' ),
							'id'		=> 'hashtag',
							'type'		=> 'input',
							'std'		=> '',
							'required'	=> array( 'type', 'equals', 'hashtag' )
						),
				
						array(
							'name'		=> __( 'Select Media', 'avia_framework' ),
							'desc'		=> __( 'Select what media you want to display.', 'avia_framework' ),
							'id'		=> 'media',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> $cls_pro,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'All', 'avia_framework' )			=> 'all',
												__( 'Photos only', 'avia_framework' )	=> 'photos',
												__( 'Videos only', 'avia_framework' )	=> 'videos'
											)
						),
				
						array(
							'name'		=> __( 'Exclude Content', 'avia_framework' ),
							'desc'		=> __( 'Remove content which contain certain words or hashtags in the caption. Separate multiple entries by commas (e.g. #awesome,#mytag)', 'avia_framework' ),
							'id'		=> 'excludewords',
							'type'		=> 'input',
							'container_class'	=> $cls_pro,
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Filter Content', 'avia_framework' ),
							'desc'		=> __( 'Only display content which contain certain words or hashtags in the caption. Separate multiple entries by commas (e.g. #awesome,#mytag)', 'avia_framework' ),
							'id'		=> 'includewords',
							'type'		=> 'input',
							'container_class'	=> $cls_pro,
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Whitelist Content', 'avia_framework' ),
							'desc'		=> __( 'Only display content that match one of the post ids in this "whitelist". Separate multiple entries by commas (e.g. 2,125)', 'avia_framework' ),
							'id'		=> 'whitelist',
							'type'		=> 'input',
							'container_class'	=> $cls_pro,
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Sort Order', 'avia_framework' ),
							'desc'		=> __( 'Select the sort order of the displayed photos/videos.', 'avia_framework' ),
							'id'		=> 'sortby',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half av_half_first',
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )		=> '',
												__( 'Newest to Oldest', 'avia_framework' )	=> 'none',
												__( 'Random', 'avia_framework' )			=> 'random',
											)
						),
				
						array(
							'name'		=> __( 'Display initially', 'avia_framework' ),
							'desc'		=> __( 'The number of photos/videos to display initially.', 'avia_framework' ),
							'id'		=> 'num',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half',
							'subtype'	=> AviaHtmlHelper::number_array( 1, 33, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						)
						
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_instagram' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name'		=> __( 'Width', 'avia_framework' ),
							'desc'		=> __( 'The width of your feed. Any number, add px (= default) or %.', 'avia_framework' ) . $default_setting,
							'id'		=> 'width',
							'type'		=> 'input',
							'std'		=> '',
							'container_class' => 'av_half av_half_first'
						),
			
						array(
							'name'		=> __( 'Height', 'avia_framework' ),
							'desc'		=> __( 'The height of your feed. Any number, add px (= default) or %.', 'avia_framework' ) . $default_setting,
							'id'		=> 'height',
							'type'		=> 'input',
							'std'		=> '',
							'container_class' => 'av_half',
						),
				
						array(
							'name'		=> __( 'Number of columns in your feed', 'avia_framework' ),
							'desc'		=> __( 'Select the number of columns to display.', 'avia_framework' ),
							'id'		=> 'cols',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half av_half_first',
							'subtype'	=> AviaHtmlHelper::number_array( 1, 10, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						),
			
						array(
							'name'		=> __( 'Image Resolution', 'avia_framework' ),
							'desc'		=> __( 'Select the resolution of the images.', 'avia_framework' ),
							'id'		=> 'imageres',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half',
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Auto', 'avia_framework' )			=> 'auto',
												__( 'Full', 'avia_framework' )			=> 'full',
												__( 'Medium', 'avia_framework' )		=> 'medium',
												__( 'Thumbnail', 'avia_framework' )		=> 'thumb',
											)
						),
			
						array(
							'name'		=> __( 'Background color of the feed', 'avia_framework' ),
							'desc'		=> __( 'Set a custom background color. Any hex color code.', 'avia_framework' ) . $default_setting,
							'id'		=> 'background',
							'type'		=> 'colorpicker',
							'std'		=> ''
						),
			
						array(
							'name'		=> __( 'Custom CSS Class', 'avia_framework' ),
							'desc'		=> __( 'Add a CSS class to the feed container.', 'avia_framework' ),
							'id'		=> 'class',
							'type'		=> 'input',
							'std'		=> ''
						)
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
							'name'		=> __( 'Layout', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> 'layout',
							'type'		=> 'select',
							'std'		=> 'isb-grid',
							'container_class'	=> $cls_pro,
							'subtype'	=> array( 
												__( 'Grid', 'avia_framework' )				=> 'isb-grid',
												__( 'Carousel Slider', 'avia_framework' )	=> 'isb-carousel',
												__( 'Masonry Grid', 'avia_framework' )		=> 'isb-masonry',
												__( 'Highlight Grid', 'avia_framework' )	=> 'isb-highlight'
											)
						),
			
						
			
						array(
							'name'		=> __( 'Spacing', 'avia_framework' ),
							'desc'		=> __( 'The spacing around your photos. Any number, add px (= default) or %.', 'avia_framework' ) . $default_setting,
							'id'		=> 'imagepadding',
							'type'		=> 'input',
							'std'		=> ''
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Layout', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_layout' ), $template );
			
			
			$c = array(
						array(
							'name'		=> __( 'Show feed header', 'avia_framework' ),
							'desc'		=> __( 'Select to show the feed header', 'avia_framework' ),
							'id'		=> 'showheader',
							'type'		=> 'select',
							'std'		=> '',
							'container_class' => 'av_half av_half_first',
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
			
						array(
							'name'		=> __( 'Show bio in header', 'avia_framework' ),
							'desc'		=> __( 'Select to show the bio in header.', 'avia_framework' ),
							'id'		=> 'showbio',
							'type'		=> 'select',
							'std'		=> '',
							'container_class' => 'av_half',
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
			
						array(
							'name'		=> __( 'Size of the header', 'avia_framework' ),
							'desc'		=> __( 'Select to size of the header.', 'avia_framework' ),
							'id'		=> 'headersize',
							'type'		=> 'select',
							'std'		=> '',
							'container_class' => 'av_half av_half_first',
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Small', 'avia_framework' )			=> 'small',
												__( 'Medium', 'avia_framework' )		=> 'medium ',
												__( 'Large', 'avia_framework' )			=> 'large ',
											)
						),
			
						array(
							'name'		=> __( 'Headercolor', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' )  . $default_setting,
							'id'		=> 'headercolor',
							'type'		=> 'colorpicker',
							'std'		=> '',
							'container_class' => 'av_half'
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Header', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_header' ), $template );
			
			
			$c = array(
						array(
							'name'		=> __( 'Show the &quot;Load More&quot; Button', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> 'showbutton',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
			
						array(
							'name'		=> __( 'Background color of the button', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ) . $default_setting,
							'id'		=> 'buttoncolor',
							'type'		=> 'colorpicker',
							'std'		=> '',
							'container_class' => 'av_half av_half_first'
						),
			
						array(
							'name'		=> __( 'Text color of the button', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ) . $default_setting,
							'id'		=> 'buttontextcolor',
							'type'		=> 'colorpicker',
							'std'		=> '',
							'container_class' => 'av_half'
						),
			
						array(
							'name'		=> __( 'Button text', 'avia_framework' ),
							'desc'		=> __( 'Enter text used for the button.', 'avia_framework' ) . $default_setting,
							'id'		=> 'buttontext',
							'type'		=> 'input',
							'std'		=> '',
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( '&quot;Load More&quot; Button', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_load_more' ), $template );
			
			$c = array(
						array(
							'name'		=> __( 'Show the &quot;Follow on Instagram&quot; Button', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> 'showfollow',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
			
						array(
							'name'		=> __( 'Background color of the button', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ) . $default_setting,
							'id'		=> 'followcolor',
							'type'		=> 'colorpicker',
							'std'		=> '',
							'container_class' => 'av_half av_half_first'
						),
			
						array(
							'name'		=> __( 'Text color of the button', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ) . $default_setting,
							'id'		=> 'followtextcolor',
							'type'		=> 'colorpicker',
							'std'		=> '',
							'container_class' => 'av_half'
						),
			
						array(
							'name'		=> __( 'Button text', 'avia_framework' ),
							'desc'		=> __( 'Enter text used for the button.', 'avia_framework' ) . $default_setting,
							'id'		=> 'followtext',
							'type'		=> 'input',
							'std'		=> ''
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( '&quot;Follow on Instagram&quot; Button', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_follow_me' ), $template );
			
			
			$c = array(
						array(
							'name'		=> __( 'Background color when hovering over a photo', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ) . $default_setting,
							'id'		=> 'hovercolor',
							'type'		=> 'colorpicker',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'std'		=> ''
						),
			
						array(
							'name'		=> __( 'Text/icon color when hovering over a photo', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ) . $default_setting,
							'id'		=> 'hovertextcolor',
							'type'		=> 'colorpicker',
							'container_class'	=> 'av_half' . $cls_pro,
							'std'		=> ''
						),
			
						array(
							'name'		=> __( 'Information to display when hovering over a photo', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> 'hoverdisplay',
							'type'		=> 'select',
							'std'		=> '',
							'multiple'	=> 6,
							'container_class'	=> $cls_pro,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )			=> '',
												__( 'Username', 'avia_framework' )				=> 'username',
												__( 'Expand Icon', 'avia_framework' )			=> 'icon',
												__( 'Date', 'avia_framework' )					=> 'date',
												__( 'Instagram Icon/Link', 'avia_framework' )	=> 'instagram',
												__( 'Location', 'avia_framework' )				=> 'location',
												__( 'Caption', 'avia_framework' )				=> 'caption',
												__( 'Like/Comment Icons', 'avia_framework' )	=> 'likes'
											)
						)
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Photos Hover Style', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_hover' ), $template );
			
			$c = array(
						array(
							'name'		=> __( 'Show the photo caption', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> 'showcaption',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
				
						array(
							'name'		=> __( 'Number of characters', 'avia_framework' ),
							'desc'		=> __( 'The number of characters of the caption to display. Any number.', 'avia_framework' ) . $default_setting,
							'id'		=> 'captionlength',
							'type'		=> 'input_number',
							'min'		=> 1,
							'step'		=> 1,
							'std'		=> '',
							'container_class'	=> 'av_half' . $cls_pro,
						),
				
						array(
							'name'		=> __( 'Text color of the caption', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ) . $default_setting,
							'id'		=> 'captioncolor',
							'type'		=> 'colorpicker',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Size of caption text', 'avia_framework' ),
							'desc'		=> __( 'The size of the caption text. Any number.', 'avia_framework' ) . $default_setting,
							'id'		=> 'captionsize',
							'type'		=> 'input_number',
							'min'		=> 1,
							'step'		=> 1,
							'std'		=> '',
							'container_class'	=> 'av_half' . $cls_pro,
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Caption', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_caption' ), $template );
			
			$c = array(
						array(
							'name'		=> __( 'Show the Likes & Comments', 'avia_framework' ),
							'desc'		=> __( 'Select to show the Likes & Comments', 'avia_framework' ),
							'id'		=> 'showlikes',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> $cls_pro,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
				
						array(
							'name'		=> __( 'Color of the Likes & Comments', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ) . $default_setting,
							'id'		=> 'likescolor',
							'type'		=> 'colorpicker',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'std'		=> ''
						),
				
						array(
							'name'		=> __( 'Size of Likes & Comments', 'avia_framework' ),
							'desc'		=> __( 'The size of the text. Any number.', 'avia_framework' ) . $default_setting,
							'id'		=> 'likessize',
							'type'		=> 'input_number',
							'min'		=> 1,
							'step'		=> 1,
							'std'		=> '',
							'container_class'	=> 'av_half' . $cls_pro,
						),
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Likes & Comments', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_Likes' ), $template );
			
			
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(
							'name'		=> __( 'Number of rows in the carousel', 'avia_framework' ),
							'desc'		=> __( 'Select the number of rows of photos/videos in the carousel', 'avia_framework' ),
							'id'		=> 'carouselrows',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'required'	=> array( 'layout', 'equals', 'isb-carousel' ),
							'subtype'	=> AviaHtmlHelper::number_array( 1, 2, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						),
			
						array(
							'name'		=> __( 'Carousel Loop Type', 'avia_framework' ),
							'desc'		=> __( 'Select the Carousel Loop Type', 'avia_framework' ),
							'id'		=> 'carouselloop',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half' . $cls_pro,
							'required'	=> array( 'layout', 'equals', 'isb-carousel' ),
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )		=> '',
												__( 'Rewind', 'avia_framework' )			=> 'rewind',
												__( 'Infinitely loop', 'avia_framework' )	=> 'infinity'
											)
						),
			
						array(
							'name'		=> __( 'Show Carousel Navigation Arrows', 'avia_framework' ),
							'desc'		=> __( 'Select to show carousel navigation arrows', 'avia_framework' ),
							'id'		=> 'carouselarrows',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'required'	=> array( 'layout', 'equals', 'isb-carousel' ),
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
			
						array(
							'name'		=> __( 'Show Carousel Pagination', 'avia_framework' ),
							'desc'		=> __( 'Select to show carousel pagination', 'avia_framework' ),
							'id'		=> 'carouselpag',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half' . $cls_pro,
							'required'	=> array( 'layout', 'equals', 'isb-carousel' ),
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
			
						array(
							'name'		=> __( 'Enable Carousel Autoplay', 'avia_framework' ),
							'desc'		=> __( 'Select to enable carousel autoplay', 'avia_framework' ),
							'id'		=> 'carouselautoplay',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'required'	=> array( 'layout', 'equals', 'isb-carousel' ),
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
			
						array(
							'name'		=> __( 'Carousel Interval Time', 'avia_framework' ),
							'desc'		=> __( 'The interval time between slides for autoplay in milliseconds. Any number.', 'avia_framework' ) . $default_setting,
							'id'		=> 'carouseltime',
							'type'		=> 'input_number',
							'min'		=> 1000,
							'step'		=> 1000,
							'std'		=> '',
							'container_class'	=> 'av_half' . $cls_pro,
							'required'	=> array( 'layout', 'equals', 'isb-carousel' ),
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Carousel Slider Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_carousel_slider' ), $template );
			
			
			$c = array(
						array(
							'name'		=> __( 'Highlight', 'avia_framework' ),
							'desc'		=> __( 'Select the type of Highlight for your feed.', 'avia_framework' ),
							'id'		=> 'highlighttype',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> $cls_pro,
							'required'	=> array( 'layout', 'equals', 'isb-highlight' ),
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Pattern', 'avia_framework' )		=> 'pattern',
												__( 'Post ID', 'avia_framework' )		=> 'id',
												__( 'Hashtag', 'avia_framework' )		=> 'hashtag'
											)
						),
			
						array(
							'name'		=> __( 'Highlight Pattern', 'avia_framework' ),
							'desc'		=> __( 'How often a post is highlighted - eg. every 6th post. Any number.', 'avia_framework' ) . $default_setting,
							'id'		=> 'highlightpattern',
							'type'		=> 'input_number',
							'min'		=> 1,
							'step'		=> 1,
							'std'		=> '',
							'required'	=> array( 'layout', 'equals', 'isb-highlight' ),
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
						),
			
						array(
							'name'		=> __( 'Highlight Offset', 'avia_framework' ),
							'desc'		=> __( 'When to start the highlight pattern. Any number.', 'avia_framework' ) . $default_setting,
							'id'		=> 'highlightoffset',
							'type'		=> 'input_number',
							'min'		=> 1,
							'step'		=> 1,
							'std'		=> '',
							'required'	=> array( 'layout', 'equals', 'isb-highlight' ),
							'container_class'	=> 'av_half' . $cls_pro,
						),
			
						array(
							'name'		=> __( 'Highlight Hashtag', 'avia_framework' ),
							'desc'		=> __( 'Highlight posts with these hashtags, seperate multiple hashtags with commas.', 'avia_framework' ) . $default_setting,
							'id'		=> 'highlighthashtag',
							'type'		=> 'input',
							'std'		=> '',
							'required'	=> array( 'layout', 'equals', 'isb-highlight' ),
							'container_class'	=> $cls_pro
						)
				);
			
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Highlight Grid Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_highlight_grid' ), $template );
			
			
			$c = array(
						array(
							'name'		=> __( 'Caption Links', 'avia_framework' ),
							'desc'		=> __( 'Use urls in captions for the photo link instead of linking to instagram.com.', 'avia_framework' ),
							'id'		=> 'captionlinks',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> $cls_pro,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Link Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_links' ), $template );
			
			
			$c = array(
						array(
							'name'		=> __( 'Lightbox', 'avia_framework' ),
							'desc'		=> __( 'Select to disable plugin lightbox and link to Instagram page.', 'avia_framework' ),
							'id'		=> 'disablelightbox',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> $cls_pro,
							'subtype'	=> array( 
												__( 'Use plugin lightbox', 'avia_framework' )			=> '',
												__( 'Open Instagram in new window', 'avia_framework' )	=> 'true'
											)
						),
				
						array(
							'name'		=> __( 'Show comments in the lightbox', 'avia_framework' ),
							'desc'		=> __( 'Select to show comments in the lightbox.', 'avia_framework' ),
							'id'		=> 'lightboxcomments',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
			
						array(
							'name'		=> __( 'Number of comments to show', 'avia_framework' ),
							'desc'		=> __( 'Enter the number of comments to show in the lightbox. Any number.', 'avia_framework' ) . $default_setting,
							'id'		=> 'numcomments',
							'type'		=> 'input_number',
							'min'		=> 1,
							'step'		=> 1,
							'std'		=> '',
							'container_class'	=> 'av_half' . $cls_pro
						)
			
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Lightbox', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_lightbox' ), $template );
			
			$c = array(
						array(
							'name'		=> __( 'Autoscroll', 'avia_framework' ),
							'desc'		=> __( 'Select to load more content automatically as the user scrolls down the page.', 'avia_framework' ),
							'id'		=> 'autoscroll',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
				
						array(
							'name'		=> __( 'Autoscroll distance', 'avia_framework' ),
							'desc'		=> __( 'Enter the distance before the end of feed or page that triggers the loading of more content. Any number.', 'avia_framework' ) . $default_setting,
							'id'		=> 'autoscrolldistance',
							'type'		=> 'input',
							'container_class'	=> 'av_half' . $cls_pro,
							'std'		=> '',
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Auto Load More on Scroll', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_autoscroll' ), $template );
			
			
			$c = array(
						array(
							'name'		=> __( 'Disable the mobile layout', 'avia_framework' ),
							'desc'		=> __( 'Select to disable the mobile layout', 'avia_framework' ),
							'id'		=> 'disablemobile',
							'type'		=> 'select',
							'std'		=> '',
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						),
			
						array(
							'name'		=> __( 'Display initially on mobile', 'avia_framework' ),
							'desc'		=> __( 'The number of photos/videos to display initially for mobile screens.', 'avia_framework' ),
							'id'		=> 'nummobile',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half av_half_first' . $cls_pro,
							'subtype'	=> AviaHtmlHelper::number_array( 1, 33, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						),
				
						array(
							'name'		=> __( 'Number of columns on mobile', 'avia_framework' ),
							'desc'		=> __( 'The number of columns in your feed for mobile screens.', 'avia_framework' ),
							'id'		=> 'colsmobile',
							'type'		=> 'select',
							'std'		=> '',
							'container_class'	=> 'av_half' . $cls_pro,
							'subtype'	=> AviaHtmlHelper::number_array( 1, 10, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Mobile Settings (smaller than 480 pixels)', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_mobile' ), $template );
		}

	
		/**
		 * Frontend Shortcode Handler
		 *
		 * @since 4.7.3.1
		 * @param array $atts array of attributes
		 * @param string $content text within enclosing form of shortcode element 
		 * @param string $shortcodename the shortcode found, when == callback name
		 * @return string $output returns the modified html string 
		 */
		public function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
			extract( AviaHelper::av_mobile_sizes( $atts ) );	//return $av_font_classes, $av_title_font_classes and $av_display_classes 
			
			$default = AviaInstagramFeed()->get_default_sc_params();
			
			/**
			 * Use apply_filters( "shortcode_atts_{$shortcode}", $out, $pairs, $atts, $shortcode ); to extend attributes not supported in ALB options:
			 * 
			 * add_filter( 'shortcode_atts_av_sb_instagram_feed', 'custom_shortcode_atts_av_sb_instagram_feed', 10, 4 );
			 * @since 4.7.3.1
			 */
			$atts = shortcode_atts( $default, $atts, $this->config['shortcode'] );
			
			/**
			 * Reset options if not pro version active
			 */
			if( ! AviaInstagramFeed()->is_proversion_active() )
			{
				$atts['type'] = 'user';
				$atts['layout'] = 'grid';
			}
			
			$sc = AviaInstagramFeed()->create_shortcode( $atts );
			
			avia_sc_sb_instagram_feed::$feed_count ++;
			$lightbox = ( '' == $atts['disablelightbox'] ) ? 'noLightbox' : '';
			$el_id = ! empty( $meta['custom_el_id'] ) ? $meta['custom_el_id'] : 'id="avia-sb-instagram-feed-' . avia_sc_sb_instagram_feed::$feed_count . '"';
			
			$output = '';
			
			$output .= "<div {$el_id} class='sb-instagram-feed {$av_display_classes} {$meta['el_class']} {$lightbox}'>";
			$output .=		do_shortcode( $sc );
			$output .= '</div>';
			
			return $output;
		}
		
	}
}