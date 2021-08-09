<?php
/**
 * Privacy and Cookies Tab
 * =======================
 * 
 * @author kriesi
 * @since 4.4
 * @since 4.8.2 in this file
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;



$avia_elements[] = array(
			'slug'          => 'cookie',
			'name'          => __( 'Privacy and Cookies','avia_framework' ),
			'desc'          => '',
			'id'            => 'avia_p_and_c',
			'type'          => 'heading',
			'std'           => '',
			'nodescription' => true
		);



//START TAB CONTAINER
$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_1',
			'nodescription'	=> true,
			'class'			=> 'avia_tab_container avia_set'
		);

// Start TAB
$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_2',
			'nodescription'	=> true,
			'class'			=> 'avia_tab avia_tab2',
			'name'			=> __( 'Privacy Policy', 'avia_framework' )
		);


$eu_msg  = __( 'In case you deal with any EU customers/visitors these options allow you to make your site GDPR compliant.', 'avia_framework' ) . '<br />';
$eu_msg .= __( 'The following default text will be applied if you leave the textfields empty:', 'avia_framework' ) . '<br />';
$eu_msg .= '<p><strong>' . av_privacy_class::get_default_privacy_message() . '</strong></p>';

$avia_elements[] = array(
			'slug'		=> 'cookie',
			'name'		=> __( 'Privacy Policy','avia_framework' ),
			'desc'		=> $eu_msg,
			'id'		=> 'gdpr_overveiw',
			'type'		=> 'heading',
			'std'		=> '',
			'nodescription'	=> true
		);


$policy_page_wp = get_option( 'wp_page_for_privacy_policy' );

if( empty( $policy_page_wp ) )
{
	global $wp_version;

	//remove any beta tags from version string
	$clean_version = explode( '-', $wp_version );
	$clean_version = $clean_version[0];

	$notice_class = ' av-text-notice';
	$notice_msg = __( 'Attention: You need to set a Privacy Policy page here to activate these features:', 'avia_framework' ) . ' <a target="_blank" href="' . admin_url( 'privacy.php' ) . '">' . __( 'Set Privacy Policy', 'avia_framework' ) . '</a>';


	if(version_compare($clean_version, '4.9.6', '<' ))
	{
		$notice_class 	= ' av-text-notice av-notice-error';
		$notice_msg 	= __( 'Attention: You need WordPress version 4.9.6 or higher to use these features', 'avia_framework' );
	}

	$avia_elements[] =	array(
				'slug'		=> 'cookie',
				'desc'		=> "<br><strong class='{$notice_class} av-prev-el-notice'>" . $notice_msg . '</strong>',
				'id'		=> 'privacy_activate',
				'type'		=> 'heading',
				'std'		=> '',
				'nodescription'	=> true
			);
}

else /******************************** PRIVACY PAGE ACTIVE *****************************************/
{

$desc = __( 'A short message that can be displayed below forms, along with a checkbox, that lets the user know that he has to agree to your privacy policy in order to send the form. See default text above if you leave empty.', 'avia_framework' );

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Append a privacy policy message to your comment form?', 'avia_framework' ),
			'desc' 	=> __( 'Check to append a message to the comment form for unregistered users. Commenting without consent is no longer possible', 'avia_framework' ),
			'id' 	=> 'privacy_message_commentform_active',
			'type' 	=> 'checkbox',
			'std'	=> false,
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Message below comment form', 'avia_framework' ),
			'desc' 	=> $desc,
			'id' 	=> 'privacy_message',
			'type' 	=> 'textarea',
			'class' => 'av_small_textarea',
			'std' 	=> '',
			'required' => array( 'privacy_message_commentform_active', 'privacy_message_commentform_active' ),
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Append a privacy policy message to template builder contact forms?', 'avia_framework' ),
			'desc' 	=> __( 'Check to append a message to all of your contact forms.', 'avia_framework' ),
			'id' 	=> 'privacy_message_contactform_active',
			'type' 	=> 'checkbox',
			'std'	=> false,
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Message below template builder contact forms', 'avia_framework' ),
			'desc' 	=> $desc,
			'id' 	=> 'privacy_message_contact',
			'type' 	=> 'textarea',
			'class' => 'av_small_textarea',
			'std' 	=> '',
			'required' => array( 'privacy_message_contactform_active', 'privacy_message_contactform_active' ),
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Append a privacy policy message to mailchimp contact forms?', 'avia_framework' ),
			'desc' 	=> __( 'Check to append a message to all of your mailchimp forms.', 'avia_framework' ),
			'id' 	=> 'privacy_message_mailchimp_active',
			'type' 	=> 'checkbox',
			'std'	=> false,
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Message below mailchimp subscription forms', 'avia_framework' ),
			'desc' 	=> $desc,
			'id' 	=> 'privacy_message_mailchimp',
			'type' 	=> 'textarea',
			'class' => 'av_small_textarea',
			'std' 	=> '',
			'required' => array( 'privacy_message_mailchimp_active', 'privacy_message_mailchimp_active' ),
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Append a privacy policy message to your login forms?', 'avia_framework' ),
			'desc' 	=> __( 'Check to append a message to the default login forms.', 'avia_framework' ),
			'id' 	=> 'privacy_message_login_active',
			'type' 	=> 'checkbox',
			'std'	=> false,
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Message below login forms', 'avia_framework' ),
			'desc' 	=> $desc,
			'id' 	=> 'privacy_message_login',
			'type' 	=> 'textarea',
			'class' => 'av_small_textarea',
			'std' 	=> '',
			'required' => array( 'privacy_message_login_active', 'privacy_message_login_active' ),
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Append a privacy policy message to your registration forms?', 'avia_framework' ),
			'desc' 	=> __( 'Check to append a message to the default registrations forms.', 'avia_framework' ),
			'id' 	=> 'privacy_message_registration_active',
			'type' 	=> 'checkbox',
			'std'	=> false
		);

