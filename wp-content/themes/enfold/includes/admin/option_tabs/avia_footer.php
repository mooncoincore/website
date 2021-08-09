<?php
/**
 * Footer Tab
 * ==========
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;




$avia_elements[] = array(
			'slug'		=> 'footer',
			'name'		=> __( 'Default Footer &amp; Socket Settings', 'avia_framework' ),
			'desc'		=> __( 'Do you want to display the footer widgets &amp; footer socket or a page content as footer? This default setting can be changed individually for each page.', 'avia_framework' ),
			'id'		=> 'display_widgets_socket',
			'type'		=> 'select',
			'std'		=> 'all',
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'Widget based footer options', 'avia_framework' ) => array(
										__( 'Display the footer widgets & socket', 'avia_framework' )			=> 'all',
										__( 'Display only the footer widgets (no socket)', 'avia_framework' )	=> 'nosocket',
										__( 'Display only the socket (no footer widgets)', 'avia_framework' )	=> 'nofooterwidgets',
										__( "Don't display the socket & footer widgets", 'avia_framework' )		=> 'nofooterarea',
								),

								__( 'Page based Footer options', 'avia_framework' ) => array(
										__( 'Select a page to display as footer and socket','avia_framework' )	=> 'page_in_footer_socket',
										__( 'Select a page to display as footer (no socket)','avia_framework' )	=> 'page_in_footer',
								)
							)
		);

$avia_elements[] = array(
			'slug'			=> 'footer',
			'name'			=> __( 'Select page', 'avia_framework' ),
			'desc'			=> __( 'Select a page to display the content of this page in the footer area. You may also use pages created with the advanced layout builder.', 'avia_framework' ),
			'id'			=> 'footer_page',
			'type'			=> 'select',
			'subtype'		=> 'page',
			'std'			=> '',
			'with_first'	=> true,
			'required'		=> array( 'display_widgets_socket', '{contains_array}page_in_footer_socket;page_in_footer' ),
			'class'			=> 'avia-style',
		);


$avia_elements[] = array(
			'slug'		=> 'footer',
			'name'		=> __( 'Footer Columns', 'avia_framework' ),
			'desc'		=> __( 'How many columns should be displayed in your footer', 'avia_framework' ),
			'id'		=> 'footer_columns',
			'type'		=> 'select',
			'std'		=> '4',
			'required'	=> array( 'display_widgets_socket', '{contains_array}all;nosocket' ),
			'subtype'	=> array(
								__( '1', 'avia_framework' ) => '1',
								__( '2', 'avia_framework' ) => '2',
								__( '3', 'avia_framework' ) => '3',
								__( '4', 'avia_framework' ) => '4',
								__( '5', 'avia_framework' ) => '5' 
							)
		);

$avia_elements[] = array(
			'slug'		=> 'footer',
			'name'		=> __( 'Copyright', 'avia_framework' ),
			'desc'		=> __( 'Add a custom copyright text at the bottom of your site. eg:', 'avia_framework' ) . '<br/><strong>&copy; ' . __( 'Copyright','avia_framework' ) . '  - ' . get_bloginfo( 'name' ) . '</strong>',
			'id'		=> 'copyright',
			'type'		=> 'text',
			'std'		=> '',
			'required'	=> array( 'display_widgets_socket', '{contains_array}all;nofooterwidgets;page_in_footer_socket' ),
		);


$avia_elements[] = array(
			'slug'		=> 'footer',
			'name'		=> __( 'Social Icons', 'avia_framework' ),
			'desc'		=> __( 'Check to display the social icons defined in', 'avia_framework' ) .
								" <a href='#goto_social'>" .
								__( 'Social Profiles', 'avia_framework' ) .
								'</a> ' .
								 __( 'in your socket', 'avia_framework' ),
			'id'		=> 'footer_social',
			'required'	=> array( 'display_widgets_socket', '{contains_array}all;nofooterwidgets;page_in_footer_socket' ),
			'type'		=> 'checkbox',
			'std'		=> ''
		);


