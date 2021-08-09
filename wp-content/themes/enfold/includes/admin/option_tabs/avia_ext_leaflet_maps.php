<?php
/**
 * Leaflet Open Street Maps Tab
 * ============================
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;


$desc  = __( 'OpenStreetMaps and Leaflet are open source projects that allow to display mobile friendly maps without using a key or account.', 'avia_framework' ) . '<br /><br />';
$desc .= __( 'To be independent from any external hoster we bundle the necessary basic Leaflet js library with the theme and store it on your server.', 'avia_framework' ) . ' ';
$desc .= __( 'Check <a href="https://leafletjs.com/index.html" target="_blank" rel="noopener noreferrer">Leaflet Javascript Library Homepage</a> for more information about this library.', 'avia_framework' ) . ' ';
$desc .= __( 'Only requests to the OpenStreetMaps databases are passed to external servers.', 'avia_framework' ) . ' ';
$desc .= __( 'Here you find information about <a href="https://wiki.osmfoundation.org/wiki/Privacy_Policy" target="_blank" rel="noopener noreferrer">OpenStreetMap Privacy Policy</a>.', 'avia_framework' ) . ' ';
$desc .= __( 'It is even possible to <a href="https://openmaptiles.org/" target="_blank" rel="noopener noreferrer">host your own OpenStreetMap Tiles</a>.', 'avia_framework' );



$avia_elements[] = array(	
			'slug'		=> 'leaflet_maps',
			'name'		=> __( 'OpenStreetMaps (OSM) And Leaflet', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'Leaflet_maps_header',
			'type'		=> 'heading',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'		=> 'leaflet_maps',
			'name'		=> __( 'OpenStreetMaps and Leaflet', 'avia_framework' ),
			'desc'		=> __( 'Activate if you want to use this feature.', 'avia_framework' ),
			'id'		=> 'leaflet_maps_enable_feature',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'subtype'	=> array( 
								__( 'Disabled', 'avia_framework' )	=> '',
								__( 'Enable', 'avia_framework' )	=> 'enabled',
							)
		);


$avia_elements[] = array(	
			'slug'			=> 'leaflet_maps', 
			'type'			=> 'visual_group_start',
			'id'			=> 'Leaflet_maps_container_start', 
			'required'		=> array( 'leaflet_maps_enable_feature', '{contains_array}enabled' ),
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'		=> 'leaflet_maps',
			'name'		=> __( 'Leaflet Map Plugin', 'avia_framework' ),
			'desc'		=> __( 'This free plugin provides the necessary interface to interact with the Leaflet javascript library and OSM. You can download and activate the plugin here.', 'avia_framework' ),
			'id'		=> 'leaflet_maps_plugin_check',
			'type'		=> 'plugin_check',
			'nodescription'	=> true,
			'no_found'	=> __( 'We were not able to detect this plugin.', 'avia_framework' ),
			'found'		=> sprintf( __( 'We were able to detect this plugin. Nothing left to do here. <a href="%s" rel="noopener noreferrer">Plugin Settings</a>. Please keep your plugin up to date.', 'avia_framework' ), admin_url( 'admin.php?page=leaflet-map' ) ),
			'plugins'	=> array(
						'Leaflet Map' => array(
										'download'	=> 'leaflet-map', 					
										'file'		=> 'leaflet-map/leaflet-map.php', 
							)
						)
		);

$desc  = __( 'We do not recommend to change any plugin settings unless you know what you are doing. All supported settings can be changed per map.', 'avia_framework' ) . '<br /><br />';
$desc .= __( 'As already mentioned above we bundle the necessary Leaflet Javascript Library files to access OSM and load them from your server. You can change this here, if you want to load the files from another location or have problems because plugin was updated and our theme contains an incompatible version of the library:', 'avia_framework' ) . ' ';
$desc .= '<a href="https://leafletjs.com/download.html" target="_blank" rel="noopener noreferrer">' . __( 'Download latest library files here', 'avia_framework' ) . '</a>. ';
$desc .= __( 'Theme default location is:  .../themes/enfold/config-leaflet-maps/assets/leafletjs', 'avia_framework' ). '<br /><br />';

$avia_elements[] = array(	
			'slug'		=> 'leaflet_maps',
			'name'		=> __( 'Change Leaflet Library Files Location', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'leaflet_maps_settings_header',
			'type'		=> 'heading',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'		=> 'leaflet_maps',
			'name'		=> __( 'Leaflet Javascript Library js File', 'avia_framework' ),
			'desc'		=> __( 'Select the location for the loaded javascript file', 'avia_framework' ),
			'id'		=> 'leaflet_maps_js_file',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'subtype'	=> array( 
								__( 'Use theme bundled library', 'avia_framework' )					=> '',
								__( 'Use plugin default library', 'avia_framework' )				=> 'default',
								__( 'Use manual added URL in settings page', 'avia_framework' )		=> 'custom',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'leaflet_maps',
			'name'		=> __( 'Leaflet Javascript Library css File', 'avia_framework' ),
			'desc'		=> __( 'Select the location for the loaded css file', 'avia_framework' ),
			'id'		=> 'leaflet_maps_css_file',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'global'	=> true,
			'subtype'	=> array( 
								__( 'Use theme bundled library', 'avia_framework' )					=> '',
								__( 'Use plugin default library', 'avia_framework' )				=> 'default',
								__( 'Use manual added URL in settings page', 'avia_framework' )		=> 'custom',
							)
		);

$avia_elements[] = array(	
			'slug'			=> 'leaflet_maps', 
			'type'			=> 'visual_group_end',
			'id'			=> 'Leaflet_maps_container_close',
			'nodescription' => true
		);


