<?php

$interval_tab = get_post_meta($post_id, 'tw_hide_interval_tab',true)=='on'?'false':'true';
$interval = get_post_meta($post_id, 'tw_interval_time', true);
$locale = get_post_meta($post_id, 'tw_locale', true);
$symbol = get_post_meta($post_id, 'tw_symbol', true);
$color_theme = get_post_meta($post_id, 'tw_color_theme', true);
$transparent_bg = get_post_meta($post_id, 'tw_transparent_bg', true)=='on'?'true':'false';
$autosize = get_post_meta($post_id, 'tw_auto', true);
$tw_width='';$tw_height='';

if( $autosize != 'on' ){
    $tw_width       =   get_post_meta($post_id, 'tw_width', true);
    $tw_height      =   get_post_meta($post_id, 'tw_height', true);
}else{
	$tw_width       =   '100%';
    $tw_height      =   '100%';
}

$crypto_html ='';
$id = 'ccpw-technical-analysis-widget';
$crypto_html .='<!-- TradingView Widget BEGIN -->';
if( $autosize == 'on'){
    $crypto_html .='<div class="tradingview-widget-container-parent" style="height:550px">';
}
$crypto_html .='<div class="tradingview-widget-container" id="ccpw-analysis-widget-'.$post_id.'">
    <div class="tradingview-widget-container__widget"></div>
    <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/symbols/NASDAQ-AAPL/technicals/" rel="noopener" target="_blank"><span class="blue-text">Technical Analysis for AAPL</span></a> by TradingView</div>
    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-technical-analysis.js" async>
    {
    "showIntervalTabs": '.$interval_tab.',
    "width": "'.$tw_width.'",
    "colorTheme": "'.$color_theme.'",
    "isTransparent": '.$transparent_bg.',
    "locale": "'.$locale.'",
    "symbol": "'.$symbol.'",
    "interval": "'.$interval.'",
    "height": "'.$tw_height.'"
  }
    </script>
  </div>';
  if( $autosize == 'on'){
        $crypto_html .='</div>';
  }
$crypto_html .='<!-- TradingView Widget END -->';