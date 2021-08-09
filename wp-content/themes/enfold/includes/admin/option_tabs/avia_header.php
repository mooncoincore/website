<?php
/**
 * Header Tab - Header Layout Settings
 * ===================================
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;



$avia_elements[] = array(	
			'slug'			=> 'header',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_header_conditional_start',
			'nodescription' => true,
			'required'		=> array( 'header_position', '{contains}header_top' ),
			'inactive'		=> __( 'These options are only available if you select a layout that has a main menu positioned at the top. You currently have your main menu placed in a sidebar', 'avia_framework' ) . '<br/><br/>' .
									__( 'You can change that setting', 'avia_framework' ) .
									" <a href='#goto_layout'>" .
									__( 'at General Layout', 'avia_framework' ) .
									'</a>'
		);


$frontendheader_label = __( 'A rough layout preview of the header area', 'avia_framework' );

$avia_elements[] =	array(
			'slug'	=> 'header',
			'id' 	=> 'default_header_target',
			'type' 	=> 'target',
			'std' 	=> "
					<style type='text/css'>

					#avia_options_page #avia_default_header_target{background: #f8f8f8;border: none;padding: 30px;border-bottom: 1px solid #e5e5e5; margin-bottom: 25px;}
					#avia_header_preview{color:#999; border:1px solid #e1e1e1; padding:0px 45px; overflow:hidden; background-color:#fff; position: relative;}

					#avia_options_page #pr-main-area{line-height:69px; overflow:hidden;}
					#pr-menu{float:right; font-size:12px; line-height: inherit;}

					#pr-menu .pr-menu-single{display:inline-block; padding:0px 7px; position:relative; }
					#pr-menu .main_nav_header .pr-menu-single{padding:20px 7px;}

					#pr-menu-inner.seperator_small_border .pr-menu-single{display:inline; border-right: 1px solid #e1e1e1; padding:0px 7px;}
					#pr-menu-inner.seperator_big_border .pr-menu-single{ border-right: 1px solid #e1e1e1; width: 80px; text-align: center; padding: 25px 7px;}
					#pr-menu-inner.seperator_big_border .pr-menu-single-first{border-left:1px solid #e1e1e1;}


					.bottom_nav_header #pr-menu-inner.seperator_big_border .pr-menu-single{padding: 9px 7px;}

					#pr-logo{ max-width: 150px; max-height: 70px; float:left;}
					#avia_header_preview.large #pr-logo{ max-width: 215px; max-height: 115px; padding-top:0px;}
					#avia_header_preview.large .main_nav_header #pr-menu-inner.seperator_big_border .pr-menu-single{padding: 48px 7px;}
					#avia_options_page #avia_header_preview.large #pr-main-area{line-height:15px;}

					#search_icon{opacity:0.3; margin-left: 10px; top:26px; position:relative; display:none; z-index:10; height:16px;}
					#search_icon.header_searchicon{display:inline; top:4px;}
					#pr-content-area{display:block; clear:both; padding:15px 45px; overflow:hidden; background-color:#fcfcfc; text-align:center; border:1px solid #e1e1e1; border-top:none;}
					.logo_right #pr-logo{float:right}
					.logo_center{text-align:center;}
					.logo_center #pr-logo{float:none}
					.menu_left #pr-menu{float:left}
					#avia_options_page .bottom_nav_header#pr-main-area{line-height: 1em;}
					.bottom_nav_header #pr-menu{float:none; clear:both; line-height:36px; }
					.top_nav_header div#pr-menu { position: absolute; top: -1px; width: 100%; left: 0; }
					.top_nav_header#pr-main-area{margin-top:40px;}
					.bottom_nav_header #pr-menu:before { content: ''; border-top: 1px solid #e1e1e1; width: 150%; position:absolute; height: 1px; left: -50px;}
					.top_nav_header #pr-menu:before{ top: 36px; }
					.minimal_header .top_nav_header #pr-menu:before{opacity:0;}
					.minimal_header_shadow .top_nav_header #pr-menu:before{opacity:1; box-shadow: 0 1px 3px 0px rgba(0,0,0,0.1); }


					#pr-menu-2nd{height: 28px; color:#aaa; border:1px solid #e1e1e1; padding:5px 45px; overflow:hidden; background-color:#f8f8f8; border-bottom:none; display:none; font-size:11px;}
					.extra_header_active #pr-menu-2nd{display:block;}
					.pr-secondary-items{display:none;}
					.secondary_left .pr-secondary-items, .secondary_right .pr-secondary-items{display:block; float:left; margin:0 10px 0 0;}
					.secondary_right .pr-secondary-items{float:right; margin:0 0 0 10px;}

					.pr-icons{opacity:0.3; display:none; position:relative; top:1px;}
					.icon_active_left.extra_header_active #pr-menu-2nd .pr-icons{display:block; float:left; margin:0 10px 0 0;}
					.icon_active_right.extra_header_active #pr-menu-2nd .pr-icons{display:block; float:right; margin:0 0 0 10px ;}

					.icon_active_main #pr-main-icon{float:right; position:relative; line-height:inherit;}
					.icon_active_main #pr-main-icon .pr-icons{display:block; top: 3px; margin: 0 0 0 17px; line-height:inherit; width:66px;}
					.icon_active_main .logo_right #pr-main-icon {left: 211px; float: left; width: 0px;}
					.icon_active_main .logo_right #pr-main-icon {left: 211px; float: left; width: 0px;}
					.icon_active_main .large .logo_right #pr-main-icon {left:-55px;}

					.icon_active_main .bottom_nav_header #pr-main-icon{top:23px;}
					.icon_active_main .large #pr-main-icon{top:46px;}

					.icon_active_main .logo_right.bottom_nav_header #pr-main-icon{float:left; left:-17px;}
					.icon_active_main .logo_center.bottom_nav_header #pr-main-icon{float: right; top: 0px; position: absolute; right: 24px;}
					.icon_active_main .large .logo_center.bottom_nav_header #pr-main-icon{top: 29px;}
					.icon_active_main .logo_center.bottom_nav_header #pr-main-icon .pr-icons{margin:0; top:35px;}
					.icon_active_main .large .logo_center.bottom_nav_header #pr-main-icon .pr-icons { top: 23px; }

					.pr-phone-items{display:none;}
					.phone_active_left  .pr-phone-items{display:block; float:left;}
					.phone_active_right .pr-phone-items{display:block; float:right;}

					.header_stretch #avia_header_preview, .header_stretch #pr-menu-2nd{ padding-left: 15px; padding-right: 15px; }
					.header_stretch .icon_active_main .logo_right.menu_left #pr-main-icon {left:-193px;}

					.inner-content{color:#999; text-align: justify; }

					#pr-breadcrumb{line-height:23px; color:#aaa; border:1px solid #e1e1e1; padding:5px 45px; overflow:hidden; background-color:#f8f8f8; border-top:none; font-size:16px;}
					#pr-breadcrumb .some-breadcrumb{float:right; font-size:11px; line-height:23px;}
					#pr-breadcrumb.title_bar .some-breadcrumb, #pr-breadcrumb.hidden_title_bar{ display:none; }

					.pr-menu-single.pr-menu-single-first:after {
					content: '';
					width: 90%;
					height: 1px;
					border-bottom: 2px solid #9cc2df;
					display: block;
					top: 85%;
					left: 7%;
					position: absolute;
					}

					.burger_menu #pr-menu-inner{
						display:none;
					}

					#pr-burger-menu{
						    display: none;
						    height: 40px;
						    width: 30px;
						    margin-top: 17px;
						    margin-left:20px;
						    float: right;
						    position: relative;
						    z-index:10;
					}

					#avia_header_preview.large #pr-burger-menu{margin-top: 39px;}

					#pr-burger-menu span{
						display:block;
						border-top:4px solid #aaa;
						margin-top: 6px;
					}

					.main_nav_header .burger_menu #pr-burger-menu{
						display:block;
					}

					.seperator_small_border .pr-menu-single.pr-menu-single-first:after { top: 145%; }
					.seperator_big_border .pr-menu-single.pr-menu-single-first:after { top: 98%; left: 0; width: 100%;}
					.bottom_nav_header .pr-menu-single.pr-menu-single-first:after { top: 92%; left: 0%; width:100%; }

					.minimal_header .pr-menu-single.pr-menu-single-first:after{display:none;}
					.minimal_header #avia_header_preview{border-bottom:none;}
					.minimal_header_shadow #avia_header_preview { box-shadow: 0 2px 8px 0px rgba(0,0,0,0.1); }

					.bottom_nav_header #search_icon.header_searchicon{float:right; top: 10px;}
					.burger_menu #pr-burger-menu{display:block;}
					#avia_header_preview .bottom_nav_header #pr-burger-menu{ margin:0; float:left; }
					.top_nav_header #search_icon, .top_nav_header #pr-burger-menu{margin:0px 10px;}

					</style>
					<div class='av-header-area-preview' >
						<div id='pr-stretch-wrap' >
							<small class='live_bg_small'>{$frontendheader_label}</small>
							<div id='pr-header-style-wrap' >
								<div id='pr-phone-wrap' >
									<div id='pr-social-wrap' >
										<div id='pr-seconary-menu-wrap' >
											<div id='pr-menu-2nd'>{$iconSpan}<span class='pr-secondary-items'>Login | Signup | etc</span><span class='pr-phone-items'>Phone: 555-4432</span></div>
											<div id='avia_header_preview' >
												<div id='pr-main-area' >
													<img id='pr-logo' src='" . AVIA_BASE_URL . "images/layout/logo_modern.png' alt=''/>
													<div id='pr-main-icon'>{$iconSpan}</div>
													<div id='pr-menu'>


													<span id='pr-menu-inner'><span class='pr-menu-single pr-menu-single-first'>Home</span><span class='pr-menu-single'>About</span><span class='pr-menu-single'>Contact</span></span> <img id='search_icon' src='".AVIA_BASE_URL."images/layout/search.png'  alt='' />
													<div id='pr-burger-menu'>
														<span class='burger-top'></span>
														<span class='burger-mid'></span>
														<span class='burger-low'></span>
													</div>

													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id='pr-breadcrumb'>Some Title <span class='some-breadcrumb'>Home  &#187; Admin  &#187; Header </span></div>
							<div id='pr-content-area'> Content / Slideshows / etc
							<div class='inner-content'>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium sem.</div>
							</div>
						</div>
					</div>
					",
			'nodescription' => true
		);

//START TAB CONTAINER
$avia_elements[] = array(
			'slug'			=> 'header',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_header_tab1_start',
			'class'			=> 'avia_tab_container avia_set',
			'nodescription'	=> true
		);

// START TAB
$avia_elements[] = array(
			'slug'			=> 'header',
			'name'			=> __( 'Header layout', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_header_tab2_start',
			'class'			=> 'avia_tab avia_tab2',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Menu and Logo Position', 'avia_framework' ),
			'desc'		=> __( 'You can choose various different logo and main menu positions here', 'avia_framework' ),
			'id'		=> 'header_layout',
			'type'		=> 'select',
			'std'		=> '',
			'class'		=> 'av_2columns av_col_1',
			'no_first'	=> true,
			'target'	=> array( '.av-header-area-preview::#pr-main-area::set_class' ),
			'subtype'	=> array( 
								__( 'Logo left, Menu right', 'avia_framework' )  	=> 'logo_left main_nav_header menu_right',
								__( 'Logo right, Menu Left', 'avia_framework' )	 	=> 'logo_right main_nav_header menu_left',
								__( 'Logo left, Menu below', 'avia_framework' ) 	=> 'logo_left bottom_nav_header menu_left',
								__( 'Logo right, Menu below', 'avia_framework' ) 	=> 'logo_right bottom_nav_header menu_center',
								__( 'Logo center, Menu below', 'avia_framework' ) 	=> 'logo_center bottom_nav_header menu_right',
								__( 'Logo center, Menu above', 'avia_framework' ) 	=> 'logo_center bottom_nav_header top_nav_header menu_center',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Header Size', 'avia_framework' ),
			'desc'		=> __( 'Choose a predefined header size. You can also apply a custom height to the header', 'avia_framework' ),
			'id'		=> 'header_size',
			'type'		=> 'select',
			'std'		=> '',
			'class'		=> 'av_2columns av_col_2',
			'target'	=> array( '.av-header-area-preview::#avia_header_preview::set_class' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'slim', 'avia_framework' )  				=> 'slim',
								__( 'large', 'avia_framework' )					=> 'large',
								__( 'custom pixel value', 'avia_framework' ) 	=> 'custom',
							)
		);


$customsize = array();
for( $x = 45; $x <= 300; $x++ )
{ 
	$customsize[ $x . 'px' ] = $x;
}

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Header Custom Height', 'avia_framework' ),
			'desc'		=> __( 'Choose a custom height in pixels (wont be reflected in the preview above, only on your actual page)', 'avia_framework' ),
			'id'		=> 'header_custom_size',
			'type'		=> 'select',
			'std'		=> '150',
			'required'	=> array( 'header_size', 'custom' ),
			'no_first'	=> true,
			'subtype'	=> $customsize
		);


$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Header Style', 'avia_framework' ),
			'desc'		=> __( 'Choose which header style you want to use', 'avia_framework' ),
			'id'		=> 'header_style',
			'type'		=> 'select',
			'std'		=> '',
			'target'	=> array( '.av-header-area-preview::#pr-header-style-wrap::set_class' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Default (with borders, active menu indicator and slightly transparent)', 'avia_framework' )	=> '',
								__( 'Minimal (no borders, indicators or transparency)', 'avia_framework' )							=> 'minimal_header',
								__( 'Minimal with drop shadow (no borders, indicators or transparency)', 'avia_framework' )			=> 'minimal_header minimal_header_shadow',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Header Title and Breadcrumbs', 'avia_framework' ),
			'desc'		=> __( 'Choose if and how you want to display the Title and Breadcrumb of your page. This option can be overwritten when writing/editing a page', 'avia_framework' ),
			'id'		=> 'header_title_bar',
			'type'		=> 'select',
			'std'		=> 'title_bar_breadcrumb',
			'target'	=> array( '.av-header-area-preview::#pr-breadcrumb::set_class' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Display title and breadcrumbs', 'avia_framework' )	=> 'title_bar_breadcrumb',
								__( 'Display only title', 'avia_framework' )			=> 'title_bar',
								__( 'Display only breadcrumbs', 'avia_framework' )		=> 'breadcrumbs_only',
								__( 'Hide both', 'avia_framework' )						=> 'hidden_title_bar',
							)
		);


// END TAB
$avia_elements[] = array(
			'slug'			=> 'header',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_header_tab2_end',
			'nodescription' => true
		);



// START TAB
$avia_elements[] = array(
			'slug'			=> 'header',
			'name'			=> __( 'Header Behavior', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_header_tab3_start',
			'class'			=> 'avia_tab avia_tab2',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'	=> 'header',
			'name' 	=> __( 'Sticky Header', 'avia_framework' ),
			'desc' 	=> __( 'If checked the header will stick to the top of your site if user scrolls down (ignored on smartphones)', 'avia_framework' ),
			'id' 	=> 'header_sticky',
			'type' 	=> 'checkbox',
			'std'	=> 'true'
		);

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Shrinking Header', 'avia_framework' ),
			'desc'		=> __( 'If checked the sticky header will shrink once the user scrolls down (ignored on smartphones + tablets)', 'avia_framework' ),
			'id'		=> 'header_shrinking',
			'type'		=> 'checkbox',
			'std'		=> 'true',
			'required'	=> array( 'header_sticky', 'header_sticky' ),
		);

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Unstick topbar', 'avia_framework' ),
			'desc'		=> __( 'If checked the small top bar above the header with social icons, secondary menu and extra information will no longer stick to the top', 'avia_framework' ),
			'id'		=> 'header_unstick_top',
			'type'		=> 'checkbox',
			'std'		=> '',
			'required'	=> array( 'header_sticky', 'header_sticky' ),
		);


$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Let logo and menu position adapt to browser window', 'avia_framework' ),
			'desc'		=> __( 'If checked the elements in your header will always be placed at the browser window edge, instead of matching the content width', 'avia_framework' ),
			'id'		=> 'header_stretch',
			'type'		=> 'checkbox',
			'std'		=> '',
			'target'	=> array( '.av-header-area-preview::#pr-stretch-wrap::set_class' ),
			);

// END TAB
$avia_elements[] = array(
			'slug'			=> 'header',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_header_tab3_end',
			'nodescription' => true
		);


// START TAB
$avia_elements[] = array(
			'slug'			=> 'header',
			'name'			=> __( 'Extra Elements', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_header_tab4_start',
			'class'			=> 'avia_tab avia_tab2',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Header Social Icons', 'avia_framework' ),
			'desc'		=> __( 'Choose if and where to display social icons. You can define the icons at', 'avia_framework' ) .
								" <a href='#goto_social'>" .
								__( 'Social Profiles', 'avia_framework' ) .
								'</a>',
			'id'		=> 'header_social',
			'type'		=> 'select',
			'std'		=> '',
			'class'		=> 'av_2columns av_col_1',
			'target'	=> array( '.av-header-area-preview::#pr-social-wrap::set_class' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'No social Icons', 'avia_framework' )					=> '',
								__( 'Display in top bar at the left', 'avia_framework' )	=> 'icon_active_left extra_header_active',
								__( 'Display in top bar at the right', 'avia_framework' )	=> 'icon_active_right extra_header_active',
								__( 'Display in main header area', 'avia_framework' )		=> 'icon_active_main',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Header Secondary Menu', 'avia_framework' ),
			'desc'		=> __( 'Choose if you want to display a secondary menu and where to display it', 'avia_framework' ),
			'id'		=> 'header_secondary_menu',
			'type'		=> 'select',
			'std'		=> '',
			'class'		=> 'av_2columns av_col_2',
			'target'	=> array( '.av-header-area-preview::#pr-seconary-menu-wrap::set_class' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'No Secondary Menu', 'avia_framework' )							=> '',
								__( 'Secondary Menu in top bar at the left', 'avia_framework' )		=> 'secondary_left extra_header_active',
								__( 'Secondary Menu in top bar at the right', 'avia_framework' )	=> 'secondary_right extra_header_active',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Header Phone Number/Extra Info', 'avia_framework' ),
			'desc'		=> __( 'Choose if you want to display an additional phone number or some extra info in your header', 'avia_framework' ),
			'id'		=> 'header_phone_active',
			'type'		=> 'select',
			'std'		=> '',
			'class'		=> 'av_2columns av_col_1',
			'target'	=> array( '.av-header-area-preview::#pr-phone-wrap::set_class' ),
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'No Phone Number/Extra Info', 'avia_framework' )		=> '',
								__( 'Display in top bar at the left', 'avia_framework' )	=> 'phone_active_left extra_header_active',
								__( 'Display in top bar at the right', 'avia_framework' )	=> 'phone_active_right extra_header_active',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'header',
			'name'		=> __( 'Phone Number or small info text', 'avia_framework' ),
			'desc'		=> __( 'Add the text that should be displayed in your header here', 'avia_framework' ),
			'id'		=> 'phone',
			'type'		=> 'text',
			'std'		=> '',
			'class'		=> 'av_2columns av_col_2',
			'required'	=> array( 'header_phone_active', '{contains}phone_active' )
		);


// END TAB
$avia_elements[] = array(
			'slug'			=> 'header',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_header_tab4_end',
			'nodescription' => true
		);


// START TAB
$avia_elements[] = array(
			'slug'			=> 'header',
			'name'			=> __( 'Transparency Options', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_header_tab5_start',
			'class'			=> 'avia_tab avia_tab2',
			'nodescription'	=> true
		);



$avia_elements[] = array(	
			'slug'			=> 'header',
			'name'			=> __( 'What is header transparency', 'avia_framework' ),
			'desc'			=> __( 'When creating/editing a page you can select to have the header be transparent and display the content (usually a fullwidth slideshow or a fullwidth image) beneath. In those cases you will usually need a different Logo and Main Menu color which can be set here.', 'avia_framework' ) . "<br/><a class='av-modal-image' href='" . get_template_directory_uri() . "/images/framework-helper/header_transparency.jpg'>" . __( '(Show example Screenshot)', 'avia_framework' ) . '</a>',
			'id'			=> 'transparency_description',
			'type'			=> 'heading',
			'std'			=> '',
			'nodescription'	=> true
		);


$avia_elements[] =	array(
			'slug'	=> 'header',
			'name' 	=> __( 'Transparency Logo', 'avia_framework' ),
			'desc' 	=> __( 'Upload a logo image, or enter the URL or ID of an image if its already uploaded. (Leave empty to use the default logo)', 'avia_framework' ),
			'id' 	=> 'header_replacement_logo',
			'type' 	=> 'upload',
			'label'	=> __( 'Use Image as logo', 'avia_framework' )
		);

$avia_elements[] =	array(
			'slug'	=> 'header',
			'name' 	=> __( 'Transparency menu color', 'avia_framework' ),
			'desc' 	=> __( 'Menu color for transparent header (Leave empty to use the default color)', 'avia_framework' ),
			'id' 	=> 'header_replacement_menu',
			'type' 	=> 'colorpicker',
			'std' 	=> ''
		);

// END TAB
$avia_elements[] = array(
			'slug'			=> 'header',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_header_tab5_end',
			'nodescription' => true
		);


//END TAB CONTAINER
$avia_elements[] = array(
			'slug'			=> 'header',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_header_tab1_end',
			'nodescription' => true
		);


// close conditional
$avia_elements[] = array(
			'slug'			=> 'header',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_header_conditional_close',
			'nodescription' => true
		);

