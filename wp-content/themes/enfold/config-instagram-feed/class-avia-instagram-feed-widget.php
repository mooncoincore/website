<?php
/**
 * Implements the widget
 *
 * @since 4.7.3.1
 * @added_by Günter
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

/**
 * Return if plugin not active
 * 
 * A fallback only to prevent errors if loaded directly
 */
if ( ! class_exists( 'SB_Instagram_Feed' ) )
{
	return;
}

if( ! class_exists( 'Avia_Instagram_Feed_Widget' ) )
{
	class Avia_Instagram_Feed_Widget extends Avia_Widget
	{
		
		/**
		 * @since 4.7.3.1
		 */
		public function __construct() 
		{
			$widget_ops = array( 
							'classname' => 'avia-instagram-smash-ballon', 
							'description' => __( 'Displays your latest Instagram feeds (Smash Balloon Plugin)', 'avia_framework' ) 
						);
			
			parent::__construct(
								'avia-instagram-feed-smash',
								THEMENAME . ' ' . __( 'Instagram (Smash Balloon)', 'avia_framework' ),
								$widget_ops
							);
			
		}
		
		/**
		 * @since 4.7.3.1
		 */
		public function __destruct() 
		{
			parent::__destruct();
		}
		
		/**
		 * Ensure a valid instance array filled with defaults
		 * 
		 * @since 4.7.3.1
		 * @param array $instance
		 * @return array
		 */
		protected function parse_args_instance( array $instance )
		{
			$default = AviaInstagramFeed()->get_default_sc_params( array( 'title' => __( 'Instagram Feeds', 'avia_framework' ) ) );
			
			$new_instance = wp_parse_args( $instance, $default );
			
			/**
			 * @since 4.7.3.1
			 * @param array $new_instance
			 * @param array $instance
			 * @return array
			 */
			$new_instance = apply_filters( 'avf_instagram_feed_smash_widget_parse_arg', $new_instance, $instance );

			return $new_instance;
		}
		
		/**
		 * Output the form in backend
		 * 
		 * @since 4.7.3.1
		 * @param array $instance
		 */
		public function form( $instance ) 
		{
			/**
			 * Events are not cloned when element is added to a widget area - we need to attach classes to add them manually
			 * When element exists on pageload events are correctly attached.
			 * 
			 * IMPORTANT: Must be at the beginning of this function
			 */
			$cls_colorpicker = ! empty( $instance ) ? ' avia-colorpicker-attached' : ' avia-colorpicker-to-attach';
			
			$instance = $this->parse_args_instance( $instance );
			$fields = $this->get_field_names();
			
			foreach( $instance as $key => $value ) 
			{
				if( in_array( $key, $fields ) )
				{
					$instance[ $key ] = esc_attr( $value );
				}
			}
			
			if( ! AviaInstagramFeed()->is_proversion_active() )
			{
				$instance['type'] = 'user';
				$instance['layout'] = 'isb-grid';
			}
			
			$cls_pro = ' avia-instagram-feed-pro-option';
				
			$elements = array();
			
			
			$desc = sprintf( __( 'Leaving options empty will use the settings defined in %s Instagram Feed Plugin Options %s. Not all options are supported. Pro options are only available if you use the pro version of the plugin.', 'avia_framework' ), '<a href="' . admin_url( 'admin.php?page=sb-instagram-feed&tab=customize' ) . '" target="_blank">', '</a>' );
				
			$elements[] = array(
							'name'          => '',
							'desc'          => $desc,
							'id'            => $this->id . '-main-opt-desc',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'avia-primary-heading-instagram',
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Widget Title:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'title' ),
							'id_name'	=> $this->get_field_name( 'title' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'std'		=> $instance['title'],
						);
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'Content', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-content-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-configure',
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Select filter for displaying:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'type' ),
							'id_name'	=> $this->get_field_name( 'type' ),
							'type'		=> 'select',
							'std'		=> $instance['type'],
							'class'		=> 'widefat avia-headline-light avia-no-desc avia-coditional-widget-select' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Display content from a User ID', 'avia_framework' )			=> 'user',
												__( 'Display content from one or more hashtags', 'avia_framework' )	=> 'hashtag',
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'User Name:', 'avia_framework' ),
							'desc'		=> __( 'Your Instagram User Name. This must be from a connected account on the &quot;Configure&quot; tab of the plugin.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'user' ),
							'id_name'	=> $this->get_field_name( 'user' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light opt-user',
							'std'		=> $instance['user'],
						);
			
			$elements[] = array(
							'name'		=> __( 'Hashtag(s):', 'avia_framework' ),
							'desc'		=> __( 'Any hashtag. Separate multiple hashtags by commas (e.g. #awesome,#mytag)', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'hashtag' ),
							'id_name'	=> $this->get_field_name( 'hashtag' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light opt-hashtag' . $cls_pro,
							'std'		=> $instance['hashtag'],
						);
			
			$elements[] = array(
							'name'		=> __( 'Select Media:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'media' ),
							'id_name'	=> $this->get_field_name( 'media' ),
							'type'		=> 'select',
							'std'		=> $instance['media'],
							'class'		=> 'widefat avia-headline-light avia-no-desc' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'All', 'avia_framework' )			=> 'all',
												__( 'Photos only', 'avia_framework' )	=> 'photos',
												__( 'Videos only', 'avia_framework' )	=> 'videos'
											)
						);
			
			
			$elements[] = array(
							'name'		=> __( 'Sort Order:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'sortby' ),
							'id_name'	=> $this->get_field_name( 'sortby' ),
							'type'		=> 'select',
							'std'		=> $instance['sortby'],
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )		=> '',
												__( 'Newest to Oldest', 'avia_framework' )	=> 'none',
												__( 'Random', 'avia_framework' )			=> 'random',
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Display initially:', 'avia_framework' ),
							'desc'		=> __( 'The number of photos/videos to display initially.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'num' ),
							'id_name'	=> $this->get_field_name( 'num' ),
							'type'		=> 'select',
							'std'		=> $instance['num'],
							'class'		=> 'widefat avia-headline-light',
							'no_first'	=> true,
							'subtype'	=> avia_backend_number_array( 1, 33, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						);
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'General Styling', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-general-styling-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-customize',
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Width:', 'avia_framework' ),
							'desc'		=> __( 'The width of your feed. Any number, add px (= default) or %.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'width' ),
							'id_name'	=> $this->get_field_name( 'width' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light',
							'std'		=> $instance['width'],
						);
			
			$elements[] = array(
							'name'		=> __( 'Height:', 'avia_framework' ),
							'desc'		=> __( 'The height of your feed. Any number, add px (= default) or %.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'height' ),
							'id_name'	=> $this->get_field_name( 'height' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light',
							'std'		=> $instance['height'],
						);
			
			$elements[] = array(
							'name'		=> __( 'Number of columns in your feed:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'cols' ),
							'id_name'	=> $this->get_field_name( 'cols' ),
							'type'		=> 'select',
							'std'		=> $instance['cols'],
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'no_first'	=> true,
							'subtype'	=> avia_backend_number_array( 1, 10, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						);
			
			$elements[] = array(
							'name'		=> __( 'Image Resolution:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'imageres' ),
							'id_name'	=> $this->get_field_name( 'imageres' ),
							'type'		=> 'select',
							'std'		=> $instance['imageres'],
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Auto', 'avia_framework' )			=> 'auto',
												__( 'Full', 'avia_framework' )			=> 'full',
												__( 'Medium', 'avia_framework' )		=> 'medium',
												__( 'Thumbnail', 'avia_framework' )		=> 'thumb',
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Background color of the feed', 'avia_framework' ),
							'desc'		=> __( 'Set a custom background color. Any hex color code.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'background' ),
							'id_name'	=> $this->get_field_name( 'background' ),
							'type'		=> 'colorpicker',
							'class'		=> 'widefat avia-headline-light' . $cls_colorpicker,
							'std'		=> $instance['background']
						);
			
			$elements[] = array(
							'name'		=> __( 'Custom CSS Class:', 'avia_framework' ),
							'desc'		=> __( 'Add a CSS class to the feed container', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'class' ),
							'id_name'	=> $this->get_field_name( 'class' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light',
							'std'		=> $instance['class'],
						);
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'Layout', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-layout-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-layout',
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Layout:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'layout' ),
							'id_name'	=> $this->get_field_name( 'layout' ),
							'type'		=> 'select',
							'std'		=> $instance['layout'],
							'class'		=> 'widefat avia-headline-light avia-no-desc avia-coditional-widget-select' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Grid', 'avia_framework' )				=> 'isb-grid',
												__( 'Carousel Slider', 'avia_framework' )	=> 'isb-carousel',
												__( 'Masonry Grid', 'avia_framework' )		=> 'isb-masonry',
												__( 'Highlight Grid', 'avia_framework' )	=> 'isb-highlight'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Spacing:', 'avia_framework' ),
							'desc'		=> __( 'The spacing around your photos. Any number, add px (= default) or %.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'imagepadding' ),
							'id_name'	=> $this->get_field_name( 'imagepadding' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light',
							'std'		=> $instance['imagepadding'],
						);
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'Header', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-header-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-header',
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Show the feed Header:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'showheader' ),
							'id_name'	=> $this->get_field_name( 'showheader' ),
							'type'		=> 'select',
							'std'		=> $instance['showheader'],
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Show the bio in Header:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'showbio' ),
							'id_name'	=> $this->get_field_name( 'showbio' ),
							'type'		=> 'select',
							'std'		=> $instance['showbio'],
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Size of the header:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'headersize' ),
							'id_name'	=> $this->get_field_name( 'headersize' ),
							'type'		=> 'select',
							'std'		=> $instance['headersize'],
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Small', 'avia_framework' )			=> 'small',
												__( 'Medium', 'avia_framework' )		=> 'medium ',
												__( 'Large', 'avia_framework' )			=> 'large ',
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Headercolour:', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'headercolor' ),
							'id_name'	=> $this->get_field_name( 'headercolor' ),
							'type'		=> 'colorpicker',
							'class'		=> 'avia-headline-light' . $cls_colorpicker,
							'std'		=> $instance['headercolor']
						);			
			
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( '&quot;Load More&quot; Button', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-load-more-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-load-more',
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Show the &quot;Load More&quot; Button:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'showbutton' ),
							'id_name'	=> $this->get_field_name( 'showbutton' ),
							'type'		=> 'select',
							'std'		=> $instance['showbutton'],
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Background color of the button:', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'buttoncolor' ),
							'id_name'	=> $this->get_field_name( 'buttoncolor' ),
							'type'		=> 'colorpicker',
							'class'		=> 'avia-headline-light' . $cls_colorpicker,
							'std'		=> $instance['buttoncolor']
						);			
			
			$elements[] = array(
							'name'		=> __( 'Text color of the button:', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'buttontextcolor' ),
							'id_name'	=> $this->get_field_name( 'buttontextcolor' ),
							'type'		=> 'colorpicker',
							'class'		=> 'avia-headline-light' . $cls_colorpicker,
							'std'		=> $instance['buttontextcolor']
						);			
			
			$elements[] = array(
							'name'		=> __( 'Text used for the button:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'buttontext' ),
							'id_name'	=> $this->get_field_name( 'buttontext' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'std'		=> $instance['buttontext'],
						);
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( '&quot;Follow on Instagram&quot; Button', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-follow-me-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-follow-me',
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Show the &quot;Follow on Instagram&quot; Button:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'showfollow' ),
							'id_name'	=> $this->get_field_name( 'showfollow' ),
							'type'		=> 'select',
							'std'		=> $instance['showfollow'],
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Background color of the button:', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'followcolor' ),
							'id_name'	=> $this->get_field_name( 'followcolor' ),
							'type'		=> 'colorpicker',
							'class'		=> 'avia-headline-light' . $cls_colorpicker,
							'std'		=> $instance['followcolor']
						);			
			
			$elements[] = array(
							'name'		=> __( 'Text color of the button:', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'followtextcolor' ),
							'id_name'	=> $this->get_field_name( 'followtextcolor' ),
							'type'		=> 'colorpicker',
							'class'		=> 'avia-headline-light' . $cls_colorpicker,
							'std'		=> $instance['followtextcolor']
						);			
			
			$elements[] = array(
							'name'		=> __( 'Text used for the button:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'followtext' ),
							'id_name'	=> $this->get_field_name( 'followtext' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'std'		=> $instance['followtext'],
						);
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'Photos Hover Style', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-photos-hover-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-photos-hover' . $cls_pro,
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Background color when hovering over a photo:', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'hovercolor' ),
							'id_name'	=> $this->get_field_name( 'hovercolor' ),
							'type'		=> 'colorpicker',
							'class'		=> 'avia-headline-light' . $cls_pro . $cls_colorpicker,
							'std'		=> $instance['hovercolor']
						);
			
			$elements[] = array(
							'name'		=> __( 'Text/icon color when hovering over a photo:', 'avia_framework' ),
							'desc'		=> __( 'Set a custom color. Any hex color code.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'hovertextcolor' ),
							'id_name'	=> $this->get_field_name( 'hovertextcolor' ),
							'type'		=> 'colorpicker',
							'class'		=> 'avia-headline-light' . $cls_pro . $cls_colorpicker,
							'std'		=> $instance['hovertextcolor']
						);
			
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'Carousel Slider', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-carousel-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-carousel opt-isb-carousel' . $cls_pro,
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Number of rows of posts in the carousel:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'carouselrows' ),
							'id_name'	=> $this->get_field_name( 'carouselrows' ),
							'type'		=> 'select',
							'std'		=> $instance['carouselrows'],
							'class'		=> 'widefat avia-headline-light avia-no-desc opt-isb-carousel' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> avia_backend_number_array( 1, 2, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						);
			
			$elements[] = array(
							'name'		=> __( 'Carousel Loop Type:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'carouselloop' ),
							'id_name'	=> $this->get_field_name( 'carouselloop' ),
							'type'		=> 'select',
							'std'		=> $instance['carouselloop'],
							'class'		=> 'widefat avia-headline-light avia-no-desc opt-isb-carousel' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Rewind', 'avia_framework' )			=> 'rewind',
												__( 'Infinitely loop', 'avia_framework' )	=> 'infinity'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Show Carousel Navigation Arrows:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'carouselarrows' ),
							'id_name'	=> $this->get_field_name( 'carouselarrows' ),
							'type'		=> 'select',
							'std'		=> $instance['carouselarrows'],
							'class'		=> 'widefat avia-headline-light avia-no-desc opt-isb-carousel' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Show Carousel Pagination:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'carouselpag' ),
							'id_name'	=> $this->get_field_name( 'carouselpag' ),
							'type'		=> 'select',
							'std'		=> $instance['carouselpag'],
							'class'		=> 'widefat avia-headline-light avia-no-desc opt-isb-carousel' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Enable Carousel Autoplay:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'carouselautoplay' ),
							'id_name'	=> $this->get_field_name( 'carouselautoplay' ),
							'type'		=> 'select',
							'std'		=> $instance['carouselautoplay'],
							'class'		=> 'widefat avia-headline-light avia-no-desc opt-isb-carousel' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Carousel Interval Time:', 'avia_framework' ),
							'desc'		=> __( 'The interval time between slides for autoplay in milliseconds.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'carouseltime' ),
							'id_name'	=> $this->get_field_name( 'carouseltime' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light opt-isb-carousel' . $cls_pro,
							'std'		=> $instance['carouseltime'],
						);
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'Highlight Grid', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-highlight-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-highlight opt-isb-highlight' . $cls_pro,
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Type of Highlight:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'highlighttype' ),
							'id_name'	=> $this->get_field_name( 'highlighttype' ),
							'type'		=> 'select',
							'std'		=> $instance['highlighttype'],
							'class'		=> 'widefat avia-headline-light avia-no-desc opt-isb-highlight' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Pattern', 'avia_framework' )		=> 'pattern',
												__( 'Post ID', 'avia_framework' )		=> 'id',
												__( 'Hashtag', 'avia_framework' )		=> 'hashtag'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Highlight Pattern:', 'avia_framework' ),
							'desc'		=> __( 'How often a post is highlighted - eg. every 6th post.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'highlightpattern' ),
							'id_name'	=> $this->get_field_name( 'highlightpattern' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light opt-isb-highlight' . $cls_pro,
							'std'		=> $instance['highlightpattern'],
						);
			
			$elements[] = array(
							'name'		=> __( 'Highlight Offset:', 'avia_framework' ),
							'desc'		=> __( 'When to start the highlight pattern.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'highlightoffset' ),
							'id_name'	=> $this->get_field_name( 'highlightoffset' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light opt-isb-highlight' . $cls_pro,
							'std'		=> $instance['highlightoffset'],
						);
			
			$elements[] = array(
							'name'		=> __( 'Highlight Hashtag:', 'avia_framework' ),
							'desc'		=> __( 'Highlight posts with these hashtags, seperate multiple hashtags with commas.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'highlighthashtag' ),
							'id_name'	=> $this->get_field_name( 'highlighthashtag' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light opt-isb-highlight' . $cls_pro,
							'std'		=> $instance['highlighthashtag'],
						);
			
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'Link Settings', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-link-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram' . $cls_pro,
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Caption Links:', 'avia_framework' ),
							'desc'		=> __( 'Use urls in captions for the photo link instead of linking to instagram.com.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'captionlinks' ),
							'id_name'	=> $this->get_field_name( 'captionlinks' ),
							'type'		=> 'select',
							'std'		=> $instance['captionlinks'],
							'class'		=> 'widefat avia-headline-light' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'Lightbox', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-lightbox-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram' . $cls_pro,
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Lightbox:', 'avia_framework' ),
							'desc'		=> __( 'Select to disable plugin lightbox and link to Instagram page.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'disablelightbox' ),
							'id_name'	=> $this->get_field_name( 'disablelightbox' ),
							'type'		=> 'select',
							'std'		=> $instance['disablelightbox'],
							'class'		=> 'widefat avia-headline-light' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use plugin lightbox', 'avia_framework' )			=> '',
												__( 'Open Instagram in new window', 'avia_framework' )	=> 'true',
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Show comments in the lightbox:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'lightboxcomments' ),
							'id_name'	=> $this->get_field_name( 'lightboxcomments' ),
							'type'		=> 'select',
							'std'		=> $instance['lightboxcomments'],
							'class'		=> 'widefat avia-headline-light avia-no-desc' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Number of comments to show:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'numcomments' ),
							'id_name'	=> $this->get_field_name( 'numcomments' ),
							'type'		=> 'text',
							'class'		=> 'widefat avia-headline-light avia-no-desc' . $cls_pro,
							'std'		=> $instance['numcomments'],
						);
			
			
			
			/**
			 * =========================================================================================================
			 */
			$elements[] = array(
							'name'          => __( 'Mobile (smaller than 480 pixels):', 'avia_framework' ),
							'desc'          => '',
							'id'            => $this->id . '-mobile-opts',
							'std'           => '',
							'type'          => 'heading',
							'class'			=> 'widefat avia-options-heading-instagram avia-options-mobile',
							'nodescription' => true
						);
			
			$elements[] = array(
							'name'		=> __( 'Disable the mobile layout:', 'avia_framework' ),
							'desc'		=> '',
							'id'		=> $this->get_field_id( 'disablemobile' ),
							'id_name'	=> $this->get_field_name( 'disablemobile' ),
							'type'		=> 'select',
							'std'		=> $instance['disablemobile'],
							'class'		=> 'widefat avia-headline-light avia-no-desc',
							'no_first'	=> true,
							'subtype'	=> array( 
												__( 'Use Default', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )			=> 'true',
												__( 'No', 'avia_framework' )			=> 'false'
											)
						);
			
			$elements[] = array(
							'name'		=> __( 'Display initially mobile:', 'avia_framework' ),
							'desc'		=> __( 'The number of photos/videos to display initially for mobile screens.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'nummobile' ),
							'id_name'	=> $this->get_field_name( 'nummobile' ),
							'type'		=> 'select',
							'std'		=> $instance['nummobile'],
							'class'		=> 'widefat avia-headline-light' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> avia_backend_number_array( 1, 33, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						);
			
			
			
			$elements[] = array(
							'name'		=> __( 'Number of columns on mobile:', 'avia_framework' ),
							'desc'		=> __( 'The number of columns in your feed for mobile screens.', 'avia_framework' ),
							'id'		=> $this->get_field_id( 'colsmobile' ),
							'id_name'	=> $this->get_field_name( 'colsmobile' ),
							'type'		=> 'select',
							'std'		=> $instance['colsmobile'],
							'class'		=> 'widefat avia-headline-light' . $cls_pro,
							'no_first'	=> true,
							'subtype'	=> avia_backend_number_array( 1, 10, 1, array( __( 'Use Default', 'avia_framework' ) => '' ) )
						);
			
			
			$output = '';
			
			$pro_class = AviaInstagramFeed()->is_proversion_active() ? ' avia_instagram_feed_pro' : 'avia_instagram_feed_basic';
			
			
			$output .= '<div class="avia_widget_form avia_widget_conditional_form avia_instagram_feed_form ' . $pro_class . '">';
			$output .=		$this->render_form_elements( $elements );
			$output .= '</div>';
			
			echo $output;
		}
		
		/**
		 * Update the form data
		 * 
		 * @since 4.7.3.1
		 * @param array $new_instance
		 * @param array $old_instance
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) 
		{
			$instance = $this->parse_args_instance( $old_instance );
			
			$fields = $this->get_field_names();
			
			$hex_params = AviaInstagramFeed()->get_hex_params();
			$unit_params = AviaInstagramFeed()->get_unit_params();
			$number_params = AviaInstagramFeed()->get_number_params();
			
			foreach( $new_instance as $key => $value ) 
			{
				if( in_array( $key, $fields ) )
				{
					$mod_val = trim( strip_tags( $value ) );
					
					if( in_array( $key, $unit_params ) )
					{
						$mod_val = AviaInstagramFeed()->validate_unit( $mod_val );
					}
					else if( in_array( $key, $hex_params ) )
					{
						$mod_val = AviaInstagramFeed()->validate_hex_color( $mod_val );
					}
					else if( in_array( $key, $number_params ) )
					{
						$mod_val = AviaInstagramFeed()->validate_number( $value );
					}
					
					$instance[ $key ] = $mod_val;
				}
			}
			
			return $instance;
		}
		
		
		/**
		 * Output the widget in frontend
		 * 
		 * @since 4.7.3.1
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) 
		{
			$instance = $this->parse_args_instance( $instance );
			
			$fields = $this->get_field_names();
			
			foreach( $instance as $key => $value ) 
			{
				if( in_array( $key, $fields ) )
				{
					$instance[ $key ] = esc_attr( $value );
				}
			}
			
			/**
			 * Add additional parameters not supported
			 * 
			 * @since 4.7.3.1
			 * @param array $instance
			 * @param string $context
			 * @param array $args
			 * @return array
			 */
			$instance = apply_filters( 'avf_widget_output_args', $instance, '_sb_instagram_feed', $args );
			
			
			echo $args['before_widget'];
			
			/**
			 * @since 4.7.3.1
			 * @param array $args
			 * @param array $instance
			 */
			do_action( 'ava_widget_before_widget', $args, $instance );
					
			$lightbox = '' == $instance['disablelightbox'] ? 'noLightbox' : '';
					
			$out  = '';
			
			$out .= '<div class="avia-widget avia-instagram-feed-smash-widget ' . $lightbox . '">';
			
			if( ! empty( $instance['title'] ) )
			{
				$out .=		'<div class="avia-widget-title avia-instagram-feed-smash-widget-title">';
				$out .=			$args['before_title'];
				$out .=				$instance['title'];
				$out .=			$args['after_title'];
				$out .=		'</div>';
			}
			
			$sc = AviaInstagramFeed()->create_shortcode( $instance );
			
			$out .= do_shortcode( $sc );
			
			$out .= '</div>';
			
			
			/**
			 * @since 4.7.3.1
			 * @param string $out
			 * @param array $args
			 * @param array $instance
			 */
			$out = apply_filters( 'avf_widget_output', $out, $args, $instance );
			
			echo $out;
			
			/**
			 * @since 4.7.3.1
			 * @param array $args
			 * @param array $instance
			 */
			do_action( 'ava_widget_after_widget', $args, $instance );
			
			echo $args['after_widget'];
		}
		
	}
}