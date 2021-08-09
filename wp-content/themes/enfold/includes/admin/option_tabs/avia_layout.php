<?php
/**
 * General Layout Tab
 * ==================
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;


$frontend_label = __( 'A rough preview of the frontend.', 'avia_framework' );

$avia_elements[] =	array(
			'slug'	=> 'layout',
			'id' 	=> 'default_layout_target',
			'type' 	=> 'target',
			'std' 	=> "
					<style type='text/css'>
						.avprev-layout-container, .avprev-layout-container *{
							-moz-box-sizing: border-box;
							-webkit-box-sizing: border-box;
							box-sizing: border-box;
						}
						#boxed .avprev-layout-container{ padding:0 23px; border:1px solid #e1e1e1; background-color: #555;}
						#av-framed-box .avprev-layout-container{ padding:23px; border:1px solid #e1e1e1; background-color: #555;}
						.avprev-layout-container-inner{border:none; overflow: hidden;}
						.avprev-layout-container-inner{border: 1px solid #e1e1e1; background:#fff;}
						.avprev-layout-content-container{overflow:hidden; margin:0 auto; position:relative;}
						.avprev-layout-container-sizer{margin:0 auto; position:relative; z-index:5;}
						.avprev-layout-content-container .avprev-layout-container-sizer{display:table;}
						.avprev-layout-content-container .avprev-layout-container-sizer .av-cell{display:table-cell; padding: 20px;}
						.avprev-layout-content-container .avprev-layout-container-sizer:after{ background: #F8F8F8; position: absolute; top: 0; left: 99%; width: 100%; height: 100%; content: ''; z-index:1;}
						.avprev-layout-header{border-bottom:1px solid #e1e1e1; padding:20px; overflow: hidden;}
						.avprev-layout-slider{border-bottom:1px solid #e1e1e1; padding:30px 20px; background:#3B740F url('" . AVIA_IMG_URL . "layout/diagonal-bold-light.png') top left repeat; color:#fff;}
						.avprev-layout-content{border-right:1px solid #e1e1e1; width:73%; }
						.avprev-layout-sidebar{border-left:1px solid #e1e1e1; background:#f8f8f8; left:-1px; position:relative; min-height:141px;}
						.avprev-layout-menu-description{float:left;}
						.avprev-layout-menu{float:right; color:#999;}


						#header_right .avprev-layout-header{border-left:1px solid #e1e1e1; width:130px; float:right; border-bottom:none;}
						#header_left .avprev-layout-header{border-right:1px solid #e1e1e1; width:130px; float:left; border-bottom:none;}

						#header_right .avprev-layout-content-container{border-right:1px solid #e1e1e1; right:-1px;}
						#header_left  .avprev-layout-content-container{border-left:1px solid #e1e1e1; left:-1px;}

						#header_left .avprev-layout-menu, #header_right .avprev-layout-menu{float:none; padding-top:23px; clear:both; }
						#header_left .avprev-layout-divider, #header_right .avprev-layout-divider{display:none;}
						#header_left .avprev-layout-menuitem, #header_right .avprev-layout-menuitem{display:block; border-bottom:1px dashed #e1e1e1; padding:3px;}
						#header_left .avprev-layout-menuitem-first, #header_right .avprev-layout-menuitem-first{border-top:1px dashed #e1e1e1;}
						#header_left .avprev-layout-header .avprev-layout-container-sizer, #header_right .avprev-layout-header .avprev-layout-container-sizer{width:100%!important;}


						.avprev-layout-container-widget{display:none; border:1px solid #e1e1e1; padding:7px; font-size:12px; margin-top:5px; text-align:center;}
						.avprev-layout-container-social{margin-top:5px; text-align:center;}
						.av-active .pr-icons{display:block; }

						#header_left .avprev-layout-container-widget.av-active, #header_right .avprev-layout-container-widget.av-active{display:block;}
						#header_left .avprev-layout-container-social.av-active, #header_right .avprev-layout-container-widget.av-social{display:block;}

						#av-framed-box .avprev-layout-container-inner{border:none;}
						#boxed .avprev-layout-container-inner{border:none;}

					</style>

					<small class='live_bg_small'>{$frontend_label}</small>
					<div class='avprev-layout-container'>
						<div class='avprev-layout-container-inner'>
							<div class='avprev-layout-header'>
								<div class='avprev-layout-container-sizer'>
									<strong class='avprev-layout-menu-description'>Logo + Main Menu Area</strong>
									<div class='avprev-layout-menu'>
									<span class='avprev-layout-menuitem avprev-layout-menuitem-first'>Home</span>
									<span class='avprev-layout-divider'>|</span>
									<span class='avprev-layout-menuitem'>About</span>
									<span class='avprev-layout-divider'>|</span>
									<span class='avprev-layout-menuitem'>Contact</span>
									</div>
								</div>

								<div class='avprev-layout-container-social'>
									{$iconSpan}
								</div>

								<div class='avprev-layout-container-widget'>
									<strong>Widgets</strong>
								</div>

							</div>

							<div class='avprev-layout-content-container'>
								<div class='avprev-layout-slider'>
									<strong>Fullwidth Area (eg: Fullwidth Slideshow)</strong>
								</div>

								<div class='avprev-layout-container-sizer'>
									<div class='avprev-layout-content av-cell'><strong>Content Area</strong><p>This is the content area. The content area holds all your blog entries, pages, products etc</p></div>
									<div class='avprev-layout-sidebar av-cell'><strong>Sidebar</strong><p>This area holds all your sidebar widgets</p>
									</div>
								</div>
							</div>

						</div>
					</div>


					",
			'nodescription' => true		
	);

//START TAB CONTAINER
$avia_elements[] = array(
			'slug'			=> 'layout',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_layout_container_start',
			'class'			=> 'avia_tab_container avia_set',
			'nodescription'	=> true
		);


// START TAB
$avia_elements[] = array(
			'slug'			=> 'layout',
			'name'			=> __( 'Layout', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_layout_tab1_start',
			'class'			=> 'avia_tab avia_tab2',
			'nodescription'	=> true
		);



$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Use stretched or boxed layout?', 'avia_framework' ),
			'desc'		=> __( 'The stretched layout expands from the left side of the viewport to the right.', 'avia_framework' ),
			'id'		=> 'color-body_style',
			'type'		=> 'select',
			'std'		=> 'stretched',
			'class'		=> 'av_2columns av_col_1',
			'no_first'	=> true,
			'target'	=> array( 'default_slideshow_target, #avia_default_layout_target::.avia_control_container::set_id' ),
			'subtype'	=> array(	
								__( 'Stretched layout', 'avia_framework' )	=> 'stretched',
								__( 'Boxed Layout', 'avia_framework' )		=> 'boxed',
								__( 'Fixed Frame', 'avia_framework' )		=> 'av-framed-box'
							)
		);

$numbers = array();
for( $i = 1; $i <= 75; $i++ )
{
	$numbers[ $i . 'px' ] = $i;
}

$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Frame Width', 'avia_framework' ),
			'desc'		=> __( 'Modify the frame color by changing the Body Background in', 'avia_framework' ) .
								" <a href='#goto_styling'>" .
								__( 'General Styling', 'avia_framework' ) .
								'</a>',
			'id'		=> 'color-frame_width',
			'type'		=> 'select',
			'std'		=> '20',
			'class'		=> 'av_2columns av_col_2',
			'required' => array( 'color-body_style', '{contains}framed' ),
			'no_first'	=> true,
			'subtype'	=> $numbers
		);

$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Logo and Main Menu', 'avia_framework' ),
			'desc'		=> __( 'You can place your logo and main menu at the top of your site or within a sidebar', 'avia_framework' ),
			'id'		=> 'header_position',
			'type'		=> 'select',
			'std'		=> 'header_top',
			'class'		=> 'av_2columns av_col_2',
			'target'	=> array( 'default_layout_target, #avia_default_slideshow_target::.avprev-layout-container, .avprev-design-container::set_id_single' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Top Header', 'avia_framework' )	=> 'header_top',
								__( 'Left Sidebar', 'avia_framework' )	=> 'header_left header_sidebar',
								__( 'Right Sidebar', 'avia_framework' )	=> 'header_right header_sidebar',
							)
		);


$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Content Alignment', 'avia_framework' ),
			'desc'		=> __( 'If the window width exceeds the maximum content width, where do you want to place your content', 'avia_framework' ),
			'id'		=> 'layout_align_content',
			'type'		=> 'select',
			'std'		=> 'content_align_center',
			'class'		=> 'av_2columns av_col_1',
			'required'	=> array( 'header_position', '{contains}header_sidebar' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Center Content', 'avia_framework' )		=> 'content_align_center',
								__( 'Position at the Left', 'avia_framework' )	=> 'content_align_left',
								__( 'Position at the Right', 'avia_framework' )	=> 'content_align_right',
							)
		);


$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Sticky Sidebar menu', 'avia_framework' ),
			'desc'		=> __( 'You can choose if you want a sticky sidebar that does not scroll with the content', 'avia_framework' ),
			'id'		=> 'sidebarmenu_sticky',
			'type'		=> 'select',
			'std'		=> 'conditional_sticky',
			'class'		=> 'av_2columns av_col_2',
			'required'	=> array( 'header_position', '{contains}header_left' ),
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Sticky if Sidebar is smaller than the screen height, scroll otherwise', 'avia_framework' ) => 'conditional_sticky',
								__( 'Always Sticky', 'avia_framework' )	=> 'always_sticky',
								__( 'Never Sticky', 'avia_framework' )	=> 'never_sticky',
							)
		);

$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Main Menu Sidebar', 'avia_framework' ),
			'desc'		=> __( 'You can choose to use the main menu area to also display widget areas', 'avia_framework' ),
			'id'		=> 'sidebarmenu_widgets',
			'type'		=> 'select_sidebar',
			'std'		=> '',
			'no_first'	=> true,
			'required'	=> array( 'header_position', '{contains}header_sidebar' ),
			'target'	=> array( 'default_layout_target::.avprev-layout-container-widget::set_active' ),
			'exclude'	=> array(), /*eg: 'Displayed Everywhere'*/
			'additions'	=> array( 
								'No widgets'						=> '', 
								/* 'Display Widgets by page logic'	=> 'av-auto-widget-logic', */ 
								'Display a specific Widget Area'	=> '%result%' ),
		);

