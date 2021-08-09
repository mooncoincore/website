<?php
if( ! defined( 'AVIA_FW' ) )	{	exit( 'No direct script access allowed' );	}

/**
 * Handles importing a demo
 * 
 * By default errors importing a demo due to not activated plugins are ignored.
 * 
 * Creating demos it is usefull to check for import errors - In wp-config.php:
 * 
 * Enable logging to default error log, behaviour is same as default, reroute to theme options page when finished:
 *		- define( 'WP_DEBUG', true );
 * 
 * To enable additional displaying of error message to user - no reroute to theme options page when error occur:
 *		-  define( 'AVIA_REPORT_DEMO_IMPORT_ERRORS', true );
 * 
 */

if ( ! defined( 'WP_LOAD_IMPORTERS' ) )	
{	
	define( 'WP_LOAD_IMPORTERS', true );	
}

if( ! defined( 'AVIA_REPORT_DEMO_IMPORT_ERRORS' ) )
{
	define( 'AVIA_REPORT_DEMO_IMPORT_ERRORS', false );
}

//heavily increased the execution time. if an image optimization plugin is active this will be necessary when importing larger demos
@ini_set( 'max_execution_time', 1200 );

$demo_full_name = ! empty( $_REQUEST['demo_full_name'] ) ? stripslashes( $_REQUEST['demo_full_name'] ) : '';
$debug_prefix = sprintf( __( 'Demo Importer (%s):', 'avia_framework' ), $demo_full_name ) . ' ';

if( defined( 'WP_DEBUG' ) && WP_DEBUG )
{
	error_log( $debug_prefix . __( 'Import started', 'avia_framework' ) );
}

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

$avia_importerError = false;

global $avia_config;

/**
 * We have to handle:
 * 
 *	- a default demo shipped with theme (default)
 *	- Demos shipped with theme
 *	- Downloaded demos from an external server
 * 
 * @since 4.8.2
 */
$default_path = get_template_directory() . '/includes/admin/demo_files/default';

if( isset( $_POST['files'] ) && ! empty( $_POST['files'] ) ) 
{
	$default_path = get_template_directory() . $_POST['files'];
}
else if( isset( $_POST['import_dir'] ) && isset( $_POST['demo_name'] ) && ! empty( $_POST['import_dir'] ) && ! empty( $_POST['demo_name'] ) )
{
	$default_path = trailingslashit( $_POST['import_dir'] ) . $_POST['demo_name'];
}

$import_filepath = apply_filters(  'avf_import_dummy_filepath', $default_path, THEMENAME );

/**
 * Make path global to allow other scripts to get access (e.g. config-woocommerce/admin-import.php)
 */
$avia_config['demo_import']['current_import_filepath'] = $import_filepath;


//check if wp_importer, the base importer class is available, otherwise include it
if( ! class_exists( 'WP_Importer' ) ) 
{
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
	{
		require_once( $class_wp_importer );
	}
	else
	{
		$avia_importerError = true;
	}
}

//check if the wp import class is available, this class handles the wordpress XML files. If not include it
//make sure to exclude the init function at the end of the file in kriesi_importer
if( ! class_exists( 'WP_Import' ) ) 
{
	$class_wp_import = AVIA_PHP . 'wordpress-importer/wordpress-importer.php';
	if ( file_exists( $class_wp_import ) )
	{
		require_once( $class_wp_import );
	}
	else
	{
		$avia_importerError = true;
	}
}

