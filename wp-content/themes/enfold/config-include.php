<?php
/* 
 * This helper file checks for active plugins and disables include of config files
 * 
 * @since 4.5.7.1
 * @added_by Günter
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( 'bbPress' ) )
{
	add_theme_support( 'avia_exclude_bbPress' );
}

if ( ! class_exists( 'Tribe__Events__Main' ) )
{
	add_theme_support( 'deactivate_tribe_events_calendar' );
}

if( ! class_exists( 'GFForms' ) )
{
	add_theme_support( 'avia_exclude_GFForms' );
}

if( ! class_exists( 'ZenOfWPMenuLogic' ) && ! class_exists( 'Themify_Conditional_Menus' ) )
{
	add_theme_support( 'avia_exclude_menu_exchange' );
}

if( ! isset( $relevanssi_variables ) || ! isset( $relevanssi_variables['file'] ) )
{
	add_theme_support( 'avia_exclude_relevanssi' );
}

if ( ! class_exists( 'WooCommerce' ) )
{
	add_theme_support( 'avia_exclude_WooCommerce' );
}

if( ! class_exists( 'wpSEO' ) && ! defined( 'WPSEO_VERSION' ) )
{
	add_theme_support( 'avia_exclude_wpSEO' );
}

if( ! ( defined( 'ICL_SITEPRESS_VERSION' ) && defined( 'ICL_LANGUAGE_CODE' ) ) )
{
	add_theme_support( 'avia_exclude_wpml' );
}

if ( ! class_exists( 'SB_Instagram_Feed' ) )
{
	add_theme_support( 'avia_exclude_instagram_feed' );
}

if ( ! class_exists( 'Leaflet_Map' ) )
{
	add_theme_support( 'avia_exclude_leaflet_map' );
}

/**
 * 
 * @since 4.5.7.1
 */
do_action( 'ava_deactivate_enfold_plugin_addons' );