<?php
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

/*
 * The function within this file are theme specific:
 * they are used only by this theme and not by the Avia Framework in general
 */
 

/* wrap embeds into a proportion containing div */
if(!function_exists('avia_preload_screen'))
{
	function avia_preload_screen()
	{
		$class = avia_get_option('preloader_transitions') != "disabled" ? 'av-transition-enabled' : "";
		$label = __('Loading','avia_framework');
		$logo  = avia_get_option('preloader_logo');
		if( is_numeric( $logo ) )
		{ 
			$logo = wp_get_attachment_image_src( $logo, 'full' ); 
			$logo = is_array( $logo ) ? $logo[0] : ''; 
		}
		
		if( $logo ) 
		{
			$class .= " av-transition-with-logo";
			$logo = "<img class='av-preloading-logo' src='{$logo}' alt='{$label}' title='{$label}' />";
		}
		
		$output  = "";
		$output .= 	"<div class='av-siteloader-wrap {$class}'>";
		$output .= 		"<div class='av-siteloader-inner'>";
		$output .= 			"<div class='av-siteloader-cell'>";
		$output .= 			$logo;
		$output .= 			"<div class='av-siteloader'><div class='av-siteloader-extra'></div>";
		$output .= 			"</div>";
		$output .= 		"</div>";
		$output .= 	"</div>";
		$output .= "</div>";
		
		return $output;
	}
}



/* filter menu item urls */
if(!function_exists('avia_menu_item_filter'))
{
	add_filter( 'avf_menu_items', 'avia_menu_item_filter', 10 );

	function avia_menu_item_filter ( $item  )
	{
		
		if(isset( $item->url ) && strpos($item->url, '#DOMAIN') === 0)
		{
			$item->url = str_replace("#DOMAIN", get_site_url(), $item->url);
		}
		
	    return $item;
	}
}

 
 
/* filter menu item urls */
if(!function_exists('avia_maps_key_for_plugins'))
{
	add_filter( 'script_loader_src', 'avia_maps_key_for_plugins', 10, 2 );

	function avia_maps_key_for_plugins ( $url, $handle  )
	{
		$key = get_option( 'gmap_api' );
		
		if ( ! $key ) { return $url; }
		
		if ( strpos( $url, "maps.google.com/maps/api/js" ) !== false || strpos( $url, "maps.googleapis.com/maps/api/js" ) !== false ) 
		{
				//	if no key, we can generate a new link with our key
			if ( strpos( $url, "key=" ) === false ) 
			{	
				$url = av_google_maps::api_url( $key );
			}
		}
	
		return $url;
	}
}



/* wrap embeds into a proportion containing div */
if(!function_exists('avia_iframe_proportion_wrap'))
{
	add_filter( 'embed_oembed_html', 'avia_iframe_proportion_wrap', 10, 4 );

	function avia_iframe_proportion_wrap ( $html, $url, $attr, $post_ID  )
	{
		if(strpos($html, '<iframe') !== false)
		{
			$html = "<div class='avia-iframe-wrap'>{$html}</div>";
		}
	    return $html;
	}
}



/* AJAX SEARCH */
if( ! function_exists( 'avia_append_search_nav' ) )
{
	//first append search item to main menu
	add_filter( 'wp_nav_menu_items', 'avia_append_search_nav', 9997, 2 );
	add_filter( 'avf_fallback_menu_items', 'avia_append_search_nav', 9997, 2 );

	/**
	 * 
	 * 
	 * @param string $items
	 * @param array $args
	 * @return string
	 */
	function avia_append_search_nav ( $items, $args )
	{	
		if( avia_get_option( 'header_searchicon', 'header_searchicon' ) != "header_searchicon" ) 
		{
			return $items;
		}
		
		if( avia_get_option( 'header_position', 'header_top' ) != "header_top" ) 
		{
			return $items;
		}

		if( ( is_object( $args ) && $args->theme_location == 'avia') || ( is_string( $args ) && $args = 'fallback_menu' ) )
		{
			
			ob_start();
			get_search_form();
			$form = ob_get_clean();
			
			$form = str_replace( '<form ', '<form role="search" ', $form );
			$form = htmlspecialchars( $form );

			/**
			 * Avoid duplicate indexing or empty search page
			 * 
			 * @since 4.5.3
			 * @param string $items
			 * @param array $args 
			 * @return string
			 */
			$nofollow = apply_filters( 'avf_nav_search_icon_nofollow', 'rel="nofollow"', $items, $args );

			$aria_label = __( 'Search', 'avia_framework' );
			$aria_label = apply_filters( 'avf_nav_search_aria_label', $aria_label, $items, $args );

			$items .=	'<li id="menu-item-search" class="noMobile menu-item menu-item-search-dropdown menu-item-avia-special">';
			$items .=		'<a aria-label="' . $aria_label . '" href="?s=" '. $nofollow . ' data-avia-search-tooltip="' . $form . '" ' . av_icon_string( 'search', false ) . '>';
			$items .=			'<span class="avia_hidden_link_text">' . __( 'Search', 'avia_framework' ) . '</span>';
			$items .=		'</a>';
			$items .=	'</li>';
		}
		
		return $items;
	}
}

/*	Prepare a possible fix for menu plugins, that remove theme location from menu array to exchange the menus	*/
if( ! function_exists( 'avia_save_menu_location' ) )
{
	add_filter( 'wp_nav_menu_args', 'avia_save_menu_location', 1, 1 );
	
	function avia_save_menu_location( $args )
	{
		global $avia_config;
		
		$avia_config['current_menu_location_output'] = isset( $args['theme_location'] ) ? $args['theme_location'] : '';
		
		return $args;
	}
}


if( ! function_exists( 'avia_append_burger_menu' ) )
{
	//first append search item to main menu
	add_filter( 'wp_nav_menu_items', 'avia_append_burger_menu', 9998, 2 );
	add_filter( 'avf_fallback_menu_items', 'avia_append_burger_menu', 9998, 2 );

	/**
	 * Append the burger menu
	 * 
	 * @param string $items
	 * @param array $args
	 * @return string
	 */
	function avia_append_burger_menu ( $items , $args )
	{	
		global $avia_config;
		
		$location = ( is_object( $args ) && isset( $args->theme_location ) ) ? $args->theme_location : '';
		$original_location = isset( $avia_config['current_menu_location_output'] ) ? $avia_config['current_menu_location_output'] : '';	
		
		/**
		 * Allow compatibility with plugins that change menu or third party plugins to manpulate the location
		 * 
		 * @used_by Enfold config-menu-exchange\config.php			10
		 * @since 4.1.3
		 */
		$location = apply_filters( 'avf_append_burger_menu_location', $location, $original_location, $items , $args );
		
	    if( ( is_object( $args ) && ( $location == 'avia' ) ) || ( is_string( $args ) && ( $args == "fallback_menu" ) ) )
	    {
	        $class = avia_get_option('burger_size');
	        
	        $items .= '<li class="av-burger-menu-main menu-item-avia-special ' . $class . '">
	        			<a href="#" aria-label="' . esc_attr( __( 'Menu', 'avia_framework' ) ) . '" aria-hidden="false">
							<span class="av-hamburger av-hamburger--spin av-js-hamburger">
								<span class="av-hamburger-box">
						          <span class="av-hamburger-inner"></span>
						          <strong>' . __( 'Menu', 'avia_framework' ) . '</strong>
								</span>
							</span>
							<span class="avia_hidden_link_text">' . esc_html( __( 'Menu', 'avia_framework' ) ) . '</span>
						</a>
	        		   </li>';
	    }
	    return $items;
	}
}

if( ! function_exists( 'avia_is_burger_menu' ) )
{
	function avia_is_burger_menu ()
	{
		$burger_menu = false;

		if( avia_get_option( 'menu_display' ) !== 'burger_menu' )
		{
			return $burger_menu;
		}

		if( avia_get_option( 'header_position' ) !== 'header_top' )
		{
			return $burger_menu;
		}

		//if(avia_get_option('header_position') !== 'header_top') return $burger_menu;
		//if(strpos(avia_get_option('header_layout'), 'main_nav_header') === false) return $burger_menu;

		return true;
	}
}



