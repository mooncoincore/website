<?php
namespace aviaBuilder\base;

/**
 * Base class implements methods to handle modal popup templates
 *
 * @added_by Günter
 * @since 4.8.4
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if( ! class_exists( __NAMESPACE__ . '\aviaPopupTemplatesBase' ) )
{
	class aviaPopupTemplatesBase 
	{
		/**
		 * Array of dynamic templates added on the fly
		 *		template_id  =>  array();
		 * 
		 * @since 4.6.4
		 * @var array
		 */
		protected $dynamic_templates;
		
		/**
		 * Array of HTML codesnippets
		 * 
		 * @since 4.6.4
		 * @var array 
		 */
		protected $html_templates;
		
		/**
		 * @since 4.6.4
		 */
		protected function __construct() 
		{
			$this->dynamic_templates = array(); 
			$this->html_templates = array();
		}
		
		/**
		 * @since 4.6.4
		 */
		public function __destruct() 
		{
			unset( $this->dynamic_templates );
			unset( $this->html_templates );
		}

		/**
		 * Main entry function:
		 * ====================
		 * 
		 * Replaces predefined templates for easier maintainnance of code
		 * Recursive function. Also supports nested templates.
		 * 
		 * @since 4.5.6.1
		 * @param array $elements
		 * @return array
		 */
		public function replace_templates( array $elements )
		{
			if( empty( $elements ) )
			{
				return $elements;
			}
			
			$start_check = true;
			
			while( $start_check )
			{
				$offset = 0;
				foreach( $elements as $key => $element ) 
				{
					if( isset( $element['subelements'] ) )
					{
						$elements[ $key ]['subelements'] = $this->replace_templates( $element['subelements'] );
					}
					
					if( ! isset( $element['type'] ) || $element['type'] != 'template' )
					{
						$offset++;
						if( $offset >= count( $elements ) )
						{
							$start_check = false;
							break;
						}
						continue;
					}

					$replace = $this->get_template( $element );
					if( false === $replace )
					{
						$offset++;
						if( $offset >= count( $elements ) )
						{
							$start_check = false;
							break;
						}
						continue;
					}

					array_splice( $elements, $offset, 1, $replace );
					break;
				}
			}
			
			return $elements;
		}

		/**
		 * Returns the array elements to replace the template array element.
		 * Dynamic templates override predefined.
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @param boolean
		 * @return array|false
		 */
		protected function get_template( array $element, $parent = false )
		{
			if( ! isset( $element['template_id'] ) )
			{
				return false;
			}
			
			if( array_key_exists( $element['template_id'], $this->dynamic_templates ) )
			{
				if( $parent === false || ! method_exists( $this, $element['template_id'] ) )
				{
					$result = $this->get_dynamic_template( $element );
					return $result;
				}
			}
			
			if( ! method_exists( $this, $element['template_id'] ) )
			{
				return false;
			}
			
			$result = call_user_func_array( array( $this, $element['template_id'] ), array( $element ) );
			return $result;
		}
		
		/**
		 * Returns if a template exists
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @return string|false				false | 'predefined' | 'dynamic' | 'dynamic and fixed'
		 */
		public function template_exists( $template_id )
		{
			$exist = false;
			
			if( array_key_exists( $template_id, $this->dynamic_templates ) )
			{
				$exist = 'dynamic';
			}
			
			if( method_exists( $this, $template_id ) )
			{
				$exist = false === $exist ? 'predefined' : 'dynamic and predefined';
			}
			
			return $exist;
		}

		/**
		 * Add a dynamic template
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @param array $template_data
		 * @param boolean $ignore_debug_notice
		 */
		public function register_dynamic_template( $template_id, array $template_data, $ignore_debug_notice = false )
		{
			if( defined( 'WP_DEBUG' ) && WP_DEBUG && false === $ignore_debug_notice )
			{
				$exist = $this->template_exists( $template_id );
				if( false !== $exist )
				{
					error_log( sprintf( __( 'Already existing template %1$s is overwritten (%2$s). Make sure this is intended.', 'avia_framework' ), $template_id, $exist )  );
				}
			}
			
			$this->dynamic_templates[ $template_id ] = $template_data;
		}
		
		/**
		 * Adds a template to the list of available templates.
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @param mixed $template_data
		 * @param boolean $overwrite
		 * @return boolean
		 */
		public function register_html_template( $template_id, $template_data, $overwrite = false )
		{
			if( array_key_exists( $template_id, $this->html_templates ) && ( false === $overwrite ) )
			{
				return false;
			}
			
			$this->html_templates[ $template_id ] = $template_data;
			return true;
		}
		
		/**
		 * Returns the stored content. If template does not exist '' is returned.
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @return string
		 */
		public function get_html_template( $template_id )
		{
			return isset( $this->html_templates[ $template_id ] ) ? $this->html_templates[ $template_id ] : '';
		}

		/**
		 * Removes a registered dynamic template
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @return boolean
		 */
		public function deregister_dynamic_template( $template_id )
		{
			if( ! isset( $this->dynamic_templates[ $template_id ] ) )
			{
				return false;
			}
			
			unset( $this->dynamic_templates[ $template_id ] );
			return true;
		}

		/**
		 * Return content of template.
		 * 
		 * if 'templates_include'	=> add content of all templates
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array|false
		 */
		protected function get_dynamic_template( array $element )
		{
			$template_content = $this->dynamic_templates[ $element['template_id'] ];
			
			$result = $this->get_templates_to_include( $template_content, $element );
			if( false !== $result )
			{
				return $result;
			}
			
			return $template_content;
		}
		
		/**
		 * Returns all templates to include
		 * 
		 * @since 4.6.4
		 * @param array $template
		 * @param array|null $parent_template
		 * @return array|false
		 */
		protected function get_templates_to_include( array $template, $parent_template = null )
		{
			if( empty( $template['templates_include'] ) )
			{
				return false;
			}
			
			$attr = is_null( $parent_template ) ? $template : $parent_template;
			unset( $attr['template_id'] );
			unset( $attr['templates_include'] );
			
			$result = array();
					
			foreach( $template['templates_include'] as $sub_template ) 
			{
				if( false !== $this->template_exists( $sub_template ) )
				{
					$temp = array(	
									'template_id'   => $sub_template,
								);		
					
					foreach( $attr as $key => $value ) 
					{
						$temp[ $key ] = $value;
					}
					
					$result[] = $temp;
				}
			}
			
			return $result;
		}
	}

}