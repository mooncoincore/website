<?php
/**
 * Newsletter Tab
 * ==============
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;



$avia_elements[] = array(	
			'slug'		=> 'newsletter',
			'name'		=> 	__( 'Newsletter via Mailchimp', 'avia_framework' ),
			'desc'		=> __( 'Mailchimp allows you to easily use newsletter functionality with this theme. In order to use the Newsletter features you need to create a Mailchimp account and enter your API key into the field below.', 'avia_framework' ) . "<br/><br/><a href='https://admin.mailchimp.com/account/api' target='_blank' rel='noopener noreferrer'>" . __( 'You can find your API key here', 'avia_framework' ) . '</a>',
			'type'		=> 'heading',
			'std'		=> '',
			'nodescription'	=> true
		);

$avia_elements[] =	array(
			'slug'		=> 'newsletter',
			'name'		=> __( 'Enter a valid Mailchimp API Key to use all newsletter related theme functions', 'avia_framework' ),
			'desc'		=> false,
			'id'		=> 'mailchimp_api',
			'type'		=> 'verification_field',
			'std'		=> '',
			'help'		=> '',
			'ajax'		=> 'av_mailchimp_check_ajax',
			'button-label'		=> __( 'Check API Key', 'avia_framework' ),
			'button-relabel'	=> __( 'Check Key again & renew Lists', 'avia_framework' )
		);

$avia_elements[] = array(
			'slug'      => 'newsletter',
			'name'      => __( 'Last verified key - hidden - used for internal use only', 'avia_framework' ),
			'desc'      => '',
			'id'        => 'mailchimp_verified_key',
			'type'      => 'hidden',
			'std'       => '',
		);