$avia_elements[] =	array(
			'slug'		=> 'cookie',
			'name'		=> __( 'Message below registration forms', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'privacy_message_registration',
			'type'		=> 'textarea',
			'class'		=> 'av_small_textarea',
			'std'		=> '',
			'required'	=> array( 'privacy_message_registration_active', 'privacy_message_registration_active' ),
		);


// END TAB
$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_2',
			'nodescription' => true
		);

// Start TAB
$avia_elements[] = array(
			'slug'			=> 'cookie',
			'name'			=>	__( 'Privacy Shortcodes', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_3',
			'nodescription' => true,
			'class'			=> 'avia_tab avia_tab2'
		);

$pp_id = get_option( 'wp_page_for_privacy_policy' );
$pp_url = admin_url( "post.php?post={$pp_id}&action=edit" );
$pp_title = get_the_title( $pp_id );

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Shortcodes you can use in your Privacy Policy','avia_framework' ) . " - <a target='_blank' href='{$pp_url}'>({$pp_title})</a>",
			'desc'	=> __( 'In order to offer your users a better experience you can use the shortcodes listed here in your privacy policy. These shortcodes allow your users to change certain behavior of your website.', 'avia_framework' ) .
						'<ul>' .
						'<li><strong>[av_privacy_allow_cookies]</strong> - '	. __( ' allows a user to refuse cookies and hides message bar (needs 2 cookies for that, others are removed)', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_accept_essential_cookies]</strong> - '	. __( ' allows a user to opt out from essential theme and all other cookies (except 2 from av_privacy_allow_cookies)', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_google_tracking]</strong> - '	. __( ' allows a user to disable Google tracking in his or her browser', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_google_webfonts]</strong> - '	. __( ' allows a user to disable the use of Google webfonts in his or her browser', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_google_recaptcha]</strong> - '	. __( ' allows a user to disable the use of Google reCaptcha in his or her browser', 'avia_framework' ) . '</li>'.
						'<li><strong>[av_privacy_google_maps]</strong> - '		. __( ' allows a user to disable the use of Google Maps in his or her browser', 'avia_framework' ) . '</li>'.
						'<li><strong>[av_privacy_video_embeds]</strong> - '		. __( ' allows a user to disable video embeds in his or her browser', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_custom_cookie cookie_name=""]</strong> - ' .__( ' allows a user to disable custom cookies (see options below) in his or her browser', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_link]</strong> - '				. __( ' displays a link to the privacy policy page set in your WordPress admin panel or to a custom page', 'avia_framework' ) . '</li>' .
//						'<li><strong>[av_privacy_google_webfonts]</strong> - '. __( ' allows a user to disable the use of google webfonts', 'avia_framework' ) . '</li>' .
						'</ul><br>' .
						__( 'Please note: if you do not like the default text that is displayed by those shortcodes you can change it by using [shortcode]Your text here[/shortcode]', 'avia_framework' ) .
						 '<br /><br />' .
						'<ul>' .
						'<li><strong>[av_privacy_cookie_info id="" class=""]</strong> - ' . __( ' adds a list about used and accessable cookies in domain with value and additional info about the cookie', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_accept_button wrapper_class="" id="" class=""]your button text[/av_privacy_accept_button]</strong> - ' . __( ' adds an accept cookies button', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_accept_all_button wrapper_class="" id="" class=""]your button text[/av_privacy_accept_all_button]</strong> - ' . __( ' adds an accept all cookies and services button', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_do_not_accept_button wrapper_class="" id="" class=""]your button text[/av_privacy_do_not_accept_button]</strong> - ' . __( ' adds a do not accept cookies button', 'avia_framework' ) . '</li>' .
						'<li><strong>[av_privacy_modal_popup_button wrapper_class="" id="" class=""]your button text[/av_privacy_modal_popup_button]</strong> - ' . __( ' adds a button that opens the privacy modal popup window - you have to enable cookie consent message bar', 'avia_framework' ) . '</li>' .
						'</ul><br>',
			'id'	=> 'gdpr_shortcodes',
			'type'	=> 'heading',
			'std'	=> '',
			'nodescription'	=> true
		);

}


