<?php
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

/**
 * Handles option page definition for import of demos
 *
 * IMPORTANT:
 * ==========
 *
 * Enable WP_DEBUG to get complete error messages during import in debug.log file
 * (Always make sure to disable not needed plugins when testing import of a demo during development - otherwise import might break for user !! )
 *
 *
 * @added_by Günter
 * @since 4.8.2
 * @since 4.8.3			demo file management by plugin "Download Manager" and https://kriesi.at/themes/demo-downloads/
 *
 *
 * DEMO Files:
 * ===========
 *
 *
 * Directory atructure on user server:
 *
 * Downloaded demos are placed in
 *
 *		- .../uploads/avia_demo_files/$demo_name
 *
 *
 * User must select which demos he wants to download.
 * @since 4.8.2:
 *		- a one click import was added: Download File - Import - Delete downloaded files
 *		- User can swith to 3 step import with   add_theme_support( 'avia_demo_store_downloaded_files' );
 *
 * --------
 * Array elements:
 *
 *	Shipped demos:
 *
 *		'image'		Link to preview image (if empty only a button is displayed)  e.g.   'includes/admin/demo_files/demo_images/enfold-2017.jpg'
 *		'files'		Link to files without file extension e.g.  '/includes/admin/demo_files/enfold-2017'
 *
 *	External demos (since 4.8.2, changed 4.8.3 ):
 *
 *		'demo_name'			Name of folder and files in folder e.g.    'enfold-2017'
 *		'download'			ID of download manager to find download
 *		'demo_img'			URL to preview image, image name MUST be same as demo_name) e.g.   'enfold-2017.jpg'
 *
 */


global $avia_config;

/**
 * Define global variables to deal with demo import and the demo files
 *
 * @since 4.8.2
 */
if( ! isset( $avia_config['demo_import'] ) )
{
//	$avia_config['demo_import']['download_server_url'] = 'https://kriesi.at/enfold_demos/';
	$avia_config['demo_import']['download_manager_url'] = 'https://kriesi.at/themes/demo-downloads/download/';


	$avia_config['demo_import']['local_image_url'] = trailingslashit( get_template_directory_uri() ) . 'includes/admin/demo_files/demo_images/';
	$avia_config['demo_import']['local_image_path'] = trailingslashit( get_template_directory() ) . 'includes/admin/demo_files/demo_images/';
	$avia_config['demo_import']['local_image_path'] = str_replace( '\\' , '/', $avia_config['demo_import']['local_image_path'] );

	$avia_config['demo_import']['upload_folders'] = wp_upload_dir();
	$avia_config['demo_import']['upload_folders']['basedir'] = str_replace( '\\' , '/', $avia_config['demo_import']['upload_folders']['basedir'] );
	if( is_ssl() )
	{
		$avia_config['demo_import']['local_image_url'] = str_replace( 'http://', 'https://', $avia_config['demo_import']['local_image_url'] );
		$avia_config['demo_import']['upload_folders']['baseurl'] = str_replace( 'http://', 'https://', $avia_config['demo_import']['upload_folders']['baseurl'] );
	}

	$folder = apply_filters( 'avf_demo_import_folder_name', 'avia_demo_files' );

	$avia_config['demo_import']['upload_folders']['main_dir'] = trailingslashit( trailingslashit( $avia_config['demo_import']['upload_folders']['basedir'] ) . $folder );
	$avia_config['demo_import']['upload_folders']['main_url'] = trailingslashit( trailingslashit( $avia_config['demo_import']['upload_folders']['baseurl'] ) . $folder );
}

/**
 *
 * @since 4.8.2
 * @param array
 * @return array
 */
$avia_config['demo_import'] = apply_filters( 'avf_demo_import_settings', $avia_config['demo_import'] );


/**
 * Define the demos
 */

$what_get 		= __( 'What you get:', 'avia_framework' );
$online_demo 	= __( 'Online Demo', 'avia_framework' );
$demo_id		= 0;

/**
 * Shipped Demos
 * =============
 */
