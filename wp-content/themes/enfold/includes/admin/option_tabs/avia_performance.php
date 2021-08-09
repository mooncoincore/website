<?php
/**
 * Performance Tab
 * ===============
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;



$avia_elements[] = array(	
			'slug'			=> 'performance',
			'name'			=> __( 'Website performance and optimization', 'avia_framework' ),
			'desc'			=> __( 'The options here allow you to fine-tune and speed up your theme depending on your needs.', 'avia_framework' ) . '<br>',
			'id'			=> 'performance_header',
			'type'			=> 'heading',
			'std'			=> '',
			'nodescription' => true
		);



$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_performance_compression_start',
			'class'			=> 'avia_tab_container avia_set',
			'nodescription'	=> true
		);



$avia_elements[] = array(	
			'slug'			=> 'performance',
			'name'			=> __( 'File Compression', 'avia_framework' ),
			'desc'			=> __( 'In order to increase the speed of your website you can activate file merging and compression for your CSS and Javascript files. This will reduce and optimize the amount of code loaded.', 'avia_framework' ) . '<br><br>' . '<strong>' . __( 'Please note:', 'avia_framework' ) . ' </strong>' .
									__( 'By default compression is enabled. It is recommended to only disable the feature when you encounter errors (some server environments might cause trouble with active compression) or while you are actively developing your website and are adding new CSS rules or Javascript functions.', 'avia_framework' ),
			'id'			=> 'performance_header_file_compression',
			'type'			=> 'heading',
			'std'			=> '',	
			'nodescription'	=> true
		);


$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'CSS file merging and compression', 'avia_framework' ),
			'desc'		=> __( 'Select which level of file merging and compression you want to apply to your CSS files', 'avia_framework' ),
			'id'		=> 'merge_css',
			'type'		=> 'select',
			'std'		=> 'avia',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Disable - no CSS file merging and compression', 'avia_framework' ) => 'none',
								/*__( 'Compress advanced template builder CSS files (level 1)', 'avia_framework' ) => 'avia-module',*/
								__( 'Enable - merge and compress all theme CSS files', 'avia_framework' ) => 'avia',
								/*__( 'Compress all theme and plugin CSS files (level 3)', 'avia_framework' ) => 'all',*/
							)
		);

$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Javascript file merging and compression', 'avia_framework' ),
			'desc'		=> __( 'Select which level of file merging and compression you want to apply to your Javascript files.', 'avia_framework' ),
			'id'		=> 'merge_js',
			'type'		=> 'select',
			'std'		=> 'avia',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Disable - no Javascript file merging and compression', 'avia_framework' ) => 'none',
								/*__( 'Compress advanced template builder javascript files (level 1)', 'avia_framework' ) => 'avia-module',*/
								__( 'Enable - merge and compress all theme javascript files', 'avia_framework' ) => 'avia',
								/* __( 'Compress all theme and plugin files (level 3)', 'avia_framework' ) => 'all', */
							)
		);

$avia_elements[] = array(
			'slug'	=> 'performance',
			'name'	=> __( 'Show advanced options', 'avia_framework' ),
			'desc'	=> __( 'Contains options for special use cases when problems occur using compression', 'avia_framework' ),
			'id'	=> 'merge_show_advanced',
			'type'	=> 'checkbox',
			'std'	=> false,
		);

$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Http security level for checking readability of merged files', 'avia_framework' ),
			'desc'		=> __( 'Some server configuration make problems with ssl certificates (mostly self-signed certificates) when we check the readability of created merged files. In that case the files are not created. If you experience such problems try to disable the ssl verification during the creation process. This does not effect the protocol on frontend pageload.', 'avia_framework' ),
			'id'		=> 'merge_disable_ssl',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'required'	=> array( 'merge_show_advanced', '{contains_array}merge_show_advanced' ),
			'subtype'	=> array(
									__( 'Use ssl verification if needed for site (= default)', 'avia_framework' )					=> '',
									__( 'Disable ssl verification when checking readability of merged files', 'avia_framework' )	=> 'disable_ssl',
								)
						);

