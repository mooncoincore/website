<?php


if ($design == "style-1" ) {

	$display_charts = get_post_meta($post_id,'display_list_price_chart',true);
	$currency_symbol = ccpwp_get_currency_symbol($fiat_currency);
	$currency_price = ccpwp_usd_conversions($fiat_currency);
	
		$small_chart_color = '#006400';
		$small_chart_bgcolor = '#90EE90';
		$chart_fill = 'false';
		$coin_chart = ccpw_generate_svg_chart(
			$coin_id,
			$coin_price,
			$small_chart_color,
			$small_chart_bgcolor,
			$period = '7d',
			$points = 0,
			$currency_symbol,
			$currency_price,
			$chart_fill
		);

	$coin_html .= '<tr  id="' . esc_attr($coin_id) . '">';
	$coin_html .= '<td>'.$cmc_link_start . $coin_logo_html . '<span class="name">' . $coin_name . '</span>
    <span class="symbol">(' . $coin_symbol . ')</span>               
			'. $cmc_link_end .'</td>';

	$coin_html .= '<td class="price">' . $coin_price_html . '</td>';
	if ($display_changes) {
		$coin_html .= '<td>' . $changes_coin_html . '</td>';
	}

	if($display_charts){
	$coin_html .= '<td><div class="ccpw-card-chart">'.$coin_chart.'</div></td>';
	}
	$coin_html .= '</tr>';
}
						
//STYLE-2
else if ( $design == "style-2" ) {
	$coin_html .= '<tr  id="' . esc_attr($coin_id) . '">';
	$coin_html .= '<td>'. $cmc_link_start . $coin_logo_html . '<span class="name">' . $coin_name . '</span>' . $cmc_link_end . '</td>';
	$coin_html .= '<td class="price"><div class="price-value">' . $coin_price_html . '</div></td>';

	if ($display_changes) {
		$coin_html .= '<td>';
		$coin_html .= $changes_coin_html;
		$coin_html .= '</td>';
	}

	$coin_html .= '<td><div class="supply">' . $available_supply . ' ' . strtoupper($coin_symbol) . '</div></td>';
	$coin_html .= '<td><div class="list-vol">' . $fiat_symbol . $volume . '</div></td>';
	$coin_html .= '<td class="price-v"><div class="market_cap">' . $fiat_symbol . $market_cap . '</div></td>';
	$coin_html .= '</tr>';
}

else if ( $design == "style-4" ) {
	$coin_html .= '<tr '.$coin_attr_for_live.' class="ccpw_coin_cont">';
//	$coin_html .= '<td class="rank">' . $coin['rank'] . '</td>';
	$coin_html .= '<td>'. $cmc_link_start . $coin_logo_html . '<span class="name">' . $coin_name . '</span><span class="symbol">(' . $coin_symbol . ')</span>' . $cmc_link_end . '</td>';
	$coin_html .= '<td ><span class="live_p">' . $fiat_symbol . $coin_price . '</span></td>';
	if($display_changes){
		$coin_html .= '<td class="live_c ccpw-changes ' . $change_class_24h . '">' . $change_sign_24h . $percent_change_24h . '</td>';
	}
	$coin_html .= '</tr>';

}
//MULTICURRENCY LIST-WIDGET
else if ( $design == "style-5" ) {

	$coin_html .= '<tr  id="' . esc_attr($coin_id) . '">';
	$coin_html .= '<td>'.$cmc_link_start.$coin_logo_html . '<span class="name">' . $coin_name . '</span>
	<span class="symbol">(' . $coin_symbol . ')</span>'.$cmc_link_end.'</td>';

	$coin_html .= '<td class="price">' . ccpwp_get_currency_symbol('USD') . $coin_price_html . '</td>';

	$coin_html .= '<td class="ccpw-multi-price-text">';
	$coin_html .= ccpwp_get_currency_symbol('EUR') . ccpwp_format_number($multicurrency["EUR"] * $coin['price']);
	$coin_html .= '</td>';

	$coin_html .= '<td class="ccpw-multi-price-text">';
	$coin_html .= ccpwp_get_currency_symbol('GBP') . ccpwp_format_number($multicurrency["GBP"] * $coin['price']);
	$coin_html .= '</td>';

	$coin_html .= '</tr>';
} else {
	$coin_html .= '<tr id="' . esc_attr($coin_id) . '">';
	$coin_html .= '<td>' . $coin_logo_html . '<span class="name">' . $coin_name . '</span></td>';
	$coin_html .= '<td class="price">' . $coin_price_html . '</td>';
	if ($display_changes) {
	$coin_html .= '<td>' . $changes_coin_html . '</td>';
	}
	$coin_html .= '</tr>';

}
						