$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_3',
			'nodescription' => true
		);


// Start TAB
$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_4',
			'nodescription' => true,
			'class'			=> 'avia_tab avia_tab2',
			'name'			=>	__( 'Cookie Handling', 'avia_framework' )
		);


$cookie_desc = '';
$cookie_desc .= __( "Make sure you comply with the <a target='_blank' href='http://ec.europa.eu/ipg/basics/legal/cookies/index_en.htm' rel='noopener noreferrer'>EU cookie law</a> by informing users that your site uses cookies. This can be done with a small notification bar or modal popup window", 'avia_framework' );
$cookie_desc .= '<br><br>';
$cookie_desc .= __( 'You can also use the message bar to display a one time message not related to cookies if you do not need to inform your customers about the use of cookies.', 'avia_framework' ) . ' ';
$cookie_desc .= '<br><br>';
$cookie_desc .= __( 'More detailed information about the cookie law, message bar usage, the styling of the bar and more can be found in our documentation: ', 'avia_framework' ) . '<br><a href="https://kriesi.at/documentation/enfold/privacy-cookies/" target="_blank" rel="noopener noreferrer">' . __( 'Enfold Privacy And Cookies', 'avia_framework' ) . '</a>.';
$cookie_desc .= '<br><br>';
$cookie_desc .= '<strong>' . __( 'Using a caching plugin: Whenever you make changes here please clear server cache to allow a rebuild of the pages to reflect the changed options.', 'avia_framework' ) . '</strong>';

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Cookie Handling and Cookie Consent Messages', 'avia_framework' ),
			'desc'	=> $cookie_desc,
			'type'	=> 'heading',
			'id'	=> 'cookie_overlay_description',
//			'class'	=> 'avia_heading_boxed',
			'std'	=> '',
			'nodescription'	=> true
		);


/***************************************************************************************************/
/**
 * Cookie Consent section
 *
 * @author tinabillinger
 * @since 4.3
 * @since 4.5.7.2 extended by Günter
 */

$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Enable cookie consent messages', 'avia_framework' ),
			'desc'	=> __( 'Enable cookie consent messages to use message bar and modal popup.', 'avia_framework' ),
			'id'	=> 'cookie_consent',
			'type'	=> 'select',
			'std'	=> '',
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'Disable cookie consent messages', 'avia_framework' )		=> '',
								__( 'Enable cookie consent messages', 'avia_framework' )		=> 'cookie_consent'
							)
		);

$avia_elements[] = array(
			'slug'		=> 'cookie',
			'name'		=> __( 'Default Cookie Behaviour', 'avia_framework' ),
			'desc'		=> __( 'Select how cookies and privacy options should be loaded by default for new visitors. Please remember that it is the responsibility of the website owner to fulfill the local rules for data privacy.', 'avia_framework' ),
			'id'		=> 'cookie_default_settings',
			'type'		=> 'select',
			'required'	=> array( 'cookie_consent', '{contains_array}cookie_consent;cookie__consent_no_bar' ),
			'std'		=> '',
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'All cookies and services are accepted on first page load, user can opt out', 'avia_framework' )	=> '',
								__( 'User must accept and can opt out, all selected by default', 'avia_framework' )				=> 'can_opt_out',
								__( 'User must accept and must opt in, only essential cookies selected', 'avia_framework' )		=> 'needs_opt_in',
								__( 'Essential cookies are accepted on first page load, user must opt in', 'avia_framework' )	=> 'essential_only',
							)
		);


