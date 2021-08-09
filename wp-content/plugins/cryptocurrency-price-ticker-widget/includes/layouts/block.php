<?php
$currency_symbol = ccpwp_get_currency_symbol($fiat_currency);
$currency_price = ccpwp_usd_conversions($fiat_currency);
/*
 *  This is 'block' layout file for 'Price-block' type
 */
$coin_logo = ccpwp_coin_logo_html($coin['coin_id'], $size = 22);

$is_chart_fill = get_post_meta($post_id, 'block_chart_fill', true);

$is_chart_fill = $is_chart_fill!=''?'true':'false';

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

// availabe design: style-1 accordion, style-2 rank card, style-3 and style-4

if ($design_block == 'style-1') {

    $coin_chart = ccpw_generate_svg_chart(
        $coin_id,
        $coin_price,
        $small_chart_color,
        $small_chart_bgcolor,
        $period = '7d',
        $points = 1,
        $currency_symbol,
        $currency_price,
        $chart_fill = $is_chart_fill
    );

    $price_in_eur = 'EUR: ' . ccpwp_get_currency_symbol('EUR') . ccpwp_format_number($multicurrency["EUR"] * $coin['price']);
    $price_in_gbp = 'GBP: ' . ccpwp_get_currency_symbol('GBP') . ccpwp_format_number($multicurrency["GBP"] * $coin['price']);

    $coin_html .= '<div class="coin-container ccpw-block ccpw-main-accordion ' . $design_block . '" data-coin="' . $coin_name . '">';
    $coin_html .= '<div class="ccpw-coin-accordion-upper">';
    $coin_html .= $coin_logo . ' ' . '<span class="ccpw-coin-name">' . $coin_name . '</span>';
    $coin_html .= '<div class="ccpw-rightside"><span class="ccpw-coin-price">' . $fiat_symbol . $coin_price . ' </span>';
    $coin_html .= '<span class="ccpw-collapseBtn ccpw_icon-down-double"></span></div>';
    $coin_html .= "</div>";
    $coin_html .= '<div id="' . $coinId . '" class="ccpw-coin-accordion-dropdown" style="display:none;">';
    $coin_html .= '<div class="ccpw-coin-price-widget-left">' . $fiat_symbol . $coin_price . '</div>';
    $coin_html .= '<div class="ccpw-coin-price-widget-right"><span class="ccpw-coin-price-widget">' . $price_in_eur . '</span><br/>';
    $coin_html .= '<span class="ccpw-coin-price-widget">' . $price_in_gbp . '</span></div>';
    $coin_html .= '<div class="ccpw-block-chart" >' . $coin_chart . '</div><span class="chart24h">' . __('7D Chart', 'ccpw') . '</span>';
    if ($percent_change_24h != "") { // don't show changes div if changes are not available
        $coin_html .= '<span class="ccpw-coin-24h-change" style="background:' . $small_chart_bgcolor . ';">' . $percent_change_24h . '</span>';
    }
    $coin_html .= "</div></div>"; // end of ccpw-main-accordion

} else if ($design_block == 'style-2') {

    $coin_chart = ccpw_generate_svg_chart(
        $coin_id,
        $coin_price,
        $small_chart_color,
        $small_chart_bgcolor,
        $period = '7d',
        $points = 0,
        $currency_symbol,
        $currency_price
    );

    $coin_html .= '<div class="' . $block_cols . ' blocktop-area">';
    $coin_html .= '<div class="coin-container ccpw-block ' . $design_block . '">';
    $coin_html .= '<div class="ccpw-block-body">';
    $coin_html .= '<div class="ccpw-coin-intro">';

    $coin_html .= '<span class="ccpw-coin-logo">' . $coin_logo . '</span>';
    $coin_html .= '<span class="ccpw-coin-name">' . $coin_name . '</span>';
    $coin_html .= '<span class="ccpw-coin-symbol">(' . $coin_symbol . ')</span>';
    $coin_html .= '</div>';
//    $coin_html .= '<div class="ccpw-coin-rank">' . __('Rank', 'ccpw') . ': ' . $coin['rank'] . '</div>';
    $coin_html .= '<div class="ccpw-coin-info">';
    $coin_html .= '<span class="ccpw-coin-price">' . $fiat_symbol . $coin_price . '</span>';
    if ($block_display_changes && $percent_change_24h != "") { // don't show changes div if changes are not available
        if ($percent_change_24h < 0) {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-down"><i class="ccpw_icon-down" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
        } else {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-up"><i class="ccpw_icon-up" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
        }
    }

    $coin_html .= '<span class="ccpw-coin-24h-volume"><span>' . __('(24H Vol)', 'ccpw') . '</span><br/>' . $fiat_symbol . $volume24H . '</span>';
    $coin_html .= '</div>';
    $coin_html .= $coin_chart;
    $coin_html .= '<span class="chart24h">' . __('7D Chart', 'ccpw') . '</span></div>'; // end of ccpw-block-body
    $coin_html .= '</div>'; // end of ccpw-main-card
    $coin_html .= '</div>'; // end of blocktop-area
} else if ($design_block == 'style-3') {

    $coin_chart = ccpw_generate_svg_chart(
        $coin_id,
        $coin_price,
        $small_chart_color,
        $small_chart_bgcolor = ccpw_hex2rgba($small_chart_bgcolor, 0.30),
        $period = '7d',
        $points = 0,
        $currency_symbol,
        $currency_price
    );
    $coin_html .= '<div class="' . $block_cols . ' blocktop-area">';

    $coin_html .= '<div class="coin-container ccpw-block ' . $design_block . '">';
    $coin_html .= '<div class="ccpw-block-body">';
    $coin_html .= '<div class="ccpw-coin-intro">';
    $coin_html .= '<span class="ccpw-coin-name">' . $coin_name . '</span>';
    $coin_html .= '<span class="ccpw-coin-symbol">(' . $coin_symbol . ')</span>';
    $coin_html .= '<span class="ccpw-coin-price">' . $fiat_symbol . $coin_price . '</span>';
    $coin_html .= '</div>';
    if ($block_display_changes && $percent_change_24h != "") { // don't show changes div if changes are not available
        if ($percent_change_24h < 0) {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-down"><i class="ccpw_icon-down" aria-hidden="true"></i> ' . $percent_change_24h . ' <span>' . __('24H', 'ccpw') . '</span></span>';
        } else {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-up"><i class="ccpw_icon-up" aria-hidden="true"></i> ' . $percent_change_24h . ' <span>' . __('24H', 'ccpw') . '</span></span>';
        }
    }
    $coin_html .= '<div class="ccpw-coin-info">';
    $coin_html .= '<span class="ccpw-coin-24h-volume">' . __('24H Vol:', 'ccpw') . ' ' . $fiat_symbol . $volume24H . '</span>';
    $coin_html .= '</div>';
    $coin_html .= $coin_chart;
    $coin_html .= '</div>'; // end of ccpw-block-body
    $coin_html .= '</div>'; // end of ccpw-main-card
    $coin_html .= '</div>'; // end of blocktop-area

} else if ($design_block == 'style-4') {

    $coin_chart = ccpw_generate_svg_chart(
        $coin_id,
        $coin_price,
        $small_chart_color = ccpw_hex2rgba($small_chart_color, 0.70),
        $small_chart_bgcolor = ccpw_hex2rgba($small_chart_bgcolor, 0.20),
        $period = '7d',
        $points = 0,
        $currency_symbol,
        $currency_price
    );
    $coin_html .= '<div class="' . $block_cols . ' blocktop-area">';
    $coin_html .= '<div class="coin-container ccpw-block ' . $design_block . '">';
    $coin_html .= '<div class="ccpw-block-body">';
    $coin_html .= '<div class="ccpw-coin-intro">';
    $coin_html .= '<span class="ccpw-coin-name">' . $coin_name . '</span>';
    $coin_html .= '<span class="ccpw-coin-symbol">(' . $coin_symbol . ')</span>';
    $coin_html .= '</div>';
    $coin_html .= '<span class="ccpw-coin-logo">' . $coin_logo . '</span>';
    $coin_html .= '<div class="ccpw-coin-info">';
    $coin_html .= '<span class="ccpw-coin-price">' . $fiat_symbol . $coin_price . '</span>';
    if ($block_display_changes && $percent_change_24h != "") { // don't show changes div if changes are not available
        if ($percent_change_24h < 0) {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-down"><i class="ccpw_icon-down" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
        } else {
            $coin_html .= '<span class="ccpw-coin-24h-change ccpw-status-up"><i class="ccpw_icon-up" aria-hidden="true"></i> ' . $percent_change_24h . '</span>';
        }
    }
    $coin_html .= '<span class="ccpw-coin-24h-volume"><span>' . __('(24H Vol)', 'ccpw') . '</span><br/>' . $fiat_symbol . $volume24H . '</span>';
    $coin_html .= '</div>';
    $coin_html .= $coin_chart;
    $coin_html .= '</div>'; // end of ccpw-block-body
    $coin_html .= '</div>'; // end of ccpw-main-card
    $coin_html .= '</div>'; // end of blocktop-area
} else if ($design_block == 'style-5') {

    $coin_html .= '<div class="blocktop-area">';
    /*
     *    Grab EUR and GBP price.
     *    Check if number formating is enabled
     */
    $coin_price = $coin['price'];

    $price_in_eur = ccpwp_format_number($multicurrency["EUR"] * $coin['price']);
    $price_in_gbp = ccpwp_format_number($multicurrency["GBP"] * $coin['price']);
    $coin_price = ccpwp_format_number($coin_price);

    $usd_symbol = ccpwp_get_currency_symbol('USD');
    $eur_symbol = ccpwp_get_currency_symbol('EUR');
    $gbp_symbol = ccpwp_get_currency_symbol('GBP');

    $coin_html .= '<div class="coin-container ccpw-block style-5">';

    $coin_html .= '<div class="ccpw-block-body">';
    $coin_html .= '<div class="ccpw-coin-price-banner">';
    $coin_html .= '<span class="ccpw-dynamic-coin-symbol">$</span><span class="ccpw-dynamic-coin-price">' . $coin_price . '</span>';
    $coin_html .= '</div>';
    $coin_html .= '<div class="ccpw-coin-details-footer">';

    $coin_html .= '<div class="ccpw-price-change"><table class="ccpw-priceChange-table">';
    $coin_html .= '<tr><td>' . $coin_logo_html;
    $coin_html .= '<span class="ccpw-coin-name">' . $coin_name . '</span>';
    $coin_html .= '<span class="ccpw-coin-symbol">(' . $coin_symbol . ')</span>';
    $coin_html .= '</td>';

    if ($percent_change_24h != "") { // don't show changes div if changes are not available
        if ($percent_change_24h < 0) {
            $coin_html .= '<td><span class="ccpw-coin-24h-change ccpw-status-down"><span>' . __('24H', 'ccpw') . '</span><br/><i class="ccpw_icon-down" aria-hidden="true"></i> ' . $percent_change_24h . '</span></td>';
        } else {
            $coin_html .= '<td><span class="ccpw-coin-24h-change ccpw-status-up"><span>' . __('24H', 'ccpw') . '</span><br/><i class="ccpw_icon-up" aria-hidden="true"></i> ' . $percent_change_24h . '</span></td>';
        }
    }
    $coin_html .= '</tr></table></div>';

    $coin_html .= '<div class="ccpw-currency-switcher">
                            <span class="ccpw-currencybtn active" data-coin-symbol="' . $usd_symbol . '" data-coin-price="' . $coin_price . '">' . __('USD', 'ccpw') . '</span>
                            <span class="ccpw-currencybtn" data-coin-symbol="' . $eur_symbol . '" data-coin-price="' . $price_in_eur . '">' . __('EUR', 'ccpw') . '</span>
                            <span class="ccpw-currencybtn" data-coin-symbol="' . $gbp_symbol . '" data-coin-price="' . $price_in_gbp . '">' . __('GBP', 'ccpw') . '</span>
                        </div>';
    $coin_html .= '</div>';
    $coin_html .= '</div>';
    $coin_html .= '</div>';
    $coin_html .= '</div>';
}