$desc  = __( 'As long as you do not change the theme version number all changes to content of js or css files will result in the same hash extension - this means browsers will not recognize these changes until the browser cache expires. To fix this Enfold adds an additional unique timestamp (since 4.7).', 'avia_framework' ) . '<br /><br />';
$desc .= __( 'Some server configurations cache internal WP data and caused by a known but not yet fixed WP bug return wrong information about the existence of a compressed file - resulting in generating a new file again on every pageload and a rapidly growing folder ../wp-content/uploads/dynamic_avia.', 'avia_framework' ) . '<br /><br />';
$desc .= __( 'To avoid this you can select here to fix this WP bug. You can also supress adding the timestamp - if you wish. Depending on your hoster it may still take some time till this setting will work correctly. Disable file merging, select &quot;Delete old CSS and JS files&quot; - wait for some time, clear server cache and then reactivate your settings.', 'avia_framework' );

$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Unique timestamp of merged files and WP object cache bug', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'merge_disable_unique_timestamp',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'required'	=> array( 'merge_show_advanced', '{contains_array}merge_show_advanced' ),
			'subtype'	=> array(
								__( 'Add unique timestamps (= default)', 'avia_framework' )		=> '',
								__( 'Disable adding unique timestamps only', 'avia_framework' )	=> 'disable_unique_timestamp',
								__( 'Fix WP bug, add unique timestamps', 'avia_framework' )		=> 'fix_wp_bug',
								__( 'Fix WP bug, disable unique timestamps', 'avia_framework' )	=> 'disable_unique_timestamp fix_wp_bug'

							)
		);

$desc  = __( 'On some server configurations you might be receiving error messages like &quot;Remove query strings from static resources&quot;.', 'avia_framework' ) . ' ';
$desc .= '<a href="https://kinsta.com/knowledgebase/remove-query-strings-static-resources/" target="_blank" rel="noopener noreferrer">' . __( 'Background information', 'avia_framework' ) . '</a><br /><br />';
$desc .= __( 'Select here to remove the query string from static resources - but be aware the query strings allow browsers to detect changes to files and invalidate the browser cached files. Not doing this might break the layout or function of your site after an update until these files expire in browser cache.', 'avia_framework' ) . '<br /><br />';
$desc .= __( 'THIS OPTION IS IGNORED WHEN WP_DEBUG = true.', 'avia_framework' );

$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Remove query string from static resources', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'remove_query_string_from_resources',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'required'	=> array( 'merge_show_advanced', '{contains_array}merge_show_advanced' ),
			'subtype'	=> array(
								__( 'Leave query strings', 'avia_framework' )	=> '',
								__( 'Remove query strings', 'avia_framework' )	=> 'remove_query_string_from_resources',
							)
		);

$desc  = __( 'Select font display behaviour for your uploaded custom fonts and icon fonts. Please read carefully backend documentation before changing. You can also use filter avf_font_display.', 'avia_framework' ) . '<br />';
$desc .= '<a href="https://developers.google.com/web/updates/2016/02/font-display" target="_blank" rel="noopener noreferrer">' . __( 'Controlling Font Performance with font-display', 'avia_framework' ) . '</a>' . '<br />';
$desc .= '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display" target="_blank" rel="noopener noreferrer">' . __( 'MDN font-display', 'avia_framework' ) . '</a>' . '<br />';

$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Custom Font Display Behaviour', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'custom_font_display',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'required'	=> array( 'merge_show_advanced', '{contains_array}merge_show_advanced' ),
			'subtype'	=> array(
								__( 'auto (= default)', 'avia_framework' )	=> '',
								__( 'block', 'avia_framework' )				=> 'block',
								__( 'swap', 'avia_framework' )				=> 'swap',
								__( 'fallback', 'avia_framework' )			=> 'fallback',
								__( 'optional', 'avia_framework' )			=> 'optional'
							)
		);

$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_performance_compression_end',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_performance_disable_elements_start',
			'nodescription'	=> true
		);


$avia_elements[] = array(
			'slug'			=> 'performance',	
			'name'			=> __( 'Disable Template Builder Elements', 'avia_framework' ),
			'desc'			=> __( 'The theme allows you to disable template builder elements that you do not need. This reduces the amount of Javascript and CSS loaded in your frontend', 'avia_framework' ) . '<br>',
			'id'			=> 'performance_header_disable_alb_elemets',
			'type'			=> 'heading',
			'std'			=> '',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Disabling of template builder elements', 'avia_framework' ),
			'desc'		=> __( 'By default the theme will only load elements that are used on your posts and pages. You can disable the feature or manually manage loaded elements if you run into trouble', 'avia_framework' ),
			'id'		=> 'disable_alb_elements',
			'type'		=> 'select',
			'std'		=> 'auto',
			'no_first'	=> true,
			'subtype'	=> array( 
								__( 'Always load all elements', 'avia_framework' ) => 'load_all',
								__( 'Load only used elements (recommended)', 'avia_framework' ) => 'auto',
								__( 'Manually manage loaded elements', 'avia_framework' ) => 'manually',
							)
		);