$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_5',
			'nodescription'	=> true,
			'required'		=> array( 'cookie_consent', '{contains_array}cookie_consent;message_bar' ),
		);

$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Cookie Consent Message Bar', 'avia_framework' ),
			'desc'	=> __( 'Define content and buttons for your message bar to inform users about the use of cookies and services and depending on your country laws to opt in or opt out for services and cookies. If you make changes to message text or button label the message bar will be show again.', 'avia_framework' ),
			'id'	=> 'consent_msg_bar_headline',
			'type'	=> 'heading',
//			'class'	=> 'avia_heading_boxed',
			'std'	=> '',
			'nodescription'	=> true
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Message', 'avia_framework' ),
			'desc' 	=> __( 'Provide a message which indicates that your site uses cookies.', 'avia_framework' ),
			'id' 	=> 'cookie_content',
			'type' 	=> 'textarea',
			'std'   => __( 'This site uses cookies. By continuing to browse the site, you are agreeing to our use of cookies.', 'avia_framework' )
		);


$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Message Bar Position', 'avia_framework' ),
			'desc'	=> __( 'Where on the page should the message bar appear?', 'avia_framework' ),
			'id'	=> 'cookie_position',
			'type'	=> 'select',
			'std'	=> 'bottom',
			'no_first'	=> true,
			'subtype'	=> array(
							__( 'Top', 'avia_framework' )					=> 'top',
							__( 'Bottom', 'avia_framework' )				=> 'bottom',
							__( 'Top Left Corner', 'avia_framework' )		=> 'top-left',
							__( 'Top Right Corner', 'avia_framework' )		=> 'top-right',
							__( 'Bottom Left Corner', 'avia_framework' )	=> 'bottom-left',
							__( 'Bottom Right Corner', 'avia_framework' )	=> 'bottom-right',
						)
				);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_6',
//			'class'			=> 'avia_boxed_visual_group',
			'nodescription'	=> true,
		);

$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Buttons', 'avia_framework' ),
			'desc'	=> __( 'You can create any number of buttons/links for your message bar here:', 'avia_framework' ),
			'type'	=> 'heading',
			'std'	=> '',
			'nodescription' => true
		);


$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_6',
			'nodescription'	=> true,
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_7',
//			'class'			=> 'avia_boxed_visual_group',
			'nodescription'	=> true,
		);

$avia_elements[] =	array(
			'slug'			=> 'cookie',
			'id' 			=> 'msg_bar_buttons',
			'type' 			=> 'group',
			'linktext' 		=> '+',
			'deletetext' 	=> '×',
			'blank' 		=> true,
			'nodescription' => true,
			'std'			=> array(
									array(
										'msg_bar_button_label'		=> __( 'Accept settings', 'avia_framework' ),
										'msg_bar_button_action'		=> '',
										'msg_bar_button_tooltip'	=> __( 'Allow to use cookies, you can modify used cookies in settings', 'avia_framework' )
										),
									array(
										'msg_bar_button_label'		=> __( 'Hide notification only', 'avia_framework' ),
										'msg_bar_button_action'		=> 'hide_notification',
										'msg_bar_button_tooltip'	=> __( 'Do not allow to use cookies - some functionality on our site might not work as expected.', 'avia_framework' )
										),
									array(
										'msg_bar_button_label'		=> __( 'Settings', 'avia_framework' ),
										'msg_bar_button_action'		=> 'info_modal',
										'msg_bar_button_tooltip'	=> __( 'Get more info about cookies and select which one you want to allow or not.', 'avia_framework' ),
										),
									),
			'subelements' 	=> array(
									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Button Label', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'msg_bar_button_label',
										'type' 	=> 'text',
										'class' => 'av_3columns av_col_1'
										),

									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Button Action', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'msg_bar_button_action',
										'type' 	=> 'select',
										'class' => 'av_3columns av_col_2',
										'no_first'	=> true,
										'subtype'	=> array(
												__( 'Accept settings and dismiss notification', 'avia_framework' )	=> '',
												__( 'Accept all cookies and services, dismiss notification', 'avia_framework' )	=> 'select_all',
												__( 'Do not accept and hide notification', 'avia_framework' )		=> 'hide_notification',
												__( 'Open info modal on privacy and cookies', 'avia_framework' )	=> 'info_modal',
												__( 'Link to another page', 'avia_framework' )						=> 'link',
											)
										),

									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Button Link', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'msg_bar_button_link',
										'type' 	=> 'text',
										'class' => 'av_3columns av_col_3',
										'required' => array( 'msg_bar_button_action', '{contains}link' )
										),

									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Button Tooltip', 'avia_framework' ),
										'desc' 	=> __( 'Enter an additional tooltip to give a closer information about use of the button', 'avia_framework' ),
										'id' 	=> 'msg_bar_button_tooltip',
										'type' 	=> 'text',
										'class' => ''
										),
								)
						);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_7',
			'nodescription'	=> true,
	);



