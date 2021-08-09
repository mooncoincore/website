<?php

class CCPW_Shortcode
{

    /**
     *  Including required files
     */
    public function __construct()
    {
        // Register Main shortcode for all layouts
        add_shortcode('ccpw', array($this, 'ccpw_shortcode'));
        add_action('wp_ajax_nopriv_ccpw_load_svg', array($this, 'ccpw_load_svg'));
        add_action('wp_ajax_ccpw_load_svg', array($this, 'ccpw_load_svg'));

        require CCPWP_PATH . '/includes/ccpw-dynamic-styles.php';
        require CCPWP_PATH . '/includes/ccpw-load-assets.php';
    }
    /**
     *  CCPW main shortcode handler for all layouts
     */

    public function ccpw_load_svg()
    {
        $coin_symbol = $_REQUEST['coin_symbol'];
        $small_chart_bgcolor = $_REQUEST['chart_bgcolor'];
        $small_chart_color = $_REQUEST['chart_color'];

        echo ccpw_generate_svg_chart($coin_symbol,$coin_price, $small_chart_color, $small_chart_bgcolor);
        exit();
    }
    public function get_cmc_single_page_slug(){
          // Initialize Titan for cmc links
          $cmc_slug='';
          if (class_exists('TitanFramework')) {
            $cmc_titan = TitanFramework::getInstance('cmc_single_settings');
            $cmc_slug = $cmc_titan->getOption('single-page-slug');
            if (empty($cmc_slug)) {
                $cmc_slug = 'currencies';
            }
        } else {
            $cmc_slug = 'currencies';
        }
        return $cmc_slug;
    }

    /*
    Main shortcode
    Handling all type of widgets
     */

