<?php
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

/**
 * Contains translateable js strings for backend
 * 
 * Will be encoded as global variable - '_js' is replaced with '_L10n'
 */

$strings['avia_modal_js']  = array(
				'ajax_error'		=> __( 'Error fetching content - please reload the page and try again', 'avia_framework' ),
				'login_error'		=> __( 'It seems your are no longer logged in. Please reload the page and try again', 'avia_framework' ),
				'timeout'			=> __( 'Your session timed out. Simply reload the page and try again', 'avia_framework' ),
				'error'				=> __( 'An error occurred', 'avia_framework' ),
				'attention'			=> __( 'Attention!', 'avia_framework' ),
				'success'			=> __( 'All right!', 'avia_framework' ),
				'save'				=> __( 'Save', 'avia_framework' ),
				'close'				=> __( 'Close', 'avia_framework' ),
				'connection_error'	=> __( 'Error occured when trying to connect to server.', 'avia_framework' ),

				//	shortcode specific
				'select_layout'		=> __( 'Select a cell layout', 'avia_framework' ),
				'no_layout'			=> __( 'The current number of cells does not allow any layout variations', 'avia_framework' ),
				'add_one_cell'		=> __( 'You need to add at least one cell', 'avia_framework' ),
				'remove_one_cell'	=> __( 'You need to remove at least one cell', 'avia_framework' ),

				'gmap_api_text'		=> __( 'Google changed the way google maps work. You now need to enter a valid Google Maps API Key', 'avia_framework' ) . '<br/><br/>'.
									   __( 'You can read a description on how to create and enter that key here:', 'avia_framework' ) . ' ' .
									   "<a target='_blank' href='" . admin_url( 'admin.php?page=avia#goto_google' ) . "'>" . __( 'Enfold Google Settings', 'avia_framework' ) . '</a>',
				
				'gmap_api_wrong'	=> __( 'It seems that your Google API key is not configured correctly', 'avia_framework' ) . '<br/><br/>'.
									   __( 'The key is probably either restricted to the wrong domain or the domain syntax you entered is wrong.', 'avia_framework' ) . ' <br><br>' .
									   __( 'Please check your API key', 'avia_framework' ) . " <a target='_blank' href='https://console.developers.google.com/apis/credentials' rel='noopener noreferrer'>" . __( 'here', 'avia_framework' ) . '</a><br><br>'.
									   __( 'The domain that should be allowed is:', 'avia_framework' ) . ' <br><strong>'. trailingslashit( get_site_url() ) . '*</strong>',
								   
				'toomanyrequests'	=> __( 'Too many requests at once, please wait a few seconds before requesting coordinates again', 'avia_framework' ),
				'notfound'			=> __( "Address couldn't be found by Google, please add it manually", 'avia_framework' ),
				'insertaddress' 	=> __( 'Please insert a valid address in the fields above', 'avia_framework' ),
	
				'alb_same_element'	=> __( 'You cannot select the current edited element as a template. Please use another template or no template. Your selection has been reset.', 'avia_framework' ),
				/**
				 * Limitation to alb option values: Some character break the ALB in backend and frontend.
				 * We offer plugin https://github.com/KriesiMedia/enfold-library/tree/master/integration%20plugins/Enfold/Special%20Character%20Translation as a workaround
				 * 
				 * A comma seperated list of characters you want to check from being entered in input="text" and textarea in modal popup
				 */
				'alb_critical_modal_charecters'	=> implode( ',', Avia_Builder()->critical_modal_charecters() ),
	
				'noPermission'		=> __( 'You have not sufficient rights to perform this operation.', 'avia_framework' ),
				'noTemplate'		=> __( 'Error: Template does not exist.', 'avia_framework' ),
				'deleteTemplate'	=> __( 'Do you really want to delete this custom element permanently? This action cannot be undone.', 'avia_framework' ),
				'notDeletedTempl'	=> __( 'Custom element was not deleted.', 'avia_framework' ),
				
				'leafletNotActive'	=> __( 'Leaflet is not active at the moment. We cannot find the coordinates for your address.', 'avia_framework' ),
				'leafletResults'	=> __( 'Search Results for address:', 'avia_framework' ),
				'leafletNotFound'	=> __( 'No results found. Please check your input, e.g. spelling of street, city, ....', 'avia_framework' ),
				'leafletNoAddress'	=> __( 'You did not enter an address. Please enter at least a City to fetch the coordinates.', 'avia_framework' ),
			);
			
			
$strings['avia_history_js']  = array(
				'undo_label' => __( 'Undo', 'avia_framework' ),
				'redo_label' => __( 'Redo', 'avia_framework' ),
			);			


$strings['avia_template_save_js']  = array(
				'no_content' => __( 'You need to add at least one element to the canvas to save this entry as a template', 'avia_framework' ),
				'chose_name' => __( 'Choose Template Name', 'avia_framework' ),
				'chose_save' => __( 'Save Element as Template: Choose a Name', 'avia_framework' ),
				'chars'      => __( 'Allowed Characters: Whitespace', 'avia_framework' ),
				'save_msg'   => __( 'Template Name must have at least 3 characters', 'avia_framework' ),
				'not_found'  => __( 'Could not load the template. You might want to try and reload the page', 'avia_framework' ),
			);	
