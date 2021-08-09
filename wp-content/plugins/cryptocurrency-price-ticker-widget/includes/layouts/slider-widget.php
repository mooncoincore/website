<?php
$coin_html .= '<div class="' . $card_cols . ' slidertop-area">';
	
	$changes_coin_html	=	"";
		$changes_coin_html .= '<span class="ccpw-changes ' . $change_class_24h . '">';
		$changes_coin_html .= $change_sign_24h . $percent_change_24h;
		$changes_coin_html .= '</span>';

if( $design_slider == "style-1" ){
	
	
	$coin_html .= '<div class="coin-container text-center ccpw-slider ' . $design_slider . '"><div class="ccpw-slider-body">';
	$coin_html .= '<div class="slider-icon-name">';
	$coin_html .= $coin_logo_html;
	
		$coin_html .= '<div class="slider-price-area"><div class="ccpw-name">' . $coin_name . '</div>';
		$coin_html .= $coin_price_html . '</div>';
	
	$coin_html .= '</div>';
	if ($slider_display_changes) {
		$coin_html .= '<div class="slider-changes">' . $changes_coin_html;
		$coin_html .= '<div class="ccpw-changes-details">';
	
		$coin_html .= '<span class="changes-time">' . __('24H', 'ccpw') . '</span>';

		$coin_html .= '</div>';	// end of div before changes-time
		$coin_html .= '</div>';
	}

    $coin_html .= '</div></div>';

}else if( $design_slider == "style-2" ){

	$coin_html .= '<div class="coin-container text-center ccpw-slider ' . $design_slider . '"><div class="ccpw-slider-body">';
	$coin_html .= '<div class="slider-icon-name">';
	
	$coin_html .= $coin_logo_html;
	
		$coin_html .= '<div class="ccpw-name">' . $coin_name . '<span> ('. $coin_symbol .')</span></div>';

	$coin_html .= '</div>';
	$coin_html .= '<div class="slider-changes"><div class="slider-price-area">'.$coin_price_html . '</div>';

	$coin_html .= '<div class="ccpw-changes-details">';
	
	// Gather changes as per settings


	$coin_html .=  $changes_coin_html;

	$coin_html .= '<span class="changes-time">' . __('24H', 'ccpw') . '</span>';
	

	$coin_html .= '</div>';	// end of div before changes-time
	$coin_html .= '</div>';

    $coin_html .= '</div></div>';

}else if( $design_slider == "style-3" ){

	$coin_logo  =   ccpwp_coin_logo_html($coin_id, $size = 22);
    $small_chart_color = '#006400';
    $small_chart_bgcolor = '#90EE90';
    /* Change chart color if required */
    if (strpos($coin['percent_change_7d'], $change_sign_minus) !== false) {
        $small_chart_color = '#ff0000';
        $small_chart_bgcolor = '#ff9999';
	}
	if( isset($slider_chart_color) && $slider_chart_color!="" ){
        $small_chart_bgcolor = $slider_chart_color;
        $small_chart_color = $slider_chart_color;
	}
	
	$currency_symbol = ccpwp_get_currency_symbol($fiat_currency);
	$currency_price = ccpwp_usd_conversions($fiat_currency);
  
	$coin_chart = ccpw_generate_svg_chart(
		$coin_id,
		$coin_price,
		$small_chart_color,
		$small_chart_bgcolor,
		$period = '7d',
		$points=0,
		$currency_symbol,
		$currency_price,
		$chart_fill='false'
	);

	$coin_html .= '<div class="coin-container text-center ccpw-slider ' . $design_slider . '"><div class="ccpw-slider-body">';
	
	$coin_html .= '<div class="ccpw-slider-info">';
		
			$coin_html .= '<div class="ccpw_icon">'.$coin_logo.'</div>';
		
	$coin_html .= '<div class="ccpw-coin-name">'.$coin_symbol.'/'.$fiat_currency.'</div>';
	$coin_html .= '<div class="ccpw-coin-price">'.$coin_price_html . '</div>';
	
	$coin_html .= '<div class="ccpw-changes-details">';
	
	// Gather changes as per settings

	$coin_html .=  $changes_coin_html;
		$coin_html .= '<span class="changes-time">' . __('24H', 'ccpw') . '</span>';

	$coin_html .= '</div>';	// end of div before changes-time
	
	$coin_html .= '</div>'; // end of card-slider-info
	
	$coin_html .= '<div class="ccpw-slider-chart" >'.$coin_chart.'</div>';
    $coin_html .= '</div></div>';

}

$coin_html .= '</div>'; // end of slidertop-area.