$avia_elements[] = array(
			'slug'			=> 'performance',
			'id'			=> 'alb_disabled',
			'type'			=> 'template_builder_element_loader',
			'std'			=> '',
			'required'		=> array( 'disable_alb_elements', 'manually' ),
			'nodescription'	=> true,
		);

$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Scan Widgets for Theme Shortcodes', 'avia_framework' ),
			'desc'		=> __( 'Enable scan only when you use Theme Shortcodes in widgets and do not use these in pages/posts. If you run into troubles please use one of the other options. Please reload the widget page when finished editing to force a scan of the widgets.', 'avia_framework' ),
			'id'		=> 'scan_widgets_for_alb_elements',
			'type'		=> 'select',
			'std'		=> '',
			'required'	=> array( 'disable_alb_elements', 'auto' ),
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'Do not scan widgets', 'avia_framework' )	=> '',
								__( 'Scan widgets', 'avia_framework' )			=> 'scan_widgets'
							)
		);


$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_performance_disable_elements_end',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_performance_disable_features_start',
			'nodescription'	=> true
		);


$avia_elements[] = array(	
			'slug'			=> 'performance',
			'name'			=> __( 'Disable Features', 'avia_framework' ),
			'desc'			=> __( 'Here you can disable theme features that are not used by every website', 'avia_framework' ) . '<br>',
			'id'			=> 'performance_header_2' ,
			'type'			=> 'heading',
			'std'			=> '',
			'nodescription' => true
		);


$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Self hosted videos and audio features (WP-Mediaelement scripts)', 'avia_framework' ),
			'desc'		=> __( 'By default the theme will load wp-mediaelement scripts only if needed on your posts and pages. You can disable the feature or force loading these elements if you run into troubles - some plugins require these elements and rely on the WP default behaviour loading these scripts.', 'avia_framework' ),
			'id'		=> 'disable_mediaelement',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'Default - only load when needed (recommended)', 'avia_framework' )			=> '',
								__( 'Disable self hosted video and audio features', 'avia_framework' )			=> 'disable_mediaelement',
								__( 'Always load media features (= WP default behaviour)', 'avia_framework' )	=> 'force_mediaelement',
							)
		);

$avia_elements[] = array(
			'slug'	=> 'performance',
			'name' 	=> __( 'Disable external video features', 'avia_framework' ),
			'desc' 	=> __( 'Check if you do not use Youtube or Vimeo video features.', 'avia_framework' ),
			'id' 	=> 'disable_video',
			'type' 	=> 'checkbox',
			'std'	=> '',
		 );


$avia_elements[] = array(
			'slug'	=> 'performance',
			'name' 	=> __( 'Disable the blog', 'avia_framework' ),
			'desc' 	=> __( 'Check if you do not use the blog. This will disable the blog page as well as the blog template builder element, the comments template builder element and category pages that are based on the default categories (if any plugin post types use these overview styles please keep this feature enabled)', 'avia_framework' ),
			'id' 	=> 'disable_blog',
			'type' 	=> 'checkbox',
			'std'	=> '',
		);



$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_performance_disable_features_end',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_performance_disable_wp_defaults_start',
			'nodescription'	=> true
		);



$avia_elements[] = array(	
			'slug'			=> 'performance',
			'name'			=> __( 'Change WordPress defaults', 'avia_framework' ),
			'desc'			=> __( 'Here you can disable WordPress default scripts and styles that are not necessary for most websites', 'avia_framework' ) . '<br>',
			'id'			=> 'performance_header_wp_defaults',
			'type'			=> 'heading',
			'std'			=> '',
			'nodescription'	=> true
		);

$avia_elements[] = array(
			'slug'	=> 'performance',
			'name' 	=> __( 'Disable Emoji/Smiley Support', 'avia_framework' ),
			'desc' 	=> __( 'Check to disable Emoji/Smiley Support. (Emojis are used by WordPress by default but most websites do not use them)', 'avia_framework' ),
			'id' 	=> 'disable_emoji',
			'type' 	=> 'checkbox',
			'std'	=> ''
		);

