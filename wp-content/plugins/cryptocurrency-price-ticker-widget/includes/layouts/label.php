<?php

$coin_html .= $cmc_link_start.'<li id="' . esc_attr($coin_id) . '" class="' . $design . '"  >';
$coin_html .= '<div class="coin-container ' . $design . '">';
//STYLE-1
if ($design == "style-1") {
	$coin_html .= $coin_logo_html;
	$coin_html .= '<span class="name">' . $coin_symbol . '</span>';
	$coin_html .= $coin_price_html;
	$coin_html .= $changes_coin_html;
}
//STYLE-2
else if ($design == "style-2") {

	
	$coin_html .= '<span class="ccpw_icon">' . $coin_logo . '</span>';
	
	$coin_html .= '<div class="label-style2"><span class="name">' . $coin_name . '</span>';
	$coin_html .= '<span class="symbol">(' . $coin_symbol . ')</span>';
	$coin_html .= '<br class="style-2-responsive"/>' . $coin_price_html;
	$coin_html .= $changes_coin_html . '</div>';

} else if ($design == "style-4") {

	$coin_html .= '<div '.$coin_attr_for_live.'  class="ccpw_coin_cont '. $design . '">';
	$coin_html .= $coin_logo_html;
	$coin_html .= '<span class="name">' . $coin_name . '</span>';
	$coin_html .= $live_price_changes;
	if ($display_changes || $display_changes_old) {
		$coin_html .= $live_changes;
	}
	$coin_html .= '</div>';


}
					
//Multicurrency price label
else if ($design == "style-5") {
	$coin_html .= '<div class="ccpw-multilabel">
					
					<span class="ccpw-multilabel-name name">' . $coin_symbol . '</span>';
	$coin_html .= $coin_logo_html;

	$coin_html .= '<div class="ccpw-multilabel-prices">';

	$coin_html .= '<div class="ccpw-multi-price">';
	$coin_html .= '<div class="ccpw-multi-price-header">';
	$coin_html .= '<span class="name">' . __("USD", "ccpw") . '</span>';
	$coin_html .= '</div>';

	$coin_html .= '<div class="ccpw-multi-price-text">';
	$coin_html .= ccpwp_get_currency_symbol('USD') . $coin_price_html;
	$coin_html .= '</div>';
	$coin_html .= '</div>';
			
	               // $multicurrency = ccpwp_usd_conversions("all");
		    
			//EUR
	$coin_html .= '<div class="ccpw-multi-price">';
	$coin_html .= '<div class="ccpw-multi-price-header">';

	$coin_html .= '<span class="name">' . __("EUR", "ccpw") . '</span>';

	$coin_html .= '</div>';

	$coin_html .= '<div class="ccpw-multi-price-text">';
	$coin_html .= '<span class="ticker-price">';
	$coin_html .= ccpwp_get_currency_symbol('EUR') . ccpwp_format_number($multicurrency["EUR"] * $coin['price']);
	$coin_html .= '</span>';
	$coin_html .= '</div>';
	$coin_html .= '</div>';
			
			//GBP
	$coin_html .= '<div class="ccpw-multi-price">';
	$coin_html .= '<div class="ccpw-multi-price-header">';
	$coin_html .= '<span class="name">' . __('GBP ', 'ccpw') . '</span>';
	$coin_html .= '</div>';

	$coin_html .= '<div class="ccpw-multi-price-text">';
	$coin_html .= '<span class="ticker-price">';
	$coin_html .= ccpwp_get_currency_symbol('GBP') . ccpwp_format_number($multicurrency["GBP"] * $coin['price']);
	$coin_html .= '</span>';
	$coin_html .= '</div>';
	$coin_html .= '</div>';

	$coin_html .= '</div></div>';

}
					
//STYLE-3
else {
	$coin_html .= $coin_logo_html;
	$coin_html .= '<span class="name">' . $coin_name . '</span>';
	$coin_html .= $coin_price_html;
	if ($display_changes) {
		$coin_html .= '';
	}
	$coin_html .= '<div class="style-3-changes">' . $all_c_p_html . '</div>';
}


$coin_html .= '</div></li>'.$cmc_link_end;