$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_5',
			'nodescription'	=> true,
			'required'		=> array( 'cookie_consent', '{contains_array}cookie_consent;message_bar' ),
	);


$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_8',
			'nodescription'	=> true,
			'required'		=> array( 'cookie_consent', '{contains_array}cookie_consent;cookie__consent_no_bar;message_bar' )
		);

$desc  = __( 'Define a modal popup window to inform visitors about your privacy policy and to opt in or out of services and cookies.', 'avia_framework' );
$desc .= '<br /><br />';
$desc .= __( 'By default we use the built in lightbox to show the popup. If you want to use your own lightbox you can assign a js wrapper function to avia_cookie_consent_modal_callback (see file enfold\js\avia-snippet-cookieconsent.js) to activate yours.', 'avia_framework' );

$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Modal Popup Window', 'avia_framework' ),
			'desc'	=> $desc,
			'id'	=> 'modal_popup_window_headline',
			'type'	=> 'heading',
			'std'	=> '',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Display Modal Popup Actions', 'avia_framework' ),
			'desc'	=> __( 'Select how to display your modal popup window. Country law regulation might enforce you to show the popup on first page load. If user does not accept cookies he will be prompted every time when opening a new window or tab.', 'avia_framework' ),
			'id'	=> 'modal_popup_window_action',
			'type'	=> 'select',
			'std'	=> '',
			'no_first'	=> true,
			'subtype'	=> array(
							__( 'Open with a button only', 'avia_framework' )		=> '',
							__( 'Open immediately on pageload', 'avia_framework' )	=> 'page_load',
						)
			);


$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_9',
//			'class'			=> 'avia_boxed_visual_group',
			'nodescription'	=> true,
		);


$avia_elements[] =	array(
			'slug'			=> 'cookie',
			'id' 			=> 'modal_popup_window_buttons',
			'type' 			=> 'group',
			'linktext' 		=> '+',
			'deletetext' 	=> '×',
			'blank' 		=> true,
			'nodescription' => true,
			'std'			=> array(
									array(
										'modal_popup_button_label'		=> __( 'Accept settings', 'avia_framework' ),
										'modal_popup_button_action'		=> '',
										'modal_popup_button_tooltip'	=> __( 'Allow to use cookies, you always can modify used cookies and services', 'avia_framework' )
										),
									array(
										'modal_popup_button_label'		=> __( 'Hide notification only', 'avia_framework' ),
										'modal_popup_button_action'		=> 'hide_notification',
										'modal_popup_button_tooltip'	=> __( 'Do not allow to use cookies or services - some functionality on our site might not work as expected.', 'avia_framework' )
										)
									),
			'subelements' 	=> array(

									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Button Label', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'modal_popup_button_label',
										'type' 	=> 'text',
										'std'	=> '',
										'class' => 'av_3columns av_col_1'
										),

									array(
										'slug'		=> 'cookie',
										'name'		=> __( 'Button Action', 'avia_framework' ),
										'desc'		=> '',
										'id'		=> 'modal_popup_button_action',
										'type'		=> 'select',
										'class'		=> 'av_3columns av_col_2',
										'no_first'	=> true,
										'std'		=> '',
										'subtype'	=> array(
														__( 'Accept settings and dismiss notification', 'avia_framework' )	=> '',
														__( 'Accept all cookies and services, dismiss notification', 'avia_framework' )	=> 'select_all',
														__( 'Do not accept and hide notification', 'avia_framework' )		=> 'hide_notification',
														__( 'Link to another page', 'avia_framework' )						=> 'link',
													)
										),

									array(
										'slug'		=> 'cookie',
										'name'		=> __( 'Button Link', 'avia_framework' ),
										'desc'		=> '',
										'id'		=> 'modal_popup_button_link',
										'type'		=> 'text',
										'std'		=> '',
										'class'		=> 'av_3columns av_col_3',
										'required'	=> array( 'modal_popup_button_action', '{contains}link' )
										),

									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Button Tooltip', 'avia_framework' ),
										'desc' 	=> __( 'Enter an additional tooltip to give a closer information about use of the button', 'avia_framework' ),
										'id' 	=> 'modal_popup_button_tooltip',
										'type' 	=> 'text',
										'std'	=> '',
										'class' => ''
										),
								)
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_9',
			'nodescription'	=> true,
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Modal Window Custom Content', 'avia_framework' ),
			'desc'	=> __( 'Instead of displaying the default content set custom content yourself.', 'avia_framework' ),
			'id'	=> 'cookie_info_custom_content',
			'type'	=> 'checkbox',
			'std'	=> false
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_10',
			'nodescription'	=> true,
			'required'		=> array( 'cookie_info_custom_content', 'cookie_info_custom_content' )
		);

