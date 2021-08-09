<?php
/**
 * Theme Extension Main Tab Content
 * ================================
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;

/**
 * Theme Extensions Tab
 * ====================
 */

$avia_elements[] = array(	
			'slug'		=> 'avia_ext',
			'name'		=> __( 'Theme Extensions Options', 'avia_framework' ),
			'desc'		=> __( 'The options on this page allow you to activate and use extended features of the theme.', 'avia_framework' ),
			'id'		=> 'avia_ext_intro_heading',
			'type'		=> 'heading',
			'nodescription' => true
		);



