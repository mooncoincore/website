<?php

/**
 * Generating Dynamic styles for all widgets
 */
function ccpw_dynamic_style($post_id,$id,$type, $back_color, $font_color,$custom_css)

//  public function ccpw_dynamic_style($type, $chart_color, $back_color, $font_color, $post_id, $custom_css, $wid_id, $id, $design_card, $design_block, $design_ticker)
  {
	  $dynamic_styles = "";
	  $border_color = "";
	  $border_top_clr = "";
	  $border_bottom_clr = "";
	  $bg_color = !empty($back_color) ? "background-color:" . $back_color . ";" : "background-color:#eee;";
	  $fnt_color = !empty($font_color) ? "color:" . $font_color . ";" : "color:#000;";
	  $btm_color = !empty($font_color) ? "border-color:" . ccpw_hex2rgba($font_color, 0.25) . " !important;" : "border-color:#ccc !Important;";

	  /*Calculator Dynamic Styles*/
	  switch($type){
		case 'binance-live-widget':
			$chart_color = get_post_meta($post_id, 'binance_chart_color', true);
			$chart_color = empty( $chart_color ) ? '#a8e1ee' : $chart_color;
			//.card-block svg g path
			$dynamic_styles .= "#ccpwp_vui_widget_".$post_id." .card-block .coin-box{". $bg_color . $fnt_color . "}";
			$dynamic_styles .= "#ccpwp_vui_widget_".$post_id." .card-block .coin-box svg > g path:nth-child(1){fill:". $chart_color ."!important;fill-opacity:1!important}";
			$dynamic_styles .= "#ccpwp_vui_widget_".$post_id." .card-block .coin-box svg > g path:nth-child(2){stroke:". $font_color ."!important;}";
		break;
		case "ticker":
			$border_color = !empty($back_color) ? "border-color:" . $back_color . ";" : "border-color:#000;";
            $border_top_clr = !empty($back_color) ? "border-top-color:" . $back_color . ";" : "border-top-color:#000;";
            $border_bottom_clr = !empty($back_color) ? "border-bottom-color:" . $back_color . ";" : "border-bottom-color:#000;";
			$design_ticker = get_post_meta($post_id, 'design_ticker', true);
			switch($design_ticker){
				case "style-1":
				 $dynamic_styles .= "
				   .tooltipster #" . $id . " {" . $bg_color . "}
				   .tooltipster-sidetip .tooltipster-box {" . $bg_color . $border_color . "}
				   .tooltipster-sidetip.tooltipster-top .tooltipster-arrow-background {" . $border_top_clr . "}
				   .tooltipster-sidetip.tooltipster-bottom .tooltipster-arrow-background {" . $border_bottom_clr . "}
				   .tooltipster-sidetip.tooltipster-top .tooltipster-arrow-border{" . $border_top_clr . "}
				   .tooltipster-sidetip.tooltipster-bottom .tooltipster-arrow-border {" . $border_bottom_clr . "}
				   .tooltip-title,
				   .tooltip-list-vol,
				   .tooltip-market_cap{" . $fnt_color . "}
			   ";
				break;
				case "style-3":
				case "style-5":   
				   $dynamic_styles .= "
				   .style-3_chart #" . $id . " .coin-container:after {
					   border-color: " . ccpw_hex2rgba($font_color, 0.18) . ";
				   }";    
				   $dynamic_styles .= "
				   .style-5_big #" . $id . " .coin-container:after {
					   border-color: " . ccpw_hex2rgba($font_color, 0.18) . ";
				   }";
				break;
				case "style-4":
				   $dynamic_styles .= "
				   .style-4_big #" . $id . " .coin-container:after {
					   border-color: " . ccpw_hex2rgba($font_color, 0.18) . ";
				   } ";
				break;
				default:
				break;
			   }

			   $dynamic_styles .= "
				   .ccpw-ticker-cont ul#" . $id . " li {" . $bg_color . "}
				   .ccpw-ticker-cont ul#" . $id . " li .ticker-name,
				   .ccpw-ticker-cont ul#" . $id . " li .ticker-symbol,
				   .ccpw-ticker-cont ul#" . $id . " li .live-pricing,
				   .ccpw-ticker-cont ul#" . $id . " li  .ticker-price {" . $fnt_color . "}
			   ";

		break;	
		  case "calculator":
			  $dynamic_styles .= "
			  .ccpw_calculator.cmc_calculator {
				  background-color: " . $back_color . ";
				  color: " . $font_color . ";
			  }";
		  break;
		  case "accordion-block":
			  $dynamic_styles .= "
			  #" . $id . ".ccpw-container.price-block .ccpw-main-accordion {
				  background-color: " . $back_color . ";
				  color: " . $font_color . ";
			  }
			  #" . $id . " .ccpw-block .ccpw-coin-price-widget{
				  color: " . $font_color . ";
				  background: " . $back_color . ";
			  }
			  #" . $id . " .ccpw-block span.chart24h {
				  background: " . $font_color . ";
				  color: " . $back_color . ";
			  }
			  #" . $id . ".price-block.style-3 .ccpw-row {
				  border-color: " . ccpw_hex2rgba($font_color, 0.55) . ";
			  }";
		  break;
		  case "price-block":
			  $design_block = get_post_meta($post_id, 'design_block', true);
			  // Price block: accordion design
			  if ($design_block == "style-1") {
				  $dynamic_styles .= "
					  #" . $id . ".ccpw-container.price-block {
						  box-shadow: 1px 1px 0px 2px " . ccpw_hex2rgba($font_color, 0.15) . ";
						  background-color: " . $back_color . ";
						  color: " . $font_color . ";
					  }
					  #" . $id . " .ccpw-block .ccpw-coin-price-widget{
						  color: " . $font_color . ";
						  background: " . $back_color . ";
					  }
					  #" . $id . " .ccpw-block span.chart24h {
						  background: " . $font_color . ";
						  color: " . $back_color . ";
					  }";
				  // Price block: Rank card design
			  } else if ($design_block == 'style-2') {
				  $dynamic_styles .= "
					  #" . $id . " .ccpw-block-body {
						  color: " . $font_color . ";
						  background: " . $back_color . ";
					  }";
			  } else if ($design_block == 'style-3') {
				  $dynamic_styles .= "
					  #" . $id . " .ccpw-block-body{
						  color: " . $font_color . ";
						  background: " . $back_color . ";
					  }";
			  } else if ($design_block == 'style-4') {
				  $dynamic_styles .= "
					  #" . $id . " .ccpw-block-body{
						  color: " . $font_color . ";
						  background: " . $back_color . ";
					  }";
			  } else if ($design_block == 'style-5') {
				  $dynamic_styles .= "
					  #" . $id . " .blocktop-area {
						  color: " . $font_color . ";
						  background: " . $back_color . ";
					  }
					  #" . $id . " span.ccpw-currencybtn {
						  color: " . ccpw_hex2rgba($back_color, 0.95) . ";
						  background: " . ccpw_hex2rgba($font_color, 0.55) . ";
					  }
					  #" . $id . " span.ccpw-currencybtn.active {
						  color: " . $back_color . ";
						  background: " . $font_color . ";
					  }
					  #" . $id . " .price-block.style-5 span.ccpw-coin-24h-change span {
						  color: " . ccpw_hex2rgba($back_color, 0.98) . ";
						  background: " . ccpw_hex2rgba($font_color, 0.65) . ";
					  }";
			  }
		  break;
		  case "price-label":
			   /*Price Label Dynamic Styles*/
			  $dynamic_styles .= "
			  #" . $id . ".price-label .row li,
			  #" . $id . ".price-label .row li.style-2 .style-2 .ccpw_icon {" . $bg_color . "}
			  #" . $id . ".price-label .row li span.name,
			  #" . $id . ".price-label .row li span.symbol,
			  #" . $id . ".price-label .row li span.ticker-price,
			  #" . $id . ".price-label .row li .ccpw-multi-price-text,
			  #" . $id . ".price-label .row li .style-4 .live-pricing {" . $fnt_color . "}
		  ";
		  break;
		  case "chart":
				/*Chart Dynamic Styles*/
			  $chart_color = get_post_meta($post_id, 'chart_color', true);
			  $dynamic_styles .= "
			  #" . $id . " .ccc-chart-header { background: " . $chart_color . "}
			  #" . $id . " #ccc-chart-block .exportBtnTop,
			  #" . $id . " a.tabperiods.tabperiods_active,
			  #" . $id . " .coin_details {
				  color: " . $chart_color . ";
				  background: " . ccpw_hex2rgba($chart_color, 0.15) . ";
			  }
			  #" . $id . " .coin_details {
				  border: 1px solid " . ccpw_hex2rgba($chart_color, 0.16) . ";
			  }
			  .ccpw-container_chart #" . $id . " .coin-container:after,
			  .ccpw-container_four #" . $id . " .coin-container:after {" . $btm_color . "}
		  ";
		  break;
		  case "price-card":
		  case "slider-widget":
			   /*Price Card Dynamic Styles*/
			  $design_card = get_post_meta($post_id, 'design_card', true);
			  $dynamic_styles .= "
			  #" . $id . " .coin-container .ccpw-card-body, #" . $id . " .coin-container .ccpw-slider-body{" . $bg_color . $fnt_color . "}
			  #" . $id . ".price-card .changes-time {
				  color: " . ccpw_hex2rgba($back_color, 0.98) . ";
				  background: " . ccpw_hex2rgba($font_color, 0.65) . ";
			  }
			  #" . $id . ".slider-widget .changes-time {
				  color: " . ccpw_hex2rgba($back_color, 0.98) . ";
				  background: " . ccpw_hex2rgba($font_color, 0.65) . ";
			  }
			  #" . $id . ".slider-widget .style-2 .ccpw-slider-body {
				  border-color: " . ccpw_hex2rgba($font_color, 0.18) . "
			  }";
			  
			  if ($design_card == "style-2") {
				  $dynamic_styles .= "
					  #" . $id . ".price-card .style-2 .market-data .ccpw-supply,
					  #" . $id . ".price-card .style-2 .market-data .ccpw-vol,
					  #" . $id . ".price-card .style-2 .market-data .ccpw-cap {" . $btm_color . "}
					  #" . $id . ".price-card .style-2 .ccpw-card-body {
						  box-shadow: inset -1px -1px 0px 2px " . ccpw_hex2rgba($font_color, 0.35) . ";
					  }
				  ";
			  }
			  if ($design_card == "style-3") {
				  $dynamic_styles .= "
					  #" . $id . ".price-card .style-3 .ccpw-card-body {
					  box-shadow: inset -1px -1px 0px 2px " . ccpw_hex2rgba($font_color, 0.35) . ";
					  }
					  #" . $id . ".price-card .style-3 .ccpw-price-changes .ccpw-changes .changes-time-all {
						  color: " . ccpw_hex2rgba($back_color, 0.98) . ";
						  background: " . ccpw_hex2rgba($font_color, 0.65) . ";
					  }
					  #" . $id . ".price-card .style-3 .ccpw-price-changes .ccpw-changes {
						  border-color: " . ccpw_hex2rgba($font_color, 0.22) . ";
					  }
				  ";
			  }
			  if ($design_card == "style-4") {
				  $dynamic_styles .= "
					  .price-card .style-4 .live-pricing .live_t {
						  color: " . ccpw_hex2rgba($back_color, 0.98) . ";
						  background: " . ccpw_hex2rgba($font_color, 0.65) . ";
					  }
				  ";
			  }
		  break;
		  case "list-widget":
			   /*List Widget Dynamic Styles*/
			  $dynamic_styles .= "
			  .ccpw-widget #" . $id . "{" . $bg_color . $fnt_color . "}
			  .ccpw-widget .ccpw_table tr #" . $id . " {" . $bg_color . "}
			  .ccpw-widget #" . $id . ".table {" . $bg_color . $fnt_color . " box-shadow:inset 0px 0px 0px 3px " . ccpw_hex2rgba($font_color, 0.25) . ";}
			  #" . $id . " .ccpw_table > tbody > tr > td,
			  #" . $id . " .ccpw_table > tbody > tr > td .ticker-price,
			  #" . $id . " .ccpw_table > tbody > tr > td.price,
			  #" . $id . " .ccpw_table.table > thead > tr > th {" . $btm_color . $fnt_color . $bg_color . "}
			   ";

		   $dynamic_styles .= ".ccpw_table span.symbol { position:initial !Important; }";
		 
		  break;
		  case "rss-feed":
				/*News Feed Dynamic Styles*/
			  $dynamic_styles .= "
			  .tickercontainer #" . $id . " {" . $bg_color . "}
			  .tickercontainer #" . $id . " .ccpw-news a{" . $fnt_color . "}
			  .ccpw-news-ticker .tickercontainer li:after {color: " . ccpw_hex2rgba($font_color, 0.65) . ";}
			  .ccpw-ticker-rss-view #" . $id . "{" . $bg_color . ";}
			  .ccpw-ticker-rss-view #" . $id . " .ccpw-news .feed-publish-date{" . $fnt_color . "}
			  .ccpw-ticker-rss-view #" . $id . "	.ccpw-news .rss-desc p.news-desc {" . $fnt_color . "}
			  .ccpw-ticker-rss-view #" . $id . "	.ccpw-news h2.ccpw-news-link a{" . $fnt_color . "}
			  ";
		  break;
		  case "multi-currency-tab":
		  /*Multicurrency Dynamic Styles*/
		  $bg_coloronly = !empty($back_color) ? ":" . ccpw_hex2rgba($back_color, 0.25) . ";" : ":#ddd;";
		  $fnt_color = !empty($font_color) ? "color:" . $font_color . ";" : "color:#000;";
		  $fnt_coloronly = !empty($font_color) ? ":" . ccpw_hex2rgba($font_color, 0.55) . ";" : ":#666;";
		  $fnt_colorlight = !empty($font_color) ? ":" . ccpw_hex2rgba($font_color, 0.15) . ";" : ":#eee;";
		  $dynamic_styles .= "
			  .currency_tabs#" . $id . ",.currency_tabs#" . $id . " ul.multi-currency-tab li.active-tab{" . $bg_color . "border-color" . $fnt_colorlight . "}
			  .currency_tabs#" . $id . " ul.multi-currency-tab li{border-color" . $fnt_coloronly . "background-color" . $fnt_colorlight . "}
			  .currency_tabs#" . $id . " .mtab-content, .currency_tabs#" . $id . " ul.multi-currency-tab li{" . $fnt_color . "}
			  .currency_tabs#" . $id . " ul.multi-currency-tab-content li{border-color" . $fnt_colorlight . "}
		  ";
		  break;
		  default:
		  break;
	  }
	  $dynamic_styles .= $custom_css;
	  return $s = "<style type='text/css'>" . $dynamic_styles . "</style>";
	}
	