if( avia_count_active_plugins() > 0 )
{
	$avia_elements[] = array(
				'slug'	=> 'performance',
				'name' 	=> __( 'Disable jQuery Migrate', 'avia_framework' ),
				'desc' 	=> __( 'Check to disable &quot;jQuery Migrate&quot;. It is an old backward compatibility library for jQuery that is required by some plugins. Make sure that none of your active plugins require it before disabling it!', 'avia_framework' ),
				'id' 	=> 'disable_jq_migrate',
				'type' 	=> 'checkbox',
				'std'	=> '',
			);

	$avia_elements[] = array(
				'slug'	=> 'performance',
				'name' 	=> __( 'Load jQuery in your footer', 'avia_framework' ),
				'desc' 	=> __( 'Loading jQuery in your footer will speed up site rendering but may cause problems with plugins. Only use if you know what you are doing ;-)', 'avia_framework' ),
				'id' 	=> 'jquery_in_footer',
				'type' 	=> 'checkbox',
				'std'	=> '',
			);
}

$avia_elements[] = array(
			'slug'	=> 'performance',
			'name' 	=> __( 'Load Google fonts in footer', 'avia_framework' ),
			'desc' 	=> __( 'Loading the fonts in your footer will speed up the site rendering, but also cause a small flicker of text on page load', 'avia_framework' ),
			'id' 	=> 'gfonts_in_footer',
			'type' 	=> 'checkbox',
			'std'	=> ''
			);

$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_performance_disable_wp_defaults_end',
			'nodescription' => true
		);

$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_performance_disable_responsive_img_start',
			'nodescription'	=> true
		);


$avia_elements[] = array(
			'slug'			=> 'performance',
			'name'			=> __( 'Responsive Images And Lazy Loading', 'avia_framework' ),
//			'desc'			=> __( 'Responsive images are supported using the logic provided by WP.', 'avia_framework' ) . '<br />',
			'id'			=> 'performance_header_4',
			'type'			=> 'heading',
			'nodescription' => true
		);

$desc  = __( 'Select to enable lazy loading using native HTML. Currently WP only supports images, but this might be extended for iframes in future. Please keep in mind that lazy loading might break animations when scrolling to images.', 'avia_framework' ) . ' ';
$desc .= __( 'If you disable lazy loading here this will override any specific element settings of ALB elements. It might not work for 3rd party plugins not using the WP API correctly.', 'avia_framework' );

$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Lazy Loading', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'lazy_loading',
			'type'		=> 'select',
			'std'		=> '',
			'no_first'	=> true,
			'subtype'	=> array(
								__( 'Enable lazy loading', 'avia_framework' )	=> '',
								__( 'Disable lazy loading', 'avia_framework' )	=> 'no_lazy_loading_all',
							)
		);

$avia_elements[] = array(
			'slug'	=> 'performance',
			'name' 	=> __( 'Responsive Images', 'avia_framework' ),
			'desc' 	=> __( 'Check to enable theme support for responsive images using the standard WP implementation for this feature.', 'avia_framework' ),
			'id' 	=> 'responsive_images',
			'type' 	=> 'checkbox',
			'std'	=> 'responsive_images'
		);


$desc  = __( 'Check to enable theme support for responsive images for theme lightbox. This feature starts with version 4.8.2.', 'avia_framework' ) . ' ';
$desc .= __( 'Most of the ALB elements and posts should be supporting this feature since 4.8.3. Please report in our support forum if you encounter problems.', 'avia_framework' );
		
$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Responsive Images For Lightbox (currently in beta only)', 'avia_framework' ),
			'desc'		=> $desc,
			'id'		=> 'responsive_images_lightbox',
			'type'		=> 'checkbox',
			'std'		=> '',
			'required'	=> array( 'responsive_images', 'responsive_images' ),
		);


$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_start',
			'id'			=> 'avia_performance_images_thumbs_start',
			'class'			=> 'visual-set-no-top-border',
			'nodescription' => true,
			'required'		=> array( 'responsive_images', '{contains_array}responsive_images' )
		);

$avia_elements[] = array(
			'slug'		=> 'performance',
			'name'		=> __( 'Image Thumbnails Info', 'avia_framework' ),
			'desc'		=> __( 'Select to show a grouped overview of available image thumbnails.', 'avia_framework' ),
			'id'		=> 'responsive_images_thumbs',
			'type'		=> 'checkbox',
			'std'		=> false
		);

