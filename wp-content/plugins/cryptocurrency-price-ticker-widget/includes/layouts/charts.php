<?php

/**
 * Generating Chart for coins
 */
	$chart_html = '';
	$coin_symbol = $coin['symbol'];
	$coin_id = $coin['coin_id'];
	$coin_name=$coin['name'];
	$mainChartType = get_post_meta($post_id, 'main_chart_type', true);
	
	$coin_price = ccpwp_format_number( $coin['price'] );
	$market_cap = ccpwp_format_number( $coin['market_cap'] );
	$volume = ccpwp_format_number( $coin['total_volume'] );

	$c_height = $chart_height ? $chart_height . "px" : "400px";
	if($mainChartType=="cryptocompare"){
		if($coin_symbol){
		$color=isset($chart_color)? $chart_color: '#919191';
		$color=ccpw_hex2rgba($color, 0.85);
		$color_light=ccpw_hex2rgba($color, 0.50);
		$chart_html .='<div id="ccc-chart-block"><div class="ccc-chart-header">'.$coin_name.' '.__('Chart', 'ccpw').'<div class="ccc-logo-right">'.__('Chart by', 'ccpw').'<a href="http://cryptocompare.com" target="_blank" rel="nofollow">CryptoCompare</a></div></div>
		<script type="text/javascript">
		baseUrl = "https://widgets.cryptocompare.com/";
		var scripts = document.getElementsByTagName("script");
		var embedder = scripts[ scripts.length - 1 ];
		var cccTheme = {
			"General":{"borderWidth":"0px","borderColor":"#FFF","showExport":true},
			"Tabs":{"borderColor":"#FFF","activeBorderColor":"'.$color.'"},
			"Chart":{"fillColor":"#222","borderColor":"'.$color.'"},
			"Conversion":{"lineHeight":"10px"}};
		(function (){
		var appName = encodeURIComponent(window.location.hostname);
		if(appName==""){appName="local";}
		var s = document.createElement("script");
		s.type = "text/javascript";
		s.async = true;
		var theUrl = baseUrl+\'serve/v3/coin/chart?fsym='.$coin_symbol.'&tsyms=USD,EUR,CNY,GBP\';
		s.src = theUrl + ( theUrl.indexOf("?") >= 0 ? "&" : "?") + "app=" + appName;
		embedder.parentNode.appendChild(s);
		})();
		</script></div>
		';
	}
	
	}else if($mainChartType=="tradingview" || $trading_view ){

		$coin_symbol = $coin_symbol=='MIOTA'?'IOTA':$coin_symbol;

		switch($coin_symbol){
			case "STEEM":
			case "USDT":
			case "ADA":
				$tsymbol="BITTREX:".$coin_symbol."USD";
			break;
			case "SBD":
				$tsymbol="BITTREX:SBDBTC";
			break;
			case "BCH":
				$tsymbol="BITSTAMP:BCHUSD";
			break;
			case "BNB":
			case "XEM":
				$tsymbol="BINANCE:".$coin_symbol."BTC";
			break;
			case "DASH":
			case "IOTA":
			case "ONT":
				$tsymbol="BINANCE:".$coin_symbol."USD";
			break;
			case "USDC":
				$tsymbol="BINANCE:BTCUSDC";
			break;
			default:
				$tsymbol="BITFINEX:".$coin_symbol."USD";
		}

		$chart_html .= '<!-- TradingView Widget BEGIN -->
			<div class="tradingview-widget-container">
			  <div id="tradingview_' . $coin_symbol . '"></div>
			  <div class="tradingview-widget-copyright"></div>
			  <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
			  <script type="text/javascript">
			  new TradingView.widget(
			  {
			   "width":"100%",
			  "height": ' . $chart_height . ',
			  "symbol":"'.$tsymbol.'",
			  "interval": "D",
			  "timezone": "Etc/UTC",
			  "theme": "Light",
			  "style": "1",
			  "locale": "en",
			  "toolbar_bg": "#f1f3f6",
			  "enable_publishing": false,
			  "allow_symbol_change": true,
			  "hideideas": true,
			  "container_id": "tradingview_' . $coin_symbol . '"
			}
			  );
			  </script>
			</div>
			<!-- TradingView Widget END -->';
	}else{
		$coin_logo = ccpwp_coin_logo_html($coin_id, $size = 128);
		$coin_released = '';
		if ($coin_symbol == "MIOTA") {
			$coin_symbol = 'IOT';
			$coin_released = '90day';
		} else {
			$coin_released = '365day';
		}

		$coin_price_html = "";
		$coin_price_html .= '<span class="ticker-price">' . $fiat_symbol . $coin_price . '</span>';

		if (in_array($coin_symbol, array("BTX", "NAS", "GBYTE", "BCN"))) {
			$chart_html .= '<div class="no-data">' . __(
				'It is a new coin.API\'s have no available data',
				'ccpw'
			) . '</div>';
		} else {

			//$chart_data = ccpwp_full_chart_data($coin_symbol);
			//$data_json = json_encode($chart_data);
			// $chart_html .= '<script id ="' . $coin_symbol . '-chart-data" type = "application/json" >' . $data_json . '</script>';
			$chart_from_lbl= __('From', 'ccpw');
			$chart_to_lbl = __('To', 'ccpw');
			$chart_zoom_lbl = __('Zoom', 'ccpw');
			$chart_price_lbl = __('Price', 'ccpw');
			$chart_volume_lbl = __('Volume', 'ccpw');
			$color=isset($chart_color)? $chart_color: '#2196F3';
			$chart_cont= '<div 
			class="ccpw-chart" 
			data-coin-period="'.$coin_released .'"
			data-coin-id="' . $coin_id . '"
			data-chart-color="'. $color .'"
			data-from-lbl="'. $chart_from_lbl . '"
			data-zoom-lbl="' . $chart_zoom_lbl . '"
			data-to-lbl="' . $chart_to_lbl . '"
			data-price-lbl="' . $chart_price_lbl . '"
			data-volume-lbl="' . $chart_volume_lbl . '"
			></div>';
			$chart_html .= '<div class="coin_details" data-rest-url="'.site_url('wp-json/ccpw/v1/generate-chart/').'"><ul>
				<li><div class="ccpw_icon">' . $coin_logo . '</div></li>
				<li><div class="ticker-name"><strong>' . $coin_name . '<br/>(' . $coin_symbol . ')</strong></div>
				</li>
				<li class="c_info"><strong>' . __('Price ', 'ccpw') . '</strong> <div class="chart_coin_price CCP-' . $coin_id . '">' . $coin_price_html . '</div></li>
				<li  class="c_info"><strong>' . __('Market Cap', 'ccpw') . '</strong> 
				<div class="coin_market_cap"><span class="CCMC">' . $fiat_symbol. $market_cap . '</span></div></li>
				</ul></div>';
			$chart_html .= '<div class="chart-wrp"  id="CCPW-CHART-' . $coin_id . '" style="width:100%; height:' . $c_height . ';" >';
			$chart_html.=$chart_cont;
			$chart_html .= '<img class="ccpw-preloader" src="' . CCPWP_URL . 'images/chart-loading.svg">
			</div>';	

	}
}