if( ! function_exists( 'avia_ajax_search' ) )
{
	/**
	 * Perform search and return result
	 */
	function avia_ajax_search()
	{
		unset( $_REQUEST['action'] );

		if( empty( $_REQUEST['s'] ) )
		{
			$_REQUEST['s'] = array_shift( array_values( $_REQUEST ) );
		}

		if( empty( $_REQUEST['s'] ) )
		{
			die();
		}

		$defaults = array(
						'numberposts'		=> 5,
						'post_type'			=> 'any',
						'post_status'		=> 'publish',
						'post_password'		=> '',
						'suppress_filters'	=> false,
						'results_hide_fields' => ''
					);

		/**
		 * WP Filter for the contents of the search query variable
		 *
		 * @param string
		 * @return string
		 */
		$_REQUEST['s'] = apply_filters( 'get_search_query', $_REQUEST['s'] );

		$search_parameters = array_merge( $defaults, $_REQUEST );

		if ( $search_parameters['results_hide_fields'] !== '' )
		{
			$search_parameters['results_hide_fields'] = explode( ',', $_REQUEST['results_hide_fields'] );
		}
		else
		{
			$search_parameters['results_hide_fields'] = array();
		}

		/**
		 * @used_by				Avia_Custom_Pages		10
		 * @used_by				config-woocommerce\config.php  avia_woocommerce_ajax_search_params()	20
		 *
		 * @param array
		 * @return array
		 */
		$search_query = apply_filters( 'avf_ajax_search_query', http_build_query( $search_parameters ) );

		/**
		 * @used_by			Avia_Relevanssi			10
		 *
		 * @param string $function_name
		 * @param array $search_query
		 * @param array $search_parameters
		 * @param array $defaults
		 * @return string
		 */
		$query_function = apply_filters( 'avf_ajax_search_function', 'get_posts', $search_query, $search_parameters, $defaults );

		$posts = ( ( $query_function == 'get_posts') || ! function_exists( $query_function ) ) ? get_posts( $search_query ) : $query_function( $search_query, $search_parameters, $defaults );

		$search_messages = array(
				'no_criteria_matched' => __( 'Sorry, no posts matched your criteria', 'avia_framework' ),
				'another_search_term' => __( 'Please try another search term', 'avia_framework' ),
				'time_format'         => get_option( 'date_format' ),
				'all_results_query'   => http_build_query( $_REQUEST ),
				'all_results_link'    => home_url( '?' . http_build_query( $_REQUEST ) ),
				'view_all_results'    => __( 'View all results', 'avia_framework' )
			);

		$search_messages = apply_filters( 'avf_ajax_search_messages', $search_messages, $search_query );

		if( empty( $posts ) )
		{
			$output  = '<span class="av_ajax_search_entry ajax_not_found">';
			$output .=		'<span class="av_ajax_search_image ' . av_icon_string( 'info' ) . '">';
			$output .=		'</span>';
			$output .=		'<span class="av_ajax_search_content">';
			$output .=			'<span class="av_ajax_search_title">';
			$output .=				$search_messages['no_criteria_matched'];
			$output .=			'</span>';
			$output .=			'<span class="ajax_search_excerpt">';
			$output .=				$search_messages['another_search_term'];
			$output .=			'</span>';
			$output .=		'</span>';
			$output .= '</span>';

			echo $output;
			die();
		}

		//if we got posts resort them by post type
		$output = '';
		$sorted = array();
		$post_type_obj = array();

		foreach( $posts as $post )
		{
			$sorted[ $post->post_type][] = $post;
			if( empty( $post_type_obj[ $post->post_type ] ) )
			{
				$post_type_obj[ $post->post_type ] = get_post_type_object( $post->post_type );
			}
		}

		//now we got everything we need to preapre the output
		foreach( $sorted as $key => $post_type )
		{
			// check if post titles are in the hidden fields list
			if ( ! in_array( 'post_titles', $search_parameters['results_hide_fields'] ) )
			{
				if( isset( $post_type_obj[ $key ]->labels->name ) )
				{
					$label = apply_filters( 'avf_ajax_search_label_names', $post_type_obj[ $key ]->labels->name );
					$output .= "<h4>{$label}</h4>";
				}
				else
				{
					$output .= '<hr />';
				}
			}

			foreach( $post_type as $post )
			{
				$image = '';
				$extra_class = '';

				// check if image is in the hidden fields list
				if ( ! in_array( 'image', $search_parameters['results_hide_fields'] ) )
				{
					$image = get_the_post_thumbnail( $post->ID, 'thumbnail' );
					$extra_class = $image ? 'with_image' : '';
					$post_type = $image ? '' : ( get_post_format( $post->ID ) != '' ? get_post_format( $post->ID ) : 'standard' );
					$iconfont = $image ? '' : av_icon_string( $post_type );

				}

				$excerpt = '';

				// check if post meta fields are in the hidden fields list
				if ( ! in_array( 'meta', $search_parameters['results_hide_fields'] ) )
				{
					if( ! empty( $post->post_excerpt ) )
					{
						/**
						 *
						 * @since ???
						 * @since 4.8.5.1		added $post
						 * @param string $excerpt
						 * @param WP_Post $post
						 * @return string
						 */
						$excerpt = apply_filters( 'avf_ajax_search_excerpt', avia_backend_truncate( $post->post_excerpt, 70, ' ', '...', true, '', true ), $post );
					}
					else
					{
						/**
						 *
						 * @since ???
						 * @param string $excerpt
						 * @param WP_Post $post
						 * @return string
						 */
						$excerpt = apply_filters( 'avf_ajax_search_no_excerpt', get_the_time( $search_messages['time_format'], $post->ID ), $post );
					}
				}

				$link = apply_filters( 'av_custom_url', get_permalink( $post->ID ), $post );

				$output .= "<a class ='av_ajax_search_entry {$extra_class}' href='{$link}'>";

				if ( $image !== '' || $iconfont )
				{
					$output .= "<span class='av_ajax_search_image' {$iconfont}>";
					$output .=		$image;
					$output .= '</span>';
				}
				$output .= '<span class="av_ajax_search_content">';
				$output .=		'<span class="av_ajax_search_title">';
				$output .=			get_the_title($post->ID);
				$output .=		'</span>';

				if( $excerpt !== '' )
				{
					$output .=		'<span class="ajax_search_excerpt">';
					$output .=			$excerpt;
					$output .=		'</span>';
				}
				$output .=	'</span>';
				$output .= '</a>';
			}
		}

		$output .= "<a class='av_ajax_search_entry av_ajax_search_entry_view_all' href='{$search_messages['all_results_link']}'>";
		$output .=		$search_messages['view_all_results'];
		$output .= '</a>';

		echo $output;
		
		die();
	}
	
	// now hook into wordpress ajax function to catch any ajax requests
	add_action( 'wp_ajax_avia_ajax_search', 'avia_ajax_search' );
	add_action( 'wp_ajax_nopriv_avia_ajax_search', 'avia_ajax_search' );
}

if( ! function_exists( 'avia_social_widget_icon' ) )
{
	/**
	 * Modify twitter social count widget and add social icons as iconfont
	 *
	 * @param string $content
	 * @param string $icon
	 * @return string
	 */
	function avia_social_widget_icon( $content, $icon )
	{
		$content = '<span class="social_widget_icon" ' . av_icon_string( $icon ) . '></span>' . $content;

		return $content;
	}

	add_filter( 'avf_social_widget', 'avia_social_widget_icon', 2, 2 );
}


//call functions for the theme
add_filter( 'the_content_more_link', 'avia_remove_more_jump_link' );
add_post_type_support( 'page', 'excerpt' );



//allow additional file type uploads
if( ! function_exists( 'avia_upload_mimes' ) )
{
	function avia_upload_mimes( $mimes )
	{
		return array_merge( $mimes, array( 'mp4' => 'video/mp4', 'ogv' => 'video/ogg', 'webm' => 'video/webm', 'txt' => 'text/plain' ) );
	}

	add_filter( 'upload_mimes', 'avia_upload_mimes' );
}


//change default thumbnail size and fullwidth size on theme activation
if(!function_exists('avia_set_thumb_size'))
{
	add_action('avia_backend_theme_activation', 'avia_set_thumb_size');
	function avia_set_thumb_size()
	{
		update_option( 'thumbnail_size_h', 80 ); update_option( 'thumbnail_size_w', 80 );
		update_option( 'large_size_w', 1030 ); 	 update_option( 'large_size_h', 1030 );
	}
}




//add support for post thumbnails
add_theme_support( 'post-thumbnails' );




//advanced title + breadcrumb function
if(!function_exists('avia_title'))
{
	function avia_title($args = false, $id = false)
	{
		global $avia_config;

		if(!$id) $id = avia_get_the_id();
		
		$header_settings = avia_header_setting();
		if($header_settings['header_title_bar'] == 'hidden_title_bar') return "";
		
		$defaults 	 = array(

			'title' 		=> get_the_title($id),
			'subtitle' 		=> "", //avia_post_meta($id, 'subtitle'),
			'link'			=> get_permalink($id),
			'html'			=> "<div class='{class} title_container'><div class='container'>{heading_html}{additions}</div></div>",
			'heading_html'	=> "<{heading} class='main-title entry-title {heading_class}'>{title}</{heading}>",
			'class'			=> 'stretch_full container_wrap alternate_color '.avia_is_dark_bg('alternate_color', true),
			'breadcrumb'	=> true,
			'additions'		=> "",
			'heading'		=> 'h1', //headings are set based on this article: http://yoast.com/blog-headings-structure/
			'heading_class'	=> ''
		);

		if ( is_tax() || is_category() || is_tag() )
		{
			global $wp_query;

			$term = $wp_query->get_queried_object();
			$defaults['link'] = get_term_link( $term );
		}
		else if(is_archive())
		{
			$defaults['link'] = "";
		}
		
		
		// Parse incomming $args into an array and merge it with $defaults
		$args = wp_parse_args( $args, $defaults );
		
		/**
		 * @used_by		config-woocommerce\config.php avia_title_args_woopage()				10
		 * @since < 4.0
		 * @return array
		 */
		$args = apply_filters( 'avf_title_args', $args, $id );

		//disable breadcrumb if requested
		if($header_settings['header_title_bar'] == 'title_bar') $args['breadcrumb'] = false;
		
		//disable title if requested
		if($header_settings['header_title_bar'] == 'breadcrumbs_only') $args['title'] = '';


		// OPTIONAL: Declare each item in $args as its own variable i.e. $type, $before.
		extract( $args, EXTR_SKIP );

		if( empty( $title ) ) 
		{
			$class .= " empty_title ";
		}
		
        $markup = avia_markup_helper( array( 'context' => 'avia_title', 'echo' => false ) );
		
		if( ! empty( $link ) && ! empty( $title ) ) 
		{
			$title = "<a href='" . $link . "' rel='bookmark' title='" . __( 'Permanent Link:', 'avia_framework' ) . " " . esc_attr( $title ) . "' $markup>" . $title . "</a>";
		}
		
		if( ! empty( $subtitle ) ) 
		{
			$additions .= "<div class='title_meta meta-color'>" . wpautop( $subtitle ) . "</div>";
		}
		
		if( $breadcrumb ) 
		{
			$additions .= Avia_Breadcrumb_Trail()->get_trail( array( 'separator' => '/', 'richsnippet' => true ) );
		}

		if( ! $title ) 
		{
			$heading_html = "";
		}
		
		$html = str_replace( '{heading_html}', $heading_html, $html );
		
		
		$html = str_replace( '{class}', $class, $html );
		$html = str_replace( '{title}', $title, $html );
		$html = str_replace( '{additions}', $additions, $html );
		$html = str_replace( '{heading}', $heading, $html );
		$html = str_replace( '{heading_class}', $heading_class, $html );

		if(!empty($avia_config['slide_output']) && !avia_is_dynamic_template($id) && !avia_is_overview())
		{
			$avia_config['small_title'] = $title;
		}
		else
		{
			return $html;
		}
	}
}


if( ! function_exists( 'avia_wp_get_the_title' ) )
{
	/**
	 * Retrieve post title.
	 *
	 * This is a wrapper function for backwards comp. because Enfold did not always use WP function get_the_title() to get post title.
	 * To avoid breaking older sites that rely on unfiltered post title we add a filter here and return unfiltered post content.
	 *
	 * @since 4.7.3.1
	 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
	 * @return string
	 */
	function avia_wp_get_the_title( $post = 0 )
	{
		$post = get_post( $post );
		
		if( ! $post instanceof WP_Post )
		{
			return '';
		}
		
		/**
		 * Filter might be removed in oncoming versions
		 * 
		 * @since 4.7.3.1
		 * @param boolean
		 * @param WP_Post $post
		 * @return boolean
		 */
		$supress = apply_filters( 'avf_legacy_suppress_wp_filter_post_title', false, $post );
		
		if( false !== $supress )
		{
			return $post->post_title;
		}
		
		return get_the_title( $post );
	}
}




