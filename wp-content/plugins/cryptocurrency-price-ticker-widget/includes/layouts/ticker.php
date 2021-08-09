<?php 
	switch($design_ticker){
		case "style-1":
			$coin_html .= '<li '.$coin_attr_for_live.'
			class="ccpw_coin_cont ' . $design_ticker . '"  id="' . esc_attr($coin_id) . '">';
			$coin_html .= '<div class="coin-container ccpw-tooltip" data-tooltip-content="#tooltip_content_' . $coin_id . '">';
			$coin_html .= $cmc_link_start;
			$coin_html .= '<span class="ccpw_icon">' . $coin_logo . '</span>';
			$coin_html .= '<span class="ticker-name">' . $coin_name . '</span>';
			$coin_html .= '<span class="ticker-symbol">(' . $coin_symbol . ')</span>';
			$coin_html .= $cmc_link_end;
			if ($is_live_changes) {
				$coin_html .= $live_price_changes . $live_changes;
			} else {
				$coin_html .= $coin_price_html . $changes_coin_html;
			}
			$coin_html .= '</div>';
			$coin_html .= '<div class="ccpw_tooltip_templates">
			<span id="tooltip_content_' . $coin_id . '">
			<div class="tooltip-title"><span class="title-style"><strong>' . strtoupper($coin_name) . '</strong></span></div>
			<div class="24per-change"><span class="changes ' . $change_class_24h . '">' . __('24H : ', 'ccpw') . $change_sign_24h . $percent_change_24h . '</span></div>
			<div class="tooltip-list-vol">' . __('Volume : ', 'ccpw') . $fiat_symbol . $volume . '</div>
			<div class="tooltip-market_cap">' . __('Marketcap : ', 'ccpw') . $fiat_symbol . $market_cap . '</div>
			</span>
			</div>';
			$coin_html .= '</li>';
		break;
		case "style-3":
			/* Change chart color if required */
			$small_chart_color = '#006400';
			$small_chart_bgcolor = '#90EE90';
			
		   if(strpos($coin['percent_change_7d'], $change_sign_minus) !== false) {
			   $small_chart_color = '#ff0000';
			   $small_chart_bgcolor = '#ff9999';
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
		   $coin_html .= '<li '.$coin_attr_for_live.' 
			class="ccpw_coin_cont ' . $design_ticker . '" 
			id="' . esc_attr($coin_id) . '">';
		   $coin_html .= '<div class="coin-container">';
		   $coin_html .= '<div class="ccpw_left">';
		   $coin_html .= $cmc_link_start;
		   $coin_html .= '<span class="ccpw_icon_style_3">' . $coin_logo_big . '</span>';
		   
		   if ($is_live_changes) {
			   $coin_html .= '<br/>' . $live_changes;
		   } else {
			   $coin_html .= '<br/>' . $changes_coin_html . '</div>';
		   }
	   
		   $coin_html .= '<div class="ccpw_right">';
		   $coin_html .= '<span class="ticker-name">' . $coin_name . '</span>';
		   $coin_html .= '<span class="ticker-symbol">(' . $coin_symbol . ')</span>';
		   $coin_html .= $cmc_link_end;
		   if ($is_live_changes) {
			   $coin_html .= $live_price_changes.'</div>';
		   } else {
			   $coin_html .= $coin_price_html;
		   }
		   $coin_html .= '<br/>' . $coin_chart;
		   $coin_html .= '</div></div>';
		   $coin_html .= '</li>';

		break;
		case "style-4":
			$coin_html .= '<li   '.$coin_attr_for_live.'  class="ccpw_coin_cont ' . $design_ticker . '" id="' . esc_attr($coin_id) . '">';
			$coin_html .= '<div class="coin-container">';
			$coin_html .= '<div class="ccpw_left">';
			$coin_html .= $cmc_link_start;
			
				$coin_html .= '<span class="ccpw_icon_style_4">' . $coin_logo_big . '</span>';
			

			$coin_html .= '</div><div class="ccpw_right">';
			$coin_html .= '<span class="ticker-name">' . $coin_name . '</span>';
			$coin_html .= '<span class="ticker-symbol">(' . $coin_symbol . ')</span><br/>';
			$coin_html .= $cmc_link_end;
			if ($is_live_changes) {
				$coin_html .= $live_price_changes . $live_changes;
			} else {
				$coin_html .= $coin_price_html . $changes_coin_html;
			}
			$coin_html .= '</div></div>';
			$coin_html .= '</li>';
		break;
		case "style-5":
			$small_chart_color = '#006400';
			$small_chart_bgcolor = '#90EE90';
			if(strpos($coin['percent_change_7d'], $change_sign_minus) !== false) {
				$small_chart_color = '#ff0000';
				$small_chart_bgcolor = '#ff9999';
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
			
			$coin_html .= '<li  '.$coin_attr_for_live.'  class="ccpw_coin_cont ' . $design_ticker . '" id="' . esc_attr($coin_id) . '">';
			$coin_html .= '<div class="coin-container">';
			$coin_html .= '<div class="ccpw_left">';
			$coin_html .= $cmc_link_start;
			$coin_html .= '<span class="ccpw_icon_style_5">' . $coin_logo_big . '</span>';
			$coin_html .= '</div><div class="ccpw_right">';
			$coin_html .= '<span class="ticker-name">' . $coin_name . '</span>';
			$coin_html .= '<span class="ticker-symbol">(' . $coin_symbol . ')</span><br/>';
			$coin_html .= $cmc_link_end;
			if ($is_live_changes) {
				$coin_html .= $live_price_changes . $live_changes;
			} else {
				$coin_html .= $coin_price_html . $changes_coin_html;
			}
			$coin_html .= '</div>'.$coin_chart.'</div>';
			$coin_html .= '</li>';
		break;
		default:
		// style-2
			$coin_html .= '<li '.$coin_attr_for_live .'  class="ccpw_coin_cont ' . $design_ticker . '" id="' . esc_attr($coin_id) . '">';
			$coin_html .= '<div class="coin-container">';
			$coin_html .= $cmc_link_start;
			$coin_html .= '<span class="ccpw_icon">' . $coin_logo . '</span>';
			$coin_html .= '<span class="ticker-name">' . $coin_name . '</span>';
			$coin_html .= '<span class="ticker-symbol">(' . $coin_symbol . ')</span>';
			$coin_html .= $cmc_link_end;
			if ($is_live_changes) {
				$coin_html .= $live_price_changes . $live_changes;
			} else {
				$coin_html .= $coin_price_html . $changes_coin_html;
			}
			$coin_html .= '</div>';	
		break;
	}