$desc  = __( 'In case you need additional image sizes you can use a plugin like', 'avia_framework' ) . ' ';
$desc .= '<a href="https://wordpress.org/plugins/simple-image-sizes/" target="_blank" rel="noopener noreferrer">Simple Image Sizes</a>. ';
$desc .= __( 'For advanced users:', 'avia_framework' ) . ' ';
$desc .= '<a href="https://github.com/KriesiMedia/enfold-library/blob/master/actions%20and%20filters/Layout/avf_modify_thumb_size.php" target="_blank" rel="noopener noreferrer">Enfold Code Snippets Library</a>.';

$avia_elements[] = array(
			'slug'			=> 'performance',
			'name'			=> __( 'Responsive Images Thumbnails Overview:', 'avia_framework' ),
			'desc'			=> $desc,
			'id'			=> 'performance_header_responsive_img',
			'type'			=> 'heading',
			'nodescription'	=> true,
			'required'		=> array( 'responsive_images_thumbs', '{contains_array}responsive_images_thumbs' )
		);

$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'responsive_images_overview',
			'id'			=> 'responsive_images_overview',
			'nodescription'	=> true,
			'required'		=> array( 'responsive_images_thumbs', '{contains_array}responsive_images_thumbs' )
		);

$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_performance_images_thumbs_end',
			'nodescription'	=> true
		);


$avia_elements[] = array(
			'slug'			=> 'performance',
			'type'			=> 'visual_group_end',
			'id'			=> 'avia_performance_disable_responsive_img_end',
			'nodescription'	=> true
		);