if( ! function_exists( 'avia_post_nav' ) )
{
	/**
	 * Add navigation link elements for single post pages
	 * 
	 * @since < 4.0
	 * @param boolean $same_category
	 * @param string $taxonomy
	 * @return string
	 */
	function avia_post_nav( $same_category = false, $taxonomy = 'category' )
	{
		global $post, $wp_version;
		
		/**
		 * Create a settings array to allow filtering and change behaviour
		 */
		$settings = array();
		
		$settings['disable_post_nav_option'] = avia_get_option('disable_post_nav');
		$settings['skip_output'] = ! is_singular() || 'disable_post_nav' == $settings['disable_post_nav_option'];
		$settings['loop_post_nav'] = 'loop_post_nav' == $settings['disable_post_nav_option'];
				
		$settings['same_category'] = $same_category;
		$settings['excluded_terms'] = '';
		$settings['wpversion'] = $wp_version;
		$settings['type'] = get_post_type();
		$settings['taxonomy'] = ( $settings['type'] == 'portfolio' ) ? 'portfolio_entries' : $taxonomy;
		
		/**
		 * Don't display if a fullscreen slider is available since they overlap
		 */
		$settings['is_fullwidth'] = false;
		if( ( class_exists( 'avia_sc_layerslider' ) && ! empty( avia_sc_layerslider::$slide_count ) ) || 
			( class_exists( 'avia_sc_slider_full' ) && ! empty( avia_sc_slider_full::$slide_count ) )  )
		{
			 $settings['is_fullwidth'] = true;
		}
		
		$settings['is_hierarchical'] = is_post_type_hierarchical( $settings['type'] );
		
		/**
		 * Check if we need to skip output
		 */
		if( ! $settings['skip_output'] )
		{
			$settings['skip_output'] = $settings['is_hierarchical'] || $settings['is_fullwidth'];
		}
		
		/**
		 * Backwards compatibility - next 2 lines will be removed in a future version
		 */
		$settings = apply_filters_deprecated( 'avia_post_nav_settings', array( $settings ), '4.5.6', 'avf_post_nav_settings', __( 'Return values handling has changed', 'avia_framework' ) );
		$settings['skip_output'] = $settings['skip_output'] || ! empty( $settings['is_hierarchical'] ) || ! empty( $settings['is_fullwidth'] );
		
		
		/**
		 * $settings['skip_output'] = true if you want to skip output
		 * $settings['same_category'] = true|false
		 * $settings['excluded_terms'] = array|comma speerated string of id's
		 * 
		 * @used_by			config-bbpress\config.php  avia_bbpress_avf_post_nav_settings		10
		 * @since 4.5.6
		 * @return array
		 */
		$settings = apply_filters( 'avf_post_nav_settings', $settings );
			
		if( true === $settings['skip_output'] )
		{
			return '';
		}
			
		if(version_compare( $settings['wpversion'], '3.8', '>=' ) )
		{
			$entries['prev'] = get_previous_post( $settings['same_category'], $settings['excluded_terms'], $settings['taxonomy'] );
			$entries['next'] = get_next_post( $settings['same_category'], $settings['excluded_terms'], $settings['taxonomy'] );
		}
		else
		{
			$entries['prev'] = get_previous_post( $settings['same_category'] );
			$entries['next'] = get_next_post( $settings['same_category'] );
		}
		
		$queried_entries = $entries;
		if( true === $settings['loop_post_nav'] && ( ! $entries['prev'] instanceof WP_Post || ! $entries['next'] instanceof WP_Post ) )
		{
			$order = ! $entries['prev'] instanceof WP_Post ? 'DESC' : 'ASC';
			$args = array(
						'post_type'			=> $settings['type'],
						'post_status'		=> 'publish',
						'posts_per_page'	=> 1,
						'orderby'			=> array( 'post_date' => $order, 'ID' => $order )
					);
			
			
			$tax_query = array();
			
			if( $settings['same_category'] )
			{
				$ids_in = array();
				$terms = get_the_terms( $post, $settings['taxonomy'] );
				if( is_array( $terms ) && ! empty( $terms ) )
				{
					foreach( $terms as $term )
					{
						$ids_in[] = $term->term_id;
					}
				}
				
				if( ! empty( $ids_in ) )
				{
					$tax_query[] = array(
											'taxonomy'	=> $settings['taxonomy'],
											'field'		=> 'term_id',
											'terms'		=> $ids_in,
											'operator'	=> 'IN',
									);
				}
			}
			
			if( ! empty( $settings['excluded_terms'] ) )
			{
				$ids_not_in = array();
				if( is_array( $settings['excluded_terms'] ) )
				{
					$ids_not_in = $settings['excluded_terms'];
				}
				else
				{
					$ids_not_in = explode( ',', $settings['excluded_terms'] );
				}
				
				if( ! empty( $ids_not_in ) )
				{
					$tax_query[] = array(
											'taxonomy'	=> $settings['taxonomy'],
											'field'		=> 'term_id',
											'terms'		=> $ids_not_in,
											'operator'	=> 'NOT IN',
									);
				}
			}
			
			if( count( $tax_query ) > 1 )
			{
				$tax_query['relation'] = 'AND';
			}
			
			if( count( $tax_query ) >= 1 )
			{
				$args['tax_query'] = $tax_query;
			}
			
			/**
			 * Allows e.g. to change sort order of posts (see WP filter "get_{$adjacent}_post_sort") in get_adjacent_post()
			 * 
			 * @since 4.5.6.2
			 * @param array $args
			 * @param array $settings
			 * @return array
			 */
			$args = apply_filters( 'avf_post_nav_loop_args', $args, $settings );
			
			$looped = new WP_Query( $args );
			if( $looped->post_count >= 1 )
			{
				if( ! $entries['prev'] instanceof WP_Post )
				{
					$entries['prev'] = $looped->posts[0];
				}
				else
				{
					$entries['next'] = $looped->posts[0];
				}
			}
		}
		
		/**
		 * Backwards comp. only, will be removed in future
		 * @since < 4.0
		 * @added 4.5.6
		 */
		$entries = apply_filters_deprecated( 'avia_post_nav_entries', array( $entries, $settings, $queried_entries ), '4.5.6', 'avf_post_nav_entries', __( 'Filter name has changed', 'avia_framework' ) );

		/**
		 * @used_by		config-events-calendar\config.php avia_events_custom_post_nav()			10
		 * 
		 * @since 4.5.6
		 * @return array
		 */
		$entries = apply_filters( 'avf_post_nav_entries', $entries, $settings, $queried_entries );
		
		
		$output = '';

		foreach( $entries as $key => $entry )
		{
			if( empty( $entry ) ) 
			{
				continue;
			}
			
			$the_title 	= isset($entry->av_custom_title) ? $entry->av_custom_title : avia_backend_truncate(get_the_title( $entry->ID), 75, ' ' );
			$link 		= isset($entry->av_custom_link)  ? $entry->av_custom_link  : get_permalink( $entry->ID );
			$image 		= isset($entry->av_custom_image) ? $entry->av_custom_image : get_the_post_thumbnail( $entry->ID, 'thumbnail' );
			
			$tc1   = $tc2 = '';
			$class = $image ? 'with-image' : 'without-image';

			$output .= "<a class='avia-post-nav avia-post-{$key} {$class}' href='{$link}' >";
			$output .= "    <span class='label iconfont' " . av_icon_string( $key ) . "></span>";
			$output .= "    <span class='entry-info-wrap'>";
			$output .= "        <span class='entry-info'>";
			
			$tc1     = "            <span class='entry-title'>{$the_title}</span>";
			if( $image )
			{ 
				$tc2 = "            <span class='entry-image'>{$image}</span>";
			}
			
			$output .= $key == 'prev' ?  $tc1 . $tc2 : $tc2 . $tc1;

			$output .= "        </span>";
			$output .= "    </span>";
			$output .= "</a>";
		}
		
		return $output;
	}
}






//wrap ampersands into special calss to apply special styling

if(!function_exists('avia_ampersand'))
{
	add_filter('avia_ampersand','avia_ampersand');

	function avia_ampersand($content)
	{
		//ampersands
		$content = str_replace(" &amp; "," <span class='special_amp'>&amp;</span> ",$content);
		$content = str_replace(" &#038; "," <span class='special_amp'>&amp;</span> ",$content);
	
		
		// quotes
		$content = str_replace("“","<span class='special_amp'>“</span>",$content); // left double quotation mark “
		$content = str_replace("”","<span class='special_amp'>”</span>",$content); // right double quotation mark ”
		$content = str_replace("„","<span class='special_amp'>„</span>",$content); // double low-9 quotation mark „
		
		
		$content = str_replace("&#8220;","<span class='special_amp'>&#8220;</span>",$content); // left double quotation mark “
		$content = str_replace("&#8221;","<span class='special_amp'>&#8221;</span>",$content); // right double quotation mark ”
		$content = str_replace("&#8222;","<span class='special_amp'>&#8222;</span>",$content); // double low-9 quotation mark „

		return $content;
	}
}





// checks if a background color of a specific region is dark  or light and returns a class name
if(!function_exists('avia_is_dark_bg'))
{
	function avia_is_dark_bg($region, $return_only = false)
	{
		global $avia_config;

		$return = "";
		$color = $avia_config['backend_colors']['color_set'][$region]['bg'];

		$is_dark = avia_backend_calc_preceived_brightness($color, 70);

		$return = $is_dark ? "dark_bg_color" : "light_bg_color";
		if($return_only)
		{
			return $return;
		}
		else
		{
			echo $return;
		}
	}
}




//set post excerpt to be visible on theme acivation in user backend
if(!function_exists('avia_show_menu_description'))
{

	//add_action('avia_backend_theme_activation', 'avia_show_menu_description');
	function avia_show_menu_description()
	{
		global $current_user;
	    get_currentuserinfo();
		$old_meta_data = $meta_data = get_user_meta($current_user->ID, 'metaboxhidden_page', true);

		if(is_array($meta_data) && isset($meta_data[0]))
		{
			$key = array_search('postexcerpt', $meta_data);

			if($key !== false)
			{
				unset($meta_data[$key]);
				update_user_meta( $current_user->ID, 'metaboxhidden_page', $meta_data, $old_meta_data );
			}
		}
		else
		{
				update_user_meta( $current_user->ID, 'metaboxhidden_page', array('postcustom', 'commentstatusdiv', 'commentsdiv', 'slugdiv', 'authordiv', 'revisionsdiv') );
		}
	}
}


