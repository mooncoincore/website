<?php

$cdata = ccpwp_coin_arr($id="",$type="options");
$selected_base = get_post_meta($post_id,'cal_base_currency', true );
$selected_target = get_post_meta($post_id,'cal_target_currency', true );

$crypto_data = (array)$cdata;
$btc_price='';
if(isset($crypto_data['BTC']->price_usd)){
$btc_price= ccpwp_format_number($crypto_data['BTC']->price_usd);
}
$currencies_list = (array)ccpwp_usd_conversions('all');
$crypto_dropdown ='';
$fiat_dropdown = '';
$both_c_dropdown='';
//$calculator_type = get_post_meta($post_id, 'calculator_type', true);
$calculator_type="CF";
$i=0;
$selected='';
$crypto_dropdown .= '<select  class="ccpw_calculate crypto_select" id="crypto_dd">';
if (is_array($crypto_data)) {
    foreach ($crypto_data as $id => $coinObj) {
        $coin = (array)$coinObj;
        $i++;
        if($coin['coin_id']==$selected_base ){
            $selected = 'selected="selected"';
        }else{
            $selected = ''; 
        }
     $crypto_dropdown .= '<option '.$selected.' value=' . $coin['price'] . '>' . $coin['name'] .'('.$coin['symbol'].')</option>';
    }
}

$crypto_dropdown .= '</select>';

$both_c_dropdown.= '<select data-default-currency="" class="ccpw_calculate fiat_select"
 id="fiat_dd">';
$both_c_dropdown .= '<optgroup label="' . __('Currencies', 'cmc') . '">';
if (is_array($currencies_list)) {
    foreach ($currencies_list as $name => $price) {
        if($name==$selected_target ){
            $selected = 'selected="selected"';
        }else{
            $selected = ''; 
        }
        $both_c_dropdown .= '<option '.$selected.' value=' . $price . '>' . $name . '</option>';
    }
}

$both_c_dropdown .= '</optgroup><optgroup label="' . __('Crypto Currencies', 'cmc') . '">';
if (is_array($crypto_data)) {
    foreach ($crypto_data as $id => $coinObj) {
        $coin = (array)$coinObj;
        if($coin['coin_id']==$selected_target ){
            $selected = 'selected="selected"';
        }else{
            $selected = ''; 
        }
        $both_c_dropdown .= '<option '.$selected.' value=' . $coin['price'] . '>' . $coin['name'] .'('.$coin['symbol'].')</option>';
      }
}
$both_c_dropdown .= ' </optgroup></select>';
$crypto_input= '<input id="crypto_amount" value="1" type="number" name="amount" class="ccpw_calculate crypto_input">';
$fiat_input = '<input id="fiat_amount" value="" type="number" name="amount" class="ccpw_calculate crypto_input">';
$output = ccpw_HTMLpluginVersion();
$output .= '<div id="ccpw-'.$post_id.'" data-calc-id="ccpw-'.uniqid().'" data-type="'. $calculator_type . '" class="ccpw_calculator cmc_calculator">';

if($calculator_type=="CF"){
    $output .= '<div class="cmc_calculator_block">';
    $output .= '<span class="cal_lbl">
' . __('Enter Amount', 'cmc') . '</span>';
    $output .= $crypto_input;
    $output .='</div>';
    $output .= '<div class="cmc_calculator_block">';
    $output .= '<span class="cal_lbl">
' . __('Base Currency', 'cmc') . '</span>';
    $output .= $crypto_dropdown;
    $output .= '</div>';
    $output .= '<div class="cmc_calculator_block">';
    $output.= '<span class="cal_lbl">
' . __('Convert To', 'cmc') . '</span>';
    $output .=$both_c_dropdown;
    $output.='<div></div>';
    $output .='</div>';

}

$coin_id = __("1 Bitcoin", 'ccpw');
$coin_name =$selected_base;
$converted_price=$selected_target;
$output .= '<h2><div class="cmc_rs_lbl">1 ' . $coin_name . '</div>';
$output .= '<div class="equalsto">=</div><div class="cmc_cal_rs">'.$selected_target.'</div></h2>';
$output .= '</div>';
