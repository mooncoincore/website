<?php
/**
 * Import/Export/... Tab
 * =====================
 * 
 * @since 4.8.2
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

global $avia_config, $avia_pages, $avia_elements;



$warning  = '<br />';
$warning .= '<strong>';
$warning .= __( 'We strongly recommend to export your current settings now to have a fallback.', 'avia_framework' );
$warning .= '</strong>';


if( is_child_theme() )
{
	$avia_elements[] = array(
				'slug'	=> 'upload',
				'name' 	=> __( 'Import Settings From Your Parent Theme', 'avia_framework' ),
				'desc' 	=> __( "We have detected that you are using a Child Theme. That's Great!. If you want to, we can import the settings of your Parent theme to your Child theme. Please be aware that this will overwrite your current child theme settings.", 'avia_framework' ) . $warning,
				'id' 	=> 'parent_setting_import',
				'type' 	=> 'parent_setting_import'
			);
}


$avia_elements[] = array(
			'slug'	=> 'upload',
			'name' 	=> __( 'Export Theme Settings File', 'avia_framework' ),
			'desc' 	=> __( 'Click the button to generate and download a config file which contains the theme settings. You can use the config file to import the theme settings on another sever.', 'avia_framework' ),
			'id' 	=> 'theme_settings_export',
			'type' 	=> 'theme_settings_export'
		);


$avia_elements[] = array(
			'slug'	=> 'upload',
			'name' 	=> __( 'Select Theme Options To Import', 'avia_framework' ),
			'desc' 	=> __( 'Check if you do not want to import all settings from an exported theme settings file. Please read the <a href="https://kriesi.at/documentation/enfold/backup-theme-settings" target="_blank" rel="noopener noreferrer">documentation</a> for more information how to customize import.', 'avia_framework' ),
			'id' 	=> 'upload_filter_checkbox',
			'type' 	=> 'checkbox',
			'std'	=> ''
		);

$avia_elements[] = array(
			'slug'		=> 'upload',
			'name'		=> __( 'Keep Quick CSS Content', 'avia_framework' ),
			'desc'		=> __( 'Check if you want to keep your added CSS stylings in &quot;General Styling -> Quick CSS&quot;. In case you select single tabs to import below your Quick CSS settings will be kept by default except you select the tab containing the Quick CSS field. In this case you must check here to keep them.', 'avia_framework' ),
			'id'		=> 'upload_keep_quick_css',
			'type'		=> 'checkbox',
			'std'		=> '',
			'required'	=> array( 'upload_filter_checkbox', 'upload_filter_checkbox' ),
		);

$avia_elements[] = array(
			'slug'		=> 'upload',
			'name'		=> __( 'Select Theme Options Tabs For Import', 'avia_framework' ),
			'desc'		=> __( 'Do not select any tabs to import all or select which tabs of the theme options you want to import from the uploaded settings file. All options in these selected tabs will be imported - options in other tabs will not be modified.', 'avia_framework' ),
			'id'		=> 'upload_filter_tabs',
			'type'		=> 'select',
			'multiple'	=> '6',
			'std'		=> '',
			'no_first'	=> true,
			'subtype'	=> 'option_page_tabs',
			'required'	=> array( 'upload_filter_checkbox', 'upload_filter_checkbox' )
		);

$avia_elements[] = array(
			'slug'				=> 'upload',
			'name'				=> __( 'Import Theme Settings File', 'avia_framework' ),
			'desc'				=> __( "Upload a theme configuration file here. Note that the configuration file settings will overwrite your current configuration and you can't restore the current configuration afterwards.", 'avia_framework' ) . $warning,
			'id'				=> 'config_file_upload',
			'type'				=> 'file_upload',
			'std'				=> '',
			'title'				=> __( 'Upload Theme Settings File', 'avia_framework' ),
			'button'			=> __( 'Insert Settings File', 'avia_framework' ),
			'trigger'			=> 'av_config_file_insert',
			// 'fopen_check' 	=> 'true',
			'file_extension'	=> 'txt',
			'file_type'			=> 'text/plain'
		);

if( ! current_theme_supports( 'avia_disable_reset_options' ) )
{
	$avia_elements[] = array(
				'slug'		=> 'upload',
				'name'		=> __( 'Theme Reset All Options Button', 'avia_framework' ),
				'desc'		=> __( 'Select if you want to block reset of theme options and hide the reset button. You must select activate button before you can reset theme options.', 'avia_framework' ),
				'id'		=> 'reset_options_button',
				'type'		=> 'select',
				'std'		=> '',
				'no_first'	=> true,
				'subtype'	=> array(
								__( 'Activate reset all options button', 'avia_framework' )			=> '',
								__( 'Block and hide reset all options button', 'avia_framework' )	=> 'block_hide',
							)
			);

	$avia_elements[] = array(
				'slug'			=> 'upload',
				'type'			=> 'visual_group_start',
				'id'			=> 'avia_upload_reset_button_group_start',
				'nodescription'	=> true,
				'required'		=> array( 'reset_options_button', '' ),
			);

	$avia_elements[] = array(
				'slug'	=> 'upload',
				'name' 	=> __( 'Select Theme Options To Reset', 'avia_framework' ),
				'desc' 	=> __( 'Check if you do not want to reset all options. Please read the <a href="https://kriesi.at/documentation/enfold/backup-theme-settings" target="_blank" rel="noopener noreferrer">documentation</a> for more information how to customize resetting theme options.', 'avia_framework' ),
				'id' 	=> 'reset_filter_checkbox',
				'type' 	=> 'checkbox',
				'std'	=> ''
			);

	$avia_elements[] = array(
				'slug'		=> 'upload',
				'name'		=> __( 'Keep Quick CSS Content', 'avia_framework' ),
				'desc'		=> __( 'Check if you want to keep your added CSS stylings in &quot;General Styling -> Quick CSS&quot;. In case you select single tabs to reset below your Quick CSS settings will be kept by default except you select the tab containing the Quick CSS field. In this case you must check here to keep them.', 'avia_framework' ),
				'id'		=> 'reset_keep_quick_css',
				'type'		=> 'checkbox',
				'std'		=> '',
				'required'	=> array( 'reset_filter_checkbox', 'reset_filter_checkbox' ),
			);

	$avia_elements[] = array(
				'slug'		=> 'upload',
				'name'		=> __( 'Select Theme Options Tabs To Reset', 'avia_framework' ),
				'desc'		=> __( 'Do not select any tabs to reset all options or select which tabs of the theme options you want to reset. All options in these selected tabs will be set to theme factory values - options in other tabs will not be modified.', 'avia_framework' ),
				'id'		=> 'reset_filter_tabs',
				'type'		=> 'select',
				'multiple'	=> '6',
				'std'		=> '',
				'no_first'	=> true,
				'subtype'	=> 'option_page_tabs',
				'required'	=> array( 'reset_filter_checkbox', 'reset_filter_checkbox' ),
			);

	$avia_elements[] = array(
				'slug'		=> 'upload',
				'name'		=> __( 'Reset Selected Options', 'avia_framework' ),
				'desc'		=> __( 'Click the button to reset selected options to theme factory default values. Note that this will overwrite your current configuration and you cannot restore the current configuration afterwards.', 'avia_framework' ) . $warning,
				'id'		=> 'reset_selected_button',
				'type'		=> 'reset_selected_button',
				'required'	=> array( 'reset_filter_checkbox', 'reset_filter_checkbox' ),
			);

	$avia_elements[] = array(
				'slug'			=> 'upload',
				'type'			=> 'visual_group_end',
				'id'			=> 'avia_upload_reset_button_group_end',
				'nodescription'	=> true
		);

}

$avia_elements[] = array(
			'slug'	=> 'upload',
			'name' 	=> __( 'Export Layout Builder Templates', 'avia_framework' ),
			'desc' 	=> __( 'Click the button to generate and download a file which contains the Layout Builder saved templates. You can use this file to import the templates on another server.', 'avia_framework' ),
			'id' 	=> 'alb_templates_export',
			'type' 	=> 'alb_templates_export'
		);

$avia_elements[] = array(
			'slug'				=> 'upload',
			'name'				=> __( 'Import Layout Builder Templates File', 'avia_framework' ),
			'desc'				=> __( 'Upload a Layout Builder Templates file here. The uploaded templates will be added to the existing templates. Same named templates will not be overwritten.', 'avia_framework' ),
			'id'				=> 'alb_templates_upload',
			'type'				=> 'file_upload',
			'std'				=> '',
			'title'				=> __( 'Upload Layout Builder Templates File', 'avia_framework' ),
			'button'			=> __( 'Insert Layout Builder Templates File', 'avia_framework' ),
			'trigger'			=> 'av_alb_templates_file_insert',
			// 'fopen_check' 	=> 'true',
			'file_extension'	=> 'txt',
			'file_type'			=> 'text/plain',
		);

$avia_elements[] = array(
			'slug'				=> 'upload',
			'name'				=> __( 'Iconfont Manager', 'avia_framework' ),
			'desc'				=> __( 'You can upload additional Iconfont Packages generated with', 'avia_framework' ) . " <a href='http://fontello.com/' target='_blank' rel='noopener noreferrer'>Fontello</a>  ".
										__( 'or use monocolored icon sets from', 'avia_framework' ) . " <a href='http://www.flaticon.com/' target='_blank' rel='noopener noreferrer'>Flaticon</a>. ".
										__( 'Those icons can then be used in your Layout Builder.', 'avia_framework' ) . '<br/><br/>'.
										__( "The 'Default Font' can't be deleted.", 'avia_framework' ) . '<br/><br/>'.
										__( 'Make sure to delete any fonts that you are not using, to keep the loading time for your visitors low', 'avia_framework' ),
			'id'				=> 'iconfont_upload',
			'type'				=> 'file_upload',
			'std'				=> '',
			'title'				=> __( 'Upload/Select Fontello Font Zip', 'avia_framework' ),
			'button'			=> __( 'Insert Zip File', 'avia_framework' ),
			'trigger'			=> 'av_fontello_zip_insert',
			// 'fopen_check' 	=> 'true',
			'file_extension'	=> 'zip', //used to check if user can upload this file type
			'file_type'			=> 'application/octet-stream, application/zip', //used for javascript gallery to display file types
		);


$avia_elements[] = array(
			'slug'				=> 'upload',
			'name'				=> __( 'Custom Font Manager', 'avia_framework' ),
			'desc'				=> __( "You can upload your custom Font zip files. Intended for <a href='https://fonts.google.com/' target='_blank' rel='noopener noreferrer'>Google Webkit Fonts</a>.", 'avia_framework' ) . '<br/><br/>' .
										__( 'Make sure to delete any fonts that you are not using, to keep the loading time for your visitors low', 'avia_framework' ),
			'id'				=> 'typefont_upload',
			'type'				=> 'file_upload',
			'std'				=> '',
			'title'				=> __( 'Upload/Select Font Zip File', 'avia_framework' ),
			'button'			=> __( 'Insert Zip File', 'avia_framework' ),
			'trigger'			=> 'av_typefont_zip_insert',
			// 'fopen_check' 	=> 'true',
			'file_extension'	=> 'zip', //used to check if user can upload this file type
			'file_type'			=> 'application/octet-stream, application/zip', //used for javascript gallery to display file types
		);