$avia_elements[] = array(
			'slug'			=> 'performance',
			'name'			=> __( 'Image Optimization', 'avia_framework' ),
			'desc'			=> __("Enfold checks if it can detect an image optimization plugin and if it can't find a familiar one recommends a few that are known to work great with the theme", 'avia_framework' ) . '<br>' .
									__( '(If you are running an image optimization plugin that is not detected just ignore this message)', 'avia_framework' ),
			'id'			=> 'image_optimisation_check',
			'type'			=> 'plugin_check',
			'nodescription'	=> true,
	
			'no_found'		=> __( 'We were not able to detect an active image optimization plugin. It is recommended to use one to speed up your site. Here are a few suggestions:', 'avia_framework' ),
			'found'			=> __( 'We were able to detect an image optimization plugin. Great! Nothing left to do here ;)', 'avia_framework' ),
			'too_many'		=> __( 'We were able to detect multiple active image optimization plugins. It is recommended to use only one!', 'avia_framework' ),

			'plugins' => array(
							'Optimus - WordPress Image Optimizer' => array(
											'download'	=> 'optimus',
											'file'		=> 'optimus/optimus.php',
											'desc'		=> '<ul>
																<li>Simple to use with only a few options</li>
																<li>Good size reduction while keeping images pretty</li>
																<li>Very good google pagespeed scores</li>
																<li>Free version works already good, premium version is even better and also rather cheap</li>
															</ul>'
										),


							'ShortPixel Image Optimizer' => array( 
											'download'	=> 'shortpixel-image-optimiser', 	
											'file'		=> 'shortpixel-image-optimiser/wp-shortpixel.php', 
											'desc'		=> '<ul>
																<li>Fine tuning possible due to more options</li>
																<li>Allows you to heavily reduce file size if image quality is not a concern</li>
																<li>Good google pagespeed scores</li>
																<li>Free version will be sufficient for most smaller sites.</li>
															</ul>'
										),

							'WP Smush - Image Optimization' => array( 
											'download'	=> 'resmushit-image-optimizer',
											'file'		=> 'wp-smushit/wp-smush.php' 
										),
							'Imagify Image Optimizer' => array( 
											'download'	=> 'imagify',
											'file'		=> 'imagify/imagify.php' 
										),
							'Compress JPEG & PNG images (TinyPNG)' => array( 
											'download'	=> 'tiny-compress-images', 		
											'file'		=> 'tiny-compress-images/tiny-compress-images.php' 
										),
							'Kraken.io Image Optimizer' => array( 
											'download'	=> 'kraken-image-optimizer', 		
											'file'		=> 'kraken-image-optimizer/kraken.php' 
										),
							'EWWW Image Optimizer' => array( 
											'download'	=> 'ewww-image-optimizer', 		
											'file'		=> 'ewww-image-optimizer/ewww-image-optimizer.php' 
										),
							'EWWW Image Optimizer Cloud' => array( 
											'download'	=> 'ewww-image-optimizer-cloud', 
											'file'		=> 'ewww-image-optimizer-cloud/ewww-image-optimizer-cloud.php' 
										),
							'CheetahO Image Optimizer' => array( 
											'download'	=> 'cheetaho-image-optimizer', 	
											'file'		=> 'cheetaho-image-optimizer/cheetaho.php' 
										),
							'Zara 4 Image Compression' => array( 
											'download'	=> 'zara-4', 						
											'file'		=> 'zara-4/zara-4.php' 
										),
							'ImageRecycle pdf & image compression' => array( 
											'download'	=> 'imagerecycle-pdf-image-compression', 
											'file'		=> 'imagerecycle-pdf-image-compression/wp-image-recycle.php' 
										),
							'Prizm Image' => array( 
											'download'	=> false, 						
											'file'		=> 'prizm-image/wp-prizmimage.php' 
										),
							'CW Image Optimizer' => array( 
											'download'	=> false, 						
											'file'		=> 'cw-image-optimizer/cw-image-optimizer.php' 
										),
							'Imsanity' => array( 
											'download' => 'imsanity', 					
											'file' => 'imsanity/imsanity.php' 
										),
							'Way2enjoy Image Optimizer and Resize Image – WordPress Image Compression' => array( 
											'download'	=> 'way2enjoy-compress-images', 	
											'file'		=> 'way2enjoy-compress-images/way2enjoy.php' 
										),
							'JPG, PNG Compression and Optimization' => array( 
											'download'	=> 'wp-image-compression',		
											'file'		=> 'wp-image-compression/wp-image-compression.php' 
										),
							'Highcompress Image Compressor' => array( 
											'download'	=> 'high-compress', 				
											'file'		=> 'high-compress/highcompress.php' 
										),
							'Image Optimizer by 10web – Image Optimizer and Compression plugin' => array( 
											'download'	=> 'image-optimizer-wd', 		
											'file'		=> 'image-optimizer-wd/io-wd.php' 
										),
							'Ultimate Image Optimization Helpers' => array( 
											'download'	=> 'ultimate-image-optimization-helpers', 
											'file'		=> 'ultimate-image-optimization-helpers/ultimate-image-optimization-helpers.php' 
										),
							'Pixpie – Intelligent Image Compression' => array( 
											'download'	=> 'wp-pixpie', 	
											'file'		=> 'wp-pixpie/wp-pixpie-plugin.php' 
										),
							'Resize Image After Upload' => array( 
											'download'	=> 'resize-image-after-upload', 	
											'file'		=> 'resize-image-after-upload/resize-image-after-upload.php'
										),
							'TP Image Optimizer' => array( 
											'download'	=> 'tp-image-optimizer', 		
											'file'		=> 'tp-image-optimizer/tp-image-optimizer.php' 
										)
					)

		);