/**
 * Google Analytics Support
 * ========================
 */
if( ! function_exists( 'avia_get_tracking_code' ) )
{
	add_action( 'init', 'avia_get_tracking_code' );

	/**
	 * Make google analytics code work, even if the user only enters the UA id (UA-[0-9]*-[0-9]*).
	 * With 4.8 support is extended for new GA4 tracking code G-[0-9A-Za-z] (Jan. 2021 - https://github.com/KriesiMedia/wp-themes/issues/3032 )
	 * 
	 * The async tracking code is placed in the header (GDPR is supported to only load when allowed).
	 * 
	 * @return void
	 */
	function avia_get_tracking_code()
	{
		global $avia_config;
		
		$avia_config['analytics_code'] = '';
		
		$analytics = avia_get_option( 'analytics', '' );
		$avia_config['analytics_code'] = trim( $analytics );
		
		if( empty( $avia_config['analytics_code'] ) ) 
		{
			return;
		}

		//	if we only get passed the UA-id or G-id create the script for the user (universal tracking code)
		if( strpos( $avia_config['analytics_code'], 'UA-' ) === 0 || strpos( $avia_config['analytics_code'], 'G-' ) === 0 ) 
		{
			$avia_config['analytics_code'] = "
<!-- Global site tag (gtag.js) - Google Analytics -->
<script id='google_analytics_script' class='google_analytics_scripts' async src='https://www.googletagmanager.com/gtag/js?id=" . $avia_config['analytics_code'] . "'></script>
<script class='google_analytics_scripts' type='text/javascript'>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '" . $avia_config['analytics_code'] . "', { 'anonymize_ip': true });
</script>
";
		}
		
		add_action( 'wp_footer', 'avia_print_tracking_code', 10000 );
	}
	
}

