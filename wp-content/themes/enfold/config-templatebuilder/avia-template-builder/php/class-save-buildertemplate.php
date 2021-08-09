<?php
/**
* Central Class for creating and saving template snippets via ajax
*/

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) { die( '-1' ); }

if ( ! class_exists( 'aviaSaveBuilderTemplate' ) ) 
{

	class aviaSaveBuilderTemplate
	{
		/**
		 *
		 * @var AviaBuilder 
		 */
		protected $builder;
	
		/**
		 * 
		 * @param AviaBuilder $builder
		 * @return type
		 */
		public function __construct( $builder )
		{
			$this->builder = $builder;
			
			if( ! isset( $_REQUEST['avia_export'] ) )
			{
				if( $this->builder->disable_drag_drop == true ) 
				{
					return;
				}
			}
			
			$this->actions_and_filters();
		}
		
		/** 
		* filter and action hooks
		*/
		protected function actions_and_filters()
		{
			$ver = AviaBuilder::VERSION;

			#js
			wp_enqueue_script( 'avia_template_save_js', AviaBuilder::$path['assetsURL'] . 'js/avia-template-saving.js', array( 'avia_element_js' ), $ver, true );

			#ajax
			add_action( 'wp_ajax_avia_ajax_save_builder_template', array( $this, 'handler_save_builder_template' ), 10, 0 );
			add_action( 'wp_ajax_avia_ajax_delete_builder_template', array( $this, 'handler_delete_builder_template' ), 10, 0 );
			add_action( 'wp_ajax_avia_ajax_fetch_builder_template', array( $this, 'handler_fetch_builder_template' ), 10, 0 );

			add_filter( 'avf_generate_export_file', array( $this, 'handler_generate_export_file' ), 10, 1 );
		}
        
		
		/** 
		* save button html
		*/
		public function create_save_button()
		{
			$names = $this->template_names();
			$list = '';

			if( empty( $names ) )
			{
				$list = "<li class='avia-no-template'>" . __( 'No Templates saved yet', 'avia_framework' ) . "</li>\n";
			}
			else
			{
				foreach( $names as $name )
				{
					$list .= "<li><a href='#'>{$name}</a><span class='avia-delete-template'></span></li>\n";
				}
			}

			$output  = '';
			$output .= "<div class='avia-template-save-button-container avia-attach-template-save avia-hidden-dropdown'>";
			$output .=		"<a class='open-template-button button' href='#open'>" . __( 'Templates', 'avia_framework' ) . '</a>';
			$output .=		"<div class='avia-template-save-button-inner'> <span class='avia-arrow'></span>";
			$output .=			"<a class='save-template-button button button-primary button-large' href='#save'>" . __( 'Save Entry as Template','avia_framework' ) . '</a>';
			$output .=			"<div class='avia-template-list-wrap'>";
			$output .=				"<span class='avia-templates-miniheader'>" . __( 'Load Template', 'avia_framework' ) . ':</span>';
			$output .=				'<ul>';
			$output .=                 $list;
			$output .=				'</ul>';
			$output .=			'</div>';
			$output .=		'</div>';
			$output .= '</div>';

			return $output;
		}

        
		/**
		 * Helper function that fetches all template names
		 *
		 */
		public function template_names()
		{
			$templates = $this->get_meta_values();
			$names = array();

			foreach( $templates as $template )
			{
				$name = explode( '}}}', $template );
				$names[] = str_replace( '{{{', '', $name[0] );
			}

			natcasesort( $names );
			return $names;
		}
        
        
		/**
		 * Ajax Function that checks if template can be saved
		 *
		 * @param string $name
		 * @param string $value
		 */
		public function handler_save_builder_template( $name = '%', $value = '' )
		{   
			check_ajax_referer( 'avia_nonce_save', 'avia-save-nonce' );

			$name  = isset( $_POST['templateName'] )  ? $_POST['templateName']  : $name;
			$value = isset( $_POST['templateValue'] ) ? $_POST['templateValue'] : $value;

			if( ! $this->save_builder_template( $name, $value ) )
			{
				 echo __( 'Template name already in use. Please delete the template with this name first or choose a different name', 'avia_framework' );
			}
			else
			{
				echo 'avia_template_saved';
			}

			die();
		}
		
		/**
		 * Saves the template. An existing template is not overwritten.
		 * 
		 * @since 4.6.4
		 * @param string $name
		 * @param string $value
		 * @return boolean
		 */
		protected function save_builder_template( $name, $value ) 
		{
			$id     = AviaStoragePost::get_custom_post('template_builder_snippets');

			$key = $this->generate_key( $name );
			$old = $this->get_meta_values( $key );

			if( ! empty( $old ) )
			{
			   return false;
			}

			Avia_Builder()->get_shortcode_parser()->set_builder_save_location( 'none' );
			$value = ShortcodeHelper::clean_up_shortcode( $value );

			update_post_meta( $id, $key, '{{{' . $name . '}}}' . $value );

			return true;
		}

		/**
		 * Ajax Function that deletes a template
		 *
		 * @param string $name
		 */
		public function handler_delete_builder_template( $name = '%' )
		{
			check_ajax_referer( 'avia_nonce_save', 'avia-save-nonce' );

			$name = isset( $_POST['templateName'] ) ? $_POST['templateName'] : $name;
			$id = AviaStoragePost::get_custom_post( 'template_builder_snippets' );

			$key = $this->generate_key( $name );
			$result = delete_post_meta( $id, $key );

			echo 'avia_template_deleted';
			die();
		}
		
		/**
		 * Retrieve a saved template.
		 * If called via ajax the JS will then insert it into the canvas area.
		 *
		 * @param string|false $template_name
		 * @return string
		 */
		public function handler_fetch_builder_template( $template_name = false )
		{
			$error = true;
			
			if( false === $template_name )
			{
				$name = isset( $_POST['templateName'] ) ? $_POST['templateName'] : false;
			}
			else
			{
				$name = $template_name;
			}
			
			if( ! empty( $name ) ) 
			{
				$key = $this->generate_key( $name );
				$template = $this->get_meta_values( $key );
            
				if( ! empty( $template ) ) 
				{
					$error = false;
				}
			}
			
			if( $error )
			{
				$return = 'avia_fetching_error';

				if( false === $template_name )
				{
					echo $return;
					die();
				}
			}
			else
			{
				$return = str_replace( '{{{' . $name . '}}}', '', $template[0] );

				if( false === $template_name )
				{
					$return = $this->builder->text_to_interface( $return );
					echo $return;
					die();
				}
			}

			return $return;
		}
		
		
		/**
		 * Helper function that creates the post meta key
		 *
		 * @param string $name
		 * @return string
		 */
		protected function generate_key( $name )
		{
			return '_avia_builder_template_' . str_replace( ' ', '_', strtolower( $name ) );
		}
        
        
		/**
		 * Helper function that fetches all meta values with a specific key (cross post)
		 *
		 */
		protected function get_meta_values( $key = '_avia_builder_template_%' ) 
		{
			global $wpdb;

			if( empty( $key ) ) 
			{
				return;
			}

			$compare_by = strpos( $key, '%' ) !== false ? 'LIKE' : '=';
			$id = AviaStoragePost::get_custom_post( 'template_builder_snippets' );

			$r = $wpdb->get_col( $wpdb->prepare( "
					SELECT meta_value FROM {$wpdb->postmeta}
					WHERE  meta_key {$compare_by} '%s'
					AND post_id = '%s'
				", $key, $id) );

			return $r;
		}

		/**
		 * Returns the export file if requested
		 * 
		 * @since 4.6.4
		 * @param null|array
		 * @return null|array
		 */
		public function handler_generate_export_file( $param = null ) 
		{
			//	In case already handled
			if( ! is_null( $param ) )
			{
				return $param;
			}
			
			if( ! isset( $_REQUEST['avia_generate_alb_templates_file'] ) )
			{
				return $param;
			}
			
			$names = $this->template_names();
			$content = array(
							'__file_content'	=> 'alb-saved-templates'
						);
			
			foreach( $names as $name ) 
			{
				/**
				 * Skip a template from exporting
				 * 
				 * @since 4.6.4
				 * @param boolean
				 * @param string $name
				 * @return boolean					
				 */
				if( false !== apply_filters( 'avf_skip_export_alb_template', false, $name ) )
				{
					continue;
				}
				
				$template = $this->handler_fetch_builder_template( $name );
				if( $template != 'avia_fetching_error' )
				{
					$content[ $name ] = $template;
				}
			}
			
			if( empty( $content ) )
			{
				$content = '';
			}
			else
			{
				$content = base64_encode( serialize( $content ) );
			}
			
			$file = array(
						'name'		=> 'alb-saved-templates',
						'content'	=> $content
					);
			
			return $file;
		}
				
		/**
		 * Imports templates from an export file.
		 * Existing templates will be appended with an extension to the template name.
		 * 
		 * @since 4.6.4
		 * @param string $contents
		 * @return string
		 * @throws Exception
		 */
		public function import_saved_templates( $contents ) 
		{
			$templates = unserialize( base64_decode( $contents ) );
			
			if( ! is_array( $templates ) || ! isset( $templates['__file_content'] ) || ( $templates['__file_content'] != 'alb-saved-templates' ) )
			{
				throw new Exception( __( 'Illegal file was imported. No templates were added.', 'avia_framework' ) );
			}
			
			unset( $templates['__file_content'] );
			
			if( empty( $templates ) )
			{
				throw new Exception( __( 'File did not contain any templates. No templates were added.', 'avia_framework' ) );
			}
			
			$stored_names = $this->template_names();
			$imported = 0;
			
			foreach( $templates as $name => $template ) 
			{
				/**
				 * Skip a template from exporting
				 * 
				 * @since 4.6.4
				 * @param boolean
				 * @param string $name
				 * @param string $template
				 * @return boolean					
				 */
				if( false !== apply_filters( 'avf_skip_import_alb_template', false, $name, $template ) )
				{
					continue;
				}
				
				$i = 0;
				$new = $name;
				while( in_array( $new, $stored_names ) )
				{
					$i++;
					$new = $name . '-' . $i;
				}
				
				if( $this->save_builder_template( $new, $template ) )
				{
					$imported++;
					$stored_names[] = $new;
				}

			}
			
			return sprintf( __( 'Successfull: %d templates could be imported.', 'avia_framework' ), $imported );
		}
	

	} // end class

} // end if !class_exists