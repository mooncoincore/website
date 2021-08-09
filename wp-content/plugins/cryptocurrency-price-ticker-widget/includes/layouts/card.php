<?php 

$coin_html .= '<div class="' . $card_cols . ' cardtop-area">';

if ($card_display_changes) {
	
	$changes_coin_html .= '<span class="ccpw-changes ' . $change_class_24h . '">';
	$changes_coin_html .= $change_sign_24h . $percent_change_24h;
	$changes_coin_html .= '</span>';

}

//STYLE-1
if ($design_card == "style-1" || $design == "style-1") {

	$coin_html .= '<div class="coin-container text-center ccpw-card ' . $design_card . ' ' . $design . '"><div class="ccpw-card-body">';
	$coin_html .= $coin_logo_html;
		$coin_html .= '<div class="price-area"><div class="ccpw-name">' . $coin_name . '</div>';
		$coin_html .= $changes_coin_html;

		if ($card_display_changes) {
			$coin_html .= '<span class="changes-time">' . __('24H', 'ccpw') . '</span>';
		}
		$coin_html .= '</div>';

	$coin_html .= $coin_price_html;
	$coin_html .= '</div></div>';
}

//STYLE-2			
else if ($design_card == "style-2" || $design == "style-2") {
	$coin_html .= '<div class="coin-container text-center ccpw-card ' . $design_card . ' ' . $design . '"><div class="ccpw-card-body">';

	$coin_html .= $coin_logo_html;
	
		$coin_html .= '<div class="name">' . $coin_name . '<br/>';
		$coin_html .= '<div class="ccpw-price">' . $fiat_symbol . ' <span>' . $coin_price . '</span></div></div>';
	
	$coin_html .= '<div class="market-data"><div class="ccpw-supply"><span class="d-left">' . __('Supply ', 'ccpw') . '</span><span class="d-right">' . $available_supply . ' ' . strtoupper($coin_symbol) . '</span></div>';

	$coin_html .= '<div class="ccpw-vol"><span class="d-left">' . __('Volume ', 'ccpw') . '</span><span class="d-right">' . $fiat_symbol . $volume . '</span></div>';
	$coin_html .= '<div class="ccpw-cap"><span class="d-left">';
	$coin_html .= __('Market Cap ', 'ccpw') . '</span><span class="d-right">' . $market_cap_html;
	$coin_html .= '</span></div>';

	if ( ($card_display_changes && $changes_coin_html!="") || $display_changes ) {	// show 'Change' div only if changes available
		$coin_html .= '<div class="ccpw-price-changes"><span class="d-left">' . __('Change ', 'ccpw') . '</span><span class="d-right">' . $changes_coin_html . '</span></div></div>';
	} else {
		$coin_html .= '</div>';
	}

	$coin_html .= '</div></div>';

} else if ($design_card == "style-4" || $design == "style-4") {
	
	$coin_html .= '<div  '.$coin_attr_for_live.' class="ccpw_coin_cont coin-container text-center ccpw-card ' . $design_card . ' ' . $design . '" id="'.$coin['coin_id'].'" ><div class="ccpw-card-body">';

	$coin_html .= $coin_logo_html;
	$coin_html .= '<div class="ccpw-name">' . $coin_name . '</div>';
	if($percent_change_24h!="" ){
		$coin_html .= $live_price_changes . $live_changes;
	}else{	// Show static price if 24h live changes are not available
		$coin_html .= $live_price_changes ;
	}
	$coin_html .= '</div></div>';

}else if ($design_card == "style-5") {

}else if ($design_card == "style-6") {

	$currency_symbol = ccpwp_get_currency_symbol($fiat_currency);
	$currency_price = ccpwp_usd_conversions($fiat_currency);
	
		$small_chart_color = '#006400';
		$small_chart_bgcolor = '#90EE90';
		if (strpos($coin['percent_change_7d'], $change_sign_minus) !== false) {
			$small_chart_color = '#ff0000';
			$small_chart_bgcolor = '#ff9999';
		}
		$coin_chart = ccpw_generate_svg_chart(
			$coin_id,
			$coin_price,
			$small_chart_color,
			$small_chart_bgcolor,
			$period = '7d',
			$points = 0,
			$currency_symbol,
			$currency_price,
			$chart_fill='false'
		);
		
	
	$coin_html .= '<div class="coin-container text-center ccpw-card ' . $design_card . '"><div class="ccpw-card-body">';
	$coin_html .= $coin_logo_html;
		$coin_html .= '<div class="ccpw-card-chart">' . $coin_chart . '</div><div class="price-area"><div class="ccpw-name">' . $coin_name . '</div>';
		$coin_html .= $changes_coin_html;

		if ($card_display_changes) {
			$coin_html .= '<span class="changes-time">' . __('24H', 'ccpw') . '</span>';
		}
		$coin_html .= '</div>';

	$coin_html .= $coin_price_html;
	$coin_html .= '</div></div>';

}else if ($design_card == "style-7") {

	//Price card showing multicurrencies

	$coin_html .= '<div class="coin-container text-center ccpw-card ' . $design_card . '"><div class="ccpw-card-body">';

	$coin_html .= $coin_logo_html;
	$coin_html .= '<div class="multicard-price-area full">
			
			<div class="ccpw-multicard-symbol">' . $coin_symbol . '</div>
			<div class="ccpw-multicard-name"><span class="ccpw-multicard-subtitle">' . $coin_name . '</span></div>';
	$coin_html .= '</div>';

	$coin_html .= '<div class="ccpw-multi-prices">';

	$coin_html .= '<div class="ccpw-multi-price">';
	$coin_html .= '<div class="ccpw-multi-price-header">';
	$coin_html .= __('USD ', 'ccpw');
	$coin_html .= '</div>';

	$coin_html .= '<div class="ccpw-multi-price-text">';
	$coin_html .= ccpwp_get_currency_symbol('USD') . $coin_price_html;//PRICE IN USD
	$coin_html .= '</div>';
	$coin_html .= '</div>';
			
	       // $multicurrency = ccpwp_usd_conversions("all");
		    
			//EUR
	$coin_html .= '<div class="ccpw-multi-price">';
	$coin_html .= '<div class="ccpw-multi-price-header">';
	$coin_html .= __('EUR ', 'ccpw');
	$coin_html .= '</div>';

	$coin_html .= '<div class="ccpw-multi-price-text">';
	$coin_html .= ccpwp_get_currency_symbol('EUR') . ccpwp_format_number($multicurrency["EUR"] * $coin['price']);
	$coin_html .= '</div>';
	$coin_html .= '</div>';
			
			//GBP
	$coin_html .= '<div class="ccpw-multi-price">';
	$coin_html .= '<div class="ccpw-multi-price-header">';
	$coin_html .= __('GBP ', 'ccpw');
	$coin_html .= '</div>';

	$coin_html .= '<div class="ccpw-multi-price-text">';
	$coin_html .= ccpwp_get_currency_symbol('GBP') . ccpwp_format_number($multicurrency["GBP"] * $coin['price']);
	$coin_html .= '</div>';
	$coin_html .= '</div>';

	$coin_html .= '</div>';
	$coin_html .= '</div></div>';

}else {
	//STYLE-3

	$coin_html .= '<div class="coin-container ccpw-card ' . $design_card . ' ' . $design . '"><div class="ccpw-card-body">';
	$coin_html .= $coin_logo_html;
	
		$coin_html .= '<div class="name ccpw-card-title">' . $coin_symbol . '<br/>
			    <span class="ccpw-card-subtitle">' . $coin_name . '</span></div>';
	
	$coin_html .= '<div class="changes_wrp ccpw-card-text">';
	$coin_html .= '<div class="ccpw-price">' . __('Price ', 'ccpw') . $fiat_symbol . ' <span>' . $coin_price . '</span></div>';
	$coin_html .= '<div class="ccpw-price-changes">' . $all_c_p_html . '</div>';

	$coin_html .= '</div></div></div>';
}


$coin_html .= '</div>';