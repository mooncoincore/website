<?php

if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


/* - WPML compatibility - */
if( defined( 'ICL_SITEPRESS_VERSION' ) && defined( 'ICL_LANGUAGE_CODE' ) )
{
	require_once 'config_legacy.php';
	require_once 'class-avia-wpml.php';
	

	
	add_filter( 'avia_filter_backend_page_title', 'avia_wpml_backend_page_title' );
	add_action( 'init', 'avia_wpml_backend_language_switch' );
    add_filter( 'avf_execute_avia_meta_header', '__return_true', 10, 1 );
	


	if( ! function_exists( 'avia_wpml_backend_language_switch' ) )
	{
		/**
		 * Language switch hook for the backend
		 */
		function avia_wpml_backend_language_switch()
		{
			if( isset( $_GET['lang'] ) && is_admin() )
			{
				/**
				 * @used_by		avia_WPML::handler_ava_copy_options			10
				 */
				do_action( 'avia_wpml_backend_language_switch' );
			}
		}
	}

	if( ! function_exists( 'avia_wpml_backend_page_title' ) )
	{
		/**
		 * Filters the menu entry in the backend and displays the language in addition to the theme name
		 *
		 * @param string $title
		 * @return string
		 */
		function avia_wpml_backend_page_title( $title )
		{
			if( ICL_LANGUAGE_CODE == '' )
			{
				return $title;
			}

			$append = "";
			if( 'all' != ICL_LANGUAGE_CODE )
			{
				$append = " (" . strtoupper( ICL_LANGUAGE_CODE ) . ")";
			}
			else
			{
				global $avia_config;

				$wpml_options = $avia_config['wpml']['settings'];
				$append = " (" . strtoupper( $wpml_options['default_language'] ) . ")";
			}
			
			return $title . $append;
		}
	}


	if( ! function_exists( 'avia_wpml_register_post_type_permalink' ) )
	{
		/**
		 * Creates an additional dynamic slug rewrite rule for custom categories
		 * 
		 * @return boolean
		 */
		function avia_wpml_register_post_type_permalink() 
		{
			global $wp_post_types, $wp_rewrite, $wp, $avia_config;

			if( ! isset( $avia_config['custom_post'] ) )
			{
				return false;
			}

			$slug_array = Avia_WPML()->wpml_get_options( 'portfolio-slug' );

			foreach( $avia_config['wpml']['lang'] as $lang => $values )
			{
				foreach( $avia_config['custom_post'] as $post_type => $arguments )
				{
					$args = (object) $arguments['args'];
					$args->rewrite['slug'] = $slug_array[ $lang ];
					$args->permalink_epmask = EP_PERMALINK;
					$post_type = sanitize_key( $post_type );

					if ( false !== $args->rewrite && ( is_admin() || '' != get_option('permalink_structure') ) )
					{
						$wp_rewrite->add_permastruct( "{$post_type}_{$lang}", "{$args->rewrite['slug']}/%{$post_type}%", $args->rewrite['with_front'], $args->permalink_epmask );
					}
				}
			}
			
			return true;
		}
	}

	/*
	* Filters the links generated for the language switcher in case a user is viewing a single portfolio entry and changes the portfolio slug if necessary
	*/
	if( ! function_exists( 'avia_wpml_url_filter' ) )
	{
		function avia_wpml_url_filter( $lang )
		{
			$post_type	= get_post_type();

			if( "portfolio" == $post_type )
			{
				$slug = Avia_WPML()->wpml_get_options( 'portfolio-slug' );
				$current = isset( $slug[ICL_LANGUAGE_CODE] ) ? $slug[ICL_LANGUAGE_CODE] : "";
				
				foreach( $lang as $key => $options )
				{
					if( isset( $options['url'] ) && $current != "" && $current != $slug[$key] && "" != $slug[ $key ] )
					{
						$lang[ $key ]['url'] = str_replace( "/" . $current . "/", "/" . $slug[ $key ] . "/", $lang[ $key ]['url'] );
					}
				}
			}
			
			return $lang;
		}
	}

	/*
	* styleswitcher for the avia framework
	*/
	if( ! function_exists( 'avia_wpml_language_switch' ) )
	{
		add_action( 'avia_meta_header', 'avia_wpml_language_switch', 10 );
		add_action( 'ava_main_header_sidebar', 'avia_wpml_language_switch', 10 );

		function avia_wpml_language_switch()
		{
			global $sitepress, $avia_config;
            
            if( empty( $avia_config['wpml_language_menu_position'] ) ) 
			{
				$avia_config['wpml_language_menu_position'] = apply_filters( 'avf_wpml_language_switcher_position', 'sub_menu' );
			}
			
            if( $avia_config['wpml_language_menu_position'] != 'sub_menu' ) 
			{
				return;
			}

			// icl_get_languages deprecated since 3.2
			$languages = function_exists( 'wpml_get_active_languages_filter' ) ? wpml_get_active_languages_filter( '', 'skip_missing=0&orderby=custom' ) : icl_get_languages( 'skip_missing=0&orderby=custom' );
			$output = '';

			if( is_array( $languages ) )
			{
				$output .= "<ul class='avia_wpml_language_switch avia_wpml_language_switch_extra'>";

				foreach( $languages as $lang )
				{
					$currentlang = ( ICL_LANGUAGE_CODE == $lang['language_code'] ) ? 'avia_current_lang' : '';

					if( ! avia_is_overview() && ( is_home() || is_front_page() ) )
					{
						$url = $sitepress->language_url( $lang['language_code'] );
						
						/**
						 * @since 4.5.6.1
						 * @return string
						 */
						$lang['url'] = apply_filters( 'avf_wpml_language_switcher_url', $url, $lang['language_code'], $avia_config['wpml_language_menu_position'] );
					}
					         
					$output .= "<li class='language_" . $lang['language_code'] . " $currentlang'><a href='" . esc_url( $lang['url'] ) . "'>";
					$output .= "	<span class='language_flag'><img title='" . $lang['native_name'] . "' src='". esc_url( $lang['country_flag_url'] ) . "' alt='" . $lang['native_name'] . "' /></span>";
					$output .= "	<span class='language_native'>{$lang['native_name']}</span>";
					$output .= "	<span class='language_translated'>{$lang['translated_name']}</span>";
					$output .= "	<span class='language_code'>{$lang['language_code']}</span>";
					$output .= "</a></li>";
				}

				$output .= "</ul>";
			}

			echo $output;
		}
	}

	//Add all the necessary filters. There are a LOT of WordPress functions, and you may need to add more filters for your site.
	if( ! function_exists('avia_wpml_correct_domain_in_url' ) )
	{
		// some installs require this fix: https://wpml.org/errata/enfold-theme-styles-not-loading-with-different-domains/
		if ( ! is_admin() ) 
		{
			add_filter ('home_url', 'avia_wpml_correct_domain_in_url');
			add_filter ('site_url', 'avia_wpml_correct_domain_in_url');
			add_filter ('get_option_siteurl', 'avia_wpml_correct_domain_in_url');
			add_filter ('stylesheet_directory_uri', 'avia_wpml_correct_domain_in_url');
			add_filter ('template_directory_uri', 'avia_wpml_correct_domain_in_url');
			add_filter ('post_thumbnail_html', 'avia_wpml_correct_domain_in_url');
			add_filter ('plugins_url', 'avia_wpml_correct_domain_in_url');
			add_filter ('admin_url', 'avia_wpml_correct_domain_in_url');
			add_filter ('wp_get_attachment_url', 'avia_wpml_correct_domain_in_url');

		}

		/**
		* Changes the domain for a URL so it has the correct domain for the current language
		* Designed to be used by various filters
		*
		* @param string $url
		* @return string
		*/
		function avia_wpml_correct_domain_in_url( $url )
		{
			//  icl_get_home_url was deprecaated since 3.2
		    if( function_exists( 'wpml_get_home_url_filter' ) || function_exists( 'icl_get_home_url' ) )
		    {
		        // Use the language switcher object, because that contains WPML settings, and it's available globally
		        global $icl_language_switcher, $avia_config;

		        // Only make the change if we're using the languages-per-domain option
		        if( isset( $icl_language_switcher->settings['language_negotiation_type'] ) && $icl_language_switcher->settings['language_negotiation_type'] == 2 )
		        {
					if( ! Avia_WPML()->is_default_language() )
					{
						$wpml_home = function_exists( 'wpml_get_home_url_filter' ) ? wpml_get_home_url_filter() : icl_get_home_url();
		            	return str_replace( untrailingslashit( get_option( 'home' ) ), untrailingslashit( $wpml_home ), $url );
		    		}
		    	}
		    }
		    return $url;
		}
	}


	if( ! function_exists( 'avia_append_language_code_to_ajax_url' ) )
	{
		add_filter ( 'avia_ajax_url_filter', 'avia_append_language_code_to_ajax_url' );

		function avia_append_language_code_to_ajax_url( $url )
		{
			//conert url in case we are using different domain
			$url = avia_wpml_correct_domain_in_url( $url );

			//after converting the url in case it was necessary also append the language code
			$url .= '?lang=' . ICL_LANGUAGE_CODE;

			return $url;
		}
	}

	if( ! function_exists( 'avia_backend_language_switch' ) )
	{
	   add_filter( 'avia_options_page_header', 'avia_backend_language_switch' );

		function avia_backend_language_switch()
		{
		    $current_page = basename( $_SERVER['SCRIPT_NAME'] );
		    $query = '?';
			
            if( ! empty( $_SERVER['QUERY_STRING'] ) )
            {
                $query .= $_SERVER['QUERY_STRING'] . '&';
            }

			// icl_get_languages deprecated since 3.2
			$languages = function_exists( 'wpml_get_active_languages_filter' ) ? wpml_get_active_languages_filter( '', 'skip_missing=0&orderby=id' ) : icl_get_languages( 'skip_missing=0&orderby=id' );
			$output = '';

			if( is_array( $languages ) && ! empty( $languages ) )
			{
				$output .= "<ul class='avia_wpml_language_switch'>";
				$output .=		"<li><span class='avia_cur_lang_edit'>" . __( 'Editing:', 'avia_framework' ) . "</span><span class='avia_cur_lang'><img title='" . $languages[ ICL_LANGUAGE_CODE ]['native_name'] . "' alt='" . $languages[ ICL_LANGUAGE_CODE ]['native_name'] . "' src='" . $languages[ ICL_LANGUAGE_CODE ]['country_flag_url'] . "' />";
				$output .=		ICL_LANGUAGE_NAME_EN. " (" . __( 'Change', 'avia_framework' ) . ")</span>";
				
				unset( $languages[ ICL_LANGUAGE_CODE ] );
				
				$output .= "<ul class='avia_sublanguages'>";

				foreach( $languages as $lang )
				{
					$linkurl = esc_url( add_query_arg( 'lang', $lang['language_code'], admin_url( $current_page . $query ) ) );

					$output .= "<li class='language_" . $lang['language_code'] . "'>";
					$output .=		"<a href='" . $linkurl . "'>";
					$output .=			"<span class='language_flag'><img title='" . $lang['native_name'] . "' src='". esc_url( $lang['country_flag_url'] ) . "' alt='" . $lang['native_name'] . "' /></span>";
					$output .=			"<span class='language_native'>" . $lang['native_name'] . "</span>";
					$output .=		"</a>";
					$output .= '</li>';
				}

				$output .= '</ul>';
				$output .= '</li>';
				$output .= '</ul>';
				
				$output .="
				<style type='text/css'>
				.avia_wpml_language_switch {
                    z-index: 100;
                    padding: 10px;
                    position: absolute;
                    top: 13px;
                    left: 0;
                    margin:0;

                    }
				.avia_wpml_language_switch ul { display:none;
                    z-index: 100;
                    background-color: white;
                    position: absolute;
                    width: 128px;
                    padding: 57px 10px 10px;
                    left: -2px;
                    border: 1px solid #E1E1E1;
                    border-top: none;
                    margin-top: 0;
                    top:0;
                    }
				.avia_wpml_language_switch li:hover ul{display:block;}
				.avia_wpml_language_switch li a{text-decoration:none;}
				.avia_sublanguages li{
				margin:0;
				padding: 7px 0;
				border-top:1px solid #e1e1e1;
				}
				.avia_cur_lang, .avia_cur_lang_edit{ font-size:11px; padding:3px 0; z-index:300; position:relative; cursor:pointer; color: #5C951E; display:block;}
				.avia_cur_lang_edit{ color: #7D8388;}
				.avia_cur_lang img{margin:0px 4px -1px 0;}
				</style>
				";

			}

			return $output;
		}
	}

	if( ! function_exists( 'avia_change_wpml_home_link' ) )
	{
		add_filter( 'WPML_filter_link', 'avia_change_wpml_home_link', 10, 2 );
		
		function avia_change_wpml_home_link( $url, $lang )
		{
		    global $sitepress;
			
		    if( is_front_page() ) 
			{
				$new_url = $sitepress->language_url( $lang['language_code'] );
				
				/**
				 * @since 4.5.6.1
				 * @return string
				 */
				$url = apply_filters( 'avf_wpml_change_home_link', $new_url, $url, $lang );
			}
			
		    return $url;
		}
	}

	if( ! function_exists( 'avia_wpml_slideshow_slide_id_check' ) )
	{
		add_filter( 'avf_avia_builder_slideshow_filter', 'avia_wpml_slideshow_slide_id_check', 10, 2 );
		
		/**
		 * Change key of post array back to untranslated ID to be able to recognise selected image
		 * 
		 * @param array $slideshow_data
		 * @param mixed $context_object
		 * @return array
		 */
		function avia_wpml_slideshow_slide_id_check( array $slideshow_data, $context_object )
		{
		    $id_array = $slideshow_data['id_array'];
		    $slides = $slideshow_data['slides'];
		
		    if( empty( $id_array ) || empty( $slides ) ) 
			{
				return $slideshow_data;
			}
		
		    foreach( $id_array as $key => $id )
		    {
		        if( ! isset( $slides[ $id ] ) )
		        {
					//	icl_object_id deprecated since 3.2 - backward comp only
		            $id_of_translated_attachment = function_exists( 'wpml_object_id_filter' ) ? wpml_object_id_filter( $id, "attachment", true ) : icl_object_id( $id, "attachment", true );
		
		            if( $id_of_translated_attachment && isset( $slides[ $id_of_translated_attachment] ) )
		            {
		                $slides[ $id ] = $slides[ $id_of_translated_attachment ];
		                unset( $slides[ $id_of_translated_attachment ] );
		            }
		        }
		    }
		
		    $slideshow_data['slides'] = $slides;
		    return $slideshow_data;
		}
	}
	
	
	
	if(!function_exists('avia_wpml_author_name_translation'))
	{
		add_filter( 'avf_author_name', 'avia_wpml_author_name_translation', 10, 2);
		function avia_wpml_author_name_translation($name, $author_id)
		{
			if(function_exists('icl_t')) $name = icl_t('Authors', 'display_name_'.$author_id, $name);
			return $name;
		}
	}



	if(!function_exists('avia_wpml_author_nickname_translation'))
	{
		add_filter( 'avf_author_nickname', 'avia_wpml_author_nickname_translation', 10, 2);
		function avia_wpml_author_nickname_translation($name, $author_id)
		{
			if(function_exists('icl_t')) $name = icl_t('Authors', 'nickname_'.$author_id, $name);
			return $name;
		}
	}

	if( ! function_exists( 'avia_wpml_translate_date_format' ) )
	{
		function avia_wpml_translate_date_format( $format )
		{
			if( function_exists( 'icl_translate' ) ) 
			{
				$format = icl_translate( 'Formats', $format, $format );
			}
			
			return $format;
		}
	
		add_filter( 'option_date_format', 'avia_wpml_translate_date_format' );
	}
	
	
	if( ! function_exists( 'avia_wpml_translate_all_search_results_url' ) )
	{
		function avia_wpml_translate_all_search_results_url( $search_messages, $search_query )
		{
			//  icl_get_home_url was deprecaated since 3.2
			$wpml_home = function_exists( 'wpml_get_home_url_filter' ) ? wpml_get_home_url_filter() : icl_get_home_url();
			$search_messages['all_results_link'] = $wpml_home . '?' . $search_messages['all_results_query'];
			
			return $search_messages;
		}
	
		add_filter( 'avf_ajax_search_messages', 'avia_wpml_translate_all_search_results_url', 10, 2 );
	}
	
	
	if( ! function_exists( 'avia_translate_ids_from_query' ) )
	{
		/**
		 * Translate linkpicker id's
		 * 
		 * @param array $query
		 * @param array $params
		 * @return array
		 */
		function avia_translate_ids_from_query( $query, $params )
		{
			
			if( ! empty( $query['tax_query'][0]['terms'] ) && ! empty( $query['tax_query'][0]['taxonomy'] ) )
			{
				$query['tax_query'][0]['terms'] = avia_wpml_translate_object_ids( $query['tax_query'][0]['terms'], $query['tax_query'][0]['taxonomy'] );
			}
			else if( ! empty( $query['post__in'] ) && ! empty( $query['post_type'] ) )
			{
				$query['post__in'] = avia_wpml_translate_object_ids( $query['post__in'], $query['post_type'] );
			}
		
			return $query;
		}
		
		add_filter( 'avia_masonry_entries_query', 'avia_translate_ids_from_query', 10, 2 );
		add_filter( 'avia_post_grid_query', 'avia_translate_ids_from_query', 10, 2 );
		add_filter( 'avia_post_slide_query', 'avia_translate_ids_from_query', 10, 2 );
		add_filter( 'avia_blog_post_query', 'avia_translate_ids_from_query', 10, 2 );
	}
	
	if( ! function_exists( 'avia_translate_alb_linkpicker' ) )
	{
		/**
		 * Fix that WPML does not always translate the id's for objects in ALB elements with linkpickers.
		 * In case translation returns null we keep the original value as fallback.
		 * Some elements like masonry use filters above to translate.
		 * Set eg. $this->config['linkpickers']	= array( 'link' ); to activate this hook. 
		 * 
		 * @since 4.6.4
		 * @added_by Günter
		 * @param string $link_string
		 * @param string $link_id
		 * @param array $atts
		 * @param string $shortcode
		 * @param aviaShortcodeTemplate $shortcode_class
		 * @return string
		 */
		function avia_translate_alb_linkpicker( $link_string, $link_id = '', $atts = array(), $shortcode = '', $shortcode_class = null )
		{
			//	this is a fallback situation only
			if( ( strpos( $link_string, 'http://' ) === 0 ) || ( strpos( $link_string, 'https://' ) === 0 ) )
			{
				$link_string = 'manually,' . $link_string;
			}
			
			$link = explode( ',', $link_string, 2 );
			
			$taxonomy = isset( $link[0] ) ? $link[0] : '';
			
			switch( $taxonomy )
			{
				case '':
				case 'lightbox':
				case 'manually':
					return $link_string;
			}
			
			$terms = isset( $link[1] ) ? $link[1] : '';
			
			$translated = avia_wpml_translate_object_ids( $terms, $taxonomy );
			
			if( ! empty( $translated ) )
			{
				if( is_array( $translated ) )
				{
					$translated = implode( ',', $translated );
				}
				
				$link_string = $taxonomy . ',' . $translated;
			}
			
			return $link_string;
			
		}
		
		add_filter( 'avf_alb_linkpicker_value', 'avia_translate_alb_linkpicker', 10, 5 );
	}
	
	if( ! function_exists( 'avia_wpml_translate_attachment_id' ) )
	{
		/**
		 * Get current language attachment id. Returns original if no translation exists.
		 * 
		 * @since 4.8
		 * @param int $attachment_id
		 * @return int
		 */
		function avia_wpml_translate_attachment_id( $attachment_id )
		{
			return avia_wpml_translate_object_ids( $attachment_id, 'attachment' );
		}
		
		add_filter( 'avf_alb_attachment_id', 'avia_wpml_translate_attachment_id', 10, 1 );
	}
	
	if( ! function_exists( 'avia_wpml_translate_object_ids' ) )
	{
		/**
		 * Wrapper to allow WPML to translate the id's for objects in ALB elements
		 * In case translation returns null we keep the original value as fallback
		 * 
		 * @since 4.6.4
		 * @added_by Günter
		 * @param string|array $element_ids
		 * @param string $element_type
		 * @return string|array					returns same structure as rendered
		 */
		function avia_wpml_translate_object_ids( $element_ids, $element_type )
		{
			if( ! is_array( $element_ids ) )
			{
				$object = 'string';
				$ids = explode( ',', $element_ids );
			}
			else
			{
				$object = 'array';
				$ids = $element_ids;
			}
			
			$translated = array();
			
			foreach( $ids as $id )
			{
				//	icl_object_id deprecated since 3.2 - backward comp only
				$new = function_exists( 'wpml_object_id_filter' ) ?  wpml_object_id_filter( $id, $element_type, true ) : icl_object_id(  $id, $element_type, true );
				$translated[] = ! is_null( $new ) ? $new : $id;
			}
			
			if( ! empty( $translated ) )
			{
				$element_ids = ( 'string' == $object ) ? implode( ',', $translated ) : $translated;
			}
			
			return $element_ids;
		}
		
		add_filter( 'avf_alb_taxonomy_values', 'avia_wpml_translate_object_ids', 10, 2 );
	}
	
	
	if( ! function_exists( 'avia_translate_check_by_tag_values' ) )
	{
		/**
		 * Translate tag values for attachments (av-helper-masonry.php)
		 * 
		 * @since < 4.0
		 * @param array $value
		 * @return array
		 */
	    function avia_translate_check_by_tag_values( $value )
	    {
	        if( ! empty( $value ) && is_array( $value ) )
	        {
	            foreach( $value as $key => $data )
	            {
	                $orig_term = get_term_by( 'slug', $data, 'post_tag' );
	                if( false === $orig_term ) 
					{
						continue;
					}
						
					//	icl_object_id deprecated since 3.2 - backward comp only
					$translated_id = function_exists( 'wpml_object_id_filter' ) ? wpml_object_id_filter( $orig_term->term_id, 'post_tag', true ) : icl_object_id( $orig_term->term_id, 'post_tag', true );
					if( is_null( $translated_id ) || ( ! is_numeric( $translated_id ) ) )
					{
						continue;
					}
					
					if( $orig_term->term_id == $translated_id )
					{
						continue;
					}
					
					$translated_term = get_term_by( 'id', $translated_id, 'post_tag' );
					if( false === $translated_term ) 
					{
						continue;
					}
					
					$value[ $key ] = $translated_term->slug;	                
	            }
	        }
			
	        return $value;
	    }
	
	    add_filter( 'avf_ratio_check_by_tag_values', 'avia_translate_check_by_tag_values', 10, 1 );
	}
	
	
	if( ! function_exists( 'avia_wpml_get_special_pages_ids' ) )
	{
		add_filter( 'avf_get_special_pages_ids', 'avia_wpml_get_special_pages_ids', 20, 2 );

		/**
		 * Returns page id's that do not belong to normal page lists 
		 * like custom 404, custom maintainence mode page, custom footer page
		 * Returns an array of all languages
		 * 
		 * @since 4.5.1
		 * @param array $post_ids
		 * @param string $context
		 * @return array
		 */
		function avia_wpml_get_special_pages_ids( $post_ids = array(), $context = '' )
		{
			global $avia_config;

			/**
			 * Return anything except true to hide inactive special pages
			 * 
			 * @since 4.5.5
			 * @return boolean
			 */
			$show_inactive = apply_filters( 'avf_show_inactive_special_pages', true, $post_ids, $context ) === true ? true : false;
			
			$langs = $avia_config['wpml']['lang'];

			$maintenance_mode = Avia_WPML()->wpml_get_options( 'maintenance_mode' );
			$maintenance_page = Avia_WPML()->wpml_get_options( 'maintenance_page' );
			$error404_custom = Avia_WPML()->wpml_get_options( 'error404_custom' );
			$error404_page = Avia_WPML()->wpml_get_options( 'error404_page' );
			$display_widgets_socket = Avia_WPML()->wpml_get_options( 'display_widgets_socket' );
			$footer_page = Avia_WPML()->wpml_get_options( 'footer_page' );

			foreach( $langs as $lang_id => $lang ) 
			{	
						// Maintenance Page				
				$active = in_array( $maintenance_mode[ $lang_id ], array( 'maintenance_mode', 'maintenance_mode_redirect' ) );
				if( is_numeric( $maintenance_page[ $lang_id ] ) && ( (int) $maintenance_page[ $lang_id ] > 0 ) )
				{
					if( $active || $show_inactive )
					{	
						$post_ids[] = (int) $maintenance_page[ $lang_id ];
					}
				}

						// 404 Page
				$active = in_array( $error404_custom[ $lang_id ], array( 'error404_custom', 'error404_redirect' ) );
				if( is_numeric( $error404_page[ $lang_id ] ) && ( (int) $error404_page[ $lang_id ] > 0 ) )
				{
					if( $active || $show_inactive )
					{
						$post_ids[] = (int) $error404_page[ $lang_id ];
					}
				}		

						// Footer Page
				$active = in_array( $display_widgets_socket[ $lang_id ], array( 'page_in_footer', 'page_in_footer_socket' ) );
				if( is_numeric( $footer_page[ $lang_id ] ) && ( (int) $footer_page[ $lang_id ] > 0 ) )
				{
					if( $active || $show_inactive )
					{
						$post_ids[] = (int) $footer_page[ $lang_id ];
					}
				}
			}

			$post_ids = array_unique( $post_ids, SORT_NUMERIC );
			return $post_ids;
		}
	}

	if( ! function_exists( 'avia_wpml_alb_options_select_hierarchical_post_type_id' ) )
	{
		/**
		 * Fixes problem with links to posts/pages in ALB elements in modal popup in backend (reported and provided by WPML comp. team)
		 * 
		 * @since 4.5.7.2
		 * @param int $selected_id
		 * @param string $object_type			'page' | 'post' | 'category' | ......
		 * @param array $option_selected
		 * @param array $element
		 * @return int
		 */
		function avia_wpml_alb_options_select_hierarchical_post_type_id( $selected_id, $object_type, array $option_selected, array $element )
		{
			$selected_id = apply_filters( 'wpml_object_id', $selected_id, $option_selected[0], true ); 
			return $selected_id;
		}
		
		add_filter( 'avf_alb_options_select_hierarchical_post_type_id', 'avia_wpml_alb_options_select_hierarchical_post_type_id', 10, 4 );
	}

}

/*fix for: https://wpml.org/errata/translation-editor-support-avia-layout-builder-enfold/*/
/**
 * Removed with 4.2.6 by Günter
 * Replaced by function avia_wpml_sync_avia_layout_builder_meta below
 * 
 * The datastructire of $fields must have changed. On testing if was always false because $fields['body']['data'] does not exist any more
 */
if( ! function_exists( 'avia_wpml_sync_avia_layout_builder' ) )
{
	/**
	 * Ensure backwards comp - structure was checked with this version - might already have been changed earlier
	 */
	if ( defined( 'WPML_TM_VERSION' ) && version_compare( WPML_TM_VERSION, '2.5.2', '<' ) )
	{
		add_action( 'wpml_translation_job_saved', 'avia_wpml_sync_avia_layout_builder', 10, 3 );

		function avia_wpml_sync_avia_layout_builder( $new_post_id, $fields, $job ) 
		{
			if( isset( $fields['body']['data'] ) ) 
			{
				if ( 'active' === get_post_meta( $new_post_id, '_aviaLayoutBuilder_active', true ) ) 
				{
					update_post_meta(
						$new_post_id,
						'_aviaLayoutBuilderCleanData',
						$fields['body']['data']
					);
				}
			}
		}
	}
}

if( ! function_exists( 'avia_wpml_sync_avia_layout_builder_meta' ) )
{
	add_action( 'wpml_pro_translation_completed', 'avia_wpml_sync_avia_layout_builder_meta', 10, 3 );

	/**
	 * This filter is called when translation management is active and a post is translated with the WPML translation screen (not directly).
	 * In this case the save_post filter is not called and we have to update our meta fields here (esp. the shortcode tree)
	 * Post has been updated in DB already.
	 * 
	 * @since 4.2.6
	 * @added_by Günter
	 * @param int $new_post_id
	 * @param array $fields
	 * @param stdClass $job
	 */
	function avia_wpml_sync_avia_layout_builder_meta( $new_post_id, $fields, $job )
	{
		global $post;
		
		$translation = get_post( $new_post_id );

		if( ! $translation instanceof WP_Post )
		{
			return;
		}

		$builder_status = Avia_Builder()->get_alb_builder_status( $new_post_id );
		$loc = ( 'active' != $builder_status ) ? 'content' : 'clean_data';

		/**
		 * WPML only updates the post_content field
		 */
		$content = trim( $translation->post_content );
		
		/**
		 * Update our internal data
		 */
		Avia_Builder()->get_shortcode_parser()->set_builder_save_location( $loc );
		$content = ShortcodeHelper::clean_up_shortcode( $content, 'balance_only' );
		
		/**
		 * We do not update post content for the moment - currently id's are not used and as this is a translation id's should be set - but are not unique !!
		 */
		$id_content = Avia_Builder()->element_manager()->set_element_ids_in_content( $content, $new_post_id );
		
		if( 'active' == $builder_status )
		{
			Avia_Builder()->save_posts_alb_content( $new_post_id, $id_content );
		}
		
		/**
		 * Make sure we have the correct shortcode tree
		 */
		$tree = ShortcodeHelper::build_shortcode_tree( $content );
		Avia_Builder()->save_shortcode_tree( $new_post_id, $tree );
		
		/**
		 * Save an original post to allow logic of element manager (save post logic has $post set)
		 */
		$old_post = $post;
		$post = $translation;
		
		Avia_Builder()->element_manager()->updated_post_content( $id_content, $new_post_id );
		
		$post = $old_post;
	}
}


/*compatibility function for the portfolio problems*/
if(!function_exists('avia_portfolio_compat') && defined('ICL_SITEPRESS_VERSION') && defined('ICL_LANGUAGE_CODE'))
{
	add_action( 'avia_action_before_framework_init', 'avia_portfolio_compat', 30);
	function avia_portfolio_compat()
	{
		global $avia_config;
		if(empty($avia_config['wpml']['settings']['custom_posts_sync_option']) || empty($avia_config['wpml']['settings']['custom_posts_sync_option']['portfolio']))
		{
			$settings = get_option('icl_sitepress_settings');
			$settings['custom_posts_sync_option']['portfolio'] = 1;
			$settings['taxonomies_sync_option']['portfolio_entries'] = 1;
			update_option('icl_sitepress_settings', $settings);
		}
	}
}



/**
 * Error 404 - Custom Page
 * Hooks into 'the_posts' filter and display the defined 404 page - compatible with WPML
 * Removed by günter with 4.4.2 and replaced with wp_redirect to set 404 header code
 * 
 * Left in case we need a fallback - can be deleted in some future version
 * 
 * @author tinabillinger
 * @since 4.3
 */
//if( ! function_exists( 'av_error404_wpml' ) )
//{
//    function av_error404_wpml($posts)
//    {
//        if( defined('ICL_SITEPRESS_VERSION') && defined('ICL_LANGUAGE_CODE') && !is_admin()) {
//            if (avia_get_option('error404_custom') == "error404_custom") {
//                // prevent endless loop
//                remove_filter( 'the_posts', 'av_error404_wpml', 999 );
//                if ( empty( $posts ) &&
//                    is_main_query() &&
//                    !is_robots() &&
//                    !is_home() &&
//                    !is_feed() &&
//                    !is_search() &&
//                    !is_archive() &&
//                    ( !defined('DOING_AJAX') || !DOING_AJAX ) ) {
//                    global $wp_query;
//                    $error404_page = avia_get_option('error404_page');
//                    $wp_query = null;
//                    $wp_query = new WP_Query();
//                    $wp_query->query( 'page_id=' . $error404_page );
//                    $wp_query->the_post();
//                    $template = get_page_template();
//                    $posts = $wp_query->posts;
//                    $wp_query->rewind_posts();
//                    return $posts;
//                }
//            }
//        }
//        return $posts;
//    }
//    add_filter( 'the_posts', 'av_error404_wpml', 999 );
//}


if( ! function_exists( 'av_wpml_get_fb_language_code' ) )
{
	/**
	 * Return the current WPML facebook language code
	 * 
	 * @since 4.3.2
	 * @author Günter
	 * @param string $langcode
	 * @param string $source				'fb-page'
	 */
	function av_wpml_get_fb_language_code( $langcode, $source )
	{
		//	icl_object_id deprecated since 3.2 - backward comp only
		if( function_exists( 'wpml_object_id_filter' ) || function_exists( 'icl_object_id' ) ) 
		{
			$locale = ICL_LANGUAGE_NAME_EN;
			$fbxml = @simplexml_load_file( AVIA_BASE . '/config-wpml/FacebookLocales.xml' );

			if( is_object( $fbxml ) )
			{
				foreach( $fbxml as $loc ) 
				{
					if( $loc->englishName == $locale ) 
					{
						$langcode = $loc->codes->code->standard->representation;
						break;
					}
				}
			}
		}
		
		return $langcode;
	}
	
}

if( ! function_exists( 'av_wpml_breadcrumbs_get_parents' ) )
{
	add_filter( 'avf_breadcrumbs_get_parents', 'av_wpml_breadcrumbs_get_parents', 10, 1 );
	
	/**
	 * Allow to translate breadcrumb trail - fixes a problem with parent page for portfolio not being translated correctly
	 * https://kriesi.at/support/topic/parent-page-link-works-correct-but-translation-doesnt/
	 * https://wpml.org/forums/topic/enfold-theme-cant-copy-breadcrumb-hierarchy/#post-893784
	 * 		
	 * @since 4.5.1
	 * @param int $post_id
	 * @return int
	 */
	function av_wpml_breadcrumbs_get_parents( $post_id )
	{
		return apply_filters( 'wpml_object_id' , $post_id, 'page', true );
	}

}
