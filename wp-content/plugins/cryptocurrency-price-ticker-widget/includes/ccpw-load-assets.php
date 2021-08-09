<?php

function ccpw_load_assets($type,$post_id) {

        if (!wp_script_is('jquery', 'done')) {
            wp_enqueue_script('jquery','true');
        }
        // global assets
        wp_register_script('ccpw-sparkline', CCPWP_URL . 'assets/js/Chart.bundle.min.js',null,CCPWP_VERSION);
        wp_register_script('ccpw-lscache', CCPWP_URL . 'assets/js/lscache.min.js', array('jquery'), CCPWP_VERSION, true);
        wp_register_script('ccpw_small_charts', CCPWP_URL . 'assets/js/ccpw-small-charts.js', array('jquery'), CCPWP_VERSION, true);
        wp_localize_script(
            'ccpw_small_charts',
            'ajax_object',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );
		wp_enqueue_script('ccpw-numeral', CCPWP_URL. 'assets/js/numeral.min.js',array('jquery'),CCPWP_VERSION);
        wp_register_style( 'ctl-bootstrap', CCPWP_URL . 'assets/css/min/bootstrap.min.css', array(), CCPWP_VERSION , null );
        wp_enqueue_style('ccpwp-styles', CCPWP_URL . 'assets/css/min/ccpwp-styles.min.css', array(), CCPWP_VERSION, null, 'all');
        wp_enqueue_style('ccpwp-icons', CCPWP_URL . 'assets/css/ccpw-icons.css', array(), CCPWP_VERSION, null, 'all');
        $is_live_changes = get_post_meta($post_id, 'live_changes', true);
        
        if ( $is_live_changes == "on"){
            wp_enqueue_script('ccpw-jquery-number', CCPWP_URL . 'assets/js/jquery.number.min.js', array('jquery'), CCPWP_VERSION, true);
            
            wp_enqueue_script('ccpw_stream', CCPWP_URL . 'assets/js/stream.min.js', array('jquery'), CCPWP_VERSION, true);
        }
if( $type == 'binance-live-widget' ){
    wp_enqueue_script( 'ccpw-binance-live-widget', CCPWP_URL . 'assets/js/ccpw-binance-live-widget.js', array('jquery'), CCPWP_VERSION, true );
    wp_enqueue_style('ccpwp-vue-widget-style', CCPWP_URL . 'assets/css/vue-widget-style.css', array(), CCPWP_VERSION, null, 'all');
}else if ($type == 'price-block') {
            wp_enqueue_script('ccpw-sparkline');
            wp_enqueue_script('ccpw-lscache');
            wp_enqueue_script('ccpw_small_charts');
            wp_enqueue_style('ccpw-price-block', CCPWP_URL . 'assets/css/min/ccpw-price-block.min.css',null, CCPWP_VERSION);
            $disable_bootstrap = get_post_meta($post_id, 'disable_bootstrap', true); 
            $design_block = get_post_meta($post_id, 'design_block', true);
			if ($design_block == 'style-1') {
                // enqueue only for accordion
                wp_enqueue_script('ccpw-lscache');
                wp_enqueue_script('ccpw-accordion-js', CCPWP_URL . 'assets/js/ccpw-accordion.js', array('jquery', 'ccpw_small_charts'), CCPWP_VERSION, true);
            } else if (($design_block == 'style-2' || $design_block == 'style-3' || $design_block == 'style-4') && $disable_bootstrap != true) {
                wp_enqueue_style('ctl-bootstrap');
            } else if ($design_block == 'style-5') {
                wp_enqueue_style('ccpw_block_odometer_style', CCPWP_URL . 'assets/css/min/ccpw-odometer-theme-default.min.css',null, CCPWP_VERSION);
                wp_register_script('ccpw_block_style_5', CCPWP_URL . 'assets/js/ccpw-block-style-5.js', array('jquery'), CCPWP_VERSION, true);
                wp_register_script('ccpw_block_odometer', CCPWP_URL . 'assets/js/ccpw-odometer.min.js', array('jquery'), CCPWP_VERSION, true);
                wp_enqueue_script('ccpw_block_style_5');
                wp_enqueue_script('ccpw_block_odometer');
            }
   } else if ($type == 'accordion-block') {
            wp_enqueue_script('ccpw-sparkline');
            wp_enqueue_script('ccpw-lscache');
            wp_enqueue_script('ccpw-accordion-js', CCPWP_URL . 'assets/js/ccpw-accordion.js', array('jquery', 'ccpw_small_charts'), CCPWP_VERSION, true);
            wp_enqueue_style('ccpw-price-block', CCPWP_URL . 'assets/css/min/ccpw-price-block.min.css',null, CCPWP_VERSION);
            $design_accordion = get_post_meta($post_id, 'accordion-block-design', true);
            if($design_accordion == "style-2") {
                wp_enqueue_script('ccpw-jquery-number', CCPWP_URL . 'assets/js/jquery.number.min.js', array('jquery'), CCPWP_VERSION, true);
             
                wp_enqueue_script('ccpw_stream', CCPWP_URL . 'assets/js/stream.min.js', array('jquery'), CCPWP_VERSION, true);
                }

    } else if ($type == "ticker") {  
        wp_enqueue_style('ccpw-ticker', CCPWP_URL . 'assets/css/min/ccpw-ticker.min.css',null,CCPWP_VERSION);
        wp_enqueue_script('ccpw_bxslider_js',  CCPWP_URL .'assets/js/jquery.bxslider.min.js', array('jquery'), CCPWP_VERSION, true);
           //load scripts
        wp_enqueue_style('ccpw-tooltip-css', CCPWP_URL . 'assets/css/min/tooltipster.bundle.min.css',null,CCPWP_VERSION);
        wp_enqueue_script('ccpw-tooltip-js', CCPWP_URL . 'assets/js/tooltipster.bundle.min.js', array('jquery', 'ccpw_bxslider_js'), CCPWP_VERSION, true);
        wp_enqueue_script('ccpw_ticker',CCPWP_URL . 'assets/js/ccpw-ticker.js', array('jquery', 'ccpw_bxslider_js'), CCPWP_VERSION, true);
       
		$design_ticker = get_post_meta($post_id, 'design_ticker', true);
        switch($design_ticker){
         case "style-1":
         
         break;
         case "style-3":
         case "style-5":   
            // make sure the chart.js enqueue at the end of all ticker
                // enqueue sparkline chart only when required in ticker
                wp_enqueue_script('ccpw-lscache');
                wp_enqueue_script('ccpw-sparkline');
                wp_enqueue_script('ccpw_ticker_charts', CCPWP_URL . 'assets/js/ccpw-small-charts.min.js', array('jquery'), CCPWP_VERSION, true);
                wp_localize_script(
                    'ccpw_ticker_charts',
                    'ajax_object',
                    array('ajax_url' => admin_url('admin-ajax.php'))
                );
            break;
		 default:
		 break;
        }  

    } else if ($type == "list-widget") {
			$disable_bootstrap = get_post_meta($post_id, 'disable_bootstrap', true);
            // list widget styles
            if ($disable_bootstrap != true) {
                wp_enqueue_style('ctl-bootstrap');
            }
            wp_enqueue_style('ccpw-list-widget', CCPWP_URL . 'assets/css/min/ccpw-list-widget.min.css',null,CCPWP_VERSION);
			$design = get_post_meta($post_id, 'design', true);
            if ($design == "style-1") {
                wp_enqueue_script('ccpw-sparkline');
                wp_enqueue_script('ccpw-lscache');
                wp_enqueue_script('ccpw_small_charts');
            }else if($design == "style-4") {
                wp_enqueue_script('ccpw-jquery-number', CCPWP_URL . 'assets/js/jquery.number.min.js', array('jquery'), CCPWP_VERSION, true);
             
                wp_enqueue_script('ccpw_stream', CCPWP_URL . 'assets/js/stream.min.js', array('jquery'), CCPWP_VERSION, true);
            }

  	} else if ($type == "price-label") {
            wp_enqueue_style('ccpw-price-label', CCPWP_URL . 'assets/css/min/ccpw-price-label.min.css',null,CCPWP_VERSION);
            $design = get_post_meta($post_id, 'design', true);
            if( $design == "style-4"){
            
                wp_enqueue_script('ccpw_stream', CCPWP_URL . 'assets/js/stream.min.js', array('jquery'), CCPWP_VERSION, true);
            }
    } else if ($type == "chart") {
            wp_enqueue_style('ccpw-charts-style', CCPWP_URL . 'assets/css/min/ccpw-charts.min.css',null,CCPWP_VERSION);
            // dynamic chart assets
            wp_enqueue_script('amcharts', 'https://www.amcharts.com/lib/3/amcharts.js',null,CCPWP_VERSION);
            wp_enqueue_script('amcharts-serial', 'https://www.amcharts.com/lib/3/serial.js',null,CCPWP_VERSION);
            wp_enqueue_script('amcharts-stock', 'https://www.amcharts.com/lib/3/amstock.js',null,CCPWP_VERSION);
            //    wp_enqueue_script('amcharts-export','https://www.amcharts.com/lib/3/plugins/export/export.min.js');
            //wp_enqueue_style( 'amcharts-export-css','https://www.amcharts.com/lib/3/plugins/export/export.css');
          
            wp_enqueue_script('ccpw-lscache');
            wp_enqueue_script('ccpw-jquery-number', CCPWP_URL . 'assets/js/jquery.number.min.js', array('jquery'), CCPWP_VERSION, true);
            wp_enqueue_script('ccpw-chart-js', CCPWP_URL . 'assets/js/create-charts.min.js', array('jquery', 'amcharts'), CCPWP_VERSION, true);
        } else if ($type == "price-card") {
			$disable_bootstrap = get_post_meta($post_id, 'disable_bootstrap', true);
            if ($disable_bootstrap != true) {
                wp_enqueue_style('ctl-bootstrap');
            }
            wp_enqueue_style('ccpw-price-card', CCPWP_URL . 'assets/css/min/ccpw-price-card.min.css',null,CCPWP_VERSION);
			$design_card = get_post_meta($post_id, 'design_card', true);
            if ($design_card == "style-6") {
                wp_enqueue_script('ccpw-sparkline');
                wp_enqueue_script('ccpw-lscache');
                wp_enqueue_script('ccpw_small_charts');
            } else if ($design_card == "style-5") {
                wp_enqueue_style('ccpw-slider-widget', CCPWP_URL . 'assets/css/min/ccpw-slider-widget-min.css',null,CCPWP_VERSION);
                wp_register_script('ccpw-slick-js', CCPWP_URL . 'assets/js/ccpw-slick.min.js', array('jquery'), CCPWP_VERSION, false);
                wp_enqueue_script('ccpw-slick-js');
                wp_enqueue_script('ccpw-slider-js', CCPWP_URL . 'assets/js/ccpw-slider.min.js', array('jquery', 'ccpw-slick-js'), CCPWP_VERSION, false);

            }else if( $design_card == "style-4"){
                wp_enqueue_script('ccpw-jquery-number', CCPWP_URL . 'assets/js/jquery.number.min.js', array('jquery'), CCPWP_VERSION, true);
                wp_enqueue_script('ccpw_stream', CCPWP_URL . 'assets/js/stream.min.js', array('jquery'), CCPWP_VERSION, true);
             
            }
        } else if ($type == "slider-widget") {
            wp_enqueue_style('ccpw-slider-widget', CCPWP_URL . 'assets/css/min/ccpw-slider-widget.min.css',null,CCPWP_VERSION);
            wp_enqueue_script('ccpw-slick-js', CCPWP_URL . 'assets/js/ccpw-slick.min.js', array('jquery'), CCPWP_VERSION, false);
            wp_enqueue_script('ccpw-slider-js', CCPWP_URL . 'assets/js/ccpw-slider.js', array('jquery', 'ccpw-slick-js'), CCPWP_VERSION, false);
			$design_slider = get_post_meta($post_id, 'design_slider', true);
            if ($design_slider == "style-3") {
                wp_enqueue_script('ccpw-sparkline');
                wp_enqueue_script('ccpw-lscache');
                wp_enqueue_script('ccpw_small_charts');

            }
    } else if ($type == "rss-feed") {
		$rss_style = get_post_meta($post_id, 'rss_style', true);
		if($rss_style == "ticker-rss"){
			$rss_ticker_speed = get_post_meta($post_id, 'rss_ticker_speed', true);
			$speed_rss_ticker = !empty($rss_ticker_speed) ? $rss_ticker_speed : 15;
			$id = "ccpw-".$type."-". $post_id;
            wp_enqueue_style('ccpw-news-ticker', CCPWP_URL . 'assets/css/min/ccpw-news-ticker.min.css',null,CCPWP_VERSION);
            wp_enqueue_script('ccpw_bxslider_js',  CCPWP_URL .'assets/js/jquery.bxslider.min.js', array('jquery'), CCPWP_VERSION, true);
            wp_add_inline_script('ccpw_bxslider_js', 'jQuery(document).ready(function($){
		   $(".ccpw-news-ticker #' . $id . '").each(function(index){
                    $(this).bxSlider({
                        ticker:true,
                        minSlides:1,
                        maxSlides:12,
                        slideWidth:"auto",
                        tickerHover:true,
                        wrapperClass:"tickercontainer",
                        speed:'.$speed_rss_ticker.'*4000,
                    });
		   });

	   });');
		} else if ($rss_style == "list-rss") {
			wp_enqueue_style('ccpw-news-ticker', CCPWP_URL . 'assets/css/min/ccpw-news-ticker.min.css',null,CCPWP_VERSION);
		}

   }  else if ($type == "multi-currency-tab") {
            wp_enqueue_style('ccpw-multi-currency-tabs', CCPWP_URL . 'assets/css/min/ccpw-multi-currency-tabs.min.css',null,CCPWP_VERSION);
            wp_enqueue_script('ccpw_script', CCPWP_URL . 'assets/js/ccpw-script.js', array('jquery'), CCPWP_VERSION);

   } else if ($type == 'calculator') {
            wp_enqueue_script('cmc-select2-js', CCPWP_URL . 'assets/js/select2.min.js', array('jquery'), false, true);
            wp_enqueue_style('cmc-select2-css', CCPWP_URL . 'assets/css/select2.min.css',null,CCPWP_VERSION);
            wp_enqueue_script('cmc-numeral');
            wp_enqueue_style('ccpw-calculator-style', CCPWP_URL . 'assets/css/min/ccpw-calculator.min.css', null, CCPWP_VERSION);
            wp_register_script('cmc-calculator', CCPWP_URL . 'assets/js/ccpw-calcuator.min.js', array('jquery', 'cmc-select2-js'), CCPWP_VERSION, true);
            wp_enqueue_script('cmc-calculator');
            //    $cmc_styles = '';
            //wp_add_inline_style('ccpw-calculator-style', $cmc_styles);

   } else if( $type == 'table-widget' ){
          
			wp_enqueue_script('ccpw-datatable', CCPWP_URL. 'assets/js/jquery.dataTables.min.js',null,CCPWP_VERSION);
			wp_enqueue_script('ccpw-headFixer', CCPWP_URL. 'assets/js/tableHeadFixer.js',null,CCPWP_VERSION);
			wp_enqueue_style('ccpw-custom-datatable-style', CCPWP_URL. 'assets/css/ccpw-custom-datatable.css',null,CCPWP_VERSION);
			wp_enqueue_script('ccpw-table-script', CCPWP_URL. 'assets/js/ccpw-table-widget.min.js',array('jquery'),CCPWP_VERSION);
			wp_enqueue_script('ccpw-table-sort', CCPWP_URL. 'assets/js/tablesort.min.js',array('jquery'),CCPWP_VERSION);
            wp_localize_script(
				'ccpw-table-script',
				'ccpw_js_objects',
                array('ajax_url' => admin_url('admin-ajax.php'),
                      'wp_nonce'=>wp_create_nonce('ccpwf-tbl-widget')
                )
			);
        }
      
  }
