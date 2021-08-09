<?php
/**
 * Legacy functions - will be removed in future releases
 * 
 * 
 */

if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


//	deprecated filters
//add_action( 'init', 'avia_wpml_register_post_type_permalink', 20 );
//add_filter( 'icl_ls_languages', 'avia_wpml_url_filter' );
//add_action( 'avia_wpml_backend_language_switch', 'avia_default_dynamics' );
//add_filter( 'wp_nav_menu_items', 'avia_append_lang_flags', 9998, 2 );
//add_filter( 'avf_fallback_menu_items', 'avia_append_lang_flags', 9998, 2 );

/**
 * Deprecated functions with 4.8
 * =============================
 * 
 * Moved to avia_WPML
 */
if( ! function_exists( 'avia_wpml_options_language' ) )
{
	/**
	 * @deprecated since version 4.8
	 * @param array $base_data
	 */
	function avia_wpml_options_language( $base_data )
	{
		_deprecated_function( 'avia_wpml_options_language', '4.8', 'Avia_WPML()->handler_avf_options_languages()' );

		return Avia_WPML()->handler_avf_options_languages( $base_data );
	}
}

if( ! function_exists( 'avia_wpml_is_default_language' ) )
{
	/**
	 * @deprecated since version 4.8
	 * @return boolean
	 */
	function avia_wpml_is_default_language()
	{
		_deprecated_function( 'avia_wpml_is_default_language', '4.8', 'Avia_WPML()->is_default_language()' );

		return Avia_WPML()->is_default_language();
	}
}

if( ! function_exists( 'avia_wpml_get_languages' ) )
{
	/**
	 * @deprecated since version 4.8
	 */
	function avia_wpml_get_languages()
	{
		_deprecated_function( 'avia_wpml_get_languages', '4.8', 'Avia_WPML()->handler_ava_get_languages()' );

		Avia_WPML()->handler_ava_get_languages();
	}
}

if( ! function_exists( 'avia_wpml_get_options' ) )
{
	/**
	 * @deprecated since version 4.8
	 * @param string $option_key
	 * @return array
	 */
	function avia_wpml_get_options( $option_key )
	{
		_deprecated_function( 'avia_wpml_get_options', '4.8', 'Avia_WPML()->wpml_get_options()' );

		return Avia_WPML()->wpml_get_options( $option_key );
	}
}

if( ! function_exists( 'avia_wpml_register_assets' ) )
{
	/**
	 * @deprecated since version 4.8
	 */
	function avia_wpml_register_assets()
	{
		_deprecated_function( 'avia_wpml_register_assets', '4.8', 'Avia_WPML()->handler_wp_enqueue_scripts()' );

		Avia_WPML()->handler_wp_enqueue_scripts( $option_key );
	}
}

if( ! function_exists( 'avia_wpml_copy_options' ) )
{
	/**
	 * @deprecated since version 4.8
	 */
	function avia_wpml_copy_options()
	{
		_deprecated_function( 'avia_wpml_copy_options', '4.8', 'Avia_WPML()->handler_ava_copy_options()' );

		Avia_WPML()->handler_ava_copy_options();
	}
}

if( ! function_exists( 'avia_wpml_filter_dropdown_post_query' ) )
{
	function avia_wpml_filter_dropdown_post_query( $prepare_sql, $table_name, $limit, $element )
	{
		_deprecated_function( 'avia_wpml_filter_dropdown_post_query', '4.8', 'Avia_WPML()->handler_avf_dropdown_post_query()' );

		return Avia_WPML()->handler_avf_dropdown_post_query( $prepare_sql, $table_name, $limit, $element );
	}
}
        
/**
 * End deprecated functions with 4.8
 * =================================
 *
 */

/**
 * Deprecated functions with 4.8.2
 * ===============================
 * 
 * Moved to avia_WPML
 */
	
if( ! function_exists( 'avia_append_lang_flags' ) )
{
	function avia_append_lang_flags( $items, $args )
	{
		_deprecated_function( 'avia_append_lang_flags', '4.8.2', 'Avia_WPML()->handler_append_lang_flags()' );

		return Avia_WPML()->handler_append_lang_flags( $items, $args );
	}
}
    
/**
 * End deprecated functions with 4.8.2
 * ===================================
 *
 */
