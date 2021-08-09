<?php
namespace aviaBuilder\traits;

/**
 * Helper to create styles for ALB button elements:
 *		- av_button
 *		- av_buttonrow -> av_buttonrow_item
 *		- av_button_big
 *
 * @author		Günter
 * @since 4.8.4
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if( ! trait_exists( __NAMESPACE__ . '\scButtonStyles' ) )
{
	trait scButtonStyles
	{
		/**
		 * 
		 * @var array
		 */
		protected $default_btn_atts;
		
		/**
		 * @since 4.8.4
		 */
		protected function _construct_scButtonStyles() 
		{
			$this->default_btn_atts = null;
		}
		
		/**
		 * @since 4.8.4
		 */
		protected function _destruct_scButtonStyles()
		{
			unset( $this->default_btn_atts );
		}
		
		/**
		 * Returns the default settings for a button
		 * 
		 * @since 4.8.4
		 * @return array
		 */
		protected function get_default_btn_atts() 
		{
			if( ! is_array( $this->default_btn_atts ) )
			{
				$this->default_btn_atts = array(
						'label'					=> 'Click me', 
						'link'					=> '', 
						'link_target'			=> '',
						'size'					=> 'small',
						'position'				=> 'center',
						'icon_select'			=> 'yes',
						'icon'					=> '', 
						'font'					=> '',
						'icon_hover'			=> '',
						'label_display'			=> '',
						'title_attr'			=> '',
						'color'					=> 'theme-color',
						'custom_bg'				=> '#444444',
						'custom_font'			=> '#ffffff',

						'color_options'			=> '',		//	added 4.7.5.1
						'btn_color_bg'			=> 'theme-color',		
						'btn_custom_grad_direction'	=> 'vertical',
						'btn_custom_grad_1'		=> '',
						'btn_custom_grad_2'		=> '',
						'btn_custom_grad_3'		=> '',
						'btn_color_bg_hover'	=> 'theme-color',
						'btn_color_font'		=> 'custom',
						'btn_custom_bg'			=> '#444444',
						'btn_custom_bg_hover'	=> '#444444',
						'btn_custom_font'		=> '#ffffff',
						'btn_color_font_hover'	=> '#ffffff',
						'btn_custom_font_hover'	=> '#ffffff'
					);
				
			}
			
			return $this->default_btn_atts;
		}
		
		/**
		 * Sets the extended button stylings
		 * 
		 * @since 4.8.4
		 * @param \aviaElementStyling $styling
		 * @param array $atts
		 */
		protected function set_button_styes( \aviaElementStyling $styling, array &$atts, $is_modal_item = false )
		{
			$styling->create_callback_styles( $atts, $is_modal_item );
			
			if( ! empty( $atts['icon_hover'] ) )
			{
				$styling->add_classes( 'container', 'av-icon-on-hover' );
			}
			
			if( ! empty( $atts['label_display'] ) && $atts['label_display'] == 'av-button-label-on-hover' ) 
			{
				$styling->add_classes( 'container', array( 'av-button-label-on-hover', 'av-button-notext' ) );
			}

			if( empty( $atts['label'] ) ) 
			{
				$styling->add_classes( 'container', 'av-button-notext' );
			}
					
			if( '' == $atts['color_options'] )
			{
				if( 'custom' == $atts['color'] ) 
				{
					$colors = array(
								'background-color'	=> $atts['custom_bg'],
								'border-color'		=> $atts['custom_bg'],
								'color'				=> $atts['custom_font']
							);
					
					$styling->add_styles( 'container', $colors );
				}
				else
				{
					$styling->add_classes( 'container', $this->class_by_arguments( 'color', $atts, true ) );
				}
			}
			else		//	color_options_advanced - added 4.7.5.1
			{
				if( 'custom' == $atts['btn_color_bg'] )
				{
					$colors = array(
								'background-color'	=> $atts['btn_custom_bg'],
								'border-color'		=> $atts['btn_custom_bg']
							);
					
					$styling->add_styles( 'container', $colors );
				}
				else if( 'btn_custom_grad' == $atts['btn_color_bg'] )
				{
					$styling->add_callback_styles( 'container', array( 'btn_custom_grad' ) );
					$styling->add_styles( 'container-hover', array( 'opacity' => $atts['btn_custom_grad_opacity'] ) );
//					$styling->add_classes( 'container', 'avia-color-gradient-background' );
				}
				else 
				{
					$styling->add_classes( 'container', 'avia-color-' . $atts['btn_color_bg'] );
				}
				
				if( 'custom' == $atts['btn_color_font'] )
				{
					$styling->add_styles( 'container', array( 'color' => $atts['btn_custom_font'] ) );
				}
				else
				{
					$styling->add_classes( 'container', 'avia-font-color-' . $atts['btn_color_font'] );
				}
				
				if( 'custom' == $atts['btn_color_bg_hover'] )
				{
					$styling->add_styles( 'container-hover', array( 'background-color' => $atts['btn_custom_bg_hover'] ) );
				}
				else
				{
					if( ! $this->is_special_button_color( $atts['btn_color_bg_hover'] ) )
					{
						$styling->add_styles( 'container-hover', array( 'background-color' => $atts['btn_color_bg_hover'] ) );
					}
				}
				
				if( 'custom' == $atts['btn_color_font_hover'] )
				{
					$styling->add_styles( 'container-hover', array( 'color' => $atts['btn_custom_font_hover'] ) );
				}
				else
				{
					$styling->add_styles( 'container-hover', array( 'color' => $atts['btn_color_font_hover'] ) );
				}
			}
			
			if( is_numeric( $atts['hover_opacity'] ) )
			{
				//	ignore when gradient background
				if( ! ( $atts['color_options'] == 'color_options_advanced' && $atts['btn_color_bg'] == 'btn_custom_grad'  ) )
				{
					$styling->add_styles( 'container-hover', array( 'opacity' => $atts['hover_opacity'] ) );
				}
			}
			
			if( ! empty( $atts['sonar_effect_effect'] ) )
			{
				$styling->add_classes( 'container', 'avia-sonar-shadow' );
				$styling->add_callback_styles( 'container-after', array( 'border_radius' ) );
				
				if( false !== strpos( $atts['sonar_effect_effect'], 'shadow' ) )
				{
					if( 'shadow_permanent' == $atts['sonar_effect_effect'] )
					{
						$styling->add_callback_styles( 'container-after', array( 'sonar_effect' ) );
					}
					else
					{
						$styling->add_callback_styles( 'container-after-hover', array( 'sonar_effect' ) );
					}
				}
				else
				{
					if( false !== strpos( $atts['sonar_effect_effect'], 'permanent' ) )
					{
						$styling->add_callback_styles( 'container', array( 'sonar_effect' ) );
					}
					else
					{
						$styling->add_callback_styles( 'container-hover', array( 'sonar_effect' ) );
					}
				}
			}
			
			$styling->add_callback_styles( 'container', array( 'border', 'border_radius', 'box_shadow' ) );
			$styling->add_callback_styles( 'container-hover-overlay', array( 'border_radius' ) );
			
			$transition = $styling->transition_rules( 'all 0.4s ease-in-out' );
			
			$styling->add_styles_conditionally( 'container', $transition );
			$styling->add_styles_conditionally( 'container-hover', $transition );
			
		}
	}
	
}
