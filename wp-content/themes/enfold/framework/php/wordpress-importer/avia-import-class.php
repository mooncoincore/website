<?php
if ( ! defined( 'AVIA_FW' ) )	{	exit( 'No direct script access allowed' );	}

if( ! class_exists( 'avia_wp_import' ) )
{
	
	class avia_wp_import extends WP_Import
	{
	//	var $preStringOption; 
	//	var $results;
	//	var $getOptions;
	//	var $saveOptions;
	//	var $termNames;

		/**
		 * Handles import of options saved from a demo
		 * 
		 * @since 4.8.2							support fot txt files including options similar to php file
		 * @param string $option_file				full path to php or txt file
		 * @param boolean $import_only
		 * @return boolean
		 */
		public function saveOptions( $option_file, $import_only = false )
		{	
			if( false !== strpos( $option_file, '.php' ) )
			{
				@include_once( $option_file );
			}
			else if( false !== strpos( $option_file, '.txt' ) )
			{
				$file_content = $this->read_txt_file_contents( $option_file );
				extract( $file_content );
			}

//			switch( $import_only )
//			{
//				case 'options': 
//					$dynamic_pages = $dynamic_elements = false; 
//					break;
//				case 'dynamic_pages': 
//					$options = $dynamic_elements = false; 
//					break;
//				case 'dynamic_elements': 
//					$options = $dynamic_pages = false; 
//					break;
//			}
		
//			if( ! isset( $options ) && ! isset( $dynamic_pages ) && ! isset( $dynamic_elements ) ) 
			if( ! isset( $options ) )
			{ 
				return false; 
			}

			$options = unserialize( base64_decode( $options ) );
//			$dynamic_pages = unserialize( base64_decode( $dynamic_pages ) );
//			$dynamic_elements = unserialize( base64_decode( $dynamic_elements ) );
		
			global $avia;

			if( ! isset( $database_option ) || ! is_array( $database_option ) )
			{
				$database_option = array();
			}

			if( is_array( $options ) )
			{
				foreach( $avia->option_pages as $page )
				{
					if( ! array_key_exists( $page['parent'], $options ) )
					{
						$database_option[ $page['parent'] ] = array();
					}
					else
					{
						$database_option[ $page['parent'] ] = $this->extract_default_values( $options[ $page['parent'] ], $page, $avia->subpages ) ;
					}
				}
			}

			if( ! empty( $database_option ) )
			{
				update_option( $avia->option_prefix, $database_option );
			}
		
//			if( ! empty( $dynamic_pages ) )
//			{
//				update_option($avia->option_prefix.'_dynamic_pages', $dynamic_pages );
//			}
//		
//			if( ! empty( $dynamic_elements ) )
//			{
//				update_option($avia->option_prefix.'_dynamic_elements', $dynamic_elements );
//			}
		
			if( ! empty( $fonts ) )
			{
				$this->import_iconfont( $fonts );
			}

			if( ! empty( $layerslider ) )
			{
				$this->import_layerslides( $layerslider );
			}


			if( ! empty( $widget_settings ) )
			{
				$widget_settings = unserialize( base64_decode( $widget_settings ) );
				if( ! empty( $widget_settings ) )
				{
					foreach( $widget_settings as $key => $setting )
					{
						update_option( $key, $setting );
					}
				}
			}
		
		}
		
		/**
		 * As we now support txt for demo options file we have to extract the content and return the array
		 * To be backwards comp with already existing demos we support the old syntax:
		 *		$key = '......';
		 * 
		 * @since 4.8.2
		 * @param string $file
		 * @return array
		 */
		protected function read_txt_file_contents( $file ) 
		{
			$content = @file_get_contents( $file );
			$content = trim( $content );
			
			if( empty( $content ) )
			{
				return array();
			}
			
			$content = str_replace( "\r", "\n", $content );
			$lines = explode( "\n", $content );
			
			$options = array();
			
			foreach( $lines as $key => $line ) 
			{
				$sep = strpos( $line, '=' );
				if( false ===  $sep )
				{
					continue;
				}
				
				$opt_key = trim( substr( $line, 0, $sep ) );
				$opt_val = trim( substr( $line, $sep + 1 ) );
				
				$opt_key = str_replace( '$', '', $opt_key );
				
				$len = strlen( $opt_val );
				
				if( strpos( $opt_val, '"' ) !== 0 )
				{
					continue;
				}
				if( strrpos( $opt_val, '";' ) !== ( $len - 2 ) )
				{
					continue;
				}
				
				$options[ $opt_key ] = substr( $opt_val, 1, $len - 3 );
			}

			return $options;
		}


		/**
		 * 
		 * @param string $layerslider
		 */
		protected function import_layerslides( $layerslider )
		{
			@ini_set( 'max_execution_time', 300 );

			$slider = urlencode( $layerslider );
			$remoteURL = 'https://kriesi.at/themes/wp-content/uploads/avia-sample-layerslides/' . $slider . '.zip';

			$uploads = wp_upload_dir();
			$downloadPath = $uploads['basedir'] . '/lsimport.zip';

			// Download package
			$request = wp_remote_post( $remoteURL, array(
							'method'	=> 'POST',
							'timeout'	=> 300,
							'body'		=> array()
						));

			$zip = wp_remote_retrieve_body( $request );

			if( ! $zip ) 
			{
				die( __( "LayerSlider couldn't download your selected slider. Please check LayerSlider -> System Status for potential issues. The WP Remote functions may be unavailable or your web hosting provider has to allow external connections to our domain.", 'avia_framework' ) );
			}

			// Save package
			if( ! file_put_contents( $downloadPath, $zip ) ) 
			{
				die( __( "LayerSlider couldn't save the downloaded slider on your server. Please check LayerSlider -> System Status for potential issues. The most common reason for this issue is the lack of write permission on the /wp-content/uploads/ directory.", 'avia_framework' ) );
			}

			// Load importUtil & import the slider
			include LS_ROOT_PATH . '/classes/class.ls.importutil.php';
			$import = new LS_ImportUtil( $downloadPath );

			// Remove package
			if( file_exists( $downloadPath ) )
			{
				unlink( $downloadPath );
			}
		}


		/**
		 * 
		 * @param string $new_fonts
		 */
		protected function import_iconfont( $new_fonts )
		{
			@ini_set( 'max_execution_time', 300 );

			//update iconfont option 
			$key = 'avia_builder_fonts';
			$fonts_old = get_option( $key );

			if( empty( $fonts_old ) ) 
			{
				$fonts_old = array();
			}

			$new_fonts = unserialize( base64_decode( $new_fonts ) );
			$merged_fonts = array_merge( $new_fonts , $fonts_old );
			$files_to_copy = array( 'config.json', 'FONTNAME.svg', 'FONTNAME.ttf', 'FONTNAME.eot', 'FONTNAME.woff', 'FONTNAME.woff2' );	
			update_option( $key, $merged_fonts );

			$http 			= new WP_Http();
			$font_uploader 	= new avia_font_manager();
			$paths			= $font_uploader->paths;

			//if a temp dir already exists remove it and create a new one
			if( ! is_dir( $paths['tempdir'] ) )
			{
				$fontdir = avia_backend_create_folder( $paths['tempdir'], false );
				if( ! $fontdir ) 
				{
					echo __( 'Wasn\'t able to create the folder for font files', 'avia_framework' );
				}
			}

			//download iconfont files into uploadsfolder
			foreach( $new_fonts as $font_name => $font )
			{
				if( empty( $fonts_old[ $font_name ] ) )
				{
					//folder name
					$new_font_folder = trailingslashit( $paths['tempdir'] );

					//if a sub dir already exists remove it and create a new one
					if( is_dir( $new_font_folder ) ) 
					{
						$font_uploader->delete_folder( $new_font_folder );
					}

					$subpdir = avia_backend_create_folder( $new_font_folder, false );
					if( ! $subpdir )
					{ 
						echo __( 'Wasn\'t able to create sub-folder for font files', 'avia_framework' );
					}

					//iterate over files on remote server and create the same ones on this server
					foreach( $files_to_copy as $file_to_copy )
					{
						$file_to_copy 	= str_replace( 'FONTNAME', $font_name, $file_to_copy );
						$origin_url 	= $font['origin_folder'] . trailingslashit( $font['folder'] ) . $file_to_copy;
						$new_path		= trailingslashit( $new_font_folder ) . $file_to_copy;
						$headers 		= $http->request( $origin_url, array( 'stream' => true, 'filename' => $new_path ) );
					}
					
					//create a config file
					$font_uploader->font_name = $font_name;
					$font_uploader->create_config();
				}
			}
		}
	
		/**
		 *  Extracts the default values from the option_page_data array in case no database savings were done yet
		 *  The functions calls itself recursive with a subset of elements if groups are encountered within that array
		 * 
		 * @param array $elements
		 * @param array $page
		 * @param array $subpages
		 * @return array
		 */
		public function extract_default_values( array $elements, array $page, array $subpages )
		{
	
			$values = array();

			foreach( $elements as $element )
			{
					if( isset( $element['type'] ) && ( $element['type'] == 'group') )
				{	
					if( ! is_array( $element['std'] ) )
					{
						//	Fallback situation in case theme option std value is not an array
						$values[ $element['id'] ][0] = array();
					}
					else
					{
						$iterations = count( $element['std'] );

						for( $i = 0; $i < $iterations; $i++ )
						{
							$values[ $element['id'] ][ $i ] = $this->extract_default_values( $element['std'][ $i ], $page, $subpages );
						}
					}
				}
				else if( isset( $element['id'] ) )
				{
					if( ! isset($element['std'] ) ) 
					{
						$element['std'] = '';
					}

					if( $element['type'] == 'select' && ! is_array( $element['subtype'] ) )
					{	
						if( ! isset( $element['taxonomy'] ) ) 
						{
							$element['taxonomy'] = 'category';
						}

						$values[ $element['id'] ] = $this->getSelectValues( $element['subtype'], $element['std'], $element['taxonomy'] );
					}
					else
					{
						$values[ $element['id'] ] = $element['std'];
					}
				}
			}

			return $values;
		}

		/**
		 * Filters the imported options:
		 * 
		 * If filter_xxx is used the original options are kept and only options set in filter_xxx are copied.
		 * If skip_xxx is used the imported option values are replaced by the old ones.
		 * 
		 * Filter has priority to skip.
		 * 
		 * If no filter the original array is returned.
		 * 
		 * 
		 * $filter = array(
		 *			filter_tabs		=> parent:slug,parent:slug
		 *			filter_values	=> parent:option_name,parent:option_name
		 *			skip_tabs		=> parent:slug,parent:slug
		 *			skip_values		=> parent:option_name,parent:option_name
		 *		)
		 * 
		 * @since 4.6.4
		 * @param array $database_option
		 * @param array $imported_options
		 * @param array $filter
		 * @return array
		 */
		public function filter_imported_options( array $database_option, array $imported_options, array $filter ) 
		{
			global $avia;

			if( empty( $filter ) )
			{
				return $database_option;
			}

			$filter_keys = array( 'filter_tabs', 'filter_values', 'skip_tabs', 'skip_values' );

			$filter_stat = false;

			foreach( $filter_keys as $key ) 
			{
				if( isset( $filter[ $key ] ) && trim( $filter[ $key ] ) != '' )
				{
					$filter_stat = false !== strpos( $key, 'filter' ) ? 'filter' : 'skip';
					break;
				}
			}

			if( false === $filter_stat )
			{
				return $database_option;
			}

			//	Cleanup array
			foreach( $filter_keys as $key ) 
			{
				if( isset( $filter[ $key ] ) && trim( $filter[ $key ] ) == '' )
				{
					unset( $filter[ $key ] );
				}
			}

			$avia_options = is_array( $avia->options ) ? $avia->options : array();
			if( 'filter' == $filter_stat )
			{
				$new_options = $avia_options;
			}
			else
			{
				$new_options = $database_option;
			}
		
			foreach( $filter_keys as $operation ) 
			{
				if( ! isset( $filter[ $operation ] ) )
				{
					continue;
				}

				$sources = explode( ',', $filter[ $operation ] );

				foreach( $sources as $source ) 
				{
					if( false === strpos( $source, ':' ) )
					{
						$source = ':' . $source;
					}

					$source = explode( ':', $source, 2 );

					$parent = trim( $source[0] );

					if( false !== strpos( $operation, 'value' ) )
					{
						$id = trim( $source[1] );

						$default = $this->get_std_value( $parent, $id );

						if( false !== strpos( $operation, 'filter' ) )
						{
							$new_options[ $parent ][ $id ] = isset( $database_option[ $parent ][ $id ] ) ? $database_option[ $parent ][ $id ] : $default;
						}
						else
						{
							$new_options[ $parent ][ $id ] = isset( $avia_options[ $parent ][ $id ] ) ? $avia_options[ $parent ][ $id ] : $default;
						}
						continue;
					}

					if( ! isset( $imported_options[ $parent ] ) || ! is_array( $imported_options[ $parent ] ) )
					{
						continue;
					}

					$subpage = trim( $source[1] );

					foreach( $imported_options[ $parent ] as $element ) 
					{
						if( isset( $element['id'] ) && isset( $element['slug'] ) && $element['slug'] == $subpage )
						{
							$id = $element['id'];
							$default = $this->get_std_value( $parent, $id );

							if( false !== strpos( $operation, 'filter' ) )
							{
								$new_options[ $parent ][ $id ] = isset( $database_option[ $parent ][ $id ] ) ? $database_option[ $parent ][ $id ] : $default;
							}
							else
							{
								$new_options[ $parent ][ $id ] = isset( $avia_options[ $parent ][ $id ] ) ? $avia_options[ $parent ][ $id ] : $default;
							}
						}
					}
				}
			}

			return $new_options;
		}

		/**
		 * Get the std value for an option as fallback in case element is missing
		 * 
		 * @since 4.6.4
		 * @param string $parent
		 * @param string $id
		 * @return string
		 */
		protected function get_std_value( $parent, $id )
		{
			global $avia;

			if( ! isset( $avia->subpages[ $parent ] ) || ! is_array( $avia->subpages[ $parent ] ) )
			{
				return '';
			}

			$subpages = $avia->subpages[ $parent ];

			foreach( $avia->option_page_data as $key => $element ) 
			{
				if( isset( $element['slug'] ) && in_array( $element['slug'], $subpages ) )
				{
					if( isset( $element['id'] ) && $element['id'] == $id )
					{
						return ( isset( $element['std'] ) ) ? $element['std'] : '';
					}
				}
			}

			return '';
		}

		/**
		 * 
		 * @param string $type
		 * @param string $name
		 * @param string $taxonomy
		 * @return string|int
		 */
		protected function getSelectValues( $type, $name, $taxonomy )
		{
			switch( $type )
			{
				case 'page':
				case 'post':	
					$the_post = get_page_by_title( $name, 'OBJECT', $type );
					if( isset( $the_post->ID ) ) 
					{
						return $the_post->ID;
					}
					break;

				case 'cat':
					if( ! empty( $name ) )
					{
						$return = array();

						foreach( $name as $cat_name )
						{	
							if( $cat_name )
							{	
								if( ! $taxonomy ) 
								{
									$taxonomy = 'category';
								}
								$the_category = get_term_by( 'name', $cat_name, $taxonomy );

								if( $the_category ) 
								{
									$return[] = $the_category->term_id;
								}
							}
						}
						if( ! empty( $return ) )
						{
							if( ! isset( $return[1] ) )
							{
								 $return = $return[0];
							}
							else
							{
								$return = implode( ',', $return );
							}
						}
						return $return;
					}
					break;
			}
		}

		/*
		
		/**
		 * Renames existing menus so that newly added menu items are not appended
		 */
		public function rename_existing_menus()
		{
			$menus = wp_get_nav_menus();

			if( ! empty( $menus) )
			{	
				//wp_delete_nav_menu($menu->slug);

				foreach( $menus as $menu )
				{
					$updated = false;
					$i = 0;

					while( ! is_numeric( $updated ) ) //try to update the menu name. if it exists increment the number and thereby change the name
					{
						$i++;
						$args['menu-name'] = __( 'Previously used menu','avia_framework' ) . ' ' . $i;
						$args['description'] = $menu->description;
						$args['parent'] = $menu->parent;

						$updated = wp_update_nav_menu_object( $menu->term_id, $args ); //return a number on success or wp_error object if menu name exists

						//fallback, prevents infinite loop if something weird happens
						if( $i > 100 ) 
						{
							$updated = 1;
						}
					}
				}
			}
		}

		/**
		 * 
		 * 
		 */
		public function set_menus()
		{
			global $avia_config;

			//get all registered menu locations
			$locations = get_theme_mod( 'nav_menu_locations' );

			//get all created menus
			$avia_menus = wp_get_nav_menus();


			if( ! empty( $avia_menus ) && ! empty( $avia_config['nav_menus'] ) )
			{
				$avia_navs = array();
				
				foreach( $avia_config['nav_menus'] as $key => $nav_menu )
				{
					if( isset( $nav_menu['html'] ) )
					{
						$avia_navs[ $key ] = $nav_menu['html'];
					}
					else
					{
						$avia_navs[ $key ] = $nav_menu;
					}
				}

				foreach( $avia_menus as $avia_menu )
				{
					//check if we got a menu that corresponds to the Menu name array ($avia_config['nav_menus']) we have set in functions.php
					// a partial match like 'Main Menu' 'Main', or 'Secondary' is enough
					if( is_object( $avia_menu ) )
					{
						foreach( $avia_navs as $key => $value )
						{
							$value = strtolower( $value );
							$name = strtolower( $avia_menu->name );

							if( strpos( $value, $name ) !== false )
							{
								$locations[ $key ] = $avia_menu->term_id;
							}
						}
					}
				}
			}

			//update the theme
			set_theme_mod( 'nav_menu_locations', $locations );
		}


		/**
		 * 
		 * @param array $item
		 * @return void
		 */
		function process_menu_item( $item ) 
		{
			@ini_set('max_execution_time', 300);

			// skip draft, orphaned menu items
			if ( 'draft' == $item['status'] )
			{
				return;
			}

			$menu_slug = false;
			if ( isset( $item['terms'] ) ) 
			{
				// loop through terms, assume first nav_menu term is correct menu
				foreach ( $item['terms'] as $term ) 
				{
					if ( 'nav_menu' == $term['domain'] ) 
					{
						$menu_slug = $term['slug'];
						break;
					}
				}
			}

			// no nav_menu term associated with this menu item
			if ( ! $menu_slug ) 
			{
				_e( 'Menu item skipped due to missing menu slug', 'wordpress-importer' );
				echo '<br />';
				return;
			}

			$menu_id = term_exists( $menu_slug, 'nav_menu' );
			if ( ! $menu_id ) 
			{
				printf( __( 'Menu item skipped due to invalid menu slug: %s', 'wordpress-importer' ), esc_html( $menu_slug ) );
				echo '<br />';
				return;
			} 
			else 
			{
				$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
			}

			foreach( $item['postmeta'] as $meta )
			{
				${$meta['key']} = $meta['value']; //kriesi mod: php 7 fix - added braces
			}

			if( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[ intval( $_menu_item_object_id ) ] ) ) 
			{
				$_menu_item_object_id = $this->processed_terms[ intval( $_menu_item_object_id ) ];
			} 
			else if( 'post_type' == $_menu_item_type && isset( $this->processed_posts[ intval( $_menu_item_object_id ) ] ) ) 
			{
				$_menu_item_object_id = $this->processed_posts[ intval( $_menu_item_object_id ) ];
			} 
			else if( 'custom' != $_menu_item_type ) 
			{
				// associated object is missing or not imported yet, we'll retry later
				$this->missing_menu_items[] = $item;
				return;
			}

			if( isset( $this->processed_menu_items[ intval( $_menu_item_menu_item_parent ) ] ) ) 
			{
				$_menu_item_menu_item_parent = $this->processed_menu_items[ intval( $_menu_item_menu_item_parent ) ];
			} 
			else if( $_menu_item_menu_item_parent ) 
			{
				$this->menu_item_orphans[intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
				$_menu_item_menu_item_parent = 0;
			}

			// wp_update_nav_menu_item expects CSS classes as a space separated string
			$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
			
			if( is_array( $_menu_item_classes ) )
			{
				$_menu_item_classes = implode( ' ', $_menu_item_classes );
			}

			$args = array(
					'menu-item-object-id'	=> $_menu_item_object_id,
					'menu-item-object'		=> $_menu_item_object,
					'menu-item-parent-id'	=> $_menu_item_menu_item_parent,
					'menu-item-position'	=> intval( $item['menu_order'] ),
					'menu-item-type'		=> $_menu_item_type,
					'menu-item-title'		=> $item['post_title'],
					'menu-item-url'			=> $_menu_item_url,
					'menu-item-description'	=> $item['post_content'],
					'menu-item-attr-title'	=> $item['post_excerpt'],
					'menu-item-target'		=> $_menu_item_target,
					'menu-item-classes'		=> $_menu_item_classes,
					'menu-item-xfn'			=> $_menu_item_xfn,
					'menu-item-status'		=> $item['status']
				);

			$id = wp_update_nav_menu_item( $menu_id, 0, $args );
			if ( $id && ! is_wp_error( $id ) )
				$this->processed_menu_items[intval($item['post_id'])] = (int) $id;

			/*kriesi mod: necessary to add custom post meta to the import*/
			if ( $id && ! is_wp_error( $id ) )
			{
				foreach( $item['postmeta'] as $itemkey => $meta )
				{
					$key = str_replace( '_', '-', ltrim( $meta['key'], '_' ) );

					/*do a check: only add keys that do not exist - parent menu item is a special case that must be checked as well*/
					if( ! array_key_exists( $key, $args ) && $key != 'menu-item-menu-item-parent' )
					{
						if( ! empty( $meta['value'] ) )
						{
							update_post_meta( $id, $meta['key'], $meta['value'] );
						}
					}
				}
			}
			/*end mod*/
		
		}
	}

}	//	end class_exists

