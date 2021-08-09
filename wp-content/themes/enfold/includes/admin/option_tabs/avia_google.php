<?php
/**
 * Google Services Tab
 * ===================
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;



$avia_elements[] = array(
			'slug'	        => 'google',
			'type'          => 'visual_group_start',
			'id'            => 'avia_google_analytics_group_start',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'          => 'google',
			'name'          => __( 'Google Analytics', 'avia_framework' ),
			'desc'          => '',
			'id'            => 'avia_google_analytics_heading',
			'type'          => 'heading',
			'std'           => '',
			'nodescription' => true
		);

$desc  = __( 'Either enter your Google tracking id (UA-XXXXX-X), (G-XXXXX) or your full Google Analytics tracking Code here.', 'avia_framework' );
$desc .= '<br><br>';
$desc .= __( 'If you want to offer your visitors the option to stop being tracked you can place the shortcode [av_privacy_google_tracking] somewhere on your site.', 'avia_framework' ) . ' ';
$desc .= __( 'More information and more privacy settings you find here:', 'avia_framework' ) . ' ';
$desc .= '<a href="' . admin_url( 'admin.php?page=avia#goto_cookie' )  . '">' . __( 'Privacy and Cookies', 'avia_framework' ) . '</a>';

$avia_elements[] =	array(
			'slug'  => 'google',
			'name'  => __( 'Google Analytics Tracking Code', 'avia_framework' ),
			'desc'  => $desc,
			'id'    => 'analytics',
			'type'	=> 'textarea',
			'std'	=> '',
			'class' => 'av_small_textarea',
		);

$avia_elements[] = array(
			'slug'          => 'google',
			'type'          => 'visual_group_end',
			'id'            => 'avia_google_analytics_group_ens',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'	        => 'google',
			'type'          => 'visual_group_start',
			'id'            => 'avia_google_maps_group_start',
			'nodescription' => true
		);


$google_link = 'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,places_backend&keyType=CLIENT_SIDE&reusekey=true';
$tutorial_link = 'https://kriesi.at/documentation/enfold/how-to-register-a-google-maps-api-key/';


$avia_elements[] = array(
			'slug'		=> 'google',
			'name'		=> __( 'Google Maps', 'avia_framework' ),
			'desc'		=> __( 'Google recently changed the way their map service works. New pages which want to use Google Maps need to register an API key for their website. Older pages should work fine without this API key. If the google map elements of this theme do not work properly you need to register a new API key.', 'avia_framework' ) . "<br><a href='{$google_link}' target='_blank' rel='noopener noreferrer'>" . __( 'Register an API Key', 'avia_framework' ) . "</a> | <a target='_blank' href='{$tutorial_link}' rel='noopener noreferrer'>" . __( 'Tutorial: How to create an API key', 'avia_framework' ) . '</a>',
			'id'		=> 'avia_gmaps_heading',
			'type'		=> 'heading',
			'std'		=> '',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'		=> 'google',
			'name'		=> __( 'Enable Google Maps on your site', 'avia_framework' ),
			'desc'		=> __( 'Select if you want to use Google Maps on your site. If it is disabled no Javascript to connect to Google Maps will be loaded in frontend.', 'avia_framework' ),
			'id'		=> 'gmap_enabled',
			'type'		=> 'select',
			'std'		=> 'disable_gmap',
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'Disable Google Maps', 'avia_framework' )	=> 'disable_gmap',
								__( 'Use Google Maps', 'avia_framework' )		=> ''
							)
		);


$avia_elements[] = array(
			'slug'           => 'google',
			'name'           => __( 'Enter a valid Google Maps API Key to use all map related theme functions', 'avia_framework' ),
			'desc'           => '',
			'id'             => 'gmap_api',
			'type'           => 'verification_field',
			'std'            => '',
			'required'       => array( 'gmap_enabled', '' ),
			'ajax'           => 'av_maps_api_check',
			'js_callback'    => 'av_maps_js_api_check',
			'class'          => 'av_full_description',
			'button-label'   => __( 'Check API Key', 'avia_framework' ),
			'button-relabel' => __( ' Check API Key', 'avia_framework' )
		);

$avia_elements[] = array(
			'slug'      => 'google',
			'name'      => __( 'Last verify state - hidden - used for internal use only', 'avia_framework' ),
			'desc'      => '',
			'id'        => 'gmap_verify_state',
			'type'      => 'hidden',
			'std'       => ''
		);

$avia_elements[] = array(
			'slug'      => 'google',
			'name'      => __( 'Last verified keys - hidden - used for internal use only', 'avia_framework' ),
			'desc'      => '',
			'id'        => 'gmap_verified_key',
			'type'      => 'hidden',
			'std'       => '',
		);

$avia_elements[] = array(
			'slug'          => 'google',
			'type'          => 'visual_group_end',
			'id'            => 'avia_google_maps_group_end',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'	        => 'google',
			'type'          => 'visual_group_start',
			'id'            => 'avia_google_recaptcha_group_start',
			'class'         => 'av-verify-button-container',
			'nodescription' => true
		);


$recaptcha = 'https://developers.google.com/recaptcha/intro';
$recaptcha_v3 = 'https://developers.google.com/recaptcha/docs/v3';
$recaptcha_admin = 'https://www.google.com/recaptcha/admin';
$recaptcha_doc = 'https://kriesi.at/documentation/enfold/contact-form/#captcha';

$recaptcha_desc  = __( 'Add Google reCAPTCHA widget functionality to the theme to verify if user is a human. Currently only enfold contact forms are supported and you can choose for each form individually if you want to use a reCAPTCHA.', 'avia_framework' ) . '<br />';
$recaptcha_desc .= sprintf( __( 'Info about <a href="%1$s" target="_blank" rel="noopener noreferrer">Google reCAPTCHA</a>. You need to create <a href="%2$s" target="_blank" rel="noopener noreferrer">API keys</a> for your site. Also check our <a href="%3$s" target="_blank" rel="noopener noreferrer">documentation.</a>', 'avia_framework' ), $recaptcha, $recaptcha_admin, $recaptcha_doc ) . '<br />';
$recaptcha_v3 = sprintf( __( 'Please keep in mind that Version 3 needs to <a href="%1$s" target="_blank" rel="noopener noreferrer">monitor user behaviour and collects user data</a>. In case the score does not recognize a human Version 2 checkbox will be used additionally for verification. Therefore you must also register V2 keys.', 'avia_framework' ), $recaptcha_v3 );
$recaptcha_score = __( 'A score of 1.0 is very likely a good interaction, 0.0 is very likely a bot. Google recommends a threshold of 0.5 by default. In case we encounter a non human we ask user to verify with Version 2 chckbox.', 'avia_framework' );

$avia_elements[] = array(
			'slug'          => 'google',
			'name'          => __( 'Google ReCAPTCHA','avia_framework' ),
			'desc'          => $recaptcha_desc,
			'id'            => 'avia_recaptcha',
			'type'          => 'heading',
			'std'           => '',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'     => 'google',
			'name'     => __( 'Select if you want to use Google reCAPTCHA', 'avia_framework' ),
			'desc'     => $recaptcha_v3,
			'id'       => 'avia_recaptcha_version',
			'type'     => 'select',
			'no_first' => true,
			'std'      => '',
			'subtype'  => array(
								__( 'Disable reCAPTCHA', 'avia_framework' )     => '',
								__( 'reCAPTCHA Version 2', 'avia_framework' )   => 'avia_recaptcha_v2',
								__( 'reCAPTCHA Version 3 (needs V2 as fallback verification)', 'avia_framework' )   => 'avia_recaptcha_v3',
							)
		);

$avia_elements[] = array(
			'slug'     => 'google',
			'name'     => __( 'Site Key Version 2', 'avia_framework' ),
			'desc'     => __( 'Enter the reCAPTCHA v2 API site key here.', 'avia_framework' ),
			'id'       => 'avia_recaptcha_pkey_v2',
			'type'     => 'text',
			'std'      => '',
			'required' => array( 'avia_recaptcha_version', '{contains_array}avia_recaptcha_v2;avia_recaptcha_v3' ),
		);

$avia_elements[] = array(
			'slug'     => 'google',
			'name'     => __( 'Secret Key Version 2', 'avia_framework' ),
			'desc'     => __( 'Enter the reCAPTCHA v2 API secret key here.', 'avia_framework' ),
			'id'       => 'avia_recaptcha_skey_v2',
			'type'     => 'text',
			'std'      => '',
			'required' => array( 'avia_recaptcha_version', '{contains_array}avia_recaptcha_v2;avia_recaptcha_v3' ),
		);

$avia_elements[] = array(
			'slug'			=> 'google',
			'name'			=> '',
			'desc'			=> '',
			'id'			=> 'avia_recaptcha_key_verify_v2',
			'type'			=> 'verification_field',
			'std'			=> '',
			'required'		=> array( 'avia_recaptcha_version','{contains_array}avia_recaptcha_v2;avia_recaptcha_v3' ),
			'force_callback' => true,
			'input_ids'		=> array( 'avia_recaptcha_version', 'avia_recaptcha_pkey_v2', 'avia_recaptcha_skey_v2', 'avia_recaptcha_pkey_v3', 'avia_recaptcha_skey_v3' ),
			'ajax'			=> 'av_recaptcha_api_check',
			'js_callback'	=> 'av_recaptcha_js_api_check',
			'class'			=> 'av_full_description',
			'button-label'   => __( 'Check reCAPTCHA API Keys Version 2', 'avia_framework' ),
			'button-relabel' => __( ' Check reCAPTCHA API Keys Version 2', 'avia_framework' )
		);

$avia_elements[] = array(
			'slug'		=> 'google',
			'name'		=> __( 'Site Key Version 3', 'avia_framework' ),
			'desc'		=> __( 'Enter the reCAPTCHA v3 API site key here.', 'avia_framework' ),
			'id'		=> 'avia_recaptcha_pkey_v3',
			'type'		=> 'text',
			'std'		=> '',
			'required'	=> array( 'avia_recaptcha_version', 'avia_recaptcha_v3' ),
		);

$avia_elements[] = array(
			'slug'		=> 'google',
			'name'		=> __( 'Secret Key Version 3', 'avia_framework' ),
			'desc'		=> __( 'Enter the reCAPTCHA v3 API secret key here.', 'avia_framework' ),
			'id'		=> 'avia_recaptcha_skey_v3',
			'type'		=> 'text',
			'std'		=> '',
			'required'	=> array( 'avia_recaptcha_version', 'avia_recaptcha_v3' ),
		);

$numbers = array();
for( $i = 0; $i <= 10; $i++ )
{
	$numbers[ number_format( $i / 10.0, 1, ',', ' ' ) ] = (string) $i;
}

$avia_elements[] = array(
			'slug'     => 'google',
			'name'     => __( 'Select Score For Human', 'avia_framework' ),
			'desc'     => $recaptcha_score,
			'id'       => 'avia_recaptcha_score',
			'type'     => 'select',
			'std'      => '5',
			'no_first' => true,
			'required' => array( 'avia_recaptcha_version', 'avia_recaptcha_v3' ),
			'subtype'  => $numbers
		);

/**
 * @used_by				av_google_recaptcha
 * @since 4.6.2
 */
