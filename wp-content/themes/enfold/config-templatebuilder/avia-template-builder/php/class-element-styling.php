<?php
/**
 * This class provides callback methods for ALB modal popup element settings to generate style rules combining several setting fields.
 * This avoids duplicating code in shortcode handler to build the style strings.
 * 
 * 
 * @author		Günter
 * @since 4.8.4
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if( ! class_exists( 'aviaElementStyling' ) )
{
	class aviaElementStyling extends \aviaBuilder\base\aviaElementStylingRules
	{
		/**
		 * @since 4.8.4
		 * @param aviaShortcodeTemplate $shortcode
		 * @param string $element_id
		 */
		public function __construct( aviaShortcodeTemplate $shortcode, $element_id ) 
		{
			parent::__construct( $shortcode, $element_id );
		}
		
		/**
		 * @since 4.8.4
		 */
		public function __destruct() 
		{
			parent::__destruct();
		}
		
		/**
		 * Styles for "Box Shadow"
		 * 
		 * @since 4.8.4
		 * @param array $element
		 * @param array $atts
		 */
		protected function box_shadow( array $element, array $atts ) 
		{
			$callback = $element['styles_cb'];
			
			$id = \AviaHelper::array_value( $callback, 'id' );
			if( empty( $id ) )
			{
				return;
			}
			
			//	ensure to have an empty structure we can rely on later
			$this->callback_settings[ $id ]['styles'] = array();
			
			$shadow = \AviaHelper::array_value( $atts, $id );
			if( empty( $shadow ) )
			{
				return;
			}
			
			if( 'outside' == $shadow )
			{
				$shadow = '';
			}

			$style = \AviaHelper::array_value( $atts, $id . '_style' );
			$style = \AviaHelper::multi_value_result_lockable( $style );
			
			$color = \AviaHelper::array_value( $atts, $id . '_color' );
	
			if( 'none' == $shadow )
			{
				$string = $shadow;
			}
			else
			{
				$string = "{$shadow} {$style['fill_with_0_style']} {$color}";
			}
			
			$rules = $this->box_shadow_rules( $string );
			
			$this->callback_settings[ $id ]['styles'] = array_merge( $this->callback_settings[ $id ]['styles'], $rules );
		}
		
		/**
		 * Styles for "Border"
		 * 
		 * @since 4.8.4
		 * @param array $element
		 * @param array $atts
		 */
		protected function border( array $element, array $atts ) 
		{
			$callback = $element['styles_cb'];
			
			$id = \AviaHelper::array_value( $callback, 'id' );
			if( empty( $id ) )
			{
				return;
			}
			
			//	ensure to have an empty structure we can rely on later
			$this->callback_settings[ $id ]['styles'] = array();
			
			$border = \AviaHelper::array_value( $atts, $id );
			if( empty( $border ) )
			{
				return;
			}
			
			$this->callback_settings[ $id ]['styles']['border-style'] = $border;
			
			$width = \AviaHelper::array_value( $atts, $id . '_width' );
			$width = AviaHelper::multi_value_result_lockable( $width );
			$this->callback_settings[ $id ]['styles']['border-width'] = $width['fill_with_0_style'];
			
			$color = isset( $atts[ $id . '_color' ] ) ? $atts[ $id . '_color' ] : '';
			
			if( ! empty( $color ) )
			{
				$this->callback_settings[ $id ]['styles']['border-color'] = $color;
			}
		}
		
		/**
		 * Styles for "Border Radius"
		 * 
		 * @since 4.8.4
		 * @param array $element
		 * @param array $atts
		 */
		protected function border_radius( array $element, array $atts ) 
		{
			$callback = $element['styles_cb'];
			
			$id = \AviaHelper::array_value( $callback, 'id' );
			if( empty( $id ) )
			{
				return;
			}
			
			//	ensure to have an empty structure we can rely on later
			$this->callback_settings[ $id ]['styles'] = array();
			
			$radius = \AviaHelper::array_value( $atts, $id );
			
			if( empty( $radius ) )
			{
				return;
			}
			
			$radius = AviaHelper::multi_value_result_lockable( $radius );
			$rules = $this->border_radius_rules( $radius['fill_with_0_style'] );
			
			$this->callback_settings[ $id ]['styles'] = array_merge( $this->callback_settings[ $id ]['styles'], $rules );
		}
		
		
	
		/**
		 * Styles for "Border Radius"
		 * 
		 * @since 4.8.4
		 * @param array $element
		 * @param array $atts
		 */
		protected function gradient_colors( array $element, array $atts ) 
		{
			$callback = $element['styles_cb'];
			
			$id = \AviaHelper::array_value( $callback, 'id' );
			if( empty( $id ) )
			{
				return;
			}
			
			if( ! is_array( $id ) )
			{
				$id_save = $id;
				$id_array = array(
							$id . '_direction',
							$id . '_1',
							$id . '_2',
							$id . '_3',
						);
			}
			else
			{
				$id_save = $id[0];
				$id_array = $id;
			}
			
			//	ensure to have an empty structure we can rely on later
			$this->callback_settings[ $id_save ]['styles'] = array();
			
			$direction = \AviaHelper::array_value( $atts, $id_array[0] );
			$col1 = \AviaHelper::array_value( $atts, $id_array[1] );
			$col2 = \AviaHelper::array_value( $atts, $id_array[2] );
			$col3 = \AviaHelper::array_value( $atts, $id_array[3] );
			
			if( empty( $col1 ) || empty( $col2 ) )
			{
				return;
			}
			
			$rule_prefix = 'linear-gradient';
			$rule_colors = '';
			$append3 = true;
			
			switch( $direction )
			{
				case 'vertical':
					$rule_colors = "to bottom, {$col1}, {$col2}";
					break;
				case 'vertical_rev':
					$rule_colors = "to top, {$col1}, {$col2}";
					break;
				case 'horizontal':
					$rule_colors = "to right, {$col1}, {$col2}";
					break;
				case 'horizontal_rev':
					$rule_colors = "to left, {$col1}, {$col2}";
					break;
				case 'diagonal_tb':
					$rule_colors = "to bottom right, {$col1}, {$col2}";
					break;
				case 'diagonal_tb_rev':
					$rule_colors = "to top left, {$col1}, {$col2}";
					break;
				case 'diagonal_bt':
					$rule_colors = "45deg, {$col1}, {$col2}";
					break;
				case 'diagonal_bt_rev':
					$rule_colors = "215deg, {$col1}, {$col2}";
					break;
				case 'radial':
					$rule_prefix = 'radial-gradient';
					$rule_colors = "{$col1}, {$col2}";
					break;
				case 'radial_rev':
					$rule_prefix = 'radial-gradient';
					$rule_colors = "{$col2}, {$col1}";
					$append3 = false;
					break;
			}
			
			if( empty( $rule_colors ) )
			{
				return;
			}
			
			if( ! empty( $col3 ) )
			{
				$rule_colors = $append3 ? "{$rule_colors}, {$col3}" : "{$col3}, {$rule_colors}";
			}
			
			$background = $this->gradient_color_rules( $rule_prefix, $rule_colors, $col1 );
			
			$this->callback_settings[ $id_save ]['styles'] = array_merge( $this->callback_settings[ $id_save ]['styles'], $background );
		}
		
		/**
		 * Styles for "Sonar Effect"
		 * 
		 * @since 4.8.4
		 * @param array $element
		 * @param array $atts
		 */
		protected function sonar_effect( array $element, array $atts )
		{
			$callback = $element['styles_cb'];
			
			$id = \AviaHelper::array_value( $callback, 'id' );
			if( empty( $id ) )
			{
				return;
			}
			
			//	ensure to have an empty structure we can rely on later
			$this->callback_settings[ $id ]['styles'] = array();
			$this->callback_settings[ $id ]['keyframes'] = array();
			
			$effect = \AviaHelper::array_value( $atts, $id . '_effect' );
			if( empty( $effect ) )
			{
				return;
			}
			
			$color = \AviaHelper::array_value( $atts, $id . '_color', '#ffffff', 'not_empty' );
			$duration = \AviaHelper::array_value( $atts, $id . '_duration', '1', 'not_empty' );
			$scale = \AviaHelper::array_value( $atts, $id . '_scale', '1.5', 'not_empty' );
			$el_opac = \AviaHelper::array_value( $atts, $id . '_opac', '0.5', 'not_empty' );
			
			$infinite = in_array( $effect, array( 'shadow_permanent', 'pulsate_permanent', 'shadow_hover_perm', 'pulsate_hover_perm', 'element_permanent', 'element_hover_perm' ) ) ? ' .1s infinite' : '';
			$sonar_id = "av_sonarEffect_{$this->element_id}";
			
			$el_opac = in_array( $effect, array( 'pulsate_permanent', 'pulsate_hover_once', 'pulsate_hover_perm', 'element_permanent', 'element_hover_once', 'element_hover_perm' ) ) ? $el_opac : '0.5';
			$shadow_opac = false !== strpos( $effect, 'shadow' ) ? '0' : $el_opac;
				
			
			$animation = '';
			
			if( false === strpos( $effect, 'element') )
			{
				$animation .= '  0% {opacity: 0.3;}' . $this->new_ln;
				$animation .= " 40% {opacity: {$el_opac}; box-shadow: 0 0 0 2px rgba(255,255,255,0.1), 0 0 10px 10px {$color}, 0 0 0 10px rgba(255,255,255,0.5);}" . $this->new_ln;
				$animation .= "100% {opacity: {$shadow_opac}; box-shadow: 0 0 0 2px rgba(255,255,255,0.1), 0 0 10px 10px {$color}, 0 0 0 10px rgba(255,255,255,0.5); -webkit-transform: scale({$scale}); transform: scale({$scale});}";
			}
			else
			{
				$animation .= '  0% {opacity: 0.3;}' . $this->new_ln;
				$animation .= " 50% {opacity: {$el_opac};}" . $this->new_ln;
				$animation .= "100% {opacity: {$el_opac}; -webkit-transform: scale({$scale}); transform: scale({$scale});}";
			}
			
			$keyframes = $this->sonar_keyframes( $sonar_id, $animation );
			
			$this->callback_settings[ $id ]['keyframes'] = array_merge( $this->callback_settings[ $id ]['keyframes'], $keyframes );
			
			$sonar = "{$sonar_id} {$duration}s ease-in-out{$infinite}";
			$animation_rules = $this->animation_rules( $sonar );
			
			$this->callback_settings[ $id ]['styles'] = array_merge( $this->callback_settings[ $id ]['styles'], $animation_rules );
		}
		
		/**
		 * Styles for a single "SVG divider"
		 * 
		 *		- $id
		 *		- $id . '_color'
		 * 
		 * @since 4.8.4
		 * @param array $element
		 * @param array $atts
		 */
		protected function svg_divider( array $element, array $atts ) 
		{
			$callback = $element['styles_cb'];
			
			$id = \AviaHelper::array_value( $callback, 'id' );
			if( empty( $id ) )
			{
				return;
			}
			
			//	ensure to have an empty structure we can rely on later
			$this->callback_settings[ $id ]['styles'] = array();
			$this->callback_settings[ $id ]['classes'] = array();
			
			$svg = \AviaHelper::array_value( $atts, $id );
			if( empty( $svg ) )
			{
				return;
			}
			
			$location = \AviaHelper::array_value( $callback, 'location', 'top', 'not_empty' );
			
			$shape = \AviaHelper::array_value( $atts, $id );
			$color = \AviaHelper::array_value( $atts, $id . '_color' );
			$height = \AviaHelper::array_value( $atts, $id . '_height' );
			$max_height = \AviaHelper::array_value( $atts, $id . '_max_height' );
			$width = \AviaHelper::array_value( $atts, $id . '_width' );
			$flip = \AviaHelper::array_value( $atts, $id . '_flip' );
			$invert = \AviaHelper::array_value( $atts, $id . '_invert' );
			$front = \AviaHelper::array_value( $atts, $id . '_front' );
			$opacity = \AviaHelper::array_value( $atts, $id . '_opacity' );
			
			/**
			 * Add a substyle for 'svg' - we use _svg to get a unique id
			 */
			$svg_id = $id . '_svg';
			
			if( is_numeric( $height ) )
			{
				$this->callback_settings[ $svg_id ]['styles']['height'] = $height . 'px';
			}
			else
			{
				$this->callback_settings[ $svg_id ]['styles']['height'] = $height;
				if( is_numeric( $max_height ) )
				{
					$this->callback_settings[ $svg_id ]['styles']['max-height'] = $max_height . 'px';
				}
			}
			
			$this->callback_settings[ $svg_id ]['styles']['opacity'] = $opacity;
			
			if( AviaSvgShapes()->supports_width( $shape ) )
			{
				$this->callback_settings[ $svg_id ]['styles']['width'] = "calc($width% + 1.3px)";
			}
			
			/**
			 * Add an additional substyle for 'path' - we use _color to get a unique id
			 */
			$sub_id = $id . '_color';
			if( ! empty( $color ) )
			{
				$this->callback_settings[ $sub_id ]['styles']['fill'] = $color;
			}
			
			if( 'top' != $location )
			{
				$this->callback_settings[ $id ]['classes'][] = 'avia-divider-svg-bottom';
			}
			else
			{
				$this->callback_settings[ $id ]['classes'][] = 'avia-divider-svg-top';
			}
			
			if( ! empty( $flip ) && AviaSvgShapes()->can_flip( $shape ) )
			{
				$this->callback_settings[ $id ]['classes'][] = 'avia-flipped-svg';
			}
			
			if( ! empty( $front ) )
			{
				$this->callback_settings[ $id ]['classes'][] = 'avia-to-front';
			}
				
			if( ! empty( $invert ) && AviaSvgShapes()->can_invert( $shape ) )
			{
				$this->callback_settings[ $id ]['classes'][] = 'avia-svg-negative';
			}
			else
			{
				$this->callback_settings[ $id ]['classes'][] = 'avia-svg-original';
			}
		}
		
	}
	
}
