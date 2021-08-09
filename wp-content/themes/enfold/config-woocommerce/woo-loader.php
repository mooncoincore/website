<?php
/**
 * Load correct version of file
 * 
 * @since 4.5.6
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && version_compare( WC()->version, '3.5.7', '>=' ) )
{
	require_once( 'config.php' );
	
	if( 'block' == AviaGutenberg()->selected_editor() )
	{
		require_once( 'class-avia-wc-block-editor.php' );
	}
}
else
{
	require_once( 'config-356.php' );
}