    public function ccpw_shortcode($atts, $content = null)
    {  
        $atts = shortcode_atts(array(
            'id' => '',
            'class' => '',
        ), $atts, 'ccpw');

        $post_id = $atts['id'];

        // Do not execute on post type other than 'ccpw'
        // Live preview fixes
        if( get_post_type($post_id) != 'ccpw' ){
            return;
        }

        $post_status = get_post_status($post_id);

        // make sure the widget is already published!
        if( $post_status != 'publish' ){
			 return;
         //   return _e('widget id:'. $post_id .' is not available. widget status: '.$post_status,'ccpw');
        }

        $cron_available = get_transient('cmc-saved-coindata');

        if ($cron_available === false || $cron_available == '') {
            ccpwp_get_api_data();
        }
        // init vars
        $output = ccpw_HTMLpluginVersion();
        $cls = '';
        $crypto_html = '';
        $design_ticker = '';
        $design = '';
        $design_card = '';
        $cmc_link_start = '';
        $cmc_link_end = '';
        // get widgets common settings
        $is_live_changes = '';
        $type = get_post_meta($post_id, 'type', true);
        $currency = get_post_meta($post_id, 'currency', true);
        $custom_css = get_post_meta($post_id, 'custom_css', true);
        $enable_formatting = get_post_meta($post_id, 'display_format', true);
        $is_live_changes = get_post_meta($post_id, 'live_changes', true);
        $disable_bootstrap = get_post_meta($post_id, 'disable_bootstrap', true);
        $is_cmc_enable  =   get_option('cmc-dynamic-links');
        $back_color = get_post_meta($post_id, 'back_color', true);
        $font_color = get_post_meta($post_id, 'font_color', true);
        $id = "wid-" . $post_id;
        $id = "ccpw-".$type."-". $post_id;
        $card_display_changes = get_post_meta($post_id, 'card_display_changes', true);
        $cmc_slug=$this->get_cmc_single_page_slug();
        
       
        $display_currencies=array();
        $show_coins = get_post_meta($post_id,'show-coins',true);
        $display_currencies = get_post_meta($post_id,'display_currencies', true );
        $getData = (!empty($show_coins))?$show_coins:'custom';

        // Grab the metadata
        if ($type == 'price-block') {
            $currency = get_post_meta($post_id, 'price_block_currency', true);
        } else if ($type == "slider-widget") {
            $currency = get_post_meta($post_id, 'slider_widget_currency', true);
        }
        $fiat_currency = $currency ? $currency : "USD";
        $display_changes = get_post_meta($post_id, 'display_changes', true);
        $display_changes_old = get_post_meta($post_id, 'display_changes', true);
        if ( $type == "price-label" || $type == "list-widget" ) {
            $display_changes = get_post_meta($post_id, 'label_list_display_changes', true);
        }

       
        $column = get_post_meta($post_id, 'column', true);
        $column2 = ccpw_generate_column($column);
        $design = get_post_meta($post_id, 'design', true);


         // $all_coin_data =  array_merge( array_flip($display_currencies), $all_coin_data );
         $usd_conversions = (array) ccpwp_usd_conversions('all');
         $currency_rate = isset($usd_conversions[$fiat_currency])?$usd_conversions[$fiat_currency]:0;
         $fiat_symbol = ccpwp_get_currency_symbol($fiat_currency);
      $g_attr_for_live='
          data-currency-rate="'.$currency_rate.'" 
          data-currency-symbol="'.$fiat_symbol.'" 
          data-currency-type="'.$fiat_currency.'" ';

          
        switch($type){
            case 'binance-live-widget':
                // here we are and you are so are so
            break;
            case "ticker":
                $ticker_position = get_post_meta($post_id, 'ticker_position', true);
                $liveCls = $is_live_changes ? 'ccpw-live' : '';
                $ticker_speed = get_post_meta($post_id, 'ticker_speed', true);
                $t_speed = $ticker_speed ? $ticker_speed : 15;
                $ticker_in_mobile = get_post_meta($post_id, 'ticker_in_mobile', true);
                $design_ticker = get_post_meta($post_id, 'design_ticker', true);
                $currency_ticker = get_post_meta($post_id, 'currency_ticker', true);
            break;
            case "price-block":
                $design_block = get_post_meta($post_id, 'design_block', true);
                $block_display_changes = get_post_meta($post_id, 'block_display_changes', true);
                $block_chart_color = get_post_meta($post_id, 'block_chart_color', true);
                $block_column = get_post_meta($post_id, 'block_column', true); // Column settings from price-block style
                $block_cols = isset($block_column) ? $block_column : "col-md-4";
            break; 
            case "price-card":
                $card_cols = isset($column) ? $column : "col-md-4";
                $design_card = get_post_meta($post_id, 'design_card', true);
                $card_display_changes = get_post_meta($post_id, 'card_display_changes', true);
            break; 
            case "slider-widget":
                $card_cols = isset($column) ? $column : "col-md-4";
                $design_slider = get_post_meta($post_id, 'design_slider', true);
                $slider_autoplay = get_post_meta($post_id, 'slider_autoplay', true);
                $slider_display_changes = get_post_meta($post_id, 'slider_display_changes', true);
                $slider_chart_color = get_post_meta($post_id, 'slider_chart_color', true);
            break;  
            case "accordion-block":
                $design_accordion = get_post_meta($post_id, 'accordion-block-design', true);
            break;    
            case "multi-currency-tab":
                $mt_currencies = get_post_meta($post_id, 'mt-currencies', true);
            break;      
               
            case "chart":
                $trading_view = get_post_meta($post_id, 'trading-view-chart', true);
                $chart_height = get_post_meta($post_id, 'chart_height', true);
                $chart_color = get_post_meta($post_id, 'chart_color', true);
            break; 
            case "list-widget":
                    
             break; 
            default:
            break;

        }

        /**
         *  Gernates styles for all layouts
         */
         $styles =ccpw_dynamic_style($post_id,$id,$type, $back_color,$font_color,$custom_css);
         /**
         *  Loading all JS and CSS
         */
        ccpw_load_assets($type,$post_id);


        if( $type == "binance-live-widget" ){
           

            require dirname(__FILE__) . '/layouts/vue-widget.php';
            return $crypto_html.$styles;
        }else if( $type == "technical-analysis" ){
            require dirname(__FILE__) . '/layouts/technical-analysis.php';
            return $crypto_html;

        }else if( $type == "quick-stats" ){
            require dirname(__FILE__) . '/layouts/quick-stats.php';
            return $crypto_html;
            
        }else if ($type == "table-widget") {
			$show_coins = get_post_meta($post_id, 'display_currencies_for_table', true);
			$coin_per_page = get_post_meta($post_id, 'pagination_for_table', true);
			$cls = 'ccpw-coinslist_wrapper';
			$preloader_url = CCPWP_URL . 'assets/chart-loading.svg';
			$ccpw_prev_coins = __('Previous', 'ccpw');
			$ccpw_next_coins = __('Next', 'ccpw');
			$coin_loading_lbl = __('Loading...', 'ccpw');
			$ccpw_no_data = __('No Coin Found', 'ccpw');

		} else if ($type == "rss-feed") {
            //------------------------------ rss feed options ------------------------------------
            $rss_style = get_post_meta($post_id, 'rss_style', true);
            $rss_ticker_position = get_post_meta($post_id, 'rss_ticker_position', true);
            $rss_excerpt = get_post_meta($post_id, 'rss_excerpt', true);
            $rss_desc_length = !empty($rss_excerpt) ? $rss_excerpt : 55;
            $rss_url = get_post_meta($post_id, 'rss_url', true);
            $rss_url_second = get_post_meta($post_id, 'rss_url_second', true);

            $rss_excrpt_text = get_post_meta($post_id, 'rss_excerpt_text', true);
            $rss_excerpt_text = !empty($rss_excrpt_text) ? $rss_excrpt_text : 'Read More';
            $rss_number_of_news = get_post_meta($post_id, 'rss_number_of_news', true);
            $rss_no_of_news = !empty($rss_number_of_news) ? $rss_number_of_news : 999;

            $rss_ticker_speed = get_post_meta($post_id, 'rss_ticker_speed', true);
            $speed_rss_ticker = !empty($rss_ticker_speed) ? $rss_ticker_speed : 15;
            //------------------------------------------------------------------
            require dirname(__FILE__) . '/layouts/news-ticker.php';
            return $output . $styles;

        } else if ($type == "changelly-widget") {
            require dirname(__FILE__) . '/layouts/changelly.php';
            return $output . $styles;

        } else if ($type == "calculator") {
            require dirname(__FILE__) . '/layouts/calculator.php';
            return $output . $styles;
        } else {

            if( !empty($getData) && is_numeric($getData)){
                // fetch data from db
                 $all_coin_data = ccpwp_coin_arr($getData, "top");
               //  $all_coin_data = ccpw_get_top_coins_data($getData);
             }else{
                  // fetch data from db
                  if(is_array($display_currencies)&& count($display_currencies)>0){
                      $all_coin_data = ccpwp_coin_arr($display_currencies, "all");
                    }else{
                    return  $error = __('You have not selected any currencies to display', 'ccpw');
                  }
             }
         
        if ($all_coin_data) {
                if (!is_array($all_coin_data) || count($all_coin_data) <= 0) {
                    return __('You have not selected any currency to display', 'ccpw');
                }
           
                $bitcoin = ccpwp_coins_data("bitcoin");
                $btc_price = $bitcoin[0]['price'];
                    
                $j = 0; // for list-widget style-3 rank
                foreach ($all_coin_data as $currency) {
                    $j++;
                    $coin = $currency;
                    /**
                     *  generate_html function is creating HTML for all layouts
                     */
                    $coin_html = '';
                    $changes_coin_html = '';
                    $coin_name = $coin['name'];
                    $coin_symbol = $coin['symbol'];
                    /**
                     * Creating vars for later use
                     */

                    $coin_id = $coin['coin_id'];

                    $available_supply = $coin["circulating_supply"];

                    $coin_slug = strtolower($coin_name);
                    
                    if( $coin['coin_id'] == 'bitcoin' ){
                        $coin['price_btc'] = 1;
                    }else{
                        $coin['price_btc'] = ccpwp_format_number( $coin['price'] / ( 1 / $btc_price ) );
                    }
                    $coin_price = $coin['price'] * $currency_rate;
                    $market_cap = $coin['market_cap'] * $currency_rate;
                    $volume = $coin['total_volume'] * $currency_rate;

                    if ($enable_formatting) {
                        $coin_price = ccpwp_format_number($coin_price);
                        $volume = ccpw_format_coin_values($volume);
                        $market_cap = ccpw_format_coin_values($market_cap);
                        $available_supply = ccpw_format_coin_values($available_supply);
                    } else {
                        $coin_price = ccpwp_format_number($coin_price);
                        $volume = ccpwp_format_number($volume);
                        $market_cap = ccpwp_format_number($market_cap);
                        $available_supply = ccpwp_format_number($available_supply);
                    }
                    $percent_change_24h = $coin['percent_change_24h'] != "" ? ccpwp_format_number($coin['percent_change_24h']) . '%' : '';
                    $coin_price_html = "";
              

                    if (($type == "price-block") ||
                        ($type == "price-card" && $design_card == "style-7") ||
                        ($type == "price-label" && $design == "style-5") ||
                        ($type == "list-widget" && $design == "style-5" ) 
                    ) {
                        $multicurrency = ccpwp_usd_conversions("all");
                        $coin_price_multicurrency = ccpwp_format_number($coin['price']);
                        $coin_price_html .= '<span class="ticker-price">' . $coin_price_multicurrency . '</span>';
                    } else {
                        $coin_price_html .= '<span class="ticker-price">' . $fiat_symbol . $coin_price . '</span>';
                    }

                    $live_price_changes = "";
                    $live_changes = "";

                    if ($coin_symbol == "MIOTA") {
                        $coinId = 'IOT';
                    } else {
                        $coinId = $coin_symbol;
                    }

                    $market_cap_html = $fiat_symbol . $market_cap;

                    $change_sign = '<i class="ccpw_icon-up" aria-hidden="true"></i>';
                    $change_class = "up";
                    $change_sign_minus = "-";


                    $change_sign_24h = '<i class="ccpw_icon-up" aria-hidden="true"></i>';
                    $change_class_24h = "up";
                    if (strpos($coin['percent_change_24h'], $change_sign_minus) !== false) {
                        $change_sign_24h = '<i class="ccpw_icon-down" aria-hidden="true"></i>';
                        $change_class_24h = "down";
                    }

                    $change_sign_7d = '<i class="ccpw_icon-up" aria-hidden="true"></i>';
                    $change_class_7d = "up";

                    if ($display_changes || $display_changes_old) {

                            $changes_coin_html .= '<span class="ccpw-changes ' . $change_class_24h . '">';
                            $changes_coin_html .= $change_sign_24h . $percent_change_24h;
                            $changes_coin_html .= '</span>';

                    }

                    $live_price_changes .= '<div class="live-pricing"><span class="live_p">' . $fiat_symbol . $coin_price . '</span>';

                    $live_changes .= '<span class="live_c ccpw-changes ' . $change_class_24h . '">' . $change_sign_24h . $percent_change_24h . '</span>
                        <span class="live_t">24H</span>
                        </div>';

                    $all_c_p_html = '';
                    if ($display_changes 
                    || $card_display_changes
                     || $display_changes_old) {


                        if ($percent_change_24h != "") {
                            $all_c_p_html .= '<span class="ccpw-changes ' . $change_class_24h . '"><span class="changes-time-all">' . __(" 24H %", "ccpw") . '</span></br>';
                            $all_c_p_html .= $change_sign_24h . $percent_change_24h;
                            $all_c_p_html .= '</span>';
                        }

                    }

                    $coin_logo_html = '';
                
                    if ($type == "ticker" || $type == "price-label" || $type == "list-widget") {
                        $coin_logo = ccpwp_coin_logo_html($coin_id, $size = 32);
                        $coin_logo_big = ccpwp_coin_logo_html($coin_id, $size = 128);
                        $coin_logo_html .= '<div class="ccpw_icon">' . $coin_logo . '</div>';
                    } else if ($type == "price-card") {
                        $coin_logo = ccpwp_coin_logo_html($coin_id, $size = 52);
                        $coin_logo_html .= '<div class="ccpw_icon">' . $coin_logo . '</div>';
                    } else if( $type == "multi-currency-tab" ) {
                        $coin_logo_card = ccpwp_coin_logo_html($coin_id, $size = 22);
                        $coin_logo_html .= '<div class="ccpw_icon">' . $coin_logo_card . '</div>';
                    }else {
                        $coin_logo_card = ccpwp_coin_logo_html($coin_id, $size = 128);
                        $coin_logo_html .= '<div class="ccpw_icon">' . $coin_logo_card . '</div>';
                    }
                    if ( $is_cmc_enable == true) {
                        $coin_url = esc_url(home_url($cmc_slug . '/' . $coin_symbol . '/' . $coin_id . '/'));
                        $cmc_link_start = '<a class="cmc_links" title="' . $coin_name . '" href="' . $coin_url . '">';
                        $cmc_link_end = '</a>';
                    }             
                    
                    $coin_attr_for_live='
                     data-coin-price="'.esc_attr($coin_price).'"
                     data-trading-pair="' . esc_attr($coin_symbol) . 'USDT"
                     data-coin-id="' . esc_attr($coin_id) . '" 

                     ';

                    /*
                    loading layout files
                     */
                    if ($type == "price-block") {
                        require dirname(__FILE__) . '/layouts/block.php';
                        $crypto_html .= $coin_html;
                    } else if ($type == "ticker") {

                        require dirname(__FILE__) . '/layouts/ticker.php';
                        $crypto_html .= $coin_html;
                    } else if ($type == "price-label") {
                        require dirname(__FILE__) . '/layouts/label.php';
                        $crypto_html .= $coin_html;
                    } else if ($type == "price-card" && $design_card!='style-5') {
                        require dirname(__FILE__) . '/layouts/card.php';
                        $crypto_html .= $coin_html;
                    } else if ($type == "multi-currency-tab") {
                        require dirname(__FILE__) . '/layouts/multi-currency-tab.php';
                        $crypto_html .= $coin_html;
                    } else if ($type == 'slider-widget' || ($type == 'price-card' && $design_card=='style-5') ) {
                        require dirname(__FILE__) . '/layouts/slider-widget.php';
                        $crypto_html .= $coin_html;
                    }else if($type == 'accordion-block' ){
                        require dirname(__FILE__) . '/layouts/accordion-block.php';
                        $crypto_html .= $coin_html;
                    }else if($type == 'price-button' ){
                        require dirname(__FILE__) . '/layouts/price-button.php';
                        $crypto_html .= $coin_html;
                    }
                    else {
                        require dirname(__FILE__) . '/layouts/list-widget.php';
                        $crypto_html .= $coin_html;
                    } //end of layout files
                }
            } else {
                // if users don't have seleted any currencies from settings panel
                return __('You have not selected any currency to display', 'ccpw');
            }

        }
/**
 * Creating Wrapper HTML according to the type
 */
		/*
		*	Table widget html wrapper
		*/
		if( $type == 'table-widget' ){

            require dirname(__FILE__) . '/layouts/table-widget.php';
			$output .= $crypto_html;

		} else if( $type == "slider-widget" || ( $type == "price-card" && $design_card == "style-5") ){
            /**
             * Slider widget with backword compatibility for price-card > style-5
             */
            $liveCls = '';
            $output .= '<div '.$g_attr_for_live.' id="' . $id . '" class="ccpw-container ' . $type . ' ' . $liveCls . ' ' . $design_slider . '"><div class="slider-row" data-display-column="' . $column2 . '" data-slider-autoplay="' . $slider_autoplay . '">';
            $output .= $crypto_html;
            $output .= '</div></div>';
        } else if ($type == 'accordion-block') {
            switch($design_accordion){
                case 'style-1':
                case 'style-3':
                    $output .= '<div id="' . $id . '" class="ccpw-container price-block accordion-style ' . $design_accordion . '">';
                break;
                case 'style-2':
                    $output .= '<div id="' . $id . '"  '.$g_attr_for_live.' class="ccpw-live ccpw-container price-block accordion-style ' . $design_accordion . '" '.$g_attr_for_live.'>';
                break;
            }
            $output .= $crypto_html;
            $output .= '</div>';
        } else if($type == 'price-button'){
             $output .= $crypto_html;
        } 
        else if ($type == 'price-block') {

            $output .= '<div id="' . $id . '" class="ccpw-container price-block ' . $design_block . '">';
            $output .= $crypto_html;
            $output .= '</div>';

// Ticker Wrapper
        } else if ($type == "ticker") {
            $curreny_info_attrs='';
           
            // ticker position
            if ($ticker_position == "footer" || $ticker_position == "header") {
                if ($ticker_position == "footer") {
                    $container_cls = 'ccpw-footer-ticker-fixedbar';
                } else {
                    $container_cls = 'ccpw-header-ticker-fixedbar ';
                }

            } else {
                $container_cls = '';
            }
            switch($design_ticker){
                case "style-3":
                    $output .= '<div '.$g_attr_for_live.' style="display:none" class="' . $liveCls . ' style-3_chart ccpw-ticker-cont  ' . $container_cls . '">';
                    $output .= '<ul data-speed="'.$t_speed.'"  id="' . $id . '">';
                    $output .= $crypto_html;
                    $output .= '</ul></div>';
                break;
                case "style-4":
                    $output .= '<div '.$g_attr_for_live.' style="display:none" class="' . $liveCls . ' style-4_big  ccpw-ticker-cont  ' . $container_cls . '">';
                    $output .= '<ul data-speed="'.$t_speed.'"  id="' . $id . '">';
                    $output .= $crypto_html;
                    $output .= '</ul></div>';
                break;
                case "style-5":
                    $output .= '<div '.$g_attr_for_live.' style="display:none" class="' . $liveCls . ' style-5_big  ccpw-ticker-cont  ' . $container_cls . '">';
                    $output .= '<ul data-speed="'.$t_speed.'"  id="' . $id . '">';
                    $output .= $crypto_html;
                    $output .= '</ul></div>';
                break;
                default:
                    $output .= '<div '.$g_attr_for_live.' style="display:none" class=" style-1-2 ccpw-ticker-cont  ' . $container_cls . ' ' . $liveCls . '" >';
                    $output .= '<ul data-speed="'.$t_speed.'"  id="' . $id . '">';
                    $output .= $crypto_html;
                    $output .= '</ul></div>';
                break;
            }
        } else if ($type == "list-widget") {
            $cls = 'ccpw-widget';
            $liveCls = '';
            if ($design == "style-4") {
                $liveCls = 'ccpw-live';
            }
            $output .= '<div  class="' . $cls . '" id="' . $id . '" >
            <table  '.$g_attr_for_live.' class="ccpw_table ccpw-container table ' . $liveCls . ' ' . $design . '"
             id="' . $id . '">';
            /**
             * Creating Wrapper HTML For List widget
             */
            switch($design){
                case "style-2":
                    $output .= '<thead><tr>
                    <th>' . __('Name', 'ccpw') . '</th>
                    <th>' . __('Price', 'ccpw') . '</th>';
                    // Rendering conditional changes
                    if ($display_changes) {
                        $output .= '<th>' . __('24H %', 'ccpw') . '</th>';                    
                    }
                    $output .= ' <th>' . __('Supply', 'ccpw') . '</th>
                    <th>' . __('Volume', 'ccpw') . '</th>
                    <th>' . __('Market Cap', 'ccpw') . '</th>
                    </tr> </thead>';

                break;
                case "style-4":
                    $output .= '<thead><tr>'
                    //<th>'.__('#','ccpw').'</th>
                    .'<th>'.__('Name','ccpw').'</th>
                    <th>'.__('Price','ccpw').'</th>';
                    if ($display_changes) {
                    $output .= '<th>' . __('24H %', 'ccpw') . '</th>';                    
                    }
                    $output .='</tr></thead>';   
                break;
                case "style-5":
                    $output .= '<thead><tr>
                    <th>'.__('Name','ccpw').'</th>
                    <th>'.__('Price','ccpw').' (USD)</th>
                    <th>'.__('Price','ccpw').' (EUR)</th>
                    <th>'.__('Price','ccpw').' (GBP)</th>
                    </tr></thead>';
                break;
                case "style-3":
                    $output .= '<thead> <tr>
                    <th>' . __('Name', 'ccpw') . '</th>
                    <th>' . __('Price', 'ccpw') . '</th>';
                    if ($display_changes) {
                    $output .= '<th>' . __('24H %', 'ccpw') . '</th>';
                    }
                    $output .= '</tr></thead>';
                break;

               default:
                    $display_charts = get_post_meta($post_id, 'display_list_price_chart', true);
                    $output .= '<thead> <tr>
                    <th>' . __('Name', 'ccpw') . '</th>
                    <th>' . __('Price', 'ccpw') . '</th>';
                        if ($display_changes) {
                        $output .= '<th>' . __('24H %', 'ccpw') . '</th>';
                        }
                        if ($display_charts) {
                        $output .= '<th>' . __('Chart (7D)', 'ccpw') . '</th>';
                        }
                    $output .= '</tr></thead>';
                break;
            }
            $output .= '<tbody>';
            $output .= $crypto_html;
            $output .= '</tbody></table></div>';

        } else if ($type == "price-card" || $type == "price-label") {
            /**
             * Creating Wrapper HTML For Price card and price label
             */
    
            $liveCls = '';
            if ($design_card == "style-4" || $design == "style-4") {
                $liveCls = 'ccpw-live';
                $output .= '<div '.$g_attr_for_live.' id="' . $id . '" class="ccpw-container ' . $type . ' ' . $liveCls . '"><div class="row">';
                $output .= $crypto_html;
                $output .= '</div></div>';

            } else {
                $output .= '<div id="' . $id . '" class="ccpw-container ' . $type .' ' . $liveCls . '"><div class="row">';
                $output .= $crypto_html;
                $output .= '</div></div>';
            }

        } else if ($type == "chart") {
            /**
             * Creating Wrapper HTML Chart
             */
            $output .= '<div id="' . $id . '" class="ccpw-container ' . $type . '">';

            if (is_array($display_currencies) && !empty($display_currencies)) {
                $all_coin_data = ccpwp_coin_arr($display_currencies, "all");
                $all_coin_data =  array_merge( array_flip($display_currencies), $all_coin_data );
                foreach ($all_coin_data as $currency) {
                    $coin = $currency;
                    /**
                     * generate_chart is handling chart layout
                     */
                    require dirname(__FILE__) . '/layouts/charts.php';
                    $output .= $chart_html;
                }
            }
            $output .= '</div>';

        } else if ($type == "multi-currency-tab") {
            //end chart type
            $output .= '<div class="currency_tabs" id="' . $id . '">';
            $output .= '<ul class="multi-currency-tab">';
            $i = 0;
            if (is_array($mt_currencies)) {
                unset($mt_currencies['BTC']);
                foreach ($mt_currencies as $currency) {
                    $i++;
                    $cls = '';
                    if ($i == 1) {
                        $cls = 'active-tab';
                    }
                    $slug = strtolower($currency);
                    $output .= '<li data-currency="' . $slug . '" class="' . $cls . '">' . $currency . '</li>';
                }
            }
            if (isset($mt_currencies['BTC'])) {
                $output .= '<li data-currency="btc" class="' . $cls . '">' . __("BTC", "ccpw") . '</li>';
            }
            $output .= '</ul><div><ul class="multi-currency-tab-content">';
            $output .= $crypto_html;
            $output .= '</ul></div></div>';

        }
        /**
         * Disabling ticker on mobile devices
         */

        if (wp_is_mobile()  ){
            if( ( $type == "ticker" && $ticker_in_mobile ) ||
                ( $type == "rss-feed" && $rss_style == 'ticker-rss' && $rss_ticker_in_mobile) ){
            }else{
                return $output . $styles ;
            }
        } else {
            return $output . $styles ;
        }
    }
 

  

}
