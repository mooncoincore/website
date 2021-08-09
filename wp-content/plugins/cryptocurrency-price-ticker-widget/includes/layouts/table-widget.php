<?php
$crypto_html = '';
$getCoins='';
$id = "ccpw-coinslist_wrapper";
switch($show_coins){
	case 'top-10':
		$getCoins='10';
	break;
	case 'top-50':
		$getCoins='50';
	break;
	case 'top-100':
		$getCoins='100';
	break;
	default:
	break;
}
if($getCoins){
$pagination=$getCoins<$coin_per_page?$getCoins:$coin_per_page;
}else{
	$pagination=$coin_per_page;	
}
$live_class = $is_live_changes!=""?'ccpw-live':'';
$crypto_html .= '<div id="' . $id . '" class="' . $cls . '">

<table '.$g_attr_for_live.' id="ccpw-datatable-' . $post_id . '"
class="display ccpw_table_widget table-striped table-bordered no-footer '.$live_class.'"
data-currency-type="' . $fiat_currency . '" data-next-coins="' . $ccpw_next_coins . '" data-loadinglbl="' . $coin_loading_lbl . '" data-live-changes="'.$is_live_changes.'"
data-prev-coins="' . $ccpw_prev_coins . '" data-currency-slug="' . esc_url(home_url($cmc_slug)) . '"
data-required-currencies="' . $show_coins . '" data-zero-records="' . $ccpw_no_data . '"
 data-pagination="' . $pagination. '" data-dynamic-link="'.$is_cmc_enable.'" 
data-number-formating="' . $enable_formatting . '"  
style="border:none!important;">

<thead data-preloader="' . $preloader_url . '">
<th data-classes="desktop ccpw_coin_rank" data-index="rank">' . __('#', 'ccpw') . '</th>
<th data-classes="desktop ccpw_name" data-index="name">' . __('Name', 'ccpw') . '</th>
<th data-classes="desktop ccpw_coin_price" data-index="price">' . __('Price', 'ccpw') . '</th>
<th data-classes="desktop ccpw_coin_change24h" data-index="change_percentage_24h">' . __('Changes 24h', 'ccpw') . '</th>
<th data-classes="desktop ccpw_coin_market_cap" data-index="market_cap">' . __('Market CAP', 'ccpw') . '</th>';

$crypto_html .= '<th data-classes="ccpw_coin_total_volume" data-index="total_volume">' . __('Volume', 'ccpw') . '</th>
				<th data-classes="ccpw_coin_supply" data-index="supply">' . __('Supply', 'ccpw') . '</th>';

$crypto_html .= '</tr></thead><tbody>';
$crypto_html .= '</tbody><tfoot>
				</tfoot></table>';

$crypto_html .= '</div>';
