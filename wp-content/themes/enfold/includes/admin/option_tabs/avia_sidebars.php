<?php
/**
 * Sidebar Settings Tab
 * ====================
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;



$avia_elements[] = array(
			'slug'		=> 'sidebars',
			'name'		=> __( 'Sidebar on Archive Pages', 'avia_framework' ),
			'desc'		=> __( 'Choose the archive sidebar position here. This setting will be applied to all archive pages', 'avia_framework' ),
			'id'		=> 'archive_layout',
			'type'		=> 'select',
			'std'		=> 'sidebar_right',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'left sidebar', 'avia_framework' )	=> 'sidebar_left',
								__( 'right sidebar', 'avia_framework' )	=> 'sidebar_right',
								__( 'no sidebar', 'avia_framework' )	=> 'fullsize'
							)
		);

$avia_elements[] = array(
			'slug'		=> 'sidebars',
			'name'		=> __( 'Sidebar on Blog Page', 'avia_framework' ),
			'desc'		=> __( 'Choose the blog sidebar position here. This setting will be applied to the blog page', 'avia_framework' ),
			'id'		=> 'blog_layout',
			'type'		=> 'select',
			'std'		=> 'sidebar_right',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'left sidebar', 'avia_framework' )	=> 'sidebar_left',
								__( 'right sidebar', 'avia_framework' )	=> 'sidebar_right',
								__( 'no sidebar', 'avia_framework' )	=> 'fullsize'
							)
		);

$avia_elements[] = array(
			'slug'		=> 'sidebars',
			'name'		=> __( 'Sidebar on Single Post Entries', 'avia_framework' ),
			'desc'		=> __( 'Choose the blog post sidebar position here. This setting will be applied to single blog posts', 'avia_framework' ),
			'id'		=> 'single_layout',
			'type'		=> 'select',
			'std'		=> 'sidebar_right',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'left sidebar', 'avia_framework' )	=> 'sidebar_left',
								__( 'right sidebar', 'avia_framework' )	=> 'sidebar_right',
								__( 'no sidebar', 'avia_framework' )	=> 'fullsize'
							)
		);


$avia_elements[] = array(
			'slug'		=> 'sidebars',
			'name'		=> __( 'Sidebar on Pages', 'avia_framework' ),
			'desc'		=> __( 'Choose the default page layout here. You can change the setting of each individual page when editing that page', 'avia_framework' ),
			'id'		=> 'page_layout',
			'type'		=> 'select',
			'std'		=> 'sidebar_right',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'left sidebar', 'avia_framework' )	=> 'sidebar_left',
								__( 'right sidebar', 'avia_framework' )	=> 'sidebar_right',
								__( 'no sidebar', 'avia_framework' )	=> 'fullsize'
							)
		);

$avia_elements[] = array(
			'slug'		=> 'sidebars',
			'name'		=> __( 'Separate Sidebars for Blog and Archive Pages', 'avia_framework' ),
			'desc'		=> __( "Show separate 'Archive Sidebar' on Archive Pages", 'avia_framework' ),
			'id'		=> 'archive_sidebar',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Show Blog Sidebar on Archive Pages', 'avia_framework' )				=> '',
								__( 'Show separate Archive Sidebar on Archive Pages', 'avia_framework' )	=> 'archive_sidebar_separate'
							)
		);

$avia_elements[] = array(
			'slug'		=> 'sidebars',
			'name'		=> __( 'Sidebar on Smartphones', 'avia_framework' ),
			'desc'		=> __( 'Show sidebar on smartphones (Sidebar is displayed then below the actual content)', 'avia_framework' ),
			'id'		=> 'smartphones_sidebar',
			'type'		=> 'checkbox',
			'std'		=> '',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Hide sidebar on smartphones', 'avia_framework' )	=> '',
								__( 'Show sidebar on smartphones', 'avia_framework' )	=> 'smartphones_sidebar_visible'
							)
		);

$avia_elements[] = array(
			'slug'		=> 'sidebars',
			'name'		=> __( 'Page Sidebar navigation', 'avia_framework' ),
			'desc'		=> __( 'Display a sidebar navigation for all nested subpages of a page automatically?', 'avia_framework' ),
			'id'		=> 'page_nesting_nav',
			'type'		=> 'checkbox',
			'std'		=> 'true',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Display sidebar navigation', 'avia_framework' )		=> 'true',
								__("Don't display Sidebar navigation", 'avia_framework' )	=> ''
							)
	);

$avia_elements[] = array(
			'slug'		=> 'sidebars',
			'name'		=> __( 'Sidebar Separator Styling', 'avia_framework' ),
			'desc'		=> __( 'Do you want to separate the sidebar from your main content with a border?', 'avia_framework' ),
			'id'		=> 'sidebar_styling',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'With Border', 'avia_framework' )	=> '',
								__( 'No Border', 'avia_framework' )		=> 'no_sidebar_border',
								__( 'Shadow', 'avia_framework' )		=> 'sidebar_shadow',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'sidebars',
			'name'		=> __( 'Left Sidebar Text Alignment', 'avia_framework' ),
			'desc'		=> __( 'Define text alignment for the left sidebar', 'avia_framework' ),
			'id'		=> 'sidebar_left_textalign',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'Right', 'avia_framework' )	=> '',
								__( 'Left', 'avia_framework' )	=> 'align_left',
							)
		);


$avia_elements[] = array(	
			'slug'			=> 'sidebars',
			'name'			=> __( 'Create new Sidebar Widget Areas', 'avia_framework' ),
			'desc'			=> __( 'The theme supports the creation of custom widget areas. Simply open your', 'avia_framework' ) . " <a target='_blank' href='" . admin_url( 'widgets.php' ) . "'>" . __( 'Widgets Page', 'avia_framework' ) . '</a> '.
									__( 'and add a new Sidebar Area. Afterwards you can choose to display this Widget Area in the Edit Page Screen.', 'avia_framework' ),
			'id'			=> 'sidebars_widgetdescription',
			'type'			=> 'heading',
			'std'			=> '',
			'nodescription'	=> true
			);