$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Display social icons below main menu? (You can set your social icons at', 'avia_framework' ) .
							" <a href='#goto_social'>" .
							__( 'Social Profiles', 'avia_framework' ) .
							'</a>)',
			'desc'		=> __( 'Check to display', 'avia_framework' ),
			'id'		=> 'sidebarmenu_social',
			'type'		=> 'checkbox',
			'std'		=> '',
			'target'	=> array( 'default_layout_target::.avprev-layout-container-social::set_active' ),
			'required'	=> array( 'header_position', '{contains}header_sidebar' ),
		);



// END TAB
$avia_elements[] = array(
			'slug'			=> 'layout',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_layout_tab1_end',
			'nodescription' => true
		);


// START TAB
$avia_elements[] = array(
			'slug'			=> 'layout',
			'name'			=> __( 'Dimensions', 'avia_framework' ),
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_layout_tab2_start',
			'class'			=> 'avia_tab avia_tab2',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'	=> 'layout',
			'name' 	=> __( 'Responsive Site', 'avia_framework' ),
			'desc' 	=> __( 'If enabled the size of your website will adapt and change the layout to fit smaller screens, like tablets or mobile phones', 'avia_framework' ),
			'id' 	=> 'responsive_active',
			'type' 	=> 'checkbox',
			'std'	=> 'enabled'
		);

