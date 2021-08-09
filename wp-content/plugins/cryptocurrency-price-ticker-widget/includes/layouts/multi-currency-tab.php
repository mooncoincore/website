<?php 

$coin_price = $coin['price'];
$data_attr = '';

if (is_array($mt_currencies) && in_array("BTC", $mt_currencies)) {
	$data_attr .= 'data-btc="' . ccpwp_format_number( $coin_price / $btc_price ). ' BTC"';
}

if (is_array($mt_currencies)) {
	//unset($mt_currencies['BTC']);
	foreach ($mt_currencies as $currency) {
		$slug = strtolower($currency);

		if (isset($usd_conversions[$currency])) {
			$curr_rate = $usd_conversions[$currency];
			$price = ccpwp_format_number($coin_price * $curr_rate);
			$fiat_symbol = ccpwp_get_currency_symbol($currency);
			$data_attr .= 'data-' . $slug . '="' . $fiat_symbol .$price . '"';
		}

	}
}
$currency_symbol = ccpwp_get_currency_symbol($fiat_currency);
$usd_price = ccpwp_format_number($coin_price);
$coin_html .= '<li id="' . esc_attr($coin_id) . '">';
$coin_html .= '<div class="mtab-content">'.$cmc_link_start;

$coin_html .= '<span class="mtab_icon">' . $coin_logo_html . '</span>';

$coin_html .= '<span class="mtab_name">'.$coin_name.'(' . $coin_symbol . ')</span>'. $cmc_link_end.'
				
				<div class="tab-price-area"><span ' . $data_attr . 'data-usd="' . $usd_price . '"  class="mtab_price">' . $currency_symbol . $usd_price . '</span>';

if ($display_changes && $percent_change_24h!="") {
	$coin_html .= '<span class="mtab_ ' . $change_class_24h . '">';
	$coin_html .= $change_sign_24h . $percent_change_24h;
	$coin_html .= '</span>';
}
$coin_html .= '</div></div></li>';
