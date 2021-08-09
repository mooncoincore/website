<?php

$stats_currency = get_post_meta($post_id, 'quick_stats_currency', true);

$crypto_html = '';

$coin = ccpwp_get_coin_stats( $stats_currency ) ;

$market_cap = $coin['market_cap'];
if( is_numeric( $market_cap ) && $market_cap > 0){
	$market_cap = '$'.ccpwp_format_number( $coin['market_cap'] );
}
$volume =  $coin['total_volume'];
if( is_numeric( $market_cap ) && $market_cap > 0){
	$volume = '$'.ccpwp_format_number( $coin['total_volume'] );
}
$market_cap_n_volume = '0.00';

if( !empty( $coin['market_cap'] ) && !empty( $coin['total_volume'] ) &&
$coin['market_cap'] > 0 && $coin['total_volume'] > 0 ){
		$market_cap_n_volume = ccpwp_format_number( $coin['total_volume'] / $coin['market_cap'] );
}

$low_7d = $coin['low_7d'];
if( is_numeric( $low_7d )){
	$low_7d = '$'.ccpwp_format_number($coin['low_7d']);
}
$high_7d = $coin['high_7d'];
if( is_numeric( $low_7d )){
	$low_7d = '$'.ccpwp_format_number($coin['high_7d']);
}

$btc_ratio = $coin['btc_ratio'];
if( is_numeric( $btc_ratio ) && $btc_ratio > 0 ){
	$btc_ratio = '1 BTC = '.number_format($coin['btc_ratio'],2).' '.$coin['symbol'];
}
$html  ='<!---------- CCPWP Version:-'. CCPWP_VERSION  .' By Cool Plugins Team-------------->';
$html .= '<div id="ccpwp_'.$coin['id'].'_stats" class="ccpwp_stats_container">';
$html .= '<table class="ccpwp_stats_table"><tbody>';

$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>Market Cap Rank</th>';
	$html .= '<td>#'.$coin['rank'].'</td>';
$html .= '</tr>';

$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>'.$coin['name'].' Price</th>';
	$html .= '<td>$'. ccpwp_format_number( $coin['price'] ).'</td>';
$html .= '</tr>';

$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>Market Cap</th>';
	$html .= '<td>'.$market_cap.'</td>';
$html .= '</tr>';

$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>Market Cap Dominance</th>';
	$html .= '<td>'. number_format( $coin['market_dominance'], 2 ).'%</td>';
$html .= '</tr>';

$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>Trading Volume</th>';
	$html .= '<td>'.$volume.'</td>';
$html .= '</tr>';

$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>Volume / Market Cap</th>';
	$html .= '<td>'.$market_cap_n_volume.'</td>';
$html .= '</tr>';


$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>24h Low / 24h High</th>';
	$html .= '<td>$'. ccpwp_format_number($coin['low_24h']) .' / $'. ccpwp_format_number($coin['high_24h']).'</td>';
$html .= '</tr>';

$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>7d Low / 7d High</th>';
	$html .= '<td>'.$low_7d.' / '. $high_7d.'</td>';
$html .= '</tr>';
	
$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>All-Time High</th>';
	$html .= '<td>$'. ccpwp_format_number($coin['ath']) .'</td>';
$html .= '</tr>';

$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>Since All-Time High</th>';
	$html .= '<td>'. number_format($coin['since_ath'],2) .'%</td>';
$html .= '</tr>';

$html .= '<tr class="ccpwp_stats_row">';
	$html .= '<th>'.$coin['name'].' / Bitcoin Ratio</th>';
	$html .= '<td>'.$btc_ratio.'</td>';
$html .= '</tr>';

$html .= '</tbody></table>';
$html .= '</div>';

$crypto_html = $html;