$desc  = __( 'Define content of your modal popup window to inform visitors about your privacy policy. Use shortcodes to add toggles so visitors can opt in or out of services and cookies.', 'avia_framework' );
$desc .= '<br /><br />';
$desc .= '<strong>';
$desc .=		__( 'If you want to allow your visitors to opt out of essential cookies and hide the message bar when returning to your site you need to add the following 2 shortcodes to your content:', 'avia_framework' );
$desc .= '</strong>';
$desc .= '<ul>';
$desc .=	'<li><strong>[av_privacy_allow_cookies]</strong> - ' . __( 'allows a user to refuse cookies and hides message bar (needs 2 cookies for that, others are removed)', 'avia_framework' ) . '</li>';
$desc .=	'<li><strong>[av_privacy_accept_essential_cookies]</strong> - ' . __( 'allows a user to opt out from essential theme and all other cookies (except 2 from av_privacy_allow_cookies)', 'avia_framework' ) . '</li>';
$desc .= '</ul>';


$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Modal Popup Window Content', 'avia_framework' ),
			'desc'	=> $desc,
			'id'	=> 'modal_popup_window_content_headline',
			'type'	=> 'heading',
			'std'	=> '',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Main Heading', 'avia_framework' ),
			'desc' 	=> '',
			'id' 	=> 'cookie_info_content_heading',
			'type' 	=> 'text',
			'std'	=> 'Cookie and Privacy Settings'
		);


$contents = av_privacy_helper()->get_default_modal_popup_content( 'no_filter' );

$avia_elements[] =	array(
			'slug'			=> 'cookie',
			'id' 			=> 'cookie_info_content',
			'type' 			=> 'group',
			'linktext' 		=> '+',
			'deletetext' 	=> '×',
			'blank' 		=> true,
			'nodescription' => true,
			'std'			=> $contents,
			'subelements' 	=> array(
									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Tab Label', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'label',
										'type' 	=> 'text',
										'std'	=> '',
										'class' => 'av_2columns av_col_1'
									),
									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Tab Content', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'content',
										'type' 	=> 'textarea',
										'std'	=> '',
										'class' => 'av_2columns av_col_2'
									)
								)
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_10',
			'nodescription' => true,
			'required'		=> array( 'cookie_info_custom_content', 'cookie_info_custom_content' )
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_8',
			'nodescription'	=> true,
			'required'		=> array( 'cookie_consent', '{contains_array}cookie_consent;cookie__consent_no_bar;message_bar' )
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_11',
			'nodescription'	=> true,
			'required'		=> array( 'cookie_consent', '{contains_array}cookie_consent' )
		);


$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Advanced Options', 'avia_framework' ),
			'desc'	=> '',
			'id'	=> 'cookie_advanced_options_headline',
			'type'	=> 'heading',
			'std'	=> '',
//			'class' => 'avia_boxed_visual_group',
			'nodescription'	=> true
		);

$avia_elements[] =	array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Show advanced options', 'avia_framework' ),
			'desc'	=> __( 'Contains options for special use cases like using the message bar just for simple notifications', 'avia_framework' ),
			'id'	=> 'cookie_show_advanced_options',
			'type'	=> 'checkbox',
			'std'	=> false,
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_12',
			'nodescription' => true,
			'required'		=> array( 'cookie_show_advanced_options', '{contains_array}cookie_show_advanced_options' )
		);

