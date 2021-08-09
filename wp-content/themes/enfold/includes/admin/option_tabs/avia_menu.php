<?php
/**
 * Main Menu Tab
 * =============
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;


$frontendheader_label = __( 'A rough layout preview of the main menu', 'avia_framework' );

$avia_elements[] =	array(
			'slug'	=> 'menu',
			'id' 	=> 'main_menu_preview',
			'type' 	=> 'target',
			'std' 	=> "
					<style type='text/css'>

					#avia_options_page #avia_main_menu_preview{background: #f8f8f8; padding: 30px;border-bottom: 1px solid #e5e5e5; margin-bottom: 25px;}
					#av-main-menu-preview-container{color:#999; border:1px solid #e1e1e1; padding:0px 45px; overflow:hidden; background-color:#fff; position: relative;}

					#avia_options_page #pr-main-area{line-height:69px; overflow:hidden;}

					.main-menu-wrap{float:right; height:70px; line-height:70px;}


					[data-av_set_global_tab_active='av_display_burger'] .av-header-area-preview-menu-only #av-menu-overlay{display:block;}
					[data-av_set_global_tab_active='av_display_burger'] .av-header-area-preview-menu-only #pr-burger-menu{display:block;}
					[data-av_set_global_tab_active='av_display_burger'] #pr-menu #pr-menu-inner{display:none;}


					#av-menu-overlay{position: absolute; left:31px; display:none; bottom: 31px; top: 54px; right: 31px; background: rgba(0,0,0,0.2); z-index: 1;}
					#av-menu-overlay .av-overlay-menu-item{display:block; padding:8px 20px; border-bottom: 1px solid #e1e1e1;}
					#av-menu-overlay .av-overlay-menu-item-sub{display:block; color:#999;}
					#av-menu-overlay-scroll{position:absolute; top:0; right:0; bottom:0; width:280px; background:#fff; padding-top:70px; color:#666;}
					[data-submenu_visibility*='av-submenu-hidden'] #av-menu-overlay .av-overlay-menu-item-sub{display:none;}
					[data-burger_size*='av-small-burger-icon'] #pr-burger-menu{    -ms-transform: scale(0.6); transform: scale(0.6);}


					[data-overlay_style='av-overlay-full'] #av-menu-overlay-scroll{background:transparent; color:#fff; width:100%; text-align: center;}
					[data-overlay_style='av-overlay-full'] #av-menu-overlay .av-overlay-menu-item{border:none; font-size:16px;}
					[data-overlay_style='av-overlay-full'] #av-menu-overlay{ background: rgba(0,0,0,0.8);}
					[data-av_set_global_tab_active='av_display_burger'] [data-overlay_style='av-overlay-full'] #pr-burger-menu span{border-color:#fff;}


					[data-overlay_style*='av-overlay-side-minimal'] #av-menu-overlay-scroll{display:table; height:100%;padding:0;}
					[data-overlay_style*='av-overlay-side-minimal'] #av-menu-overlay-scroll > *{display:table-cell; height:100%; vertical-align:middle;}
					[data-overlay_style*='av-overlay-side-minimal'] #av-menu-overlay .av-overlay-menu-item{border:none;}

					</style>
					<div class='av-header-area-preview av-header-area-preview-menu-only' >

						<div id='av-menu-overlay'>
							<div id='av-menu-overlay-scroll'>
									<div id='av-menu-overlay-scroll-inner'>
									<span class='av-overlay-menu-item'>Home</span>
									<span class='av-overlay-menu-item'>About</span>
									<span class='av-overlay-menu-item av-overlay-menu-item-sub'>- Team</span>
									<span class='av-overlay-menu-item av-overlay-menu-item-sub'>- History</span>
									<span class='av-overlay-menu-item'>Contact</span>
								</div>
							</div>
						</div>

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


													<span id='pr-menu-inner'><span class='pr-menu-single pr-menu-single-first'>Home</span><span class='pr-menu-single'>About</span><span class='pr-menu-single'>Contact</span></span> <img id='search_icon' src='" . AVIA_BASE_URL . "images/layout/search.png'  alt='' />
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
							<div id='pr-content-area'> Content / Slideshows / etc
							<div class='inner-content'><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. </p>

							<p>Donec quam felis, ultricies nec, pellentesque eu, pretium sem.Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium sem.</p>

							<p>Donec quam felis, ultricies nec, pellentesque eu, pretium sem.Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium sem.</p>

							</div>
							</div>
						</div>
					</div>
					",
			'nodescription' => true
		);

//START TAB CONTAINER
$avia_elements[] = array(
			'slug'			=> 'menu',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_menu_container_start',
			'class'			=> 'avia_tab_container avia_set',
			'nodescription'	=> true
		);


// START TAB
$avia_elements[] = array(
			'slug'			=> 'menu',
			'name'			=> __( 'General', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_menu_tab1_start',
			'class'			=> 'avia_tab avia_tab2',
			'nodescription'	=> true
		);


$avia_elements[] = array(
			'slug'		=> 'menu',
			'name'		=> __( 'Menu Items for Desktop', 'avia_framework' ),
			'desc'		=> __( 'Choose how you want to display the menu items on desktop computers. If you choose to display the &quot;burger&quot; icon on desktop computers it will also be used on tablets and mobile devices ', 'avia_framework' ),
			'id'		=> 'menu_display',
			'type'		=> 'select',
			'std'		=> '',
//			'required'	=> array( 'header_layout', '{contains}main_nav_header' ),
			'target'	=> array( '.av-header-area-preview::#pr-menu::set_class' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Display as text', 'avia_framework' )	=> '',
								__( 'Display as icon', 'avia_framework' )	=> 'burger_menu',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'menu',
			'name'		=> __( 'Alternate Menu for Mobile', 'avia_framework' ),
			'desc'		=> __( 'Choose if you want to display the alternate menu on mobile devices.', 'avia_framework' ),
			'id'		=> 'alternate_menu',
			'type'		=> 'select_menu',
			'std'		=> '',
			'required'	=> array( 'menu_display', '' ),
			'no_first'	=> true,
			'subtype'	=> array(
							__( 'Do not use an alternate menu for mobile', 'avia_framework' )	=> ''
						)
			);

$avia_elements[] = array(
			'slug'		=> 'menu',
			'name'		=> __( 'Menu Items for mobile', 'avia_framework' ),
			'desc'		=> __( "The mobile menu is usually displayed on Smartphone screensize only. If you have a lot of main menu items you might want to activate it for tablet screen size as well so it doesn't overlap the logo on tablets or small screens", 'avia_framework' ),
			'id'		=> 'header_mobile_activation',
			'type'		=> 'select',
			'std'		=> 'mobile_menu_phone',
			'required'	=> array( 'menu_display', '' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Activate only for Smartphones (browser width below 768px)', 'avia_framework' )			=> 'mobile_menu_phone',
								__( 'Activate for Smartphones and Tablets (browser width below 990px)', 'avia_framework' )	=> 'mobile_menu_tablet',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'menu',
			'name'		=> __( 'Separator between menu items', 'avia_framework' ),
			'desc'		=> __( 'Choose if you want to display a border between menu items', 'avia_framework' ),
			'id'		=> 'header_menu_border',
			'type'		=> 'select',
			'std'		=> '',
			'target'	=> array( '.av-header-area-preview::#pr-menu-inner::set_class' ),
			'no_first'	=> true,
			'required'	=> array( 'menu_display', '' ),
			'subtype'	=> array( 
								__( 'No separator', 'avia_framework' )  => '',
								__( 'Small separator', 'avia_framework' ) => 'seperator_small_border',
								__( 'Large separator', 'avia_framework' ) => 'seperator_big_border',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'menu',
			'name'		=> __( 'Append search icon to main menu', 'avia_framework' ),
			'desc'		=> __( 'If enabled a search Icon will be appended to the main menu that allows the users to perform an &quot;AJAX&quot; Search', 'avia_framework' ),
			'id'		=> 'header_searchicon',
			'type'		=> 'checkbox',
			'std'		=> 'true',
			'target'	=> array( '.av-header-area-preview::#search_icon::set_class' )
		);

// END TAB
$avia_elements[] = array(
			'slug'			=> 'menu',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_menu_tab1_end',
			'nodescription' => true
		);


// START TAB
$avia_elements[] = array(
			'slug'			=> 'menu',
			'name'			=> __( 'Burger/Mobile Menu', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_menu_tab2_start',
			'class'			=> 'avia_tab avia_tab2',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'		=> 'menu',
			'name'		=> __( 'Menu Icon Submenu items', 'avia_framework' ),
			'desc'		=> __( 'Choose how to display the submenu items of the icon menu', 'avia_framework' ),
			'id'		=> 'submenu_visibility',
			'type'		=> 'select',
			'std'		=> '',
			'target'	=> array( '#avia_main_menu_preview::.avia_control_container::set_data' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Always display submenu items', 'avia_framework' )		=> '',
								__( 'Display submenu items on click', 'avia_framework' )	=> 'av-submenu-hidden av-submenu-display-click',
								__( 'Display submenu items on hover', 'avia_framework' )	=> 'av-submenu-hidden av-submenu-display-hover',
							)
		);


$avia_elements[] = array(
			'slug'		=> 'menu',
			'name'		=> __( 'Clone title menu items to submenu', 'avia_framework' ),
			'desc'		=> __( 'Since you selected to display submenu items on click or on hover, the parent menu item does no longer navigate to the URL it contains, but toggles the visibility of its submenu items. If you want users to be able to open the parent menu URL the theme can create a clone of that item in the submenu', 'avia_framework' ),
			'id'		=> 'submenu_clone',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=>	true,
			'required'	=> array( 'submenu_visibility', '{contains_array}av-submenu-display-click;av-submenu-display-hover' ),
			'subtype'	=> array( 
								__( 'Do not create a clone', 'avia_framework' )						=> 'av-submenu-noclone',
								__( 'Create a clone for the title menu item', 'avia_framework' )	=> 'av-submenu-clone',
							)
		);


$avia_elements[] = array(
			'slug'		=> 'menu',
			'name'		=> __( 'Menu Icon Style', 'avia_framework' ),
			'desc'		=> __( 'Set the style of the &quot;Burger&quot; Icon', 'avia_framework' ),
			'id'		=> 'burger_size',
			'type'		=> 'select',
			'std'		=> '',
			'target'	=> array( '.av-header-area-preview::#pr-stretch-wrap::set_data' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Default', 'avia_framework' )	=> '',
								__( 'Small', 'avia_framework' )		=> 'av-small-burger-icon',
							)
		);


$avia_elements[] = array(
			'slug'		=> 'menu',
			'name'		=> __( 'Menu Overlay Style', 'avia_framework' ),
			'desc'		=> __( 'Set the style of the page overlay that appears when the burger menu is clicked', 'avia_framework' ),
			'id'		=> 'overlay_style',
			'type'		=> 'select',
			'std'		=> 'av-overlay-side av-overlay-side-classic',
			'target'	=> array( '#avia_main_menu_preview::.avia_control_container::set_data' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Full Page Overlay Menu', 'avia_framework' )		=> 'av-overlay-full',
								__( 'Sidebar Flyout Menu (Classic)', 'avia_framework' )	=> 'av-overlay-side av-overlay-side-classic',
								__( 'Sidebar Flyout Menu (Minimal)', 'avia_framework' )	=> 'av-overlay-side av-overlay-side-minimal',
							)
		);

// END TAB
$avia_elements[] = array(
			'slug'			=> 'menu',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_menu_tab2_end',
			'nodescription' => true
		);



// START TAB
$avia_elements[] = array(
			'slug'			=> 'menu',
			'name'			=> __( 'Burger/Mobile Menu styling', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_menu_tab3_start',
			'class'			=> 'avia_tab avia_tab2',
			'nodescription'	=> true
		);


$avia_elements[] = array(
			'slug'	=> 'menu',
			'name' 	=> __( 'Menu Icon Color', 'avia_framework' ),
			'desc' 	=> __( 'Set a custom color of the &quot;Burger&quot; Icon. Leave empty to use the default menu color', 'avia_framework' ),
			'id' 	=> 'burger_color',
			'type' 	=> 'colorpicker',
			'std' 	=> '',
			'class' => '',
		);

$avia_elements[] = array(
			'slug'	=> 'menu',
			'name' 	=> __( 'Flyout width', 'avia_framework' ),
			'desc' 	=> __( 'Set a custom width for the Flyout. Pixel and % values are allowed. Eg: 350px or 70%', 'avia_framework' ),
			'id' 	=> 'burger_flyout_width',
			'type' 	=> 'text',
			'std' 	=> '350px'
		);

$avia_elements[] = array(	
			'slug'	=> 'menu',
			'name'	=> __( 'Advanced color and styling options', 'avia_framework' ),
			'desc'	=> __( 'You can edit more and advanced color and styling options for the overlay/slideout menu items in' ) .
							" <a href='#goto_customizer'>" .
							__( 'Advanced Styling', 'avia_framework' ) .
							'</a>',
			'id'	=> 'menu_overlay_description',
			'type' => 'heading',
			'std'	=> '',
			'nodescription'=>true
		);


// END TAB
$avia_elements[] = array(
			'slug'			=> 'menu',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_menu_tab3_end',
			'nodescription' => true
		);



//END TAB CONTAINER
$avia_elements[] = array(
			'slug'			=> 'menu',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_menu_container_end',
			'nodescription' => true
		);