$avia_elements[] = array(
			'slug'			=> 'performance',
			'name'			=> __( 'Caching Plugins', 'avia_framework' ),
			'desc'			=> __( "Enfold checks if it can detect a website caching plugin and if it can't find a familiar one recommends a few that are known to work great with the theme", 'avia_framework' ) . '<br>' .
								__( '(If you are running a caching plugin that is not detected or your webhost has built in caching ignore this message)', 'avia_framework' ),
			'id'			=> 'performance_cache_plugins',
			'type'			=> 'plugin_check',
			'nodescription'	=> true,
	
			'no_found'		=>__( 'We were not able to detect an active caching plugin. It is recommended to use one to speed up your site. Here are a few suggestions:', 'avia_framework' ),
			'found'			=>__( 'We were able to detect a caching plugin. Great! Nothing left to do here ;)', 'avia_framework' ),
			'too_many'		=>__( 'We were able to detect multiple active caching plugins. It is recommended to use only one!', 'avia_framework' ),

			'plugins' => array(
							'WP Super Cache' => array( 
											'download'	=> 'wp-super-cache', 	
											'file'		=> 'wp-super-cache/wp-cache.php', 
											'desc'		=> '<ul>
																<li>Great all around caching plugin</li>
																<li>Easy setup (usually no need to change the default settings)</li>
															</ul>'
										),
							'Comet Cache' => array( 
											'download'	=> 'comet-cache', 
											'file'		=> 'comet-cache/comet-cache.php', 
											'desc'		=> '<ul>
																<li>Plug and Play, no setup necessary</li>
																<li>Premium version available</li>
															</ul>'
										),
							'W3 Total Cache' => array( 
											'download'	=> 'w3-total-cache', 
											'file'		=> 'w3-total-cache/w3-total-cache.php', 
											'desc'		=> '<ul>
																<li>Fast</li>
																<li>Easy to use</li>
															</ul>'
										),


							'Comet Cache Pro' => array( 
											'download'	=> 'comet-cache', 		
											'file'		=> 'comet-cache-pro/comet-cache-pro.php' 
										),
							'Wot Cache' => array( 
											'download'	=> false, 				
											'file'		=> 'wot-cache/wot-cache.php' 
										),
							'WP Rocket' => array( 
											'download'	=> false, 				
											'file'		=> 'wp-rocket/wp-rocket.php' 
										),
							'WP Fastest Cache' => array( 
											'download'	=> 'wp-fastest-cache', 	
											'file'		=> 'wp-fastest-cache/wpFastestCache.php' 
										),
							'Simple Cache' => array( 
											'download' => 'simple-cache', 		
											'file' => 'simple-cache/simple-cache.php' 
										),
							'Cachify' => array( 
											'download'	=> 'cachify', 			
											'file'		=> 'cachify/cachify.php' 
										),
							'Hyper Cache' => array( 
											'download'	=> 'hyper-cache', 		
											'file'		=> 'hyper-cache/plugin.php' 
										),
							'Cache Enabler' => array( 
											'download'	=> 'cache-enabler', 		
											'file'		=> 'cache-enabler/cache-enabler.php' 
										),
							'Autoptimize' => array( 
											'download'	=> 'autoptimize', 		
											'file'		=> 'autoptimize/autoptimize.php' 
										),
							'Cache Control' => array( 
											'download'	=> 'cache-control', 		
											'file'		=> 'cache-control/cache-control.php' 
										),
							'Fast Velocity Minify' => array( 
											'download'	=> 'fast-velocity-minify', 	
											'file'		=> 'fast-velocity-minify/fvm.php' 
										),
							'Gator Cache' => array( 
											'download'	=> 'gator-cache', 		
											'file'		=> 'gator-cache/gator-cache.php' 
										),
							'Breeze' => array( 
											'download'	=> 'breeze', 				
											'file'		=> 'breeze/breeze.php' 
										),
							'Super Static Cache' => array( 
											'download'	=> 'super-static-cache', 	
											'file'		=> 'super-static-cache/super-static-cache.php' 
										),
							'YASAKANI Cache' => array( 
											'download'	=> 'yasakani-cache', 		
											'file'		=> 'yasakani-cache/yasakani-cache.php.php' 
										),
							'Lite Speed Cache' => array( 
											'download'	=> 'litespeed-cache', 	
											'file'		=> 'litespeed-cache/litespeed-cache.php' 
										),
							'Hummingbird Page Speed Optimization' => array( 
											'download'	=> 'hummingbird-performance', 	
											'file'		=> 'hummingbird-performance/wp-hummingbird.php' 
										),
							'Powered Cache' => array( 
											'download'	=> 'powered-cache', 		
											'file'		=> 'powered-cache/powered-cache.php' 
										),
							'Page Speed Optimization' => array( 
											'download'	=> 'above-the-fold-optimization', 	
											'file'		=> 'above-the-fold-optimization/abovethefold.php' 
										),
							'Varnish Caching' => array( 
											'download'	=> 'vcaching', 			
											'file'		=> 'vcaching/vcaching.php' 
										),
							'Borlabs Cache' => array( 
											'download'	=> false, 				
											'file'		=> 'borlabs-cache/borlabs-cache.php' 
										),
						)

		);

$avia_elements[] = array(
			'slug'	=> 'performance',
			'name'	=> __( 'Delete old CSS and JS files?', 'avia_framework' ),
			'desc'	=> __( 'Check if you want to delete expired CSS and JS files generated by the theme. Only recommended if you are NOT using a caching plugin (since a cached page might still use those files)', 'avia_framework' ),
			'id'	=> 'delete_assets_after_save',
			'type'	=> 'checkbox',
			'std'	=> ''
		);