$desc  = __( 'Select if you want to use cookie logic or only display one time messages to your visitor but do not need the cookie logic. The message bar pops up again whenever you change the displayed text or button labels.', 'avia_framework' );
$desc .= '<br /><br />';
$desc .= __( 'To use cookie logic without showing a message bar please check our documentation:', 'avia_framework' ) . ' <a href="https://kriesi.at/documentation/enfold/privacy-cookies/#notification-bar" target="_blank" rel="noopener noreferrer">' . __( 'Enfold Privacy And Cookies', 'avia_framework' ) .'</a>.';

$avia_elements[] = array(
			'slug'		=> 'cookie',
			'name'		=> __( 'Select use of the message bar', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'cookie_message_bar_only',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'required'	=> array( 'cookie_show_advanced_options', '{contains_array}cookie_show_advanced_options' ),
			'subtype'	=> array(
								__( 'Display message bar and use cookie logic', 'avia_framework' )			=> '',
								__( 'Use as a simple message bar without cookie logic', 'avia_framework' )	=> 'cookie_message_bar_only bottom',
							)
		);

//
//if( current_theme_supports( 'avia_gdpr_permanent_hide_message_bar' ) )
//{
//	$avia_elements[] = array(
//				'slug'	=> 'cookie',
//				'name'	=> __( 'Hide Message Bar permanently', 'avia_framework' ),
//				'desc'	=> __( 'Select if you want to use the implemented cookie logic and allow visitors to opt in or opt out of cookies and services but only want to provide a custom settings page.', 'avia_framework' ),
//				'id'	=> 'cookie_consent_no_bar',
//				'type'	=> 'select',
//				'std'	=> '',
//				'no_first'	=> true,
//				'subtype'	=> array(
//									__( 'Display message bar', 'avia_framework' )		=> '',
//									__( 'Hide Message Bar permanently', 'avia_framework' )	=> 'cookie_consent_no_bar',
//								)
//			);
//
//	$requ_cookie_consent_no_bar = array( 'cookie_consent_no_bar', '' );
//}


$avia_elements[] = array(
			'slug'		=> 'cookie',
			'name'		=> __( 'Show reopen badge', 'avia_framework' ),
			'desc'		=> __( 'Select to show a badge to reopen the message bar', 'avia_framework' ),
			'id'		=> 'cookie_consent_badge',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
//			'required'	=> array( 'cookie_consent_no_bar', '' ),
			'subtype'	=> array(
								__( 'Disable badge', 'avia_framework' )		=> '',
								__( 'Show badge at the bottom left of the screen', 'avia_framework' )	=> 'left bottom',
								__( 'Show badge at the bottom right of the screen', 'avia_framework' )	=> 'right bottom'
							)
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_14',
			'nodescription' => true,
			'required'		=> array( 'cookie_message_bar_only', '' )
		);

$desc  = __( 'Select to force a reload of the page when user clicks the &quot;Accept Settings....&quot; or &quot;Do not accept....&quot; button. If you do not use external services a page reload is usually not necessary.', 'avia_framework' );

$avia_elements[] = array(
			'slug'		=> 'cookie',
			'name'		=> __( 'Auto Reload Page', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'cookie_auto_reload',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'No auto reload required', 'avia_framework' )								=> '',
								__( 'Auto reload on &quot;Accept Settings ...&quot; only', 'avia_framework' )	=> 'reload_accept',
								__( 'Auto reload on &quot;Do not accept ...&quot; only', 'avia_framework' )		=> 'reload_no_accept',
								__( 'Auto reload on both buttons', 'avia_framework' )							=> 'reload_both'
							)
		);

$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name' 	=> __( 'Refuse Cookie Warning', 'avia_framework' ),
			'desc' 	=> __( 'Provide a short message for a browser alert when user clicks the &quot;Do not accept and hide notification&quot; button. Inform him that refusing cookies will show the message bar every time he opens a new window or tab. Leave empty if you do not want to show the browser alert.', 'avia_framework' ),
			'id' 	=> 'cookie_refuse_button_alert',
			'type' 	=> 'textarea',
			'std'   => __( 'When refusing all cookies this site might not be able to work as expected. Please check our settings page and opt out for cookies or functions you do not want to use and accept cookies. You will be shown this message every time you open a new window or a new tab.\n\nAre you sure you want to continue?', 'avia_framework' )
		);


