<?php
namespace aviaBuilder\traits;

/**
 * Helper to identify named colors
 *
 * @author		Günter
 * @since 4.8.4
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if( ! trait_exists( __NAMESPACE__ . '\avScTraitNamedColors' ) )
{
	trait scNamedColors
	{
		/**
		 * Holds name of named button colors 
		 * 
		 * @since 4.8.4
		 * @var array
		 */
		private $named_button_colors;
		
		/**
		 * @since 4.8.4
		 */
		protected function _construct_scNamedColors() 
		{
			$this->named_button_colors = null;
		}
		
		/**
		 * @since 4.8.4
		 */
		protected function _destruct_scNamedColors()
		{
			unset( $this->named_button_colors );
		}

		/**
		 * Initialise the array
		 * 
		 * @since 4.8.4
		 */
		private function init_named_button_colors()
		{
			$this->named_button_colors = array(
						'light',
						'dark',
						'theme-color',
						'theme-color-highlight',
						'theme-color-subtle'
					);
		}
		
		/**
		 * Checks if a given color is a special named button color that is defined in dynamic css
		 * depending on theme options settings
		 * 
		 * @since 4.8.4
		 * @param string $color_name
		 * @return boolean
		 */
		protected function is_special_button_color( $color_name ) 
		{
			if( ! is_array( $this->named_button_colors ) )
			{
				$this->init_named_button_colors();
			}
			
			return in_array( $color_name, $this->named_button_colors );
		}
		
	}
}
