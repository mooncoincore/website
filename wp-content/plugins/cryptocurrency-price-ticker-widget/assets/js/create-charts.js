(function ($) {


  $.fn.gernateChart = function () {
  //  $(this).parents().find(".ccpw-preloader").show();
    var coinId = $(this).data("coin-id");
    var mainThis = $(this);
    var coinperiod = $(this).data("coin-period");

    var fromLbl = $(this).data("from-lbl");
    var toLbl = $(this).data("to-lbl");
    var zoomLbl = $(this).data("zoom-lbl");
    var priceLbl = $(this).data("price-lbl");
    var currentPrice = $(this).parents('.ccpw-container.chart').find('.CCP-'+coinId).text();
    var rest_url = $(this).parents('.ccpw-container.chart').find('.coin_details').attr('data-rest-url');
    var currentVol = $(this).parents('.ccpw-container.chart').children('.CCMC').text();
    var volumeLbl = $(this).data("volume-lbl");
    var color = $(this).data("chart-color");
    var price_section = $(this).find(".CCP-" + coinId);
    var priceData = [];
    var milliseconds = (new Date).getTime();
    if (currentPrice < 0.50) {
      var formatedPrice = numeral(currentPrice).format('00.000000')
    } else {
        var formatedPrice = numeral(currentPrice).format('00.00')
    }
    var currentPriceIndex = {
      date: milliseconds,
      value: formatedPrice.replace('$','') ,
      volume: numeral(currentVol).format('00.00')
  };
    $(this).find('.CCMC,.CCV').number(true);
    var cacheIndex=coinId+'-historical-data';
    var coinCacheData = lscache.get(cacheIndex);
if (coinCacheData) {
  priceData=coinCacheData;
  generateChart(coinId, color, priceData, fromLbl, toLbl, zoomLbl, priceLbl, volumeLbl)
} else {
    jQuery.ajax({
        type: "get",
        dataType: "json",
        url: rest_url,
        data: {'coin_id':coinId},
        async: !0,
        success: function (response) {
            if (response.status == "success") {
                if (response.data && response.data != null) {
                    var priceData = response.data;
                    var lastIndex = priceData[priceData.length-1];
                    currentPriceIndex.volume = lastIndex.volume;
                    priceData.push(currentPriceIndex);
                    mainThis.find("#cmc-chart-preloader").hide();
                  //cached for 1 hour
                    lscache.set(coinId+'-historical-data', priceData, 120);

                    generateChart(coinId, color, priceData, fromLbl, toLbl, zoomLbl, priceLbl, volumeLbl)
                } else {
                 //   mainThis.find("#cmc-chart-preloader").hide();
                    mainThis.find("#cmc-no-data").show();
                    mainThis.css('height', 'auto')
                    console.log('No data available!');
                }
            }
        }
      });
    }  
  }

  var generateChart = function(coinId, color, priceData, fromLbl, toLbl, zoomLbl, priceLbl, volumeLbl){
    if(priceData.price=='')return;
    $.each(priceData, function(index,data){
      var value;
        if ( parseFloat( data.value )< 0.50) {
          value = parseFloat( numeral(data.value).format('00.000000') );
        } else if( parseFloat( data.value )>= 0.51){
            value = parseFloat( numeral(data.value).format('00.00') );
        }
        priceData[index] = {'date':data.date,'value':value,'volume':data.volume};
        return priceData;
    });
    
      var chart = AmCharts.makeChart('CCPW-CHART-' + coinId, {
        "type": "stock",
        "theme": "light",
        "hideCredits": true,
        "categoryAxesSettings": {
          "minPeriod": "mm"
        },
        "dataSets": [{
          "title": "USD",
          "color": color,
          "fieldMappings": [{
            "fromField": "value",
            "toField": "value"
          }, {
            "fromField": "volume",
            "toField": "volume"
          }],

          "dataProvider": priceData,
          "categoryField": "date"
        }],

        "panels": [{
          "showCategoryAxis": true,
          "title": priceLbl,
          "percentHeight": 70,

          "stockGraphs": [{
            "id": "g1",
            "valueField": "value",
            "type": "smoothedLine",
            "lineThickness": 2,
            "bullet": "round",
            "comparable": true,
            "compareField": "value",
            "balloonText": "[[title]]:<b>[[value]]</b>",
            "compareGraphBalloonText": "[[title]]:<b>[[value]]</b>"
          }],


          "stockLegend": {
            "periodValueTextComparing": "[[percents.value.close]]%",
            "periodValueTextRegular": "[[value.close]]"
          },

          "allLabels": [{
            "x": 200,
            "y": 115,
            "text": "",
            "align": "center",
            "size": 16
          }],

          "drawingIconsEnabled": false
        }, {
          "title": volumeLbl,
          "percentHeight": 30,
          "stockGraphs": [{
            "valueField": "volume",
            "type": "column",
            "showBalloon": false,
            "cornerRadiusTop": 2,
            "fillAlphas": 1
          }],

          "stockLegend": {
            "periodValueTextRegular": "[[value.close]]"
          },

        }],

        "chartScrollbarSettings": {
          "graph": "g1",
          "usePeriod": "10mm",
          "position": "bottom"
        },

        "chartCursorSettings": {
          "valueBalloonsEnabled": true,
          "fullWidth": true,
          "cursorAlpha": 0.1,
          "valueLineBalloonEnabled": true,
          "valueLineEnabled": true,
          "valueLineAlpha": 0.5
        },
        "periodSelector": {
          "position": "top",
          "periodsText": zoomLbl,
          "fromText": fromLbl,
          "toText": toLbl,
          "periods": [
            {
              "period": "DD",

              "count": 1,
              "label": "1D"
            },
            {
              "period": "DD",

              "count": 7,
              "label": "7D"
            },
            {
              "period": "MM",
              "count": 1,
              "selected": true,
              "label": "1M"
            },
            {
              "period": "MM",
              "count": 3,
              "label": "3M"
            },
            {
              "period": "MM",
              "count": 6,
              "label": "6M"
            },
            /*  {
                  "period": "YY",
                  "count": 1,
                  "label": "1Y"
              },
              */
            {
              "period": "MAX",
              "label": "1Y"
            }]
        },

        "export": {
          "enabled": false,
          "position": "top-right"
        }
      });
  }
  $('.ccpw-chart').each(function (index) {
    $(this).gernateChart();
  });

})(jQuery);