if( ! function_exists( 'avia_print_tracking_code' ) )
{
	/**
	 * 
	 * @global type $avia_config
	 */
	function avia_print_tracking_code()
	{
		global $avia_config;

		if( ! empty( $avia_config['analytics_code'] ) )
		{
			//extract UA ID from code
			$UAID = false;
			$extra_code = '';
			$match = array();
			
			$search_uaid = '!UA-[0-9]+-[0-9]+!';
			$search_g4 = '!G-[0-9A-Za-z]+!';
			
			preg_match( $search_uaid, $avia_config['analytics_code'], $match );
			
			if( ! empty( $match ) && isset( $match[0] ) ) 
			{
				$UAID = $match[0];
			}
			else
			{
				preg_match( $search_g4, $avia_config['analytics_code'], $match );
				if( ! empty( $match ) && isset( $match[0] ) ) 
				{
					$UAID = $match[0];
				}
			}
			
			$code = json_encode( $avia_config['analytics_code'] );
			
			//if we got a valid uaid, add the js cookie check 
			if( $UAID )
			{
				$extra_code = "
<script type='text/javascript'>
			
	(function($) {
					
			/*	check if google analytics tracking is disabled by user setting via cookie - or user must opt in.	*/
					
			var analytics_code = {$code}.replace(/\\\"/g, '\"' );
			var html = document.getElementsByTagName('html')[0];

			$('html').on( 'avia-cookie-settings-changed', function(e) 
			{
					var cookie_check = html.className.indexOf('av-cookies-needs-opt-in') >= 0 || html.className.indexOf('av-cookies-can-opt-out') >= 0;
					var allow_continue = true;
					var silent_accept_cookie = html.className.indexOf('av-cookies-user-silent-accept') >= 0;
					var script_loaded = $( 'script.google_analytics_scripts' );

					if( cookie_check && ! silent_accept_cookie )
					{
						if( ! document.cookie.match(/aviaCookieConsent/) || html.className.indexOf('av-cookies-session-refused') >= 0 )
						{
							allow_continue = false;
						}
						else
						{
							if( ! document.cookie.match(/aviaPrivacyRefuseCookiesHideBar/) )
							{
								allow_continue = false;
							}
							else if( ! document.cookie.match(/aviaPrivacyEssentialCookiesEnabled/) )
							{
								allow_continue = false;
							}
							else if( document.cookie.match(/aviaPrivacyGoogleTrackingDisabled/) )
							{
								allow_continue = false;
							}
						}
					}

					if( ! allow_continue )
					{ 
//						window['ga-disable-{$UAID}'] = true;
						if( script_loaded.length > 0 )
						{
							script_loaded.remove();
						}
					}
					else
					{
						if( script_loaded.length == 0 )
						{
							$('head').append( analytics_code );
						}
					}
			});
			
			$('html').trigger( 'avia-cookie-settings-changed' );
						
	})( jQuery );
				
</script>";
			}
				
			echo $extra_code;
		}
	}
}


if( ! function_exists( 'avia_header_setting' ) )
{
	/**
	 * checks which header style we are using. In general the whole site has the same header active, based on the option in theme options->header
	 * however, for the theme demo we need to showcase all headers, thats why we can simply add a custom field key to overwrite the default heading
	 * 
	 * @param string|false $single_val
	 * @return array|mixed
	 */
	function avia_header_setting( $single_val = false )
	{
		global $avia_config;

		if( isset( $avia_config['header_settings'] ) && $single_val && isset( $avia_config['header_settings'][ $single_val ] ) ) 
		{
			return $avia_config['header_settings'][ $single_val ];
		}

		//	return cached header setting if available
		if( isset( $avia_config['header_settings'] ) && ! $single_val ) 
		{
			return $avia_config['header_settings']; 
		}

		$defaults = array(  
						'header_position' 			=> 'header_top',
						'header_layout'				=> 'logo_left menu_right', 
						'header_size'				=> 'slim', 
						'header_custom_size'		=> '', 
						'header_sticky'				=> 'header_sticky', 
						'header_shrinking'			=> 'header_shrinking', 
						'header_title_bar'			=> '',
						'header_social'				=> '',
						'header_unstick_top'		=> '',
						'header_secondary_menu'		=> '', 
						'header_stretch'			=> '',
						'header_custom_size'		=> '',
						'header_phone_active'		=> '',
						'header_replacement_logo'	=> '',
						'header_replacement_logo_title'		=> '',
						'header_replacement_logo_alt'		=> '',
						'header_replacement_logo_srcset'	=> '',
						'header_replacement_logo_sizes'		=> '',
						'header_replacement_menu'	=> '',
						'submenu_visibility' 		=> '',
						'overlay_style'				=> 'av-overlay-side',
						'header_searchicon' 		=> true,
						'header_mobile_activation' 	=> 'mobile_menu_phone',
						'phone'						=>'',
						'sidebarmenu_sticky' 		=> 'conditional_sticky',
						'layout_align_content' 		=> 'content_align_center',
						'sidebarmenu_widgets' 		=> '',
						'sidebarmenu_social' 		=> 'disabled',
						'header_menu_border' 		=> '',
						'header_style'				=> '',
						'blog_global_style'			=> '',
						'menu_display' 				=> '',
						'alternate_menu'			=> '',
						'submenu_clone' 			=> 'av-submenu-noclone',
					);
							
		$settings = avia_get_option();
		
		//overwrite with custom fields if they are set
		$post_id = avia_get_the_id();
		if( $post_id && is_singular() )
		{	
			$custom_fields = get_post_custom( $post_id );
			
			foreach( $defaults as $key => $default )
			{
				if( ! empty( $custom_fields[ $key ] ) && ! empty( $custom_fields[ $key ][0]) ) 
				{
					$settings[ $key ] = $custom_fields[ $key ][0];
				}
			}
			
			//check if header transparency is set to true
			$transparency = post_password_required() ? false : get_post_meta( $post_id, 'header_transparency', true );
		}
				
		$header = shortcode_atts( $defaults, $settings );
		$header['header_scroll_offset'] = avia_get_header_scroll_offset( $header );
		
		//	if sidebar main menu is active set the header accordingly and return the sidebar header
		if( $header['header_position'] != 'header_top' ) 
		{
			return avia_header_setting_sidebar( $header, $single_val );
		}
		
		//------------------------------------------------------------------------------------------------------
		//------------------------------------------------------------------------------------------------------


		//if header main menu is above the logo set a var to indicate that and disable transparency and shrinking
		if( strpos( $header['header_layout'], 'top_nav_header' ) !== false ) 
		{
			$header['header_menu_above'] = true;
			$header['header_shrinking']  = 'disabled';
			$transparency = false;
		}
		
		//set header transparency
		
		$header['header_transparency'] = '';
		if( ! empty( $transparency ) ) 
		{
			$header['header_transparency'] = 'header_transparency';
		}
		if( ! empty( $transparency ) && strpos( $transparency, 'glass') ) 
		{
			$header['header_transparency'] .= ' header_glassy';
		}
		if( ! empty( $transparency ) && strpos( $transparency, 'with_border') ) 
		{
			$header['header_transparency'] .= ' header_with_border';
		}
		if( ! empty( $transparency ) && strpos( $transparency, 'hidden') ) 
		{
			$header['disabled'] = true;
		}
		if( ! empty( $transparency ) && strpos( $transparency, 'scrolldown') ) 
		{
			$header['header_transparency'] .= ' header_scrolldown';
			$header['header_sticky'] = 'header_sticky';
		}
		
		//	deactivate title bar if header is transparent
		if( ! empty( $transparency ) ) 
		{
			$header['header_title_bar'] = 'hidden_title_bar';
		}
		
		//	sticky and shrinking are tied together
		if( $header['header_sticky'] == 'disabled' ) 
		{ 
			$header['header_shrinking'] = 'disabled'; 
			$header['header_scroll_offset'] =  0;
		}
		
		//if the custom height is less than 70 shrinking doesnt really work
		if( $header['header_size'] == 'custom' && (int) $header['header_custom_size'] < 65 ) 
		{
			$header['header_shrinking'] = 'disabled';
		}
		
		//deactivate icon menu if we dont have the correct header
		if( strpos( avia_get_option( 'header_layout' ), 'main_nav_header' ) === false ) 
		{
			$header['menu_display'] = '';
		}
		
		if( $header['menu_display'] == 'burger_menu' ) 
		{ 
			$header['header_menu_border'] = '';
		}
		
		if( avia_is_burger_menu() )
		{
			$header['header_mobile_activation'] = 'mobile_menu_tablet';
		}
		
		//create a header class so we can style properly
		$header_class_var = array(	
								'header_position', 
								'header_layout', 
								'header_size', 
								'header_sticky', 
								'header_shrinking', 
								'header_stretch', 
								'header_mobile_activation', 
								'header_transparency', 
								'header_searchicon', 
								'header_unstick_top',
								'header_menu_border',
								'header_style'
							);
								
		$header['header_class'] = '';
		
		foreach( $header_class_var as $class_name )
		{
			if( ! empty( $header[ $class_name ] ) )
			{
				if( $header[ $class_name ] == 'disabled' ) 
				{
					$header[$class_name] = $class_name . '_disabled';
				}
				
				$header['header_class'] .= ' av_' . str_replace( ' ', ' av_', $header[ $class_name ] );
			}
		}
		
		//set manual flag if we should display the top bar
		$header['header_topbar'] = false;
		if( strpos( $header['header_social'], 'extra_header_active' ) !== false || strpos( $header['header_secondary_menu'], 'extra_header_active' ) !== false || ! empty( $header['header_phone_active'] ) )
		{ 
			$header['header_topbar'] = 'header_topbar_active';
		}
		
		//set manual flag if the menu is at the bottom
		$header['bottom_menu'] = false;
		if( strpos( $header['header_layout'],'bottom_nav_header' ) !== false ) 
		{
			$header['bottom_menu'] = 'header_bottom_menu_active'; 
		}
		else
		{
			$header['header_class'] .= ' av_bottom_nav_disabled ';
		} 
		
		//header class that tells us to use the alternate logo
		if( ! empty( $header['header_replacement_logo'] ) )
		{
			$header['header_class'] .= ' av_alternate_logo_active'; 
			
			if( ! is_numeric( $header['header_replacement_logo'] ) && function_exists( 'attachment_url_to_postid' ) )
			{
				$found = attachment_url_to_postid( $header['header_replacement_logo'] );
				if( $found > 0 )
				{
					$header['header_replacement_logo'] = $found;
				}
			}
			
			if( is_numeric( $header['header_replacement_logo'] ) )
			{
				$header_replacement_logo_id = $header['header_replacement_logo'];
				
				//	@since 4.8.2 support for responsive logo for retina screens
				$header_replacement_logo_src = Av_Responsive_Images()->responsive_image_src( $header_replacement_logo_id, 'full' );
				
				if( is_array( $header_replacement_logo_src ) )
				{
					$header['header_replacement_logo'] = $header_replacement_logo_src[0];
					
					/**
					 * We added title and alt attribute - this allows to ignore it
					 * 
					 * @since 4.5.7.2
					 * @param boolean
					 * @param int
					 * @rturn boolean
					 */
					if( false === apply_filters( 'avf_hide_transparency_logo_meta', false, $header_replacement_logo_id ) )
					{
						$header['header_replacement_logo_title'] = get_the_title( $header_replacement_logo_id );
						$header['header_replacement_logo_alt'] = get_post_meta( $header_replacement_logo_id, '_wp_attachment_image_alt', true );
						$header['header_replacement_logo_srcset'] = $header_replacement_logo_src['srcset'];
						$header['header_replacement_logo_sizes'] = $header_replacement_logo_src['sizes'];
					}
				}
			}
			
			$header_filtered = array(
								'header_replacement_logo'			=> $header['header_replacement_logo'],
								'header_replacement_logo_title'		=> $header['header_replacement_logo_title'],
								'header_replacement_logo_alt'		=> $header['header_replacement_logo_alt'],
								'header_replacement_logo_srcset'	=> $header['header_replacement_logo_srcset'],
								'header_replacement_logo_sizes'		=> $header['header_replacement_logo_sizes'],
							);
			
			/**
			 * @since 4.7.4.1
			 * @param array $header_filtered
			 * @return array
			 */
			$header_filtered = apply_filters( 'avf_transparency_logo_data', $header_filtered );
			
			$header = array_merge( $header, $header_filtered );
		}
		
		//header class that tells us to use the alternate logo
		if( empty( $header['header_menu_border'] ) )
		{
			$header['header_class'] .= ' av_header_border_disabled'; 
		}
		
		/**
		 * Modify the header settings
		 * 
		 * @param array $header
		 * @param string $context
		 * @return array
		 */
		$header = apply_filters('avf_header_setting_filter', $header, 'setting_header' );

		//make settings available globaly
		$avia_config['header_settings'] = $header;
		
		if( ! empty( $single_val ) && isset( $header[ $single_val ] ) ) 
		{
			return $header[ $single_val ];
		}
		
		return $header;
	}
}

if( ! function_exists( 'avia_header_setting_sidebar' ) )
{
	/**
	 * 
	 * @param array $header
	 * @param string|false $single_val
	 * @return array
	 */
	function avia_header_setting_sidebar( $header, $single_val = false )
	{
		$overwrite = array(  	
							'header_layout'				=> 'logo_left menu_right', 
							'header_size'				=> 'slim', 
							'header_custom_size'		=> '', 
							'header_sticky'				=> 'disabled', 
							'header_shrinking'			=> 'disabled', 
							'header_title_bar'			=> 'hidden_title_bar',
							'header_social'				=> '', 
							'header_secondary_menu'		=> '', 
							'header_stretch'			=> '',
							'header_custom_size'		=> '',
							'header_phone_active'		=> 'disabled',
							'header_replacement_logo'	=> '',
							'header_replacement_logo_title'		=> '',
							'header_replacement_logo_alt'		=> '',
							'header_replacement_logo_srcset'	=> '',
							'header_replacement_logo_sizes'		=> '',
							'header_replacement_menu'	=> '',
							'header_mobile_activation'	=> 'mobile_menu_phone',
							'phone'						=>'',
							'header_menu_border'		=> '',
							'header_topbar'				=> false,
							'bottom_menu'				=> false,
							'header_style'				=> '',
							'menu_display'				=> '',
							'submenu_clone'				=> 'av-submenu-noclone',
						);
		
		$header = array_merge( $header, $overwrite );
		
		//	Reset to actual user setting - otherwise burger menu will result in wrong behaviour
		$settings = avia_get_option();
		$header['submenu_clone'] = isset( $settings['submenu_clone'] ) && in_array( $settings['submenu_clone'], array( 'av-submenu-clone', 'av-submenu-noclone' ) ) ? $settings['submenu_clone'] : 'av-submenu-noclone';
	
		if( strpos($header['header_position'] , 'left') === false ) 
		{
			$header['sidebarmenu_sticky'] = 'never_sticky';
		}
		
		$header['header_class'] = ' av_' . str_replace( ' ', ' av_', $header['header_position'] . ' ' . $header['sidebarmenu_sticky'] );
		
		/**
		 * Modify the header settings
		 * 
		 * @param array $header
		 * @param string $context
		 * @return array
		 */
		$header = apply_filters( 'avf_header_setting_filter', $header, 'setting_sidebar' );
		
		//make settings available globaly
		$avia_config['header_settings'] = $header;
		
		if( ! empty( $single_val ) && isset( $header[ $single_val ] ) ) 
		{
			return $header[ $single_val ];
		}
		
		return $header;
	}
}


if(!function_exists('avia_get_header_scroll_offset'))
{
	function avia_get_header_scroll_offset($header = array())
	{
			//#main data attribute used to calculate scroll offset
			$modifier = 0;
			
			if(empty($header)) 
			{
				$header['header_position'] = avia_get_option('header_position','header_top');
				$header['header_size'] = avia_get_option('header_size');
				$header['header_custom_size'] = avia_get_option('header_custom_size');
				$header['header_style'] = avia_get_option('header_style');
			}
			
			if('minimal_header' == $header['header_style']) $modifier = 2;
			
			switch($header['header_size'])
			{
				case 'large': 	$header['header_scroll_offset'] = 116; break;
				case 'custom': 	$header['header_scroll_offset'] = $header['header_custom_size'] - $modifier; break;
				default : 		$header['header_scroll_offset'] = 88; break;
			}
			
			if($header['header_position'] != 'header_top') $header['header_scroll_offset'] = 0;
			
			return $header['header_scroll_offset'];
	}
}

if(!function_exists('avia_header_class_string'))
{
	function avia_header_class_string($necessary = array() , $prefix = 'html_'){
		
		if(empty($necessary)) $necessary = array(	'header_position', 
													'header_layout', 
													'header_size', 
													'header_sticky',
													'header_shrinking', 
													'header_topbar', 
													'header_transparency',
													'header_mobile_activation',
													'header_searchicon',
													'layout_align_content',
													'header_unstick_top',
													'header_stretch',
													'header_style',
													'blog_global_style',
													'menu_display',
													'submenu_visibility',
													'overlay_style',
													'alternate_menu',
													'submenu_clone',

												);

		$settings  	= avia_header_setting();
		$class		= array();
		$post_id 	= function_exists('avia_get_the_id') ? avia_get_the_id() : get_the_ID();
		
		foreach($necessary as $class_name)
		{
			if(!empty($settings[$class_name]))
			{
				$result = array_filter(explode(' ', $settings[$class_name]));
				$class = array_merge($class, $result);
			}
		}
		
		if($post_id) $class[] = 'entry_id_'.$post_id;
		if(is_admin_bar_showing()) $class[] = 'av_admin_bar_active';
		
		
		
		$class = apply_filters('avf_header_classes', $class, $necessary, $prefix);
		
		if(!empty($class))
		{
			$class = array_unique($class);
			$class = ' '.$prefix.implode(' '.$prefix, $class);
		}

		
		return $class;
	}
}


if( ! function_exists( 'avia_blog_class_string' ) )
{
	/**
	 * Returns class string for disabled blog data
	 * 
	 * @since ???
	 * @param array $necessary
	 * @param string $prefix
	 * @return string
	 */
	function avia_blog_class_string( array $necessary = array(), $prefix = 'av-' )
	{
		if( empty( $necessary ) ) 
		{
			$necessary = array(	
							'blog-meta-author', 
							'blog-meta-comments', 
							'blog-meta-category', 
							'blog-meta-date',
							'blog-meta-html-info', 
							'blog-meta-tag', 
						);
		}
		
		$class = array();
		$settings = avia_get_option();
	
		foreach( $necessary as $class_name )
		{
			if( isset( $settings[ $class_name ] ) && $settings[ $class_name ] == 'disabled' ) 
			{
				$class[] = $class_name . '-disabled';
			}
		}
		
		$class = array_filter( array_unique( $class ) );
		
		if( empty( $class ) )
		{
			return '';
		}
		
		return ' ' . $prefix . implode( ' ' . $prefix, $class );
	}
}



if(!function_exists('avia_header_html_custom_height'))
{
	function avia_header_html_custom_height()
	{
		$settings = avia_header_setting();
		
		if($settings['header_size'] == "custom")
		{
			$modifier = 0;
			$size = $settings['header_custom_size'];
			$bottom_bar = $settings['bottom_menu'] == true ? 52 : 0;
			$top_bar	= $settings['header_topbar'] == true ? 30 : 0;
			
			if(!empty($settings['header_style']) && "minimal_header" == $settings['header_style'] ){ $modifier = 2;}
			
			
			$html =  "";
			$html .= "\n<style type='text/css' media='screen'>\n";
			$html .= " #top #header_main > .container, #top #header_main > .container .main_menu  .av-main-nav > li > a,";
			$html .= " #top #header_main #menu-item-shop .cart_dropdown_link{ height:{$size}px; line-height: {$size}px; }\n";
			$html .= " .html_top_nav_header .av-logo-container{ height:{$size}px;  }\n";
			$html .= " .html_header_top.html_header_sticky #top #wrap_all #main{ padding-top:".((int)$size + $bottom_bar + $top_bar - $modifier)."px; } \n";
			$html .= "</style>\n";
			
			echo $html;
		}
		
	}

	add_action('wp_head', 'avia_header_html_custom_height');
	
}


/*
* Display sidebar widgets in the main navigation area when it is set as sidebar instead of top
*/
if(!function_exists('avia_sidebar_menu_additions'))
{
	function avia_sidebar_menu_additions()
	{
		$settings = avia_header_setting();
		$output   = "";
		
		if($settings['header_position'] != "header_top")
		{
			/*add social icons*/
			if($settings['sidebarmenu_social'] != "disabled")
			{
				$social_args = array('outside'=>'ul', 'inside'=>'li', 'append' => '');
				$social	= avia_social_media_icons($social_args, false);
				if($social) $output .= "<div class='av-sidebar-social-container'>".$social."</div>";
			}
			
		
			/*add widgets*/
			if(!empty( $settings['sidebarmenu_widgets']))
			{
				if('av-auto-widget-logic' == $settings['sidebarmenu_widgets'])
				{
				
				}
				else if( is_dynamic_sidebar( $settings['sidebarmenu_widgets'] ) )
				{
					ob_start();
					dynamic_sidebar( $settings['sidebarmenu_widgets'] );
					$output .= ob_get_clean();
					$output = "<aside class='avia-custom-sidebar-widget-area sidebar sidebar_right'>".$output."</aside>";
				}
			}
		}
		
		echo $output;
		
	}

	add_action('ava_after_main_menu', 'avia_sidebar_menu_additions');
	
}





/*
* Display a subnavigation for pages that is automatically generated, so the users doesnt need to work with widgets
*/
if(!function_exists('avia_sidebar_menu'))
{
    function avia_sidebar_menu($echo = true)
    {
        $sidebar_menu = "";

        $subNav = avia_get_option('page_nesting_nav');
  
        
        $the_id = @get_the_ID();
        $args 	= array();
		global $post;

        if($subNav && $subNav != 'disabled' && !empty($the_id) && is_page())
        {
            $subNav = false;
            $parent = $post->ID;
            $sidebar_menu = "";

            if (!empty($post->post_parent))
            {
                if(isset($post->ancestors)) $ancestors  = $post->ancestors;
                if(!isset($ancestors)) $ancestors  = get_post_ancestors($post->ID);
                $root		= count($ancestors)-1;
                $parent 	= $ancestors[$root];
            }

            $args = array('title_li'=>'', 'child_of'=>$parent, 'echo'=>0, 'sort_column'=>'menu_order, post_title');

            //enables user to change query args
            $args = apply_filters('avia_sidebar_menu_args', $args, $post);

            //hide or show child pages in menu - if the class is set to 'widget_nav_hide_child' the child pages will be hidden
            $display_child_pages = apply_filters('avia_sidebar_menu_display_child', 'widget_nav_hide_child', $args, $post);

            $children = wp_list_pages($args);

            if ($children)
            {
                $default_sidebar = false;
                $sidebar_menu .= "<nav class='widget widget_nav_menu $display_child_pages'><ul class='nested_nav'>";
                $sidebar_menu .= $children;
                $sidebar_menu .= "</ul></nav>";
            }
        }

        $sidebar_menu = apply_filters('avf_sidebar_menu_filter', $sidebar_menu, $args, $post);

        if($echo == true) { echo $sidebar_menu; } else { return $sidebar_menu; }
    }
}



/*
show tag archive page for post type - without this code you'll get 404 errors: http://wordpress.org/support/topic/custom-post-type-tagscategories-archive-page
*/
if(!function_exists('avia_fix_tag_archive_page'))
{
	function avia_fix_tag_archive_page($query)
	{
	    $post_types = get_post_types();

	    if ( is_category() || is_tag())
	    {
			if(!is_admin() && $query->is_main_query() )
	        {
		        $post_type = get_query_var(get_post_type());

		        if ($post_type) {
		            $post_type = $post_type;
		        } else {
		            $post_type = $post_types;
		        }
		        $query->set('post_type', $post_type);
			}
	    }


	    return $query;
	}
	add_filter('pre_get_posts', 'avia_fix_tag_archive_page');
}



/*
 * add html5.js script to head section - required for IE compatibility
 */
if(!function_exists('avia_print_html5_js_script'))
{
    add_action('wp_head', 'avia_print_html5_js_script');

    function avia_print_html5_js_script()
    {
        $template_url = get_template_directory_uri();
        $output = '';

        $output .= '<!--[if lt IE 9]>';
        $output .= '<script src="'.$template_url.'/js/html5shiv.js"></script>';
        $output .= '<![endif]-->';
        echo $output;
    }
}


if(!function_exists('avia_add_compat_header'))
{
	add_filter('wp_headers', 'avia_add_compat_header');
	function avia_add_compat_header($headers)
	{
		if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
		{
	    	$headers['X-UA-Compatible'] = 'IE=edge,chrome=1';
	    }
	    return $headers;
	}
}


if( ! function_exists( 'avia_add_hide_featured_image_select' ) )
{	
	/**
	 * Add a select box to hide featured image on single post
	 * 
	 * @param array $elements
	 * @return array
	 */
	function avia_add_hide_featured_image_select( array $elements )
	{	
		if( ! is_admin() || ! function_exists( 'get_current_screen' ) )
		{
			return $elements;
		}
		
		$screen = get_current_screen();
		if( ! $screen instanceof WP_Screen )
		{
			return $elements;
		}

		$hide_pt = apply_filters( 'avf_display_featured_image_posttypes', array( 'post', 'portfolio' ) );
		
		if( ! in_array( $screen->post_type, $hide_pt ) )
		{
			return $elements;
		}
		
		switch( $screen->post_type )
		{
			case 'post':
				$desc = __( 'Select to display featured image for a single post entry.', 'avia_framework' );
				break;
			case 'portfolio':
				$desc = __( 'Select to display featured image for a single portfolio entry.', 'avia_framework' );
				break;
			default:
				$desc = apply_filters( 'avf_display_featured_image_desc', __( 'Select to display featured image for a single entry.', 'avia_framework' ) );
				break;
		}
		
		$elements[] = array(
						'slug'		=> 'layout',
						'name'		=> __( 'Featured Image', 'avia_framework' ),
						'desc'		=> $desc,
						'id'		=> '_avia_hide_featured_image',
						'type'		=> 'select',
						'std'		=> '',
						'class'		=> 'avia-style',
						'subtype'	=> array( 
											__( 'Show on single entry', 'avia_framework' ) => '',
											__( 'Hide on single entry', 'avia_framework' ) => '1'
										)
					);
	   
		return $elements;
	}
	
	add_filter( 'avf_builder_elements', 'avia_add_hide_featured_image_select', 10, 1 );
}

if(!function_exists('avia_active_caching'))
{
	function avia_active_caching()
	{
		if(defined('W3TC') || defined('WPCACHEHOME') || class_exists('HyperCache') || class_exists('\\quick_cache\\plugin'))
		{
			return true;
		}
		return false;
	}
}





if( ! function_exists('avia_menu_button_style' ) )
{
	add_action( 'wp_nav_menu_item_custom_fields', 'avia_menu_button_style', 10, 5 );

	function avia_menu_button_style( $output, $item, $depth, $args, $current_object_id = 0 )
	{
	        $item_id = $item->ID;
	        $key = "style";
			$name  = "menu-item-avia-".$key; //name prefix must be the same for all items
			$value = get_post_meta( $item->ID, '_'.$name, true);
	        ?>
	
	        <!-- *************** start conditional logic input fields *************** -->
	        <p class="field-avia-link-style description description-wide avia_mega_menu avia_mega_menu_d0">
				<label for="<?php echo $key; ?>">
					<?php _e( 'Menu Style' ); ?><br />
					<select id="<?php echo $name . "-". $item_id;?>" class="widefat edit-menu-item-target" name="<?php echo $name . "[". $item_id ."]";?>">
						<option value="" 										<?php selected( $value,  ''); ?>										><?php _e('Default Style'); ?>	</option>
						<option value="av-menu-button av-menu-button-colored" 	<?php selected( $value,  'av-menu-button av-menu-button-colored'); ?>	><?php _e('Button Style (Colored)' ); ?>	</option>
						<option value="av-menu-button av-menu-button-bordered" 	<?php selected( $value,  'av-menu-button av-menu-button-bordered'); ?>	><?php _e('Button Style (Bordered)'); ?>	</option>
					</select>
				</label>
			</p>
	        
	        <?php
	        
	        
	}
	
	add_filter('avf_mega_menu_post_meta_fields','avia_menu_button_style_save',10,3);
    function avia_menu_button_style_save($check, $menu_id, $menu_item_db)
    {
        $check = array_merge($check, array('style'));
        return $check;
    }
	
	
}
if(!function_exists('avia_generate_grid_dimension'))
{
	add_action('ava_generate_styles','avia_generate_grid_dimension', 30, 3); /*after theme update*/
	
	function avia_generate_grid_dimension($options, $color_set, $styles)
	{
		global $avia_config;
		if ( $options !== "" ){ extract($options); }
		
		if(empty($content_width))  $content_width 	= 73;
		if(empty($combined_width)) $combined_width 	= 100;
		if(empty($responsive_size)) $responsive_size = "1130px";
		
		if($responsive_size != "")
		{
			$avia_config['style'][] = array(
			'key'	=>	'direct_input',
			'value'	=> ".container {width:".$combined_width."%;} .container .av-content-small.units {width:".$content_width."%; }
			
			  .responsive .boxed#top , .responsive.html_boxed.html_header_sticky #header, 
			  .responsive.html_boxed.html_header_transparency #header{ width: ".$responsive_size."; max-width:90%; }
			  .responsive .container{ max-width: ".$responsive_size."; }
			"
			);
		}
	}
}



/*
function that disables the alb drag and drop for non admins
*/

if( ! function_exists( 'avia_disable_alb_drag_drop' ) )
{
	function avia_disable_alb_drag_drop( $disable )
	{
		if( ! current_user_can( 'switch_themes' ) || avia_get_option( 'lock_alb_for_admins', 'disabled' ) != 'disabled' )
		{
			$disable = avia_get_option( 'lock_alb', 'disabled' ) != 'disabled' ? true : false;
		}		
		
		return $disable;
	}
	
	add_filter( 'avf_allow_drag_drop', 'avia_disable_alb_drag_drop', 30, 1 );
}







/*
function to display frame
*/

if(!function_exists('avia_framed_layout'))
{
	function avia_framed_layout($options, $color_set, $styles)
	{
		global $avia_config;
		extract($styles);

		if(isset($body_style) && $body_style === "av-framed-box")
		{
			$avia_config['style'][] = array(
			'key'	=>	'direct_input',
			'value'	=> "
			
			html.html_av-framed-box{ padding:{$frame_width}px; }
			html.html_av-framed-box{ padding:{$frame_width}px; }
			html.html_av-framed-box .av-frame{ width: {$frame_width}px; height: {$frame_width}px; background:$body_color;}
			
			
			.html_header_top.html_header_sticky.html_av-framed-box #header_main,
			.html_header_top.html_header_sticky.html_av-framed-box #header_meta{
				margin:0 {$frame_width}px;
			}
			
			html .avia-post-prev{left: {$frame_width}px; }
			html .avia-post-next{right:{$frame_width}px; }
			
			html.html_av-framed-box.html_av-overlay-side .av-burger-overlay-scroll{ right:{$frame_width}px; }
			"
			);
		}
	}
	
	add_action('ava_generate_styles', 'avia_framed_layout', 40 , 3);
}

