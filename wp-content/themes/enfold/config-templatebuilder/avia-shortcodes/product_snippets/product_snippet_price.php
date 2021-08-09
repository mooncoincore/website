<?php
/**
 * Product Price
 *
 * Display the price for the current product
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( 'woocommerce' ) )
{
    add_shortcode( 'av_product_price', 'avia_please_install_woo' );
    return;
}

if ( ! class_exists( 'avia_sc_produc_price' ) )
{
    class avia_sc_produc_price extends aviaShortcodeTemplate
    {
        /**
         * Create the config array for the shortcode button
         */
        function shortcode_insert_button()
        {
            $this->config['self_closing']	= 'yes';

            $this->config['name']			= __( 'Product Price', 'avia_framework' );
            $this->config['tab']			= __( 'Plugin Additions', 'avia_framework' );
            $this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-price.png';
            $this->config['order']			= 20;
            $this->config['target']			= 'avia-target-insert';
            $this->config['shortcode']		= 'av_product_price';
            $this->config['tooltip']		= __( 'Display the price for the current product', 'avia_framework' );
            $this->config['drag-level']		= 3;
            $this->config['tinyMCE']		= array( 'disable' => 'true' );
            $this->config['posttype']		= array( 'product', __( 'This element can only be used on single product pages', 'avia_framework' ) );
        }


        /**
         * Editor Element - this function defines the visual appearance of an element on the AviaBuilder Canvas
         * Most common usage is to define some markup in the $params['innerHtml'] which is then inserted into the drag and drop container
         * Less often used: $params['data'] to add data attributes, $params['class'] to modify the className
         *
         *
         * @param array $params this array holds the default values for $content and $args.
         * @return $params the return array usually holds an innerHtml key that holds item specific markup.
         */
        function editor_element( $params )
        {
			$params = parent::editor_element( $params );
            
            $params['innerHtml'] .= "<div class='avia-flex-element'>";
            $params['innerHtml'] .= 		__( 'Display the price for the current product.', 'avia_framework' );
            $params['innerHtml'] .= '</div>';

            return $params;
        }



        /**
         * Frontend Shortcode Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */
        function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
        {
			//	fix for seo plugins which execute the do_shortcode() function before everything is loaded
			global $product;
			if( ! function_exists( 'WC' ) || ! WC() instanceof WooCommerce || ! is_object( WC()->query ) || ! $product instanceof WC_Product )
			{
				return '';
			}

            if( ! isset( $meta['el_class'] ) )
			{
				$meta['el_class'] = '';
			}
			
			 $output = '';

			$output .=	"<div class='av-woo-price {$meta['el_class']}'>";
			$output .=		'<p class="price">' . $product->get_price_html() . '</p>';
			$output .=	'</div>';
			
//			//	fix a problem with SEO plugin
//			if( function_exists( 'wc_clear_notices' ) )
//			{
//				wc_clear_notices();
//			}

            return $output;
        }
    }
}



