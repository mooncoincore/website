<?php
$currency_symbol = ccpwp_get_currency_symbol($fiat_currency);
$currency_price = ccpwp_usd_conversions($fiat_currency);
/*
 *  This is 'block' layout file for 'Price-block' type
 */
$coin_logo = ccpwp_coin_logo_html($coin['coin_id'], $size = 22);

//$is_chart_fill = get_post_meta($post_id, 'block_chart_fill', true);

$is_chart_fill = 'true';

$small_chart_color = '#006400';
$small_chart_bgcolor = '#90EE90';
/* Change chart color if required */
if (strpos($coin['percent_change_7d'], $change_sign_minus) !== false) {
    $small_chart_color = '#ff0000';
    $small_chart_bgcolor = '#ff9999';
}
if (isset($block_chart_color) && $block_chart_color != "") {
    $small_chart_bgcolor = $block_chart_color;
    $small_chart_color = $block_chart_color;
}

$volume24H = $volume;


if ($design_accordion== 'style-1') {

    $coin_chart = ccpw_generate_svg_chart(
        $coin_id,
        $coin_price,
        $small_chart_color,
        $small_chart_bgcolor,
        $period = '7d',
        $points = 1,
        $currency_symbol,
        $currency_price,
        $chart_fill ='true'
    );
    $coin_html .= '<div class="coin-container ccpw-block ccpw-main-accordion ' . $design_accordion . '" data-coin="' . $coin_name . '">';
    $coin_html .= '<div class="ccpw-coin-accordion-upper">';
    $coin_html .= $coin_logo . ' ' . '<span class="ccpw-coin-name">' . $coin_name . '</span>';
    $coin_html .= '<div class="ccpw-rightside"><span class="ccpw-coin-price">' . $fiat_symbol . $coin_price . ' </span>';
    $coin_html .= '<span class="ccpw-collapseBtn ccpw_icon-down-double"></span></div>';
    $coin_html .= "</div>";
    $coin_html .= '<div id="' . $coinId . '" class="ccpw-coin-accordion-dropdown" style="display:none;">';
    $coin_html .= '<div class="ccpw-coin-price-widget-left">' . $fiat_symbol . $coin_price . '</div>';
    if ($percent_change_24h < 0) {
        $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-down"><i class="ccpw_icon-down" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
    } else {
        $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-up"><i class="ccpw_icon-up" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
    }
    $coin_html .= '<div class="ccpw-block-chart" >' . $coin_chart . '</div><span class="chart24h">' . __('7D Chart', 'ccpw') . '</span>';
   
    $coin_html .= "</div></div>"; // end of ccpw-main-accordion

    }else if($design_accordion == 'style-2'){
        $coin_chart = ccpw_generate_svg_chart(
            $coin_id,
            $coin_price,
            $small_chart_color,
            $small_chart_bgcolor,
            $period = '7d',
            $points = 1,
            $currency_symbol,
            $currency_price,
            $chart_fill ='false'
        );

        $coin_html .= '<div  '.$coin_attr_for_live.' class="ccpw_coin_cont coin-container ccpw-block ccpw-main-accordion style-1" data-coin="' . $coin_name . '">';
        $coin_html .= '<div class="ccpw-coin-accordion-upper">';
        $coin_html .= $coin_logo . ' ' . '<span class="ccpw-coin-name">' . $coin_name . '</span>';
        $coin_html .= '<div class="ccpw-rightside"><span class="ccpw-coin-price live_price">' . $fiat_symbol . $coin_price . ' </span>';
        $coin_html .= '<span class="ccpw-collapseBtn ccpw_icon-down-double"></span></div>';
        $coin_html .= "</div>";
        $coin_html .= '<div id="' . $coinId . '" class="ccpw-coin-accordion-dropdown" style="display:none;">';
        $coin_html .= '<div class="ccpw-coin-price-widget-left live-pricing"><span class="live_p">' . $fiat_symbol . $coin_price . '</span></div>';
        if ($percent_change_24h < 0) {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-down live_c"><i class="ccpw_icon-down" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
        } else {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-up live_c"><i class="ccpw_icon-up" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
        }
        $coin_html .= '<div class="ccpw-block-chart" >' . $coin_chart . '</div><span class="chart24h">' . __('7D Chart', 'ccpw') . '</span>';
     
        $coin_html .= "</div></div>"; // end of ccpw-main-accordion 

    }else if($design_accordion == 'style-3'){
        $coin_html .= '<div class="coin-container ccpw-block ccpw-main-accordion style-1" data-coin="' . $coin_name . '">';
        $coin_html .= '<div class="ccpw-coin-accordion-upper">';
        $coin_html .= $coin_logo . ' ' . '<span class="ccpw-coin-name">' . $coin_name . '</span>';
        $coin_html .= '<div class="ccpw-rightside"><span class="ccpw-coin-price live_price">' . $fiat_symbol . $coin_price . ' </span>';
        $coin_html .= '<span class="ccpw-collapseBtn ccpw_icon-down-double"></span></div>';
        $coin_html .= "</div>";
        $coin_html .= '<div id="' . $coinId . '" class="ccpw-coin-accordion-dropdown" style="display:none;">';

        $coin_html .= '<div class="ccpw-row"><span class="ccpw-label">'.__('Symbol','ccpw').'</span><span class="ccpw-value">' . 
        $coin_symbol . '</span></div>';

        $coin_html .= '<div class="ccpw-row"><span class="ccpw-label">'.__('Price','ccpw').'</span><span class="ccpw-value">' . 
        $fiat_symbol . $coin_price . '</span></div>';

        $coin_html .= '<div class="ccpw-row"><span class="ccpw-label">'.__('Changes 24h','ccpw').'</span>';

        if ($percent_change_24h < 0) {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-down"><i class="ccpw_icon-down" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
        } else {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-up"><i class="ccpw_icon-up" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
        }

        $coin_html .= '</div>';

        $coin_html .= '<div class="ccpw-row"><span class="ccpw-label">'.__('Total Volume','ccpw').'</span><span class="ccpw-value">' . 
        $volume . '</span></div>';

        $coin_html .= '<div class="ccpw-row"><span class="ccpw-label">'.__('Market Cap','ccpw').'</span><span class="ccpw-value">' . 
        $market_cap . '</span></div>';

        $coin_html .= '<div class="ccpw-row"><span class="ccpw-label">'.__('Circulating Supply','ccpw').'</span><span class="ccpw-value">' . 
        $available_supply .' '.$coin_symbol. '</span></div>';

        //<div class="ccpw-coin-24h-change live_c">'.$percent_change_24h.'</div>';
     
        $coin_html .= "</div></div>"; // end of ccpw-main-accordion 
    }