if( $avia_importerError !== false )
{
	$msg = sprintf( __( 'The Auto importing script could not be loaded. Please use the wordpress importer and import the XML file <pre>%s</pre> manually.', 'avia_framework' ), $import_filepath . '.xml' );
	
	if( defined( 'WP_DEBUG' ) && WP_DEBUG )
	{
		error_log( $debug_prefix . $msg );
	}
	
	exit( 'avia_error-' . $msg );
}
else
{
	if( class_exists( 'WP_Import' ) ) 
	{
		include_once( 'wordpress-importer/avia-import-class.php' );
	}
	
	$import_error = false;

	if( ! is_file( $import_filepath . '.xml' ) )
	{
		$msg = sprintf( __( 'The XML file <pre>%s</pre> containing the demo content is not available or could not be read.<br/> You might want to try to set the file permission to chmod 777.<br/>If this doesn\'t work please use the wordpress importer and import the XML file (should be located in your themes folder: dummy.xml) manually <a href="/wp-admin/import.php">here.</a>', 'avia_framework' ), $import_filepath . '.xml' );
		if( defined( 'WP_DEBUG' ) && WP_DEBUG )
		{
			error_log( $debug_prefix . $msg );
		}
		
		exit( 'avia_error-' . $msg );
	}
	else
	{
		if( ! isset( $custom_export ) )
		{
			/**
			 * @used_by		enfold\config-woocommerce\admin-import.php					10
			 */
			do_action( 'avia_import_hook' );
			
			/**
			 * WP Importer plugin echos messages -> success message AND error messages (e.g. when posttypes of non active plugins are imported)
			 */
			ob_start();
			
			$wp_import = new avia_wp_import();
			$wp_import->rename_existing_menus();
			$wp_import->fetch_attachments = true;
			$wp_import->import( $import_filepath . '.xml' );
			
			//	With 4.8.2 with downloading files we change to .txt, so php is only a fallback
			$options_file = '';
			if( is_file( $import_filepath . '.txt' ) )
			{
				$options_file = $import_filepath . '.txt';
			}
			else if( is_file( $import_filepath . '.php' ) )
			{
				$options_file = $import_filepath . '.php';
			}
			
			if( ! empty( $options_file ) )
			{
				$wp_import->saveOptions( $options_file );
			}
			
			$wp_import->set_menus();
			
			$wp_import_msg = trim( ob_get_clean() );
			
			$result_pos = strpos( $wp_import_msg, '<p>All done.' );
			if( 0 ===  $result_pos )
			{
				if( defined( 'WP_DEBUG' ) && WP_DEBUG )
				{
					error_log( $debug_prefix . 'No Errors from importer: ' . $wp_import_msg );
				}
				
				echo $wp_import_msg;
			}
			else if( false !== $result_pos && ( ! defined( 'AVIA_REPORT_DEMO_IMPORT_ERRORS' ) || true !== AVIA_REPORT_DEMO_IMPORT_ERRORS ) )
			{
				//	Error message only reported to log file - not to user
				if( defined( 'WP_DEBUG' ) && WP_DEBUG )
				{
					$out = str_replace( '<br />', "\n\t\t", $wp_import_msg );
					error_log( $debug_prefix . 'Errors from importer - not reported to user: ' . "\n\t\t" . $out );
				}
				
				$done = substr( $wp_import_msg, $result_pos );
				echo $done;
			}
			else
			{
				//	report errors to log and user
				if( defined( 'WP_DEBUG' ) && WP_DEBUG )
				{
					$out = str_replace( '<br />', "\n\t\t", $wp_import_msg );
					error_log( $debug_prefix . 'Errors from importer: ' . "\n\t\t" . $out );
				}
				
				//	limit output to user in modal popup
				$err_msg = '';
				if( strlen( $wp_import_msg ) > 0 )
				{
					$wp_import_msg = explode( '<br />', $wp_import_msg );
					$add_more = true;
					$i = 0;
					
					foreach( $wp_import_msg as $err ) 
					{
						if( false !== stripos( $err, '<p>All done' ) )
						{
							$add_more = false;
							break;
						}
						
						$err_msg .= '<br />' . $err;
						
						$i++;
						if( $i > 10 )
						{
							break;
						}
					}
					
					if( $add_more )
					{
						$err_msg .= '<br />..........';
					}
				}
				
				if( '' != $err_msg )
				{
					$import_error = true;
					
					$out  =  'avia_error-' . __( 'Errors occured importing demo. Maybe you did not activate all necessary plugins or you have existing content that is in conflict with the demo content.', 'avia_framework' ) . '<br />';
					$out .= __( 'Error(s) returned:', 'avia_framework' ) . '<br />';
					$out .= $err_msg;
					
					echo $out;
				}
			}
			
			/**
			 * @used_by		enfold\config-woocommerce\config.php			10
			 */
			do_action( 'avia_after_import_hook' ); // todo: rename. make sure to update hook name of our woocommerce import script
		}
		else
		{
			$import = new avia_wp_import();
			$import->saveOptions( $import_filepath . '.php', $custom_export );
			
			do_action( 'avia_after_custom_import_hook' );
		}
		
		/**
		 * generic hook. example use: after demo setting import we want to regen cached stylesheet
		 * 
		 * @used_by			class aviaElementManager				10
		 * @used_by			functions-enfold.php					30
		 * @used_by			includes\helper-assets.php				100
		 */
		do_action( 'ava_after_import_demo_settings' );
		
		
		/**
		 * In case an error occured we can break script now
		 */
		if( $import_error )
		{
			exit;
		}
		
		update_option( 'av_demo_content_imported', true );
	}
}




