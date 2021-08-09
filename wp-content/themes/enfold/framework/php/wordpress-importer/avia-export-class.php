<?php  
if ( ! defined( 'AVIA_FW' ) )	{	exit( 'No direct script access allowed' );	}
/**
 * This file holds the class that creates the options export file for the wordpress importer
 *
 * @since 4.8.2
 * 
 * For demo developers: To generate the php file needed add to wp-config.php:
 * 
 *	- define( 'AVIA_GENERATE_DEMO_PHP_FILE', true );
 * 
 *
 * @author		Christian "Kriesi" Budschedl
 * @copyright	Copyright (c) Christian Budschedl
 * @link		http://kriesi.at
 * @link		http://aviathemes.com
 * @since		Version 1.1
 * @package 	AviaFramework
 */

/**
 *
 */
if( ! class_exists( 'avia_wp_export' ) )
{
	class avia_wp_export 
	{
		
		/**
		 *
		 * @var avia_superobject 
		 */
		protected $avia_superobject;
		
		/**
		 *
		 * @var array 
		 */
		protected $subpages;
		
		/**
		 *
		 * @var array 
		 */
		protected $options;
		
		/**
		 *
		 * @var string 
		 */
		protected $db_prefix;
		
		/**
		 * 
		 * @param avia_superobject $avia_superobject
		 */
		public function __construct( $avia_superobject )
		{
			if( ! isset( $_GET['avia_export'] ) ) 
			{
				return;
			}
			
			if( defined('DOING_AJAX') && DOING_AJAX ) 
			{
				return;
			}
			
			$this->avia_superobject = $avia_superobject;
			$this->subpages = $avia_superobject->subpages;
			$this->options  = apply_filters( 'avia_filter_global_options_export', $avia_superobject->options );
			$this->db_prefix = $avia_superobject->option_prefix;
			
			add_action( 'admin_init', array( $this, 'initiate' ), 200 );
		}
		
		
		/**
		 * @since 4.6.4
		 */
		public function __destruct() 
		{
			unset( $this->avia_superobject );
			Unset( $this->subpages );
			unset( $this->options );
		}
		
		/**
		 * Performs the export
		 */
		public function initiate()
		{
			
			/**
			 * Returns array of file to export:
			 *		array(	
			 *				'name'		=>  ....
			 *				'content'	=>	....
			 *			)
			 * 
			 * @used_by			aviaSaveBuilderTemplate				10
			 * @since 4.6.4
			 * @return array|null
			 */
			$export_file = apply_filters( 'avf_generate_export_file', null );
			if( is_array( $export_file ) )
			{
				$name = isset( $export_file['name'] ) ? $export_file['name'] : 'unknown';
				$content = isset( $export_file['content'] ) ? $export_file['content'] : '';
				
				//	generate downlaod file and exit !!
				$this->generate_export_file( $content, $name );
				exit();
			}
			
			//get the first subkey of the saved options array
			foreach( $this->subpages as $subpage_key => $subpage )
			{
				$export[ $subpage_key ] = $this->export_array_generator( $this->avia_superobject->option_page_data, $this->options[ $subpage_key ], $subpage );
			}
			
			//export of options
			$export = base64_encode( serialize( $export ) );

			//	check to generate export file for download and exit or php file output
			$download_config = true;
			if( ! isset( $_GET[ 'avia_generate_config_file'] ) )
			{
				$download_config = false;
			}
			
			if( $download_config && defined( 'AVIA_GENERATE_DEMO_PHP_FILE' ) && true === AVIA_GENERATE_DEMO_PHP_FILE )
			{
				$download_config = false;
			}
			
			if( $download_config )
			{
				//	generate downlaod file and exit !!
				$this->generate_export_file( $export );
				exit();
			}

			$widget_settings = $this->export_widgets();
			$widget_settings = base64_encode( serialize( $widget_settings ) );

			$fonts = $this->export_option( 'avia_builder_fonts' );
			
			//export of dynamic pages
			//
			//	deprecated 4.8.2
			//	
//			$export_dynamic_pages = get_option( $this->db_prefix . '_dynamic_pages' );
//			if( $export_dynamic_pages ) 
//			{
//				$export_dynamic_pages = base64_encode( serialize( $export_dynamic_pages ) );
//			}
//			
//			//export of dynamic elements
//			$export_dynamic_elements = get_option( $this->db_prefix . '_dynamic_elements' );
//			if( $export_dynamic_elements ) 
//			{
//				$export_dynamic_elements = base64_encode( serialize( $export_dynamic_elements ) );
//			}
  
			$info = sprintf( __( 'this is a base64 encoded option set created for the demo %s. If you choose to import the demo files with the help of the framework importer these options will also be imported', '' ), THEMENAME );

			$content = '';

//			$content .= "<?php \n\n";
//			$content .= "/*  {$info}  */\n\n";

			$content .= '$options = "';
			$content .=			$export;
			$content .= '";' . "\n";
			
//			echo '<pre>'."\n";
//			echo '$dynamic_pages = "';
//			print_r( $export_dynamic_pages );
//			echo '";</pre>';
//			
//			echo '<pre>'."\n";
//			echo '$dynamic_elements = "';
//			print_r( $export_dynamic_elements );
//			echo '";</pre>';
			
			$content .= "\n";
			$content .= '$widget_settings = "';
			$content .=			$widget_settings;
			$content .= '";' . "\n";

			if( ! empty( $fonts ) )
			{
				$content .= "\n";
				$content .= '$fonts = "';
				$content .=			$fonts;
				$content .= '";' . "\n";
			}

			if( isset( $_GET['layerslider'] ) )
			{
				$content .= "\n";
				$content .= '$layerslider = "';
				$content .=			$_GET['layerslider'];
				$content .= '";' . "\n";
			}
			
			//	generate downlaod file and exit !!
			$this->generate_export_file( $content, 'demo-file', 'txt' );
			exit();
			
		}

		/**
		 * Generates the output file
		 * 
		 * @param string $export_data
		 * @param string $which
		 * @param string $ext
		 */
		protected function generate_export_file( $export_data, $which = 'theme-settings', $ext = 'txt' )
		{
			$today = getdate();
			$today_str = $today['year'] . '-' . $today['mon'] . '-' . $today['mday'];

			$export_file = THEMENAME . '-' . $which . '-' . $today_str . '.' . $ext;

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . urlencode( $export_file ) );
			header( 'Content-Type: application/force-download' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Type: application/download' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			print $export_data;

			die();
		}

		/**
		 * 
		 * @param string $option_name
		 * @return string
		 */
		protected function export_option( $option_name )
		{
			$option = get_option( $option_name  );

			if(!empty($option))
			{
				$option = base64_encode( serialize( $option ) );
			}

			return $option;
		}
        
		/**
		 * 
		 * @return array
		 */
		protected function export_widgets()
		{
			global $wp_registered_widgets;
			
			$options = array();
			$saved_widgets = array();
			
			//get all registered widget option names
			foreach( $wp_registered_widgets as $registered )
			{
				if( isset( $registered['callback'] ) && isset( $registered['callback'][0] ) && isset( $registered['callback'][0]->option_name ) )
				{
					$options[] = $registered['callback'][0]->option_name;
				}
			}
			
			//check if the database options got anything stored but the default value _multiwidget
			foreach( $options as $key )
			{
				$widget = get_option( $key, array() );
				$treshhold = 1;
				
				if( array_key_exists( '_multiwidget', $widget ) ) 
				{
					$treshhold = 2;
				}
				
				if( $treshhold <= count( $widget ) )
				{
					$saved_widgets[ $key ] = $widget;
				}
			}
			
			//get sidebar positions
			$saved_widgets['sidebars_widgets'] = get_option('sidebars_widgets');
			
			return $saved_widgets;
			
		}
		
		/**
		 * 
		 * @param array $elements
		 * @param array $options
		 * @param string $subpage
		 * @param boolean $grouped
		 * @return array
		 */
		protected function export_array_generator( $elements, $options, $subpage, $grouped = false )
		{	

			$export = array();
			
			//iterate over all option page elements
			foreach( $elements as $element )
			{
				if( ( in_array( $element['slug'], $subpage ) || $grouped ) && isset( $element['id'] ) && isset( $options[ $element['id']] ) )
				{
					if( $element['type'] != 'group' )
					{
						if( isset( $element['subtype'] ) && ! is_array( $element['subtype'] ) )
						{
							//pass id-value and subtype
							$taxonomy = false;
							if( isset( $element['taxonomy'] ) ) 
							{
								$taxonomy = $element['taxonomy'];
							}
							
							$value = avia_backend_get_post_page_cat_name_by_id( $options[ $element['id'] ] , $element['subtype'], $taxonomy );
						}
						else
						{
							$value = $options[ $element['id'] ];
						}
						
						if( isset( $value ) )
						{
							$element['std'] = $value;
							$export[ $element['id'] ] = $element;
						}
					}
					else
					{
						$iterations = count( $options[ $element['id'] ] );
						$export[ $element['id'] ] = $element;
						
						for( $i = 0; $i < $iterations; $i++ )
						{
							$export[ $element['id'] ]['std'][ $i ] = $this->export_array_generator( $element['subelements'], $options[ $element['id'] ][ $i ], $subpage, true );
						}
					}
				}
			}
			
			return $export;
		}
		
	}
}


