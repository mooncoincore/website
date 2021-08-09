<?php
/**
 * Demo Import Tab
 * ===============
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;

		


$avia_elements[] = array(	
						'slug'			=> 'demo',
						'name'			=> __( 'Download And Import Demo Files', 'avia_framework' ),
						'desc'			=> __( 'If you are new to wordpress or have problems creating posts or pages that look like the Theme Demo you can download the demos you like from our server and import dummy posts and pages that will definitely help to understand how those tasks are done.', 'avia_framework' ) . '<br /><br />' .
										   __( 'We recommend to use a clean WP installation for importing a demo to avoid conflicts with existing content.', 'avia_framework' ) .
										   '<br/><br/><strong class="av-text-notice">' .
										   __( 'Notice: If you want to completely remove a demo installation after importing it, you can use a plugin like', 'avia_framework' ) . ' <a target="_blank" href="https://wordpress.org/plugins/wordpress-reset/">WordPress Reset</a></strong>',
						'id'			=> 'demoimportdescription',
						'std'			=> '',
						'type'			=> 'heading',
						'nodescription'	=> true
					);	


include( AVIA_BASE . 'includes/admin/register-demo-import.php' );