if( current_theme_supports( 'avia_recaptcha_show_legal_information' ) )
{
	$desc  = __( 'Select if you want to show the default Google badge or only a message below the submit button. This is mandatory if you want to use V3.', 'avia_framework' );
	$desc .= ' <a href="https://developers.google.com/recaptcha/docs/faq#id-like-to-hide-the-recaptcha-badge-what-is-allowed" target="_blank" rel="noopener noreferrer">' . __( 'See Google documentation', 'avia_framework' ) . '</a>.';

	$avia_elements[] = array(
				'slug'     => 'google',
				'name'     => __( 'Google Legal Information', 'avia_framework' ),
				'desc'     => $desc,
				'id'       => 'avia_recaptcha_badge',
				'type'     => 'select',
				'no_first' => true,
				'std'      => 'contact_only_message',
				'required' => array( 'avia_recaptcha_version', 'avia_recaptcha_v3' ),
				'subtype'  => array(
									__( 'Show default Google badge on all pages', 'avia_framework' )				=> '',
									__( 'Show a message string on contact form page instead', 'avia_framework' )	=> 'message',
									__( 'Show message string on contact form page only, hide badge on other pages', 'avia_framework' ) => 'contact_only_message',
									__( 'Hide badge and string completely on every page', 'avia_framework' )		=> 'hide'
								),
			);
}
else
{
	$desc = '<strong>' . __( 'Google Legal Information - for developers', 'avia_framework' ) . '</strong><br />';
	$desc .= __( 'If you want to change the default behaviour you can display a select box by adding to functions.php:', 'avia_framework' ) . '<br />';
	$desc .= 'add_theme_support( "avia_recaptcha_show_legal_information" );';

	$avia_elements[] =	array(
				'slug'		=> 'google',
				'name'		=> '',
				'desc'		=> $desc,
				'id'		=> 'avia_recaptcha_legal_description',
				'type'		=> 'heading',
				'std'		=> '',
				'nodescription' => true
			);

	$avia_elements[] = array(
				'slug'      => 'google',
				'name'      => __( 'Google Legal Information - hidden - uses default value', 'avia_framework' ),
				'desc'      => '',
				'id'        => 'avia_recaptcha_badge',
				'type'      => 'hidden',
				'std'       => 'contact_only_message'
			);
}

