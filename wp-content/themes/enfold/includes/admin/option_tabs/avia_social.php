<?php
/**
 * Social Profiles Tab
 * ===================
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;


$avia_elements[] = array(	
			'slug'			=> 'social',
			'name'			=> __( 'Your social profiles', 'avia_framework' ),
			'desc'			=> __( 'You can enter links to your social profiles here. Afterwards you can choose where to display them by activating them in the respective area', 'avia_framework' ) .' ( '. __( 'e.g:', 'avia_framework' ) . " <a href='#goto_layout'>". __( 'General Layout', 'avia_framework' ) . "</a>, <a href='#goto_header'>". __( 'Header', 'avia_framework' ) . "</a>, <a href='#goto_footer'>". __( 'Footer', 'avia_framework' ) . '</a> )',
			'id'			=> 'socialdescription',
			'type'			=> 'heading',
			'std'			=> '',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'			=> 'social',
			'id' 			=> 'social_icons',
			'type' 			=> 'group',
			'linktext' 		=> '+',
			'deletetext' 	=> '×',
			'blank' 		=> true,
			'nodescription' => true,
			'std'			=> array(
									array( 'social_icon' => 'twitter', 'social_icon_link' => 'https://twitter.com/kriesi' ),
									array( 'social_icon' => 'dribbble', 'social_icon_link' => 'https://dribbble.com/kriesi' ),
								),
			'subelements' 	=> array(

									array(
										'slug'		=> 'social',
										'name'		=> __( 'Social Icon', 'avia_framework' ),
										'desc'		=> '',
										'id'		=> 'social_icon',
										'type'		=> 'select',
										'std'		=> 'twitter',
										'class'		=> 'av_2columns av_col_1',
										'subtype'	=> $avia_config['social_icon_array']
									),

									array(
										'slug'		=> 'social',
										'name'		=> __( 'Social Icon URL:', 'avia_framework' ),
										'desc'		=> '',
										'id'		=> 'social_icon_link',
										'type'		=> 'text',
										'std'		=> '',
										'class'		=> 'av_2columns av_col_2' ),
									)
								);