if(!function_exists('avia_framed_layout_bars'))
{
	function avia_framed_layout_bars()
	{
		if( avia_get_option('color-body_style') == "av-framed-box" )
		{
			$output  = "";
			$output .= "<div class='av-frame av-frame-top av-frame-vert'></div>";
			$output .= "<div class='av-frame av-frame-bottom av-frame-vert'></div>";
			$output .= "<div class='av-frame av-frame-left av-frame-hor'></div>";
			$output .= "<div class='av-frame av-frame-right av-frame-hor'></div>";
			
			echo $output;
		}
	}
	
	add_action('wp_footer', 'avia_framed_layout_bars', 10 );
}








/*
function that saves the style options array into an external css file rather than fetching the data from the database
*/

if(!function_exists('avia_generate_stylesheet'))
{
	add_action('ava_after_theme_update', 			'avia_generate_stylesheet', 30, 1); /*after theme update*/
	add_action('ava_after_import_demo_settings', 	'avia_generate_stylesheet', 30, 1); /*after demo settings imoport*/
	add_action('avia_ajax_after_save_options_page', 'avia_generate_stylesheet', 30, 1); /*after options page saving*/
	
	function avia_generate_stylesheet($options = false)
	{
		global $avia;
		
		$safe_name = avia_backend_safe_string($avia->base_data['prefix']);
		$safe_name = apply_filters('avf_dynamic_stylesheet_filename', $safe_name);

	    if( defined('AVIA_CSSFILE') && AVIA_CSSFILE === false )
	    {
	        $dir_flag           = update_option( 'avia_stylesheet_dir_writable'.$safe_name, 'false' );
	        $stylesheet_flag    = update_option( 'avia_stylesheet_exists'.$safe_name, 'false' );
	        return;
	    }

	    $wp_upload_dir  = wp_upload_dir();
	    $stylesheet_dir = $wp_upload_dir['basedir'] . '/dynamic_avia';
	    $stylesheet_dir = str_replace('\\', '/', $stylesheet_dir);
	    $stylesheet_dir = apply_filters('avia_dyn_stylesheet_dir_path',  $stylesheet_dir);
	    $isdir = avia_backend_create_folder($stylesheet_dir);

	    /*
	    * directory could not be created (WP upload folder not writeable)
	    * @todo save error in db and output error message for user.
	    * @todo maybe add mkdirfix: http://php.net/manual/de/function.mkdir.php
	    */

	    if($isdir === false)
	    {
	        $dir_flag = update_option( 'avia_stylesheet_dir_writable'.$safe_name, 'false' );
	        $stylesheet_flag = update_option( 'avia_stylesheet_exists'.$safe_name, 'false' );
	        return;
	    }

	    /*
	     *  Go ahead - WP managed to create the folder as expected
	     */
	    $stylesheet = trailingslashit( $stylesheet_dir ) . $safe_name.'.css';
	    $stylesheet = apply_filters('avia_dyn_stylesheet_file_path', $stylesheet);


	    //import avia_superobject and reset the options array
	    $avia_superobject = $GLOBALS['avia'];
		$avia_superobject->reset_options();

		//regenerate style array after saving options page so we can create a new css file that has the actual values and not the ones that were active when the script was called
		avia_prepare_dynamic_styles();

	    //generate stylesheet content
	    $generate_style = new avia_style_generator($avia_superobject,false,false,false);
	    $styles         = $generate_style->create_styles();

	    $created        = avia_backend_create_file($stylesheet, $styles, true);

	    if($created === true)
	    {
	        $dir_flag = update_option( 'avia_stylesheet_dir_writable'.$safe_name, 'true' );
	        $stylesheet_flag = update_option( 'avia_stylesheet_exists'.$safe_name, 'true' );
			$dynamic_id = update_option( 'avia_stylesheet_dynamic_version'.$safe_name, uniqid() );
	    }
	    else
	    {
		    $dir_flag = update_option( 'avia_stylesheet_dir_writable'.$safe_name, 'false' );
	        $stylesheet_flag = update_option( 'avia_stylesheet_exists'.$safe_name, 'false' );
			$dynamic_id = delete_option( 'avia_stylesheet_dynamic_version'.$safe_name);
	    }
	}
}