$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_cookie_group_start_15',
			'nodescription'	=> true,
		);



$desc  = __( 'Define additional custom cookies set by plugins. There are browser security limitations and it might not be possible to remove them using JavaScript or PHP.', 'avia_framework' ) . ' ';
$desc .= __( 'Cookies must be in the same domain and you need to specify the name and the path (case sensitive) that is shown in the developer tools of your browser. Please see <a href="https://kriesi.at/documentation/enfold/privacy-cookies/#additional-custom-cookies">Additional Custom Cookies</a> on our documentation.', 'avia_framework' );
$desc .= '<br /><br />';
$desc .= __( 'To add a toggle for that cookie use the following shortcodes:', 'avia_framework' );
$desc .= '<br /><br />';
$desc .= '<strong>' . __( '[av_privacy_custom_cookie cookie_name=""]', 'avia_framework' ) . '</strong>';
$desc .= '<br />';
$desc .= '<strong>' . __( '[av_privacy_custom_cookie cookie_name=""]Your text here[/av_privacy_custom_cookie] ', 'avia_framework' ) . '</strong>';

$avia_elements[] = array(
			'slug'	=> 'cookie',
			'name'	=> __( 'Additional Custom Cookies', 'avia_framework' ),
			'desc'	=> $desc,
			'id'	=> 'cookie_custom_cookies_headline',
			'type'	=> 'heading',
			'std'	=> '',
			'nodescription'	=> true
		);

$avia_elements[] =	array(
			'slug'			=> 'cookie',
			'id'			=> 'custom_cookies',
			'type'			=> 'group',
			'linktext'		=> '+',
			'deletetext'	=> '×',
			'blank'			=> true,
			'nodescription'	=> true,
			'std'			=> array(
									array(
										'cookie_name'			=> '',
										'cookie_path'			=> '',
										'cookie_content'		=> '',
										'cookie_info_desc'		=> '',
										'cookie_compare_action'	=> ''
									)
								),
			'subelements'	=> array(
									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Cookie Name', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'cookie_name',
										'type' 	=> 'text',
										'std'	=> '',
										'class' => 'av_2columns av_col_1'
										),
									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Cookie Path', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'cookie_path',
										'type' 	=> 'text',
										'std'	=> '',
										'class' => 'av_2columns av_col_2'
										),
									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Description For Toggle', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'cookie_content',
										'type' 	=> 'text',
										'std'	=> '',
										'class' => 'av_2columns av_col_1'
										),
									array(
										'slug'	=> 'cookie',
										'name' 	=> __( 'Description For Cookie Info List', 'avia_framework' ),
										'desc' 	=> '',
										'id' 	=> 'cookie_info_desc',
										'type' 	=> 'text',
										'std'	=> '',
										'class' => 'av_2columns av_col_2'
										),
									array(
										'slug'	=> 'cookie',
										'name'	=> __( 'Compare Action', 'avia_framework' ),
										'desc'	=> __( 'Select your compare action if you have to remove multiple cookies. Be carefull with contains as this removes all cookies that contain the string (except the disabled cookie).', 'avia_framework' ),
										'id'	=> 'cookie_compare_action',
										'type'	=> 'select',
										'std'	=> '',
										'no_first'	=> true,
										'subtype'	=> array(
														__( 'Cookie equals cookie name', 'avia_framework' )			=> '',
														__( 'Cookie starts with cookie name', 'avia_framework' )	=> 'starts_with',
														__( 'Cookie contains cookie name', 'avia_framework' )		=> 'contains'
													)
										)
								)
		);


$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_15',
			'nodescription'	=> true,
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_14',
			'nodescription' => true,
			'required'		=> array( 'cookie_message_bar_only', '' )
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_12',
			'nodescription'	=> true,
			'required'		=> array( 'cookie_show_advanced_options', '{contains_array}cookie_show_advanced_options' )
		);

$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_11',
			'nodescription'	=> true,
			'required'		=> array( 'cookie_consent', '{contains_array}cookie_consent' )
		);


//END TAB
$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_4',
			'nodescription' => true
		);

//END TAB CONTAINER
$avia_elements[] = array(
			'slug'			=> 'cookie',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_cookie_group_end_1',
			'nodescription' => true
		);