$avia_elements[] = array(
			'slug'           => 'google',
			'name'           => '',
			'desc'           => '',
			'id'             => 'avia_recaptcha_key_verify_v3',
			'type'           => 'verification_field',
			'std'            => '',
			'required'       => array( 'avia_recaptcha_version', 'avia_recaptcha_v3' ),
			'force_callback' => true,
			'input_ids'		 => array( 'avia_recaptcha_version', 'avia_recaptcha_pkey_v2', 'avia_recaptcha_skey_v2', 'avia_recaptcha_pkey_v3', 'avia_recaptcha_skey_v3' ),
			'ajax'           => 'av_recaptcha_api_check',
			'js_callback'    => 'av_recaptcha_js_api_check',
			'class'          => 'av_full_description',
			'button-label'   => __( 'Check reCAPTCHA API Keys V3', 'avia_framework' ),
			'button-relabel' => __( ' Check reCAPTCHA API Keys V3', 'avia_framework' )
		);

$avia_elements[] = array(
			'slug'      => 'google',
			'name'      => __( 'Last verified keys - hidden - used for internal use only', 'avia_framework' ),
			'desc'      => '',
			'id'        => 'recaptcha_verified_keys_v2',
			'type'      => 'hidden',
			'std'       => ''
		);

$avia_elements[] = array(
			'slug'      => 'google',
			'name'      => __( 'Last verified keys - hidden - used for internal use only', 'avia_framework' ),
			'desc'      => '',
			'id'        => 'recaptcha_verified_keys_v3',
			'type'      => 'hidden',
			'std'       => ''
		);

$avia_elements[] = array(
			'slug'          => 'google',
			'type'          => 'visual_group_end',
			'id'            => 'avia_google_recaptcha_group_end',
			'nodescription' => true
		);