/*favicon in front and backend*/
add_action('wp_head', 'avia_add_favicon');
add_action('admin_head', 'avia_add_favicon');


function avia_add_favicon()
{ 
	echo "\n".avia_favicon(avia_get_option('favicon'))."\n";
}






/**
 * AVIA Mailchimp WIDGET
 */

if (!class_exists('avia_mailchimp_widget'))
{
		class avia_mailchimp_widget extends WP_Widget {
	
		static $script_loaded = 0;
	
		function __construct() {
			//Constructor
			$widget_ops = array('classname' => 'avia_mailchimp_widget', 'description' => 'A widget that displays a Mailchimp newsletter signup form' );
			parent::__construct( 'avia_mailchimp_widget', THEMENAME.' Mailchimp Newsletter Signup', $widget_ops );
		}

		function widget($args, $instance)
		{
			extract($args, EXTR_SKIP);
			echo $before_widget;
			
			if ( !empty( $instance['title'] ) ) { echo $before_title . $instance['title'] . $after_title; };
			
			$shortcode  = "[av_mailchimp";
			$shortcode .= " list='".$instance['mailchimp_list']."'";
			$shortcode .= " listonly='true'";
			$shortcode .= " hide_labels='true'";
			$shortcode .= " double_opt_in='".$instance['double_optin']."'";
			$shortcode .= " sent='".$instance['success']."'";
			$shortcode .= " button='".$instance['submit_label']."'";
			
			$shortcode .= "]";
				
			
			echo "<div class='av-mailchimp-widget av-mailchimp-widget-style-".$instance['styling']." '>";
			echo do_shortcode($shortcode );
			echo "</div>";
			
			echo $after_widget;

		}


		function update($new_instance, $old_instance)
		{
			$instance = $old_instance;
			$instance['title'] 			= strip_tags($new_instance['title']);
			$instance['success'] 		= strip_tags($new_instance['success']);
			$instance['styling'] 		= strip_tags($new_instance['styling']);
			$instance['double_optin'] 	= strip_tags($new_instance['double_optin']);
			$instance['mailchimp_list'] = strip_tags($new_instance['mailchimp_list']);
			$instance['submit_label'] = strip_tags($new_instance['submit_label']);
			
			return $instance;
		}



		function form($instance)
		{
			$instance = wp_parse_args( (array) $instance, array( 
				'title' 			=> __('Newsletter','avia_framework'), 
				'mailchimp_list' 	=> '', 
				'styling' 			=> '' , 
				'double_optin' 		=> 'true', 
				'success' 			=> __('Thank you for subscribing to our newsletter!','avia_framework'), 
				'submit_label' 		=> __('Subscribe','avia_framework'), 
				) 
			);
				
			$title 			= strip_tags($instance['title']);
			$mailchimp_list = strip_tags($instance['mailchimp_list']);
			$styling 		= strip_tags($instance['styling']);
			$double_optin 	= strip_tags($instance['double_optin']);
			$success 		= strip_tags($instance['success']);
			$submit_label 	= strip_tags($instance['submit_label']);

			$lists 		= get_option('av_chimplist');
			$newlist 	= array('Select a Mailchimp list...' => "");
		
			if(empty($lists))
			{
				return;
			}
			
			foreach($lists as $key => $list_item)
			{
				$newlist[$list_item['name']] = $key;
			}
			$lists = $newlist;

	?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','avia_framework');?>:
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>

			<p>
				<label for="<?php echo $this->get_field_id('mailchimp_list'); ?>"><?php _e('Mailchimp list to subscribe to','avia_framework');?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('mailchimp_list'); ?>" name="<?php echo $this->get_field_name('mailchimp_list'); ?>">
					<?php
					$list = "";

					foreach ($lists as $answer => $key)
					{
						$selected = "";
						if($key == $mailchimp_list) $selected = 'selected="selected"';

						$list .= "<option $selected value='$key'>$answer</option>";
					}
					$list .= "</select>";
					echo $list;
					?>


			</p>
			
			
			<p>
				<label for="<?php echo $this->get_field_id('styling'); ?>"><?php _e('Signup Form Styling','avia_framework');?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('styling'); ?>" name="<?php echo $this->get_field_name('styling'); ?>">
					<?php
					$answers = array(
						
						__('Default','avia_framework') => "",
						__('Boxed','avia_framework') => "boxed_form",
						
					);
					
					$list = "";
					
					foreach ($answers as $answer => $key)
					{
						$selected = "";
						if($key == $styling) $selected = 'selected="selected"';

						$list .= "<option $selected value='$key'>$answer</option>";
					}
					$list .= "</select>";
					echo $list;
					?>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('double_optin'); ?>"><?php _e('Activate double opt-in?','avia_framework');?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('double_optin'); ?>" name="<?php echo $this->get_field_name('double_optin'); ?>">
					<?php
					$answers = array(
						
						__('Yes','avia_framework') => "true",
						__('No','avia_framework') => "",
						
					);
					
					$list = "";
					
					foreach ($answers as $answer => $key)
					{
						$selected = "";
						if($key == $double_optin) $selected = 'selected="selected"';

						$list .= "<option $selected value='$key'>$answer</option>";
					}
					$list .= "</select>";
					echo $list;
					?>
			</p>
			
			<p><label for="<?php echo $this->get_field_id('success'); ?>"><?php _e('Message if user subscribes successfully','avia_framework');?>:
			<input class="widefat" id="<?php echo $this->get_field_id('success'); ?>" name="<?php echo $this->get_field_name('success'); ?>" type="text" value="<?php echo esc_attr($success); ?>" /></label></p>

			<p>
				
				<p><label for="<?php echo $this->get_field_id('submit_label'); ?>"><?php _e('Submit Button Label','avia_framework');?>:
			<input class="widefat" id="<?php echo $this->get_field_id('submit_label'); ?>" name="<?php echo $this->get_field_name('submit_label'); ?>" type="text" value="<?php echo esc_attr($submit_label); ?>" /></label></p>

			<p>



	<?php
		}
	}
		
	register_widget( 'avia_mailchimp_widget' );
}

