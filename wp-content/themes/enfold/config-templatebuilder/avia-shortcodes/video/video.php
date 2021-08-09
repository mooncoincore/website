<?php
/**
 * Video
 * 
 * Shortcode which display a video
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_video' ) ) 
{
	class avia_sc_video extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'yes';
			$this->config['base_element']	= 'yes';

			$this->config['name']			= __( 'Video', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-video.png';
			$this->config['order']			= 90;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_video';
		//				$this->config['modal_data']     = array( 'modal_class' => 'mediumscreen' );
			$this->config['tooltip']        = __( 'Display a video', 'avia_framework' );
			$this->config['disabling_allowed'] = false; //only allowed to be disabled by extra options
			$this->config['disabled']		= array(
												'condition'	=> ( avia_get_option( 'disable_mediaelement' ) == 'disable_mediaelement' && avia_get_option( 'disable_video' ) == 'disable_video' ), 
												'text'		=> __( 'This element is disabled in your theme options. You can enable it in Enfold &raquo; Performance', 'avia_framework' )
											);
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}

			
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-video', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/video/video.css', array( 'avia-layout' ), false );

			wp_enqueue_script( 'avia-module-slideshow-video', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow-video.js', array( 'avia-shortcodes' ), false, true );
			wp_enqueue_script( 'avia-module-video', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/video/video.js', array( 'avia-shortcodes' ), false, true );
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
						'name'  => __( 'Content', 'avia_framework' ),
						'nodescription' => true
					),
				
					array(
							'type'			=> 'template',
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array( 
													$this->popup_key( 'content_video' ),
													$this->popup_key( 'content_player' )
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
							'template_id'	=> $this->popup_key( 'styling_format' )
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
								'template_id'	=> $this->popup_key( 'advanced_privacy' )
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
							'type'			=> 'template',
							'template_id'	=> 'video',
							'id'			=> 'src',
							'args'			=> array( 
													'sc'			=> $this,
													'html_5_urls'	=> current_theme_supports( 'avia_template_builder_custom_html5_video_urls' )
												),
							'lockable'		=> true
						),
				
						array(	
							'name' 	=> __( 'Choose a preview/fallback image', 'avia_framework' ),
							'desc' 	=> __( 'Either upload a new, or choose an existing image from your media library', 'avia_framework' ) . '<br/><small>' . __( "Video on most mobile devices can't be controlled properly with JavaScript, so you can upload a fallback image which will be displayed instead. This image is also used if lazy loading is active.", 'avia_framework' ) . '</small>',
							'id' 	=> 'mobile_image',
							'type' 	=> 'image',
							'title' => __( 'Choose Image', 'avia_framework' ),
							'button' => __( 'Choose Image', 'avia_framework' ),
							'std' 	=> '',
							'lockable'	=> true,
							'locked'	=> array( 'src', 'attachment', 'attachment_size' )
						)						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Select Video', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_video' ), $template );
			
			$c = array(
						array(	
							'name' 	=> __( 'Enable Autoplay', 'avia_framework' ),
							'desc' 	=> __( 'Check if you want to enable video autoplay when page is loaded. Videos will be muted by default.', 'avia_framework' ),
							'id' 	=> 'video_autoplay_enabled',
							'type' 	=> 'checkbox',
							'std' 	=> '',
							'lockable'	=> true
						),
		
						
						array(
							'name' 	=> __( 'Mute Video Player', 'avia_framework' ),
							'desc' 	=> __( 'Check if you want to mute the video.', 'avia_framework' ),
							'id' 	=> 'video_mute',
							'type' 	=> 'checkbox',
							'std' 	=> '',
							'lockable'	=> true
						),
							
						array(	
							'name' 	=> __( 'Loop Video Player', 'avia_framework' ),
							'desc' 	=> __( 'Check if you want to loop the video and play it from the beginning again', 'avia_framework' ),
							'id' 	=> 'video_loop',
							'type' 	=> 'checkbox',
							'std' 	=> '',
							'lockable'	=> true
						),
					
						array(	
							'name' 	=> __( 'Hide Video Controls', 'avia_framework' ),
							'desc' 	=> __( 'Check if you want to hide the controls (works for youtube and self hosted videos)', 'avia_framework' ),
							'id' 	=> 'video_controls',
							'type' 	=> 'checkbox',
							'std' 	=> '',
							'lockable'	=> true
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Player Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_player' ), $template );
			
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Video Format', 'avia_framework' ),
							'desc' 	=> __( 'Choose if you want to display a modern 16:9 or classic 4:3 Video, or use a custom ratio', 'avia_framework' ),
							'id' 	=> 'format',
							'type' 	=> 'select',
							'std' 	=> '16:9',
							'lockable'	=> true,
							'subtype'	=> array( 
												__( '16:9',  'avia_framework' ) =>'16-9',
												__( '4:3', 'avia_framework' ) =>'4-3',
												__( 'Custom Ratio', 'avia_framework' ) =>'custom',
											)		
						),
							
						array(	
							'name' 	=> __( 'Video width', 'avia_framework' ),
							'desc' 	=> __( 'Enter a value for the width', 'avia_framework' ),
							'id' 	=> 'width',
							'type' 	=> 'input',
							'std' 	=> '16',
							'lockable'	=> true,
							'required'	=> array( 'format', 'equals', 'custom' )
						),	
						
						array(	
							'name' 	=> __( 'Video height', 'avia_framework' ),
							'desc' 	=> __( 'Enter a value for the height', 'avia_framework' ),
							'id' 	=> 'height',
							'type' 	=> 'input',
							'std' 	=> '9',
							'lockable'	=> true,
							'required'	=> array( 'format', 'equals', 'custom' )
						)
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_format' ), $c );
			
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Lazy Load videos', 'avia_framework' ),
							'desc' 	=> __( 'Option to only load the preview image. The actual video will only be fetched once the user clicks on the image (Waiting for user interaction speeds up the inital pageload).', 'avia_framework' ),
							'id' 	=> 'conditional_play',
							'type' 	=> 'select',
							'std' 	=> '',
							'lockable'	=> true,
							'subtype'	=> array(
												__( 'Always load videos', 'avia_framework' )		=> '',
												__( 'Wait for user interaction to load the video', 'avia_framework' )		=> 'confirm_all',
												__( 'Show in lightbox - loads after user interaction - preview image recommended', 'avia_framework' )	=> 'lightbox'
											),
						)				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Privacy Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_privacy' ), $template );
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
			
			$template = $this->update_template_lockable( 'src', 'URL: {{src}}', $locked );
			$url = isset( $attr['src'] ) ? $attr['src'] : '';

			$params = parent::editor_element( $params );
			$params['innerHtml'].= "<div class='avia-element-url' data-update_element_template='yes' {$template}> URL: ". $url ."</div>";
			
			$params['content'] = null;
			$params['class'] = "avia-video-element";

			return $params;
		}
			
		/**
		 * Frontend Shortcode Handler
		 *
		 * @param array $atts array of attributes
		 * @param string $content text within enclosing form of shortcode element 
		 * @param string $shortcodename the shortcode found, when == callback name
		 * @param array $meta
		 * @return string $output returns the modified html string 
		 */
		function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
			$default = array(
						'src'				=> '', 
						'src_1'				=> '', 
						'src_2'				=> '', 
						'mobile_image'		=> '',
						'fallback_link'		=> '',
						'format'			=> '16:9', 
						'height'			=> '9', 
						'width'				=> '16',
						'conditional_play'	=> '',
						'video_controls'	=> '',
						'video_mute'		=> '',
						'video_loop'		=> '',
						'video_autoplay_enabled'	=> '',
						'attachment'		=> '',
						'attachment_size'	=> ''
					);
			
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );

			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 
				
			extract( shortcode_atts( $default, $atts, $this->config['shortcode'] ) );

			/**
			 * Autoplay videos must be muted
			 */
			if( ! empty( $video_autoplay_enabled ) )
			{
				$video_mute = 1;
			}
				
			if( 'lightbox_active' != avia_get_option( 'lightbox_active', '' ) && 'lightbox' == $conditional_play  )
			{
				/**
				 * Activate a custom lightbox to show video.
				 * In frontend hook into trigger 'avia-open-video-in-lightbox' (<a> tag containing link to video) to load video in your lightbox
				 * 
				 * @since 4.6.3
				 * @param array $atts array of attributes
				 * @param string $content text within enclosing form of shortcode element 
				 * @param string $shortcodename the shortcode found, when == callback name
				 * @param array $meta
				 * @return boolean
				 */
				if( false === apply_filters( 'avf_show_video_in_custom_lightbox', false, $atts, $content, $shortcodename, $meta ) )
				{
					$conditional_play = 'confirm_all';
				}
			}
				
			$custom_class = ! empty( $meta['custom_class'] ) ? $meta['custom_class'] : '';
			$style = '';
			$html  = '';
			$fallback_img = '';
			$fallback_img_style = '';
			$video_html_raw = '';
			$video_attributes = array( 
									'autoplay'	=> empty( $video_autoplay_enabled ) ? 0 : 1, 
									'loop'		=> empty( $video_loop ) ? 0 : 1, 
									'preload'	=> '', 
									'muted'		=> empty( $video_mute ) ? 0 : 1, 
									'controls'	=> empty( $video_controls ) ? 1 : 0
								);

			if( $attachment )
			{
				$fallback = wp_get_attachment_image_src( $attachment, $attachment_size );
				if( is_array( $fallback ) )
				{
					$fallback_img = $fallback[0];
					$style = "background-image:url(\"{$fallback_img}\");";
				}
			}
				
			if( current_theme_supports( 'avia_template_builder_custom_html5_video_urls' ) && $conditional_play != 'lightbox' )
			{			
				//	In case user does not enter all 3 allow to render existing video formats in html5
				$sources = $this->get_html5_sources( array( $src, $src_1, $src_2 ) );
				
				$html5 = false;
				$html5_files = array();
				$html5_types = array();

				if( ! empty( $sources ) )
				{
					foreach( $sources as $source )
					{
						if( in_array( $source['extension'], array( 'ogv', 'webm', 'mp4' ) ) ) //check for html 5 video
						{
							$html5 = true;
							$html5_files[ $source['extension'] ] = $source['url'];
							$html5_types[ $source['extension'] ] = 'type="video/' . $source['extension'] . '"';
						}
						else
						{
							$video = $source['url'];
							$html5 = false;
							break;
						}
					}
				}

				if( $html5 && ! empty( $sources ) ) //check for html 5 video
				{
					$video_html_raw = avia_html5_video_embed( $html5_files, $fallback_img, $html5_types, $video_attributes );
					$output = $video_html_raw;
					$html = 'avia-video-html5';
					
					
					/**
					 * Removed with 4.6.4 because video shortcode does not support muted videos via attribute
					 * 
					 * Autoplay videos need to be muted !!!
					 * 
					 * Kept for fallback only, can be removed in future versions
					 */
//					$poster = empty( $fallback_img ) ? '' : " poster='{$fallback_img}' ";
//					$loop = empty( $video_loop ) ? ' loop="off" ' : ' loop="on" ';
//					$autoplay = empty( $video_autoplay_enabled ) ? ' autoplay="off" ' : ' autoplay="on" ';
//					$preload = empty( $conditional_play ) ? ' preload="auto" ' : ' preload="metadata" ';
//
//					$video = '';
//					foreach( $sources as $source )
//					{
//						$video .= $source['extension'] . '="' . $source['url'] . '" ';
//					}
//
//					$atts = trim( $video . $poster . $loop . $autoplay . $preload );
//
//					$video_html_raw = do_shortcode( '[video ' . $atts . ' mute="on"]' );
//
//					$output = $video_html_raw;
//					$html = 'avia-video-html5';
				}
				else if( ! empty( $video ) )
				{
					global $wp_embed;

					$video_html_raw = $wp_embed->run_shortcode( '[embed]' . trim( $video ) . '[/embed]' );
					
					/**
					 * In case e.g. [video ..... ] shortcode is returned
					 * 
					 * @since 4.8   do_shortcode added
					 */
					$output = do_shortcode( $video_html_raw );
				}
			}
			else if( $conditional_play != 'lightbox' )
			{
				$file_extension = substr( $src, strrpos( $src, '.' ) + 1 );

				if( in_array( $file_extension, array( 'ogv','webm','mp4' ) ) ) //check for html 5 video
				{
					$video_types = array( 'webm' => 'type="video/webm"', 'mp4' => 'type="video/mp4"', 'ogv' => 'type="video/ogg"' );
					
					$video_html_raw = avia_html5_video_embed( $src, $fallback_img, $video_types, $video_attributes );
					$output = $video_html_raw;

					$html = 'avia-video-html5';
				}
				else
				{
					global $wp_embed;

					$video_html_raw = $wp_embed->run_shortcode( '[embed]' . trim( $src ) . '[/embed]' );
					$output = $video_html_raw;

					if( ! empty( $conditional_play ) )
					{
						//append autoplay so the user does not need to click 2 times
						$video_attributes['autoplay'] = 1;
					} 
					else
					{
						$custom_class .= ' av-lazyload-immediate ';
					}
					
					/**
					 * Add selected video player params to url, does not remove any manually set parameters
					 */
					$match = array();
					preg_match( '!src="(.*?)"!', $output, $match );
					if( isset( $match[1] ) && ( ( false !== strpos( $match[1], 'www.youtube.com/' ) ) || ( false !== strpos( $match[1], 'player.vimeo.com/' ) ) ) )
					{
						$params = array();
						$youtube = false !== strpos( $match[1], 'www.youtube.com/' ) ? true : false;
						
						$params[] = 'autoplay=' . $video_attributes['autoplay'];
						$params[] = 'loop=' . $video_attributes['loop'];
						$params[] = 'controls=' . $video_attributes['controls'];
						$params[] = $youtube ? 'mute=' . $video_attributes['muted'] : 'muted=' . $video_attributes['muted'];
						
						if( ! empty( $params ) )
						{
							$params = implode( '&', $params );
						
							if( strpos( $match[1], '?' ) === false )
							{
								$output = str_replace( $match[1], $match[1] . '?' . $params, $output );
							}
							else
							{
								$output = str_replace( $match[1], $match[1] . '&' . $params, $output );
							}
						}
					}
					
					$output =	"<script type='text/html' class='av-video-tmpl'>{$output}</script>";
					$output .=	"<div class='av-click-to-play-overlay'>";
					$output .=		'<div class="avia_playpause_icon">';
					$output .=		'</div>';
					$output .=	'</div>';

					$custom_class .= ' av-lazyload-video-embed ';
				}
			}
			else
			{
				$custom_class .= ' avia-video-lightbox av-lazyload-video-embed ';

				$overlay  =	"<div class='av-click-to-play-overlay play-lightbox'>";
				$overlay .=		'<div class="avia_playpause_icon">';
				$overlay .=		'</div>';
				$overlay .=	'</div>';

				if( ( false !== stripos( $src, 'youtube.com/watch' ) ) || ( false !== stripos( $src, 'vimeo.com/' ) ) )
				{
					$custom_class .= ' avia-video-external-service ';

					$src .= ( strpos( $src, '?' ) === false ) ? '?autoplay=1' : '&autoplay=1';
					$output  = "<a href='{$src}' class='mfp-iframe lightbox-link'></a>";
				}
				else if( ! empty( $src ) )
				{
					$custom_class .= ' avia-video-standard-html ';

					$output = "<a href='{$src}' rel='lightbox' class='mfp-iframe lightbox-link'></a>";
				}

				if( ! empty( $output ) )
				{
					$output = "<script type='text/html' class='av-video-tmpl'>{$output}</script>";
					$output .= $overlay;
				}
			}
				
			if( $format == 'custom' )
			{
				$height = intval( $height );
				$width  = intval( $width );
				$ratio  = ( 100 / $width ) * $height;
				$style .= "padding-bottom:{$ratio}%;";
			}

			if( ! empty( $style ) )
			{
				$style = "style='{$style}'";
			}
				
			$markup = avia_markup_helper( array( 'context' => 'video', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );

			if( ! empty( $output ) )
			{
				$output = "<div {$meta['custom_el_id']} class='avia-video avia-video-{$format} {$html} {$custom_class} {$av_display_classes}' {$style} {$markup} data-original_url='{$src}' >{$output}</div>";
			}				

			/**
			 * Allow plugins to change output in case they want to handle it by themself.
			 * They must return the complete HTML structure.
			 * 
			 * @since 4.5.7.2
			 * @param string $output
			 * @param array $atts
			 * @param string $content
			 * @param string $shortcodename
			 * @param array|string $meta
			 * @param string $video_html_raw
			 * @return string
			 */
			$output = apply_filters( 'avf_sc_video_output', $output, $atts, $content, $shortcodename, $meta, $video_html_raw );

			return $output;
		}
		
		/**
		 * Check for valid HTML5 videos. If no one found return first file.
		 * 
		 * @since 4.8
		 * @param array $source_files
		 * @return array
		 */
		protected function get_html5_sources( array $source_files )
		{
			$sources = array();
			
			foreach( $source_files as $source_file ) 
			{
				$ext = substr( $source_file, strrpos( $source_file, '.' ) + 1 );
				if( in_array( $ext, array( 'ogv', 'webm', 'mp4' ) ) )
				{
					$sources[] = array(
									'url'		=> $source_file,
									'extension' => $ext
								);
				}
			}
			
			if( empty( $sources ) && isset( $source_files[0] ) )
			{
				$sources[] = array(
								'url'		=> $source_files[0],
								'extension' => substr( $source_files[0], strrpos( $source_files[0], '.' ) + 1 )
							);
			}
			
			return $sources;
		}
			
	}
}
