<?php
/**
 * Audio File Playlist Element
 * 
 * Shortcode that allows to add an audio playlist
 * 
 * @since 4.1.3
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if ( ! class_exists( 'avia_sc_audio_player' ) ) 
{
	
	class avia_sc_audio_player extends aviaShortcodeTemplate
	{
		/**
		 *
		 * @since 4.1.3
		 * @var int 
		 */
		static protected $instance = 1;
		
		/**
		 *
		 * @since 4.1.3
		 * @var string 
		 */
		static protected $non_ajax_style = '';
		
		/**
		 *
		 * @since 4.1.3
		 * @var array 
		 */
		protected $atts;

		/**
		 * 
		 * @since 4.1.3
		 * @param AviaBuilder $builder
		 */
		public function __construct( $builder )
		{
			parent::__construct( $builder );
			
			$this->atts = array();
		}
		
		/**
		 * 
		 * @since 4.1.3
		 */
		public function __destruct()
		{
			parent::__destruct();
			
			unset( $this->atts );
		}
		
		/**
		 * Create the config array for the shortcode button
		 * 
		 * @since 4.1.3
		 */
		public function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';
			$this->config['base_element']	= 'yes';
			
			$this->config['name']			= __( 'Audio Player', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-audio-player.png';
			$this->config['order']			= 90;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_player';
			$this->config['shortcode_nested'] = array( 'av_playlist_element' );
			$this->config['tooltip'] 	    = __( 'Add an audio player element', 'avia_framework' );
			$this->config['tinyMCE'] 		= array( 'disable' => 'true' );
			$this->config['drag-level'] 	= 3;
			$this->config['preview']		= false;
			$this->config['disabling_allowed'] = true;
			$this->config['disabled']		= array(
									'condition'	=> ( avia_get_option( 'disable_mediaelement' ) == 'disable_mediaelement' ), 
									'text'		=> __( 'This element is disabled in your theme options. You can enable it in Enfold &raquo; Performance', 'avia_framework' )
								);
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';				//	we use original code - not $meta										

		}
		
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-audioplayer', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/audio-player/audio-player.css', array( 'avia-layout' ), false );
			
				//load js
			wp_enqueue_script( 'avia-module-audioplayer', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/audio-player/audio-player.js', array( 'avia-shortcodes' ), false, true );
		}
		
		
		/**
		* Popup Elements
		*
		* If this function is defined in a child class the element automatically gets an edit button, that, when pressed
		* opens a modal window that allows to edit the element properties
		*
		* @since 4.1.3
		* @return void
		*/
		public function popup_elements()
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
													$this->popup_key( 'content_playlist' ),
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
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array(
													$this->popup_key( 'styling_player' ),
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
								'type'				=> 'modal_group', 
								'id'				=> 'content',
								'modal_title'		=> __( 'Edit Form Element', 'avia_framework' ),
								'add_label'			=> __( 'Add single audio', 'avia_framework' ),
								'modal_open'		=> 'no',
								'trigger_button'	=> 'avia-builder-audio-edit',
								'disable_manual'	=> 'yes',
								'container_class'	=> 'avia-element-fullwidth avia-multi-img',
//								'editable_item'		=> true,
								'lockable'			=> true,
								'tmpl_set_default'	=> false,
								'std'				=> array(),
								'creator'			=> array(
															'name'		=> __( 'Create and Edit Audio Playlist', 'avia_framework' ),
															'desc'		=> __( 'Here you can add new audio files to the playlist, remove files or reorder them.', 'avia_framework' ),
															'id'		=> 'id',
															'type'		=> 'audio_player',
															'state'		=> 'avia_insert_multi_audio',
															'title'		=> __( 'Add/Edit Audio Files', 'avia_framework' ),
															'button'	=> __( 'Insert Audio Files', 'avia_framework' ),
															'std'		=> ''
														),
								'subelements'		=> $this->create_modal()
						),
				
						array(	
							'name'			=> __( 'Autoplay', 'avia_framework' ),
							'desc'			=> __( 'Choose if the player starts on pageload or has to be started manually', 'avia_framework' ),
							'id'			=> 'autoplay',
							'type'			=> 'select',
							'std'			=> '',
							'lockable'		=> true,
							'subtype'		=> array(
													__( 'Start manually', 'avia_framework' )	=> 'manual',
													__( 'Start on pageload', 'avia_framework' )	=> 'autoplay'
												)
						),
				
						array(	
							'name'			=> __( 'Loop playlist', 'avia_framework' ),
							'desc'			=> __( 'Choose if you want to stop after playing the list once or if you want to continue from beginning again. <strong>Since WP 5.2 Firefox does not stop when Enfold javascript file merging and compression is enabled. Other browsers work as expected.</strong>', 'avia_framework' ),
							'id'			=> 'loop',
							'type'			=> 'select',
							'std'			=> '',
							'lockable'		=> true,
							'subtype'		=> array(
													__( 'Start from beginning again', 'avia_framework' )	=> '',
													__( 'Stop after playing last song', 'avia_framework' )	=> 'avia-playlist-no-loop'
												)
						),
				
						array(	
							'name'			=> __( 'Playlist Order', 'avia_framework' ),
							'desc'			=> __( 'Here you can select how to sort the playlist when rendering to the player on each pageload.', 'avia_framework' ),
							'id'			=> 'playorder',
							'type'			=> 'select',
							'std'			=> '',
							'lockable'		=> true,
							'subtype'		=> array(
													__( 'Use order of playlist as selected', 'avia_framework' )	=> 'normal',
													__( 'Shuffle the playlist randomly', 'avia_framework' )		=> 'shuffle',
													/*__( 'Reverse the playlist', 'avia_framework' )			=> 'reverse'*/
												)
						)
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Playlist', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_playlist' ), $template );
			
			$c = array(
						
						array(	
							'name'			=> __( 'Choose a Cover Image', 'avia_framework' ),
							'desc'			=> __( 'Either upload a new or choose an existing image from your media library', 'avia_framework' ),
							'id'			=> 'cover_id',
							'fetch'			=> 'id',
							'type'			=> 'image',
							'title'			=> __( 'Choose a Cover Image', 'avia_framework' ),
							'button'		=> __( 'Choose a Cover Image', 'avia_framework' ),
							'std'			=> '',
							'lockable'		=> true,
						),
				
/*
						array(	
							'name'			=> __( 'Cover Image Location', 'avia_framework' ),
							'desc'			=> __( 'Here you can select where to show the cover for larger screens. On mobile devices the image will be centered above the player by default.', 'avia_framework' ),
							'id'			=> 'cover_location',
							'type'			=> 'select',
							'std'			=> 'top left',
							'lockable'		=> true,
							'subtype'		=> array(
													__( 'Hide the cover image', 'avia_framework' )				=> 'hide',
													__( 'Show above player left aligned', 'avia_framework' )	=> 'top left',
													__( 'Show above player centered', 'avia_framework' )		=> 'top center',
													__( 'Show above player right aligned', 'avia_framework' )	=> 'top right',
													__( 'Show left of player', 'avia_framework' )				=> 'aside left',
													__( 'Show right of player', 'avia_framework' )				=> 'aside right'
												)
						),	
						
						array(	
							'name'			=> __( 'Cover Image Size', 'avia_framework' ),
							'desc'			=> __( 'Choose image size for your cover.', 'avia_framework' ),
							'id'			=> 'cover_size',
							'type'			=> 'select',
							'std'			=> 'thumbnail',
							'lockable'		=> true,
							'required'		=> array( 'cover_location', 'not', 'hide' ),
							'subtype'		=>  AviaHelper::get_registered_image_sizes( array(), false, true )		
						),

				
						
						
						array(	
							'name'			=> __( 'Playlist styling', 'avia_framework' ),
							'desc'			=> __( 'Here you can select the styling of the playlist', 'avia_framework' ),
							'id'			=> 'playlist_style',
							'type'			=> 'select',
							'std'			=> 'light',
							'lockable'		=> true,
							'subtype'		=> array(
													__( 'Light', 'avia_framework' )	=> 'light',
													__( 'Dark', 'avia_framework' )	=> 'dark'
												)
						),	
						
						*/
				
						array(	
							'name'			=> __( 'Tracklist', 'avia_framework' ),
							'desc'			=> __( 'Here you can select to show or hide the tracklist', 'avia_framework' ),
							'id'			=> 'tracklist',
							'type'			=> 'select',
							'std'			=> 'show',
							'lockable'		=> true,
							'subtype'		=> array(
													__( 'Show tracklist', 'avia_framework' )	=> 'show',
													__( 'Hide tracklist', 'avia_framework' )	=> 'hide'
												)
						),	
				
						array(	
							'name'			=> __( 'Tracknumbers', 'avia_framework' ),
							'desc'			=> __( 'Here you can select to show or hide the tracknumbers next to entries in the playlist', 'avia_framework' ),
							'id'			=> 'tracknumbers',
							'type'			=> 'select',
							'std'			=> 'show',
							'lockable'		=> true,
							'required'		=> array( 'tracklist', 'equals', 'show' ),
							'subtype'		=> array(
													__( 'Show tracknumbers', 'avia_framework' )	=> 'show',
													__( 'Hide tracknumbers', 'avia_framework' )	=> 'hide'
												)
						),	
				
						array(	
							'name'			=> __( 'Artists Name', 'avia_framework' ),
							'desc'			=> __( 'Here you can select to show or hide the artists name in the playlist', 'avia_framework' ),
							'id'			=> 'artists',
							'type'			=> 'select',
							'std'			=> 'show',
							'lockable'		=> true,
							'required'		=> array( 'tracklist', 'equals', 'show' ),
							'subtype'		=> array(
													__( 'Show artists name', 'avia_framework' )	=> 'show',
													__( 'Hide artists name', 'avia_framework' )	=> 'hide'
												)
						),	
				
						array(	
							'name'			=> __( 'Media Icon/Album Cover', 'avia_framework' ),
							'desc'			=> __( 'Here you can select to show or hide the media icon in the playlist. This icon can be set in the media gallery for each element as the featured image. WP will use a default icon on upload, if none is set.', 'avia_framework' ),
							'id'			=> 'media_icon',
							'type'			=> 'select',
							'std'			=> 'show',
							'lockable'		=> true,
							'subtype'		=> array(
													__( 'Show media icon/album cover', 'avia_framework' )		=> 'show',
													__( 'Hide', 'avia_framework' ) => 'hide'
												)
						),
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Player', 'avia_framework' ),
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
							'name'			=> __( 'Player styling', 'avia_framework' ),
							'desc'			=> __( 'Here you can select the general appearance of the player', 'avia_framework' ),
							'id'			=> 'player_style',
							'type'			=> 'select',
							'std'			=> 'classic',
							'lockable'		=> true,
							'subtype'		=> array(
													__( 'Classic (boxed)', 'avia_framework' )						=> 'classic',
													__( 'Minimal (borderless, no background)', 'avia_framework' )	=> 'minimal',
												)
							),	
				
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Player', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_player' ), $template );
			
			
			$c = array(
						array(	
							'name'			=> __( 'Font Color', 'avia_framework' ),
							'desc'			=> __( 'Select a font color', 'avia_framework' ),
							'id'			=> 'font_color',
							'type'			=> 'select',
							'std'			=> '',
							'lockable'		=> true,
							'subtype'		=> array( 
													__( 'Default Color', 'avia_framework' )	=> '', 
													__( 'Custom Color', 'avia_framework' )	=> 'custom-font-color'
												)
						), 
					
						array(	
							'name'			=> __( 'Custom Font Color', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font color for your Player here', 'avia_framework' ),
							'id'			=> 'custom_font_color',
							'type'			=> 'colorpicker',
							'rgba'			=> true,
							'std'			=> '',
							'lockable'		=> true,
							'required'		=> array( 'font_color', 'equals', 'custom-font-color' )
						),
				
						array(	
							'name'			=> __( 'Background Color', 'avia_framework' ),
							'desc'			=> __( 'Select a background color', 'avia_framework' ),
							'id'			=> 'background_color',
							'type'			=> 'select',
							'std'			=> '',
							'lockable'		=> true,
							'subtype'		=> array( 
													__( 'Default Color', 'avia_framework' )	=> '', 
													__( 'Custom Color', 'avia_framework' )	=> 'custom-background-color'
												)
						), 
				
						array(	
							'name'			=> __( 'Custom Background Color', 'avia_framework' ),
							'desc'			=> __( 'Select a custom background color for your Player here', 'avia_framework' ),
							'id'			=> 'custom_background_color',
							'type'			=> 'colorpicker',
							'rgba'			=> true,
							'std'			=> '',
							'lockable'		=> true,
							'required'		=> array( 'background_color', 'equals', 'custom-background-color' )
						),
						
						array(	
							'name'			=> __( 'Border Color', 'avia_framework' ),
							'desc'			=> __( 'Select a border color', 'avia_framework' ),
							'id'			=> 'border_color',
							'type'			=> 'select',
							'std'			=> '',
							'lockable'		=> true,
							'subtype'		=> array( 
													__( 'Default Color', 'avia_framework' )	=> '', 
													__( 'Custom Color', 'avia_framework' )	=> 'custom-border-color'
												)
						), 
				
						array(	
							'name'			=> __( 'Custom Border Color', 'avia_framework' ),
							'desc'			=> __( 'Select a custom background color for your Player here', 'avia_framework' ),
							'id'			=> 'custom_border_color',
							'type'			=> 'colorpicker',
							'rgba'			=> true,
							'std'			=> '',
							'lockable'		=> true,
							'required'		=> array( 'border_color', 'equals', 'custom-border-color' )
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
					
					//	Dummy element only to avoid notices
					array(	
							'name'		=> __('Which type of media is this?', 'avia_framework' ),
							'id'		=> 'audio_type',
							'type'		=> 'select',
							'std'		=> 'audio',
							'subtype'	=> array(   
												__( 'Audio File', 'avia_framework' )	=> 'audio',
												__( 'Video File', 'avia_framework' )	=> 'video',
											)
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
			
			return $elements;
		}
		
		
		/**
		 * Editor Element - this function defines the visual appearance of an element on the AviaBuilder Canvas
		 * Most common usage is to define some markup in the $params['innerHtml'] which is then inserted into the drag and drop container
		 * Less often used: $params['data'] to add data attributes, $params['class'] to modify the className
		 *
		 *
		 * @since 4.1.3
		 * @param array $params			this array holds the default values for $content and $args. 
		 * @return $params				the return array usually holds an innerHtml key that holds item specific markup.
		 */
		public function editor_element( $params )
		{	
			$element = $this->get_popup_element_by_id( 'autoplay' );
			
			/**
			 * Element has been disabled with option 'Disable self hosted video and audio features'
			 */
			if( false === $element )
			{
				return $params;
			}
			
			$playmodes = $element['subtype'];
			
			$update_template =	'<span class="av-player-{{autoplay}}">';
			
			foreach( $playmodes as $info => $playmode )
			{
				$update_template .=		'<span class="av-play-' . $playmode . '">' . $info . '</span>';
			}
			
			$update_template .=	'</span>';
			
			
			$default = array();
			$locked = array();
			$attr = $params['args'];
			Avia_Element_Templates()->set_locked_attributes( $attr, $this, $this->config['shortcode'], $default, $locked );
			
			$update	= $this->update_template_lockable( 'autoplay', $update_template, $locked );
			
			$selected = empty( $attr['autoplay'] ) ? 'manual' : $attr['autoplay'];
			$template = str_replace('{{autoplay}}', $selected, $update_template );
								
			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<img src='{$this->config['icon']}' title='{$this->config['name']}' />";
			$params['innerHtml'] .= "<div class='av-player' data-update_element_template='yes'>";
			$params['innerHtml'] .=		"{$this->config['name']} -  <span {$update}>{$template}</span>";
			$params['innerHtml'] .= '</div>';
					
			return $params;
		}
		
		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * 
		 * @since 4.1.3
		 * @param array $params		this array holds the default values for $content and $args. 
		 * @return array			the return array usually holds an innerHtml key that holds item specific markup.
		 */
		public function editor_sub_element( $params )
		{	
			$default = array();
			$locked = array();
			$attr = $params['args'];
			$content = $params['content'];
			Avia_Element_Templates()->set_locked_attributes( $attr, $this, $this->config['shortcode_nested'][0], $default, $locked, $content );
			
			$img_template = $this->update_option_lockable( array( 'cover_id', 'img_fakeArg' ), $locked );
			$title = $this->update_option_lockable( 'title_info', $locked );
			$artist = $this->update_option_lockable( 'artist', $locked );
			$album = $this->update_option_lockable( 'album', $locked );
			$description = $this->update_option_lockable( 'description', $locked );
			$filename = $this->update_option_lockable( 'filename', $locked );
			$id = $this->update_option_lockable( 'id', $locked );
			$filelength = $this->update_option_lockable( 'filelength', $locked );
			
			$title_info = isset( $attr['title'] ) ? $attr['title'] : '';
			$thumbnail = isset( $attr['icon'] ) ? '<img src="' . $attr['icon'] .  '" title="' . esc_attr(  $title_info ) . '" alt="" />' : '';
			$album_info = isset( $attr['album'] ) && 'undefined' != $attr['album'] ?  $attr['album'] : '';
			$desc_info = isset( $attr['description'] ) && 'undefined' != $attr['description'] ?  $attr['description'] : '';
			$file_info = isset( $attr['filename'] ) && 'undefined' != $attr['filename'] ?  $attr['filename'] : '';
			$id_info = isset( $attr['id'] ) && 'undefined' != $attr['id'] ? $attr['id'] : '0';
			
			$main = '<span class="avia-audiolist-title" '. $title . '>';
			
			if( isset( $attr['title'] ) && 'undefined' != $attr['title'] )
			{
				$main .=	'<span class="avia-known-title">' . $attr['title'];
			}
			else
			{
				$main .=	'<span class="avia-unknown-title">' . __( ' Unknown ' , 'avia_framework' );
			}
			$main .=		'</span></span>';	
			
			/**
			 * Currently WP does not return artist when editing an existing playlist in popup playlist editor.
			 * 
			 * This might change in future -> then uncomment the following lines to show the artist
			 */
//			$main .=		'<span class="avia_audiolist-by">' . __( ' by ' , 'avia_framework' ) . '</span>';
//			
//			if( isset( $attr['artist'] ) && 'undefined' != $attr['artist'] )
//			{
//				$main .=	'<span class="avia-audiolist-artist" ' . $artist . '>' . $attr['artist'];
//			}
//			else
//			{
//				$main .=	'<span class="avia-audiolist-artist avia-unknown" ' . $artist . '>' . __( ' unknown ' , 'avia_framework' );
//			}
//			$main .= '</span>';
			
			if( isset( $attr['filelength'] ) )
			{
				$main .=	' (<span class="avia-audiolist-length" ' . $filelength . '>' . $attr['filelength'];
			}
			else
			{
				$main .=	' (<span class="avia-audiolist-length avia-unknown" ' . $filelength . '>' . __( ' ??? ' , 'avia_framework' );
			}
			$main .= '</span>)';
			

			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_title_container' data-update_element_template='yes'>";
			$params['innerHtml'] .=		'<div ' . $this->class_by_arguments_lockable( 'audio_type', $attr, $locked ) . '>';
			$params['innerHtml'] .=			"<span class='avia_audiolist_image' {$img_template} >{$thumbnail}</span>";
			$params['innerHtml'] .=			"<div class='avia_audiolist_content'>";
			$params['innerHtml'] .=				"<h4 class='avia_title_container_inner'>{$main}</h4>";
			$params['innerHtml'] .=				"<p class='avia_content_album' {$album}>" . stripslashes( $album_info ) . '</p>';
			$params['innerHtml'] .=				"<p class='avia_content_description' {$description}>" . stripslashes( $desc_info ) . '</p>';
			$params['innerHtml'] .=				"<small class='avia_audio_url' {$filename}>" . stripslashes( $file_info ) . '</small>';
			$params['innerHtml'] .=			'</div>';
			$params['innerHtml'] .=			"<div class='hidden-attachment-id' style='display: none;' {$id}>{$id_info}</div>";
			$params['innerHtml'] .=		'</div>';
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
						'id'				=> '',
						'autoplay'			=> 'manual',
						'loop'				=> '',
						'playorder'			=> 'normal',
						'player_style'		=> 'classic',
						'cover_id'			=> '',
						'cover_size'		=> 'thumbnail',
						'cover_location'	=> 'hide',
						'playlist_style'	=> 'light',
						'tracklist'			=> 'show',
						'tracknumbers'		=> 'show',
						'artists'			=> 'show',
						'media_icon'		=> 'show',
						'font_color'		=> '',
						'custom_font_color'	=> '',
						'background_color'	=> '',
						'border_color'		=> '',
						'custom_background_color'	=>'',
						'custom_border_color'		=> ''
					);
			
			$default = $this->sync_sc_defaults_array( $default );
					
			$locked = array();
			Avia_Element_Templates()->set_locked_attributes( $atts, $this, $shortcodename, $default, $locked, $content );
			Avia_Element_Templates()->add_template_class( $meta, $atts, $default );
			
			$add = array(
					'handle'	=> $shortcodename,
					'content'	=> ShortcodeHelper::shortcode2array( $content, 1 )
				);
			
			$defaults = array_merge( $default, $add );
			
			$atts = shortcode_atts( $defaults, $atts, $this->config['shortcode'] );
			
			foreach( $atts['content'] as &$item ) 
			{
				$item_def = $this->get_default_modal_group_args();
				Avia_Element_Templates()->set_locked_attributes( $item['attr'], $this, $this->config['shortcode_nested'][0], $item_def, $locked, $item['content'] );
			}
			
			unset( $item );
			
		
			//replace some values that are removed for simplicity with defaults. can be later changed if user request those features
			$atts['cover_location'] = 'hide';
			$atts['playlist_style'] = 'light';
			
			if( $atts['media_icon'] == 'cover') 
			{
				$atts['media_icon'] = 'show';
			}
			
			if( empty( $atts['player_style'] ) )
			{
				$atts['player_style'] = 'classic';
			}
					
			$classes = array( 
								'av-player',
								'av-player-container',
								$element_id,
								'avia-playerstyle-' . $atts['player_style'],
								$shortcodename . '-' . avia_sc_audio_player::$instance
							);
			
			$element_styling->add_classes( 'container', $classes );
			
			$element_styling->add_classes_from_array( 'container', $meta, 'el_class' );
			
			if( 'hide' == $atts['tracklist'] )
			{
				$element_styling->add_classes( 'container', 'av-player-hide-playlist' );
			}
			
			if( 1 === avia_sc_audio_player::$instance )
			{
				$element_styling->add_classes( 'container', 'first' );
			}
			
			if( ! empty( $atts['loop'] ) )
			{
				$element_styling->add_classes( 'container', $atts['loop'] );
			}
			
			if( $atts['autoplay'] == 'autoplay' )
			{
				$element_styling->add_classes( 'container', 'avia-playlist-autoplay' );
			}
			
			if( ( $atts['font_color'] == 'custom-font-color') && ( ! empty( $atts['custom_font_color'] ) ) )
			{
				$element_styling->add_classes( 'container', 'avia-playlist-' . $atts['font_color'] );
				$element_styling->add_styles( 'container', array( 'color' => $atts['custom_font_color'] ) );
			}
			
			if( ( $atts['background_color'] == 'custom-background-color') && ( ! empty( $atts['custom_background_color'] ) ) )
			{
				$element_styling->add_classes( 'container', 'avia-playlist-' . $atts['background_color'] );
				$element_styling->add_styles( 'container', array( 'background-color' => $atts['custom_background_color'] ) );
			}
			
			if( ( $atts['border_color'] == 'custom-border-color') && ( ! empty( $atts['custom_border_color'] ) ) )
			{
				$element_styling->add_classes( 'container', 'avia-playlist-' . $atts['border_color'] );
				$element_styling->add_styles( 'container', array( 'border-color' => $atts['custom_border_color'] ) );
				$element_styling->add_styles( 'item', array( 'border-color' => $atts['custom_border_color'] ) );
			}
			
			if( ( $atts['cover_location'] != 'hide' ) && ( ! empty( $atts['cover_id'] ) ) )
			{
				$element_styling->add_classes( 'container', 'avia-playlist-show-cover' );
				$element_styling->add_classes( 'container', $atts['cover_location'] );
			}
			else
			{
				$element_styling->add_classes( 'container', 'avia-playlist-hide-cover' );
			}
			
			$selectors = array(
							'container'	=> ".av-player.{$element_id}",
							'item'		=> "#top #wrap_all .av-player.{$element_id} .wp-playlist-item"
				);
			
			$element_styling->add_selectors( $selectors );
			
					
			$result['default'] = $default;
			$result['atts'] = $atts;
			$result['content'] = $content;
			
			return $result;
		}
			
		/**
		 * Frontend Shortcode Handler
		 *
		 * @since 4.1.3
		 * @param array $atts array of attributes
		 * @param string $content text within enclosing form of shortcode element 
		 * @param string $shortcodename the shortcode found, when == callback name
		 * @return string $output returns the modified html string 
		 */
		public function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
			$result = $this->get_element_styles( compact( array( 'atts', 'content', 'shortcodename', 'meta' ) ) );
			
			extract( $result );
			
			$this->atts = $atts;
			
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 
			
			if( empty( $this->atts['id'] ) )
			{
				$this->atts['id'] = $this->config['shortcode'] . '-' . avia_sc_audio_player::$instance;
			}
			
			avia_sc_audio_player::$instance++;
			
			
			extract( $this->atts );
			
			/**
			 * Return if no playlist defined
			 */
			if( empty( $content ) )
			{
				return '';
			}
			
			$ids = array();
			foreach( $content as $key => $audio )
			{
				$ids[] = $audio['attr']['id'];
			}
			
			if( 'shuffle' == $playorder )
			{
				shuffle( $ids );
			}
			else if( 'reverse' == $playorder )
			{
				$ids = array_reverse( $ids );
			}
			
			/**
			 * With WP 5.2 we need to show tracklist and hide with CSS to allow stop of loop
			 */
			$args = array(
					'type'          => 'audio',
					'ids'			=> $ids,
					'style'         => empty( $playlist_style ) ? 'classic' : $playlist_style,
					'tracklist'     => true,			
					'tracknumbers'  => empty( $tracknumbers ) || ( 'hide' != $tracknumbers )  ? true : false,
					'images'        => empty( $media_icon) || ( 'hide' != $media_icon )  ? true : false,
					'artists'       => empty( $artists ) || ( 'hide' != $artists )  ? true : false
				);
			
			
			if( ( $media_icon == 'show' ) && ( is_numeric( $cover_id  ) ) )
			{
				add_filter( 'wp_get_attachment_image_src', array( $this, 'handler_wp_get_attachment_image_src' ), 10, 4 );
				add_filter( 'wp_mime_type_icon', array( $this, 'handler_wp_mime_type_icon' ), 10, 3 );
			}
			
			$player = wp_playlist_shortcode( $args );
			
			if( ( $media_icon == 'show' ) && ( is_numeric( $cover_id  ) ) )
			{
				remove_filter( 'wp_get_attachment_image_src', array( $this, 'handler_wp_get_attachment_image_src' ), 10 );
				remove_filter( 'wp_mime_type_icon', array( $this, 'handler_wp_mime_type_icon' ), 10 );
			}
			
			$cover = '';
			if( ( $cover_location != 'hide' ) && ( ! empty( $cover_id ) ) )
			{
				$cover = wp_get_attachment_link( $cover_id, $cover_size ); 
			}
			
			$style_tag = $element_styling->get_style_tag( $element_id );
			$container_class = $element_styling->get_class_string( 'container' );
			
			
			$output  = '';
			$output .= $style_tag;
			$output .= '<div id="' . $id . '" class="' . $container_class . ' ' .  $av_display_classes . '">';
			
			if( ! empty( $cover ) )
			{
				$output .=	'<div class="av-player-cover-container">';
				$output .=		'<div class="av-player-cover">' . $cover . '</div>';
				$output .=	'</div>';
			}
			
			$output .=		'<div class="av-player-player-container">';
			$output .=			$player;
			$output .=		'</div>';
			
			$output .= '</div>';
			
		
			return $output;
		}
		
		
		/**
		 * If user uploads an image for tbe player this image will overwrite any preset featured image.
		 * To speed up the code the filter should only be activated when images should be exchanged
		 * 
		 * @since 4.1.3
		 * @param array|false  $image         Either array with src, width & height, icon src, or false.
		 * @param int          $attachment_id Image attachment ID.
		 * @param string|array $size          Size of image. Image size or array of width and height values
		 *                                    (in that order). Default 'thumbnail'.
		 * @param bool         $icon          Whether the image should be treated as an icon. Default false.
		 * @return array|false
		 */
		public function handler_wp_get_attachment_image_src( $image, $attachment_id, $size, $icon )
		{
			static $recursive = 0;
			
			/**
			 * To avoid duplicating code we call the original function. Avoid endless recursions.
			 */
			if( $recursive > 0 )
			{
				return $image;
			}
			
			$new_id = $this->atts['cover_id'];
			
			if( empty( $new_id ) || ( ! is_numeric( $new_id ) ) || ( (int) $new_id == $attachment_id ) )
			{
				return $image;
			}
			
			$recursive++;
			
			$image = wp_get_attachment_image_src( $new_id, $size, $icon );
			
			$recursive--;
			
			return $image;
		}
		
		/**
		 * If user uploads an image for tbe player this image will overwrite the default mime icon from WP if no featured image was assigned
		 * to the media file.
		 * To speed up the code the filter should only be activated when images should be exchanged
		 * 
		 * @since 4.1.3
		 * @param string	$icon
		 * @param string	$mime
		 * @param int		$post_id
		 * @return string
		 */
		public function handler_wp_mime_type_icon( $icon, $mime, $post_id )
		{
			$new_id = $this->atts['cover_id'];
			
			if( empty( $new_id ) || ! is_numeric( $new_id ) )
			{
				return $icon;
			}
			
			$image = wp_get_attachment_image_src( $new_id, 'thumbnail', false );
			return is_array( $image ) ? $image[0] : '';
		}

	}		//	end class definition
	
}
