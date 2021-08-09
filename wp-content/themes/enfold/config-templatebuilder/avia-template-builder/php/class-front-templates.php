<?php
/**
 * Holds html templates to be reused in ALB frontend shortcodes
 *
 * @added_by guenter
 * @since 4.8.3
 */

if( ! class_exists( 'aviaFrontTemplates' ) )
{
	class aviaFrontTemplates
	{
	
		/**
		 * Returns HTML for arrows for e.g. slideshows
		 * 
		 * @since 4.8.3
		 * @param array $args			depends on context
		 * @return type
		 */
		static public function slide_navigation_arrows( array $args = array() ) 
		{
			$class_main = isset( $args['class_main'] ) ? $args['class_main'] : 'avia-slideshow-arrows avia-slideshow-controls';
			$container_styles = isset( $args['container_styles'] ) ? $args['container_styles'] : '';
			
			$icon_prev = isset( $args['icon_prev'] ) ? av_icon_string( $args['icon_prev'] ) : av_icon_string( 'prev_big' );
			$icon_next = isset( $args['icon_next'] ) ? av_icon_string( $args['icon_next'] ) : av_icon_string( 'next_big' );
			$class_prev = isset( $args['class_prev'] ) ? $args['class_prev'] : '';
			$class_next = isset( $args['class_next'] ) ? $args['class_next'] : '';
			$text_prev = isset( $args['text_prev'] ) ? $args['text_prev'] : __( 'Previous', 'avia_framework' );
			$text_next = isset( $args['text_next'] ) ? $args['text_next'] : __( 'Next', 'avia_framework' );
			
			$html  = '';
			
			$html .= "<div class='{$class_main}' {$container_styles}>";
			$html .= 	"<a href='#prev' class='prev-slide {$class_prev}' {$icon_prev} aria-hidden='true' tabindex='-1'>{$text_prev}</a>";
			$html .= 	"<a href='#next' class='next-slide {$class_next}' {$icon_next} aria-hidden='true' tabindex='-1'>{$text_next}</a>";
			$html .= '</div>';

			/**
			 * Customize slide navigation arrows
			 * 
			 * @since 4.8.3
			 * @param string $html
			 * @param array $args
			 * @return string
			 */
			return apply_filters( 'avf_slide_navigation_arrows_html', $html, $args );
		}
		
		/**
		 * Returns HTML for navigation dots for e.g. slideshows
		 * 
		 * @since 4.8.3
		 * @param array $args			depends on context
		 * @return string
		 */
		static public function slide_navigation_dots( array $args = array() )
		{
			$class_main = isset( $args['class_main'] ) ? $args['class_main'] : 'avia-slideshow-dots avia-slideshow-controls';
			$total_entries = isset( $args['total_entries'] ) ? $args['total_entries'] : 0;
			$container_entries = isset( $args['container_entries'] ) ? $args['container_entries'] : 1;
			
			$containers = $total_entries / (int) $container_entries;
			$final_cont = $total_entries % (int) $container_entries ? ( (int) $containers + 1 )  : (int) $containers;

			$active = 'active';

			$html  = '';
			$html .= "<div class='{$class_main}'>";

			for( $i = 1; $i <= $final_cont; $i++ )
			{
				$html .= "<a href='#{$i}' class='goto-slide {$active}' >{$i}</a>";
				$active = '';
			}

			$html .= '</div>';
			
			/**
			 * Customize slide navigation dots
			 * 
			 * @since 4.8.3
			 * @param string $html
			 * @param array $args
			 * @return string
			 */
			return apply_filters( 'avf_slide_navigation_dots_html', $html, $args );
		}
	}

}