$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Maximum Container width', 'avia_framework' ),
			'desc'		=> __( 'Enter the maximum content width for your site. Pixel and % are allowed eg: 1130px, 1310px, 100% ', 'avia_framework' ),
			'id'		=> 'responsive_size',
			'type'		=> 'text',
			'std'		=> '1310px',
			'required'	=> array( 'responsive_active', '{contains}enabled' ),
		);

$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Content | Sidebar Ratio', 'avia_framework' ),
			'desc'		=> __( 'Here you can choose the width of your content and sidebar. First Number indicates the content width, second number indicates sidebar width.', 'avia_framework' ) .'<br/><strong>'.__( 'Note:', 'avia_framework' ) .'</strong> '.
								__( 'If you want to disable sidebars you can do so in the', 'avia_framework' ) .
								" <a href='#goto_sidebars'>" .
								__( 'Sidebar Settings', 'avia_framework' ) .
								'</a>',
			'id'		=> 'content_width',
			'type'		=> 'select',
			'std'		=> '73',
			'target'	=> array( 'default_layout_target::.avprev-layout-content::width' ),
			'no_first'	=> true,
			'subtype'	=> array(
								'80% | 20%' => '80',
								'79% | 21%' => '79',
								'78% | 22%' => '78',
								'77% | 23%' => '77',
								'76% | 24%' => '76',
								'75% | 25%' => '75',
								'74% | 26%' => '74',
								'73% | 27%' => '73',
								'72% | 28%' => '72',
								'71% | 29%' => '71',

								'70% | 30%' => '70',
								'69% | 31%' => '69',
								'68% | 32%' => '68',
								'67% | 33%' => '67',
								'66% | 34%' => '66',
								'65% | 35%' => '65',
								'64% | 36%' => '64',
								'63% | 37%' => '63',
								'62% | 38%' => '62',
								'61% | 39%' => '61',

								'60% | 40%' => '60',
								'59% | 41%' => '59',
								'58% | 42%' => '58',
								'57% | 43%' => '57',
								'56% | 44%' => '56',
								'55% | 45%' => '55',
								'54% | 46%' => '54',
								'53% | 47%' => '53',
								'52% | 48%' => '52',
								'51% | 49%' => '51',
								'50% | 50%' => '50'
							)
		);

$numbers = array();
for( $i = 100; $i >= 50; $i-- )
{
	$numbers[ $i . '%' ] = $i;
}

$avia_elements[] = array(
			'slug'		=> 'layout',
			'name'		=> __( 'Content + Sidebar width', 'avia_framework' ),
			'desc'		=> __( 'Here you can enter the combined width of content and sidebar', 'avia_framework' ),
			'id'		=> 'combined_width',
			'type'		=> 'select',
			'std'		=> '100',
			'target'	=> array( 'default_layout_target::.avprev-layout-container-sizer::width' ),
			'no_first'	=> true,
			'subtype'	=> $numbers
		);


// END TAB
$avia_elements[] = array(
			'slug'			=> 'layout',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_layout_tab2_end',
			'nodescription' => true
		);

//END TAB CONTAINER
$avia_elements[] = array(
			'slug'			=> 'layout',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_layout_container_end',
			'nodescription' => true
		);

