import {subscribeSymbol,combinedStream} from './binance.js';
!(function ($) {
    "use strict";
    $(document).ready(function () {
        if($(".ccpw-live").length > 0 ){
            $(this).ccpWsetupWebSocket();
		     }
		});  
   ($.fn.ccpWsetupWebSocket = function () {
    var coinsArr=[];
    $(".ccpw-live").each(function () {
          var thisEle = $(this);
             thisEle.find(".ccpw_coin_cont").each(function () {
           var pair=$(this).data("trading-pair");
            if(pair!==undefined){
                coinsArr.push(pair.toLowerCase()+'@ticker');
                }
           });
        });
    combinedStream(coinsArr,displayRs);
   });

		    function displayRs(response){
		       	  if(response!==undefined){ 
		       	  	for (var indexkey in response) {
                    if (response.hasOwnProperty(indexkey)) {
                        var eleHolder =$('.ccpw_coin_cont[data-trading-pair="' + indexkey + '"]');
                        if (eleHolder.length > 0) {
                        	var coinLiveData=response[indexkey];
                        	  var currency_rate = eleHolder.parents('.ccpw-live').attr("data-currency-rate"),
                                currency_symbol = eleHolder.parents('.ccpw-live').attr("data-currency-symbol"),
                                currency_name = eleHolder.parents('.ccpw-live').attr("data-currency-type"),
                               

                                coinOldPrice = eleHolder.attr("data-coin-price");
                              
                              	if('price' in coinLiveData){
								var	coinLivePrice =coinLiveData.price;
                               var	coinLivePerChanges =coinLiveData.percent;
                               	var iconcls='';
                               	if(coinLivePerChanges>0){
									var iconcls='up';
                               	}else{
                               		var iconcls='down';
                               	}
                                 if ("USD" == currency_name) var converted_price = coinLivePrice;

                           		 else if ("BTC" == currency_name)
                                if ("BTC" != response.coin) var converted_price = coinLivePrice / currency_rate;
                                else converted_price = "1.00";
                           		 else var converted_price = coinLivePrice * currency_rate;


                            var formatted_price = ccpwp_numeral_formating(converted_price);
                            parseFloat(formatted_price.replace(/,/g, "")) > parseFloat(coinOldPrice)
                                ? (eleHolder.addClass("price-plus"),
                                  eleHolder.attr("data-coin-price", parseFloat(formatted_price.replace(/,/g, ""))),
                                  eleHolder.find(".live_p").html(currency_symbol + "<span>" + formatted_price + "</span>"),
                                  eleHolder.find(".live_price").html(currency_symbol + "<span>" + formatted_price + "</span>"),
                                  eleHolder.find(".live_c").html('<i class="ccpw_icon-'+iconcls+'" aria-hidden="true"></i>'+coinLivePerChanges+'%'),
                                  eleHolder.find(".live_c").addClass('up'),
                                  eleHolder.parents('td').next('td').find("span.live_c").html('<i class="ccpw_icon-'+iconcls+'" aria-hidden="true"></i>'+coinLivePerChanges+'%'),
                                  eleHolder.parents('td').next('td').find("span.live_c").addClass('up')
                                  )
                                : parseFloat(formatted_price.replace(/,/g, "")) < parseFloat(coinOldPrice) &&
                                  (eleHolder.addClass("price-minus"),
                                  eleHolder.attr("data-coin-price", parseFloat(formatted_price.replace(/,/g, ""))),
                                  eleHolder.find(".live_p").html(currency_symbol + "<span>" + formatted_price + "</span>"),
                                  eleHolder.find(".live_price").html(currency_symbol + "<span>" + formatted_price + "</span>"),
                                  eleHolder.find(".live_c").html('<i class="ccpw_icon-'+iconcls+'" aria-hidden="true"></i>'+coinLivePerChanges+'%'),
                                  eleHolder.find(".live_c").addClass('down'),
                                  eleHolder.parents('td').next('td').find("span.live_c").html('<i class="ccpw_icon-'+iconcls+'" aria-hidden="true"></i>'+coinLivePerChanges+'%'),
                                  eleHolder.parents('td').next('td').find("span.live_c").addClass('up')
                                  );


                        		}

			             		setTimeout(function () {
			                        eleHolder.removeClass("price-plus price-minus");
			                          eleHolder.find(".live_c").removeClass("up down");
			                    }, 200);
			                    	}
                     }   
		       	  }
		       
		       }				
		}
  
    function ccpwp_numeral_formating(data) {
      if (data >= 25 || data <= -1) var formatedVal = numeral(data).format("0,0.00");
      else if (data >= 0.5 && data < 25) var formatedVal = numeral(data).format("0,0.000");
      else if (data >= 0.01 && data < 0.5) var formatedVal = numeral(data).format("0,0.0000");
      else if (data >= 1e-4 && data < 0.01) var formatedVal = numeral(data).format("0,0.00000");
      else var formatedVal = numeral(data).format("0,0.00000000");
      return formatedVal;
  }
        
})(jQuery);