/**
 * WP core hack see https://core.trac.wordpress.org/ticket/15551
 * 
 * Paging does not work on single custom post type pages - always a redirect to page 1 by WP
 * 
 * 
 * @since 4.0.6
 */
if( ! function_exists( 'avia_wp_cpt_request_redirect_fix' ) )
{
	function avia_wp_cpt_request_redirect_fix( $request ) 
	{
		$args = array(
					'public'	=>	true,
					'_builtin'	=>	false
				);

		$cpts = get_post_types( $args, 'names', 'and' ); 

		if (	isset( $request->query_vars['post_type'] ) &&
				in_array( $request->query_vars['post_type'], $cpts ) &&
				true === $request->is_singular &&
				- 1 == $request->current_post &&
				true === $request->is_paged 
			) 
		{
			add_filter( 'redirect_canonical', '__return_false' );
		}

		return $request;
	}

	add_action( 'parse_query', 'avia_wp_cpt_request_redirect_fix' );
}



/**
 * mobile sizes that overwrite elements default sizes
 */
if( ! function_exists( 'av_print_custom_font_size' ) )
{
	function av_print_custom_font_size( $request ) 
	{
		echo AviaHelper::av_print_mobile_sizes();
	}

	add_action( 'wp_footer', 'av_print_custom_font_size' );
}


if( ! function_exists( 'av_disable_live_preview' ) )
{
	/**
	 * Disable element live preview
	 * 
	 * @param array $data
	 * @return array
	 */
	function av_disable_live_preview( $data ) 
	{
		if( avia_get_option( 'preview_disable' ) == 'preview_disable' )
		{
			$data['preview'] = 0;
		}

		return $data;
	}

	add_filter( 'avb_backend_editor_element_data_filter', 'av_disable_live_preview', 10, 1 );
}

if( ! function_exists( 'av_attachment_copyright_field_edit' ) )
{
	/**
	 * Adds a copyright field to the upload and edit dialogue of the media manager
	 * 
	 * @author tinabillinger
	 * @since 4.3
	 * @param array $form_fields
	 * @param WP_Post $post
	 * @return array
	 */
	function av_attachment_copyright_field_edit( $form_fields, $post )
	{

		$form_fields['av_copyright_field'] = array(
												'label'	=> __( 'Copyright', 'avia_framework' ),
												'input'	=> 'text',
												'value'	=> get_post_meta( $post->ID, '_avia_attachment_copyright', true ),
											);

		return $form_fields;
	}
	
	add_filter( 'attachment_fields_to_edit', 'av_attachment_copyright_field_edit', 99, 2 );
}


if( ! function_exists( 'av_attachment_copyright_field_save' ) )
{
	/**
	 * Saves the copyright field created by filter above
	 * 
	 * @author tinabillinger
	 * @since 4.3
	 * @param array $post
	 * @param array $attachment
	 * @return array
	 */
	function av_attachment_copyright_field_save( $post, $attachment )
	{
		if( ! empty( $attachment['av_copyright_field'] ) )
		{
			update_post_meta( $post['ID'], '_avia_attachment_copyright', $attachment['av_copyright_field'] );
		}
		else 
		{
			delete_post_meta( $post['ID'], '_avia_attachment_copyright' );
		}
		
		return $post;
	}

	add_filter( 'attachment_fields_to_save', 'av_attachment_copyright_field_save', 99, 2 );
}

if( ! function_exists( 'avia_post_thumbnail_html' ) )
{
	
	/**
	 * Attaches the information from the copyright field to get_the_post_thumbnail().
	 * The added tag is initally hidden by CSS, and can be made visible by choice
	 * 
	 * @author tinabillinger
	 * @since 4.3
	 * @param string $html
	 * @param int $post_id
	 * @param int $post_thumbnail_id
	 * @param string|array $size
	 * @param string $attr
	 * @return string
	 */
	function avia_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr )
	{
		$attachment_id = get_post_thumbnail_id( $post_id );
		$copyright_text = get_post_meta( $attachment_id, '_avia_attachment_copyright', true );

		if( $copyright_text ) 
		{
			$html .= "<small class='avia-copyright'>{$copyright_text}</small>";
		}

		return $html;
	}

	if( ! is_admin() )
	{
		add_filter( 'post_thumbnail_html', 'avia_post_thumbnail_html', 99, 5 );
	}
}


if( ! function_exists( 'av_builder_meta_box_elements_content' ) )
{
	/**
	 * Adjust element content to reflect main option settings
	 * e.g. with sdding page as footer feature we need to adjust select box content of footer settings
	 * 
	 * @since 4.2.7
	 * @added_by Günter
	 * @param array $elements
	 * @return array
	 */
	function av_builder_meta_box_elements_content( array $elements )
	{
		
		$footer_options	= avia_get_option( 'display_widgets_socket', 'all' );
		
		if( false !== strpos( $footer_options, 'page' ) )
		{
			$desc = __( 'Display the footer page?', 'avia_framework' );
			$subtype = array(
							__( 'Default Layout - set in', 'avia_framework' ) . ' ' . THEMENAME. ' > ' . __( 'Footer', 'avia_framework' )	=> '',
							__( 'Use selected page to display as footer and socket', 'avia_framework' )		=> 'page_in_footer_socket',
							__( 'Use selected page to display as footer (no socket)', 'avia_framework' )	=> 'page_in_footer',
							__( 'Don\'t display the socket & page', 'avia_framework' )						=> 'nofooterarea'
						);
		}
		else 
		{
			$desc = __( 'Display the footer widgets?', 'avia_framework' );
			$subtype = array(
							__( 'Default Layout - set in', 'avia_framework' ) . ' ' . THEMENAME . ' > ' . __( 'Footer', 'avia_framework' ) => '',
							__( 'Display the footer widgets & socket', 'avia_framework' )					=> 'all',
							__( 'Display only the footer widgets (no socket)', 'avia_framework' )			=> 'nosocket',
							__( 'Display only the socket (no footer widgets)', 'avia_framework' )			=> 'nofooterwidgets',
							__( 'Don\'t display the socket & footer widgets', 'avia_framework' )			=> 'nofooterarea'
						);
		}
		
		foreach( $elements as &$element ) 
		{
			if( 'footer' == $element['id'] )
			{
				$element['desc'] = $desc;
				$element['subtype'] = $subtype;
			}		
		}
		
		return $elements;
	}
	
	add_filter( 'avf_builder_elements', 'av_builder_meta_box_elements_content', 10000, 1 );
}


/**
 * Comment form order
 * Restore comment form order to look like previous versions were comment field is below name/mail/website
 *
 * @author Kriesi
 * @since 4.5
 */

if( ! function_exists( 'av_comment_field_order_reset' ) ) 
{
	function av_comment_field_order_reset( $fields ) 
	{
		$comment_field = $fields['comment'];
		unset( $fields['comment'] );
		$fields['comment'] = $comment_field;
		return $fields;
	}
 
	add_filter( 'comment_form_fields', 'av_comment_field_order_reset' );
}


if( ! function_exists( 'avia_remove_query_strings' ) )
{
	
	/**
	 * Remove query strings (like version) from static resources in production.
	 * 
	 * @since 4.7.4.1
	 * @param string $src
	 * @param string $handle
	 * @return string
	 */
	function avia_remove_query_strings( $src, $handle = '' )
	{
		if( defined( 'WP_DEBUG' ) && WP_DEBUG )
		{
			return $src;
		}
		
		if( avia_get_option( 'remove_query_string_from_resources', '' ) != 'remove_query_string_from_resources' )
		{
			return $src;
		}		
		
		$source = preg_split( "/(&ver|\?ver)/", $src );
		return $source[0];
	}
	
	if( ! is_admin() ) 
	{
		add_filter( 'script_loader_src', 'avia_remove_query_strings', 15, 2 );
		add_filter( 'style_loader_src', 'avia_remove_query_strings', 15, 2 );
	}
	
}
		