$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Default Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a> " . __( '(for shop functionality)', 'avia_framework' ) . '</li>'
									."<li><a href='https://wordpress.org/plugins/bbpress/' target='_blank'>BBPress</a> " . __( '(for forum functionality)', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'A few', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'image'		=> 'includes/admin/demo_files/demo_images/default.jpg',
					);

/**
 * External Server Demos
 * =====================
 */
$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Enfold 2017', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-2017/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a> " . __( '(for shop functionality)', 'avia_framework' ) . '</li>'
									."<li><a href='https://wordpress.org/plugins/bbpress/' target='_blank'>BBPress</a> " . __( '(for forum functionality)', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'enfold-2017',
					'download'	=> 180,
					'demo_img'	=> 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/enfold-2017.jpg',
				);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Small Business - Flat Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-business-flat/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'business-flat',
					'download'	=> 131,
					'demo_img'	=> 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/business-flat.jpg'
				);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Startup Business Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-startup/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'startup',
					'download'	=> 300,
					'demo_img'	=> 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/startup.jpg'
				);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: One Page Portfolio Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-one-page-portfolio/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'one-page-portfolio',
					'download'	=> 249,
					'demo_img'	=> 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/one-page-portfolio.jpg'
				);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Minimal Portfolio Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-minimal-portfolio/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'minimal-portfolio',
					'download'  => 239,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/minimal-portfolio.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Elegant Portfolio Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-elegant-portfolio/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'elegant-portfolio',
					'download'  => 175,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/elegant-portfolio.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Photography Portfolio Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-photography/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a> " . __("(if you want to sell photos online)", 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'photography',
					'download'  => 270,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/photography.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Minimal Photography Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-minimal-photography/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'minimal-photography',
					'download'  => 234,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/minimal-photography.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Dark Photography Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-dark-photography/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'dark-photography',
					'download'  => 161,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/dark-photography.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Creative Studio Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-creative-studio/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'creative-studio',
					'download'  => 156,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/creative-studio.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: One Page Agency Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-one-page-agency/' target='_blank'>{$online_demo}</a></strong></p>"
								."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
								.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
								.'</ul>'
								."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
								.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
								.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'one-page-agency',
					'download'  => 244,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/one-page-agency.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Medical Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-medical/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'medical',
					'download'  => 229,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/medical.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Shop Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-shop/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Required Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a> " . __( '(needs to be active to install the demo)', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'exists'	=> array( 'WooCommerce' => __( 'The WooCommerce Plugin is currently not active. Please install and activate it, then reload this page in order to be able to import this demo', 'avia_framework' ) ),
					'demo_name'	=> 'shop',
					'download'  => 285,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/shop.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Restaurant Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-restaurant/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a> " . __("(if you want to provide online ordering and delivery)", 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'restaurant',
					'download'  => 275,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/restaurant.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: One Page Restaurant Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-restaurant-one-page/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a> " . __("(if you want to provide online ordering and delivery)", 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'one-page-restaurant',
					'download'  => 257,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/one-page-restaurant.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: One Page Wedding Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-wedding/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'one-page-wedding',
					'download'  => 262,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/one-page-wedding.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Construction Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-construction/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'construction',
					'download'  => 146,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/construction.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Church Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-church/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Required Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='https://wordpress.org/plugins/the-events-calendar/' target='_blank'>The Events Calendar</a> "
									.__( '(needs to be active to install the demo)', 'avia_framework' ) . '</li>'
									."<li>or <a href='http://mbsy.co/6cr37' target='_blank'>The Events Calendar PRO</a></li>"
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'exists'	=> array( 'Tribe__Events__Main' => __( 'The Events Calendar Plugin is currently not active. Please install and activate it, then reload this page in order to be able to import this demo', 'avia_framework' ) ),
					'demo_name'	=> 'church',
					'download'  => 136,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/church.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Simple Blog Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-blog/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'simple-blog',
					'download'  => 269,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/simple-blog.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Lifestyle Blog Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-lifestyle-blog/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'lifestyle-blog',
					'download'  => 225,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/lifestyle-blog.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: \'Coming Soon\' Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-coming-soon/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'coming-soon',
					'download'  => 141,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/coming-soon.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: \'Landing Page\' Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-landing-page/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'landing-page',
					'download'  => 215,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/landing-page.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Travel Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-travel/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __("Required Plugins:", 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a> " . __( '(needs to be active to install the demo)', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='https://woocommerce.com/products/woocommerce-bookings/?ref=84' target='_blank'>WooCommerce Bookings</a> " . __( '(needs to be active to allow date based bookings)', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'exists'	=> array( 'WooCommerce' => __( 'The WooCommerce Plugin is currently not active. Please install and activate it, then reload this page in order to be able to import this demo', 'avia_framework' ) ),
					'demo_name'	=> 'travel',
					'download'  => 305,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/travel.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Hotel Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-hotel/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Required Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a> " . __( '(needs to be active to install the demo)', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='https://woocommerce.com/products/woocommerce-bookings/?ref=84' target='_blank'>WooCommerce Bookings</a> " . __( '(needs to be active to allow date based bookings)', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'exists'	=> array( 'WooCommerce' => __( 'The WooCommerce Plugin is currently not active. Please install and activate it, then reload this page in order to be able to import this demo', 'avia_framework' ) ),
					'demo_name'	=> 'hotel',
					'download'  => 205,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/hotel.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Spa Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-spa/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a></li>"
									."<li><a href='https://woocommerce.com/products/woocommerce-bookings/?ref=84' target='_blank'>WooCommerce Bookings</a> " . __("(needs to be active to allow date based bookings)", 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'spa',
					'download'  => 295,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/spa.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Law Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-law/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'law',
					'download'  => 220,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/law.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Consulting Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-consulting/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'consulting',
					'download'  => 151,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/consulting.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Résumé Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-resume/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'resume',
					'download'  => 280,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/resume.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: GYM Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-gym/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'gym',
					'download'  => 195,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/gym.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Health Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-health-coach/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'health',
					'download'  => 200,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/health.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: App Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-app/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'app',
					'download'  => 120,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/app.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Gaming Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-gaming/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'gaming',
					'download'  => 190,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/gaming.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: DJ Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-dj/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'dj',
					'download'  => 170,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/dj.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Band Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-band/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Required Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='http://woocommerce.com/?ref=84' target='_blank'>WooCommerce</a></li>" . __( '(needs to be active to install the demo)', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'exists'	=> array( 'WooCommerce' => __( 'The WooCommerce Plugin is currently not active. Please install and activate it, then reload this page in order to be able to import this demo', 'avia_framework' ) ),
					'demo_name'	=> 'band',
					'download'  => 125,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/band.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Freelancer Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-freelancer/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'freelancer',
					'download'  => 185,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/freelancer.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Visual Artist Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-visual-artist/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Recommended Plugins:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'None', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'demo_name'	=> 'visual-artist',
					'download'  => 310,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/visual-artist.jpg'
					);


$avia_elements[] =	array(
					'slug'		=> 'demo',
					'name'		=> __( 'Import: Knowledgebase Demo', 'avia_framework' ),
					'desc'		=> 	 "<p><strong>{$what_get} <a href='https://kriesi.at/themes/enfold-knowledgebase-demo/' target='_blank'>{$online_demo}</a></strong></p>"
									."<h4 class='av-before-plugins'>" . __( 'Required Plugins:', 'avia_framework' ) . '</h4><ul>'
									."<li><a href='https://wordpress.org/plugins/bbpress/' target='_blank'>BBPress</a> " . __( '(needs to be active to install the demo)', 'avia_framework' ) . '</li>'
									.'</ul>'
									."<h4 class='av-before-plugins'>" . __( 'Demo Images included:', 'avia_framework' ) . '</h4><ul>'
									.'<li>' . __( 'All', 'avia_framework' ) . '</li>'
									.'</ul>',
					'id'		=> 'import' . ++$demo_id,
					'type'		=> 'import',
					'exists'	=> array( 'bbPress' => __( 'The bbPress Plugin is currently not active. Please install and activate it, then reload this page in order to be able to import this demo', 'avia_framework' ) ),
					'demo_name'	=> 'knowledgebase',
					'download'  => 210,
					'demo_img'  => 'https://kriesi.at/themes/demo-downloads/wp-content/uploads/sites/85/2021/04/knowledgebase.jpg'
					);
